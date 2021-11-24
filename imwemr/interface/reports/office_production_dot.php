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
	if(trim($ins_type) == ''){
		$qryBigPart .= " and (main.pri_ins_id in ($insuranceName)
		or main.sec_ins_id in ($insuranceName)
		or main.tri_ins_id in ($insuranceName))";
	}
	else{
		$ins_type_arr=explode(',',$ins_type);
		$qryBigPart .= " and (";
		for($i=0;$i<count($ins_type_arr);$i++){
			$ins_nam=$ins_type_arr[$i];
			if(trim($ins_nam)!='Self Pay')
			{
				$mul_or="";
				if($i>0){
					$mul_or=" or ";
				}
				$qryBigPart .= " $mul_or main.$ins_nam in ($insuranceName)";
			}
		}
		$qryBigPart .= " )";
	}
}
//------------------


//TRANSACTIONS TABLE
$qry="Select trans.report_trans_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, 
trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_qry_type, trans.parent_id, trans.facility_id,
trans.trans_dot, trans.trans_del_operator_id  
FROM report_enc_trans trans WHERE trans.trans_type!='charges' 
AND (trans.trans_del_operator_id='0' OR (trans.trans_del_operator_id>0 && trans.trans_del_date<='$End_date'))";
if(empty($sc_name)==false){
	$qry.=" AND trans.facility_id IN(".$sc_name.")";	
}
if($DateRangeFor=='transaction_date'){
	$qry.=" AND (trans_dot BETWEEN '$Start_date' and '$End_date')";
}else{
	$qry.=" AND (trans_dop BETWEEN '$Start_date' and '$End_date')";	
}
if(empty($operatorName)==false){
	$qry.= " AND trans.trans_operator_id in($operatorName)";
}
$qry.=" ORDER BY trans.master_tbl_id, trans.report_trans_id, trans.trans_dot, trans.trans_dot_time";
$rs=imw_query($qry);
$encounterIdArr=array();
while($res = imw_fetch_assoc($rs))
{
	$report_trans_id=$res['report_trans_id'];
	$encounter_id= $res['encounter_id'];
	$chgDetId= $res['charge_list_detail_id'];
	$insCompId = $res['trans_ins_id'];
	$encounterIdArr[$res['encounter_id']] = $res['encounter_id'];
	$allTransChgDetId[$chgDetId]=$chgDetId;
	$trans_type= strtolower($res['trans_type']);
	$trans_by= strtolower($res['trans_by']);			
    $payloc=$res['facility_id'];
    $arrChgDetIds[$chgDetId]=$chgDetId;

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
                }else{
                    $paidForProc=0;
                }
            }

            $arrTempPayment[$chgDetId]+= $paidForProc;
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
                $pay_crd_deb_arr[$chgDetId]['Insurance']+= $crddbtamt;
            }else{
                $pay_crd_deb_arr[$chgDetId]['Patient']+= $crddbtamt;				
            }
            $arrTempPayment[$chgDetId]+= $crddbtamt;
        
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
            
            $arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
        break;
        case 'over adjustment':
            if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];

            $prevFetchedAmt=0;
            if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
                $prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
            }
            $res['trans_amount']+=$prevFetchedAmt;
            
            $arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
        break;
        case 'adjustment':
            $res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

            $prevFetchedAmt=0;
            if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
                $prevFetchedAmt = $tempRecordData[$res['parent_id']];
            }
            $res['trans_amount']+=$prevFetchedAmt;
            
            $arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
        break;
        case 'returned check':
            $res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

            $prevFetchedAmt=0;
            if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
                $prevFetchedAmt = $tempRecordData[$res['parent_id']];
            }
            $res['trans_amount']+=$prevFetchedAmt;
            
            $arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
        break;
        case 'refund':
            $res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

            $prevFetchedAmt=0;
            if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
                $prevFetchedAmt = $tempRecordData[$res['parent_id']];
            }
            $res['trans_amount']+=$prevFetchedAmt;
            
            if($viewBy=='grpby_location_physician' || $viewBy=='grpby_cpt_cat' || $viewBy=='grpby_location_cpt_cat'){
                $arrRefundAmt[$chgDetId]+= $res['trans_amount'];
            }else{
                $arrAdjustmentAmtDet[$chgDetId]+= $res['trans_amount'];
                $arrTempRefund[$chgDetId]+= $res['trans_amount'];                
            }
        break;
	}
}


//GET FIRST POSTED ENCOUTNERS FETCHED SEPARATELY DUE TO OPERATOR SELECTION
$qry="Select main.charge_list_detail_id FROM report_enc_detail main
JOIN patient_data on patient_data.id = main.patient_id
JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = main.proc_code_id 
WHERE (main.first_posted_date BETWEEN '$Start_date' AND '$End_date') 
AND (main.del_status='0' OR (main.del_status='1' AND DATE_FORMAT(main.trans_del_date, '%Y-%m-%d')>'$End_date')) ".$qryBigPart;
if(empty($sc_name) == false){
	$qry .= " and main.facility_id IN ($sc_name)";
}
if(empty($operatorName)==false){
	$qry.= " AND main.operator_id in($operatorName)";
}
if(trim($cpt_cat_2) != ''){
	$qry.= " AND cpt_fee_tbl.cpt_category2 IN ($cpt_cat_2)";							
}
$rs=imw_query($qry);
while($res=imw_fetch_assoc($rs)){
    $arrChgDetIds[$res['charge_list_detail_id']]=$res['charge_list_detail_id'];
}unset($rs);


//MAIN QUERY
if(sizeof($arrChgDetIds)>0){

    foreach($arrChgDetIds as $chgDetId)
    {
        $chg_for_temp_query.='('.$chgDetId.'),';
    }
    if(empty($chg_for_temp_query)==false){
        $chg_for_temp_query=substr($chg_for_temp_query,0, -1);
    }  

    //CREATE TEMP TABLE AND INSERT DATA
	$temp_join_part='';
	if(empty($chg_for_temp_query)==false){
		$tmp_table="IMWTEMP_report_office_production_".time().'_'.$_SESSION["authId"];
		imw_query("DROP TEMPORARY TABLE IF EXISTS ".$tmp_table);
		imw_query("CREATE TEMPORARY TABLE ".$tmp_table." (chg_id INT)");
		imw_query("INSERT INTO $tmp_table (chg_id) VALUES ".$chg_for_temp_query);
		$temp_join_part=" INNER JOIN ".$tmp_table." t_tbl ON main.charge_list_detail_id = t_tbl.chg_id";
	}
	unset($chg_for_temp_query);

	$qry="Select main.charge_list_id, main.encounter_id, main.charge_list_detail_id, main.pri_ins_id, main.sec_ins_id, main.tri_ins_id,main.operator_id,
	(main.charges * main.units) as totalAmt, main.units, date_format(main.date_of_service,'".$dateFormat."') as date_of_service,
	DATE_FORMAT(main.trans_del_date, '%Y-%m-%d') as trans_del_date,
	main.primary_provider_id_for_reports  as 'primaryProviderId', main.sec_prov_id,	main.billing_facility_id,
	main.proc_code_id, main.dx_id1, main.dx_id2, main.dx_id3, main.dx_id4,
	main.proc_balance,	main.pri_due, main.sec_due, main.tri_due, main.pat_due, main.del_status, main.first_posted_date, main.over_payment,
	main.submitted, main.over_payment, main.approved_amt,	
	users.lname as physicianLname,users.fname as physicianFname, users.mname as physicianMname,
	pos_tbl.pos_prac_code as facilityPracCode,
	pos_facilityies_tbl.pos_facility_id, patient_data.id as patient_id,
	patient_data.lname,	patient_data.fname, patient_data.mname, patient_data.default_facility,
	main.write_off, cpt_fee_tbl.cpt_prac_code, cpt_fee_tbl.cpt4_code, cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_category2, cpt_fee_tbl.cpt_cat_id  
    FROM report_enc_detail main 
    ".$temp_join_part."
	JOIN patient_data on patient_data.id = main.patient_id 
	LEFT JOIN users on users.id = main.primary_provider_id_for_reports 
    LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id
	LEFT JOIN pos_tbl ON pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
	LEFT JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = main.proc_code_id
	WHERE ((main.del_status='0') OR (main.del_status='1' AND DATE_FORMAT(main.trans_del_date, '%Y-%m-%d')>'$End_date'))";
	$qry.= $qryBigPart;
    if(empty($sc_name) == false){
        $qry .= " and main.facility_id IN ($sc_name)";
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
	if(trim($cpt_cat_2) != ''){
		$qry.= " AND cpt_fee_tbl.cpt_category2 IN ($cpt_cat_2)";							
	}
	if($pureSelfPay==true)$qry .= " and main_details.proc_selfpay=1";
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
        $qry.= "ORDER BY main.date_of_service, patient_data.lname,patient_data.fname,main.encounter_id";
    }
	$res=imw_query($qry);
	
	$main_encounter_id_arr = array();
	$facilityNameArr = array();
	$physician_initial_arr = array();
	$arrPatNoFacility=array();
    $arrAllEncIds=array();
    $mainResTotalCharges=$totalNetCharges=$totalNetCredit=0;
    
    while($rs = imw_fetch_assoc($res))
    {
        $encounter_id = $rs['encounter_id'];
        $primaryProviderId = $rs['primaryProviderId'];
        $operator_id = $rs['operator_id'];
        $pos_facility_id = $rs['pos_facility_id'];	
        $pos_facility_id = (empty($pos_facility_id)==true) ? '0' : $pos_facility_id;
        $chgDetId = $rs['charge_list_detail_id'];
        $submitted= $rs["submitted"];
        $cpt_id=$rs['proc_code_id'];
    
        $firstGrpBy= $primaryProviderId;
        if($viewBy=='grpby_crediting_physician'){
            $firstGrpBy= $rs['sec_prov_id'];
        }else if($viewBy=='grpby_location' || $viewBy=='grpby_location_physician' || $viewBy=='grpby_location_cpt_cat'){
            $firstGrpBy= $pos_facility_id;
        }else if($viewBy=='grpby_cpt_cat'){
            $firstGrpBy= $rs['cpt_cat_id'];
        }        

        $cat_2= (empty($cpt_cat_2)==false) ? $rs['cpt_category2'] : 0; 	

        //IF NOT DELETED OR DELETED AFTER FIRST POSTED DATE ONLY THEN FETCH THE RECORD
        if($rs['del_status']=='0' 
		|| $rs['first_posted_date']=='0000-00-00' 
        || ($rs['del_status']=='1' && $rs['first_posted_date']!='0000-00-00' && $rs['trans_del_date'] >= $rs['first_posted_date']))
        {
			if(!($submitted=='true' && $rs['first_posted_date']>=$Start_date && $rs['first_posted_date']<=$End_date && (sizeof($arrSelOprators)<=0 || $arrSelOprators[$oprId]))){
                $rs['totalAmt']='0';
                $rs['approved_amt']='0';
			}
      
            if($processReport == "Summary")
            {
                if($rs['totalAmt']!='0' && $rs['totalAmt']!='')
                {
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

                        //TOTAL NET CHARGES
                        $totalNetCharges+= $rs['totalAmt'];
                    }
                    elseif($viewBy=='grpby_location')
                    {
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
                }

                if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician'){
                    //PAYMENTS
                    if($arrTempPayment[$chgDetId])
                    {
                        $arrPaymentAmt[$firstGrpBy]+= $arrTempPayment[$chgDetId];
                        //TOTAL NET CREDIT
                        $totalNetCredit+=$arrTempPayment[$chgDetId];  
                    }
                    //WRITE-OFF && ADJUSTMENTS
                    if($normalWriteOffAmt[$chgDetId] || $arrAdjustmentAmtDet[$chgDetId])
                    {
                        $arrAdjustmentAmt[$firstGrpBy]+= $normalWriteOffAmt[$chgDetId] + $arrAdjustmentAmtDet[$chgDetId];
                        //TOTAL NET CHARGES
                        $totalNetCharges-= $normalWriteOffAmt[$chgDetId] + $arrAdjustmentAmtDet[$chgDetId];
                    }    
                    //REFUND
                    if($arrTempRefund[$chgDetId])
                    {
                        $arrRefundAmt[$firstGrpBy]+= $arrTempRefund[$chgDetId];
                        //TOTAL NET CREDIT
                        $totalNetCredit-=$arrTempRefund[$chgDetId];
                    }            
                }elseif($viewBy=='grpby_location')
                {
                    //PAYMENTS
                    if($patPayDetArr[$chgDetId])
                    {
                        $patPayDetArr[$firstGrpBy][$cat_2]['patPaid']+= $patPayDetArr[$chgDetId]['patPaid'];
                        $patPayDetArr[$firstGrpBy][$cat_2]['insPaid']+= $patPayDetArr[$chgDetId]['insPaid'];

                        $pay_crd_deb_arr_summmary[$firstGrpBy][$cat_2]['Insurance']+= $pay_crd_deb_arr[$chgDetId]['Insurance'];
                        $pay_crd_deb_arr_summmary[$firstGrpBy][$cat_2]['Patient']+= $pay_crd_deb_arr[$chgDetId]['Patient'];
                    }
                    //WRITE-OFF && ADJUSTMENTS
                    if($normalWriteOffAmt[$chgDetId] || $arrAdjustmentAmtDet[$chgDetId])
                    {
                        $arrAdjustmentAmt[$firstGrpBy][$cat_2]+= $normalWriteOffAmt[$chgDetId] + $arrAdjustmentAmtDet[$chgDetId];
                    }    
                    //REFUND
                    if($arrTempRefund[$chgDetId])
                    {
                        $arrRefundAmt[$firstGrpBy][$cat_2]+= $arrTempRefund[$chgDetId];
                    }            
                }elseif($viewBy=='grpby_cpt_cat'){
                    $mainResArr[$firstGrpBy][$chgDetId] = $rs;
                }elseif($viewBy=='grpby_location_cpt_cat'){
                    $mainResArr[$firstGrpBy][$rs['cpt_cat_id']][$chgDetId] = $rs;
                }else{                    
                    $mainResArr[$firstGrpBy][$primaryProviderId][$cat_2][$chgDetId] = $rs;
                }
            }
            else{ //detail	
                
                if($viewBy=='grpby_physician' || $viewBy=='grpby_crediting_physician' || $viewBy=='grpby_location'){
                    $mainResArr[$firstGrpBy][$chgDetId] = $rs;
                }
                else if($viewBy=='grpby_location_physician')
                {
                    $mainResArr[$firstGrpBy][$primaryProviderId][$chgDetId] = $rs;    
                }
                else if($viewBy=='grpby_cpt_cat')
                {
                    $mainResArr[$firstGrpBy][$cpt_id][$cat_2][$chgDetId] = $rs;
                }
                elseif($viewBy=='grpby_location_cpt_cat')
                {
                    $mainResArr[$firstGrpBy][$rs['cpt_cat_id']][$cpt_id][$cat_2][$chgDetId] = $rs;
                }					
            }	
        }
    }
    unset($arrTempPayment);
    unset($arrTempRefund);

    
}
?>