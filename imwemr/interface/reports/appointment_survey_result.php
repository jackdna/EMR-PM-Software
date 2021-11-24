<?php 
if($_POST['form_submitted']){
	
	$op_name_arr = explode(', ',strtoupper($_SESSION['authProviderName']));
	$createdBy = ucfirst(trim($op_name_arr[1][0]));
	$createdBy .= ucfirst(trim($op_name_arr[0][0]));
	
	$searchDateFrom = $searchDateTo = "";
	$dispDateFrom = $_REQUEST['Start_date'];
	$dispDateTo = $_REQUEST['End_date'];
	
	$arrDateRange= $CLSCommonFunction->changeDateSelection();
	if($dayReport=='Daily'){
		$Start_date = $End_date = date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$Start_date = $arrDateRange['WEEK_DATE'];
		$End_date = date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$Start_date = $arrDateRange['MONTH_DATE'];
		$End_date = date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$Start_date = $arrDateRange['QUARTER_DATE_START'];
		$End_date = $arrDateRange['QUARTER_DATE_END'];
	}

	//--- CHANGE DATE FORMAT ----
	$date_format_SQL = get_sql_date_format();
	$searchDateFrom = getDateFormatDB($Start_date);
	$searchDateTo = getDateFormatDB($End_date);

	$selFac = implode("," ,$_REQUEST["facility"]);
	$selPro = implode("," ,$_REQUEST["provider"]);
	
	$query = "SELECT DISTINCT(appt.id) as saAppId, appt.sa_doctor_id as sdocid, appt.sa_patient_id as spid, facility.name, appt.sa_patient_app_status_id as apptStatus, appt.sa_doctor_id, appt.procedureid as procId, DATE_FORMAT(appt.sa_app_start_date, '%m-%d-%Y') as apptDate, TIME_FORMAT(appt.sa_app_starttime, '%h:%i %p') as starttime, appt.procedure_site, appt.procedure_sec_site, appt.procedure_ter_site, TIME_FORMAT(appt.sa_app_endtime, '%h:%i %p') as endtime, u.lname as ulname, u.fname as ufname, u.mname as umname, u.sign_path as usersign, pd.lname as plname, pd.fname as pfname, pd.mname as pmname, pd.street,pd.street2, pd.city, pd.state, pd.postal_code, pd.email, pd.primary_care_id, pd.DOB, DATE_FORMAT(pd.DOB,'".$date_format_SQL."') as date_of_birth, pd.phone_home, pd.phone_cell, pd.hipaa_text FROM schedule_appointments appt INNER JOIN patient_data pd ON pd.id = appt.sa_patient_id LEFT JOIN users u ON u.id = appt.sa_doctor_id LEFT JOIN schedule_status ss ON ss.id = appt.sa_patient_app_status_id LEFT JOIN facility ON facility.id = appt.sa_facility_id WHERE appt.sa_app_start_date between '$searchDateFrom' AND '$searchDateTo' AND ss.id NOT IN (0,3,17,18,201,202,203,271)";

	if(empty($selFac) == false){
		$query .= " AND appt.sa_facility_id IN ($selFac)";
	}
	if(empty($selPro) == false){
		$query .= " AND appt.sa_doctor_id IN ($selPro)";
	}
	$query .= " order by u.lname, u.fname ,appt.sa_app_start_date asc, appt.sa_app_starttime";
	$query_res = imw_query($query);
	$num_row = imw_num_rows($query_res);
	$arrPatIds = array();
	while($res=imw_fetch_assoc($query_res)){
		$appId = $res["saAppId"];
		$arrPatIds[$appId]=$res;
	}
	$appIds = array_keys($arrPatIds);
	$appIds = implode(',', $appIds);
	
	//CPT Code Data
	$cptArr = array();
	$cptqry = imw_query("SELECT cpt_fee_id, cpt4_code, cpt_desc FROM cpt_fee_tbl"); 
	while($cptqryRes = imw_fetch_assoc($cptqry)){
		$cpt_fee_id = $cptqryRes['cpt_fee_id'];
		$cpt4_code = $cptqryRes['cpt4_code'];
		$cpt_desc = $cptqryRes['cpt_desc'];
		$cptArr[$cpt4_code] = $cpt_desc;
	}
	
	//Pt CPT code
	$cptmainArr = array();
	$superbill_query = "Select patientId, procOrder, sch_app_id from superbill where sch_app_id in($appIds) AND del_status='0'";
	$superbill_res = imw_query($superbill_query);
	$arrCPTCodes = array();
	while($sup_res=imw_fetch_assoc($superbill_res)){
		$ptCptCodes = '';
		$ptId = $sup_res["patientId"];
		$ptCptCodes = $sup_res["procOrder"];
		
		if(empty($ptCptCodes) == false){
			$tmpArr = explode(',', $ptCptCodes);
			
			if(count($tmpArr) > 0){
				foreach($tmpArr as $cptCode){
					if($cptArr[$cptCode]){
						$cptmainArr[$ptId][$cptCode] = $cptArr[$cptCode];
					}
				}
			}
		}
	}
	
	$strHeader = '<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="100%">
				<tr>	
					<td class="rptbx1" width="320">Appointment Survey Report</td>
					<td class="rptbx2" width="400">From : '.$Start_date.' To : '.$End_date.'</td>
					<td class="rptbx3" width="320">Created by '.strtoupper($createdBy).' on '.get_date_format(date("Y-m-d"))." ".date("h:i A").'&nbsp;</td>
				</tr>
			</table>';
	$strHTML .= '<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050" bgcolor="#FFF3E8">
				<tr>
					<td class="text_b_w" style="text-align:center;" width="80">Home Phone</td>
					<td class="text_b_w" style="text-align:center;" width="80">Cell Phone</td>
					<td class="text_b_w" style="text-align:center;" width="80">First Name</td>
					<td class="text_b_w" style="text-align:center;" width="80">Last Name</td>
					<td class="text_b_w" style="text-align:center;" width="70">DOS</td>
					<td class="text_b_w" style="text-align:center;" width="150">Rendering Physician</td>
					<td class="text_b_w" style="text-align:center;" width="80">Location</td>
					<td class="text_b_w" style="text-align:center;" width="80">Acct #</td>
					<td class="text_b_w" style="text-align:center;" width="70">DOB</td>
					<td class="text_b_w" style="text-align:center;" width="80">CPT Code</td>
					<td class="text_b_w" style="text-align:center;" width="80">CPT Desc</td>
					<td class="text_b_w" style="text-align:center;" width="80">Pt survey Opt In</td>
				</tr>';
		$strPDF .= '<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050" bgcolor="#FFF3E8">
			<tr>
				<td class="text_b_w" style="text-align:center;" width="75">Home Phone</td>
				<td class="text_b_w" style="text-align:center;" width="75">Cell Phone</td>
				<td class="text_b_w" style="text-align:center;" width="75">First Name</td>
				<td class="text_b_w" style="text-align:center;" width="75">Last Name</td>
				<td class="text_b_w" style="text-align:center;" width="75">DOS</td>
				<td class="text_b_w" style="text-align:center;" width="140">Rendering Physician</td>
				<td class="text_b_w" style="text-align:center;" width="80">Location</td>
				<td class="text_b_w" style="text-align:center;" width="75">Acct #</td>
				<td class="text_b_w" style="text-align:center;" width="75">DOB</td>
				<td class="text_b_w" style="text-align:center;" width="85">Pt survey Opt In</td>
			</tr>';	
				
				
				
	foreach($arrPatIds as $apptID => $appVal){
	 	$ptId = $appVal['spid'];
		$ptLName = $appVal['plname'];
		$ptFName = $appVal['pfname'];
		$apptDate = $appVal['apptDate'];
		$renderingphy = core_name_format($appVal['ulname'], $appVal['ufname'], $appVal['umname']);
		$facility = $appVal['name'];	
		$ptDOB = ($appVal['DOB'] != "0000-00-00") ? $appVal['date_of_birth'] : "N/A";
		$phone_home = core_phone_format($appVal['phone_home']);
		$phone_cell	= core_phone_format($appVal['phone_cell']);
		$hipaa_text = ($appVal['hipaa_text']) ? 'Yes' : 'No';
		
		
		$cptArr =  $cptmainArr[$ptId];
		$cptCode  = array_keys($cptArr);
		$cptCode = implode(',', $cptCode);
		$cptCodeDesc = array_values($cptArr);
		$cptCodeDesc = implode(',', $cptCodeDesc);
		$strHTML .= '<tr valign="top">
						<td class="text" bgcolor="#ffffff" align="left"> '.$phone_home.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$phone_cell.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$ptFName.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$ptLName.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$apptDate.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$renderingphy.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$facility.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$ptId.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$ptDOB.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$cptCode.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$cptCodeDesc.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$hipaa_text.'</td>
					</tr>';	
		$strPDF .= '<tr valign="top">
						<td class="text" bgcolor="#ffffff" align="left"> '.$phone_home.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$phone_cell.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$ptFName.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$ptLName.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$apptDate.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$renderingphy.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$facility.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$ptId.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$ptDOB.'</td>
						<td class="text" bgcolor="#ffffff" align="left"> '.$hipaa_text.'</td>
					</tr>';		
		
	}
	$strHTML .= '</table>';
	$strPDF .= '</table>';
	if($num_row > 0){
		$hasData = 1;
		$styleHTML = '<style>' . file_get_contents('css/reports_html.css') . '</style>';
		$csv_file_data = $styleHTML . $strHeader . $strHTML;
		$stylePDF = '<style>' . file_get_contents('css/reports_pdf.css') . '</style>';
		$strHTML = $stylePDF . $strHeader . $strPDF;
		$file_location = write_html($strHTML);
		echo $csv_file_data;		
	}else {
		 echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}	
 ?>