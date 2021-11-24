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
	$showFacCol = false;
	if($sc_name != ""){
		$showFacCol = true;
	}
	if($department != ""){
		$showFacCol = false;
	};
	
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
	DATE_FORMAT(patChg.date_of_service, '".get_sql_date_format()."') as 'date_of_service', patChg.del_status,
	payChg.encounter_id, 
	payChgDet.charge_list_detail_id, 
	payChg.transaction_date, 
	payChg.payment_mode, payChg.checkNo, payChg.creditCardNo,
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
	$tempPostedPay=array();
	$arrPostedPay=array();
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
			$paidBy = strtolower($res['paidBy']);
			$payMode = strtolower($res['payment_mode']);
			$payMode = str_replace(' ', '_', $payMode);
			if($payMode!='cash' && $payMode!='check' && $payMode!='eft' && $payMode!='veep' && $payMode!='money_order' && $payMode!='credit_card' && $payMode!='byPatient' && $payMode!='byInsurance'){
				$payMode = 'other';
			}
			
			$phyId = $res['primaryProviderId'];
			$facId = ($pay_location=='1')? $res['paid_facility'] : $res['facility_id'];
			$oprId = $res['operator_id'];
			$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
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
			
			
			if($groupBy!='department'){
				if($payMode=='credit_card')
				$arrPostedCCTypeAmts[$ccType]+=$paidAmt; 
				
				if($process=='Summary'){
					if($showFacCol){
						$firstgrp_id=$grpId;
						$secgrp_id=$facId;
						if($pay_location=='1'){ $firstgrp_id=$facId; $secgrp_id=$grpId; }
						
						$arrPostedPay[$firstgrp_id][$secgrp_id][$payMode]+= $paidAmt;
						if($paidBy=='patient'){
							$arrPostedPay[$firstgrp_id][$secgrp_id]['byPatient']+= $paidAmt;
						}else{
							$arrPostedPay[$firstgrp_id][$secgrp_id]['byInsurance']+= $paidAmt;
						}
					}else{
						if($pay_location=='1'){
							$arrPostedPay[$facId][$grpId][$payMode]+= $paidAmt; 
							if($paidBy=='patient'){
								$arrPostedPay[$facId][$grpId]['byPatient']+= $paidAmt;
							}else{
								$arrPostedPay[$facId][$grpId]['byInsurance']+= $paidAmt;
							}
						}else{
							$arrPostedPay[$grpId][$payMode]+= $paidAmt; 
							if($paidBy=='patient'){
								$arrPostedPay[$grpId]['byPatient']+= $paidAmt;
							}else{
								$arrPostedPay[$grpId]['byInsurance']+= $paidAmt;
							}
						}
					}
				}else{
					if($showFacCol){
						$firstgrp_id=$grpId;
						$secgrp_id=$facId;
						if($pay_location=='1'){ $firstgrp_id=$facId; $secgrp_id=$grpId; }
						
						$arrPostedPay[$firstgrp_id][$secgrp_id][$eid]['pat_name']=$patName;
						$arrPostedPay[$firstgrp_id][$secgrp_id][$eid]['dos']=$res['date_of_service'];
						$arrPostedPay[$firstgrp_id][$secgrp_id][$eid][$payMode]+= $paidAmt;
						if($res['checkNo']!='' || $res['creditCardNo']!=''){
							$ref_no=($res['checkNo']!='')?$res['checkNo']:substr($res['creditCardNo'],-4);
							$arrPostedPay[$firstgrp_id][$secgrp_id][$eid]['reference_no'][$ref_no]= $ref_no;
						}						

						if($paidBy=='patient'){
							$arrPostedPay[$firstgrp_id][$secgrp_id][$eid]['byPatient']+=$paidAmt;
						}else{
							$arrPostedPay[$firstgrp_id][$secgrp_id][$eid]['byInsurance']+=$paidAmt;
						}
					}
					else{
						if($pay_location=='1'){						
							$arrPostedPay[$facId][$grpId][$eid]['pat_name']=$patName;
							$arrPostedPay[$facId][$grpId][$eid]['dos']=$res['date_of_service'];
							$arrPostedPay[$facId][$grpId][$eid][$payMode]+= $paidAmt;
							if($res['checkNo']!='' || $res['creditCardNo']!=''){
								$ref_no=($res['checkNo']!='')?$res['checkNo']:substr($res['creditCardNo'],-4);
								$arrPostedPay[$facId][$grpId][$eid]['reference_no'][$ref_no]= $ref_no;
							}						
							
							if($paidBy=='patient'){
								$arrPostedPay[$facId][$grpId][$eid]['byPatient']+=$paidAmt;
							}else{
								$arrPostedPay[$facId][$grpId][$eid]['byInsurance']+=$paidAmt;
							}
						}else{
							$arrPostedPay[$grpId][$eid]['pat_name']=$patName;
							$arrPostedPay[$grpId][$eid]['dos']=$res['date_of_service'];
							$arrPostedPay[$grpId][$eid][$payMode]+= $paidAmt;
							if($res['checkNo']!='' || $res['creditCardNo']!=''){
								$ref_no=($res['checkNo']!='')?$res['checkNo']:substr($res['creditCardNo'],-4);
								$arrPostedPay[$grpId][$eid]['reference_no'][$ref_no]= $ref_no;
							}
							
							if($paidBy=='patient'){
								$arrPostedPay[$grpId][$eid]['byPatient']+=$paidAmt;
							}else{
								$arrPostedPay[$grpId][$eid]['byInsurance']+=$paidAmt;
							}
						}
					}
				}
			}else{
				if($chgDetId>0){
					$chargeIdArr[$chgDetId] = $chgDetId;
					$tempPostedPay[$paidBy][$chgDetId][$payMode]+= $paidAmt; 
					if($res['checkNo']!='' || $res['creditCardNo']!=''){
						$ref_no=($res['checkNo']!='')?$res['checkNo']:substr($res['creditCardNo'],-4);
						$tempPostedPay[$chgDetId]['reference_no'][$ref_no]= $ref_no;
					}					
	
					if($payMode=='credit_card')
					$tempCCTypeAmts[$chgDetId][$ccType]+=$paidAmt; 
				}else{
					$tempCopayEncs[$eid]=$eid;
					$tempCopayData[$eid]['paidBy']=$paidBy;
					$tempCopayData[$eid]['payMode']=$payMode;
					$tempCopayData[$eid]['paidAmt']=$paidAmt;
					$tempCopayData[$eid]['ccType']=$ccType;
					if($res['checkNo']!='' || $res['creditCardNo']!=''){
						$ref_no=($res['checkNo']!='')?$res['checkNo']:substr($res['creditCardNo'],-4);
						$tempCopayData[$eid]['reference_no'][$ref_no]= $ref_no;
					}										
				}
				$tempDeptData[$eid]['patName']=$patName;
				$tempDeptData[$eid]['dos']=$res['date_of_service'];
			}
			$arrTemp[$eid]['enc']=$eid;
			$arrTemp[$eid]['sts']=$res['del_status'];
		}
	}
	unset($rs);
	//pre($encounterIdArr);
	//pre($chargeIdArr);
	//pre($tempDeptData);

	// IF BY DEPT. THEN MANAGE POSTGED PAYMENTS
	if($groupBy!='facility' && (sizeof($chargeIdArr)>0 || sizeof($encounterIdArr)>0)){
		// FOR COPAY PAYMENTS
		if(sizeof($tempCopayEncs)>0){
			$strCopayEnc = implode(',', $tempCopayEncs);
			$qry="Select patChg.encounter_id, patChgDet.charge_list_detail_id FROM patient_charge_list patChg 
			LEFT JOIN patient_charge_list_details patChgDet ON patChgDet.charge_list_id = patChg.charge_list_id 
			WHERE patChg.encounter_id IN(".$strCopayEnc.") AND patChgDet.coPayAdjustedAmount='1' GROUP BY patChg.encounter_id";
			$rs=imw_query($qry)or die(imw_error().'_251');
			while($res=imw_fetch_array($rs)){
				$paidAmt=0;
				$chgDetId = $res['charge_list_detail_id'];
				$eid = $res['encounter_id'];
				$chargeIdArr[$chgDetId] = $chgDetId;
				if($chgDetId>0){ 
					$arrPostedChgDetIds[$chgDetId]=$chgDetId; 
				}else{
					$arrPostedEncDetIds[$eid]=$eid;
				}
					
				$paidBy= $tempCopayData[$eid]['paidBy'];
				$payMode= $tempCopayData[$eid]['payMode'];
				$paidAmt= $tempCopayData[$eid]['paidAmt'];
				$ccType= $tempCopayData[$eid]['ccType'];
				
				$tempPostedPay[$paidBy][$chgDetId][$payMode]+= $paidAmt;
				if(sizeof($tempCopayData[$eid]['reference_no'])>0){
					foreach($tempCopayData[$eid]['reference_no'] as $ref_no){
						$tempPostedPay[$chgDetId]['reference_no'][$ref_no]= $ref_no;
					}
					//AVOID TO ADD AGAIN
					unset($tempCopayData[$eid]['reference_no']);
				}
				if($payMode=='credit_card')
				$tempCCTypeAmts[$chgDetId][$ccType]+=$paidAmt; 
			} 
			unset($rs);
			unset($tempCopayEncs);
			unset($tempCopayData);
		}

		// ALL PAYMENTS
		$arr_split_parts = array_chunk($chargeIdArr, 5000);
		foreach($arr_split_parts as $arr){
			$chargeStr = implode(',', $arr);
			$qry="SELECT 
					patChg.encounter_id, 
					patChgDet.charge_list_detail_id, 
					cpt_fee_tbl.departmentId, 
					department_tbl.DepartmentCode ,
					patChg.facility_id, patChg.primary_provider_id_for_reports  
					FROM patient_charge_list patChg 
					JOIN patient_charge_list_details patChgDet ON patChgDet.charge_list_id = patChg.charge_list_id 
					JOIN patient_data pd ON pd.id = patChg.patient_id 
					LEFT JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = patChgDet.procCode 
					LEFT JOIN department_tbl ON department_tbl.DepartmentId = cpt_fee_tbl.departmentId 
					LEFT JOIN pos_facilityies_tbl ON pos_facilityies_tbl.pos_facility_id = patChg.facility_id 
					WHERE patChgDet.charge_list_detail_id IN(".$chargeStr.") 
					AND department_tbl.DepartmentId IN (".$department.") 
					ORDER BY department_tbl.DepartmentCode, pd.lname, pd.fname";
			//echo $qry;
			$rs=imw_query($qry)or die(imw_error().'_289');
			while($res=imw_fetch_array($rs)){
				$deptId='';
				$eid = $res['encounter_id'];
				$chgDetId = $res['charge_list_detail_id'];
				$deptId = $res['departmentId'];
				$deptName = $res['DepartmentCode'];
				$phy_id = $res['primary_provider_id_for_reports'];
				$facility_id = $res['facility_id'];
				if($deptId<=0 || $deptId==''){ $deptName = 'No Department'; }
				
				$dept_name_arr[$deptId] = $deptName;				
					
				//CREDIT CARD TYPE AMOUNTS ARRAY
				if(sizeof($tempCCTypeAmts[$chgDetId])>0){
					foreach($tempCCTypeAmts[$chgDetId] as $ccType => $amt){
						$arrPostedCCTypeAmts[$ccType]+= $amt;
					}
				}
				
				if($process=='Summary'){
					if(sizeof($tempPostedPay['patient'][$chgDetId])>0){
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId]['cash']+= $tempPostedPay['patient'][$chgDetId]['cash'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId]['check']+= $tempPostedPay['patient'][$chgDetId]['check'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId]['credit_card']+= $tempPostedPay['patient'][$chgDetId]['credit_card'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId]['eft']+= $tempPostedPay['patient'][$chgDetId]['eft'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId]['money_order']+= $tempPostedPay['patient'][$chgDetId]['money_order'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId]['veep']+= $tempPostedPay['patient'][$chgDetId]['veep'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId]['byPatient']+= $tempPostedPay['patient'][$chgDetId]['cash'] + $tempPostedPay['patient'][$chgDetId]['check'] + $tempPostedPay['patient'][$chgDetId]['credit_card'] + $tempPostedPay['patient'][$chgDetId]['eft'] + $tempPostedPay['patient'][$chgDetId]['money_order']+$tempPostedPay['patient'][$chgDetId]['veep'];
					}
					if(sizeof($tempPostedPay['insurance'][$chgDetId])>0){
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId]['cash']+= $tempPostedPay['insurance'][$chgDetId]['cash'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId]['check']+= $tempPostedPay['insurance'][$chgDetId]['check'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId]['credit_card']+= $tempPostedPay['insurance'][$chgDetId]['credit_card'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId]['eft']+= $tempPostedPay['insurance'][$chgDetId]['eft'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId]['money_order']+= $tempPostedPay['insurance'][$chgDetId]['money_order'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId]['veep']+= $tempPostedPay['insurance'][$chgDetId]['veep'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId]['byInsurance']+= $tempPostedPay['insurance'][$chgDetId]['cash'] + $tempPostedPay['insurance'][$chgDetId]['check'] + $tempPostedPay['insurance'][$chgDetId]['credit_card'] + $tempPostedPay['insurance'][$chgDetId]['eft'] + $tempPostedPay['insurance'][$chgDetId]['money_order']+$tempPostedPay['patient'][$chgDetId]['veep'];						
					}
				}else{
					if(sizeof($tempPostedPay['patient'][$chgDetId])>0){
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['pat_name']= $tempDeptData[$eid]['patName'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['dos']=	$tempDeptData[$eid]['dos'];
						
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['cash']+= $tempPostedPay['patient'][$chgDetId]['cash'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['check']+= $tempPostedPay['patient'][$chgDetId]['check'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['credit_card']+= $tempPostedPay['patient'][$chgDetId]['credit_card'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['eft']+= $tempPostedPay['patient'][$chgDetId]['eft'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['money_order']+= $tempPostedPay['patient'][$chgDetId]['money_order'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['veep']+= $tempPostedPay['patient'][$chgDetId]['veep'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['byPatient']+= $tempPostedPay['patient'][$chgDetId]['cash'] + $tempPostedPay['patient'][$chgDetId]['check'] + $tempPostedPay['patient'][$chgDetId]['credit_card'] + $tempPostedPay['patient'][$chgDetId]['eft'] + $tempPostedPay['patient'][$chgDetId]['money_order']+$tempPostedPay['patient'][$chgDetId]['veep'];							
					}
					if(sizeof($tempPostedPay['insurance'][$chgDetId])>0){
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['pat_name']= $tempDeptData[$eid]['patName'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['dos']=	$tempDeptData[$eid]['dos'];
						
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['cash']+= $tempPostedPay['insurance'][$chgDetId]['cash'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['check']+= $tempPostedPay['insurance'][$chgDetId]['check'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['credit_card']+= $tempPostedPay['insurance'][$chgDetId]['credit_card'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['eft']+= $tempPostedPay['insurance'][$chgDetId]['eft'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['money_order']+= $tempPostedPay['insurance'][$chgDetId]['money_order'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['veep']+= $tempPostedPay['insurance'][$chgDetId]['veep'];
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['byInsurance']+= $tempPostedPay['insurance'][$chgDetId]['cash'] + $tempPostedPay['insurance'][$chgDetId]['check'] + $tempPostedPay['insurance'][$chgDetId]['credit_card'] + $tempPostedPay['insurance'][$chgDetId]['eft'] + $tempPostedPay['insurance'][$chgDetId]['money_order']+$tempPostedPay['insurance'][$chgDetId]['veep'];
					}
					
					if(sizeof($tempPostedPay[$chgDetId]['reference_no'])>0){
						$arrDepartmentView[$phy_id][$facility_id]['posted'][$deptId][$eid]['reference_no']= implode(', ',$tempPostedPay[$chgDetId]['reference_no']);
					}
					
				}
				
			} unset($rs);
		}
		unset($tempPostedPay);
	}
	ksort($arrPostedCCTypeAmts);
//pre($arrPostedPay);

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
	DATE_FORMAT(cioPay.created_on, '".get_sql_date_format()."') as 'created_on', 
	cioPay.payment_method, cioPay.cc_type, cioPay.check_no, cioPay.cc_no,
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
			$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
			$facility = ($pay_location=='1')? $sch_facility : $sch_pos_fac_arr[$sch_facility];
			if($facility=='' || $facility<=0) { $facility=0; }
			$ccType= ($arrCreditTypes[strtolower($res['cc_type'])]!='') ? $arrCreditTypes[strtolower($res['cc_type'])] : $arrCreditTypes[0];		
			
			$grpId = $phyId;
			if($groupBy=='operator'){ $grpId = $oprId;	}
			//if($groupBy=='facility'){ $grpId = $facility;	}
			
			
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
			
			$payMode = strtolower($res['payment_method']);
			if($payMode!='cash' && $payMode!='check' && $payMode!='eft' && $payMode!='veep' && $payMode!='money order' && $payMode!='credit card' && $payMode!='byPatient' && $payMode!='byInsurance'){
				$payMode = 'other';
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
				
				if($res['check_no']!='' || $res['cc_no']!=''){
					$tempCIODetail[$payment_id]['reference_no']= ($res['check_no']!='')?$res['check_no']:substr($res['cc_no'],-4);
				}
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
		
		if($groupBy=='facility'){
			$groupArr=array();
			$tempPosFac = array_keys($tempCIOArr);
			$strTempPosFac = implode(',', $tempPosFac);
			if($pay_location=='1'){
				$rs=imw_query("Select id,name from facility where id IN(".$strTempPosFac.") ORDER BY facility");
				while($posQryRes = imw_fetch_array($rs)){
					$sch_facility_id = $posQryRes['id'];
					$groupArr[$sch_facility_id] = $sch_facility_id;
				}	
			}else{
				$qry = "Select pos_facility_id FROM pos_facilityies_tbl WHERE pos_facility_id IN(".$strTempPosFac.") ORDER BY facilityPracCode";
				$rs=imw_query($qry)or die(imw_error().'_550');
				while($posQryRes = imw_fetch_array($rs)){
					$pos_facility_id = $posQryRes['pos_facility_id'];
					$groupArr[$pos_facility_id] = $pos_facility_id;
				}	
			}
		}
	//pre($tempCIOArr);
	
	//preparing final ci/co array
		foreach($groupArr as $grpId){		
			foreach($tempCIOArr[$grpId] as $payment_id => $cioData){
				$cioPayment = $cioData['payment'];
				$refund=$cioData['refund'];

				$pay_mode = str_replace(' ', '_', $cioData['pay_mode']);
				
				if($tempCIOPaid[$payment_id]>0){
					$cioPayment = floatval($cioPayment) - floatval($tempCIOPaid[$payment_id]);
				}
				$cio_pos_facility_id = $cioData['facility'];
				if($cioPayment>0){
					
					if($pay_mode=='credit_card' && sizeof($tempCCTypeAmts[$payment_id])>0){
						foreach($tempCCTypeAmts[$payment_id] as $ccType){
							$arrCIOCCTypeAmts[$ccType]+=$cioPayment;
						}
						$arrCIOCCTypeAmts[$ccType]-=$refund;
					}
					
					if($process=='Summary'){
						if($showFacCol){
							if($pay_location=='1'){
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$pay_mode]+=($cioPayment-$refund);
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$pay_mode.'_ref_amt']+=($refund);
								
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$pay_mode.'_is_ref']=($refund>=1 && $arrCICONotApplied[$grpId][$cio_pos_facility_id][$pay_mode.'_is_ref']=='')?$pay_mode:0;
							}else{
								$arrCICONotApplied[$grpId][$cio_pos_facility_id][$pay_mode]+=($cioPayment-$refund);
								$arrCICONotApplied[$grpId][$cio_pos_facility_id][$pay_mode.'_ref_amt']+=($refund);
								
								$arrCICONotApplied[$grpId][$cio_pos_facility_id][$pay_mode.'_is_ref']=($refund>=1 && $arrCICONotApplied[$grpId][$cio_pos_facility_id][$pay_mode.'_is_ref']=='')?$pay_mode:0;
							}
						}else if($groupBy=='department'){
							$arrDepartmentView[$grpId][$cio_pos_facility_id]['cico'][$pay_mode]+=($cioPayment-$refund);
							$arrDepartmentView[$grpId][$cio_pos_facility_id]['cico'][$pay_mode.'_ref_amt']+=($refund);
							
							$arrDepartmentView[$grpId][$cio_pos_facility_id]['cico'][$pay_mode.'_is_ref']=($refund>=1 && $arrCICONotApplied[$grpId][$cio_pos_facility_id][$pay_mode.'_is_ref']=='')?$pay_mode:0;
						}else{
							if($pay_location=='1'){
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$pay_mode]+=($cioPayment-$refund);
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$pay_mode.'_ref_amt']+=$refund;
								
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$pay_mode.'_is_ref']=($refund>=1 && $arrCICONotApplied[$grpId][$pay_mode.'_is_ref']=='')?$pay_mode:0;
							}else{
								$arrCICONotApplied[$grpId][$pay_mode]+=($cioPayment-$refund);
								$arrCICONotApplied[$grpId][$pay_mode.'_ref_amt']+=$refund;
								
								$arrCICONotApplied[$grpId][$pay_mode.'_is_ref']=($refund>=1 && $arrCICONotApplied[$grpId][$pay_mode.'_is_ref']=='')?$pay_mode:0;
							}
						}
					}else{
						if($showFacCol){
							if($pay_location=='1'){
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$payment_id]['pat_name']=$tempCIODetail[$payment_id]['patName'];
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$payment_id]['paid_date']=$tempCIODetail[$payment_id]['paidDate'];
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$payment_id][$pay_mode]+=($cioPayment-$refund);
								
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$payment_id]['ref_amt']+=$refund;
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$payment_id]['is_ref']=($refund>=1)?$pay_mode:'';

								if($tempCIODetail[$payment_id]['reference_no']){
									$arrCICONotApplied[$cio_pos_facility_id][$grpId][$payment_id]['reference_no']=$tempCIODetail[$payment_id]['reference_no'];
								}
							}else{
								$arrCICONotApplied[$grpId][$cio_pos_facility_id][$payment_id]['pat_name']=$tempCIODetail[$payment_id]['patName'];
								$arrCICONotApplied[$grpId][$cio_pos_facility_id][$payment_id]['paid_date']=$tempCIODetail[$payment_id]['paidDate'];
								$arrCICONotApplied[$grpId][$cio_pos_facility_id][$payment_id][$pay_mode]+=($cioPayment-$refund);
								
								$arrCICONotApplied[$grpId][$cio_pos_facility_id][$payment_id]['ref_amt']+=$refund;
								$arrCICONotApplied[$grpId][$cio_pos_facility_id][$payment_id]['is_ref']=($refund>=1)?$pay_mode:'';

								if($tempCIODetail[$payment_id]['reference_no']){
									$arrCICONotApplied[$grpId][$cio_pos_facility_id][$payment_id]['reference_no']=$tempCIODetail[$payment_id]['reference_no'];
								}								
							}
						}else if($groupBy=='department'){
							$arrDepartmentView[$grpId][$cio_pos_facility_id]['cico'][$payment_id]['pat_name']=$tempCIODetail[$payment_id]['patName'];
							$arrDepartmentView[$grpId][$cio_pos_facility_id]['cico'][$payment_id]['paid_date']=$tempCIODetail[$payment_id]['paidDate'];
							$arrDepartmentView[$grpId][$cio_pos_facility_id]['cico'][$payment_id][$pay_mode]+=($cioPayment-$refund);
							
							$arrDepartmentView[$grpId][$cio_pos_facility_id]['cico'][$payment_id]['ref_amt']+=$refund;
							$arrDepartmentView[$grpId][$cio_pos_facility_id]['cico'][$payment_id]['is_ref']=($refund>=1)?$pay_mode:'';

							if($tempCIODetail[$payment_id]['reference_no']){
								$arrDepartmentView[$grpId][$cio_pos_facility_id]['cico'][$payment_id]['reference_no']=$tempCIODetail[$payment_id]['reference_no'];
							}															
						}else{
							if($pay_location=='1'){
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$payment_id]['pat_name']=$tempCIODetail[$payment_id]['patName'];
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$payment_id]['paid_date']=$tempCIODetail[$payment_id]['paidDate'];
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$payment_id][$pay_mode]+=($cioPayment-$refund);
								
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$payment_id]['ref_amt']+=$refund;
								$arrCICONotApplied[$cio_pos_facility_id][$grpId][$payment_id]['is_ref']=($refund>=1)?$pay_mode:'';

								if($tempCIODetail[$payment_id]['reference_no']){
									$arrCICONotApplied[$cio_pos_facility_id][$grpId][$payment_id]['reference_no']=$tempCIODetail[$payment_id]['reference_no'];
								}																
							}else{
								$arrCICONotApplied[$grpId][$payment_id]['pat_name']=$tempCIODetail[$payment_id]['patName'];
								$arrCICONotApplied[$grpId][$payment_id]['paid_date']=$tempCIODetail[$payment_id]['paidDate'];
								$arrCICONotApplied[$grpId][$payment_id][$pay_mode]+=($cioPayment-$refund);
								
								$arrCICONotApplied[$grpId][$payment_id]['ref_amt']+=$refund;
								$arrCICONotApplied[$grpId][$payment_id]['is_ref']=($refund>=1)?$pay_mode:'';

								if($tempCIODetail[$payment_id]['reference_no']){
									$arrCICONotApplied[$grpId][$payment_id]['reference_no']=$tempCIODetail[$payment_id]['reference_no'];
								}																								
							}
						}
					}
				}
				
				//MANAULLY PAID ARRAY
				if($tempCIOManuallyApplied[$payment_id]>0){
					if($process=='Summary'){
						$arrManuallyApplied[$grpId]['cico']+= $tempCIOManuallyApplied[$payment_id];
					}else{
						$arrCICOManuallyPaid[$grpId][$payment_id]['pat_name']=$tempCIODetail[$payment_id]['patName'];
						$arrCICOManuallyPaid[$grpId][$payment_id]['paid_date']=$tempCIODetail[$payment_id]['paidDate'];
						$arrCICOManuallyPaid[$grpId][$payment_id]['applied_amt']= $tempCIOManuallyApplied[$payment_id];
					}
				}
			}
		}
	}
	unset($tempCIOManuallyApplied);
	ksort($arrCIOCCTypeAmts);

	//pre($arrCICONotApplied);
	// GET PATIENT PRE PAYMENTS
	$groupArr=array();
	$tempCCTypeAmts=array();
	$patQryRes = array();
	$qry="Select pDep.id, pDep.patient_id, pDep.paid_amount, pDep.facility_id, 
	pDep.apply_payment_date, pData.default_facility, pDep.apply_payment_type, pDep.apply_amount, pDep.provider_id, 
	DATE_FORMAT(pDep.entered_date, '".get_sql_date_format()."') as 'entered_date', DATE_FORMAT(pDep.paid_date, '".get_sql_date_format()."') as 'paid_date',
	pDep.entered_by, pDep.payment_mode, pDep.credit_card_co, pDep.check_no, pDep.cc_no,
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
	/* 
		$patQryRes = $objManageData->mysqlifetchdata($qry);
	*/
	
	// GET PATIENTS HAVING 0 PROVDERID
/*	if($groupBy=='physician' || $groupBy=='department'){
		for($i=0; $i<sizeof($patQryRes); $i++){
			if($patQryRes[$i]['providerID']=='0' || $patQryRes[$i]['providerID']==''){
				$pid = $patQryRes[$i]['patient_id'];
				$arrPrePats[$pid] = $pid;
			}
		}
		if(sizeof($arrPrePats)>0){
			$strPrePats = implode(',', $arrPrePats);
			$qry="Select sa_patient_id, sa_doctor_id FROM schedule_appointments WHERE sa_patient_id IN(".$strPrePats.") ORDER BY id";
			$rs=imw_query($qry)or die(imw_error().'_638');
			while($res=imw_fetch_array($rs)){
				$pid=$res['sa_patient_id'];
				$phyId = $res['sa_doctor_id'];
				$arrPatProv[$pid] = $phyId;
			}
		}
	}*///---------------------------------
	
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
		$patName = $pid.'~'.$patQryRes['fname'].'~'.$patQryRes['mname'].'~'.$patQryRes['lname'];
		$ccType= ($arrCreditTypes[strtolower($patQryRes['credit_card_co'])]!='') ? $arrCreditTypes[strtolower($patQryRes['credit_card_co'])] : $arrCreditTypes[0];				
		
		//if($phyId<=0){ 		$phyId = $arrPatProv[$patQryRes['patient_id']];	}
		if($facility<=0 || $facility==''){ $facility= ($pay_location=='1') ? $headSchFacility :$headPosFacility ; }

		$grpId= $phyId;
		//if($groupBy=='facility'){ $grpId = $facility; }
		//else if($groupBy=='operator'){ $grpId = $oprId; }
		if($groupBy=='operator'){ $grpId = $oprId; }
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
			$payMode = strtolower($patQryRes['payment_mode']);
			if($payMode!='cash' && $payMode!='check' && $payMode!='eft' && $payMode!='veep' && 
			$payMode!='money_order' && $payMode!='credit card' && $payMode!='byPatient' && $payMode!='byInsurance'){
				$payMode = 'other';
			}
			
			$arrAllIdsData[$grpId][$id]['pay_mode']= $payMode;
			$arrAllIdsData[$grpId][$id]['pat_name']= $patName;
			if(strtolower($patQryRes['payment_mode'])=='credit card')
			$tempCCTypeAmts[$id][$ccType]=$ccType;
			
			if($patQryRes['check_no']!='' || $patQryRes['cc_no']!=''){
				$arrAllIdsData[$grpId][$id]['reference_no']= ($patQryRes['check_no']!='')?$patQryRes['check_no']:substr($patQryRes['cc_no'],-4);
			}
			
			if($DateRangeFor=='dot'){
				$arrAllIdsData[$grpId][$id]['entered_date']=$patQryRes['entered_date'];
			}else{
				$arrAllIdsData[$grpId][$id]['entered_date']=$patQryRes['paid_date'];
			}

			$groupArr[$grpId] = $grpId;
			$tempPreFac[$id] = $facility;
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

			if($balance_amount>0){
				$pay_mode= str_replace(' ', '_', $grpData['pay_mode']);
				
				$facility = $tempPreFac[$id];

				if($pay_mode=='credit_card' && sizeof($tempCCTypeAmts[$id])>0){
					foreach($tempCCTypeAmts[$id] as $ccType){
						$arrPrePayCCTypeAmts[$ccType]+=$balance_amount;
					}
					$arrPrePayCCTypeAmts[$ccType]-=$tempData[$id]['PAT_DEPOSIT_REF'];
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
					if($showFacCol){
						if($pay_location=='1'){
							$arrPrePayNotApplied[$facility][$grpId][$id]['pat_name']=$grpData['pat_name'];
							$arrPrePayNotApplied[$facility][$grpId][$id]['entered_date']=$grpData['entered_date'];
							$arrPrePayNotApplied[$facility][$grpId][$id][$pay_mode]+=(floatval($balance_amount)-floatval($tempData[$id]['PAT_DEPOSIT_REF']));
							$arrPrePayNotApplied[$facility][$grpId][$id]['is_ref']=($tempData[$id]['PAT_DEPOSIT_REF']>=1)?$pay_mode:0;
							$arrPrePayNotApplied[$facility][$grpId][$id]['ref_amt']=$tempData[$id]['PAT_DEPOSIT_REF'];
							
							if($grpData['reference_no']){
								$arrPrePayNotApplied[$facility][$grpId][$id]['reference_no']=$grpData['reference_no'];
							}
						}else{
							$arrPrePayNotApplied[$grpId][$facility][$id]['pat_name']=$grpData['pat_name'];
							$arrPrePayNotApplied[$grpId][$facility][$id]['entered_date']=$grpData['entered_date'];
							$arrPrePayNotApplied[$grpId][$facility][$id][$pay_mode]+=(floatval($balance_amount)-floatval($tempData[$id]['PAT_DEPOSIT_REF']));
							$arrPrePayNotApplied[$grpId][$facility][$id]['is_ref']=($tempData[$id]['PAT_DEPOSIT_REF']>=1)?$pay_mode:0;
							$arrPrePayNotApplied[$grpId][$facility][$id]['ref_amt']=$tempData[$id]['PAT_DEPOSIT_REF'];

							if($grpData['reference_no']){
								$arrPrePayNotApplied[$grpId][$facility][$id]['reference_no']=$grpData['reference_no'];
							}							
						}
					}elseif($groupBy=='department'){
						$arrDepartmentView[$grpId][$facility]['pre_paid'][$id]['pat_name']=$grpData['pat_name'];
						$arrDepartmentView[$grpId][$facility]['pre_paid'][$id]['entered_date']=$grpData['entered_date'];
						$arrDepartmentView[$grpId][$facility]['pre_paid'][$id][$pay_mode]+=(floatval($balance_amount)-floatval($tempData[$id]['PAT_DEPOSIT_REF']));
						$arrDepartmentView[$grpId][$facility]['pre_paid'][$id]['is_ref']=($tempData[$id]['PAT_DEPOSIT_REF']>=1)?$pay_mode:0;
						$arrDepartmentView[$grpId][$facility]['pre_paid'][$id]['ref_amt']=$tempData[$id]['PAT_DEPOSIT_REF'];

						if($grpData['reference_no']){
							$arrDepartmentView[$grpId][$facility]['pre_paid'][$id]['reference_no']=$grpData['reference_no'];
						}							
					}else{
						if($pay_location=='1'){
							$arrPrePayNotApplied[$facility][$grpId][$id]['pat_name']=$grpData['pat_name'];
							$arrPrePayNotApplied[$facility][$grpId][$id]['entered_date']=$grpData['entered_date'];
							$arrPrePayNotApplied[$facility][$grpId][$id][$pay_mode]+=(floatval($balance_amount)-floatval($tempData[$id]['PAT_DEPOSIT_REF']));
							$arrPrePayNotApplied[$facility][$grpId][$id]['is_ref']=($tempData[$id]['PAT_DEPOSIT_REF']>=1)?$pay_mode:0;
							$arrPrePayNotApplied[$facility][$grpId][$id]['ref_amt']=$tempData[$id]['PAT_DEPOSIT_REF'];

							if($grpData['reference_no']){
								$arrPrePayNotApplied[$facility][$grpId][$id]['reference_no']=$grpData['reference_no'];
							}								
						}else{
							$arrPrePayNotApplied[$grpId][$id]['pat_name']=$grpData['pat_name'];
							$arrPrePayNotApplied[$grpId][$id]['entered_date']=$grpData['entered_date'];
							$arrPrePayNotApplied[$grpId][$id][$pay_mode]+=(floatval($balance_amount)-floatval($tempData[$id]['PAT_DEPOSIT_REF']));
							$arrPrePayNotApplied[$grpId][$id]['is_ref']=($tempData[$id]['PAT_DEPOSIT_REF']>=1)?$pay_mode:0;
							$arrPrePayNotApplied[$grpId][$id]['ref_amt']=$tempData[$id]['PAT_DEPOSIT_REF'];

							if($grpData['reference_no']){
								$arrPrePayNotApplied[$grpId][$id]['reference_no']=$grpData['reference_no'];
							}							
						}
					}
				}
			}
			
			//MANUALLY APPLIED AMOUNT ARRAY
			if($tempPrePaidManuallyApplied[$id]>0){
				if($process=='Summary'){
					$arrManuallyApplied[$grpId]['pre_payment']+= $tempPrePaidManuallyApplied[$id];
				}else{
					
					$arrPrePayManuallyApplied[$grpId][$id]['pat_name']=$grpData['pat_name'];
					$arrPrePayManuallyApplied[$grpId][$id]['entered_date']=$grpData['entered_date'];
					$arrPrePayManuallyApplied[$grpId][$id]['applied_amt'] = $tempPrePaidManuallyApplied[$id];
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
		$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
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
			if($showFacCol){
				if($pay_location=='1'){
					$arrDelPostedAmounts[$facId][$grpId][$eid]['pat_name']=$patName;
					$arrDelPostedAmounts[$facId][$grpId][$eid]['eid']=$eid;
					$arrDelPostedAmounts[$facId][$grpId][$eid]['dos']=$res['date_of_service'];
					$arrDelPostedAmounts[$facId][$grpId][$eid]['del_amount']+= $paidAmt;
				}else{
					$arrDelPostedAmounts[$grpId][$facId][$eid]['pat_name']=$patName;
					$arrDelPostedAmounts[$grpId][$facId][$eid]['eid']=$eid;
					$arrDelPostedAmounts[$grpId][$facId][$eid]['dos']=$res['date_of_service'];
					$arrDelPostedAmounts[$grpId][$facId][$eid]['del_amount']+= $paidAmt;
				}
			}
			else{
				if($pay_location=='1'){
					$arrDelPostedAmounts[$facId][$grpId][$eid]['pat_name']=$patName;
					$arrDelPostedAmounts[$facId][$grpId][$eid]['eid']=$eid;
					$arrDelPostedAmounts[$facId][$grpId][$eid]['dos']=$res['date_of_service'];
					$arrDelPostedAmounts[$facId][$grpId][$eid]['del_amount']+= $paidAmt;
				}else{
					$arrDelPostedAmounts[$grpId][$eid]['pat_name']=$patName;
					$arrDelPostedAmounts[$grpId][$eid]['eid']=$eid;
					$arrDelPostedAmounts[$grpId][$eid]['dos']=$res['date_of_service'];
					$arrDelPostedAmounts[$grpId][$eid]['del_amount']+= $paidAmt;
				}
			}
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
	DATE_FORMAT(cioPay.created_on, '".get_sql_date_format()."') as 'created_on', 
	cioPay.payment_method, cioPay.cc_type,
	cioPay.created_by, cioPay.created_time, 
	cioPayDet.item_payment, 
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
		$qry.= " AND cioPay.created_by IN(".$operator.")";
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
		$payment_id = $res['payment_id'];
		$phyId = $res['sa_doctor_id'];
		$sch_facility = $res['sa_facility_id'];
		$oprId = $res['created_by'];
		$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
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
			if($showFacCol){
				if($pay_location=='1'){
					$arrDelCICOAmounts[$facility][$grpId][$payment_id]['pat_name']=$patName;
					$arrDelCICOAmounts[$facility][$grpId][$payment_id]['payment_id']=$payment_id;
					$arrDelCICOAmounts[$facility][$grpId][$payment_id]['paid_date']=$res['created_on'];
					$arrDelCICOAmounts[$facility][$grpId][$payment_id]['del_amount']+=$res['item_payment'];
				}else{
					$arrDelCICOAmounts[$grpId][$facility][$payment_id]['pat_name']=$patName;
					$arrDelCICOAmounts[$grpId][$facility][$payment_id]['payment_id']=$payment_id;
					$arrDelCICOAmounts[$grpId][$facility][$payment_id]['paid_date']=$res['created_on'];
					$arrDelCICOAmounts[$grpId][$facility][$payment_id]['del_amount']+=$res['item_payment'];
				}
			}else{
				if($pay_location=='1'){
					$arrDelCICOAmounts[$facility][$grpId][$payment_id]['pat_name']=$patName;
					$arrDelCICOAmounts[$facility][$grpId][$payment_id]['payment_id']=$payment_id;
					$arrDelCICOAmounts[$facility][$grpId][$payment_id]['paid_date']=$res['created_on'];
					$arrDelCICOAmounts[$facility][$grpId][$payment_id]['del_amount']+=$res['item_payment'];
				}else{
					$arrDelCICOAmounts[$grpId][$payment_id]['pat_name']=$patName;
					$arrDelCICOAmounts[$grpId][$payment_id]['payment_id']=$payment_id;
					$arrDelCICOAmounts[$grpId][$payment_id]['paid_date']=$res['created_on'];
					$arrDelCICOAmounts[$grpId][$payment_id]['del_amount']+=$res['item_payment'];
				}
			}
		}
	}
	//END DELETED CI/CO	

	// GET PATIENT PRE PAYMENTS
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
	while($row = imw_fetch_assoc($patQry)){
		$patQryRes[] = $row;
	}
	$arrDepIds=array();
	$tempData=array(); $arrDepIds=array(); $arrAllIds=array(); $arrAllIdsData=array();
	
	for($i=0; $i<sizeof($patQryRes); $i++){
		$printFile=true;
		$facility=0; $balance_amount=0; $doc_id=0;$refundAmt=0;
		$date='';
		
		$pid = $patQryRes[$i]['patient_id'];
		$facility= ($pay_location=='1') ? $patQryRes[$i]['facility_id'] : $sch_pos_fac_arr[$patQryRes[$i]['facility_id']];
		$phyId=$patQryRes[$i]['provider_id'];
		$oprId=$patQryRes[$i]['entered_by'];
		$patName = $pid.'~'.$patQryRes[$i]['fname'].'~'.$patQryRes[$i]['mname'].'~'.$patQryRes[$i]['lname'];
		
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
			if($showFacCol){
				if($pay_location=='1'){
					$arrDelPrePayAmounts[$facility][$grpId][$id]['pat_name']=$patName;
					$arrDelPrePayAmounts[$facility][$grpId][$id]['entered_date']=$date;
					$arrDelPrePayAmounts[$facility][$grpId][$id]['del_amount']+=$patQryRes[$i]['paid_amount'];
				}else{
					$arrDelPrePayAmounts[$grpId][$facility][$id]['pat_name']=$patName;
					$arrDelPrePayAmounts[$grpId][$facility][$id]['entered_date']=$date;
					$arrDelPrePayAmounts[$grpId][$facility][$id]['del_amount']+=$patQryRes[$i]['paid_amount'];
				}
			}else{
				if($pay_location=='1'){
					$arrDelPrePayAmounts[$facility][$grpId][$id]['pat_name']=$patName;
					$arrDelPrePayAmounts[$facility][$grpId][$id]['entered_date']=$date;
					$arrDelPrePayAmounts[$facility][$grpId][$id]['del_amount']+=$patQryRes[$i]['paid_amount'];
				}else{
					$arrDelPrePayAmounts[$grpId][$id]['pat_name']=$patName;
					$arrDelPrePayAmounts[$grpId][$id]['entered_date']=$date;
					$arrDelPrePayAmounts[$grpId][$id]['del_amount']+=$patQryRes[$i]['paid_amount'];
				}
			}
		}
	}
	
	
	//pre($arrDelAmounts);
	
	//SORTING OF DELETED AMOUNTS
	$arrKeys=array_keys($arrDelAmounts);
	if(sizeof($arrKeys)>0){
		$str=implode(',', $arrKeys);
		$qry="Select id FROM users WHERE id IN(".$str.") ORDER BY lname,fname";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$delSortedGroup[$res['id']]=$res['id'];
		}unset($rs);
	}
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
				<td style="width:258px;text-align:left" class="rpt_headers rptbx1">Payments Report ('.$process.')</td>
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
			require_once(dirname(__FILE__)."/payments_summary.php");
		}else{
			require_once(dirname(__FILE__)."/payments_details.php");
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
							Payments Report ($process)
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

if($output_option=='view' || $output_option=='output_csv'){
	if($callFrom!='scheduled'){
		echo $csv_file_data;	
	}
}

/* if($callFrom=='scheduled'){
	if($html_page_content != ""){
		$op='l';
		$page_html_script = $page_content;
		$html_file_name = get_scheduled_pdf_name('dr_total', '../common/new_html2pdf');
		file_put_contents('../common/new_html2pdf/'.$html_file_name.'.html',$html_page_content);
	}
}else{
//--- SET CHECK IN/OUT REPORT TEMPLATE ---
//$objManageData->Smarty->display(dirname(__FILE__).'/template/payments_result.tpl');
} */
?>