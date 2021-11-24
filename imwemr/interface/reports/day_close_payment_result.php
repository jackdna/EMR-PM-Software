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
if($pay_location=='1'){
	$sel_fac = $CLSReports->report_display_selected(implode(',',$facility_name),'facility_tbl',1,$fac_cnt);
}else{
	$sel_fac = $CLSReports->report_display_selected(implode(',',$facility_name),'facility',1,$posfac_cnt);
}
$sel_phy = $CLSReports->report_display_selected(implode(',',$phyId),'physician',1,$phy_cnt);
$sel_opr = $CLSReports->report_display_selected(implode(',',$operator_id),'operator',1,$opr_cnt);
$sel_dept = $CLSReports->report_display_selected(implode(',',$department),'department',1,$dept_cnt);
$sel_consolidation= ($consolidation=='1')? 'Selected' : 'Not Selected'; 

$process = $summary_detail;
if($_POST['form_submitted']){
	
	$_POST['summary_detail']='Detail';
	$process='Detail';
	
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
	$sc_name = join(',',$facility_name);
	
	//PHYSICIAN
	if($_REQUEST['phyId']!=''){
		$rqArrPhyId = $_REQUEST['phyId'];
	}
	$Physician = join(',',$rqArrPhyId);
	
	//operator_id
	if($_REQUEST['operator_id']!=''){
		$rqArrOprId = $_REQUEST['operator_id'];
	}
	$operator = join(',',$rqArrOprId);
	
	if(empty($_REQUEST['groups'])==false){
		$groupId = $_REQUEST['groups'];
	}
	$grp_id = join(',',$groupId);
	
	$departmentId = $_REQUEST['department'];
	$department = join(',',$departmentId);
}

$printFile=true;
//pre($_REQUEST);
unset($_REQUEST['operator']);
unset($_REQUEST['department']);
if(trim($_REQUEST['Submit']) != ''){
	$printFile = false;

	//--- CHANGE DATE FORMAT ----
	$startDate = getDateFormatDB($Start_date);
	$endDate   = getDateFormatDB($End_date);
	
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

	//GET CREDITY TYPE ARRAY
	$arrCreditTypes=unserialize(html_entity_decode($ccTypeArr));	
	$arrCreditTypes[0]='No CC Type';
	
	// GET DEFAULT FACILITY
	$rs=imw_query("Select id,fac_prac_code from facility where facility_type  = '1' LIMIT 1")or die(imw_error().'_27');
	$res = imw_fetch_array($rs);
	$headPosFacility = $res['fac_prac_code'];
	$headSchFacility = $res['id'];
	
	// GET SELECTED SCHEDULER FACILITIES
	$sch_fac_id_str='';
	$fac_query = "Select id,name,fac_prac_code from facility";
	$fac_query_rs = imw_query($fac_query)or die(imw_error().'_33');
	$sch_fac_id_arr = array();
	$arr_sc_facility=array();
	$arr_sch_facilities=array();
	$arr_sch_facilities[0]='Other';
	while($fac_query_res = imw_fetch_array($fac_query_rs)){	
		$fac_id = $fac_query_res['id'];
		$pos_fac_id = $fac_query_res['fac_prac_code'];
		$sch_pos_fac_arr[$fac_id] = $pos_fac_id;
		$sch_fac_arr[$pos_fac_id][] = $fac_id;
		$arr_sch_facilities[$fac_id]=$fac_query_res['name'];
	}

	//pre($sch_fac_arr);pre($sch_pos_fac_arr);
	// GET ALL POS FACILITIES DETAILS
	$qry = "Select facilityPracCode, pos_facility_id from pos_facilityies_tbl";
	$rs=imw_query($qry)or die(imw_error().'_44');
	$posFacilityArr = array();
	$posFacilityArr[0] = 'No Facility';
	while($posQryRes = imw_fetch_array($rs)){
		$pos_facility_id = $posQryRes['pos_facility_id'];
		$posFacilityArr[$pos_facility_id] = $posQryRes['facilityPracCode'];
	}	


	//GET ALL USERS
	$providerNameArr[0] = 'No Provider';
	$rs=imw_query("Select id, fname, mname, lname FROM users")or die(imw_error().'_54');
	while($res=imw_fetch_array($rs)){
		$id  = $res['id'];
		$pro_name_arr = array();
		$pro_name_arr["LAST_NAME"] = $res['lname'];
		$pro_name_arr["FIRST_NAME"] = $res['fname'];
		$pro_name_arr["MIDDLE_NAME"] = $res['mname'];
		$pro_name = changeNameFormat($pro_name_arr);
		$providerNameArr[$id] = $pro_name;
	}
	
	//--- GET GROUP NAME ---
	$group_name = "All";
	if(empty($grp_id) === false){
		$group_query = imw_query("select name from groups_new where gro_id = '$grp_id'");
		if(imw_num_rows($group_query) > 0){
			while($groupQryRes=imw_fetch_assoc($group_query)){
				$group_name = $groupQryRes['name'];
			}
		}
	}
	
	if(empty($sc_name)===false){ $arrFacilitySel = explode(',', $sc_name); }
	if(empty($Physician)===false){ $arrDoctorSel = explode(',', $Physician); }

	$schFacId=0;
	if($pay_location=='1'){
		$schFacId = $sc_name;
	}else{
		if(empty($sc_name)== false){
			$arr_sc_name=explode(',', $sc_name);
	
			$arrSchFacId=array();
			for($i=0; $i<sizeof($arr_sc_name); $i++){
				//$id= $sch_fac_arr[$arr_sc_name[$i]];
				$id= implode(",",$sch_fac_arr[$arr_sc_name[$i]]);
				if($id>0 && $id!=''){
					$arrSchFacId[$id] = $id;
				}
			}
		$arrSchFacId=array_unique($arrSchFacId);
		$schFacId = implode(',', $arrSchFacId);
		}
	}

	$cc_type_str='';
	if(sizeof($cc_type)>0){
		$cc_type_searched=implode(',',$cc_type);
		$cc_type_str= "'".str_replace(",", "','", $cc_type_searched)."'";
		$cc_type_str=strtolower($cc_type_str);
	}

	if($Physician != ""){
		$wherePart .= " AND patChg.primary_provider_id_for_reports in($Physician)";
	}
	if($sc_name != ""){
		if($pay_location=='1'){
			$wherePart .= " AND payChg.facility_id in($schFacId)";
		}else{
			$wherePart .= " AND patChg.facility_id in($sc_name)";
		}
	}
	if($grp_id != ""){
		$wherePart .= " AND patChg.gro_id in($grp_id)";
	}
	if($operator != ""){
		$wherePart .= " AND payChgDet.operator_id in($operator)";
	}
	//----GET GROUP BY ----------
	if($department != ""){
		$groupBy = "department";
	}else if($operator != ""){
		$groupBy = "operator";
	}else{
		$groupBy = "physician";
	}
	
	//--------------------------
	$orderBy='';
	if($groupBy=='physician' || $groupBy=='operator'){ $orderBy = 'users.lname, users.fname'; }
	
	if($pay_location=='1'){
		$orderBy = " facility.name, users.lname, users.fname";
	}elseif($pay_location!='1' && empty($sc_name)==false){
		$orderBy = "users.lname, users.fname, pos_facilityies_tbl.facilityPracCode";
	}		

	if(empty($orderBy)===false){
		$orderByPart = ' ORDER BY '.$orderBy.', pd.lname, pd.fname, patChg.del_status ASC';
	}
	//---------------------------------------START DATA --------------------------------------------
	//--- GET POSTED PAYMENT
	$qry = "SELECT patChg.patient_id, 
	patChg.facility_id, payChg.facility_id as 'paid_facility',
	patChg.primary_provider_id_for_reports as 'primaryProviderId', payChg.creditCardCo,
	DATE_FORMAT(patChg.date_of_service, '".$dateFormat."') as 'date_of_service', patChg.del_status,
	payChg.encounter_id, 
	payChgDet.charge_list_detail_id, 
	DATE_FORMAT(payChg.transaction_date, '".$dateFormat."') as 'transaction_date',
	DATE_FORMAT(payChg.date_of_payment, '".$dateFormat."') as 'date_of_payment',
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
	JOIN patient_charge_list patChg ON patChg.encounter_id = payChg.encounter_id";
	if($groupBy == "physician"){
		$qry.=" LEFT JOIN users ON users.id = patChg.primary_provider_id_for_reports";
	}else if($groupBy == "operator"){
		$qry.=" LEFT JOIN users ON users.id = payChgDet.operator_id";
	}
	$qry.=" 
		JOIN patient_data pd ON pd.id = patChg.patient_id 
		LEFT JOIN pos_facilityies_tbl ON pos_facilityies_tbl.pos_facility_id = patChg.facility_id
		LEFT JOIN facility ON facility.id = payChg.facility_id
		WHERE 1=1 AND pd.id IS NOT NULL";
	if($consolidation==1){	
		if($hourFrom!='' && $hourTo!=''){
			$qry.=" AND (payChgDet.deletePayment='0' OR (payChgDet.deletePayment='1' AND deleteDate> '$endDate' AND deleteTime> '$hourTo'))";
		}else{
			$qry.=" AND (payChgDet.deletePayment='0' OR (payChgDet.deletePayment='1' AND deleteDate> '$endDate'))";
		}
	}
	if($DateRangeFor=='dot'){
		$qry.=" AND (payChg.transaction_date between '$startDate' and '$endDate')";
		
		if($hourFrom!='' && $hourTo!=''){
			$qry.= " AND (DATE_FORMAT(payChgDet.entered_date, '%H:%i:%s') BETWEEN '$hourFrom' AND '$hourTo')";					
		}
	}else{
		$qry.=" AND (payChg.date_of_payment between '$startDate' and '$endDate') AND (payChgDet.deletePayment='0' OR (payChgDet.deletePayment='1' AND payChgDet.deleteDate>'$endDate'))";
	}
	if(empty($pay_method)==false){ //PAYMENT MODE
		$qry.=" AND LOWER(payChg.payment_mode)='".$pay_method."'";
	}
	if(empty($cc_type_str)==false){ //IF CREDIT CARD
		$qry.=" AND LOWER(payChg.creditCardCo) IN(".$cc_type_str.")";
	}
	
	if($consolidation==1){
		$qry.=" AND (patChg.del_status='0' OR (patChg.del_status='1' AND DATE_FORMAT(patChg.trans_del_date, '".$formatDelDate."')> '$delDate'))";
	}
	
	$qry.= $wherePart.$orderByPart;
	
	$rs = imw_query($qry)or die(imw_error().'_154');
	$arrTemp=array();
	$arrPostedEncDetIds=array();
	$arrPostedChgDetIds=array();
	$arrPostedChgDetIds[0] = 0;
	$arrPostedEncDetIds[0] = 0;
	while($res = imw_fetch_array($rs)){
		$eid = $res['encounter_id'];
		
		if(!$arrTemp[$eid] || ($arrTemp[$eid] && $arrTemp[$eid]['sts']==$res['del_status'])){ //CONDITION ADDED TO AVOID DUPLICATE DELETED ENCOUNTER
		
			$printFile=true;
			$paidAmt=0;
			$pid = $res['patient_id'];
	
			$chgDetId = $res['charge_list_detail_id'];
			$paidBy = ucfirst($res['paidBy']);
			$payMode = ucwords($res['payment_mode']);
			$payMode=($payMode=='Eft')? 'EFT': $payMode;
			$payMode=($payMode=='Veep')? 'VEEP': $payMode;
			
			if($payMode!='Cash' && $payMode!='Check' && $payMode!='EFT' && $payMode!='VEEP' && $payMode!='Money Order' && $payMode!='Credit Card'){
				$payMode = 'Other';
			}

			$dot=$res['transaction_date'];
			$dop=$res['date_of_payment'];
			$phyId = $res['primaryProviderId'];
			$facId = ($pay_location=='1')? $res['paid_facility'] : $res['facility_id'];
			$oprId = $res['operator_id'];
			$patName = core_name_format($res['lname'], $res['fname'], $res['mname']).' - '.$pid;		
			
			$encounterIdArr[$eid] = $eid;
			if($chgDetId>0){ 
				$arrPostedChgDetIds[$chgDetId]=$chgDetId; 
			}else{
				$arrPostedEncDetIds[$eid]=$eid;
			}
				
			$ccType= ($arrCreditTypes[strtolower($res['creditCardCo'])]!='') ? $arrCreditTypes[strtolower($res['creditCardCo'])] : $arrCreditTypes[0];
			
			$grpId = $phyId;
			//$grpId = ($groupBy=='facility') ? $facId : $grpId;
			$grpId = ($groupBy=='operator') ? $oprId : $grpId; 
			
			$paidAmt = $res['paidForProc'] + $res['overPayment'];
			if($res['paymentClaims'] == 'Negative Payment'){
				$paidAmt= '-'.$paidAmt;
			}
			
			
			if($payMode=='Credit Card')
			$arrCCModeAmts[$ccType]+=$paidAmt; 
			
			if($process=='Summary'){
				$arrPostedPay[$grpId][$payMode]+= $paidAmt; 
				if($paidBy=='patient'){
					$arrPostedPay[$grpId]['byPatient']+= $paidAmt;
				}else{
					$arrPostedPay[$grpId]['byInsurance']+= $paidAmt;
				}
			}else{
				$arrMainData[$payMode][$dot]['posted'][$eid]['pat_name']=$patName;
				$arrMainData[$payMode][$dot]['posted'][$eid]['dop']=$res['date_of_payment'];
				$arrMainData[$payMode][$dot]['posted'][$eid]['facility']=$facId;
				$arrMainData[$payMode][$dot]['posted'][$eid]['physician']=$phyId;
				$arrMainData[$payMode][$dot]['posted'][$eid]['operator']=$oprId;
				$arrMainData[$payMode][$dot]['posted'][$eid]['paid_by']=$paidBy;
				$arrMainData[$payMode][$dot]['posted'][$eid]['payment']+= $paidAmt;
			}

			$arrTemp[$eid]['enc']=$eid;
			$arrTemp[$eid]['sts']=$res['del_status'];
		}
	}
	unset($rs);

	// GET NOT APPLIED CI/CO for selected month
	$arrCICONotApplied=array();
	$tempCCTypeAmts=array();
	
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
	DATE_FORMAT(cioPay.created_on, '".$dateFormat."') as 'created_on', 
	cioPay.payment_method, cioPay.cc_type,
	cioPay.created_by, cioPay.created_time, 
	cioPayDet.item_payment, 
	pd.fname, pd.mname, pd.lname 
	FROM schedule_appointments sa 
	JOIN check_in_out_payment cioPay ON cioPay.sch_id  = sa.id 
	JOIN check_in_out_payment_details cioPayDet ON cioPayDet.payment_id= cioPay.payment_id
	".$cioJoin."
	JOIN patient_data pd ON pd.id = cioPay.patient_id 
	WHERE (cioPay.created_on BETWEEN '".$startDate."' AND '".$endDate."') 
	AND pd.id IS NOT NULL";
	if($consolidation==1){
		if($hourFrom!='' && $hourTo!=''){		
			$qry.=" AND (cioPayDet.status=0 OR (cioPayDet.status=1 AND cioPayDet.delete_date>'".$endDate."' AND cioPayDet.delete_time>'".$hourTo."'))";		
		}else{
			$qry.=" AND (cioPayDet.status=0 OR (cioPayDet.status=1 AND cioPayDet.delete_date>'".$endDate."'))";
		}
	}
	if(empty($sc_name) === false){
		if(empty($schFacId) ===false){
			$qry.= " AND sa.sa_facility_id IN(".$schFacId.")";
		} else {
			$qry.=" AND 1=2"; //SO THAT QUERY WILL NOT RETURN ANY RESULT.
		}
	}
	if(empty($Physician) === false){
		$qry.= " AND sa.sa_doctor_id IN(".$Physician.")";
	}
	if(empty($operator) === false){
		$qry.= " AND cioPay.created_by IN(".$operator.")";
	}
	if(empty($pay_method)==false){ //PAYMENT MODE
		$qry.=" AND LOWER(cioPay.payment_method)='".$pay_method."'";
	}
	if(empty($cc_type_str)==false){ //IF CREDIT CARD
		$qry.=" AND LOWER(cioPay.cc_type) IN(".$cc_type_str.")";
	}
	
	$qry.= $cioOrderBy;
	$rs=imw_query($qry)or die(imw_error().'_451');
	while($res=imw_fetch_array($rs)){
		
		//IF TIME SEARCHED THEN SET TIME STAMP FOR SEARCH
		if($hourFrom!='' && $hourTo!=''){
			$arr1=explode(' ', $res['created_time']);
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
			$printFile=true;
			$pid = $res['patient_id'];
			$payment_id = $res['payment_id'];
			$phyId = $res['sa_doctor_id'];
			$sch_facility = $res['sa_facility_id'];
			$oprId = $res['created_by'];
			$patName = core_name_format($res['lname'], $res['fname'], $res['mname']).' - '.$pid;
			$facility = ($pay_location=='1')? $sch_facility : $sch_pos_fac_arr[$sch_facility];
			if($facility=='' || $facility<=0) { $facility=0; }
			$ccType= ($arrCreditTypes[strtolower($res['cc_type'])]!='') ? $arrCreditTypes[strtolower($res['cc_type'])] : $arrCreditTypes[0];		
			
			$grpId = $phyId;
			
			#############################################################
			#query to get refund detail for current ci/co payments if any
			#############################################################
			$refundAmt=0;
			$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND ci_co_id = '".$res['cioPaydetID']."' AND (entered_date BETWEEN '".$startDate."' AND '".$endDate."')")or die(imw_error().'_471');
			while($rsRef=imw_fetch_array($qryRef))
			{
				$refundAmt+=$rsRef['ref_amt'];
			}
			imw_free_result($qryRef);	
			
			$payMode = ucwords($res['payment_method']);
			$payMode=($payMode=='Eft')? 'EFT': $payMode;
			$payMode=($payMode=='Veep')? 'VEEP': $payMode;			
			if($payMode!='Cash' && $payMode!='Check' && $payMode!='EFT' && $payMode!='VEEP' && $payMode!='Money Order' && $payMode!='Credit Card'){
				$payMode = 'Other';
			}
			
			$tempCIOArr[$grpId][$payment_id]['payment']+= $res['item_payment'];
			$tempCIOArr[$grpId][$payment_id]['pay_mode']= $payMode;
			$tempCIOArr[$grpId][$payment_id]['refund']+=$refundAmt;
			if(strtolower($res['payment_method'])=='credit card')
			$tempCCTypeAmts[$payment_id][$ccType]=$ccType;
			
			$tempCIOArr[$grpId][$payment_id]['facility']= $facility;
			if($process=='Detail'){
				$tempCIODetail[$payment_id]['patName'] = $patName;
				$tempCIODetail[$payment_id]['paidDate'] = $res['created_on'];
				$tempCIODetail[$payment_id]['facility'] = $facility;
				$tempCIODetail[$payment_id]['physician'] = $phyId;
				$tempCIODetail[$payment_id]['operator'] = $oprId;
			}
			
			$tempPayIds[$payment_id] =$payment_id;
			
			$groupArr[$grpId] = $grpId;
		}
	}
	//pre($tempPayIds);
	$splitted_encounters=array();
	if(sizeof($tempPayIds)>0){
		$splitted_encounters = array_chunk($tempPayIds,4000);
		$tempCIOPaid=array();
		foreach($splitted_encounters as $arr){
			$str_splitted_encs 	 = implode(',',$arr);
			$arr_acc_payment_id=array();
			$temp_acc_payment_id=array();
			
			$qry="SELECT cioPost.check_in_out_payment_id, 
						 cioPost.manually_payment, 
						 cioPost.acc_payment_id, 
						 cioPost.manually_date  
						 FROM check_in_out_payment_post cioPost 
						 WHERE cioPost.check_in_out_payment_id IN(".$str_splitted_encs.") 
						 AND cioPost.status='0'";
						 
			$rs=imw_query($qry)or die(imw_error().'_514');
			while($res=imw_fetch_array($rs)){
				$payment_id = $res['check_in_out_payment_id'];
				
				if($res['manually_payment']>0 && $res['manually_date']<=$endDate){
					$tempCIOPaid[$payment_id]+=$res['manually_payment'];
				}

				//FOR MANUAALY APPLIED BLOCK
				if($res['manually_payment']>0){
					$tempCIOManuallyApplied[$payment_id]+=$res['manually_payment'];
				}
				
				if($res['acc_payment_id']>0){ 
					$arr_acc_payment_id[$res['acc_payment_id']]=$res['acc_payment_id'];
					$temp_acc_payment_id[$res['acc_payment_id']] = $res['check_in_out_payment_id'];
				}
			}
			if(sizeof($arr_acc_payment_id)>0){
				$str_acc_payment_id = implode(',', $arr_acc_payment_id);
				
				$qry="SELECT patPay.payment_id, 
							patPayDet.paidForProc FROM 
							patient_chargesheet_payment_info patPay 
							LEFT JOIN patient_charges_detail_payment_info patPayDet ON patPayDet.payment_id = patPay.payment_id 
							WHERE patPay.payment_id IN(".$str_acc_payment_id.")";
				if($DateRangeFor=='dot'){
					$qry.=" AND patPay.transaction_date <='$endDate' ";
				}else{
					$qry.=" AND patPay.date_of_payment <='$endDate'";
				}
				$qry.=" AND ((patPayDet.deletePayment='0' OR (patPayDet.deletePayment='1' AND patPayDet.deleteDate > '".$endDate."')) 
						AND (patPayDet.unapply='0' OR (patPayDet.unapply='1' AND DATE_FORMAT(patPayDet.unapply_date, '%Y-%m-%d')>'".$endDate."')))";

				$rs=imw_query($qry)or die(imw_error().'_537');
				while($res=imw_fetch_array($rs)){
					$payment_id = $temp_acc_payment_id[$res['payment_id']];
					$tempCIOPaid[$payment_id]+=$res['paidForProc'];
				}
			}
		}
	
		//preparing final ci/co array
		foreach($groupArr as $grpId){		
			foreach($tempCIOArr[$grpId] as $payment_id => $cioData){
				$cioPayment = $cioData['payment'];
				$refund=$cioData['refund'];
				$paid_date=$tempCIODetail[$payment_id]['paidDate'];

				$pay_mode = $cioData['pay_mode'];
				
				if($tempCIOPaid[$payment_id]>0){
					$cioPayment = floatval($cioPayment) - floatval($tempCIOPaid[$payment_id]);
				}
				$cio_pos_facility_id = $cioData['facility'];
				if($cioPayment>0){
					
					if($pay_mode=='Credit Card' && sizeof($tempCCTypeAmts[$payment_id])>0){
						foreach($tempCCTypeAmts[$payment_id] as $ccType){
							$arrCCModeAmts[$ccType]+=$cioPayment;
						}
						$arrCCModeAmts[$ccType]-=$refund;
					}
					
					if($process=='Summary'){
						$arrCICONotApplied[$grpId][$pay_mode]+=($cioPayment-$refund);
						$arrCICONotApplied[$grpId][$pay_mode.'_ref_amt']+=$refund;
						
						$arrCICONotApplied[$grpId][$pay_mode.'_is_ref']=($refund>=1 && $arrCICONotApplied[$grpId][$pay_mode.'_is_ref']=='')?$pay_mode:0;
					}else{
						$arrMainData[$pay_mode][$paid_date]['cico'][$payment_id]['pat_name']=$tempCIODetail[$payment_id]['patName'];
						$arrMainData[$pay_mode][$paid_date]['cico'][$payment_id]['facility']=$cio_pos_facility_id;
						$arrMainData[$pay_mode][$paid_date]['cico'][$payment_id]['physician']=$tempCIODetail[$payment_id]['physician'];
						$arrMainData[$pay_mode][$paid_date]['cico'][$payment_id]['operator']=$tempCIODetail[$payment_id]['operator'];
						$arrMainData[$pay_mode][$paid_date]['cico'][$payment_id]['dop']=$paid_date;
						$arrMainData[$pay_mode][$paid_date]['cico'][$payment_id]['payment']=($cioPayment-$refund);
						$arrMainData[$pay_mode][$paid_date]['cico'][$payment_id]['paid_by']='Patient';
						
						$arrMainData[$pay_mode][$paid_date]['cico'][$payment_id]['ref_amt']+=$refund;
						$arrMainData[$pay_mode][$paid_date]['cico'][$payment_id]['is_ref']=($refund>=1)?$pay_mode:'';
					}
				}
				
				//MANAULLY PAID ARRAY
				if($tempCIOManuallyApplied[$payment_id]>0){
					if($process=='Summary'){
						$arrManuallyApplied[$grpId]['cico']+= $tempCIOManuallyApplied[$payment_id];
					}else{
						$arrManuallyData[$paid_date]['cico'][$payment_id]['pat_name']=$tempCIODetail[$payment_id]['patName'];
						$arrManuallyData[$paid_date]['cico'][$payment_id]['facility']=$cio_pos_facility_id;
						$arrManuallyData[$paid_date]['cico'][$payment_id]['physician']=$tempCIODetail[$payment_id]['physician'];
						$arrManuallyData[$paid_date]['cico'][$payment_id]['dop']=$paid_date;						
						$arrManuallyData[$paid_date]['cico'][$payment_id]['applied_amt']= $tempCIOManuallyApplied[$payment_id];
						$arrManuallyData[$paid_date]['cico'][$payment_id]['paid_by']='patient';
					}
				}
			}
		}
	}
	unset($tempCIOManuallyApplied);
	ksort($arrCIOCCTypeAmts);

	// GET PATIENT PRE PAYMENTS
	$groupArr=array();
	$tempCCTypeAmts=array();
	$patQryRes = array();
	$qry="Select pDep.id, pDep.patient_id, pDep.paid_amount, pDep.facility_id, 
	pDep.apply_payment_date, pData.default_facility, pDep.apply_payment_type, pDep.apply_amount, pDep.provider_id, 
	DATE_FORMAT(pDep.entered_date, '".$dateFormat."') as 'entered_date', DATE_FORMAT(pDep.paid_date, '".get_sql_date_format()."') as 'paid_date',
	pDep.entered_by, pDep.payment_mode, pDep.credit_card_co,
	pData.fname, pData.mname, pData.lname 
	FROM patient_pre_payment pDep 
	JOIN patient_data pData ON pData.id = pDep.patient_id 
	WHERE 1=1 AND pData.id IS NOT NULL ";
	if($consolidation==1){
		$qry.=" AND (pDep.del_status='0' OR (pDep.del_status='1' AND DATE_FORMAT(pDep.trans_del_date, '".$formatDelDate."')>'".$delDate."'))";
	}
	if($DateRangeFor=='dot'){
		$qry.=" AND (pDep.entered_date between '".$startDate."' and '".$endDate."')";
		
		if($hourFrom!='' && $hourTo!=''){		
			$qry.= " AND (pDep.entered_time BETWEEN '$hourFrom' AND '$hourTo')";					
		}
	}else{
		$qry.=" AND (pDep.paid_date between '".$startDate."' and '".$endDate."')";
	}
	if(empty($Physician) === false){
		$qry.= " AND pDep.provider_id IN(".$Physician.")";
	}
	if(empty($sc_name) === false){
		if(empty($schFacId) ===false){
			$qry .= " AND pDep.facility_id in($schFacId)";
		} else {
			$qry.=" AND 1=2"; //SO THAT QUERY WILL NOT RETURN ANY RESULT.
		}
	}
	if(empty($operator) === false){
		$qry .= " AND pDep.entered_by in($operator)";
	}
	if(empty($pay_method)==false){ //PAYMENT MODE
		$qry.=" AND LOWER(pDep.payment_mode)='".$pay_method."'";
	}
	if(empty($cc_type_str)==false){ //IF CREDIT CARD
		$qry.=" AND LOWER(pDep.credit_card_co) IN(".$cc_type_str.")";
	}
	
	$qry.=" ORDER BY pData.lname, pData.fname";
	
	$patQry = imw_query($qry);
	
	$arrDepIds=array();
	$tempData=array(); $arrDepIds=array(); $arrAllIds=array(); $arrAllIdsData=array();
	while($patQryRes = imw_fetch_assoc($patQry)){
		$printFile=true;
		$facility=0; $balance_amount=0; $doc_id=0;$refundAmt=0;
		
		##########################################################
		#query to get refund detail for current pre payment if any
		##########################################################

		$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND pmt_id = '".$patQryRes['id']."' AND (entered_date BETWEEN '".$startDate."' AND '".$endDate."')")or die(imw_error().'_656');
		while($rsRef=imw_fetch_array($qryRef))
		{
			$refundAmt=$rsRef['ref_amt'];
		}
		
		imw_free_result($qryRef);
	
		$pid = $patQryRes['patient_id'];
		$facility= ($pay_location=='1')? $patQryRes['facility_id'] : $sch_pos_fac_arr[$patQryRes['facility_id']];
		$phyId=$patQryRes['provider_id'];
		$oprId=$patQryRes['entered_by'];
		$patName = core_name_format($patQryRes['lname'], $patQryRes['fname'], $patQryRes['mname']).' - '.$pid;
		$ccType= ($arrCreditTypes[strtolower($patQryRes['credit_card_co'])]!='') ? $arrCreditTypes[strtolower($patQryRes['credit_card_co'])] : $arrCreditTypes[0];				
		
		//if($phyId<=0){ 		$phyId = $arrPatProv[$patQryRes['patient_id']];	}
		if($facility<=0 || $facility==''){ $facility= ($pay_location=='1') ? $headSchFacility :$headPosFacility ; }

		$grpId= $phyId;
		if($grpId<=0){ $grpId=0; } 
		
		$id= $patQryRes['id'];
				
		$balance_amount=($patQryRes['paid_amount']);
		
		if($patQryRes['apply_payment_type']=='manually' && $patQryRes['apply_payment_date']<= $endDate){
			$balance_amount-=$patQryRes['apply_amount'];
		}
		
		//FOR MANUALLY APPLIED BLOCK
		if($patQryRes['apply_payment_type']=='manually'){
			$tempPrePaidManuallyApplied[$id]+= $patQryRes['apply_amount'];
		}


		if($balance_amount>0 || $patQryRes['apply_amount']>0){
			$tempData[$id]['PAT_DEPOSIT']=$patQryRes['paid_amount'];
			$tempData[$id]['PAT_DEPOSIT_REF']=$refundAmt;
			
			if($patQryRes['apply_payment_type']=='manually' && $patQryRes['apply_payment_date']<= $endDate){
				$tempData[$id]['APPLIED_AMT']+= $patQryRes['apply_amount'];
			}
			if($patQryRes['apply_payment_date']!='0000-00-00'){
				$arrDepIds[$id]=$id;	
			}
			
			$arrAllIds[$id]=$id;
			$payMode = ucwords($patQryRes['payment_mode']);
			$payMode=($payMode=='Eft')? 'EFT': $payMode;
			$payMode=($payMode=='Veep')? 'VEEP': $payMode;			
			if($payMode!='Cash' && $payMode!='Check' && $payMode!='EFT' && $payMode!='VEEP' && $payMode!='Money Order' && $payMode!='Credit Card'){
				$payMode = 'Other';
			}
			
			$arrAllIdsData[$grpId][$id]['pay_mode']= $payMode;
			$arrAllIdsData[$grpId][$id]['pat_name']= $patName;
			if(strtolower($patQryRes['payment_mode'])=='credit card')
			$tempCCTypeAmts[$id][$ccType]=$ccType;
			
			if($DateRangeFor=='dot'){
				$arrAllIdsData[$grpId][$id]['entered_date']=$patQryRes['entered_date'];
			}else{
				$arrAllIdsData[$grpId][$id]['entered_date']=$patQryRes['paid_date'];
			}

			$groupArr[$grpId] = $grpId;
			$tempPreFac[$id] = $facility;
			$tempPrePhy[$id] = $phyId;
			$tempPreOper[$id] = $oprId;
			
		}
	}

	// GET PRE PAT ENCOUNTER APPLIED AMTS
	if(count($arrDepIds)>0){
		$strDepIds=implode(',', $arrDepIds);
		$preAppQry="Select payChgDet.patient_pre_payment_id, payChgDet.paidForProc FROM patient_chargesheet_payment_info payChg  
		JOIN patient_charges_detail_payment_info payChgDet ON payChgDet.payment_id = payChg.payment_id
		WHERE payChgDet.patient_pre_payment_id IN($strDepIds)";
		if($DateRangeFor=='dot'){
			$preAppQry.=" AND (payChg.transaction_date BETWEEN '".$startDate."' and '".$endDate."')";
		}else{
			$preAppQry.=" AND (payChg.date_of_payment BETWEEN '".$startDate."' and '".$endDate."')";
		}
		$preAppQry.="
		AND ((payChgDet.deletePayment='0' OR (payChgDet.deletePayment='1' AND payChgDet.deleteDate>'".$endDate."'))
		AND (payChgDet.unapply='0' OR (payChgDet.unapply='1' AND DATE_FORMAT(payChgDet.unapply_date, '%Y-%m-%d')>'".$endDate."')))";
		
		$preAppRs=imw_query($preAppQry)or die(imw_error().'_715');
		while($preAppRes=imw_fetch_array($preAppRs)){
			$id = $preAppRes['patient_pre_payment_id'];
			$tempData[$id]['APPLIED_AMT']+= $preAppRes['paidForProc'];
		}
	}
	// GROUPING OF DATA
	if(sizeof($groupArr)>0){
		$groupStr = implode(',', $groupArr);
		$groupArr=array();
		$groupArr[0]=0;
		$qry="Select id FROM users WHERE id IN(".$groupStr.") ORDER by lname, fname";
		if($groupBy=='facility'){
			if($pay_location=='1'){
				$qry="Select id,name from facility WHERE id IN(".$groupStr.") ORDER BY name";
			}else{
				$qry="Select pos_facility_id as 'id' from pos_facilityies_tbl WHERE pos_facility_id IN(".$groupStr.") ORDER BY facilityPracCode";
			}
		}
		$rs=imw_query($qry)or die(imw_error().'_731');
		while($res=imw_fetch_array($rs)){
			$groupArr[$res['id']] = $res['id'];
		}
	}
	// PRE PAYMENTS FINAL ARRAY
	$arrPrePayNotApplied=array();
	foreach($groupArr as $grpId){
		foreach($arrAllIdsData[$grpId] as $id => $grpData){
			$balance_amount= floatval($tempData[$id]['PAT_DEPOSIT']) - floatval($tempData[$id]['APPLIED_AMT']);
			$dot=$grpData['entered_date'];
			if($balance_amount>0){
				$pay_mode= $grpData['pay_mode'];
				
				$facility = $tempPreFac[$id];
				$physician = $tempPrePhy[$id];
				$opr_id = $tempPreOper[$id];
				
				$arrPayModeAmts[$pay_mode]+=$balance_amount-$tempData[$id]['PAT_DEPOSIT_REF']; 
				
				if($pay_mode=='Credit Card' && sizeof($tempCCTypeAmts[$id])>0){
					foreach($tempCCTypeAmts[$id] as $ccType){
						$arrCCModeAmts[$ccType]+=$balance_amount;
					}
					$arrCCModeAmts[$ccType]-=$tempData[$id]['PAT_DEPOSIT_REF'];
				}
				
				if($process=='Summary'){
					if($showFacCol){
						if($pay_location=='1'){
							$arrPrePayNotApplied[$facility][$grpId][$pay_mode]+=(floatval($balance_amount)-floatval($tempData[$id]['PAT_DEPOSIT_REF']));
							$arrPrePayNotApplied[$facility][$grpId][$pay_mode.'_is_ref']=($tempData[$id]['PAT_DEPOSIT_REF']>=1 && $arrPrePayNotApplied[$grpId][$facility][$pay_mode.'_is_ref']=='')?$pay_mode:0;
							$arrPrePayNotApplied[$facility][$grpId][$pay_mode.'_ref_amt']+=$tempData[$id]['PAT_DEPOSIT_REF'];
						}else{
							$arrPrePayNotApplied[$grpId][$facility][$pay_mode]+=(floatval($balance_amount)-floatval($tempData[$id]['PAT_DEPOSIT_REF']));
							$arrPrePayNotApplied[$grpId][$facility][$pay_mode.'_is_ref']=($tempData[$id]['PAT_DEPOSIT_REF']>=1 && $arrPrePayNotApplied[$grpId][$facility][$pay_mode.'_is_ref']=='')?$pay_mode:0;
							$arrPrePayNotApplied[$grpId][$facility][$pay_mode.'_ref_amt']+=$tempData[$id]['PAT_DEPOSIT_REF'];
						}
					}elseif($groupBy=='department'){
						$arrDepartmentView[$grpId][$facility]['pre_paid'][$pay_mode]+=(floatval($balance_amount)-floatval($tempData[$id]['PAT_DEPOSIT_REF']));
						$arrDepartmentView[$grpId][$facility]['pre_paid'][$pay_mode.'_is_ref']=($tempData[$id]['PAT_DEPOSIT_REF']>=1 && $arrPrePayNotApplied[$grpId][$facility][$pay_mode.'_is_ref']=='')?$pay_mode:0;
						$arrDepartmentView[$grpId][$facility]['pre_paid'][$pay_mode.'_ref_amt']+=$tempData[$id]['PAT_DEPOSIT_REF'];
					}else{
						if($pay_location=='1'){
							$arrPrePayNotApplied[$facility][$grpId][$pay_mode]+=(floatval($balance_amount)-floatval($tempData[$id]['PAT_DEPOSIT_REF']));						
							$arrPrePayNotApplied[$facility][$grpId][$pay_mode.'_is_ref']=($tempData[$id]['PAT_DEPOSIT_REF']>=1 && $arrPrePayNotApplied[$grpId][$facility][$pay_mode.'_is_ref']=='')?$pay_mode:0;
							$arrPrePayNotApplied[$facility][$grpId][$pay_mode.'_ref_amt']+=$tempData[$id]['PAT_DEPOSIT_REF'];
						}else{
							$arrPrePayNotApplied[$grpId][$pay_mode]+=(floatval($balance_amount)-floatval($tempData[$id]['PAT_DEPOSIT_REF']));						
							$arrPrePayNotApplied[$grpId][$pay_mode.'_is_ref']=($tempData[$id]['PAT_DEPOSIT_REF']>=1 && $arrPrePayNotApplied[$grpId][$facility][$pay_mode.'_is_ref']=='')?$pay_mode:0;
							$arrPrePayNotApplied[$grpId][$pay_mode.'_ref_amt']+=$tempData[$id]['PAT_DEPOSIT_REF'];
						}
					}
				}else{
					$arrMainData[$pay_mode][$dot]['pre_pay'][$id]['pat_name']=$grpData['pat_name'];
					$arrMainData[$pay_mode][$dot]['pre_pay'][$id]['facility']=$facility;
					$arrMainData[$pay_mode][$dot]['pre_pay'][$id]['physician']=$physician;
					$arrMainData[$pay_mode][$dot]['pre_pay'][$id]['operator']=$opr_id;
					$arrMainData[$pay_mode][$dot]['pre_pay'][$id]['dop']=$dot;
					$arrMainData[$pay_mode][$dot]['pre_pay'][$id]['paid_by']='Patient';							
					$arrMainData[$pay_mode][$dot]['pre_pay'][$id]['payment']+=(floatval($balance_amount)-floatval($tempData[$id]['PAT_DEPOSIT_REF']));
					$arrMainData[$pay_mode][$dot]['pre_pay'][$id]['is_ref']=($tempData[$id]['PAT_DEPOSIT_REF']>=1)?$pay_mode:0;
					$arrMainData[$pay_mode][$dot]['pre_pay'][$id]['ref_amt']=$tempData[$id]['PAT_DEPOSIT_REF'];
				}
			}
			
			//MANUALLY APPLIED AMOUNT ARRAY
			if($tempPrePaidManuallyApplied[$id]>0){
				if($process=='Summary'){
					$arrManuallyApplied[$grpId]['pre_payment']+= $tempPrePaidManuallyApplied[$id];
				}else{
					$arrManuallyData[$dot]['pre_pay'][$id]['pat_name']=$grpData['pat_name'];
					$arrManuallyData[$dot]['pre_pay'][$id]['facility']=$facility;
					$arrManuallyData[$dot]['pre_pay'][$id]['physician']=$physician;
					$arrManuallyData[$dot]['pre_pay'][$id]['dop']=$dot;
					$arrManuallyData[$dot]['pre_pay'][$id]['paid_by']='patient';												
					$arrManuallyData[$dot]['pre_pay'][$id]['applied_amt'] = $tempPrePaidManuallyApplied[$id];
				}
			}
		}	
	}
	unset($tempPreFac);
	unset($tempPrePaidManuallyApplied);
	ksort($arrPrePayCCTypeAmts);
	//END NOT APPLIED AMOUNTS

	//--------------- DELETED PAYMENTS -------------
	//--- GET POSTED PAYMENT
	$strPostedChgDetIds=implode(',', $arrPostedChgDetIds);
	$strPostedEncDetIds=implode(',', $arrPostedEncDetIds);
	$time_part='';
	if($hourFrom!='' && $hourTo!=''){
		$time_part=" AND deleteTime>'$hourTo'";
	}

	$qry = "SELECT patChg.patient_id, 
	patChg.facility_id, patChg.billing_facility_id,
	patChg.primary_provider_id_for_reports as 'primaryProviderId', payChg.creditCardCo,
	DATE_FORMAT(patChg.date_of_service, '".$dateFormat."') as 'date_of_service', patChg.del_status,
	payChg.encounter_id, 
	payChgDet.charge_list_detail_id, 
	payChg.transaction_date, 
	payChg.payment_mode,
	DATE_FORMAT(payChg.transaction_date, '".$dateFormat."') as 'transaction_date',
	DATE_FORMAT(payChg.date_of_payment, '".$dateFormat."') as 'date_of_payment',
	payChgDet.paidBy, 
	payChgDet.paidForProc, 
	payChgDet.overPayment, 
	payChgDet.operator_id, 
	payChgDet.deletePayment, DATE_FORMAT(payChgDet.deleteDate, '".$dateFormat."') as 'delete_date',
	payChgDet.del_operator_id,
	payChg.paymentClaims, 
	pd.fname, 
	pd.mname, 
	pd.lname  
	FROM patient_chargesheet_payment_info payChg 
	JOIN patient_charges_detail_payment_info payChgDet ON payChgDet.payment_id = payChg.payment_id 
	JOIN patient_charge_list patChg ON patChg.encounter_id = payChg.encounter_id";
	if($groupBy == "physician"){
		$qry.=" LEFT JOIN users ON users.id = patChg.primary_provider_id_for_reports";
	}else if($groupBy == "operator"){
		$qry.=" LEFT JOIN users ON users.id = payChgDet.operator_id";
	}
	$qry.=" 
		JOIN patient_data pd ON pd.id = patChg.patient_id 
		LEFT JOIN pos_facilityies_tbl ON pos_facilityies_tbl.pos_facility_id = patChg.facility_id
		LEFT JOIN facility ON facility.id = payChg.facility_id	
		WHERE pd.id IS NOT NULL ";
	if($consolidation==1){
		if($DateRangeFor=='dot'){
			$qry.=" AND payChgDet.deletePayment='1' AND (deleteDate BETWEEN '$startDate' AND '$endDate') AND payChg.transaction_date<'$startDate' ".$time_part;
		}else{
			$qry.=" AND payChgDet.deletePayment='1' AND (deleteDate BETWEEN '$startDate' AND '$endDate') AND payChg.date_of_payment<'$startDate' ".$time_part;
		}
	}else{
		if($DateRangeFor=='dot'){
			if($hourFrom!='' && $hourTo!=''){
				$qry.=" AND payChgDet.deletePayment='1' AND (deleteDate BETWEEN '$startDate' AND '$endDate') AND deleteTime>'$hourTo'";
			}else{
				$qry.=" AND payChgDet.deletePayment='1' AND (deleteDate BETWEEN '$startDate' AND '$endDate')";
			}
		}else{
			if(empty($strPostedChgDetIds)==false){
				if($hourFrom!='' && $hourTo!=''){
					$qry.=" AND payChgDet.charge_list_detail_id IN(".$strPostedChgDetIds.") AND payChgDet.deletePayment='1' AND deleteDate>'$endDate' AND deleteTime>'$hourTo' 
					 AND (payChg.date_of_payment between '$startDate' and '$endDate')";
				}else{
					$qry.=" AND IF(payChgDet.charge_list_detail_id>0, payChgDet.charge_list_detail_id IN(".$strPostedChgDetIds."), payChg.encounter_id IN(".$strPostedEncDetIds.")) 
					AND payChgDet.deletePayment='1' AND deleteDate>'$endDate' AND (payChg.date_of_payment between '$startDate' and '$endDate')";
				}
			}else{
				$qry.=" AND 1=2"; //SO THAT QUERY WILL NOT RETURN ANY RESULT.
			}
		}
	}
	if(empty($pay_method)==false){ //PAYMENT MODE
		$qry.=" AND LOWER(payChg.payment_mode)='".$pay_method."'";
	}
	if(empty($cc_type_str)==false){ //IF CREDIT CARD
		$qry.=" AND LOWER(payChg.creditCardCo) IN(".$cc_type_str.")";
	}
	if($Physician != ""){
		$wherePart .= " AND patChg.primary_provider_id_for_reports in($Physician)";
	}
	if($sc_name != ""){
		if($pay_location=='1'){
			$qry .= " AND patChg.billing_facility_id in($schFacId)";
		}else{
			$qry .= " AND patChg.facility_id in($sc_name)";
		}
	}
	if($grp_id != ""){
		$qry .= " AND patChg.gro_id in($grp_id)";
	}
	if($operator != ""){
		$qry .= " AND payChgDet.del_operator_id in($operator)";
	}
	$qry.= $orderByPart;

	$rs = imw_query($qry)or die(imw_error());
	$tempDelPostedPay=array();
	$arrDelAmounts=array();
	$arrDelTemp=array();
	while($res = imw_fetch_array($rs)){
		$eid = $res['encounter_id'];
		$del_date=$res['delete_date'];
		$entered_date= ($DateRangeFor=='dot')? $res['transaction_date'] :$res['date_of_payment'];
		
		$printFile=true;
		$paidAmt=0;
		$pid = $res['patient_id'];

		$chgDetId = $res['charge_list_detail_id'];
		$paidBy = strtolower($res['paidBy']);
		$payMode = strtolower($res['payment_mode']);
		$payMode = str_replace(' ', '_', $payMode);
		if($payMode!='cash' && $payMode!='check' && $payMode!='eft' && $payMode!='veep' && 
			$payMode!='money_order' && $payMode!='credit_card' && $payMode!='byPatient' && $payMode!='byInsurance'){
			$payMode = 'other';
		}
		
		$phyId = $res['primaryProviderId'];
		$facId = ($pay_location=='1') ? $res['billing_facility_id'] :$res['facility_id'];
		$oprId = $res['operator_id'];
		$patName = core_name_format($res['lname'], $res['fname'], $res['mname']).' - '.$pid;
		$encounterIdDelArr[$eid] = $eid;
	
		$grpId = $phyId;
		$grpId = ($groupBy=='operator') ? $oprId : $grpId; 
		
		$paidAmt = $res['paidForProc'] + $res['overPayment'];
		if($res['paymentClaims'] == 'Negative Payment'){
			$paidAmt= '-'.$paidAmt;
		}
		
		if($process=='Summary'){
			if($showFacCol){
				if($pay_location=='1'){
					$arrDelAmounts[$facId][$grpId]['posted']+= $paidAmt;
				}else{
					$arrDelAmounts[$grpId][$facId]['posted']+= $paidAmt;					
				}
			}else{
				if($pay_location=='1'){
					$arrDelAmounts[$facId][$grpId]['posted']+= $paidAmt; 
				}else{
					$arrDelAmounts[$grpId]['posted']+= $paidAmt; 
				}
			}
		}else{
			$arrDelAmounts[$del_date]['posted'][$eid]['pat_name']=$patName;
			$arrDelAmounts[$del_date]['posted'][$eid]['entered_date']=$entered_date;
			$arrDelAmounts[$del_date]['posted'][$eid]['del_opr']=$res['del_operator_id'];
			$arrDelAmounts[$del_date]['posted'][$eid]['del_amount']+= $paidAmt;
		}
	} 
	unset($rs);

	//GET DELETED CI/CO
	$arrDelCICONotApplied=array();
	$tempDelCCTypeAmts=array();
	
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
	DATE_FORMAT(cioPay.created_on, '".$dateFormat."') as 'created_on', 
	cioPay.payment_method, cioPay.cc_type,
	cioPay.delete_operator_id, cioPay.created_time, 
	cioPayDet.item_payment, DATE_FORMAT(cioPayDet.delete_date, '".$dateFormat."') as 'delete_date',
	pd.fname, pd.mname, pd.lname 
	FROM schedule_appointments sa 
	JOIN check_in_out_payment cioPay ON cioPay.sch_id  = sa.id 
	JOIN check_in_out_payment_details cioPayDet ON cioPayDet.payment_id= cioPay.payment_id
	".$cioJoin."
	JOIN patient_data pd ON pd.id = cioPay.patient_id 
	WHERE cioPayDet.item_payment>0 AND pd.id IS NOT NULL ";
	
	if($consolidation==1){
		if($hourFrom!='' && $hourTo!=''){		
			$qry.=" AND cioPayDet.status=1 AND (cioPayDet.delete_date BETWEEN '".$startDate."' AND '".$endDate."') AND cioPay.created_on<'".$startDate."' AND cioPayDet.delete_time>'".$hourTo."'";		
		}else{
			$qry.=" AND cioPayDet.status=1 AND (cioPayDet.delete_date BETWEEN '".$startDate."' AND '".$endDate."') AND cioPay.created_on<'".$startDate."'";
		}
	}else{
		if($hourFrom!='' && $hourTo!=''){		
			$qry.=" AND cioPayDet.status=1 AND (cioPayDet.delete_date BETWEEN '".$startDate."' AND '".$endDate."') AND cioPayDet.delete_time>'".$hourTo."'";		
		}else{
			$qry.=" AND cioPayDet.status=1 AND (cioPayDet.delete_date BETWEEN '".$startDate."' AND '".$endDate."')";
		}
	}
	if(empty($sc_name) === false){
		if(empty($schFacId) ===false){
			$qry.= " AND sa.sa_facility_id IN(".$schFacId.")";
		} else{
			$qry.=" AND 1=2"; //SO THAT QUERY WILL NOT RETURN ANY RESULT.
		}
	}
	if(empty($Physician) === false){
		$qry.= " AND sa.sa_doctor_id IN(".$Physician.")";
	}
	if(empty($operator) === false){
		$qry.= " AND cioPay.delete_operator_id IN(".$operator.")";
	}
	if(empty($pay_method)==false){ //PAYMENT MODE
		$qry.=" AND LOWER(cioPay.payment_method)='".$pay_method."'";
	}
	if(empty($cc_type_str)==false){ //IF CREDIT CARD
		$qry.=" AND LOWER(cioPay.cc_type) IN(".$cc_type_str.")";
	}
	
	$qry.= $cioOrderBy;
	$rs=imw_query($qry)or die(imw_error());
	while($res=imw_fetch_array($rs)){
		
		$printFile=true;
		$pid = $res['patient_id'];
		$del_date=$res['delete_date'];
		$payment_id = $res['payment_id'];
		$phyId = $res['sa_doctor_id'];
		$sch_facility = $res['sa_facility_id'];
		$patName = core_name_format($res['lname'], $res['fname'], $res['mname']).' - '.$pid;
		$facility = ($pay_location=='1')? $sch_facility : $sch_pos_fac_arr[$sch_facility];
		if($facility=='' || $facility<=0) { $facility=0; }
		
		$grpId = $phyId;
		if($groupBy=='operator'){ $grpId = $oprId;	}
		
		if($process=='Summary'){
			if($showFacCol){
				if($pay_location=='1'){
					$arrDelAmounts[$facility][$grpId]['cico']+=$res['item_payment'];
				}else{
					$arrDelAmounts[$grpId][$facility]['cico']+=$res['item_payment'];
				}
			}else{
				if($pay_location=='1'){
					$arrDelAmounts[$facility][$grpId]['cico']+=$res['item_payment'];
				}else{
					$arrDelAmounts[$grpId]['cico']+=$res['item_payment'];
				}
			}
		}else{
			$arrDelAmounts[$del_date]['cico'][$payment_id]['pat_name']=$patName;
			$arrDelAmounts[$del_date]['cico'][$payment_id]['entered_date']=$res['created_on'];
			$arrDelAmounts[$del_date]['cico'][$payment_id]['del_opr']=$res['delete_operator_id'];
			$arrDelAmounts[$del_date]['cico'][$payment_id]['del_amount']+=$res['item_payment'];
		}
	}
	//END DELETED CI/CO	

	// GET PATIENT PRE PAYMENTS
	$groupArr=array();
	$tempCCTypeAmts=array();
	$patQryRes = array();
	$qry="Select pDep.id, pDep.patient_id, pDep.paid_amount, pDep.facility_id, 
	pDep.apply_payment_date, pData.default_facility, pDep.apply_payment_type, pDep.apply_amount, pDep.provider_id,
	DATE_FORMAT(pDep.entered_date, '".$dateFormat."') as 'entered_date', DATE_FORMAT(pDep.paid_date, '".get_sql_date_format()."') as 'paid_date',
	DATE_FORMAT(pDep.trans_del_date,'".$dateFormat."') as 'trans_del_date',
	pDep.del_operator_id, pDep.payment_mode, pDep.credit_card_co,
	pData.fname, pData.mname, pData.lname 
	FROM patient_pre_payment pDep 
	JOIN patient_data pData ON pData.id = pDep.patient_id 
	WHERE 1=1 AND pData.id IS NOT NULL ";
	if($consolidation==1){
		$qry.=" AND pDep.del_status='1' AND (DATE_FORMAT(pDep.trans_del_date, '".$formatDelDate."') BETWEEN '".$startDate."' AND '".$endDate."') AND pDep.entered_date<'".$startDate."'";
	}else{
		$qry.=" AND pDep.del_status='1' AND (DATE_FORMAT(pDep.trans_del_date, '".$formatDelDate."') BETWEEN '".$startDate."' AND '".$endDate."')";
	}
	if(empty($Physician) === false){
		$qry.= " AND pDep.provider_id IN(".$Physician.")";
	}
	if(empty($sc_name) === false){
		if(empty($schFacId) ===false){
			$qry .= " AND pDep.facility_id in($schFacId)";
		} else {
			$qry.=" AND 1=2"; //SO THAT QUERY WILL NOT RETURN ANY RESULT.
		}
	}
	if(empty($operator) === false){
		$qry .= " AND pDep.del_operator_id in($operator)";
	}
	if(empty($pay_method)==false){ //PAYMENT MODE
		$qry.=" AND LOWER(pDep.payment_mode)='".$pay_method."'";
	}
	if(empty($cc_type_str)==false){ //IF CREDIT CARD
		$qry.=" AND LOWER(pDep.credit_card_co) IN(".$cc_type_str.")";
	}
	
	$qry.=" ORDER BY pData.lname, pData.fname";
	$patQry = imw_query($qry);
	while($row = imw_fetch_assoc($patQry)){
		$patQryRes[] = $row;
	}
	$arrDepIds=array();
	$tempData=array(); $arrDepIds=array(); $arrAllIds=array(); $arrAllIdsData=array();
	
	for($i=0; $i<sizeof($patQryRes); $i++){
		$printFile=true;
		$facility=0; $balance_amount=0; $doc_id=0;$refundAmt=0;
		$date='';
		
		$del_date=$patQryRes[$i]['trans_del_date'];
		$pid = $patQryRes[$i]['patient_id'];
		$facility= ($pay_location=='1') ? $patQryRes[$i]['facility_id'] : $sch_pos_fac_arr[$patQryRes[$i]['facility_id']];
		$phyId=$patQryRes[$i]['provider_id'];
		$oprId=$patQryRes[$i]['entered_by'];
		$patName = core_name_format($patQryRes[$i]['lname'], $patQryRes[$i]['fname'], $patQryRes[$i]['mname']).' - '.$pid;
		
		//if($phyId<=0){ 		$phyId = $arrPatProv[$patQryRes[$i]['patient_id']];	}
		if($facility<=0 || $facility==''){ $facility= ($biling_facility=='1') ? $headSchFacility : $headPosFacility; }

		$grpId= $oprId;
		if($grpId<=0){ $grpId=0; } 
		
		$id= $patQryRes[$i]['id'];
				
		if($DateRangeFor=='dot'){
			$date = $patQryRes[$i]['entered_date'];
		}else{
			$date = $patQryRes[$i]['paid_date'];
		}

		if($process=='Summary'){
			if($showFacCol){
				if($pay_location=='1'){
					$arrDelAmounts[$facility][$grpId]['pre_payment']+= $patQryRes[$i]['paid_amount'];
				}else{
					$arrDelAmounts[$grpId][$facility]['pre_payment']+= $patQryRes[$i]['paid_amount'];
				}
			}else{
				if($pay_location=='1'){
					$arrDelAmounts[$facility][$grpId]['pre_payment']+= $patQryRes[$i]['paid_amount'];
				}else{
					$arrDelAmounts[$grpId]['pre_payment']+= $patQryRes[$i]['paid_amount'];
				}
			}
		}else{
			$arrDelAmounts[$del_date]['pre_pay'][$id]['pat_name']=$patName;
			$arrDelAmounts[$del_date]['pre_pay'][$id]['entered_date']=$date;
			$arrDelAmounts[$del_date]['pre_pay'][$id]['del_opr']=$patQryRes[$i]['del_operator_id'];
			$arrDelAmounts[$del_date]['pre_pay'][$id]['del_amount']+=$patQryRes[$i]['paid_amount'];
		}
	}
	
	
	//pre($arrDelAmounts);
	
	//SORTING OF DELETED AMOUNTS
/* 	$arrKeys=array_keys($arrDelAmounts);
	if(sizeof($arrKeys)>0){
		$str=implode(',', $arrKeys);
		$qry="Select id FROM users WHERE id IN(".$str.") ORDER BY lname,fname";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$delSortedGroup[$res['id']]=$res['id'];
		}unset($rs);
	} */
	//END DELETED PRE-PAYMENTS
	if($printFile==true){
		$page_content='';

		//--- PAGE HEADER DATA ---
		$dateRangeFor=strtoupper($DateRangeFor);
		$curDate = date(''.$phpDateFormat.' H:i A');
		$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$op_name = $op_name_arr[1][0];
		$op_name .= $op_name_arr[0][0];

		$facilitySelected='All';
		$doctorSelected='All';
		if(sizeof($arrDoctorSel)>0){
			$doctorSelected = (sizeof($arrDoctorSel)>1) ? 'Multi' : $providerNameArr[$Physician];  
		}
		
		$sel_pay_method= ucwords($pay_method);
		$sel_cc_type= ucwords($sel_cc_type);		
		if($sel_pay_method=='')$sel_pay_method='All';
		if($sel_cc_type=='')$sel_cc_type='All';		
		
		//MAIN HEADER PDF
		$mainHeaderPDF='
		<table width="100%" border="0" cellpadding="3" cellspacing="1" class="rpt_padding">
			<tr valign="top">
				<td style="width:258px;text-align:left" class="rpt_headers rptbx1">Day Close Payment Report ('.$process.')</td>
				<td style="width:258px;text-align:left" class="rpt_headers rptbx2">Selected Group: '.$sel_grp.'</td>
				<td style="width:258px;text-align:left" class="rpt_headers rptbx3">Selected '.$dateRangeFor.' ('.$Start_date.' - '.$End_date.') Time:'.$hourFromL.'-'.$hourToL.'</td>
				<td style="width:258px;text-align:left" class="rpt_headers rptbx1">Created by: '.$op_name.' on '.$curDate.'</td>
			</tr>	
			<tr valign="top">
				<td class="rpt_headers rptbx1">Selected Facility: '.$sel_fac.'</td>
				<td class="rpt_headers rptbx2">Selected Physician: '.$sel_phy.'</td>
				<td class="rpt_headers rptbx3">Selected Operator: '.$sel_opr.'</td>
				<td class="rpt_headers rptbx1">Selected Dept.: '.$sel_dept.'</td>
			</tr>					
			<tr valign="top">
				<td class="rptbx1">Selected Payment Method: '.$sel_pay_method.'</td>
				<td class="rptbx2">Consolidation: '.$sel_consolidation.'</td>
				<td class="rptbx3"></td>
				<td class="rptbx1"></td>
			</tr>					
		</table>';
		
		if($process=='Summary'){
			//require_once(dirname(__FILE__)."/day_close_payment_summary.php");
		}else{
			require_once(dirname(__FILE__)."/day_close_payment_details.php");
		}
		
		if(trim($page_content) != ''){				
			$tooltip='Red coloured CI/CO and Pre-Payments represents that there is refund amount deducted from these payments.';
			
			$page_content .= '<table width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8" >
					<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
					<tr><td style="width:20px;" style="background-color:#FFFFFF;">&nbsp;</td>
					<td style="width:4px;" height="5px;" bgcolor="#FF0000"></td>
					<td class="info" style="padding-left:20px; ; background-color:#FFFFFF;">
					'.$tooltip.'
					<br>Refund amount can be view by mouse over on red coloured amount.
					</td>
					</tr>
					</table>';
					
			$pdf_content .= '<table width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8" >
					<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
					<tr><td style="width:20px;background-color:#FFFFFF;">&nbsp;</td>
					<td style="width:4px;" height="5px;" bgcolor="#FF0000">&nbsp;</td>
					<td class="info" style="padding-left:20px; background-color:#FFFFFF;">
					'.$tooltip.'
					</td>
					</tr>
					</table>';
					
	
			$html_page_content = '';
			if($process=='Detail'){
				$html_page_content .= $pdf_content;
			}else{
				$html_page_content.='
				<page backtop="13mm" backbottom="10mm">			
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
					'.$mainHeaderPDF.'
				</page_header>
				'.$pdf_content.'
				</page>';
			}			
			$op='l';
			
			//--- CSV FILE DATA --
			$page_content = <<<DATA
				<table class="rpt_table rpt rpt_table-bordered rpt_padding">
					<tr >
						<td class="rptbx1" style="width:25%">
							Day Close Payment Report ($process)
						</td>
						<td class="rptbx2" style="width:25%">
							Selected Group: $sel_grp
						</td>
						<td class="rptbx3" style="width:25%">
							Selected $dateRangeFor ($Start_date $End_date) Time:$hourFromL-$hourToL
						</td>
						<td class="rptbx1" style="width:25%">
							Created by: $op_name on $curDate
						</td>
					</tr>	
					<tr>
						<td class="rptbx1" style="width:25%">
							Selected Facility: $sel_fac
						</td>
						<td class="rptbx2" style="width:25%">
							Selected Physician: $sel_phy
						</td>
						<td class="rptbx3" style="width:25%">
							Selected Operator: $sel_opr
						</td>
						<td class="rptbx1" style="width:25%">
							Selected Department: $sel_dept
						</td>
					</tr>
					<tr valign="top">
						<td class="rptbx1">
							Selected Payment Method: $sel_pay_method
						</td>
						<td class="rptbx2">Consolidation: $sel_consolidation</td>
						<td class="rptbx3"></td>
						<td class="rptbx1"></td>
					</tr>					
				</table>
				$page_content
DATA;
		}
	}
	$conditionChk = true;
}

$HTMLCreated=0;
if($printFile == true and $page_content != ''){
	$HTMLCreated=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$page_content;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$html_page_content;

	$file_location = write_html($strHTML, 'payments_report.html');
}else{
	if($callFrom!='scheduled'){
		$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}

if($callFrom!='scheduled'){
	if($output_option=='output_pdf'){
		if($printFile == true and $page_content != ''){
			echo '<div class="text-center alert alert-info">PDF generated in separate window.</div>';
		}else{
			echo $csv_file_data;
		}
	}else{
		echo $csv_file_data;
	}
}

?>