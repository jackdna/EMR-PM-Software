<?php
error_reporting(1);
$allTotalAmountArr = array();
//--- GET PROCEDURE ID ARRAY -------
$firstIDArr = array_keys($mainResultArr);
$j = $k = $m = 0; 
$data_csv_final='';
$chartData = array();
$chartFacilityId = array();
$chartProcedureId = array();
$chartProcedureName = array();
$chartProviderId = array();

if($groupby == "Procedure"){
	$colspan=17; 
	$total_cols = 17;
} else{
	$colspan=17; 
	$total_cols = 17;
}
$backtop = "21mm";
$blankTDRVU = $titleTDRVU = $titleTDRVU_PDF = '';

$w_first_col = 10;
$w_units = 3;
$w_phy = 10;
$w_date = 7;
if($rvu=='1'){
	$colspan+=1;
	$total_cols+=1;
	$w_first_col = 10;
	$backtop = "28mm";
	$blankTDRVU='<td class="text10_b" width="'.$widthRVU.'" style="text-align:right;"></td>';
	$titleTDRVU='<td class="text10_b" width="'.$widthRVU.'" style="text-align:right;">Non Fac. Pricing Amt.</td>';
	$titleTDRVU_PDF='<td class="text10_b" style="text-align:right; width:'.$pdf_w_cols.'">Non Fac. Pricing Amt.</td>';
}

$exclude_cols = $w_first_col + $w_units + $w_phy + ($w_date * 2);
$w_cols =  (100 - $exclude_cols)/($total_cols-5);

//GETTING WIDTHS FOR PDF - BECAUSE ONE COLUMN EXCLUDED IN PDF
$pdfColspan=$colspan-1;
$pdfTotal_cols = $total_cols-1;
$exclude_cols = $w_first_col + $w_units + $w_phy + ($w_date * 2);
$pdf_w_cols =  (100 - $exclude_cols)/($pdfTotal_cols-5);

$exclude_cols = ($pdf_w_cols * ($pdfTotal_cols-5)) + $w_units + $w_phy + ($w_date * 2) ;
$pdf_w_first_col = 100 - ($exclude_cols);
//----------------------

$exclude_cols = ($w_cols * ($total_cols-5)) + $w_units + $w_phy + ($w_date * 2) ;
$w_first_col = 100 - ($exclude_cols);

$w_units = $w_units."%";
$w_phy = $w_phy."%";
$w_date = $w_date."%";
$w_cols = $w_cols."%";
$w_first_col = $w_first_col."%";

//PDF COLS
$pdf_w_cols = $pdf_w_cols."%";
$pdf_w_first_col = $pdf_w_first_col."%";
//--------------------------


if($rvu=='1'){
$titleTDRVU_PDF='<td class="text_b_w" style="text-align:right; width:'.$w_cols.'">Non Fac. Pricing Amt.</td>';
}

//CSV DATA
//if($monthDiff>6){
	$data_header="Group :".$group_name.", CPT Analysis Report(".$process."), ".$dayReport." ( ".$search." ) From ".$fromDate." To ".$toDate.", Created by ".$opInitial." on ".$curDate."\n";
	$data_header.="Procedure :".$procedure_name.", Practice ".$practice_name.", Physician :".$physician_name.", Crediting Physician :".$crediting_physician_name."\n";
	
	if($groupby == "Facility" || $groupby == "Physician"){ $data_header="CPT Code, ";}else{ $data_header.="Phy., "; }
	$data_header.="Patient-ID, DOS, Payment Date, ";
	if($groupby == "Procedure" ){ $data_header.="Facility, "; }
	$data_header.="Insurance, Units, Total Charges, Allowed Amount, Pat. Paid, Ins. Paid, Total Payment, Credit, Adjustment, Pat. Due, Ins. Due, Balance\n";
//}

//MAKING OUTPUT DATA
$file_name="CPT_Analytic.csv";
$csv_file_name= write_html("", $file_name);
//CSV FILE NAME
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');

$arr=array();
$arr[]='CPT Analysis Report (Detail)';
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
elseif($groupby == "Facility"){$GourpCSV ='Facility';}
$arr=array();
$arr[]=$GourpCSV;
$arr[]=$CSVColulm;
if($CSVColulm1){
$arr[]=$CSVColulm1;
}
$arr[]='Patient-ID';
$arr[]='DOS';
$arr[]='Payment Date';
if($groupby == "Procedure"){
	$arr[]='Facility';
}
$arr[]='Insurance';	
$arr[]='Units';	
$arr[]='Charges';
$arr[]='Allowed Amt.';	
$arr[]='Pat. Paid';	
$arr[]='Ins. Paid';	
$arr[]='Total Payment';	
$arr[]='Credit';
$arr[]='Adjustment';	
$arr[]='Pat. Due';	
$arr[]='Ins. Due';
$arr[]='Balance';
fputcsv($fp,$arr, ",","\"");
$fp = fopen ($csv_file_name, 'a+');
$arr=array();
for($p=0;$p<count($firstIDArr);$p++){
	$data_csv='';
	$firstId = $firstIDArr[$p];
	//---- GET PROVIDER ID ARRAY UNDER SINGLE FACILITY/PROCEDURE --------
	$secondDataArr = $mainResultArr[$firstId];
	$secondIDArr = array();
	if(count($secondDataArr)>0){
		 $secondIDArr = array_keys($secondDataArr);
	}
	if($groupby == "Facility"){
		//--- GET FACILITY NAME ------
	$facilityId = $firstId;
	$facilityName = core_refine_user_input($facilityNameArr[$facilityId]);
	$page_content .= '<tr><td colspan="'.$colspan.'" class="text_b_w">Facility: '.$facilityName.'</td></tr>';
	$page_contentPDF .= '<tr><td colspan="'.$colspan.'" class="text_b_w">Facility: '.$facilityName.'</td></tr>';
	//if($monthDiff>6){
	//$data_csv.="Facility: ".$facilityName.",,,,,,,,,,,,,,,,,\n";
	//}
	}
	else if($groupby == "Physician"){
		//--- GET PHYSICIAN NAME ------
	$providerId = $firstId;
	$physicianName = core_refine_user_input($providerNameArr[$providerId]);
	$page_content .= '<tr><td colspan="'.$colspan.'" class="text_b_w">Physician: '.$physicianName.'</td></tr>';
	$page_contentPDF .= '<tr><td colspan="'.$colspan.'" class="text_b_w">Physician: '.$physicianName.'</td></tr>';
	//if($monthDiff>6){
	//$data_csv.="Physician: ".$physicianName.",,,,,,,,,,,,,,,,,\n";
	//}
	}
	else if($groupby == "CPTCategory"){
		//--- GET CPTCategory NAME ------
	$CPTId = $firstId;
	$CPTName = $category_data_arr[$CPTId];
	$page_content .= '<tr><td colspan="'.$colspan.'" class="text_b_w">CPT Category: '.$CPTName.'</td></tr>';
	$page_contentPDF .= '<tr><td colspan="'.$colspan.'" class="text_b_w">CPT Category: '.$CPTName.'</td></tr>';
	//$data_csv.="CPT Category: ".$CPTName.",,,,,,,,,,,,,,,,,\n";
	}
	else if($groupby == "ins_group"){
		//--- GET CPTCategory NAME ------
	$insGrpid = $firstId;
	$insGrpName = $arrAllInsGroups[$insGrpid];
	$page_content .= '<tr><td colspan="'.$colspan.'" class="text_b_w">Ins. Group: '.$insGrpName.'</td></tr>';
	$page_contentPDF .= '<tr><td colspan="'.$colspan.'" class="text_b_w">Ins. Group: '.$insGrpName.'</td></tr>';
	//$data_csv.="Ins. Group: ".$insGrpName.",,,,,,,,,,,,,,,,,\n";
	}	
	else{
	//--- GET PROCEDURE NAME ------
	
	$procedureId = $firstId;
	$procedureName = core_refine_user_input($procedureNameArr[$procedureId]);
	$page_content .='<tr><td colspan="'.$colspan.'" class="text_b_w">Procedure: '.$procedureName.'</td></tr>';
	$page_contentPDF .='<tr><td colspan="'.$colspan.'" class="text_b_w">Procedure: '.$procedureName.'</td></tr>';
	//if($monthDiff>6){
	//$data_csv.="Procedure: ".$procedureName.",,,,,,,,,,,,,,,,,\n";
	//}
	}
	//--- START PROVIDER LOOP -------
	//echo "<br> -----secondIDArr-----------<br>";print_r($secondIDArr);
	$subTotalAmountArr = array();$stepUpId = "";
	for($pr=0;$pr<count($secondIDArr);$pr++){		
		$secondId = $secondIDArr[$pr];
		$secondDetailDataArr = $secondDataArr[$secondId];
		
		//--- GET PROVIDER NAME ---
		if($groupby == "Procedure" || $groupby == "Facility" || $groupby == "CPTCategory" || $groupby=="ins_group"){
			$providerId = $secondId;
			$provider_name_arr = preg_split('/ /',$providerNameArr[$providerId]);
			$physicianName = $provider_name_arr[1][0];
			$physicianName .= $provider_name_arr[0][0];
			$physicianName = trim(strtoupper($physicianName));
			
		}
		if($groupby == "Physician" ){
		$facilityId = $secondId;
		$facilityName = $facilityNameArr[$facilityId];
		}
		
		$thirdIdArr = array();
		if(count($secondDetailDataArr)>0){
			$thirdIdArr = array_keys($secondDetailDataArr);
		}//echo "<br> -----thirdIdArr-----------<br>";print_r($thirdIdArr);
		$provider_total_arr = array();
		//--- START CHARGE LIST DETAIL LOOP UNDER SINGLE PROVIDER -------
		
		for($d=0;$d<count($thirdIdArr);$d++){
			$totalBalance='';
			$thirdId = $thirdIdArr[$d];
			
			//--- GET ENCOUNTER DETAILS--------
			$encounterDataArr = $secondDetailDataArr[$thirdId];
			//echo "<br> -----encounterDataArr-----------<br>";print_r($encounterDataArr);
			$date_of_service = $encounterDataArr['date_of_service'];
			$facilityPracCode = $encounterDataArr['facilityPracCode'];
			$lastPaymentDate = $encounterDataArr['lastPaymentDate'];
			$postedDate = $encounterDataArr['postedDate'];
			
			$procedureId = $encounterDataArr['procCode'];
			$procedureName = core_refine_user_input($procedureCodeArr[$procedureId]);
			$procedureDesc = $procedureDescArr[$procedureId];
			
			if($lastPaymentDate == '00-00-0000'){
				$lastPaymentDate = NULL;
			}
			$postedDate = $dataArr['postedDate'];
			if($postedDate == '00-00-00'){
				$postedDate = NULL;
			}
			//--- GET PATIENT NAME ----
			$patientName = core_name_format($encounterDataArr['patient_lname'], $encounterDataArr['patient_fname'], $encounterDataArr['patient_mname']);
			
			$patient_id = $encounterDataArr['patient_id'];

			$arrPatientIds[$patient_id ]=$patient_id;
			
			//--- GET INSURANCE COMPANY NAME ----
			$primaryInsuranceCoId = $encounterDataArr['primaryInsuranceCoId'];	
			$secondaryInsuranceCoId = $encounterDataArr['secondaryInsuranceCoId'];
			$tertiaryInsuranceCoId = $encounterDataArr['tertiaryInsuranceCoId'];
			$insCompanyNameArr = array();
			if($primaryInsuranceCoId > 0){
				$insCompanyNameArr[] = $insCompanyArr[$primaryInsuranceCoId];
			}
			if($secondaryInsuranceCoId > 0){
				$insCompanyNameArr[] = $insCompanyArr[$secondaryInsuranceCoId];
			}
			if($tertiaryInsuranceCoId > 0){
				$insCompanyNameArr[] = $insCompanyArr[$tertiaryInsuranceCoId];
			}
			$insCompanyNameCsv=$insCompanyName = join(', ',$insCompanyNameArr);

			// BALANCE DEDUCT BY OVER PAYMENT
			if($encounterDataArr["newBalance"]>0){
				$totalBalance = $encounterDataArr['newBalance'];
			}else{
				if($encounterDataArr['overPaymentForProc']>0){
					$totalBalance = - $encounterDataArr['overPaymentForProc']; //overPayment
				}else{
					$totalBalance = $encounterDataArr['newBalance'];
				}
			}			
			$insuranceDue = $encounterDataArr['insDue'];
			$insuranceDue = ($insuranceDue<0) ? 0 : $insuranceDue; 
			$patientDue = $encounterDataArr['patDue'];
			$patientDue = ($patientDue<0) ? 0 : $patientDue;
			$patientDue = ($totalBalance<=0) ? 0 : $patientDue;

			$submitted = $encounterDataArr["submitted"];
			$first_posted_date = $encounterDataArr["first_posted_date"];

			

			
			//--- ENCOUNTER CHARGES AMOUNT -----
			$units='';
			if(($DateRangeFor=='transaction_date' || $DateRangeFor=='date_of_payment') && ($submitted=='true' && $first_posted_date>=$Start_date && $first_posted_date<=$End_date)){			
				$totalAmount = $encounterDataArr['totalAmt'];
				$units = intval($encounterDataArr['units']);
			}
			if($DateRangeFor!='transaction_date' && $DateRangeFor!='date_of_payment'){			
				$totalAmount = $encounterDataArr['totalAmt'];
				$units = intval($encounterDataArr['units']);
			}
			
			$charge_list_detail_id = $encounterDataArr['charge_list_detail_id'];

			//ALLOWED AMOUNT
			$approvedAmt = $encounterDataArr['approvedAmt'];			
			

			//---- GET PAID AMOUNT FOR PATIENT ------
			$paidForProc = $patPaidAmt = $insPaidAmt = 0 ;
			if(count($mainEncounterPayArr['ALL'][$charge_list_detail_id])){
				// TOTAL PAYMENT
				$paidForProc = array_sum($mainEncounterPayArr['ALL'][$charge_list_detail_id]);
				//PATIENT PAYMENT
				$patPaidAmt  = array_sum($patPayDetArr['ALL']['patPaid'][$charge_list_detail_id]);
				//INSURANCE PAYMENT
				$insPaidAmt  = array_sum($patPayDetArr['ALL']['insPaid'][$charge_list_detail_id]);
			}
			$encounterId = $encounterDataArr['encounter_id'];
			$patCrdDbt = array_sum($pay_crd_deb_arr[$charge_list_detail_id]['Patient']);
			$patPaidAmt+= $patCrdDbt;
			$insCrdDbt = array_sum($pay_crd_deb_arr[$charge_list_detail_id]['Insurance']);
			$insPaidAmt+= $insCrdDbt;
			$paidForProc += $patCrdDbt + $insCrdDbt;
			
			
			// CREDIT AMOUNT and TOTAL BALANCE
			$creditAmount = $encounterDataArr['overPaymentForProc'];

			// ADJUSTMENT AMOUNT
			$adjustmentAmount = $arrAdjustmentAmt[$charge_list_detail_id] + $normalWriteOffAmt[$charge_list_detail_id];
									
			//--- GET SUB TOTAL AMOUNT --------
			$subTotalAmountArr['COUNT'][] = $units;
			$subTotalAmountArr['CHARGES'][] = $totalAmount;
			$subTotalAmountArr['APPROVED_AMT'][] = $approvedAmt;
			$subTotalAmountArr['PATPAID'][] = $patPaidAmt;
			$subTotalAmountArr['INSPAID'][] = $insPaidAmt;
			$subTotalAmountArr['PAYMENTS'][] = $paidForProc;
			$subTotalAmountArr['CREDIT'][] = $creditAmount;
			$subTotalAmountArr['ADJUSTMENT'][] = $adjustmentAmount;
			$subTotalAmountArr['PATDUE'][] = $patientDue;
			$subTotalAmountArr['INSDUE'][] = $insuranceDue;
			$subTotalAmountArr['BALANCE'][] = $totalBalance;
			//INSURANCE COUNT
			if(sizeof($arrInsurance)>0){
				if($arrInsurance[$encounterDataArr['primaryInsuranceCoId']]){
					$subTotalAmountArr['PRI_INSURANCE'][$encounterId]=$encounterId;
					$allTotalAmountArr['PRI_INSURANCE'][$encounterId]=$encounterId;
				}else
				if($arrInsurance[$encounterDataArr['secondaryInsuranceCoId']]){
					$subTotalAmountArr['SEC_INSURANCE'][$encounterId]=$encounterId;
					$allTotalAmountArr['SEC_INSURANCE'][$encounterId]=$encounterId;
				}
			}			
			
			//--- GET GRAND TOTAL AMOUNT --------
			$allTotalAmountArr['COUNT'][] = $units;
			$allTotalAmountArr['CHARGES'][] = $totalAmount;
			$allTotalAmountArr['APPROVED_AMT'][] = $approvedAmt;
			$allTotalAmountArr['PATPAID'][] = $patPaidAmt;
			$allTotalAmountArr['INSPAID'][] = $insPaidAmt;
			$allTotalAmountArr['PAYMENTS'][] = $paidForProc;
			$allTotalAmountArr['CREDIT'][] = $creditAmount;
			$allTotalAmountArr['ADJUSTMENT'][] = $adjustmentAmount;
			$allTotalAmountArr['PATDUE'][] = $patientDue;
			$allTotalAmountArr['INSDUE'][] = $insuranceDue;
			$allTotalAmountArr['BALANCE'][] = $totalBalance;
			
			// RVU VALUES
			$rvuTd='';
			if($rvu=='1'){
				$work_rvu = $allRVUValues[$procedureId]['work_rvu'];
				$pe_rvu = $allRVUValues[$procedureId]['pe_rvu'];
				$mp_rvu = $allRVUValues[$procedureId]['mp_rvu'];

				$facPricingAmt = round((((($work_rvu * $bugdet_neu_adj_gpci) * $work_gpci) + ($pe_rvu * $practice_expense_gpci) + ($mp_rvu * $malpractice_gpci))  * $convFactor)  * $units, 2);				
				
				if($facPricingAmt<=0){ $facPricingAmt='';}
				$rvuTd='<td class="text_10" style="text-align:right; width:'.$w_cols.'" valign="top">'.$CLSReports->numberFormat($facPricingAmt,2).' &nbsp;</td>';
			}
			
			//--- NUMBER FORMAT FOR PATIENT AMOUNT ---------
			$totalAmount = $CLSReports->numberFormat($totalAmount,2);
			$approvedAmt = $CLSReports->numberFormat($approvedAmt,2,1);
			$patPaidAmt = $CLSReports->numberFormat($patPaidAmt,2);
			$insPaidAmt = $CLSReports->numberFormat($insPaidAmt,2);
			$paidForProc = $CLSReports->numberFormat($paidForProc,2);
			$creditAmount = $CLSReports->numberFormat($creditAmount,2);
			$adjustmentAmount = $CLSReports->numberFormat($adjustmentAmount,2);
			$patientDue = $CLSReports->numberFormat($patientDue,2);
			$insuranceDue = $CLSReports->numberFormat($insuranceDue,2);
			$totalBalance = $CLSReports->numberFormat($totalBalance,2);
			
			if($groupby == "Procedure"){$GourpCSV=$procedureName; $GourpCSVId=$procedureId; $secGrpCSV=$physicianName;}
			elseif($groupby == "Physician"){$GourpCSV=$physicianName; $GourpCSVId=$providerId; $secGrpCSV=$procedureName;}
			elseif($groupby == "CPTCategory"){$GourpCSV=$CPTName; $GourpCSVId=$CPTId; $secGrpCSV=$procedureName;}
			elseif($groupby == "ins_group"){$GourpCSV =$insGrpName; $GourpCSVId=$insGrpid; $secGrpCSV=$procedureName;}
			elseif($groupby == "Facility"){$GourpCSV =$facilityName; $GourpCSVId=$facilityId; $secGrpCSV=$procedureName;}			
			
			$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['group_by']=$GourpCSV;
			if($groupby == "Facility" && $stepUpId != $providerId){
				$stepUpId = $providerId;
				//if($monthDiff<=6){
					$page_content .= '<tr><td colspan="'.$colspan.'" class="text_b_w">Facility: '.$facilityName.' - > Physician: '.$physicianName.'</td></tr>';
					$page_contentPDF .= '<tr><td colspan="'.$colspan.'" class="text_b_w">Facility: '.$facilityName.' - > Physician: '.$physicianName.'</td></tr>';
				//}else{
				//	$data_csv.="Physician: ".$physicianName.",,,,,,,,,,,,,,,,,\n";
					//}
			}
			else if($groupby == "Physician" && $stepUpId != $facilityId){
				$stepUpId = $facilityId;
				//if($monthDiff<=6){
				$page_content .= '<tr><td colspan="'.$colspan.'" class="text_b_w">Facility: '.$facilityName.'</td></tr>';
				$page_contentPDF .= '<tr><td colspan="'.$colspan.'" class="text_b_w">Facility: '.$facilityName.'</td></tr>';
				//}else{
				//	$data_csv.="Facility: ".$facilityName.",,,,,,,,,,,,,,,,,\n";
				//}
			}
			$page_content .='<tr bgcolor="#FFFFFF">';
			$page_contentPDF .='<tr bgcolor="#FFFFFF">';
			
			if($groupby == "Facility" || $groupby == "Physician" || $groupby == "CPTCategory" || $groupby == "ins_group"){
				$csvTD=$procedureName;
				if(strlen($procedureName)>14){
					$procedureName=substr($procedureName,0,13).'...';
				}

				//if($monthDiff<=6){
					$page_content .='<td class="text_10" valign="top" style="word-wrap:break-word; width:'.$w_phy.'">'.$procedureName.'</td>';
					$page_content .='<td class="text_10" valign="top" style="word-wrap:break-word; width:'.$w_phy.'">'.$procedureDesc.'</td>';
					$page_contentPDF .='<td class="text_10" valign="top" style="word-wrap:break-word; width:'.$w_phy.'">'.$procedureName.'</td>';
				//}
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['conditional_1']=$procedureName;
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['conditional_1_1']=$procedureDesc;
			}
			else{
				$csvTD=$physicianName;
				//if($monthDiff<=6){
					$page_content .= '<td class="text_10" valign="top" style="word-wrap:break-word; width:'.$w_phy.'">'.$physicianName.'</td>';
					$page_contentPDF .= '<td class="text_10" valign="top" style="word-wrap:break-word; width:'.$w_phy.'">'.$physicianName.'</td>';
				//}
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['conditional_1']=$physicianName;
			}
				
				//if($monthDiff<=6){
					$page_content .= '
					<td class="text_10" valign="top" style="word-wrap:break-word; width:'.$w_first_col.'">'.$patientName.' - '.$patient_id.'</td>
					<td style="word-wrap:break-word; text-align:center; width:'.$w_date.'" class="text_10" valign="top"  nowrap >'.$date_of_service.'</td>
					<td style="word-wrap:break-word; text-align:center; width:'.$w_date.'" class="text_10" valign="top">'.$lastPaymentDate.'</td>';
					$page_contentPDF .= '
					<td class="text_10" valign="top" style="word-wrap:break-word; width:'.$w_first_col.'">'.$patientName.' - '.$patient_id.'</td>
					<td style="word-wrap:break-word; text-align:center; width:'.$w_date.'" class="text_10" valign="top"  nowrap >'.$date_of_service.'</td>
					<td style="word-wrap:break-word; text-align:center; width:'.$w_date.'" class="text_10" valign="top">'.$lastPaymentDate.'</td>';
				//}else{
					$data_csv.=$csvTD.", ".str_replace(',',' ',$patientName).' - '.$patient_id.", ".$date_of_service.", ".$lastPaymentDate.", ";
				//}
				
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['patient_name']=$patientName.' - '.$patient_id;
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['dos']=$date_of_service;
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['last_payment_date']=$lastPaymentDate;
				
				
				if($groupby == "Procedure"){
					//if($monthDiff<=6){
						if(strlen($facilityPracCode)>13){ $facilityPracCodepdf= substr($facilityPracCode,0,11).'..';}
						$insCompanyNamePDF = $insCompanyName;
						if(strlen($insCompanyNamePDF)>5){ $insCompanyNamePDF = substr($insCompanyNamePDF,0,5).'..';}
						$page_content .='
						<td style="word-wrap:break-word; text-align:center;width:'.$w_cols.'" class="text_10" valign="top">'.$facilityPracCode.'</td>
						<td class="text_10" valign="top" style="word-wrap:break-word; width:'.$w_cols.'">'.$insCompanyName.'</td>';
						$page_contentPDF .='
						<td style="word-wrap:break-word; text-align:center;width:'.$w_cols.'" class="text_10" valign="top">'.$facilityPracCodepdf.'</td>
						<td class="text_10" valign="top" style="word-wrap:break-word; width:'.$w_cols.'">'.$insCompanyNamePDF.'</td>';
					//}else{
						$data_csv.=$facilityPracCode.", ".str_replace(',',' ',$insCompanyNameCsv).", ";
					//}
					$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['conditional_2']=$facilityPracCode;
					$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['conditional_2_1']=$insCompanyName;
				}
				else{
					//if($monthDiff<=6){
						$insCompanyNamePDF = $insCompanyName;
						if(strlen($insCompanyNamePDF)>5){ $insCompanyNamePDF = substr($insCompanyNamePDF,0,5).'..';}
						$page_content .= '<td class="text_10" valign="top" style="word-wrap:break-word; width:'.$w_cols.'">'.$insCompanyName.'</td>';
						$page_contentPDF .= '<td class="text_10" valign="top" style="word-wrap:break-word; width:'.$w_cols.'">'.$insCompanyNamePDF.'</td>';
					//}else{
						$data_csv.=str_replace(',',' ',$insCompanyNameCsv).", ";
					//}
					$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['conditional_2']=$insCompanyName;
				}
				
				//if($monthDiff<=6){
					$page_content .= '
					<td style="word-wrap:break-word; text-align:center; width:'.$w_units.'" class="text_10" valign="top">'.$units.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.'" class="text_10" valign="top">'.$totalAmount.'</td>
					<td class="notInPdf text_10" style="word-wrap:break-word; text-align:right; width:'.$w_cols.'" valign="top">'.$approvedAmt.'</td>
					'.$rvuTd.'
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$patPaidAmt.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$insPaidAmt.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$paidForProc.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$creditAmount.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$adjustmentAmount.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$patientDue.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$insuranceDue.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$totalBalance.'</td>
				</tr>';
				
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['units']=$units;
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['charges']=$totalAmount;
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['approved_amt']=$approvedAmt;
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['pat_paid']=$patPaidAmt;
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['ins_paid']=$insPaidAmt;
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['total_paid']=$paidForProc;
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['credit']=$creditAmount;
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['adjustment']=$adjustmentAmount;
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['pat_due']=$patientDue;
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['ins_due']=$insuranceDue;
				$arr[$GourpCSV][$secGrpCSV][$patientName][$d]['balance']=$totalBalance;
				//fputcsv($fp,$arr, ",","\"");
				//$fp = fopen ($csv_file_name, 'a+');
				
				$page_contentPDF .= '
					<td style="word-wrap:break-word; text-align:center; width:'.$w_units.'" class="text_10" valign="top">'.$units.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.'" class="text_10" valign="top">'.$totalAmount.'</td>
					<td class="notInPdf text_10" style="word-wrap:break-word; text-align:right; width:'.$w_cols.'" valign="top">'.$approvedAmt.'</td>
					'.$rvuTd.'
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$patPaidAmt.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$insPaidAmt.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$paidForProc.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$creditAmount.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$adjustmentAmount.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$patientDue.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$insuranceDue.'</td>
					<td style="word-wrap:break-word; text-align:right; width:'.$w_cols.';" class="text_10" valign="top">'.$totalBalance.'</td>
				</tr>';
				//}
			//if($monthDiff>6){
				$data_csv.= $units.", ".str_replace(',','',$totalAmount).", ".str_replace(',','',$approvedAmt).", ".str_replace(',','',$patPaidAmt).", ".str_replace(',','',$insPaidAmt).", ".str_replace(',','',$paidForProc).", ".str_replace(',','',$creditAmount).", ".str_replace(',','',$adjustmentAmount).", ".str_replace(',','',$patientDue).", ".str_replace(',','',$insuranceDue).", ".str_replace(',','',$totalBalance)."\n";
			//}
		}
		
		if($pr == count($secondIDArr)-1){
			//--- GET GRAND TOTAL AMOUNTS -------
			$subTotalUnits = array_sum($subTotalAmountArr['COUNT']);
			$subTotalAmount = array_sum($subTotalAmountArr['CHARGES']);
			$subTotalApprovedAmt = array_sum($subTotalAmountArr['APPROVED_AMT']);
			$subTotalPatPaid = array_sum($subTotalAmountArr['PATPAID']);
			$subTotalInsPaid = array_sum($subTotalAmountArr['INSPAID']);
			$subPaidForProc = array_sum($subTotalAmountArr['PAYMENTS']);	
			$subTotalCredit = array_sum($subTotalAmountArr['CREDIT']);	
			$subTotalAdjustment = array_sum($subTotalAmountArr['ADJUSTMENT']);	
			$subTotalPatDue = array_sum($subTotalAmountArr['PATDUE']);
			$subTotalInsDue = array_sum($subTotalAmountArr['INSDUE']);
			$subTotalBalance = array_sum($subTotalAmountArr['BALANCE']);
			$subTotalPriCount = count($subTotalAmountArr['PRI_INSURANCE']);
			$subTotalSecCount = count($subTotalAmountArr['SEC_INSURANCE']);

			if($dispChart == 1){
				// CHART INDIVIDUAL PROCEDURE TOTAL 
				$cPno = array_search($procedureId, $chartProcedureId);
				$bool = in_array($procedureId, $chartProcedureId);

				if($bool == FALSE){
					$chartFacility[$m][1] = $procedureName;
					$chartFacility[$m][2] = $subTotalAmount;
					$chartFacility[$m][3] = $subPaidForProc;					
					$chartProcedureId[$m] = $procedureId;
					$m++;
				}
				else{
					$chartFacility[$cPno][1] = $provider_name;
					$chartFacility[$cPno][2] += $subTotalAmount;
					$chartFacility[$cPno][3] += $subPaidForProc;
				}
			}
						
			//---- NUMBER FORMAT FOR GRAND TOTAL AMOUNTS ---------
			$subTotalAmount = $CLSReports->numberFormat($subTotalAmount,2);
			$subTotalApprovedAmt = $CLSReports->numberFormat($subTotalApprovedAmt,2);
			$subTotalPatPaid = $CLSReports->numberFormat($subTotalPatPaid,2);
			$subTotalInsPaid = $CLSReports->numberFormat($subTotalInsPaid,2);
			$subPaidForProc = $CLSReports->numberFormat($subPaidForProc,2);
			$subTotalCredit = $CLSReports->numberFormat($subTotalCredit,2);
			$subTotalAdjustment = $CLSReports->numberFormat($subTotalAdjustment,2);
			$subTotalPatDue = $CLSReports->numberFormat($subTotalPatDue,2);
			$subTotalInsDue = $CLSReports->numberFormat($subTotalInsDue,2);
			$subTotalBalance = $CLSReports->numberFormat($subTotalBalance,2);
			
			//if($monthDiff>6){
			$data_csv.=",,,,,,,,,,,,,,,,,\n";
			//}

			$colspanForInsCnt=$colspan-6;			
			
			$page_content .='
				<tr><td  colspan="'.$colspan.'" class="total-row"></td></tr>
				<tr class="subtotal">
					<td style="text-align:right;"></td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;" ></td>
					<td style="text-align:right;"></td>';

					//if($monthDiff>6){
					$data_csv.=", , , , ";
					//}
					if($groupby == "Procedure"){
					$page_content .= '<td style="text-align:right;"></td>';
					//if($monthDiff>6){
					//$data_csv.=", ";
					//}
					}
					if($groupby != "Procedure"){
						$page_content .= '<td style="text-align:right;"></td>';
					}
					$page_content .= '
					<td style="text-align:right;font-size:10px;" class="text_10b" nowrap>Sub Total:</td>
					<td class="text_10b" style="text-align:center;font-size:11px;">'.$subTotalUnits.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalAmount.'</td>
					<td class="notInPdf text_10b" style="text-align:right;font-size:11px;">'.$subTotalApprovedAmt.'</td>
					'.$blankTDRVU.'
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalPatPaid.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalInsPaid.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subPaidForProc.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalCredit.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalAdjustment.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalPatDue.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalInsDue.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalBalance.'</td>
				</tr>';
				if(sizeof($arrInsurance)>0){
					$page_content .='
					<tr class="subtotal">
						<td style="text-align:left;" colspan="4"></td>
						<td style="text-align:right;" colspan="2">Primary Ins#</td>
						<td style="text-align:left;" colspan="'.$colspanForInsCnt.'">&nbsp;'.$subTotalPriCount.'</td>
					</tr>
					<tr class="subtotal">
						<td style="text-align:left;" colspan="4"></td>
						<td style="text-align:right;" colspan="2">Secondary Ins#</td>
						<td style="text-align:left;" colspan="'.$colspanForInsCnt.'">&nbsp;'.$subTotalSecCount.'</td>
					</tr>';
				}
				$page_content .='<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';
				
				$page_contentPDF .='
				<tr><td  colspan="'.$colspan.'" class="total-row"></td></tr>
				<tr class="subtotal">
					<td style="text-align:right;"></td>
					<td style="text-align:right;"></td>
					<td style="text-align:right;" ></td>';
					if($groupby == "Procedure"){
						$page_contentPDF.='<td style="text-align:right;"></td>';
					}

					if($groupby == "Procedure"){
						$page_contentPDF .= '<td style="text-align:right;"></td>';
						$data_csv.=", ";
					}
					if($groupby != "Procedure"){
						$page_contentPDF .= '<td style="text-align:right;font-size:10px;" class="text_10b" nowrap></td>';
					}
					$page_contentPDF .= '
					<td style="text-align:right;font-size:10px;" class="text_10b" nowrap>Sub Total:</td>
					<td class="text_10b" style="text-align:center;font-size:11px;">'.$subTotalUnits.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalAmount.'</td>
					<td class="notInPdf text_10b" style="text-align:right;font-size:11px;">'.$subTotalApprovedAmt.'</td>
					'.$blankTDRVU.'
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalPatPaid.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalInsPaid.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subPaidForProc.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalCredit.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalAdjustment.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalPatDue.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalInsDue.'</td>
					<td class="text_10b" style="text-align:right;font-size:11px;">'.$subTotalBalance.'</td>
				</tr>';
				if(sizeof($arrInsurance)>0){
					$page_contentPDF .='
					<tr class="subtotal">
						<td style="text-align:left;" colspan="4"></td>
						<td style="text-align:right;" colspan="2">Primary Ins#</td>
						<td style="text-align:left;" colspan="'.$colspanForInsCnt.'">&nbsp;'.$subTotalPriCount.'</td>
					</tr>
					<tr class="subtotal">
						<td style="text-align:left;" colspan="4"></td>
						<td style="text-align:right;" colspan="2">Secondary Ins#</td>
						<td style="text-align:left;" colspan="'.$colspanForInsCnt.'">&nbsp;'.$subTotalSecCount.'</td>
					</tr>';
				}
				$page_contentPDF .='<tr><td colspan="'.$colspan.'" class="total-row"></td></tr>';

				//if($monthDiff>6){
				//$data_csv.= "Sub Total :, ".$subTotalUnits.", ".str_replace(',','',$subTotalAmount).", ".str_replace(',','',$subTotalApprovedAmt).", ".str_replace(',','',$subTotalPatPaid).", ".str_replace(',','',$subTotalInsPaid).", ".str_replace(',','',$subPaidForProc).", ".str_replace(',','',$subTotalCredit).", ".str_replace(',','',$subTotalAdjustment).", ".str_replace(',','',$subTotalPatDue).", ".str_replace(',','',$subTotalInsDue).", ".str_replace(',','',$subTotalBalance)."\n";
				//}
		}
	}
	//if($monthDiff>6){
	$data_csv_final.=str_replace("&nbsp;", " ", $data_csv);
	//}
}

//SORTING OF CSV EXPORT-------------------------------
$tempArr=$arr;
unset($arr);
ksort($tempArr, SORT_NATURAL);
foreach($tempArr as $firstGroup =>$arrSecGroup){
	ksort($arrSecGroup, SORT_NATURAL);
	foreach($arrSecGroup as $secGroup =>$arrPatients){
		ksort($arrPatients);
		foreach($arrPatients as $patName =>$arrPatients){
			foreach($arrPatients as $arrDetails){

				$arr=array();
				$arr[]=$firstGroup;
				$arr[]=$arrDetails['conditional_1'];	//CPT CODE or PHYSICIAN NAME
				if($groupby == "Facility" || $groupby == "Physician" || $groupby == "CPTCategory" || $groupby == "ins_group"){
					$arr[]=$arrDetails['conditional_1_1'];	//CPT DESCRIPTION
				}
				$arr[]=$arrDetails['patient_name'];
				$arr[]=$arrDetails['dos'];
				$arr[]=$arrDetails['last_payment_date'];
				if($groupby == "Procedure"){
					$arr[]=$arrDetails['conditional_2']; //FACILITY
					$arr[]=$arrDetails['conditional_2_1']; //INSURANCE NAME
				}else{
					$arr[]=$arrDetails['conditional_2']; //INSURANCE NAME
				}
				$arr[]=$arrDetails['units'];
				$arr[]=$arrDetails['charges'];
				$arr[]=$arrDetails['approved_amt'];
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
//----------------------------------------------------


//-----------DELETED RECORDS-------------
$del_csv = $del_csv_html = $del_pdf_html = $del_paid_csv='';


$del_csv='';
if(count($arrVoidPay)>0){

	$del_total_cols = 14;
	$del_pat_col =  "10";
	$del_fac_col = $del_fac_col1 = "10";
	$del_w_cols = $del_w_cols1 = floor((100 - ($del_pat_col+$del_fac_col))/($del_total_cols-2));
	$del_pat_col = $del_pat_col1 = 100 - ( (($del_total_cols-2) * $del_w_cols) + $del_fac_col);
	$del_w_cols = $del_w_cols."%";
	$del_pat_col = $del_pat_col."%";
	$del_fac_col = $del_fac_col."%";
	
	$fristGrpArr = array_keys($arrVoidPay);
	$arrGrandTotal = array();
	foreach($fristGrpArr as $firstID){
		$arrFirstTotal = array();
		if($groupby=='Physician' || $groupby=='Procedure' || $groupby == "CPTCategory" || $groupby == "ins_group"){
			$firstTitle='Physician';
			$firstGrpName = $providerNameArr[$firstID];
		}else{
			$firstTitle='Facility';
			$firstGrpName = $arrAllFacilities[$firstID];
		}
		$del_csv.='<tr><td class="text_b_w" align="left" colspan="'.$del_total_cols.'">'.$firstTitle.' : '.$firstGrpName.'</td></tr>';
		
		$secGrpArr = array_keys($arrVoidPay[$firstID]);
		$arrPaymentCntFirst = $arrPatCheckFirst = $arrCheckFirst = $arrCashPFirst = $arrEFTFirst = $arrMoneyOrderFirst = $arrVEEPFirst = $arrCCPFirst = $arrAdjFirst = '';
		foreach($secGrpArr as $secID){
			$arrSecTotal = array();
			if($groupby=='Physician' || $groupby=='Procedure' || $groupby == "CPTCategory" || $groupby == "ins_group"){
				$secTitle='Facility';
				$secGrpName = $arrAllFacilities[$secID];
			}else{
				$secTitle='Physician';
				$secGrpName = $providerNameArr[$secID];
			}

			$encArr = $arrVoidPay[$firstID][$secID]['detail'];
			//$del_csv.='<tr><td class="text_b_w" align="left" colspan="'.$total_cols.'">'.$secTitle.' : '.$secGrpName.'</td></tr>';
			foreach($encArr as $encID=>$arrData){
				$delOprArr = array_unique(array_values($arrData['opr']));
				$delOprNameArr = array();
				foreach($delOprArr as $oprId){
					$delOprNameArr[] = $userNameTwoCharArr[$oprId];
				}
				$delOprName = join(", ",array_unique($delOprNameArr));

			$del_csv .= '
			<tr>
				<td class="text_10" bgcolor="#FFFFFF" align="left" style="width:'.$del_pat_col.'">'.$arrData['pt_name'].' - '.$arrData['pt_id'].'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="left" style="width:'.$del_fac_col.'">'.$secGrpName.'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="center" style="width:'.$del_w_cols.'">'.$arrData['dos'].'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="center" style="width:'.$del_w_cols.'">'.$encID.'</td>
				<td class="text_10" bgcolor="#FFFFFF" align="right" style="width:'.$del_w_cols.'">'.$CLSReports->numberFormat($arrData['charges'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;width:'.$del_w_cols.'">'.$CLSReports->numberFormat($arrData['cash'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;width:'.$del_w_cols.'">'.$CLSReports->numberFormat($arrData['pt_check'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;width:'.$del_w_cols.'">'.$CLSReports->numberFormat($arrData['ins_check'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;width:'.$del_w_cols.'">'.$CLSReports->numberFormat($arrData['CC'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;width:'.$del_w_cols.'">'.$CLSReports->numberFormat($arrData['EFT'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;width:'.$del_w_cols.'">'.$CLSReports->numberFormat($arrData['MO'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;width:'.$del_w_cols.'">'.$CLSReports->numberFormat($arrData['VEEP'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;width:'.$del_w_cols.'">'.$CLSReports->numberFormat($arrData['adjustments'],2).'</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;width:'.$del_w_cols.'">&nbsp;'.$delOprName.'</td>
			</tr>';
			$arrFirstTotal['charges'] += $arrData['charges'];
			$arrFirstTotal['cash'] += $arrData['cash'];
			$arrFirstTotal['pt_check'] += $arrData['pt_check'];
			$arrFirstTotal['ins_check'] += $arrData['ins_check'];
			$arrFirstTotal['CC'] += $arrData['CC'];
			$arrFirstTotal['EFT'] += $arrData['EFT'];
			$arrFirstTotal['MO'] += $arrData['MO'];	
			$arrFirstTotal['VEEP'] += $arrData['VEEP'];	
			$arrFirstTotal['adjustments'] += $arrData['adjustments'];
			}
		}
		//FIRST GROUP TOTAL		
		$del_csv .= '
			<tr><td colspan="'.$del_total_cols.'" class="total-row"></td></tr>
			<tr class="text_12b">
				<td bgcolor="#FFFFFF"  style="text-align:right; width:'.($pat_col1+$fac_col1+$w_cols1+$w_cols1).'%;" colspan="4" >'.$firstTitle.' Total: </td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['charges'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['cash'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['pt_check'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['ins_check'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['CC'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['EFT'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['MO'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['VEEP'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['adjustments'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:left">&nbsp;</td>
			</tr>
			<tr><td colspan="'.$del_total_cols.'" class="total-row"></td></tr>
			';
			$del_csv .= '
			<tr><td colspan="'.$del_total_cols.'" class="total-row"></td></tr>
			<tr class="text_12b">
				<td bgcolor="#FFFFFF"  style="text-align:right" colspan="4">Total Voided Payment: </td>
				<td bgcolor="#FFFFFF"  style="text-align:right" > </td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrFirstTotal['cash'] + $arrFirstTotal['pt_check']+$arrFirstTotal['ins_check']+$arrFirstTotal['CC']+$arrFirstTotal['EFT']+$arrFirstTotal['MO']+$arrFirstTotal['VEEP'],2).'</td>
				<td bgcolor="#FFFFFF"  style="text-align:right" colspan="8"></td>
			</tr>
			<tr><td colspan="'.$total_cols.'" class="total-row"></td></tr>
			';
			$arrGrandTotal['charges'] += $arrFirstTotal['charges'];
			$arrGrandTotal['cash'] += $arrFirstTotal['cash'];
			$arrGrandTotal['pt_check'] += $arrFirstTotal['pt_check'];
			$arrGrandTotal['ins_check'] += $arrFirstTotal['ins_check'];
			$arrGrandTotal['CC'] += $arrFirstTotal['CC'];
			$arrGrandTotal['EFT'] += $arrFirstTotal['EFT'];
			$arrGrandTotal['MO'] += $arrFirstTotal['MO'];
			$arrGrandTotal['VEEP'] += $arrFirstTotal['VEEP'];
			$arrGrandTotal['adjustments'] += $arrFirstTotal['adjustments'];		

	}
	
	$arrTotDelPayment=$arrGrandTotal['cash'] + $arrGrandTotal['pt_check']+$arrGrandTotal['ins_check']+$arrGrandTotal['CC']+$arrGrandTotal['EFT']+$arrGrandTotal['MO']+$arrGrandTotal['VEEP'];
	$totalDeletedCharges=$arrGrandTotal['charges'];
	$totalDeletedPayments=$arrTotDelPayment;
	$totalDeletedAdjustments=$arrGrandTotal['adjustments'];

	$del_csv .= '
			<tr><td colspan="'.$del_total_cols.'" class="total-row"></td></tr>
			<tr class="text_12b">
				<td bgcolor="#FFFFFF"  style="text-align:right" colspan="4">Grand Total: </td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['charges'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['cash'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['pt_check'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['ins_check'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['CC'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['EFT'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['MO'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['VEEP'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrGrandTotal['adjustments'],2).'</td>
					<td class="text_12" bgcolor="#FFFFFF" style="text-align:left">&nbsp;</td>
			</tr>
			<tr><td colspan="'.$del_total_cols.'" class="total-row"></td></tr>
			';

	$del_csv .= '
			<tr><td colspan="'.$del_total_cols.'" class="total-row"></td></tr>
			<tr class="text_12b">
				<td bgcolor="#FFFFFF"  style="text-align:right" colspan="4">Total Voided Payments: </td>
				<td bgcolor="#FFFFFF"  style="text-align:right" > </td>
				<td class="text_12" bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($arrTotDelPayment,2).'</td>
				<td bgcolor="#FFFFFF"  style="text-align:right" colspan="8"></td>
					
			</tr>
			<tr><td colspan="'.$del_total_cols.'" class="total-row"></td></tr>
			';

	$del_paid_csv .= '
	'.$page_header_val.'
	<table class="rpt_table rpt_table-bordered">
	<tr id="heading_orange"><td colspan="'.$del_total_cols.'">Voided Records</td></tr>
	<tr>
			<td align="center" class="text_b_w" style="width:'.$del_pat_col.'">Patient Name-ID</td>
			<td align="center" class="text_b_w" style="width:'.$del_fac_col.'">Facility</td>
			<td align="center" class="text_b_w" style="width:'.$del_w_cols.'">DOS</td>
			<td align="center" class="text_b_w" style="width:'.$del_w_cols.'">E.ID</td>
			<td align="center" class="text_b_w" style="width:'.$del_w_cols.'">Charges</td>
			<td width="400" class="text_b_w" colspan="7" style="text-align:center; width:'.(7*$del_w_cols1).'%">Payments</td>
			<td align="center" class="text_b_w" style="width:'.$del_w_cols.'">Write-off & Adj</td>
			<td align="center" class="text_b_w" style="width:'.$del_w_cols.'">Del Opr</td>
		</tr>
		<tr>
			<td align="center" class="text_b_w" colspan="5" style="width:'.($del_pat_col1+$del_fac_col1+($del_w_cols1*3)).'%"></td>
			<td align="center" class="text_b_w" style="width:'.$del_w_cols.'">Cash</td>
			<td align="center" class="text_b_w" style="width:'.$del_w_cols.'">Pt Check</td>
			<td align="center" class="text_b_w" style="width:'.$del_w_cols.'">Ins Check</td>
			<td align="center" class="text_b_w" style="width:'.$del_w_cols.'">CC</td>
			<td align="center" class="text_b_w" style="width:'.$del_w_cols.'">EFT</td>
			<td align="center" class="text_b_w" style="width:'.$del_w_cols.'">MO</td>
			<td align="center" class="text_b_w" style="width:'.$del_w_cols.'">VEEP</td>
			<td align="center" class="text_b_w" style="width:'.$del_w_cols.'"></td>					
			<td align="center" class="text_b_w" style="width:'.$del_w_cols.'"></td>
		</tr>'.	
		$del_csv.'
	</table>';


	$del_paid_pdf .=$del_paid_csv;
}

//GRAND TOTAL IF NORMAL AND DELETED AVAILABLE
if(empty($page_content) == false && empty($del_paid_csv) == false){
	$allTotalAmount = array_sum($allTotalAmountArr['CHARGES']);
	$allPaidForProc = array_sum($allTotalAmountArr['PAYMENTS']);
	$allAdjustment = array_sum($allTotalAmountArr['ADJUSTMENT']);
	$grandCharges=$allTotalAmount-$totalDeletedCharges;
	$grandPayments=$allPaidForProc-$totalDeletedPayments;
	$grandAdjustments=$allAdjustment-$totalDeletedAdjustments;
	
	$grand_totals_csv= '
	<table class="rpt_table rpt_table-bordered">
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
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($allTotalAmount,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($allPaidForProc,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($allAdjustment,2).'</td>
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

	$grand_totals_pdf= '
	<table class="rpt_table rpt_table-bordered">
	<tr id="heading_orange"><td colspan="4">Grand Totals</td></tr>
	<tr>
		<td class="text_b_w" style="width:15%"></td>
		<td class="text_b_w" style="width:15%; text-align:center">Charges</td>
		<td class="text_b_w" style="width:15%; text-align:center">Payments</td>
		<td class="text_b_w" style="width:15%; text-align:center">Write-off & Adj</td>
	</tr>
	<tr class="text_12b">
		<td bgcolor="#FFFFFF" style="text-align:right">Total : </td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($allTotalAmount,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($allPaidForProc,2).'</td>
		<td bgcolor="#FFFFFF" style="text-align:right">'.$CLSReports->numberFormat($allAdjustment,2).'</td>
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


//--- CREATE VARIABLE FOR PDF FILE -------
if(empty($page_content) == false || empty($del_paid_csv) == false){
	$printFile = true;
	$op = 'l';
	//--- GET GRAND TOTAL AMOUNTS -------
	$allTotalUnits = array_sum($allTotalAmountArr['COUNT']);
	$allTotalAmount = array_sum($allTotalAmountArr['CHARGES']);
	$allTotalApprovedAmt = array_sum($allTotalAmountArr['APPROVED_AMT']);
	$allTotalPatPaid = array_sum($allTotalAmountArr['PATPAID']);
	$allTotalInsPaid = array_sum($allTotalAmountArr['INSPAID']);
	$allPaidForProc = array_sum($allTotalAmountArr['PAYMENTS']);
	$allTotalCredit = array_sum($allTotalAmountArr['CREDIT']);
	$allTotalAdjustment = array_sum($allTotalAmountArr['ADJUSTMENT']);
	$allTotalPatDue = array_sum($allTotalAmountArr['PATDUE']);
	$allTotalInsDue = array_sum($allTotalAmountArr['INSDUE']);
	$allTotalBalance = array_sum($allTotalAmountArr['BALANCE']);
	$allTotalPriCnt = count($allTotalAmountArr['PRI_INSURANCE']);
	$allTotalSecCnt = count($allTotalAmountArr['SEC_INSURANCE']);
	
	//---- NUMBER FORMAT FOR GRAND TOTAL AMOUNTS ---------
	$allTotalAmount = $CLSReports->numberFormat($allTotalAmount,2);
	$allTotalApprovedAmt = $CLSReports->numberFormat($allTotalApprovedAmt,2);
	$allTotalPatPaid = $CLSReports->numberFormat($allTotalPatPaid,2);
	$allTotalInsPaid = $CLSReports->numberFormat($allTotalInsPaid,2);
	$allPaidForProc = $CLSReports->numberFormat($allPaidForProc,2);
	$allTotalCredit = $CLSReports->numberFormat($allTotalCredit,2);
	$allTotalAdjustment = $CLSReports->numberFormat($allTotalAdjustment,2);
	$allTotalPatDue = $CLSReports->numberFormat($allTotalPatDue,2);
	$allTotalInsDue = $CLSReports->numberFormat($allTotalInsDue,2);
	$allTotalBalance = $CLSReports->numberFormat($allTotalBalance,2);
	$arr = array();
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	if($CSVColulm1) $arr[]="";
	$arr[]="TOTAL:";
	$arr[]=$allTotalUnits;
	$arr[]=$allTotalAmount;
	$arr[]=$allTotalApprovedAmt;
	$arr[]=$allTotalPatPaid;
	$arr[]=$allTotalInsPaid;
	$arr[]=$allPaidForProc;
	$arr[]=$allTotalCredit;
	$arr[]=$allTotalAdjustment;
	$arr[]=$allTotalPatDue;
	$arr[]=$allTotalInsDue;
	$arr[]=$allTotalBalance;
	fputcsv($fp,$arr, ",","\"");
	$fp = fopen ($csv_file_name, 'a+');
	
	fclose($fp);

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
				<td class="rptbx3">Selected Physician: $physician_name &nbsp;&nbsp; Crediting Phy.: $crediting_physician_name</td>
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

	//REMOVING COLUMN THAT WILL NOT TRANSFER TO PDF
	$page_content_pdf = preg_replace('/<td class="notInPdf(.+)<\/td>/', '', $page_contentPDF);
	//REPLACING $w_cols with $pdf_w_cols
	$page_content_pdf = str_replace('width:'.$w_cols, 'width:'.$pdf_w_cols, $page_content_pdf);
	$page_content_pdf = str_replace('width:'.$w_first_col, 'width:'.$pdf_w_first_col, $page_content_pdf);
	$page_content_pdf = str_replace('colspan="'.$colspan.'"', 'colspan="'.$pdfColspan.'"', $page_content_pdf);
//echo '<textarea cols="500" rows="5">'.$page_content_pdf.'</textarea>';

	$data = <<<DATA
		<page backtop="$backtop" backbottom="5mm">
		<page_footer>
			<table class="rpt_table rpt_table-bordered">
				<tr>
					<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		<page_header>		
			$page_head_data_pdf
			<table style="width:100%;">				
				<tr>
DATA;
				if($groupby == "Facility" || $groupby == "Physician" || $groupby == "CPTCategory" || $groupby == "ins_group"){	
				
				$data .=<<<DATA
				<td  class="text_b_w" style="text-align:center;width:$w_phy">CPT Code</td>
DATA;
				}
				else{
				$data .=<<<DATA
				<td style="text-align:left;width:$w_phy" class="text_b_w">Phy.</td>
DATA;
				}
				$data .=<<<DATA
					<td style="text-align:center; width:$pdf_w_first_col" class="text_b_w">Patient-ID</td>
					<td style="text-align:center; width:$w_date" class="text_b_w">DOS</td>
					<td style="text-align:center; width:$w_date" class="text_b_w">Payment Date</td>
				
DATA;
				if($groupby == "Procedure" ){
				$data .=<<<DATA
				<td style="text-align:center; width:$pdf_w_cols" class="text_b_w">Facility</td>
				<td style="text-align:center; width:$pdf_w_cols" class="text_b_w">Insurance</td>
DATA;
				}
				else{
				$data .=<<<DATA
				<td style="text-align:center; width:$pdf_w_cols" class="text_b_w" >Insurance</td>
DATA;
				}
				$data .=<<<DATA
					<td style="text-align:center;width:$w_units" class="text_b_w">Units</td>
					<td style="text-align:center;width:$pdf_w_cols" class="text_b_w">Charges</td>
					$titleTDRVU_PDF
					<td style="text-align:center;width:$pdf_w_cols" class="text_b_w">Pat. Paid</td>
					<td style="text-align:center;width:$pdf_w_cols" class="text_b_w">Ins. Paid</td>
					<td style="text-align:center;width:$pdf_w_cols" class="text_b_w">Total Payment</td>
					<td style="text-align:center;width:$pdf_w_cols" class="text_b_w">Credit</td>
					<td style="text-align:center;width:$pdf_w_cols" class="text_b_w">Adjustment</td>
					<td style="text-align:center;width:$pdf_w_cols" class="text_b_w">Pat. Due</td>
					<td style="text-align:center;width:$pdf_w_cols" class="text_b_w">Ins. Due</td>
					<td style="text-align:center;width:$pdf_w_cols" class="text_b_w">Balance</td>
				</tr>
			</table> 
		</page_header>
		<table class="table">					
			$page_content_pdf
			<tr><td class="total-row" colspan="$pdfColspan" ></td></tr>
			<tr class="grandtotal">
				
DATA;
				if($groupby == "Procedure" ){
				$data .= <<<DATA
				<td  height="1px"></td>
DATA;
				}
				$data .= <<<DATA
				
				<td colspan="5" style="text-align:right;" class="text_10b" nowrap>Total :</td>
				<td class="text_10b" style="text-align:center;">$allTotalUnits</td>
				<td class="text_10b" style="text-align:right;" >$allTotalAmount</td>
				$blankTDRVU
				<td class="text_10b" style="text-align:right;">$allTotalPatPaid</td>
				<td class="text_10b" style="text-align:right;">$allTotalInsPaid</td>
				<td class="text_10b" style="text-align:right;">$allPaidForProc</td>
				<td class="text_10b" style="text-align:right;">$allTotalCredit</td>
				<td class="text_10b" style="text-align:right;">$allTotalAdjustment</td>
				<td class="text_10b" style="text-align:right;">$allTotalPatDue</td>
				<td class="text_10b" style="text-align:right;">$allTotalInsDue</td>
				<td class="text_10b" style="text-align:right;">$allTotalBalance</td>
			</tr>
DATA;
			if(sizeof($arrInsurance)>0){
				$data .= <<<DATA
				<tr class="grandtotal">
					<td style="text-align:left;" colspan="4"></td>
					<td style="text-align:left;" colspan="2">Primary Ins#</td>
					<td style="text-align:left;" colspan="$colspanForInsCnt">&nbsp;$allTotalPriCnt</td>
				</tr>
				<tr class="grandtotal">
					<td style="text-align:left;" colspan="4"></td>
					<td style="text-align:left;" colspan="2">Secondary Ins#</td>
					<td style="text-align:left;" colspan="$colspanForInsCnt">&nbsp;$allTotalSecCnt</td>
				</tr>
DATA;
			}
			$data .= <<<DATA
			<tr><td class="total-row" colspan="$pdfColspan" ></td></tr>
		</table>
		$del_paid_pdf
		$grand_totals_pdf
		</page>
DATA;
unset($page_content_pdf);


	//--- CSV FILE DATA ---
	$csvFileData =<<<DATA
		$page_head_data
		<table class="rpt_table rpt_table-bordered">
			<tr id="heading_orange">
DATA;
				if($groupby == "Facility" || $groupby == "Physician" || $groupby == "CPTCategory" || $groupby == "ins_group"){	
				
				$csvFileData .=<<<DATA
				<td width="10" style="text-align:center;" class="text10_b">CPT Code</td>
				<td style="text-align:center;" class="text_b_w">CPT Desc.</td>	
DATA;
				}
				else{
				$csvFileData .=<<<DATA
				<td width="10" style="text-align:center;" class="text10_b">Phy.</td>
DATA;
				}
				$csvFileData .=<<<DATA
				<td width="170" style="text-align:center;" class="text10_b">Patient-ID</td>
				<td width="70" style="text-align:center;" class="text10_b" >DOS</td>
				<td width="70" style="text-align:center;" class="text10_b">Payment Date</td>
DATA;
				if($groupby == "Procedure" ){
				$csvFileData .=<<<DATA
				<td width="50" style="text-align:center;" class="text10_b">Facility</td>
DATA;
				}
				$csvFileData .=<<<DATA
				<td width="110" style="text-align:center;" class="text10_b">Insurance</td>
				<td width="30" style="text-align:center;" class="text10_b">Units</td>
				<td width="70" style="text-align:center;" class="text10_b">Charges</td>
				<td width="70" style="text-align:center;" class="text10_b">Allowed Amt.</td>
				$titleTDRVU
				<td width="70" style="text-align:center;" class="text10_b">Pat. Paid</td>
				<td width="70" style="text-align:center;" class="text10_b">Ins. Paid</td>
				<td width="70" style="text-align:center;" class="text10_b">Total Payment</td>
				<td width="70" style="text-align:center;" class="text10_b">Credit</td>
				<td width="70" style="text-align:center;" class="text10_b">Adjustment</td>
				<td width="70" style="text-align:center;" class="text10_b">Pat. Due</td>
				<td width="70" style="text-align:center;" class="text10_b">Ins. Due</td>
				<td width="70" style="text-align:center;" class="text10_b">Balance</td>
			</tr>
			$page_content
			<tr><td class="total-row" colspan="$colspan" ></td></tr>
			<tr class="grandtotal">
				<td style="text-align:right;"></td>
				<td style="text-align:right;"></td>
				<td style="text-align:right;"></td>
				<td style="text-align:right;"></td>
				
DATA;
				if($groupby == "Procedure" ){
				$csvFileData .= <<<DATA
				<td width="70" height="1px"></td>
DATA;
				}
				if($groupby != "Procedure" ){
				$csvFileData .= <<<DATA
				<td class="text_10b" style="text-align:right;" nowrap></td>
DATA;
				}
				$csvFileData .= <<<DATA
				<td class="text_10b" style="text-align:right;" nowrap>Total :</td>
				<td class="text_10b" style="text-align:center;">$allTotalUnits</td>
				<td class="text_10b" style="text-align:right;" >$allTotalAmount</td>
				<td class="text_10b" style="text-align:right;" >$allTotalApprovedAmt</td>
				$blankTDRVU
				<td class="text_10b" style="text-align:right;">$allTotalPatPaid</td>
				<td class="text_10b" style="text-align:right;">$allTotalInsPaid</td>
				<td class="text_10b" style="text-align:right;">$allPaidForProc</td>
				<td class="text_10b" style="text-align:right;">$allTotalCredit</td>
				<td class="text_10b" style="text-align:right;">$allTotalAdjustment</td>
				<td class="text_10b" style="text-align:right;">$allTotalPatDue</td>
				<td class="text_10b" style="text-align:right;">$allTotalInsDue</td>
				<td class="text_10b" style="text-align:right;">$allTotalBalance</td>
			</tr>
DATA;
			if(sizeof($arrInsurance)>0){
			$csvFileData .= <<<DATA
			<tr class="grandtotal">
				<td style="text-align:left;" colspan="4"></td>
				<td style="text-align:left;" colspan="2">Primary Ins#</td>
				<td style="text-align:left;" colspan="$colspanForInsCnt">&nbsp;$allTotalPriCnt</td>
			</tr>
			<tr class="grandtotal">
				<td style="text-align:left;" colspan="4"></td>
				<td style="text-align:left;" colspan="2">Secondary Ins#</td>
				<td style="text-align:left;" colspan="$colspanForInsCnt">&nbsp;$allTotalSecCnt</td>
			</tr>
DATA;
			}

		$csvFileData.=<<<DATA
			<tr><td class="total-row" colspan="$colspan" ></td></tr>
		</table>
		$del_paid_csv
		$grand_totals_csv
DATA;

//if($monthDiff>6){
	//$data_csv_final.=" , , , , ";
	if($groupby == "Procedure" ){
		$data_csv_final.=", ";
	}
	if($groupby != "Procedure" ){
		$data_csv_final.=", ";
	}
	$data_csv_final.= "Total :, ".$allTotalUnits.", ".str_replace(',','',$allTotalAmount).", ".str_replace(',','',$allTotalApprovedAmt).", ".str_replace(',','',$allTotalPatPaid).", ".str_replace(',','',$allTotalInsPaid).", ".str_replace(',','',$allPaidForProc).", ".str_replace(',','',$allTotalCredit).", ".str_replace(',','',$allTotalAdjustment).", ".str_replace(',','',$allTotalPatDue).", ".str_replace(',','',$allTotalInsDue).", ".str_replace(',','',$allTotalBalance)."\n";
	$data_csv_final2=$data_header.$data_csv_final;
//}

}
?>