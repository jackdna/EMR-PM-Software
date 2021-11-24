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

	$fac_query = "select id,name from facility";
	$fac_query_res = imw_query($fac_query);
	$arrAllFacilities = array();
	while ($fac_res = imw_fetch_array($fac_query_res)) {
		$arrAllFacilities[$fac_res['id']]=$fac_res['name'];
	}
	
	
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

	//getting report generator name
	$report_generator_name = NULL;
	if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
		$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
		$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
		$report_generator_name = strtoupper($report_generator_name);
	}
	$sch_query = "SELECT pd.id as pid, pd.lname, pd.fname, pd.mname, date_format(pd.DOB, '".get_sql_date_format()."') as DOB, pd.phone_home, pd.phone_biz, pd.phone_cell, pd.email, pd.street, pd.street2, pd.postal_code, pd.city, pd.state, pd.country_code, pd.preferr_contact,
					pd.hipaa_mail, pd.hipaa_email, pd.hipaa_voice, hipaa_text, sa.case_type_id,
					CONCAT(pcp.LastName, ', ', pcp.FirstName, ' ', pcp.MiddleName) as primary_care_physician, pcp.default_facility as pcp_fac, pcp.Address1, pcp.Address2, pcp.City, pcp.State, pcp.ZipCode,  
					sp.proc as procedurename, sa.sa_doctor_id,
					CONCAT(DATE_FORMAT(sa.sa_app_start_date, '".get_sql_date_format()."'), ' ',DATE_FORMAT(sa.sa_app_starttime, '%h:%i %p')) as  appt_date_time,
					sa.sa_facility_id 			
					FROM patient_data pd INNER JOIN schedule_appointments sa ON pd.id = sa.sa_patient_id
						LEFT JOIN insurance_data ins ON pd.id = ins.pid 
						LEFT JOIN insurance_companies ins_comp ON ins_comp.id = ins.provider
						LEFT JOIN refferphysician pcp ON pd.primary_care_phy_id = pcp.physician_Reffer_id
						LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid
						
					WHERE (sa.sa_app_start_date between '$st_date' and '$en_date') and sa.sa_patient_app_status_id NOT IN(18,19,20,201,203) and pd.patientStatus = 'Active'";

//die(sch_query);
	if(empty($active_insurance)==false){
		$sch_query .= " and ins.actInsComp='1'";
	}
	if(empty($primaryProviderId) === false){
		$sch_query .= " and sa.sa_doctor_id in($primaryProviderId)";
	}
	if(empty($facility_name_str) === false){
		$sch_query .= " and sa.sa_facility_id in($facility_name_str)";
	}
	
	$sch_query .= " group by sa.id order by sa.sa_app_start_date, sa.sa_app_starttime";
	$sch_query_res = array();
	$sch_qry = imw_query($sch_query);
	while($res=imw_fetch_assoc($sch_qry)){
		$sch_query_res[] = $res;
	}
	
	//GET INSURANCE INFO
	$arrCaseIds=array();
	foreach($sch_query_res as $sch_report_row){
		$arrCaseIds[$sch_report_row['case_type_id']]=$sch_report_row['case_type_id'];
	}
	if(sizeof($arrCaseIds)>0){
		$strCaseIds=implode(',', $arrCaseIds);
		$qry="Select ins.ins_caseid, ins.id as ins_data_id, ins_comp.name as comp_name, ins.policy_number, ins.auth_required FROM 
		insurance_data ins 
		LEFT JOIN insurance_companies ins_comp ON ins_comp.id = ins.provider 
		WHERE ins.ins_caseid IN(".$strCaseIds.") AND type='primary'";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$caseId=$res['ins_caseid'];
			$arrCaseData[$caseId]['ins_data_id']=$res['ins_data_id'];
			$arrCaseData[$caseId]['comp_name']=$res['comp_name'];
			$arrCaseData[$caseId]['policy_number']=$res['policy_number'];
			$arrCaseData[$caseId]['auth_required']=$res['auth_required'];
		}unset($rs);
	}unset($arrCaseIds);

	//MAKING OUTPUT DATA
	$file_name="Appointment_Report.csv";
	$csv_file_name= write_html("", $file_name);
	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');
	$arr=array();
	$arr[]="Patient Name";
	$arr[]="Phone Home";				
	$arr[]="Phone Biz";
	$arr[]="Phone Cell";
	$arr[]="Email Id";
	$arr[]="Preferred Method";
	$arr[]="Reminder Choices";
	$arr[]="Address";
	$arr[]="DOB";
	$arr[]="Primary Care Physician";
	$arr[]="Appt Physician";
	$arr[]="Practice";
	$arr[]="PCP Address";
	$arr[]="Appt Date & Time";
	$arr[]="Appt Type";
	$arr[]="Facility";
	$arr[]="Ins. Name";
	$arr[]="Policy #";
	$arr[]="Auth #";
	fputcsv($fp,$arr, ",","\"");
	$fp = fopen ($csv_file_name, 'a+');

	$page_data = '';
	if(count($sch_query_res) > 0){
	$strHTML = '			
		<page backtop="5mm" backbottom="10mm">			
		<page_footer>
			<table style="width:100%;">
				<tr>
					<td style="text-align:center; width: 100%">Page [[page_cu]]/[[page_nb]]</td>
				</tr>
			</table>
		</page_footer>';	
		$strHTML .= '<page_header>
						<table class="rpt_table rpt rpt_table-bordered rpt_padding">
							<tr>
								<td style="text-align:left;" class="rptbx1" width="350">Appointment Report</td>
								<td style="text-align:left;" class="rptbx2" width="350">Date From : '.$Start_date.' To : '.$End_date.'</td>
								<td style="text-align:left;" class="rptbx3" width="350">Created by '.$opInitial.' on '.$curDate.'</td>
							</tr>
						</table>	
					</page_header>';


	$strHTML .= '<table class="rpt_table rpt rpt_table-bordered" >
				<tr>
					<td class="text_b_w" width="130">Patient Name</td>
					<td class="text_b_w" width="90">Phone Home</td>				
					<td class="text_b_w" width="90">Phone Biz</td>
					<td class="text_b_w" width="80">Phone Cell</td>
					<td class="text_b_w" width="114">PCP</td>
					<td class="text_b_w" width="114">Practice</td>
					<td class="text_b_w" width="115">Appt Date &amp; Time</td>
					<td class="text_b_w" width="114">Ins. Name</td>
					<td class="text_b_w" width="114">Policy #</td>
				</tr>';
	
    $page_data = '<table class="rpt_table rpt rpt_table-bordered rpt_padding">
			<tr>
				<td style="text-align:left;" class="rptbx1" width="350">Appointment Report</td>
				<td style="text-align:left;" class="rptbx2" width="350">Date From : '.$Start_date.' To : '.$End_date.'</td>
				<td style="text-align:left;" class="rptbx3" width="350">Created by '.$opInitial.' on '.$curDate.'</td>
			</tr>
		</table>
		<table class="rpt_table rpt rpt_table-bordered">
        <tr>
            <td class="text_b_w alignCenter" style="width:120px;">Patient Name</td>
            <td class="text_b_w alignCenter" style="width:50px;">Phone Home</td>				
            <td class="text_b_w alignCenter" style="width:50px;">Phone Biz</td>
            <td class="text_b_w alignCenter" style="width:50px;">Phone Cell</td>
            <td class="text_b_w alignCenter" style="width:70px;">Email Id</td>
            <td class="text_b_w alignCenter" style="width:70px;">Preferred Method</td>
			<td class="text_b_w alignCenter" style="width:70px;">Reminder Choices</td>
            <td class="text_b_w alignCenter" style="width:100px;">Address</td>
            <td class="text_b_w alignCenter" style="width:60px;">DOB</td>
            <td class="text_b_w alignCenter" style="width:75px;">Primary Care Physician</td>
			<td class="text_b_w alignCenter" style="width:75px;">Appt Physician</td>
			<td class="text_b_w alignCenter" style="width:70px;">Practice</td>
			<td class="text_b_w alignCenter" style="width:80px;">PCP Address</td>            
            <td class="text_b_w alignCenter" style="width:70px;">Appt Date &amp; Time</td>
            <td class="text_b_w alignCenter" style="width:70px;">Appt Type</td>
			<td class="text_b_w alignCenter" style="width:70px;">Facility</td>
            <td class="text_b_w alignCenter" style="width:50px;">Ins. Name</td>
            <td class="text_b_w alignCenter" style="width:40px;">Policy #</td>
            <td class="text_b_w alignCenter" style="width:50px;">Auth #</td>
        </tr>';
     
        foreach($sch_query_res as $sch_report_row)
        {
            $pt_name = core_name_format($sch_report_row['lname'], $sch_report_row['fname'], $sch_report_row['mname']);
			$address = $sch_report_row["street"];
			if(trim($sch_report_row["street2"]) != "")
			{
				$address .= ', '.trim($sch_report_row["street2"]);	
			}
			if(trim($sch_report_row["postal_code"]) != "")
			{
				$address .= ' ('.trim($sch_report_row["postal_code"]).')';					
			}
			if(trim($sch_report_row["city"]) != "")
			{
				$address .= ', '.trim($sch_report_row["city"]);	
			}			
			if(trim($sch_report_row["state"]) != "")
			{
				$address .= ', '.trim($sch_report_row["state"]);	
			}
			if(trim($sch_report_row["country_code"]) != "")
			{
				$address .= ', '.trim($sch_report_row["country_code"]);	
			}

			$pcp_fac_arr = 	explode(',', $sch_report_row["pcp_fac"]);
			$pcp_fac_str_arr = array();			
			foreach ($pcp_fac_arr as $fac_id) {
				$fac_id = trim($fac_id);
				$pcp_fac_str_arr[] = $facilities_arr[$fac_id]["name"];				
			}

			$pcp_fac_str = implode(', ', $pcp_fac_str_arr);
			
			$pcp_address = $sch_report_row["Address1"];
			if($sch_report_row["Address2"] != "")
			{
				$pcp_address .= ", ".$sch_report_row["Address2"];
			}

			if($sch_report_row["City"] != "")
			{
				$pcp_address .= ", ".$sch_report_row["City"];
			}

			if($sch_report_row["State"] != "")
			{
				$pcp_address .= ", ".$sch_report_row["State"];
			}						

			if($sch_report_row["ZipCode"] != "")
			{
				$pcp_address .= ", ".$sch_report_row["ZipCode"];
			}

			$patient_id = $sch_report_row["pid"];
			
			$caseId=$sch_report_row["case_type_id"];
			
			$ins_data_id = $arrCaseData[$caseId]["ins_data_id"];
			$auth_number = "";
			$auth_check = $arrCaseData[$caseId]['auth_required'];
			
			$appt_physician=$providerNameArr[$sch_report_row["sa_doctor_id"]];
			
			$preferr_contact = '';
			$preferr_method = $sch_report_row["preferr_contact"];
			if($preferr_method == 0){
				if(trim($sch_report_row["phone_home"]) != ""){
					$preferr_contact = 'Home';
				}
			}else if($preferr_method == 1){
				if(trim($sch_report_row["phone_biz"]) != ""){ 
					$preferr_contact = 'Work';
				}
			}else if($preferr_method == 2){
				if(trim($sch_report_row["phone_cell"]) != ""){
					$preferr_contact = 'Cell';
				}
			}
			
			//REMINDER CHOICES
			$reminder_choices='';
			$tempArr=array();
			if($sch_report_row["hipaa_mail"]=='1')$tempArr[]='Postal Mail';
			if($sch_report_row["hipaa_email"]=='1')$tempArr[]='Email';
			if($sch_report_row["hipaa_voice"]=='1')$tempArr[]='Voice';
			if($sch_report_row["hipaa_text"]=='1')$tempArr[]='Text';
			if(sizeof($tempArr)>0)$reminder_choices=implode(', ', $tempArr);
		
			if(strtolower(trim($auth_check)) == "yes"){
				$auth_no_qry = imw_query("SELECT auth_name FROM patient_auth WHERE patient_id = '".$patient_id."' and ins_data_id = '".$ins_data_id."' ORDER BY a_id DESC LIMIT 1");	
				$auth_no_data = imw_fetch_assoc($auth_no_qry);
				$auth_number = $auth_no_data["auth_name"];				
			}
			
			$page_data .= '<tr >
				<td class="white">'.$pt_name.' - '.$patient_id.'</td>
				<td class="white">'.$sch_report_row["phone_home"].'</td>
				<td class="white">'.$sch_report_row["phone_biz"].'</td>
				<td class="white">'.$sch_report_row["phone_cell"].'</td>
				<td class="white">'.$sch_report_row["email"].'</td>
				<td class="white">'.$preferr_contact.'</td>
				<td class="white">'.$reminder_choices.'</td>
				<td class="white">'.$address.'</td>
				<td class="white">'.$sch_report_row["DOB"].'</td>
				<td class="white">'.$sch_report_row["primary_care_physician"].'</td>
				<td class="white">'.$appt_physician.'</td>
				<td class="white">'.$pcp_fac_str.'</td>
				<td class="white">'.$pcp_address.'</td>
				<td class="white">'.$sch_report_row["appt_date_time"].'</td>
				<td class="white">'.$sch_report_row["procedurename"].'</td>
				<td class="white">'.$arrAllFacilities[$sch_report_row["sa_facility_id"]].'</td>
				<td class="white">'.$arrCaseData[$caseId]['comp_name'].'</td>
				<td class="white">'.$arrCaseData[$caseId]['policy_number'].'</td>
				<td class="white">'.$auth_number.'</td>
			</tr>';	

			$pt_data = $pt_name.' - '.$patient_id;
			$strHTML .= '<tr>
				<td>'.wordwrap($pt_data, 15, "<br>\n", true).'</td>
				<td>'.$sch_report_row["phone_home"].'</td>
				<td>'.$sch_report_row["phone_biz"].'</td>
				<td>'.$sch_report_row["phone_cell"].'</td>
				<td>'.wordwrap($sch_report_row["primary_care_physician"], 15, "<br>\n", true).'</td>
				<td>'.wordwrap($pcp_fac_str, 15, "<br>\n", true).'</td>
				<td>'.wordwrap($sch_report_row["appt_date_time"], 15, "<br>\n", true).'</td>
				<td>'.wordwrap($arrCaseData[$caseId]['comp_name'], 15, "<br>\n", true).'</td>
				<td>'.wordwrap($arrCaseData[$caseId]['policy_number'], 15, "<br>\n", true).'</td>
			</tr>';	
			$arr=array();
			$arr[]=$pt_name.' - '.$patient_id;
			$arr[]=$sch_report_row["phone_home"];
			$arr[]=$sch_report_row["phone_biz"];
			$arr[]=$sch_report_row["phone_cell"];
			$arr[]=$sch_report_row["email"];
			$arr[]=$preferr_contact;
			$arr[]=$reminder_choices;
			$arr[]=$address;
			$arr[]=$sch_report_row["DOB"];
			$arr[]=$sch_report_row["primary_care_physician"];
			$arr[]=$appt_physician;
			$arr[]=$pcp_fac_str;
			$arr[]=$pcp_address;
			$arr[]=$sch_report_row["appt_date_time"];
			$arr[]=$sch_report_row["procedurename"];
			$arr[]=$arrAllFacilities[$sch_report_row["sa_facility_id"]];
			$arr[]=$arrCaseData[$caseId]['comp_name'];
			$arr[]=$arrCaseData[$caseId]['policy_number'];
			$arr[]=$auth_number;
			fputcsv($fp,$arr, ",","\"");		
        }
		$appt_count = count($sch_query_res);
		$page_data .= '<tr>
				<td colspan="19" class="white" ><b>Total Appointments</b> : '.$appt_count.'</td>
			</tr>
		</table>';
		$strHTML .= '<tr>
				<td colspan="17" class="white"><b>Total Appointments</b> : '.$appt_count.'</td>
			</tr></table></page>';
	}
	fclose($fp);
//--- CREATE PDF FILE FOR PRINTING -----
$hasData=0;
if($printFile == true and $page_data != ''){
	$hasData=1;
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csv_file_data= $styleHTML.$page_data;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$strHTML;

	$file_location = write_html($strHTML);
}else{
	$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
}

echo $csv_file_data;
}

?>