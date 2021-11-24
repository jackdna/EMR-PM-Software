<?php

//GETTING AMOUNTS FOR SELECTED DOS.

//CHARGES
$qry = "
Select main.charge_list_id, main.encounter_id, main.charge_list_detail_id, main.pri_ins_id,	main.sec_ins_id, main.tri_ins_id, main.operator_id,
main.facility_id, main.gro_id, (main.charges * main.units) as totalAmt, main.units, date_format(main.date_of_service,'".$date_format_SQL."') as date_of_service,
main.primary_provider_id_for_reports  as 'primaryProviderId', main.sec_prov_id,	
main.proc_code_id, main.dx_id1, main.dx_id2, main.dx_id3, main.dx_id4,
main.proc_balance,	main.pri_due, main.sec_due, main.tri_due, main.pat_due, main.over_payment,
users.lname as physicianLname,users.fname as physicianFname, users.mname as physicianMname,
pos_tbl.pos_prac_code as facilityPracCode,
pos_facilityies_tbl.pos_facility_id, patient_data.id as patient_id,
patient_data.lname,	patient_data.fname, patient_data.mname, patient_data.default_facility, main.write_off".$strDeptField." 
FROM report_enc_detail main 
".$strDeptJoin."
JOIN patient_data on patient_data.id = main.patient_id 
LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id 
LEFT JOIN pos_tbl ON pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
JOIN users on users.id = main.primary_provider_id_for_reports 
WHERE (main.date_of_service between '".$startDate."' AND '".$endDate."')				 
AND main.del_status='0'";	
if(empty($str_cpt_cat_2) === false){
	$qry.= " and cpt_fee_tbl.cpt_category2 in($str_cpt_cat_2)";
}	
if(empty($sc_name) === false){
	$qry.= " and main.facility_id in($sc_name)";
}
if(empty($grp_id) === false){
	$qry.= " and main.gro_id in($grp_id)";
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
if($groupBy=='grpby_facility'){
	$qry.= " ORDER BY pos_facilityies_tbl.facilityPracCode, users.lname, users.fname, patient_data.lname, patient_data.fname";	
}else{
	$qry.= " ORDER BY users.lname, users.fname, pos_facilityies_tbl.facilityPracCode, patient_data.lname, patient_data.fname";	
}
$qryRs=imw_query($qry);

$arrChgdetid_cat2=array();
while($qryRes = imw_fetch_array($qryRs)){	

	$totalBalance=0;
	$encounter_id = $qryRes['encounter_id'];		
	$charge_list_detail_id = $qryRes['charge_list_detail_id'];

	$doctor_id = $qryRes['primaryProviderId'];
	$facility_id = $qryRes['facility_id'];

	//IF BILLING PHYSICIAN NOT SELECTED AND CREDIT PHYSICIAN SELECTED THEN DISPLAY GROUPED BY SECONDARY PHYSICIAN
	if(empty($Physician) === true && empty($credit_physician) === false){
		$doctor_id=$qryRes['sec_prov_id'];
	}

	$firstGroupBy = $doctor_id;
	$secGroupBy = $facility_id;
	if($groupBy=='grpby_facility'){
		$firstGroupBy = $facility_id;
		$secGroupBy = $doctor_id;
	}
	
	$selGroup=$arrAllGroups[$qryRes['gro_id']];
	$selDoctor=$providerNameArr[$doctor_id];

	$cat_2= (empty($str_cpt_cat_2)==false) ? $qryRes['cpt_category2'] : 0; 	
	
	$arrChgdetid_cat2[$charge_list_detail_id]=$cat_2;

	if($summary_detail=='summary'){
		$checkInDataArr[$firstGroupBy][$secGroupBy]=$qryRes;
		$dr_tot_amt_arr[$firstGroupBy][$secGroupBy][$cat_2][]=$qryRes['totalAmt'];
	
		if($qryRes['over_payment']>0){
			$dr_tot_credit_arr[$firstGroupBy][$secGroupBy][$cat_2][]= $qryRes['over_payment'];
		}
	}else{
		$checkInDataArr[$firstGroupBy][$secGroupBy][$encounter_id]=$qryRes;
		$dr_tot_amt_arr[$firstGroupBy][$secGroupBy][$encounter_id]+=$qryRes['totalAmt'];
	
		if($qryRes['over_payment']>0){
			$dr_tot_credit_arr[$firstGroupBy][$secGroupBy][$encounter_id]+= $qryRes['over_payment'];
		}
	}

	$dr_tot_enc_details[$firstGroupBy][$secGroupBy][$encounter_id]= $qryRes;	

	$main_encounter_id_arr[$encounter_id] = $encounter_id;		
	$tempEncArr[$encounter_id]['firstgroupid'] = $firstGroupBy;
	$tempEncArr[$encounter_id]['secondgroupid'] = $secGroupBy;
	$tempChgArr[$charge_list_detail_id] = $encounter_id;
}
unset($qryRs);	

//PAYMENTS & ADJUSTMENTS
if(sizeof($tempChgArr)>0){
	$main_encounter_id_str = join(',',$main_encounter_id_arr);
	$splitted_chgdetid = array_chunk(array_keys($tempChgArr),1500);
	$getPaymentDetailsArr1 = $getPaymentDetailsArr = array();

	foreach($splitted_chgdetid as $arr){
		$str_splitted_chgdetid	 = implode(',',$arr);

		$qry="Select trans.report_trans_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, 
		trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_dot, trans.trans_qry_type,trans.parent_id,
		DATE_FORMAT(trans.trans_dot,'".$dateFormat."') as trans_dot, trans.trans_del_operator_id  
		FROM report_enc_trans trans 
		WHERE trans.charge_list_detail_id IN(".$str_splitted_chgdetid.") 
		AND LOWER(trans.trans_type)!='charges'
		ORDER BY trans.master_tbl_id, trans.report_trans_id, trans.trans_dot, trans.trans_dot_time";
		$rs=imw_query($qry);
		while($res = imw_fetch_assoc($rs)){
			$report_trans_id=$res['report_trans_id'];
			$encounter_id= $res['encounter_id'];
			$chgDetId= $res['charge_list_detail_id'];
			$insCompId = $res['trans_ins_id'];
			$trans_type= strtolower($res['trans_type']);
			$trans_by= strtolower($res['trans_by']);
			$cat_2=$arrChgdetid_cat2[$chgDetId];

			$firstGroupBy = $tempEncArr[$encounter_id]['firstgroupid'];
			$secGroupBy = $tempEncArr[$encounter_id]['secondgroupid'];

			$tempRecordData[$report_trans_id]=$res['trans_amount'];			
			
			switch($trans_type){
				case 'paid':
				case 'copay-paid':
				case 'deposit':
				case 'interest payment':
				case 'negative payment':
				case 'copay-negative payment':
					$paidForProc=$res['trans_amount'];
					if($trans_type=='negative payment' || $trans_type=='copay_negative payment' || $res['trans_del_operator_id']>0)$paidForProc="-".$res['trans_amount'];
					if(($trans_type=='negative payment' || $trans_type=='copay-negative payment') && $res['trans_del_operator_id']>0)$paidForProc=$res['trans_amount'];

					//IF parent_id >0 THEN IT MEANS RECORD IS UPDATED. THEN REMOVE PREVIOUS FETCHED AMOUNT.
					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = ($trans_type=='negative payment' || $trans_type=='copay-negative payment') ? $tempRecordData[$res['parent_id']] : "-".$tempRecordData[$res['parent_id']];
					}
					$paidForProc+=$prevFetchedAmt; 
					
					if($summary_detail=='summary'){
						$dr_tot_paid_arr[$firstGroupBy][$secGroupBy][$cat_2][] = $paidForProc;
					}else{
						$dr_tot_paid_arr[$firstGroupBy][$secGroupBy][$encounter_id]+= $paidForProc;
					}
					
					//SET CREDIT/DEBIT TO 0 BECAUSE LOOP IS RUNNING BASED ON CHARGE LISET DETAIL TABLE.
					//$pay_crd_deb_arr[$encounter_id]=0;
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
					
					if($summary_detail=='summary'){
						$dr_tot_paid_arr[$firstGroupBy][$secGroupBy][$cat_2][] = $crddbtamt;	
					}else{
						$dr_tot_paid_arr[$firstGroupBy][$secGroupBy][$encounter_id]+= $crddbtamt;
					}
					
				break;
				case 'default_writeoff':
					//TO FETCH ONLY LAST DEFAULT WRITE-OFF FOR PROCEDURE
					$tempLastWriteOff[$chgDetId]= $res['trans_amount'];
				break;
				case 'write off':
				case 'discount':
				case 'over adjustment':
					if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];

					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
					}
					$res['trans_amount']+=$prevFetchedAmt;
					
					if($summary_detail=='summary'){
						$dr_tot_adj_arr[$firstGroupBy][$secGroupBy][$cat_2][]= $res['trans_amount'];
					}else{
						$dr_tot_adj_arr[$firstGroupBy][$secGroupBy][$encounter_id]+= $res['trans_amount'];
					}
				break;
				case 'adjustment':
				case 'returned check':
					$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
					}
					$res['trans_amount']+=$prevFetchedAmt;
					
					if($summary_detail=='summary'){
						$dr_tot_adj_arr[$firstGroupBy][$secGroupBy][$cat_2][]= $res['trans_amount'];
					}else{
						$dr_tot_adj_arr[$firstGroupBy][$secGroupBy][$encounter_id]+= $res['trans_amount'];
					}
						
				break;
				case 'refund':
					$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

					$prevFetchedAmt=0;
					if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
						$prevFetchedAmt = $tempRecordData[$res['parent_id']];
					}
					$res['trans_amount']+=$prevFetchedAmt;
					
					if($summary_detail=='summary'){
						$dr_tot_ref_arr[$firstGroupBy][$secGroupBy][$cat_2][]= $res['trans_amount'];
					}else{
						$dr_tot_ref_arr[$firstGroupBy][$secGroupBy][$encounter_id]+= $res['trans_amount'];
					}
				break;
			}
		}
	
		//DEFAULT WRITE-OFF
		foreach($tempLastWriteOff as $chgDetId => $writeOffAmt){
			$encounter_id= $tempChgArr[$chgDetId];
			$cat_2=$arrChgdetid_cat2[$chgDetId];

			$firstGroupBy = $tempEncArr[$encounter_id]['firstgroupid'];
			$secGroupBy = $tempEncArr[$encounter_id]['secondgroupid'];
			
			if($summary_detail=='summary'){
				$dr_tot_adj_arr[$firstGroupBy][$secGroupBy][$cat_2][]= $writeOffAmt;			
			}else{
				$dr_tot_adj_arr[$firstGroupBy][$secGroupBy][$encounter_id]+= $writeOffAmt;
			}
				
		}
		unset($tempLastWriteOff);
	}
}


//GETTING BEGINNING/ENDING A/R
function getPreviousBal($callFrom='', $startDate='', $endDate='', $prevDate='', $strProviders='', $strFacilities='', $groupBy='', $grp_id='', $str_cpt_category_2='', $summary_detail='summary',$str_credit_physician='', $chksamebilling=''){
	global $dr_tot_enc_details;
	global $date_format_SQL;

	$balWithoutCreditCalculated=0;
	$dr_tot_beg_ar_arr=array();
	$arrBalEncs =array();
	
	$arrAllEncounters=array();
	$arrEncBalance=array();
	$arrEncounters = array();
	$arrEncDetails= array();
	$arrEncCharges= array();
	
	$deptJoin = ''; $deptField = '';
	if( $strDepartment || $groupBy == 'grpby_department' || empty($str_cpt_category_2)==false)
	{
		$deptField = ', cpt_fee_tbl.departmentId, cpt_fee_tbl.cpt_category2';
		$deptJoin = ' JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = main.proc_code_id'; 
	}	

	$begQry ="Select main.patient_id, main.charge_list_id, main.encounter_id, main.charge_list_detail_id, main.pri_ins_id, main.sec_ins_id,
	main.tri_ins_id, main.operator_id, main.gro_id, (main.charges * main.units) as totalAmt, main.units,
	main.date_of_service, main.facility_id, DATE_FORMAT(main.date_of_service, '".$date_format_SQL."') as date_of_service, 
	main.primary_provider_id_for_reports  as 'primaryProviderId', main.sec_prov_id,	
	main.proc_balance,	main.pri_due, main.sec_due, main.tri_due, main.pat_due, main.over_payment, main.over_payment, 
	patient_data.lname,patient_data.fname, patient_data.mname".$deptField." 
	FROM report_enc_detail main 
	".$deptJoin."
	JOIN patient_data ON patient_data.id = main.patient_id 
	WHERE (main.date_of_service BETWEEN '2005-01-01' AND '$prevDate') AND main.del_status='0'";	
	if(empty($str_cpt_category_2) == false){
		$begQry.= " and cpt_fee_tbl.cpt_category2 in($str_cpt_category_2)";
	}									
	if(empty($strProviders) === false){
		$begQry.= " and main.primary_provider_id_for_reports IN ($strProviders)";
	}
	if(empty($str_credit_physician) === false){
		$begQry.= " and main.sec_prov_id IN ($str_credit_physician)";
	}
	if($chksamebillingcredittingproviders==1){
		$begQry.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
	}	
	if(empty($strFacilities)===false){
		$begQry.=" AND main.facility_id IN (".$strFacilities.")";
	}
	if(empty($grp_id) === false){
		$begQry .= " and main.gro_id in($grp_id)";
	}
	$begRs=imw_query($begQry);
	$totBal=0;
	while($begRes=imw_fetch_array($begRs)){
		$totalCharges=0;
		$encounter_id= $begRes['encounter_id'];
		$doctor_id = $begRes['primaryProviderId'];
		$facility_id = $begRes['facility_id'];

		//IF BILLING PHYSICIAN NOT SELECTED AND CREDIT PHYSICIAN SELECTED THEN DISPLAY GROUPED BY SECONDARY PHYSICIAN
		if(empty($strProviders) === true && empty($str_credit_physician) === false){
			$doctor_id=$begRes['sec_prov_id'];
		}		
		
		$firstGroupBy = $doctor_id;
		$secGroupBy = $facility_id;
		
		if($groupBy=='grpby_facility'){
			$firstGroupBy = $facility_id;
			$secGroupBy = $doctor_id;
		}
		
		$cat_2= (empty($str_cpt_category_2)==false) ? $begRes['cpt_category2'] : 0; 

		$totalBal = ($begRes['pri_due']+$begRes['sec_due']+$begRes['tri_due']+$begRes['pat_due']) - $begRes['over_payment'];

		//OVER PAYMENT DEDUCTING FROM BALANCE BECAUASE CREDIT ALSO DISPLAYING
		//if($begRes['overPaymentForProc']>0){
			//$totalBal-= $begRes['overPaymentForProc'];	
		//}
		
		if($summary_detail=='summary'){
			$dr_tot_beg_ar_arr[$firstGroupBy][$secGroupBy][$cat_2]+= $totalBal;
		}else{
			$dr_tot_beg_ar_arr[$firstGroupBy][$secGroupBy][$encounter_id]+= $totalBal;
		}	
		
		if($callFrom=='ending'){
			if($totalBal!='0'){
				$tempWithoutCreditCalculated[$encounter_id]+=$totalBal;
			}
		}

		$dr_tot_enc_details[$firstGroupBy][$secGroupBy][$encounter_id]= $begRes;	
		
		$arrEncBalance[$begRes['encounter_id']][]=0;
		$arrEncCharges[$begRes['encounter_id']]+=$totalCharges; 
		$arrEncounters[$begRes['encounter_id']] = $begRes['encounter_id'];
		$arrBalEncs[$begRes['encounter_id']] = $begRes['encounter_id'];
	}

	if($callFrom=='ending'){
		if(sizeof($tempWithoutCreditCalculated)>0){
			$tempEncIds= implode(',',$tempWithoutCreditCalculated);
			$qry="Select encounter_id, totalBalance FROM patient_charge_list WHERE encounter_id IN(".$tempEncIds.")";
			$rs=imw_query($qry);
			while($res=imw_fetch_assoc($rs)){
				if($res['totalBalance']=='0' && $tempWithoutCreditCalculated[$res['encounter_id']]!='0'){
					$balWithoutCreditCalculated+= $tempWithoutCreditCalculated[$res['encounter_id']];
				}
			}
		}
	}
	
	$strAllEncounters = implode(',', $arrAllEncounters);
	$dr_tot_beg_ar_arr['beg_enc']['beg_enc']=$strAllEncounters;
	$dr_tot_beg_ar_arr['balWithoutCreditCalculated']['balWithoutCreditCalculated']=$balWithoutCreditCalculated;
	
	return $dr_tot_beg_ar_arr;
	
} // END ENDING A/R ------------------------------------------------

$dr_tot_beg_ar_arr=getPreviousBal('begining', $startDate, $endDate,$prevDate, $Physician, $sc_name, $groupBy, $grp_id, $str_cpt_cat_2, $summary_detail,$credit_physician,$chksamebillingcredittingproviders);
$dr_tot_bal_arr=getPreviousBal('ending', $startDate, $endDate,$endDate, $Physician, $sc_name, $groupBy, $grp_id, $str_cpt_cat_2, $summary_detail,$credit_physician,$chksamebillingcredittingproviders);
		
?>