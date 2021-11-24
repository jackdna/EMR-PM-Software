<?php
if($_POST['form_submitted']){
	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	$arrDateRange= $CLSCommonFunction->changeDateSelection();

	if($dayReport=='Daily'){
		$start_date = $end_date= date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$start_date = $arrDateRange['WEEK_DATE'];
		$end_date= date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$start_date = $arrDateRange['MONTH_DATE'];
		$end_date= date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$start_date = $arrDateRange['QUARTER_DATE_START'];
		$end_date = $arrDateRange['QUARTER_DATE_END'];
	}	
	$st_date = getDateFormatDB($start_date);
	$en_date = getDateFormatDB($end_date);

	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = $op_name_arr[1][0];
	$op_name .= $op_name_arr[0][0];
	$op_name = strtoupper($op_name);
	
	$rqArrPhyId = $_REQUEST['phyId'];
	$rqPhyId = join(',',$rqArrPhyId);
	
	$query = "SELECT DISTINCT(appt.id) as saAppId, appt.sa_doctor_id as sdocid, appt.sa_patient_id as spid, appt.sa_patient_app_status_id as apptStatus, DATE_FORMAT(appt.sa_app_start_date, '%m/%d/%Y') as apptDate, TIME_FORMAT(appt.sa_app_starttime, '%H:%i') as starttime, TIME_FORMAT(appt.sa_app_endtime, '%h:%i %p') as endtime, u.lname as ulname, u.fname as ufname, u.mname as umname, u.user_npi, u.pro_suffix, pd.lname as plname, pd.fname as pfname, pd.mname as pmname, pd.street,pd.street2, pd.city, pd.state, pd.postal_code, pd.email, pd.primary_care_id, pd.DOB, DATE_FORMAT(pd.DOB,'%m/%d/%Y') as date_of_birth, pd.phone_home, pd.phone_cell, pd.preferr_contact, insd.provider, pd.sex, pd.ethnicity, pd.race, pd.language, facility.name,insurance_companies.name as ins_comp, insurance_companies.groupedIn FROM schedule_appointments appt 
	INNER JOIN patient_data pd ON pd.id = appt.sa_patient_id 
	LEFT JOIN users u ON u.id = appt.sa_doctor_id
	LEFT JOIN facility ON facility.id = appt.sa_facility_id
	LEFT JOIN insurance_data insd ON insd.ins_caseid = appt.case_type_id && insd.ins_caseid != 0
	LEFT JOIN insurance_companies on insurance_companies.id=insd.provider	
	WHERE appt.sa_app_start_date between '$st_date' AND '$en_date' 
	AND appt.sa_patient_app_status_id NOT IN (203,201,18,19,20,3) AND insd.type = 'primary'";
	if(empty($rqPhyId) == false){
		$query .= " AND appt.sa_doctor_id IN ($rqPhyId)";
	}
	$query_res = imw_query($query);
	while($res=imw_fetch_assoc($query_res)){
		$appId = $res["saAppId"];
		$arrPatIds[$appId]=$res;
	}
	$appIds = array_keys($arrPatIds);
	$appIds = implode(',', $appIds);
	
	
	//MAKING OUTPUT DATA
	$file_name="patient_visit_report.csv";
	$csv_file_name= write_html("", $file_name);

	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');
	
	$arr=array();
	$arr[]="Patient Visit Info Report";
	$arr[]="Selected Provider :" .$selGrpDisp;
	$arr[]="From :" .$start_date." To :" .$end_date;
	$arr[]="Created by" .$op_name." on" .get_date_format(date("Y-m-d"))." ".date("h:i A");
	fputcsv($fp,$arr, ",","\"");
	
	$arr=array();
	$arr[]="Site Code";
	$arr[]="Site Name";
	$arr[]="ACO Flag";
	$arr[]="File Date";
	$arr[]="Service Date";
	$arr[]="Visit Time";
	$arr[]="Medical Record Number";
	$arr[]="Unique Identifier";
	$arr[]="Financial Class";
	$arr[]="Payor";
	$arr[]="Last Name";
	$arr[]="First Name";
	$arr[]="Middle Initial";
	$arr[]="Sex";
	$arr[]="Date Of Birth";
	$arr[]="Address 1";
	$arr[]="Address 2";
	$arr[]="City";
	$arr[]="State";
	$arr[]="Zip Code";
	$arr[]="Email";
	$arr[]="Phone";
	$arr[]="Language Id";
	$arr[]="Ethnicity";
	$arr[]="Race";
	$arr[]="Provider NPI";
	$arr[]="Provider Last Name";
	$arr[]="Provider First Name";
	$arr[]="Provider Middle Initial";
	$arr[]="Provider Title";
	$arr[]="Provider Specialty";
	fputcsv($fp,$arr, ",","\"");
	$fp = fopen ($csv_file_name, 'a+');
	
	$strHTML .= '<table class="rpt_table rpt rpt_table-bordered rpt_padding" width="1050" bgcolor="#FFF3E8">
				<tr>
					<td class="text_b_w" style="text-align:center;" width="80">Site Code</td>
					<td class="text_b_w" style="text-align:center;" width="80">Site Name</td>
					<td class="text_b_w" style="text-align:center;" width="80">ACO Flag</td>
					<td class="text_b_w" style="text-align:center;" width="80">File Date</td>
					<td class="text_b_w" style="text-align:center;" width="70">Service Date</td>
					<td class="text_b_w" style="text-align:center;" width="150">Visit Time</td>
					<td class="text_b_w" style="text-align:center;" width="80">Medical Record Number</td>
					<td class="text_b_w" style="text-align:center;" width="80">Unique Identifier</td>
					<td class="text_b_w" style="text-align:center;" width="70">Financial Class</td>
					<td class="text_b_w" style="text-align:center;" width="80">Payor</td>
					<td class="text_b_w" style="text-align:center;" width="80">Last Name</td>
					<td class="text_b_w" style="text-align:center;" width="80">First Name</td>
					<td class="text_b_w" style="text-align:center;" width="80">Middle Initial</td>
					<td class="text_b_w" style="text-align:center;" width="80">Sex</td>
					<td class="text_b_w" style="text-align:center;" width="80">Date Of Birth</td>
					<td class="text_b_w" style="text-align:center;" width="80">Address 1</td>
					<td class="text_b_w" style="text-align:center;" width="80">Address 2</td>
					<td class="text_b_w" style="text-align:center;" width="80">City</td>
					<td class="text_b_w" style="text-align:center;" width="80">State</td>
					<td class="text_b_w" style="text-align:center;" width="80">Zip Code</td>
					<td class="text_b_w" style="text-align:center;" width="80">Email</td>
					<td class="text_b_w" style="text-align:center;" width="80">Phone</td>
					<td class="text_b_w" style="text-align:center;" width="80">Language Id</td>
					<td class="text_b_w" style="text-align:center;" width="80">Ethnicity</td>
					<td class="text_b_w" style="text-align:center;" width="80">Race</td>
					<td class="text_b_w" style="text-align:center;" width="80">Provider NPI</td>
					<td class="text_b_w" style="text-align:center;" width="80">Provider Last Name</td>
					<td class="text_b_w" style="text-align:center;" width="80">Provider First Name</td>
					<td class="text_b_w" style="text-align:center;" width="80">Provider Middle Initial</td>
					<td class="text_b_w" style="text-align:center;" width="80">Provider Title</td>
					<td class="text_b_w" style="text-align:center;" width="80">Provider Specialty</td>
				</tr>';
	foreach($arrPatIds as $apptID => $appVal){
		$uLName = $appVal['ulname'];
		$uFName = $appVal['ufname'];
		$uMName = $appVal['umname'];
		$uNPI = $appVal['user_npi'];
		$uTitle = $appVal['pro_suffix'];
		$ptLName = $appVal['plname'];
		$ptFName = $appVal['pfname'];
		$ptMName = $appVal['pmname'];
		$pt_sex = $appVal['sex'];
		$ptsex = ($pt_sex == 'Male' ? "M" : ($pt_sex == 'Female' ? "F" : ($pt_sex == "" ? " " : "")));
		$ptDOB = $appVal['date_of_birth'];
		$apptDate = $appVal['apptDate'];
		$spid = $appVal['spid'];
		$city = $appVal['city'];
		$state = $appVal['state'];
		$zip = $appVal['postal_code'];
		$email = $appVal['email'];
		$street = $appVal['street'];
		$street2 = $appVal['street2'];
		$ethnicity = $appVal['ethnicity'];
		$ptlanguage = $appVal['language'];
		
		$language = '0';
		if (strpos($ptlanguage, 'Spanish') !== false) {
			$language = '1';
		}
		$race = $appVal['race'];
		$starttime = $appVal['starttime'];
		$facName = $appVal['name'];
		$ins_comp = $appVal['ins_comp'];
		$intPhoneNo = $appVal["phone_home"];
		$prefer_contact = $appVal["preferr_contact"];
		if($prefer_contact == 0){
			if(trim($appVal["phone_home"]) != ""){$intPhoneNo = core_phone_format($appVal["phone_home"]); }
		} else if($prefer_contact == 1){
			if(trim($appVal["phone_biz"]) != ""){$intPhoneNo = core_phone_format($appVal["phone_biz"]); }				
		}else if($prefer_contact == 2){
			if(trim($appVal["phone_cell"]) != ""){$intPhoneNo = core_phone_format($appVal["phone_cell"]); }				
		}
		$eid = getPatientEnc($spid);
		$groupedIn = $appVal['groupedIn'];
		$insGrpName = getInsGrp($groupedIn);
		
		//FOR CSV
		$arr=array();
		$arr[]="2103000747";
		$arr[]="Lehigh Valley Center for Sight PC";
		$arr[]="Non-ACO";
		$arr[]=$apptDate;
		$arr[]=$apptDate;
		$arr[]=$starttime;
		$arr[]=$spid;
		$arr[]=$eid;
		$arr[]=$insGrpName;
		$arr[]=$ins_comp;
		$arr[]=$ptLName;
		$arr[]=$ptFName;
		$arr[]=$ptMName;
		$arr[]=$ptsex;
		$arr[]=$ptDOB;
		$arr[]=$street;
		$arr[]=$street2;
		$arr[]=$city;
		$arr[]=$state;
		$arr[]=$zip;
		$arr[]=$email;
		$arr[]=$intPhoneNo;
		$arr[]=$language;
		$arr[]=$ethnicity;
		$arr[]=$race;
		$arr[]=$uNPI;
		$arr[]=$uLName;
		$arr[]=$uFName;
		$arr[]=$uMName;
		$arr[]=$uTitle;
		$arr[]="Ophthalmology";
		fputcsv($fp,$arr, ",","\"");
		// 2103000747 for Allentown and 2103004551 for Easton
		$strHTML .= '<tr valign="top">
					<td class="text" style="text-align:center;" width="80">2103000747</td>
					<td class="text" style="text-align:center;" width="80">Lehigh Valley Center for Sight PC</td>
					<td class="text" style="text-align:center;" width="80">Non-ACO</td>
					<td class="text" style="text-align:center;" width="80">'.$apptDate.'</td>
					<td class="text" style="text-align:center;" width="70">'.$apptDate.'</td>
					<td class="text" style="text-align:center;" width="70">'.$starttime.'</td>
					<td class="text" style="text-align:center;" width="80">'.$spid.'</td>
					<td class="text" style="text-align:center;" width="80">'.$eid.'</td>
					<td class="text" style="text-align:center;" width="70">'.$insGrpName.'</td>
					<td class="text" style="text-align:center;" width="80">'.$ins_comp.'</td>
					<td class="text" style="text-align:center;" width="80">'.$ptLName.'</td>
					<td class="text" style="text-align:center;" width="80">'.$ptFName.'</td>
					<td class="text" style="text-align:center;" width="80">'.$ptMName.'</td>
					<td class="text" style="text-align:center;" width="80">'.$ptsex.'</td>
					<td class="text" style="text-align:center;" width="80">'.$ptDOB.'</td>
					<td class="text" style="text-align:center;" width="80">'.$street.'</td>
					<td class="text" style="text-align:center;" width="80">'.$street2.'</td>
					<td class="text" style="text-align:center;" width="80">'.$city.'</td>
					<td class="text" style="text-align:center;" width="80">'.$state.'</td>
					<td class="text" style="text-align:center;" width="80">'.$zip.'</td>
					<td class="text" style="text-align:center;" width="80">'.$email.'</td>
					<td class="text" style="text-align:center;" width="80">'.$intPhoneNo.'</td>
					<td class="text" style="text-align:center;" width="80">'.$language.'</td>
					<td class="text" style="text-align:center;" width="80">'.$ethnicity.'</td>
					<td class="text" style="text-align:center;" width="80">'.$race.'</td>
					<td class="text" style="text-align:center;" width="80">'.$uNPI.'</td>
					<td class="text" style="text-align:center;" width="80">'.$uLName.'</td>
					<td class="text" style="text-align:center;" width="80">'.$uFName.'</td>
					<td class="text" style="text-align:center;" width="80">'.$uMName.'</td>
					<td class="text" style="text-align:center;" width="80">'.$uTitle.'</td>
					<td class="text" style="text-align:center;" width="80">Ophthalmology</td>
					</tr>';	
					$printFile = 1;
					$hasData = 1;
	}
	$strHTML .= '</table>';
	fclose($fp);
}
if($hasData){
	echo '<div class="text-center alert alert-info">Please click on link near application bottom to download CSV file.</div>';
} else {
	echo '<div class="text-center alert alert-info">No record found.</div>';
}
function getPatientEnc($pid){
	$qry1=imw_query("select encounterId, patient_id from  chart_master_table where patient_id='$pid' order by id desc");
	$co=imw_num_rows($qry1);
	$encounterId = "";
	if($co > 0){
		$crow=imw_fetch_array($qry1);
		$encounterId = $crow["encounterId"];	
	}
	return $encounterId;
}
function getInsGrp($gid){
	$qry2=imw_query("select title from ins_comp_groups where id='$gid'");
	$go=imw_num_rows($qry2);
	$encounterId = "";
	if($go > 0){
		$grow=imw_fetch_array($qry2);
		$title = $grow["title"];	
	}
	return $title;
}
?>	