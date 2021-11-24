<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
require_once("../../../config/globals.php");
require_once($GLOBALS['srcdir']."/classes/medical_hx/general_health.class.php");
$health = new GeneralHealth($_REQUEST['curr_tab']);

/** 
 * Parameters Sanitization to prevent arbitrary values - Security Fixes
 **/
$_REQUEST['assigned_nurse'] = xss_rem($_REQUEST['assigned_nurse']);

/** Allow numeric only */
$_REQUEST['patient_id_genHealth'] = (int)xss_rem($_REQUEST['patient_id_genHealth'], 3);
$_REQUEST['ptFormId'] = xss_rem($_REQUEST['ptFormId'], 3);
$_REQUEST['this_blood_sugar_id'] = xss_rem($_REQUEST['this_blood_sugar_id'], 3);
$_REQUEST['this_cholesterol_id'] = xss_rem($_REQUEST['this_cholesterol_id'], 3);

/* Prevent further operations if invalid patient Id supplied - Security fixes */
if( $_REQUEST['patient_id_genHealth'] < 1 )
{
	die('Invalid Patient Id supplied.');
}



/******CHECKING IF SAVING CALLED BEFORE FULL INTERFACE LOADED******/
if($_REQUEST['gen_health_page_load_done']!='yes'){
	die('Patient General Health data not loaded properly. Please click on tab again and wait till page loads competely.');
}
/******************************************************************/
//pre($_REQUEST);die;
$arr_info_alert = array();
if(isset($_REQUEST["info_alert"]) && count($_REQUEST["info_alert"]) > 0){
	$arr_info_alert = unserialize(urldecode($_REQUEST["info_alert"]));
}

$vs_form_id = $_SESSION['form_id'];
if(!isset($_SESSION['form_id'])){
	$vs_form_id = $_SESSION['finalize_id'];
}


$delimiter = '~|~';
$pid = $_REQUEST["patient_id_genHealth"];

//geting from request serialize hid_arr_review_GH and hid_arr_review_AD and hid_arr_review_Social - start
$arrReview_GH = array();
$arrReview_GH = unserialize(urldecode($_REQUEST["hid_arr_review_GH"]));

$arrReview_AD = array();
$arrReview_AD = unserialize(urldecode($_REQUEST["hid_arr_review_AD"]));

$arrReview_Social = array();
$arrReview_Social = unserialize(urldecode($_REQUEST["hid_arr_review_Social"]));
//geting from request serialize hid_arr_review_GH and hid_arr_review_AD and hid_arr_review_Social - end


/*---including save of social smoke status---*/
require_once("inc_social_save.php");
require_once("../family_hx/inc_save_social_relatives.php");

//Advance Directive
$ado_option_text_value = $_REQUEST['ado_other_txt'];
$ado_option_value = $_REQUEST['ado_option'];

//Primary Care Provider
$phyDBID = "";
$phyName = "";
$intRefPhyId = 0;
$strRefPhyName = "";
$arrTemp = explode("; ", $_REQUEST['med_doctor']);
$_REQUEST['med_doctor'] = $arrTemp[0];

$_REQUEST['med_doctor'] = xss_rem($_REQUEST['med_doctor']);	/** Sanitize the parameter to prevent arbitrary values - Security Fixes */

if($_REQUEST['med_doctor'] != "" && (trim($_REQUEST['med_doctor']) != trim($_REQUEST['hidd_med_doctor']))){	
	list($intRefPhyId, $strRefPhyName) = $cls_common->chk_create_ref_phy($_REQUEST['med_doctor'], 6);
	if((empty($intRefPhyId) == false) && (empty($strRefPhyName) == false)){
		$phyDBID = $intRefPhyId;
		$phyName = $strRefPhyName;
		$_REQUEST['med_doctor'] = $phyName;
		$updatePatientData = "update patient_data set primary_care_phy_id = '".$phyDBID."', 
							primary_care_phy_name =  '".imw_real_escape_string(htmlentities($phyName))."', 
							desc_ado_other_txt = '".imw_real_escape_string(htmlentities($ado_option_text_value))."' ,
							ado_option = '".imw_real_escape_string(htmlentities($ado_option_value))."' 
							where id = '".$pid."'";
		$rsUpdatePatientData = imw_query($updatePatientData);
	}
}
else{
	//--- UPDATE ADO OPTIONS ----
	$ado_patient_data_saveqry = "update patient_data set desc_ado_other_txt = '".imw_real_escape_string(htmlentities($ado_option_text_value))."' ,
									ado_option = '".imw_real_escape_string(htmlentities($ado_option_value))."' 
									where id = '$pid' ";
	$ado_saveSql = imw_query($ado_patient_data_saveqry);
}

if( array_key_exists('assigned_nurse', $_REQUEST) && !empty($_REQUEST['assigned_nurse']) )
{
	imw_query("UPDATE patient_data SET assigned_nurse = '".$_REQUEST['assigned_nurse']."' WHERE id = '".$pid."'");
}

if((int)$pid > 0){
	if($intRefPhyId > 0){
		$selPatRefPhyMulti = "select id, status from patient_multi_ref_phy where patient_id = '".$pid."' and status = 0 and phy_type = 3 limit 1";
		$rsPatRefPhyMulti = imw_query($selPatRefPhyMulti);
		if(imw_num_rows($rsPatRefPhyMulti) > 0){
			$rowPatRefPhyMulti = imw_fetch_row($rsPatRefPhyMulti);
			$intPatRefPhyMultiID = $intPatRefPhyMultiStatus = 0;
			$intPatRefPhyMultiID = $rowPatRefPhyMulti[0];
			$intPatRefPhyMultiStatus = $rowPatRefPhyMulti[1];
			//$qryUp = "update patient_multi_ref_phy set status = 0 where id = '".$intPatRefPhyMultiID."' LIMIT 1 ";
			//imw_query($qryUp);
			$qryUp = "update patient_multi_ref_phy set ref_phy_id = '".$intRefPhyId."',ref_phy_name = '".$strRefPhyName."' where id = '".$intPatRefPhyMultiID."' LIMIT 1 ";
			imw_query($qryUp);
		}
		else{
			$qryInsertCoPhy = "insert into patient_multi_ref_phy 
										(patient_id, ref_phy_id, ref_phy_name, phy_type, created_by, created_by_date_time) Values 
										('".$_SESSION['patient']."', '".$intRefPhyId."', '".$strRefPhyName."', '3','".$_SESSION['authId']."', '".date('Y-m-d H:i:s')."')";
			$rsInsertCoPhy = imw_query($qryInsertCoPhy);
		}
	}
}

$med_doctor = $_REQUEST['med_doctor'];

//Comments
$genMedComments = $_REQUEST['genHealthComments'];

//Blood Sugar FROM MAIN FORM
$blood_sugar_primary_key_id = $_REQUEST["this_blood_sugar_id"];
$blood_sugar_date = "";
if(trim($_REQUEST["this_blood_sugar_date"]) != "" && getDateFormatDB(trim($_REQUEST["this_blood_sugar_date"])) != "0000-00-00"){
	$blood_sugar_date = getDateFormatDB(trim($_REQUEST["this_blood_sugar_date"]));
	/*$blood_sugar_date_arr = explode("-",$blood_sugar_date);
	$blood_sugar_date_month = $blood_sugar_date_arr[0];
	$blood_sugar_date_day = $blood_sugar_date_arr[1];
	$blood_sugar_date_year = $blood_sugar_date_arr[2];
	$blood_sugar_date = $blood_sugar_date_year."-".$blood_sugar_date_month."-".$blood_sugar_date_day;*/
}
if($blood_sugar_date == "0000-00-00"){
	$blood_sugar_date = "";
}
$blood_sugar_mg = floatval($_REQUEST["this_blood_sugar"]);
$blood_sugar_hba1c_val = $_REQUEST["this_blood_sugar_hba1c_val"];
$blood_sugar_hba1c = $_REQUEST["this_blood_sugar_hba1c"];
$blood_sugar_fasting = $_REQUEST["this_blood_sugar_fasting"];
$arrBloodSugarTime = explode("-",$_REQUEST["this_blood_sugar_time"]);

$timeOfDaySequence = "";
$timeOfDaySequence = $arrBloodSugarTime[0];
$blood_sugar_time_of_day = $arrBloodSugarTime[1];

$blood_sugar_time_of_day_other = $_REQUEST["this_blood_sugar_other"];
$blood_sugar_description = $_REQUEST["this_blood_sugar_desc"];

if($blood_sugar_primary_key_id != ""){
	$str_query_blood_sugar = "update patient_blood_sugar set ";
}else{
	$str_query_blood_sugar = "insert into patient_blood_sugar set ";
	$str_query_blood_sugar .= " created_on = now(), ";
	$str_query_blood_sugar .= " created_by = '".$_SESSION["authId"]."', ";
	$str_query_blood_sugar .= " patient_id = '".$pid."', ";
}
$str_query_blood_sugar .= "	sugar_value = '".imw_real_escape_string(htmlentities($blood_sugar_mg))."',
							hba1c = '".imw_real_escape_string(htmlentities($blood_sugar_hba1c))."',
							is_fasting = '".imw_real_escape_string(htmlentities($blood_sugar_fasting))."',
							time_of_day = '".imw_real_escape_string(htmlentities($blood_sugar_time_of_day))."',
							time_of_day_other = '".imw_real_escape_string(htmlentities($blood_sugar_time_of_day_other))."',
							time_of_day_sequence = '".imw_real_escape_string(htmlentities($timeOfDaySequence))."',
							description = '".imw_real_escape_string(htmlentities($blood_sugar_description))."',
							hba1c_val = '".imw_real_escape_string(htmlentities($blood_sugar_hba1c_val))."',
							creation_date = '".$blood_sugar_date.date(" H:i:s")."',							
							modified_on = '".date('Y-m-d H:i:s')."',
							modified_by = '".$_SESSION['authId']."' ";
if($blood_sugar_primary_key_id != ""){
	$str_query_blood_sugar .= " where id = '".$blood_sugar_primary_key_id."'";	
}
if($blood_sugar_date != ""){
	imw_query($str_query_blood_sugar);
}

//Cholesterol FROM MAIN FORM
$cholesterol_primary_key_id = $_REQUEST["this_cholesterol_id"];
$cholesterol_date = "";
if(trim($_REQUEST["this_cholesterol_date"]) != "" && trim($_REQUEST["this_cholesterol_date"]) != "0000-00-00"){
	$cholesterol_date = getDateFormatDB(trim($_REQUEST["this_cholesterol_date"]));
}
$cholesterol_total = floatval($_REQUEST["this_cholesterol_total"]);
$cholesterol_triglycerides = floatval($_REQUEST["this_cholesterol_triglycerides"]);
$cholesterol_LDL = floatval($_REQUEST["this_cholesterol_LDL"]);
$cholesterol_HDL = floatval($_REQUEST["this_cholesterol_HDL"]);
$cholesterol_description = $_REQUEST["this_cholesterol_desc"];

if($cholesterol_primary_key_id != ""){
	$str_query_cholesterol = "update patient_cholesterol set ";
}else{
	$str_query_cholesterol = "insert into patient_cholesterol set ";
	$str_query_cholesterol .= " created_on = now(), ";
	$str_query_cholesterol .= " created_by = '".$_SESSION["authId"]."', ";
	$str_query_cholesterol .= " patient_id = '".$pid."', ";
}
$str_query_cholesterol .= " cholesterol_total = '".imw_real_escape_string(htmlentities($cholesterol_total))."', 
							cholesterol_triglycerides = '".imw_real_escape_string(htmlentities($cholesterol_triglycerides))."', 
							cholesterol_LDL = '".imw_real_escape_string(htmlentities($cholesterol_LDL))."', 
							cholesterol_HDL = '".imw_real_escape_string(htmlentities($cholesterol_HDL))."', 
							description = '".imw_real_escape_string(htmlentities($cholesterol_description))."', 
							creation_date = '".$cholesterol_date.date(" H:i:s")."', 							
							modified_on = now(),
							modified_by = '".$_SESSION['authId']."' ";
if($cholesterol_primary_key_id != ""){
	$str_query_cholesterol .= " where id = '".$cholesterol_primary_key_id."'";	
}
if($cholesterol_date != ""){
	imw_query($str_query_cholesterol);
}

//Please mark any condition you or blood relative have presently or have had in the past - STARTS

//any conditions you
if($any_conditions_u1 != ""){
	$any_conditions_u_arr1 = ",";
	$any_conditions_u_arr1 .= implode(",",$any_conditions_u1);
	$any_conditions_u_arr1 .= ",";
}

$any_conditions_u_arr1_n = "";
if($any_conditions_u1_n != ""){
	$any_conditions_u_arr1_n = ",";
	$any_conditions_u_arr1_n .= implode(",",$any_conditions_u1_n);
	$any_conditions_u_arr1_n .= ",";
}

//under control
if($chk_under_control!=""){
	$chk_under_control_array = implode(",", $chk_under_control);
}
$chk_under_control_array_for_reviwed = $chk_under_control_array;
//Sub Conditions for Arthiritis
if($_POST["elem_subCondition_u1"] != ""){
	$sub_conditions_you = implode(",", $_POST["elem_subCondition_u1"]);
}

$diabetes_values = $_REQUEST["text_diabetes_id"];				//diabetes values
if(is_array($diabetes_values) == true){
	$diabetes_values = implode(", ", $diabetes_values);
}

//conditions descriptions for both(pt and relative)
$txtHighBloodPresher = $_REQUEST['txtHighBloodPresher'];
$txtArthrities = $_REQUEST['txtArthrities'];
$txtStroke = $_REQUEST['txtStroke'];
$desc_u = $_REQUEST["elem_desc_u"];
$txtUlcers = $_REQUEST['txtUlcers'];
$txtCancer = $_REQUEST['txtCancer'];
$txtThyroidProblems = $_REQUEST['txtThyroidProblems'];
$txtLungProblem = $_REQUEST['txtLungProblem'];
$txtThyroidProblem = $_REQUEST['txtThyroidProblem'];
$txtLDL = $_REQUEST["txtLDL"];
$any_conditions_others1 = $_REQUEST["any_conditions_others1"];


//MUR fields
$chk_annual_colorectal_cancer_screenings = $_REQUEST["chk_annual_colorectal_cancer_screenings"];
$chk_receiving_annual_mammogram = $_REQUEST["chk_receiving_annual_mammogram"];
$chk_received_flu_vaccine = $_REQUEST["chk_received_flu_vaccine"];
$chk_received_pneumococcal_vaccine = $_REQUEST["chk_received_pneumococcal_vaccine"];
$chk_high_risk_for_cardiac = $_REQUEST["chk_high_risk_for_cardiac"];
$chk_fall_risk_assd = $_REQUEST["chk_fall_risk_assd"];

$chk_blood_pressure = $_REQUEST["chk_blood_pressure"];
$chk_bmi = $_REQUEST["chk_bmi"];
$received_flu_vaccine_type = $_REQUEST["received_flu_vaccine_type"];
$pneumococcal_vaccine_type = $_REQUEST["pneumococcal_vaccine_type"];
$fall_risk_ass_type = $_REQUEST["fall_risk_ass_type"];
$blood_pressure_type = $_REQUEST["blood_pressure_type"];
$bmi_type = $_REQUEST["bmi_type"];
$bp_systolic = $_REQUEST["bp_systolic"];
$bp_dystolic = $_REQUEST["bp_dystolic"];
$bmi_height = $_REQUEST["bmi_height"];
$bmi_weight = $_REQUEST["bmi_weight"];
$bmi_result = $_REQUEST["bmi_result"];
$bmi_weight_unit = $_REQUEST["bmi_weight_unit"];
$bmi_height_unit = $_REQUEST["bmi_height_unit"];

if($bmi_height){ $bmi_height = ($bmi_height*12);} //CONVERT FEET INTO INCHES.
if($bmi_height_unit){ $bmi_height = ($bmi_height+$bmi_height_unit); }



//Please mark any condition you or blood relative have presently or have had in the past - ENDS
//Review of Systems - STARTS

//Ticked as Negative
if($negChkBx != ""){
	$negChkBxArr = implode(",",$negChkBx);		
}	

//Constitutional & Integumentary
if($review_const != ""){
	$review_const_arr = ",";
	$review_const_arr .= implode(",",$review_const);
	$review_const_arr .= ",";
}
$review_const_others = $_REQUEST["review_const_others"];

//Head/ Neck 
if($review_head != ""){
	$review_head_arr = ",";
	$review_head_arr .= implode(",",$review_head);
	$review_head_arr .= ",";
}
$review_head_others = $_REQUEST["review_head_others"];

//Respiratory 
if($review_resp != ""){
	$review_resp_arr = ",";
	$review_resp_arr .= implode(",",$review_resp);
	$review_resp_arr .= ",";
}
$review_resp_others = $_REQUEST["review_resp_others"];

//Cardiovascular
if($review_card != ""){
	$review_card_arr = ",";
	$review_card_arr .= implode(",",$review_card);
	$review_card_arr .= ",";
}
$review_card_others = $_REQUEST["review_card_others"];

//Gastrointenstinal 
if($review_gastro != ""){
	$review_gastro_arr = ",";
	$review_gastro_arr .= implode(",",$review_gastro);
	$review_gastro_arr .= ",";
}
$review_gastro_others = $_REQUEST["review_gastro_others"];

//Genitourinary
if($review_genit != ""){
	$review_genit_arr = ",";
	$review_genit_arr .= implode(",",$review_genit);
	$review_genit_arr .= ",";
}
$review_genit_others = $_REQUEST["review_genit_others"];

//Allergic/Immunologic & Blood/ Lymphatic
if($review_aller != ""){
	$review_aller_arr = ",";
	$review_aller_arr .= implode(",",$review_aller);
	$review_aller_arr .= ",";
}
$review_aller_others = $_REQUEST["review_aller_others"];

//Neurological Psychiatry & Musculoskeletal 
if($review_neuro != ""){
	$review_neuro_arr = ",";
	$review_neuro_arr .= implode(",",$review_neuro);
	$review_neuro_arr .= ",";
}
$review_neuro_others = $_REQUEST["review_neuro_others"];

//
//review_intgmntr
if($review_intgmntr != ""){
	$review_intgmntr_arr = ",";
	$review_intgmntr_arr .= implode(",",$review_intgmntr);
	$review_intgmntr_arr .= ",";
	$ar_review_sys["review_intgmntr"] = $review_intgmntr_arr;
}
if(!empty($_REQUEST["review_intgmntr_others"])){
$ar_review_sys["review_intgmntr_others"] = $_REQUEST["review_intgmntr_others"];
}
$_REQUEST["review_intgmntr"] = $ar_review_sys["review_intgmntr"];

//review_psychiatry
if($review_psychiatry != ""){
	$review_psychiatry_arr = ",";
	$review_psychiatry_arr .= implode(",",$review_psychiatry);
	$review_psychiatry_arr .= ",";
	$ar_review_sys["review_psychiatry"] = $review_psychiatry_arr;
}
if(!empty($_REQUEST["review_psychiatry_others"])){
$ar_review_sys["review_psychiatry_others"] = $_REQUEST["review_psychiatry_others"];
}
$_REQUEST["review_psychiatry"] = $ar_review_sys["review_psychiatry"];

//review_blood_lymph
if($review_blood_lymph != ""){
	$review_blood_lymph_arr = ",";
	$review_blood_lymph_arr .= implode(",",$review_blood_lymph);
	$review_blood_lymph_arr .= ",";
	$ar_review_sys["review_blood_lymph"] = $review_blood_lymph_arr;
}
if(!empty($_REQUEST["review_blood_lymph_others"])){
$ar_review_sys["review_blood_lymph_others"] = $_REQUEST["review_blood_lymph_others"];
}
$_REQUEST["review_blood_lymph"] = $ar_review_sys["review_blood_lymph"];

//review_musculoskeletal
if($review_musculoskeletal != ""){
	$review_musculoskeletal_arr = ",";
	$review_musculoskeletal_arr .= implode(",",$review_musculoskeletal);
	$review_musculoskeletal_arr .= ",";
	$ar_review_sys["review_musculoskeletal"] = $review_musculoskeletal_arr;
}
if(!empty($_REQUEST["review_musculoskeletal_others"])){
$ar_review_sys["review_musculoskeletal_others"] = $_REQUEST["review_musculoskeletal_others"];
}
$_REQUEST["review_musculoskeletal"] = $ar_review_sys["review_musculoskeletal"];

//review_endocrine
if($review_endocrine != ""){
	$review_endocrine_arr = ",";
	$review_endocrine_arr .= implode(",",$review_endocrine);
	$review_endocrine_arr .= ",";
	$ar_review_sys["review_endocrine"] = $review_endocrine_arr;
}
if(!empty($_REQUEST["review_endocrine_others"])){
$ar_review_sys["review_endocrine_others"] = $_REQUEST["review_endocrine_others"];
}
$_REQUEST["review_endocrine"] = $ar_review_sys["review_endocrine"];

//review_eye
if($review_eye != ""){
	$review_eye_arr = ",";
	$review_eye_arr .= implode(",",$review_eye);
	$review_eye_arr .= ",";
	$ar_review_sys["review_eye"] = $review_eye_arr;
}
if(!empty($_REQUEST["review_eye_others"])){
$ar_review_sys["review_eye_others"] = $_REQUEST["review_eye_others"];
}
$_REQUEST["review_eye"] = $ar_review_sys["review_eye"];

$str_review_sys = json_encode($ar_review_sys);


//Review of Systems - ENDS


//insert / update general health
$check_data1 = "select * from general_medicine where patient_id = '".$pid."'";
$checkSql1 = imw_query($check_data1) or die (imw_error());
$checkrows1 = imw_num_rows($checkSql1);

$row = imw_fetch_array($checkSql1);
if($checkrows1>0){			
	// update			
	$newGeneralMedicineId =  $row['general_id'];		//for audit
	$generalsaveqry = "update general_medicine set ";
}else{			
	// insert new
	$generalsaveqry = "insert into general_medicine set ";					
	$generalsaveqry .= " patient_id = '".$pid."', ";
}		

//-----SETTING VALUE FOR others checkbox---------
$dbOtherChkVal = $row['any_conditions_others_both'];
$OtherChkVal = $_REQUEST['any_conditions_others_both']['0'];
if($OtherChkVal == 1){
	$OtherChkValForReviwed = ','.$OtherChkVal.',';
}
/*strip commas*/
if(strlen($dbOtherChkVal)==3){$dbOtherChkVal = substr($dbOtherChkVal,1,1);}
else if(strlen($dbOtherChkVal)==5){$dbOtherChkVal = substr($dbOtherChkVal,1,3);}

if(empty($dbOtherChkVal) && !empty($OtherChkVal)){
	$OtherChkVal = ','.$OtherChkVal.',';
}else if(empty($dbOtherChkVal) && empty($OtherChkVal)){
	$OtherChkVal = '';
}else if(!empty($dbOtherChkVal)){
	list($ptOther,$relOther) = explode(',',$dbOtherChkVal);
	if(!empty($OtherChkVal) && $OtherChkVal!=$ptOther){
		$OtherChkVal = ','.$OtherChkVal.','.$ptOther.',';
	}
	else if(!empty($OtherChkVal) && $OtherChkVal==$ptOther && empty($relOther)){
		$OtherChkVal = ','.$OtherChkVal.',';
	}
	else if(!empty($OtherChkVal) && $OtherChkVal==$ptOther && !empty($relOther)){
		$OtherChkVal = ','.$OtherChkVal.','.$relOther.',';
	}
	else if(empty($OtherChkVal) && $dbOtherChkVal=='2'){
		$OtherChkVal = ','.$dbOtherChkVal.',';
	}
	else if(empty($OtherChkVal) && $dbOtherChkVal=='1' && empty($relOther)) {
		$OtherChkVal = '';
	}
}

//UNDER CONTROL UPDATION
$chk_under_control_array = get_set_pat_rel_values_save($row["chk_under_control"],$chk_under_control_array,"pat",$delimiter);

//SUB CONDITIONS YOU
$sub_conditions_you = get_set_pat_rel_values_save($row["sub_conditions_you"],$sub_conditions_you,"pat",$delimiter);

//DIABETES VALUE
$diabetes_values = get_set_pat_rel_values_save($row["diabetes_values"],$diabetes_values,"pat",$delimiter);

//conditions descriptions for both(pt and relative)
$txtHighBloodPresher = $_REQUEST['txtHighBloodPresher'];
$txtHighBloodPresher = get_set_pat_rel_values_save($row["desc_high_bp"],$txtHighBloodPresher,"pat",$delimiter);

$txtHeartProblem = $_REQUEST['txtHeartProblem'];
$txtHeartProblem = get_set_pat_rel_values_save($row["desc_heart_problem"],$txtHeartProblem,"pat",$delimiter);

$txtArthrities = $_REQUEST['txtArthrities'];
$txtArthrities = get_set_pat_rel_values_save($row["desc_arthrities"],$txtArthrities,"pat",$delimiter);

$txtLungProblem = $_REQUEST['txtLungProblem'];
$txtLungProblem = get_set_pat_rel_values_save($row["desc_lung_problem"],$txtLungProblem,"pat",$delimiter);

$txtStroke = $_REQUEST['txtStroke'];
$txtStroke = get_set_pat_rel_values_save($row["desc_stroke"],$txtStroke,"pat",$delimiter);

$txtThyroidProblems = $_REQUEST['txtThyroidProblems'];
$txtThyroidProblems = get_set_pat_rel_values_save($row["desc_thyroid_problems"],$txtThyroidProblems,"pat",$delimiter);

$desc_u = $_REQUEST["elem_desc_u"];
$desc_u = get_set_pat_rel_values_save($row["desc_u"],$desc_u,"pat",$delimiter);

$txtLDL = $_REQUEST["txtLDL"];
$txtLDL = get_set_pat_rel_values_save($row["desc_LDL"],$txtLDL,"pat",$delimiter);

$txtUlcers = $_REQUEST['txtUlcers'];
$txtUlcers = get_set_pat_rel_values_save($row["desc_ulcers"],$txtUlcers,"pat",$delimiter);

$txtCancer = $_REQUEST['txtCancer'];
$txtCancer = get_set_pat_rel_values_save($row["desc_cancer"],$txtCancer,"pat",$delimiter);

$any_conditions_others1 = $_REQUEST["any_conditions_others1"];
$any_conditions_others1 = get_set_pat_rel_values_save($row["any_conditions_others"],$any_conditions_others1,"pat",$delimiter);

$generalsaveqry.= "med_doctor = '".imw_real_escape_string(htmlentities($med_doctor))."' ";

$generalsaveqry.= " ,genMedComments='".imw_real_escape_string(htmlentities($genMedComments))."' ";

$generalsaveqry.= " ,any_conditions_you='".imw_real_escape_string(htmlentities($any_conditions_u_arr1))."' ";
//$generalsaveqry.= " ,any_conditions_relative='".$any_conditions_relative_arr1."' ";
$generalsaveqry.= " ,any_conditions_others_both='".imw_real_escape_string(htmlentities($OtherChkVal))."' ";
$generalsaveqry.= " ,sub_conditions_you='".imw_real_escape_string(htmlentities($sub_conditions_you))."' ";

$generalsaveqry.= " ,chk_under_control='".imw_real_escape_string(htmlentities($chk_under_control_array))."' ";
$generalsaveqry.= " ,desc_high_bp = '".imw_real_escape_string(htmlentities($txtHighBloodPresher))."' ";
$generalsaveqry.= " ,desc_arthrities = '".imw_real_escape_string(htmlentities($txtArthrities))."' ";
$generalsaveqry.= " ,desc_stroke = '".imw_real_escape_string(htmlentities($txtStroke))."' ";
$generalsaveqry.= " ,desc_u='".imw_real_escape_string(htmlentities($desc_u))."' ";
$generalsaveqry.= " ,desc_ulcers = '".imw_real_escape_string(htmlentities($txtUlcers))."' ";
$generalsaveqry.= " ,desc_cancer = '".imw_real_escape_string(htmlentities($txtCancer))."' ";
$generalsaveqry.= " ,desc_heart_problem = '".imw_real_escape_string(htmlentities($txtHeartProblem))."' ";
$generalsaveqry.= " ,desc_lung_problem = '".imw_real_escape_string(htmlentities($txtLungProblem))."' ";
$generalsaveqry.= " ,desc_thyroid_problems = '".imw_real_escape_string(htmlentities($txtThyroidProblems))."'";
$generalsaveqry.= " ,desc_LDL= '".imw_real_escape_string(htmlentities($txtLDL))."' ";
$generalsaveqry.= " ,any_conditions_others='".imw_real_escape_string(htmlentities($any_conditions_others1))."' ";

$generalsaveqry.= " ,diabetes_values='".imw_real_escape_string(htmlentities($diabetes_values))."' ";

$generalsaveqry.= " ,chk_annual_colorectal_cancer_screenings = '".imw_real_escape_string(htmlentities($chk_annual_colorectal_cancer_screenings))."' ";
$generalsaveqry.= " ,chk_receiving_annual_mammogram = '".imw_real_escape_string(htmlentities($chk_receiving_annual_mammogram))."' ";
$generalsaveqry.= " ,chk_received_flu_vaccine = '".imw_real_escape_string(htmlentities($chk_received_flu_vaccine))."' ";
$generalsaveqry.= " ,chk_received_pneumococcal_vaccine = '".imw_real_escape_string(htmlentities($chk_received_pneumococcal_vaccine))."' ";
$generalsaveqry.= " ,chk_high_risk_for_cardiac = '".imw_real_escape_string(htmlentities($chk_high_risk_for_cardiac))."' ";
$generalsaveqry.= " ,chk_fall_risk_assd = '".imw_real_escape_string(htmlentities($chk_fall_risk_assd))."' ";
$generalsaveqry.= " ,chk_blood_pressure = '".imw_real_escape_string(htmlentities($chk_blood_pressure))."' ";
$generalsaveqry.= " ,chk_bmi = '".imw_real_escape_string(htmlentities($chk_bmi))."' ";
$generalsaveqry.= " ,received_flu_vaccine_type = '".imw_real_escape_string(htmlentities($received_flu_vaccine_type))."' ";
$generalsaveqry.= " ,pneumococcal_vaccine_type = '".imw_real_escape_string(htmlentities($pneumococcal_vaccine_type))."' ";
$generalsaveqry.= " ,fall_risk_ass_type = '".imw_real_escape_string(htmlentities($fall_risk_ass_type))."' ";
$generalsaveqry.= " ,blood_pressure_type = '".imw_real_escape_string(htmlentities($blood_pressure_type))."' ";
$generalsaveqry.= " ,bmi_type = '".imw_real_escape_string(htmlentities($bmi_type))."' ";


$generalsaveqry.= " ,negChkBx = '".imw_real_escape_string(htmlentities($negChkBxArr))."' ";

$generalsaveqry.= " ,review_const='".imw_real_escape_string(htmlentities($review_const_arr))."' ";
$generalsaveqry.= " ,review_const_others='".imw_real_escape_string(htmlentities($review_const_others))."' ";

$generalsaveqry.= " ,review_head='".imw_real_escape_string(htmlentities($review_head_arr))."' ";
$generalsaveqry.= " ,review_head_others='".imw_real_escape_string(htmlentities($review_head_others))."' ";

$generalsaveqry.= " ,review_resp='".imw_real_escape_string(htmlentities($review_resp_arr))."' ";
$generalsaveqry.= " ,review_resp_others='".imw_real_escape_string(htmlentities($review_resp_others))."' ";

$generalsaveqry.= " ,review_card='".imw_real_escape_string(htmlentities($review_card_arr))."' ";
$generalsaveqry.= " ,review_card_others='".imw_real_escape_string(htmlentities($review_card_others))."' ";

$generalsaveqry.= " ,review_gastro='".imw_real_escape_string(htmlentities($review_gastro_arr))."' ";
$generalsaveqry.= " ,review_gastro_others='".imw_real_escape_string(htmlentities($review_gastro_others))."' ";

$generalsaveqry.= " ,review_genit='".imw_real_escape_string(htmlentities($review_genit_arr))."' ";
$generalsaveqry.= " ,review_genit_others='".imw_real_escape_string(htmlentities($review_genit_others))."' ";

$generalsaveqry.= " ,review_aller='".imw_real_escape_string(htmlentities($review_aller_arr))."' ";
$generalsaveqry.= " ,review_aller_others='".imw_real_escape_string(htmlentities($review_aller_others))."' ";

$generalsaveqry.= " ,review_neuro='".imw_real_escape_string(htmlentities($review_neuro_arr))."' ";
$generalsaveqry.= " ,review_neuro_others='".imw_real_escape_string(htmlentities($review_neuro_others))."' ";


$generalsaveqry.= " ,review_sys='".imw_real_escape_string($str_review_sys)."' ";

$generalsaveqry.= " ,nutrition_counseling='".imw_real_escape_string(htmlentities($_POST['con_for_nut']))."' ";

$generalsaveqry.= " ,birth_sex='".imw_real_escape_string($_POST['birth_sex'])."' ";

$generalsaveqry.= " ,birth_sex_date='".imw_real_escape_string(getDateFormatDB($_POST['birth_sex_date']))."' ";

if($_POST['con_for_nut'] == ''){
	$_POST['con_for_nut_date'] = '00-00-0000';
}

$_POST['con_for_nut_date'] = getDateFormatDB($_POST['con_for_nut_date']);

$generalsaveqry.= " ,nutrition_counseling_date='".imw_real_escape_string(htmlentities($_POST['con_for_nut_date']))."' ";

$generalsaveqry.= " ,physical_activity_counseling='".imw_real_escape_string(htmlentities($_POST['con_for_phy']))."' ";

$_POST['con_for_phy_date'] = getDateFormatDB($_POST['con_for_phy_date']);
$generalsaveqry.= " ,physical_activity_counseling_date='".imw_real_escape_string(htmlentities($_POST['con_for_phy_date']))."' ";
if($_REQUEST['cbkMasterPtCon'] != "no"){
	$_REQUEST['cbkMasterPtCon'] = "yes";
}
$generalsaveqry.= " ,cbk_master_pt_con='".imw_real_escape_string(htmlentities($_REQUEST['cbkMasterPtCon']))."' ";
$generalsaveqry.= " ,any_conditions_you_n='".imw_real_escape_string(htmlentities($any_conditions_u_arr1_n))."' ";
$generalsaveqry.= " ,any_conditions_others_n='".imw_real_escape_string(htmlentities($_REQUEST["any_conditions_others_n"]))."' ";

$generalsaveqry.= " ,cbk_master_ROS='".imw_real_escape_string(htmlentities($_REQUEST["cbkMasterROS"]))."' ";


if($checkrows1>0){
	$generalsaveqry .= " where patient_id='".$pid."' ";
}

$generalsaveSql = imw_query($generalsaveqry);

if($checkrows1==0){			
	$newGeneralMedicineId = imw_insert_id();		//for audit
}
if(!$generalsaveSql){
	echo ("Error : ". imw_error()."<br>".$generalsaveSql);
	$generalMedicineError = "Error : ". imw_errno() . ": " . imw_error();
	print "<br /><br />";
	print $generalsaveqry;
	print "<br /><br />";
	die("Contact Support.");
}else{
	
	// Save No Known Medical conditions in Patient H&P Chart
	
	$facArr = get_facility_details();
	$enable_hp = $facArr['enable_hp'];
	if( $enable_hp) {
		$health->save_med_cond_hp();
	}
	
	//R7 Fix - Wrong Vital Sign saving 
	$ptFormId = (isset($_REQUEST['ptFormId']) && empty($_REQUEST['ptFormId']) == false) ? $_REQUEST['ptFormId'] : '';
	if(empty($ptFormId)){
		//If empty Form Id then get last chart note (Form) ID
		$chkForm = imw_query('SELECT * FROM chart_master_table where patient_id = "'.$_SESSION['patient'].'" AND delete_status = 0 ORDER BY date_of_service DESC LIMIT 0,1');
		if(imw_num_rows($chkForm) > 0){
			$rowFetch = imw_fetch_assoc($chkForm);
			$ptFormId = $rowFetch['id'];
		}
	}
	
	$sqlCheckVs = "SELECT `id` FROM `vital_sign_master` WHERE `form_id`='".$ptFormId."'";
	$sqlCheckVs = imw_query($sqlCheckVs);
	$vs_id = false;
	if($sqlCheckVs && imw_num_rows($sqlCheckVs)>0){
		$vs_id = imw_fetch_assoc($sqlCheckVs);
		$vs_id = $vs_id['id'];
	}//echo $vs_id;die;
	//var_dump($vs_id);
	if(!empty($chk_bmi) && !empty($bmi_type) || !empty($chk_blood_pressure) && !empty($blood_pressure_type) || !empty($bp_systolic) || !empty($bp_dystolic) || !empty($bmi_height) || !empty($bmi_weight) || !empty($bmi_result) || !empty($bmi_weight_unit)){
		
		$where = '';
		if($vs_id){
			$vs_qry = "UPDATE";
			$other_fields = ",  modified_on='".date('Y-m-d h:i:s')."', modified_by='".$_SESSION['authId']."' ";
			$where = " WHERE `id`='".$vs_id."'";
			
			if($bp_systolic){
				$vital_sign_patient = imw_query("UPDATE `vital_sign_patient` SET vital_master_id='".$vs_id."', vital_sign_id='1', range_vital='".$bp_systolic."', unit='mmHg' WHERE vital_master_id='".$vs_id."' AND vital_sign_id='1'");
			}
			if($bp_dystolic){
				$vital_sign_patient = imw_query("UPDATE `vital_sign_patient` SET vital_master_id='".$vs_id."', vital_sign_id='2', range_vital='".$bp_dystolic."', unit='mmHg' WHERE vital_master_id='".$vs_id."' AND vital_sign_id='2'");
			}
			
			if($bmi_height){
				$vital_sign_patient = imw_query("UPDATE `vital_sign_patient` SET vital_master_id='".$vs_id."', vital_sign_id='7', range_vital='".$bmi_height."', unit='inch' WHERE vital_master_id='".$vs_id."' AND vital_sign_id='7'");
			}
			
			if($bmi_weight){
				$vital_sign_patient = imw_query("UPDATE `vital_sign_patient` SET vital_master_id='".$vs_id."', vital_sign_id='8', range_vital='".$bmi_weight."', unit='".$bmi_weight_unit."' WHERE vital_master_id='".$vs_id."' AND vital_sign_id='8'");
			}
			
			if($bmi_result){
				$vital_sign_patient = imw_query("UPDATE `vital_sign_patient` SET vital_master_id='".$vs_id."', vital_sign_id='9', range_vital='".$bmi_result."', unit='kg/sqr. m' WHERE vital_master_id='".$vs_id."' AND vital_sign_id='9'");
			}
			
		}
		else{
			$vs_qry = "INSERT INTO";
			$other_fields = ",  created_on='".date('Y-m-d h:i:s')."', created_by='".$_SESSION['authId']."' ";
		}
		
		$vs_qry .= " `vital_sign_master` SET patient_id='".$pid."', date_vital='".date('Y-m-d')."', time_vital='".date('h:i A')."', comment='".imw_real_escape_string(htmlentities($bmi_type))."', bp_type='".imw_real_escape_string(htmlentities($blood_pressure_type))."', status='0', `form_id`='".$ptFormId."'".$other_fields.$where;
		 $vs_qry_res = imw_query($vs_qry);
		
		 if(!$where){
			 $lastInsertedRecord = imw_insert_id();
		 }
	
		 if( (int) $lastInsertedRecord > 0 ) 
		 {  
		 	$vital_sign_patient = imw_query("INSERT INTO `vital_sign_patient` SET vital_master_id='".$lastInsertedRecord."', vital_sign_id='1', range_vital='".$bp_systolic."', unit='mmHg'");
				
			$vital_sign_patient1 = imw_query("INSERT INTO `vital_sign_patient` SET vital_master_id='".$lastInsertedRecord."', vital_sign_id='2', range_vital='".$bp_dystolic."', unit='mmHg'");
			
			$vital_sign_patient2 = imw_query("INSERT INTO `vital_sign_patient` SET vital_master_id='".$lastInsertedRecord."', vital_sign_id='3', range_vital='', unit='beats/minute'");
				
			$vital_sign_patient3 = imw_query("INSERT INTO `vital_sign_patient` SET vital_master_id='".$lastInsertedRecord."', vital_sign_id='4', range_vital='', unit='breaths/minute'");
					
			$vital_sign_patient4 = imw_query("INSERT INTO `vital_sign_patient` SET vital_master_id='".$lastInsertedRecord."', vital_sign_id='5', range_vital='', unit='ml/l'");
			
			$vital_sign_patient5 = imw_query("INSERT INTO `vital_sign_patient` SET vital_master_id='".$lastInsertedRecord."', vital_sign_id='6', range_vital='', unit='&deg;f'");
			
			$vital_sign_patient6 = imw_query("INSERT INTO `vital_sign_patient` SET vital_master_id='".$lastInsertedRecord."', vital_sign_id='7', range_vital='".$bmi_height."', unit='inch'");
			
			$vital_sign_patient7 = imw_query("INSERT INTO `vital_sign_patient` SET vital_master_id='".$lastInsertedRecord."', vital_sign_id='8', range_vital='".$bmi_weight."', unit='".$bmi_weight_unit."'");
		
			$vital_sign_patient8 = imw_query("INSERT INTO `vital_sign_patient` SET vital_master_id='".$lastInsertedRecord."', vital_sign_id='9', range_vital='".$bmi_result."', unit='kg/sqr. m'");
		 
		}
	}
}
	
//START INSERT data to patient_custom_field TABLE for General Health
if($pid <> ""){		
	$createdBy = $_SESSION['authId'];
	$arrPatientControlId = $_REQUEST['hidPatientControlPId'];
	$arrcustomField = $_REQUEST['hidcustomField'];
	$control = explode("_",$Value);		 		
	$controlVal = $_REQUEST[$control[0]]; 
	if(count($arrPatientControlId) > 0){
		foreach($arrPatientControlId as $patientControlKey => $patientControlValue){ 
			foreach($arrcustomField as $Key => $Value){		
				$control = explode("_",$Value);		 		
				$controlVal = $_REQUEST[$control[0]];
				$controlType = "";
				$controlType = $control[2]; 		 		
				if(!$arrPatientControlId[$patientControlKey]){
					$customQry = "insert into patient_custom_field set "; 
					$customQry .= "created_by = '".$createdBy."',";
					$customQry .= "created_date_time = '".date('Y-m-d H:i:s')."',";	
				}
				elseif($arrPatientControlId[$patientControlKey]){
					$customQry = "update patient_custom_field set "; 
					$customQry .= "modified_by = '".$createdBy."',";
					$customQry .= "modified_date_time = '".date('Y-m-d H:i:s')."',";	
					$queryUpdate = " where id = '".$arrPatientControlId[$patientControlKey]."'";
				}	
				$customQry .= "patient_id = '$pid',";
				$customQry .= "admin_control_id = '$control[1]',";
				if($controlType == "checkbox"){
					if($controlVal){
						$customQry .= "patient_cbk_control_value = 'checked',";
					}
					else{
						$customQry .= "patient_cbk_control_value = 'unChecked',";
					}
				}
				$customQry .= "patient_control_value = '".imw_real_escape_string(htmlentities($controlVal))."'";
				if($arrPatientControlId[$patientControlKey]){
					$customQry .= $queryUpdate;			
				}	
				$rsCustomQry = "";		
				$rsCustomQry = imw_query($customQry);
				if($rsCustomQry){									
					unset($arrPatientControlId[$patientControlKey]);	
					unset($arrcustomField[$Key]);						
					break ;						
				}
			}
		}
	}
	//Spatiality Question START
	if((isset($_REQUEST["totQuestion"]) == true) && (empty($_REQUEST["totQuestion"]) == false)){
		$newPatSplQueAnsID = 0;
		$intTotQuestion = 0;
		$patientId = $pid;
		$currentOprator = $_SESSION['authId'];
		$intTotQuestion = $_REQUEST["totQuestion"];
		for($i = 0; $i < $intTotQuestion; $i++){
			$arrTxtAreaAnswerOption = array();
			$intSplId = $intQueId = $intAnsType = 0;
			$strAnswer = $strAnsElType = "";
			$intSplId = $_REQUEST["hidSplId$i"];
			$intQueId = $_REQUEST["hidQueId$i"];
			$strAnsElType = $_REQUEST["hidAnsElType$i"];
			if($strAnsElType == "textarea"){
				$strAnswer = core_refine_user_input($_REQUEST["txtAreaAnswer$i"]);
				$intAnsType = 0;
			}
			elseif($strAnsElType == "multipleSelect"){
				$intAnsType = 1;
				$arrTxtAreaAnswerOption = $_REQUEST["txtAreaAnswer$i"];
			}
			
			//pre($arrPatQueAnsOpDeActiveState,1);							
			$qryChkRecord = "select id from patient_specialty_question_answer where 
								specialty_id = '".$intSplId."' and 
								question_id = '".$intQueId."' and 
								patient_id = '".$patientId."' and 
								med_hx_tab = 'General Health' and 
								row_del_status = '0' LIMIT 1 ";
			$rsChkRecord = imw_query($qryChkRecord);			
			if(imw_num_rows($rsChkRecord) > 0){
				$intPatSplQueId = 0;
				$rowChkRecord = imw_fetch_row($rsChkRecord);
				$intPatSplQueId = $rowChkRecord[0];
				//Collecting this question answer options for current patient
				$arrPatQueAnsOpActiveState = $arrPatQueAnsOpDeActiveState = $arrDeActiveRowId = array();
				$qryGetPatOp = "select * from patient_specialty_question_options_answer where question_id = '".$intQueId."' and  patient_specialty_question_answer_id = '".$intPatSplQueId."' ";
				$rsGetPatOp = imw_query($qryGetPatOp);
				while($rowGetPatOp = imw_fetch_array($rsGetPatOp)){
					$intPatRowId = $intPatQueId = $intPatOpId = $intPatStateId = 0;
					$intPatRowId = $rowGetPatOp["id"];
					$intPatQueId = $rowGetPatOp["question_id"];
					$intPatOpId = $rowGetPatOp["option_id"];
					$intPatStateId = $rowGetPatOp["state"];
					if($intPatStateId == 1){
						$arrPatQueAnsOpActiveState[$intPatOpId] = $intPatRowId;
					}
					elseif($intPatStateId == 0){
						$arrPatQueAnsOpDeActiveState[$intPatOpId] = $intPatRowId;
					}
				}
				//Collecting this question answer options for current patient - End
				if($intPatSplQueId > 0){
					if($intAnsType == 0){
						$qryUpdate = "update patient_specialty_question_answer set row_modify_by = '".$currentOprator."', 
										row_modify_date_time = NOW(), 
										pat_answer = '".$strAnswer."', 
										pat_provider = '".$_REQUEST["patProviderID"]."', 
										med_hx_tab = 'General Health', 
										ans_type = '".$intAnsType."' 
										where id = '".$intPatSplQueId."'";
						$rsUpdate = imw_query($qryUpdate);
					}
					elseif($intAnsType == 1){
						$qryUpdate = "update patient_specialty_question_answer set row_modify_by = '".$currentOprator."', 
										row_modify_date_time = NOW(),
										pat_provider = '".$_REQUEST["patProviderID"]."', 
										med_hx_tab = 'General Health', 
										ans_type = '".$intAnsType."' 
										where id = '".$intPatSplQueId."'";
						$rsUpdate = imw_query($qryUpdate);
						//pre($arrPatQueAnsOpActiveState,1);
						//pre($arrTxtAreaAnswerOption,1);
						foreach($arrTxtAreaAnswerOption as $intTxtAreaAnswerOptionKey => $intTxtAreaAnswerOptionVal){
							if((int)$intTxtAreaAnswerOptionVal > 0){
								if((array_key_exists($intTxtAreaAnswerOptionVal, $arrPatQueAnsOpActiveState) == false) && (array_key_exists($intTxtAreaAnswerOptionVal, $arrPatQueAnsOpDeActiveState) == false)){
									$qryInsrtPatSplQueOpAns = "insert into patient_specialty_question_options_answer 
															(patient_specialty_question_answer_id, question_id, option_id, state) 
															Values('".$intPatSplQueId."', '".$intQueId."', '".$intTxtAreaAnswerOptionVal."', '1')";
									imw_query($qryInsrtPatSplQueOpAns);							
								}
								else{
									if(array_key_exists($intTxtAreaAnswerOptionVal, $arrPatQueAnsOpDeActiveState) == true){
										$arrDeActiveRowId[] = $arrPatQueAnsOpDeActiveState[$intTxtAreaAnswerOptionVal];
									}
									unset($arrPatQueAnsOpActiveState[$intTxtAreaAnswerOptionVal]);
								}
							}
						}
					}
					//pre($arrPatQueAnsOpActiveState,1);
					if(count($arrPatQueAnsOpActiveState) > 0){
						$strPatQueAnsID = $qryUpdatePatSplQueOpAnsMakeDeactive = "";
						$strPatQueAnsID = implode(",",$arrPatQueAnsOpActiveState);
						$qryUpdatePatSplQueOpAnsMakeDeactive = "update patient_specialty_question_options_answer set state = '0' where id IN(".$strPatQueAnsID.") ";
						imw_query($qryUpdatePatSplQueOpAnsMakeDeactive);							
					}
					if(count($arrDeActiveRowId) > 0){
						$strDeActiveRowId = $qryUpdatePatSplQueOpAnsMakeActive = "";
						$strDeActiveRowId = implode(",",$arrDeActiveRowId);
						$qryUpdatePatSplQueOpAnsMakeActive = "update patient_specialty_question_options_answer set state = '1' where id IN(".$strDeActiveRowId.") ";
						imw_query($qryUpdatePatSplQueOpAnsMakeActive);							
					}
				}
			}
			else{				
				if((empty($strAnswer) == false) && ($intAnsType == 0)){
					$qryInsert = "insert into patient_specialty_question_answer (specialty_id, question_id, patient_id, pat_provider, ans_type, pat_answer, row_created_by, row_created_date_time, med_hx_tab) values 
									('".$intSplId."', '".$intQueId."', '".$patientId."', '".$_REQUEST["patProviderID"]."', '".$intAnsType."', '".$strAnswer."', '".$currentOprator."', NOW(), 'General Health')";
					$rsInsert = imw_query($qryInsert);
				}
				elseif(($intAnsType == 1) && (count($arrTxtAreaAnswerOption) > 0)){
					//die($qryChkRecord);
					$qryInsert = "insert into patient_specialty_question_answer (specialty_id, question_id, patient_id, pat_provider, ans_type, pat_answer, row_created_by, row_created_date_time, med_hx_tab) values 
									('".$intSplId."', '".$intQueId."', '".$patientId."', '".$_REQUEST["patProviderID"]."', '".$intAnsType."', '".$strAnswer."', '".$currentOprator."', NOW(), 'General Health')";
					$rsInsert = imw_query($qryInsert);
					$newPatSplQueAnsID = imw_insert_id();
					if($newPatSplQueAnsID > 0){
						foreach($arrTxtAreaAnswerOption as $intTxtAreaAnswerOptionKey => $intTxtAreaAnswerOptionVal){
							if((int)$intTxtAreaAnswerOptionVal > 0){
								$qryInsrtPatSplQueOpAns = "insert into patient_specialty_question_options_answer 
															(patient_specialty_question_answer_id, question_id, option_id, state) 
															Values('".$newPatSplQueAnsID."', '".$intQueId."', '".$intTxtAreaAnswerOptionVal."', '1')
															";
								imw_query($qryInsrtPatSplQueOpAns);							
							}
						}
					}
				}
			}
		}
	}
	//Spatiality Question END
}

//
$_REQUEST['exam_date'] = $exam_date_new;
$_REQUEST['eye_problem'] = $eye_problem_arr;
$_REQUEST['any_conditions_u'] = $any_conditions_u_arr;
$_REQUEST['any_conditions_relative'] = $any_conditions_relative_arr;

$_REQUEST['any_conditions_u1'] = $any_conditions_u_arr1;
$_REQUEST['any_conditions_u1_n'] = $any_conditions_u_arr1_n;

$_REQUEST['any_conditions_relative1'] = $any_conditions_relative_arr1;
//$_REQUEST['any_conditions_relative1_n'] = $anyConditionsRelativeN;

$_REQUEST['any_conditions_others_both'] = $OtherChkValForReviwed;

$_REQUEST['review_const'] = $review_const_arr;
$_REQUEST['review_head'] = $review_head_arr;
$_REQUEST['review_resp'] = $review_resp_arr;
$_REQUEST['review_card'] = $review_card_arr;
$_REQUEST['review_gastro'] = $review_gastro_arr;
$_REQUEST['review_genit'] = $review_genit_arr;
$_REQUEST['review_aller'] = $review_aller_arr;
$_REQUEST['review_neuro'] = $review_neuro_arr;
$_REQUEST['negChkBx'] = $negChkBxArr;
$_REQUEST['chk_under_control'] = $chk_under_control_array_for_reviwed;

if(!$_REQUEST['chk_annual_colorectal_cancer_screenings']){
	$_REQUEST['chk_annual_colorectal_cancer_screenings'] = 0;
}
if(!$_REQUEST['chk_receiving_annual_mammogram']){
	$_REQUEST['chk_receiving_annual_mammogram'] = 0;
}
if(!$_REQUEST['chk_received_flu_vaccine']){
	$_REQUEST['chk_received_flu_vaccine'] = 0;
}
if(!$_REQUEST['chk_received_pneumococcal_vaccine']){
	$_REQUEST['chk_received_pneumococcal_vaccine'] = 0;
}

if(!$_REQUEST["offered_cessation_counseling"]){
	$_REQUEST["offered_cessation_counseling"] = 0;
}
if(!$_REQUEST["chk_high_risk_for_cardiac"]){
	$_REQUEST["chk_high_risk_for_cardiac"] = 0;
}
if(!$_REQUEST["radio_family_smoke"]){
	$_REQUEST["radio_family_smoke"] = 0;
}
if(!$_REQUEST["cbkMasterROS"]){
	$_REQUEST["cbkMasterROS"] = 0;
}

$_REQUEST["smokers_in_relatives"] = implode(",",(array)$_REQUEST["smokers_in_relatives"]);

$_REQUEST["elem_subCondition_u1"] = implode(",",(array)$_REQUEST["elem_subCondition_u1"]);

$arr_this_blood_sugar_time = explode("-",$_REQUEST["this_blood_sugar_time"]);
$_REQUEST["this_blood_sugar_time"] = $arr_this_blood_sugar_time[1];
if(!$_REQUEST["this_blood_sugar_fasting"]){
	$_REQUEST["this_blood_sugar_fasting"] = 0;
}
if(!$_REQUEST["offered_cessation_counseling"]){
	$_REQUEST["offered_cessation_counseling"] = 0;
}
//
require_once("../family_hx/inc_save_general_health_relatives.php");
//audit

//  Audit Functionality Here
$audit_policy_status = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];
if($audit_policy_status == 1){
	$arrAuditTrail = array();
	$arrAuditTrail = unserialize(urldecode($_REQUEST["hidDataMedicalHistory_General_Health"]));
	foreach ((array)$arrAuditTrail as $key => $value) {
		if(trim($arrAuditTrail[$key]['Filed_Label']) == "elem_chronicDesc"){
			$arrAuditTrail [$key]["New_Value"] = addcslashes($strDesc,"\0..\37!@\177..\377");
		}	
		if(trim($arrAuditTrail[$key]['Filed_Label']) == "elem_chronicRelative"){
			$arrAuditTrail [$key]["New_Value"] = addcslashes($strRelative,"\0..\37!@\177..\377");
		}
		
		if(trim($arrAuditTrail [$key]["Table_Name"]) == "general_medicine")
		{
			if (array_key_exists('Pk_Id', $arrAuditTrail[$key])) 
			{
				if(empty($arrAuditTrail [$key]["Pk_Id"]) && $arrAuditTrail [$key]["Pk_Id"] == ""){
					$arrAuditTrail [$key]["Pk_Id"] = $newGeneralMedicineId;
					$arrAuditTrail [$key]["Action"] = "add";			
				}			
			}		
		}
	}
	$table = array("general_medicine");
	$error = array($generalMedicineError);
	$mergedArray = merging_array($table,$error);
	auditTrail($arrAuditTrail,$mergedArray,0,0,0);
}


//making review in database - start
require_once($GLOBALS['srcdir'].'/classes/class.cls_review_med_hx.php');
$OBJReviewMedHx = new CLSReviewMedHx;
foreach ((array)$arrReview_Social as $key => $value) {
	if(trim($arrReview_Social[$key]['UI_Filed_Name']) == "SmokingStatus"){
		$smoke_status = $_POST["SmokingStatus"];
		if($smoke_status!=""){
			$smoke_qry=imw_query("select * from smoking_status_tbl where id='$smoke_status'");		
			$smoke_row=imw_fetch_array($smoke_qry);
			$add_smoke_detail= ucfirst($smoke_row['desc']).' / '.$smoke_row['code'];
		}
		$arrReview_Social[$key]["New_Value"] = imw_real_escape_string(htmlentities($add_smoke_detail));
	}
	if(trim($arrReview_Social[$key]['UI_Filed_Name']) == "alcohal"){
		$alcohal = implode(",",$_POST["alcohal"]);
		$arrReview_Social[$key]["New_Value"] = imw_real_escape_string(htmlentities($alcohal));
	}
}

foreach ((array)$arrReview_GH as $key => $value) {
	if(trim($arrReview_GH[$key]['Filed_Label']) == "elem_chronicDesc"){
		$arrReview_GH[$key]["New_Value"] = addcslashes($strDesc,"\0..\37!@\177..\377");
	}	
	if(trim($arrReview_GH[$key]['Filed_Label']) == "elem_chronicRelative"){
		$arrReview_GH[$key]["New_Value"] = addcslashes($strRelative,"\0..\37!@\177..\377");
	}
	
	if(trim($arrReview_GH[$key]['UI_Filed_Name']) == "text_diabetes_id"){
		$text_diabetes_id = $_REQUEST['text_diabetes_id'];					//high bp
		if(is_array($text_diabetes_id) == true){
			$strTextDiabetesVal = implode(", ", $text_diabetes_id);
		}
		else{
			$strTextDiabetesVal = $text_diabetes_id;
		}
		$arrReview_GH[$key]["New_Value"] = addcslashes($strTextDiabetesVal,"\0..\37!@\177..\377");
	}
	
	
	if(trim($arrReview_GH[$key]['UI_Filed_Name']) == "rel_text_diabetes_id"){
		$rel_text_diabetes_id = $_REQUEST['rel_text_diabetes_id'];					//high bp
		if(is_array($rel_text_diabetes_id) == true){
			$strRelTextDiabetesVal = implode(", ", $rel_text_diabetes_id);
		}
		else{
			$strRelTextDiabetesVal = $rel_text_diabetes_id;
		}
		$arrReview_GH[$key]["New_Value"] = addcslashes($strRelTextDiabetesVal,"\0..\37!@\177..\377");
	}
	
	if(trim($arrReview_GH[$key]['UI_Filed_Name']) == "relDescHighBp"){
		$relDescHighBp = $_REQUEST['relDescHighBp'];					//high bp
		if(is_array($relDescHighBp) == true){
			$strRelDescHighBp = implode(", ", $relDescHighBp);
		}
		else{
			$strRelDescHighBp = $relDescHighBp;
		}
		if($_REQUEST['rel_other_relDescHighBp']!=''){
			$strRelDescHighBp.=', '.$_REQUEST['rel_other_relDescHighBp']; 
		}
		$arrReview_GH[$key]["New_Value"] = addcslashes($strRelDescHighBp,"\0..\37!@\177..\377");
	}
	if(trim($arrReview_GH[$key]['UI_Filed_Name']) == "relDescArthritisProb"){
		$relDescArthritisProb = $_REQUEST['relDescArthritisProb'];					//arthiritis
		if(is_array($relDescArthritisProb) == true){
			$strRelDescArthritisProb = implode(", ", $relDescArthritisProb);
		}
		else{
			$strRelDescArthritisProb = $relDescArthritisProb;
		}
		if($_REQUEST['rel_other_relDescArthritisProb']!=''){
			$strRelDescArthritisProb.=', '.$_REQUEST['rel_other_relDescArthritisProb']; 
		}
		$arrReview_GH[$key]["New_Value"] = addcslashes($strRelDescArthritisProb,"\0..\37!@\177..\377");
	}	
	if(trim($arrReview_GH[$key]['UI_Filed_Name']) == "relDescStrokeProb"){
		$relDescStrokeProb = $_REQUEST['relDescStrokeProb'];			//stroke
		if(is_array($relDescStrokeProb) == true){
			$strRelDescStrokeProb = implode(", ", $relDescStrokeProb);
		}
		else{
			$strRelDescStrokeProb = $relDescStrokeProb;
		}
		if($_REQUEST['rel_other_relDescStrokeProb']!=''){
			$strRelDescStrokeProb.=', '.$_REQUEST['rel_other_relDescStrokeProb']; 
		}
		$arrReview_GH[$key]["New_Value"] = addcslashes($strRelDescStrokeProb,"\0..\37!@\177..\377");
	}
	if(trim($arrReview_GH[$key]['UI_Filed_Name']) == "elem_desc_r"){
		$elem_desc_r = $_REQUEST["elem_desc_r"];								//diabetic
		if(is_array($elem_desc_r) == true){
			$strDesc_r = implode(", ", $elem_desc_r);
		}
		else{
			$strDesc_r = $elem_desc_r;
		}
		if($_REQUEST['rel_other_elem_desc_r']!=''){
			$strDesc_r.=', '.$_REQUEST['rel_other_elem_desc_r']; 
		}
		$arrReview_GH[$key]["New_Value"] = addcslashes($strDesc_r,"\0..\37!@\177..\377");
	}
	if(trim($arrReview_GH[$key]['UI_Filed_Name']) == "relDescUlcersProb"){
		$relDescUlcersProb = $_REQUEST['relDescUlcersProb'];			//Ulcers
		if(is_array($relDescUlcersProb) == true){
			$strRelDescUlcersProb = implode(", ", $relDescUlcersProb);
		}
		else{
			$strRelDescUlcersProb = $relDescUlcersProb;
		}
		if($_REQUEST['rel_other_relDescUlcersProb']!=''){
			$strRelDescUlcersProb.=', '.$_REQUEST['rel_other_relDescUlcersProb']; 
		}
		$arrReview_GH[$key]["New_Value"] = addcslashes($strRelDescUlcersProb,"\0..\37!@\177..\377");
	}
	if(trim($arrReview_GH[$key]['UI_Filed_Name']) == "relDescCancerProb"){
		$relDescCancerProb = $_REQUEST['relDescCancerProb'];			//Cancer
		if(is_array($relDescCancerProb) == true){
			$strRelDescCancerProb = implode(", ", $relDescCancerProb);
		}
		else{
			$strRelDescCancerProb = $relDescCancerProb;
		}
		if($_REQUEST['rel_other_relDescCancerProb']!=''){
			$strRelDescCancerProb.=', '.$_REQUEST['rel_other_relDescCancerProb']; 
		}
		$arrReview_GH[$key]["New_Value"] = addcslashes($strRelDescCancerProb,"\0..\37!@\177..\377");
	}
	if(trim($arrReview_GH[$key]['UI_Filed_Name']) == "relDescHeartProb"){
		$relDescHeartProb = $_REQUEST['relDescHeartProb'];				//Heart Problem
		if(is_array($relDescHeartProb) == true){
			$strRelDescHeartProb = implode(", ", $relDescHeartProb);
		}
		else{
			$strRelDescHeartProb = $relDescHeartProb;
		}
		if($_REQUEST['rel_other_relDescHeartProb']!=''){
			$strRelDescHeartProb.=', '.$_REQUEST['rel_other_relDescHeartProb']; 
		}
		$arrReview_GH[$key]["New_Value"] = addcslashes($strRelDescHeartProb,"\0..\37!@\177..\377");
	}
	if(trim($arrReview_GH[$key]['UI_Filed_Name']) == "relDescLungProb"){
		$relDescLungProb = $_REQUEST['relDescLungProb'];				//Lung Problem
		if(is_array($relDescLungProb) == true){
			$strRelDescLungProb = implode(", ", $relDescLungProb);
		}
		else{
			$strRelDescLungProb = $relDescLungProb;
		}
		if($_REQUEST['rel_other_relDescLungProb']!=''){
			$strRelDescLungProb.=', '.$_REQUEST['rel_other_relDescLungProb']; 
		}
		$arrReview_GH[$key]["New_Value"] = addcslashes($strRelDescLungProb,"\0..\37!@\177..\377");
	}
	if(trim($arrReview_GH[$key]['UI_Filed_Name']) == "relDescThyroidProb"){
		$relDescThyroidProb = $_REQUEST['relDescThyroidProb'];			//Thyroid problem
		if(is_array($relDescThyroidProb) == true){
			$strRelDescThyroidProb = implode(", ", $relDescThyroidProb);
		}
		else{
			$strRelDescThyroidProb = $relDescThyroidProb;
		}
		if($_REQUEST['rel_other_relDescThyroidProb']!=''){
			$strRelDescThyroidProb.=', '.$_REQUEST['rel_other_relDescThyroidProb']; 
		}
		$arrReview_GH[$key]["New_Value"] = addcslashes($strRelDescThyroidProb,"\0..\37!@\177..\377");
	}
	if(trim($arrReview_GH[$key]['UI_Filed_Name']) == "relDescLDL"){
		$relDescLDL = $_REQUEST["relDescLDL"];							//LDL
		if(is_array($relDescLDL) == true){
			$strRelDescLDL = implode(", ", $relDescLDL);
		}
		else{
			$strRelDescLDL = $relDescLDL;
		}
		if($_REQUEST['rel_other_relDescLDL']!=''){
			$strRelDescLDL.=', '.$_REQUEST['rel_other_relDescLDL']; 
		}
		$arrReview_GH[$key]["New_Value"] = addcslashes($strRelDescLDL,"\0..\37!@\177..\377");
	}
	if(trim($arrReview_GH[$key]['UI_Filed_Name']) == "ghRelDescOthers"){
		$ghRelDescOthers = $_REQUEST['ghRelDescOthers'];				//Others
		if(is_array($ghRelDescOthers) == true){
			$strGhRelDescOthers = implode(", ", $ghRelDescOthers);
		}
		else{
			$strGhRelDescOthers = $ghRelDescOthers;
		}
		if($_REQUEST['rel_other_ghRelDescOthers']!=''){
			$strGhRelDescOthers.=', '.$_REQUEST['rel_other_ghRelDescOthers']; 
		}
		$arrReview_GH[$key]["New_Value"] = addcslashes($strGhRelDescOthers,"\0..\37!@\177..\377");
	}
	
	if(trim($arrReview_FamilyHx[$key]['UI_Filed_Name']) == "rel_elem_chronicDesc"){
		$arrReview_FamilyHx[$key]["New_Value"] = addcslashes($strDescForReviwed,"\0..\37!@\177..\377");
	}
	if(trim($arrReview_FamilyHx[$key]['UI_Filed_Name']) == "elem_chronicRelative"){
		$arrReview_FamilyHx[$key]["New_Value"] = addcslashes($strRelativeForReviwed,"\0..\37!@\177..\377");
	}
		
}

$OBJReviewMedHx->reviewMedHx($arrReview_GH,$_SESSION['authId'],"General Health",$pid,0,0);

$OBJReviewMedHx->reviewMedHx($arrReview_AD,$_SESSION['authId'],"General Health - Advanced Directive",$pid,0,0);

$_REQUEST['number_of_years_with_smoke'].= ' '.$_REQUEST['smoke_years_months'];

$OBJReviewMedHx->reviewMedHx($arrReview_Social,$_SESSION['authId'],"General Health",$pid,0,0);
//making review in database - end

//redirecting...
$curr_tab = $_REQUEST["curr_tab"];
$curr_dir = "general_health";
$next_tab = $_REQUEST["next_tab"];
$next_dir = $_REQUEST["next_dir"];
$curr_tab = ($next_tab != "") ? $next_tab : $curr_tab; 
$buttons_to_show = $_REQUEST["buttons_to_show"];

// Removed Removed Sync Code

?>	
<script type="text/javascript">
	//if(typeof(window.parent.setChkChangeDefault)!='undefined'){window.parent.setChkChangeDefault();}
	var curr_tab = '<?php echo $curr_tab; ?>';
	top.show_loading_image("show", 100);	
	if(top.document.getElementById('medical_tab_change')) {
		if(top.document.getElementById('medical_tab_change').value!='yes') {
			top.alert_notification_show('<?php echo $arr_info_alert["save"];?>');
		}
		if(top.document.getElementById('medical_tab_change').value=='yes') {
			top.chkConfirmSave('yes','set');		
		}
		top.document.getElementById('medical_tab_change').value='';
	}
	top.fmain.location.href = top.JS_WEB_ROOT_PATH+'/interface/Medical_history/index.php?showpage='+curr_tab;
	top.show_loading_image("hide");	
</script>
