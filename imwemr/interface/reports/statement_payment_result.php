<?php 
$dateFormat= get_sql_date_format();
$curDate = date($phpDateFormat.' h:i A');

$printFile = true;
if($_POST['form_submitted']){
	$printFile = false;	
	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	$arrDateRange= $CLSCommonFunction->changeDateSelection();

	if($dayReport=='Daily'){
		$Start_date = $End_date= date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$Start_date = $arrDateRange['WEEK_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$Start_date = $arrDateRange['MONTH_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$Start_date = $arrDateRange['QUARTER_DATE_START'];
		$End_date = $arrDateRange['QUARTER_DATE_END'];
	}
	
	//CHANGE DATE FORMAT
	$Start_date = getDateFormatDB($Start_date);
	$End_date = getDateFormatDB($End_date);

	//GETTING REPORT GENERATOR NAME
	$report_generator_name = NULL;
	if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
		$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
		$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
		$report_generator_name = strtoupper($report_generator_name);
	}
	
	//GET GROUP NAME
	$group_name = "All Groups";
	if(empty($grp_id)===false){
		$arrGrpIds=explode(',',trim($grp_id));
		if(count($arrGrpIds)>1){
			$group_name = 'Multiple';
		}elseif(count($arrGrpIds)==1){
			$qry = "Select name from groups_new where gro_id = '$grp_id'";
			$res = imw_query($qry);
			$tmp_grp_ins_arr = array();
			if (imw_num_rows($res) > 0) {
				while ($det_row = imw_fetch_array($res)) {
					$group_name = $groupQryRes['name'];
				}
			}
		}
	}
	
	// -- GET ALL POS FACILITIES
	$arrAllFacilities=array();
	$qry = "select pos_facilityies_tbl.facilityPracCode as name, pos_facilityies_tbl.pos_facility_id as id, pos_tbl.pos_prac_code from pos_facilityies_tbl left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id order by pos_facilityies_tbl.headquarter desc, pos_facilityies_tbl.facilityPracCode";
	$qryRs = imw_query($qry);
	while($qryRes = imw_fetch_array($qryRs)){
		$id = $qryRes['id'];
		$name = $qryRes['name'];
		$pos_prac_code = $qryRes['pos_prac_code'];
		$arrAllFacilities[$id] = $name.' - '.$pos_prac_code;
	}						

	//GET ALL USERS
	$rs=imw_query("Select id, fname, mname, lname FROM users");	
	$providerNameArr[0] = 'No Provider';
	while($res=imw_fetch_array($rs)){
		$id  = $res['id'];
		$pro_name_arr = array();
		$pro_name_arr["LAST_NAME"] = $res['lname'];
		$pro_name_arr["FIRST_NAME"] = $res['fname'];
		$pro_name_arr["MIDDLE_NAME"] = $res['mname'];
		$pro_name = changeNameFormat($pro_name_arr);
		$providerNameArr[$id] = $pro_name;
	}
	
	// GET PAYMENTS --- MAIN QUERIES
	$paymentQry ="Select patient_chargesheet_payment_info.encounter_id,patient_chargesheet_payment_info.paymentClaims,
			patient_charges_detail_payment_info.paidForProc + 
			patient_charges_detail_payment_info.overPayment as paidForProc,
			patient_charges_detail_payment_info.charge_list_detail_id  
			FROM patient_chargesheet_payment_info 
			LEFT JOIN patient_charges_detail_payment_info ON patient_charges_detail_payment_info.payment_id = patient_chargesheet_payment_info.payment_id 
			WHERE patient_charges_detail_payment_info.deletePayment != '1' AND patient_chargesheet_payment_info.statement_pmt>0";
	if($Start_date != '' and $End_date != ''){
		$paymentQry.= " and (patient_chargesheet_payment_info.$DateRangeFor between '$Start_date' and '$End_date')";
	}
	$paymentRs=imw_query($paymentQry);
	$arrPayments=array();
	$arrEncounters=array();
	while($paymentRes=imw_fetch_array($paymentRs)){
		$encounter_id= $paymentRes['encounter_id'];
		$charge_det_id= $paymentRes['charge_list_detail_id'];
		$paidForProc = $paymentRes['paidForProc'];
		
		if($paymentRes['paymentClaims'] == 'Negative Payment'){
			$paidForProc = '-'.$paidForProc;
		}		
		
		if($charge_det_id>0){
			$arrPayments[$encounter_id][$charge_det_id]+= $paidForProc;
		}else{
			$arrCopayPayments[$encounter_id]+= $paidForProc;
		}

		$arrEncounters[$encounter_id]=$encounter_id;
	}
	
	// GET PROCEDURES DETAILS
	if(count($arrEncounters)>0){
		$printFile=true;
		$arrGroupData=array();
		$arrSummaryData=array();
		$strEncounters = implode(',',$arrEncounters);
		$chargesQry="Select patChg.encounter_id,patChg.patient_id,patChg.primary_provider_id_for_reports as 'primaryProviderId',patChg.facility_id,patChgDet.charge_list_detail_id,patChgDet.procCode, 
		DATE_FORMAT(patChg.date_of_service, '".$dateFormat."') as 'date_of_service',
		patChgDet.coPayAdjustedAmount,cpt_fee_tbl.cpt4_code,pd.lname,pd.fname, pd.mname FROM patient_charge_list patChg 
		LEFT JOIN patient_charge_list_details patChgDet ON patChgDet.charge_list_id = patChg.charge_list_id 
		LEFT JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patChgDet.procCode
		LEFT JOIN patient_data pd ON pd.id = patChg.patient_id  
		LEFT JOIN users ON users.id = patChg.primary_provider_id_for_reports 
		WHERE patChg.encounter_id In($strEncounters)";
		if(empty($grp_id) == false){
			$chargesQry.= " and patChg.gro_id IN ($grp_id)";
		}
		$chargesQry.=" ORDER BY users.lname, patChg.date_of_service";
		$chargesRs=imw_query($chargesQry);
		while($chargesRes=imw_fetch_array($chargesRs)){
			$pat_id=$chargesRes['patient_id'];
			$providerId=$chargesRes['primaryProviderId'];
			$facilityId=$chargesRes['facility_id'];
			$encounter_id=$chargesRes['encounter_id'];
			$charge_det_id=$chargesRes['charge_list_detail_id'];
			
			if(count($arrPayments[$encounter_id][$charge_det_id])>0 || count($arrCopayPayments[$encounter_id])>0){
				if($Process!="Summary"){
					$arrGroupData[$facilityId][$providerId][$encounter_id]=$encounter_id;
					$arrProcData[$encounter_id][$charge_det_id]=$chargesRes;
				}else{
					$paidAmt=$arrPayments[$encounter_id][$charge_det_id];
					if($chargesRes['coPayAdjustedAmount']==1){
						$paidAmt+=$arrCopayPayments[$encounter_id];
					}
					$arrSummaryData[$facilityId][$providerId]['PAY_AMT']+=$paidAmt;
				}
			}
		}
	}
	
	// MAKE HTML
	$totPaidAmt=0;
	$phyTots=0;
	$facTots=0;
	$grandPayAmt=0;

	$report_tit='Statement Payment (Detail)';
	if($Process=="Summary"){ $report_tit='Statement Payment (Summary)'; } 
	
		$startDate = $Start_date;
 		$lastDate  = $End_date;
		//Convert it into a timestamp.
		$from = strtotime($startDate);
		$to = strtotime($lastDate);
		$globalDateFormat = phpDateFormat();
		//Convert it to DD-MM-YYYY
		$fromDate = date($globalDateFormat, $from);
 		$toDate = date($globalDateFormat, $to);

		$page_data_header='<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
			<tr class="rpt_headers">
				<td class="rptbx1" width="235">'.$report_tit.'</td>
				<td class="rptbx2" width="245">From : '.$fromDate.' To : '.$toDate.'</td>
				<td class="rptbx3" width="235">Created By: '.$report_generator_name.' on '.date(phpDateFormat()." h:i A").'&nbsp;</td>
			</tr>
			<tr class="rpt_headers">
				<td class="rptbx1">Selected Group: '.$group_name.'</td>
				<td class="rptbx2"></td>
				<td class="rptbx3"></td>
			</tr>			
		</table>';	
	
	$page_data='';
	if($Process == "Summary"){
		foreach($arrSummaryData as $facId => $facArr){
			$facTots=0;
			$page_data.='<tr><td class="text_b_w" colspan="3">Facility : '.$arrAllFacilities[$facId].'</td></tr>';
			$pdf_data.='<tr><td class="text_b_w" colspan="3">Facility : '.$arrAllFacilities[$facId].'</td></tr>';
		
			foreach($facArr as $providerId => $providerArr){
				$facTots+=$providerArr['PAY_AMT'];
				$page_data.='<tr><td class="text_10" style="background:#FFFFFF;">'.$providerNameArr[$providerId].'</td>
				<td class="text_10" style="background:#FFFFFF; text-align:right">'.numberFormat($providerArr['PAY_AMT'],2).'&nbsp;</td>
				<td class="text_10" style="background:#FFFFFF;"></td>
				</tr>';
			}
			//FACILITY TOTALS
			$grandPayAmt+=$facTots;
			$page_data.='<tr><td class="text_10b" style="background:#FFFFFF; text-align:right">Facility Total : </td>
			<td class="text_10b" style="background:#FFFFFF; text-align:right">'.numberFormat($facTots,2).'&nbsp;</td>
			<td class="text_10" style="background:#FFFFFF;"></td>
			</tr>';
		}
		//GRAND TOTAL
		$page_data.='<tr style="height:25px">
		<td class="text_10b" style="text-align:right; background:#FFFFFF;">Grand Total :</td>
		<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.numberFormat($grandPayAmt,2).'&nbsp;</td>
		<td class="text_10" style="background:#FFFFFF;"></td>
		</tr>';

		// MAKE FINAL DATA
		$page_data_display=$page_data_header.'
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
		<tr>
			<td class="text_b_w" style="width:300px; text-align:center;">Physician</td>
			<td class="text_b_w" style="width:200px; text-align:center;">Payment Amt.</td>
			<td class="text_b_w" style="width:auto;"></td>
		</tr>'.$page_data.'</table>';
		
		$pdf_page_content=$page_data_header.'
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
		<tr>
			<td class="text_b_w" style="width:300px; text-align:center;">Physician</td>
			<td class="text_b_w" style="width:140px; text-align:center;">Payment Amt.</td>
			<td class="text_b_w" style="width:300px;"></td>
		</tr>'.$page_data.'</table>';	
			
	}else{
		foreach($arrGroupData as $facId => $facArr){
			$facTots=0;
			$page_data.='<tr><td class="text_b_w" colspan="6">Facility : '.$arrAllFacilities[$facId].'</td></tr>';
			$pdf_data.='<tr><td class="text_b_w" colspan="5">Facility : '.$arrAllFacilities[$facId].'</td></tr>';
		
			foreach($facArr as $providerId => $providerArr){
				$phyTots=0;
				$page_data.='<tr><td class="text_b_w" colspan="6">Physician : '.$providerNameArr[$providerId].'</td></tr>';
				$pdf_data.='<tr><td class="text_b_w" colspan="5">Physician : '.$providerNameArr[$providerId].'</td></tr>';
		
				foreach($providerArr as $encounter_id){
					$page_part=$pdf_part='';
					$totPaid=0;
					foreach($arrProcData[$encounter_id] as $chgDetId => $chargeDetails){
						$paidAmt=0;
						
						$paidAmt=$arrPayments[$encounter_id][$chgDetId];
						if($chargeDetails['coPayAdjustedAmount']==1){
							$paidAmt+=$arrCopayPayments[$encounter_id];
						}
						$totPaid+=$paidAmt;
						$totPaidAmt+=$paidAmt;
						$page_part.='<tr>
						<td class="text_10" style="background:#FFFFFF;" colspan="3"></td>
						<td class="text_10" style="text-align:center; background:#FFFFFF;">'.$chargeDetails['cpt4_code'].'</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;">'.numberFormat($paidAmt,2).'&nbsp;</td>
						<td class="text_10" style="background:#FFFFFF;"></td>
						</tr>';
						$pdf_part.='<tr>
						<td class="text_10" style="background:#FFFFFF;" colspan="3"></td>
						<td class="text_10" style="text-align:center; background:#FFFFFF;">'.$chargeDetails['cpt4_code'].'</td>
						<td class="text_10" style="text-align:right; background:#FFFFFF;">'.numberFormat($paidAmt,2).'&nbsp;</td>
						</tr>';
					}
					//--- MAKE PATIENT ROW
					$phyTots+=$totPaid;
					$patient_name_arr = array();
					$patient_name_arr["LAST_NAME"] = $chargeDetails['lname'];
					$patient_name_arr["FIRST_NAME"] = $chargeDetails['fname'];
					$patient_name_arr["MIDDLE_NAME"] = $chargeDetails['mname'];		
					$patient_name = changeNameFormat($patient_name_arr).' - '.$chargeDetails['patient_id'];
					$page_data.='
					<tr style="height:25px">
						<td class="text_10 alignLeft white">&nbsp;'.$patient_name.'</td>
						<td class="text_10 white" style="text-align:center;">&nbsp;'.$chargeDetails['date_of_service'].'</td>
						<td class="text_10 white" style="text-align:center;">'.$chargeDetails['encounter_id'].'&nbsp;</td>
						<td class="text_10 white" style=""></td>
						<td class="text_10b white" style="text-align:right;">'.numberFormat($totPaid,2).'&nbsp;</td>
						<td class="text_10 white"></td>
					</tr>'.$page_part;
					$pdf_data.='
					<tr style="height:25px">
						<td class="text_10 alignLeft white">&nbsp;'.$patient_name.'</td>
						<td class="text_10 white" style="text-align:center;">&nbsp;'.$chargeDetails['date_of_service'].'</td>
						<td class="text_10 white" style="text-align:center;">'.$chargeDetails['encounter_id'].'&nbsp;</td>
						<td class="text_10 white" style=""></td>
						<td class="text_10b white" style="text-align:right;">'.numberFormat($totPaid,2).'&nbsp;</td>
					</tr>'.$pdf_part;
		
				}
				//PHYSICIAN TOTALS
				$facTots+=$phyTots;
				$page_data.='<tr><td class="text_10b" style="background:#FFFFFF; text-align:right" colspan="4">Physician Total : </td>
				<td class="text_10b" style="background:#FFFFFF; text-align:right">'.numberFormat($phyTots,2).'&nbsp;</td>
				<td class="text_10" style="background:#FFFFFF;"></td>
				</tr>';				
				$pdf_data.='<tr><td class="text_10b" style="background:#FFFFFF; text-align:right" colspan="4">Physician Total : </td>
				<td class="text_10b" style="background:#FFFFFF; text-align:right">'.numberFormat($phyTots,2).'&nbsp;</td></tr>';				
			}
			//FACILITY TOTALS
			$grandPayAmt+=$facTots;
			$page_data.='<tr><td class="text_10b" style="background:#FFFFFF; text-align:right" colspan="4">Facility Total : </td>
			<td class="text_10b" style="background:#FFFFFF; text-align:right">'.numberFormat($facTots,2).'&nbsp;</td>
			<td class="text_10" style="background:#FFFFFF;"></td>
			</tr>';
			$pdf_data.='<tr><td class="text_10b" style="background:#FFFFFF; text-align:right" colspan="4">Facility Total : </td>
			<td class="text_10b" style="background:#FFFFFF; text-align:right">'.numberFormat($facTots,2).'&nbsp;</td>
			</tr>';				
		}
		//GRAND TOTAL
		$page_data.='<tr style="height:25px">
		<td class="text_10b" style="text-align:right; background:#FFFFFF;" colspan="4">Grand Total :</td>
		<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.numberFormat($grandPayAmt,2).'&nbsp;</td>
		<td class="text_10" style="background:#FFFFFF;"></td>
		</tr>';
		$pdf_data.='<tr style="height:25px">
		<td class="text_10b" style="text-align:right; background:#FFFFFF;" colspan="4">Grand Total :</td>
		<td class="text_10b" style="text-align:right; background:#FFFFFF;">'.numberFormat($grandPayAmt,2).'&nbsp;</td>
		</tr>';
		
		//MAKE FINAL DATA
		$page_data_display=$page_data_header.'
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
		<tr>
			<td class="text_b_w" style="width:200px; text-align:center;">Patient Name-ID</td>
			<td class="text_b_w" style="width:200px; text-align:center;">DOS</td>
			<td class="text_b_w" style="width:200px; text-align:center;">Encounter-ID</td>
			<td class="text_b_w" style="width:200px; text-align:center;">Procedure</td>
			<td class="text_b_w" style="width:200px; text-align:center;">Payment Amt.</td>
			<td class="text_b_w" style="width:auto;"></td>
		</tr>'.$page_data.'</table>';
		
		$pdf_page_content=$page_data_header.'
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
		<tr>
			<td class="text_b_w" style="width:200px; text-align:center;">Patient Name-ID</td>
			<td class="text_b_w" style="width:130px; text-align:center;">DOS</td>
			<td class="text_b_w" style="width:130px; text-align:center;">Encounter-ID</td>
			<td class="text_b_w" style="width:130px; text-align:center;">Procedure</td>
			<td class="text_b_w" style="width:140px; text-align:center;">Payment Amt.</td>
		</tr>'.$pdf_data.'</table>';		
	}
	
}

if($printFile == true and $page_data_display != ''){
		$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
		$csvFileData= $styleHTML.$page_data_display;

		$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
		$strHTML = $stylePDF.$pdf_page_content;
		$file_location = write_html($strHTML);
}
if($printFile == true and $page_data_display != ''){
	echo $csvFileData;
} else{
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}
?>