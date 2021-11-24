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
	$cpt_code_id= (sizeof($cpt_code_id)>0) ? implode(',',$cpt_code_id) : '';
	$adjustmentId= (sizeof($adjustmentId)>0) ? implode(',',$adjustmentId) : '';
	$insuranceGrp= (sizeof($insuranceGrp)>0) ? implode(',',$insuranceGrp) : '';
	$insuranceName= (sizeof($arrInsurance)>0) ? implode(',',$arrInsurance) : '';
	$wrt_code= (sizeof($wrt_code)>0) ? implode(',',$wrt_code) : '';
	$modifiers= (sizeof($modifiers)>0) ? implode(',',$modifiers) : '';
	$operatorName= (sizeof($operator_id)>0) ? implode(',',$operator_id) : '';
	$dx_code10= (sizeof($icd10_codes)>0) ? "'".implode("','", $icd10_codes)."'"  : '';
	$ins_type= (sizeof($ins_types)>0) ? implode(',',$ins_types) : '';
	$cpt_cat_2= (sizeof($cpt_cat_2)>0) ? implode(',',$cpt_cat_2) : '';
	$billing_by=='';
	if($billing_location=='1')$billing_by='billing_location';elseif($pay_location=='1')$billing_by='pay_location';
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
	/*
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
	}*/

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
	$arrSchFacilites=array();
	$arrSchFacilites[0]='No Pay Location';
	$rs=imw_query("Select id, name, fac_prac_code FROM facility");	
	while($res=imw_fetch_assoc($rs)){
		$arrPosFacOfSchFac[$res['id']] = $res['fac_prac_code'];
		$arrSchFacilites[$res['id']] = $res['name'];
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

	if($registered_fac=='1'){
		//IF DEMOGRAPHICS HAS NOT DEFAULT FACILITY SET THEN FETCH FACILITY ID FROM LAST APPOINTMENT
		$qry="Select sa_patient_id, sa_facility_id from schedule_appointments
		WHERE sa_patient_app_status_id not in (201, 18, 203, 19, 20) and sa_app_start_date <= now()
		ORDER BY sa_app_start_date ASC, sa_app_starttime ASC"; 
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$arrHomeFacOfPatients[$res['sa_patient_id']] = $arrPosFacOfSchFac[$res['sa_facility_id']];
		}unset($rs);
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


	$mainResArr = array();
	$search = 'DOP';
	if($DateRangeFor=='transaction_date'){ $search='DOT'; }
	if($DateRangeFor == 'date_of_service'){
		$search = 'DOS';
		require_once(dirname(__FILE__).'/productvity_physician_charges.php');
	}else{
		require_once(dirname(__FILE__).'/productvity_physician_payments.php');
	}



	// *READ DETAIL*
	//GETTING CHARGES FOR ENCOUNERS WHICH HAVE NOT POSTED. IT HAS BELOW TWO TYPES OF ENCOUNTERS.
	// -ENCOUNTERS WHICH HAVE YET NOT POSTED AND NO ANY ADJUSTMENT AND PAYMENT DONE.
	// -ENCOUNTERS WHICH HAVE YET NOT POSTED BUT PAYMENT OR ADJUSEMENT HAS DONE DONE FOR THAT.
	// -QUERY IS DOS BASED
	$chargesForNotPosted=$unitsForNotPosted='';
	if($DateRangeFor=='transaction_date'){		
		$qry="Select main.patient_id, main.encounter_id, (main.charges * main.units) as totalAmt, main.units,
		main.facility_id, main.billing_facility_id, main.primary_provider_id_for_reports  
		FROM report_enc_detail main JOIN patient_data pd ON pd.id=main.patient_id
		JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = main.proc_code_id	
		WHERE main.first_posted_date='0000-00-00' AND main.del_status='0' AND (main.charges * main.units) = main.proc_balance 
		AND (main.date_of_service BETWEEN '$Start_date' and '$End_date')";
		if($pay_location=='1'){
			$qry.= " and main.billing_facility_id IN ($sc_name)";	
		}else{
			if(empty($sc_name) == false){
				if($registered_fac=='1'){
					$qry.= " and pd.default_facility IN ($sc_name)";
				}else{
					$qry.= " and main.facility_id IN ($sc_name)";	
				}
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
		if(trim($cpt_cat_2) != ''){
			$qry.= " AND cpt_fee_tbl.cpt_category2 IN ($cpt_cat_2)";							
		}
		if($pureSelfPay==true)$qry.= " and main.proc_selfpay=1";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$chargesForNotPosted+=$res['totalAmt'];
			$unitsForNotPosted+=$res['units'];
			
		}
	}	
	//----------------------------------------------


	//FOR DELETED CHARGES AND PAYMENTS
	if($DateRangeFor=='transaction_date' || ($DateRangeFor=='date_of_payment' && sizeof($arrAllEncIds)>0)){
		
		$arrFilteredEncIds=array();
		$strFilteredEncIds='';
		$strAllEncIds= implode(',', $arrAllEncIds);
		
		//GETTING ALL ENCOUNTERS/TRANSACTIONS DELETED AFTER SELECTED DATE RANGE FOR ALL FETCHED ENCOUNTERS
/*		if($DateRangeFor=='date_of_payment'){
			
			if(sizeof($arrAllEncIds)>0){
				$strAllEncIds= implode(',', $arrAllEncIds);
				$qry="Select encounter_id FROM report_enc_trans WHERE encounter_id IN(".$strAllEncIds.") 
				AND trans_del_date>'$End_date'";
				$rs=imw_query($qry);
				while($res=imw_fetch_array($rs)){
					$arrFilteredEncIds[$res['encounter_id']]=$res['encounter_id'];
				}
			}
			if(sizeof($arrFilteredEncIds)>0){ $strFilteredEncIds= implode(',', $arrFilteredEncIds); }
		}*/
		
		$whereQry='';

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
		
		// GET DELETED CHARGES AMOUNTS
		$chrgqry="Select main.charge_list_detail_id, main.encounter_id, main.patient_id, main.facility_id, main.billing_facility_id, main.total_charges, pd.default_facility,
		main.primary_provider_id_for_reports  as 'primaryProviderId', DATE_FORMAT(main.date_of_service, '".$dateFormat."') as 'date_of_service',
		main.del_operator_id, main.proc_code_id, pd.fname, pd.mname, pd.lname  
		FROM report_enc_detail main 
		JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = main.proc_code_id
		LEFT JOIN patient_data pd ON pd.id = main.patient_id
		WHERE main.del_operator_id>0";
		if($DateRangeFor=='transaction_date'){
			//IF "WITHOUT DELETED AMOUNTS" CHECKED THEN ONLY DISPLAY DELETED AMOUNT	THAT HAVE DOS BEFORE SEARCH CRITERIA
			if($no_del_amt=='1'){
				$chrgqry.=" AND (DATE_FORMAT(main.trans_del_date, '%Y-%m-%d') BETWEEN '$Start_date' AND '$End_date') AND main.first_posted_date!='0000-00-00' AND main.first_posted_date<'$Start_date'"; 	
			}else{
				$chrgqry.=" AND (DATE_FORMAT(main.trans_del_date, '%Y-%m-%d') BETWEEN '$Start_date' AND '$End_date')"; 	
			}
		}else{
			$chrgqry.=" AND (main.encounter_id IN(".$strAllEncIds.") AND main.trans_del_date>'$End_date')";			
		}
		if(empty($sc_name) == false){
			if($pay_location=='1'){
				$chrgqry .= " and main.billing_facility_id IN ($sc_name)";
			}else{
				if($registered_fac=='1'){
					$chrgqry .= " and pd.default_facility IN ($sc_name)";
				}else{
					$chrgqry .= " and main.facility_id IN ($sc_name)";
				}
			}
		}
		if(empty($operatorName)==false){
			$chrgqry.= " AND main.del_operator_id in($operatorName)";
		}
		if(trim($cpt_cat_2) != ''){
			$chrgqry.= " AND cpt_fee_tbl.cpt_category2 IN ($cpt_cat_2)";							
		}
		$chrgqry.=$whereQry .' Order By ';
		$chrgqry.= ($sort_by == 'patient') ? "pd.lname,pd.fname,main.date_of_service" : "main.date_of_service, pd.lname, pd.fname";
		
		//echo $chrgqry;
		
		$res=imw_query($chrgqry);
		while($result=imw_fetch_assoc($res)){
			$chgDetId = $result['charge_list_detail_id'];
			$encID = $result['encounter_id'];
			$delOprId= $result['del_operator_id'];
			$cptCode = $arrAllCPTCodes[$result['proc_code_id']];
			$cptCode = (strlen($cptCode)>8) ? substr($cptCode,0, 8).'...' : $cptCode;
			$cptDesc = $arrCptDesc[$result['proc_code_id']];
			
			$primaryProviderId=$result['primaryProviderId'];
			if($registered_fac=='1'){
				if($result['default_facility']>0){
					$facility_id=$result['default_facility'];
				}else{
					$facility_id=$arrHomeFacOfPatients[$result['patient_id']];
				}				
			}else{
				if($pay_location=='1'){
					$facility_id=$result['billing_facility_id'];
				}else{
					$facility_id=$result['facility_id'];
				}
			}
			
			$firstGrp = $primaryProviderId;
			$secGrp = $facility_id;
			if($viewBy=='facility'){
				$firstGrp = $facility_id;
				$secGrp = $primaryProviderId;
			}			
			
			$delChargesArr[$firstGrp][$secGrp][$encID][$chgDetId] = $result;
			$delChargesSummArr[$firstGrp][$secGrp]+= $result['total_charges']; // FOR SUMMARY
			$arrVoidPay[$firstGrp][$secGrp]['charges'] += $result['total_charges']; 
			$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['charges'] += $result['total_charges'];
			$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['pt_id'] = $result['patient_id'];
			$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['dos'] = $result['date_of_service'];
			$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['pt_name'] = core_name_format($result['lname'], $result['fname'], $result['mname']);						

			$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['opr'][$delOprId] = $delOprId;
			$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['proc_code'][$cptCode] = $cptCode;				
			$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['proc_desc'][$cptDesc] = $cptDesc;				
		}
		
		//PAYMENTS & ADJUSTMENTS
		$qry="Select trans.patient_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, trans.trans_method, 
		trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_dot, trans.trans_qry_type, trans.facility_id as 'pay_location',
		DATE_FORMAT(trans.trans_dot,'".$dateFormat."') as trans_dot, trans.trans_dot, trans.trans_del_operator_id, trans.trans_del_date,
		DATE_FORMAT(main.date_of_service, '".$dateFormat."') as 'date_of_service', main.primary_provider_id_for_reports  as 'primaryProviderId',
		main.proc_code_id, main.facility_id, main.billing_facility_id, main.first_posted_date, pd.fname, pd.lname, pd.mname, pd.default_facility 
		FROM report_enc_trans trans 
		JOIN report_enc_detail main ON main.charge_list_detail_id = trans.charge_list_detail_id
		JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = main.proc_code_id	
		LEFT JOIN patient_data pd ON pd.id = trans.patient_id 
		WHERE LOWER(trans.trans_type)!='charges' AND trans_del_operator_id>0 AND trans.trans_type!='default_writeoff'";
		//IF "WITHOUT DELETED AMOUNTS" CHECKED THEN ONLY DISPLAY DELETED AMOUNT	THAT HAVE DOS BEFORE SEARCH CRITERIA
		//if($inc_del_trans=='1'){
		//	$qry.=" AND (DATE_FORMAT(patChgDet.trans_del_date, '%Y-%m-%d') BETWEEN '$Start_date' AND '$End_date') AND patChg.date_of_service<'$Start_date'"; 	
		//}else{
			//$qry.=" AND (trans.trans_del_date BETWEEN '$Start_date' AND '$End_date')"; 	
		//}
		if($DateRangeFor=='transaction_date'){
			if($no_del_amt=='1'){
				$qry.=" AND (trans.trans_del_date BETWEEN '$Start_date' AND '$End_date') AND 
				if(LOWER(trans.trans_type)='charges', main.first_posted_date<'$Start_date' AND main.first_posted_date!='0000-00-00', trans.trans_dot<'$Start_date')";
			}else{
				$qry.=" AND (trans.trans_del_date BETWEEN '$Start_date' AND '$End_date') ";
			}
		}else{
			$qry.=" AND (trans.encounter_id IN(".$strAllEncIds.") AND trans.trans_del_date>'$End_date')";			
		}
		if(empty($sc_name) == false){
			if($pay_location=='1'){
				$qry .= " and trans.facility_id IN ($sc_name)";
			}else{
				if($registered_fac=='1'){
					$qry .= " and pd.default_facility IN ($sc_name)";
				}else{
					$qry .= " and main.facility_id IN ($sc_name)";
				}
			}
		}
		if(empty($operatorName)==false){
			$qry.= " AND trans.trans_del_operator_id in($operatorName)";
		}
		if(trim($cpt_cat_2) != ''){
			$qry.= " AND cpt_fee_tbl.cpt_category2 IN ($cpt_cat_2)";							
		}
		$qry.=$whereQry .' Order By ';
		$qry.= ($sort_by == 'patient') ? "pd.lname,pd.fname,main.date_of_service" : "main.date_of_service, pd.lname, pd.fname";
		
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			
			//IF DELETED BEFORE FIRST POSTED DATE THEN IGNORE THAT
			//if($res['first_posted_date']=='0000-00-00' || $res['first_posted_date'] <= $res['trans_del_date']){
				$chgDetId=$res['charge_list_detail_id'];
							
				//IF FUTURE DATE DELETED REOCORD IS NOT EXIST IN ALREADY FETCHED CHARGE LIST IDS THEN SKIP IT.				
				if($DateRangeFor=='date_of_payment' && $res['trans_del_date']>$End_date && $res['trans_type']!='charges' && !$allTransChgDetId[$chgDetId]){
					continue;
				}
				
				$primaryProviderId=$res['primaryProviderId'];
				if($registered_fac=='1'){
					if($result['default_facility']>0){
						$facility_id=$res['default_facility'];
					}else{
						$facility_id=$arrHomeFacOfPatients[$res['patient_id']];
					}									
				}else{
					if($pay_location=='1'){
						$facility_id=$res['pay_location'];
					}else{
						$facility_id=$res['facility_id'];
					}
				}
				
				$facility_id= (empty($facility_id)==true)? '0' : $facility_id;
				$encID = $res['encounter_id'];	

				$chgDetId= $res['charge_list_detail_id'];
				$cptCode = $arrAllCPTCodes[$res['proc_code_id']];
				$cptCode = (strlen($cptCode)>8) ? substr($cptCode,0, 8).'...' : $cptCode;
				$cptDesc = $arrCptDesc[$res['proc_code_id']];
				$trans_type= strtolower($res['trans_type']);
				$mode = strtolower($res['trans_method']);
				$paid_by = strtolower($res['trans_by']);
				$delOprId= $res['trans_del_operator_id'];
				
				$firstGrp = $primaryProviderId;
				$secGrp = $facility_id;
				if($viewBy=='facility'){
					$firstGrp = $facility_id;
					$secGrp = $primaryProviderId;
				}
		
				if(!$tempRecords[$encID]){
					$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['pt_id'] = $res['patient_id'];
					$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['pt_name'] = core_name_format($res['lname'], $res['fname'], $res['mname']);						
					$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['dos'] = $res['date_of_service'];
					$tempRecords[$encID]=$encID;
				}
				
				switch($trans_type){
					case 'paid':
					case 'copay-paid':
					case 'negative payment':
					case 'copay-negative payment':
						$paidForProc=$res['trans_amount'];
						if($trans_type=='negative payment' || $trans_type=='copay-negative payment')$paidForProc="-".$res['trans_amount'];

						switch($mode){
							case 'cash':
								$arrVoidPay[$firstGrp][$secGrp]['cash']+= $paidForProc;
								$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['cash'] += $paidForProc;
							break;
							case 'check':
								if($paid_by=='patient' || $paid_by == 'res. party'){
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
				$arrVoidPay[$firstGrp][$secGrp]['detail'][$encID]['proc_desc'][$cptDesc] = $cptDesc;				
			}
		//}

	}// END DELETED	
}

//GET SELECTED
$dispRegFacility=($registered_fac=='1')? 'Yes' : 'No';
$selgroup = $CLSReports->report_display_selected($grp_id,'group',1, $allGrpCount);
if($pay_location=='1'){
	$selFac = $CLSReports->report_display_selected($sc_name,'facility_tbl',1, $sch_fac_cnt);
}else{
	$selFac = $CLSReports->report_display_selected($sc_name,'practice',1, $allFacCount);	
}
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

if($processReport == "Summary"){
	//-- GET SUMMARY REPORT  ---------
	require_once(dirname(__FILE__).'/summary_productivity.php');
}
else{
	//-- GET DETAIL REPORT  ---------
	//if($cpt_check){
	//	require_once(dirname(__FILE__).'/detail_productivity_cpt.php');
	//} else {
		require_once(dirname(__FILE__).'/detail_productivity.php');
	//}
}


// SAVE Search Criteria
if(!isset($callFrom) || $callFrom != 'scheduled'){
	if((isset($search_name) && $search_name!='' && empty($varCriteria)==false) || ($chkSaveSearch=='1' && empty($varCriteria)==false)){
		$search_name=trim($search_name);
		$qryPart='Insert into';
		$fieldPart=", report_name='".addslashes($search_name)."'";
		$qryWhere='';
		if($savedCriteria!='' && $chkSaveSearch=='1'){
			$qryPart='Update'; 
			$fieldPart='';
			$qryWhere=" WHERE id='".$savedCriteria."'";
		}
		
		$qry="Select id FROM reports_searches WHERE report_name='".$search_name."' AND report='productivity_proficiency_criteria'";
		$rs=imw_query($qry);
		if(imw_num_rows($rs)<=0 || (imw_num_rows($rs)>0 && $qryPart=='Update')){
			$qry="$qryPart reports_searches SET uid='".$_SESSION['authId']."', report='productivity_proficiency_criteria',
			search_data='".addslashes($varCriteria)."', saved_date='".date('Y-m-d H:i:s')."' ".$fieldPart.$qryWhere;
			imw_query($qry);
		}

	}
	/*if($varCriteria!=''){
		$srchQry="Select * from reports_searches WHERE uid='".$_SESSION['authId']."' AND report='productivity_proficiency' 
		AND search_data='".$varCriteria."'";
		$srchRs=mysql_query($srchQry);
		if(mysql_num_rows($srchRs)<=0){
			mysql_query("Insert into reports_searches SET uid='".$_SESSION['authId']."', report='productivity_proficiency',
			report_name='', search_data='".$varCriteria."', saved_date='".date('Y-m-d H:i:s')."'");
		}
	}*/
}
//---------------------

$HTMLCreated=0;
if($printFile == true and $csv_file_data != ''){
	$HTMLCreated=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$csv_file_data;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$pdf_page_content;
	//$filebasepath =data_path().'UserId_'.$_SESSION['authId'].'/tmp/reports/';
	
	/*if( !is_dir($filebasepath) ){
		mkdir($filebasepath, 0755, true);
		chown($filebasepath, 'apache');
	}*/

	$now   = time();
	//DELETING 7 DAYS OLD FILES OF THIS REPORT
	foreach(glob(data_path()."UserId_".$_SESSION['authId']."/tmp/practice_analytic_*") as $html_file_names){
		if($html_file_names){
			if($now - filemtime($html_file_names) >=  60 * 60 * 24 * 7) { // 7 days
				unlink($html_file_names);
			}
		}
	}

	$pdfName = '/practice_analytic_'.$now.'.html';
	$file_location = write_html($strHTML,$pdfName);
	
}else{
	if($callFrom!='scheduled'){
		$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}

if($callFrom!='scheduled'){
	if($output_option=='view' || $output_option=='output_csv'){
		if($output_option=='output_csv' && $processReport == "Detail"){
			echo '<div class="text-center alert alert-info">Please click on link near application bottom to download CSV file.</div>';
		}else{
			echo $csv_file_data;	
		}
	}
}
?>
