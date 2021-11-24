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
ini_set("memory_limit","3072M");
$printFile = true;
$arrGroupSel=array();
$arrFacilitySel=array();
$arrDoctorSel=array();
$arrAllGroups = array();

if( $_POST['form_submitted'] ){
	$printFile = false;
	$checkInDataArr=array();
	
	$arrDateRange= $CLSCommonFunction->changeDateSelection();
	if($dayReport=='Daily'){
		$Start_date = $End_date = date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$Start_date = $arrDateRange['WEEK_DATE'];
		$End_date = date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$Start_date = $arrDateRange['MONTH_DATE'];
		$End_date = date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$Start_date = $arrDateRange['QUARTER_DATE_START'];
		$End_date = $arrDateRange['QUARTER_DATE_END'];
	}

	//--- CHANGE DATE FORMAT ----
	$date_format_SQL = get_sql_date_format();
	$startDate = getDateFormatDB($Start_date);
	$endDate = getDateFormatDB($End_date);

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
	$sc_name = implode(',',$facility_name);
	$Physician = implode(',',$phyId);
	$department = implode(',',$department);
	$operator_id = implode(',',$operator_id);
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
	$strDeptJoin = ''; $strDeptField = '';
	if( $department || $groupBy == 'grpby_department' )
	{
		$strDeptField = ', cpt_fee_tbl.departmentId';
		$strDeptJoin = ' JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = main.proc_code_id'; 
	}

	if($DateRangeFor=='dos' || $DateRangeFor=='doc'){
		include('account_receivable_result_dos.php');
	}else{
		
		//DOT OR DOP BASED
		//TRANSACTIONS TABLE
		$qry="Select trans.report_trans_id, trans.parent_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by,
		trans.trans_type, trans.trans_amount, trans.trans_del_operator_id,
		trans.trans_dot, trans.trans_dop, trans.trans_del_date FROM report_enc_trans trans WHERE LOWER(trans_type)!='charges'";
		if($reptByForFun=='dor'){
			$qry.=" AND (trans.trans_dop BETWEEN '$startDate' and '$endDate')";
		}else{
			$qry.=" AND (trans.trans_dot BETWEEN '$startDate' and '$endDate') AND trans_del_date<='$endDate'";
		}
		$qry.=" ORDER BY trans.master_tbl_id, trans.report_trans_id, trans.trans_dot, trans.trans_dot_time";

		$rs=imw_query($qry);
		while($res = imw_fetch_assoc($rs)){
			$report_trans_id=$res['report_trans_id'];
			$encounter_id= $res['encounter_id'];
			$chgDetId= $res['charge_list_detail_id'];
			$trans_type= strtolower($res['trans_type']);
			$trans_by= strtolower($res['trans_by']);			

			$tempRecordData[$report_trans_id]=$res['trans_amount'];
			$tempChgDet[$chgDetId]=$chgDetId;
			
			switch($trans_type){
				case 'paid':
				case 'copay-paid':
				case 'deposit':
				case 'interest payment':
				case 'negative payment':
				case 'copay-negative payment':
					$paidForProc=0;
					$paidForProc=$res['trans_amount'];

					if($trans_type=='negative payment' || $trans_type=='copay-negative payment' || $res['trans_del_operator_id']>0)$paidForProc="-".$res['trans_amount'];
					if(($trans_type=='negative payment' || $trans_type=='copay-negative payment') && $res['trans_del_operator_id']>0)$paidForProc=$res['trans_amount'];

					$tempPaymentArr[$chgDetId]+=$paidForProc;

					//IF parent_id >0 THEN IT MEANS RECORD IS UPDATED. THEN REMOVE PREVIOUS FETCHED AMOUNT.
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = ($trans_type=='negative payment') ? $tempRecordData[$res['parent_id']] : "-".$tempRecordData[$res['parent_id']];
						$tempPaymentArr[$chgDetId]+=$prevFetchedAmt;
					}
				break;

				case 'credit':
				case 'debit':
					$crddbtamt=0;
					if($trans_type=='credit'){ 
						if($res['trans_del_operator_id']>0){
							$crddbtamt="-".$res['trans_amount'];
						}else{
							$crddbtamt=$res['trans_amount'];
						}
					}else{  //debit
						if($res['trans_del_operator_id']>0){
							$crddbtamt=$res['trans_amount'];
						}else{
							$crddbtamt="-".$res['trans_amount'];
						}
					}
					$tempPaymentArr[$chgDetId]+=$crddbtamt;
					
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = ($trans_type=='credit') ? "-".$tempRecordData[$res['parent_id']] : $tempRecordData[$res['parent_id']];
						$tempPaymentArr[$chgDetId]+= $prevFetchedAmt;
					}
				break;

				case 'default_writeoff':
					$tempDefaultWriteOff[$chgDetId]= $res['trans_amount'];
				break;

				case 'write off':
				case 'discount':
				case 'over adjustment':
					if($res['trans_del_operator_id']>0){
						$tempAdjArr[$chgDetId]+="-".$res['trans_amount'];
					}else{
						$tempAdjArr[$chgDetId]+=$res['trans_amount'];
					}
					
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
						$tempAdjArr[$chgDetId]+= $prevFetchedAmt;
					}
				break;

				case 'adjustment':
				case 'returned check':
					if($res['trans_del_operator_id']>0){
						$tempAdjArr[$chgDetId]+= $res['trans_amount'];
					}else{
						$tempAdjArr[$chgDetId]+= "-".$res['trans_amount'];						
					}

					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = $tempRecordData[$res['parent_id']];
						$tempAdjArr[$chgDetId]+= $prevFetchedAmt;
					}
				break;
				
				case 'refund':
					if($res['trans_del_operator_id']>0){
						$tempRefArr[$chgDetId]+=$res['trans_amount'];
					}else{
						$tempRefArr[$chgDetId]+="-".$res['trans_amount'];
					}

					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = $tempRecordData[$res['parent_id']];
						$tempRefArr[$chgDetId]+= $prevFetchedAmt;
					}
				break;
			}
		}
		//-----------------------
	
		
		//GET PREVIOUS DEFAULT WRITE-OFF FOR ENCOUNTERS THAT ARE FALLING IN SEARCHED DATE CRITERIA.
		$arrPrevDateWriteOff=array();
		if(sizeof($tempDefaultWriteOff)>0){
			$dd=explode('-', $startDate);
			$prevDateForWriteOff = date('Y-m-d', mktime(0,0,0, $dd[1], $dd[2]-1,$dd[0]));
			
			$temp_normalWriteChgIdStr=implode(',', array_keys($tempDefaultWriteOff));
			$qry="Select charge_list_detail_id, trans_amount FROM report_enc_trans WHERE charge_list_detail_id IN($temp_normalWriteChgIdStr) 
			AND LOWER(trans_type)='default_writeoff'";
			if($reptByForFun=='dot'){
				$qry.=" AND (trans_dot BETWEEN '2015-01-01' AND '$prevDateForWriteOff')";
				$qry.=" ORDER BY trans_dot ASC, trans_dot_time ASC";
			}else{
				$qry.=" AND (trans_dop BETWEEN '2015-01-01' AND '$prevDateForWriteOff')";
				$qry.=" ORDER BY trans_dop ASC, trans_dop_time ASC";
			}
			$rs=imw_query($qry);
			while($res=imw_fetch_assoc($rs)){
				$arrPrevDateWriteOff[$res['charge_list_detail_id']]= $res['trans_amount'];
			}unset($rs);
		}	

		//GET ENCOUNTERS OF FIRST POSTED DATE
		$arrFirstPostedChgIds=array();
		$qry="Select encounter_id, charge_list_detail_id, del_status FROM report_enc_detail WHERE 
		(first_posted_date BETWEEN '$startDate' AND '$endDate') 
		AND (del_status='0' OR (del_status='1' AND DATE_FORMAT(trans_del_date, '%Y-%m-%d')>'$endDate'))";
		if(empty($sc_name) === false){
			$qry.= " and facility_id in($sc_name)";
		}
		if(empty($grp_id) === false){
			$qry.= " and gro_id in($grp_id)";
		}
		if(empty($Physician) === false){
			$qry.= " and primary_provider_id_for_reports in($Physician)";
		}
		if(empty($operator_id) === false){
			$qry.= " and operator_id in($operator_id)";
		}
		if(empty($insCompanies) === false){
			$qry.= " and ( main.pri_ins_id in($insCompanies) 
					OR	main.sec_ins_id in($insCompanies)
					OR	main.tri_ins_id in($insCompanies) )";
		}
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$arrFirstPostedChgIds[$res['charge_list_detail_id']] = $res['charge_list_detail_id'];
		}unset($rs);
		
	
		//MERGING OF ARRAYS
		$arrFinalAllChgIds=array();
		$arrFinalAllChgIds=array_merge($tempChgDet, $arrFirstPostedChgIds); // merging with first posted array
		$arrFinalAllChgIds=array_unique($arrFinalAllChgIds);

		$main_encounter_id_arr = array();
		$tempArr=array();
		if(sizeof($arrFinalAllChgIds)>0){
			$splitted_chgids = array_chunk($arrFinalAllChgIds,3000);
		
			foreach($splitted_chgids as $arr){
				$str_splitted_chgids 	 = implode(',',$arr);
		
				$qry="Select main.encounter_id, main.charge_list_detail_id, 
				main.date_of_service, main.gro_id, main.facility_id, main.primary_provider_id_for_reports as 'primaryProviderId', main.over_payment, 
				main.first_posted_date, (main.charges * main.units) as totalAmt, main.del_status, DATE_FORMAT(main.trans_del_date, '%Y-%m-%d') as 'trans_del_date',
				patient_data.lname as patient_lname,patient_data.fname as patient_fname, patient_data.mname as patient_mname 
				".$strDeptField."
				FROM report_enc_detail main
				".$strDeptJoin." 			
				LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id 
				LEFT JOIN pos_tbl on pos_tbl.pos_id =  pos_facilityies_tbl.pos_id 
				JOIN patient_data on patient_data.id = main.patient_id 
				LEFT JOIN users ON users.id = main.primary_provider_id_for_reports 
				WHERE main.charge_list_detail_id IN(".$str_splitted_chgids.")";
				if(empty($sc_name) === false){
					$qry.= " and main.facility_id in($sc_name)";
				}
				if(empty($grp_id) === false){
					$qry.= " and main.gro_id in($grp_id)";
				}
				if(empty($Physician) === false){
					$qry.= " and main.primary_provider_id_for_reports in($Physician)";
				}
				if(empty($operator_id) === false){
					$qry.= " and main.operator_id in($operator_id)";
				}
				if(empty($department) === false){
					$qry.= " and cpt_fee_tbl.departmentId in($department)";
				}
				if(empty($insCompanies) === false){
					$qry.= " and ( main.pri_ins_id in($insCompanies) 
					OR	main.sec_ins_id in($insCompanies)
					OR	main.tri_ins_id in($insCompanies) )";
				}
				$qry.= " ORDER BY pos_facilityies_tbl.facilityPracCode, users.lname, users.fname";				
				$rs=imw_query($qry);
				while($res = imw_fetch_assoc($rs)){
	
					$chgDetId= $res['charge_list_detail_id'];

					$doctor_id = $res['primaryProviderId'];
					$facility_id = $res['facility_id'];
					$group_id = $begRes['gro_id'];
					$opr_id = $begRes['operator_id'];
					$dept_id = isset($begRes['departmentId']) ? $begRes['departmentId'] : 0;
					
					$firstGroupBy = $doctor_id;
					$secGroupBy = $facility_id;
					if($groupBy=='grpby_facility'){
						$firstGroupBy = $facility_id;
						$secGroupBy = $doctor_id;
					}elseif($groupBy=='grpby_groups'){
						$firstGroupBy = $group_id;
						$secGroupBy = $doctor_id;
					}elseif($groupBy=='grpby_operators'){
						$firstGroupBy = $opr_id;
						$secGroupBy = $facility_id;
					}elseif($groupBy=='grpby_department'){
						$firstGroupBy = $dept_id;
						$secGroupBy = $doctor_id;
					}
					
					$checkInDataArr[$firstGroupBy][$secGroupBy]=1;

					//CHARGES
					if(($res['first_posted_date']>=$startDate && $res['first_posted_date']<=$endDate) && ($res['del_status']=='0' || ($res['del_status']=='1' && $res['trans_del_date']>$endDate))){
						$dr_tot_amt_arr[$firstGroupBy][$secGroupBy][]=$res['totalAmt'];
					}
					//OVER-PAYMENT
					if($res['over_payment']>0){
						$dr_tot_credit_arr[$firstGroupBy][$secGroupBy][]= $res['over_payment'];
					}
					//PAYMENT
					if($tempPaymentArr[$chgDetId]){
						$dr_tot_paid_arr[$firstGroupBy][$secGroupBy][] = $tempPaymentArr[$chgDetId];
					}
					//ADJUSTMENTS
					if($tempAdjArr[$chgDetId]){
						$dr_tot_adj_arr[$firstGroupBy][$secGroupBy][] = $tempAdjArr[$chgDetId];
					}
					if($tempDefaultWriteOff[$chgDetId]){ 
						$dr_tot_adj_arr[$firstGroupBy][$secGroupBy][] = $tempDefaultWriteOff[$chgDetId];
					}
					//REFUND
					if($tempRefArr[$chgDetId]){
						$dr_tot_ref_arr[$firstGroupBy][$secGroupBy][] = $tempRefArr[$chgDetId];
					}
					//WRITE-OFF >0 CHECK REMOVED BECAUSE IF DEFAULT WRITE-OFF NOW BECOME "0" EVEN THEN WE HAVE TO DEDUCT PREVIOUS WRITE-OFF AMT.
					if(isset($tempDefaultWriteOff[$chgDetId])){ 
						$dr_tot_prev_writeoff_arr[$firstGroupBy][$secGroupBy][] = $arrPrevDateWriteOff[$chgDetId];
					}
				}
				unset($qryRs);		
			}
		}

		unset($tempDefaultWriteOff);
		unset($arrPrevDateWriteOff);
		unset($tempChgDet);
		unset($tempPaymentArr);
		unset($tempAdjArr);
		unset($tempRefArr);	
	
	
		//GETTING BEGINNING A/R  --------------------------------
		function getPreviousBal($callFrom='', $startDate='', $endDate='', $prevDate='', $strProviders='', $strFacilities='', $groupBy='', $grp_id='', $reptByForFun='dot',$strDepartment = '', $strOperator = '',$strInsCompanies){
			global $fac_name_arr;
			$dr_tot_beg_ar_arr=array();
			$arrBalEncs =array();
			
			$arrAllEncounters=array();
			$tempPaymentArr=array();
			$tempDefaultWriteOff=array();
			$tempAdjArr=array();
			$tempRefArr=array();
			$tempWriteOffArr=array();
			$arrFirstPostedCharges=array();
			
			$deptJoin = ''; $deptField = '';
			if( $strDepartment || $groupBy == 'grpby_department' )
			{
				$deptField = ', cpt_fee_tbl.departmentId';
				$deptJoin = ' JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode'; 
			}
			
			$qry="Select trans.report_trans_id, trans.parent_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, 
			trans.trans_type, trans.trans_amount, trans.trans_del_operator_id, trans.trans_del_date 
			FROM report_enc_trans trans WHERE LOWER(trans_type)!='charges'";
			if($reptByForFun=='dor'){
				$qry.=" AND (trans.trans_dop BETWEEN '2005-01-01' AND '".$prevDate."')";
			}else{
				$qry.=" AND (trans.trans_dot BETWEEN '2005-01-01' AND '".$prevDate."')";
			}
			$qry.=" AND (trans.trans_del_operator_id='0' OR (trans.trans_del_operator_id>0 && trans.trans_del_date<='".$prevDate."')) 
			ORDER BY trans.master_tbl_id, trans.report_trans_id, trans.trans_dot, trans.trans_dot_time";
	
			$rs=imw_query($qry);
			while($res = imw_fetch_assoc($rs)){
				$report_trans_id=$res['report_trans_id'];
				$encounter_id= $res['encounter_id'];
				$chgDetId= $res['charge_list_detail_id'];
				$trans_type= strtolower($res['trans_type']);
				$trans_by= strtolower($res['trans_by']);			
	
				$tempRecordData[$report_trans_id]=$res['trans_amount'];
				$tempChgDet[$chgDetId]=$chgDetId;

/*				if($res['trans_del_date']>=$startDate && $res['trans_del_date']<=$endDate){
					$arrAllEncounters[$encounter_id]=$encounter_id;
				}*/
				
				switch($trans_type){
					case 'paid':
					case 'copay-paid':
					case 'deposit':
					case 'interest payment':
					case 'negative payment':
					case 'copay-negative payment':
					
						$paidForProc=$res['trans_amount'];
						if($trans_type=='negative payment' || $trans_type=='copay-negative payment' || $res['trans_del_operator_id']>0)$paidForProc="-".$res['trans_amount'];
						if(($trans_type=='negative payment' || $trans_type=='copay-negative payment') && $res['trans_del_operator_id']>0)$paidForProc=$res['trans_amount'];						
						$tempPaymentArr[$chgDetId]+=$paidForProc;
	
						//IF parent_id >0 THEN IT MEANS RECORD IS UPDATED. THEN REMOVE PREVIOUS FETCHED AMOUNT.
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = ($trans_type=='negative payment' || $trans_type=='copay-negative payment') ? $tempRecordData[$res['parent_id']] : "-".$tempRecordData[$res['parent_id']];
							$tempPaymentArr[$chgDetId]+=$prevFetchedAmt;
						}
					break;
	
					case 'credit':
					case 'debit':
						$crddbtamt=$res['trans_amount'];
						if($trans_type=='credit'){ 
							$crddbtamt= ($res['trans_del_operator_id']>0) ? "-".$res['trans_amount'] : $res['trans_amount'];							
						}else{  //debit
							$crddbtamt= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
						}
						$tempPaymentArr[$chgDetId]+=$res['trans_amount'];
						
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = ($trans_type=='credit') ? "-".$tempRecordData[$res['parent_id']] : $tempRecordData[$res['parent_id']];
							$tempPaymentArr[$chgDetId]+= $prevFetchedAmt;
						}
					break;
	
					case 'default_writeoff':
						$tempDefaultWriteOff[$chgDetId]= $res['trans_amount'];
					break;
	
					case 'write off':
						if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];
						$tempWriteOffArr[$chgDetId]+=$res['trans_amount'];
	
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
							$tempWriteOffArr[$chgDetId]+= $prevFetchedAmt;
						}
					break;	
						
					case 'discount':
					case 'over adjustment':
						if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];
						$tempAdjArr[$chgDetId]+=$res['trans_amount'];
	
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
							$tempAdjArr[$chgDetId]+= $prevFetchedAmt;
						}
					break;

					case 'adjustment':
					case 'returned check':
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
						$tempAdjArr[$chgDetId]+=	$res['trans_amount'];					
	
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = $tempRecordData[$res['parent_id']];
							$tempAdjArr[$chgDetId]+= $prevFetchedAmt;
						}
					break;
					
					case 'refund':
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
						$tempRefArr[$chgDetId]+=$res['trans_amount'];					
	
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = $tempRecordData[$res['parent_id']];
							$tempRefArr[$chgDetId]+= $prevFetchedAmt;
						}
					break;
				}
			}

			//GET ENCOUNTERS OF FIRST POSTED DATE
			$arrFirstPostedChgIds=array();
			$qry="Select encounter_id, charge_list_detail_id, (charges * units) as totalAmt FROM report_enc_detail WHERE (first_posted_date BETWEEN '2005-01-01' AND '$prevDate') 
			AND (del_status='0' OR (del_status='1' AND DATE_FORMAT(trans_del_date, '%Y-%m-%d')>'$prevDate'))";
			if(empty($strProviders)===false){
				$qry.=" AND primary_provider_id_for_reports IN (".$strProviders.")";
			}
			if(empty($strFacilities)===false){
				$qry.=" AND facility_id IN (".$strFacilities.")";						
			}
			if(empty($grp_id) === false){
				$qry .= " AND gro_id IN ($grp_id)";
			}
			if(empty($strOperator) === false){
				$qry .= " and operator_id in($strOperator)";
			}
			if(empty($strInsCompanies) === false){
				$qry.= " and (primaryInsuranceCoId in($strInsCompanies) 
						OR	secondaryInsuranceCoId in($strInsCompanies)
						OR	tertiaryInsuranceCoId in($strInsCompanies) )";
			}

			$rs=imw_query($qry);
			while($res=imw_fetch_array($rs)){
				$arrFirstPostedChgIds[$res['charge_list_detail_id']] = $res['charge_list_detail_id'];
				$arrFirstPostedCharges[$res['charge_list_detail_id']]+= $res['totalAmt'];
			}unset($rs);
	

			//MERGING OF ARRAYS
			$arrFinalAllChgIds=array();
			$arrFinalAllChgIds=array_merge($tempChgDet, $arrFirstPostedChgIds); // merging with first posted array
			$arrFinalAllChgIds=array_unique($arrFinalAllChgIds);

			//MAKING FINAL ARRAY
			if(sizeof($arrFinalAllChgIds)>0){
				$splitted_chgids = array_chunk($arrFinalAllChgIds,3000);

				foreach($splitted_chgids as $arr){
					$str_splitted_chgids 	 = implode(',',$arr);

					$qry="Select main.encounter_id, main.charge_list_detail_id, 
					main.date_of_service, main.gro_id, main.facility_id, main.primary_provider_id_for_reports as 'primaryProviderId', main.over_payment, 
					main.del_status, DATE_FORMAT(main.trans_del_date, '%Y-%m-%d') as 'trans_del_date',
					patient_data.lname as patient_lname,patient_data.fname as patient_fname, patient_data.mname as patient_mname 
					FROM report_enc_detail main
					".$deptJoin."
					LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id 
					LEFT JOIN pos_tbl on pos_tbl.pos_id =  pos_facilityies_tbl.pos_id 
					JOIN patient_data on patient_data.id = main.patient_id 
					LEFT JOIN users ON users.id = main.primary_provider_id_for_reports 
					WHERE main.charge_list_detail_id IN(".$str_splitted_chgids.") 
					AND ((main.del_status='0') OR (main.del_status='1' AND DATE_FORMAT(main.trans_del_date, '%Y-%m-%d')>'$prevDate'))";
					if(empty($strProviders)===false){
						$qry.=" AND main.primary_provider_id_for_reports IN (".$strProviders.")";
					}
					if(empty($strFacilities)===false){
						$qry.=" AND main.facility_id IN (".$strFacilities.")";						
					}
					if(empty($grp_id) === false){
						$qry .= " AND main.gro_id IN ($grp_id)";
					}
					if(empty($strOperator) === false){
						$qry .= " and main.operator_id in($strOperator)";
					}
					if(empty($strDepartment) === false){
						$qry .= " and cpt_fee_tbl.departmentId in($strDepartment)";
					}
					if(empty($strInsCompanies) === false){
						$qry.= " and ( main.pri_ins_id in($strInsCompanies) 
						OR	main.sec_ins_id in($strInsCompanies)
						OR	main.tri_ins_id in($strInsCompanies) )";
					}
					$qry.= " ORDER BY pos_facilityies_tbl.facilityPracCode, users.lname, users.fname";	
					$rs=imw_query($qry);

					while($res = imw_fetch_assoc($rs)){
	
						$charges=0;
						$chgDetId= $res['charge_list_detail_id'];
						$doctor_id = $res['primaryProviderId'];
						$facility_id = $res['facility_id'];
						$encounter_id= $res['encounter_id'];
						//$arrAllEncounters[$encounter_id]=$encounter_id;

						$firstGroupBy = $doctor_id;
						$secGroupBy = $facility_id;
						if($groupBy=='grpby_facility'){
							$firstGroupBy = $facility_id;
							$secGroupBy = $doctor_id;
						}elseif($groupBy=='grpby_groups'){
							$firstGroupBy = $group_id;
							$secGroupBy = $doctor_id;
						}elseif($groupBy=='grpby_operators'){
							$firstGroupBy = $opr_id;
							$secGroupBy = $facility_id;
						}elseif($groupBy=='grpby_department'){
							$firstGroupBy = $dept_id;
							$secGroupBy = $doctor_id;
						}						

						//CHARGES
						if($arrFirstPostedCharges[$chgDetId]>0){
							$charges=$arrFirstPostedCharges[$chgDetId];
						}
						
						$adj= ($tempDefaultWriteOff[$chgDetId] + $tempWriteOffArr[$chgDetId] + $tempAdjArr[$chgDetId]) + $tempRefArr[$chgDetId];
						$balAmt = $charges - ($tempPaymentArr[$chgDetId] + $adj);
						$dr_tot_beg_ar_arr[$firstGroupBy][$secGroupBy]+= $balAmt;
						
//if($encounter_id==264573){
	if($callFrom=='begining'){
		//echo $chgDetId.' - '.$charges.'-'.$tempPaymentArr[$chgDetId].'-'.$adj.' - '.$tempRefArr[$chgDetId].'<br>';
		$arr1[$encounter_id]+=$balAmt;	
//		$n[$encounter_id]+=$charges;	
	}
//}
					}
				}
			}

foreach($arr1 as $eid =>$val){
	if($val>=1 || $val<=-1){
	//	echo $eid.'-'.round($val,2).'<br>';
	}
}
			unset($tempDefaultWriteOff);
			unset($tempWriteOffArr);
			unset($arrPrevDateWriteOff);
			unset($tempChgDet);
			unset($tempPaymentArr);
			unset($tempAdjArr);
			unset($tempRefArr);

			// DEBIT CI/CO AND PRE PAY UNAPPLIED AMOUNTS
			$cioBalance= $prePayBalance = 0;
			$firstGroupBy = $provId;
			$secGroupBy = $facId;
			if($groupBy=='grpby_facility'){
				$firstGroupBy = $facId;
				$secGroupBy = $provId;
			}
			
			$strAllEncounters = implode(',', $arrAllEncounters);
			$dr_tot_beg_ar_arr['beg_enc']['beg_enc']=$strAllEncounters;
			
			return $dr_tot_beg_ar_arr;
			
		} // END ENDING A/R ------------------------------------------------
		

		$dr_tot_beg_ar_arr=getPreviousBal('begining', $startDate, $endDate,$prevDate, $Physician, $sc_name, $groupBy, $grp_id, $reptByForFun,$department,$operator_id,$insCompanies);
		$dr_tot_bal_arr=getPreviousBal('ending', $startDate, $endDate,$endDate, $Physician, $sc_name, $groupBy, $grp_id, $reptByForFun,$department,$operator_id,$insCompanies);

	} //END DOT/DOP CHECK

	$strAllBegEncs='';
	//$strAllBegEncs=$dr_tot_beg_ar_arr['beg_enc']['beg_enc'];
	$balWithoutCreditCalculated=$dr_tot_bal_arr['balWithoutCreditCalculated']['balWithoutCreditCalculated'];
	unset($dr_tot_beg_ar_arr['beg_enc']['beg_enc']);
	unset($dr_tot_bal_arr['beg_enc']['beg_enc']);
	unset($dr_tot_beg_ar_arr['balWithoutCreditCalculated']['balWithoutCreditCalculated']);
	unset($dr_tot_bal_arr['balWithoutCreditCalculated']['balWithoutCreditCalculated']);
	// ----END BEG A/R


	//COLLECT PROVIDRS AND PHYSICIANS FROM PREVIOUS BAL FUNCTION TOO
	foreach($dr_tot_beg_ar_arr as $firstGrpId => $grpData){
		foreach($grpData as $secondGrpId => $val){
			$checkInDataArr[$firstGrpId][$secondGrpId]=1;
		}
	}

	foreach($dr_tot_bal_arr  as $firstGrpId => $grpData){
		foreach($grpData as $secondGrpId => $val){
			$checkInDataArr[$firstGrpId][$secondGrpId]=1;
		}
	}
	//-------------------------------------------------------------
	
	// *READ DETAIL*
	//GETTING CHARGES FOR ENCOUNERS WHICH HAVE NOT POSTED. IT HAS BELOW TWO TYPES OF ENCOUNTERS.
	// -ENCOUNTERS WHICH HAVE YET NOT POSTED AND NO ANY ADJUSTMENT AND PAYMENT DONE.
	// -ENCOUNTERS WHIHC HAVDE YET NOT POSTED BUT PAYMENT OR ADJUSEMENT HAS DONE DONE FOR THAT.
	// -QUERY IS DOS BASED
	$chargesForNotPosted='';
	if($DateRangeFor=='dot'){	
		$qry="Select main.patient_id, main.encounter_id, (main.charges * main.units) as totalAmt, main.facility_id, main.primary_provider_id_for_reports  
		FROM report_enc_detail main JOIN patient_data pd ON pd.id=main.patient_id 
		WHERE main.first_posted_date='0000-00-00' AND main.del_status='0' 
		AND (main.date_of_service BETWEEN '$startDate' and '$endDate')";
		if(empty($sc_name) === false){
			$qry .= " AND main.facility_id in($sc_name)";
		}
		if(empty($grp_id) === false){
			$qry .= " AND main.gro_id in($grp_id)";
		}
		if(empty($Physician) === false){
			$qry .= " AND main.primary_provider_id_for_reports in($Physician)";
		}
		if(empty($operator_id) === false){
			$qry.= " AND main.operator_id in($operator_id)";
		}
		if(empty($insCompanies) === false){
			$qry.= " and ( main.pri_ins_id in($insCompanies) 
			OR	main.sec_ins_id in($insCompanies)
			OR	main.tri_ins_id in($insCompanies) )";
		}
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$chargesForNotPosted+=$res['totalAmt'];
		}
	}
	//----------------------------------------------

	//GET DELETED AMOUNTS
	if($DateRangeFor=='dor' || $DateRangeFor=='dot'){
		$tempDefaultWriteOff=array();
		$tempChgDet=array();
		//TRANSACTIONS TABLE
		$qry="Select trans.report_trans_id, trans.parent_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by,
		trans.trans_type, trans.trans_amount, trans.trans_del_operator_id,
		trans.trans_dot, trans.trans_dop, trans.trans_del_date FROM report_enc_trans trans 
		WHERE LOWER(trans_type) NOT IN('charges', 'default_writeoff')
		AND (trans.trans_del_date BETWEEN '$startDate' and '$endDate') AND trans.trans_dot<'$startDate'
		ORDER BY trans.master_tbl_id , trans.trans_dot, trans.trans_dot_time";
		$rs=imw_query($qry);
		while($res = imw_fetch_assoc($rs)){
			$report_trans_id=$res['report_trans_id'];
			$encounter_id= $res['encounter_id'];
			$chgDetId= $res['charge_list_detail_id'];
			$trans_type= strtolower($res['trans_type']);
			$trans_by= strtolower($res['trans_by']);			

			$tempRecordData[$report_trans_id]=$res['trans_amount'];
			$tempChgDet[$chgDetId]=$chgDetId;
			
			switch($trans_type){
				case 'paid':
				case 'copay-paid':
				case 'deposit':
				case 'interest payment':
				case 'negative payment':
				case 'copay-negative payment':
					$paidForProc=0;
					if(($trans_type=='negative payment' || $trans_type=='copay-negative payment')){	
						$tempDelPaymentArr[$chgDetId]+="-".$res['trans_amount'];
					}else{
						$tempDelPaymentArr[$chgDetId]+=$res['trans_amount'];
					}
				break;

				case 'credit':
				case 'debit':
					$crddbtamt=0;
					if($trans_type=='credit'){ 
						$tempDelPaymentArr[$chgDetId]+=$res['trans_amount'];
					}else{
						$tempDelPaymentArr[$chgDetId]+="-".$res['trans_amount'];
					}
				break;

				case 'write off':
				case 'discount':
				case 'over adjustment':
					$tempDelAdjArr[$chgDetId]+=$res['trans_amount'];
				break;

				case 'adjustment':
				case 'returned check':
					$tempDelAdjArr[$chgDetId]+="-".$res['trans_amount'];
				break;
				
				case 'refund':
					$tempDelRefArr[$chgDetId]+="-".$res['trans_amount'];
				break;
			}
		}

		//FINAL DELETED AMOUNTS ARRAY
		$qry="Select main.encounter_id, main.charge_list_detail_id, 
		main.date_of_service, main.gro_id, main.facility_id, main.primary_provider_id_for_reports as 'primaryProviderId', main.over_payment, 
		main.first_posted_date, (main.charges * main.units) as totalAmt, main.del_status, DATE_FORMAT(main.trans_del_date, '%Y-%m-%d') as 'trans_del_date'
		".$strDeptField."
		FROM report_enc_detail main
		".$strDeptJoin." 			
		WHERE 1=1";
		if(sizeof($tempChgDet)>0){
			$tempChgDet_str=implode(',', $tempChgDet);
			$qry.=" AND (main.charge_list_detail_id IN(".$tempChgDet_str.") OR ((DATE_FORMAT(main.trans_del_date, '%Y-%m-%d') BETWEEN '$startDate' and '$endDate') AND main.first_posted_date<'$startDate' AND main.first_posted_date!='0000-00-00'))";
		}else{
			$qry.=" AND (DATE_FORMAT(main.trans_del_date, '%Y-%m-%d') BETWEEN '$startDate' and '$endDate') AND main.first_posted_date<'$startDate' AND main.first_posted_date!='0000-00-00')";
		}
		if(empty($sc_name) === false){
			$qry.= " and main.facility_id in($sc_name)";
		}
		if(empty($grp_id) === false){
			$qry.= " and main.gro_id in($grp_id)";
		}
		if(empty($Physician) === false){
			$qry.= " and main.primary_provider_id_for_reports in($Physician)";
		}
		if(empty($operator_id) === false){
			$qry.= " and main.operator_id in($operator_id)";
		}
		if(empty($department) === false){
			$qry.= " and cpt_fee_tbl.departmentId in($department)";
		}
		if(empty($insCompanies) === false){
			$qry.= " and ( main.pri_ins_id in($insCompanies) 
			OR	main.sec_ins_id in($insCompanies)
			OR	main.tri_ins_id in($insCompanies) )";
		}
		//echo $qry.= " ORDER BY ";				
		$rs=imw_query($qry);
		while($res = imw_fetch_assoc($rs)){
			$chgDetId= $res['charge_list_detail_id'];
			$doctor_id = $res['primaryProviderId'];
			$facility_id = $res['facility_id'];
			$group_id = $begRes['gro_id'];
			$opr_id = $begRes['operator_id'];
			$dept_id = isset($begRes['departmentId']) ? $begRes['departmentId'] : 0;

			$firstGroupBy = $doctor_id;
			$secGroupBy = $facility_id;
			if($groupBy=='grpby_facility'){
				$firstGroupBy = $facility_id;
				$secGroupBy = $doctor_id;
			}elseif($groupBy=='grpby_groups'){
				$firstGroupBy = $group_id;
				$secGroupBy = $doctor_id;
			}elseif($groupBy=='grpby_operators'){
				$firstGroupBy = $opr_id;
				$secGroupBy = $facility_id;
			}elseif($groupBy=='grpby_department'){
				$firstGroupBy = $dept_id;
				$secGroupBy = $doctor_id;
			}
			
			$checkInDataArr[$firstGroupBy][$secGroupBy]=1;

			//DELETED AMOUNTS
			if(($res['del_status']=='1' && $res['trans_del_date']>=$startDate && $res['trans_del_date']<=$endDate && $res['first_posted_date']<$startDate)){
				$del_amounts_arr[$firstGroupBy][$secGroupBy]['CHARGES']+= $res['totalAmt'];						
			}
			if($tempDelPaymentArr[$chgDetId]){
				$del_amounts_arr[$firstGroupBy][$secGroupBy]['PAYMENT']+= $tempDelPaymentArr[$chgDetId];
			}
			if($tempDelAdjArr[$chgDetId]){
				$del_amounts_arr[$firstGroupBy][$secGroupBy]['ADJUSTMENT']+= $tempDelAdjArr[$chgDetId];
			}
			if($tempDelRefArr[$chgDetId]){
				$del_amounts_arr[$firstGroupBy][$secGroupBy]['ADJUSTMENT']+= $tempDelRefArr[$chgDetId];
			}
		}
		unset($rs);	
		unset($tempDelPaymentArr);
		unset($tempDelAdjArr);
		unset($tempDelRefArr);
		//-----------------------
	}

	// GET NOT APPLIED CI/CO for selected month
	$arrCICONotApplied=array();
	$schFacId='';
	if(empty($sc_name)==false){
		$arr_sc_name=explode(',', $sc_name);
		$arrSchFacId=array();
		for($i=0; $i<sizeof($arr_sc_name); $i++){
			$id=$sch_fac_arr[$arr_sc_name[$i]];
			if($id>0 && $id!=''){
				$arrSchFacId[$id] = $id;
			}
		}
		$arrSchFacId=array_unique($arrSchFacId);
		$schFacId = implode(',', $arrSchFacId);
	}

	$qry="Select sa.sa_facility_id, sa.sa_doctor_id, cioPay.payment_id, cioPay.total_payment, cioPay.created_on    
	FROM schedule_appointments sa 
	JOIN check_in_out_payment cioPay ON cioPay.sch_id  = sa.id 
	LEFT JOIN users ON users.id = sa.sa_doctor_id 
	WHERE cioPay.total_payment>0 AND (cioPay.created_on BETWEEN '2016-01-01' AND '".$endDate."') AND cioPay.del_status=0 ";
	if(empty($schFacId) ===false){
		$qry.= " AND sa.sa_facility_id IN(".$schFacId.")";
	}
	if(empty($Physician) === false){
		$qry.= " and sa.sa_doctor_id IN(".$Physician.")";
	}
	$qry.= " ORDER BY users.lname, users.fname";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$payment_id = $res['payment_id'];
		$created_on = $res['created_on'];
		$doc_id = $res['sa_doctor_id'];
		$sch_facility = $res['sa_facility_id'];
		$facility = $sch_pos_fac_arr[$sch_facility];
		if($facility=='' || $facility<=0) { $facility=0; }

		$firstGroupBy = $doc_id;
		$secGroupBy = $sch_facility;
		if($groupBy=='grpby_facility'){
			$firstGroupBy = $sch_facility;
			$secGroupBy = $doc_id;
		}
		$tt+=$res['total_payment'];
		$tempCIOArr[$payment_id]['payment']+= $res['total_payment'];
		$tempCIOArr[$payment_id]['created_on']= $res['created_on'];
		$tempCIOArr[$payment_id]['firstGroupBy']= $firstGroupBy;
		$arrGrpCICONotApplied[$firstGroupBy]=$firstGroupBy;
		
	}

	$splitted_encounters=array();
	if(sizeof($tempCIOArr)>0){
		$arrPayIds=array_keys($tempCIOArr);
		$splitted_encounters = array_chunk($arrPayIds,4000);
		$tempCIOPaid=array();
		foreach($splitted_encounters as $arr){
			$str_splitted_encs 	 = implode(',',$arr);
			$arr_acc_payment_id=array();
			$temp_acc_payment_id=array();
			
			$qry="Select cioPost.check_in_out_payment_id, cioPost.manually_payment, cioPost.acc_payment_id, cioPost.manually_date FROM check_in_out_payment_post cioPost 
			WHERE cioPost.check_in_out_payment_id IN(".$str_splitted_encs.") AND cioPost.status='0'";
			$rs=imw_query($qry);
			while($res=imw_fetch_array($rs)){
				$payment_id = $res['check_in_out_payment_id'];
				
				if($res['manually_payment']>0){
					$tempCIOPaid[$payment_id]+=$res['manually_payment'];
				}
				
				if($res['acc_payment_id']>0){ 
					$arr_acc_payment_id[$res['acc_payment_id']]=$res['acc_payment_id'];
					$temp_acc_payment_id[$res['acc_payment_id']] = $res['check_in_out_payment_id'];
				}
			}unset($rs);
			if(sizeof($arr_acc_payment_id)>0){
				$str_acc_payment_id = implode(',', $arr_acc_payment_id);
				
				$qry="Select patPay.payment_id, patPayDet.paidForProc FROM 
				patient_chargesheet_payment_info patPay 
				LEFT JOIN patient_charges_detail_payment_info patPayDet ON patPayDet.payment_id = patPay.payment_id 
				WHERE patPay.payment_id IN(".$str_acc_payment_id.") AND patPayDet.deletePayment='0' AND patPayDet.unapply='0'";
				$rs=imw_query($qry);
				while($res=imw_fetch_array($rs)){
					$payment_id = $temp_acc_payment_id[$res['payment_id']];

					$tempCIOPaid[$payment_id]+=$res['paidForProc'];
				}unset($rs);
			}
		}
		foreach($tempCIOArr as $payment_id => $cioData){
			$cioPayment = $cioData['payment'];
			$created_on = $cioData['created_on'];
			$firstGroupBy = $cioData['firstGroupBy'];
			if($tempCIOPaid[$payment_id]>0){
				$cioPayment = $cioPayment - $tempCIOPaid[$payment_id];
			}

			if($cioPayment!=0 && $cioPayment!=''){
				if($created_on<$startDate){
					$arrCICONotAppliedFirst[$firstGroupBy]+=$cioPayment;
				}
				else if($created_on>=$startDate && $created_on<=$endDate){
					$arrCICONotAppliedSec[$firstGroupBy]+=$cioPayment;
				}
				
				$tempForRefund[$payment_id]['first_group_id']= $firstGroupBy;
				$tempForRefund[$payment_id]['created_on']= $created_on;
			}
		}
		
		if(sizeof($tempForRefund)>0){
			$strForRefund = implode(',', array_keys($tempForRefund));
			$qry="Select id, payment_id FROM check_in_out_payment_details WHERE payment_id IN(".$strForRefund.")";
			$rs=imw_query($qry);
			while($res=imw_fetch_array($rs)){
				$payment_det_id = $res['id'];
				$payment_id = $res['payment_id'];
				$created_on = $tempForRefund[$payment_id]['created_on'];
				
				if($created_on<$startDate){
					$arrCicoForRefundFirst[$payment_det_id] = $payment_det_id;
				}else if($created_on>=$startDate && $created_on<=$endDate){
					$arrCicoForRefundSec[$payment_det_id] = $payment_det_id;
				}
				
				$arrCicoIdForRefund[$payment_det_id] = $tempForRefund[$payment_id]['first_group_id'];									
			}unset($rs);
		}
	}
	//----------	

	// GET PATIENT PRE PAYMENTS
	$patQry="Select pDep.id, pDep.paid_amount, pDep.facility_id, 
	pDep.apply_payment_date, pDep.provider_id, pDep.apply_payment_type, pDep.apply_amount, pDep.entered_date   
	FROM patient_pre_payment pDep 
	LEFT JOIN patient_data pData ON pData.id = pDep.patient_id 
	LEFT JOIN users ON users.id = pDep.provider_id
	WHERE pDep.del_status='0'";
	if($reptByForFun=='dot'){
		$patQry.=" AND (pDep.entered_date between '2016-01-01' and '".$endDate."')";
	}else{
		$patQry.=" AND (pDep.paid_date between '2016-01-01' and '".$endDate."')";
	}
	if(empty($schFacId) ===false){
		$patQry.= " AND pDep.facility_id IN(".$schFacId.")";
	}
	if(empty($Physician) === false){
		$patQry.= " and pDep.provider_id IN(".$Physician.")";
	} 
	$patQry.=" ORDER BY users.lname, users.fname";
		
	$patQryRs = imw_query($patQry);

	$arrDepIds=array();
	$tempData=array(); $arrDepIds=array(); $arrAllIds=array(); $arrAllIdsData=array();
	while($patQryRes =imw_fetch_array($patQryRs)){	
		
		$facility=0; $balance_amount=0; $doc_id=0;
		
		$facility=$patQryRes['facility_id'];
		
		$id= $patQryRes['id'];
		$doc_id = $patQryRes['provider_id'];
		$balance_amount=$patQryRes['paid_amount'];

		if($patQryRes['apply_payment_type']=='manually'){
			$balance_amount-=$patQryRes['apply_amount'];
		}
		
		if($balance_amount>0){
			$tempData[$id]['PAT_DEPOSIT']=$patQryRes['paid_amount'];
			if($patQryRes[$i]['apply_payment_type']=='manually'){
				$tempData[$id]['APPLIED_AMT']+= $patQryRes['apply_amount'];
			}
			if($patQryRes['apply_payment_date']!='0000-00-00'){
				$arrDepIds[$id]=$id;	
			}
			$arrAllIds[$id]=$id;
			$arrAllIdsData[$id]['Doctor']=$doc_id;
			$arrAllIdsData[$id]['Facility']=$facility;
			$arrAllIdsData[$id]['Entered_Date']=$patQryRes['entered_date'];
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
	$arrPrePayNotApplied=array();
	foreach($arrAllIds as $id){
		$balance_amount=$tempData[$id]['PAT_DEPOSIT']-$tempData[$id]['APPLIED_AMT'];
		if($balance_amount>0){
			$doc_id= $arrAllIdsData[$id]['Doctor'];
			$fac_id= $arrAllIdsData[$id]['Facility'];
			$enteredDate = $arrAllIdsData[$id]['Entered_Date'];

			$firstGroupBy = $doc_id;
			$secGroupBy = $fac_id;
			if($groupBy=='grpby_facility'){
				$firstGroupBy = $fac_id;
				$secGroupBy = $doc_id;
			}
			
			if($enteredDate<$startDate){
				$arrPrePayNotAppliedFirst[$firstGroupBy]+=$balance_amount;
				$arrPrePayForRefundFirst[$id] = $id;
			}
			else if($enteredDate>=$startDate && $enteredDate<=$endDate){
				$arrPrePayNotAppliedSec[$firstGroupBy]+=$balance_amount;
				$arrPrePayForRefundSec[$id] = $id;
			}
			$arrGrpPrePayNotApplied[$firstGroupBy]=$firstGroupBy;
			
			$arrPrePayIdForRefund[$id] = $firstGroupBy;
		}
	}
	
	//CI/CO AND PRE-PAYMENT REFUND AMOUNTS
	if(sizeof($arrCicoIdForRefund)>0 || sizeof($arrPrePayIdForRefund)>0){
		$qryPart ='';
		if(sizeof($arrCicoIdForRefund)>0){
			$strCicoIdForRefund = implode(',', array_keys($arrCicoIdForRefund));
			$qryPart=" ci_co_id IN(".$strCicoIdForRefund.")";
		}
		if(sizeof($arrPrePayIdForRefund)>0){
			$strPrePayIdForRefund = implode(',', array_keys($arrPrePayIdForRefund));
			if(sizeof($arrCicoIdForRefund)>0){
				$qryPart.=" OR pmt_id IN(".$strPrePayIdForRefund.")";	
			}else{
				$qryPart.=" pmt_id IN(".$strPrePayIdForRefund.")";					
			}
		}
		
		#############################################################
		#query to get refund detail for pre payments and ci/co if any
		#############################################################
		$qry="Select ci_co_id, pmt_id, ref_amt FROM ci_pmt_ref WHERE (".$qryPart.") AND del_status='0' AND (entered_date BETWEEN '2016-01-01' AND '".$endDate."')";
		$rs=imw_query($qry);
		while($res= imw_fetch_array($rs)){
			if($res['ci_co_id']>0){
				$firstGroupBy = $arrCicoIdForRefund[$res['ci_co_id']];
				if($arrCicoForRefundFirst[$res['ci_co_id']]){
					$arrCicoRefundFirst[$firstGroupBy]+= $res['ref_amt'];
				}else if($arrCicoForRefundSec[$res['ci_co_id']]){
					$arrCicoRefundSec[$firstGroupBy]+= $res['ref_amt'];
				}
			}
			if($res['pmt_id']>0){
				$firstGroupBy = $arrPrePayIdForRefund[$res['pmt_id']];
				if($arrPrePayForRefundFirst[$res['pmt_id']]){				
					$arrPrePayRefundFirst[$firstGroupBy]+= $res['ref_amt'];
				}elseif($arrPrePayForRefundSec[$res['pmt_id']]){				
					$arrPrePayRefundSec[$firstGroupBy]+= $res['ref_amt'];
				}
			}
		}
	}
	//END NOT APPLIED AMOUNTS
	
	

	// REFUND FROM CI/CO AND PRE PAYMENTS
	$qry="Select ciRef.id, ciRef.ci_co_id, ciRef.pmt_id, ciRef.ref_amt, ciRef.payment_method, ciRef.patient_id, 
	DATE_FORMAT(ciRef.entered_date, '".get_sql_date_format()."') as 'entered_date' 
	FROM ci_pmt_ref ciRef WHERE del_status='0' AND (entered_date BETWEEN '$startDate' AND '$endDate')"; 
	$rs=imw_query($qry);
	
	while($res = imw_fetch_array($rs)){
		$id = $res['id'];
		if($res['ci_co_id']>0){
			$tempCICOIds[$res['ci_co_id']] = $res['ci_co_id'];
			$arrCICORefundsDet[$res['ci_co_id']][$id]['ref_amt']= $res['ref_amt'];
			$arrCICORefundsDet[$res['ci_co_id']][$id]['method']= $res['payment_method'];
			$arrCICORefundsDet[$res['ci_co_id']][$id]['ref_date']= $res['entered_date'];
			$tempCICOForSummary[$res['ci_co_id']]+= $res['ref_amt'];
			
		}else if($res['pmt_id']>0){
			$tempPMTIds[$res['pmt_id']] = $res['pmt_id'];
			$arrPMTRefundsDet[$res['pmt_id']][$id]['ref_amt']= $res['ref_amt'];
			$arrPMTRefundsDet[$res['pmt_id']][$id]['method']= $res['payment_method'];
			$arrPMTRefundsDet[$res['pmt_id']][$id]['ref_date']= $res['entered_date'];
			$tempPMTForSummary[$res['pmt_id']]+= $res['ref_amt'];
		}
	}
	unset($rs);
	
	// CI/CO RECORDS
	if(sizeof($tempCICOIds)>0){
		$strCICOIds = implode(',', $tempCICOIds);
		unset($tempCICOIds);
		
		$qry="Select cicoDet.id, cicoDet.item_payment, cicoPay.patient_id, DATE_FORMAT(cicoPay.created_on, '".get_sql_date_format()."') as 'created_on', schAppt.sa_doctor_id, sa_facility_id,
		pd.lname, pd.fname, pd.mname  
		FROM check_in_out_payment_details cicoDet 
		LEFT JOIN check_in_out_payment cicoPay ON cicoPay.payment_id = cicoDet.payment_id 
		LEFT JOIN schedule_appointments schAppt ON schAppt.id = cicoPay.sch_id 
		LEFT JOIN patient_data pd ON pd.id = cicoPay.patient_id  
		WHERE cicoDet.id IN(".$strCICOIds.")";
		if(empty($Physician) == false){
			$qry.=" AND schAppt.sa_doctor_id IN ($Physician)";
		}
		if(empty($schFacId) == false){
			$qry.=" AND schAppt.sa_facility_id IN ($schFacId)";
		}
		$qry.=" ORDER BY pd.lname, pd.fname";
		$rs=imw_query($qry);
		
		while($res = imw_fetch_array($rs)){
			$printFile = true;	
			$CICODetId = $res['id'];
			$pid = $res['patient_id'];
			$phyId = $res['sa_doctor_id'];
			
			$arrMainOthers[$phyId][$pid] = $pid;
			$arrCICORefunds[$phyId][$pid][$CICODetId] = $sch_pos_fac_arr[$res['sa_facility_id']];
			$arrPatDetail[$CICODetId]['fname'] = $res['fname'];
			$arrPatDetail[$CICODetId]['mname'] = $res['mname'];
			$arrPatDetail[$CICODetId]['lname'] = $res['lname'];
			$arrPatDetail[$CICODetId]['pay_date'] = $res['created_on'];
			$arrPatDetail[$CICODetId]['pay_amt']+= $res['item_payment'];
			
			//FOR SUMMARY
			//$arrRefundData[$phyId]['payment']+= $res['item_payment'];
			//$arrRefundData[$phyId]['refund']+= $tempCICOForSummary[$CICODetId];
		}
		unset($rs);
	}
	
	// PRE-PAMENT RECORDS
	if(sizeof($tempPMTIds)>0){
		$strPMTIds = implode(',', $tempPMTIds);
		unset($tempPMTIds);
		
		$qry="Select patPrePay.id, patPrePay.patient_id, patPrePay.patient_id, patPrePay.facility_id, patPrePay.paid_amount,
		DATE_FORMAT(patPrePay.paid_date, '".get_sql_date_format()."') as 'paid_date', patPrePay.provider_id,
		pd.lname, pd.fname, pd.mname FROM patient_pre_payment patPrePay 
		LEFT JOIN patient_data pd ON pd.id = patPrePay.patient_id 
		WHERE patPrePay.id IN(".$strPMTIds.")";
		if(empty($Physician) == false){
			$qry.=" AND patPrePay.provider_id IN ($Physician)";
		}
		if(empty($schFacId) == false){
			$qry.=" AND patPrePay.facility_id IN ($schFacId)";
		}
		$qry.=" ORDER BY pd.lname, pd.fname";
		$rs=imw_query($qry);
		
		while($res= imw_fetch_array($rs)){
			$printFile = true;	
			$pmtId = $res['id'];
			$pid = $res['patient_id'];
			$phyId = $res['provider_id'];
			
			$arrMainOthers[$phyId][$pid] = $pid;
			$arrPMTRefunds[$phyId][$pid][$pmtId] = $sch_pos_fac_arr[$res['facility_id']];
			$arrPatDetail[$pmtId]['fname'] = $res['fname'];
			$arrPatDetail[$pmtId]['mname'] = $res['mname'];
			$arrPatDetail[$pmtId]['lname'] = $res['lname'];
			$arrPatDetail[$pmtId]['pay_date'] = $res['paid_date'];
			$arrPatDetail[$pmtId]['pay_amt']+= $res['paid_amount'];

			//FOR SUMMARY
			//$arrRefundData[$phyId]['payment']+= $res['paid_amount'];
			//$arrRefundData[$phyId]['refund']+= $tempPMTForSummary[$pmtId];
		}
		unset($rs);
	}
	//----END REFUND----------------------------------------
	
	// Collecting Data for Appointment Detail
	$apptDetailsArrFull = $CLSReports->getPatientAppointments('', $startDate, $endDate, $hourFrom, $hourTo, $groupBy, $Physician, $sc_name, $grp_id, $operator_id, $insCompanies);
	$apptDetailsArr = $apptDetailsArrFull['detail'];
	$apptSummaryArr = $apptDetailsArrFull['summary'];
	
	if(count($checkInDataArr) > 0 || sizeof($arrMainOthers)>0){
		$printFile = true;
		
		require_once(dirname(__FILE__)."/account_receivable_result_detail.php");
		if(trim($page_content) != ''){				
			
			//--- PAGE HEADER DATA ---
			$curDate = date(''.$phpDateFormat.' H:i A');
			$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
			$op_name = $op_name_arr[1][0];
			$op_name .= $op_name_arr[0][0];
			
			$groupSelected = $CLSReports->report_display_selected($grp_id,'group',1, $allGrpCount);
			$facilitySelected = $CLSReports->report_display_selected($sc_name,'practice',1, $allFacOptions);
			$doctorSelected = $CLSReports->report_display_selected($Physician,'physician',1, $allPhyOptions);
			$dateModeSelected= strtoupper($reptByForFun);
			

			$tooltip='Red coloured CI/CO and Pre-Payments represents that there is refund amount deducted from these payments.';

			$html_page_content = <<<DATA
				<style type="text/css">
DATA;
			$html_page_content .= file_get_contents("css/reports_pdf.css");
			$html_page_content .= <<<DATA
				</style>
				<page backtop="8mm" backbottom="10mm">			
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
					<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050" >
						<tr class="rpt_headers">
							<td class="rptbx1" style="width:342px;">
								$dbtemp_name $summary_detail Report
							</td>
							
							<td class="rptbx2" style="width:342px;">
								$dateModeSelected ($Start_date - $End_date)
							</td>
							<td class="rptbx3" style="width:342px;">
								Created by: $op_name on $curDate
							</td>
						</tr>	
						<tr class="rpt_headers">
							<td class="rptbx1" >
								Selected Groups: $groupSelected
							</td>
							<td class="rptbx2" >Selected Facility : $facilitySelected</td>
							<td class="rptbx3" >Selected Physician : $doctorSelected</td>
						</tr>					
					</table>
				</page_header>
				$page_content
				<table width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8" width="1050" >
					<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
					<tr>
						<td class="info" style="background-color:#FFFFFF;"></td>
						<td style="width:2px;" height="5px;" bgcolor="#FF0000"></td>
						<td class="info" style="padding-left:20px; ; background-color:#FFFFFF;">
						$tooltip
					</td>
					</tr>
				</table>		
DATA;
			//--- CSV FILE DATA --
			$html_css= '<style>'.file_get_contents("css/reports_html.css").'</style>';
			$page_content = <<<DATA
				$html_css
				<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
					<tr class="rpt_headers">
						<td class="rptbx1" style="width:342px;">
							$dbtemp_name $summary_detail Report
						</td>
						
						<td class="rptbx2" style="width:342px;">
							$dateModeSelected ($Start_date - $End_date)
						</td>
						<td class="rptbx3" style="width:342px;">
							Created by: $op_name on $curDate
						</td>
					</tr>	
					<tr class="rpt_headers">
						<td class="rptbx1" >
							Selected Groups: $groupSelected
						</td>
						<td class="rptbx2" >Selected Facility : $facilitySelected</td>
						<td class="rptbx3" >Selected Physician : $doctorSelected</td>
					</tr>					
				</table>
				$page_content
				<table class="rpt rpt_table rpt_table-bordered" width="1050" >
					<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
					<tr><td class="info" style="background-color:#FFFFFF;"></td>
						<td style="width:2px;" height="5px;" bgcolor="#FF0000"></td>
						<td class="info" style="padding-left:20px; ; background-color:#FFFFFF;">
						$tooltip
						<br>Refund amount can be view by mouse over on red coloured amount.
						</td>
					</tr>
				</table>				
DATA;
			
			$html_file_name = $html_file_name;
			$showBtn = true;
			$csvFileData = $page_content;
			
		}
		
	}

}


$hasData=0;
if($page_content){
	echo $page_content;
	$hasData=1;
	$op = "l";
	$file_location = write_html($html_page_content);
} else{
	echo '<div class="text-center alert alert-info">No Recod Exists.</div>';
}
	

?>
