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

ini_set("memory_limit","3072M");
set_time_limit (0);

$dateFormat= get_sql_date_format();
$curDate = date($phpDateFormat.' h:i A');

$FCName= $_SESSION['authId'];
$pureSelfPay=false;

//check is pure self pay (only)selected
if($ins_type=='Self Pay')
{
	$pureSelfPay=true;	
	//if this option is selected then we need to ignore selected insurance companies
	$insuranceName='';
}


$printFile = true;
if($_POST['form_submitted']){

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

	//-- OPERATOR INITIAL -------
	$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);
	
	$printFile = false;
	$Sdate = $Start_date;
	$Edate = $End_date;
	$writte_off_arr = array();
	$arrInsOfEnc=array();
	$arrrCPTCat2=array(1=>'Service', 2=>'Material');

	// COMBINE INS AND INS GROUPS
	if(empty($ins_carriers)==false){ $tempInsArr[] = implode(",",$ins_carriers); }
	if(empty($insuranceGrp)==false){ $tempInsArr[] = implode(",",$insuranceGrp); 	}
	$str_insuranceGrp=implode("|",$insuranceGrp);
	$str_insuranceComp=implode(",",$ins_carriers);
	
	$tempSelIns = implode(',', $tempInsArr);
	$tempInsArr = array();
	if(empty($tempSelIns)==false){
	$tempInsArr = explode(',', $tempSelIns);
	}
	$tempInsArr = array_unique($tempInsArr);
	$insuranceName  = implode(',', $tempInsArr);
	$arrInsurance=array();
	if(sizeof($tempInsArr)>0){
		$arrInsurance=array_combine($tempInsArr,$tempInsArr);
	}
	unset($tempInsArr);
	

	$arrSelOprators=array();
	if(sizeof($operator_id)>0){
		$operator_id=array_combine($operator_id, $operator_id);
		$arrSelOprators=$operator_id;
	}
	
	// Merge CPT Code and CPT Categories
	$tmpCptCodeArr = array();
	$cpt_code_id= array_filter($cpt_code_id);
	$cpt_cat_id= array_filter($cpt_cat_id);
	
	$cpt_categories=(sizeof($cpt_cat_id)>0)? implode('|', $cpt_cat_id) :'';
	$cpt_ids=(sizeof($cpt_code_id)>0)? implode(',', $cpt_code_id) :'';
	$cpt_code_id_title = implode(',',$cpt_code_id);
	$cpt_cat_id_title  = implode(',',$cpt_cat_id);
	
	if(empty($cpt_code_id)==false) $tmpCptCodeArr[] = implode(',',$cpt_code_id);
	if(empty($cpt_cat_id)==false ) $tmpCptCodeArr[] = implode(',',$cpt_cat_id);
	
	$tmpCptCodeStr = implode(',',$tmpCptCodeArr);
	$cpt_code_id = explode(',',$tmpCptCodeStr);
	
	//SETTINGS OF DX10 CODES
	$icd10='';
	if(sizeof($icd10_codes)>0){
		$icd10=implode('|', $icd10_codes);
		$t_str=implode(",", $icd10_codes);
		$icd10_codes=explode(',',$t_str);
	}

	$tmp=array();	
	foreach($cpt_cat_2 as $catId)
	{
		if($arrrCPTCat2[$catId])$tmp[$arrrCPTCat2[$catId]]=$arrrCPTCat2[$catId];
	}
	if(sizeof($tmp)>0)
	{
		$selCptCat2=implode(', ',$tmp);
	}

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_id)<=0 && isPosFacGroupEnabled()){
		$facility_id = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_id)<=0){
			$facility_id[0]='NULL';
		}
	}

	//MERGING OF POS FACILITIES AND POS FACILITIES GROUPS
	if(sizeof($facility_id)>0){
		$facility_id=array_combine($facility_id, $facility_id);
	}
	if(sizeof($pos_facility_groups)>0){
		$strPosFacGroupIds=implode(',',$pos_facility_groups);
		$qry = "Select pos_facility_id from pos_facilityies_tbl WHERE posfacilitygroup_id IN(".$strPosFacGroupIds.")";
		$qryRs = imw_query($qry);
		while($qryRes  =imw_fetch_assoc($qryRs)){
			$facility_id[$qryRes['pos_facility_id']] = $qryRes['pos_facility_id'];
		}
		//IF NO MAPPING FOUND THEN STOP RETURNING ANY RECORD BY PASSING '00000'
		if(sizeof($facility_id)<=0){
			$facility_id[0]='00000';
		}
	}
	// ------------------------------	
	
	//MAKE COMMA SEPEARATED OF ALL SEARCH CRITERIA
	$grp_id= (sizeof($grp_id)>0) ? implode(',',$grp_id) : '';
	$sc_name= (sizeof($facility_id)>0) ? implode(',',$facility_id) : '';
	$Physician= (sizeof($filing_provider)>0) ? implode(',',$filing_provider) : '';
	$credit_physician= (sizeof($crediting_provider)>0) ? implode(',',$crediting_provider) : '';
	$cpt_code_id= (sizeof($cpt_code_id)>0) ? implode(',',$cpt_code_id) : '';
	$insuranceGrp= (sizeof($insuranceGrp)>0) ? implode(',',$insuranceGrp) : '';
	$insuranceName= (sizeof($arrInsurance)>0) ? implode(',',$arrInsurance) : '';
	$operatorName= (sizeof($operator_id)>0) ? implode(',',$operator_id) : '';
	$dx_code10= (sizeof($icd10_codes)>0) ? "'".implode("','", $icd10_codes)."'"  : '';
	$ins_type= (sizeof($ins_types)>0) ? implode(',',$ins_types) : '';
	$cpt_cat_2= (sizeof($cpt_cat_2)>0) ? implode(',',$cpt_cat_2) : '';
	//---------------------------------------

	// MAKE Search Criteria Vars
	$varCriteria=$grp_id.'~'.$sc_name.'~'.$Physician.'~'.$credit_physician.'~'.$DateRangeFor.'~'.$total_method;
	$varCriteria.='~'.$dayReport.'~'.$Start_date.'~'.$End_date.'~'.$processReport.'~'.$registered_fac.'~'.$viewBy;
	$varCriteria.='~'.$cpt_ids.'~'.$dx_code.'~'.$adjustmentId.'~'.$str_insuranceGrp.'~'.$str_insuranceComp;
	$varCriteria.='~'.$ins_type.'~'.$wrt_code.'~'.$modifiers.'~'.$operatorName.'~'.$hourFrom.'~'.$hourTo.'~'.$icd10;
	$varCriteria.='~'.$no_del_amt.'~'.$sort_by.'~'.$output_option.'~'.$pay_location.'~'.$cpt_categories.'~'.$billing_by;
	//---------------------

	//--- CHANGE DATE FORMAT -------
	if($Start_date != '' && $End_date != ''){
		$Start_date = getDateFormatDB($Start_date);
		$End_date = getDateFormatDB($End_date);	
	}

	$dateDiff= strtotime($End_date)-strtotime($Start_date);
	$dateDiff= round($dateDiff/ (60*60*24));
	
	//--- GET GROUP NAME ---
	$group_name = "All Selected";
	if(empty($grp_id) === false){
		$group_query = "select name from groups_new where gro_id = '$grp_id'";
		$groupQryRs = imw_query($group_query);		
		$groupQryRes = imw_fetch_assoc($groupQryRs);
		if(count($groupQryRes)>0){
			$group_name = $groupQryRes[0]['name'];
		}
	}
	
	// -- GET ALL POS-FACILITIES
	$arrAllFacilities=array();
	$arrAllFacilities[0] = 'No Facility';
	$qry = "select pos_facilityies_tbl.facilityPracCode as name,
		pos_facilityies_tbl.pos_facility_id as id,
		pos_tbl.pos_prac_code
		from pos_facilityies_tbl
		left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
		order by pos_facilityies_tbl.headquarter desc,
		pos_facilityies_tbl.facilityPracCode";
	$qryRs = imw_query($qry);
	while($qryRes  =imw_fetch_assoc($qryRs)){
		$id = $qryRes['id'];
		$name = $qryRes['name'];
		$pos_prac_code = $qryRes['pos_prac_code'];
		$arrAllFacilities[$id] = $name.' - '.$pos_prac_code;
	}						
	// ------------------------------
		
	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
		
		// two character array
		$operatorInitial = substr($providerResArr['fname'],0,1);
		$operatorInitial .= substr($providerResArr['lname'],0,1);
		$userNameTwoCharArr[$id] = strtoupper($operatorInitial);
	}

	//GET ALL INSURANCE COMAPNIES
	$arrAllInsCompanies[0]='No Insurance';
	$arrAllInsCompanies['SELF PAY']='SELF PAY';
	$arrAllInsCompaniesCSV[0]='No Insurance';
	$arrAllInsCompaniesCSV['SELF PAY']='SELF PAY';
	$qry="Select id as insCompId,in_house_code as insCompINHouseCode,name as insCompName FROM insurance_companies";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$id = $res['insCompId'];
		$insName = $res['insCompINHouseCode'];
		$insNameCSV = $res['insCompINHouseCode'];
		if(trim($insName) == ''){
			$insName = substr($res['insCompName'],0,20);
			$insNameCSV = $res['insCompName'];
		}
		$arrAllInsCompanies[$id] = $insName;
		$arrAllInsCompaniesCSV[$id] = $insNameCSV;
	}
	//GET ALL CPT PRACTICE CODES (FOR DELETED AMOUNTS)
	$arrAllCPTCodes[0]='No CPT';
	$qry="Select cpt_fee_id, cpt_prac_code, departmentId, cpt_desc FROM cpt_fee_tbl";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAllCPTCodes[$res['cpt_fee_id']] = $res['cpt_prac_code'];
		$arrDeptOfCptCodes[$res['cpt_fee_id']] = $res['departmentId'];
		$arrCptDesc[$res['cpt_fee_id']] = $res['cpt_desc'];
	}

	//CPT CATEGORY
	$arrAllCptCats[0]='No CPT Cat';
	$qry="Select cpt_cat_id, cpt_category FROM cpt_category_tbl";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAllCptCats[$res['cpt_cat_id']] = $res['cpt_category'];
	}

    //GET INSURANCE GROUP DROP DOWN
	$insGroupQryRes = imw_query("SELECT id, title FROM ins_comp_groups");
	$arrAllInsGroups[0] = 'No Insurance';
	$arrAllInsGroups['SELF PAY'] = 'SELF PAY';
	$arrAllInsGroups['Group Not Mapped'] = 'Group Not Mapped';
	$arrInsMapInsGroups[0]='0';
	$arrInsMapInsGroups['SELF PAY'] = 'SELF PAY';
	while ($row = imw_fetch_array($insGroupQryRes)) {
		$ins_grp_id = $row['id'];
		$ins_grp_name = $row['title'];
		$arrAllInsGroups[$ins_grp_id] = $ins_grp_name;
	
		$qry = "SELECT id FROM insurance_companies WHERE groupedIn = '" . $row['id'] . "'";
		$res = imw_query($qry);
		$tmp_grp_ins_arr = array();
		if (imw_num_rows($res) > 0) {
			while ($det_row = imw_fetch_array($res)) {
				$arrInsMapInsGroups[$det_row['id']]= $ins_grp_id;
			}
		}
	}	

	$reptBy = 'encounterIds';
	$reptByCredit = 'encounterIds';
	
	$reptByForFun='';
	if($DateRangeFor =='date_of_payment'){ $reptByForFun='dop';}
	else if($DateRangeFor =='transaction_date'){ $reptByForFun='dot';}

	$mainResArr = array();
	$search = 'DOP';
    if($DateRangeFor=='transaction_date'){ $search='DOT'; }
    
	if($DateRangeFor == 'date_of_service'){
		$search = 'DOS';
		require_once(dirname(__FILE__).'/office_production_dos.php');
	}else{
		require_once(dirname(__FILE__).'/office_production_dot.php');
	}
}

//GET SELECTED
$selgroup = $CLSReports->report_display_selected($grp_id,'group',1, $allGrpCount);
$selFac = $CLSReports->report_display_selected($sc_name,'practice',1, $allFacCount);	
$selPhy = $CLSReports->report_display_selected($Physician,'physician',1, $allPhyCount);
$selCrPhy = $CLSReports->report_display_selected($credit_physician,'physician',1, $allCrPhyCount);
$selInsurance = $CLSReports->report_display_selected($insuranceName,'insurance',1, $insurance_cnt);
$selCPT = $CLSReports->report_display_selected($cpt_code_id,'cpt_code',1, $allCPTCount);
$selDX10 = $CLSReports->report_display_selected($dx_code10,'dx_code10',1, $allDXCount10);
$selOpr = $CLSReports->report_display_selected($operatorName,'operator',1, $allOprCount);
$selInsType = 'All';
if($ins_type!=''){
	$ins_type_arr=explode(',',$ins_type);
	if(sizeof($ins_type_arr)<3){
		foreach($ins_type_arr as $insType){
			$tempArrIns[] = ucfirst(substr($insType, 0, strlen($insType)-13));
		}
		$selInsType = implode(', ', $tempArrIns);
	}
}

if($processReport == "Summary"){
	if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician')
	{
		require_once(dirname(__FILE__).'/office_production_physician_summary.php');
	}
	elseif($viewBy=='grpby_location')
	{
		require_once(dirname(__FILE__).'/office_production_location_summary.php');
	}
	elseif($viewBy=='grpby_location_physician')
	{
		require_once(dirname(__FILE__).'/office_production_location_physician_summary.php');
	}	
	elseif($viewBy=='grpby_cpt_cat')
	{
		require_once(dirname(__FILE__).'/office_production_cpt_cat_summary.php');
	}	
	elseif($viewBy=='grpby_location_cpt_cat')
	{
		require_once(dirname(__FILE__).'/office_production_location_cpt_cat_summary.php');
	}					
}
else{
	if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician')
	{
		require_once(dirname(__FILE__).'/office_production_physician_detail.php');
	}
	elseif($viewBy=='grpby_location')
	{
		require_once(dirname(__FILE__).
		'/office_production_location_detail.php');
	}	
	elseif($viewBy=='grpby_location_physician')
	{
		require_once(dirname(__FILE__).'/office_production_location_physician_detail.php');
	}		
	elseif($viewBy=='grpby_cpt_cat')
	{
		require_once(dirname(__FILE__).'/office_production_cpt_cat_detail.php');
	}			
	elseif($viewBy=='grpby_location_cpt_cat')
	{
		require_once(dirname(__FILE__).'/office_production_location_cpt_cat_detail.php');
	}				
}

$HTMLCreated=0;
if($printFile == true){
	$HTMLCreated=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$page_data= $styleHTML.$page_data;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$pdf_data;
	//$filebasepath =data_path().'UserId_'.$_SESSION['authId'].'/tmp/reports/';
	
	/*if( !is_dir($filebasepath) ){
		mkdir($filebasepath, 0755, true);
		chown($filebasepath, 'apache');
	}*/
	
	$now   = time();
	//DELETING 7 DAYS OLD FILES OF THIS REPORT
	foreach(glob(data_path()."UserId_".$_SESSION['authId']."/tmp/office_production_*") as $html_file_names){
		if($html_file_names){
			if($now - filemtime($html_file_names) >=  60 * 60 * 24 * 7) { // 7 days
				unlink($html_file_names);
			}
		}
	}

	$pdfName = '/office_production_'.$now.'.html';
	$file_location = write_html($strHTML,$pdfName);
	
}else{
	if($callFrom!='scheduled'){
		$page_data = '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}

if($callFrom!='scheduled'){
	if($output_option=='output_csv'){
		echo '<div class="text-center alert alert-info">Please click on link near application bottom to download CSV file.</div>';
	}elseif($output_option=='output_pdf'){		
		echo '<div class="text-center alert alert-info">PDF generated in separate window.</div>';
	}else{
		echo $page_data;	
	}
}
?>
