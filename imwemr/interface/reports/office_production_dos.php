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

$arrPaymentAmt=array();
$normalWriteOffAmt=array();
$arrAdjustmentAmt=array();
$main_encounter_id_arr = array();
$facilityNameArr = array();
$physician_initial_arr = array();
$arrPatNoFacility=array();
$arrEncGroupMap=array();
$arrChgGroupMap=array();
$arrChgDetIds=array();
$mainResArr=array();
$chg_for_temp_query='';
$totalNetCharges=0;

//--- GET ALL CHARGES ----
$qry = "Select main.charge_list_id, main.encounter_id, main.charge_list_detail_id, main.billing_facility_id, main.pri_ins_id,	
main.sec_ins_id, main.tri_ins_id, main.operator_id, 
(main.charges * main.units) as totalAmt, main.units, main.approved_amt, date_format(main.date_of_service,'".$dateFormat."') as date_of_service,
main.primary_provider_id_for_reports  as 'primaryProviderId', main.sec_prov_id,	
main.proc_code_id, main.dx_id1, main.dx_id2, main.dx_id3, main.dx_id4,
main.proc_balance,	main.pri_due, main.sec_due, main.tri_due, main.pat_due, main.over_payment,
users.lname as physicianLname,users.fname as physicianFname, users.mname as physicianMname,
pos_tbl.pos_prac_code as facilityPracCode,
pos_facilityies_tbl.pos_facility_id, patient_data.id as patient_id,
patient_data.lname,	patient_data.fname, patient_data.mname, patient_data.default_facility,
main.write_off, cpt_fee_tbl.cpt_prac_code, cpt_fee_tbl.cpt4_code, cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_category2, cpt_fee_tbl.cpt_cat_id  
FROM report_enc_detail main 
JOIN patient_data on patient_data.id = main.patient_id 
LEFT JOIN users on users.id = main.primary_provider_id_for_reports 
LEFT JOIN users sec_user on sec_user.id = main.sec_prov_id 
LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id
LEFT JOIN pos_tbl ON pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
LEFT JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = main.proc_code_id
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
if(trim($cpt_cat_2) != ''){
	$qry.= " AND cpt_fee_tbl.cpt_category2 IN ($cpt_cat_2)";							
}
if($pureSelfPay==true)$qry.= " and main.proc_selfpay=1";

//QUERY GROUPING
if($viewBy=='grpby_physician')
{
    $qry.= " ORDER BY users.lname, users.fname, patient_data.lname, patient_data.fname, patient_data.mname";
}elseif($viewBy=='grpby_crediting_physician')
{
    $qry.= " ORDER BY sec_user.lname, sec_user.fname, patient_data.lname, patient_data.fname, patient_data.mname";
}elseif($viewBy=='grpby_facility')
{
    $qry.= " ORDER BY pos_facilityies_tbl.facilityPracCode, patient_data.lname, patient_data.fname, patient_data.mname";
}elseif($viewBy=='grpby_cpt_cat')
{
    $qry.= " ORDER BY cpt_fee_tbl.cpt_prac_code, patient_data.lname, patient_data.fname, patient_data.mname";
}else{
    $qry.= " ORDER BY main.date_of_service, patient_data.lname,patient_data.fname,main.encounter_id";
}
$res=imw_query($qry);


while($rs = imw_fetch_assoc($res)){
	$encounter_id = $rs['encounter_id'];
	$main_encounter_id_arr[$encounter_id] = $encounter_id;
	$primaryProviderId = $rs['primaryProviderId'];
	$operator_id = $rs['operator_id'];
	$deptId= $arrDeptOfCptCodes[$rs["proc_code_id"]];
	$pos_facility_id = $rs['pos_facility_id'];	
	$pos_facility_id = (empty($pos_facility_id)==true) ? '0' : $pos_facility_id;
	$chgDetId = $rs['charge_list_detail_id'];
	$cpt_id=$rs['proc_code_id'];
	$arrChgDetIds[$chgDetId]=$chgDetId;
	

	$chg_for_temp_query.='('.$chgDetId.'),';

	$firstGrpBy= $primaryProviderId;
	if($viewBy=='grpby_crediting_physician'){
		$firstGrpBy= $rs['sec_prov_id'];
	}else if($viewBy=='grpby_location' || $viewBy=='grpby_location_physician' || $viewBy=='grpby_location_cpt_cat'){
		$firstGrpBy= $pos_facility_id;
	}else if($viewBy=='grpby_cpt_cat'){
		$firstGrpBy= $rs['cpt_cat_id'];
	}

	//$arrEncGroupMap[$encounter_id]=$firstGrpBy;
	$arrChgGroupMap[$chgDetId]=$firstGrpBy;

	$cat_2= (empty($cpt_cat_2)==false) ? $rs['cpt_category2'] : 0; 	
	$arrChgdetid_cat2[$chgDetId]=$cat_2;
	
	if($processReport == "Summary")
	{
		//GROUP BY PROVIDED/CREDITIING-PROVIDER
		if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician'){
			if($rs['cpt_category2']=='1')
			{
				$mainResArr[$firstGrpBy]['charges_service']+= $rs['totalAmt'];
			}elseif($rs['cpt_category2']=='2')
			{
				$mainResArr[$firstGrpBy]['charges_material']+= $rs['totalAmt'];
			}else{
				$mainResArr[$firstGrpBy]['charges_other']+= $rs['totalAmt'];
			}
			
			$mainResArr[$firstGrpBy]['charges']+= $rs['totalAmt'];
			$mainResTotalCharges+= $rs['totalAmt'];
		}
		elseif($viewBy=='grpby_location')
		{ //GROUP BY LOCATION
			$mainResArr[$firstGrpBy][$cat_2]['charges']+= $rs['totalAmt'];
			$mainResArr[$firstGrpBy][$cat_2]['ins_due']+= $rs['pri_due']+ $rs['sec_due']+ $rs['tri_due'];
			$mainResArr[$firstGrpBy][$cat_2]['pat_due']+= $rs['pat_due'];

            //BALANCE
            $balAmt=0;
            if($rs["proc_balance"]>0){
                $balAmt= $rs['proc_balance'];
            }else{
                if($rs['over_payment']>0){
                    $balAmt= $rs['proc_balance'] - $rs['over_payment'];
                }else{
                    $balAmt= $rs['proc_balance'];
                }
			}
			$mainResArr[$firstGrpBy][$cat_2]['balance']+= $balAmt;
		}
		elseif($viewBy=='grpby_cpt_cat')
		{
			$mainResArr[$firstGrpBy][$chgDetId] = $rs;
		}		
		elseif($viewBy=='grpby_location_cpt_cat')
		{
			$mainResArr[$firstGrpBy][$rs['cpt_cat_id']][$chgDetId] = $rs;
		}				
		else{ 
			$mainResArr[$firstGrpBy][$primaryProviderId][$cat_2][$chgDetId] = $rs;
		}
	} 
	else{	//detail	 
		if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician' || $viewBy=='grpby_location')
		{
			$mainResArr[$firstGrpBy][$chgDetId] = $rs;
		}
		elseif($viewBy=='grpby_location_physician')
		{ //GROUP BY LOCATION
			$mainResArr[$firstGrpBy][$primaryProviderId][$chgDetId] = $rs;
		}			
		elseif($viewBy=='grpby_cpt_cat')
		{
			$mainResArr[$firstGrpBy][$cpt_id][$cat_2][$chgDetId] = $rs;
		}					
		elseif($viewBy=='grpby_location_cpt_cat')
		{
			$mainResArr[$firstGrpBy][$rs['cpt_cat_id']][$cpt_id][$cat_2][$chgDetId] = $rs;
		}					

	}
}unset($rs);

if(empty($chg_for_temp_query)==false){
	$chg_for_temp_query=substr($chg_for_temp_query,0, -1);
} 

//TRANSACTIONS TABLE
if(sizeof($arrChgDetIds)>0){

	//CREATE TEMP TABLE AND INSERT DATA
	$temp_join_part='';
	if(empty($chg_for_temp_query)==false){
		$tmp_table="IMWTEMP_report_office_production_".time().'_'.$_SESSION["authId"];
		imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);
		imw_query("CREATE TEMPORARY TABLE ".$tmp_table." (chg_id INT)");
		imw_query("INSERT INTO $tmp_table (chg_id) VALUES ".$chg_for_temp_query);
		$temp_join_part=" INNER JOIN ".$tmp_table." t_tbl ON trans.charge_list_detail_id = t_tbl.chg_id";
	}
	unset($chg_for_temp_query);

	$qry="Select trans.report_trans_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, 
	trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_qry_type, trans.parent_id,
	trans.trans_dot, trans.trans_del_operator_id  
	FROM report_enc_trans trans 
	".$temp_join_part."
	WHERE trans.trans_type!='charges' 
	ORDER BY trans.trans_dot, trans.trans_dot_time, trans.report_trans_id";
	$rs=imw_query($qry);
	while($res = imw_fetch_assoc($rs))
	{
		$report_trans_id=$res['report_trans_id'];
		$encounter_id= $res['encounter_id'];
		$chgDetId= $res['charge_list_detail_id'];
		$insCompId = $res['trans_ins_id'];
		$trans_type= strtolower($res['trans_type']);
		$trans_by= strtolower($res['trans_by']);	
		$firstGrpBy=$arrChgGroupMap[$chgDetId];
		$cat_2=$arrChgdetid_cat2[$chgDetId];
		
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

					if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician' || $viewBy=='grpby_location_physician' || $viewBy=='grpby_cpt_cat' || $viewBy=='grpby_location_cpt_cat'){
						$patPayDetArr[$chgDetId]['patPaid']+= $paidForProc;
					}else if($viewBy=='grpby_location'){
						if($processReport == "Summary"){
							$patPayDetArr[$firstGrpBy][$cat_2]['patPaid']+= $paidForProc;
						}else{
							$patPayDetArr[$chgDetId]['patPaid']+= $paidForProc;
						}
					}

				}else if($trans_by == 'insurance'){
					if(empty($insuranceName)===true || (empty($insuranceName)===false && $arrInsurance[$insCompId])){	
						if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician' || $viewBy=='grpby_location_physician' || $viewBy=='grpby_cpt_cat' || $viewBy=='grpby_location_cpt_cat'){
							$patPayDetArr[$chgDetId]['insPaid']+= $paidForProc;
						}else if($viewBy=='grpby_location'){
							if($processReport == "Summary"){
								$patPayDetArr[$firstGrpBy][$cat_2]['insPaid']+= $paidForProc;
							}else{
								$patPayDetArr[$chgDetId]['insPaid']+= $paidForProc;
							}
						}							
					}else{
						$paidForProc=0;
					}
				}

				$arrPaymentAmt[$firstGrpBy]+= $paidForProc;
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
			
				if($trans_by=='insurance'){
					if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician' || $viewBy=='grpby_location_physician' || $viewBy=='grpby_cpt_cat' || $viewBy=='grpby_location_cpt_cat'){					
						$pay_crd_deb_arr[$chgDetId]['Insurance']+= $crddbtamt;
					}else if($viewBy=='grpby_location'){
						if($processReport == "Summary"){
							$pay_crd_deb_arr_summary[$firstGrpBy][$cat_2]['Insurance']+= $crddbtamt;
						}else{
							$pay_crd_deb_arr[$chgDetId]['Insurance']+= $crddbtamt;
						}
					}
				}else{
					if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician' || $viewBy=='grpby_location_physician' || $viewBy=='grpby_cpt_cat' || $viewBy=='grpby_location_cpt_cat'){					
						$pay_crd_deb_arr[$chgDetId]['Patient']+= $crddbtamt;
					}else if($viewBy=='grpby_location'){
						if($processReport == "Summary"){
							$pay_crd_deb_arr_summary[$firstGrpBy][$cat_2]['Patient']+= $crddbtamt;
						}else{
							$pay_crd_deb_arr[$chgDetId]['Patient']+= $crddbtamt;
						}
					}
				}
				$arrPaymentAmt[$firstGrpBy]+= $crddbtamt;
			
			break;
			case 'default_writeoff':
				$normalWriteOffAmt[$chgDetId]= $res['trans_amount'];
			break;
			case 'write off':
			case 'discount':
				if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];

				$prevFetchedAmt=0;
				if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
					$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
				}
				$res['trans_amount']+=$prevFetchedAmt;
				
				if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician' || $viewBy=='grpby_location_physician' || $viewBy=='grpby_cpt_cat' || $viewBy=='grpby_location_cpt_cat'){					
					$arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
					$arrAdjustmentAmt[$firstGrpBy]+= $res['trans_amount'];
				}else if($viewBy=='grpby_location'){
					$arrAdjustmentAmt[$firstGrpBy][$cat_2]+= $res['trans_amount'];
					$arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
				}	
			break;
			case 'over adjustment':
				if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];

				$prevFetchedAmt=0;
				if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
					$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
				}
				$res['trans_amount']+=$prevFetchedAmt;
				
				if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician' || $viewBy=='grpby_location_physician' || $viewBy=='grpby_cpt_cat' || $viewBy=='grpby_location_cpt_cat'){	
					$arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
					$arrAdjustmentAmt[$firstGrpBy]+= $res['trans_amount'];
				}else if($viewBy=='grpby_location'){
					$arrAdjustmentAmt[$firstGrpBy][$cat_2]+= $res['trans_amount'];
					$arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
				}
			break;
			case 'adjustment':
				$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

				$prevFetchedAmt=0;
				if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
					$prevFetchedAmt = $tempRecordData[$res['parent_id']];
				}
				$res['trans_amount']+=$prevFetchedAmt;
				
				if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician' || $viewBy=='grpby_location_physician' || $viewBy=='grpby_cpt_cat' || $viewBy=='grpby_location_cpt_cat'){	
					$arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
					$arrAdjustmentAmt[$firstGrpBy]+= $res['trans_amount'];
				}else if($viewBy=='grpby_location'){
					$arrAdjustmentAmt[$firstGrpBy][$cat_2]+= $res['trans_amount'];
					$arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
				}
			break;
			case 'returned check':
				$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

				$prevFetchedAmt=0;
				if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
					$prevFetchedAmt = $tempRecordData[$res['parent_id']];
				}
				$res['trans_amount']+=$prevFetchedAmt;
				
				if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician' || $viewBy=='grpby_location_physician' || $viewBy=='grpby_cpt_cat' || $viewBy=='grpby_location_cpt_cat'){	
					$arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
					$arrAdjustmentAmt[$firstGrpBy]+= $res['trans_amount'];
				}else if($viewBy=='grpby_location'){
					$arrAdjustmentAmt[$firstGrpBy][$cat_2]+= $res['trans_amount'];
					$arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
				}
			break;
			case 'refund':
				$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

				$prevFetchedAmt=0;
				if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
					$prevFetchedAmt = $tempRecordData[$res['parent_id']];
				}
				$res['trans_amount']+=$prevFetchedAmt;
				
				if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician'){	
					$arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
					$arrRefundAmt[$firstGrpBy]+= $res['trans_amount'];
				}else if($viewBy=='grpby_location'){
					$arrRefundAmt[$firstGrpBy][$cat_2]+= $res['trans_amount'];
					$arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
				}else if($viewBy=='grpby_location_physician'){
					if($processReport == "Summary"){
						$arrRefundAmt[$chgDetId]+= $res['trans_amount'];
					}else{
						$arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
					}
				}else if($viewBy=='grpby_cpt_cat' || $viewBy=='grpby_location_cpt_cat'){
					$arrRefundAmt[$chgDetId]+= $res['trans_amount'];
				}				

			break;
		}
	}
	//DROP TEMP TABLE
	imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);


	//SETTING NORMAL WRITE-OFF ARRAY
	foreach($normalWriteOffAmt as $chgDetId =>$amt)
	{
		if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician' || $viewBy=='grpby_location_physician'){	
			$firstGrpBy=$arrChgGroupMap[$chgDetId];	
			$arrAdjustmentAmt[$firstGrpBy]+= $amt;
		}else if($viewBy=='grpby_location'){
			$firstGrpBy=$arrChgGroupMap[$chgDetId];	
			$cat_2=$arrChgdetid_cat2[$chgDetId];
			$arrAdjustmentAmt[$firstGrpBy][$cat_2]+= $amt;
		}	
	}

	//GETTING NET CHARGES AMOUNT
	$totalNetCredit=0;
	if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician'){		
		foreach($mainResArr as $firstGrpBy=> $firstGrpData)
		{
			$totalNetCharges+= $firstGrpData['charges'] - $arrAdjustmentAmt[$firstGrpBy];

			//TOTAL NET CREDIT
			$totalNetCredit+=$arrPaymentAmt[$firstGrpBy]-$arrRefundAmt[$firstGrpBy];
		}
	}
}
?>