<?php
$op = 'l';
//---- GET FACILITY ID ARRAY -------
$firstIdArr = array_keys($mainResultArr);
$procedure_total_arr = array();

$j = $k = $m = 0; 
$chartData = array();
$chartFacilityId = array();
$chartProcedureId = array();
$chartProcedureName = array();
$chartProviderId = array();
$chartProcedureData = array();
$arrCountEnc=array();
$page_content = '';

$colspan=13;
$pdfColW="65";
$total_cols = 13;
$pageHt = '20mm';
if(sizeof($arrInsurance)>0){
	$colspan=$colspan+1;
	$total_cols=$total_cols+1;
}
$blankTDRVU = $titleTDRVU = $titleTDRVU_PDF = '';

if($rvu=='1'){
	$total_cols =$colspan+1;
	$colspan=$colspan+1;
	$pdfColW="54";
	$blankTDRVU='<td class="text_10" style="text-align:right;"></td>';
	$titleTDRVU='<td class="text_b_w" width="90px" style="text-align:right;">Non Fac. Pricing Amt.</td>';
	$pageHt = '22mm';
}
$w_cols = 90/($total_cols-1);
$w_first_col = 100 - ($w_cols * ($total_cols-1))."%"; //"10%";
$w_cols.='%';

if($rvu=='1'){
	$titleTDRVU_PDF='<td class="text_b_w" style="text-align:right; width:'.$w_cols.'">Non Fac. Pricing Amt.</td>';
}
$extraTD = '';
if($groupby != "Procedure"){
	$extraTD = '<td class="text_10b" style="text-align:right;"></td>';
}
//MAKING OUTPUT DATA
$file_name="CPT_Analytic.csv";
$csv_file_name= write_html("", $file_name);
//CSV FILE NAME
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');

$arr=array();
$arr[]='CPT Analysis Report (Summary)';
$arr[]="$dayReport ($search) From : $fromDate To : $toDate";
$arr[]="Created by $opInitial on $curDate";
fputcsv($fp,$arr, ",","\"");
$arr=array();
$arr[]="Selected Group: ".$group_name;
$arr[]="Selected Facility : ".$practice_name;
$arr[]="Selected Physician : ".$physician_name;
$arr[]="Selected Crediting Physician : ".$crediting_physician_name;
fputcsv($fp,$arr, ",","\"");
$arr=array();
$arr[]="Selected CPT Cat. :".$cpt_cat_name;
$arr[]="Selected Procedure :".$procedure_name;
$arr[]="Selected Insurance :".$insurance_name;
fputcsv($fp,$arr, ",","\"");

if($groupby == "Procedure" || $groupby == "Physician" || $groupby == "CPTCategory" || $groupby == "ins_group"){

$CSVColulm = 'Physician Name';
$CSVColulm1 = "";
if($groupby == "Facility" || $groupby == "Physician" || $groupby == "CPTCategory" || $groupby=='ins_group'){
	$CSVColulm = 'CPT Code';
	$CSVColulm1 = 'CPT Desc';
}
$GourpCSV = "";
if($groupby == "Procedure"){$GourpCSV='Procedure';}
elseif($groupby == "Physician"){$GourpCSV='Physician';}
elseif($groupby == "CPTCategory"){$GourpCSV='CPT Category';}
elseif($groupby == "ins_group"){$GourpCSV ='Ins. Group';}
$arr=array();
$arr[]=$GourpCSV;
$arr[]=$CSVColulm;
if($CSVColulm1){
	$arr[]=$CSVColulm1;
}
$arr[]="Proc. / Encs.";
if(sizeof($arrInsurance)>0){
$arr[]="Pri# / Sec#";
}
$arr[]="Charges";
$arr[]="Allowed Amt";
if($rvu=='1'){
$arr[]="Non Fac. Pricing Amt.";
}
$arr[]="Pat. Paid";
$arr[]="Ins. Paid";
$arr[]="Payments";
$arr[]="Credit";
$arr[]="Adj.";
$arr[]="Pat. Due";
$arr[]="Ins. Due";
$arr[]="Balance";
fputcsv($fp,$arr, ",","\"");
$fp = fopen ($csv_file_name, 'a+');
unset($arr);

//FOR FACILITY AND PHYSICIAN
for($f=0;$f<count($firstIdArr);$f++){
	$procedure_sub_total_arr = array();
	$arrCountEncSub=array();
	$firstId = $firstIdArr[$f];
	if($groupby == "Procedure"){
		$procedureId = $firstId;
		$procedureName = core_refine_user_input($procedureNameArr[$procedureId]);
		$page_content .= <<<DATA
			<tr >
				<td align="left" colspan="$colspan" class="text_b_w">Procedure : $procedureName</td>
			</tr>
DATA;
	}
	else if($groupby == "Physician"){
		//--- GET PHYSICIAN NAME ------
	$providerId = $firstId;
	$physicianName = core_refine_user_input($providerNameArr[$providerId]);
	$page_content .= <<<DATA
		<tr><td colspan="$colspan" class="text_b_w">Physician: $physicianName</td></tr>
DATA;
	}
	else if($groupby == "CPTCategory"){
		//--- GET PHYSICIAN NAME ------
	$CPTId = $firstId;
	$CPTName = $category_data_arr[$CPTId];
	$page_content .= <<<DATA
		<tr><td colspan="$colspan" class="text_b_w">CPT Category: $CPTName</td></tr>
DATA;
	}
	else if($groupby == "ins_group"){
		//--- GET PHYSICIAN NAME ------
	$insGrpid = $firstId;
	$insGrpName = $arrAllInsGroups[$insGrpid];
	$page_content .= <<<DATA
		<tr><td colspan="$colspan" class="text_b_w">Ins. Group: $insGrpName</td></tr>
DATA;
	}	
	$secondDataArr = $mainResultArr[$firstId];
	$secondIdArr = array();
	if(count($secondDataArr)>0){
		$secondIdArr = array_keys($secondDataArr);
	}
	$procedure_detail_arr = array();

	for($i=0;$i<count($secondIdArr);$i++){
		$secondId = $secondIdArr[$i];
		$encounter_detail_arr = array();
		$arrTemp=array();
		$thirdDataArr = $secondDataArr[$secondId];
		
		if($groupby == "Physician" || $groupby == "CPTCategory" || $groupby == "ins_group"){
			$procedureId = $secondId;
			$procedureName = core_refine_user_input($procedureCodeArr[$procedureId]);
			$procedureDesc = core_refine_user_input($procedureDescArr[$procedureId]);
		}
		else if($groupby == "Procedure"){
			$providerId = $secondId;
			$physicianName = $providerNameArr[$providerId];
		}

		$thirdIdArr = array();
		if(count($thirdDataArr)>0){
			$thirdIdArr = array_keys($thirdDataArr);
		}
		
		for($d=0;$d<count($thirdIdArr);$d++){
			$totalBalance='';
			$paidForProc = $patPaidAmt = $insPaidAmt = $paidAmt = 0;
			$creditAmount= $adjustmentAmount = $write_off=0 ;
			$thirdId = $thirdIdArr[$d];
			
			$encounterDataArr = $thirdDataArr[$thirdId];
			$charge_list_detail_id = $encounterDataArr['charge_list_detail_id'];
			$encounterId = $encounterDataArr['encounter_id'];
			
			$arrCountEnc[$encounterId] = $encounterId;
			$arrCountEncSub[$encounterId] = $encounterId;
			$arrCountEncTot[$encounterId] = $encounterId;

			if($encounterDataArr["newBalance"]>0){
				$totalBalance = $encounterDataArr['newBalance'];
			}else{
				if($encounterDataArr['overPaymentForProc']>0){
					$totalBalance = - $encounterDataArr['overPaymentForProc'];
				}else{
					$totalBalance = $encounterDataArr['newBalance'];
				}
			}			
			$insuranceDue = $encounterDataArr['insDue'];
			$insuranceDue = ($insuranceDue<0) ? 0 : $insuranceDue; 
			$patientDue = $encounterDataArr['patDue'];
			$patientDue = ($patientDue<0) ? 0 : $patientDue;
			$patientDue = ($totalBalance<=0) ? 0 : $patientDue;
			
			$encounter_detail_arr['PATIENTDUE'][] = $patientDue;
			$encounter_detail_arr['INSURANCEDUE'][] = $insuranceDue;
			$encounter_detail_arr['BALANCE'][] = $totalBalance;
			
			$submitted = $encounterDataArr["submitted"];
			$first_posted_date = $encounterDataArr["first_posted_date"];
			
			
			//--- GET TOTAL CHARGES -------
			if(($DateRangeFor=='transaction_date' || $DateRangeFor=='date_of_payment') && ($submitted=='true' && $first_posted_date>=$Start_date && $first_posted_date<=$End_date)){
				$encounter_detail_arr['CHARGES'][$charge_list_detail_id] = $encounterDataArr['totalAmt'];
				$encounter_detail_arr['COUNT'][$charge_list_detail_id] = $encounterDataArr['units'];
			}
			if($DateRangeFor!='transaction_date' && $DateRangeFor!='date_of_payment'){
				$encounter_detail_arr['CHARGES'][$charge_list_detail_id] = $encounterDataArr['totalAmt'];
				$encounter_detail_arr['COUNT'][$charge_list_detail_id] = $encounterDataArr['units'];
			}

			//ALLOWED AMOUNT
			$encounter_detail_arr['APPROVED_AMT'][$charge_list_detail_id] = $encounterDataArr['approvedAmt'];


			//---- GET PAID AMOUNT FOR PATIENT ------
			if(count($mainEncounterPayArr['ALL'][$charge_list_detail_id])){
				// TOTAL PAYMENT
				$paidAmt = array_sum($mainEncounterPayArr['ALL'][$charge_list_detail_id]);
				//PATIENT PAYMENT
				$patPaidAmt  = array_sum($patPayDetArr['ALL']['patPaid'][$charge_list_detail_id]);
				//INSURANCE PAYMENT
				$insPaidAmt  = array_sum($patPayDetArr['ALL']['insPaid'][$charge_list_detail_id]);
			}
			$patCrdDbt = array_sum($pay_crd_deb_arr[$charge_list_detail_id]['Patient']);
			$patPaidAmt+= $patCrdDbt;
			$insCrdDbt = array_sum($pay_crd_deb_arr[$charge_list_detail_id]['Insurance']);
			$insPaidAmt+= $insCrdDbt;
			$paidAmt += $patCrdDbt + $insCrdDbt;
			
			
			// CREDIT AMOUNT and TOTAL BALANCE
			$creditAmount = $encounterDataArr['overPaymentForProc'];
			// ADJUSTMENT AMOUNT
			$adjustmentAmount = $arrAdjustmentAmt[$charge_list_detail_id] + $normalWriteOffAmt[$charge_list_detail_id];

			$encounter_detail_arr['CREDIT'][] = $creditAmount;
			$encounter_detail_arr['ADJUSTMENT'][] = $adjustmentAmount;
			$encounter_detail_arr['PATIENTPAID'][] = $patPaidAmt;
			$encounter_detail_arr['INSURANCEPAID'][] = $insPaidAmt;
			$encounter_detail_arr['PAYMENTS'][]  = $paidAmt;

			//INSURANCE COUNT
			if(sizeof($arrInsurance)>0){
				if($arrInsurance[$encounterDataArr['primaryInsuranceCoId']]){
					$encounter_detail_arr['PRI_INSURANCE'][$encounterId]=$encounterId;
					$procedure_sub_total_arr['PRI_INSURANCE'][$encounterId]=$encounterId;
					$arrGrandTot['PRI_INSURANCE'][$encounterId]=$encounterId;
				}else
				if($arrInsurance[$encounterDataArr['secondaryInsuranceCoId']]){
					$encounter_detail_arr['SEC_INSURANCE'][$encounterId]=$encounterId;
					$procedure_sub_total_arr['SEC_INSURANCE'][$encounterId]=$encounterId;
					$arrGrandTot['SEC_INSURANCE'][$encounterId]=$encounterId;
				}
			}
		}

		
		//--- GET TOTAL CHARGES FOR SINGLE PROVIDER -----
		$units = array_sum($encounter_detail_arr['COUNT']);
		$totalAmount = array_sum($encounter_detail_arr['CHARGES']);
		$totalApprovedAmt = array_sum($encounter_detail_arr['APPROVED_AMT']);
		$paidForProc = array_sum($encounter_detail_arr['PAYMENTS']);
		$patientPaid = array_sum($encounter_detail_arr['PATIENTPAID']);
		$insurancePaid = array_sum($encounter_detail_arr['INSURANCEPAID']);
		$patientDue = array_sum($encounter_detail_arr['PATIENTDUE']);
		$insuranceDue = array_sum($encounter_detail_arr['INSURANCEDUE']);
		$credit = array_sum($encounter_detail_arr['CREDIT']);
		$adjustment = array_sum($encounter_detail_arr['ADJUSTMENT']);
		$balance = array_sum($encounter_detail_arr['BALANCE']);
		$priInsCnt = count($encounter_detail_arr['PRI_INSURANCE']);
		$secInsCnt = count($encounter_detail_arr['SEC_INSURANCE']);


		//--- GET SUB TOTAL FOR PROCEDURE -------
		$procedure_sub_total_arr['COUNT'][] = $units;
		$procedure_sub_total_arr['CHARGES'][] = $totalAmount;
		$procedure_sub_total_arr['APPROVED_AMT'][] = $totalApprovedAmt;
		$procedure_sub_total_arr['PAYMENTS'][] = $paidForProc;
		$procedure_sub_total_arr['PATIENTPAID'][] = $patientPaid;			
		$procedure_sub_total_arr['INSURANCEPAID'][] = $insurancePaid;			
		$procedure_sub_total_arr['CREDIT'][] = $credit;			
		$procedure_sub_total_arr['ADJUSTMENT'][] = $adjustment;			
		$procedure_sub_total_arr['PATIENTDUE'][] = $patientDue;			
		$procedure_sub_total_arr['INSURANCEDUE'][] = $insuranceDue;	
		$procedure_sub_total_arr['BALANCE'][] = $balance;
		
		//---- NUMBER FORMAT FOR SINGLE PROVIDER AMOUUNT ---
		$totalAmount = $CLSReports->numberFormat($totalAmount,2);
		$totalApprovedAmt = $CLSReports->numberFormat($totalApprovedAmt,2,1);
		$paidForProc = $CLSReports->numberFormat($paidForProc,2);
		$patientPaid = $CLSReports->numberFormat($patientPaid,2);
		$insurancePaid = $CLSReports->numberFormat($insurancePaid,2);
		$credit = $CLSReports->numberFormat($credit,2);
		$adjustment = $CLSReports->numberFormat($adjustment,2);
		$patientDue = $CLSReports->numberFormat($patientDue,2);
		$insuranceDue = $CLSReports->numberFormat($insuranceDue,2);
		$balance = $CLSReports->numberFormat($balance,2);

		// RVU VALUES
		$rvuTd= $rvuCSV = '';
		if($rvu=='1'){
			$work_rvu = $allRVUValues[$procedureId]['work_rvu'];
			$pe_rvu = $allRVUValues[$procedureId]['pe_rvu'];
			$mp_rvu = $allRVUValues[$procedureId]['mp_rvu'];

			$facPricingAmt = round((((($work_rvu * $bugdet_neu_adj_gpci) * $work_gpci) + ($pe_rvu * $practice_expense_gpci) + ($mp_rvu * $malpractice_gpci))  * $convFactor)  * $units, 2);
			
			if($facPricingAmt<=0){ $facPricingAmt='';}
			/*$rvuTd='<td class="text_10" style="text-align:right; width:50px" valign="top">'.numberFormat($facPricingAmt,2).'</td>';*/
			$rvuTd='<td class="text_10" style="text-align:right; width:'.$w_cols.'" valign="top">'.$CLSReports->numberFormat($facPricingAmt,2).'</td>';
			$rvuCSV = $CLSReports->numberFormat($facPricingAmt,2);
		}
		$insCountTd='';
		if(sizeof($arrInsurance)>0){
			$insCountTd='<td class="text_10" style="text-align:center; width:'.$w_cols.'">\''.$priInsCnt.'/'.$secInsCnt.'\'</td>';	
		}
		
		$page_content .= <<<DATA
			<tr bgcolor="#FFFFFF">
DATA;
		if($groupby == "Physician" || $groupby == "CPTCategory" || $groupby=="ins_group"){
			if(strlen($procedureName)>14){
				$procedureName=substr($procedureName,0,13).'...';
			}
			$page_content .= <<<DATA
			<td align="left" class="text_10" width="70" style=" word-wrap:break-word; width:$w_first_col">$procedureName</td>
			<td align="left" class="text_10" width="70" style=" word-wrap:break-word; width:$w_first_col">$procedureDesc</td>
DATA;
		}else{
		$page_content .= <<<DATA
			<td align="left" class="text_10" style="word-wrap:break-word; width:$w_first_col">$physicianName</td>
DATA;
		}
		$page_content .= <<<DATA
			<td class="text_10" style="text-align:center; word-wrap:break-word; width:$w_cols" >$units</td>
			$insCountTd
			<td class="text_10" style="text-align:right; word-wrap:break-word; width:$w_cols">$totalAmount</td>
			<td class="text_10" style="text-align:right; word-wrap:break-word; width:$w_cols">$totalApprovedAmt</td>
			$rvuTd
			<td class="text_10" style="text-align:right; word-wrap:break-word; width:$w_cols">$patientPaid</td>
			<td class="text_10" style="text-align:right; word-wrap:break-word; width:$w_cols">$insurancePaid</td>
			<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$paidForProc</td>
			<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$credit</td>
			<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$adjustment</td>
			<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$patientDue</td>
			<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$insuranceDue</td>
			<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$balance</td>
		</tr>
DATA;
		if($groupby == "Procedure"){$GourpCSV=$procedureName;}
		elseif($groupby == "Physician"){$GourpCSV=$physicianName;}
		elseif($groupby == "CPTCategory"){$GourpCSV=$CPTName;}
		elseif($groupby == "ins_group"){$GourpCSV =$insGrpName;}
		
		$secGroup='';	
		if($groupby == "Physician" || $groupby == "CPTCategory" || $groupby=="ins_group"){
			$secGroup=$procedureName;
		}else{
			$secGroup=$physicianName;
		}

		$arr[$GourpCSV][$secGroup][$i]['main_gruop']=$GourpCSV;
		if($groupby == "Physician" || $groupby == "CPTCategory" || $groupby=="ins_group"){
			$arr[$GourpCSV][$secGroup][$i]['optional_1']=$procedureName;	
			$arr[$GourpCSV][$secGroup][$i]['optional_1_1']=$procedureDesc;	
		}else{
			$arr[$GourpCSV][$secGroup][$i]['optional_1']=$physicianName;	
		} 
		$arr[$GourpCSV][$secGroup][$i]['units']=$units;
		if(sizeof($arrInsurance)>0){
			$arr[$GourpCSV][$secGroup][$i]['counts']= "'$priInsCnt / $secInsCnt'";	
		}
		$arr[$GourpCSV][$secGroup][$i]['charges']=$totalAmount;
		$arr[$GourpCSV][$secGroup][$i]['apporved_amt']=$totalApprovedAmt;
		if($rvu=='1'){	
			$arr[$GourpCSV][$secGroup][$i]['rvu']=$rvuCSV;
		}
		$arr[$GourpCSV][$secGroup][$i]['pat_paid']=$patientPaid;
		$arr[$GourpCSV][$secGroup][$i]['ins_paid']=$insurancePaid;
		$arr[$GourpCSV][$secGroup][$i]['total_paid']=$paidForProc;
		$arr[$GourpCSV][$secGroup][$i]['credit']=$credit;
		$arr[$GourpCSV][$secGroup][$i]['adjustment']=$adjustment;
		$arr[$GourpCSV][$secGroup][$i]['pat_due']=$patientDue;
		$arr[$GourpCSV][$secGroup][$i]['ins_due']=$insuranceDue;
		$arr[$GourpCSV][$secGroup][$i]['balance']=$balance;
	}

	//---- PROCEDURE/PHYSICIAN TOTAL ---
	$totTotalEnc = count($arrCountEncSub);
	$Sub_TotalUnits = array_sum($procedure_sub_total_arr['COUNT']);
	$Sub_TotalAmount = array_sum($procedure_sub_total_arr['CHARGES']);
	$Sub_TotalApprovedAmt = array_sum($procedure_sub_total_arr['APPROVED_AMT']);
	$Sub_totalPatPaid = array_sum($procedure_sub_total_arr['PATIENTPAID']);
	$Sub_totalInsPaid = array_sum($procedure_sub_total_arr['INSURANCEPAID']);
	$Sub_PaidForProc = array_sum($procedure_sub_total_arr['PAYMENTS']);
	$Sub_totalCredit = array_sum($procedure_sub_total_arr['CREDIT']);
	$Sub_totalAdjustment = array_sum($procedure_sub_total_arr['ADJUSTMENT']);
	$Sub_totalPatDue = array_sum($procedure_sub_total_arr['PATIENTDUE']);
	$Sub_totalInsDue = array_sum($procedure_sub_total_arr['INSURANCEDUE']);
	$Sub_totalBalance = array_sum($procedure_sub_total_arr['BALANCE']);
	$Sub_totalPriCnt = count($procedure_sub_total_arr['PRI_INSURANCE']);
	$Sub_totalSecCnt = count($procedure_sub_total_arr['SEC_INSURANCE']);
	
	//GRAND TOTAL ARRAY
	$arrGrandTot['COUNT'][] = $Sub_TotalUnits;
	$arrGrandTot['CHARGES'][] = $Sub_TotalAmount;
	$arrGrandTot['APPROVED_AMT'][] = $Sub_TotalApprovedAmt;
	$arrGrandTot['PAYMENTS'][] = $Sub_PaidForProc;
	$arrGrandTot['PATIENTPAID'][] = $Sub_totalPatPaid;			
	$arrGrandTot['INSURANCEPAID'][] = $Sub_totalInsPaid;			
	$arrGrandTot['CREDIT'][] = $Sub_totalCredit;			
	$arrGrandTot['ADJUSTMENT'][] = $Sub_totalAdjustment;			
	$arrGrandTot['PATIENTDUE'][] = $Sub_totalPatDue;			
	$arrGrandTot['INSURANCEDUE'][] = $Sub_totalInsDue;			
	$arrGrandTot['BALANCE'][] = $Sub_totalBalance;			

	$subInsTd='';
	if(sizeof($arrInsurance)>0){	
		$subInsTd='<td class="text_10" style="text-align:center; width:'.$w_cols.'">\''.$Sub_totalPriCnt.'/'.$Sub_totalSecCnt.'\'</td>';
	}
	
	$page_content.='
	<tr><td class="total-row" colspan="'.$colspan.'" ></td></tr>
	<tr bgcolor="#FFFFFF">
		'.$extraTD.'
		<td class="text_10b" style="text-align:right;">Total :</td>
		<td class="text_10b" style="text-align:center;">\''.$Sub_TotalUnits.'/'.$totTotalEnc.'\'</td>
		'.$subInsTd.'
		<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat($Sub_TotalAmount,2).'</td>
		<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat($Sub_TotalApprovedAmt,2).'</td>
		'.$blankTDRVU.'
		<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat($Sub_totalPatPaid,2).'</td>
		<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat($Sub_totalInsPaid,2).'</td>
		<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat($Sub_PaidForProc,2).'</td>
		<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat($Sub_totalCredit,2).'</td>
		<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat($Sub_totalAdjustment,2).'</td>
		<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat($Sub_totalPatDue,2).'</td>
		<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat($Sub_totalInsDue,2).'</td>
		<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat($Sub_totalBalance,2).'</td>
	</tr>
	<tr><td class="total-row" colspan="'.$colspan.'" ></td></tr>';
}


//SORTING OF CSV
$tempArr=$arr;
unset($arr);
ksort($tempArr, SORT_NATURAL);

foreach($tempArr as $firstGroup =>$arrSecGroup){
	ksort($arrSecGroup, SORT_NATURAL);
	foreach($arrSecGroup as $secGroup =>$arrData){
		foreach($arrData as $arrDetails){

			$arr=array();
			$arr[]=$firstGroup;
			if($groupby == "Physician" || $groupby == "CPTCategory" || $groupby=="ins_group"){
				$arr[]=$arrDetails['optional_1'];	
				$arr[]=$arrDetails['optional_1_1'];	//CPT DESCRIPTION
			}else{
				$arr[]=$arrDetails['optional_1'];	
			}
		
			$arr[]=$arrDetails['units'];
			if(sizeof($arrInsurance)>0){
				$arr[]=$arrDetails['counts'];
			}
			$arr[]=$arrDetails['charges'];
			$arr[]=$arrDetails['apporved_amt'];
			if($rvu=='1'){	
				$arr[]=$arrDetails['rvu']; 
			}
			$arr[]=$arrDetails['pat_paid'];
			$arr[]=$arrDetails['ins_paid'];
			$arr[]=$arrDetails['total_paid'];
			$arr[]=$arrDetails['credit'];
			$arr[]=$arrDetails['adjustment'];
			$arr[]=$arrDetails['pat_due'];
			$arr[]=$arrDetails['ins_due'];
			$arr[]=$arrDetails['balance'];

			fputcsv($fp,$arr, ",","\"");			
		}
	}
}
unset($tempArr);
unset($arr);

}else{
	
	$arr=array();
	$arr[]='Facility';
	$arr[]='Physician';
	$arr[]='CPT Code';
	$arr[]='CPT Desc';
	$arr[]="Proc. / Encs.";
	if(sizeof($arrInsurance)>0){
	$arr[]="Pri# / Sec#";
	}
	$arr[]="Charges";
	$arr[]="Allowed Amt";
	if($rvu=='1'){
	$arr[]="Non Fac. Pricing Amt.";
	}
	$arr[]="Pat. Paid";
	$arr[]="Ins. Paid";
	$arr[]="Payments";
	$arr[]="Credit";
	$arr[]="Adj.";
	$arr[]="Pat. Due";
	$arr[]="Ins. Due";
	$arr[]="Balance";
	fputcsv($fp,$arr, ",","\"");
	$fp = fopen ($csv_file_name, 'a+');
	unset($arr);
// FACILITY
$procedure_total_arr=array();
for($f=0;$f<count($firstIdArr);$f++){
	$arrFacEnc=array();
	$arrFacTot=array();
	
	$firstId = $firstIdArr[$f];

	$facilityId = $firstId;
	$facilityName = core_refine_user_input($facilityNameArr[$facilityId]);
	$page_content .= <<<DATA
	<tr>
		<td align="left" colspan="$colspan" class="text_b_w">Facility : $facilityName</td>
	</tr>
DATA;

	$secondDataArr = $mainResultArr[$firstId];
	$secondIdArr = array();
	if(count($secondDataArr)>0){
		$secondIdArr = array_keys($secondDataArr);
	}
	
	for($p=0;$p<count($secondIdArr);$p++){
		$arrCountEncSub=array();
		$procedure_detail_arr = array();
		$procedure_sub_total_arr = array();
		$secondId = $secondIdArr[$p];
		$thirdDataArr = $secondDataArr[$secondId];
		
		$thirdIdArr = array();
		if(count($thirdDataArr)>0){
			$thirdIdArr = array_keys($thirdDataArr);
		}
		
		$providerId = $secondId;
		$physicianName = core_refine_user_input($providerNameArr[$providerId]);
		

		$facilityId = $firstId;
		$facilityName = core_refine_user_input($facilityNameArr[$facilityId]);
		$page_content .= <<<DATA
		<tr>
			<td align="left" colspan="$colspan" class="text_b_w">Physician : $physicianName</td>
		</tr>
DATA;
		
		for($c=0;$c<count($thirdIdArr);$c++){
			$encounter_detail_arr=array();
			$arrTemp=array();
			$thirdId = $thirdIdArr[$c];
			$procedureId = $thirdId;
			$procedureName = core_refine_user_input($procedureCodeArr[$procedureId]);
			$procedureDesc = core_refine_user_input($procedureDescArr[$procedureId]);

			$fourthDataArr = $thirdDataArr[$thirdId];
			$fourthIdArr = array();
			if(count($fourthDataArr)>0){
				$fourthIdArr = array_keys($fourthDataArr);
			}
	
			for($d=0;$d<count($fourthIdArr);$d++){
				$totalBalance='';
				$paidForProc = $patPaidAmt = $insPaidAmt = $paidAmt = 0;
				$creditAmount= $adjustmentAmount = $write_off=0 ;
				$fourthId = $fourthIdArr[$d];
				
				//--- GET ENCOUNTER DETAILS--------
				$encounterDataArr = $fourthDataArr[$fourthId];
	
				$charge_list_detail_id = $encounterDataArr['charge_list_detail_id'];
				$encounterId = $encounterDataArr['encounter_id'];
				
				$arrCountEnc[$encounterId] = $encounterId;
				$arrCountEncSub[$encounterId] = $encounterId;
				$arrCountEncTot[$encounterId] = $encounterId;
				$arrFacEnc[$encounterId] = $encounterId;
	
				if($encounterDataArr["newBalance"]>0){
					$totalBalance = $encounterDataArr['newBalance'];
				}else{
					if($encounterDataArr['overPaymentForProc']>0){
						$totalBalance = - $encounterDataArr['overPaymentForProc'];
					}else{
						$totalBalance = $encounterDataArr['newBalance'];
					}
				}			
				$insuranceDue = $encounterDataArr['insDue'];
				$insuranceDue = ($insuranceDue<0) ? 0 : $insuranceDue; 
				$patientDue = $encounterDataArr['patDue'];
				$patientDue = ($patientDue<0) ? 0 : $patientDue;
				$patientDue = ($totalBalance<=0) ? 0 : $patientDue;
				
				$encounter_detail_arr['PATIENTDUE'][] = $patientDue;
				$encounter_detail_arr['INSURANCEDUE'][] = $insuranceDue;
				$encounter_detail_arr['BALANCE'][] = $totalBalance;
				
				$submitted = $encounterDataArr["submitted"];
				$first_posted_date = $encounterDataArr["first_posted_date"];
				
				$encounter_detail_arr['COUNT'][$charge_list_detail_id] = $encounterDataArr['units'];
	
				//--- GET TOTAL CHARGES -------
				if(($DateRangeFor=='transaction_date' || $DateRangeFor=='date_of_payment') && ($submitted=='true' && $first_posted_date>=$Start_date && $first_posted_date<=$End_date)){
					$encounter_detail_arr['CHARGES'][$charge_list_detail_id] = $encounterDataArr['totalAmt'];
					$encounter_detail_arr['APPROVED_AMT'][$charge_list_detail_id] = $encounterDataArr['approvedAmt'];
				}
				if($DateRangeFor!='transaction_date' && $DateRangeFor!='date_of_payment'){
					$encounter_detail_arr['CHARGES'][$charge_list_detail_id] = $encounterDataArr['totalAmt'];
					$encounter_detail_arr['APPROVED_AMT'][$charge_list_detail_id] = $encounterDataArr['approvedAmt'];
				}
				
				//---- GET PAID AMOUNT FOR PATIENT ------
				if(count($mainEncounterPayArr['ALL'][$charge_list_detail_id])){
					// TOTAL PAYMENT
					$paidAmt = array_sum($mainEncounterPayArr['ALL'][$charge_list_detail_id]);
					//PATIENT PAYMENT
					$patPaidAmt  = array_sum($patPayDetArr['ALL']['patPaid'][$charge_list_detail_id]);
					//INSURANCE PAYMENT
					$insPaidAmt  = array_sum($patPayDetArr['ALL']['insPaid'][$charge_list_detail_id]);
				}

				$patCrdDbt = array_sum($pay_crd_deb_arr[$charge_list_detail_id]['Patient']);
				$patPaidAmt+= $patCrdDbt;
				$insCrdDbt = array_sum($pay_crd_deb_arr[$charge_list_detail_id]['Insurance']);
				$insPaidAmt+= $insCrdDbt;
				$paidAmt += $patCrdDbt + $insCrdDbt;
				
				
				// CREDIT AMOUNT and TOTAL BALANCE
				$creditAmount = $encounterDataArr['overPaymentForProc'];
				// ADJUSTMENT AMOUNT
				$adjustmentAmount = $arrAdjustmentAmt[$charge_list_detail_id] + $normalWriteOffAmt[$charge_list_detail_id];
	
				$encounter_detail_arr['CREDIT'][] = $creditAmount;
				$encounter_detail_arr['ADJUSTMENT'][] = $adjustmentAmount;
				$encounter_detail_arr['PATIENTPAID'][] = $patPaidAmt;
				$encounter_detail_arr['INSURANCEPAID'][] = $insPaidAmt;
				$encounter_detail_arr['PAYMENTS'][]  = $paidAmt;

				//INSURANCE COUNT
				if(sizeof($arrInsurance)>0){
					if($arrInsurance[$encounterDataArr['primaryInsuranceCoId']]){
						$encounter_detail_arr['PRI_INSURANCE'][$encounterId]=$encounterId;
						$procedure_sub_total_arr['PRI_INSURANCE'][$encounterId]=$encounterId;
						$arrGrandTot['PRI_INSURANCE'][$encounterId]=$encounterId;
					}
					if($arrInsurance[$encounterDataArr['secondaryInsuranceCoId']]){
						$encounter_detail_arr['SEC_INSURANCE'][$encounterId]=$encounterId;
						$procedure_sub_total_arr['SEC_INSURANCE'][$encounterId]=$encounterId;
						$arrGrandTot['SEC_INSURANCE'][$encounterId]=$encounterId;
					}
				}
			}
			
			//--- GET TOTAL CHARGES FOR SINGLE PROVIDER -----
			$units = array_sum($encounter_detail_arr['COUNT']);
			$totalAmount = array_sum($encounter_detail_arr['CHARGES']);
			$totalApprovedAmt = array_sum($encounter_detail_arr['APPROVED_AMT']);
			$paidForProc = array_sum($encounter_detail_arr['PAYMENTS']);
			$patientPaid = array_sum($encounter_detail_arr['PATIENTPAID']);
			$insurancePaid = array_sum($encounter_detail_arr['INSURANCEPAID']);
			$patientDue = array_sum($encounter_detail_arr['PATIENTDUE']);
			$insuranceDue = array_sum($encounter_detail_arr['INSURANCEDUE']);
			$credit = array_sum($encounter_detail_arr['CREDIT']);
			$adjustment = array_sum($encounter_detail_arr['ADJUSTMENT']);
			$balance = array_sum($encounter_detail_arr['BALANCE']);
			$priInsCount = count($encounter_detail_arr['PRI_INSURANCE']);
			$secInsCount = count($encounter_detail_arr['SEC_INSURANCE']);
	
			if($dispChart == 1)
			{	
				// CHART INDIVIDUAL PHYSICIAN TOTAL 
				$cNno = array_search($providerId, $chartProviderId);
				if(in_array($providerId, $chartProviderId) === false)
				{				
					$chartData[$n][1] = $provider_name;
					$chartData[$n][2] = $totalAmount;
					$chartData[$n][3] = $paidForProc;					
					$chartProviderId[$n] = $providerId;
					$n++;
				}else {
					$chartData[$cNno][1] = $provider_name;				
					$chartData[$cNno][2] += $totalAmount;
					$chartData[$cNno][3] += $paidForProc;
				}				
			}
			

			//--- GET SUB TOTAL FOR PROCEDURE -------
			$procedure_sub_total_arr['COUNT'][] = $units;
			$procedure_sub_total_arr['CHARGES'][] = $totalAmount;
			$procedure_sub_total_arr['APPROVED_AMT'][] = $totalApprovedAmt;
			$procedure_sub_total_arr['PAYMENTS'][] = $paidForProc;
			$procedure_sub_total_arr['PATIENTPAID'][] = $patientPaid;			
			$procedure_sub_total_arr['INSURANCEPAID'][] = $insurancePaid;			
			$procedure_sub_total_arr['CREDIT'][] = $credit;			
			$procedure_sub_total_arr['ADJUSTMENT'][] = $adjustment;			
			$procedure_sub_total_arr['PATIENTDUE'][] = $patientDue;			
			$procedure_sub_total_arr['INSURANCEDUE'][] = $insuranceDue;			
			$procedure_sub_total_arr['BALANCE'][] = $balance;		
				
			
			//---- NUMBER FORMAT FOR SINGLE PROVIDER AMOUUNT ---
			$totalAmount = $CLSReports->numberFormat($totalAmount,2);
			$totalApprovedAmt = $CLSReports->numberFormat($totalApprovedAmt,2);
			$paidForProc = $CLSReports->numberFormat($paidForProc,2);
			$patientPaid = $CLSReports->numberFormat($patientPaid,2);
			$insurancePaid = $CLSReports->numberFormat($insurancePaid,2);
			$credit = $CLSReports->numberFormat($credit,2);
			$adjustment = $CLSReports->numberFormat($adjustment,2);
			$patientDue = $CLSReports->numberFormat($patientDue,2);
			$insuranceDue = $CLSReports->numberFormat($insuranceDue,2);
			$balance = $CLSReports->numberFormat($balance,2);
			$priInsCount = $CLSReports->numberFormat($priInsCount,2);
			$secInsCount = $CLSReports->numberFormat($secInsCount,2);
	
			// RVU VALUES
			$rvuTd=$rvuCSV='';
			if($rvu=='1'){
				$work_rvu = $allRVUValues[$procedureId]['work_rvu'];
				$pe_rvu = $allRVUValues[$procedureId]['pe_rvu'];
				$mp_rvu = $allRVUValues[$procedureId]['mp_rvu'];
	
				$facPricingAmt = round((((($work_rvu * $bugdet_neu_adj_gpci) * $work_gpci) + ($pe_rvu * $practice_expense_gpci) + ($mp_rvu * $malpractice_gpci))  * $convFactor)  * $units, 2);
				
				if($facPricingAmt<=0){ $facPricingAmt='';}
				$rvuTd='<td class="text_10" style="text-align:right; width:'.$w_cols.'" valign="top">'.numberFormat($facPricingAmt,2).'</td>';
				$rvuCSV = numberFormat($facPricingAmt,2);
			}
			$insCountTd='';
			if(sizeof($arrInsurance)>0){
				$insCountTd='<td class="text_10" style="text-align:center; width:'.$w_cols.'" valign="top">\''.$priInsCount.'/'.$secInsCount.'\'</td>';
			}
			if(strlen($procedureName)>14){
				$procedureName=substr($procedureName,0,13).'...';
			}			
			
			$page_content .= <<<DATA
			<tr bgcolor="#FFFFFF">
				<td align="left" class="text_10" style="word-wrap:break-word; width:$w_first_col">$procedureName</td>
				<td align="left" class="text_10" style="word-wrap:break-word; width:$w_cols">$procedureDesc</td>
				<td style="text-align:center; word-wrap:break-word; width:$w_cols" class="text_10">$units</td>
				$insCountTd
				<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$totalAmount</td>
				<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$totalApprovedAmt</td>
				$rvuTd
				<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$patientPaid</td>
				<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$insurancePaid</td>
				<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$paidForProc</td>
				<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$credit</td>
				<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$adjustment</td>
				<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$patientDue</td>
				<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$insuranceDue</td>
				<td style="text-align:right; word-wrap:break-word; width:$w_cols" class="text_10">$balance</td>
			</tr>
DATA;

		$arr[$facilityName][$physicianName][$procedureName][$c]['facility']=$facilityName;	
		$arr[$facilityName][$physicianName][$procedureName][$c]['physician']=$physicianName;	
		$arr[$facilityName][$physicianName][$procedureName][$c]['procedure']=$procedureName;	
		$arr[$facilityName][$physicianName][$procedureName][$c]['procedure_desc']=$procedureDesc;	
		$arr[$facilityName][$physicianName][$procedureName][$c]['units']=$units;
		if(sizeof($arrInsurance)>0){
			$arr[$facilityName][$physicianName][$procedureName][$c]['counts']= "'$priInsCount / $secInsCount'";	
		}
		$arr[$facilityName][$physicianName][$procedureName][$c]['charges']=$totalAmount;
		$arr[$facilityName][$physicianName][$procedureName][$c]['approved_amt']=$totalApprovedAmt;
		if($rvu=='1'){	
			$arr[$facilityName][$physicianName][$procedureName][$c]['rvu']=$rvuCSV;
		}
		$arr[$facilityName][$physicianName][$procedureName][$c]['pat_paid']=$patientPaid;
		$arr[$facilityName][$physicianName][$procedureName][$c]['ins_paid']=$insurancePaid;
		$arr[$facilityName][$physicianName][$procedureName][$c]['total_paid']=$paidForProc;
		$arr[$facilityName][$physicianName][$procedureName][$c]['credit']=$credit;
		$arr[$facilityName][$physicianName][$procedureName][$c]['adjustment']=$adjustment;
		$arr[$facilityName][$physicianName][$procedureName][$c]['pat_due']=$patientDue;
		$arr[$facilityName][$physicianName][$procedureName][$c]['ins_due']=$insuranceDue;
		$arr[$facilityName][$physicianName][$procedureName][$c]['balance']=$balance;

}

		//--- SUB TOTAL -------
		$subTotalUnits = array_sum($procedure_sub_total_arr['COUNT']);
		$subTotalAmount = array_sum($procedure_sub_total_arr['CHARGES']);
		$subTotalApprovedAmt = array_sum($procedure_sub_total_arr['APPROVED_AMT']);
		$subPatientPaid = array_sum($procedure_sub_total_arr['PATIENTPAID']);
		$subInsurancePaid = array_sum($procedure_sub_total_arr['INSURANCEPAID']);
		$subPaidForProc = array_sum($procedure_sub_total_arr['PAYMENTS']);
		$subCredit = array_sum($procedure_sub_total_arr['CREDIT']);
		$subAdjustment = array_sum($procedure_sub_total_arr['ADJUSTMENT']);
		$subPatientDue = array_sum($procedure_sub_total_arr['PATIENTDUE']);
		$subInsuranceDue = array_sum($procedure_sub_total_arr['INSURANCEDUE']);
		$subBalance = array_sum($procedure_sub_total_arr['BALANCE']);
		$subPriCnt = count($procedure_sub_total_arr['PRI_INSURANCE']);
		$subSecCnt = count($procedure_sub_total_arr['SEC_INSURANCE']);
		
		//FACILITY TOTAL ARRAY
		$arrFacTot['COUNT'][]= $subTotalUnits;
		$arrFacTot['CHARGES'][]= $subTotalAmount;
		$arrFacTot['APPROVED_AMT'][]= $subTotalApprovedAmt;
		$arrFacTot['PATIENTPAID'][]= $subPatientPaid;
		$arrFacTot['INSURANCEPAID'][]= $subInsurancePaid;
		$arrFacTot['PAYMENTS'][]= $subPaidForProc;
		$arrFacTot['CREDIT'][]= $subCredit;
		$arrFacTot['ADJUSTMENT'][]= $subAdjustment;
		$arrFacTot['PATIENTDUE'][]= $subPatientDue;
		$arrFacTot['INSURANCEDUE'][]= $subInsuranceDue;
		$arrFacTot['BALANCE'][]= $subBalance;
		$arrFacTot['PRI_INSURANCE'][]= $subPriCnt;
		$arrFacTot['SEC_INSURANCE'][]= $subSecCnt;
		
		//GRAND TOTAL ARRAY
		$arrGrandTot['COUNT'][] = $subTotalUnits;
		$arrGrandTot['CHARGES'][] = $subTotalAmount;
		$arrGrandTot['APPROVED_AMT'][] = $subTotalApprovedAmt;
		$arrGrandTot['PAYMENTS'][] = $subPaidForProc;
		$arrGrandTot['PATIENTPAID'][] = $subPatientPaid;			
		$arrGrandTot['INSURANCEPAID'][] = $subInsurancePaid;			
		$arrGrandTot['CREDIT'][] = $subCredit;			
		$arrGrandTot['ADJUSTMENT'][] = $subAdjustment;			
		$arrGrandTot['PATIENTDUE'][] = $subPatientDue;			
		$arrGrandTot['INSURANCEDUE'][] = $subInsuranceDue;			
		$arrGrandTot['BALANCE'][] = $subBalance;
	
		//---- NUMBER FORMAT FOR SINGLE PROCEDURE SUB TOTAL AMOUUNT ---
		$subTotalEnc  =count($arrCountEncSub);
		$subTotalAmount = $CLSReports->numberFormat($subTotalAmount,2);
		$subTotalApprovedAmt = $CLSReports->numberFormat($subTotalApprovedAmt,2);
		$subPatientPaid = $CLSReports->numberFormat($subPatientPaid,2);
		$subInsurancePaid = $CLSReports->numberFormat($subInsurancePaid,2);
		$subPaidForProc = $CLSReports->numberFormat($subPaidForProc,2);
		$subCredit = $CLSReports->numberFormat($subCredit,2);
		$subAdjustment = $CLSReports->numberFormat($subAdjustment,2);
		$subPatientDue = $CLSReports->numberFormat($subPatientDue,2);
		$subInsuranceDue = $CLSReports->numberFormat($subInsuranceDue,2);
		$subBalance = $CLSReports->numberFormat($subBalance,2);

		$subCountTd='';
		if(sizeof($arrInsurance)>0){
			$subCountTd='<td class="text_10b" style="text-align:center; width:'.$w_cols.'" valign="top">\''.$subPriCnt.'/'.$subSecCnt.'\'</td>';
		}
	
		$page_content .= '
			<tr><td class="total-row" colspan="'.$colspan.'" ></td></tr>
			<tr bgcolor="#FFFFFF">
				'.$extraTD.'
				<td class="text_10b" style="text-align:right;" nowrap>Sub Total :</td>
				<td class="text_10b" style="text-align:center;">\''.$subTotalUnits.'/'.$subTotalEnc.'\'</td>
				'.$subCountTd.'
				<td class="text_10b" style="text-align:right;">'.$subTotalAmount.'</td>
				<td class="text_10b" style="text-align:right;">'.$subTotalApprovedAmt.'</td>
				'.$blankTDRVU.'
				<td class="text_10b" style="text-align:right;">'.$subPatientPaid.'</td>
				<td class="text_10b" style="text-align:right;">'.$subInsurancePaid.'</td>
				<td class="text_10b" style="text-align:right;">'.$subPaidForProc.'</td>
				<td class="text_10b" style="text-align:right;">'.$subCredit.'</td>
				<td class="text_10b" style="text-align:right;">'.$subAdjustment.'</td>
				<td class="text_10b" style="text-align:right;">'.$subPatientDue.'</td>
				<td class="text_10b" style="text-align:right;">'.$subInsuranceDue.'</td>
				<td class="text_10b" style="text-align:right;">'.$subBalance.'</td>
			</tr>
			<tr><td class="total-row" colspan="'.$colspan.'" ></td></tr>';
	} // END PHYSICIAN

	$procedure_sub_total_arr['COUNT'][] = $units;

	//FACILITY TOTAL
	$subTotalEnc  =count($arrFacEnc);
	$subTotalUnits = array_sum($arrFacTot['COUNT']);
	$subTotalAmount = $CLSReports->numberFormat(array_sum($arrFacTot['CHARGES']),2);
	$subTotalApprovedAmt = $CLSReports->numberFormat(array_sum($arrFacTot['APPROVED_AMT']),2);
	$subPatientPaid = $CLSReports->numberFormat(array_sum($arrFacTot['PATIENTPAID']),2);
	$subInsurancePaid = $CLSReports->numberFormat(array_sum($arrFacTot['INSURANCEPAID']),2);
	$subPaidForProc = $CLSReports->numberFormat(array_sum($arrFacTot['PAYMENTS']),2);
	$subCredit = $CLSReports->numberFormat(array_sum($arrFacTot['CREDIT']),2);
	$subAdjustment = $CLSReports->numberFormat(array_sum($arrFacTot['ADJUSTMENT']),2);
	$subPatientDue = $CLSReports->numberFormat(array_sum($arrFacTot['PATIENTDUE']),2);
	$subInsuranceDue = $CLSReports->numberFormat(array_sum($arrFacTot['INSURANCEDUE']),2);
	$subBalance = $CLSReports->numberFormat(array_sum($arrFacTot['BALANCE']),2);
	$subPriCnt = count($arrFacTot['PRI_INSURANCE']);
	$subSecCnt = count($arrFacTot['SEC_INSURANCE']);

	$subCountTd='';
	if(sizeof($arrInsurance)>0){
		$subCountTd='<td class="text_10b" style="text-align:center; width:'.$w_cols.'" valign="top">\''.$subPriCnt.'/'.$subSecCnt.'\'</td>';
	}

	$page_content .='
		<tr><td class="total-row" colspan="'.$colspan.'" ></td></tr>
		<tr bgcolor="#FFFFFF">
			'.$extraTD.'
			<td class="text_10b" style="text-align:right;" nowrap>Sub Total :</td>
			<td class="text_10b" style="text-align:center;">\''.$subTotalUnits.'/'.$subTotalEnc.'\'</td>
			'.$subCountTd.'
			<td class="text_10b" style="text-align:right;">'.$subTotalAmount.'</td>
			<td class="text_10b" style="text-align:right;">'.$subTotalApprovedAmt.'</td>
			'.$blankTDRVU.'
			<td class="text_10b" style="text-align:right;">'.$subPatientPaid.'</td>
			<td class="text_10b" style="text-align:right;">'.$subInsurancePaid.'</td>
			<td class="text_10b" style="text-align:right;">'.$subPaidForProc.'</td>
			<td class="text_10b" style="text-align:right;">'.$subCredit.'</td>
			<td class="text_10b" style="text-align:right;">'.$subAdjustment.'</td>
			<td class="text_10b" style="text-align:right;">'.$subPatientDue.'</td>
			<td class="text_10b" style="text-align:right;">'.$subInsuranceDue.'</td>
			<td class="text_10b" style="text-align:right;">'.$subBalance.'</td>
		</tr>
		<tr><td class="total-row" colspan="'.$colspan.'" ></td></tr>';
	}

	//SORTING OF CSV
	$tempArr=$arr;
	unset($arr);
	ksort($tempArr, SORT_NATURAL);

	foreach($tempArr as $facility =>$arrFacGroup){
		ksort($arrFacGroup, SORT_NATURAL);
		foreach($arrFacGroup as $physician =>$arrPhyGroup){
			ksort($arrPhyGroup, SORT_NATURAL);
			foreach($arrPhyGroup as $cpt =>$arrCPTGroup){
				foreach($arrCPTGroup as $arrDetails){

					$arr=array();
					$arr[]=$arrDetails['facility'];
					$arr[]=$arrDetails['physician'];
					$arr[]=$arrDetails['procedure'];
					$arr[]=$arrDetails['procedure_desc'];
					$arr[]=$arrDetails['units'];
					if(sizeof($arrInsurance)>0){
						$arr[]=$arrDetails['counts'];
					}
					$arr[]=$arrDetails['charges'];
					$arr[]=$arrDetails['apporved_amt'];
					if($rvu=='1'){	
						$arr[]=$arrDetails['rvu']; 
					}
					$arr[]=$arrDetails['pat_paid'];
					$arr[]=$arrDetails['ins_paid'];
					$arr[]=$arrDetails['total_paid'];
					$arr[]=$arrDetails['credit'];
					$arr[]=$arrDetails['adjustment'];
					$arr[]=$arrDetails['pat_due'];
					$arr[]=$arrDetails['ins_due'];
					$arr[]=$arrDetails['balance'];

					fputcsv($fp,$arr, ",","\"");			
				}
			}
		}
	}
	unset($tempArr);
	unset($arr);

} //END PROCEDURE 


	//TOTAL
	$totalEnc  =count($arrCountEnc);
	$arrCountEnc=array();

	$totCountTd='';
	if(sizeof($arrInsurance)>0){
		$totCountTd='<td class="text_10b" style="text-align:center; width:'.$w_cols.'" valign="top">\''.count($arrGrandTot['PRI_INSURANCE']).'/'.count($arrGrandTot['SEC_INSURANCE']).'\'</td>';
	}

	//TOTAL
	if(empty($page_content)==false){
		$page_content.='
		<tr><td class="total-row" colspan="'.$colspan.'" ></td></tr>
		<tr bgcolor="#FFFFFF">
			'.$extraTD.'
			<td class="text_10b" style="text-align:right;">Grand Total :</td>
			<td class="text_10b" style="text-align:center;">\''.array_sum($arrGrandTot['COUNT']).'/'.$totalEnc.'\'</td>
			'.$totCountTd.'
			<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat(array_sum($arrGrandTot['CHARGES']),2).'</td>
			<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat(array_sum($arrGrandTot['APPROVED_AMT']),2).'</td>
			'.$blankTDRVU.'
			<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat(array_sum($arrGrandTot['PATIENTPAID']),2).'</td>
			<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat(array_sum($arrGrandTot['INSURANCEPAID']),2).'</td>
			<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat(array_sum($arrGrandTot['PAYMENTS']),2).'</td>
			<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat(array_sum($arrGrandTot['CREDIT']),2).'</td>
			<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat(array_sum($arrGrandTot['ADJUSTMENT']),2).'</td>
			<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat(array_sum($arrGrandTot['PATIENTDUE']),2).'</td>
			<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat(array_sum($arrGrandTot['INSURANCEDUE']),2).'</td>
			<td class="text_10b" style="text-align:right;">'.$CLSReports->numberFormat(array_sum($arrGrandTot['BALANCE']),2).'</td>
			</tr>
		<tr><td class="total-row" colspan="'.$colspan.'" ></td></tr>';	
		
		$ins = array_sum($arrGrandTot['COUNT']);
		$arr=array();
		if($groupby == "Facility"){
			$arr[]="";
			$arr[]="";
			$arr[]="";
		} else{
			$arr[]="";
			$arr[]="";
		}
		$arr[]="Grand Total :";
		$arr[]= "'$ins / $totalEnc'";	
		if(sizeof($arrInsurance)>0){
			$priIns = count($arrGrandTot['PRI_INSURANCE']);
			$secIns = count($arrGrandTot['SEC_INSURANCE']);
			$arr[]= "'$priIns / $totalEnc'";	
		}
		$arr[]= numberFormat(array_sum($arrGrandTot['CHARGES']),2);
		$arr[]= numberFormat(array_sum($arrGrandTot['APPROVED_AMT']),2);
		if($rvu=='1'){	
			$arr[]="";
		}
		$arr[]= numberFormat(array_sum($arrGrandTot['PATIENTPAID']),2);
		$arr[]= numberFormat(array_sum($arrGrandTot['INSURANCEPAID']),2);
		$arr[]= numberFormat(array_sum($arrGrandTot['PAYMENTS']),2);
		$arr[]= numberFormat(array_sum($arrGrandTot['CREDIT']),2);
		$arr[]= numberFormat(array_sum($arrGrandTot['ADJUSTMENT']),2);
		$arr[]= numberFormat(array_sum($arrGrandTot['PATIENTDUE']),2);
		$arr[]= numberFormat(array_sum($arrGrandTot['INSURANCEDUE']),2);
		$arr[]= numberFormat(array_sum($arrGrandTot['BALANCE']),2);
		fputcsv($fp,$arr, ",","\"");
		$fp = fopen ($csv_file_name, 'a+');
		
		
	}

	//-----------DELETED CHARGES-------------
	$del_csv = $del_csv_html = $del_pdf_html ='';
	if(count($arrVoidPay)>0){
		
		$total_cols = 10;
		$phy_col =  "17";
		$w_cols_del = $w_cols1 = floor((100 - ($phy_col))/($total_cols-1));
		$phy_col = $phy_col1 = 100 - ( (($total_cols-1) * $w_cols_del));
		$w_cols_del = $w_cols_del."%";
		$phy_col = $phy_col."%";
		
		$fristGrpArr = array_keys($arrVoidPay);
		$arrDelTotal = array();
		foreach($fristGrpArr as $firstID){
			$arrFirstTotal = $arrPhyTotal = array();
			if($groupby=='Physician' || $groupby=='Procedure' || $groupby=='CPTCategory' || $groupby=='ins_group'){
				$firstTitle='Physician';
				$firstGrpName = $providerNameArr[$firstID];
			}else{
				$firstTitle='Facility';
				$firstGrpName = $arrAllFacilities[$firstID];
			}
			//$phyName = $providerNameArr[$phyID];
			$del_csv.=
				'<tr>
					<td class="text_b_w" align="left" colspan="'.$total_cols.'">'.$firstTitle.' : '.$firstGrpName.'</td>
				</tr>';
	
			foreach($arrVoidPay[$firstID] as $secID => $grpCharges){
				if($groupby=='Physician' || $groupby=='Procedure' || $groupby=='CPTCategory' || $groupby=='ins_group'){
					$secTitle='Facility';
					$secGrpName = $arrAllFacilities[$secID];
				}else{
					$secTitle='Physician';
					$secGrpName = $providerNameArr[$secID];
				}
				//$facName = $arrAllFacilities[$facID];
				//$delTotPhyChg += $grpCharges;
				$del_csv.='<tr>
					<td class="text_12" bgcolor="#FFFFFF" align="left">'.$secGrpName.'</td>
					<td class="text_12" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['charges'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['cash'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['pt_check'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['ins_check'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['CC'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['EFT'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['MO'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['VEEP'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" align="right">'.$CLSReports->numberFormat($grpCharges['adjustments'],2).'</td>
					
				</tr>';
				$arrFirstTotal['charges'] += $grpCharges['charges'];
				$arrFirstTotal['cash'] += $grpCharges['cash'];
				$arrFirstTotal['pt_check'] += $grpCharges['pt_check'];
				$arrFirstTotal['ins_check'] += $grpCharges['ins_check'];
				$arrFirstTotal['CC'] += $grpCharges['CC'];
				$arrFirstTotal['EFT'] += $grpCharges['EFT'];
				$arrFirstTotal['MO'] += $grpCharges['MO'];
				$arrFirstTotal['VEEP'] += $grpCharges['VEEP'];
				$arrFirstTotal['adjustments'] += $grpCharges['adjustments'];
			}
			$del_csv.= '
				<tr><td class="total-row" colspan="'.$total_cols.'"></td></tr>
				<tr class="text_12b">
					<td bgcolor="#FFFFFF"  align="right">'.$firstTitle.' Total: </td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['charges'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['cash'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['pt_check'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['ins_check'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['CC'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['EFT'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['MO'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['VEEP'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['adjustments'],2).'</td>
				</tr>
				<tr><td class="total-row" colspan="'.$total_cols.'"></td></tr>
				';
				$arrDelTotal['charges'] += $arrFirstTotal['charges'];
				$arrDelTotal['cash'] += $arrFirstTotal['cash'];
				$arrDelTotal['pt_check'] += $arrFirstTotal['pt_check'];
				$arrDelTotal['ins_check'] += $arrFirstTotal['ins_check'];
				$arrDelTotal['CC'] += $arrFirstTotal['CC'];
				$arrDelTotal['EFT'] += $arrFirstTotal['EFT'];
				$arrDelTotal['MO'] += $arrFirstTotal['MO'];
				$arrDelTotal['VEEP'] += $arrFirstTotal['VEEP'];
				$arrDelTotal['adjustments'] += $arrFirstTotal['adjustments'];
		}
	
		$totDelPayment=$arrDelTotal['cash'] + $arrDelTotal['pt_check']+$arrDelTotal['ins_check']+$arrDelTotal['CC']+$arrDelTotal['EFT']+$arrDelTotal['MO']+$arrDelTotal['VEEP'];
		$totalDeletedCharges=$arrDelTotal['charges'];
		$totalDeletedPayments=$totDelPayment;
		$totalDeletedAdjustments=$arrDelTotal['adjustments'];
		
	$del_csv.= '<tr><td class="total-row" colspan="'.$total_cols.'"></td></tr>
				<tr class="text_12b">
				<td bgcolor="#FFFFFF"  align="right">Grand Total: </td>
				<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['charges'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['cash'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['pt_check'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['ins_check'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['CC'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['EFT'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['MO'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['VEEP'],2).'</td>
					<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrDelTotal['adjustments'],2).'</td>
				</tr>
				<tr><td class="total-row" colspan="'.$total_cols.'"></td></tr>
				';
	$del_csv .= '
				<tr><td colspan="'.$total_cols.'" class="total-row"></td></tr>
				<tr class="text_12b">
					<td bgcolor="#FFFFFF"  style="text-align:right" >Total Voided Payment: </td>
					<td bgcolor="#FFFFFF"  style="text-align:right" > </td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totDelPayment,2).'</td>
					<td bgcolor="#FFFFFF"  style="text-align:right" colspan="7"></td>
				</tr>
				<tr><td  colspan="'.$total_cols.'" class="total-row"></td></tr>
				';			
	
		$del_csv_html .='
		<table style="width:100%" class="rpt_table rpt_table-bordered">
			<tr id="heading_orange"><td colspan="'.$total_cols.'">Voided Charges and Payments</td></tr>
			<tr>
				<td class="text_b_w" align="center" style="width:'.$phy_col.'">Facility</td>
				<td class="text_b_w" align="center" style="width:'.$w_cols_del.'">Charges</td>
				<td class="text_b_w" align="center" style="text-align:center;width:'.($w_cols1*7).'%" colspan="7">Payments</td>
				<td class="text_b_w" align="center" style="width:'.$w_cols_del.'">Write-off & Adj</td>
			</tr>
			<tr>
				<td class="text_b_w" align="center" style="width:'.$phy_col.'"></td>
				<td class="text_b_w" align="center" style="width:'.$w_cols_del.'"></td>
				<td class="text_b_w" align="center" style="width:'.$w_cols_del.'">Cash</td>
				<td class="text_b_w" align="center" style="width:'.$w_cols_del.'">Pt Check</td>
				<td class="text_b_w" align="center" style="width:'.$w_cols_del.'">Ins Check</td>
				<td class="text_b_w" align="center" style="width:'.$w_cols_del.'">CC</td>
				<td class="text_b_w" align="center" style="width:'.$w_cols_del.'">EFT</td>
				<td class="text_b_w" align="center" style="width:'.$w_cols_del.'">MO</td>
				<td class="text_b_w" align="center" style="width:'.$w_cols_del.'">VEEP</td>
				<td class="text_b_w" align="center" style="width:'.$w_cols_del.'"></td>
			</tr>
			'.
			$del_csv.'
		</table>';
	
	}
	
	//GRAND TOTAL IF NORMAL AND DELETED AVAILABLE
	if(sizeof($mainResultArr)>0 && empty($del_csv_html) == false){
		$totalChargesAmt=array_sum($arrGrandTot['CHARGES']);
		$totalPaymentsAmt=array_sum($arrGrandTot['PAYMENTS']);
		$totalAdjustmentAmt=array_sum($arrGrandTot['ADJUSTMENT']);
		$grandCharges=$totalChargesAmt-$totalDeletedCharges;
		$grandPayments=$totalPaymentsAmt-$totalDeletedPayments;
		$grandAdjustments=$totalAdjustmentAmt-$totalDeletedAdjustments;
		
		$grand_totals_csv= '
		<table style="width:100%" class="rpt_table rpt_table-bordered">
		<tr id="heading_orange"><td colspan="5">Grand Totals</td></tr>
		<tr>
			<td class="text_b_w" style="width:15%"></td>
			<td class="text_b_w" style="width:15%; text-align:center">Charges</td>
			<td class="text_b_w" style="width:15%; text-align:center">Payments</td>
			<td class="text_b_w" style="width:15%; text-align:center">Write-off & Adj</td>
			<td class="text_b_w" style="width:auto"></td>
		</tr>
		<tr class="text_12b">
			<td bgcolor="#FFFFFF" style="text-align:right">Total : </td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalChargesAmt,2).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalPaymentsAmt,2).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalAdjustmentAmt,2).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right"></td>
		</tr>
		<tr class="text_12b">
			<td bgcolor="#FFFFFF" style="text-align:right">Total Deleted : </td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalDeletedCharges,2).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalDeletedPayments,2).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalDeletedAdjustments,2).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right"></td>
		</tr>
		<tr><td colspan="5" class="total-row"></td></tr>
		<tr class="text_12b">
			<td bgcolor="#FFFFFF"  style="text-align:right">Grand Total : </td>
			<td bgcolor="#FFFFFF"  style="text-align:right">'.$CLSReports->numberFormat($grandCharges,2,1).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandPayments,2,1).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandAdjustments,2,1).'</td>
			<td bgcolor="#FFFFFF"  style="text-align:right"></td>
		</tr>
		<tr><td colspan="5" class="total-row"></td></tr>
		</table>';	
	
		$grand_totals_pdf='
		<table style="width:100%" class="rpt_table rpt_table-bordered">
		<tr id="heading_orange"><td colspan="4">Grand Totals</td></tr>
		<tr>
			<td class="text_b_w" style="width:15%"></td>
			<td class="text_b_w" style="width:15%; text-align:center">Charges</td>
			<td class="text_b_w" style="width:15%; text-align:center">Payments</td>
			<td class="text_b_w" style="width:15%; text-align:center">Write-off & Adj</td>
		</tr>
		<tr class="text_12b">
			<td bgcolor="#FFFFFF" style="text-align:right">Total : </td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalChargesAmt,2).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalPaymentsAmt,2).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalAdjustmentAmt,2).'</td>
		</tr>
		<tr class="text_12b">
			<td bgcolor="#FFFFFF" style="text-align:right">Total Deleted : </td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalDeletedCharges,2).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalDeletedPayments,2).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($totalDeletedAdjustments,2).'</td>
		</tr>
		<tr><td colspan="4" class="total-row"></td></tr>
		<tr class="text_12b">
			<td bgcolor="#FFFFFF"  style="text-align:right">Grand Total : </td>
			<td bgcolor="#FFFFFF"  style="text-align:right">'.$CLSReports->numberFormat($grandCharges,2,1).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandPayments,2,1).'</td>
			<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($grandAdjustments,2,1).'</td>
		</tr>
		<tr><td colspan="4" class="total-row"></td></tr>
		</table>';	
	}

	if(empty($page_content) == false || empty($del_csv_html)==false){
		$printFile = true;
		$facility_name = $facilityNameArr[$facilityId];
		//-- OPERATOR INITIAL -------
		$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$opInitial = $authProviderNameArr[1][0];
		$opInitial .= $authProviderNameArr[0][0];
		$opInitial = strtoupper($opInitial);
		$page_head_data = '';
		
		$titleInsTd='';
		if(sizeof($arrInsurance)>0){
			$titleInsTd='<td class="text_b_w" style="text-align:center; width:$w_cols" >Pri# / Sec#</td>';
		}
		
		//if($f == 0){
		//--- PAGE HEADER DATA ---
		$page_head_data =<<<DATA
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
				<tr class="rpt_headers">
					<td class="rptbx1" width="33%">CPT Analysis Report ($process)</td>
					<td class="rptbx2" width="34%">$dayReport ( $search ) From $fromDate&nbsp;To $toDate</td>
					<td class="rptbx3" width="33%">Created by $opInitial on $curDate</td>
				</tr>
				<tr class="rpt_headers">
					<td class="rptbx1">Selected Group: $group_name</td>
					<td class="rptbx2">Selected Facility : $practice_name</td>
					<td class="rptbx3">Selected Physician : $physician_name &nbsp;&nbsp; Crediting Phy.: $crediting_physician_name</td>
				</tr>
				<tr class="rpt_headers">
					<td class="rptbx1">Selected CPT Cat. : $cpt_cat_name</td>
					<td class="rptbx2">Selected Procedure : $procedure_name</td>
					<td class="rptbx3">Selected Insurance : $insurance_name</td>
				</tr>				
			</table>
DATA;

		$page_head_data_pdf =<<<DATA
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
				<tr class="rpt_headers">
					<td class="rptbx1" width="348">CPT Analysis Report ($process)</td>
					<td class="rptbx2" width="348">$dayReport ( $search ) From $fromDate&nbsp;To $toDate</td>
					<td class="rptbx3" width="348">Created by $opInitial on $curDate</td>
				</tr>
				<tr class="rpt_headers">
					<td class="rptbx1">Selected Group: $group_name</td>
					<td class="rptbx2">Selected Facility : $practice_name</td>
					<td class="rptbx3">Selected Physician : $physician_name &nbsp;&nbsp; Crediting Phy.: $crediting_physician_name</td>
				</tr>
				<tr class="rpt_headers">
					<td class="rptbx1">Selected CPT Cat. : $cpt_cat_name</td>
					<td class="rptbx2">Selected Procedure : $procedure_name</td>
					<td class="rptbx3">Selected Insurance : $insurance_name</td>
				</tr>				
			</table>
DATA;

		//}

		//--- PDF FILE DATA ---
		$data .= <<<DATA
			<page backtop="$pageHt" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>		
				$page_head_data_pdf
				<table  class="table" >					
					<tr id="heading_orange">
DATA;
				if($groupby == "Facility" || $groupby == "Physician" || $groupby == "CPTCategory" || $groupby=='ins_group'){
				$data .= <<<DATA
					<td class="text_b_w" style="text-align:center; width:$w_first_col">CPT Code</td>
					<td class="text_b_w" style="text-align:right; width:$w_cols">CPT Desc</td>
DATA;				
				}else{
				$data .= <<<DATA
					<td class="text_b_w" style="text-align:center; width:$w_first_col">Physician Name</td>
DATA;
				}
		
		$data .= <<<DATA
					<td class="text_b_w" style="text-align:right; width:$w_cols">Proc. / Encs.&nbsp;</td>
					$titleInsTd
					<td class="text_b_w" style="text-align:right; width:$w_cols">Charges&nbsp;</td>
					<td class="text_b_w" style="text-align:right; width:$w_cols">Allowed Amt&nbsp;</td>
					$titleTDRVU_PDF
					<td class="text_b_w" style="text-align:right; width:$w_cols">Pat. Paid&nbsp;</td>
					<td class="text_b_w" style="text-align:right; width:$w_cols">Ins. Paid&nbsp;</td>
					<td class="text_b_w" style="text-align:right; width:$w_cols">Payments&nbsp;</td>
					<td class="text_b_w" style="text-align:right; width:$w_cols">Credit&nbsp;</td>
					<td class="text_b_w" style="text-align:right; width:$w_cols">Adj.&nbsp;</td>
					<td class="text_b_w" style="text-align:right; width:$w_cols">Pat. Due&nbsp;</td>
					<td class="text_b_w" style="text-align:right; width:$w_cols">Ins. Due&nbsp;</td>
					<td class="text_b_w" style="text-align:right; width:$w_cols">Balance&nbsp;</td>
					</tr>
				</table>
			</page_header>		
			<table class="table">		
				$page_content
			</table>
				$del_csv_html
				$grand_totals_pdf
			</page>
DATA;

		//--- PDF FILE DATA ---
		$csvFileData .= <<<DATA
			$page_head_data
			<table class="rpt_table rpt_table-bordered">
				<tr id="heading_orange">
DATA;
				if($groupby == "Facility" || $groupby == "Physician" | $groupby == "CPTCategory" || $groupby=='ins_group'){
				$csvFileData .= <<<DATA
					<td style="text-align:center;" width="200" class="text_b_w">CPT Code</td>
					<td style="text-align:center;" width="200" class="text_b_w">CPT Desc</td>
DATA;
				}
				else{
					$csvFileData .= <<<DATA
					<td style="text-align:center;" width="200" class="text_b_w">Physician Name</td>
DATA;
				}
				$csvFileData .= <<<DATA
					<td style="text-align:right;" width="100" class="text_b_w">Proc. / Encs.&nbsp;</td>
					$titleInsTd					
					<td style="text-align:right;" width="100" class="text_b_w">Charges&nbsp;</td>
					<td style="text-align:right;" width="100" class="text_b_w">Allowed Amt&nbsp;</td>
					$titleTDRVU
					<td style="text-align:right;" width="100" class="text_b_w">Pat. Paid&nbsp;</td>
					<td style="text-align:right;" width="100" class="text_b_w">Ins. Paid&nbsp;</td>
					<td style="text-align:right;" width="100" class="text_b_w">Payments&nbsp;</td>
					<td style="text-align:right;" width="100" class="text_b_w">Credit&nbsp;</td>
					<td style="text-align:right;" width="100" class="text_b_w">Adj.&nbsp;</td>
					<td style="text-align:right;" width="100" class="text_b_w">Pat. Due&nbsp;</td>
					<td style="text-align:right;" width="100" class="text_b_w">Ins. Due&nbsp;</td>
					<td style="text-align:right;" width="100" class="text_b_w">Balance&nbsp;</td>
				</tr>
				$page_content
			</table>
			$del_csv_html
			$grand_totals_csv
DATA;
	}
?>