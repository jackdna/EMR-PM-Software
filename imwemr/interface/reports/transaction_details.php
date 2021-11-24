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

//REPORT NAME
if($temp_id){
	$sql_query = imw_query("SELECT * FROM `custom_reports` WHERE id='$temp_id' and `delete_status` = 0");
	if(imw_num_rows($sql_query) > 0){
		$row = imw_fetch_assoc($sql_query);
		$dbtemp_id  = $row['id'];
		$dbtemp_name  = $row['template_name'];
		$default_report  = $row['default_report'];
		$dbtemplate_fields  = unserialize($row['template_fields']);
	}
	$filter_arr = array();
	foreach($dbtemplate_fields as $obj){
		$filter_arr[$obj['name']] = $obj['value'];
	}
}
//CSV NAME
$dbtemp_name_CSV= strtolower(str_replace(" ", "_", $dbtemp_name)).".csv";
//--------------------


if(empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}

//--- GET DEPARTMENT NAME ---
$strdepartment = implode(',',$_REQUEST['department']);
$departmentName = $CLSReports->get_department_dropdown($strdepartment);


if($_POST['form_submitted']) {
    $grp_id_sel = array_combine($grp_id, $grp_id);
    $facility_id_sel = array_combine($facility_id, $facility_id);
	$filing_provider_sel = array_combine($filing_provider, $filing_provider);
    $strPhysician = implode(',', $filing_provider);
    $str_credit_physician= implode(',', $crediting_provider);
    $operator_id_sel= array_combine($operator_id, $operator_id);
    $insuranceGrp_sel = array_combine($insuranceGrp, $insuranceGrp);
    $ins_carriers_sel = array_combine($ins_carriers, $ins_carriers);
    $cpt_cat_id = array_combine($cpt_cat, $cpt_cat);
    $cpt_code_id = array_combine($cpt, $cpt);
    $dx_code10_sel = array_combine($icd10_codes, $icd10_codes);
    $modifiers_sel = array_combine($modifiers, $modifiers);
    $adjustmentId_sel = array_combine($adjustmentId, $adjustmentId);
    $wrt_code_sel = array_combine($wrt_code, $wrt_code);
    $ins_types_sel = array_combine($ins_types, $ins_types);
	$selectedProc = array_combine($selectedProc, $selectedProc);
	$selectedRef = array_combine($selectedRef, $selectedRef);
    $processReport = $_REQUEST['processReport'];
	$sort_by = $_REQUEST['sort_by'];
}


//REPORT LOGIC INFORMATION FLOATING DIV
$logicWidth = 700;
$logicDiv = reportLogicInfo('productivity', 'tpl', $logicWidth);
$logicCSS = reportLogicInfoHeader('tpl');

//--- GET ALL OPERATORS DETAILS ----
$selOperId= join(',',$operator_id_sel);
$operatorOption = $CLSCommonFunction->dropDown_providers($selOperId, '', '');

//--- GET ALL CPT CATEGORIES 
$cpt_cat_qry = "select cpt_cat_id, cpt_category from cpt_category_tbl order by cpt_category";
$cpt_cat_qry_rs = imw_query($cpt_cat_qry);
$cpt_cat_arr = array();
$cpt_cat_options='';
while($cpt_cat_qry_res= imw_fetch_assoc($cpt_cat_qry_rs)){
	$sel='';
	$cptCatId = $cpt_cat_qry_res['cpt_cat_id'];
	$cptCatName = $cpt_cat_qry_res['cpt_category'];
	
	$iqry = "Select cpt_fee_id from cpt_fee_tbl 
		WHERE cpt_prac_code != '' And cpt_cat_id = ".$cptCatId."  order by cpt_prac_code asc";
	$isql = imw_query($iqry);
	$icnt = imw_num_rows($isql);
	$tmpCptCodeArr = array();
	if( $icnt ) {
		while( $ires = imw_fetch_assoc($isql) ){
			$tmpCptCodeArr[]  = $ires['cpt_fee_id'];
		}
	}
	
	$selected = '';
  $cpt_ids = implode(",", $tmpCptCodeArr);
  $cpt_cat_arr[$cpt_ids] = $cptCatName;
	
	if($cpt_cat_sel[$cpt_ids])$sel='SELECTED';
    $cpt_cat_options .= "<option value='" . $cpt_ids . "' ".$sel.">".$cptCatName."</option>";	
}

//CPT CODES
$qry = "Select cpt_prac_code,cpt_fee_id,status,delete_status from cpt_fee_tbl 
		WHERE cpt_prac_code != '' order by cpt_prac_code asc";
$res = imw_query($qry);
while ($row = imw_fetch_array($res)) {
    $color = '';
    $cpt_fee_id = $row['cpt_fee_id'];
    $cpt_prac_code = (strlen($row['cpt_prac_code'])>38)? substr($row['cpt_prac_code'],0,38).'...': $row['cpt_prac_code'];
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

foreach ($arrICD10Code as $dx10Literals => $dx10code) {
	$dx10Literals = str_replace("'", "", $dx10Literals);
	$sel = ($dx_code10_sel[$dx10Literals]) ? 'selected' : '';
    $all_dx10_code_options.= "<option value='".$dx10Literals."' ".$sel.">" . $dx10code . "</option>";
}
$allDXCount10 = sizeof($arrICD10Code);

//SET ADJUSTMENT CODE
$qry = "Select a_id, a_code from adj_code where a_code != ''";
$res = imw_query($qry);
$adjustDataArr = array();
$adj_code_options = '';
while ($row = imw_fetch_array($res)) {
    $sel = '';
    $a_id = $row['a_id'];
    $a_code = $row['a_code'];
    $adjustDataArr[$a_id] = $a_code;

    if (sizeof($adjustmentId_sel) > 0) {
        if ($adjustmentId_sel[$a_id])
            $sel = "SELECTED";
    }

    $adj_code_options .= "<option value='" . $a_id . "' " . $sel . ">" . $a_code . "</option>";
}
$allAdjCount = sizeof($adjustDataArr);

//SET WRITE OFF CODE
$qry = "select w_id,w_code from write_off_code where trim(w_code) != '' order by w_code";
$res = imw_query($qry);
$writeOffArr = array();
$write_off_options = '';
while ($row = imw_fetch_array($res)) {
    $sel = '';
    $w_id = $row['w_id'];
    $w_code = $row['w_code'];
    $writeOffArr[$w_id] = $w_code;

    if (sizeof($wrt_code_sel) > 0) {
        if ($wrt_code_sel[$w_id])
            $sel = 'SELECTED';
    }

    $write_off_options .= "<option value='" . $w_id . "' " . $sel . ">" . $w_code . "</option>";
}
$allWriteoffCount = sizeof($writeOffArr);

//	GET ALL MODIFIERS
$arModifiers = array();
$modifier_options = '';
$qry = "SELECT modifiers_id, mod_prac_code, delete_status FROM modifiers_tbl ORDER BY mod_prac_code";
$rs = imw_query($qry);
while ($row = imw_fetch_assoc($rs)) {
    $sel = '';
    $color = '';
    if ($row['delete_status'] == 1)
        $color = 'color:#CC0000!important';

    if (sizeof($modifiers_sel) > 0) {
        if ($modifiers_sel[$row['modifiers_id']])
            $sel = "SELECTED";
    }

    $arrModifiers[$row['modifiers_id']] = $row['mod_prac_code'];
    $modifier_options .= '<option value="' . $row['modifiers_id'] . '" ' . $sel . ' style="' . $color . '">' . $row['mod_prac_code'] . '</option>';
}
$allModCount = sizeof($arrModifiers);


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
		if (sizeof($ins_carriers_sel) > 0) {
			if ($ins_carriers_sel[$ins_id]) {
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

        if ($insuranceGrp_sel[$grp_ins_ids])
            $selected = 'SELECTED';
        $ins_group_options .= "<option value='" . $grp_ins_ids . "' " . $selected . ">" . $ins_grp_name . "</option>";
    }
}


//GET GROUPS NAME
$rs = imw_query("Select  gro_id,name,del_status from groups_new order by name");
$core_drop_groups = "";
while ($row = imw_fetch_array($rs)) {
    $sel = '';
    $color = '';
    if ($row['del_status'] == '1')
        $color = 'color:#CC0000!important';

    if ($grp_id_sel[$row['gro_id']])
        $sel = 'SELECTED';

    $core_drop_groups .= '<option value="' . $row['gro_id'] . '" ' . $sel . ' style="' . $color . '" >' . $row['name'] . '</option>';
}
$allGrpCount = sizeof(explode('</option>', $core_drop_groups)) - 1;


//--- GET POS FACILITY NAME ----
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

if($dbtemp_name=='Referring Revenue' || $dbtemp_name=='Provider Revenue'){ 
	$facilityName = $sch_facility_options;
} 

//SEARCH MODE DEFAULT SELECTION
$default_date_range_for='';
if(!$_POST['form_submitted']){
	$default_date_range_for='dos';
	if(isset($filter_arr['dot'])){ $default_date_range_for='transaction_date';}
	elseif(isset($filter_arr['dos'])){ $default_date_range_for='dos';}
	elseif(isset($filter_arr['dor'])){ $default_date_range_for='date_of_payment';}
}
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
            <input type="hidden" name="callby_saved_dropdown" id="callby_saved_dropdown" value="<?php echo $_POST['callby_saved_dropdown'];?>">
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
                  	<div class="reportlft">
                    	<div class="practbox">
                      	<div class="anatreport"><h2>Practice Filter</h2></div>
                        <div class="clearfix"></div>
                        <div class="pd5" id="searchcriteria">
                        	<div class="row">
                          	<div class="col-sm-4">
                            	<label>Groups</label>
                              <select name="grp_id[]" id="grp_id" data-container="#group_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                              <?php echo $core_drop_groups; ?>
                              </select>
                           	</div>
                            
                            <div class="col-sm-4">
                                <label>Facility</label>
                                <select name="facility_id[]" id="facility_id" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                    <?php echo $facilityName; ?>
                                </select>
                            </div>
                            
                            <div class="col-sm-4">
                              <label>Physician</label>
                              <select name="filing_provider[]" id="filing_provider" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                  <?php echo $physicianName; ?>
                              </select>
                            </div>
							<div class="col-sm-6">
                              <label>Crediting Provider</label>
                              <select name="crediting_provider[]" id="crediting_provider" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                  <?php echo $creditPhysicinName; ?>
                              </select>
                            </div>                            
                            <div class="col-sm-6">
                              <label>Operator</label>
                              <select name="operator_id[]" id="operator_id" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                  <?php echo $operatorOption; ?>
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
                                            
                            <div class="col-sm-12 mt2">
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="DateRangeFor" id="dos" onclick="javascript:enable_disable_val(this.value);" value="date_of_service"  <?php if ($_POST['DateRangeFor'] == 'date_of_service' || $default_date_range_for=='dos') echo 'CHECKED'; ?> />
                            		<label for="dos">DOS</label>
                            	</div>
                              
                              <div class="radio radio-inline pointer">
                              	<input type="radio" name="DateRangeFor" id="dor" onclick="javascript:enable_disable_val(this.value);" value="date_of_payment" <?php if ($_POST['DateRangeFor'] == 'date_of_payment' || $default_date_range_for=='date_of_payment') echo 'CHECKED'; ?> />
                                <label for="dor">DOR</label>
                             	</div>
                              
                              <div class="radio radio-inline pointer">
                              	<input type="radio" name="DateRangeFor" id="dot" onclick="javascript:enable_disable_val(this.value);" value="transaction_date" <?php if ($_POST['DateRangeFor'] == 'transaction_date' || $default_date_range_for=='transaction_date') echo 'CHECKED'; ?> /> 
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
								<div class="col-sm-4">
									<label for="insuranceGrp">Insurance Group</label>
								  <select name="insuranceGrp[]" id="insuranceGrp" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
										<?php echo $ins_group_options; ?>
									</select>
								</div>
								<div class="col-sm-4" >
									<label for="ins_carriers">Ins. Carriers</label>
								  <select name="ins_carriers[]" id="ins_carriers" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
								  <?php echo $insComName_options; ?>
								  </select>
								</div>
								<div class="col-sm-4" >
									<label for="icd10_codes">ICD10 Codes</label>
								  <select name="icd10_codes[]" id="icd10_codes" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
								   <?php echo $all_dx10_code_options; ?>
								  </select>
								</div>
								<div class="col-sm-4" >
									<label for="cpt">CPT</label>
								  <select name="cpt[]" id="cpt" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
								   <?php echo $cpt_code_options; ?>
								  </select>
								</div>
								<div class="col-sm-4" >
									<label for="modifiers">Modifiers</label>
								  <select name="modifiers[]" id="modifiers" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
								   <?php echo $modifier_options; ?>
								  </select>
								</div>
								<div class="col-sm-4" >
									<label for="adjustmentId">Adj. Code</label>
								  <select name="adjustmentId[]" id="adjustmentId" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
								   <?php echo $adj_code_options; ?>
								  </select>
								</div>
								<div class="col-sm-4" >
									<label for="wrt_code">Write-off Code</label>
								  <select name="wrt_code[]" id="wrt_code" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
								   <?php echo $write_off_options; ?>
								  </select>
								</div>
								<div class="col-sm-8">
									<label for="ins_types">Check # (Comma Sep.)</label>
									<input name="checkNo" id="checkNo"  class="form-control" value="<?php echo $_POST['checkNo'];?>">
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
                                  <input type="radio" name="output_option" id="output_csv" value="output_csv" <?php if ($_POST['output_option'] == 'output_csv' || $_POST['output_option'] == '') echo 'CHECKED'; ?>/> 
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
										if($_POST['callby_saved_dropdown']==''){
											if ($_POST['form_submitted']) {
													include('transaction_details_result.php');
											} else {
												echo '<div class="text-center alert alert-info">No Search Done.</div>';
											}
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
<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
	<input type="hidden" name="file_format" id="file_format" value="zip">
    <input type="hidden" name="zipName" id="zipName" value="">	
    <input type="hidden" name="file" id="file" value="<?php echo $zip_file_name;?>" />
</form> 
<script type="text/javascript">
	var dbtemp_name='<?php echo $dbtemp_name;?>';
	var processReport='<?php echo $processReport; ?>';
	var printFile = '<?php echo $printFile; ?>';

	$(function () { $('[data-toggle="tooltip"]').tooltip(); 
		<?php if( $dbtemp_name == 'Practice Analytics' ){?>
		$('input[name="processReport"]').on('change',function(){
			if($(this).val().toLowerCase() == 'summary'){
				$('#cpt_check').prop('checked', false);
				$('#cpt_check').prop('disabled', true);
			} else{
				$('#cpt_check').prop('disabled', false);
			}
		});
		<?php } ?>
	});
	
	function enable_disable_val(ctrlVal){
		if(ctrlVal=='transaction_date' || ctrlVal=='date_of_payment'){
			$('#checkNo').prop('disabled', false);
		}else{
			$('#checkNo').prop('disabled', true);
		}
	}
	
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
		<?php if( $dbtemp_name == 'Practice Analytics' ){?>
		if($('input[name="processReport"]:checked').val().toLowerCase() == 'summary'){
				$('#cpt_check').prop('checked', false);
				$('#cpt_check').prop('disabled', true);
			} else{
				$('#cpt_check').prop('disabled', false);
			}
		<?php } ?>
	});

	var file_location = '<?php echo $file_location; ?>';
	var printFile = '<?php echo $printFile; ?>';
	var HTMLCreated='<?php echo $HTMLCreated; ?>';

	
	//BUTTONS
	var mainBtnArr = new Array();
	top.btn_show("PPR", mainBtnArr);
	//-------	
	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');

		document.frm_reports.submit();
	}


	$(document).ready(function (e) {
		var DateRangeFor='<?php echo $_POST['DateRangeFor'];?>';
		if(DateRangeFor=='')DateRangeFor='transaction_date';
		enable_disable_val(DateRangeFor);
		DateOptions('<?php echo $_POST['dayReport'];?>');
		
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
		if(printFile==1){
			$(".fltimg .toggle-sidebar").click();
			download_csv();
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