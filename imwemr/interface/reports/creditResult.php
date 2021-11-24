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

	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = ucfirst(trim($op_name_arr[1][0]));
	$op_name .= ucfirst(trim($op_name_arr[0][0]));
	$curDate = date(phpDateFormat().' h:i A');	

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
	$credit_physician= (sizeof($crediting_provider)>0) ? implode(',',$crediting_provider) : '';		
	
	//---- GET OPERATOR DETAILS ----------
	$rs=imw_query("select id,lname,fname,mname,pro_title,user_type,delete_status from users");
	$usersQryRes = array();
	$userNameArr = array();
	$operator_arr = array();
	$phyIdArr = array();
	while($res=imw_fetch_array($rs)){
		$usersQryRes[] = $res;
	}
	for($u=0;$u<count($usersQryRes);$u++){
		$id = $usersQryRes[$u]['id'];
		//--- PROVIDER TYPE CHECK ONLY FOR PHYSICIAN ---	
		$user_type = $usersQryRes[$u]['user_type'];
		if($user_type == 1 || $user_type == 5){
			$phyIdArr[] = $id;
		}

		$provider_name_arr[$id] = core_name_format($usersQryRes[$u]['lname'], $usersQryRes[$u]['fname'], $usersQryRes[$u]['mname']);		
		
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

	//---- GET ALL POS FACILITIES DETAILS ------
	$rs=imw_query("select pos_facilityies_tbl.facilityPracCode, pos_facilityies_tbl.pos_facility_id,pos_tbl.pos_prac_code from pos_facilityies_tbl join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id");
	$posQryRes = array();
	while($res=imw_fetch_array($rs)){
		$posQryRes[] = $res;
	}
	$posFacilityArr = array();
	for($p=0;$p<count($posQryRes);$p++){
		$facilityPracCode = $posQryRes[$p]['facilityPracCode'];
		$pos_facility_id = $posQryRes[$p]['pos_facility_id'];
		$pos_prac_code = $posQryRes[$p]['pos_prac_code'];
		$posFacilityArr[$pos_facility_id] = $facilityPracCode.' - '.$pos_prac_code;
	}	
	
	

	// GET REPORT DATA
	$columnsArr = array();
	$none_charge_list = array();
	$get_charge_list_id = array();
	$printFile = false;
	$sortByPayment = NULL;
	$curDate = date(phpDateFormat().' h:i A');
	
	//--- GET GROUP NAME ---
	$group_name = "All Groups Selected";
	if(empty($grp_id) === false){
		$rs = imw_query("select name from groups_new where gro_id = '$grp_id'");
		$res=imw_fetch_assoc($rs);
		$rows = imw_num_rows($res);
		if(count($rows)>0){
			$group_name = $res['name'];
		}
	}

	$groupSelected = $CLSReports->report_display_selected($grp_id,'group',1, $allGrpCount);
	$practice_name = $CLSReports->report_display_selected($sc_name,'practice',1, $allFacOptions);
	$physician_name = $CLSReports->report_display_selected($Physician,'physician',1, $allPhyCount);
	$crphysicianSelected = $CLSReports->report_display_selected($credit_physician,'physician',1, $allCrPhyCount);
	
	
	//--- GET ALL RESULTS FROM PATIENT CHARGE LIST TABLE ---------
	$ptChrgQry = "Select patient_charge_list.encounter_id,patient_charge_list.patient_id,
	date_format(patient_charge_list.date_of_service , '".$dateFormat."') as date_of_service, 
	patient_data.id,patient_data.lname,patient_data.fname,patient_data.mname,
	patient_charge_list.charge_list_id,patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId',
	patient_charge_list.facility_id, patient_charge_list.secondaryProviderId,
	patient_charge_list_details.overPaymentForProc, patient_charge_list_details.charge_list_detail_id,
	patient_charge_list_details.totalAmount, patient_charge_list_details.paidForProc,  
	cpt_fee_tbl.cpt4_code,
	users.fname as pro_fname,users.lname as pro_lname
	FROM patient_charge_list 
	LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id 
	LEFT JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode 
	JOIN patient_data ON patient_data.id = patient_charge_list.patient_id
	JOIN users ON users.id = patient_charge_list.primary_provider_id_for_reports
	WHERE (patient_charge_list.date_of_service between '$startDate' and '$endDate')
	and patient_charge_list.overPayment>0 AND patient_charge_list_details.del_status='0'";
	if(empty($sc_name) == false){
		$ptChrgQry .= " and patient_charge_list.facility_id IN ($sc_name)";
	}
	if(empty($grp_id) == false){
		$ptChrgQry .= " and patient_charge_list.gro_id IN ($grp_id)";
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
	$qry_res = imw_query($ptChrgQry);
	
	
	$mainQryRes = array();
	$ovr_pay_arr=array();
	while($res=imw_fetch_array($qry_res)){
		$mainQryRes[] = $res;
	}
	for($i=0;$i<count($mainQryRes);$i++){
		$printFile = true;	
		$doctor_id = $mainQryRes[$i]['primaryProviderId'];
		$facilityId = $mainQryRes[$i]['facility_id'];
		$encounter_id = $mainQryRes[$i]['encounter_id'];
		$charge_list_id = $mainQryRes[$i]['charge_list_id'];
		$charge_list_detail_id = $mainQryRes[$i]['charge_list_detail_id'];

		if(empty($Physician) == true && empty($credit_physician) === false){
			$doctor_id=$mainQryRes[$i]['secondaryProviderId'];
		}		
		
		if($process=='Summary'){
			$ovr_pay_arr[$doctor_id][$facilityId]['overPaymentForProc']+= $mainQryRes[$i]['overPaymentForProc'];
			$ovr_pay_arr[$doctor_id][$facilityId]['totalAmount']+= $mainQryRes[$i]['totalAmount'];
			$ovr_pay_arr[$doctor_id][$facilityId]['paidForProc']+= $mainQryRes[$i]['paidForProc'];
		}else{
			$ovr_pay_arr[$doctor_id][$encounter_id][] = $mainQryRes[$i];
		}
	}
	
	$op = 'p';
	$main_provider_arr=array();
	//--- GET ADJUSTMENT AMOUNT ---
	$op = 'l';
	$main_provider_arr=array_keys($ovr_pay_arr);
	if($process=='Summary'){
		require_once(dirname(__FILE__).'/creditSummaryResult.php');
	}else{
		require_once(dirname(__FILE__).'/creditDetailResult.php');
	}


	if($printFile == true){
		$pdf_css= '<style>'.file_get_contents("css/reports_pdf.css").'</style>';
		$strHTML = <<<DATA
			$pdf_css
			$pdfData
DATA;
		$file_location = write_html($strHTML);
	}
	
	if($callFrom != 'scheduled'){
		if($printFile == true){
			echo $csvFileData;
		} else {
			 echo '<div class="text-center alert alert-info">No record exists.</div>';
		}
	}
}
?>