<?php
//GETTING AMOUNTS FOR SELECTED DOS.

//CHARGES
$qry ="Select main.charge_list_id, main.encounter_id, main.charge_list_detail_id, main.pri_ins_id,	main.sec_ins_id, main.tri_ins_id, main.operator_id,
main.facility_id, main.gro_id, (main.charges * main.units) as totalAmt, main.units, date_format(main.date_of_service,'".$dateFormat."') as date_of_service,
main.primary_provider_id_for_reports  as 'primaryProviderId', main.sec_prov_id,	
main.proc_code_id, main.dx_id1, main.dx_id2, main.dx_id3, main.dx_id4,
main.proc_balance,	main.pri_due, main.sec_due, main.tri_due, main.pat_due, main.over_payment,
users.lname as physicianLname,users.fname as physicianFname, users.mname as physicianMname,
pos_tbl.pos_prac_code as facilityPracCode,
pos_facilityies_tbl.pos_facility_id, patient_data.id as patient_id,
patient_data.lname,	patient_data.fname, patient_data.mname, patient_data.default_facility, main.write_off 
".$strDeptField." 
FROM report_enc_detail main 
".$strDeptJoin." 
LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id 
LEFT JOIN pos_tbl ON pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
JOIN patient_data on patient_data.id = main.patient_id 
JOIN users on users.id = main.primary_provider_id_for_reports 
WHERE main.del_status='0'";
if($DateRangeFor=='dos'){
	$qry.=" AND (main.date_of_service between '".$startDate."' AND '".$endDate."')";	
}else{	//DOC
	$qry.=" AND (DATE_FORMAT(main.entered_date, '%Y-%m-%d') BETWEEN '".$startDate."' AND '".$endDate."')";	
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
$qry.= " ORDER BY pos_facilityies_tbl.facilityPracCode,users.lname, users.fname";	

$qryRs=imw_query($qry);

while($qryRes = imw_fetch_array($qryRs)){	

	$totalBalance=0;
	$encounter_id = $qryRes['encounter_id'];		
	$charge_list_detail_id = $qryRes['charge_list_detail_id'];
	$doctor_id = $qryRes['primaryProviderId'];
	$facility_id = $qryRes['facility_id'];
	$group_id = $qryRes['gro_id'] ? $qryRes['gro_id'] : 0;
	$opr_id = $qryRes['operator_id'] ? $qryRes['operator_id'] : 0;
	$dept_id = isset($qryRes['departmentId']) ? $qryRes['departmentId'] : 0;
	
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
									
	$selGroup=$arrAllGroups[$qryRes['gro_id']];
	$selDoctor=$providerNameArr[$doctor_id];

	$checkInDataArr[$firstGroupBy][$secGroupBy]=$qryRes;
	$dr_tot_amt_arr[$firstGroupBy][$secGroupBy][]=$qryRes['totalAmt'];

	if($qryRes['over_payment']>0){
		$dr_tot_credit_arr[$firstGroupBy][$secGroupBy][]= $qryRes['over_payment'];
	}
	
	$main_encounter_id_arr[$encounter_id] = $encounter_id;		
	$tempEncArr[$encounter_id]['firstgroupid'] = $firstGroupBy;
	$tempEncArr[$encounter_id]['secondgroupid'] = $secGroupBy;
	$tempChgArr[$charge_list_detail_id] = $encounter_id;
}
unset($qryRs);	

//PAYMENTS & ADJUSTMENTS
if(sizeof($main_encounter_id_arr)>0){

	$main_encounter_id_str = join(',',$main_encounter_id_arr);
	$splitted_encounters = array_chunk($main_encounter_id_arr,1500);

	foreach($splitted_encounters as $arr){
		$str_splitted_encs 	 = implode(',',$arr);

		$qry="Select trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, 
		trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_dot, trans.trans_qry_type,
		DATE_FORMAT(trans.trans_dot,'".$dateFormat."') as trans_dot, trans.trans_del_operator_id  
		FROM report_enc_trans trans 
		WHERE trans.encounter_id IN(".$str_splitted_encs.") 
		AND LOWER(trans.trans_type)!='charges'
		ORDER BY trans.trans_dot, trans.trans_dot_time";
		$rs=imw_query($qry);
		while($res = imw_fetch_assoc($rs)){
			$encounter_id= $res['encounter_id'];
			$chgDetId= $res['charge_list_detail_id'];
			$insCompId = $res['trans_ins_id'];
			$trans_type= strtolower($res['trans_type']);
			$trans_by= strtolower($res['trans_by']);			

			$firstGroupBy = $tempEncArr[$encounter_id]['firstgroupid'];
			$secGroupBy = $tempEncArr[$encounter_id]['secondgroupid'];
			
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
					
					$dr_tot_paid_arr[$firstGroupBy][$secGroupBy][] = $paidForProc;
					
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
					$dr_tot_paid_arr[$firstGroupBy][$secGroupBy][] = $crddbtamt;	
					
				break;
				case 'default_writeoff':
					//TO FETCH ONLY LAST DEFAULT WRITE-OFF FOR PROCEDURE
					$tempLastWriteOff[$chgDetId]= $res['trans_amount'];
				break;
				case 'write off':
				case 'discount':
				case 'over adjustment':
					if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];
					$dr_tot_adj_arr[$firstGroupBy][$secGroupBy][]= $res['trans_amount'];
				break;
				case 'adjustment':
				case 'returned check':
					$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
					$dr_tot_adj_arr[$firstGroupBy][$secGroupBy][]= $res['trans_amount'];
				break;
				case 'refund':
					$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
					$dr_tot_ref_arr[$firstGroupBy][$secGroupBy][]= $res['trans_amount'];
				break;
			}
		}
	
		//DEFAULT WRITE-OFF
		foreach($tempLastWriteOff as $chgDetId => $writeOffAmt){
			$encounter_id= $tempChgArr[$chgDetId];

			$firstGroupBy = $tempEncArr[$encounter_id]['firstgroupid'];
			$secGroupBy = $tempEncArr[$encounter_id]['secondgroupid'];
			$dr_tot_adj_arr[$firstGroupBy][$secGroupBy][]= $writeOffAmt;			
		}
		unset($tempLastWriteOff);
	}	
}


//GETTING BEGINNING/ENDING A/R
function getPreviousBal($callFrom='', $startDate='', $endDate='', $prevDate='', $strProviders='', $strFacilities='', $groupBy='', $grp_id='', $strDepartment = '', $strOperator = '',$strInsCompanies, $dateRangeType='dos'){
	$balWithoutCreditCalculated=0;
	$dr_tot_beg_ar_arr=array();
	$arrBalEncs =array();
	$arrAllEncounters=array();
	$arrEncBalance=array();
	$arrEncounters = array();
	$arrEncDetails= array();
	$arrEncCharges= array();
	
	$deptJoin = ''; $deptField = '';
	if( $strDepartment || $groupBy == 'grpby_department' )
	{
		$deptField = ', cpt_fee_tbl.departmentId';
		$deptJoin = ' JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = main.proc_code_id'; 
	}
	
	$begQry ="Select main.charge_list_id, main.encounter_id, main.charge_list_detail_id, main.pri_ins_id, main.sec_ins_id,
	main.tri_ins_id, main.operator_id, main.gro_id, (main.charges * main.units) as totalAmt, main.units,
	main.date_of_service, main.facility_id,
	main.primary_provider_id_for_reports  as 'primaryProviderId', main.sec_prov_id,	
	main.proc_balance,	main.pri_due, main.sec_due, main.tri_due, main.pat_due, main.over_payment ".$deptField."
	FROM report_enc_detail main 
	".$deptJoin."
	JOIN patient_data ON patient_data.id = main.patient_id 
	WHERE main.del_status='0'";	
	if($dateRangeType=='dos'){
		$begQry.=" AND (main.date_of_service between '2005-01-01' AND '$prevDate')";	
	}else{	//DOC
		$begQry.=" AND (DATE_FORMAT(main.entered_date, '%Y-%m-%d') BETWEEN '2005-01-01' AND '$prevDate')";	
	}
	if(empty($strProviders)===false){
		$begQry.=" AND main.primary_provider_id_for_reports IN (".$strProviders.")";
	}
	if(empty($strFacilities)===false){
		$begQry.=" AND main.facility_id IN (".$strFacilities.")";
	}
	if(empty($grp_id) === false){
		$begQry .= " and main.gro_id in($grp_id)";
	}
	if(empty($strOperator) === false){
		$begQry .= " and main.operator_id in($strOperator)";
	}
	if(empty($strDepartment) === false){
		$begQry .= " and cpt_fee_tbl.departmentId in($strDepartment)";
	}

	if(empty($strInsCompanies) === false){
		$begQry.= " and ( main.pri_ins_id in($strInsCompanies) 
								OR	main.sec_ins_id in($strInsCompanies)
								OR	main.tri_ins_id in($strInsCompanies) )";
	}
		
	$begRs=imw_query($begQry);
	$totBal=0;
	while($begRes=imw_fetch_array($begRs)){
		$totalCharges=0;
		$doctor_id = $begRes['primaryProviderId'];
		$facility_id = $begRes['facility_id'];
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

		$totalBal = $begRes['pri_due']+$begRes['sec_due']+$begRes['tri_due']+$begRes['pat_due'];

		//OVER PAYMENT DEDUCTING FROM BALANCE BECAUASE CREDIT ALSO DISPLAYING
		//if($begRes['overPaymentForProc']>0){
			//$totalBal-= $begRes['overPaymentForProc'];	
		//}
		
		$dr_tot_beg_ar_arr[$firstGroupBy][$secGroupBy]+= $totalBal;		
		
		if($callFrom=='ending'){
			if($begRes['totalBalance']=='0' && $totalBal!='0'){
				$balWithoutCreditCalculated+= $totalBal;
			}
		}
		
		$arrEncBalance[$begRes['encounter_id']][]=0;
		$arrEncCharges[$begRes['encounter_id']]+=$totalCharges; 
		$arrEncounters[$begRes['encounter_id']] = $begRes['encounter_id'];
		$arrBalEncs[$begRes['encounter_id']] = $begRes['encounter_id'];
	}

	$strAllEncounters = implode(',', $arrAllEncounters);
	$dr_tot_beg_ar_arr['beg_enc']['beg_enc']=$strAllEncounters;
	$dr_tot_beg_ar_arr['balWithoutCreditCalculated']['balWithoutCreditCalculated']=$balWithoutCreditCalculated;
	
	return $dr_tot_beg_ar_arr;
	
} // END ENDING A/R ------------------------------------------------

$dr_tot_beg_ar_arr=getPreviousBal('begining', $startDate, $endDate,$prevDate, $Physician, $sc_name, $groupBy, $grp_id,$department,$operator_id,$insCompanies, $DateRangeFor);
$dr_tot_bal_arr=getPreviousBal('ending', $startDate, $endDate,$endDate, $Physician, $sc_name, $groupBy, $grp_id,$department,$operator_id,$insCompanies, $DateRangeFor);



?>