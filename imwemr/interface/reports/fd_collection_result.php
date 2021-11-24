<?php
$curDate = date($phpDateFormat);		
if(empty($Start_date) === true){
	$Start_date = $curDate;
	$End_date = $curDate;
}
$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
$op_name = ucfirst(trim($op_name_arr[1][0]));
$op_name .= ucfirst(trim($op_name_arr[0][0]));
$curDate = date(phpDateFormat().' h:i A');

$printFile = true;
$csvFileData = NULL;
if(empty($_POST['form_submitted']) === false){
	$pdfData = NULL;
	$printFile = false;
	$reportType = 'checkinout';	
	
	// --- CHECK IF CI-CO/ENCOUNTER SET IN ADMIN - REPORT FLOW WILL CHANGE ACOORDINGLY
	$query = imw_query("Select show_check_in, show_check_out from copay_policies");
	$policyQryRes = imw_fetch_assoc($query);
	if($policyQryRes['show_check_in']=='0' && $policyQryRes['show_check_out']=='0'){
		$reportType = 'encounter';
	}
	
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

	$startDate = getDateFormatDB($Start_date);
	$endDate = getDateFormatDB($End_date);

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
	
	// GET ALL FACILITIES IN ARRAY
	$facqry = imw_query("Select id, name, fac_prac_code from facility ORDER BY name");
	$allFacilityArr =array();
	$return = '';
	$selFacArr =  $sc_name;
	$arrSelFac = array();
	$strSelFac='';

	while($qryRes = imw_fetch_assoc($facqry)){
		$id = $qryRes['id'];
		$name = $qryRes['name'];
		$posFacId = $qryRes['fac_prac_code'];
		$allFacilityArr[$id] = $name; 
		
		if(empty($sc_name)==false){
			if(in_array($posFacId, $selFacArr)){
				$arrSelFac[$id] = $id;
			}
		}
	}
	if(sizeof($arrSelFac)>0){
		$strSelFac = implode(',', $arrSelFac);
	}
	// -------------------------------------------
	$operatorInitial='';
	$sel_operatorId=$operatorId=implode(",",$operatorId);
	$sel_Physician=$Physician;
	if(empty($operatorId) == true){
		$operatorId = join(',',$operator_arr);
		$sel_operatorId="";
	}
	
	$Physician = implode(",",$Physician);
	$PhysicianSel = $Physician;
	if(empty($Physician) == true){
		$PhysicianSel='';
		$Physician = join(',',$phyIdArr);
	}
	$grp_id = implode(",",$grp_id);
	
	$practice_name = $CLSReports->report_display_selected($sc_name, 'practice', 1, $allFacCount);
	$physican_name = $CLSReports->report_display_selected($PhysicianSel, 'physician', 1, $allPhyCount);
	$operator_name = $CLSReports->report_display_selected($sel_operatorId, 'operator', 1, $allOprCount);
	$group_name = $CLSReports->report_display_selected($grp_id, 'group', 1, $allGrpCount);
	
	
	// GET ALL CI/CO FIELDS IN ARRAY
	$query = imw_query("Select * FROM check_in_out_fields");
	$allINOutFields =array(); $qryRes = array();
	while($res= imw_fetch_array($query)){
		$qryRes[] = $res;
	}
	$return = '';
	for($i=0;$i<count($qryRes);$i++){
		if($qryRes[$i]['item_name']!=''){
			$id = $qryRes[$i]['id'];
			$item_name = $qryRes[$i]['item_name'];
			$allINOutFields[$id] = $item_name; 
		}
	}
	// -------------------------------------------
	//---- GET FRONT DESK REPORTS DATA ----------
	require_once(dirname(__FILE__).'/fd_SummaryReport.php');
	if($printFile == true){
	$css_style_pdf = '<style type="text/css">'.file_get_contents("css/reports_pdf.css").'</style>';
	$css_style_html = '<style type="text/css">'.file_get_contents("css/reports_html.css").'</style>';
	$strHTML = <<<DATA
		$css_style_pdf
		<style>
		.total-row{
			height:1px; 
			padding:0px;
			background:#009933;		
		}
		</style>
		$pdfData
DATA;

$tooltip='Red coloured CI/CO and Pre-Payments represents that there is refund amount deducted from these payments.';

if($strHTML){
	$strHTML .= '<table width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8" >
		<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
		<tr><td style="width:20px;" class="info" style="background-color:#FFFFFF;">&nbsp;</td>
		<td style="width:4px;" height="5px;" bgcolor="#FF0000"></td>
		<td class="info" style="padding-left:20px; ; background-color:#FFFFFF;">
		'.$tooltip.'
		</td>
		</tr>
		</table>';
	}
}

if($csvFileData){
	$csvFileData .= '<table width="100%" cellspacing="1" cellpadding="1" border="0" bgcolor="#FFF3E8" >
	<tr><td style="height:10px; background-color:#FFFFFF;" colspan="3"></td></tr>
	<tr><td style="width:20px;" style="background-color:#FFFFFF;">&nbsp;</td>
	<td style="width:4px;" height="5px;" bgcolor="#FF0000"></td>
	<td class="info" style="padding-left:20px; ; background-color:#FFFFFF;">
	'.$tooltip.'<br/>Refund amount can be view by mouse over on red coloured amount.
	</td>
	</tr>
	</table>';
}

$file_location = write_html($strHTML);
if($printFile == true){
	echo $csvFileData;
} else {
	echo '<div class="text-center alert alert-info">No record found.</div>';
}
}
 
?>