<?php
if($rvu=='1'){
	//GET RVU VALS FROM POLICIES
	$rs=imw_query("Select work_gpci, bugdet_neu_adj_gpci, practice_expense_gpci, malpractice_gpci, conversion_factor FROM copay_policies");
	$res  =imw_fetch_array($rs);
	$work_gpci  = $res['work_gpci'];
	$bugdet_neu_adj_gpci  = $res['bugdet_neu_adj_gpci'];
	$practice_expense_gpci  = $res['practice_expense_gpci'];
	$malpractice_gpci  = $res['malpractice_gpci'];
	$convFactor	= $res['conversion_factor'];
	unset($rs);
}

//--- FETCH ALL ENCOUNTERS WITH SEARCH CERTARI -----
$qry = "select patient_charge_list.patient_id,
		patient_charge_list_details.procCharges * 
		patient_charge_list_details.units as totalAmt,
		patient_charge_list_details.coPayAdjustedAmount,
		patient_charge_list.totalBalance,
		patient_charge_list_details.charge_list_detail_id,
		date_format(patient_charge_list.date_of_service,'".$dateFormat."')
		as date_of_service,pos_facilityies_tbl.facilityPracCode,
		patient_charge_list.encounter_id,patient_charge_list.facility_id,
		patient_charge_list.primary_provider_id_for_reports  as 'primaryProviderId',
		patient_charge_list_details.last_pri_paid_date,
		patient_charge_list_details.procCode,
		patient_charge_list.lastPaymentDate,patient_charge_list_details.units,
		patient_charge_list_details.overPaymentForProc,
		patient_charge_list_details.procCharges,
		patient_charge_list_details.approvedAmt,
		patient_charge_list_details.pat_due as 'patDue',
		patient_charge_list_details.pri_due + patient_charge_list_details.sec_due 
		+ patient_charge_list_details.tri_due as 'insDue', 
		patient_charge_list_details.newBalance,
		date_format(patient_charge_list.lastPaymentDate,'".$dateFormat."')
		as lastPaymentDate,patient_charge_list.primaryInsuranceCoId,
		date_format(patient_charge_list.postedDate,'".$dateFormat."')
		as postedDate,patient_charge_list.secondaryInsuranceCoId,
		patient_charge_list.tertiaryInsuranceCoId,
		patient_charge_list.insuranceDue,
		patient_charge_list.patientDue,
		patient_charge_list_details.write_off,cpt_fee_tbl.cpt_desc,cpt_fee_tbl.cpt_cat_id as cptcatid,
		patient_charge_list_details.procCode,cpt_fee_tbl.cpt_prac_code,
		patient_data.lname as patient_lname,patient_data.fname as patient_fname,
		patient_data.mname as patient_mname,users.lname as physician_lname,
		users.fname as physician_fname,users.mname as physician_mname,
		patient_charge_list.copayPaid,patient_charge_list.copay
		from 
		patient_charge_list 
		join 
		patient_charge_list_details
		on patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
		join 
		cpt_fee_tbl 
		on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
		LEFT JOIN 
		pos_facilityies_tbl 
		on pos_facilityies_tbl.pos_facility_id = patient_charge_list.facility_id
		join 
		patient_data on patient_data.id = patient_charge_list.patient_id
		join 
		users on users.id = patient_charge_list.primary_provider_id_for_reports
		where 
		patient_charge_list.date_of_service between '$Start_date' and '$End_date' 
		AND patient_charge_list_details.del_status='0'";	
		
if(empty($sc_name) === false){
	$qry .= " AND patient_charge_list.facility_id in($sc_name)";
}
if(empty($grp_id) === false){
	$qry .= " AND patient_charge_list.gro_id in($grp_id)";
}
if(empty($Physician) === false or empty($credit_physician) === false){
	if(empty($Physician) === false and empty($credit_physician) === false){
		$qry.= " and (patient_charge_list.primary_provider_id_for_reports IN ($Physician) 
		and patient_charge_list.secondaryProviderId IN ($credit_physician))";				
	}
	else if(empty($Physician) === false){
		$qry.= " and patient_charge_list.primary_provider_id_for_reports IN ($Physician)";
	}
	else if(empty($credit_physician) === false){
		$qry.= " and patient_charge_list.secondaryProviderId IN ($credit_physician)";
	}
}
if($chksamebillingcredittingproviders==1){
	$qry.= " and patient_charge_list.primary_provider_id_for_reports!=patient_charge_list.secondaryProviderId";							
}
if(empty($insId) === false){
	$qry .= " AND (patient_charge_list.primaryInsuranceCoId IN ($insId)
				OR patient_charge_list.secondaryInsuranceCoId IN ($insId) 
				OR patient_charge_list.tertiaryInsuranceCoId IN ($insId))";	
}
/*if(empty($Procedure) === false){
	$qry .= " and patient_charge_list_details.procCode in($Procedure)";
}*/
if(empty($cpt_cat_id) === false || empty($Procedure) === false){
	$cpt_data_str = '';
	if(empty($cpt_cat_id) === false){
		$cpt_id_qry = "select cpt_fee_id from cpt_fee_tbl where cpt_cat_id in ($cpt_cat_id)";
		$cpt_id_qry_rs = imw_query($cpt_id_qry);
		$cpt_data_arr = array();
		while($cpt_id_qry_res = imw_fetch_assoc($cpt_id_qry_rs)){
			$cpt_data_arr[] = $cpt_id_qry_res['cpt_fee_id'];
			$cpt_data_str = implode(",",$cpt_data_arr);
		}
	}
	if(empty($Procedure) === false){
		if($cpt_data_str == '')
		$cpt_data_str = (is_array($Procedure)) ? implode(',', $Procedure) : $Procedure;
		else
		$cpt_data_str = implode(',',array($Procedure,$cpt_data_str));
	}
	$cpt_data_arr = explode(',', $cpt_data_str);
	for($i=0; $i< sizeof($cpt_data_arr); $i++){
		if($cpt_data_arr[$i]!='' && $cpt_data_arr[$i]>0){
			$tempCPTArr[] = $cpt_data_arr[$i];
		}
	}
	$tempCPTStr = implode(',', $tempCPTArr);	
	$qry .= " AND patient_charge_list_details.procCode in($tempCPTStr)";
}
if(empty($cpt_cat_2) == false){
	$qry.= " and cpt_fee_tbl.cpt_category2 IN ($cpt_cat_2)";
}
$qry.= " ORDER BY users.lname,users.fname";		

if($sort_by == 'patient') 
	$qry .= ", patient_lname , patient_fname , date_of_service , patient_charge_list.encounter_id";
elseif($sort_by == 'cpt')
	$qry .= ", cpt_fee_tbl.cpt_prac_code";
else
	$qry.= ", date_of_service , patient_lname , patient_fname , patient_charge_list.encounter_id";

//echo $qry."<br>";
$qryRes = imw_query($qry);
$mainResultArr = array();
$main_encounter_id_arr = array();
$procedureDescArr = array();
$providerNameArr = array();
$primaryInsIdArr = array();
$procedureCodeArr = array();
$cptCatArr = array();
while($qryRs = imw_fetch_assoc($qryRes)){
	//--- GET ENCOUNTER ARRAY --------
	$encounter_id = $qryRs['encounter_id'];
	$main_encounter_id_arr[$encounter_id] = $encounter_id;
	
	//--- GET PROCEDURE DETAILS ---------
	$procCode = trim($qryRs['procCode']);
	$cpt_prac_code = trim($qryRs['cpt_prac_code']);
	$cpt_prac_code .= ' - '.$qryRs['cpt_desc'];
	$procedureNameArr[$procCode] =  $cpt_prac_code;
	$procedureCodeArr[$procCode] =  trim($qryRs['cpt_prac_code']);
	$procedureDescArr[$procCode] =  trim($qryRs['cpt_desc']);
	
	//--- GET INSURANCE COMPANIES ID ---------
	$primaryInsuranceCoId = $qryRs['primaryInsuranceCoId'];
	$secondaryInsuranceCoId = $qryRs['secondaryInsuranceCoId'];
	$tertiaryInsuranceCoId = $qryRs['tertiaryInsuranceCoId'];
	
	$primaryInsIdArr[$primaryInsuranceCoId] = $primaryInsuranceCoId;
	$primaryInsIdArr[$secondaryInsuranceCoId] = $secondaryInsuranceCoId;
	$primaryInsIdArr[$tertiaryInsuranceCoId] = $tertiaryInsuranceCoId;
	
	//---- GET PHYSICIAN NAME -------
	$primaryProviderId = $qryRs['primaryProviderId'];
	$providerName =core_name_format($qryRs['physician_lname'], $qryRs['physician_fname'], $qryRs['physician_mname']);
	
	if(strlen($providerName)>17){
		if($groupby == "Procedure")
		$providerName = str_replace(',',',<br>',$providerName);
		//$providerName = str_replace(',',',<br>',$providerName);
	}
	$providerNameArr[$primaryProviderId] = $providerName;
	
	//---- GET FACILITY NAME --------
	$facility_id = $qryRs['facility_id'];
	$facilityPracCode = $qryRs['facilityPracCode'];
	$facilityNameArr[$facility_id] = $facilityPracCode;
	
	//--- GET CONTRACT PRICES OF PROCEDURES ----
	$cpt_catid = $qryRs['cptcatid']; 
	
	if($total_method == "contract_price"){
		$cpt_prac_code = trim($qryRs['cpt_prac_code']);
		$contract_price = $CLSReports->getContractFee($cpt_prac_code,$primaryInsuranceCoId,true);
		$qryRs["totalAmt"] = $contract_price;
	}
	
	$charge_list_detail_id = $qryRs['charge_list_detail_id'];
	$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
	if($qryRs['coPayAdjustedAmount']==1){
		$arrCopayChgDetId[$encounter_id]=$charge_list_detail_id;
	}

	//ALLOWED AMOUNT LOGIC AS PER DISCUSSION
	if($qryRs['last_pri_paid_date']=='0000-00-00'){
		$qryRs['approvedAmt']=0;
	}
	
	if($groupby == "Facility"){
	
		if($process == "Summary"){		
			$mainResultArr[$facility_id][$primaryProviderId][$procCode][$charge_list_detail_id] = $qryRs;
		}
		//--- CREATE ARRAY FOR DETAIL REPORT ----
		else{
			$mainResultArr[$facility_id][$primaryProviderId][$charge_list_detail_id] = $qryRs;
		}	
	}
	else if($groupby == "Physician"){
	
		if($process == "Summary"){		
			$mainResultArr[$primaryProviderId][$procCode][$charge_list_detail_id] = $qryRs;
		}
		//--- CREATE ARRAY FOR DETAIL REPORT ----
		else{
			$mainResultArr[$primaryProviderId][$facility_id][$charge_list_detail_id] = $qryRs;
		}	
	}
	else if($groupby == "CPTCategory"){
	
		if($process == "Summary"){		
			$mainResultArr[$cpt_catid][$procCode][$charge_list_detail_id] = $qryRs;
		}
		//--- CREATE ARRAY FOR DETAIL REPORT ----
		else{
			$mainResultArr[$cpt_catid][$facility_id][$charge_list_detail_id] = $qryRs;
		}	
	}
	else if($groupby == "ins_group"){
		$insGroup= ($arrInsMapInsGroups[$primaryInsuranceCoId]>0) ? $arrInsMapInsGroups[$primaryInsuranceCoId] : '0';

		if($process == "Summary"){		
			$mainResultArr[$insGroup][$procCode][$charge_list_detail_id] = $qryRs;
		}
		//--- CREATE ARRAY FOR DETAIL REPORT ----
		else{
			$mainResultArr[$insGroup][$procCode][$charge_list_detail_id] = $qryRs;
		}	
	}	
	else{
		//--- CREATE ARRAY FOR SUMMARY REPORT ----
		if($process == "Summary"){		
			$mainResultArr[$procCode][$primaryProviderId][$charge_list_detail_id] = $qryRs;
		}
		//--- CREATE ARRAY FOR DETAIL REPORT ----
		else{
			$mainResultArr[$procCode][$primaryProviderId][$charge_list_detail_id] = $qryRs;
		}
		//-------GROUPBY FACILITY ---------
		
	}
	
}imw_free_result($qryRes);
?>