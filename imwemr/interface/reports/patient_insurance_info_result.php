<?php
$page_data = '';	$printFile= false;
if ($_REQUEST['form_submitted']) {

	//ALL USERS
	$providerNameArr=array();
	$rs=imw_query("Select id,fname, mname, lname FROM users");
	while($res=imw_fetch_assoc($rs)){
		$id = $res['id'];
		$nameArr = array();
		$nameArr["LAST_NAME"] = $res['lname'];
		$nameArr["FIRST_NAME"] = $res['fname'];
		$nameArr["MIDDLE_NAME"] = $res['mname'];
		$providerNameArr[$id] = changeNameFormat($nameArr);
	}unset($rs);
	
	$printFile = true;
	
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
	
	
	//-- OPERATOR INITIAL -------
	$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$opInitial = $authProviderNameArr[1][0];
	$opInitial .= $authProviderNameArr[0][0];
	$opInitial = strtoupper($opInitial);
	
	$st_date = getDateFormatDB($Start_date);
	$en_date = getDateFormatDB($End_date);
	
	if($month>0){
		$last_day = date("t", mktime(0,0,0,$month,1,$year));
		$st_date = $year.'-'.$month.'-'.'01';
		$en_date = $year.'-'.$month.'-'.$last_day;
	}
	$primaryProviderId = join(",",$providerID);
	$facility_name_str = join(",",$facility_name);	
	$firstFac = $facility_name[0];

	$sch_query = "SELECT pd.id as pid, pd.lname, pd.fname, pd.mname, date_format(pd.DOB, '".$date_format_SQL."') as DOB,
	sa.case_type_id, sp.proc as procedurename, sa.sa_doctor_id, 
	DATE_FORMAT(sa.sa_app_start_date, '".$date_format_SQL."') as 'sa_app_start_date', ins_comp.name as 'ins_comp_name', 
	ins.policy_number, ins.type as 'ins_type', users.fname 'phy_fname', users.lname as 'phy_lname', users.user_npi 
	FROM schedule_appointments sa 
	JOIN patient_data pd ON pd.id = sa.sa_patient_id 
	JOIN users ON users.id=sa.sa_doctor_id 
	LEFT JOIN insurance_data ins ON ins.ins_caseid = sa.case_type_id 
	LEFT JOIN insurance_companies ins_comp ON ins_comp.id = ins.provider 
	LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid 
	WHERE (sa.sa_app_start_date between '$st_date' and '$en_date') 
	AND sa.sa_patient_app_status_id NOT IN(18,19,20) and pd.patientStatus='Active'";
	if(empty($primaryProviderId) === false){
		$sch_query .= " and sa.sa_doctor_id in($primaryProviderId)";
	}
	if(empty($facility_name_str) === false){
		$sch_query .= " and sa.sa_facility_id in($facility_name_str)";
	}
	$sch_query .= " order by pd.lname, pd.fname, sa.sa_app_start_date, sa.sa_app_starttime, ins.type";
	$sch_query_res = array();
	$sch_qry = imw_query($sch_query);
	while($res=imw_fetch_assoc($sch_qry)){
		$sch_query_res[] = $res;
		$arrCaseIds[$res['case_type_id']]=$res['case_type_id'];
	}

	$page_data = '';
	if(count($sch_query_res) > 0){

        foreach($sch_query_res as $sch_report_row)
        {
            $pt_name = core_name_format($sch_report_row['lname'], $sch_report_row['fname'], $sch_report_row['mname']);

			$pcp_fac_arr = 	explode(',', $sch_report_row["pcp_fac"]);
			$pcp_fac_str_arr = array();			
			foreach ($pcp_fac_arr as $fac_id) {
				$fac_id = trim($fac_id);
				$pcp_fac_str_arr[] = $facilities_arr[$fac_id]["name"];				
			}

			$pcp_fac_str = implode(', ', $pcp_fac_str_arr);
			
			$patient_id = $sch_report_row["pid"];
			$caseId=$sch_report_row["case_type_id"];
			
			$ins_data_id = $arrCaseData[$caseId]["ins_data_id"];
			$auth_number = "";
			$auth_check = $arrCaseData[$caseId]['auth_required'];
			
			$appt_physician=$providerNameArr[$sch_report_row["sa_doctor_id"]];
			
			$data_part .= '<tr >
				<td class="text_10 white" style="width:105px">'.$sch_report_row['lname'].'</td>
				<td class="text_10 white" style="width:105px">'.$sch_report_row['fname'].'</td>
				<td class="text_10 white" style="width:70px">'.$sch_report_row["DOB"].'</td>
				<td class="text_10 white" style="width:100px">'.$sch_report_row['user_npi'].'</td>
				<td class="text_10 white" style="width:100px">'.$sch_report_row["phy_lname"].'</td>
				<td class="text_10 white" style="width:100px">'.$sch_report_row["phy_fname"].'</td>
				<td class="text_10 white" style="width:70px">'.$sch_report_row["sa_app_start_date"].'</td>
				<td class="text_10 white" style="width:100px">'.$sch_report_row["procedurename"].'</td>
				<td class="text_10 white" style="width:100px">'.$sch_report_row['ins_comp_name'].'</td>
				<td class="text_10 white" style="width:70px">'.ucfirst($sch_report_row["ins_type"]).'</td>
				<td class="text_10 white" style="width:100px">'.$sch_report_row["policy_number"].'</td>
			</tr>';	
        }


		$selPhy = $CLSReports->report_display_selected($primaryProviderId,'physician',1, $allPhyCount);
		$selFac = $CLSReports->report_display_selected($sc_name,'facility_tbl',1, $allFacCount);

		$strHTML='			
		<page backtop="5mm" backbottom="10mm">			
		<page_footer>
			<table style="width:100%;">
				<tr>
					<td style="text-align:center; width: 100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>
		<page_header>
		<table class="rpt_table rpt rpt_table-bordered rpt_padding">
			<tr>
				<td style="text-align:left;" class="rptbx1" width="345">Patient Insurance Info Report</td>
				<td style="text-align:left;" class="rptbx2" width="350">Date From : '.$Start_date.' To : '.$End_date.'</td>
				<td style="text-align:left;" class="rptbx3" width="350">Created by '.$opInitial.' on '.$curDate.'</td>
			</tr>
			<tr>
				<td style="text-align:left;" class="rptbx1">Physician: '.$selPhy.'</td>
				<td style="text-align:left;" class="rptbx2">Facility: '.$selFac.'</td>
				<td style="text-align:left;" class="rptbx3"></td>
			</tr>
		</table>
		<table style="width:100%" class="rpt_table rpt_table-bordered">
        <tr>
			<td class="text_b_w" style="width:105px">Patient Last Name</td>
			<td class="text_b_w" style="width:105px">Patient First Name</td>				
			<td class="text_b_w" style="width:70px">Patient DOB</td>
			<td class="text_b_w" style="width:100px">Provider NPI</td>
			<td class="text_b_w" style="width:100px">Provider Last Name</td>
			<td class="text_b_w" style="width:100px">Provider First Name</td>
			<td class="text_b_w" style="width:70px">Date of Service</td>
			<td class="text_b_w" style="width:100px">Appointment Type</td>
			<td class="text_b_w" style="width:100px">Insurance Name</td>
			<td class="text_b_w" style="width:70px">Insurance Type</td>
			<td class="text_b_w" style="width:100px">Subscriber Insurance ID</td>
        </tr>
		</table>	
		</page_header>
		<table class="rpt_table rpt_table-bordered" style="width:100%">
		'.$data_part.'
		</table></page>';
		
		$page_data=
		'<table class="rpt_table rpt rpt_table-bordered rpt_padding">
			<tr>
				<td style="text-align:left;" class="rptbx1" width="345">Patient Insurance Info Report</td>
				<td style="text-align:left;" class="rptbx2" width="350">Date From : '.$Start_date.' To : '.$End_date.'</td>
				<td style="text-align:left;" class="rptbx3" width="350">Created by '.$opInitial.' on '.$curDate.'</td>
			</tr>
			<tr>
				<td style="text-align:left;" class="rptbx1">Physician: '.$selPhy.'</td>
				<td style="text-align:left;" class="rptbx2">Facility: '.$selFac.'</td>
				<td style="text-align:left;" class="rptbx3"></td>
			</tr>
		</table>
		<table style="width:100%" class="rpt_table rpt_table-bordered">
        <tr>
			<td class="text_b_w" style="width:105px">Patient Last Name</td>
			<td class="text_b_w" style="width:105px">Patient First Name</td>				
			<td class="text_b_w" style="width:70px">Patient DOB</td>
			<td class="text_b_w" style="width:100px">Provider NPI</td>
			<td class="text_b_w" style="width:100px">Provider Last Name</td>
			<td class="text_b_w" style="width:100px">Provider First Name</td>
			<td class="text_b_w" style="width:70px">Date of Service</td>
			<td class="text_b_w" style="width:100px">Appointment Type</td>
			<td class="text_b_w" style="width:100px">Insurance Name</td>
			<td class="text_b_w" style="width:70px">Insurance Type</td>
			<td class="text_b_w" style="width:100px">Subscriber Insurance ID</td>
        </tr>
			'.$data_part.'
		</table>';
	}

//--- CREATE PDF FILE FOR PRINTING -----
$hasData=0;
if($printFile == true and $page_data != ''){
	$hasData=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$page_data;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$page_data;

	$file_location = write_html($strHTML);
}else{
	$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
}

echo $csv_file_data;
}

?>