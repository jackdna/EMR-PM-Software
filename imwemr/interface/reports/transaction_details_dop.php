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
FILE : productivity_physcian_payments.php
PURPOSE : PRODUCTIVITY PAYMENTS FOR PHYSICIAN
ACCESS TYPE : DIRECT
*/

//MAKING QUERY PART ------------------
$qryBigPart='';
if(empty($grp_id) == false){
	$qryBigPart .= " and main.gro_id IN ($grp_id)";
}
if(empty($Physician) === false){
	$qryBigPart.= " and main.primary_provider_id_for_reports IN ($Physician)";
}
if(empty($credit_physician) === false){
	$qryBigPart.= " and main.sec_prov_id IN ($credit_physician)";
}
if($chksamebillingcredittingproviders==1){
	$qryBigPart.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
}
if(trim($insuranceName) != ''){
	$qryBigPart .= " and (main.pri_ins_id in ($insuranceName)
	or main.sec_ins_id in ($insuranceName)
	or main.tri_ins_id in ($insuranceName))";
}
//------------------

//GETTING INSURANCE COMP TYPES FOR ENCOUNTERS
$qry="Select main.encounter_id, main.pri_ins_id, main.sec_ins_id, main.tri_ins_id FROM report_enc_detail main
JOIN patient_data on patient_data.id = main.patient_id 
WHERE (main.pri_ins_id>0 OR main.sec_ins_id>0 OR main.tri_ins_id>0) ".$qryBigPart;
if(empty($sc_name) == false){
	$qry .= " and main.facility_id IN ($sc_name)";
}
$rs=imw_query($qry);
while($res=imw_fetch_assoc($rs)){
	$eid=$res['encounter_id'];
	$arrInsOfEnc[$eid][$res['pri_ins_id']]['primary']= $res['pri_ins_id'];
	$arrInsOfEnc[$eid][$res['sec_ins_id']]['secondary']= $res['sec_ins_id'];
	$arrInsOfEnc[$eid][$res['tri_ins_id']]['tertiary']= $res['tri_ins_id'];
	
}unset($rs);

$chAmtQryCriteria = '';
if($checkNo!=''){
	$checkNoTemp = explode(',',$checkNo);
	$or='';
	for($i=0;$i< sizeof($checkNoTemp); $i++){
		$checkNoTemp[$i]=strtoupper(trim($checkNoTemp[$i]));
		if(empty($checkNoTemp[$i])==false){
			if($i>0)$or=' OR ';
			$checkTemp[$checkNoTemp[$i]] = $checkNoTemp[$i];
			$chAmtQryPart.= $or." UPPER(TRIM(trans.check_number)) LIKE '%".$checkNoTemp[$i]."' ";
		}
	}
	if(empty($chAmtQryPart)==false){
		$chAmtQryCriteria.=" AND (".$chAmtQryPart.")";
	}
}
//TRANSACTIONS TABLE
$qry="Select trans.report_trans_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, 
trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_qry_type, trans.parent_id, trans.facility_id,
trans.trans_dot, trans.trans_dop, trans.trans_del_operator_id, trans.trans_method, trans.check_number  
FROM report_enc_trans trans 
WHERE trans.trans_type!='charges'";
if($DateRangeFor=='transaction_date'){
	$qry.=" AND (trans_dot BETWEEN '$Start_date' and '$End_date')";
}else{
	$qry.=" AND (trans_dop BETWEEN '$Start_date' and '$End_date')";	
}
if(empty($operatorName)==false){
	$qry.= " AND trans.trans_operator_id in($operatorName)";
}
if(empty($adjustmentId)==false){
	$qry.= " AND (LOWER(trans.trans_type) IN('adjustment','over adjustment','returned check') AND trans.trans_code_id in($adjustmentId))";
}
if(empty($wrt_code)==false){
	$qry.= " AND (LOWER(trans.trans_type) IN('default_writeoff','write off') AND trans.trans_code_id in($wrt_code))";
}
if(empty($checkNo)==false){
	$qry.= $chAmtQryCriteria;
}
$qry.=" ORDER BY trans.master_tbl_id, trans.report_trans_id, trans.trans_dot, trans.trans_dot_time";
$rs=imw_query($qry);
$encounterIdArr=array();
$c=0;
while($res = imw_fetch_assoc($rs)){
	$report_trans_id=$res['report_trans_id'];
	$encounter_id= $res['encounter_id'];
	$chgDetId= $res['charge_list_detail_id'];
	$insCompId = $res['trans_ins_id'];
	$encounterIdArr[$res['encounter_id']] = $res['encounter_id'];
	$allTransChgDetId[$chgDetId]=$chgDetId;
	$trans_type= strtolower($res['trans_type']);
	$trans_by= strtolower($res['trans_by']);			
	$trans_method= $res['trans_method'];			
	$check_number= $res['check_number'];
	
	$payloc=$res['facility_id'];

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
				$arrAllTransactions[$chgDetId][$c]['pat_payment'] = $paidForProc + $prevFetchedAmt;
				$arrAllTransactions_date[$chgDetId][$c] = ($DateRangeFor=='transaction_date') ?$res['trans_dot']: $res['trans_dop'];
				$arrAllTransactions_payinfo['mode'][$chgDetId][$c] = $trans_method;
				$arrAllTransactions_payinfo['check_number'][$chgDetId][$c] = $check_number;
				$c++;
			}else if($trans_by == 'insurance'){
				if(empty($insuranceName)===true || (empty($insuranceName)===false && $arrInsurance[$insCompId])){	

					if($arrInsOfEnc[$encounter_id][$insCompId]['primary']){
						$arrAllTransactions[$chgDetId][$c]['pri_payment'] = $paidForProc + $prevFetchedAmt;
						$arrAllTransactions_date[$chgDetId][$c] = ($DateRangeFor=='transaction_date') ?$res['trans_dot']: $res['trans_dop'];
						$arrAllTransactions_payinfo['mode'][$chgDetId][$c] = $trans_method;
						$arrAllTransactions_payinfo['check_number'][$chgDetId][$c] = $check_number;
						$c++;
					}else if($arrInsOfEnc[$encounter_id][$insCompId]['secondary'] || $arrInsOfEnc[$encounter_id][$insCompId]['tertiary']){
						$arrAllTransactions[$chgDetId][$c]['sec_payment'] = $paidForProc + $prevFetchedAmt;
						$arrAllTransactions_date[$chgDetId][$c] = ($DateRangeFor=='transaction_date') ?$res['trans_dot']: $res['trans_dop'];
						$arrAllTransactions_payinfo['mode'][$chgDetId][$c] = $trans_method;
						$arrAllTransactions_payinfo['check_number'][$chgDetId][$c] = $check_number;
						$c++;
					}
				}else{
					$paidForProc=0;
				}						
			}
			$mainEncounterPayArr[$chgDetId]+= $paidForProc + $prevFetchedAmt;
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
				$arrAllTransactions[$chgDetId][$c]['pri_payment'] = $crddbtamt + $prevFetchedAmt;
				$arrAllTransactions_date[$chgDetId][$c] = ($DateRangeFor=='transaction_date') ?$res['trans_dot']: $res['trans_dop'];
				$arrAllTransactions_payinfo['mode'][$chgDetId][$c] = $trans_method;
				$arrAllTransactions_payinfo['check_number'][$chgDetId][$c] = $check_number;
				$c++;
			}else{
				$arrAllTransactions[$chgDetId][$c]['pat_payment'] = $crddbtamt + $prevFetchedAmt;
				$arrAllTransactions_date[$chgDetId][$c] = ($DateRangeFor=='transaction_date') ?$res['trans_dot']: $res['trans_dop'];
				$arrAllTransactions_payinfo['mode'][$chgDetId][$c] = $trans_method;
				$arrAllTransactions_payinfo['check_number'][$chgDetId][$c] = $check_number;
				$c++;
			}
			
		break;
		case 'default_writeoff':
			$arrAllTransactions[$chgDetId][$c]['writeoff'] = $res['trans_amount'];
			$arrAllTransactions_date[$chgDetId][$c] = ($DateRangeFor=='transaction_date') ?$res['trans_dot']: $res['trans_dop'];
			$arrAllTransactions_payinfo['mode'][$chgDetId][$c] = $trans_method;
			$arrAllTransactions_payinfo['check_number'][$chgDetId][$c] = $check_number;
			$c++;
		break;
		case 'write off':
		case 'discount':
			if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];

			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
			}
			
			$arrAllTransactions[$chgDetId][$c]['writeoff'] = $res['trans_amount'] + $prevFetchedAmt;
			$arrAllTransactions_date[$chgDetId][$c] = ($DateRangeFor=='transaction_date') ?$res['trans_dot']: $res['trans_dop'];
			$arrAllTransactions_payinfo['mode'][$chgDetId][$c] = $trans_method;
			$arrAllTransactions_payinfo['check_number'][$chgDetId][$c] = $check_number;
			$c++;
		break;
		case 'over adjustment':
			if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];		

			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
			}

			$arrAllTransactions[$chgDetId][$c]['adj'] = $res['trans_amount'] + $prevFetchedAmt;
			$arrAllTransactions_date[$chgDetId][$c] = ($DateRangeFor=='transaction_date') ?$res['trans_dot']: $res['trans_dop'];
			$arrAllTransactions_payinfo['mode'][$chgDetId][$c] = $trans_method;
			$arrAllTransactions_payinfo['check_number'][$chgDetId][$c] = $check_number;
			$c++;
			
		break;
		case 'adjustment':
			$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = $tempRecordData[$res['parent_id']];
			}
			
			$arrAllTransactions[$chgDetId][$c]['adj'] = $res['trans_amount'] + $prevFetchedAmt;
			$arrAllTransactions_date[$chgDetId][$c] = ($DateRangeFor=='transaction_date') ?$res['trans_dot']: $res['trans_dop'];
			$arrAllTransactions_payinfo['mode'][$chgDetId][$c] = $trans_method;
			$arrAllTransactions_payinfo['check_number'][$chgDetId][$c] = $check_number;
			$c++;
			
		break;
		case 'returned check':
			$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = $tempRecordData[$res['parent_id']];
			}
			
			$arrAllTransactions[$chgDetId][$c]['adj'] = $res['trans_amount'] + $prevFetchedAmt;
			$arrAllTransactions_date[$chgDetId][$c] = ($DateRangeFor=='transaction_date') ?$res['trans_dot']: $res['trans_dop'];
			$arrAllTransactions_payinfo['mode'][$chgDetId][$c] = $trans_method;
			$arrAllTransactions_payinfo['check_number'][$chgDetId][$c] = $check_number;
			$c++;
		break;
		case 'refund':
			$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];		

			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = $tempRecordData[$res['parent_id']];
			}
			
			$arrAllTransactions[$chgDetId][$c]['refund'] = $res['trans_amount'] + $prevFetchedAmt;
			$arrAllTransactions_date[$chgDetId][$c] = ($DateRangeFor=='transaction_date') ?$res['trans_dot']: $res['trans_dop'];
			$arrAllTransactions_payinfo['mode'][$chgDetId][$c] = $trans_method;
			$arrAllTransactions_payinfo['check_number'][$chgDetId][$c] = $check_number;
			$c++;
		break;
	}
}


//GET FIRST POSTED ENCOUTNERS FETCHED SEPARATELY DUE TO OPERATOR SELECTION
$firstPostedEnc=array();
$qry="Select main.encounter_id FROM report_enc_detail main
JOIN patient_data on patient_data.id = main.patient_id 
WHERE (main.first_posted_date BETWEEN '$Start_date' AND '$End_date') 
AND (main.del_status='0' OR (main.del_status='1' AND DATE_FORMAT(main.trans_del_date, '%Y-%m-%d')>'$End_date')) ".$qryBigPart;
if(empty($sc_name) == false){
	$qry .= " and main.facility_id IN ($sc_name)";
}
if(empty($operatorName)==false){
	$qry.= " AND main.operator_id in($operatorName)";
}
$rs=imw_query($qry);
while($res=imw_fetch_assoc($rs)){
	$firstPostedEnc[$res['encounter_id']]=$res['encounter_id'];
}unset($rs);
//------------------------------

//MERGE WITH FIRST POSTED ENCOUNTERS
$encounterIdArr=array_merge($encounterIdArr, $firstPostedEnc);
$encounterIdArr = array_unique($encounterIdArr);
$encounterIdStr = join(',',$encounterIdArr);


//MAIN QUERY
if(sizeof($encounterIdArr)>0){
	$qry="Select main.charge_list_id, main.encounter_id, main.charge_list_detail_id, main.pri_ins_id, main.sec_ins_id, main.tri_ins_id,main.operator_id,
	(main.charges * main.units) as totalAmt, main.units, date_format(main.date_of_service,'".$dateFormat."') as date_of_service,
	DATE_FORMAT(main.trans_del_date, '%Y-%m-%d') as trans_del_date,
	main.primary_provider_id_for_reports  as 'primaryProviderId', main.sec_prov_id,	main.billing_facility_id,
	main.proc_code_id, main.dx_id1, main.dx_id2, main.dx_id3, main.dx_id4,
	main.proc_balance,	main.pri_due, main.sec_due, main.tri_due, main.pat_due, main.del_status, main.first_posted_date, main.over_payment,
	main.submitted, main.over_payment, 	
	users.lname as physicianLname,users.fname as physicianFname, users.mname as physicianMname,
	pos_tbl.pos_prac_code as facilityPracCode,
	pos_facilityies_tbl.pos_facility_id, patient_data.id as patient_id,
	patient_data.lname,	patient_data.fname, patient_data.mname, patient_data.default_facility,
	main.write_off, cpt_fee_tbl.cpt_prac_code, cpt_fee_tbl.cpt4_code
	FROM report_enc_detail main 
	JOIN patient_data on patient_data.id = main.patient_id 
	LEFT JOIN users on users.id = main.primary_provider_id_for_reports 
	LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id
	LEFT JOIN pos_tbl ON pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
	LEFT JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = main.proc_code_id
	WHERE 1=1";
	$qry.= $qryBigPart;
	
	if(empty($sc_name) == false){
		$qry .= " and main.facility_id IN ($sc_name)";
	}
	
	if(trim($cpt_code_id) != ''){
		$qry.= " AND main.proc_code_id in ($cpt_code_id)";
	}
	if(trim($dx_code10)!= ''){
		$qry.=" AND (main.dx_id1 in ($dx_code10)
		or main.dx_id2 in ($dx_code10)
		or main.dx_id3 in ($dx_code10)
		or main.dx_id4 in ($dx_code10))";
	}
	if(trim($modifiers)!=''){
		$qry.= " and (main.mod_id1 IN($modifiers)
		OR main.mod_id2 IN($modifiers)
		OR main.mod_id3 IN($modifiers)
		OR main.mod_id4 IN($modifiers))";
	}
	if(sizeof($encounterIdArr)>0){
		$qry.= " AND main.encounter_id in ($encounterIdStr)";		
	}
	
	if($pureSelfPay==true)$qry .= " and main_details.proc_selfpay=1";
	
	$qry .= " order by users.lname,users.fname, pos_facilityies_tbl.facilityPracCode, main.date_of_service,
	patient_data.lname,patient_data.fname, main.encounter_id";
	$res=imw_query($qry);
	
	//--- PRIMARY PROVIDER ID ARRAY ----
	$arr_selected_phy = array();
	if(empty($Physician) === false){
		$arr_selected_phy = preg_split("/,/", $Physician);
	}
	
	//--- SECONDARY PROVIDER ID ARRAY ----
	$credit_physician_arr = array();
	if($credit_physician != ''){
		$credit_physician_arr = preg_split("/,/", $credit_physician);
	}
	
	$main_encounter_id_arr = array();
	$facilityNameArr = array();
	$physician_initial_arr = array();
	$arrPatNoFacility=array();
	$arrAllEncIds=array();
	while($rs = imw_fetch_assoc($res)){
		$printFile=true;
		$encounter_id = $rs['encounter_id'];
		$arrAllEncIds[$encounter_id]=$encounter_id;
		
		$charge_list_detail_id = $rs['charge_list_detail_id'];
		$doctor_id = $rs['primaryProviderId'];
		$secondaryProviderId = $rs['sec_prov_id'];
	
		//---- GET POS FACILITY NAME ---
		$pos_facility_id = ($pay_location=='1')? $rs['billing_facility_id'] :$rs['pos_facility_id'];
		$pos_facility_id = (empty($pos_facility_id)==true) ? '0' : $pos_facility_id;
		$first_posted_date= $rs["first_posted_date"];
		$oprId=$rs['operator_id'];
		$deptId= $arrDeptOfCptCodes[$rs["proc_code_id"]];
		$submitted= $rs["submitted"];

		if(empty($Physician) === true && empty($credit_physician) === false){
			$doctor_id = $rs['sec_prov_id'];
		}	
		
		//IF NOT DELETED OR DELETED AFTER FIRST POSTED DATE ONLY THEN FETCH THE RECORD
		if($rs['del_status']=='0' 
		|| $first_posted_date=='0000-00-00' 
		|| ($rs['del_status']=='1' && $first_posted_date!='0000-00-00' && $rs['trans_del_date'] >= $first_posted_date)){

			if(!($submitted=='true' && $first_posted_date>=$Start_date && $first_posted_date<=$End_date && (sizeof($arrSelOprators)<=0 || $arrSelOprators[$oprId]))){
				$rs['totalAmt']='0';
			}

			if($rs['default_facility']<=0 || $rs['default_facility']==''){
				$arrPatNoFacility[$rs['patient_id']]=$rs['patient_id'];
			}
		
			$arrChgDetIds[$charge_list_detail_id] = $charge_list_detail_id;
		
			$firstGrpBy= $doctor_id;
			$secGrpBy = $pos_facility_id;
		
			//ARRAY TO RECORD PRIMARY, SECONDARY & TER. INS COMPANY OF ENCOUNTER
			$arrInsOfEnc[$encounter_id][$rs['pri_ins_id']]['primary']= $rs['pri_ins_id'];
			$arrInsOfEnc[$encounter_id][$rs['sec_ins_id']]['secondary']= $rs['sec_ins_id'];
			$arrInsOfEnc[$encounter_id][$rs['tri_ins_id']]['tertiary']= $rs['tri_ins_id'];
		
			//---- GET SECONDARY PHYSICIAN INITIAL ------
			$pro_id = $rs['primaryProviderId'];
			if($secondaryProviderId > 0){
				$pro_id = $rs['secondaryProviderId'];
			}
			$provider_name_arr = preg_split('/, /',$providerNameArr[$pro_id]);
			$physician_initial = $provider_name_arr[1][0];
			$physician_initial .= $provider_name_arr[0][0];
			$rs['physician_initial'] = strtoupper($physician_initial);
	
			$mainResArr[$firstGrpBy][$secGrpBy][$charge_list_detail_id] = $rs;

			$main_encounter_id_arr[$encounter_id] = $encounter_id;
			$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
		}
	}
}
unset($arrEncTime);
unset($arrToMatchInsIds);
unset($tempTransPayLoc);
?>