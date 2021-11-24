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

	$printFile = false;
	$Sdate = $Start_date;
	$Edate = $End_date;
	$writte_off_arr = array();
	$arrInsOfEnc=array();

	// COMBINE INS AND INS GROUPS
	if(empty($ins_carriers)==false){ $tempInsArr[] = implode(",",$ins_carriers); }
	if(empty($insuranceGrp)==false){ $tempInsArr[] = implode(",",$insuranceGrp); 	}
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

	// COMBINE CPT AND CPT CATEGORY
	$cpt_code_id='';
	if(sizeof($cpt)>0){ $tempCptArr[] = implode(",",$cpt); }
	if(sizeof($cpt_cat_id)>0){ $tempCptArr[] = implode(",",$cpt_cat_id); 	}
	$tempSelCpt = implode(',', $tempCptArr);
	$tempCptArr = array();
	if(empty($tempSelCpt)==false){
		$tempCptArr = explode(',', $tempSelCpt);
	}
	$tempCptArr = array_unique($tempCptArr);
	if(sizeof($tempCptArr)>0){
		$cpt_code_id=implode(',', $tempCptArr);
	}
	unset($tempCptArr);	

	
	$arrSelOprators=array();
	if(empty($operator_id)===false){
		$arrSelOprators=explode(',', $operator_id);
		$arrSelOprators=array_combine($arrSelOprators, $arrSelOprators);
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
	$adjustmentId= (sizeof($adjustmentId)>0) ? implode(',',$adjustmentId) : '';
	$insuranceGrp= (sizeof($insuranceGrp)>0) ? implode(',',$insuranceGrp) : '';
	$insuranceName= (sizeof($arrInsurance)>0) ? implode(',',$arrInsurance) : '';
	$wrt_code= (sizeof($wrt_code)>0) ? implode(',',$wrt_code) : '';
	$modifiers= (sizeof($modifiers)>0) ? implode(',',$modifiers) : '';
	$operatorName= (sizeof($operator_id)>0) ? implode(',',$operator_id) : '';
	$dx_code10= (sizeof($icd10_codes)>0) ? implode(",",$icd10_codes) : '';
	$ins_type= (sizeof($ins_types)>0) ? implode(',',$ins_types) : '';
	$department= (sizeof($department)>0) ? implode(',',$department) : '';
	//---------------------------------------

	// MAKE Search Criteria Vars
	$varCriteria=$grp_id.'~'.$sc_name.'~'.$Physician.'~'.$credit_physician.'~'.$DateRangeFor.'~'.$total_method;
	$varCriteria.='~'.$dayReport.'~'.$Start_date.'~'.$End_date.'~'.$summary_detail.'~'.$home_facility.'~'.$viewBy;
	$varCriteria.='~'.$cpt_code_id.'~'.$dx_code.'~'.$adjustmentId.'~'.$insuranceGrp.'~'.$insuranceName;
	$varCriteria.='~'.$ins_type.'~'.$wrt_code.'~'.$modifiers.'~'.$operatorName.'~'.$hourFrom.'~'.$hourTo.'~'.$dx_code10.'~'.$sort_by;
	//---------------------

	//--- CHANGE DATE FORMAT -------
	if($Start_date != '' && $End_date != ''){
		$Start_date = getDateFormatDB($Start_date);
		$End_date = getDateFormatDB($End_date);	
	}

	if($hourFrom != '' && $hourTo != ''){
		$ampmFrom=$ampmTo='am';
		$hourFrom=($hourFrom<10)? '0'.$hourFrom: $hourFrom;
		$hourTo=($hourTo<10)? '0'.$hourTo: $hourTo;
		$hourFromL=$hourFrom;
		$hourToL=$hourTo;

		if($hourFrom>=12){ $hourFromL=$hourFrom-12; $ampmFrom='pm';}
		if($hourTo>=12){ $hourToL=$hourTo-12; $ampmTo='pm';}
		$hourFromL=($hourFromL<=0)? 12: $hourFromL;
		$hourToL=($hourToL<=0)? 12: $hourToL;
		
		$hourFromL.=$ampmFrom;
		$hourToL.=$ampmTo;
		
		$hourFrom=$hourFrom.':00:00';
		$hourTo=$hourTo.':00:00';		
	}
	
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
	
	//REPLCAE CREDIT PHYSICIAN WITH 0
	if(empty($Physician) === false && empty($credit_physician) === false){
		if($Physician != $credit_physician){
			$arrPhysician=explode(',',$Physician);
			$arrCreditPhysician=explode(',',$credit_physician);
			for($i=0; $i<sizeof($arrPhysician); $i++){
				if($key=array_search($arrPhysician[$i],$arrCreditPhysician)){
					$arrCreditPhysician[$key]=0;
				}
			}
			$credit_physician_edited=implode(',',$arrCreditPhysician);
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
		
	//GET SCHEDULE FACILITIES
	$arrPosFacOfSchFac=array();
	$rs=imw_query("Select id, fac_prac_code FROM facility");	
	while($res=imw_fetch_assoc($rs)){
		$arrPosFacOfSchFac[$res['id']] = $res['fac_prac_code'];
		$sch_fac_arr[$res['fac_prac_code']][] = $res['id'];
		$sch_pos_fac_arr[$res['id']] = $res['fac_prac_code'];
	}unset($rs);

	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	$providerNameArr[0] = 'No Provider';
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
	while($res=imw_fetch_assoc($rs)){
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
	
		//GET INSURANCE GROUP DROP DOWN
	$insGroupQryRes = imw_query("SELECT id, title FROM ins_comp_groups");
	$arrAllInsGroups[0] = 'No Insurance';
	$arrInsMapInsGroups[0]='0';
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
	
	$arrAll_dep_cpt = array();
	$qry= "Select cpt_fee_id, cpt_prac_code FROM cpt_fee_tbl where departmentId in ($department)";
	$rs=imw_query($qry);
	while ($row = imw_fetch_array($rs)) {
		$arrAll_dep_cpt[] = $row['cpt_fee_id'];
	}
	if(sizeof($arrAll_dep_cpt)>0){
		$dept_cpt_id =implode(',', $arrAll_dep_cpt);
		$cpt_code_id .= $dept_cpt_id;
	}
	
	//GET ALL GROUPS
	$rs = imw_query("Select  gro_id,name,del_status from groups_new");
	while ($row = imw_fetch_array($rs)) {
		$arrAllGroups[$row['gro_id']]=$row['name'];
	}
	
	$reptBy = 'encounterIds';
	$reptByCredit = 'encounterIds';
	
	$reptByForFun='';
	if($DateRangeFor =='date_of_payment'){ $reptByForFun='dop';}
	else if($DateRangeFor =='transaction_date'){ $reptByForFun='dot';}

	$pay_crd_deb_arr =array();

	//DELETED CHECKED USED FOR ADJUSTMENTS AND PAYMENTS	
	$WcheckDel = $AcheckDel = $checkDel='no';
	if($without_deleted_amounts=='1'){
		$WcheckDel = 'yes';
		$AcheckDel = 'yes';
		$checkDel = 'yes';
	}
	
	//--- GET Appoinment Status---
	$status_query = "select id, status_name, alias from schedule_status";
	$statusQryRes = get_array_records_query($status_query);		
	$arrApptStatus[0] = 'Created/Restored';
	for($i=0; $i<sizeof($statusQryRes); $i++){	
		$status_name = $statusQryRes[$i]['alias'];
		$arrApptStatus[$statusQryRes[$i]['id']]=$status_name;
	}

	$mainResArr = array();
	$search = 'DOP';
	if($DateRangeFor=='transaction_date'){ $search='DOT'; }
	if($DateRangeFor == 'date_of_service'){
		$search = 'DOS';
		require_once(dirname(__FILE__).'/financial_analytic_custom_charges.php');
	}else{
		require_once(dirname(__FILE__).'/financial_analytic_custom_payments.php');
	}
	
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


	// *READ DETAIL*
	//GETTING CHARGES FOR ENCOUNERS WHICH HAVE NOT POSTED. IT HAS BELOW TWO TYPES OF ENCOUNTERS.
	// -ENCOUNTERS WHICH HAVE YET NOT POSTED AND NO ANY ADJUSTMENT AND PAYMENT DONE.
	// -ENCOUNTERS WHICH HAVE YET NOT POSTED BUT PAYMENT OR ADJUSEMENT HAS DONE DONE FOR THAT.
	// -QUERY IS DOS BASED
	$chargesForNotPosted='';
	if($DateRangeFor=='transaction_date'){		
		$qry="Select main.patient_id, main.encounter_id, (main.charges * main.units) as totalAmt, main.facility_id, main.billing_facility_id, main.primary_provider_id_for_reports  
		FROM report_enc_detail main JOIN patient_data pd ON pd.id=main.patient_id 
		WHERE main.first_posted_date='0000-00-00' AND main.del_status='0' AND (main.charges * main.units) = main.proc_balance 
		AND (main.date_of_service BETWEEN '$Start_date' and '$End_date')";
		if(empty($sc_name) == false){
			if($home_facility=='1'){
				$qry.= " and pd.default_facility IN ($sc_name)";
			}else{
				$qry.= " and main.facility_id IN ($sc_name)";	
			}
		}
		if(empty($grp_id) == false){
			$qry.= " and main.gro_id IN ($grp_id)";
		}
		if(empty($Physician) === false){
			$qry.= " and main.primary_provider_id_for_reports IN ($Physician)";
		}
		if(empty($credit_physician) === false){
			$qry.= " and main.sec_prov_id IN ($credit_physician)";
		}
		if($chksamebillingcredittingproviders==1){
			$qry.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
		}		
		if(trim($cpt_code_id) != ''){
			$qry.= " AND main.proc_code_id in ($cpt_code_id)";
		}
		if(trim($dx_code) != '' || trim($dx_code10) != ''){
			$qry.= ' AND (';
			$andOR='';
			if(trim($dx_code)!= ''){
				$qry.= " (main.dx_id1 in ($dx_code)
				or main.dx_id1 in ($dx_code)
				or main.dx_id3 in ($dx_code)
				or main.dx_id4 in ($dx_code))";
				$andOR=' OR ';
			}
			if(trim($dx_code10)!= ''){
				$qry.=$andOR." (main.dx_id1 in ($dx_code10)
				or main.dx_id2 in ($dx_code10)
				or main.dx_id3 in ($dx_code10)
				or main.dx_id4 in ($dx_code10))";
			}
			$qry.= ') ';	
		}
		if(trim($modifiers)!=''){
			$qry.= " and (main.mod_id1 IN($modifiers)
			OR main.mod_id2 IN($modifiers)
			OR main.mod_id3 IN($modifiers))";
		}
		if(trim($wrt_code)!= ''){
			if(empty($writeOffChdDetIdStr)==true){
				$qry.= " and main.write_off_code_id in ($wrt_code)";		
			}else{
				$qry.= " and (main.write_off_code_id in ($wrt_code) OR main.charge_list_detail_id IN(".$writeOffChdDetIdStr."))";
			}
		}
		if($adjustmentId!=''){
			$qry.= " and main.charge_list_detail_id IN(".$adjCodeChdDetIdStr.")";			
		}
		if(trim($insuranceName) != ''){
			if(trim($ins_type) == ''){
				$qry.= " and (main.pri_ins_id in ($insuranceName)
					or main.sec_ins_id in ($insuranceName)
					or main.tri_ins_id in ($insuranceName))";
			}
			else{
				$ins_type_arr=explode(',',$ins_type);
				$qry.= " and (";
				for($i=0;$i<count($ins_type_arr);$i++){
					$ins_nam=$ins_type_arr[$i];
					if(trim($ins_nam)!='Self Pay')
					{
						$mul_or="";
						if($i>0){
							$mul_or=" or ";
						}
					
						$qry.= " $mul_or main.$ins_nam in ($insuranceName)";
					}
				}
				$qry.= " )";
			}
		}
		if(empty($operatorName)==false){
			$qry.= " and main.operator_id in($operatorName)";
		}
		if($pureSelfPay==true)$qry.= " and main.proc_selfpay=1";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$chargesForNotPosted+=$res['totalAmt'];
		}
	}		
	//----------------------------------------------

	
	//FOR DELETED CHARGES AND PAYMENTS
	if($DateRangeFor != 'date_of_service'){
		$whereQry='';
		if(empty($sc_name) == false){
			if($home_facility=='1'){
				$whereQry .= " and patient_data.default_facility IN ($sc_name)";
			}else{
			$whereQry .= " and main.facility_id IN ($sc_name)";
		}
		}
		if(empty($grp_id) == false){
			$whereQry .= " and main.gro_id IN ($grp_id)";
		}
		if(empty($Physician) === false){
			$whereQry.= " and main.primary_provider_id_for_reports IN ($Physician)";
		}	
		if(empty($credit_physician) === false){
			$whereQry.= " and main.sec_prov_id IN ($credit_physician)";
		}
		if($chksamebillingcredittingproviders==1){
			$whereQry.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
		}
		if(empty($operatorName)==false){
			$whereQry= " AND trans.trans_del_operator_id in($operatorName)";
		}
		if(trim($cpt_code_id) != ''){
			$whereQry.= " AND main.proc_code_id in ($cpt_code_id)";
		}
		if(trim($dx_code) != '' || trim($dx_code10) != ''){
			$whereQry.= ' AND (';
			$andOR='';
			if(trim($dx_code)!= ''){
				$whereQry.= " (main.dx_id1 in ($dx_code)
				or main.dx_id2 in ($dx_code)
				or main.dx_id3 in ($dx_code)
				or main.dx_id4 in ($dx_code))";
				$andOR=' OR ';
			}
			if(trim($dx_code10)!= ''){
				$whereQry.=$andOR." (main.dx_id1 in ($dx_code10)
				or main.dx_id2 in ($dx_code10)
				or main.dx_id3 in ($dx_code10)
				or main.dx_id4 in ($dx_code10))";
			}
			$whereQry.= ') ';	
		}
		if(trim($modifiers)!=''){
			$whereQry.= " and (main.mod_id1 IN($modifiers)
			OR main.mod_id2 IN($modifiers)
			OR main.mod_id3 IN($modifiers))";
		}
		if(trim($insuranceName) != ''){
			if(trim($ins_type) == ''){
				$whereQry .= " and (main.pri_ins_id in ($insuranceName)
				or main.sec_ins_id in ($insuranceName)
				or main.tri_ins_id in ($insuranceName))";
			}
			else{
				$ins_type_arr=explode(',',$ins_type);
				$whereQry .= " and (";
				for($i=0;$i<count($ins_type_arr);$i++){
					$ins_nam=$ins_type_arr[$i];
					if(trim($ins_nam)!='Self Pay')
					{
						$mul_or="";
						if($i>0){
							$mul_or=" or ";
						}
						$whereQry .= " $mul_or main.$ins_nam in ($insuranceName)";
					}
				}
				$whereQry .= " )";
			}
		}

		// GET DELETED AMOUNTS
		$arrVoidPay = array();
		$tempRecords=array();
		$qry="Select trans.patient_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, trans.trans_method, 
		trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_dot, trans.trans_qry_type,
		DATE_FORMAT(trans.trans_dot,'".$dateFormat."') as trans_dot, trans.trans_dot, trans.trans_del_operator_id, trans.trans_del_date,
		DATE_FORMAT(main.date_of_service, '".$dateFormat."') as 'date_of_service', main.primary_provider_id_for_reports  as 'primaryProviderId',
		main.sec_prov_id, main.proc_code_id, main.facility_id, main.first_posted_date, pd.fname, pd.lname, pd.mname, pd.default_facility 
		FROM report_enc_trans trans 
		JOIN report_enc_detail main ON main.charge_list_detail_id = trans.charge_list_detail_id 
		LEFT JOIN patient_data pd ON pd.id = trans.patient_id 
		WHERE trans_del_operator_id>0 AND trans.trans_type!='default_writeoff'";
		//IF "WITHOUT DELETED AMOUNTS" CHECKED THEN ONLY DISPLAY DELETED AMOUNT	THAT HAVE DOS BEFORE SEARCH CRITERIA
		if($inc_del_trans=='1'){
			$qry.=" AND (DATE_FORMAT(trans.trans_del_date, '%Y-%m-%d') BETWEEN '$Start_date' AND '$End_date') AND main.date_of_service<'$Start_date'"; 	
		}else{
			$qry.=" AND (trans.trans_dot BETWEEN '$Start_date' AND '$End_date')"; 	
		}
		$qry.=$whereQry .' Order By ';
		$qry.= ($sort_by == 'patient') ? "pd.lname,pd.fname,main.date_of_service" : "main.date_of_service, pd.lname, pd.fname";
		
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			//IF DELETED BEFORE FIRST POSTED DATE THEN IGNORE THAT
			if($res['first_posted_date']=='0000-00-00' || $res['first_posted_date'] <= $res['trans_del_date']){
				$doctor_id=$res['primaryProviderId'];
				if($home_facility=='1'){
					$facility_id=$res['default_facility'];
				}else{
					$facility_id=$res['facility_id'];
				}
				$facility_id= (empty($facility_id)==true)? '0' : $facility_id;
				$encID = $res['encounter_id'];	

				$chgDetId= $res['charge_list_detail_id'];
				$cptCode = $arrAllCPTCodes[$res['proc_code_id']];
				$cptCode = (strlen($cptCode)>8) ? substr($cptCode,0, 8).'...' : $cptCode;
				$trans_type= strtolower($res['trans_type']);
				$mode = strtolower($res['trans_method']);
				$paid_by = strtolower($res['trans_by']);
				$delOprId= $res['trans_del_operator_id'];

				if(empty($Physician) === true && empty($credit_physician) === false){
					$doctor_id = $res['sec_prov_id'];
				}				
				
				$firstGrp = $doctor_id;
				$secGrp = $facility_id;
				if($viewBy=='facility'){
					$firstGrp = $facility_id;
					$secGrp = $doctor_id;
				}
		
				if(!$tempRecords[$encID]){
					$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['pt_id'] = $res['patient_id'];
					$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['pt_name'] = core_name_format($res['lname'], $res['fname'], $res['mname']);						
					$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['dos'] = $res['date_of_service'];
					$tempRecords[$encID]=$encID;
				}
				
				switch($trans_type){
					case 'charges':
						$delChargesArr[$firstGrp][$secGrp][$encID][$chgDetId] = $res;
						$delChargesSummArr[$firstGrp][$secGrp]+= $res['trans_amount']; // FOR SUMMARY
						$arrVoidPay[$firstGrp][$secGrp]['charges'] += $res['trans_amount']; 
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['charges'] += $res['trans_amount']; 
					break;
					case 'paid':
					case 'copay-paid':
					case 'deposit':
					case 'interest payment':
					case 'negative payment':
					case 'copay-negative payment':
						$paidForProc=$res['trans_amount'];
						if($trans_type=='negative payment')$paidForProc="-".$res['trans_amount'];

						switch($mode){
							case 'cash':
								$arrVoidPay[$firstGrp][$secGrp]['cash']+= $paidForProc;
								$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['cash'] += $paidForProc;
							break;
							case 'check':
								if($paid_by=='patient'){
									$arrVoidPay[$firstGrp][$secGrp]['pt_check'] +=$paidForProc;
									$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['pt_check'] += $paidForProc;
								}else{
									$arrVoidPay[$firstGrp][$secGrp]['ins_check'] += $paidForProc;
									$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['ins_check'] += $paidForProc;
								}
							break;
							case 'credit card':
								$arrVoidPay[$firstGrp][$secGrp]['CC'] += $paidForProc;
								$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['CC'] += $paidForProc;
							break;
							case 'eft':
								$arrVoidPay[$firstGrp][$secGrp]['EFT'] += $paidForProc;
								$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['EFT'] += $paidForProc;
							break;
							case 'money order':
								$arrVoidPay[$firstGrp][$secGrp]['MO'] +=$paidForProc;
								$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['MO'] += $paidForProc;
							break;
							case 'veep':
								$arrVoidPay[$firstGrp][$secGrp]['VEEP'] +=$paidForProc;
								$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['VEEP'] += $paidForProc;
							break;
						}
					break;
					case 'credit':
					case 'debit':
						$crddbtamt=$res['trans_amount'];
						if($trans_type=='credit'){ 
							$crddbtamt= "-".$res['trans_amount'];							
						}else{  //debit
							$crddbtamt= $res['trans_amount'];				
						}

						$arrVoidPay[$firstGrp][$secGrp]['adjustments']+= $crddbtamt;
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['adjustments']+= $crddbtamt;
					break;
					case 'write off':
					case 'discount':
					case 'over adjustment':
						$arrVoidPay[$firstGrp][$secGrp]['adjustments']+= $res['trans_amount'];
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['adjustments']+= $res['trans_amount'];
					break;
					case 'adjustment':
					case 'returned check':
					case 'refund':
						$arrVoidPay[$firstGrp][$secGrp]['adjustments']+= "-".$res['trans_amount'];
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['adjustments']+= "-".$res['trans_amount'];
					break;
				}
				$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['opr'][$delOprId] = $delOprId;
				$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['proc_code'][$cptCode] = $cptCode;				
			}
		}

	}// END DELETED	
	

	//UNAPPLIED CI/CO AND PRE-PAYMENTS		
	if($inc_ci_co_prepay==1){
		// GET NOT APPLIED CI/CO for selected month
		$arrCICONotApplied=array();
		$tempCCTypeAmts=array();
		$schFacId=0;
	
		if(empty($sc_name)== false){
			$arr_sc_name=explode(',', $sc_name);
	
			$arrSchFacId=array();
			for($i=0; $i<sizeof($arr_sc_name); $i++){
				$id= implode(",",$sch_fac_arr[$arr_sc_name[$i]]);
				if($id>0 && $id!=''){
					$arrSchFacId[$id] = $id;
				}
			}
		$arrSchFacId=array_unique($arrSchFacId);
		$schFacId = implode(',', $arrSchFacId);
		}
	
		
		$cioJoin='';
		$cioOrderBy='ORDER BY pd.lname, pd.fname';
		if($viewBy=='physician'){
			$cioJoin=" LEFT JOIN users ON users.id = sa.sa_doctor_id";
		}
		if($viewBy=='operator' || $viewBy=='department'){
			$cioJoin=" LEFT JOIN users ON users.id = cioPay.created_by";
		}
		if($viewBy=='physician' || $viewBy=='operator' || $viewBy=='department'){
			$cioOrderBy=" ORDER BY users.lname, users.fname, pd.lname, pd.fname";
		}
		$qry="SELECT sa.sa_facility_id,";
		$qry .= " sa.sa_doctor_id,";
		$qry .= " cioPayDet.id as cioPaydetID,
		cioPay.patient_id, 
		cioPay.payment_id, 
		DATE_FORMAT(cioPay.created_on, '".get_sql_date_format()."') as 'created_on', 
		cioPay.payment_method, cioPay.cc_type,
		cioPay.created_by, cioPay.created_time, 
		cioPayDet.item_payment, 
		pd.fname, pd.mname, pd.lname 
		FROM schedule_appointments sa 
		JOIN check_in_out_payment cioPay ON cioPay.sch_id  = sa.id 
		JOIN check_in_out_payment_details cioPayDet ON cioPayDet.payment_id= cioPay.payment_id
		".$cioJoin."
		JOIN patient_data pd ON pd.id = cioPay.patient_id 
		WHERE (cioPay.created_on BETWEEN '".$Start_date."' AND '".$End_date."')";
		if($hourFrom!='' && $hourTo!=''){		
			$qry.=" AND (cioPayDet.status=0 OR (cioPayDet.status=1 AND cioPayDet.delete_date>'".$End_date."' AND cioPayDet.delete_time>'".$hourTo."'))";
		}else{
			$qry.=" AND (cioPayDet.status=0 OR (cioPayDet.status=1 AND cioPayDet.delete_date>'".$End_date."'))";
		}
		if(empty($schFacId) ===false){
			$qry.= " AND sa.sa_facility_id IN(".$schFacId.")";
		}
		if(empty($Physician) === false){
			$qry.= " AND sa.sa_doctor_id IN(".$Physician.")";
		}
		if(empty($operatorName) === false){
			$qry.= " AND cioPay.created_by IN(".$operatorName.")";
		}
		
		$qry.= $cioOrderBy;
		$rs=imw_query($qry)or die(imw_error().'_451');
		while($res=imw_fetch_array($rs)){
			
			//IF TIME SEARCHED THEN SET TIME STAMP FOR SEARCH
			if($hourFrom!='' && $hourTo!=''){
				$arr1=explode(' ', $res['created_time']);
				$ampm=$arr1[1];
				list($hour, $minute)=explode(':', $arr1[0]);
				if(trim($ampm)=='AM' && trim($hour)=='12'){//Midnight Time
					$hour='00';
				}else if(trim($ampm)=='PM' && trim($hour)<12){ //Noon Time
					$hour= $hour+12;
				}
				$createdTime=$hour.':'.$minute.':00';
			}
	
			if($hourFrom=='' && $hourTo=='' || ($hourFrom!='' && $hourTo!='' && ($createdTime>=$hourFrom && $createdTime<=$hourTo))){
				$printFile=true;
				$pid = $res['patient_id'];
				$payment_id = $res['payment_id'];
				$phyId = $res['sa_doctor_id'];
				$sch_facility = $res['sa_facility_id'];
				$oprId = $res['created_by'];
				$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
				$facility = $sch_pos_fac_arr[$sch_facility];
				if($facility=='' || $facility<=0) { $facility=0; }
				
				$grpId = $phyId;
				if($viewBy=='operator'){ $grpId = $oprId;	}
				if($viewBy=='facility'){ $grpId = $facility;	}
				
				
				#############################################################
				#query to get refund detail for current ci/co payments if any
				#############################################################
				$refundAmt=0;
				$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND ci_co_id='".$res['cioPaydetID']."' 
				AND (entered_date BETWEEN '".$Start_date."' AND '".$End_date."')")or die(imw_error().'_471');
				while($rsRef=imw_fetch_array($qryRef))
				{
					$refundAmt+=$rsRef['ref_amt'];
				}
				imw_free_result($qryRef);	
				
				$tempCIOArr[$grpId][$payment_id]['payment']+= $res['item_payment'];
				$tempCIOArr[$grpId][$payment_id]['refund']+=$refundAmt;
				$tempCIOArr[$grpId][$payment_id]['facility']= $facility;
				$tempCIOArr[$grpId][$payment_id]['physician']= $phyId;
				if($processReport=='Detail'){
					$tempCIODetail[$payment_id]['patName'] = $patName;
					$tempCIODetail[$payment_id]['paidDate'] = $res['created_on'];
					$tempCIODetail[$payment_id]['facility'] = $facility;
					$tempCIODetail[$payment_id]['physician'] = $phyId;
				}
				
				$tempPayIds[$payment_id] =$payment_id;
				$groupArr[$grpId] = $grpId;
			}
		}

		$splitted_encounters=array();
		if(sizeof($tempPayIds)>0){
			$splitted_encounters = array_chunk($tempPayIds,4000);
			$tempCIOPaid=array();
			foreach($splitted_encounters as $arr){
				$str_splitted_encs 	 = implode(',',$arr);
				$arr_acc_payment_id=array();
				$temp_acc_payment_id=array();
				
				$qry="SELECT cioPost.check_in_out_payment_id, 
							 cioPost.manually_payment, 
							 cioPost.acc_payment_id, 
							 cioPost.manually_date  
							 FROM check_in_out_payment_post cioPost 
							 WHERE cioPost.check_in_out_payment_id IN(".$str_splitted_encs.") 
							 AND cioPost.status='0'";
							 
				$rs=imw_query($qry)or die(imw_error().'_514');
				while($res=imw_fetch_array($rs)){
					$payment_id = $res['check_in_out_payment_id'];
					
					if($res['manually_payment']>0 && $res['manually_date']<=$End_date){
						$tempCIOPaid[$payment_id]+=$res['manually_payment'];
					}
	
					//FOR MANUAALY APPLIED BLOCK
					if($res['manually_payment']>0){
						$tempCIOManuallyApplied[$payment_id]+=$res['manually_payment'];
					}
					
					if($res['acc_payment_id']>0){ 
						$arr_acc_payment_id[$res['acc_payment_id']]=$res['acc_payment_id'];
						$temp_acc_payment_id[$res['acc_payment_id']] = $res['check_in_out_payment_id'];
					}
				}
				if(sizeof($arr_acc_payment_id)>0){
					$str_acc_payment_id = implode(',', $arr_acc_payment_id);
					
					$qry="SELECT patPay.payment_id, 
								patPayDet.paidForProc FROM 
								patient_chargesheet_payment_info patPay 
								LEFT JOIN patient_charges_detail_payment_info patPayDet ON patPayDet.payment_id = patPay.payment_id 
								WHERE patPay.payment_id IN(".$str_acc_payment_id.")";
					if($DateRangeFor=='dot'){
						$qry.=" AND patPay.transaction_date <='$End_date' ";
					}else{
						$qry.=" AND patPay.date_of_payment <='$End_date'";
					}
					$qry.=" AND ((patPayDet.deletePayment='0' OR (patPayDet.deletePayment='1' AND patPayDet.deleteDate > '".$End_date."')) 
							AND (patPayDet.unapply='0' OR (patPayDet.unapply='1' AND DATE_FORMAT(patPayDet.unapply_date, '%Y-%m-%d')>'".$End_date."')))";
	
					$rs=imw_query($qry)or die(imw_error().'_537');
					while($res=imw_fetch_array($rs)){
						$payment_id = $temp_acc_payment_id[$res['payment_id']];
						$tempCIOPaid[$payment_id]+=$res['paidForProc'];
					}
				}
			}
			
			if($viewBy=='facility'){
				$groupArr=array();
				$tempPosFac = array_keys($tempCIOArr);
				$strTempPosFac = implode(',', $tempPosFac);
				$qry = "Select pos_facility_id FROM pos_facilityies_tbl WHERE pos_facility_id IN(".$strTempPosFac.") ORDER BY facilityPracCode";
				$rs=imw_query($qry)or die(imw_error().'_550');
				while($posQryRes = imw_fetch_array($rs)){
					$pos_facility_id = $posQryRes['pos_facility_id'];
					$groupArr[$pos_facility_id] = $pos_facility_id;
				}	
			}
		
			//FINAL CI/CO ARRAY
			foreach($groupArr as $grpId){	
				foreach($tempCIOArr[$grpId] as $payment_id => $cioData){
					$cioPayment = $cioData['payment'];
					$refund=$cioData['refund'];
					$pay_mode = str_replace(' ', '_', $cioData['pay_mode']);
	
					if($tempCIOPaid[$payment_id]>0){
						$cioPayment = floatval($cioPayment) - floatval($tempCIOPaid[$payment_id]);
					}
					
					$secGrpId = $cioData['facility'];
					if($viewBy=='facility'){$secGrpId=$cioData['physician'];}

					
					if($cioPayment>0){
						if($processReport=='Summary'){
							$arrCICONotApplied[$grpId][$secGrpId]['amount']+=($cioPayment-$refund);
							$arrCICONotApplied[$grpId][$secGrpId]['ref_amt']+=($refund);
							
							$arrCICONotApplied[$grpId][$secGrpId]['is_ref']=($refund>=1 && $arrCICONotApplied[$grpId][$cio_pos_facility_id]['is_ref']=='')?$pay_mode:0;
						}else{
							$arrCICONotApplied[$grpId][$secGrpId][$payment_id]['pat_name']=$tempCIODetail[$payment_id]['patName'];
							$arrCICONotApplied[$grpId][$secGrpId][$payment_id]['paid_date']=$tempCIODetail[$payment_id]['paidDate'];
							$arrCICONotApplied[$grpId][$secGrpId][$payment_id]['amount']+=($cioPayment-$refund);
							
							$arrCICONotApplied[$grpId][$secGrpId][$payment_id]['ref_amt']+=$refund;
						}
					}
					
					//MANAULLY PAID ARRAY
					if($tempCIOManuallyApplied[$payment_id]>0){
						if($processReport=='Summary'){
							$arrManuallyApplied[$grpId]['cico']+= $tempCIOManuallyApplied[$payment_id];
						}else{
							$arrCICOManuallyPaid[$grpId][$payment_id]['pat_name']=$tempCIODetail[$payment_id]['patName'];
							$arrCICOManuallyPaid[$grpId][$payment_id]['paid_date']=$tempCIODetail[$payment_id]['paidDate'];
							$arrCICOManuallyPaid[$grpId][$payment_id]['applied_amt']= $tempCIOManuallyApplied[$payment_id];
						}
					}
				}
			}
		}
		unset($tempCIOManuallyApplied);
	
		
		// GET PATIENT PRE PAYMENTS
		$groupArr=array();
		$tempCCTypeAmts=array();
		$patQryRes = array();
		$qry="Select pDep.id, pDep.patient_id, pDep.paid_amount, pDep.provider_id, 
		pDep.apply_payment_date, pData.default_facility, pDep.apply_payment_type, pDep.apply_amount,
		DATE_FORMAT(pDep.entered_date, '".get_sql_date_format()."') as 'entered_date', DATE_FORMAT(pDep.paid_date, '".get_sql_date_format()."') as 'paid_date',
		pDep.entered_by, pDep.payment_mode, pDep.credit_card_co,
		pData.fname, pData.mname, pData.lname 
		FROM patient_pre_payment pDep 
		LEFT JOIN patient_data pData ON pData.id = pDep.patient_id 
		WHERE 1=1";
		if($consolidation==1){
			$qry.=" AND (pDep.del_status='0' OR (pDep.del_status='1' AND DATE_FORMAT(pDep.trans_del_date, '%Y-%m-%d')>'".$delDate."'))";
		}
		if($DateRangeFor=='dot'){
			$qry.=" AND (pDep.entered_date between '".$Start_date."' and '".$End_date."')";
			
			if($hourFrom!='' && $hourTo!=''){		
				$qry.= " AND (pDep.entered_time BETWEEN '$hourFrom' AND '$hourTo')";					
			}
		}else{
			$qry.=" AND (pDep.paid_date between '".$Start_date."' and '".$End_date."')";
		}
		if(empty($Physician) === false){
			$qry.= " AND pDep.provider_id IN(".$Physician.")";
		}
		if(empty($schFacId) ===false){
			$qry .= " AND pDep.facility_id in($schFacId)";
		}
		if(empty($operatorName) === false){
			$qry .= " AND pDep.entered_by in($operatorName)";
		}
		$qry.=" ORDER BY pData.lname, pData.fname";
		
		$patQry = imw_query($qry);
		while($row = imw_fetch_assoc($patQry)){
			$patQryRes[] = $row;
		}
		
		$arrDepIds=array();
		$tempData=array(); $arrDepIds=array(); $arrAllIds=array(); $arrAllIdsData=array();
		for($i=0; $i<sizeof($patQryRes); $i++){
			$printFile=true;
			$facility=0; $balance_amount=0; $doc_id=0;$refundAmt=0;
			
			##########################################################
			#query to get refund detail for current pre payment if any
			##########################################################
	
			$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND pmt_id = '".$patQryRes[$i]['id']."' AND (entered_date BETWEEN '".$Start_date."' AND '".$End_date."')")or die(imw_error().'_656');
			while($rsRef=imw_fetch_array($qryRef))
			{
				$refundAmt=$rsRef['ref_amt'];
			}
			
			imw_free_result($qryRef);
		
			$pid = $patQryRes[$i]['patient_id'];
			$facility=$patQryRes[$i]['default_facility'];
			$phyId=$patQryRes[$i]['provider_id'];
			$oprId=$patQryRes[$i]['entered_by'];
			$patName = $pid.'~'.$patQryRes[$i]['fname'].'~'.$patQryRes[$i]['mname'].'~'.$patQryRes[$i]['lname'];
			
			if($facility<=0 || $facility==''){ $facility=$headPosFacility; }
	
			$grpId= $oprId;
			if($viewBy=='facility'){ $grpId = $facility; }
			else if($viewBy=='operator'){ $grpId = $oprId; }
			if($grpId<=0){ $grpId=0; } 
			
			$id= $patQryRes[$i]['id'];
					
			$balance_amount=($patQryRes[$i]['paid_amount']);
			
			if($patQryRes[$i]['apply_payment_type']=='manually' && $patQryRes[$i]['apply_payment_date']<= $End_date){
				$balance_amount-=$patQryRes[$i]['apply_amount'];
			}
			
			//FOR MANUALLY APPLIED BLOCK
			if($patQryRes[$i]['apply_payment_type']=='manually'){
				$tempPrePaidManuallyApplied[$id]+= $patQryRes[$i]['apply_amount'];
			}
	
	
			if($balance_amount>0 || $patQryRes[$i]['apply_amount']>0){
				$tempData[$id]['PAT_DEPOSIT']=$patQryRes[$i]['paid_amount'];
				$tempData[$id]['PAT_DEPOSIT_REF']=$refundAmt;
				
				if($patQryRes[$i]['apply_payment_type']=='manually'){
					$tempData[$id]['APPLIED_AMT']+= $patQryRes[$i]['apply_amount'];
				}
				if($patQryRes[$i]['apply_payment_date']!='0000-00-00'){
					$arrDepIds[$id]=$id;	
				}
				
				$arrAllIds[$id]=$id;
				$arrAllIdsData[$grpId][$id]['pay_mode']=strtolower($patQryRes[$i]['payment_mode']);
				$arrAllIdsData[$grpId][$id]['pat_name']=$patName;
				if(strtolower($patQryRes[$i]['payment_mode'])=='credit card')
				$tempCCTypeAmts[$id][$ccType]=$ccType;
				
				if($DateRangeFor=='dot'){
					$arrAllIdsData[$grpId][$id]['entered_date']=$patQryRes[$i]['entered_date'];
				}else{
					$arrAllIdsData[$grpId][$id]['entered_date']=$patQryRes[$i]['paid_date'];
				}
	
				$groupArr[$grpId] = $grpId;
				$tempPreFac[$id] = $facility;
			}
		}
	
		// GET PRE PAT ENCOUNTER APPLIED AMTS
		if(count($arrDepIds)>0){
			$strDepIds=implode(',', $arrDepIds);
			$preAppQry="Select payChgDet.patient_pre_payment_id, payChgDet.paidForProc FROM patient_chargesheet_payment_info payChg  
			JOIN patient_charges_detail_payment_info payChgDet ON payChgDet.payment_id = payChg.payment_id
			WHERE payChgDet.patient_pre_payment_id IN($strDepIds)";
			if($DateRangeFor=='dot'){
				$preAppQry.=" AND (payChg.transaction_date BETWEEN '".$Start_date."' and '".$End_date."')";
			}else{
				$preAppQry.=" AND (payChg.date_of_payment BETWEEN '".$Start_date."' and '".$End_date."')";
			}
			$preAppQry.="
			AND ((payChgDet.deletePayment='0' OR (payChgDet.deletePayment='1' AND payChgDet.deleteDate>'".$End_date."'))
			AND (payChgDet.unapply='0' OR (payChgDet.unapply='1' AND DATE_FORMAT(payChgDet.unapply_date, '%Y-%m-%d')>'".$End_date."')))";
			
			$preAppRs=imw_query($preAppQry)or die(imw_error().'_715');
			while($preAppRes=imw_fetch_array($preAppRs)){
				$id = $preAppRes['patient_pre_payment_id'];
				$tempData[$id]['APPLIED_AMT']+= $preAppRes['paidForProc'];
			}
		}
		// GROUPING OF DATA
		if(sizeof($groupArr)>0){
			$groupStr = implode(',', $groupArr);
			$groupArr=array();
			$groupArr[0]=0;
			$qry="Select id FROM users WHERE id IN(".$groupStr.") ORDER by lname, fname";
			if($viewBy=='facility'){
				$qry="Select pos_facility_id as 'id' from pos_facilityies_tbl WHERE pos_facility_id IN(".$groupStr.") ORDER BY facilityPracCode";
			}
			$rs=imw_query($qry)or die(imw_error().'_731');
			while($res=imw_fetch_array($rs)){
				$groupArr[$res['id']] = $res['id'];
			}
		}
		// PRE PAYMENTS FINAL ARRAY
		$arrPrePayNotApplied=array();
		foreach($groupArr as $grpId){
			foreach($arrAllIdsData[$grpId] as $id => $grpData){
				$balance_amount= floatval($tempData[$id]['PAT_DEPOSIT']) - floatval($tempData[$id]['APPLIED_AMT']);
	
				if($balance_amount>0){
					$facility = $tempPreFac[$id];
					
					if($processReport=='Summary'){
						$arrPrePayNotApplied[$grpId][$facility]['amount']+=(floatval($balance_amount)-floatval($tempData[$id]['PAT_DEPOSIT_REF']));
						$arrPrePayNotApplied[$grpId][$facility]['ref_amt']+=$tempData[$id]['PAT_DEPOSIT_REF'];
					}else{
						$arrPrePayNotApplied[$grpId][$facility][$id]['pat_name']=$grpData['pat_name'];
						$arrPrePayNotApplied[$grpId][$facility][$id]['entered_date']=$grpData['entered_date'];
						$arrPrePayNotApplied[$grpId][$facility][$id]['amount']+=(floatval($balance_amount)-floatval($tempData[$id]['PAT_DEPOSIT_REF']));
						$arrPrePayNotApplied[$grpId][$facility][$id]['ref_amt']=$tempData[$id]['PAT_DEPOSIT_REF'];
					}
				}
				
				//MANUALLY APPLIED AMOUNT ARRAY
				if($tempPrePaidManuallyApplied[$id]>0){
					if($processReport=='Summary'){
						$arrManuallyApplied[$grpId]['pre_payment']+= $tempPrePaidManuallyApplied[$id];
					}else{
						$arrPrePayManuallyApplied[$grpId][$id]['pat_name']=$grpData['pat_name'];
						$arrPrePayManuallyApplied[$grpId][$id]['entered_date']=$grpData['entered_date'];
						$arrPrePayManuallyApplied[$grpId][$id]['applied_amt'] = $tempPrePaidManuallyApplied[$id];
					}
				}
			}	
		}
		unset($tempPreFac);
		unset($tempPrePaidManuallyApplied);
		ksort($arrPrePayCCTypeAmts);
		//END NOT APPLIED AMOUNTS		
	}
	
}


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

// Collecting Data for Appointment Detail
$apptDetailsArrFull = $CLSReports->getPatientAppointments('', $Start_date, $End_date, $hourFrom, $hourTo, $viewBy, $Physician, $sc_name, $grp_id, $operatorName, $insuranceName);
$apptDetailsArr = $apptDetailsArrFull['detail'];
$apptSummaryArr = $apptDetailsArrFull['summary'];





if($processReport == "Summary"){
	//-- GET SUMMARY REPORT  ---------
	require_once(dirname(__FILE__).'/financial_analytic_summary.php');
}
else{
	//-- GET DETAIL REPORT  ---------
	require_once(dirname(__FILE__).'/financial_analytic_detail.php');
}

if($printFile == true and $csv_file_data != ''){
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$csv_file_data;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$pdf_page_content;

	$file_location = write_html($strHTML);
}else{
	$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
}


echo $csv_file_data;	
?>
