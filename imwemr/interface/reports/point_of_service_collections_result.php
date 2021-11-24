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
	$tempDataArr=array();

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

	$arr_sel_fac=array();
	$Physician = implode(',',$Physician);
	$grp_id = implode(',',$grp_id);
	$arr_sel_fac=$sc_name;
	if(sizeof($arr_sel_fac)>0)$arr_sel_fac=array_combine($arr_sel_fac,$arr_sel_fac);
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
	$rs = imw_query("select gro_id, name from groups_new ORDER BY name");
	while($res=imw_fetch_assoc($rs)){
		$arrAllGroups[$res['gro_id']] = $res['name'];
	}


	// GET SCHEDULE FACILITIES
	$query = "Select id,name,fac_prac_code, facility_type from facility";
	$fac_query=imw_query($query);
	$sch_fac_id_arr = array();
	$arr_pos_fac=array();
	$arr_sch_facilities=array();
	$headPosFacility=0;
	$arr_sel_sch_fac=array();
	$schFacId='';
	while($fac_query_res=imw_fetch_array($fac_query)){
		$fac_id = $fac_query_res['id'];
		$sch_fac_id_arr[$fac_id] = $fac_id;
		$arr_sch_facilities[$fac_id]=$fac_query_res['name'];
		$sch_pos_fac_arr[$fac_id] = $fac_query_res['fac_prac_code'];
		$arr_pos_fac[$fac_query_res['fac_prac_code']]=$fac_query_res['fac_prac_code'];
		
		if($fac_query_res['facility_type']=='1'){
			$headPosFacility=$fac_query_res['fac_prac_code'];
		}
		
		if($arr_sel_fac[$fac_query_res['fac_prac_code']]){
			$arr_sel_sch_fac[$fac_id]=$fac_id;
		}
		
	}
	if(sizeof($arr_sel_sch_fac)>0){
		$schFacId = implode(',',$arr_sel_sch_fac);
	}


	// -- GET ALL POS-FACILITIES
	$arrAllFacilities=array();
	$arrAllFacilities[0] = 'No Facility';
	$qry = "select pos_facilityies_tbl.facilityPracCode as name,
		pos_facilityies_tbl.pos_facility_id as id,
		pos_tbl.pos_prac_code
		from pos_facilityies_tbl
		left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
		order by pos_facilityies_tbl.facilityPracCode";
	$qryRs = imw_query($qry);
	while($qryRes  =imw_fetch_assoc($qryRs)){
		$id = $qryRes['id'];
		$name = $qryRes['name'];
		$pos_prac_code = $qryRes['pos_prac_code'];
		$arrAllFacilities[$id] = $name.' - '.$pos_prac_code;
	}						
	// ------------------------------

	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname from users ORDER BY lname, fname, mname");
	$providerNameArr = array();
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
	}


	//GETTING CI/CO PAYMENTS
	$qry="SELECT sa.sa_facility_id, cioPayDet.id as cioPaydetID, cioPay.patient_id, cioPay.payment_id, 
	DATE_FORMAT(cioPay.created_on, '".$dateFormat."') as 'created_on', 
	cioPay.created_by, cioPayDet.item_payment, 
	pd.fname, pd.mname, pd.lname, facility.default_group  
	FROM schedule_appointments sa 
	JOIN check_in_out_payment cioPay ON cioPay.sch_id  = sa.id 
	JOIN check_in_out_payment_details cioPayDet ON cioPayDet.payment_id= cioPay.payment_id
	JOIN patient_data pd ON pd.id = cioPay.patient_id 
	LEFT JOIN facility ON facility.id = sa.sa_facility_id 
	WHERE (cioPay.created_on BETWEEN '".$startDate."' AND '".$endDate."') 
	AND pd.id IS NOT NULL AND cioPayDet.status='0' AND cioPayDet.item_payment>0";
	if(empty($sc_name) === false){
		if(empty($schFacId) ===false){
			$qry.= " AND sa.sa_facility_id IN(".$schFacId.")";
		} else {
			$qry.=" AND 1=2"; //SO THAT QUERY WILL NOT RETURN ANY RESULT.
		}
	}
	if(empty($Physician) === false){
		$qry.= " AND cioPay.created_by IN(".$Physician.")";
	}
	if(empty($grp_id) === false){
		$qry.= " AND facility.default_group in($grp_id)";
	}

	$rs=imw_query($qry)or die(imw_error().'_451');
	while($res=imw_fetch_array($rs)){
		
		$printFile=true;
		$payment_id = $res['payment_id'];
		$sch_facility = $res['sa_facility_id'];
		$fac_id=$sch_pos_fac_arr[$sch_facility];
		if($fac_id=='' || $fac_id<=0) { $fac_id=0; }
		$oprId = $res['created_by'];
		$group_id= $res['default_group'];
		$created_on=$res['created_on'];

		$tempDataArr[$created_on][$oprId][$fac_id][$group_id][]=$res['item_payment'];
		$arrForSorting[$created_on]=$created_on;
	}	

	//GETTING PRE-PAYMENTS
	$qry="Select pDep.paid_amount, pDep.facility_id, pDep.apply_payment_date, pData.default_facility, 
	DATE_FORMAT(pDep.paid_date, '".$dateFormat."') as 'paid_date', pDep.entered_by, facility.default_group 
	FROM patient_pre_payment pDep 
	JOIN patient_data pData ON pData.id = pDep.patient_id 
	LEFT JOIN facility ON facility.id=pDep.facility_id 
	WHERE pDep.del_status='0' AND pData.id IS NOT NULL  
	AND (pDep.paid_date BETWEEN '".$startDate."' and '".$endDate."') AND pDep.paid_amount>0";
	if(empty($sc_name) === false){
		if(empty($schFacId) ===false){
			$qry .= " AND pDep.facility_id in($schFacId)";
		} else {
			$qry.=" AND 1=2"; //SO THAT QUERY WILL NOT RETURN ANY RESULT.
		}
	}
	if(empty($Physician) === false){
		$qry .= " AND pDep.entered_by in($Physician)";
	}
	if(empty($grp_id) === false){
		$qry.= " AND facility.default_group in($grp_id)";
	}
	$patQry = imw_query($qry);

	while($patQryRes = imw_fetch_assoc($patQry)){
		$printFile=true;
	
		$sch_facility=$patQryRes['facility_id'];
		$fac_id=$sch_pos_fac_arr[$sch_facility];
		$oprId=$patQryRes['entered_by'];
		$group_id=$patQryRes['default_group'];
		$paid_date=$patQryRes['paid_date'];
		
		if($fac_id<=0 || $fac_id=='')$fac_id=$headPosFacility;

		$tempDataArr[$paid_date][$oprId][$fac_id][$group_id][]=$patQryRes['paid_amount'];
		$arrForSorting[$paid_date]=$paid_date;
	}


	if(sizeof($tempDataArr)>0){
		
		$mainResultArr=array();

		//SORTING OF ALL DATA
		sort($arrForSorting);
		foreach($arrForSorting as $posted_date){

			foreach($providerNameArr as $proid =>$proname){

				if($tempDataArr[$posted_date][$proid]){
					foreach($arrAllFacilities as $facid =>$facname){
						
						if($tempDataArr[$posted_date][$proid][$facid]){
							foreach($arrAllGroups as $groupid =>$groupname){
								
								if($tempDataArr[$posted_date][$proid][$facid][$groupid]){
									$mainResultArr[$posted_date][$proid][$facid][$groupid]=$tempDataArr[$posted_date][$proid][$facid][$groupid];
								}
							}
						}
					}
				}
			}
		}
		
		//pre($mainResultArr);

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

		//$mainResultArr[$res['first_posted_date']][$primaryProviderId][$facilityId][$group_id][$chgDetId]=$chgDetId;
				
		foreach($mainResultArr as $posteddate => $postedData){	
			$dispDate=$posteddate;
/*			if($posteddate!='0000-00-00'){
				$dispDate=date('m-d-Y', strtotime($posteddate));
			}else{
				$dispDate='00-00-0000';
			}
*/			
			foreach($postedData as $proid => $proData){
				foreach($proData as $facid => $facData){
					foreach($facData as $groupid => $groupData){
						
						$payment=array_sum($groupData);
						$unit=count($groupData);

						$average_amt= $payment / $unit;
						
						$page_content.='
						<tr>
							<td class="text alignLeft white" style="width:10%;">'.$dispDate.'</td>
							<td class="text alignLeft white" style="width:15%;">'.$providerNameArr[$proid].'</td>
							<td class="text alignLeft white" style="width:15%;">'.$arrAllFacilities[$facid].'</td>
							<td class="text alignLeft white" style="width:15%;">'.$arrAllGroups[$groupid].'</td>
							<td class="text alignLeft white" style="width:15%; text-align:right">&nbsp;'.$CLSReports->numberFormat($payment,2).'</td>
							<td class="text alignLeft white" style="width:15%; text-align:right">&nbsp;'.$unit.'</td>
							<td class="text alignLeft white" style="width:15%; text-align:right">&nbsp;'.$CLSReports->numberFormat($average_amt,2).'</td>
						</tr>';
						//$total+=$payment;
					}
				}
			}
		}

//echo 'Total '.$total;

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
			<td class="rptbx1" style="width:20%">Point of Service Collections</td>	
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
			<td class="text_b_w alignCenter" style="width:10%;">Date Posted</td>				
			<td class="text_b_w alignCenter" style="width:15%;">User</td>
			<td class="text_b_w alignCenter" style="width:15%;">Location</td>
			<td class="text_b_w alignCenter" style="width:15%;">Billing Entity</td>
			<td class="text_b_w alignCenter" style="width:15%;">Point of Service Collection Total</td>
			<td class="text_b_w alignCenter" style="width:15%;">Point of Service Collection (Count) </td>
			<td class="text_b_w alignCenter" style="width:15%;">Avg. Amt Collected for Day</td>
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