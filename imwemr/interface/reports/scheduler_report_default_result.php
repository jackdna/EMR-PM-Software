<?php
ini_set("memory_limit","3072M");
set_time_limit(0);
$page_data = NULL;
$pdf_data = NULL;
$msgDisp = "No record exists.";
$curDate = date(''.$phpDateFormat.' H:i A');
if($Start_date == ""){
	$Start_date = $curDate;
	$End_date = $curDate;
}

if($_POST['form_submitted']){
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
	
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$createdBy = ucfirst(trim($op_name_arr[1][0]));
	$createdBy .= ucfirst(trim($op_name_arr[0][0]));

	$facility_name_str = join(',',$facility_name);
	$rqArrAppStatus = $_REQUEST['ap_status'];
	$rqAppStatus = join(',',$rqArrAppStatus);
	
	$rqArrProcedures = $_REQUEST['procedures'];
	$rqProcedures = join(',',$rqArrProcedures);
	
	$rqArrProceduresSec = $_REQUEST['proceduresSec'];
	$rqProceduresSec = join(',',$rqArrProceduresSec);
	
	$rqArrProceduresTer = $_REQUEST['proceduresTer'];
	$rqProceduresTer = join(',',$rqArrProceduresTer);
	
	$rqArrPhyId = $_REQUEST['phyId'];
	$rqPhyId = join(',',$rqArrPhyId);
	
	$rqArrHeard = $_REQUEST['heard'];
	$rqHeard = join(',',$rqArrHeard);
	
	$rqArrInsType = $_REQUEST['ins_type'];
	foreach ($rqArrInsType as $key => $value) {
		$rqArrInsType[$key] = "'".$value."'";
	}
	$rqInsType = join(',',$rqArrInsType);
	
	$rqArrInsProvider = $_REQUEST['insId'];	
	$rqInsProvider = join(',',$rqArrInsProvider);
	
	$rqArrDxcode = $_REQUEST['Dxcode'];	
	foreach ($rqArrDxcode as $key => $value) {
		$rqArrDxcode[$key] = "'".$value."'";
	}
	$rqDxcode = join(',',$rqArrDxcode);
	
	//ICD10
	$Dxcodes = $_REQUEST['Dxcode10'];	
	if(sizeof($Dxcodes)>0){
		$rqDxcode10=implode(',',$Dxcodes);
		$rqDxcode10= "'".str_replace(",", "','", $rqDxcode10)."'";
	}	
	
	$rqIdReffPhysician = $_REQUEST['id_reff_physician'];	
	
	$groups = join(',',$_REQUEST['groups']);
	
	//--- CHANGE DATE FORMAT -----------
	$StartDate = getDateFormatDB($Start_date);
	$EndDate = getDateFormatDB($End_date);
	
	if(empty($rqHeard) === true && trim($_REQUEST["inc_ins_id"]) != 1){
		$td_width = '104px';
		$cnt_td_width = '100px';
	}else{
		$td_width = '100px';
		$cnt_td_width = '80px';
	}
	
	$arr_refphy_names=array();
	if(empty($rqIdReffPhysician) == false){
		$rs=imw_query("Select physician_Reffer_id, FirstName, MiddleName, LastName FROM refferphysician WHERE physician_Reffer_id IN(".$rqIdReffPhysician.")");
		while($res  =imw_fetch_assoc($rs)){
			$refname= core_name_format($res['LastName'], $res['FirstName'], $res['MiddleName']);
			$arr_refphy_names[$res['physician_Reffer_id']]= $refname;
		}
	}
		
	$where_query='';	
	$ins_policy_inc = "";
	if(trim($_REQUEST["inc_ins_id"]) == 1){
		$ins_policy_inc = ", insd.policy_number as ins_policy_no";	
		$where_query .= " AND (date_format(insd.effective_date,'%Y-%m-%d')<='$StartDate' and (date_format(insd.expiration_date,'%Y-%m-%d')>='$EndDate' or  date_format(insd.expiration_date,'%Y-%m-%d')='0000-00-00'))";
	}		
	
	//--- INITIAL POPULATION OF PATIENTS FROM SCHEDULER DURING GIVEN PERIOD-------
	$query = "SELECT appt.sa_doctor_id as sdocid, appt.sa_patient_id as spid, facility.name,
			  appt.sa_patient_app_status_id as apptStatus, appt.sa_doctor_id,
			  appt.procedureid as procId, DATE_FORMAT(appt.sa_app_start_date, '".get_sql_date_format()."') as apptDate, 
			  TIME_FORMAT(appt.sa_app_starttime, '%h:%i %p') as starttime,
			  appt.procedure_site, appt.procedure_sec_site, appt.procedure_ter_site,   
			  TIME_FORMAT(appt.sa_app_endtime, '%h:%i %p') as endtime, u.lname as ulname, 
			  u.fname as ufname, u.mname as umname, u.sign_path as usersign, pd.lname as plname, pd.fname as pfname, pd.mname as pmname, 
			  pd.street,pd.street2, pd.city, pd.state, pd.postal_code, pd.email, pd.primary_care_id,
			  hau.heard_options as heard_options, ss.status_name as ssname, sp.proc as procedurename, sec_sp.proc as secondary_procedurename, ter_sp.proc as tertiary_procedurename
			  ".$ins_policy_inc."
			  FROM schedule_appointments appt 
			  INNER JOIN patient_data pd ON pd.id = appt.sa_patient_id";
	if(empty($rqIdReffPhysician) == false){
		$query .= " and pd.primary_care_id IN ($rqIdReffPhysician)";
	}
	$query .= " LEFT JOIN users u ON u.id = appt.sa_doctor_id
			  LEFT JOIN heard_about_us hau ON hau.heard_id = pd.heard_abt_us
			  LEFT JOIN schedule_status ss ON ss.id = appt.sa_patient_app_status_id
			  LEFT JOIN facility ON facility.id = appt.sa_facility_id
			  LEFT JOIN slot_procedures sp ON sp.id = appt.procedureid
			  LEFT JOIN slot_procedures sec_sp ON sec_sp.id = appt.sec_procedureid
			  LEFT JOIN slot_procedures ter_sp ON ter_sp.id = appt.tertiary_procedureid
			  ";
	if((empty($rqInsType) == false) || (empty($rqInsProvider) == false) || trim($_REQUEST["inc_ins_id"]) == 1){ 
		$query .= " LEFT JOIN insurance_data insd ON insd.ins_caseid = appt.case_type_id && insd.ins_caseid != 0 ";
	}
	
	if((empty($rqInsType) != false) && (empty($rqInsProvider) != false) && trim($_REQUEST["inc_ins_id"]) == 1)	
	{
		$query .= " && insd.type = 'primary' ";		
	}
	
	$query .=  " WHERE appt.sa_app_start_date between '$StartDate' AND '$EndDate'";
	
			  
	$orderBy = " ,appt.sa_app_start_date asc, appt.sa_app_starttime";

	if(sizeof($operator_id) > 0){
		$operator_id_str = join(',',$operator_id);
		$qry = "select username from users where id in ($operator_id_str)";
		$user_query = imw_query($qry);
		$userQryRes = $userNameArr = array();
		while($qryRes  =imw_fetch_assoc($user_query)){
			$userQryRes[] = $qryRes;
		}	
		for($u=0;$u<count($userQryRes);$u++){
			$userNameArr[] = "'".$userQryRes[$u]['username']."'";
		}
		$userNameStr = join(',',$userNameArr);
		$query .= " and appt.sa_madeby IN ($userNameStr)";
	}
	
	if(empty($facility_name_str) == false){
		$query .= " and appt.sa_facility_id IN ($facility_name_str)";
	}
	if(empty($rqPhyId) == false){
		$query .= " and appt.sa_doctor_id IN ($rqPhyId)";
	}
	
	if(empty($groups) === false){
		$query .= " and facility.default_group in($groups)";
	}
	if($rqAppStatus != NULL ){
		$query .= " and appt.sa_patient_app_status_id IN ($rqAppStatus)";
		$orderBy .= ", appt.sa_patient_app_status_id";
	}
	if(empty($rqProcedures) == false){
		$query .= " and appt.procedureid IN ($rqProcedures)";
		$orderBy .= ", appt.procedureid";
	}
	if(empty($rqProceduresSec) == false){
		$query .= " and appt.sec_procedureid IN ($rqProceduresSec)";
		$orderBy .= ", appt.sec_procedureid";
	}
	if(empty($rqProceduresTer) == false){
		$query .= " and appt.tertiary_procedureid IN ($rqProceduresTer)";
		$orderBy .= ", appt.tertiary_procedureid";
	}
	$hrd_cols = 4;
	if(empty($rqHeard) == false){
		$query .= " and pd.heard_abt_us IN ($rqHeard)";
		$hrd_cols = 5;
	}
	if(trim($_REQUEST["inc_ins_id"]) == 1)
	{
		$hrd_cols += 1;
	}
	
	if(empty($rqInsType) == false){
		$query .= " and insd.type IN ($rqInsType)";
	}	
	if(empty($rqInsType) != false && trim($_REQUEST["inc_ins_id"]) == 1){
		//$query .= " and insd.type IN ('primary')";
	}		
	if(empty($rqInsProvider) == false){
		$query .= " and insd.provider IN ($rqInsProvider)";
	}
	if($aging_from>0) {
		$query .= " and DATEDIFF('".date('Y-m-d')."', pd.DOB) / 365 >=".$aging_from;
	}
	if($aging_to>0) {
		$query .= " and DATEDIFF('".date('Y-m-d')."', pd.DOB) / 365 <=".$aging_to;
	}
	$query .= $where_query;
	$query .= " order by u.lname, u.fname $orderBy";
	$query_res = imw_query($query);
	$num_row = imw_num_rows($query_res);
	$arrPatIds = array();
	while($qryRes  =imw_fetch_assoc($query_res)){
		$arrPatIds[$qryRes['spid']]=$qryRes['spid'];
		$result[] = $qryRes;
	}	
	$arr_physician = array();
	$arr_patient_id = array();
	$arr_apStatus = array();
	$arr_apDt = array();
	$arr_procedure = array();
	$arr_heardAbt = array();
	
	if(count($num_row) > 0){
		$pat_for_sch_arr=array();
		$pat_for_sch_appt_arr=array();
		if(empty($rqDxcode10) == false){ 
			$arrPatHasDXCode=array();
			//CHECK PATIENTS IN SUPERBILL
			if(sizeof($arrPatIds)>0){
				$strPatIds=implode(',', $arrPatIds);
				$qryGetDxCodeSuperBill = "SELECT sb.patientId AS sbPatId, procInfo.id as procInfoId, 
				procInfo.dx1,procInfo.idSuperBill FROM superbill sb
				INNER JOIN procedureinfo procInfo ON (procInfo.idSuperBill = sb.idSuperBill)
				where sb.patientId IN(".$strPatIds.") and sb.postedStatus = '0' and procInfo.delete_status = '0' 
				and (sb.dateOfService between '$StartDate' AND '$EndDate') 
				AND (procInfo.dx1 IN ($rqDxcode10) OR procInfo.dx2 IN ($rqDxcode10) OR 
				procInfo.dx3 IN ($rqDxcode10) OR procInfo.dx4 IN ($rqDxcode10))";	
				$rs = imw_query($qryGetDxCodeSuperBill);
				while($res=imw_fetch_array($rs)){
					$arrPatHasDXCode[$res['patientId']]=$res['patientId'];
					unset($arrPatIds[$res['patientId']]);
				}
				unset($rs);
			}

			//CHECK PENDING PATIENTS IN PATIENT CHARGELIST
			if(sizeof($arrPatIds)>0){
				$strPatIds=implode(',', $arrPatIds);
				$qryGetDxCodeAcc = "SELECT pcl.patient_id 
				FROM patient_charge_list pcl
				INNER JOIN patient_charge_list_details pcld ON 
				(pcld.charge_list_id = pcl.charge_list_id )
				where pcl.patient_id IN(".$strPatIds.") 
				and (pcl.date_of_service	between '$StartDate' AND '$EndDate')
				AND (pcld.diagnosis_id1 IN ($rqDxcode10) OR pcld.diagnosis_id2 IN ($rqDxcode10) 
					OR pcld.diagnosis_id3 IN ($rqDxcode10) OR pcld.diagnosis_id4 IN ($rqDxcode10))";
				$rs = imw_query($qryGetDxCodeAcc);
				while($res=imw_fetch_array($rs)){
					$arrPatHasDXCode[$res['patient_id']]=$res['patient_id'];
					unset($arrPatIds[$res['patient_id']]);
				}
				unset($rs);
			}		
			unset($arrPatIds);
		}
				
		for($i=0; $i<count($result); $i++){
			$sa_patient_app_status_id = $result[$i]['apptStatus'];
			$intPatIdSA = $result[$i]['spid'];
			if($i==0) {
				$intPatIdSAComma = $intPatIdSA;
			}else {
				$intPatIdSAComma .= ','.$intPatIdSA;
			}
			$pat_for_sch_arr[$result[$i]['spid']]=$result[$i]['spid'];
			$pat_for_sch_appt_arr[$result[$i]['spid']]=$result[$i]['ulname']."~~".$result[$i]['ufname']."~~".$result[$i]['apptDate']."~~".$result[$i]['starttime']."~~".$result[$i]['name']."~~".$result[$i]['procedurename']."~~".$result[$i]['usersign']."~~".$result[$i]['secondary_procedurename']."~~".$result[$i]['tertiary_procedurename'];
			$blProcessDxRecordPat = false;
			if(empty($rqDxcode10) == false){
				if($arrPatHasDXCode[$intPatIdSA]){
					$blProcessDxRecordPat = true;		
				}
			}
			else{
				$blProcessDxRecordPat = true;
			}
			
			if($blProcessDxRecordPat == true){
				$page_data_arr = array();
				//--- PROVIDER NAME FORMAT ---
				$pro_name_arr = array();
				$pro_name_arr["LAST_NAME"] = $result[$i]['ulname'];
				$pro_name_arr["FIRST_NAME"] = $result[$i]['ufname'];
				$pro_name_arr["MIDDLE_NAME"] = $result[$i]['umname'];
				$pro_name = changeNameFormat($pro_name_arr);
				$sa_doctor_id = $result[$i]["sa_doctor_id"];
				$page_data_arr['PROVIDER_NAME'] = $pro_name;
				
				//--- PATIENT NAME FORMAT ---
				$pat_name_arr = array();
				$pat_name_arr["LAST_NAME"] = $result[$i]['plname'];
				$pat_name_arr["FIRST_NAME"] = $result[$i]['pfname'];
				$pat_name_arr["MIDDLE_NAME"] = $result[$i]['pmname'];
				$pat_name = changeNameFormat($pat_name_arr);				
				$page_data_arr['PATIENT_NAME'] = $pat_name." - ".$intPatIdSA;
				
				$page_data_arr["LNAME"] = $result[$i]['plname'];
				$page_data_arr["FNAME"] = $result[$i]['pfname'];

				$page_data_arr['STREET1'] = trim($result[$i]['street']);
				$page_data_arr['STREET2'] = trim($result[$i]['street2']);
				$page_data_arr['CITY'] = $result[$i]['city'];
				$page_data_arr['STATE'] = $result[$i]['state'];
				$page_data_arr['POSTAL_CODE'] = $result[$i]['postal_code'];
				$page_data_arr['EMAIL'] = $result[$i]['email'];
				
				if($sa_patient_app_status_id == 201){
					$result[$i]['ssname'] = 'ToDo';
				}
				
				$page_data_arr['APPT_STATUS'] = ucwords(($result[$i]['ssname']) ? $result[$i]['ssname'] : "New Appointment");
				$page_data_arr['APPT_DATE'] = $result[$i]['apptDate'];
				$page_data_arr['APPT_TIME'] = trim($result[$i]['starttime']).'&nbsp;to&nbsp;'.trim($result[$i]['endtime']);
				$page_data_arr['PROCEDURE'] = $result[$i]['procedurename'];
				$page_data_arr['SECONDARY_PROCEDURE'] = $result[$i]['secondary_procedurename'];
				$page_data_arr['TERTIARY_PROCEDURE'] = $result[$i]['tertiary_procedurename'];
				$page_data_arr['PROCEDURE_SITE'] = $result[$i]['procedure_site'];
				$page_data_arr['SEC_PROCEDURE_SITE'] = $result[$i]['procedure_sec_site'];
				$page_data_arr['TER_PROCEDURE_SITE'] = $result[$i]['procedure_ter_site'];
				$page_data_arr['INSURANCE_POLICY_NO'] = $result[$i]['ins_policy_no'];				
				$page_data_arr['HEARD_OPTIONS'] = $result[$i]['heard_options'];
				$page_data_arr['FACILITY_NAME'] = $result[$i]['name'];
				$page_data_arr['PATIENT_ID'] = $intPatIdSA;
				
				if(empty($rqIdReffPhysician) == false){
					$ref_phy_id= $result[$i]['primary_care_id'];
					$arr_physician[$ref_phy_id][] = $page_data_arr;
				}else{
					$arr_physician[$sa_doctor_id][] = $page_data_arr;
				}
			}
		}
		
		$pat_for_sch_imp=implode(',',array_unique($pat_for_sch_arr));
		if($report_type=="Address Labels"){
			if(count($pat_for_sch_arr)>0){
				include_once "new_account_recall_letter.php";
			}
		}else if($report_type=="Recall letter"){
			
			if(count($pat_for_sch_arr)>0){
				//$pat_for_srh_appt_imp=implode(',',array_unique($pat_for_sch_appt_arr));
				array_unique($pat_for_sch_appt_arr);
				include_once "new_account_recall_letter.php";
			}
		}else if($report_type=="Post Card"){
			if(count($pat_for_sch_arr)>0){
				include_once "new_account_recall_letter.php";
			}
		}else{
			if(count($arr_physician) > 0){
				
				//MAKING OUTPUT DATA
				$file_name="scheduler_report_".time().".csv";
				$csv_file_name= write_html("", $file_name);

				if(file_exists($csv_file_name)){
					unlink($csv_file_name);
				}
				$fp = fopen ($csv_file_name, 'a+');	
				$arr=array();
				$arr[]="Scheduler Report";
				$arr[]="Report Period:" .$Start_date." To :" .$End_date;
				$arr[]="Created by" .$createdBy." on " .get_date_format(date("Y-m-d"))." ".date("h:i A");
				fputcsv($fp,$arr, ",","\"");
				
				$fp = fopen ($csv_file_name, 'a+');
				$CSVrowTitle="PHYSICIAN";
				if(empty($rqIdReffPhysician) == false){
					$CSVrowTitle="REFERRING PHYSICIAN";
				}
				$arr=array();
				$arr[]=$CSVrowTitle;
				$arr[]="Pt Id";
				$arr[]="Pt Last Name";
				$arr[]="Pt First Name";
				$arr[]="Street Address1";
				$arr[]="Street Address2";
				$arr[]="City";
				$arr[]="State";
				$arr[]="Zip";
				$arr[]="Email";
				$arr[]="Facility";
				$arr[]="Appt. Date";
				$arr[]="Appt. Time";
				$arr[]="Appointment Status";
				$arr[]="Procedure/Site";
				$arr[]="Secondary Procedure/Site";
				$arr[]="Tertiary Procedure/Site";
				$arr[]="Future Appt.";
				if(trim($_REQUEST["inc_ins_id"]) == 1){
					$arr[]="Ins. Policy #";
				}
				if(empty($rqHeard) == false){
					$arr[]="Heard About Us";
				}
				fputcsv($fp,$arr, ",","\"");
				$fp = fopen ($csv_file_name, 'a+');
				$oldPhysician = NULL;
				$physician_id_arr = array_keys($arr_physician);
				$cntPack = 0;
				for($i=0;$i<count($physician_id_arr);$i++){
					$physician_id = $physician_id_arr[$i];
					$data_arr = $arr_physician[$physician_id];
					$PROVIDER_NAME = $data_arr[0]['PROVIDER_NAME'];
					$pro_cols = 18;
					if(empty($rqHeard) === false){
						$pro_cols = 19;
						$td_width = '50px';
					}
					if(trim($_REQUEST["inc_ins_id"]) == 1)
					{
						$pro_cols += 1;
					}

					$rowTitle="PHYSICIAN";
					if(empty($rqIdReffPhysician) == false){
						$PROVIDER_NAME= $arr_refphy_names[$physician_id];
						$rowTitle="REFERRING PHYSICIAN";
					}
					
					$page_data .= <<<DATA
							<tr>
								<td class="text_b_w alignLeft" colspan="$pro_cols">$rowTitle : $PROVIDER_NAME</td>
							</tr>						
DATA;
					$pro_cols_pdf = ($pro_cols-2);
					$pdf_data .= <<<DATA
							<tr>
								<td class="text_b_w alignLeft" colspan="$pro_cols_pdf">$rowTitle : $PROVIDER_NAME</td>
							</tr>						
DATA;

					for($jb=0;$jb<=count($data_arr);$jb++){
						$var_arr[] = $data_arr[$jb]['INSURANCE_POLICY_NO'];
					}
					
					$allEmpty = true;
					foreach( $var_arr as $key => $val) {
						if($val != '') {
							$allEmpty = false;
							break;
						}
					}
					
					if($allEmpty == true){
						$ins_width = '116px';
					}else{
						$ins_width = '50px';
					}
					for($p=0,$cnt=1;$p<count($data_arr);$p++,$cnt++){					
						$cntPack++;
						$patId = $data_arr[$p]['PATIENT_ID'];
						$patientName = $data_arr[$p]['PATIENT_NAME'];
						
						$street1 = $data_arr[$p]['STREET1'];
						$street2 = $data_arr[$p]['STREET2'];
						$city = $data_arr[$p]['CITY'];
						$state = $data_arr[$p]['STATE'];
						$postal_code = $data_arr[$p]['POSTAL_CODE'];
						$email = $data_arr[$p]['EMAIL'];
						$facility = $data_arr[$p]['FACILITY_NAME'];
						$app_status = $data_arr[$p]['APPT_STATUS'];
						$app_date = $data_arr[$p]['APPT_DATE'];
						$app_time = $data_arr[$p]['APPT_TIME'];
						$app_procedure = ($data_arr[$p]['PROCEDURE_SITE']!='')? $data_arr[$p]['PROCEDURE']."/".$data_arr[$p]['PROCEDURE_SITE'] : $data_arr[$p]['PROCEDURE'];
						$app_secondary_procedure = ($data_arr[$p]['SEC_PROCEDURE_SITE']!='')? $data_arr[$p]['SECONDARY_PROCEDURE']."/".$data_arr[$p]['SEC_PROCEDURE_SITE'] : $data_arr[$p]['SECONDARY_PROCEDURE'];
						$app_tertiary_procedure = ($data_arr[$p]['TER_PROCEDURE_SITE']!='')? $data_arr[$p]['TERTIARY_PROCEDURE']."/".$data_arr[$p]['TER_PROCEDURE_SITE'] : $data_arr[$p]['TERTIARY_PROCEDURE'];
						$ins_policy_no = $data_arr[$p]['INSURANCE_POLICY_NO'];
						$heard_abt = $data_arr[$p]['HEARD_OPTIONS'];
						$patientLName = $data_arr[$p]['LNAME'];
						$patientFName = $data_arr[$p]['FNAME'];
						
						$qryGetPtFutureApp = "SELECT DATE_FORMAT(sa_app_start_date, '".get_sql_date_format()."') as apptDate FROM schedule_appointments WHERE sa_patient_id = ".$patId." AND sa_app_start_date > '".$EndDate."' AND sa_patient_app_status_id NOT IN('18','203') ORDER BY sa_app_start_date ASC LIMIT 0,1";
						$rs = imw_query($qryGetPtFutureApp);
						$res=imw_fetch_assoc($rs);
						$future_app =  $res['apptDate'];							
						
						$rqHeardTd = NULL;
						if(empty($rqHeard) == false){
							$rqHeardTd 			= '<td class="valignTop text_10" style="text-align:left">'.$heard_abt.'</td>';
							if(trim($_REQUEST["inc_ins_id"]) != 1){
								$pdf_rqHeardTd 		= '<td class="valignTop text_10" style="text-align:left; width:102px;">'.$heard_abt.'</td>';
							}else{
								$pdf_rqHeardTd 		= '<td class="valignTop text_10" style="text-align:left; width:50px;">'.$heard_abt.'</td>';
							}
						}
						$appStatusTd 			= '<td class="valignTop text_10" style="text-align:left">'.$app_status.'</td>';
						$pdf_appStatusTd 		= '<td class="valignTop text_10" style="width:'.$td_width.'; text-align:left">'.wordwrap($app_status, 8, "<br>\n", true).'</td>';
						$chbxPackage 			= '';
						if($report_type=="Package"){
							$appStatusTd		= '';
							$pdf_appStatusTd 	= '';
							$chbxPackage 		= '<input type="checkbox" name="chbxPackage[]" id="chbxPackage'.$cntPack.'" checked value="'.$patId.'" onClick="top.fmain.chkPtIdComma();">';
						}
						
						
						$ins_policy_td = '';
						if(trim($_REQUEST["inc_ins_id"]) == 1)
						{
							$ins_policy_td = '<td class="valignTop text_10" style="text-align:left">'.$ins_policy_no.'</td>';
							if(empty($rqHeard) === true){	
								$pdf_ins_policy_td 	= '<td class="valignTop text_10" style="text-align:left; width:102px;">'.wordwrap($ins_policy_no, 8, "<br>\n", true).'</td>';
							}else{
								$pdf_ins_policy_td 	= '<td class="valignTop text_10" style="text-align:left; width:'.$ins_width.';">'.wordwrap($ins_policy_no, 8, "<br>\n", true).'</td>';
							}
							
						}

						//FOR CSV
		$arr=array();
		$arr[]=$PROVIDER_NAME;
		$arr[]=$patId;
		$arr[]=$patientLName;
		$arr[]=$patientFName;
		$arr[]=$street1;
		$arr[]=$street2;
		$arr[]=$city;
		$arr[]=$state;
		$arr[]=$postal_code;
		$arr[]=$email;
		$arr[]=$facility;
		$arr[]=$app_date;
		$arr[]=$app_time;
		$arr[]=$app_status;
		$arr[]=$app_procedure;
		$arr[]=$app_secondary_procedure;
		$arr[]=$app_tertiary_procedure;
		$arr[]=$future_app;
		if(trim($_REQUEST["inc_ins_id"]) == 1){
			$arr[]=$ins_policy_no;
		}
		if(empty($rqHeard) == false){
			$arr[]=$$heard_abt;
		}
		fputcsv($fp,$arr, ",","\"");
						
						
						$page_data .= <<<DATA
							<tr bgcolor="#FFFFFF">
								<td class="valignTop text_10 nowrap" style="text-align:center;">$cnt $chbxPackage</td>
								<td class="valignTop text_10" style="text-align:left">$patId</td>
								<td class="valignTop text_10" style="text-align:left">$patientLName</td>
								<td class="valignTop text_10" style="text-align:left">$patientFName</td>
								<td class="valignTop text_10" style="text-align:left">$street1</td>
								<td class="valignTop text_10" style="text-align:left">$street2</td>
								<td class="valignTop text_10" style="text-align:left">$city</td>
								<td class="valignTop text_10" style="text-align:left">$state</td>
								<td class="valignTop text_10" style="text-align:left">$postal_code</td>
								<td class="valignTop text_10" style="text-align:left">$email</td>
								<td class="valignTop text_10" style="text-align:left">$facility</td>
								<td class="valignTop text_10" style="text-align:center">$app_date</td>
								<td class="valignTop text_10" style="text-align:center">$app_time</td>
								$appStatusTd
								<td class="valignTop text_10" style="text-align:left">$app_procedure</td>
								<td class="valignTop text_10" style="text-align:left">$app_secondary_procedure</td>
								<td class="valignTop text_10" style="text-align:left">$app_tertiary_procedure</td>
								<td class="valignTop text_10" style="text-align:left">$future_app</td>
								$ins_policy_td
								$rqHeardTd
							</tr>
DATA;
						$email_str=array();
						if(strlen(trim($email)) > 12){
							$email_str = str_split($email,12);
							$email = "";
							for($e=0;$e<count($email_str);$e++){
								$email .= $email_str[$e]."<br>";
							} 
						}
						
						$patientName = 	wordwrap($patientName, 15, "<br>\n", true);
						$street1 = wordwrap($street1, 13, "<br>\n", true);
						$street1 =	wordwrap($street2, 13, "<br>\n", true);
						$email = wordwrap($email, 10, "<br>\n", true);
						$facility = wordwrap($facility, 12, "<br>\n", true);
						$app_procedure = wordwrap($app_procedure, 8, "<br>\n", true);
						
						$pdf_data .= <<<DATA
							<tr bgcolor="#FFFFFF">
								<td class="valignTop text_10" style="width:15px; text-align:center">$cnt</td>
								<td class="valignTop text_10" style="width:130px; text-align:left">$patientName</td>
								<td class="valignTop text_10" style="width:80px; text-align:left">$street1</td>
								<td class="valignTop text_10" style="width:70px; text-align:left">$street2</td>
								<td class="valignTop text_10" style="width:70px; text-align:left">$city</td>
								<td class="valignTop text_10" style="width:20px; text-align:left">$state</td>
								<td class="valignTop text_10" style="width:40px; text-align:left">$postal_code</td>
								<td class="valignTop text_10" nowrap="nowrap" style="width:80px; text-align:left">$email</td>
								<td class="valignTop text_10" style="width:70px; text-align:left">$facility</td>
								<td class="valignTop text_10" style="width:60px; text-align:center">$app_date</td>
								<td class="valignTop text_10" style="width:90px; text-align:center">$app_time</td>
								$pdf_appStatusTd
								<td class="valignTop text_10" style="width:$td_width; text-align:left">$app_procedure</td>
								$pdf_ins_policy_td
								$pdf_rqHeardTd
							</tr>
DATA;

					}
				}
			}
		}
	}
	
	
	//--- REPORT HEADER ---
	if(empty($page_data) === false){		
		$curDate.= date(" g:i A",time());
		$header_hrd_td = NULL;
		$content_hrd_td = NULL;
		$td_width = '100px';
		$cnt_td_width = '100px';
		if(empty($rqHeard) === false){
			if(trim($_REQUEST["inc_ins_id"]) != 1){
				$header_hrd_td = '<td style="width:95px; text-align:center;" class="text_b_w">Heard About Us</td>';
			}else{
				$header_hrd_td = '<td style="width:70px; text-align:center;" class="text_b_w">Heard About Us</td>';
			}
			
			$content_hrd_td = '<td style="width:50px; height:1px"></td>';
			$td_width = '80px';
			$cnt_td_width = '80px';
		}
		$ins_policy_td_print = '';
		if(trim($_REQUEST["inc_ins_id"]) == 1)
		{
			if(empty($rqHeard) === true){
				$ins_policy_td_print = '<td style="width:135px; text-align:center;" class="text_b_w">Ins. Policy #</td>';
			}else{
				$ins_policy_td_print = '<td style="width:70px; text-align:center;" class="text_b_w">Ins. Policy #</td>';
			}
			$ins_policy_td_print_content = '<td style="width:50px; height:1px"></td>';
			if(empty($rqHeard) === false){
				$td_width = '60px';
				$cnt_td_width = '60px';				
			}
			else
			{
				$td_width = '80px';
				$cnt_td_width = '80px';	
			}
		}		
		
		
		$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
		//--- PDF FILE CONTENT ---
		$pdf_file_content = <<<DATA
			$stylePDF
			<page backtop="14mm" backbottom="7mm">			
			<page_footer>
				<table style="width:100%;">
					<tr>
						<td style="text-align:center; width: 100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
				<tr class="rpt_headers">
					<td class="rptbx1" width="350">
						$dbtemp_name $summary_detail Report
					</td>
					<td class="rptbx2" width="350">
						Report Period: $Start_date - $End_date
					</td>
					<td class="rptbx3" width="350">
						Created by: $createdBy on $curDate
					</td>
				</tr>
			</table>
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
				<tr>
					<td style="width:15px; text-align:center;" class="text_b_w">#</td>
					<td style="width:135px; text-align:center;" class="text_b_w">Patient Name</td>
					<td style="width:85px; text-align:center;" class="text_b_w">St. Address1</td>
					<td style="width:77px; text-align:center;" class="text_b_w">St. Address2</td>
					<td style="width:70px; text-align:center;" class="text_b_w">City</td>
					<td style="width:20px; text-align:center;" class="text_b_w">State</td>
					<td style="width:40px; text-align:center;" class="text_b_w">Zip</td>
					<td style="width:80px; text-align:center;" class="text_b_w">Email</td>
					<td style="width:70px; text-align:center;" class="text_b_w">Facility</td>
					<td style="width:60px; text-align:center;" class="text_b_w">Appt. Date</td>
					<td style="width:90px; text-align:center;" class="text_b_w">Appt. Time</td>
					<td style="width:$td_width; text-align:center;" class="text_b_w">Appt. Status</td>
					<td style="width:$td_width; text-align:center;" class="text_b_w">Procedure/Site</td>
					$ins_policy_td_print
					$header_hrd_td
				</tr>
			</table>
			</page_header>
			<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" style="background-color:#c7c7c7">
				$pdf_data
			</table>
			</page>
DATA;
		$file_location = write_html($pdf_file_content);
	}
	
}
if($page_data != ""){
$appointStstus =  $insPol = $heardtd ="";
if($report_type != 'Package'){
	 $appointStstus = '<th class=\'text_b_w\' bgcolor=\'#FFFFFF\'>Appointment Status</th>';
}
if($inc_ins_id == '1'){
	 $insPol = '<th class=\'text_b_w\' bgcolor=\'#FFFFFF\'>Ins. Policy #</th>';      
}
if($rqHeard != ''){
    $heardtd = '<th class=\'text_b_w\' bgcolor=\'#FFFFFF\'>Heard About Us</th>';
}
$headertblString = "
	<table class=\"rpt rpt_table rpt_table-bordered rpt_padding\" width=\"100%\" >
		<tr class=\"rpt_headers\">
			<td class=\"rptbx1\" width=\"33%\">
				$dbtemp_name $summary_detail Report
			</td>
			<td class=\"rptbx2\" width=\"34%\">
				Report Period: $Start_date - $End_date
			</td>
			<td class=\"rptbx3\" width=\"33%\">
				Created by: $createdBy on $curDate
			</td>
		</tr>
	</table>
	<table class=\"rpt rpt_table rpt_table-bordered rpt_padding\" width=\"100%\" >
		<tr>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">#</th>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">Pt Id</th>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">Pt Last Name</th>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">Pt First Name</th>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">Street Address1</th>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">Street Address2</th>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">City</th>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">State</th>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">Zip</th>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">Email</th>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">Facility</th>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">Appt. Date</th>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">Appt. Time</th>
			$appointStstus
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">Procedure/Site</th>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">Secondary Procedure/Site</th>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">Tertiary Procedure/Site</th>
			<th class=\"text_b_w\" bgcolor=\"#FFFFFF\">Future Appt.</th>
			$insPol
			$heardth
		</tr>
		$page_data
	</table>";
	
	if($callFrom!='scheduled'){
		echo $headertblString;
	}
}

//--- SET CSV FILE CONTENT ----
if(empty($page_data) == false){
	if(!empty($appStatusTd)) {
		$showBtn = 1;
	} else{
		echo '<div class="text-center alert alert-info">No record exits.</div>';
	}
}

if($report_type=="Recall letter" || $report_type=="Address Labels" || $report_type=="Post Card"){
	if($num>0){
		echo '<div class="text-center alert alert-info">Please Check PDF.</div>';
	}else{
		echo '<div class="text-center alert alert-info">'.$msgDisp.'</div>';
	}
}

if($report_type=="Package"){
	if($cntPack == 0){
		echo '<div class="text-center alert alert-info">'.$msgDisp.'</div>';
	}
}

$package_category_id='';
if($report_type=='Package') {
	$package_category_id = $packageListId;	
}
$patient_id_comma = $intPatIdSAComma;
?>