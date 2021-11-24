<?php
ini_set("memory_limit","3072M");
$printFile = true;
$arrGroupSel=array();
$arrFacilitySel=array();
$arrDoctorSel=array();
$arrAllGroups = array();
$curDate = date(phpDateFormat().' h:i A');
$now=date_create(date('Y-m-d H:i:s'));


if( $_POST['form_submitted'] ){
	$printFile = false;
	$checkInDataArr=array();
	
	//--- CHANGE DATE FORMAT ----
	$date_format_SQL = get_sql_date_format();
	if($Start_date!='' && $End_date!=''){
		$startDate = getDateFormatDB($Start_date);
		$endDate = getDateFormatDB($End_date);
	}
	if($due_start_date!='' && $due_end_date!=''){
		$due_start_date = getDateFormatDB($due_start_date);
		$due_end_date = getDateFormatDB($due_end_date);
	}
	
	$dd=explode('-', $startDate);
	$prevDate = date('Y-m-d', mktime(0,0,0, $dd[1], $dd[2]-1,$dd[0]));

	$reptByForFun = $DateRangeFor;
	$checkDel= ($reptByForFun=='dot') ?  'yes' : '';
	
	// GET DEFAULT FACILITY
	$rs = imw_fetch_assoc(imw_query("select fac_prac_code from facility where facility_type  = '1' LIMIT 1"));
	$headPosFacility=$rs['fac_prac_code'];
	
	// GET SELECTED SCHEDULER FACILITIES
	$sch_fac_id_str='';
	$fac_query = "Select id,name,fac_prac_code from facility";
	$fac_query_rs = imw_query($fac_query);
	$sch_fac_id_arr = array();
	while($fac_query_res = imw_fetch_array($fac_query_rs)){	
		$fac_id = $fac_query_res['id'];
		$pos_fac_id = $fac_query_res['fac_prac_code'];
		$sch_pos_fac_arr[$fac_id] = $pos_fac_id;
		$sch_fac_arr[$pos_fac_id] = $fac_id;
	}

	// -- GET ALL POS-FACILITIES
	$fac_name_arr=array();
	$fac_name_arr[0] = 'No Facility';
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
		$fac_name_arr[$id] = $name.' - '.$pos_prac_code;
	}						
	
	//GET ALL USERS
	$rs=imw_query("Select id, fname, mname, lname FROM users");	
	$providerNameArr[0] = 'No Provider';
	while($res=imw_fetch_array($rs)){
		$id  = $res['id'];
		$pro_name_arr = array();
		$pro_name_arr["LAST_NAME"] = $res['lname'];
		$pro_name_arr["FIRST_NAME"] = $res['fname'];
		$pro_name_arr["MIDDLE_NAME"] = $res['mname'];
		$pro_name = changeNameFormat($pro_name_arr);
		$providerNameArr[$id] = $pro_name;
	}
	
	//GET ALL Departments
	$rs=imw_query("Select * FROM department_tbl");	
	$deptNameArr[0] = 'No Department';
	while($res=imw_fetch_array($rs)){
		$id  = $res['DepartmentId'];
		$dept_name = $res['DepartmentDesc'] . ($res['DepartmentCode'] ? ' - '.$res['DepartmentCode'] : '');
		$deptNameArr[$id] = $dept_name;
	}					
	
	//--- GET GROUP NAME ---
	$group_query = "select gro_id, name from groups_new";
	$groupQryRes = get_array_records_query($group_query);		
	$arrAllGroups[0] = 'No Group';
	for($i=0; $i<sizeof($groupQryRes); $i++){	
		$group_name = $groupQryRes[$i]['name'];
		$arrAllGroups[$groupQryRes[$i]['gro_id']]=$group_name;
	}
	
	//--- GET Appoinment Status---
	$status_query = "select id, status_name, alias from schedule_status";
	$statusQryRes = get_array_records_query($status_query);		
	$arrApptStatus[0] = 'Created/Restored';
	for($i=0; $i<sizeof($statusQryRes); $i++){	
		$status_name = $statusQryRes[$i]['alias'];
		$arrApptStatus[$statusQryRes[$i]['id']]=$status_name;
	}

	//--- GETTING SCHEDULER PROCEDURES
	$rs = imw_query("SELECT id, proc, acronym FROM slot_procedures WHERE proc != ''");
	$arrAllProcedures = array();
	while ($res = imw_fetch_assoc($rs)) {
		$arrAllProcedures[$res['id']] = $res['proc'];
	}
	
	// GET DEFAULT FACILITY
	$rs = imw_fetch_assoc(imw_query("select fac_prac_code from facility where facility_type  = '1' LIMIT 1"));
	$headPosFacility=$rs['fac_prac_code'];

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_name)<=0 && isPosFacGroupEnabled()){
		$facility_name = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_name)<=0){
			$facility_name[0]='NULL';
		}
	}
		
	$grp_id = implode(',',$groups);
	$strFacilities = implode(',',$facility_name);
	$strProviders = implode(',',$phyId);
	$department = implode(',',$department);
	$operator_id = implode(',',$operator_id);
	$cpt_code_id = implode(',',$cpt);
	$task_assign_operator_id = implode(',',$task_assign_operator_id);
	$groupBy = $grpby_block;
	
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

		$collectionIds = get_account_status_id_collections();
		if(empty($collectionIds)==false){
			$arrCollectionIds= explode(',', $collectionIds);
			$arrCollectionIds = array_combine($arrCollectionIds, $arrCollectionIds);
		}
		$searchMod=$DateRangeFor;
		
		// COMBINE INS AND INS GROUPS
		if(empty($ins_carriers)==false){ $tempInsArr[] = implode(",",$ins_carriers); }
		if(empty($insuranceGrp)==false){ $tempInsArr[] = implode(",",$insuranceGrp); 	}
		$tempSelIns = implode(',', $tempInsArr);
		$tempInsArr = array();
		if(empty($tempSelIns)==false){
		$tempInsArr = explode(',', $tempSelIns);
		}
		$tempInsArr = array_unique($tempInsArr);
		$insCompanies  = implode(',', $tempInsArr);
		$arrInsurance=array();
		if(sizeof($tempInsArr)>0){
			$arrInsurance=array_combine($tempInsArr,$tempInsArr);
		}
		unset($tempInsArr);

		$insuranceNameArr=explode(',',$insCompanies);
		if($insuranceNameArr[0]<=0){ $insuranceNameArr=array(); }	// REMOVE BLANK ELEMENT
		
		// MAKE Search Criteria Vars
		$varCriteria=$grp_id.'~'.$strProviders.'~'.$strFacilities;
		$varCriteria.='~'.$Start_date.'~'.$End_date.'~'.$DateRangeFor.'~'.$summary_detail;
		$varCriteria.='~'.$insCompanies.'~'.$ins_group;
		$varCriteria.='~'.$aging_start.'~'.$aging_to.'~'.$BalanceAmount.'~'.$task_assign_operator_id;
		$varCriteria.='~'.$due_start_date.'~'.$due_end_date.'~'.$accNotes.'~'.$output_option.'~'.$task_status;
		//---------------------
	
		$qry ="Select elem_arCycle FROM copay_policies WHERE policies_id='1'";
		$rs=imw_query($qry);
		$res=imw_fetch_array($rs);
		$aggingCycle = $res['elem_arCycle'];
		//---------------------//
	
		$All_due = false;
		if($aging_to == 'All'){
			$aging_to = 180;
			$All_due = true;
		}
	
	if($startDate!="" && $endDate!=""){
		$whr_dos_chk=" and patient_charge_list.date_of_service  between '$startDate' and '$endDate'";
	}else if($startDate!=""){
		$whr_dos_chk=" and patient_charge_list.date_of_service >=$startDate";
	}else if($endDate!=""){
		$whr_dos_chk=" and patient_charge_list.date_of_service <= $endDate";
	}
	
	$dueJoin=$whr_due_date_chk = "";
	if(empty($task_assign_operator_id) == false || ($due_start_date!="" && $due_end_date!="") || empty($task_status) == false){
		$dueJoin = " JOIN paymentscomment ON paymentscomment.encounter_id = patient_charge_list.encounter_id"; 
		if($task_assign_operator_id){
			$whr_due_date_chk .= " AND paymentscomment.task_assign_for IN ($task_assign_operator_id)";
		}
		if($due_start_date!="" && $due_end_date!=""){
			$whr_due_date_chk .= " AND (paymentscomment.reminder_date  between '$due_start_date' AND '$due_end_date')";
		}
		if($task_status!=""){
			$whr_due_date_chk .= " AND paymentscomment.task_done = $task_status ";
		}
	}
	// CPT CODE ARRAY
	$arrAllCPTCodes=array();
	$rs=imw_query("Select cpt_fee_id, cpt_prac_code FROM cpt_fee_tbl");
	while($res=imw_fetch_array($rs)){
		$arrAllCPTCodes[$res['cpt_fee_id']] = $res['cpt_prac_code'];
	}
		
		//--- GET DATA FROM PATIENT CHARGE LIST ---
	$qry = "select patient_charge_list_details.pat_due,patient_charge_list.encounter_id,
	patient_charge_list_details.from_pat_due_date, patient_charge_list.date_of_service,
	patient_charge_list.date_of_service as last_dos_diff,	
	patient_data.pat_account_status 
	from patient_charge_list USE INDEX (patchargelist_dos)  
	$dueJoin
	LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id 
	JOIN patient_data ON patient_data.id = patient_charge_list.patient_id
	where patient_charge_list_details.pat_due > 0  AND patient_charge_list.totalBalance>0 
	AND patient_charge_list_details.del_status='0' 
	$whr_dos_chk $whr_due_date_chk";
	//if(empty($collectionIds)===false){
	//	$qry .= " and patient_data.pat_account_status NOT IN ($collectionIds)";
	//}
	if(empty($grp_id) == false){
		$qry .= " and patient_charge_list.gro_id in ($grp_id)";
	}
	if(trim($insCompanies) != ''){			
		$qry .= " and (patient_charge_list.primaryInsuranceCoId in($insCompanies) 
			or patient_charge_list.secondaryInsuranceCoId in($insCompanies) 
			or patient_charge_list.tertiaryInsuranceCoId in($insCompanies))";
	}
	if(empty($strFacilities) == false){
		$qry .= " AND patient_charge_list.facility_id IN ($strFacilities)";	
	}
	if(empty($strProviders) == false){
		$qry .= " AND patient_charge_list.primary_provider_id_for_reports in($strProviders)";
	}
	if(trim($cpt_code_id) != ''){
		$qry.= " AND patient_charge_list_details.procCode in ($cpt_code_id)";
	}	
	if($DateRangeFor=='dos'){
		$qry .=" and DATEDIFF(NOW(),patient_charge_list.date_of_service)>=$aging_start";

		if($All_due == false){
			$qry .= " and DATEDIFF(NOW(),patient_charge_list.date_of_service)<=$aging_to";
		}
	}else{
		$qry .=" and IF(from_pat_due_date>0,(DATEDIFF(NOW(), from_pat_due_date)>=$aging_start), (DATEDIFF(NOW(),patient_charge_list.date_of_service)>=$aging_start))";

		if($All_due == false){
			$qry .= " and IF(from_pat_due_date>0,(DATEDIFF(NOW(), from_pat_due_date)<=$aging_to), (DATEDIFF(NOW(),patient_charge_list.date_of_service)<=$aging_to))";
		}
	}
	$patQryRes = array();
	$query = imw_query($qry);
	while($res=imw_fetch_assoc($query)){
		$patQryRes[] = $res;
	}
	$patBalArr = array();
	$totalPatBalArr = array();
	$totalCollectionBalance=0;
	for($i=0;$i<count($patQryRes);$i++){
		$arrEncIds[$patQryRes[$i]['encounter_id']]=$patQryRes[$i]['encounter_id'];
		
		//ALL COLLECTION STATUS TOTAL
		if(sizeof($arrCollectionIds)>0 && $arrCollectionIds[$patQryRes[$i]['pat_account_status']]){
			$totalCollectionBalance+= $patQryRes[$i]['pat_due'];
			
		}else{
			$interval = date_diff(date_create($patQryRes[$i]["date_of_service"]), $now);
			$agingCompareDOS=$interval->days;
			
			if($DateRangeFor=='dot'){
				if($patQryRes[$i]["from_pat_due_date"]!='0000-00-00'){
					$interval = date_diff(date_create($patQryRes[$i]["from_pat_due_date"]), $now);
					$agingCompare=$interval->days;
				}else{
					$agingCompare = $agingCompareDOS; 
				}
			}else{
				$agingCompare=$agingCompareDOS;
			}
	
			for($a=$aging_start;$a<=$aging_to;$a++){
				$start = $a;
				$a = $a > 0 ? $a - 1 : $a;
				$end = ($a) + $aggingCycle;
	
				if($agingCompare >= $start && $agingCompare <= $end){
					$patBalArr[$a][] = $patQryRes[$i]["pat_due"];
					$totalPatBalArr[] = $patQryRes[$i]["pat_due"];
				}
				$a += $aggingCycle;
			}
	
			//--- PATIENT DUE WITHOUT INSURANCE AS A/R AGGING BY 181+ -------
			if($All_due == true){
				if($agingCompare >= 181){
					$patBalArr[181][] = $patQryRes[$i]["pat_due"];
					$totalPatBalArr[] = $patQryRes[$i]["pat_due"];
				}
			}
		}
	}
	$totalPatBalAmt = array_sum($totalPatBalArr);
	$totalPatBalAmt = $CLSReports->numberFormat($totalPatBalAmt,2);
	
	$BalanceAmount = $BalanceAmount == '' ? 0 : $BalanceAmount;	
	
	//--- GET MAIN DATA AS SELECTED GROUP ID AND AS A/R AGGING --------	
	$qryPriPart = $qrySecPart = $qryTerPart = $qryDOSPart= $qryAggings='';
	
	if($DateRangeFor=='dot'){
		$qryAggings='
		DATEDIFF(NOW(),patient_charge_list.date_of_service) as last_pri_dop_diff,		
		DATEDIFF(NOW(),patient_charge_list_details.from_sec_due_date) as last_sec_dop_diff,
		DATEDIFF(NOW(),patient_charge_list_details.from_ter_due_date) as last_ter_dop_diff,
		DATEDIFF(NOW(),patient_charge_list_details.from_pat_due_date) as last_pat_dop_diff,';
	}

	$qryPriPart= " (DATEDIFF(NOW(),patient_charge_list.date_of_service) >= $aging_start)";
	$qrySecPart= " (DATEDIFF(NOW(),from_sec_due_date) >= $aging_start)";
	$qryTerPart= " (DATEDIFF(NOW(),from_ter_due_date) >= $aging_start)";
	if($All_due == false){
		$qryPriPart.= " and (DATEDIFF(NOW(),patient_charge_list.date_of_service) <= '$aging_to')";
		$qrySecPart.= " and (DATEDIFF(NOW(),from_sec_due_date) <= '$aging_to')";
		$qryTerPart.= " and (DATEDIFF(NOW(),from_ter_due_date) <= '$aging_to')";
	}
	
	$query = "select patient_charge_list.primaryInsuranceCoId ,patient_charge_list.patient_id,
	patient_charge_list.secondaryInsuranceCoId , patient_charge_list.charge_list_id,
	date_format(patient_charge_list.date_of_service,'%m-%d-%y') as date_of_service,
	date_format(patient_charge_list.postedDate,'%m-%d-%y') as postedDate,
	date_format(patient_data.DOB,'%m-%d-%y') as patient_dob,
	patient_charge_list.encounter_id, patient_charge_list.totalBalance, 
	patient_data.lname,patient_data.fname,patient_data.mname,
	patient_data.DOB, patient_data.pat_account_status,
	patient_charge_list.tertiaryInsuranceCoId ,patient_charge_list.primary_paid,
	patient_charge_list.secondary_paid,patient_charge_list.tertiary_paid,
	patient_charge_list.patientDue,
	patient_charge_list.encounter_id, patient_charge_list.superbillFormId, 
	patient_charge_list.case_type_id, patient_charge_list.gro_id, patient_charge_list.facility_id,
	DATEDIFF(NOW(),patient_charge_list.date_of_service) as dos_date_diff,	
	".$qryAggings."
	patient_charge_list_details.charge_list_detail_id,
	patient_charge_list_details.pri_due as pri_due,
	patient_charge_list_details.sec_due as sec_due,
	patient_charge_list_details.tri_due as tri_due,
	patient_charge_list_details.pat_due as pat_due, 
	patient_charge_list_details.procCode 
	FROM patient_charge_list USE INDEX (patchargelist_dos) 
	$dueJoin
	LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id 
	JOIN patient_data on patient_data.id = patient_charge_list.patient_id 
	where patient_charge_list_details.del_status='0' 
	and (pri_due + sec_due + tri_due) > $BalanceAmount AND patient_charge_list.totalBalance>0 
	$whr_dos_chk $whr_due_date_chk";		
	//if(empty($collectionIds)===false){
	//	$query .= " and patient_data.pat_account_status NOT IN ($collectionIds)";
	//}
	if($grp_id>0){
		$query .= " and patient_charge_list.gro_id IN ($grp_id)";
	}	
	if(empty($strFacilities) == false){
		$query .= " AND patient_charge_list.facility_id IN ($strFacilities)";	
	}
	if(empty($strProviders) == false){
		$query .= " AND patient_charge_list.primary_provider_id_for_reports in($strProviders)";
	}
	
	if(empty($strProviders) == false){
		$query .= " AND patient_charge_list.primary_provider_id_for_reports in($strProviders)";
	}
	
	if(empty($strProviders) == false){
		$query .= " AND patient_charge_list.primary_provider_id_for_reports in($strProviders)";
	}
	if(trim($cpt_code_id) != ''){
		$query.= " AND patient_charge_list_details.procCode in ($cpt_code_id)";
	}		
	
	if($DateRangeFor=='dos'){
		if(trim($insCompanies) == ''){
			if(trim($ins_group) == ''){			
				$query .= "AND ((pri_due + sec_due + tri_due)>0 AND $qryPriPart)";
			}
			else if($ins_group == 'primary'){
				$query .= " AND (patient_charge_list_details.pri_due>0 AND $qryPriPart)";
			}
			else if($ins_group == 'secondary'){
				$query .= " AND (patient_charge_list_details.sec_due>0 AND $qryPriPart)";
			}
		}
		else if(trim($insCompanies) != ''){
			if(trim($ins_group) == ''){			
				$query .= " AND ((patient_charge_list.primaryInsuranceCoId in($insCompanies) 
						AND patient_charge_list_details.pri_due>0 AND $qryPriPart) 
					OR (patient_charge_list.secondaryInsuranceCoId in($insCompanies) 
						AND patient_charge_list_details.sec_due>0 AND $qryPriPart) 
					OR (patient_charge_list.tertiaryInsuranceCoId in($insCompanies) 
						AND patient_charge_list_details.tri_due>0 AND $qryPriPart))";
			}
			else if($ins_group == 'primary'){
				$query .= " AND (patient_charge_list.primaryInsuranceCoId in($insCompanies) 
					AND patient_charge_list_details.pri_due>0 AND $qryPriPart)";
			}
			else if($ins_group == 'secondary'){
				$query .= " AND (patient_charge_list.secondaryInsuranceCoId in($insCompanies) 
				AND patient_charge_list_details.sec_due>0  AND $qryPriPart)";
			}
		}
	}else{ //DOT
		if(trim($insCompanies) == ''){
			if(trim($ins_group) == ''){			
				$query .= " AND ((pri_due>0 AND $qryPriPart)
					OR (sec_due>0 AND $qrySecPart) 
					OR (tri_due>0 AND $qryTerPart))";
			}
			else if($ins_group == 'primary'){
				$query .= " AND (patient_charge_list_details.pri_due>0 AND $qryPriPart)";
			}
			else if($ins_group == 'secondary'){
				$query .= " AND (patient_charge_list_details.sec_due>0 AND $qrySecPart)";
			}
		}
		else if(trim($insCompanies) != ''){
			if(trim($ins_group) == ''){			
				$query .= " AND ((patient_charge_list.primaryInsuranceCoId in($insCompanies) 
						AND patient_charge_list_details.pri_due>0 AND $qryPriPart) 
					OR (patient_charge_list.secondaryInsuranceCoId in($insCompanies) 
						AND patient_charge_list_details.sec_due>0 AND $qrySecPart) 
					OR (patient_charge_list.tertiaryInsuranceCoId in($insCompanies) 
						AND patient_charge_list_details.tri_due>0 AND $qryTerPart))";
			}
			else if($ins_group == 'primary'){
				$query .= " AND (patient_charge_list.primaryInsuranceCoId in($insCompanies) 
					AND patient_charge_list_details.pri_due>0 AND $qryPriPart)";
			}
			else if($ins_group == 'secondary'){
				$query .= " AND (patient_charge_list.secondaryInsuranceCoId in($insCompanies) 
				AND patient_charge_list_details.sec_due>0  AND $qrySecPart)";
			}
		}
	}
	$query .= " order by trim(patient_data.lname), patient_data.fname, 
				patient_charge_list.date_of_service desc";

	$qryRes = array();
	$qry = imw_query($query);
	while($res=imw_fetch_assoc($qry)){
		$qryRes[] = $res;
	}
	
	$insComIdArr = array();
	$mainInsIdArr = array();
	$patientNameArr = array();
	$grandPatientDueArr = array();
	$patient_age_arr = array();
	$task_reminder_enc_arr = array();
	$totalCollectionInsBalance=0;
	$enc_for_temp_query='';
	$arrSuperbillIds=array();
	for($i=0;$i<count($qryRes);$i++){
		
		//ALL COLLECTION STATUS TOTAL
		if(sizeof($arrCollectionIds)>0 && $arrCollectionIds[$qryRes[$i]['pat_account_status']]){
			$totalCollectionBalance+= $qryRes[$i]['pri_due']+$qryRes[$i]['sec_due']+$qryRes[$i]['tri_due'];
			$totalCollectionInsBalance+= $qryRes[$i]['pri_due']+$qryRes[$i]['sec_due']+$qryRes[$i]['tri_due'];
			
		}else{
		
			$patient_id = $qryRes[$i]['patient_id'];
			$encounter_id = $qryRes[$i]['encounter_id'];
			$task_reminder_enc_arr[$encounter_id] = $encounter_id;
			$priDue=$secDue=$triDue=0;
			$priInsId=$secInsId=$triInsId=0;
			$detailId = $qryRes[$i]['charge_list_detail_id'];
			$primaryInsuranceCoId = $qryRes[$i]['primaryInsuranceCoId'];
			$secondaryInsuranceCoId = $qryRes[$i]['secondaryInsuranceCoId'];
			$tertiaryInsuranceCoId = $qryRes[$i]['tertiaryInsuranceCoId'];
			$caseId = $qryRes[$i]['case_type_id'];
			$priDue= $qryRes[$i]['pri_due'];
			$secDue= $qryRes[$i]['sec_due'];
			$triDue= $qryRes[$i]['tri_due'];
			$arrEncIds[$encounter_id]=$encounter_id;
			$arrSuperbillIds[$qryRes[$i]['superbillFormId']]=$qryRes[$i]['superbillFormId'];

			//SETTING OF GROUP BY (ONLY IN CASE OF GROUPS or FACILITY)
			$firstGroupId='';
			if($grpby_block!='grpby_insurance'){
				$firstGroupId= ($grpby_block=='grpby_groups') ? $qryRes[$i]['gro_id'] : $qryRes[$i]['facility_id'];
				$firstGroupId= (empty($firstGroupId)==true) ? '0': $firstGroupId;
			}
			
			$dos_date_diff = $qryRes[$i]['dos_date_diff'];
			if($DateRangeFor=='dot'){
				$last_pri_dop_diff = $qryRes[$i]['last_pri_dop_diff'];
				$last_sec_dop_diff = $qryRes[$i]['last_sec_dop_diff'];
				$last_ter_dop_diff = $qryRes[$i]['last_ter_dop_diff'];
				$last_pat_dop_diff = $qryRes[$i]['last_pat_dop_diff'];
			}
			
			$insId = NULL;
			//--- IF PRIMARY INSURANCE NOT PAID ----
			if($primaryInsuranceCoId != '' && $priDue>0){
				$arrPatPolicyComp[$patient_id][$caseId][$primaryInsuranceCoId][$encounter_id]['primary']= $encounter_id;
				
				if($insuranceCompGroup=='' || $insuranceCompGroup=='primary'){
					if(sizeof($insuranceNameArr)>0){
						if(in_array($primaryInsuranceCoId,$insuranceNameArr)){
							$priInsId = $qryRes[$i]['primaryInsuranceCoId'];
						}
					}else{
						$priInsId = $qryRes[$i]['primaryInsuranceCoId'];
					}
				}
			}
			
			//--- IF SECONDARY INSURANCE NOT PAID ----
			if($secondaryInsuranceCoId != '' && $secDue>0){
				$arrPatPolicyComp[$patient_id][$caseId][$secondaryInsuranceCoId][$encounter_id]['secondary']= $encounter_id;
	
				if($insuranceCompGroup=='' || $insuranceCompGroup=='secondary'){
					if(sizeof($insuranceNameArr)>0){
						if(in_array($secondaryInsuranceCoId,$insuranceNameArr)){
							$secInsId = $qryRes[$i]['secondaryInsuranceCoId'];
						}
					}else{
						$secInsId = $qryRes[$i]['secondaryInsuranceCoId'];
					}			
				}
			}
			//--- IF TERTIARY INSURANCE NOT PAID ----
			if($tertiaryInsuranceCoId != '' && $triDue>0){
				$arrPatPolicyComp[$patient_id][$caseId][$tertiaryInsuranceCoId][$encounter_id]['tertiary']= $encounter_id;
				
				if($insuranceCompGroup==''){
					if(sizeof($insuranceNameArr)>0){
						if(in_array($tertiaryInsuranceCoId,$insuranceNameArr)){
							$triInsId = $qryRes[$i]['tertiaryInsuranceCoId'];
						}
					}else{
						$triInsId = $qryRes[$i]['tertiaryInsuranceCoId'];
					}			
				}
			}
			//--- SINGLE INSURANCE COMPANY ---
			if(trim($insuranceName) != ''){
				if(trim($insuranceName) == $insId){
					$insId = $insuranceName;
				}
			}
			
			//--- GET PATIENT NAME -----------
			$patient_name_arr = array();
			$patient_name_arr["LAST_NAME"] = $qryRes[$i]['lname'];
			$patient_name_arr["FIRST_NAME"] = $qryRes[$i]['fname'];
			$patient_name_arr["MIDDLE_NAME"] = $qryRes[$i]['mname'];
			if($qryRes[$i]['fname']!=""){
				$patient_name = $qryRes[$i]['lname'].', '.$qryRes[$i]['fname'].' ';
			}else{
				$patient_name = $qryRes[$i]['lname'].' ';
			}
			if(trim($qryRes[$i]['mname']) != ''){
				$patient_name .= substr(trim($qryRes[$i]['mname']),0,1).".";
			}
			
			$patient_name .= ' - '.$patient_id;
	
			if($priInsId > 0 || $secInsId > 0 || $triInsId > 0){
				
				$patient_dob = $qryRes[$i]['patient_dob'];
				$patient_age_arr[$patient_id] = $patient_dob .' ('.get_age($qryRes[$i]['DOB']).')';
				$patientNameArr[$patient_id] = $patient_name;
											
				if($DateRangeFor=='dos'){
					for($a=$aging_start;$a<=$aging_to;$a++){
						$start = $a;
						$a = $a > 0 ? $a - 1 : $a;
						$end = ($a) + $aggingCycle;
						//--- INSURANCE DUE AS A/R AGGING -------
						if($summary_detail == "summary"){
							if($priDue>0){
								if($dos_date_diff >= $start && $dos_date_diff <= $end){
									$insComIdArr[$priInsId] = $priInsId;
									$qryRes[$i]['insuranceDue']=$priDue; 
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$priInsId][$start][] = $qryRes[$i];
									}else{
										$mainInsIdArr[$priInsId][$start][] = $qryRes[$i];
									}
								}
							}
							if($secDue>0){
								if($dos_date_diff >= $start && $dos_date_diff <= $end){
									$insComIdArr[$secInsId] = $secInsId;
									$qryRes[$i]['insuranceDue']=$secDue; 
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$secInsId][$start][] = $qryRes[$i];
									}else{									
										$mainInsIdArr[$secInsId][$start][] = $qryRes[$i];
									}
								}
							}
							if($triDue>0){
								if($dos_date_diff >= $start && $dos_date_diff <= $end){
									$insComIdArr[$triInsId] = $triInsId;
									$qryRes[$i]['insuranceDue']=$triDue; 
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$triInsId][$start][] = $qryRes[$i];
									}else{									
										$mainInsIdArr[$triInsId][$start][] = $qryRes[$i];
									}
								}
							}	
											
						}
						else{
							if($priDue>0){
								if($dos_date_diff >= $start && $dos_date_diff <= $end){
									$insComIdArr[$priInsId] = $priInsId;
									$qryRes[$i]['insuranceDue']=$priDue; 
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$priInsId][$patient_id][$encounter_id][$start][] = $qryRes[$i];
									}else{									
										$mainInsIdArr[$priInsId][$patient_id][$encounter_id][$start][] = $qryRes[$i];
									}
								}
							}
							if($secDue>0){
								if($dos_date_diff >= $start && $dos_date_diff <= $end){
									$insComIdArr[$secInsId] = $secInsId;
									$qryRes[$i]['insuranceDue']=$secDue; 
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$secInsId][$patient_id][$encounter_id][$start][] = $qryRes[$i];
									}else{									
										$mainInsIdArr[$secInsId][$patient_id][$encounter_id][$start][] = $qryRes[$i];
									}
								}
							}
							if($triDue>0){
								if($dos_date_diff >= $start && $dos_date_diff <= $end){						
									$insComIdArr[$triInsId] = $triInsId;
									$qryRes[$i]['insuranceDue']=$triDue; 
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$triInsId][$patient_id][$encounter_id][$start][] = $qryRes[$i];
									}else{									
										$mainInsIdArr[$triInsId][$patient_id][$encounter_id][$start][] = $qryRes[$i];
									}
								}
							}
						}
						$a += $aggingCycle;
					}
					
					if($All_due == true){
						//--- INSURANCE DUE AS A/R AGGING BY 181+ -------
						if($summary_detail == "summary"){
							if($priDue>0){
								if($dos_date_diff >= 181){
									$insComIdArr[$priInsId] = $priInsId;
									$qryRes[$i]['insuranceDue']=$priDue;
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$priInsId][181][] = $qryRes[$i];
									}else{
										$mainInsIdArr[$priInsId][181][] = $qryRes[$i];
									}
								}
							}
							if($secDue>0){
								if($dos_date_diff >= 181){
									$insComIdArr[$secInsId] = $secInsId;
									$qryRes[$i]['insuranceDue']=$secDue;
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$secInsId][181][] = $qryRes[$i];
									}else{
										$mainInsIdArr[$secInsId][181][] = $qryRes[$i];
									}
								}
							}
							if($triDue>0){
								if($dos_date_diff >= 181){
									$insComIdArr[$triInsId] = $triInsId;
									$qryRes[$i]['insuranceDue']=$triDue;
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$triInsId][181][] = $qryRes[$i];
									}else{
										$mainInsIdArr[$triInsId][181][] = $qryRes[$i];
									}
								}
							}
							
						}
						else{
							if($priDue>0){
								if($dos_date_diff >= 181){
									$insComIdArr[$priInsId] = $priInsId;
									$qryRes[$i]['insuranceDue']=$priDue; 
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$priInsId][$patient_id][$encounter_id][181][] = $qryRes[$i]; 
									}else{
										$mainInsIdArr[$priInsId][$patient_id][$encounter_id][181][] = $qryRes[$i]; 
									}
								}
							}
							if($secDue>0){
								if($dos_date_diff >= 181){							
									$insComIdArr[$secInsId] = $secInsId;
									$qryRes[$i]['insuranceDue']=$secDue; 
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$secInsId][$patient_id][$encounter_id][181][] = $qryRes[$i]; 
									}else{
										$mainInsIdArr[$secInsId][$patient_id][$encounter_id][181][] = $qryRes[$i]; 
									}
								}
							}
							if($triDue>0){
								if($dos_date_diff >= 181){							
									$insComIdArr[$triInsId] = $triInsId;
									$qryRes[$i]['insuranceDue']=$triDue; 
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$triInsId][$patient_id][$encounter_id][181][] = $qryRes[$i]; 
									}else{
										$mainInsIdArr[$triInsId][$patient_id][$encounter_id][181][] = $qryRes[$i];
									}
								}
							}						
						}
					}
				}else{ //DOT
					for($a=$aging_start;$a<=$aging_to;$a++){
						$start = $a;
						$a = $a > 0 ? $a - 1 : $a;
						$end = ($a) + $aggingCycle;
						//--- INSURANCE DUE AS A/R AGGING -------
						if($summary_detail == "summary"){
							if($priDue>0){
								if($last_pri_dop_diff >= $start && $last_pri_dop_diff <= $end){
									$insComIdArr[$priInsId] = $priInsId;
									$qryRes[$i]['insuranceDue']=$priDue; 
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$priInsId][$start][] = $qryRes[$i];
									}else{
										$mainInsIdArr[$priInsId][$start][] = $qryRes[$i];
									}
								}
							}
							if($secDue>0){
								if($last_sec_dop_diff >= $start && $last_sec_dop_diff <= $end){
									$insComIdArr[$secInsId] = $secInsId;
									$qryRes[$i]['insuranceDue']=$secDue; 
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$secInsId][$start][] = $qryRes[$i];
									}else{
										$mainInsIdArr[$secInsId][$start][] = $qryRes[$i];
									}
								}
							}
							if($triDue>0){
								if($last_ter_dop_diff >= $start && $last_ter_dop_diff <= $end){
									$insComIdArr[$triInsId] = $triInsId;
									$qryRes[$i]['insuranceDue']=$triDue; 
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$triInsId][$start][] = $qryRes[$i];
									}else{
										$mainInsIdArr[$triInsId][$start][] = $qryRes[$i];
									}
								}
							}	
											
						}
						else{
							if($priDue>0){
								if($last_pri_dop_diff >= $start && $last_pri_dop_diff <= $end){
									$insComIdArr[$priInsId] = $priInsId;
									$qryRes[$i]['insuranceDue']=$priDue; 
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$priInsId][$patient_id][$encounter_id][$start][] = $qryRes[$i];
									}else{
										$mainInsIdArr[$priInsId][$patient_id][$encounter_id][$start][] = $qryRes[$i];
									}
								}
							}
							if($secDue>0){
								if($last_sec_dop_diff >= $start && $last_sec_dop_diff <= $end){
									$insComIdArr[$secInsId] = $secInsId;
									$qryRes[$i]['insuranceDue']=$secDue; 
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$secInsId][$patient_id][$encounter_id][$start][] = $qryRes[$i];
									}else{
										$mainInsIdArr[$secInsId][$patient_id][$encounter_id][$start][] = $qryRes[$i];
									}
								}
							}
							if($triDue>0){
								if($last_ter_dop_diff >= $start && $last_ter_dop_diff <= $end){						
									$insComIdArr[$triInsId] = $triInsId;
									$qryRes[$i]['insuranceDue']=$triDue; 
									if($grpby_block!='grpby_insurance'){
										$mainInsIdArr[$firstGroupId][$triInsId][$patient_id][$encounter_id][$start][] = $qryRes[$i];
									}else{
										$mainInsIdArr[$triInsId][$patient_id][$encounter_id][$start][] = $qryRes[$i];
									}
								}
							}
						}
						$a += $aggingCycle;
					}
					
					if($All_due == true){
						//--- INSURANCE DUE AS A/R AGGING BY 181+ -------
						if($summary_detail == "summary"){
							if($priDue>0){
								if($last_pri_dop_diff >= 181){
									$insComIdArr[$priInsId] = $priInsId;
									$qryRes[$i]['insuranceDue']=$priDue;
									if($grpby_block!='grpby_insurance'){									
										$mainInsIdArr[$firstGroupId][$priInsId][181][] = $qryRes[$i];
									}else{
										$mainInsIdArr[$priInsId][181][] = $qryRes[$i];
									}
								}
							}
							if($secDue>0){
								if($last_sec_dop_diff >= 181){
									$insComIdArr[$secInsId] = $secInsId;
									$qryRes[$i]['insuranceDue']=$secDue;
									if($grpby_block!='grpby_insurance'){									
										$mainInsIdArr[$firstGroupId][$secInsId][181][] = $qryRes[$i];
									}else{
										$mainInsIdArr[$secInsId][181][] = $qryRes[$i];
									}
								}
							}
							if($triDue>0){
								if($last_ter_dop_diff >= 181){
									$insComIdArr[$triInsId] = $triInsId;
									$qryRes[$i]['insuranceDue']=$triDue;
									if($grpby_block!='grpby_insurance'){									
										$mainInsIdArr[$firstGroupId][$triInsId][181][] = $qryRes[$i];
									}else{
										$mainInsIdArr[$triInsId][181][] = $qryRes[$i];
									}
								}
							}
							
						}
						else{
							if($priDue>0){
								if($last_pri_dop_diff >= 181){
									$insComIdArr[$priInsId] = $priInsId;
									$qryRes[$i]['insuranceDue']=$priDue; 
									if($grpby_block!='grpby_insurance'){									
										$mainInsIdArr[$firstGroupId][$priInsId][$patient_id][$encounter_id][181][] = $qryRes[$i]; 
									}else{									
										$mainInsIdArr[$priInsId][$patient_id][$encounter_id][181][] = $qryRes[$i]; 
									}
								}
							}
							if($secDue>0){
								if($last_sec_dop_diff >= 181){							
									$insComIdArr[$secInsId] = $secInsId;
									$qryRes[$i]['insuranceDue']=$secDue; 
									if($grpby_block!='grpby_insurance'){									
										$mainInsIdArr[$firstGroupId][$secInsId][$patient_id][$encounter_id][181][] = $qryRes[$i]; 
									}else{									
										$mainInsIdArr[$secInsId][$patient_id][$encounter_id][181][] = $qryRes[$i]; 
									}
								}
							}
							if($triDue>0){
								if($last_ter_dop_diff >= 181){							
									$insComIdArr[$triInsId] = $triInsId;
									$qryRes[$i]['insuranceDue']=$triDue; 
									if($grpby_block!='grpby_insurance'){									
										$mainInsIdArr[$firstGroupId][$triInsId][$patient_id][$encounter_id][181][] = $qryRes[$i]; 
									}else{									
										$mainInsIdArr[$triInsId][$patient_id][$encounter_id][181][] = $qryRes[$i];
									}
								}
							}						
						}
					}				
				}
			}
			
		}
	}
	


	//FETCH PAYMENTS AND ADJUSTMENTS
	$arrAdjustmentAmt=$arrPaymentAmt=array();
	if(($inc_adjustments=='1' || $inc_payments=='1') && sizeof($arrEncIds)>0){
		$enc_for_temp_query='';
		foreach($arrEncIds as $encounter_id){
			$enc_for_temp_query.='('.$encounter_id.'),';
		}
		$enc_for_temp_query=substr($enc_for_temp_query,0, -1);
		
		//CREATE TEMP TABLE AND INSERT DATA
		$temp_join_part='';
		if(empty($enc_for_temp_query)==false){
			$tmp_table="IMWTEMP_reports_enc_ids_".time().'_'.$_SESSION["authId"];
			imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);
			imw_query("CREATE TEMPORARY TABLE ".$tmp_table." (enc_id INT)");
			imw_query("INSERT INTO $tmp_table (enc_id) VALUES ".$enc_for_temp_query);
			$temp_join_part=" INNER JOIN ".$tmp_table." t_tbl ON trans.encounter_id = t_tbl.enc_id";
		}

		$qry="Select trans.report_trans_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, 
		trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_qry_type, trans.parent_id,
		trans.trans_dot, trans.trans_del_operator_id  
		FROM report_enc_trans trans 
		".$temp_join_part."
		WHERE trans.trans_type!='charges' 
		ORDER BY trans.trans_dot, trans.trans_dot_time";
		$rs=imw_query($qry);
		while($res = imw_fetch_assoc($rs)){
			$report_trans_id=$res['report_trans_id'];
			$encounter_id= $res['encounter_id'];
			$chgDetId= $res['charge_list_detail_id'];
			$insCompId = $res['trans_ins_id'];
			$trans_type= strtolower($res['trans_type']);
			$trans_by= strtolower($res['trans_by']);	
			$tempRecordData[$report_trans_id]=$res['trans_amount'];		
			
			switch($trans_type){
				case 'paid':
				case 'copay-paid':
				case 'deposit':
				case 'interest payment':
				case 'negative payment':
				case 'copay-negative payment':
					$paidForProc=$res['trans_amount'];
					if($trans_type=='negative payment' || $trans_type=='copay-negative payment' || $res['trans_del_operator_id']>0)$paidForProc="-".$res['trans_amount'];
					if(($trans_type=='negative payment' || $trans_type=='copay-negative payment') && $res['trans_del_operator_id']>0)$paidForProc=$res['trans_amount'];

					//IF parent_id >0 THEN IT MEANS RECORD IS UPDATED. THEN REMOVE PREVIOUS FETCHED AMOUNT.
					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = ($trans_type=='negative payment' || $trans_type=='copay-negative payment') ? $tempRecordData[$res['parent_id']] : "-".$tempRecordData[$res['parent_id']];
					}
					$paidForProc+=$prevFetchedAmt; 
					
					$arrPaymentAmt[$chgDetId]+=$paidForProc;
				break;

				case 'credit':
				case 'debit':
					$crddbtamt=$res['trans_amount'];
					if($trans_type=='credit'){ 
						$crddbtamt= ($res['trans_del_operator_id']>0) ? "-".$res['trans_amount'] : $res['trans_amount'];							
					}else{  //debit
						$crddbtamt= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];				
					}

					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = ($trans_type=='credit') ? "-".$tempRecordData[$res['parent_id']] : $tempRecordData[$res['parent_id']];
					}
					$crddbtamt+=$prevFetchedAmt; 
					
					$arrPaymentAmt[$chgDetId]+= $crddbtamt;
					
				break;
				case 'default_writeoff':
					$normalWriteOffAmt[$chgDetId]= $res['trans_amount'];
				break;
				case 'write off':
				case 'discount':
					if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];

					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
					}
					$res['trans_amount']+=$prevFetchedAmt;
					
					$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'];
				break;
				case 'over adjustment':
					if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];

					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
					}
					$res['trans_amount']+=$prevFetchedAmt;
					
					$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'];
				break;
				case 'adjustment':
					$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = $tempRecordData[$res['parent_id']];
					}
					$res['trans_amount']+=$prevFetchedAmt;
					
					$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'];
				break;
				case 'returned check':
					$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = $tempRecordData[$res['parent_id']];
					}
					$res['trans_amount']+=$prevFetchedAmt;
					
					$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'];
				break;
				case 'refund':
					$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = $tempRecordData[$res['parent_id']];
					}
					$res['trans_amount']+=$prevFetchedAmt;
					
					$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'];
				break;
			}
		}
		//DROP TEMP TABLE
		imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);			
	}	
	unset($arrEncIds);

	//GET APPOINTMENT DETAILS
	$arrSuperbillIds=array_filter($arrSuperbillIds);
	if(($inc_appt_detail=='1') && sizeof($arrSuperbillIds)>0){

		$sb_for_temp_query='';
		foreach($arrSuperbillIds as $sb_id){
			$sb_for_temp_query.='('.$sb_id.'),';
		}
		$sb_for_temp_query=substr($sb_for_temp_query,0, -1);
		
		//CREATE TEMP TABLE AND INSERT DATA
		$temp_join_part='';
		if(empty($sb_for_temp_query)==false){
			$tmp_table="IMWTEMP_reports_sb_ids_".time().'_'.$_SESSION["authId"];
			imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);
			imw_query("CREATE TEMPORARY TABLE ".$tmp_table." (sb_id INT)");
			imw_query("INSERT INTO $tmp_table (sb_id) VALUES ".$sb_for_temp_query);
			$temp_join_part=" INNER JOIN ".$tmp_table." t_tbl ON sp.idSuperBill = t_tbl.sb_id";
		}	
		
		$qry = "SELECT sp.encounterId, date_format(sa.sa_app_start_date,'%m-%d-%Y') as sa_app_start_date,
		date_format(sa.sa_app_starttime, '%h:%i') as 'sa_app_starttime', sa.procedureid FROM superbill sp 
		".$temp_join_part."
		JOIN schedule_appointments sa ON sa.id=sp.sch_app_id 
		WHERE sp.sch_app_id>0";
		$rs = imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$eid=$res['encounterId'];
			$arrApptDetails[$eid]['appt_date']=$res['sa_app_start_date'];
			$arrApptDetails[$eid]['appt_time']=$res['sa_app_starttime'];
			$arrApptDetails[$eid]['appt_procedure']=$res['procedureid'];
		}
	}	


	//--- GET SELECTED INSURANCE COMPANIES NAME -------
	$insComIdStr = join(',',$insComIdArr);
	$insQry = "select id, in_house_code, name, contact_name, phone
				from insurance_companies where id in($insComIdStr) order by in_house_code";
	$insQryRes = array();
	$qry = imw_query($insQry);
	while($res=imw_fetch_assoc($qry)){
		$insQryRes[] = $res;
	}
	$insComIdArr = array();
	$selfInsIdArr = array();
	$insCompDetails = array();
	for($i=0;$i<count($insQryRes);$i++){
		$id = $insQryRes[$i]['id'];
		$name = trim($insQryRes[$i]['in_house_code']);
		if(empty($name) === true){
			$name = substr(trim($insQryRes[$i]['name']),0,8);
		}
		
		$contact_name = $insQryRes[$i]['contact_name'];
		$phone = $insQryRes[$i]['phone'];
		
		if(strtolower($name) == 'self pay'){
			$selfInsIdArr[$id] = $name;
		}
		else{
			$insComIdArr[$id] = $name;
		}			
		
		$insCompDetails[$id]['contact_name'] = $contact_name;
		$insCompDetails[$id]['phone'] = core_phone_format($phone);
	}

	// GET SELECTED PATIENT'S POLICY NOS -
	if(sizeof($arrPatPolicyComp)>0){
		$insPats='';
		$insPats = array_keys($arrPatPolicyComp);
		if(empty($insPats)===false){
			$strInsPats = implode(',', $insPats);
			$qry="Select type, pid, provider, policy_number, ins_caseid FROM insurance_data WHERE pid IN(".$strInsPats.") ORDER BY id";
			$rs = imw_query($qry);
			while($res=imw_fetch_array($rs)){
				$pid = $res['pid'];
				$insType = $res['type'];
				$caseId = $res['ins_caseid'];
				$compId = $res['provider'];
				
				$tempPolicyArr[$pid][$caseId][$compId][$insType] = $res['policy_number'];
			}
		}
		
		foreach($arrPatPolicyComp as $pid => $caseData){
			foreach($caseData as $caseId => $compData){
				foreach($compData as $compId => $encData){
					foreach($encData as $eid => $policyData){
						$insType = 'primary';
						$prefix = 'Pri- ';
						if($policyData['secondary']){		$prefix = 'Sec- ';	$insType = 'secondary'; }
						else if($policyData['tertiary']){	$prefix = 'Ter- '; $insType = 'tertiary'; }
						
						$arrEncInsPolicyNos[$eid].= $prefix.$tempPolicyArr[$pid][$caseId][$compId][$insType].'<br>';
					}
				}
			}
		}
	}
	if(count($mainInsIdArr)>0 || count($grandPatientDueArr)>0){
		//GET CREDIT AMOUNTS FOR ALL ENCOUNTERS
		//if($searchMod=='dot'){
			$totalOverPayment=0;

			//GETTING CREDIT AMOUNT WHERE OVER-PAYMENT IN MINUS FOR WHOLE ENCOUTNER.
			$qry= "Select SUM(patient_charge_list.overPayment) as 'totalOverPayment'
			FROM patient_charge_list 
			LEFT JOIN patient_data on patient_data.id = patient_charge_list.patient_id 
			WHERE patient_charge_list.del_status='0' AND patient_charge_list.overPayment>0";
			if(empty($strFacilities) == false){
				$qry.= " AND patient_charge_list.facility_id IN ($strFacilities)";
			}
			if(empty($strProviders) == false){
				$qry.= " AND patient_charge_list.primary_provider_id_for_reports in($strProviders)";
			}
			if(empty($grp_id) == false){
				$qry.= " and patient_charge_list.gro_id IN ($grp_id)";
			}
			if($startDate != '' && $endDate != ''){
				$qry.= " and (patient_charge_list.date_of_service between '$startDate' and '$endDate')";
			}
			$rs=imw_query($qry);
			$res=imw_fetch_assoc($rs);
			$totalOverPayment= $res['totalOverPayment'];

			//GETTING CREDIT AMOUNT WHERE OVER-PAYMENT DONE FOR PROCEDURE BUT STILL BALANCE EXIST FOR WHOLE ENCOUTNER.
			$qry= "Select SUM(patient_charge_list_details.overPaymentForProc) as 'totalOverPayment'
			FROM patient_charge_list 
			LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id 
			LEFT JOIN patient_data on patient_data.id = patient_charge_list.patient_id 
			WHERE patient_charge_list_details.overPaymentForProc>0 AND patient_charge_list.totalBalance>0
			AND patient_charge_list_details.del_status='0'";
			if(empty($strFacilities) == false){
				$qry.= " AND patient_charge_list.facility_id IN ($strFacilities)";
			}
			if(empty($strProviders) == false){
				$qry.= " AND patient_charge_list.primary_provider_id_for_reports in($strProviders)";
			}
			if(empty($grp_id) == false){
				$qry.= " and patient_charge_list.gro_id IN ($grp_id)";
			}
			if($startDate != '' && $endDate != ''){
				$qry.= " and (patient_charge_list.date_of_service between '$startDate' and '$endDate')";
			}
			$rs=imw_query($qry);
			$res=imw_fetch_assoc($rs);
			$totalOverPaymentHavingEncBal= $res['totalOverPayment'];			
			
			$totalOverPayment+= $totalOverPaymentHavingEncBal;			
		}
	//}
}
// Common header files for CSV and PDF
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$styleHTML.='<link href="'.$GLOBALS["webroot"].'/library/css/report.css" type="text/css" rel="stylesheet">';
	$styleHTML.='<link href="'.$GLOBALS["webroot"].'/library/css/common.css" type="text/css" rel="stylesheet">';
	$styleHTML.='<link href="'.$GLOBALS["webroot"].'/library/css/bootstrap.css" type="text/css" rel="stylesheet">';
	$styleHTML.='<script type="text/javascript" src="'.$GLOBALS["webroot"].'/library/js/jquery.min.1.12.4.js"></script>';
	$styleHTML.='<script type="text/javascript" src="'.$GLOBALS["webroot"].'/library/js/table2CSV.js"></script>';
	$styleHTML.='<script type="text/javascript">
				function getCSVData(){
					if($("#csv_text").val()==\'\'){
						var csv_value=$(\'#html_data_div\').table2CSV({delivery:\'value\'});
						$("#csv_text").val(csv_value);
						document.csvDownloadForm.submit();
					}
				}
				function download_csv() {
					document.getElementById("csvDirectDownloadForm").submit();	
				}				
				function resize() {
					var h = window.innerHeight;
					var adj_height = h-85;
					document.getElementById("html_data_div").style.height = adj_height; 
				}
				$(function() { resize(); });
				
				function showDetails(obj){
					var rowId = $(obj).data(\'id\');
					var RowObj = $(\'td[data-id="\'+rowId+\'"]\');
					if(RowObj.hasClass(\'hide\') == true) RowObj.removeClass(\'hide\');
					else RowObj.addClass(\'hide\');
				}
                
				function loadRptPatient(ptid) {
                    //To check restrict access of patient before load
                    $.when(opener.top.check_for_break_glass_restriction(ptid)).done(function(response){
                        opener.top.removeMessi();
                        if(response.rp_alert==\'y\') {
                            var patId=response.patId;
                            var bgPriv=response.bgPriv;
                            var rp_alert=response.rp_alert;
                            opener.top.core_restricted_prov_alert(patId, bgPriv,\'\',\'\',\'RPT\');
                        }else{
                            opener.top.core_set_pt_session(top.fmain, ptid,\'../accounting/review_payments.php\');
                        }
                    });
                }
				
			</script>';
	
	$stylePDF='<style>'.file_get_contents('css/reports_pdf.css').'</style>';	
	$stylePDF.='<link href="'.$GLOBALS["webroot"].'/library/css/report.css" type="text/css" rel="stylesheet">';
	$stylePDF.='<link href="'.$GLOBALS["webroot"].'/library/css/bootstrap.css" type="text/css" rel="stylesheet">';
	
if($summary_detail=='summary')
{
	if($grpby_block!='grpby_insurance'){
		include("insurance_ar_aging_grouping_summary.php");
	}else{

		$printAllDue = false;
		$m = 0;
		//--- GET TOTAL COLUMN WIDTH ----
		$column = ceil(($aging_to - $aging_start) / $aggingCycle);
		if($All_due == true){
			$column++;
		}

//---- GET TOTAL WIDTH FOR AUTO GENERATE TD --------
switch($column){
	case '1':
		$width = floor(310/$column);
		$hdWidth = $width;
		$headerWidth = 238;
	break;
	case '2':
		$width = floor(395/$column);
		$hdWidth = $width + 10;		
		$headerWidth = 238;
	break;
	case '3':
		$width = floor(435/$column);
		$hdWidth = $width + 7;
		$headerWidth = 240;
	break;
	case '4':
		$width = floor(450/$column);
		$hdWidth = $width + 5;
		$headerWidth = 240;
	break;
	case '5':
		$width = floor(470/$column);
		$hdWidth = $width;
		$headerWidth = 241;
	break;
	case '6':
		$width = floor(470/$column);
		$hdWidth = $width;
		$headerWidth = 241;
	break;
	case '7':
		$width = floor(470/$column);
		$hdWidth = $width;
		$headerWidth = 244;
	break;
}
//$width.='px';
//--- TOTAL TD COLSPAN ---
$totalTd = 1 + $column;
if($inc_payments=='1'){	$totalTd++;	}
if($inc_adjustments=='1'){	$totalTd++;	}
//---- GET ALL DUE AMOUNT FOR SELF PAY INSURANCE COMPANY -------
$grandTotalArr = array();

if(count($selfInsIdArr) > 0){
	$self_ins_id_arr = array_keys($selfInsIdArr);
	$csvPageContent = $pageContent = NULL;
	for($i=0;$i<count($self_ins_id_arr);$i++){
		$ins_id = $self_ins_id_arr[$i];
		$ins_name = $selfInsIdArr[$ins_id];
		$total_ins_due = 0;
		$insuarnceDueData = NULL;
		for($a=$aging_start;$a<=$aging_to;$a++){
			$start = $a;
			$a = $a > 0 ? $a - 1 : $a;
			$insDueAmt = 0;
			$insDataArr = $mainInsIdArr[$ins_id][$start];
			for($d=0;$d<count($insDataArr);$d++){
				$insDueAmt += preg_replace('/,/','',$insDataArr[$d]['insuranceDue']);
			}
			
			$grandTotalArr[$start][] = $insDueAmt;
			$total_ins_due += $insDueAmt;
			
			//--- NUMBER FORMAT ----------
			$insDueAmt = $CLSReports->numberFormat($insDueAmt,2);
			$insuarnceDueData .= <<<DATA
				<td style="text-align:right;" width="$width" class="text_10">$insDueAmt</td>
DATA;
			$a += $aggingCycle;
		}
		
		if($All_due == true){
			$insDataArr = $mainInsIdArr[$ins_id][181];
			for($d=0;$d<count($insDataArr);$d++){
				$insDueAmt += preg_replace('/,/','',$insDataArr[$d]['insuranceDue']);
			}
			
			$grandTotalArr[181][] = $insDueAmt;
			$total_ins_due += $insDueAmt;
			
			//--- NUMBER FORMAT ----------
			$insDueAmt = $CLSReports->numberFormat($insDueAmt,2);
			$insuarnceDueData .= <<<DATA
				<td style="text-align:right;" width="$width" class="text_10">$insDueAmt</td>
DATA;
		}
		
		
		$ins_name_pdf=wordwrap($ins_name, 15, "<br>", true);

		//--- NUMBER FORMAT ----		
		$total_ins_due = $CLSReports->numberFormat($total_ins_due,2);
		$totalTd1 = $totalTd + 1;
		$pageContent .= <<<DATA
			<tr>
				<td align="left" width="100" class="text_10">$ins_name_pdf</td>
				$insuarnceDueData
				<td align="right" width="$width" class="text_10">$total_ins_due</td>
			</tr>
			<tr>
				<td height="1px" bgcolor="#000000" colspan="$totalTd1"></td>
			</tr>
DATA;

		$csvPageContent .= <<<DATA
			<tr bgcolor="#FFFFFF">
				<td style="text-align:left;" class="text_10">$ins_name</td>
				$insuarnceDueData
				<td style="text-align:right;" class="text_10">$total_ins_due</td>
			</tr>
DATA;
	}
}

//---- GET ALL INSURANCE COMPANIES DATA -------
if(count($insComIdArr) > 0){
	$ins_com_id_arr = array_keys($insComIdArr);
	for($i=0;$i<count($ins_com_id_arr);$i++){
		$ins_id = $ins_com_id_arr[$i];
		$ins_name = $insComIdArr[$ins_id];
		$total_ins_due = $payments=$adjustments=0;
		$insuarnceDueData = NULL;
		for($a=$aging_start;$a<=$aging_to;$a++){
			$start = $a;
			$a = $a > 0 ? $a - 1 : $a;
			$insDueAmt = 0;
			$insDataArr = $mainInsIdArr[$ins_id][$start];
			for($d=0;$d<count($insDataArr);$d++){
				$insDueAmt += preg_replace('/,/','',$insDataArr[$d]['insuranceDue']);

				$chgDetId=$insDataArr[$d]['charge_list_detail_id'];
				//PAYMENTS
				if($arrPaymentAmt[$chgDetId]){
					$payments+=$arrPaymentAmt[$chgDetId];
					unset($arrPaymentAmt[$chgDetId]);
				}
				//ADJUSTMENTS
				if($arrAdjustmentAmt[$chgDetId] || $normalWriteOffAmt[$chgDetId]){
					$adjustments+=$arrAdjustmentAmt[$chgDetId]+$normalWriteOffAmt[$chgDetId];
					if($arrAdjustmentAmt[$chgDetId])unset($arrAdjustmentAmt[$chgDetId]);
					if($normalWriteOffAmt[$chgDetId])unset($normalWriteOffAmt[$chgDetId]);
				}					
			}
			
			$grandTotalArr[$start][] = $insDueAmt;
			$total_ins_due += $insDueAmt;
			
			//--- NUMBER FORMAT ----------
			$insDueAmt = $CLSReports->numberFormat($insDueAmt,2);
			$insuarnceDueData .= <<<DATA
				<td style="text-align:right;" width="$width" class="text_10">$insDueAmt</td>
DATA;
			$a+= $aggingCycle;
		}
		
		if($All_due == true){
			$insDataArr = $mainInsIdArr[$ins_id][181];
			$insDueAmt = 0;
			for($d=0;$d<count($insDataArr);$d++){
				$insDueAmt += preg_replace('/,/','',$insDataArr[$d]['insuranceDue']);
				
				$chgDetId=$insDataArr[$d]['charge_list_detail_id'];
				//PAYMENTS
				if($arrPaymentAmt[$chgDetId]){
					$payments+=$arrPaymentAmt[$chgDetId];
					unset($arrPaymentAmt[$chgDetId]);
				}
				//ADJUSTMENTS
				if($arrAdjustmentAmt[$chgDetId] || $normalWriteOffAmt[$chgDetId]){
					$adjustments+=$arrAdjustmentAmt[$chgDetId]+$normalWriteOffAmt[$chgDetId];
					if($arrAdjustmentAmt[$chgDetId])unset($arrAdjustmentAmt[$chgDetId]);
					if($normalWriteOffAmt[$chgDetId])unset($normalWriteOffAmt[$chgDetId]);
				}	
			}
			$grandTotalArr[181][] = $insDueAmt;
			$total_ins_due += $insDueAmt;
			
			//--- NUMBER FORMAT ----------
			$insDueAmt = $CLSReports->numberFormat($insDueAmt,2);
			$insuarnceDueData .= <<<DATA
				<td style="text-align:right;" width="$width" class="text_10">$insDueAmt</td>
DATA;
		}

		$ins_name2 = explode(" ",$ins_name);
		$ins_name3 = "";
		foreach($ins_name2 as $ins_val){  
				$val_len = strlen($ins_val);
				if($val_len>18){
				$loop_len = ceil($val_len/18);
				$lp_start = 0;
				 for($lp_i=0;$lp_i<$loop_len;$lp_i++){ 
				 $ins_name3.= substr($ins_val,$lp_start,18).' ';
					$lp_start+=18; 
				 }
			  }else{ 
			  	$ins_name3.=$ins_val.' ';
			  }
		}
		if($ins_name3)$ins_name=$ins_name3; 
		
		//--- NUMBER FORMAT ----		
		$total_ins_due = $CLSReports->numberFormat($total_ins_due,2);
		$Tds = $totalTd-1;
		$Tds= ($inc_payments==1)? $Tds-1: $Tds;
		$Tds= ($inc_adjustments==1)? $Tds-1: $Tds;
		$totalTd1 = $totalTd + 1;

		$ins_name_pdf=wordwrap($ins_name, 15, "<br>", true);


		//--- PDF DETAIL DATA FOR INSURANCE DUE ---
		$td_payments=$td_adjustments='';
		if($inc_payments==1){
			$total_ins_payments+=$payments;
			$td_payments='<td style="text-align:right;" width="'.$width.'" class="text_10">'.$CLSReports->numberFormat($payments,2).'</td>';
		}
		if($inc_adjustments==1){
			$total_ins_adjustments+=$adjustments;
			$td_adjustments='<td style="text-align:right;" width="'.$width.'" class="text_10">'.$CLSReports->numberFormat($adjustments,2).'</td>';
		}

		$pageContent.='
		<tr>
			<td colspan="'.$totalTd1.'">&nbsp;</td>
		</tr>
		<tr>
			<td align="left" width="80" class="text_10">'.$ins_name_pdf.'</td>
			'.$insuarnceDueData.'
			<td align="right" width="'.$width.'" class="text_10">'.$total_ins_due.'</td>
			'.$td_payments.'
			'.$td_adjustments.'
			</tr>
		<tr>
			<td height="1px" bgcolor="#000000" colspan="'.$totalTd1.'"></td>
		</tr>';

		//--- CSV DETAIL DATA FOR INSURANCE DUE ---
		$csvPageContent .='
			<tr bgcolor="#FFFFFF">
				<td style="text-align:left;" class="text_10">'.$ins_name.'</td>
				'.$insuarnceDueData.'
				<td style="text-align:right;" class="text_10">'.$total_ins_due.'</td>
				'.$td_payments.'
				'.$td_adjustments.'
			</tr>';
	}
}


//--- CREATE DATA FOR HTML FILE -----
if($pageContent != ''){
	//-- GET OPERATOR NAME ----
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = ucfirst(trim($op_name_arr[1][0]));
	$op_name .= ucfirst(trim($op_name_arr[0][0]));
	$patDueWdOutIns = $patientDueData = $totalDueData = NULL;
	
	$grand_total_arr = array();
	//--- GET HEADER LABELS -------		
	for($a=$aging_start;$a<=$aging_to;$a++){
		$start = $a;
		$a = $a > 0 ? $a - 1 : $a;
		$end = ($a) + $aggingCycle;
		$headerTd .= <<<DATA
			<td class="text_b_w" width="$hdWidth" style="text-align:right;">
				$start - $end
			</td>
DATA;
		
		$insDue = NULL;
		if(count($grandTotalArr[$start])>0){			
			$insDue = array_sum($grandTotalArr[$start]);
			// CHART	
			$chartFacility[$m][1] = $start."-".$end;
			$chartFacility[$m][2] = $insDue;			

		}
		$grand_total_arr[$start][] = $insDue;
		
		//--- GET PATIENT DUE AMOUNT WITHOUT INSURANCE CARRIER ---
		$pat_ins_due_amt = NULL;
		if(count($patBalArr[$a])>0){
			$pat_ins_due_amt = array_sum($patBalArr[$a]);
		}	
		$grand_total_arr[$start][] = $pat_ins_due_amt;	
		
		$pat_ins_due_amt = $CLSReports->numberFormat($pat_ins_due_amt,2);
		$patDueWdOutIns .= <<<DATA
			<td class="text_10b" width="$width" style="text-align:right;">
				$pat_ins_due_amt
			</td>
DATA;
		
		$totalBalance += $insDue;	
		$insDue = $CLSReports->numberFormat($insDue,2);
		$totalDueData .= <<<DATA
			<td class="text_10b" width="$width" style="text-align:right;">
				$insDue
			</td>
DATA;
		//--- PATIENT DUE AMOUNT -----
		$patDue = NULL;		
		if(count($grandPatientDueArr[$start])>0){
			$patDue = array_sum($grandPatientDueArr[$start]);
			// CHART
			$chartFacility[$m][3]=$patDue;
		}
		
		$patientDueAmountArr[] = $patDue;
		$patDue = $CLSReports->numberFormat($patDue,2);
		
		$patientDueData .= <<<DATA
			<td class="text_10b" width="$width" style="text-align:right;">
				$patDue
			</td>
DATA;
		
		$a += $aggingCycle;
		$m++;
	}
	
	//--- GET DATA FOR AGGING 181+ -------- 
	if($All_due == true){
		$headerTd .= <<<DATA
			<td class="text_b_w" width="$width" style="text-align:right;">181+</td>
DATA;
		
		$insDue = NULL;
		// CHART
		$chartFacility[$m][1] = '181+';
		
		if(count($grandTotalArr[181])>0){
			$insDue = array_sum($grandTotalArr[181]);
			// CHART
			$chartFacility[$m][2] = $insDue;	
		}
		$grand_total_arr[181][] = $insDue;

		$totalBalance += $insDue;	
		$insDue = $CLSReports->numberFormat($insDue,2);		
		$totalDueData .= <<<DATA
			<td class="text_10b" style="text-align:right;">
				$insDue
			</td>
DATA;
		
		//--- GET PATIENT DUE AMOUNT WITH OUT INSURANCE CARRIER ---
		$pat_ins_due_amt = NULL;
		if(count($patBalArr[181])>0){
			$pat_ins_due_amt = array_sum($patBalArr[181]);
		}
		$grand_total_arr[181][] = $pat_ins_due_amt;		
		
		$pat_ins_due_amt = $CLSReports->numberFormat($pat_ins_due_amt,2);
		$patDueWdOutIns .= <<<DATA
			<td class="text_10b" width="$width" style="text-align:right;">
				$pat_ins_due_amt
			</td>
DATA;

		//--- PATIENT DUE AMOUNT -----
		$patDue = NULL;		
		if(count($grandPatientDueArr[181])>0){
			$patDue = array_sum($grandPatientDueArr[181]);
			
			// CHART
			$chartFacility[$m][3] = $patDue;	
		}
		$patientDueAmountArr[] = $patDue;
		$patDue = $CLSReports->numberFormat($patDue,2);
		
		$patientDueData .= <<<DATA
			<td class="text_10b" width="$width" style="text-align:right;">
				$patDue
			</td>
DATA;
	}
	
	//--- GET GRAND TOTAL AMOUNT ---
	$grandTotalKeys = array_keys($grand_total_arr);
	$grandTdTotalData = NULL;
	$totalGrandDueAmtArr = array();
	for($g=0;$g<count($grandTotalKeys);$g++){
		$keyid = $grandTotalKeys[$g];
		$grandTdTotal = NULL;
		if(count($grand_total_arr[$keyid])>0){
			$grandTdTotal = array_sum($grand_total_arr[$keyid]);
		}
		$totalGrandDueAmtArr[] = $grandTdTotal;
		$grandTdTotal = $CLSReports->numberFormat($grandTdTotal,2);
		$grandTdTotalData .= <<<DATA
			<td class="text_10b" width="$width" style="text-align:right;">
				$grandTdTotal
			</td>
DATA;
	}
	
	$totalGrandDueAmt = array_sum($totalGrandDueAmtArr);
	$grandTotal = $totalGrandDueAmt + $totalCollectionBalance; 
	$balnaceAfterDeduct = $grandTotal - $totalOverPayment;	
	$totalCollectionBalance = $CLSReports->numberFormat($totalCollectionBalance,2,1);
	$grandTotal = $CLSReports->numberFormat($grandTotal,2);
	$totalGrandDueAmt = $CLSReports->numberFormat($totalGrandDueAmt,2);
	$totalOverPayment = $CLSReports->numberFormat($totalOverPayment,2,1);
	$balnaceAfterDeduct = $CLSReports->numberFormat($balnaceAfterDeduct,2);

	//TOTALING OF PAYMENTS AND ADJUSTMENTS
	$total_other_payments=array_sum($arrPaymentAmt);
	$total_other_adjustments=array_sum($arrAdjustmentAmt);
	$grandPayments=	$total_ins_payments+$total_other_payments;
	$grandAdjustments=	$total_ins_adjustments+$total_other_adjustments;
	
	$td_total_ins_payments=$td_total_other_payments=$td_grand_payments=$td_total_ins_adjustments=$td_total_other_adjustments=$td_grand_adjustments='';
	$td_blank_payments=$td_blank_adjustments='';
	if($inc_payments==1){
		$td_total_ins_payments='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($total_ins_payments,2).'</td>';
		$td_total_other_payments='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($total_other_payments,2).'</td>';
		$td_grand_payments='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($grandPayments,2).'</td>';
		$td_blank_payments='<td align="right" width="'.$width.'" class="text_10b"></td>';
	}
	if($inc_adjustments==1){
		$td_total_ins_adjustments='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($total_ins_adjustments,2).'</td>';
		$td_total_other_adjustments='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($total_other_adjustments,2).'</td>';
		$td_grand_adjustments='<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$CLSReports->numberFormat($grandAdjustments,2).'</td>';
		$td_blank_adjustments='<td align="right" width="'.$width.'" class="text_10b"></td>';
	}

	//--- HEADER DATA -------
	$insProviderName = 'All Selected';
	if(trim($insuranceName) != ''){
		$insProviderName = $insComIdArr[$insuranceName];
	}

	$patientDueAmount = array_sum($patientDueAmountArr);
	
	$patientDueAmount = $CLSReports->numberFormat($patientDueAmount,2);
	if(trim($totalBalance) > 0 || $patientDueAmount > 0){		
		//--- CHANGE NUMBER FORMAT -----------
		$totalBalance = $CLSReports->numberFormat($totalBalance,2);
		$createdWidth = $headerWidth;// + 200;
		//--- GET PDF DATA -----

		$htmlPart='';
		//if($DateRangeFor=='dot'){
			$htmlPart='
			<tr bgcolor="#FFFFFF">
				<td style="text-align:right;" width="100" class="text_10b">Credit Amount :</td>
				<td bgcolor="#FFFFFF" colspan="'.$Tds.'"></td>
				<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$totalOverPayment.'</td>
				'.$td_blank_payments.'
				'.$td_blank_adjustments.'
			</tr>
			<tr><td class="total-row" colspan="'.$totalTd1.'"></td></tr>				
			<tr bgcolor="#FFFFFF">
				<td style="text-align:right;" width="100" class="text_10b">Deducting Credit from Grand Total :</td>
				<td bgcolor="#FFFFFF" colspan="'.$Tds.'"></td>
				<td style="text-align:right;" width="'.$width.'" class="text_10b">'.$balnaceAfterDeduct.'</td>
				'.$td_blank_payments.'
				'.$td_blank_adjustments.'
			</tr>
			<tr><td class="total-row" colspan="'.$totalTd1.'"></td></tr>';
		//}
		
		$title_payments=$title_adjustment='';
		if($inc_payments==1){
			$title_payments='<td class="text_b_w" width="$width" align="right">Payments</td>';
		}
		if($inc_adjustments==1){
			$title_adjustment='<td class="text_b_w" width="$width" align="right">Adjustment</td>';
		}		

		$pdfData .= <<<DATA
			$stylePDF
			<page backtop="12mm" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
				<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="750">
					<tr class="rpt_headers">
						<td class="rptbx1" width="250">
							A/R Aging - Insurance $summary_detail
						</td>
						
						<td class="rptbx2" width="250">
							Date ($Start_date - $End_date)
						</td>
						<td class="rptbx3" width="250">
							Created by: $op_name on $curDate
						</td>
					</tr>
				</table>
				<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="750">
					<tr>
						<td class="text_b_w" width="100" align="center">Description</td>
						$headerTd
						<td class="text_b_w" width="$width" align="right">Balance</td>
						$title_payments
						$title_adjustment
					</tr>
				</table>
			</page_header>
			<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="750">
				$pageContent
				<tr><td colspan="$totalTd1">&nbsp;</td></tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>
				<tr>
					<td align="right" width="100" class="text_10b">Ins. due : </td>
					$totalDueData
					<td align="right" width="$width" class="text_10b">$totalBalance</td>
					$td_total_ins_payments
					$td_total_ins_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>
				<tr>
					<td align="right" width="100" class="text_10b">Pt. due : </td>
					$patDueWdOutIns
					<td align="right" width="$width" class="text_10b">$totalPatBalAmt</td>
					$td_total_other_payments
					$td_total_other_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>
				<tr>
					<td align="right" width="100" class="text_10b">Grand Total : </td>
					$grandTdTotalData
					<td align="right" width="$width" class="text_10b">$totalGrandDueAmt</td>
					$td_grand_payments
					$td_grand_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>	
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" width="100" class="text_10b">Patients Under Collection :</td>
					<td bgcolor="#FFFFFF" colspan="$Tds"></td>
					<td style="text-align:right;" width="$width" class="text_10b">$totalCollectionBalance</td>
					$td_blank_payments
					$td_blank_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd1"></td></tr>				
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" width="100" class="text_10b">Grand Total :</td>
					<td bgcolor="#FFFFFF" colspan="$Tds"></td>
					<td style="text-align:right;" width="$width" class="text_10b">$grandTotal</td>
					$td_blank_payments
					$td_blank_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd1"></td></tr>				
				$htmlPart
			</table>
			</page>
DATA;
	
		//--- GET CSV DATA -----
		$csvFileContent .= <<<DATA
			$styleHTML
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050" >
				<tr class="rpt_headers">
					<td class="rptbx1" width="350">
						A/R Aging - Insurance $summary_detail
					</td>
					
					<td class="rptbx2" width="350">
						Date ($Start_date - $End_date)
					</td>
					<td class="rptbx3" width="350">
						Created by: $op_name on $curDate
					</td>
				</tr>
			</table>
			<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
				<tr>
					<td class="text_b_w" width="auto" style="text-align:center;">Description</td>
					$headerTd
					<td class="text_b_w" width="$width" style="text-align:right;">Balance</td>
					$title_payments
					$title_adjustment
				</tr>
				$csvPageContent
				<tr><td bgcolor="#FFFFFF" colspan="$totalTd1">&nbsp;</td></tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" class="text_10b">Ins. due : </td>
					$totalDueData
					<td style="text-align:right;" class="text_10b">$totalBalance</td>
					$td_total_ins_payments
					$td_total_ins_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" width="100" class="text_10b">Pt. due : </td>
					$patDueWdOutIns
					<td style="text-align:right;" width="$width" class="text_10b">$totalPatBalAmt</td>
					$td_total_other_payments
					$td_total_other_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" width="100" class="text_10b">Total : </td>
					$grandTdTotalData
					<td style="text-align:right;" width="$width" class="text_10b">$totalGrandDueAmt</td>
					$td_grand_payments
					$td_grand_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd1"></td>
				</tr>				
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" width="100" class="text_10b">Patients Under Collection :</td>
					<td bgcolor="#FFFFFF" colspan="$Tds"></td>
					<td style="text-align:right;" width="$width" class="text_10b">$totalCollectionBalance</td>
					$td_blank_payments
					$td_blank_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd1"></td></tr>				
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" width="100" class="text_10b">Grand Total :</td>
					<td bgcolor="#FFFFFF" colspan="$Tds"></td>
					<td style="text-align:right;" width="$width" class="text_10b">$grandTotal</td>
					$td_blank_payments
					$td_blank_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd"></td></tr>				
				$htmlPart
			</table>
DATA;
		}
		$finalContant = "<div id='html_data_div' style='overflow-y:auto'>$csvFileContent</div><br />";
		$finalContant .= "<div id='module_buttons' class='text-center ad_modal_footer'><button type=\"button\" class=\"btn btn-success\" onclick=\"opener.top.fmain.generate_pdf('p')\";>Print PDF</button>&nbsp;&nbsp;<button type=\"button\" class=\"btn btn-success\" onclick=\"getCSVData();\">Export CSV</button></div><form name=\"csvDownloadForm\" id=\"csvDownloadForm\" action=\"".$GLOBALS['webroot']."/interface/reports/downloadFile.php\" method =\"post\" > 
			<input type=\"hidden\" name=\"csv_text\" id=\"csv_text\">	
			<input type=\"hidden\" name=\"csv_file_name\" id=\"csv_file_name\" value=\"Insurance_AR.csv\" />
		</form>";
		$file = write_html($finalContant, "Insurance_ar_aging.html");	
		$file_path = str_ireplace($GLOBALS['fileroot'],$GLOBALS['webroot'],$file);
			echo '<div class="text-center alert alert-info">Result is populated in separate window</div>';	
	} else {
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
	}
	// END SUMMARY
}else{

if($grpby_block!='grpby_insurance'){
	include('insurance_ar_aging_grouping_detail.php');
}else{


$printAllDue = false;
$strQryPart = $strXML = $strLink = $slicedOut = $forDataURL = NULL;
//--- GET TOTAL COLUMN WIDTH ----
$column = ceil(($aging_to - $aging_start) / $aggingCycle);

if($All_due == true){
$column++;
}

$grandTotalArr = array();
$ptNoteArr = array();

$task_reminder_enc_arr = implode(', ', $task_reminder_enc_arr);
$getDetailsStr = "SELECT encounter_id, encComments,task_assign_for, DATE_FORMAT(reminder_date, '".get_sql_date_format()."') as reminder_date 
FROM paymentscomment 
WHERE encounter_id in ($task_reminder_enc_arr) 
$whr_due_date_chk
ORDER By commentId";
$getDetailsQry = imw_query($getDetailsStr);
while($getDetailsRow = imw_fetch_assoc($getDetailsQry)){
	$prov_rem_id = explode(',', $getDetailsRow['task_assign_for']);
	$reminder_date = ($getDetailsRow['reminder_date'] != "00-00-0000") ? $getDetailsRow['reminder_date'] : "";
	$tempProArr = array();
	foreach($prov_rem_id as $id){
		$pt_id_rem = $providerNameArr[$id];
		if(empty($pt_id_rem) == false) $tempProArr[] =  $pt_id_rem;
	}
	
	$remDate_Pro = "";
	if(count($tempProArr)){
		$remDate_Pro = "\n Assign for: \n".implode("\n", $tempProArr);
	}
	$arrEncNotes[$getDetailsRow['encounter_id']]['reminder_note']= $getDetailsRow['encComments'];
	$arrEncNotes[$getDetailsRow['encounter_id']]['reminder_date_provider']= $reminder_date.$remDate_Pro;
}

//---- GET TOTAL WIDTH FOR AUTO GENERATE TD --------
switch($column){
	case '1':
		$width = '280';
		//$pdfWidth = $colWidthAR / 2;
		$pdfWidth = $pdfHeaderWidth = $colHeaderWidthAR / 2;
	break;
	case '2':
		$width = '185';
		//$pdfWidth = $colWidthAR / 3;
		$pdfWidth = $pdfHeaderWidth = ($colHeaderWidthAR / 3) - 2;
	break;
	case '3':
		$width = '138';
		//$pdfWidth = $colWidthAR / 4;
		$pdfWidth = $pdfHeaderWidth = ($colHeaderWidthAR / 4) - 3;
	break;
	case '4':
		$width = '109';
		//$pdfWidth = $colWidthAR / 5;
		$pdfWidth = $pdfHeaderWidth = ($colHeaderWidthAR / 5) - 4;
	break;
	case '5':
		$width = '90';
		//$pdfWidth = $colWidthAR / 6;
		$pdfWidth = $pdfHeaderWidth = ($colHeaderWidthAR / 6) - 4;
	break;
	case '6':
		$width = '77';
		//$pdfWidth = $colWidthAR / 7;
		$pdfWidth = $pdfHeaderWidth = ($colHeaderWidthAR / 7) - 4;
	break;
	case '7':
		$width = '67';
		//$pdfWidth = $colWidthAR / 8;
		$pdfWidth = $pdfHeaderWidth = ($colHeaderWidthAR / 8) - 4;
	break;
}
$pdfHeaderWidth=$pdfHeaderWidth+7;
//--- TOTAL TD COLSPAN ---
$otherCols  = 2; 
if($inc_payments==1){$otherCols++;}
if($inc_adjustments==1){$otherCols++;}
$totalTd = $column + $otherCols;


$totalColTd = $totalTd - 1;
$cols1 = floor($totalTd / 3);
$cols = floor($totalTd / 2);
$cols2 = $totalTd- ($cols1+$cols1);

//Width to TDS
$totalWidth = 960;
$totaltdWidth = round(100/$totalTd,2);
$totaltdWidth.='%';
$btmcol	= $totalTd - 2;	
if($inc_payments==1){$btmcol--;}
if($inc_adjustments==1){$btmcol--;}

$pageContent = NULL;
$csvPageContent = NULL;
$self_ins_id_arr = array_keys($selfInsIdArr);

//MAKING OUTPUT DATA FOR CSV
$file_name="insurance_ar_aging_".time().".csv";
$csv_file_name= write_html("", $file_name);

//CSV FILE NAME
//$csv_file_name = "../../data/".PRACTICE_PATH."/UserId_".$_SESSION['authId']."/tmp/eid_status_".time().'.csv';
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');

for($in=0;$in<count($self_ins_id_arr);$in++){

	$insuranceId = $self_ins_id_arr[$in];
	$insuranceName = $selfInsIdArr[$insuranceId];
	if (strlen($insuranceName) > 20) {
		$insuranceName = substr($insuranceName, 0, 20) . '...';
	}
	
	$contact_name = $insCompDetails[$insuranceId]['contact_name'];
	$phone = $insCompDetails[$insuranceId]['phone'];
	
	//--- INSURANCE COMPANY NAME ----
	$pageContent .= <<<DATA
			<tr>
				<td class="text_b_w" colspan="$cols2" style="">$insuranceName</td>
				<td class="text_b_w" colspan="$cols1" style="">Contact Name: $contact_name</td>
				<td class="text_b_w" colspan="$cols1" style="">Phone#: $phone</td>
			</tr>
DATA;
	
	$csvPageContent .= <<<DATA
			<tr>
				<td class="text_b_w" colspan="$cols2" style="">$insuranceName</td>
				<td class="text_b_w" colspan="$cols1" style="">Contact Name: $contact_name</td>
				<td class="text_b_w" colspan="$cols1" style="">Phone#: $phone</td>
			</tr>
DATA;

	$patientData = $mainInsIdArr[$insuranceId];
	$patient_id_arr = array_keys($patientData);	
	for($i=0;$i<count($patient_id_arr);$i++){
		$patient_id = $patient_id_arr[$i];
		$encounterDataArr = $patientData[$patient_id];		
		$encounterIdArr = array();
		$pat_age = $patient_age_arr[$patient_id];
		
		if(count($encounterDataArr) > 0){
			$encounterIdArr = array_keys($encounterDataArr);
			//---  ALL ENCOUNTERS OF A SINGLE PATIENT --------
			$pageContent1 = NULL;
			$PDFpageContent1 = NULL;
			$payments=$adjustments=0;
			for($e=0;$e<count($encounterIdArr);$e++){
				$encounter_id = $encounterIdArr[$e];
				$data_of_service = NULL;
				//--- GET ALL AGGING REPORTS OF SINGLE ENCOUNTERS FOR A PATIENT ---
				$ecounter_data = NULL;
				$totalBalance = 0;
				$posted_date = NULL;
				for($a=$aging_start;$a<=$aging_to;$a++){
					$start = $a;
					$a = $a > 0 ? $a - 1 : $a;
					//---- ENCOUNTER DATE OF SERVICE ------
					$dataArr = $encounterDataArr[$encounter_id][$start];
					if($dataArr[0]['date_of_service']){
						$data_of_service = $dataArr[0]['date_of_service'];

					}
					//---- ENCOUNTER DATE OF POSTED ------
					if($dataArr[0]['postedDate']){
						$posted_date = $dataArr[0]['postedDate'];
					}
					
					//--- GET ENCOUNTER DUE AMOUNT -----------
					$insuranceDue = 0;
					for($en=0;$en<count($dataArr);$en++){
						$insuranceDue += preg_replace('/,/','',$dataArr[$en]['insuranceDue']);
					}
					
					$grandTotalArr[$start][] = $insuranceDue;
					
					//--- PATIENT ENCOUNTER BALANCE ----					
					$totalBalance += $insuranceDue;

					//--- NUMBER FORMAT -----
					$insuranceDue = $CLSReports->numberFormat($insuranceDue,2);
					$ecounter_data .= "<td class=\"text_10\" style=\"text-align:right; width:$totaltdWidth\" >$insuranceDue</td>";
					$a += $aggingCycle;
				}
				
				if($All_due == true){
					//---- ENCOUNTER DATE OF SERVICE ------
					$dataArr = $encounterDataArr[$encounter_id][181];
					if($dataArr[0]['date_of_service']){
						//By Karan
						//$ServiceDate = $dataArr[0]['date_of_service'];
						//$dd1 = explode('-',$ServiceDate);
						//$data_of_service = date(''.phpDateFormat().'', mktime(0,0,0, date($dd1[0]), date($dd1[1]), date($dd1[2])));
						
						// By Jaswant Sir // 
						$data_of_service = $dataArr[0]['date_of_service'];
						
					}
					
					if($dataArr[0]['postedDate']){
						//By Karan
						//$postedDate = $dataArr[0]['postedDate'];
						//$dd = explode('-',$postedDate);
						//$posted_date = date(''.phpDateFormat().'', mktime(0,0,0, date($dd[0]), date($dd[1]), date($dd[2])));
						
						// By Jaswant Sir //
						$posted_date = $dataArr[0]['postedDate'];
						
					}
					//--- GET ENCOUNTER DUE AMOUNT -----------
					$insuranceDue = 0;
					for($en=0;$en<count($dataArr);$en++){
						$insuranceDue += preg_replace('/,/','',$dataArr[$en]['insuranceDue']);
					}
					
					$grandTotalArr[181][] = $insuranceDue;
					
					//--- PATIENT ENCOUNTER BALANCE ----					
					$totalBalance += $insuranceDue;

					//--- NUMBER FORMAT -----
					$insuranceDue = $CLSReports->numberFormat($insuranceDue,2);
					$ecounter_data .= <<<DATA
						<td class="text_10" style="text-align:right; width:$totaltdWidth">$insuranceDue</td>
DATA;
				}
				
				//--- NUMBER FORMAT -----
				$totalBalance = $CLSReports->numberFormat($totalBalance,2);
				$pageContent1 .= <<<DATA
					<tr bgcolor="#FFFFFF">
						<td class="text_10" style="text-align:left;">$data_of_service - $encounter_id &nbsp; ($posted_date)</td>
						<td class="text_10" style="text-align:right;" width="$totaltdWidth">$totalBalance</td>
					</tr>
DATA;

				$PDFpageContent1 .= <<<DATA
					<tr bgcolor="#FFFFFF">
						<td class="text_10" style="text-align:left;">$data_of_service - $encounter_id &nbsp; ($posted_date)</td>
						<td class="text_10" style="text-align:right;" width="$totaltdWidth">$totalBalance</td>
					</tr>
DATA;
			}
		}
		
		//--- PATIENT NAME ----
		$patientName = $patientNameArr[$patient_id];
		
		//--- PDF DEATILS DATA ----
		$pageContent .= <<<DATA
			<tr>
				<td colspan="$totalTd" class="text_10b">$patientName ($pat_age)</td>
			</tr>
			<tr>
				<td colspan="$totalTd" height="5px" class="text_10b"></td>
			</tr>
			$pageContent1	
			<tr>
				<td colspan="$totalTd" bgcolor="#000000" height="1px"></td>
			</tr>
			<tr>
				<td colspan="$totalTd" class="text_10b">&nbsp;</td>
			</tr>			
DATA;
		
		//--- CSV DEATILS DATA ----	
		$url= $GLOBALS['rootdir']."/reports/set_session.php?patient=".$patient_id."&file_name=".$result_file_name;
		$csvPageContent .= <<<DATA
			<tr bgcolor="#FFFFFF">
				<td colspan="$totalTd" class="text_10b">$patientName ($pat_age)</td>
			</tr>
			$pageContent1
			<tr bgcolor="#FFFFFF"><td colspan="$totalTd">&nbsp;</td></tr>	
DATA;
	}
}
//$a=0;
for($a=$aging_start;$a<=$aging_to;$a++){
	$start = $a;
	$a = ($a > 0) ? ($a - 1) : $a;
	$end = ($a) + $aggingCycle;
	$headerTd .= <<<DATA
		<td class="text_b_w" style="text-align:right; width:$totaltdWidth;">
			$start - $end
		</td>
DATA;
	
	//FOR CSV
	$arrTitles[]=$start." - ".$end;

	$a += $aggingCycle;
}
if($All_due == true){
	$headerTd .= <<<DATA
		<td class="text_b_w" style="text-align:right; width:$totaltdWidth;">181+</td>
DATA;
	$insDue = 0;
	if(count($grandTotalArr[181])>0){
		$insDue = array_sum($grandTotalArr[181]);
	}
	
	//FOR CSV
	$arrTitles[]="181+";
}

//FOR CSV
$arrFinal=array();
$arrFinal[]="A/R Aging - Insurance ".$summary_detail;
$arrFinal[]="Date (".$Start_date." - ".$End_date.")";
$arrFinal[]="Created by: ".$op_name." on ".$curDate;
fputcsv($fp,$arrFinal, ",","\"");			

$arrFinal=array();
$arrFinal[]="Patient Name-ID";
$arrFinal[]="DOB";
$arrFinal[]="DOS";
$arrFinal[]="Posted Date";
$arrFinal[]="Encounter ID";
foreach($arrTitles as $title){
	$arrFinal[]=$title;
}
$arrFinal[]="Balance";
if($inc_payments==1){
	$arrFinal[]="Payments";
}
if($inc_adjustments==1){
	$arrFinal[]="Adjustment";
}
$arrFinal[]="Insurance";
$arrFinal[]="Policy No.";
$arrFinal[]="Insurance Contact No.";
$arrFinal[]="Procedures";
if($accNotes){	
	$arrFinal[]="A/C Notes";
	$arrFinal[]="Next Follow Up Date";
}
if($inc_appt_detail==1){
	$arrFinal[]='Appointment Date Time';
	$arrFinal[]='Appointment Procedure';
}
fputcsv($fp,$arrFinal, ",","\"");	

$ins_com_id_arr = array_keys($insComIdArr);
for($in=0;$in<count($ins_com_id_arr);$in++){

	$insuranceId = $ins_com_id_arr[$in];
	$insuranceName = $insComIdArr[$insuranceId];
	if (strlen($insuranceName) > 20) {
		$insuranceName = substr($insuranceName, 0, 20) . '...';
	}
	$contact_name = $insCompDetails[$insuranceId]['contact_name'];
	$phone = $insCompDetails[$insuranceId]['phone'];
	
	//--- INSURANCE COMPANY NAME ----
	$pageContent .= <<<DATA
			<tr>
				<td class="text_b_w" colspan="$cols2" style="">$insuranceName</td>
				<td class="text_b_w" colspan="$cols1" style="">Contact Name: $contact_name</td>
				<td class="text_b_w" colspan="$cols1" style="">Phone#: $phone</td>
			</tr>
DATA;
	
	$csvPageContent .= <<<DATA
			<tr>
				<td class="text_b_w" colspan="$cols2" style="">$insuranceName</td>
				<td class="text_b_w" colspan="$cols1" style="">Contact Name: $contact_name</td>
				<td class="text_b_w" colspan="$cols1" style="">Phone#: $phone</td>
			</tr>
DATA;

	$patientData = $mainInsIdArr[$insuranceId];
	
	$patient_id_arr = array_keys($patientData);	
	for($i=0;$i<count($patient_id_arr);$i++){
		$patient_id = $patient_id_arr[$i];
		$encounterDataArr = $patientData[$patient_id];		
		$encounterIdArr = array();
		$patientName = $patientNameArr[$patient_id];
		$pat_age = $patient_age_arr[$patient_id];
		if(count($encounterDataArr) > 0){
			$encounterIdArr = array_keys($encounterDataArr);
			//---  ALL ENCOUNTERS OF A SINGLE PATIENT --------
			$pageContent1 = NULL;
			$PDFpageContent1 = NULL;
			$arrProcCode=array();
			for($e=0;$e<count($encounterIdArr);$e++){
				$arrProcCode=array();
				$arr=array();
				$encounter_id = $encounterIdArr[$e];
				$data_of_service = NULL;
				//--- GET ALL AGGING REPORTS OF SINGLE ENCOUNTERS FOR A PATIENT ---
				$ecounter_data = NULL;
				$totalBalance = $payments=$adjustments=0;
				$posted_date = NULL;
				for($a=$aging_start;$a<=$aging_to;$a++){
					$start = $a;
					$a = $a > 0 ? $a - 1 : $a;
					//---- ENCOUNTER DATE OF SERVICE ------
					$dataArr = $encounterDataArr[$encounter_id][$start];
					if($dataArr[0]['date_of_service']){
						$data_of_service = $dataArr[0]['date_of_service'];
					}
					//---- ENCOUNTER DATE OF POSTED ------
					if($dataArr[0]['postedDate']){
						$posted_date = $dataArr[0]['postedDate'];
					}
					
					//--- GET ENCOUNTER DUE AMOUNT -----------
					$insuranceDue = 0;
					for($en=0;$en<count($dataArr);$en++){
						$insuranceDue += preg_replace('/,/','',$dataArr[$en]['insuranceDue']);
						
						if($dataArr[$en]['insuranceDue']>0){
							$procCode=$arrAllCPTCodes[$dataArr[$en]['procCode']];
							$arrProcCode[$procCode]= $procCode; 
						}

						//PAYMENTS
						$chgDetId=$dataArr[$en]['charge_list_detail_id'];
						if($arrPaymentAmt[$chgDetId]){
							$payments+=$arrPaymentAmt[$chgDetId];
							unset($arrPaymentAmt[$chgDetId]);
						}
						//ADJUSTMENTS
						if($arrAdjustmentAmt[$chgDetId] || $normalWriteOffAmt[$chgDetId]){
							$adjustments+=$arrAdjustmentAmt[$chgDetId]+$normalWriteOffAmt[$chgDetId];
							if($arrAdjustmentAmt[$chgDetId])unset($arrAdjustmentAmt[$chgDetId]);
							if($normalWriteOffAmt[$chgDetId])unset($normalWriteOffAmt[$chgDetId]);
						}							
					}
					
					$grandTotalArr[$start][] = $insuranceDue;

				
					
					//--- PATIENT ENCOUNTER BALANCE ----					
					$totalBalance += $insuranceDue;

					//--- NUMBER FORMAT -----
					$insuranceDue = $CLSReports->numberFormat($insuranceDue,2);
					$insuranceDue=(empty($insuranceDue)==true)? " " : $insuranceDue;
					$ecounter_data .= <<<DATA
						<td class="text_10" valign="top" style="text-align:right; width:$totaltdWidth">$insuranceDue</td>
DATA;
					//FOR CSV
					$arr[]=$insuranceDue;
					$a += $aggingCycle;
				}
				
				if($All_due == true){
					//---- ENCOUNTER DATE OF SERVICE ------
					$dataArr = $encounterDataArr[$encounter_id][181];
					if($dataArr[0]['date_of_service']){
						$data_of_service = $dataArr[0]['date_of_service'];
					}
					
					if($dataArr[0]['postedDate']){
						$posted_date = $dataArr[0]['postedDate'];
					}
					//--- GET ENCOUNTER DUE AMOUNT -----------
					$insuranceDue = 0;
					for($en=0;$en<count($dataArr);$en++){
						$insuranceDue += preg_replace('/,/','',$dataArr[$en]['insuranceDue']);
						
						if($dataArr[$en]['insuranceDue']>0){
							$procCode=$arrAllCPTCodes[$dataArr[$en]['procCode']];
							$arrProcCode[$procCode]= $procCode; 
						}

						//PAYMENTS
						$chgDetId=$dataArr[$en]['charge_list_detail_id'];
						if($arrPaymentAmt[$chgDetId]){
							$payments+=$arrPaymentAmt[$chgDetId];
							unset($arrPaymentAmt[$chgDetId]);
						}
						//ADJUSTMENTS
						if($arrAdjustmentAmt[$chgDetId] || $normalWriteOffAmt[$chgDetId]){
							$adjustments+=$arrAdjustmentAmt[$chgDetId]+$normalWriteOffAmt[$chgDetId];
							if($arrAdjustmentAmt[$chgDetId])unset($arrAdjustmentAmt[$chgDetId]);
							if($normalWriteOffAmt[$chgDetId])unset($normalWriteOffAmt[$chgDetId]);
						}	
					}
					
					$grandTotalArr[181][] = $insuranceDue;

	

					//--- PATIENT ENCOUNTER BALANCE ----					
					$totalBalance += $insuranceDue;
					
					//--- NUMBER FORMAT -----
					$insuranceDue = $CLSReports->numberFormat($insuranceDue,2);
					$ecounter_data .= <<<DATA
						<td class="text_10" valign="top" style="text-align:right; width:$totaltdWidth">$insuranceDue</td>
DATA;
					//FOR CSV
					$arr[]=$insuranceDue;
				}
				$procCode = implode(', ', $arrProcCode);
				$procCodepdf = wordwrap($procCode, 40, "<br>\n", true);
				
				$arrNote = $arrEncNotes[$encounter_id]['reminder_note'];
				$encPDFComments = wordwrap($arrNote, 40, "<br>\n", true);
				$arrDateAssinFor = $arrEncNotes[$encounter_id]['reminder_date_provider'];
				 
				
				//PAYMENTS & ADJUSTMENTS
				$td_payments=$td_adjustments='';
				if($inc_payments==1){
					$total_ins_payments+=$payments;
					$arrGroupTotal['payments']+= $payments;
					$td_payments='<td class="text_10" valign="top" style="text-align:right; width:'.$totaltdWidth.'">'.$CLSReports->numberFormat($payments,2).'</td>';
					
				}
				if($inc_adjustments==1){
					$total_ins_adjustments+=$adjustments;
					$arrGroupTotal['adjustments']+= $adjustments;
					$td_adjustments='<td class="text_10" valign="top" style="text-align:right; width:'.$totaltdWidth.'">'.$CLSReports->numberFormat($adjustments,2).'</td>';
				}

				$acc_notes = ""; 
				$acc_Cols = 2;
				$acc_Cols= ($accNotes)? $acc_Cols+2: $acc_Cols;
				$acc_Cols= ($inc_appt_detail==1)? $acc_Cols+2: $acc_Cols;
				$acc_width = round(100/$acc_Cols, 2); 

				if($accNotes){	
					$acc_notes = "<td class='text_10'  valign='top' style='text-align:left; width:$acc_width%'>$encPDFComments</td>
					<td class='text_10' valign='top' style='text-align:left; width:$acc_width%'>$arrDateAssinFor</td>";
					
					$accHdr = "<td class='text_b_w' style='text-align:left; width:$acc_width%'>A/C Notes</td>
					<td class='text_b_w' style='text-align:left; width:$acc_width%'>Next Follow Up Date</td>";
					
				}
				
				//APPOINTMENT DETAILS
				$appt_details=$apptHdr='';
				$appt_date_time=$arrApptDetails[$encounter_id]['appt_date']." ".$arrApptDetails[$encounter_id]['appt_time'];
				$appt_procedure=$arrAllProcedures[$arrApptDetails[$encounter_id]['appt_procedure']];
				if($inc_appt_detail==1){
					$appt_details = "<td class='text_10'  valign='top' style='text-align:left;'>".$appt_date_time."</td>
					<td class='text_10' valign='top' style='text-align:left;'>".$appt_procedure."</td>";

					$apptHdr = "<td class='text_b_w' style='text-align:left; width:$acc_width%'>Appointent Date Time</td>
					<td class='text_b_w' style='text-align:left; width:$acc_width%'>Appointment Procedure</td>";
				}
				 
				//--- NUMBER FORMAT -----
				$totalBalance = $CLSReports->numberFormat($totalBalance,2);
				$pageContent1 .= <<<DATA
					<tr bgcolor="#FFFFFF">
						<td class="text_10" valign="top" style="text-align:left; width:$totaltdWidth">$data_of_service - $encounter_id<br>($posted_date)</td>
						$ecounter_data
						<td class="text_10" valign="top" style="text-align:right; width:$totaltdWidth">$totalBalance</td>
						$td_payments
						$td_adjustments
					</tr>
					<tr>
						<td colspan="$totalTd" class="hide" data-id="$patient_id">
							<table class="rpt rpt_table rpt_table-bordered rpt_padding" >	
								<tr>
									<td class="text_b_w" style="text-align:left; width:$acc_width%">Policy No.</td>
									<td class="text_b_w" style="text-align:left; width:$acc_width%">Procedures</td>
									$accHdr
									$apptHdr
								</tr>
								<tr bgcolor="#FFFFFF">
									<td class="text_10" valign="top" style="text-align:left;">$arrEncInsPolicyNos[$encounter_id]</td>
									<td class="text_10" valign="top" style="text-align:left;">$procCodepdf</td>
									$acc_notes
									$appt_details
								</tr>
							</table>
						</td>
					</tr>
DATA;

				$PDFpageContent1 .= <<<DATA
					<tr>
						<td class="text_10" valign="top" style="text-align:left; width:$totaltdWidth">$data_of_service - $encounter_id<br> ($posted_date)</td>
						$ecounter_data
						<td class="text_10" valign="top" style="text-align:right; width:$totaltdWidth">$totalBalance</td>
						$td_payments
						$td_adjustments
					</tr>
					<tr>
						<td colspan="$totalTd">
							<table class="rpt rpt_table rpt_table-bordered rpt_padding">	
								<tr>
									<td class="text_b_w" style="text-align:left; width:$acc_width%">Policy No.</td>
									<td class="text_b_w" style="text-align:left; width:$acc_width%">Procedures</td>
									$accHdr
									$apptHdr
								</tr>
								<tr bgcolor="#FFFFFF">
									<td class="text_10" valign="top" style="text-align:left;">$arrEncInsPolicyNos[$encounter_id]</td>
									<td class="text_10" valign="top" style="text-align:left;">$procCodepdf</td>
									$acc_notes
									$appt_details
								</tr>
							</table>
						</td>
					</tr>					
DATA;
				//FOR CSV
				$arrPatientRows=array();
				$arrPatientRows[]=$patientName;
				$arrPatientRows[]=$pat_age;
				$arrPatientRows[]=$data_of_service;
				$arrPatientRows[]=$posted_date;
				$arrPatientRows[]=$encounter_id;
				foreach($arr as $balAmt){
					$arrPatientRows[]=$balAmt;	
				}
				$arrPatientRows[]=$totalBalance;
				if($inc_payments==1){
					$arrPatientRows[]=$payments;
				}
				if($inc_adjustments==1){
					$arrPatientRows[]=$adjustments;
				}				
				$arrPatientRows[]=str_replace("\n", " ",$insuranceName);
				$arrPatientRows[]=str_replace("\n", " ",$arrEncInsPolicyNos[$encounter_id]);
				$arrPatientRows[]=$phone;
				$arrPatientRows[]=str_replace("\n", " ",$procCode);
				if($accNotes){	
					$arrPatientRows[]=str_replace("\n", " ",$arrNote);
					$arrPatientRows[]=$arrDateAssinFor;
				}
				if($inc_appt_detail==1){
					$arrPatientRows[]=$appt_date_time;
					$arrPatientRows[]=$appt_procedure;
				}
				fputcsv($fp,$arrPatientRows, ",","\"");
			}
		}
		
		
		//--- PDF DEATILS DATA ----
		$pageContent .= <<<DATA
			<tr>
				<td colspan="$totalTd" class="text_10b">$patientName ($pat_age)</td>
			</tr>
			<tr>
				<td colspan="$totalTd" class="text_10b" height="5px"></td>
			</tr>
			$PDFpageContent1	
			<tr>
				<td colspan="$totalTd" bgcolor="#000000" height="1px"></td>
			</tr>
			<tr>
				<td colspan="$totalTd" class="text_10b">&nbsp;</td>
			</tr>			
DATA;
		
		//--- CSV DEATILS DATA ----	
		$csvPageContent .= <<<DATA
			<tr bgcolor="#FFFFFF">
				<td colspan="$totalTd" class="text_10b" style="width:300px;"><a href="javascript:void(0);" onClick="loadRptPatient($patient_id)" class='text_10b_purpule'>$patientName - $pat_age</a><span class="pull-right" onclick="showDetails(this);" data-id="$patient_id"><a href="#">
				<span class="glyphicon glyphicon-chevron-down"></span></a></span></td>
			</tr>
			$pageContent1
			<tr bgcolor="#FFFFFF"><td colspan="$totalTd">&nbsp;</td></tr>	
DATA;
	}
}

//--- CREATE DATA FOR HTML TO PDF FILE -----
if($pageContent){
	$arrTotDues=array();
	$arrTotDues[]="Total :";
	$arrTotDues[]="";
	$arrTotDues[]="";
	$arrTotDues[]="";
	$arrTotDues[]="";
	
	//-- GET OPERATOR NAME ----
	$op_name = strtoupper($_SESSION['authProviderName']);
	//--- GET HEADER LABELS -------	
	$totalBalance = 0;
	for($a=$aging_start;$a<=$aging_to;$a++){
		$start = $a;
		$a = $a > 0 ? $a - 1 : $a;
		$end = ($a) + $aggingCycle;
		
		$insDue = NULL;
		if(count($grandTotalArr[$start])>0){
			$insDue = array_sum($grandTotalArr[$start]);
		}

		$totalBalance += $insDue;	
		$insDue = $CLSReports->numberFormat($insDue,2);		
		
		$totalDueData .= <<<DATA
			<td class="text_10b" style="text-align:right;">
				$insDue
			</td>
DATA;

		//FOR CSV
		$arrTotDues[]=$insDue;

		$a += $aggingCycle;
	}
	
	//--- GET HEADER LBEL FOR AGGING 181+ -------- 
	if($All_due == true){
		$insDue = 0;
		if(count($grandTotalArr[181])>0){
			$insDue = array_sum($grandTotalArr[181]);
		}
		
		$totalBalance += $insDue;	
		$insDue = $CLSReports->numberFormat($insDue,2);		
		$totalDueData .= <<<DATA
			<td class="text_10b" style="text-align:right;">
				$insDue
			</td>
DATA;
		//FOR CSV
		$arrTotDues[]=$insDue;

	}
	
	//--- HEADER DATA -------
	$insProviderName = 'All Selected';
	if(trim($insuranceName) != ''){
		$insProviderName = $insComIdArr[$insuranceName];
	}

	//FOR CSV
	$totCols=$totalTd+2;
	
	if(trim($totalBalance) > 0){
		
		//--- CHANGE NUMBER FORMAT -----------
		$grandTotal = $totalBalance + $totalCollectionInsBalance;
		$balnaceAfterDeduct = $grandTotal - $totalOverPayment;	
		$totalCollectionInsBalance = $CLSReports->numberFormat($totalCollectionInsBalance,2,1);
		$totalBalance = $CLSReports->numberFormat($totalBalance,2);
		$grandTotal = $CLSReports->numberFormat($grandTotal,2);
		$totalOverPayment = $CLSReports->numberFormat($totalOverPayment,2,1);
		$balnaceAfterDeduct = $CLSReports->numberFormat($balnaceAfterDeduct,2);

		//TOTALING OF PAYMENTS AND ADJUSTMENTS
		$title_payments=$title_adjustment=$td_total_ins_payments=$td_total_other_payments=$td_grand_payments=$td_total_ins_adjustments=$td_total_other_adjustments=$td_grand_adjustments='';
		$td_blank_payments=$td_blank_adjustments='';
		if($inc_payments==1){
			$title_payments='<td class="text_b_w" style="text-align:right; width:'.$totaltdWidth.'">Payments</td>';
			$td_total_ins_payments='<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">'.$CLSReports->numberFormat($total_ins_payments,2).'</td>';
			$td_grand_payments='<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">'.$CLSReports->numberFormat($grandPayments,2).'</td>';
			$td_blank_payments='<td align="right" style="text-align:right;  width:'.$totaltdWidth.'" class="text_10b"></td>';
		}
		if($inc_adjustments==1){
			$title_adjustment='<td class="text_b_w" style="text-align:right; width:'.$totaltdWidth.'">Adjustment</td>';
			$td_total_ins_adjustments='<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">'.$CLSReports->numberFormat($total_ins_adjustments,2).'</td>';
			$td_grand_adjustments='<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">'.$CLSReports->numberFormat($grandAdjustments,2).'</td>';
			$td_blank_adjustments='<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b"></td>';
		}		
		
		//$totalTd1 = $totalTd + 1;
		$htmlPart='';
		if($DateRangeFor=='dot'){
			$htmlPart='
			<tr bgcolor="#FFFFFF">
				<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">Credit Amount : </td>
				<td colspan="'.$btmcol.'" bgcolor="#FFFFFF"></td>
				<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">'.$totalOverPayment.'</td>
				'.$td_blank_payments.'
				'.$td_blank_adjustments.'
			</tr>
			<tr><td class="total-row" colspan="'.$totalTd.'"></td></tr>
			<tr bgcolor="#FFFFFF">
				<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">Deducting Credit from Grand total : </td>
				<td colspan="'.$btmcol.'" bgcolor="#FFFFFF"></td>
				<td style="text-align:right; width:'.$totaltdWidth.'" class="text_10b">'.$balnaceAfterDeduct.'</td>
				'.$td_blank_payments.'
				'.$td_blank_adjustments.'				
			</tr>
			<tr><td class="total-row" colspan="'.$totalTd.'"></td></tr>';

			//FOR CSV
			$arrCredits[]="Credit Amount:";
			for($i=0;$i<$totCols;$i++){
				$arrCredits[]="";
			}
			$arrCredits[]=$totalOverPayment;

			$arrCreditsDeduct[]="Deducting Credit from Grand total :";
			for($i=0;$i<$totCols;$i++){
				$arrCreditsDeduct[]="";
			}
			$arrCreditsDeduct[]=$balnaceAfterDeduct;
		}
		
		$pdfData .= <<<DATA
			$stylePDF
			<page backtop="12mm" backbottom="7mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center; width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
				<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%;" >
					<tr class="rpt_headers">
						<td class="rptbx1" style="width:33%">
							A/R Aging - Insurance $summary_detail
						</td>
						
						<td class="rptbx2" style="width:33%">
							Date ($Start_date - $End_date)
						</td>
						<td class="rptbx3" style="width:33%">
							Created by: $op_name on $curDate
						</td>
					</tr>
				</table>
				<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%;" >
					<tr>
						<td class="text_b_w" style="text-align:center; width:$totaltdWidth">Description</td>
						$headerTd
						<td class="text_b_w" style="text-align:right; width:$totaltdWidth">Balance</td>
						$title_payments
						$title_adjustment
					</tr>
				</table>
			</page_header>
			<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%;" >
				$pageContent
				<tr>
					<td class="total-row" colspan="$totalTd"></td>
				</tr>
				<tr>
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">Total: </td>
					$totalDueData
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">$totalBalance</td>
					$td_total_ins_payments
					$td_total_ins_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">Patients Under Collection: </td>
					<td colspan="$btmcol" bgcolor="#FFFFFF"></td>
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">$totalCollectionInsBalance</td>
					$td_blank_payments
					$td_blank_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd"></td></tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">Grand Total: </td>
					<td colspan="$btmcol" bgcolor="#FFFFFF"></td>
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">$grandTotal</td>
					$td_total_ins_payments
					$td_total_ins_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd"></td></tr>
				$htmlPart
			</table>
			</page>
DATA;
		
		//--- CSV FILE DATA ----
		$csvFileContent .= <<<DATA
			$styleHTML
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050">
				<tr class="rpt_headers">
					<td class="rptbx1" width="350">
						A/R Aging - Insurance $summary_detail
					</td>
					
					<td class="rptbx2" width="350">
						Date ($Start_date - $End_date)
					</td>
					<td class="rptbx3" width="350">
						Created by: $op_name on $curDate
					</td>
				</tr>
			</table>
			<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
				<tr>
					<td class="text_b_w" style="text-align:center; width:$totaltdWidth">Description</td>
					$headerTd
					<td class="text_b_w" style="text-align:right; width:$totaltdWidth">Balance</td>
					$title_payments
					$title_adjustment
				</tr>
				$csvPageContent
				<tr bgcolor="#FFFFFF"><td colspan="$totalTd">&nbsp;</td></tr>
				<tr>
					<td class="total-row" colspan="$totalTd"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right;" class="text_10b">Total : </td>
					$totalDueData
					<td style="text-align:right;" class="text_10b">$totalBalance</td>
					$td_total_ins_payments
					$td_total_ins_adjustments
				</tr>
				<tr>
					<td class="total-row" colspan="$totalTd"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">Patients Under Collection : </td>
					<td colspan="$btmcol" class="text_10b"></td>
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">$totalCollectionInsBalance</td>
					$td_blank_payments
					$td_blank_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd"></td></tr>
				<tr bgcolor="#FFFFFF">
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">Grand Total : </td>
					<td colspan="$btmcol" class="text_10b"></td>
					<td style="text-align:right; width:$totaltdWidth" class="text_10b">$grandTotal</td>
					$td_total_ins_payments
					$td_total_ins_adjustments
				</tr>
				<tr><td class="total-row" colspan="$totalTd"></td></tr>
				$htmlPart
			</table>
DATA;

			//FOR CSV
			$arrTotDues[]=$totalBalance;
			if($inc_payments==1){
				$arrTotDues[]=$CLSReports->numberFormat($total_ins_payments,2);
			}
			if($inc_adjustments==1){
				$arrTotDues[]=$CLSReports->numberFormat($total_ins_adjustments,2);
			}
			fputcsv($fp,$arrTotDues, ",","\""); //TOTAL DUES
			//PAT UNDER COLLECTION
			$arrPatUnderCol=array();
			$arrPatUnderCol[]="Patients Under Collection :";
			for($i=0;$i< $totCols; $i++){
				$arrPatUnderCol[]="";
			}
			$arrPatUnderCol[]=$totalCollectionInsBalance;
			fputcsv($fp,$arrPatUnderCol, ",","\"");
			//GRAND TOTAL	
			$arrGrand=array();
			$arrGrand[]="Grand Total :";
			for($i=0;$i< $totCols; $i++){
				$arrGrand[]="";
			}
			$arrGrand[]=$grandTotal;
			if($inc_payments==1){
				$arrGrand[]=$CLSReports->numberFormat($total_ins_payments,2);
			}
			if($inc_adjustments==1){
				$arrGrand[]=$CLSReports->numberFormat($total_ins_adjustments,2);
			}			
			fputcsv($fp,$arrGrand, ",","\"");
			
			fputcsv($fp,$arrCredits, ",","\""); //CREDIT AMOUNT
			fputcsv($fp,$arrCreditsDeduct, ",","\""); //CREDIT DEDUCTING
}
fclose($fp);

$finalContant = "<div id='html_data_div' style='overflow-y:auto'>$csvFileContent</div><br />";
$finalContant .= "<div id='module_buttons' class='text-center ad_modal_footer'><button type=\"button\" class=\"btn btn-success\" onclick=\"opener.top.fmain.generate_pdf('l')\";>Print PDF</button>";
if($summary_detail=='summary'){
	$finalContant .= "&nbsp;&nbsp;<button type=\"button\" class=\"btn btn-success\" onclick=\"getCSVData();\">Export CSV</button>";
}else{
	$finalContant .= "&nbsp;&nbsp;<button type=\"button\" class=\"btn btn-success\" onclick=\"download_csv();\">Export CSV</button>";
}
$finalContant .= "</div><form name=\"csvDownloadForm\" id=\"csvDownloadForm\" action=\"".$GLOBALS['webroot']."/interface/reports/downloadFile.php\" method =\"post\" > 
<input type=\"hidden\" name=\"csv_text\" id=\"csv_text\"><input type=\"hidden\" name=\"csv_file_name\" id=\"csv_file_name\" value=\"Insuarnce_AR.csv\" /></form>";
$finalContant.="<form name=\"csvDirectDownloadForm\" id=\"csvDirectDownloadForm\" action=\"".$GLOBALS['webroot']."/interface/reports/downloadCSV.php\" method =\"post\">"; 
$finalContant.="<input type=\"hidden\" name=\"file_format\" id=\"file_format\" value=\"csv\">";
$finalContant.="<input type=\"hidden\" name=\"zipName\" id=\"zipName\" value=\"\">";
$finalContant.="<input type=\"hidden\" name=\"file\" id=\"file\" value=\"".$csv_file_name."\" /></form>";

	$file = write_html($finalContant, "insurance_ar_aging.html");	
	$file_path = str_ireplace($GLOBALS['fileroot'],$GLOBALS['webroot'],$file);
	if($callFrom != 'scheduled'){
		echo '<div class="text-center alert alert-info">Result is populated in separate window</div>';
	}
	
	} else {
		if($callFrom != 'scheduled'){
			echo '<div class="text-center alert alert-info">No Record Found.</div>';
		}
	}
}	
}
$file_location = write_html($pdfData);

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
		
		$qry="Select id FROM reports_searches WHERE report_name='".$search_name."' AND report='ar_aging_insurance_criteria'";
		$rs=imw_query($qry);
		if(imw_num_rows($rs)<=0 || (imw_num_rows($rs)>0 && $qryPart=='Update')){
			$qry="$qryPart reports_searches SET uid='".$_SESSION['authId']."', report='ar_aging_insurance_criteria',
			search_data='".addslashes($varCriteria)."', saved_date='".date('Y-m-d H:i:s')."' ".$fieldPart.$qryWhere;
			imw_query($qry);
		}
	}
}
//---------------------

if($pageContent){
?>
<script>
	var url ='<?php echo $file_path; ?>';
	var number = 1 + Math.floor(Math.random() * 6);
	//url += '&num='+number;
	var n = url.substring(url.lastIndexOf('/')+1);
		temp_n= n.split('.');
		n = temp_n[0];
		if(top.arr_opened_popups[n] && (top.arr_opened_popups[n].closed == false)){
			var n = top.arr_opened_popups[n].location.href=url;
			n.focus();
		}else{
			top.popup_win('<?php echo $file_path; ?>','resizable=1');
		}	
</script>
<?php } ?>
