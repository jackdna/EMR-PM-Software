<?php 
ini_set("memory_limit","2048M");
set_time_limit (300);
include_once($GLOBALS['fileroot'] . '/library/classes/scheduler/appt_page_functions.php');
$styleHTML='<style>'.file_get_contents($GLOBALS['fileroot'].'/interface/reports/css/reports_html.css').'</style>';
echo $styleHTML;

$dateFormat= get_sql_date_format();
$phpDateFormat = phpDateFormat();
$curDate = date($phpDateFormat.' h:i A');		

$FCName= $_SESSION['authId'];

$page_data = NULL;
$pdf_data = NULL;
$curDate = date($phpDateFormat);
if($Start_date == ""){
	$Start_date = $curDate;
	$End_date = $curDate;
}
$printPdFBtn  = 0;
$csvBtn  = 0;
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
	//---------------------

	//VARIABLE DECLARATION
	$join_query=$where_query='';	
	
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$createdBy = ucfirst(trim($op_name_arr[1][0]));
	$createdBy .= ucfirst(trim($op_name_arr[0][0]));



	$StartDate = getDateFormatDB($Start_date);
	$EndDate = getDateFormatDB($End_date);
	
	$qry="Select id, fac_prac_code FROM facility";
	$rs=imw_query($qry);
	$arrAllPosFacOfFacility=array();
	while($res=imw_fetch_assoc($rs)){
		$arrAllPosFacOfFacility[$res['id']]=$res['fac_prac_code'];
	}unset($rs);
	
	
	$qry="select pos.pos_facility_id, pos.facilityPracCode, pos_tbl.pos_prac_code FROM pos_facilityies_tbl pos LEFT JOIN pos_tbl ON pos_tbl.pos_id=pos.pos_id";
	$rs=imw_query($qry);
	$arrAllPosFacility=array();
	while($res=imw_fetch_assoc($rs)){
		$arrAllPosFacility[$res['pos_facility_id']]=$res['facilityPracCode'].' - '.$res['pos_prac_code'];
	}unset($rs);
	
	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
	}
	
	
	

	$qry="select par.*,pd.* from patient_app_recall as par,patient_data as pd where (recalldate BETWEEN '$StartDate' AND '$EndDate') and par.patient_id=pd.id AND par.descriptions != 'MUR_PATCH' and (pd.lname between 'a' and 'z' or pd.lname like 'z%') and par.procedure_id not in ('-1') AND pd.patientStatus='Active' ORDER BY pd.lname asc,pd.fname asc";
	
	$rs=@imw_query($qry);
	$num=@imw_num_rows($rs);

	
	$table_string = '';
	$table_string = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	if($num > 0) {
	$table_string .='<table style="width:100%" class="rpt_table rpt rpt_table-bordered rpt_padding">
		<tr>
			<td class="rptbx1" style="width:350px">'.$dbtemp_name.'</td>
			<td class="rptbx2" style="text-align:center; width:350px">Report Period : '.$Start_date.' to '.$End_date.'</td>
			<td class="rptbx3" style="text-align:center; width:350px">
				Created By '.$createdBy.' on '.$curDate.'
			</td>
		</tr>
	</table>';
	$table_string .= '<table class="rpt_table rpt rpt_table-bordered">
	<tr>
		<td style="width:70px; text-align:center;" class="text_b_w">Account-ID</td>             
		<td style="width:120px; text-align:center;"class="text_b_w">Patient Name</td>
		<td style="width:160px; text-align:center;" class="text_b_w">Address</td>							
		<td style="width:70px; text-align:center;" class="text_b_w">City</td>
		<td style="width:50px; text-align:center;" class="text_b_w">State</td>
		<td style="width:50px; text-align:center;" class="text_b_w">Zip</td>
		<td style="width:75px; text-align:center;" class="text_b_w">Telephone</td>
		<td style="width:75px; text-align:center;" class="text_b_w">DOB</td>
		<td style="width:120px; text-align:center;" class="text_b_w">Provider</td>			
		<td style="width:120px; text-align:center;" class="text_b_w">Default Facility</td>			
		<td style="width:70px; text-align:center;" class="text_b_w">Recall Date</td>
	</tr>';
	
	while($rw=imw_fetch_assoc($rs)){	
			$patientid=$rw['patient_id'];
			$hipaa_voice=$rw['hipaa_voice'];
			$pat_det=patient_data($patientid);
			$proc_id=$rw['procedure_id'];
			$desc=$rw['descriptions'];
			$oper=$rw['operator'];
			$providerID=$rw['providerID'];
			
			$recalldate=strtotime($rw['recalldate']);
			$recalldate=get_date_format($rw['recalldate']);
			$dob=get_date_format($pat_det[4]);
			$phone_default = $rw["phone_home"];
			$prefer_contact = $rw["preferr_contact"];
			
			$pos_facility=$rw['default_facility'];
					
			if($prefer_contact == 0)
			{
				if(trim($rw["phone_home"]) != ""){$phone_default = $rw["phone_home"]; }
			}
			else if($prefer_contact == 1)
			{
				if(trim($rw["phone_biz"]) != ""){$phone_default = $rw["phone_biz"]; }				
			}
			else if($prefer_contact == 2)
			{
				if(trim($rw["phone_cell"]) != ""){$phone_default = $rw["phone_cell"]; }				
			}
			
			$telephone = core_phone_format($phone_default);	
			
			$address = $pat_det[1].' '.$pat_det[2];
			$address = wordwrap($address, 23, "<br>\n", true);
			$table_string .=	'<tr bgcolor="#FFFFFF">
				<td>'.$patientid.'</td>                
				<td>'.$pat_det[9].'</td>
                <td>'.$address.'</td>							
                <td>'.$pat_det[5].'</td>
                <td>'.$pat_det[6].'</td>
                <td>'.$pat_det[7].'</td>
                <td>'.$telephone.'</td>
                <td>'.$dob.'</td>
                <td>'.$providerNameArr[$providerID].'</td>
				<td>'.$arrAllPosFacility[$pos_facility].'</td>			
				<td>'.$recalldate.'</td>
           </tr>';
	}	
		$table_string .='</table>';
		$csvBtn  = 1;
		$printPdFBtn  = 1;
		$showbtn  = 1;
		$file_location = write_html($table_string);	
	} else {
		$table_string = '<div class="text-center alert alert-info">No Record Found.</div>';
		$printPdFBtn  = 0;
		$csvBtn  = 0;
		$showbtn  = 0;
	}
	echo $table_string;
}
?>