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
$arrFacilitySel=array();
$arrDoctorSel=array();
$sel_grp = $CLSReports->report_display_selected(implode(',',$groups),'group',1,$grp_cnt);
$sel_grp = (strlen($sel_grp)>25) ? substr($sel_grp,0, 23).'...' : $sel_grp;
$sel_fac = $CLSReports->report_display_selected(implode(',',$facility_name),'facility_tbl',1,$fac_cnt);
$sel_phy = $CLSReports->report_display_selected(implode(',',$phyId),'physician',1,$phy_cnt);
$sel_opr = $CLSReports->report_display_selected(implode(',',$operator_id),'operator',1,$opr_cnt);
$sel_method= (empty($pay_method)==false) ? strtoupper($pay_method) : 'All';

$Process =  strtolower($summary_detail);

if($_POST['form_submitted']){
	$conditionChk = false;
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
	$facility_name = join(',',$facility_name);
	
	//PHYSICIAN
	if(empty($_REQUEST['phyId'])==false){
		$rqArrPhyId = $_REQUEST['phyId'];
	}
	$providerID = join(',',$rqArrPhyId);
	
	//operator_id
	if(empty($_REQUEST['operator_id'])==false){
		$rqArrOprId = $_REQUEST['operator_id'];
	}
	$opr_name = join(',',$rqArrOprId);
	
	if(empty($_REQUEST['groups'])==false){
		$groupId = $_REQUEST['groups'];
	}
	$grp_id = join(',',$groupId);
}

$page_data = '';	$printFile= true;


if(empty($Submit) === false){

	$printFile = false;
	
	//Combining Payments  data - Check in, Check Out, Pre , Post payments
	$arrPayData = array();
	
	
	$st_date = getDateFormatDB($Start_date);
	$en_date = getDateFormatDB($End_date);
	
	$primaryProviderId = $providerID;
	$facility_name_str = $facility_name;
	$opr_name_str = $opr_name;

	// GET ALL USERS
	$userNameTwoCharArr=array();
	$rs=imw_query("Select id,fname,lname FROM users");
	while($res=imw_fetch_array($rs)){
		$usrNameArr=array();
		$usrNameArr["LAST_NAME"] = $res['lname'];
		$usrNameArr["FIRST_NAME"] = $res['fname'];
		$usrName = changeNameFormat($usrNameArr);
		$arrAllUsers[$res['id']]=$usrName;

		// two character array
		$operatorInitial = substr($res['fname'],0,1);
		$operatorInitial .= substr($res['lname'],0,1);
		$userNameTwoCharArr[$res['id']] = strtoupper($operatorInitial);
	}
	
	//getting report generator name
	$report_generator_name = NULL;
	if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
		$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
		$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
		$report_generator_name = strtoupper($report_generator_name);
	}

	// GET SELECTED SCHEDULER FACILITIES
	$sch_fac_id_str='';
	$query = "Select id,name,fac_prac_code from facility";
	if(empty($facility_name_str)==false){ 
		$query.= " WHERE id IN($facility_name_str)"; 
	}
	$fac_query=imw_query($query);
	$sch_fac_id_arr = array();
	$arr_pos_fac=array();
	$arr_sch_facilities=array();
	while($fac_query_res=imw_fetch_array($fac_query)){
		$fac_id = $fac_query_res['id'];
		$sch_fac_id_arr[$fac_id] = $fac_id;
		$arr_sch_facilities[$fac_id]=$fac_query_res['name'];
		$sch_pos_fac_arr[$fac_id] = $fac_query_res['fac_prac_code'];
		$arr_pos_fac[$fac_query_res['fac_prac_code']]=$fac_query_res['fac_prac_code'];
		
	}
	$sch_fac_id_str = implode(',',$sch_fac_id_arr);
	
	// GET ALL FACILITIES
	$allFacArr=array();
	$qry = "select pos_facilityies_tbl.facilityPracCode as name,
				pos_facilityies_tbl.pos_facility_id as id,
				pos_tbl.pos_prac_code
				from pos_facilityies_tbl 
				LEFT JOIN pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id";
	if(sizeof($arr_pos_fac)>0){
		$str_pos_fac=implode(',', $arr_pos_fac);
		$qry.=" WHERE pos_facility_id IN($str_pos_fac)";
	}
	$qryRs = imw_query($qry);
	while($qryRes = imw_fetch_array($qryRs)){
		$allFacArr[$qryRes['id']] = $qryRes['name'].' - '.$qryRes['pos_prac_code'];
	}
	
	// CI/CO FIELDS
	$rs= imw_query("Select * FROM check_in_out_fields");
	while($res = imw_fetch_array($rs)){
		$cioFields[$res['id']] = $res['item_name'];	
	}

	$hasData=0;
	// CI/CO AMOUNTS
	$sch_query ="SELECT 
	sa.sa_facility_id, sa.sa_patient_id, sa.sa_doctor_id, 
	cioPay.payment_id, 
	cioPay.sch_id, 
	cioPay.created_by, cioPay.payment_method,cioPay.check_no,cioPay.cc_type, cioPay.cc_no,
	DATE_FORMAT(cioPay.created_on, '".get_sql_date_format()."') as 'paidDate', 
	cioPay.total_payment,
	cioDet.id, 
	cioDet.item_payment as 'cioPayment', 
	cioDet.status as 'detStatus', 
	cioDet.item_id,
	pd.fname as 'pfname', 
	pd.mname as 'pmname', 
	pd.lname as 'plname', 
	us.fname, 
	us.lname 
	FROM schedule_appointments sa 
	LEFT JOIN check_in_out_payment cioPay ON cioPay.sch_id  = sa.id 
	LEFT JOIN check_in_out_payment_details cioDet ON cioDet.payment_id  = cioPay.payment_id  
	LEFT JOIN users us on us.id = sa.sa_doctor_id 
	JOIN patient_data pd ON pd.id = sa.sa_patient_id
	LEFT JOIN facility ON sa.sa_facility_id = facility.id 
	WHERE (cioPay.created_on between '".$st_date."' and '".$en_date."') 
	AND pd.id IS NOT NULL";
	if($report_type== "WithDeleted"){
		$sch_query .= " AND (cioDet.status='0' OR (cioDet.status ='1' && cioDet.delete_date >'".$en_date."')) ";
	}else{
		$sch_query .= " AND cioDet.status='0' ";
	}
	if(empty($primaryProviderId) === false){
		$sch_query .= " and sa.sa_doctor_id in($primaryProviderId)";
	}
	if(empty($facility_name_str) === false){
		$sch_query .= " and sa.sa_facility_id in($facility_name_str)";
	}
	if(empty($opr_name_str) === false){
		$sch_query .= " and cioPay.created_by in($opr_name_str)";
	}
	if(empty($grp_id) === false){
		$sch_query .= " and facility.default_group in($grp_id)";
	}
	if(empty($pay_method) === false){
		$sch_query .= " and LOWER(cioPay.payment_method)='".$pay_method."'";
	}

	$sch_query .= " ORDER BY sa.sa_facility_id, us.lname, cioPay.created_on";

	$rs=imw_query($sch_query);

	$arrTempDetIds=array();
	$arrApplyEids=array();
	$arrPayData = array();
	$arrCioAccId = array();
	$arrCIODetId = array();
	$arrPatPayDetail = array();
	$cicopayment_method_arr = array();
	while($res=imw_fetch_array($rs)){	
		$hasData=1;
		$facility=''; $check_no = ''; $payment_method = '';	$docNameArr= array();	$patNameArr= array();
		$cioDetId= $res['id'];
		$payment_id = $res['payment_id'];
		$paidDate = $res['paidDate'];
		
		//PRABH-GETTING REFUND AMOUNTS
		$refundAmt=0;
		$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND ci_co_id = '".$res['id']."' AND (entered_date BETWEEN '".$st_date."' AND '".$en_date."')");
		while($rsRef=imw_fetch_array($qryRef))
		{
			$refundAmt+=$rsRef['ref_amt'];
		}
		imw_free_result($qryRef);
		
		$facility = $res['sa_facility_id'];
		if($facility=='') { $facility=0; }

		$doc_id = $res['sa_doctor_id'];
		$patId = $res['sa_patient_id'];
		$itemId=$res['item_id'];
		$oprId = $res['created_by'];
		
		$CICOpayArrVal = $res['payment_method'];
		$reference_no = (empty($res['check_no']) === false) ? $res['check_no'] : '';
		
		if('credit card' == strtolower($res['payment_method']) ){
			$res['cc_type'] = ($res['cc_type']) ? 'CC-'.$res['cc_type'] : 'Credit Card';
			$CICOpayArrVal = $res['cc_type'];
			$reference_no=substr($res['cc_no'], -4);
		}
		
		$hasData = 1;
		if(!$arrCIODetId[$cioDetId]){
			$arrPayData[$doc_id][$facility]['payment'] += $res['cioPayment']-$refundAmt;
			$arrPayData[$doc_id][$facility]['payment_ref'] += $refundAmt;
			//----DETAIL ARRAY------
			
			$arrPatPayDetail[$patId][$paidDate][$oprId]['cio_payment'] += $res['cioPayment']-$refundAmt;
			$arrPatPayDetail[$patId][$paidDate][$oprId]['cio_payment_ref'] += $refundAmt;
			$arrPatPayDetail[$patId][$paidDate][$oprId]['cio_item_id'] = $itemId;
			//$arrPatPayDetail[$patId][$paidDate][$oprId]['cio_pay_mode'][$CICOpayArrVal]  += $res['cioPayment']-$refundAmt;
			
			//merging check in - check out payments 
			$payIncrVal = $res['cioPayment']-$refundAmt;
			$arrPatPayDetail[$patId][$paidDate][$oprId]['PAYMENT_ROW']['CheckIn'][$payment_id]['method'] = $CICOpayArrVal;
			$arrPatPayDetail[$patId][$paidDate][$oprId]['PAYMENT_ROW']['CheckIn'][$payment_id]['reference_no'] = $reference_no;
			$arrPatPayDetail[$patId][$paidDate][$oprId]['PAYMENT_ROW']['CheckIn'][$payment_id]['pay']+= $payIncrVal;
			
			//------------------------
			$arrCIODetId[$cioDetId] = $cioDetId;
			$arrTempCIO[$cioDetId]['doctor']=$doc_id;
			$arrTempCIO[$cioDetId]['facility']=$facility;
			$arrTempCIO[$cioDetId]['pat_id']=$patId;
			$arrTempCIO[$cioDetId]['paid_date']=$paidDate;
			$arrTempCIO[$cioDetId]['opr_id']=$oprId;
			$arrTempCIO[$cioDetId]['payment_id']=$payment_id;
			
			
			//PAYMENT BREAKDOWN
			$arrPayBreakdown[strtoupper($res['payment_method'])]+= $res['cioPayment']-$refundAmt;
			if( 'credit card' == strtolower($res['payment_method']) ){
				$arrCCPayBreakdown[strtoupper($res['cc_type'])]+= $res['cioPayment']-$refundAmt;
			}
		}
	}
	unset($rs);

	//GETTING APPLIED DATA FOR CI/CO	
	$splitted_encounters=array();
	if(sizeof($arrTempCIO)>0){
		$arrAllPayDetIds= array_keys($arrTempCIO);
		$splitted_encounters = array_chunk($arrAllPayDetIds,4000);
		$tempCIOPaid=array();
		
		foreach($splitted_encounters as $arrSplittedPayDetIds){
			$str_splitted_encs 	 = implode(',',$arrSplittedPayDetIds);
			$arr_acc_payment_id=array();
			$arrCioAccPayId = array();

			$qry="SELECT cioPost.check_in_out_payment_detail_id, cioPost.check_in_out_payment_id, 
			 cioPost.manually_payment, 
			 cioPost.acc_payment_id 
			 FROM check_in_out_payment_post cioPost 
			 WHERE cioPost.check_in_out_payment_detail_id IN(".$str_splitted_encs.") 
			 AND cioPost.status='0'";
			$rs=imw_query($qry);
			while($res=imw_fetch_array($rs)){
				$payment_id = $res['check_in_out_payment_id'];
				$cioDetId = $res['check_in_out_payment_detail_id'];
				$acc_payment_id = $res['acc_payment_id'];

				if($res['manually_payment']>0){
					$tempCIOArr[$cioDetId]['manual_applied']+= $res['manually_payment'];
					$tempCIOManuallyApplied[$payment_id]+=$res['manually_payment'];
				}
				
				if($res['acc_payment_id']>0){ 
					$arr_acc_payment_id[$acc_payment_id]=$acc_payment_id;
					$arrCioAccId[$acc_payment_id] = $acc_payment_id;
					$arrCioAccPayId[$acc_payment_id] = $cioDetId;
				}
			}
			unset($rs);


			if(sizeof($arr_acc_payment_id)>0){
				$str_acc_payment_id = implode(',', $arr_acc_payment_id);
				
				$qry="SELECT patPay.payment_id, 
					  patPayDet.paidForProc 
					  FROM patient_chargesheet_payment_info patPay 
					  LEFT JOIN patient_charges_detail_payment_info patPayDet ON patPayDet.payment_id = patPay.payment_id 
					  WHERE patPay.payment_id IN(".$str_acc_payment_id.") 
					  AND patPayDet.deletePayment='0' 
					  AND patPayDet.unapply='0'";

				$rs=imw_query($qry);
				while($res=imw_fetch_array($rs)){
					$cioDetId = $arrCioAccPayId[$res['payment_id']];
					$tempCIOArr[$cioDetId]['encounter_applied']+= $res['paidForProc'];
				}unset($rs);
			}
			unset($arr_acc_payment_id);

			//COMBINING APPLIED CI/CO DATA
			foreach($tempCIOArr as $cioDetId => $appliedData){
				$doc_id=	$arrTempCIO[$cioDetId]['doctor'];
				$facility=	$arrTempCIO[$cioDetId]['facility'];
				$patId=		$arrTempCIO[$cioDetId]['pat_id'];
				$paidDate=	$arrTempCIO[$cioDetId]['paid_date'];
				$oprId=		$arrTempCIO[$cioDetId]['opr_id'];
				$payment_id= $arrTempCIO[$cioDetId]['payment_id'];
				
				//FOR SUMMARY
				$arrPayData[$doc_id][$facility]['applied']+= $tempCIOArr[$cioDetId]['manual_applied'] + $tempCIOArr[$cioDetId]['encounter_applied'];
				//FOR DETAIL
				$arrPatPayDetail[$patId][$paidDate][$oprId]['cio_applied']+= $tempCIOArr[$cioDetId]['manual_applied'] + $tempCIOArr[$cioDetId]['encounter_applied'];
				$arrPatPayDetail[$patId][$paidDate][$oprId]['PAYMENT_ROW']['CheckIn'][$payment_id]['applied']+= $tempCIOArr[$cioDetId]['manual_applied'] + $tempCIOArr[$cioDetId]['encounter_applied'];

				//FOR MANUALLY APPLIED BLOCK
				if($tempCIOManuallyApplied[$payment_id]>0){
					//SUMMARY
					$arrManuallyApplied[$oprId]['cico']+= $tempCIOManuallyApplied[$payment_id];

					//DETAIL
					$arrCICOManuallyPaid[$oprId][$patId][$payment_id]['pat_id']=$patId;
					$arrCICOManuallyPaid[$oprId][$patId][$payment_id]['paid_date']=$paidDate;
					$arrCICOManuallyPaid[$oprId][$patId][$payment_id]['applied_amt']= $tempCIOManuallyApplied[$payment_id];
				}
			}
		}
		unset($arrTempCIO);
	}	


	// GET PATIENT PRE PAYMENTS
	$patQry="SELECT pDep.id, 
	pDep.patient_id, 
	pDep.paid_amount, 
	DATE_FORMAT(pDep.entered_date, '".get_sql_date_format()."') as 'entered_date', 
	DATE_FORMAT(pDep.paid_date, '".get_sql_date_format()."') as 'paidDate',
	pDep.entered_by, 
	pDep.apply_payment_type, 
	pDep.apply_amount, 
	pDep.del_status, pDep.provider_id,
	pDep.del_operator_id, pDep.payment_mode, pDep.check_no, pDep.cc_no, pDep.credit_card_co as cc_type,
	DATE_FORMAT(pDep.trans_del_date, '".get_sql_date_format()."') as 'delDate', pDep.facility_id,
	pData.default_facility, 
	pData.fname as 'pfname', 
	pData.mname as 'pmname', 
	pData.lname as 'plname' 
	FROM patient_pre_payment pDep 
	JOIN patient_data pData ON pData.id = pDep.patient_id 
	LEFT JOIN facility ON facility.id = pDep.facility_id 
	WHERE pData.id IS NOT NULL ";
	if($report_type== "WithDeleted"){
		$patQry .= " AND (pDep.del_status='0' OR (pDep.del_status='1' AND DATE_FORMAT(pDep.trans_del_date, '%Y-%m-%d')>'$en_date'))";
	}else{
		$patQry .= " AND pDep.del_status='0' ";
	}
	if(empty($facility_name_str) === false){
		$patQry .= " AND pDep.facility_id in($facility_name_str)";
	}
	if(empty($opr_name_str) === false){
		$patQry .= " AND pDep.entered_by in($opr_name_str)";
	}
	if(empty($grp_id) === false){
		$patQry .= " AND facility.default_group in($grp_id)";
	}
	if(empty($primaryProviderId) === false){
		$patQry.= " AND pDep.provider_id IN(".$primaryProviderId.")";
	}
	if(empty($pay_method) === false){
		$patQry .= " and LOWER(pDep.payment_mode)='".$pay_method."'";
	}
	if($DateRangeFor=='dot'){
		$patQry.=" AND (pDep.entered_date between '".$st_date."' and '".$en_date."')";
	}else{
		$patQry.=" AND (pDep.paid_date between '".$st_date."' and '".$en_date."')";
	}
	
	$patQry .= " ORDER BY pDep.entered_date";
	$qryRes = imw_query($patQry);
	$prepayment_method_arr = array();
	while($row = imw_fetch_assoc($qryRes)){
		$patQryRes[] = $row;
	}
	
	$arrDepIds=array();
	$arrPrePayData = array();
	$arrPrePayDelData = array();
	for($i=0;$i<count($patQryRes);$i++){
		$refundAmt=0;
		//PRABH: GET REFUND AMOUNTS

		$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND pmt_id = '".$patQryRes[$i]['id']."' AND (entered_date BETWEEN '".$st_date."' AND '".$en_date."')")or die(imw_error().'_656');
		while($rsRef=imw_fetch_array($qryRef))
		{
			$refundAmt+=$rsRef['ref_amt'];
		}
		imw_free_result($qryRef);
		
		$hasData=1;
		$facility=''; $check_no = ''; $payment_method = '';	$docNameArr= array();	$patNameArr= array();
		$patId = $patQryRes[$i]['patient_id'];
		$oprId = $patQryRes[$i]['entered_by'];
		$id= $patQryRes[$i]['id'];
		$paidDate = $patQryRes[$i]['paidDate'];
		$enterDate = $patQryRes[$i]['entered_date'];
		
		$prepayArrVal = $patQryRes[$i]['payment_mode'];
		$reference_no = (empty($patQryRes[$i]['check_no']) === false) ? $patQryRes[$i]['check_no'] : '';
		
		if( 'credit card' == strtolower($patQryRes[$i]['payment_mode']) ){
			$patQryRes[$i]['cc_type'] = $patQryRes[$i]['cc_type'] ? 'CC-'.$patQryRes[$i]['cc_type'] : 'Credit Card';
			$prepayArrVal = $patQryRes[$i]['cc_type'];
			$reference_no=substr($patQryRes[$i]['cc_no'], -4);

		}
		
		$hasData = 1;
		$arrPrePayData[$oprId]['payment'] += $patQryRes[$i]['paid_amount']-$refundAmt;
		$arrPrePayData[$oprId]['payment_ref'] += $refundAmt;
		//---DETAIL ARRAY---
		$arrPatPayDetail[$patId][$paidDate][$oprId]['pre_payment'] += $patQryRes[$i]['paid_amount']-$refundAmt;
		$arrPatPayDetail[$patId][$paidDate][$oprId]['pre_payment_ref'] +=$refundAmt;
		$arrPatPayDetail[$patId][$paidDate][$oprId]['pre_pay_id'][] = $id;
		
		//$arrPatPayDetail[$patId][$paidDate][$oprId]['pre_pay_mode'][$prepayArrVal]  += $patQryRes[$i]['paid_amount']-$refundAmt;
		
		//merging pre payments 
		$payPreIncrVal =+ $patQryRes[$i]['paid_amount']-$refundAmt;
		$arrPatPayDetail[$patId][$paidDate][$oprId]['PAYMENT_ROW']['PrePay'][$id]['method'] = $prepayArrVal;
		$arrPatPayDetail[$patId][$paidDate][$oprId]['PAYMENT_ROW']['PrePay'][$id]['reference_no'] = $reference_no;
		$arrPatPayDetail[$patId][$paidDate][$oprId]['PAYMENT_ROW']['PrePay'][$id]['pay']+= $payPreIncrVal;
		
		
		//------------------
		if($patQryRes[$i]['apply_payment_type'] == 'manually'){

			//-------ADD MANUALLY APPLIED PRE PAYMENTS-------------------
			$arrPrePayData[$oprId]['applied'] += $patQryRes[$i]['apply_amount'];
			//--DETAIL ARRAY-
			//$arrPatPayDetail[$patId][$paidDate][$oprId]['pre_applied'] += $patQryRes[$i]['apply_amount'];
			$arrPatPayDetail[$patId][$paidDate][$oprId]['PAYMENT_ROW']['PrePay'][$id]['applied']+= $patQryRes[$i]['apply_amount'];

			//SUMMARY
			$arrManuallyApplied[$oprId]['pre_payment']+= $patQryRes[$i]['apply_amount'];
			//DETAIL
			$arrPrePayManuallyApplied[$oprId][$id]['pat_name']=$patId;
			$arrPrePayManuallyApplied[$oprId][$id]['entered_date']=$paidDate;
			$arrPrePayManuallyApplied[$oprId][$id]['applied_amt'] = $patQryRes[$i]['apply_amount'];
			
			/* if($DateRangeFor=='dot'){
				$arrPrePayManuallyApplied[$oprId][$id]['entered_date']=$patQryRes[$i]['entered_date'];
			}else{
				$arrPrePayManuallyApplied[$oprId][$id]['entered_date']=$patQryRes[$i]['paidDate'];
			} */
			
		}
		$arrPrePayData[$oprId]['pre_pay_id'][] = $id;
		$arrDepIds[$id]=$id;

		//PAYMENT BREAKDOWN
		$arrPayBreakdown[strtoupper($patQryRes[$i]['payment_mode'])]+= $patQryRes[$i]['paid_amount']-$refundAmt;
		if( 'credit card' == strtolower($patQryRes[$i]['payment_mode']) ){
				$arrCCPayBreakdown[strtoupper($patQryRes[$i]['cc_type'])]+= $patQryRes[$i]['paid_amount']-$refundAmt;
		}
	}

	
	// GET PRE PAT ENCOUNTER APPLIED AMTS
	$arrPrePayApplied = array();
	$arrPrePayAppliedId = array();
	if(count($arrDepIds)>0){
		$strDepIds=implode(',', $arrDepIds);
		$preAppQry="SELECT
		pDep.id, pDep.patient_id,
		pDep.entered_by, DATE_FORMAT(pDep.paid_date, '".get_sql_date_format()."') as 'paidDate',
		patPayDet.paidForProc,
		patPayDet.unapply,
		patPayDet.deletePayment
		FROM patient_pre_payment pDep 
		LEFT JOIN patient_charges_detail_payment_info patPayDet ON patPayDet.patient_pre_payment_id = pDep.id 
		WHERE pDep.id IN($strDepIds) 
		AND (patPayDet.deletePayment='0' OR (patPayDet.deletePayment='1' AND patPayDet.deleteDate>'".$en_date."'))
		WHERE pDep.id IN($strDepIds)";
		if($report_type== "WithDeleted"){
			$preAppQry .= " AND (patPayDet.deletePayment='0' OR (patPayDet.deletePayment='1' AND patPayDet.deleteDate>'".$en_date."')) ";
			$preAppQry .= " AND (patPayDet.unapply='0' OR (patPayDet.unapply='1' AND DATE_FORMAT(patPayDet.unapply_date, '%Y-%m-%d') >'".$en_date."')) ";
		}else{
			$preAppQry .= " AND patPayDet.deletePayment='0' ";
			$preAppQry .= " AND patPayDet.unapply='0' ";
		}
		$preAppRs=imw_query($preAppQry);
		while($preAppRes=imw_fetch_array($preAppRs)){
			$id = $preAppRes['id'];
			$oprId = $preAppRes['entered_by'];
			$unapply = $preAppRes['unapply'];
			$patId = $preAppRes['patient_id'];
			$paidDate = $preAppRes['paidDate'];
			$deletePayment = $preAppRes['deletePayment'];
			//$arrPrePayAppliedId[$id][] = $preAppRes['paidForProc'];
			$arrPatPayDetail[$patId][$paidDate][$oprId]['PAYMENT_ROW']['PrePay'][$id]['applied']+= $preAppRes['paidForProc'];
		}
	}
	unset($tempData);

	//GET ALL APPLIED CICO IDS
	$appliedCICOIds=array();
	$q = "SELECT acc_payment_id FROM check_in_out_payment_post WHERE status=0";
	$rs = imw_query($q);
	while($res = imw_fetch_array($rs)){
		$id = $res['acc_payment_id'];
		$appliedCICOIds[$id] = $id;
	}unset($rs);
	//------------------------
	
	$strApplyEids=implode(',',$arrApplyEids);
	// GET ACCOUNTING TRANSACTIONS
	$transQry = "SELECT patient_chargesheet_payment_info.facility_id, 
						patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId', 
						patient_charge_list.patient_id,
						patient_charge_list.encounter_id, 
						DATE_FORMAT(patient_charge_list.date_of_service, '".get_sql_date_format()."') as 'DOS', 		
					
						patient_chargesheet_payment_info.operatorId, 
						DATE_FORMAT(patient_chargesheet_payment_info.transaction_date, '".get_sql_date_format()."') as 'paidDate',
						patient_chargesheet_payment_info.paymentClaims,
						patient_chargesheet_payment_info.payment_id,
						patient_chargesheet_payment_info.transaction_date, patient_chargesheet_payment_info.payment_mode,  
						patient_chargesheet_payment_info.checkNo, patient_chargesheet_payment_info.creditCardNo, patient_chargesheet_payment_info.creditCardCo as cc_type,
						
						patient_charges_detail_payment_info.payment_details_id,
						patient_charges_detail_payment_info.charge_list_detail_id,
						patient_charges_detail_payment_info.paidForProc + patient_charges_detail_payment_info.overPayment as paidForProc,
						patient_charges_detail_payment_info.operator_id, 	
						patient_charges_detail_payment_info.patient_pre_payment_id,
						patient_charges_detail_payment_info.unapply, 
					
						patient_data.fname as 'pfname', 
						patient_data.mname as 'pmname',
						patient_data.lname as 'plname',
						
						users.fname, users.lname
						
				FROM patient_charge_list 
				LEFT JOIN patient_chargesheet_payment_info 
						  ON patient_chargesheet_payment_info.encounter_id = patient_charge_list.encounter_id
				LEFT JOIN patient_charges_detail_payment_info
						  ON patient_charges_detail_payment_info.payment_id = patient_chargesheet_payment_info.payment_id
				JOIN patient_data ON patient_data.id = patient_charge_list.patient_id 
				LEFT JOIN users ON users.id = patient_charge_list.primary_provider_id_for_reports 
				WHERE patient_data.id IS NOT NULL 
	";
	if($report_type== "WithDeleted"){
		$transQry .= " AND (patient_charges_detail_payment_info.deletePayment='0' 
							OR (patient_charges_detail_payment_info.deletePayment='1'
							AND patient_charges_detail_payment_info.deleteDate>'$en_date')) ";
	}else{
		$transQry .= " AND patient_charges_detail_payment_info.deletePayment='0' ";
	}
	if(empty($primaryProviderId) === false){
		$transQry .= " AND patient_charge_list.primary_provider_id_for_reports in($primaryProviderId)";
	}
	if(empty($facility_name_str) === false){
		$transQry .= " AND patient_chargesheet_payment_info.facility_id in($facility_name_str)";
	}
	if(empty($opr_name_str) === false){
		$transQry .= " AND patient_charges_detail_payment_info.operator_id in($opr_name_str)";
	}
	if(empty($grp_id) === false){
		$transQry .= " and patient_charge_list.gro_id in($grp_id)";
	}
	if(empty($pay_method) === false){
		$transQry .= " and LOWER(patient_chargesheet_payment_info.payment_mode)='".$pay_method."'";
	}
	
	if($DateRangeFor=='dot'){
		$transQry.=" AND (patient_chargesheet_payment_info.transaction_date between '$st_date' and '$en_date')";
	}else{
		$transQry.=" AND (patient_chargesheet_payment_info.date_of_payment between '$st_date' and '$en_date')";
	}
	
	$transQry .= " group by patient_charges_detail_payment_info.payment_details_id ORDER BY patient_chargesheet_payment_info.facility_id, users.lname, patient_chargesheet_payment_info.transaction_date";
	$transQryRes = imw_query($transQry);
	$postpayment_method_arr = array();
	$trans_query_res = array();
	while($row = imw_fetch_assoc($transQryRes)){
		$trans_query_res[] = $row;
	}
	
	$arrAppAmtInDate = array();
	$arrCioAppDateId = array();
	$arrPreAppDateId = array();
	
	for($i=0;$i<count($trans_query_res);$i++){
		$hasData=1;
		$facility=''; $posFacility=''; $check_no = ''; $payment_method = ''; 	$docNameArr= array();	$patNameArr= array();
		$facility = $trans_query_res[$i]['facility_id'];
		$doc_id = $trans_query_res[$i]['primaryProviderId'];
		$patId = $trans_query_res[$i]['patient_id'];
		$oprId=$trans_query_res[$i]['operator_id'];
		$encId=$trans_query_res[$i]['encounter_id'];
		$paidDate = $trans_query_res[$i]['paidDate'];
		$payment_id = $trans_query_res[$i]['payment_id'];
		$chgDetId = $trans_query_res[$i]['charge_list_detail_id'];
		$payDetId = $trans_query_res[$i]['payment_details_id'];
		
		$postpayArrVal = $trans_query_res[$i]['payment_mode'];
		$reference_no = (empty($trans_query_res[$i]['checkNo']) === false) ? $trans_query_res[$i]['checkNo'] : '';
		
		if( 'credit card' == strtolower($trans_query_res[$i]['payment_mode']) ){
			$trans_query_res[$i]['cc_type'] = $trans_query_res[$i]['cc_type'] ? 'CC-'.$trans_query_res[$i]['cc_type'] : 'Credit Card';
			$postpayArrVal = $trans_query_res[$i]['cc_type'];
			$reference_no=substr($trans_query_res[$i]['creditCardNo'], -4);
		}
		
		//-------BEGIN SUMMARY POSTED AMOUNT ---------------------------------------------------------
		if(($trans_query_res[$i]['patient_pre_payment_id']<=0 && !$arrCioAccId[$trans_query_res[$i]['payment_id']] && !$appliedCICOIds[$trans_query_res[$i]['payment_id']]) 
		   || ($trans_query_res[$i]['patient_pre_payment_id']>0 && $trans_query_res[$i]['unapply']=='1')){
			 
			$hasData = 1;
			$arrPostedEncDetIds[$encId]=$encId;
			$arrPostedChgDetIds[$chgDetId]=$chgDetId;
						
			$paidForProc= $trans_query_res[$i]['paidForProc'];			
			if($trans_query_res[$i]['paymentClaims'] == 'Negative Payment'){
				$paidForProc= '-'.$paidForProc;
			}
			$arrPayData[$doc_id][$facility]['posted']+= $paidForProc;
			$arrPatPayDetail[$patId][$paidDate][$oprId]['posted']+= $paidForProc;
			
			//$arrPatPayDetail[$patId][$paidDate][$oprId]['post_pay_mode'][$postpayArrVal]  += $paidForProc;
			
			//merging post payments 
			$payPostIncrVal = $paidForProc;
			$arrPatPayDetail[$patId][$paidDate][$oprId]['PAYMENT_ROW']['PostPay'][$payDetId]['method'] = $postpayArrVal;
			$arrPatPayDetail[$patId][$paidDate][$oprId]['PAYMENT_ROW']['PostPay'][$payDetId]['reference_no'] = $reference_no;
			$arrPatPayDetail[$patId][$paidDate][$oprId]['PAYMENT_ROW']['PostPay'][$payDetId]['pay'] = $payPostIncrVal;

			//PAYMENT BREAKDOWN
			$arrPayBreakdown[strtoupper($trans_query_res[$i]['payment_mode'])]+= $paidForProc;
			if( 'credit card' == strtolower($trans_query_res[$i]['payment_mode']) ){
				$arrCCPayBreakdown[strtoupper($trans_query_res[$i]['cc_type'])]+= $paidForProc;
			}
		}
		//-------END SUMMARY POSTED AMOUNT---------------------------------------------------------

		
		//GET PRE PAYMENTS OF SELECTED DATES
		if($trans_query_res[$i]['patient_pre_payment_id']>0 && $trans_query_res[$i]['unapply']=='0'){
			$arrPreAppDateId[$trans_query_res[$i]['patient_pre_payment_id']] += $trans_query_res[$i]['paidForProc'];
		}
		//GET CI/CO OF SELECTED DATES
		if($trans_query_res[$i]['patient_pre_payment_id']<=0){
			$arrCioAppDateId[$trans_query_res[$i]['payment_id']] += $trans_query_res[$i]['paidForProc'];
		}
	}
	
	if(sizeof($arrCioAppDateId)>0){
		$strCioAccDateId = implode(',', array_keys($arrCioAppDateId));
		$qry = "SELECT acc_payment_id, patient_id 
				FROM check_in_out_payment_post 
				WHERE acc_payment_id IN(".$strCioAccDateId.") 
					AND status='0'
				";		
		$rs = imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$t[$res['patient_id']]+=$arrCioAppDateId[$res['acc_payment_id']];
			$arrAppAmtInDate['cio_payment'] += $arrCioAppDateId[$res['acc_payment_id']];
		}
	}

	//GETTING CI/CO MANUALY APPLIED FOR DATE RANGE
	$qry = "SELECT manually_payment, check_in_out_payment_post.patient_id  
			FROM check_in_out_payment_post 
			JOIN check_in_out_payment ON check_in_out_payment.payment_id = check_in_out_payment_post.check_in_out_payment_id 
			WHERE (check_in_out_payment_post.manually_date BETWEEN '$st_date' and '$en_date') 
					AND manually_payment>0 AND status='0' AND del_status='0'";
	if(empty($opr_name_str) === false){
		$qry.= " AND check_in_out_payment.created_by IN($opr_name_str)";
	}
	if(empty($pay_method) === false){
		$qry .= " and LOWER(check_in_out_payment.payment_method)='".$pay_method."'";
	}
	$rs = imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAppAmtInDate['cio_payment'] += $res['manually_payment'];
		$arrManualAppliedAmts['cio_payment']+=$res['manually_payment'];
	}
	
	if(sizeof($arrPreAppDateId)>0){
		$strPreAppDateId=implode(',', array_keys($arrPreAppDateId));
		$qry = "SELECT id, patient_id  
				FROM patient_pre_payment 
				WHERE id IN(".$strPreAppDateId.") 
						AND del_status='0'";		
		$rs = imw_query($qry);
		while($res = imw_fetch_array($rs)){
			$arrAppAmtInDate['pre_payment'] += $arrPreAppDateId[$res['id']];
			$t[$res['patient_id']]+=$arrPreAppDateId[$res['id']];
		}
	}
	//GETTING PRE PAYMENTS MANUALY APPLIED FOR DATE RANGE
	$qry = "SELECT apply_amount, patient_id 
			FROM patient_pre_payment 
			WHERE (apply_payment_date BETWEEN '$st_date' and '$en_date') 
					AND apply_payment_type='manually' 
					AND del_status='0' 
					AND apply_amount>0
			";
	if(empty($opr_name_str) === false){
		$qry .= " AND entered_by in($opr_name_str)";
	}
	if(empty($pay_method) === false){
		$qry .= " AND LOWER(patient_pre_payment.payment_mode)='".$pay_method."'";
	}	
	$rs = imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAppAmtInDate['pre_payment'] += $res['apply_amount'];
		$arrManualAppliedAmts['pre_payment']+=$res['apply_amount'];
	}


	//GETTING POSTED REFUND AMOUNTS. PRE-PAY AND CI/CO REFUNDS FOR WHICH PAYMENTS WAS DONE BEFORE SELECTED DATE RANGE
	//REFUNDS OF POSTED PAYMENTS
	$qry = "SELECT crd.patient_id, crd.amountApplied, crd.cpt_code, crd.crAppliedToEncId, crd.charge_list_detail_id, 
	DATE_FORMAT(crd.dateApplied, '" . get_sql_date_format() . "') as dateApplied, crd.payment_mode, crd.operatorApplied, 
	DATE_FORMAT(patchg.date_of_service, '" . get_sql_date_format() . "') as date_of_service,
	pd.fname, pd.mname, pd.lname  
	FROM creditapplied crd JOIN patient_charge_list patchg ON patchg.encounter_id= crd.crAppliedToEncId 
	JOIN patient_data pd ON pd.id=crd.patient_id 
	WHERE LOWER(crd.crAppliedTo) = 'payment' AND (DATE_FORMAT(crd.entered_date, '%Y-%m-%d') BETWEEN '$st_date' and '$en_date') 
	AND (crd.delete_credit='0' OR (crd.delete_credit='1' AND DATE_FORMAT(crd.del_date_time, '%Y-%m-%d')>'$en_date'))";
	if(empty($primaryProviderId) === false){
		$qry .= " AND patchg.primary_provider_id_for_reports in($primaryProviderId)";
	}
	if(empty($facility_name_str) === false){
		$qry .= " AND crd.facility_id in($facility_name_str)";
	}
	if(empty($grp_id) === false){
		$qry .= " and patchg.gro_id in($grp_id)";
	}
	if(empty($pay_method) === false){
		$qry .= " and LOWER(crd.payment_mode)='".$pay_method."'";
	}
    if (empty($opr_name_str) === false) {
        $qry .= " AND crd.operatorApplied IN(" . $opr_name_str . ")";
    }
	$qry.=" ORDER BY pd.lname, pd.fname";
    $rs = imw_query($qry);
    while($res = imw_fetch_assoc($rs)) {
		$arrPostedRefundData[$res['crAppliedToEncId']][]=$res;
		
		//FOR SUMMARY
		$arrRefundAmounts[$res['operatorApplied']]['posted']+=$res['amountApplied'];
    }

	
    //GET REFUND FROM CI/CO AND PRE PAYMENTS
    $qry = "Select ciRef.id, ciRef.ci_co_id, ciRef.pmt_id, ciRef.ref_amt, ciRef.payment_method, ciRef.patient_id, 
	DATE_FORMAT(ciRef.entered_date, '" . get_sql_date_format() . "') as 'entered_date', ciRef.entered_by  
	FROM ci_pmt_ref ciRef WHERE (ciRef.entered_date BETWEEN '$st_date' AND '$en_date') 
	AND (ciRef.del_status='0' OR (ciRef.del_status='1' AND ciRef.del_date>'$en_date'))";
    if (empty($opr_name_str) === false) {
        $qry .= " AND ciRef.entered_by IN(".$opr_name_str.")";
    }
	if(empty($pay_method) === false){
		$qry .= " and LOWER(ciRef.payment_method)='".$pay_method."'";
	}
    $rs = imw_query($qry);
    while ($res = imw_fetch_array($rs)) {
        $id = $res['id'];
		$oprid=$res['entered_by'];
        
		if($res['ci_co_id'] > 0) {
			$tempCICOIds[$res['ci_co_id']] = $res['ci_co_id'];
			$arrCICORefundsDet[$res['ci_co_id']][$id]['ref_amt'] = $res['ref_amt'];
			$arrCICORefundsDet[$res['ci_co_id']][$id]['method'] = $res['payment_method'];
			$arrCICORefundsDet[$res['ci_co_id']][$id]['ref_date'] = $res['entered_date'];
			$arrCICORefundsDet[$res['ci_co_id']][$id]['entered_by'] = $res['entered_by'];
			
		} else if ($res['pmt_id'] > 0) {
			$tempPMTIds[$res['pmt_id']] = $res['pmt_id'];
			$arrPMTRefundsDet[$res['pmt_id']][$id]['ref_amt'] = $res['ref_amt'];
			$arrPMTRefundsDet[$res['pmt_id']][$id]['method'] = $res['payment_method'];
			$arrPMTRefundsDet[$res['pmt_id']][$id]['ref_date'] = $res['entered_date'];
			$arrPMTRefundsDet[$res['pmt_id']][$id]['entered_by'] = $res['entered_by'];
		}
    }

    // REFUND CI/CO RECORDS
    if (sizeof($tempCICOIds) > 0) {
        $strCICOIds = implode(',', $tempCICOIds);
        unset($tempCICOIds);

        $qry = "Select cicoDet.id, cicoDet.item_payment, cicoPay.patient_id, DATE_FORMAT(cicoPay.created_on, '" . get_sql_date_format() . "') as 'created_on', schAppt.sa_doctor_id, sa_facility_id,
		pd.lname, pd.fname, pd.mname  
		FROM check_in_out_payment_details cicoDet 
		LEFT JOIN check_in_out_payment cicoPay ON cicoPay.payment_id = cicoDet.payment_id 
		LEFT JOIN schedule_appointments schAppt ON schAppt.id = cicoPay.sch_id 
		LEFT JOIN patient_data pd ON pd.id = cicoPay.patient_id  
		WHERE cicoDet.id IN(". $strCICOIds.") AND cicoPay.created_on<'$st_date'";
        if (empty($primaryProviderId) == false) {
            $qry .= " AND schAppt.sa_doctor_id IN ($primaryProviderId)";
        }
        if (empty($facility_name_str) == false) {
            $qry .= " AND schAppt.sa_facility_id IN ($facility_name_str)";
        }
        $qry .= " ORDER BY pd.lname, pd.fname";
        $rs = imw_query($qry);
        while ($res = imw_fetch_array($rs)) {
            $printFile = true;
            $CICODetId = $res['id'];
            $pid = $res['patient_id'];
            $phyId = $res['sa_doctor_id'];
			$facId = ($arrPosFacAtFac[$res['sa_facility_id']]>0) ? $arrPosFacAtFac[$res['sa_facility_id']] : 0;

			$arrRefundOthers[$pid] = $pid;
			$arrCICORefunds[$pid][$CICODetId]['fname'] = $res['fname'];
			$arrCICORefunds[$pid][$CICODetId]['mname'] = $res['mname'];
			$arrCICORefunds[$pid][$CICODetId]['lname'] = $res['lname'];
			$arrCICORefunds[$pid][$CICODetId]['pay_date'] = $res['created_on'];
			$arrCICORefunds[$pid][$CICODetId]['pay_amt'] += $res['item_payment'];

			//FOR SUMMARY
			foreach($arrCICORefundsDet[$CICODetId] as $data){
				$opr_id=$data['entered_by'];
				$arrRefundAmounts[$opr_id]['cico']+= $data['ref_amt'];
			}
		}
        unset($rs);
    }

    // REFUND PRE-PAMENT RECORDS
    if (sizeof($tempPMTIds) > 0) {
        $tempIdCheck=array();
		$strPMTIds = implode(',', $tempPMTIds);
        unset($tempPMTIds);

        $qry = "Select patPrePay.id, patPrePay.patient_id, patPrePay.patient_id, patPrePay.facility_id, patPrePay.paid_amount,
		DATE_FORMAT(patPrePay.paid_date, '" . get_sql_date_format() . "') as 'paid_date', patPrePay.provider_id, 
		pd.lname, pd.fname, pd.mname FROM patient_pre_payment patPrePay 
		LEFT JOIN patient_data pd ON pd.id = patPrePay.patient_id 
		WHERE patPrePay.id IN(".$strPMTIds.") AND patPrePay.paid_date<'$st_date'";
        if (empty($primaryProviderId) == false) {
            $qry .= " AND patPrePay.provider_id IN ($primaryProviderId)";
        }
        if (empty($facility_name_str) == false) {
            $qry .= " AND patPrePay.facility_id IN ($facility_name_str)";
        }
        $qry .= " ORDER BY pd.lname, pd.fname";
        $rs = imw_query($qry);

        while ($res = imw_fetch_array($rs)) {
            $printFile = true;
            $pmtId = $res['id'];
            $pid = $res['patient_id'];
            $phyId = $res['provider_id'];
			$facId = ($arrPosFacAtFac[$res['facility_id']]>0) ? $arrPosFacAtFac[$res['facility_id']] : 0;

			$arrRefundOthers[$pid] = $pid;
			$arrPMTRefunds[$pid][$pmtId]['fname'] = $res['fname'];
			$arrPMTRefunds[$pid][$pmtId]['mname'] = $res['mname'];
			$arrPMTRefunds[$pid][$pmtId]['lname'] = $res['lname'];
			$arrPMTRefunds[$pid][$pmtId]['pay_date'] = $res['paid_date'];
			$arrPMTRefunds[$pid][$pmtId]['pay_amt'] += $res['paid_amount'];

			//FOR SUMMARY
			foreach($arrPMTRefundsDet[$pmtId] as $data){
				$opr_id=$data['entered_by'];
				$arrRefundAmounts[$opr_id]['pre_paid']+= $data['ref_amt'];
			}
        }
        unset($rs);
    }	


if($DateRangeFor=='dor' && $not_display_deleted_block==''){
	//--- GET DELETED POSTED PAYMENT ----------------
	if(sizeof($arrPostedEncDetIds)>0){
		$strPostedChgDetIds=implode(',', $arrPostedChgDetIds);
		$strPostedEncDetIds=implode(',', $arrPostedEncDetIds);
	
		$qry = "SELECT patChg.patient_id, 
		payChg.facility_id, 
		patChg.primary_provider_id_for_reports as 'primaryProviderId', payChg.creditCardCo,
		DATE_FORMAT(patChg.date_of_service, '".get_sql_date_format()."') as 'date_of_service', patChg.del_status,
		payChg.encounter_id, 
		payChgDet.charge_list_detail_id, 
		payChg.transaction_date, 
		payChg.payment_mode, 
		payChgDet.paidBy, 
		payChgDet.paidForProc, 
		payChgDet.overPayment, 
		payChgDet.operator_id, 
		payChgDet.deletePayment, payChgDet.deleteDate,
		payChg.paymentClaims, 
		pd.fname, 
		pd.mname, 
		pd.lname  
		FROM patient_chargesheet_payment_info payChg 
		JOIN patient_charges_detail_payment_info payChgDet ON payChgDet.payment_id = payChg.payment_id 
		JOIN patient_charge_list patChg ON patChg.encounter_id = payChg.encounter_id 
		JOIN patient_data pd ON pd.id = patChg.patient_id 
		LEFT JOIN pos_facilityies_tbl ON pos_facilityies_tbl.pos_facility_id = patChg.facility_id 
		JOIN users ON users.id= patChg.primary_provider_id_for_reports   
		WHERE 1=1 AND pd.id IS NOT NULL ";
		if(empty($primaryProviderId) === false){
			$qry .= " AND patChg.primary_provider_id_for_reports in($primaryProviderId)";
		}
		if(empty($facility_name_str) === false){
			$qry .= " AND payChg.facility_id in($facility_name_str)";
		}
		if(empty($opr_name_str) === false){
			$qry .= " AND payChgDet.del_operator_id in($opr_name_str)";
		}
		if(empty($grp_id) === false){
			$qry .= " AND patChg.gro_id in($grp_id)";
		}
		if(empty($pay_method) === false){
			$qry .= " AND LOWER(payChg.payment_mode)='".$pay_method."'";
		}
		if(empty($strPostedChgDetIds)==false){
			$qry.=" AND IF(payChgDet.charge_list_detail_id>0, payChgDet.charge_list_detail_id IN(".$strPostedChgDetIds."), payChg.encounter_id IN(".$strPostedEncDetIds.")) 
			AND payChgDet.deletePayment='1' AND deleteDate>'$en_date' AND (payChg.date_of_payment between '$st_date' and '$en_date')";
		}
		if(empty($pay_method)==false){ //PAYMENT MODE
			$qry.=" AND LOWER(payChg.payment_mode)='".$pay_method."'";
		}
		$qry.= " ORDER BY users.lname, payChg.facility_id, payChg.transaction_date";

		$rs = imw_query($qry)or die(imw_error());
		$tempDelPostedPay=array();
		$arrDelTemp=array();
		while($res = imw_fetch_array($rs)){
			$eid = $res['encounter_id'];
			$printFile=true;
			$paidAmt=0;
			$pid = $res['patient_id'];
			$chgDetId = $res['charge_list_detail_id'];
			$paidBy = strtolower($res['paidBy']);
			$payMode = strtolower($res['payment_mode']);
			$payMode = str_replace(' ', '_', $payMode);
			$phyId = $res['primaryProviderId'];
			$facId = $res['facility_id'];
			$oprId = $res['operator_id'];
			$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
			$encounterIdDelArr[$eid] = $eid;
			$grpId = $phyId;
			
			$paidAmt = $res['paidForProc'] + $res['overPayment'];
			if($res['paymentClaims'] == 'Negative Payment'){
				$paidAmt= '-'.$paidAmt;
			}
			
			if($Process=='summary'){
				$arrDelAmounts[$grpId]['posted']+= $paidAmt; 
			}else{
				$arrDelPostedAmounts[$grpId][$eid]['pat_name']=$patName;
				$arrDelPostedAmounts[$grpId][$eid]['eid']=$eid;
				$arrDelPostedAmounts[$grpId][$eid]['dos']=$res['date_of_service'];
				$arrDelPostedAmounts[$grpId][$eid]['del_amount']+= $paidAmt;
			}
		} 
		unset($rs);
	}
	//END DELETED PRE-PAYMENTS

	//GET DELETED CI/CO
	$arrDelCICONotApplied=array();
	$tempDelCCTypeAmts=array();
	$arrDelCICOAmounts=array();
	
	$cioJoin='';
	$cioOrderBy='ORDER BY pd.lname, pd.fname';
	if($groupBy=='physician'){
		$cioJoin=" LEFT JOIN users ON users.id = sa.sa_doctor_id";
	}
	if($groupBy=='operator' || $groupBy=='department'){
		$cioJoin=" LEFT JOIN users ON users.id = cioPay.created_by";
	}
	if($groupBy=='physician' || $groupBy=='operator' || $groupBy=='department'){
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
	cioPayDet.delete_date,	
	pd.fname, pd.mname, pd.lname 
	FROM schedule_appointments sa 
	JOIN check_in_out_payment cioPay ON cioPay.sch_id  = sa.id 
	JOIN check_in_out_payment_details cioPayDet ON cioPayDet.payment_id= cioPay.payment_id
	".$cioJoin."
	JOIN patient_data pd ON pd.id = cioPay.patient_id 
	WHERE cioPayDet.item_payment>0 AND pd.id IS NOT NULL ";
	$qry.=" AND cioPayDet.status=1 AND cioPayDet.delete_date>'$en_date' AND (DATE_FORMAT(cioPay.created_on, '%Y-%m-%d') BETWEEN '".$st_date."' AND '".$en_date."')";
	if(empty($facility_name_str) ===false){
		$qry.= " AND sa.sa_facility_id IN(".$facility_name_str.")";
	}
	if(empty($primaryProviderId) === false){
		$qry.= " AND sa.sa_doctor_id IN(".$primaryProviderId.")";
	}
	if(empty($opr_name_str) === false){
		$qry.= " AND cioPay.created_by IN(".$opr_name_str.")";
	}
	if(empty($pay_method)==false){ //PAYMENT MODE
		$qry.=" AND LOWER(cioPay.payment_method)='".$pay_method."'";
	}
	if(empty($cc_type)==false){ //IF CREDIT CARD
		$qry.=" AND LOWER(cioPay.cc_type) IN(".$cc_type.")";
	}
	
	$qry.= $cioOrderBy;
	$rs=imw_query($qry)or die(imw_error());
	while($res=imw_fetch_array($rs)){
		$printFile=true;
		$pid = $res['patient_id'];
		$payment_id = $res['payment_id'];
		$phyId = $res['sa_doctor_id'];
		$facility = $res['sa_facility_id'];
		$oprId = $res['created_by'];
		$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
		if($facility=='' || $facility<=0) { $facility=0; }
		
		$grpId = $phyId;
		if($groupBy=='operator'){ $grpId = $oprId;	}
		if($Process=='summary'){
			$arrDelAmounts[$grpId]['cico']+=$res['item_payment'];
		}else{
			$arrDelCICOAmounts[$grpId][$payment_id]['pat_name']=$patName;
			$arrDelCICOAmounts[$grpId][$payment_id]['payment_id']=$payment_id;
			$arrDelCICOAmounts[$grpId][$payment_id]['paid_date']=$res['created_on'];
			$arrDelCICOAmounts[$grpId][$payment_id]['del_amount']+=$res['item_payment'];
			
		}
	}
	//END DELETED CI/CO	
	
	// GET DELETED PATIENT PRE PAYMENTS
	$groupArr=array();
	$tempCCTypeAmts=array();
	$patQryRes = array();
	$qry="Select pDep.id, pDep.patient_id, pDep.paid_amount, pDep.facility_id, 
	pDep.apply_payment_date, pData.default_facility, pDep.apply_payment_type, pDep.apply_amount, pDep.provider_id,
	DATE_FORMAT(pDep.entered_date, '".get_sql_date_format()."') as 'entered_date', DATE_FORMAT(pDep.paid_date, '".get_sql_date_format()."') as 'paid_date',
	pDep.entered_by, pDep.payment_mode, pDep.credit_card_co,
	pData.fname, pData.mname, pData.lname 
	FROM patient_pre_payment pDep 
	JOIN patient_data pData ON pData.id = pDep.patient_id 
	WHERE 1=1 AND pData.id IS NOT NULL ";
	$qry.=" AND pDep.del_status='1' AND pDep.trans_del_date>'$en_date' AND (DATE_FORMAT(pDep.paid_date, '%Y-%m-%d') BETWEEN '".$st_date."' AND '".$en_date."')";
	if(empty($primaryProviderId) === false){
		$qry.= " AND pDep.provider_id IN(".$primaryProviderId.")";
	}
	if(empty($facility_name_str) ===false){
		$qry .= " AND pDep.facility_id in($facility_name_str)";
	}
	if(empty($opr_name_str) === false){
		$qry .= " AND pDep.entered_by in($opr_name_str)";
	}
	if(empty($pay_method)==false){ //PAYMENT MODE
		$qry.=" AND LOWER(pDep.payment_mode)='".$pay_method."'";
	}
	if(empty($cc_type)==false){ //IF CREDIT CARD
		$qry.=" AND LOWER(pDep.credit_card_co) IN(".$cc_type.")";
	}
	$qry.=" ORDER BY pData.lname, pData.fname";
	$patQry = imw_query($qry);
	while($row = imw_fetch_assoc($patQry)){	$patQryRes[] = $row; }
	$arrDepIds=array();
	$tempData=array(); $arrDepIds=array(); $arrAllIds=array(); $arrAllIdsData=array();
	
	for($i=0; $i<sizeof($patQryRes); $i++){
		$printFile=true;
		$facility=0; $balance_amount=0; $doc_id=0;$refundAmt=0;
		$pid = $patQryRes[$i]['patient_id'];
		$facility=$patQryRes[$i]['facility_id'];
		$phyId=$patQryRes[$i]['provider_id'];
		$oprId=$patQryRes[$i]['entered_by'];
		$patName = $pid.'~'.$patQryRes[$i]['fname'].'~'.$patQryRes[$i]['mname'].'~'.$patQryRes[$i]['lname'];
		//if($facility<=0 || $facility==''){ $facility=$headPosFacility; }
		$grpId= $oprId;
		if($grpId<=0){ $grpId=0; } 
		
		$id= $patQryRes[$i]['id'];
		$date = $patQryRes[$i]['paid_date'];
		if($Process=='summary'){
			$arrDelAmounts[$grpId]['pre_payment']+= $patQryRes[$i]['paid_amount'];
		}else{
			$arrDelPrePayAmounts[$grpId][$id]['pat_name']=$patName;
			$arrDelPrePayAmounts[$grpId][$id]['entered_date']=$date;
			$arrDelPrePayAmounts[$grpId][$id]['del_amount']+=$patQryRes[$i]['paid_amount'];
		}
	}

}
	
	//---BEGIN GET PATIENT ARRAY-----------------------------------------------------
	$arrPatientId = array_keys($arrPatPayDetail);
	$qry = "SELECT id,
				CONCAT(patient_data.lname,', ',patient_data.fname, ' - ',id) AS name FROM patient_data 
				WHERE patient_data.id IN(".implode(",",array_unique($arrPatientId)).") ORDER BY patient_data.lname, patient_data.fname, patient_data.mname";
	$qryres = imw_query($qry);
	$arrPatient = array();
	while($row = imw_fetch_assoc($qryres)){
		$arrPatient[$row['id']] = $row['name'];
		$arrAlphaSort[$row['id']]=$row['id'];
		$arrPatPayDetail[$row['id']]= $arrPatPayDetail[$row['id']];
	}
	
	//ALPHABETICAL SORTING
	$arrPatPayTemp= $arrPatPayDetail;
	unset($arrPatPayDetail);	
	foreach($arrAlphaSort as $pid){
		foreach($arrPatPayTemp[$pid] as $paidDate => $operData){
			foreach($operData as $operId => $recordInfo){
				if(isset($recordInfo['cio_payment'])){
					$arrPatPayDetail[$pid][$paidDate][$operId]['cio_payment_method']=$recordInfo['cio_payment_method'];
					$arrPatPayDetail[$pid][$paidDate][$operId]['cio_payment']=$recordInfo['cio_payment'];
					$arrPatPayDetail[$pid][$paidDate][$operId]['cio_payment_ref']=$recordInfo['cio_payment_ref'];
					$arrPatPayDetail[$pid][$paidDate][$operId]['cio_item_id']=$recordInfo['cio_item_id'];
					
					foreach($recordInfo['PAYMENT_ROW']['CheckIn'] as $id =>$arrDetail){
						$arrPatPayDetail[$pid][$paidDate][$operId]['PAYMENT_ROW']['CheckIn'][$id]['method']=$arrDetail['method'];
						$arrPatPayDetail[$pid][$paidDate][$operId]['PAYMENT_ROW']['CheckIn'][$id]['reference_no']=$arrDetail['reference_no'];
						$arrPatPayDetail[$pid][$paidDate][$operId]['PAYMENT_ROW']['CheckIn'][$id]['pay']=$arrDetail['pay'];
						$arrPatPayDetail[$pid][$paidDate][$operId]['PAYMENT_ROW']['CheckIn'][$id]['applied']=$arrDetail['applied'];
					}

					$arrPatPayDetail[$pid][$paidDate][$operId]['cio_applied']=$recordInfo['cio_applied'];
				}
				if(isset($recordInfo['pre_payment'])){
					$arrPatPayDetail[$pid][$paidDate][$operId]['pre_payment_method']=$recordInfo['pre_payment_method'];
					$arrPatPayDetail[$pid][$paidDate][$operId]['pre_payment']=$recordInfo['pre_payment'];
					$arrPatPayDetail[$pid][$paidDate][$operId]['pre_payment_ref']=$recordInfo['pre_payment_ref'];
					$arrPatPayDetail[$pid][$paidDate][$operId]['pre_pay_id']=$recordInfo['pre_pay_id'];
					
					foreach($recordInfo['PAYMENT_ROW']['PrePay'] as $id =>$arrDetail){
						$arrPatPayDetail[$pid][$paidDate][$operId]['PAYMENT_ROW']['PrePay'][$id]['method']=$arrDetail['method'];
						$arrPatPayDetail[$pid][$paidDate][$operId]['PAYMENT_ROW']['PrePay'][$id]['reference_no']=$arrDetail['reference_no'];
						$arrPatPayDetail[$pid][$paidDate][$operId]['PAYMENT_ROW']['PrePay'][$id]['pay']=$arrDetail['pay'];
						$arrPatPayDetail[$pid][$paidDate][$operId]['PAYMENT_ROW']['PrePay'][$id]['applied']=$arrDetail['applied'];
					}

					$arrPatPayDetail[$pid][$paidDate][$operId]['pre_applied']=$recordInfo['pre_applied'];
				}
				if(isset($recordInfo['posted'])){
					
					$arrPatPayDetail[$pid][$paidDate][$operId]['post_payment_method']=$recordInfo['post_payment_method'];
					$arrPatPayDetail[$pid][$paidDate][$operId]['posted']=$recordInfo['posted'];

					foreach($recordInfo['PAYMENT_ROW']['PostPay'] as $id => $arrDetail){
						$arrPatPayDetail[$pid][$paidDate][$operId]['PAYMENT_ROW']['PostPay'][$id]['method']=$arrDetail['method'];
						$arrPatPayDetail[$pid][$paidDate][$operId]['PAYMENT_ROW']['PostPay'][$id]['reference_no']=$arrDetail['reference_no'];
						$arrPatPayDetail[$pid][$paidDate][$operId]['PAYMENT_ROW']['PostPay'][$id]['pay']=$arrDetail['pay'];
					}
				}
			}
		}
	}
	
	
	//---END GET PATIENT ARRAY-----------------------------------------------------
	
	//---------------- END FETCHING DATA----
	
	if($Process=='summary'){
		unset($complete_page_data);
		include_once('daily_balance_summary.php');
		$strHTML1 = $strHTML = $complete_page_data;
	}else{
		unset($complete_page_data);
		include_once('daily_balance_detail.php');
		$strHTML1 = $strHTML = $complete_page_data;
	}
	
	//--- CREATE PDF FILE FOR PRINTING -----
	if($printFile == true and $complete_page_data != '')
	{
		$strHTML1 = "<style>".file_get_contents('css/reports_pdf.css')."</style>";
		$strHTML1.=<<<DATA
			<page backtop="10mm" backbottom="10mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
			$pdf_header
			</page_header>
			$main_block_pdf
			$page_data
			$manually_applied_cico_html
			$manually_applied_pre_pay_html
			$manually_applied_html
			</page>
DATA;
	//	$objManageData->Smarty->assign("showBtn",true);	
	$conditionChk = true;
	}
}


$tooltip='Red coloured CI/CO and Pre-Payments represents that there is refund amount deducted from these payments.';
	
if($strHTML1)
{
$strHTML1 .= '<table width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8" >
	<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
	<tr><td style="width:20px;" class="info" style="background-color:#FFFFFF;">&nbsp;</td>
	<td style="width:4px;" height="5px;" bgcolor="#FF0000"></td>
	<td class="info" style="padding-left:20px; background-color:#FFFFFF;">
	'.$tooltip.'
	</td>
	</tr>
	</table>';
}
$op = 'l';

if($complete_page_data)
{
$complete_page_data .= '<table width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8">
		<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
		<tr><td style="width:20px;" style="background-color:#FFFFFF;">&nbsp;</td>
		<td style="width:4px;" height="5px;" bgcolor="#FF0000"></td>
		<td class="info" style="padding-left:20px; ; background-color:#FFFFFF;">
		'.$tooltip.'<br/>Refund amount can be view by mouse over on red coloured amount.
		</td>
		</tr>
		</table>';
}

$HTMLCreated=0;
if($printFile == true and $complete_page_data != ''){
	$HTMLCreated=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$complete_page_data;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$strHTML1;

	$file_location = write_html($strHTML,'daily_balance.html');
}else{
	if($callFrom != 'scheduled'){
		$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}

if($output_option=='view' || $output_option=='output_csv'){
	if($callFrom != 'scheduled'){
		echo $csv_file_data;	
	}
}

//--- SET ROOT PATH ---
//$objManageData->Smarty->assign("WEB_ROOT",$GLOBALS["REPORTS_PDF_FOLDER"]);
//$objManageData->Smarty->assign("printFile",$printFile);

//$html_file_name = 'print_pdf_file_'.$_SESSION['authId'];
/* if($callFrom != 'scheduled'){
	$html_file_name = get_pdf_name($_SESSION['authId'],'daily_balance');
	file_put_contents('new_html2pdf/'.$html_file_name.'.html',$strHTML1);
}

$objManageData->Smarty->assign('page_data',$complete_page_data);
$objManageData->Smarty->assign("html_file_name",$html_file_name);	

if($callFrom == 'scheduled'){
	if($strHTML1 != ""){
		$op='p';
		$page_html_script = $complete_page_data;
		$html_file_name = get_scheduled_pdf_name('daily_balance', '../common/new_html2pdf');
		file_put_contents('../common/new_html2pdf/'.$html_file_name.'.html',$strHTML1);
	}
}else{
	$objManageData->Smarty->display(dirname(__FILE__).'/template/daily_balance_result.tpl');
} */
?>