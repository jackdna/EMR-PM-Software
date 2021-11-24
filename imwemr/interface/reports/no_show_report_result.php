<?php

//getting report generator name
//do no move this code under post condition
$report_generator_name = NULL;
if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
	$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
	$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
	$report_generator_name = strtoupper($report_generator_name);
}

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
	
	$rqArrPhyId = $_REQUEST['providerID'];
	$rqPhyId = join(',',$rqArrPhyId);
	
	$rqArrFacId = $_REQUEST['facility_name'];
	$rqFacId = join(',',$rqArrFacId);
	
	$query = "select sa.id, sa.sa_comments, sa.sa_patient_id, sa.sa_patient_name, DATE_FORMAT(sa.sa_app_start_date, '$date_format_SQL') as 'appDate', 	
				TIME_FORMAT(sa.sa_app_starttime,'%h:%i %p') selStartTime, TIME_FORMAT(sa.sa_app_endtime,'%h:%i %p') selEndTime, 
				pd.fname as pFname, pd.mname as 'pMname', pd.lname as 'pLname', pd.street, pd.street2, pd.city, pd.state, pd.postal_code, pd.phone_home, pd.phone_biz, pd.phone_cell, pd.preferr_contact, pd.email
				from schedule_appointments sa 
				LEFT JOIN patient_data pd ON pd.id = sa.sa_patient_id 
				WHERE (sa.sa_app_start_date BETWEEN '".$st_date."' AND '".$en_date."') 
				AND sa.sa_patient_app_status_id = '3'";
			  
	if(empty($rqPhyId) == false){
		$query .= " AND sa.sa_doctor_id IN ($rqPhyId)";
	}if(empty($rqFacId) == false){
		$query .= " AND sa.sa_facility_id IN ($rqFacId)";
	}
	$query .= " ORDER BY sa.sa_app_start_date ASC, sa.sa_app_starttime ASC";
	$query_res = imw_query($query);
	
	$page_content = '';
	while($appVal=imw_fetch_assoc($query_res)){
		$printFile = true;
		$appt_id=$appVal["id"];
		$intPhoneNo = $appVal["phone_home"];
		$prefer_contact = $appVal["preferr_contact"];
		if($prefer_contact == 0){
			if(trim($appVal["phone_home"]) != ""){$intPhoneNo = "Home Phone"; }
		} else if($prefer_contact == 1){
			if(trim($appVal["phone_biz"]) != ""){$intPhoneNo = "Biz Phone"; }				
		}else if($prefer_contact == 2){
			if(trim($appVal["phone_cell"]) != ""){$intPhoneNo = "Cell Phone"; }				
		}
		
		$appt_time='';
		$appt_time=$appVal["selStartTime"]." To ".$appVal["selEndTime"];
		$page_content .= '<tr valign="top">
					<td class="text" style="vertical-align:middle;text-align:left;width:9%">&nbsp;'.$appVal["sa_patient_id"].'</td>
					<td class="text" style="vertical-align:middle;text-align:left;width:9%">&nbsp;'.$appVal["pLname"].'</td>
					<td class="text" style="vertical-align:middle;text-align:left;width:9%">&nbsp;'.$appVal["pFname"].'</td>
					<td class="text" style="vertical-align:middle;text-align:left;width:9%">&nbsp;'.$appVal["phone_home"].'</td>
					<td class="text" style="vertical-align:middle;text-align:left;width:9%">&nbsp;'.$appVal["phone_biz"].'</td>
					<td class="text" style="vertical-align:middle;text-align:left;width:9%">&nbsp;'.$appVal["phone_cell"].'</td>
					<td class="text" style="vertical-align:middle;text-align:left;width:9%">&nbsp;'.$intPhoneNo.'</td>
					<td class="text" style="vertical-align:middle;text-align:left;width:9%">&nbsp;'.$appVal["appDate"].'</td>
					<td class="text" style="vertical-align:middle;text-align:left;width:9%">&nbsp;'.$appt_time.'</td>
					<td class="text" style="text-align:left;width:19%">
					<input type="hidden" id="saved_appt_comment_'.$appt_id.'" name="saved_appt_comment_'.$appt_id.'" value="'. stripslashes($appVal["sa_comments"]).'">
					<textarea rows="2" cols="20" style="width: 100%" name="appt_comment_'.$appt_id.'" id="appt_comment_'.$appt_id.'" class="appt_comments" onBlur="remove_operator_name_date(\''.$appt_id.'\'); " onFocus="get_operator_name_date(\'appt_comment_'.$appt_id.'\')">'. stripslashes($appVal["sa_comments"]).'</textarea></td>
					</tr>';	
	}
	
	$page_data='
			<table class="rpt_table rpt_table-bordered rpt_padding">
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:20%">'.$dbtemp_name.'</td>	
				<td class="rptbx2" style="width:40%; text-align:center">From : '.$start_date.' To : '.$end_date.'</td>					
				<td class="rptbx3" style="width:40%; text-align:center">Created By: '.$report_generator_name.' on '.date($phpDateFormat)." ".date("h:i A").'</td>
			</tr>
			</table>
			<table class="rpt_table rpt_table-bordered">
			<tr>
				<td class="text_b_w" style="text-align:center;width:9%">Pt. ID</td>
				<td class="text_b_w" style="text-align:center;width:9%">Last Name</td>
				<td class="text_b_w" style="text-align:center;width:9%">First Name</td>
				<td class="text_b_w" style="text-align:center;width:9%">Home Phone</td>
				<td class="text_b_w" style="text-align:center;width:9%">Biz Phone</td>
				<td class="text_b_w" style="text-align:center;width:9%">Cell Phone</td>
				<td class="text_b_w" style="text-align:center;width:9%">Preferred</td>
				<td class="text_b_w" style="text-align:center;width:9%">Appt. Date</td>
				<td class="text_b_w" style="text-align:center;width:9%">Appt. Time</td>
				<td class="text_b_w" style="text-align:center;width:19%">Appt. Comments</td>
			</tr>
			</table>
			<table class="rpt_table rpt_table-bordered">
			'.$page_content.'
			</table>';
	
	$pdf_data= '
			<page backtop="11mm" backbottom="10mm">			
				<page_footer>
					<table style="width:100%;">
						<tr>
							<td style="text-align:center; width: 100%">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
					<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%">
					<tr>
						<td class="rptbx1" style="width:20%">'.$dbtemp_name.'</td>	
						<td class="rptbx2" style="width:40%; text-align:center">From : '.$start_date.' To : '.$end_date.'</td>					
						<td class="rptbx3" style="width:40%; text-align:center">Created By: '.$report_generator_name.' on '.date($phpDateFormat)." ".date("h:i A").'</td>
					</tr>
					</table>
					<table class="rpt_table rpt_table-bordered rpt_padding" style="width:100%">
					<tr>
						<td class="text_b_w" style="text-align:center;width:9%">Pt. ID</td>
						<td class="text_b_w" style="text-align:center;width:9%">Last Name</td>
						<td class="text_b_w" style="text-align:center;width:9%">First Name</td>
						<td class="text_b_w" style="text-align:center;width:9%">Home Phone</td>
						<td class="text_b_w" style="text-align:center;width:9%">Biz Phone</td>
						<td class="text_b_w" style="text-align:center;width:9%">Cell Phone</td>
						<td class="text_b_w" style="text-align:center;width:9%">Preferred</td>
						<td class="text_b_w" style="text-align:center;width:9%">Appt. Date</td>
						<td class="text_b_w" style="text-align:center;width:9%">Appt. Time</td>
						<td class="text_b_w" style="text-align:center;width:19%">Appt. Comments</td>
					</tr></table>
				</page_header>
			<table style="width:100%" class="rpt_table rpt_table-bordered"  style="width:100%">'.
			str_replace('textarea','span',str_replace('textarea rows="2" cols="20" style="width: 100%"','span',$page_content))
			.'</table>
			</page>';	
	
	//--- CREATE PDF FILE FOR PRINTING -----
	$hasData=0;
	if($printFile == true and $page_data != ''){
		$hasData=1;
		$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
		$csv_file_data= $styleHTML.$page_data;

		$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
		$strHTML = $stylePDF.$pdf_data;

		$file_location = write_html($strHTML);
	}else{
		$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
	}
	echo $csv_file_data;
}
?>	