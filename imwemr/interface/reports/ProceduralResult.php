<?php
ini_set("memory_limit","3072M");
$dateFormat= get_sql_date_format();
$curDate = date($phpDateFormat.' h:i A');		
$FCName= $_SESSION['authId'];
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
	$fromDate=$Start_date;
	$toDate=$End_date;
	
	$start_time='00:00:00';
	$end_time='23:59:59';
	
	//--- CHANGE DATE FORMAT -------
	$Start_date = getDateFormatDB($Start_date);
	$End_date = getDateFormatDB($End_date);

	$diff = date_diff(date_create($Start_date),date_create($End_date));
	$monthDiff= floor($diff->y * 12 + $diff->m + $diff->d/30 + $diff->h / 24);
	
	$reptByForFun='';
	if($DateRangeFor=='date_of_payment'){ $reptByForFun='dop';}
	else if($DateRangeFor=='transaction_date'){ $reptByForFun='dot';}

	//-- OPERATOR INITIAL -------
	$authProviderNameArr = preg_split('/ /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);

	//--- GET ALL PROVIDER NAME ----
	$qry = "select id,fname,mname,lname from users";
	$res = imw_query($qry);
	$providerNameArr = array();
	while($res=imw_fetch_assoc($rs)){	
		$id = $res['id'];
		$providerNameArr[$id] = core_name_format($res['lname'], $res['fname'], $res['mname']);
		
		// two character array
		$operatorInitial = substr($res['fname'],0,1);
		$operatorInitial .= substr($res['lname'],0,1);
		$userNameTwoCharArr[$id] = strtoupper($operatorInitial);
	}

	// -- GET ALL POS-FACILITIES
	$arrAllFacilities=array();
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
	// ------------------------------

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($sc_name)<=0 && isPosFacGroupEnabled()){
		$sc_name = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($sc_name)<=0){
			$sc_name[0]='NULL';
		}
	}
	
	//MAKE COMMA SEPEARATED OF ALL SEARCH CRITERIA
	$grp_id= (sizeof($grp_id)>0) ? implode(',',$grp_id) : '';
	$sc_name= (sizeof($sc_name)>0) ? implode(',',$sc_name) : '';
	$Physician= (sizeof($Physician)>0) ? implode(',',$Physician) : '';
	$credit_physician= (sizeof($crediting_provider)>0) ? implode(',',$crediting_provider) : '';
	$cpt_cat_id= (sizeof($cpt_cat_id)>0) ? implode(',',$cpt_cat_id) : '';
	$Procedure= (sizeof($Procedure)>0) ? implode(',',$Procedure) : '';
	$ins_group_str= (sizeof($ins_group)>0) ? implode(',',$ins_group) : '';
	$cpt_cat_2= (sizeof($cpt_cat_2)>0) ? implode(',',$cpt_cat_2) : '';

	
	//cpt_cat_id Procedure
	
	//---------------------------------------

	// Collecting Insurance Companies and groups
	if(empty($ins_carriers)==false){ $tempInsArr[] = implode(',',$ins_carriers); }
	if(empty($ins_group)==false){ $tempInsArr[] = implode(',',$ins_group); }
	$tempSelIns = implode(',', $tempInsArr);
	$tempInsArr = array();
	if(empty($tempSelIns)==false){
	$tempInsArr = explode(',', $tempSelIns);
	}
	$tempInsArr = array_unique($tempInsArr);
	$insId = implode(',', $tempInsArr);
	$arrInsurance=array();
	if(sizeof($tempInsArr)>0){
		$arrInsurance = array_combine($tempInsArr,$tempInsArr);
	} 
	unset($tempInsArr);

	//--- GET GROUP NAME ---
	$group_name = $CLSReports->report_display_selected($grp_id,'group', '1', $allGrpCount);
	$cpt_cat_name = $CLSReports->report_display_selected($cpt_cat_id,'cpt_category', '1', $allCPTCatCount);
	$procedure_name = $CLSReports->report_display_selected($Procedure,'procedure', '1', $allProcCount);
	$practice_name = $CLSReports->report_display_selected($sc_name,'practice', '1', $allFacCount);
	$physician_name = $CLSReports->report_display_selected($Physician,'physician', '1', $allPhyCount);
	$crediting_physician_name = $CLSReports->report_display_selected($credit_physician,'physician', '1', $allCrPhyCount);
	$insurance_name = $CLSReports->report_display_selected($insId,'insurance', '1', $allInsCount);
	$cpt_cat_name = (strlen($cpt_cat_name)>20) ? substr($cpt_cat_name,0,20).'...' : $cpt_cat_name;
	
	//--- GET RVU VALUES
	if($rvu=='1'){
		$rs=imw_query("Select rvu_records.cpt_fee_id, rvu_records.work_rvu, rvu_records.pe_rvu,
		rvu_records.mp_rvu, cpt_fee_tbl.cpt4_code FROM rvu_records LEFT JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = rvu_records.cpt_fee_id");
		while($res=imw_fetch_array($rs)){
			$allRVUValues[$res['cpt_fee_id']] = $res;
		}
	}

	// MAKE Search Criteria Vars
	$varCriteria=$grp_id.'~'.$cpt_cat_id.'~'.$Procedure.'~'.$sc_name.'~'.$Physician.'~'.$insId;
	$varCriteria.='~'.$DateRangeFor.'~'.$dayReport.'~'.$fromDate.'~'.$toDate.'~'.$total_method.'~'.$groupby.'~'.$process.'~'.$dispChart;
	$varCriteria.='~'.$includeDelIns.'~'.$includeDelCPT.'~'.$sort_by.'~'.$credit_physician.'~'.$chksamebillingcredittingproviders;

	
	// GET ADJUSTMENT IF SEARCH WITH DOP/DOT
	if($DateRangeFor!='date_of_service'){
		//	GET CREDIT AMOUNT
		$credit_qry_rs = $CLSReports->__getCreditappliedRept('', $mode='CHARGELISTID',$Start_date,$End_date, '', '', '', '', '', '', 'yes', $checkDel='no', '', $reptByForFun,$insId);
		while($credit_qry_res = imw_fetch_assoc($credit_qry_rs)){
			$chargelist_id_adjust = $credit_qry_res['charge_list_detail_id_adjust'];
			if($credit_qry_res['crAppliedTo']=="adjustment"){ 
				if($credit_qry_res['type']=='Insurance'){
					$pay_crd_deb_arr[$chargelist_id_adjust]['Insurance'][] = $credit_qry_res['amountApplied'];
				}else{
					$pay_crd_deb_arr[$chargelist_id_adjust]['Patient'][] = $credit_qry_res['amountApplied'];				
				}
				if($chargelist_id_adjust!=''){ $arrAdjustmentEncs[$chargelist_id_adjust] = $chargelist_id_adjust; }
			}
		}unset($credit_qry_res);

		//	GET DEBIT AMOUNT
		$debit_qry_rs = $CLSReports->__getDebitappliedData('', $mode='CHARGELISTID',$Start_date,$End_date, '', '', '', '', '', '', 'yes', $checkDel='no', '', $reptByForFun,$insId);
		while($debit_qry_res = imw_fetch_assoc($debit_qry_rs)){
			$chargelist_id = $debit_qry_res['charge_list_detail_id'];
			if($debit_qry_res['crAppliedTo']=="adjustment"){
				if($debit_qry_res['type']=='Insurance'){
					$pay_crd_deb_arr[$chargelist_id]['Insurance'][] = '-'.$debit_qry_res['amountApplied'];
				}else{
					$pay_crd_deb_arr[$chargelist_id]['Patient'][] = '-'.$debit_qry_res['amountApplied'];
				}
				if($chargelist_id!=''){ $arrAdjustmentEncs[$chargelist_id] = $chargelist_id; }
			}
		}unset($debit_qry_res);

		// GET DEFAULT WRITEOFF
		$writeOffQryRs = $CLSReports->__getDetailWriteOffAmtNew('',$Start_date,$End_date, '', '', $mode='CHARGELISTID', $insId, $reptByForFun, $checkDel='yes', '', '', '', 'no', 'yes');
		while($writeOffQryRes = imw_fetch_assoc($writeOffQryRs)){
			$chargelist_id = $writeOffQryRes['charge_list_detail_id'];
			$normalWriteOffAmt[$chargelist_id]= $writeOffQryRes['write_off_amount'];
			if($chargelist_id!=''){ $arrAdjustmentEncs[$chargelist_id] = $chargelist_id; }
		}unset($writeOffQryRes);

		// GET ADJUSTMENT AMOUNTS
		$arrAdjustmentAmt = $CLSReports->getReportAdjustmentAmtCopy('', 'CHARGELISTID','1',$Start_date,$End_date, $checkDel='no', $reptByForFun, $insId, '', 'yes');
		foreach($arrAdjustmentAmt as $chargelist_id => $writeOffAmt){
			if($chargelist_id!='') { $arrAdjustmentEncs[$chargelist_id] = $chargelist_id; }
		}
	}
	$search = 'DOP';
	if($DateRangeFor=='transaction_date'){ $search='DOT'; }
	
	if($DateRangeFor == 'date_of_service'){
		$search = 'DOS';
		require_once(dirname(__FILE__).'/productivity_procedure_charge_list.php');
		$main_encounter_id_str = join(',',$main_encounter_id_arr);
		$splitted_encounters = array_chunk($main_encounter_id_arr,1500);
		$getPaymentDetailsArr1 = $getPaymentDetailsArr = array();

		foreach($splitted_encounters as $arr){
			$str_splitted_encs 	 = implode(',',$arr);
			$getPaymentDetailsArr1[] = $CLSReports->__getPaymentDetails($str_splitted_encs,'','','','', 'ENCOUNTERID');
		}
		foreach($getPaymentDetailsArr1 as $pymntArr){
			$getPaymentDetailsArr = array_merge($pymntArr,$getPaymentDetailsArr);
		}
		unset($getPaymentDetailsArr1);
	}else{
		require_once(dirname(__FILE__).'/productivity_procedure_payments.php');
		$main_encounter_id_str = join(',',$main_encounter_id_arr);
		$splitted_encounters = array_chunk($main_encounter_id_arr,1500);
		$getPaymentDetailsArr1 = $getPaymentDetailsArr = array();
		foreach($splitted_encounters as $arr){
			$str_splitted_encs 	 = implode(',',$arr);
			$getPaymentDetailsArr1[] = $CLSReports->__getPaymentDetails($str_splitted_encs,$Start_date,$End_date,$DateRangeFor, '', $mode='ENCOUNTERID', '', $checkDel='no');
		}
		foreach($getPaymentDetailsArr1 as $pymntArr){
			$getPaymentDetailsArr = array_merge($pymntArr,$getPaymentDetailsArr);
		}unset($getPaymentDetailsArr1);
	}

	$mainChargeListIdsStr = implode(",",$charge_list_detail_id_arr);
	$main_encounter_id_arr_str = implode(',',$main_encounter_id_arr);
	
	if(count($mainResultArr)>0){		
		//	GET CREDIT AMOUNT
		if($DateRangeFor=='date_of_service'){
			$credit_qry_rs = $CLSReports->__getCreditappliedRept($mainChargeListIdsStr, $mode='CHARGELISTID');
			while($credit_qry_res = imw_fetch_assoc($credit_qry_rs)){
				$chargelist_id_adjust = $credit_qry_res['charge_list_detail_id_adjust'];
				if($credit_qry_res['crAppliedTo']=="adjustment"){ 
					if($credit_qry_res['type']=='Insurance'){
						$pay_crd_deb_arr[$chargelist_id_adjust]['Insurance'][] = $credit_qry_res['amountApplied'];
					}else{
						$pay_crd_deb_arr[$chargelist_id_adjust]['Patient'][] = $credit_qry_res['amountApplied'];				
					}
				}
			}unset($credit_qry_res);
			//	GET DEBIT AMOUNT
			$debit_qry_rs = $CLSReports->__getDebitappliedData($mainChargeListIdsStr, $mode='CHARGELISTID');
			while($debit_qry_res = imw_fetch_assoc($debit_qry_rs)){
				$chargelist_id = $debit_qry_res['charge_list_detail_id'];
				if($debit_qry_res['crAppliedTo']=="adjustment"){
					if($debit_qry_res['type']=='Insurance'){
						$pay_crd_deb_arr[$chargelist_id]['Insurance'][] = '-'.$debit_qry_res['amountApplied'];
					}else{
						$pay_crd_deb_arr[$chargelist_id]['Patient'][] = '-'.$debit_qry_res['amountApplied'];
					}
				}
			}unset($debit_qry_res);
		}

		//--- GET MAIN PAYMENT ARRAY -----------
		$mainEncounterPayArr = array();
		for($i=0;$i<count($getPaymentDetailsArr);$i++){
			$fetchPayment=true;
			$charge_list_detail_id = $getPaymentDetailsArr[$i]['charge_list_detail_id'];
			$mainChargeListIdsArr[$charge_list_detail_id] = $charge_list_detail_id;
			$encounter_id = $getPaymentDetailsArr[$i]['encounter_id'];
			$paidForProc = $getPaymentDetailsArr[$i]['paidForProc'];
			$insCompId = $getPaymentDetailsArr[$i]['insProviderId'];
			
			if($getPaymentDetailsArr[$i]['paymentClaims'] == 'Negative Payment'){
				$paidForProc = '-'.$paidForProc;
			}

			//IF COPAY
			if($charge_list_detail_id<=0){
				$fetchPayment=false;
				$charge_list_detail_id= $arrCopayChgDetId[$encounter_id];
				if($charge_list_detail_id_arr[$charge_list_detail_id]){
					$fetchPayment=true;	
				}
			}
			if($fetchPayment==true){
				if($getPaymentDetailsArr[$i]['paidBy'] == 'Patient' || $getPaymentDetailsArr[$i]['paidBy'] == 'Res. Party')
				{
					$patPayDetArr['ALL']['patPaid'][$charge_list_detail_id][] = $paidForProc;
				}
				else if($getPaymentDetailsArr[$i]['paidBy'] == 'Insurance'){
					if(empty($arrInsurance)===true || (empty($arrInsurance)===false && $arrInsurance[$insCompId])){	
						$patPayDetArr['ALL']['insPaid'][$charge_list_detail_id][] = $paidForProc;
					}else{
						$paidForProc=0;
					}
				}
				$mainEncounterPayArr['ALL'][$charge_list_detail_id][] = $paidForProc;				
			}
		}unset($getPaymentDetailsArr);
		
		// GET ADJUSTMENT AMOUNTS
		if($DateRangeFor=='date_of_service'){
			// GET DEFAULT WRITEOFF
			$normalWriteOffAmt=array();
			$splitted_encounters = array_chunk($charge_list_detail_id_arr,1500);
			$getPaymentDetailsArr1 =  array();
			foreach($splitted_encounters as $arr){
				$str_splitted_encs 	 = implode(',',$arr);
				$writeOffQryRs = $CLSReports->__getDetailWriteOffAmt($str_splitted_encs, '', '', '', '',$mode='CHARGELISTID');
				while($writeOffQryRes = imw_fetch_assoc($writeOffQryRs)){
					$chargelist_id = $writeOffQryRes['charge_list_detail_id'];
					$normalWriteOffAmt[$chargelist_id]+= $writeOffQryRes['write_off'];
				}unset($writeOffQryRes);			
			}
			unset($getPaymentDetailsArr1);
			// ADJUSTMENT
			$getPaymentDetailsArr1 = $arrAdjustmentAmt = array();
			foreach($splitted_encounters as $arr){
				$str_splitted_encs 	 = implode(',',$arr);
				$getPaymentDetailsArr1 = $CLSReports->getReportAdjustmentAmtCopy($str_splitted_encs, $mode='CHARGELISTID');
				foreach($getPaymentDetailsArr1 as $chgId => $adjAmt){
					$arrAdjustmentAmt[$chgId]+=$adjAmt;
				} unset($getPaymentDetailsArr1);		
			}
		}
	}

	//FOR DELETED CHARGES AND PAYMENTS ----------------------
	if($DateRangeFor != 'date_of_service'){
		$whereQry='';
		$whereOperQry='';
		if(empty($sc_name) == false){
			$whereQry.= " AND patChg.facility_id IN ($sc_name)";
		}
		if(empty($grp_id) == false){
			$whereQry.= " AND patChg.gro_id IN ($grp_id)";
		}
		if(empty($Physician) === false or empty($credit_physician) === false){
			if(empty($Physician) === false and empty($credit_physician) === false){
				$whereQry.= " and (patChg.primary_provider_id_for_reports IN ($Physician) 
				and patChg.secondaryProviderId IN ($credit_physician))";
			}
			else if(empty($Physician) === false){
				$whereQry.= " and patChg.primary_provider_id_for_reports IN ($Physician)";
			}
			else if(empty($credit_physician) === false){
				$whereQry.= " and patChg.secondaryProviderId IN ($credit_physician)";
			}
		}
		if($chksamebillingcredittingproviders==1){
			$whereQry.= " and patChg.primary_provider_id_for_reports!=patChg.secondaryProviderId";							
		}		
		if(empty($tempCPTStr) === false){
			$whereQry.= " and patChgDet.procCode in($tempCPTStr)";
		}
		if(empty($insId) === false){
			$whereQry.= " AND (patChg.primaryInsuranceCoId IN ($insId)
						OR patChg.secondaryInsuranceCoId IN ($insId) 
						OR patChg.tertiaryInsuranceCoId IN ($insId))";	
		}

				
		// GET DELETED CHARGES
		$arrVoidPay = array();
		$qry = "SELECT patChg.patient_id, 
					patChg.encounter_id, 
					patChg.primary_provider_id_for_reports  as 'primaryProviderId', 
					patChg.facility_id, patChg.first_posted_date, 
					patChg.date_of_service,
					patChgDet.trans_del_date,
					patChgDet.charge_list_detail_id, 
					patChgDet.del_operator_id, 
					patChgDet.totalAmount, 
					pd.fname,
					pd.mname,
					pd.lname  
			FROM patient_charge_list patChg 
			JOIN patient_charge_list_details patChgDet ON patChgDet.charge_list_id = patChg.charge_list_id 
			LEFT JOIN patient_data pd ON pd.id = patChg.patient_id 
			WHERE patChgDet.del_status='1' AND patChg.primary_provider_id_for_reports>0 
			AND (patChgDet.trans_del_date BETWEEN '$Start_date $start_time' AND '$End_date $end_time') 
			AND patChgDet.procCode>0
			".$whereQry.$whereOperQry." ORDER BY ";
			$qry.= ($sort_by == 'patient') ? "pd.lname,pd.fname,date_of_service" : "date_of_service, pd.lname, pd.fname";
		//echo $qry;
		$rs=imw_query($qry);

		while($res=imw_fetch_assoc($rs)){
			//IF DELETED BEFORE FIRST POSTED DATE THEN IGNORE THAT
			if($res['first_posted_date']=='0000-00-00' || $res['first_posted_date'] <= $res['trans_del_date']){
			
				$primaryProviderId=$res['primaryProviderId'];
				$facility_id=$res['facility_id'];
				$encID = $res['encounter_id'];	
				$chgDetId= $res['charge_list_detail_id'];
				$res['date_of_service']= date($phpDateFormat, strtotime($res['date_of_service']));
				$res['deleted_date']= date($phpDateFormat, strtotime($res['trans_del_date']));
				$res['trans_del_date']= date('Y-m-d', strtotime($res['trans_del_date']));
				
				$firstGroup = $primaryProviderId;
				$secGroup = $facility_id;
				if($groupby=='Facility'){
					$firstGroup = $facility_id;
					$secGroup = $primaryProviderId;
				}
		
				$delChargesArr[$firstGroup][$secGroup][$encID][$chgDetId] = $res;
				$delChargesSummArr[$firstGroup][$secGroup]+= $res['totalAmount']; // FOR SUMMARY
				$arrVoidPay[$firstGroup][$secGroup]['charges'] += $res['totalAmount']; 
				$arrVoidPay[$firstGroup][$secGroup]['detail'][$encID]['charges'] += $res['totalAmount']; 
				$arrVoidPay[$firstGroup][$secGroup]['detail'][$encID]['dos'] = $res['date_of_service'];
				$arrVoidPay[$firstGroup][$secGroup]['detail'][$encID]['pt_id'] = $res['patient_id'];
				$arrVoidPay[$firstGroup][$secGroup]['detail'][$encID]['opr'][$res['del_operator_id']] = $res['del_operator_id'];
				$arrVoidPay[$firstGroup][$secGroup]['detail'][$encID]['pt_name']= core_name_format($res['lname'], $res['fname'], $res['mname']);
			}
		}

		//GET DELETED PAYMENTS and ADJUSTMENTS
		$delAdjEncArr=array();			
		$arrDelAdj = $CLSReports->getDelReportAdjustmentAmt($Start_date, $End_date, $Physician, $sc_name, '', $grp_id, '', '', '', 'CHARGELISTDETIDS');
		$delAdjEncArr=array_keys($arrDelAdj);

		//DELETED COPAY AMOUNTS ARE NOT FETCHING BECAUSE THESE DELETED ARE NOT FETCHED IN UPPER PAYMENT BLOCKS 
		//BECAUSE IN DETAIL TABLES WHILE DELETING COPAY PAYMENT COPAY APPLIED ID BECOMES ZERO. SO IT WAS NOT MATHCING WITH FINANCIAL DASHBOARD
		$delPaidQry = "SELECT paidInfo.encounter_id, 
		  paidInfo.paid_by, 
		  paidInfo.payment_mode, 
		  paidInfo.paymentClaims,
		  
		  paidDetInfo.paidForProc + paidDetInfo.overPayment as paidForProc,		
		  paidDetInfo.charge_list_detail_id, 
		  paidDetInfo.del_operator_id, 
		  DATE_FORMAT(paidDetInfo.deleteDate, '".$dateFormat."') as 'deleteDate' 
		  
		FROM patient_chargesheet_payment_info paidInfo 
		JOIN patient_charges_detail_payment_info paidDetInfo ON paidDetInfo.payment_id = paidInfo.payment_id 
		WHERE paidDetInfo.deletePayment='1' 
		AND (paidDetInfo.deleteDate BETWEEN '$Start_date' AND '$End_date') 
		AND charge_list_detail_id>0";
		if(empty($operatorName)==false){
			$delPaidQry.= " AND paidInfo.operatorId in($operatorName)";
		}

		$delPaidRs = imw_query($delPaidQry);
		while($delPaidRes = imw_fetch_assoc($delPaidRs)){
			
			$delEncId=$delPaidRes['encounter_id'];
			$delChgDetId=$delPaidRes['charge_list_detail_id'];
			$delEncPhy=$delPaidRes['primaryProviderId'];
			$delEncFac=$delPaidRes['facility_id'];

			//----temp block
			$mode = $delPaidRes['payment_mode'];
			$paid_by = $delPaidRes['paid_by'];
			$delOprId = $delPaidRes['del_operator_id'];
			
			if($delPaidRes['paymentClaims'] == 'Negative Payment'){
				$delPaidRes['paidForProc'] = '-'.$delPaidRes['paidForProc'];
			}
			if($mode=="Check"){
				$tempDelPay[$delChgDetId][$mode][$paid_by]+= $delPaidRes['paidForProc'];
			}else{
				$tempDelPay[$delChgDetId][$mode]+= $delPaidRes['paidForProc'];
			}
			$tempDelPay[$delChgDetId]['del_opr_id'][$delOprId]= $delOprId;
			$tempDelPayEnc[$delEncId]=$delEncId;
			//--------------
		}unset($delPaidRs);
		
		//MAKE DELETED PAYMENT FINAL ARRAY
		if(sizeof($tempDelPayEnc)>0 || sizeof($delAdjEncArr)>0){
			$arrayCheckEnc=array();
			$arrDelPaidEncs=array();
			if(sizeof($tempDelPayEnc)>0){
				$arrDelPaidEncs= $tempDelPayEnc;
			}
			$mergedEnc=array_merge($arrDelPaidEncs, $delAdjEncArr);
			
			if(sizeof($mergedEnc)>0){
				$mergedEncStr=implode(',', $mergedEnc);
				$qry="Select patChg.patient_id, patChg.encounter_id, DATE_FORMAT(patChg.date_of_service, '".$dateFormat."') as 'date_of_service',
				patChg.primary_provider_id_for_reports  as 'primaryProviderId', patChg.facility_id, patChgDet.charge_list_detail_id,
				pd.fname, pd.lname, pd.mname  
				FROM patient_charge_list patChg 
				JOIN patient_charge_list_details patChgDet ON patChgDet.charge_list_id = patChg.charge_list_id 
				LEFT JOIN patient_data pd ON pd.id = patChg.patient_id 
				WHERE patChg.encounter_id IN(".$mergedEncStr.") AND patChg.primary_provider_id_for_reports>0 
				".$whereQry." ORDER BY patChg.charge_list_id, pd.lname, pd.fname";
				$rs= imw_query($qry);
				
				while($delPaidRes=imw_fetch_array($rs)){
					$delEncId=$delPaidRes['encounter_id'];
					$delChgDetId=$delPaidRes['charge_list_detail_id'];
					$delEncPhy=$delPaidRes['primaryProviderId'];
					$delEncFac=$delPaidRes['facility_id'];
					$ifEncFetched=0;
					
					$firstGrp=$delEncPhy;	$secGrp=$delEncFac;	if(strtolower($groupby)=='Facility'){$firstGrp=$delEncFac;	$secGrp=$delEncPhy;}					
	
					if($tempDelPay[$delChgDetId]){
						$ifEncFetched=1;					
					}
					
					if($tempDelPay[$delChgDetId]['Cash']){
						$arrVoidPay[$firstGrp][$secGrp]['cash']+= $tempDelPay[$delChgDetId]['Cash']; 
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$delEncId]['cash'] += $tempDelPay[$delChgDetId]['Cash']; 
					}
					if($tempDelPay[$delChgDetId]['Check']['Patient'] || $tempDelPay[$delChgDetId]['Check']['Res. Party']){
						if($tempDelPay[$delChgDetId]['Check']['Patient']){
							$arrVoidPay[$firstGrp][$secGrp]['pt_check'] += $tempDelPay[$delChgDetId]['Check']['Patient'];
							$arrVoidPay[$firstGrp][$secGrp]['detail'][$delEncId]['pt_check'] += $tempDelPay[$delChgDetId]['Check']['Patient'];
						}else{
							$arrVoidPay[$firstGrp][$secGrp]['pt_check'] += $tempDelPay[$delChgDetId]['Check']['Res. Party'];
							$arrVoidPay[$firstGrp][$secGrp]['detail'][$delEncId]['pt_check'] += $tempDelPay[$delChgDetId]['Check']['Res. Party'];
						}
					}
					if($tempDelPay[$delChgDetId]['Check']['Insurance']){
						$arrVoidPay[$firstGrp][$secGrp]['ins_check'] += $tempDelPay[$delChgDetId]['Check']['Insurance'];
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$delEncId]['ins_check'] += $tempDelPay[$delChgDetId]['Check']['Insurance'];
					}
					if($tempDelPay[$delChgDetId]['Credit Card']){
						$arrVoidPay[$firstGrp][$secGrp]['CC'] += $tempDelPay[$delChgDetId]['Credit Card'];
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$delEncId]['CC'] += $tempDelPay[$delChgDetId]['Credit Card'];
					}
					if($tempDelPay[$delChgDetId]['EFT']){
						$arrVoidPay[$firstGrp][$secGrp]['EFT'] += $tempDelPay[$delChgDetId]['EFT'];
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$delEncId]['EFT'] += $tempDelPay[$delChgDetId]['EFT'];
					}
					if($tempDelPay[$delChgDetId]['Money Order']){
						$arrVoidPay[$firstGrp][$secGrp]['MO'] +=$tempDelPay[$delChgDetId]['Money Order'];
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$delEncId]['MO'] += $tempDelPay[$delChgDetId]['Money Order'];
					}
					if($tempDelPay[$delChgDetId]['VEEP']){
						$arrVoidPay[$firstGrp][$secGrp]['VEEP'] +=$tempDelPay[$delChgDetId]['VEEP'];
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$delEncId]['VEEP'] += $tempDelPay[$delChgDetId]['VEEP'];
					}
	
					//DEL ADJUSTMENTS
					if(sizeof($arrDelAdj[$delEncId])>0){
						
						foreach($arrDelAdj[$delEncId] as $delInfo){

							if($delInfo['DETAIL_ID']==$delChgDetId){

								$arrVoidPay[$firstGrp][$secGrp]['adjustments']+= $delInfo['AMT'];
								$arrVoidPay[$firstGrp][$secGrp]['detail'][$delEncId]['adjustments']+= $delInfo['AMT'];

								$oprIds = $delInfo['OPERATOR'];
								$arrVoidPay[$firstGrp][$secGrp]['detail'][$delEncId]['opr'][$oprIds] = $oprIds;
								$ifEncFetched=1;		
							}
						}
					}

					if($ifEncFetched==1){
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$delEncId]['dos'] = $delPaidRes['date_of_service']; 
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$delEncId]['pt_id'] = $delPaidRes['patient_id'];
						$arrVoidPay[$firstGrp][$secGrp]['detail'][$delEncId]['pt_name'] = core_name_format($delPaidRes['lname'], $delPaidRes['fname'], $delPaidRes['mname']);
						
						foreach($tempDelPay[$delChgDetId]['del_opr_id'] as $oprId){
							$arrVoidPay[$firstGrp][$secGrp]['detail'][$delEncId]['opr'][$oprId] = $oprId;
						}
					}
					//$arrayCheckEnc[$delEncId]=$delEncId;

				}unset($delPaidRs);
				unset($tempDelPay);
			}
		}
	}// END DELETED


	if($process == "Summary"){
		//-- GET SUMMARY DISPLAY DATA ---------
		require_once(dirname(__FILE__).'/summaryProcedure.php');
	}
	else{
		//--- GET INSURANCE COMPANIES DETAILS ----					
		$primaryInsIdStr = join(',',$primaryInsIdArr);
		$qry = "Select * from insurance_companies";
		if(empty($primaryInsIdStr)==false){
			$qry.= " where id in ($primaryInsIdStr)";
		}
		$insQryRs=	imw_query($qry);
		$insCompanyArr = array();
		while($insQryRes = imw_fetch_assoc($insQryRs)){	
			$id = $insQryRes['id'];
			$in_house_code = trim($insQryRes['in_house_code']);
			if(empty($in_house_code)){
				$in_house_code = substr($insQryRes['in_house_code'],0,9);
			}
			$insCompanyArr[$id] = $in_house_code;
		}
		//-- GET DETAIL DISPLAY DATA ---------
		require_once(dirname(__FILE__).'/detailProcedure.php');
	}
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
		
		$qry="Select id FROM reports_searches WHERE report_name='".$search_name."' AND report='productivity_procedural_criteria'";
		$rs=imw_query($qry);
		if(imw_num_rows($rs)<=0 || (imw_num_rows($rs)>0 && $qryPart=='Update')){
			$qry="$qryPart reports_searches SET uid='".$_SESSION['authId']."', report='productivity_procedural_criteria',
			search_data='".addslashes($varCriteria)."', saved_date='".date('Y-m-d H:i:s')."' ".$fieldPart.$qryWhere;
			imw_query($qry);
		}

	}
}
//---------------------

//--- CREATE PDF FILE FOR PRINTING -----
if($printFile == true and $data != ''){
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csvFileData= $styleHTML.$csvFileData;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$data;

	//--- CREATE HTML FILE FOR PDF ----
	if($callFrom!='scheduled'){
		$file_location = write_html($strHTML);
	}

	if(file_exists($file_location)){
		$size_bytes = filesize($file_location);
		$size_mb = $size_bytes/1048576;
	}
	
	if((isset($GLOBALS['rp_html_size']) && $size_mb > $GLOBALS['rp_html_size']) || (!isset($GLOBALS['rp_html_size']) && $size_mb > 4)){
		$tempDir = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/UserId_".$_SESSION['authId']."/tmp/";
		if(!is_dir($tempDir)){
			mkdir($tempDir,0777, true);
		}
		$file = $tempDir.'CPT_Analytic';
		if(file_exists($file.'.csv')){

			$fParts=explode("/", $file);
			$zipName = $fParts[count($fParts)-1].'.zip';
			$zip = new ZipArchive();
			if ($zip->open($file.'.zip', ZipArchive::CREATE)!==TRUE) {
				exit("cannot open <$zipName>\n");
			}
			$zip->addFile($file.'.csv', 'CPT_Analytic.csv');
			$zip->close();
			
			$createZip = 1;
			$file = $file.'.zip';
		}
		if($callFrom!='scheduled'){
			echo '<div class="text-center alert alert-info">Zip file for result is created.</div>';
		}
	} else{
		if($callFrom!='scheduled'){
			echo $csvFileData;	
		}
	}
}else{
	if($callFrom!='scheduled'){
		echo $csvFileData = '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}
?>