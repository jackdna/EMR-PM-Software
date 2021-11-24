
<?php
ini_set("memory_limit","3072M");

$printFile = true;
$arrGroupSel=array();
$arrFacilitySel=array();
$arrDoctorSel=array();
$arrAllGroups = array();
$curDate = date(''.$phpDateFormat.' H:i A');
if( $_POST['form_submitted'] ){
	$printFile = false;
	$checkInDataArr=array();

	//--- CHANGE DATE FORMAT ----
	$date_format_SQL = get_sql_date_format();
	if($Start_date!='' && $End_date!=''){
		$startDate = getDateFormatDB($Start_date);
		$endDate = getDateFormatDB($End_date);
	}
	if($due_start_date!='' && $due_end_date!=''){
		$due_start_date = getDateFormatDB($due_start_date);
		$due_end_date = getDateFormatDB($due_end_date);
	}
	
	
	$dd=explode('-', $startDate);
	$prevDate = date('Y-m-d', mktime(0,0,0, $dd[1], $dd[2]-1,$dd[0]));

	$reptByForFun = $DateRangeFor;
	$checkDel= ($reptByForFun=='dot') ?  'yes' : '';
	
	// GET DEFAULT FACILITY
	$rs = imw_fetch_assoc(imw_query("select fac_prac_code from facility where facility_type  = '1' LIMIT 1"));
	$headPosFacility=$rs['fac_prac_code'];
	
	// GET SELECTED SCHEDULER FACILITIES
	$sch_fac_id_str='';
	$fac_query = "Select id,name,fac_prac_code from facility";
	$fac_query_rs = imw_query($fac_query);
	$sch_fac_id_arr = array();
	while($fac_query_res = imw_fetch_array($fac_query_rs)){	
		$fac_id = $fac_query_res['id'];
		$pos_fac_id = $fac_query_res['fac_prac_code'];
		$sch_pos_fac_arr[$fac_id] = $pos_fac_id;
		$sch_fac_arr[$pos_fac_id] = $fac_id;
	}

	// -- GET ALL POS-FACILITIES
	$fac_name_arr=array();
	$fac_name_arr[0] = 'No Facility';
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
		$fac_name_arr[$id] = $name.' - '.$pos_prac_code;
	}						
	
	//GET ALL USERS
	$rs=imw_query("Select id, fname, mname, lname FROM users");	
	$providerNameArr[0] = 'No Provider';
	while($res=imw_fetch_array($rs)){
		$id  = $res['id'];
		$pro_name_arr = array();
		$pro_name_arr["LAST_NAME"] = $res['lname'];
		$pro_name_arr["FIRST_NAME"] = $res['fname'];
		$pro_name_arr["MIDDLE_NAME"] = $res['mname'];
		$pro_name = changeNameFormat($pro_name_arr);
		$providerNameArr[$id] = $pro_name;
	}
	
	//GET ALL Departments
	$rs=imw_query("Select * FROM department_tbl");	
	$deptNameArr[0] = 'No Department';
	while($res=imw_fetch_array($rs)){
		$id  = $res['DepartmentId'];
		$dept_name = $res['DepartmentDesc'] . ($res['DepartmentCode'] ? ' - '.$res['DepartmentCode'] : '');
		$deptNameArr[$id] = $dept_name;
	}					
	
	//--- GET GROUP NAME ---
	$group_query = "select gro_id, name from groups_new";
	$groupQryRes = get_array_records_query($group_query);		
	$arrAllGroups[0] = 'No Group';
	for($i=0; $i<sizeof($groupQryRes); $i++){	
		$group_name = $groupQryRes[$i]['name'];
		$arrAllGroups[$groupQryRes[$i]['gro_id']]=$group_name;
	}
	
	//--- GET Appoinment Status---
	$status_query = "select id, status_name, alias from schedule_status";
	$statusQryRes = get_array_records_query($status_query);		
	$arrApptStatus[0] = 'Created/Restored';
	for($i=0; $i<sizeof($statusQryRes); $i++){	
		$status_name = $statusQryRes[$i]['alias'];
		$arrApptStatus[$statusQryRes[$i]['id']]=$status_name;
	}

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_name)<=0 && isPosFacGroupEnabled()){
		$facility_name = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_name)<=0){
			$facility_name[0]='NULL';
		}
	}
		
	$grp_id = implode(',',$groups);
	$strProviders = implode(',',$phyId);
	$strFacilities = implode(',',$facility_name);
	$department = implode(',',$department);
	$operator_id = implode(',',$operator_id);
	$cpt_code_id = implode(',',$cpt);
	$task_assign_operator_id = implode(',',$task_assign_operator_id);	
	$groupBy = $grpby_block;

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
		
		$hourFromL.=$ampmFrom;
		$hourToL.=$ampmTo;
		
		$hourFrom=$hourFrom.':00:00';
		$hourTo=$hourTo.':00:00';		
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
	if(empty($grp_id)===false){ $arrGroupSel = explode(',', $grp_id);}
	if(empty($sc_name)===false){ $arrFacilitySel = explode(',', $sc_name); }
	if(empty($Physician)===false){ $arrDoctorSel = explode(',', $Physician); }
	if( is_array($arrInsurance) && count($arrInsurance) > 0){
		$insCompanies = implode(',',$arrInsurance);
	}
}
	
	// GET COLLECTION STATUS IDs
		$collectionIds = get_account_status_id_collections();
		if(empty($collectionIds)==false){
			$arrCollectionIds= explode(',', $collectionIds);
			$arrCollectionIds = array_combine($arrCollectionIds, $arrCollectionIds);
		}
		
		$qry ="Select elem_arCycle FROM copay_policies WHERE policies_id='1'";
		$rs=imw_query($qry);
		$res=imw_fetch_array($rs);
		$aggingCycle = $res['elem_arCycle'];
		//---------------------//
		
		if($aging_to=="All"){
			$show_aging_to="181+";
		}else{
			$show_aging_to=$aging_to;
		}

		$printFile = false;
		$All_due = false;
		if($aging_to == 'All'){
			$aging_to = 180;
			$All_due = true;
		}
		
		$patientMainDataArr = array();
		$arrPatSort=array();

	
	$dueJoin=$whr_due_date_chk = "";
	$notes_search_done=0;
	if(empty($task_assign_operator_id) == false || ($due_start_date!="" && $due_end_date!="")){
		$notes_search_done=1;
		$dueJoin = " JOIN paymentscomment ON paymentscomment.encounter_id = patient_charge_list.encounter_id"; 
		if($task_assign_operator_id){
			$whr_due_date_chk .= " AND paymentscomment.task_assign_for IN ($task_assign_operator_id)";
		}
		if($due_start_date!="" && $due_end_date!=""){
			$whr_due_date_chk .= " AND (paymentscomment.reminder_date  between '$due_start_date' AND '$due_end_date')";
		}
	}

	//PRE-PAYMENTS
	if($dispPrePayments==1 && $notes_search_done==0){
		$groupArr=array();
		$tempCCTypeAmts=array();
		$qry="Select pDep.id, pDep.patient_id, pDep.paid_amount, pDep.provider_id,
		pDep.apply_payment_date, pData.default_facility, pDep.apply_payment_type, pDep.apply_amount,
		DATE_FORMAT(pDep.entered_date, '%m-%d-%y') as 'entered_date', DATE_FORMAT(pDep.paid_date, '%m-%d-%y') as 'paid_date',
		pDep.entered_by, pDep.payment_mode, pDep.credit_card_co,
		pData.fname, pData.mname, pData.lname, pData.street, pData.street2, pData.postal_code, pData.zip_ext,
		pData.city, pData.state, pData.country_code, pData.phone_home, pData.phone_biz, pData.phone_biz,
		pData.phone_cell, pData.preferr_contact 
		FROM patient_pre_payment pDep 
		JOIN patient_data pData ON pData.id = pDep.patient_id 
		WHERE pDep.del_status='0'";
		
		if(empty($patientId) === false){
			$startLname = '';
			$qry .= " and pDep.patient_id in ($patientId)";
		}
		else if(trim($startLname) != ''){
			$qry .= " and (trim(pData.lname) between '$startLname' and '$endLname' 
					or trim(pData.lname) like '$endLname%')";
		}				
		if($startDate != '' && $endDate != ''){
			$qry.=" AND (pDep.entered_date BETWEEN '".$startDate."' AND '".$endDate."')";
		}
		if(empty($strFacilities) == false){
			$qry .= " and pDep.facility_id IN ($strFacilities)";
		}
		if(empty($strProviders) == false){
			$qry.= " and pDep.provider_id in($strProviders)";
		}

		$qry.=" and DATEDIFF(NOW(),pDep.entered_date)>=$aging_start";
		if($All_due == false && ($startDate == '' && $endDate == '')){
			$qry.= " and DATEDIFF(NOW(),entered_date)<=$aging_to";
		}
		$qry.=" ORDER BY pData.lname, pData.fname";
		$patQryRs = imw_query($qry);
		
		$arrDepIds=array();
		$tempData=array(); $arrDepIds=array(); $arrAllIds=array(); $arrAllIdsData=array();
		while($patQryRes=imw_fetch_assoc($patQryRs)){
			$address='';
			$printFile=true;
			$facility=0; $balance_amount=0; $doc_id=0;$refundAmt=0;
			
			##########################################################
			#query to get refund detail for current pre payment if any
			##########################################################
			$qry="Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND pmt_id = '".$patQryRes['id']."'";
			if($startDate != '' && $endDate != ''){
				$qry.=" AND (entered_date BETWEEN '".$startDate."' AND '".$endDate."')";
			}
			$qryRef=imw_query($qry);
			while($rsRef=imw_fetch_array($qryRef)){
				$refundAmt=$rsRef['ref_amt'];
			}

			$pid = $patQryRes['patient_id'];
			$id= $patQryRes['id'];
			$balance_amount=$patQryRes['paid_amount'];
	
			if($patQryRes['apply_payment_type']=='manually' && ($endDate!='' && $patQryRes['apply_payment_date']<= $endDate)){
				$balance_amount-=$patQryRes['apply_amount'];
			}
			
			if($balance_amount>0){
				//--- PATIENT NAME -------
				$patient_name_arr = array();
				$patient_name_arr["LAST_NAME"] = $patQryRes['lname'];
				$patient_name_arr["FIRST_NAME"] = $patQryRes['fname'];
				$patient_name_arr["MIDDLE_NAME"] = $patQryRes['mname'];
				$patName = changeNameFormat($patient_name_arr);
				
				$tempData[$id]['PAT_DEPOSIT']=$patQryRes['paid_amount'];
				$tempData[$id]['PAT_DEPOSIT_REF']=$refundAmt;
				
				if($patQryRes['apply_payment_type']=='manually'){
					$tempData[$id]['APPLIED_AMT']+= $patQryRes['apply_amount'];
				}
				//if($patQryRes['apply_payment_date']!='0000-00-00'){
				$arrDepIds[$id]=$id;	
				//}
				
				$arrAllIds[$id]=$id;
				$arrAllIdsData[$id]['pay_mode']=strtolower($patQryRes['payment_mode']);
				$arrAllIdsData[$id]['pat_id']=$pid;
				$arrAllIdsData[$id]['pat_name']=$patName;
					
				if($dispDemographics=='1'){
					$arrPatients[$pid] = $pid;
					//ADDRESS
					if($patQryRes["street"]!='' && $patQryRes["street2"]!=''){
						$address= '&nbsp;'.$patQryRes["street"].', '.$patQryRes["street2"].'<br>';
					}else{
						$address= '&nbsp;'.$patQryRes["street"].'<br>';
					}
					if($patQryRes["city"]!=''){ $address.= '&nbsp;'.$patQryRes["city"]; }
					if($patQryRes["state"]!=''){ $address.= '&nbsp;'.$patQryRes["state"]; }
					if($patQryRes["postal_code"]!=''){ $address.= '&nbsp;'.$patQryRes["postal_code"]; }
					//PHONE
					if($patQryRes["preferr_contact"]=='0'){ $address.= '<br>&nbsp;'.$patQryRes["phone_home"];}
					else if($patQryRes["preferr_contact "]=='1'){ $address.= '<br>&nbsp;'.$patQryRes["phone_biz"];}
					else if($patQryRes["preferr_contact "]=='2'){ $address.= '<br>&nbsp;'.$patQryRes["phone_cell"];}
				}
				$arrAllIdsData[$id]['address']=$address;
			}
		}unset($patQryRs);
		
			// GET PRE PAT ENCOUNTER APPLIED AMTS
		if(count($arrDepIds)>0){
			$strDepIds=implode(',', $arrDepIds);
			$preAppQry="Select payChgDet.patient_pre_payment_id, payChgDet.paidForProc FROM patient_chargesheet_payment_info payChg  
			JOIN patient_charges_detail_payment_info payChgDet ON payChgDet.payment_id = payChg.payment_id
			WHERE payChgDet.patient_pre_payment_id IN($strDepIds)";
			if($startDate != '' && $endDate != ''){
				$preAppQry.=" AND (payChg.transaction_date BETWEEN '".$startDate."' and '".$endDate."')";
			}
			$preAppQry.=" AND payChgDet.deletePayment='0' AND payChgDet.unapply='0'";

			$preAppRs=imw_query($preAppQry);
			while($preAppRes=imw_fetch_array($preAppRs)){
				$id = $preAppRes['patient_pre_payment_id'];
				$tempData[$id]['APPLIED_AMT']+= $preAppRes['paidForProc'];
			}unset($preAppRs);
		}
		// PRE PAYMENTS FINAL ARRAY
		$arrPrePayNotApplied=array();
		foreach($arrAllIdsData as $id => $grpData){
			$pt_dep = $tempData[$id]['PAT_DEPOSIT'];
			$app_amt =  $tempData[$id]['APPLIED_AMT'];
			$pt_dep_ref =  $tempData[$id]['PAT_DEPOSIT_REF'];
			
			$pt_dep = floatval(preg_replace("/[^-0-9\.]/","",$pt_dep));
			$app_amt = floatval(preg_replace("/[^-0-9\.]/","",$app_amt));
			$pt_dep_ref = floatval(preg_replace("/[^-0-9\.]/","",$pt_dep_ref));
			$balance_amount = $pt_dep - ($app_amt + $pt_dep_ref);
			if($balance_amount>0){
				$pid=$grpData['pat_id'];	
				if($summary_detail=='summary'){
					$patientMainDataArr[$pid]['pre_paid']+=$balance_amount;
				}else{
					$patientMainDataArr[$pid]['patient_name']=$grpData['pat_name'];
					$patientMainDataArr[$pid]['address']=$grpData['address'];
					$patientMainDataArr[$pid]['pre_paid']+=$balance_amount;
					$arrPatSort[$pid]=$grpData['pat_name'];
				}
			}
		}unset($tempPreFac);
		ksort($arrPrePayCCTypeAmts);		
	}

	$arrPatients=array();
	//--- GET DATA FROM PATIENT CHARGE LIST ---
	$ptChrQry = "select patient_charge_list.charge_list_id,
	patient_charge_list.patient_id,patient_charge_list_details.pat_due, 
	patient_charge_list.encounter_id,
	DATEDIFF(NOW(), from_pat_due_date) as last_dop_diff,
	DATEDIFF(NOW(),date_of_service) as last_dos_diff,
	patient_data.lname,patient_data.fname,patient_data.mname, patient_data.pat_account_status,
	patient_charge_list.primaryInsuranceCoId, patient_charge_list.secondaryInsuranceCoId,
	patient_charge_list.tertiaryInsuranceCoId, patient_charge_list.primary_paid,
	patient_charge_list.secondary_paid, patient_charge_list.tertiary_paid,
	patient_charge_list_details.pat_due, patient_data.street, patient_data.street2, patient_data.postal_code, patient_data.zip_ext,
	patient_data.city, patient_data.state, patient_data.country_code, patient_data.phone_home, patient_data.phone_biz, patient_data.phone_biz,
	patient_data.phone_cell, patient_data.preferr_contact   
	FROM patient_charge_list 
	$dueJoin
	LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id 
	JOIN patient_data on patient_data.id = patient_charge_list.patient_id
	WHERE patient_charge_list_details.pat_due > 0 AND patient_charge_list.totalBalance>0 
	AND patient_charge_list_details.del_status='0' $whr_due_date_chk";
	//if(empty($collectionIds)===false){
	//	$ptChrQry .= " and patient_data.pat_account_status NOT IN ($collectionIds)";
	//}
	if(empty($patientId) === false){
		$startLname = '';
		$ptChrQry .= " and patient_charge_list.patient_id in ($patientId)";
	}
	else if(trim($startLname) != ''){
		$ptChrQry .= " and (trim(patient_data.lname) between '$startLname' and '$endLname' 
				or trim(patient_data.lname) like '$endLname%')";
	}				
	if(empty($grp_id) == false){
		$ptChrQry .= " and patient_charge_list.gro_id in ($grp_id)";
	}
	if(trim($insCompanies) != ''){
		$ptChrQry .= " and (patient_charge_list.primaryInsuranceCoId in($insCompanies) 
			OR patient_charge_list.secondaryInsuranceCoId in($insCompanies) 
			OR patient_charge_list.tertiaryInsuranceCoId in($insCompanies))";
	}
	if(empty($strFacilities) == false){
		$ptChrQry .= " and patient_charge_list.facility_id IN ($strFacilities)";
	}
	if(empty($strProviders) == false){
		$ptChrQry .= " and patient_charge_list.primary_provider_id_for_reports in($strProviders)";
	}
	if(empty($grt_bal) == false){
		$ptChrQry .= " and patient_charge_list_details.pat_due > $grt_bal";
	}
	if(trim($cpt_code_id) != ''){
		$ptChrQry.= " AND patient_charge_list_details.procCode in ($cpt_code_id)";
	}		
	if($startDate != '' && $endDate != ''){
		$ptChrQry .= " and (patient_charge_list.date_of_service between '$startDate' and '$endDate')";
	}
	if($DateRangeFor=='dos'){
		$ptChrQry .=" and DATEDIFF(NOW(),date_of_service)>=$aging_start";

		if($All_due == false && ($startDate == '' && $endDate == '')){
			$ptChrQry .= " and DATEDIFF(NOW(),date_of_service)<=$aging_to";
		}
	}else{	//DOT
		$ptChrQry .=" and IF(from_pat_due_date>0,(DATEDIFF(NOW(), from_pat_due_date)>=$aging_start),(DATEDIFF(NOW(),date_of_service)>=$aging_start))";
		
		if($All_due == false && ($startDate == '' && $endDate == '')){
			$ptChrQry .= " and IF(from_pat_due_date>0,(DATEDIFF(NOW(), from_pat_due_date)<=$aging_to),(DATEDIFF(NOW(),date_of_service)<=$aging_to))";
		}	
	}
	$ptChrQry .= " order by patient_data.lname, patient_data.fname, patient_charge_list.date_of_service desc";

	$arrTempEncIds=array();
	$query = imw_query($ptChrQry);
	while($res=imw_fetch_assoc($query)){
		
		//ALL COLLECTION STATUS TOTAL
		if(sizeof($arrCollectionIds)>0 && $arrCollectionIds[$res['pat_account_status']]){
			$totalCollectionBalance+= $res['pat_due'];
			
		}else{
		
			$address='';
			$encounter_id = $res['encounter_id'];
			$arrTempEncIds[$encounter_id]=$res['patient_id'];
			//--- PATIENT NAME -------
			$patinet_id = $res['patient_id'];
			$patient_name_arr = array();
			$patient_name_arr["LAST_NAME"] = $res['lname'];
			$patient_name_arr["FIRST_NAME"] = $res['fname'];
			$patient_name_arr["MIDDLE_NAME"] = $res['mname'];
			$patient_name = changeNameFormat($patient_name_arr);
			
			$arrTempPat[$patinet_id]['lname'] = $res['lname'];
			$arrTempPat[$patinet_id]['fname'] = $res['fname'];
			$arrTempPat[$patinet_id]['mname'] = $res['mname'];
			
			
			$agingCompare = $res["last_dos_diff"];
			if($DateRangeFor=='dot'){
				$agingCompare = $res["last_dop_diff"];
				if($agingCompare==NULL){ $agingCompare = $res["last_dos_diff"]; }
			}
			
			$patientDue = $res['pat_due'];
	
			if($dispDemographics=='1'){
				$arrPatients[$patinet_id] = $patinet_id;
				//ADDRESS
				if($res["street"]!='' && $res["street2"]!=''){
					$address= '&nbsp;'.$res["street"].', '.$res["street2"].'<br>';
				}else{
					$address= '&nbsp;'.$res["street"].'<br>';
				}
				if($res["city"]!=''){ $address.= '&nbsp;'.$res["city"]; }
				if($res["state"]!=''){ $address.= '&nbsp;'.$res["state"]; }
				if($res["postal_code"]!=''){ $address.= '&nbsp;'.$res["postal_code"]; }
				//PHONE
				if($res["preferr_contact"]=='0'){ $address.= '<br>&nbsp;'.$res["phone_home"];}
				else if($res["preferr_contact "]=='1'){ $address.= '<br>&nbsp;'.$res["phone_biz"];}
				else if($res["preferr_contact "]=='2'){ $address.= '<br>&nbsp;'.$res["phone_cell"];}
			}
	
			$patientMainDataArr[$patinet_id]['patient_name'] = $patient_name;
			$patientMainDataArr[$patinet_id]['address'] = $address;
			$arrPatSort[$patinet_id]=$patient_name;
			
			for($a=$aging_start;$a<=$aging_to;$a++){
				$start = $a;
				$a = $a > 0 ? $a - 1 : $a;
				$end = ($a) + $aggingCycle;
	
				if($agingCompare >= $start and  $agingCompare <= $end){
					$patientMainDataArr[$patinet_id][$start][] = $patientDue;
				}
	
				$a += $aggingCycle;				
			}
			
			if($All_due == true){
				if($agingCompare >= 181){
					$patientMainDataArr[$patinet_id][181][] = $patientDue;
				}
			}
		}
			
	}
	//SORTING OF PATIENTS FOR DETAIL VIEW
	asort($arrPatSort);
	
	//GET RESPONSIBLE PARTY
	if(count($arrPatients)>0){
		$strPatients= implode(',', $arrPatients);
		$rs= imw_query("Select patient_id, fname, mname, lname FROM resp_party WHERE patient_id IN(".$strPatients.")");
		while($res = imw_fetch_array($rs)){
			$pid = $res['patient_id'];
			if(!($res['fname']==$arrTempPat[$pid]['fname'] && $res['lname']==$arrTempPat[$pid]['lname'])){
				$patient_name_arr=array();
				$patient_name_arr["LAST_NAME"] = $res['lname'];
				$patient_name_arr["FIRST_NAME"] = $res['fname'];
				$patient_name_arr["MIDDLE_NAME"] = $res['mname'];
				$resp_name = changeNameFormat($patient_name_arr);
				$arrRespParty[$pid]	= $resp_name;			
			}
		}
	}unset($rs); unset($arrTempPat);
	
	//GET ACCOUNTING NOTES
	$arrEncNotes=array();
	if(sizeof($arrTempEncIds)>0){
		$strTempEncIds = implode(', ', array_keys($arrTempEncIds));
		$getDetailsStr = "SELECT encounter_id, encComments,task_assign_for, DATE_FORMAT(reminder_date, '".get_sql_date_format()."') as reminder_date FROM paymentscomment 
		WHERE encounter_id in ($strTempEncIds) 
		$whr_due_date_chk
		ORDER By commentId ASC";
		$getDetailsQry = imw_query($getDetailsStr);
		while($getDetailsRow = imw_fetch_assoc($getDetailsQry)){
			 $pid=$arrTempEncIds[$getDetailsRow['encounter_id']];
			 $strAssignNames='';
			 $arrAssignNames=array();
			 
			 if(empty($getDetailsRow['task_assign_for'])==false){
				 $arrAssign=explode(',', $getDetailsRow['task_assign_for']);
				 foreach($arrAssign as $userid){
					$arrAssignNames[]=$providerNameArr[$userid];
				 }
				 
				 if(sizeof($arrAssignNames)>0){
					$strAssignNames = 'Assign for: <br>'.implode('<br>', $arrAssignNames);
				 }
			 }
			 
			 $arrEncNotes[$pid]['notes']=$getDetailsRow['encComments'];
			 $arrEncNotes[$pid]['task_assign_for']=$strAssignNames;
			 $arrEncNotes[$pid]['reminder_date']=($getDetailsRow['reminder_date']!='00-00-0000')?$getDetailsRow['reminder_date']:'';
		}
	}
	
	
	//--- GET ALL SELECTED INSURANCE COMPANY NAME ---------		
	$insComDetails = array();
	$qry="Select id as insCompId,in_house_code as insCompINHouseCode,name as insCompName FROM insurance_companies";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$id = $res['insCompId'];
		$insName = $ers['insCompINHouseCode'];
		if(trim($insName) == ''){
			$insName = substr($res['insCompName'],0,20);
		}
		$insComDetails[$id] = $insName;
	}
	
	// Common header files for CSV and PDF
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$styleHTML.='<link href="'.$GLOBALS["webroot"].'/library/css/report.css" type="text/css" rel="stylesheet">';
	$styleHTML.='<link href="'.$GLOBALS["webroot"].'/library/css/common.css" type="text/css" rel="stylesheet">';
	$styleHTML.='<link href="'.$GLOBALS["webroot"].'/library/css/bootstrap.css" type="text/css" rel="stylesheet">';
	$styleHTML.='<script type="text/javascript" src="'.$GLOBALS["webroot"].'/library/js/jquery.min.1.12.4.js"></script>';
	$styleHTML.='<script type="text/javascript" src="'.$GLOBALS["webroot"].'/library/js/table2CSV.js"></script>';
	$styleHTML.='<script type="text/javascript">
				function getCSVData(){
					if($("#csv_text").val()==\'\'){
						var csv_value=$(\'#html_data_div\').table2CSV({delivery:\'value\'});
						$("#csv_text").val(csv_value);
						document.csvDownloadForm.submit();
					}
				}
				function resize() {
					var h = window.innerHeight;
					var adj_height = h-85;
					document.getElementById("html_data_div").style.height = adj_height; 
				}
				$(function() { resize(); });
                function loadRptPatient(ptid) {
                    //To check restrict access of patient before load
                    $.when(opener.top.check_for_break_glass_restriction(ptid)).done(function(response){
                        opener.top.removeMessi();
                        if(response.rp_alert==\'y\') {
                            var patId=response.patId;
                            var bgPriv=response.bgPriv;
                            var rp_alert=response.rp_alert;
                            opener.top.core_restricted_prov_alert(patId, bgPriv,\'\',\'\',\'RPT\');
                        }else{
                            opener.top.core_set_pt_session(top.fmain, ptid,\'../accounting/review_payments.php\');
                        }
                    });
                }
			</script>';
	
	$stylePDF='<style>'.file_get_contents('css/reports_pdf.css').'</style>';	
	$stylePDF.='<link href="'.$GLOBALS["webroot"].'/library/css/report.css" type="text/css" rel="stylesheet">';
	$stylePDF.='<link href="'.$GLOBALS["webroot"].'/library/css/bootstrap.css" type="text/css" rel="stylesheet">';
	
	if($summary_detail=='summary'){
	$column = ceil(($aging_to - $aging_start) / $aggingCycle);
	if($dispPrePayments==1){ 
		$column++; 
	}
	if($All_due == true){
		$column++;
	}
	
	//---- GET TOTAL WIDTH FOR TD --------
	$headerWidth = 230;
/* 	switch($column){
		case '1':
			$width = floor(320/$column);
		break;
		case '2':
			$width = floor(425/$column);
			$headerWidth = 231;
		break;
		case '3':
			$width = floor(475/$column);
		break;
		case '4':
			$width = floor(504/$column);
			$headerWidth = 232;
		break;
		case '5':
			$width = floor(520/$column);
			$headerWidth = 230;
		break;
		case '6':
			$width = floor(533/$column);
			$headerWidth = 226;
		break;
		case '7':
			$width = floor(540/$column);
			$headerWidth = 231;
		break;
		case '8':
			$width = floor(544/$column);
			$headerWidth = 235;
		break;
	} */
	
	$column1 = $column +1;
	$width = 850/$column1;	
		
	//--- TOTAL TD ---
	$tds = $column;
	$totalTd = $column+2;
if(count($patientMainDataArr)>0){
	$patient_id_arr = array_keys($patientMainDataArr);
	//-- START FOR ALL PATIENTS -----
	$pageContent = NULL;
	for($i=0;$i<count($patient_id_arr);$i++){
		$patientId = $patient_id_arr[$i];
		$patientDataArr = $patientMainDataArr[$patientId];

		if($dispPrePayments==1){
			$pre_paid=$patientDataArr['pre_paid'];
			$totalPatDueArr['pre_paid'][] = $pre_paid;
			$patientPrint = true;
		}

		for($a=$aging_start;$a<=$aging_to;$a++){
			$start = $a;
			$a = $a > 0 ? $a - 1 : $a;
			$patient_due = 0;
			//--- GET DUE AMOUNT FOR PATIENT -----------
			if(count($patientMainDataArr[$patientId][$start])>0){				
				$patient_due = array_sum($patientMainDataArr[$patientId][$start]);
				$insurance_due = array_sum($insuranceMainDataArr[$patientId][$start]);
				if($disp = 'patientDue' && $patient_due > 0 && $patient_due>$grt_bal){
					$printAllDue = true;
					$totalPatDueArr[$start][] = $patient_due;
				}
				else if($disp = 'insuranceDue' && $insurance_due > 0 && $insurance_due>$grt_bal){
					$patientPrint = true;
					$totalPatDueArr[$start][] = $insurance_due;
				}				
			}
			$a += $aggingCycle;
		}
		//--- GET DUE AMOUNT FOR PATIENT BY 181+ -----------
		if($All_due == true){
			$patient_due = 0;
			//---- GET PATIENT DUE AMOUNT -----
			if(count($patientMainDataArr[$patientId][181])>0){				
				$patient_due = array_sum($patientMainDataArr[$patientId][181]);
				$insurance_due = array_sum($insuranceMainDataArr[$patientId][181]);
				if($disp = 'patientDue' && $patient_due > 0 && $patient_due>$grt_bal){
					$printAllDue = true;
					$totalPatDueArr[181][] = $patient_due;
				}
				else if($disp = 'insuranceDue' && $insurance_due > 0 && $insurance_due>$grt_bal){
					$patientPrint = true;
					$totalPatDueArr[181][] = $insurance_due;
				}					
			}
		}
	}
	
	//PRE-PAYMENT TOTAL TD
	$totalprepaid=0;
	$headerTd = NULL;
	if($dispPrePayments==1){
		$headerTd .='<td class="text_b_w" width="'.$width.'" style="text-align:center;">Pre Payment</td>';
	}
	if(count($totalPatDueArr['pre_paid'])>0){
		$totalprepaid = array_sum($totalPatDueArr['pre_paid']);
		$totalPatDueData.='<td class="text_10b" width="'.$width.'" style="text-align:right">'.$CLSReports->numberFormat($totalprepaid,2).'</td>';
	}
	
	for($a=$aging_start;$a<=$aging_to;$a++){
		$start = $a;
		$a = $a > 0 ? $a - 1 : $a;
		$end = $a + $aggingCycle;
		$headerTd .= <<<DATA
			<td class="text_b_w" width="$width" style="text-align:center;">$start - $end</td>
DATA;
		//--- PATIENT DUE AMOUNT -----
		$totalBalAmount = 0;
		if(count($totalPatDueArr[$start])>0){
			$totalBalAmount = array_sum($totalPatDueArr[$start]);
		}

		// CHART	
		//$label= $start."-".$end;
		//$strXML .= "<set label='" .  escapeXML($label,$forDataURL) . " - $".$totalBalAmount."' value='" . $totalBalAmount . "' isSliced='" . $slicedOut . "' " . $strLink . " />";
		
		$totalBalPatientDueAmount += preg_replace('/,/','',$totalBalAmount);
		//--- Number format -----
		$totalBalAmount = $CLSReports->numberFormat($totalBalAmount,2);
		$totalPatDueData .= <<<DATA
			<td class="text_10b" width="$width" style="text-align:center;">$totalBalAmount</td>
DATA;
	
		$a += $aggingCycle;
	}
	
	//--- GET BALANCE INFORMATION FOR AGGING 180+  -------
	if($All_due == true){
		$totalBalAmount = 0;
		//--- PATIENT DUE AMOUNT -----
		if(count($totalPatDueArr[181])>0){
			$totalBalAmount = array_sum($totalPatDueArr[181]);
		}

		// CHART	
		//$label= "181+";
		//$strXML .= "<set label='" .  escapeXML($label,$forDataURL) . " - $".$totalBalAmount."' value='" . $totalBalAmount . "' isSliced='" . $slicedOut . "' " . $strLink . " />";
		
		$totalBalPatientDueAmount += preg_replace('/,/','',$totalBalAmount);

		$totalBalAmount = $CLSReports->numberFormat($totalBalAmount,2);
		/*
		//--- INSURANCE DUE AMOUNT -----
		$insDueAmount = 0;
		if(count($totalInsDueArr[181])>0){
			$insDueAmount = array_sum($totalInsDueArr[181]);
		}
		$totalBalInsDueAmount += preg_replace('/,/','',$insDueAmount);
		$insDueAmount = $CLSReports->numberFormat($insDueAmount,2);
		*/
		$headerTd .= <<<DATA
			<td class="text_b_w" width="$width" style="text-align:center;">181+</td>
DATA;
		//--- PATIENT DUE AMOUNT BY 181+ -----
		$totalPatDueData .= <<<DATA
			<td class="text_10b" width="$width" style="text-align:center;">$totalBalAmount</td>
DATA;
		/*
		//--- INSURANCE DUE AMOUNT BY 181+ -----
		$totalInsDueData .= <<<DATA
			<td class="text_10b" width="$width" align="right">$insDueAmount</td>
DATA;
		*/
	}
	$totalBalPatientDueAmount= $totalBalPatientDueAmount-$totalprepaid;
	
	$grandTotal = $totalBalPatientDueAmount + $totalCollectionBalance; 
	$totalCollectionBalance = $CLSReports->numberFormat($totalCollectionBalance,2,1);
	$grandTotal = $CLSReports->numberFormat($grandTotal,2);
	
	
	//--- GRAND TOTAL NUMBER FORMAT ----
	$totalBalPatientDueAmount = $CLSReports->numberFormat($totalBalPatientDueAmount,2);
	/*$totalBalInsDueAmount = $CLSReports->numberFormat($totalBalInsDueAmount,2);/*/
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = ucfirst(trim($op_name_arr[1][0]));
	$op_name .= ucfirst(trim($op_name_arr[0][0]));

	//--- CREATE MAIN VARIABLE FOR HTML FILE ------
	$pdf_page_content = <<<DATA
		$stylePDF
		<page backtop="18mm" backbottom="5mm">
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		<page_header>		
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050">
			<tr class="rpt_headers">
				<td class="rptbx1" width="350">
					A/R Aging - Patient $summary_detail
				</td>
				
				<td class="rptbx2" width="350">
					Date ($Start_date - $End_date)
				</td>
				<td class="rptbx3" width="350">
					Created by: $op_name on $curDate
				</td>
			</tr>
		</table>
		<table class="rpt rpt_table rpt_table-bordered" width="1050">
			<tr>
				<td class="text_b_w" width="90" align="center">&nbsp;</td>
				$headerTd
				<td class="text_b_w" width="$width" align="right">Balance</td>
			</tr>
		</table>
		</page_header>		
		<table class="rpt rpt_table rpt_table-bordered" width="100%" bgcolor="#FFF3E8">	
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" width="90" align="right">Total Patient : </td>
				$totalPatDueData
				<td class="text_10b" bgcolor="#FFFFFF" width="$width" align="right">$totalBalPatientDueAmount</td>
			</tr>
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>		
			<tr bgcolor="#FFFFFF">
				<td class="text_10b" width="100" style="text-align:right;">Patients Under Collection : </td>
				<td colspan="$tds" bgcolor="#FFFFFF"></td>
				<td class="text_10b" width="$width" style="text-align:right;">$totalCollectionBalance</td>
			</tr>
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>										
			<tr bgcolor="#FFFFFF">
				<td class="text_10b" width="100" style="text-align:right;">Grand Total : </td>
				<td colspan="$tds" bgcolor="#FFFFFF"></td>
				<td class="text_10b" width="$width" style="text-align:right;">$grandTotal</td>
			</tr>
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>										
		</table>
		</page>
DATA;

	//--- CREATE MAIN VARIABLE FOR CSV FILE ------
	$csv_page_content = <<<DATA
		$styleHTML
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050">
			<tr class="rpt_headers">
				<td class="rptbx1" width="350">
					A/R Aging - Patient $summary_detail
				</td>
				
				<td class="rptbx2" width="350">
					Date ($Start_date - $End_date)
				</td>
				<td class="rptbx3" width="350">
					Created by: $op_name on $curDate
				</td>
			</tr>
		</table>
		<table class="rpt rpt_table rpt_table-bordered"  width="1050">
			<tr>
				<td class="text_b_w" width="100" style="text-align:center;">&nbsp;</td>
				$headerTd
				<td class="text_b_w" width="$width" style="text-align:right;">Balance</td>
			</tr>
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>
			<tr bgcolor="#FFFFFF">
				<td class="text_10b" width="100" style="text-align:right;">Total Patient : </td>
				$totalPatDueData
				<td class="text_10b" width="$width" style="text-align:right;">$totalBalPatientDueAmount</td>
			</tr>
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>										
			<tr bgcolor="#FFFFFF">
				<td class="text_10b" width="100" style="text-align:right;">Patients Under Collection : </td>
				<td colspan="$tds"></td>
				<td class="text_10b" width="$width" style="text-align:right;">$totalCollectionBalance</td>
			</tr>
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>										
			<tr bgcolor="#FFFFFF">
				<td class="text_10b" width="100" style="text-align:right;">Grand Total : </td>
				<td colspan="$tds"></td>
				<td class="text_10b" width="$width" style="text-align:right;">$grandTotal</td>
			</tr>
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>										
		</table>
DATA;
	
	$finalContant = "<div id='html_data_div' style='overflow-y:auto'>$csv_page_content</div><br />";
	$finalContant .= "<div id='module_buttons' class='text-center ad_modal_footer'><button type=\"button\" class=\"btn btn-success\" onclick=\"opener.top.fmain.generate_pdf('l')\";>Print PDF</button>&nbsp;&nbsp;<button type=\"button\" class=\"btn btn-success\" onclick=\"getCSVData();\">Export CSV</button></div><form name=\"csvDownloadForm\" id=\"csvDownloadForm\" action=\"".$GLOBALS['webroot']."/interface/reports/downloadFile.php\" method =\"post\" > 
	<input type=\"hidden\" name=\"csv_text\" id=\"csv_text\">	
	<input type=\"hidden\" name=\"csv_file_name\" id=\"csv_file_name\" value=\"Patient_AR.csv\" />
	</form>";
	$file_location = write_html($pdf_page_content);
	$file = write_html($finalContant, "patient_ar_aging.html");	
	$file_path = str_ireplace($GLOBALS['fileroot'],$GLOBALS['webroot'],$file);
		echo '<div class="text-center alert alert-info">Result is populated in separate window.</div>';
	}else {
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}else{
	$printAllDue = false;
	$column = ceil(($aging_to - $aging_start) / $aggingCycle);
	if($dispPrePayments==1){ 
		$column++; 
	}
	if($summary_detail=='detail'){
		$column=$column+2; //TOW COLUMNS RELATED TO NOTES
	}
	if($All_due == true){
		$column++;
	}
	$column=$column+2;

//---- GET TOTAL WIDTH --------
$headerWidth = 230;
/* switch($column){
	case '1':
		$width = floor(270/$column);
	break;
	case '2':
		$width = floor(357/$column);
		$headerWidth = 228;
	break;
	case '3':
		$width = floor(399/$column);
	break;
	case '4':
		$width = floor(424/$column);
		$headerWidth = 232;
	break;
	case '5':
		$width = floor(439/$column);
		$headerWidth = 228;
	break;
	case '6':
		$width = floor(449/$column);
		$headerWidth = 228;
	break;
	case '7':
		$width = floor(456/$column);
		$headerWidth = 234;
	break;
	case '8':
		$width = floor(460/$column);
		$headerWidth = 238;
	break;
} */

$firstColWidth=15;
$width = round((100-$firstColWidth)/($column-1),2);
$firstColWidth.='%';
$width.='%';

$column1 = $column;
//--- TOTAL TD ---
$tds= $column-2;
$totalTd = $column;


//---- CHECK PATIENT ID ------
if(count($arrPatSort)>0){
	//-- START FOR ALL PATIENT -----
	$csvPageContent = $pdfPageContent = NULL;
	foreach($arrPatSort as $patientId => $patName){
		$accountNotes=$accountNotesDet='';
		$patientPrint == false;
		$pre_paid=0;
		$patientDataArr = $patientMainDataArr[$patientId];
		$patient_name = $patientDataArr['patient_name'];
		$patient_name .= ' - '.$patientId;
		$patient_namePDF = $patientDataArr['patient_name'];
		$patient_namePDF .= ' - '.$patientId;
		
		
		
		
		$address = '<br>'.$patientDataArr['address'];
		//$addressPFD = '<br>'.wordwrap($patientDataArr['address'], 23, "<br>\n", true);
		$addressPFD = '';
		if($arrRespParty[$patientId]){
			$address.='<br>Resp Party: '.$arrRespParty[$patientId];
			$addressPFD.='<br>Resp Party:<br>';
			$addressPFD.= $arrRespParty[$patientId];
		}
		$accountNotesPDF="";
		if($arrEncNotes[$patientId]){
			$accountNotes=$arrEncNotes[$patientId]['notes'];
			$accountNotesPDF=$arrEncNotes[$patientId]['notes'];
			if(strlen($accountNotes)>100){
				$accountNotes = substr($accountNotes,0,100).'...';
				$accountNotes = wordwrap($accountNotes, 12, "<br>\n", true);	
			}
			if(strlen($accountNotesPDF)>50){
				$accountNotesPDF = substr($accountNotesPDF,0,50).'...';
			}
			$accountNotesPDF = wordwrap($accountNotesPDF, 11, "<br>\n", true);	
			$accountNotesDet=$arrEncNotes[$patientId]['reminder_date'].'<br>'.$arrEncNotes[$patientId]['task_assign_for'];
		}
		
		if($dispPrePayments==1){
			$pre_paid=$patientDataArr['pre_paid'];
			$totalPatDueArr['pre_paid'][] = $pre_paid;
			$patientPrint = true;
		}

		//---- GET DATA FOR SINGLE PATIENT SPECIFIC DATA ----------
		$patientDueData = NULL;
		$subTotalPatientBal = 0;
		for($a=$aging_start;$a<=$aging_to;$a++){
			$start = $a;
			$a = $a > 0 ? $a - 1 : $a;
			$patient_due = 0;
			//--- PATIENT DUE AMOUNT -----
			if(count($patientMainDataArr[$patientId][$start])>0){				
				$patient_due = array_sum($patientMainDataArr[$patientId][$start]);
				if($patient_due > 0 && $patient_due>$grt_bal){
					$patientPrint = true;
					$totalPatDueArr[$start][] = $patient_due;
				}
			}
			$a += $aggingCycle;
			$subTotalPatientBal += preg_replace('/,/','',$patient_due);
			$patient_due = $CLSReports->numberFormat($patient_due,2);
			$patientDueData.='<td class="text_10" style="text-align:right; vertical-align:top; width:'.$width.'">'.$patient_due.'</td>';
		}
		//--- PATIENT DUE AMOUNT BY 181+ -----
		if($All_due == true){			
			if(count($patientMainDataArr[$patientId][181])>0 && array_sum($patientMainDataArr[$patientId][181])>$grt_bal){
				$patient_due = array_sum($patientMainDataArr[$patientId][181]);
				if($patient_due > 0 && $patient_due>$grt_bal){
					$patientPrint = true;
					$totalPatDueArr[181][] = $patient_due;
				}
			}
			
			$subTotalPatientBal += preg_replace('/,/','',$patient_due);
			$patient_due = $CLSReports->numberFormat($patient_due,2);
			$patientDueData .='<td class="text_10" style="text-align:right; vertical-align:top; width:'.$width.'">'.$patient_due.'</td>';
		}
		
		//--- GET SINGLE PATIENT DATA ----
		if($patientPrint == true or $printIns == true){
			$printAllDue = true;
			$subTotalPatientBal= $subTotalPatientBal-$pre_paid; 
			$subTotalPatientBal = $CLSReports->numberFormat($subTotalPatientBal,2);
			$pdfPageContent.='
				<tr bgcolor="#FFFFFF">
					<td class="text_10" align="left" style="width:'.$firstColWidth.'">'.$patient_namePDF.' '.$addressPFD.'</td>
					<td class="text_10" style="text-align:left; width:'.$width.'">'.$accountNotesPDF.'</td>
					<td class="text_10" style="text-align:left; width:'.$width.'">'.$accountNotesDet.'</td>';	

			if($dispPrePayments==1){
				$pdfPageContent.='<td class="text_10" style="text-align:right; width:'.$width.'">'.$CLSReports->numberFormat($pre_paid,2).'</td>';
			}
			$pdfPageContent.=					
					$patientDueData.'
					<td class="text_10" align="right" style="vertical-align:top; width:'.$width.'">'.$subTotalPatientBal.'</td>
				</tr>';

			//--- CSV PAGE CONTENT ---
			$csvPageContent.='
				<tr bgcolor="#FFFFFF">
					<td class="text_10" width="200">';
					
			$csvPageContent .= "<a href='javascript:void(0);' onClick=\"loadRptPatient($patientId)\"; class='text_10b_purpule'>".$patient_name."</a>".$address."</td>
					<td class=\"text_10\" width=\"80\" style=\"text-align:left\">".$accountNotes."</td>
					<td class=\"text_10\" width=\"80\" style=\"text-align:left\">".$accountNotesDet."</td>";
			if($dispPrePayments==1){
				$csvPageContent.='<td class="text_10" style="text-align:right; width:'.$width.'">'.$CLSReports->numberFormat($pre_paid,2).'</td>';
			}	
			$csvPageContent .=
				$patientDueData.'
				<td class="text_10" style="text-align:right; vertical-align:top; width:'.$width.'">'.$subTotalPatientBal.'</td>
			</tr>';
		}
	}
	

	if($summary_detail=='detail'){
		$totalPatDueData.='<td class="text_10b" style="text-align:right; width:'.$width.'"></td>
						   <td class="text_10b" style="text-align:right; width:'.$width.'"></td>';
	}
	//PRE-PAYMENT TOTAL TD
	$totalprepaid=0;
	if(count($totalPatDueArr['pre_paid'])>0){
		$totalprepaid = array_sum($totalPatDueArr['pre_paid']);
		$totalPatDueData .='<td class="text_10b" style="text-align:right; width:'.$width.'">'.$CLSReports->numberFormat($totalprepaid,2).'</td>';
	}
	//---- HEADER DATA VARIABLE -----------
	$headerTd = NULL;
	for($a=$aging_start;$a<=$aging_to;$a++){
		$start = $a;
		$a = $a > 0 ? $a - 1 : $a;
		$end = ($a) + $aggingCycle;
		$headerTd .= <<<DATA
			<td class="text_b_w" style="text-align:right; width:$width">$start - $end</td>
DATA;
		//--- PATIENT DUE AMOUNT -----
		$totalBalAmount = 0;
		if(count($totalPatDueArr[$start])>0){
			$totalBalAmount = array_sum($totalPatDueArr[$start]);
		}
		$totalBalPatientDueAmount += preg_replace('/,/','',$totalBalAmount);
		//--- NUMBER FORMAT -----
		$totalBalAmount = $CLSReports->numberFormat($totalBalAmount,2);
		$totalPatDueData .= <<<DATA
			<td class="text_10b" style="text-align:right; width:$width">$totalBalAmount</td>
DATA;
		$a += $aggingCycle;
	}
	
	//--- GET BALANCE INFORMATION FOR AGGING 180+  -------
	if($All_due == true){
		$totalBalAmount = 0;
		//--- PATIENT DUE AMOUNT -----
		if(count($totalPatDueArr[181])>0){
			$totalBalAmount = array_sum($totalPatDueArr[181]);
		}

		$totalBalPatientDueAmount += preg_replace('/,/','',$totalBalAmount);
		$totalBalAmount = $CLSReports->numberFormat($totalBalAmount,2);
		$headerTd .= <<<DATA
			<td class="text_b_w" tyle="text-align:right; width:$width">181+</td>
DATA;
		//--- PATIENT DUE AMOUNT BY 181+ -----
		$totalPatDueData .= <<<DATA
			<td class="text_10b" style="text-align:right; width:$width">$totalBalAmount</td>
DATA;
	}
	$totalBalPatientDueAmount= $totalBalPatientDueAmount-$totalprepaid;

	$grandTotal = $totalBalPatientDueAmount + $totalCollectionBalance; 
	$totalCollectionBalance = $CLSReports->numberFormat($totalCollectionBalance,2,1);
	$grandTotal = $CLSReports->numberFormat($grandTotal,2);
	
	//--- GRAND TOTAL NUMBER FORMAT -----
	$totalBalPatientDueAmount = $CLSReports->numberFormat($totalBalPatientDueAmount,2);
	$totalBalInsDueAmount = $CLSReports->numberFormat($totalBalInsDueAmount,2);
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = ucfirst(trim($op_name_arr[1][0]));
	$op_name .= ucfirst(trim($op_name_arr[0][0]));
	
	//---- CREATE MAIN VARIABLE FOR HTML FILE ----
	$prePayTd='';
	if($dispPrePayments==1){
		$prePayTdPDF='<td class="text_b_w" style="text-align:center; width:'.$width.'">Prepayment</td>';
		$prePayTd='<td class="text_b_w" style="text-align:center; width:'.$width.'">Pre Payment</td>';
	}

echo $totalTd;
	$pdf_page_content = <<<DATA
		$stylePDF
		<page backtop="5mm" backbottom="4mm">
		<page_footer>
			<table style="width: 100%;">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		<page_header>		
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" >
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:33%">
					A/R Aging - Patient $summary_detail
				</td>
				
				<td class="rptbx2" style="width:33%">
					Date ($Start_date - $End_date)
				</td>
				<td class="rptbx3" style="width:34%">
					Created by: $op_name on $curDate
				</td>
			</tr>
		</table>
		</page_header>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" >
			<tr>
				<td class="text_b_w" style="text-align:center; width:$firstColWidth">Description</td>
				<td class="text_b_w" style="text-align:center; width:$width">A/C Notes</td>
				<td class="text_b_w" style="text-align:center; width:$width">Next Follow Up Date</td>
				$prePayTdPDF
				$headerTd
				<td class="text_b_w" style="text-align:right; width:$width">Balance</td>
			</tr>
			$pdfPageContent
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>
			<tr>
				<td class="text_10b" style="text-align:right; width:$width">Total Patient</td>
				$totalPatDueData
				<td class="text_10b" style="text-align:right; width:$width">$totalBalPatientDueAmount</td>
			</tr>
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>
			<tr bgcolor="#FFFFFF">
				<td class="text_10b" style="text-align:right; width:$width">Patients Under Collection : </td>
				<td colspan="$tds" bgcolor="#FFFFFF"></td>
				<td class="text_10b" style="text-align:right; width:$width">$totalCollectionBalance</td>
			</tr>
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>											
			<tr bgcolor="#FFFFFF">
				<td class="text_10b" style="text-align:right; width:$width">Grand Total : </td>
				<td colspan="$tds" bgcolor="#FFFFFF"></td>
				<td class="text_10b" style="text-align:right; width:$width">$grandTotal</td>
			</tr>
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>											
		</table>
		</page>
DATA;
	$file_location = write_html($pdf_page_content);
	//---- CREATE MAIN VARIABLE FOR CSV FILE ----
	$csv_page_content = <<<DATA
		$styleHTML
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" >
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:33%">
					A/R Aging - Patient $summary_detail
				</td>
				
				<td class="rptbx2" style="width:33%">
					Date ($Start_date - $End_date)
				</td>
				<td class="rptbx3" style="width:34%">
					Created by: $op_name on $curDate
				</td>
			</tr>
		</table>
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" >
			<tr>
				<td class="text_b_w" style="text-align:center; width:$firstColWidth">Description</td>
				<td class="text_b_w" style="text-align:center; width:$width">A/C Notes</td>
				<td class="text_b_w" style="text-align:center; width:$width">Next Follow Up Date</td>
				$prePayTd
				$headerTd
				<td class="text_b_w" style="text-align:right; width:$width">Balance</td>
			</tr>
			$csvPageContent
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>
			<tr bgcolor="#FFFFFF">
				<td class="text_10b" style="text-align:right; width:$width">Total Patient : </td>
				$totalPatDueData
				<td class="text_10b" style="text-align:right; width:$width">$totalBalPatientDueAmount</td>
			</tr>
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>											
			<tr bgcolor="#FFFFFF">
				<td class="text_10b" style="text-align:right; width:$width">Patients Under Collection : </td>
				<td colspan="$tds" bgcolor="#FFFFFF"></td>
				<td class="text_10b" style="text-align:right; width:$width">$totalCollectionBalance</td>
			</tr>
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>											
			<tr bgcolor="#FFFFFF">
				<td class="text_10b" style="text-align:right; width:$width">Grand Total : </td>
				<td colspan="$tds" bgcolor="#FFFFFF"></td>
				<td class="text_10b" style="text-align:right; width:$width">$grandTotal</td>
			</tr>
			<tr>
				<td class="total-row" colspan="$totalTd"></td>
			</tr>											
		</table>
		</page>
DATA;
	$finalContant = "<div id='html_data_div' style='overflow-y:scroll'>$csv_page_content</div><br />";
	$finalContant .= "<div id='module_buttons' class='text-center ad_modal_footer'><button type=\"button\" class=\"btn btn-success\" onclick=\"opener.top.fmain.generate_pdf('l')\";>Print PDF</button>&nbsp;&nbsp;<button type=\"button\" class=\"btn btn-success\" onclick=\"getCSVData();\">Export CSV</button></div><form name=\"csvDownloadForm\" id=\"csvDownloadForm\" action=\"".$GLOBALS['webroot']."/interface/reports/downloadFile.php\" method =\"post\" > 
		<input type=\"hidden\" name=\"csv_text\" id=\"csv_text\">	
		<input type=\"hidden\" name=\"csv_file_name\" id=\"csv_file_name\" value=\"Patient_AR.csv\" />
	</form>";
	$file = write_html($finalContant, "patient_ar_aging.html");	
	$file_path = str_ireplace($GLOBALS['fileroot'],$GLOBALS['webroot'],$file);
		if($callFrom != 'scheduled'){
			echo '<div class="text-center alert alert-info">Result is populated in separate window</div>';
		}
	} else {
		if($callFrom != 'scheduled'){
			echo '<div class="text-center alert alert-info">No Record Found.</div>';
		}
	}
}
if(count($arrPatSort)>0 || count($patientMainDataArr)>0){
?>
<script>
	var url ='<?php echo $file_path; ?>';
	var number = 1 + Math.floor(Math.random() * 6);
	//url += '&num='+number;
	var n = url.substring(url.lastIndexOf('/')+1);
		temp_n= n.split('.');
		n = temp_n[0];
		if(top.arr_opened_popups[n] && (top.arr_opened_popups[n].closed == false)){
			var n = top.arr_opened_popups[n].location.href=url;
			n.focus();
		}else{
			top.popup_win('<?php echo $file_path; ?>','resizable=1');
		}
</script>
<?php } ?>
