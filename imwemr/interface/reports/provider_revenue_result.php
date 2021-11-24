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
FILE : provider_revenue_result.php
PURPOSE :  PROVIDER PRODUCTIVITY REPORT RESULT
ACCESS TYPE : DIRECT
*/
$printFile = true;
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

if($_REQUEST["print"] == "yes"){
	die();
}

$htmlData='';

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

	$doe_pat_ids_arr[]=0;
	$doe_pat_Qry = "select id from patient_data where lname = 'doe'";
	$doe_pat_Res = imw_query($doe_pat_Qry);
		while($doe_pat_Row = imw_fetch_assoc($doe_pat_Res)){
			$doe_pat_ids_arr[]=$doe_pat_Row['id'];
		}
	$doe_pat_ids_str = implode(',', $doe_pat_ids_arr);
	unset($doe_pat_ids_arr);

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_id)<=0 && isPosFacGroupEnabled()){
		//FIRST GET PRIVILEGED POS FACILITIES
		$priv_pos_facility = $CLSReports->getFacilityName('', '0', 'array');
		$facility_id= $CLSReports->getSchFacilityInfo('', $priv_pos_facility);

		if(sizeof($facility_id)<=0){
			$facility_id[0]='NULL';
		}
	}	
	
	$printFile = false;
	$grp_id=array_combine($grp_id,$grp_id);
	$group_id = implode(',', $grp_id);
	$physician_id = implode(',', $filing_provider);
	$str_crediting_provider = implode(',', $crediting_provider);
	$facility_id = implode(',', $facility_id);
	$procedure_id = implode(',', $procedure_id);
	$cpt_cat_2 = implode(',', $cpt_cat_2);
	
	if($processReport == 'Detail'){
		$detailRes='true';
	}

	$showYear = substr($Start_date, -2);
	$start_date = getDateFormatDB($Start_date);

	//--- GET MONTH END DATE ----
	if($phpDateFormat=='m-d-Y'){
		list($m, $d, $y) = preg_split('/-/', $End_date);
	}else{
		list($d, $m, $y) = preg_split('/-/', $End_date);
	}
	$end_mth_date = cal_days_in_month(CAL_GREGORIAN, $m, $y);
	$end_date = $y.'-'.$m.'-'.$end_mth_date;
	
	//GET SELECTED GROUPS
	$allSelGroups=array();
	$rs=imw_query("Select gro_id FROM groups_new");
	while($res=imw_fetch_array($rs)){
		if(empty($group_id)==false){
			if($grp_id[$res['gro_id']]){
				$allSelGroups[$res['gro_id']] = $res['gro_id'];
			}
		}else{
			$allSelGroups[$res['gro_id']] = $res['gro_id'];
		}
	}


	//GET SELECTED POS FACILITIES
	$pos_fac_id_arr=array();
	if(empty($facility_id) === false){
		$fac_qry = "select fac_prac_code,name,id from facility where id  in($facility_id)";
		$fac_qry_res = imw_query($fac_qry);
		while($fac_row = imw_fetch_array($fac_qry_res)){
			if(empty($fac_row['fac_prac_code']) === false){
				$pos_fac_id_arr[$fac_row['fac_prac_code']] = $fac_row['fac_prac_code'];
			}
		}
		$pos_fac_id_str = implode(',',array_unique($pos_fac_id_arr));
		$fac_whr = " and facility_id in($pos_fac_id_str)";
	}else{
		//ALL POS FACILITIES
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
			$pos_fac_id_arr[$id] = $id;
		}						
	}
	// ------------------------------
	
	//--- GET ALL PROVIDER NAME --
	$proQryRs = imw_query("select id, lname, fname, mname, user_type, Enable_Scheduler from users");
	$prividerNameArr = array();
	$arrPhy=array();
	while($proQryRes = imw_fetch_assoc($proQryRs)){
		$pid = $proQryRes['id'];
		$prividerNameArr[$pid] = core_name_format($proQryRes['lname'], $proQryRes['fname'], $proQryRes['mname']);
		
		if($proQryRes['user_type']=='1' || $proQryRes['Enable_Scheduler']=='1'){
			$arrPhy[$pid]=$pid;
		}
	}
	
	if(sizeof($filing_provider)<=0){
		$physician_id=implode(',', $arrPhy);
	}
	
	//--- GET SCHEDULE APPOINTMENT DETAILS -----
	$sch_qry = "select schedule_appointments.id as sch_id, slot_procedures.proc as procedure_name,
		date_format(schedule_appointments.sa_app_start_date, '".$date_format_SQL."') as sa_app_start_date , 
		time_format(schedule_appointments.sa_app_starttime,'%h:%i %p') as sa_app_starttime, 
		time_format(schedule_appointments.sa_app_endtime,'%h:%i %p') as sa_app_endtime,
		schedule_appointments.sa_facility_id, schedule_appointments.sa_patient_id, facility.name,
		patient_data.lname, patient_data.fname, patient_data.mname,
		schedule_appointments.sa_doctor_id
		from schedule_appointments join facility on facility.id = schedule_appointments.sa_facility_id
		join slot_procedures on slot_procedures.id = schedule_appointments.procedureid
		join patient_data on patient_data.id = schedule_appointments.sa_patient_id
		where schedule_appointments.sa_doctor_id in (".$physician_id.")
		and (schedule_appointments.sa_app_start_date between '".$start_date."' and '".$end_date."')
		and schedule_appointments.sa_patient_app_status_id NOT IN(203,201,18,19,20,3)";
	if(empty($procedure_id) === false){
		$sch_qry .= " and schedule_appointments.procedureid in (".$procedure_id.")";
	}
	if(empty($facility_id) === false){
		$sch_qry .= " and schedule_appointments.sa_facility_id in (".$facility_id.")";
	}
	$sch_qry .= "order by schedule_appointments.sa_app_start_date, patient_data.lname, patient_data.fname";

	$sch_qry_res = imw_query($sch_qry);
	
	$sch_appt_data_arr = array();
	$facilityNameArr = array();
	$patientIdArr = array();
	$facilityDetailArr = array();
	$providerIdArr = array();
	$previous_facility = "";
	$cnt = 1;
	while($sch_qry_row = imw_fetch_assoc($sch_qry_res)){
		$sa_patient_id = $sch_qry_row['sa_patient_id'];
		$patientIdArr[$sa_patient_id] = $sa_patient_id;
		$sa_doctor_id = $sch_qry_row['sa_doctor_id'];
		$providerIdArr[$sa_doctor_id] = $prividerNameArr[$sa_doctor_id];
		$sch_id = $sch_qry_row['sch_id'];
		$sa_facility_id = $sch_qry_row['sa_facility_id'];
		$facilityNameArr[$sa_facility_id] = $sch_qry_row['name'];
		if($phpDateFormat=='m-d-Y'){
			list($month, $day, $year) = preg_split('/-/',$sch_qry_row['sa_app_start_date']);
		}else{
			list($day, $month, $year) = preg_split('/-/',$sch_qry_row['sa_app_start_date']);
		}
		$month = (int)$month;
		$sch_appt_data_arr['TOTAL_APPOITMENT'][$month][$sa_doctor_id][] = $sch_qry_row['sa_app_start_date'];
		$sch_appt_data_arr['FACILITY'][$sa_doctor_id][$sa_facility_id][$month][] = $sch_qry_row;
		$patient_name = core_name_format($sch_qry_row['lname'], $sch_qry_row['fname'], $proQryRes['mname']);
		
		$sch_qry_row['patient_name'] = $patient_name. ' - '. $sch_qry_row['sa_patient_id'];
		$facilityDetailArr[$sa_doctor_id][$sa_facility_id][] = $sch_qry_row;	
	}
	asort($providerIdArr);
	$provider_id_arr = array_keys($providerIdArr);
	
	if(imw_num_rows($sch_qry_res) > 0){
		$printFile = true;
		$printData= true;
		
		//--- MONTH HEADER DATA ---
		list($stY, $stM, $stD) = preg_split("/-/",$start_date);		
		list($enY, $enM, $enD) = preg_split("/-/",$end_date);

		//--- GET TOTAL MONTH COUNT ---
		$endDate = $enY.$enM;
		$startDate = $stY.$stM;
		$monthRs = imw_query("select period_diff($endDate, $startDate) as months");
		$monthQryRes = imw_fetch_assoc($monthRs);
		$totalMonthCount = $monthQryRes['months']+1;
		
		$yearArr = array();
		$monthNameArr = array();
		for($d=0;$d<$totalMonthCount;$d++){
			$selectMonth = date('m', mktime(0,0,0,$stM + $d, $stD, $stY));			
			$selectMonth = (int)$selectMonth;
			$monthNameArr[$selectMonth] = date('M-y', mktime(0,0,0,$stM + $d, $stD, $stY));
			$yearArr[$selectMonth] = date('Y', mktime(0,0,0,$stM + $d, $stD, $stY));
			
			$totalDays = cal_days_in_month(CAL_GREGORIAN, $selectMonth, $stY);
			$monthDayCntArr[$selectMonth] = $totalDays;
		}
		$monthNameArr['total'] = 'Total';
		$monthArr = array_keys($yearArr);
		$monthArr[] = 'total';
		$total_month = count($monthArr);

		//Total PDF Page width = 1065 px

		switch($total_month){
			case 1:
				$width = "370";
				break;
			case 2:
				$width = "355";
				break;
			case 3:
				$width = "265";
				break;
			case 4:
				$width = "210";
				break;			
			case 5:
				$width = "175";
				break;
			case 6:
				$width = "150";
				break;
			case 7:
				$width = "130";
				break;			
			case 8:
				$width = "115";
				break;			
			case 9:
				$width = "104";
				break;			
			case 10:
				$width = "94";
				break;	
			case 11:
				$width = "65";
				break;					
			case 12:
				$width = "80";
				break;					
			default:
				$width = "70";
		}

		//--- IF DETAILS ---
		$colspan=count($monthArr)+1;

		
		//--- GET TOTAL APPOINTMENT COUNTS ----
		$totalMonthCntArr = array();
		for($p=0;$p<count($provider_id_arr);$p++){
			$totalApptCntArr = array();
			$pro_id = $provider_id_arr[$p];
			for($m=0;$m<count($monthArr);$m++){
				$month = $monthArr[$m];
				$appt_count = count($sch_appt_data_arr['TOTAL_APPOITMENT'][$month][$pro_id]);
				$totalApptCntArr[] = $appt_count;
				if($appt_count > 0){
					$totalMonthCntArr[$month][$pro_id] = $appt_count;
					$GrandtotalMonthCntArr[$month][] = $appt_count;
				}
			}
			$totalMonthCntArr['total'][$pro_id] = array_sum($totalApptCntArr);
			$GrandtotalMonthCntArr['total'][] = array_sum($totalApptCntArr);
		}
		
		unset($totalApptCntArr);

		
		//--- GET APPOINTMENT TOTAL FACILITY WISE ----
		$facDataArr = $sch_appt_data_arr['FACILITY'];
		$providerFacIdArr = array_keys($facDataArr);
		$facilityCntArr = array();
		for($pr=0;$pr<count($providerFacIdArr);$pr++){
			$pro_fac_id = $providerFacIdArr[$pr];
			$facilityIdArr = array_keys($facDataArr[$pro_fac_id]);
			for($f=0;$f<count($facilityIdArr);$f++){
				$facilityId = $facilityIdArr[$f];
				$facilityTotalCnt = array();
				for($m=0;$m<count($monthArr);$m++){
					$month = $monthArr[$m];
					$fac_appt_count = count($facDataArr[$pro_fac_id][$facilityId][$month]);
					$facilityTotalCnt[] = $fac_appt_count;
					if($fac_appt_count > 0){
						$facilityCntArr[$pro_fac_id][$facilityId][$month] = $fac_appt_count;
					}
				}
				$facilityCntArr[$pro_fac_id][$facilityId]['total'] = array_sum($facilityTotalCnt);
			}
		}
		
		if(empty($selectedProc) === false){
				
			$cpt_group_id_str = implode(',',$selectedProc);
			
			//--- GET CPT GROUP DATA ---
			$cptGroupQry = "select cpt_group_name, cpt_code_name, cpt_group_id from cpt_group_tbl
						where cpt_group_status = '0' order by cpt_group_name";
			$cptGroupQryRes = imw_query($cptGroupQry);
			$selectedProcArr = array();
			$select_proc_id_arr = array();
			$group_cpt_arr = array();
			while($cptGroupRow = imw_fetch_assoc($cptGroupQryRes)){
				$cpt_group_id = $cptGroupRow['cpt_group_id'];
				$cpt_group_name = ucwords($cptGroupRow['cpt_group_name']);
				$cptCodeNameArr = preg_split('/, /',$cptGroupRow['cpt_code_name']);
				
				if(in_array($cpt_group_id,$selectedProc) === true or empty($selectedProc) === true){
					$group_cpt_arr = array_merge($group_cpt_arr, $cptCodeNameArr);
					$selectedProcArr[$cpt_group_name] = $cptCodeNameArr;
					$select_proc_id_arr[] = $cptGroupRow['cpt_code_name'];
				}
			}
		}
			
		//---- GET ALL CPT CODE ----
		$procIdArr = array();
		$procQryRs = imw_query("select cpt_fee_id, cpt4_code from cpt_fee_tbl where delete_status = '0'");		
		$procIdArr = array();
		$procCptCodeArr = array();
		
		while($procQryRes = imw_fetch_assoc($procQryRs)){
			$cpt_fee_id = $procQryRes['cpt_fee_id'];
			$procCptCodeArr[$cpt_fee_id] = $procQryRes['cpt4_code'];
			if(count($group_cpt_arr) > 0){
				if(in_array($procQryRes['cpt4_code'], $group_cpt_arr) === true){
					$procIdArr[$cpt_fee_id] = $cpt_fee_id;
				}
			}
			else{
				$procIdArr[$cpt_fee_id] = $cpt_fee_id;
			}
		}
		
		$procIdArrQry = $procIdArr;
		$procIdStr = implode(',', $procIdArr);
		$patientIdStr = implode(',', $patientIdArr);
		
		unset($patientIdArr);
		unset($procIdArr);
		
		$monthArrQry= $monthArr;
		unset($monthArrQry[count($monthArrQry)-1]);

		$summaryTotalChrgArr = array();
		$summaryTotalPaidArr = array();

		$dParts = explode('-',$start_date);
		$start_date_chg = date('Y-m-d', mktime(0,0,0,$dParts[1], 1,$dParts[0]));
		//--- GET TOTAL CHARGES -----
		$chrgesQry = "Select main.encounter_id, main.date_of_service, 
		(main.charges*main.units) as 'totalAmount', main.proc_balance as 'totalBalance', main.gro_id,
	 	main.charge_list_id, main.proc_code_id as procCodeStr,
		main.primary_provider_id_for_reports as 'primaryProviderId',main.date_of_service,
		main.charge_list_detail_id, main.proc_code_id, main.facility_id 
		FROM report_enc_detail main 
		JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = main.proc_code_id
		WHERE main.del_status = '0' AND main.primary_provider_id_for_reports in ($physician_id) $fac_whr  
		AND (main.date_of_service BETWEEN '$start_date_chg' AND '$end_date')";
		if(empty($str_crediting_provider)==false){
			$chrgesQry.=" and main.sec_prov_id IN ($str_crediting_provider)";
		}
		if($chksamebillingcredittingproviders==1){
			$chrgesQry.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
		}
		if(trim($cpt_cat_2) != ''){
			$chrgesQry.= " AND cpt_fee_tbl.cpt_category2 IN ($cpt_cat_2)";							
		}
		$chrgesRes = imw_query($chrgesQry);
		$selProcDetailArr = array();
		$totalChargesArr = array();
		$mainChargeListIdArr = array();
		$mainEncounterIdArr = array();
		$pro_enc_arr = array();
		$total_proc_cnt_arr = array();
		$arrAllChargeIds=array();
		$pro_enc_id_arr = array();
		$pro_charge_id_arr=array();
		$pro_encounters_arr=array();

		while($chrgesRow = imw_fetch_assoc($chrgesRes)){
			$totAmt=0;
			$procId = $chrgesRow['proc_code_id'];
			$groupId = $chrgesRow['gro_id'];
			$facId = $chrgesRow['facility_id'];
			$primaryProviderId = $chrgesRow['primaryProviderId'];
			list($year, $month, $day) = preg_split('/-/', $chrgesRow['date_of_service']);
			$month = (int)$month;
			$totAmt = preg_replace('/,/','',number_format($chrgesRow['totalAmount'], 2));
			$arrAllChargeIds[$chrgesRow['charge_list_detail_id']] = $chrgesRow['charge_list_detail_id'];
			
			if($procIdArrQry[$procId] && $allSelGroups[$groupId]){
				$mainChldIdArr[$chrgesRow['charge_list_detail_id']] = $chrgesRow['charge_list_detail_id'];
				$mainEncounterIdArr[$chrgesRow['encounter_id']] = $chrgesRow['encounter_id'];
				$pro_enc_arr[$chrgesRow['encounter_id']] = $primaryProviderId;
				
				if($chrgesRow['date_of_service']>=$start_date && $chrgesRow['date_of_service']<=$end_date){
					$procCodeArr = preg_split('/,/', $chrgesRow['procCodeStr']);
					$providerIdArr[$primaryProviderId] = $prividerNameArr[$primaryProviderId];	
					$mainChargeListIdArr[$month][] = $chrgesRow['charge_list_id'];
					$totalChargesArr[$primaryProviderId][$month][] = $totAmt;
					$GrandtotalChargesArr[$month][] = $totAmt;
					$totalEncounterArr[$primaryProviderId][$month][$chrgesRow['encounter_id']] = $chrgesRow['encounter_id'];
					$GrandtotalEncounterArr[$month][$chrgesRow['encounter_id']] = $chrgesRow['encounter_id'];
					foreach($procCodeArr as $procId){
						$procVal = $procCptCodeArr[$procId];
		
						foreach($selectedProcArr as $groupKey => $cptValArr){
							if(in_array($procVal, $cptValArr) === true){
								$total_proc_cnt_arr[$primaryProviderId][$month][] = $chrgesRow;
								$selProcDetailArr[$primaryProviderId][$groupKey][$month][] = $chrgesRow;
								$total_proc_cnt_arr_tot[$primaryProviderId][] = $chrgesRow;
							}
						}
					}
				}
			}
			
			$selectedYear = $yearArr[$month];
			$totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $selectedYear);
			$getStartDate = date('Y-m-d', mktime(0,0,0,$month,1,$selectedYear));
			$getEndDate = date('Y-m-d', mktime(0,0,0,$month,$totalDays,$selectedYear));
			// GET SUMMARY CHARGES
			if($chrgesRow['date_of_service']>=$getStartDate && $chrgesRow['date_of_service']<=$getEndDate){
				$summaryTotalChrgArr[$primaryProviderId][$month][] = $totAmt;
				$GrandsummaryTotalChrgArr[$month][] = $totAmt;
			}
			
			// ARRAY FOR SUMMARY PAYMENTS
			$pro_enc_id_arr[$chrgesRow['encounter_id']] = $chrgesRow['primaryProviderId'];
			$pro_encounters_arr[$chrgesRow['encounter_id']]=$chrgesRow['encounter_id'];
			$pro_charge_id_arr[$chrgesRow['charge_list_detail_id']] = $chrgesRow['charge_list_detail_id'];
		}

		asort($providerIdArr);
		$provider_id_arr = array_keys($providerIdArr);

		$mainEncounterIdStr = implode(',',$mainEncounterIdArr);
		$mainChldIdStr = implode(',',$mainChldIdArr);
		$mainCopayEncIdStr = implode(',',$mainCopayEncIdArr);
		$strAllChargeIds = implode(',', $arrAllChargeIds);
		
		unset($mainEncounterIdArr);
		unset($mainChldIdArr);

		$totalPaidAmtArr = array();
		$total_amt_avg_arr = array();

		//--- GET TOTAL CHARGES AND TOTAL PAID AMOUNT ----
		if(empty($mainEncounterIdStr) === false){
			$total_charges_arr = array();
			$totalAmtArr = array();
			$total_paid_arr = array();
			$total_paid_amt_arr = array();
			$totalChrgesAvgArr = array();
			$totalPaidAvgArr = array();
			$total_bal_arr = array();
			$total_bal_amt_arr = array();
			//$monthDayCntArr = array();

			list($stY, $stM, $stD) = preg_split("/-/",$start_date);		
			list($enY, $enM, $enD) = preg_split("/-/",$end_date);
			$startMY=$stY.'-'.$stM;
			$endMY=$enY.'-'.$enM;

			//--- GET PROVIDER PAYMENT DETAILS -----
			$payQry = "Select trans.trans_amount, trans.trans_type,
			trans.encounter_id, trans.trans_dop, trans.trans_dot, trans.trans_del_operator_id  
			FROM report_enc_trans trans  
			WHERE LOWER(trans.trans_type) IN('paid','copay-paid','negative payment','credit','debit','copay-negative payment','interest payment','deposit')";
			if($DateRangeFor=='dop'){ 
				$payQry.=" AND (DATE_FORMAT(trans.trans_dop, '%Y-%m') BETWEEN '$startMY' and '$endMY')";
			}else{
				$payQry.=" AND (DATE_FORMAT(trans.trans_dot, '%Y-%m') BETWEEN '$startMY' and '$endMY')";
			}
			$payQry.=" ORDER BY trans.trans_dot, trans.trans_dot_time";
			$payQryRes = imw_query($payQry);
			$pay_crd_deb_arr =array();
			while($payQryRow = imw_fetch_assoc($payQryRes)){
				$enc_id=$payQryRow['encounter_id'];
				$trans_type= strtolower($payQryRow['trans_type']);
				$tempEncId[$enc_id]=$enc_id;
				
				switch($trans_type){
					case 'paid':
					case 'copay-paid':
					case 'deposit':
					case 'interest payment':
					case 'negative payment':
					case 'copay-negative payment':
						$paidForProc=$payQryRow['trans_amount'];
						if($trans_type=='negative payment' || $trans_type=='copay-negative payment' || $payQryRow['trans_del_operator_id']>0)$paidForProc="-".$payQryRow['trans_amount'];
						if(($trans_type=='negative payment' || $trans_type=='copay-negative payment') && $payQryRow['trans_del_operator_id']>0)$paidForProc=$payQryRow['trans_amount'];

						list($y,$month,$d)=($DateRangeFor=='dop') ? explode('-', $payQryRow['trans_dop']) : explode('-', $payQryRow['trans_dot']);
						$month=(int)$month;
						$tempPayments[$enc_id][$month]+=$paidForProc;
					break;
				
					case 'credit':
					case 'debit':
						$crddbtamt=$payQryRow['trans_amount'];
						if($trans_type=='credit'){ 
							$crddbtamt= ($payQryRow['trans_del_operator_id']>0) ? "-".$payQryRow['trans_amount'] : $payQryRow['trans_amount'];
						}else{  //debit
							$crddbtamt= ($payQryRow['trans_del_operator_id']>0) ? $payQryRow['trans_amount'] : "-".$payQryRow['trans_amount'];				
						}
						
						$pay_crd_deb_arr[$enc_id][] = $crddbtamt;
					break;				
				}
			}
	
			if(sizeof($tempEncId)>0){
				$chkEncIdArr=array();
				$str_tempEncId=implode(',', $tempEncId);
				$payQry = "Select main.encounter_id, main.primary_provider_id_for_reports as 'primaryProviderId' 
				FROM report_enc_detail main
				JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = main.proc_code_id		
				WHERE main.encounter_id  IN(".$str_tempEncId.") 
				AND main.primary_provider_id_for_reports in ($physician_id) 
				$fac_whr";
				if(empty($str_crediting_provider)==false){
					$payQry.=" and main.sec_prov_id IN ($str_crediting_provider)";
				}
				if($chksamebillingcredittingproviders==1){
					$payQry.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";
				}
				if(trim($cpt_cat_2) != ''){
					$payQry.= " AND cpt_fee_tbl.cpt_category2 IN ($cpt_cat_2)";							
				}	
				$payQry.=" GROUP BY main.encounter_id";				
				$payQryRes = imw_query($payQry);				
				while($payQryRow = imw_fetch_assoc($payQryRes)){
					$enc_id= $payQryRow['encounter_id'];
					$proId= $payQryRow['primaryProviderId'];

					
					
					foreach($tempPayments[$enc_id] as $month => $paidForProc){
						$totalPaidAmtArr[$proId][$month][] = $paidForProc;
						$GrandtotalPaidAmtArr[$month][] = $paidForProc;
						//SUMMARY PAYMENTS
						if(!$chkEncIdArr[$enc_id]){
						//	$paidForProc+= array_sum($pay_crd_deb_arr[$enc_id]);
							$chkEncIdArr[$enc_id]=$enc_id;
						}
						$summaryTotalPaidArr[$proId][$month][] = $paidForProc;
						$GrandsummaryTotalPaidArr[$month][] = $paidForProc;
					}
				}				
			}
			unset($tempEncId);
			unset($tempPayments);
		}
		//----------------------------------------
		//--- GET AGGING CYCLE -----
		$policiesQry = imw_query("Select elem_arCycle from copay_policies where policies_id = '1'");
		$polociesDetails = imw_fetch_assoc($policiesQry);
		$aggingCycle = $polociesDetails['elem_arCycle'];
		
		$aggingDrop = array();			
		for($i=0;$i<180;$i++){
			$j = $i == 0 ? '00' : $i + 1;  	
			$aggingDrop[$j] = $j .'-'. ($aggingCycle+$i);
			$i += ($aggingCycle - 1);
		}
		$aggingDrop[181] = '181+';
		
		$agingCols = sizeof($aggingDrop) + 1;
		$agingWidth = (round(1065 / $agingCols, 0)) - 3;
		
		$arAgingDataArr = array();
		$totalAmtAgingArr = array();
		$arAgingDataArr_wofac = array();
		
		$aging_start=0;
		$aging_to=180;
		$qry="Select main.primary_provider_id_for_reports as 'primaryProviderId',
		(main.pri_due + main.sec_due + main.tri_due + main.pat_due) as 'totalBalance', main.total_charges, 
		DATEDIFF(NOW(),main.date_of_service) as last_pri_dop_diff,
		DATEDIFF(NOW(),main.from_sec_due_date) as last_sec_dop_diff,
		DATEDIFF(NOW(),main.from_ter_due_date) as last_ter_dop_diff,
		DATEDIFF(NOW(),main.from_pat_due_date) as last_pat_dop_diff,
		DATEDIFF(NOW(),main.date_of_service) as last_dos_diff
		FROM report_enc_detail main
		JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = main.proc_code_id	
		WHERE main.del_status='0' AND main.primary_provider_id_for_reports in($physician_id)";
		if(empty($str_crediting_provider)==false){
			$qry.=" and main.sec_prov_id IN ($str_crediting_provider)";
		}
		if($chksamebillingcredittingproviders==1){
			$qry.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
		}
		if(trim($cpt_cat_2) != ''){
			$qry.= " AND cpt_fee_tbl.cpt_category2 IN ($cpt_cat_2)";							
		}	
		$rs= imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$arrTempAging = array();
			$provider_id = $res['primaryProviderId'];

			if($res['last_pri_dop_diff']!='') { $arrTempAging[] = $res['last_pri_dop_diff']; }
			if($res['last_sec_dop_diff']!='') { $arrTempAging[] = $res['last_sec_dop_diff']; }
			if($res['last_ter_dop_diff']!='') { $arrTempAging[] = $res['last_ter_dop_diff']; }
			if($res['last_pat_dop_diff']!='') { $arrTempAging[] = $res['last_pat_dop_diff']; }

			sort($arrTempAging);
			$smallestAging = $arrTempAging[0];
			
			for($a=$aging_start;$a<=$aging_to;$a++){
				$start = $a;
				$a = $a > 0 ? $a - 1 : $a;
				$end = ($a) + $aggingCycle;
				$b=$a;
				if($b==0){ $b='00'; }else{ $b++;}

				if($smallestAging >= $start and  $smallestAging <= $end){
					$arBalTotalMainArr[$provider_id][$b]+=$res['totalBalance'];
					$arrAgingTotalMainArr[$provider_id][$b]['balance']+=$res['totalBalance'];
					$arrAgingTotalMainArr[$provider_id][$b]['charges']+=$res['total_charges'];
				}
				$a += $aggingCycle;
			}
			
			if($smallestAging>=181){
				$arBalTotalMainArr[$provider_id][181]+=$res['totalBalance'];
				$arrAgingTotalMainArr[$provider_id][181]['balance']+=$res['totalBalance'];
				$arrAgingTotalMainArr[$provider_id][181]['charges']+=$res['total_charges'];
			}
		}

		$arBalTotalMain=0;
		foreach($arrAgingTotalMainArr as $provider_id => $agingData){
			
			$arBalTotalMain= array_sum($arBalTotalMainArr[$provider_id]);

			foreach($agingData as $agingStart => $agingDet){
				$totalAmtAging = $agingDet['charges'];
				$totalBalanceAging = $agingDet['balance'];
				
				
				$enc_avg = number_format(($totalBalanceAging * 100) / $arBalTotalMain, 2);
				
				$arBalTotalArr[] =$totalBalanceAging;
				$totalAmtAgingArr[] =$totalAmtAging;
				
				if($totalBalanceAging > 0){
					$arAgingDataArr[$provider_id][$agingStart] = $CLSReports->numberFormat($totalBalanceAging,2).' ('.$enc_avg.'%)';
					$arAgingDataArr_wofac[$agingStart][] = $totalBalanceAging;
				}
			}

			$arBalTotalStr = array_sum($arBalTotalArr);
			$totalAmtAging = array_sum($totalAmtAgingArr);
			unset($arBalTotalArr);
			unset($totalAmtAgingArr);
			$enc_avg = number_format(($arBalTotalStr * 100) / $arBalTotalMain, 2);

			
			$arAgingDataArr[$provider_id]['total_ar_bal'] = $CLSReports->numberFormat($arBalTotalStr, 2).' ('.$enc_avg.'%)';
			$arAgingDataArr_wofac['total_ar_bal'][] = $arBalTotalStr;
		}

		foreach($arAgingDataArr_wofac as $keyWo => $valWo){
			$ar_tot=array_sum($arAgingDataArr_wofac['total_ar_bal']);
			$ar_tot_avg="";
			$ar_tot_avg=number_format((array_sum($arAgingDataArr_wofac[$keyWo]) * 100) / $ar_tot, 2);
			if(array_sum($arAgingDataArr_wofac[$keyWo])>0){
				$arAgingDataArr_wofac_new[$keyWo]=$CLSReports->numberFormat(array_sum($arAgingDataArr_wofac[$keyWo]),2).' ('.$ar_tot_avg.'%)';
			}
		}
		//  END AGING CYCLE			
		
	}
}

// Generate PDF Data
for($i=0; $i<count($providerIdArr);$i++){
	$PDF_content = '<table style="width:100%" class="rpt_table rpt_table-bordered">
    			  	  <tr>
						<td class="text_b_w" colspan="'.$colspan.'">Provider Name : $proName</td>
					</tr>        
					<tr>
						<td class="text_b_w" style="width:170px;"></td>';
}
// End Code For Generate PDF
// Get PDF file Name

foreach($providerIdArr as $proid => $proName){
	$htmlData.='<table style="width:100%" class="rpt_table rpt_table-bordered"><tr><td class="text_b_w" colspan="'.$colspan.'">Provider Name : '.$proName.'</td></tr>
	<tr><td class="text_b_w"></td>';
		foreach($monthArr as $monthKey => $monthVal){
			$htmlData.='<td class="text_b_w" style="text-align:center; width:'.$width.'px;">'.$monthNameArr[$monthVal].'</td>';
		}
	$htmlData.='</tr><tr><td class="text_10b" style="background:#FFFFFF;">Appointments</td>';
	foreach($monthArr as $monthKey => $monthVal){
		$htmlData.='<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$totalMonthCntArr[$monthVal][$proid].'</td>';
	}
	$htmlData.='</tr>'; 
	foreach($facilityCntArr[$proid] as $facility_id => $facDetails){
	$styleColor= ($detailRes) ? 'background:#FF9900;' : '';
	
	$htmlData.='<tr><td class="text_10b" onclick="toggleTblNew(\'toggle_tbl_'.$proid.'_'.$facility_id.'\');" style="cursor:pointer;background:#FFFFFF; width:'.$width.'px; '.$styleColor.'">';
	if($detailRes){
	$htmlData.='<span id="\'icon_toggle_tbl_'.$proid.'_'.$facility_id.'\'" style="float:right;" class="ui-icon ui-icon-circle-arrow-n fl"></span>';
	}
	
	$htmlData.=''.$facilityNameArr[$facility_id].'</td>';
	foreach($monthArr as $monthKey => $monthVal){
		$htmlData.='<td class="text_10" style="text-align:right; width:'.$width.'px; background:#FFFFFF;">'.$facDetails[$monthVal].'</td>';
	}
	$htmlData.='</tr>';
	if($detailRes){
		$htmlData.='</table>
		<table style="width:100%" class="rpt_table rpt_table-bordered" id=\'toggle_tbl_'.$proid.'_'.$facility_id.'\'">';
		$details_fac = $facilityDetailArr[$proid][$facility_id];
		$htmlData.='<tr>
				<td class="text_b_w" style="width:20px;">#</td>
				<td class="text_b_w" style="width:300px;">Patient Name</td>
				<td class="text_b_w" style="width:300px;">Appointment Date Time</td>
				<td class="text_b_w" style="width:440px;">Procedure</td>
			</tr>';
			foreach($details_fac as $detailKey => $detailVal){
				$rowCnt=$detailKey+1;
				$htmlData.='
				<tr>
					<td style="background:#FFFFFF; width:20px;" class="text_10">'.$rowCnt.'</td>
					<td style="background:#FFFFFF; width:300px;" class="text_10">'.$detailVal['patient_name'].'</td>
					<td style="background:#FFFFFF; width:300px;" class="text_10">'.$detailVal['sa_app_start_date'].' ('.$detailVal['sa_app_starttime'].' - '.$detailVal['sa_app_endtime'].')</td>
					<td style="background:#FFFFFF; width:440px;" class="text_10">'.$detailVal['procedure_name'].'</td>
				</tr>';
			}                
		$htmlData.='
		</table>
		<table style="width:100%" class="rpt_table rpt_table-bordered">';
	}
	}       
	
	$selProcProDetailArr=$selProcDetailArr[$proid];
	ksort($selProcProDetailArr);
	
	$htmlData.='
	<tr>
		<td colspan="'.$colspan.'" style="background:#FFFFFF;">&nbsp;</td>
	</tr>';
	foreach($selProcProDetailArr as $procName => $procDetails){
		$proc_name=($procName!='zzzz')? $procName : 'Default';
		$totalCnt='';
		$htmlData.='
		<tr>
			<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">'.$proc_name.'</td>';
			foreach($monthArr as $monthKey => $monthVal){
				$proc_cnt = count($procDetails[$monthVal]);
				$totalCnt = $proc_cnt+$totalCnt;
				if($monthVal=='total')$proc_cnt=$totalCnt;
				
				$htmlData.='<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$proc_cnt.'</td>';
			}
			
		
		$proc_cnt_tot='';
		$htmlData.='</tr>
		<tr>
			<td class="text_10" style="background:#FFFFFF;"></td>';
			foreach($monthArr as $monthKey => $monthVal){
				$proc_cnt= count($procDetails[$monthVal]);
				$proc_cnt_total=$proc_cnt*100;
				$totProvAppt= $totalMonthCntArr[$monthVal][$proid];
				$proc_cnt_avg= $proc_cnt_total/$totProvAppt;
				$totalCnt= count($total_proc_cnt_arr_tot[$proid]);
				$proc_cnt_tot= $proc_cnt+$proc_cnt_tot;
				if($monthVal=='total'){
					$proc_cnt_total=$proc_cnt_tot*100;
					$proc_cnt_avg=$proc_cnt_total/$totProvAppt;
				}

				$procCountAverage= ($proc_cnt_avg!=0) ? $CLSReports->numberFormat($proc_cnt_avg,2).'%' :'';
				$htmlData.='
				<td class="text_10" style="text-align:right; background:#FFFFFF;"></td>';
			}
		$htmlData.='</tr>';
	}
	$htmlData.='
	<tr>
		<td colspan="'.$colspan.'" style="background:#FFFFFF;">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">Total Encounters</td>';
		$total_Encounter_arr=$totalEncounterArr[$proid];
		$totalEncounter="";
		foreach($monthArr as $monthKey => $monthVal){
			$total_encounter_val= count($total_Encounter_arr[$monthVal]);
			$totalEncounter = $totalEncounter+$total_encounter_val;
			if($monthVal=='total'){
				$total_encounter_val=$totalEncounter;
			}
			$htmlData.='<td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$width.'px;">'.$total_encounter_val.'</td>';
		}
	$htmlData.='
	</tr>
	<tr>
		<td colspan="'.$colspan.'" style="background:#FFFFFF;">&nbsp;</td>
	</tr>   
	<tr>
		<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">Total Charges</td>';

		$total_charges_arr=$totalChargesArr[$proid];
		$totalChargesAmt="";
	
		foreach($monthArr as $monthKey => $monthVal){
			$total_charges_amt= array_sum($total_charges_arr[$monthVal]);
			$totalChargesAmt=$totalChargesAmt+$total_charges_amt;
			if($monthVal=='total'){
				$total_charges_amt=$totalChargesAmt;
			}
			$total_charges_amt=($total_charges_amt!=0) ? $CLSReports->numberFormat($total_charges_amt,2) : '';
			$htmlData.='<td class="text_10" style="text-align:right; background:#FFFFFF; width:'.$width.'px;">'.$total_charges_amt.'</td>';
		}
	$htmlData.='
	</tr>            
	<tr>
		<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">Daily Charges Avg.</td>';
		$totalChargeArr=$totalChargesArr[$proid];
		$GrtotalChargesAmt="";
		$GrtotalMonthcontVal="";
		foreach($monthArr as $monthKey => $monthVal){
			$mnthDayCnt =$monthDayCntArr[$monthVal];
			$total_charges_amt =array_sum($totalChargeArr[$monthVal]);
			$totalChargesAmtAvg= $total_charges_amt/$mnthDayCnt;
			$GrtotalChargesAmt=$total_charges_amt+$GrtotalChargesAmt;
			$GrtotalMonthcontVal=$mnthDayCnt+$GrtotalMonthcontVal;
			if($monthVal=='total'){
				$totalChargesAmtAvg=$GrtotalChargesAmt/$GrtotalMonthcontVal;
			}
			
			$totalChargesAmtAvg=($totalChargesAmtAvg!=0)? $CLSReports->numberFormat($totalChargesAmtAvg,2) : '';
			$htmlData.='<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$totalChargesAmtAvg.'</td>';
		}
	$htmlData.='
	</tr>
	<tr>
		<td colspan="'.$colspan.'" style="background:#FFFFFF;">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">Total Payments</td>';
		$totalPayArr=$totalPaidAmtArr[$proid];
		$totalPayAmt="";
		foreach($monthArr as $monthKey => $monthVal){
			$total_payment_amt= array_sum($totalPayArr[$monthVal]);
			$totalPayAmt=$totalPayAmt+$total_payment_amt;
			if($monthVal=='total'){
				$total_payment_amt=$totalPayAmt;
			}
			
			$total_payment_amt = ($total_payment_amt!=0) ? $CLSReports->numberFormat($total_payment_amt,2) : '';
			$htmlData.='<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$total_payment_amt.'</td>';
		}

	$htmlData.='
	</tr>       	        
	<tr>
		<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">Daily Payments Avg.</td>';
		$totalPayArr=$totalPaidAmtArr[$proid];
		$GrtotalChargesAmt="";
		$GrtotalMonthcontVal="";
		foreach($monthArr as $monthKey => $monthVal){
			$mnthDayCnt=$monthDayCntArr[$monthVal];
			$total_payment_amt= array_sum($totalPayArr[$monthVal]);
			$totalPayAmtAvg=$total_payment_amt/$mnthDayCnt;
			$GrtotalChargesAmt=$total_payment_amt+$GrtotalChargesAmt;
			$GrtotalMonthcontVal=$mnthDayCnt+$GrtotalMonthcontVal;
			if($monthVal== 'total'){
				$totalPayAmtAvg=$GrtotalChargesAmt/$GrtotalMonthcontVal;
			}
			$totalPayAmtAvg=($totalPayAmtAvg!=0)? $CLSReports->numberFormat($totalPayAmtAvg,2) :'';
			$htmlData.='<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$totalPayAmtAvg.'</td>';
		}
	$htmlData.='
	</tr>    
	<tr>
		<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">Appt Payments Avg.</td>';
		$totalPayArr=$totalPaidAmtArr[$proid];
		$totalAppPay="";
		$totalPayAppAvg="";
		$total_payment_amt="";
		$GrtotalChargesAmt="";
		$GrtotalMonthcontVal="";
		foreach($monthArr as $monthKey => $monthVal){
			$totalAppPay=$totalMonthCntArr[$monthVal][$proid];
			$total_payment_amt= array_sum($totalPayArr[$monthVal]);
			$totalPayAppAvg=$total_payment_amt/$totalAppPay;
			$GrtotalChargesAmt=$total_payment_amt+$GrtotalChargesAmt;
			if($monthVal=='total'){
				$totalPayAppAvg=$GrtotalChargesAmt/$GrtotalMonthcontVal;
			}
			$GrtotalMonthcontVal=$totalAppPay+$GrtotalMonthcontVal;
			
			$totalPayAppAvg= ($totalPayAppAvg!=0) ? $CLSReports->numberFormat($totalPayAppAvg,2): '';
			$htmlData.='
			<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$totalPayAppAvg.'</td>';
		}
	$htmlData.='
	</tr>       
	<tr>
		<td colspan="'.$colspan.'" style="background:#FFFFFF;">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">Summary Charges</td>';
		$totalChrgSummary=$summaryTotalChrgArr[$proid];
		$totalSumChrgAmt="";
		foreach($monthArr as $monthKey => $monthVal){
			$total_chrg_sumry_amt= array_sum($totalChrgSummary[$monthVal]);
			$totalSumChrgAmt=$totalSumChrgAmt+$total_chrg_sumry_amt;
			if($monthVal=='total'){
				$total_chrg_sumry_amt=$totalSumChrgAmt;
			}
			
			$total_chrg_sumry_amt=($total_chrg_sumry_amt!=0)? $CLSReports->numberFormat($total_chrg_sumry_amt,2) : '';
			$htmlData.='<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$total_chrg_sumry_amt.'</td>';
		}
		
	$htmlData.='
	</tr>        
	<tr>
		<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">Summary Payments</td>';
		$sumTotalPaidArr=$summaryTotalPaidArr[$proid];
		$totalSumPaidAmt="";
		foreach($monthArr as $monthKey => $monthVal){
			$total_pay_sumry_amt=array_sum($sumTotalPaidArr[$monthVal]);
			$totalSumPaidAmt=$totalSumPaidAmt+$total_pay_sumry_amt;
			if($monthVal=='total'){
				$total_pay_sumry_amt=$totalSumPaidAmt;
			}
			$total_pay_sumry_amt= ($total_pay_sumry_amt!=0)? $CLSReports->numberFormat($total_pay_sumry_amt,2) : '';
			$htmlData.='<td class="text_10" style="text-align:right; background:#FFFFFF;">'.$total_pay_sumry_amt.'</td>';
		}
	$htmlData.='
	</tr>
	<tr>
		<td colspan="'.$colspan.'" style="background:#FFFFFF;">&nbsp;</td>
	</tr>                
</table>
<table style="width:100%" class="rpt_table rpt_table-bordered">   	
	<tr>
		<td class="text_10b" style="background:#FFFFFF;" colspan="'.$agingCols.'">Total A/R AGING</td>
	</tr>
	<tr>';
		foreach($aggingDrop as $aging_kay => $aging_val){
			$htmlData.='<td class="text_10b" style="background:#FFFFFF; width:'.$agingWidth.'px; text-align:center;">'.$aging_val.'</td>';
		}
		$htmlData.='<td class="text_10b" style="background:#FFFFFF; width:'.$agingWidth.'px;">Total</td>
	</tr>        
	<tr>';
		foreach($aggingDrop as $aging_kay => $aging_val){
			$htmlData.='<td class="text_10" style="background:#FFFFFF; width:'.$agingWidth.'px; text-align:right;">'.$arAgingDataArr[$proid][$aging_kay].'</td>';
		}
		$arProBal=$arAgingDataArr[$proid];
		$htmlData.='<td class="text_10" style="background:#FFFFFF; width:'.$agingWidth.'px;">'.$arAgingDataArr[$proid]['total_ar_bal'].'</td>
	</tr>
	<tr>
		<td class="text_10b" style="background:#FFFFFF; text-align:right;" colspan="'.$agingCols.'">&nbsp;</td>
	</tr>
</table>';
}

$htmlData.='<table style="width:100%" class="rpt_table rpt_table-bordered"><tr><td colspan="'.$colspan.'" style="background-color:#009933;height:2px;"></td></tr>       
	<tr><td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">G. Total Appointments</td>';
		
foreach($monthArr as $monthKey => $monthVal){
		$htmlData.='<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.array_sum($GrandtotalMonthCntArr[$monthVal]).'</td>';
		}
$htmlData.='</tr> <tr><td colspan="'.$colspan.'" style="background:#FFFFFF;">&nbsp;</td></tr><tr>
		<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">G. Total Encounters</td>';
		$GrandtotalEncounter="";
		foreach($monthArr as $monthKey => $monthVal){
			$grand_total_encounter_val=count($GrandtotalEncounterArr[$monthVal]);
			$GrandtotalEncounter=$GrandtotalEncounter+$grand_total_encounter_val;
			if($monthVal=='total'){
				$grand_total_encounter_val=$GrandtotalEncounter;
			}
			$htmlData.='<td class="text_10b" style="text-align:right; background:#FFFFFF; width:'.$width.'px;">'.$grand_total_encounter_val.'</td>';
		}
	$htmlData.='
	</tr>
	<tr>
		<td colspan="'.$colspan.'" style="background:#FFFFFF;">&nbsp;</td>
	</tr>   
	<tr>
		<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">G. Total Charges</td>';
		$GrandtotalChargesAmt="";
		foreach($monthArr as $monthKey => $monthVal){
			$grand_total_charges_amt=array_sum($GrandtotalChargesArr[$monthVal]);
			$GrandtotalChargesAmt=$GrandtotalChargesAmt+$grand_total_charges_amt;
			if($monthVal=='total'){
				$grand_total_charges_amt=$GrandtotalChargesAmt;
			}
			
			$grand_total_charges_amt=($grand_total_charges_amt!=0) ?  $CLSReports->numberFormat($grand_total_charges_amt,2) : '';
			$htmlData.='<td class="text_10b" style="text-align:right; background:#FFFFFF; width:'.$width.'px;">'.$grand_total_charges_amt.'</td>';
		}
	$htmlData.='
	</tr>            
	<tr>
		<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">G. Daily Charges Avg.</td>';
		$GrtotalChargesAmt="";
		$GrtotalMonthcontVal="";
		foreach($monthArr as $monthKey => $monthVal){
			$mnthDayCnt=$monthDayCntArr[$monthVal];
			$grand_total_charges_amt= array_sum($GrandtotalChargesArr[$monthVal]);
			$GrandtotalChargesAmtAvg=$grand_total_charges_amt/$mnthDayCnt;
			$GrtotalChargesAmt=$grand_total_charges_amt+$GrtotalChargesAmt;
			$GrtotalMonthcontVal=$mnthDayCnt+$GrtotalMonthcontVal;
			if($monthVal=='total'){
				$GrandtotalChargesAmtAvg=$GrtotalChargesAmt/$GrtotalMonthcontVal;
			}
			
			$GrandtotalChargesAmtAvg= ($GrandtotalChargesAmtAvg!=0) ? $CLSReports->numberFormat($GrandtotalChargesAmtAvg,2) : '';
			$htmlData.='
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$GrandtotalChargesAmtAvg.'</td>';
		}
	$htmlData.='
	</tr>
	<tr>
		<td colspan="'.$colspan.'" style="background:#FFFFFF;">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">G. Total Payments</td>';
		$GrandtotalPayAmt="";
		foreach($monthArr as $monthKey => $monthVal){
			$grand_total_payment_amt=array_sum($GrandtotalPaidAmtArr[$monthVal]);
			$GrandtotalPayAmt=$GrandtotalPayAmt+$grand_total_payment_amt;
			if($monthVal=='total'){
				$grand_total_payment_amt=$GrandtotalPayAmt;
			}
			$grand_total_payment_amt=($grand_total_payment_amt!=0) ?  $CLSReports->numberFormat($grand_total_payment_amt,2) : '';
			$htmlData.='
			<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$grand_total_payment_amt.'</td>';
		}
	$htmlData.='
	</tr>       	        
	<tr>
		<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">G. Daily Payments Avg.</td>';
		$GrtotalChargesAmt="";
		$GrtotalMonthcontVal="";
		foreach($monthArr as $monthKey => $monthVal){
			$mnthDayCnt=$monthDayCntArr[$monthVal];
			$grand_total_payment_amt=array_sum($GrandtotalPaidAmtArr[$monthVal]);
			$GrandtotalPayAmtAvg=$grand_total_payment_amt/$mnthDayCnt;
			$GrtotalChargesAmt=$grand_total_payment_amt+$GrtotalChargesAmt;
			$GrtotalMonthcontVal=$mnthDayCnt+$GrtotalMonthcontVal;
			 if($monthVal=='total'){
				$GrandtotalPayAmtAvg=$GrtotalChargesAmt/$GrtotalMonthcontVal;
			}
			$GrandtotalPayAmtAvg=($GrandtotalPayAmtAvg!=0) ?  $CLSReports->numberFormat($GrandtotalPayAmtAvg,2) : '';
			$htmlData.='<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$GrandtotalPayAmtAvg.'</td>';
		}
	$htmlData.='
	</tr>    
	<tr>
		<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">G. Appt Payments Avg.</td>';
		$GrandtotalAppPay="";
		$GrandtotalPayAppAvg="";
		$grand_total_payment_amt="";
		$GrtotalChargesAmt="";
		$GrtotalMonthcontVal="";
		foreach($monthArr as $monthKey => $monthVal){
			$GrandtotalAppPay=array_sum($GrandtotalMonthCntArr[$monthVal]);
			$grand_total_payment_amt=array_sum($GrandtotalPaidAmtArr[$monthVal]);
			$GrandtotalPayAppAvg=$grand_total_payment_amt/$GrandtotalAppPay;
			$GrtotalChargesAmt=$grand_total_payment_amt+$GrtotalChargesAmt;
			 if($monthVal=='total'){
				$GrandtotalPayAppAvg=$GrtotalChargesAmt/$GrtotalMonthcontVal;
			}
			$GrtotalMonthcontVal=$GrandtotalAppPay+$GrtotalMonthcontVal;
			$GrandtotalPayAppAvg=($GrandtotalPayAppAvg!=0) ?  $CLSReports->numberFormat($GrandtotalPayAppAvg,2) : '';
			$htmlData.='<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$GrandtotalPayAppAvg.'</td>';
		}
	$htmlData.='
	</tr>       
	<tr>
		<td colspan="'.$colspan.'" style="background:#FFFFFF;">&nbsp;</td>
	</tr>
	<tr>
		<td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">G. Summary Charges</td>';
		$GrandtotalSumChrgAmt="";
		foreach($monthArr as $monthKey => $monthVal){
			$grand_total_chrg_sumry_amt=array_sum($GrandsummaryTotalChrgArr[$monthVal]);
			$GrandtotalSumChrgAmt=$GrandtotalSumChrgAmt+$grand_total_chrg_sumry_amt;
			if($monthVal=='total'){
				$grand_total_chrg_sumry_amt=$GrandtotalSumChrgAmt;
			}
			$grand_total_chrg_sumry_amt=($grand_total_chrg_sumry_amt!=0) ?  $CLSReports->numberFormat($grand_total_chrg_sumry_amt,2) : '';
			$htmlData.='<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$grand_total_chrg_sumry_amt.'</td>';
		}
	$htmlData.='</tr><tr><td class="text_10b" style="background:#FFFFFF; width:'.$width.'px;">G. Summary Payments</td>';
		$GrandtotalSumPaidAmt="";
		foreach($monthArr as $monthKey => $monthVal){
			$grand_total_pay_sumry_amt=array_sum($GrandsummaryTotalPaidArr[$monthVal]);
			$GrandtotalSumPaidAmt=$GrandtotalSumPaidAmt+$grand_total_pay_sumry_amt;
			if($monthVal=='total'){
				$grand_total_pay_sumry_amt=$GrandtotalSumPaidAmt;
			}
			$grand_total_pay_sumry_amt=($grand_total_pay_sumry_amt!=0) ?  $CLSReports->numberFormat($grand_total_pay_sumry_amt,2) : '';
			$htmlData.='<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.$grand_total_pay_sumry_amt.'</td>';
		}
	
	
	$htmlData.='</tr><tr><td colspan="'.$colspan.'" style="background:#FFFFFF;">&nbsp;</td></tr></table>
		<table style="width:100%" class="rpt_table rpt_table-bordered"><tr><td class="text_10b" style="background:#FFFFFF;" colspan="'.$agingCols.'">G. Total A/R AGING</td></tr><tr>';
		foreach($aggingDrop as $aging_kay => $aging_val){
			$htmlData.='<td class="text_10b" style="background:#FFFFFF; width:'.$agingWidth.'px; text-align:center;">'.$aging_val.'</td>';
		}
			$htmlData.='<td class="text_10b" style="background:#FFFFFF; width:'.$agingWidth.'px;">Total</td></tr><tr>';
		foreach($aggingDrop as $aging_kay => $aging_val){
			$htmlData.='<td class="text_10" style="background:#FFFFFF; width:'.$agingWidth.'px; text-align:right;">'.$arAgingDataArr_wofac_new[$aging_kay].'</td>';
		}
			$htmlData.='<td class="text_10" style="background:#FFFFFF; width:'.$agingWidth.'px;">'.$arAgingDataArr_wofac_new['total_ar_bal'].'</td></tr><tr>
			<td class="text_10b" style="background:#FFFFFF; text-align:right;" colspan="'.$agingCols.'">&nbsp;</td></tr></table>
			<table style="width:100%" class="rpt_table rpt_table-bordered">
			<tr><td colspan="'.$colspan.'" style="background-color:#009933;height:2px;"></td></tr>   
			<tr><td colspan="'.$colspan.'" style="background:#FFFFFF;">&nbsp;</td></tr>                
			</table>';
			$curDate = date($globalDateFormat.' H:i A');
			$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
			$op_name = $op_name_arr[1][0];
			$op_name .= $op_name_arr[0][0];
			$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
			$html_page_content = <<<DATA
				$stylePDF
				<page backtop="5mm" backbottom="13mm">			
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
					<table class="rpt_table rpt rpt_table-bordered rpt_padding">
						<tr>
							<td class="rptbx1" style="text-align:left; width:320px;">Provider Monthly Report ($processReport)</td>
							<td class="rptbx2" style="text-align:left; width:400px">Report Period : $Start_date - $End_date</td>
							<td class="rptbx3" style="text-align:left; width:320px;">Created by: $op_name on $curDate</td>
						</tr>	
					</table>
				</page_header>
				$htmlData
				</page>
DATA;
$file_location = write_html($html_page_content);

$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
$htmlData= $styleHTML.$htmlData;
$HTMLCreated=0;
if($printFile){
	$HTMLCreated=1;
	if($output_option=='view' || $output_option=='output_csv'){
		$header_part='
			<table style="width:100%" class="rpt_table rpt_table-bordered">		
				<tr>
					<td class="rptbx1" style="width:33%;">Provider Monthly Report ('.$processReport.')</td>
					<td class="rptbx2" style="text-align:center;width:34%;">Report Period : '.$Start_date.' to '.$End_date.'</td>
					<td class="rptbx3" style="text-align:right;width:33%;">
						Created By '.$op_name.' on '.$curDate.'&nbsp;
					</td>
				</tr>
			</table>';
		
		if($callFrom!='scheduled'){
			echo $header_part.$htmlData;
		}
	}
} else {
	if($callFrom!='scheduled'){
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}
?>
