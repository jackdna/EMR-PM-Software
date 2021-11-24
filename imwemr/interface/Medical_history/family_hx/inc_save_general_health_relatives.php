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

$strDivider = '~|~';
$delimiter = '~|~';

//GETTING DB VALUE TO MERGE/SPLIC pt & relatives VALUES.
$getGHquery = "select * from general_medicine where patient_id='".(int) $pid."'";
$getGHResult = imw_query($getGHquery);
$getGHrs = imw_fetch_assoc($getGHResult);


//any conditions relative
if($any_conditions_relative1 != ""){
	$any_conditions_relative_arr1 = ",";
	$any_conditions_relative_arr1 .= implode(",",$any_conditions_relative1);
	$any_conditions_relative_arr1 .= ",";
}
if($any_conditions_relative1_n != ""){
	$anyConditionsRelativeN = ",";
	$anyConditionsRelativeN .= implode(",",$any_conditions_relative1_n);
	$anyConditionsRelativeN .= ",";
}

//-----SETTING VALUE FOR others checkbox---------
$dbOtherChkVal = $getGHrs['any_conditions_others_both'];
$OtherChkVal = $_REQUEST['any_conditions_others_rel']['0'];
//die('ot'.$OtherChkVal);
$OtherChkValForReviwed = "";
if($OtherChkVal == 2){
	$OtherChkValForReviwed = ','.$OtherChkVal.',';
}
//die('ot'.$OtherChkValForReviwed);
/*strip commas*/
if(strlen($dbOtherChkVal)==3){$dbOtherChkVal = substr($dbOtherChkVal,1,1);}
else if(strlen($dbOtherChkVal)==5){$dbOtherChkVal = substr($dbOtherChkVal,1,3);}
if(empty($dbOtherChkVal) && !empty($OtherChkVal)){
	$OtherChkVal = ','.$OtherChkVal.',';
}else if(empty($dbOtherChkVal) && empty($OtherChkVal)){
	$OtherChkVal = '';
}else if(!empty($dbOtherChkVal)){
	list($ptOther,$relOther) = explode(',',$dbOtherChkVal);
	if(!empty($OtherChkVal) && $OtherChkVal!=$ptOther) $OtherChkVal = ','.$ptOther.','.$OtherChkVal.',';
	else if(!empty($OtherChkVal) && $OtherChkVal==$ptOther)$OtherChkVal = ','.$OtherChkVal.',';
	else if(empty($OtherChkVal) && $ptOther=='1') $OtherChkVal = ','.$ptOther.',';
	else if(empty($OtherChkVal) && $ptOther=='2') $OtherChkVal = '';	
}
//	die('dbother='.$dbOtherChkVal.'&nbsp; chkval='.$OtherChkVal);
//--------END OF SETTING VALUE FOR others checkbox-----------

//----under control-------
if($chk_under_control!=""){
	$chk_under_control_array = implode(",", $chk_under_control);
}
$dbChkUC = $getGHrs["chk_under_control"]; //currnet db value for this field.

if(stristr($dbChkUC,$strDivider)){
	list($ChkUCpt,$ChkUCrelDB) = explode($strDivider,$dbChkUC);
	$ChkUC = $ChkUCpt.$strDivider.$chk_under_control_array;
}else{$ChkUC = $dbChkUC.$strDivider.$chk_under_control_array;}
//	die('DBvalue='.$dbChkUC.'&nbsp;&nbsp;New Value='.$ChkUC);
//-------end of under control value management--------

//Sub Conditions for Arthiritis
if($_POST["rel_elem_subCondition_u1"] != ""){
	$rel_sub_conditions_you = implode(",", $_POST["rel_elem_subCondition_u1"]);
}
$elemSubConditionU1ForReviwed = $rel_sub_conditions_you;
$dbSubCU = $getGHrs["sub_conditions_you"]; //currnet db value for this field.
if(stristr($dbSubCU,$strDivider)){
	list($SubCUpt,$SubCUrel) = explode($strDivider,$dbSubCU);
	$SubConYou = $SubCUpt.$strDivider.$rel_sub_conditions_you;
}else{$SubConYou = $dbSubCU.$strDivider.$rel_sub_conditions_you;}
//	die('DBvalue='.$dbSubCU.'&nbsp;&nbsp;New Value='.$SubConYou);
//---------END OF SUB CONDITION FOR arthiritis.


//conditions relations
$relDescHighBp = $_REQUEST['relDescHighBp'];					//high bp
if(is_array($relDescHighBp) == true){
	$strRelDescHighBp = implode(", ", $relDescHighBp);
}
else{
	$strRelDescHighBp = $relDescHighBp;
}
if((preg_match("/\bOther\b/i", $strRelDescHighBp) || preg_match("/\b Other\b/i", $strRelDescHighBp)) && $_REQUEST['rel_other_relDescHighBp']!='') {
	$strRelDescHighBp.=', '.$_REQUEST['rel_other_relDescHighBp'];
}

$relDescArthritisProb = $_REQUEST['relDescArthritisProb'];		//arthiritis
if(is_array($relDescArthritisProb) == true){
	$strRelDescArthritisProb = implode(", ", $relDescArthritisProb);
}
else{
	$strRelDescArthritisProb = $relDescArthritisProb;
}
if((preg_match("/\bOther\b/i", $strRelDescArthritisProb) || preg_match("/\b Other\b/i", $strRelDescArthritisProb)) && $_REQUEST['rel_other_relDescArthritisProb']!='') {
	$strRelDescArthritisProb.=', '.$_REQUEST['rel_other_relDescArthritisProb'];
}

$relDescStrokeProb = $_REQUEST['relDescStrokeProb'];			//stroke
if(is_array($relDescStrokeProb) == true){
	$strRelDescStrokeProb = implode(", ", $relDescStrokeProb);
}
else{
	$strRelDescStrokeProb = $relDescStrokeProb;
}
if((preg_match("/\bOther\b/i", $strRelDescStrokeProb) || preg_match("/\b Other\b/i", $strRelDescStrokeProb)) && $_REQUEST['rel_other_relDescStrokeProb']!='') {
	$strRelDescStrokeProb.=', '.$_REQUEST['rel_other_relDescStrokeProb'];
}

$desc_r = $_REQUEST["elem_desc_r"];								//diabetic
if(is_array($desc_r) == true){
	$strDesc_r = implode(", ", $desc_r);
}
else{
	$strDesc_r = $desc_r;
}
if((preg_match("/\bOther\b/i", $strDesc_r) || preg_match("/\b Other\b/i", $strDesc_r)) && $_REQUEST['rel_other_elem_desc_r']!='') {
	$strDesc_r.=', '.$_REQUEST['rel_other_elem_desc_r'];
}

$relDescUlcersProb = $_REQUEST['relDescUlcersProb'];			//Ulcers
if(is_array($relDescUlcersProb) == true){
	$strRelDescUlcersProb = implode(", ", $relDescUlcersProb);
}
else{
	$strRelDescUlcersProb = $relDescUlcersProb;
}
if((preg_match("/\bOther\b/i", $strRelDescUlcersProb) || preg_match("/\b Other\b/i", $strRelDescUlcersProb)) && $_REQUEST['rel_other_relDescUlcersProb']!='') {
	$strRelDescUlcersProb.=', '.$_REQUEST['rel_other_relDescUlcersProb'];
}

$relDescCancerProb = $_REQUEST['relDescCancerProb'];			//Cancer
if(is_array($relDescCancerProb) == true){
	$strRelDescCancerProb = implode(", ", $relDescCancerProb);
}
else{
	$strRelDescCancerProb = $relDescCancerProb;
}
if((preg_match("/\bOther\b/i", $strRelDescCancerProb) || preg_match("/\b Other\b/i", $strRelDescCancerProb)) && $_REQUEST['rel_other_relDescCancerProb']!='') {
	$strRelDescCancerProb.=', '.$_REQUEST['rel_other_relDescCancerProb'];
}

$relDescHeartProb = $_REQUEST['relDescHeartProb'];				//Heart Problem
if(is_array($relDescHeartProb) == true){
	$strRelDescHeartProb = implode(", ", $relDescHeartProb);
}
else{
	$strRelDescHeartProb = $relDescHeartProb;
}
if((preg_match("/\bOther\b/i", $strRelDescHeartProb) || preg_match("/\b Other\b/i", $strRelDescHeartProb)) && $_REQUEST['rel_other_relDescHeartProb']!='') {
	$strRelDescHeartProb.=', '.$_REQUEST['rel_other_relDescHeartProb'];
}

$relDescLungProb = $_REQUEST['relDescLungProb'];				//Lung Problem
if(is_array($relDescLungProb) == true){
	$strRelDescLungProb = implode(", ", $relDescLungProb);
}
else{
	$strRelDescLungProb = $relDescLungProb;
}
if((preg_match("/\bOther\b/i", $strRelDescLungProb) || preg_match("/\b Other\b/i", $strRelDescLungProb)) && $_REQUEST['rel_other_relDescLungProb']!='') {
	$strRelDescLungProb.=', '.$_REQUEST['rel_other_relDescLungProb'];
}

$relDescThyroidProb = $_REQUEST['relDescThyroidProb'];			//Thyroid problem
if(is_array($relDescThyroidProb) == true){
	$strRelDescThyroidProb = implode(", ", $relDescThyroidProb);
}
else{
	$strRelDescThyroidProb = $relDescThyroidProb;
}
if((preg_match("/\bOther\b/i", $strRelDescThyroidProb) || preg_match("/\b Other\b/i", $strRelDescThyroidProb)) && $_REQUEST['rel_other_relDescThyroidProb']!='') {
	$strRelDescThyroidProb.=', '.$_REQUEST['rel_other_relDescThyroidProb'];
}

$relDescLDL = $_REQUEST["relDescLDL"];							//LDL
if(is_array($relDescLDL) == true){
	$strRelDescLDL = implode(", ", $relDescLDL);
}
else{
	$strRelDescLDL = $relDescLDL;
}
if((preg_match("/\bOther\b/i", $strRelDescLDL) || preg_match("/\b Other\b/i", $strRelDescLDL)) && $_REQUEST['rel_other_relDescLDL']!='') {
	$strRelDescLDL.=', '.$_REQUEST['rel_other_relDescLDL'];
}

$ghRelDescOthers = $_REQUEST['ghRelDescOthers'];				//Others
if(is_array($ghRelDescOthers) == true){
	$strGhRelDescOthers = implode(", ", $ghRelDescOthers);
}
else{
	$strGhRelDescOthers = $ghRelDescOthers;
}
if((preg_match("/\bOther\b/i", $strGhRelDescOthers) || preg_match("/\b Other\b/i", $strGhRelDescOthers)) && $_REQUEST['rel_other_ghRelDescOthers']!='') {
	$strGhRelDescOthers.=', '.$_REQUEST['rel_other_ghRelDescOthers'];
}

//-----start of setting Diabetes value--------
$diabetes_values = $_REQUEST["rel_text_diabetes_id"];				//diabetes values
if(is_array($diabetes_values) == true){
	$strDiabetes_values = implode(", ", $diabetes_values);
}
else{
	$strDiabetes_values = $diabetes_values;
}
$DV = get_set_pat_rel_values_save($getGHrs["diabetes_values"],$strDiabetes_values,"rel",$delimiter);

//die('dbv='.$DV);
//-----end of setting Diabetes value--------

//conditions descriptions for both(pt and relative)
$relTxtHighBloodPresher = $_REQUEST['relTxtHighBloodPresher'];
$relTxtHighBloodPresher = get_set_pat_rel_values_save($getGHrs["desc_high_bp"],$relTxtHighBloodPresher,"rel",$delimiter);

/*$dbtxt1 = $getGHrs["desc_high_bp"]; //currnet db value for this field.
//die('db='.$dbtxt1);
if(stristr($dbtxt1,$strDivider)){
	list($text1pt,$text1rel) = explode($strDivider,$dbtxt1);
	$relTxtHighBloodPresher = $text1pt.$strDivider.$relTxtHighBloodPresher;
}else{$relTxtHighBloodPresher = $dbtxt1.$strDivider.$relTxtHighBloodPresher;}
//die('DBvalue='.$dbtxt1.'&nbsp;&nbsp;New Value='.$relTxtHighBloodPresher);
*/

$relTxtArthrities = $_REQUEST['relTxtArthrities'];
$relTxtArthrities = get_set_pat_rel_values_save($getGHrs["desc_arthrities"],$relTxtArthrities,"rel",$delimiter);
/*$dbtxt2 = $getGHrs["desc_arthrities"]; //currnet db value for this field.
if(stristr($dbtxt2,$strDivider)){
	list($text2pt,$text2rel) = explode($strDivider,$dbtxt2);
	$relTxtArthrities = $text2pt.$strDivider.$relTxtArthrities;
}else{$relTxtArthrities = $dbtxt2.$strDivider.$relTxtArthrities;}
*/

$relTxtStroke = $_REQUEST['relTxtStroke'];
$relTxtStroke = get_set_pat_rel_values_save($getGHrs["desc_stroke"],$relTxtStroke,"rel",$delimiter);

/*$dbtxt3 = $getGHrs["desc_stroke"]; //currnet db value for this field.
if(stristr($dbtxt3,$strDivider)){
	list($text3pt,$text3rel) = explode($strDivider,$dbtxt3);
	$relTxtStroke = $text3pt.$strDivider.$relTxtStroke;
}else{$relTxtStroke = $dbtxt3.$strDivider.$relTxtStroke;}
*/

$desc_u = $_REQUEST["rel_elem_desc_u"];
$desc_u = get_set_pat_rel_values_save($getGHrs["desc_u"],$desc_u,"rel",$delimiter);
/*
$dbtxt4 = $getGHrs["desc_u"]; //currnet db value for this field.
if(stristr($dbtxt4,$strDivider)){
	list($text4pt,$text4rel) = explode($strDivider,$dbtxt4);
	$desc_u = $text4pt.$strDivider.$desc_u;
}else{$desc_u = $dbtxt4.$strDivider.$desc_u;}
*/

$relTxtUlcers = $_REQUEST['relTxtUlcers'];
$relTxtUlcers = get_set_pat_rel_values_save($getGHrs["desc_ulcers"],$relTxtUlcers,"rel",$delimiter);
/*
$dbtxt5 = $getGHrs["desc_ulcers"]; //currnet db value for this field.
if(stristr($dbtxt5,$strDivider)){
	list($text5pt,$text5rel) = explode($strDivider,$dbtxt5);
	$relTxtUlcers = $text5pt.$strDivider.$relTxtUlcers;
}else{$relTxtUlcers = $dbtxt5.$strDivider.$relTxtUlcers;}
*/

$relTxtCancer = $_REQUEST['relTxtCancer'];
$relTxtCancer = get_set_pat_rel_values_save($getGHrs["desc_cancer"],$relTxtCancer,"rel",$delimiter);
/*
$dbtxt6 = $getGHrs["desc_cancer"]; //currnet db value for this field.
if(stristr($dbtxt6,$strDivider)){
	list($text6pt,$text6rel) = explode($strDivider,$dbtxt6);
	$relTxtCancer = $text6pt.$strDivider.$relTxtCancer;
}else{$relTxtCancer = $dbtxt6.$strDivider.$relTxtCancer;}
*/

$relTxtHeartProblem = $_REQUEST['relTxtHeartProblem'];
$relTxtHeartProblem = get_set_pat_rel_values_save($getGHrs["desc_heart_problem"],$relTxtHeartProblem,"rel",$delimiter);
/*
$dbtxt7 = $getGHrs["desc_heart_problem"]; //currnet db value for this field.
if(stristr($dbtxt7,$strDivider)){
	list($text7pt,$text7rel) = explode($strDivider,$dbtxt7);
	$relTxtHeartProblem = $text7pt.$strDivider.$relTxtHeartProblem;
}else{$relTxtHeartProblem = $dbtxt7.$strDivider.$relTxtHeartProblem;}
*/

$relTxtLungProblem = $_REQUEST['relTxtLungProblem'];
$relTxtLungProblem = get_set_pat_rel_values_save($getGHrs["desc_lung_problem"],$relTxtLungProblem,"rel",$delimiter);
/*
$dbtxt8 = $getGHrs["desc_lung_problem"]; //currnet db value for this field.
if(stristr($dbtxt8,$strDivider)){
	list($text8pt,$text8rel) = explode($strDivider,$dbtxt8);
	$relTxtLungProblem = $text8pt.$strDivider.$relTxtLungProblem;
}else{$relTxtLungProblem = $dbtxt8.$strDivider.$relTxtLungProblem;}
*/

$relTxtThyroidProblems = $_REQUEST['relTxtThyroidProblems'];
$relTxtThyroidProblems = get_set_pat_rel_values_save($getGHrs["desc_thyroid_problems"],$relTxtThyroidProblems,"rel",$delimiter);
/*
$dbtxt9 = $getGHrs["desc_thyroid_problems"]; //currnet db value for this field.
if(stristr($dbtxt9,$strDivider)){
	list($text9pt,$text9rel) = explode($strDivider,$dbtxt9);
	$txtThyroidProblem = $text9pt.$strDivider.$txtThyroidProblem;
}else{$txtThyroidProblem = $dbtxt9.$strDivider.$txtThyroidProblem;}
*/
$reltxtLDL = $_REQUEST["reltxtLDL"];
$reltxtLDL = get_set_pat_rel_values_save($getGHrs["desc_LDL"],$reltxtLDL,"rel",$delimiter);
/*
$dbtxt10 = $getGHrs["desc_LDL"]; //currnet db value for this field.
if(stristr($dbtxt10,$strDivider)){
	list($text10pt,$text10rel) = explode($strDivider,$dbtxt10);
	$reltxtLDL = $text10pt.$strDivider.$reltxtLDL;
}else{$reltxtLDL = $dbtxt10.$strDivider.$reltxtLDL;}
*/

$rel_any_conditions_others1 = $_REQUEST["rel_any_conditions_others1"];
$rel_any_conditions_others1 = get_set_pat_rel_values_save($getGHrs["any_conditions_others"],$rel_any_conditions_others1,"rel",$delimiter);
/*
$dbtxt11 = $getGHrs["any_conditions_others"]; //currnet db value for this field.
if(stristr($dbtxt11,$strDivider)){
	list($text11pt,$text11rel) = explode($strDivider,$dbtxt11);
	$rel_any_conditions_others1 = $text11pt.$strDivider.$rel_any_conditions_others1;
}else{$rel_any_conditions_others1 = $dbtxt11.$strDivider.$rel_any_conditions_others1;}
*/

//insert / update general health
$check_data1 = "select general_id from general_medicine where patient_id = '".$pid."'";
$checkSql1 = imw_query($check_data1) or die (imw_error());
$checkrows1 = imw_num_rows($checkSql1);

if($checkrows1>0){			
	// update
	$row = imw_fetch_array($checkSql1);		
	$newGeneralMedicineId =  $row['general_id'];		//for audit
	$generalsaveqry = "update general_medicine set ";
	$generalsaveqry .= " patient_id = '".$pid."' ";
}else{			
	// insert new
	$generalsaveqry = "insert into general_medicine set ";					
	$generalsaveqry .= " patient_id = '".$pid."' ";
}		

//$generalsaveqry.= " ,med_doctor = '".$med_doctor."' ";
//$generalsaveqry.= " ,genMedComments='".$genMedComments."' ";
//$generalsaveqry.= " ,any_conditions_you='".$any_conditions_u_arr1."' ";
$generalsaveqry.= " ,any_conditions_relative='".imw_real_escape_string(htmlentities($any_conditions_relative_arr1))."' ";
$generalsaveqry.= " ,any_conditions_others_both='".imw_real_escape_string(htmlentities($OtherChkVal))."' ";
$generalsaveqry.= " ,sub_conditions_you='".imw_real_escape_string(htmlentities($SubConYou))."' ";

$generalsaveqry.= " ,chk_under_control='".imw_real_escape_string(htmlentities($ChkUC))."' ";

$generalsaveqry.= " ,relDescHighBp='".imw_real_escape_string(htmlentities($strRelDescHighBp))."' ";
$generalsaveqry.= " ,relDescArthritisProb='".imw_real_escape_string(htmlentities($strRelDescArthritisProb))."' ";
$generalsaveqry.= " ,relDescStrokeProb='".imw_real_escape_string(htmlentities($strRelDescStrokeProb))."' ";
$generalsaveqry.= " ,desc_r='".imw_real_escape_string(htmlentities($strDesc_r))."' ";
$generalsaveqry.= " ,relDescUlcersProb='".imw_real_escape_string(htmlentities($strRelDescUlcersProb))."' ";
$generalsaveqry.= " ,relDescCancerProb='".imw_real_escape_string(htmlentities($strRelDescCancerProb))."' ";
$generalsaveqry.= " ,relDescHeartProb='".imw_real_escape_string(htmlentities($strRelDescHeartProb))."' ";
$generalsaveqry.= " ,relDescLungProb='".imw_real_escape_string(htmlentities($strRelDescLungProb))."' ";
$generalsaveqry.= " ,relDescThyroidProb='".imw_real_escape_string(htmlentities($strRelDescThyroidProb))."' ";
$generalsaveqry.= " ,relDescLDL = '".imw_real_escape_string(htmlentities($strRelDescLDL))."' ";
$generalsaveqry.= " ,ghRelDescOthers='".imw_real_escape_string(htmlentities($strGhRelDescOthers))."' ";

$generalsaveqry.= " ,desc_high_bp = '".imw_real_escape_string(htmlentities($relTxtHighBloodPresher))."' ";
$generalsaveqry.= " ,desc_arthrities = '".imw_real_escape_string(htmlentities($relTxtArthrities))."' ";
$generalsaveqry.= " ,desc_stroke = '".imw_real_escape_string(htmlentities($relTxtStroke))."' ";
$generalsaveqry.= " ,desc_u='".imw_real_escape_string(htmlentities($desc_u))."' ";
$generalsaveqry.= " ,desc_ulcers = '".imw_real_escape_string(htmlentities($relTxtUlcers))."' ";
$generalsaveqry.= " ,desc_cancer = '".imw_real_escape_string(htmlentities($relTxtCancer))."' ";
$generalsaveqry.= " ,desc_heart_problem = '".imw_real_escape_string(htmlentities($relTxtHeartProblem))."' ";
$generalsaveqry.= " ,desc_lung_problem = '".imw_real_escape_string(htmlentities($relTxtLungProblem))."' ";
$generalsaveqry.= " ,desc_thyroid_problems = '".imw_real_escape_string(htmlentities($relTxtThyroidProblems))."'";
$generalsaveqry.= " ,desc_LDL= '".imw_real_escape_string(htmlentities($reltxtLDL))."' ";
$generalsaveqry.= " ,any_conditions_others='".imw_real_escape_string(htmlentities($rel_any_conditions_others1))."' ";
if($_REQUEST['cbkMasterFamCon'] != "no"){
	$_REQUEST['cbkMasterFamCon'] = "yes";
}
$generalsaveqry.= " ,cbk_master_fam_con='".imw_real_escape_string(htmlentities($_REQUEST['cbkMasterFamCon']))."' ";
$generalsaveqry.= " ,any_conditions_relative1_n='".imw_real_escape_string(htmlentities($anyConditionsRelativeN))."' ";
$generalsaveqry.= " ,any_conditions_others_rel_n='".imw_real_escape_string(htmlentities($_REQUEST["any_conditions_others_rel_n"]))."' ";
$generalsaveqry.= " ,diabetes_values='".imw_real_escape_string(htmlentities($DV))."' ";
//$generalsaveqry.= " ,chk_annual_colorectal_cancer_screenings = '".$chk_annual_colorectal_cancer_screenings."' ";
//$generalsaveqry.= " ,chk_receiving_annual_mammogram = '".$chk_receiving_annual_mammogram."' ";
//$generalsaveqry.= " ,chk_received_flu_vaccine = '".$chk_received_flu_vaccine."' ";
//$generalsaveqry.= " ,chk_high_risk_for_cardiac = '".$chk_high_risk_for_cardiac."' ";


//$generalsaveqry.= " ,negChkBx = '".$negChkBxArr."' ";

//$generalsaveqry.= " ,review_const='".$review_const_arr."' ";
//$generalsaveqry.= " ,review_const_others='".$review_const_others."' ";

//$generalsaveqry.= " ,review_head='".$review_head_arr."' ";
//$generalsaveqry.= " ,review_head_others='".$review_head_others."' ";

//$generalsaveqry.= " ,review_resp='".$review_resp_arr."' ";
//$generalsaveqry.= " ,review_resp_others='".$review_resp_others."' ";

//$generalsaveqry.= " ,review_card='".$review_card_arr."' ";
//$generalsaveqry.= " ,review_card_others='".$review_card_others."' ";

//$generalsaveqry.= " ,review_gastro='".$review_gastro_arr."' ";
//$generalsaveqry.= " ,review_gastro_others='".$review_gastro_others."' ";

//$generalsaveqry.= " ,review_genit='".$review_genit_arr."' ";
//$generalsaveqry.= " ,review_genit_others='".$review_genit_others."' ";

//$generalsaveqry.= " ,review_aller='".$review_aller_arr."' ";
//$generalsaveqry.= " ,review_aller_others='".$review_aller_others."' ";

//$generalsaveqry.= " ,review_neuro='".$review_neuro_arr."' ";
//$generalsaveqry.= " ,review_neuro_others='".$review_neuro_others."' ";

if($checkrows1>0){
	$generalsaveqry .= " where patient_id='".$pid."' ";
}
//echo $generalsaveqry; die;
$generalsaveSql = imw_query($generalsaveqry);

if($checkrows1==0){			
	$newGeneralMedicineId = imw_insert_id();		//for audit
}
if(!$generalsaveSql){
	echo ("Error : ". imw_error()."<br>".$generalsaveSql);
	$generalMedicineError = "Error : ". imw_errno() . ": " . imw_error();
}
$_REQUEST['any_conditions_relative1'] = $any_conditions_relative_arr1;
$_REQUEST['any_conditions_relative1_n'] = $anyConditionsRelativeN;
$_REQUEST['any_conditions_others_rel'] = $OtherChkValForReviwed;
if($elemSubConditionU1ForReviwed == $delimiter){
	$elemSubConditionU1ForReviwed = '';
}
$_REQUEST['rel_elem_subCondition_u1'] = $elemSubConditionU1ForReviwed;
?>