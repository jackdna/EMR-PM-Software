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

$op='l';

if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}

//--- GET DEPARTMENT NAME ---
$strdepartment = implode(',',$_REQUEST['department']);
$departmentName = $CLSReports->get_department_dropdown($strdepartment);

//SAVED SEARCH CRITERIA 
$report_name_for_saved='';
if($dbtemp_name=='Practice Analytics'){
	$report_name_for_saved='productivity_proficiency_criteria';
}
/*if (empty($_POST['saved_search']) == true) {
    $srchQry = "Select id FROM reports_searches WHERE uid=" . $_SESSION['authId'] . " AND report='productivity_proficiency' ORDER BY saved_date DESC LIMIT 0,1";
    if (isset($searchCriteria) && $searchCriteria != '') {
        $srchQry = "Select id, search_data FROM reports_searches WHERE id=" . $searchCriteria;
    }
    $srchRs = imw_query($srchQry);
    $srchRes = imw_fetch_array($srchRs);
    $_POST['saved_search'] = $srchRes['id'];
}
$saved_id = $_POST['saved_search'];
*/

if (isset($saved_searched_id) && $saved_searched_id != '') {
    $saved_id = $saved_searched_id;
}

$searchOptions = '';
$srchQry = "Select id, DATE_FORMAT(saved_date, '".$date_format_SQL." %H:%i:%s') as saved_date,search_data, report, report_name, uid FROM 
reports_searches WHERE report='".$report_name_for_saved."' ORDER BY saved_date DESC";
$srchRs = imw_query($srchQry);
while ($srchRes = imw_fetch_array($srchRs)) {
    $sel = '';
    //$strSaved.= $srchRes['id'].'#'.$srchRes['search_data'].'$';

   if ($saved_id == $srchRes['id']) {
        $sel = 'Selected';
        $dataParts = explode('~', $srchRes['search_data']);
        $grp_id = explode(',',$dataParts[0]);
        $facility_id = explode(',', $dataParts[1]);
        $filing_provider = explode(',',$dataParts[2]);
        $crediting_provider = explode(',',$dataParts[3]);
        $_POST['DateRangeFor'] = $dataParts[4];
        //$seltotalMethod = $dataParts[5];
        $_POST['dayReport'] = $dataParts[6];
        if ($_POST['dayReport'] == 'Selected Date') {
            $_POST['dayReport'] = 'Date';
        }
        $_REQUEST['Start_date'] = $dataParts[7];
        $_REQUEST['End_date'] = $dataParts[8];
        $_POST['processReport'] = $dataParts[9];
        $_POST['registered_fac'] = $dataParts[10];
        $_POST['viewBy'] = $dataParts[11];
        $cpt = explode(',',$dataParts[12]);
        //$icd10_codes = $dataParts[13];
       //$selAdjCodes = $dataParts[14];
        $insuranceGrp = explode('|',$dataParts[15]);
        $ins_carriers = explode(',',$dataParts[16]);
        $ins_types = explode(',',$dataParts[17]);
        //$wrt_code = $dataParts[18];
        $modifiers = explode(',', $dataParts[19]);
        $operator_id = explode(',',$dataParts[20]);
        $hourFrom = $dataParts[21];
        $hourTo = $dataParts[22];
        $icd10_codes = explode('|', str_replace("''", "",$dataParts[23]));
		$_POST['no_del_amt'] = $dataParts[24];
		$_REQUEST['sort_by'] = $dataParts[25];
		$outputOptions = $dataParts[26];
		$_POST['pay_location'] = $dataParts[27];
		$cpt_cat = explode('|', str_replace("''", "",$dataParts[28]));
		if($dataParts[29]=='billing_location')$_POST['billing_location']=1;elseif($dataParts[29]=='pay_location')$_POST['pay_location']=1;
    }


	//SET INSURANCE TYPES
	foreach($ins_types as $key => $val){
		$newval=$val;
		switch($val){
			case 'primaryInsuranceCoId':
				$newval='pri_ins_id';
			break;	
			case 'secondaryInsuranceCoId':
				$newval='sec_ins_id';
			break;	
			case 'tertiaryInsuranceCoId':
				$newval='tri_ins_id';
			break;	
		}
		$ins_types[$key]=$newval;
	}

	$sel2 = '';
	$sel2 = ($_POST['savedCriteria'] == $srchRes['id']) ? 'selected' : ''; //SELECTION IS BASED ON SELECTED VALUE OF DD
	$searchOptions .= '<option value="' . $srchRes['id'] . '" title="'.$GLOBALS['webroot'].'/library/images/delete_icon.png" '. $sel2 . '>' . trim($srchRes['report_name']). '</option>';

	$arrSearchName[]=$srchRes['report_name'];	
}
json_encode($arrSearchName);


/* if($_POST['processReport']!='')$processReport=$_POST['processReport'];
  if(empty($_POST['selDateRangeFor'])==false)$DateRangeFor=$_POST['selDateRangeFor'];
  if(empty($DateRangeFor)==true)$DateRangeFor='date_of_service';
 */

/*$arrSelGroups = array();
if (empty($selGroups) == false) {
    $arrSelGroups = explode(',', $selGroups);
    $arrSelGroups = array_combine($arrSelGroups, $arrSelGroups);
}
$arrSelCPTCodes = array();
if (empty($selCPTCodes) == false) {
    $arrSelCPTCodes = explode(',', $selCPTCodes);
    $arrSelCPTCodes = array_combine($arrSelCPTCodes, $arrSelCPTCodes);
}
$arrSelModifiers = array();
if (empty($selModifiers) == false) {
    $arrSelModifiers = explode(',', $selModifiers);
    $arrSelModifiers = array_combine($arrSelModifiers, $arrSelModifiers);
}
$arrSelAdjCodes = array();
if (empty($selAdjCodes) == false) {
    $arrSelAdjCodes = explode(',', $selAdjCodes);
    $arrSelAdjCodes = array_combine($arrSelAdjCodes, $arrSelAdjCodes);
}
$arrSelWriteOff = array();
if (empty($selWriteOff) == false) {
    $arrSelWriteOff = explode(',', $selWriteOff);
    $arrSelWriteOff = array_combine($arrSelWriteOff, $arrSelWriteOff);
}
$arrSelDXCodes = array();
if (empty($selDXCodes) == false) {
    $arrSelDXCodes = explode(',', $selDXCodes);
    $arrSelDXCodes = array_combine($arrSelDXCodes, $arrSelDXCodes);
}
$arrSelDX10Codes = array();
if (empty($selDX10Codes) == false) {
    $arrSelDX10Codes = explode(',', $selDX10Codes);
    $arrSelDX10Codes = array_combine($arrSelDX10Codes, $arrSelDX10Codes);
}
$arrSelInsComp = array();
if (empty($selInsComp) == false) {
    $arrSelInsComp = explode(',', $selInsComp);
    $arrSelInsComp = array_combine($arrSelInsComp, $arrSelInsComp);
}
$arrSelInsGroup = array();
if (empty($selInsGroup) == false) {
    $arrSelInsGroup = explode(',', $selInsGroup);
    $arrSelInsGroup = array_combine($arrSelInsGroup, $arrSelInsGroup);
}
$arrSelInsType = array();
if (empty($selInsType) == false) {
    $arrSelInsType = explode(',', $selInsType);
    $arrSelInsType = array_combine($arrSelInsType, $arrSelInsType);
}*/

//$objManageData->Smarty->assign('saved_data', $saved_data);
//$objManageData->Smarty->assign('saved_id', $saved_id);
//$objManageData->Smarty->assign('searchOptions',$searchOptions);
//$objManageData->Smarty->assign('arrSelInsType',$arrSelInsType);

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

//CLEARED VARIABLES SO DOES NOT GIVE PROBLEM IN RESULT FILES
//$grp_id=$facility_id=$filing_provider=$operator_id=$insuranceGrp=$ins_carriers=$icd10_codes=$modifiers=$adjustmentId=$wrt_code=$ins_types=0;


//IF SAVED SEARCH OR FORM VALUES HAVE TO SEND FURTHER
/*if ($_POST['call_from_saved'] == 'yes' || !$_POST['form_submitted']) {
    $cpt_code_id = $arrSelCPTCodes;
    $dx_code = $arrSelDXCodes;
    $dx_code10 = $arrSelDX10Codes;
    $adjustmentId = $arrSelAdjCodes;
    $wrt_code = $arrSelWriteOff;
    $modifiers = $arrSelModifiers;
    $insuranceName = $arrSelInsComp;
    //insuranceGrp
    $grp_id = $arrSelGroups;
    $sc_name = $selFacilities;
    $strPhysician = $selProviders;
    $str_credit_physician = $selCreditProviders;
    $operatorName = $selOperators;
    $ins_type = $arrSelInsType;
    $viewBy = $selViewBy;
    $DateRangeFor = $selDateRangeFor;
}*/

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
	
	if($cpt_cat_id[$cpt_ids])$sel='SELECTED';
    $cpt_cat_options .= "<option value='" . $cpt_ids . "' ".$sel.">".$cptCatName."</option>";	
}

//CPT CODES
$qry = "Select cpt_prac_code,cpt_fee_id,status,delete_status from cpt_fee_tbl 
		WHERE cpt_prac_code != '' order by cpt_prac_code asc";
$res = imw_query($qry);
$cpt_code_options=$cpt_code_options_deleted='';
while ($row = imw_fetch_array($res)) {
    $color = '';
    $cpt_fee_id = $row['cpt_fee_id'];
    $cpt_prac_code = (strlen($row['cpt_prac_code'])>38)? substr($row['cpt_prac_code'],0,38).'...': $row['cpt_prac_code'];
	
	$sel = '';
	if (sizeof($cpt_code_id) > 0) {
		if ($cpt_code_id[$cpt_fee_id]) {
			$sel = 'selected';
		}
    }
    
	if ($row['delete_status'] == 1 || $row['status'] == 'Inactive'){
		$color = 'color:#CC0000!important';
		$cpt_code_options_deleted .= "<option value='" . $cpt_fee_id . "' style='" . $color . "' ".$sel.">" . $cpt_prac_code . "</option>";
	}else{
		$cpt_code_options .= "<option value='" . $cpt_fee_id . "' style='" . $color . "' ".$sel.">" . $cpt_prac_code . "</option>";
	}
   
	$cptDetailsArr[$cpt_fee_id] = $cpt_prac_code;
}
$cpt_code_options.=$cpt_code_options_deleted;

//SET ICD10 CODE DROP DOWN
//$arrICD10Code = $CLSReports->getICD10Codes();
$all_dx10_code_options = $CLSReports->getICD10Codes('yes','','yes', $dx_code10_sel);
//$all_dx10_code_options = '';
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
$modifier_options = $modifier_options_deleted='';
$qry = "SELECT modifiers_id, mod_prac_code, delete_status FROM modifiers_tbl ORDER BY mod_prac_code";
$rs = imw_query($qry);
while ($row = imw_fetch_assoc($rs)) {
    $sel = '';
    $color = '';
    
    if (sizeof($modifiers_sel) > 0) {
        if ($modifiers_sel[$row['modifiers_id']])
            $sel = "SELECTED";
    }

    $arrModifiers[$row['modifiers_id']] = $row['mod_prac_code'];
	if ($row['delete_status'] == 1){
        $color = 'color:#CC0000!important';
		$modifier_options_deleted .= '<option value="' . $row['modifiers_id'] . '" ' . $sel . ' style="' . $color . '">' . $row['mod_prac_code'] . '</option>';
	}else{
		$modifier_options .= '<option value="' . $row['modifiers_id'] . '" ' . $sel . ' style="' . $color . '">' . $row['mod_prac_code'] . '</option>';
	}
}
$modifier_options.=$modifier_options_deleted;
$allModCount = sizeof($arrModifiers);

//SET INSURANCE COMPANY DROP DOWN
$insQryRes = insurance_provider_xml_extract();
$ins_comp_arr = array();
$insComName_options =$insComName_options_deleted= '';
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
            $insComName_options_deleted .= "<option value='" . $ins_id . "' ".$sel." style='color:#CC0000!important'>" . $ins_name . "</option>";
    }
}
unset($tempArrSort);
unset($tempInsCompdata);
$insComName_options.=$insComName_options_deleted;
$insurance_cnt = sizeof($ins_comp_arr);


//GET INSURANCE GROUP DROP DOWN
$insGroupQryRes = imw_query("SELECT id, title, delete_status FROM ins_comp_groups");
$ins_group_arr = array();
$ins_group_options = $ins_group_options='';
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

		if ($insuranceGrp_sel[$grp_ins_ids])$selected = 'SELECTED';
		
		$color = '';
		if ($row['delete_status']<=0 || $row['delete_status']==''){
			$ins_group_options .= "<option value='" . $grp_ins_ids . "' style='" . $color . "' " . $selected . ">" . $ins_grp_name . "</option>";
		}else{
			$color = 'color:#CC0000!important';
			$ins_group_options_deleted.= "<option value='" . $grp_ins_ids . "' style='" . $color . "' " . $selected . ">" . $ins_grp_name . "</option>";
		}
    }
}
$ins_group_options.=$ins_group_options_deleted;


//GET GROUPS NAME
$rs = imw_query("Select  gro_id,name,del_status from groups_new order by name");
$core_drop_groups = $core_drop_groups_deleted="";
$tempArrCount=array();
while ($row = imw_fetch_array($rs)) {
    $color = ''; $sel = '';
	if($grp_id_sel[$row['gro_id']])$sel = 'SELECTED';
	if ($row['del_status'] == '0'){
		$core_drop_groups .= '<option value="' . $row['gro_id'] . '" ' . $sel . ' style="' . $color . '" >' . $row['name'] . '</option>';
	}else{
		$color = 'color:#CC0000!important';
		$core_drop_groups_deleted .= '<option value="' . $row['gro_id'] . '" ' . $sel . ' style="' . $color . '" >' . $row['name'] . '</option>';
	}
	$tempArrCount[$row['gro_id']]=$row['gro_id'];  
}
$core_drop_groups.=$core_drop_groups_deleted;
$allGrpCount = sizeof($tempArrCount);


//REFERRING PHYSICIAN
$refGroupQry = "select ref_group_name,ref_group_id,ref_id from ref_group_tbl
			where ref_group_status = '0' order by ref_group_name";
$refGroupQryRs = imw_query($refGroupQry);
$selectedRefArr = array();
$sel=($selectedRef['-1'])? 'SELECTED' : '';
$ref_phy_group_options.= '<option value="-1" '.$sel.'>All Ref Phy</option>';
while($refGroupQryRes=imw_fetch_assoc($refGroupQryRs)){
	//cpt_group_name, cpt_code_name
	$sel=($selectedRef[$refGroupQryRes['ref_group_id']])? 'SELECTED' : '';
	
	$ref_group_name = ucwords($refGroupQryRes['ref_group_name']);
	$ref_phy_group_options.= '<option value="' .$refGroupQryRes['ref_group_id']. '" ' . $sel . '>' . $ref_group_name . '</option>';
}

//--- GET CPT GROUP DATA ---
$cptGroupQry = "select cpt_group_name, cpt_group_id from cpt_group_tbl
			where cpt_group_status = '0' order by cpt_group_name";
$cptGroupQryRs = imw_query($cptGroupQry);
$cpt_group_options.= '';
while($cptGroupQryRes=imw_fetch_assoc($cptGroupQryRs)){
	$cpt_group_name = ucwords($cptGroupQryRes['cpt_group_name']);
	$cpt_group_id = $cptGroupQryRes['cpt_group_id'];
	
	$sel=($selectedProc[$cptGroupQryRes['cpt_group_id']])? 'SELECTED' : '';

	$cpt_group_options.= '<option value="' .$cpt_group_id. '" '.$sel.'>' . $cpt_group_name . '</option>';	
}

/* if(isset($searchCriteria) && $searchCriteria != '')
  $objManageData->Smarty->assign('selSearchCriteria',$searchCriteria);
  else
  $objManageData->Smarty->assign('selSearchCriteria','');
 */

//--- GET SCHDEDULE FACILITY
$priv_pos_facility = $CLSReports->getFacilityName('', '0', 'array');
$sch_facility_options= $CLSReports->getSchFacilityInfo($facility_id_sel, $priv_pos_facility, 'options');
$sch_fac_cnt = sizeof(explode('</option>', $sch_facility_options)) - 1;
 
//--- GET POS FACILITY NAME ----
$facilityName = $CLSReports->getFacilityName($facility_id_sel, '1');
$allFacCount = sizeof(explode('</option>', $facilityName)) - 1;

//--- GET PHYSICIAN NAME ---
//PHYSICIAN NAMES ARE BASED ON PRIVILEGES FROM USER ADMIN SETTINGS
$privileged_prov_ids='';
$arr_privileged_prov_ids=array();
if($dbtemp_name=='Provider Revenue'){
	$qry="Select users.id, users.view_all_provider_financials, users.provider_financials, user_groups.name as 'group_name' 
	FROM users JOIN user_groups ON users.user_group_id=user_groups.id WHERE users.id='".$_SESSION['authId']."'";
	$rs=imw_query($qry);
	$res=imw_fetch_assoc($rs);
	$res['group_name']=strtolower($res['group_name']);
	if($res['group_name']=='physicians' || $res['group_name']=='technicians' || $res['group_name']=='nurse'){
		$privileged_prov_ids= ($res['view_all_provider_financials']=='0')? $_SESSION['authId'] : '';
	}else{
		//OTHER USER GROUPS
		$privileged_prov_ids= ($res['view_all_provider_financials']=='0')? $res['provider_financials'] : '';
		if($res['view_all_provider_financials']=='0' && empty($privileged_prov_ids)==true)$privileged_prov_ids='NULL';
	}

	if(empty($privileged_prov_ids)==false){
		$arr_privileged_prov_ids=explode(',', $privileged_prov_ids);
		$arr_privileged_prov_ids=array_combine($arr_privileged_prov_ids, $arr_privileged_prov_ids);
		//IF ONLY ONE ID PRIVILEGED THEN MAKE IT SELECT AUTOMATICALLY
		if(sizeof($arr_privileged_prov_ids)==1)$str_credit_physician=$strPhysician=$privileged_prov_ids;
	}else{
		//$arr_privileged_prov_ids[0]='0';
	}
}

$physicianName = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '', 'report', "","", $arr_privileged_prov_ids);
$phy_sel= ($filing_provider['0']=='0')? 'SELECTED' : '';
$physicianName.= '<option value="0" '.$phy_sel.'>None</option>';
$allPhyCount = sizeof(explode('</option>', $physicianName)) - 1;

//GET CREDIT PHYSICIANS
$creditPhysicinName = $CLSCommonFunction->drop_down_providers($str_credit_physician, '1', '1', '', 'report', "","", $arr_privileged_prov_ids);
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

//MAKE TIME OPTIONS
$timeOptions = '<option value="0">00 am</option>';
for ($i = 1; $i <= 23; $i++) {
    $fromSel = $toSel = '';
    $ampm = 'am';
    $num = $i;
    if ($i > 11) {
        if ($i > 12)
            $num = $i - 12;
        $ampm = 'pm';
    }
    if ($num < 10)
        $num = '0' . $num;

    if ($_POST['hourFrom'] == $i)
        $fromSel = 'SELECTED';
    $timeHourFromOptions .= '<option value="' . $i . '" ' . $fromSel . '>' . $num . ' ' . $ampm . '</option>';

    if ($_POST['hourTo'] == $i)
        $toSel = 'SELECTED';
    $timeHourToOptions .= '<option value="' . $i . '" ' . $toSel . '>' . $num . ' ' . $ampm . '</option>';
}

//INSURANCE TYPES
$arrInsTypes = array('pri_ins_id' => 'Primary', 'sec_ins_id' => 'Secondary', 'tri_ins_id' => 'Tertiary', 'Self Pay' => 'Self Pay');
$allowMultiInsType = true;
if( $dbtemp_name == 'Insurance Analytics') {
	$allowMultiInsType = false;
	$arrInsTypes = array('pri_ins_id' => 'Primary', 'sec_ins_id' => 'Secondary');
}
//$sel = ($ins_types[$key] || (sizeof($ins_types == 0) && $counter == 0) ) ? 'SELECTED' : ''; 

$counter=-1;
foreach ($arrInsTypes as $key => $val) {$counter++;
    $sel = '';
    if (sizeof($ins_types_sel) > 0) {
        if ($ins_types_sel[$key])
            $sel = 'SELECTED';
    }else if($dbtemp_name == 'Insurance Analytics' && $counter == 0)
						$sel = 'SELECTED';
    $ins_type_options .= '<option value="' . $key . '" ' . $sel . '>' . $val . '</option>';
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
	elseif(isset($filter_arr['doc'])){ $default_date_range_for='doc';}
}


//SET GROUP BY SELECTION
$grpby_physician_checked=$grpby_physician_checked=$grpby_operators_checked=$grpby_groups_checked=$grpby_procedure_checked='';
$grpby_ins_groups_checked=$grpby_department_checked='';
if($_POST['viewBy']==''){
	if(isset($filter_arr['grpby_physician']))$grpby_physician_checked='CHECKED';
	elseif(isset($filter_arr['grpby_facility']))$grpby_facility_checked='CHECKED';
	elseif(isset($filter_arr['grpby_operators']))$grpby_operators_checked='CHECKED';
	elseif(isset($filter_arr['grpby_groups']))$grpby_groups_checked='CHECKED';
	elseif(isset($filter_arr['grpby_procedure']))$grpby_procedure_checked='CHECKED';
	elseif(isset($filter_arr['grpby_ins_groups']))$grpby_ins_groups_checked='CHECKED';
	elseif(isset($filter_arr['grpby_department']))$grpby_department_checked='CHECKED';
}else{
	if($_POST['viewBy']=='physician')$grpby_physician_checked='CHECKED';
	if($_POST['viewBy']=='facility')$grpby_facility_checked='CHECKED';
	if($_POST['viewBy']=='operator')$grpby_operators_checked='CHECKED';
	if($_POST['viewBy']=='group')$grpby_groups_checked='CHECKED';
	if($_POST['viewBy']=='procedure')$grpby_procedure_checked='CHECKED';
	if($_POST['viewBy']=='ins_group')$grpby_ins_groups_checked='CHECKED';
	if($_POST['viewBy']=='department')$grpby_department_checked='CHECKED';
}

if($dbtemp_name == 'Itemized Receipts'){
	$_POST['output_option'] = 'output_pdf'; 
}

//REPORT LOGIC INFORMATION FLOATING DIV
$logicWidth = 75;
if($dbtemp_name == "Practice Analytics"){
	$report_key='productivity';
}else if($dbtemp_name == "Provider Analytics"){
	$logicWidth = 50;
	$report_key='provider_analytics';
}else if($dbtemp_name == "Facility Revenue"){
	$logicWidth = 50;
	$report_key='facility_revenue';
}else if($dbtemp_name == "Insurance Analytics"){
	$logicWidth = 65;
	$report_key='insurance_analytics';
}

$logicDiv = reportLogicInfo($report_key, 'tpl', $logicWidth);
//$logicCSS = reportLogicInfoHeader('tpl');	
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

            <style type="text/css">
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
                      	<div class="anatreport"><h2 class="this_rpt_info">Practice Filter
						<?php if($dbtemp_name == "Practice Analytics" || $dbtemp_name == "Provider Analytics" || $dbtemp_name == "Facility Revenue"
						|| $dbtemp_name == "Insurance Analytics"){ ?>
							<div id="rptInfoImg" style="float:right" class="rptInfoImg" onClick="showHideReportInfo(event, '<?php echo $logicWidth;?>')"></div>
						<?php }?>		
						</h2>
						</div>
                        <div class="clearfix"></div>
                        <div class="pd5" id="searchcriteria">
                        	<div class="row">
							<?php if($dbtemp_name == "Practice Analytics"){?>
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
                            <?php }?>
                          	<div class="col-sm-4">
                            	<label>Groups</label>
                              <select name="grp_id[]" id="grp_id" data-container="#group_drop" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['groups']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                              <?php echo $core_drop_groups; ?>
                              </select>
                           	</div>
                            
                            <div class="col-sm-4">
                                <label>Facility</label>
                                <select name="facility_id[]" id="facility_id" data-container="#select_drop" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['facility']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                    <?php echo $facilityName; ?>
                                </select>
                            </div>
                            
                            <div class="col-sm-4">
                            	<label>Department</label>
                              <select name="department[]" id="department" data-container="#select_drop" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['department']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                              <?php echo $departmentName; ?>
                              </select>
                          	</div>
                            
                            <div class="col-sm-4">
                              <label>Billing Provider</label>
                              <select name="filing_provider[]" id="filing_provider" data-container="#select_drop" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['filing_provider']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                  <?php echo $physicianName; ?>
                              </select>
                            </div>
                            
                            <div class="col-sm-4">
                              <label>Crediting Provider</label>
                              <select name="crediting_provider[]" id="crediting_provider" data-container="#select_drop" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['crediting_provider']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                  <?php echo $creditPhysicinName; ?>
                              </select>
                            </div>
                            <div class="col-sm-4">
                              <label>Operator</label>
                              <select name="operator_id[]" id="operator_id" data-container="#select_drop" class="selectpicker" <?php echo ($temp_id && !isset($filter_arr['operators']))?'disabled':''; ?> data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                  <?php echo $operatorOption; ?>
                              </select>
                            </div>
							<?php if($dbtemp_name == "Practice Analytics" || $dbtemp_name == "Provider Analytics" || $dbtemp_name == "Provider Revenue" || $dbtemp_name == "Referring Revenue" || $dbtemp_name == "Facility Revenue" || $dbtemp_name == "Insurance Analytics" || $default_report == 0){?>
							<div class="col-sm-12">
								<div class="checkbox pointer" style="padding-top:5px; padding-bottom:10px">
									<input type="checkbox" name="chksamebillingcredittingproviders" id="chksamebillingcredittingproviders" value="1" <?php if($_POST['chksamebillingcredittingproviders']==1)echo 'checked';?>  />
									<label for="chksamebillingcredittingproviders">Exclude where billing and crediting providers are same</label>
								</div>										  
							</div>	
							<?php } ?>							
							<?php if($dbtemp_name == "Itemized Receipts"){?>
							<div class="col-sm-12">
								<div class="">
									<!-- Pt. Search -->
									<div class="col-sm-12"><label>Patient</label></div>
									<div class="col-sm-5">
										<input type="hidden" name="patientId" id="patientId" value="<?php echo $_REQUEST['patientId'];?>">
										<input class="form-control" type="text" id="txt_patient_name" value="<?php echo $_REQUEST['txt_patient_name'];?>" name="txt_patient_name" onkeypress="{if (event.keyCode==13)return searchPatient()}" >
									</div> 
									<div class="col-sm-5">
										<select name="txt_findBy" id="txt_findBy" onkeypress="{if (event.keyCode==13)return searchPatient()}" class="form-control minimal">
											<option value="Active">Active</option>
											<option value="Inactive">Inactive</option>
											<option value="Deceased">Deceased</option> 
											<option value="Resp.LN">Resp.LN</option> 
											<option value="Ins.Policy">Ins.Policy</option>
										</select>
									</div> 
									<div class="col-sm-2 text-center">
										<button class="btn btn-success" type="button" onclick="searchPatient();"><span class="glyphicon glyphicon-search"></span></button>
									</div> 	
								</div>
							</div>
							<?php }?>
                            
                            <?php if($dbtemp_name=='Referring Revenue' || $dbtemp_name=='Facility Revenue'){ 
								if($dbtemp_name=='Referring Revenue'){
							?>
                                <div class="col-sm-4 ref_rev">
                                  <label>Ref. Phy. Groups</label>
                                  <select name="selectedRef[]" id="selectedRef" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                      <?php echo $ref_phy_group_options; ?>
                                  </select>
                                </div>
                                <?php }?>

                                <div class="col-sm-4 ref_rev">
                                  <label>CPT Groups</label>
                                  <select name="selectedProc[]" id="selectedProc" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                      <?php echo $cpt_group_options; ?>
                                  </select>
                                </div>                                

<!--                                <div class="col-sm-4 ref_rev">
                                  <label>Type</label>
                                  <select name="srh_type" id="srh_type" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                      <option value="Charges" <?php echo ($_POST['srh_type']=='Charges') ? 'SELECTED': ''; ?>>Charges</option>
                                      <option value="Receipts" <?php echo ($_POST['srh_type']=='Receipts') ? 'SELECTED': ''; ?>>Receipts</option>
                                  </select>
                                </div>-->
                            <?php } ?>

                              
                            
                            
                            <div class="col-sm-12">
                              <label>Period</label>
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
                              <div class="col-sm-6">
                                <div class="form-group">
                                  <label>Time From</label>
                                  <select name="hourFrom" id="hourFrom" class="selectpicker" data-width="100%" data-actions-box="false" title="Select" >
                                  <?php echo $timeHourFromOptions; ?>
                                  </select>
                                </div>
                              </div>
                              <div class="col-sm-6">
                                <div class="form-group">
                                  <label>Time To</label>
                                  <select name="hourTo" id="hourTo" class="selectpicker" data-width="100%" data-actions-box="false" title="Select">
                                  <?php echo $timeHourToOptions; ?>
                                  </select>
                                </div>
                              </div>
                            </div>
                                            
                            <div class="col-sm-6 mt2"><br /> 
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="processReport" id="summary" value="Summary" <?php if ($_POST['processReport'] == 'Summary' ) echo 'CHECKED'; ?> <?php echo ($temp_id && !isset($filter_arr['summary_detail']))?'disabled':''; ?> /> 
                                <label for="summary">Summary</label>
                            	</div>
                              <div class="radio radio-inline pointer">
                              	<input type="radio" name="processReport" id="detail" value="Detail" <?php if ($_POST['processReport'] == 'Detail' ||  empty($_POST['processReport'])) echo 'CHECKED'; ?> <?php echo ($temp_id && !isset($filter_arr['summary_detail']))?'disabled':''; ?> /> 
                                <label for="detail">Detail</label>
                            	</div>
                          	</div>
                            
                            <div class="col-sm-12 mt2">
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="DateRangeFor" id="dos" onclick="javascript:enable_disable_time(this.value); enable_disable_del(this.value);" value="date_of_service"  <?php if ($_POST['DateRangeFor'] == 'date_of_service' || $default_date_range_for=='dos') echo 'CHECKED'; ?> <?php echo ($temp_id && !isset($filter_arr['dos']))?'disabled':''; ?> />
                            		<label for="dos">DOS</label>
                            	</div>
                              
                              <div class="radio radio-inline pointer">
                              	<input type="radio" name="DateRangeFor" id="doc" onclick="javascript:enable_disable_time(this.value); enable_disable_del(this.value);" value="doc" <?php if ($_POST['DateRangeFor'] == 'doc' || $default_date_range_for=='doc') echo 'CHECKED'; ?> <?php echo ($temp_id && !isset($filter_arr['doc']))?'disabled':''; ?> /> 
                                <label for="doc">DOC</label>
                            	</div>
                              
                              <div class="radio radio-inline pointer">
                              	<input type="radio" name="DateRangeFor" id="dor" onclick="javascript:enable_disable_time(this.value); enable_disable_del(this.value);" value="date_of_payment" <?php if ($_POST['DateRangeFor'] == 'date_of_payment' || $default_date_range_for=='date_of_payment') echo 'CHECKED'; ?> <?php echo ($temp_id && !isset($filter_arr['dor']))?'disabled':''; ?> />
                                <label for="dor">DOR</label>
                             	</div>
                              
                              <div class="radio radio-inline pointer">
                              	<input type="radio" name="DateRangeFor" id="dot" onclick="javascript:enable_disable_time(this.value); enable_disable_del(this.value);" value="transaction_date" <?php if ($_POST['DateRangeFor'] == 'transaction_date' || $default_date_range_for=='transaction_date') echo 'CHECKED'; ?> <?php echo ($temp_id && !isset($filter_arr['dot']))?'disabled':''; ?> /> 
                                <label for="dot">DOT</label>
                             	</div>
                            </div>
							<div class="col-sm-12 mt2">
								<?php if($dbtemp_name=='Practice Analytics'){ ?>
                                <div class="checkbox checkbox-inline pointer">
                                    <input type="checkbox" name="no_del_amt" id="no_del_amt" value="1" <?php echo (!$_POST['form_submitted'] || $_POST['no_del_amt']=='1')?'checked':''; ?> />
                                    <label for="no_del_amt">No Del Amt.&nbsp;&nbsp;</label>
                                </div>
                                <?php } ?>
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
                              <select name="insuranceGrp[]" id="insuranceGrp" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" <?php echo ($temp_id && !isset($filter_arr['ins_group']))?'disabled':''; ?>>
                             		<?php echo $ins_group_options; ?>
                            	</select>
                          	</div>
                            
                            <div class="col-sm-4" >
                            	<label for="ins_carriers">Ins. Carriers</label>
                              <select name="ins_carriers[]" id="ins_carriers" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" <?php echo ($temp_id && !isset($filter_arr['ins_carriers']))?'disabled':''; ?>>
                              <?php echo $insComName_options; ?>
                              </select>
                           	</div>
                            
                            <div class="col-sm-4" >
                            	<label for="ins_types">Ins. Types</label>
                              <select name="ins_types[]" id="ins_types" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" <?php echo ($allowMultiInsType ? 'multiple' : '');?> data-actions-box="true" data-title="Select All" <?php echo ($temp_id && !isset($filter_arr['ins_types']))?'disabled':''; ?>>
                               <?php echo $ins_type_options; ?>
                              </select>
                           	</div>
                            
                            <div class="col-sm-4" >
                            	<label for="icd10_codes">ICD10 Codes</label>
                              <select name="icd10_codes[]" id="icd10_codes" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" <?php echo ($temp_id && !isset($filter_arr['icd10_codes']))?'disabled':''; ?>>
                               <?php echo $all_dx10_code_options; ?>
                              </select>
                           	</div>
                            
                            <div class="col-sm-4" >
                            	<label for="cpt_cat">CPT Category</label>
                              <select name="cpt_cat[]" id="cpt_cat" data-container="#select_drop" class="selectpicker" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" <?php echo ($temp_id && !isset($filter_arr['cpt_cat']))?'disabled':''; ?>>
                              	<?php echo $cpt_cat_options;?>
                              </select>
                           	</div>
                            
                            
                            <div class="col-sm-4" >
                            	<label for="cpt">CPT</label>
                              <select name="cpt[]" id="cpt" class="selectpicker pull-right" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" <?php echo ($temp_id && !isset($filter_arr['cpt']))?'disabled':''; ?>>
                               <?php echo $cpt_code_options; ?>
                              </select>
                           	</div>
                            <?php if($dbtemp_name=='Practice Analytics' || $dbtemp_name=='Provider Analytics' || $dbtemp_name=='Provider Revenue' || $dbtemp_name=='Referring Revenue' || $dbtemp_name=='Facility Revenue' || $dbtemp_name=='Insurance Analytics'){?>
                            <div class="col-sm-4">
								<label for="cpt_cat_2">CPT Category 2</label>
								<select name="cpt_cat_2[]" id="cpt_cat_2" class="selectpicker" data-width="100%" multiple data-actions-box="true" data-title="Select All">
									<option value="1" <?php echo (in_array('1',$cpt_cat_2) ? 'selected' : '');?>>Service</option>
									<option value="2" <?php echo (in_array('2',$cpt_cat_2) ? 'selected' : '');?>>Material</option>
								</select>
							</div>
							<?php } ?>
                            <div class="col-sm-4" >
                            	<?php if ( $dbtemp_name == 'Practice Analytics') { ?>
                             	<label for="modifiers">Modifiers</label>
								<select name="modifiers[]" id="modifiers" class="selectpicker pull-right" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
								<?php echo $modifier_options; ?>
								</select>
								<?php } ?>
                          	</div>
                          	<!-- Add Sort By Filter for practice analytics report only -->
                          	<?php if ( $dbtemp_name == 'Practice Analytics' || $default_report == 0) { ?>
                          	<div class="col-sm-4" >
                            	<label for="sort_by">Sort By</label>
								<select name="sort_by" id="sort_by" class="selectpicker pull-right" data-width="100%" data-title="Sort By" >
									<option value="patient" <?php echo ($sort_by == 'patient' ? 'selected' : '');?>>Patient</option>  
			                        <option value="cpt" <?php echo ($sort_by == 'cpt' ? 'selected' : '');?>>CPT Code</option>  
			                        <option value="dos" <?php echo (!in_array($sort_by,array('patient','cpt')) ? 'selected' : '');?>>Date of Service</option>  
								</select>
                          	</div>
                          	<?php } ?>
                            <div class="col-sm-6" >
                             	<div class="checkbox pointer">
                              	<input type="checkbox" name="registered_fac" id="registered_fac" value="1" <?php echo ($temp_id && !isset($filter_arr['registered_fac']))?'disabled':''; ?> <?php echo ($_POST['registered_fac'] == '1')?'checked':''; ?> onClick="decide_enable('registered_fac');" />
                              	<label for="registered_fac">Registered Facility</label>
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
                              	<input type="radio" name="viewBy" id="grpby_groups" <?php echo (!isset($filter_arr['grpby_groups']))?'disabled':''; ?> value="groups" <?php echo $grpby_groups_checked; ?>/> 
                                <label for="grpby_groups">Groups</label>
                             	</div>
                           	</div>
                            
                            <div class="col-sm-4">
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="viewBy" id="grpby_facility" <?php echo (!isset($filter_arr['grpby_facility']))?'disabled':''; ?> value="facility" <?php echo $grpby_facility_checked; ?>/> 
                                <label for="grpby_facility">Facility</label>
                             	</div>
                           	</div>
                            
                            <div class="col-sm-4">
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="viewBy" id="grpby_physician" <?php echo (!isset($filter_arr['grpby_physician']))?'disabled':''; ?> value="physician" <?php echo $grpby_physician_checked; ?>/> 
                                <label for="grpby_physician">Physician</label>
                            	</div>
                           	</div>
                            
                            <div class="col-sm-4">	
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="viewBy" id="grpby_operators" <?php echo (!isset($filter_arr['grpby_operators']))?'disabled':''; ?> value="operator" <?php echo $grpby_operators_checked; ?>/> 
                                <label for="grpby_operators">Operators</label>
                            	</div>
                           	</div>
                            
                            <div class="col-sm-4">	
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="viewBy" id="grpby_department" <?php echo (!isset($filter_arr['grpby_department']))?'disabled':''; ?> value="department" <?php echo $grpby_department_checked; ?>/> 
                                <label for="grpby_department">Department</label>
                            	</div>
                           	</div>
                           	
                           	<div class="col-sm-4">	
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="viewBy" id="grpby_procedure" <?php echo (!isset($filter_arr['grpby_procedure']))?'disabled':''; ?> value="procedure" <?php echo $grpby_procedure_checked; ?>/> 
                                <label for="grpby_procedure">Procedure</label>
                            	</div>
                           	</div>

                           	<div class="col-sm-4">	
                            	<div class="radio radio-inline pointer">
                              	<input type="radio" name="viewBy" id="grpby_ins_groups" <?php echo (!isset($filter_arr['grpby_ins_groups']))?'disabled':''; ?> value="ins_group" <?php echo $grpby_ins_groups_checked; ?>/> 
                                <label for="grpby_ins_groups">Ins Groups (Pri. Ins)</label>
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
                          	
                            <div class="col-sm-6">
                            	<div class="checkbox checkbox-inline pointer">
                                <?php
									$checked='';
									if(isset($filter_arr['inc_appt_detail'])){
										if ($_POST['inc_appt_detail'] == '1')$checked='CHECKED';
									}
								?>
                              	<input type="checkbox" name="inc_appt_detail" id="inc_appt_detail" <?php echo ($temp_id &&  !isset($filter_arr['inc_appt_detail']))?'disabled':''; ?> <?php echo $checked;?> value="1" /> 
                                <label for="inc_appt_detail">Appointment</label>
                             	</div>
                           	</div>
                            
                                         		
                            <div class="col-sm-6">
                            	<div class="checkbox checkbox-inline pointer">
                                <?php
									$checked='';
									if(isset($filter_arr['inc_ci_co_prepay'])){
										if ($_POST['inc_ci_co_prepay'] == '1')$checked='CHECKED';
									}
								?>                                
                              	<input type="checkbox" name="inc_ci_co_prepay" id="inc_ci_co_prepay" <?php echo ($temp_id && !isset($filter_arr['inc_ci_co_prepay']))?'disabled':''; ?> value="1" <?php echo $checked;?> /> 
                                <label for="inc_ci_co_prepay">CI/CO/Pre-Pay</label>
                            	</div>
                           	</div>
                            
                            <div class="col-sm-6">
                            	<div class="checkbox checkbox-inline pointer">
                                <?php
									$checked='';
									if(isset($filter_arr['inc_payments'])){
										if(!$_POST['form_submitted'] && $_POST['inc_payments'] == '')$checked='CHECKED';
										if ($_POST['inc_payments'] == '1')$checked='CHECKED';
									}
								?>                                 
                              	<input type="checkbox" name="inc_payments" id="inc_payments" <?php echo ($temp_id && !isset($filter_arr['inc_payments']))?'disabled':''; ?> value="1" <?php echo $checked;?> /> 
                                <label for="inc_payments">Payments</label>
                            	</div>
                           	</div>
                            
                            <div class="col-sm-6">
                            	<div class="checkbox checkbox-inline pointer">
                                <?php
									$checked='';
									if(isset($filter_arr['inc_adjustments'])){
										if(!$_POST['form_submitted'] && $_POST['inc_adjustments'] == '')$checked='CHECKED';
										if ($_POST['inc_adjustments'] == '1')$checked='CHECKED';
									}
								?>                                 
                              	<input type="checkbox" name="inc_adjustments" id="inc_adjustments" <?php echo ($temp_id && !isset($filter_arr['inc_adjustments']))?'disabled':''; ?> value="1" <?php echo $checked;?> /> 
                                <label for="inc_adjustments">Adjustments</label>
                            	</div>
                           	</div>
                            
                            <div class="clearfix"></div>
                            
                            <div class="col-sm-6">
                            	<div class="checkbox checkbox-inline pointer">
                                <?php
									$checked='';
									if(isset($filter_arr['inc_summary_charges'])){
										if(!$_POST['form_submitted'] && $_POST['inc_summary_charges'] == '')$checked='CHECKED';
										if ($_POST['inc_summary_charges'] == '1')$checked='CHECKED';
									}
								?>                                  
                              	<input type="checkbox" name="inc_summary_charges" id="inc_summary_charges" <?php echo ($temp_id && !isset($filter_arr['inc_summary_charges']))?'disabled':''; ?> value="1" <?php echo $checked;?> /> 
                                <label for="inc_summary_charges">Summary&nbsp;Charges</label>
                            	</div>
                           	</div>
                            
                            <div class="col-sm-6">
                            	<div class="checkbox checkbox-inline pointer">
                                <?php
									$checked='';
									if(isset($filter_arr['inc_del_trans'])){
										if ($_POST['inc_del_trans'] == '1')$checked='CHECKED';
									}
								?>   
                              	<input type="checkbox" name="inc_del_trans" id="inc_del_trans" <?php echo ($temp_id && !isset($filter_arr['inc_del_trans']))?'disabled':''; ?> value="1"  <?php echo $checked;?> /> 
                                <label for="inc_del_trans">Delete Transaction</label>
                            	</div>
                           	</div>
                          </div>
                      	</div>
                     	
                      </div>     

					  <?php if($dbtemp_name=='Practice Analytics'){	?>
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
                      <?php }?>
                      
                                            
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
                                <input type="radio" name="output_option" id="output_pdf" <?php echo ($temp_id && !isset($filter_arr['output_pdf']))?'disabled':''; ?> value="output_pdf" <?php if ($_POST['output_option'] == 'output_pdf') echo 'CHECKED'; ?>/> 
                                <label for="output_pdf">PDF</label>
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
											if (($_POST['call_from_saved'] == 'yes' || $_POST['form_submitted']) 
											&& ($dbtemp_name=='Practice Analytics' || $dbtemp_name=='Provider Analytics' || $dbtemp_name=='Provider Revenue'
											|| $dbtemp_name=='Referring Revenue' || $dbtemp_name=='Facility Revenue' || $dbtemp_name=='Insurance Analytics' || $dbtemp_name=='Itemized Receipts')) {
												
												if($dbtemp_name=='Practice Analytics'){
													include('productivity_result.php');
												}else if($dbtemp_name=='Provider Analytics'){
													include('provider_analytics_result.php');												
												}else if($dbtemp_name=='Provider Revenue'){
													include('provider_revenue_result.php');												
												}else if($dbtemp_name=='Referring Revenue'){
													include('referring_physician_revenue_result.php');												
												}else if($dbtemp_name=='Facility Revenue'){
													include('facility_revenue_result.php');												
												}else if($dbtemp_name=='Insurance Analytics'){
													include('ins_analytics_result.php');												
												}else if($dbtemp_name=='Itemized Receipts'){
													include('itemized_receipts_result.php');																									
												}else{
													include('productivity_result.php');
												}
												
											}elseif ($_POST['form_submitted']) {
												include('financial_analytic_custom.php');
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
<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
	<input type="hidden" name="file_format" id="file_format" value="csv">
    <input type="hidden" name="zipName" id="zipName" value="">	
    <input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
</form> 
<input type="hidden" name="pat_encounter_id" id="pat_encounter_id" value="<?php echo $str_encounters;?>">	

<script type="text/javascript">
	var dbtemp_name='<?php echo $dbtemp_name;?>';
	var op='<?php echo $op;?>';
	var output='<?php echo $_POST['output_option'];?>';
	var processReport='<?php echo $processReport; ?>';
	var arrSearchName=[];
	var arrSearchName=<?php echo json_encode($arrSearchName); ?>;
	

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
	if (printFile == '1') {
		if(dbtemp_name=='Itemized Receipts'){
			mainBtnArr[0] = new Array("print", "Print Receipt", "top.fmain.call_itemized_pdf();");
		}else{
			mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf('"+op+"');");
			if(dbtemp_name=='Practice Analytics' && processReport=="Detail"){
				mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.download_csv();");
			}else{
				mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
			}
		}
	}
	top.btn_show("PPR", mainBtnArr);
	//-------	

	function call_itemized_pdf(){
			//var parWidth = document.body.clientWidth;
			//var parHeight = document.body.clientHeight+80;	

		var parWidth=400;
		var parHeight=400;
		var pat_encounter_id=$('#pat_encounter_id').val();
		if(pat_encounter_id!=''){
			
			//var eid='';
			top.popup_win("../accounting/receipt.php?eId="+pat_encounter_id,'width='+parWidth+',height='+parHeight+',top=10,left=40,scrollbars=yes,resizable=yes');			
		}		
	}
	
	function get_report() {
		top.show_loading_image('hide');
		top.show_loading_image('show');
		
		$('#callby_saved_dropdown').val('');
			
		if(dbtemp_name=='Provider Revenue'){
			var ctrlObj=$('#filing_provider');
			var ctrlObjCredit=$('#crediting_provider');
			if(!ctrlObj.val() && !ctrlObjCredit.val()){
				top.show_loading_image('hide');
				alert('Please select Filing Provider or Crediting Provider.');
				return false;
			}
		}
		
		if(dbtemp_name=='Referring Revenue'){
			var ctrlObj=$('#selectedRef');
			if(!ctrlObj.val()){
				top.show_loading_image('hide');
				alert('Please select referring physician groups.');
				return false;
			}
		}

		if(dbtemp_name=='Facility Revenue'){
			var ctrlObj=$('#facility_id');
			if(!ctrlObj.val()){
				top.show_loading_image('hide');
				alert('Please select facility.');
				return false;
			}
		}

		if(dbtemp_name=='Itemized Receipts'){
			if($('#patientId').val()=='' || $('#txt_patient_name').val()==''){
				top.show_loading_image('hide');
				alert('Please select patient.');
				return false;				
			}
			

		}
		
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

	function enable_disable_time(ctrlVal){
		if(ctrlVal=='transaction_date'){
			$('#hourFrom').prop('disabled', false);
			$('#hourTo').prop('disabled', false);
		}else{
			$('#hourFrom').val('');
			$('#hourTo').val('');
			$('#hourFrom').prop('disabled', true);
			$('#hourTo').prop('disabled', true);
		}
	}

	function enable_disable_del(ctrlVal){
		if(document.getElementById('no_del_amt')){
			if(ctrlVal=='date_of_service'){
				$('#no_del_amt').prop('checked', false);
				$('#no_del_amt').prop('disabled', true);
			}else{
				$('#no_del_amt').prop('disabled', false);
			}
		}
	}

   function show_facility(ctrl){
	   if($('#'+ctrl).is(":checked")){
		   if(ctrl=='billing_location'){
			   $('#pay_location').prop('checked',false);
			   $('#registered_fac').prop('checked',false);

			   //$("input[name='DateRangeFor'][id='dor']").prop("checked", true);
			   $("input[name='DateRangeFor'][id='dos']").prop("disabled", false);
		   }else{
			   $('#billing_location').prop('checked',false);
			   $('#registered_fac').prop('checked',false);

			   //IF PAY LOCATION SELECTED THEN DISABLE DOS AND DOC
			   $("input[name='DateRangeFor'][id='dor']").prop("checked", true);
			   $("input[name='DateRangeFor'][id='dos']").prop("disabled", true);
		   }
		   $('#facility_id').html('<?php echo $sch_facility_options;?>');
		   $('#facility_id').selectpicker("refresh");
	   }else{
		   $('#facility_id').html('<?php echo $facilityName;?>');
	       $('#facility_id').selectpicker("refresh");
	   }
   }

   function decide_enable(callfrom){
	   if(callfrom=='registered_fac'){
		   if($('#registered_fac').is(":checked")){
			   $('#billing_location').prop('checked', false);
			   $('#pay_location').prop('checked', false);
			   show_facility();
		   }
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
			url: top.JS_WEB_ROOT_PATH+"/interface/physician_console/ajax_html.php?from=console&task=pt_details_ajax&return_data=yes&ptid="+id,
			dataType:'JSON',
			success: function(r){
				if(r.id){
					if(obj){
						set_xml_modal_values(r.id,r.pt_name);
					}else{
						$("#txt_patient_name").val(r.pt_name);
						$("#patientId").val(r.id);
					}
				}else{
					top.fAlert("Patient not exists.");
					$("#txt_patient_name").val('');
					$("#patientId").val('');
					return false;
				}	
			}
		});
	}	
	//previous name was getvalue
	function physician_console(id,name){
		document.getElementById("txt_patient_name").value = name;
		document.getElementById("patientId").value = id;
	}
	
	
	$(document).ready(function (e) {
		var DateRangeFor='<?php echo $_POST['DateRangeFor'];?>';
		if(DateRangeFor=='')DateRangeFor='transaction_date';
		DateOptions('<?php echo $_POST['dayReport'];?>');
		enable_disable_time('<?php echo $_POST['DateRangeFor'];?>');
		show_facility('<?php echo $p=($_POST['billing_location']=='1')?'billing_location':'pay_location';?>');
		
		//$('#call_from_saved').val('0');

		//DateOptions("<?php echo $_POST['dayReport']; ?>");
		//enableOrNot("<?php echo $_POST['processReport']; ?>");
		//addRemoveGroupBy("<?php echo $DateRangeFor; ?>");
		<?php if( $dbtemp_name == 'Practice Analytics' ) { ?>
		$("#savedCriteria").msDropdown({roundedBorder: false});
		oDropdown = $("#savedCriteria").msDropdown().data("dd");
		oDropdown.visibleRows(10);
		<?php } ?>
		
		function set_container_height(){
			$_hgt = (window.innerHeight) - $('#module_buttons').outerHeight(true);
			$('.reportlft').css({
				'height':$_hgt,
				'max-height':$_hgt,
				'overflow-x':'hidden',
				'overflow-y':'auto'
			});
			$('.report-content').css({'height':(window.innerHeight),'overflow-x':'hidden','overflow-y':'auto'});
			

		if(HTMLCreated==1){
			if(output=='output_pdf'){
				generate_pdf(op);
				top.show_loading_image('hide');
			}
			if(output=='output_csv'){
				if(dbtemp_name=='Practice Analytics' && processReport=="Detail"){
					download_csv();
				}else{
					export_csv();
				}
				top.show_loading_image('hide');
			}
		}
	} 

	$(window).load(function(){
		set_container_height();
		//TRIGGERING CLICK EVENT TO COLLAPSE LEFT SEARCH BAR
		if(HTMLCreated==1){
			$(".fltimg .toggle-sidebar").click();
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