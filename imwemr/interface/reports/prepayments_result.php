<?php
$without_pat = "yes";
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

$page_data = '';	$printFile= true;

if(empty($Submit) === false){
	$printFile = false;

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

	$arrFacilitySel=array();
	$arrDoctorSel=array();
	$sel_grp = $CLSReports->report_display_selected(implode(',',$groups),'group',1,$grp_cnt);
	$sel_grp = (strlen($sel_grp)>25) ? substr($sel_grp,0, 23).'...' : $sel_grp;
	$sel_fac = $CLSReports->report_display_selected(implode(',',$facility_name),'facility_tbl',1,$fac_cnt);
	$sel_phy = $CLSReports->report_display_selected(implode(',',$phyId),'physician',1,$phy_cnt);
	$sel_opr = $CLSReports->report_display_selected(implode(',',$operator_id),'operator',1,$opr_cnt);
	$sel_method= (empty($pay_method)==false) ? strtoupper($pay_method) : 'All';
	$Process =  strtolower($summary_detail);	

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
	pDep.del_operator_id, pDep.payment_mode, pDep.check_no, pDep.credit_card_co as cc_type,
	DATE_FORMAT(pDep.trans_del_date, '".get_sql_date_format()."') as 'delDate', pDep.facility_id, pDep.comment, 
	pData.default_facility, 
	pData.fname, pData.mname, pData.lname 
	FROM patient_pre_payment pDep 
	JOIN patient_data pData ON pData.id = pDep.patient_id 
	LEFT JOIN facility ON facility.id = pDep.facility_id 
	WHERE pData.id IS NOT NULL 
	AND (pDep.del_status='0' OR (pDep.del_status='1' AND DATE_FORMAT(pDep.trans_del_date, '%Y-%m-%d')>'$en_date'))";
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
	
	$arrDepIds=array();
	$arrPrePayData = array();
	$arrPrePayDelData = array();
	while($res = imw_fetch_assoc($qryRes)){	
		$refundAmt=0;
		$patient_name = core_name_format($res['lname'], $res['fname'], $res['mname']);
		//PRABH: GET REFUND AMOUNTS

		$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND pmt_id = '".$res['id']."' AND (entered_date BETWEEN '".$st_date."' AND '".$en_date."')")or die(imw_error().'_656');
		while($rsRef=imw_fetch_array($qryRef))
		{
			$refundAmt+=$rsRef['ref_amt'];
		}
		imw_free_result($qryRef);
		
		$hasData=1;
		$facility=''; $check_no = ''; $payment_method = '';	$docNameArr= array();	$patNameArr= array();
		$patId = $res['patient_id'];
		$oprId = $res['entered_by'];
		$id= $res['id'];
		$paidDate = $res['paidDate'];
		$enterDate = $res['entered_date'];
		$facility=$res['facility_id'];
		
		$check_no = (empty($res['check_no']) === false) ? '- '.$res['check_no'] : '';
		$payment_method = $res['payment_mode'].$check_no; 
		if(strtolower($res['payment_mode'])=='credit card'){
			$res['cc_type'] = $res['cc_type'] ? 'CC-'.$res['cc_type'] : 'Credit Card';
			$payment_method = $res['cc_type'];
		}
		$prepayment_method_arr[$patId][$id] = $prepayArrVal;
		
		$hasData = 1;
		$arrPrePayData[$oprId][$facility]['payment'] += $res['paid_amount']-$refundAmt;
		$arrPrePayData[$oprId][$facility]['payment_ref'] += $refundAmt;
		//---DETAIL ARRAY---
		$arrPatPayDetail[$id]['pat_id']=$patId;
		$arrPatPayDetail[$id]['pat_name']=$patient_name;
		$arrPatPayDetail[$id]['entered_date']=$enterDate;
		$arrPatPayDetail[$id]['payment_date']=$paidDate;
		$arrPatPayDetail[$id]['operator']=$oprId;
		$arrPatPayDetail[$id]['location']=$res['facility_id'];
		$arrPatPayDetail[$id]['comment']=$res['comment'];
		$arrPatPayDetail[$id]['payment_method'] = $payment_method;
		$arrPatPayDetail[$id]['payment'] += $res['paid_amount']-$refundAmt;
		$arrPatPayDetail[$id]['payment_ref'] +=$refundAmt;
		$arrPatPayDetail[$id]['pay_id'][] = $id;
		
		//merging pre payments 
		$payPreIncrVal =+ $res['paid_amount']-$refundAmt;
		$arrPatPayDetail[$id]['PAYMENT_ROW']['PrePay']['method'][] = $prepayArrVal;
		$arrPatPayDetail[$id]['PAYMENT_ROW']['PrePay']['pay'][] = $payPreIncrVal;
		
		//------------------
		if($res['apply_payment_type'] == 'manually'){

			//-------ADD MANUALLY APPLIED PRE PAYMENTS-------------------
			$arrPrePayData[$oprId][$facility]['applied'] += $res['apply_amount'];
			//--DETAIL ARRAY-
			$arrPatPayDetail[$id]['applied']+= $res['apply_amount'];

			//SUMMARY
			$arrManuallyApplied[$oprId]['payment']+= $res['apply_amount'];
			//DETAIL
			$arrPrePayManuallyApplied[$oprId][$id]['pat_name']=$patId;
			$arrPrePayManuallyApplied[$oprId][$id]['entered_date']=$paidDate;
			$arrPrePayManuallyApplied[$oprId][$id]['applied_amt'] = $res['apply_amount'];
		}
		$arrPrePayData[$oprId][$facility]['pre_pay_id'][] = $id;
		$arrDepIds[$id]=$id;

		//PAYMENT BREAKDOWN
		$arrPayBreakdown[strtoupper($res['payment_mode'])]+= $res['paid_amount']-$refundAmt;
		if( 'credit card' == strtolower($res['payment_mode']) ){
			$arrCCPayBreakdown[strtoupper($res['cc_type'])]+= $res['paid_amount']-$refundAmt;
		}
	}

	
	// GET PRE PAT ENCOUNTER APPLIED AMTS
	$arrPrePayApplied = array();
	$arrPrePayAppliedId = array();
	if(count($arrDepIds)>0){
		$strDepIds=implode(',', $arrDepIds);
		$preAppQry="SELECT
		pDep.id, 
		pDep.entered_by,pDep.facility_id,
		patPayDet.paidForProc,
		patPayDet.unapply,
		patPayDet.deletePayment 
		FROM patient_pre_payment pDep 
		LEFT JOIN patient_charges_detail_payment_info patPayDet ON patPayDet.patient_pre_payment_id = pDep.id 
		WHERE pDep.id IN($strDepIds) 
		AND (patPayDet.deletePayment='0' OR (patPayDet.deletePayment='1' AND patPayDet.deleteDate>'".$en_date."')) 
		AND (patPayDet.unapply='0' OR (patPayDet.unapply='1' AND DATE_FORMAT(patPayDet.unapply_date, '%Y-%m-%d') >'".$en_date."'))";
		$preAppRs=imw_query($preAppQry);
		while($preAppRes=imw_fetch_array($preAppRs)){
			$id = $preAppRes['id'];
			$arrPrePayAppliedId[$id]+= $preAppRes['paidForProc'];
			$arrPrePayData[$preAppRes['entered_by']][$preAppRes['facility_id']]['applied']+= $preAppRes['paidForProc'];
		}
	}
	unset($tempData);


	// GET DELETED PATIENT PRE PAYMENTS
	$groupArr=array();
	$tempCCTypeAmts=array();
	$patQryRes = array();
	$qry="Select pDep.id, pDep.patient_id, pDep.paid_amount, pDep.facility_id, 
	pDep.apply_payment_date, pData.default_facility, pDep.apply_payment_type, pDep.apply_amount, pDep.provider_id,
	DATE_FORMAT(pDep.entered_date, '".get_sql_date_format()."') as 'entered_date', DATE_FORMAT(pDep.paid_date, '".get_sql_date_format()."') as 'paid_date',
	DATE_FORMAT(pDep.trans_del_date, '".get_sql_date_format()."') as 'deleted_date',
	pDep.entered_by, pDep.payment_mode, pDep.credit_card_co, pDep.comment,
	pData.fname, pData.mname, pData.lname 
	FROM patient_pre_payment pDep 
	JOIN patient_data pData ON pData.id = pDep.patient_id 
	WHERE 1=1 AND pData.id IS NOT NULL ";
	$qry.=" AND pDep.del_status='1' AND (DATE_FORMAT(pDep.trans_del_date, '%Y-%m-%d') BETWEEN '".$st_date."' AND '".$en_date."')";
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
	$arrDepIds=array();
	$tempData=array(); $arrDepIds=array(); $arrAllIds=array(); $arrAllIdsData=array();
	
	while($res=imw_fetch_assoc($patQry)){
		$hasData=1;
		$printFile=true;
		$facility=0; $balance_amount=0; $doc_id=0;$refundAmt=0;
		$pid = $res['patient_id'];
		$facility=$res['facility_id'];
		$phyId=$res['provider_id'];
		$oprId=$res['entered_by'];
		$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
		//if($facility<=0 || $facility==''){ $facility=$headPosFacility; }
		$grpId= $oprId;
		if($grpId<=0){ $grpId=0; } 
		
		$id= $res['id'];
		if($Process=='summary'){
			$arrDelAmounts[$oprId][$facility]['pre_payment']+= $res['paid_amount'];
		}else{
			$arrDelPrePayAmounts[$id]['pat_name']=$patName;
			$arrDelPrePayAmounts[$id]['operator']=$oprId;
			$arrDelPrePayAmounts[$id]['entered_date']=$res['entered_date'];
			$arrDelPrePayAmounts[$id]['paid_date']=$res['paid_date'];
			$arrDelPrePayAmounts[$id]['deleted_date']=$res['deleted_date'];
			$arrDelPrePayAmounts[$id]['del_amount']+=$res['paid_amount'];
			$arrDelPrePayAmounts[$id]['comment']=$res['comment'];
		}
	}


	
	if($Process=='summary'){
		unset($complete_page_data);
		include_once('prepayments_summary.php');
		$strHTML1 = $strHTML = $complete_page_data;
	}else{
		unset($complete_page_data);
		include_once('prepayments_detail.php');
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


$tooltip='Red coloured Pre-Payments represents that there is refund amount deducted from these payments.';
	
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
	
	if($output_option=='output_pdf'){
		$csv_file_data='<div class="text-center alert alert-info">PDF is generated in a separate window.</div>';
	}
	echo $csv_file_data;
	
}else{
	if($callFrom != 'scheduled'){
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}



?>