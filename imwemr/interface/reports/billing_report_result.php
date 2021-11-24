<?php

$without_pat = "yes";
require_once("reports_header.php");
$dateFormat= get_sql_date_format();
$phpDateFormat = phpDateFormat();
$curDate = date($phpDateFormat.' h:i A');		
$FCName= $_SESSION['authId'];
$page_data = NULL;
$pdf_data = NULL;
$curDate = date($phpDateFormat);
if($Start_date == ""){
	$Start_date = $curDate;
	$End_date = $curDate;
}

$sel_grp = $CLSReports->report_display_selected(implode(',', $grp_id),'group',1,$grp_cnt);
$sel_fac = $CLSReports->report_display_selected(implode(',', $facility_name),'facility_tbl',1,$fac_cnt);
$sel_phy = $CLSReports->report_display_selected(implode(',', $phyId),'physician',1,$phy_cnt);
	
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
	//---------------------

	
	//VARIABLE DECLARATION
	$join_query=$where_query='';	
	
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$createdBy = ucfirst(trim($op_name_arr[1][0]));
	$createdBy .= ucfirst(trim($op_name_arr[0][0]));

	//SET ARRAY
	if(empty($_REQUEST['facility_name'])==false){
		$facility_name=array_combine($_REQUEST['facility_name'],$_REQUEST['facility_name']);
	}
	//FACILITY
	$facilityId = join(',',$facility_name);
	//PROCEDURE
	$rqArrProcedures = $_REQUEST['procedures'];
	$rqProcedures = join(',',$rqArrProcedures);
	//PHYSICIAN
	$sel_pro_arr=$rqArrPhyId = $_REQUEST['phyId'];
	$sel_pro = join(',',$rqArrPhyId);
	//INS TYPE
	$rqArrInsType = $_REQUEST['ins_type'];
	foreach ($rqArrInsType as $key => $value) {
		$rqArrInsType[$key] = "'".$value."'";
	}
	$rqInsType = join(',',$rqArrInsType);
	//INSURANCE
	$rqArrInsProvider = $_REQUEST['insId'];	
	$rqInsProvider = join(',',$rqArrInsProvider);
	//ICD10
	$rqDxcode10 = join(',',$_REQUEST['Dxcode10']);	
	//APPT STATUS
	$rqArrAppStatus = $_REQUEST['ap_status'];
	$rqAppStatus = join(',',$rqArrAppStatus);
	//Group ID
	if(sizeof($_REQUEST['groups'])>0){
		$groupId = $_REQUEST['groups'];
	}
	$grp_id = join(',',$groupId);
	
	//Operator_id
	$rqArrOprId = $_REQUEST['operator_id'];
	$operator = join(',',$rqArrOprId);
	//ARRAY MAP FOR PREFFERED PHONE
	$arrMapPreferredPhone=array('0'=>'Home Phone','1'=>'Work Phone','2'=>'Mobile Phone');
	//DATE FORMAT
	$StartDate = getDateFormatDB($Start_date);
	$EndDate = getDateFormatDB($End_date);
}	

$blExcludePageTags = false; //default setting for summary report
$printFile = true;
if(trim($_REQUEST['Submit']) != ''){
	$conditionChk = false;
	$printFile = false;
	$arrEncounters=array();
	
	$sel_date = $Start_date;
	$curDate =getDateFormatDB($StartDate);

	$physicianNameArr = $CLSCommonFunction->drop_down_providers($strPhysician, '1', '1', '1', 'report');
	if(count($sel_pro_arr) == 0){
		if(count($physicianNameArr)>0){
			$sel_pro_arr = array_keys($physicianNameArr);
		}
	}
	$sel_pro = join(',',$sel_pro_arr);
	
	//--- Get Appointment Cancelled on this date (new requirement - show only those cancelled appts which have been cancelled on selected date) ------
	$query = "select sch_id from previous_status where date_format(dateTime, '%Y-%m-%d') = '$curDate' 
									and status = '18'";
	$qryRes = imw_query($query);
	$ps_id = array();
	while($schQryDetailsRes  =imw_fetch_assoc($qryRes)){
		$ps_id[] = $schQryDetailsRes['sch_id'];
	}
	$str_ps_id = "";
	$str_ps_id = implode(", ",$ps_id);
	if($str_ps_id == ""){
		$str_ps_id = "-1";
	}
	//------GET GROUP DATA---
	$arrGroups = array();
	$q = "select gro_id,name from groups_new";
	$qry = imw_query($q);
	$res  = imw_fetch_assoc($qry);
	if(count($res)>0){
		foreach($res as $key=>$row)
		$arrGroups[$row['gro_id']] = $row['name'];
	}
	//-----------------------
	//---- GET APPOINTMENT STATUS OF SELECTED DATE -----------
	$main_query = "
	SELECT schedule_appointments.sa_patient_app_status_id, 
	schedule_appointments.id, schedule_appointments.sa_facility_id, facility.name, 
	schedule_appointments.sa_patient_id, schedule_appointments.sa_app_starttime, 
	schedule_appointments.sa_app_endtime , schedule_appointments.sa_app_duration,
	schedule_appointments.sa_doctor_id, patient_data.lname,patient_data.fname,patient_data.mname,
	slot_procedures.proc 
	FROM schedule_appointments 
	LEFT JOIN slot_procedures ON slot_procedures.id = schedule_appointments.procedureId
	JOIN patient_data ON schedule_appointments.sa_patient_id = patient_data.id 
	LEFT JOIN facility ON schedule_appointments.sa_facility_id = facility.id 
	WHERE schedule_appointments.sa_app_start_date = '$curDate' 
	AND if(schedule_appointments.sa_patient_app_status_id = '18', schedule_appointments.id in ($str_ps_id), '1 = 1') 
	AND (schedule_appointments.sa_doctor_id > 0 or schedule_appointments.sa_test_id > 0) 
	AND schedule_appointments.sa_patient_app_status_id != '203'";
	if(empty($facilityId) === false){
		$main_query .= " and schedule_appointments.sa_facility_id in($facilityId)";
	}
	if(empty($sel_pro) === false){
		$main_query .= " and schedule_appointments.sa_doctor_id in($sel_pro)";
	}
	if(empty($grp_id) === false){
		$main_query .= " and facility.default_group in($grp_id)";
	}
	if(empty($operator) === false){
		$main_query.= " AND schedule_appointments.status_update_operator_id IN(".$operator.")";
	}
	$main_query .= " order by facility.name, schedule_appointments.sa_app_starttime,
				patient_data.lname,patient_data.fname";
	
	$qry_main = imw_query($main_query);
	$appointmentDataArr = array();
	$sidArr = array();
	$arrApptPats=array();
	while($appQryRes  = imw_fetch_assoc($qry_main)){
		$sch_id = $appQryRes['id'];
		$sa_doctor_id = $appQryRes['sa_doctor_id'];
		$appointmentDataArr[$sa_doctor_id][] = $appQryRes;
		$sidArr[] = $appQryRes['id'];
		$arrApptPats[$appQryRes['sa_patient_id']][] = $appQryRes['sa_patient_id'];
	}
	$sidStr = join(',',$sidArr);
	
	//--- GET CHECK IN AND CHECK OUT TIME ------
	$main_query = "select sch_id,status,status_time from previous_status where sch_id in($sidStr)";
	$qry_check_in_out = imw_query($main_query);
	$schDataArr = array();
	while($schQryRes  = imw_fetch_assoc($qry_check_in_out)){
		$sch_id = $schQryRes['sch_id'];
		$schDataArr[$sch_id][] = $schQryRes;
	}
		
	//pre($schDataArr);
	//--- SUPER BILL CHARGES ------
	$superbill_query = "Select todaysCharges, patientId, encounterId, sch_app_id from superbill 
									where dateOfService = '$curDate' AND del_status='0' AND merged_with='0'";
	if(empty($grp_id) === false){
		$superbill_query .= " and superbill.gro_id in($grp_id)";
	}								
	$superbill_query .= " ORDER BY idSuperBill";
	
	$super_bill_query = imw_query($superbill_query);
	$superbillDataArr = array();
	while($superbillQryRes  = imw_fetch_assoc($super_bill_query)){
		$pid = $superbillQryRes['patientId'];
		$eid = $superbillQryRes['encounterId'];
		$superbillDataArr[$pid][$eid] = $superbillQryRes['todaysCharges'];
		$arrEncountersSn[$pid][]= $eid;
		$arrEncSchId[$superbillQryRes['sch_app_id']][$eid] = $eid;
	}
	//pre($superbillDataArr);pre($arrEncountersSn);pre($arrEncSchId);
	//--- GET ALL CHARGES DETAILS ------
	$all_charges_qry = "select totalAmt,submitted,encounter_id,patient_id,superbill.todaysCharges,
		patient_charge_list.sch_app_id  
		from patient_charge_list LEFT JOIN superbill ON superbill.encounterId=patient_charge_list.encounter_id where
	 	(patient_charge_list.postedDate = '$curDate' or patient_charge_list.entered_date = '$curDate' 
		or patient_charge_list.Re_submitted_date = '$curDate' or patient_charge_list.date_of_service = '$curDate') 
		AND patient_charge_list.del_status='0'";
	if(empty($grp_id) === false){
		$all_charges_qry .= " and patient_charge_list.gro_id in($grp_id)";
	}		
	
	$charges_qry = imw_query($all_charges_qry);
	$patientChargesDataArr = array();
	$encounterIdArr = array();
	while($chargesQryRes  = imw_fetch_assoc($charges_qry)){
		$patient_id = $chargesQryRes['patient_id'];
		$encounter_id = $chargesQryRes['encounter_id'];
		$sch_id = $chargesQryRes['sch_app_id'];
		$submitted = $chargesQryRes['submitted'];
		$totalAmt = $chargesQryRes['totalAmt'];
		$encounterIdArr[] = $chargesQryRes['encounter_id'];
		$patientChargesDataArr[$patient_id]['encounter_id'][$encounter_id] = $chargesQryRes['encounter_id'];
		
		if(strtolower($submitted) == 'true'){
			$submittedEncIdArr[$encounter_id] = $encounter_id;
		}
		else{
			$patientChargesDataArr[$patient_id]['not_posted_amount'][$encounter_id] = $totalAmt;
		}	
		
		if($chargesQryRes[$i]['todaysCharges']>0){
			$superbillDataArr[$patient_id][$encounter_id] = $chargesQryRes['todaysCharges'];
		}
		
		if(!in_array($encounter_id, $arrEncountersSn[$patient_id])){
			$arrEncountersSn[$patient_id][]= $encounter_id;
		}
		
		$arrEncSchId[$sch_id][$encounter_id] = $encounter_id;
	}
	//pre($arrEncountersSn);
	//pre($arrEncSchId);
	$encounterIdStr = join(',',$encounterIdArr);
	$submittedEncIdStr = join(',',$submittedEncIdArr);

	// GET POSTED AND SUBMITTED AMOUNT
	if(sizeof($submittedEncIdArr)>0){
		$subQry="Select encounter_id, patient_id, posted_amount FROM posted_record 
				WHERE encounter_id IN($submittedEncIdStr) ORDER BY id";
		$subRs = imw_query($subQry);
		while($subRes = imw_fetch_array($subRs)){
			$pid= $subRes['patient_id'];
			$eid = $subRes['encounter_id'];
			$submittedEncAmt[$pid][$eid] = $subRes['posted_amount'];
			$patientChargesDataArr[$pid]['posted_amount'][$eid] = $subRes['posted_amount']; 
		}
	}
	//---------------------------------	
	//--- GET ALL TRANSACTION -------
	$all_transaction = "Select patient_charges_detail_payment_info.paidForProc + patient_charges_detail_payment_info.overPayment
	as paidForProc, patient_chargesheet_payment_info.payment_mode,
	patient_charges_detail_payment_info.patient_pre_payment_id, 
	patient_chargesheet_payment_info.encounter_id,
	patient_charges_detail_payment_info.payment_id, patient_charge_list.patient_id 
	FROM patient_charges_detail_payment_info 
	JOIN patient_chargesheet_payment_info ON patient_chargesheet_payment_info.payment_id = patient_charges_detail_payment_info.payment_id 
	LEFT JOIN patient_charge_list ON patient_charge_list.encounter_id = patient_chargesheet_payment_info.encounter_id 
	WHERE patient_charges_detail_payment_info.paidDate = '$curDate' 
	and patient_chargesheet_payment_info.encounter_id in($encounterIdStr) 
	and patient_charges_detail_payment_info.deletePayment != 1 
	AND patient_pre_payment_id<=0 
	ORDER BY patient_charges_detail_payment_info.paidDate";
	$transaction = imw_query($all_transaction);
	$paymentDataArr = array();
	while($paymentQryRes = imw_fetch_assoc($transaction)){
		$rs=imw_query("Select id FROM check_in_out_payment_post WHERE acc_payment_id='".$paymentQryRes['payment_id']."'");
		if(imw_num_rows($rs)<=0){
			$patient_id = $paymentQryRes['patient_id'];
			$encounter_id = $paymentQryRes['encounter_id'];
			$payment_mode = strtolower($paymentQryRes['payment_mode']);
			$paidForProc = $paymentQryRes['paidForProc'];
	
			if($payment_mode == 'cash'){
				$paymentDataArr[$encounter_id]['cash'][] = $paidForProc;
			}
			else if($payment_mode == 'check'){
				$paymentDataArr[$encounter_id]['check'][] = $paidForProc;
			}
			else if($payment_mode == 'money order'){
				$paymentDataArr[$encounter_id]['money order'][] = $paidForProc;
			}
			else if($payment_mode == 'veep'){
				$paymentDataArr[$encounter_id]['veep'][] = $paidForProc;
			}
			else if($payment_mode == 'eft'){
				$paymentDataArr[$encounter_id]['eft'][] = $paidForProc;
			}
			else{
				$paymentDataArr[$encounter_id]['credit'][] = $paidForProc;
			}
		}
	}

	//--- GET ALL APPOINTMENTS ----------
	// CI/CO PATIENTS
	$sch_query ="Select sa.sa_patient_id, sa.sa_facility_id, sa.sa_doctor_id,   
	cioDet.item_payment as 'cioPayment', cioPay.total_payment, cioPay.payment_method, cioPay.payment_id, cioDet.id, cioDet.status as 'detStatus', 
	pd.fname as 'pfname', pd.mname as 'pmname', pd.lname as 'plname' 
	FROM schedule_appointments sa 
	LEFT JOIN check_in_out_payment cioPay ON cioPay.sch_id  = sa.id 
	LEFT JOIN check_in_out_payment_details cioDet ON cioDet.payment_id = cioPay.payment_id  
	LEFT JOIN patient_data pd ON pd.id = sa.sa_patient_id 
	LEFT JOIN facility ON sa.sa_facility_id = facility.id 
	WHERE cioPay.created_on ='".$curDate."' AND sa.sa_app_start_date = '".$curDate."' AND cioPay.total_payment>0 AND cioDet.status='0'";
	if(empty($facilityId) === false){
		$sch_query .= " and sa.sa_facility_id in($facilityId)";
	}
	if(empty($sel_pro) === false){
		$sch_query .= " and sa.sa_doctor_id in($sel_pro)";
	}
	if(empty($grp_id) === false){
		$sch_query.= " and facility.default_group in($grp_id)";
	}
	if(empty($operator) === false){
		$sch_query.= " AND sa.status_update_operator_id IN(".$operator.")";
	}
	$sch_query_rs = imw_query($sch_query);
	while($sch_query_res = imw_fetch_array($sch_query_rs)){
		//query to get refund detail for current ci/co payments if any
		$refundAmt=0;
		$qryRef = imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND ci_co_id = '".$sch_query_res['id']."'")or die(imw_error().'_471');
		while($rsRef = imw_fetch_array($qryRef))
		{
			$refundAmt+=$rsRef['ref_amt'];
		}
		imw_free_result($qryRef);
		
		$paymentId = $sch_query_res['payment_id'];
		$patient_id = $sch_query_res['sa_patient_id'];
		$fac_id = $sch_query_res['sa_facility_id'];
		$doc_id = $sch_query_res['sa_doctor_id'];
		$payMethod  =strtolower($sch_query_res['payment_method']);
		
		if(count($arrTemp[$paymentId])<=0){
			$arrCIOTots['CIO_AMT']+= $sch_query_res['total_payment']-$refundAmt;
			$arrCIOTots['CIO_AMT_REF']+=$refundAmt;
			
			$arrTemp[$paymentId] = $paymentId;

			$cioPatientPay[$doc_id][$fac_id][$patient_id][$payMethod]+= $sch_query_res['total_payment']-$refundAmt;
		}
		// COLLOECT DETAILS IDS FOR MANUAL AND ENCOUNTER APPLIED
		$arrTempAccPayDetIds[$sch_query_res['id']]=$sch_query_res['id'];

	}
	unset($sch_query_rs);
	
	
	if(sizeof($arrTempAccPayDetIds)>0){
		$strTempAccPayDetIds = implode(',', $arrTempAccPayDetIds);
		unset($arrTempAccPayDetIds);

		//GETTING CI/CO MANUAL APPLIED AMOUNTS		
		$sch_query ="Select cioPost.manually_payment, cioPost.manually_date, cioPost.acc_payment_id   
		FROM check_in_out_payment_post cioPost WHERE cioPost.status='0' 
		AND cioPost.check_in_out_payment_detail_id IN(".$strTempAccPayDetIds.")";
		$sch_query_rs = imw_query($sch_query);
		while($sch_query_res = imw_fetch_array($sch_query_rs)){
	
			// MANUAL APPLIED
			if($sch_query_res['manually_payment']>0 && $sch_query_res['manually_date']==$curDate){	
				$arrCIOTots['APPLIED_AMT']+=$sch_query_res['manually_payment'];
			}
			
			// COLLECT IDS FOR ENCOUNTER APPLIED
			if($sch_query_res['acc_payment_id']>0){
				$arrTempAccPayId[$sch_query_res['acc_payment_id']]=$sch_query_res['acc_payment_id'];
			}
		}
		unset($sch_query_rs);
		
		//GETTING CI/CO ENCOUNTER APPLIED AMOUNTS		
		if(sizeof($arrTempAccPayId)>0){
			$strTempAccPayId = implode(',', $arrTempAccPayId);
			unset($arrTempAccPayId);		
			$sch_query ="Select patPayDet.paidForProc 
			FROM patient_charges_detail_payment_info patPayDet 
			LEFT JOIN patient_chargesheet_payment_info patPayInfo ON patPayInfo.payment_id = patPayDet.payment_id 
			WHERE patPayInfo.transaction_date ='".$curDate."' AND patPayDet.payment_id IN(".$strTempAccPayId.")";
			$sch_query_rs = imw_query($sch_query);
			while($sch_query_res = imw_fetch_array($sch_query_rs)){
		
				// ENCOUNTER APPLIED
				$arrCIOTots['APPLIED_AMT']+=$sch_query_res['paidForProc'];
			}
			unset($sch_query_rs);
		}
	}
	
	// GET PATIENT PRE PAYMENTS
	$patQry="Select pDep.id, pDep.patient_id, pDep.id, pDep.paid_amount, pDep.apply_payment_date, pDep.apply_payment_type, pDep.apply_amount,
	pDep.payment_mode, sa.sa_facility_id, sa.sa_doctor_id FROM patient_pre_payment pDep 
	JOIN schedule_appointments sa ON sa.sa_patient_id = pDep.patient_id 
	LEFT JOIN facility ON sa.sa_facility_id = facility.id 
	WHERE pDep.entered_date ='".$curDate."' AND sa_app_start_date = '".$curDate."' 
	AND sa.sa_patient_app_status_id NOT IN(203, 18, 3) AND del_status='0'";
	if(empty($grp_id) === false){
		$patQry.= " and facility.default_group in($grp_id)";
	}
	if(empty($facilityId) === false){
		$patQry .= " and sa.sa_facility_id in($facilityId)";
	}
	if(empty($sel_pro) === false){
		$patQry .= " and sa.sa_doctor_id in($sel_pro)";
	}
	if(empty($operator) === false){
		$patQry.= " AND sa.status_update_operator_id IN(".$operator.")";
	}
	$arrDepIds=array();
	$arrPrePayIds=array();
	$patQryRs = imw_query($patQry);
		while($patQryRes = imw_fetch_array($patQryRs)){
		if(!$arrPrePayIds[$patQryRes['id']]){
			##########################################################
			#query to get refund detail for current pre payment if any
			##########################################################
	
			$qryRef = imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND pmt_id = '".$patQryRes['id']."'")or die(imw_error().'_656');
			while($rsRef = imw_fetch_array($qryRef)){
				$refundAmt+=$rsRef['ref_amt'];
			}
			imw_free_result($qryRef);
			
			$id = $patQryRes['id'];
			$patient_id = $patQryRes['patient_id'];
			$fac_id = $patQryRes['sa_facility_id'];
			$doc_id = $patQryRes['sa_doctor_id'];
			
			$payMethod = strtolower($patQryRes['payment_mode']);
			if($patQryRes['apply_payment_type']=='manually' && $patQryRes['apply_payment_date']==$curDate){
				$arrDepTots['APPLIED_AMT']+=$patQryRes['apply_amount'];
			}
			$arrDepTots['PAT_DEPOSIT']+=$patQryRes['paid_amount']-$refundAmt;
			$arrDepTots['PAT_DEPOSIT_REF']+=$refundAmt;
			$arrDepIds[$patQryRes['id']]=$patQryRes['id'];
	
			$prePayPatients[$doc_id][$fac_id][$patient_id][$payMethod]+= $patQryRes['paid_amount']-$refundAmt;
			$arrPrePayIds[$patQryRes['id']]=$patQryRes['id'];
		}
	}
	unset($arrPrePayIds);
	
	
	// GET PRE PAT ENCOUNTER APPLIED AMTS
	if(count($arrDepIds)>0){
		$strDepIds=implode(',', $arrDepIds);
		$preAppQry="Select pDep.id, pDep.entered_by, patPayDet.paidForProc, patPayDet.patient_pre_payment_id FROM patient_pre_payment pDep 
		LEFT JOIN patient_charges_detail_payment_info patPaydet ON patPayDet.patient_pre_payment_id = pDep.id 
		WHERE pDep.id IN($strDepIds) AND patPayDet.deletePayment='0' AND patPayDet.unapply='0'";
		$preAppRs = imw_query($preAppQry);
		while($preAppRes = imw_fetch_array($preAppRs)){
			$payId =$preAppRes['patient_pre_payment_id'];
			$arrDepTots['APPLIED_AMT']+=$preAppRes['paidForProc'];
		}
	}
	$newpage = 'No';
	$reportProcess = "details";
	if(count($appointmentDataArr) > 0){
		$printFile = true;
		include('dayReport'.$reportProcess.'.php');
	}
}

$HTMLCreated=0;
if($strHTML and $conditionChk == true){
	$HTMLCreated=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$csv_data;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$strHTML;

	$file_location = write_html($strHTML);
}else{
	if($callFrom != 'scheduled'){
		$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}

if($callFrom != 'scheduled'){
	echo $csv_file_data;
}
?>