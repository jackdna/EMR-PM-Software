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
?>
<?php
/*
FILE : yearlyResult.php
PURPOSE : Display results of yearly report
ACCESS TYPE : Direct
*/

$printFile = true;

$chartAppointments = array();
$chartCharges = array();
$grandTotals = array();
$FCName = $_SESSION['authId'];
if($_POST['form_submitted']){
	
	$startDate="$Start_year-01-01";
	$endDate="$End_year-12-31";

	$startDateMonth=$Start_year.'-01';
	$endDateMonth=$End_year.'-12';

	//MAKE COMMA SEPEARATED OF ALL SEARCH CRITERIA
	$grp_id= (sizeof($grp_id)>0) ? implode(',',$grp_id) : '';
	$sc_name= (sizeof($sc_name)>0) ? implode(',',$sc_name) : '';
	$Physician= (sizeof($Physician)>0) ? implode(',',$Physician) : '';
	//---------------------------------------


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
	}unset($qryRs);					
	
	// ------------------------------
	//GET SCHEDULER FACILITY IDS
	$sch_fac_arr=array();
	$sch_fac_str='';
	$rs=imw_query("Select id,fac_prac_code FROM facility");
	while($res=imw_fetch_array($rs)){
		$sch_fac_arr[$res['id']] = $res['fac_prac_code'];
	}unset($rs);
	
	if($date_range_for=='DOS'){

	$printFile = false;
	//--- GET ALL CHARGES DATA ----	
	$chg_query = "select patient_charge_list.charge_list_id,patient_charge_list.totalAmt, patient_charge_list.date_of_service, 
				patient_charge_list.encounter_id, patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId', 
				patient_charge_list.facility_id 
				FROM patient_charge_list join users on users.id = patient_charge_list.primary_provider_id_for_reports 
				join patient_data ON patient_data.id = patient_charge_list.patient_id 
				where patient_data.lname !='doe' AND patient_charge_list.del_status='0' 
				and date_format(patient_charge_list.date_of_service,'%Y') between '$Start_year' and '$End_year'";
	if(trim($grp_id) != ''){
		$chg_query.= " AND patient_charge_list.gro_id IN ($grp_id)";
	}
	if(trim($Physician) != ''){
		$chg_query .= " and patient_charge_list.primary_provider_id_for_reports in ($Physician)";
	}
	if(trim($sc_name) != ''){
		$chg_query .= " and patient_charge_list.facility_id IN($sc_name)";
	}
	$chg_query .= " order by users.lname, users.fname, date_format(patient_charge_list.date_of_service,'%Y')";

	$chgQryRs = imw_query($chg_query);
	$totalChargesDataArr = array();
	$pro_id_arr = array();
	$fac_id_arr = array();
	while($chgQryRes = imw_fetch_array($chgQryRs)){
		$patDue = $insDue = $balanceAmt='';
		list($dt_year,$dt_month,$dt_date) = preg_split('/-/',$chgQryRes['date_of_service']);
		$primaryProviderId = $chgQryRes['primaryProviderId'];
		$fac_id = $chgQryRes['facility_id'];
		$pro_id_arr[$primaryProviderId] = $primaryProviderId;
		$fac_id_arr[$fac_id] = $fac_id;
		$firstGroupId = $primaryProviderId;
		
		$rs=imw_query("Select SUM(newBalance) as 'newBalance', SUM(overPaymentForProc) as 'overPaymentForProc', SUM(pat_due) as 'patientDue' ,SUM(pri_due+sec_due+tri_due) as 'insuranceDue' FROM patient_charge_list_details WHERE charge_list_id ='".$chgQryRes['charge_list_id']."' AND del_status='0'");
		$res=imw_fetch_array($rs);
		$chgQryRes['patientDue'] = $res['patientDue'];		
		$chgQryRes['insuranceDue'] = $res['insuranceDue'];	
		
		$patDue = ($chgQryRes['patientDue']<0) ? 0 : $chgQryRes['patientDue']; 
		//$patDue = ($chgQryRes['totalBalance']<=0) ? 0 : $chgQryRes['patientDue']; 
		$totalChargesDataArr[$firstGroupId][$dt_year][$dt_month]['patientDue'][] = $patDue;
		$insDue = ($chgQryRes['insuranceDue']<0) ? 0 : $chgQryRes['insuranceDue']; 
		$balanceAmt = $res['newBalance'];
		if($res['overPaymentForProc']>0) { $balanceAmt= $balanceAmt - $res['overPaymentForProc']; }
		$totalChargesDataArr[$firstGroupId][$dt_year][$dt_month]['insuranceDue'][] = $insDue;
		$totalChargesDataArr[$firstGroupId][$dt_year][$dt_month]['balanceAmt'][] = $balanceAmt;
		$totalChargesDataArr[$firstGroupId][$dt_year][$dt_month]['date_of_service'][] = $chgQryRes['date_of_service'];
		$totalChargesDataArr[$firstGroupId][$dt_year][$dt_month]['encounter_id'][] = $chgQryRes['encounter_id'];
		$totalChargesDataArr[$firstGroupId][$dt_year][$dt_month]['overPayment'][] = $res['overPaymentForProc'];
		
		//FACILITY SUMMARY
		$arrFacSummary[$fac_id]['pat_due']+=$patDue;
		$arrFacSummary[$fac_id]['ins_due']+=$insDue;
		$arrFacSummary[$fac_id]['credit_amt']+=$res['overPaymentForProc'];
		$arrFacSummary[$fac_id]['bal_amt']+=$balanceAmt;
		
		$tempFacForEnc[$chgQryRes['encounter_id']]=$fac_id;
	}


	//--- GET PROVIDER DATA -----
	$provider_data_arr = array();
	$providerIdArr = array_keys($totalChargesDataArr);
	for($p=0;$p<count($providerIdArr);$p++){
		$firstGroupId = $providerIdArr[$p];
		$year_data_arr = $totalChargesDataArr[$firstGroupId];
		$year_arr = array_keys($year_data_arr);
		for($y=0;$y<count($year_arr);$y++){
			$dt_year = $year_arr[$y];
			$month_data_arr = $year_data_arr[$dt_year];
			$month_arr = array_keys($month_data_arr);

			for($m=0;$m<count($month_arr);$m++){
				$dt_month = $month_arr[$m];
				$charge_data_arr = $month_data_arr[$dt_month];
				$all_encounters='';
				$appointments=0;
				//--- TOTAL CHARGES AMOUNT ---
				$total_pat_due = array_sum($charge_data_arr['patientDue']);
				$total_ins_due = array_sum($charge_data_arr['insuranceDue']);
				$total_credit_amt = array_sum($charge_data_arr['overPayment']);
				$total_balance_amt = array_sum($charge_data_arr['balanceAmt']);
				$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["patientDue"] = $total_pat_due;				
				$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["insuranceDue"] = $total_ins_due;
				$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["credit_amount"] = $total_credit_amt;	
				$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["balanceAmt"] = $total_balance_amt;				

				//--- GET TOTAL APPOINTMENTS DATA ------
				$st_date = $dt_year.'-'.$dt_month.'-01';
				$last_date = cal_days_in_month(CAL_GREGORIAN, $dt_month, $dt_year);
				$end_date = $dt_year.'-'.$dt_month.'-'.$last_date;
				$appQryPart='';

				$app_query = "select sa_facility_id, count(id) as total_app from schedule_appointments
							where sa_app_start_date between '$st_date' and '$end_date'";
				$appQryPart= " AND sa_doctor_id = '$firstGroupId'";
				$app_query.=$appQryPart." AND sa_patient_app_status_id NOT IN(203,201,18,19,20,3) GROUP BY sa_facility_id";

				$appQryRs = imw_query($app_query);
				$appointments=0;
				while($appQryRes = imw_fetch_assoc($appQryRs)){
					$fac_id= $sch_fac_arr[$appQryRes['sa_facility_id']];
					$arrFacSummary[$fac_id]['appt']+=$appQryRes['total_app'];
					$appointments+= $appQryRes['total_app'];
				}
				unset($appQryRs);
				$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["total_appointments"] = $appointments;
				$all_encounters = implode(",",$charge_data_arr["encounter_id"]);

				//--- GET CHARGES and WRITE-OFF AMOUMNT FOR EVERY MONTH ----
				$writeOffArr = array();	
				$totalAmtArr = array();
				$totalChargesArr = array();
				$totWriteOffAmt=0;
				$arr_charge_list_id=array();
				$writeOffQryRs = imw_query("Select patChg.encounter_id, patChgDet.write_off,
					patChgDet.procCharges * patChgDet.units as totalAmt, patChgDet.charge_list_detail_id, patChg.facility_id   
					FROM patient_charge_list patChg 
					JOIN patient_charge_list_details patChgDet ON patChgDet.charge_list_id = patChg.charge_list_id 
					where patChg.encounter_id IN($all_encounters) AND patChgDet.del_status='0'");				
				while($writeOffQryRes = imw_fetch_array($writeOffQryRs)){
					$fac_id=$writeOffQryRes['facility_id'];
					$writeOffArr[] = $writeOffQryRes['write_off']; 
					$totalChargesArr[] = $writeOffQryRes['totalAmt']; 	
					$arr_charge_list_id[$writeOffQryRes['charge_list_detail_id']] = $writeOffQryRes['charge_list_detail_id'];
					
					$arrFacSummary[$fac_id]['tot_charges']+=$writeOffQryRes['totalAmt'];
					$arrFacSummary[$fac_id]['adj_amt']+=$writeOffQryRes['write_off'];
				}
				
				$totWriteOffAmt = array_sum($writeOffArr);
				$totChargesAmt = array_sum($totalChargesArr);
				$str_charge_list_id = implode(',',$arr_charge_list_id);

				//--- GET ADJUSTMENT AMOUMNT FOR EVERY MONTH ----
				$arrAdjustmentAmt = array();
				$adjustAmt=0;
				$arrAdjustmentAmt = $CLSReports->getReportAdjustmentAmtCopy($all_encounters);	
				$adjustAmt = array_sum($arrAdjustmentAmt) + $totWriteOffAmt;							
				$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["adjustment_amount"] = $adjustAmt;
				//FACILITY SUMMARY
				foreach($arrAdjustmentAmt as $enc_id => $adjAmt){
					$fac_id=$tempFacForEnc[$enc_id];
					$arrFacSummary[$fac_id]['adj_amt']+=$adjAmt;
				}

				$pay_crd_deb_arr =array();
				//	GET CREDIT AMOUNT
				$credit_qry = $CLSReports->__getCreditappliedRept($str_charge_list_id, $mode='CHARGELISTID');
				$credit_qry_rs=imw_query($credit_qry);
				while($credit_qry_res = imw_fetch_assoc($credit_qry_rs)){	
					$encounter_id_adjust = $credit_qry_res['crAppliedToEncId_adjust'];
					$fac_id=$tempFacForEnc[$encounter_id_adjust];
					if($credit_qry_res[$i]['crAppliedTo']=="adjustment"){ 
						if($credit_qry_res[$i]['type']=='Insurance'){
							$pay_crd_deb_arr['Insurance'][]= $credit_qry_res['amountApplied'];
							$arrFacSummary[$fac_id]['ins_paid']+= $credit_qry_res['amountApplied'];
						}else{
							$pay_crd_deb_arr['Patient'][]= $credit_qry_res['amountApplied'];				
							$arrFacSummary[$fac_id]['pat_paid']+= $credit_qry_res['amountApplied'];
						}
						$arrFacSummary[$fac_id]['tot_paid']+=$credit_qry_res['amountApplied'];
					}
				}

				//	GET DEBIT AMOUNT
				$debit_qry = $CLSReports->__getDebitappliedData($str_charge_list_id, $mode='CHARGELISTID');
				$debit_qry_rs=imw_query($debit_qry);
				while($debit_qry_res = imw_fetch_assoc($debit_qry_rs)){	
					$encounter_id = $debit_qry_res['crAppliedToEncId'];
					$fac_id=$tempFacForEnc[$encounter_id];
					if($debit_qry_res['crAppliedTo']=="adjustment"){
						if($debit_qry_res['type']=='Insurance'){
							$pay_crd_deb_arr['Insurance'][]= '-'.$debit_qry_res['amountApplied'];
							$arrFacSummary[$fac_id]['ins_paid']+= '-'.$debit_qry_res['amountApplied'];
						}else{
							$pay_crd_deb_arr['Patient'][]= '-'.$debit_qry_res['amountApplied'];
							$arrFacSummary[$fac_id]['pat_paid']+= '-'.$debit_qry_res['amountApplied'];
						}
						$arrFacSummary[$fac_id]['tot_paid']+='-'.$debit_qry_res['amountApplied'];
					}
				}
				
				//--- GET PAYMENTS DATA ----
				$PAY_QUERY ="Select patient_charges_detail_payment_info.paidForProc + 
							patient_charges_detail_payment_info.overPayment as TotalPayment,
							patient_charges_detail_payment_info.paidBy, 
							patient_chargesheet_payment_info.encounter_id, 
							patient_chargesheet_payment_info.paymentClaims 
							from patient_chargesheet_payment_info join patient_charges_detail_payment_info on
							patient_charges_detail_payment_info.payment_id = patient_chargesheet_payment_info.payment_id
							where patient_charges_detail_payment_info.deletePayment = '0' 
							and patient_chargesheet_payment_info.encounter_id in ($all_encounters)";
				$payQryRs = imw_query($PAY_QUERY);
				$paidAmtArr = array();
				$patPayDetArr = array();
				while($payQryRes = imw_fetch_array($payQryRs)){
					$TotalPayment = $payQryRes['TotalPayment'];
					$encounter_id = $payQryRes['encounter_id'];
					$fac_id=$tempFacForEnc[$encounter_id];
					
					if($payQryRes['paymentClaims'] == 'Negative Payment'){
						$TotalPayment = '-'.$TotalPayment;
					}
					if($payQryRes['paidBy'] == 'Patient' || $payQryRes['paidBy'] == 'Res. Party')
					{
						$patPayDetArr['patPaid'][] = $TotalPayment;
						$arrFacSummary[$fac_id]['pat_paid']+=$TotalPayment;
					}
					else if($payQryRes['paidBy'] == 'Insurance'){
						$patPayDetArr['insPaid'][] = $TotalPayment;
						$arrFacSummary[$fac_id]['ins_paid']+=$TotalPayment;
					}
					$paidAmtArr[] = $TotalPayment;
					$arrFacSummary[$fac_id]['tot_paid']+=$TotalPayment;
				}
				$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["total_charges"] = $totChargesAmt;	
				
				$patCrdDeb = array_sum($pay_crd_deb_arr['Patient']);
				$insCrdDeb = array_sum($pay_crd_deb_arr['Insurance']);
				$paidAmt = array_sum($paidAmtArr) + $patCrdDeb + $insCrdDeb;
				$patPaidAmt = array_sum($patPayDetArr['patPaid']) + $patCrdDeb;
				$insPaidAmt = array_sum($patPayDetArr['insPaid']) + $insCrdDeb;
				$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["TotalPayment"] = $paidAmt;
				$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["patPaid"] = $patPaidAmt;
				$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["insPaid"] = $insPaidAmt;
				
				//GRAND TOTALS ARRAYS
				$grandTotals['APPOINTMENTS'][] = $appointments;
				$grandTotals['CHARGES'][] = $totChargesAmt;
				$grandTotals['PAT_PAID'][] = $patPaidAmt;
				$grandTotals['INS_PAID'][] = $insPaidAmt;
				$grandTotals['TOT_PAID'][] = $paidAmt;
				$grandTotals['CREDIT'][] = $total_credit_amt;
				$grandTotals['ADJUSTMET'][] = $adjustAmt;
				$grandTotals['PAT_DUE'][] = $total_pat_due;
				$grandTotals['INS_DUE'][] = $total_ins_due;
				$grandTotals['BALANCE'][] = $total_balance_amt;
			}
		}
	}

	}else{ // DOP/DOT functionality
	
		$printFile = false;
		//get all enc
		$allEncArrTemp=array();
		$strEncArrTemp='';
		$reptByForFun=strtolower($date_range_for);
		
		$qry="Select patchg.encounter_id, patchg.primary_provider_id_for_reports as 'primaryProviderId',
		patchg.facility_id, patchg.gro_id FROM patient_charge_list patchg
		JOIN patient_data ON patient_data.id=patchg.patient_id WHERE LOWER(patient_data.lname) !='doe'";
		if(trim($Physician) != '')$qry .= " AND patchg.primary_provider_id_for_reports in ($Physician)";
		if(trim($sc_name) != '')$qry .= " and patchg.facility_id IN($sc_name)";
		if(trim($grp_id) != '')$qry .= " and patchg.gro_id IN($grp_id)";

		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$eid=$res['encounter_id'];
			$allEncArrTemp[$res['encounter_id']]=$res['encounter_id'];
			$tempEncInfo[$eid]['physician']=$res['primaryProviderId'];
			$tempEncInfo[$eid]['facility']=$res['facility_id'];
			$tempEncInfo[$eid]['group']=$res['gro_id'];
		}

		$strEncArrTemp=implode(',', $allEncArrTemp);


		//YEAR-MONTH ARRAY
		$i=0;
		$beginDate=$endDate=$delLastDate='';
		for($y=$Start_year; $y<=$End_year; $y++){
			for($m=1; $m<=12; $m++){
				if($m<10){ $m='0'.$m; }
				$st_date = $y.'-'.$m.'-01';
				$last_date = cal_days_in_month(CAL_GREGORIAN, $m, $y);
				$end_date = $y.'-'.$m.'-'.$last_date;
				
				$arrYearMonth[$i]['start']=$st_date;
				$arrYearMonth[$i]['end']=$end_date;
				if(empty($beginDate)==true){
					$beginDate=$st_date;
				}
				$delLastDate=$endDate=$end_date;
				$i++;
			}
		}

		if(sizeof($allEncArrTemp)>0){
			$arrAllEncs=array();
			$arrCheckChgdetId=array();
			foreach($arrYearMonth as $date_range){
				$tempEncIds=array();
				$arrTotAmt=array();
				$st_date=$date_range['start'];
				$end_date=$date_range['end'];
				list($dt_year,$dt_month,$dt_day)= explode('-', $st_date);


				//ADJUSTMENTS
				$arrAdjustmentAmt = array();
				$adjustAmt=0;
				$arrAdjustmentAmt = $CLSReports->getReportAdjustmentAmtCopy($strEncArrTemp,'','',$st_date, $end_date,'yes', $reptByForFun, '', $delLastDate);	
				foreach($arrAdjustmentAmt as $eid => $adjAmt){
					$firstGroupId=$tempEncInfo[$eid]['physician'];
					$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["adjustment_amount"]+= $adjAmt;				
					$tempEncIds[$eid]=$eid;
					$arrTotAmt['adj_amt']+=$adjAmt;
					
					$fac_id=$tempEncInfo[$eid]['facility'];
					$arrFacSummary[$fac_id]['adj_amt']+=$adjAmt;
				}
				unset($arrAdjustmentAmt);

				//GET DEFAULT WRITE-OFF
				$tempDefaultWriteOff=array();
				$writeOffQryRs = $CLSReports->__getDetailWriteOffAmtNew($strEncArrTemp, $st_date, $end_date, '', '', $mode='ENCOUNTERID', '', $reptByForFun, $checkDel='yes', $delLastDate);
				while($writeOffQryRes = imw_fetch_assoc($writeOffQryRs)){
					$eid = $writeOffQryRes[$i]['encounter_id'];
					$chgDetId = $writeOffQryRes['charge_list_detail_id'];
					$firstGroupId=$tempEncInfo[$eid]['physician'];
					$tempDefaultWriteOff[$firstGroupId][$eid][$chgDetId]=$writeOffQryRes['write_off_amount'];
					$tempGrandAdj[$chgDetId]=$writeOffQryRes['write_off_amount'];
					$tempEncForChg[$chgDetId]=$eid;
				}
				foreach($tempDefaultWriteOff as $eid => $chgData){
					foreach($chgata as $chgDetId => $writeoffAmt){
						$firstGroupId=$tempEncInfo[$eid]['physician'];
						$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["adjustment_amount"]+= $writeoffAmt;
						//$arrTotAmt['adj_amt']+=$writeoffAmt;
						$tempEncIds[$eid]=$eid;
					}
				}unset($tempDefaultWriteOff);
				
				$pay_crd_deb_arr =array();
				$tempPaidEnc=array();
				//	GET CREDIT AMOUNT
				$credit_qry_rs = $CLSReports->__getCreditappliedRept($strEncArrTemp,'',$st_date, $end_date,'','','','','','','','yes', $delLastDate, $reptByForFun);
				while($credit_qry_res = imw_fetch_assoc($credit_qry_rs)){
					$eid = $credit_qry_res['crAppliedToEncId_adjust'];
					if($credit_qry_res['crAppliedTo']=="adjustment"){ 
						if($credit_qry_res['type']=='Insurance'){
							$pay_crd_deb_arr[$eid]['Insurance'][]= $credit_qry_res['amountApplied'];
						}else{
							$pay_crd_deb_arr[$eid]['Patient'][]= $credit_qry_res['amountApplied'];				
						}
						$tempEncIds[$eid]=$eid;
						$tempPaidEnc[$eid]=$eid;
					}
				}
				//	GET DEBIT AMOUNT
				$debit_qry_rs = $CLSReports->__getDebitappliedData($strEncArrTemp,'',$st_date, $end_date,'','','','','','','',$checkDel='yes', $delLastDate, $reptByForFun);
				while($debit_qry_res = imw_fetch_assoc($debit_qry_rs)){
					$eid = $debit_qry_res['crAppliedToEncId'];
					if($debit_qry_res['crAppliedTo']=="adjustment"){
						if($debit_qry_res['type']=='Insurance'){
							$pay_crd_deb_arr[$eid]['Insurance'][]= '-'.$debit_qry_res['amountApplied'];
						}else{
							$pay_crd_deb_arr[$eid]['Patient'][]= '-'.$debit_qry_res['amountApplied'];
						}
						$tempEncIds[$eid]=$eid;
						$tempPaidEnc[$eid]=$eid;
					}
				}

				//PAYMENTS
				$paidAmtArr=$patPayDetArr=array();		
				$qry = "Select (patient_charges_detail_payment_info.paidForProc + patient_charges_detail_payment_info.overPayment) as totalPayment,
				patient_charges_detail_payment_info.paidBy, 
				patient_chargesheet_payment_info.encounter_id, 
				patient_chargesheet_payment_info.paymentClaims, patient_chargesheet_payment_info.date_of_payment  
				FROM patient_chargesheet_payment_info 
				LEFT JOIN patient_charges_detail_payment_info on patient_charges_detail_payment_info.payment_id = patient_chargesheet_payment_info.payment_id
				WHERE ((patient_charges_detail_payment_info.deletePayment='0') OR (patient_charges_detail_payment_info.deletePayment='1' AND deleteDate> '$delLastDate'))";
				if($date_range_for=='DOP'){
					$qry.=" AND (patient_chargesheet_payment_info.date_of_payment BETWEEN '$st_date' AND '$end_date')";
				}else{
					$qry.=" AND (patient_chargesheet_payment_info.transaction_date BETWEEN '$st_date' AND '$end_date')";
				}
				if(empty($strEncArrTemp)==false){ 
					$qry.="	AND patient_chargesheet_payment_info.encounter_id IN(".$strEncArrTemp.")";
				}
				$rs=imw_query($qry);
				while($res=imw_fetch_array($rs)){
					$eid=$res['encounter_id'];
					$totalPayment = $res['totalPayment'];
					$patPaid=$insPaid=$patCrdDeb=$insCrdDeb=0;
					
					if($res['paymentClaims'] == 'Negative Payment'){
						$totalPayment = '-'.$totalPayment;
					}
					if($res['paidBy'] == 'Patient' || $res['paidBy'] == 'Res. Party'){
						$patPayDetArr[$eid]['patPaid'][]= $totalPayment;
					}else if($res['paidBy'] == 'Insurance'){
						$patPayDetArr[$eid]['insPaid'][]= $totalPayment;
					}
					$paidAmtArr[$eid][]= $totalPayment;

					$tempEncIds[$eid]=$eid;
					$tempPaidEnc[$eid]=$eid;
				}unset($rs);

				//PAYMENTS
				foreach($tempPaidEnc as $eid){
					$firstGroupId=$tempEncInfo[$eid]['physician'];
					$patCrdDeb=$insCrdDeb=0;
					
					if($pay_crd_deb_arr[$eid]){
						$patCrdDeb = array_sum($pay_crd_deb_arr[$eid]['Patient']);
						$insCrdDeb = array_sum($pay_crd_deb_arr[$eid]['Insurance']);
					}
					$paidAmt = array_sum($paidAmtArr[$eid]) + $patCrdDeb + $insCrdDeb;
					$patPaidAmt = array_sum($patPayDetArr[$eid]['patPaid']) + $patCrdDeb;
					$insPaidAmt = array_sum($patPayDetArr[$eid]['insPaid']) + $insCrdDeb;

					$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["TotalPayment"]+= $paidAmt;
					$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["patPaid"]+= $patPaidAmt;
					$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["insPaid"]+= $insPaidAmt;
										
					$arrTotAmt['tot_paid']+=$paidAmt;
					$arrTotAmt['pat_paid']+=$patPaidAmt;
					$arrTotAmt['ins_paid']+=$insPaidAmt;

					$fac_id=$tempEncInfo[$eid]['facility'];
					$arrFacSummary[$fac_id]['tot_paid']+=$paidAmt;
					$arrFacSummary[$fac_id]['pat_paid']+=$patPaidAmt;
					$arrFacSummary[$fac_id]['ins_paid']+=$insPaidAmt;
				}

				//CHARGES
				$qry = "Select patchg.charge_list_id, patchg.first_posted_date, patchg.date_of_service,
				patchg.encounter_id, patchgdet.overPaymentForProc, patchgdet.newBalance, patchgdet.charge_list_detail_id,  
				patchgdet.pat_due as 'patientDue', (patchgdet.pri_due+patchgdet.sec_due+patchgdet.tri_due) as 'insuranceDue',
				(patchgdet.procCharges * patchgdet.units) as totalAmt 
				FROM patient_charge_list patchg 
				JOIN patient_charge_list_details patchgdet ON patchgdet.charge_list_id= patchg.charge_list_id 
				WHERE ((patchgdet.del_status='0') OR (patchgdet.del_status='1' AND DATE_FORMAT(patchgdet.trans_del_date, '%Y-%m-%d')>'$delLastDate'))";
				if(sizeof($tempEncIds)>0){
					$strEncIds=implode(',', $tempEncIds);
					$qry.=" AND (patchg.encounter_id IN(".$strEncIds.") OR (patchg.first_posted_date BETWEEN '$st_date' AND '$end_date'))";
				}else{
					$qry.=" AND (patchg.first_posted_date BETWEEN '$st_date' AND '$end_date')";
				}
				if(empty($Physician)==false)$qry.=" AND patchg.primary_provider_id_for_reports in ($Physician)";
				if(empty($sc_name)==false)$qry.=" AND patchg.facility_id IN($sc_name)";
				if(empty($grp_id)==false)$qry.=" AND patchg.gro_id IN($grp_id)";
				$rs=imw_query($qry);
				
				while($res=imw_fetch_assoc($rs)){
					$totCharges=$patDue=$insDue=0;
					$eid=$res['encounter_id'];
					$chgdetid=$res['charge_list_detail_id'];
					$fac_id=$tempEncInfo[$eid]['facility'];
					
					$firstGroupId=$tempEncInfo[$eid]['physician'];
					$balanceAmt = $res['newBalance'];

					if($res['overPaymentForProc']>0) { $balanceAmt= $balanceAmt - $res['overPaymentForProc']; }
					$patDue = ($res['patientDue']<0) ? 0 : $res['patientDue']; 
					$insDue = ($res['insuranceDue']<0) ? 0 : $res['insuranceDue']; 
					
					if(($res['first_posted_date']>=$st_date && $res['first_posted_date']<=$end_date)){
						$provider_data_arr[$firstGroupId][$dt_year][$dt_month]['total_charges']+= $res['totalAmt'];
						$provider_data_arr[$firstGroupId][$dt_year][$dt_month]['patientDue']+= $patDue;
						$provider_data_arr[$firstGroupId][$dt_year][$dt_month]['insuranceDue']+= $insDue;
						$provider_data_arr[$firstGroupId][$dt_year][$dt_month]['credit_amount']+= $res['overPaymentForProc'];						
						$provider_data_arr[$firstGroupId][$dt_year][$dt_month]['balanceAmt']+= $balanceAmt;

						$arrTotAmt['tot_charges']+=$res['totalAmt'];
						$arrTotAmt['pat_due']+=$patDue;
						$arrTotAmt['ins_due']+=$insDue;
						$arrTotAmt['credit_amt']+=$res['overPaymentForProc'];
						$arrTotAmt['bal_amt']+=$balanceAmt;
						$arrCheckChgdetId[$chgdetid]=$chgdetid;

						$arrFacSummary[$fac_id]['tot_charges']+=$res['totalAmt'];
						$arrFacSummary[$fac_id]['pat_due']+=$patDue;
						$arrFacSummary[$fac_id]['ins_due']+=$insDue;
						$arrFacSummary[$fac_id]['credit_amt']+=$res['overPaymentForProc'];
						$arrFacSummary[$fac_id]['bal_amt']+=$balanceAmt;					
					}
					
					if(!$arrCheckChgdetId[$chgdetid]){
						$provider_data_arr[$firstGroupId][$dt_year][$dt_month]['patientDue']+= $patDue;
						$provider_data_arr[$firstGroupId][$dt_year][$dt_month]['insuranceDue']+= $insDue;
						$provider_data_arr[$firstGroupId][$dt_year][$dt_month]['credit_amount']+= $res['overPaymentForProc'];						
						$provider_data_arr[$firstGroupId][$dt_year][$dt_month]['balanceAmt']+= $balanceAmt;

						$arrTotAmt['pat_due']+=$patDue;
						$arrTotAmt['ins_due']+=$insDue;
						$arrTotAmt['credit_amt']+=$res['overPaymentForProc'];
						$arrTotAmt['bal_amt']+=$balanceAmt;
						$arrCheckChgdetId[$chgdetid]=$chgdetid;

						$arrFacSummary[$fac_id]['pat_due']+=$patDue;
						$arrFacSummary[$fac_id]['ins_due']+=$insDue;
						$arrFacSummary[$fac_id]['credit_amt']+=$res['overPaymentForProc'];
						$arrFacSummary[$fac_id]['bal_amt']+=$balanceAmt;					
					}
				}

				//APPOINTMENTS
				$qry = "select sa_facility_id, sa_doctor_id, count(id) as total_app FROM schedule_appointments
				WHERE (sa_app_start_date BETWEEN '$st_date' and '$end_date') 
				AND sa_patient_app_status_id NOT IN(203,201,18,19,20,3)";
				if(empty($Physician)==false){
					$qry.= " AND sa_doctor_id IN(".$Physician.")";
				}
				$qry.=" GROUP BY sa_doctor_id";
				$rs = imw_query($qry);
				while($res = imw_fetch_assoc($rs)){
					$fac_id=0;
					$firstGroupId=$res['sa_doctor_id'];
					$fac_id=$sch_fac_arr[$res['sa_facility_id']];
					$appointments=$res['total_app'];
					$provider_data_arr[$firstGroupId][$dt_year][$dt_month]["total_appointments"] = $res['total_app'];	
					$arrTotAmt['appt']+=$res['total_app'];
					
					$arrFacSummary[$fac_id]['appt']+=$res['total_app'];
				}
				unset($rs);

				//GRAND TOTALS ARRAYS
				$grandTotals['APPOINTMENTS'][] = $arrTotAmt['appt'];
				$grandTotals['CHARGES'][] = $arrTotAmt['tot_charges'];
				$grandTotals['PAT_PAID'][] = $arrTotAmt['pat_paid'];
				$grandTotals['INS_PAID'][] = $arrTotAmt['ins_paid'];
				$grandTotals['TOT_PAID'][] = $arrTotAmt['tot_paid'];
				$grandTotals['CREDIT'][] = $arrTotAmt['credit_amt'];
				$grandTotals['ADJUSTMET'][] = $arrTotAmt['adj_amt'];
				$grandTotals['PAT_DUE'][] = $arrTotAmt['pat_due'];
				$grandTotals['INS_DUE'][] = $arrTotAmt['ins_due'];
				$grandTotals['BALANCE'][] = $arrTotAmt['bal_amt'];				
			}
			
			//FOR GRAND TOTAL TO AVOID DEFAULT WRITE-OFF ADD SEVERAL MONTHS
			$grandTotals['ADJUSTMET'][] = array_sum($tempGrandAdj);					
			
			//DEFAULT WRITE-OFF FOR FACILITY SUMMARY TO AVOID ADD SEVERAL MONTHS
			foreach($tempGrandAdj as $chgDetId => $defaultWriteOff){
				$eid=$tempEncForChg[$chgDetId];
				$fac_id=$tempEncInfo[$eid]['facility'];
				$arrFacSummary[$fac_id]['adj_amt']+=$adjAmt;
			}
		}
	}
	unset($arrAllEncs);
	unset($arrTotAmt);
	unset($tempEncIds);
	unset($arrCheckChgdetId);
	unset($tempGrandAdj);
	unset($tempEncForChg);
	

	if(count($provider_data_arr) > 0){
		$printFile = true;
		$proIdTemp = array_keys($provider_data_arr);
		//REMOVE BLANK KEYS
		foreach($proIdTemp as $proId){
			if($proId>0){ $proIdArr[$proId]=$proId;	}
		}
		$pro_id_str= implode(',', $proIdArr);
		unset($providerIdArr);

		//--- SELECT ALL PROVIDER DETAILS ----
		$pro_query = "select id, lname, fname , mname from users where id in ($pro_id_str) ORDER BY lname, fname";
		$proQryRs = imw_query($pro_query);
		$pro_name_arr = array();
		while($proQryRes=imw_fetch_assoc($proQryRs)){
			$id = $proQryRes['id'];

			$name = core_name_format($proQryRes['lname'], $proQryRes['fname'], $proQryRes['mname']);			
			$pro_name_arr[$id] = $name;
			$providerIdArr[]=$id;
		}
		
		$monthArr = array('1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July',
		'8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December');
					
		//--- CHART MONTHS DATA ----
		$chartMonthArr = array('1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');

		$oldColspan=14;
		$colspan=14;
		$yearDiff = $End_year - $Start_year;
		$colspan+=$yearDiff; 
		$firstPartCol= 4;
		$secPartCol= ($oldColspan - $firstPartCol) + $yearDiff;
		
		$pdfPageW = 1010;
		$firstColW = 110;
		$monthColW = floor(($pdfPageW - $firstColW) / ($oldColspan-1));
		$monthColWCur = floor(($pdfPageW - $firstColW) / ($colspan-1));

		
		//MAKE MONTH NAME ROW
		$regMonthData = NULL;
		$regMonthDataCur=NULL;
		
		for($m=1;$m<=count($monthArr);$m++){					
			$monthName = $monthArr[$m];
			$regMonthData .='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$monthColW.'px; text-align:center">'.$monthName.'</td>';
			$regMonthDataCSV .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:center">'.$monthName.'</td>';
			$regMonthDataCurCSV .='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:center">'.$monthName.'</td>';
		}
		$regMonthData.='<td class="text_10b" bgcolor="#FFFFFF" style="width:'.$monthColW.'px; text-align:center">Total</td>';
		$regMonthDataCSV.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:center">Total</td>';
		$regMonthDataCurCSV.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:center">Total</td>';
		$regMonthDataCur = $regMonthData; // NO EXTRA COLUMN FOR PDF
	
		//FINAL HTML
		$charti = 0;
		$secGroupArr=array();
		for($p=0;$p<count($providerIdArr);$p++){
			$data = false;
			$arrProviderTot=array();
			$arrRowTots = array();
			$firstGroupId = $providerIdArr[$p];

			$firstGroup_name = ucwords(trim($pro_name_arr[$firstGroupId]));
			$firstGroupTitle = 'Physician';	
		
			$yearDetailArr = $provider_data_arr[$firstGroupId];
			
			$year_detail_arr = array_keys($yearDetailArr);
			$startDataYear = $year_detail_arr[0];
			$lastDataYear = $year_detail_arr[sizeof($year_detail_arr)-1];
			
			if(sizeof($year_detail_arr)>0){
				
				for($y=0;$y<count($year_detail_arr);$y++){
					$arrSubTotals=array();
					$dt_year = $year_detail_arr[$y];
					
					$className='oldYear';
					$cols = $oldColspan;
					$monthRowCSV = $regMonthDataCSV;
					$monthRowPDF = $regMonthData;
					
					if($dt_year==$lastDataYear){
						$className='curYear';
						$cols = $colspan;
						$curCSVPart='';
						for($i=$startDataYear; $i<$lastDataYear; $i++){
							//$regMonthDataCur .='<td class="text_10b text_10b_purpule link_cursor" bgcolor="#FFFFFF" style="width:'.$monthColWCur.'px">'.$sY.'</td>';
							$curCSVPart.='<td class="text_12b_purple link_cursor" bgcolor="#FFFFFF"  style="text-align:center" onclick="toggleTblNew(\''.$firstGroupId.'_'.$i.'\');">'.$i.'</td>';			
						}
						$monthRowCSV = $curCSVPart.$regMonthDataCurCSV;
						$monthRowPDF = $regMonthDataCur;
					} 
					
					$page_content .= '
					<tr class="'.$className.'" id="'.$firstGroupId.'_'.$dt_year.'">
					<td width="100%">
						<table style="width:100%" class="rpt_table rpt_table-bordered">
						<tr>
							<td class="text_b_w" colspan="'.$firstPartCol.'" style="text-align:left; height:15px;">&nbsp;'.$firstGroupTitle.' : '.$firstGroup_name.'</td>
							<td class="text_b_w" colspan="'.$secPartCol.'">&nbsp;Year : '.$dt_year.'</td>
						</tr>
						<tr bgcolor="#FFFFFF"><td class="text_10b"></td>'.$monthRowCSV.'</tr>';
	
					$pdf_content .= '
					<tr>
						<td class="text_b_w" colspan="'.$firstPartCol.'" style="text-align:left; height:15px;">&nbsp;'.$firstGroupTitle.' : '.$firstGroup_name.'</td>
						<td class="text_b_w" colspan="'.$secPartCol.'">&nbsp;Year : '.$dt_year.'</td>
					</tr>
					<tr><td class="text_10b" style="width:'.$firstColW.'px" bgcolor="#FFFFFF"></td>'.$monthRowPDF.'</tr>';
		
					//--- GET TOTAL CHARGES FOR MONTHS ---
					$monthData = NULL;
					$total_enc_data = NULL;
					$avg_data = NULL;
					$payment_data = NULL;
					$patPaidData = NULL;
					$insPaidData = NULL;
					$patDueData = NULL;
					$insDueData = NULL;
					$creditData = NULL;
					$adjustmentData = NULL;
					$balanceData = NULL;
					$payment_avg_data = NULL;
					
					$ths='no';
					for($m=1;$m<=count($monthArr);$m++){
						$ths='yes';	
						$month = $m < 10 ? '0'.$m : $m;
						$month_detail_arr = $provider_data_arr[$firstGroupId][$dt_year][$month];
						$monthTotalCharges = $month_detail_arr['total_charges'];
						$total_appointments = (int)$month_detail_arr['total_appointments'];
						$TotalPayment = $month_detail_arr['TotalPayment'];
						$totalPatPaid = $month_detail_arr['patPaid'];
						$totalInsPaid = $month_detail_arr['insPaid'];
						$totalPatDue = $month_detail_arr['patientDue'];
						$totalInsDue = $month_detail_arr['insuranceDue'];
						$totalCreditAmt = $month_detail_arr['credit_amount'];
						$totalAdjustmentAmt = $month_detail_arr['adjustment_amount'];
						$totalBalanceAmt = $month_detail_arr['balanceAmt'];
	
						$arrSubTotals[$m]+=$totalBalanceAmt;
						$arrProviderTot[$m]+=$totalBalanceAmt;
													
						$tot_avg = NULL;
						$pay_tot_avg = NULL;
						
						if($total_appointments > 0){
							$tot_avg = ($monthTotalCharges / $total_appointments);
							$pay_tot_avg = ($TotalPayment / $total_appointments);
						}else{
							$total_appointments = NULL;
						}
						
						// CHART DATA
						if($dispChart == 1){
							$chartAppointments[$dt_year][$m][1] = $chartMonthArr[$m];
							$chartAppointments[$dt_year][$m][2] += $total_appointments;
	
							$chartCharges[$dt_year][$m][1] = $chartMonthArr[$m];
							$chartCharges[$dt_year][$m][2] += $monthTotalCharges;
	
							$chartChargesAvg[$dt_year][$m][1] = $chartMonthArr[$m];
							$chartChargesAvg[$dt_year][$m][2] += $tot_avg;
	
							$chartPayments[$dt_year][$m][1] = $chartMonthArr[$m];
							$chartPayments[$dt_year][$m][2] += $TotalPayment;
	
							$chartPaymentsAvg[$dt_year][$m][1] = $chartMonthArr[$m];
							$chartPaymentsAvg[$dt_year][$m][2] += $pay_tot_avg;
							$charti++;							
						}
						//--------------								
						
						//ROW TOTALS
						$arrRowTots[$dt_year]['appointments']+= $total_appointments;
						$arrRowTots[$dt_year]['charges']+= $monthTotalCharges;
						$arrRowTots[$dt_year]['pat_paid']+= $totalPatPaid;
						$arrRowTots[$dt_year]['ins_paid']+= $totalInsPaid;
						$arrRowTots[$dt_year]['payments']+= $TotalPayment;
						$arrRowTots[$dt_year]['credit']+= $totalCreditAmt;
						$arrRowTots[$dt_year]['adjustment']+= $totalAdjustmentAmt;
						$arrRowTots[$dt_year]['pat_due']+= $totalPatDue;
						$arrRowTots[$dt_year]['ins_due']+= $totalInsDue;
						$arrRowTots[$dt_year]['balance']+= $totalBalanceAmt;
						
						//--- CHANGE NUMBER FORMAT ----
						$monthTotalCharges = $CLSReports->numberFormat($monthTotalCharges,2);
						$mth_tot_avg = $CLSReports->numberFormat($tot_avg,2);
						$TotalPayment = $CLSReports->numberFormat($TotalPayment,2);
						$pay_tot_avg = $CLSReports->numberFormat($pay_tot_avg,2);
						$totalPatPaid = $CLSReports->numberFormat($totalPatPaid,2);
						$totalInsPaid = $CLSReports->numberFormat($totalInsPaid,2);
						$totalPatDue = $CLSReports->numberFormat($totalPatDue,2);
						$totalInsDue = $CLSReports->numberFormat($totalInsDue,2);
						$totalCreditAmt = $CLSReports->numberFormat($totalCreditAmt,2);
						$totalAdjustmentAmt = $CLSReports->numberFormat($totalAdjustmentAmt,2);
						$totalBalanceAmt = $CLSReports->numberFormat($totalBalanceAmt,2);
						
						if($monthTotalCharges!='' && $totalPatDue==''){ $totalPatDue= '$0.00'; }
						if($monthTotalCharges!='' && $totalInsDue==''){ $totalInsDue= '$0.00'; }
						
						$total_enc_data.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$total_appointments.'</td>';
						$monthData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$monthTotalCharges.'</td>';
						$avg_data.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$mth_tot_avg.'</td>';
						$patPaidData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$totalPatPaid.'</td>';
						$insPaidData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$totalInsPaid.'</td>';
						$payment_data.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$TotalPayment.'</td>';
						$payment_avg_data.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$pay_tot_avg.'</td>';
						$creditData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$totalCreditAmt.'</td>';
						$adjustmentData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$totalAdjustmentAmt.'</td>';
						$patDueData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$totalPatDue.'</td>';
						$insDueData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$totalInsDue.'</td>';
						$balanceData.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$totalBalanceAmt.'</td>';
					}
				
	
					$old_appt = $old_monthData = $old_avg_data = $old_patPaidData = $old_insPaidData = $old_payment_data='';
					$old_payment_avg_data = $old_creditData = $old_adjustmentData = $old_patDueData = $old_insDueData = $old_balanceData='';
					
					if($Start_year < $End_year && $dt_year==$lastDataYear){ 
						$tempArr = array();
						//OLD YEARS TOTAL
						for($i=$startDataYear; $i<=$lastDataYear; $i++){
							if($i < $lastDataYear){ //only before current year
								$oldAvgCharges =  $arrRowTots[$i]['charges'] / $arrRowTots[$i]['appointments'];
								$oldAvgPayments =  $arrRowTots[$i]['payments'] / $arrRowTots[$i]['appointments'];
								
								$old_appt.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$arrRowTots[$i]['appointments'].'</td>';
								$old_monthData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$i]['charges'],2).'</td>';
								$old_avg_data.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($oldAvgCharges,2).'</td>';
								$old_patPaidData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$i]['pat_paid'],2).'</td>';
								$old_insPaidData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$i]['ins_paid'],2).'</td>';
								$old_payment_data.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$i]['payments'],2).'</td>';
								$old_payment_avg_data.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($oldAvgPayments,2).'</td>';
								$old_creditData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$i]['credit'],2).'</td>';
								$old_adjustmentData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$i]['adjustment'],2).'</td>';
								$old_patDueData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$i]['pat_due'],2).'</td>';
								$old_insDueData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$i]['ins_due'],2).'</td>';
								$old_balanceData.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$i]['balance'],2).'</td>';
							}
							
							$tempArr['appointments']+= $arrRowTots[$i]['appointments'];
							$tempArr['charges']+= $arrRowTots[$i]['charges'];
							$tempArr['pat_paid']+= $arrRowTots[$i]['pat_paid'];
							$tempArr['ins_paid']+= $arrRowTots[$i]['ins_paid'];
							$tempArr['payments']+= $arrRowTots[$i]['payments'];
							$tempArr['credit']+= $arrRowTots[$i]['credit'];
							$tempArr['adjustment']+= $arrRowTots[$i]['adjustment'];
							$tempArr['pat_due']+= $arrRowTots[$i]['pat_due'];
							$tempArr['ins_due']+= $arrRowTots[$i]['ins_due'];
							$tempArr['balance']+= $arrRowTots[$i]['balance'];
						}
						
						//ROW TOTALS
						$rowAvgCharges =  $tempArr['charges'] / $tempArr['appointments'];
						$rowAvgPayments =  $tempArr['payments'] / $tempArr['appointments'];
						
						$total_enc_data.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$tempArr['appointments'].'</td>';
						$monthData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($tempArr['charges'],2).'</td>';
						$avg_data.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($rowAvgCharges,2).'</td>';
						$patPaidData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($tempArr['pat_paid'],2).'</td>';
						$insPaidData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($tempArr['ins_paid'],2).'</td>';
						$payment_data.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($tempArr['payments'],2).'</td>';
						$payment_avg_data.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($rowAvgPayments,2).'</td>';
						$creditData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($tempArr['credit'],2).'</td>';
						$adjustmentData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($tempArr['adjustment'],2).'</td>';
						$patDueData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($tempArr['pat_due'],2).'</td>';
						$insDueData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($tempArr['ins_due'],2).'</td>';
						$balanceData.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($tempArr['balance'],2).'</td>';
						
					}else{
						//ROW TOTALS
						$rowAvgCharges =  $arrRowTots[$dt_year]['charges'] / $arrRowTots[$dt_year]['appointments'];
						$rowAvgPayments =  $arrRowTots[$dt_year]['payments'] / $arrRowTots[$dt_year]['appointments'];
						
						$total_enc_data.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$arrRowTots[$dt_year]['appointments'].'</td>';
						$monthData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$dt_year]['charges'],2).'</td>';
						$avg_data.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($rowAvgCharges,2).'</td>';
						$patPaidData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$dt_year]['pat_paid'],2).'</td>';
						$insPaidData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$dt_year]['ins_paid'],2).'</td>';
						$payment_data.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$dt_year]['payments'],2).'</td>';
						$payment_avg_data.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($rowAvgPayments,2).'</td>';
						$creditData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$dt_year]['credit'],2).'</td>';
						$adjustmentData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$dt_year]['adjustment'],2).'</td>';
						$patDueData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$dt_year]['pat_due'],2).'</td>';
						$insDueData.='<td class="text_10" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$dt_year]['ins_due'],2).'</td>';
						$balanceData.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrRowTots[$dt_year]['balance'],2).'</td>';
						
					}
					//---- ALL DATA ---
					$page_content.= <<<DATA
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Appt Kept</td>
						$old_appt $total_enc_data						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Charges</td>
						$old_monthData $monthData						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Avg. Charges</td>
						$old_avg_data $avg_data						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Pat. Paid</td>
						$old_patPaidData $patPaidData
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Ins. Paid</td>
						$old_insPaidData $insPaidData						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Payments</td>
						$old_payment_data $payment_data						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Avg. Payments</td>
						$old_payment_avg_data $payment_avg_data						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Credit</td>
						$old_creditData $creditData						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Adjustment</td>
						$old_adjustmentData $adjustmentData						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Pat. Due</td>
						$old_patDueData $patDueData						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Ins. Due</td>
						$old_insDueData $insDueData						
					</tr>
					<tr>
						<td class="text_10b" bgcolor="#FFFFFF">Balance</td>
						$old_balanceData $balanceData						
					</tr>
					</table></td></tr>
DATA;
	
					$pdf_content.= <<<DATA
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Appt Kept</td>
						$total_enc_data						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Charges</td>
						$monthData						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Avg. Charges</td>
						$avg_data						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Pat. Paid</td>
						$patPaidData
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Ins. Paid</td>
						$insPaidData						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Payments</td>
						$payment_data						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Avg. Payments</td>
						$payment_avg_data						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Credit</td>
						$creditData						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Adjustment</td>
						$adjustmentData						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Pat. Due</td>
						$patDueData						
					</tr>
					<tr>
						<td class="text_10" bgcolor="#FFFFFF">Total Ins. Due</td>
						$insDueData						
					</tr>
					<tr>
						<td class="text_10b" bgcolor="#FFFFFF">Balance</td>
						$balanceData						
					</tr>
DATA;

			}

			//TOTALS OF PHYSICIAN
			$subHTML=''; $totPhyBal=0;
			$oldYearTD= $monthRow = '';
								
			for($m=1;$m<=count($monthArr);$m++){
				$totPhyBal+= $arrProviderTot[$m]; 
				$totBal = $CLSReports->numberFormat($arrProviderTot[$m],2);		
				$subHTML.='<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$totBal.'</td>';
			}
			$page_content.=
			'<tr><td>
			<table style="width:100%" class="rpt_table rpt_table-bordered">
				<tr bgcolor="#FFFFFF"><td></td>'.$regMonthDataCSV.'</tr>
				 <tr bgcolor="#FFFFFF"><td class="text_10b">'.$firstGroupTitle.' Total :</td>'
				.$subHTML.
				'<td class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($totPhyBal,2).'</td>
				 </tr>
			</table></td></tr>';

			$pdf_content.=
			'<tr><td class="text_10b" bgcolor="#FFFFFF">'.$firstGroupTitle.' Total :</td>'
			.$subHTML.
			'<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totPhyBal,2).'</td>
			 </tr>';

			//-----------------					
			
			$page_content .='<tr bgcolor="#FFFFFF"><td class="text_10">&nbsp;</td></tr>';
			$pdf_content .='<tr><td class="text_10" bgcolor="#FFFFFF" colspan="'.$cols.'">&nbsp;</td></tr>';
			}
		}

		
		//--- GET PAGE HEADER DATA ---
		$curDate = date(phpDateFormat().' h:i A');
		$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$op_name = $op_name_arr[1][0];
		$op_name .= $op_name_arr[0][0];
		
		$dispDateFrom=date(phpDateFormat(), mktime(0,0,0, 1,1,$Start_year));
		$dispDateEnd=date(phpDateFormat(), mktime(0,0,0, 12,31,$End_year));
		
		
		//---- GET ALL DATA CSV FILE -- 
		$PAGE_DATA ='
		<table style="width:100%" class="rpt_table rpt_table-bordered">
			<tr class="rpt_headers">
				<td width="33%" align="center">Yearly Report</td>
				<td width="33%" align="center">'.$date_range_for.' : '.$dispDateFrom.' To '.$dispDateEnd.'</td>					
				<td width="34%" align="center">Created by '.$op_name.' on '.$curDate.'</td>
			</tr>
		</table>
		<table style="width:100%" class="rpt_table rpt_table-bordered">'
			.$page_content.'
		</table>';


		//----FACILITY SUMMARY & GRAND TOTALS -- 
		$GRAND_TOTAL.=
		'<table style="width:100%" class="rpt_table rpt_table-bordered">
			<tr id="heading_orange"><td  colspan="11" >Practice Summary</td></tr>
			<tr >
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:130px;">Practice</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:60px;">Appt</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:93px;">Charges</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:93px;">Pat. Paid</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:93px;">Ins. Paid</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:93px;">Total Paid</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:93px;">Credit</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:93px;">Adjustment</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:93px;">Pat. Due</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:93px;">Ins. Due</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:93px;">Balance</td>
			</tr>';
			$arrTot=array();
			foreach($arrFacSummary as $fac_id => $summDet){
				$arrTot['appt']+=$summDet['appt'];
				$arrTot['tot_charges']+=$summDet['tot_charges'];
				$arrTot['pat_paid']+=$summDet['pat_paid'];
				$arrTot['ins_paid']+=$summDet['ins_paid'];
				$arrTot['tot_paid']+=$summDet['tot_paid'];
				$arrTot['credit_amt']+=$summDet['credit_amt'];
				$arrTot['adj_amt']+=$summDet['adj_amt'];
				$arrTot['pat_due']+=$summDet['pat_due'];
				$arrTot['ins_due']+=$summDet['ins_due'];
				$arrTot['bal_amt']+=$summDet['bal_amt'];
				
				$GRAND_TOTAL.='
				<tr>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:left">'.$arrAllFacilities[$fac_id].'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$summDet['appt'].'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat($summDet['tot_charges'],2).'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat($summDet['pat_paid'],2).'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat($summDet['ins_paid'],2).'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat($summDet['tot_paid'],2).'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat($summDet['credit_amt'],2).'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat($summDet['adj_amt'],2).'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat($summDet['pat_due'],2).'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat($summDet['ins_due'],2).'</td>
					<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat($summDet['bal_amt'],2).'</td>
				</tr>';
			}
			$GRAND_TOTAL.='
			<tr><td colspan="11" class="total-row"></td></tr>
			<tr>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">Grand Total</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">'.$arrTot['appt'].'</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($arrTot['tot_charges'],2).'</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($arrTot['pat_paid'],2).'</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($arrTot['ins_paid'],2).'</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($arrTot['tot_paid'],2).'</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($arrTot['credit_amt'],2).'</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($arrTot['adj_amt'],2).'</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($arrTot['pat_due'],2).'</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($arrTot['ins_due'],2).'</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:right">'.$CLSReports->numberFormat($arrTot['bal_amt'],2).'</td>
			</tr>
			<tr><td colspan="11" class="total-row"></td></tr>
			</table>';
			unset($arrTot);
			
/*			$GRAND_TOTAL.='
			<table width="100%" cellpadding="1" cellspacing="1" bgcolor="#FFF3E8" style="border:1px solid #999999; margin-top:8px;margin-bottom:8px;">
			<tr><td  colspan="10" class="text_b_w">Grand Totals</td></tr>
			<tr >
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:103px;">Appointments</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:103px;">Charges</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:103px;">Pat. Paid</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:103px;">Ins. Paid</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:103px;">Total Paid</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:103px;">Credit</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:103px;">Adjustment</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:103px;">Pat. Due</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:103px;">Ins. Due</td>
				<td bgcolor="#FFFFFF" class="text_10b" style="text-align:center;width:103px;">Balance</td>
			</tr>
			<tr>
				<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.array_sum($grandTotals['APPOINTMENTS']).'</td>
				<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat(array_sum($grandTotals['CHARGES']),2).'</td>
				<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat(array_sum($grandTotals['PAT_PAID']),2).'</td>
				<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat(array_sum($grandTotals['INS_PAID']),2).'</td>
				<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat(array_sum($grandTotals['TOT_PAID']),2).'</td>
				<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat(array_sum($grandTotals['CREDIT']),2).'</td>
				<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat(array_sum($grandTotals['ADJUSTMET']),2).'</td>
				<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat(array_sum($grandTotals['PAT_DUE']),2).'</td>
				<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat(array_sum($grandTotals['INS_DUE']),2).'</td>
				<td bgcolor="#FFFFFF" class="text_10" style="text-align:right">'.$CLSReports->numberFormat(array_sum($grandTotals['BALANCE']),2).'</td>
			</tr>
		</table>';*/
		$PAGE_DATA.=$GRAND_TOTAL;

		
		//---- GET ALL DATA HTML FILE -- 
		$HTML_PAGE_DATA ='
		<page backtop="5mm" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
			<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
				<tr>
					<td width="320" class="text_b_w" align="center">Yearly Report</td>
					<td width="400" class="text_b_w" align="center">'.$date_range_for.' : '.$dispDateFrom.' To '.$dispDateEnd.'</td>					
					<td width="334" class="text_b_w" align="center">Created by '.$op_name.' on '.$curDate.'</td>
				</tr>
			</table>
			</page_header>
			<table style="width:100%" class="rpt_table rpt_table-bordered">
				'.$pdf_content.'
			</table>
			'.$GRAND_TOTAL.'
		</page>';
		
		$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';
		$PAGE_DATA= $styleHTML.$PAGE_DATA;
	
		$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
		$HTML_PAGE_DATA = $stylePDF.$HTML_PAGE_DATA;


		//--- CREATE HTML FILE FOR PDF PRINTING ---
		if($callFrom != 'scheduled'){
			//$html_file_name = get_pdf_name($_SESSION['authId'],'yearly');
			//file_put_contents('new_html2pdf/'.$html_file_name.'.html',$HTML_PAGE_DATA);
			$file_location = write_html($HTML_PAGE_DATA);
		}
	}else{
		$PAGE_DATA = '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}

if($callFrom == 'scheduled'){
	if($HTML_PAGE_DATA != ""){
		$op='l';
		$page_html_script = $PAGE_DATA;
		$html_file_name = get_scheduled_pdf_name('yearly', '../common/new_html2pdf');
		file_put_contents('../common/new_html2pdf/'.$html_file_name.'.html',$HTML_PAGE_DATA);
	}
	
}else{		
	if($callFrom != 'scheduled'){
		echo $PAGE_DATA;
	}
}

?>