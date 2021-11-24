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

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");

header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");

include_once("../../../config/globals.php");
require_once(dirname(__FILE__)."/../../../library/classes/SaveFile.php");
$library_path = $GLOBALS['webroot'].'/library';
$patient_id = $_SESSION['patient'];
$updir=substr(data_path(), 0, -1);
$srcDir = substr(data_path(1), 0, -1);
$oSaveFile = new SaveFile($patient_id);
$oSaveFile->ptDir("consent_forms");
$oSaveFileUser = new SaveFile();

$enable_sig_web = enable_web_sig_pad();
?>
<style>
.consentObjectAfterSign{ border:solid 1px; border-color:#99CC33; }
.consentObjectBeforSign{ border:solid 1px; border-color:#FF9900}
</style>
<?php
$health_Appt = $_REQUEST['health_Appt'];
$apptIdList = $_REQUEST['apptIdList'];
$curentDate = date("Y-m-d");	
$saveDone = false;
$Global_date_Format = phpDateFormat();
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
function getSigImage($postDataWit,$pth="",$rootPath){
	global $webServerRootDirectoryName;
	$rootPath = $webServerRootDirectoryName;//THIS PARAMETER HOLD LOCAL ADDRESS OF SERVER i.e. "/var/www/html";
	$output = shell_exec("java -cp .:".$rootPath."SigPlusLinuxHSB/SigPlus.jar:".$rootPath."SigPlusLinuxHSB/RXTXcomm.jar:".$rootPath."SigPlusLinuxHSB SigPlusImgDemoV2 ".$postDataWit." ".$rootPath."SigPlusLinuxHSB/sig.jpg 2>&1 ");
	@copy($rootPath."SigPlusLinuxHSB/sig.jpg",$pth);
	
}
if($_REQUEST['savePreOpHealthQues'] == 1 && $health_Appt!='changeAppt'){
	
	$heartTrouble = $_REQUEST['chbx_ht'];
	$stroke = $_REQUEST['chbx_sht'];
	$HighBP = $_REQUEST['chbx_HighBP'];
	$anticoagulationTherapy = $_REQUEST['chbx_anti_thrp'];
	$asthma = $_REQUEST['chbx_ast_slp'];
	$tuberculosis = $_REQUEST['chbx_tuber'];
	$diabetes = $_REQUEST['chbx_diab'];
	$insulinDependence = $_REQUEST['chbx_subdiab'];
	$epilepsy = $_REQUEST['chbx_epile'];	
	$restlessLegSyndrome = $_REQUEST['chbx_restless'];
	$hepatitis = $_REQUEST['chbx_hepat'];
	$hepatitisA = $_REQUEST['HepatitisA'];
	$hepatitisB = $_REQUEST['HepatitisB'];
	$hepatitisC = $_REQUEST['HepatitisC'];
	$kidneyDisease = $_REQUEST['chbx_kidn'];
	$shunt = $_REQUEST['chbx_subkidnShunt'];
	$fistula = $_REQUEST['chbx_subkidnFistula'];	
	$hivAutoimmuneDiseases = $_REQUEST['chbx_hiv_auto'];
	$hivTextArea = addslashes($_REQUEST['hivTextArea']);
	$cancerHistory = $_REQUEST['chbx_hist_can'];
	$cancerHistoryDesc = addslashes($_REQUEST['cancerHistory']);
	$brest_cancer = $_REQUEST['brest_cancer'];
	$organTransplant = $_REQUEST['chbx_org_trns'];
	$organTransplantDesc = addslashes($_REQUEST['organTransDesc']);
	$anesthesiaBadReaction = $_REQUEST['chbx_bad_react'];	
	$otherTroubles = addslashes($_REQUEST['otherTroubles']);
	//$allergies_status = $_REQUEST['chbx_drug_react']; //NOW READ FROM PATIENT-INFO->MEDICAL HISTORY
	$allergies_status_reviewed = $_REQUEST['chbx_drug_react_reviewed'];
	$walker = $_REQUEST['chbx_use_wheel'];
	$contactLenses = $_REQUEST['chbx_wear_cont'];
	$smoke = $_REQUEST['chbx_smoke'];
	$smokeHowMuch = addslashes($_REQUEST['smokeHowMuch']);
	$smokeAdvise = addslashes($_REQUEST['smokeAdvise']);
	if($smoke != 'Yes') {
		$smokeAdvise = '';
	}
	$alchohol = $_REQUEST['chbx_drink'];	
	$alchoholHowMuch = addslashes($_REQUEST['alchoholHowMuch']);
	$alchoholAdvise = addslashes($_REQUEST['alchoholAdvise']);
	if($alchohol != 'Yes') {
		$alchoholAdvise = '';
	}
	$autoInternalDefibrillator = $_REQUEST['chbx_hav_auto_int'];
	$metalProsthetics = $_REQUEST['chbx_hav_any_met'];
	$notes = addslashes($_REQUEST['notesDesc']);	
	$emergencyContactPerson = addslashes($_REQUEST['emergencyContactPerson']);
	$emergencyContactPhone = addslashes($_REQUEST['emergencyContactTel']);
	$witnessname = addslashes($_REQUEST['witnessname']);
	$dateQuestionnaire = addslashes($_REQUEST['date']);

	
	//By Karan
	if($Global_date_Format == "d-m-Y")
	{
		list($day, $month, $year) = explode('-',$dateQuestionnaire);
	}
	else
	{
		list($month, $day, $year) = explode('-',$dateQuestionnaire);
	}
	
	//list($month, $day, $year) = split('-',$dateQuestionnaire);
	$dateQuestionnaire = $year."-".$month."-".$day;
	
	if(($heartTrouble!='') && ($stroke!='') 
		&& ($HighBP!='') && ($anticoagulationTherapy!='') 
		&& ($asthma!='') && ($tuberculosis!='')
		&& ($diabetes!='') && ($epilepsy!='')
		&& ($restlessLegSyndrome!='') && ($hepatitis!='')  
		&& ($kidneyDisease!='') && ($hivAutoimmuneDiseases!='') 
		&& ($cancerHistory!='') //&& ($_POST['brest_cancer']!='') 
		&& ($organTransplant !='') && ($anesthesiaBadReaction !='') 
		&& ($walker !='') && ($contactLenses!='') 
		&& ($smoke!='') &&($alchohol!='') 
		&& ($autoInternalDefibrillator!='') && ($metalProsthetics!='') 
		&& ($emergencyContactPerson!='') && ($emergencyContactPhone!='') 
		//&& ($elem_signature!='') 
		&& ($witnessname!='')
		//&& ($witnSign!='')  
		//&& ($chk_signNurseId<>"0")
	  ) {
		$formStatus = 'completed';
	
	}else {
		$formStatus='not completed';
	}
	
	//START SAVE SIGNATURE
	$saveSigQry ="";
	for($ps=1;$ps<=2;$ps++){	
		$postSigData='';
		$ptWtHealthSrc='';
		$postSigDataPtImgSavePath='';
		$postSigDataWtImgSavePath='';
		
		if($ps==1) { 
			$postSigData = $_REQUEST['SigDataPt']; 
			$postSigDataLoadValue=$_REQUEST['SigDataPtLoadValue'];
		}else if($ps==2) { 
			$postSigData = $_REQUEST['SigDataWt'];
			$postSigDataLoadValue=$_REQUEST['SigDataWtLoadValue']; 
		}
		if(!$postSigData  || $postSigData=='undefined') { 
			if((!$postSigDataLoadValue && !$_REQUEST['hidden_patient_sign_image_path']) && $ps==1) { 
				$formStatus='not completed';
			}
		}
	
		if($postSigData != '' && $postSigData != '000000000000000000000000000000000000000000000000000000000000000000000000'  && $postSigData !='undefined') {
			$ptWtHealthSrc = 'surgery_sign_'.$patient_id.'_'.date('d_m_y_h_i_s').'_'.$ps.'.jpg';
			if(class_exists("COM") && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
				$aConn = new COM("SIGPLUS.SigPlusCtrl.1");
				$aConn->InitSigPlus();
				$aConn->SigCompressionMode = 2;
				$aConn->SigString=$postSigData;
				$aConn->ImageFileFormat = 4; //4=jpg, 0=bmp, 6=tif
				$aConn->ImageXSize = 150; //width of resuting image in pixels
				$aConn->ImageYSize =65; //height of resulting image in pixels
				
				$aConn->ImagePenWidth = 11; //thickness of ink in pixels
				$aConn->JustifyMode = 5;  //center and fit signature to size
				
		
				//ALSO SAVE IMAGE FOR PDF
				$pathPDF = data_path().'PatientId_'.$patient_id.'/surgery_sign_'.$patient_id.'_'.date('d_m_y_h_i_s').'_'.$ps.'.jpg';
				$aConn->WriteImageFile("$pathPDF");
				//ALSO SAVE IMAGE FOR PDF			
			}else {
				$patNewPath = data_path().'PatientId_'.$patient_id.'/'.$ptWtHealthSrc;	
				getSigImage($postSigData,$patNewPath,$rootServerPath);
			}
			if($ps=='1') { 				
				$postSigDataPtImgSavePath = 'PatientId_'.$patient_id.'/'.$ptWtHealthSrc; 
				$patient_sign_image_path = addslashes($postSigDataPtImgSavePath);
				$patientSign = addslashes($postSigData);
				$saveSigQry .= "patientSign = '".$patientSign."',
							   patient_sign_image_path = '".$patient_sign_image_path."',
							  ";
			}else if($ps=='2') { 				
				$postSigDataWtImgSavePath = 'PatientId_'.$patient_id.'/'.$ptWtHealthSrc; 
				$witness_sign_image_path = addslashes($postSigDataWtImgSavePath);
				$witnessSign = addslashes($postSigData);
				$saveSigQry .= "witnessSign = '".$witnessSign."',
								witness_sign_image_path = '".$witness_sign_image_path."',
							  ";
			}
			//echo '<br><br>'.$saveSigQry.'<br>';
		}
		
	}		
	//END SAVE SIGNATURE
		
	//START SAVE TOUCH SIGNATURE
	$hidden_patient_sign_image_path = $_REQUEST['hidden_patient_sign_image_path'];
	$hidden_patient_sign_image_path = str_ireplace($srcDir,"",$hidden_patient_sign_image_path);
	if($hidden_patient_sign_image_path) {
		$saveSigQry .= "patient_sign_image_path = '".$hidden_patient_sign_image_path."',
							  ";	
	}
	$hidden_witness_sign_image_path = $_REQUEST['hidden_witness_sign_image_path'];
	$hidden_witness_sign_image_path = str_ireplace($srcDir,"",$hidden_witness_sign_image_path);
	if($hidden_witness_sign_image_path) {
		$saveSigQry .= "witness_sign_image_path = '".$hidden_witness_sign_image_path."',
							  ";	
	}
	//END SAVE TOUCH SIGNATURE
	
	if($preOpHealthQuesId){
		$saveMainQry = "update surgery_center_pre_op_health_ques set ";
	}
	elseif(!$preOpHealthQuesId){
		$saveMainQry = "insert into surgery_center_pre_op_health_ques set ";
	}
	$saveQuery = "form_status = '".$formStatus."',
				heartTrouble = '".$heartTrouble."',
				stroke = '".$stroke."',
				HighBP = '".$HighBP."',				  
				anticoagulationTherapy = '".$anticoagulationTherapy."',
				asthma = '".$asthma."',
				tuberculosis = '".$tuberculosis."',
				diabetes = '".$diabetes."',
				insulinDependence = '".$insulinDependence."',				  
				epilepsy = '".$epilepsy."',
				restlessLegSyndrome = '".$restlessLegSyndrome."',
				hepatitis = '".$hepatitis."',
				hepatitisA = '".$hepatitisA."',
				hepatitisB = '".$hepatitisB."',
				hepatitisC = '".$hepatitisC."',
				kidneyDisease = '".$kidneyDisease."',
				shunt = '".$shunt."',
				fistula = '".$fistula."',
				hivAutoimmuneDiseases = '".$hivAutoimmuneDiseases."',				  
				hivTextArea = '".$hivTextArea."',
				cancerHistory = '".$cancerHistory."',
				cancerHistoryDesc = '".$cancerHistoryDesc."',
				brest_cancer = '".$brest_cancer."',
				organTransplant = '".$organTransplant."',
				organTransplantDesc = '".$organTransplantDesc."',
				anesthesiaBadReaction = '".$anesthesiaBadReaction."',
				otherTroubles = '".$otherTroubles."',
				allergies_status_reviewed = '".$allergies_status_reviewed."',				  
				walker = '".$walker."',
				contactLenses = '".$contactLenses."',
				smoke = '".$smoke."',
				smokeHowMuch = '".$smokeHowMuch."',
				smokeAdvise = '".$smokeAdvise."',
				alchohol = '".$alchohol."',
				alchoholHowMuch = '".$alchoholHowMuch."',
				alchoholAdvise = '".$alchoholAdvise."',
				autoInternalDefibrillator = '".$autoInternalDefibrillator."',				  
				metalProsthetics = '".$metalProsthetics."',
				notes = '".$notes."',
				emergencyContactPerson = '".$emergencyContactPerson."',
				emergencyContactPhone = '".$emergencyContactPhone."',
				witnessname = '".$witnessname."',
				dateQuestionnaire = '".$dateQuestionnaire."',				  
				patient_id = '".$patient_id."',				  
				
				heartTroubleDesc 				= '".addslashes($_POST['heartTroubleDesc'])."',
				strokeDesc 						= '".addslashes($_POST['strokeDesc'])."',
				HighBPDesc 						= '".addslashes($_POST['HighBPDesc'])."',
				anticoagulationTherapyDesc 		= '".addslashes($_POST['anticoagulationTherapyDesc'])."',
				asthmaDesc 						= '".addslashes($_POST['asthmaDesc'])."',
				tuberculosisDesc 				= '".addslashes($_POST['tuberculosisDesc'])."',
				diabetesDesc 					= '".addslashes($_POST['diabetesDesc'])."',
				epilepsyDesc 					= '".addslashes($_POST['epilepsyDesc'])."',
				restlessLegSyndromeDesc 		= '".addslashes($_POST['restlessLegSyndromeDesc'])."',
				hepatitisDesc 					= '".addslashes($_POST['hepatitisDesc'])."',
				kidneyDiseaseDesc 				= '".addslashes($_POST['kidneyDiseaseDesc'])."',
				anesthesiaBadReactionDesc 		= '".addslashes($_POST['anesthesiaBadReactionDesc'])."',
				walkerDesc 						= '".addslashes($_POST['walkerDesc'])."',
				contactLensesDesc 				= '".addslashes($_POST['contactLensesDesc'])."',
				autoInternalDefibrillatorDesc	= '".addslashes($_POST['autoInternalDefibrillatorDesc'])."'
		";
		
		
		if($preOpHealthQuesId){
			$saveMainQry = $saveMainQry.$saveSigQry.$saveQuery." where preOpHealthQuesId = $preOpHealthQuesId";
		}
		else{
			$saveMainQry = $saveMainQry.$saveSigQry.$saveQuery;
		}	
		$rsSaveMainQry = imw_query($saveMainQry);
		if(!$preOpHealthQuesId){
			$latestInsertId = imw_insert_id();
		}else{
			$latestInsertId = $preOpHealthQuesId;
		}
		
		//insertion in iolink_healthquestionadmin
			$questionsDesc[] = $_REQUEST['question'];
			$selectAdminQuestionsQry = "select * from surgery_center_health_questioner";
			$selectAdminQuestions = imw_query($selectAdminQuestionsQry);
			$selectAdminQuestionsRows = imw_num_rows($selectAdminQuestions);
			$i=0;
			while($ResultselectAdminQuestions = imw_fetch_array($selectAdminQuestions)){
				$quId[]=$ResultselectAdminQuestions['question'];
			}
			for($key=1;$key<=$selectAdminQuestionsRows;$key++){
				$adminQuestionArray[] = $_REQUEST['question'.$key];
				//$adminQuestDescArr[] = ($_REQUEST['question'.$key] !="Yes" ? "" : $_REQUEST['adminQuestDescNew'.$key]);
				$inputQuest 	= $_REQUEST['question'.$key];
				$inputQuestDesc	= ($_REQUEST['question'.$key] !="Yes" ? "" : $_REQUEST['adminQuestDesc'.$key]);
				$hlthQuestLabel = $_REQUEST['hlthQuestLabel'.$key];
				$chkQuestRes = imw_query("SELECT * FROM surgery_center_health_question_admin WHERE
						      patient_id='".$patient_id."' AND adminQuestion='".addslashes($hlthQuestLabel)."'");
				if(imw_num_rows($chkQuestRes)>0) {
					$insertQuestionQry = "UPDATE surgery_center_health_question_admin SET 
														adminQuestion='".addslashes($hlthQuestLabel)."',
														adminQuestionStatus='".$inputQuest."',	
														adminQuestionDesc='".addslashes($inputQuestDesc)."',
														patient_id= '".$patient_id."',
														preOpHealthQuesId = '".$latestInsertId."'
														WHERE patient_id='".$patient_id."' AND adminQuestion='".addslashes($hlthQuestLabel)."'
														";
				}else {
					$insertQuestionQry = "insert into surgery_center_health_question_admin set 
														adminQuestion='".addslashes($hlthQuestLabel)."',
														adminQuestionStatus='".$inputQuest."',									
														adminQuestionDesc='".addslashes($inputQuestDesc)."',
														patient_id= '".$patient_id."',
														preOpHealthQuesId = '".$latestInsertId."'
														";
				}
				$insertQuestionQry = imw_query($insertQuestionQry);
			}						

		for($intCountMedNameTextBoxSingle = 0; $intCountMedNameTextBoxSingle < 20; $intCountMedNameTextBoxSingle++){			
			$allergyName = addslashes($_REQUEST["Allergies_quest".$intCountMedNameTextBoxSingle]);
			$allergyReaction = addslashes($_REQUEST["Reaction_quest".$intCountMedNameTextBoxSingle]);
			$allergyIdHidden = $_REQUEST["allergyIdHidden".$intCountMedNameTextBoxSingle];
			$allergyNameHidden = $_REQUEST["allergyNameHidden".$intCountMedNameTextBoxSingle];
			$allergyReactionHidden = $_REQUEST["allergyReactionHidden".$intCountMedNameTextBoxSingle];
			
			if($allergyIdHidden=="" && $allergyNameHidden=="" && $allergyReactionHidden=="" && $allergyName!=""){
				$saveAlleryMain = "insert into surgery_center_patient_allergy set
								   patient_id = '".$patient_id."',
								   allergy_name = '".$allergyName."',
								   reaction_name = '".$allergyReaction."',
								   preOpHealthQuesId = '".$latestInsertId."'
								  ";
				$rsSaveAllery =	imw_query($saveAlleryMain);					  
			}
			else{
				if(!$allergyName) {
					$queryUpdateAllergy = "DELETE FROM surgery_center_patient_allergy WHERE
											patient_id = '$patient_id' and										
											pre_op_allergy_id = '$allergyIdHidden'";
				}else {
					$queryUpdateAllergy = "Update surgery_center_patient_allergy set allergy_name='".addslashes($allergyName)."',
											reaction_name='".addslashes($allergyReaction)."' WHERE
											patient_id = '$patient_id' and										
											pre_op_allergy_id = '$allergyIdHidden'";
				}
				$queryUpdateAllergySts = imw_query($queryUpdateAllergy) or die(imw_error());
			}
		}
		for($intCountMedNameTextBoxSingle = 0; $intCountMedNameTextBoxSingle < 20; $intCountMedNameTextBoxSingle++){			
			$medicationName = addslashes($_REQUEST["medication_name".$intCountMedNameTextBoxSingle]);
			$medicationDosage = addslashes($_REQUEST["medication_dosage".$intCountMedNameTextBoxSingle]);
			$madicationIdHidden = $_REQUEST["medicationIdHidden".$intCountMedNameTextBoxSingle];
			$medicationNameHidden = $_REQUEST["medicationNameHidden".$intCountMedNameTextBoxSingle];
			$medicationDosageHidden = $_REQUEST["medicationDosageHidden".$intCountMedNameTextBoxSingle];
			
			if($madicationIdHidden=="" && $medicationNameHidden=="" && $medicationDosageHidden=="" && $medicationName!=""){
				$saveMedicationMain = "insert into surgery_center_patient_medication set
									   patient_id = '".$patient_id."',
									   prescription_medication_name = '".$medicationName."',
									   prescription_medication_desc = '".$medicationDosage."',
									   preOpHealthQuesId = '".$latestInsertId."'
									  ";
				$rsSavemedication =	imw_query($saveMedicationMain);					  
			}
			else{
				if(!$medicationName) {
					$queryUpdateMedication = "DELETE FROM surgery_center_patient_medication WHERE
												patient_id = '$patient_id' and										
												prescription_medication_id = '$madicationIdHidden'";
				}else {
					$queryUpdateMedication = "Update surgery_center_patient_medication set prescription_medication_name='".addslashes($medicationName)."',
												prescription_medication_desc='".addslashes($medicationDosage)."' WHERE
												patient_id = '$patient_id' and										
												prescription_medication_id = '$madicationIdHidden'";
				}
				$queryUpdateMedicationSts = imw_query($queryUpdateMedication) or die(imw_error());
				
			}
		}
	$saveDone = true;	
}
//START CODE TO GET ALL APPOINTMENTS OF PATIENT
$allApptPtQry  	= "SELECT sa.id,sa.sa_app_start_date FROM schedule_appointments sa 
					INNER JOIN slot_procedures sp on sp.id = sa.procedureid 
					LEFT JOIN users us on us.id = sa.sa_doctor_id 
				   WHERE 
				  	sa.sa_patient_id = '".$patient_id."' AND sa.sa_app_start_date >= '".$curentDate."'
					AND sa_patient_app_status_id NOT IN (203,201,18,19,20) ORDER BY sa_app_start_date ASC ";
$allApptPtRes 	= imw_query($allApptPtQry);
$apptSelectList ='Select Future Appointment Date <select name="apptIdList" class="text_10" id="apptIdList" style="width:100px;" onChange="changeApptFun(this);"><option value="">Select</option>';

if(imw_num_rows($allApptPtRes)>0) {
	$allAppStartDateShow='';
	while($allApptPtRow = imw_fetch_array($allApptPtRes)) {
		$sel			= '';
		$allApptId 		= $allApptPtRow['id'];
		$allAppStartDate= $allApptPtRow['sa_app_start_date'];
		$allAppStartDateShow = get_date_format(date('Y-m-d',strtotime($allAppStartDate)));
		if($allApptId==$apptIdList) { $sel='selected'; }
		$apptSelectList .= '<option value="'.$allApptId.'"  '.$sel.' >'.$allAppStartDateShow.'</option>';
	}
}
$apptSelectList .='</select>';
//END CODE TO GET ALL APPOINTMENTS OF PATIENT

//START GET NKDA STATUS
$nkdaAllergyStatus = "";
$noMedicationStatus = "";
$noMedicationComments = "";
$nkdaAllergyQry = "SELECT no_value, comments FROM commonNoMedicalHistory WHERE patient_id = '".$patient_id."'  AND module_name = 'Allergy' || module_name = 'Medication' ";
$nkdaAllergyRes = imw_query($nkdaAllergyQry) or die(imw_error($nkdaAllergyQry));
if(imw_num_rows($nkdaAllergyRes)>0) {
	while($nkdaAllergyRow = imw_fetch_array($nkdaAllergyRes)) {
		$no_value = $nkdaAllergyRow["no_value"];
		if($no_value == "NoAllergies") {
			$nkdaAllergyStatus = "Yes";	
		}
		if($no_value == "NoMedications") {
			$noMedicationStatus = "Yes";
			$noMedicationComments = $nkdaAllergyRow["comments"];	
		}
	}
}
//END GET NKDA STATUS

$selectQuerey = "select * from surgery_center_pre_op_health_ques where patient_id = $patient_id";
$rsSelectQuerey = imw_query($selectQuerey);
$numRowsSelectQuerey = imw_num_rows($rsSelectQuerey);
if($numRowsSelectQuerey > 0){
	$rowGetSelectQuerey = imw_fetch_array($rsSelectQuerey);
	extract($rowGetSelectQuerey);
}

function print_row($label,$chkBoxValue,$chkBoxName,$txtAreaValue,$txtAreaName,$xtraData = '',$xtraData2 = '')
{
	$html = '';
	$tmpArr = array('Smoke' => 'How much', 'Drink Alcohol' => 'How much', 'Have any Metal Prosthetics' => 'Notes');
	$chkBoxClass = ($chkBoxValue=='No' || $chkBoxValue=='Yes') ? '' : 'checkbox-mandatory';
	$txtRowClass= ($chkBoxValue=='Yes') ? '' : 'hidden';
	$placeholder = (array_key_exists($label,$tmpArr) ? $tmpArr[$label] : 'Describe');
	
	//if($chkBoxValue=='Yes') { echo 'glyphicon-menu-up';}else { echo 'glyphicon-menu-down';}
	
	$html .= '
		<div class="col-xs-9">
			<label>'.$label.'</label>
		</div>
		
		<div class="col-xs-3">
			<div class="row">
				<div class="col-xs-5 text-center">
					<div class="checkbox '.$chkBoxClass.'">
						<input '.($chkBoxValue=='Yes' ? "checked" : '').' name="'.$chkBoxName.'" type="checkbox" value="Yes" id="'.$chkBoxName.'_yes" onChange="javascript:checkSingle(\''.$chkBoxName.'_yes\',\''.$chkBoxName.'\'); changeChbxColor(\''.$chkBoxName.'\'); disp(this,\''.$chkBoxName.'_row\');">
						<label for="'.$chkBoxName.'_yes"></label>
					</div>
				</div>
				
				<div class="col-xs-4 text-center">
					<div class="checkbox '.$chkBoxClass.'">
						<input '.($chkBoxValue=='No' ? "checked" : '').' name="'.$chkBoxName.'" type="checkbox" value="No" id="'.$chkBoxName.'_no" onChange="javascript:checkSingle(\''.$chkBoxName.'_no\',\''.$chkBoxName.'\'); changeChbxColor(\''.$chkBoxName.'\'); disp_none(this,\''.$chkBoxName.'_row\'); ">
						<label for="'.$chkBoxName.'_no"></label>
					</div>
				</div>
				
				<div class="col-xs-3 text-center ">
					<i class="glyphicon '.($chkBoxValue=='Yes' ? "glyphicon-menu-up" : 'glyphicon-menu-down').' pd5 pointer" id="up_dwn_'.$chkBoxName.'_row" onClick="javascript:disp_rev(this,\''.$chkBoxName.'_row\')"></i>
				</div>
					
			</div>
		</div>
		
		<div class="clearfix"></div>  
		
		<div class="col-xs-12 pd5 '.$txtRowClass.'" id="'.$chkBoxName.'_row" style="background-color:#efefef;">
			'.$xtraData.'
			<textarea id="'.$txtAreaName.'" placeholder="'.$placeholder.'" class="form-control" name="'.$txtAreaName.'">'.stripslashes($txtAreaValue).'</textarea>
			'.$xtraData2.'
		</div>';	
	return $html;		
}
?>
<html>
<head>
<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-select.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css" type="text/css" />

<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
<link href="<?php echo $library_path; ?>/css/style.css" rel="stylesheet">

<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
<style>
	textarea { max-height:35px!important; padding:2px !important;}
	label { font-weight:500!important;}
	.text-right label { text-align:left; }
</style>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
<?php if( $enable_sig_web ){ include_once $GLOBALS['srcdir'].'/sigpad/SigWebTablet.php'; } ?>
<script type="text/javascript">
function print_form_health_quest(){
	var information_id ;//= document.getElementById("information_id").value;
	var show_td ;//= document.getElementById("show_td").value;
	var objId ;//= document.getElementById("SigData1");		
	var patientId = '<?php echo $patient_id;?>';
	window.open('../patient_info/surgery_consent_forms/pre_op_health_ques_surgery_print.php?patientId='+patientId,'print_health_form','');
}

function showHiddenTables(obj,id,val,chbxObj){
	var objId = obj;
	
	if(val) {
		document.getElementById(objId).style.display = "none";
		if(document.getElementById(id)){
			document.getElementById(id).src = "../../main/images/block.gif";
		}
		if(chbxObj.checked==true) {
			if(document.getElementById(objId)){
				if(val=='yes') {
					document.getElementById(objId).style.display = "block";
					if(document.getElementById(id)){
						document.getElementById(id).src = "../../main/images/none.gif";
					}
				}else if(val=='no') {
					document.getElementById(objId).style.display = "none";
					if(document.getElementById(id)){
						document.getElementById(id).src = "../../main/images/block.gif";
					}
				}
			}
		}
	}else {
		if(document.getElementById(objId)){
			if(document.getElementById(objId).style.display == "none"){
				document.getElementById(objId).style.display = "block";
				if(document.getElementById(id)){
					document.getElementById(id).src = "../../main/images/none.gif";
				}
			}
			else if(document.getElementById(objId).style.display == "block"){
				document.getElementById(objId).style.display = "none";
				if(document.getElementById(id)){
					document.getElementById(id).src = "../../main/images/block.gif";
				}
			}
		}
	}
}

function clearAllergies(obj){	
	for(a=1;a<=20;a++){
		var allergyTxtBox = "Allergies_quest"+a;
		document.getElementById(allergyTxtBox).value = "";
		if(obj.checked == true){
			document.getElementById(allergyTxtBox).readOnly = true;
		}
		else if(obj.checked == false){
			document.getElementById(allergyTxtBox).readOnly = false;
		}
		var reactionTxtBox = "Reaction_quest"+a;
		document.getElementById(reactionTxtBox).value = "";
		if(obj.checked == true){
			document.getElementById(reactionTxtBox).readOnly = true;
		}
		else if(obj.checked == false){
			document.getElementById(reactionTxtBox).readOnly = false;
		}
	}
}

function savePreOpHealth(){
	document.getElementById("savePreOpHealthQues").value = '1';	
	if(typeof saveSig == 'function')
		saveSig();
	else
		SetSig();
	/*
	if(document.getElementById("apptIdList").value =='') {
		alert('Please Select/Create Future Appointment Date')	;
	}else {
		document.frm_health_ques.submit();
	}*/
	document.frm_health_ques.submit();
}

function OnSign1(){
	if(document.getElementById("SigPlus1")){		
		if(document.getElementById("SigPlus2")){		
			document.getElementById("SigPlus2").TabletState = 0;
		}
		document.getElementById("SigPlus1").TabletState = 1;
		var activeObj = $("#tdObjectSigPlusPreHlthPtSign");
		var activeObjN= $("#tdObjectSigPlusPreHlthWtSign");
		
		if(activeObj.length > 0 ) {
				activeObj.removeClass('consentObjectBeforSign')
								 .addClass("consentObjectAfterSign");
		}
		if(activeObjN.length > 0 ) {
				activeObjN.removeClass('consentObjectAfterSign')
									.addClass("consentObjectBeforSign");
		}
		
	}
}
	
function OnClear1(){
	if(document.getElementById("SigPlus1")){
   		document.getElementById("SigPlus1").ClearTablet();
	}
}

function OnSign2(){
	if(document.getElementById("SigPlus2")){		
		if(document.getElementById("SigPlus1")){		
			document.getElementById("SigPlus1").TabletState = 0;
		}
		document.getElementById("SigPlus2").TabletState = 1;
		
		var activeObj = $("#tdObjectSigPlusPreHlthWtSign");
		var activeObjN= $("#tdObjectSigPlusPreHlthPtSign");
		
		if(activeObj.length > 0 ) {
				activeObj.removeClass('consentObjectBeforSign')
								 .addClass("consentObjectAfterSign");
		}
		if(activeObjN.length > 0 ) {
				activeObjN.removeClass('consentObjectAfterSign')
									.addClass("consentObjectBeforSign");
		}
	}
}
	
function OnClear2(){
	if(document.getElementById("SigPlus2")){
   		document.getElementById("SigPlus2").ClearTablet();
	}
}

function trimNew(val){ 
	return val.replace(/^\s+|\s+$/, ''); 
}

function OnSignIpadPhy(patient_id,sigFor,idInnerHTML,signSeqNum){
	window.open("../../common/chartNoteSignatureIPad.php?patient_id="+patient_id+"&sigFor="+sigFor+"&idInnerHTML="+idInnerHTML+"&signSeqNum="+signSeqNum,"chartNoteSignature");
}

function image_DIV(imageSrc,div,id,seqNum){
	var srcNew = '';
	if(imageSrc){
		if(trimNew(imageSrc) != "") {			
			srcNew 	= imageSrc.replace(/\s/g,'');
			//srcNew 	= srcNew.replace('interface/','../../');alert(srcNew);
			
			if(div == "ptHealth"){
				if(typeof(document.getElementById('hidden_patient_sign_image_path')) != "undefined") {
					document.getElementById('hidden_patient_sign_image_path').value=srcNew;
				}
				if(typeof(document.getElementById(id)) != "undefined") {
					$("#"+id).html("<tr><td style='vertical-align:top;'><img src='"+imageSrc+"' style='width:150px; height:65px;'></td></tr>");		
				}
			}else if(div == "wtHealth"){
				if(typeof(document.getElementById('hidden_witness_sign_image_path')) != "undefined") {
					document.getElementById('hidden_witness_sign_image_path').value=srcNew;
				}
				if(typeof(document.getElementById(id)) != "undefined") {
					$("#"+id).html("<tr><td style='vertical-align:top;'><img src='"+imageSrc+"' style='width:150px; height:65px;'></td></tr>");		
				}
			}
		}
	}
}

function y2k(number){
	return (number < 1000)? number+1900 : number;
}

var today = new Date();
var day = today.getDate();
var month = today.getMonth()
var year = y2k(today.getYear());

function padout(number){
	return (number < 10) ? '0' + number : number;
}

function restart1(){
	//By Karan
	function getDatePosition(Format, Date){
		var t = [];
		$.each(Format , function(index, val) { 
			if(val === Date){
				t.push(index)
			}

		});
		return t;
	}
		
	var global_date_format = top.jquery_date_format;
	
	var date_global_format = global_date_format.split('-');
	var arr = $.makeArray( date_global_format );
	var DatePickFormat = {};
	
	var str_date = getDatePosition(arr,"dd");
	DatePickFormat[str_date] = padout(day);
	
	var str_month = getDatePosition(arr,"mm");
	DatePickFormat[str_month] = padout(month - 0 + 1);
	
	var str_year = getDatePosition(arr,"yy");
	DatePickFormat[str_year] = year;
	
	date_global_format = DatePickFormat[0] + "-" +DatePickFormat[1] + "-" + DatePickFormat[2];


document.frm_health_ques.date.value=''+ date_global_format;

//document.frm_health_ques.date.value=''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year ;
mywindow.close();
}

function newWindow(q)
{
	mywindow=open('../../common/mycal.php?md='+q,'rajan','width=200,height=250,top=200,left=300');
	mywindow.location.href = '../../common/mycal.php?md='+q;
	if(mywindow.opener == null)
	mywindow.opener = self;
}

function SetSig() {
	if(document.getElementById('SigPlus1')){
		if(document.getElementById('SigPlus1').NumberOfTabletPoints!=0){
			document.getElementById('SigPlus1').SigCompressionMode=1;
			document.getElementById('SigDataPt').value = document.getElementById("SigPlus1").SigString;
			//document.frm_health_ques.SigDataPt.value = document.getElementById("SigPlus1").SigString;
		}
  	}
	if(document.getElementById('SigPlus2')){
		if(document.getElementById('SigPlus2').NumberOfTabletPoints!=0){
			document.getElementById('SigPlus2').SigCompressionMode=1;
			document.getElementById('SigDataWt').value = document.getElementById("SigPlus2").SigString;
			//document.frm_health_ques.SigDataWt.value = document.getElementById("SigPlus2").SigString;	
		}
	}	
}

function changeApptFun(obj) {
	document.getElementById("health_Appt").value = 'changeAppt';
	document.getElementById("hiddenApptIdList").value = obj.value;
	document.frm_health_ques.submit();	
}

function disp_rev(_this,elem_id) {
	
	var elem_obj = $("#"+elem_id);
	var $_this = $(_this);
	if( elem_obj.hasClass('hidden') )
	{
		elem_obj.removeClass('hidden');
		$_this.addClass('glyphicon-menu-up')
				 .removeClass('glyphicon-menu-down');	
		
	}
	else
	{
		elem_obj.addClass('hidden');
		$_this.addClass('glyphicon-menu-down')
				 .removeClass('glyphicon-menu-up');
		
	}
}

function disp(_this,elem_id) {
	var $_this = $(_this);
	if( $_this.is(':checked') ) {
		$('#' + elem_id).removeClass('hidden');
		$('#' + 'up_dwn_'+elem_id).removeClass('glyphicon-menu-down');
		$('#' + 'up_dwn_'+elem_id).addClass('glyphicon-menu-up');
	}
	else {
		$('#' + elem_id).addClass('hidden');
		$('#' + 'up_dwn_'+elem_id).removeClass('glyphicon-menu-up');
		$('#' + 'up_dwn_'+elem_id).addClass('glyphicon-menu-down');
	}
}

function disp_none(_this,elem_id) {
	var $_this = $(_this);
	if( $_this.is(':checked') ) {
		$('#' + elem_id).addClass('hidden');
		$('#' + 'up_dwn_'+elem_id).removeClass('glyphicon-menu-up');
		$('#' + 'up_dwn_'+elem_id).addClass('glyphicon-menu-down');
	}
}
</script>
<body class="bg-white"> 
<form action="" name="frm_health_ques" id="frm_health_ques" method="post">
  <input type="hidden" name="savePreOpHealthQues" id="savePreOpHealthQues" />
  <input type="hidden" name="preOpHealthQuesId" id="preOpHealthQuesId" value='<?php echo $preOpHealthQuesId;?>' />
  <input type="hidden" name="health_Appt" id="health_Appt" />
  <input type="hidden" name="hiddenApptIdList" id="hiddenApptIdList" />
  <input type="hidden" name="hidden_patient_sign_image_path" id="hidden_patient_sign_image_path" value='<?php echo $patient_sign_image_path;?>' />
	<input type="hidden" name="hidden_witness_sign_image_path" id="hidden_witness_sign_image_path" />
	
	<div style=" height:<?php print $_SESSION['wn_height']-290;?>px; overflow-x:hidden; overflow-y:scroll;">
 		<div class="col-xs-12">
    	<div class="row">
      
      	<div class="col-xs-12">
      		<div class="head">Pre-Op Health Questionnaire</div>
        </div>
        
        <div class="col-xs-12 col-sm-6">
        	<div class="col-xs-12 border">
        	
            <div class="row">	
              <div class="col-xs-12 head sub_head">
                <div class="col-xs-9">
                  Have you ever had
                </div>
                <div class="col-xs-3">
                  <div class="row">
                    <div class="col-xs-5 text-center">Yes</div>
                    <div class="col-xs-4 text-center">No</div>
                    <div class="col-xs-3 text-center">&nbsp;</div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Heart Trouble/Heart Attack -->
            <div class="row">
              <?php 
                echo print_row('Heart Trouble/Heart Attack',$heartTrouble,'chbx_ht',$heartTroubleDesc,'heartTroubleDesc');
              ?>
            </div>
            
            <!-- Stroke -->
            <div class="row">
              <?php  
                echo print_row('Stroke',$stroke,'chbx_sht',$strokeDesc,'strokeDesc');
              ?>
            </div>
            
            <!-- HighBP -->
            <div class="row">
              <?php 
                echo print_row('HighBP',$HighBP,'chbx_HighBP',$HighBPDesc,'HighBPDesc');
              ?>
            </div>
            
            <!-- Anticoagulation therapy (i.e. Blood Thinners) -->
            <div class="row">
              <?php 
                echo print_row('Anticoagulation therapy (i.e. Blood Thinners)',$anticoagulationTherapy,'chbx_anti_thrp',$anticoagulationTherapyDesc,'anticoagulationTherapyDesc');
              ?>
            </div>
            
            <!-- Asthma, Sleep Apnea, Breathing Problems -->
            <div class="row">
              <?php 
                echo print_row('Asthma, Sleep Apnea, Breathing Problems',$asthma,'chbx_ast_slp',$asthmaDesc,'asthmaDesc');
              ?>
            </div>
            
            <!-- Tuberculosis -->
            <div class="row">
              <?php 
                echo print_row('Tuberculosis',$tuberculosis,'chbx_tuber',$tuberculosisDesc,'tuberculosisDesc');
              ?>
            </div>
            
            <!-- Diabetes -->
            <div class="row">
              <?php
                $xtra_data = '';
                $xtra_data .= '
                  <div class="checkbox checkbox-inline">
                    <input class="field checkbox" type="checkbox" '.($insulinDependence=="Yes" ? "Checked" :'').' name="chbx_subdiab" value="Yes" id="chbx_subdiab_yes" onChange="checkSingle(\'chbx_subdiab_yes\',\'chbx_subdiab\')" />
                    <label for="chbx_subdiab_yes">Insulin Dependent</label>	
                  </div>
                  <div class="checkbox checkbox-inline">
                    <input class="field checkbox" type="checkbox" '.($insulinDependence=="No" ? "Checked" :'').' name="chbx_subdiab" value="No" id="chbx_subdiab_no" onChange="checkSingle(\'chbx_subdiab_no\',\'chbx_subdiab\')" />
                    <label for="chbx_subdiab_no">Non-Insulin Dependent</label>	
                  </div>'; 
                echo print_row('Diabetes',$diabetes,'chbx_diab',$diabetesDesc,'diabetesDesc',$xtra_data);
              ?>
            </div>
            
            <!-- Epilepsy, Convulsions, Parkinson's, Vertigo -->
            <div class="row">
              <?php 
                echo print_row('Epilepsy, Convulsions, Parkinson\'s, Vertigo',$epilepsy,'chbx_epile',$epilepsyDesc,'epilepsyDesc');
              ?>
            </div>
            
            <!-- Restless Leg Syndrome -->
            <div class="row">
              <?php 
                echo print_row('Restless Leg Syndrome',$restlessLegSyndrome,'chbx_restless',$restlessLegSyndromeDesc,'restlessLegSyndromeDesc');
              ?>
            </div>
            
            <!-- Hepatitis -->
            <div class="row">
              <?php
                $xtra_data = '';
                $xtra_data .= '
                  <div class="checkbox checkbox-inline">
                    <input type="checkbox" name="HepatitisA" id="HepatitisA" value="true" '.($hepatitisA == 'true'? "CHECKED" : '').' />
                    <label for="HepatitisA">A</label>	
                  </div>
                  <div class="checkbox checkbox-inline">
                    <input type="checkbox" name="HepatitisB" id="HepatitisB" value="true" '.($hepatitisB == 'true'? "CHECKED" : '').' />
                    <label for="HepatitisB">B</label>	
                  </div>
                  <div class="checkbox checkbox-inline">
                    <input type="checkbox" name="HepatitisC" id="HepatitisC" value="true" '.($hepatitisC == 'true'? "CHECKED" : '').' />
                    <label for="HepatitisC">C</label>	
                  </div>'; 
                echo print_row('Hepatitis',$hepatitis,'chbx_hepat',$hepatitisDesc,'hepatitisDesc',$xtra_data);
              ?>
            </div>
            
            <!-- Kidney Disease, Dialysis -->
            <div class="row">
              <?php 
                $xtra_data = '';
                $xtra_data .= '
                  <div class="checkbox checkbox-inline">
                    <input class="field checkbox" type="checkbox" '.($shunt=="Yes" ? "Checked" : '').' value="Yes" name="chbx_subkidnShunt" id="chbx_subkidn_yes" />
                    <label for="chbx_subkidn_yes">Do you have a Shunt</label>	
                  </div>
                  <div class="checkbox checkbox-inline">
                    <input class="field checkbox" type="checkbox" '.($fistula=="Yes" ? "Checked" : '').' value="Yes" name="chbx_subkidnFistula" id="chbx_subkidn_no" />
                    <label for="chbx_subkidn_no">Fistula</label>	
                  </div>'; 
                echo print_row('Kidney Disease, Dialysis',$kidneyDisease,'chbx_kidn',$kidneyDiseaseDesc,'kidneyDiseaseDesc',$xtra_data);
              ?>
            </div>
            
            <!-- HIV, Autoimmune Diseases -->
            <div class="row">
              <?php 
                echo print_row('HIV, Autoimmune Diseases',$hivAutoimmuneDiseases,'chbx_hiv_auto',$hivTextArea,'hivTextArea');
              ?>
            </div>
            
            <!-- History of cancer -->
            <div class="row">
              <?php 
                $xtra_data = '';
                $xtra_data .= '
                  <label><b>Breast cancer:</b>&nbsp;</label>
                  <div class="checkbox checkbox-inline">
                    <input '.($brest_cancer == 'Lef' ? 'CHECKED' : '').' value="Lef" name="brest_cancer" id="chbx_brest_cancer_left" onChange="checkSingle(\'chbx_brest_cancer_left\',\'brest_cancer\')" type="checkbox">
                    <label for="chbx_brest_cancer_left">Left</label>	
                  </div>
                  <div class="checkbox checkbox-inline">
                    <input '.($brest_cancer == 'Rig' ? 'CHECKED' : '').' value="Rig" name="brest_cancer" id="chbx_brest_cancer_right" onChange="checkSingle(\'chbx_brest_cancer_right\',\'brest_cancer\')" type="checkbox">
                    <label for="chbx_brest_cancer_right">Right</label>	
                  </div>'; 
                  
                echo print_row('History of cancer',$cancerHistory,'chbx_hist_can',$cancerHistoryDesc,'cancerHistory',$xtra_data);
              ?>
            </div>
            
            <!-- Organ Transplant -->
            <div class="row">
              <?php 
                echo print_row('Organ Transplant',$organTransplant,'chbx_org_trns',$organTransplantDesc,'organTransDesc');
              ?>
            </div>
            
            <!-- A Bad Reaction to Local or General Anesthesia -->
            <div class="row">
              <?php 
                echo print_row('A Bad Reaction to Local or General Anesthesia',$anesthesiaBadReaction,'chbx_bad_react',$anesthesiaBadReactionDesc,'anesthesiaBadReactionDesc');
              ?>
            </div>
            
            <!-- Other -->
            <div class="row pd10" style="background-color:#D1E0C9;">
              <div class="col-xs-2 col-md-1 ">
                <label><b>Other</b></label>
              </div>
              <div class="col-xs-10 col-md-11">
                <textarea id="otherTroubles" name="otherTroubles" class="form-control"><?php echo stripslashes($otherTroubles); ?></textarea>
              </div>
            </div>
            
            <?php 	
    					$selectAdminQuestionsQry="select * from surgery_center_health_questioner order by healthQuestioner";
							$selectAdminQuestions=imw_query($selectAdminQuestionsQry);
							$selectAdminQuestionsRows=imw_num_rows($selectAdminQuestions);
							
							$inc=0;
							$selectPatientAdminQuestions = "select * from surgery_center_health_question_admin where patient_id = $patient_id order by id";
							$reSelectPatientAdminQuestions = imw_query($selectPatientAdminQuestions);
							$patientAdminQuestions = $patientAdminQuestionsDesc = array();
							while($rowSelectPatientAdminQuestions = imw_fetch_array($reSelectPatientAdminQuestions)){
								$key = $rowSelectPatientAdminQuestions['adminQuestion'];
								$value =  $rowSelectPatientAdminQuestions['adminQuestionStatus'];
								$valueDesc =  $rowSelectPatientAdminQuestions['adminQuestionDesc'];
								$patientAdminQuestions[$key] = $value;
								$patientAdminQuestionsDesc[$key] = $valueDesc;
							}
								
							if($selectAdminQuestionsRows > 0){
								$a = 0;
								while($resultSelectAdminQuestions=imw_fetch_array($selectAdminQuestions)){
									$a++;
									$quesStatusVal= $patientAdminQuestions[$resultSelectAdminQuestions['question']];
									$quesStatusValDesc= $patientAdminQuestionsDesc[$resultSelectAdminQuestions['question']];
									$quesChkedYes = ($quesStatusVal == 'Yes') ? 'checked' : '';
									$quesChkedNo = ($quesStatusVal == 'No') ? 'checked' : '';
						?>
            			
                    <div class="col-xs-9">
                        <input type="hidden" name="hlthQuestLabel<?php echo $a; ?>" value="<?php echo $resultSelectAdminQuestions['question'];?>">
                        <label><?php echo $resultSelectAdminQuestions['question'];?></label>
                    </div>
                    <div class="col-xs-3">
                        <div class="row">
                            <div class="col-xs-5 text-center">
                                <div class="checkbox">
                                    <input type="checkbox" name="question<?php echo $a; ?>" value="Yes" id="question_yes<?php echo $a; ?>" onClick="javascript:checkSingle('question_yes<?php echo $a; ?>','question<?php echo $a; ?>'),disp(this,'chbx_admin_quest_tb_id<?php echo $a; ?>')"; <?php echo $quesChkedYes; ?> /><label for="question_yes<?php echo $a; ?>">&nbsp;</label>
                                </div>
                            </div>
            
                            <div class="col-xs-4 text-center">
                                <div class="checkbox">
                                    <input type="checkbox" name="question<?php echo $a; ?>" value="No" id="question_no<?php echo $a; ?>" onClick="javascript:checkSingle('question_no<?php echo $a; ?>','question<?php echo $a; ?>'),disp_none(this,'chbx_admin_quest_tb_id<?php echo $a; ?>')"; <?php echo $quesChkedNo; ?> ><label for="question_no<?php echo $a; ?>">&nbsp;</label>
                                </div>
                            </div>
            
                            <div class="col-xs-3 text-center ">
                                <i class="glyphicon <?php if($quesStatusVal=='Yes') { echo 'glyphicon-menu-up';}else { echo 'glyphicon-menu-down';}?> pd5 pointer" id="up_dwn_chbx_admin_quest_tb_id<?php echo $a; ?>" onClick="javascript:disp_rev(this,'chbx_admin_quest_tb_id<?php echo $a; ?>')"></i>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>  
                    <div class="col-xs-12 pd5 <?php if($quesStatusVal=='Yes') echo ''; else echo 'hidden'; ?>" id="chbx_admin_quest_tb_id<?php echo $a; ?>" style="background-color:#efefef;">
                        <textarea id="adminQuestDesc<?php echo $a; ?>" placeholder="Describe" class="form-control" name="adminQuestDesc<?php echo $a; ?>"><?php echo stripslashes($quesStatusValDesc); ?></textarea>
                    </div>                	
           	<?php
								}
							}
						?>
            
          </div>
       	</div>
        
        <div class="clearfix visible-xs">&nbsp;</div>
        
        <div class="col-xs-12 col-sm-6">
        	<div class="col-xs-12 border">
          	
            <div class="row"> 
            	<div class="col-xs-12 head sub_head">	
                <div class="col-xs-6 col-sm-5 col-md-6 col-lg-7">
                  Allergies/Drug Reaction
                </div>
                <div class="col-xs-1 col-lg-1 text-right">
                  <div class="checkbox">
                    <input type="checkbox" disabled <?php if($nkdaAllergyStatus =='Yes'){ echo 'CHECKED'; } ?> value="Yes"  name="chbx_drug_react" id="chbx_drug_react_no" onClick="clearAllergies(this);" />
                    <label for="chbx_drug_react_no">NKA</label>
                  </div>
                </div>
                <div class="col-xs-5 col-sm-5 col-md-4 col-lg-4 text-right nowrap">
                  <div class="checkbox">
                    <input class="checkbox"  type="checkbox" <?php if($allergies_status_reviewed=='Yes'){ echo 'CHECKED'; } ?>  value="Yes" name="chbx_drug_react_reviewed" id="chbx_drug_react_yes" />
                    <label for="chbx_drug_react_yes">Allergies Reviewed</label>
                  </div>	
                </div>
              </div>
          	</div>
           	
            <div class="row bg-gray">
            	<div class="col-xs-12">
                <div class="col-xs-6"><label class="margin_0 pd5"><b>Name</b></label></div>
                <div class="col-xs-6"><label class="margin_0 pd5"><b>Reaction</b></label></div>
              </div>
           	</div> 
            
            <div class="row tabl-responsive" id="iframe_health_quest" style="height:95px; overflow:auto; ">
            	<table cellpadding="0" cellspacing="0" class="table table-bordered table-condensed table-striped scroll margin_0">
              <tbody>
              <?php 											
								$qry = "select * from lists where pid=$patient_id and type in(3,7) and allergy_status = 'Active'";
								$queryGetAllergyPrecRes = imw_query($qry) or die(imw_error());
								$i_healthquest_allerg = 1;
								while($allergyRow = imw_fetch_array($queryGetAllergyPrecRes)){
									$intPatientAllergyId = "";
									$strPatientAllergyName = "";
									$strPatientAllergyreaction = "";
									$strPatientAllergyName = $allergyRow['title'];
									$strPatientAllergyreaction = $allergyRow['comments'];
							?> 
              		<input type="hidden" name="allergyIdHidden<?php echo $i_healthquest_allerg;?>" id="allergy_IdHidden<?php echo $intCount;?>" />
                  <input type="hidden" name="allergyNameHidden<?php echo $i_healthquest_allerg;?>" id="allergy_nameHidden<?php echo $intCount;?>"/>
                  <input type="hidden" name="allergyReactionHidden<?php echo $i_healthquest_allerg;?>" id="allergy_reactionHidden<?php echo $intCount;?>"/>
                  <tr>
                  	<td class="col-xs-6"><?php echo stripslashes($strPatientAllergyName); ?></td>
                    <td ><?php echo stripslashes($strPatientAllergyreaction); ?></td>
                 	</tr>
             	<?php 
									$i_healthquest_allerg++;
								} 
							?>
              </tbody>
              </table>
            </div>
            
        	</div>
          
          <!-- Medications -->
          <div class="col-xs-12 border mt2">
          	
            <div class="row"> 
            	<div class="col-xs-12 head sub_head">
              <div class="col-xs-5 col-sm-12 col-md-5 col-lg-5 nowrap">
                Take Prescription Medications
              </div>
              
              <div class="col-xs-3 col-sm-5 col-md-3 col-lg-3 ">
                <div class="checkbox nowrap">
                  <input type="checkbox" disabled <?php if($noMedicationStatus =='Yes'){ echo 'CHECKED'; } ?> value="Yes"  name="chbx_no_med_status" id="chbx_no_med_status">
                  <label for="chbx_no_med_status">No Medication</label>
                </div>	
              </div>
              
              <div class="col-xs-4 col-sm-7 col-md-4 col-lg-4 ">
              	<textarea class="form-control" style="height:27px!important;" readonly><?php echo stripslashes(nl2br($noMedicationComments));?></textarea>
              </div>
              
              <div class="clearfix visible-sm mb5"></div>
              </div>
          	</div>
           	
            <div class="row bg-gray">
            	<div class="col-xs-12">
                <div class="col-xs-4"><label class="margin_0 pd5"><b>Name</b></label></div>
                <div class="col-xs-4"><label class="margin_0 pd5"><b>Dosage</b></label></div>
                <div class="col-xs-4"><label class="margin_0 pd5"><b>Sig</b></label></div>
              </div>
           	</div> 
            
            <div class="row tabl-responsive" id="iframe_health_quest_medication" style="height:95px; overflow:auto; ">
            	<table cellpadding="0" cellspacing="0" class="table table-bordered table-condensed table-striped scroll margin_0">
              <tbody>	
              <?php 	
              	$queryGetMedication = "select * from lists where pid=$patient_id and (type='1' or type='4') and allergy_status = 'Active'";
								$queryGetMedicationRes = imw_query($queryGetMedication) or die(imw_error());
								$i_healthquest_medication = 1;
								while($medicationRow = imw_fetch_array($queryGetMedicationRes)){													
									$strPatientMedicationName = $medicationRow['title'];
									$strPatientMedicationDosage = $medicationRow['destination'];
									$strPatientMedicationSig = $medicationRow['sig'];										
							?> 
              		<tr>
                  	<input type="hidden" name="medicationIdHidden<?php echo $i_healthquest_medication;?>" id="medication_IdHidden<?php echo $i_healthquest_medication;?>" />
                    <input type="hidden" name="medicationNameHidden<?php echo $i_healthquest_medication;?>" id="medication_NameHidden<?php echo $i_healthquest_medication;?>"/>
                    <input type="hidden" name="medicationReactionHidden<?php echo $i_healthquest_medication;?>" id="medication_ReactionHidden<?php echo $i_healthquest_medication;?>"/>
                    <td class="col-xs-4"><?php echo stripslashes($strPatientMedicationName); ?></td>
                    <td class="col-xs-4"><?php echo stripslashes($strPatientMedicationDosage); ?></td>
                    <td ><?php echo stripslashes($strPatientMedicationSig); ?></td>
                	</tr>
							<?php 
									$i_healthquest_medication++;
								} 
              ?>	
              </tbody>
							</table>
            </div>
            
        	</div>
          
          <div class="col-xs-12 border mt2">
          	<div class="row">
            	<div class="col-xs-12 head sub_head">
                <div class="col-xs-9">Do You</div>
                <div class="col-xs-3">
                  <div class="row">
                    <div class="col-xs-5 text-center">Yes</div>
                    <div class="col-xs-4 text-center">No</div>
                    <div class="col-xs-3 text-center">&nbsp;</div>
                  </div>
                </div>
              </div>
          	</div>
            
            <!-- Use a Wheel Chair, Walker or Cane -->
            <div class="row">
            	<?php 
								echo print_row('Use a Wheel Chair, Walker or Cane',$walker,'chbx_use_wheel',$walkerDesc,'walkerDesc');
							?>
           	</div>
            
            <!-- Wear Contact lenses -->
            <div class="row">
            	<?php 
								echo print_row('Wear Contact lenses',$contactLenses,'chbx_wear_cont',$contactLensesDesc,'contactLensesDesc');
							?>
           	</div>
            
            <!-- Smoke -->
            <div class="row">
            	<?php 
                $xtra_data2 = '';
                $xtra_data2 .= '
                  <label>Patient advised not to smoke 24 H prior to surgery&nbsp;&nbsp;&nbsp;</label>
                  <div class="checkbox checkbox-inline">
                    <input '.($smokeAdvise == 'Yes' ? 'CHECKED' : '').' value="Yes" name="smokeAdvise" id="smokeAdvise" type="checkbox" >
                  	<label for="smokeAdvise"></label>
				  </div>
                  '; 
								
								echo print_row('Smoke',$smoke,'chbx_smoke',$smokeHowMuch,'smokeHowMuch','',$xtra_data2);
							?>
           	</div>
            
            <!-- Drink Alcohol -->
            <div class="row">
            	<?php
                $xtra_data2 = '';
                $xtra_data2 .= '
                  <label>Patient advised not to drink 24 H prior to surgery&nbsp;&nbsp;&nbsp;</label>
                  <div class="checkbox checkbox-inline">
					<input '.($alchoholAdvise == 'Yes' ? 'CHECKED' : '').' value="Yes" name="alchoholAdvise" id="alchoholAdvise" type="checkbox" >
                  	<label for="alchoholAdvise"></label>
				  </div>
                  '; 
				 
								echo print_row('Drink Alcohol',$alchohol,'chbx_drink',$alchoholHowMuch,'alchoholHowMuch','',$xtra_data2);
							?>
           	</div>
            
            <!-- Have an automatic internal defibrillator -->
            <div class="row">
            	<?php 
								echo print_row('Have an automatic internal defibrillator',$autoInternalDefibrillator,'chbx_hav_auto_int',$autoInternalDefibrillatorDesc,'autoInternalDefibrillatorDesc');
							?>
           	</div>
            
            <!-- Have any Metal Prosthetics -->
            <div class="row">
            	<?php 
								echo print_row('Have any Metal Prosthetics',$metalProsthetics,'chbx_hav_any_met',$notesDesc,'notesDesc');
							?>
           	</div>
         	</div>
          
        </div>
        
        <div class="clearfix"></div>
       	
        
        <div class="col-xs-6 col-sm-6 mt2">
        	<div class="col-xs-12 border">
        	
            <div class="row">	
              <div class="col-xs-12 head">
                Emergency Contact
              </div>
            </div>
            
            <div class="row ">
            	<div class="col-xs-6 mb5">
              	<label>Contact Person</label>
                <input type="text" name="emergencyContactPerson" id="emergencyContactPerson" value="<?php echo $emergencyContactPerson; ?>" class="form-control" >	
              </div>
              
              <div class="col-xs-6 mb5">
              	<label>Tel.</label>
                <input type="text"   name="emergencyContactTel" id="emergencyContactTelId" value="<?php echo $emergencyContactPhone; ?>" maxlength="12"  class="form-control" >
              </div>
              
            </div>
            
            
         	</div>
       	</div>   
           
       	<div class="col-xs-6 col-sm-6 mt2">
        	<div class="col-xs-12 border">
          		
              <div class="row">	
                <div class="col-xs-12 head">
                  Witness
                </div>
              </div>
            
          	 	<div class="row ">
            		<div class="col-xs-12 mb5">
              		<label>Witness Name</label>
                  <input type="text" name="witnessname" id="witnessnameId" value="<?php echo $witnessname; ?>" maxlength="15"  class="form-control" />
              	</div>
            	</div>  	
          </div>
       	</div>
        
      	<div class="clearfix"></div>
       	
        <div class="col-xs-12">
        	<div class="row row-flex">
          
            <div class="col-xs-5 mt2">
              <div class="col-xs-12 border">
                <div class="row">
                  <div class="col-xs-3 text-center">
                    <img class="mt2" src="<?php echo $library_path;?>/images/sig-icon.png" />
                    <label class="nowrap"><b>Patient Signature</b></label>
                  </div>
                  <?php 
									//$patient_sign_image_path = '';
									//$witness_sign_image_path = '';
									?>
                  <div class="col-xs-9 text-center" id="tbl_sign_pt_health_id">
                    <input type="hidden" name="elem_signature" value="<?php echo $patientSign; ?>">
                    <input type="hidden" name="SigDataPt" id="SigDataPt">
                    <input type="hidden" name="SigDataPtLoadValue" id="SigDataPtLoadValue" value="<?php echo $patientSign;?>" />
                    <?php
                      if($patient_sign_image_path) {
                    ?>		
                        <img src="<?php echo data_path(1).$patient_sign_image_path;?>" width="150" height="65">
                    <?php
                      }
                      else
                      {
                    ?>
                        <div class="row" style="display:flex;">
                         <?php if( $enable_sig_web) { ?>
                         	<div class="col-xs-7 consentObjectBeforSign" id="tdObjectSigPlusPreHlthPtSign">
                         		<canvas id="SigPlus1" name="SigPlus1" width="150" height="65"></canvas>
													</div>
                         <?php } else { ?>
                          <div class="col-xs-7 consentObjectBeforSign" id="tdObjectSigPlusPreHlthPtSign">
                            <OBJECT classid=clsid:69A40DA3-4D42-11D0-86B0-0000C025864A height="75"
                                        id="SigPlus1" name="SigPlus1"
                                        style="HEIGHT: 65px; WIDTH: 150px; LEFT: 0px; TOP: 0px;" 
                                        VIEWASTEXT>
                                        <PARAM NAME="_Version" VALUE="131095">
                                        <PARAM NAME="_ExtentX" VALUE="4842">
                                        <PARAM NAME="_ExtentY" VALUE="1323">
                                        <PARAM NAME="_StockProps" VALUE="0">
                                    </OBJECT>	
                          </div>
                          <?php } ?>
                          <div class="col-xs-5">
                            <div class="row mt20">
                              <div class="col-xs-4 pointer">
                                <img src="<?php echo $library_path;?>/images/sig-start.png" id="SignBtnPtHealth" name="SignBtnPtHealth" title="Signature" onClick="<?php if($enable_sig_web) echo 'OnSign(1,\'SigPlus1\',\'SigDataPt\');'; else echo 'OnSign1();';?>" />
                              </div>
                          
                              <div class="col-xs-4 pointer">
                                <img src="<?php echo $library_path;?>/images/sig-touch.png" alt="Touch Signature" title="Touch Signature" id="SignBtnTouchPtHealth" name="SignBtnTouchPtHealth" onClick="OnSignIpadPhy('<?php echo $_SESSION['patient'];?>','ptHealth','tbl_sign_pt_health_id','1')" />
                              </div>
                              
                              <div class="col-xs-4 pointer">
                                <img src="<?php echo $library_path;?>/images/sig-remove.png" id="clearSignPtHealth" alt="Clear Sign" title="Clear Sign" name="clearSignPtHealth" onClick="<?php if($enable_sig_web) echo 'OnClear(1,\'SigPlus1\',\'SigDataPt\');'; else echo 'OnClear1();';?>" />
                              </div>
                            </div>
                          </div>
                        </div>    
                    <?php
                      }
                    ?>
                    
                  </div>
                </div>     
              </div>
            </div> 
        
            <div class="col-xs-2 border mt2">
              <div class="col-xs-12 ">
                <label><b>Date</b></label>
                <?php
                  $tmp_date = $dateQuestionnaire;
                  $dateQuestionnaire = $month."-".$day."-".$year;
                  $dateQuestionnaire = get_date_format($tmp_date);
                  if(!$dateQuestionnaire || get_number($dateQuestionnaire)=='0') { 
                    $dateQuestionnaire = get_date_format(date('Y-m-d'));
                  }
                ?>
                <div class="input-group">
                  <input id="date1" type="text" name="date" onBlur="checkdate(this); " value="<?php echo $dateQuestionnaire;?>" maxlength=10 class="form-control datepicker">
                  <label class="input-group-addon pointer" for="date1">
                    <i class="glyphicon glyphicon-calendar"></i>
                  </label>
                </div>    
                    
              </div>
            </div>  
        	
            <div class="col-xs-5 mt2">
              <div class="col-xs-12 border">
                <div class="row">
                  <div class="col-xs-3 text-center">
                    <img class="mt2" src="<?php echo $library_path;?>/images/sig-icon.png" />
                    <label class="nowrap"><b>Witness Signature</b></label>
                  </div>
                  <div class="col-xs-9 text-center" id="tbl_sign_wt_health_id">
                    <input type="hidden" name="witnSign" value="<?php echo $witnessSign; ?>">
                    <input type="hidden" name="SigDataWt" id="SigDataWt">
                    <input type="hidden" name="SigDataWtLoadValue" id="SigDataWtLoadValue" value="<?php echo $witnessSign;?>">
                    <?php
                      if($witness_sign_image_path) {
                    ?>		
                        <img src="<?php echo data_path(1).$witness_sign_image_path;?>" width="150" height="65">
                    <?php
                      }
                      else
                      {
                    ?>
                        <div class="row" style="display:flex;">
                         <?php if( $enable_sig_web) { ?>
                         	<div class="col-xs-7 consentObjectBeforSign" id="tdObjectSigPlusPreHlthWtSign">
                         		<canvas id="SigPlus2" name="SigPlus2" width="150" height="65"></canvas>
													</div>
                         <?php } else { ?>
                          <div class="col-xs-7 consentObjectBeforSign" id="tdObjectSigPlusPreHlthWtSign">
                            <OBJECT classid=clsid:69A40DA3-4D42-11D0-86B0-0000C025864A height="75"
                                id="SigPlus2" name="SigPlus2"
                                style="HEIGHT: 65px; WIDTH: 150px; LEFT: 0px; TOP: 0px;" 
                                VIEWASTEXT>
                                <PARAM NAME="_Version" VALUE="131095">
                                <PARAM NAME="_ExtentX" VALUE="4842">
                                <PARAM NAME="_ExtentY" VALUE="1323">
                                <PARAM NAME="_StockProps" VALUE="0">
                            </OBJECT>		
                          </div>
                         <?php } ?> 
                          <div class="col-xs-5">
                            <div class="row mt20">
                              <div class="col-xs-4 pointer">
                                <img src="<?php echo $library_path;?>/images/sig-start.png" id="SignBtnWtHealth" name="SignBtnWtHealth" title="Signature" onClick="<?php if($enable_sig_web) echo 'OnSign(2,\'SigPlus2\',\'SigDataWt\');'; else echo 'OnSign2();';?>" />
                              </div>
                          
                              <div class="col-xs-4 pointer">
                                <img src="<?php echo $library_path;?>/images/sig-touch.png" alt="Touch Signature" title="Touch Signature" id="SignBtnTouchPtHealth" name="SignBtnTouchPtHealth" onClick="OnSignIpadPhy('<?php echo $_SESSION['patient'];?>','wtHealth','tbl_sign_wt_health_id','1')" />
                              </div>
                              
                              <div class="col-xs-4 pointer">
                                <img src="<?php echo $library_path;?>/images/sig-remove.png" id="clearSignWtHealth" alt="Clear Sign" title="Clear Sign" name="clearSignWtHealth" onClick="<?php if($enable_sig_web) echo 'OnClear(2,\'SigPlus2\',\'SigDataWt\');'; else echo 'OnClear2();';?>" />
                              </div>
                            </div>
                          </div>
                        </div>    
                    <?php
                      }
                    ?>
                    
                  </div>
                </div>     
              </div>
            </div>
        	
          </div>
        </div>
        
     	</div>
    </div>
 	
  </div>
</form>

<script>
//Btn ---
top.btn_show("POSCF");
//Btn ---
	$(function(){
		$('body').on('click focus','.datepicker',function(){
			$(this).datetimepicker({timepicker:false,format:top.jquery_date_format,maxDate:new Date(),autoclose: true, scrollInput:false});		
		});
	});
</script>

</body>
</html>		
<?php 
if($saveDone == true){
	echo "<script language=\"javascript\">top.fAlert('Information successfully saved');</script>";
}
?>		