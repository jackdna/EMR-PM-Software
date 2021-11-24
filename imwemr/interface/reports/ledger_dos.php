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

/*
FILE : productivity_physician_charges.php
PURPOSE : PRODUCTIVITY CHARGES FOR PHYSICIAN
ACCESS TYPE : DIRECT
*/

$fac_join='';
if($billing_location=='1' && $viewBy=='facility'){
	$fac_join= " LEFT JOIN facility on facility.id = main.billing_facility_id";
}else{
	$fac_join= " LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id
				 LEFT JOIN pos_tbl ON pos_tbl.pos_id = pos_facilityies_tbl.pos_id";
}


//IF CHECKVIEW SELECTED
if($reportType=='checkView'){
	$qry_big_part='';

	//GET ALL CHARGES
	if(empty($departmentIds) == false){
		$qry_big_part.= " and department_tbl.departmentId IN ($departmentIds)";	
	}
	if(empty($sc_name) == false){
		if($billing_location=='1'){
			$qry_big_part.= " and main.billing_facility_id IN ($sc_name)";
		}else{
			$qry_big_part.= " and main.facility_id IN ($sc_name)";	
		}
	}
	if(empty($grp_id) == false){
		$qry_big_part.= " and main.gro_id IN ($grp_id)";
	}
	
	if(empty($Physician) === false){
		$qry_big_part.= " and main.primary_provider_id_for_reports IN ($Physician)";
	}
	if(empty($credit_physician) === false){
		$qry_big_part.= " and main.sec_prov_id IN ($credit_physician)";
	}
	if($chksamebillingcredittingproviders==1){
		$qry_big_part.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
	}	
	if(trim($insuranceName) != ''){
		if(trim($ins_type) == ''){
			$qry_big_part.= " and (main.pri_ins_id in ($insuranceName)
				or main.sec_ins_id in ($insuranceName)
				or main.tri_ins_id in ($insuranceName))";
		}
		else{
			$ins_type_arr=explode(',',$ins_type);
			$qry_big_part.= " and (";
			for($i=0;$i<count($ins_type_arr);$i++){
				$ins_nam=$ins_type_arr[$i];
				if(trim($ins_nam)!='Self Pay')
				{
					$mul_or="";
					if($i>0){
						$mul_or=" or ";
					}
				
					$qry_big_part.= " $mul_or main.$ins_nam in ($insuranceName)";
				}
			}
			$qry_big_part.= " )";
		}
	}
	if(empty($operatorName)==false){
		$qry_big_part.= " and main.operator_id in($operatorName)";
	}
	if($pureSelfPay==1)$qry_big_part.= " and main.proc_selfpay=1";	
	
	if(empty($Start_date)==false && empty($End_date)==false){
		if($DateRangeFor=='doc'){
			$qry_big_part.= " AND (main.first_posted_date between '$Start_date' and '$End_date')";
		}else{
			$qry_big_part.= " AND (main.date_of_service between '$Start_date' and '$End_date')";                                            
		}
	}
	
	//GET CHECK CHARGE ENCOUNTERS
	$qry="Select main.encounter_id FROM report_enc_detail main 
	WHERE  main.del_status='0' ".$qry_big_part;	
	$rs=imw_query($qry);
	while($res = imw_fetch_assoc($rs)){
		$tempCheckEnc[$res['encounter_id']]=$res['encounter_id'];
	}
	
	//GETTING PAYMENTS AND ADJUSTMENTS OF CHECKS
	$tempCheckChgIds=array();
	if(sizeof($tempCheckEnc)>0){
		$splitted_encids = array_chunk($tempCheckEnc,2000);
		foreach($splitted_encids as $arr){
			$str_splitted_enc_ids = implode(',',$arr);		

			$qry="Select trans.report_trans_id, trans.patient_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, trans.trans_method, 
			trans.check_number, trans.cc_type, trans.cc_number, trans.parent_id,
			trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_qry_type,
			trans.trans_dot, trans.trans_del_operator_id  
			FROM report_enc_trans trans 
			WHERE LOWER(trans.trans_method) IN('check','eft','money order') AND trans.encounter_id IN(".$str_splitted_enc_ids.") 
			AND trans.trans_type!='charges' $chAmtQryCriteria ORDER BY trans.trans_dot, trans.trans_dot_time, trans.report_trans_id";
			
			$rs=imw_query($qry);
			while($res = imw_fetch_assoc($rs)){
				$report_trans_id=$res['report_trans_id'];
				$encounter_id= $res['encounter_id'];
				$chgDetId= $res['charge_list_detail_id'];
				$insCompId = $res['trans_ins_id'];
				$trans_type= strtolower($res['trans_type']);
				$trans_by= strtolower($res['trans_by']);
				$res['trans_method']= strtolower($res['trans_method']);	
				$check_number= (empty($res['check_number'])==false)? strtolower(trim($res['check_number'])) : '0';
				$tempCheckChgIds[$chgDetId]=$chgDetId;
				$tempRecordDetail[$report_trans_id]['check_no']=$check_number;
				$tempRecordDetail[$report_trans_id]['trans_amt']=$res['trans_amount'];				
				
				$tempRecordData[$report_trans_id]=$res['trans_amount'];	
				$arrCheckNos[$chgDetId][$check_number] = $check_number;				
				$arrTransIds[$report_trans_id] = $report_trans_id;	

				//RECORD FIRST RECORD ID TO SOLVE PROBLEM EVEN IF RECORD IS MODIFIED MORE THAN TWO TIME.
				//THIS IS DONE SO THAT RECORD WILL DISPLAY ONLY LAST MODIFED PAYMENT METHOD IN RESULT.
				//BUT THERE ARE DIFFERENT OPERATORS MODIFIED SAME PAYMENT AND REPORT IS SEARCHED FOR PARTICULAR OPERATOR THEN THIS LOGIC MAY FAIL.
				$record_id=($res['parent_id']>0)? $res['parent_id']: $report_trans_id;
				if($res['parent_id']>0){
					if($tempRecording[$res['parent_id']]>0){
						$tempArrFirstRecordid[$report_trans_id]=$tempArrFirstRecordid[$res['parent_id']];
					}else{
						$tempArrFirstRecordid[$report_trans_id]=$res['parent_id'];
					}
					$tempRecording[$report_trans_id]=$report_trans_id;
				}
				$record_id=($tempArrFirstRecordid[$report_trans_id]>0)? $tempArrFirstRecordid[$report_trans_id]: $record_id;
				//--------------------------------------------							

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
	
						//IF parent_id >0 THEN IT MEANS RECORD IS UPDATED. THEN REMOVE PREVIOUS FETCHED AMOUNT.
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = ($trans_type=='negative payment' || $trans_type=='copay-negative payment') ? $tempRecordData[$res['parent_id']] : "-".$tempRecordData[$res['parent_id']];

							//IF CHECK NUMBER CHANGED THEN REMOVE AMT FROM OLD CHECK NUMBER
							$old_check_no=$tempRecordDetail[$res['parent_id']]['check_no'];
							$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
							if($old_check_no!=$check_number){
								if($trans_by == 'patient' || $trans_by == 'res. party'){
									$patPayCheckDetArr[$old_check_no][$chgDetId]['patPaid']-=$old_trans_amt;
								}else{
									$patPayCheckDetArr[$old_check_no][$chgDetId]['insPaid']-=$old_trans_amt;
								}
								$mainEncounterCheckPayArr[$old_check_no][$chgDetId]-= $old_trans_amt;
								$patPayCheckDetArr[$old_check_no][$chgDetId]['paid'][$record_id]-=$old_trans_amt;
								$prevFetchedAmt=0;
							}
						}
			
						if($trans_by == 'patient' || $trans_by == 'res. party'){
							$patPayCheckDetArr[$check_number][$chgDetId]['patPaid']+= $paidForProc+$prevFetchedAmt;
							$patPayCheckDetArr[$check_number][$chgDetId]['lastDOT'] = $res['trans_dot'];
						}else if($trans_by == 'insurance'){
							if(empty($insuranceName)===true || (empty($insuranceName)===false && $arrInsurance[$insCompId])){	
								$patPayCheckDetArr[$check_number][$chgDetId]['insPaid']+= $paidForProc+$prevFetchedAmt;
								$patPayCheckDetArr[$check_number][$chgDetId]['lastDOT'] = $res['trans_dot'];
							}else{
								$paidForProc=0;
							}
						}
	
						$patPayCheckDetArr[$check_number][$chgDetId]['paid'][$record_id]+= $paidForProc+$prevFetchedAmt;
						$patPayCheckDetArr[$check_number][$chgDetId]['method'][$record_id]= $res['trans_method'];
						$patPayCheckDetArr[$check_number][$chgDetId]['check_num'][$record_id]= $res['check_number'];
						$patPayCheckDetArr[$check_number][$chgDetId]['cc_type'][$record_id]= $res['cc_type'];
						$patPayCheckDetArr[$check_number][$chgDetId]['cc_number'][$record_id]= $res['cc_number'];
						$patPayCheckDetArr[$check_number][$chgDetId]['paid_date'][$record_id] = $res['trans_dop'];
						
						$mainEncounterCheckPayArr[$check_number][$chgDetId]+= $paidForProc+$prevFetchedAmt;
			
					break;
	
					case 'credit':
					case 'debit':
						$crddbtamt=$res['trans_amount'];
						if($trans_type=='credit'){ 
							$crddbtamt= ($res['trans_del_operator_id']>0) ? "-".$res['trans_amount'] : $res['trans_amount'];							
						}else{  //debit
							$crddbtamt= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];				
						}
	
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = ($trans_type=='credit') ? "-".$tempRecordData[$res['parent_id']] : $tempRecordData[$res['parent_id']];

							//IF CHECK NUMBER/PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD CHECK NUMBER
							$old_check_no=$tempRecordDetail[$res['parent_id']]['check_no'];
							$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
							if($old_check_no!=$check_number){
								if($trans_by=='insurance'){
									$patPayCheckDetArr[$old_check_no][$chgDetId]['insPaid']-=$old_trans_amt;
								}else{
									$patPayCheckDetArr[$old_check_no][$chgDetId]['patPaid']-=$old_trans_amt;
								}
								$mainEncounterCheckPayArr[$old_check_no][$chgDetId]-= $old_trans_amt;
								$patPayCheckDetArr[$old_check_no][$chgDetId]['paid'][$record_id]-=$old_trans_amt;
								$prevFetchedAmt=0;
							}							
						}
						
						if($trans_by=='insurance'){
							$patPayCheckDetArr[$check_number][$chgDetId]['insPaid']+= $crddbtamt+$prevFetchedAmt;
							if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['lastDOT'] = $res['trans_dot'];
						}else{
							$patPayCheckDetArr[$check_number][$chgDetId]['patPaid']+= $crddbtamt+$prevFetchedAmt;				
							if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['lastDOT'] = $res['trans_dot'];
						}

						$patPayCheckDetArr[$check_number][$chgDetId]['paid'][$record_id]+= $crddbtamt+$prevFetchedAmt;
						$patPayCheckDetArr[$check_number][$chgDetId]['method'][$record_id]= $res['trans_method'];
						$patPayCheckDetArr[$check_number][$chgDetId]['check_num'][$record_id]= $res['check_number'];
						$patPayCheckDetArr[$check_number][$chgDetId]['cc_type'][$record_id]= $res['cc_type'];
						$patPayCheckDetArr[$check_number][$chgDetId]['cc_number'][$record_id]= $res['cc_number'];
						$patPayCheckDetArr[$check_number][$chgDetId]['paid_date'][$record_id] = $res['trans_dop'];
						
						$mainEncounterCheckPayArr[$check_number][$chgDetId]+= $crddbtamt+$prevFetchedAmt;
						
					break;
					case 'default_writeoff':
						$normalWriteOffCheckAmt[$check_number][$chgDetId]= $res['trans_amount'];
						if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['lastDOT'] = $res['trans_dot'];
					break;
					case 'write off':
					case 'discount':
						if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];
	
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];

							//IF CHECK NUMBER/PAY LOCATION CHANGED THEN REMOVE AMT FROM OLD CHECK NUMBER
							$old_check_no=$tempRecordDetail[$res['parent_id']]['check_no'];
							$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
							if($old_check_no!=$check_number){
								$writte_off_check_arr[$old_check_no][$chgDetId]-=$old_trans_amt;
								$prevFetchedAmt=0;
							}								
						}
						
						$writte_off_check_arr[$check_number][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
						if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['lastDOT'] = $res['trans_dot'];
					break;
					case 'over adjustment':
						if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];
	
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];

							//IF CHECK NUMBER CHANGED THEN REMOVE AMT FROM OLD CHECK NUMBER
							$old_check_no=$tempRecordDetail[$res['parent_id']]['check_no'];
							$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
							if($old_check_no!=$check_number){
								$arrCheckAdjustmentAmt[$old_check_no][$chgDetId]-=$old_trans_amt;
								$prevFetchedAmt=0;
							}								
						}
						
						$arrCheckAdjustmentAmt[$check_number][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
						if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['lastDOT'] = $res['trans_dot'];
					break;
					case 'adjustment':
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
	
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];							

							//IF CHECK NUMBER CHANGED THEN REMOVE AMT FROM OLD CHECK NUMBER
							$old_check_no=$tempRecordDetail[$res['parent_id']]['check_no'];
							$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
							if($old_check_no!=$check_number){
								$arrCheckAdjustmentAmt[$old_check_no][$chgDetId]-=$old_trans_amt;
								$prevFetchedAmt=0;
							}															
						}

						$arrCheckAdjustmentAmt[$check_number][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
						if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['lastDOT'] = $res['trans_dot'];
					break;
					case 'returned check':
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
	
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = $tempRecordData[$res['parent_id']];

							//IF CHECK NUMBER CHANGED THEN REMOVE AMT FROM OLD CHECK NUMBER
							$old_check_no=$tempRecordDetail[$res['parent_id']]['check_no'];
							$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
							if($old_check_no!=$check_number){
								$arrCheckAdjustmentAmt[$old_check_no][$chgDetId]-=$old_trans_amt;
								$prevFetchedAmt=0;
							}								
						}
						
						$arrCheckAdjustmentAmt[$check_number][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
						if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['lastDOT'] = $res['trans_dot'];
					break;
					case 'refund':
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
	
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = $tempRecordData[$res['parent_id']];

							//IF CHECK NUMBER CHANGED THEN REMOVE AMT FROM OLD CHECK NUMBER
							$old_check_no=$tempRecordDetail[$res['parent_id']]['check_no'];
							$old_trans_amt=$tempRecordDetail[$res['parent_id']]['trans_amt'];
							if($old_check_no!=$check_number){
								$arrCheckAdjustmentAmt[$old_check_no][$chgDetId]-=$old_trans_amt;
								$prevFetchedAmt=0;
							}							
						}
						
						$arrCheckAdjustmentAmt[$check_number][$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
						if($res['trans_del_operator_id']<=0)$patPayCheckDetArr[$check_number][$chgDetId]['lastDOT'] = $res['trans_dot'];
					break;
				}
			}
		}
	}
	unset($tempRecording);
	unset($tempArrFirstRecordid);


	//GETTING CHARGES BASED ON CHECK TRANSACTIONS
	if(sizeof($tempCheckChgIds)>0){
		$splitted_chgids = array_chunk($tempCheckChgIds,2000);
		foreach($splitted_chgids as $arr){
			$str_splitted_chg_ids = implode(',',$arr);		
	
			$qry= "Select main.charge_list_id, main.encounter_id, main.patient_id, main.charge_list_detail_id, main.pri_ins_id,	main.sec_ins_id, main.tri_ins_id, main.operator_id,
			(main.charges * main.units) as totalAmt, main.units, date_format(main.date_of_service,'".$dateFormat."') as date_of_service,
			date_format(main.first_posted_date,'".$dateFormat."') as 'first_posted_date_formatted', date_format(main.entered_date,'".$dateFormat."') as entered_date,
			main.primary_provider_id_for_reports  as 'primaryProviderId', main.sec_prov_id,	main.facility_id, main.billing_facility_id, 
			main.proc_code_id, main.dx_id1, main.dx_id2, main.dx_id3, main.dx_id4, main.mod_id1,  main.mod_id2,  main.mod_id3,  main.mod_id4, 
			main.proc_balance,	main.pri_due, main.sec_due, main.tri_due, main.pat_due, main.over_payment,
			users.lname as physicianLname,users.fname as physicianFname, users.mname as physicianMname, main.submitted, main.re_submitted, main.re_submitted_date,
			patient_data.id as patient_id,
			patient_data.lname,	patient_data.fname, patient_data.mname, patient_data.default_facility,
			main.write_off, cpt_fee_tbl.cpt_prac_code, cpt_fee_tbl.cpt4_code, cpt_fee_tbl.departmentId , cpt_fee_tbl.cpt_desc 
			FROM report_enc_detail main 
			JOIN patient_data on patient_data.id = main.patient_id 
			JOIN users on users.id = main.primary_provider_id_for_reports 
			".$fac_join."
			JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = main.proc_code_id
			LEFT JOIN department_tbl on department_tbl.departmentId = cpt_fee_tbl.departmentId 
			WHERE main.charge_list_detail_id IN(".$str_splitted_chg_ids.")";

			if($viewBy=='facility'){
				if($billing_location=='1'){
					$qry.= " ORDER BY facility.name, main.date_of_service, patient_data.lname,patient_data.fname,main.encounter_id";
				}else{
					$qry.= " ORDER BY pos_facilityies_tbl.facilityPracCode,
					main.date_of_service, patient_data.lname,patient_data.fname,main.encounter_id";
				}
			}else{
				$qry.= " ORDER BY users.lname,users.fname, pos_facilityies_tbl.facilityPracCode,
				main.date_of_service, patient_data.lname,patient_data.fname,main.encounter_id";
			}
			
			$res=imw_query($qry);
			
			//--- PRIMARY PROVIDER ID ARRAY ----
			$arr_selected_phy = array();
			if(empty($Physician) === false){
				$arr_selected_phy = preg_split("/,/", $Physician);
			}
			
			$main_encounter_id_arr = array();
			$facilityNameArr = array();
			$physician_initial_arr = array();
			$arrPatNoFacility=array();
			while($rs = imw_fetch_assoc($res)){
				$encounter_id = $rs['encounter_id'];
				$main_encounter_id_arr[$encounter_id] = $encounter_id;
				$doctor_id = $rs['primaryProviderId'];
				$operator_id = $rs['operator_id'];
				$deptId= $arrDeptOfCptCodes[$rs["proc_code_id"]];
				$pos_facility_id = ($billing_location=='1') ? $rs['billing_facility_id'] : $rs['facility_id'];	
				$pos_facility_id = (empty($pos_facility_id)==true) ? '0' : $pos_facility_id;
				$charge_list_detail_id = $rs['charge_list_detail_id'];
				$submitted= $rs["submitted"];
				$re_submitted=$rs["re_submitted"];
				$re_submitted_date=$rs["re_submitted_date"];
				
				if(empty($Physician) == true && empty($credit_physician) === false){
					$doctor_id=$rs['sec_prov_id'];
				}				
				
				$firstGrpBy= $doctor_id;
				$secGrpBy = $pos_facility_id;
				if($viewBy=='facility'){
					$firstGrpBy= $pos_facility_id;
					$secGrpBy = $doctor_id;
				}
			
				$displayInResub  =false;
				$displayInSub =false;
				if($DateRangeFor!='date_of_service' && $re_submitted == 'true' && $submitted == 'true' && ($re_submitted_date>= $postedFrom && $re_submitted_date <= $postedTo)){
					$displayInResub = true;
				}else if($DateRangeFor!='date_of_service' && $submitted == 'true'){
					$displayInSub = true;
				}			
				
				if($processReport == "Summary"){
			
					if(($re_submitted == 'true' && $submitted == 'true' && $DateRangeFor=='date_of_service')
					|| ($DateRangeFor!='date_of_service' && $displayInResub==true)){ 
						foreach($arrCheckNos[$charge_list_detail_id] as $check_number){						
							$mainResCheckArr['re_posted_charges'][$check_number][$encounter_id][] = $rs;
						}
			
						$main_encounter_id_arr[$encounter_id] = $encounter_id;
						$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
					
					}else if($submitted == 'false' || $submitted == false){ //not-posted
						foreach($arrCheckNos[$charge_list_detail_id] as $check_number){						
							$mainResCheckArr['not_posted_charges'][$check_number][$encounter_id][] = $rs;
						}
						
			
						$main_encounter_id_arr[$encounter_id] = $encounter_id;
						$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
					
					}else if(($submitted == 'true' && $re_submitted == 'false' && $DateRangeFor=='date_of_service')
					|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'false' && !in_array($encounter_id, $arrAllResubEncs)) 
					|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'true' && $displayInSub==true)){//re-submitted 
						foreach($arrCheckNos[$charge_list_detail_id] as $check_number){						
							$mainResCheckArr['posted_charges'][$check_number][$encounter_id][] = $rs;
						}

						$main_encounter_id_arr[$encounter_id] = $encounter_id;
						$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
					}
					
				}
				else{		
					//---- GET SECONDARY PHYSICIAN INITIAL ------
					$pro_id = $doctor_id;
			
					$provider_name_arr = preg_split('/, /',$providerNameArr[$pro_id]);
					$physician_initial = $provider_name_arr[1][0];
					$physician_initial .= $provider_name_arr[0][0];
					$rs['physician_initial'] = strtoupper($physician_initial);
			
			
					if(($re_submitted == 'true' && $submitted == 'true' && $DateRangeFor=='date_of_service')
					|| ($DateRangeFor!='date_of_service' && $displayInResub==true)){ //re-submitted 
						foreach($arrCheckNos[$charge_list_detail_id] as $check_number){
							$mainResCheckArr['re_posted_charges'][$firstGrpBy][$check_number][$encounter_id][] = $rs;
						}
						
						$main_encounter_id_arr[$encounter_id] = $encounter_id;
						$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
					
					}else if($submitted == 'false' || $submitted == false){ //not-posted
						foreach($arrCheckNos[$charge_list_detail_id] as $check_number){
							$mainResCheckArr['not_posted_charges'][$firstGrpBy][$check_number][$encounter_id][] = $rs;
						}
						$main_encounter_id_arr[$encounter_id] = $encounter_id;
						$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
					
					}else if(($submitted == 'true' && $re_submitted == 'false' && $DateRangeFor=='date_of_service')
					|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'false' && !in_array($encounter_id, $arrAllResubEncs)) 
					|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'true' && $displayInSub==true)){//posted
			
						foreach($arrCheckNos[$charge_list_detail_id] as $check_number){
							$mainResCheckArr['posted_charges'][$firstGrpBy][$check_number][$encounter_id][] = $rs;
						}
			
						$main_encounter_id_arr[$encounter_id] = $encounter_id;
						$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
					}
				}
			}unset($rs);
		}
	}
}

//NORMAL VIEW
//GET ALL CHARGES
$qry = "Select main.charge_list_id, main.encounter_id, main.patient_id, main.charge_list_detail_id, main.pri_ins_id,	main.sec_ins_id, main.tri_ins_id, main.operator_id,
		(main.charges * main.units) as totalAmt, main.units, date_format(main.date_of_service,'".$dateFormat."') as date_of_service,
		date_format(main.first_posted_date,'".$dateFormat."') as 'first_posted_date_formatted', date_format(main.entered_date,'".$dateFormat."') as entered_date,
		main.primary_provider_id_for_reports  as 'primaryProviderId', main.sec_prov_id,	
		main.proc_code_id, main.dx_id1, main.dx_id2, main.dx_id3, main.dx_id4, main.mod_id1,  main.mod_id2,  main.mod_id3,  main.mod_id4, 
		main.proc_balance,	main.pri_due, main.sec_due, main.tri_due, main.pat_due, main.over_payment, main.facility_id, main.billing_facility_id, 
		users.lname as physicianLname,users.fname as physicianFname, users.mname as physicianMname, main.submitted, main.re_submitted, main.re_submitted_date,
		patient_data.id as patient_id,
		patient_data.lname,	patient_data.fname, patient_data.mname, patient_data.default_facility,
		main.write_off, cpt_fee_tbl.cpt_prac_code, cpt_fee_tbl.cpt4_code, cpt_fee_tbl.departmentId, cpt_fee_tbl.cpt_desc
		FROM report_enc_detail main 
		JOIN patient_data on patient_data.id = main.patient_id 
		JOIN users on users.id = main.primary_provider_id_for_reports 
		".$fac_join."
		JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = main.proc_code_id
		LEFT JOIN department_tbl on department_tbl.departmentId = cpt_fee_tbl.departmentId 
		WHERE main.del_status='0'";

if(empty($Start_date)==false && empty($End_date)==false){
	if($DateRangeFor=='doc'){
		$qry.= " AND (main.first_posted_date between '$Start_date' and '$End_date')";
	}else{
		$qry.= " AND (main.date_of_service between '$Start_date' and '$End_date')";                                            
	}	
}
if(empty($departmentIds) == false){
	$qry.= " and department_tbl.departmentId IN ($departmentIds)";	
}
if(empty($sc_name)==false){
	if($billing_location=='1'){
		$qry.= " and main.billing_facility_id IN ($sc_name)";
	}else{
		$qry.= " and main.facility_id IN ($sc_name)";	
	}
}
if(empty($grp_id) == false){
	$qry.= " and main.gro_id IN ($grp_id)";
}

if(empty($Physician) === false){
	$qry.= " and main.primary_provider_id_for_reports IN ($Physician)";
}
if(empty($credit_physician) === false){
	$qry.= " and main.sec_prov_id IN ($credit_physician)";
}
if($chksamebillingcredittingproviders==1){
	$qry.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
}


if(trim($insuranceName) != ''){
	if(trim($ins_type) == ''){
		$qry.= " and (main.pri_ins_id in ($insuranceName)
			or main.sec_ins_id in ($insuranceName)
			or main.tri_ins_id in ($insuranceName))";
	}
	else{
		$ins_type_arr=explode(',',$ins_type);
		$qry.= " and (";
		for($i=0;$i<count($ins_type_arr);$i++){
			$ins_nam=$ins_type_arr[$i];
			if(trim($ins_nam)!='Self Pay')
			{
				$mul_or="";
				if($i>0){
					$mul_or=" or ";
				}
			
				$qry.= " $mul_or main.$ins_nam in ($insuranceName)";
			}
		}
		$qry.= " )";
	}
}

if(empty($operatorName)==false){
	$qry.= " and main.operator_id in($operatorName)";
}

if(empty($strChSearchEnc) == false){
	$qry.= " and main.encounter_id IN ($strChSearchEnc)";	
}

if($pureSelfPay==1)$qry.= " and main.proc_selfpay=1";

if($viewBy=='facility'){
	if($billing_location=='1'){
		$qry.= " ORDER BY facility.name, main.date_of_service, patient_data.lname,patient_data.fname,main.encounter_id";
	}else{
		$qry.= " ORDER BY pos_facilityies_tbl.facilityPracCode,
		main.date_of_service, patient_data.lname,patient_data.fname,main.encounter_id";
	}
}else{
	$qry.= " ORDER BY users.lname,users.fname, pos_facilityies_tbl.facilityPracCode,
	main.date_of_service, patient_data.lname,patient_data.fname,main.encounter_id";
}
$res=imw_query($qry);
//--- PRIMARY PROVIDER ID ARRAY ----
$arr_selected_phy = array();
if(empty($Physician) === false){
	$arr_selected_phy = preg_split("/,/", $Physician);
}

$main_encounter_id_arr = array();
$facilityNameArr = array();
$physician_initial_arr = array();
$arrPatNoFacility=array();
while($rs = imw_fetch_assoc($res)){
	$encounter_id = $rs['encounter_id'];
	$main_encounter_id_arr[$encounter_id] = $encounter_id;
	$doctor_id = $rs['primaryProviderId'];
	$operator_id = $rs['operator_id'];
	$deptId= $arrDeptOfCptCodes[$rs["proc_code_id"]];
	$pos_facility_id = ($billing_location=='1') ? $rs['billing_facility_id'] : $rs['facility_id'];		
	$pos_facility_id = (empty($pos_facility_id)==true) ? '0' : $pos_facility_id;
	$chgDetId = $rs['charge_list_detail_id'];
	$submitted= $rs["submitted"];
	$re_submitted=$rs["re_submitted"];
	$re_submitted_date=$rs["re_submitted_date"];

	$arrChgDetIds[$chgDetId] = $chgDetId;

	if(empty($Physician) == true && empty($credit_physician) === false){
		$doctor_id=$rs['sec_prov_id'];
	}
		
	$firstGrpBy= $doctor_id;
	$secGrpBy = $pos_facility_id;
	if($viewBy=='facility'){
		$firstGrpBy= $pos_facility_id;
		$secGrpBy = $doctor_id;
	}

	$displayInResub  =false;
	$displayInSub =false;
	if($DateRangeFor!='date_of_service' && $re_submitted == 'true' && $submitted == 'true' && ($re_submitted_date>= $postedFrom && $re_submitted_date <= $postedTo)){
		$displayInResub = true;
	}else if($DateRangeFor!='date_of_service' && $submitted == 'true'){
		$displayInSub = true;
	}			
	
	if($processReport == "Summary"){

		if(($re_submitted == 'true' && $submitted == 'true' && $DateRangeFor=='date_of_service')
		|| ($DateRangeFor!='date_of_service' && $displayInResub==true)){ 
			$mainResArr['re_posted_charges'][$firstGrpBy][$encounter_id][] = $rs;

			$main_encounter_id_arr[$encounter_id] = $encounter_id;
			$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
		
		}else if($submitted == 'false' || $submitted == false){ //not-posted
			$mainResArr['not_posted_charges'][$firstGrpBy][$encounter_id][] = $rs;

			$main_encounter_id_arr[$encounter_id] = $encounter_id;
			$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
		
		}else if(($submitted == 'true' && $re_submitted == 'false' && $DateRangeFor=='date_of_service')
		|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'false' && !in_array($encounter_id, $arrAllResubEncs)) 
		|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'true' && $displayInSub==true)){//re-submitted 
		
			$mainResArr['posted_charges'][$firstGrpBy][$encounter_id][] = $rs;

			$main_encounter_id_arr[$encounter_id] = $encounter_id;
			$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
		}
		
	}
	else{		
		//---- GET SECONDARY PHYSICIAN INITIAL ------
		$pro_id = $doctor_id;

		$provider_name_arr = preg_split('/, /',$providerNameArr[$pro_id]);
		$physician_initial = $provider_name_arr[1][0];
		$physician_initial .= $provider_name_arr[0][0];
		$rs['physician_initial'] = strtoupper($physician_initial);


		if(($re_submitted == 'true' && $submitted == 'true' && $DateRangeFor=='date_of_service')
		|| ($DateRangeFor!='date_of_service' && $displayInResub==true)){ //re-submitted 
			$mainResArr['re_posted_charges'][$firstGrpBy][$encounter_id][] = $rs;

			$main_encounter_id_arr[$encounter_id] = $encounter_id;
			$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
		
		}else if($submitted == 'false' || $submitted == false){ //not-posted
			$mainResArr['not_posted_charges'][$firstGrpBy][$encounter_id][] = $rs;

			$main_encounter_id_arr[$encounter_id] = $encounter_id;
			$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
		
		}else if(($submitted == 'true' && $re_submitted == 'false' && $DateRangeFor=='date_of_service')
		|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'false' && !in_array($encounter_id, $arrAllResubEncs)) 
		|| ($DateRangeFor!='date_of_service' && $submitted == 'true' && $re_submitted == 'true' && $displayInSub==true)){//posted

			$mainResArr['posted_charges'][$firstGrpBy][$encounter_id][] = $rs;

			$main_encounter_id_arr[$encounter_id] = $encounter_id;
			$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
		}
	}
}unset($rs);


//TRANSACTIONS TABLE
if(sizeof($arrChgDetIds)>0){
	$splitted_chgids = array_chunk($arrChgDetIds,2000);

	foreach($splitted_chgids as $arr){
		$str_splitted_chg_ids = implode(',',$arr);

		$qry="Select trans.report_trans_id, trans.patient_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, trans.trans_method, 
		trans.check_number, trans.cc_type, trans.cc_number, trans.parent_id,
		trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_qry_type,
		trans.trans_dot, trans.trans_del_operator_id  
		FROM report_enc_trans trans 
		WHERE trans.charge_list_detail_id IN(".$str_splitted_chg_ids.") 
		AND trans.trans_type!='charges' $chAmtQryCriteria 
		ORDER BY trans.trans_dot, trans.trans_dot_time, trans.report_trans_id";
		
		$rs=imw_query($qry);
		while($res = imw_fetch_assoc($rs)){
			$report_trans_id=$res['report_trans_id'];
			$encounter_id= $res['encounter_id'];
			$chgDetId= $res['charge_list_detail_id'];
			$insCompId = $res['trans_ins_id'];
			$trans_type= strtolower($res['trans_type']);
			$trans_by= strtolower($res['trans_by']);
			$res['trans_method']= strtolower($res['trans_method']);
			

			//RECORD FIRST RECORD ID TO SOLVE PROBLEM EVEN IF RECORD IS MODIFIED MORE THAN TWO TIME.
			//THIS IS DONE SO THAT RECORD WILL DISPLAY ONLY LAST MODIFED PAYMENT METHOD IN RESULT.
			//BUT THERE ARE DIFFERENT OPERATORS MODIFIED SAME PAYMENT AND REPORT IS SEARCHED FOR PARTICULAR OPERATOR THEN THIS LOGIC MAY FAIL.
			$record_id=($res['parent_id']>0)? $res['parent_id']: $report_trans_id;
			if($res['parent_id']>0){
				if($tempRecording[$res['parent_id']]>0){
					$tempArrFirstRecordid[$report_trans_id]=$tempArrFirstRecordid[$res['parent_id']];
				}else{
					$tempArrFirstRecordid[$report_trans_id]=$res['parent_id'];
				}
				$tempRecording[$report_trans_id]=$report_trans_id;
			}
			$record_id=($tempArrFirstRecordid[$report_trans_id]>0)? $tempArrFirstRecordid[$report_trans_id]: $record_id;
			//--------------------------------------------		


			if($reportType!='checkView' || ($reportType=='checkView' && !$arrTransIds[$report_trans_id])){				
				$tempRecordData[$report_trans_id]=$res['trans_amount'];	
	
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
	
						//IF parent_id >0 THEN IT MEANS RECORD IS UPDATED. THEN REMOVE PREVIOUS FETCHED AMOUNT.
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = ($trans_type=='negative payment' || $trans_type=='copay-negative payment') ? $tempRecordData[$res['parent_id']] : "-".$tempRecordData[$res['parent_id']];
						}
			
						if($trans_by == 'patient' || $trans_by == 'res. party'){
							$patPayDetArr[$chgDetId]['patPaid']+= $paidForProc+$prevFetchedAmt;
							$patPayDetArr[$chgDetId]['lastDOT'] = $res['trans_dot'];
						}else if($trans_by == 'insurance'){
							if(empty($insuranceName)===true || (empty($insuranceName)===false && $arrInsurance[$insCompId])){	
								$patPayDetArr[$chgDetId]['insPaid']+= $paidForProc+$prevFetchedAmt;
								$patPayDetArr[$chgDetId]['lastDOT'] = $res['trans_dot'];
							}else{
								$paidForProc=0;
							}
						}
						$patPayDetArr[$chgDetId]['paid'][$record_id]+= $paidForProc+$prevFetchedAmt;
						$patPayDetArr[$chgDetId]['method'][$record_id]= $res['trans_method'];
						$patPayDetArr[$chgDetId]['check_num'][$record_id]= trim($res['check_number']);
						$patPayDetArr[$chgDetId]['cc_type'][$record_id]= $res['cc_type'];
						$patPayDetArr[$chgDetId]['cc_number'][$record_id]= $res['cc_number'];
						$patPayDetArr[$chgDetId]['paid_date'][$record_id] = $res['trans_dop'];
						
						$mainEncounterPayArr[$chgDetId]+= $paidForProc+$prevFetchedAmt;
			
					break;
	
					case 'credit':
					case 'debit':
						$crddbtamt=$res['trans_amount'];
						if($trans_type=='credit'){ 
							$crddbtamt= ($res['trans_del_operator_id']>0) ? "-".$res['trans_amount'] : $res['trans_amount'];							
						}else{  //debit
							$crddbtamt= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];				
						}
	
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = ($trans_type=='credit') ? "-".$tempRecordData[$res['parent_id']] : $tempRecordData[$res['parent_id']];
						}
						
						if($trans_by=='insurance'){
							$patPayDetArr[$chgDetId]['insPaid']+= $crddbtamt+$prevFetchedAmt;
							if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastDOT'] = $res['trans_dot'];
						}else{
							$patPayDetArr[$chgDetId]['patPaid']+= $crddbtamt+$prevFetchedAmt;				
							if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastDOT'] = $res['trans_dot'];
						}

						$patPayDetArr[$chgDetId]['paid'][$record_id]+= $crddbtamt+$prevFetchedAmt;
						$patPayDetArr[$chgDetId]['method'][$record_id]= $res['trans_method'];
						$patPayDetArr[$chgDetId]['check_num'][$record_id]= trim($res['check_number']);
						$patPayDetArr[$chgDetId]['cc_type'][$record_id]= $res['cc_type'];
						$patPayDetArr[$chgDetId]['cc_number'][$record_id]= $res['cc_number'];
						$patPayDetArr[$chgDetId]['paid_date'][$record_id] = $res['trans_dop'];
						
						$mainEncounterPayArr[$chgDetId]+= $crddbtamt+$prevFetchedAmt;
						
					break;
					case 'default_writeoff':
						$normalWriteOffAmt[$chgDetId]= $res['trans_amount'];
						if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastDOT'] = $res['trans_dot'];
					break;
					case 'write off':
					case 'discount':
						if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];
	
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
						}
						
						$writte_off_arr[$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
						if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastDOT'] = $res['trans_dot'];
					break;
					case 'over adjustment':
						if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];
	
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
						}
						
						$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
						if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastDOT'] = $res['trans_dot'];
					break;
					case 'adjustment':
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
	
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
						}
						
						$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
						if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastDOT'] = $res['trans_dot'];
					break;
					case 'returned check':
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
	
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = $tempRecordData[$res['parent_id']];
						}
						
						$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
						if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastDOT'] = $res['trans_dot'];
					break;
					case 'refund':
						$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
	
						$prevFetchedAmt=0;
						if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
							$prevFetchedAmt = $tempRecordData[$res['parent_id']];
						}
						
						$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount']+$prevFetchedAmt;
						if($res['trans_del_operator_id']<=0)$patPayDetArr[$chgDetId]['lastDOT'] = $res['trans_dot'];
					break;
				}
			}
		}
	}
}
unset($tempRecording);
unset($tempArrFirstRecordid);
?>