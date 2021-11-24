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
require_once($GLOBALS['srcdir']."/classes/medical_hx/ocular.class.php");
include_once $GLOBALS['srcdir'].'/classes/class.cls_review_med_hx.php';
$ocular = new Ocular($_REQUEST['curr_tab']);
$cls_review  = new CLSReviewMedHx();
$library_path = $GLOBALS['webroot'].'/library';
$patient_id = $ocular->patient_id;
$delimiter = $ocular->delimiter;
extract($_REQUEST);

$arr_info_alert = array();
if(isset($_REQUEST["info_alert"]) && count($_REQUEST["info_alert"]) > 0){
	$arr_info_alert = unserialize(urldecode($_REQUEST["info_alert"]));
}

//getting policy status
$policyStatus = $_REQUEST["policyStatus"];
if($policyStatus == 1){
	$arrAuditTrail = array();
	$arrAuditTrail = unserialize(urldecode($_REQUEST["hidDataMedicalHistory_Ocular"]));
}
//geting from request serialize hid_arr_review_ocular - start
$arrReview_Ocular = array();
$arrReview_Ocular = unserialize(urldecode($_REQUEST["hid_arr_review_ocular"]));
//geting from request serialize hid_arr_review_ocular - end

//setting db elements
//Do you wear
$u_wear = $_REQUEST["u_wear"];

//Last eye exam date
$exam_date_new = getDateFormatDB($exam_date);

//Eye Problems - Please check any of the problems you have 
if($eye_problem != ""){
	$eye_problem_arr = ",";
	$eye_problem_arr.= implode(",",$eye_problem);
	$eye_problem_arr.= ",";
}
$eye_problem_other = $_REQUEST["eye_problem_other"];

//Please mark any condition you or blood relative have presently or have had in the past
$strSep = "~!!~~";
$strSep2 = ":*:";
if($any_conditions_u != ""){
	$any_conditions_u_arr = ",";
	$any_conditions_u_arr.= implode(",",$any_conditions_u);
	$any_conditions_u_arr.= ",";

	//description 	
	$strDesc = "";
	if(count($any_conditions_u) > 0){
		foreach( $any_conditions_u as $key => $val  ){

			$_POST["elem_chronicDesc_".$val] = xss_rem($_POST["elem_chronicDesc_".$val], 4, 'sanitize');	/* Sanitization to prevent arbitrary values - Security Fixes */
			
			$strDesc.="".$val.$strSep2.$_POST["elem_chronicDesc_".$val].$strSep;
		}
	}
}
if(!empty($_REQUEST["any_conditions_other_u"])){
	$any_conditions_other_u = $_REQUEST["any_conditions_other_u"];

	$_POST["elem_chronicDesc_other"] = xss_rem($_POST["elem_chronicDesc_other"], 4, 'sanitize');	/* Sanitization to prevent arbitrary values - Security Fixes */
	$strDesc.= ""."other".$strSep2.$_POST["elem_chronicDesc_other"].$strSep;
}
$anyConditionsUArrForReviwed = $any_conditions_u_arr;
$strDescForReviwedForPat = $strDesc;

$query = "select OtherDesc,chronicDesc from ocular where patient_id='".$patient_id."'";
$sql = imw_query($query);
$tempOtDescRS = imw_fetch_assoc($sql);

$OtherDesc = $_REQUEST['OtherDesc'];
$OtherDesc = get_set_pat_rel_values_save($tempOtDescRS["OtherDesc"],$OtherDesc,"pat",$delimiter);

 
$strDesc = get_set_pat_rel_values_save($tempOtDescRS["chronicDesc"],$strDesc,"pat",$delimiter);
$any_conditions_u_arr = get_set_pat_rel_values_save($tempOtDescRS["any_conditions_you"],$any_conditions_u_arr,"pat",$delimiter);

if($patient_id != "")
{
//insert / update
$check_data = "select ocular_id from ocular where patient_id = '".$patient_id."' LIMIT 1";
$checkSql = imw_query($check_data);
$checkrows = imw_num_rows($checkSql);
if($checkrows>0)
{			
	// update		
	$row = imw_fetch_array($checkSql);		
	$newOculerId = $row['ocular_id'];		
	$ocularsaveqry = "update ocular set ";
	//$ocularsaveqry .= " patient_id='".$patient_id."' ";
}else{			
	// insert new
	$ocularsaveqry = "insert into ocular set ";					
	$ocularsaveqry .= " patient_id='".$patient_id."', ";
}		
$ocularsaveqry.= " you_wear='$u_wear' ";
$ocularsaveqry.= " ,last_exam_date='".$exam_date_new."' ";
$ocularsaveqry.= " ,eye_problems='".$eye_problem_arr."' ";
$ocularsaveqry.= " ,eye_problems_other='".imw_real_escape_string(htmlentities($eye_problem_other))."' ";
$ocularsaveqry.= " ,any_conditions_you = '".imw_real_escape_string($any_conditions_u_arr)."' ";
$ocularsaveqry.= " ,any_conditions_others_you = '".imw_real_escape_string(htmlentities($any_conditions_other_u))."' ";

$ocularsaveqry.= " ,OtherDesc = '".imw_real_escape_string(htmlentities($OtherDesc))."' ";
$ocularsaveqry.= " ,chronicDesc = '".imw_real_escape_string(htmlentities($strDesc))."' ";

if($checkrows>0){
	$ocularsaveqry .= " where patient_id = '".$patient_id."' ";
}

$saveSqlocular = imw_query($ocularsaveqry);
if($checkrows==0)
{			
	$newOculerId = imw_insert_id();
	if($newOculerId>0)
	{
		$query = "select * from ocular where ocular_id='".$newOculerId."' LIMIT 1";
		$sql = imw_query($query);
		$result = imw_fetch_assoc($medSql);
		$arrReview_Ocular = array();
		$arrReview_Ocular = $cls_review->getReviewArrayOcular($result,'','',$_SESSION['authId'],'add');
	}
}

require_once(getcwd()."/../family_hx/inc_save_occular_relatives.php");
}
//START INSERT data to patient_custom_field and Specialty Question TABLE Ocular
if($patient_id != "")
{
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
					$customQry .= "created_date_time = NOW(),";	
				}elseif($arrPatientControlId[$patientControlKey]){
					$customQry = "update patient_custom_field set "; 
					$customQry .= "modified_by = '".$createdBy."',";
					$customQry .= "modified_date_time = NOW(),";	
					$queryUpdate = " where id = '".$arrPatientControlId[$patientControlKey]."'";
				}	
				$customQry .= "patient_id = '$patient_id',";
				$customQry .= "admin_control_id = '".imw_real_escape_string($control[1])."',";
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
	if((isset($_REQUEST["totQuestion"]) == true) && (empty($_REQUEST["totQuestion"]) == false))
	{
		$newPatSplQueAnsID = 0;
		$intTotQuestion = 0;
		$patientId = $patient_id;
		$currentOprator = $_SESSION['authId'];
		$intTotQuestion = $_REQUEST["totQuestion"];
		for($i = 0; $i < $intTotQuestion; $i++)
		{
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
			
			$qryChkRecord = "select id from patient_specialty_question_answer where 
								specialty_id = '".$intSplId."' and 
								question_id = '".$intQueId."' and 
								patient_id = '".$patientId."' and 
								med_hx_tab = 'Ocular' and 
								row_del_status = '0' LIMIT 1 ";
			$rsChkRecord = imw_query($qryChkRecord);
			if(imw_num_rows($rsChkRecord) > 0){
				$intPatSplQueId = 0;
				$rowChkRecord = imw_fetch_row($rsChkRecord);
				$intPatSplQueId = $rowChkRecord[0];
				//Collecting this question answer options for current patient - start
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
										med_hx_tab = 'Ocular', 
										ans_type = '".$intAnsType."' 
										where id = '".$intPatSplQueId."'";
						$rsUpdate = imw_query($qryUpdate);
					}
					elseif($intAnsType == 1){
						$qryUpdate = "update patient_specialty_question_answer set row_modify_by = '".$currentOprator."', 
										row_modify_date_time = NOW(),
										pat_provider = '".$_REQUEST["patProviderID"]."', 
										med_hx_tab = 'Ocular', 
										ans_type = '".$intAnsType."' 
										where id = '".$intPatSplQueId."'";
						$rsUpdate = imw_query($qryUpdate);
						
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
						echo $qryUpdatePatSplQueOpAnsMakeDeactive = "update patient_specialty_question_options_answer set state = '0' where id IN(".$strPatQueAnsID.") ";
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
									('".$intSplId."', '".$intQueId."', '".$patientId."', '".$_REQUEST["patProviderID"]."', '".$intAnsType."', '".$strAnswer."', '".$currentOprator."', NOW(), 'Ocular')";
					$rsInsert = imw_query($qryInsert);
				}
				elseif(($intAnsType == 1) && (count($arrTxtAreaAnswerOption) > 0)){
					$qryInsert = "insert into patient_specialty_question_answer (specialty_id, question_id, patient_id, pat_provider, ans_type, pat_answer, row_created_by, row_created_date_time, med_hx_tab) values 
									('".$intSplId."', '".$intQueId."', '".$patientId."', '".$_REQUEST["patProviderID"]."', '".$intAnsType."', '".$strAnswer."', '".$currentOprator."', NOW(), 'Ocular')";
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
//END INSERT data to patient_custom_field and Specialty Question TABLE Ocular
			
$_REQUEST['exam_date'] = $exam_date_new;
$_REQUEST['eye_problem'] = $eye_problem_arr;
$_REQUEST['any_conditions_u'] = $anyConditionsUArrForReviwed;
$_REQUEST['any_conditions_u1'] = $any_conditions_u_arr1;
$_REQUEST['any_conditions_others_both'] = $any_conditions_others_both_arr;

if($policyStatus == 1){
	//  Audit Functionality
	foreach ((array)$arrAuditTrail as $key => $value) {
		if(trim($arrAuditTrail[$key]['Filed_Label']) == "elem_chronicDesc"){
			$arrAuditTrail [$key]["New_Value"] = $strDescForReviwedForPat;
		}
		if(trim($arrAuditTrail[$key]['Filed_Label']) == "elem_chronicRelative"){
			$arrAuditTrail [$key]["New_Value"] = $strRelative;
		}
		if(trim($arrAuditTrail [$key]["Table_Name"]) == "ocular"){
			if (array_key_exists('Pk_Id', $arrAuditTrail[$key])) {
				if(empty($arrAuditTrail [$key]["Pk_Id"]) && $arrAuditTrail [$key]["Pk_Id"] == ""){
					$arrAuditTrail [$key]["Pk_Id"] = $newOculerId;
					$arrAuditTrail [$key]["Action"] = "add";
					$arrAuditTrail [$key]["pid"] = $patient_id;
				}
			}
		}
	}
	$table = array("ocular");
	$error = array($oculerError);
	$mergedArray = merging_array($table,$error);
	auditTrail($arrAuditTrail,$mergedArray,0,0,0);	
}
//policy status

//making review in database - start
foreach ((array)$arrReview_Ocular as $key => $value) {
	switch (trim($arrReview_Ocular[$key]['UI_Filed_Name'])):
		case "elem_chronicDesc":
			$arrReview_Ocular[$key]["New_Value"] = trim(html_entity_decode($strDescForReviwedForPat));
			break;
		case "elem_chronicRelative":
			$arrReview_Ocular[$key]["New_Value"] = trim(html_entity_decode($strRelative));
			break;
		case "eye_problem_other_check":
			if($_REQUEST['eye_problem_other'] != ""){				
				$arrReview_Ocular[$key]["New_Value"] = "yes";
			}
			else{
				$arrReview_Ocular[$key]["New_Value"] = "no";
			}
			break;
		case "rel_elem_chronicDesc":
			$arrReview_Ocular[$key]["New_Value"] = trim(html_entity_decode($strDescForReviwedForRel));
			break;	
		case "elem_chronicRelative":
			$arrReview_Ocular[$key]["New_Value"] = trim(html_entity_decode($strRelativeForReviwed));
			break;		
	endswitch;
}
$cls_review->reviewMedHx($arrReview_Ocular,$_SESSION['authId'],"Ocular Hx",$patient_id,0,0);

//making review in database - end
//redirecting...
$curr_tab = xss_rem($_REQUEST["curr_tab"]);
$next_tab = xss_rem($_REQUEST["next_tab"]);
$next_dir = xss_rem($_REQUEST["next_dir"]);
if($next_tab != ""){
	$curr_tab = $next_tab;
}
// Remove Remote Server Sync Code
?>
<script type="text/javascript">
	var curr_tab = '<?php echo xss_rem($curr_tab); ?>';	
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
	//top.reset_toolbar_icons('<?php echo $patient_id;?>');
	top.fmain.location.href = '../index.php?showpage='+curr_tab;	
	top.show_loading_image("hide");
</script>
