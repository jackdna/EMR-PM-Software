<?php
$arrFacilitySel=array();
$arrDoctorSel=array();
$currency=$GLOBALS['currency'];
$printFile=true;
if(empty($_POST['form_submitted']) === false){
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

	$startDate = getDateFormatDB($Start_date);
	$endDate = getDateFormatDB($End_date);

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($sc_name)<=0 && isPosFacGroupEnabled()){
		$sc_name = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($sc_name)<=0){
			$sc_name[0]='NULL';
		}
	}
	
	$Physician = implode(",",$Physician);
	$credit_physician= (sizeof($crediting_provider)>0) ? implode(',',$crediting_provider) : '';	
	$grp_id = implode(",",$grp_id);
	$sc_name = implode(",",$sc_name);
	
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = ucfirst(trim($op_name_arr[1][0]));
	$op_name .= ucfirst(trim($op_name_arr[0][0]));
	$curDate = date($phpDateFormat.' h:i A');
	
	// GET ALL POS FACILITIES DETAILS
	$qry = "Select facilityPracCode, pos_facility_id from pos_facilityies_tbl";
	$rs=imw_query($qry);
	$posFacilityArr = array();
	$posFacilityArr[0] = 'No Facility';
	while($posQryRes = imw_fetch_array($rs)){
		$pos_facility_id = $posQryRes['pos_facility_id'];
		$posFacilityArr[$pos_facility_id] = $posQryRes['facilityPracCode'];
	}	

	//GET ALL USERS
	$providerNameArr[0] = 'No Provider';
	$rs=imw_query("Select id, fname, mname, lname FROM users ORDER BY lname, fname");	
	while($res=imw_fetch_array($rs)){
		$id  = $res['id'];
		$pro_name_arr = array();
		$pro_name_arr["LAST_NAME"] = $res['lname'];
		$pro_name_arr["FIRST_NAME"] = $res['fname'];
		$pro_name_arr["MIDDLE_NAME"] = $res['mname'];
		$pro_name = changeNameFormat($pro_name_arr);
		$providerNameArr[$id] = $pro_name;
	}

	//---------------------------------------START DATA --------------------------------------------
	//GET RVU VALS FROM POLICIES
	$rs=imw_query("Select work_gpci, bugdet_neu_adj_gpci, practice_expense_gpci, malpractice_gpci, conversion_factor FROM copay_policies");
	$res  =imw_fetch_array($rs);
	$work_gpci  = $res['work_gpci'];
	$bugdet_neu_adj_gpci  = $res['bugdet_neu_adj_gpci'];
	$practice_expense_gpci  = $res['practice_expense_gpci'];
	$malpractice_gpci  = $res['malpractice_gpci'];
	$convFactor	= $res['conversion_factor'];
	unset($rs);

	// GET RVU RECORDS
	$qry="Select rvu.cpt_fee_id, rvu.work_rvu, rvu.pe_rvu, rvu.mp_rvu FROM rvu_records rvu";
	 $rs = imw_query($qry);
	$arrRVUData=array();
	while($res = imw_fetch_array($rs)){
		$cptId = $res['cpt_fee_id'];
		$arrRVUData[$cptId]['work_rvu'] = $res['work_rvu'];
		$arrRVUData[$cptId]['pe_rvu'] = $res['pe_rvu'];
		$arrRVUData[$cptId]['mp_rvu'] = $res['mp_rvu'];
	}
	unset($rs);

	//--- GET POSTED PAYMENT
	$qry = "Select patChg.encounter_id, patChg.facility_id, patChg.primary_provider_id_for_reports as 'primaryProviderId', patChgDet.procCode, cpt_fee_tbl.cpt_prac_code, patChgDet.totalAmount,
	pos_facilityies_tbl.facilityPracCode, patChg.secondaryProviderId    
	FROM patient_charge_list patChg JOIN patient_charge_list_details patChgDet 
	ON patChgDet.charge_list_id = patChg.charge_list_id 
	JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id =  patChgDet.procCode 
	LEFT JOIN users ON users.id = patChg.primary_provider_id_for_reports 
	LEFT JOIN pos_facilityies_tbl ON pos_facilityies_tbl.pos_facility_id = patChg.facility_id 
	WHERE (patChg.date_of_service BETWEEN '$startDate' AND '$endDate') AND patChgDet.del_status='0'";
	if(empty($sc_name) === false){
		$qry .= " AND patChg.facility_id in($sc_name)";
	}
	if(empty($grp_id) === false){
		$qry .= " AND patChg.gro_id in($grp_id)";
	}
	if(empty($Physician) === false){
		$qry .= " AND patChg.primary_provider_id_for_reports in($Physician)";
	}
	if(empty($credit_physician) === false){
		$qry.= " and patChg.secondaryProviderId IN ($credit_physician)";
	}
	if($chksamebillingcredittingproviders==1){
		$qry.= " and patChg.primary_provider_id_for_reports!=patChg.secondaryProviderId";							
	}	
	$qry.=" ORDER BY users.lname, users.fname,";
	if(empty($sc_name) === false){
		$qry.=" pos_facilityies_tbl.facilityPracCode,";
	}
	$qry.=" IF(cpt_fee_tbl.cpt_prac_code REGEXP '^-?[0-9]+$', cpt_fee_tbl.cpt_prac_code, 0) DESC, cpt_fee_tbl.cpt_prac_code DESC";
	
	$rs = imw_query($qry);
	$tempPostedPay=array();
	$arrPostedPay=array();
	while($res = imw_fetch_array($rs)){
		$printFile=true;
		$paidAmt=0;
		$doctor_id = $res['primaryProviderId'];
		$facId = $res['facility_id'];
		$practice_code = $res['cpt_prac_code'];
		$cptCodeId = $res['procCode'];
		$eid = $res['encounter_id'];
		
		if(empty($Physician) === true && empty($credit_physician) === false){
			$doctor_id = $res['secondaryProviderId'];
		}
		
		$work_rvu = $arrRVUData[$cptCodeId]['work_rvu'];
		$pe_rvu = $arrRVUData[$cptCodeId]['pe_rvu'];
		$mp_rvu = $arrRVUData[$cptCodeId]['mp_rvu'];

		if(empty($sc_name) === false){
			$arrResultData[$doctor_id][$facId][$cptCodeId]['RVU_AMT']=$res['rvu_amount'];
			$arrResultData[$doctor_id][$facId][$cptCodeId]['practice_code']=$practice_code;
			$arrResultData[$doctor_id][$facId][$cptCodeId]['work_rvu']=$work_rvu;
			$arrResultData[$doctor_id][$facId][$cptCodeId]['pe_rvu']=$pe_rvu;
			$arrResultData[$doctor_id][$facId][$cptCodeId]['mp_rvu']=$mp_rvu;
			$arrResultData[$doctor_id][$facId][$cptCodeId]['charges']+=$res['totalAmount'];
			$arrResultCount[$doctor_id][$facId][$cptCodeId]['encounters'][$eid]=$eid;
			$arrResultCount[$doctor_id][$facId][$cptCodeId]['units'][]=1;
		}else{
			$arrResultData[$doctor_id][$cptCodeId]['RVU_AMT']=$res['rvu_amount'];
			$arrResultData[$doctor_id][$cptCodeId]['practice_code']=$practice_code;
			$arrResultData[$doctor_id][$cptCodeId]['work_rvu']=$work_rvu;
			$arrResultData[$doctor_id][$cptCodeId]['pe_rvu']=$pe_rvu;
			$arrResultData[$doctor_id][$cptCodeId]['mp_rvu']=$mp_rvu;
			$arrResultData[$doctor_id][$cptCodeId]['charges']+=$res['totalAmount'];
			$arrResultCount[$doctor_id][$cptCodeId]['encounters'][$eid]=$eid;
			$arrResultCount[$doctor_id][$cptCodeId]['units'][]=1;
		}

		$arrPhysicianEnc[$doctor_id][$eid]=$eid;
		$arrGrandEnc[$eid]=$eid;
		
		$arrFetechedCptId[$cptCodeId]=$cptCodeId;
	} 
	unset($rs);
	
	$facilitySelected = $CLSReports->report_display_selected($sc_name, 'practice', 1, $allFacCount);
	$doctorSelected = $CLSReports->report_display_selected($Physician, 'physician', 1, $allPhyCount);
	$selCrPhy = $CLSReports->report_display_selected($credit_physician,'physician',1, $allCrPhyCount);
	$groupSelected = $CLSReports->report_display_selected($grp_id, 'group', 1, $allGrpCount);
	
	if($printFile==true){
		$page_content='';
		$arrPatPayTot=array();
		$content_part= $patient_html='';
		$pageFacTitle = $pdfFacTitle='';
		if(empty($sc_name) === false){
			$pageFacTitle = '<td class="text_b_w" width="160" style="text-align:left;">Facility</td>';
			$pdfFacTitle = '<td class="text_b_w" width="160" style="text-align:left;">Facility</td>';
		}

		if(sizeof($arrResultData)>0){
			$dataExists=true;
			$grandTot=0;
			$arrGrandTot = array();

			if($process=='Detail'){
				$colspan=8;
				$colspan1 = 2;
				$colWidth='110';
				
				//GROUP BY FACILITY
				if(empty($sc_name) === false){
					$colspan=9;
					$colspan1=3;
					$colWidth='90';
					
					foreach($arrResultData as $phyId => $phyData){
						$arrPhyTot = array();
						foreach($phyData as $facId => $facData){
	
							foreach($facData as $cptCodeId => $cptData){
								$tot=0;
								$cptCount = count($arrResultCount[$phyId][$facId][$cptCodeId]['units']);
								$encCount = count($arrResultCount[$phyId][$facId][$cptCodeId]['encounters']);
								
								$rvuAmt = $cptData['work_rvu'] + $cptData['pe_rvu'] + $cptData['mp_rvu'];
								$adj_rvu = round(((($cptData['work_rvu'] * $bugdet_neu_adj_gpci) * $work_gpci) + ($cptData['pe_rvu'] * $practice_expense_gpci) + ($cptData['mp_rvu'] * $malpractice_gpci)), 2);
								$cpt_rvu = round($adj_rvu * $convFactor ,2);
								$provider_rvu = round($cpt_rvu * $cptCount ,2);
								$tot_work_rvu = round($cptData['work_rvu'] * $cptCount, 2);
				
								$arrPhyTot['cpt_count']+= $cptCount;
								$arrPhyTot['charges']+= $cptData['charges'];
								$arrPhyTot['tot_work_rvu']+= $tot_work_rvu;
								$arrPhyTot['adj_rvu']+= $adj_rvu;
								$arrPhyTot['cpt_rvu']+= $cpt_rvu;
								$arrPhyTot['provider_rvu']+= $provider_rvu;

								$tot_work_rvu = ($tot_work_rvu>0) ? number_format($tot_work_rvu,2) : 0;
								$adj_rvu = ($adj_rvu>0) ? number_format($adj_rvu,2) : 0;
								$cpt_rvu = ($cpt_rvu>0) ? number_format($cpt_rvu,2) : 0;
								
								$content_part .= '
								<tr>
									<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;" width="160">&nbsp;'.$providerNameArr[$phyId].'</td>
									<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;" width="160">&nbsp;'.$posFacilityArr[$facId].'</td>
									<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;" width="150">&nbsp;'.$cptData['practice_code'].'</td>
									<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="100">'.$cptCount.'/'.$encCount.'&nbsp;</td>
									<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="'.$colWidth.'">'.$currency.number_format($cptData['charges'],2).'&nbsp;</td>
									<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="'.$colWidth.'">'.$tot_work_rvu.'&nbsp;</td>
									<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="'.$colWidth.'">'.$adj_rvu.'&nbsp;</td>
									<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="'.$colWidth.'">'.$cpt_rvu.'&nbsp;</td>
									<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="'.$colWidth.'">'.$currency.number_format($provider_rvu,2).'&nbsp;</td>
								</tr>';			
							}
						}

						$arrGrandTot['cpt_count']+= $arrPhyTot['cpt_count'];
						$arrGrandTot['charges']+= $arrPhyTot['charges'];
						$arrGrandTot['tot_work_rvu']+= $arrPhyTot['tot_work_rvu'];
						$arrGrandTot['adj_rvu']+= $arrPhyTot['adj_rvu'];
						$arrGrandTot['cpt_rvu']+= $arrPhyTot['cpt_rvu'];
						$arrGrandTot['provider_rvu']+= $arrPhyTot['provider_rvu'];

						$arrPhyTot['tot_work_rvu'] = ($arrPhyTot['tot_work_rvu']>0) ? number_format($arrPhyTot['tot_work_rvu'],2) : 0;
						$arrPhyTot['adj_rvu'] = ($arrPhyTot['adj_rvu']>0) ? number_format($arrPhyTot['adj_rvu'],2) : 0;
						$arrPhyTot['cpt_rvu'] = ($arrPhyTot['cpt_rvu']>0) ? number_format($arrPhyTot['cpt_rvu'],2) : 0;
						
						//PHYSICIAN TOTAL
						$content_part .= '
						<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
						<tr>
							<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$colspan1.'">Phyisician Total :</td>
							<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPhyTot['cpt_count'].'/'.count($arrPhysicianEnc[$phyId]).'&nbsp;</td>
							<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$currency.number_format($arrPhyTot['charges'],2).'&nbsp;</td>
							<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPhyTot['tot_work_rvu'].'&nbsp;</td>
							<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPhyTot['adj_rvu'].'&nbsp;</td>
							<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPhyTot['cpt_rvu'].'&nbsp;</td>
							<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$currency.number_format($arrPhyTot['provider_rvu'],2).'&nbsp;</td>
						</tr>
						<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>';
					}
				}else{
					//WITHOUT FACILITY GROUPING				
					foreach($arrResultData as $phyId => $phyData){
						$arrPhyTot = array();
						foreach($phyData as $cptCodeId => $cptData){

							$tot=0;
							$cptCount = count($arrResultCount[$phyId][$cptCodeId]['units']);
							$encCount = count($arrResultCount[$phyId][$cptCodeId]['encounters']);
							
							$rvuAmt = $cptData['work_rvu'] + $cptData['pe_rvu'] + $cptData['mp_rvu'];
							$adj_rvu = round(((($cptData['work_rvu'] * $bugdet_neu_adj_gpci) * $work_gpci) + ($cptData['pe_rvu'] * $practice_expense_gpci) + ($cptData['mp_rvu'] * $malpractice_gpci)), 2);
							$cpt_rvu = round($adj_rvu * $convFactor ,2);
							$provider_rvu = round($cpt_rvu * $cptCount ,2);
							$tot_work_rvu = round($cptData['work_rvu'] * $cptCount, 2);
			
							$arrPhyTot['cpt_count']+= $cptCount;
							$arrPhyTot['charges']+= $cptData['charges'];
							$arrPhyTot['tot_work_rvu']+= $tot_work_rvu;
							$arrPhyTot['adj_rvu']+= $adj_rvu;
							$arrPhyTot['cpt_rvu']+= $cpt_rvu;
							$arrPhyTot['provider_rvu']+= $provider_rvu;
							
							$tot_work_rvu = ($tot_work_rvu>0) ? number_format($tot_work_rvu,2) : 0;
							$adj_rvu = ($adj_rvu>0) ? number_format($adj_rvu,2) : 0;
							$cpt_rvu = ($cpt_rvu>0) ? number_format($cpt_rvu,2) : 0;
							
							$content_part .= '
							<tr>
								<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;" width="160">&nbsp;'.$providerNameArr[$phyId].'</td>
								<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;" width="160">&nbsp;'.$cptData['practice_code'].'</td>
								<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="100">'.$cptCount.'/'.$encCount.'&nbsp;</td>
								<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="'.$colWidth.'">'.$currency.number_format($cptData['charges'],2).'&nbsp;</td>
								<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="'.$colWidth.'">'.$tot_work_rvu.'&nbsp;</td>
								<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="'.$colWidth.'">'.$adj_rvu.'&nbsp;</td>
								<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="'.$colWidth.'">'.$cpt_rvu.'&nbsp;</td>
								<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="'.$colWidth.'">'.$currency.number_format($provider_rvu,2).'&nbsp;</td>
							</tr>';			
						}
						
						$arrGrandTot['cpt_count']+= $arrPhyTot['cpt_count'];
						$arrGrandTot['charges']+= $arrPhyTot['charges'];
						$arrGrandTot['tot_work_rvu']+= $arrPhyTot['tot_work_rvu'];
						$arrGrandTot['adj_rvu']+= $arrPhyTot['adj_rvu'];
						$arrGrandTot['cpt_rvu']+= $arrPhyTot['cpt_rvu'];
						$arrGrandTot['provider_rvu']+= $arrPhyTot['provider_rvu'];

						$arrPhyTot['tot_work_rvu'] = ($arrPhyTot['tot_work_rvu']>0) ? number_format($arrPhyTot['tot_work_rvu'],2) : 0;
						$arrPhyTot['adj_rvu'] = ($arrPhyTot['adj_rvu']>0) ? number_format($arrPhyTot['adj_rvu'],2) : 0;
						$arrPhyTot['cpt_rvu'] = ($arrPhyTot['cpt_rvu']>0) ? number_format($arrPhyTot['cpt_rvu'],2) : 0;
						
						//PHYSICIAN TOTAL
						$content_part .= '
						<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
						<tr>
							<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$colspan1.'">Phyisician Total :</td>
							<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPhyTot['cpt_count'].'/'.count($arrPhysicianEnc[$phyId]).'&nbsp;</td>
							<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$currency.number_format($arrPhyTot['charges'],2).'&nbsp;</td>
							<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPhyTot['tot_work_rvu'].'&nbsp;</td>
							<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPhyTot['adj_rvu'].'&nbsp;</td>
							<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrPhyTot['cpt_rvu'].'&nbsp;</td>
							<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$currency.number_format($arrPhyTot['provider_rvu'],2).'&nbsp;</td>
						</tr>
						<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>';
					}
				}
								
				$arrGrandTot['tot_work_rvu'] = ($arrGrandTot['tot_work_rvu']>0) ? number_format($arrGrandTot['tot_work_rvu'],2) : 0;
				$arrGrandTot['adj_rvu'] = ($arrGrandTot['adj_rvu']>0) ? number_format($arrGrandTot['adj_rvu'],2) : 0;
				$arrGrandTot['cpt_rvu'] = ($arrGrandTot['cpt_rvu']>0) ? number_format($arrGrandTot['cpt_rvu'],2) : 0;

				// GRAND TOTAL
				$page_content .=' 
				<table class="rpt_table rpt rpt_table-bordered" bgcolor="#FFF3E8" width="1050">'
				.$content_part.'
				<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$colspan1.'">Grand Total :</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrGrandTot['cpt_count'].'/'.count($arrGrandEnc).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$currency.number_format($arrGrandTot['charges'],2).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrGrandTot['tot_work_rvu'].'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrGrandTot['adj_rvu'].'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrGrandTot['cpt_rvu'].'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$currency.number_format($arrGrandTot['provider_rvu'],2).'&nbsp;</td>
				</tr>
				<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
				</table>';
				
				// PAGE HEADERS
				$pageHeaderPDF='<table class="rpt_table rpt rpt_table-bordered" bgcolor="#FFF3E8" width="1050">
				<tr>
					<td class="text_b_w" width="160" style="text-align:left;">Physician</td>
					'.$pdfFacTitle.'
					<td class="text_b_w" width="160" style="text-align:left;">CPT Code</td>
					<td class="text_b_w" width="100" style="text-align:right;">CPT Count&nbsp;</td>
					<td class="text_b_w" width="'.$colWidth.'" style="text-align:right;">Charges&nbsp;</td>
					<td class="text_b_w" width="'.$colWidth.'" style="text-align:right;">Tot. Work RVU&nbsp;</td>
					<td class="text_b_w" width="'.$colWidth.'" style="text-align:right;">Adj. RVU&nbsp;</td>
					<td class="text_b_w" width="'.$colWidth.'" style="text-align:right;">RVU/CPT&nbsp;</td>
					<td class="text_b_w" width="'.$colWidth.'" style="text-align:right;">Provider RVU&nbsp;</td>
				</tr>
				</table>';
				
				$pageHeaderCSV='<table  width="1050" class="rpt_table rpt rpt_table-bordered" bgcolor="#FFF3E8">
				<tr>
					<td class="text_b_w" width="160" style="text-align:left;">Physician</td>
					'.$pageFacTitle.'
					<td class="text_b_w" width="160" style="text-align:left;">CPT Code</td>
					<td class="text_b_w" width="100" style="text-align:right;">CPT Count&nbsp;</td>
					<td class="text_b_w" width="'.$colWidth.'" style="text-align:right;">Charges&nbsp;</td>
					<td class="text_b_w" width="'.$colWidth.'" style="text-align:right;">Tot. Work RVU&nbsp;</td>
					<td class="text_b_w" width="'.$colWidth.'" style="text-align:right;">Adj. RVU&nbsp;</td>
					<td class="text_b_w" width="'.$colWidth.'" style="text-align:right;">RVU/CPT&nbsp;</td>
					<td class="text_b_w" width="'.$colWidth.'" style="text-align:right;">Provider RVU&nbsp;</td>
				</tr>
				</table>';

			}else{
				//SUMMARY
				$arrFacTot=array();
				$colspan=6;
				$colspan1 = 1;
				$lastColWidth='250';

				//GROUP BY FACILITY
				if(empty($sc_name) === false){
					$colspan=7;
					$colspan1=2;
					$lastColWidth='85';
					
					foreach($arrResultData as $phyId => $phyData){
						$arrPhyTot = array();
						$cnt=1;
						foreach($phyData as $facId => $facData){
							$arrFacTot = array();
							
							foreach($facData as $cptCodeId => $cptData){
								$tot=0;
								$cptCount = count($arrResultCount[$phyId][$facId][$cptCodeId]['units']);
								$encCount = count($arrResultCount[$phyId][$facId][$cptCodeId]['encounters']);
								
								$rvuAmt = $cptData['work_rvu'] + $cptData['pe_rvu'] + $cptData['mp_rvu'];
								$adj_rvu = round(((($cptData['work_rvu'] * $bugdet_neu_adj_gpci) * $work_gpci) + ($cptData['pe_rvu'] * $practice_expense_gpci) + ($cptData['mp_rvu'] * $malpractice_gpci)), 2);
								$cpt_rvu = round($adj_rvu * $convFactor ,2);
								$provider_rvu = round($cpt_rvu * $cptCount ,2);
								$tot_work_rvu = round($cptData['work_rvu'] * $cptCount, 2);
				
								$arrPhyTot['cpt_count']+= $cptCount;
								$arrPhyTot['charges']+= $cptData['charges'];
								$arrPhyTot['tot_work_rvu']+= $tot_work_rvu;
								$arrPhyTot['provider_rvu']+= $provider_rvu;
								
								$oldFacility = $facId;
								$arrFacTot['cpt_count']+= $cptCount;
								$arrFacTot['charges']+= $cptData['charges'];
								$arrFacTot['tot_work_rvu']+= $tot_work_rvu;
								$arrFacTot['provider_rvu']+= $provider_rvu;
								$cnt++;
							}

							$arrFacTot['tot_work_rvu'] = ($arrFacTot['tot_work_rvu']>0) ? number_format($arrFacTot['tot_work_rvu'],2) : 0;
	
							$content_part .= '
							<tr>
								<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;" width="160">&nbsp;'.$providerNameArr[$phyId].'</td>
								<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;" width="160">&nbsp;'.$posFacilityArr[$facId].'</td>
								<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="160">'.$currency.number_format($arrFacTot['charges'],2).'&nbsp;</td>
								<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="160">'.$arrFacTot['cpt_count'].'&nbsp;</td>
								<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="160">'.$arrFacTot['tot_work_rvu'].'&nbsp;</td>
								<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="160">'.$currency.number_format($arrFacTot['provider_rvu'],2).'&nbsp;</td>
								<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="'.$lastColWidth.'"></td>
							</tr>';
							
						}
						$arrGrandTot['cpt_count']+= $arrPhyTot['cpt_count'];
						$arrGrandTot['charges']+= $arrPhyTot['charges'];
						$arrGrandTot['tot_work_rvu']+= $arrPhyTot['tot_work_rvu'];
						$arrGrandTot['provider_rvu']+= $arrPhyTot['provider_rvu'];
						
					}
				}else{
					//WITHOUT FACILITY GROUPING
					foreach($arrResultData as $phyId => $phyData){
						$arrPhyTot = array();

						$cnt=1;
						foreach($phyData as $cptCodeId => $cptData){
							
							$tot=0;
							$cptCount = count($arrResultCount[$phyId][$cptCodeId]['units']);
							$encCount = count($arrResultCount[$phyId][$cptCodeId]['encounters']);
							
							$rvuAmt = $cptData['work_rvu'] + $cptData['pe_rvu'] + $cptData['mp_rvu'];
							$adj_rvu = round(((($cptData['work_rvu'] * $bugdet_neu_adj_gpci) * $work_gpci) + ($cptData['pe_rvu'] * $practice_expense_gpci) + ($cptData['mp_rvu'] * $malpractice_gpci)), 2);
							$cpt_rvu = round($adj_rvu * $convFactor ,2);
							$provider_rvu = round($cpt_rvu * $cptCount ,2);
							$tot_work_rvu = round($cptData['work_rvu'] * $cptCount, 2);
			
							$arrPhyTot['cpt_count']+= $cptCount;
							$arrPhyTot['charges']+= $cptData['charges'];
							$arrPhyTot['tot_work_rvu']+= $tot_work_rvu;
							$arrPhyTot['provider_rvu']+= $provider_rvu;
							
							$cnt++;
						}
						
						$arrGrandTot['cpt_count']+= $arrPhyTot['cpt_count'];
						$arrGrandTot['charges']+= $arrPhyTot['charges'];
						$arrGrandTot['tot_work_rvu']+= $arrPhyTot['tot_work_rvu'];
						$arrGrandTot['provider_rvu']+= $arrPhyTot['provider_rvu'];
						
						$arrFacTot['tot_work_rvu'] = ($arrFacTot['tot_work_rvu']>0) ? number_format($arrFacTot['tot_work_rvu'],2) : 0;
						
						//PHYSICIAN TOTAL
						$content_part .= '
						<tr>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:left;" width="160">&nbsp;'.$providerNameArr[$phyId].'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="160">'.$currency.number_format($arrPhyTot['charges'],2).'&nbsp;</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="160">'.$arrPhyTot['cpt_count'].'&nbsp;</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="160">'.$arrPhyTot['tot_work_rvu'].'&nbsp;</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="160">'.$currency.number_format($arrPhyTot['provider_rvu'],2).'&nbsp;</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right;" width="'.$lastColWidth.'"></td>
						</tr>';
					}					
				}
				
				$arrGrandTot['tot_work_rvu'] = ($arrGrandTot['tot_work_rvu']>0) ? number_format($arrGrandTot['tot_work_rvu'],2) : 0;
				
				// GRAND TOTAL
				$page_content .=' 
				<table class="rpt_table rpt rpt_table-bordered" bgcolor="#FFF3E8" width="1050">'
				.$content_part.'
				<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="'.$colspan1.'">Grand Total :</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$currency.number_format($arrGrandTot['charges'],2).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrGrandTot['cpt_count'].'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$arrGrandTot['tot_work_rvu'].'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;">'.$currency.number_format($arrGrandTot['provider_rvu'],2).'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" width="'.$lastColWidth.'"></td>
				</tr>
				<tr><td class="total-row" colspan="'.$colspan.'"></td></tr>
				</table>';	

				
				// HEADERS
				$pageHeaderPDF='<table class="rpt_table rpt rpt_table-bordered" bgcolor="#FFF3E8" width="1050">
				<tr>
					<td class="text_b_w" style="text-align:left;" width="160">Physician</td>
					'.$pdfFacTitle.'
					<td class="text_b_w" style="text-align:right;" width="160">Charges&nbsp;</td>
					<td class="text_b_w" style="text-align:right;" width="160">CPT Count&nbsp;</td>
					<td class="text_b_w" style="text-align:right;" width="160">Tot. Work RVU&nbsp;</td>
					<td class="text_b_w" style="text-align:right;" width="160">Provider RVU&nbsp;</td>
					<td class="text_b_w" style="text-align:right;" width="'.$lastColWidth.'"></td>
				</tr>
				</table>';
				
				$pageHeaderCSV='<table  class="rpt_table rpt rpt_table-bordered" bgcolor="#FFF3E8" width="1050">
				<tr>
					<td class="text_b_w" style="text-align:left;" width="160">Physician</td>
					'.$pageFacTitle.'
					<td class="text_b_w" style="text-align:right;" width="160">Charges&nbsp;</td>
					<td class="text_b_w" style="text-align:right;" width="160">CPT Count&nbsp;</td>
					<td class="text_b_w" style="text-align:right;" width="160">Tot. Work RVU&nbsp;</td>
					<td class="text_b_w" style="text-align:right;" width="160">Provider RVU&nbsp;</td>
					<td class="text_b_w" style="text-align:right;" width="'.$lastColWidth.'"></td>
				</tr>
				</table>';
			}
		}		
		
		if(trim($page_content) != ''){				
			
			//--- PAGE HEADER DATA ---
			$curDate = date(phpDateFormat().' H:i A');
			$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
			$op_name = $op_name_arr[1][0];
			$op_name .= $op_name_arr[0][0];

			$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
			$html_page_content = <<<DATA
				$stylePDF
				<page backtop="14mm" backbottom="10mm">			
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
					<table width="1050" cellpadding="1" cellspacing="1" border="0" bgcolor="#FFF3E8" class="rpt_table rpt rpt_table-bordered rpt_padding">
						<tr class="rpt_headers">
							<td class="rptbx1" align="left" width="350">Provider RVU Report ($process)</td>
							<td class="rptbx2" align="left" width="350">Selected DOS ($Start_date - $End_date)</td>
							<td class="rptbx3" align="left" width="350">Created by $op_name on $curDate</td>
						</tr>
						<tr class="rpt_headers">
							<td class="rptbx1" align="left">Selected Group : $groupSelected</td>
							<td class="rptbx2" align="left">Selected Facility: $facilitySelected</td>
							<td class="rptbx3" align="left">Physician: $doctorSelected&nbsp;&nbsp;&nbsp;&nbsp;Cr. Phy.: $selCrPhy</td>
						</tr>
					</table>
					$pageHeaderPDF
				</page_header>
				$page_content
				</page>
DATA;
			//--- CREATE HTML FILE FOR PDF PRINTING ---
			/* if($callFrom != 'scheduled'){
			$html_file_name = get_pdf_name($_SESSION['authId'],'rvu_result');
			file_put_contents("new_html2pdf/$html_file_name.html",$html_page_content);
			} */
			
			//--- CSV FILE DATA --
			$page_content = <<<DATA
				<table width="1050" cellpadding="1" cellspacing="1" border="0" bgcolor="#FFF3E8" class="rpt_table rpt rpt_table-bordered rpt_padding">
					<tr class="rpt_headers">
						<td class="rptbx1" align="left" width="350">Provider RVU Report ($process)</td>
						<td class="rptbx2" align="left" width="350">Selected DOS ($Start_date - $End_date)</td>
						<td class="rptbx3" align="left" width="350">Created by $op_name on $curDate</td>
					</tr>
					<tr class="rpt_headers">
						<td class="rptbx1" align="left">Selected Group : $groupSelected</td>
						<td class="rptbx2" align="left">Selected Facility: $facilitySelected</td>
						<td class="rptbx3" align="left">Physician: $doctorSelected&nbsp;&nbsp;&nbsp;&nbsp;Cr. Phy.: $selCrPhy</td>
					</tr>
				</table>
				$pageHeaderCSV			
				$page_content
DATA;
		}
	}
}	

if($printFile == 1){
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$page_content;
	if($callFrom != 'scheduled'){
		$file_location = write_html($html_page_content);
	}
}else{
	$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
}
echo $csv_file_data;
?>