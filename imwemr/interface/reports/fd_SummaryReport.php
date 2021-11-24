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

$columnsArr = array();
$none_charge_list = array();
$get_charge_list_id = array();
$printFile = false;
$sortByPayment = NULL;
$showCurrencySymbol=showCurrency();
$sqlDateFormat=get_sql_date_format();
if($sortBy == 'patientName'){
	$sortByPat = 'patient_data.lname,patient_data.fname';
	$sortByPat_comm = ' patient_data.lname,patient_data.fname, ';
	$sortByPat_Pre = ' pData.lname,pData.fname ';
}
else if($sortBy == 'paymentDate'){
	$sortByDot_ci_comm = 'check_in_out_payment.created_on, ';
	$sortByDot_pre_comm = ' pDep.entered_date ';
	$sortByPayment = ' patient_charge_list.entered_date';
}
else{
	$sortByDot_ci_comm = 'check_in_out_payment.created_on, ';
	$sortByPat_Pre = ' pData.lname,pData.fname ';
	$sortByDos = ' patient_charge_list.date_of_service';
}

$group_query = imw_query("select cpt_code_name from cpt_group_tbl where cpt_group_name ='OPTICAL'");
$groupQryRes = imw_fetch_assoc($group_query);	
if(count($groupQryRes)>0){
	$main_proc =trim($groupQryRes['cpt_code_name']);
}
$main_proc_arr=explode(',',$main_proc);
$main_proc_str="";
for($j=0;$j<count($main_proc_arr);$j++){
	$main_proc_str .="'".trim($main_proc_arr[$j])."',";
}

$main_proc_str =substr($main_proc_str,0,-1);
$group_query = imw_query("select cpt_fee_id,cpt4_code from cpt_fee_tbl where  cpt4_code in ($main_proc_str)");
while($groupQryRes = imw_fetch_assoc($group_query)){
	$main_tbl_cpt_arr[$groupQryRes['cpt_fee_id']] = $groupQryRes['cpt4_code'];
}

//if($processReport == 'fd_DetailReport'){
if($reportType == 'encounter'){

//--- GET PAYMENT RECORDS ----------
	$query = "Select patient_charges_detail_payment_info.payment_details_id,
	patient_charges_detail_payment_info.paidForProc, 
	patient_charges_detail_payment_info.overPayment,
	patient_charges_detail_payment_info.charge_list_detail_id, 
	patient_chargesheet_payment_info.paymentClaims,
	users.lname as usersLname,users.fname as usersFname, 
	patient_chargesheet_payment_info.*
	from patient_charges_detail_payment_info
	join patient_chargesheet_payment_info on
	patient_chargesheet_payment_info.payment_id = patient_charges_detail_payment_info.payment_id
	join users on users.id = patient_chargesheet_payment_info.operatorId
	where patient_chargesheet_payment_info.operatorId in ($operatorId)
	and patient_charges_detail_payment_info.deletePayment = '0'
	and (patient_chargesheet_payment_info.transaction_date between '$startDate' and '$endDate' 
	or patient_chargesheet_payment_info.date_of_payment between '$startDate' and '$endDate') 
	order by patient_chargesheet_payment_info.transaction_date,
	patient_chargesheet_payment_info.payment_id";
	$paid_encounter_arr = array();
	$mainPayment_Arr = array();
	$payment_id_arr = array();
	$paymentQry	= imw_query($query);
	while($paymentQryRes = imw_fetch_assoc($paymentQry)){
		$encounter_id = $paymentQryRes['encounter_id'];
		$payment_details_id = $paymentQryRes['payment_details_id'];
		$paid_encounter_arr[$encounter_id] = $encounter_id;	
		$payment_id = $paymentQryRes['payment_id'];
		$mainPayment_Arr[$encounter_id]['payment_id'][] = $payment_details_id;
		if($paymentQryRes['paymentClaims']=='Negative Payment'){
			$paymentQryRes['paidForProc']='-'.$paymentQryRes['paidForProc'];
		}
		$mainPayment_Arr[$encounter_id][$payment_details_id] = $paymentQryRes;
		$mainPayment_det_Arr[$encounter_id][$payment_details_id][$paymentQryRes['charge_list_detail_id']] = $paymentQryRes;
		$mainPayment_dets_Arr[$encounter_id][]=$payment_details_id;
		$operatorInitial = substr($paymentQryRes['usersFname'],0,1);
		$operatorInitial .= substr($paymentQryRes['usersLname'],0,1);
		$operatorInitial = strtoupper($operatorInitial);
		$mainPayment_Arr[$encounter_id][$payment_details_id]['operatorInitial'] = $operatorInitial;
		$payment_id_arr[] = $payment_id;
	}

	$paid_encounter_str = join(',',$paid_encounter_arr);
	$mainPaymentArr = array();
	$primaryProviderIdArr = array();
	$mainPaymentEncounterArr = array();	
	$departmentEncounterArr = array();
	if($paid_encounter_str){
	$qry = "select patient_data.lname,patient_data.mname,patient_data.fname,
			patient_data.id,patient_charge_list.encounter_id, patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId',
			date_format(patient_charge_list.date_of_service,'".$sqlDateFormat."') 
			as date_of_service from patient_charge_list join
			patient_data on patient_data.id = patient_charge_list.patient_id
			where patient_charge_list.encounter_id in ($paid_encounter_str)";
	if(empty($sc_name) == false){
		$qry .= " and patient_charge_list.facility_id IN ($sc_name)";
	}
	if(empty($grp_id) == false){
		$qry .= " and patient_charge_list.gro_id IN ($grp_id)";
	}

	if(empty($Physician) == false){
		$qry .= " and patient_charge_list.primary_provider_id_for_reports IN ($Physician)";
	}
	
	$qry .= " order by $sortByPat $sortByPayment $sortByDos";
	$qryrs	= imw_query($qry);
	while($qryRes = imw_fetch_assoc($qryrs)){
		$printFile = true;
		$encounter_id = $qryRes['encounter_id'];
		$primaryProviderId = $qryRes['primaryProviderId'];
		$departmentEncounterArr[] = $qryRes['encounter_id'];
		$primaryProviderIdArr[$primaryProviderId] = $qryRes['primaryProviderId'];
		$paidProEncounter[$primaryProviderId][] = $encounter_id;
		$mainPaymentEncounterArr[$encounter_id] = $encounter_id;
		$mainPaymentArr[$encounter_id]['charge_list'] = $qryRes;
		$mainPaymentArr[$encounter_id]['payment'] = $mainPayment_Arr[$encounter_id];
	}
}

//--- GET ALL RESULTS FROM PATIENT CHARGE LIST TABLE ---------
$pymntQry = "select patient_charge_list.encounter_id,patient_charge_list.patient_id,
		patient_charge_list.submitted,patient_charge_list.Re_submitted,
		patient_charge_list.postedDate,patient_charge_list.entered_date,
		date_format(patient_charge_list.date_of_service , '".$sqlDateFormat."') as date_of_service, 
		patient_data.id,patient_data.lname,patient_data.fname,patient_data.mname,
		patient_charge_list.operator_id,patient_charge_list.submitted_operator_id,
		patient_charge_list.totalAmt,patient_charge_list.charge_list_id,
		patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId',patient_charge_list.charge_list_id,
		patient_charge_list.charge_list_id from patient_charge_list join patient_data on 
		patient_data.id = patient_charge_list.patient_id
		where (patient_charge_list.submitted_operator_id in($operatorId)
		or patient_charge_list.operator_id in ($operatorId))
		and (patient_charge_list.postedDate between '$startDate' and '$endDate'
		or patient_charge_list.entered_date between '$startDate' and '$endDate')";
if(empty($sc_name) == false){
	$pymntQry .= " and patient_charge_list.facility_id IN ($sc_name)";
}
if(empty($grp_id) == false){
	$pymntQry .= " and patient_charge_list.gro_id IN ($grp_id)";
}

if(empty($Physician) == false){
	$pymntQry .= " and patient_charge_list.primary_provider_id_for_reports IN ($Physician)";
}
$pymntQry .= " group by patient_charge_list.encounter_id";

$mainPostedArr = array();
$mainPostedProviderArr = array();
$notSubmitedArr = array();
$mainNotPostedProviderArr = array();
$submitedArr = array();
$mainRePostedProviderArr = array();
$reSubmitedPaidArr = array();
$notSubmitedPaidArr = array();
$submitedPaidArr = array();
$paidReSubmitEncounterArr = array();
$paidNotSubmitEncounterArr = array();
$dept_charge_list_id_arr = array();
$mainPostedEncounterArr = array();
$mainNotPostedArr = array();
$mainRePostedArr = array();
$mainPaymentEncounterStr = join(',',$mainPaymentEncounterArr);

$pymntQryRs	= imw_query($pymntQry);
while($mainQryRes = imw_fetch_assoc($pymntQryRs)){
	$primaryProviderId = $mainQryRes['primaryProviderId'];
	$encounter_id = $mainQryRes['encounter_id'];
	$charge_list_id = $mainQryRes['charge_list_id'];
	$charge_list_id_arr[] = $mainQryRes['charge_list_id'];
	$submitted_operator_id = $mainQryRes['submitted_operator_id'];
	$submitted = $mainQryRes['submitted'];
	$Re_submitted = $mainQryRes['Re_submitted'];
	$submitChk = false;
	if($submitted_operator_id > 0){
		preg_match("/$submitted_operator_id/",$operatorId,$submitChkArr);
		if(count($submitChkArr)>0){
			$submitChk = true;
		}
	}
	preg_match("/$encounter_id/",$mainPaymentEncounterStr,$pidChkArr);
	
	//--- ALL RE SUBMITTED ENCOUNTERS ---------
	if($Re_submitted == 'true' && $submitted == 'true' && $submitChk == true){
		$printFile = true;
		if(count($pidChkArr)>0){
			$paidReSubmitEncounterArr[$encounter_id] = $encounter_id;
			unset($mainPaymentEncounterArr[$encounter_id]);
			$reSubmitedPaidArr[$encounter_id] = $mainPaymentArr[$encounter_id];
		}
		else{	
			$mainRePostedArr[$primaryProviderId] = $primaryProviderId;
			$mainRePostedEncounterArr[$primaryProviderId][] = $charge_list_id;
			$mainRePostedProviderArr[$primaryProviderId][$charge_list_id] = $mainQryRes;
			$reSubmitedArr[] = $mainQryRes;
		}
	}
	
	//--- ALL NOT SUBMITTED ENCOUNTERS ---------
	else if($Re_submitted == 'false' && $submitted == 'false' && $submitChk == false){
		$printFile = true;		
		if(count($pidChkArr)>0){
			$paidNotSubmitEncounterArr[$encounter_id] = $encounter_id;
			unset($mainPaymentEncounterArr[$encounter_id]);
			$notSubmitedPaidArr[$encounter_id] = $mainPaymentArr[$encounter_id];
		}else{	
			$mainNotPostedEncounterArr[$primaryProviderId][] = $charge_list_id;
			$mainNotPostedProviderArr[$primaryProviderId][$charge_list_id] = $mainQryRes;
			$mainNotPostedArr[$primaryProviderId] = $primaryProviderId;
			$notSubmitedArr[] = $mainQryRes;
		}
	}
	//--- ALL SUBMITTED ENCOUNTERS ---------
	else if($Re_submitted == 'false' && $submitted == 'true' && $submitChk == true){
		$printFile = true;
		$submitedArr[] = $mainQryRes;		
		if(count($pidChkArr)>0){
			$mainPaymentEncounterArr[$encounter_id] = $encounter_id;
			$submitedPaidArr[$encounter_id] = $mainPaymentArr[$encounter_id];
		}
		$mainPostedEncounterArr[$primaryProviderId][] = $charge_list_id;
		$mainPostedProviderArr[$primaryProviderId][$charge_list_id] = $mainQryRes;
		$mainPostedArr[$primaryProviderId] = $primaryProviderId;
	}	
}

	$charge_list_id_str = join(',',$charge_list_id_arr);
	$chrQry = "select charge_list_detail_id,procCode from patient_charge_list_details where charge_list_id in($charge_list_id_str)";
	$chrQryRs	= imw_query($chrQry);
	while($mainQryRes = imw_fetch_assoc($chrQryRs)){
		if($main_tbl_cpt_arr[$mainQryRes['procCode']]){	
			$pcld_proccode[$mainQryRes['charge_list_detail_id']] = $main_tbl_cpt_arr[$mainQryRes['procCode']];
		}else{
			$pcld_proccode[$mainQryRes['charge_list_detail_id']] = 0;
		}
	}

	//--- GET ALL ENCOUNTER ----
	$encounter_id_arr = array_keys($paidReSubmitEncounterArr);
	$encounter_id_arr = array_merge($encounter_id_arr,array_keys($paidNotSubmitEncounterArr));
	$encounter_id_arr = array_merge($encounter_id_arr,array_keys($mainPaymentEncounterArr));
	$encounter_id_str = join(',',$encounter_id_arr);
	
	$op = 'p';
	$operatorIdN = $operatorId;

	//--- GET ADJUSTMENT AMOUNT ---
	$adjQry = imw_query("select ap.id,ap.patient_id,ap.encounter_id, ap.charge_list_detail_id,ap.ins_id, ap.charge_list_id, ap.payment_by, ap.payment_method, ap.check_number, ap.cc_type, ap.cc_number, replace(ap.payment_amount,',','') as  payment_amount, ap.payment_type, patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId' FROM account_payments ap LEFT JOIN patient_charge_list ON patient_charge_list.encounter_id = ap.encounter_id where ap.encounter_id in ($encounter_id_str) and ap.del_status = 0");
	
	$adjAmtArr = array();
	$adjQryRs	= imw_query($adjQry);
	while($adjQryRes = imw_fetch_assoc($adjQryRs)){
		$encounter_id = $adjQryRes['encounter_id'];
		$payment_amount = $adjQryRes['payment_amount'];
		if($adjQryRes['payment_type'] == 'Over Adjustment'){
			$payment_amount = '-'.$payment_amount;
		}
		$adjAmtArr[$encounter_id][] = $payment_amount;
	}
	$op = 'l';

	if($processReport == 'Detail'){
		require_once(dirname(__FILE__).'/fd_DetailReport.php');		
	}else{
		require_once(dirname(__FILE__).'/fd_SummaryResult.php');
	}
	
}else{
	
	//	-----	CHECK IN/OUT PAYMENTS   ----------
	$arrCopayIds = array();
	$arrOpticalIds = array();
	$grdTotCopay  =array();
	$grdTotOptical  =array();

	$operatorIdN = $operatorId;

	// GET IDS FOR COPAY AND OPTICAL TYPES
	$qryType="Select id, item_name FROM check_in_out_fields WHERE item_name LIKE '%copay%'";
	$rsType= imw_query($qryType);
	while($resType = imw_fetch_assoc($rsType)){
		$arrCopayIds[$resType['id']] = $resType['id']; 
	}
	$qryType="Select id, item_name FROM check_in_out_fields WHERE item_name LIKE '%optical%'";
	$rsType= imw_query($qryType);
	while($resType = imw_fetch_assoc($rsType)){
		$arrOpticalIds[$resType['id']] = $resType['id'];
	}
	// -----------------------------------
	
	if($processReport == 'Detail'){
		require_once(dirname(__FILE__).'/fd_DetailReport.php');		
	}else{

	//--- GET ALL PAYMENTS HISTORY OF A PATIENT ---
		$pay_query ="Select schedule_appointments.id as 'schID', schedule_appointments.sa_facility_id, schedule_appointments.sa_doctor_id, 
		check_in_out_payment.*, DATE_FORMAT(check_in_out_payment.created_on, '".$sqlDateFormat."') as created_on, check_in_out_payment_details.*,check_in_out_payment_details.id as cioPaydetID ,
		DATE_FORMAT(check_in_out_payment.delete_date, '".$sqlDateFormat."') as deleteDate, DATE_FORMAT(check_in_out_payment_details.delete_date, '".$sqlDateFormat."') as deleteDateDet,
	 	check_in_out_payment_details.delete_time as deleteTimeDet, check_in_out_payment_details.delete_operator_id as delete_operator_id_det 
		from check_in_out_payment 
		JOIN check_in_out_payment_details on check_in_out_payment_details.payment_id = check_in_out_payment.payment_id 
		JOIN schedule_appointments on schedule_appointments.id = check_in_out_payment.sch_id 
		JOIN patient_data on patient_data.id = check_in_out_payment.patient_id 
		where schedule_appointments.sa_doctor_id IN($Physician) 
		and patient_data.lname != '' 
		and created_on between '$startDate' and '$endDate' AND check_in_out_payment.total_payment>0";
	if($strSelFac){
		$pay_query .=" and schedule_appointments.sa_facility_id IN($strSelFac)";
	}
	if($operatorIdN){
		$pay_query .=" and check_in_out_payment.created_by IN($operatorIdN)";
	}
	
	$pay_query.=" ORDER BY $sortByPat_comm schedule_appointments.sa_facility_id, schedule_appointments.sa_doctor_id, check_in_out_payment.payment_id desc";
	
	$paymentDetIdArr =array();
	$scheduleIdArr =array();
	
	$pay_query_rs = imw_query($pay_query);
	$payQryResSize = sizeof($pay_query_rs);
	while($payQryRes = imw_fetch_assoc($pay_query_rs)){

		##########################################################
		#query to get refund detail for current ci/co payments if any
		##########################################################
		$refundAmt=0;
		$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND ci_co_id = '".$payQryRes['cioPaydetID']."' AND (entered_date BETWEEN '".$startDate."' AND '".$endDate."')")or die(imw_error().'_471');
		
		while($rsRef=imw_fetch_array($qryRef)){
			$refundAmt+=$rsRef['ref_amt'];
		}
		imw_free_result($qryRef);
			
		$printFile = true;
		$facilityId=$payQryRes['sa_facility_id'];
		$doctorId=$payQryRes['sa_doctor_id'];
		$delOperId=$payQryRes['delete_operator_id_det'];
		$patientId=$payQryRes['patient_id'];
		$paymentId=$payQryRes['payment_id'];
		
		$paymentDetIdArr[$payQryRes['id']] = $payQryRes['id'];
		
		if($payQryRes['del_status']=='0' && $payQryRes['status']=='0'){	
			$facilityArr[$facilityId] = $allFacilityArr[$facilityId];
			$doctorArr[$facilityId][$doctorId] = $userNameArr[$doctorId];
			
			$paymentDetArr[$facilityId][$doctorId][$patientId][$paymentId]['payment_id'] = $paymentId;
			$paymentDetArr[$facilityId][$doctorId][$patientId][$paymentId]['payment_type'] = $payQryRes['payment_type']; 
			$paymentDetArr[$facilityId][$doctorId][$patientId][$paymentId]['total_payment'] = $payQryRes['total_payment']-$refundAmt;
			$paymentDetArr[$facilityId][$doctorId][$patientId][$paymentId]['total_payment_ref'] = $refundAmt;
			
			$patientDet[$patientId][$paymentId][] = $payQryRes;
			
			$patientAmtArr[$paymentId]['item_payment'][] = $payQryRes['item_payment']-$refundAmt;
			$patientAmtArr[$paymentId]['item_payment_ref'][] = $refundAmt;
			
			$refAmtArr[$payQryRes['cioPaydetID']]['ref_amt']= $refundAmt;
			
			$totPatientAmtArr[$paymentId]['total_payment'] = array_sum($patientAmtArr[$paymentId]['item_payment']);
			$totPatientAmtArr[$paymentId]['total_payment_ref'] = array_sum($patientAmtArr[$paymentId]['item_payment_ref']);
			
			// GRAND TOTAL ARRAY FOR COPAY AND OPTICAL
			if(in_array($payQryRes['item_id'], $arrCopayIds)){
				$grdTotCopay[] = $payQryRes['item_payment']-$refundAmt;
			}
			if(in_array($payQryRes['item_id'], $arrOpticalIds)){
				$grdTotOptical[] = $payQryRes['item_payment']-$refundAmt;
			}
		}else{
			// DELETED RECORDS
			$delFacilityArr[$facilityId] = $allFacilityArr[$facilityId];
			$delDoctorArr[$facilityId][$doctorId][$delOperId] = $userNameArr[$doctorId];
			
			$delPaymentDetArr[$facilityId][$doctorId][$delOperId][$patientId][$paymentId]['payment_id'] = $paymentId;
			$delPaymentDetArr[$facilityId][$doctorId][$delOperId][$patientId][$paymentId]['payment_type'] = $payQryRes['payment_type']; 
			$delPaymentDetArr[$facilityId][$doctorId][$delOperId][$patientId][$paymentId]['total_payment'] = $payQryRes['total_payment']-$refundAmt;
			
			$delPatientDet[$patientId][$paymentId][] = $payQryRes;
			$delPaymentDetIdArr[$payQryRes['id']] = $payQryRes['id']; 
			
			$delPatientAmtArr[$paymentId]['item_payment'][] = $payQryRes['item_payment']-$refundAmt;
			$delPatientAmtArr[$paymentId]['item_payment_ref'][] = $refundAmt;
			$delTotPatientAmtArr[$paymentId]['total_payment'] = array_sum($delPatientAmtArr[$paymentId]['item_payment']);
			$delTotPatientAmtArr[$paymentId]['total_payment_ref'] = array_sum($delPatientAmtArr[$paymentId]['item_payment_ref']);
			
			// GRAND TOTAL ARRAY FOR COPAY AND OPTICAL
			if(in_array($payQryRes['item_id'], $arrCopayIds)){
				$delGrdTotCopay[] = $payQryRes['item_payment']-$refundAmt;
			}
			if(in_array($payQryRes['item_id'], $arrOpticalIds)){
				$delGrdTotOptical[] = $payQryRes['item_payment']-$refundAmt;
			}
		}
	}
	
	
	//echo "<pre>"; print_r($paymentDetArr);
	
	// GET PROC_CODE and PAID AMT
	$appliedDetArr = array();
	$paymentDetIdStr = implode(",", $paymentDetIdArr);
	$procQry = "Select check_in_out_payment_post.check_in_out_payment_id, check_in_out_payment_post.check_in_out_payment_detail_id, check_in_out_payment_details.status,
	patient_charge_list_details.charge_list_detail_id ,patient_charge_list_details.procCode, cpt_fee_tbl.cpt4_code, patient_charges_detail_payment_info.paidForProc, patient_charges_detail_payment_info.deletePayment , patient_charges_detail_payment_info.unapply 
	FROM check_in_out_payment_post 
	LEFT JOIN check_in_out_payment_details ON check_in_out_payment_details.id = check_in_out_payment_post.check_in_out_payment_detail_id 
	LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_detail_id = check_in_out_payment_post.charge_list_detail_id 
	LEFT JOIN patient_charges_detail_payment_info ON patient_charges_detail_payment_info.payment_id = check_in_out_payment_post.acc_payment_id  
	LEFT JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode 
	WHERE check_in_out_payment_post.check_in_out_payment_detail_id IN(".$paymentDetIdStr.")";
	
	$procQryRs	= imw_query($procQry);
	while($procQryRes = imw_fetch_assoc($procQryRs)){
		$check_in_out_id = $procQryRes['check_in_out_payment_id'];
		$check_in_out_det_id = $procQryRes['check_in_out_payment_detail_id'];

		if($procQryRes['deletePayment']=='0' && $procQryRes['status']=='0' && $procQryRes['unapply']=='0'){
			$appliedDetArr[$check_in_out_id][$check_in_out_det_id]['cpt4_code'][] = $procQryRes['cpt4_code'];
			$appliedDetArr[$check_in_out_id][$check_in_out_det_id]['paidForProc'][] = $procQryRes['paidForProc'];
			
			$tempArr[$check_in_out_id][] = $procQryRes['paidForProc'];
			$totAppliedDetArr[$check_in_out_id] = array_sum($tempArr[$check_in_out_id]);
		}else{
			$delAppliedDetArr[$check_in_out_id][$check_in_out_det_id]['cpt4_code'][] = $procQryRes['cpt4_code'];
			$delAppliedDetArr[$check_in_out_id][$check_in_out_det_id]['paidForProc'][] = $procQryRes['paidForProc'];
			
			$delTempArr[$check_in_out_id][] = $procQryRes['paidForProc'];
			$delTotAppliedDetArr[$check_in_out_id] = array_sum($delTempArr[$check_in_out_id]);
		}
	}
	
	
	

//echo "<pre>"; print_r($paymentDetArr);

	//--- HEADER FOR PDF FILE ---
	$fileHeaderData = <<<DATA
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" bgcolor="#FFF3E8">
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:260px;">Front Desk Collection Report : Summary</td>
				<td class="rptbx2" style="width:260px;">Selected Group : $group_name</td>
				<td class="rptbx3" style="width:260px;">Selected Facility : $practice_name</td>
				<td class="rptbx1" style="width:260px;">Created by $op_name on $curDate</td>
			</tr>
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:260px;">Selected Physician : $physican_name</td>
				<td class="rptbx2" style="width:260px;">Selected Operator : $operator_name</td>
				<td class="rptbx3" style="width:260px;">Selected Facility : $practice_name</td>
				<td class="rptbx1" style="width:260px;">elected Date : $Start_date To $End_date</td>
			</tr>
	</table>
DATA;

$fileHeaderData2 = <<<DATA
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="700" bgcolor="#FFF3E8">
			<tr>	
				<td align="left" class="rptbx1" width="200" style="font-size: 11px;">FD Collection Report: Summary</td>
				<td align="left" class="rptbx2" width="300" colspan="2" style="font-size: 11px;">Selected Group : $group_name</td>	
				<td align="left" class="rptbx3" width="200" style="font-size: 11px;">Created by $op_name on $curDate </td>
			</tr>
			<tr>
				<td align="left" class="rptbx1" style="font-size: 11px;">Sel Facility : $practice_name</td>
				<td align="left" class="rptbx2" style="font-size: 11px;">Sel Physician : $physician_name</td>
				<td align="left" class="rptbx2" style="font-size: 11px;">Sel Operator : $operator_name</td>
				<td align="left" class="rptbx3" style="font-size: 11px;">Sel Date : $Start_date To $End_date</td>
			</tr>
	</table>
DATA;
	$csvFileData = $fileHeaderData;
	
	$grdTotCash =array();
	$grdTotCheck =array();
	$grdTotCC =array();
	$grdTotMo =array();
	$grdTotEft =array();
	
	foreach($facilityArr as $facId => $facName){
		if(sizeof($paymentDetArr[$facId])<=0){ break; }
		$facPayAmt = array();
		$facAppAmt = array();
		$facBalAmt = array();
		
		$csvFileData.= <<<DATA
		
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
			<tr>	
				<td align="left" class="text_b_w" colspan="6">Facility : $facName</td>
			</tr>
			<tr class="text_b_w">	
				<td align="center" style="width:250px">Physician Name</td>
				<td align="center" style="width:150px">Payment Type</td>
				<td align="center" style="width:150px">Payment</td>
				<td align="center" style="width:150px">Applied</td>
				<td align="center" style="width:150px">Balance</td>
				<td align="center" style="width:auto"></td>
			</tr>
DATA;

		$pdfData2.= <<<DATA
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
			<tr>	
				<td align="left" class="text_b_w" colspan="6">Facility : $facName</td>
			</tr>
			<tr class="text_b_w">	
				<td align="center" class="text_b_w" style="width:140px">Physician Name</td>
				<td align="center" class="text_b_w" style="width:140px">Payment Type</td>
				<td align="center" class="text_b_w" style="width:140px">Payment</td>
				<td align="center" class="text_b_w" style="width:140px">Applied</td>
				<td align="center" class="text_b_w" style="width:140px">Balance</td>
			</tr>
DATA;

		foreach($doctorArr[$facId] as $docId => $docName){

			if(sizeof($paymentDetArr[$facId][$docId])<=0){ break; }

			$docPayType = array();
			$docItemArr = array();
			$docItemArr = array();
			$docItemArr = array();
			$docItemArr = array();
			$docTotPayAmt = array();
			$docTotAppAmt = array();
			$docTotBalAmt = array();
					
			$csvFileData.= <<<DATA
				<tr style="height:20px;">	
					<td align="left" style="width:250px" class="text11b" bgcolor="#FFFFFF">$docName</td>
					<td align="left" style="width:150px" class="text11b" bgcolor="#FFFFFF"></td>
					<td align="right" style="width:150px" class="text11b" bgcolor="#FFFFFF"></td>
					<td align="right" style="width:150px" class="text11b" bgcolor="#FFFFFF"></td>
					<td align="right" style="width:150px" class="text11b" bgcolor="#FFFFFF"></td>
					<td align="right" style="width:auto" class="text11b" bgcolor="#FFFFFF"></td>
				</tr>
DATA;

			$pdfData2.= <<<DATA
				<tr style="height:20px;">	
					<td align="left" class="text_12" bgcolor="#FFFFFF">$docName</td>
					<td align="left" class="text_12" bgcolor="#FFFFFF"></td>
					<td align="right" class="text_12" bgcolor="#FFFFFF"></td>
					<td align="right" class="text_12" bgcolor="#FFFFFF"></td>
					<td align="right" class="text_12" bgcolor="#FFFFFF"></td>
				</tr>
DATA;
			
//			echo "<pre>"; print_r($paymentDetArr[$facId][$docId]);
			
			foreach($paymentDetArr[$facId][$docId] as $patID => $patDetails){

				foreach($patDetails as $paymentID => $patDet){

					foreach($patientDet[$patID][$paymentID] as $pId => $payDetail){
						
						
						$paymentType = strtoupper($payDetail['payment_type']);
						$paymentMethod = strtoupper($payDetail['payment_method']);
						$payId = $payDetail['payment_id'];
						
						//$refundAmt= [$payDetail['cioPaydetID']]['ref_amt'];
						$refundAmt=$refAmtArr[$payDetail['cioPaydetID']]['ref_amt'];
						
						$paymentDetId = $payDetail['id'];
						$payAmt = $payDetail['item_payment']-$refundAmt;
						
						if($paymentMethod =='CASH'){
							$grdTotCash[] =  $payAmt; 						
						}
						if($paymentMethod =='CHECK'){
							$grdTotCheck[] =  $payAmt; 						
						}
						if($paymentMethod =='CREDIT CARD'){
							$grdTotCC[] =  $payAmt; 						
						}
						if($paymentMethod =='MONEY ORDER'){
							$grdTotMo[] =  $payAmt; 						
						}
						if($paymentMethod =='EFT'){
							$grdTotEft[] =  $payAmt; 						
						}
	
						$totItemApply = array_sum($appliedDetArr[$payId][$paymentDetId]['paidForProc']);
						
						if($totItemApply < ($payDetail['item_payment']-$refundAmt)){
							$totBal =($payDetail['item_payment']-$refundAmt) - $totItemApply;
						}else{
							$totBal =0;
						}
						
						$docPayType[$paymentType] = $paymentType;
						$docItemArr[$paymentType][$payDetail['item_id']]['item_name'] = $allINOutFields[$payDetail['item_id']];
						$docItemArr[$paymentType][$payDetail['item_id']]['payAmt']+= $payAmt;
						$docItemArr[$paymentType][$payDetail['item_id']]['payAmtRef']+= $refundAmt;
						$docItemArr[$paymentType][$payDetail['item_id']]['applied']+= $totItemApply;
						$docItemArr[$paymentType][$payDetail['item_id']]['balance']+= $totBal;
					}
				}
			}

			// SHOW DOCTOR TOTALS
			foreach($docPayType as $payType){
				$csvFileData.= <<<DATA
					<tr>	
						<td align="right" class="text11b" bgcolor="#FFFFFF">$payType</td>
						<td align="left" colspan="5" class="text" bgcolor="#FFFFFF"></td>
					</tr>
DATA;
				$pdfData2.= <<<DATA
					<tr>	
						<td align="right" class="text_12" bgcolor="#FFFFFF">$payType</td>
						<td align="left" colspan="4" class="text" bgcolor="#FFFFFF"></td>
					</tr>
DATA;
				foreach($docItemArr[$payType] as $itemId => $itemDet){
					$itemName = $itemDet['item_name'];
					$itemPay = $itemDet['payAmt'];
					$itemPayRef = $itemDet['payAmtRef'];
					$itemApplied = $itemDet['applied'];
					$itemBalance = $itemDet['balance'];
					
					if($itemPayRef>0)
					$redRow='style="color:#FF0000" title="Refund '.$CLSReports->numberFormat($itemPayRef,2).'"';
					else 
					$redRow='';
						
					// DISPLAY SUB TOTAL FOR DOCTOR
					$docTotPayAmt[$docId][] = $itemPay;
					$docTotAppAmt[$docId][] = $itemApplied;
					$docTotBalAmt[$docId][] = $itemBalance;
					
					$itemPay = $CLSReports->numberFormat($itemPay,2);
					$itemPay =($itemPay=='')? $showCurrencySymbol."00.00" : $itemPay;
					$itemApplied = $CLSReports->numberFormat($itemApplied,2);
					$itemApplied =($itemApplied=='')? $showCurrencySymbol."00.00" : $itemApplied;
					$itemBalance = $CLSReports->numberFormat($itemBalance,2);				
					$itemBalance =($itemBalance=='')? $showCurrencySymbol."00.00" : $itemBalance;
					
					$csvFileData.= <<<DATA
						<tr>	
							<td align="left" class="text" bgcolor="#FFFFFF"></td>
							<td align="left" class="text" bgcolor="#FFFFFF">$itemName</td>
							<td align="right" class="text" bgcolor="#FFFFFF" $redRow>$itemPay</td>
							<td align="right" class="text" bgcolor="#FFFFFF">$itemApplied</td>
							<td align="right" class="text" bgcolor="#FFFFFF">$itemBalance</td>
							<td align="left" class="text" bgcolor="#FFFFFF"></td>
						</tr>
DATA;

					$pdfData2.= <<<DATA
						<tr>	
							<td align="left" class="text_12" bgcolor="#FFFFFF"></td>
							<td align="left" class="text_12" bgcolor="#FFFFFF">$itemName</td>
							<td align="right" class="text_12" bgcolor="#FFFFFF" $redRow>$itemPay</td>
							<td align="right" class="text_12" bgcolor="#FFFFFF">$itemApplied</td>
							<td align="right" class="text_12" bgcolor="#FFFFFF">$itemBalance</td>
						</tr>
DATA;

				}
			}

			$docTotPay = array_sum($docTotPayAmt[$docId]);
			$docTotApp = array_sum($docTotAppAmt[$docId]);
			$docTotBal = array_sum($docTotBalAmt[$docId]);
			// DISPLAY SUB TOTAL FOR FACILITY
			$facPayAmt[$facId][] = $docTotPay;
			$facAppAmt[$facId][] = $docTotApp;
			$facBalAmt[$facId][] = $docTotBal;
			
			$docTotPay = $CLSReports->numberFormat($docTotPay,2);
			$docTotPay =($docTotPay=='')? $showCurrencySymbol."00.00" : $docTotPay;
			$docTotApp = $CLSReports->numberFormat($docTotApp,2);
			$docTotApp =($docTotApp=='')? $showCurrencySymbol."00.00" : $docTotApp;
			$docTotBal = $CLSReports->numberFormat($docTotBal,2);				
			$docTotBal =($docTotBal=='')? $showCurrencySymbol."00.00" : $docTotBal;
			
			$csvFileData.= <<<DATA
				<tr><td class="total-row" colspan="6"></td></tr>
				<tr style="height:20px">
					<td align="right" class="text11b" bgcolor="#FFFFFF"></td>	
					<td align="right" class="text11b" bgcolor="#FFFFFF">Sub Total&nbsp;</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotPay</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotApp</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotBal</td>
					<td align="left" class="text11b" bgcolor="#FFFFFF"></td>
				</tr>
				<tr><td class="total-row" colspan="6"></td></tr>
DATA;
			$pdfData2.= <<<DATA
				<tr><td class="total-row" colspan="6"></td></tr>
				<tr>	
					<td align="right" class="text_b" bgcolor="#FFFFFF"></td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">Sub Total&nbsp;</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotPay</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotApp</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotBal</td>
				</tr>
				<tr><td class="total-row" colspan="6"></td></tr>
DATA;

		}

		$facPay = array_sum($facPayAmt[$facId]);
		$facApp = array_sum($facAppAmt[$facId]);
		$facBal = array_sum($facBalAmt[$facId]);

		// GRAND TOTALS ARRAY
		$grdTotPayAmtArr[] = $facPay;
		$grdTotAppAmtArr[] = $facApp;
		$grdTotBalAmtArr[] = $facBal;

		$facPay = $CLSReports->numberFormat($facPay,2);
		$facPay =($facPay=='')? $showCurrencySymbol."00.00" : $facPay;
		$facApp = $CLSReports->numberFormat($facApp,2);
		$facApp =($facApp=='')? $showCurrencySymbol."00.00" : $facApp;
		$facBal = $CLSReports->numberFormat($facBal,2);	
		$facBal =($facBal=='')? $showCurrencySymbol."00.00" : $facBal;			

		$csvFileData.= <<<DATA
			<tr style="height:20px">	
				<td align="right" class="text11b" bgcolor="#FFFFFF"></td>
				<td align="right" class="text11b" bgcolor="#FFFFFF">Total&nbsp;</td>
				<td align="right" class="text11b" bgcolor="#FFFFFF">$facPay</td>
				<td align="right" class="text11b" bgcolor="#FFFFFF">$facApp</td>
				<td align="right" class="text11b" bgcolor="#FFFFFF">$facBal</td>
				<td align="left" class="text11b" bgcolor="#FFFFFF"></td>
			</tr>
			<tr><td class="total-row" colspan="6"></td></tr>
		</table>	
DATA;
		$pdfData2.= <<<DATA
			<tr style="height:20px">	
				<td align="right" class="text_b" bgcolor="#FFFFFF"></td>
				<td align="right" class="text_b" bgcolor="#FFFFFF">Total&nbsp;</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF">$facPay</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF">$facApp</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF">$facBal</td>
			</tr>
			<tr><td class="total-row" colspan="6"></td></tr>
		</table>
DATA;
		

	}
	
	
	// GRAND TOTALS
	if(sizeof($paymentDetArr)>0){
	$grdTotPayAmt = array_sum($grdTotPayAmtArr);
	$grdAmt = $grdTotPayAmt;
	$grdTotPayAmt = $CLSReports->numberFormat($grdTotPayAmt,2);
	$grdTotPayAmt =($grdTotPayAmt=='')? $showCurrencySymbol."00.00" : $grdTotPayAmt;		
	$grdTotAppAmt = $CLSReports->numberFormat(array_sum($grdTotAppAmtArr),2);
	$grdTotAppAmt =($grdTotAppAmt=='')? $showCurrencySymbol."00.00" : $grdTotAppAmt;		
	$grdTotBalAmt = $CLSReports->numberFormat(array_sum($grdTotBalAmtArr),2);
	$grdTotBalAmt =($grdTotBalAmt=='')? $showCurrencySymbol."00.00" : $grdTotBalAmt;		

	$grdCash = array_sum($grdTotCash);	
	$grdTotCashAmt = $CLSReports->numberFormat($grdCash,2);
	$grdTotCashAmt =($grdTotCashAmt=='')? $showCurrencySymbol."00.00" : $grdTotCashAmt;		
	$grdCheck = array_sum($grdTotCheck);	
	$grdTotCheckAmt = $CLSReports->numberFormat($grdCheck,2);
	$grdTotCheckAmt =($grdTotCheckAmt=='')? $showCurrencySymbol."00.00" : $grdTotCheckAmt;	
	$grdCC = array_sum($grdTotCC);	
	$grdTotCCAmt = $CLSReports->numberFormat($grdCC,2);
	$grdTotCCAmt =($grdTotCCAmt=='')? $showCurrencySymbol."00.00" : $grdTotCCAmt;		
	
	$grdMo = array_sum($grdTotMo);	
	$grdTotMoAmt = $CLSReports->numberFormat($grdMo,2);
	$grdTotMoAmt =($grdTotMoAmt=='')? $showCurrencySymbol."00.00" : $grdTotMoAmt;		
	
	$grdEft = array_sum($grdTotEft);	
	$grdTotEftAmt = $CLSReports->numberFormat($grdEft,2);
	$grdTotEftAmt =($grdTotEftAmt=='')? $showCurrencySymbol."00.00" : $grdTotEftAmt;		
	
	$grdTotAmtType = $grdCash + $grdCheck + $grdCC + $grdMo + $grdEft; 
	$grdTotAmtType = $CLSReports->numberFormat($grdTotAmtType,2);
	$grdTotAmtType =($grdTotAmtType=='')? $showCurrencySymbol."00.00" : $grdTotAmtType;		

		
	$acctAmt = 0;
	$grdTotCopayAmt = array_sum($grdTotCopay);
	$acctAmt = $grdTotCopayAmt;
	$grdTotCopayAmt = $CLSReports->numberFormat($grdTotCopayAmt,2);
	$grdTotCopayAmt =($grdTotCopayAmt=='')? $showCurrencySymbol."00.00" : $grdTotCopayAmt;		

	$grdTotOpticalAmt = array_sum($grdTotOptical);
	$acctAmt+= $grdTotOpticalAmt;
	$grdTotOpticalAmt = $CLSReports->numberFormat($grdTotOpticalAmt,2);
	$grdTotOpticalAmt =($grdTotOpticalAmt=='')? $showCurrencySymbol."00.00" : $grdTotOpticalAmt;		
	
	$grdProc = $grdAmt - $acctAmt;
	$grdTotProcAmt = $CLSReports->numberFormat($grdProc,2);
	$grdTotProcAmt =($grdTotProcAmt=='')? $showCurrencySymbol."00.00" : $grdTotProcAmt;		
	
	$grdMethod = $acctAmt + $grdProc;
	$grdTotAmtMethod = $CLSReports->numberFormat($grdMethod,2);
	$grdTotAmtMethod =($grdTotAmtMethod=='')? $showCurrencySymbol."00.00" : $grdTotAmtMethod;		
	
	$csvFileData.= <<<DATA
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding">
		<tr><td class="total-row" colspan="6"></td></tr>
		<tr style="height:20px">	
			<td align="right" style="width:150px"; class="text11b"></td>
			<td align="right" style="width:250px"; class="text11b">Total&nbsp;</td>
			<td align="right" style="width:150px"; class="text11b">$grdTotPayAmt</td>
			<td align="right" style="width:150px"; class="text11b">$grdTotAppAmt</td>
			<td align="right" style="width:150px"; class="text11b">$grdTotBalAmt</td>
			<td align="left" style="width:auto"; class="text11b"></td>
		</tr>
		<tr><td class="total-row" colspan="6"></td></tr>
		
		<tr style="height:30px"><td colspan="6" bgcolor="#FFFFFF"></td></tr>
		<tr>	
			<td align="right" class="text11b" bgcolor="#FFFFFF" ></td>
			<td align="right" class="text11b" bgcolor="#FFFFFF" >Cash</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF" >$grdTotCashAmt</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF">Copay</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF">$grdTotCopayAmt</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF"></td>
		</tr>
		<tr>	
			<td align="right" class="text11b" bgcolor="#FFFFFF" ></td>
			<td align="right" class="text11b" bgcolor="#FFFFFF" >Check</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF" >$grdTotCheckAmt</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF">Optical</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF">$grdTotOpticalAmt</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF"></td>
		</tr>
		<tr>
			<td align="right" class="text11b" bgcolor="#FFFFFF" ></td>	
			<td align="right" class="text11b" bgcolor="#FFFFFF" >Credit Card</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF" >$grdTotCCAmt</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF">Procedures</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF">$grdTotProcAmt</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF"></td>
		</tr>
		<tr>
			<td align="right" class="text11b" bgcolor="#FFFFFF" ></td>	
			<td align="right" class="text11b" bgcolor="#FFFFFF" >Money Order</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF" >$grdTotMoAmt</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF"></td>
			<td align="right" class="text11b" bgcolor="#FFFFFF"></td>
			<td align="right" class="text11b" bgcolor="#FFFFFF"></td>
		</tr>
		<tr>
			<td align="right" class="text11b" bgcolor="#FFFFFF" ></td>	
			<td align="right" class="text11b" bgcolor="#FFFFFF" >EFT</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF" >$grdTotEftAmt</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF"></td>
			<td align="right" class="text11b" bgcolor="#FFFFFF"></td>
			<td align="right" class="text11b" bgcolor="#FFFFFF"></td>
		</tr>
		<tr><td class="total-row" colspan="6"></td></tr>
		<tr style="height:20px">	
			<td align="right" style="width:150px"; class="text11b" bgcolor="#FFFFFF"></td>
			<td align="right" style="width:250px"; class="text11b" bgcolor="#FFFFFF">Total&nbsp;</td>
			<td align="right" style="width:150px"; class="text11b" bgcolor="#FFFFFF">$grdTotAmtType</td>
			<td align="right" style="width:150px"; class="text11b" bgcolor="#FFFFFF"></td>
			<td align="right" style="width:150px"; class="text11b" bgcolor="#FFFFFF">$grdTotAmtMethod</td>
			<td align="left" style="width:auto"; class="text11b" bgcolor="#FFFFFF"></td>
		</tr>
		<tr><td class="total-row" colspan="6"></td></tr>
		
	</table>	
DATA;
	$pdfData2.= <<<DATA
		<table width="700" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
		<tr><td class="total-row" colspan="5"></td></tr>
		<tr style="height:20px" >	
			<td align="right" style="width:140px"; class="text_b" bgcolor="#FFFFFF"></td>
			<td align="right" style="width:140px"; class="text_b" bgcolor="#FFFFFF">Total&nbsp;</td>
			<td align="right" style="width:140px"; class="text_b" bgcolor="#FFFFFF">$grdTotPayAmt</td>
			<td align="right" style="width:140px"; class="text_b" bgcolor="#FFFFFF">$grdTotAppAmt</td>
			<td align="right" style="width:140px"; class="text_b" bgcolor="#FFFFFF">$grdTotBalAmt</td>
		</tr>
		<tr><td class="total-row" colspan="5"></td></tr>

		<tr ><td colspan="5" bgcolor="#FFFFFF" style="height:20px"></td></tr>
		<tr>	
			<td align="right" class="text_b" ></td>
			<td align="right" class="text_b" >Cash</td>
			<td align="right" class="text_b" >$grdTotCashAmt</td>
			<td align="right" class="text_b" >Copay</td>
			<td align="right" class="text_b" >$grdTotCopayAmt</td>
		</tr>
		<tr>	
			<td align="right" class="text_b" ></td>
			<td align="right" class="text_b" >Check</td>
			<td align="right" class="text_b" >$grdTotCheckAmt</td>
			<td align="right" class="text_b" >Optical</td>
			<td align="right" class="text_b" >$grdTotOpticalAmt</td>
		</tr>
		<tr>
			<td align="right" class="text_b" ></td>	
			<td align="right" class="text_b" >Credit Card</td>
			<td align="right" class="text_b" >$grdTotCCAmt</td>
			<td align="right" class="text_b">Procedures</td>
			<td align="right" class="text_b">$grdTotProcAmt</td>
		</tr>
		<tr>	
			<td align="right" class="text_b" ></td>
			<td align="right" class="text_b" >Money Order</td>
			<td align="right" class="text_b" >$grdTotMoAmt</td>
			<td align="right" class="text_b"></td>
			<td align="right" class="text_b"></td>
		</tr>
		<tr>
			<td align="right" class="text_b" ></td>	
			<td align="right" class="text_b" >EFT</td>
			<td align="right" class="text_b" >$grdTotEftAmt</td>
			<td align="right" class="text_b"></td>
			<td align="right" class="text_b"></td>
		</tr>
		<tr><td class="total-row" colspan="5"></td></tr>
		<tr style="height:20px" >	
			<td align="right" style="width:140px"; class="text_b" bgcolor="#FFFFFF"></td>
			<td align="right" style="width:140px"; class="text_b" bgcolor="#FFFFFF">Total&nbsp;</td>
			<td align="right" style="width:140px"; class="text_b" bgcolor="#FFFFFF">$grdTotAmtType</td>
			<td align="right" style="width:140px"; class="text_b" bgcolor="#FFFFFF"></td>
			<td align="right" style="width:140px"; class="text_b" bgcolor="#FFFFFF">$grdTotAmtMethod</td>
		</tr>
		<tr><td class="total-row" colspan="5"></td></tr>
	</table>	
DATA;


		$pdfData.= <<<DATA
			<page backtop="9mm" backbottom="10mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
			$fileHeaderData2
			</page_header>
			$pdfData2
			</page>
DATA;
	}

// DELETED CI/CO RECORDS
	//--- HEADER FOR PDF FILE ---
if(sizeof($delFacilityArr)>0){	
$fileHeaderData = $delcsvFileData = $delpdfData2 ='';
$subTotPayAmtArr = array();
$subTotBalAmtArr = array();
$subTotAppAmtArr = array();
$grdTotPayAmtArr = array();
$grdTotAppAmtArr = array();
$grdTotBalAmtArr = array();
	
	$fileHeaderData = <<<DATA
		<table width="100%"class="rpt rpt_table rpt_table-bordered rpt_padding">
			<tr>	
				<td style="text-align:center;" class="text_b_w" colspan="4">Deleted Records</td>
			</tr>
			<tr>	
				<td align="left" class="rptbx1" style="width:229px;">Front Desk Collection Report : Summary</td>
				<td align="left" class="rptbx2" style="width:290px;">Selected Group : $group_name</td>	
				<td align="left" class="rptbx2" style="width:270px;">Selected Facility : $practice_name</td>
				<td align="left" class="rptbx3" style="width:222px;">Created by $op_name on $curDate </td>
			</tr>
			<tr>	
				<td align="left" class="rptbx1">Sel Physician : $physican_name</td>
				<td align="left" class="rptbx2" colspan="2">Sel Operator : $operator_name</td>
				<td align="left" class="rptbx3">Sel Date : $Start_date To $End_date</td>
			</tr>
	</table>
DATA;
$fileHeaderData2 = <<<DATA
		<table width="700" class="rpt rpt_table rpt_table-bordered rpt_padding">
			<tr>	
				<td align="left" class="rptbx1" style="width:200px;">FD Collection Report : Summary</td>
				<td align="left" class="rptbx2" style="width:300px;" colspan="2">Selected Group : $group_name</td>	
				<td align="left" class="rptbx3" style="width:200px;">Created by $op_name on $curDate </td>
			</tr>
			<tr>
				<td align="left" class="rptbx1">Sel Facility : $practice_name</td>
				<td align="left" class="rptbx2">Sel Physician : $physician_name</td>
				<td align="left" class="rptbx2">Sel Operator : $operator_name</td>
				<td align="left" class="rptbx3">Sel Date : $Start_date To $End_date</td>
			</tr>
	</table>
DATA;
	$grdTotCash =array();
	$grdTotCheck =array();
	$grdTotCC =array();
	$grdTotMo =array();
	$grdTotEft =array();
	
	foreach($delFacilityArr as $facId => $facName){
		
		$delDocDetArr = array_keys($delDoctorArr[$facId]);

		if(sizeof($delPaymentDetArr[$facId])<=0){ break; }
		$facPayAmt = array();
		$facAppAmt = array();
		$facBalAmt = array();
		
		$delcsvFileData.= <<<DATA
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
			<tr>	
				<td align="left" class="text_b_w" colspan="7">Facility : $facName</td>
			</tr>
			<tr class="text_b_w">
				<td align="center" style="width:250px">Physician Name</td>
				<td align="center" style="width:150px">Payment Type</td>
				<td align="center" style="width:150px">Payment</td>
				<td align="center" style="width:150px">Applied</td>
				<td align="center" style="width:150px">Balance</td>
				<td align="center" style="width:150px">Deleted By</td>
				<td align="center" style="width:auto"></td>
			</tr>
DATA;

		$delpdfData2.= <<<DATA
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
			<tr>	
				<td align="left" class="text_b_w" colspan="7">Facility : $facName</td>
			</tr>
			<tr class="text_b_w">	
				<td align="center" class="text_b_w" style="width:120px">Physician Name</td>
				<td align="center" class="text_b_w" style="width:120px">Payment Type</td>
				<td align="center" class="text_b_w" style="width:110px">Payment</td>
				<td align="center" class="text_b_w" style="width:110px">Applied</td>
				<td align="center" class="text_b_w" style="width:120px">Balance</td>
				<td align="center" class="text_b_w" style="width:120px">Deleted By</td>
			</tr>
DATA;

		foreach($delDocDetArr as $docId){
			$docName = $userNameArr[$docId];			
			
			if(sizeof($delPaymentDetArr[$facId][$docId])<=0){ break; }

			$docPayType = array();
			$docDelOprArr = array();
			$docItemArr = array();
			$docItemArr = array();
			$docItemArr = array();
			$docItemArr = array();
			$docTotPayAmt = array();
			$docTotAppAmt = array();
			$docTotBalAmt = array();
					
			$delcsvFileData.= <<<DATA
				<tr style="height:20px;">	
					<td align="left" class="text11b" bgcolor="#FFFFFF">$docName</td>
					<td align="left" class="text11b" bgcolor="#FFFFFF" colspan="6"></td>
				</tr>
DATA;

			$delpdfData2.= <<<DATA
				<tr style="height:20px;">	
					<td align="left" class="text_12" bgcolor="#FFFFFF">$docName</td>
					<td align="left" class="text_12" bgcolor="#FFFFFF"></td>
					<td align="right" class="text_12" bgcolor="#FFFFFF"></td>
					<td align="right" class="text_12" bgcolor="#FFFFFF"></td>
					<td align="right" class="text_12" bgcolor="#FFFFFF"></td>
					<td align="right" class="text_12" bgcolor="#FFFFFF"></td>
				</tr>
DATA;
			
			foreach($delPaymentDetArr[$facId][$docId] as $delOprId => $deldet){
				$deletedBy = $userNameArr[$delOprId];

				//echo '<pre>'; print_r($delPaymentDetArr[$facId][$docId]);
				
				foreach($delPaymentDetArr[$facId][$docId][$delOprId] as $patID => $patDetails){

					foreach($patDetails as $paymentID => $patDet){
	
						foreach($delPatientDet[$patID][$paymentID] as $pId => $payDetail){
							
							$paymentType = strtoupper($payDetail['payment_type']);
							$paymentMethod = strtoupper($payDetail['payment_method']);
							$payId = $payDetail['payment_id'];
							$paymentDetId = $payDetail['id'];
							$payAmt = $payDetail['item_payment'];
		
							$totItemApply = array_sum($delAppliedDetArr[$payId][$paymentDetId]['paidForProc']);
							if($totItemApply < $payDetail['item_payment']){
								$totBal =$payDetail['item_payment'] - $totItemApply;
							}else{
								$totBal =0;
							}

							$docPayType[$paymentType] = $paymentType;
							$docDelOprArr[$paymentType][$delOprId] = $deletedBy;
							$docItemArr[$paymentType][$delOprId][$payDetail['item_id']]['item_name'] = $allINOutFields[$payDetail['item_id']];
							$docItemArr[$paymentType][$delOprId][$payDetail['item_id']]['payAmt']+= $payAmt;
							$docItemArr[$paymentType][$delOprId][$payDetail['item_id']]['applied']+= $totItemApply;
							$docItemArr[$paymentType][$delOprId][$payDetail['item_id']]['balance']+= $totBal;
						}
					}
				}
			}

			// SHOW DOCTOR TOTALS
			foreach($docPayType as $payType){
				$delcsvFileData.= <<<DATA
					<tr>	
						<td align="right" class="text11b" bgcolor="#FFFFFF">$payType</td>
						<td align="left" colspan="6" class="text" bgcolor="#FFFFFF"></td>
					</tr>
DATA;
				$delpdfData2.= <<<DATA
					<tr>	
						<td align="right" class="text_12" bgcolor="#FFFFFF">$payType</td>
						<td align="left" colspan="5" class="text" bgcolor="#FFFFFF"></td>
					</tr>
DATA;
				foreach($docDelOprArr[$payType] as $del_opr_id => $del_opr_name){

					foreach($docItemArr[$payType][$del_opr_id] as $itemId => $itemDet){
						$itemName = $itemDet['item_name'];
						$itemPay = $itemDet['payAmt'];
						$itemApplied = $itemDet['applied'];
						$itemBalance = $itemDet['balance'];
						
						// DISPLAY SUB TOTAL FOR DOCTOR
						$docTotPayAmt[$docId][] = $itemPay;
						$docTotAppAmt[$docId][] = $itemApplied;
						$docTotBalAmt[$docId][] = $itemBalance;
						
						$itemPay = $CLSReports->numberFormat($itemPay,2);
						$itemPay =($itemPay=='')? $showCurrencySymbol."00.00" : $itemPay;
						$itemApplied = $CLSReports->numberFormat($itemApplied,2);
						$itemApplied =($itemApplied=='')? $showCurrencySymbol."00.00" : $itemApplied;
						$itemBalance = $CLSReports->numberFormat($itemBalance,2);				
						$itemBalance =($itemBalance=='')? $showCurrencySymbol."00.00" : $itemBalance;
						
						$delcsvFileData.= <<<DATA
							<tr>	
								<td align="left" class="text" bgcolor="#FFFFFF"></td>
								<td align="left" class="text" bgcolor="#FFFFFF">$itemName</td>
								<td align="right" class="text" bgcolor="#FFFFFF">$itemPay</td>
								<td align="right" class="text" bgcolor="#FFFFFF">$itemApplied</td>
								<td align="right" class="text" bgcolor="#FFFFFF">$itemBalance</td>
								<td style="text-align:left" class="text" bgcolor="#FFFFFF">&nbsp;$del_opr_name</td>
								<td align="left" class="text" bgcolor="#FFFFFF"></td>
							</tr>
DATA;
	
						$delpdfData2.= <<<DATA
							<tr>	
								<td align="left" class="text_b" bgcolor="#FFFFFF"></td>
								<td align="left" class="text_b" bgcolor="#FFFFFF">$itemName</td>
								<td align="right" class="text_b" bgcolor="#FFFFFF">$itemPay</td>
								<td align="right" class="text_b" bgcolor="#FFFFFF">$itemApplied</td>
								<td align="right" class="text_b" bgcolor="#FFFFFF">$itemBalance</td>
								<td style="text-align:left" class="text_b" bgcolor="#FFFFFF">&nbsp;$del_opr_name</td>
							</tr>
DATA;
					}
				}
			}

			$docTotPay = array_sum($docTotPayAmt[$docId]);
			$docTotApp = array_sum($docTotAppAmt[$docId]);
			$docTotBal = array_sum($docTotBalAmt[$docId]);
			// DISPLAY SUB TOTAL FOR FACILITY
			$facPayAmt[$facId][] = $docTotPay;
			$facAppAmt[$facId][] = $docTotApp;
			$facBalAmt[$facId][] = $docTotBal;
			
			$docTotPay = $CLSReports->numberFormat($docTotPay,2);
			$docTotPay =($docTotPay=='')? $showCurrencySymbol."00.00" : $docTotPay;
			$docTotApp = $CLSReports->numberFormat($docTotApp,2);
			$docTotApp =($docTotApp=='')? $showCurrencySymbol."00.00" : $docTotApp;
			$docTotBal = $CLSReports->numberFormat($docTotBal,2);				
			$docTotBal =($docTotBal=='')? $showCurrencySymbol."00.00" : $docTotBal;
			
			$delcsvFileData.= <<<DATA
				<tr><td class="total-row" colspan="7"></td></tr>
				<tr style="height:20px">
					<td align="right" class="text11b" bgcolor="#FFFFFF"></td>	
					<td align="right" class="text11b" bgcolor="#FFFFFF">Sub Total&nbsp;</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotPay</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotApp</td>
					<td align="right" class="text11b" bgcolor="#FFFFFF">$docTotBal</td>
					<td align="left" class="text11b" bgcolor="#FFFFFF" colspan="2"></td>
				</tr>
				<tr><td class="total-row" colspan="7"></td></tr>
DATA;
			$delpdfData2.= <<<DATA
				<tr><td class="total-row" colspan="7"></td></tr>
				<tr style="height:20px">	
					<td align="right" class="text_b" bgcolor="#FFFFFF"></td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">Sub Total&nbsp;</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotPay</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotApp</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF">$docTotBal</td>
					<td align="right" class="text_b" bgcolor="#FFFFFF"></td>
				</tr>
				<tr><td class="total-row" colspan="7"></td></tr>
DATA;

		}

		$facPay = array_sum($facPayAmt[$facId]);
		$facApp = array_sum($facAppAmt[$facId]);
		$facBal = array_sum($facBalAmt[$facId]);

		// GRAND TOTALS ARRAY
		$grdTotPayAmtArr[] = $facPay;
		$grdTotAppAmtArr[] = $facApp;
		$grdTotBalAmtArr[] = $facBal;

		$facPay = $CLSReports->numberFormat($facPay,2);
		$facPay =($facPay=='')? $showCurrencySymbol."00.00" : $facPay;
		$facApp = $CLSReports->numberFormat($facApp,2);
		$facApp =($facApp=='')? $showCurrencySymbol."00.00" : $facApp;
		$facBal = $CLSReports->numberFormat($facBal,2);	
		$facBal =($facBal=='')? $showCurrencySymbol."00.00" : $facBal;			

		$delcsvFileData.= <<<DATA
			<tr style="height:20px">
				<td align="right" class="text11b" bgcolor="#FFFFFF"></td>	
				<td align="right" class="text11b" bgcolor="#FFFFFF">Total&nbsp;</td>
				<td align="right" class="text11b" bgcolor="#FFFFFF">$facPay</td>
				<td align="right" class="text11b" bgcolor="#FFFFFF">$facApp</td>
				<td align="right" class="text11b" bgcolor="#FFFFFF">$facBal</td>
				<td align="left" class="text11b" bgcolor="#FFFFFF" colspan="2"></td>
			</tr>
			<tr><td class="total-row" colspan="7"></td></tr>
			</table>
DATA;
		$delpdfData2.= <<<DATA
			<tr style="height:20px">	
				<td align="right" class="text_b" bgcolor="#FFFFFF"></td>
				<td align="right" class="text_b" bgcolor="#FFFFFF">Total&nbsp;</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF">$facPay</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF">$facApp</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF">$facBal</td>
				<td align="right" class="text_b" bgcolor="#FFFFFF"></td>
			</tr>
			<tr><td class="total-row" colspan="6"></td></tr>
			</table>
DATA;
		

	}
	
	// GRAND TOTALS
	$grdTotPayAmt = array_sum($grdTotPayAmtArr);
	$grdAmt = $grdTotPayAmt;
	$grdTotPayAmt = $CLSReports->numberFormat($grdTotPayAmt,2);
	$grdTotPayAmt =($grdTotPayAmt=='')? $showCurrencySymbol."00.00" : $grdTotPayAmt;		
	$grdTotAppAmt = $CLSReports->numberFormat(array_sum($grdTotAppAmtArr),2);
	$grdTotAppAmt =($grdTotAppAmt=='')? $showCurrencySymbol."00.00" : $grdTotAppAmt;		
	$grdTotBalAmt = $CLSReports->numberFormat(array_sum($grdTotBalAmtArr),2);
	$grdTotBalAmt =($grdTotBalAmt=='')? $showCurrencySymbol."00.00" : $grdTotBalAmt;		

	$delcsvFileData.= <<<DATA
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
		<tr style="height:20px">
			<td align="right" class="text11b" bgcolor="#FFFFFF" style="width:172px" colspan="2">(Deleted Records) Total&nbsp;</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF" style="width:150px">$grdTotPayAmt</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF" style="width:150px;">$grdTotAppAmt</td>
			<td align="right" class="text11b" bgcolor="#FFFFFF" style="width:150px">$grdTotBalAmt</td>
			<td align="left" class="text11b" bgcolor="#FFFFFF" colspan="2" style="width:378px;"></td>
		</tr>
		<tr><td class="total-row" colspan="7"></td></tr>
	</table>	
DATA;

	$csvFileData.= $fileHeaderData.$delcsvFileData;

	$delpdfData2.= <<<DATA
		<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
		<tr style="height:20px" >
			<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:1px"></td>
			<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:246px">(Deleted Records) Total&nbsp;</td>
			<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:115px">$grdTotPayAmt</td>
			<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:115px">$grdTotAppAmt</td>
			<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:125px">$grdTotBalAmt</td>
			<td align="right" class="text_b" bgcolor="#FFFFFF" style="width:122px"></td>
		</tr>
		<tr><td class="total-row" colspan="6"></td></tr>
	</table>	
DATA;


$pdfData.= <<<DATA
	<page backtop="5mm" backbottom="5mm">
	<page_footer>
		<table style="width: 100%;">
			<tr>
				<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			</tr>
		</table>
	</page_footer>
	<page_header>
	$fileHeaderData2
	</page_header>
	$delpdfData2
	</page>
DATA;
}

	}
}

//PRE-PAYMENTS BLOCK
// GET PATIENT PRE PAYMENTS
$patQry="Select pDep.id, pDep.patient_id, pDep.paid_amount, DATE_FORMAT(pDep.entered_date, '".$sqlDateFormat."') as 'paidDate', 
pData.providerID, pData.default_facility, pDep.entered_by, pDep.apply_payment_type, pDep.apply_amount, 
pDep.del_status, pDep.del_operator_id, DATE_FORMAT(pDep.trans_del_date, '".$sqlDateFormat."') as 'delDate', pDep.payment_mode, 
pData.fname as 'pfname', pData.mname as 'pmname', pData.lname as 'plname' 
FROM patient_pre_payment pDep 
LEFT JOIN patient_data pData ON pData.id = pDep.patient_id 
WHERE (pDep.entered_date between '$startDate' and '$endDate')";
if($strSelFac){
	$patQry .=" and pDep.facility_id IN($strSelFac)";
}
if(empty($operatorId) === false){
	$patQry .= " AND pDep.entered_by in($operatorId)";
}
$patQry .= " ORDER BY $sortByPat_Pre $sortByDot_pre_comm";
$arrDepIds=array();

$patQryRs	= imw_query($patQry);
	while($patQryRes = imw_fetch_assoc($patQryRs)){
	$refundAmt=0;

	##########################################################
	#query to get refund detail for current pre payment if any
	##########################################################

	$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND pmt_id = '".$patQryRes['id']."' AND (entered_date BETWEEN '".$startDate."' AND '".$endDate."')")or die(imw_error().'_656');
	while($rsRef=imw_fetch_array($qryRef))
	{
		$refundAmt+=$rsRef['ref_amt'];
	}
	imw_free_result($qryRef);
		
	$hasData=1;
	$facility='';	$docNameArr= array();	$patNameArr= array();

	$patId = $patQryRes['patient_id'];
	$oprId = $patQryRes['entered_by'];
	$id= $patQryRes['id'];
	$patNameArr["LAST_NAME"] = $patQryRes['plname'];
	$patNameArr["FIRST_NAME"] = $patQryRes['pfname'];
	$patNameArr["MIDDLE_NAME"] = $patQryRes['pmname'];
	$patName = changeNameFormat($patNameArr);
	
	if($patQryRes['del_status']=='0'){
		$tempData[$id]['OPR_ID']=$oprId;
		$tempData[$id]['PAT_ID']=$patId;
		$tempData[$id]['PAT_NAME']=$patName;
		$tempData[$id]['OPR_NAME']=$oprId;
		$tempData[$id]['PAID_DATE']=$patQryRes['paidDate'];
		$tempData[$id]['PAT_DEPOSIT']=$patQryRes['paid_amount']-$refundAmt;
		$tempData[$id]['PAT_DEPOSIT_REF']=$refundAmt;
		$tempData[$id]['PAID_MODE']=$patQryRes['payment_mode'];
		
		
		if($patQryRes['apply_payment_type']=='manually'){
			$tempData[$id]['APPLIED_AMT']+= $patQryRes['apply_amount'];
		}
		if($patQryRes['apply_payment_date']!='0000-00-00'){
			$arrDepIds[$id]=$id;	
		}
		$arrAllIds[$id]=$id;
	}elseif($patQryRes['del_status']=='1'){
		// DELETED PRE PAYMENTS
		$tempDelData[$patQryRes['delDate']]=$id;
		$delOprId=$patQryRes['del_operator_id'];
		$arrDepDelPatData[$id]['PATNAME'] = $patName.'-'.$patId;
		$arrDepDelPatData[$id]['PAT_DEPOSIT']= $patQryRes['paid_amount']-$refundAmt;
		$arrDepDelPatData[$id]['PAT_DEPOSIT_REF']= $refundAmt;
		$arrDepDelPatData[$id]['DEL_DATE'] = $patQryRes['delDate'];
		$arrDepDelPatData[$id]['DEL_OPERATOR']= $delOprId;
	}
}
// GET PRE PAT ENCOUNTER APPLIED AMTS
if(count($arrDepIds)>0){
	$strDepIds=implode(',', $arrDepIds);
	$preAppQry="Select patient_pre_payment_id, paidForProc FROM patient_charges_detail_payment_info  
	WHERE patient_charges_detail_payment_info.patient_pre_payment_id IN($strDepIds) AND deletePayment='0' AND unapply='0'";
	$preAppRs=imw_query($preAppQry);
	while($preAppRes=imw_fetch_array($preAppRs)){
		$id = $preAppRes['patient_pre_payment_id'];
		$tempData[$id]['APPLIED_AMT']+= $preAppRes['paidForProc'];
	}
}
// PRE PAYMENTS FINAL ARRAY
$totNotAppliedPreAmt=0;
$arrDepBreakDown=array();
foreach($arrAllIds as $id){
	$balance_amount=$tempData[$id]['PAT_DEPOSIT']-$tempData[$id]['APPLIED_AMT'];
	$oprId=$tempData[$id]['OPR_ID'];
	$printFile=true;
	
	if($processReport!='Summary'){
		$arrDepPatientData[$id]['PATNAME'] = $tempData[$id]['PAT_NAME'].'-'.$tempData[$id]['PAT_ID'];
		$arrDepPatientData[$id]['PAID_DATE'] = $tempData[$id]['PAID_DATE'];
		$arrDepPatientData[$id]['PAT_DEPOSIT']= $tempData[$id]['PAT_DEPOSIT'];
		$arrDepPatientData[$id]['PAT_DEPOSIT_REF']= $tempData[$id]['PAT_DEPOSIT_REF'];
		$arrDepPatientData[$id]['APPLIED_AMT']= $tempData[$id]['APPLIED_AMT'];
		$arrDepPatientData[$id]['NOT_APPLIED_AMT']= $balance_amount;
		$arrDepPatientData[$id]['OPERATOR']= $oprId;
	}
	$arrOperatorDepTots[$oprId]['PAT_DEPOSIT']+=$tempData[$id]['PAT_DEPOSIT'];
	$arrOperatorDepTots[$oprId]['PAT_DEPOSIT_REF']+=$tempData[$id]['PAT_DEPOSIT_REF'];
	$arrOperatorDepTots[$oprId]['APPLIED_AMT']+=$tempData[$id]['APPLIED_AMT'];
	$arrOperatorDepTots[$oprId]['NOT_APPLIED_AMT']+=$balance_amount;
	$arrOperators[$oprId]=$oprId;
	$totNotAppliedPreAmt+=$balance_amount;
	
	$paidMode=ucfirst(strtolower($tempData[$id]['PAID_MODE']));
	$arrDepBreakDown[$paidMode]+=$tempData[$id]['PAT_DEPOSIT'];
}
unset($tempData);

$htmlColWidth=220;
$pdfColWidth=140;
$colSpan=5;
$notApplyTitle='Unapplied Amount';
if($reportType!='encounter'){
	$colSpan=6;
	$htmlColWidth=135;
	$pdfColWidth=110;
	$notApplyTitle='Balance';
	$appliedTitleCol='<td class="text_b_w" style="width:'.$htmlColWidth.'px; text-align:center;">Applied</td>';
	$appliedTitleColPdf='<td class="text_b_w" style="width:'.$pdfColWidth.'px; text-align:center;">Applied</td>';
}

if($processReport == 'Detail'){
	if(sizeof($arrDepPatientData)>0){
		$prePayTotal=$preApplyTot=$preNotApplyTot=0;
		$pre_page_data.='<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding">';
		$pre_page_data.='<tr><td class="text_b_w" style="text-align:left" colspan="'.$colSpan.'">&nbsp;Patient Pre Payments</td></tr>';
		$preStrHTML.='<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" style="margin-top:20px;">';
		$preStrHTML.= '<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colSpan.'">&nbsp;Patient Pre Payments</td></tr>';	
	
		$pre_page_data.=
		'<tr>
			<td class="text_b_w" style="width:150px; text-align:center;">Patient Name-ID</td>
			<td class="text_b_w" style="width:'.$htmlColWidth.'px; text-align:center;">Payment Date</td>
			<td class="text_b_w" style="width:'.$htmlColWidth.'px; text-align:center;">Pre Payment</td>
			'.$appliedTitleCol.'
			<td class="text_b_w" style="width:'.$htmlColWidth.'px; text-align:center;">'.$notApplyTitle.'</td>
			<td class="text_b_w" style="width:auto; text-align:center;">Operator</td>
		</tr>';	
		//PDF						
		$preStrHTML .= '
		<tr>
			<td class="text_b_w" style="width:160px; text-align:center;">Patient Name-ID</td>
			<td class="text_b_w" style="width:90px; text-align:center;">Payment Date</td>
			<td class="text_b_w" style="width:90px; text-align:center;">Pre Payment</td>
			'.$appliedTitleColPdf.'
			<td class="text_b_w" style="width:90px; text-align:center;">'.$notApplyTitle.'</td>
			<td class="text_b_w" style="width:100px; text-align:center;">Operator</td>
		</tr>';							
		foreach($arrDepPatientData as $id => $patData){
			$prePayTotal+=$patData['PAT_DEPOSIT'];
			$prePayRefTotal+=$patData['PAT_DEPOSIT_REF'];
			$preApplyTot+=$patData['APPLIED_AMT'];
			$preNotApplyTot+=$patData['NOT_APPLIED_AMT'];
			
			if($patData['PAT_DEPOSIT_REF']>0)
			$redRow=';color:#FF0000" title="Refund '.$CLSReports->numberFormat($patData['PAT_DEPOSIT_REF'],2);
			else $redRow='';
			
			$pre_page_data.='
			<tr style="height:25px">
				<td class="text_10 alignLeft white">&nbsp;'.$patData['PATNAME'].'</td>
				<td class="text_10 alignLeft white" style="text-align:center">&nbsp;'.$patData['PAID_DATE'].'</td>
				<td class="text_10 white" style="text-align:right'.$redRow.'">'.$CLSReports->numberFormat($patData['PAT_DEPOSIT'],2,1).'&nbsp;</td>';
				if($reportType!='encounter'){
				$pre_page_data.='<td class="text_10 alignLeft white" style="text-align:right;">'.$CLSReports->numberFormat($patData['APPLIED_AMT'],2,1).'&nbsp;</td>';
				}				
			$pre_page_data.='
			<td class="text_10 white" style="text-align:right;">'.$CLSReports->numberFormat($patData['NOT_APPLIED_AMT'],2,1).'&nbsp;</td>
				<td class="text_10 white" style="text-align:center;">'.$userNameTwoCharArr[$patData['OPERATOR']].'</td>
			</tr>';
			//PDF
			$preStrHTML.='
			<tr style="height:25px">
				<td>&nbsp;'.$patData['PATNAME'].'</td>
				<td style="text-align:center">&nbsp;'.$patData['PAID_DATE'].'</td>
				<td style="text-align:right'.$redRow.'">'.$CLSReports->numberFormat($patData['PAT_DEPOSIT'],2,1).'&nbsp;</td>';
				if($reportType!='encounter'){
				$preStrHTML.='<td style="text-align:right;">'.$CLSReports->numberFormat($patData['APPLIED_AMT'],2,1).'&nbsp;</td>';
				}
			$preStrHTML.='								
				<td style="text-align:right;">'.$CLSReports->numberFormat($patData['NOT_APPLIED_AMT'],2,1).'&nbsp;</td>
				<td style="text-align:center;">'.$userNameTwoCharArr[$patData['OPERATOR']].'</td>
			</tr>';
		}
	
		$pre_page_data.='
		<tr style="height:30px">
			<td class="text_10b white" style="text-align:right;"></td>
			<td class="text_10b white" style="text-align:right;">Total :</td>
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($prePayTotal,2,1).'&nbsp;</td>';
			if($reportType!='encounter'){
			$pre_page_data.='<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($preApplyTot,2,1).'&nbsp;</td>';
			}				
		$pre_page_data.='	
			<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($preNotApplyTot,2,1).'&nbsp;</td>
			<td class="text_10b white" style="text-align:right;"></td>
		</tr>';		
		//PDF		
		$preStrHTML.='
		<tr style="height:30px">
			<td class="text_b white" style="text-align:right; width:'.$pdfColWidth.'px;"></td>
			<td class="text_b white" style="text-align:right; width:'.$pdfColWidth.'px;">Total :</td>
			<td class="text_b white" style="text-align:right; width:'.$pdfColWidth.'px;">'.$CLSReports->numberFormat($prePayTotal,2,1).'&nbsp;</td>';
			if($reportType!='encounter'){
			$preStrHTML.='<td class="text_b white" style="text-align:right; width:'.$pdfColWidth.'px;">'.$CLSReports->numberFormat($preApplyTot,2,1).'&nbsp;</td>';
			}
		$preStrHTML.='
			<td class="text_b white" style="text-align:right; width:'.$pdfColWidth.'px;">'.$CLSReports->numberFormat($preNotApplyTot,2,1).'&nbsp;</td>
			<td class="text_b white" style="text-align:right; width:'.$pdfColWidth.'px;"></td>
		</tr>';	
		
		//PAYMENT BREAKDOWN
		if(sizeof($arrDepBreakDown)>0){
			$preCols=5;
			if($reportType!='encounter'){$preCols=6;}
			$pendingCols=$preCols-3;
			$pre_page_data.='<tr><td colspan="'.$preCols.'" class="white" style="height:"15px">&nbsp;</td></tr>';			
			$totAmt=0;
			
			foreach($arrDepBreakDown as $mode => $paidAmt){
				$totAmt+=$paidAmt;
				$pre_page_data.=
				'<tr>
					<td class="white text_10" style="text-align:right">&nbsp;</td>
					<td class="white text_10b" style="text-align:right">'.$mode.'</td>
					<td class="white text_10" style="text-align:right">'.$CLSReports->numberFormat($paidAmt,2).'</td>
					<td class="white text_10" colspan="'.$pendingCols.'"></td>
				</tr>';
				//PDF
				$preStrHTML.=
				'<tr>
					<td class="white text_12" style="text-align:right">&nbsp;</td>
					<td class="white text_b" style="text-align:right; border:0px">'.$mode.'</td>
					<td class="white text_12" style="text-align:right">'.$CLSReports->numberFormat($paidAmt,2).'</td>
					<td class="white text_12" colspan="'.$pendingCols.'"></td>
				</tr>';
			}
			$pre_page_data.=
			'<tr><td class="total-row" colspan="'.$preCols.'"></td></tr>
			<tr>
				<td class="white text_10" style="text-align:right">&nbsp;</td>
				<td class="white text_10b" style="text-align:right">Total</td>
				<td class="white text_10b" style="text-align:right">'.$CLSReports->numberFormat($totAmt,2).'</td>
				<td class="white text_10" colspan="'.$pendingCols.'"></td>
			</tr>
			<tr><td class="total-row" colspan="'.$preCols.'"></td></tr>';
			//PDF
			$preStrHTML.=
			'<tr><td class="total-row" colspan="'.$preCols.'"></td></tr>
			<tr>
				<td class="white text_12" style="text-align:right">&nbsp;</td>
				<td class="white text_b" style="text-align:right;">Total</td>
				<td class="white text_b" style="text-align:right;">'.$CLSReports->numberFormat($totAmt,2).'</td>
				<td class="white text_12" colspan="'.$pendingCols.'"></td>
			</tr>
			<tr><td class="total-row" colspan="'.$preCols.'"></td></tr>';
		}						
		$pre_page_data.='</table>';
		$preStrHTML.='</table>';
		$csvFileData.=$pre_page_data;
		$pdfData.=$preStrHTML;
	}
}
if($processReport != 'Detail'){
	$colSpan=4;
	$pdfColWidth=172;
	if($reportType!='encounter'){
		$colSpan=5;
		$htmlColWidth=150;
		$pdfColWidth=116;
	}	
	if(sizeof($arrOperatorDepTots)>0){
		$prePayTotal=$preApplyTot=$preNotApplyTot=0;
		
		$pre_summary_data.=
		'<table width="700" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
		<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colSpan.'">&nbsp;Patient Pre Payments</td></tr>
		<tr>
		<td class="text_b_w" style="text-align:left; width:240px;">Operator</td>
		<td class="text_b_w" style="text-align:center; width:'.$htmlColWidth.'px;">Payment</td>
		'.$appliedTitleCol.'
		<td class="text_b_w" style="text-align:center; width:'.$htmlColWidth.'px;">'.$notApplyTitle.'</td>
		<td class="text_b_w" style="width:auto;"></td>
		</tr>';
		//PDF
		$pre_summary_html.=
		'<table width="700" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
		<tr><td class="text_b_w" style="text-align:left;" colspan="'.$colSpan.'">&nbsp;Patient Pre Payments</td></tr>
		<tr>
		<td class="text_b_w">Operator</td>
		<td class="text_b_w" style="text-align:center">Payment</td>
		'.$appliedTitleColPdf.'
		<td class="text_b_w" style="text-align:center">'.$notApplyTitle.'</td>
		</tr>';
		
		foreach($arrOperators as $oprID){
			$arrOperatorsTots=array();
			
			if(count($arrOperatorDepTots[$oprID])>0){
				$prePayTotal+=$arrOperatorDepTots[$oprID]['PAT_DEPOSIT'];
				$preApplyTot+=$arrOperatorDepTots[$oprID]['APPLIED_AMT'];
				$preNotApplyTot+=$arrOperatorDepTots[$oprID]['NOT_APPLIED_AMT'];
				
				if($arrOperatorDepTots[$oprID]['PAT_DEPOSIT_REF']>0)
				$redRow=';color:#FF0000" title="Refund '.$CLSReports->numberFormat($arrOperatorDepTots[$oprID]['PAT_DEPOSIT_REF'],2);
				else $redRow='';
				
				$pre_summary_data.='<tr><td class="white text_12" style="tsext-align:left">'.$userNameArr[$oprID].'</td>
				<td class="white text_12" style="text-align:right'.$redRow.'">'.$CLSReports->numberFormat($arrOperatorDepTots[$oprID]['PAT_DEPOSIT'],2,1).'&nbsp;</td>';
				if($reportType!='encounter'){
				$pre_summary_data.='<td class="text_12 alignLeft white" style="text-align:right;">'.$CLSReports->numberFormat($arrOperatorDepTots[$oprID]['APPLIED_AMT'],2,1).'&nbsp;</td>';
				}				
				$pre_summary_data.='
				<td class="white text_12" style="text-align:right">'.$CLSReports->numberFormat($arrOperatorDepTots[$oprID]['NOT_APPLIED_AMT'],2,1).'&nbsp;</td>
				<td class="white"></td>
				</tr>';
				//PDF
				$pre_summary_html.='<tr><td class="white text_12" style="text-align:left;">'.$userNameArr[$oprID].'</td>
				<td class="white text_12" style="text-align:right'.$redRow.'">'.$CLSReports->numberFormat($arrOperatorDepTots[$oprID]['PAT_DEPOSIT'],2,1).'&nbsp;</td>';
				if($reportType!='encounter'){
				$pre_summary_html.='<td class="text_12 white" style="text-align:right;">'.$CLSReports->numberFormat($arrOperatorDepTots[$oprID]['APPLIED_AMT'],2,1).'&nbsp;</td>';
				}
				$pre_summary_html.='
				<td class="white text_12" style="text-align:right;">'.$CLSReports->numberFormat($arrOperatorDepTots[$oprID]['NOT_APPLIED_AMT'],2,1).'&nbsp;</td>
				</tr>';
			}
		
		}
		$pre_summary_data.='<tr><td class="total-row" colspan="5"></td></tr><tr><td class="white text_12b" style="text-align:right">Total :</td>
		<td class="white text_12b" style="text-align:right">'.$CLSReports->numberFormat($prePayTotal,2,1).'&nbsp;</td>';
		if($reportType!='encounter'){
		$pre_summary_data.='<td class="text_10b white" style="text-align:right;">'.$CLSReports->numberFormat($preApplyTot,2,1).'&nbsp;</td>';
		}
		$pre_summary_data.='				
		<td class="white text_12b" style="text-align:right">'.$CLSReports->numberFormat($preNotApplyTot,2,1).'&nbsp;</td>
		<td class="white"></td>
		</tr><tr><td class="total-row" colspan="5"></td></tr>';
		//PDF
		$pre_summary_html.='<tr><td class="total-row" colspan="5"></td></tr><tr><td class="white text_b" style="text-align:right; width:370px">Total :</td>
		<td class="white text_b" style="text-align:right; width:'.$pdfColWidth.'px">'.$CLSReports->numberFormat($prePayTotal,2,1).'&nbsp;</td>';
		if($reportType!='encounter'){
		$pre_summary_html.='<td class="text_b white" style="text-align:right; width:'.$pdfColWidth.'px;">'.$CLSReports->numberFormat($preApplyTot,2,1).'&nbsp;</td>';
		}
		$pre_summary_html.='
		<td class="white text_b" style="text-align:right; width:'.$pdfColWidth.'px">'.$CLSReports->numberFormat($preNotApplyTot,2,1).'&nbsp;</td>
		</tr><tr><td class="total-row" colspan="5"></td></tr>';

		//PAYMENT BREAKDOWN
		if(sizeof($arrDepBreakDown)>0){
			$pre_summary_data.='<tr><td colspan="5" class="white" style="height:"15px">&nbsp;</td></tr>';			
			$totAmt=0;
			foreach($arrDepBreakDown as $mode => $paidAmt){
				$totAmt+=$paidAmt;
				$pre_summary_data.=
				'<tr>
					<td class="white text_12b" style="text-align:right">'.$mode.'</td>
					<td class="white text_12" style="text-align:right">'.$CLSReports->numberFormat($paidAmt,2).'</td>
					<td class="white text_12" colspan="3"></td>
				</tr>';
				//PDF
				$pre_summary_html.=
				'<tr>
					<td class="white text_12b" style="text-align:right;">'.$mode.'</td>
					<td class="white text_12" style="text-align:right">'.$CLSReports->numberFormat($paidAmt,2).'</td>
					<td class="white text_12" colspan="3"></td>
				</tr>';
			}
			$pre_summary_data.=
			'<tr><td class="total-row" colspan="5"></td></tr>
			<tr>
				<td class="white text_12b" style="text-align:right">Total</td>
				<td class="white text_12b" style="text-align:right">'.$CLSReports->numberFormat($totAmt,2).'</td>
				<td class="white text_12" colspan="3"></td>
			</tr>
			<tr><td class="total-row" colspan="5"></td></tr>';
			//PDF
			$pre_summary_html.=
			'<tr><td class="total-row" colspan="5"></td></tr>
			<tr>
				<td class="white text_12b" style="text-align:right;">Total</td>
				<td class="white text_12b" style="text-align:right;">'.$CLSReports->numberFormat($totAmt,2).'</td>
				<td class="white text_12" colspan="3"></td>
			</tr>
			<tr><td class="total-row" colspan="5"></td></tr>';
		}
		$pre_summary_data.='</table>';
		$pre_summary_html.='</table>';
		$csvFileData.=$pre_summary_data;
		$pdfData.=$pre_summary_html;
	}
}
//DELETED PRE PAYMENTS BLOCK
if(sizeof($arrDepDelPatData)>0){
ksort($tempDelData);
	$pdfColWidth=150;
	if($reportType=='encounter'){$pdfColWidth=147;}

	$prePayTotal=0;
	$pre_del_page_data.='<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">';
	$pre_del_page_data.='<tr><td class="text_b_w" style="text-align:left" colspan="4">&nbsp;Deleted Patient Pre Payments</td></tr>';
	$preDelStrHTML.='<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">';
	$preDelStrHTML.= '<tr><td class="text_b_w" style="text-align:left;" colspan="4">&nbsp;Deleted Patient Pre Payments</td></tr>';	

	$pre_del_page_data.=
	'<tr>
		<td class="text_b_w" style="width:250px; text-align:center;">Patient Name-ID</td>
		<td class="text_b_w" style="width:250px; text-align:center;">Pre Payment</td>
		<td class="text_b_w" style="width:250px; text-align:center;">Deleted Date</td>
		<td class="text_b_w" style="width:250px; text-align:center;">Deleted By</td>
	</tr>';							
	$preDelStrHTML .= '
	<tr>
		<td class="text_b_w" style="text-align:center; width:230px;">Patient Name-ID</td>
		<td class="text_b_w" style="text-align:center;">Pre Payment</td>
		<td class="text_b_w" style="text-align:center;">Deleted Date</td>
		<td class="text_b_w" style="text-align:center;">Deleted By</td>
	</tr>';	
	foreach($tempDelData as $date => $id){
		$patData=$arrDepDelPatData[$id];
		$prePayTotal+=$patData['PAT_DEPOSIT'];
		$pre_del_page_data.='
		<tr style="height:25px">
			<td class="text_10 alignLeft white" style="width:250px;">&nbsp;'.$patData['PATNAME'].'</td>
			<td class="text_10 white" style="width:250px; text-align:right;">'.$CLSReports->numberFormat($patData['PAT_DEPOSIT'],2).'&nbsp;</td>
			<td class="text_10 alignLeft white" style="width:250px; text-align:center">&nbsp;'.$patData['DEL_DATE'].'</td>
			<td class="text_10 white" style="width:250px; text-align:center;">'.$userNameTwoCharArr[$patData['DEL_OPERATOR']].'</td>
		</tr>';
		$preDelStrHTML.='
		<tr style="height:25px">
			<td class="text_12 alignLeft white">&nbsp;'.$patData['PATNAME'].'</td>
			<td class="text_12 white" style="text-align:right;">'.$CLSReports->numberFormat($patData['PAT_DEPOSIT'],2).'&nbsp;</td>
			<td class="text_12 alignLeft white" style="text-align:center">&nbsp;'.$patData['DEL_DATE'].'</td>
			<td class="text_12 white" style="text-align:center;">'.$userNameTwoCharArr[$patData['DEL_OPERATOR']].'</td>
		</tr>';
	}

	$pre_del_page_data.='
	<tr><td class="total-row" colspan="4"></td></tr>
	<tr style="height:30px">
		<td class="text_10b white" style="text-align:right; width:250px;">Total :</td>
		<td class="text_10b white" style="text-align:right; width:250px;">'.$CLSReports->numberFormat($prePayTotal,2).'&nbsp;</td>
		<td class="text_10b white" style="text-align:right; width:250px;"></td>
		<td class="text_10b white" style="text-align:right; width:auto;"></td>
	</tr><tr><td class="total-row" colspan="4"></td></tr>';				
	$preDelStrHTML.='
	<tr><td class="total-row" colspan="4"></td></tr><tr style="height:30px">
		<td class="text_b white" style="text-align:right; width:205px;">Total :</td>
		<td class="text_b white" style="text-align:right; width:205px;">'.$CLSReports->numberFormat($prePayTotal,2).'&nbsp;</td>
		<td class="text_b white" style="text-align:right; width:'.$pdfColWidth.'px;"></td>
		<td class="text_b white" style="text-align:right; width:'.$pdfColWidth.'px;"></td>
	</tr><tr><td class="total-row" colspan="4"></td></tr>';					
	$pre_del_page_data.='</table>';
	$preDelStrHTML.='</table>';
	$csvFileData.=$pre_del_page_data;
	$pdfData.=$preDelStrHTML;
}
//-- END PRE PAYMENTS	


// GRAND TOTALS
if($reportType=='encounter'){
	$grandTotal=$grdTotalPayments + $preNotApplyTot;
	if($grandTotal>0){
		$csvFileData.=
		'<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
		<tr><td class="text_b_w" style="text-align:left;" colspan="3">&nbsp;Totals</td></tr>
		<tr style="height:20px;">
		<td class="white text_b" style="text-align:right; width:510px;">Total Payments :</td>
		<td class="white text_b" style="text-align:right; width:100px;">'.$CLSReports->numberFormat($grdTotalPayments,2).'&nbsp;</td>
		<td class="white text_b" style="width:auto;"></td>
		</tr>
		<tr><td class="total-row" colspan="3"></td></tr>
		<tr style="height:20px;">
		<td class="white text_b" style="text-align:right;">Total Pre Payments But Not Applied :</td>
		<td class="white text_b" style="text-align:right;">'.$CLSReports->numberFormat($preNotApplyTot,2).'&nbsp;</td>
		<td class="white text_b" style="width:auto;"></td>
		</tr>
		<tr><td class="total-row" colspan="3"></td></tr>
		<tr style="height:20px;">
		<td class="white text_b" style="text-align:right;">Total :</td>
		<td class="white text_b" style="text-align:right;">'.$CLSReports->numberFormat($grandTotal,2).'&nbsp;</td>
		<td class="white text_b" style="width:auto;"></td>
		</tr>
		<tr><td class="total-row" colspan="3"></td></tr>
		</table>';
		$pdfData.=
		'<table width="100%" class="rpt rpt_table rpt_table-bordered rpt_padding" bgcolor="#FFF3E8">
		<tr><td class="text_b_w" style="text-align:left;" colspan="3">&nbsp;Totals</td></tr>
		<tr>
		<td class="white text_b" style="text-align:right; width:510px;">Total Payments :</td>
		<td class="white text_b" style="text-align:right; width:220px;">'.$CLSReports->numberFormat($grdTotalPayments,2).'&nbsp;</td>
		<td class="white text_b" style=" width:15px;"></td>
		</tr>
		<tr><td class="total-row" colspan="3"></td></tr>
		<tr>
		<td class="white text_b" style="text-align:right;">Total Pre Payments But Not Applied :</td>
		<td class="white text_b" style="text-align:right;">'.$CLSReports->numberFormat($preNotApplyTot,2).'&nbsp;</td>
		<td class="white text_b"></td>
		</tr>
		<tr><td class="total-row" colspan="3"></td></tr>
		<tr>
		<td class="white text_b" style="text-align:right;">Total :</td>
		<td class="white text_b" style="text-align:right;">'.$CLSReports->numberFormat($grandTotal,2).'&nbsp;</td>
		<td class="white text_b"></td>
		</tr>
		<tr><td class="total-row" colspan="3"></td></tr>
		</table>';
	}
}
?>