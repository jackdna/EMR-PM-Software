<?php

$columnsArr = array();
$none_charge_list = array();
$get_charge_list_id = array();
$printFile = false;
$sortByPayment = NULL;

$reptByForFun = 'dot';

if(stristr($sortBy, 'DOS')){
	$sortBy= 'patient_charge_list.date_of_service desc,patient_data.lname';
	$sortByPayment = ' order by patient_charge_list.date_of_service desc,patient_data.lname';
	
}else if(stristr($sortBy, 'patientName')){
	$sortBy = 'patient_data.lname,patient_data.fname';
	$sortByPayment = ' order by patient_data.lname,patient_data.fname';
}
else if(stristr($sortBy, 'Charges')){
	$orderByTemp='Charges';
	$sortBy = ' patChgDet.totalAmount DESC';
	$sortByPayment = ' order by patChgDet.totalAmount DESC';
}
else{
	$sortBy = 'patient_charge_list.date_of_service desc';
}



// MAIN WHERE BLOCK
if(empty($sc_name) == false){
	$mainWhere .= " and patChg.facility_id IN ($sc_name)";
}
if(empty($grp_id) == false){
	$mainWhere.= " and patChg.gro_id IN ($grp_id)";
}
if(empty($Physician) == false){
	$mainWhere.= " and patChg.primary_provider_id_for_reports IN ($Physician)";
}
//-------------------

// GET ADJUSTMENT AND WRITE-OFF AMOUNTS
$paid_encounter_arr = array();
$adjAmtArr = array();
$tempAdjArr=array();
//GET CREDIT AMOUNT
$strCreditDebitEncs='';
$credit_qry_rs = $CLSReports->__getCreditappliedRept('', $mode='ENCOUNTERID',$startDate, $endDate, '', $operatorId, '', '', '', '', '', $checkDel='yes', '', $reptByForFun, '','','', $hourFrom, $hourTo);
while ($credit_qry_res = imw_fetch_assoc($credit_qry_rs)) {
	$encounter_id_adjust = $credit_qry_res['crAppliedToEncId_adjust'];
	$chgDetId_adjust = $credit_qry_res['charge_list_detail_id_adjust'];
	$oprId = $credit_qry_res['operatorApplied'];
	if($credit_qry_res[$i]['crAppliedTo']=="adjustment"){ 
		$pay_crd_deb_arr[$oprId][$encounter_id_adjust][] = $credit_qry_res[$i]['amountApplied'];
		if($encounter_id_adjust!='') { $arrCreditDebitEncs[$encounter_id_adjust] = $encounter_id_adjust; }
		$arrTempPaidChgDetId[$chgDetId_adjust] =$chgDetId_adjust;
		$arrTempOperData[$chgDetId_adjust]['oper'][$oprId] =  $oprId;
	}
}

//	GET DEBIT AMOUNT
$debit_qry_rs = $CLSReports->__getDebitappliedData('', $mode='ENCOUNTERID',$startDate, $endDate, '', $operatorId, '', '', '', '', '', $checkDel='yes', '', $reptByForFun, '','','', $hourFrom, $hourTo);
while ($debit_qry_res = imw_fetch_assoc($debit_qry_rs)) {
	$encounter_id = $debit_qry_res['crAppliedToEncId'];
	$chgDetId = $debit_qry_res['charge_list_detail_id'];
	$oprId = $debit_qry_res['operatorApplied'];
	if($debit_qry_res['crAppliedTo']=="adjustment"){
		$pay_crd_deb_arr[$oprId][$encounter_id][] = '-'.$debit_qry_res['amountApplied'];
		if($encounter_id!='') { $arrCreditDebitEncs[$encounter_id] = $encounter_id; }
		$arrTempPaidChgDetId[$chgDetId] =$chgDetId;
		$arrTempOperData[$chgDetId]['oper'][$oprId] =  $oprId;
	}
}
if(sizeof($arrCreditDebitEncs)>0){
	$strCreditDebitEncs = implode(',', $arrCreditDebitEncs);
}
//----------------------------------------

//--- GET PAYMENT RECORDS ----------
$payQry = "Select patient_charge_list.encounter_id, 
							patient_charges_detail_payment_info.payment_details_id,
							patient_charges_detail_payment_info.charge_list_detail_id,
							
							patient_charges_detail_payment_info.paidForProc, 
							patient_charges_detail_payment_info.overPayment,
							patient_charges_detail_payment_info.deletePayment,
							patient_charges_detail_payment_info.del_operator_id,
							patient_charges_detail_payment_info.deleteDate,
							patient_charges_detail_payment_info.deleteTime,
							
							patient_chargesheet_payment_info.payment_id,
							patient_chargesheet_payment_info.operatorId, 
							patient_chargesheet_payment_info.paid_by, 
							patient_charge_list.facility_id, 
							patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId',
							users.lname as usersLname,
							users.fname as usersFname, 
							patient_chargesheet_payment_info.*
							FROM patient_charges_detail_payment_info
							JOIN patient_chargesheet_payment_info on 
							patient_chargesheet_payment_info.payment_id = patient_charges_detail_payment_info.payment_id 
							 
							JOIN users on users.id = patient_chargesheet_payment_info.operatorId 
							
							JOIN patient_charge_list 
							ON 
							(
								patient_charge_list.encounter_id = patient_chargesheet_payment_info.encounter_id 
								AND patient_charge_list.del_status='0'
							)
							
							WHERE (patient_chargesheet_payment_info.transaction_date between '$startDate' and '$endDate')";
if($hourFrom!='' && $hourTo!=''){
	$payQry.= " AND (DATE_FORMAT(patient_charges_detail_payment_info.entered_date, '%H:%i:%s') BETWEEN '$hourFrom' AND '$hourTo')";					
}
if(empty($operatorId)==false){
	$payQry.=" AND patient_chargesheet_payment_info.operatorId in ($operatorId)";
}
$payQry.=" ORDER BY patient_chargesheet_payment_info.transaction_date";

$paymentQryRes = $mainPayment_Arr = $payment_id_arr = $del_payment_arr = array();

$qryRs = imw_query($payQry);
while($qryRes = imw_fetch_array($qryRs)){
	$paymentQryRes[] = $qryRes;
}
for($i=0;$i<count($paymentQryRes);$i++){
	$facility_id = $paymentQryRes[$i]['facility_id'];
	$primaryProviderId = $paymentQryRes[$i]['primaryProviderId'];
	$encounter_id = $paymentQryRes[$i]['encounter_id'];
	$payment_details_id = $paymentQryRes[$i]['payment_details_id'];
	$charge_list_detail_id = $paymentQryRes[$i]['charge_list_detail_id'];
	$oprId = $paymentQryRes[$i]['operatorId'];

	if(($paymentQryRes[$i]['deletePayment']==0) || ($paymentQryRes[$i]['deletePayment']==1 && $paymentQryRes[$i]['deleteDate'] > $endDate && $paymentQryRes[$i]['deleteTime']>$hourTo)){
		if($encounter_id>0){
			$paid_encounter_arr[$encounter_id] = $encounter_id;	
		}

		$payment_id = $paymentQryRes[$i]['payment_id'];
		$mainPayment_Arr[$encounter_id][$oprId][] = $paymentQryRes[$i];

		$operatorInitial = $userNameTwoCharArr[$oprId];
		//$mainPayment_Arr[$encounter_id][$oprId][$payment_details_id]['operatorInitial'] = $operatorInitial;
		$payment_id_arr[] = $payment_id;
		//------GET charge_list_detail_id FOR COPAY PAYMENTS FOR DEPARTMENT ARRAY----------
		if($charge_list_detail_id == 0){
			$chrlistQry = imw_query("SELECT patient_charge_list_details.charge_list_detail_id FROM patient_chargesheet_payment_info
			JOIN  patient_charge_list ON patient_charge_list.encounter_id   = 	patient_chargesheet_payment_info.encounter_id 	 
			JOIN  patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id 
			WHERE patient_chargesheet_payment_info.payment_id = '".$payment_id."' AND patient_charge_list_details.coPayAdjustedAmount = 1");
			$row = imw_fetch_assoc($chrlistQry);
			$charge_list_detail_id = $row['charge_list_detail_id'];
		}
		//-------------------------------------
		//$mainPayment_Arr[$encounter_id][$oprId]['charge_list_detail_id'][$charge_list_detail_id][] = $paymentQryRes[$i];
		//-------------------------------------
		//OPERATOR TEMP DATA 
		$oprPaidAmt=0;
		$oprPaidAmt=$paymentQryRes[$i]['paidForProc'] + $paymentQryRes[$i]['overPayment'];
		if($paymentQryRes[$i]['paymentClaims']=='Negative Payment'){
			$oprPaidAmt='-'.$oprPaidAmt;
		}
		$arrTempOperData[$charge_list_detail_id]['oper'][$oprId]= $oprId;
		$arrTempOperPaid[$charge_list_detail_id][$oprId]+= $oprPaidAmt;

		if($groupBy=='physician'){
			$arrTempDOTOrder[$primaryProviderId][$facility_id][$encounter_id] = $encounter_id;
		}else{
			$arrTempDOTOrder[$facility_id][$primaryProviderId][$encounter_id] = $encounter_id; 
		}
		$arrTempPaidChgDetId[$charge_list_detail_id]= $charge_list_detail_id;
	}
}

if(sizeof($arrCreditDebitEncs)>0){
	$paid_encounter_arr = array_merge($paid_encounter_arr, $arrCreditDebitEncs);
}

$paid_encounter_str = join(',',$paid_encounter_arr);
$del_encounter_str = join(',',array_keys($del_payment_arr));
$mainPaymentArr = array();
$primaryProviderIdArr = array();
$mainPaymentEncounterArr = array();	
$notSubmitedArr = array();
$paidNotSubmitEncounterArr = array();
$mainNotPostedEncounterArr = array();
$mainNotPostedProviderArr = array();
$mainNotPostedArr = array();
$temp_del_paid_arr = array();
$qryRes = array();
//DATA BASED ON PAYMENTS
$paymtQry = "SELECT patient_data.lname,patient_data.mname,patient_data.fname,patient_data.id, 
patient_charge_list.facility_id, patient_charge_list.charge_list_id, patient_charge_list.encounter_id, 
patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId',patient_charge_list.postedDate,patient_charge_list.entered_date,
patient_charge_list.operator_id,
patient_charge_list.postedAmount,
date_format(patient_charge_list.date_of_service,'".$dateFormat."') as date_of_service,
patient_charge_list.Re_submitted, patient_charge_list.submitted, 
patChgDet.charge_list_detail_id, patChgDet.totalAmount, cpt_fee_tbl.departmentId ";

if($paid_encounter_str != "")
$paymtQry .=	", IF(patient_charge_list.encounter_id IN ($paid_encounter_str),'1','') AS paidStatus";
else 
$paymtQry .= ", 0 AS paidStatus";

if($del_encounter_str != "")
$paymtQry .=	", IF(patient_charge_list.encounter_id IN ($del_encounter_str),'1','') AS delStatus";
else 
$paymtQry .= ", 0 AS delStatus";

$paymtQry .=" 	FROM  patient_charge_list 
				JOIN patient_charge_list_details patChgDet ON patChgDet.charge_list_id = patient_charge_list.charge_list_id 
				JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = patChgDet.procCode
				JOIN patient_data ON patient_data.id = patient_charge_list.patient_id 
				WHERE (";
if($paid_encounter_str != "")									
$paymtQry .="	patient_charge_list.encounter_id IN ($paid_encounter_str)";

if($paid_encounter_str != "" && $del_encounter_str != "")
$paymtQry .=	" OR ";

if($del_encounter_str != "")							
$paymtQry .=	" patient_charge_list.encounter_id IN ($del_encounter_str)";

if(($paid_encounter_str!= "" || $del_encounter_str!="") && $strCreditDebitEncs!='')
$paymtQry .=	" OR ";

if($strCreditDebitEncs != "")							
$paymtQry .= 	" patient_charge_list.encounter_id IN ($strCreditDebitEncs)";
$paymtQry .=	" )";

if(empty($sc_name) == false){
$paymtQry .= 	" and patient_charge_list.facility_id IN ($sc_name)";
}
if(empty($grp_id) == false){
$paymtQry .= 	" and patient_charge_list.gro_id IN ($grp_id)";
}
if(empty($Physician) == false){
$paymtQry .= 	" and patient_charge_list.primary_provider_id_for_reports IN ($Physician)";
}
$paymtQry .= 	" $sortByPayment";

$qryRs = imw_query($paymtQry);
while($qry_res = imw_fetch_array($qryRs)){
	$qryRes[] = $qry_res;
}
for($i=0;$i<count($qryRes);$i++){
	$printFile = true;
	$encounter_id = $qryRes[$i]['encounter_id'];
	$primaryProviderId = $qryRes[$i]['primaryProviderId'];
	$facility_id = $qryRes[$i]['facility_id'];
	$chargeListId = $qryRes[$i]['charge_list_id'];
	$charge_list_detail_id = $qryRes[$i]['charge_list_detail_id'];
	//$oprId = $arrTempOperData[$charge_list_detail_id]['oper'];
	$deptId = $qryRes[$i]['departmentId'];

	//$firstGrpId=$oprId;
	$secGrpId=$primaryProviderId;
	if($groupBy=='facility'){
		$secGrpId=$facility_id;
	}else if($groupBy=='department'){
		$secGrpId=$deptId;
	}

	if($qryRes[$i]['paidStatus']==1 && $arrTempPaidChgDetId[$charge_list_detail_id]){
		$arrChargeListIds[$chargeListId] = $chargeListId;	// new
		//$primaryProviderIdArr[$primaryProviderId][$facility_id] = $facility_id;
		//$facilityIdArr[$facility_id][$primaryProviderId] = $primaryProviderId;
		//$mainPaymentEncounterArr[$encounter_id] = $encounter_id;
		$mainPaymentArr[$encounter_id]['charge_list'] = $qryRes[$i];

		//LOOP - MAY BE MULTIPLE OPERATOR FOR A CHARGE LIST DET ID
		foreach($arrTempOperData[$charge_list_detail_id]['oper'] as $oprId){
			$mainArr[$oprId][$secGrpId][$encounter_id] = $encounter_id;
		}

		if($qryRes[$i]['Re_submitted'] == 'false' && $qryRes[$i]['submitted'] == 'false'){
			//$notSubmitedArr[$firstGrpId][$secGrpId][] = $qryRes[$i];
			//$paidNotSubmitEncounterArr[$firstGrpId][$secGrpId][$encounter_id] = $encounter_id;
			//$mainNotPostedEncounterArr[$firstGrpId][$secGrpId][] = $charge_list_detail_id;
			//$mainNotPostedProviderArr[$firstGrpId][$secGrpId][$charge_list_detail_id] = $qryRes[$i];
			//$mainNotPostedArr[$firstGrpId] = $firstGrpId;
			//OPERATOR DATA
			//$arrOperatorData[$oprId][$firstGrpId][$secGrpId]['CHARGES_NOT_POSTED']+= $qryRes[$i]['totalAmount'];
		}
	}
	if($qryRes[$i]['delStatus'] == 1){
		$temp_del_paid_arr[$firstGrpId][$secGrpId][$encounter_id] = $encounter_id;
		$temp_del_paid_arr[$firstGrpId][$secGrpId][$encounter_id] = $qryRes[$i];
		$delPayEncArr[$encounter_id]=$encounter_id;
	}
}

//--- GET ALL RESULTS FROM PATIENT CHARGE LIST TABLE ---------
$payChrg = "select patient_charge_list.encounter_id,
		patient_charge_list.patient_id,
		patient_charge_list.submitted,
		patient_charge_list.postedDate,
		patient_charge_list.Re_submitted,
		patient_charge_list.Re_submitted_date,
		patient_charge_list.postedDate,
		patient_charge_list.entered_date,
		date_format(patient_charge_list.date_of_service , '".$dateFormat."') as 'date_of_service', 
		patient_data.id,
		patient_data.lname,
		patient_data.fname,
		patient_data.mname,
		patient_charge_list.operator_id,
		patient_charge_list.first_posted_opr_id,
		patient_charge_list.totalAmt,
		patient_charge_list.charge_list_id,
		patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId',
		patient_charge_list.charge_list_id,
		patient_charge_list.charge_list_id,
		patient_charge_list.postedAmount, 
		patient_charge_list.first_posted_date, 
		patient_charge_list.facility_id, 
		patChgDet.del_status, 
		DATE_FORMAT(patChgDet.trans_del_date, '%Y-%m-%d') as 'trans_del_date',
		patChgDet.totalAmount, 
		patChgDet.charge_list_detail_id, 
		cpt_fee_tbl.departmentId 
		FROM patient_charge_list 
		JOIN patient_charge_list_details patChgDet 
		ON patChgDet.charge_list_id = patient_charge_list.charge_list_id 
		
		JOIN cpt_fee_tbl 
		ON cpt_fee_tbl.cpt_fee_id = patChgDet.procCode 
		
		JOIN patient_data 
		ON patient_data.id = patient_charge_list.patient_id
		
		where (patient_charge_list.first_posted_date between '$startDate' and '$endDate')";
		
if(empty($sc_name) == false){
	$payChrg .= " and patient_charge_list.facility_id IN ($sc_name)";
}
if(empty($grp_id) == false){
	$payChrg .= " and patient_charge_list.gro_id IN ($grp_id)";
}
if(empty($Physician) == false){
	$payChrg .= " and patient_charge_list.primary_provider_id_for_reports IN ($Physician)";
}
if(empty($operatorId)==false){
	$payChrg .= " AND patient_charge_list.first_posted_opr_id in($operatorId)";
}
$payChrg .= " order by $sortBy";

$mainPostedArr = array();
$mainPostedProviderArr = array();
$submitedArr = array();
$mainRePostedProviderArr = array();
$reSubmitedPaidArr = array();
$submitedPaidArr = array();
$paidReSubmitEncounterArr = array();
$dept_charge_list_id_arr = array();
$mainPostedEncounterArr = array();
$mainRePostedArr = array();
$mainQryRes = array();
$mainPaymentEncounterStr = join(',',$mainPaymentEncounterArr);

$qryRs = imw_query($payChrg);
while($qry_res = imw_fetch_array($qryRs)){
	$mainQryRes[] = $qry_res;
}
for($i=0;$i<count($mainQryRes);$i++){		
	if(($mainQryRes[$i]['del_status']=='0') || ($mainQryRes[$i]['del_status']=='1' && $mainQryRes[$i]['trans_del_date']>$endDate)){
		$facility_id = $mainQryRes[$i]['facility_id'];
		$primaryProviderId = $mainQryRes[$i]['primaryProviderId'];
		$encounter_id = $mainQryRes[$i]['encounter_id'];
		$charge_list_id = $mainQryRes[$i]['charge_list_id'];
		$charge_list_detail_id = $mainQryRes[$i]['charge_list_detail_id'];
		$first_posted_opr_id = $mainQryRes[$i]['first_posted_opr_id'];
		$submitted = $mainQryRes[$i]['submitted'];
		$Re_submitted = $mainQryRes[$i]['Re_submitted'];
		$postedDate = $mainQryRes[$i]['postedDate'];
		$entered_date = $mainQryRes[$i]['entered_date'];
		$deptId = $mainQryRes[$i]['departmentId'];

		$firstGrpId=$first_posted_opr_id;
		$secGrpId=$primaryProviderId;
		if($groupBy=='facility'){
			$secGrpId=$facility_id;
		}else if($groupBy=='department'){
			$secGrpId=$deptId;
		}
		
		//--- ALL SUBMITTED ENCOUNTERS ---------
		if($submitted == 'true' && $mainQryRes[$i]['first_posted_date']>= $startDate && $mainQryRes[$i]['first_posted_date']<=$endDate){
			$printFile = true;

			$mainPaymentEncounterArr[$encounter_id] = $encounter_id;
			$submitedPaidArr[$encounter_id] = $mainPaymentArr[$encounter_id];

			$mainArr[$firstGrpId][$secGrpId][$encounter_id] = $encounter_id;

			$mainPostedEncounterArr[$firstGrpId][$secGrpId][$encounter_id][$charge_list_detail_id] = $mainQryRes[$i];

		}
	}
}

//--- GET ALL ENCOUNTER ----
$encounter_id_arr = array_keys($paidReSubmitEncounterArr);
$encounter_id_arr = array_merge($encounter_id_arr,array_keys($paidNotSubmitEncounterArr));
$encounter_id_arr = array_merge($encounter_id_arr,array_keys($mainPaymentEncounterArr));
$encounter_id_str = join(',',$encounter_id_arr);

$op = 'p';
$operatorIdN = $operatorId;

// GET PATIENT PRE PAYMENTS
if(strstr($viewBy,'inout')){
	$patQry="Select pDep.id, pDep.patient_id, pDep.paid_amount, DATE_FORMAT(pDep.paid_date, '".$dateFormat."') as 'paidDate', 
	pData.providerID, pData.default_facility, pDep.entered_by, pDep.apply_payment_type, pDep.apply_amount, 
	pDep.del_status, pDep.del_operator_id, DATE_FORMAT(pDep.trans_del_date, '".$dateFormat."') as 'delDate', 
	pDep.trans_del_date, pDep.payment_mode,
	pData.fname as 'pfname', pData.mname as 'pmname', pData.lname as 'plname' 
	FROM patient_pre_payment pDep 
	LEFT JOIN patient_data pData ON pData.id = pDep.patient_id 
	WHERE 1=1";
	if($dateBy=='paymentDate'){
		$patQry.=" AND (pDep.paid_date between '$startDate' and '$endDate')";
	}
	if($dateBy=='transactionDate'){
		$patQry.=" AND (pDep.entered_date between '$startDate' and '$endDate')";
	}
	if($hourFrom!='' && $hourTo!=''){
		$patQry.= " AND (pDep.entered_time BETWEEN '$hourFrom' AND '$hourTo')";					
	}
	if(empty($PhysicianSel)==false && empty($Physician) === false){ //$PhysicianSel check was ncecessary so that if not selected in filter then no need to check.
		$patQry.= " AND pDep.provider_id IN(".$Physician.")";
	}
	if(empty($sc_name) === false){
		if(empty($schFacStr) ===false){
			$patQry .= " AND pDep.facility_id in($schFacStr)";
		} else {
			$patQry.=" AND 1=2"; //SO THAT QUERY WILL NOT RETURN ANY RESULT.
		}
	}
	if(empty($operatorId) === false){
		$patQry .= " AND pDep.entered_by in($operatorId)";
	}
	$patQry .= " ORDER BY pDep.paid_date";
	$qryRs = imw_query($patQry);
	$patQryRes = $arrDepIds = array();
	while($qry_res = imw_fetch_array($qryRs)){
		$patQryRes[] = $qry_res;
	}
	for($i=0;$i<count($patQryRes);$i++){
		$printFile=true;
		$hasData=1;
		$facility='';	$docNameArr= array();	$patNameArr= array();
	
		$patId = $patQryRes[$i]['patient_id'];
		$oprId = $patQryRes[$i]['entered_by'];
		$id= $patQryRes[$i]['id'];
		list($transDelDate, $transDelTime) = explode(' ', $patQryRes[$i]['trans_del_date']); 
		
		//PRABH: GETTING REFUND AMOUNTS
		$refundAmt=0;
		if($patQryRes[$i]['id']){
			$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE (del_status='0' OR (del_status='1' AND del_date>'".$endDate."')) AND pmt_id = '".$patQryRes[$i]['id']."' AND (entered_date BETWEEN '".$startDate."' AND '".$endDate."')")or die(imw_error().'_656');
			while($rsRef=imw_fetch_array($qryRef)){
				$refundAmt+=$rsRef['ref_amt'];
			}
		}
		
		$patNameArr["LAST_NAME"] = $patQryRes[$i]['plname'];
		$patNameArr["FIRST_NAME"] = $patQryRes[$i]['pfname'];
		$patNameArr["MIDDLE_NAME"] = $patQryRes[$i]['pmname'];
		$patName = changeNameFormat($patNameArr);
	
		if($patQryRes[$i]['del_status']=='0' || ($patQryRes[$i]['del_status']=='1' && $transDelDate> $endDate && $transDelTime>$hourTo)){
			$tempData[$id]['OPR_ID']=$oprId;
			$tempData[$id]['PAT_ID']=$patId;
			$tempData[$id]['PAT_NAME']=$patName;
			$tempData[$id]['OPR_NAME']=$oprId;
			$tempData[$id]['PAID_DATE']=$patQryRes[$i]['paidDate'];
			$tempData[$id]['PAT_DEPOSIT']=$patQryRes[$i]['paid_amount'];
			$tempData[$id]['PAT_DEPOSIT_REF']=$refundAmt;
			$tempData[$id]['PAT_DEPOSIT_MODE']=$patQryRes[$i]['payment_mode'];
			
			if($patQryRes[$i]['apply_payment_type']=='manually'){
				$tempData[$id]['APPLIED_AMT']+= $patQryRes[$i]['apply_amount'];
				$tempPrePaidManuallyApplied[$id]+= $patQryRes[$i]['apply_amount'];
			}
			if($patQryRes[$i]['apply_payment_date']!='0000-00-00'){
				$arrDepIds[$id]=$id;	
			}
	
			$arrOperatorDepTots[$oprId]['MODE']= strtoupper($patQryRes[$i]['payment_mode']);
			
			if(strtolower($patQryRes[$i]['payment_mode'])=='cash'){
				$arrDepPatientData[$oprId][$id]['MODE']='Cash';
				$arrDepPayments['CASH']+=($patQryRes[$i]['paid_amount']-$refundAmt);
				$arrOperatorDepTots[$oprId]['CASH']+=($patQryRes[$i]['paid_amount']-$refundAmt);
				
				$arrDepPayments['CASH_REF']+=$refundAmt;
				$arrOperatorDepTots[$oprId]['CASH_REF']+=$refundAmt;
				
			}elseif(strtolower($patQryRes[$i]['payment_mode'])=='check'){
				$arrDepPatientData[$oprId][$id]['MODE']='Check';
				$arrDepPayments['CHECK']+=($patQryRes[$i]['paid_amount']-$refundAmt);
				$arrOperatorDepTots[$oprId]['CHECK']+=($patQryRes[$i]['paid_amount']-$refundAmt);
				
				$arrDepPayments['CHECK_REF']+=$refundAmt;
				$arrOperatorDepTots[$oprId]['CHECK_REF']+=$refundAmt;
				
			}elseif(strtolower($patQryRes[$i]['payment_mode'])=='credit card'){
				$arrDepPatientData[$oprId][$id]['MODE']='Credit Card';
				$arrDepPayments['CC']+=($patQryRes[$i]['paid_amount']-$refundAmt);
				$arrOperatorDepTots[$oprId]['CC']+=($patQryRes[$i]['paid_amount']-$refundAmt);
				
				$arrDepPayments['CC_REF']+=$refundAmt;
				$arrOperatorDepTots[$oprId]['CC_REF']+=$refundAmt;
				
			}elseif(strtolower($patQryRes[$i]['payment_mode'])=='money order'){
				$arrDepPatientData[$oprId][$id]['MODE']='Money Order';
				$arrDepPayments['MO']+=($patQryRes[$i]['paid_amount']-$refundAmt);
				$arrOperatorDepTots[$oprId]['MO']+=($patQryRes[$i]['paid_amount']-$refundAmt);
				
				$arrDepPayments['MO_REF']+=$refundAmt;
				$arrOperatorDepTots[$oprId]['MO_REF']+=$refundAmt;
				
			}
			
			$arrAllIds[$id]=$id;
		}elseif($patQryRes[$i]['del_status']=='1'){
			// DELETED PRE PAYMENTS
			$tempDelData[$patQryRes[$i]['delDate']]=$id;
			$delOprId=$patQryRes[$i]['del_operator_id'];
			$arrDepDelPatData[$id]['PATNAME'] = $patName.'-'.$patId;
			$arrDepDelPatData[$id]['PAT_DEPOSIT']= $patQryRes[$i]['paid_amount'];
			$arrDepDelPatData[$id]['DEL_DATE'] = $patQryRes[$i]['delDate'];
			$arrDepDelPatData[$id]['DEL_OPERATOR']= $delOprId;
		}
	}
	// GET PRE PAT ENCOUNTER APPLIED AMTS
	if(count($arrDepIds)>0){
		$strDepIds=implode(',', $arrDepIds);
		$preAppQry="Select patient_pre_payment_id, paidForProc FROM patient_charges_detail_payment_info  
		WHERE patient_charges_detail_payment_info.patient_pre_payment_id IN($strDepIds) 
		AND (deletePayment='0' OR (deletePayment='1' AND deleteDate>'".$endDate."')) 
		AND (unapply='0' OR (unapply='1' AND DATE_FORMAT(unapply_date, '%Y-%m-%d') >'".$endDate."'))";
		$preAppRs=imw_query($preAppQry);
		while($preAppRes=imw_fetch_array($preAppRs)){
			$id = $preAppRes['patient_pre_payment_id'];
			$tempData[$id]['APPLIED_AMT']+= $preAppRes['paidForProc'];
		}
	}
	// PRE PAYMENTS FINAL ARRAY
	$totNotAppliedPreAmt=0;
	foreach($arrAllIds as $id){
		
		$balance_amount=$tempData[$id]['PAT_DEPOSIT'];
		
		if($tempData[$id]['PAT_DEPOSIT_REF']>0)
		{
			$balance_amount=$tempData[$id]['PAT_DEPOSIT']-$tempData[$id]['PAT_DEPOSIT_REF'];
		}
		
		if($tempData[$id]['APPLIED_AMT']>0){
			$balance_amount=$tempData[$id]['PAT_DEPOSIT']-$tempData[$id]['APPLIED_AMT'];
		}
		
		$oprId=$tempData[$id]['OPR_ID'];
		
		if($processReport!='Summary'){
			$arrDepPatientData[$oprId][$id]['PATNAME'] = $tempData[$id]['PAT_NAME'].'-'.$tempData[$id]['PAT_ID'];
			$arrDepPatientData[$oprId][$id]['PAID_DATE'] = $tempData[$id]['PAID_DATE'];
			$arrDepPatientData[$oprId][$id]['PAT_DEPOSIT']= $tempData[$id]['PAT_DEPOSIT'];
			$arrDepPatientData[$oprId][$id]['PAT_DEPOSIT_REF']= $tempData[$id]['PAT_DEPOSIT_REF'];
			$arrDepPatientData[$oprId][$id]['PAT_DEPOSIT_MODE']= $tempData[$id]['PAT_DEPOSIT_MODE'];
			$arrDepPatientData[$oprId][$id]['NOT_APPLIED_AMT']= $balance_amount;
		}
		$arrOperatorDepTots[$oprId]['PAT_DEPOSIT']+=($tempData[$id]['PAT_DEPOSIT']-$tempData[$id]['PAT_DEPOSIT_REF']);
		$arrOperatorDepTots[$oprId]['PAT_DEPOSIT_REF']= $tempData[$id]['PAT_DEPOSIT_REF'];
		$arrOperatorDepTots[$oprId]['PAT_DEPOSIT_MODE']= $tempData[$id]['PAT_DEPOSIT_MODE'];
		$arrOperatorDepTots[$oprId]['NOT_APPLIED_AMT']+=$balance_amount;
		
		//MANUALLY APPLIED AMOUNT ARRAY
		if($tempPrePaidManuallyApplied[$id]>0){
			if($processReport=='Summary'){
				$arrManuallyApplied[$oprId]['pre_payment']+= $tempPrePaidManuallyApplied[$id];
			}else{
				
				$arrPrePayManuallyApplied[$oprId][$id]['pat_id']= $patId;
				$arrPrePayManuallyApplied[$oprId][$id]['pat_name']= $patName;
				$arrPrePayManuallyApplied[$oprId][$id]['entered_date']= $tempData[$id]['PAID_DATE'];
				$arrPrePayManuallyApplied[$oprId][$id]['applied_amt'] = $tempPrePaidManuallyApplied[$id];
			}
		}		

		$arrOperators[$oprId]=$oprId;
		$totNotAppliedPreAmt+=$balance_amount;
	}
	unset($tempData);
}
//-- END PRE-PAYMENTS---


if($processReport == 'Detail'){
	$op = 'l';
	if(strstr($viewBy,'trans')) {
		require_once(dirname(__FILE__).'/front_desk_detail_report.php');
	}
}

if($processReport=='Summary'){
	include 'front_desk_summary_report_physician.php';
}

if(strstr($viewBy,'inout')){
	//--- GET ALL PAYMENTS HISTORY OF A PATIENT ---
	$pay_query ="select check_in_out_payment.*,patient_data.lname, schedule_appointments.sa_doctor_id, 
	patient_data.fname,patient_data.mname, DATE_FORMAT(created_on, '".$dateFormat."') as created_on ,check_in_out_payment_details.id as ciopDetId
	FROM check_in_out_payment 
	join schedule_appointments on check_in_out_payment.sch_id = schedule_appointments.id 
	join patient_data on patient_data.id = check_in_out_payment.patient_id 
	join check_in_out_payment_details on 
	check_in_out_payment.payment_id = check_in_out_payment_details.payment_id
	where schedule_appointments.sa_doctor_id IN($Physician) 
	and created_on between '$startDate' and '$endDate' 
	and check_in_out_payment.total_payment>0";
	if($hourFrom!='' && $hourTo!=''){		
		$pay_query.=" AND (check_in_out_payment.del_status=0 OR (check_in_out_payment.del_status=1 AND check_in_out_payment.delete_date>'".$endDate."' AND check_in_out_payment.delete_time>'".$hourTo."')) 
				AND (check_in_out_payment_details.status='0' OR (check_in_out_payment_details.status ='1' && check_in_out_payment_details.delete_date >'".$endDate."'  && check_in_out_payment_details.delete_time >'".$hourTo."'))";		
	}else{
		$pay_query.=" and patient_data.lname != '' AND (check_in_out_payment.del_status='0' OR (check_in_out_payment.del_status='1' && check_in_out_payment.delete_date>'".$endDate."'))
		and (check_in_out_payment_details.status='0' OR (check_in_out_payment_details.status ='1' && check_in_out_payment_details.delete_date >'".$endDate."'))";
	}
	if(empty($schFacStr)===false){
		$pay_query.=" AND schedule_appointments.sa_facility_id IN(".$schFacStr.")";
	}
	if(empty($operatorId)==false){
		$pay_query.=" AND check_in_out_payment.created_by in ($operatorId)";
	}
	$pay_query.=" group by check_in_out_payment.payment_id order by payment_id desc, patient_data.lname, patient_data.fname";
	$qryRs = imw_query($pay_query);
	while($qry_rs = imw_fetch_array($qryRs)){
		$payQryRes[] = $qry_rs;
	}
	if(count($payQryRes) > 0){
		$printFile = true;
	}
	//--- CHECK IN/OUT TRANSACTIONS ---
	$item_ids_arr = array();
	$item_names_arr = array();
	$fldQryRes = array();
	$sel_row_qry = imw_query("select id, item_name from check_in_out_fields");
	while($qry_res = imw_fetch_array($sel_row_qry)){
		$fldQryRes[] = $qry_res;
	}
	for($i=0;$i<count($fldQryRes);$i++){
		$fld_id = $fldQryRes[$i]['id'];
		$item_ids_arr[] = $fld_id;
		$item_names_arr[$fld_id] = $fldQryRes[$i]['item_name'];
	}
	
	$payment_id_arr = array();
	$total_payment_arr = array();
	$payment_method_arr = array();
	$payment_check_cc_arr = array();
	$payment_cc_date_arr = array();
	$created_on_arr = array();
	$created_by_arr = array();
	$pay_detail_arr = array();
	$refundAmt_arr = array();
	
	for($i=0;$i<count($payQryRes);$i++){
		//IF TIME SEARCHED THEN SET TIME STAMP FOR SEARCH
		if($hourFrom!='' && $hourTo!=''){
			$arr1=explode(' ', $payQryRes[$i]['created_time']);
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
	
			$operId = $payQryRes[$i]['created_by'];
			$payment_id = $payQryRes[$i]['payment_id'];
			
			$arrOperator[$operId][$payment_id] = $payment_id;
			$payment_id_arr[] = $payQryRes[$i]['payment_id'];
			$total_payment_arr[$payment_id] = $payQryRes[$i]['total_payment'];
			$payment_method_arr[$payment_id] = $payQryRes[$i]['payment_method'];
			
			$pay_detail_arr[$payment_id] = $payQryRes[$i];
			
			if($payQryRes[$i]['payment_method'] == 'Check'){
				$payment_check_cc_arr[$payment_id] = $payQryRes[$i]['check_no'];
			}
			else if($payQryRes[$i]['payment_method'] == 'Credit Card'){
				$cc_no = substr($payQryRes[$i]['cc_no'],-4);
				$payment_check_cc_arr[$payment_id] = $payQryRes[$i]['cc_type'] .'-'. $cc_no;
				
				if(trim($payQryRes[$i]['cc_expire_date']) != ''){
					$cc_expire_date = $payQryRes[$i]['cc_expire_date'];
					$payment_cc_date_arr[$payment_id] = $cc_expire_date;
				}
			}
			$created_on_arr[$payment_id] = $payQryRes[$i]['created_on'];
			$created_by_arr[$payment_id] = $payQryRes[$i]['created_by'];
		}
	}
	
	$item_ids_imp = join("','",$item_ids_arr);	
	$payment_id_imp = join("','",$payment_id_arr);	
	$payment_detail = array();
	$pay_query_res = array();
	
	//--- GET CHECK IN/OUT PAYMENT FOR DETAILS VIEW ---
	$qry_payment = imw_query("select *,sum(item_payment) as item_payments from check_in_out_payment_details 
					where payment_id in('$payment_id_imp') 
					AND (status='0' OR (status='1' AND delete_date>'".$endDate."')) GROUP BY payment_id, item_id");
	while($qry_res = imw_fetch_array($qry_payment)){
		$pay_query_res[] = $qry_res;
	}
	
	for($i=0;$i<count($pay_query_res);$i++){
		$item_id = $pay_query_res[$i]['item_id']; 
		$pay_id = $pay_query_res[$i]['payment_id'];
		$payment_detail[$pay_id][] = $pay_query_res[$i];

		##########################################################
		#query to get refund detail for current ci/co payments if any
		##########################################################
		$refundAmt=0;
		$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE (del_status='0' OR (del_status='1' AND del_date>'".$endDate."')) 
		AND ci_co_id = '".$pay_query_res[$i]['id']."' AND (entered_date BETWEEN '".$startDate."' AND '".$endDate."')")or die(imw_error().'_471');
		while($rsRef=imw_fetch_array($qryRef)){
			$refund=$rsRef['ref_amt'];
			$total_refund_arr[$pay_id][$item_id]+= $refund;
			$refundAmt_arr[$pay_id]+= $refund;
		}
	}
	
	//GETTING MANUALLY APPLIED CI/CO AMOUNTS FOR MANUALLY BLOCK
	if(sizeof($payment_id_arr)>0){
		$qry="Select patient_id, check_in_out_payment_id, manually_payment FROM check_in_out_payment_post 
		WHERE check_in_out_payment_id IN('".$payment_id_imp."') AND manually_payment>0";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$payment_id=$res['check_in_out_payment_id'];
			$oprId = $created_by_arr[$payment_id];
			
			if($processReport=='Summary'){
				$arrManuallyApplied[$oprId]['cico']+= $res['manually_payment'];
			}else{
				$arrCICOManuallyPaid[$oprId][$payment_id]['pat_name']= $res['patient_id'].'~'.$pay_detail_arr[$payment_id]['fname'].'~'.$pay_detail_arr[$payment_id]['mname'].'~'.$pay_detail_arr[$payment_id]['lname'];
				$arrCICOManuallyPaid[$oprId][$payment_id]['paid_date']=$created_on_arr[$payment_id];
				$arrCICOManuallyPaid[$oprId][$payment_id]['applied_amt']= $res['manually_payment'];
			}
		}
	}
}

//--- GET HEADER IF SUMMARY REPORT ----	
if($processReport != 'Detail'){
	$total_refund_arr=array();

	if(strstr($viewBy,'inout')){
	$chk_data_res = NULL;
	$total_pay_arr = array();
	$pdf_data_res = NULL;

	//PRE-PAYMENTS BLOCK
	if(sizeof($arrOperatorDepTots)>0){
		$prePayTotal=$preNotApplyTot=0;
		
		$pre_summary_data.=
		'<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8">
			<tr id="heading_orange">
				<td align="left" colspan="7">&nbsp;Patient Pre Payments</td>
			</tr>
		<tr>
		<td class="text_b_w" style="text-align:left; width:260px;">Operator</td>
		<td class="text_b_w" style="text-align:center; width:200px;">Cash</td>
		<td class="text_b_w" style="text-align:center; width:200px;">Check</td>
		<td class="text_b_w" style="text-align:center; width:200px;">Credit Card</td>
		<td class="text_b_w" style="text-align:center; width:200px;">Money Order</td>
		<td class="text_b_w" style="text-align:center; width:250px;">Tot. Payment</td>
		<td class="text_b_w" style="text-align:center; width:250px;">Unapplied Amount</td>
		</tr>';
		$pre_pdf_head.=
		'<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%">
		<tr id="heading_orange"><td style="text-align:left;" colspan="7">&nbsp;Patient Pre Payments</td></tr>
		<tr>
		<td class="text_b_w" style="width:150px">Operator</td>
		<td class="text_b_w" style="text-align:center; width:150px">Cash</td>
		<td class="text_b_w" style="text-align:center; width:150px">Check</td>
		<td class="text_b_w" style="text-align:center; width:150px">Credit Card</td>
		<td class="text_b_w" style="text-align:center; width:150px">Money Order</td>
		<td class="text_b_w" style="text-align:center; width:150px">Tot. Payment</td>
		<td class="text_b_w" style="text-align:center; width:150px">Unapplied Amount</td>
		</tr></table>';
		
		$pre_summary_html.='<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%">';
		
		foreach($arrOperators as $oprID){
			$arrOperatorsTots=array();
			
			if(count($arrOperatorDepTots[$oprID])>0){
			
				$prePayTotal+=$arrOperatorDepTots[$oprID]['PAT_DEPOSIT'];
				$preNotApplyTot+=$arrOperatorDepTots[$oprID]['NOT_APPLIED_AMT'];
				$preCash+=$arrOperatorDepTots[$oprID]['CASH'];
				$preCheck+=$arrOperatorDepTots[$oprID]['CHECK'];
				$preCC+=$arrOperatorDepTots[$oprID]['CC'];
				$preMO+=$arrOperatorDepTots[$oprID]['MO'];
				
				
				$pre_summary_data.='<tr><td class="white text_10" style="tsext-align:left">'.$userNameArr[$oprID].'</td>
				<td class="white text_10" style="text-align:right';
				if($arrOperatorDepTots[$oprID]['CASH_REF'])$pre_summary_data.=';color:#FF0000" title="$'.$arrOperatorDepTots[$oprID]['CASH_REF'].' Refund';
				$pre_summary_data.='">'.numberFormat($arrOperatorDepTots[$oprID]['CASH'],2,1).'&nbsp;</td>
				<td class="white text_10" style="text-align:right';
				if($arrOperatorDepTots[$oprID]['CHECK_REF'])$pre_summary_data.=';color:#FF0000" title="$'.$arrOperatorDepTots[$oprID]['CHECK_REF'].' Refund';
				$pre_summary_data.='"">'.numberFormat($arrOperatorDepTots[$oprID]['CHECK'],2,1).'&nbsp;</td>
				<td class="white text_10" style="text-align:right';
				if($arrOperatorDepTots[$oprID]['CC_REF'])$pre_summary_data.=';color:#FF0000" title="$'.$arrOperatorDepTots[$oprID]['CC_REF'].' Refund';
				$pre_summary_data.='"">'.numberFormat($arrOperatorDepTots[$oprID]['CC'],2,1).'&nbsp;</td>
				<td class="white text_10" style="text-align:right';
				if($arrOperatorDepTots[$oprID]['MO'])$pre_summary_data.=';color:#FF0000" title="$'.$arrOperatorDepTots[$oprID]['MO_REF'].' Refund';
				$pre_summary_data.='"">'.numberFormat($arrOperatorDepTots[$oprID]['MO'],2,1).'&nbsp;</td>
				<td class="white text_10" style="text-align:right">'.numberFormat($arrOperatorDepTots[$oprID]['PAT_DEPOSIT'],2,1).'&nbsp;</td>
				<td class="white text_10" style="text-align:right">'.numberFormat($arrOperatorDepTots[$oprID]['NOT_APPLIED_AMT'],2,1).'&nbsp;</td>
				</tr>';
				$pre_summary_html.='<tr><td class="white text_10" style="text-align:left;width:150px">'.$userNameArr[$oprID].'</td>
				<td class="white text_10" style="text-align:right;width:150px';
				if($arrOperatorDepTots[$oprID]['CASH_REF'])$pre_summary_html.=';color:#FF0000" title="$'.$arrOperatorDepTots[$oprID]['CASH_REF'].' Refund';
				$pre_summary_html.='">'.numberFormat($arrOperatorDepTots[$oprID]['CASH'],2,1).'&nbsp;</td>
				<td class="white text_10" style="text-align:right;width:150px';
				if($arrOperatorDepTots[$oprID]['CHECK_REF'])$pre_summary_html.=';color:#FF0000" title="$'.$arrOperatorDepTots[$oprID]['CHECK_REF'].' Refund';
				$pre_summary_html.='">'.numberFormat($arrOperatorDepTots[$oprID]['CHECK'],2,1).'&nbsp;</td>
				<td class="white text_10" style="text-align:right;width:150px';
				if($arrOperatorDepTots[$oprID]['CC_REF'])$pre_summary_html.=';color:#FF0000" title="$'.$arrOperatorDepTots[$oprID]['CC_REF'].' Refund';
				$pre_summary_html.='">'.numberFormat($arrOperatorDepTots[$oprID]['CC'],2,1).'&nbsp;</td>
				<td class="white text_10" style="text-align:right;width:150px';
				if($arrOperatorDepTots[$oprID]['MO_REF'])$pre_summary_html.';color:#FF0000" title="$'.$arrOperatorDepTots[$oprID]['MO_REF'].' Refund';
				$pre_summary_html.='">'.numberFormat($arrOperatorDepTots[$oprID]['MO'],2,1).'&nbsp;</td>
				<td class="white text_10" style="text-align:right;width:150px">'.numberFormat($arrOperatorDepTots[$oprID]['PAT_DEPOSIT'],2,1).'&nbsp;</td>
				<td class="white text_10" style="text-align:right;width:150px">'.numberFormat($arrOperatorDepTots[$oprID]['NOT_APPLIED_AMT'],2,1).'&nbsp;</td>
				</tr>';
			}
		
		}
		
		$redRowPP='';
				
		$pre_summary_data.='<tr><td class="white text_10b" style="text-align:right">Operators Total :</td>
		<td class="white text_10b" style="text-align:right">'.numberFormat($preCash,2,1).'&nbsp;</td>
		<td class="white text_10b" style="text-align:right">'.numberFormat($preCheck,2,1).'&nbsp;</td>
		<td class="white text_10b" style="text-align:right">'.numberFormat($preCC,2,1).'&nbsp;</td>
		<td class="white text_10b" style="text-align:right">'.numberFormat($preMO,2,1).'&nbsp;</td>
		<td class="white text_10b" style="text-align:right">'.numberFormat($prePayTotal,2,1).'&nbsp;</td>
		<td class="white text_10b" style="text-align:right">'.numberFormat($preNotApplyTot,2,1).'&nbsp;</td>
		</tr>';
		$pre_summary_html.='<tr><td class="white text_10b" style="text-align:right">Operators Total :</td>
		<td class="white text_10b" style="text-align:right">'.numberFormat($preCash,2,1).'&nbsp;</td>
		<td class="white text_10b" style="text-align:right">'.numberFormat($preCheck,2,1).'&nbsp;</td>
		<td class="white text_10b" style="text-align:right">'.numberFormat($preCC,2,1).'&nbsp;</td>
		<td class="white text_10b" style="text-align:right">'.numberFormat($preMO,2,1).'&nbsp;</td>
		<td class="white text_10b" style="text-align:right">'.numberFormat($prePayTotal,2,1).'&nbsp;</td>
		<td class="white text_10b" style="text-align:right">'.numberFormat($preNotApplyTot,2,1).'&nbsp;</td>
		</tr>';
		$pre_summary_data.='</table>';
		$pre_summary_html.='</table>';

		$pre_pdf='
		<page backtop="18mm" backbottom="10mm">
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		<page_header>'
		.$fileHeaderData.
		 $pre_pdf_head.
		'</page_header>'. 
		 $pre_summary_html
		.'</page>';
		
		//$pre_summary_data.=$pre_del_page_data;
		//$pre_summary_html.=$preDelStrHTML;
		$csvFileData.=$pre_summary_data;
		$pdfData.=$pre_pdf;
	}
	//-- END PRE PAYMENTS		
	
	//PRINTING CHECK IN CHECK OUT PAYMENTS
	foreach($arrOperator as $operId => $paymentIdArr){
		$arrSubTot=array();
		$chk_data_res.='<tr><td align="left" class="text_b_w" colspan="6">Operator : '.$userNameArr[$operId].'</td></tr>';
		$pdf_data_res.='<tr><td align="left" class="text_b_w" colspan="5">Operator : '.$userNameArr[$operId].'</td></tr>';

		foreach($paymentIdArr as $pay_id){	
			$payment_detail_arr = $pay_detail_arr[$pay_id];
			
			$total_pay_arr[] = $total_payment_arr[$pay_id];
			$total_refund_arr[] = $refundAmt_arr[$pay_id];
			$total_pay = $total_payment_arr[$pay_id];
			$refundAmt=$refundAmt_arr[$pay_id];
			$payment_method = $payment_method_arr[$pay_id];
			//echo $payment_method.'<br/>';
			$payment_check_cc = $payment_check_cc_arr[$pay_id];
			$payment_cc_date = $payment_cc_date_arr[$pay_id];
			$created_on = $created_on_arr[$pay_id];
			$created_by_name = $userNameArr[$created_by_arr[$pay_id]];
			//--- PATIENT NAME --
			$pat_name_arr = array();
			$pat_name_arr['LAST_NAME'] = $payment_detail_arr['lname'];
			$pat_name_arr['FIRST_NAME'] = $payment_detail_arr['fname'];
			$pat_name_arr['MIDDLE_NAME'] = $payment_detail_arr['mname'];
			
			
			$pat_name = changeNameFormat($pat_name_arr);
			$pat_name .= ' - '.$payment_detail_arr['patient_id'];

			$arrSubTot['tot_pay']+=($total_pay-$refundAmt);
			if($refundAmt)
			{
				$redRow=';color:#FF0000';
				$title=' title="$'.$refundAmt.' Refund"';
			}
			else
			{
				$redRow='';
				$title='';
			}
			$total_pay = numberFormat($total_pay-$refundAmt,2);
			
			
			$chk_data_res .= <<<DATA
			<tr>
				<td align="left" bgcolor="#FFFFFF" class="text_10" width="210">$pat_name</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right $redRow" width="210" $title>$total_pay</td>
				<td align="left" bgcolor="#FFFFFF" class="text_10" width="210">&nbsp;&nbsp;$payment_method</td>
				<td align="left" bgcolor="#FFFFFF" class="text_10" width="210">$payment_check_cc&nbsp;</td>
				<td align="left" bgcolor="#FFFFFF" class="text_10" width="210">$created_on</td>
				<td align="left" bgcolor="#FFFFFF" class="text_10" width="auto"></td>
			</tr>
DATA;
		$pdf_data_res .= <<<DATA
			<tr>
				<td align="left" bgcolor="#FFFFFF" class="text_10" width="210">$pat_name</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right $redRow" width="210" $title>$total_pay&nbsp; </td>
				<td align="left" bgcolor="#FFFFFF" class="text_10" width="210">&nbsp;&nbsp;$payment_method </td>
				<td align="left" bgcolor="#FFFFFF" class="text_10" width="210">$payment_check_cc&nbsp;</td>
				<td align="left" bgcolor="#FFFFFF" class="text_10" width="210">$created_on</td>
			</tr>
DATA;
		}
		
		$chk_data_res.='
		<tr>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right;">Operator Total : </td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right;">'.numberFormat($arrSubTot['tot_pay'],2).'</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right;" colspan="5"></td>
		</tr>';

		$pdf_data_res.='
		<tr>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right;">Operator Total : </td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right;">'.numberFormat($arrSubTot['tot_pay'],2).'</td>
			<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right;" colspan="3"></td>
		</tr>';
	}
		
	if(trim($chk_data_res) != ''){
		
	$Total_Payment = numberFormat(((array_sum($total_pay_arr))-(array_sum($refundAmt_arr))),2);		
	//--- PDF FILE DATA -----
	$pdfData .= <<<DATA
		<page backtop="17mm" backbottom="5mm">
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>	
		<page_header>	
			$fileHeaderData
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%">
				<tr height="20" id="heading_orange">
					<td align="left" colspan="5">Check in/out payments</td>
				</tr>
				<tr height="20">
					<td align="center" class="text_b_w" width="210">Patient Name</td>
					<td align="center" class="text_b_w" width="210">Payments</td>
					<td align="center" class="text_b_w" width="210">Method</td>
					<td align="center" class="text_b_w" width="210">CC / Ch.#</td>
					<td align="center" class="text_b_w" width="210">Collected On</td>
				</tr>
			</table>
		</page_header>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%">
			$pdf_data_res	
			<tr height="20" bgcolor="#FFFFFF">
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">Total CI/CO Payment :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">$Total_Payment</td>
				<td class="text_10b" bgcolor="#FFFFFF" colspan="3"></td>
			</tr>				
		</table>
		</page>
DATA;

	//--- CSV FILE DATA ---	
	$csvFileData .= <<<DATA
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8">
			<tr height="20" id="heading_orange">
				<td align="left" colspan="6">Check in/out payments</td>
			</tr>
			<tr height="20">
				<td align="center" class="text_b_w">Patient Name</td>
				<td align="center" class="text_b_w">Payments</td>
				<td align="center" class="text_b_w">Method</td>
				<td align="center" class="text_b_w">CC / Ch.#</td>
				<td align="center" class="text_b_w">Collected On</td>
				<td align="center" class="text_b_w"></td>
			</tr>
			$chk_data_res	
			<tr height="20" bgcolor="#FFFFFF">
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">Total CI/CO Payment :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">$Total_Payment</td>
				<td class="text_10b" bgcolor="#FFFFFF" colspan="5"></td>
			</tr>				
		</table>
DATA;
	}
	}
	
	//MANUALLY APPLIED RECORDS
	if(sizeof($arrManuallyApplied)>0){
		$dataExists=true;
		$delDataExists=true;
		$content_part='';
		$arrTotals=array();
		
		foreach($arrManuallyApplied as $grpId => $grpData){
			$firstGroupName = $userNameArr[$grpId];
			$total= $grpData['cico']+$grpData['pre_payment']; 
			$arrTotals['cico']+= $grpData['cico'];
			$arrTotals['pre_payment']+= $grpData['pre_payment'];
			$arrTotals['total']+= $total;
			
			$content_part .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; word-wrap:break-word;">'.$firstGroupName.'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.numberFormat($grpData['cico'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.numberFormat($grpData['pre_payment'],2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.numberFormat($total,2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
			</tr>';			
		}
		
		// TOTAL
		$facCol='';
		$colspan=5;
		$colWidth='9%';
		$firstColSpan=1;
		if($showFacCol){
			$colspan+=1;
			$firstColSpan=2;
		}
		$total_cols=$colspan-2;
		$first_col = "20";
		$last_col="40";
		$w_cols = floor((100 - ($first_col+$last_col)) / $total_cols);
		$first_col = 100 - (($total_cols * $w_cols) + $last_col);
			
		$grand_first_col = 	$first_col;
		$grand_w_cols = $w_cols;
		
		$first_col = $first_col."%";
		$w_cols = $w_cols."%";
		$last_col = $last_col."%";
		
		if($showFacCol){
			$facCol = '<td class="text_b_w" style="width:'.$w_cols.'; text-align:left;">Facility</td>';
		}
	
		$delCICOTotal=$arrTotals['cico'];
		$delPrePayTotal=$arrTotals['pre_payment'];
	
		$manually_applied_html.=' 
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" cellspacing="1" style="width:100%">
		<tr id="heading_orange"><td colspan="'.$colspan.'">Manually Applied Amounts</td></tr>
		<tr id="heading1">
			<td style="width:'.$first_col.'; text-align:left;">Operator &nbsp;</td>
			<td style="width:'.$w_cols.'; text-align:right;">CI/CO Payments&nbsp;</td>
			<td style="width:'.$w_cols.'; text-align:right;">Pre-Payments&nbsp;</td>
			<td style="width:'.$w_cols.'; text-align:right;">Total&nbsp;</td>
			<td style="width:'.$last_col.'; text-align:right;">&nbsp;</td>
		</tr>'
		.$content_part.'
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$firstColSpan.'">Manually Applied Total&nbsp;:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberFormat($arrTotals['cico'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberFormat($arrTotals['pre_payment'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberFormat($arrTotals['total'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;"></td>
		</tr>
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
		</table>';		
	}
		
}else{


	// MAKE DATA FOR CHECK IN CHECK OUT PAYMENT COLUMNS
	$payDataTotals = array();
	$totCash = $totCC =  $totCheck = NULL;
	$payDataCols = array();

	for($p=0,$seq=1;$p<count($payment_id_arr);$p++){
		$pay_id = $payment_id_arr[$p];
		
		$pay_detail = $payment_detail[$pay_id];	
		for($d=0;$d<count($pay_detail);$d++){
			##########################################################
			#query to get refund detail for current ci/co payments if any
			##########################################################
			$refundAmt=0;
			if($pay_detail[$d]['id']){
				$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND ci_co_id = '".$pay_detail[$d]['id']."' AND (entered_date BETWEEN '".$startDate."' AND '".$endDate."')")or die(imw_error().'_471');
				$rsRef=imw_fetch_array($qryRef);
				$refund=$rsRef['ref_amt'];
			}	
			$item_id = $pay_detail[$d]['item_id'];
			if($item_id > 0){
				$item_payment=0;
				$item_names_str = $item_names_arr[$item_id];
				$item_payment = $pay_detail[$d]['item_payments']-$refund;
				if($item_payment > 0 || $refund>0){	
					$payDataCols[$pay_id][$item_id] = $pay_detail[$d]['item_payments'];
					$colName = NULL;
					$bool= array_key_exists($item_id, $item_names_arr);
					
					if($bool==TRUE){ 
						$exp = explode(" ",$item_names_str);
						if(sizeof($exp)>1){
							foreach($exp as $val)
							{
								$colName.=substr($val,0,1);
							}
						}else{
							$colName=$exp[0];
						}
						
						$payDataTotals[$item_id][]= $item_payment;
						$columnsArr[$item_id]=$colName;

					}else {
						$payDataTotals[$item_id][]= $item_payment;
					}
				}	
			}
		}
	}		

	$payCols = sizeof($columnsArr);
	$cols = $payCols + 6;
	$pdfPageWidth = 1025;
	$deductPixels = $payCols * 3;
	$dynamicColW = round(($pdfPageWidth - 500) / $payCols) - $deductPixels;

	foreach($columnsArr as $key =>$value2){
		$value = numberFormat(array_sum($payDataTotals[$key]),2);
		
		$chk_title_cols.='<td align="center" class="text_b_w" width="'.$dynamicColW.'">'.$value2.'</td>';
		$pdf_title_cols.='<td class="text_b_w" align="center" width="'.$dynamicColW.'">'.$value2.'</td>';

		$chk_pay_totals.='<td class="text_10b" style="text-align:right">'.$value.'&nbsp;</td>';
		$pdf_pay_totals.='<td class="text_10b" style="text-align:right">'.$value.'&nbsp;</td>';
		
	}
	//--------------------------------------
	
	$chk_data_res = $pdf_data_res = NULL;
	$total_pay_arr = array();
	$totCash = $totCheck = $totCC = $totEFT = $totMoneyOrder = array();	

	foreach($arrOperator as $operId => $paymentIdArr){
		$arrSubTot = array();
		$arrSubTot_1 =array();
		$chk_data_res.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$cols.'">Operator : '.$userNameArr[$operId].'</td></tr>';
		$pdf_data_res.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$cols.'">Operator : '.$userNameArr[$operId].'</td></tr>';
				
		foreach($paymentIdArr as $pay_id){
			$total_pay_arr[] = $total_payment_arr[$pay_id];
			//$total_refund_arr[]=$total_refund_arr[$pay_id];
			
			$payment_method = $payment_method_arr[$pay_id];
			$payment_check_cc = $payment_check_cc_arr[$pay_id];
			$payment_cc_date = $payment_cc_date_arr[$pay_id];
			$created_on = $created_on_arr[$pay_id];
			$created_by_name = $userNameArr[$created_by_arr[$pay_id]];
			$payment_detail_arr = $pay_detail_arr[$pay_id];
			$payment = $total_payment_arr[$pay_id];
			$refund = array_sum($total_refund_arr[$pay_id]);
			$total_pay = $total_payment_arr[$pay_id];
			$total_pay = $total_pay-$refund;
			
			if($refund>0)
			{
				$redRow=';color:#FF0000';
				$title=' title="$'.$refund.' Refund"';
			}
			else
			{
				$redRow='';
				$title='';
			}
			//--- PATIENT NAME --
			$pat_name_arr = array();
			$pat_name_arr['LAST_NAME'] = $payment_detail_arr['lname'];
			$pat_name_arr['FIRST_NAME'] = $payment_detail_arr['fname'];
			$pat_name_arr['MIDDLE_NAME'] = $payment_detail_arr['mname'];
			$pat_name = changeNameFormat($pat_name_arr);
			$pat_name .= ' - '.$payment_detail_arr['patient_id'];
	
			$pdf_data_val = $chk_data_val = NULL;
			$pay_detail = $payment_detail[$pay_id];
		
			if($payment_method == 'Cash'){			$totCash[] = $total_pay;}
			if($payment_method == 'Credit Card'){	$totCC[] = $total_pay;}
			if($payment_method == 'Check'){			$totCheck[] = $total_pay;}
			if($payment_method == 'EFT'){			$totEFT[] = $total_pay;}
			if($payment_method == 'Money Order'){	$totMoneyOrder[] = $total_pay;}
	
			$arrSubTot['totPay']+= $total_pay;
			
			// for data columns
			$p=0;
			foreach($columnsArr as $key => $value){
				$refund=0;
				if($payDataCols[$pay_id][$key]){
					$refund = $total_refund_arr[$pay_id][$key];
					$item_payment = numberFormat(($payDataCols[$pay_id][$key]-$refund),2,1);
					$arrSubTot_1[$p]+= $payDataCols[$pay_id][$key]-$refund;
					
					if($refund){
						$redRow=';color:#FF0000';
						$title=' title="$'.$refund.' Refund"';
					}else{
						$redRow='';
						$title='';
					}
				}else{
					$item_payment = 0;
					$redRow='';
					$title='';
				}
				$chk_data_val .= <<<DATA
					<td class="text_10" style="text-align:right $redRow" width="$dynamicColW" $title>$item_payment&nbsp; </td>
DATA;
				$pdf_data_val.= <<<DATA
					<td class="text_10" style="text-align:right $redRow" width="$dynamicColW" $title>$item_payment&nbsp;</td>
DATA;
				$p++;
			}
			//----------------	
		
			$total_pay = numberFormat($total_pay,2);
			$chk_data_res .= <<<DATA
				<tr bgcolor="#FFFFFF">
					<td align="left" class="text_10" width="160">$pat_name</td>
					<td class="text_10" style="text-align:right" width="100">$total_pay &nbsp;</td>
DATA;
			$chk_data_res .= <<<DATA
					$chk_data_val
					<td align="left" class="text_10" width="100">&nbsp;&nbsp;$payment_method</td>					
					<td align="left" class="text_10" width="120">$payment_check_cc &nbsp; </td>
					<td align="left" class="text_10" width="100">$payment_cc_date &nbsp;</td>
					<td align="left" class="text_10" width="100">$created_on</td>
				</tr>
DATA;
			$pdf_data_res .= <<<DATA
				<tr bgcolor="#FFFFFF">
					<td align="left" class="text_10" width="150">$pat_name</td>
					<td class="text_10" style="text-align:right $redRow" width="70">$total_pay&nbsp;</td>
					$pdf_data_val
					<td align="left" class="text_10" width="70">&nbsp;&nbsp;$payment_method</td>
					<td align="left" class="text_10" width="70">$payment_check_cc&nbsp;</td>
					<td align="left" class="text_10" width="70">$payment_cc_date&nbsp;</td>
					<td align="left" class="text_10" width="70">$created_on</td>
				</tr>
DATA;
		}
		// OPERATOR TOTAL
		$chk_data_val= $pdf_data_val ='';
		for($i=0; $i< sizeof($arrSubTot_1); $i++){
			$chk_data_val.='<td class="text_10b" style="text-align:right;">'.numberFormat($arrSubTot_1[$i],2).'&nbsp;</td>';
			$pdf_data_val.='<td class="text_10b" style="text-align:right;">'.numberFormat($arrSubTot_1[$i],2).'&nbsp;</td>';
		} 
		$chk_data_res.='
		<tr><td class="total-row" colspan="'.$cols.'"></td></tr>
		<tr bgcolor="#FFFFFF">
			<td class="text_10b" style="text-align:right">Operator Total : </td>
			<td class="text_10b" style="text-align:right;">'.numberFormat($arrSubTot['totPay'],2).'&nbsp;</td>
			'.$chk_data_val.'
			<td align="left" class="text_10" colspan="4"></td>					
		</tr>
		<tr><td class="total-row" colspan="'.$cols.'"></td></tr>';
		$pdf_data_res.='
		<tr><td class="total-row" colspan="'.$cols.'"></td></tr>
		<tr bgcolor="#FFFFFF">
			<td class="text_10b" style="text-align:right">Operator Total : </td>
			<td class="text_10b" style="text-align:right;">'.numberFormat($arrSubTot['totPay'],2).'&nbsp;</td>
			'.$pdf_data_val.'
			<td align="left" class="text_10" colspan="4"></td>					
		</tr>
		<tr><td class="total-row" colspan="'.$cols.'"></td></tr>';	
	}
	
	if(trim($chk_data_res) != ''){
		$colsPart = $cols - 1;
		$Total_Payment = numberFormat(((array_sum($total_pay_arr))-(array_sum($refundAmt_arr))),2);
		//--- PDF FILE DATA -----
		if(strstr($viewBy,'inout')){		
			$totCash = numberFormat(array_sum($totCash),2);		
			$totCC = numberFormat(array_sum($totCC),2);		
			$totCheck = numberFormat(array_sum($totCheck),2);
			$totEFT = numberFormat(array_sum($totEFT),2);
			$totMoneyOrder = numberFormat(array_sum($totMoneyOrder),2);
			
			$pdfData.= <<<DATA
				<page backtop="17mm" backbottom="5mm">
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>	
				<page_header>	
					$fileHeaderData
					<table width="100%" border="0" class="rpt rpt_table rpt_table-bordered rpt_padding">
						<tr height="20" id="heading_orange">
							<td align="left" colspan="$cols">Check in/out payments</td>
						</tr>
						<tr height="20">
							<td align="center" class="text_b_w" width="150">Patient Name</td>
							<td align="center" class="text_b_w" width="70">Payments</td>
							$pdf_title_cols
							<td align="center" class="text_b_w" width="70">Method</td>
							<td align="center" class="text_b_w" width="70">CC / Ch.#</td>
							<td align="center" class="text_b_w" width="70">CC Exp. Date</td>
							<td align="center" class="text_b_w" width="70">Collected On</td>
						</tr>
					</table>
				</page_header>
				<table width="100%" border="0" class="rpt rpt_table rpt_table-bordered rpt_padding">
					$pdf_data_res	
					<tr><td class="total-row" colspan="$cols"></td></tr>
					<tr height="20" bgcolor="#FFFFFF">
						<td class="text_10b" style="text-align:right" bgcolor="#FFFFFF">Total Payment : </td>
						<td class="text_10b" style="text-align:right" bgcolor="#FFFFFF">$Total_Payment</td>
						$pdf_pay_totals
						<td class="text_10b" colspan="4" bgcolor="#FFFFFF"></td>
					</tr>
					<tr><td class="total-row" colspan="$cols"></td></tr>
					<tr bgcolor="#FFFFFF">	
						<td class="text_10b"  style="text-align:left" bgcolor="#FFFFFF"></td>
						<td class="text_10b"  style="text-align:left" bgcolor="#FFFFFF" colspan="$colsPart">
						Cash : $totCash &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						Check : $totCheck &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						CC : $totCC &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						EFT : $totEFT &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						MO : $totMoneyOrder
						</td>
				   </tr>
				   <tr><td class="total-row" colspan="$cols"></td></tr>	
				</table>
				</page>
DATA;
			
			//--- CSV FILE DATA ---	
			$csvFileData .= <<<DATA
				<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8">
					<tr height="20" id="heading_orange">
						<td align="left" colspan="$cols">Check in/out payments</td>
					</tr>
					<tr height="20">
						<td align="center" class="text_b_w">Patient Name</td>
						<td align="center" class="text_b_w">Payments</td>
						$chk_title_cols
						<td align="center" class="text_b_w">Method</td>
						<td align="center" class="text_b_w">CC / Ch.#</td>
						<td align="center" class="text_b_w" width="100">CC Exp. Date</td>
						<td align="center" class="text_b_w">Collected On</td>
					</tr>
					$chk_data_res
					<tr><td class="total-row" colspan=$cols></td></tr>
					<tr height="20" bgcolor="#FFFFFF">
						<td class="text_10b" style="text-align:right" bgcolor="#FFFFFF">Total CI/CO Payment :</td>
						<td class="text_10b" style="text-align:right" bgcolor="#FFFFFF">$Total_Payment</td>
						$chk_pay_totals
						<td class="text_10b" colspan="5" bgcolor="#FFFFFF">&nbsp;</td>
					</tr>
					<tr><td class="total-row" colspan=$cols></td></tr>
					<tr bgcolor="#FFFFFF">	
						<td class="text_10b"  style="text-align:left" bgcolor="#FFFFFF"></td>
						<td class="text_10b"  style="text-align:left" bgcolor="#FFFFFF" colspan="$colsPart">
						Cash : $totCash &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						Check : $totCheck &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						CC : $totCC &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						EFT : $totEFT &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						MO : $totMoneyOrder
						</td>
				   </tr>
				   <tr><td class="total-row" colspan=$cols></td></tr>	
				   </table>
DATA;
		
		}
	}

	//CI/CO MANUALLY APPLIED
	$content_part= $applied_html='';
	$totalCICOManuallyApplied=0;
	$arrAppliedPatPayTot=array();
	
	if(sizeof($arrCICOManuallyPaid)>0){
		$colspan= 4;
	
		$total_cols = 2;
		$first_col = "22";
		$last_col = "53";
		$w_cols = floor((100 - ($first_col+$last_col)) /$total_cols);
		
		$first_col = 100 - (($total_cols * $w_cols) + $last_col);
	
		$grand_first_col=$first_col;
		$grand_w_cols=$w_cols;	
		
		$first_col = $first_col.'%';
		$w_cols = $w_cols."%";
		$last_col = $last_col."%";
		
		foreach($arrCICOManuallyPaid as $grpId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			$firstGroupName = $userNameArr[$grpId];
	
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;Operator - '.$firstGroupName.'</td></tr>';
		
			foreach($grpData as $eid => $grpDetail){
				$pName = explode('~', $grpDetail['pat_name']);
				$patient_name_arr = array();
				$patient_name_arr["LAST_NAME"] = $pName[3];
				$patient_name_arr["FIRST_NAME"] = $pName[1];
				$patient_name_arr["MIDDLE_NAME"] = $pName[2];		
				$patient_name = changeNameFormat($patient_name_arr);
				$patient_name.= ' - '.$pName[0];
				
				$firstGrpTotal['applied_amt']+=	$grpDetail['applied_amt'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['paid_date'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.numberFormat($grpDetail['applied_amt'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
				</tr>';			
			}
		
			$arrAppliedPatPayTot['applied_amt']+=	$firstGrpTotal['applied_amt'];
		
			$content_part.=' 
			<tr><td class="total-row"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="2">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberFormat($firstGrpTotal['applied_amt'],2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">&nbsp;</td>
			</tr>
			<tr><td class="total-row"  colspan="'.$colspan.'"></td></tr>';
		}
	
		$totalCICOManuallyApplied=$arrAppliedPatPayTot['applied_amt'];
		
		// TOTAL
		// HEADER
		$header='
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" cellspacing="1" style="width:100%">		
		<tr id="heading_orange"><td colspan="'.$colspan.'">CI/CO Manually Applied Amounts</td></tr>
		<tr id="heading1">
			<td style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
			<td style="width:'.$w_cols.'; text-align:center;">DOT</td>
			<td style="width:'.$w_cols.'; text-align:right;">Applied Amount</td>
			<td style="width:'.$last_col.';">&nbsp;</td>
		</tr>
		</table>';
	
		
		//HTML
		$manually_applied_cico_html .=
		$header.' 
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" cellspacing="1" style="width:100%">
		'.$content_part.'
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="2">CI/CO Manually Applied Amounts&nbsp;:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberFormat($arrAppliedPatPayTot['applied_amt'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>	
		</table>';
	}

	//PRE-PAYMENT MANUALLY APPLIED
	$content_part= $applied_html='';
	$arrAppliedPatPayTot=array();
	if(sizeof($arrPrePayManuallyApplied)>0){
		$colspan= 4;
	
		$total_cols = 2;
		$first_col = "22";
		$last_col = "53";
		$w_cols = floor((100 - ($first_col+$last_col)) /$total_cols);
		
		$first_col = 100 - (($total_cols * $w_cols) + $last_col);
	
		$grand_first_col=$first_col;
		$grand_w_cols=$w_cols;	
		
		$first_col = $first_col.'%';
		$w_cols = $w_cols."%";
		$last_col = $last_col."%";
		
		foreach($arrPrePayManuallyApplied as $grpId => $grpData){
			$rowTot=0;
			$firstGroupName='';
			$firstGrpTotal=array();
			$firstGroupName = $userNameArr[$grpId];
	
			$content_part.='<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colspan.'">&nbsp;Operator - '.$firstGroupName.'</td></tr>';
		
			foreach($grpData as $eid => $grpDetail){

				$patient_name= $grpDetail['pat_name'].' - '.$grpDetail['pat_id'];
				$firstGrpTotal['applied_amt']+=	$grpDetail['applied_amt'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:'.$first_col.'">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$w_cols.'">'.$grpDetail['entered_date'].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:'.$w_cols.'">'.numberFormat($grpDetail['applied_amt'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:'.$last_col.'"></td>
				</tr>';			
			}
		
			$arrAppliedPatPayTot['applied_amt']+=	$firstGrpTotal['applied_amt'];
		
			$content_part.=' 
			<tr><td class="total-row"  colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="2">'.$firstGroupTitle.' Total :</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberFormat($firstGrpTotal['applied_amt'],2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">&nbsp;</td>
			</tr>
			<tr><td class="total-row"  colspan="'.$colspan.'"></td></tr>';
		}
	
		
		// TOTAL
		// HEADER
		$header='
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" cellspacing="1" style="width:100%">		
		<tr id="heading_orange"><td colspan="'.$colspan.'">Pre-Payments Manually Applied Amounts</td></tr>
		<tr id="heading1">
			<td style="width:'.$first_col.'; text-align:left;">&nbsp;Patient Name-Id</td>
			<td style="width:'.$w_cols.'; text-align:center;">DOT</td>
			<td style="width:'.$w_cols.'; text-align:right;">Applied Amount</td>
			<td style="width:'.$last_col.';">&nbsp;</td>
		</tr>
		</table>';
	
	
		//HTML
		$manually_applied_pre_pay_html .=
		$header.' 
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" cellspacing="1" style="width:100%">
		'.$content_part.'
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
		<tr>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="2">Pre-Payments Manually Applied Amounts&nbsp;:</td>
			<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberFormat($arrAppliedPatPayTot['applied_amt'],2).'&nbsp;</td>
			<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
		</tr>
		<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>';
	
		//IF MANUALLY APPLIED CI/CO ALSO EXIST THEN MAKE TOTAL OF BOTH
		if($totalCICOManuallyApplied>0){
			$tot= $totalCICOManuallyApplied + $arrAppliedPatPayTot['applied_amt'];
			$manually_applied_pre_pay_html.='
			<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="2">Total Manually Applied Amounts&nbsp;:</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.numberFormat($tot,2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF">&nbsp;</td>
			</tr>
			<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>';
		}
		$manually_applied_pre_pay_html.='</table>';
	}		
}

//--- GET SUMMARY HEADER DATA FOR HTML FILE -------
if($printFile == true){
	//--- GET CSV DATA FOR SUMMARY -----
	$csvFileData .= <<<DATA
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8">		
DATA;
}


// TOTAL PRE PAYMENT NOT APPLIED
if(sizeof($arrOperatorDepTots)>0 && strstr($viewBy,'inout')){
	if($totNotAppliedPreAmt<=0 || $totNotAppliedPreAmt==''){
		$totNotAppliedPreAmt='$0.00';
	}else{
		$totNotAppliedPreAmt=numberFormat($totNotAppliedPreAmt, 2);
	}
	$csvFileData.='
	<table width="100%" border="0" cellpadding="1" cellspacing="1" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">				
	<tr id="heading_orange">
		<td align="left" colspan="3" class="text_b_w" style="font-weight:bold">Pre Payment Amounts</td>
	</tr>
	<tr>
		<td align="right" bgcolor="#FFFFFF" class="text_10b" style="width:250px">Payment Done But Not Applied :</td>
		<td bgcolor="#FFFFFF" class="text_10b" style="width:150px; text-align:right">'.$totNotAppliedPreAmt.'</td>
		<td bgcolor="#FFFFFF" style="width:auto; text-align:left"></td>
	</tr>
	<tr style="height:10px; background-color:#FFF"><td colspan="3"></td></tr>

	</table>';
	
	$firstColW='200px';
	$secColW='150px';
	$thirdColW='695px';
	$pdfData .='
	<table width="100%" border="0" cellpadding="1" cellspacing="1" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">				
	<tr id="heading_orange">
		<td width="100%" align="left" colspan="3" class="text_b_w" style="font-weight:bold">Pre Payment Amounts</td>
	</tr>
	<tr>
		<td align="right" bgcolor="#FFFFFF" class="text_10b" style="width:'.$firstColW.'">Payment Done But Not Applied :</td>
		<td bgcolor="#FFFFFF" class="text_10b" style="width:'.$secColW.'; text-align:right">'.$totNotAppliedPreAmt.'</td>
		<td bgcolor="#FFFFFF" class="text_10b" style="width:'.$thirdColW.'; text-align:left">&nbsp;</td>
	</tr>
	</table>';
}

//ADDING MANUAALY APPLIED BLOCKS TO HTML
$csvFileData.= 
$manually_applied_cico_html.
$manually_applied_pre_pay_html.
$manually_applied_html;

$pdfData.=
$manually_applied_cico_html.
$manually_applied_pre_pay_html.
$manually_applied_html;

//--TOOLTIP
$tooltip='Red coloured CI/CO and Pre-Payments represents that there is refund amount deducted from these payments.';
if($pdfData){
$pdfData .= '<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8" >
	<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
	<tr><td style="width:20px;" class="info" style="background-color:#FFFFFF;">&nbsp;</td>
	<td style="width:4px;" height="5px;" bgcolor="#FF0000"></td>
	<td class="info" style="padding-left:20px; ; background-color:#FFFFFF;">
	'.$tooltip.'
	</td>
	</tr>
	</table>';
}

if($csvFileData){
$csvFileData .= '<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8" >
		<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
		<tr><td style="width:20px;" style="background-color:#FFFFFF;">&nbsp;</td>
		<td style="width:4px;" height="5px;" bgcolor="#FF0000"></td>
		<td class="info" style="padding-left:20px; ; background-color:#FFFFFF;">
		'.$tooltip.'<br/>Refund amount can be view by mouse over on red coloured amount.
		</td>
		</tr>
		</table>';
}
//----------

if($printFile == true){
	$csvFileData .= <<<DATA
	</table>
DATA;
}
?>