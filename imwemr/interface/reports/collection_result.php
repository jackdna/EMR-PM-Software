<?php

$masterEncArr =array();
$arrMasterPatDet =array();
$masterPatArr=array();
$getSqlDateFormat = get_sql_date_format();
$phpDateFormat = phpDateFormat();
$curDate = date($phpDateFormat.' h:i A');		
$page_data = NULL;
$pdf_data = NULL;

if(empty($_POST['form_submitted']) === false){
	
	$search = "DOS";
	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	
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
	$Sdate=$Start_date;
	$Edate=$End_date;
	$Start_date = getDateFormatDB($Start_date);
	$End_date = getDateFormatDB($End_date);

	$next_action_status_arr = $next_action_status;
	if($key=array_search('other', $next_action_status_arr)){
		unset($next_action_status_arr[$key]);
		$next_action_status = implode(',', $next_action_status_arr);
	}else{
		$next_action_status = implode(',', $next_action_status_arr);
	}

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_name)<=0 && isPosFacGroupEnabled()){
		$facility_name = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_name)<=0){
			$facility_name[0]='NULL';
		}
	}	

	//FACILITY
	$sc_name = join(',',$facility_name);
	
	//PHYSICIAN
	$rqArrPhyId = $_REQUEST['phyId'];
	$Physician = join(',',$rqArrPhyId);
	
	$groupId = $_REQUEST['groups'];
	$grp_id = join(',',$groupId);
	
	//--- GET GROUP NAME ---
	$group_name = $CLSReports->report_display_selected($grp_id,'group','1');
	$practice_name = $CLSReports->report_display_selected($sc_name,'practice','1');
	$physician_name = $CLSReports->report_display_selected($Physician,'physician','1');
	$action_name = $CLSReports->report_display_selected($next_action_status,'nextAction','1');
	
	$chkbox_after_collection_arr = explode(',', $chkbox_after_collection);
	
	// GET ID OF COLLECTION STATUS
	$collectionId='';
	$collectionId = get_account_status_id_collections();

	// GET ALL ACTION CODES
	$arrAllActionCodes=array();
	$qry = "select id, action_status from patient_next_action";
	$rs=imw_query($qry);
	while($res = imw_fetch_array($rs)){
		$arrAllActionCodes[$res['id']] = $res['action_status'];
	}
	
	if(empty($letter_chk_imp) === false){
		require_once(dirname(__FILE__).'/collection_letter_pdf.php');
	}
}

// MASTER QUERY TO GET LAST ENCOUNTER FOR EVERY PATIENT
if($collectionId!=''){
	$masterQry = "Select report_enc_detail.patient_id, report_enc_detail.encounter_id, report_enc_detail.total_charges as totalAmt,
			report_enc_detail.collection,
			report_enc_detail.collectionAmount,
			report_enc_detail.pat_due 
			FROM report_enc_detail 
			JOIN users on users.id = report_enc_detail.primary_provider_id_for_reports
			JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = report_enc_detail.facility_id
			JOIN patient_data on patient_data.id = report_enc_detail.patient_id
			WHERE trim(patient_data.lname) != 'doe' AND report_enc_detail.del_status='0' 
			and patient_data.pat_account_status IN(".$collectionId.") 
			";
			
	if(empty($next_action_status)==true){
		
		$masterQry .= " AND report_enc_detail.collectionDate between '$Start_date' and '$End_date'";
		$masterQry .= " AND report_enc_detail.pat_due>0";		
		
		if(empty($sc_name) == false){
			$masterQry .= " and report_enc_detail.facility_id IN ($sc_name)";
		}
		if(empty($grp_id) == false){
			$masterQry .= " and report_enc_detail.gro_id IN ($grp_id)";
		}
		if(empty($Physician) == false){
			$masterQry .= " and report_enc_detail.primary_provider_id_for_reports IN ($Physician)";
		}
		if($startLname){
			$masterQry .= " and patient_data.lname > '$startLname'";
		}
		if($endLname){
			$masterQry .= " and (patient_data.lname < '$endLname' or patient_data.lname like '$endLname%')";
		}
		if($patientId){
			$masterQry .= " and patient_data.id = '$patientId'";
		}
	}
	if(empty($next_action_status)==false){
		$masterQry .=" AND patient_data.next_action_status IN(".$next_action_status.")";
	}
	$masterQry .= " order by report_enc_detail.encounter_id";
	
	
	$masterRs=imw_query($masterQry);
	$arrTotalPatCharges=array();
	$arrTotalPatBalance=array();
	$arrTotalPatCollDet=array();
	while($masterRes = imw_fetch_array($masterRs)){
		$masterAllEncArr[$masterRes['encounter_id']] = $masterRes['encounter_id'];
		$masterAllPatEnc[$masterRes['encounter_id']] = $masterRes['patient_id'];
		$masterEncArr[$masterRes['patient_id']] =$masterRes['encounter_id'];
		$masterPatArr[$masterRes['patient_id']]  =$masterRes['patient_id'];
	}
	$masterAllEncStr  =implode(',', $masterAllEncArr);
	$masterEncStr  =implode(',', $masterEncArr);
	$masterPatStr  =implode(',', $masterPatArr);	
}

// GET TOTAL PAT DUE OF SELECTED PATIENTS
$tempEncArr=array();
if(sizeof($masterPatArr)>0){
	$qry = "Select report_enc_detail.patient_id, report_enc_detail.encounter_id, report_enc_detail.collection, report_enc_detail.total_charges as totalAmount, collectionAmount, date_format(report_enc_detail.collectionDate,'$getSqlDateFormat') as collectionDate, pat_due 
	 FROM report_enc_detail
	 WHERE report_enc_detail.encounter_id IN(".$masterAllEncStr.") AND report_enc_detail.del_status='0' 
	 ORDER BY report_enc_detail.charge_list_id";
	$rs=imw_query($qry);
	while($res= imw_fetch_array($rs)){
		$totalBalance=0;
		$arrTotalPatCharges[$res['patient_id']]+= $res['totalAmount'];
		$arrTotAmountLetter[$res['patient_id']]+= $res['totalAmount'];
		if(!in_array($res['encounter_id'], $tempEncArr)){
			$arrTotalPatCollDet[$res['patient_id']]['COLL_AMT']+= $res['collectionAmount'];
			$tempEncArr[$res['encounter_id']] = $res['encounter_id'];		
		}
		if($res['collection']=='true'){
			$arrTotalPatCollDet[$res['patient_id']]['COLL_DATE']= $res['collectionDate'];
		}
	
		if($res["pat_due"]>0){
			$totalBalance = $res['pat_due'];
		}
		$arrTotalPatBalance[$res['patient_id']]+=$totalBalance;
		$arrTotBalanceLetter[$res['patient_id']]+= $totalBalance;
	}
}

if(sizeof($masterEncArr)>0){
	$qry = "select report_enc_detail.charge_list_id,
			report_enc_detail.encounter_id,
			date_format(report_enc_detail.date_of_service,'$getSqlDateFormat') as date_of_service,
			date_format(report_enc_detail.letter_sent_date,'$getSqlDateFormat') as letter_sent_date,
			report_enc_detail.primary_provider_id_for_reports as 'primaryProviderId',
			report_enc_detail.charge_list_detail_id,
			users.lname as physicianLname,users.fname as physicianFname,
			users.mname as physicianMname,pos_facilityies_tbl.facilityPracCode,
			pos_facilityies_tbl.pos_facility_id,patient_data.id as patient_id,
			patient_data.lname,	patient_data.fname, patient_data.mname,
			patient_data.next_action_status,patient_data.DOB,patient_data.ss,patient_data.primary_care, patient_data.patient_notes,
			patient_data.acc_statement_count, report_enc_detail.proc_code_id, patient_data.patientStatus, 
			patient_data.preferr_contact,
			patient_data.phone_home, patient_data.phone_biz, patient_data.phone_cell, patient_data.street, patient_data.street2, patient_data.postal_code,
			patient_data.city, patient_data.state, patient_data.country_code, 			
			report_enc_detail.lastPaymentDate
			from report_enc_detail
			join users on users.id = report_enc_detail.primary_provider_id_for_reports
			join pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = report_enc_detail.facility_id
			join patient_data on patient_data.id = report_enc_detail.patient_id
			where report_enc_detail.encounter_id IN(".$masterEncStr.") AND report_enc_detail.del_status='0'";
	
	$qry .= " order by users.lname,users.fname, 
			pos_facilityies_tbl.facilityPracCode,
			report_enc_detail.date_of_service, 
			patient_data.lname,patient_data.fname";
	
	$qryRs=imw_query($qry);
	$main_encounter_id_arr = array();
	$facilityNameArr = array();
	$physician_initial_arr = array();
	$pt_id_arr = array();
	while($qryRes = imw_fetch_array($qryRs)){
		$patient_id = $qryRes['patient_id'];
		$encounter_id = $qryRes['encounter_id'];
		$main_encounter_id_arr[$encounter_id] = $encounter_id;
		$primaryProviderId = $qryRes['primaryProviderId'];
		
		//---- GET POS FACILITY NAME ---
		$pos_facility_id = $qryRes['pos_facility_id'];	
		$facilityPracCode = $qryRes['facilityPracCode'];
		$facilityNameArr[$pos_facility_id] = $facilityPracCode;	
	
		$mainResArr[$primaryProviderId][$pos_facility_id][$patient_id][] = $qryRes;
		
		//---- GET PHYSICIAN INITIAL ------
		$physician_initial = substr($qryRes['physicianFname'],0,1);
		$physician_initial .= substr($qryRes['physicianLname'],0,1);
		$physician_initial = trim(strtoupper($physician_initial));
		$physician_initial_arr[$primaryProviderId] = $physician_initial;
		$pt_id_arr[$patient_id] = $patient_id;
	}
	
	//RESPONSIBLE PARTY / GUARANTOR 
	$guarantorArr = array();
	$pt_id_Str  = implode(',', $pt_id_arr);
	$ptrpqry = imw_query("SELECT patient_id,fname,lname,address,address2,city,state,zip,ss,home_ph,mobile FROM `resp_party` WHERE patient_id IN ($pt_id_Str) ");
	while($rpqryRes = imw_fetch_assoc($ptrpqry)){
		$patient_id	= $rpqryRes['patient_id'];
		$guarantorArr[$patient_id] = $rpqryRes;
	}
	
	//Employer Data
	$employerArr = array();
	$epqry = imw_query("SELECT pid,name,street,postal_code,city,state FROM `employer_data` WHERE pid IN ($pt_id_Str) ");
	while($epqryRes = imw_fetch_assoc($epqry)){
		$pid = $epqryRes['pid'];
		$employerArr[$pid] = $epqryRes;
	}
	
	//Insurance Data
	$insArr = array();
	$insqry = imw_query("
	SELECT  ins_case.patient_id,ins_data.id as ins_id, ins_data.group_number as group_number,ins_data.group_number as group_number, ins_case.ins_caseid as ins_case_id, ins_cs_type.case_name as ins_case_name, ins_data.type as ins_type, ins_data.policy_number as policy_number, ins_comp.name as ins_provider, ins_comp.contact_address as ins_address, ins_comp.City as ins_city, ins_comp.State as ins_state,
	ins_comp.Zip as ins_zip, ins_comp.phone as ins_phone FROM insurance_case ins_case 
		LEFT JOIN insurance_case_types ins_cs_type ON (ins_case.ins_case_type = ins_cs_type.case_id)
		LEFT JOIN insurance_data ins_data ON (ins_case.ins_caseid = ins_data.ins_caseid)
		LEFT JOIN insurance_companies ins_comp ON (ins_data.provider = ins_comp.id)
		WHERE ins_case.patient_id IN ($pt_id_Str) 
		AND ins_data.actInsComp = 1
		AND ins_case.case_status = 'Open'
		AND ins_case.del_status = 0");
	while($insqryRes = imw_fetch_assoc($insqry)){
		$pid = $insqryRes['patient_id'];
		$insArr[$pid][$insqryRes['ins_type']] = $insqryRes;
	}
		
	//CPT Code Data
	$cptArr = array();
	$cptqry = imw_query("SELECT cpt_fee_id, cpt_prac_code, cpt_desc FROM cpt_fee_tbl"); 
	while($cptqryRes = imw_fetch_assoc($cptqry)){
		$cpt_fee_id = $cptqryRes['cpt_fee_id'];
		$cpt_desc = $cptqryRes['cpt_desc'];
		$cptArr[$cpt_fee_id] = $cpt_desc;
	}

	// DAYS AR
	$arArr = array();
	$arQry = "Select patient_charge_list.patient_id, patient_charge_list.encounter_id,lastPaymentDate, patient_charge_list.date_of_service, 
		patient_charge_list.statement_status, date_format(patient_charge_list.statement_date,'$getSqlDateFormat') as statement_date,
		patient_charge_list.statement_date as 'st_date',
		date_format(patient_charge_list.letter_sent_date,'$getSqlDateFormat') as letter_sent_date, patient_charge_list.patientDue,
		IF(lastPaymentDate<>'0000-00-00', patient_charge_list.lastPaymentDate, patient_charge_list.date_of_service) as lastARDate,
		IF(lastPaymentDate<>'0000-00-00',DATEDIFF(NOW(),patient_charge_list.lastPaymentDate),DATEDIFF(NOW(),patient_charge_list.date_of_service)) as arAgingDays  
		FROM patient_charge_list 
		WHERE patient_id IN(".$pt_id_Str.") AND patient_charge_list.del_status='0'";	
		if(empty($Start_date)==false && empty($End_date)==false){
			$arQry.= " AND patient_charge_list.date_of_service between '$Start_date' and '$End_date'";
		}
		if(empty($sc_name) == false){
			$arQry.= " and patient_charge_list.facility_id IN ($sc_name)";
		}
		if(empty($grp_id) == false){
			$arQry.= " and patient_charge_list.gro_id IN ($grp_id)";
		}
		if(empty($Physician) == false){
			$arQry.= " and patient_charge_list.primary_provider_id_for_reports IN ($Physician)";
		}		
		$arQry.= " ORDER BY date_of_service"; 
		while($arRes = imw_fetch_assoc($arQry)){
		$patient_id = $arRes['patient_id'];
		$arAgingDays = $arRes['arAgingDays'];
		$arArr[$patient_id] = $arAgingDays;
	}
	
	$qry=imw_query("Select * from report_enc_trans WHERE trans_type !='charges' and trans_del_operator_id='0' 
	and encounter_id in($masterAllEncStr) and patient_id>0 ORDER BY trans_dot, trans_dot_time asc");
	while($qryRes = imw_fetch_array($qry)){
		$patient_id = $qryRes['patient_id'];
		if($qryRes['trans_type']=='paid' || $qryRes['trans_type']=='Interest Payment' || $qryRes['trans_type']=='Deposit' || $qryRes['trans_type']=='Negative Payment'){
			if($qryRes['trans_type']=='Negative Payment'){
				$qryRes['trans_amount']='-'.$qryRes['trans_amount'];
			}
			$mainEncounterPayArr[$patient_id][$qryRes['master_tbl_id']]=$qryRes['trans_amount'];
		}
		if($qryRes['trans_type']=='credit'){
			$pay_crd_arr[$patient_id][$qryRes['master_tbl_id']]= $qryRes['trans_amount'];
		}
		if($qryRes['trans_type']=='debit'){
			$pay_deb_arr[$patient_id][$qryRes['master_tbl_id']]= '-'.$qryRes['trans_amount'];
		}
		if($qryRes['trans_type']=='Discount' || $qryRes['trans_type']=='Write Off' || $qryRes['trans_type']=='Discount'){
			$normalWriteOffAmt[$patient_id][$qryRes['master_tbl_id']]= $qryRes['trans_amount'];
		}
		if($qryRes['trans_type']=='Adjustment' || $qryRes['trans_type']=='Over Adjustment' || $qryRes['trans_type']=='Returned Check'){
			$arrAdjustmentAmt[$patient_id][$qryRes['master_tbl_id']]= $qryRes['trans_amount'];
		}
	}
	//-------------------------------------
}

//CSV Download




// ------- HTML CREATION
$main_colspan=12;
$sub_colspan=7;
$blank_td="";
if(count($mainResArr)>0){
	
	//MAKING OUTPUT DATA
	$file_name="collection_report.csv";
	$csv_file_name1= write_html("", $file_name);
	if(file_exists($csv_file_name1)){
		unlink($csv_file_name1);
	}
	$fp = fopen ($csv_file_name1, 'a+');	
	$arr=array();
	$arr[]="Physician Name";
	$arr[]="Location";
	$arr[]="Patient Name";
	$arr[]="Address";
	$arr[]="Preferred Contact";
	$arr[]="SERVICE DATE (D.O.S.)";
	$arr[]="Coll. Date";
	$arr[]="Letter Sent Date";
	$arr[]="Action Code";
	$arr[]="Total Charges";
	$arr[]="Total Payments";
	$arr[]="Total Pat. Bal.";
	$arr[]="Coll. Amount";
	$arr[]="Guarantor First Name";
	$arr[]="Guarantor Last Name";
	$arr[]="Address 1";
	$arr[]="Address 2";
	$arr[]="City";
	$arr[]="State";
	$arr[]="Zip";
	$arr[]="Home Phone";
	$arr[]="Cell Phone";
	$arr[]="Patient D.O.B.";
	$arr[]="Patient SS#";
	$arr[]="Guarantor SS#";
	$arr[]="Employer Name";
	$arr[]="Employer Address";
	$arr[]="City";
	$arr[]="State";
	$arr[]="Zip";
	$arr[]="Last Payment Date";
	$arr[]="Primary Insurance Name";
	$arr[]="Address";
	$arr[]="City";
	$arr[]="State";
	$arr[]="Zip";
	$arr[]="Insurance Phone";
	$arr[]="Primary Group";
	$arr[]="Primary Policy Number";
	$arr[]="Secondary Insurance Name";
	$arr[]="Address";
	$arr[]="City";
	$arr[]="State";
	$arr[]="Phone";
	$arr[]="Zip";
	$arr[]="Group";
	$arr[]="Policy Number";
	$arr[]="Procedure code / Description";
	$arr[]="Referring Physician";
	$arr[]="Notes/Denial Reason";
	$arr[]="Pt Status";
	$arr[]="No Days in AR";
	$arr[]="No of Statements";
	$arr[]="Billing Entity";
	
	fputcsv($fp,$arr, ",","\"");
	$fp = fopen ($csv_file_name1, 'a+');

	
	$conditionChk = true;
	$totalPatCounts = 0;
	$TotalChargesDueArr = array();
	$TotalPaymentsArr = array();
	$TotalInsuranceDueArr = array();
	$TotalPatientDueArr = array();
	$TotalCreditAmountArr = array();
	$totalWriteOffArr = array();
	$TotalBalanceArr = array();
	$TotalCollectionArr = array();
	//--- GET PROVIDER ID ARRAY ----
	$providerIdArr = array_keys($mainResArr);
	for($p=0;$p<count($providerIdArr);$p++){
		$provider_id = $providerIdArr[$p];
		//--- GET FACILITY DETAIL UNDER SINGLE PROVIDER -------
		$facilityDataArr = $mainResArr[$provider_id];
		$facilityIdArr = array();
		if(count($facilityDataArr)>0){
			$facilityIdArr = array_keys($facilityDataArr);
		}		
		
		for($f=0;$f<count($facilityIdArr);$f++){
			//---- SINGLE PROVIDER TOATAL VARIABLES RESET -------
			$subTotalChargesDueArr = array();
			$subTotalPaymentsArr = array();
			$subTotalInsuranceDueArr = array();
			$subTotalPatientDueArr = array();
			$subTotalCreditAmountArr = array();
			$subtotalWriteOffArr = array();
			$subTotalBalanceArr = array();
			$subTotalCollectionArr = array();
			
			//--- GET PATIEMNT DETAILS UNDER SINGLE FACILITY
			$facility_id = $facilityIdArr[$f];
			$patientDataArr = $facilityDataArr[$facility_id];
			$patientIdArr = array();
			if(count($patientDataArr)>0){
				$patientIdArr = array_keys($patientDataArr);
			}
			for($e=0;$e<count($patientIdArr);$e++){
				$totalPatCounts++;
				$patientId = $patientIdArr[$e];
				$physicianName = '';
				$facilityName = '';
				$write_off = 0;
				$patientPaidAmt=0;
				$copay=0;
				
				if($e == 0){
					$physicianName = $physician_initial_arr[$provider_id];
					$facilityName = $facilityNameArr[$facility_id];
				}
				$phyNameCSV = $physician_initial_arr[$provider_id];
				$facNameCSV = $facilityNameArr[$facility_id];
				//--- GET PATIENT DETAILS ----
				$patientDetailsArr = $patientDataArr[$patientId];

				$encounterId = $patientDetailsArr[0]['encounter_id'];
				$patient_id = $patientDetailsArr[0]['patient_id'];
				$patient_name = $patientDetailsArr[0]['lname'].', ';
				$patient_name .= $patientDetailsArr[0]['fname'].' ';
				$patient_name .= $patientDetailsArr[0]['mname'];
				$patient_ss = $patientDetailsArr[0]['ss'];
				$patient_dob = $patientDetailsArr[0]['DOB'];
				$patient_RF = $patientDetailsArr[0]['primary_care'];
				$patient_notes = $patientDetailsArr[0]['patient_notes'];
				$acc_statement_count = $patientDetailsArr[0]['acc_statement_count'];
				$proc_code_id = $patientDetailsArr[0]['proc_code_id'];
				$patientStatus = $patientDetailsArr[0]['patientStatus'];
						
				$patient_name = ucfirst(trim($patient_name));
				if($patient_name[0] == ','){
					$patient_name = substr($patient_name,1);
				}
				$patient_name .= ' - '.$patient_id;

				$date_of_service = $patientDetailsArr[0]['date_of_service'];
				$letterSentDate = ($patientDetailsArr[0]['letter_sent_date']!='00-00-0000') ? $patientDetailsArr[0]['letter_sent_date'] : '';
				$lastPaymentDate = ($patientDetailsArr[0]['lastPaymentDate']!='0000-00-00') ? $patientDetailsArr[0]['lastPaymentDate'] : '';
				
				$next_action_code='';
				if($patientDetailsArr[0]['next_action_status']>0){
					$next_action_code = $arrAllActionCodes[$patientDetailsArr[0]['next_action_status']];
				}
				
				//---- PATIENT ACCOUNT AMOUNTS ----
				$totalAmt = $arrTotalPatCharges[$patient_id];
				
				$guarantorData = $guarantorArr[$patient_id];
				
				$employerData = $employerArr[$patient_id];
				
				$insArrPri = $insArr[$patient_id]['primary'];
				
				$insArrSec = $insArr[$patient_id]['secondary'];
				
				$cpt_val = $cptArr[$proc_code_id];
				
				$arDay = $arArr[$patient_id];
			
				$patientPaidAmt = array_sum($mainEncounterPayArr[$patient_id]) + array_sum($normalWriteOffAmt[$patient_id]) + array_sum($arrAdjustmentAmt[$patient_id]) + array_sum($pay_crd_arr[$patient_id])+array_sum($pay_deb_arr[$patient_id]) + $copay;
				$totalBalance = $arrTotalPatBalance[$patient_id];
				$collectionAmount = $arrTotalPatCollDet[$patient_id]['COLL_AMT'];
				$collectionDate = $arrTotalPatCollDet[$patient_id]['COLL_DATE'];
				
				//---- SINGLE PROVIDER TOATAL VARIABLES -------
				$subTotalChargesDueArr[] = $totalAmt;
				$subTotalPaymentsArr[] = $patientPaidAmt;
				$subTotalBalanceArr[] = $totalBalance;
				$subTotalCollectionArr[] = $collectionAmount;
				
				//---- GRAND TOATAL VARIABLES -------
				$TotalChargesDueArr[] = $totalAmt;
				$TotalPaymentsArr[] = $patientPaidAmt;
				$TotalBalanceArr[] = $totalBalance;
				$TotalCollectionArr[] = $collectionAmount;
				
				//---NUMBER FORMAT FOR SINGLE ENCOUNTER --------
				$totalAmt = numberFormat($totalAmt,2);
				$patientPaidAmt = numberFormat($patientPaidAmt,2);
				$totalBalance = numberFormat($totalBalance,2);
				$collectionAmount = numberFormat($collectionAmount,2);

				$dos_width="70px";
				$pat_width="130px";
				$fac_width="100px";
				$fac_title_width="100px";
				$coll_date="70px";
				$letter_date="100px";
				
				$data .= <<<DATA
					<tr bgcolor="#FFFFFF">
						<td width="10" style="text-align:left;" class="text_10" valign="top">$physicianName</td>
						<td style="text-align:left;width:$fac_width" class="text_10" valign="top">$facilityName</td>
						<td style="text-align:left;width:$pat_width" class="text_10" valign="top">$patient_name</td>
						<td style="text-align:center;width:$dos_width" class="text_10" valign="top">$date_of_service</td>
						<td style="text-align:center;width:$coll_date" class="text_10" valign="top">$collectionDate</td>
						<td style="text-align:center;width:$letter_date" class="text_10" valign="top">$letterSentDate</td>
						<td style="text-align:left;width:100px" class="text_10" valign="top">$next_action_code</td>
						<td style="text-align:right;width:100px" class="text_10" valign="top">$totalAmt</td>
						<td style="text-align:right;width:110px" class="text_10" valign="top">$patientPaidAmt</td>
						<td style="text-align:right;width:110px" class="text_10" valign="top">$totalBalance</td>
						<td style="text-align:right;width:120px" class="text_10" valign="top">$collectionAmount</td>
					</tr>
DATA;

					
					//PATIENT ADDRESS
					$address='';
					$tempArrAdd=array();
					$tempArrAdd[]=$patientDetailsArr[0]['street'];
					$tempArrAdd[]=$patientDetailsArr[0]['street2'];
					$tempArrAdd[]=$patientDetailsArr[0]['city'];
					$tempArrAdd[]=$patientDetailsArr[0]['state'];
					$tempArrAdd[]=$patientDetailsArr[0]['country_code'];
					$tempArrAdd[]=$patientDetailsArr[0]['postal_code'];
					//remove blank values
					$tempArrAdd=array_filter($tempArrAdd);		
					$address=implode(', ', $tempArrAdd);
					
					//PATIENT Phone
					$telephone='';
					$phone_default = $patientDetailsArr[0]["phone_home"];
					$prefer_contact = $patientDetailsArr[0]["preferr_contact"];
					if($prefer_contact == 0){
						if(trim($patientDetailsArr[0]["phone_home"]) != ""){$phone_default = $patientDetailsArr[0]["phone_home"]; }
					}else if($prefer_contact == 1){
						if(trim($patientDetailsArr[0]["phone_biz"]) != ""){$phone_default = $patientDetailsArr[0]["phone_biz"]; }				
					}else if($prefer_contact == 2){
						if(trim($patientDetailsArr[0]["phone_cell"]) != ""){$phone_default = $patientDetailsArr[0]["phone_cell"]; }				
					}
					$telephone = core_phone_format($phone_default);			
			
					
						$arr=array();
						$arr[]=$phyNameCSV;
						$arr[]=$facNameCSV;
						$arr[]=$patient_name;
						$arr[]=$address;
						$arr[]=$telephone;
						$arr[]=$date_of_service;
						$arr[]=$collectionDate;
						$arr[]=$letterSentDate;
						$arr[]=$next_action_code;
						$arr[]=$totalAmt;
						$arr[]=$patientPaidAmt;
						$arr[]=$totalBalance;
						$arr[]=$collectionAmount;
						$arr[]=$guarantorData['fname'];
						$arr[]=$guarantorData['lname'];
						$arr[]=$guarantorData['address'];
						$arr[]=$guarantorData['address2'];
						$arr[]=$guarantorData['city'];
						$arr[]=$guarantorData['state'];
						$arr[]=$guarantorData['zip'];
						$arr[]=$guarantorData['home_ph'];
						$arr[]=$guarantorData['mobile'];
						$arr[]=$patient_dob;
						$arr[]=$patient_ss;
						$arr[]=$guarantorData['ss'];
						$arr[]=$employerData['name'];
						$arr[]=$employerData['street'];
						$arr[]=$employerData['postal_code'];
						$arr[]=$employerData['city'];
						$arr[]=$employerData['state'];
						$arr[]=$lastPaymentDate;
						$arr[]=$insArrPri['ins_provider'];
						$arr[]=$insArrPri['ins_address'];
						$arr[]=$insArrPri['ins_city'];
						$arr[]=$insArrPri['ins_state'];
						$arr[]=$insArrPri['ins_zip'];
						$arr[]=$insArrPri['ins_phone']; 
						$arr[]=$insArrPri['group_number'];
						$arr[]=$insArrPri['policy_number'];
						$arr[]=$insArrSec['ins_provider'];
						$arr[]=$insArrSec['ins_address'];
						$arr[]=$insArrSec['ins_city'];
						$arr[]=$insArrSec['ins_state'];
						$arr[]=$insArrSec['ins_zip'];
						$arr[]=$insArrSec['ins_phone']; 
						$arr[]=$insArrSec['group_number'];
						$arr[]=$insArrSec['policy_number'];
						$arr[]=$cpt_val;
						$arr[]=$patient_RF;
						$arr[]=$patient_notes;
						$arr[]=$patientStatus;
						$arr[]=$arDay;
						$arr[]=$acc_statement_count;
						$arr[]='';
						fputcsv($fp,$arr, ",","\"");		
				
				
				$patient_name_csv="<a href='javascript:void();' onClick=new_window('".$GLOBALS['rootdir']."/reports/set_session.php?patient=".$patient_id."'); class='text_10'>".$patient_name."</a>";

				$data_csv .= <<<DATA
					<tr bgcolor="#FFFFFF">
						<td style="text-align:left;" class="text_10" valign="top">
							<label class="checkbox checkbox-inline pointer">
								<input style="cursor:pointer;" type="checkbox" name="chk_box[]" id="chk_all_$encounterId" class="chk_all" value="$encounterId" $chkSelected>
								<label for="chk_all_$encounterId"></label>
							</label>
							<input type="hidden" name="pat_collection[]" value="$patient_id">
						</td>
						<td style="text-align:left;" class="text_10" valign="top">$physicianName</td>
						<td style="text-align:left;" class="text_10" valign="top">$facilityName</td>
						<td style="text-align:left;" class="text_10" valign="top">$patient_name_csv</td>
						<td style="text-align:center;" class="text_10" valign="top">$date_of_service</td>
						<td style="text-align:center;" class="text_10" valign="top">$collectionDate</td>
						<td style="text-align:center;" class="text_10" valign="top">$letterSentDate</td>
						<td style="text-align:left;" class="text_10" valign="top">$next_action_code</td>
						<td style="text-align:right;" class="text_10" valign="top">$totalAmt</td>
						<td style="text-align:right;" class="text_10" valign="top">$patientPaidAmt</td>
						<td style="text-align:right;" class="text_10" valign="top">$totalBalance</td>
						<td style="text-align:right;" class="text_10" valign="top">$collectionAmount</td>
					</tr>
DATA;


				
				if($e == (count($patientIdArr) -1)){
				
					//---- GET GRAND TOTAL AMOUNT -------
					$subTotalChargesDue = array_sum($subTotalChargesDueArr);
					$subTotalPayments = array_sum($subTotalPaymentsArr);
					$subTotalBalance = array_sum($subTotalBalanceArr);
					$subTotalCollection = array_sum($subTotalCollectionArr);
					
					//---- NUMBER FORMAT FOR SINGLE PROVIDER ----
					$subTotalChargesDue = ''.$showCurrencySymbol.''.number_format($subTotalChargesDue,2);
					$subTotalPayments = $subTotalPayments > 0 ? ''.$showCurrencySymbol.''.number_format($subTotalPayments,2) : '0.00';
					$subTotalBalance = $subTotalBalance > 0 ? ''.$showCurrencySymbol.''.number_format($subTotalBalance,2) : '0.00';
					$subTotalCollection = $subTotalCollection > 0 ? ''.$showCurrencySymbol.''.number_format($subTotalCollection,2) : '0.00';
					$data .= <<<DATA
						
						<tr bgcolor="#FFFFFF">
							<td style="text-align:right;" class="text_10b" colspan="$sub_colspan">Sub Total : </td>
							<td style="text-align:right;" class="text_10b">$subTotalChargesDue</td>
							<td style="text-align:right;" class="text_10b">$subTotalPayments</td>
							<td style="text-align:right;" class="text_10b">$subTotalBalance</td>
							<td style="text-align:right;" class="text_10b">$subTotalCollection</td>
						</tr>	
						
						<tr bgcolor="#FFFFFF"><td colspan="$main_colspan"></td></tr>
DATA;
					$data_csv .= <<<DATA
						
						<tr bgcolor="#FFFFFF">
							<td class="text_10b"></td>
							<td style="text-align:right;" class="text_10b" colspan="$sub_colspan">Sub Total : </td>
							<td style="text-align:right;" class="text_10b">$subTotalChargesDue</td>
							<td style="text-align:right;" class="text_10b">$subTotalPayments</td>
							<td style="text-align:right;" class="text_10b">$subTotalBalance</td>
							<td style="text-align:right;" class="text_10b">$subTotalCollection</td>
						</tr>
							
						<tr bgcolor="#FFFFFF"><td colspan="$main_colspan"></td></tr>
DATA;
				}
			}	
			
		}		
	}
	fclose ($fp);
	//---- GET GRAND TOTAL AMOUNT -------
	$TotalChargesDue = array_sum($TotalChargesDueArr);
	$TotalPayments = array_sum($TotalPaymentsArr);
	$TotalInsuranceDue = array_sum($TotalInsuranceDueArr);
	$TotalPatientDue = array_sum($TotalPatientDueArr);
	$TotalCreditAmount = array_sum($TotalCreditAmountArr);
	$total_write_off = array_sum($totalWriteOffArr);
	$TotalBalance = array_sum($TotalBalanceArr);
	$TotalCollection = array_sum($TotalCollectionArr);
	
	//---- NUMBER FORMAT FOR GRAND TOTAL AMOUNT ------
	$TotalChargesDue = ''.$showCurrencySymbol.''.number_format($TotalChargesDue,2);
	$TotalPayments = $TotalPayments > 0 ? ''.$showCurrencySymbol.''.number_format($TotalPayments,2) : '0.00';
	$TotalInsuranceDue = $TotalInsuranceDue > 0 ? ''.$showCurrencySymbol.''.number_format($TotalInsuranceDue,2) : '';
	$TotalPatientDue = $TotalPatientDue > 0 ? ''.$showCurrencySymbol.''.number_format($TotalPatientDue,2) : '';
	$TotalCreditAmount = $TotalCreditAmount > 0 ? ''.$showCurrencySymbol.''.number_format($TotalCreditAmount,2) : '';
	$total_write_off = $total_write_off > 0 ? ''.$showCurrencySymbol.''.number_format($total_write_off,2) : '';
	$TotalBalance = $TotalBalance > 0 ? ''.$showCurrencySymbol.''.number_format($TotalBalance,2) : '0.00';
	$TotalCollection = $TotalCollection > 0 ? ''.$showCurrencySymbol.''.number_format($TotalCollection,2) : '0.00';
	
	//--- GET HEADER DATA -----
	if(empty($data) == false){
		$curDate = date(''.$phpDateFormat.' H:i A');	
		
		//-- OPERATOR INITIAL -------
		$authProviderNameArr = preg_split('/ /',strtoupper($_SESSION['authProviderName']));
		$opInitial = $authProviderNameArr[1][0];
		$opInitial .= $authProviderNameArr[0][0];
		$opInitial = strtoupper($opInitial);
		$printFile = true;
		
		//--- PAGE HEADER DATA --
		$page_head_data =<<<DATA
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
				<tr class="rpt_headers">
					<td class="rptbx1" style="width:342px;">
						Collections Reports
					</td>
					<td class="rptbx2" style="width:350px;">
						Selected Collection Date : From $Sdate&nbsp;To $Edate
					</td>
					<td class="rptbx3" style="width:350px;">
						Created by : $opInitial on $curDate
					</td>
				</tr>
				<tr class="rpt_headers">
					<td class="rptbx1" >
						Selected Groups : $group_name
					</td>
					<td class="rptbx2" >Selected Facility : $practice_name</td>
					<td class="rptbx3" >Selected Physician : $physician_name</td>
				</tr>
				<tr><td colspan="4" style="height:1px;"></td></tr>
			</table>
DATA;
		
		$pdf_file_data = <<<DATA
			<page backtop="15mm" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center; width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>		
				$page_head_data
				<table width="auto" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td style="text-align:center;width:25px" class="text_10">Phy.</td>
						<td style="text-align:center;width:$fac_title_width" class="text_10">Facility</td>
						<td style="text-align:center;width:$pat_width" class="text_10">Patient Name</td>
						<td style="text-align:center;width:$dos_width" class="text_10">DOS</td>
						<td style="text-align:center;width:$coll_date" class="text_10">Coll. Date</td>
						<td style="text-align:center;width:$letter_date" class="text_10">Letter Sent Date</td>
						<td style="text-align:center;width:110px" class="text_10">Action Code</td>
						<td style="text-align:right;width:100px" class="text_10">Total Charges</td>
						<td style="text-align:right;width:110px" class="text_10">Total Payments</td>
						<td style="text-align:right;width:110px" class="text_10">Total Pat. Bal.</td>
						<td style="text-align:right;width:116px" class="text_10">Coll. Amount</td>
					</tr>
				</table>
			</page_header>
			<table width="100%" border="0" cellpadding="10" cellspacing="1">
				$data
				
				<tr>
					<td align="left" class="text_10b" colspan="4">Total Patients ($totalPatCounts)</td>
					<td align="right" class="text_10b" colspan="3">Grand Total : </td>
					<td align="right" class="text_10b">$TotalChargesDue</td>
					<td align="right" class="text_10b">$TotalPayments</td>					
					<td align="right" class="text_10b">$TotalBalance</td>
					<td align="right" class="text_10b">$TotalCollection</td>
				</tr>	
			</table>
			</page>
DATA;
		
		//--- CSV FILE CONTENT ---
		$csv_file_content = <<<DATA
			$page_head_data
			<form action="" method="post" name="frm_csv">
			<table class="rpt_table rpt rpt_table-bordered table" style="width:100%">
				<tr>
					<td style="text-align:center;" width="10" class="text_10">
						<label class="checkbox checkbox-inline pointer">
							<input style="cursor:pointer;" type="checkbox" name="chk_all" id="chk_all" onclick="chk_all_fun(this.checked,'');">
							<label for="chk_all"></label>
						</label>
					</td>
					<td style="text-align:center;" width="10" class="text_10">Phy.</td>
					<td style="text-align:center;" width="90" class="text_10">Facility</td>
					<td style="text-align:center;" width="180" class="text_10">Patient Name</td>
					<td style="text-align:center;" width="101" class="text_10">DOS</td>
					<td style="text-align:center;" width="110" class="text_10">Coll. Date</td>
					<td style="text-align:center;" width="110" class="text_10">Letter Sent Date</td>
					<td style="text-align:center;" width="100" class="text_10">Action Code</td>
					<td style="text-align:center;" width="120" class="text_10">Total Charges</td>
					<td style="text-align:center;" width="120" class="text_10">Total Payments</td>
					<td style="text-align:center;" width="120" class="text_10">Total Pat. Bal.</td>
					<td style="text-align:center;" width="120" class="text_10">Coll. Amount</td>
				</tr>
				$data_csv
				<tr bgcolor="#FFFFFF">
					<td style="text-align:left;" class="text_10b" colspan="4">Total Patients ($totalPatCounts)</td>
					<td style="text-align:right;" class="text_10b" colspan="4">Grand Total : </td>
					<td style="text-align:right;" class="text_10b">$TotalChargesDue</td>
					<td style="text-align:right;" class="text_10b">$TotalPayments</td>					
					<td style="text-align:right;" class="text_10b">$TotalBalance</td>
					<td style="text-align:right;" class="text_10b">$TotalCollection</td>
				</tr>
					
			</table>
		</form>
DATA;
	}
	
// SET ARRAY FOR PRINT LETTER
$strTotAmount = htmlentities(serialize($arrTotAmountLetter));
$strTotBalance = htmlentities(serialize($arrTotBalanceLetter));
$csv_file_content.='<input type="hidden" name="patAmount" id="patAmount" value="'.$strTotAmount.'">
<input type="hidden" name="patBalance" id="patBalance" value="'.$strTotBalance.'">';

}else{
	$csv_file_content = '<div class="text-center alert alert-info">No Record Found.</div>';
}


$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
echo $styleHTML.$csv_file_content;

$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
$strHTML = $stylePDF.$pdf_file_data;

$file_location = write_html($strHTML);
//$dbtemp_name_CSV=$styleHTML.$csv_file_content;
?>