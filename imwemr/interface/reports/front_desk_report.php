<?php 
$curDate = date($phpDateFormat);		
if(empty($Start_date) === true){
	$Start_date = $curDate;
	$End_date = $curDate;
}
$printFile = true;
$csvFileData = NULL;
if(empty($_POST['form_submitted']) === false){
	$pdfData = NULL;
	$printFile = false;
	
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

	$srchSDate = $Start_date;
	$srchEDate = $End_date;
	$startDate = getDateFormatDB($Start_date);
	$endDate = getDateFormatDB($End_date);
	
	if($hourFrom != '' && $hourTo != ''){
		$ampmFrom=$ampmTo='am';
		$hourFrom=($hourFrom<10)? '0'.$hourFrom: $hourFrom;
		$hourTo=($hourTo<10)? '0'.$hourTo: $hourTo;
		$hourFromL=$hourFrom;
		$hourToL=$hourTo;

		if($hourFrom>=12){ $hourFromL=$hourFrom-12; $ampmFrom='pm';}
		if($hourTo>=12){ $hourToL=$hourTo-12; $ampmTo='pm';}
		$hourFromL=($hourFromL<=0)? 12: $hourFromL;
		$hourToL=($hourToL<=0)? 12: $hourToL;
		
		$hourFromAmPm=$hourFromL.':00:00 '.strtoupper($ampmFrom);
		$hourToAmPm=$hourToL.':00:00 '.strtoupper($ampmTo);

		$hourFromL.=$ampmFrom;
		$hourToL.=$ampmTo;
		
		$hourFrom=$hourFrom.':00:00';
		$hourTo=$hourTo.':00:00';

		$formatDelDate='%Y-%m-%d %H:%i:%s';
		$delDate=$endDate.' '.$hourTo;
	}
	
	//--GET DEPARTMENT NAMES
	$rs=imw_query("Select * FROM department_tbl");
	while($res=imw_fetch_array($rs)){
		$arrDepartments[$res['DepartmentId']] = $res['DepartmentCode'];
	} unset($rs);
	
	//---- GET ALL POS FACILITIES DETAILS ------
	$posFacilityArr=array();
	$qry = "select pos_facilityies_tbl.facilityPracCode as name, pos_facilityies_tbl.pos_facility_id as id, pos_tbl.pos_prac_code from pos_facilityies_tbl
		left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id order by pos_facilityies_tbl.headquarter desc, pos_facilityies_tbl.facilityPracCode";
	$qryRs = imw_query($qry);
	while($qryRes = imw_fetch_array($qryRs)){
		$id = $qryRes['id'];
		$name = $qryRes['name'];
		$pos_prac_code = $qryRes['pos_prac_code'];
		$posFacilityArr[$id] = $name.' - '.$pos_prac_code;
	}
	
	//---- GET ALL FACILITIES ASSOCIATED WITH POS FAC 
	$sc_name = implode(',', $sc_name);
	if(empty($sc_name) === false){
		$rs = imw_query("Select id FROM facility WHERE fac_prac_code IN(".$sc_name.")");
		$schFacArr = array();
		while($res= imw_fetch_array($rs)){
			$schFacArr[$res['id']]=$res['id'];
		}
		$schFacStr = implode(',', $schFacArr);
	}
	
	//---- GET OPERATOR DETAILS ----------
	$opr = imw_query("select id,lname,fname,mname,pro_title,user_type,delete_status from users");
	$usersQryRes = $userNameArr = $userNameTwoCharArr = $operator_arr = $phyIdArr = array();
	while($res= imw_fetch_array($opr)){
		$usersQryRes[] = $res;
	}
	for($u=0;$u<count($usersQryRes);$u++){
		$id = $usersQryRes[$u]['id'];
		//--- PROVIDER TYPE CHECK ONLY FOR PHYSICIAN ---	
		$user_type = $usersQryRes[$u]['user_type'];
		if($user_type == 1 || $user_type == 5){
			$phyIdArr[] = $id;
		}
		//--- DELETE PROVIDER CHECK ---
		$delete_status = $usersQryRes[$u]['delete_status'];
		$operatorInitial='';
		$operator_arr[] = $id;
		$user_name_arr = array();
		$user_name_arr['TITLE'] = $usersQryRes[$u]['pro_title'];
		$user_name_arr['LAST_NAME'] = $usersQryRes[$u]['lname'];
		$user_name_arr['FIRST_NAME'] = $usersQryRes[$u]['fname'];
		$user_name_arr['MIDDLE_NAME'] = $usersQryRes[$u]['mname'];
		$userNameArr[$id] = changeNameFormat($user_name_arr);
		
		// TWO character array
		$operatorInitial = substr($usersQryRes[$u]['fname'],0,1);
		$operatorInitial .= substr($usersQryRes[$u]['lname'],0,1);
		$userNameTwoCharArr[$id] = strtoupper($operatorInitial);
	}
	
	$operatorId = implode(",",$operatorId);
	$operatorIdSel = $operatorId;
	if(empty($operatorId) == true){
		$operatorIdSel='';
	}
	
	$Physician = implode(",",$Physician);
	$PhysicianSel = $Physician;
	if(empty($Physician) == true){
		$PhysicianSel='';
		$Physician = join(',',$phyIdArr);
	}
	
	$grp_id = implode(",",$grp_id);

	//---- SET VIEW BY VALUES ----------
	$viewBy = implode(",",$viewBy);
	$viewBySel= $viewBy;
	if(empty($viewBy) == true){
		$viewBySel='';
		$viewBy = 'trans,inout';
	}
	$dateBy='transactionDate';
	
	$practice_name = $CLSReports->report_display_selected($sc_name, 'practice', 1, $allFacCount);
	$physican_name = $CLSReports->report_display_selected($Physician, 'physician', 1, $allPhyCount);
	$operator_name = $CLSReports->report_display_selected($operatorId, 'operator', 1, $allOprCount);
	$group_name = $CLSReports->report_display_selected($grp_id, 'group', 1, $allGrpCount);
	
	// MAKE Search Criteria Vars
	$varCriteria=$grp_id.'~'.$sc_name.'~'.$operatorIdSel.'~'.$PhysicianSel.'~'.$viewBySel.'~'.$groupBy;
	$varCriteria.='~'.$dateBy.'~'.$dayReport.'~'.$srchSDate.'~'.$srchEDate.'~'.$sortBy.'~'.$processReport;
	//---------------------

	//---- GET FRONT DESK REPORTS DATA ----------
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$op_name = ucfirst(trim($op_name_arr[1][0]));
	$op_name .= ucfirst(trim($op_name_arr[0][0]));
	$curDate = date($phpDateFormat.' h:i A');
		
	$firstColW=260;
	$secColW=255;
	$thirdColW=255;
	$fourthColW=260;
	if($processReport=='Summary'){
		$firstColW='25%';
		$secColW='25%';
		$thirdColW='25%';
		$fourthColW=225;
	}
	
	$fileHeaderData='
	<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" bgcolor="#FFF3E8">
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:260px;">Front Desk '.$processReport.' Report</td>
				<td class="rptbx2" style="width:260px;">Selected Group : '.$group_name.'</td>
				<td class="rptbx3" style="width:260px;">From : '.$Start_date.' To : '.$End_date.'</td>
				<td class="rptbx1" style="width:260px;">Created by '.$op_name.' on '.$curDate.'</td>
			</tr>
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:260px;">Selected Physician : '.$physican_name.'</td>
				<td class="rptbx2" style="width:260px;">Selected Facility: '.$practice_name.'</td>
				<td class="rptbx3" style="width:260px;">Selected Operator: '.$operator_name.'</td>
				<td class="rptbx1" style="width:260px;">Time:'.$hourFromL.'-'.$hourToL.'</td>
			</tr>
	</table>';
	require_once(dirname(__FILE__).'/front_desk_summary_report.php');
	if($printFile == true){
		$css_style_pdf = '<style type="text/css">'.file_get_contents("css/reports_pdf.css").'</style>';
		$css_style_html = '<style type="text/css">'.file_get_contents("css/reports_html.css").'</style>';
		$strHTML = $css_style_pdf.$pdfData;
		$file_location = write_html($strHTML);
		$csvData = $css_style_html.$fileHeaderData.$csvFileData;
		
		if($callFrom != 'scheduled'){
			echo $csvData;
		}
	} else{
		if($callFrom != 'scheduled'){
			echo '<div class="text-center alert alert-info">No record found.</div>';
		}
	}
}
?>