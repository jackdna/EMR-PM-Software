<?php
//$objDataManage = new DataManage;

$currency=$GLOBALS['currency'];
$dateFormat= get_sql_date_format();
$phpDateFormat=phpDateFormat();
$curDate = date($phpDateFormat);
if(empty($Start_date) === true){
	$Start_date = $curDate;
	$End_date = $curDate;
}

$printFile = true;
$csvFileData = NULL;
if($_POST['form_submitted']){
	$pdfData = NULL;
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
	
	//--- CHANGE DATE FORMAT ---
	$startDate = getDateFormatDB($Start_date);
	$endDate = getDateFormatDB($End_date);

	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = ucfirst(trim($op_name_arr[1][0]));
	$op_name .= ucfirst(trim($op_name_arr[0][0]));
	$curDate = date($phpDateFormat.' h:i A');

	//---- GET OPERATOR DETAILS ----------
	$userQry = imw_query("select id,lname,fname,mname,pro_title,user_type,delete_status from users");
	$usersQryRes = array();
	$userNameArr = array();
	$operator_arr = array();
	$phyIdArr = array();
	while($qryRes  =imw_fetch_assoc($userQry)){
		$usersQryRes[] = $qryRes;
	}
	for($u=0;$u<count($usersQryRes);$u++){
		$id = $usersQryRes[$u]['id'];
		//--- PROVIDER TYPE CHECK ONLY FOR PHYSICIAN ---	
		$user_type = $usersQryRes[$u]['user_type'];
		if($user_type == 1 || $user_type == 5){
			$phyIdArr[] = $id;
		}
		//--- DELETE PROVIDER CHECK ---
		$delete_status = $usersQryRes[$u]['delete_status'];
		if($delete_status == 0){
			$operator_arr[] = $id;
			$user_name_arr = array();
			$user_name_arr['TITLE'] = $usersQryRes[$u]['pro_title'];
			$user_name_arr['LAST_NAME'] = $usersQryRes[$u]['lname'];
			$user_name_arr['FIRST_NAME'] = $usersQryRes[$u]['fname'];
			$user_name_arr['MIDDLE_NAME'] = $usersQryRes[$u]['mname'];
			$userNameArr[$id] = changeNameFormat($user_name_arr);
		}
	}

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($sc_name)<=0 && isPosFacGroupEnabled()){
		$sc_name = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($sc_name)<=0){
			$sc_name[0]='NULL';
		}
	}

	$grp_id = $_REQUEST['grp_id'];
	$grp_id = join(',',$grp_id);
	
	$sc_name = join(',',$sc_name);
	
	$Physician = $_REQUEST['Physician'];
	$Physician = join(',',$Physician);

	if(empty($Physician) == true){
		$Physician = join(',',$phyIdArr);
	}

	$credit_physician= (sizeof($crediting_provider)>0) ? implode(',',$crediting_provider) : '';	
	
	//--- GET GROUP NAME ---
	$group_name = "All Groups Selected";
	if(empty($grp_id) === false){
		$group_query = "select name from groups_new where gro_id = '$grp_id'";
		$groupQryRs = imw_query($group_query);	
		$groupQryRes = imw_fetch_assoc($groupQryRs);
		if(count($groupQryRes)>0){
			$group_name = $groupQryRes['name'];
		}
	}
	
	//---- GET ALL POS FACILITIES DETAILS ------
	$qry = "Select pos_facilityies_tbl.facilityPracCode, pos_facilityies_tbl.pos_facility_id,pos_tbl.pos_prac_code from pos_facilityies_tbl join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id";
	$posFacilityArr = array();
	$qryRs = imw_query($qry);
	while($qryRes = imw_fetch_array($qryRs)){
		$facilityPracCode = $qryRes['facilityPracCode'];
		$pos_facility_id = $qryRes['pos_facility_id'];
		$pos_prac_code = $qryRes['pos_prac_code'];
		$posFacilityArr[$pos_facility_id] = $facilityPracCode.' - '.$pos_prac_code;
	}

		//--- GET ALL RESULTS FROM PATIENT CHARGE LIST TABLE ---------
	$ptChrgQry = "Select patient_charge_list.encounter_id,patient_charge_list.patient_id,
			date_format(patient_charge_list.date_of_service , '".$dateFormat."') as date_of_service, 
			patient_data.lname,patient_data.fname,patient_data.mname, patient_charge_list_details.approvedAmt,
			patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId', patient_charge_list.secondaryProviderId,
			patient_charge_list.facility_id,
			patient_charge_list_details.totalAmount,
			cpt_fee_tbl.cpt4_code, cpt_fee_tbl.fee, cpt_fee_table.cpt_fee,
			users.fname as pro_fname,users.lname as pro_lname
			FROM patient_charge_list 
			LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id 
			LEFT JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id  = patient_charge_list_details.procCode 
			LEFT JOIN insurance_companies ON insurance_companies.id = patient_charge_list.primaryInsuranceCoId 
			LEFT JOIN cpt_fee_table ON cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id 
			JOIN patient_data ON patient_data.id = patient_charge_list.patient_id
			JOIN users ON users.id = patient_charge_list.primary_provider_id_for_reports
			WHERE (patient_charge_list.date_of_service between '$startDate' and '$endDate') 
			AND (patient_charge_list.del_status='0' AND patient_charge_list_details.del_status='0') 
			AND cpt_fee_table.fee_table_column_id = insurance_companies.FeeTable";

	if($allowType=='unmatched'){
		$ptChrgQry .= " AND (patient_charge_list_details.approvedAmt>0 AND patient_charge_list_details.approvedAmt <> cpt_fee_table.cpt_fee)";
	}
	if(empty($grp_id) == false){
		$ptChrgQry .= " and patient_charge_list.gro_id IN ($grp_id)";
	}
	if(empty($sc_name) == false){
		$ptChrgQry .= " and patient_charge_list.facility_id IN ($sc_name)";
	}
	
	if(empty($Physician) == false){
		$ptChrgQry .= " and patient_charge_list.primary_provider_id_for_reports IN ($Physician)";
	}
	if(empty($credit_physician) === false){
		$ptChrgQry.= " and patient_charge_list.secondaryProviderId IN ($credit_physician)";
	}
	if($chksamebillingcredittingproviders==1){
		$ptChrgQry.= " and patient_charge_list.primary_provider_id_for_reports!=patient_charge_list.secondaryProviderId";							
	}	
	$ptChrgQry .= " ORDER BY users.lname,users.fname,patient_data.lname,patient_data.fname,patient_charge_list.encounter_id";
	
	$ptQry = imw_query($ptChrgQry);
	$mainQryRes = array();
	while($qryRes  =imw_fetch_assoc($ptQry)){
		$mainQryRes[] = $qryRes;
	}
	$ovr_pay_arr=array();
	for($i=0;$i<count($mainQryRes);$i++){
		$printFile = true;	
		$doctor_id = $mainQryRes[$i]['primaryProviderId'];
		$facilityId = $mainQryRes[$i]['facility_id'];
		$encounter_id = $mainQryRes[$i]['encounter_id'];
		$charge_list_id = $mainQryRes[$i]['charge_list_id'];

		if(empty($Physician) == true && empty($credit_physician) === false){
			$doctor_id=$mainQryRes[$i]['secondaryProviderId'];
		}				
				
		if($process=='Summary'){
			$ovr_pay_arr[$doctor_id][$facilityId]['totalAmount']+= $mainQryRes[$i]['totalAmount'];
			$ovr_pay_arr[$doctor_id][$facilityId]['fee']+= $mainQryRes[$i]['cpt_fee'];
			$ovr_pay_arr[$doctor_id][$facilityId]['approvedAmt']+= $mainQryRes[$i]['approvedAmt'];
		}else{
			$ovr_pay_arr[$doctor_id][$encounter_id][] = $mainQryRes[$i];
		}
			
		$pro_name_arr = array();
		$pro_name_arr['LAST_NAME'] = $mainQryRes[$i]['pro_lname'];
		$pro_name_arr['FIRST_NAME'] = $mainQryRes[$i]['pro_fname'];
		$pro_name_arr['MIDDLE_NAME'] = "";			
		$provider_name_arr[$doctor_id]= changeNameFormat($pro_name_arr);	
	}

	$main_provider_arr=array_keys($ovr_pay_arr);
	$op = 'l';

	$groupSelected = $CLSReports->report_display_selected($grp_id,'group',1, $allGrpCount);
	$facilitySelected = $CLSReports->report_display_selected($sc_name,'practice',1, $allFacCount);
	$physicianSelected = $CLSReports->report_display_selected($Physician,'physician',1, $allPhyCount);
	$crphysicianSelected = $CLSReports->report_display_selected($credit_physician,'physician',1, $allCrPhyCount);
	
	if($process=='Summary'){
		require_once(dirname(__FILE__).'/allowVerify_SummaryReport.php');
	}else{
		require_once(dirname(__FILE__).'/allowVerify_DetailReport.php');		
	}

	if($printFile == true){
		$stylePDF = '<style>' . file_get_contents('css/reports_pdf.css') . '</style>';
		$strHTML = $stylePDF . $pdfData;
		$file_location = write_html($strHTML);
		$styleHTML = '<style>' . file_get_contents('css/reports_html.css') . '</style>';
		
		if($callFrom != 'scheduled'){
			echo $csv_file_data = $styleHTML . $csvFileData;
		}
	}else{
		if($callFrom != 'scheduled'){
			echo '<div class="text-center alert alert-info">No record exists.</div>';
		}
	}
}

?>