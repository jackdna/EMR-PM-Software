<?php
//--- GET ALL ENCOUNTERS FROM PAYMENT TABLE ----
$qry = "select patient_chargesheet_payment_info.encounter_id,
		patient_chargesheet_payment_info.payment_id,patient_charges_detail_payment_info.charge_list_detail_id,
		patient_chargesheet_payment_info.date_of_payment, patient_chargesheet_payment_info.transaction_date 
		from patient_chargesheet_payment_info join patient_charges_detail_payment_info ON 
		patient_charges_detail_payment_info.payment_id = patient_chargesheet_payment_info.payment_id
		where patient_chargesheet_payment_info.$DateRangeFor between '$Start_date' and '$End_date'";
$mainQryRes = imw_query($qry);
$encounterIdArr = array();
while($mainQryRs = imw_fetch_assoc($mainQryRes)){
	$encounter_id = $mainQryRs['encounter_id'];
	$encounterIdArr[$encounter_id] = $encounter_id;

	if($mainQryRs[$i]['charge_list_detail_id']>0){
		$paidChgDetIdArr[$mainQryRs[$i]['charge_list_detail_id']] = $mainQryRs[$i]['charge_list_detail_id'];
	}else{
		$copayEncIds[$encounter_id]=$encounter_id;
	}
}
imw_free_result($mainQryRes);
$encounterIdStr = join(',',$encounterIdArr);

$tempCPTStr='';
if(empty($cpt_cat_id) === false || empty($Procedure) === false){
	$cpt_data_str = '';
	if(empty($cpt_cat_id) === false){
		$cpt_id_qry = "select cpt_fee_id from cpt_fee_tbl where cpt_cat_id in ($cpt_cat_id)";
		$cpt_id_qry_rs = imw_query($cpt_id_qry);
		$cpt_data_arr = array();
		while($cpt_id_qry_res=imw_fetch_assoc($cpt_id_qry_rs)){
			$cpt_data_arr[] = $cpt_id_qry_res['cpt_fee_id'];
		}
		$cpt_data_str = implode(",",$cpt_data_arr);
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
}

//GET ENCOUNTERS OF ADJUSTMENT CHARGE LIST DETAIL IDS
$arrAdjEncounterIds=array();
if(sizeof($arrAdjustmentEncs)>0){
	$strAdjustmentDetIds  = implode(',',$arrAdjustmentEncs);
	$qry="Select charge_list_id FROM patient_charge_list_details WHERE charge_list_detail_id IN(".$strAdjustmentDetIds.")";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrChargeListIds[$res['charge_list_id']]=$res['charge_list_id'];
	}unset($rs);
	
	if(sizeof($arrChargeListIds)>0){
		$strChargeListIds  = implode(',',$arrChargeListIds);
		unset($arrChargeListIds);
		
		$qry="Select encounter_id FROM patient_charge_list WHERE charge_list_id IN(".$strChargeListIds.")";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$arrAdjEncounterIds[$res['encounter_id']]=$res['encounter_id'];
		}unset($rs);
	}
}

$mainResultArr = array();
$main_encounter_id_arr = array();
$procedureNameArr = array();
$providerNameArr = array();
$primaryInsIdArr = array();
$procedureCodeArr = array();
$procedureDescArr = array();

$arrFinalAllEncIds=array();
$arrFinalAllEncIds=array_merge($encounterIdArr, $arrAdjEncounterIds);

if(sizeof($arrFinalAllEncIds)>0){
	//$splitted_encounters = array_chunk($arrFinalAllEncIds,3000);

	$str_encs= implode('),(', $arrFinalAllEncIds);
	$str_encs='('.$str_encs.')';

	//CREATE TEMP TABLE AND INSERT DATA
	$temp_join_part='';
	$tmp_table="IMWTEMP_report_cpt_analysis_enc_ids_".time().'_'.$_SESSION["authId"];
	imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);
	imw_query("CREATE TEMPORARY TABLE ".$tmp_table." (enc_id INT)");
	imw_query("INSERT INTO $tmp_table (enc_id) VALUES ".$str_encs);
	imw_query("CREATE INDEX tempEncounterindx ON $tmp_table (enc_id)");	
	$temp_join_part=" INNER JOIN ".$tmp_table." t_tbl ON patient_charge_list.encounter_id = t_tbl.enc_id";
	
	
	$qry = "Select patient_charge_list.patient_id,patient_charge_list.primaryInsuranceCoId,
	patient_charge_list_details.coPayAdjustedAmount,
	patient_charge_list_details.charge_list_detail_id,
	date_format(patient_charge_list.date_of_service,'".$dateFormat."')
	as date_of_service,pos_facilityies_tbl.facilityPracCode, patient_charge_list_details.del_status,
	patient_charge_list.encounter_id,patient_charge_list.facility_id,
	patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId',
	patient_charge_list.totalBalance,
	patient_charge_list.first_posted_date,patient_charge_list.submitted,
	patient_charge_list.lastPaymentDate,patient_charge_list_details.units,
	patient_charge_list_details.last_pri_paid_date,
	patient_charge_list_details.overPaymentForProc,
	patient_charge_list_details.procCharges * patient_charge_list_details.units as totalAmt,
	patient_charge_list_details.overPaymentForProc +
	patient_charge_list_details.paidForProc as paidForProc,
	patient_charge_list_details.approvedAmt,
	patient_charge_list_details.pat_due as 'patDue',
	patient_charge_list_details.pri_due + patient_charge_list_details.sec_due 
	+ patient_charge_list_details.tri_due as 'insDue', 
	date_format(patient_charge_list.lastPaymentDate,'".$dateFormat."')
	as lastPaymentDate,patient_charge_list.primaryInsuranceCoId,
	date_format(patient_charge_list.postedDate,'".$dateFormat."')
	as postedDate,patient_charge_list.secondaryInsuranceCoId,
	patient_charge_list.tertiaryInsuranceCoId,
	patient_charge_list_details.write_off,cpt_fee_tbl.cpt_desc,
	patient_charge_list_details.procCode,cpt_fee_tbl.cpt_prac_code,cpt_fee_tbl.cpt_cat_id as cptcatid,
	patient_charge_list_details.newBalance, DATE_FORMAT(patient_charge_list_details.trans_del_date, '%Y-%m-%d') as 'trans_del_date',
	patient_data.lname as patient_lname,patient_data.fname as patient_fname,
	patient_data.mname as patient_mname,users.lname as physician_lname,
	users.fname as physician_fname,users.mname as physician_mname,
	patient_charge_list.copayPaid,patient_charge_list.copay
	from patient_charge_list join patient_charge_list_details
	on patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
	".$temp_join_part."
	join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
	left join pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = patient_charge_list.facility_id
	join patient_data on patient_data.id = patient_charge_list.patient_id
	join users on users.id = patient_charge_list.primary_provider_id_for_reports
	where 1=1";
	//AND (patient_charge_list_details.del_status='0' OR (patient_charge_list_details.del_status='1' AND DATE_FORMAT(patient_charge_list_details.trans_del_date, '%Y-%m-%d')>'$End_date'))
	
	if(empty($sc_name) === false){
		$qry.= " and patient_charge_list.facility_id in($sc_name)";
	}
	if(empty($grp_id) === false){
		$qry.= " and patient_charge_list.gro_id in($grp_id)";
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
		$qry.= " AND (patient_charge_list.primaryInsuranceCoId IN ($insId) 
		OR patient_charge_list.secondaryInsuranceCoId IN ($insId) 
		OR patient_charge_list.tertiaryInsuranceCoId IN ($insId))";	
	}
	if(empty($tempCPTStr) === false){
		$qry.= " and patient_charge_list_details.procCode in($tempCPTStr)";
	}
	if(empty($cpt_cat_2) == false){
		$qry.= " and cpt_fee_tbl.cpt_category2 IN ($cpt_cat_2)";
	}
	$qry.= " group by patient_charge_list_details.charge_list_detail_id";
	$qry .= " ORDER BY users.lname,users.fname, patient_data.lname,patient_data.fname";	
	$qryRes = imw_query($qry) or die(imw_error());
	
	while($qryRs = imw_fetch_assoc($qryRes)){
		$encounter_id = $qryRs['encounter_id'];
		$charge_list_detail_id = $qryRs['charge_list_detail_id'];
		
		
		//GET ONLY EXACT PROCEDURE RECORDS (IF ADJUSTMENTS/PAYMENTS/COPAY RECORDS)
		if($arrAdjustmentEncs[$charge_list_detail_id] || $paidChgDetIdArr[$charge_list_detail_id] || $copayEncIds[$encounter_id]){

		//IF NOT DELETED OR DELETED AFTER FIRST POSTED DATE ONLY THEN FETCH THE RECORD
		if($qryRs['del_status']=='0' 
		|| $qryRs['first_posted_date']=='0000-00-00' 
		|| ($qryRs['del_status']=='1' && $qryRs['first_posted_date']!='0000-00-00' && $qryRs['trans_del_date'] >= $qryRs['first_posted_date'])){

				//--- GET PROCEDURE DETAILS ---------
				$procCode = trim($qryRs['procCode']);
				$cpt_prac_code = trim($qryRs['cpt_prac_code']);
				$cpt_prac_code .= '  '.$qryRs['cpt_desc'];
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
				}
				$providerNameArr[$primaryProviderId] = $providerName;
			
				//---- GET FACILITY NAME --------
				$facility_id = $qryRs['facility_id'];
				$facilityPracCode = $qryRs['facilityPracCode'];
				$facilityNameArr[$facility_id] = $facilityPracCode;
				
				$coPayAdjustedAmount = $qryRs['coPayAdjustedAmount'];
				$paidForProc = $qryRs['paidForProc'];
				if($coPayAdjustedAmount==1){
					$arrCopayChgDetId[$encounter_id]=$charge_list_detail_id;
				}
				
				$cpt_catid = $qryRs['cptcatid']; 
									
				//--- GET CONTRACT PRICES OF PROCEDURES ----
				if($total_method == "contract_price"){
					$cpt_prac_code = trim($qryRs['cpt_prac_code']);
					$contract_price = $CLSReports->getContractFee($cpt_prac_code,$primaryInsuranceCoId,true);
					$qryRs["totalAmt"] = $contract_price;
				}
				if($groupby == "Facility"){
					if($process == "Summary"){		
							$main_encounter_id_arr[$encounter_id] = $encounter_id;
							$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
							$mainResultArr[$facility_id][$primaryProviderId][$procCode][$charge_list_detail_id] = $qryRs;
					}
					//--- CREATE ARRAY FOR DETAIL REPORT ----
					else{
						$main_encounter_id_arr[$encounter_id] = $encounter_id;
						$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
						$mainResultArr[$facility_id][$primaryProviderId][$charge_list_detail_id] = $qryRs;
						$arrForOrderBy[$facility_id][$primaryProviderId][$charge_list_detail_id]= core_name_format($qryRs['patient_lname'], $qryRs['patient_fname'], $qryRs['patient_mname']);
						$arrForOrderCPT[$facility_id][$primaryProviderId][$charge_list_detail_id]= $qryRs['cpt_prac_code'];
						$arrForOrderDOS[$facility_id][$primaryProviderId][$charge_list_detail_id]= $qryRs['date_of_service'];
						
					}	
				}
				else if($groupby == "Physician"){
					if($process == "Summary"){		
						$main_encounter_id_arr[$encounter_id] = $encounter_id;
						$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
						$mainResultArr[$primaryProviderId][$procCode][$charge_list_detail_id] = $qryRs;
					}
					//--- CREATE ARRAY FOR DETAIL REPORT ----
					else{
						$main_encounter_id_arr[$encounter_id] = $encounter_id;
						$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
						$mainResultArr[$primaryProviderId][$facility_id][$charge_list_detail_id] = $qryRs;
						$arrForOrderBy[$primaryProviderId][$facility_id][$charge_list_detail_id]= core_name_format($qryRs['patient_lname'], $qryRs['patient_fname'], $qryRs['patient_mname']);
						$arrForOrderCPT[$primaryProviderId][$facility_id][$charge_list_detail_id]= $qryRs['cpt_prac_code'];
						$arrForOrderDOS[$primaryProviderId][$facility_id][$charge_list_detail_id]= $qryRs['date_of_service'];
					}	
				}
				else if($groupby == "CPTCategory"){
					//--- CREATE ARRAY FOR SUMMARY REPORT ----
					if($process == "Summary"){		
						$main_encounter_id_arr[$encounter_id] = $encounter_id;
						$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
						$mainResultArr[$cpt_catid][$procCode][$charge_list_detail_id] = $qryRs;
					}
					//--- CREATE ARRAY FOR DETAIL REPORT ----
					else{
						$main_encounter_id_arr[$encounter_id] = $encounter_id;
						$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
						$mainResultArr[$cpt_catid][$procCode][$charge_list_detail_id] = $qryRs;
						$arrForOrderBy[$cpt_catid][$procCode][$charge_list_detail_id]= core_name_format($qryRs['patient_lname'], $qryRs['patient_fname'], $qryRs['patient_mname']);
						$arrForOrderCPT[$cpt_catid][$procCode][$charge_list_detail_id]= $qryRs['cpt_prac_code'];
						$arrForOrderDOS[$cpt_catid][$procCode][$charge_list_detail_id]= $qryRs['date_of_service'];
					}
				}
				else if($groupby == "ins_group"){
					$insGroup= ($arrInsMapInsGroups[$primaryInsuranceCoId]>0) ? $arrInsMapInsGroups[$primaryInsuranceCoId] : '0';
					//--- CREATE ARRAY FOR SUMMARY REPORT ----
					if($process == "Summary"){		
						$main_encounter_id_arr[$encounter_id] = $encounter_id;
						$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
						$mainResultArr[$insGroup][$procCode][$charge_list_detail_id] = $qryRs;
					}
					//--- CREATE ARRAY FOR DETAIL REPORT ----
					else{
						$main_encounter_id_arr[$encounter_id] = $encounter_id;
						$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
						$mainResultArr[$insGroup][$procCode][$charge_list_detail_id] = $qryRs;
						$arrForOrderBy[$insGroup][$procCode][$charge_list_detail_id]= core_name_format($qryRs['patient_lname'], $qryRs['patient_fname'], $qryRs['patient_mname']);
						$arrForOrderCPT[$insGroup][$procCode][$charge_list_detail_id]= $qryRs['cpt_prac_code'];
						$arrForOrderDOS[$insGroup][$procCode][$charge_list_detail_id]= $qryRs['date_of_service'];
					}
				}					
				else{
					//--- CREATE ARRAY FOR SUMMARY REPORT ----
					if($process == "Summary"){		
						$main_encounter_id_arr[$encounter_id] = $encounter_id;
						$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
						$mainResultArr[$procCode][$primaryProviderId][$charge_list_detail_id] = $qryRs;
					}
					//--- CREATE ARRAY FOR DETAIL REPORT ----
					else{
						$main_encounter_id_arr[$encounter_id] = $encounter_id;
						$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
						$mainResultArr[$procCode][$primaryProviderId][$charge_list_detail_id] = $qryRs;
						$arrForOrderBy[$procCode][$primaryProviderId][$charge_list_detail_id]= core_name_format($qryRs['patient_lname'], $qryRs['patient_fname'], $qryRs['patient_mname']);
						$arrForOrderCPT[$procCode][$primaryProviderId][$charge_list_detail_id]= $qryRs['cpt_prac_code'];
						$arrForOrderDOS[$procCode][$primaryProviderId][$charge_list_detail_id]= $qryRs['date_of_service'];
					}
				}
			}
		}
	}imw_free_result($qryRes);
	//DROP TEMP TABLE
	imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);	
}
unset($arrFinalAllEncIds);
unset($arrAdjEncounterIds);
//-----------------


//--- FETCH ALL ENCOUNTERS WITH SEARCH CERTARIA --
$qry = "select patient_charge_list.patient_id,
patient_charge_list_details.coPayAdjustedAmount,
patient_charge_list_details.charge_list_detail_id,
date_format(patient_charge_list.date_of_service,'".$dateFormat."')
as date_of_service,pos_facilityies_tbl.facilityPracCode,
patient_charge_list.encounter_id,patient_charge_list.facility_id,
patient_charge_list.primary_provider_id_for_reports  as 'primaryProviderId',
patient_charge_list.totalBalance,
patient_charge_list.first_posted_date,patient_charge_list.submitted,
patient_charge_list.lastPaymentDate,patient_charge_list_details.units,
patient_charge_list_details.last_pri_paid_date,
patient_charge_list_details.overPaymentForProc,
patient_charge_list_details.approvedAmt,
patient_charge_list_details.procCharges * patient_charge_list_details.units as totalAmt,
patient_charge_list_details.overPaymentForProc +
patient_charge_list_details.paidForProc as paidForProc,
patient_charge_list_details.pat_due as 'patDue',
patient_charge_list_details.pri_due + patient_charge_list_details.sec_due 
+ patient_charge_list_details.tri_due as 'insDue', 
date_format(patient_charge_list.lastPaymentDate,'".$dateFormat."')
as lastPaymentDate,patient_charge_list.primaryInsuranceCoId,
date_format(patient_charge_list.postedDate,'".$dateFormat."')
as postedDate,patient_charge_list.secondaryInsuranceCoId,
patient_charge_list.tertiaryInsuranceCoId,
patient_charge_list_details.write_off,cpt_fee_tbl.cpt_desc,
patient_charge_list_details.procCode,cpt_fee_tbl.cpt_prac_code,cpt_fee_tbl.cpt_cat_id as cptcatid,
patient_charge_list_details.newBalance, patient_charge_list_details.del_status, 
DATE_FORMAT(patient_charge_list_details.trans_del_date, '%Y-%m-%d') as 'trans_del_date',
patient_data.lname as patient_lname,patient_data.fname as patient_fname,
patient_data.mname as patient_mname,users.lname as physician_lname,
users.fname as physician_fname,users.mname as physician_mname,
patient_charge_list.copayPaid,patient_charge_list.copay
from patient_charge_list join patient_charge_list_details
on patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id
join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
left join pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = patient_charge_list.facility_id
join patient_data on patient_data.id = patient_charge_list.patient_id
join users on users.id = patient_charge_list.primary_provider_id_for_reports
where 1=1";	
//AND (patient_charge_list_details.del_status='0' OR (patient_charge_list_details.del_status='1' AND DATE_FORMAT(patient_charge_list_details.trans_del_date, '%Y-%m-%d')>'$End_date'))		
if(empty($sc_name) === false){
	$qry .= " and patient_charge_list.facility_id in($sc_name)";
}
if(empty($grp_id) === false){
	$qry .= " and patient_charge_list.gro_id in($grp_id)";
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
if(empty($tempCPTStr) === false){
	$qry .= " and patient_charge_list_details.procCode in($tempCPTStr)";
}

$qry .=" AND (patient_charge_list.first_posted_date BETWEEN '$Start_date' AND '$End_date')";
$qry .= " group by patient_charge_list_details.charge_list_detail_id";

if($process == "Summary"){
	$qry .= " ORDER BY users.lname,users.fname, cpt_fee_tbl.cpt_prac_code";	
}else{
	$qry .= " ORDER BY users.lname,users.fname, patient_data.lname,patient_data.fname";	
} 
$qryRes = imw_query($qry);

while($qryRs = imw_fetch_assoc($qryRes)){
	$encounter_id = $qryRs['encounter_id'];

	//IF NOT DELETED OR DELETED AFTER FIRST POSTED DATE ONLY THEN FETCH THE RECORD
	if($qryRs['del_status']=='0' 
	|| $qryRs['first_posted_date']=='0000-00-00' 
	|| ($qryRs['del_status']=='1' && $qryRs['first_posted_date']!='0000-00-00' && $qryRs['trans_del_date'] >= $qryRs['first_posted_date'])){
	
		//--- GET PROCEDURE DETAILS ---------
		$procCode = trim($qryRs['procCode']);
		$cpt_prac_code = trim($qryRs['cpt_prac_code']);
		$cpt_prac_code .= '  '.$qryRs['cpt_desc'];
		$procedureNameArr[$procCode] =  $cpt_prac_code;
		$procedureCodeArr[$procCode] =  trim($qryRs['cpt_prac_code']);
		$charge_list_detail_id = $qryRs['charge_list_detail_id'];
		
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
		}
		$providerNameArr[$primaryProviderId] = $providerName;
	
		//---- GET FACILITY NAME --------
		$facility_id = $qryRs['facility_id'];
		$facilityPracCode = $qryRs['facilityPracCode'];
		$facilityNameArr[$facility_id] = $facilityPracCode;
		
		$coPayAdjustedAmount = $qryRs['coPayAdjustedAmount'];
		$paidForProc = $qryRs['paidForProc'];
		
	
		if($coPayAdjustedAmount==1){
			$arrCopayChgDetId[$encounter_id]=$charge_list_detail_id;
		}

		//ALLOWED AMOUNT LOGIC AS PER DISCUSSION
		if($qryRs['last_pri_paid_date']=='0000-00-00'){
			$qryRs['approvedAmt']=0;
		}
	
		$cpt_catid = $qryRs['cptcatid']; 
		
		//--- GET CONTRACT PRICES OF PROCEDURES ----
		if($total_method == "contract_price"){
			$cpt_prac_code = trim($qryRs['cpt_prac_code']);
			$contract_price = $CLSReports->getContractFee($cpt_prac_code,$primaryInsuranceCoId,true);
			$qryRs["totalAmt"] = $contract_price;
		}
		if($groupby == "Facility"){
			if($process == "Summary"){		
					$main_encounter_id_arr[$encounter_id] = $encounter_id;
					$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
					$mainResultArr[$facility_id][$primaryProviderId][$procCode][$charge_list_detail_id] = $qryRs;
			}
			//--- CREATE ARRAY FOR DETAIL REPORT ----
			else{
				$main_encounter_id_arr[$encounter_id] = $encounter_id;
				$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
				$mainResultArr[$facility_id][$primaryProviderId][$charge_list_detail_id] = $qryRs;
				$arrForOrderBy[$facility_id][$primaryProviderId][$charge_list_detail_id]= core_name_format($qryRs['patient_lname'], $qryRs['patient_fname'], $qryRs['patient_mname']);
				$arrForOrderCPT[$facility_id][$primaryProviderId][$charge_list_detail_id]= $qryRs['cpt_prac_code'];
				$arrForOrderDOS[$facility_id][$primaryProviderId][$charge_list_detail_id]= $qryRs['date_of_service'];
			}	
		}
		else if($groupby == "Physician"){
		
			if($process == "Summary"){		
				$main_encounter_id_arr[$encounter_id] = $encounter_id;
				$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
				$mainResultArr[$primaryProviderId][$procCode][$charge_list_detail_id] = $qryRs;
			}
			//--- CREATE ARRAY FOR DETAIL REPORT ----
			else{
				$main_encounter_id_arr[$encounter_id] = $encounter_id;
				$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
				$mainResultArr[$primaryProviderId][$facility_id][$charge_list_detail_id] = $qryRs;
				$arrForOrderBy[$primaryProviderId][$facility_id][$charge_list_detail_id]= core_name_format($qryRs['patient_lname'], $qryRs['patient_fname'], $qryRs['patient_mname']);
				$arrForOrderCPT[$primaryProviderId][$facility_id][$charge_list_detail_id]= $qryRs['cpt_prac_code'];
				$arrForOrderDOS[$primaryProviderId][$facility_id][$charge_list_detail_id]= $qryRs['date_of_service'];
			}	
		}
		else if($groupby == "CPTCategory"){
			//--- CREATE ARRAY FOR SUMMARY REPORT ----
			if($process == "Summary"){		
				$main_encounter_id_arr[$encounter_id] = $encounter_id;
				$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
				$mainResultArr[$cpt_catid][$procCode][$charge_list_detail_id] = $qryRs;
			}
			//--- CREATE ARRAY FOR DETAIL REPORT ----
			else{
				$main_encounter_id_arr[$encounter_id] = $encounter_id;
				$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
				$mainResultArr[$cpt_catid][$procCode][$charge_list_detail_id] = $qryRs;
				$arrForOrderBy[$cpt_catid][$procCode][$charge_list_detail_id]= core_name_format($qryRs['patient_lname'], $qryRs['patient_fname'], $qryRs['patient_mname']);
				$arrForOrderCPT[$cpt_catid][$procCode][$charge_list_detail_id]= $qryRs['cpt_prac_code'];
				$arrForOrderDOS[$cpt_catid][$procCode][$charge_list_detail_id]= $qryRs['date_of_service'];
			}
		}
		else if($groupby == "ins_group"){
			$insGroup= ($arrInsMapInsGroups[$primaryInsuranceCoId]>0) ? $arrInsMapInsGroups[$primaryInsuranceCoId] : '0';
			//--- CREATE ARRAY FOR SUMMARY REPORT ----
			if($process == "Summary"){		
				$main_encounter_id_arr[$encounter_id] = $encounter_id;
				$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
				$mainResultArr[$insGroup][$procCode][$charge_list_detail_id] = $qryRs;
			}
			//--- CREATE ARRAY FOR DETAIL REPORT ----
			else{
				$main_encounter_id_arr[$encounter_id] = $encounter_id;
				$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
				$mainResultArr[$insGroup][$procCode][$charge_list_detail_id] = $qryRs;
				$arrForOrderBy[$insGroup][$procCode][$charge_list_detail_id]= core_name_format($qryRs['patient_lname'], $qryRs['patient_fname'], $qryRs['patient_mname']);
				$arrForOrderCPT[$insGroup][$procCode][$charge_list_detail_id]= $qryRs['cpt_prac_code'];
				$arrForOrderDOS[$insGroup][$procCode][$charge_list_detail_id]= $qryRs['date_of_service'];
			}
		}			
		else{
			//--- CREATE ARRAY FOR SUMMARY REPORT ----
			if($process == "Summary"){		
				$main_encounter_id_arr[$encounter_id] = $encounter_id;
				$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
				$mainResultArr[$procCode][$primaryProviderId][$charge_list_detail_id] = $qryRs;
			}
			//--- CREATE ARRAY FOR DETAIL REPORT ----
			else{
				$main_encounter_id_arr[$encounter_id] = $encounter_id;
				$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
				$mainResultArr[$procCode][$primaryProviderId][$charge_list_detail_id] = $qryRs;
				$arrForOrderBy[$procCode][$primaryProviderId][$charge_list_detail_id]= core_name_format($qryRs['patient_lname'], $qryRs['patient_fname'], $qryRs['patient_mname']);
				$arrForOrderCPT[$procCode][$primaryProviderId][$charge_list_detail_id]= $qryRs['cpt_prac_code'];
				$arrForOrderDOS[$procCode][$primaryProviderId][$charge_list_detail_id]= $qryRs['date_of_service'];
			}
		}
	}
}imw_free_result($qryRes);


//SORTING BY ALPHABETICAL TO MAIN ARRAY
if($process == "Detail"){	
	$tempArray=$mainResultArr;
	$mainResultArr=array();
	
	foreach($tempArray as $first_grp_id => $first_grp_arr){
		foreach($first_grp_arr as $sec_grp_id => $sec_grp_arr){
			
			if($sort_by == 'patient'){
				$chgdetid_arr=$arrForOrderBy[$first_grp_id][$sec_grp_id];
			}elseif($sort_by == 'cpt'){
				$chgdetid_arr=$arrForOrderCPT[$first_grp_id][$sec_grp_id];
			}else{ 
				$chgdetid_arr=$arrForOrderDOS[$first_grp_id][$sec_grp_id];
			}
			asort($chgdetid_arr);
			foreach($chgdetid_arr as $chddetid => $pat_name){
				$mainResultArr[$first_grp_id][$sec_grp_id][$chddetid]= $sec_grp_arr[$chddetid];
			}
		}
	}
	unset($tempArray);
}

?>