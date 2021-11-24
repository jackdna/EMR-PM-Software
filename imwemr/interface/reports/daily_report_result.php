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

$sel_grp = $CLSReports->report_display_selected($grp_id,'group',1,$grp_cnt);
$sel_fac = $CLSReports->report_display_selected($facility_name,'facility',1,$fac_cnt);
$sel_phy = $CLSReports->report_display_selected($phyId,'physician',1,$pro_cnt);
$sel_opr = $CLSReports->report_display_selected($opr_name,'operator',1,$opr_cnt);

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

$group_query = "select name from groups_new";
	$groupQryRs = imw_query($group_query);		
	$groupQryRes = imw_fetch_assoc($groupQryRs);
	if(count($groupQryRes)>0){
	$group_name = $groupQryRes[0]['name'];
	}

	
//--- GET ALL GROUP NAME ----
$providerRs = imw_query("Select gro_id, name from groups_new");
$groupNameArr = array();
while($groupResArr = imw_fetch_assoc($providerRs)){
	$id = $groupResArr['gro_id'];
	$name = $groupResArr['name'];
	$groupNameArr[$id] = $name;
}	

//ALL POS FACILITIES
$arrAllPosFacilities=array();
if($_REQUEST['registered_fac']==1){
	$arrAllFacilities[0] = 'No Facility';
	
	$qry = "Select pos_facilityies_tbl.facilityPracCode as name,
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
		$arrAllPosFacilities[$id] = $name.' - '.$pos_prac_code;
	}
}

//ALL FACILITY
	$arrFacOfPracCode=array();
	$arrAllFacilities=array();
	$arrSelPosFacilities=array();
	$qry = "Select id, name, fac_prac_code FROM facility";
	$qryRs = imw_query($qry);
	while($qryRes  =imw_fetch_assoc($qryRs)){
		$id = $qryRes['id'];
		$name = $qryRes['name'];
		$arrAllFacilities[$id] = $name;
		
		//IF FACILITY SELECTED IN SEARCH CRITERIA
		if(empty($facility_name_str)==false && $facility_name[$id]){
			$arrSelPosFacilities[$qryRes['fac_prac_code']]=$qryRes['fac_prac_code'];
		}
	}
	if(sizeof($arrSelPosFacilities)>0){
		$strSelPosFacilities=implode(',', $arrSelPosFacilities);
	}

$Process =  strtolower($summary_detail);
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
	$formatDelDate='%Y-%m-%d';
	$delDate=$endDate;
	
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
		
		$hourFromAmPm=$hourFromL.':00:00 '.strtoupper($ampmFrom);
		$hourToAmPm=$hourToL.':00:00 '.strtoupper($ampmTo);

		$hourFromL.=$ampmFrom;
		$hourToL.=$ampmTo;
		
		$hourFrom=$hourFrom.':00:00';
		$hourTo=$hourTo.':00:00';

		$formatDelDate='%Y-%m-%d %H:%i:%s';
		$delDate=$endDate.' '.$hourTo;
	}	
	
		// Collecting Insurance Companies and groups
	if(empty($ins_carriers)==false){ $tempInsArr[] = implode(',',$ins_carriers); }
	if(empty($insuranceGrp)==false){ $tempInsArr[] = implode(',',$insuranceGrp); }
	$tempSelIns = implode(',', $tempInsArr);
	$tempInsArr = array();
	if(empty($tempSelIns)==false){
	$tempInsArr = explode(',', $tempSelIns);
	}
	$tempInsArr = array_unique($tempInsArr);
	$insuranceName  = implode(',', $tempInsArr);
	$arrInsurance=array();
	if(sizeof($tempInsArr)>0){
		$arrInsurance = array_combine($tempInsArr,$tempInsArr);
	} 
	unset($tempInsArr);
	$insCompanies = '';
	if( is_array($arrInsurance) && count($arrInsurance) > 0){
		$insCompanies = implode(',',$arrInsurance);
	}
	
	
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
	$rqArrPhyId = $_REQUEST['phyId'];
	$providerID = join(',',$rqArrPhyId);
	
	//operator_id
	$rqArrOprId = $_REQUEST['operator_id'];
	$opr_name = join(',',$rqArrOprId);
	
	$groupId = $_REQUEST['groups'];
	$grp_id = join(',',$groupId);
}

$page_data = '';	$printFile= true;



if(empty($Submit) === false){

	$printFile = false;
	
	$st_date = getDateFormatDB($Start_date);
	$en_date = getDateFormatDB($End_date);
	
	$primaryProviderId = $providerID;
	$facility_name_str = $facility_name;
	$opr_name_str = $opr_name;

	// GET ALL USERS
	$rs=imw_query("Select id,fname,lname FROM users");
	while($res=imw_fetch_array($rs)){
		$usrNameArr=array();
		$usrNameArr["LAST_NAME"] = $res['lname'];
		$usrNameArr["FIRST_NAME"] = $res['fname'];
		$usrName = changeNameFormat($usrNameArr);
		$arrAllUsers[$res['id']]=$usrName;
	}
	
	//getting report generator name
	$report_generator_name = NULL;
	if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
		$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
		$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
		$report_generator_name = strtoupper($report_generator_name);
	}
	if(empty($facility_name_str)==false){ 
		$schfacPart = " WHERE fac_prac_code IN($facility_name_str)"; 
		$posfacPart = " WHERE pos_facility_id IN($facility_name_str)"; 
	}	
	// GET SELECTED SCHEDULER FACILITIES
	$sch_fac_id_str='';
	$fac_query = imw_query("Select id,name,fac_prac_code from facility $schfacPart");		
	$sch_fac_id_arr = array();
	while($fac_query_res=imw_fetch_array($fac_query)){
		$fac_id = $fac_query_res['id'];
		$sch_fac_id_arr[$fac_id] = $fac_id;
		$sch_pos_fac_arr[$fac_id] = $fac_query_res['fac_prac_code'];
	}
	$sch_fac_id_str = implode(',',$sch_fac_id_arr);
	// GET ALL FACILITIES
	$allFacArr=array();
	$qry = "select pos_facilityies_tbl.facilityPracCode as name,
				pos_facilityies_tbl.pos_facility_id as id,
				pos_tbl.pos_prac_code
				from pos_facilityies_tbl 
				LEFT JOIN pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
				$posfacPart";
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
	cioPay.created_by, cioPay.payment_method, 
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
	us.lname,
	facility.default_group as groupId	
	FROM schedule_appointments sa 
	LEFT JOIN check_in_out_payment cioPay ON cioPay.sch_id  = sa.id 
	LEFT JOIN check_in_out_payment_details cioDet ON cioDet.payment_id  = cioPay.payment_id  
	LEFT JOIN users us on us.id = sa.sa_doctor_id 
	LEFT JOIN patient_data pd ON pd.id = sa.sa_patient_id
	LEFT JOIN facility ON sa.sa_facility_id = facility.id 
	WHERE (cioPay.created_on between '".$st_date."' and '".$en_date."') 
	AND (cioPay.del_status='0' OR (cioPay.del_status='1' && cioPay.delete_date>'".$en_date."')) 
	AND (cioDet.status='0' OR (cioDet.status ='1' && cioDet.delete_date >'".$en_date."')) 
	AND cioPay.total_payment>0 AND pd.id IS NOT NULL";
	
	if(empty($primaryProviderId) === false){
		$sch_query .= " and sa.sa_doctor_id in($primaryProviderId)";
	}
	if(empty($facility_name_str) === false){
		$sch_query .= " and sa.sa_facility_id in($sch_fac_id_str)";
	}
	if(empty($opr_name_str) === false){
		$sch_query .= " and cioPay.created_by in($opr_name_str)";
	}
	if(empty($grp_id) === false){
		$sch_query .= " and facility.default_group in($grp_id)";
	}
	$sch_query .= " ORDER BY sa.sa_facility_id, us.lname, cioPay.created_on";
	$rs=imw_query($sch_query);

	$arrTempDetIds=array();
	$arrApplyEids=array();
	$arrPayData = array();
	$arrCioAccId = array();
	$arrCIODetId = array();
	$arrPatPayDetail = array();
	
	while($res=imw_fetch_array($rs)){	
		$hasData=1;
		$facility='';	$docNameArr= array();	$patNameArr= array();
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
		
		$sch_facility = $res['sa_facility_id'];
		$facility = $sch_pos_fac_arr[$sch_facility];
		if($facility=='') { $facility=0; }

		$doc_id = $res['sa_doctor_id'];
		$patId = $res['sa_patient_id'];
		$itemId=$res['item_id'];
		$oprId = $res['created_by'];
		$groupId = $res['groupId'];

		
		$firstGrpBy = $sa_doctor_id;
		if($grpby_block=='grpby_facility'){
			$firstGrpBy= $sa_facility_id;
		}
		if($grpby_block=='grpby_operators'){
			$firstGrpBy= $oprId;
		}
		if($grpby_block=='grpby_groups'){
			$firstGrpBy= $groupId;
		}	
		
		$hasData = 1;
		if(!$arrCIODetId[$cioDetId]){
			$arrPayData[$doc_id][$facility]['payment'] += $res['cioPayment']-$refundAmt;
			$arrPayData[$doc_id][$facility]['payment_ref'] += $refundAmt;
			//----DETAIL ARRAY------
			$arrPatPayDetail[$firstGrpBy][$paidDate][$oprId]['cio_payment'] += $res['cioPayment']-$refundAmt;
			$arrPatPayDetail[$firstGrpBy][$paidDate][$oprId]['cio_payment_ref'] += $refundAmt;
			$arrPatPayDetail[$firstGrpBy][$paidDate][$oprId]['cio_item_id'] = $itemId;
			$arrPatPayDetail[$firstGrpBy][$paidDate][$oprId]['pt_id'] = $patId;
			//------------------------
			$arrCIODetId[$cioDetId] = $cioDetId;
			$arrTempCIO[$cioDetId]['doctor']=$doc_id;
			$arrTempCIO[$cioDetId]['facility']=$facility;
			$arrTempCIO[$cioDetId]['pat_id']=$patId;
			$arrTempCIO[$cioDetId]['paid_date']=$paidDate;
			$arrTempCIO[$cioDetId]['opr_id']=$oprId;
			$arrTempCIO[$cioDetId]['payment_id']=$payment_id;
			$arrTempCIO[$cioDetId]['groupId']=$groupId;
			
			//PAYMENT BREAKDOWN
			$arrPayBreakdown[strtoupper($res['payment_method'])]+= $res['cioPayment']-$refundAmt;
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
						if($hourFrom!='' && $hourTo!=''){
							$qry.= " AND patPayDet.paid_time BETWEEN '$hourFrom' AND '$hourTo'";					
						}
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
				$groupId   = $arrTempCIO[$cioDetId]['groupId'];
				
				$firstGrpBy = $doc_id;
				if($grpby_block=='grpby_facility'){
					$firstGrpBy= $facility;
				}
				if($grpby_block=='grpby_operators'){
					$firstGrpBy= $oprId;
				}
				if($grpby_block=='grpby_groups'){
					$firstGrpBy= $groupId;
				}
				
				//FOR SUMMARY
				$arrPayData[$doc_id][$facility]['applied']+= $tempCIOArr[$cioDetId]['manual_applied'] + $tempCIOArr[$cioDetId]['encounter_applied'];
				//FOR DETAIL
				$arrPatPayDetail[$firstGrpBy][$paidDate][$oprId]['cio_applied']+= $tempCIOArr[$cioDetId]['manual_applied'] + $tempCIOArr[$cioDetId]['encounter_applied'];
				$arrPatPayDetail[$firstGrpBy][$paidDate][$oprId]['pt_id'] = $patId;

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
	DATE_FORMAT(pDep.entered_date, '".get_sql_date_format()."') as 'paidDate', 
	pDep.entered_by, 
	pDep.apply_payment_type, 
	pDep.apply_amount, 
	pDep.del_status, 
	pDep.del_operator_id, pDep.payment_mode, pDep.provider_id,
	DATE_FORMAT(pDep.trans_del_date, '".get_sql_date_format()."') as 'delDate',
	
	pData.default_facility, 
	pData.fname as 'pfname', 
	pData.mname as 'pmname', 
	pData.lname as 'plname' 
	FROM patient_pre_payment pDep 
	LEFT JOIN patient_data pData ON pData.id = pDep.patient_id 
	WHERE (pDep.entered_date BETWEEN '$st_date' AND '$en_date')  AND pData.id IS NOT NULL 
	AND (pDep.del_status='0' OR (pDep.del_status='1' AND DATE_FORMAT(pDep.trans_del_date, '%Y-%m-%d')>'$en_date'))";
	if(empty($sch_fac_id_str)==false){
		$patQry .= " AND pDep.facility_id in($sch_fac_id_str)";
	}
	if(empty($opr_name_str) === false){
		$patQry .= " AND pDep.entered_by in($opr_name_str)";
	}
	if($hourFrom!='' && $hourTo!=''){
		$patQry.= " AND pDep.sa_app_starttime BETWEEN '$hourFrom' AND '$hourTo'";					
	}
	
	$patQry .= " ORDER BY pDep.entered_date";
	$qryRes = imw_query($patQry);
	while($row = imw_fetch_assoc($qryRes)){
		$patQryRes[] = $row;
	}
	
	$arrDepIds=array();
	$arrPrePayData = array();
	$arrPrePayDetail = array();
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
		$facility='';	$docNameArr= array();	$patNameArr= array();
		$patId = $patQryRes[$i]['patient_id'];
		$oprId = $patQryRes[$i]['entered_by'];
		$id= $patQryRes[$i]['id'];
		$paidDate = $patQryRes[$i]['paidDate'];
		
		$hasData = 1;
		$arrPrePayData[$oprId]['payment'] += $patQryRes[$i]['paid_amount']-$refundAmt;
		$arrPrePayData[$oprId]['payment_ref'] += $refundAmt;
		
		$arrPrePayDetail[$patId]['payment'] += $patQryRes[$i]['paid_amount']-$refundAmt;
		$arrPrePayDetail[$patId]['payment_ref'] += $refundAmt;
		
		$firstGrpBy = $doc_id;
		if($grpby_block=='grpby_facility'){
			$firstGrpBy= $facility;
		}
		if($grpby_block=='grpby_operators'){
			$firstGrpBy= $oprId;
		}
		
		//---DETAIL ARRAY---
		$arrPatPayDetail[$firstGrpBy][$paidDate][$oprId]['pre_payment'] += $patQryRes[$i]['paid_amount']-$refundAmt;
		$arrPatPayDetail[$firstGrpBy][$paidDate][$oprId]['pre_payment_ref'] +=$refundAmt;
		$arrPatPayDetail[$firstGrpBy][$paidDate][$oprId]['pt_id'] = $patId;
		$arrPatPayDetail[$firstGrpBy][$paidDate][$oprId]['pre_pay_id'][] = $id;
		//------------------
		if($patQryRes[$i]['apply_payment_type'] == 'manually'){

			//-------ADD MANUALLY APPLIED PRE PAYMENTS-------------------
			$arrPrePayData[$oprId]['applied'] += $patQryRes[$i]['apply_amount'];
			$arrPrePayDetail[$patId]['applied'] += $patQryRes[$i]['apply_amount'];
			//--DETAIL ARRAY-
			$arrPatPayDetail[$firstGrpBy][$paidDate][$oprId]['pre_applied'] += $patQryRes[$i]['apply_amount'];
			$arrPatPayDetail[$firstGrpBy][$paidDate][$oprId]['pt_id'] = $patId;

			//SUMMARY
			$arrManuallyApplied[$oprId]['pre_payment']+= $patQryRes[$i]['apply_amount'];
			//DETAIL
			$arrPrePayManuallyApplied[$oprId][$id]['pat_name']=$patId;
			$arrPrePayManuallyApplied[$oprId][$id]['entered_date']=$paidDate;
			$arrPrePayManuallyApplied[$oprId][$id]['applied_amt'] = $patQryRes[$i]['apply_amount'];

		}
		$arrPrePayData[$oprId]['pre_pay_id'][] = $id;
		$arrPrePayDetail[$patId]['pre_pay_id'][] = $id;
		$arrDepIds[$id]=$id;

		//PAYMENT BREAKDOWN
		$arrPayBreakdown[strtoupper($patQryRes[$i]['payment_mode'])]+= $patQryRes[$i]['paid_amount']-$refundAmt;
	}

	
	// GET PRE PAT ENCOUNTER APPLIED AMTS
	$arrPrePayApplied = array();
	$arrPrePayAppliedId = array();
	if(count($arrDepIds)>0){
		$strDepIds=implode(',', $arrDepIds);
		$preAppQry="SELECT
		pDep.id, 
		pDep.entered_by, 
		patPayDet.paidForProc,
		patPaydet.unapply,
		patPaydet.deletePayment
		FROM patient_pre_payment pDep 
		LEFT JOIN patient_charges_detail_payment_info patPaydet ON patPayDet.patient_pre_payment_id = pDep.id 
		WHERE pDep.id IN($strDepIds) 
		AND (patPayDet.deletePayment='0' OR (patPayDet.deletePayment='1' AND patPayDet.deleteDate>'".$en_date."')) 
		AND (patPayDet.unapply='0' OR (patPayDet.unapply='1' AND DATE_FORMAT(patPayDet.unapply_date, '%Y-%m-%d') >'".$en_date."'))";

		$preAppRs=imw_query($preAppQry);
		while($preAppRes=imw_fetch_array($preAppRs)){
			$id = $preAppRes['id'];
			$oprId = $preAppRes['entered_by'];
			$unapply = $preAppRes['unapply'];
			$deletePayment = $preAppRes['deletePayment'];
			$arrPrePayAppliedId[$id][] = $preAppRes['paidForProc'];
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
	$transQry = "SELECT patient_charge_list.facility_id, 
						patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId', 
						patient_charge_list.patient_id,
						patient_charge_list.encounter_id, 
						DATE_FORMAT(patient_charge_list.date_of_service, '".get_sql_date_format()."') as 'DOS', 		
					
						patient_chargesheet_payment_info.operatorId, 
						DATE_FORMAT(patient_chargesheet_payment_info.transaction_date, '".get_sql_date_format()."') as 'paidDate',
						patient_chargesheet_payment_info.paymentClaims,
						patient_chargesheet_payment_info.payment_id,
						patient_chargesheet_payment_info.transaction_date, patient_chargesheet_payment_info.payment_mode,  
					
						patient_charges_detail_payment_info.charge_list_detail_id,
						patient_charges_detail_payment_info.paidForProc + patient_charges_detail_payment_info.overPayment as paidForProc,
						patient_charges_detail_payment_info.operator_id, 	
						patient_charges_detail_payment_info.patient_pre_payment_id,
						patient_charges_detail_payment_info.unapply, 
					
						patient_data.fname as 'pfname', 
						patient_data.mname as 'pmname',
						patient_data.lname as 'plname',
						
						users.fname, users.lname, patient_charge_list.gro_id as groupId
						
				FROM patient_charge_list 
				LEFT JOIN patient_chargesheet_payment_info 
						  ON patient_chargesheet_payment_info.encounter_id = patient_charge_list.encounter_id
				LEFT JOIN patient_charges_detail_payment_info
						  ON patient_charges_detail_payment_info.payment_id = patient_chargesheet_payment_info.payment_id
				LEFT JOIN patient_data ON patient_data.id = patient_charge_list.patient_id 
				LEFT JOIN users ON users.id = patient_charge_list.primary_provider_id_for_reports 
				WHERE (patient_chargesheet_payment_info.transaction_date BETWEEN '$st_date' and '$en_date') 
						AND (patient_charges_detail_payment_info.deletePayment='0' 
								OR (patient_charges_detail_payment_info.deletePayment='1'
										 AND patient_charges_detail_payment_info.deleteDate>'$en_date')
							)  AND patient_data.id IS NOT NULL 
	";
	if(empty($primaryProviderId) === false){
		$transQry .= " AND patient_charge_list.primary_provider_id_for_reports in($primaryProviderId)";
	}
	if(empty($facility_name_str) === false){
		$transQry .= " AND patient_charge_list.facility_id in($facility_name_str)";
	}
	if(empty($opr_name_str) === false){
		$transQry .= " AND patient_charges_detail_payment_info.operator_id in($opr_name_str)";
	}
	if(empty($grp_id) === false){
		$transQry .= " and patient_charge_list.gro_id in($grp_id)";
	}
	if(empty($insCompanies) === false){
			$qry.= " and ( patient_charge_list.primaryInsuranceCoId in($insCompanies) 
							 			OR	patient_charge_list.secondaryInsuranceCoId in($insCompanies)
										OR	patient_charge_list.tertiaryInsuranceCoId in($insCompanies) )";
		}
	$transQry .= " group by patient_charges_detail_payment_info.payment_details_id ORDER BY patient_charge_list.facility_id, users.lname, patient_chargesheet_payment_info.transaction_date";
	$transQryRes = imw_query($transQry);
	while($row = imw_fetch_assoc($transQryRes)){
		$trans_query_res[] = $row;
	}
	
	$arrAppAmtInDate = array();
	$arrCioAppDateId = array();
	$arrPreAppDateId = array();
	

	for($i=0;$i<count($trans_query_res);$i++){
		$hasData=1;
		$facility=''; $posFacility='';	$docNameArr= array();	$patNameArr= array();
		$facility = $trans_query_res[$i]['facility_id'];
		$doc_id = $trans_query_res[$i]['primaryProviderId'];
		$patId = $trans_query_res[$i]['patient_id'];
		$oprId=$trans_query_res[$i]['operator_id'];
		$encId=$trans_query_res[$i]['encounter_id'];
		$paidDate = $trans_query_res[$i]['paidDate'];
		$groupId = $trans_query_res[$i]['groupId'];

		//-------BEGIN SUMMARY POSTED AMOUNT ---------------------------------------------------------
		if(($trans_query_res[$i]['patient_pre_payment_id']<=0 && !$arrCioAccId[$trans_query_res[$i]['payment_id']] && !$appliedCICOIds[$trans_query_res[$i]['payment_id']]) 
		   || ($trans_query_res[$i]['patient_pre_payment_id']>0 && $trans_query_res[$i]['unapply']=='1')){
			   
			$hasData = 1;
			$paidForProc= $trans_query_res[$i]['paidForProc'];			
			
			$firstGrpBy = $doc_id;
			if($grpby_block=='grpby_facility'){
				$firstGrpBy= $facility;
			}
			if($grpby_block=='grpby_operators'){
				$firstGrpBy= $oprId;
			}
			if($grpby_block=='grpby_groups'){
				$firstGrpBy= $groupId;
			}
			
			
			if($trans_query_res[$i]['paymentClaims'] == 'Negative Payment'){
				$paidForProc= '-'.$paidForProc;
			}
			$arrPayData[$doc_id][$facility]['posted']+= $paidForProc;
			$arrPatPayDetail[$firstGrpBy][$paidDate][$oprId]['posted']+= $paidForProc;
			$arrPatPayDetail[$firstGrpBy][$paidDate][$oprId]['pt_id'] = $patId;

			//PAYMENT BREAKDOWN
			$arrPayBreakdown[strtoupper($trans_query_res[$i]['payment_mode'])]+= $paidForProc;
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
		$qry = "SELECT acc_payment_id 
				FROM check_in_out_payment_post 
				WHERE acc_payment_id IN(".$strCioAccDateId.") 
					AND status='0'
				";		
		$rs = imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$arrAppAmtInDate['cio_payment'] += $arrCioAppDateId[$res['acc_payment_id']];
		}
	}
	//GETTING CI/CO MANUALY APPLIED FOR DATE RANGE
	$qry = "SELECT manually_payment 
			FROM check_in_out_payment_post 
			JOIN check_in_out_payment ON check_in_out_payment.payment_id = check_in_out_payment_post.check_in_out_payment_id 
			WHERE (check_in_out_payment_post.manually_date BETWEEN '$st_date' and '$en_date') 
					AND manually_payment>0 AND status='0' AND del_status='0'";
	if(empty($opr_name_str) === false){
		$qry.= " AND check_in_out_payment.created_by IN($opr_name_str)";
	}
	$rs = imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAppAmtInDate['cio_payment'] += $res['manually_payment'];
	}
	
	if(sizeof($arrPreAppDateId)>0){
		$strPreAppDateId=implode(',', array_keys($arrPreAppDateId));
		$qry = "SELECT id 
				FROM patient_pre_payment 
				WHERE id IN(".$strPreAppDateId.") 
						AND del_status='0'";		
		$rs = imw_query($qry);
		while($res = imw_fetch_array($rs)){
			$arrAppAmtInDate['pre_payment'] += $arrPreAppDateId[$res['id']];
		}
	}
	//GETTING PRE PAYMENTS MANUALY APPLIED FOR DATE RANGE
	$qry = "SELECT apply_amount 
			FROM patient_pre_payment 
			WHERE (apply_payment_date BETWEEN '$st_date' and '$en_date') 
					AND apply_payment_type='manually' 
					AND del_status='0' 
					AND apply_amount>0
			";
	if(empty($opr_name_str) === false){
		$qry .= " AND entered_by in($opr_name_str)";
	}
	$rs = imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$arrAppAmtInDate['pre_payment'] += $res['apply_amount'];
	}
	
	//---BEGIN GET PATIENT ARRAY-----------------------------------------------------
	$arrPatientId = array();
	foreach($arrPatPayDetail as $key => $val){
		foreach($val as $chld_key => $chld_val){
			foreach($chld_val as $obj){
				$arrPatientId[] = $obj['pt_id'];
			}
		}
	}
	
	$qry = "SELECT 
				id,
				CONCAT(patient_data.lname,', ',patient_data.fname, ' - ',id) AS name
				FROM patient_data 
				WHERE patient_data.id IN(".implode(",",array_unique($arrPatientId)).")";
	$qryres = imw_query($qry);
	$arrPatient = array();
	while($row = imw_fetch_assoc($qryres)){
		$arrPatient[$row['id']] = $row['name'];
	}
	//---END GET PATIENT ARRAY-----------------------------------------------------
	
	if($inc_appt == 1) {
		$app_query = "Select DATE_FORMAT(sa_app_start_date, '".$dateFormat."') as apptDate, TIME_FORMAT(sa_app_starttime, '%h:%i %p') as starttime	FROM schedule_appointments where sa_patient_id IN(".implode(",",array_unique($arrPatientId)).")";
		$app_det_query = imw_query($app_query);
		$arrPtApptDetail = array();
		while($row = imw_fetch_assoc($app_det_query)){
			$apptDate 	= 	$row['apptDate'];
			$starttime 	= 	$row['starttime'];
			$arrPtApptDetail['date'] = $apptDate;
			$arrPtApptDetail['time'] = $starttime;
		}
	}
	//---------------- END FETCHING DATA----
	
	if($Process=='summary'){
		unset($complete_page_data);
		include_once('daily_report_summary.php');
		$strHTML1 = $strHTML = $complete_page_data;
	}else{
		unset($complete_page_data);
		include_once('daily_report_detail.php');
		$strHTML1 = $strHTML = $complete_page_data;
	}
	
	//--- CREATE PDF FILE FOR PRINTING -----
	if($printFile == true and $complete_page_data != '')
	{
		$strHTML1 = "<style>".file_get_contents('css/reports_pdf.css')."</style>";
		$strHTML1.=<<<DATA
		<style type="text/css">
			.rpt_headers{
				font-size: 11px;
				font-family: Arial, Helvetica, sans-serif;
				font-weight: bold;
				color: #FFFFFF;
				background-color: #4684ab;
				text-align: left;
				word-wrap: break-word;
			}
			.text_10b{
				font-size:11px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#000000;
			}
			#heading_orange td
			{
				text-align: left;
				background-color: #FDD685;
				color: #000;
				width: 100%;
				font-weight: bold;
			}
			#heading1 td {
				font-size: 11px;
				font-family: Arial, Helvetica, sans-serif;
				font-weight: bold;
				color: #000000;
				background-color: #BCD5E1;
				padding: 2px;
			}
			.text_10{
				font-size:11px;
				font-family:Arial, Helvetica, sans-serif;
				color:#000000;
			}
			.table {
				border-collapse: separate;
				border: none;
				background-color: #FFF3E8;
				border-spacing: 1px;
			}
			.data td
			{
				background-color: #fff;
				font-family:Arial, Helvetica, sans-serif;
				font-size: 11px;
				color: #333333;
				padding-left: 5px;
				text-align: left;
				vertical-align: top;
			}
			
			.subtotal td {
				background-color: #fff;
				font-family: Arial, Helvetica, sans-serif;
				font-size: 11px;
				color: #333333;
				font-weight: bold;
				padding-left: 5px;
				text-align: left;
				vertical-align: top;
			}
		</style>
			<page backtop="12mm" backbottom="10mm">
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
			$page_data
			$manually_applied_cico_html
			$manually_applied_pre_pay_html
			$manually_applied_html
			</page>
DATA;
	//	$objManageData->Smarty->assign("showBtn",true);	
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

if($complete_page_data){
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$complete_page_data;
}else{
	$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
}
echo $csv_file_data;

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