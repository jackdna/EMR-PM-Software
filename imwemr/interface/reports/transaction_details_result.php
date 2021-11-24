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

/*
FILE : ProductivityResult.php
PURPOSE : PRODUCTIVITY RESULT FOR PHYSICIAN 
ACCESS TYPE : DIRECT
*/
//ini_set("memory_limit","20480M");
set_time_limit (0);


$dateFormat= get_sql_date_format();
$curDate = date(phpDateFormat().' h:i A');		

$FCName= $_SESSION['authId'];
$pureSelfPay=false;

//check is pure self pay (only)selected
if($ins_type=='Self Pay')
{
	$pureSelfPay=true;	
	//if this option is selected then we need to ignore selected insurance companies
	$insuranceName='';
}

$printFile = false;

if($_POST['form_submitted']){
	$printFile = false;
	$Sdate = $Start_date;
	$Edate = $End_date;
	$writte_off_arr = array();
	$arrInsOfEnc=array();

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

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_id)<=0 && isPosFacGroupEnabled()){
		$facility_id = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_id)<=0){
			$facility_id[0]='NULL';
		}
	}

	//MAKE COMMA SEPEARATED OF ALL SEARCH CRITERIA
	$grp_id= (sizeof($grp_id)>0) ? implode(',',$grp_id) : '';
	$sc_name= (sizeof($facility_id)>0) ? implode(',',$facility_id) : '';
	$Physician= (sizeof($filing_provider)>0) ? implode(',',$filing_provider) : '';
	$credit_physician= (sizeof($crediting_provider)>0) ? implode(',',$crediting_provider) : '';
	$cpt_code_id= (sizeof($cpt)>0) ? implode(',',$cpt) : '';
	$adjustmentId= (sizeof($adjustmentId)>0) ? implode(',',$adjustmentId) : '';
	//$insuranceGrp= (sizeof($insuranceGrp)>0) ? implode(',',$insuranceGrp) : '';
	//$insuranceName= (sizeof($arrInsurance)>0) ? implode(',',$arrInsurance) : '';
	$wrt_code= (sizeof($wrt_code)>0) ? implode(',',$wrt_code) : '';
	$modifiers= (sizeof($modifiers)>0) ? implode(',',$modifiers) : '';
	$operatorName= (sizeof($operator_id)>0) ? implode(',',$operator_id) : '';
	$dx_code10= (sizeof($icd10_codes)>0) ? "'".implode("','", $icd10_codes)."'"  : '';
	$ins_type= (sizeof($ins_types)>0) ? implode(',',$ins_types) : '';
	//---------------------------------------

	// COMBINE INS AND INS GROUPS
	if(sizeof($ins_carriers)>0){ $tempInsArr[] = implode(",",$ins_carriers); }
	if(sizeof($insuranceGrp)>0){ $tempInsArr[] = implode(",",$insuranceGrp); 	}
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

	
	//--- CHANGE DATE FORMAT -------
	if($Start_date != '' && $End_date != ''){
		$Start_date = getDateFormatDB($Start_date);
		$End_date = getDateFormatDB($End_date);	
	}

	
	//--- GET GROUP NAME ---
	$group_name = "All Selected";
	if(empty($grp_id) === false){
		$group_query = imw_query("select name from groups_new where gro_id = '$grp_id'");
		$groupQryRes = imw_fetch_assoc($group_query);		
		if(count($groupQryRes)>0){
			$group_name = $groupQryRes['name'];
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
	while($qryRes  =imw_fetch_array($qryRs)){
		$id = $qryRes['id'];
		$name = $qryRes['name'];
		$pos_prac_code = $qryRes['pos_prac_code'];
		$arrAllFacilities[$id] = $name.' - '.$pos_prac_code;
	}						
	// ------------------------------
		
	//GET SCHEDULE FACILITIES
	$arrPosFacOfSchFac=array();
	$rs=imw_query("Select id, fac_prac_code FROM facility");	
	while($res=imw_fetch_array($rs)){
		$arrPosFacOfSchFac[$res['id']] = $res['fac_prac_code'];
	}unset($rs);

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
	$qry="Select id as insCompId,in_house_code as insCompINHouseCode,name as insCompName FROM insurance_companies";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$id = $res['insCompId'];
		$insName = $ers['insCompINHouseCode'];
		if(trim($insName) == ''){
			$insName = substr($res['insCompName'],0,20);
		}
		
		$arrAllInsCompanies[$id] = $insName;
	}

	//GET ALL CPT PRACTICE CODES (FOR DELETED AMOUNTS)
	$arrAllCPTCodes[0]='No CPT';
	$qry="Select cpt_fee_id, cpt_prac_code, departmentId FROM cpt_fee_tbl";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAllCPTCodes[$res['cpt_fee_id']] = $res['cpt_prac_code'];
		$arrDeptOfCptCodes[$res['cpt_fee_id']] = $res['departmentId'];
	}

	//GET ALL DEPARTMENT CODES
	$arrDeptNames[0] = 'Undefined';
	$qry="Select DepartmentId, DepartmentCode, DepartmentDesc FROM department_tbl";
	$rs=imw_query($qry);
	while($res = imw_fetch_array($rs)){
		$arrDeptNames[$res['DepartmentId']] = $res['DepartmentDesc'].' - '.$res['DepartmentCode'];
	}
	asort($arrDeptNames);
	

	$reptBy = 'encounterIds';
	$reptByCredit = 'encounterIds';
	
	$reptByForFun='';
	if($DateRangeFor =='date_of_payment'){ $reptByForFun='dop';}
	else if($DateRangeFor =='transaction_date'){ $reptByForFun='dot';}

	$pay_crd_deb_arr =array();

	//DELETED CHECKED USED FOR ADJUSTMENTS AND PAYMENTS	
	$WcheckDel = $AcheckDel = $checkDel='yes';
	if($without_deleted_amounts=='1'){
		$WcheckDel = 'yes';
		$AcheckDel = 'yes';
		$checkDel = 'yes';
	}
	
	$mainResArr = array();
	$search = 'DOP';
	if($DateRangeFor=='transaction_date'){ $search='DOT'; }
	if($DateRangeFor == 'date_of_service'){
		$search = 'DOS';
		require_once(dirname(__FILE__).'/transaction_details_dos.php');
	}
	else{
		$reptByCredit='chargelistIds';
		require_once(dirname(__FILE__).'/transaction_details_dop.php');
	}
	$charge_list_detail_id_arr_str= join(',',$charge_list_detail_id_arr);

	//GETTING FACILITY FOR PATIENTS HAVING NO DEFAULT FACILITY
	if(sizeof($arrPatNoFacility)>0){
		$strPatNoFacility=implode(',', $arrPatNoFacility);
		$qry="Select sa_patient_id, sa_facility_id from schedule_appointments
		WHERE sa_patient_app_status_id not in (201, 18, 203, 19, 20)
		AND sa_patient_id IN(".$strPatNoFacility.") and sa_app_start_date <= now()
		ORDER BY sa_app_start_date ASC, sa_app_starttime ASC"; 
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$arrHomeFacOfPatients[$res['sa_patient_id']] = $arrPosFacOfSchFac[$res['sa_facility_id']];
		}unset($rs);
	}

	//---- GET WRITE OFF AMOUNT ----
	if($printFile ==true){
		//$main_encounter_id_str = implode(",", $main_encounter_id_arr);

		//GET SELECTED
		$selgroup = $CLSReports->report_display_selected($grp_id,'group',1, $allGrpCount);
		$selFac = $CLSReports->report_display_selected($sc_name,'practice',1, $allFacCount);
		$selPhy = $CLSReports->report_display_selected($Physician,'physician',1, $allPhyCount);
		$selCrPhy = $CLSReports->report_display_selected($credit_physician,'physician',1, $allCrPhyCount);
		$selInsurance = $CLSReports->report_display_selected($insuranceName,'insurance',1, $insurance_cnt);
		$selCPT = $CLSReports->report_display_selected($cpt_code_id,'cpt_code',1, $allCPTCount);
		$selDX = $CLSReports->report_display_selected($dx_code,'dx_code',1, $allDXCount);
		$selDX10 = $CLSReports->report_display_selected($dx_code10,'dx_code10',1, $allDXCount10);
		$selModifiers = $CLSReports->report_display_selected($modifiers,'modifiers',1, $allModCount);
		$selAdjCode = $CLSReports->report_display_selected($adjustmentId,'adj_code',1, $allAdjCount);
		$selWriteoff = $CLSReports->report_display_selected($wrt_code,'writeoff_code',1, $allWriteoffCount);
		$selOpr = $CLSReports->report_display_selected($operatorName,'operator',1, $allOprCount);

		$file='transaction_details_'.$_SESSION['authId'].'_'.strtotime(date('Y-m-d H:i:s')).'.csv';
		$csv_file_name = write_html("", $file);
		
		if($DateRangeFor=='date_of_service'){
			require_once(dirname(__FILE__).'/transaction_details_dos_view.php');
		}else{
			require_once(dirname(__FILE__).'/transaction_details_dop_view.php');
		}

		if(file_exists($csv_file_name)){
			
			$zip_file_name= str_replace('.csv', '.zip', $csv_file_name);
			$fParts=explode("/", $csv_file_name);
			$csvName = $fParts[count($fParts)-1];
			$zipName = str_replace('.csv','',$fParts[count($fParts)-1]).'.zip';
			$zip = new ZipArchive();
			if($zip->open(str_replace('.csv','',$csv_file_name).'.zip', ZipArchive::CREATE)!==TRUE) {
				exit("cannot open <$zipName>\n");
			}
			$zip->addFile($csv_file_name, $csvName);
			$zip->close();
			
			unlink($csv_file_name);
			
			echo '<div class="text-center alert alert-info">Please click on link near application bottom to download ZIP file.</div>';
		}
	}else{
		echo '<div class="text-center alert alert-info">No Record Found.</div>';		
	}
}
?>
