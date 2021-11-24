<?php
require_once('../../library/classes/audit_common_function.php');
require_once('../../library/classes/imgGdFun.php');
$_REQUEST['order_by']=xss_rem($_REQUEST['order_by'],1);

function sort_review_values($str){
	if(!empty($str)){
	$str = stripslashes($str);
	$arstr = explode("<br>", $str);
	asort($arstr);
	$str = implode("<br>",$arstr);
	}
	return $str;
}

$Start_date = $_POST['dat_frm'];
$End_date = $_POST['dat_to'];
$dateFormat= get_sql_date_format();
$phpDateFormat = phpDateFormat();
$curDate = date($phpDateFormat.' h:i A');
$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
$createdBy = ucfirst(trim($op_name_arr[1][0]));
$createdBy .= ucfirst(trim($op_name_arr[0][0]));

//--- get recent selected patient ------
$auth_id = $_SESSION['authId'];
$qry = "select patient_id from recent_users 
		where provider_id = $auth_id order by enter_date";
$qryRes = imw_query($qry);

$searchOption = '';
while($row = imw_fetch_assoc($qryRes)){
	$patient_id = $row['patient_id'];
	$qry = "select concat(lname,', ',fname) as name , mname from patient_data
			where id = $patient_id";
	$patientDetails = imw_query($qry);
	$patientDetails = imw_fetch_assoc($patientDetails);	
	$patient_name = ucwords($patientDetails[0]['name'].' '.substr($patientDetails[0]['mname'],0,1));
	$searchOption .= '
		<option value = "'.$patient_id.':'.$patient_name.'">'.$patient_name.' - '.$patient_id.'</option>
	';
}
//--- Get Patient Search ------
$dat_frm_exp=explode('-',xss_rem($_POST['dat_frm'],1));
$dat_to_exp=explode('-',xss_rem($_POST['dat_to'],1));
$action=$_POST['action'];
$ptid=xss_rem(imw_real_escape_string($_POST['patientId']), 1);
$ptname=$_POST['patient'];
$errflag = false;
if($ptid=="" && $ptname!=""){
	$sql = "SELECT `id` FROM `patient_data` WHERE `lname`='".$ptname."' LIMIT 1";
	$dt = imw_query($sql);
	if(imw_num_rows($dt)>0){
		$dt = imw_fetch_assoc($dt);
		$ptid = $dt['id'];
	}
	else{
		$errflag = true;
	}
}

$operater=$_POST['operater'];
if($operater<>""){
	$opr_whr="at.Operater_Id='$operater' and";
}
if($action<>""){
	switch ($action):
	case "app_start/app_stop":
		$acc = explode("/",$action);
		$act_whr="(at.Action='$acc[0]' or at.Action='$acc[1]') and";		
		break;
	case "user_login_s/user_logout_s":
		$acc = explode("/",$action);
		$act_whr="(at.Action='$acc[0]' or at.Action='$acc[1]') and";
		break;	
	default:
		$act_whr="at.Action='$action' and";
	endswitch;	
}

if($_POST['dat_frm'] && $_POST['dat_to']){
	$dat_frm_final = getDateFormatDB($_POST['dat_frm']);
	$dat_to_final = getDateFormatDB($_POST['dat_to']);
	$from_time = ' 00:00:00';
	$to_time = ' 23:59:59';			
	$dateRange = " (Date_Time BETWEEN '".$dat_frm_final.$from_time."'"." AND '".$dat_to_final.$to_time."') and";	
}

$arrMEDHXOculer = array();
$arrMEDHXOculer [] = array("Filed_Label"=> "u_wear","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "None");
$arrMEDHXOculer [] = array("Filed_Label"=> "u_wear","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Glasses");
$arrMEDHXOculer [] = array("Filed_Label"=> "u_wear","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Contact Lenses");
$arrMEDHXOculer [] = array("Filed_Label"=> "u_wear","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Glasses And Contact Lenses");
$arrMEDHXOculer [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Blurred or Poor Vision :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Poor Night Vision :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Gritty Sensation :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Trouble Reading Signs :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Glare From Lights :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Tearing :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Poor Depth Perception :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Halos Around Lights :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '9',"Filed_Label_Og_Val"=> "Itching or Burning :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '10',"Filed_Label_Og_Val"=> "Trouble Identifying Colors :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '11',"Filed_Label_Og_Val"=> "See Spots or Floaters :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '12',"Filed_Label_Og_Val"=> "Eye Pain :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "Double Vision :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "See Light Flashes :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "eye_problem","Filed_Label_Val"=> '15',"Filed_Label_Og_Val"=> "Redness or Bloodshot :&#x2713;");

$arrMEDHXOculer [] = array("Filed_Label"=> "any_conditions_other_u","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "any_conditions_other_u","Filed_Label_Val"=> '',"Filed_Label_Og_Val"=> "&#x2717;");

$arrMEDHXOculer [] = array("Filed_Label"=> "any_conditions_u","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You Dry Eyes :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "any_conditions_u","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "You Macular Degeneration :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "any_conditions_u","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "You Glaucoma :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "any_conditions_u","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "You Retinal Detachment :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "any_conditions_u","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "You Cataracts :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "any_conditions_u","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "You Keratoconus :&#x2713;");

$arrMEDHXOculer [] = array("Filed_Label"=> "rel_elem_chronicDesc","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative Dry Eyes :");
$arrMEDHXOculer [] = array("Filed_Label"=> "rel_elem_chronicDesc","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Macular Degeneration :");
$arrMEDHXOculer [] = array("Filed_Label"=> "rel_elem_chronicDesc","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Glaucoma :");
$arrMEDHXOculer [] = array("Filed_Label"=> "rel_elem_chronicDesc","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Retinal Detachment :");
$arrMEDHXOculer [] = array("Filed_Label"=> "rel_elem_chronicDesc","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Cataracts :");
$arrMEDHXOculer [] = array("Filed_Label"=> "rel_elem_chronicDesc","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Relative Keratoconus :");
$arrMEDHXOculer [] = array("Filed_Label"=> "rel_elem_chronicDesc","Filed_Label_Val"=> 'other',"Filed_Label_Og_Val"=> "Relative other :");


$arrMEDHXOculer [] = array("Filed_Label"=> "rel_any_conditions_relative","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative Dry Eyes :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "rel_any_conditions_relative","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Macular Degeneration :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "rel_any_conditions_relative","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Glaucoma :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "rel_any_conditions_relative","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Retinal Detachment :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "rel_any_conditions_relative","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Cataracts :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "rel_any_conditions_relative","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Relative Keratoconus :&#x2713;");


$arrMEDHXOculer [] = array("Filed_Label"=> "this_blood_sugar_fasting","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Fasting :&#x2713;");
$arrMEDHXOculer [] = array("Filed_Label"=> "this_blood_sugar_fasting","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "Fasting :&#x2717;");

$arrMEDHXOculer [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative Dry Eyes");
$arrMEDHXOculer [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Macular Degeneration");
$arrMEDHXOculer [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Glaucoma");
$arrMEDHXOculer [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Retinal Detachment");
$arrMEDHXOculer [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Cataracts");
$arrMEDHXOculer [] = array("Filed_Label"=> "elem_chronicDesc","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You Dry Eyes");
$arrMEDHXOculer [] = array("Filed_Label"=> "elem_chronicDesc","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "You Macular Degeneration");
$arrMEDHXOculer [] = array("Filed_Label"=> "elem_chronicDesc","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "You Glaucoma");
$arrMEDHXOculer [] = array("Filed_Label"=> "elem_chronicDesc","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "You Retinal Detachment");
$arrMEDHXOculer [] = array("Filed_Label"=> "elem_chronicDesc","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "You Cataracts");
$arrMEDHXOculer [] = array("Filed_Label"=> "elem_chronicDesc","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "You Keratoconus");
$arrMEDHXOculer [] = array("Filed_Label"=> "elem_chronicDesc","Filed_Label_Val"=> 'other',"Filed_Label_Og_Val"=> "You other");
$arrMEDHXOculer [] = array("Filed_Label"=> "elem_chronicRelative","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative Dry Eyes");
$arrMEDHXOculer [] = array("Filed_Label"=> "elem_chronicRelative","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Macular Degeneration");
$arrMEDHXOculer [] = array("Filed_Label"=> "elem_chronicRelative","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Glaucoma");
$arrMEDHXOculer [] = array("Filed_Label"=> "elem_chronicRelative","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Retinal Detachment");
$arrMEDHXOculer [] = array("Filed_Label"=> "elem_chronicRelative","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Cataracts");
$arrMEDHXOculer [] = array("Filed_Label"=> "elem_chronicRelative","Filed_Label_Val"=> 'other',"Filed_Label_Og_Val"=> "Relative other :");
$arrMEDHXGenHealth = array();

$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You High Blood Pressure :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "You Heart Problem :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "You Arthritis :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "You Lung Problems :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "You Stroke :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "You Thyroid Problems :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "You Diabetes :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "You Ulcers :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "You LDL :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "You Cancer :&#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You High Blood Pressure :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "You Heart Problem :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "You Arthritis :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "You Lung Problems :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "You Stroke :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "You Thyroid Problems :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "You Diabetes :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "You Ulcers :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "You LDL :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_u1_n","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "You Cancer :&#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative High Blood Pressure :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Heart Problem :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Relative Arthritis :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Lung Problems :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Stroke :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Relative Thyroid Problems :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Diabetes :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Relative Ulcers :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "Relative LDL :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "Relative Cancer :&#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative High Blood Pressure :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Heart Problem :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Relative Arthritis :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Lung Problems :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Stroke :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Relative Thyroid Problems :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Diabetes :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Relative Ulcers :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "Relative LDL :&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_relative1_n","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "Relative Cancer :&#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_others_rel","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Other &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_others_both","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You Other &#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_others_both","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You Other");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "any_conditions_others_both","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Other");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "elem_subCondition_u1", "Filed_Label_Val"=> '7.1', "Filed_Label_Og_Val"=> "Arthrities :RA");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "elem_subCondition_u1", "Filed_Label_Val"=> '7.2', "Filed_Label_Og_Val"=> "Arthrities : OA");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Fever &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Fever &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Weight Loss &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Rash &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Skin Disease &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Fatigue &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Sinus Infection &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Post Nasal Drips &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Runny Nose &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Dry Mouth &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Deafness &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Cough &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Bronchitis &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Shortness of Breath &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Asthma &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Emphysema &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "COPD &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "TB &#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Chest Pain &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Congestive Heart Failure &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Irregular Heart beat &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Shortness of Breath &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "High Blood Pressure &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Low Blood Pressure &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Pacemaker/defibrillator &#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Vomiting &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Ulcers &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Diarrhea &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Bloody Stools &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Hepatitis &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Jaundice &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Constipation &#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Genital Ulcers &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Discharge &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Kidney Stones &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Blood in Urine &#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_aller","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Seasonal Allergies &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_aller","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Hay Fever &#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Headache &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Migraines &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Paralysis Fever &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Joint Ache &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Seizures &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Numbness &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Faints &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Stroke &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '9',"Filed_Label_Og_Val"=> "Multiple Sclerosis &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '10',"Filed_Label_Og_Val"=> "Alzheimer's Disease &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '11',"Filed_Label_Og_Val"=> "Parkinson's Disease &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '12',"Filed_Label_Og_Val"=> "Dementia &#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Rashes &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Wounds &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Breast Lumps &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Eczema &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Dermatitis &#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Depression &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Anxiety &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Paranoia &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Sleep Patterns &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Mental and/or emotional factors &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Alzheimer's Disease &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Parkinson's disease &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Memory Loss &#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Anemia &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Blood Transfusions &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Excessive Bleeding &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Purpura &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Infection &#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Pain &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Joint Ache &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Stiffness &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Swelling &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Paralysis Fever &#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Mood Swings &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Constipation &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Polydipsia &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Hypothyroidism &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Hyperthyroidism &#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Vision loss &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Eye pain &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Double vision &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Headache &#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Negative for constitutional &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Negative for ear, nose, mouth & throat &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Negative for respiratory &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Negative for cardiovascular &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Negative for gastrointenstinal &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Negative for genitourinary &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Negative for allergic/immunologic &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Negative for neurological &#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '9',"Filed_Label_Og_Val"=> "Negative for integumentary &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '10',"Filed_Label_Og_Val"=> "Negative for psychiatry &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '11',"Filed_Label_Og_Val"=> "Negative for hemotologic/lymphatic &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '12',"Filed_Label_Og_Val"=> "Negative for musculoskeletal &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "Negative for endocrine &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "Negative for eyes &#x2713;");
//
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "Under Control : High Blood Pressure &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '2', "Filed_Label_Og_Val"=> "Under Control : Heart Problem &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '3', "Filed_Label_Og_Val"=> "Under Control : Arthrities &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '4', "Filed_Label_Og_Val"=> "Under Control : Lung Problems &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '5', "Filed_Label_Og_Val"=> "Under Control : Stroke &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '6', "Filed_Label_Og_Val"=> "Under Control : Thyroid Problems &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '7', "Filed_Label_Og_Val"=> "Under Control : Diabetes &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '8', "Filed_Label_Og_Val"=> "Under Control : LDL &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '9', "Filed_Label_Og_Val"=> "Under Control : Ulcers &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '10', "Filed_Label_Og_Val"=> "Under Control : Others &#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_under_control", "Filed_Label_Val"=> '11', "Filed_Label_Og_Val"=> "Under Control : Cancer  &#x2713;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_annual_colorectal_cancer_screenings", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_annual_colorectal_cancer_screenings", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "&#x2717;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_receiving_annual_mammogram", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_receiving_annual_mammogram", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "&#x2717;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_received_flu_vaccine", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_received_flu_vaccine", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "&#x2717;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_high_risk_for_cardiac", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "chk_high_risk_for_cardiac", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "&#x2717;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "con_for_nut", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "con_for_nut", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "&#x2717;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "con_for_phy", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "&#x2713;");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "con_for_phy", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "&#x2717;");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "imchk_child_immunization", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "Selected");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "imchk_child_immunization", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "De-Selected");

$arrMEDHXGenHealth [] = array("Filed_Label"=> "immunization_child1", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "Checked");
$arrMEDHXGenHealth [] = array("Filed_Label"=> "immunization_child1", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "Unchecked");

$arrMEDHX [] = array("Filed_Label"=> "offered_cessation_counseling", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "De-Selected");
$arrMEDHX [] = array("Filed_Label"=> "offered_cessation_counseling", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "Selected");

$arrMEDHX [] = array("Filed_Label"=> "radio_family_smoke", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "No");
$arrMEDHX [] = array("Filed_Label"=> "radio_family_smoke", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "Yes");

//-----Karandeep Singh Dhaliwal

$arrMEDHXSocial = array();
$arrMEDHXSocial [] = array("Filed_Label"=> "smoke","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Yes");
$arrMEDHXSocial [] = array("Filed_Label"=> "smoke","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "No");

$arrMEDHX [] = array("Filed_Label"=> "med_type","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Ocular &#x2717;");
$arrMEDHX [] = array("Filed_Label"=> "med_type","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Ocular &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "compliant","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "No");
$arrMEDHX [] = array("Filed_Label"=> "compliant","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Yes");


$arrMEDHX [] = array("Filed_Label"=> "sg_occular","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Ocular &#x2717;");
$arrMEDHX [] = array("Filed_Label"=> "sg_occular","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Ocular &#x2713;");

$arrMEDHX [] = array("Filed_Label"=> "ag_occular_drug","Filed_Label_Val"=> 'fdbATDrugName',"Filed_Label_Og_Val"=> "Drug");
$arrMEDHX [] = array("Filed_Label"=> "ag_occular_drug","Filed_Label_Val"=> 'fdbATIngredient',"Filed_Label_Og_Val"=> "Ingredient");
$arrMEDHX [] = array("Filed_Label"=> "ag_occular_drug","Filed_Label_Val"=> 'fdbATAllergenGroup',"Filed_Label_Og_Val"=> "Allergen");

$arrProvider = array();
$arrProvider [] = array("Filed_Label"=> "Lock/Unlock","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "Unlock");
$arrProvider [] = array("Filed_Label"=> "Lock/Unlock","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Lock");


$arrProvider [] = array("Filed_Label"=> "Enable_Scheduler","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "");
$arrProvider [] = array("Filed_Label"=> "Enable_Scheduler","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Enable Scheduler");

$arrDEMOGRAPHICS = array();
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "reportExemption","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "noBalBill","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "pat_hs","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "emr","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "vip","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "vip","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "Unchecked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "h_statement","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "h_statement","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "Unchecked");

$arrDEMOGRAPHICS [] = array("Filed_Label"=> "pf_contact","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Mobile");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "pf_contact","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Work Phone");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "pf_contact","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "Home Phone");

$arrDEMOGRAPHICS [] = array("Filed_Label"=> "view_portal","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "view_portal","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "Unchecked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "update_portal","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "update_portal","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "Unchecked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "lockPatient","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Lock");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "lockPatient","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "Unlock");


$arrDEMOGRAPHICS [] = array("Filed_Label"=> "chk_mobile", "Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Selected");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "chk_mobile", "Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "De-Selected");

$arrDEMOGRAPHICS [] = array("Filed_Label"=> "hipaa_mail", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "Checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "hipaa_mail", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "Unchecked");

$arrDEMOGRAPHICS [] = array("Filed_Label"=> "hipaa_email", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "hipaa_email", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "UnChecked");

$arrDEMOGRAPHICS [] = array("Filed_Label"=> "hipaa_voice", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "hipaa_voice", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "Unchecked");

$arrDEMOGRAPHICS [] = array("Filed_Label"=> "chkNotesScheduler", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "chkNotesScheduler", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "Unchecked");

$arrDEMOGRAPHICS [] = array("Filed_Label"=> "chkNotesChartNotes", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "chkNotesChartNotes", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "Unchecked");

$arrDEMOGRAPHICS [] = array("Filed_Label"=> "chkNotesAccounting", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "chkNotesAccounting", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "Unchecked");

$arrDEMOGRAPHICS [] = array("Filed_Label"=> "chkHippaFamilyInformation","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "chkMobileTableFamilyInformation","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "chkMobileTableFamilyInformation","Filed_Label_Val"=> '0',"Filed_Label_Og_Val"=> "Unchecked");

$arrDEMOGRAPHICS [] = array("Filed_Label"=> "cbk_self_pay_provider", "Filed_Label_Val"=> '1', "Filed_Label_Og_Val"=> "checked");
$arrDEMOGRAPHICS [] = array("Filed_Label"=> "cbk_self_pay_provider", "Filed_Label_Val"=> '0', "Filed_Label_Og_Val"=> "UnChecked");


$arrMEDHXSx = array();
$arrMEDHXSx [] = array("Filed_Label"=> "sx_site","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "OS");
$arrMEDHXSx [] = array("Filed_Label"=> "sx_site","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "OD");
$arrMEDHXSx [] = array("Filed_Label"=> "sx_site","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "OU");


$vquery_t = "select * from facility";
$vsql_t = imw_query($vquery_t);
$se="";
while($rs_t = imw_fetch_array($vsql_t)){
	$arrProvider [] = array("Filed_Label"=> "default_facility","Filed_Label_Val"=> $rs_t["id"],"Filed_Label_Og_Val"=> $rs_t['name']);
}
$vquery_t = "select * from groups_new";
$vsql_t = imw_query($vquery_t);
$se="";
while($rs_t = imw_fetch_array($vsql_t)){			
	$arrProvider [] = array("Filed_Label"=> "default_group","Filed_Label_Val"=> $rs_t["gro_id"],"Filed_Label_Og_Val"=> $rs_t['name']);	
}

$vquery_t = "select * from user_type";
$vsql_t = imw_query($vquery_t);
$se="";
while($rs_t = imw_fetch_array($vsql_t)){			
	$arrProvider [] = array("Filed_Label"=> "pro_type","Filed_Label_Val"=> $rs_t["user_type_id"],"Filed_Label_Og_Val"=> $rs_t['user_type_name']);	
}

$vquery_t = "select * from  refferphysician";
$vsql_t = imw_query($vquery_t);
$se="";
while($rs_t = imw_fetch_array($vsql_t)){			
	$arrProvider [] = array("Filed_Label"=> "pCarePhy","Filed_Label_Val"=> $rs_t["physician_Reffer_id"],"Filed_Label_Og_Val"=> $rs_t['LastName'].', '.$rs_t['FirstName']);	
}

$vquery_u = "select * from user_groups";
$vsql_u = imw_query($vquery_u);
while($rs_u = imw_fetch_array($vsql_u)){			
	$arrProvider [] = array("Filed_Label"=> "pro_group","Filed_Label_Val"=> $rs_u["id"],"Filed_Label_Og_Val"=> $rs_u['name']);	
}

$arrPatientTables = array(	
							"patient_family_info"=>array(0=>"patient_id",1=>"id"),
							"patient_data"=>array(0=>"id",1=>"id"),
							"resp_party"=>array(0=>"patient_id",1=>"id"),
							"employer_data"=>array(0=>"pid",1=>"id"),
							"insurance_case"=>array(0=>"patient_id",1=>"ins_caseid"),
							"insurance_data"=>array(0=>"pid",1=>"id"),
							"patient_reff"=>array(0=>"patient_id",1=>"reff_id"),
							"ocular"=>array(0=>"patient_id",1=>"ocular_id"),
							"general_medicine"=>array(0=>"patient_id",1=>"general_id"),
							"lists"=>array(0=>"pid",1=>"id"),
							"social_history"=>array(0=>"patient_id",1=>"social_id"),
							"consent_form_signature"=>array(0=>"patient_id",1=>"consent_form_signature_id"),
							"surgery_consent_form_signature"=>array(0=>"patient_id",1=>"consent_form_signature_id"),
							"patient_custom_field"=>array(0=>"patient_id",1=>"patient_id"),							
							"restricted_providers"=>array(0=>"patient_id",1=>"restrict_id"),
							"document_patient_rel"=>array(0=>"p_id",1=>"id"),
							"users"=>array(0=>"id",1=>"id"),
							"immunizations"=>array(0=>"patient_id",1=>"id"),
							"chart_master_table"=>array(0=>"patient_id",1=>"id")
					);

$wherePatient='';
if($ptid<>""){
	$pat_whr = "";
	$pat_whr_join = "";
	if(count($arrPatientTables) > 0){
		
		// Push patient data into Temporary table
		$tmpQry = array();
		foreach($arrPatientTables as $thisTable => $thisColumn){
			$tmpQry[] = "SELECT ".$thisColumn[1].", '".$thisTable."' FROM ".$thisTable." WHERE ".$thisColumn[0]." = '".$ptid."' ";
		}
		$tmpQryStr = implode(" UNION ",$tmpQry);
		
		$tmpTable = "IMWTEMP_tmpAuditTrailId_".$ptid."_".strtotime(date('Y-m-d')); 
		$tmpQryDrp = "DROP TEMPORARY TABLE IF EXISTS ".$tmpTable.";"; imw_query($tmpQryDrp) or die(imw_error());;
		$tmpQryCrt = "CREATE TEMPORARY TABLE ".$tmpTable." ( id INT PRIMARY KEY AUTO_INCREMENT, recordId INT, tablename VARCHAR(500) );"; imw_query($tmpQryCrt) or die(imw_error());
		$tmpQryIns = "Insert Into ".$tmpTable." (recordId,tablename) ".$tmpQryStr;
		//echo $tmpQryIns.'<br><br>';
		imw_query($tmpQryIns) or die(imw_error());
		//$tmpQryIndx = "Create Index ".$tmpTable."INDX ON ".$tmpTable." (recordId);"; imw_query($tmpQryIndx) ;
		/*$pat_whr .= "  ( ";
		foreach($arrPatientTables as $thisTable => $thisColumn){
			$pat_whr .= " ((at.Pk_Id IN (SELECT ".$thisColumn[1]." FROM ".$thisTable." WHERE ".$thisColumn[0]." = '".$ptid."')) AND (at.Table_Name = '".$thisTable."')) OR ";
		}
		$pat_whr = substr($pat_whr,0,-3);
		$pat_whr .= " ) and";*/
		
		$pat_whr_join = " Inner Join ".$tmpTable." TM on (TM.recordId = at.Pk_Id And at.Table_Name = TM.tablename) ";
	}
	$wherePatient=" at.pid IN(".$ptid.") AND";
}

function getSigImage($ImageData,$fieldLabel,$PkId,$id){
	$fieldLabel = str_replace(" ","_",$fieldLabel);
	$fieldLabel = str_replace("<br>","_",$fieldLabel);
	if($ImageData != '000000000000000000000000000000000000000000000000000000000000000000000000'){
		if(!file_exists(realpath(dirname(__FILE__).'/sig_images').'/'.$id.$fieldLabel.'.jpg')){
			if(class_exists("COM") && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
			$aConn = new COM("SIGPLUS.SigPlusCtrl.1");
			$aConn->InitSigPlus();
			$aConn->SigCompressionMode = 2;
			$aConn->SigString=$ImageData;
			$aConn->ImageFileFormat = 4; //4=jpg, 0=bmp, 6=tif
			$aConn->ImageXSize = 500; //width of resuting image in pixels
			$aConn->ImageYSize =165; //height of resulting image in pixels
			$aConn->ImagePenWidth = 11; //thickness of ink in pixels
			$aConn->JustifyMode = 5;  //center and fit signature to size			
			$path =  realpath(dirname(__FILE__).'/sig_images').'/'.$id.$fieldLabel.'.jpg';
			$patientSignArr[] = $path;
			$aConn->WriteImageFile("$path");
			$image = '<span style="text-align: left" ><img name="sig_images/'.$id.$fieldLabel.'.jpg" id="sig_images/'.$id.$fieldLabel.'.jpg" src="sig_images/'.$id.$fieldLabel.'.jpg" width="150" height="83"></span>';
			return $image;
			}
		}
		else{
			$image = '<span style="text-align: left" ><img name="sig_images/'.$id.$fieldLabel.'.jpg" id="sig_images/'.$id.$fieldLabel.'.jpg" src="sig_images/'.$id.$fieldLabel.'.jpg" width="150" height="83"></span>';
			return $image;
		}	
	}	
}
$html_created = 0;
?> 
<!DOCTYPE html>
<html>
<head>
<link href="/imedicwarer8-dev/library/css/reports_html.css" rel="stylesheet">
<style type="text/css">
.text_9{ font-family:"verdana"; font-size:9px; color:#333333;}
.text_b_w {	font-family: "Verdana";	text-decoration: none;	color: #FFFFFF;	font-size: 10px; font-weight:bold; }
</style>
<script type="text/javascript">
function y2k(number){
	return (number < 1000)? number+1900 : number;
}
var today = new Date();
var day = today.getDate();
var month = today.getMonth()
var year = y2k(today.getYear());
	
function newWindow(q){
	mywindow=open('../common/mycal.php?md='+q,'rajan','width=200,height=250,top=200,left=300');
	mywindow.location.href = '../common/mycal.php?md='+q;
	if(mywindow.opener == null)
	mywindow.opener = self;
}
function restart(fieldName){
	document.getElementById(fieldName).value=''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year ;
	mywindow.close();
}
function padout(number){
	return (number < 10) ? '0' + number : number;
}

function img_display(val){
	document.getElementById("loading_img").style.display = val;
}
function sch_report_form_submit(){
	var returnVal = validDateCheck("dat_frm","dat_to");
	var message="";
	if(document.getElementById("dat_frm").value == "") {
		message += "Start date sould not be blank. \n";
	}
	if(document.getElementById("dat_to").value == "") {
		message += "End date should not be blank. \n";
	}
	if(returnVal == true){
		message += "\n Start date Should be less than End date. \n";	
		document.getElementById("dat_frm").select();
	}
	if(message != "") {
		alert(message);
		return false;
	}
	img_display('block');
	document.sch_report_form.submit();
}

function printWindow(){
	var parWidth = parent.document.body.clientWidth;
	var dispSetting="toolbar=no,location=no,directories=yes,menubar=no,"; 
    dispSetting+="scrollbars=yes,width="+parWidth+", height=600, left=60, top=25"; 
  	var auditData = document.getElementById("auditData").innerHTML; 
  	//alert(auditData);
 	var docprint=window.open("","",dispSetting); 
	docprint.document.open(); 
	docprint.document.write('<html><head><title>Audit Report</title>'); 
	docprint.document.write('</head><body bgcolor="#FFFFFF"  onLoad="self.print()"><center>');          
	docprint.document.write(auditData);          
	docprint.document.write('</center></body></html>'); 
	docprint.document.close(); 
	docprint.focus(); 
}
window.onbeforeprint = function(){
	document.getElementById("print").style.display="none";
}
window.onafterprint = function(){
	document.getElementById("print").style.display="block";
}
function export_audit_csv(arrData){
	//alert(arrData);
	document.getElementById('csvString').value = arrData;
	document.getElementById('exportAuditCSV').submit();
}
function set_order(nam,val){
	img_display('none');
	document.getElementById('order_by').value="";
	if(val==""){
		document.getElementById('order_by').value='asc-'+nam;
	}else{
		var order_by_arr=val.split('-');
		var order_by=order_by_arr[0];
		if(order_by=='asc' && order_by_arr[1]==nam){
			document.getElementById('order_by').value='desc-'+nam;
		}else{
			document.getElementById('order_by').value='asc-'+nam;
		}
	}	
	img_display('block');
	document.sch_report_form.submit();
}
</script>
</head>
<body class="body_c" style="margin-top:0; margin-left:0;">
<div class="W100per" id="loading_img" style="display:none; top:220px; left:470px; z-index:1000; position:absolute; margin:0 auto;"><img src="../../images/loading_image.gif"></div>
		<div style="width:100%;overflow-x:hidden; overflow-y:scroll;" id="auditData">
        <script type="text/javascript">
			window.onafterprint = function(){
				window.close();
			}
        </script>
       
		<?php
		if($_REQUEST['doSearch']=='yes') {
			$_REQUEST['doSearch']='no';
		?>
		<table class="rpt_table rpt rpt_table-bordered rpt_padding" id="tblaudit">
		<tr>
			<td class="rptbx1" style="width:33%">Audit Report</td>
			<td class="rptbx2" style="width:33%">Report Period : <?php echo $Start_date; ?> to <?php echo  $End_date; ?></td>
			<td class="rptbx3" style="width:33%">
				Created By: <?php echo $createdBy; ?> on <?php echo  $curDate; ?>
			</td>
		</tr>
		</table>
		<table class="rpt_table rpt rpt_table-bordered">
			<tr style="height:20px;">
				<td style="width:30px;" class="text_b_w">&nbsp;S.No.</td> 
                <td style="width:104px;" class="text_b_w">
					Pt Name &nbsp;
						<?php 
						if($_REQUEST['order_by']=='desc-PatName'){ ?>
							<img src="../chart_notes/images/arr_dn1.gif" border="0">
						<?php }else if($_REQUEST['order_by']=='asc-patName'){ ?>
							<img src="../chart_notes/images/arr_up1.gif" border="0">
						<?php } ?>
					</a>
				</td>
                <!--Patient Name-->
                <!--Patient ID-->
                <td style="width:80px;" class='text_b_w alignLeft'>
					<a href="javascript:void(0);" onClick="set_order('pid','<?php echo $_REQUEST['order_by']; ?>');">
						Pt Id &nbsp;
						<?php 
						if($_REQUEST['order_by']=='desc-pid'){ ?>
							<img src="../chart_notes/images/arr_dn1.gif" class="noborder">
						<?php }else if($_REQUEST['order_by']=='asc-pid'){ ?>
							<img src="../chart_notes/images/arr_up1.gif" class="noborder">
						<?php } ?>
					</a>
				</td>
                <!--Patient ID-->
                <!--Module-->
				<td style="width:100px;" class='text_b_w alignLeft'>
					<a href="javascript:void(0);" onClick="set_order('Category','<?php echo $_REQUEST['order_by']; ?>');">
						Module &nbsp;
						<?php 
						if($_REQUEST['order_by']=='desc-Category'){ ?>
							<img src="../chart_notes/images/arr_dn1.gif" class="noborder">
						<?php }else if($_REQUEST['order_by']=='asc-Category'){ ?>
							<img src="../chart_notes/images/arr_up1.gif" class="noborder">
						<?php } ?>
					</a>
				</td>
				<!--Module-->
                <!--Element-->
				<td style="width:100px;" class='text_b_w alignLeft'>
					<a href="javascript:void(0);" onClick="set_order('Field_Label','<?php echo $_REQUEST['order_by']; ?>');">
						Element &nbsp;
						<?php 
						if($_REQUEST['order_by']=='desc-Field_Label'){ ?>
							<img src="../chart_notes/images/arr_dn1.gif" class="noborder">
						<?php }else if($_REQUEST['order_by']=='asc-Field_Label'){ ?>
							<img src="../chart_notes/images/arr_up1.gif" class="noborder">
						<?php } ?>
					</a>
				</td>
                <!--Element-->
                <!--Original Value-->
				<td style="width:100px;" class='text_b_w alignLeft'>
					<a href="javascript:void(0);" onClick="set_order('Old_Value','<?php echo $_REQUEST['order_by']; ?>');">
						Original Value &nbsp;
						<?php 
						if($_REQUEST['order_by']=='desc-Old_Value'){ ?>
							<img src="../chart_notes/images/arr_dn1.gif" class="noborder">
						<?php }else if($_REQUEST['order_by']=='asc-Old_Value'){ ?>
							<img src="../chart_notes/images/arr_up1.gif" class="noborder">
						<?php } ?>
					</a>
				</td>
                <!--Original Value-->
                <!--New Value-->
				<td style="width:94px;" class='text_b_w alignLeft'>
					<a href="javascript:void(0);" onClick="set_order('New_Value','<?php echo $_REQUEST['order_by']; ?>');">
						New Value &nbsp;
						<?php 
						if($_REQUEST['order_by']=='desc-New_Value'){ ?>
							<img src="../chart_notes/images/arr_dn1.gif" class="noborder">
						<?php }else if($_REQUEST['order_by']=='asc-New_Value'){ ?>
							<img src="../chart_notes/images/arr_up1.gif" class="noborder">
						<?php } ?>
					</a>
				</td>
                <!--New Value-->
                <!--Operation-->
				<td style="width:80px;" class='text_b_w alignLeft'>
					<a href="javascript:void(0);" onClick="set_order('Action','<?php echo $_REQUEST['order_by']; ?>');">
						Operation &nbsp;
						<?php 
						if($_REQUEST['order_by']=='desc-Action'){ ?>
							<img src="../chart_notes/images/arr_dn1.gif" class="noborder">
						<?php }else if($_REQUEST['order_by']=='asc-Action'){ ?>
							<img src="../chart_notes/images/arr_up1.gif" class="noborder">
						<?php } ?>
					</a>
				</td>
                <!--Operation-->
                <!--Result-->
				<td style="width:68px;" class='text_b_w alignLeft'>
					<a href="javascript:void(0);" onClick="set_order('Query_Success','<?php echo $_REQUEST['order_by']; ?>');">
						Result &nbsp;
						<?php 
						if($_REQUEST['order_by']=='desc-Query_Success'){ ?>
							<img src="../chart_notes/images/arr_dn1.gif" class="noborder">
						<?php }else if($_REQUEST['order_by']=='asc-Query_Success'){ ?>
							<img src="../chart_notes/images/arr_up1.gif" class="noborder">
						<?php } ?>
					</a>
				</td>
                <!--Result-->
                <!--Modified By-->
                <td style="width:50px;" class='text_b_w alignLeft'>
					Mod by&nbsp;
				</td>
                <!--Modified By-->
                <!--Date/Time-->
				<td style="width:139px;" class='text_b_w alignLeft'>
					<a href="javascript:void(0);" onClick="set_order('Date_Time','<?php echo $_REQUEST['order_by']; ?>');">
						Date/Time &nbsp;
						<?php 
						if($_REQUEST['order_by']=='desc-Date_Time'){ ?>
							<img src="../chart_notes/images/arr_dn1.gif" class="noborder">
						<?php }else if($_REQUEST['order_by']=='asc-Date_Time'){ ?>
							<img src="../chart_notes/images/arr_up1.gif" class="noborder">
						<?php } ?>
					</a>
				</td>
                <!--Date/Time-->
                <!--Machine Info-->
				<td style="width:110px;" class='text_b_w alignLeft'>
					Machine Info
				</td>
			</tr>
				<?php
					
					$sn=0;
					$tab_nam='audit_trail';
					if($errflag == false){
						$order_by=explode('-',$_REQUEST['order_by']);
						if($order_by[0]){
							if($order_by[1]=='Field_Label'){
								$ord_by= " order by concat(at.$order_by[1],at.Filed_Text,'N/A') ".$order_by[0];
							}else{
								$ord_by= " order by at.".$order_by[1].' '.$order_by[0];
							}	
						}
						$sel_tab_qry1="select at.id,at.Pk_Id,at.Data_Base_Field_Name,at.Operater_Id,at.Category,
							at.Action,at.MAC_Address,
							at.Category_Desc,at.Field_Label,at.Old_Value,at.New_Value,at.Query_Success,at.pid,
							at.Machine_Name,at.Browser_Type,at.Depend_Select,at.Depend_Table,at.Depend_Search,
							at.Filed_Text,ur.lname,ur.fname,
							DATE_FORMAT(at.Date_Time, '".get_sql_date_format()." %h:%i %p') as Date_Time,at.Date_Time atdt
							from $tab_nam as at
							". $pat_whr_join."
							LEFT JOIN users ur ON ur.id = at.Operater_Id ";
								
						if($opr_whr || $act_whr || $pat_whr || $mod_whr || $mod_sub_whr || $dateRange || $wherePatient){
							$sel_tab_qry1 .= "where 
												$opr_whr $act_whr $pat_whr 
												$mod_whr $mod_sub_whr $dateRange $wherePatient
											 ";	
							$sel_tab_qry1 = substr(trim($sel_tab_qry1), 0, -3);				 
						}
						if($ord_by){
							$sel_tab_qry1 .= " $ord_by ";	
						}
						else{
							$sel_tab_qry1 .= " ORDER BY atdt DESC";
						}
						$sel_tab_qry1 .= " LIMIT 10000";
						
						//COPAY TYPE
						$arrCopayType=array();
						$arrCopayType[0]='Practice';
						$arrCopayType[1]='Dilated/Un-Dilated';
						$arrCopayType[2]='Office/Test';
						$run_tab1=imw_query($sel_tab_qry1);
						if(imw_num_rows($run_tab1)>0){
						$kkk = 0;
						$arrAuditdata = array();
						$arrAuditdata[] = array("sn" => " S.No.","patName" => "Pt Name","patId" => "Pt Id","module" => "Module","element" => "Element","oldvalue" => "Original Value","newvalue" => "New Value","action" => "Operation","result" => "Result","Modified_by" => "Modified By","Modified" => "Date/Time","machineInfo" => "Machine Info");		
							while($row_tab1=imw_fetch_array($run_tab1)){
								$id=$row_tab1['id'];
								$Pk_Id=$row_tab1['Pk_Id'];
								$Data_Base_Field_Name=$row_tab1['Data_Base_Field_Name'];
								$Category_Desc=$row_tab1['Category_Desc'];
								$MAC_Address=$row_tab1['MAC_Address'];
								$Machine_Name=$row_tab1['Machine_Name'];
								$Browser_Type=$row_tab1['Browser_Type'];
								$Operater_Id=$row_tab1['Operater_Id'];
								$operater_name=substr($row_tab1['fname'],0,1).substr($row_tab1['lname'],0,1);
								$Category=$row_tab1['Category'];
								$Action=$row_tab1['Action'];
								$Date_Time=$row_tab1['Date_Time'];
								$Field_Label=$row_tab1['Field_Label'];
								$Old_Value=$row_tab1['Old_Value'];
								$New_Value=$row_tab1['New_Value'];
								$Depend_Select=$row_tab1['Depend_Select'];
								$Depend_Table=$row_tab1['Depend_Table'];
								$Depend_Search=$row_tab1['Depend_Search'];
								$Filed_Text =$row_tab1['Filed_Text'];
								$pid =$row_tab1['pid'];
								
								if($row_tab1['Query_Success']=='0'){
									$Query_Success="Success";
								}else{
									$Query_Success="Fail";
								}
								
								//--
								if($Data_Base_Field_Name=='deleted_by' || $Data_Base_Field_Name=='purged_by'){
									if(!empty($Old_Value)){
										$tmp = getUserFirstName($Old_Value,3);
										if(!empty($tmp)){
											$Old_Value = $tmp; //." - ".$Old_Value;
										}
									}else{ $Old_Value=""; }
									
									if(!empty($New_Value)){
										$tmp = getUserFirstName($New_Value,3);
										if(!empty($tmp)){
											$New_Value = $tmp; //." - ".$New_Value;
										}
									}else{ $New_Value=""; }
								}
								
								if($Data_Base_Field_Name=='deleted_on' || $Data_Base_Field_Name=='purged_on'){
									if(!empty($Old_Value)){
										$Old_Value = (strpos($Old_Value, "0000-00-00") === false) ? date ( $phpDateFormat." h:i A", strtotime($Old_Value) ) : "";
									}
									
									if(!empty($New_Value)){
										$New_Value = (strpos($New_Value, "0000-00-00") === false) ? date ( $phpDateFormat." h:i A", strtotime($New_Value) ) : "";
									}
								}
								
								if($Data_Base_Field_Name=='delete_status'){
									
									$Old_Value = !empty($Old_Value) ? "Deleted" : "Not Deleted";
									$New_Value = !empty($New_Value) ? "Deleted" : "Not Deleted";
									
								}
								
								if( $Data_Base_Field_Name=='purge_status'){
									
									$Old_Value = !empty($Old_Value) ? "Purged" : "Not Purged";
									$New_Value = !empty($New_Value) ? "Purged" : "Not Purged";
									
								}
								
								//--

								if($Data_Base_Field_Name=='copay_type'){
									$Old_Value=$arrCopayType[$Old_Value];
									$New_Value=$arrCopayType[$New_Value];
								}
								if($Data_Base_Field_Name=='self_pay_provider'){
									$Old_Value=($Old_Value=='' || $Old_Value=='0') ? 'No' : 'Yes';
									$New_Value=($New_Value=='' || $New_Value=='0') ? 'No' : 'Yes';
								}
								if($Field_Label=='cbkMasterROS'){
									$Old_Value=($Old_Value=='' || $Old_Value=='0') ? 'No' : 'Yes';
									$New_Value=($New_Value=='' || $New_Value=='0') ? 'No' : 'Yes';
								}
								
								if($Depend_Select && $Depend_Table && $Depend_Search){
									$getOrignalValOLD = $Depend_Select." from ".$Depend_Table." where ".$Depend_Search." in (".$Old_Value.")";
									$rsGetOrignalValOLD = imw_query($getOrignalValOLD);
									if($rsGetOrignalValOLD){
										if(imw_num_rows($rsGetOrignalValOLD)){												
											$arrOldVal = array();
											while($row = imw_fetch_array($rsGetOrignalValOLD)){
												$arrOldVal[] = $row[0];
											}
											$Old_Value = implode("<br>",$arrOldVal);											
										}
									}
									$getOrignalValNEW = $Depend_Select." from ".$Depend_Table." where ".$Depend_Search." in (".$New_Value.")";									
									$rsGetOrignalValNEW = imw_query($getOrignalValNEW);
									if($rsGetOrignalValNEW){
										if(imw_num_rows($rsGetOrignalValNEW)){										
											$arrNewVal = array();	
											while($row = imw_fetch_array($rsGetOrignalValNEW)){												
												$arrNewVal[] = $row[0];																							
											}
											//print_r($arrNewVal);die;
											$New_Value = implode("<br>",$arrNewVal);
										}
									}					
								}

								$arrFLD = explode("_",$Field_Label);
								$orignalFieldLabel = $Field_Label;
								//Medical History
								switch ($arrFLD[0]):
									case "md":						
										//Medical History -> Medication		
										$noNumeric = preg_replace('#\d+#', '', $arrFLD[1]);
										$Field_Label = $arrFLD[0].'_'.$noNumeric;
									break;	
									case "ag":								
										$noNumeric = preg_replace('#\d+#', '', $arrFLD[2]);							
										$Field_Label = $arrFLD[0].'_'.$arrFLD[1].'_'.$noNumeric;
									break;	
									case "sg":								
										//Medical History -> Surgery
										$noNumeric = preg_replace('#\d+#', '', $arrFLD[1]);
										$Field_Label = $arrFLD[0].'_'.$noNumeric;
									break;
									case "imchk":	//Medical History -> Immunizations										
										$noNumeric = preg_replace('#\d+#', '', $arrFLD[2]);
										$Field_Label = $arrFLD[0].'_'.$arrFLD[1].'_'.$noNumeric;
									break;									
									case "chkHippaFamilyInformation":	//Demographic -> family Info (Release HIPAA Info Y/N)																			
										$Field_Label = $arrFLD[0];
									break;
									case "chkMobileTableFamilyInformation":	//Demographic -> family Info (Mobile)																			
										$Field_Label = $arrFLD[0];
									break;
									
									default:			
										$Field_Label = $orignalFieldLabel;										
								endswitch;

								switch ($Field_Label):
									
									case "u_wear":
										//Medical History-> Oculer																				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXOculer,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
									break;
									case "eye_problem":										
										//Medical History-> Oculer
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXOculer,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
									break;	
									case "any_conditions_other_u":										
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXOculer,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];																	
									break;
									case "any_conditions_u":										
										//Medical History-> Oculer
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXOculer,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];																		
									break;		
									case "any_conditions_relative":										
										//Medical History-> Oculer
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXOculer,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];																		
									break;
														
									case "chk_annual_colorectal_cancer_screenings":					
										//Medical History-> Oculer				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];			
									break;
									case "chk_receiving_annual_mammogram":					
										//Medical History-> Oculer				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];																		
									break;
									case "chk_received_flu_vaccine":					
										//Medical History-> Oculer				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];																		
									break;
									
									case "chk_high_risk_for_cardiac":					
										//Medical History-> Oculer				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];																		
									break;					

									case "con_for_nut":
									case "con_for_phy":					
										//Medical History-> Oculer				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];																		
									break;					
																	
									case "elem_chronicDesc":												
										$orignalValue = getOrignalValWt2Sep($Old_Value,$New_Value,$arrMEDHXOculer,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];															
									break;
									case "elem_chronicRelative":																				
										$orignalValue = getOrignalValWt2Sep($Old_Value,$New_Value,$arrMEDHXOculer,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];										
									break;
									case "rel_elem_chronicDesc":												
										$orignalValue = getOrignalValWt2Sep($Old_Value,$New_Value,$arrMEDHXOculer,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];														
									break;										
									case "this_blood_sugar_fasting":												
										$orignalValue = getOrignalValWt2Sep($Old_Value,$New_Value,$arrMEDHXOculer,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];														
									break;	
									case "any_conditions_u1":
									case "any_conditions_u1_n":
										//Medical History-> Gerneal Health
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];
									break;
									case "any_conditions_relative1":
									case "any_conditions_relative1_n":
										//Medical History-> Gerneal Health																				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];
									break;
									case "any_conditions_others_rel":
										//Medical History-> Gerneal Health																				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];
									break;
									case "any_conditions_others_both":
										//Medical History-> Gerneal Health																				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];
									break;
									
									
									case "any_conditions_others_both":				
										//Medical History-> Gerneal Health																										
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];
									break;
									
									case "review_eye":
									case "review_endocrine":
									case "review_musculoskeletal":
									case "review_blood_lymph":
									case "review_psychiatry":
									case "review_intgmntr":
										//Medical History-> Gerneal Health
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];
									break;
									
									case "review_const":		
										//Medical History-> Gerneal Health
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];
									break;
									case "review_head":			
										//Medical History-> Gerneal Health																										
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];
									break;
									case "review_resp":				
										//Medical History-> Gerneal Health																
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];
									break;
									case "review_card":	
										//Medical History-> Gerneal Health																													
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];
									break;
									case "review_gastro":	
										//Medical History-> Gerneal Health																													
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];
									break;
									case "review_genit":	
										//Medical History-> Gerneal Health
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];										
									break;
									case "negChkBx":	
										//Medical History-> Gerneal Health
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];										
									break;
									case "smoke":	
										//Medical History-> Social
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXSocial,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];									
									break;	
									case "review_aller":	
										//Medical History-> Gerneal Health								
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
									break;									
									case "review_neuro":	
										//Medical History-> Gerneal Health										
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
									break;	
									
									case "rel_any_conditions_relative":										
										//Medical History-> Gerneal Health										
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXOculer,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];																	
									break;									
									case "med_type":	
										//Medical History-> Medication																				
										//echo 'hlo'.$New_Value;
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "compliant":	
										//Medical History-> Medication																				
										//echo 'hlo'.$New_Value;
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "sg_occular":	
										//Medical History-> Surgery																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;	
									case "ag_occular_drug":																				
										//Medical History-> Allergies																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "sx_site":																				
										//Medical History-> Sx Site																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXSx,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "Lock/Unlock":												
										//Privider
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrProvider,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;	
									case "pro_type":	
										//Privider	
										$vv=explode('-', $New_Value);																		
										$New_Value=$vv[0];
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrProvider,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "pCarePhy":	
										//Privider																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrProvider,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "pro_pass_new":	
										//Privider																			
										//$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrProvider,$Field_Label);										
										//$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = '**********';
										$New_Value = '**********';	
										$Field_Label = "Password";
									break;
									case "access_pri_audit":	
										//Privider				
										$new_value_setting=array();
										$old_value_setting=array();
										$change_priv_label = array(
											"priv_cl_work_view" => "Clinical-Work View",
											"priv_cl_tests" => "Clinical-Tests",
											"priv_cl_medical_hx" => "Clinical-Medical Hx",
											
											"priv_Front_Desk" => "Front Desk",
											"priv_Billing" => "Billing",
											"priv_Accounting" => "Accounting",
											"priv_Security" => "Security",
											
											"priv_sc_scheduler" => "Reports-Scheduler",
											"priv_sc_house_calls" => "Reports-House Calls",
											"priv_sc_recall_fulfillment" => "Reports-Recall Fulfillment",
											"priv_bi_front_desk" => "Reports-Front Desk",
											"priv_bi_ledger" => "Reports-Ledger",
											"priv_bi_prod_payroll" => "Reports-Productivity & Payroll",
											"priv_bi_ar" => "Reports-A/R",
											"priv_bi_statements" => "Reports-Statements",
											"priv_bi_end_of_day" => "Reports-End of Day",
											
											"priv_cl_clinical" => "Reports-Clinical",
											"priv_cl_visits" => "Reports-Visits",
											"priv_cl_ccd" => "Reports-CCD",
											"priv_cl_order_set" => "Reports-Order Set",
											
											"priv_vo_clinical" => "View-Clinical",
											"priv_vo_pt_info" => "View-Patient Info",
											"priv_vo_acc" => "View-Accounting",
											
											"priv_Sch_Override" => "Sch. Override",
											"priv_pt_Override" => "Pt. Override",
											"priv_admin" => "Admin",
											"priv_Optical" => "Optical",
											"priv_iOLink" => "iOLink",
											"priv_break_glass" => "Break Glass"
										);

										$old_orignalValue = unserialize(html_entity_decode(urldecode($Old_Value)));		
										$new_orignalValue = unserialize(html_entity_decode(urldecode($New_Value)));
										if($new_orignalValue){
											foreach($new_orignalValue as $key => $val){
												if($val==1){
													$new_value_setting[]='<b>'.$change_priv_label[$key].'</b>=Yes';
												}else{
													$new_value_setting[]='<b>'.$change_priv_label[$key].'</b>=No';
												}
											}
											$new_value_setting_imp=@implode(',<br>',$new_value_setting);
										}	
										
										if($old_orignalValue){
											foreach($old_orignalValue as $key => $val){
												if($val==1){
													$old_value_setting[]='<b>'.$change_priv_label[$key].'</b>=Yes';
												}else{
													$old_value_setting[]='<b>'.$change_priv_label[$key].'</b>=No';
												}
											}
											$old_value_setting_imp=@implode(',<br>',$old_value_setting);
										}
										
										$Old_Value = $old_value_setting_imp;
										$New_Value = $new_value_setting_imp;	
										$Field_Label = $orignalFieldLabel;
									break;
									case "Enable_Scheduler":				
										//Privider																
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrProvider,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;		
									case "default_facility":		
										//Privider		
										if($Filed_Text=="Patient Facility"){
											$Old_Value = $Old_Value;
											$New_Value = $New_Value;	
											$Field_Label = $orignalFieldLabel;
										}else{															
											$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrProvider,$Field_Label);										
											$orignalValue = explode("~~~~",$orignalValue);										
											$Old_Value = $orignalValue[0];
											$New_Value = $orignalValue[1];	
											$Field_Label = $orignalFieldLabel;
										}
										
									break;
									case "default_group":	
										//Privider																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrProvider,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;	
									case "pro_group":	
										//Provider group																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrProvider,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "reportExemption":	
										//Demographics																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "noBalBill":	
										//Demographics																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "pat_hs":	
										//Demographics																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "emr":	
										//Demographics																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "vip":	
										//Demographics																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "h_statement":	
										//Demographics																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "pf_contact":	
										//Demographics																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "view_portal":	
										//Demographics																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "update_portal":	
										//Demographics																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "lockPatient":	
										//Demographics																			
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "pass1":	
										//Demographics																																						
										$Old_Value = ($Old_Value) ? "<i>[Not Displayed]</i>" : "";
										$New_Value = ($New_Value) ? "<i>[Not Displayed]</i>" : "";
										$Field_Label = $orignalFieldLabel;
									break;	
									case "pass2":	
										//Demographics																																						
										$Old_Value = ($Old_Value) ? "<i>[Not Displayed]</i>" : "";
										$New_Value = ($New_Value) ? "<i>[Not Displayed]</i>" : "";
										$Field_Label = $orignalFieldLabel;
									break;	
									case "hipaa_mail":	
										//Demographics																				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "hipaa_email":			
										//Demographics																		
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "hipaa_voice":	
										//Demographics																				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;							
									
									case "chkNotesScheduler":
										//Demographics																					
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;							
									case "chkNotesChartNotes":	
										//Demographics																				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;							
									case "chkNotesAccounting":			
										//Demographics																		
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "cbk_self_pay_provider":
										//Demographics Insurance																					
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "chkHippaFamilyInformation":			
										//Demographic -> family Info (Release HIPAA Info Y/N)																		
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
										//print_r( $orignalValue); 
									break;			
									case "chkMobileTableFamilyInformation":			
										//Demographic -> family Info (Mobile)																		
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
										//print_r( $orignalValue); 
									break;																
									case "chk_under_control":			
																	
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];																		
									break;
									case "elem_subCondition_u1":																				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "imchk_child_immunization":																				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
										//print_r( $orignalValue); 
									break;
									case "immunization_child1":																				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
										//print_r( $orignalValue); 
									break;
									case "chk_mobile":
										//Demographics	
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrDEMOGRAPHICS,$Field_Label);
										$orignalValue = explode("~~~~",$orignalValue);
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];
										$Field_Label = $orignalFieldLabel;
									break;		
									case "offered_cessation_counseling":																				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "radio_family_smoke":																				
										$orignalValue = getOrignalValComa($Old_Value,$New_Value,$arrMEDHX,$Field_Label);										
										$orignalValue = explode("~~~~",$orignalValue);										
										$Old_Value = $orignalValue[0];
										$New_Value = $orignalValue[1];	
										$Field_Label = $orignalFieldLabel;
									break;
									case "elem_sign":		
										drawOnImage_new($New_Value,$imgName,$saveImg="4",'img_new_'.$id, $kkk);
										$New_Value="<img src=\"/$web_RootDirectoryName/interface/audit/sig_images/".$GLOBALS['img_new_'.$id]."\" alt=\"alt image\">";
										
										drawOnImage_new($Old_Value,$imgName,$saveImg="5",'img_old_'.$id, $kkk+1);
										$Old_Value="<img src=\"/$web_RootDirectoryName/interface/audit/sig_images/".$GLOBALS['img_old_'.$id]."\" alt=\"alt image\">";
										$Field_Label = $orignalFieldLabel;
										$kkk++;
									break;
									
								endswitch;

								switch (trim($Data_Base_Field_Name)):
									case "signature_content":	
										$orignalValue = getSigImage($New_Value,$Field_Label,$Pk_Id,$id);																				
										$New_Value = $orignalValue;									
									break;
								endswitch;

								switch ($Action):
									case "app_start":
										$Action = "Start Application";
									break;
									case "app_stop":
										$operater_name =(trim($operater_name)!="" && trim($operater_name)!=",") ? "".ucwords($operater_name): "N/A" ;
										$Action = "Stop Application";
									break;	
									case "user_login_s":
										$operater_name =(trim($operater_name)!="" && trim($operater_name)!=",") ? "".ucwords($operater_name): "N/A" ;
										$Action = "Provider Login";
									break;		
									case "user_logout_s":
										$operater_name =(trim($operater_name)!="" && trim($operater_name)!=",") ? "".ucwords($operater_name): "N/A" ;
										$Action = "Provider Logout";
									break;									
									case "user_session_timeout_s":										
										$Action = "Session Timeout";
									break;
									case "user_login_f":
										$getWhoseLock = "select CONCAT_WS(',',fname,lname) as whoseLockName from users where id = '$Pk_Id'";
										$rsGetWhoseLock = imw_query($getWhoseLock);
										if($rsGetWhoseLock){
											if(imw_num_rows($rsGetWhoseLock)){	
												extract(imw_fetch_array($rsGetWhoseLock));												
												$operater_name =(trim($operater_name)!="" && trim($operater_name)!=",") ? "Auth. Failed By :".ucwords($operater_name): "N/A" ;
												$operater_name .= "<br>";
												$operater_name .= (trim($whoseLockName)!="" && trim($whoseLockName)!=",") ? "Auth. Failed To :".ucwords($whoseLockName): "N/A" ;													
											}
										}										
										$Action = "Auth. Failed";
									break;		
									case "query_search":										
										$Action = "Search/Query";
									break;	
									case "sig_create":										
										$Action = "Signature Created";
									break;	
									case "phi_export":				
										$operater_name =(trim($operater_name)!="" && trim($operater_name)!=",") ? "Export By :".ucwords($operater_name): "N/A" ;						
										$Action = "PHI Export";
										$Field_Label = $Old_Value;
										$Old_Value = "";
										$New_Value = "";
									break;															
								endswitch;
								
								//sort
								if(strpos($Field_Label,"review_") !== false || $Field_Label == "negChkBx"){
									$Old_Value = sort_review_values($Old_Value);
									$New_Value = sort_review_values($New_Value);
								}
								
							$sn++;	
							if($sn % 2 == 0) { $bgClr = 'tblBg'; } else{ $bgClr = 'bgcolor';}
					?>
					<tr class="<?php echo $bgClr;?>" height="20">
						<td align="center" width="30px" class='text_9' valign="top"><?php echo $sn; ?></td> 
                        <?php 
							if($pid>0){
								$pat_info=$pid;
								$qry_pat="select fname,lname from patient_data where id ='$pid'";
								$sel_pat=imw_query($qry_pat);
								$row_pat=imw_fetch_array($sel_pat);
								$pat_fname=$row_pat['fname'];
								$pat_lname=$row_pat['lname'];
								if($pat_fname!="" && $pat_lname!=""){
									$pat_comma=", ";
								}
								$pat_info = $pat_lname.$pat_comma.$pat_fname;
								$pat_info_PID = $pid;
							}
							else{
								$pat_info="N/A";
								$pat_info_PID="N/A";
							}					
						?>                        
                        <td class='text_9 valignTop alignLeft' style="width:104px;"><?php echo $pat_info; ?></td>
                        <td style="width:10px;" class='text_9 valignTop alignLeft'><?php echo $pat_info_PID; ?></td>
						<td style="width:100px;" class='text_9 alignLeft valignTop'>
							<?php 
							$Category_Desc = str_replace("_"," ",$Category_Desc);
							$Category_Desc = str_replace("-"," ",$Category_Desc);
							$Category_Desc = str_replace("insurence","Insurance",$Category_Desc);
							
							$Category_Desc = str_replace("printMedicalHistory","Print Medical History",$Category_Desc);
							echo ucwords($Category_Desc);
							$strModule = ucwords($Category_Desc);					
							?>
						</td>
						<td style="width:100px;" class='text_9 alignLeft valignTop'>
							<?php
								$strElement = "";
								if($Filed_Text!=""){
									$strElement = ucwords($Filed_Text);
									echo $strElement;									
								}
								elseif($Field_Label!=""){
									$strElement =  ucwords($Field_Label);
									echo $strElement;
								}
								else{
									$strElement = "N\A"; 
									echo $strElement;
								}
								?>
                        </td>
						<td style="width:100px;" class='text_9 valignTop alignLeft'>
							<?php 
								if($Action != "view"){
									if($Field_Label=="" && $Filed_Text == ""){
										echo "N/A";
									}else if(stristr($Field_Label,"phone_home_table_family_information") || stristr($Field_Label,"phone_work_table_family_information") || stristr($Field_Label,"phone_cell_table_family_information")){
										echo ucwords(isDate(core_phone_format($Old_Value)));
									}else if(trim($Old_Value)=="00-00-0000"){
										echo "";
									}
									else{
										echo ucwords(isDate($Old_Value));
									}
								}
								elseif($Action == "view"){
									echo "Viewed Only";
								}
							?>
                        </td>
						<td style="width:85px;" class='text_9 alignLeft valignTop'>
							<?php 
								if($Action != "view"){
									if($Field_Label=="" && $Action != "Search/Query" && $Filed_Text == ""){
										echo "N/A";
									}
									else if(stristr($Field_Label,"phone_home_table_family_information") || stristr($Field_Label,"phone_work_table_family_information") || stristr($Field_Label,"phone_cell_table_family_information")){
										echo ucwords(isDate(core_phone_format($New_Value)));
									}else if(trim($New_Value)=="00-00-0000"){
										echo "";
									}
									else{
										echo ucwords(isDate($New_Value));
									}
								}
								elseif($Action == "view"){
									echo "Viewed Only";
								}
							?>
                       	</td>
						<td style="width:89px;" class='text_9 alignLeft valignTop'><?php echo ucwords($Action); ?></td>
						<td style="width:68px;" class='text_9 valignTop alignLeft'><?php echo ucwords($Query_Success); ?></td>
                        <td style="width:50px;" class='text_9 valignTop alignCenter'>
							<?php
								echo $operater_name =(trim($operater_name)!="" && trim($operater_name)!=",") ? ucwords($operater_name): "N/A" ;
							?>
						</td>
						<td style="width:139px;" class='text_9 alignLeft nowrap valignTop'>
							<?php 
								echo $Date_Time;
								$strModified = $Date_Time;
								?>
						</td>
						<td style="width:110px;" class='text_9 valignTop alignLeft'>
							<?php echo '<b>Name : </b>'.ucwords($Machine_Name);
								$strMacInfo = "Name : ".ucwords($Machine_Name);
							?>
						</td>
					</tr>
					<?php
					if($Filed_Text=="" && $Field_Label!=""){
						$strFileld = ucwords($Field_Label);
					}
					elseif($Filed_Text!="" && ($Field_Label!="" || $Field_Label=="")){
						$strFileld =  ucwords($Filed_Text);
					}
					else{
						$strFileld =  "N\A"; 
					}
					
					if($Action != "view"){
						if($Field_Label=="" && $Filed_Text == ""){
							$strOldVal = "N/A";
						}
						else{
							$strOldVal = ucwords(isDate($Old_Value));
						}
					}
					
					if($Action != "view"){
						if($Field_Label=="" && $Action != "Search/Query" ){
							$strNewVal = "N/A";
						}
						else{
							$strNewVal = ucwords(isDate($New_Value));
						}
					}
					$arrAuditdata[] = array("sn" => $sn,"patName" => $pat_info,"patId" => $pat_info_PID,"module" => $strModule,"element" => $strElement,"oldvalue" => $strOldVal,"newvalue" => $strNewVal,"action" => ucwords($Action),"result" => ucwords($Query_Success),"Modified_by" => $operater_name,"Modified" => $strModified,"machineInfo" => ucwords($strMacInfo));		
							}
						}
					}
						
					// Drop Temporary Table 
					imw_query($tmpQryDrp);
			
					//}
					$html_created = 1;
					if($sn==0){
					$html_created = 0;	
				?>
					<tr class="tblBg">
						<td class="text_9 text-center" colspan="12"  style="color:#FF0000;"><strong><strong>No Record Found</strong></strong></td>
					</tr>
				<?php } ?>
		  </table>
		<?php 
		
		}
		?>	
		</div>
		<script type="text/javascript">
	  	if(document.getElementById('patientId')){
			document.getElementById('patientId').value='<?php echo $_REQUEST['patientId'];?>';
			document.getElementById('patient').value='<?php echo $_REQUEST['patient'];?>';
		}
	</script>
</body>
</html>