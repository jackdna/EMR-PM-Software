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

$tempChgDetIds=array();
if(trim($wrt_code)!= '' || trim($adjustmentId)!=''){
	$typeString=$sep="";
	if(trim($wrt_code)!= ''){
		$typeString="'default_writeoff','write off','discount'";
		$sep=",";
	}
	if(trim($adjustmentId)!= ''){
		$typeString.=$sep."'adjustment','over adjustment','returned check'";
	}
	$qry="Select trans.charge_list_detail_id FROM report_enc_trans trans 
	WHERE LOWER(trans.trans_type) IN(".$typeString.")";
	$rs=imw_query($qry);
	while($res = imw_fetch_assoc($rs)){
		$tempChgDetIds[$res['charge_list_detail_id']]=$res['charge_list_detail_id'];
	}
}

//--- GET ALL CHARGES ----
$qry= "select main.charge_list_id,
main.encounter_id,main.pri_ins_id,
main.sec_ins_id, main.tri_ins_id, main.operator_id,
main.total_charges as 'totalAmt',
main.over_payment,
main.units,
main.approved_amt, main.last_pri_paid_date,
date_format(main.date_of_service,'".$dateFormat."') as date_of_service,
main.primary_provider_id_for_reports  as 'primaryProviderId', main.sec_prov_id,		
main.over_payment,
main.charge_list_detail_id,
main.proc_code_id,
main.dx_id1, main.dx_id2,
main.dx_id3, main.dx_id4,
main.proc_balance,
main.pri_due, main.sec_due, 
main.tri_due, main.pat_due, 
users.lname as physicianLname,users.fname as physicianFname, users.mname as physicianMname,
pos_tbl.pos_prac_code as facilityPracCode,
pos_facilityies_tbl.pos_facility_id, patient_data.id as patient_id,
patient_data.lname,	patient_data.fname, patient_data.mname, patient_data.default_facility,
main.write_off,cpt_fee_tbl.cpt_prac_code, cpt_fee_tbl.cpt_desc 
FROM report_enc_detail main 
JOIN patient_data on patient_data.id = main.patient_id 
JOIN users on users.id = main.primary_provider_id_for_reports 
LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id 
LEFT JOIN pos_tbl ON pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = main.proc_code_id
WHERE (main.date_of_service between '$Start_date' and '$End_date')				 
AND main.del_status='0'";
		
if(empty($sc_name) == false){
	$qry.= " and main.facility_id IN ($sc_name)";	
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
if(trim($cpt_code_id) != ''){
	$qry.= " AND main.proc_code_id in ($cpt_code_id)";
}
if(trim($dx_code) != '' || trim($dx_code10) != ''){
	$qry.= ' AND (';
	$andOR='';
	if(trim($dx_code)!= ''){
		$qry.= " (main.dx_id1 in ($dx_code)
		or main.dx_id2 in ($dx_code)
		or main.dx_id3 in ($dx_code)
		or main.dx_id4 in ($dx_code))";
		$andOR=' OR ';
	}
	if(trim($dx_code10)!= ''){
		$qry.=$andOR." (main.dx_id1 in ($dx_code10)
		or main.dx_id2 in ($dx_code10)
		or main.dx_id3 in ($dx_code10)
		or main.dx_id4 in ($dx_code10))";
	}
	$qry.= ') ';	
}
if(trim($modifiers)!=''){
	$qry.= " and (main.mod_id1 IN($modifiers)
	OR main.mod_id2 IN($modifiers)
	OR main.mod_id3 IN($modifiers)
	OR main.mod_id4 IN($modifiers))";
}
if(empty($wrt_code)==false){
	if(sizeof($tempChgDetIds)>0){
		$str_tempChgDetIds=implode(',', $tempChgDetIds);
		$qry.= " and main.charge_list_detail_id IN(".$str_tempChgDetIds.")";
	}else{
		$qry.= " and 5=6"; //SO THAT NO RECORD CAN BE FETCH
	}
}
if($adjustmentId!=''){
	$qry.= " and main.charge_list_detail_id IN(".$adjCodeChdDetIdStr.")";			
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
if($pureSelfPay==true)$qry.= " and main.proc_selfpay=1";

$qry.= " ORDER BY users.lname,users.fname, pos_facilityies_tbl.facilityPracCode,
main.date_of_service, patient_data.lname,patient_data.fname, 
main.encounter_id";

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
while($rs = imw_fetch_assoc($res)){
	$printFile=true;
	$encounter_id = $rs['encounter_id'];
	$main_encounter_id_arr[$encounter_id] = $encounter_id;
	$doctor_id = $rs['primaryProviderId'];
	$operator_id = $rs['operator_id'];
	$deptId= $arrDeptOfCptCodes[$rs["proc_code_id"]];
	$pos_facility_id = $rs['pos_facility_id'];	
	$pos_facility_id = (empty($pos_facility_id)==true) ? '0' : $pos_facility_id;
	$facilityPracCode = $rs['facilityPracCode'];
	$facilityNameArr[$pos_facility_id] = $facilityPracCode;	
	$chgDetId = $rs['charge_list_detail_id'];
	$default_facility = $rs['default_facility'];

	if(empty($Physician) === true && empty($credit_physician) === false){
		$doctor_id = $rs['sec_prov_id'];
	}

	$arrChgDetIds[$chgDetId] = $chgDetId;

	if($rs['coPayAdjustedAmount']==1){
		$arrCopayEncs[$encounter_id] = $rs['charge_list_detail_id'];
	}
	
	if($rs['default_facility']<=0 || $rs['default_facility']==''){
		$arrPatNoFacility[$rs['patient_id']]=$rs['patient_id'];
	}
	
	//ARRAY TO RECORD PRIMARY, SECONDARY & TER. INS COMPANY OF ENCOUNTER
	$arrInsOfEnc[$encounter_id][$rs['pri_ins_id']]['primary']= $rs['pri_ins_id'];
	$arrInsOfEnc[$encounter_id][$rs['sec_ins_id']]['secondary']= $rs['sec_ins_id'];
	$arrInsOfEnc[$encounter_id][$rs['tri_ins_id']]['tertiary']= $rs['tri_ins_id'];

	$firstGrpBy= $doctor_id;
	$secGrpBy = $pos_facility_id;
	
	$mainResArr[$firstGrpBy][$secGrpBy][$chgDetId]= $rs;
	
}unset($rs);


//TRANSACTIONS TABLE
if(sizeof($arrChgDetIds)>0){
	$splitted_chgids = array_chunk($arrChgDetIds,2000);

	foreach($splitted_chgids as $arr){
		$str_splitted_chg_ids = implode(',',$arr);

		$qry="Select trans.report_trans_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, 
		trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_qry_type, trans.parent_id,
		trans.trans_dot, trans.trans_del_operator_id  
		FROM report_enc_trans trans 
		WHERE trans.charge_list_detail_id IN(".$str_splitted_chg_ids.") 
		AND trans.trans_type!='charges' 
		ORDER BY trans.trans_dot, trans.trans_dot_time";
		$rs=imw_query($qry);
		while($res = imw_fetch_assoc($rs)){
			$report_trans_id=$res['report_trans_id'];
			$encounter_id= $res['encounter_id'];
			$chgDetId= $res['charge_list_detail_id'];
			$insCompId = $res['trans_ins_id'];
			$trans_type= strtolower($res['trans_type']);
			$trans_by= strtolower($res['trans_by']);	
			
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
					$paidForProc+=$prevFetchedAmt; 
					
					if($trans_by == 'patient' || $trans_by == 'res. party'){
						$patPayDetArr[$chgDetId]['patPaid']+= $paidForProc;
					}else if($trans_by == 'insurance'){
						if(empty($insuranceName)===true || (empty($insuranceName)===false && $arrInsurance[$insCompId])){	
							$patPayDetArr[$chgDetId]['insPaid']+= $paidForProc;
	
							//GET PAYMENTS BASED ON PRIMARY/SEC/TER COMPMAY
							if($arrInsOfEnc[$encounter_id][$insCompId]['primary']){
								$patPayDetArr[$chgDetId]['priPaid']+= $paidForProc;
							}else if($arrInsOfEnc[$encounter_id][$insCompId]['secondary']){
								$patPayDetArr[$chgDetId]['secPaid']+= $paidForProc;
							}else if($arrInsOfEnc[$encounter_id][$insCompId]['tertiary']){
								$patPayDetArr[$chgDetId]['terPaid']+= $paidForProc;
							}
						}else{
							$paidForProc=0;
						}
					}
					$mainEncounterPayArr[$chgDetId]+= $paidForProc;
			
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
					$crddbtamt+=$prevFetchedAmt; 
					
					
					if($viewBy=='insurance'){
						if($insCompId>0){
							$pay_crd_deb_arr[$insCompId][$chgDetId][]= $crddbtamt;	
							$ins_paid_adj_enc[$chgDetId]=$chgDetId;
							//TO MATCH IF INS ID IS DIFFERENT THAN ITS ENCOUNTER IDS
							$arrToMatchInsIds[$chgDetId][$insCompId]=$insCompId;
						}else{
							$arrOtherGrandTotalTemp[$chgDetId]['debit_credit']+= $crddbtamt;
						}
					}else{
						if($trans_by=='insurance'){
							$pay_crd_deb_arr[$chgDetId]['Insurance']+= $crddbtamt;
						}else{
							$pay_crd_deb_arr[$chgDetId]['Patient']+= $crddbtamt;				
						}
					}
					
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
					$res['trans_amount']+=$prevFetchedAmt;
					
					$writte_off_arr[$chgDetId]+= $res['trans_amount'];
				break;
				case 'over adjustment':
					if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];

					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
					}
					$res['trans_amount']+=$prevFetchedAmt;
					
					$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'];
				break;
				case 'adjustment':
					$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = $tempRecordData[$res['parent_id']];
					}
					$res['trans_amount']+=$prevFetchedAmt;
					
					$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'];
				break;
				case 'returned check':
					$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = $tempRecordData[$res['parent_id']];
					}
					$res['trans_amount']+=$prevFetchedAmt;
					
					$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'];
				break;
				case 'refund':
					$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = $tempRecordData[$res['parent_id']];
					}
					$res['trans_amount']+=$prevFetchedAmt;
					
					$arrAdjustmentAmt[$chgDetId]+= $res['trans_amount'];
				break;
			}
		}
	}
}
?>