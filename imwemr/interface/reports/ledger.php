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
//require_once('common/report_logic_info.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

$op='l';
$current_date=date('Y-m-d');



//GET LATEST SEARCH OF THIS USER
if (isset($saved_searched_id) && $saved_searched_id != '') {
    $saved_id = $saved_searched_id;
}
$searchOptions = '';
$srchQry = "Select id, DATE_FORMAT(saved_date, '".$date_format_SQL." %H:%i:%s') as saved_date,search_data, report, report_name, uid FROM 
reports_searches WHERE report='ledger_criteria' ORDER BY saved_date DESC";
$srchRs = imw_query($srchQry);
while ($srchRes = imw_fetch_array($srchRs)) {
	$sel = '';
	
	if($saved_id == $srchRes['id']) {
		$sel = 'Selected';
		$saved_id = $srchRes['id'];
		$dataParts = explode('~', $srchRes['search_data']);
		
		$grp_id = explode(',',$dataParts[0]);
		$facility_id = explode(',',$dataParts[1]);
		$filing_provider = explode(',',$dataParts[2]);
		$operator_id = explode(',',$dataParts[3]);
		$department = explode(',',$dataParts[4]);
		$ins_carriers = explode(',',$dataParts[5]);
		$_POST['checkNo'] = $dataParts[6];
		$_POST['amtCriteria'] = $dataParts[7];
		$_POST['checkAmt'] = $dataParts[8];
		$_POST['DateRangeFor'] = $dataParts[9];
		$_POST['dayReport'] = $dataParts[10];
		if($_POST['dayReport']=='Selected Date'){ $_POST['dayReport']='Date';}
		$_REQUEST['Start_date'] = $dataParts[11];
		$_REQUEST['End_date'] = $dataParts[12];
		$_POST['reportType'] = $dataParts[13];
		//$reportWise = $dataParts[14];
		$_POST['processReport'] = $dataParts[15];
		$_POST['batchFiles'] = $dataParts[16];
		$_POST['viewBy'] = $dataParts[17];
		
		//NEW
		$insuranceGrp= $dataParts[18];
		$_POST['pureSelfPay'] = $dataParts[19];
		$_POST['pay_method']= $dataParts[20];
		$_POST['cc_type']= explode(',',$dataParts[21]);
		$_POST['output_option']= $dataParts[22];
		$crediting_provider = explode(',',$dataParts[23]);
	}

	$sel2 = '';
	$sel2 = ($_POST['savedCriteria'] == $srchRes['id']) ? 'selected' : ''; //SELECTION IS BASED ON SELECTED VALUE OF DD
	$searchOptions .= '<option value="' . $srchRes['id'] . '" title="'.$GLOBALS['webroot'].'/library/images/delete_icon.png" '. $sel2 . '>' . trim($srchRes['report_name']). '</option>';

	$arrSearchName[]=$srchRes['report_name'];	
}
json_encode($arrSearchName);
	

if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}

//CHECK STORED PROCEDURES
$sp_exist=0;
$rs=imw_query("SELECT * FROM INFORMATION_SCHEMA.ROUTINES WHERE 
ROUTINE_TYPE='PROCEDURE' AND ROUTINE_SCHEMA='".IMEDIC_IDOC."' AND ROUTINE_NAME='spi_report_closed_day'");
if(imw_num_rows($rs)>0){
	$sp_exist=1;
}

if ($_POST['form_submitted']) {
    $grp_id_sel = array_combine($grp_id, $grp_id);
    $facility_id_sel = array_combine($facility_id, $facility_id);
    $strPhysician = implode(',', $filing_provider);
	$str_credit_physician= implode(',', $crediting_provider);
    $operator_id_sel= array_combine($operator_id, $operator_id);
	$strdepartment= implode(',', $department);
    $insuranceGrp_sel = array_combine($insuranceGrp, $insuranceGrp);
    $ins_carriers_sel = array_combine($ins_carriers, $ins_carriers);
    $processReport = $_REQUEST['processReport'];
	$sort_by = $_REQUEST['sort_by'];
	
	$rs=imw_query("SELECT * FROM INFORMATION_SCHEMA.ROUTINES WHERE 
    ROUTINE_TYPE='PROCEDURE' AND ROUTINE_SCHEMA='".IMEDIC_IDOC."' AND ROUTINE_NAME='spi_report_closed_day'");
	if($sp_exist==1){
		imw_query("CALL spi_report_closed_day(@StatusCode,@Message);");
	}
}

//--- GET DEPARTMENT NAME ---
$departmentName = $CLSReports->get_department_dropdown($strdepartment);
$allDeptCount = sizeof(explode('</option>', $departmentName)) - 1;

//REPORT LOGIC INFORMATION FLOATING DIV
$logicWidth = 60;
$logicDiv = reportLogicInfo('reportLedger', 'tpl', $logicWidth);

//--- GET ALL OPERATORS DETAILS ----
$selOperId= join(',',$_REQUEST['operator_id']);
$operatorOption = $CLSCommonFunction->dropDown_providers($selOperId, '', '');



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
				if (sizeof($ins_carriers_sel) > 0) {
					if ($ins_carriers_sel[$ins_id]) {
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

        if ($insuranceGrp_sel[$grp_ins_ids])
            $selected = 'SELECTED';
        $ins_group_options .= "<option value='" . $grp_ins_ids . "' " . $selected . ">" . $ins_grp_name . "</option>";
    }
}

//GET GROUPS NAME
$rs = imw_query("Select  gro_id,name,del_status from groups_new order by name");
$core_drop_groups = "<option value=''> All </option>";
while ($row = imw_fetch_array($rs)) {
    $sel = '';
    $color = '';
    if ($row['del_status'] == '1')
        $color = 'color:#CC0000!important';

    if ($grp_id_sel[$row['gro_id']])
        $sel = 'SELECTED';

    $core_drop_groups .= '<option value="' . $row['gro_id'] . '" ' . $sel . ' style="' . $color . '" >' . $row['name'] . '</option>';
}
$allGrpCount = sizeof(explode('</option>', $core_drop_groups)) - 2;


//--- GET SCHDEDULE FACILITY
$fac_query = "select id,name from facility order by name";
$fac_query_res = imw_query($fac_query);
$fac_id_arr = array();
$sch_facility_options = "";
while ($fac_res = imw_fetch_array($fac_query_res)) {
	$sel='';
    $fac_id = $fac_res['id'];
    $fac_id_arr[$fac_id] = addslashes($fac_res['name']);
	if($pay_location=='1' || $billing_location=='1'){
		if($facility_id_sel[$fac_id])$sel='SELECTED';
	}

    $sch_facility_options .= '<option value="'.$fac_res['id'].'" '.$sel.'>' .addslashes($fac_res['name']). '</option>';
}
$sch_fac_cnt=sizeof($fac_id_arr);


//--- GET FACILITY NAME ----
$facilityName = $CLSReports->getFacilityName($facility_id_sel, '1');
$allFacCount = sizeof(explode('</option>', $facilityName)) - 1;

//--- GET PHYSICIAN NAME ---
$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report');
$phy_sel= ($filing_provider['0']=='0')? 'SELECTED' : '';
$physicianName.= '<option value="0" '.$phy_sel.'>None</option>';
$allPhyCount = sizeof(explode('</option>', $physicianName)) - 1;

$creditPhysicinName = $CLSCommonFunction->drop_down_providers($str_credit_physician, '1', '1', '', 'report');
$allCrPhyCount = sizeof(explode('</option>', $creditPhysicinName)) - 1;

//GET ALL OPERATORS DETAILS
$allOprCount = 0;
$operatorNameOptions = '';
$res = imw_query("Select id, lname, fname, delete_status from users WHERE lname!='' ORDER BY delete_status, lname, fname");
while ($row = imw_fetch_array($res)) {
    $select = '';
    $color = '';
    if ($row['delete_status'] == 1)
        $color = 'color:#CC0000!important';

    if (sizeof($operator_id_sel) > 0) {
        if ($operator_id_sel[$row['id']])
            $select = "SELECTED";
    }

    $operatorNameOptions .= "<option $select value='" . $row['id'] . "' style=\"" . $color . "\">" . $row['lname'] . ' ' . $row['fname'] . "</option>";
    $allOprCount++;
}

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

//GET ACTB TYPE AHEAD FOR BATCHES
$strTypeAhead = '';
$sel_qry=imw_query("select tracking from manual_batch_file WHERE del_status='0' AND post_status='1'");
while($sel_rs=imw_fetch_array($sel_qry)){
	$strTypeAhead.="'".str_replace("'","",$sel_rs['tracking'])."',";	
}
$strTypeAhead = substr($strTypeAhead,0,-1);


//CSV NAME
$dbtemp_name_CSV= strtolower(str_replace(" ", "_", $dbtemp_name)).".csv";

//SEARCH MODE DEFAULT SELECTION
$default_date_range_for='';
if(!$_POST['form_submitted']){
	$default_date_range_for='dos';
	if(isset($filter_arr['dot'])){ $default_date_range_for='transaction_date';}
	elseif(isset($filter_arr['dos'])){ $default_date_range_for='dos';}
	elseif(isset($filter_arr['dor'])){ $default_date_range_for='date_of_payment';}
	elseif(isset($filter_arr['doc'])){ $default_date_range_for='doc';}
}

//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
$arrDateRange= $CLSCommonFunction->changeDateSelection();
if($dayReport=='Daily'){
	$fun_start_date = $fun_end_date= date($phpDateFormat);
}else if($dayReport=='Weekly'){
	$fun_start_date = $arrDateRange['WEEK_DATE'];
	$fun_end_date= date($phpDateFormat);
}else if($dayReport=='Monthly'){
	$fun_start_date = $arrDateRange['MONTH_DATE'];
	$fun_end_date= date($phpDateFormat);
}else if($dayReport=='Quarterly'){
	$fun_start_date = $arrDateRange['QUARTER_DATE_START'];
	$fun_end_date = $arrDateRange['QUARTER_DATE_END'];
}

//GET LAST EXECUTED CRON DATE
$pol_qry=imw_query("select DATE_FORMAT(report_closed_day,'%Y-%m-%d') as 'report_closed_day' from copay_policies");
$pol_row=imw_fetch_array($pol_qry);
$report_closed_day=$pol_row['report_closed_day'];
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
			
			#searchcriteria .dropdown-menu{ height:200px; overflow-y: scroll}
        </style>
    </head>
    <body>

        <form name="frm_reports" id="frm_reports" action="" method="post">
        	<input type="hidden" name="form_submitted" id="form_submitted" value="1">
            <input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
            
            <input type="hidden" name="form_submitted" id="form_submitted" value="1">
            <input type="hidden" name="call_from_saved" id="call_from_saved" value="0">
            <div class=" container-fluid">
            	<div class="anatreport">
                    <div id="select_drop" style="position:absolute;bottom:0px;"></div>                     
              	<div class="row" id="row-main">
                	<div class="col-md-3" id="sidebar">
                  	<div class="reportlft">
                    	<div class="practbox">
                      	<div class="anatreport"><h2>Practice Filter
							<div id="rptInfoImg" style="float:right" class="rptInfoImg" onClick="showHideReportInfo(event, '<?php echo $logicWidth;?>')"></div>
						</h2></div>
                        <div class="clearfix"></div>
                        <div class="pd5" id="searchcriteria">
                        	<div class="row">
                            <div class="col-sm-5">
                                <div class="checkbox checkbox-inline pointer">
                                    <input type="checkbox" name="billing_location" id="billing_location" value="1" <?php if($_POST['billing_location']=='1') echo 'CHECKED'; ?> onClick="show_facility('billing_location');" /> 
                                    <label for="billing_location">Billing Location</label>
                                </div>
                            </div>                                            
                            <div class="col-sm-7">
                                <div class="checkbox checkbox-inline pointer">
                                    <input type="checkbox" name="pay_location" id="pay_location" value="1" <?php if($_POST['pay_location']=='1') echo 'CHECKED'; ?> onClick="show_facility('pay_location');" /> 
                                    <label for="pay_location">Pay Location</label>
                                </div>
                            </div>                                            
                          	<div class="col-sm-4">
                            	<label>Groups</label>
                              <select name="grp_id[]" id="grp_id" data-container="#group_drop" class="selectpicker"  data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                              <?php echo $core_drop_groups; ?>
                              </select>
                           	</div>
                            
                            <div class="col-sm-4">
                                <label>Facility</label>
                                <select name="facility_id[]" id="facility_id" data-container="#select_drop" class="selectpicker"  data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                    <?php echo $facilityName; ?>
                                </select>
                            </div>

                            <div class="col-sm-4">
                              <label>Billing Provider</label>
                              <select name="filing_provider[]" id="filing_provider" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                  <?php echo $physicianName; ?>
                              </select>
                            </div>
							<div class="col-sm-4">
                              <label>Crediting Provider</label>
                              <select name="crediting_provider[]" id="crediting_provider" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                  <?php echo $creditPhysicinName; ?>
                              </select>
                            </div>                            
                            <div class="col-sm-4">
                              <label>Operator</label>
                              <select name="operator_id[]" id="operator_id" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                  <?php echo $operatorOption; ?>
                              </select>
                            </div>

                            <div class="col-sm-4">
                            	<label>Department</label>
                              <select name="department[]" id="department" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                              <?php echo $departmentName; ?>
                              </select>
                          	</div>                            
							<div class="col-sm-12">
								<div class="checkbox pointer" style="padding-top:5px; padding-bottom:10px">
									<input type="checkbox" name="chksamebillingcredittingproviders" id="chksamebillingcredittingproviders" value="1" <?php if($_POST['chksamebillingcredittingproviders']==1)echo 'checked';?>  />
									<label for="chksamebillingcredittingproviders">Exclude where billing and crediting providers are same</label>
								</div>										  
							</div>	
                            
                            <div class="col-sm-12">
                              <label>Period</label>
                              <div id="dateFieldControler">
                              	<select name="dayReport" id="dayReport" class="selectpicker"  data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
                                <option value="Daily" <?php if ($_POST['dayReport'] == 'Daily') echo 'SELECTED'; ?>>Daily</option>
                                <option value="Weekly" <?php if ($_POST['dayReport'] == 'Weekly') echo 'SELECTED'; ?>>Weekly</option>
                                <option value="Monthly" <?php if ($_POST['dayReport'] == 'Monthly') echo 'SELECTED'; ?>>Monthly</option>
                                <option value="Quarterly" <?php if ($_POST['dayReport'] == 'Quarterly') echo 'SELECTED'; ?>>Quarterly</option>
                                <option value="Date" <?php if ($_POST['dayReport'] == 'Date') echo 'SELECTED'; ?>>Date Range</option>
								<option value="None" <?php if ($_POST['dayReport'] == 'None') echo 'SELECTED'; ?>>None</option>
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
                                            
                            <div class="col-sm-6 mt2"><br /> 
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="processReport" id="summary" value="Summary" <?php if ($_POST['processReport'] == 'Summary' ) echo 'CHECKED'; ?> /> 
                                <label for="summary">Summary</label>
                            	</div>
                              <div class="radio radio-inline pointer">
                              	<input type="radio" name="processReport" id="detail" value="Detail" <?php if ($_POST['processReport'] == 'Detail' ||  empty($_POST['processReport'])) echo 'CHECKED'; ?>  /> 
                                <label for="detail">Detail</label>
                            	</div>
                          	</div>

                            <div class="col-sm-12 mt5">
                              <div class="radio radio-inline pointer">
                              	<input type="radio" name="reportType" id="reportNormalView" value="normalView" <?php if ($_POST['reportType'] == 'normalView' ||  empty($_POST['reportType'])) echo 'CHECKED'; ?>  /> 
                                <label for="reportNormalView">Normal View</label>
                            	</div>

                              <div class="radio radio-inline pointer">
                              	<input type="radio" name="reportType" id="reportCheckView" value="checkView" <?php if ($_POST['reportType'] == 'checkView') echo 'CHECKED'; ?>  /> 
                                <label for="reportCheckView">Check View</label>
                            	</div>
                          	</div>
                            
                            <div class="col-sm-12 mt2">
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="DateRangeFor" id="dos" onclick="javascript:enableDisableControls(this.value,'dateRangeFor');" value="date_of_service"  <?php if ($_POST['DateRangeFor'] == 'date_of_service' || $default_date_range_for=='dos') echo 'CHECKED'; ?> />
                            		<label for="dos">DOS</label>
                            	</div>
                              
                              <div class="radio radio-inline pointer">
                              	<input type="radio" name="DateRangeFor" id="doc" onclick="javascript:enableDisableControls(this.value,'dateRangeFor');" value="doc" <?php if ($_POST['DateRangeFor'] == 'doc' || $default_date_range_for=='doc') echo 'CHECKED'; ?> /> 
                                <label for="doc">DOC</label>
                            	</div>
                              
                              <div class="radio radio-inline pointer">
                              	<input type="radio" name="DateRangeFor" id="dor" onclick="javascript:enableDisableControls(this.value,'dateRangeFor');" value="date_of_payment" <?php if ($_POST['DateRangeFor'] == 'date_of_payment' || $default_date_range_for=='date_of_payment') echo 'CHECKED'; ?> />
                                <label for="dor">DOR</label>
                             	</div>
                              
                              <div class="radio radio-inline pointer">
                              	<input type="radio" name="DateRangeFor" id="dot" onclick="javascript:enableDisableControls(this.value,'dateRangeFor');" value="transaction_date" <?php if ($_POST['DateRangeFor'] == 'transaction_date' || $default_date_range_for=='transaction_date') echo 'CHECKED'; ?> /> 
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
                              <select name="insuranceGrp[]" id="insuranceGrp" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                             		<?php echo $ins_group_options; ?>
                            	</select>
                          	</div>
                            
                            <div class="col-sm-3" >
                            	<label for="ins_carriers">Ins. Carriers</label>
                              <select name="ins_carriers[]" id="ins_carriers" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" >
                              <?php echo $insComName_options; ?>
                              </select>
                           	</div>

                            <div class="col-sm-3" style="margin-top:20px" >
								<div class="checkbox checkbox-inline pointer pull-right">
									<input type="checkbox" name="pureSelfPay" id="pureSelfPay" value="1" <?php echo ($_POST['pureSelfPay'] == '1')?'checked':''; ?> onClick="changeInsProperties();" />
									<label for="pureSelfPay">Self Pay</label>
								</div>
                           	</div>
                            
                            <div class="col-sm-6" >
                            	<label for="ins_types">Batch Track</label>
                              <input name="batchFiles" id="batchFiles" class="form-control" value="<?php echo $_POST['batchFiles'];?>" onblur="enableDisableControls(this.value,'batchCtrl')">
                           	</div>

                            <div class="col-sm-6" >
                            	<label for="ins_types">Check # (Comma Sep.)</label>
                              <input name="checkNo" id="checkNo"  class="form-control" value="<?php echo $_POST['checkNo'];?>" onBlur="changeBatchProperties(this.value);">
                           	</div>

                            <div class="col-sm-6" >
                            	<div class="row">
                                    <div class="col-sm-6" >
                                        <label for="ins_types">Find By</label>
                                        <select name="amtCriteria" id="amtCriteria" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" data-actions-box="true" >
											<option value=">=" <?php if($_POST['amtCriteria']=='>=')echo 'selected';?> >>=</option>
                                            <option value="=" <?php if($_POST['amtCriteria']=='=')echo 'selected';?>>=</option>
                                            <option value="<=" <?php if($_POST['amtCriteria']=='<=')echo 'selected';?>><=</option>
            		                   </select>
                                    </div>  
                                    <div class="col-sm-6" >
                                        <label for="ins_types">Check Amt.</label>
                                      <input name="checkAmt" id="checkAmt"  class="form-control" value="<?php echo $_POST['checkAmt'];?>">
                                    </div>  
								</div>
                           	</div>

                            <div class="col-sm-6" >
                            	<div class="row">
                                    <div class="col-sm-6" >
                                        <label for="ins_types">Method</label>
                                        <select name="pay_method" id="pay_method" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" data-actions-box="true" onChange="show_cctypes(this.value);" >
											<option value="" >Select</option>
                                            <option value="cash" <?php if($_POST['pay_method']=='cash')echo 'selected';?> >Cash</option>
                                            <option value="check" <?php if($_POST['pay_method']=='check')echo 'selected';?> >Check</option>
                                            <option value="credit card" <?php if($_POST['pay_method']=='credit card')echo 'selected';?>>Credit Card</option>
                                            <option value="eft" <?php if($_POST['pay_method']=='eft')echo 'selected';?>>EFT</option>
                                            <option value="money order" <?php if($_POST['pay_method']=='money order')echo 'selected';?>>Money Order</option>
            		                   </select>
                                    </div>  
                                    <div class="col-sm-6" >
                                    	<div id="cctypes" style="display:none">
                                            <label for="ins_types">CC Type</label>
                                            <select name="cc_type[]" id="cc_type" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" >
                                                <option value="ax" <?php if(in_array('ax', $_POST['cc_type']))echo 'selected';?>>American Express</option>
                                                <option value="care credit" <?php if(in_array('care credit', $_POST['cc_type']))echo 'selected';?>>Care Credit</option>
                                                <option value="dis" <?php if(in_array('dis', $_POST['cc_type']))echo 'selected';?>>Discover</option>
                                                <option value="mc" <?php if(in_array('mc', $_POST['cc_type']))echo 'selected';?>>Master Card</option>
                                                <option value="visa" <?php if(in_array('visa', $_POST['cc_type']))echo 'selected';?>>Visa</option>
                                           </select>
                                       </div>
                                    </div>  
								</div>
                           	</div>                            
                        	</div>
                       	</div>
                    	</div>
                      
                      <div class="grpara">
                      	<div class="anatreport"><h2>Group BY</h2></div>
                        <div class="clearfix"></div>
                        <div class="pd5" id="searchcriteria">
                        	<div class="row">
                            <div class="col-sm-4">
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="viewBy" id="grpby_physician" value="physician" <?php if ($_POST['viewBy'] == 'physician' || $_POST['viewBy'] == '') echo 'CHECKED'; ?>/> 
                                <label for="grpby_physician">Physician</label>
                            	</div>
                           	</div>

                            <div class="col-sm-4">
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="viewBy" id="grpby_facility" value="facility" <?php if ($_POST['viewBy'] == 'facility') echo 'CHECKED'; ?>/> 
                                <label for="grpby_facility">Facility</label>
                             	</div>
                           	</div>
                          </div>
                      	</div>
                     	</div>

					  <div class="grpara">
                      	<div class="anatreport"><h2>Saved Criteria</h2></div>
                        <div class="clearfix"></div>
                        <div class="pd5" id="searchcriteria">
                        	<div class="row">
                                <div class="col-sm-5">
                                  <label>Saved Searches</label>
    	                           <select name="savedCriteria" id="savedCriteria" style="width:100%;" data-maincss="blue" onchange="javascript:dChk=0; callSavedSearch(this.value, 'frm_reports'); saved_functionality('savedCriteria');">
	                                 <option value="" >Select</option>
        	                          <?php echo $searchOptions; ?>
            	                    </select> 
                                    <input type="hidden" name="saved_searched_id" id="saved_searched_id" value="">
                                </div>
                                <div class="col-sm-2" >
                                    <div class="checkbox pointer" style="padding-top:17px">
                                    <input type="checkbox" name="chkSaveSearch" id="chkSaveSearch" value="1" onClick="javascript:show_saved();" />
                                    <label for="chkSaveSearch">Save</label>
                                    </div>
                                </div> 
                                <div class="col-sm-5" id="div_search_name" style="display:none" >
                                	<label>Name of Search</label>
	                                <input type="text" name="search_name" id="search_name" value="" class="form-control" onBlur="javascript: saved_functionality('search_name');" />
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
                      	</div>
                   		</div>
                   		
											<div class="clearfix">&nbsp;</div>
                 		</div>
                 		
										<div id="module_buttons" class="ad_modal_footer text-center">
                    	<button class="savesrch" type="button" onClick="top.fmain.get_report()">Search</button>
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
                                        if($_POST['form_submitted']) {
											include('ledger_result.php');
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

<!--LOADER -->
<!--<div class="modal" id="div_report_day_close" role="dialog">
<div class="common_modal_wrapper">
<div class="modal-dialog"><div class="modal-content">
<div class="modal-header bg-primary"><h4 class="modal-title">Update Report Data</h4></div>
<div id="report_create_div">
    <div class="modal-body" style="max-height:'+cont_height+'px;overflow:auto;overflow-y:scroll">
    <div class="row">
    <div class="col-sm-3 text-center" style="float:left"><div class="loading_container"><div class="process_loader"></div><div id="div_loading_text" class="text-info"></div></div></div><iframe name="iframe_ccda" id="iframe_reports_daily" frameborder="0" width="100%" height="0"></iframe>
    <div class="col-sm-9 alert alert-info text-center" id="loader_content" style="float:right"><span>Please wait recent data is being import.</span></div><div class="col-sm-12">
    <div id="module_buttons" class="modal-footer ad_modal_footer"><div class="col-sm-12"><button type="button" class="btn btn-danger" data-dismiss="modal" onClick="call_form_submit();">Close</button></div></div>
    </div></div>
    </div>
</div>    
</div></div>
</div>
</div>-->

<form name="csvDownloadForm" id="csvDownloadForm" action="downloadFile.php" method ="post" > 
	<input type="hidden" name="csv_text" id="csv_text">	
    <input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV;?>" />
</form> 
<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
	<input type="hidden" name="file_format" id="file_format" value="csv">
    <input type="hidden" name="zipName" id="zipName" value="">	
    <input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
</form> 
<script type="text/javascript">
	var current_date='<?php echo $current_date;?>';
	var report_closed_day='<?php echo $report_closed_day;?>';
	var fun_start_date='<?php echo $fun_start_date;?>';
	var fun_end_date='<?php echo $fun_end_date;?>';
	var dbtemp_name='<?php echo $dbtemp_name;?>';
	var op='<?php echo $op;?>';
	var output='<?php echo $_POST['output_option'];?>';
	var arrTypeAhead= new Array(<?php echo remLineBrk($strTypeAhead); ?>);
	var arrSearchName=[];
	var arrSearchName=<?php echo json_encode($arrSearchName); ?>;
	var sp_exist='<?php echo $sp_exist;?>';
	var processReport='<?php echo $processReport; ?>';

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
		
		$('#batchFiles').typeahead({source:arrTypeAhead});


	});

	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	var HTMLCreated='<?php echo $HTMLCreated; ?>';
	
	//BUTTONS
	var mainBtnArr = new Array();
	if (printFile == '1') {
		mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf('"+op+"');");
		mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
	}
	top.btn_show("PPR", mainBtnArr);
	//-------	



	//FOR DATA IMPORT LOADER
	var loder_icon = '<div id="div_loading_image" class="text-center"><div class="loading_container"><div class="process_loader"></div><div id="div_loading_text" class="text-info"></div></div></div>';
	function report_daily_closed(){
		var content_str = '<div class="row"><div class="col-sm-12 alert alert-info text-center"><span>Preparing report tables, please wait.</span></div><div class="col-sm-12">'+loder_icon+'</div></div><iframe name="iframe_ccda" id="iframe_reports_daily" frameborder="0" width="100%" height="0"></iframe>';
		var footer_cont ='<button type="button" class="btn btn-danger" data-dismiss="modal" onClick="submit_form();">Close</button>';
		show_modal('report_create_div','Update Report Data',content_str,' ','','modal_70','','no');
	}
	$('body').on('show.bs.modal','#report_create_div',function(){
		$(this).find('.modal-body').css('height', 300);
		$('#iframe_reports_daily').attr('src','<?php echo $GLOBALS['webroot']; ?>/interface/reports/report_closed_day.php?callfrom=reports');
	});
	
		
	function get_report() {
		top.show_loading_image('hide');		
		var start_date=current_date;
		$('#callby_saved_dropdown').val('');		
		
		if ($('#chkSaveSearch').prop('checked') == true) {
			if($('#savedCriteria').val()=='' && $('#search_name').val()==''){
				top.show_loading_image('hide');
				alert('Please select saved search or enter new search name.');
				return false;
			}
			else if($('#search_name').val()!=''){
				for(x in arrSearchName) {
					if (arrSearchName[x] == $('#search_name').val()) {
						top.show_loading_image('hide');
						alert('Search name already exist.');
						return false;
					}
				}
			}
		}


		var datecheck = $('#dayReport').val();
		var chkNo = $('#checkNo').val()
		if(datecheck == 'None'){
			if(chkNo == ""){
				top.fAlert("Please enter check number.");
				return false;
			}
		}	

		
		/*if(sp_exist==0){
			switch($('#dayReport').val()){
				case 'Weekly':
				case 'Monthly':
				case 'Quarterly':
					start_date=fun_end_date;
					break;
				case 'Date':
					d=$('#End_date').val();
					var arr=new Array();
					arr=d.split('-');
					//alert(arr[2]+'-'+arr[0]+'-'+arr[1]);
					start_date=arr[2]+'-'+arr[0]+'-'+arr[1];
					break;
			}
			var date1=new Date(start_date);
			var date2=new Date(report_closed_day);

			if(date1>date2 || +date1===+date2){
				report_daily_closed();
			}else{
				top.show_loading_image('show');
				document.frm_reports.submit();
			}
		}else{*/
			top.show_loading_image('show');
			document.frm_reports.submit();			
		//}
	}

	function submit_form(){
		top.show_loading_image('show');
		document.frm_reports.submit();
	}

	function generate_pdf(op) {
		if (file_location != '') {
			top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
			top.html_to_pdf(file_location, op);
			window.close();
		}
	}
	
	function show_cctypes(selval){
		if(selval=='credit card'){
			$('#cctypes').css('display', 'block');
		}else{
			$('#cc_type').selectpicker('val','').selectpicker('refresh');
			$('#cctypes').css('display', 'none');
		}
	}

	function changeBatchProperties(val){
		if(val!=''){
			$('#batchFiles').val('');
			$('#batchFiles').attr('disabled', true);
		}else{
			$('#batchFiles').attr('disabled', false);
		}
	}

   function show_facility(ctrl){
	   if($('#'+ctrl).is(":checked")){
		   if(ctrl=='billing_location'){
			   $('#pay_location').prop('checked',false);

			   //$("input[name='DateRangeFor'][id='dor']").prop("checked", true);
			   $("input[name='DateRangeFor'][id='dos']").prop("disabled", false);
			   $("input[name='DateRangeFor'][id='doc']").prop("disabled", false);
		   }else{
			   $('#billing_location').prop('checked',false);

			   //IF PAY LOCATION SELECTED THEN DISABLE DOS AND DOC
			   if($("input[name='DateRangeFor'][id='dos']").is(":checked") || $("input[name='DateRangeFor'][id='doc']").is(":checked")){
			   		$("input[name='DateRangeFor'][id='dor']").prop("checked", true);
			   }
			   $("input[name='DateRangeFor'][id='dos']").prop("disabled", true);
			   $("input[name='DateRangeFor'][id='doc']").prop("disabled", true);
		   }
		   $('#facility_id').html('<?php echo $sch_facility_options;?>');
		   $('#facility_id').selectpicker("refresh");
	   }else{
		   $("input[name='DateRangeFor'][id='dos']").prop("disabled", false);
		   $("input[name='DateRangeFor'][id='doc']").prop("disabled", false);
		   
		   $('#facility_id').html('<?php echo $facilityName;?>');
	       $('#facility_id').selectpicker("refresh");
	   }
   }
   
   function show_saved(){
	   if($('#chkSaveSearch').is(":checked")){
		   $('#div_search_name').css('display','block');
	   }else{
		   $('#div_search_name').css('display','none');
	   }
   }

   function saved_functionality(callfrom){
	   if(callfrom=='search_name'){
		   if($('#search_name').val()!=''){
				$('#savedCriteria_child ul li').removeClass('selected');
				var oDropdown = $("#savedCriteria").msDropdown().data("dd");
				oDropdown.set("selectedIndex", 0);
		   }
	   }else{
		   if($('#savedCriteria').val()!=''){
			   $('#search_name').val('');
			   $('#div_search_name').css('display','none');
		   }else if($('#savedCriteria').val()=='' && $('#chkSaveSearch').is(":checked")){
			   $('#div_search_name').css('display','block');
		   }
	   }
   }      

   function enableDisableControls(vals,callFrom){


		if(callFrom=='dateRangeFor'){
			if(vals!='transaction_date' && vals!='date_of_payment'){
				$('#batchFiles').val('');
				DateOptions("Daily");
				$('#dayReport').val("Daily");
			}
		}else{
			if(vals!=''){
				
				//DateOptions("Daily");
				//$('#Start_date').val("");
				//$('#End_date').val("");
				//$('#dayReport').val("None");

				if($('#dos').is(':checked') || $('#doc').is(':checked')){
					  $('#dot').attr("checked", "checked");
				}
			}			
		}
	}	

	$(document).ready(function (e) {
		
		DateOptions('<?php echo $_POST['dayReport'];?>');
		changeBatchProperties('<?php echo $_POST['checkNo'];?>');
		
		//$('#call_from_saved').val('0');

		//DateOptions("<?php echo $_POST['dayReport']; ?>");
		//enableOrNot("<?php echo $_POST['processReport']; ?>");
		//addRemoveGroupBy("<?php echo $DateRangeFor; ?>");

		//$("#searchCriteria").msDropdown({roundedBorder: false});
		//oDropdown = $("#searchCriteria").msDropdown().data("dd");
		//oDropdown.visibleRows(10);
		
		show_facility('<?php echo $p=($_POST['billing_location']=='1')?'billing_location':'pay_location';?>');

		$("#savedCriteria").msDropdown({roundedBorder: false});
		oDropdown = $("#savedCriteria").msDropdown().data("dd");
		oDropdown.visibleRows(10);
		
		function set_container_height(){
			$_hgt = (window.innerHeight) - $('#module_buttons').outerHeight(true);
			$('.reportlft').css({
				'height':$_hgt,
				'max-height':$_hgt,
				'overflow-x':'hidden',
				'overflow-y':'auto'
			});
			$('.report-content').css({'height':(window.innerHeight),'overflow-x':'hidden','overflow-y':'auto'});
		} 

		$(window).load(function(){
			set_container_height();
			//TRIGGERING CLICK EVENT TO COLLAPSE LEFT SEARCH BAR
			if(HTMLCreated==1){
				$(".fltimg .toggle-sidebar").click();
			}
			
			var pay_method='<?php echo $_POST['pay_method'];?>';
			
			if(pay_method=='credit card'){
				show_cctypes('credit card');
			}
		});
		
		if(HTMLCreated==1){
			if(output=='output_pdf'){
				generate_pdf(op);
				top.show_loading_image('hide');
			}
			if(output=='output_csv'){
				if(processReport=="Detail"){
					download_csv();
				}else{
					export_csv();
				}				
				top.show_loading_image('hide');
			}
		}		

		$(window).resize(function(){
			set_container_height();
		});
		var page_heading = "<?php echo $dbtemp_name; ?>";
		set_header_title(page_heading);
	});

</script>
    </body>
</html>