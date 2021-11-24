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
FILE : provider_analytic_result_dot.php
PURPOSE :  RESULT FOR PAYROLL REPORT
ACCESS TYPE : DIRECT
*/


//FIRST GETTING CHARGE LIST IDS OF TRANSACTIONS
$chargelistIdArr=array();
$qry="Select trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, 
trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_dot, trans.trans_qry_type,
DATE_FORMAT(trans.trans_dot,'".$dateFormat."') as trans_dot, trans.trans_del_operator_id  
FROM report_enc_trans trans 
WHERE LOWER(trans.trans_type)!='charges'";
if($DateRangeFor=='transaction_date'){
	$qry.=" AND ((trans_del_operator_id='0' AND (trans_dot BETWEEN '$StartDate' and '$EndDate')) 
	OR (trans_del_operator_id>0 AND (trans_dot BETWEEN '$StartDate' and '$EndDate') AND (trans_del_date BETWEEN '$StartDate' and '$EndDate')))";
}else{
	$qry.=" AND ((trans_del_operator_id='0' AND (trans_dop BETWEEN '$StartDate' and '$EndDate')) 
	OR (trans_del_operator_id>0 AND (trans_dop BETWEEN '$StartDate' and '$EndDate') AND (trans_del_date BETWEEN '$StartDate' and '$EndDate')))";
}
$qry.=" ORDER BY trans.trans_dot, trans.trans_dot_time";
$rs=imw_query($qry);
while($res = imw_fetch_assoc($rs)){
	$chargelistIdArr[$res['charge_list_detail_id']] =$res['charge_list_detail_id'];
}

//GET FIRST POSTED ENCOUTNERS
$firstPostedChgIds=array();
$qry="Select charge_list_detail_id FROM report_enc_detail WHERE (first_posted_date BETWEEN '$StartDate' AND '$EndDate')";
if(trim($grp_id) != ''){
	$qry .= " and gro_id IN ($grp_id)";
}
if(trim($filing_provider) != ''){
	$qry .= " and primary_provider_id_for_reports IN ($filing_provider)";
}
if(trim($str_crediting_provider) != ''){
	$qry .= " and main.sec_prov_id IN ($str_crediting_provider)";
}
if($chksamebillingcredittingproviders==1){
	$qry.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
}
$rs=imw_query($qry);
while($res=imw_fetch_assoc($rs)){
	$firstPostedChgIds[$res['charge_list_detail_id']]=$res['charge_list_detail_id'];
	$chargelistIdArr[$res['charge_list_detail_id']] =$res['charge_list_detail_id'];
}unset($rs);

//MERGE WITH FIRST POSTED ENCOUNTERS
//$chargelistIdArr=array_merge($chargelistIdArr, $firstPostedChgIds);
//$chargelistIdArr = array_unique($chargelistIdArr);
$chargelistIdStr = join(',',$chargelistIdArr);

//GET MAIN ARRAY BASED ON ABOVE ENCOUNTERS
if(sizeof($chargelistIdArr)>0){
	$qry = "Select main.encounter_id, users.lname as physicanLname, users.mname as physicanMname,users.fname as physicanFname,
	main.first_posted_date,	main.primary_provider_id_for_reports as 'primaryProviderId', main.charge_list_detail_id,
	(main.charges * main.units) as 'procCharges', main.units, cpt_fee_tbl.cpt4_code,cpt_fee_tbl.cpt_desc 
	FROM report_enc_detail main  
	JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = main.proc_code_id 
	JOIN users ON users.id = main.primary_provider_id_for_reports 
	WHERE (main.del_status='0' OR (main.del_status='1' 
	AND DATE_FORMAT(main.trans_del_date, '%Y-%m-%d')>'$EndDate'))
	AND main.charge_list_detail_id in($chargelistIdStr)";
	if(trim($grp_id) != ''){
		$qry .= " and main.gro_id IN ($grp_id)";
	}
	if(trim($sc_name) != ''){
		$qry .= " and main.facility_id IN ($sc_name)";
	}	
	if(trim($filing_provider) != ''){
		$qry .= " and main.primary_provider_id_for_reports IN ($filing_provider)";
	}
	if(trim($str_crediting_provider) != ''){
		$qry .= " and main.sec_prov_id IN ($str_crediting_provider)";
	}
	if($chksamebillingcredittingproviders==1){
		$qry.= " and main.primary_provider_id_for_reports!=main.sec_prov_id";							
	}
	if(trim($cpt_cat_2) != ''){
		$qry.= " AND cpt_fee_tbl.cpt_category2 IN ($cpt_cat_2)";							
	}	
	$qry .= " order by users.lname,users.fname, cpt_fee_tbl.cpt4_code";
	$chargeQryRs = imw_query($qry);
	
	while($chargeQryRes=imw_fetch_array($chargeQryRs)){
		$encounter_id = $chargeQryRes['encounter_id'];
		$charge_list_detail_id=$chargeQryRes['charge_list_detail_id'];
		$encounter_id_arr[$encounter_id] = $encounter_id;
		$charge_list_detail_id_arr[$charge_list_detail_id] = $charge_list_detail_id;
		$providerIdArr[$charge_list_detail_id]= $chargeQryRes['primaryProviderId'];
		$providerIdEncArr[$encounter_id]= $chargeQryRes['primaryProviderId'];

		//--- PROVIDER NAME ------
		$primaryProviderId = $chargeQryRes['primaryProviderId'];
		$physician_name = $chargeQryRes['physicanLname'].', ';
		$physician_name .= $chargeQryRes['physicanFname'].' ';
		$physician_name .= $chargeQryRes['physicanMname'];
		$physician_name = ucfirst(trim($physician_name));
		if($physician_name[0] == ','){
			$physician_name = substr($physician_name,1);
		}
		$physicianNameArr[$primaryProviderId] = $physician_name;
		
		$cpt4_code = trim($chargeQryRes['cpt4_code']);
		$cpt_desc = trim($chargeQryRes['cpt_desc']);
		$cpt4_code_arr[$cpt4_code] = $cpt_desc;
		$cpt4_code_chg_arr[$charge_list_detail_id]=$cpt4_code;
		
		// KEEP CHARGES ONLY IF FIRST POSTED DATE
		if(!($chargeQryRes['first_posted_date']>=$StartDate && $chargeQryRes['first_posted_date']<=$EndDate)){
			$chargeQryRes['procCharges']=0;
		}

		$mainResArr[$primaryProviderId][$cpt4_code][] = $chargeQryRes;
	}
	imw_free_result($chargeQryRs);
}
unset($chargelistIdArr);


//TRANSACTIONS TABLE
$qry="Select trans.report_trans_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, trans.trans_method,
trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_dot, trans.trans_qry_type, trans.parent_id,
DATE_FORMAT(trans.trans_dot,'".$dateFormat."') as trans_dot, trans.trans_del_operator_id  
FROM report_enc_trans trans 
WHERE LOWER(trans.trans_type)!='charges'";
if($DateRangeFor=='transaction_date'){
	$qry.=" AND ((trans_del_operator_id='0' AND (trans_dot BETWEEN '$StartDate' and '$EndDate')) 
	OR (trans_del_operator_id>0 AND (trans_dot BETWEEN '$StartDate' and '$EndDate') AND (trans_del_date BETWEEN '$StartDate' and '$EndDate')))";
}else{
	$qry.=" AND ((trans_del_operator_id='0' AND (trans_dop BETWEEN '$StartDate' and '$EndDate')) 
	OR (trans_del_operator_id>0 AND (trans_dop BETWEEN '$StartDate' and '$EndDate') AND (trans_del_date BETWEEN '$StartDate' and '$EndDate')))";
}
$qry.=" ORDER BY trans.master_tbl_id, trans.report_trans_id, trans.trans_dot, trans.trans_dot_time";

$rs=imw_query($qry);
$i=0;
$writeOffArr = array();
$writeOffPaymentArr = array();
$ref_write_off_arr = array();
while($res = imw_fetch_assoc($rs)){
	$report_trans_id=$res['report_trans_id'];
	$encounter_id= $res['encounter_id'];
	$chgDetId= $res['charge_list_detail_id'];
	$insCompId = $res['trans_ins_id'];
	$provider_id = $providerIdEncArr[$encounter_id];
	$cpt_code= $cpt4_code_chg_arr[$chgDetId];
	$encounterIdArr[$res['encounter_id']] = $res['encounter_id'];
	$trans_type= strtolower($res['trans_type']);
	$trans_method = $res['trans_method'];
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
			
			if($trans_type=='copay-paid' || $trans_type=='copay-negative payment'){
				$mainPaymentArr[$provider_id]['copay_payment'][] = $paidForProc;
				if($insCompId == 0){
					$patient_copay_arr[$provider_id][$trans_method][] = $paidForProc;
				}
			}else{
				$mainPaymentArr[$chgDetId][] = $paidForProc;
				//--- REFRACTION PAYMENT DETAILS ---------
				$ref_detail_id = $ref_cpt_arr[$chgDetId];
				if(empty($ref_detail_id) == false){
					$ref_payment_arr[$provider_id][$trans_method][] = $paidForProc;
				}
			}				

			if($insCompId > 0){
				if($medInsIdArr['MEDICARE'][$insCompId]){
					$proPaymentArr[$provider_id]['MEDICARE'][$trans_method][] = $paidForProc;
				}
				else if($medInsIdArr['MEDICAID'][$insCompId]){
					$proPaymentArr[$provider_id]['MEDICAID'][$trans_method][] = $paidForProc;
				}
				else{
					$proPaymentArr[$provider_id]['COMMERCIAL'][$trans_method][] = $paidForProc;	
				}
			}
			else{
				$proPaymentArr[$provider_id]['PATIENT'][$trans_method][] = $paidForProc;
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

			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = ($trans_type=='credit') ? "-".$tempRecordData[$res['parent_id']] : $tempRecordData[$res['parent_id']];
			}
			$crddbtamt+=$prevFetchedAmt;		

			if($insCompId>0){
				$pay_crd_deb_arr[$chgDetId]['Insurance'][] = $crddbtamt;
			}else{
				$pay_crd_deb_arr[$chgDetId]['Patient'][] = $crddbtamt;				
			}
		break;

		case 'default_writeoff':
			$temp_default_writeoff[$chgDetId]= $res['trans_amount'];
		break;
		case 'write off':
		case 'discount':
			if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];

			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
			}
			$res['trans_amount']+=$prevFetchedAmt;
			
			if($insCompId > 0){
				//--- WRITE OFF AMOUNT BY INSURANCE COMPANIES -------
				if($medInsIdArr['MEDICARE'][$insCompId]){
					$writeOffPaymentArr[$provider_id]['MEDICARE'][] = $res['trans_amount'];
				}
				else if($medInsIdArr['MEDICAID'][$insCompId]){
					$writeOffPaymentArr[$provider_id]['MEDICAID'][] = $res['trans_amount'];
				}
				else{
					$writeOffPaymentArr[$provider_id]['COMMERCIAL'][] = $res['trans_amount'];
				}
			}
			else{
				$writeOffPaymentArr[$provider_id]['PATIENT'][] = $res['trans_amount'];
			}
			
			if($chgDetId > 0){ 
				$writeOffArr[$chgDetId][$i] = $res['trans_amount'];
				//---- REFRACTION WRITE OFF CHECK -------
				$ref_detail_id = $ref_cpt_arr[$chgDetId];
				if(empty($ref_detail_id) == false){
					$ref_write_off_arr[$provider_id][92015][] = $res['trans_amount'];
				}
				$i++;
			}else{
				//WRITE-OFF ON COPAY
				if($insCompId == 0){
					$writeOffArr[$provider_id]['PATIENT']['write_off_amount'][] = $res['trans_amount'];
				}else{
					$writeOffArr[$provider_id]['COMMERCIAL']['write_off_amount'][] = $res['trans_amount'];
				}
			}
					
		break;

		case 'over adjustment':
		case 'returned check':
			$credit_var = 'ADJUSTMENT';
			if($trans_type=='over adjustment'){
				if($res['trans_del_operator_id']>0)$res['trans_amount']="-".$res['trans_amount'];
			}else{
				$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];
			}

			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = "-".$tempRecordData[$res['parent_id']];
			}
			$res['trans_amount']+=$prevFetchedAmt;
			
			if($insCompId > 0){
				if($medInsIdArr['MEDICARE'][$insCompId]){
					$mainAdjDataArr[$provider_id][$credit_var]['MEDICARE'][] = $res['trans_amount'];
				}else if($medInsIdArr['MEDICAID'][$insCompId]){
					$mainAdjDataArr[$provider_id][$credit_var]['MEDICAID'][] = $res['trans_amount'];
				}else{			 
					$mainAdjDataArr[$provider_id][$credit_var]['COMMERCIAL'][] = $res['trans_amount'];
				}
			}else{
				$mainAdjDataArr[$provider_id][$credit_var]['PATIENT'][] = $res['trans_amount'];
			}
		break;
		
		case 'refund':
		case 'adjustment':
			$credit_var = ($trans_type=='refund') ? 'REFUND' : 'ADJUSTMENT';
			$res['trans_amount']= ($res['trans_del_operator_id']>0) ? $res['trans_amount'] : "-".$res['trans_amount'];

			$prevFetchedAmt=0;
			if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
				$prevFetchedAmt = $tempRecordData[$res['parent_id']];
			}
			$res['trans_amount']+=$prevFetchedAmt;

			if($credit_var == 'REFUND'){
				$mainAdjDataArr[$provider_id][$chgDetId][$cpt_code]['REFUND'][] = $res['trans_amount'];
			}
			if($insCompId > 0){
				if($medInsIdArr['MEDICARE'][$insCompId]){
					$mainAdjDataArr[$provider_id][$credit_var]['MEDICARE'][] = $res['trans_amount'];
				}else if($medInsIdArr['MEDICAID'][$insCompId]){
					$mainAdjDataArr[$provider_id][$credit_var]['MEDICAID'][] = $res['trans_amount'];
				}else{			 
					$mainAdjDataArr[$provider_id][$credit_var]['COMMERCIAL'][] = $res['trans_amount'];
				}
			}else{
				$mainAdjDataArr[$provider_id][$credit_var]['PATIENT'][] = $res['trans_amount'];
			}				
		break;
	}
}

//MAKING ARRAY FOR DEFAULT WRITE-OFF
if(sizeof($temp_default_writeoff)>0){
	foreach($temp_default_writeoff as $chgDetId => $writeoff){
		$provider_id = $providerIdArr[$chgDetId];
		$insCompId=$tempTransInsCompArr[$chgDetId];
		$cpt_code= $cpt4_code_chg_arr[$chgDetId];

		if($insCompId > 0){
			//--- WRITE OFF AMOUNT BY INSURANCE COMPANIES -------
			if($medInsIdArr['MEDICARE'][$insCompId]){
				$writeOffPaymentArr[$provider_id]['MEDICARE'][] = $writeoff;
			}
			else if($medInsIdArr['MEDICAID'][$insCompId]){
				$writeOffPaymentArr[$provider_id]['MEDICAID'][] = $writeoff;
			}
			else{			 
				$writeOffPaymentArr[$provider_id]['COMMERCIAL'][] = $writeoff;
			}
		}
		else{
			$writeOffPaymentArr[$provider_id]['PATIENT'][] = $writeoff;
		}
		
		
		if($chgDetId>0){
			$writeOffArr[$chgDetId][] = $writeoff;
			//--- REFRACTION AUTOMATIC WRITE OFF AMOUNT --------
			if($proc_code == 92015){
				$ref_write_off_arr[$provider_id][92015][] = $writeoff;
			}
		}else{
			//WRITE-OFF ON COPAY
			if($insCompId == 0){
				$writeOffArr[$provider_id]['PATIENT']['write_off_amount'][] = $writeoff;
			}else{
				$writeOffArr[$provider_id]['COMMERCIAL']['write_off_amount'][] = $writeoff;
			}
		}		
	}
}


//COUNTER SET FOR GROSS TOTAL OF ALL PROVIDER
$gross_total_count = 0;
$gross_total_procedure_charges = 0;
$gross_total_paid_for = 0;
$gross_total_write_off = 0;
$gross_total_refund_amount = 0;

$gross_cc_total_amount = 0;
$gross_mo_total_amount = 0;
$gross_eft_total_amount = 0;
$gross_cash_total_amount = 0;
$gross_check_total_amount = 0;
$gross_write_off_total_amount = 0;
$gross_adj_total_amount = 0;
$gross_refund_total_amount = 0;
$gross_physicain_total_cnt = 0;


if(count($mainResArr) > 0){	
	//--- GET ALL PHYSICIAN ID ------
	
	$providerIdArr = array_keys($mainResArr);
	for($p=0;$p<count($providerIdArr);$p++){
		$printFile = true;
		$physician_id = $providerIdArr[$p];		
		//--- GET ALL PROCEDURES FOR A SINGLE PROVIDER ----
		$procDataArr = $mainResArr[$physician_id];
		$procIdArr = array();
		if(count($procDataArr)>0){
			$procIdArr = array_keys($procDataArr);
		}
		//---- COUNTER RESET FOR PROVIDER TOTAL --------
		$total_count = 0;
		$total_procedure_charges = 0;
		$total_paid_for = 0;
		$total_write_off = 0;
		$total_refund_amount = 0;
		$procedure_content = '';
		$total_write_off_count = 0;
		$provider_total_write_off = 0;

		for($i=0;$i<count($procIdArr);$i++){
			$proc_code = trim($procIdArr[$i]);
			$procedure_name = core_refine_user_input($cpt4_code_arr[$proc_code]);
			//---- GET SINGLE PROCEDURE DETAILS ---------
			$proc_data_arr = $procDataArr[$proc_code];
			$procedure_count = 0;
			$procedure_charges = 0;
			$procedure_paid_for = 0;
			$procedure_write_off = 0;
			$procedure_refund_amount = 0;
			for($pr=0;$pr<count($proc_data_arr);$pr++){
				$procedure_count += $proc_data_arr[$pr]['units'];
				$procedure_charges += $proc_data_arr[$pr]['procCharges'];
				$charge_list_detail_id = $proc_data_arr[$pr]['charge_list_detail_id'];


				if(count($mainPaymentArr[$charge_list_detail_id])>0){
					$procedure_paid_for += array_sum($mainPaymentArr[$charge_list_detail_id]);
				}
				$patCrdDbt = array_sum($pay_crd_deb_arr[$charge_list_detail_id]['Patient']);
				$insCrdDbt = array_sum($pay_crd_deb_arr[$charge_list_detail_id]['Insurance']);
				$procedure_paid_for += $patCrdDbt + $insCrdDbt;
				
				if(count($writeOffArr[$charge_list_detail_id])>0){
					$procedure_write_off += array_sum($writeOffArr[$charge_list_detail_id]);
					$total_write_off_count += count($writeOffArr[$charge_list_detail_id]);
				}
				if(count($mainAdjDataArr[$physician_id][$charge_list_detail_id][$proc_code]['REFUND'])>0){
					$procedure_refund_amount += array_sum($mainAdjDataArr[$physician_id][$charge_list_detail_id][$proc_code]['REFUND']);
				}
			}
			
			$total_count += $procedure_count;
			$total_procedure_charges += $procedure_charges;
			$total_paid_for += $procedure_paid_for;
			$total_write_off += $procedure_write_off;
			$total_refund_amount += $procedure_refund_amount;
			$provider_total_write_off += $procedure_write_off;
			
			$gross_total_count += $procedure_count;
			$gross_total_procedure_charges += $procedure_charges;
			$gross_total_paid_for += $procedure_paid_for;
			$gross_total_write_off += $procedure_write_off;
			$gross_total_refund_amount += $procedure_refund_amount;
			
			//--- NUMBER FORMAT OF SINGLE PROCEDURE AMOUNT ------
			$procedure_charges = $CLSReports->numberFormat($procedure_charges,2);
			$procedure_paid_for = $CLSReports->numberFormat($procedure_paid_for,2);
			$procedure_write_off = $CLSReports->numberFormat($procedure_write_off,2);
			$procedure_refund_amount = $CLSReports->numberFormat($procedure_refund_amount,2);
			
			$procedure_content .= <<<DATA
				<tr bgcolor="#FFFFFF">
					<td class="text_10" style="width:250px">$proc_code - $procedure_name</td>
					<td class="text_10" style="text-align:right; width:30px">$procedure_count</td>
					<td class="text_10" style="text-align:right; width:180px">$procedure_charges</td>
					$rvuTd
					<td class="text_10" style="text-align:right; width:180px">$procedure_paid_for</td>
					<td class="text_10" style="text-align:right; width:180px">$procedure_write_off</td>
					<td class="text_10" style="text-align:right; width:180px">$procedure_refund_amount</td>
				</tr>
DATA;
		}


		//--- GET TOTAL COPAY AMOUNT FOR A SINGLE PROVIDER -----
		$total_copay_count = count($mainPaymentArr[$physician_id]['copay_payment']);
		$total_copay = 0;
		if($total_copay_count > 0){
			$total_copay = array_sum($mainPaymentArr[$physician_id]['copay_payment']);
		}
		$total_paid_for += $total_copay;
		$gross_total_paid_for += $total_copay;
		
		$total_copay_write_off = 0;
		$total_copay_write_off += @array_sum($writeOffArr[$physician_id]['PATIENT']['write_off_amount']);
		$total_copay_write_off += @array_sum($writeOffArr[$physician_id]['COMMERCIAL']['write_off_amount']);
		$total_write_off += $total_copay_write_off;
		$gross_total_write_off += $total_copay_write_off;
		
		//--- GET TOTAL WRITE OFF AMOUNT --------
		$total_write_off_count += count($writeOffArr[$physician_id]['PATIENT']['write_off_amount']);
		$provider_total_write_off += @array_sum($writeOffArr[$physician_id]['PATIENT']['write_off_amount']);
		$total_write_off_count += count($writeOffArr[$physician_id]['COMMERCIAL']['write_off_amount']);
		$provider_total_write_off += @array_sum($writeOffArr[$physician_id]['COMMERCIAL']['write_off_amount']);
		
		//--- NUMBER FORMAT FOR COPAY AMOUNT ------
		$total_copay = $CLSReports->numberFormat($total_copay,2);
		$total_copay_write_off = $CLSReports->numberFormat($total_copay_write_off,2);
		
		//--- NUMBER FORMAT OF SINGLE PROCEDURE AMOUNT ------
		$total_procedure_charges = $CLSReports->numberFormat($total_procedure_charges,2);
		$total_paid_for = $CLSReports->numberFormat($total_paid_for,2);
		$total_write_off = $CLSReports->numberFormat($total_write_off,2);
		$total_refund_amount = $CLSReports->numberFormat($total_refund_amount,2);
		$provider_total_write_off = $CLSReports->numberFormat($provider_total_write_off,2);
		
		//---- GET COMMERCIAL PROVIDER PAYMENT DETAILS ----
		$comercial_count = count($proPaymentArr[$physician_id]['COMMERCIAL']['Check']);
		$comercial_check = 0;
		if(count($proPaymentArr[$physician_id]['COMMERCIAL']['Check']) > 0){
			$comercial_check = array_sum($proPaymentArr[$physician_id]['COMMERCIAL']['Check']);
		}
		$check_total_amount = $comercial_check;
		$comercial_count += count($proPaymentArr[$physician_id]['COMMERCIAL']['Cash']);
		$comercial_cash = 0;
		if(count($proPaymentArr[$physician_id]['COMMERCIAL']['Cash']) > 0){
			$comercial_cash = array_sum($proPaymentArr[$physician_id]['COMMERCIAL']['Cash']);
		}
		$cash_total_amount = $comercial_cash;
		$comercial_count += count($proPaymentArr[$physician_id]['COMMERCIAL']['Credit Card']);
		$comercial_cc = 0;
		if(count($proPaymentArr[$physician_id]['COMMERCIAL']['Credit Card']) > 0){
			$comercial_cc = array_sum($proPaymentArr[$physician_id]['COMMERCIAL']['Credit Card']);
		}
		$cc_total_amount = $comercial_cc; 
		$comercial_count += count($proPaymentArr[$physician_id]['COMMERCIAL']['Money Order']);
		$comercial_mo = 0;
		if(count($proPaymentArr[$physician_id]['COMMERCIAL']['Money Order']) > 0){
			$comercial_mo = array_sum($proPaymentArr[$physician_id]['COMMERCIAL']['Money Order']);
		}
		$mo_total_amount = $comercial_mo; 
		$comercial_count += count($proPaymentArr[$physician_id]['COMMERCIAL']['EFT']);
		$comercial_eft = 0;
		if(count($proPaymentArr[$physician_id]['COMMERCIAL']['EFT']) > 0){
			$comercial_eft = array_sum($proPaymentArr[$physician_id]['COMMERCIAL']['EFT']);
		}
		$eft_total_amount = $comercial_eft; 
		$comercial_count += count($proPaymentArr[$physician_id]['COMMERCIAL']['VEEP']);
		$comercial_veep = 0;
		if(count($proPaymentArr[$physician_id]['COMMERCIAL']['VEEP']) > 0){
			$comercial_veep = array_sum($proPaymentArr[$physician_id]['COMMERCIAL']['VEEP']);
		}
		$veep_total_amount = $comercial_veep; 

		
		//---- GET COMMERCIAL WRITE OFF AMOUNT -----
		$commercial_write_off_cnt = count($writeOffPaymentArr[$physician_id]['COMMERCIAL']);
		$commercial_write_off_amount = 0;
		if(count($writeOffPaymentArr[$physician_id]['COMMERCIAL']) > 0){
			$commercial_write_off_amount = array_sum($writeOffPaymentArr[$physician_id]['COMMERCIAL']);
		}
		$write_off_total_amount = $commercial_write_off_amount;
		
		//---- GET COMMERCIAL ADJUSTMENT AMOUNT --------
		$commercial_adj_cnt = count($mainAdjDataArr[$physician_id]['ADJUSTMENT']['COMMERCIAL']);
		$commercial_adj_amount = 0;
		if($commercial_adj_cnt > 0){
			$commercial_adj_amount = array_sum($mainAdjDataArr[$physician_id]['ADJUSTMENT']['COMMERCIAL']);
		}
		$adj_total_amount = $commercial_adj_amount;
		
		//---- GET COMMERCIAL REFUND AMOUNT --------
		$commercial_refund_cnt = count($mainAdjDataArr[$physician_id]['REFUND']['COMMERCIAL']);
		$commercial_refund_amount = 0;
		if($commercial_refund_cnt > 0){
			$commercial_refund_amount = array_sum($mainAdjDataArr[$physician_id]['REFUND']['COMMERCIAL']);
		}
		$refund_total_amount = $commercial_refund_amount;
		
		//---- NUMBER FORMAT FOR COMMERCIAL AMOUNT ------
		$comercial_check = $CLSReports->numberFormat($comercial_check,2);
		$comercial_cash = $CLSReports->numberFormat($comercial_cash,2);
		$comercial_cc = $CLSReports->numberFormat($comercial_cc,2);
		$comercial_mo = $CLSReports->numberFormat($comercial_mo,2);
		$comercial_eft = $CLSReports->numberFormat($comercial_eft,2);
		$comercial_veep = $CLSReports->numberFormat($comercial_veep,2);
		$commercial_write_off_amount = $CLSReports->numberFormat($commercial_write_off_amount,2);
		$commercial_adj_amount = $CLSReports->numberFormat($commercial_adj_amount,2);
		$commercial_refund_amount = $CLSReports->numberFormat($commercial_refund_amount,2);
		
		//---- GET MEDICAID PROVIDER PAYMENT DETAILS ----
		$medicaid_count = count($proPaymentArr[$physician_id]['MEDICAID']['Check']);
		$medicaid_check = 0;
		if(count($proPaymentArr[$physician_id]['MEDICAID']['Check']) > 0){
			$medicaid_check = array_sum($proPaymentArr[$physician_id]['MEDICAID']['Check']);
		}
		$check_total_amount += $medicaid_check;
		$medicaid_count += count($proPaymentArr[$physician_id]['MEDICAID']['Cash']);
		$medicaid_cash = 0;
		if(count($proPaymentArr[$physician_id]['MEDICAID']['Cash']) > 0){
			$medicaid_cash = array_sum($proPaymentArr[$physician_id]['MEDICAID']['Cash']);
		}
		$cash_total_amount += $medicaid_cash;
		$medicaid_count += count($proPaymentArr[$physician_id]['MEDICAID']['Credit Card']);
		$medicaid_cc = 0;
		if(count($proPaymentArr[$physician_id]['MEDICAID']['Credit Card']) > 0){
			$medicaid_cc = array_sum($proPaymentArr[$physician_id]['MEDICAID']['Credit Card']);
		}
		$cc_total_amount += $medicaid_cc;
		$medicaid_count += count($proPaymentArr[$physician_id]['MEDICAID']['Money Order']);
		$medicaid_mo = 0;
		if(count($proPaymentArr[$physician_id]['MEDICAID']['Money Order']) > 0){
			$medicaid_mo = array_sum($proPaymentArr[$physician_id]['MEDICAID']['Money Order']);
		}
		$mo_total_amount += $medicaid_mo;
		$medicaid_count += count($proPaymentArr[$physician_id]['MEDICAID']['EFT']);
		$medicaid_eft = 0;
		if(count($proPaymentArr[$physician_id]['MEDICAID']['EFT']) > 0){
			$medicaid_eft = array_sum($proPaymentArr[$physician_id]['MEDICAID']['EFT']);
		}
		$eft_total_amount += $medicaid_eft;		
		$medicaid_count += count($proPaymentArr[$physician_id]['MEDICAID']['VEEP']);
		$medicaid_veep = 0;
		if(count($proPaymentArr[$physician_id]['MEDICAID']['veep']) > 0){
			$medicaid_veep = array_sum($proPaymentArr[$physician_id]['MEDICAID']['VEEP']);
		}
		$veep_total_amount += $medicaid_veep;
		
		
		//---- GET MEDICAID WRITE OFF AMOUNT -----
		$medicaid_write_off_cnt = count($writeOffPaymentArr[$physician_id]['MEDICAID']);
		$medicaid_write_off_amount = 0;
		if(count($writeOffPaymentArr[$physician_id]['MEDICAID']) > 0){
			$medicaid_write_off_amount = array_sum($writeOffPaymentArr[$physician_id]['MEDICAID']);
		}
		$write_off_total_amount += $medicaid_write_off_amount;
		
		//---- GET MEDICAID ADJUSTMENT AMOUNT --------
		$medicaid_adj_cnt = count($mainAdjDataArr[$physician_id]['ADJUSTMENT']['MEDICAID']);
		$medicaid_adj_amount = 0;
		if($medicaid_adj_cnt > 0){
			$medicaid_adj_amount = array_sum($mainAdjDataArr[$physician_id]['ADJUSTMENT']['MEDICAID']);
		}
		$adj_total_amount += $medicaid_adj_amount;
		//---- GET MEDICAID REFUND AMOUNT --------
		$medicaid_refund_cnt = count($mainAdjDataArr[$physician_id]['REFUND']['MEDICAID']);
		$medicaid_refund_amount = 0;
		if($medicaid_refund_cnt > 0){
			$medicaid_refund_amount = array_sum($mainAdjDataArr[$physician_id]['REFUND']['MEDICAID']);
		}
		$refund_total_amount += $medicaid_refund_amount;
		
		//---- NUMBER FORMAT FOR MEDICAID AMOUNT ------
		$medicaid_check = $CLSReports->numberFormat($medicaid_check,2);
		$medicaid_cash = $CLSReports->numberFormat($medicaid_cash,2);
		$medicaid_cc = $CLSReports->numberFormat($medicaid_cc,2);
		$medicaid_mo = $CLSReports->numberFormat($medicaid_mo,2);
		$medicaid_eft = $CLSReports->numberFormat($medicaid_eft,2);
		$medicaid_veep = $CLSReports->numberFormat($medicaid_veep,2);
		$medicaid_write_off_amount = $CLSReports->numberFormat($medicaid_write_off_amount,2);
		$medicaid_adj_amount = $CLSReports->numberFormat($medicaid_adj_amount,2);
		$medicaid_refund_amount = $CLSReports->numberFormat($medicaid_refund_amount,2);
		
		//---- GET MEDICARE PROVIDER PAYMENT DETAILS ----
		$medicare_count = count($proPaymentArr[$physician_id]['MEDICARE']['Check']);
		$medicare_check = 0;
		if(count($proPaymentArr[$physician_id]['MEDICARE']['Check']) > 0){
			$medicare_check = array_sum($proPaymentArr[$physician_id]['MEDICARE']['Check']);
		}
		$check_total_amount += $medicare_check;
		$medicare_count += count($proPaymentArr[$physician_id]['MEDICARE']['Cash']);
		$medicare_cash = 0;
		if(count($proPaymentArr[$physician_id]['MEDICARE']['Cash']) > 0){
			$medicare_cash = array_sum($proPaymentArr[$physician_id]['MEDICARE']['Cash']);
		}
		$cash_total_amount += $medicare_cash;
		$medicare_count += count($proPaymentArr[$physician_id]['MEDICARE']['Credit Card']);
		$medicare_cc = 0;
		if(count($proPaymentArr[$physician_id]['MEDICARE']['Credit Card']) > 0){
			$medicare_cc = array_sum($proPaymentArr[$physician_id]['MEDICARE']['Credit Card']);
		}
		$cc_total_amount += $medicare_cc;
		$medicare_count += count($proPaymentArr[$physician_id]['MEDICARE']['Money Order']);
		$medicare_mo = 0;
		if(count($proPaymentArr[$physician_id]['MEDICARE']['Money Order']) > 0){
			$medicare_mo = array_sum($proPaymentArr[$physician_id]['MEDICARE']['Money Order']);
		}
		$mo_total_amount += $medicare_mo;
		$medicare_count += count($proPaymentArr[$physician_id]['MEDICARE']['EFT']);
		$medicare_eft = 0;
		if(count($proPaymentArr[$physician_id]['MEDICARE']['EFT']) > 0){
			$medicare_eft = array_sum($proPaymentArr[$physician_id]['MEDICARE']['EFT']);
		}
		$eft_total_amount += $medicare_eft;		
		$medicare_count += count($proPaymentArr[$physician_id]['MEDICARE']['VEEP']);
		$medicare_veep = 0;
		if(count($proPaymentArr[$physician_id]['MEDICARE']['VEEP']) > 0){
			$medicare_veep = array_sum($proPaymentArr[$physician_id]['MEDICARE']['VEEP']);
		}
		$veep_total_amount += $medicare_veep;
		
		//---- GET MEDICARE WRITE OFF AMOUNT -----
		$medicare_write_off_cnt = count($writeOffPaymentArr[$physician_id]['MEDICARE']);
		$medicare_write_off_amount = 0;
		if(count($writeOffPaymentArr[$physician_id]['MEDICARE']) > 0){
			$medicare_write_off_amount = array_sum($writeOffPaymentArr[$physician_id]['MEDICARE']);
		}
		$write_off_total_amount += $medicare_write_off_amount;
		
		//---- GET MEDICARE ADJUSTMENT AMOUNT --------
		$medicare_adj_cnt = count($mainAdjDataArr[$physician_id]['ADJUSTMENT']['MEDICARE']);
		$medicare_adj_amount = 0;
		if($medicare_adj_cnt > 0){
			$medicare_adj_amount = array_sum($mainAdjDataArr[$physician_id]['ADJUSTMENT']['MEDICARE']);
		}
		$adj_total_amount += $medicare_adj_amount;
		
		//---- GET MEDICARE REFUND AMOUNT --------
		$medicare_refund_cnt = count($mainAdjDataArr[$physician_id]['REFUND']['MEDICARE']);
		$medicare_refund_amount = 0;
		if($medicare_refund_cnt > 0){
			$medicare_refund_amount = array_sum($mainAdjDataArr[$physician_id]['REFUND']['MEDICARE']);
		}
		$refund_total_amount += $medicare_refund_amount;
		
		//---- NUMBER FORMAT FOR MEDICARE AMOUNT ------
		$medicare_check = $CLSReports->numberFormat($medicare_check,2);
		$medicare_cash = $CLSReports->numberFormat($medicare_cash,2);
		$medicare_cc = $CLSReports->numberFormat($medicare_cc,2);
		$medicare_mo = $CLSReports->numberFormat($medicare_mo,2);
		$medicare_eft = $CLSReports->numberFormat($medicare_eft,2);
		$medicare_veep = $CLSReports->numberFormat($medicare_veep,2);
		$medicare_write_off_amount = $CLSReports->numberFormat($medicare_write_off_amount,2);
		$medicare_adj_amount = $CLSReports->numberFormat($medicare_adj_amount,2);
		$medicare_refund_amount = $CLSReports->numberFormat($medicare_refund_amount,2);
		
		//---- GET PATIENT PROVIDER PAYMENT DETAILS ----
		$patient_count = count($proPaymentArr[$physician_id]['PATIENT']['Check']);
		$patient_check = 0;
		if(count($proPaymentArr[$physician_id]['PATIENT']['Check']) > 0){
			$patient_check = array_sum($proPaymentArr[$physician_id]['PATIENT']['Check']);
		}
		$check_total_amount += $patient_check;
		$patient_count += count($proPaymentArr[$physician_id]['PATIENT']['Cash']);
		$patient_cash = 0;
		if(count($proPaymentArr[$physician_id]['PATIENT']['Cash']) > 0){
			$patient_cash = array_sum($proPaymentArr[$physician_id]['PATIENT']['Cash']);
		}
		$cash_total_amount += $patient_cash;
		$patient_count += count($proPaymentArr[$physician_id]['PATIENT']['Credit Card']);
		$patient_cc = 0;
		if(count($proPaymentArr[$physician_id]['PATIENT']['Credit Card']) > 0){
			$patient_cc = array_sum($proPaymentArr[$physician_id]['PATIENT']['Credit Card']);
		}
		$cc_total_amount += $patient_cc;
		$patient_count += count($proPaymentArr[$physician_id]['PATIENT']['Money Order']);
		$patient_mo = 0;
		if(count($proPaymentArr[$physician_id]['PATIENT']['Money Order']) > 0){
			$patient_mo = array_sum($proPaymentArr[$physician_id]['PATIENT']['Money Order']);
		}
		$mo_total_amount += $patient_mo;
		$patient_count += count($proPaymentArr[$physician_id]['PATIENT']['EFT']);
		$patient_eft = 0;
		if(count($proPaymentArr[$physician_id]['PATIENT']['EFT']) > 0){
			$patient_eft = array_sum($proPaymentArr[$physician_id]['PATIENT']['EFT']);
		}
		$eft_total_amount += $patient_eft;
		$patient_count += count($proPaymentArr[$physician_id]['PATIENT']['VEEP']);
		$patient_veep = 0;
		if(count($proPaymentArr[$physician_id]['PATIENT']['VEEP']) > 0){
			$patient_veep = array_sum($proPaymentArr[$physician_id]['PATIENT']['VEEP']);
		}
		$veep_total_amount += $patient_veep;
				
		//---- GET PATIENT WRITE OFF AMOUNT -----
		$patient_write_off_count = count($writeOffPaymentArr[$physician_id]['PATIENT']);
		$patient_write_off_amount = 0;
		if(count($writeOffPaymentArr[$physician_id]['PATIENT']) > 0){
			$patient_write_off_amount = array_sum($writeOffPaymentArr[$physician_id]['PATIENT']);
		}
		$write_off_total_amount += $patient_write_off_amount;

		//---- GET PATIENT ADJUSTMENT AMOUNT --------
		$patient_adj_cnt = count($mainAdjDataArr[$physician_id]['ADJUSTMENT']['PATIENT']);
		$patient_adj_amount = 0;
		if($patient_adj_cnt > 0){
			$patient_adj_amount = array_sum($mainAdjDataArr[$physician_id]['ADJUSTMENT']['PATIENT']);
		}
		$adj_total_amount += $patient_adj_amount;
		
		//---- GET PATIENT REFUND AMOUNT --------
		$patient_refund_cnt = count($mainAdjDataArr[$physician_id]['REFUND']['PATIENT']);
		$patient_refund_amount = 0;
		if($patient_refund_cnt > 0){
			$patient_refund_amount = array_sum($mainAdjDataArr[$physician_id]['REFUND']['PATIENT']);
		}
		$refund_total_amount += $patient_refund_amount;
		
		//---- NUMBER FORMAT FOR PATIENT AMOUNT ------
		$patient_check = $CLSReports->numberFormat($patient_check,2);
		$patient_cash = $CLSReports->numberFormat($patient_cash,2);
		$patient_cc = $CLSReports->numberFormat($patient_cc,2);
		$patient_mo = $CLSReports->numberFormat($patient_mo,2);
		$patient_eft = $CLSReports->numberFormat($patient_eft,2);
		$patient_veep = $CLSReports->numberFormat($patient_veep,2);
		$patient_write_off_amount = $CLSReports->numberFormat($patient_write_off_amount,2);
		$patient_adj_amount = $CLSReports->numberFormat($patient_adj_amount,2);
		$patient_refund_amount = $CLSReports->numberFormat($patient_refund_amount,2);
		
		//--- PATIENT COPAY AMOUNT DETAILS ----
		$patient_copay_count = count($patient_copay_arr[$physician_id]['Cash']);
		$patient_copay_cash = 0;
		if(count($patient_copay_arr[$physician_id]['Cash']) > 0){
			$patient_copay_cash = array_sum($patient_copay_arr[$physician_id]['Cash']);
		}
		$patient_copay_count += count($patient_copay_arr[$physician_id]['Check']);
		$patient_copay_check = 0;
		if(count($patient_copay_arr[$physician_id]['Check']) > 0){
			$patient_copay_check = array_sum($patient_copay_arr[$physician_id]['Check']);
		}
		$patient_copay_count += count($patient_copay_arr[$physician_id]['Credit Card']);
		$patient_copay_cc = 0;
		if(count($patient_copay_arr[$physician_id]['Credit Card']) > 0){
			$patient_copay_cc = array_sum($patient_copay_arr[$physician_id]['Credit Card']);
		}
		$patient_copay_count += count($patient_copay_arr[$physician_id]['Money Order']);
		$patient_copay_mo = 0;
		if(count($patient_copay_arr[$physician_id]['Money Order']) > 0){
			$patient_copay_mo = array_sum($patient_copay_arr[$physician_id]['Money Order']);
		}
		$patient_copay_count += count($patient_copay_arr[$physician_id]['EFT']);
		$patient_copay_eft = 0;
		if(count($patient_copay_arr[$physician_id]['EFT']) > 0){
			$patient_copay_eft = array_sum($patient_copay_arr[$physician_id]['EFT']);
		}
		$patient_copay_count += count($patient_copay_arr[$physician_id]['VEEP']);
		$patient_copay_veep = 0;
		if(count($patient_copay_arr[$physician_id]['VEEP']) > 0){
			$patient_copay_veep = array_sum($patient_copay_arr[$physician_id]['VEEP']);
		}
		
		//--- COPAY WRITE OFF AMOUNT ------
		$patient_copay_write_off_cnt = count($writeOffArr[$physician_id]['PATIENT']['write_off_amount']);
		if($patient_copay_write_off_cnt > 0){
			$patient_copay_write_off_amount = array_sum($writeOffArr[$physician_id]['PATIENT']['write_off_amount']);
		}
		$write_off_total_amount += $patient_copay_write_off_amount;
		
		//--- NUMBER FORMAT FOR PATIENT COPAY AMOUNT -------
		$patient_copay_cash = $CLSReports->numberFormat($patient_copay_cash,2);
		$patient_copay_check = $CLSReports->numberFormat($patient_copay_check,2);
		$patient_copay_cc = $CLSReports->numberFormat($patient_copay_cc,2);
		$patient_copay_mo = $CLSReports->numberFormat($patient_copay_mo,2);
		$patient_copay_eft = $CLSReports->numberFormat($patient_copay_eft,2);
		$patient_copay_veep = $CLSReports->numberFormat($patient_copay_veep,2);
		$patient_copay_write_off_amount = $CLSReports->numberFormat($patient_copay_write_off_amount,2);
		
		//--- REFRACTION PAYMENT DETAILS -------
		$patient_ref_count = count($ref_payment_arr[$physician_id]['Cash']);
		$patient_ref_cash = 0;
		if(count($ref_payment_arr[$physician_id]['Cash']) > 0){
			$patient_ref_cash = array_sum($ref_payment_arr[$physician_id]['Cash']);
		}
		$patient_ref_count += count($ref_payment_arr[$physician_id]['Check']);
		$patient_ref_check = 0;
		if(count($ref_payment_arr[$physician_id]['Check']) > 0){
			$patient_ref_check = array_sum($ref_payment_arr[$physician_id]['Check']);
		}
		$patient_ref_count += $ref_payment_arr[$physician_id]['Credit Card'];
		$patient_ref_cc = 0;
		if(count($ref_payment_arr[$physician_id]['Credit Card']) > 0){
			$patient_ref_cc = array_sum($ref_payment_arr[$physician_id]['Credit Card']);
		}
		$patient_ref_count += $ref_payment_arr[$physician_id]['Money Order'];
		$patient_ref_mo = 0;
		if(count($ref_payment_arr[$physician_id]['Money Order']) > 0){
			$patient_ref_mo = array_sum($ref_payment_arr[$physician_id]['Money Order']);
		}
		$patient_ref_count += $ref_payment_arr[$physician_id]['EFT'];
		$patient_ref_eft = 0;
		if(count($ref_payment_arr[$physician_id]['EFT']) > 0){
			$patient_ref_eft = array_sum($ref_payment_arr[$physician_id]['EFT']);
		}
		$patient_ref_count += $ref_payment_arr[$physician_id]['VEEP'];
		$patient_ref_veep = 0;
		if(count($ref_payment_arr[$physician_id]['VEEP']) > 0){
			$patient_ref_veep = array_sum($ref_payment_arr[$physician_id]['VEEP']);
		}
		
		//---- REFRACTION WRITE OFF AMOUNT -------
		$refraction_write_off_count = count($ref_write_off_arr[$physician_id][92015]);
		$refraction_write_off_amount = 0;
		if($refraction_write_off_count > 0){
			$refraction_write_off_amount = array_sum($ref_write_off_arr[$physician_id][92015]);
		}
		
		//--- REFRACTION ADJUSTMENT AMOUNT --------
		$refraction_adj_cnt = count($mainAdjDataArr[$physician_id][92015]['ADJUSTMENT']);
		$refraction_adj_amount = 0;
		if($refraction_adj_cnt > 0){
			$refraction_adj_amount = array_sum($mainAdjDataArr[$physician_id][92015]['ADJUSTMENT']);
		}
		
		//--- REFRACTION REFUND AMOUNT --------
		$refraction_refund_cnt = count($mainAdjDataArr[$physician_id][92015]['REFUND']);
		$refraction_refund_amount = 0;
		if($refraction_refund_cnt > 0){
			$refraction_refund_amount = array_sum($mainAdjDataArr[$physician_id][92015]['REFUND']);
		}
		
		//--- NUMBER FOR FOR REFRACTION AMOUNT ------
		$patient_ref_cash = $CLSReports->numberFormat($patient_ref_cash,2);
		$patient_ref_check = $CLSReports->numberFormat($patient_ref_check,2);
		$patient_ref_cc = $CLSReports->numberFormat($patient_ref_cc,2);
		$patient_ref_mo = $CLSReports->numberFormat($patient_ref_mo,2);
		$patient_ref_eft = $CLSReports->numberFormat($patient_ref_eft,2);
		$patient_ref_veep = $CLSReports->numberFormat($patient_ref_veep,2);
		$refraction_write_off_amount = $CLSReports->numberFormat($refraction_write_off_amount,2);
		$refraction_adj_amount = $CLSReports->numberFormat($refraction_adj_amount,2);
		$refraction_refund_amount = $CLSReports->numberFormat($refraction_refund_amount,2);
		
		//--- NUMBER FORMAT FOR TOTAL AMOUNT ----	
		$gross_veep_total_amount += $veep_total_amount;
		$gross_eft_total_amount += $eft_total_amount;
		$gross_mo_total_amount += $mo_total_amount;
		$gross_cc_total_amount += $cc_total_amount;
		$gross_cash_total_amount += $cash_total_amount;
		$gross_check_total_amount += $check_total_amount;
		$gross_write_off_total_amount += $write_off_total_amount;
		$gross_adj_total_amount += $adj_total_amount;
		$gross_refund_total_amount += $refund_total_amount;
		
		//--- NUMBER FORMAT FOR TOTAL AMOUNT ----		
		$veep_total_amount = $CLSReports->numberFormat($veep_total_amount,2);
		$eft_total_amount = $CLSReports->numberFormat($eft_total_amount,2);
		$mo_total_amount = $CLSReports->numberFormat($mo_total_amount,2);
		$cc_total_amount = $CLSReports->numberFormat($cc_total_amount,2);
		$cash_total_amount = $CLSReports->numberFormat($cash_total_amount,2);
		$check_total_amount = $CLSReports->numberFormat($check_total_amount,2);
		$write_off_total_amount = $CLSReports->numberFormat($write_off_total_amount,2);
		$adj_total_amount = $CLSReports->numberFormat($adj_total_amount,2);
		$refund_total_amount = $CLSReports->numberFormat($refund_total_amount,2);
		
		
		//---- HEADER DATA FOR RESULTS ----------
		$physician_name = $physicianNameArr[$physician_id];
		$physicain_total_cnt = $patient_count + $medicare_count + $medicaid_count + $comercial_count;
		$gross_physicain_total_cnt += $patient_count + $medicare_count + $medicaid_count + $comercial_count;
		
		//--- GET GROUP NAME FOR HEADER ------
		$date_range = 'DOS'; 
		if(trim($DateRangeFor) == 'postedDate'){
			$date_range = 'DOC';
		}
		if(trim($DateRangeFor) == 'date_of_payment'){
			$date_range = 'DOP'; 
		}
		if(trim($DateRangeFor) == 'transaction_date'){
			$date_range = 'DOT'; 
		}

		//-- OPERATOR INITIAL -------
		$authProviderNameArr = preg_split('/ /',strtoupper($_SESSION['authProviderName']));
		
		$opInitial = $authProviderNameArr[1][0];
		$opInitial .= $authProviderNameArr[0][0];
		$opInitial = strtoupper($opInitial);
		
		$page_head_data = '';
		$pageHt = "9mm";
		if($p == 0){
			$pageHt = "13mm";
			$page_head_data = <<<DATA
				<table style="width:100%" class="rpt_table rpt_table-bordered">
					<tr>
						<td class="rptbx1" width="262" style="text-align:left;">Provider Analytics Report</td>
						<td class="rptbx2" width="263" style="text-align:left;">Selected Group: $group_name</td>
						<td class="rptbx2" width="263" style="text-align:left;">
							($date_range) $Start_date - $End_date
						</td>
						<td class="rptbx3" width="262" style="text-align:right;">
							Created by $opInitial on $curDate
						</td>
					</tr>
				</table>
DATA;
		}
		
		$pdfData .= <<<DATA
			<page backtop="17mm" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
				<table class="rpt_table rpt_table-bordered rpt_padding">
				<tr class="rpt_headers">
					<td class="rptbx1" style="text-align:left; width:25%">Provider Analytics Report</td>
					<td class="rptbx2" style="text-align:left; width:25%">Selected Group: $group_name</td>
					<td class="rptbx2" style="text-align:left; width:25%">($date_range) $Start_date - $End_date</td>
					<td class="rptbx3" style="text-align:right; width:25%">Created by $opInitial on $curDate</td>
				</tr>
				</table>
			
				<table style="width:100%" class="rpt_table rpt_table-bordered">
					<tr>
						<td class="text_b_w" style="text-align:left;" colspan="6">Physician Name : $physician_name</td>
					</tr>					
					<tr>
						<td class="text_b_w" align="center" style="width:250px">Procedure</td>
						<td class="text_b_w" style="text-align:right; width:30px">Count</td>
						<td class="text_b_w" style="text-align:right; width:180px">Charges</td>
						<td class="text_b_w" style="text-align:right; width:180px">Payment</td>
						<td class="text_b_w" style="text-align:right; width:180px">Write-off</td>
						<td class="text_b_w" style="text-align:right; width:180px">Refund</td>
					</tr>
				</table>
			</page_header>
			<table style="width:100%" class="rpt_table rpt_table-bordered">
				$procedure_content
				<tr>
					<td class="text_10" align="left" valign="top" style="width:250px">COPAY</td>
					<td class="text_10" valign="top" style="text-align:right; width:30px">$total_copay_count</td>
					<td class="text_10" valign="top" style="text-align:right; width:180px"></td>
					<td class="text_10" valign="top" style="text-align:right; width:180px">$total_copay</td>
					<td class="text_10" valign="top" style="text-align:right; width:180px">$total_copay_write_off</td>
					<td class="text_10" valign="top" style="text-align:right; width:180px"></td>
				</tr>
				<tr>
					<td height="1px" bgcolor="#009933" colspan="6"></td>
				</tr>
				<tr>
					<td class="text_10b" style="text-align:right" style="width:250px">$physician_name</td>
					<td class="text_10b" style="text-align:right; width:30px">$total_count</td>
					<td class="text_10b" style="text-align:right; width:180px">$total_procedure_charges</td>
					<td class="text_10b" style="text-align:right; width:180px">$total_paid_for</td>
					<td class="text_10b" style="text-align:right; width:180px">$total_write_off</td>
					<td class="text_10b" style="text-align:right; width:180px">$total_refund_amount</td>
				</tr>
				<tr>
					<td height="1px" bgcolor="#009933" colspan="6"></td>
				</tr>
				<tr>
					<td colspan="6">&nbsp;</td>
				</tr>
			</table>
			<table style="width:100%" class="rpt_table rpt_table-bordered">
				<tr>
					<td class="text_b_w" align="center">Payment details</td>
					<td class="text_b_w" style="text-align:right">Count</td>
					<td class="text_b_w" colspan="6" style="text-align:center">Payments</td>						
					<td class="text_b_w" style="text-align:right">Write-off</td>
					<td class="text_b_w" style="text-align:right">Adjs.</td>
					<td class="text_b_w" style="text-align:right">Refund</td>
				</tr>
				<tr>
					<td class="text_b_w" colspan="2">&nbsp;</td>
					<td class="text_b_w" style="text-align:right">Card</td>
					<td class="text_b_w" style="text-align:right">Cash</td>
					<td class="text_b_w" style="text-align:right">Checks</td>
					<td class="text_b_w" style="text-align:right">Money Order</td>
					<td class="text_b_w" style="text-align:right">EFT</td>
					<td class="text_b_w" style="text-align:right">VEEP</td>
					<td class="text_b_w" colspan="3" >&nbsp;</td>
				</tr>
				<tr>
					<td class="text_10" style="width:90px">Balance Write off</td>
					<td class="text_10" style="text-align:right; width:30px">$total_write_off_count</td>
					<td class="text_10" style="text-align:center; width:89px"></td>
					<td class="text_10" style="text-align:center; width:89px"></td>
					<td class="text_10" style="text-align:center; width:89px"></td>
					<td class="text_10" style="text-align:center; width:89px"></td>
					<td class="text_10" style="text-align:center; width:89px"></td>
					<td class="text_10" style="text-align:center; width:89px"></td>
					<td class="text_10" style="text-align:right; width:89px">$provider_total_write_off</td>
					<td class="text_10" style="text-align:center; width:89px"></td>
					<td class="text_10" style="text-align:center; width:89px"></td>
				</tr>
				<tr><td colspan="11" class="total-row"></td></tr>
				<tr>
					<td class="text_10">Commercial Ins. Det</td>
					<td class="text_10" style="text-align:right; width:30px">$comercial_count</td>
					<td class="text_10" style="text-align:right">$comercial_cc</td>
					<td class="text_10" style="text-align:right">$comercial_cash</td>
					<td class="text_10" style="text-align:right">$comercial_check</td>
					<td class="text_10" style="text-align:right">$comercial_mo</td>
					<td class="text_10" style="text-align:right">$comercial_eft</td>
					<td class="text_10" style="text-align:right">$comercial_veep</td>
					<td class="text_10" style="text-align:right">($commercial_write_off_cnt) $commercial_write_off_amount</td>
					<td class="text_10" style="text-align:right">($commercial_adj_cnt) $commercial_adj_amount</td>
					<td class="text_10" style="text-align:right">($commercial_refund_cnt) $commercial_refund_amount</td>
				</tr>
				<tr><td colspan="11" class="total-row"></td></tr>
				<tr>
					<td class="text_10">Medicaid. Details</td>
					<td class="text_10" style="text-align:right; width:30px">$medicaid_count</td>
					<td class="text_10" style="text-align:right">$medicaid_cc</td>
					<td class="text_10" style="text-align:right">$medicaid_cash</td>
					<td class="text_10" style="text-align:right">$medicaid_check</td>
					<td class="text_10" style="text-align:right">$medicaid_mo</td>
					<td class="text_10" style="text-align:right">$medicaid_eft</td>
					<td class="text_10" style="text-align:right">$medicaid_veep</td>
					<td class="text_10" style="text-align:right">($medicaid_write_off_cnt) $medicaid_write_off_amount</td>
					<td class="text_10" style="text-align:right">($medicaid_adj_cnt) $medicaid_adj_amount</td>
					<td class="text_10" style="text-align:right">($medicaid_refund_cnt) $medicaid_refund_amount</td>
				</tr>
				<tr><td colspan="11" class="total-row"></td></tr>
				<tr>
					<td class="text_10">Medicare Details</td>
					<td class="text_10" style="text-align:right; width:30px">$medicare_count</td>
					<td class="text_10" style="text-align:right">$medicare_cc</td>
					<td class="text_10" style="text-align:right">$medicare_cash</td>
					<td class="text_10" style="text-align:right">$medicare_check</td>
					<td class="text_10" style="text-align:right">$medicare_mo</td>
					<td class="text_10" style="text-align:right">$medicare_eft</td>
					<td class="text_10" style="text-align:right">$medicare_veep</td>
					<td class="text_10" style="text-align:right">($medicare_write_off_cnt) $medicare_write_off_amount</td>
					<td class="text_10" style="text-align:right">($medicare_adj_cnt) $medicare_adj_amount</td>
					<td class="text_10" style="text-align:right">($medicare_refund_cnt) $medicare_refund_amount</td>
				</tr>
				<tr><td colspan="11" class="total-row"></td></tr>
				<tr>
					<td class="text_10">Patient Details</td>
					<td class="text_10" style="text-align:right; width:30px">$patient_count</td>
					<td class="text_10" style="text-align:right">$patient_cc</td>
					<td class="text_10" style="text-align:right">$patient_cash</td>
					<td class="text_10" style="text-align:right">$patient_check</td>
					<td class="text_10" style="text-align:right">$patient_mo</td>
					<td class="text_10" style="text-align:right">$patient_eft</td>
					<td class="text_10" style="text-align:right">$patient_veep</td>
					<td class="text_10" style="text-align:right">($patient_write_off_count) $patient_write_off_amount</td>
					<td class="text_10" style="text-align:right">($patient_adj_cnt) $patient_adj_amount</td>
					<td class="text_10" style="text-align:right">($patient_refund_cnt) $patient_refund_amount</td>
				</tr>
				<tr><td colspan="11" class="total-row"></td></tr>
				<tr>
					<td class="text_10">Patient CoPay Det</td>
					<td class="text_10" style="text-align:right; width:30px">$patient_copay_count</td>
					<td class="text_10" style="text-align:right">$patient_copay_cc</td>
					<td class="text_10" style="text-align:right">$patient_copay_cash</td>
					<td class="text_10" style="text-align:right">$patient_copay_check</td>
					<td class="text_10" style="text-align:right">$patient_copay_mo</td>
					<td class="text_10" style="text-align:right">$patient_copay_eft</td>
					<td class="text_10" style="text-align:right">$patient_copay_veep</td>
					<td class="text_10" style="text-align:right">($patient_copay_write_off_cnt) $patient_copay_write_off_amount</td>
					<td class="text_10" style="text-align:right"></td>
					<td class="text_10" style="text-align:right"></td>
				</tr>
				<tr><td colspan="11" class="total-row"></td></tr>
				<tr>
					<td class="text_10">Vision (Refraction)</td>
					<td class="text_10" style="text-align:right; width:30px">$patient_ref_count</td>
					<td class="text_10" style="text-align:right">$patient_ref_cc</td>
					<td class="text_10" style="text-align:right">$patient_ref_cash</td>
					<td class="text_10" style="text-align:right">$patient_ref_check</td>
					<td class="text_10" style="text-align:right">$patient_ref_mo</td>
					<td class="text_10" style="text-align:right">$patient_ref_eft</td>
					<td class="text_10" style="text-align:right">$patient_ref_veep</td>
					<td class="text_10" style="text-align:right">($refraction_write_off_count) $refraction_write_off_amount</td>
					<td class="text_10" style="text-align:right">($refraction_adj_cnt) $refraction_adj_amount</td>
					<td class="text_10" style="text-align:right">($refraction_refund_cnt) $refraction_refund_amount</td>
				</tr>
				<tr><td colspan="11" class="total-row"></td></tr>
				<tr>
					<td  class="text_10b" style="text-align:right">$physician_name</td>
					<td  class="text_10b" style="text-align:right; width:30px">$physicain_total_cnt</td>
					<td  class="text_10b" style="text-align:right">$cc_total_amount</td>
					<td  class="text_10b" style="text-align:right">$cash_total_amount</td>
					<td  class="text_10b" style="text-align:right">$check_total_amount</td>
					<td  class="text_10b" style="text-align:right">$mo_total_amount</td>
					<td  class="text_10b" style="text-align:right">$eft_total_amount</td>
					<td  class="text_10b" style="text-align:right">$veep_total_amount</td>
					<td  class="text_10b" style="text-align:right">$write_off_total_amount</td>
					<td  class="text_10b" style="text-align:right">$adj_total_amount</td>
					<td  class="text_10b" style="text-align:right">$refund_total_amount</td>
				</tr>
				<tr><td colspan="11" class="total-row"></td></tr>
			</table>
		</page>
DATA;

		// for CSV data
		$csv_data .= <<<DATA
			$page_head_data
			<table style="width:100%" class="rpt_table rpt_table-bordered">
				<tr>
					<td class="text_b_w" style="text-align:left;" colspan="6">Physician Name : $physician_name</td>
				</tr>					
				<tr>
					<td height="2px" colspan="6"></td>
				</tr>
				<tr>
					<td class="text_b_w" width="200" align="center">Procedure</td>
					<td class="text_b_w" width="20" style="text-align:right">Count</td>
					<td class="text_b_w" width="130" style="text-align:right">Charges</td>
					<td class="text_b_w" width="130" style="text-align:right">Payment</td>
					<td class="text_b_w" width="120" style="text-align:right">Write-off</td>
					<td class="text_b_w" width="120" style="text-align:right">Refund</td>
				</tr>
			</table>
			<table style="width:100%" class="rpt_table rpt_table-bordered">
				$procedure_content
				<tr>
					<td class="text_10" width="200" align="left" valign="top">COPAY</td>
					<td class="text_10" width="20"  valign="top" style="text-align:right">$total_copay_count</td>
					<td class="text_10" width="140" valign="top" style="text-align:right"></td>
					<td class="text_10" width="130" valign="top" style="text-align:right">$total_copay</td>
					<td class="text_10" width="120" valign="top" style="text-align:right">$total_copay_write_off</td>
					<td class="text_10" width="120" valign="top" style="text-align:right"></td>
				</tr>
				<tr>
					<td height="5px" bgcolor="#FFFFFF" colspan="6"></td>
				</tr>
				<tr>
					<td height="1px" bgcolor="#009933" colspan="6"></td>
				</tr>
				<tr>
					<td class="text_10b" width="200" style="text-align:right">$physician_name</td>
					<td class="text_10b" width="20" style="text-align:right">$total_count</td>
					<td class="text_10b" width="140" style="text-align:right">$total_procedure_charges</td>
					<td class="text_10b" width="130" style="text-align:right">$total_paid_for</td>
					<td class="text_10b" width="120" style="text-align:right">$total_write_off</td>
					<td class="text_10b" width="120" style="text-align:right">$total_refund_amount</td>
				</tr>
				<tr>
					<td height="1px" bgcolor="#009933" colspan="6"></td>
				</tr>
				<tr>
					<td colspan="6">&nbsp;</td>
				</tr>
			</table>
			<table style="width:100%" class="rpt_table rpt_table-bordered">
				<tr>
					<td class="text_b_w" width="110" align="center">Payment details</td>
					<td class="text_b_w" width="30" style="text-align:right">Count</td>
					<td class="text_b_w" colspan="6" style="text-align:center">Payments</td>						
					<td class="text_b_w" width="100" style="text-align:right">Write-off</td>
					<td class="text_b_w" width="90" style="text-align:right">Adjs.</td>
					<td class="text_b_w" width="80" style="text-align:right">Refund&nbsp;</td>
				</tr>
				<tr>
					<td class="text_b_w" colspan="2">&nbsp;</td>
					<td class="text_b_w" width="100" style="text-align:right">Card</td>
					<td class="text_b_w" width="100" style="text-align:right">Cash</td>
					<td class="text_b_w" width="100" style="text-align:right">Checks</td>
					<td class="text_b_w" width="100" style="text-align:right">Money Order</td>
					<td class="text_b_w" width="100" style="text-align:right">EFT</td>
					<td class="text_b_w" width="100" style="text-align:right">VEEP</td>
					<td class="text_b_w" colspan="3" >&nbsp;</td>
				</tr>
				<tr>
					<td class="text_10" width="120">Balance Write off</td>
					<td class="text_10" width="20" style="text-align:right">$total_write_off_count</td>
					<td class="text_10" width="100" align="center"></td>
					<td class="text_10" width="100" align="center"></td>
					<td class="text_10" width="100" align="center"></td>
					<td class="text_10" width="100" align="center"></td>
					<td class="text_10" width="100" align="center"></td>
					<td class="text_10" width="100" align="center"></td>
					<td class="text_10" width="100" style="text-align:right">$provider_total_write_off</td>
					<td class="text_10" width="90" align="center"></td>
					<td class="text_10" width="80" align="center"></td>
				</tr>
				<tr>
					<td colspan="11" height="1px" bgcolor="#009933"></td>
				</tr>
				<tr>
					<td class="text_10" width="120">Commercial Ins. Det</td>
					<td class="text_10" width="20" style="text-align:right">$comercial_count</td>
					<td class="text_10" width="100" style="text-align:right">$comercial_cc</td>
					<td class="text_10" width="100" style="text-align:right">$comercial_cash</td>
					<td class="text_10" width="100" style="text-align:right">$comercial_check</td>
					<td class="text_10" width="100" style="text-align:right">$comercial_mo</td>
					<td class="text_10" width="100" style="text-align:right">$comercial_eft</td>
					<td class="text_10" width="100" style="text-align:right">$comercial_veep</td>
					<td class="text_10" width="100" style="text-align:right">($commercial_write_off_cnt) $commercial_write_off_amount</td>
					<td class="text_10" width="90" style="text-align:right">($commercial_adj_cnt) $commercial_adj_amount</td>
					<td class="text_10" width="80" style="text-align:right">($commercial_refund_cnt) $commercial_refund_amount</td>
				</tr>
				<tr>
					<td colspan="11" height="1px" bgcolor="#009933"></td>
				</tr>
				<tr>
					<td class="text_10" width="120">Medicaid. Details</td>
					<td class="text_10" width="20" style="text-align:right">$medicaid_count</td>
					<td class="text_10" width="100" style="text-align:right">$medicaid_cc</td>
					<td class="text_10" width="100" style="text-align:right">$medicaid_cash</td>
					<td class="text_10" width="100" style="text-align:right">$medicaid_check</td>
					<td class="text_10" width="100" style="text-align:right">$medicaid_mo</td>
					<td class="text_10" width="100" style="text-align:right">$medicaid_eft</td>
					<td class="text_10" width="100" style="text-align:right">$medicaid_veep</td>
					<td class="text_10" width="100" style="text-align:right">($medicaid_write_off_cnt) $medicaid_write_off_amount</td>
					<td class="text_10" width="90" style="text-align:right">($medicaid_adj_cnt) $medicaid_adj_amount</td>
					<td class="text_10" width="80" style="text-align:right">($medicaid_refund_cnt) $medicaid_refund_amount</td>
				</tr>
				<tr>
					<td colspan="11" height="1px" bgcolor="#009933"></td>
				</tr>
				<tr>
					<td class="text_10" width="120">Medicare Details</td>
					<td class="text_10" width="20" style="text-align:right">$medicare_count</td>
					<td class="text_10" width="100" style="text-align:right">$medicare_cc</td>
					<td class="text_10" width="100" style="text-align:right">$medicare_cash</td>
					<td class="text_10" width="100" style="text-align:right">$medicare_check</td>
					<td class="text_10" width="100" style="text-align:right">$medicare_mo</td>
					<td class="text_10" width="100" style="text-align:right">$medicare_eft</td>
					<td class="text_10" width="100" style="text-align:right">$medicare_veep</td>
					<td class="text_10" width="100" style="text-align:right">($medicare_write_off_cnt) $medicare_write_off_amount</td>
					<td class="text_10" width="90" style="text-align:right">($medicare_adj_cnt) $medicare_adj_amount</td>
					<td class="text_10" width="80" style="text-align:right">($medicare_refund_cnt) $medicare_refund_amount</td>
				</tr>
				<tr>
					<td colspan="11" height="1px" bgcolor="#009933"></td>
				</tr>
				<tr>
					<td class="text_10" width="120">Patient Details</td>
					<td class="text_10" width="20" style="text-align:right">$patient_count</td>
					<td class="text_10" width="100" style="text-align:right">$patient_cc</td>
					<td class="text_10" width="100" style="text-align:right">$patient_cash</td>
					<td class="text_10" width="100" style="text-align:right">$patient_check</td>
					<td class="text_10" width="100" style="text-align:right">$patient_mo</td>
					<td class="text_10" width="100" style="text-align:right">$patient_eft</td>
					<td class="text_10" width="100" style="text-align:right">$patient_veep</td>
					<td class="text_10" width="100" style="text-align:right">($patient_write_off_count) $patient_write_off_amount</td>
					<td class="text_10" width="90" style="text-align:right">($patient_adj_cnt) $patient_adj_amount</td>
					<td class="text_10" width="80" style="text-align:right">($patient_refund_cnt) $patient_refund_amount</td>
				</tr>
				<tr>
					<td colspan="11" height="1px" bgcolor="#009933"></td>
				</tr>
				<tr>
					<td class="text_10" width="120">Patient CoPay Det</td>
					<td class="text_10" width="20" style="text-align:right">$patient_copay_count</td>
					<td class="text_10" width="100" style="text-align:right">$patient_copay_cc</td>
					<td class="text_10" width="100" style="text-align:right">$patient_copay_cash</td>
					<td class="text_10" width="100" style="text-align:right">$patient_copay_check</td>
					<td class="text_10" width="100" style="text-align:right">$patient_copay_mo</td>
					<td class="text_10" width="100" style="text-align:right">$patient_copay_eft</td>
					<td class="text_10" width="100" style="text-align:right">$patient_copay_veep</td>
					<td class="text_10" width="100" style="text-align:right">($patient_copay_write_off_cnt) $patient_copay_write_off_amount</td>
					<td class="text_10" width="90" style="text-align:right"></td>
					<td class="text_10" width="80" style="text-align:right"></td>
				</tr>
				<tr>
					<td colspan="11" height="1px" bgcolor="#009933"></td>
				</tr>
				<tr>
					<td class="text_10" width="120">Vision (Refraction)</td>
					<td class="text_10" width="20" style="text-align:right">$patient_ref_count</td>
					<td class="text_10" width="100" style="text-align:right">$patient_ref_cc</td>
					<td class="text_10" width="100" style="text-align:right">$patient_ref_cash</td>
					<td class="text_10" width="100" style="text-align:right">$patient_ref_check</td>
					<td class="text_10" width="100" style="text-align:right">$patient_ref_mo</td>
					<td class="text_10" width="100" style="text-align:right">$patient_ref_eft</td>
					<td class="text_10" width="100" style="text-align:right">$patient_ref_veep</td>
					<td class="text_10" width="100" style="text-align:right">($refraction_write_off_count) $refraction_write_off_amount</td>
					<td class="text_10" width="90" style="text-align:right">($refraction_adj_cnt) $refraction_adj_amount</td>
					<td class="text_10" width="80" style="text-align:right">($refraction_refund_cnt) $refraction_refund_amount</td>
				</tr>
				<tr>
					<td colspan="11" height="1px" bgcolor="#009933"></td>
				</tr>
				<tr>
					<td colspan="11">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="11" height="1px" bgcolor="#009933"></td>
				</tr>
				<tr>
					<td  class="text_10b" style="text-align:right" width="120">$physician_name</td>
					<td  class="text_10b" width="20" style="text-align:right">$physicain_total_cnt</td>
					<td  class="text_10b" width="100" style="text-align:right">$cc_total_amount</td>
					<td  class="text_10b" width="100" style="text-align:right">$cash_total_amount</td>
					<td  class="text_10b" width="100" style="text-align:right">$check_total_amount</td>
					<td  class="text_10b" width="100" style="text-align:right">$mo_total_amount</td>
					<td  class="text_10b" width="100" style="text-align:right">$eft_total_amount</td>
					<td  class="text_10b" width="100" style="text-align:right">$veep_total_amount</td>
					<td  class="text_10b" width="100" style="text-align:right">$write_off_total_amount</td>
					<td  class="text_10b" width="90" style="text-align:right">$adj_total_amount</td>
					<td  class="text_10b" width="80" style="text-align:right">$refund_total_amount</td>
				</tr>
				<tr>
					<td colspan="11" height="1px" bgcolor="#009933"></td>
				</tr>
			</table>
DATA;
	}
	
$gross_total_procedure_charges = $CLSReports->numberFormat($gross_total_procedure_charges,2);
$gross_total_paid_for = $CLSReports->numberFormat($gross_total_paid_for,2);
$gross_total_write_off = $CLSReports->numberFormat($gross_total_write_off,2);
$gross_total_refund_amount = $CLSReports->numberFormat($gross_total_refund_amount,2);

$gross_mo_total_amount = $CLSReports->numberFormat($gross_mo_total_amount,2);
$gross_eft_total_amount = $CLSReports->numberFormat($gross_eft_total_amount,2);
$gross_veep_total_amount = $CLSReports->numberFormat($gross_veep_total_amount,2);
$gross_cc_total_amount = $CLSReports->numberFormat($gross_cc_total_amount,2);
$gross_cash_total_amount = $CLSReports->numberFormat($gross_cash_total_amount,2);
$gross_check_total_amount = $CLSReports->numberFormat($gross_check_total_amount,2);
$gross_write_off_total_amount = $CLSReports->numberFormat($gross_write_off_total_amount,2);
$gross_adj_total_amount = $CLSReports->numberFormat($gross_adj_total_amount,2);
$gross_refund_total_amount = $CLSReports->numberFormat($gross_refund_total_amount,2);

$csv_data .= <<<DATA
	<table style="width:100%" class="rpt_table rpt_table-bordered">
		<tr><td colspan="6">&nbsp;</td></tr>
		<tr><td colspan="6" height="1px" bgcolor="#009933"></td></tr>
		<tr>
			<td class="text_b_w" width="200" style="text-align:right;"></td>
			<td class="text_b_w" width="40" style="text-align:right;">Count</td>
			<td class="text_b_w" width="200" style="text-align:right;">Charges</td>
			<td class="text_b_w" width="200" style="text-align:right;">Payment</td>
			<td class="text_b_w" width="200" style="text-align:right;">Write-off</td>
			<td class="text_b_w" width="200" style="text-align:right;">Refund</td>
		</tr>
		
		<tr>
			<td  class="text_10b" style="text-align:left;">Physican Total</td>
			<td  width="40" class="text_10b" style="text-align:right;">$gross_total_count</td>
			<td  class="text_10b" style="text-align:right;">$gross_total_procedure_charges</td>
			<td  class="text_10b" style="text-align:right;">$gross_total_paid_for</td>
			<td  class="text_10b" style="text-align:right;">$gross_total_write_off</td>
			<td  class="text_10b" style="text-align:right;">$gross_total_refund_amount</td>
		</tr>
	</table>		
		
	<table style="width:100%" class="rpt_table rpt_table-bordered">
		<tr><td colspan="10">&nbsp;</td></tr>
		 <tr>
			<td class="text_b_w" width="100" style="text-align:right;"></td>
			<td class="text_b_w" style="text-align:center;">Count</td>
			<td class="text_b_w" colspan="6" style="text-align:center;">Payments</td>						
			<td class="text_b_w" style="text-align:center;">Write-off</td>
			<td class="text_b_w" style="text-align:center;">Adjs.</td>
			<td class="text_b_w" style="text-align:center;">Refund</td>
		</tr>
		<tr>
			<td class="text_b_w" colspan="2">&nbsp;</td>
			<td class="text_b_w" style="text-align:center;">Card</td>
			<td class="text_b_w" style="text-align:center;">Cash</td>
			<td class="text_b_w" style="text-align:center;">Checks</td>
			<td class="text_b_w" style="text-align:center;">Money Order</td>
			<td class="text_b_w" style="text-align:center;">EFT</td>
			<td class="text_b_w" style="text-align:center;">VEEP</td>
			<td class="text_b_w" colspan="3" >&nbsp;</td>
		</tr>
		<tr>
			<td class="text_10b" width="100" style="text-align:left;">Payment total</td>
			<td class="text_10b" width="30" style="text-align:right;">$gross_physicain_total_cnt</td>
			<td class="text_10b" width="30" style="text-align:right;">$gross_cc_total_amount</td>
			<td class="text_10b" width="100" style="text-align:center;">$gross_cash_total_amount</td>
			<td class="text_10b" width="100" style="text-align:center;">$gross_check_total_amount</td>
			<td class="text_10b" width="100" style="text-align:center;">$gross_mo_total_amount</td>
			<td class="text_10b" width="100" style="text-align:center;">$gross_eft_total_amount</td>
			<td class="text_10b" width="100" style="text-align:center;">$gross_veep_total_amount</td>
			<td class="text_10b" width="100" style="text-align:center;">$gross_write_off_total_amount</td>
			<td class="text_10b" width="100" style="text-align:right;">$gross_adj_total_amount</td>
			<td class="text_10b" width="100" style="text-align:center;">$gross_refund_total_amount</td>
		</tr>
	</table>
DATA;

$pdfData .= <<<DATA
	<table style="width:100%" class="rpt_table rpt_table-bordered">
		<tr><td colspan="6">&nbsp;</td></tr>
		<tr><td colspan="6" height="1px" bgcolor="#009933"></td></tr>
		<tr>
			<td class="text_b_w" style="text-align:center; width:160px"></td>
			<td class="text_b_w" style="text-align:center; width:40px">Count</td>
			<td class="text_b_w" style="text-align:center; width:200px">Charges</td>
			<td class="text_b_w" style="text-align:center; width:200px">Payment</td>
			<td class="text_b_w" style="text-align:center; width:195px">Write-off</td>
			<td class="text_b_w" style="text-align:center; width:195px">Refund</td>
		</tr>
		<tr><td colspan="6" class="total-row"></td></tr>
		<tr>
			<td  class="text_10b" style="text-align:left;">Physican Total</td>
			<td  class="text_10b" style="text-align:right; width:40px">$gross_total_count</td>
			<td  class="text_10b" style="text-align:right;">$gross_total_procedure_charges</td>
			<td  class="text_10b" style="text-align:right;">$gross_total_paid_for</td>
			<td  class="text_10b" style="text-align:right;">$gross_total_write_off</td>
			<td  class="text_10b" style="text-align:right;">$gross_total_refund_amount</td>
		</tr>
		<tr><td colspan="6" class="total-row"></td></tr>
	</table>		
		
	<table style="width:100%" class="rpt_table rpt_table-bordered">
		<tr><td colspan="11">&nbsp;</td></tr>
		 <tr>
			<td class="text_b_w" style="text-align:center;"></td>
			<td class="text_b_w" style="text-align:center;">Count</td>
			<td class="text_b_w" colspan="6" style="text-align:center;">Payments</td>						
			<td class="text_b_w" style="text-align:center;">Write-off</td>
			<td class="text_b_w" style="text-align:center;">Adjs.</td>
			<td class="text_b_w" style="text-align:center;">Refund</td>
		</tr>
		<tr>
			<td class="text_b_w" colspan="2">&nbsp;</td>
			<td class="text_b_w" style="text-align:center;">Card</td>
			<td class="text_b_w" style="text-align:center;">Cash</td>
			<td class="text_b_w" style="text-align:center;">Checks</td>
			<td class="text_b_w" style="text-align:center;">Money Order</td>
			<td class="text_b_w" style="text-align:center;">EFT</td>
			<td class="text_b_w" style="text-align:center;">VEEP</td>
			<td class="text_b_w" colspan="3" >&nbsp;</td>
		</tr>
		<tr><td colspan="11" class="total-row"></td></tr>
		<tr>
			<td class="text_10b" style="text-align:left; width:90px">Payment total</td>
			<td class="text_10b" style="text-align:right; width:30px">$gross_physicain_total_cnt</td>
			<td class="text_10b" style="text-align:right; width:89px">$gross_cc_total_amount</td>
			<td class="text_10b" style="text-align:right; width:89px">$gross_cash_total_amount</td>
			<td class="text_10b" style="text-align:right; width:89px">$gross_check_total_amount</td>
			<td class="text_10b" style="text-align:right; width:89px">$gross_mo_total_amount</td>
			<td class="text_10b" style="text-align:right; width:89px">$gross_eft_total_amount</td>
			<td class="text_10b" style="text-align:right; width:89px">$gross_veep_total_amount</td>
			<td class="text_10b" style="text-align:right; width:89px">$gross_write_off_total_amount</td>
			<td class="text_10b" style="text-align:right; width:89px">$gross_adj_total_amount</td>
			<td class="text_10b" style="text-align:right; width:89px">$gross_refund_total_amount</td>
		</tr>
		<tr><td colspan="11" class="total-row"></td></tr>
	</table>
			
DATA;
}
?>