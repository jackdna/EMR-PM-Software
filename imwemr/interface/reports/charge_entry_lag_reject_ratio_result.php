<?php
if($_POST['form_submitted']){
	$print = false;
	$op_name_arr = explode(', ',strtoupper($_SESSION['authProviderName']));
	$createdBy = ucfirst(trim($op_name_arr[1][0]));
	$createdBy .= ucfirst(trim($op_name_arr[0][0]));

	$startDate = date('Y-m-01'); // hard-coded '01' for first day
	$endDate  = date('Y-m-t'); // Month last date
	$previous_day_date = date('Y-m-d', strtotime(' -1 day')); // get previous date from current date  

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility)<=0 && isPosFacGroupEnabled()){
		$facility = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility)<=0){
			$facility[0]='000001';
		}
	}
	
	$groupSelected = $CLSReports->report_display_selected($groups,'group',1, $allGrpCount);
	$facilitySelected = $CLSReports->report_display_selected($facility,'practice',1, $allFacOptions);
	$doctorSelected = $CLSReports->report_display_selected($operator_id,'operator',1, $allOprOptions);
	
	$selFac = implode("," ,$facility);
	$selOpe = implode("," ,$_REQUEST["operator_id"]);	
	$selGro = implode("," ,$_REQUEST["groups"]);	
	
	$qry = "Select main.report_enc_detail_id, main.encounter_id, main.charge_list_detail_id, (main.charges * main.units) as totalAmt, main.units, main.date_of_service, main.first_posted_date, main.facility_id, main.billing_facility_id, main.primary_provider_id_for_reports  as 'primaryProviderId', main.gro_id, main.operator_id, users.lname, users.fname  
	FROM report_enc_detail main
	LEFT JOIN users ON users.id = main.operator_id
	LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id 	
	WHERE (first_posted_date between '$startDate' AND '$endDate') AND del_status='0'";
	if(empty($selOpe) === false){
		$qry.= " AND main.operator_id IN ($selOpe)";
	}
	if(empty($selFac)===false){
		$qry.=" AND facility_id IN (".$selFac.")";						
	}
	if(empty($selGro) === false){
		$qry .= " AND gro_id IN (".$selGro.")";
	}
	$qry.= " ORDER BY users.lname, users.fname, pos_facilityies_tbl.facilityPracCode";
	$res=imw_query($qry);
	$arrResultData = array();
	$arrResultPrevData = array();
	$arrayEnc = array();
	while($rs = imw_fetch_assoc($res)){
		$phyId = $rs['primaryProviderId'];
		$opeId = $rs['operator_id'];
		$facId = $rs['facility_id'];
		$grpId = $rs['gro_id'];
		$first_posted_date = $rs['first_posted_date'];
		$date_of_service = $rs['date_of_service'];
		$encId = $rs['encounter_id'];
		$ChareListId = $rs['charge_list_detail_id'];
		$arrayEnc[$encId] = $encId;
		$arrayChareListIds[$ChareListId] = $ChareListId;
		$arrResultData[$opeId][$facId][$grpId][] = $rs;
		if($first_posted_date == $previous_day_date){
			$arrResultPrevData[$opeId][$facId][$grpId][$ChareListId] = $rs;
		}
	}
	unset($rs);
	$ChareListIds = array_keys($arrayChareListIds);
	$ChareListIds = implode(',', $ChareListIds);	
	
	$encounterIds = array_keys($arrayEnc);
	$encounterIds = implode(',', $encounterIds);

	// GET Last DOS Charge Posted
	$deniedQry = "SELECT charge_list_detail_id, deniedAmount, deniedDate FROM deniedpayment WHERE charge_list_detail_id in ($ChareListIds) and denialDelStatus = '0' AND deniedById >0 ORDER BY deniedDate DESC";
	$deniedRs = imw_query($deniedQry);
	while($deniedRes = imw_fetch_assoc($deniedRs)){
		$arrDeniedAmount[$deniedRes['charge_list_detail_id']]=$deniedRes;
		$arrResUnitSumm[$deniedRes['charge_list_detail_id']]['count'] = 0; 
		if($deniedRes['deniedDate'] == $previous_day_date){
			$arrPreviousDeniedAmount[$deniedRes['charge_list_detail_id']]=$deniedRes;
			$arrResPrevUnitSumm[$deniedRes['charge_list_detail_id']]['count'] = 0; 
		}
	}
	imw_free_result($dateRs);
	
	// GET Denied payment
	$dateQry = "Select date_of_service, encounter_id FROM report_enc_detail where (first_posted_date between '$startDate' and '$endDate') AND encounter_id in ($encounterIds) ORDER BY date_of_service DESC";
	$dateRs = imw_query($dateQry);
	while($dateRes = imw_fetch_array($dateRs)){
		$arrTempDates[$dateRes['encounter_id']]=$dateRes['date_of_service'];
	}
	imw_free_result($dateRs);
	
	$strHTML = '<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050">
				<tr>	
					<td class="rptbx1" width="350">Charge Entry Lag - Reject Ratio</td>
					<td class="rptbx2" width="350"></td>
					<td class="rptbx3" width="350">Created by '.strtoupper($createdBy).' on '.get_date_format(date("Y-m-d"))." ".date("h:i A").'&nbsp;</td>
				</tr>
				<tr>	
					<td class="rptbx1" width="350">Selected Group: '.$groupSelected.'</td>
					<td class="rptbx2" width="350">Selected Physician: '.$doctorSelected.'</td>
					<td class="rptbx3" width="350">Selected Facility: '.$facilitySelected.'</td>
				</tr>
			</table>';	
	$strHTML .= '<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050" bgcolor="#FFF3E8">
			<tr>
				<td class="text_b_w" style="text-align:center;" width="80">User</td>
				<td class="text_b_w" style="text-align:center;" width="80">Location</td>
				<td class="text_b_w" style="text-align:center;" width="80">Billing Entiry</td>
				<td class="text_b_w" style="text-align:center;" width="80">Total Charges MTD (Dollar)</td>
				<td class="text_b_w" style="text-align:center;" width="70">Total Charge MTD (Count)</td>
				<td class="text_b_w" style="text-align:center;" width="150">Charges Entered for Previous Day Total (Dollar) </td>
				<td class="text_b_w" style="text-align:center;" width="80">Charges Entered for Previous Day (Count) </td>
				<td class="text_b_w" style="text-align:center;" width="80">Last DOS Charge Posted</td>
				<td class="text_b_w" style="text-align:center;" width="70">Total Rejections MTD (Count) </td>
				<td class="text_b_w" style="text-align:center;" width="80">Total Rejections MTD (Dollar) </td>
				<td class="text_b_w" style="text-align:center;" width="80">Total Rejections for Previous Day (Count) </td>
				<td class="text_b_w" style="text-align:center;" width="80">Total Rejections for Previous Day (Dollar) </td>
			</tr>';
	
	//pre($arrResultPrevData);
	foreach($arrResultData as $opeId => $arrVal){
		$phyName = $providerNameArr[$opeId];
		foreach($arrVal as $facId => $facval){
			$facName = $facNameArr[$facId];
			$totalCharges = $totalUnits = "";
			foreach($facval as $grpId => $grpval){
			$groupName = $group_id_arr[$grpId];
			$print = true; $hasData = 1; $totUnitCount = array(); $totUnitPrevCount = array();
				foreach($grpval as $grpData){
					$ChareListId = $grpData['charge_list_detail_id'];
					$totalCharges[$ChareListId] = $grpData['totalAmt'];
					$totalUnits[$ChareListId] = $grpData['units'];
				
					$lastDOS = $arrTempDates[$grpData['encounter_id']];
					
					$previousDayCharges[$ChareListId] = $arrResultPrevData[$opeId][$facId][$grpId][$ChareListId]['totalAmt'];
					$previousDayUnits[$ChareListId] =   $arrResultPrevData[$opeId][$facId][$grpId][$ChareListId]['units'];
					
					$resDetail = $arrResUnitSumm[$ChareListId];
					if($resDetail){
						$totUnitCount[$ChareListId][] = $resDetail['count'];
						$deniedAmount[] = $arrDeniedAmount[$ChareListId]['deniedAmount'];
					}	
					$resPreDetail = $arrResPrevUnitSumm[$ChareListId];
					if($resPreDetail){
						$totUnitPrevCount[$ChareListId][]=$resPreDetail['count'];
						$deniedAmountPrev[] = $arrPreviousDeniedAmount[$ChareListId]['deniedAmount'];
					}
				}
				$totalCharges = array_sum($totalCharges);
				$totalUnits = array_sum($totalUnits);
				$previousDayCharges = array_sum($previousDayCharges);
				$previousDayUnits = array_sum($previousDayUnits);
				$deniedAmount = array_sum($deniedAmount);
				$deniedAmountPrev = array_sum($deniedAmountPrev);
				$totUnitCount = count($totUnitCount);
				$totUnitPrevCount = count($totUnitPrevCount);
				
				$strHTML .= '<tr valign="top">
					<td class="text" bgcolor="#ffffff" align="left"> '.$phyName.'</td>
					<td class="text" bgcolor="#ffffff" align="left"> '.$facName.'</td>
					<td class="text" bgcolor="#ffffff" align="left"> '.$groupName.'</td>
					<td class="text" bgcolor="#ffffff" align="left"> '.$CLSReports->numberFormat($totalCharges,2).'&nbsp;</td>
					<td class="text" bgcolor="#ffffff" align="left"> '.$totalUnits.'</td>
					<td class="text" bgcolor="#ffffff" align="left"> '.$CLSReports->numberFormat($previousDayCharges,2).'&nbsp;</td>
					<td class="text" bgcolor="#ffffff" align="left"> '.$previousDayUnits.'</td>
					<td class="text" bgcolor="#ffffff" align="left"> '.$lastDOS.'</td>
					<td class="text" bgcolor="#ffffff" align="left"> '.$totUnitCount.'</td>
					<td class="text" bgcolor="#ffffff" align="left"> '.$CLSReports->numberFormat($deniedAmount,2).'&nbsp;</td>
					<td class="text" bgcolor="#ffffff" align="left"> '.$totUnitPrevCount.'</td>
					<td class="text" bgcolor="#ffffff" align="left"> '.$CLSReports->numberFormat($deniedAmountPrev,2).'&nbsp;</td>
					</tr>';
			}
		}
	}
}
$strHTML .= '</table">';
if($strHTML && $print==true){
	echo $strHTML;
}else{
	echo '<div class="text-center alert alert-info">No Record Found.</div>';
}
?>