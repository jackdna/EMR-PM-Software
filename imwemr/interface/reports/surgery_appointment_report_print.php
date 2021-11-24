<?php
//Function files
if(empty($form_submitted) === false){	
	$blIncludePatientAddress = false;
	if($include_pat_Add == 1){
		$blIncludePatientAddress = true;
	}

$rep_fac = implode(",", $rep_fac);

$strProviderIds = implode(",", $providerID);

// SITE ARRAY
$arr_site = array("" => "", "Bilateral" => "(OU)", "Left" => "(OS)", "Right" => "(OD)");

	//--- CHANGE DATE FORMAT ---
	$dtDBEffectDate = getDateFormatDB($eff_date);
	
	//--- GET SCHEDULE APPOINTMENT ----
	$strQry = "SELECT u.id, sa.sa_facility_id ,sa.sa_patient_id, p.EMR, 
		CONCAT(p.street, p.street2,'<BR>',p.city, ', ', p.state, ' ', p.postal_code) as patientAddress, 
		sa.sa_madeby, pt.test_name ,CONCAT_WS(',',sp.acronym,sp1.acronym,sp2.acronym) as acronym, sa.sa_app_duration, 
		DATE_FORMAT(sa.sa_app_time,'".get_sql_date_format()."') as sa_app_time, sa.sa_comments, sa.procedure_site, p.DOB, 
		CONCAT(p.lname,', ',p.fname,' ',p.mname,' - ',sa.sa_patient_id) as pname, 
		DATE_FORMAT(p.DOB,'".get_sql_date_format()."') as date_of_birth, p.phone_home, p.phone_biz, p.phone_cell,
		p.phone_contact, p.preferr_contact,u.lname, 
		u.fname, u.mname, CONCAT(o.lname,', ',SUBSTRING(o.fname,1,1),' ',o.mname) as oname, 
		sa.sa_doctor_id, TIME_FORMAT(sa.sa_app_starttime,'%h:%i %p') selStartTime, 
		TIME_FORMAT(sa.sa_app_starttime,'%H:%i:%s') selStartTimeToMatch, f.name as facility_name, sa.arrival_time
		FROM schedule_appointments sa LEFT JOIN facility f ON f.id = sa.sa_facility_id 
		LEFT JOIN users u ON u.id = sa.sa_doctor_id 
		LEFT JOIN users o ON o.username = sa.sa_madeby 
		LEFT JOIN patient_data p ON p.id = sa.sa_patient_id  
		LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid
		LEFT JOIN slot_procedures sp1 ON sp1.id = sa.sec_procedureid 
		LEFT JOIN slot_procedures sp2 ON sp2.id = sa.tertiary_procedureid 	
		LEFT JOIN patient_tests pt ON (pt.id = sa.sa_test_id AND pt.facility = sa.sa_facility_id) 
		WHERE sa_patient_app_status_id NOT IN(201,18,203)
		and ('$dtDBEffectDate' BETWEEN sa_app_start_date AND sa_app_end_date)";
	if(empty($rep_fac) === false){
		$strQry .= " and sa_facility_id IN ($rep_fac)";
	}
	if(empty($strProviderIds) === false){
		$strQry .= " and sa_doctor_id IN ($strProviderIds)";
	}
					 
	$strQry .= "ORDER BY sa_doctor_id ASC, sa_facility_id ASC, sa_app_starttime ASC";

	$qry_chkses=imw_query($strQry);
	$resAppts = array();
	while($res_ses=imw_fetch_assoc($qry_chkses)){
		$resAppts[]=$res_ses;
	}
	
	$intTempProviderId = 0; //to carry forward last provider id in loop
	$intTempFacilityId = 0; //to carry forward last facility id in loop
	$intSerialNo = 0;
	
	$intTotAppts = count($resAppts);
	$html_css = '<style>'.file_get_contents("css/reports_pdf.css").'</style>';
	if($intTotAppts > 0){
		$strHTML = <<<DATA
			$html_css
			<page backtop="18mm" backbottom="10mm">			
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align: center;	width: 100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
DATA;
		
		$report_page_content = array();
		for($i = 0; $i < $intTotAppts; $i++){
			
			// SET SITE
			$procedure_site = '';
			$procedure_site = ($resAppts[$i]['procedure_site']) ? $arr_site[$resAppts[$i]['procedure_site']] : "";				 
			
			//--- PROVIDER DETAILS ----
			$intProviderId = $resAppts[$i]['sa_doctor_id'];
			$strProviderNmArr = array();
			$strProviderNmArr["LAST_NAME"] = $resAppts[$i]['lname'];
			$strProviderNmArr["FIRST_NAME"] = $resAppts[$i]['fname'];
			$strProviderNmArr["MIDDLE_NAME"] = $resAppts[$i]['mname'];
			$strProviderNm = changeNameFormat($strProviderNmArr);
			
			$strFacilityNm = $resAppts[$i]['facility_name'];
			
			//--- PATIENT DETAILS -----
			$intPatientId = $resAppts[$i]['sa_patient_id'];
			$strPatientNm = $resAppts[$i]['pname'];
			$dtPatientDOB = $resAppts[$i]['date_of_birth'];

			$intAge = show_age($resAppts[$i]['DOB']);
			$intPhoneNo = NULL;
			
			switch($resAppts[$i]['preferr_contact']){
				case '0':
					$intPhoneNo = core_phone_format(trim($resAppts[$i]['phone_home']));
				break;
				case '1':
					$intPhoneNo = core_phone_format(trim($resAppts[$i]['phone_biz']));
				break;
				case '2':
					$intPhoneNo = core_phone_format(trim($resAppts[$i]['phone_cell']));
				break;
			}
			
			if(empty($intPhoneNo)==true){
				if(trim($resAppts[$i]['phone_home']) != ''){
					$intPhoneNo = core_phone_format(trim($resAppts[$i]['phone_home']));
				}
				else if(trim($resAppts[$i]['phone_cell']) != ''){
					$intPhoneNo = core_phone_format(trim($resAppts[$i]['phone_cell']));
				}
				else if(trim($resAppts[$i]['phone_biz']) != ''){
					$intPhoneNo = core_phone_format(trim($resAppts[$i]['phone_biz']));
				}
				else if(trim($resAppts[$i]['phone_contact']) != ''){
					$intPhoneNo = core_phone_format(trim($resAppts[$i]['phone_contact']));
				}
			}
			
			//--- GET PATIENT ADDRESS ------
			if($blIncludePatientAddress == true){
				$strPatientAddress = $resAppts[$i]['patientAddress'];
			}

			//--- PATIENT EMR CHECK ----
			$strEMR = NULL;
			if($resAppts[$i]['EMR'] == 1){
				$strEMR = "e";
			}
			
			//---- GET PROCEDURE OR TEST NAME ---
			$strProcedureNm = NULL;
			if(trim($resAppts[$i]['acronym'])){
				$strProcedureNm = trim($resAppts[$i]['acronym']);
			}
			else if(trim($resAppts[$i]['test_name'])){
				$strProcedureNm = trim($resAppts[$i]['test_name']);
			}
			
			//--- OPERATOR NAME ----
			$strOperatorNm = $resAppts[$i]['oname'];
			
			$strarrival_time = $resAppts[$i]['arrival_time'];
			
			//--- OTHER FIELDS DATA ----
			$strComments = wordwrap(ucfirst($resAppts[$i]['sa_comments']),35,"<br>\n",true);
			$strApptStartTime = $resAppts[$i]['selStartTime'];
			$strApptStartTimeToMatch = $resAppts[$i]['selStartTimeToMatch'];
			$strApptDuration = number_format($resAppts[$i]['sa_app_duration']/60);	
			$stApptMadeDate = $resAppts[$i]['sa_app_time'];
			$insurance_detail = $CLSReports->getpatient_Insurance($intPatientId);
			$insurance_type = $insurance_detail[0];	
			$policy_no = $insurance_detail[1];
			
			//NEW PAGE FOR EVERY NEW PROVIDER OR FACILITY ----
			if($intTempProviderId != $intProviderId || $intTempFacilityId != $intFacilityId){
				$intSerialNo = 0;
				if($i > 0){
					$strHTML .= <<<DATA
						</table></page><page pageset="old">
DATA;
				}

				$curDate = get_date_format(date('Y-m-d'))." ".date("g:i A",time());
				$createdBy = $_SESSION['authProviderName'];
				$strHTML .= <<<DATA
					<page_header>
					<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:1050px">
						<tr class="rpt_headers">
							<td align="left" class="rptbx1" width="350">Surgery Appointment Report</td>
							<td align="left" class="rptbx2" width="350">Appointment Date : $eff_date</td>
							<td align="left" class="rptbx3" width="350">Created by $op_name on $curDate</td>
						</tr>
					</table>
					<table cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8" style="width:1050px">
						<tr>
							<td class="text_b_w" align="left" colspan="6">Provider Name : $strProviderNm</td>
							<td class="text_b_w" align="left" colspan="6">Facility : $strFacilityNm</td>
						</tr>
						<tr><td colspan="12" style="height:1px;"></td></tr>
						<tr>
							<td class="text_b_w" align="center" style="width:15px">Appt.</td>
							<td class="text_b_w" align="center" style="width:50px">Time</td>				
							<td class="text_b_w" align="center" style="width:50px">Arrival Time</td>				
							<td class="text_b_w" align="center" style="width:60px">Duration</td>
							<td class="text_b_w" align="center" style="width:160px">Patient Name-ID</td>
							<td class="text_b_w" align="center" style="width:100px">DOB (Age)</td>
							<td class="text_b_w" align="center" style="width:80px">Phone #</td>
							<td class="text_b_w" align="center" style="width:80px">Procedure</td>
							<td class="text_b_w" align="center" style="width:40px">Site</td>
							<td class="text_b_w" align="center" style="width:140px">Comments</td>
							<td class="text_b_w" align="center" style="width:100px">Insurance</td>
							<td class="text_b_w" align="center" style="width:150px">Policy No.</td>
						</tr>
					</table>
					</page_header>
					<table cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8" style="width:1050px">
DATA;
				
				$report_page_content[$i]['strProviderNm'] = $strProviderNm;
				$report_page_content[$i]['strFacilityNm'] = $strFacilityNm;
			}
			
			$intSerialNo++;
			
			if(trim($strEMR) != ""){
				$strPatientNm .= " ($strEMR)";
			}
			
			if($blIncludePatientAddress == true){
				if(str_replace(",","",trim(strip_tags($strPatientAddress))) != "")
					$strPatientNm .= '<BR>'.$strPatientAddress;
			}
			
			$strHTML .= <<<DATA
					<tr valign="top">
						<td class="text_10" align="center" style="width:15px">$intSerialNo</td>
						<td class="text_10" align="left" style="width:50px">$strApptStartTime</td>				
						<td class="text_10" align="left" style="width:50px">$strarrival_time</td>				
						<td class="text_10" align="left" style="width:60px">$strApptDuration Min.</td>
						<td class="text_10" align="left" style="width:180px">$strPatientNm</td>
						<td class="text_10" align="left" style="width:100px">$dtPatientDOB ($intAge)</td>
						<td class="text_10" align="left" style="width:80px">$intPhoneNo</td>
						<td class="text_10" align="left" style="width:80px">$strProcedureNm</td>
						<td class="text_10" align="left" style="width:40px">$procedure_site</td>
						<td class="text_10" align="left" style="width:150px;font-size:10px">$strComments</td>
						<td class="text_10" align="left" style="width:110px">$insurance_type</td>
						<td class="text_10" align="left" style="width:160px">$policy_no</td>
					</tr>
DATA;

			//--- CSV FILE CONTENT ----
			$report_page_content[$i]['intSerialNo'] = $intSerialNo;
			$report_page_content[$i]['strApptStartTime'] = $strApptStartTime;
			$report_page_content[$i]['strarrival_time'] = $strarrival_time;
			$report_page_content[$i]['strApptDuration'] = $strApptDuration .' Min.';
			$report_page_content[$i]['strPatientNm'] = $strPatientNm;
			$report_page_content[$i]['dtPatientDOB'] = $dtPatientDOB ." ($intAge)";
			$report_page_content[$i]['intPhoneNo'] = $intPhoneNo;
			$report_page_content[$i]['strProcedureNm'] = $strProcedureNm;
			$report_page_content[$i]['strComments'] = $strComments;
			$report_page_content[$i]['insurance_type'] = $insurance_type;
			$report_page_content[$i]['policy_no'] = $policy_no;
			$report_page_content[$i]['procedure_site'] = $procedure_site;
			
			$intTempProviderId = $intProviderId;
			$intTempFacilityId = $intFacilityId;
		}
		$strHTML .= '</table></page>';
	}
}
$curDate = date(''.$phpDateFormat.' H:i A');
$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
$op_name = $op_name_arr[1][0];
$op_name .= $op_name_arr[0][0];
$page_header_val = '
<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%" bgcolor="#FFF3E8">
	<tr class="rpt_headers">
		<td align="left" class="rptbx1" width="350">Surgery Appointment Report</td>
		<td align="left" class="rptbx2" width="350">Appointment Date :  '.$eff_date.'</td>
		<td align="left" class="rptbx3" width="350">Created by '.$op_name.' on '.$curDate.' </td>
	</tr>
</table>';
//--- SET SURGERY APPOINTMENT REPORT RESULT DATA ---
if(count($report_page_content) > 0){
	$printFile = 1;
	$file_location = write_html($strHTML);
	echo $page_header_val;
	echo '<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%" bgcolor="#FFF3E8">						
	<tr>
		<td class="text_b_w" align="center" style="width:15px">Appt.</td>
		<td class="text_b_w" align="center" style="width:50px">Time</td>				
		<td class="text_b_w" align="center" style="width:50px">Arrival Time</td>				
		<td class="text_b_w" align="center" style="width:60px">Duration</td>
		<td class="text_b_w" align="center" style="width:160px">Patient Name-ID</td>
		<td class="text_b_w" align="center" style="width:100px">DOB (Age)</td>
		<td class="text_b_w" align="center" style="width:80px">Phone #</td>
		<td class="text_b_w" align="center" style="width:80px">Procedure</td>
		<td class="text_b_w" align="center" style="width:40px">Site</td>
		<td class="text_b_w" align="center" style="width:140px">Comments</td>
		<td class="text_b_w" align="center" style="width:100px">Insurance</td>
		<td class="text_b_w" align="center" style="width:150px">Policy No.</td>
	</tr>';
	foreach($report_page_content as $key => $val){
		if($report_page_content[$key]['strProviderNm']){
			echo '<tr>
					<td class="text_b_w" colspan="6" style="text-align:left;">Provider Name : '.$report_page_content[$key]['strProviderNm'].'</td>
					<td class="text_b_w" colspan="6" style="text-align:left;">Facility Name : '.$report_page_content[$key]['strFacilityNm'].'</td>
				</tr>';		
		}
			echo '<tr>
				<td class="text_10 valignTop" bgcolor="#FFFFFF">'.$report_page_content[$key]['intSerialNo'].'</td>
				<td class="text_10 valignTop" bgcolor="#FFFFFF">'.$report_page_content[$key]['strApptStartTime'].'</td>
				<td class="text_10 valignTop" bgcolor="#FFFFFF">'.$report_page_content[$key]['strarrival_time'].'</td>
				<td class="text_10 valignTop" bgcolor="#FFFFFF">'.$report_page_content[$key]['strApptDuration'].'</td>
				<td class="text_10 valignTop" bgcolor="#FFFFFF">'.$report_page_content[$key]['strPatientNm'].'</td>
				<td class="text_10 valignTop" bgcolor="#FFFFFF">'.$report_page_content[$key]['dtPatientDOB'].'</td>
				<td class="text_10 valignTop" bgcolor="#FFFFFF">'.$report_page_content[$key]['intPhoneNo'].'</td>
				<td class="text_10 valignTop" bgcolor="#FFFFFF">'.$report_page_content[$key]['strProcedureNm'].'</td>
				<td class="text_10 valignTop" bgcolor="#FFFFFF">'.$report_page_content[$key]['procedure_site'].'</td>
				<td class="text_10 valignTop" bgcolor="#FFFFFF">'.$report_page_content[$key]['strComments'].'</td>
				<td class="text_10 valignTop" bgcolor="#FFFFFF">'.$report_page_content[$key]['insurance_type'].'</td>
				<td class="text_10 valignTop" bgcolor="#FFFFFF">'.$report_page_content[$key]['policy_no'].'</td>
			</tr>';
		
	}
	echo '</table>';
}else{
	echo '<div class="text-center alert alert-info">No record exists.</div>';
	$printFile = 0;
}
?>