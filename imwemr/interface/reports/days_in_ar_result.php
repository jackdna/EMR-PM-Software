<?php
$arrFacilitySel=array();
$arrDoctorSel=array();
$arrInsSel=array();

$printFile = true;
$hasData = 0;
$dateFormat= get_sql_date_format();

if( $_POST['form_submitted'] ){
	$printFile = false;
	//DAYS IN LAST 6 MONTHS
	$startDate = date('Y-m-d', mktime(0,0,0, date('m') - 6, date('d'), date('Y')));
	$endDate  = date('Y-m-d');

	$dd = explode('-', $startDate);
	$Start_date = $dd[1].'-'.$dd[2].'-'.$dd[0];
	$End_date =  date(phpDateFormat());
	//---------------------

	//GET ALL USERS
	$providerNameArr[0] = 'No Provider';
	$rs=imw_query("Select id, fname, mname, lname FROM users");	
	while($res=imw_fetch_array($rs)){
		$id  = $res['id'];
		$pro_name_arr = array();
		$pro_name_arr["LAST_NAME"] = $res['lname'];
		$pro_name_arr["FIRST_NAME"] = $res['fname'];
		$pro_name_arr["MIDDLE_NAME"] = $res['mname'];
		$pro_name = changeNameFormat($pro_name_arr);
		$providerNameArr[$id] = $pro_name;
	}
	
	//--- GET GROUP NAME ---
	$arrAllGroups = array();
	$arrAllGroups[0] = 'No Group';
	$group_query = imw_query("Select gro_id, name from groups_new");
	while($groupQryRes = imw_fetch_array($group_query)){
		$group_name = $groupQryRes['name'];
		$arrAllGroups[$groupQryRes['gro_id']]=$groupQryRes['name'];
	}

	// -- GET ALL POS-FACILITIES
	$arrAllFacilities=array();
	$arrAllFacilities[0] = 'No Facility';
	$qry = "select pos_facilityies_tbl.facilityPracCode as name,
		pos_facilityies_tbl.pos_facility_id as id,
		pos_tbl.pos_prac_code
		from pos_facilityies_tbl
		left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
		order by pos_facilityies_tbl.headquarter desc,
		pos_facilityies_tbl.facilityPracCode";
	$qryRs = imw_query($qry);
	while($qryRes  =imw_fetch_assoc($qryRs)){
		$id = $qryRes['id'];
		$name = $qryRes['name'];
		$pos_prac_code = $qryRes['pos_prac_code'];
		$arrAllFacilities[$id] = $name.' - '.$pos_prac_code;
	}							

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_name)<=0 && isPosFacGroupEnabled()){
		$facility_name = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_name)<=0){
			$facility_name[0]='NULL';
		}
	}

	$grp_id=$sc_name=$Physician=$insuranceName='';
	$arrFacilitySel=$facility_name;
	$arrDoctorSel=$phyId;
	$arrInsSel=$ins_carriers;
	if(sizeof($groups)>0){ $grp_id = implode(',', $groups); }
	if(sizeof($facility_name)>0){ $sc_name = implode(',', $facility_name); }
	if(sizeof($phyId)>0){ $Physician = implode(',', $phyId); }
	if(sizeof($ins_carriers)>0){ $insuranceName = implode(',', $ins_carriers); }

	//---------------------------------------START DATA --------------------------------------------
	$arrResultData=array();
	// LAST 6 MONTH CHARGES
	$qry = "Select patChg.patient_id, patChg.encounter_id, patChg.primary_provider_id_for_reports as 'primaryProviderId', patChgDet.totalAmount, 
	DATEDIFF(NOW(),patChg.date_of_service) as dos_aging, patChg.gro_id, patChg.facility_id, DATE_FORMAT(patChg.date_of_service, '$dateFormat') as 'date_of_service',
	patChgDet.pri_due, patChgDet.sec_due, patChgDet.tri_due, patChgDet.pat_due, pd.fname, pd.mname, pd.lname 
	FROM patient_charge_list patChg 
	JOIN patient_charge_list_details patChgDet ON patChgDet.charge_list_id = patChg.charge_list_id 
	JOIN patient_data pd ON pd.id = patChg.patient_id 
	LEFT JOIN users ON users.id = patChg.primary_provider_id_for_reports 
	WHERE patChg.del_status='0' AND (patChg.date_of_service BETWEEN '$startDate' AND '$endDate') 
	AND patChg.totalAmt>0";
	if(empty($grp_id) === false){
		$qry .= " AND patChg.gro_id in($grp_id)";
	}
	if(empty($Physician) === false){
		$qry .= " AND patChg.primary_provider_id_for_reports in($Physician)";
	}
	if(empty($sc_name) === false){
		$qry .= " AND patChg.facility_id in($sc_name)";
	}
	if(trim($insuranceName) != ''){			
		$qry .= " AND (patChg.primaryInsuranceCoId in($insuranceName) 
			OR patChg.secondaryInsuranceCoId in($insuranceName) 
			OR patChg.tertiaryInsuranceCoId in($insuranceName))";
	}
	$qry.=" ORDER BY users.lname, users.lname";
	$rs = imw_query($qry);
	$arrAllEncs = array();
	$tempArrDOSAging=array();
	while($res = imw_fetch_array($rs)){
		$printFile=true;
		$paidAmt=0;
		$eid = $res['encounter_id'];
		$groupId = $res['gro_id'];
		$phyId = $res['primaryProviderId'];
		$facId = $res['facility_id'];
		$balance = $res['pri_due'] + $res['sec_due'] + $res['tri_due'] + $res['pat_due'];
		
		$firstGroupBy = $phyId;
		if($grpby_block=='grpby_groups'){
			$firstGroupBy = $groupId;
		}else if($grpby_block=='grpby_facility'){
			$firstGroupBy = $facId;
		}
		
		$arrResultData[$firstGroupBy]['charges']+= $res['totalAmount'];
		$arrResultData[$firstGroupBy]['balance']+= $balance;

		//FOR DETAIL VIEW
		$arrResultDataDetail[$firstGroupBy][$eid]['pat_name']=$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
		$arrResultDataDetail[$firstGroupBy][$eid]['pat_id']= $res['patient_id'];
		$arrResultDataDetail[$firstGroupBy][$eid]['dos']= $res['date_of_service'];
		$arrResultDataDetail[$firstGroupBy][$eid]['charges']+= $res['totalAmount'];
		$arrResultDataDetail[$firstGroupBy][$eid]['balance']+= $balance;


		$grandCharges+= $res['totalAmount'];
		$grandBalance+= $balance;
		
		$arrAllEncs[$eid] = $eid;
		$arrEncProvider[$eid] = $phyId;
		
		$tempArrDOSAging[]=$res['dos_aging'];
	} 
	unset($rs);
	
	// LAST 6 MONTHS PAYMENTS of DOS ENCOUNTERS
/*	if(sizeof($arrAllEncs)>0){
		$splitted_encounters = array_chunk($arrAllEncs,5000);
		foreach($splitted_encounters as $arr){
			$str_splitted_encs 	 = implode(',',$arr);
			
			$qry="Select payChg.encounter_id, payChg.paymentClaims, payChgDet.paidForProc, payChgDet.overPayment 
			FROM patient_chargesheet_payment_info payChg 
			JOIN patient_charges_detail_payment_info payChgDet ON payChgDet.payment_id = payChg.payment_id 
			WHERE payChg.encounter_id IN(".$str_splitted_encs.")";
		
			$rs = mysql_query($qry);
			while($res = mysql_fetch_array($rs)){
				$printFile=true;
				$firstGroupBy = $arrEncProvider[$res['encounter_id']];
			
				$paidAmt = $res['paidForProc'] + $res['overPayment'];
				if($res['paymentClaims'] == 'Negative Payment'){
					$paidAmt= '-'.$paidAmt;
				}
	
				$arrResultData[$firstGroupBy]['payments']+= $paidAmt;
				$grandPayments+= $paidAmt;
			}
		}
	}
	unset($arrAllEncs);
	unset($splitted_encounters);*/

	if($printFile==true){
		$page_content='';

		//--- PAGE HEADER DATA ---
		$curDate = date(''.phpDateFormat().' H:i A');
		$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$op_name = $op_name_arr[1][0];
		$op_name .= $op_name_arr[0][0];
		
		$group_name = $CLSReports->report_display_selected($grp_id,'group',1, $allGrpCount);		
		$doctorSelected = $CLSReports->report_display_selected($Physician,'physician',1, $allPhyCount);
		$facilitySelected = $CLSReports->report_display_selected($sc_name,'practice',1, $allFacCount);	
		$insSelected = $CLSReports->report_display_selected($insuranceName,'insurance',1, $insurance_cnt);

		
		//GETTING BIGGEST DOS AGING
		rsort($tempArrDOSAging);
		$totDays=$tempArrDOSAging[0];

		$firstGroupTitle='Physician';
		if($grpby_block=='grpby_groups'){
			$firstGroupTitle='Group';
		}else if($grpby_block=='grpby_facility'){
			$firstGroupTitle='Facility';
		}			

		if($summary_detail=='summary'){
			//MAKE CONTENT DATA
			foreach($arrResultData as $firstGrpId => $firstGrpData){

				$firstGroupName = $providerNameArr[$firstGrpId];
				if($grpby_block=='grpby_groups'){
					$firstGroupName = $arrAllGroups[$firstGrpId];
				}else if($grpby_block=='grpby_facility'){
					$firstGroupName = $arrAllFacilities[$firstGrpId];
				}					
					
				$totCharges+= $firstGrpData['charges'];
				$totBalance+= $firstGrpData['balance'];
				
				$avgDailyChg = 	round($firstGrpData['charges'] / $totDays);
				$daysInAR 	= 	round($firstGrpData['balance'] / $avgDailyChg);
				$GlobalCurrency = showcurrency();
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:24%">&nbsp;'.$firstGroupName.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:19%">'.$CLSReports->numberFormat($firstGrpData['charges'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:19%">'.$CLSReports->numberFormat($firstGrpData['balance'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#e9f1f5" style="text-align:right; width:19%;">'.$CLSReports->numberFormat($avgDailyChg,2).'&nbsp;</td>
					<td class="text_10" bgcolor="#e9f1f5" style="text-align:right; width:19%;">'.$daysInAR.'&nbsp;</td>
				</tr>';	
			}
			
			// TOTAL
			$grandAvgChg = round($grandCharges / $totDays);
			$grandARDays = round($grandBalance / $grandAvgChg);

			$page_content .=' 
			<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
			<tr>
				<td class="text_b_w" style="text-align:left;">&nbsp;'.$firstGroupTitle.'</td>
				<td class="text_b_w" style="text-align:right;">Charges&nbsp;</td>
				<td class="text_b_w" style="text-align:right;">Total A/R&nbsp;</td>
				<td class="text_b_w" style="text-align:right;">Avg. Daily Charges&nbsp;</td>
				<td class="text_b_w" style="text-align:right;">Days in A/R&nbsp;</td>
			</tr>'
			.$content_part.'
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">Total : </td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totCharges,2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totBalance,2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="2"></td>
			</tr>
			<tr><td class="text_10" bgcolor="#FFFFFF" style="height:20px" colspan="5"></td></tr>
			<tr style="height:25px">
				<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;">Total Average Daily Charges : </td>
				<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;">'.$CLSReports->numberFormat($grandAvgChg,2).'&nbsp;</td>
				<td class="text_10" bgcolor="#e9f1f5" style="text-align:right;" colspan="3"></td>
			</tr>
			<tr style="height:25px">
				<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;">Days In A/R : </td>
				<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;">'.$grandARDays.'&nbsp;</td>
				<td class="text_10" bgcolor="#e9f1f5" style="text-align:right;" colspan="3"></td>
			</tr>
			</table>';	
			
			//PDF
			$pdfHeader='<table style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8">
			<tr>
				<td class="text_b_w" style="text-align:left; width:24%">&nbsp;Physician</td>
				<td class="text_b_w" style="text-align:right; width:19%">Charges&nbsp;</td>
				<td class="text_b_w" style="text-align:right; width:19%">Total A/R&nbsp;</td>
				<td class="text_b_w" style="text-align:right; width:19%">Avg. Daily Charges&nbsp;</td>
				<td class="text_b_w" style="text-align:right; width:19%">Days in A/R&nbsp;</td>
			</tr>
			</table>';
			$pdf_content .='<br /> 
			<table style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8">'
			.$content_part.'
			<tr>
				<td class="text_b_w" bgcolor="#FFFFFF" style="text-align:right;">Total : </td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totCharges,2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totBalance,2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="2"></td>
			</tr>
			<tr><td class="text_10" bgcolor="#FFFFFF" style="height:20px" colspan="5"></td></tr>
			<tr style="height:25px">
				<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;">Total Average Daily Charges : </td>
				<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;">'.$CLSReports->numberFormat($grandAvgChg,2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="3"></td>
			</tr>
			<tr style="height:25px">
				<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;">Days In A/R : </td>
				<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;">'.$grandARDays.'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" colspan="3"></td>
			</tr>
			</table>';
			// END CONTENT DATA	
			$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
			$html_page_content =
				$stylePDF.'
				<page backtop="19mm" backbottom="10mm">			
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
					<table class="rpt_table rpt rpt_table-bordered rpt_padding">
						<tr>
							<td class="rptbx1" style="width:320px;">Days in A/R Report (Summary)</td>
							<td class="rptbx2" style="width:400px;">DOS ('.$Start_date.' - '.$End_date.')</td>
							<td class="rptbx3" style="width:320px;">Created by: '.$op_name.' on '.$curDate.'</td>
						</tr>	
						<tr>
							<td class="rptbx1">Selected Group: '.$group_name.'</td>
							<td class="rptbx2">Selected Insurance: '.$insSelected.'</td>
							<td class="rptbx3"></td>							
						</tr>							
						<tr>
							<td class="rptbx1">Selected Facility : '.$facilitySelected.'</td>
							<td class="rptbx2">Selected Physician : '.$doctorSelected.'</td>
							<td class="rptbx3"></td>
						</tr>	
					</table>
					'.$pdfHeader.'
				</page_header>
				'.$pdf_content.'
				</page>';
	
			$file_location = write_html($html_page_content, 'days_in_ar.html');	
			//--- CSV FILE DATA --
			$styleHTML = '<style>'.file_get_contents('css/reports_html.css').'</style>';
			$page_content =
					$styleHTML.'
					<table class="rpt_table rpt rpt_table-bordered rpt_padding">
						<tr>
							<td class="rptbx1" style="width:33%">Days in A/R Report (Summary)</td>
							<td class="rptbx2" style="width:33%">DOS ('.$Start_date.' - '.$End_date.')</td>
							<td class="rptbx3" style="width:33%">Created by: '.$op_name.' on '.$curDate.'</td>
						</tr>	
						<tr>
							<td class="rptbx1" style="width:33%">Selected Group: '.$group_name.'</td>
							<td class="rptbx2" style="width:33%">Selected Insurance: '.$insSelected.'</td>							
							<td class="rptbx3" style="width:33%"></td>							
						</tr>							
						<tr>
							<td class="rptbx1" style="width:33%">Selected Facility : '.$facilitySelected.'</td>
							<td class="rptbx2" style="width:33%">Selected Physician : '.$doctorSelected.'</td>
							<td class="rptbx3" style="width:33%"></td>
						</tr>	
					</table>
				'.$page_content;

			$hasData = 1;
		}else{
			//DETAIL VIEW
			foreach($arrResultDataDetail as $firstGrpId => $firstGrpData){

				$firstGroupName = $providerNameArr[$firstGrpId];
				if($grpby_block=='grpby_groups'){
					$firstGroupName = $arrAllGroups[$firstGrpId];
				}else if($grpby_block=='grpby_facility'){
					$firstGroupName = $arrAllFacilities[$firstGrpId];
				}				
				
				$totCharges+= $arrResultData[$firstGrpId]['charges'];
				$totBalance+= $arrResultData[$firstGrpId]['balance'];
				
				$avgDailyChg = 	round($arrResultData[$firstGrpId]['charges'] / $totDays);
				$daysInAR 	= 	round($arrResultData[$firstGrpId]['balance'] / $avgDailyChg);
				
				$content_part .= '
				<tr>
					<td class="text_b_w" style="text-align:center; background-color:#878787; color:#fff" colspan="2">'.$firstGroupTitle.'</td>
					<td class="text_b_w" style="text-align:center; background-color:#878787; color:#fff">Total Charges</td>
					<td class="text_b_w" style="text-align:center; background-color:#878787; color:#fff">Total A/R</td>
					<td class="text_b_w" style="text-align:center; background-color:#878787; color:#fff">Avg. Daily Charges</td>
					<td class="text_b_w" style="text-align:center; background-color:#878787; color:#fff">Days in A/R</td>
				</tr>				
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;" colspan="2">&nbsp;'.$firstGroupName.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrResultData[$firstGrpId]['charges'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($arrResultData[$firstGrpId]['balance'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;;">'.$CLSReports->numberFormat($avgDailyChg,2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;">'.$daysInAR.'&nbsp;</td>
				</tr>
				<tr>
					<td class="text_b_w" style="text-align:center;">Patient Name</td>
					<td class="text_b_w" style="text-align:center;">Encounter ID</td>
					<td class="text_b_w" style="text-align:center;">DOS</td>
					<td class="text_b_w" style="text-align:center;">Charges</td>
					<td class="text_b_w" style="text-align:center;">Balance</td>
					<td class="text_b_w" style="text-align:center;">Day in A/R</td>
				</tr>';	
				
				foreach($firstGrpData as $encid => $detailData){
						
					$arr=explode('~', $detailData['pat_name']);
					$patient_name = core_name_format($arr[2], $arr[0], $arr[1]);
					
					$avgDailyChg_det = 	round($detailData['charges'] / $totDays);
					$daysInAR_det 	= 	round($detailData['balance'] / $avgDailyChg_det);
					
					$content_part .= '
					<tr>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:15%">&nbsp;'.$patient_name.' - '.$detailData['pat_id'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:15%">&nbsp;'.$encid.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:15%">&nbsp;'.$detailData['dos'].'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:20%">'.$CLSReports->numberFormat($detailData['charges'],2).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:20%">'.$CLSReports->numberFormat($detailData['balance'],2).'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:15%;">'.$daysInAR_det.'&nbsp;</td>
					</tr>';	
				}
			}	

			// TOTAL
			$grandAvgChg = round($totCharges / $totDays);
			$grandARDays = round($totBalance / $grandAvgChg);
			
			//--- CSV FILE DATA --
			$styleHTML = '<style>'.file_get_contents('css/reports_html.css').'</style>';
			$page_content =
			$styleHTML.'
			<table class="rpt_table rpt rpt_table-bordered rpt_padding">
				<tr>
					<td class="rptbx1" style="width:33%">Days in A/R Report (Detail)</td>
					<td class="rptbx2" style="width:33%">DOS ('.$Start_date.' - '.$End_date.')</td>
					<td class="rptbx3" style="width:33%">Created by: '.$op_name.' on '.$curDate.'</td>
				</tr>	
				<tr>
					<td class="rptbx1" style="width:33%">Selected Group: '.$group_name.'</td>
					<td class="rptbx2" style="width:33%">Selected Insurance: '.$insSelected.'</td>							
					<td class="rptbx3" style="width:33%"></td>							
				</tr>							
				<tr>
					<td class="rptbx1" style="width:33%">Selected Facility : '.$facilitySelected.'</td>
					<td class="rptbx2" style="width:33%">Selected Physician : '.$doctorSelected.'</td>
					<td class="rptbx3" style="width:33%"></td>
				</tr>	
			</table>
			<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
			'.$content_part.'
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Total : </td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totCharges,2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totBalance,2).'&nbsp;</td>
				<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
			</tr>
			<tr><td class="text_10" bgcolor="#FFFFFF" style="height:20px" colspan="6"></td></tr>
			<tr style="height:25px">
				<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;" colspan="2">Total Average Daily Charges : </td>
				<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;">'.$CLSReports->numberFormat($grandAvgChg,2).'&nbsp;</td>
				<td class="text_10" bgcolor="#e9f1f5" style="text-align:right;" colspan="3"></td>
			</tr>
			<tr style="height:25px">
				<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;" colspan="2">Days In A/R : </td>
				<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;">'.$grandARDays.'&nbsp;</td>
				<td class="text_10" bgcolor="#e9f1f5" style="text-align:right;" colspan="3"></td>
			</tr>			
			</table>';	


			
			$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
			$html_page_content =
				$stylePDF.'
				<page backtop="14mm" backbottom="10mm">			
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
					<table class="rpt_table rpt rpt_table-bordered rpt_padding">
						<tr>
							<td class="rptbx1" style="width:320px;">Days in A/R Report (Detail)</td>
							<td class="rptbx2" style="width:400px;">DOS ('.$Start_date.' - '.$End_date.')</td>
							<td class="rptbx3" style="width:320px;">Created by: '.$op_name.' on '.$curDate.'</td>
						</tr>	
						<tr>
							<td class="rptbx1">Selected Group: '.$group_name.'</td>
							<td class="rptbx2">Selected Insurance: '.$insSelected.'</td>
							<td class="rptbx3"></td>							
						</tr>							
						<tr>
							<td class="rptbx1">Selected Facility : '.$facilitySelected.'</td>
							<td class="rptbx2">Selected Physician : '.$doctorSelected.'</td>
							<td class="rptbx3"></td>
						</tr>	
					</table>
				</page_header>
				<table class="rpt_table rpt rpt_table-bordered" style="width:100%">
				'.$content_part.'
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Total : </td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totCharges,2).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$CLSReports->numberFormat($totBalance,2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;"></td>
				</tr>
				<tr><td class="text_10" bgcolor="#FFFFFF" style="height:20px" colspan="6"></td></tr>
				<tr style="height:25px">
					<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;" colspan="2">Total Average Daily Charges : </td>
					<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;">'.$CLSReports->numberFormat($grandAvgChg,2).'&nbsp;</td>
					<td class="text_10" bgcolor="#e9f1f5" style="text-align:right;" colspan="3"></td>
				</tr>
				<tr style="height:25px">
					<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;" colspan="2">Days In A/R : </td>
					<td class="text_10b" bgcolor="#e9f1f5" style="text-align:right;">'.$grandARDays.'&nbsp;</td>
					<td class="text_10" bgcolor="#e9f1f5" style="text-align:right;" colspan="3"></td>
				</tr>					
				</table>
				</page>';
				
				$file_location = write_html($html_page_content, 'days_in_ar.html');	
			
			$hasData = 1;
		}			
	}
}

$op='l';
if($callFrom!='scheduled'){
	if($page_content){
		if($output_option=='view' || $output_option=='output_csv'){
			echo $page_content;
		}elseif($output_option=='output_pdf'){
			echo '<div class="text-center alert alert-info">PDF generated in separate window.</div>';
		}
	}else {
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}
?>