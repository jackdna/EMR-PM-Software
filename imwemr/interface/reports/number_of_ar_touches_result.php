<?php
$dateFormat=get_sql_date_format();
$currency = showCurrency();
$curDate = date(phpDateFormat());
if(empty($Start_date) === true){
	$Start_date = $curDate;
	$End_date = $curDate;
}
$printFile = true;
$csvFileData = NULL;

if ($_POST['form_submitted']) {
	$pdfData = NULL;
	$printFile = false;

	//--- CHANGE DATE FORMAT ---
	$startDate = getDateFormatDB($Start_date);
	$endDate = getDateFormatDB($End_date);

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($sc_name)<=0 && isPosFacGroupEnabled()){
		$sc_name = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($sc_name)<=0){
			$sc_name[0]='NULL';
		}
	}

	$Physician = implode(',',$Physician);
	$grp_id = implode(',',$grp_id);
	$sc_name = implode(',',$sc_name);
	

	// GET REPORT DATA
	$columnsArr = array();
	$none_charge_list = array();
	$get_charge_list_id = array();
	$printFile = false;
	$sortByPayment = NULL;
	$curDate = date(phpDateFormat().' h:i A');
	
	//--- GET GROUP NAME ---
	$arrAllGroups=array();
	$rs = imw_query("select gro_id, name from groups_new");
	while($res=imw_fetch_assoc($rs)){
		$arrAllGroups[$res['gro_id']] = $res['name'];
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
	// ------------------------------

	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
	}

	//--- GET ALL RESULTS FROM PATIENT CHARGE LIST TABLE ---------
	$ptChrgQry = "Select main.encounter_id, (main.charges * main.units) as totalAmt, main.units, main.gro_id, main.charge_list_detail_id,
	date_format(main.first_posted_date,'".$dateFormat."') as 'first_posted_date_formatted', main.facility_id, 
	main.operator_id as 'primaryProviderId', main.proc_balance, main.over_payment,
	main.from_sec_due_date, main.from_ter_due_date, main.from_pat_due_date, main.date_of_service, 
	DATEDIFF(main.from_sec_due_date,main.date_of_service) as 'sec_due_aging', 
	DATEDIFF(main.from_ter_due_date,main.date_of_service) as 'ter_due_aging', 
	DATEDIFF(main.from_pat_due_date,main.date_of_service) as 'pat_due_aging', 
	DATEDIFF(NOW(),main.date_of_service) as 'cuurent_aging' 
	FROM report_enc_detail main 
	LEFT JOIN users on users.id = main.operator_id 
	LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id
	LEFT JOIN pos_tbl ON pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
	LEFT JOIN groups_new ON groups_new.gro_id = main.gro_id 
	WHERE main.del_status='0' AND (main.first_posted_date BETWEEN '".$startDate."' AND '".$endDate."')";
	if(empty($sc_name) == false){
		$ptChrgQry .= " and main.facility_id IN ($sc_name)";
	}
	if(empty($grp_id) == false){
		$ptChrgQry .= " and main.gro_id IN ($grp_id)";
	}
	if(empty($Physician) == false){
		$ptChrgQry .= " and main.operator_id IN ($Physician)";
	}
	$ptChrgQry .= " ORDER BY main.first_posted_date, users.lname,users.fname, pos_facilityies_tbl.facilityPracCode, groups_new.name";
	$qry_res = imw_query($ptChrgQry) or die(imw_error());
	$mainQryRes = array();
	$ovr_pay_arr=array();
	while($res=imw_fetch_array($qry_res)){
		$printFile = true;	
		$aging=0;
		$last_paid_date='';
		$tempArrAging=array();
		$eid=$res['encounter_id'];
		$chgdetid=$res['charge_list_detail_id'];
		$group_id=$res['gro_id'];
		$primaryProviderId = $res['primaryProviderId'];
		$facilityId = $res['facility_id'];
		$posted_date= $res['first_posted_date_formatted'];

		$bal=$res['proc_balance'];
		$bal = ($res['over_payment']>0) ? "-".$res['over_payment'] : $bal;

		if($bal>0){
			if($res['sec_due_aging']>0){$tempArrAging[]=$res['sec_due_aging']; $last_paid_date=$res['from_sec_due_date']; }
			if($res['ter_due_aging']>0){$tempArrAging[]=$res['ter_due_aging']; $last_paid_date=$res['from_ter_due_date']; }
			if($res['pat_due_aging']>0){$tempArrAging[]=$res['pat_due_aging']; $last_paid_date=$res['from_pat_due_date']; }
			if(sizeof($tempArrAging)<=0){$tempArrAging[]=$res['cuurent_aging']; $last_paid_date=$res['date_of_service']; }
			rsort($tempArrAging);
			$aging=current($tempArrAging);
		}
		
		$mainResultArr[$primaryProviderId][$facilityId][$group_id][$posted_date]['charges']+=$res['totalAmt'];
		$mainResultArr[$primaryProviderId][$facilityId][$group_id][$posted_date]['balance']+=$bal;
		$mainResultArr[$primaryProviderId][$facilityId][$group_id][$posted_date]['units']+=$res['units'];
		if($bal>0){
			$mainResultArrAging[$primaryProviderId][$facilityId][$group_id][$posted_date][$last_paid_date]+=$aging;
		}
		$arr[$posted_date]=$posted_date;
	}

	if(sizeof($mainResultArr)>0){

/*		$pfx=",";
		$csvFileName = 'point_of_service_collections.csv';
		$file_name= $csvFileName;
		$csv_file_name= write_html("", $file_name);
	
		//CSV FILE NAME
		if(file_exists($csv_file_name)){
			unlink($csv_file_name);
		}
		$fp = fopen ($csv_file_name, 'a+');
		$strData.="Date Posted".$pfx;
		$strData.="User".$pfx;
		$strData.="Location".$pfx;
		$strData.="Billing Entity".$pfx;
		$strData.="Point of Service Collection Total".$pfx;
		$strData.="Point of Service Collection (Count)".$pfx;
		$strData.="Avg. Amt Collected for Day";
		$strData.= "\n";
		$fp=fopen($csv_file_name,'w');
		@fwrite($fp,$strData);
		@fclose($fp);*/
		
		foreach($mainResultArr as $proid => $provData){
			foreach($provData as $facid => $facData){
				foreach($facData as $groupid => $groupData){
					foreach($groupData as $posteddate => $postedData){
						
						$aging= ceil(array_sum($mainResultArrAging[$proid][$facid][$groupid][$posteddate]) / count($mainResultArrAging[$proid][$facid][$groupid][$posteddate]));
						
						$page_content.='
						<tr>
							<td class="text alignLeft white" style="width:10%;">'.$posteddate.'</td>
							<td class="text alignLeft white" style="width:15%;">'.$providerNameArr[$proid].'</td>
							<td class="text alignLeft white" style="width:15%;">'.$arrAllFacilities[$facid].'</td>
							<td class="text alignLeft white" style="width:15%;">'.$arrAllGroups[$groupid].'</td>
							<td class="text alignLeft white" style="width:10%; text-align:right">&nbsp;'.$postedData['units'].'</td>							
							<td class="text alignLeft white" style="width:15%; text-align:right">&nbsp;'.$CLSReports->numberFormat($postedData['charges'],2).'</td>
							<td class="text alignLeft white" style="width:10%; text-align:right">&nbsp;'.$CLSReports->numberFormat($postedData['balance'],2).'</td>
							<td class="text alignLeft white" style="width:10%; text-align:right">'.$aging.'</td>
						</tr>';


/*						$strData.= $posteddate.$pfx;
						$strData.= $providerNameArr[$proid].$pfx;
						$strData.= $arrAllFacilities[$facid].$pfx;
						$strData.= $arrAllGroups[$groupid].$pfx;
						$strData.= $CLSReports->numberFormat($postedData['charges'],2).$pfx;
						$strData.= $postedData['units'].$pfx;
						$strData.= $CLSReports->numberFormat($day_average,2);
						$strData.= "\n";
						$fp=fopen($csv_file_name,"w");
						@fwrite($fp,$strData);*/
					}
				}
			}
		}
		//@fclose($fp);

		$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$opInitial = $authProviderNameArr[1][0];
		$opInitial .= $authProviderNameArr[0][0];
		$opInitial = strtoupper($opInitial);

		$groupSelected = $CLSReports->report_display_selected($grp_id,'group',1, $allGrpCount);
		$practice_name = $CLSReports->report_display_selected($sc_name,'practice',1, $allFacOptions);
		$physician_name = $CLSReports->report_display_selected($Physician,'physician',1, $allPhyCount);

		$page_data='
		<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%">
		<tr class="rpt_headers">
			<td class="rptbx1" style="width:20%">Number of AR Touches</td>	
			<td class="rptbx2" style="width:40%">From : '.$Start_date.' To : '.$End_date.'</td>					
			<td class="rptbx3" style="width:40%">Created By: '.$opInitial.' on '.date($phpDateFormat)." ".date("h:i A").'</td>
		</tr>
		<tr class="rpt_headers">
			<td class="rptbx1">Groups: '.$groupSelected.'</td>	
			<td class="rptbx2">Physician: '.$physician_name.'</td>					
			<td class="rptbx3">Facility: '.$practice_name.'</td>
		</tr>
		</table>
		<table class="rpt_table rpt_table-bordered" style="width:100%">
		<tr>
			<td class="text_b_w alignCenter" style="width:10%;">Date Entered</td>				
			<td class="text_b_w alignCenter" style="width:15%;">User</td>
			<td class="text_b_w alignCenter" style="width:15%;">Location</td>
			<td class="text_b_w alignCenter" style="width:15%;">Billing Entity</td>
			<td class="text_b_w alignCenter" style="width:10%;">Count of Notes Entered</td>
			<td class="text_b_w alignCenter" style="width:15%;">Associated Charges on Notes Entered</td>
			<td class="text_b_w alignCenter" style="width:10%;">Associated Balance on Notes Entered</td>
			<td class="text_b_w alignCenter" style="width:10%;">Avg. Aging Days from DOS for Notes Entered</td>
		</tr>
		</table>
		<table class="rpt_table rpt_table-bordered" style="width:100%">
		'.$page_content.'
		</table>';
		
	}


	$op = 'p';
	//DECIDING OUTPUT
	$HTMLCreated=0;
	$hasData=0;
	if($printFile == true and $page_data != ''){
		$hasData=1;
		$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
		$csv_file_data= $styleHTML.$page_data;
	}else{
		$page_data = '<div class="text-center alert alert-info">No Record Found.</div>';
	}
	
	echo $page_data;
}
?>