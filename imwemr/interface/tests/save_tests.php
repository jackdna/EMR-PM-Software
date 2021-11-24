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
require_once("../../config/globals.php");
require_once("../../library/patient_must_loaded.php");
require_once("../../library/classes/class.tests.php");
require_once("../../library/classes/SaveFile.php");
require_once("../../library/classes/ChartTestPrev.php");
require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");

$library_path 			= $GLOBALS['webroot'].'/library';
$patient_id				= $_SESSION['patient'];
$objTests				= new Tests;
$objTests->patient_id 	= $patient_id;
$elem_per_vo			= $objTests->get_tests_VO_access_status();

//MAKING OBJECT TO SAVE IMAGE FILES
$oSaveFile = new SaveFile($patient_id);

//MAKING OBJECT OF CHART NOTE CLASS
$oChartNote = new ChartNote($patient_id,0);

//start signature work
$hidd_prev_signature_path = $_POST["hidd_prev_signature_path"];
$hidd_signature_path = $_POST["hidd_signature_path"];
$signPathQry=$signDTmQry=$signDTmFieldName=$signDTmFieldValue=$signPathFieldName=$signPathFieldValue="";
$boolSignDtTm = false;
if($hidd_prev_signature_path && $hidd_signature_path && $hidd_prev_signature_path!=$hidd_signature_path) {
	$hidd_prev_signature_real_path=realpath($tmpDirPth_up.$hidd_prev_signature_path);
	if(file_exists($hidd_prev_signature_real_path)) {
		@unlink($hidd_prev_signature_real_path);
	}
	$boolSignDtTm = true;
	$signatureDateTime = date("Y-m-d H:i:s");
}
if($hidd_signature_path && !$hidd_prev_signature_path) {
	$boolSignDtTm = true; $signatureDateTime = date("Y-m-d H:i:s");
}
if($boolSignDtTm == true) {
	$signDTmQry = " sign_path_date_time='".$signatureDateTime."', ";
	$signDTmFieldName = " ,sign_path_date_time ";
	$signDTmFieldValue = " ,'".$signatureDateTime."' ";
}
if($hidd_signature_path) {
	$signPathQry = " sign_path='".$hidd_signature_path."', ";
	$signPathFieldName = " ,sign_path ";
	$signPathFieldValue = " ,'".$hidd_signature_path."' ";
	
}
//end signature work


switch($_POST["elem_saveForm"]){
	case "Disc":{
		$discOdSummary = "";
		$retinaOdSummary = "";
		$maculaOdSummary = "";

		$discOsSummary = "";
		$retinaOsSummary = "";
		$maculaOsSummary = "";

		$patientId = $_POST["elem_patientId"];
		$formId = $_POST["elem_formId"];
		$hd_disc_mode = $_POST["hd_disc_mode"];
		$disc_id = $_POST["elem_discId"];
		$examDate = getDateFormatDB($_POST["elem_examDate"]);
		if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
		$fundusDiscPhoto = $_POST["elem_fundusDiscPhoto"];
		$shots = $_POST["elem_shots"];
		$extraCopy = $_POST["elem_extraCopy"];
		$photoEye = $_POST["elem_photoEye"];
		$performedBy = $_POST["elem_performedBy"];
		$ptUnderstanding = $_POST["elem_ptUnderstanding"];
		$reliabilityOd = $_POST["elem_reliabilityOd"];
		$reliabilityOs = $_POST["elem_reliabilityOs"];
		$discOd = $_POST["elem_discOd"];
		$cdOd = imw_real_escape_string($_POST["elem_cdOd"]);
		$retinaOd = $_POST["elem_retinaOd"];
		$maculaOd = $_POST["elem_maculaOd"];
		$resDescOd = imw_real_escape_string($_POST["elem_resDescOd"]);
		$discOs = $_POST["elem_discOs"];
		$cdOs = imw_real_escape_string($_POST["elem_cdOs"]);
		$retinaOs = $_POST["elem_retinaOs"];
		$maculaOs = $_POST["elem_maculaOs"];
		$resDescOs = imw_real_escape_string($_POST["elem_resDescOs"]);
		$stable = $_POST["elem_stable"];
		$monitorAg = $_POST["elem_monitorAg"];
		$fuApa = $_POST["elem_fuApa"];
		$ptInformed = $_POST["elem_ptInformed"];
		$fuRetina = $_POST["elem_fuRetina"];
		$fuRetinaDesc = imw_real_escape_string($_POST["elem_fuRetinaDesc"]);
		$signature = $_POST["elem_signature"];
		$phyName = $_POST["elem_physician"];
		$normal_OD = $_POST['normal_OD'];
		$tech2InformPt = $_POST["elem_tech2InformPt"];
		$elem_noP = $_POST["elem_noP"];
		$examTime = $_POST["elem_examTime"];
		$contiMeds = $_POST["elem_contiMeds"];
		$diagnosis = imw_real_escape_string($_POST["elem_diagnosis"]);
		$diagnosisOther = imw_real_escape_string($_POST["elem_diagnosisOther"]);
		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
		
		$forum_procedure = (isset($_REQUEST['forum_procedure']))?imw_real_escape_string($_REQUEST['forum_procedure']):"";
		$zeissAction = (isset($_REQUEST['forum_procedure']))?$_POST['zeissAction']:false;

		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
			$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
			$encounter_id = $objTests->getEncounterId();
		}

		//Tech Comments
		if($_POST["elem_desc"] == 'Technician Comments'){
			$_POST["elem_desc"] = '';
		}
		$discDesc = imw_real_escape_string($_POST["elem_desc"]);

		$Macula_BDR_OD_T = $_POST["Macula_BDR_OD_T"];
		$Macula_BDR_OD_1 = $_POST["Macula_BDR_OD_1"];
		$Macula_BDR_OD_2 = $_POST["Macula_BDR_OD_2"];
		$Macula_BDR_OD_3 = $_POST["Macula_BDR_OD_3"];
		$Macula_BDR_OD_4 = $_POST["Macula_BDR_OD_4"];
		$arr = array("T"=>$Macula_BDR_OD_T,"+1"=>$Macula_BDR_OD_1,"+2"=>$Macula_BDR_OD_2,"+3"=>$Macula_BDR_OD_3,"+4"=>$Macula_BDR_OD_4 );
		$maculaOdSummary .= $objTests->getTestSumm($arr,"BDR");

		$Macula_Rpe_OD_T = $_POST["Macula_Rpe_OD_T"];
		$Macula_Rpe_OD_1 = $_POST["Macula_Rpe_OD_1"];
		$Macula_Rpe_OD_2 = $_POST["Macula_Rpe_OD_2"];
		$Macula_Rpe_OD_3 = $_POST["Macula_Rpe_OD_3"];
		$Macula_Rpe_OD_4 = $_POST["Macula_Rpe_OD_4"];
		$arr = array("T"=>$Macula_Rpe_OD_T,"+1"=>$Macula_Rpe_OD_1,"+2"=>$Macula_Rpe_OD_2,"+3"=>$Macula_Rpe_OD_3,"+4"=>$Macula_Rpe_OD_4 );
		$maculaOdSummary .= $objTests->getTestSumm($arr,"Rpe change");

		$Macula_Edema_OD_T = $_POST["Macula_Edema_OD_T"];
		$Macula_Edema_OD_1 = $_POST["Macula_Edema_OD_1"];
		$Macula_Edema_OD_2 = $_POST["Macula_Edema_OD_2"];
		$Macula_Edema_OD_3 = $_POST["Macula_Edema_OD_3"];
		$Macula_Edema_OD_4 = $_POST["Macula_Edema_OD_4"];
		$arr = array("T"=>$Macula_Edema_OD_T,"+1"=>$Macula_Edema_OD_1,"+2"=>$Macula_Edema_OD_2,"+3"=>$Macula_Edema_OD_3,"+4"=>$Macula_Edema_OD_4 );
		$maculaOdSummary .= $objTests->getTestSumm($arr,"Edema");

		$Macula_Drusen_OD_T = $_POST["Macula_Drusen_OD_T"];
		$Macula_Drusen_OD_1 = $_POST["Macula_Drusen_OD_1"];
		$Macula_Drusen_OD_2 = $_POST["Macula_Drusen_OD_2"];
		$Macula_Drusen_OD_3 = $_POST["Macula_Drusen_OD_3"];
		$Macula_Drusen_OD_4 = $_POST["Macula_Drusen_OD_4"];
		$arr = array("T"=>$Macula_Drusen_OD_T,"+1"=>$Macula_Drusen_OD_1,"+2"=>$Macula_Drusen_OD_2,"+3"=>$Macula_Drusen_OD_3,"+4"=>$Macula_Drusen_OD_4 );
		$maculaOd_drusen = 	$objTests->getTestSumm($arr,"Drusen");
		$maculaOdSummary .= $maculaOd_drusen;

		$Macula_SRNVM_OD = $_POST['Macula_SRNVM_OD'];
		$Macula_Scars_OD = $_POST['Macula_Scars_OD'];
		$Macula_Hemorrhage_OD = $_POST['Macula_Hemorrhage_OD'];
		$Macula_Microaneurysm_OD = $_POST['Macula_Microaneurysm_OD'];
		$Macula_Exudates_OD = $_POST['Macula_Exudates_OD'];
		$Macula_Normal_OD = $_POST['Macula_Normal_OD'];
		$arr = array("SRNVM"=>$Macula_SRNVM_OD,"Scars"=>$Macula_Scars_OD,"Hemorrhage"=>$Macula_Hemorrhage_OD,
				"Microaneurysm"=>$Macula_Microaneurysm_OD,"Exudates"=>$Macula_Exudates_OD,"Normal"=>$Macula_Normal_OD );
		$maculaOdSummary .= $objTests->getTestSumm($arr,"");

		$Periphery_Hemorrhage_OD = $_POST['Periphery_Hemorrhage_OD'];
		$Periphery_Microaneurysms_OD = $_POST['Periphery_Microaneurysms_OD'];
		$Periphery_Exudates_OD = $_POST['Periphery_Exudates_OD'];
		$Periphery_Cr_Scars_OD = $_POST['Periphery_Cr_Scars_OD'];
		$Periphery_NV_OD = $_POST['Periphery_NV_OD'];
		$Periphery_Nevus_OD = $_POST['Periphery_Nevus_OD'];
		$Periphery_Edema_OD = $_POST['Periphery_Edema_OD'];
		$arr = array("Hemorrhage"=>$Periphery_Hemorrhage_OD,"Microaneurysms"=>$Periphery_Microaneurysms_OD,"Exudates"=>$Periphery_Exudates_OD,
				"Cr Scars"=>$Periphery_Cr_Scars_OD,"NV"=>$Periphery_NV_OD,"Nevus"=>$Periphery_Nevus_OD,"Edema"=>$Periphery_Edema_OD );
		$retinaOdSummary .= $objTests->getTestSumm($arr,"");

		$normal_OS = $_POST["normal_OS"];

		$Macula_BDR_OS_T = $_POST["Macula_BDR_OS_T"];
		$Macula_BDR_OS_1 = $_POST["Macula_BDR_OS_1"];
		$Macula_BDR_OS_2 = $_POST["Macula_BDR_OS_2"];
		$Macula_BDR_OS_3 = $_POST["Macula_BDR_OS_3"];
		$Macula_BDR_OS_4 = $_POST["Macula_BDR_OS_4"];
		$arr = array("T"=>$Macula_BDR_OS_T,"+1"=>$Macula_BDR_OS_1,"+2"=>$Macula_BDR_OS_2,"+3"=>$Macula_BDR_OS_3,"+4"=>$Macula_BDR_OS_4 );
		$maculaOsSummary .= $objTests->getTestSumm($arr,"BDR");

		$Macula_Rpe_OS_T = $_POST["Macula_Rpe_OS_T"];
		$Macula_Rpe_OS_1 = $_POST["Macula_Rpe_OS_1"];
		$Macula_Rpe_OS_2 = $_POST["Macula_Rpe_OS_2"];
		$Macula_Rpe_OS_3 = $_POST["Macula_Rpe_OS_3"];
		$Macula_Rpe_OS_4 = $_POST["Macula_Rpe_OS_4"];
		$arr = array("T"=>$Macula_Rpe_OS_T,"+1"=>$Macula_Rpe_OS_1,"+2"=>$Macula_Rpe_OS_2,"+3"=>$Macula_Rpe_OS_3,"+4"=>$Macula_Rpe_OS_4 );
		$maculaOsSummary .= $objTests->getTestSumm($arr,"Rpe change");

		$Macula_Edema_OS_T = $_POST["Macula_Edema_OS_T"];
		$Macula_Edema_OS_1 = $_POST["Macula_Edema_OS_1"];
		$Macula_Edema_OS_2 = $_POST["Macula_Edema_OS_2"];
		$Macula_Edema_OS_3 = $_POST["Macula_Edema_OS_3"];
		$Macula_Edema_OS_4 = $_POST["Macula_Edema_OS_4"];
		$arr = array("T"=>$Macula_Edema_OS_T,"+1"=>$Macula_Edema_OS_1,"+2"=>$Macula_Edema_OS_2,"+3"=>$Macula_Edema_OS_3,"+4"=>$Macula_Edema_OS_4 );
		$maculaOsSummary .= $objTests->getTestSumm($arr,"Edema");

		$Macula_Drusen_OS_T = $_POST["Macula_Drusen_OS_T"];
		$Macula_Drusen_OS_1 = $_POST["Macula_Drusen_OS_1"];
		$Macula_Drusen_OS_2 = $_POST["Macula_Drusen_OS_2"];
		$Macula_Drusen_OS_3 = $_POST["Macula_Drusen_OS_3"];
		$Macula_Drusen_OS_4 = $_POST["Macula_Drusen_OS_4"];
		$arr = array("T"=>$Macula_Drusen_OS_T,"+1"=>$Macula_Drusen_OS_1,"+2"=>$Macula_Drusen_OS_2,"+3"=>$Macula_Drusen_OS_3,"+4"=>$Macula_Drusen_OS_4 );
		$maculaOs_drusen = $objTests->getTestSumm($arr,"Drusen");
		$maculaOsSummary .= $maculaOs_drusen;

		$Macula_SRNVM_OS = $_POST['Macula_SRNVM_OS'];
		$Macula_Scars_OS = $_POST['Macula_Scars_OS'];
		$Macula_Hemorrhage_OS = $_POST['Macula_Hemorrhage_OS'];
		$Macula_Microaneurysm_OS = $_POST['Macula_Microaneurysm_OS'];
		$Macula_Exudates_OS = $_POST['Macula_Exudates_OS'];
		$Macula_Normal_OS = $_POST['Macula_Normal_OS'];
		$arr = array("SRNVM"=>$Macula_SRNVM_OS,"Scars"=>$Macula_Scars_OS,"Hemorrhage"=>$Macula_Hemorrhage_OS,
				"Microaneurysm"=>$Macula_Microaneurysm_OS,"Exudates"=>$Macula_Exudates_OS,"Normal"=>$Macula_Normal_OS );
		$maculaOsSummary .= $objTests->getTestSumm($arr,"");

		$Periphery_Hemorrhage_OS = $_POST['Periphery_Hemorrhage_OS'];
		$Periphery_Microaneurysms_OS = $_POST['Periphery_Microaneurysms_OS'];
		$Periphery_Exudates_OS = $_POST['Periphery_Exudates_OS'];
		$Periphery_Cr_Scars_OS = $_POST['Periphery_Cr_Scars_OS'];
		$Periphery_NV_OS = $_POST['Periphery_NV_OS'];
		$Periphery_Nevus_OS = $_POST['Periphery_Nevus_OS'];
		$Periphery_Edema_OS = $_POST['Periphery_Edema_OS'];
		$arr = array("Hemorrhage"=>$Periphery_Hemorrhage_OS,"Microaneurysms"=>$Periphery_Microaneurysms_OS,"Exudates"=>$Periphery_Exudates_OS,
				"Cr Scars"=>$Periphery_Cr_Scars_OS,"NV"=>$Periphery_NV_OS,"Nevus"=>$Periphery_Nevus_OS,"Edema"=>$Periphery_Edema_OS );
		$retinaOsSummary .= $objTests->getTestSumm($arr,"");

		$Sharp_Pink_OD = $_POST["Sharp_Pink_OD"];
		$Pale_OD = $_POST["Pale_OD"];
		$Large_Cap_OD = $_POST["Large_Cap_OD"];
		$Sloping_OD = $_POST["Sloping_OD"];
		$Notch_OD = $_POST["Notch_OD"];
		$NVD_OD = $_POST["NVD_OD"];
		$Leakage_OD = $_POST["Leakage_OD"];
		$arr = array("Sharp &amp; Pink"=>$Sharp_Pink_OD, "Pale"=>$Pale_OD, "Large Cap"=>$Large_Cap_OD, "Sloping"=>$Sloping_OD,
					"Notch"=>$Notch_OD,"Leakage"=>$Leakage_OD);
		$discOdSummary .= $objTests->getTestSumm($arr,"");

		$Sharp_Pink_OS = $_POST["Sharp_Pink_OS"];
		$Pale_OS = $_POST["Pale_OS"];
		$Large_Cap_OS = $_POST["Large_Cap_OS"];
		$Sloping_OS = $_POST["Sloping_OS"];
		$Notch_OS = $_POST["Notch_OS"];
		$NVD_OS = $_POST["NVD_OS"];
		$Leakage_OS = $_POST["Leakage_OS"];
		$arr = array("Sharp &amp; Pink"=>$Sharp_Pink_OS, "Pale"=>$Pale_OS, "Large Cap"=>$Large_Cap_OS, "Sloping"=>$Sloping_OS,
					"Notch"=>$Notch_OS,"Leakage"=>$Leakage_OS);
		$discOsSummary .= $objTests->getTestSumm($arr,"");

		$discComments = imw_real_escape_string($_POST["discComments"]);
		$ptInformedNv = $_POST["elem_informedPtNv"];

		//check
		if(empty($disc_id)){

			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;

			if(isset($arrTest2edit["Disc"]) && !empty($arrTest2edit["Disc"])){

				//Check in db
				$cQry = "select disc_id FROM disc
						WHERE patientId ='".$patientId."'
						AND disc_id = '".$arrTest2edit["Disc"]."' ";
				$row = sqlQuery($cQry);
				$disc_id = (($row == false) || empty($row["disc_id"])) ? "0" : $row["disc_id"];

				//Unset Session Disc
				$arrTest2edit["Disc"] = "";
				$arrTest2edit["Disc"] = NULL;
				unset($arrTest2edit["Disc"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);

			}else{

				$cQry = "select disc_id FROM disc WHERE patientId='".$patientId."'
						AND examDate = '".$examDate."' AND examTime = '".$examTime."' ";
				$row = sqlQuery($cQry);
				$disc_id = (($row == false) || !empty($row["disc_id"])) ? "0" : $row["disc_id"];

			}
		}

		if(empty($disc_id)){
			$phyName=($zeissAction)?0:$phyName; /*Mark test uninterpreted if "Forum Add"*/
			$sql = "INSERT INTO `disc`".
					"(".
					"`disc_id`, `fundusDiscPhoto`, `photoEye`, `shots`, `extraCopy`, `discDesc`, `performedBy`, `ptUnderstanding`, ".
					"`reliabilityOd`, `reliabilityOs`, `discOd`, `discOs`, `cdOd`, `cdOs`, `retinaOd`, `retinaOs`, `maculaOd`, `maculaOs`, ".
					"`discOdSummary`, `discOsSummary`, `retinaOdSummary`, `retinaOsSummary`, `maculaOdSummary`, `maculaOsSummary`, ".
					"`resDescOd`, `resDescOs`, `stable`, `monitorAg`, `fuRetina`, `fuApa`, `ptInformed`, `signature`, `phyName`, ".
					"`examDate`, `patientId`, `formId`, `fuRetinaDesc`, ".
					"`Sharp_Pink_OD`, `Pale_OD`, `Large_Cap_OD`, `Sloping_OD`, `Notch_OD`, `NVD_OD`, `Leakage_OD`, ".
					"`Macula_BDR_OD_T`, `Macula_BDR_OD_1`, `Macula_BDR_OD_2`, `Macula_BDR_OD_3`, `Macula_BDR_OD_4`, ".
					"`Sharp_Pink_OS`, `Pale_OS`, `Large_Cap_OS`, `Sloping_OS`, `Notch_OS`, `NVD_OS`, `Leakage_OS`, ".
					"`Macula_BDR_OS_T`, `Macula_BDR_OS_1`, `Macula_BDR_OS_2`, `Macula_BDR_OS_3`, `Macula_BDR_OS_4`, ".
					"`normal_OD`, `Macula_Rpe_OD_T`, `Macula_Rpe_OD_1`, `Macula_Rpe_OD_2`, `Macula_Rpe_OD_3`, `Macula_Rpe_OD_4`, ".
					"`Macula_Edema_OD_T`, `Macula_Edema_OD_1`, `Macula_Edema_OD_2`, `Macula_Edema_OD_3`, `Macula_Edema_OD_4`, ".
					"`Macula_SRNVM_OD`, `Macula_Scars_OD`, `Macula_Hemorrhage_OD`, `Macula_Microaneurysm_OD`, `Macula_Exudates_OD`, `Macula_Normal_OD`, ".
					"`Periphery_Hemorrhage_OD`, `Periphery_Microaneurysms_OD`, `Periphery_Exudates_OD`, ".
					"`Periphery_Cr_Scars_OD`, `Periphery_NV_OD`, `Periphery_Nevus_OD`, `Periphery_Edema_OD`, ".
					"`normal_OS`, `Macula_Rpe_OS_T`, `Macula_Rpe_OS_1`, `Macula_Rpe_OS_2`, `Macula_Rpe_OS_3`, `Macula_Rpe_OS_4`, ".
					"`Macula_Edema_OS_T`, `Macula_Edema_OS_1`, `Macula_Edema_OS_2`, `Macula_Edema_OS_3`, `Macula_Edema_OS_4`, ".
					"`Macula_SRNVM_OS`, `Macula_Scars_OS`, `Macula_Hemorrhage_OS`, `Macula_Microaneurysm_OS`, `Macula_Exudates_OS`, `Macula_Normal_OS`, ".
					"`Periphery_Hemorrhage_OS`, `Periphery_Microaneurysms_OS`, `Periphery_Exudates_OS`, `discComments`, ".
					"`Periphery_Cr_Scars_OS`, `Periphery_NV_OS`, `Periphery_Nevus_OS`, `Periphery_Edema_OS`, tech2InformPt, ".
					"ptInformedNv, examTime,contiMeds,diagnosis,diagnosisOther, ".
					"maculaOd_drusen,maculaOs_drusen, ".
					"encounter_id,ordrby,ordrdt,forum_procedure  ".$signPathFieldName.$signDTmFieldName.
					") ".
					"VALUES ".
					"( ".
					"NULL, '".$fundusDiscPhoto."', '".$photoEye."', '".$shots."', '".$extraCopy."', '".$discDesc."', '".$performedBy."', '".$ptUnderstanding."', ".
					"'".$reliabilityOd."', '".$reliabilityOs."', '".$discOd."', '".$discOs."', '".$cdOd."', '".$cdOs."', '".$retinaOd."', '".$retinaOs."', '".$maculaOd."', '".$maculaOs."', ".
					"'".$discOdSummary."', '".$discOsSummary."', '".$retinaOdSummary."', '".$retinaOsSummary."', '".$maculaOdSummary."', '".$maculaOsSummary."', ".
					"'".$resDescOd."', '".$resDescOs."', '".$stable."', '".$monitorAg."', '".$fuRetina."', '".$fuApa."', '".$ptInformed."', '".$signature."', '".$phyName."', ".
					"'".$examDate."', '".$patientId."', '".$formId."', '".$fuRetinaDesc."', ".
					"'".$Sharp_Pink_OD."', '".$Pale_OD."', '".$Large_Cap_OD."', '".$Sloping_OD."', '".$Notch_OD."', '".$NVD_OD."', '".$Leakage_OD."', ".
					"'".$Macula_BDR_OD_T."', '".$Macula_BDR_OD_1."', '".$Macula_BDR_OD_2."', '".$Macula_BDR_OD_3."', '".$Macula_BDR_OD_4."', ".
					"'".$Sharp_Pink_OS."', '".$Pale_OS."', '".$Large_Cap_OS."', '".$Sloping_OS."', '".$Notch_OS."', '".$NVD_OS."', '".$Leakage_OS."', ".
					"'".$Macula_BDR_OS_T."', '".$Macula_BDR_OS_1."', '".$Macula_BDR_OS_2."', '".$Macula_BDR_OS_3."', '".$Macula_BDR_OS_4."', ".
					"'".$normal_OD."', '".$Macula_Rpe_OD_T."', '".$Macula_Rpe_OD_1."', '".$Macula_Rpe_OD_2."', '".$Macula_Rpe_OD_3."', '".$Macula_Rpe_OD_4."', ".
					"'".$Macula_Edema_OD_T."', '".$Macula_Edema_OD_1."', '".$Macula_Edema_OD_2."', '".$Macula_Edema_OD_3."', '".$Macula_Edema_OD_4."', ".
					"'".$Macula_SRNVM_OD."', '".$Macula_Scars_OD."', '".$Macula_Hemorrhage_OD."', '".$Macula_Microaneurysm_OD."', '".$Macula_Exudates_OD."', '".$Macula_Normal_OD."', ".
					"'".$Periphery_Hemorrhage_OD."', '".$Periphery_Microaneurysms_OD."', '".$Periphery_Exudates_OD."', ".
					"'".$Periphery_Cr_Scars_OD."', '".$Periphery_NV_OD."', '".$Periphery_Nevus_OD."', '".$Periphery_Edema_OD."', ".
					"'".$normal_OS."', '".$Macula_Rpe_OS_T."', '".$Macula_Rpe_OS_1."', '".$Macula_Rpe_OS_2."', '".$Macula_Rpe_OS_3."', '".$Macula_Rpe_OS_4."', ".
					"'".$Macula_Edema_OS_T."', '".$Macula_Edema_OS_1."', '".$Macula_Edema_OS_2."', '".$Macula_Edema_OS_3."', '".$Macula_Edema_OS_4."', ".
					"'".$Macula_SRNVM_OS."', '".$Macula_Scars_OS."', '".$Macula_Hemorrhage_OS."', '".$Macula_Microaneurysm_OS."', '".$Macula_Exudates_OS."', '".$Macula_Normal_OS."', ".
					"'".$Periphery_Hemorrhage_OS."', '".$Periphery_Microaneurysms_OS."', '".$Periphery_Exudates_OS."', '".$discComments."', ".
					"'".$Periphery_Cr_Scars_OS."', '".$Periphery_NV_OS."', '".$Periphery_Nevus_OS."', '".$Periphery_Edema_OS."', '".$tech2InformPt."', ".
					"'".$ptInformedNv."', '".$examTime."','".$contiMeds."','".$diagnosis."','".$diagnosisOther."', ".
					"'".$maculaOd_drusen."', '".$maculaOs_drusen."', ".
					"'".$encounter_id."','".$ordrby."','".$ordrdt."', '".$forum_procedure."' ".$signPathFieldValue.$signDTmFieldValue.
					") ";
			$insertId = imw_query($sql);
			$disc_id = imw_insert_id();			
		}else if(!empty($disc_id)){
			$sql = "UPDATE disc ".
				 "SET ".
				 "fundusDiscPhoto='".$fundusDiscPhoto."', ".
				 "photoEye='".$photoEye."', ".
				 "shots='".$shots."', ".
				 "extraCopy='".$extraCopy."', ".
				 "discDesc='".$discDesc."', ".
				 "performedBy='".$performedBy."', ".
				 "ptUnderstanding='".$ptUnderstanding."', ".
				 "reliabilityOd='".$reliabilityOd."', ".
				 "reliabilityOs='".$reliabilityOs."', ".
				 "discOd='".$discOd."', ".
				 "discOs='".$discOs."', ".
				 "cdOd='".$cdOd."', ".
				 "cdOs='".$cdOs."', ".
				 "retinaOd='".$retinaOd."', ".
				 "retinaOs='".$retinaOs."', ".
				 "maculaOd='".$maculaOd."', ".
				 "maculaOs='".$maculaOs."', ".
				 "discOdSummary='".$discOdSummary."', ".
				 "discOsSummary='".$discOsSummary."', ".
				 "retinaOdSummary='".$retinaOdSummary."', ".
				 "retinaOsSummary='".$retinaOsSummary."', ".
				 "maculaOdSummary='".$maculaOdSummary."', ".
				 "maculaOsSummary='".$maculaOsSummary."', ".
				 "resDescOd='".$resDescOd."', ".
				 "resDescOs='".$resDescOs."', ".
				 "stable='".$stable."', ".
				 "monitorAg='".$monitorAg."', ".
				 "fuRetina='".$fuRetina."', ".
				 "fuApa='".$fuApa."', ".
				 "ptInformed='".$ptInformed."', ".
				 "signature='".$signature."', ";
				 
				if(!$zeissAction){  /*Prevent changing interpretation status if "Forum Add/Delete"*/
					$sql .= "phyName = '".$phyName."', ";
				}
				 
			$sql .= "examDate='".$examDate."', ".
				 //"patientId='".$patientId."', ".
				 "fuRetinaDesc='".$fuRetinaDesc."', ".
				 "Sharp_Pink_OD ='".$Sharp_Pink_OD."', ".
				 "Pale_OD ='".$Pale_OD."', ".
				 "Large_Cap_OD ='".$Large_Cap_OD."', ".
				 "Sloping_OD ='".$Sloping_OD."', ".
				 "Notch_OD ='".$Notch_OD."', ".
				 "NVD_OD ='".$NVD_OD."', ".
				 "Leakage_OD ='".$Leakage_OD."', ".
				 "normal_OD ='".$normal_OD."', ".
				 "Macula_Rpe_OD_T ='".$Macula_Rpe_OD_T."', ".
				 "Macula_Rpe_OD_1 ='".$Macula_Rpe_OD_1."', ".
				 "Macula_Rpe_OD_2 ='".$Macula_Rpe_OD_2."', ".
				 "Macula_Rpe_OD_3 ='".$Macula_Rpe_OD_3."', ".
				 "Macula_Rpe_OD_4 ='".$Macula_Rpe_OD_4."', ".
				 "Macula_Edema_OD_T ='".$Macula_Edema_OD_T."', ".
				 "Macula_Edema_OD_1 ='".$Macula_Edema_OD_1."', ".
				 "Macula_Edema_OD_2 ='".$Macula_Edema_OD_2."', ".
				 "Macula_Edema_OD_3 ='".$Macula_Edema_OD_3."', ".
				 "Macula_Edema_OD_4 ='".$Macula_Edema_OD_4."', ".
				 "Macula_SRNVM_OD ='".$Macula_SRNVM_OD."', ".
				 "Macula_Scars_OD ='".$Macula_Scars_OD."', ".
				 "Macula_Hemorrhage_OD ='".$Macula_Hemorrhage_OD."', ".
				 "Macula_Microaneurysm_OD ='".$Macula_Microaneurysm_OD."', ".
				 "Macula_Exudates_OD ='".$Macula_Exudates_OD."', ".
				 "Macula_Normal_OD ='".$Macula_Normal_OD."', ".
				 "Periphery_Hemorrhage_OD ='".$Periphery_Hemorrhage_OD."', ".
				 "Periphery_Microaneurysms_OD ='".$Periphery_Microaneurysms_OD."', ".
				 "Periphery_Exudates_OD ='".$Periphery_Exudates_OD."', ".
				 "Periphery_Cr_Scars_OD ='".$Periphery_Cr_Scars_OD."', ".
				 "Periphery_NV_OD ='".$Periphery_NV_OD."', ".
				 "Periphery_Nevus_OD ='".$Periphery_Nevus_OD."', ".
				 "Periphery_Edema_OD ='".$Periphery_Edema_OD."', ".
				 "Macula_BDR_OD_T ='".$Macula_BDR_OD_T."', ".
				 "Macula_BDR_OD_1 ='".$Macula_BDR_OD_1."', ".
				 "Macula_BDR_OD_2 ='".$Macula_BDR_OD_2."', ".
				 "Macula_BDR_OD_3 ='".$Macula_BDR_OD_3."', ".
				 "Macula_BDR_OD_4 ='".$Macula_BDR_OD_4."', ".
				 "normal_OS ='".$normal_OS."', ".
				 "Sharp_Pink_OS ='".$Sharp_Pink_OS."', ".
				 "Pale_OS ='".$Pale_OS."', ".
				 "Large_Cap_OS ='".$Large_Cap_OS."', ".
				 "Sloping_OS ='".$Sloping_OS."', ".
				 "Notch_OS ='".$Notch_OS."', ".
				 "NVD_OS ='".$NVD_OS."', ".
				 "Leakage_OS ='".$Leakage_OS."', ".
				 "Macula_Rpe_OS_T ='".$Macula_Rpe_OS_T."', ".
				 "Macula_Rpe_OS_1 ='".$Macula_Rpe_OS_1."', ".
				 "Macula_Rpe_OS_2 ='".$Macula_Rpe_OS_2."', ".
				 "Macula_Rpe_OS_3 ='".$Macula_Rpe_OS_3."', ".
				 "Macula_Rpe_OS_4 ='".$Macula_Rpe_OS_4."', ".
				 "Macula_Edema_OS_T ='".$Macula_Edema_OS_T."', ".
				 "Macula_Edema_OS_1 ='".$Macula_Edema_OS_1."', ".
				 "Macula_Edema_OS_2 ='".$Macula_Edema_OS_2."', ".
				 "Macula_Edema_OS_3 ='".$Macula_Edema_OS_3."', ".
				 "Macula_Edema_OS_4 ='".$Macula_Edema_OS_4."', ".
				 "Macula_SRNVM_OS ='".$Macula_SRNVM_OS."', ".
				 "Macula_Scars_OS ='".$Macula_Scars_OS."', ".
				 "Macula_Hemorrhage_OS ='".$Macula_Hemorrhage_OS."', ".
				 "Macula_Microaneurysm_OS ='".$Macula_Microaneurysm_OS."', ".
				 "Macula_Exudates_OS ='".$Macula_Exudates_OS."', ".
				 "Macula_Normal_OS ='".$Macula_Normal_OS."', ".
				 "Periphery_Hemorrhage_OS ='".$Periphery_Hemorrhage_OS."', ".
				 "Periphery_Microaneurysms_OS ='".$Periphery_Microaneurysms_OS."', ".
				 "Periphery_Exudates_OS ='".$Periphery_Exudates_OS."', ".
				 "discComments ='".$discComments."', ".
				 "Periphery_Cr_Scars_OS ='".$Periphery_Cr_Scars_OS."', ".
				 "Periphery_NV_OS ='".$Periphery_NV_OS."', ".
				 "Periphery_Nevus_OS ='".$Periphery_Nevus_OS."', ".
				 "Periphery_Edema_OS ='".$Periphery_Edema_OS."', ".
				 "Macula_BDR_OS_T ='".$Macula_BDR_OS_T."', ".
				 "Macula_BDR_OS_1 ='".$Macula_BDR_OS_1."', ".
				 "Macula_BDR_OS_2 ='".$Macula_BDR_OS_2."', ".
				 "Macula_BDR_OS_3 ='".$Macula_BDR_OS_3."', ".
				 "Macula_BDR_OS_4 ='".$Macula_BDR_OS_4."', ".
				 "tech2InformPt = '".$tech2InformPt."', ".
				 "ptInformedNv = '".$ptInformedNv."', ".
				 "examTime = '".$examTime."', ".
				 "contiMeds = '".$contiMeds."', ".
				 "diagnosis = '".$diagnosis."', ".
				 "diagnosisOther = '".$diagnosisOther."', ".
				 "maculaOd_drusen = '".$maculaOd_drusen."', ".
				 "maculaOs_drusen = '".$maculaOs_drusen."', ".
				 "encounter_id='".$encounter_id."', ".
				 "forum_procedure='".$forum_procedure."', ".
				 $signPathQry.
				 $signDTmQry.
				 "ordrby='".$ordrby."',ordrdt='".$ordrdt."' ".
				 //"WHERE formId='".$formId."' ";
				 "WHERE disc_id='".$disc_id."' ";
			$res = sqlQuery($sql);
		}
		
		/*code, Purpose: Generate HL7 message for Test*/
		 if(constant("ZEISS_FORUM") == "YES"){
			$zeissMsgType="DISC";
			$zeissPatientId = $patientId;
			$zeissTestId = $disc_id;
			include("./zeissTestHl7.php");
		 }
		/*End code*/
		

		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "Disc", $disc_id);
		/*--interpretation end--*/
		//$cls_notifications->set_notification_status('testseye');
		
		//Super-Bill -----------
		if(!empty($ordrby) && !empty($disc_id)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$disc_id;
			$arIn["sb_testName"]="Fundus";
                        $arIn["form_id"]=$formId;
                        $arIn["test_interpreted"]= ((int)$phyName > 0)? true : false;
			
			$oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);			
		}
		

		//save tests Pdf
		$objTests->saveTestPdfExe_2($patientId,$disc_id,"Fundus");

		//FormId updates other tables -----
		if(!empty($formId)){
			// Make chart notes valid
			$oChartNote->makeChartNotesValid($formId);
		}
		//FormId updates other tables -----
		//Redirect
		//require_once(getcwd()."/save_charts_js.php");
		//Javascript ----------
		echo "<script>".
				"try{
					";
					if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
						$afterSaveinPopup = 'yes';
					}else{
						$afterSaveinPopup = 'no';
						echo "top.alert_notification_show(\"Test is saved.\");";
					}
					echo "
					window.location.replace(\"test_disc.php?noP=".$elem_noP."&tId=".$disc_id."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
				}catch(err){}".
			 "</script>";
		exit();
		//Javascript ----------
		break;
	}
	case "discExternal":{
		$discOdSummary = "";
		$retinaOdSummary = "";
		$maculaOdSummary = "";

		$discOsSummary = "";
		$retinaOsSummary = "";
		$maculaOsSummary = "";

		$patientId = $_POST["elem_patientId"];
		$formId = $_POST["elem_formId"];
		$hd_disc_mode = $_POST["hd_disc_mode"];
		$disc_id = $_POST["elem_discId"];
		$examDate = getDateFormatDB($_POST["elem_examDate"]);
		if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
		$fundusDiscPhoto = $_POST["elem_fundusDiscPhoto"];
		$shots = $_POST["elem_shots"];
		$extraCopy = $_POST["elem_extraCopy"];
		$discDesc = ($_POST["elem_desc"] == 'Technician Comments') ? "" : imw_real_escape_string($_POST["elem_desc"]);
		$photoEye = $_POST["elem_photoEye"];
		$performedBy = $_POST["elem_performedBy"];
		$ptUnderstanding = $_POST["elem_ptUnderstanding"];
		$reliabilityOd = $_POST["elem_reliabilityOd"];
		$reliabilityOs = $_POST["elem_reliabilityOs"];
		$discOd = $_POST["elem_discOd"];
		$cdOd = $_POST["elem_cdOd"];
		$retinaOd = $_POST["elem_retinaOd"];
		$maculaOd = $_POST["elem_maculaOd"];
		$resDescOd = imw_real_escape_string($_POST["elem_resDescOd"]);
		$discOs = $_POST["elem_discOs"];
		$cdOs = $_POST["elem_cdOs"];
		$retinaOs = $_POST["elem_retinaOs"];
		$maculaOs = $_POST["elem_maculaOs"];
		$resDescOs = imw_real_escape_string($_POST["elem_resDescOs"]);
		$stable = $_POST["elem_stable"];
		$monitorAg = $_POST["elem_monitorAg"];
		$fuApa = $_POST["elem_fuApa"];
		$ptInformed = $_POST["elem_ptInformed"];
		$fuRetina = $_POST["elem_fuRetina"];
		$fuRetinaDesc = imw_real_escape_string($_POST["elem_fuRetinaDesc"]);
		$signature = $_POST["elem_signature"];
		$phyName = $_POST["elem_physician"];
		$normal_OD = $_POST['normal_OD'];
		$tech2InformPt = $_POST["elem_tech2InformPt"];
		$contiMeds = $_POST["elem_contiMeds"];
		$diagnosis = imw_real_escape_string($_POST["elem_diagnosis"]);
		$diagnosisOther = imw_real_escape_string($_POST["elem_diagnosisOther"]);
		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
		
		$forum_procedure = (isset($_REQUEST['forum_procedure']))?imw_real_escape_string($_REQUEST['forum_procedure']):"";
		$zeissAction = (isset($_REQUEST['forum_procedure']))?$_POST['zeissAction']:false;

		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
			$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
			$encounter_id = $objTests->getEncounterId();
		}

		$ptosisOd_neg = $_POST["elem_ptosisOd_neg"];
		$ptosisOd_T = $_POST["elem_ptosisOd_T"];
		$ptosisOd_pos1 = $_POST["elem_ptosisOd_pos1"];
		$ptosisOd_pos2 = $_POST["elem_ptosisOd_pos2"];
		$ptosisOd_pos3 = $_POST["elem_ptosisOd_pos3"];
		/* $arr = array("T"=>$Macula_BDR_OD_T,"+1"=>$Macula_BDR_OD_1,"+2"=>$Macula_BDR_OD_2,"+3"=>$Macula_BDR_OD_3,"+4"=>$Macula_BDR_OD_4 );
		$maculaOdSummary .= $objTests->getTestSumm($arr,"BDR"); */

		$ptosisOd_pos4 = $_POST["elem_ptosisOd_pos4"];
		$ptosisOd_rul = $_POST["elem_ptosisOd_rul"];
		$ptosisOd_rll = $_POST["elem_ptosisOd_rll"];
		$dermaOd_neg = $_POST["elem_dermaOd_neg"];
		$dermaOd_T = $_POST["elem_dermaOd_T"];
		/* $arr = array("T"=>$Macula_Rpe_OD_T,"+1"=>$Macula_Rpe_OD_1,"+2"=>$Macula_Rpe_OD_2,"+3"=>$Macula_Rpe_OD_3,"+4"=>$Macula_Rpe_OD_4 );
		$maculaOdSummary .= $objTests->getTestSumm($arr,"Rpe change"); */

		$dermaOd_pos1 = $_POST["elem_dermaOd_pos1"];
		$dermaOd_pos2 = $_POST["elem_dermaOd_pos2"];
		$dermaOd_pos3 = $_POST["elem_dermaOd_pos3"];
		$dermaOd_pos4 = $_POST["elem_dermaOd_pos4"];
		$dermaOd_rul = $_POST["elem_dermaOd_rul"];
		/* $arr = array("T"=>$Macula_Edema_OD_T,"+1"=>$Macula_Edema_OD_1,"+2"=>$Macula_Edema_OD_2,"+3"=>$Macula_Edema_OD_3,"+4"=>$Macula_Edema_OD_4 );
		$maculaOdSummary .= $objTests->getTestSumm($arr,"Edema");	 */

		$dermaOd_rll = $_POST['elem_dermaOd_rll'];
		$pterygium1mmOd = $_POST['elem_pterygium1mmOd'];
		$pterygium2mmOd = $_POST['elem_pterygium2mmOd'];
		$pterygium3mmOd = $_POST['elem_pterygium3mmOd'];
		$pterygium4mmOd = $_POST['elem_pterygium4mmOd'];
		$pterygium5mmOd = $_POST['elem_pterygium5mmOd'];
		/* $arr = array("SRNVM"=>$Macula_SRNVM_OD,"Scars"=>$Macula_Scars_OD,"Hemorrhage"=>$Macula_Hemorrhage_OD,
				"Microaneurysm"=>$Macula_Microaneurysm_OD,"Exudates"=>$Macula_Exudates_OD,"Normal"=>$Macula_Normal_OD );
		$maculaOdSummary .= $objTests->getTestSumm($arr,""); */

		$pterygiumNasalOd = $_POST['elem_pterygiumNasalOd'];
		$pterygiumTemporalOd = $_POST['elem_pterygiumTemporalOd'];
		$vascOd_Stromal = $_POST['elem_vascOd_Stromal'];
		$vascOd_SubEpithelial = $_POST['elem_vascOd_SubEpithelial'];
		$vascOd_Superficial = $_POST['elem_vascOd_Superficial'];
		$vascOd_Deep = $_POST['elem_vascOd_Deep'];
		$vascOd_Endothelial = $_POST['elem_vascOd_Endothelial'];
		$vascOd_Peripheral = $_POST['elem_vascOd_Peripheral'];

		/* $arr = array("Hemorrhage"=>$Periphery_Hemorrhage_OD,"Microaneurysms"=>$Periphery_Microaneurysms_OD,"Exudates"=>$Periphery_Exudates_OD,
				"Cr Scars"=>$Periphery_Cr_Scars_OD,"NV"=>$Periphery_NV_OD,"Nevus"=>$Periphery_Nevus_OD,"Edema"=>$Periphery_Edema_OD );
		$retinaOdSummary .= $objTests->getTestSumm($arr,""); */

		$normal_OS = $_POST["normal_OS"];

		$vascOd_Central = $_POST["elem_vascOd_Central"];
		$vascOd_Pannus = $_POST["elem_vascOd_Pannus"];
		$vascOd_GhostBV = $_POST["elem_vascOd_GhostBV"];
		$vascOd_Superior = $_POST["elem_vascOd_Superior"];
		$vascOd_Inferior = $_POST["elem_vascOd_Inferior"];
		$vascOd_Nasal = $_POST["elem_vascOd_Nasal"];
		/* $arr = array("T"=>$Macula_BDR_OS_T,"+1"=>$Macula_BDR_OS_1,"+2"=>$Macula_BDR_OS_2,"+3"=>$Macula_BDR_OS_3,"+4"=>$Macula_BDR_OS_4 );
		$maculaOsSummary .= $objTests->getTestSumm($arr,"BDR"); */

		$vascOd_Temporal = $_POST["elem_vascOd_Temporal"];
		$NevusOd_neg = $_POST["elem_NevusOd_neg"];
		$NevusOd_Pos = $_POST["elem_NevusOd_Pos"];
		$NevusOd_Inferior = $_POST["elem_NevusOd_Inferior"];
		$NevusOd_Superior = $_POST["elem_NevusOd_Superior"];
		/* $arr = array("T"=>$Macula_Rpe_OS_T,"+1"=>$Macula_Rpe_OS_1,"+2"=>$Macula_Rpe_OS_2,"+3"=>$Macula_Rpe_OS_3,"+4"=>$Macula_Rpe_OS_4 );
		$maculaOsSummary .= $objTests->getTestSumm($arr,"Rpe change"); */

		$NevusOd_Temporal = $_POST["elem_NevusOd_Temporal"];
		$NevusOd_Nasal = $_POST["elem_NevusOd_Nasal"];
		$ptosisOs_neg = $_POST["elem_ptosisOs_neg"];
		$ptosisOs_T = $_POST["elem_ptosisOs_T"];
		$ptosisOs_pos1 = $_POST["elem_ptosisOs_pos1"];
		/* $arr = array("T"=>$Macula_Edema_OS_T,"+1"=>$Macula_Edema_OS_1,"+2"=>$Macula_Edema_OS_2,"+3"=>$Macula_Edema_OS_3,"+4"=>$Macula_Edema_OS_4 );
		$maculaOsSummary .= $objTests->getTestSumm($arr,"Edema");		 */

		$ptosisOs_pos2 = $_POST['elem_ptosisOs_pos2'];
		$ptosisOs_pos3 = $_POST['elem_ptosisOs_pos3'];
		$ptosisOs_pos4 = $_POST['elem_ptosisOs_pos4'];
		$ptosisOs_rul = $_POST['elem_ptosisOs_rul'];
		$ptosisOs_rll = $_POST['elem_ptosisOs_rll'];
		$dermaOs_neg = $_POST['elem_dermaOs_neg'];
		/* $arr = array("SRNVM"=>$Macula_SRNVM_OS,"Scars"=>$Macula_Scars_OS,"Hemorrhage"=>$Macula_Hemorrhage_OS,
				"Microaneurysm"=>$Macula_Microaneurysm_OS,"Exudates"=>$Macula_Exudates_OS,"Normal"=>$Macula_Normal_OS );
		$maculaOsSummary .= $objTests->getTestSumm($arr,""); */

		$dermaOs_T = $_POST['elem_dermaOs_T'];
		$dermaOs_pos1 = $_POST['elem_dermaOs_pos1'];
		$dermaOs_pos2 = $_POST['elem_dermaOs_pos2'];
		$dermaOs_pos3 = $_POST['elem_dermaOs_pos3'];
		$dermaOs_pos4 = $_POST['elem_dermaOs_pos4'];
		$dermaOs_rul = $_POST['elem_dermaOs_rul'];
		$dermaOs_rll = $_POST['elem_dermaOs_rll'];
		/* $arr = array("Hemorrhage"=>$Periphery_Hemorrhage_OS,"Microaneurysms"=>$Periphery_Microaneurysms_OS,"Exudates"=>$Periphery_Exudates_OS,
				"Cr Scars"=>$Periphery_Cr_Scars_OS,"NV"=>$Periphery_NV_OS,"Nevus"=>$Periphery_Nevus_OS,"Edema"=>$Periphery_Edema_OS );
		$retinaOsSummary .= $objTests->getTestSumm($arr,""); */

		$pterygium1mmOs = $_POST["elem_pterygium1mmOs"];
		$pterygium2mmOs = $_POST["elem_pterygium2mmOs"];
		$pterygium3mmOs = $_POST["elem_pterygium3mmOs"];
		$pterygium4mmOs = $_POST["elem_pterygium4mmOs"];
		$pterygium5mmOs = $_POST["elem_pterygium5mmOs"];
		$pterygiumNasalOs = $_POST["elem_pterygiumNasalOs"];
		$pterygiumTemporalOs = $_POST["elem_pterygiumTemporalOs"];
		/* $arr = array("Sharp &amp; Pink"=>$Sharp_Pink_OD, "Pale"=>$Pale_OD, "Large Cap"=>$Large_Cap_OD, "Sloping"=>$Sloping_OD,
					"Notch"=>$Notch_OD,"Leakage"=>$Leakage_OD);
		$discOdSummary .= $objTests->getTestSumm($arr,""); */

		$vascOs_SubEpithelial = $_POST["elem_vascOs_SubEpithelial"];
		$vascOs_Stromal = $_POST["elem_vascOs_Stromal"];
		$vascOs_Superficial = $_POST["elem_vascOs_Superficial"];
		$vascOs_Deep = $_POST["elem_vascOs_Deep"];
		$vascOs_Endothelial = $_POST["elem_vascOs_Endothelial"];
		$vascOs_Peripheral = $_POST["elem_vascOs_Peripheral"];
		$vascOs_Central = $_POST["elem_vascOs_Central"];

		$vascOs_Pannus = $_POST["elem_vascOs_Pannus"];
		$vascOs_GhostBV = $_POST["elem_vascOs_GhostBV"];
		$vascOs_Superior = $_POST["elem_vascOs_Superior"];
		$vascOs_Inferior = $_POST["elem_vascOs_Inferior"];
		$vascOs_Nasal = $_POST["elem_vascOs_Nasal"];
		$vascOs_Temporal = $_POST["elem_vascOs_Temporal"];
		$irisNevusOs_neg = $_POST["elem_irisNevusOs_neg"];
		$irisNevusOs_Pos = $_POST["elem_irisNevusOs_Pos"];
		$irisNevusOs_Inferior = $_POST["elem_irisNevusOs_Inferior"];
		$irisNevusOs_Superior = $_POST["elem_irisNevusOs_Superior"];
		$irisNevusOs_Temporal = $_POST["elem_irisNevusOs_Temporal"];
		$irisNevusOs_Nasal = $_POST["elem_irisNevusOs_Nasal"];
		/* $arr = array("Sharp &amp; Pink"=>$Sharp_Pink_OS, "Pale"=>$Pale_OS, "Large Cap"=>$Large_Cap_OS, "Sloping"=>$Sloping_OS,
					"Notch"=>$Notch_OS,"Leakage"=>$Leakage_OS);
		$discOsSummary .= $objTests->getTestSumm($arr,""); */

		$discComments = imw_real_escape_string($_POST["discComments"]);
		$ptInformedNv = $_POST["elem_informedPtNv"];
		$elem_noP = $_POST["elem_noP"];
		$examTime = $_POST["elem_examTime"];


		//check
		if(empty($disc_id)){

			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;

			if(isset($arrTest2edit["discExternal"]) && !empty($arrTest2edit["discExternal"])){

				//Check in db
				$cQry = "select disc_id FROM disc_external
						WHERE patientId ='".$patientId."'
						AND disc_id = '".$arrTest2edit["discExternal"]."' ";
				$row = sqlQuery($cQry);
				$disc_id = (($row == false) || empty($row["disc_id"])) ? "0" : $row["disc_id"];

				//Unset Session discExternal
				$arrTest2edit["discExternal"] = "";
				$arrTest2edit["discExternal"] = NULL;
				unset($arrTest2edit["discExternal"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);

			}else{

				$cQry = "select disc_id FROM disc_external WHERE patientId='".$patientId."'
						AND examDate = '".$examDate."' AND examTime = '".$examTime."' ";
				$row = sqlQuery($cQry);
				$disc_id = (($row == false) || empty($row["disc_id"])) ? "0" : $row["disc_id"];

			}

		}

		if(empty($disc_id)){
			 $sql = "Insert into disc_external ".
				 "SET ".
				 "fundusDiscPhoto='".$fundusDiscPhoto."', ".
				 "photoEye='".$photoEye."', ".
				 "shots='".$shots."', ".
				 "extraCopy='".$extraCopy."', ".
				 "discDesc='".$discDesc."', ".
				 "performedBy='".$performedBy."', ".
				 "ptUnderstanding='".$ptUnderstanding."', ".
				 "reliabilityOd='".$reliabilityOd."', ".
				 "reliabilityOs='".$reliabilityOs."', ".
				 "discOd='".$discOd."', ".
				 "discOs='".$discOs."', ".

				 "resDescOd='".$resDescOd."', ".
				 "resDescOs='".$resDescOs."', ".
				 "stable='".$stable."', ".
				 "monitorAg='".$monitorAg."', ".
				 "fuRetina='".$fuRetina."', ".
				 "fuApa='".$fuApa."', ".
				 "ptInformed='".$ptInformed."', ".
				 "signature='".$signature."', ";
				 
				if(!$zeissAction){  /*Mark test uninterpretation if "Forum Add"*/
					$sql .= "phyName = '".$phyName."', ";
				}
				
			 $sql .= "examDate='".$examDate."', ".
				 "patientId='".$patientId."', ".
				 "formId='".$formId."', ".
				 "fuRetinaDesc='".$fuRetinaDesc."', ".
				 "ptosisOd_neg ='".$ptosisOd_neg."', ".
				 "ptosisOd_T ='".$ptosisOd_T."', ".
				 "ptosisOd_pos1 ='".$ptosisOd_pos1."', ".
				 "ptosisOd_pos2 ='".$ptosisOd_pos2."', ".
				 "ptosisOd_pos3 ='".$ptosisOd_pos3."', ".
				 "ptosisOd_pos4 ='".$ptosisOd_pos4."', ".
				 "ptosisOd_rul ='".$ptosisOd_rul."', ".
				 "normal_OD ='".$normal_OD."', ".
				 "ptosisOd_rll ='".$ptosisOd_rll."', ".
				 "dermaOd_neg ='".$dermaOd_neg."', ".
				 "dermaOd_T ='".$dermaOd_T."', ".
				 "dermaOd_pos1 ='".$dermaOd_pos1."', ".
				 "dermaOd_pos2 ='".$dermaOd_pos2."', ".
				 "dermaOd_pos3 ='".$dermaOd_pos3."', ".
				 "dermaOd_pos4 ='".$dermaOd_pos4."', ".
				 "dermaOd_rul ='".$dermaOd_rul."', ".
				 "dermaOd_rll ='".$dermaOd_rll."', ".
				 "pterygium1mmOd ='".$pterygium1mmOd."', ".
				 "pterygium2mmOd ='".$pterygium2mmOd."', ".
				 "pterygium3mmOd ='".$pterygium3mmOd."', ".
				 "pterygium4mmOd ='".$pterygium4mmOd."', ".
				 "pterygium5mmOd ='".$pterygium5mmOd."', ".
				 "pterygiumNasalOd ='".$pterygiumNasalOd."', ".
				 "pterygiumTemporalOd ='".$pterygiumTemporalOd."', ".
				 "vascOd_SubEpithelial ='".$vascOd_SubEpithelial."', ".
				 "vascOd_Stromal ='".$vascOd_Stromal."', ".
				 "vascOd_Superficial ='".$vascOd_Superficial."', ".
				 "vascOd_Deep ='".$vascOd_Deep."', ".
				 "vascOd_Endothelial ='".$vascOd_Endothelial."', ".
				 "vascOd_Peripheral ='".$vascOd_Peripheral."', ".
				 "vascOd_Central ='".$vascOd_Central."', ".
				 "vascOd_Pannus ='".$vascOd_Pannus."', ".
				 "vascOd_GhostBV ='".$vascOd_GhostBV."', ".
				 "vascOd_Superior='".$vascOd_Superior."', ".
				 "vascOd_Inferior ='".$vascOd_Inferior."', ".
				 "vascOd_Nasal ='".$vascOd_Nasal."', ".
				 "vascOd_Temporal ='".$vascOd_Temporal."', ".
				 "NevusOd_neg ='".$NevusOd_neg."', ".
				 "NevusOd_Pos ='".$NevusOd_Pos."', ".
				 "NevusOd_Inferior ='".$NevusOd_Inferior."', ".
				 "NevusOd_Superior ='".$NevusOd_Superior."', ".
				 "NevusOd_Temporal ='".$NevusOd_Temporal."', ".
				 "NevusOd_Nasal ='".$NevusOd_Nasal."', ".
				 "ptosisOs_neg ='".$ptosisOs_neg."', ".
				 "ptosisOs_T ='".$ptosisOs_T."', ".
				 "ptosisOs_pos1 ='".$ptosisOs_pos1."', ".
				 "ptosisOs_pos2 ='".$ptosisOs_pos2."', ".
				 "ptosisOs_pos3 ='".$ptosisOs_pos3."', ".
				 "ptosisOs_pos4 ='".$ptosisOs_pos4."', ".
				 "ptosisOs_rul ='".$ptosisOs_rul."', ".
				 "ptosisOs_rll ='".$ptosisOs_rll."', ".
				 "dermaOs_neg ='".$dermaOs_neg."', ".
				 "dermaOs_T ='".$dermaOs_T."', ".
				 "dermaOs_pos1 ='".$dermaOs_pos1."', ".
				 "dermaOs_pos2 ='".$dermaOs_pos2."', ".
				 "dermaOs_pos3 ='".$dermaOs_pos3."', ".
				 "dermaOs_pos4 ='".$dermaOs_pos4."', ".
				 "dermaOs_rul ='".$dermaOs_rul."', ".
				 "dermaOs_rll ='".$dermaOs_rll."', ".
				 "pterygium1mmOs ='".$pterygium1mmOs."', ".
				 "pterygium2mmOs ='".$pterygium2mmOs."', ".
				 "pterygium3mmOs ='".$pterygium3mmOs."', ".
				 "pterygium4mmOs ='".$pterygium4mmOs."', ".
				 "pterygium5mmOs ='".$pterygium5mmOs."', ".
				 "pterygiumNasalOs ='".$pterygiumNasalOs."', ".
				 "discComments ='".$discComments."', ".
				 "pterygiumTemporalOs ='".$pterygiumTemporalOs."', ".
				 "vascOs_SubEpithelial ='".$vascOs_SubEpithelial."', ".
				 "vascOs_Stromal ='".$vascOs_Stromal."', ".
				 "vascOs_Superficial ='".$vascOs_Superficial."', ".
				 "vascOs_Deep ='".$vascOs_Deep."', ".
				 "vascOs_Endothelial ='".$vascOs_Endothelial."', ".
				 "vascOs_Peripheral ='".$vascOs_Peripheral."', ".
				 "vascOs_Central ='".$vascOs_Central."', ".
				 "vascOs_Pannus ='".$vascOs_Pannus."', ".
				 "vascOs_GhostBV ='".$vascOs_GhostBV."', ".
				 "vascOs_Superior ='".$vascOs_Superior."', ".
				 "vascOs_Inferior ='".$vascOs_Inferior."', ".
				 "vascOs_Nasal ='".$vascOs_Nasal."', ".
				 "vascOs_Temporal ='".$vascOs_Temporal."', ".
				 "irisNevusOs_neg ='".$irisNevusOs_neg."', ".
				 "irisNevusOs_Pos ='".$irisNevusOs_Pos."', ".
				 "irisNevusOs_Inferior ='".$irisNevusOs_Inferior."', ".
				 "irisNevusOs_Superior ='".$irisNevusOs_Superior."', ".
				 "irisNevusOs_Temporal ='".$irisNevusOs_Temporal."', ".
				 "irisNevusOs_Nasal ='".$irisNevusOs_Nasal."', ".
				 "tech2InformPt = '".$tech2InformPt."', ".
				 "ptInformedNv = '".$ptInformedNv."', ".
				 "examTime = '".$examTime."', ".
				 "contiMeds = '".$contiMeds."', ".
				 "diagnosis = '".$diagnosis."', ".
				 "diagnosisOther = '".$diagnosisOther."', ".
				 "encounter_id='".$encounter_id."', ".
				 "forum_procedure='".$forum_procedure."', ".
				 $signPathQry.
				 $signDTmQry.
				 "ordrby='".$ordrby."',ordrdt='".$ordrdt."' ".
				 "";
			$insertId = sqlInsert($sql);
			$disc_id = $insertId;			
		}else if(!empty($disc_id)){
			$sql = "UPDATE disc_external ".
				 "SET ".
				 "fundusDiscPhoto='".$fundusDiscPhoto."', ".
				 "photoEye='".$photoEye."', ".
				 "shots='".$shots."', ".
				 "extraCopy='".$extraCopy."', ".
				 "discDesc='".$discDesc."', ".
				 "performedBy='".$performedBy."', ".
				 "ptUnderstanding='".$ptUnderstanding."', ".
				 "reliabilityOd='".$reliabilityOd."', ".
				 "reliabilityOs='".$reliabilityOs."', ".
				 "discOd='".$discOd."', ".
				 "discOs='".$discOs."', ".

				 "resDescOd='".$resDescOd."', ".
				 "resDescOs='".$resDescOs."', ".
				 "stable='".$stable."', ".
				 "monitorAg='".$monitorAg."', ".
				 "fuRetina='".$fuRetina."', ".
				 "fuApa='".$fuApa."', ".
				 "ptInformed='".$ptInformed."', ".
				 "signature='".$signature."', ";
				 
				if(!$zeissAction){  /*Prevent changing interpretation status if "Forum Add/Delete"*/
					$sql .= "phyName = '".$phyName."', ";
				}
				
			 $sql .= "examDate='".$examDate."', ".
				 //"patientId='".$patientId."', ".
				 "fuRetinaDesc='".$fuRetinaDesc."', ".
				 "ptosisOd_neg ='".$ptosisOd_neg."', ".
				 "ptosisOd_T ='".$ptosisOd_T."', ".
				 "ptosisOd_pos1 ='".$ptosisOd_pos1."', ".
				 "ptosisOd_pos2 ='".$ptosisOd_pos2."', ".
				 "ptosisOd_pos3 ='".$ptosisOd_pos3."', ".
				 "ptosisOd_pos4 ='".$ptosisOd_pos4."', ".
				 "ptosisOd_rul ='".$ptosisOd_rul."', ".
				 "normal_OD ='".$normal_OD."', ".
				 "ptosisOd_rll ='".$ptosisOd_rll."', ".
				 "dermaOd_neg ='".$dermaOd_neg."', ".
				 "dermaOd_T ='".$dermaOd_T."', ".
				 "dermaOd_pos1 ='".$dermaOd_pos1."', ".
				 "dermaOd_pos2 ='".$dermaOd_pos2."', ".
				 "dermaOd_pos3 ='".$dermaOd_pos3."', ".
				 "dermaOd_pos4 ='".$dermaOd_pos4."', ".
				 "dermaOd_rul ='".$dermaOd_rul."', ".
				 "dermaOd_rll ='".$dermaOd_rll."', ".
				 "pterygium1mmOd ='".$pterygium1mmOd."', ".
				 "pterygium2mmOd ='".$pterygium2mmOd."', ".
				 "pterygium3mmOd ='".$pterygium3mmOd."', ".
				 "pterygium4mmOd ='".$pterygium4mmOd."', ".
				 "pterygium5mmOd ='".$pterygium5mmOd."', ".
				 "pterygiumNasalOd ='".$pterygiumNasalOd."', ".
				 "pterygiumTemporalOd ='".$pterygiumTemporalOd."', ".
				 "vascOd_SubEpithelial ='".$vascOd_SubEpithelial."', ".
				 "vascOd_Stromal ='".$vascOd_Stromal."', ".
				 "vascOd_Superficial ='".$vascOd_Superficial."', ".
				 "vascOd_Deep ='".$vascOd_Deep."', ".
				 "vascOd_Endothelial ='".$vascOd_Endothelial."', ".
				 "vascOd_Peripheral ='".$vascOd_Peripheral."', ".
				 "vascOd_Central ='".$vascOd_Central."', ".
				 "vascOd_Pannus ='".$vascOd_Pannus."', ".
				 "vascOd_GhostBV ='".$vascOd_GhostBV."', ".
				 "vascOd_Superior='".$vascOd_Superior."', ".
				 "vascOd_Inferior ='".$vascOd_Inferior."', ".
				 "vascOd_Nasal ='".$vascOd_Nasal."', ".
				 "vascOd_Temporal ='".$vascOd_Temporal."', ".
				 "NevusOd_neg ='".$NevusOd_neg."', ".
				 "NevusOd_Pos ='".$NevusOd_Pos."', ".
				 "NevusOd_Inferior ='".$NevusOd_Inferior."', ".
				 "NevusOd_Superior ='".$NevusOd_Superior."', ".
				 "NevusOd_Temporal ='".$NevusOd_Temporal."', ".
				 "NevusOd_Nasal ='".$NevusOd_Nasal."', ".
				 "ptosisOs_neg ='".$ptosisOs_neg."', ".
				 "ptosisOs_T ='".$ptosisOs_T."', ".
				 "ptosisOs_pos1 ='".$ptosisOs_pos1."', ".
				 "ptosisOs_pos2 ='".$ptosisOs_pos2."', ".
				 "ptosisOs_pos3 ='".$ptosisOs_pos3."', ".
				 "ptosisOs_pos4 ='".$ptosisOs_pos4."', ".
				 "ptosisOs_rul ='".$ptosisOs_rul."', ".
				 "ptosisOs_rll ='".$ptosisOs_rll."', ".
				 "dermaOs_neg ='".$dermaOs_neg."', ".
				 "dermaOs_T ='".$dermaOs_T."', ".
				 "dermaOs_pos1 ='".$dermaOs_pos1."', ".
				 "dermaOs_pos2 ='".$dermaOs_pos2."', ".
				 "dermaOs_pos3 ='".$dermaOs_pos3."', ".
				 "dermaOs_pos4 ='".$dermaOs_pos4."', ".
				 "dermaOs_rul ='".$dermaOs_rul."', ".
				 "dermaOs_rll ='".$dermaOs_rll."', ".
				 "pterygium1mmOs ='".$pterygium1mmOs."', ".
				 "pterygium2mmOs ='".$pterygium2mmOs."', ".
				 "pterygium3mmOs ='".$pterygium3mmOs."', ".
				 "pterygium4mmOs ='".$pterygium4mmOs."', ".
				 "pterygium5mmOs ='".$pterygium5mmOs."', ".
				 "pterygiumNasalOs ='".$pterygiumNasalOs."', ".
				 "discComments ='".$discComments."', ".
				 "pterygiumTemporalOs ='".$pterygiumTemporalOs."', ".
				 "vascOs_SubEpithelial ='".$vascOs_SubEpithelial."', ".
				 "vascOs_Stromal ='".$vascOs_Stromal."', ".
				 "vascOs_Superficial ='".$vascOs_Superficial."', ".
				 "vascOs_Deep ='".$vascOs_Deep."', ".
				 "vascOs_Endothelial ='".$vascOs_Endothelial."', ".
				 "vascOs_Peripheral ='".$vascOs_Peripheral."', ".
				 "vascOs_Central ='".$vascOs_Central."', ".
				 "vascOs_Pannus ='".$vascOs_Pannus."', ".
				 "vascOs_GhostBV ='".$vascOs_GhostBV."', ".
				 "vascOs_Superior ='".$vascOs_Superior."', ".
				 "vascOs_Inferior ='".$vascOs_Inferior."', ".
				 "vascOs_Nasal ='".$vascOs_Nasal."', ".
				 "vascOs_Temporal ='".$vascOs_Temporal."', ".
				 "irisNevusOs_neg ='".$irisNevusOs_neg."', ".
				 "irisNevusOs_Pos ='".$irisNevusOs_Pos."', ".
				 "irisNevusOs_Inferior ='".$irisNevusOs_Inferior."', ".
				 "irisNevusOs_Superior ='".$irisNevusOs_Superior."', ".
				 "irisNevusOs_Temporal ='".$irisNevusOs_Temporal."', ".
				 "irisNevusOs_Nasal ='".$irisNevusOs_Nasal."', ".
				 "tech2InformPt = '".$tech2InformPt."', ".
				 "ptInformedNv = '".$ptInformedNv."', ".
				 "examTime = '".$examTime."', ".
				 "contiMeds = '".$contiMeds."', ".
				 "diagnosis = '".$diagnosis."', ".
				 "diagnosisOther = '".$diagnosisOther."', ".
				 "encounter_id='".$encounter_id."', ".
				 "forum_procedure='".$forum_procedure."', ".
				 $signPathQry.
				 $signDTmQry.
				 "ordrby='".$ordrby."',ordrdt='".$ordrdt."' ".
				 //"WHERE formId='".$formId."' ";
				 "WHERE disc_id = '".$disc_id."' ";
			$res = sqlQuery($sql);
		}
		
		/*code, Purpose: Generate HL7 message for Test*/
		$zeissMsgType="DISCEXTERNAL";
		$zeissPatientId = $patientId;
		$zeissTestId = $disc_id;
		include("./zeissTestHl7.php");
		/*End code*/

		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "discExternal", $disc_id);
		/*--interpretation end--*/
		//$cls_notifications->set_notification_status('testseye');
		//Super-Bill -----------
		## Debugging
		//print_r($_POST);
		//exit();
		## Debugging
		if(!empty($ordrby) && !empty($disc_id)){
			$arIn=array();
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$disc_id;
			$arIn["sb_testName"]="External";
                        $arIn["form_id"]=$formId;
                        $arIn["test_interpreted"]= ((int)$phyName > 0)? true : false;

			$oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);
		}
		//Super-Bill -----------

		//save tests Pdf
		$objTests->saveTestPdfExe_2($patientId,$disc_id,"External");

		//FormId updates other tables -----
		if(!empty($formId)){
			// Make chart notes valid
			makeChartNotesValid($formId);
		}
		
		//Javascript ----------
		echo "<script>".
				"try{";
					if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
						$afterSaveinPopup = 'yes';
					}else{
						$afterSaveinPopup = 'no';
						echo "top.alert_notification_show(\"Test has been saved.\");";
					}
					echo "
					window.location.replace(\"test_external.php?noP=".$elem_noP."&tId=".$disc_id."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
				}catch(err){}".
			 "</script>";
		exit();
		//Javascript ----------

		break;
	}
	case "TemplateTests":{
		//$elem_saveForm=$_POST["elem_saveForm"];
		$elem_test_template_id=$_POST['elem_test_template_id'];
		$patientId=$_POST["elem_patientId"];
		$formId=$_POST["elem_formId"];
		$hd_topo_mode=$_POST["hd_topo_mode"];
		$testOtherId=$_POST["elem_testOtherId"];
		$wind_opn=$_POST["wind_opn"];
		$elem_operatorId=$_POST["elem_operatorId"];
		$elem_operatorName=$_POST["elem_operatorName"];
		$elem_noP=$_POST["elem_noP"];
		$examTime=$_POST["elem_examTime"];
		$examDate=getDateFormatDB($_POST["elem_examDate"]);
		if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
		$test_other='TemplateTests';
		$test_other_eye=$_POST["elem_topoMeterEye"];
		$techComments=($_POST["techComments"] == 'Technician Comments') ? "" : imw_real_escape_string($_POST["techComments"]);
		$elem_performedByName=$_POST["elem_performedByName"];
		$performedBy=$_POST["elem_performedBy"];
		$ptUnderstanding=$_POST["elem_ptUnderstanding"];
		$diagnosis=imw_real_escape_string($_POST["elem_diagnosis"]);
		$diagnosisOther=imw_real_escape_string($_POST["elem_diagnosisOther"]);
		$sel_preview=$_POST["sel_preview"];
		$reliabilityOd=$_POST["elem_reliabilityOd"];
		$reliabilityOs=$_POST["elem_reliabilityOs"];
		$descOd=imw_real_escape_string($_POST["elem_descOd"]);
		$inter_pret_od=imw_real_escape_string($_POST["elem_inter_pret_od"]);
		$descOs=imw_real_escape_string($_POST["elem_descOs"]);
		$inter_pret_os=imw_real_escape_string($_POST["elem_inter_pret_os"]);
		$stable=$_POST["elem_stable"];
		$fuApa=$_POST["elem_fuApa"];
		$tech2InformPt=$_POST["elem_tech2InformPt"];
		$ptInformed=$_POST["elem_ptInformed"];
		$ptInformedNv=$_POST["elem_informedPtNv"];
		//$phyName=$_POST["phyName"];
		$phyName=$_POST["elem_physician"];
		$contiMeds = $_POST["elem_contiMeds"];
		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
	
		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
		$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
		$encounter_id = $objTests->getEncounterId();
		}
	
		//Post
	
		//Check
		if(empty($testOtherId)){
	
			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;
			if(isset($arrTest2edit["TemplateTests"]) && !empty($arrTest2edit["TemplateTests"])){
	
				//Check in db
				$cQry = "SELECT test_other_id FROM test_other
						WHERE patientId ='".$patientId."'
						AND test_other_id = '".$arrTest2edit["TemplateTests"]."'";
				$row = sqlQuery($cQry);
				$testOtherId = (($row == false) || empty($row["test_other_id"])) ? "0" : $row["test_other_id"];
	
				//Unset Session TestOther
				$arrTest2edit["TemplateTests"] = "";
				$arrTest2edit["TemplateTests"] = NULL;
				unset($arrTest2edit["TemplateTests"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);
	
			}else{
	
				$cQry = "select test_other_id FROM test_other WHERE patientId='".$patientId."' AND
						examDate='".$examDate."' AND examTime='".$examTime."'";
				$row = sqlQuery($cQry);
				$testOtherId = (($row == false) && !empty($row["test_other_id"])) ? "0" : $row["test_other_id"];
	
			}
		}
	
		//Test Query
		if(empty($testOtherId)){
			$sql = "INSERT INTO test_other ".
					"( ".
					"test_other_id, test_other, test_template_id, test_other_eye, performedBy, diagnosis, ptUnderstanding, reliabilityOd, reliabilityOs, ".
					"descOd, descOs, inter_pret_od, inter_pret_os, ".
					"stable, fuApa, ptInformed, phyName, examDate, patientId, formId, diagnosisOther, ".
					"tech2InformPt, techComments, ptInformedNv, examTime, contiMeds, ".
					"encounter_id,ordrby,ordrdt ".$signPathFieldName.$signDTmFieldName.
					") ".
					"VALUES ".
					"( ".
					"NULL, '".$test_other."', '".$elem_test_template_id."', '".$test_other_eye."', '".$performedBy."', '".$diagnosis."', '".$ptUnderstanding."', '".$reliabilityOd."', '".$reliabilityOs."', ".
					"'".$descOd."', '".$descOs."',".
					"'".$inter_pret_od."', '".$inter_pret_os."',".
					"'".$stable."', '".$fuApa."', '".$ptInformed."', '".$phyName."', '".$examDate."', '".$patientId."', '".$formId."', '".$diagnosisOther."', ".
					"'".$tech2InformPt."', '".$techComments."', '".$ptInformedNv."', '".$examTime."','".$contiMeds."', ".
					"'".$encounter_id."','".$ordrby."','".$ordrdt."' ".$signPathFieldValue.$signDTmFieldValue.
					") ";
			$re = imw_query($sql);
			$insertId = imw_insert_id();
			$testOtherId = $insertId;
		}else if(!empty($testOtherId)){
			$sql = "UPDATE test_other ".
				"SET ".
				//"test_other = '".$test_other."', ".
				"test_template_id = '".$elem_test_template_id."', ".
				"test_other_eye = '".$test_other_eye."', ".
				"performedBy = '".$performedBy."', ".
				"diagnosis = '".$diagnosis."', ".
				"ptUnderstanding = '".$ptUnderstanding."', ".
				"reliabilityOd = '".$reliabilityOd."', ".
				"reliabilityOs = '".$reliabilityOs."', ".
				"descOd = '".$descOd."', ".
				"descOs = '".$descOs."', ".
				"inter_pret_od = '".$inter_pret_od."', ".
				"inter_pret_os = '".$inter_pret_os."', ".
				"stable = '".$stable."', ".
				"fuApa = '".$fuApa."', ".
				"ptInformed = '".$ptInformed."', ".
				"phyName = '".$phyName."', ".
				"examDate = '".$examDate."', ".
				//"patientId = '".$patientId."', ".
				"tech2InformPt = '".$tech2InformPt."', ".
				"diagnosisOther = '".$diagnosisOther."', ".
				"techComments = '".$techComments."', ".
				"ptInformedNv = '".$ptInformedNv."', ".
				"examTime = '".$examTime."', ".
				"contiMeds = '".$contiMeds."', ".
				"encounter_id='".$encounter_id."', ".
				$signPathQry.
				$signDTmQry.
				"ordrby='".$ordrby."',ordrdt='".$ordrdt."' ".
				//"WHERE formId = '".$formId."' ";
				"WHERE test_other_id = '".$testOtherId."' ";
			$res = sqlQuery($sql);
		}
	
		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "TemplateTests", $testOtherId);
		/*--interpretation end--*/
			//$cls_notifications->set_notification_status('testseye');
	
		//Super-Bill -----------
		if(!empty($ordrby) && !empty($testOtherId)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$testOtherId;
			$arIn["sb_testName"]="TemplateTests";
                        $arIn["form_id"]=$formId;
                        $arIn["test_interpreted"]= ((int)$phyName > 0)? true : false;

			$oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);			
		}
	
		//save tests Pdf
		$objTests->saveTestPdfExe_2($patientId,$testOtherId,"TemplateTests");
	
		//FormId updates other tables -----
		if(!empty($formId)){
			// Make chart notes valid
			$oChartNote->makeChartNotesValid($formId);
		}
	
		//Redirect
		echo "<script>".
				"try{";
				if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
					$afterSaveinPopup = 'yes';
				}else{
					$afterSaveinPopup = 'no';
					echo "top.alert_notification_show(\"Test has been saved.\");";
				}
				echo "
					window.location.replace(\"test_template.php?noP=".$elem_noP."&tId=".$testOtherId."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
				}catch(err){}".
			 "</script>";
		exit();
		break;
	}
	case "CustomTests":{
		//$elem_saveForm=$_POST["elem_saveForm"];
		$elem_test_template_id=$_POST['elem_test_template_id'];
		$elem_test_template_version = $_POST['elem_version_id'];
		$patientId=$_POST["elem_patientId"];
		$formId=$_POST["elem_formId"];
		//$elem_edMode=$_POST["elem_edMode"];
		$testOtherId=$_POST["elem_testOtherId"];
		//$wind_opn=$_POST["wind_opn"];
		$elem_operatorId=$_POST["elem_operatorId"];
		$elem_operatorName=$_POST["elem_operatorName"];
		$elem_noP=$_POST["elem_noP"];
		$examTime=$_POST["elem_examTime"];
		$examDate=getDateFormatDB($_POST["elem_examDate"]);
		if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
		$test_other='CustomTests';
		$test_other_eye=$_POST["elem_topoMeterEye"];
		$techComments=($_POST["techComments"] == 'Technician Comments') ? "" : imw_real_escape_string($_POST["techComments"]);
		$elem_performedByName=$_POST["elem_performedByName"];
		$performedBy=$_POST["elem_performedBy"];
		$ptUnderstanding=$_POST["elem_ptUnderstanding"];
		$diagnosis=imw_real_escape_string($_POST["elem_diagnosis"]);
		$diagnosisOther=imw_real_escape_string($_POST["elem_diagnosisOther"]);
		//$sel_preview=$_POST["sel_preview"];
		$reliabilityOd=$_POST["elem_reliabilityOd"];
		$reliabilityOs=$_POST["elem_reliabilityOs"];
		//$descOd=imw_real_escape_string($_POST["elem_descOd"]);
		//$inter_pret_od=imw_real_escape_string($_POST["elem_inter_pret_od"]);
		//$descOs=imw_real_escape_string($_POST["elem_descOs"]);
		//$inter_pret_os=imw_real_escape_string($_POST["elem_inter_pret_os"]);
		
		$test_main_options	= imw_real_escape_string($_POST['elem_test_main_options']);
		$test_result		= imw_real_escape_string($_POST['elem_test_result']);
		$test_treatment		= imw_real_escape_string($_POST['elem_test_treatment']);
		/*
		$stable=$_POST["elem_stable"];
		$fuApa=$_POST["elem_fuApa"];
		$tech2InformPt=$_POST["elem_tech2InformPt"];
		$ptInformed=$_POST["elem_ptInformed"];
		$ptInformedNv=$_POST["elem_informedPtNv"];
		$contiMeds = $_POST["elem_contiMeds"];
		*/
		//$phyName=$_POST["phyName"];
		$phyName=$_POST["elem_physician"];
		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
	
		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
		$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
		$encounter_id = $objTests->getEncounterId();
		}
	
		//Post
	
		//Check
		if(empty($testOtherId)){
			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;
			if(isset($arrTest2edit["CustomTests"]) && !empty($arrTest2edit["CustomTests"])){
	
				//Check in db
				$cQry = "SELECT test_id FROM test_custom_patient 
						WHERE patientId ='".$patientId."'
						AND test_id = '".$arrTest2edit["CustomTests"]."'";
				$row = sqlQuery($cQry);//echo $cQry;die;
				$testOtherId = (($row == false) || empty($row["test_id"])) ? "0" : $row["test_id"];
	
				//Unset Session TestOther
				$arrTest2edit["CustomTests"] = "";
				$arrTest2edit["CustomTests"] = NULL;
				unset($arrTest2edit["CustomTests"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);
	
			}else{
				$cQry = "select test_id FROM test_custom_patient WHERE patientId='".$patientId."' AND
						examDate='".$examDate."' AND examTime='".$examTime."'";
				$row = sqlQuery($cQry);
				$testOtherId = (($row == false) && !empty($row["test_id"])) ? "0" : $row["test_id"];
			}
		}

		$dss_service = $_POST['dss_service'];
		$dss_service_ien = $_POST['dss_service_ien'];
		$dss_service_orderable_item = $_POST['dss_service_orderable_item'];
		$dss_placeOfConsult = $_POST['dss_placeOfConsult'];
		$dss_reasonForRequest = $_POST['dss_reasonForRequest'];
		$elem_dxCode_dss = $_POST['elem_dxCode_dss'];
		$elem_dxText_dss = $_POST['elem_dxText_dss'];


		//Test Query
		if(empty($testOtherId)){

			if(isDssEnable()) {
				$dfn = '';
				$dssTestFields = "";
				$dssTestValues = "";
				$dfnSql = imw_query("SELECT External_MRN_5 FROM patient_data WHERE id = ".$_SESSION['patient']);
				if(imw_num_rows($dfnSql) > 0) {
				    $dfnSqlResult = imw_fetch_assoc($dfnSql);
				    $dfn = $dfnSqlResult['External_MRN_5'];
				}

				$dssTestFields = ",dss_orderable_item,dss_service,dss_service_ien,dss_placeOfConsult,dss_reasonForRequest,dss_dxCode,dss_dxText,dss_orderNumber,dss_group,dss_orderTime,dss_status";
				$dssTestValues = ",'".$dss_service_orderable_item."','".$dss_service."','".$dss_service_ien."','".$dss_placeOfConsult."','".$dss_reasonForRequest."','".$elem_dxCode_dss."','".$elem_dxText_dss."'";

				if(!empty($dfn) && $dfn != '') {
					include_once( $GLOBALS['srcdir'].'/dss_api/dss_enc_visit_notes.php' );
	        		$obj = new Dss_enc_visit_notes();
	        		$postArray = array(
						"patient" => $dfn,
						"provider" => $_SESSION['dss_loginDUZ'],
						"location" => $_SESSION['dss_location'],
						"orderId" => $dss_service_orderable_item,
						"reasonForRequest"=> array($dss_reasonForRequest),
						"categoryCode" => "I",
						"placeOfConsult" => $dss_placeOfConsult,
						"diagnosis" => $elem_dxCode_dss."^".$elem_dxText_dss,
						"orderCheckList" => array("^")
	        		);
	        		
	        		try {
		        		$result = $obj->ConsultSave($postArray);
		        		$dss_orderNumber = '';
		        		$dss_group = '';
		        		$dss_orderTime = '';
		        		$dss_status = '';

						foreach ($result as $key => $arr) {
						    $x = explode('~', $arr['orderNumber']);
						    $y = explode(';', $x[1]);

							$dss_orderNumber = $y[0];
							$dss_group = $arr['group'];
							$dss_orderTime = $obj->filemanToBase($arr['orderTime']);
							$dss_status = $arr['status'];

							$dssTestValues .= ",'".$dss_orderNumber."','".$dss_group."','".$dss_orderTime."','".$dss_status."'";
						}

	        		} catch(Exception $e) {
						echo $e->getMessage();	        			
	        		}
	        	} else {
	        		echo "<script>top.fAlert(\"Patient is not mapped with DSS.\");</script>";
	        		exit();
	        	}
			}

			$sql = "INSERT INTO test_custom_patient ".
					"( ".
					"test_other, test_template_id,version, test_other_eye, performedBy, diagnosis, ptUnderstanding, reliabilityOd, reliabilityOs, ".
					"test_main_options,test_result,test_treatment, ".
					"phyName, examDate, patientId, formId, diagnosisOther, ".
					"techComments, examTime, ".
					"encounter_id,ordrby,ordrdt ".$signPathFieldName.$signDTmFieldName.$dssTestFields.
					") ".
					"VALUES ".
					"( ".
					"'".$test_other."', '".$elem_test_template_id."', '".$elem_test_template_version."', '".$test_other_eye."', '".$performedBy."', '".$diagnosis."', '".$ptUnderstanding."', '".$reliabilityOd."', '".$reliabilityOs."', ".
					"'".$test_main_options."', '".$test_result."',".
					"'".$test_treatment."',".
					"'".$phyName."', '".$examDate."', '".$patientId."', '".$formId."', '".$diagnosisOther."', ".
					"'".$techComments."', '".$examTime."', ".
					"'".$encounter_id."','".$ordrby."','".$ordrdt."' ".$signPathFieldValue.$signDTmFieldValue.$dssTestValues.
					") ";
			$re = imw_query($sql);
			//echo $sql.'<hr>'.imw_error();die;
			$insertId = imw_insert_id();
			$testOtherId = $insertId;
		}else if(!empty($testOtherId)){
			$sql = "UPDATE test_custom_patient ".
				"SET ".
				//"test_other = '".$test_other."', ".
				"test_template_id = '".$elem_test_template_id."', ".
				"version = '".$elem_test_template_version."', ".
				"test_other_eye = '".$test_other_eye."', ".
				"performedBy = '".$performedBy."', ".
				"diagnosis = '".$diagnosis."', ".
				"ptUnderstanding = '".$ptUnderstanding."', ".
				"reliabilityOd = '".$reliabilityOd."', ".
				"reliabilityOs = '".$reliabilityOs."', ".
				"test_main_options = '".$test_main_options."', ".
				"test_result = '".$test_result."', ".
				"test_treatment = '".$test_treatment."', ".
				"phyName = '".$phyName."', ".
				"examDate = '".$examDate."', ".
				//"patientId = '".$patientId."', ".
				"diagnosisOther = '".$diagnosisOther."', ".
				"techComments = '".$techComments."', ".
				"examTime = '".$examTime."', ".
				"encounter_id='".$encounter_id."', ".
				$signPathQry.
				$signDTmQry.
				"ordrby='".$ordrby."',ordrdt='".$ordrdt."' ".
				//"WHERE formId = '".$formId."' ";
				"WHERE test_id = '".$testOtherId."' ";
			$res = sqlQuery($sql);
		}
	
		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "CustomTests", $testOtherId);
		/*--interpretation end--*/
			//$cls_notifications->set_notification_status('testseye');
	
		//Super-Bill -----------
		if(!empty($ordrby) && !empty($testOtherId)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$testOtherId;
			$arIn["sb_testName"]="CustomTests";
                        $arIn["form_id"]=$formId;
                        $arIn["test_interpreted"]= ((int)$phyName > 0)? true : false;

			$oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);			
		}
	
		//save tests Pdf
		$objTests->saveTestPdfExe_2($patientId,$testOtherId,"CustomTests");
	
		//FormId updates other tables -----
		if(!empty($formId)){
			// Make chart notes valid
			$oChartNote->makeChartNotesValid($formId);
		}
	
		//Redirect
		echo "<script>".
				"try{";
				if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
					$afterSaveinPopup = 'yes';
				}else{
					$afterSaveinPopup = 'no';
					echo "top.alert_notification_show(\"Test has been saved.\");";
				}
				echo "
					window.location.replace(\"test_template_custom_patient.php?noP=".$elem_noP."&tId=".$testOtherId."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
				}catch(err){}".
			 "</script>";
		exit();
		
		//$op = "1" ;
		//header("Location: test_pacchy.php?op=".$op);
		break;
	}
	case 'TestOther':{
		//$elem_saveForm=$_POST["elem_saveForm"];
		$elem_test_template_id=0;
		$patientId=$_POST["elem_patientId"];
		$formId=$_POST["elem_formId"];
		$hd_topo_mode=$_POST["hd_topo_mode"];
		$testOtherId=$_POST["elem_testOtherId"];
		$wind_opn=$_POST["wind_opn"];
		$elem_operatorId=$_POST["elem_operatorId"];
		$elem_operatorName=$_POST["elem_operatorName"];
		$elem_noP=$_POST["elem_noP"];
		$examTime=$_POST["elem_examTime"];
		$examDate=getDateFormatDB($_POST["elem_examDate"]);
		if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
		$test_other=imw_real_escape_string($_POST["elem_testOtherName"]);
		$test_other_eye=$_POST["elem_topoMeterEye"];
		$techComments=($_POST["techComments"] == 'Technician Comments') ? "" : imw_real_escape_string($_POST["techComments"]);
		$elem_performedByName=$_POST["elem_performedByName"];
		$performedBy=$_POST["elem_performedBy"];
		$ptUnderstanding=$_POST["elem_ptUnderstanding"];
		$diagnosis=imw_real_escape_string($_POST["elem_diagnosis"]);
		$diagnosisOther=imw_real_escape_string($_POST["elem_diagnosisOther"]);
		$sel_preview=$_POST["sel_preview"];
		$reliabilityOd=$_POST["elem_reliabilityOd"];
		$reliabilityOs=$_POST["elem_reliabilityOs"];
		$descOd=imw_real_escape_string($_POST["elem_descOd"]);
		$inter_pret_od=imw_real_escape_string($_POST["elem_inter_pret_od"]);
		$descOs=imw_real_escape_string($_POST["elem_descOs"]);
		$inter_pret_os=imw_real_escape_string($_POST["elem_inter_pret_os"]);
		$stable=$_POST["elem_stable"];
		$fuApa=$_POST["elem_fuApa"];
		$tech2InformPt=$_POST["elem_tech2InformPt"];
		$ptInformed=$_POST["elem_ptInformed"];
		$ptInformedNv=$_POST["elem_informedPtNv"];
		//$phyName=$_POST["phyName"];
		$phyName=$_POST["elem_physician"];
		$contiMeds = $_POST["elem_contiMeds"];
		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
	
		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
			$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
			$encounter_id = $objTests->getEncounterId();
		}
	
		//Post
	
		//Check
		if(empty($testOtherId)){
			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;
			if(isset($arrTest2edit["TestOther"]) && !empty($arrTest2edit["TestOther"])){
	
				//Check in db
				$cQry = "SELECT test_other_id FROM test_other
						WHERE patientId ='".$patientId."'
						AND test_other_id = '".$arrTest2edit["TestOther"]."' AND test_template_id=0";
				$row = sqlQuery($cQry);
				$testOtherId = (($row == false) || empty($row["test_other_id"])) ? "0" : $row["test_other_id"];
	
				//Unset Session TestOther
				$arrTest2edit["TestOther"] = "";
				$arrTest2edit["TestOther"] = NULL;
				unset($arrTest2edit["TestOther"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);
	
			}else{
	
				$cQry = "select test_other_id FROM test_other WHERE patientId='".$patientId."' AND
						examDate='".$examDate."' AND examTime='".$examTime."' AND test_template_id=0";
				$row = sqlQuery($cQry);
				$testOtherId = (($row == false) && !empty($row["test_other_id"])) ? "0" : $row["test_other_id"];
	
			}
		}
	
		//Test Query
		if(empty($testOtherId)){
			$sql = "INSERT INTO test_other ".
					"( ".
					"test_other_id, test_other, test_template_id, test_other_eye, performedBy, diagnosis, ptUnderstanding, reliabilityOd, reliabilityOs, ".
					"descOd, descOs, inter_pret_od, inter_pret_os, ".
					"stable, fuApa, ptInformed, phyName, examDate, patientId, formId, diagnosisOther, ".
					"tech2InformPt, techComments, ptInformedNv, examTime, contiMeds, ".
					"encounter_id,ordrby,ordrdt ".$signPathFieldName.$signDTmFieldName.
					") ".
					"VALUES ".
					"( ".
					"NULL, '".$test_other."', '0', '".$test_other_eye."', '".$performedBy."', '".$diagnosis."', '".$ptUnderstanding."', '".$reliabilityOd."', '".$reliabilityOs."', ".
					"'".$descOd."', '".$descOs."',".
					"'".$inter_pret_od."', '".$inter_pret_os."',".
					"'".$stable."', '".$fuApa."', '".$ptInformed."', '".$phyName."', '".$examDate."', '".$patientId."', '".$formId."', '".$diagnosisOther."', ".
					"'".$tech2InformPt."', '".$techComments."', '".$ptInformedNv."', '".$examTime."','".$contiMeds."', ".
					"'".$encounter_id."','".$ordrby."','".$ordrdt."' ".$signPathFieldValue.$signDTmFieldValue.
					") ";
			$re = imw_query($sql);
			$insertId = imw_insert_id();
			$testOtherId = $insertId;
		}else if(!empty($testOtherId)){
			$sql = "UPDATE test_other ".
				"SET ".
				"test_other = '".$test_other."', ".
				"test_template_id = '0', ".
				"test_other_eye = '".$test_other_eye."', ".
				"performedBy = '".$performedBy."', ".
				"diagnosis = '".$diagnosis."', ".
				"ptUnderstanding = '".$ptUnderstanding."', ".
				"reliabilityOd = '".$reliabilityOd."', ".
				"reliabilityOs = '".$reliabilityOs."', ".
				"descOd = '".$descOd."', ".
				"descOs = '".$descOs."', ".
				"inter_pret_od = '".$inter_pret_od."', ".
				"inter_pret_os = '".$inter_pret_os."', ".
				"stable = '".$stable."', ".
				"fuApa = '".$fuApa."', ".
				"ptInformed = '".$ptInformed."', ".
				"phyName = '".$phyName."', ".
				"examDate = '".$examDate."', ".
				//"patientId = '".$patientId."', ".
				"tech2InformPt = '".$tech2InformPt."', ".
				"diagnosisOther = '".$diagnosisOther."', ".
				"techComments = '".$techComments."', ".
				"ptInformedNv = '".$ptInformedNv."', ".
				"examTime = '".$examTime."', ".
				"contiMeds = '".$contiMeds."', ".
				"encounter_id='".$encounter_id."', ".
				$signPathQry.
				$signDTmQry.
				"ordrby='".$ordrby."',ordrdt='".$ordrdt."' ".
				//"WHERE formId = '".$formId."' ";
				"WHERE test_other_id = '".$testOtherId."' ";
			$res = sqlQuery($sql);
		}
	
		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "TestOther", $testOtherId);
		/*--interpretation end--*/
			//$cls_notifications->set_notification_status('testseye');
	
		//Super-Bill -----------
		if(!empty($ordrby) && !empty($testOtherId)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$testOtherId;
			$arIn["sb_testName"]="Other";
			$arIn["form_id"]=$formId;
			$oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);			
		}
	
		//save tests Pdf
		$objTests->saveTestPdfExe_2($patientId,$testOtherId,"Other");
	
		//FormId updates other tables -----
		if(!empty($formId)){
			// Make chart notes valid
			$oChartNote->makeChartNotesValid($formId);
		}
	
		//Redirect
		echo "<script>".
				"try{";
					if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
						$afterSaveinPopup = 'yes';
					}else{
						$afterSaveinPopup = 'no';
						echo "top.alert_notification_show(\"Test has been saved.\");";
					}
					echo "
					window.location.replace(\"test_other.php?noP=".$elem_noP."&tId=".$testOtherId."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
				}catch(err){}".
			 "</script>";
		exit();
		break;
	}
	case 'TestLabs':{
	//$elem_saveForm=$_POST["elem_saveForm"];
	$patientId=$_POST["elem_patientId"];
	$formId=$_POST["elem_formId"];
	$hd_topo_mode=$_POST["hd_topo_mode"];
	$testLabsId=$_POST["elem_testLabsId"];
	$wind_opn=$_POST["wind_opn"];
	$elem_operatorId=$_POST["elem_operatorId"];
	$elem_operatorName=$_POST["elem_operatorName"];
	$elem_noP=$_POST["elem_noP"];
	$examTime=$_POST["elem_examTime"];
	$examDate=getDateFormatDB($_POST["elem_examDate"]);
	if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
	$test_labs=imw_real_escape_string($_POST["elem_testLabsName"]);
	$test_labs_eye=$_POST["elem_topoMeterEye"];
	$techComments=($_POST["techComments"] == 'Technician Comments') ? "" : imw_real_escape_string($_POST["techComments"]);
	$elem_performedByName=$_POST["elem_performedByName"];
	$performedBy=$_POST["elem_performedBy"];
	$ptUnderstanding=$_POST["elem_ptUnderstanding"];
	$diagnosis=imw_real_escape_string($_POST["elem_diagnosis"]);
	$diagnosisOther=imw_real_escape_string($_POST["elem_diagnosisOther"]);
	$sel_preview=$_POST["sel_preview"];
	$reliabilityOd=$_POST["elem_reliabilityOd"];
	$reliabilityOs=$_POST["elem_reliabilityOs"];
	$descOd=imw_real_escape_string($_POST["elem_descOd"]);
	$inter_pret_od=imw_real_escape_string($_POST["elem_inter_pret_od"]);
	$descOs=imw_real_escape_string($_POST["elem_descOs"]);
	$inter_pret_os=imw_real_escape_string($_POST["elem_inter_pret_os"]);
	$stable=$_POST["elem_stable"];
	$fuApa=$_POST["elem_fuApa"];
	$tech2InformPt=$_POST["elem_tech2InformPt"];
	$ptInformed=$_POST["elem_ptInformed"];
	$ptInformedNv=$_POST["elem_informedPtNv"];
	//$phyName=$_POST["phyName"];
	$phyName=$_POST["elem_physician"];
	$contiMeds = $_POST["elem_contiMeds"];
	$ordrby = $_POST["elem_opidTestOrdered"];
	$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
	
		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
			$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
			$encounter_id = $objTests->getEncounterId();
		}
	
		//Check
		if(empty($testLabsId)){
			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;
			if(isset($arrTest2edit["TestLabs"]) && !empty($arrTest2edit["TestLabs"])){
	
				//Check in db
				$cQry = "SELECT test_labs_id FROM test_labs
					WHERE patientId ='".$patientId."'
					AND test_labs_id = '".$arrTest2edit["TestLabs"]."' ";
			$row = sqlQuery($cQry);
				$testLabsId = (($row == false) || empty($row["test_labs_id"])) ? "0" : $row["test_labs_id"];
	
				//Unset Session TestLabs
				$arrTest2edit["TestLabs"] = "";
				$arrTest2edit["TestLabs"] = NULL;
				unset($arrTest2edit["TestLabs"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);
	
			}else{
	
				$cQry = "select test_labs_id FROM test_labs WHERE patientId='".$patientId."' AND
						examDate='".$examDate."' AND examTime='".$examTime."'";
				$row = sqlQuery($cQry);
				$testLabsId = (($row == false) && !empty($row["test_labs_id"])) ? "0" : $row["test_labs_id"];
	
			}
		}
	
		//Test Query
		if(empty($testLabsId)){
			$sql = "INSERT INTO test_labs ".
				"( ".
				"test_labs_id, test_labs, test_labs_eye, performedBy, diagnosis, ptUnderstanding, reliabilityOd, reliabilityOs, ".
				"descOd, descOs, inter_pret_od, inter_pret_os, ".
				"stable, fuApa, ptInformed, phyName, examDate, patientId, formId, diagnosisOther, ".
				"tech2InformPt, techComments, ptInformedNv, examTime,contiMeds, ".
				"encounter_id,ordrby,ordrdt ".$signPathFieldName.$signDTmFieldName.
				") ".
				"VALUES ".
				"( ".
				"NULL, '".$test_labs."', '".$test_labs_eye."', '".$performedBy."', '".$diagnosis."', '".$ptUnderstanding."', '".$reliabilityOd."', '".$reliabilityOs."', ".
				"'".$descOd."', '".$descOs."',".
				"'".$inter_pret_od."', '".$inter_pret_os."',".
				"'".$stable."', '".$fuApa."', '".$ptInformed."', '".$phyName."', '".$examDate."', '".$patientId."', '".$formId."', '".$diagnosisOther."', ".
				"'".$tech2InformPt."', '".$techComments."', '".$ptInformedNv."', '".$examTime."','".$contiMeds."', ".
				"'".$encounter_id."','".$ordrby."','".$ordrdt."' ".$signPathFieldValue.$signDTmFieldValue.
				") ";
			$re = imw_query($sql);
			$insertId = imw_insert_id();
			$testLabsId = $insertId;
		}else if(!empty($testLabsId)){
			$sql = "UPDATE test_labs ".
			"SET ".
			"test_labs = '".$test_labs."', ".
			"test_labs_eye = '".$test_labs_eye."', ".
			"performedBy = '".$performedBy."', ".
			"diagnosis = '".$diagnosis."', ".
			"ptUnderstanding = '".$ptUnderstanding."', ".
			"reliabilityOd = '".$reliabilityOd."', ".
			"reliabilityOs = '".$reliabilityOs."', ".
			"descOd = '".$descOd."', ".
			"descOs = '".$descOs."', ".
			"inter_pret_od = '".$inter_pret_od."', ".
			"inter_pret_os = '".$inter_pret_os."', ".
			"stable = '".$stable."', ".
			"fuApa = '".$fuApa."', ".
			"ptInformed = '".$ptInformed."', ".
			"phyName = '".$phyName."', ".
			"examDate = '".$examDate."', ".
			//"patientId = '".$patientId."', ".
			"tech2InformPt = '".$tech2InformPt."', ".
			"diagnosisOther = '".$diagnosisOther."', ".
			"techComments = '".$techComments."', ".
			"ptInformedNv = '".$ptInformedNv."', ".
			"examTime = '".$examTime."', ".
			"contiMeds = '".$contiMeds."', ".
			"encounter_id='".$encounter_id."', ".
			$signPathQry.
			$signDTmQry.
			"ordrby='".$ordrby."',ordrdt='".$ordrdt."' ".
			//"WHERE formId = '".$formId."' ";
			"WHERE test_labs_id = '".$testLabsId."' ";
			$res = sqlQuery($sql);
		}
	
		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "TestLabs", $testLabsId);
		/*--interpretation end--*/
			//$cls_notifications->set_notification_status('testseye');
	
		//Super-Bill -----------
		if(!empty($ordrby) && !empty($testLabsId)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$testLabsId;
			$arIn["sb_testName"]="Labs";
			$arIn["form_id"]=$formId;
			$oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);			
		}
	
		//save tests Pdf
		$objTests->saveTestPdfExe_2($patientId,$testLabsId,"Labs");
	
		//FormId updates other tables -----
		if(!empty($formId)){
			// Make chart notes valid
			$oChartNote->makeChartNotesValid($formId);
		}
	
		//Redirect
		echo "<script>".
				"try{";
				if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
					$afterSaveinPopup = 'yes';
				}else{
					$afterSaveinPopup = 'no';
					echo "top.alert_notification_show(\"Test has been saved.\");";
				}
				echo "
					window.location.replace(\"test_labs.php?noP=".$elem_noP."&tId=".$testLabsId."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
				}catch(err){}".
			 "</script>";
		exit();
		break;
	}
	case "Topography":{
		$examDate = getDateFormatDB($_POST["elem_examDate"]);
		if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
		$topoMeter = $_POST["elem_topoMeter"];
		$topoMeterEye = $_POST["elem_topoMeterEye"];
		$performedBy = $_POST["elem_performedBy"];
		$ptUnderstanding = $_POST["elem_ptUnderstanding"];
		$diagnosis = imw_real_escape_string($_POST["elem_diagnosis"]);
		$diagnosisOther = imw_real_escape_string($_POST["elem_diagnosisOther"]);
		$reliabilityOd = $_POST["elem_reliabilityOd"];
		$reliabilityOs = $_POST["elem_reliabilityOs"];
		$pachyOd = $_POST["elem_pachyOd"];
		$descOd = imw_real_escape_string($_POST["elem_descOd"]);
		$pachyOs = $_POST["elem_pachyOs"];

		$descOs = imw_real_escape_string($_POST["elem_descOs"]);
		$stable = $_POST["elem_stable"];
		$fuApa = $_POST["elem_fuApa"];
		$ptInformed = $_POST["elem_ptInformed"];
		$comments = imw_real_escape_string($_POST["elem_comments"]);
		$signature = $_POST["elem_pachySign"];
		$phyName = $_POST["elem_physician"];
		$patientId = $_POST["elem_patientId"];
		$formId = $_POST["elem_formId"];
		$hd_pachy_mode = $_POST["hd_pachy_mode"];
		$pachy_id = $_POST["elem_pachyId"];
		$tech2InformPt = $_POST["elem_tech2InformPt"];
		$ptInformedNv = $_POST["elem_informedPtNv"];
		$elem_noP = $_POST["elem_noP"];
		$examTime = $_POST["elem_examTime"];
		$topoId = $_POST["elem_topoId"];
		$contiMeds = $_POST["elem_contiMeds"];
		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
		
		$forum_procedure = (isset($_REQUEST['forum_procedure']))?imw_real_escape_string($_REQUEST['forum_procedure']):"";
		$zeissAction = (isset($_REQUEST['zeissAction']))?$_POST['zeissAction']:false;

		$treat = $_POST["elem_treat"];
		$treat_other = imw_real_escape_string($_POST["elem_treatOther"]);
		//Prog ---
		$tmp = $_POST["elem_prog"];
		$tmp2 = "";
		if(count($tmp)){
			foreach($tmp as $t1 => $t2){
				if(!empty($tmp2)){
					$tmp2 .= ",";
				}
				$tmp2 .= "".$t2;
			}
		}
		$prog = $tmp2;
		//Prog ---

		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
			$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
			$encounter_id = $objTests->getEncounterId();
		}

		//Comments
		if($_POST["techComments"] == 'Technician Comments'){
			$_POST["techComments"] = '';
		}
		$elem_techComments = imw_real_escape_string($_POST["techComments"]);

		//Check
		if(empty($topoId)){

			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;

			if(isset($arrTest2edit["Topogrphy"]) && !empty($arrTest2edit["Topogrphy"])){

				//Check in db
				$cQry = "select topo_id FROM topography
						WHERE patientId ='".$patientId."'
						AND topo_id = '".$arrTest2edit["Topogrphy"]."' ";
				$row = sqlQuery($cQry);
				$topoId = (($row == false) || empty($row["topo_id"])) ? "0" : $row["topo_id"];

				//Unset Session Topogrphy
				$arrTest2edit["Topogrphy"] = "";
				$arrTest2edit["Topogrphy"] = NULL;
				unset($arrTest2edit["Topogrphy"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);

			}else{

				$cQry = "select topo_id FROM topography WHERE patientId='".$patientId."' AND
						examDate='".$examDate."' AND examTime='".$examTime."' ";
				$row = sqlQuery($cQry);
				$topoId = (($row == false) && !empty($row["topo_id"])) ? "0" : $row["topo_id"];

			}
		}

		if(empty($topoId)){
			$phyName=($zeissAction)?0:$phyName; /*Mark test uninterpreted if "Forum Add"*/
			$sql = "INSERT INTO topography ".
					"( ".
					"topo_id, topoMeter, topoMeterEye, performedBy, diagnosis, ptUnderstanding, reliabilityOd, reliabilityOs, ".
					"topoOd, topoOs,".
					"descOd, descOs, ".
					"stable, fuApa, ptInformed, comments, signature, phyName, examDate, patientId, formId, diagnosisOther, ".
					"tech2InformPt, techComments, ptInformedNv, examTime,contiMeds, ".
				    "encounter_id,ordrby,ordrdt,prog,treat,treat_other,forum_procedure ".$signPathFieldName.$signDTmFieldName.
					") ".
					"VALUES ".
					"( ".
					"NULL, '".$topoMeter."', '".$topoMeterEye."', '".$performedBy."', '".$diagnosis."', '".$ptUnderstanding."', '".$reliabilityOd."', '".$reliabilityOs."', ".
					"'".$pachyOd."', '".$pachyOs."',".
					"'".$descOd."', '".$descOs."',".
					"'".$stable."', '".$fuApa."', '".$ptInformed."', '".$comments."', '".$signature."', '".$phyName."', '".$examDate."', '".$patientId."', '".$formId."', '".$diagnosisOther."', ".
					"'".$tech2InformPt."', '".$elem_techComments."', '".$ptInformedNv."', '".$examTime."','".$contiMeds."', ".
					"'".$encounter_id."','".$ordrby."','".$ordrdt."', ".
				    "'".$prog."', '".$treat."','".$treat_other."','".$forum_procedure."' ".$signPathFieldValue.$signDTmFieldValue.
					") ";
			$res_insert = imw_query($sql);
			$insertId = imw_insert_id();
			$topoId = $insertId;
		}else if(!empty($topoId)){
			$sql = "UPDATE topography ".
				"SET ".
				"topoMeter = '".$topoMeter."', ".
				"topoMeterEye = '".$topoMeterEye."', ".
				"performedBy = '".$performedBy."', ".
				"diagnosis = '".$diagnosis."', ".
				"ptUnderstanding = '".$ptUnderstanding."', ".
				"reliabilityOd = '".$reliabilityOd."', ".
				"reliabilityOs = '".$reliabilityOs."', ".
				"topoOd = '".$topoOd."', ".
				"topoOs = '".$topoOs."', ".
				"descOd = '".$descOd."', ".
				"descOs = '".$descOs."', ".
				"stable = '".$stable."', ".
				"fuApa = '".$fuApa."', ".
				"ptInformed = '".$ptInformed."', ".
				"comments = '".$comments."', ".
				"signature = '".$signature."', ";
			if(!$zeissAction){  /*Prevent changing interpretation status if "Forum Add/Delete"*/
				$sql .= "phyName = '".$phyName."', ";
			}
			$sql .= "examDate = '".$examDate."', ".
				//"patientId = '".$patientId."', ".
				"tech2InformPt = '".$tech2InformPt."', ".
				"diagnosisOther = '".$diagnosisOther."', ".
				"techComments = '".$elem_techComments."', ".
				"ptInformedNv = '".$ptInformedNv."', ".
				"examTime = '".$examTime."', ".
				"contiMeds = '".$contiMeds."', ".
				"encounter_id='".$encounter_id."', ".
				"ordrby='".$ordrby."',ordrdt='".$ordrdt."', ".
				"prog='".$prog."', ".
				"treat='".$treat."', ".
				"forum_procedure='".$forum_procedure."', ".
				$signPathQry.
				$signDTmQry.
				"treat_other = '".$treat_other."' ".
				//"WHERE formId = '".$formId."' ";
				"WHERE topo_id = '".$topoId."' ";
			$res = sqlQuery($sql);
		}
		
		/*code, Purpose: Generate HL7 message for Test*/
		$zeissMsgType="TOPOGRAPHY";
		$zeissPatientId = $patientId;
		$zeissTestId = $topoId;
		include("./zeissTestHl7.php");
		/*End code*/

		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "Topogrphy", $topoId);
		/*--interpretation end--*/
		//Super-Bill -----------
		if(!empty($ordrby) && !empty($topoId)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$topoId;
			$arIn["sb_testName"]="Topography";
			$arIn["form_id"]=$formId;
                        $arIn["test_interpreted"]= ((int)$phyName > 0)? true : false;

			$oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);
		}

		//Super-Bill -----------

		//save tests Pdf
		$objTests->saveTestPdfExe_2($patientId,$topoId,"Topography");

		//FormId updates other tables -----
		if(!empty($formId)){
			// Make chart notes valid
			$oChartNote->makeChartNotesValid($formId);
		}
				
		echo "<script>".
			"try{";
				if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
					$afterSaveinPopup = 'yes';
				}else{
					$afterSaveinPopup = 'no';
					echo "top.alert_notification_show(\"Test has been saved.\");";
				}
				echo "
				window.location.replace(\"test_topography.php?noP=".$elem_noP."&tId=".$topoId."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
			}catch(err){}".
		 "</script>";
		exit();
		break;

	}
	case "BScan":{
		//$elem_saveForm=$_POST["elem_saveForm"];
		$patientId=$_POST["elem_patientId"];
		$formId=$_POST["elem_formId"];
		$hd_bscan_mode=$_POST["hd_bscan_mode"];
		$bscanId=$_POST["elem_bscanId"];
		$wind_opn=$_POST["wind_opn"];
		$elem_operatorId=$_POST["elem_operatorId"];
		$elem_operatorName=$_POST["elem_operatorName"];
		$elem_noP=$_POST["elem_noP"];
		$examTime=$_POST["elem_examTime"];
		$examDate=getDateFormatDB($_POST["elem_examDate"]);
		if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
		$test_bscan_eye=$_POST["elem_bscanMeterEye"];
		$techComments=($_POST["techComments"] == 'Technician Comments') ? "" : imw_real_escape_string($_POST["techComments"]);
		$elem_performedByName=$_POST["elem_performedByName"];
		$performedBy=$_POST["elem_performedBy"];
		$ptUnderstanding=$_POST["elem_ptUnderstanding"];
		$diagnosis=imw_real_escape_string($_POST["elem_diagnosis"]);
		$diagnosisOther=imw_real_escape_string($_POST["elem_diagnosisOther"]);
		$sel_preview=$_POST["sel_preview"];
		$reliabilityOd=$_POST["elem_reliabilityOd"];
		$reliabilityOs=$_POST["elem_reliabilityOs"];
		$descOd=imw_real_escape_string($_POST["elem_descOd"]);
		$descOs=imw_real_escape_string($_POST["elem_descOs"]);


		$stable=$_POST["elem_stable"];
		$fuApa=$_POST["elem_fuApa"];
		$tech2InformPt=$_POST["elem_tech2InformPt"];
		$ptInformed=$_POST["elem_ptInformed"];
		$ptInformedNv=$_POST["elem_informedPtNv"];
		$phyName=$_POST["elem_physician"];
		$contiMeds = $_POST["elem_contiMeds"];
		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
		$treat = $_POST["elem_treat"];
		
		$forum_procedure = (isset($_REQUEST['forum_procedure']))? imw_real_escape_string($_REQUEST['forum_procedure']):"";
		$zeissAction = (isset($_REQUEST['zeissAction']))?$_POST['zeissAction']:false;

		$tmp = $_POST["elem_tstod"];
		$tmp2 = "";
		if(count($tmp)>0){
			foreach($tmp as $t1 => $t2){
				if(!empty($tmp2)){
					$tmp2 .= ",";
				}
				$tmp2 .= "".$t2;
			}
		}
		$tstod = "".$tmp2;

		$tmp = $_POST["elem_tstos"];
		$tmp2 = "";
		if(count($tmp)>0){
			foreach($tmp as $t1 => $t2){
				if(!empty($tmp2)){
					$tmp2 .= ",";
				}
				$tmp2 .= "".$t2;
			}
		}
		$tstos = "".$tmp2;

		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
			$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
			$encounter_id = $objTests->getEncounterId();
		}
		
		//Check
		if(empty($bscanId)){
			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;
			if(isset($arrTest2edit["BScan"]) && !empty($arrTest2edit["BScan"])){
				//Check in db
				$cQry = "SELECT test_bscan_id FROM test_bscan
						WHERE patientId ='".$patientId."'
						AND test_bscan_id = '".$arrTest2edit["BScan"]."' ";
				$row = sqlQuery($cQry);
				$bscanId = (($row == false) || empty($row["test_bscan_id"])) ? "0" : $row["test_bscan_id"];

				//Unset Session BScan
				$arrTest2edit["BScan"] = "";
				$arrTest2edit["BScan"] = NULL;
				unset($arrTest2edit["BScan"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);

			}else{

				$cQry = "select test_bscan_id FROM test_bscan WHERE patientId='".$patientId."' AND
						examDate='".$examDate."' AND examTime='".$examTime."' ";
				$row = sqlQuery($cQry);
				$bscanId = (($row == false) && !empty($row["test_bscan_id"])) ? "0" : $row["test_bscan_id"];

			}
		}

		//Test Query
		if(empty($bscanId)){
			$phyName=($zeissAction)? 0 : $phyName; /*Mark test uninterpreted if "Forum Add"*/
			$sql = "INSERT INTO test_bscan ".
					"( ".
					"test_bscan_id, test_bscan_eye, performedBy, diagnosis, ptUnderstanding, reliabilityOd, reliabilityOs, ".
					"descOd, descOs, ".
					"stable, fuApa, ptInformed, phyName, examDate, patientId, formId, diagnosisOther, ".
					"tech2InformPt, techComments, ptInformedNv, examTime, contiMeds, ".
					"encounter_id,ordrby,ordrdt,treat, ".
					"tstod, tstos, forum_procedure ".$signPathFieldName.$signDTmFieldName.
					") ".
					"VALUES ".
					"( ".
					"NULL, '".$test_bscan_eye."', '".$performedBy."', '".$diagnosis."', '".$ptUnderstanding."', '".$reliabilityOd."', '".$reliabilityOs."', ".
					"'".$descOd."', '".$descOs."',".
					"'".$stable."', '".$fuApa."', '".$ptInformed."', '".$phyName."', '".$examDate."',".
					"'".$patientId."', '".$formId."', '".$diagnosisOther."', ".
					"'".$tech2InformPt."', '".$techComments."', '".$ptInformedNv."', '".$examTime."','".$contiMeds."', ".
					"'".$encounter_id."','".$ordrby."','".$ordrdt."','".$treat."', ".
				    "'".$tstod."','".$tstos."', '".$forum_procedure."' ".$signPathFieldValue.$signDTmFieldValue.
					") ";
			$ress = imw_query($sql);
			$insertId = imw_insert_id();
			$bscanId = $insertId;
		}else if(!empty($bscanId)){
			$sql = "UPDATE test_bscan ".
				"SET ".
				"test_bscan_eye = '".$test_bscan_eye."', ".
				"performedBy = '".$performedBy."', ".
				"diagnosis = '".$diagnosis."', ".
				"ptUnderstanding = '".$ptUnderstanding."', ".
				"reliabilityOd = '".$reliabilityOd."', ".
				"reliabilityOs = '".$reliabilityOs."', ".
				"descOd = '".$descOd."', ".
				"descOs = '".$descOs."', ".
				"stable = '".$stable."', ".
				"fuApa = '".$fuApa."', ".
				"ptInformed = '".$ptInformed."', ";
				
				if(!$zeissAction){  /*Prevent changing interpretation status if "Forum Add/Delete"*/
					$sql .= "phyName = '".$phyName."', ";
				}
				
			$sql .= "examDate = '".$examDate."', ".
				//"patientId = '".$patientId."', ".
				"tech2InformPt = '".$tech2InformPt."', ".
				"diagnosisOther = '".$diagnosisOther."', ".
				"techComments = '".$techComments."', ".
				"ptInformedNv = '".$ptInformedNv."', ".
				"examTime = '".$examTime."', ".
				"contiMeds = '".$contiMeds."', ".
				"encounter_id='".$encounter_id."', ".
				"ordrby='".$ordrby."',ordrdt='".$ordrdt."', ".
				"treat = '".$treat."', ".
				"forum_procedure = '".$forum_procedure."', ".
				 $signPathQry.
				 $signDTmQry.
				"tstod = '".$tstod."', tstos = '".$tstos."' ".
				//"WHERE formId = '".$formId."' ";
				"WHERE test_bscan_id = '".$bscanId."' ";
			$res = sqlQuery($sql);
		}
		
		/*code, Purpose: Generate HL7 message for Test*/
		$zeissMsgType="BSCAN";
		$zeissPatientId = $patientId;
		$zeissTestId = $bscanId;
		include("./zeissTestHl7.php");
		/*End code*/

		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "BScan", $bscanId);
		/*--interpretation end--*/
		//$cls_notifications->set_notification_status('testseye');
		
		//Super-Bill -----------
		if(!empty($ordrby) && !empty($bscanId)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$bscanId;
			$arIn["sb_testName"]="B-Scan";
                        $arIn["form_id"]=$formId;
                        $arIn["test_interpreted"]= ((int)$phyName > 0)? true : false;
                        
			$oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);	
		}
		//Super-Bill -----------

		//save tests Pdf
		$objTests->saveTestPdfExe_2($patientId,$bscanId,"B-Scan");

		//FormId updates other tables -----
		if(!empty($formId)){
			// Make chart notes valid
			$oChartNote->makeChartNotesValid($formId);
		}

		echo "<script>".
				"try{";
				if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
					$afterSaveinPopup = 'yes';
				}else{
					$afterSaveinPopup = 'no';
					echo "top.alert_notification_show(\"Test has been saved.\");";
				}
				echo "
				}catch(err){}"."
				window.location.replace(\"test_bscan.php?noP=".$elem_noP."&tId=".$bscanId."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
			 </script>";
		exit();
		//Javascript ----------
	
		
		break;
	}
	case "CellCount":{
		//Post
		$patientId=$_POST["elem_patientId"];
		$formId=$_POST["elem_formId"];
		$hd_cellcnt_mode=$_POST["hd_cellcnt_mode"];
		$cellCntId=$_POST["elem_cellCntId"];
		$wind_opn=$_POST["wind_opn"];
		$elem_operatorId=$_POST["elem_operatorId"];
		$elem_operatorName=$_POST["elem_operatorName"];
		$elem_noP=$_POST["elem_noP"];
		$examTime=$_POST["elem_examTime"];
		$examDate=getDateFormatDB($_POST["elem_examDate"]);
		if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
		$test_cellcnt_eye=$_POST["elem_cellCntEye"];
		$techComments=($_POST["techComments"] == 'Technician Comments') ? "" : imw_real_escape_string($_POST["techComments"]);
		$elem_performedByName=$_POST["elem_performedByName"];
		$performedBy=$_POST["elem_performedBy"];
		$ptUnderstanding=$_POST["elem_ptUnderstanding"];
		$diagnosis=imw_real_escape_string($_POST["elem_diagnosis"]);
		$diagnosisOther=imw_real_escape_string($_POST["elem_diagnosisOther"]);
		$sel_preview=$_POST["sel_preview"];
		$reliabilityOd=$_POST["elem_reliabilityOd"];
		$reliabilityOs=$_POST["elem_reliabilityOs"];
		$descOd=imw_real_escape_string($_POST["elem_descOd"]);
		$descOs=imw_real_escape_string($_POST["elem_descOs"]);
		
		$forum_procedure = (isset($_REQUEST['forum_procedure']))?imw_real_escape_string($_REQUEST['forum_procedure']):"";
		$zeissAction = (isset($_REQUEST['zeissAction']))?$_POST['zeissAction']:false;
	
		$stable=$_POST["elem_stable"];
		$fuApa=$_POST["elem_fuApa"];
		$tech2InformPt=$_POST["elem_tech2InformPt"];
		$ptInformed=$_POST["elem_ptInformed"];
		$ptInformedNv=$_POST["elem_informedPtNv"];
	
		$phyName=$_POST["elem_physician"];
		$contiMeds = $_POST["elem_contiMeds"];
		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
	
		$numod = imw_real_escape_string($_POST["elem_numOd"]);
		$cdod = imw_real_escape_string($_POST["elem_cdOd"]);
		$avgod = imw_real_escape_string($_POST["elem_avgOd"]);
		$sdod = imw_real_escape_string($_POST["elem_sdOd"]);
		$cvod = imw_real_escape_string($_POST["elem_cvOd"]);
		$mxod = imw_real_escape_string($_POST["elem_mxOd"]);
		$mnod = imw_real_escape_string($_POST["elem_mnOd"]);
		$e6aod = imw_real_escape_string($_POST["elem_6aOd"]);
		$cctod = imw_real_escape_string($_POST["elem_cctOd"]);
		if(!empty($_POST["elem_ppyOd"])){
			$ppod = "Y";
		}else if(!empty($_POST["elem_ppnOd"])){
			$ppod = "N";
		}
	
		$numos = imw_real_escape_string($_POST["elem_numOs"]);
		$cdos = imw_real_escape_string($_POST["elem_cdOs"]);
		$avgos = imw_real_escape_string($_POST["elem_avgOs"]);
		$sdos = imw_real_escape_string($_POST["elem_sdOs"]);
		$cvos = imw_real_escape_string($_POST["elem_cvOs"]);
		$mxos = imw_real_escape_string($_POST["elem_mxOs"]);
		$mnos = imw_real_escape_string($_POST["elem_mnOs"]);
		$e6aos = imw_real_escape_string($_POST["elem_6aOs"]);
		$cctos = imw_real_escape_string($_POST["elem_cctOs"]);
		if(!empty($_POST["elem_ppyOs"])){
			$ppos = "Y";
		}else if(!empty($_POST["elem_ppnOs"])){
			$ppos = "N";
		}
	
		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
		$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
		$encounter_id = $objTests->getEncounterId();
		}

		//Check
		if(empty($cellCntId)){
			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;
			if(isset($arrTest2edit["CellCount"]) && !empty($arrTest2edit["CellCount"])){
				//Check in db
				$cQry = "SELECT test_cellcnt_id FROM test_cellcnt
						WHERE patientId ='".$patientId."'
						AND test_cellcnt_id = '".$arrTest2edit["CellCount"]."' ";
				$row = sqlQuery($cQry);
				$cellCntId = (($row == false) || empty($row["test_cellcnt_id"])) ? "0" : $row["test_cellcnt_id"];
	
				//Unset Session TestOther
				$arrTest2edit["CellCount"] = "";
				$arrTest2edit["CellCount"] = NULL;
				unset($arrTest2edit["CellCount"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);
			}else{	
				$cQry = "select test_cellcnt_id FROM test_cellcnt WHERE patientId='".$patientId."' AND
						examDate='".$examDate."' AND examTime='".$examTime."' ";
				$row = sqlQuery($cQry);
				$cellCntId = (($row == false) && !empty($row["test_cellcnt_id"])) ? "0" : $row["test_cellcnt_id"];
			}
		}
	
		//Test Query
		if(empty($cellCntId)){
			$phyName=($zeissAction) ? 0 : $phyName; /*Mark test uninterpreted if "Forum Add"*/
			$sql = "INSERT INTO test_cellcnt ".
					"( ".
					"test_cellcnt_id, test_cellcnt_eye, performedBy, diagnosis, ptUnderstanding, reliabilityOd, reliabilityOs, ".
					"descOd, descOs, ".
					"stable, fuApa, ptInformed, phyName, examDate, patientId, formId, diagnosisOther, ".
					"tech2InformPt, techComments, ptInformedNv, examTime, contiMeds, ".
					"encounter_id,ordrby,ordrdt, ".
					"numod, cdod, avgod, sdod, cvod, mxod, mnod, e6aod, cctod, ppod, ".
					"numos, cdos, avgos, sdos, cvos, mxos, mnos, e6aos, cctos, ppos, forum_procedure ".$signPathFieldName.$signDTmFieldName.
					") ".
					"VALUES ".
					"( ".
					"NULL, '".$test_cellcnt_eye."', '".$performedBy."', '".$diagnosis."', '".$ptUnderstanding."', '".$reliabilityOd."', '".$reliabilityOs."', ".
					"'".$descOd."', '".$descOs."',".
					"'".$stable."', '".$fuApa."', '".$ptInformed."', '".$phyName."', '".$examDate."', '".$patientId."', '".$formId."', '".$diagnosisOther."', ".
					"'".$tech2InformPt."', '".$techComments."', '".$ptInformedNv."', '".$examTime."','".$contiMeds."', ".
					"'".$encounter_id."','".$ordrby."','".$ordrdt."', ".
					"'".$numod."', '".$cdod."', '".$avgod."', '".$sdod."', '".$cvod."', ".
					"'".$mxod."', '".$mnod."', '".$e6aod."', '".$cctod."', '".$ppod."', ".
					"'".$numos."', '".$cdos."', '".$avgos."', '".$sdos."', '".$cvos."', ". 
					"'".$mxos."', '".$mnos."', '".$e6aos."', '".$cctos."', '".$ppos."', '".$forum_procedure."' ".$signPathFieldValue.$signDTmFieldValue.
					") ";
			$ress = imw_query($sql);
			$insertId = imw_insert_id();
			$cellCntId = $insertId;
		}else if(!empty($cellCntId)){
			$sql = "UPDATE test_cellcnt ".
				"SET ".
				"test_cellcnt_eye = '".$test_cellcnt_eye."', ".
				"performedBy = '".$performedBy."', ".
				"diagnosis = '".$diagnosis."', ".
				"ptUnderstanding = '".$ptUnderstanding."', ".
				"reliabilityOd = '".$reliabilityOd."', ".
				"reliabilityOs = '".$reliabilityOs."', ".
				"descOd = '".$descOd."', ".
				"descOs = '".$descOs."', ".
				"stable = '".$stable."', ".
				"fuApa = '".$fuApa."', ".
				"ptInformed = '".$ptInformed."', ";
				
				if(!$zeissAction){  /*Prevent changing interpretation status if "Forum Add"*/
					$sql .= "phyName = '".$phyName."', ";
				}
				
			$sql .= "examDate = '".$examDate."', ".
				//"patientId = '".$patientId."', ".
				"tech2InformPt = '".$tech2InformPt."', ".
				"diagnosisOther = '".$diagnosisOther."', ".
				"techComments = '".$techComments."', ".
				"ptInformedNv = '".$ptInformedNv."', ".
				"examTime = '".$examTime."', ".
				"contiMeds = '".$contiMeds."', ".
				"encounter_id='".$encounter_id."', ".
				"ordrby='".$ordrby."',ordrdt='".$ordrdt."', ".
	
				"numod='".$numod."', ".
				"cdod='".$cdod."', ".
				"avgod='".$avgod."', ".
				"sdod='".$sdod."', ".
				"cvod='".$cvod."', ".
				"mxod='".$mxod."', ".
				"mnod='".$mnod."', ".
				"e6aod='".$e6aod."', ".
				"cctod='".$cctod."', ".
				"ppod='".$ppod."', ".
	
				"numos='".$numos."', ".
				"cdos='".$cdos."', ".
				"avgos='".$avgos."', ".
				"sdos='".$sdos."', ".
				"cvos='".$cvos."', ".
				"mxos='".$mxos."', ".
				"mnos='".$mnos."', ".
				"e6aos='".$e6aos."', ".
				"cctos='".$cctos."', ".
				"forum_procedure='".$forum_procedure."', ".
				 $signPathQry.
				 $signDTmQry.
				"ppos='".$ppos."' ".
	
				//"WHERE formId = '".$formId."' ";
				"WHERE test_cellcnt_id = '".$cellCntId."' ";
			$res = sqlQuery($sql);
		}
	
		/*code, Purpose: Generate HL7 message for Test*/
		$zeissMsgType="CELLCOUNT";
		$zeissPatientId = $patientId;
		$zeissTestId = $cellCntId;
		include("./zeissTestHl7.php");
		/*End code*/
	
		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "CellCount", $cellCntId);

		//Super-Bill -----------
		if(!empty($ordrby) && !empty($cellCntId)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$cellCntId;
			$arIn["sb_testName"]="CellCount";
			$arIn["form_id"]=$formId;
                        $arIn["test_interpreted"]= ((int)$phyName > 0)? true : false;

                        $oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);
		}

		//save tests Pdf
		$objTests->saveTestPdfExe_2($patientId,$cellCntId,"CellCount");
	
		//FormId updates other tables -----
		if(!empty($formId)){
			// Make chart notes valid
			$oChartNote->makeChartNotesValid($formId);
		}
	
		//Redirect
		echo "<script>".
				"try{";
				if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
					$afterSaveinPopup = 'yes';
				}else{
					$afterSaveinPopup = 'no';
					echo "top.alert_notification_show(\"Test has been saved.\");";
				}
				echo "
				}catch(err){}
				window.location.replace(\"test_cellcount.php?noP=".$elem_noP."&tId=".$cellCntId."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
			 </script>";
		exit();
		
		break;
	}
	case "Pachy":{
		$examDate = getDateFormatDB($_POST["elem_examDate"]);
		if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
		$pachyMeter = $_POST["elem_pachyMeter"];
		$pachyMeterEye = $_POST["elem_pachyMeterEye"];
		$performedBy = $_POST["elem_performedBy"];
		$ptUnderstanding = $_POST["elem_ptUnderstanding"];
		$diagnosis = imw_real_escape_string($_POST["elem_diagnosis"]);
		$diagnosisOther = imw_real_escape_string($_POST["elem_diagnosisOther"]);
		$reliabilityOd = $_POST["elem_reliabilityOd"];
		$reliabilityOs = $_POST["elem_reliabilityOs"];
		$pachyOd = $_POST["elem_pachyOd"];
		$pachy_od_readings = $_POST["elem_pachy_od_readings"];
		$pachy_od_average = $_POST["elem_pachy_od_average"];
		$pachy_od_correction_value = $_POST["elem_pachy_od_correction_value"];
		$descOd = imw_real_escape_string($_POST["elem_descOd"]);
		$pachyOs = $_POST["elem_pachyOs"];
		$pachy_os_readings = $_POST["elem_pachy_os_readings"];
		$pachy_os_average = $_POST["elem_pachy_os_average"];
		$pachy_os_correction_value = $_POST["elem_pachy_os_correction_value"];
		$descOs = imw_real_escape_string($_POST["elem_descOs"]);
		$stable = $_POST["elem_stable"];
		$fuApa = $_POST["elem_fuApa"];
		$ptInformed = $_POST["elem_ptInformed"];
		$comments = imw_real_escape_string($_POST["elem_comments"]);
		$signature = $_POST["elem_pachySign"];
		$phyName = $_POST["elem_physician"];
		$patientId = $_POST["elem_patientId"];
		$formId = $_POST["elem_formId"];
		$hd_pachy_mode = $_POST["hd_pachy_mode"];
		$pachy_id = $_POST["elem_pachyId"];

		$Central_OD = $_POST["Central_OD"];
		$Nasal_OD = $_POST["Nasal_OD"];
		$Inferior_OD = $_POST["Inferior_OD"];
		$Temporal_OD = $_POST["Temporal_OD"];
		$Superior_OD = $_POST["Superior_OD"];

		$Central_OS = $_POST["Central_OS"];
		$Nasal_OS = $_POST["Nasal_OS"];
		$Inferior_OS = $_POST["Inferior_OS"];
		$Temporal_OS = $_POST["Temporal_OS"];
		$Superior_OS = $_POST["Superior_OS"];
		$tech2InformPt = $_POST["elem_tech2InformPt"];
		$ptInformedNv = $_POST["elem_informedPtNv"];
		$elem_noP = $_POST["elem_noP"];
		$examTime = $_POST["elem_examTime"];
		$contiMeds = $_POST["elem_contiMeds"];
		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
		
		$iris_iridec_od = $_POST["elem_iris_iridec_od"];
		$iris_iridec_os = $_POST["elem_iris_iridec_os"];		

		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
			$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
			$encounter_id = $objTests->getEncounterId();
		}

		//Tech Comments
		if($_POST["techComments"] == 'Technician Comments'){
			$_POST["techComments"] = '';
		}
		$elem_techComments = imw_real_escape_string($_POST["techComments"]);

		//Check
		if(empty($pachy_id)){

			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;
			if(isset($arrTest2edit["Pacchy"]) && !empty($arrTest2edit["Pacchy"])){

				//Check in db
				$cQry = "select pachy_id FROM pachy
						WHERE patientId ='".$patientId."'
						AND pachy_id = '".$arrTest2edit["Pacchy"]."' ";
				$row = sqlQuery($cQry);
				$pachy_id = (($row == false) || empty($row["pachy_id"])) ? "0" : $row["pachy_id"];

				//Unset Session Pacchy
				$arrTest2edit["Pacchy"] = "";
				$arrTest2edit["Pacchy"] = NULL;
				unset($arrTest2edit["Pacchy"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);

			}else{

				$cQry = "select pachy_id FROM pachy WHERE patientId='".$patientId."'
						AND examDate = '".$examDate."' AND examTime = '".$examTime."' ";
				$row = sqlQuery($cQry);
				$pachy_id = (($row == false) || empty($row["pachy_id"])) ? "0" : $row["pachy_id"];

			}
		}

		if(empty($pachy_id)){
			$sql = "INSERT INTO pachy ".
					"( ".
					"pachy_id, pachyMeter, pachyMeterEye, performedBy, diagnosis, ptUnderstanding, reliabilityOd, reliabilityOs, ".
					"pachyOd, pachyOs, pachy_od_readings, pachy_od_average, pachy_od_correction_value, ".
					"pachy_os_readings, pachy_os_average, pachy_os_correction_value, descOd, descOs, ".
					"Central_OD, Nasal_OD, Inferior_OD, Temporal_OD, Superior_OD, ".
					"Central_OS, Nasal_OS, Inferior_OS, Temporal_OS, Superior_OS, ".
					"stable, fuApa, ptInformed, comments, signature, phyName, examDate, patientId, formId, diagnosisOther, ".
					"tech2InformPt,techComments,ptInformedNv, ".
					"examTime,contiMeds, ".
					"encounter_id,ordrby,ordrdt, ".
					"iris_iridec_od, iris_iridec_os ".$signPathFieldName.$signDTmFieldName.
					") ".
					"VALUES ".
					"( ".
					"NULL, '".$pachyMeter."', '".$pachyMeterEye."', '".$performedBy."', '".$diagnosis."', '".$ptUnderstanding."', '".$reliabilityOd."', '".$reliabilityOs."', ".
					"'".$pachyOd."', '".$pachyOs."', '".$pachy_od_readings."', '".$pachy_od_average."', '".$pachy_od_correction_value."', ".
					"'".$pachy_os_readings."', '".$pachy_os_average."', '".$pachy_os_correction_value."', '".$descOd."', '".$descOs."', ".
					"'".$Central_OD."', '".$Nasal_OD."', '".$Inferior_OD."', '".$Temporal_OD."', '".$Superior_OD."', ".
					"'".$Central_OS."', '".$Nasal_OS."', '".$Inferior_OS."', '".$Temporal_OS."', '".$Superior_OS."', ".
					"'".$stable."', '".$fuApa."', '".$ptInformed."', '".$comments."', '".$signature."', '".$phyName."', '".$examDate."', '".$patientId."', '".$formId."', '".$diagnosisOther."', ".
					"'".$tech2InformPt."','".$elem_techComments."', '".$ptInformedNv."', ".
					"'".$examTime."','".$contiMeds."', ".
					"'".$encounter_id."','".$ordrby."','".$ordrdt."', ".
					"'".$iris_iridec_od."', '".$iris_iridec_os."' ".$signPathFieldValue.$signDTmFieldValue.
					") ";
			$ress		= imw_query($sql);
			$insertId 	= imw_insert_id();
			$pachy_id 	= $insertId;
		}else if(!empty($pachy_id)){
			$sql = "UPDATE pachy ".
				"SET ".
				"pachyMeter = '".$pachyMeter."', ".
				"pachyMeterEye = '".$pachyMeterEye."', ".
				"performedBy = '".$performedBy."', ".
				"diagnosis = '".$diagnosis."', ".
				"ptUnderstanding = '".$ptUnderstanding."', ".
				"reliabilityOd = '".$reliabilityOd."', ".
				"reliabilityOs = '".$reliabilityOs."', ".
				"pachyOd = '".$pachyOd."', ".
				"pachyOs = '".$pachyOs."', ".
				"pachy_od_readings = '".$pachy_od_readings."', ".
				"pachy_od_average = '".$pachy_od_average."', ".
				"pachy_od_correction_value = '".$pachy_od_correction_value."', ".
				"pachy_os_readings = '".$pachy_os_readings."', ".
				"pachy_os_average = '".$pachy_os_average."', ".
				"pachy_os_correction_value = '".$pachy_os_correction_value."', ".
				"descOd = '".$descOd."', ".
				"descOs = '".$descOs."', ".
				"stable = '".$stable."', ".
				"fuApa = '".$fuApa."', ".
				"ptInformed = '".$ptInformed."', ".
				"comments = '".$comments."', ".
				"signature = '".$signature."', ".
				"phyName = '".$phyName."', ".
				"examDate = '".$examDate."', ".
				//"patientId = '".$patientId."', ".
				"Central_OD = '".$Central_OD."', ".
				"Nasal_OD = '".$Nasal_OD."', ".
				"Inferior_OD = '".$Inferior_OD."', ".
				"Temporal_OD = '".$Temporal_OD."', ".
				"Superior_OD = '".$Superior_OD."', ".
				"Central_OS = '".$Central_OS."', ".
				"Nasal_OS = '".$Nasal_OS."', ".
				"Inferior_OS = '".$Inferior_OS."', ".
				"Temporal_OS = '".$Temporal_OS."', ".
				"Superior_OS = '".$Superior_OS."', ".
				"tech2InformPt = '".$tech2InformPt."', ".
				"diagnosisOther = '".$diagnosisOther."', ".
				"techComments = '".$elem_techComments."', ".
				"ptInformedNv = '".$ptInformedNv."', ".
				"examTime = '".$examTime."', ".
				"contiMeds = '".$contiMeds."', ".
				"encounter_id='".$encounter_id."', ".
				"ordrby='".$ordrby."',ordrdt='".$ordrdt."', ".
				$signPathQry.
				$signDTmQry.
				"iris_iridec_od='".$iris_iridec_od."', iris_iridec_os='".$iris_iridec_os."' ".
				//"WHERE formId = '".$formId."' ";
				"WHERE pachy_id = '".$pachy_id."' ";
			$res = sqlQuery($sql);
		}

		/*--interpretation starts--*/
			$objTests->interpret_if_scan_exists($_SESSION['patient'], "Pacchy", $pachy_id);
		/*--interpretation end--*/
		//$cls_notifications->set_notification_status('testseye');
		//Super-Bill -----------
		if(!empty($ordrby) && !empty($pachy_id)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$pachy_id;
			$arIn["sb_testName"]="Pachy";
                        $arIn["form_id"]=$formId;
                        $arIn["test_interpreted"]= ((int)$phyName > 0)? true : false;
                        
                        $oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);
		}
		//Super-Bill -----------

		//save tests Pdf
		$objTests->saveTestPdfExe_2($patientId,$pachy_id,"Pachy");

		//FormId updates other tables -----
		if(!empty($formId)){
			// Make chart notes valid
			$oChartNote->makeChartNotesValid($formId);
							
			//Synchronize chart_correction_values			
			if((!empty($pachy_od_correction_value) || !empty($pachy_os_correction_value))){			
				$arr = array("elem_formId" => $formId, "elem_od_readings" => $pachy_od_readings,
						  "elem_od_average" => $pachy_od_average, "elem_od_correction_value" => $pachy_od_correction_value,
						  "elem_os_readings" => $pachy_os_readings, "elem_os_average" => $pachy_os_average,
						  "elem_os_correction_value" => $pachy_os_correction_value, "patientid" => $patientId,
						  "elem_cor_date" => $examDate
						 );
				$objTests->saveCorrectionValues($arr);
				
				//chartPtDiag
				$sql = "SELECT * FROM chart_ptPastDiagnosis WHERE patient_id='".$patientId."' ";
				$row = sqlQuery($sql);
				if( $row != false ){
					$ptPastPachy = "";
					$arr=array();
					$arr["od_readings"]=$pachy_od_readings;
					$arr["od_average"]=$pachy_od_average;
					$arr["od_correction_value"]=$pachy_od_correction_value;
					$arr["os_readings"]=$pachy_os_readings;
					$arr["os_average"]=$pachy_os_average;
					$arr["os_correction_value"]=$pachy_os_correction_value;
					$arr["cor_date"]=$examDate;
					$ptPastPachy = serialize($arr);
					$sql = "UPDATE chart_ptPastDiagnosis SET pachy = '".imw_real_escape_string($ptPastPachy)."' WHERE patient_id='".$patientId."' ";
					$res = sqlQuery($sql);
				}
				
				//Sync Ascan
				//check if A/Scan is made and syncronize correction values
				//check
				$sql = "SELECT surgical_id FROM surgical_tbl WHERE form_id='".$formId."' AND patient_id='".$patientId."' ";
				$row = sqlQuery($sql);
				if($row != false){
					$pachymetryValOD = !empty($pachy_od_average) ? $pachy_od_average : $pachy_od_readings ;
					$pachymetryValOS = !empty($pachy_os_average) ? $pachy_os_average : $pachy_os_readings ;
					
					//Update
					$sql = "UPDATE surgical_tbl SET ".
						 "pachymetryValOD = '".$pachymetryValOD."', ".
						 "pachymetryCorrecOD = '".$pachy_od_correction_value."', ".
						 "pachymetryValOS = '".$pachymetryValOS."', ".
						 "pachymetryCorrecOS = '".$pachy_os_correction_value."' ".
						 "WHERE form_id = '".$formId."' AND patient_id='".$patientId."' ";
					$row = sqlQuery($sql);
				}
				
				//Sync IOL Maser
				//check if IOL Maser is made and syncronize correction values
				//check
				$sql = "SELECT iol_master_id FROM iol_master_tbl WHERE form_id='".$formId."' AND patient_id='".$patientId."' ";
				$row = sqlQuery($sql);
				if($row != false){
					$pachymetryValOD = !empty($pachy_od_average) ? $pachy_od_average : $pachy_od_readings ;
					$pachymetryValOS = !empty($pachy_os_average) ? $pachy_os_average : $pachy_os_readings ;
					
					//Update
					$sql = "UPDATE iol_master_tbl SET ".
						 "pachymetryValOD = '".$pachymetryValOD."', ".
						 "pachymetryCorrecOD = '".$pachy_od_correction_value."', ".
						 "pachymetryValOS = '".$pachymetryValOS."', ".
						 "pachymetryCorrecOS = '".$pachy_os_correction_value."' ".
						 "WHERE form_id = '".$formId."' AND patient_id='".$patientId."' ";
					$row = sqlQuery($sql);
				}				
				
			}
		}
		
		echo "<script>".
				"try{";
				if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
					$afterSaveinPopup = 'yes';
				}else{
					$afterSaveinPopup = 'no';
					echo "top.alert_notification_show(\"Test has been saved.\");";
				}
				echo "
					window.location.replace(\"test_pacchy.php?noP=".$elem_noP."&tId=".$pachy_id."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
				}catch(err){}".
			 "</script>";
		exit();
		break;
	}
	case "GDX":{
		$patient_id = $_POST["elem_patientId"];
		$form_id = $_POST["elem_formId"];
		$hd_gdx_mode = $_POST["hd_gdx_mode"];
		$gdx_id =  $_POST["elem_gdxId"];
		$examDate =  getDateFormatDB($_POST["elem_examDate"]);
		if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
		$scanLaserEye =  $_POST["elem_scanLaserEye"];
		$performBy =  $_POST["elem_performedBy"];
		$ptUndersatnding =  $_POST["elem_ptUndersatnding"];
		$diagnosis =  imw_real_escape_string($_POST["elem_diagnosis"]);
		$reliabilityOd =  $_POST["elem_reliabilityOd"];
		$reliabilityOs =  $_POST["elem_reliabilityOs"];
		$scanLaserOd =  $_POST["elem_scanLaserOd"];
		$descOd =  $_POST["elem_descOd"];
		$scanLaserOs =  $_POST["elem_scanLaserOs"];
		$descOs =  $_POST["elem_descOs"];
		$stable =  $_POST["elem_stable"];
		$monitorIOP =  $_POST["elem_monitorIOP"];
		$fuApa =  $_POST["elem_fuApa"];
		$ptInformed =  $_POST["elem_ptInformed"];
		$comments =  imw_real_escape_string($_POST["elem_comments"]);
		$signature =  $_POST["elem_vfSign"];
		$phyName =  $_POST["elem_physician"];
		$diagnosisOther =  imw_real_escape_string($_POST["elem_diagnosisOther"]);
		$tech2InformPt = $_POST["elem_tech2InformPt"];
		$Normal_OD_PoorStudy = $_POST["elem_normal_poorStudy_od"];
		$Normal_OS_PoorStudy = $_POST["elem_normal_poorStudy_os"];
		$elem_trgtIopOd = imw_real_escape_string($_POST["elem_targetIop_OD"]);
		$elem_trgtIopOs = imw_real_escape_string($_POST["elem_targetIop_OS"]);		
		
		//$elem_interpretedBy = $_POST["elem_interpretedBy"];
		if($_POST["techComments"] == 'Technician Comments'){
			$_POST["techComments"] = '';
		}
		$elem_techComments = imw_real_escape_string($_POST["techComments"]);
		$ptInformedNv = $_POST["elem_informedPtNv"];
		$elem_noP = $_POST["elem_noP"];
		$examTime = $_POST["elem_examTime"];
		$contiMeds = $_POST["elem_contiMeds"];
		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);

		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
			$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
			$encounter_id = $objTests->getEncounterId();
		}		
		
		//TestRes
		$normal_OD = $normal_OS = "";
		//Od
		if(isset($_POST["Normal_OD_T"]) && !empty($_POST["Normal_OD_T"])){
			$normal_OD .= $_POST["Normal_OD_T"];
		}
		if(isset($_POST["elem_normal_poorStudy_od"]) && !empty($_POST["elem_normal_poorStudy_od"])){
			$normal_OD .= (empty($normal_OD)) ? "" : ",";
			$normal_OD .= $_POST["elem_normal_poorStudy_od"];
		}
		//Os
		if(isset($_POST["Normal_OS_T"]) && !empty($_POST["Normal_OS_T"])){
			$normal_OS .= $_POST["Normal_OS_T"];
		}
		if(isset($_POST["elem_normal_poorStudy_os"]) && !empty($_POST["elem_normal_poorStudy_os"])){
			$normal_OS .= (empty($normal_OS)) ? "" : ",";
			$normal_OS .= $_POST["elem_normal_poorStudy_os"];
		}
		
		//summary
		$summaryOd = $summaryOs = "";
		$nf_Thick_OD = $nf_Thick_OS = "";
		$quad_devi_OD=$quad_devi_OS="";
		$nf_Indic_OD=$nf_Indic_OS="";
		
		//Od
		$arr = array(""=>$Normal_OD_T, "Poor Study"=>$elem_normal_poorStudy_od);
		$summaryOd .= $objTests->getTestSumm($arr,"Normal");
		
		$arr = array("Normal Appearing Nerve Fiber Layer"=>$elem_normal_app_OD,"Suspicious Nerve Fiber Layer Thinning"=>$elem_sus_nrv_OD,
					"Definite Nerve Fiber Layer Thinning"=>$elem_def_nrv_OD);
		$nf_Thick_OD = $objTests->getTestSumm($arr,"Nerve Fiber Thickness Map");
		$summaryOd .= $nf_Thick_OD;		
		
		$arr = array("Superior Quardrant"=>$elem_sus_quad_OD,"Nasal Quadrant"=>$elem_nas_quad_OD,
					"Temporal Quadrant"=>$elem_temp_quad_OD,"Inferior Quadrant"=>$elem_inf_quad_OD);
		$quad_devi_OD = $objTests->getTestSumm($arr,"Quadrant Deviation Map Outside Normal");
		$summaryOd .= $quad_devi_OD;		
		
		$arr = array("0-30 Normal (Low risk of Glaucoma)"=>$elem_30_normal_OD,"31-50 Borderline"=>$elem_50_normal_OD,
					"51+ (Abnormal risk of Glaucoma)"=>$elem_51_normal_OD);
		$nf_Indic_OD = $objTests->getTestSumm($arr,"Nerve Fiber Indicator");
		$summaryOd .= $nf_Indic_OD;
		
		$arr = array($Others_OD=>$Others_OD);
		$summaryOd .= $objTests->getTestSumm($arr,"Other");
		//Os
		$arr = array(""=>$Normal_OS_T, "Poor Study"=>$elem_normal_poorStudy_os);
		$summaryOs .= $objTests->getTestSumm($arr,"Normal");
		
		$arr = array("Normal Appearing Nerve Fiber Layer"=>$elem_normal_app_OS,"Suspicious Nerve Fiber Layer Thinning"=>$elem_sus_nrv_OS,
					"Definite Nerve Fiber Layer Thinning"=>$elem_def_nrv_OS);
		$nf_Thick_OS = $objTests->getTestSumm($arr,"Nerve Fiber Thickness Map");
		$summaryOs .= $nf_Thick_OS;
		
		$arr = array("Superior Quardrant"=>$elem_sus_quad_OS,"Nasal Quadrant"=>$elem_nas_quad_OS,
					"Temporal Quadrant"=>$elem_temp_quad_OS,"Inferior Quadrant"=>$elem_inf_quad_OS);
		$quad_devi_OS = $objTests->getTestSumm($arr,"Quadrant Deviation Map Outside Normal");
		$summaryOs .= $quad_devi_OS;
		
		$arr = array("0-30 Normal (Low risk of Glaucoma)"=>$elem_30_normal_OS,"31-50 Borderline"=>$elem_50_normal_OS,
					"51+ (Abnormal risk of Glaucoma)"=>$elem_51_normal_OS);
		$nf_Indic_OS = $objTests->getTestSumm($arr,"Nerve Fiber Indicator");
		$summaryOs .= $nf_Indic_OS;
		
		$arr = array($Others_OS=>$Others_OS);
		$summaryOs .= $objTests->getTestSumm($arr,"Other");

		$descOd = (!empty($summaryOd)) ? imw_real_escape_string("".$summaryOd) : "";
		$descOs = (!empty($summaryOs)) ? imw_real_escape_string("".$summaryOs) : "";

		//echo "Desc:<br>".$descOd;
		//echo "Desc:<br>".$descOs;

		//summary

		//check
		if(empty($gdx_id)){
			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "";
			if(isset($arrTest2edit["GDX"]) && !empty($arrTest2edit["GDX"])){
				//Check in db
				$cQry = "select gdx_id FROM test_gdx
						WHERE patient_id ='".$patient_id."'
						AND gdx_id = '".$arrTest2edit["GDX"]."' ";
				$row = sqlQuery($cQry);
				$gdx_id = (($row == false) || empty($row["gdx_id"])) ? "0" : $row["gdx_id"];

				//Unset Session GDX
				$arrTest2edit["GDX"] = "";
				$arrTest2edit["GDX"] = NULL;
				unset($arrTest2edit["GDX"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);

			}else{
				$cQry = "SELECT gdx_id FROM test_gdx
						WHERE patient_id='".$patient_id."' AND examDate = '".$examDate."' AND examTime = '".$examTime."' ";
				$row = sqlQuery($cQry);
				$gdx_id = (($row == false) || empty($row["gdx_id"])) ? "0" : $row["gdx_id"];
			}
		}

		if(empty($gdx_id)){
			$sql = "INSERT INTO test_gdx ".
					"( ".
					"gdx_id, scanLaserEye, performBy, diagnosis, ptUndersatnding, ".
					"reliabilityOd, reliabilityOs, descOd, descOs, ".
					"stable, fuApa, ptInformed, comments, signature, phyName, examDate, ".
					//"Normal_OD_T, Normal_OD_1, Normal_OD_2, Normal_OD_3, Normal_OD_4, ".
					//"BorderLineDefect_OD_T, BorderLineDefect_OD_1, BorderLineDefect_OD_2, BorderLineDefect_OD_3, BorderLineDefect_OD_4, ".
					//"Abnorma_OD_T, Abnorma_OD_1, Abnorma_OD_2, Abnorma_OD_3, Abnorma_OD_4, ".
					"Others_OD, ".
					//"Normal_OS_T, Normal_OS_1, Normal_OS_2, Normal_OS_3, Normal_OS_4, ".
					//"BorderLineDefect_OS_T, BorderLineDefect_OS_1, BorderLineDefect_OS_2, BorderLineDefect_OS_3, BorderLineDefect_OS_4, ".
					//"Abnorma_OS_T, Abnorma_OS_1, Abnorma_OS_2, Abnorma_OS_3, Abnorma_OS_4, ".
					"Others_OS, ".
					"patient_id, form_id, diagnosisOther, monitorIOP,tech2InformPt, ".
					//"Normal_OD_PoorStudy, Normal_OS_PoorStudy, ".
					//"NoSigChange_OD, Improved_OD, IncAbn_OD, ".
					//"NoSigChange_OS, Improved_OS, IncAbn_OS, ".
					"iopTrgtOd, iopTrgtOs,techComments, ".
					"ptInformedNv,examTime,contiMeds, ".
					"test_res_od, test_res_os, ".
					"encounter_id,ordrby,ordrdt, ".
					//"fovea_thick_OD, fovea_thick_OS, ".
					//"avg_nfl_Thick_OD,avg_nfl_Thick_OS ".
					"normal_OD,normal_OS, ".
					"nf_Thick_OD, nf_Thick_OS, ".
					"quad_devi_OD, quad_devi_OS, ".
					"nf_Indic_OD, nf_Indic_OS ".$signPathFieldName.$signDTmFieldName.
					") ".
					"VALUES ".
					"( ".
					"NULL, '".$scanLaserEye."', '".$performBy."', '".$diagnosis."', '".$ptUndersatnding."', ".
					"'".$reliabilityOd."', '".$reliabilityOs."', '".$descOd."', '".$descOs."', ".
					"'".$stable."', '".$fuApa."', '".$ptInformed."', '".$comments."', '".$signature."', '".$phyName."', '".$examDate."', ".
					//"'".$Normal_OD_T."', '".$Normal_OD_1."', '".$Normal_OD_2."', '".$Normal_OD_3."', '".$Normal_OD_4."', ".
					//"'".$BorderLineDefect_OD_T."', '".$BorderLineDefect_OD_1."', '".$BorderLineDefect_OD_2."', '".$BorderLineDefect_OD_3."', '".$BorderLineDefect_OD_4."', ".
					//"'".$Abnorma_OD_T."', '".$Abnorma_OD_1."', '".$Abnorma_OD_2."', '".$Abnorma_OD_3."', '".$Abnorma_OD_4."', ".
					"'".imw_real_escape_string($Others_OD)."',".
					//"'".$Normal_OS_T."', '".$Normal_OS_1."', '".$Normal_OS_2."', '".$Normal_OS_3."', '".$Normal_OS_4."', ".
					//"'".$BorderLineDefect_OS_T."', '".$BorderLineDefect_OS_1."', '".$BorderLineDefect_OS_2."', '".$BorderLineDefect_OS_3."', '".$BorderLineDefect_OS_4."', ".
					//"'".$Abnorma_OS_T."', '".$Abnorma_OS_1."', '".$Abnorma_OS_2."', '".$Abnorma_OS_3."', '".$Abnorma_OS_4."', ".
					" '".imw_real_escape_string($Others_OS)."',".
					"'".$patient_id."', '".$form_id."', '".$diagnosisOther."', '".$monitorIOP."', '".$tech2InformPt."', ".
					//"'".$Normal_OD_PoorStudy."', '".$Normal_OD_PoorStudy."', ".
					//"'".$NoSigChange_OD."', '".$Improved_OD."', '".$IncAbn_OD."', ".
					//"'".$NoSigChange_OS."', '".$Improved_OS."', '".$IncAbn_OS."', ".
					"'".$elem_trgtIopOd."', '".$elem_trgtIopOs."','".$elem_techComments."', ".
					"'".$ptInformedNv."','".$examTime."','".$contiMeds."', ".
					"'".$test_res_od."', '".$test_res_os."', ".
					"'".$encounter_id."','".$ordrby."','".$ordrdt."', ".
					//"'".$elem_foveaThick_OD."','".$elem_foveaThick_OS."', ".
					//"'".$avg_nfl_Thick_OD."','".$avg_nfl_Thick_OS."' ".
					"'".imw_real_escape_string($normal_OD)."', '".imw_real_escape_string($normal_OS)."', ".
					"'".imw_real_escape_string($nf_Thick_OD)."', '".imw_real_escape_string($nf_Thick_OS)."', ".
					"'".imw_real_escape_string($quad_devi_OD)."', '".imw_real_escape_string($quad_devi_OS)."', ".
					"'".imw_real_escape_string($nf_Indic_OD)."', '".imw_real_escape_string($nf_Indic_OS)."' ".$signPathFieldValue.$signDTmFieldValue.
					") ";
			$res_insert = imw_query($sql);
			$InsertId	= imw_insert_id();
			$gdx_id = $InsertId;
		}else if(!empty($gdx_id)){
			$sql = "UPDATE test_gdx ".
					"SET ".
					//"scanLaserOct = '".$scanLaserOct."', ".
					"scanLaserEye = '".$scanLaserEye."', ".
					"performBy = '".$performBy."', ".
					"diagnosis = '".$diagnosis."', ".
					"ptUndersatnding = '".$ptUndersatnding."', ".
					"reliabilityOd = '".$reliabilityOd."', ".
					"reliabilityOs = '".$reliabilityOs."', ".
					//"scanLaserOd = '".$scanLaserOd."', ".
					//"scanLaserOs = '".$scanLaserOs."', ".
					"descOd = '".$descOd."', ".
					//"Normal_OD_T = '".$Normal_OD_T."', ".
					//"Normal_OD_1 = '".$Normal_OD_1."', ".
					//"Normal_OD_2 = '".$Normal_OD_2."', ".
					//"Normal_OD_3 = '".$Normal_OD_3."', ".
					//"Normal_OD_4 = '".$Normal_OD_4."', ".
					//"BorderLineDefect_OD_T = '".$BorderLineDefect_OD_T."', ".
					//"BorderLineDefect_OD_1 = '".$BorderLineDefect_OD_1."', ".
					//"BorderLineDefect_OD_2 = '".$BorderLineDefect_OD_2."', ".
					//"BorderLineDefect_OD_3 = '".$BorderLineDefect_OD_3."', ".
					//"BorderLineDefect_OD_4 = '".$BorderLineDefect_OD_4."', ".
					//"Abnorma_OD_T = '".$Abnorma_OD_T."', ".
					//"Abnorma_OD_1 = '".$Abnorma_OD_1."', ".
					//"Abnorma_OD_2 = '".$Abnorma_OD_2."', ".
					//"Abnorma_OD_3 = '".$Abnorma_OD_3."', ".
					//"Abnorma_OD_4 = '".$Abnorma_OD_4."', ".
					"Others_OD = '".imw_real_escape_string($Others_OD)."', ".
					//"Normal_OS_T = '".$Normal_OS_T."', ".
					//"Normal_OS_1 = '".$Normal_OS_1."', ".
					//"Normal_OS_2 = '".$Normal_OS_2."', ".
					//"Normal_OS_3 = '".$Normal_OS_3."', ".
					//"Normal_OS_4 = '".$Normal_OS_4."', ".
					//"BorderLineDefect_OS_T = '".$BorderLineDefect_OS_T."', ".
					//"BorderLineDefect_OS_1 = '".$BorderLineDefect_OS_1."', ".
					//"BorderLineDefect_OS_2 = '".$BorderLineDefect_OS_2."', ".
					//"BorderLineDefect_OS_3 = '".$BorderLineDefect_OS_3."', ".
					//"BorderLineDefect_OS_4 = '".$BorderLineDefect_OS_4."', ".
					//"Abnorma_OS_T = '".$Abnorma_OS_T."', ".
					//"Abnorma_OS_1 = '".$Abnorma_OS_1."', ".
					//"Abnorma_OS_2 = '".$Abnorma_OS_2."', ".
					//"Abnorma_OS_3 = '".$Abnorma_OS_3."', ".
					//"Abnorma_OS_4 = '".$Abnorma_OS_4."', ".
					"Others_OS = '".imw_real_escape_string($Others_OS)."', ".
					"descOs = '".$descOs."', ".
					"stable = '".$stable."', ".
					"fuApa = '".$fuApa."', ".
					"ptInformed = '".$ptInformed."', ".
					"comments = '".$comments."', ".
					"signature = '".$signature."', ".
					"phyName = '".$phyName."', ".
					"examDate = '".$examDate."', ".
					"monitorIOP = '".$monitorIOP."', ".
					//"patient_id = '".$patient_id."', ".
					"tech2InformPt = '".$tech2InformPt."', ".
					"diagnosisOther = '".$diagnosisOther."', ".
					//"Normal_OD_PoorStudy = '".$Normal_OD_PoorStudy."', ".
					//"Normal_OS_PoorStudy = '".$Normal_OS_PoorStudy."', ".
					//"NoSigChange_OD='".$NoSigChange_OD."', ".
					//"Improved_OD='".$Improved_OD."', ".
					//"IncAbn_OD='".$IncAbn_OD."', ".
					//"NoSigChange_OS='".$NoSigChange_OS."', ".
					//"Improved_OS='".$Improved_OS."', ".
					//"IncAbn_OS='".$IncAbn_OS."', ".
					"iopTrgtOd='".$elem_trgtIopOd."', ".
					"iopTrgtOs='".$elem_trgtIopOs."', ".
					"techComments='".$elem_techComments."', ".
					"ptInformedNv='".$ptInformedNv."', ".
					"examTime ='".$examTime."', ".
					"contiMeds = '".$contiMeds."', ".
					"test_res_od = '".$test_res_od."', ".
					"test_res_os = '".$test_res_os."', ".
					"encounter_id='".$encounter_id."', ".
					"ordrby='".$ordrby."',ordrdt='".$ordrdt."', ".
					//"fovea_thick_OD='".$elem_foveaThick_OD."', ".
					//"fovea_thick_OS='".$elem_foveaThick_OS."', ".
					//"avg_nfl_Thick_OD='".$avg_nfl_Thick_OD."', ".
					//"avg_nfl_Thick_OS='".$avg_nfl_Thick_OS."' ".
					//"WHERE form_id = '".$form_id."' ";
					"normal_OD='".imw_real_escape_string($normal_OD)."', ".
					"nf_Thick_OD='".imw_real_escape_string($nf_Thick_OD)."', ".
					"quad_devi_OD='".imw_real_escape_string($quad_devi_OD)."', ".
					"nf_Indic_OD='".imw_real_escape_string($nf_Indic_OD)."', ".					
					"normal_OS='".imw_real_escape_string($normal_OS)."', ".
					"nf_Thick_OS='".imw_real_escape_string($nf_Thick_OS)."', ".
					"quad_devi_OS='".imw_real_escape_string($quad_devi_OS)."', ".
					 $signPathQry.
					 $signDTmQry.
					"nf_Indic_OS='".imw_real_escape_string($nf_Indic_OS)."' ".
					"WHERE gdx_id = '".$gdx_id."' ";
			$res = sqlQuery($sql);
		}
		
		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "GDX", $gdx_id);
		/*--interpretation end--*/
		
		//Super-Bill -----------
		if(!empty($ordrby) && !empty($gdx_id)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$gdx_id;
			$arIn["sb_testName"]="GDX";
                        $arIn["form_id"]=$formId;
                        $arIn["test_interpreted"]= ((int)$phyName > 0)? true : false;

			$oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);
		}
		//Super-Bill -----------

		//save tests Pdf
		$objTests->saveTestPdfExe_2($patient_id,$gdx_id,"GDX");

		//FormId updates other tables -----
		if(!empty($form_id)){
			// Make chart notes valid
			$oChartNote->makeChartNotesValid($form_id);
		}
		//FormId updates other tables -----
		echo "<script>
				try{";
				if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
					$afterSaveinPopup = 'yes';
				}else{
					$afterSaveinPopup = 'no';
					echo "top.alert_notification_show(\"Test has been saved.\");";
				}
				echo "
					window.location.replace(\"test_gdx.php?noP=".$elem_noP."&tId=".$gdx_id."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
				}catch(err){}
			  </script>";
		exit();		
		break;	
	}
	case "NFA":{
		$patient_id = $_POST["elem_patientId"];
		$form_id = $_POST["elem_formId"];
		$hd_nfa_mode = $_POST["hd_nfa_mode"];
		$nfa_id =  $_POST["elem_nfaId"];
		$examDate =  getDateFormatDB($_POST["elem_examDate"]);
		if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
		$scanLaserNfa =  $_POST["elem_scanLaserNfa"];
		$scanLaserEye =  $_POST["elem_scanLaserEye"];
		$performBy =  $_POST["elem_performedBy"];
		$ptUndersatnding =  $_POST["elem_ptUndersatnding"];
		$diagnosis =  imw_real_escape_string($_POST["elem_diagnosis"]);
		$reliabilityOd =  $_POST["elem_reliabilityOd"];
		$reliabilityOs =  $_POST["elem_reliabilityOs"];
		$scanLaserOd =  $_POST["elem_scanLaserOd"];
		$descOd =  $_POST["elem_descOd"];
		$scanLaserOs =  $_POST["elem_scanLaserOs"];
		$descOs =  $_POST["elem_descOs"];
		$stable =  $_POST["elem_stable"];
		$monitorIOP =  $_POST["elem_monitorIOP"];
		$fuApa =  $_POST["elem_fuApa"];
		$ptInformed =  $_POST["elem_ptInformed"];
		$comments =  imw_real_escape_string($_POST["elem_comments"]);
		$signature =  $_POST["elem_vfSign"];
		$phyName =  $_POST["elem_physician"];
		$diagnosisOther =  imw_real_escape_string($_POST["elem_diagnosisOther"]);
		$tech2InformPt = $_POST["elem_tech2InformPt"];
		$Normal_OD_PoorStudy = $_POST["elem_normal_poorStudy_od"];
		$Normal_OS_PoorStudy = $_POST["elem_normal_poorStudy_os"];
		$elem_trgtIopOd = imw_real_escape_string($_POST["elem_targetIop_OD"]);
		$elem_trgtIopOs = imw_real_escape_string($_POST["elem_targetIop_OS"]);
		$ptInformedNv = $_POST["elem_informedPtNv"];
		$elem_noP = $_POST["elem_noP"];
		$examTime = $_POST["elem_examTime"];
		$contiMeds = $_POST["elem_contiMeds"];
		$rptTst1yr = $_POST["elem_rptTst1yr"];

		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);

		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
			$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
			$encounter_id = $objTests->getEncounterId();
		}

		//Decrease
		$decreaseOd = $decreaseOs = "";
		$arrdecrease = array("RA","RV","HVC",
							 "CSM","NFL","MC");
		foreach($arrdecrease as $key => $val){
			//Od
			if(isset($_POST["decreased_OD_".$val]) && !empty($_POST["decreased_OD_".$val])){
				$decreaseOd .= (empty($decreaseOd)) ? "" : ",";
				$decreaseOd .= $_POST["decreased_OD_".$val];
			}
			//Os
			if(isset($_POST["decreased_OS_".$val]) && !empty($_POST["decreased_OS_".$val])){
				$decreaseOs .= (empty($decreaseOs)) ? "" : ",";
				$decreaseOs .= $_POST["decreased_OS_".$val];
			}
		}
		//Decrease

		//Thin
		$thinOd = $thinOs = "";
		$arrthin = array("Temp","ST","SN","N",
						 "IN","IT");
		foreach($arrthin as $key => $val){
			//Od
			if(isset($_POST["thin_OD_".$val]) && !empty($_POST["thin_OD_".$val])){
				$thinOd .= (empty($thinOd)) ? "" : ",";
				$thinOd .= $_POST["thin_OD_".$val];
			}
			//Os
			if(isset($_POST["thin_OS_".$val]) && !empty($_POST["thin_OS_".$val])){
				$thinOs .= (empty($thinOs)) ? "" : ",";
				$thinOs .= $_POST["thin_OS_".$val];
			}
		}

		//Total Thin
		$totalThinOd = imw_real_escape_string($_POST["total_thin_OD"]);
		$totalThinOs = imw_real_escape_string($_POST["total_thin_OS"]);


		//Tech Comments
		if($_POST["techComments"] == 'Technician Comments'){
			$_POST["techComments"] = '';
		}
		$elem_techComments = imw_real_escape_string($_POST["techComments"]);

		//summary
		$summaryOd = $summaryOs = "";
		//Od
		$arr = array(""=>$Normal_OD_T, "Poor Study"=>$Normal_OD_PoorStudy);
		$summaryOd .= $objTests->getTestSumm($arr,"Normal");

		$arr = array("T"=>$BorderLineDefect_OD_T,"+1"=>$BorderLineDefect_OD_1,"+2"=>$BorderLineDefect_OD_2,
					"+3"=>$BorderLineDefect_OD_3,"+4"=>$BorderLineDefect_OD_4);
		$summaryOd .= $objTests->getTestSumm($arr,"Border Line Defect");

		$arr = array("T"=>$Abnorma_OD_T,"+1"=>$Abnorma_OD_1,"+2"=>$Abnorma_OD_2,
					"+3"=>$Abnorma_OD_3,"+4"=>$Abnorma_OD_4);
		$summaryOd .= $objTests->getTestSumm($arr,"Abnormal");

		$arr = array("RA"=>$decreased_OD_RA,"RV"=>$decreased_OD_RV,"HVC"=>$decreased_OD_HVC,
						"CSM"=>$decreased_OD_CSM,"NFL"=>$decreased_OD_NFL,"MC Intact RIM X 360"=>$decreased_OD_MC);
		$summaryOd .= $objTests->getTestSumm($arr,"Decreased");

		$arr = array("Temp"=>$thin_OD_Temp,"ST"=>$thin_OD_ST,"SN"=>$thin_OD_SN,
						"N"=>$thin_OD_N,"IN"=>$thin_OD_IN,"IT"=>$thin_OD_IT);
		$summaryOd .= $objTests->getTestSumm($arr,"Thin");

		$arr = array($total_thin_OD => $total_thin_OD);
		$summaryOd .= $objTests->getTestSumm($arr, "Total Thin");


		$NoSigChange_OD = $_POST["elem_noSigChange_OD"];
		$arr = array("" => $NoSigChange_OD);
		$summaryOd .= $objTests->getTestSumm($arr, "No Significant Change");

		$Improved_OD = $_POST["elem_improved_OD"];
		$arr = array("" => $Improved_OD);
		$summaryOd .= $objTests->getTestSumm($arr, "Improved");

		$IncAbn_OD = $_POST["elem_incAbn_OD"];
		$arr = array("" => $IncAbn_OD);
		$summaryOd .= $objTests->getTestSumm($arr, "Increase Abnormal");


		$arr = array($Others_OD=>$Others_OD);
		$summaryOd .= $objTests->getTestSumm($arr,"Other");
		//Os
		$arr = array(""=>$Normal_OS_T, "Poor Study"=>$Normal_OS_PoorStudy);
		$summaryOs .= $objTests->getTestSumm($arr,"Normal");

		$arr = array("T"=>$BorderLineDefect_OS_T,"+1"=>$BorderLineDefect_OS_1,"+2"=>$BorderLineDefect_OS_2,
					"+3"=>$BorderLineDefect_OS_3,"+4"=>$BorderLineDefect_OS_4);
		$summaryOs .= $objTests->getTestSumm($arr,"Border Line Defect");

		$arr = array("T"=>$Abnorma_OS_T,"+1"=>$Abnorma_OS_1,"+2"=>$Abnorma_OS_2,
					"+3"=>$Abnorma_OS_3,"+4"=>$Abnorma_OS_4);
		$summaryOs .= $objTests->getTestSumm($arr,"Abnormal");

		$arr = array("RA"=>$decreased_OS_RA,"RV"=>$decreased_OS_RV,"HVC"=>$decreased_OS_HVC,
						"CSM"=>$decreased_OS_CSM,"NFL"=>$decreased_OS_NFL,"MC Intact RIM X 360"=>$decreased_OS_MC);
		$summaryOs .= $objTests->getTestSumm($arr,"Decreased");

		$arr = array("Temp"=>$thin_OS_Temp,"ST"=>$thin_OS_ST,"SN"=>$thin_OS_SN,
						"N"=>$thin_OS_N,"IN"=>$thin_OS_IN,"IT"=>$thin_OS_IT);
		$summaryOs .= $objTests->getTestSumm($arr,"Thin");

		$arr = array($total_thin_OS => $total_thin_OS);
		$summaryOs .= $objTests->getTestSumm($arr, "Total Thin");

		$NoSigChange_OS = $_POST["elem_noSigChange_OS"];
		$arr = array("" => $NoSigChange_OS);
		$summaryOs .= $objTests->getTestSumm($arr, "No Significant Change");

		$Improved_OS = $_POST["elem_improved_OS"];
		$arr = array("" => $Improved_OS);
		$summaryOs .= $objTests->getTestSumm($arr, "Improved");

		$IncAbn_OS = $_POST["elem_incAbn_OS"];
		$arr = array("" => $IncAbn_OS);
		$summaryOs .= $objTests->getTestSumm($arr, "Increase Abnormal");

		$arr = array($Others_OS=>$Others_OS);
		$summaryOs .= $objTests->getTestSumm($arr,"Other");

		$descOd = (!empty($summaryOd)) ? imw_real_escape_string("".$summaryOd) : "";
		$descOs = (!empty($summaryOs)) ? imw_real_escape_string("".$summaryOs) : "";

		//echo "Desc:<br>".$descOd;
		//echo "Desc:<br>".$descOs;

		//summary

		//check
		if(empty($nfa_id)){
			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;
			if(isset($arrTest2edit["NFA"]) && !empty($arrTest2edit["NFA"])){

				//Check in db
				$cQry = "select nfa_id FROM nfa
						WHERE patient_id ='".$patient_id."'
						AND nfa_id = '".$arrTest2edit["NFA"]."' ";
				$row = sqlQuery($cQry);
				$nfa_id = (($row == false) || empty($row["nfa_id"])) ? "0" : $row["nfa_id"];

				//Unset Session NFA
				$arrTest2edit["NFA"] = "";
				$arrTest2edit["NFA"] = NULL;
				unset($arrTest2edit["NFA"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);

			}else{

				$cQry = "SELECT nfa_id FROM nfa WHERE patient_id='".$patient_id."'
						 AND examDate = '".$examDate."' AND examTime = '".$examTime."' ";
				$row = sqlQuery($cQry);
				$nfa_id = (($row == false) || empty($row["nfa_id"])) ? "0" : $row["nfa_id"];

			}
		}


		if(empty($nfa_id)){
			$sql = "INSERT INTO nfa ".
					"( ".
					"nfa_id, scanLaserNfa, scanLaserEye, performBy, diagnosis, ptUndersatnding, ".
					"reliabilityOd, reliabilityOs, scanLaserOd, scanLaserOs, descOd, descOs, ".
					"stable, fuApa, ptInformed, comments, signature, phyName, examDate, ".
					"Normal_OD_T, Normal_OD_1, Normal_OD_2, Normal_OD_3, Normal_OD_4, ".
					"BorderLineDefect_OD_T, BorderLineDefect_OD_1, BorderLineDefect_OD_2, BorderLineDefect_OD_3, BorderLineDefect_OD_4, ".
					"Abnorma_OD_T, Abnorma_OD_1, Abnorma_OD_2, Abnorma_OD_3, Abnorma_OD_4, Others_OD, ".
					"Normal_OS_T, Normal_OS_1, Normal_OS_2, Normal_OS_3, Normal_OS_4, ".
					"BorderLineDefect_OS_T, BorderLineDefect_OS_1, BorderLineDefect_OS_2, BorderLineDefect_OS_3, BorderLineDefect_OS_4, ".
					"Abnorma_OS_T, Abnorma_OS_1, Abnorma_OS_2, Abnorma_OS_3, Abnorma_OS_4, Others_OS, ".
					"patient_id, form_id, diagnosisOther, monitorIOP,tech2InformPt, ".
					"Normal_OD_PoorStudy, Normal_OS_PoorStudy, ".
					"NoSigChange_OD, Improved_OD, IncAbn_OD, ".
					"NoSigChange_OS, Improved_OS, IncAbn_OS, ".
					"iopTrgtOd, iopTrgtOs, techComments, ".
					"ptInformedNv,examTime,contiMeds, ".
					"decreaseOd,decreaseOs,thinOd,thinOs,".
					"totalThinOd,totalThinOs, rptTst1yr, ".
					"encounter_id,ordrby,ordrdt ".$signPathFieldName.$signDTmFieldName.
					") ".
					"VALUES ".
					"( ".
					"NULL, '".$scanLaserNfa."', '".$scanLaserEye."', '".$performBy."', '".$diagnosis."', '".$ptUndersatnding."', ".
					"'".$reliabilityOd."', '".$reliabilityOs."', '".$scanLaserOd."', '".$scanLaserOs."', '".$descOd."', '".$descOs."', ".
					"'".$stable."', '".$fuApa."', '".$ptInformed."', '".$comments."', '".$signature."', '".$phyName."', '".$examDate."', ".
					"'".$Normal_OD_T."', '".$Normal_OD_1."', '".$Normal_OD_2."', '".$Normal_OD_3."', '".$Normal_OD_4."', ".
					"'".$BorderLineDefect_OD_T."', '".$BorderLineDefect_OD_1."', '".$BorderLineDefect_OD_2."', '".$BorderLineDefect_OD_3."', '".$BorderLineDefect_OD_4."', ".
					"'".$Abnorma_OD_T."', '".$Abnorma_OD_1."', '".$Abnorma_OD_2."', '".$Abnorma_OD_3."', '".$Abnorma_OD_4."', '".imw_real_escape_string($Others_OD)."',".
					"'".$Normal_OS_T."', '".$Normal_OS_1."', '".$Normal_OS_2."', '".$Normal_OS_3."', '".$Normal_OS_4."', ".
					"'".$BorderLineDefect_OS_T."', '".$BorderLineDefect_OS_1."', '".$BorderLineDefect_OS_2."', '".$BorderLineDefect_OS_3."', '".$BorderLineDefect_OS_4."', ".
					"'".$Abnorma_OS_T."', '".$Abnorma_OS_1."', '".$Abnorma_OS_2."', '".$Abnorma_OS_3."', '".$Abnorma_OS_4."', '".imw_real_escape_string($Others_OS)."',".
					"'".$patient_id."', '".$form_id."', '".$diagnosisOther."', '".$monitorIOP."', '".$tech2InformPt."', ".
					"'".$Normal_OD_PoorStudy."', '".$Normal_OD_PoorStudy."', ".
					"'".$NoSigChange_OD."', '".$Improved_OD."', '".$IncAbn_OD."', ".
					"'".$NoSigChange_OS."', '".$Improved_OS."', '".$IncAbn_OS."', ".
					"'".$elem_trgtIopOd."', '".$elem_trgtIopOs."', '".$elem_techComments."', ".
					"'".$ptInformedNv."','".$examTime."','".$contiMeds."', ".
					"'".$decreaseOd."','".$decreaseOs."','".$thinOd."','".$thinOs."',".
					"'".$totalThinOd."','".$totalThinOs."','".$rptTst1yr."', ".
					"'".$encounter_id."','".$ordrby."','".$ordrdt."' ".$signPathFieldValue.$signDTmFieldValue.
					") ";

			$result_insert = imw_query($sql);
			$InsertId = imw_insert_id();
			$nfa_id = $InsertId;
		}else if(!empty($nfa_id)){
			$sql = "UPDATE nfa ".
					"SET ".
					"scanLaserNfa = '".$scanLaserNfa."', ".
					"scanLaserEye = '".$scanLaserEye."', ".
					"performBy = '".$performBy."', ".
					"diagnosis = '".$diagnosis."', ".
					"ptUndersatnding = '".$ptUndersatnding."', ".
					"reliabilityOd = '".$reliabilityOd."', ".
					"reliabilityOs = '".$reliabilityOs."', ".
					"scanLaserOd = '".$scanLaserOd."', ".
					"scanLaserOs = '".$scanLaserOs."', ".
					"descOd = '".$descOd."', ".
					"Normal_OD_T = '".$Normal_OD_T."', ".
					"Normal_OD_1 = '".$Normal_OD_1."', ".
					"Normal_OD_2 = '".$Normal_OD_2."', ".
					"Normal_OD_3 = '".$Normal_OD_3."', ".
					"Normal_OD_4 = '".$Normal_OD_4."', ".
					"BorderLineDefect_OD_T = '".$BorderLineDefect_OD_T."', ".
					"BorderLineDefect_OD_1 = '".$BorderLineDefect_OD_1."', ".
					"BorderLineDefect_OD_2 = '".$BorderLineDefect_OD_2."', ".
					"BorderLineDefect_OD_3 = '".$BorderLineDefect_OD_3."', ".
					"BorderLineDefect_OD_4 = '".$BorderLineDefect_OD_4."', ".
					"Abnorma_OD_T = '".$Abnorma_OD_T."', ".
					"Abnorma_OD_1 = '".$Abnorma_OD_1."', ".
					"Abnorma_OD_2 = '".$Abnorma_OD_2."', ".
					"Abnorma_OD_3 = '".$Abnorma_OD_3."', ".
					"Abnorma_OD_4 = '".$Abnorma_OD_4."', ".
					"Others_OD = '".imw_real_escape_string($Others_OD)."', ".
					"Normal_OS_T = '".$Normal_OS_T."', ".
					"Normal_OS_1 = '".$Normal_OS_1."', ".
					"Normal_OS_2 = '".$Normal_OS_2."', ".
					"Normal_OS_3 = '".$Normal_OS_3."', ".
					"Normal_OS_4 = '".$Normal_OS_4."', ".
					"BorderLineDefect_OS_T = '".$BorderLineDefect_OS_T."', ".
					"BorderLineDefect_OS_1 = '".$BorderLineDefect_OS_1."', ".
					"BorderLineDefect_OS_2 = '".$BorderLineDefect_OS_2."', ".
					"BorderLineDefect_OS_3 = '".$BorderLineDefect_OS_3."', ".
					"BorderLineDefect_OS_4 = '".$BorderLineDefect_OS_4."', ".
					"Abnorma_OS_T = '".$Abnorma_OS_T."', ".
					"Abnorma_OS_1 = '".$Abnorma_OS_1."', ".
					"Abnorma_OS_2 = '".$Abnorma_OS_2."', ".
					"Abnorma_OS_3 = '".$Abnorma_OS_3."', ".
					"Abnorma_OS_4 = '".$Abnorma_OS_4."', ".
					"Others_OS = '".imw_real_escape_string($Others_OS)."', ".
					"descOs = '".$descOs."', ".
					"stable = '".$stable."', ".
					"fuApa = '".$fuApa."', ".
					"ptInformed = '".$ptInformed."', ".
					"comments = '".$comments."', ".
					"signature = '".$signature."', ".
					"phyName = '".$phyName."', ".
					"examDate = '".$examDate."', ".
					"monitorIOP = '".$monitorIOP."', ".
					//"patient_id = '".$patient_id."', ".
					"tech2InformPt = '".$tech2InformPt."', ".
					"diagnosisOther = '".$diagnosisOther."', ".
					"Normal_OD_PoorStudy = '".$Normal_OD_PoorStudy."', ".
					"Normal_OS_PoorStudy = '".$Normal_OS_PoorStudy."', ".
					"NoSigChange_OD='".$NoSigChange_OD."', ".
					"Improved_OD='".$Improved_OD."', ".
					"IncAbn_OD='".$IncAbn_OD."', ".
					"NoSigChange_OS='".$NoSigChange_OS."', ".
					"Improved_OS='".$Improved_OS."', ".
					"IncAbn_OS='".$IncAbn_OS."', ".
					"iopTrgtOd='".$elem_trgtIopOd."', ".
					"iopTrgtOs='".$elem_trgtIopOs."', ".
					"techComments='".$elem_techComments."', ".
					"ptInformedNv='".$ptInformedNv."', ".
					"examTime='".$examTime."', ".
					"contiMeds = '".$contiMeds."', ".
					"decreaseOd = '".$decreaseOd."', ".
					"decreaseOs = '".$decreaseOs."', ".
					"thinOd = '".$thinOd."', ".
					"thinOs = '".$thinOs."', ".
					"totalThinOd = '".$totalThinOd."',".
					"totalThinOs = '".$totalThinOs."', ".
					"rptTst1yr = '".$rptTst1yr."', ".
					"encounter_id='".$encounter_id."', ".
					$signPathQry.
					$signDTmQry.
					"ordrby='".$ordrby."',ordrdt='".$ordrdt."' ".
					//"WHERE form_id = '".$form_id."' ";
					"WHERE nfa_id = '".$nfa_id."' ";
			$res = sqlQuery($sql);
		}
		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "NFA", $nfa_id);
		/*--interpretation end--*/
		
		//$cls_notifications->set_notification_status('testseye');
		//Super-Bill -----------
		if(!empty($ordrby) && !empty($nfa_id)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$nfa_id;
			$arIn["sb_testName"]="HRT";
                        $arIn["form_id"]=$formId;
                        $arIn["test_interpreted"]= ((int)$phyName > 0)? true : false;

			$oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);
		}
		//Super-Bill -----------

		//save tests Pdf
		$objTests->saveTestPdfExe_2($patient_id,$nfa_id,"HRT");

		//FormId updates other tables -----
		if(!empty($form_id)){
			// Make chart notes valid
			$oChartNote->makeChartNotesValid($form_id);

			//Syncronise with iop trgts
			$funIopSum="";
			$curformId = $objTests->isChartOpened($patient_id);
			if($curformId == false){$curformId = 0;}
			if(!empty($elem_trgtIopOd) || !empty($elem_trgtIopOs)){
				
				if($curformId != false){
					//check
					$cQry = "select sumOdIop, sumOsIop FROM chart_iop 
								WHERE form_id='".$curformId."' AND patient_id='".$patient_id."' AND purged='0' ";
					$row = sqlQuery($cQry);

					if($row == false){	// Insert
						//get Last Id
						/*$lastId = 0;
						$res = valNewRecordIop($patientId,"chart_iop.iop_id");
						for($i=0;$row=sqlFetchArray($res);$i++){
							$lastId = $row["iop_id"];
						}
						//
						//
						echo $sql = "INSERT INTO chart_iop (trgtOd,trgtOs, form_id, patient_id) VALUES ('".$elem_trgtIopOd."', '".$elem_trgtIopOs."', '".$curformId."','".$elem_patientId."') ";
						//$insertId = sqlInsert($sql);
						//
						//$test = copyLastIop($insertId,$lastId);*/
					}else{
						$sumOdIop = $row["sumOdIop"];
						$sumOsIop = $row["sumOsIop"];

						$sumOdIop = preg_replace("/Trgt:\s*\d*/", "Trgt:".$elem_trgtIopOd,$sumOdIop);
						$sumOsIop = preg_replace("/Trgt:\s*\d*/", "Trgt:".$elem_trgtIopOs,$sumOsIop);

						//Update
						$sql = "UPDATE chart_iop SET trgtOd='".$elem_trgtIopOd."', trgtOs='".$elem_trgtIopOs."', 
								sumOdIop='".imw_real_escape_string($sumOdIop)."', 
								sumOsIop='".imw_real_escape_string($sumOsIop)."' 
								WHERE form_id='".$curformId."' AND patient_id='".$patient_id."' AND purged='0' ";
						$res = sqlQuery($sql);
					}

					//Update VF
					$sql = "UPDATE vf SET iopTrgtOd='".$elem_trgtIopOd."', iopTrgtOs='".$elem_trgtIopOs."' 
							WHERE formId = '".$curformId."' AND patientId='".$patient_id."'  ";
					$res = sqlQuery($sql);

				}else{
					$curformId = 0;
				}
				//Save IOP Def Vals
				$objTests->saveIopTrgt($elem_trgtIopOd,$elem_trgtIopOs,$patient_id,$curformId);
			}else{//if empty targt values
				$objTests->remIopTrgtDefVal($elem_trgtIopOd,$elem_trgtIopOs,$patient_id,$curformId);
			}
			//End Syncronise with iop trgts
		}
		echo "<script>".
				"try{";
				if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
					$afterSaveinPopup = 'yes';
				}else{
					$afterSaveinPopup = 'no';
					echo "top.alert_notification_show(\"Test has been saved.\");";
				}
				echo "
					window.location.replace(\"test_nfa.php?noP=".$elem_noP."&tId=".$nfa_id."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
				}catch(err){}".
			 "</script>";
		exit();
		break;
	}
	case "ICG":{
		$patient_id = $_POST["elem_patientId"];
		$form_id = $_POST["elem_formId"];
		$hd_icg_mode = $_POST["hd_icg_mode"];
		$icg_id = $_POST["elem_icgId"];
		$exam_date = getDateFormatDB($_POST["elem_examDate"]);
		if($exam_date=='' || $exam_date=='0000-00-00') $exam_date = date('Y-m-d');
		$icg = $_POST["elem_icg"];
		$icg_od = $_POST["elem_icg_od"];
		$icg_early = $_POST["elem_icg_early"];
		$icg_extra = $_POST["elem_icg_extra"];
		$comments_icg = imw_real_escape_string($_POST["elem_comments_icg"]);
		$performed_by = $_POST["elem_performedBy"];
		$pa_under = $_POST["elem_pa_under"];
		$pa_inter = $_POST["elem_pa_inter"];
		$pa_inter1 = $_POST["elem_pa_inter1"];
		$disc_od = $_POST["elem_disc_od"];
		$disc_cd_od = $_POST["elem_disc_cd_od"];
		$retina_od = $_POST["elem_retina_od"];
		$macula_od = $_POST["elem_macula_od"];
		$testresults_desc_od = imw_real_escape_string($_POST["elem_testresults_desc_od"]);
		$disc_os = $_POST["elem_disc_os"];
		$disc_cd_os = $_POST["elem_disc_cd_os"];
		$retina_os = $_POST["elem_retina_os"];
		$macula_os = $_POST["elem_macula_os"];
		$testresults_desc_os = imw_real_escape_string($_POST["elem_testresults_desc_os"]);
		$Stable = $_POST["elem_Stable"];
		$MonitorAg = $_POST["elem_MonitorAg"];
		$FuApa = $_POST["elem_FuApa"];
		$PatientInformed = $_POST["elem_PatientInformed"];
		$ArgonLaser = $_POST["elem_ArgonLaser"];
		$ArgonLaserEye = $_POST["elem_ArgonLaserEye"];
		$ArgonLaserEyeOptions = $_POST["elem_ArgonLaserEyeOptions"];
		$FuRetina = $_POST["elem_FuRetina"];
		$FuRetinaComments = imw_real_escape_string($_POST["elem_FuRetinaComments"]);
		$icg_signature = $_POST["elem_icg_signature"];
		$phy = $_POST["elem_physician"];
		$tech2InformPt = $_POST["elem_tech2InformPt"];
		$ptInformedNv = $_POST["elem_informedPtNv"];
		$elem_noP = $_POST["elem_noP"];
		$examTime = $_POST["elem_examTime"];
		$contiMeds = $_POST["elem_contiMeds"];
		$diagnosis = imw_real_escape_string($_POST["elem_diagnosis"]);
		$diagnosisOther = imw_real_escape_string($_POST["elem_diagnosisOther"]);
		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
		$rptTst1yr = $_POST["elem_rptTst1yr"];
		
		$forum_procedure = (isset($_REQUEST['forum_procedure']))?imw_real_escape_string($_REQUEST['forum_procedure']):"";
		$zeissAction = (isset($_REQUEST['forum_procedure']))?$_POST['zeissAction']:false;

		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
			$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
			$encounter_id = $objTests->getEncounterId();
		}

		//summary
		$disc_od_summary=$disc_os_summary="";
		$retina_od_summary = $retina_os_summary = "";
		$macula_od_summary = $macula_os_summary = "";

		$Sharp_Pink_OD = $_POST["Sharp_Pink_OD"];
		$Pale_OD = $_POST["Pale_OD"];
		$Large_Cap_OD = $_POST["Large_Cap_OD"];
		$Sloping_OD = $_POST["Sloping_OD"];
		$Notch_OD = $_POST["Notch_OD"];
		$NVD_OD = $_POST["NVD_OD"];
		$Leakage_OD = $_POST["Leakage_OD"];
		$arr = array("Sharp &amp; Pink"=>$Sharp_Pink_OD, "Pale"=>$Pale_OD, "Large Cap"=>$Large_Cap_OD,"Sloping"=>$Sloping_OD,
				"Notch"=>$Notch_OD,"NVD"=>$NVD_OD,"Leakage"=>$Leakage_OD);
		$disc_od_summary = $objTests->getTestSumm($arr,"");

		$Retina_Hemorrhage_OD = $_POST["Retina_Hemorrhage_OD"];
		$Retina_Microaneurysms_OD = $_POST["Retina_Microaneurysms_OD"];
		$Retina_Exudates_OD = $_POST["Retina_Exudates_OD"];
		$Retina_Laser_Scars_OD = $_POST["Retina_Laser_Scars_OD"];
		$Retina_NEVI_OD = $_POST["Retina_NEVI_OD"];
		$Retina_SRVNM_OD = $_POST["Retina_SRVNM_OD"];
		$Retina_Edema_OD = $_POST["Retina_Edema_OD"];
		$Retina_Nevus_OD = $_POST["Retina_Nevus_OD"];
		
		$Retina_Ischemia_OD = $_POST["Retina_Ischemia_OD"];
		$Retina_BRVO_OD = $_POST["Retina_BRVO_OD"];
		$Retina_CRVO_OD = $_POST["Retina_CRVO_OD"];
		
		
		$Retina_BDR_OD_T = $_POST["Retina_BDR_OD_T"];
		$Retina_BDR_OD_1 = $_POST["Retina_BDR_OD_1"];
		$Retina_BDR_OD_2 = $_POST["Retina_BDR_OD_2"];
		$Retina_BDR_OD_3 = $_POST["Retina_BDR_OD_3"];
		$Retina_BDR_OD_4 = $_POST["Retina_BDR_OD_4"];
		$Retina_Druse_OD_T = $_POST["Retina_Druse_OD_T"];
		$Retina_Druse_OD_1 = $_POST["Retina_Druse_OD_1"];
		$Retina_Druse_OD_2 = $_POST["Retina_Druse_OD_2"];
		$Retina_Druse_OD_3 = $_POST["Retina_Druse_OD_3"];
		$Retina_Druse_OD_4 = $_POST["Retina_Druse_OD_4"];
		$Retina_RPE_Change_OD_T = $_POST["Retina_RPE_Change_OD_T"];
		$Retina_RPE_Change_OD_1 = $_POST["Retina_RPE_Change_OD_1"];
		$Retina_RPE_Change_OD_2 = $_POST["Retina_RPE_Change_OD_2"];
		$Retina_RPE_Change_OD_3 = $_POST["Retina_RPE_Change_OD_T"];
		$Retina_RPE_Change_OD_4 = $_POST["Retina_RPE_Change_OD_4"];

		$arr = array("Hemorrhage"=>$Retina_Hemorrhage_OD, "Microaneurysms"=>$Retina_Microaneurysms_OD,
					"Exudates"=>$Retina_Exudates_OD, "Laser Scars"=>$Retina_Laser_Scars_OD,
					"NVE"=>$Retina_NEVI_OD, "SRVNM"=>$Retina_SRVNM_OD, "Edema"=>$Retina_Edema_OD);
		$retina_od_summary .= $objTests->getTestSumm($arr,"");

		$arr = array("T"=>$Retina_BDR_OD_T,"+1"=>$Retina_BDR_OD_1,"+2"=>$Retina_BDR_OD_2,
					"+3"=>$Retina_BDR_OD_3,"+4"=>$Retina_BDR_OD_4);
		$retina_od_summary .= $objTests->getTestSumm($arr,"BDR");

		$arr = array("T"=>$Retina_Druse_OD_T,"+1"=>$Retina_Druse_OD_1,"+2"=>$Retina_Druse_OD_2,
					"+3"=>$Retina_Druse_OD_3,"+4"=>$Retina_Druse_OD_4);
		$retina_od_summary .= $objTests->getTestSumm($arr,"Druse");

		$arr = array("T"=>$Retina_RPE_Change_OD_T,"+1"=>$Retina_RPE_Change_OD_1,"+2"=>$Retina_RPE_Change_OD_2,
					"+3"=>$Retina_RPE_Change_OD_3,"+4"=>$Retina_RPE_Change_OD_4);
		$retina_od_summary .= $objTests->getTestSumm($arr,"RPE Change");

		$Druse_OD = $_POST["Druse_OD"];
		$RPE_Changes_OD = $_POST["RPE_Changes_OD"];
		$SRNVM_OD = $_POST["SRNVM_OD"];
		$Edema_OD = $_POST["Edema_OD"];
		$Scars_OD = $_POST["Scars_OD"];
		$Hemorrhage_OD = $_POST["Hemorrhage_OD"];
		$Microaneurysms_OD = $_POST["Microaneurysms_OD"];
		$Exudates_OD = $_POST["Exudates_OD"];
		$Macula_BDR_OD_T = $_POST["Macula_BDR_OD_T"];
		$Macula_BDR_OD_1 = $_POST["Macula_BDR_OD_1"];
		$Macula_BDR_OD_2 = $_POST["Macula_BDR_OD_2"];
		$Macula_BDR_OD_3 = $_POST["Macula_BDR_OD_3"];
		$Macula_BDR_OD_4 = $_POST["Macula_BDR_OD_4"];
		$Macula_SMD_OD_T = $_POST["Macula_SMD_OD_T"];
		$Macula_SMD_OD_1 = $_POST["Macula_SMD_OD_1"];
		$Macula_SMD_OD_2 = $_POST["Macula_SMD_OD_2"];
		$Macula_SMD_OD_3 = $_POST["Macula_SMD_OD_3"];
		$Macula_SMD_OD_4 = $_POST["Macula_SMD_OD_4"];

		$Feeder_Vessel_OD = $_POST["Feeder_Vessel_OD"];
		$Central_OD = $_POST["Central_OD"];
		$Nasal_OD = $_POST["Nasal_OD"];
		$Temporal_OD = $_POST["Temporal_OD"];
		$Inferior_OD = $_POST["Inferior_OD"];
		$Superior_OD = $_POST["Superior_OD"];
		$Hot_Spot_OD = $_POST["Hot_Spot_OD"];
		$Hot_Spot_Val_OD = imw_real_escape_string($_POST["Hot_Spot_Val_OD"]);
		$SR_Heme_OD = $_POST["SR_Heme_OD"];
		$Classic_CNV_OD = $_POST["Classic_CNV_OD"];
		$Occult_CNV_OD = $_POST["Occult_CNV_OD"];

		$arr = array("Druse"=>$Druse_OD,"RPE Changes"=>$RPE_Changes_OD,"SRNVM"=>$SRNVM_OD,"Edema"=>$Edema_OD,
				"Scars"=>$Scars_OD,"Hemorrhage"=>$Hemorrhage_OD,"Microaneurysms"=>$Microaneurysms_OD,"Exudates"=>$Exudates_OD);
		$macula_od_summary .= $objTests->getTestSumm($arr,"");

		$arr = array("T"=>$Macula_BDR_OD_T,"+1"=>$Macula_BDR_OD_1,"+2"=>$Macula_BDR_OD_2,
					"+3"=>$Macula_BDR_OD_3,"+4"=>$Macula_BDR_OD_4);
		$macula_od_summary .= $objTests->getTestSumm($arr,"BDR");

		$arr = array("T"=>$Macula_SMD_OD_T,"+1"=>$Macula_SMD_OD_1,"+2"=>$Macula_SMD_OD_2,
					"+3"=>$Macula_SMD_OD_3,"+4"=>$Macula_SMD_OD_4);
		$macula_od_summary .= $objTests->getTestSumm($arr,"SMD");

		$Sharp_Pink_OS = $_POST["Sharp_Pink_OS"];
		$Pale_OS = $_POST["Pale_OS"];
		$Large_Cap_OS = $_POST["Large_Cap_OS"];
		$Sloping_OS = $_POST["Sloping_OS"];
		$Notch_OS = $_POST["Notch_OS"];
		$NVD_OS = $_POST["NVD_OS"];
		$Leakage_OS = $_POST["Leakage_OS"];

		$arr = array("Sharp &amp; Pink"=>$Sharp_Pink_OS, "Pale"=>$Pale_OS, "Large Cap"=>$Large_Cap_OS,"Sloping"=>$Sloping_OS,
				"Notch"=>$Notch_OS,"NVD"=>$NVD_OS,"Leakage"=>$Leakage_OS);
		$disc_os_summary .= $objTests->getTestSumm($arr,"");

		$Retina_Hemorrhage_OS = $_POST["Retina_Hemorrhage_OS"];
		$Retina_Microaneurysms_OS = $_POST["Retina_Microaneurysms_OS"];
		$Retina_Exudates_OS = $_POST["Retina_Exudates_OS"];
		$Retina_Laser_Scars_OS = $_POST["Retina_Laser_Scars_OS"];
		$Retina_NEVI_OS = $_POST["Retina_NEVI_OS"];
		$Retina_SRVNM_OS = $_POST["Retina_SRVNM_OS"];
		$Retina_Edema_OS = $_POST["Retina_Edema_OS"];
		$Retina_Nevus_OS = $_POST["Retina_Nevus_OS"];
		
		$Retina_Ischemia_OS = $_POST["Retina_Ischemia_OS"];
		$Retina_BRVO_OS = $_POST["Retina_BRVO_OS"];
		$Retina_CRVO_OS = $_POST["Retina_CRVO_OS"];
		
		$Retina_BDR_OS_T = $_POST["Retina_BDR_OS_T"];
		$Retina_BDR_OS_1 = $_POST["Retina_BDR_OS_1"];
		$Retina_BDR_OS_2 = $_POST["Retina_BDR_OS_2"];
		$Retina_BDR_OS_3 = $_POST["Retina_BDR_OS_3"];
		$Retina_BDR_OS_4 = $_POST["Retina_BDR_OS_4"];
		$Retina_Druse_OS_T = $_POST["Retina_Druse_OS_T"];
		$Retina_Druse_OS_1 = $_POST["Retina_Druse_OS_1"];
		$Retina_Druse_OS_2 = $_POST["Retina_Druse_OS_2"];
		$Retina_Druse_OS_3 = $_POST["Retina_Druse_OS_3"];
		$Retina_Druse_OS_4 = $_POST["Retina_Druse_OS_4"];
		$Retina_RPE_Change_OS_T = $_POST["Retina_RPE_Change_OS_T"];
		$Retina_RPE_Change_OS_1 = $_POST["Retina_RPE_Change_OS_1"];
		$Retina_RPE_Change_OS_2 = $_POST["Retina_RPE_Change_OS_2"];
		$Retina_RPE_Change_OS_3 = $_POST["Retina_RPE_Change_OS_T"];
		$Retina_RPE_Change_OS_4 = $_POST["Retina_RPE_Change_OS_4"];		

		$arr = array("Hemorrhage"=>$Retina_Hemorrhage_OS, "Microaneurysms"=>$Retina_Microaneurysms_OS,
					"Exudates"=>$Retina_Exudates_OS, "Laser Scars"=>$Retina_Laser_Scars_OS,
					"NVE"=>$Retina_NEVI_OS, "SRVNM"=>$Retina_SRVNM_OS, "Edema"=>$Retina_Edema_OS);
		$retina_os_summary .= $objTests->getTestSumm($arr,"");

		$arr = array("T"=>$Retina_BDR_OS_T,"+1"=>$Retina_BDR_OS_1,"+2"=>$Retina_BDR_OS_2,
					"+3"=>$Retina_BDR_OS_3,"+4"=>$Retina_BDR_OS_4);
		$retina_os_summary .= $objTests->getTestSumm($arr,"BDR");

		$arr = array("T"=>$Retina_Druse_OS_T,"+1"=>$Retina_Druse_OS_1,"+2"=>$Retina_Druse_OS_2,
					"+3"=>$Retina_Druse_OS_3,"+4"=>$Retina_Druse_OS_4);
		$retina_os_summary .= $objTests->getTestSumm($arr,"Druse");

		$arr = array("T"=>$Retina_RPE_Change_OS_T,"+1"=>$Retina_RPE_Change_OS_1,"+2"=>$Retina_RPE_Change_OS_2,
					"+3"=>$Retina_RPE_Change_OS_3,"+4"=>$Retina_RPE_Change_OS_4);
		$retina_os_summary .= $objTests->getTestSumm($arr,"RPE Change");

		$Druse_OS = $_POST["Druse_OS"];
		$RPE_Changes_OS = $_POST["RPE_Changes_OS"];
		$SRNVM_OS = $_POST["SRNVM_OS"];
		$Edema_OS = $_POST["Edema_OS"];
		$Scars_OS = $_POST["Scars_OS"];
		$Hemorrhage_OS = $_POST["Hemorrhage_OS"];
		$Microaneurysms_OS = $_POST["Microaneurysms_OS"];
		$Exudates_OS = $_POST["Exudates_OS"];
		$Macula_BDR_OS_T = $_POST["Macula_BDR_OS_T"];
		$Macula_BDR_OS_1 = $_POST["Macula_BDR_OS_1"];
		$Macula_BDR_OS_2 = $_POST["Macula_BDR_OS_2"];
		$Macula_BDR_OS_3 = $_POST["Macula_BDR_OS_3"];
		$Macula_BDR_OS_4 = $_POST["Macula_BDR_OS_4"];
		$Macula_SMD_OS_T = $_POST["Macula_SMD_OS_T"];
		$Macula_SMD_OS_1 = $_POST["Macula_SMD_OS_1"];
		$Macula_SMD_OS_2 = $_POST["Macula_SMD_OS_2"];
		$Macula_SMD_OS_3 = $_POST["Macula_SMD_OS_3"];
		$Macula_SMD_OS_4 = $_POST["Macula_SMD_OS_4"];

		$Feeder_Vessel_OS = $_POST["Feeder_Vessel_OS"];
		$Central_OS = $_POST["Central_OS"];
		$Nasal_OS = $_POST["Nasal_OS"];
		$Temporal_OS = $_POST["Temporal_OS"];
		$Inferior_OS = $_POST["Inferior_OS"];
		$Superior_OS = $_POST["Superior_OS"];
		$Hot_Spot_OS = $_POST["Hot_Spot_OS"];
		$Hot_Spot_Val_OS = imw_real_escape_string($_POST["Hot_Spot_Val_OS"]);		

		$PED_OD = $_POST["PED_OD"];
		$PED_OS = $_POST["PED_OS"];
		
		$SR_Heme_OS = $_POST["SR_Heme_OS"];
		$Classic_CNV_OS = $_POST["Classic_CNV_OS"];
		$Occult_CNV_OS = $_POST["Occult_CNV_OS"];
		
		$icgComments = imw_real_escape_string($_POST["icgComments"]);

		$arr = array("Druse"=>$Druse_OS,"RPE Changes"=>$RPE_Changes_OS,"SRNVM"=>$SRNVM_OS,"Edema"=>$Edema_OS,
				"Scars"=>$Scars_OS,"Hemorrhage"=>$Hemorrhage_OS,"Microaneurysms"=>$Microaneurysms_OS,"Exudates"=>$Exudates_OS);
		$macula_os_summary .= $objTests->getTestSumm($arr,"");

		$arr = array("T"=>$Macula_BDR_OS_T,"+1"=>$Macula_BDR_OS_1,"+2"=>$Macula_BDR_OS_2,
					"+3"=>$Macula_BDR_OS_3,"+4"=>$Macula_BDR_OS_4);
		$macula_os_summary .= $objTests->getTestSumm($arr,"BDR");

		$arr = array("T"=>$Macula_SMD_OS_T,"+1"=>$Macula_SMD_OS_1,"+2"=>$Macula_SMD_OS_2,
					"+3"=>$Macula_SMD_OS_3,"+4"=>$Macula_SMD_OS_4);
		$macula_os_summary .= $objTests->getTestSumm($arr,"SMD");

		//check
		if(empty($icg_id)){

			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;

			if(isset($arrTest2edit["ICG"]) && !empty($arrTest2edit["ICG"])){

				//Check in db
				$cQry = "select icg_id FROM icg
						WHERE patient_id ='".$patient_id."'
						AND icg_id = '".$arrTest2edit["ICG"]."' ";
				$row_res = imw_query($cQry);
				$row = imw_fetch_assoc($row_res);
				$icg_id = (($row == false) || empty($row["icg_id"])) ? "0" : $row["icg_id"];

				//Unset Session ICG
				$arrTest2edit["ICG"] = "";
				$arrTest2edit["ICG"] = NULL;
				unset($arrTest2edit["ICG"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);

			}else{

				$cQry = "select icg_id FROM icg WHERE patient_id='".$patient_id."'
						AND exam_date = '".$exam_date."' AND examTime = '".$examTime."' ";
				$row_res = imw_query($cQry);
				$row = imw_fetch_assoc($row_res);
				$icg_id = (($row == false) || empty($row["icg_id"])) ? "0" : $row["icg_id"];

			}
		}

		if(empty($icg_id)){
			$phy=($zeissAction)?0:$phy; /*Mark test uninterpreted if "Forum Add"*/
			$sql = "INSERT INTO icg ".
				 "( ".
				 "icg_id, patient_id, exam_date, icg, icg_od, icg_early, icg_extra, comments_icg, ".
				 "performed_by, pa_under, pa_inter, pa_inter1, disc_od, disc_os, retina_od, retina_os, macula_od, macula_os, ".
				 "icg_signature, form_id, Stable, MonitorAg, FuRetina, FuApa, PatientInformed, ArgonLaser, phy, ".
				 "disc_cd_od, disc_cd_os, testresults_desc_od, testresults_desc_os, ArgonLaserEye, FuRetinaComments, ".
				 "Sharp_Pink_OD, Pale_OD, Large_Cap_OD, Sloping_OD, Notch_OD, NVD_OD, ".
				 "Leakage_OD, Retina_Hemorrhage_OD, Retina_Microaneurysms_OD, Retina_Exudates_OD, Retina_Laser_Scars_OD, Retina_NEVI_OD, Retina_SRVNM_OD, Retina_Edema_OD, Retina_Nevus_OD, ".
				 "Retina_BDR_OD_T, Retina_BDR_OD_1, Retina_BDR_OD_2, Retina_BDR_OD_3, Retina_BDR_OD_4, ".
				 "Retina_Druse_OD_T, Retina_Druse_OD_1, Retina_Druse_OD_2, Retina_Druse_OD_3, Retina_Druse_OD_4, ".
				 "Retina_RPE_Change_OD_T, Retina_RPE_Change_OD_1, Retina_RPE_Change_OD_2, Retina_RPE_Change_OD_3, Retina_RPE_Change_OD_4, ".
		 		 "Druse_OD, RPE_Changes_OD, SRNVM_OD, Edema_OD, Scars_OD, Hemorrhage_OD, Microaneurysms_OD, Exudates_OD, ".
				 "Macula_BDR_OD_T, Macula_BDR_OD_1, Macula_BDR_OD_2, Macula_BDR_OD_3, Macula_BDR_OD_4, ".
				 "Macula_SMD_OD_T, Macula_SMD_OD_1, Macula_SMD_OD_2, Macula_SMD_OD_3, Macula_SMD_OD_4, ".
				 "Feeder_Vessel_OD, Central_OD, Nasal_OD, Temporal_OD, Inferior_OD, Superior_OD, Hot_Spot_OD, Hot_Spot_Val_OD, ".
				 "Sharp_Pink_OS, Pale_OS, Large_Cap_OS, Sloping_OS, Notch_OS, NVD_OS, ".
				 "Leakage_OS, Retina_Hemorrhage_OS, Retina_Microaneurysms_OS, Retina_Exudates_OS, Retina_Laser_Scars_OS, Retina_NEVI_OS, Retina_SRVNM_OS, Retina_Edema_OS, Retina_Nevus_OS, ".
				 "Retina_BDR_OS_T, Retina_BDR_OS_1, Retina_BDR_OS_2, Retina_BDR_OS_3, Retina_BDR_OS_4, ".
				 "Retina_Druse_OS_T, Retina_Druse_OS_1, Retina_Druse_OS_2, Retina_Druse_OS_3, Retina_Druse_OS_4, ".
				 "Retina_RPE_Change_OS_T, Retina_RPE_Change_OS_1, Retina_RPE_Change_OS_2, Retina_RPE_Change_OS_3, Retina_RPE_Change_OS_4, ".
		 		 "Druse_OS, RPE_Changes_OS, SRNVM_OS, Edema_OS, Scars_OS, Hemorrhage_OS, Microaneurysms_OS, Exudates_OS, ".
				 "Macula_BDR_OS_T, Macula_BDR_OS_1, Macula_BDR_OS_2, Macula_BDR_OS_3, Macula_BDR_OS_4, ".
				 "Macula_SMD_OS_T, Macula_SMD_OS_1, Macula_SMD_OS_2, Macula_SMD_OS_3, Macula_SMD_OS_4, ".
				 "Feeder_Vessel_OS, Central_OS, Nasal_OS, Temporal_OS, Inferior_OS, Superior_OS, Hot_Spot_OS, Hot_Spot_Val_OS, ".				 "ArgonLaserEyeOptions, ".
				 "PED_OD, ".
				 "PED_OS, ".
				 "Retina_Ischemia_OD, Retina_BRVO_OD, Retina_CRVO_OD, SR_Heme_OD, Classic_CNV_OD, Occult_CNV_OD, ".
				 "Retina_Ischemia_OS, Retina_BRVO_OS, Retina_CRVO_OS, SR_Heme_OS, Classic_CNV_OS, Occult_CNV_OS, ".				 "icgComments, ".
				 "disc_od_summary,disc_os_summary,retina_od_summary,retina_os_summary,".
				 "macula_od_summary,macula_os_summary, tech2InformPt, ".
				 "ptInformedNv,examTime,ContinueMeds,diagnosis,diagnosisOther, ".
				 "encounter_id,ordrby,ordrdt,rptTst1yr,forum_procedure  ".$signPathFieldName.$signDTmFieldName.
				 ") ".
				 "VALUES ".
				 "( ".
				 "NULL, '".$patient_id."', '".$exam_date."', '".$icg."', '".$icg_od."', '".$icg_early."', '".$icg_extra."', '".$comments_icg."', ".
				 "'".$performed_by."', '".$pa_under."', '".$pa_inter."', '".$pa_inter1."', '".$disc_od."', '".$disc_os."', '".$retina_od."', '".$retina_os."', '".$macula_od."', '".$macula_os."', ".
				 "'".$icg_signature."', '".$form_id."', '".$Stable."', '".$MonitorAg."', '".$FuRetina."', '".$FuApa."', '".$PatientInformed."', '".$ArgonLaser."', '".$phy."', ".
				 "'".$disc_cd_od."', '".$disc_cd_os."', '".$testresults_desc_od."', '".$testresults_desc_os."', '".$ArgonLaserEye."', '".$FuRetinaComments."', ".
				 "'".$Sharp_Pink_OD."', '".$Pale_OD."', '".$Large_Cap_OD."', '".$Sloping_OD."', '".$Notch_OD."', '".$NVD_OD."', ".
				 "'".$Leakage_OD."', '".$Retina_Hemorrhage_OD."', '".$Retina_Microaneurysms_OD."', '".$Retina_Exudates_OD."', '".$Retina_Laser_Scars_OD."', '".$Retina_NEVI_OD."', '".$Retina_SRVNM_OD."', '".$Retina_Edema_OD."', '".$Retina_Nevus_OD."', ".
				 "'".$Retina_BDR_OD_T."', '".$Retina_BDR_OD_1."', '".$Retina_BDR_OD_2."', '".$Retina_BDR_OD_3."', '".$Retina_BDR_OD_4."', ".
				 "'".$Retina_Druse_OD_T."', '".$Retina_Druse_OD_1."', '".$Retina_Druse_OD_2."', '".$Retina_Druse_OD_3."', '".$Retina_Druse_OD_4."', ".
				 "'".$Retina_RPE_Change_OD_T."', '".$Retina_RPE_Change_OD_1."', '".$Retina_RPE_Change_OD_2."', '".$Retina_RPE_Change_OD_3."', '".$Retina_RPE_Change_OD_4."', ".
				 "'".$Druse_OD."', '".$RPE_Changes_OD."', '".$SRNVM_OD."', '".$Edema_OD."', '".$Scars_OD."', '".$Hemorrhage_OD."', '".$Microaneurysms_OD."', '".$Exudates_OD."', ".
				 "'".$Macula_BDR_OD_T."', '".$Macula_BDR_OD_1."', '".$Macula_BDR_OD_2."', '".$Macula_BDR_OD_3."', '".$Macula_BDR_OD_4."', ".
				 "'".$Macula_SMD_OD_T."', '".$Macula_SMD_OD_1."', '".$Macula_SMD_OD_2."', '".$Macula_SMD_OD_3."', '".$Macula_SMD_OD_4."', ".
				 "'".$Feeder_Vessel_OD."', '".$Central_OD."', '".$Nasal_OD."', '".$Temporal_OD."', '".$Inferior_OD."','".$Superior_OD."', '".$Hot_Spot_OD."', '".$Hot_Spot_Val_OD."', ".
				 "'".$Sharp_Pink_OS."', '".$Pale_OS."', '".$Large_Cap_OS."', '".$Sloping_OS."', '".$Notch_OS."', '".$NVD_OS."', ".
				 "'".$Leakage_OS."', '".$Retina_Hemorrhage_OS."', '".$Retina_Microaneurysms_OS."', '".$Retina_Exudates_OS."', '".$Retina_Laser_Scars_OS."', '".$Retina_NEVI_OS."', '".$Retina_SRVNM_OS."', '".$Retina_Edema_OS."', '".$Retina_Nevus_OS."', ".
				 "'".$Retina_BDR_OS_T."', '".$Retina_BDR_OS_1."', '".$Retina_BDR_OS_2."', '".$Retina_BDR_OS_3."', '".$Retina_BDR_OS_4."', ".
				 "'".$Retina_Druse_OS_T."', '".$Retina_Druse_OS_1."', '".$Retina_Druse_OS_2."', '".$Retina_Druse_OS_3."', '".$Retina_Druse_OS_4."', ".
				 "'".$Retina_RPE_Change_OS_T."', '".$Retina_RPE_Change_OS_1."', '".$Retina_RPE_Change_OS_2."', '".$Retina_RPE_Change_OS_3."', '".$Retina_RPE_Change_OS_4."', ".
				 "'".$Druse_OS."', '".$RPE_Changes_OS."', '".$SRNVM_OS."', '".$Edema_OS."', '".$Scars_OS."', '".$Hemorrhage_OS."', '".$Microaneurysms_OS."', '".$Exudates_OS."', ".
				 "'".$Macula_BDR_OS_T."', '".$Macula_BDR_OS_1."', '".$Macula_BDR_OS_2."', '".$Macula_BDR_OS_3."', '".$Macula_BDR_OS_4."', ".
				 "'".$Macula_SMD_OS_T."', '".$Macula_SMD_OS_1."', '".$Macula_SMD_OS_2."', '".$Macula_SMD_OS_3."', '".$Macula_SMD_OS_4."', ".
				 "'".$Feeder_Vessel_OS."', '".$Central_OS."', '".$Nasal_OS."', '".$Temporal_OS."', '".$Inferior_OS."','".$Superior_OS."', '".$Hot_Spot_OS."', '".$Hot_Spot_Val_OS."', ".
				 "'".$ArgonLaserEyeOptions."', ".
				 "'".$PED_OD."', ".
				 "'".$PED_OS."', ".
			  	 "'".$Retina_Ischemia_OD."', '".$Retina_BRVO_OD."', '".$Retina_CRVO_OD."', '".$SR_Heme_OD."', '".$Classic_CNV_OD."', '".$Occult_CNV_OD."', ".
				 "'".$Retina_Ischemia_OS."', '".$Retina_BRVO_OS."', '".$Retina_CRVO_OS."', '".$SR_Heme_OS."', '".$Classic_CNV_OS."', '".$Occult_CNV_OS."', ".
				 "'".$icgComments."', ".
				 "'".$disc_od_summary."', '".$disc_os_summary."', '".$retina_od_summary."', '".$retina_os_summary."', ".
				 "'".$macula_od_summary."', '".$macula_os_summary."', '".$tech2InformPt."', ".
				 "'".$ptInformedNv."','".$examTime."','".$contiMeds."','".$diagnosis."','".$diagnosisOther."', ".
				 "'".$encounter_id."','".$ordrby."','".$ordrdt."','".$rptTst1yr."', '".$forum_procedure."' ".$signPathFieldValue.$signDTmFieldValue.
				 ") ";
			$ins_res = imw_query($sql);
			$insertId = imw_insert_id();
			$icg_id = $insertId;
		}else if(!empty($icg_id)){
			$sql = "UPDATE icg ".
				 "SET ".
				 //"patient_id='".$patient_id."', ".
				 "exam_date='".$exam_date."', ".
				 "icg='".$icg."', ".
				 "icg_od='".$icg_od."', ".
				 "icg_early='".$icg_early."', ".
				 "icg_extra='".$icg_extra."', ".
				 "comments_icg='".$comments_icg."', ".
				 "performed_by='".$performed_by."', ".
				 "pa_under='".$pa_under."', ".
				 "pa_inter='".$pa_inter."', ".
				 "pa_inter1='".$pa_inter1."', ".
				 "disc_od='".$disc_od."', ".
				 "disc_os='".$disc_os."', ".
				 "retina_od='".$retina_od."', ".
				 "retina_os='".$retina_os."', ".
				 "macula_od='".$macula_od."', ".
				 "macula_os='".$macula_os."', ".
				 "icg_signature='".$icg_signature."', ".
				 "Stable='".$Stable."', ".
				 "MonitorAg='".$MonitorAg."', ".
				 "FuRetina='".$FuRetina."', ".
				 "FuApa='".$FuApa."', ".
				 "PatientInformed='".$PatientInformed."', ".
				 "ArgonLaser='".$ArgonLaser."', ";
				 
				if(!$zeissAction){  /*Prevent changing interpretation status if "Forum Add/Delete"*/
					$sql .= "phy = '".$phy."', ";
				}
				
			$sql .= "disc_cd_od='".$disc_cd_od."', ".
				 "disc_cd_os='".$disc_cd_os."', ".
				 "testresults_desc_od='".$testresults_desc_od."', ".
				 "testresults_desc_os='".$testresults_desc_os."', ".
				 "ArgonLaserEye='".$ArgonLaserEye."', ".
				 "FuRetinaComments='".$FuRetinaComments."', ".
				 "ArgonLaserEyeOptions='".$ArgonLaserEyeOptions."', ".
				 "Sharp_Pink_OD ='".$Sharp_Pink_OD."', ".
				 "Pale_OD ='".$Pale_OD."', ".
				 "Large_Cap_OD ='".$Large_Cap_OD."', ".
				 "Sloping_OD ='".$Sloping_OD."', ".
				 "Notch_OD ='".$Notch_OD."', ".
				 "NVD_OD ='".$NVD_OD."', ".
				 "Leakage_OD ='".$Leakage_OD."', ".
				 "Retina_Hemorrhage_OD ='".$Retina_Hemorrhage_OD."', ".
				 "Retina_Microaneurysms_OD ='".$Retina_Microaneurysms_OD."', ".
				 "Retina_Exudates_OD ='".$Retina_Exudates_OD."', ".
				 "Retina_Laser_Scars_OD ='".$Retina_Laser_Scars_OD."', ".
				 "Retina_NEVI_OD ='".$Retina_NEVI_OD."', ".
				 "Retina_SRVNM_OD ='".$Retina_SRVNM_OD."', ".
				 "Retina_Edema_OD ='".$Retina_Edema_OD."', ".
				 "Retina_Nevus_OD ='".$Retina_Nevus_OD."', ".
				 "Retina_BDR_OD_T ='".$Retina_BDR_OD_T."', ".
				 "Retina_BDR_OD_1 ='".$Retina_BDR_OD_1."', ".
				 "Retina_BDR_OD_2 ='".$Retina_BDR_OD_2."', ".
				 "Retina_BDR_OD_3 ='".$Retina_BDR_OD_3."', ".
				 "Retina_BDR_OD_4 ='".$Retina_BDR_OD_4."', ".
				 "Retina_Druse_OD_T ='".$Retina_Druse_OD_T."', ".
				 "Retina_Druse_OD_1 ='".$Retina_Druse_OD_1."', ".
				 "Retina_Druse_OD_2 ='".$Retina_Druse_OD_2."', ".
				 "Retina_Druse_OD_3 ='".$Retina_Druse_OD_3."', ".
				 "Retina_Druse_OD_4 ='".$Retina_Druse_OD_4."', ".
				 "Retina_RPE_Change_OD_T ='".$Retina_RPE_Change_OD_T."', ".
				 "Retina_RPE_Change_OD_1 ='".$Retina_RPE_Change_OD_1."', ".
				 "Retina_RPE_Change_OD_2 ='".$Retina_RPE_Change_OD_2."', ".
				 "Retina_RPE_Change_OD_3 ='".$Retina_RPE_Change_OD_3."', ".
				 "Retina_RPE_Change_OD_4 ='".$Retina_RPE_Change_OD_4."', ".
				 "Druse_OD ='".$Druse_OD."', ".
				 "RPE_Changes_OD ='".$RPE_Changes_OD."', ".
				 "SRNVM_OD ='".$SRNVM_OD."', ".
				 "Edema_OD ='".$Edema_OD."', ".
				 "Scars_OD ='".$Scars_OD."', ".
				 "Hemorrhage_OD ='".$Hemorrhage_OD."', ".
				 "Microaneurysms_OD ='".$Microaneurysms_OD."', ".
				 "Exudates_OD ='".$Exudates_OD."', ".
				 "Macula_BDR_OD_T ='".$Macula_BDR_OD_T."', ".
				 "Macula_BDR_OD_1 ='".$Macula_BDR_OD_1."', ".
				 "Macula_BDR_OD_2 ='".$Macula_BDR_OD_2."', ".
				 "Macula_BDR_OD_3 ='".$Macula_BDR_OD_3."', ".
				 "Macula_BDR_OD_4 ='".$Macula_BDR_OD_4."', ".
				 "Macula_SMD_OD_T ='".$Macula_SMD_OD_T."', ".
				 "Macula_SMD_OD_1 ='".$Macula_SMD_OD_1."', ".
				 "Macula_SMD_OD_2 ='".$Macula_SMD_OD_2."', ".
				 "Macula_SMD_OD_3 ='".$Macula_SMD_OD_3."', ".
				 "Macula_SMD_OD_4 ='".$Macula_SMD_OD_4."', ".
				 "Feeder_Vessel_OD ='".$Feeder_Vessel_OD."', ".
				 "Central_OD ='".$Central_OD."', ".
				 "Nasal_OD ='".$Nasal_OD."', ".
				 "Temporal_OD ='".$Temporal_OD."', ".
				 "Inferior_OD ='".$Inferior_OD."', ".
				 "Superior_OD ='".$Superior_OD."', ".
				 "Hot_Spot_OD ='".$Hot_Spot_OD."', ".
				 "Hot_Spot_Val_OD ='".$Hot_Spot_Val_OD."', ".
				 "Sharp_Pink_OS ='".$Sharp_Pink_OS."', ".
				 "Pale_OS ='".$Pale_OS."', ".
				 "Large_Cap_OS ='".$Large_Cap_OS."', ".
				 "Sloping_OS ='".$Sloping_OS."', ".
				 "Notch_OS ='".$Notch_OS."', ".
				 "NVD_OS ='".$NVD_OS."', ".
				 "Leakage_OS ='".$Leakage_OS."', ".
				 "Retina_Hemorrhage_OS ='".$Retina_Hemorrhage_OS."', ".
				 "Retina_Microaneurysms_OS ='".$Retina_Microaneurysms_OS."', ".
				 "Retina_Exudates_OS ='".$Retina_Exudates_OS."', ".
				 "Retina_Laser_Scars_OS ='".$Retina_Laser_Scars_OS."', ".
				 "Retina_NEVI_OS ='".$Retina_NEVI_OS."', ".
				 "Retina_SRVNM_OS ='".$Retina_SRVNM_OS."', ".
				 "Retina_Edema_OS ='".$Retina_Edema_OS."', ".
				 "Retina_Nevus_OS ='".$Retina_Nevus_OS."', ".
				 "Retina_BDR_OS_T ='".$Retina_BDR_OS_T."', ".
				 "Retina_BDR_OS_1 ='".$Retina_BDR_OS_1."', ".
				 "Retina_BDR_OS_2 ='".$Retina_BDR_OS_2."', ".
				 "Retina_BDR_OS_3 ='".$Retina_BDR_OS_3."', ".
				 "Retina_BDR_OS_4 ='".$Retina_BDR_OS_4."', ".
				 "Retina_Druse_OS_T ='".$Retina_Druse_OS_T."', ".
				 "Retina_Druse_OS_1 ='".$Retina_Druse_OS_1."', ".
				 "Retina_Druse_OS_2 ='".$Retina_Druse_OS_2."', ".
				 "Retina_Druse_OS_3 ='".$Retina_Druse_OS_3."', ".
				 "Retina_Druse_OS_4 ='".$Retina_Druse_OS_4."', ".
				 "Retina_RPE_Change_OS_T ='".$Retina_RPE_Change_OS_T."', ".
				 "Retina_RPE_Change_OS_1 ='".$Retina_RPE_Change_OS_1."', ".
				 "Retina_RPE_Change_OS_2 ='".$Retina_RPE_Change_OS_2."', ".
				 "Retina_RPE_Change_OS_3 ='".$Retina_RPE_Change_OS_3."', ".
				 "Retina_RPE_Change_OS_4 ='".$Retina_RPE_Change_OS_4."', ".
				 "Druse_OS ='".$Druse_OS."', ".
				 "RPE_Changes_OS ='".$RPE_Changes_OS."', ".
				 "SRNVM_OS ='".$SRNVM_OS."', ".
				 "Edema_OS ='".$Edema_OS."', ".
				 "Scars_OS ='".$Scars_OS."', ".
				 "Hemorrhage_OS ='".$Hemorrhage_OS."', ".
				 "Microaneurysms_OS ='".$Microaneurysms_OS."', ".
				 "Exudates_OS ='".$Exudates_OS."', ".
				 "Macula_BDR_OS_T ='".$Macula_BDR_OS_T."', ".
				 "Macula_BDR_OS_1 ='".$Macula_BDR_OS_1."', ".
				 "Macula_BDR_OS_2 ='".$Macula_BDR_OS_2."', ".
				 "Macula_BDR_OS_3 ='".$Macula_BDR_OS_3."', ".
				 "Macula_BDR_OS_4 ='".$Macula_BDR_OS_4."', ".
				 "Macula_SMD_OS_T ='".$Macula_SMD_OS_T."', ".
				 "Macula_SMD_OS_1 ='".$Macula_SMD_OS_1."', ".
				 "Macula_SMD_OS_2 ='".$Macula_SMD_OS_2."', ".
				 "Macula_SMD_OS_3 ='".$Macula_SMD_OS_3."', ".
				 "Macula_SMD_OS_4 ='".$Macula_SMD_OS_4."', ".
				 "Feeder_Vessel_OS ='".$Feeder_Vessel_OS."', ".
				 "Central_OS ='".$Central_OS."', ".
				 "Nasal_OS ='".$Nasal_OS."', ".
				 "Temporal_OS ='".$Temporal_OS."', ".
				 "Inferior_OS ='".$Inferior_OS."', ".
				 "Superior_OS ='".$Superior_OS."', ".
				 "Hot_Spot_OS ='".$Hot_Spot_OS."', ".
				 "Hot_Spot_Val_OS ='".$Hot_Spot_Val_OS."', ".
				 "disc_od_summary ='".$disc_od_summary."', ".
				 "disc_os_summary ='".$disc_os_summary."', ".
				 "retina_od_summary ='".$retina_od_summary."', ".
				 "retina_os_summary ='".$retina_os_summary."', ".
				 "macula_od_summary ='".$macula_od_summary."', ".
				 "icgComments ='".$icgComments."', ".
				 "PED_OD ='".$PED_OD."', ".
				 "PED_OS ='".$PED_OS."', ".
				 "Retina_Ischemia_OD ='".$Retina_Ischemia_OD."', ".
				 "Retina_BRVO_OD ='".$Retina_BRVO_OD."', ".
				 "Retina_CRVO_OD ='".$Retina_CRVO_OD."', ".
				 "SR_Heme_OD ='".$SR_Heme_OD."', ".
				 "Classic_CNV_OD ='".$Classic_CNV_OD."', ".
				 "Occult_CNV_OD ='".$Occult_CNV_OD."', ".
				 "Retina_Ischemia_OS ='".$Retina_Ischemia_OS."', ".
				 "Retina_BRVO_OS ='".$Retina_BRVO_OS."', ".
				 "Retina_CRVO_OS ='".$Retina_CRVO_OS."', ".
				 "SR_Heme_OS ='".$SR_Heme_OS."', ".
				 "Classic_CNV_OS ='".$Classic_CNV_OS."', ".
				 "Occult_CNV_OS ='".$Occult_CNV_OS."', ".
				 "macula_os_summary ='".$macula_os_summary."', ".
				 "tech2InformPt = '".$tech2InformPt."', ".
				 "ptInformedNv = '".$ptInformedNv."', ".
				 "examTime = '".$examTime."', ".
				 "ContinueMeds = '".$contiMeds."', ".
				 "diagnosis='".$diagnosis."', ".
				 "diagnosisOther='".$diagnosisOther."', ".
				 "encounter_id='".$encounter_id."', ".
				 "ordrby='".$ordrby."',ordrdt='".$ordrdt."', ".
				 "forum_procedure='".$forum_procedure."', ".
				 $signPathQry.
				 $signDTmQry.
				 "rptTst1yr='".$rptTst1yr."' ".
				 //"WHERE form_id='".$form_id."' ";
				 "WHERE icg_id='".$icg_id."' ";
			$res = imw_query($sql);
		}
		
		/*code, Purpose: Generate HL7 message for Test*/
		$zeissMsgType="ICG";
		$zeissPatientId = $patient_id;
		$zeissTestId = $icg_id;
		include("./zeissTestHl7.php");
		/*End code*/

		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "ICG", $icg_id);
		/*--interpretation end--*/
		
		//Super-Bill -----------
		if(!empty($ordrby) && !empty($icg_id)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$exam_date;//$examDate;
			$arIn["sb_testId"]=$icg_id;
			$arIn["sb_testName"]="ICG";
                        $arIn["form_id"]=$form_id;
                        $arIn["test_interpreted"]= ((int)$phy > 0)? true : false;
                        
                        $oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);
		}
		//Super-Bill -----------
		
		//save tests Pdf
		$objTests->saveTestPdfExe_2($patient_id,$icg_id,"ICG");

		//FormId updates other tables -----
		if(!empty($form_id)){
			// Make chart notes valid
			$oChartNote->makeChartNotesValid($form_id);
		}
		
		echo "<script>".
				"try{";
				if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
					$afterSaveinPopup = 'yes';
				}else{
					$afterSaveinPopup = 'no';
					echo "top.alert_notification_show(\"Test has been saved.\");";
				}
				echo "
				window.location.replace(\"test_icg.php?noP=".$elem_noP."&tId=".$icg_id."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
				}catch(err){}".
			 "</script>";
		exit();
		break;
	}
	case "IVFA":{
		$patient_id = $_POST["elem_patientId"];
		$form_id = $_POST["elem_formId"];
		$hd_ivfa_mode = $_POST["hd_ivfa_mode"];
		$vf_id = $_POST["elem_ivfaId"];
		$exam_date = getDateFormatDB($_POST["elem_examDate"]);
		if($exam_date=='' || $exam_date=='0000-00-00') $exam_date = date('Y-m-d');
		$ivfa = $_POST["elem_ivfa"];
		$ivfa_od = $_POST["elem_ivfa_od"];
		$ivfa_early = $_POST["elem_ivfa_early"];
		$ivfa_extra = $_POST["elem_ivfa_extra"];
		$comments_ivfa = imw_real_escape_string($_POST["elem_comments_ivfa"]);
		$performed_by = $_POST["elem_performedBy"];
		$pa_under = $_POST["elem_pa_under"];
		$pa_inter = $_POST["elem_pa_inter"];
		$pa_inter1 = $_POST["elem_pa_inter1"];
		$disc_od = $_POST["elem_disc_od"];
		$disc_cd_od = $_POST["elem_disc_cd_od"];
		$retina_od = $_POST["elem_retina_od"];
		$macula_od = $_POST["elem_macula_od"];
		$testresults_desc_od = imw_real_escape_string($_POST["elem_testresults_desc_od"]);
		$disc_os = $_POST["elem_disc_os"];
		$disc_cd_os = $_POST["elem_disc_cd_os"];
		$retina_os = $_POST["elem_retina_os"];
		$macula_os = $_POST["elem_macula_os"];
		$testresults_desc_os = imw_real_escape_string($_POST["elem_testresults_desc_os"]);
		$Stable = $_POST["elem_Stable"];
		$MonitorAg = $_POST["elem_MonitorAg"];
		$FuApa = $_POST["elem_FuApa"];
		$PatientInformed = $_POST["elem_PatientInformed"];
		$ArgonLaser = $_POST["elem_ArgonLaser"];
		$ArgonLaserEye = $_POST["elem_ArgonLaserEye"];
		$ArgonLaserEyeOptions = $_POST["elem_ArgonLaserEyeOptions"];
		$FuRetina = $_POST["elem_FuRetina"];
		$FuRetinaComments = imw_real_escape_string($_POST["elem_FuRetinaComments"]);
		$ivfa_signature = $_POST["elem_ivfa_signature"];
		$phy = $_POST["elem_physician"];
		$tech2InformPt = $_POST["elem_tech2InformPt"];
		$ptInformedNv = $_POST["elem_informedPtNv"];
		$elem_noP = $_POST["elem_noP"];
		$examTime = $_POST["elem_examTime"];
		$contiMeds = $_POST["elem_contiMeds"];
		$diagnosis = imw_real_escape_string($_POST["elem_diagnosis"]);
		$diagnosisOther = imw_real_escape_string($_POST["elem_diagnosisOther"]);
		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
		$rptTst1yr = $_POST["elem_rptTst1yr"];
		
		$forum_procedure = (isset($_REQUEST['forum_procedure']))?imw_real_escape_string($_REQUEST['forum_procedure']):"";
		$zeissAction = (isset($_REQUEST['forum_procedure']))?$_POST['zeissAction']:false;

		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
			$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
			$encounter_id = $objTests->getEncounterId();
		}

		//summary
		$disc_od_summary=$disc_os_summary="";
		$retina_od_summary = $retina_os_summary = "";
		$macula_od_summary = $macula_os_summary = "";

		$Sharp_Pink_OD = $_POST["Sharp_Pink_OD"];
		$Pale_OD = $_POST["Pale_OD"];
		$Large_Cap_OD = $_POST["Large_Cap_OD"];
		$Sloping_OD = $_POST["Sloping_OD"];
		$Notch_OD = $_POST["Notch_OD"];
		$NVD_OD = $_POST["NVD_OD"];
		$Leakage_OD = $_POST["Leakage_OD"];
		$arr = array("Sharp &amp; Pink"=>$Sharp_Pink_OD, "Pale"=>$Pale_OD, "Large Cap"=>$Large_Cap_OD,"Sloping"=>$Sloping_OD,
				"Notch"=>$Notch_OD,"NVD"=>$NVD_OD,"Leakage"=>$Leakage_OD);
		$disc_od_summary = $objTests->getTestSumm($arr,"");

		$Retina_Hemorrhage_OD = $_POST["Retina_Hemorrhage_OD"];
		$Retina_Microaneurysms_OD = $_POST["Retina_Microaneurysms_OD"];
		$Retina_Exudates_OD = $_POST["Retina_Exudates_OD"];
		$Retina_Laser_Scars_OD = $_POST["Retina_Laser_Scars_OD"];
		$Retina_NEVI_OD = $_POST["Retina_NEVI_OD"];
		$Retina_SRVNM_OD = $_POST["Retina_SRVNM_OD"];
		$Retina_Edema_OD = $_POST["Retina_Edema_OD"];
		$Retina_Nevus_OD = $_POST["Retina_Nevus_OD"];
		
		$Retina_Ischemia_OD = $_POST["Retina_Ischemia_OD"];
		$Retina_BRVO_OD = $_POST["Retina_BRVO_OD"];
		$Retina_CRVO_OD = $_POST["Retina_CRVO_OD"];
		
		
		$Retina_BDR_OD_T = $_POST["Retina_BDR_OD_T"];
		$Retina_BDR_OD_1 = $_POST["Retina_BDR_OD_1"];
		$Retina_BDR_OD_2 = $_POST["Retina_BDR_OD_2"];
		$Retina_BDR_OD_3 = $_POST["Retina_BDR_OD_3"];
		$Retina_BDR_OD_4 = $_POST["Retina_BDR_OD_4"];
		$Retina_Druse_OD_T = $_POST["Retina_Druse_OD_T"];
		$Retina_Druse_OD_1 = $_POST["Retina_Druse_OD_1"];
		$Retina_Druse_OD_2 = $_POST["Retina_Druse_OD_2"];
		$Retina_Druse_OD_3 = $_POST["Retina_Druse_OD_3"];
		$Retina_Druse_OD_4 = $_POST["Retina_Druse_OD_4"];
		$Retina_RPE_Change_OD_T = $_POST["Retina_RPE_Change_OD_T"];
		$Retina_RPE_Change_OD_1 = $_POST["Retina_RPE_Change_OD_1"];
		$Retina_RPE_Change_OD_2 = $_POST["Retina_RPE_Change_OD_2"];
		$Retina_RPE_Change_OD_3 = $_POST["Retina_RPE_Change_OD_T"];
		$Retina_RPE_Change_OD_4 = $_POST["Retina_RPE_Change_OD_4"];

		$arr = array("Hemorrhage"=>$Retina_Hemorrhage_OD, "Microaneurysms"=>$Retina_Microaneurysms_OD,
					"Exudates"=>$Retina_Exudates_OD, "Laser Scars"=>$Retina_Laser_Scars_OD,
					"NVE"=>$Retina_NEVI_OD, "SRVNM"=>$Retina_SRVNM_OD, "Edema"=>$Retina_Edema_OD);
		$retina_od_summary .= $objTests->getTestSumm($arr,"");

		$arr = array("T"=>$Retina_BDR_OD_T,"+1"=>$Retina_BDR_OD_1,"+2"=>$Retina_BDR_OD_2,
					"+3"=>$Retina_BDR_OD_3,"+4"=>$Retina_BDR_OD_4);
		$retina_od_summary .= $objTests->getTestSumm($arr,"BDR");

		$arr = array("T"=>$Retina_Druse_OD_T,"+1"=>$Retina_Druse_OD_1,"+2"=>$Retina_Druse_OD_2,
					"+3"=>$Retina_Druse_OD_3,"+4"=>$Retina_Druse_OD_4);
		$retina_od_summary .= $objTests->getTestSumm($arr,"Druse");

		$arr = array("T"=>$Retina_RPE_Change_OD_T,"+1"=>$Retina_RPE_Change_OD_1,"+2"=>$Retina_RPE_Change_OD_2,
					"+3"=>$Retina_RPE_Change_OD_3,"+4"=>$Retina_RPE_Change_OD_4);
		$retina_od_summary .= $objTests->getTestSumm($arr,"RPE Change");

		$Druse_OD = $_POST["Druse_OD"];
		$RPE_Changes_OD = $_POST["RPE_Changes_OD"];
		$SRNVM_OD = $_POST["SRNVM_OD"];
		$Edema_OD = $_POST["Edema_OD"];
		$Scars_OD = $_POST["Scars_OD"];
		$Hemorrhage_OD = $_POST["Hemorrhage_OD"];
		$Microaneurysms_OD = $_POST["Microaneurysms_OD"];
		$Exudates_OD = $_POST["Exudates_OD"];
		$Macula_BDR_OD_T = $_POST["Macula_BDR_OD_T"];
		$Macula_BDR_OD_1 = $_POST["Macula_BDR_OD_1"];
		$Macula_BDR_OD_2 = $_POST["Macula_BDR_OD_2"];
		$Macula_BDR_OD_3 = $_POST["Macula_BDR_OD_3"];
		$Macula_BDR_OD_4 = $_POST["Macula_BDR_OD_4"];
		$Macula_SMD_OD_T = $_POST["Macula_SMD_OD_T"];
		$Macula_SMD_OD_1 = $_POST["Macula_SMD_OD_1"];
		$Macula_SMD_OD_2 = $_POST["Macula_SMD_OD_2"];
		$Macula_SMD_OD_3 = $_POST["Macula_SMD_OD_3"];
		$Macula_SMD_OD_4 = $_POST["Macula_SMD_OD_4"];
		
		$SR_Heme_OD = $_POST["SR_Heme_OD"];
		$Classic_CNV_OD = $_POST["Classic_CNV_OD"];
		$Occult_CNV_OD = $_POST["Occult_CNV_OD"];

		$arr = array("Druse"=>$Druse_OD,"RPE Changes"=>$RPE_Changes_OD,"SRNVM"=>$SRNVM_OD,"Edema"=>$Edema_OD,
				"Scars"=>$Scars_OD,"Hemorrhage"=>$Hemorrhage_OD,"Microaneurysms"=>$Microaneurysms_OD,"Exudates"=>$Exudates_OD);
		$macula_od_summary .= $objTests->getTestSumm($arr,"");

		$arr = array("T"=>$Macula_BDR_OD_T,"+1"=>$Macula_BDR_OD_1,"+2"=>$Macula_BDR_OD_2,
					"+3"=>$Macula_BDR_OD_3,"+4"=>$Macula_BDR_OD_4);
		$macula_od_summary .= $objTests->getTestSumm($arr,"BDR");

		$arr = array("T"=>$Macula_SMD_OD_T,"+1"=>$Macula_SMD_OD_1,"+2"=>$Macula_SMD_OD_2,
					"+3"=>$Macula_SMD_OD_3,"+4"=>$Macula_SMD_OD_4);
		$macula_od_summary .= $objTests->getTestSumm($arr,"SMD");

		$Sharp_Pink_OS = $_POST["Sharp_Pink_OS"];
		$Pale_OS = $_POST["Pale_OS"];
		$Large_Cap_OS = $_POST["Large_Cap_OS"];
		$Sloping_OS = $_POST["Sloping_OS"];
		$Notch_OS = $_POST["Notch_OS"];
		$NVD_OS = $_POST["NVD_OS"];
		$Leakage_OS = $_POST["Leakage_OS"];

		$arr = array("Sharp &amp; Pink"=>$Sharp_Pink_OS, "Pale"=>$Pale_OS, "Large Cap"=>$Large_Cap_OS,"Sloping"=>$Sloping_OS,
				"Notch"=>$Notch_OS,"NVD"=>$NVD_OS,"Leakage"=>$Leakage_OS);
		$disc_os_summary .= $objTests->getTestSumm($arr,"");

		$Retina_Hemorrhage_OS = $_POST["Retina_Hemorrhage_OS"];
		$Retina_Microaneurysms_OS = $_POST["Retina_Microaneurysms_OS"];
		$Retina_Exudates_OS = $_POST["Retina_Exudates_OS"];
		$Retina_Laser_Scars_OS = $_POST["Retina_Laser_Scars_OS"];
		$Retina_NEVI_OS = $_POST["Retina_NEVI_OS"];
		$Retina_SRVNM_OS = $_POST["Retina_SRVNM_OS"];
		$Retina_Edema_OS = $_POST["Retina_Edema_OS"];
		$Retina_Nevus_OS = $_POST["Retina_Nevus_OS"];
		
		$Retina_Ischemia_OS = $_POST["Retina_Ischemia_OS"];
		$Retina_BRVO_OS = $_POST["Retina_BRVO_OS"];
		$Retina_CRVO_OS = $_POST["Retina_CRVO_OS"];
		
		$Retina_BDR_OS_T = $_POST["Retina_BDR_OS_T"];
		$Retina_BDR_OS_1 = $_POST["Retina_BDR_OS_1"];
		$Retina_BDR_OS_2 = $_POST["Retina_BDR_OS_2"];
		$Retina_BDR_OS_3 = $_POST["Retina_BDR_OS_3"];
		$Retina_BDR_OS_4 = $_POST["Retina_BDR_OS_4"];
		$Retina_Druse_OS_T = $_POST["Retina_Druse_OS_T"];
		$Retina_Druse_OS_1 = $_POST["Retina_Druse_OS_1"];
		$Retina_Druse_OS_2 = $_POST["Retina_Druse_OS_2"];
		$Retina_Druse_OS_3 = $_POST["Retina_Druse_OS_3"];
		$Retina_Druse_OS_4 = $_POST["Retina_Druse_OS_4"];
		$Retina_RPE_Change_OS_T = $_POST["Retina_RPE_Change_OS_T"];
		$Retina_RPE_Change_OS_1 = $_POST["Retina_RPE_Change_OS_1"];
		$Retina_RPE_Change_OS_2 = $_POST["Retina_RPE_Change_OS_2"];
		$Retina_RPE_Change_OS_3 = $_POST["Retina_RPE_Change_OS_T"];
		$Retina_RPE_Change_OS_4 = $_POST["Retina_RPE_Change_OS_4"];
		
		

		$arr = array("Hemorrhage"=>$Retina_Hemorrhage_OS, "Microaneurysms"=>$Retina_Microaneurysms_OS,
					"Exudates"=>$Retina_Exudates_OS, "Laser Scars"=>$Retina_Laser_Scars_OS,
					"NVE"=>$Retina_NEVI_OS, "SRVNM"=>$Retina_SRVNM_OS, "Edema"=>$Retina_Edema_OS);
		$retina_os_summary .= $objTests->getTestSumm($arr,"");

		$arr = array("T"=>$Retina_BDR_OS_T,"+1"=>$Retina_BDR_OS_1,"+2"=>$Retina_BDR_OS_2,
					"+3"=>$Retina_BDR_OS_3,"+4"=>$Retina_BDR_OS_4);
		$retina_os_summary .= $objTests->getTestSumm($arr,"BDR");

		$arr = array("T"=>$Retina_Druse_OS_T,"+1"=>$Retina_Druse_OS_1,"+2"=>$Retina_Druse_OS_2,
					"+3"=>$Retina_Druse_OS_3,"+4"=>$Retina_Druse_OS_4);
		$retina_os_summary .= $objTests->getTestSumm($arr,"Druse");

		$arr = array("T"=>$Retina_RPE_Change_OS_T,"+1"=>$Retina_RPE_Change_OS_1,"+2"=>$Retina_RPE_Change_OS_2,
					"+3"=>$Retina_RPE_Change_OS_3,"+4"=>$Retina_RPE_Change_OS_4);
		$retina_os_summary .= $objTests->getTestSumm($arr,"RPE Change");

		$Druse_OS = $_POST["Druse_OS"];
		$RPE_Changes_OS = $_POST["RPE_Changes_OS"];
		$SRNVM_OS = $_POST["SRNVM_OS"];
		$Edema_OS = $_POST["Edema_OS"];
		$Scars_OS = $_POST["Scars_OS"];
		$Hemorrhage_OS = $_POST["Hemorrhage_OS"];
		$Microaneurysms_OS = $_POST["Microaneurysms_OS"];
		$Exudates_OS = $_POST["Exudates_OS"];
		$Macula_BDR_OS_T = $_POST["Macula_BDR_OS_T"];
		$Macula_BDR_OS_1 = $_POST["Macula_BDR_OS_1"];
		$Macula_BDR_OS_2 = $_POST["Macula_BDR_OS_2"];
		$Macula_BDR_OS_3 = $_POST["Macula_BDR_OS_3"];
		$Macula_BDR_OS_4 = $_POST["Macula_BDR_OS_4"];
		$Macula_SMD_OS_T = $_POST["Macula_SMD_OS_T"];
		$Macula_SMD_OS_1 = $_POST["Macula_SMD_OS_1"];
		$Macula_SMD_OS_2 = $_POST["Macula_SMD_OS_2"];
		$Macula_SMD_OS_3 = $_POST["Macula_SMD_OS_3"];
		$Macula_SMD_OS_4 = $_POST["Macula_SMD_OS_4"];

		$PED_OD = $_POST["PED_OD"];
		$PED_OS = $_POST["PED_OS"];
		
		$SR_Heme_OS = $_POST["SR_Heme_OS"];
		$Classic_CNV_OS = $_POST["Classic_CNV_OS"];
		$Occult_CNV_OS = $_POST["Occult_CNV_OS"];
		
		$ivfaComments = imw_real_escape_string($_POST["ivfaComments"]);


		$arr = array("Druse"=>$Druse_OS,"RPE Changes"=>$RPE_Changes_OS,"SRNVM"=>$SRNVM_OS,"Edema"=>$Edema_OS,
				"Scars"=>$Scars_OS,"Hemorrhage"=>$Hemorrhage_OS,"Microaneurysms"=>$Microaneurysms_OS,"Exudates"=>$Exudates_OS);
		$macula_os_summary .= $objTests->getTestSumm($arr,"");

		$arr = array("T"=>$Macula_BDR_OS_T,"+1"=>$Macula_BDR_OS_1,"+2"=>$Macula_BDR_OS_2,
					"+3"=>$Macula_BDR_OS_3,"+4"=>$Macula_BDR_OS_4);
		$macula_os_summary .= $objTests->getTestSumm($arr,"BDR");

		$arr = array("T"=>$Macula_SMD_OS_T,"+1"=>$Macula_SMD_OS_1,"+2"=>$Macula_SMD_OS_2,
					"+3"=>$Macula_SMD_OS_3,"+4"=>$Macula_SMD_OS_4);
		$macula_os_summary .= $objTests->getTestSumm($arr,"SMD");

		//check
		if(empty($vf_id)){

			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;

			if(isset($arrTest2edit["IVFA"]) && !empty($arrTest2edit["IVFA"])){
				//Check in db
				$cQry = "select vf_id FROM ivfa
						WHERE patient_id ='".$patient_id."'
						AND vf_id = '".$arrTest2edit["IVFA"]."' ";
				$row = sqlQuery($cQry);
				$vf_id = (($row == false) || empty($row["vf_id"])) ? "0" : $row["vf_id"];
				//Unset Session IVFA
				$arrTest2edit["IVFA"] = "";
				$arrTest2edit["IVFA"] = NULL;
				unset($arrTest2edit["IVFA"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);
			}else{
				$cQry = "select vf_id FROM ivfa WHERE patient_id='".$patient_id."'
						AND exam_date = '".$exam_date."' AND examTime = '".$examTime."' ";
				$row = sqlQuery($cQry);
				$vf_id = (($row == false) || empty($row["vf_id"])) ? "0" : $row["vf_id"];
			}
		}

		if(empty($vf_id)){
			$phy=($zeissAction)?0:$phy; /*Mark test uninterpreted if "Forum Add"*/
			$sql = "INSERT INTO ivfa ".
				 "( ".
				 "vf_id, patient_id, exam_date, ivfa, ivfa_od, ivfa_early, ivfa_extra, comments_ivfa, ".
				 "performed_by, pa_under, pa_inter, pa_inter1, disc_od, disc_os, retina_od, retina_os, macula_od, macula_os, ".
				 "ivfa_signature, form_id, Stable, MonitorAg, FuRetina, FuApa, PatientInformed, ArgonLaser, phy, ".
				 "disc_cd_od, disc_cd_os, testresults_desc_od, testresults_desc_os, ArgonLaserEye, FuRetinaComments, ".
				 "Sharp_Pink_OD, Pale_OD, Large_Cap_OD, Sloping_OD, Notch_OD, NVD_OD, ".
				 "Leakage_OD, Retina_Hemorrhage_OD, Retina_Microaneurysms_OD, Retina_Exudates_OD, Retina_Laser_Scars_OD, Retina_NEVI_OD, Retina_SRVNM_OD, Retina_Edema_OD, Retina_Nevus_OD, ".
				 "Retina_BDR_OD_T, Retina_BDR_OD_1, Retina_BDR_OD_2, Retina_BDR_OD_3, Retina_BDR_OD_4, ".
				 "Retina_Druse_OD_T, Retina_Druse_OD_1, Retina_Druse_OD_2, Retina_Druse_OD_3, Retina_Druse_OD_4, ".
				 "Retina_RPE_Change_OD_T, Retina_RPE_Change_OD_1, Retina_RPE_Change_OD_2, Retina_RPE_Change_OD_3, Retina_RPE_Change_OD_4, ".
		 		 "Druse_OD, RPE_Changes_OD, SRNVM_OD, Edema_OD, Scars_OD, Hemorrhage_OD, Microaneurysms_OD, Exudates_OD, ".
				 "Macula_BDR_OD_T, Macula_BDR_OD_1, Macula_BDR_OD_2, Macula_BDR_OD_3, Macula_BDR_OD_4, ".
				 "Macula_SMD_OD_T, Macula_SMD_OD_1, Macula_SMD_OD_2, Macula_SMD_OD_3, Macula_SMD_OD_4, ".
				 "Sharp_Pink_OS, Pale_OS, Large_Cap_OS, Sloping_OS, Notch_OS, NVD_OS, ".
				 "Leakage_OS, Retina_Hemorrhage_OS, Retina_Microaneurysms_OS, Retina_Exudates_OS, Retina_Laser_Scars_OS, Retina_NEVI_OS, Retina_SRVNM_OS, Retina_Edema_OS, Retina_Nevus_OS, ".
				 "Retina_BDR_OS_T, Retina_BDR_OS_1, Retina_BDR_OS_2, Retina_BDR_OS_3, Retina_BDR_OS_4, ".
				 "Retina_Druse_OS_T, Retina_Druse_OS_1, Retina_Druse_OS_2, Retina_Druse_OS_3, Retina_Druse_OS_4, ".
				 "Retina_RPE_Change_OS_T, Retina_RPE_Change_OS_1, Retina_RPE_Change_OS_2, Retina_RPE_Change_OS_3, Retina_RPE_Change_OS_4, ".
		 		 "Druse_OS, RPE_Changes_OS, SRNVM_OS, Edema_OS, Scars_OS, Hemorrhage_OS, Microaneurysms_OS, Exudates_OS, ".
				 "Macula_BDR_OS_T, Macula_BDR_OS_1, Macula_BDR_OS_2, Macula_BDR_OS_3, Macula_BDR_OS_4, ".
				 "Macula_SMD_OS_T, Macula_SMD_OS_1, Macula_SMD_OS_2, Macula_SMD_OS_3, Macula_SMD_OS_4, ".
				 "ArgonLaserEyeOptions, ".
				 "PED_OD, ".
				 "PED_OS, ".
				 "Retina_Ischemia_OD, Retina_BRVO_OD, Retina_CRVO_OD, SR_Heme_OD, Classic_CNV_OD, Occult_CNV_OD, ".
				 "Retina_Ischemia_OS, Retina_BRVO_OS, Retina_CRVO_OS, SR_Heme_OS, Classic_CNV_OS, Occult_CNV_OS, ".				 "ivfaComments, ".
				 "disc_od_summary,disc_os_summary,retina_od_summary,retina_os_summary,".
				 "macula_od_summary,macula_os_summary, tech2InformPt, ".
				 "ptInformedNv,examTime,ContinueMeds,diagnosis,diagnosisOther, ".
				 "encounter_id,ordrby,ordrdt,rptTst1yr,forum_procedure  ".$signPathFieldName.$signDTmFieldName.
				 ") ".
				 "VALUES ".
				 "( ".
				 "NULL, '".$patient_id."', '".$exam_date."', '".$ivfa."', '".$ivfa_od."', '".$ivfa_early."', '".$ivfa_extra."', '".$comments_ivfa."', ".
				 "'".$performed_by."', '".$pa_under."', '".$pa_inter."', '".$pa_inter1."', '".$disc_od."', '".$disc_os."', '".$retina_od."', '".$retina_os."', '".$macula_od."', '".$macula_os."', ".
				 "'".$ivfa_signature."', '".$form_id."', '".$Stable."', '".$MonitorAg."', '".$FuRetina."', '".$FuApa."', '".$PatientInformed."', '".$ArgonLaser."', '".$phy."', ".
				 "'".$disc_cd_od."', '".$disc_cd_os."', '".$testresults_desc_od."', '".$testresults_desc_os."', '".$ArgonLaserEye."', '".$FuRetinaComments."', ".
				 "'".$Sharp_Pink_OD."', '".$Pale_OD."', '".$Large_Cap_OD."', '".$Sloping_OD."', '".$Notch_OD."', '".$NVD_OD."', ".
				 "'".$Leakage_OD."', '".$Retina_Hemorrhage_OD."', '".$Retina_Microaneurysms_OD."', '".$Retina_Exudates_OD."', '".$Retina_Laser_Scars_OD."', '".$Retina_NEVI_OD."', '".$Retina_SRVNM_OD."', '".$Retina_Edema_OD."', '".$Retina_Nevus_OD."', ".
				 "'".$Retina_BDR_OD_T."', '".$Retina_BDR_OD_1."', '".$Retina_BDR_OD_2."', '".$Retina_BDR_OD_3."', '".$Retina_BDR_OD_4."', ".
				 "'".$Retina_Druse_OD_T."', '".$Retina_Druse_OD_1."', '".$Retina_Druse_OD_2."', '".$Retina_Druse_OD_3."', '".$Retina_Druse_OD_4."', ".
				 "'".$Retina_RPE_Change_OD_T."', '".$Retina_RPE_Change_OD_1."', '".$Retina_RPE_Change_OD_2."', '".$Retina_RPE_Change_OD_3."', '".$Retina_RPE_Change_OD_4."', ".
				 "'".$Druse_OD."', '".$RPE_Changes_OD."', '".$SRNVM_OD."', '".$Edema_OD."', '".$Scars_OD."', '".$Hemorrhage_OD."', '".$Microaneurysms_OD."', '".$Exudates_OD."', ".
				 "'".$Macula_BDR_OD_T."', '".$Macula_BDR_OD_1."', '".$Macula_BDR_OD_2."', '".$Macula_BDR_OD_3."', '".$Macula_BDR_OD_4."', ".
				 "'".$Macula_SMD_OD_T."', '".$Macula_SMD_OD_1."', '".$Macula_SMD_OD_2."', '".$Macula_SMD_OD_3."', '".$Macula_SMD_OD_4."', ".
				 "'".$Sharp_Pink_OS."', '".$Pale_OS."', '".$Large_Cap_OS."', '".$Sloping_OS."', '".$Notch_OS."', '".$NVD_OS."', ".
				 "'".$Leakage_OS."', '".$Retina_Hemorrhage_OS."', '".$Retina_Microaneurysms_OS."', '".$Retina_Exudates_OS."', '".$Retina_Laser_Scars_OS."', '".$Retina_NEVI_OS."', '".$Retina_SRVNM_OS."', '".$Retina_Edema_OS."', '".$Retina_Nevus_OS."', ".
				 "'".$Retina_BDR_OS_T."', '".$Retina_BDR_OS_1."', '".$Retina_BDR_OS_2."', '".$Retina_BDR_OS_3."', '".$Retina_BDR_OS_4."', ".
				 "'".$Retina_Druse_OS_T."', '".$Retina_Druse_OS_1."', '".$Retina_Druse_OS_2."', '".$Retina_Druse_OS_3."', '".$Retina_Druse_OS_4."', ".
				 "'".$Retina_RPE_Change_OS_T."', '".$Retina_RPE_Change_OS_1."', '".$Retina_RPE_Change_OS_2."', '".$Retina_RPE_Change_OS_3."', '".$Retina_RPE_Change_OS_4."', ".
				 "'".$Druse_OS."', '".$RPE_Changes_OS."', '".$SRNVM_OS."', '".$Edema_OS."', '".$Scars_OS."', '".$Hemorrhage_OS."', '".$Microaneurysms_OS."', '".$Exudates_OS."', ".
				 "'".$Macula_BDR_OS_T."', '".$Macula_BDR_OS_1."', '".$Macula_BDR_OS_2."', '".$Macula_BDR_OS_3."', '".$Macula_BDR_OS_4."', ".
				 "'".$Macula_SMD_OS_T."', '".$Macula_SMD_OS_1."', '".$Macula_SMD_OS_2."', '".$Macula_SMD_OS_3."', '".$Macula_SMD_OS_4."', ".
				 "'".$ArgonLaserEyeOptions."', ".
				 "'".$PED_OD."', ".
				 "'".$PED_OS."', ".
			  	 "'".$Retina_Ischemia_OD."', '".$Retina_BRVO_OD."', '".$Retina_CRVO_OD."', '".$SR_Heme_OD."', '".$Classic_CNV_OD."', '".$Occult_CNV_OD."', ".
				 "'".$Retina_Ischemia_OS."', '".$Retina_BRVO_OS."', '".$Retina_CRVO_OS."', '".$SR_Heme_OS."', '".$Classic_CNV_OS."', '".$Occult_CNV_OS."', ".
				 "'".$ivfaComments."', ".
				 "'".$disc_od_summary."', '".$disc_os_summary."', '".$retina_od_summary."', '".$retina_os_summary."', ".
				 "'".$macula_od_summary."', '".$macula_os_summary."', '".$tech2InformPt."', ".
				 "'".$ptInformedNv."','".$examTime."','".$contiMeds."','".$diagnosis."','".$diagnosisOther."', ".
				 "'".$encounter_id."','".$ordrby."','".$ordrdt."','".$rptTst1yr."','".$forum_procedure."' ".$signPathFieldValue.$signDTmFieldValue.
				 ") ";
			$res_insert = imw_query($sql);
			$insertId = imw_insert_id();
			$vf_id = $insertId;
		}else if(!empty($vf_id)){
			$sql = "UPDATE ivfa ".
				 "SET ".
				 //"patient_id='".$patient_id."', ".
				 "exam_date='".$exam_date."', ".
				 "ivfa='".$ivfa."', ".
				 "ivfa_od='".$ivfa_od."', ".
				 "ivfa_early='".$ivfa_early."', ".
				 "ivfa_extra='".$ivfa_extra."', ".
				 "comments_ivfa='".$comments_ivfa."', ".
				 "performed_by='".$performed_by."', ".
				 "pa_under='".$pa_under."', ".
				 "pa_inter='".$pa_inter."', ".
				 "pa_inter1='".$pa_inter1."', ".
				 "disc_od='".$disc_od."', ".
				 "disc_os='".$disc_os."', ".
				 "retina_od='".$retina_od."', ".
				 "retina_os='".$retina_os."', ".
				 "macula_od='".$macula_od."', ".
				 "macula_os='".$macula_os."', ".
				 "ivfa_signature='".$ivfa_signature."', ".
				 "Stable='".$Stable."', ".
				 "MonitorAg='".$MonitorAg."', ".
				 "FuRetina='".$FuRetina."', ".
				 "FuApa='".$FuApa."', ".
				 "PatientInformed='".$PatientInformed."', ".
				 "ArgonLaser='".$ArgonLaser."', ";
				 
				if(!$zeissAction){  /*Prevent changing interpretation status if "Forum Add/Delete"*/
					$sql .= "phy = '".$phy."', ";
				}
				
			$sql .= "disc_cd_od='".$disc_cd_od."', ".
				 "disc_cd_os='".$disc_cd_os."', ".
				 "testresults_desc_od='".$testresults_desc_od."', ".
				 "testresults_desc_os='".$testresults_desc_os."', ".
				 "ArgonLaserEye='".$ArgonLaserEye."', ".
				 "FuRetinaComments='".$FuRetinaComments."', ".
				 "ArgonLaserEyeOptions='".$ArgonLaserEyeOptions."', ".
				 "Sharp_Pink_OD ='".$Sharp_Pink_OD."', ".
				 "Pale_OD ='".$Pale_OD."', ".
				 "Large_Cap_OD ='".$Large_Cap_OD."', ".
				 "Sloping_OD ='".$Sloping_OD."', ".
				 "Notch_OD ='".$Notch_OD."', ".
				 "NVD_OD ='".$NVD_OD."', ".
				 "Leakage_OD ='".$Leakage_OD."', ".
				 "Retina_Hemorrhage_OD ='".$Retina_Hemorrhage_OD."', ".
				 "Retina_Microaneurysms_OD ='".$Retina_Microaneurysms_OD."', ".
				 "Retina_Exudates_OD ='".$Retina_Exudates_OD."', ".
				 "Retina_Laser_Scars_OD ='".$Retina_Laser_Scars_OD."', ".
				 "Retina_NEVI_OD ='".$Retina_NEVI_OD."', ".
				 "Retina_SRVNM_OD ='".$Retina_SRVNM_OD."', ".
				 "Retina_Edema_OD ='".$Retina_Edema_OD."', ".
				 "Retina_Nevus_OD ='".$Retina_Nevus_OD."', ".
				 "Retina_BDR_OD_T ='".$Retina_BDR_OD_T."', ".
				 "Retina_BDR_OD_1 ='".$Retina_BDR_OD_1."', ".
				 "Retina_BDR_OD_2 ='".$Retina_BDR_OD_2."', ".
				 "Retina_BDR_OD_3 ='".$Retina_BDR_OD_3."', ".
				 "Retina_BDR_OD_4 ='".$Retina_BDR_OD_4."', ".
				 "Retina_Druse_OD_T ='".$Retina_Druse_OD_T."', ".
				 "Retina_Druse_OD_1 ='".$Retina_Druse_OD_1."', ".
				 "Retina_Druse_OD_2 ='".$Retina_Druse_OD_2."', ".
				 "Retina_Druse_OD_3 ='".$Retina_Druse_OD_3."', ".
				 "Retina_Druse_OD_4 ='".$Retina_Druse_OD_4."', ".
				 "Retina_RPE_Change_OD_T ='".$Retina_RPE_Change_OD_T."', ".
				 "Retina_RPE_Change_OD_1 ='".$Retina_RPE_Change_OD_1."', ".
				 "Retina_RPE_Change_OD_2 ='".$Retina_RPE_Change_OD_2."', ".
				 "Retina_RPE_Change_OD_3 ='".$Retina_RPE_Change_OD_3."', ".
				 "Retina_RPE_Change_OD_4 ='".$Retina_RPE_Change_OD_4."', ".
				 "Druse_OD ='".$Druse_OD."', ".
				 "RPE_Changes_OD ='".$RPE_Changes_OD."', ".
				 "SRNVM_OD ='".$SRNVM_OD."', ".
				 "Edema_OD ='".$Edema_OD."', ".
				 "Scars_OD ='".$Scars_OD."', ".
				 "Hemorrhage_OD ='".$Hemorrhage_OD."', ".
				 "Microaneurysms_OD ='".$Microaneurysms_OD."', ".
				 "Exudates_OD ='".$Exudates_OD."', ".
				 "Macula_BDR_OD_T ='".$Macula_BDR_OD_T."', ".
				 "Macula_BDR_OD_1 ='".$Macula_BDR_OD_1."', ".
				 "Macula_BDR_OD_2 ='".$Macula_BDR_OD_2."', ".
				 "Macula_BDR_OD_3 ='".$Macula_BDR_OD_3."', ".
				 "Macula_BDR_OD_4 ='".$Macula_BDR_OD_4."', ".
				 "Macula_SMD_OD_T ='".$Macula_SMD_OD_T."', ".
				 "Macula_SMD_OD_1 ='".$Macula_SMD_OD_1."', ".
				 "Macula_SMD_OD_2 ='".$Macula_SMD_OD_2."', ".
				 "Macula_SMD_OD_3 ='".$Macula_SMD_OD_3."', ".
				 "Macula_SMD_OD_4 ='".$Macula_SMD_OD_4."', ".
				 "Sharp_Pink_OS ='".$Sharp_Pink_OS."', ".
				 "Pale_OS ='".$Pale_OS."', ".
				 "Large_Cap_OS ='".$Large_Cap_OS."', ".
				 "Sloping_OS ='".$Sloping_OS."', ".
				 "Notch_OS ='".$Notch_OS."', ".
				 "NVD_OS ='".$NVD_OS."', ".
				 "Leakage_OS ='".$Leakage_OS."', ".
				 "Retina_Hemorrhage_OS ='".$Retina_Hemorrhage_OS."', ".
				 "Retina_Microaneurysms_OS ='".$Retina_Microaneurysms_OS."', ".
				 "Retina_Exudates_OS ='".$Retina_Exudates_OS."', ".
				 "Retina_Laser_Scars_OS ='".$Retina_Laser_Scars_OS."', ".
				 "Retina_NEVI_OS ='".$Retina_NEVI_OS."', ".
				 "Retina_SRVNM_OS ='".$Retina_SRVNM_OS."', ".
				 "Retina_Edema_OS ='".$Retina_Edema_OS."', ".
				 "Retina_Nevus_OS ='".$Retina_Nevus_OS."', ".
				 "Retina_BDR_OS_T ='".$Retina_BDR_OS_T."', ".
				 "Retina_BDR_OS_1 ='".$Retina_BDR_OS_1."', ".
				 "Retina_BDR_OS_2 ='".$Retina_BDR_OS_2."', ".
				 "Retina_BDR_OS_3 ='".$Retina_BDR_OS_3."', ".
				 "Retina_BDR_OS_4 ='".$Retina_BDR_OS_4."', ".
				 "Retina_Druse_OS_T ='".$Retina_Druse_OS_T."', ".
				 "Retina_Druse_OS_1 ='".$Retina_Druse_OS_1."', ".
				 "Retina_Druse_OS_2 ='".$Retina_Druse_OS_2."', ".
				 "Retina_Druse_OS_3 ='".$Retina_Druse_OS_3."', ".
				 "Retina_Druse_OS_4 ='".$Retina_Druse_OS_4."', ".
				 "Retina_RPE_Change_OS_T ='".$Retina_RPE_Change_OS_T."', ".
				 "Retina_RPE_Change_OS_1 ='".$Retina_RPE_Change_OS_1."', ".
				 "Retina_RPE_Change_OS_2 ='".$Retina_RPE_Change_OS_2."', ".
				 "Retina_RPE_Change_OS_3 ='".$Retina_RPE_Change_OS_3."', ".
				 "Retina_RPE_Change_OS_4 ='".$Retina_RPE_Change_OS_4."', ".
				 "Druse_OS ='".$Druse_OS."', ".
				 "RPE_Changes_OS ='".$RPE_Changes_OS."', ".
				 "SRNVM_OS ='".$SRNVM_OS."', ".
				 "Edema_OS ='".$Edema_OS."', ".
				 "Scars_OS ='".$Scars_OS."', ".
				 "Hemorrhage_OS ='".$Hemorrhage_OS."', ".
				 "Microaneurysms_OS ='".$Microaneurysms_OS."', ".
				 "Exudates_OS ='".$Exudates_OS."', ".
				 "Macula_BDR_OS_T ='".$Macula_BDR_OS_T."', ".
				 "Macula_BDR_OS_1 ='".$Macula_BDR_OS_1."', ".
				 "Macula_BDR_OS_2 ='".$Macula_BDR_OS_2."', ".
				 "Macula_BDR_OS_3 ='".$Macula_BDR_OS_3."', ".
				 "Macula_BDR_OS_4 ='".$Macula_BDR_OS_4."', ".
				 "Macula_SMD_OS_T ='".$Macula_SMD_OS_T."', ".
				 "Macula_SMD_OS_1 ='".$Macula_SMD_OS_1."', ".
				 "Macula_SMD_OS_2 ='".$Macula_SMD_OS_2."', ".
				 "Macula_SMD_OS_3 ='".$Macula_SMD_OS_3."', ".
				 "Macula_SMD_OS_4 ='".$Macula_SMD_OS_4."', ".
				 "disc_od_summary ='".$disc_od_summary."', ".
				 "disc_os_summary ='".$disc_os_summary."', ".
				 "retina_od_summary ='".$retina_od_summary."', ".
				 "retina_os_summary ='".$retina_os_summary."', ".
				 "macula_od_summary ='".$macula_od_summary."', ".
				 "ivfaComments ='".$ivfaComments."', ".
				 "PED_OD ='".$PED_OD."', ".
				 "PED_OS ='".$PED_OS."', ".
				 "Retina_Ischemia_OD ='".$Retina_Ischemia_OD."', ".
				 "Retina_BRVO_OD ='".$Retina_BRVO_OD."', ".
				 "Retina_CRVO_OD ='".$Retina_CRVO_OD."', ".
				 "SR_Heme_OD ='".$SR_Heme_OD."', ".
				 "Classic_CNV_OD ='".$Classic_CNV_OD."', ".
				 "Occult_CNV_OD ='".$Occult_CNV_OD."', ".
				 "Retina_Ischemia_OS ='".$Retina_Ischemia_OS."', ".
				 "Retina_BRVO_OS ='".$Retina_BRVO_OS."', ".
				 "Retina_CRVO_OS ='".$Retina_CRVO_OS."', ".
				 "SR_Heme_OS ='".$SR_Heme_OS."', ".
				 "Classic_CNV_OS ='".$Classic_CNV_OS."', ".
				 "Occult_CNV_OS ='".$Occult_CNV_OS."', ".
				 "macula_os_summary ='".$macula_os_summary."', ".
				 "tech2InformPt = '".$tech2InformPt."', ".
				 "ptInformedNv = '".$ptInformedNv."', ".
				 "examTime = '".$examTime."', ".
				 "ContinueMeds = '".$contiMeds."', ".
				 "diagnosis='".$diagnosis."', ".
				 "diagnosisOther='".$diagnosisOther."', ".
				 "encounter_id='".$encounter_id."', ".
				 "ordrby='".$ordrby."',ordrdt='".$ordrdt."', ".
				 "forum_procedure='".$forum_procedure."', ".
				 $signPathQry.
				 $signDTmQry.
				 "rptTst1yr='".$rptTst1yr."' ".
				 //"WHERE form_id='".$form_id."' ";
				 "WHERE vf_id='".$vf_id."' ";
			$res = sqlQuery($sql);
		}
		
		/*code, Purpose: Generate HL7 message for Test*/
		$zeissMsgType="IVFA";
		$zeissPatientId = $patient_id;
		$zeissTestId = $vf_id;
		include("./zeissTestHl7.php");
		/*End code*/

		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "IVFA", $vf_id);

		//Super-Bill -----------
		if(!empty($ordrby) && !empty($vf_id)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$exam_date;
			$arIn["sb_testId"]=$vf_id;
			$arIn["sb_testName"]="IVFA";
                        $arIn["form_id"]=$form_id;
                        $arIn["test_interpreted"]= ((int)$phy > 0)? true : false;

			$oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);
		}
		//Super-Bill -----------

		//save tests Pdf
		$objTests->saveTestPdfExe_2($patient_id,$vf_id,"IVFA");

		//FormId updates other tables -----
		if(!empty($form_id)){
			// Make chart notes valid
			$oChartNote->makeChartNotesValid($form_id);
		}
		
		//FormId updates other tables -----
		if(!empty($elem_noP)){
			echo "<script>".
					"try{";
					if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
						$afterSaveinPopup = 'yes';
					}else{
						$afterSaveinPopup = 'no';
						echo "top.alert_notification_show(\"Test has been saved.\");";
					}
					echo "
					window.location.replace(\"test_ivfa.php?noP=".$elem_noP."&tId=".$vf_id."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
					}catch(err){}".
				 "</script>";
			exit();
		}
		break;
	}
	case "VF-GL":{
		$patientId = $_POST["elem_patientId"];
		$formId = $_POST["elem_formId"];
		$hd_vf_mode = $_POST["hd_vf_mode"];
		$vf_id = $_POST["elem_vfId"];
		$examDate =  getDateFormatDB($_POST["elem_examDate"]);
		if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
		$testTime = $_POST["elem_testTime"];
		$vf = $_POST["elem_vf"];
		$vf_sel = $_POST["elem_vfSel"];
		$vf_sel_other = $_POST["elem_vfSelOther"];
		$vf_eye = $_POST["elem_vfEye"];
		$performedBy = $_POST["elem_performedBy"];
		$ptUnderstanding = $_POST["elem_ptUnderstanding"];
		$diagnosis = imw_real_escape_string($_POST["elem_diagnosis_od"]);
		$diagnosisOther = imw_real_escape_string($_POST["elem_diagnosisOtherOd"]);
		$diagnosis_os = imw_real_escape_string($_POST["elem_diagnosis_os"]);
		$diagnosisOther_os = imw_real_escape_string($_POST["elem_diagnosisOtherOs"]);		
		
		$reliabilityOd = $_POST["elem_reliabilityOd"];
		$reliabilityOs = $_POST["elem_reliabilityOs"];
		
		$stable = $_POST["elem_stable"];
		$monitorIOP = $_POST["elem_monitorIOP"];
		$fuApa = $_POST["elem_fuApa"];
		$ptInformed = $_POST["elem_ptInformed"];
		$comments_od = addslashes($_POST["elem_comments_od"]);
		$comments_os = addslashes($_POST["elem_comments_os"]);
		$comments = $comments_od."!~!".$comments_os;
		$signature = $_POST["elem_vfSign"];
		$phyName = $_POST["elem_physician"];
		$tech2InformPt = $_POST["elem_tech2InformPt"];		
		$elem_noP = $_POST["elem_noP"];
		$examTime = $_POST["elem_examTime"];
		$contiMeds = $_POST["elem_contiMeds"];
		$rptTst1yr = $_POST["elem_rptTst1yr"];
		
		$interpretation_OD="";
		if(!empty($_POST["elem_stable_OD"])){$interpretation_OD.=$_POST["elem_stable_OD"].", ";}
		if(!empty($_POST["elem_improve_OD"])){$interpretation_OD.=$_POST["elem_improve_OD"].", ";}
		if(!empty($_POST["elem_worse_OD"])){$interpretation_OD.=$_POST["elem_worse_OD"].", ";}
		if(!empty($_POST["elem_likeProgrsn_OD"])){$interpretation_OD.=$_POST["elem_likeProgrsn_OD"].", ";}
		if(!empty($_POST["elem_possibleProgrsn_OD"])){$interpretation_OD.=$_POST["elem_possibleProgrsn_OD"].", ";}
		if(!empty($_POST["elem_glaucoma_stage_OD"])){$interpretation_OD.=$_POST["elem_glaucoma_stage_OD"].", ";}
		$interpretation_OS="";
		if(!empty($_POST["elem_stable_OS"])){$interpretation_OS.=$_POST["elem_stable_OS"].", ";}
		if(!empty($_POST["elem_improve_OS"])){$interpretation_OS.=$_POST["elem_improve_OS"].", ";}
		if(!empty($_POST["elem_worse_OS"])){$interpretation_OS.=$_POST["elem_worse_OS"].", ";}
		if(!empty($_POST["elem_likeProgrsn_OS"])){$interpretation_OS.=$_POST["elem_likeProgrsn_OS"].", ";}
		if(!empty($_POST["elem_possibleProgrsn_OS"])){$interpretation_OS.=$_POST["elem_possibleProgrsn_OS"].", ";}
		if(!empty($_POST["elem_glaucoma_stage_OS"])){$interpretation_OS.=$_POST["elem_glaucoma_stage_OS"].", ";}
		$comments_interp = imw_real_escape_string($_POST["elem_comments_interp"]);
		$glaucoma_stage_opt_OD="";
		if(!empty($_POST["elem_glst_unspecified_OD"])){ $glaucoma_stage_opt_OD.=$_POST["elem_glst_unspecified_OD"].", "; }
		if(!empty($_POST["elem_glst_mild_OD"])){ $glaucoma_stage_opt_OD.=$_POST["elem_glst_mild_OD"].", ";}
		if(!empty($_POST["elem_glst_moderate_OD"])){ $glaucoma_stage_opt_OD.=$_POST["elem_glst_moderate_OD"].", ";}
		if(!empty($_POST["elem_glst_severe_OD"])){ $glaucoma_stage_opt_OD.=$_POST["elem_glst_severe_OD"].", ";}
		if(!empty($_POST["elem_glst_intermediate_OD"])){ $glaucoma_stage_opt_OD.=$_POST["elem_glst_intermediate_OD"].", ";}
		$glaucoma_stage_opt_OS="";
		if(!empty($_POST["elem_glst_unspecified_OS"])){ $glaucoma_stage_opt_OS.=$_POST["elem_glst_unspecified_OS"].", "; }
		if(!empty($_POST["elem_glst_mild_OS"])){ $glaucoma_stage_opt_OS.=$_POST["elem_glst_mild_OS"].", ";}
		if(!empty($_POST["elem_glst_moderate_OS"])){ $glaucoma_stage_opt_OS.=$_POST["elem_glst_moderate_OS"].", ";}
		if(!empty($_POST["elem_glst_severe_OS"])){ $glaucoma_stage_opt_OS.=$_POST["elem_glst_severe_OS"].", ";}
		if(!empty($_POST["elem_glst_intermediate_OS"])){ $glaucoma_stage_opt_OS.=$_POST["elem_glst_intermediate_OS"].", ";}
		//if(!empty($_POST["elem_glst_indeterminate"])){ $glaucoma_stage_opt.=$_POST["elem_glst_indeterminate"];}
		$plan="";
		if(!empty($_POST["elem_ptInformYPhy"])){ $plan.=$_POST["elem_ptInformYPhy"].", ";}
		if(!empty($_POST["elem_2bCallYTech"])){$plan.=$_POST["elem_2bCallYTech"].", ";}
		if(!empty($_POST["elem_byLetter"])){$plan.=$_POST["elem_byLetter"].", ";}
		if(!empty($_POST["elem_willInfrmNextVisit"])){$plan.=$_POST["elem_willInfrmNextVisit"].", ";}
		if(!empty($_POST["elem_contMeds"])){$plan.=$_POST["elem_contMeds"].", ";}
		if(!empty($_POST["elem_monitorFind"])){$plan.=$_POST["elem_monitorFind"].", ";}
		//if(!empty($_POST["elem_repeatTestNxtVst"])){$plan.=$_POST["elem_repeatTestNxtVst"];}
		if(!empty($_POST["elem_repeatTest"])){$plan.=$_POST["elem_repeatTest"];}
		
		//$repeatTestNxtVstEye = $_POST["elem_repeatTestNxtVstEye"];
		$repeatTestNxtVstEye = "";
		$repeatTestVal1 = $_POST["elem_repeatTestVal1"];
		$repeatTestVal2 = $_POST["elem_repeatTestVal2"];
		$repeatTestEye=$_POST["elem_repeatTestEye"];
		
		//$comments_plan = imw_real_escape_string($_POST["elem_comments_plan"]);
		$comments_plan = "";

		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);

		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
			$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
			$encounter_id = $objTests->getEncounterId();
		}
		
		
		 $details_high_od="";
		 if(!empty($_POST["elem_detailOd_hgtFixLoss"])){  $details_high_od.=$_POST["elem_detailOd_hgtFixLoss"].", "; }
		 if(!empty($_POST["elem_detailOd_hgtFalsePos"])){  $details_high_od.=$_POST["elem_detailOd_hgtFalsePos"].", "; }
		 if(!empty($_POST["elem_detailOd_hgtFalseNeg"])){  $details_high_od.=$_POST["elem_detailOd_hgtFalseNeg"]; }
		 
		 $poor_study_od =$_POST["elem_poorStudyOd"];
		 $poor_study_od_desc = $_POST["elem_poorStudyOd_desc"];
		 $poor_study_od_desc_other = $_POST["elem_poorStudyOd_descOther"];
		 
		 $intratest_fluctuation_od = $_POST["elem_intraFluctOd"];
		 $artifact_od  = $_POST["elem_artifactOd"];
		 $details_lids_od  = "";
		 if(!empty($_POST["elem_detailOd_lid"])){  $details_lids_od.=$_POST["elem_detailOd_lid"].", "; }
		 if(!empty($_POST["elem_detailOd_lens_rim"])){  $details_lids_od.=$_POST["elem_detailOd_lens_rim"].", "; }
		 if(!empty($_POST["elem_detailOd_lens_power"])){  $details_lids_od.=$_POST["elem_detailOd_lens_power"].", "; }
		 if(!empty($_POST["elem_detailOd_cloverleaf"])){  $details_lids_od.=$_POST["elem_detailOd_cloverleaf"]; }
		 
		 $normal_od  = $_POST["elem_normalFullOd"];  
		 $nonspecific_od  = $_POST["elem_nonspecificOd"]; 
		 
		 $nasal_step_od  = ""; 		 
		 if(!empty($_POST["elem_nasalStepOd_Sup"])){  $nasal_step_od.=$_POST["elem_nasalStepOd_Sup"].", "; }
		 if(!empty($_POST["elem_nasalStepOd_Inf"])){  $nasal_step_od.=$_POST["elem_nasalStepOd_Inf"]; }
		 
		 $arcuate_od  = ""; 
		 if(!empty($_POST["elem_arcuateOd_Sup"])){  $arcuate_od.=$_POST["elem_arcuateOd_Sup"].", "; }
		 if(!empty($_POST["elem_arcuateOd_Inf"])){  $arcuate_od.=$_POST["elem_arcuateOd_Inf"]; }
		 
		 $hemifield_od  = ""; 
		 if(!empty($_POST["elem_hemifieldOd_Sup"])){  $hemifield_od.=$_POST["elem_hemifieldOd_Sup"].", "; }
		 if(!empty($_POST["elem_hemifieldOd_Inf"])){  $hemifield_od.=$_POST["elem_hemifieldOd_Inf"]; }
		 
		 $paracentral_od  = ""; 
		 if(!empty($_POST["elem_paracentralOd_Sup"])){  $paracentral_od.=$_POST["elem_paracentralOd_Sup"].", "; }
		 if(!empty($_POST["elem_paracentralOd_Inf"])){  $paracentral_od.=$_POST["elem_paracentralOd_Inf"]; }
		 
		 $into_fixation_od  = ""; 
		 if(!empty($_POST["elem_intoFixOd_Sup"])){  $into_fixation_od.=$_POST["elem_intoFixOd_Sup"].", "; }
		 if(!empty($_POST["elem_intoFixOd_Inf"])){  $into_fixation_od.=$_POST["elem_intoFixOd_Inf"]; }
		 
		 $mdOd = $_POST["elem_mdOd"];
		 $psdOd = $_POST["elem_psdOd"];
		 $vfiOd = $_POST["elem_vfiOd"];
		 
		 $central_island_od  = imw_real_escape_string($_POST["elem_centralIslandOd"]); 
		 $enlarged_blind_spot_od  = $_POST["elem_enlargeBlindSpotOd"]; 
		 $cecocentral_scotone_od  = $_POST["elem_cecoScotoneOd"]; 
		 $central_scotoma_od  = $_POST["elem_cenScotomaOd"]; 
		 
		 $hemianopsia_od  = "";
		 if(!empty($_POST["elem_hemianopsiaOd_Right"])){$hemianopsia_od  .= $_POST["elem_hemianopsiaOd_Right"].", ";}
		 if(!empty($_POST["elem_hemianopsiaOd_Left"])){$hemianopsia_od  .= $_POST["elem_hemianopsiaOd_Left"].", ";}
		 //if(!empty($_POST["elem_hemianopsiaOd_Binasal"])){$hemianopsia_od  .= $_POST["elem_hemianopsiaOd_Binasal"].", ";}
		 if(!empty($_POST["elem_hemianopsiaOd_Bitemporal"])){$hemianopsia_od  .= $_POST["elem_hemianopsiaOd_Bitemporal"];}		 
		 
		 $quadrantanopsia_od  = "";
		 if(!empty($_POST["elem_QuadrantanopsiaOd_RightSup"])){  $quadrantanopsia_od.=$_POST["elem_QuadrantanopsiaOd_RightSup"].", "; }
		 if(!empty($_POST["elem_QuadrantanopsiaOd_LeftSup"])){  $quadrantanopsia_od.=$_POST["elem_QuadrantanopsiaOd_LeftSup"].", "; }
		 if(!empty($_POST["elem_QuadrantanopsiaOd_RightInf"])){  $quadrantanopsia_od.=$_POST["elem_QuadrantanopsiaOd_RightInf"].", "; }
		 if(!empty($_POST["elem_QuadrantanopsiaOd_LeftInf"])){  $quadrantanopsia_od.=$_POST["elem_QuadrantanopsiaOd_LeftInf"]; }
		 
		 $homonomous_od = "";
		 if(!empty($_POST["elem_HomonomousOd_Congruent"])){  $homonomous_od.=$_POST["elem_HomonomousOd_Congruent"].", "; }
		 if(!empty($_POST["elem_HomonomousOd_Incongruent"])){  $homonomous_od.=$_POST["elem_HomonomousOd_Incongruent"]; }
		 
		 //$congruent_od  = $_POST["elem_congruentOd"]; 
		 //$incongruent_od  = $_POST["elem_incongruentOd"]; 
		 $synthesis_od = imw_real_escape_string($_POST["elem_synthesisOd"]);		 
		 
		 //os
		 $details_high_os="";
		 if(!empty($_POST["elem_detailOs_hgtFixLoss"])){  $details_high_os.=$_POST["elem_detailOs_hgtFixLoss"].", "; }
		 if(!empty($_POST["elem_detailOs_hgtFalsePos"])){  $details_high_os.=$_POST["elem_detailOs_hgtFalsePos"].", "; }
		 if(!empty($_POST["elem_detailOs_hgtFalseNeg"])){  $details_high_os.=$_POST["elem_detailOs_hgtFalseNeg"]; }
		 
		 $poor_study_os =$_POST["elem_poorStudyOs"];
		 $poor_study_os_desc = $_POST["elem_poorStudyOs_desc"];
		 $poor_study_os_desc_other = $_POST["elem_poorStudyOs_descOther"];
		 
		 $intratest_fluctuation_os = $_POST["elem_intraFluctOs"];
		 $artifact_os  = $_POST["elem_artifactOs"];
		 $details_lids_os  = "";
		 if(!empty($_POST["elem_detailOs_lid"])){  $details_lids_os.=$_POST["elem_detailOs_lid"].", "; }
		 if(!empty($_POST["elem_detailOs_lens_rim"])){  $details_lids_os.=$_POST["elem_detailOs_lens_rim"].", "; }
		 if(!empty($_POST["elem_detailOs_lens_power"])){  $details_lids_os.=$_POST["elem_detailOs_lens_power"].", "; }
		 if(!empty($_POST["elem_detailOs_cloverleaf"])){  $details_lids_os.=$_POST["elem_detailOs_cloverleaf"]; }
		 
		 $normal_os  = $_POST["elem_normalFullOs"];  
		 $nonspecific_os  = $_POST["elem_nonspecificOs"]; 
		 
		 $nasal_step_os  = ""; 		 
		 if(!empty($_POST["elem_nasalStepOs_Sup"])){  $nasal_step_os.=$_POST["elem_nasalStepOs_Sup"].", "; }
		 if(!empty($_POST["elem_nasalStepOs_Inf"])){  $nasal_step_os.=$_POST["elem_nasalStepOs_Inf"]; }
		 
		 $arcuate_os  = ""; 
		 if(!empty($_POST["elem_arcuateOs_Sup"])){  $arcuate_os.=$_POST["elem_arcuateOs_Sup"].", "; }
		 if(!empty($_POST["elem_arcuateOs_Inf"])){  $arcuate_os.=$_POST["elem_arcuateOs_Inf"]; }
		 
		 $hemifield_os  = ""; 
		 if(!empty($_POST["elem_hemifieldOs_Sup"])){  $hemifield_os.=$_POST["elem_hemifieldOs_Sup"].", "; }
		 if(!empty($_POST["elem_hemifieldOs_Inf"])){  $hemifield_os.=$_POST["elem_hemifieldOs_Inf"]; }
		 
		 $paracentral_os  = ""; 
		 if(!empty($_POST["elem_paracentralOs_Sup"])){  $paracentral_os.=$_POST["elem_paracentralOs_Sup"].", "; }
		 if(!empty($_POST["elem_paracentralOs_Inf"])){  $paracentral_os.=$_POST["elem_paracentralOs_Inf"]; }
		 
		 $into_fixation_os  = ""; 
		 if(!empty($_POST["elem_intoFixOs_Sup"])){  $into_fixation_os.=$_POST["elem_intoFixOs_Sup"].", "; }
		 if(!empty($_POST["elem_intoFixOs_Inf"])){  $into_fixation_os.=$_POST["elem_intoFixOs_Inf"]; }
		 
		 $mdOs = $_POST["elem_mdOs"];
		 $psdOs = $_POST["elem_psdOs"];
		 $vfiOs = $_POST["elem_vfiOs"];		 
		 
		 $central_island_os  = imw_real_escape_string($_POST["elem_centralIslandOs"]); 
		 $enlarged_blind_spot_os  = $_POST["elem_enlargeBlindSpotOs"]; 
		 $cecocentral_scotone_os  = $_POST["elem_cecoScotoneOs"]; 
		 $central_scotoma_os  = $_POST["elem_cenScotomaOs"]; 
		 
		 $hemianopsia_os  = "";
		 if(!empty($_POST["elem_hemianopsiaOs_Right"])){$hemianopsia_os  .= $_POST["elem_hemianopsiaOs_Right"].", ";}
		 if(!empty($_POST["elem_hemianopsiaOs_Left"])){$hemianopsia_os  .= $_POST["elem_hemianopsiaOs_Left"].", ";}
		 if(!empty($_POST["elem_hemianopsiaOs_Binasal"])){$hemianopsia_os  .= $_POST["elem_hemianopsiaOs_Binasal"].", ";}
		 if(!empty($_POST["elem_hemianopsiaOs_Bitemporal"])){$hemianopsia_os  .= $_POST["elem_hemianopsiaOs_Bitemporal"];}		 
		 
		 $quadrantanopsia_os  = "";
		 if(!empty($_POST["elem_QuadrantanopsiaOs_RightSup"])){  $quadrantanopsia_os.=$_POST["elem_QuadrantanopsiaOs_RightSup"].", "; }
		 if(!empty($_POST["elem_QuadrantanopsiaOs_LeftSup"])){  $quadrantanopsia_os.=$_POST["elem_QuadrantanopsiaOs_LeftSup"].", "; }
		 if(!empty($_POST["elem_QuadrantanopsiaOs_RightInf"])){  $quadrantanopsia_os.=$_POST["elem_QuadrantanopsiaOs_RightInf"].", "; }
		 if(!empty($_POST["elem_QuadrantanopsiaOs_LeftInf"])){  $quadrantanopsia_os.=$_POST["elem_QuadrantanopsiaOs_LeftInf"]; }
		 
		 $homonomous_os = "";
		 if(!empty($_POST["elem_HomonomousOs_Congruent"])){  $homonomous_os.=$_POST["elem_HomonomousOs_Congruent"].", "; }
		 if(!empty($_POST["elem_HomonomousOs_Incongruent"])){  $homonomous_os.=$_POST["elem_HomonomousOs_Incongruent"]; }
		 
		 //$congruent_os  = $_POST["elem_congruentOs"]; 
		 //$incongruent_os  = $_POST["elem_incongruentOs"]; 
		 $synthesis_os = imw_real_escape_string($_POST["elem_synthesisOs"]);	

		$improve = $_POST["elem_improve"];	
		$worse = $_POST["elem_worse"];		
		

		//Summary
		$summaryOd=$summaryOs="";		
		if(!empty($reliabilityOd)){$summaryOd .= "Reliability : ".$reliabilityOd."; ";}
		if(!empty($mdOd)){ $summaryOd .= "MD : ".$mdOd."dB; ";  }
		if(!empty($psdOd)){ $summaryOd .= "PSD : ".$psdOd."dB; ";  }
		if(!empty($vfiOd)){ $summaryOd .= "VFI : ".$vfiOd."%; ";  }		
		
		if(!empty($details_high_od))$summaryOd .= "Details: ".$details_high_od.";";		
		if(!empty($poor_study_od))$summaryOd .= trim("".$poor_study_od." ".$poor_study_od_desc." ".$poor_study_od_desc_other.";");		
		if(!empty($intratest_fluctuation_od))$summaryOd .= "".$intratest_fluctuation_od.";";
		if(!empty($artifact_od))$summaryOd .= "".$artifact_od.";";
		if(!empty($details_lids_od))$summaryOd .= "Details: ".$details_lids_od.";";
		if(!empty($normal_od))$summaryOd .= "".$normal_od.";";
		if(!empty($nonspecific_od))$summaryOd .= "".$nonspecific_od.";";
		if(!empty($nasal_step_od))$summaryOd .= "Nasal Step: ".$nasal_step_od.";";
		if(!empty($arcuate_od))$summaryOd .= "Arcuate: ".$arcuate_od.";";
		if(!empty($hemifield_od))$summaryOd .= "Hemifield: ".$hemifield_od.";";
		if(!empty($paracentral_od))$summaryOd .= "Paracentral: ".$paracentral_od.";";
		if(!empty($into_fixation_od))$summaryOd .= "Into Fixation: ".$into_fixation_od.";";
		if(!empty($central_island_od))$summaryOd .= "Central Island: Remaining ".$central_island_od." degrees;";
		if(!empty($enlarged_blind_spot_od))$summaryOd .= "".$enlarged_blind_spot_od.";";
		if(!empty($cecocentral_scotone_od))$summaryOd .= "".$cecocentral_scotone_od.";";
		if(!empty($central_scotoma_od))$summaryOd .= "".$central_scotoma_od.";";
		//if(!empty($hemianopsia_od))$summaryOd .= "".$hemianopsia_od.";";
		//if(!empty($quadrantanopsia_od))$summaryOd .= "".$quadrantanopsia_od.";";
		//if(!empty($congruent_od))$summaryOd .= "".$congruent_od.";";
		//if(!empty($incongruent_od))$summaryOd .= "".$incongruent_od.";";
		if(!empty($synthesis_od))$summaryOd .= "Synthesis: ".$synthesis_od.";";
		
		//OS
		if(!empty($reliabilityOs)){$summaryOs .= "Reliability : ".$reliabilityOs."; ";}
		if(!empty($mdOs)){ $summaryOs .= "MD : ".$mdOs."dB; ";  }
		if(!empty($psdOs)){ $summaryOs .= "PSD : ".$psdOs."dB; ";  }
		if(!empty($vfiOs)){ $summaryOs .= "VFI : ".$vfiOs."%; ";  }
		
		if(!empty($details_high_os))$summaryOs .= "Details: ".$details_high_os.";";
		if(!empty($poor_study_os))$summaryOs .= trim("".$poor_study_os." ".$poor_study_os_desc." ".$poor_study_os_desc_other.";");
		if(!empty($intratest_fluctuation_os))$summaryOs .= "".$intratest_fluctuation_os.";";
		if(!empty($artifact_os))$summaryOs .= "".$artifact_os.";";
		if(!empty($details_lids_os))$summaryOs .= "Details: ".$details_lids_os.";";
		if(!empty($normal_os))$summaryOs .= "".$normal_os.";";
		if(!empty($nonspecific_os))$summaryOs .= "".$nonspecific_os.";";
		if(!empty($nasal_step_os))$summaryOs .= "Nasal Step: ".$nasal_step_os.";";
		if(!empty($arcuate_os))$summaryOs .= "Arcuate: ".$arcuate_os.";";
		if(!empty($hemifield_os))$summaryOs .= "Hemifield: ".$hemifield_os.";";
		if(!empty($paracentral_os))$summaryOs .= "Paracentral: ".$paracentral_os.";";
		if(!empty($into_fixation_os))$summaryOs .= "Into Fixation: ".$into_fixation_os.";";
		if(!empty($central_island_os))$summaryOs .= "Central Island: Remaining ".$central_island_os." degrees;";
		if(!empty($enlarged_blind_spot_os))$summaryOs .= "".$enlarged_blind_spot_os.";";
		if(!empty($cecocentral_scotone_os))$summaryOs .= "".$cecocentral_scotone_os.";";
		if(!empty($central_scotoma_os))$summaryOs .= "".$central_scotoma_os.";";
		//if(!empty($hemianopsia_os))$summaryOs .= "".$hemianopsia_os.";";
		//if(!empty($quadrantanopsia_os))$summaryOs .= "".$quadrantanopsia_os.";";
		//if(!empty($congruent_os))$summaryOs .= "".$congruent_os.";";
		//if(!empty($incongruent_os))$summaryOs .= "".$incongruent_os.";";
		if(!empty($synthesis_os))$summaryOs .= "Synthesis: ".$synthesis_os.";";
		
		$descOd = (!empty($summaryOd)) ? $summaryOd : "";
		$descOs = (!empty($summaryOs)) ? $summaryOs : "";
		$ptInformedNv = $_POST["elem_informedPtNv"]; // Pt informed Next visit.
		
		$elem_gla_mac = $_POST["elem_gla_mac_od"]; //od
		$gla_mac_other_od = $_POST["elem_gla_macOtherOd"];//
		$gla_mac_os = $_POST["elem_gla_mac_os"];
		$gla_mac_other_os = $_POST["elem_gla_macOtherOs"];		

		//Tech Comments
		if($_POST["techComments"] == 'Technician Comments'){
			$_POST["techComments"] = '';
		}
		$elem_techComments = addslashes($_POST['techComments']);
		$forum_procedure = (isset($_REQUEST['forum_procedure']))?imw_real_escape_string($_REQUEST['forum_procedure']):"";
		$zeissAction = (isset($_REQUEST['forum_procedure']))?$_POST['zeissAction']:false;
		
		//check
		if(empty($vf_id)){
			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;
			if(isset($arrTest2edit["VF-GL"]) && !empty($arrTest2edit["VF-GL"])){
				//Check in db
				$cQry = "select vf_gl_id FROM vf_gl
						WHERE patientId ='".$patientId."'
						AND vf_gl_id = '".$arrTest2edit["VF-GL"]."' ";
				$row = sqlQuery($cQry);
				$vf_id = (($row == false) || empty($row["vf_gl_id"])) ? "0" : $row["vf_gl_id"];

				//Unset Session VF-GL
				$arrTest2edit["VF-GL"] = "";
				$arrTest2edit["VF-GL"] = NULL;
				unset($arrTest2edit["VF-GL"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);
			}else{
				$cQry = "select vf_gl_id FROM vf_gl
						WHERE patientId ='".$patientId."'
						AND examDate = '".$examDate."' AND examTime = '".$examTime."' ";
				$row = sqlQuery($cQry);
				$vf_id = (($row == false) || empty($row["vf_gl_id"])) ? "0" : $row["vf_gl_id"];
			}
		}

		if(empty($vf_id)){
			$phyName=($zeissAction)?0:$phyName; /*Mark test uninterpreted if "Forum Add"*/
			$sql = "INSERT INTO vf_gl ".
				 "( ".
				 "vf_gl_id, vf_gl, vf_gl_sel, vf_gl_sel_other, vf_gl_eye, performedBy, diagnosis, ".
				 "ptUnderstanding, reliabilityOd, reliabilityOs, vf_gl_Od, vf_gl_Os, descOd, descOs, ".
				 "stable, fuApa, ptInformed, comments, signature, phyName, examDate, ".				 
				 "patientId, formId, diagnosisOther, monitorIOP,tech2InformPt, ".
				 "techComments, ptInformedNv, ".
				 "examTime,contiMeds,rptTst1yr, ".				 
				 "encounter_id,ordrby,ordrdt, elem_gla_mac, ".
				 
				 "details_high_od ,  poor_study_od ,  intratest_fluctuation_od ,  
				 artifact_od ,   details_lids_od ,   normal_od ,  
				 nonspecific_od ,   nasal_step_od ,   arcuate_od ,  
				 hemifield_od ,   paracentral_od ,   into_fixation_od ,  
				 central_island_od ,   enlarged_blind_spot_od ,   cecocentral_scotone_od ,  
				 central_scotoma_od ,   hemianopsia_od ,   quadrantanopsia_od ,  
				 congruent_od ,   incongruent_od ,   synthesis_od, ".
				 
				 "details_high_os ,  poor_study_os ,  intratest_fluctuation_os ,  
				 artifact_os ,   details_lids_os ,   normal_os ,  
				 nonspecific_os ,   nasal_step_os ,   arcuate_os ,  
				 hemifield_os ,   paracentral_os ,   into_fixation_os ,  
				 central_island_os ,   enlarged_blind_spot_os ,   cecocentral_scotone_os ,  
				 central_scotoma_os ,   hemianopsia_os ,   quadrantanopsia_os ,  
				 congruent_os ,   incongruent_os ,   synthesis_os, ".
				 "poor_study_od_desc, poor_study_od_desc_other, poor_study_os_desc, poor_study_os_desc_other, ".
				 "improve, worse, ".
				 "gla_mac_other_od, gla_mac_os, gla_mac_other_os, diagnosis_os, diagnosisOther_os, 
					mdOd, psdOd, vfiOd, mdOs, psdOs, vfiOs, 
					homonomous_od, homonomous_os, interpretation_OD, interpretation_OS, comments_interp, 
					glaucoma_stage_opt_OD, glaucoma_stage_opt_OS, plan, repeatTestNxtVstEye,
					repeatTestVal1, repeatTestVal2, repeatTestEye, comments_plan, testTime, forum_procedure
					".$signPathFieldName.$signDTmFieldName.
				 ") ".
				 "VALUES ".
				 "( ".
				 "NULL, '".$vf."', '".$vf_sel."', '".$vf_sel_other."', '".$vf_eye."', '".$performedBy."', '".$diagnosis."', ".
				 "'".$ptUnderstanding."', '".$reliabilityOd."', '".$reliabilityOs."', '".$vfOd."', '".$vfOs."', '".$descOd."', '".$descOs."', ".
				 "'".$stable."', '".$fuApa."', '".$ptInformed."', '".$comments."', '".$signature."', '".$phyName."', '".$examDate."', ".
				 "'".$patientId."', '".$formId."', '".$diagnosisOther."', '".$monitorIOP."', '".$tech2InformPt."', ".
				 "'".$elem_techComments."', '".$ptInformedNv."', ".
				 "'".$examTime."','".$contiMeds."', '".$rptTst1yr."', ".
				 "'".$encounter_id."','".$ordrby."','".$ordrdt."','".$elem_gla_mac."', ".
				 
				 "'".$details_high_od."' ,  '".$poor_study_od."' ,  '".$intratest_fluctuation_od."' , ".  
				 "'".$artifact_od."' ,  '".$details_lids_od."' ,  '".$normal_od."' , ".
				 "'".$nonspecific_od."' ,  '".$nasal_step_od."' ,  '".$arcuate_od."' , ".
				 "'".$hemifield_od."' ,  '".$paracentral_od."' ,  '".$into_fixation_od."' , ".
				 "'".$central_island_od."' ,  '".$enlarged_blind_spot_od."' ,  '".$cecocentral_scotone_od."' , ".
				 "'".$central_scotoma_od."' ,  '".$hemianopsia_od."' ,  '".$quadrantanopsia_od."' , ".
				 "'".$congruent_od."' ,  '".$incongruent_od."' ,  '".$synthesis_od."', ". 
				 
				 "'".$details_high_os."' ,  '".$poor_study_os."' ,  '".$intratest_fluctuation_os."' , ".  
				 "'".$artifact_os."' ,  '".$details_lids_os."' ,  '".$normal_os."' , ".
				 "'".$nonspecific_os."' ,  '".$nasal_step_os."' ,  '".$arcuate_os."' , ".
				 "'".$hemifield_os."' ,  '".$paracentral_os."' ,  '".$into_fixation_os."' , ".
				 "'".$central_island_os."' ,  '".$enlarged_blind_spot_os."' ,  '".$cecocentral_scotone_os."' , ".
				 "'".$central_scotoma_os."' ,  '".$hemianopsia_os."' ,  '".$quadrantanopsia_os."' , ".
				 "'".$congruent_os."' ,  '".$incongruent_os."' ,  '".$synthesis_os."',  ". 				 
				 "'".$poor_study_od_desc."', '".$poor_study_od_desc_other."', '".$poor_study_os_desc."', '".$poor_study_os_desc_other."', ".
				 "'".$improve."' ,  '".$worse."', ".
				
				"'".$gla_mac_other_od."' ,  '".$gla_mac_os."' ,  '".$gla_mac_other_os."' ,  '".$diagnosis_os."' ,  '".$diagnosisOther_os."', ". 
				"'".$mdOd."' ,  '".$psdOd."' ,  '".$vfiOd."' ,  '".$mdOs."' ,  '".$psdOs."' ,  '".$vfiOs."', ". 
				"'".$homonomous_od."' ,  '".$homonomous_os."' ,  '".$interpretation_OD."' , '".$interpretation_OS."' , '".$comments_interp."', ". 
				"'".$glaucoma_stage_opt_OD."' , '".$glaucoma_stage_opt_OS."' , '".$plan."' ,  '".$repeatTestNxtVstEye."', ". 
				"'".$repeatTestVal1."' ,  '".$repeatTestVal2."' ,  '".$repeatTestEye."' ,  '".$comments_plan."', '".$testTime."', '".$forum_procedure."'".
				"".$signPathFieldValue.$signDTmFieldValue.
				
				 ")";
			$insertRes = imw_query($sql);
			$insertId = imw_insert_id();
			$vf_id = $insertId;
		}else if(!empty($vf_id)){
			$sql = "UPDATE vf_gl ".
				 "SET ".
				 "vf_gl = '".$vf."', ".
				 "vf_gl_sel = '".$vf_sel."', ".
				 "vf_gl_sel_other = '".$vf_sel_other."', ".
				 "vf_gl_eye = '".$vf_eye."', ".
				 "performedBy = '".$performedBy."', ".
				 "diagnosis = '".$diagnosis."', ".
				 "ptUnderstanding = '".$ptUnderstanding."', ".
				 "reliabilityOd = '".$reliabilityOd."', ".
				 "reliabilityOs = '".$reliabilityOs."', ".
				 "vf_gl_Od = '".$vfOd."', ".
				 "vf_gl_Os = '".$vfOs."', ".
				 "descOd = '".$descOd."', ".
				 "descOs = '".$descOs."', ".
				 "stable = '".$stable."', ".
				 "fuApa = '".$fuApa."', ".
				 "ptInformed = '".$ptInformed."', ".
				 "comments = '".$comments."', ".
				 "signature = '".$signature."', ";
				 
				if(!$zeissAction){  /*Prevent changing interpretation status if "Forum Add/Delete"*/
					$sql .= "phyName = '".$phyName."', ";
				}
				
			$sql .= "examDate = '".$examDate."', ".
				 //"patientId = '".$patientId."', ".				 
				 "monitorIOP = '".$monitorIOP."', ".
				 "diagnosisOther = '".$diagnosisOther."', ".
				 "tech2InformPt = '".$tech2InformPt."', ".
				 "techComments='".$elem_techComments."', ".
				 "ptInformedNv='".$ptInformedNv."', ".
				 "examTime = '".$examTime."', ".
				 "contiMeds = '".$contiMeds."', ".
				 "rptTst1yr = '".$rptTst1yr."', ".
				 "encounter_id='".$encounter_id."', ".
				 "ordrby='".$ordrby."', ".
				 "ordrdt='".$ordrdt."', ".
				 "elem_gla_mac='".$elem_gla_mac."', ".
				 
				 "details_high_od='".$details_high_od."' ,  
				 poor_study_od='".$poor_study_od."' ,  
				 intratest_fluctuation_od='".$intratest_fluctuation_od."' ,  
				 artifact_od='".$artifact_od."' ,  
				 details_lids_od='".$details_lids_od."' ,  
				 normal_od='".$normal_od."' ,  
				 nonspecific_od='".$nonspecific_od."' ,  
				 nasal_step_od='".$nasal_step_od."' ,  
				 arcuate_od='".$arcuate_od."' ,  
				 hemifield_od='".$hemifield_od."' ,  
				 paracentral_od='".$paracentral_od."' ,  
				 into_fixation_od='".$into_fixation_od."' ,  
				 central_island_od='".$central_island_od."' ,  
				 enlarged_blind_spot_od='".$enlarged_blind_spot_od."' ,  
				 cecocentral_scotone_od='".$cecocentral_scotone_od."' ,  
				 central_scotoma_od='".$central_scotoma_od."' ,   
				 hemianopsia_od='".$hemianopsia_od."' ,  
				 quadrantanopsia_od='".$quadrantanopsia_od."' ,  
				 congruent_od='".$congruent_od."' ,  
				 incongruent_od='".$incongruent_od."' ,  
				 synthesis_od='".$synthesis_od."' ,   ".
				 
				 "details_high_os='".$details_high_os."' ,  
				 poor_study_os='".$poor_study_os."' ,  
				 intratest_fluctuation_os='".$intratest_fluctuation_os."' ,  
				 artifact_os='".$artifact_os."' ,  
				 details_lids_os='".$details_lids_os."' ,  
				 normal_os='".$normal_os."' ,  
				 nonspecific_os='".$nonspecific_os."' ,  
				 nasal_step_os='".$nasal_step_os."' ,  
				 arcuate_os='".$arcuate_os."' ,  
				 hemifield_os='".$hemifield_os."' ,  
				 paracentral_os='".$paracentral_os."' ,  
				 into_fixation_os='".$into_fixation_os."' ,  
				 central_island_os='".$central_island_os."' ,  
				 enlarged_blind_spot_os='".$enlarged_blind_spot_os."' ,  
				 cecocentral_scotone_os='".$cecocentral_scotone_os."' ,  
				 central_scotoma_os='".$central_scotoma_os."' ,   
				 hemianopsia_os='".$hemianopsia_os."' ,  
				 quadrantanopsia_os='".$quadrantanopsia_os."' ,  
				 congruent_os='".$congruent_os."' ,  
				 incongruent_os='".$incongruent_os."' ,  
				 synthesis_os='".$synthesis_os."' ,   ".				 
				 
				 "poor_study_od_desc = '".$poor_study_od_desc."', 
				 poor_study_od_desc_other = '".$poor_study_od_desc_other."', 
				 poor_study_os_desc = '".$poor_study_os_desc."', 
				 poor_study_os_desc_other = '".$poor_study_os_desc_other."', ".
				 
				 "improve='".$improve."' ,  
				 worse='".$worse."',   ".				 
				 
				 "gla_mac_other_od = '".$gla_mac_other_od."' , 
				 gla_mac_os= '".$gla_mac_os."' ,  
				 gla_mac_other_os='".$gla_mac_other_os."' , 
				 diagnosis_os= '".$diagnosis_os."' , 
				 diagnosisOther_os= '".$diagnosisOther_os."', ". 
				"mdOd='".$mdOd."' ,  psdOd='".$psdOd."' , vfiOd= '".$vfiOd."' ,  mdOs='".$mdOs."' , psdOs= '".$psdOs."' , vfiOs= '".$vfiOs."', ". 
				"homonomous_od = '".$homonomous_od."' ,  homonomous_os = '".$homonomous_os."' ,  interpretation_OD = '".$interpretation_OD."' , interpretation_OS = '".$interpretation_OS."' , comments_interp = '".$comments_interp."', ". 
				"glaucoma_stage_opt_OD = '".$glaucoma_stage_opt_OD."' ,". 
				"glaucoma_stage_opt_OS = '".$glaucoma_stage_opt_OS."' , plan=  '".$plan."' , repeatTestNxtVstEye = '".$repeatTestNxtVstEye."', ". 
				$signPathQry.
  			 	$signDTmQry.
				"repeatTestVal1 = '".$repeatTestVal1."' , repeatTestVal2 =  '".$repeatTestVal2."' ,  repeatTestEye = '".$repeatTestEye."' , comments_plan = '".$comments_plan."', ".				 
				"forum_procedure = '".$forum_procedure."' ".
				 //"WHERE formId = '".$formId."' ";
				 "WHERE vf_gl_id = '".$vf_id."' ";
			$res = sqlQuery($sql);
		}
		
		/*code, Purpose: Generate HL7 message for Test*/
		$zeissMsgType="VF-GL";
		$zeissPatientId = $patientId;
		$zeissTestId = $vf_id;
		include("./zeissTestHl7.php");
		/*End code*/
		
		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "VF-GL", $vf_id);
		/*--interpretation end--*/
		//Super-Bill -----------
		if(!empty($ordrby) && !empty($vf_id)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$vf_id;
			$arIn["sb_testName"]="VF-GL";
                        $arIn["form_id"]=$form_id;
                        $arIn["test_interpreted"]= ((int)$phyName > 0)? true : false;

			$oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);
		}

		//Super-Bill -----------

		//Test Pdf ---
		$objTests->saveTestPdfExe_2($patientId,$vf_id,"VF-GL");
		//Test Pdf ---
		
		//set Glaucoma Stage --
		$objTests->setGlucomaStageGFS($glaucoma_stage_opt_OD , $glaucoma_stage_opt_OS);
		//set Glaucoma Stage --
		
		//FormId updates other tables -----
		if(!empty($formId)){
			// Make chart notes valid
			$oChartNote->makeChartNotesValid($formId);
		}

		echo "<script>".
				"try{";
					if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
						$afterSaveinPopup = 'yes';
					}else{
						$afterSaveinPopup = 'no';
						echo "top.alert_notification_show(\"Test has been saved.\");";
					}
					echo "
					window.location.replace(\"test_vf_gl.php?noP=".$elem_noP."&tId=".$vf_id."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
				}catch(err){}".
			 "</script>";
		exit();
		break;
	}
	case "VF":{
		$patientId = $_POST["elem_patientId"];
		$formId = $_POST["elem_formId"];
		$hd_vf_mode = $_POST["hd_vf_mode"];
		$vf_id = $_POST["elem_vfId"];
		$examDate =  getDateFormatDB($_POST["elem_examDate"]);
		if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
		$vf = $_POST["elem_vf"];
		$vf_sel = $_POST["elem_vfSel"];
		$vf_sel_other = $_POST["elem_vfSelOther"];
		$vf_eye = $_POST["elem_vfEye"];
		$performedBy = $_POST["elem_performedBy"];
		$ptUnderstanding = $_POST["elem_ptUnderstanding"];
		$diagnosis = imw_real_escape_string($_POST["elem_diagnosis"]);
		$diagnosisOther = imw_real_escape_string($_POST["elem_diagnosisOther"]);
		$reliabilityOd = $_POST["elem_reliabilityOd"];
		$reliabilityOs = $_POST["elem_reliabilityOs"];
		//$vfOd = $_POST["elem_vfOd"];
		//$descOd = $_POST["elem_descOd"];
		//$vfOs = $_POST["elem_vfOs"];
		//$descOs = $_POST["elem_descOs"];
		$stable = $_POST["elem_stable"];
		$monitorIOP = $_POST["elem_monitorIOP"];
		$fuApa = $_POST["elem_fuApa"];
		$ptInformed = $_POST["elem_ptInformed"];
		$comments = addslashes($_POST["elem_comments"]);
		$signature = $_POST["elem_vfSign"];
		$phyName = $_POST["elem_physician"];
		$tech2InformPt = $_POST["elem_tech2InformPt"];
		$elem_trgtIopOd = imw_real_escape_string($_POST["elem_targetIop_OD"]);
		$elem_trgtIopOs = imw_real_escape_string($_POST["elem_targetIop_OS"]);
		$elem_noP = $_POST["elem_noP"];
		$examTime = $_POST["elem_examTime"];
		$contiMeds = $_POST["elem_contiMeds"];
		$rptTst1yr = $_POST["elem_rptTst1yr"];

		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
		
		$forum_procedure = (isset($_REQUEST['forum_procedure']))?imw_real_escape_string($_REQUEST['forum_procedure']):"";
		$zeissAction = (isset($_REQUEST['forum_procedure']))?$_POST['zeissAction']:false;
		
		$loss_sup_degree_od = $_POST["elem_loss_sup_degree_od"];
		$loss_sup_degree_os= $_POST["elem_loss_sup_degree_os"];
		$improve_degree_od = $_POST["elem_improve_degree_od"];
		$improve_degree_os = $_POST["elem_improve_degree_os"];

		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
			$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
			$encounter_id = $objTests->getEncounterId();
		}

		//Summary
		$summaryOd=$summaryOs="";

		$Normal_OD_T = $_POST["Normal_OD_T"];
		$Normal_OD_1 = $_POST["Normal_OD_1"];
		$Normal_OD_2 = $_POST["Normal_OD_2"];
		$Normal_OD_3 = $_POST["Normal_OD_3"];
		$Normal_OD_4 = $_POST["Normal_OD_4"];
		$Normal_OD_PoorStudy = $_POST["elem_normal_poorStudy_od"];
		$arr = array(""=>$Normal_OD_T, "Poor Study"=>$Normal_OD_PoorStudy);
		$summaryOd .= $objTests->getTestSumm($arr,"Normal");

		$BorderLineDefect_OD_T = $_POST["BorderLineDefect_OD_T"];
		$BorderLineDefect_OD_1 = $_POST["BorderLineDefect_OD_1"];
		$BorderLineDefect_OD_2 = $_POST["BorderLineDefect_OD_2"];
		$BorderLineDefect_OD_3 = $_POST["BorderLineDefect_OD_3"];
		$BorderLineDefect_OD_4 = $_POST["BorderLineDefect_OD_4"];
		$arr = array("T"=>$BorderLineDefect_OD_T,"+1"=>$BorderLineDefect_OD_1,"+2"=>$BorderLineDefect_OD_2,"+3"=>$BorderLineDefect_OD_3,"+4"=>$BorderLineDefect_OD_4);
		$summaryOd .= $objTests->getTestSumm($arr,"Border Line Defect");

		$Abnormal_OD_T = $_POST["Abnormal_OD_T"];
		$Abnormal_OD_1 = $_POST["Abnormal_OD_1"];
		$Abnormal_OD_2 = $_POST["Abnormal_OD_2"];
		$Abnormal_OD_3 = $_POST["Abnormal_OD_3"];
		$Abnormal_OD_4 = $_POST["Abnormal_OD_4"];
		$arr = array("T"=>$Abnormal_OD_T,"+1"=>$Abnormal_OD_1,"+2"=>$Abnormal_OD_2,"+3"=>$Abnormal_OD_3,"+4"=>$Abnormal_OD_4);
		$summaryOd .= $objTests->getTestSumm($arr,"Abnormal");

		$NasalSteep_OD_Superior = $_POST["NasalSteep_OD_Superior"];
		$NasalSteep_OD_Inferior = $_POST["NasalSteep_OD_Inferior"];
		$NasalSteep_OD_S_T = $_POST["NasalSteep_OD_S_T"];
		$NasalSteep_OD_S_1 = $_POST["NasalSteep_OD_S_1"];
		$NasalSteep_OD_S_2 = $_POST["NasalSteep_OD_S_2"];
		$NasalSteep_OD_S_3 = $_POST["NasalSteep_OD_S_3"];
		$NasalSteep_OD_S_4 = $_POST["NasalSteep_OD_S_4"];
		$NasalSteep_OD_I_T = $_POST["NasalSteep_OD_I_T"];
		$NasalSteep_OD_I_1 = $_POST["NasalSteep_OD_I_1"];
		$NasalSteep_OD_I_2 = $_POST["NasalSteep_OD_I_2"];
		$NasalSteep_OD_I_3 = $_POST["NasalSteep_OD_I_3"];
		$NasalSteep_OD_I_4 = $_POST["NasalSteep_OD_I_4"];
		$arr = array("Superior"=>$NasalSteep_OD_Superior, "T"=>$NasalSteep_OD_S_T,"+1"=>$NasalSteep_OD_S_1,
				 "+2"=>$NasalSteep_OD_S_2,"+3"=>$NasalSteep_OD_S_3,"+4"=>$NasalSteep_OD_S_4,
				 "Inferior"=>$NasalSteep_OD_Inferior,"T "=>$NasalSteep_OD_I_T,"+1 "=>$NasalSteep_OD_I_1,
				 "+2 "=>$NasalSteep_OD_I_2,"+3 "=>$NasalSteep_OD_I_3,"+4 "=>$NasalSteep_OD_I_4 );
		$summaryOd .= $objTests->getTestSumm($arr,"Nasal Step");

		$Arcuatedefect_OD_Superior = $_POST["Arcuatedefect_OD_Superior"];
		$Arcuatedefect_OD_S_T = $_POST["Arcuatedefect_OD_S_T"];
		$Arcuatedefect_OD_S_1 = $_POST["Arcuatedefect_OD_S_1"];
		$Arcuatedefect_OD_S_2 = $_POST["Arcuatedefect_OD_S_2"];
		$Arcuatedefect_OD_S_3 = $_POST["Arcuatedefect_OD_S_3"];
		$Arcuatedefect_OD_S_4 = $_POST["Arcuatedefect_OD_S_4"];
		$Arcuatedefect_OD_Inferior = $_POST["Arcuatedefect_OD_Inferior"];
		$Arcuatedefect_OD_I_T = $_POST["Arcuatedefect_OD_I_T"];
		$Arcuatedefect_OD_I_1 = $_POST["Arcuatedefect_OD_I_1"];
		$Arcuatedefect_OD_I_2 = $_POST["Arcuatedefect_OD_I_2"];
		$Arcuatedefect_OD_I_3 = $_POST["Arcuatedefect_OD_I_3"];
		$Arcuatedefect_OD_I_4 = $_POST["Arcuatedefect_OD_I_4"];
		$arr = array("Superior"=>$Arcuatedefect_OD_Superior, "T"=>$Arcuatedefect_OD_S_T,"+1"=>$Arcuatedefect_OD_S_1,
				 "+2"=>$Arcuatedefect_OD_S_2,"+3"=>$Arcuatedefect_OD_S_3,"+4"=>$Arcuatedefect_OD_S_4,
				 "Inferior"=>$Arcuatedefect_OD_Inferior,"T "=>$Arcuatedefect_OD_I_T,"+1 "=>$Arcuatedefect_OD_I_1,
				 "+2 "=>$Arcuatedefect_OD_I_2,"+3 "=>$Arcuatedefect_OD_I_3,"+4 "=>$Arcuatedefect_OD_I_4 );
		$summaryOd .= $objTests->getTestSumm($arr,"Arcuate defect");

		$Defect_OD_Central = $_POST["Defect_OD_Central"];
		$Defect_OD_Superior = $_POST["Defect_OD_Superior"];
		$Defect_OD_Inferior = $_POST["Defect_OD_Inferior"];
		$Defect_OD_Scattered = $_POST["Defect_OD_Scattered"];
		$Defect_OD_T = $_POST["Defect_OD_T"];
		$Defect_OD_1 = $_POST["Defect_OD_1"];
		$Defect_OD_2 = $_POST["Defect_OD_2"];
		$Defect_OD_3 = $_POST["Defect_OD_3"];
		$Defect_OD_4 = $_POST["Defect_OD_4"];
		$arr = array("Central"=>$Defect_OD_Central,"Superior"=>$Defect_OD_Superior,
				"Inferior"=>$Defect_OD_Inferior,"Scattered"=>$Defect_OD_Scattered,
				"T"=>$Defect_OD_T,"+1"=>$Defect_OD_1,"+2"=>$Defect_OD_2,"+3"=>$Defect_OD_3,"+4"=>$Defect_OD_4);
		$summaryOd .= $objTests->getTestSumm($arr,"Defect");

		$blindSpot_OD_T = $_POST["blindSpot_OD_T"];
		$blindSpot_OD_1 = $_POST["blindSpot_OD_1"];
		$blindSpot_OD_2 = $_POST["blindSpot_OD_2"];
		$blindSpot_OD_3 = $_POST["blindSpot_OD_3"];
		$blindSpot_OD_4 = $_POST["blindSpot_OD_4"];
		$arr = array("T"=>$blindSpot_OD_T,"+1"=>$blindSpot_OD_1,"+2"=>$blindSpot_OD_2,"+3"=>$blindSpot_OD_3,"+4"=>$blindSpot_OD_4);
		$summaryOd .= $objTests->getTestSumm($arr,"Increase size of Blind spot");

		$NoSigChange_OD = $_POST["elem_noSigChange_OD"];
		$arr = array("" => $NoSigChange_OD);
		$summaryOd .= $objTests->getTestSumm($arr, "No Significant Change");

		$Improved_OD = $_POST["elem_improved_OD"];
		$arr = array("" => $Improved_OD);
		$summaryOd .= $objTests->getTestSumm($arr, "Improved");

		$IncAbn_OD = $_POST["elem_incAbn_OD"];
		$arr = array("" => $IncAbn_OD);
		$summaryOd .= $objTests->getTestSumm($arr, "Increase Abnormal");

		$Others_OD = addslashes($_POST["Others_OD"]);
		$arr = array($Others_OD=>$Others_OD);
		$summaryOd .= $objTests->getTestSumm($arr,"Other");

		$Normal_OS_T = $_POST["Normal_OS_T"];
		$Normal_OS_1 = $_POST["Normal_OS_1"];
		$Normal_OS_2 = $_POST["Normal_OS_2"];
		$Normal_OS_3 = $_POST["Normal_OS_3"];
		$Normal_OS_4 = $_POST["Normal_OS_4"];
		$Normal_OS_PoorStudy = $_POST["elem_normal_poorStudy_os"];

		$arr = array(""=>$Normal_OS_T,"Poor Study" =>$Normal_OS_PoorStudy);
		$summaryOs .= $objTests->getTestSumm($arr,"Normal");

		$BorderLineDefect_OS_T = $_POST["BorderLineDefect_OS_T"];
		$BorderLineDefect_OS_1 = $_POST["BorderLineDefect_OS_1"];
		$BorderLineDefect_OS_2 = $_POST["BorderLineDefect_OS_2"];
		$BorderLineDefect_OS_3 = $_POST["BorderLineDefect_OS_3"];
		$BorderLineDefect_OS_4 = $_POST["BorderLineDefect_OS_4"];
		$arr = array("T"=>$BorderLineDefect_OS_T,"+1"=>$BorderLineDefect_OS_1,"+2"=>$BorderLineDefect_OS_2,
				"+3"=>$BorderLineDefect_OS_3,"+4"=>$BorderLineDefect_OS_4);
		$summaryOs .= $objTests->getTestSumm($arr,"Border Line Defect");

		$Abnormal_OS_T = $_POST["Abnormal_OS_T"];
		$Abnormal_OS_1 = $_POST["Abnormal_OS_1"];
		$Abnormal_OS_2 = $_POST["Abnormal_OS_2"];
		$Abnormal_OS_3 = $_POST["Abnormal_OS_3"];
		$Abnormal_OS_4 = $_POST["Abnormal_OS_4"];
		$arr = array("T"=>$Abnormal_OS_T,"+1"=>$Abnormal_OS_1,"+2"=>$Abnormal_OS_2,
				"+3"=>$Abnormal_OS_3,"+4"=>$Abnormal_OS_4);
		$summaryOs .= $objTests->getTestSumm($arr,"Abnormal");

		$NasalSteep_OS_Superior = $_POST["NasalSteep_OS_Superior"];
		$NasalSteep_OS_Inferior = $_POST["NasalSteep_OS_Inferior"];
		$NasalSteep_OS_S_T = $_POST["NasalSteep_OS_S_T"];
		$NasalSteep_OS_S_1 = $_POST["NasalSteep_OS_S_1"];
		$NasalSteep_OS_S_2 = $_POST["NasalSteep_OS_S_2"];
		$NasalSteep_OS_S_3 = $_POST["NasalSteep_OS_S_3"];
		$NasalSteep_OS_S_4 = $_POST["NasalSteep_OS_S_4"];
		$NasalSteep_OS_I_T = $_POST["NasalSteep_OS_I_T"];
		$NasalSteep_OS_I_1 = $_POST["NasalSteep_OS_I_1"];
		$NasalSteep_OS_I_2 = $_POST["NasalSteep_OS_I_2"];
		$NasalSteep_OS_I_3 = $_POST["NasalSteep_OS_I_3"];
		$NasalSteep_OS_I_4 = $_POST["NasalSteep_OS_I_4"];
		$arr = array("Superior"=>$NasalSteep_OS_Superior, "T"=>$NasalSteep_OS_S_T,"+1"=>$NasalSteep_OS_S_1,
				 "+2"=>$NasalSteep_OS_S_2,"+3"=>$NasalSteep_OS_S_3,"+4"=>$NasalSteep_OS_S_4,
				 "Inferior"=>$NasalSteep_OS_Inferior,"T "=>$NasalSteep_OS_I_T,"+1 "=>$NasalSteep_OS_I_1,
				 "+2 "=>$NasalSteep_OS_I_2,"+3 "=>$NasalSteep_OS_I_3,"+4 "=>$NasalSteep_OS_I_4 );
		$summaryOs .= $objTests->getTestSumm($arr,"Nasal Step");

		$Arcuatedefect_OS_Superior = $_POST["Arcuatedefect_OS_Superior"];
		$Arcuatedefect_OS_S_T = $_POST["Arcuatedefect_OS_S_T"];
		$Arcuatedefect_OS_S_1 = $_POST["Arcuatedefect_OS_S_1"];
		$Arcuatedefect_OS_S_2 = $_POST["Arcuatedefect_OS_S_2"];
		$Arcuatedefect_OS_S_3 = $_POST["Arcuatedefect_OS_S_3"];
		$Arcuatedefect_OS_S_4 = $_POST["Arcuatedefect_OS_S_4"];
		$Arcuatedefect_OS_Inferior = $_POST["Arcuatedefect_OS_Inferior"];
		$Arcuatedefect_OS_I_T = $_POST["Arcuatedefect_OS_I_T"];
		$Arcuatedefect_OS_I_1 = $_POST["Arcuatedefect_OS_I_1"];
		$Arcuatedefect_OS_I_2 = $_POST["Arcuatedefect_OS_I_2"];
		$Arcuatedefect_OS_I_3 = $_POST["Arcuatedefect_OS_I_3"];
		$Arcuatedefect_OS_I_4 = $_POST["Arcuatedefect_OS_I_4"];
		$arr = array("Superior"=>$Arcuatedefect_OS_Superior, "T"=>$Arcuatedefect_OS_S_T,"+1"=>$Arcuatedefect_OS_S_1,
				 "+2"=>$Arcuatedefect_OS_S_2,"+3"=>$Arcuatedefect_OS_S_3,"+4"=>$Arcuatedefect_OS_S_4,
				 "Inferior"=>$Arcuatedefect_OS_Inferior,"T "=>$Arcuatedefect_OS_I_T,"+1 "=>$Arcuatedefect_OS_I_1,
				 "+2 "=>$Arcuatedefect_OS_I_2,"+3 "=>$Arcuatedefect_OS_I_3,"+4 "=>$Arcuatedefect_OS_I_4 );
		$summaryOs .= $objTests->getTestSumm($arr,"Arcuate defect");

		$Defect_OS_Central = $_POST["Defect_OS_Central"];
		$Defect_OS_Superior = $_POST["Defect_OS_Superior"];
		$Defect_OS_Inferior = $_POST["Defect_OS_Inferior"];
		$Defect_OS_Scattered = $_POST["Defect_OS_Scattered"];
		$Defect_OS_T = $_POST["Defect_OS_T"];
		$Defect_OS_1 = $_POST["Defect_OS_1"];
		$Defect_OS_2 = $_POST["Defect_OS_2"];
		$Defect_OS_3 = $_POST["Defect_OS_3"];
		$Defect_OS_4 = $_POST["Defect_OS_4"];
		$arr = array("Central"=>$Defect_OS_Central,"Superior"=>$Defect_OS_Superior,
				"Inferior"=>$Defect_OS_Inferior,"Scattered"=>$Defect_OS_Scattered,
				"T"=>$Defect_OS_T,"+1"=>$Defect_OS_1,"+2"=>$Defect_OS_2,"+3"=>$Defect_OS_3,"+4"=>$Defect_OS_4);
		$summaryOs .= $objTests->getTestSumm($arr,"Defect");

		$blindSpot_OS_T = $_POST["blindSpot_OS_T"];
		$blindSpot_OS_1 = $_POST["blindSpot_OS_1"];
		$blindSpot_OS_2 = $_POST["blindSpot_OS_2"];
		$blindSpot_OS_3 = $_POST["blindSpot_OS_3"];
		$blindSpot_OS_4 = $_POST["blindSpot_OS_4"];
		$arr = array("T"=>$blindSpot_OS_T,"+1"=>$blindSpot_OS_1,"+2"=>$blindSpot_OS_2,"+3"=>$blindSpot_OS_3,"+4"=>$blindSpot_OS_4);
		$summaryOs .= $objTests->getTestSumm($arr,"Increase size of Blind spot");

		$NoSigChange_OS = $_POST["elem_noSigChange_OS"];
		$arr = array("" => $NoSigChange_OS);
		$summaryOs .= $objTests->getTestSumm($arr, "No Significant Change");

		$Improved_OS = $_POST["elem_improved_OS"];
		$arr = array("" => $Improved_OS);
		$summaryOs .= $objTests->getTestSumm($arr, "Improved");

		$IncAbn_OS = $_POST["elem_incAbn_OS"];
		$arr = array("" => $IncAbn_OS);
		$summaryOs .= $objTests->getTestSumm($arr, "Increase Abnormal");

		$Others_OS = addslashes($_POST["Others_OS"]);
		$arr = array($Others_OS=>$Others_OS);
		$summaryOs .= $objTests->getTestSumm($arr,"Other");

		$descOd = (!empty($summaryOd)) ? $summaryOd : "";
		$descOs = (!empty($summaryOs)) ? $summaryOs : "";
		$ptInformedNv = $_POST["elem_informedPtNv"]; // Pt informed Next visit.
		
		$elem_gla_mac = $_POST["elem_gla_mac"]; 

		//Tech Comments
		if($_POST["techComments"] == 'Technician Comments'){
			$_POST["techComments"] = '';
		}
		$elem_techComments = addslashes($_POST['techComments']);
		
		if(isset($_POST["elem_tapeuntaped_od_taped"])) {
			$tapeuntaped_od = $_POST["elem_tapeuntaped_od_taped"]; 
		}else if(isset($_POST["elem_tapeuntaped_od_untaped"])) {
			$tapeuntaped_od = $_POST["elem_tapeuntaped_od_untaped"]; 
		}
		
		if(isset($_POST["elem_tapeuntaped_os_taped"])) {
			$tapeuntaped_os = $_POST["elem_tapeuntaped_os_taped"]; 
		}else if(isset($_POST["elem_tapeuntaped_os_untaped"])) {
			$tapeuntaped_os = $_POST["elem_tapeuntaped_os_untaped"]; 
		}		
		//check
		if(empty($vf_id)){
			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;
			if(isset($arrTest2edit["VF"]) && !empty($arrTest2edit["VF"])){
				//Check in db
				$cQry = "select vf_id FROM vf
						WHERE patientId ='".$patientId."'
						AND vf_id = '".$arrTest2edit["VF"]."' ";
				$row = sqlQuery($cQry);
				$vf_id = (($row == false) || empty($row["vf_id"])) ? "0" : $row["vf_id"];

				//Unset Session VF
				$arrTest2edit["VF"] = "";
				$arrTest2edit["VF"] = NULL;
				unset($arrTest2edit["VF"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);
			}else{
				$cQry = "select vf_id FROM vf
						WHERE patientId ='".$patientId."'
						AND examDate = '".$examDate."' AND examTime = '".$examTime."' ";
				$row = sqlQuery($cQry);
				$vf_id = (($row == false) || empty($row["vf_id"])) ? "0" : $row["vf_id"];
			}
		}
		if(empty($vf_id)){
			$phyName=($zeissAction)?0:$phyName; /*Mark test uninterpreted if "Forum Add"*/
			$sql = "INSERT INTO vf ".
				 "( ".
				 "vf_id, vf, vf_sel, vf_sel_other, vf_eye, performedBy, diagnosis, ".
				 "ptUnderstanding, reliabilityOd, reliabilityOs, vfOd, vfOs, descOd, descOs, ".
				 "stable, fuApa, ptInformed, comments, signature, phyName, examDate, ".
				 "Normal_OD_T, Normal_OD_1, Normal_OD_2, Normal_OD_3, Normal_OD_4, ".
				 "BorderLineDefect_OD_T, BorderLineDefect_OD_1, BorderLineDefect_OD_2, BorderLineDefect_OD_3, BorderLineDefect_OD_4, ".
				 "Abnormal_OD_T, Abnormal_OD_1, Abnormal_OD_2, Abnormal_OD_3, Abnormal_OD_4, ".
				 "NasalSteep_OD_Superior, NasalSteep_OD_Inferior, ".
				 "NasalSteep_OD_S_T, NasalSteep_OD_S_1, NasalSteep_OD_S_2, NasalSteep_OD_S_3, NasalSteep_OD_S_4, ".
				 "NasalSteep_OD_I_T, NasalSteep_OD_I_1, NasalSteep_OD_I_2, NasalSteep_OD_I_3, NasalSteep_OD_I_4, ".
				 "Arcuatedefect_OD_Superior, Arcuatedefect_OD_Inferior, ".
				 "Arcuatedefect_OD_S_T, Arcuatedefect_OD_S_1, Arcuatedefect_OD_S_2, Arcuatedefect_OD_S_3, Arcuatedefect_OD_S_4, ".
				 "Arcuatedefect_OD_I_T, Arcuatedefect_OD_I_1, Arcuatedefect_OD_I_2, Arcuatedefect_OD_I_3, Arcuatedefect_OD_I_4, ".
				 "Defect_OD_Central, Defect_OD_Superior, Defect_OD_Inferior, Defect_OD_Scattered, Others_OD, ".
				 "blindSpot_OD_T, blindSpot_OD_1, blindSpot_OD_2, blindSpot_OD_3, blindSpot_OD_4, ".
				 "Normal_OS_T, Normal_OS_1, Normal_OS_2, Normal_OS_3, Normal_OS_4, ".
				 "BorderLineDefect_OS_T, BorderLineDefect_OS_1, BorderLineDefect_OS_2, BorderLineDefect_OS_3, BorderLineDefect_OS_4, ".
				 "Abnormal_OS_T, Abnormal_OS_1, Abnormal_OS_2, Abnormal_OS_3, Abnormal_OS_4, ".
				 "NasalSteep_OS_Superior, NasalSteep_OS_Inferior, ".
				 "NasalSteep_OS_S_T, NasalSteep_OS_S_1, NasalSteep_OS_S_2, NasalSteep_OS_S_3, NasalSteep_OS_S_4, ".
				 "NasalSteep_OS_I_T, NasalSteep_OS_I_1, NasalSteep_OS_I_2, NasalSteep_OS_I_3, NasalSteep_OS_I_4, ".
				 "Arcuatedefect_OS_Superior, Arcuatedefect_OS_Inferior, ".
				 "Arcuatedefect_OS_S_T, Arcuatedefect_OS_S_1, Arcuatedefect_OS_S_2, Arcuatedefect_OS_S_3, Arcuatedefect_OS_S_4, ".
				 "Arcuatedefect_OS_I_T, Arcuatedefect_OS_I_1, Arcuatedefect_OS_I_2, Arcuatedefect_OS_I_3, Arcuatedefect_OS_I_4, ".
				 "Defect_OS_Central, Defect_OS_Superior, Defect_OS_Inferior, Defect_OS_Scattered, Others_OS, ".
				 "blindSpot_OS_T, blindSpot_OS_1, blindSpot_OS_2, blindSpot_OS_3, blindSpot_OS_4, ".
				 "Defect_OD_T, Defect_OD_1, Defect_OD_2, Defect_OD_3, Defect_OD_4, ".
				 "Defect_OS_T, Defect_OS_1, Defect_OS_2, Defect_OS_3, Defect_OS_4, ".
				 "patientId, formId, diagnosisOther, monitorIOP,tech2InformPt, ".
				 "Normal_OD_PoorStudy, Normal_OS_PoorStudy, ".
				 "NoSigChange_OD, Improved_OD, IncAbn_OD, ".
				 "NoSigChange_OS, Improved_OS, IncAbn_OS, ".
				 "iopTrgtOd, iopTrgtOs, techComments, ptInformedNv, ".
				 "examTime,contiMeds,rptTst1yr, ".
				 "encounter_id,ordrby,ordrdt, elem_gla_mac, ".
				 "tapeuntaped_od, tapeuntaped_os, ".
				 "loss_sup_degree_od, loss_sup_degree_os, ".
				 "improve_degree_od, improve_degree_os, forum_procedure ".
				 $signPathFieldName.$signDTmFieldName.				 
				 ") ".
				 "VALUES ".
				 "( ".
				 "NULL, '".$vf."', '".$vf_sel."', '".$vf_sel_other."', '".$vf_eye."', '".$performedBy."', '".$diagnosis."', ".
				 "'".$ptUnderstanding."', '".$reliabilityOd."', '".$reliabilityOs."', '".$vfOd."', '".$vfOs."', '".$descOd."', '".$descOs."', ".
				 "'".$stable."', '".$fuApa."', '".$ptInformed."', '".$comments."', '".$signature."', '".$phyName."', '".$examDate."', ".
				 "'".$Normal_OD_T."', '".$Normal_OD_1."', '".$Normal_OD_2."', '".$Normal_OD_3."', '".$Normal_OD_4."', ".
				 "'".$BorderLineDefect_OD_T."', '".$BorderLineDefect_OD_1."', '".$BorderLineDefect_OD_2."', '".$BorderLineDefect_OD_3."', '".$BorderLineDefect_OD_4."', ".
				 "'".$Abnormal_OD_T."', '".$Abnormal_OD_1."', '".$Abnormal_OD_2."', '".$Abnormal_OD_3."', '".$Abnormal_OD_4."', ".
				 "'".$NasalSteep_OD_Superior."', '".$NasalSteep_OD_Inferior."', ".
				 "'".$NasalSteep_OD_S_T."', '".$NasalSteep_OD_S_1."', '".$NasalSteep_OD_S_2."', '".$NasalSteep_OD_S_3."', '".$NasalSteep_OD_S_4."', ".
				 "'".$NasalSteep_OD_I_T."', '".$NasalSteep_OD_I_1."', '".$NasalSteep_OD_I_2."', '".$NasalSteep_OD_I_3."', '".$NasalSteep_OD_I_4."', ".
				 "'".$Arcuatedefect_OD_Superior."', '".$Arcuatedefect_OD_Inferior."', ".
				 "'".$Arcuatedefect_OD_S_T."', '".$Arcuatedefect_OD_S_1."', '".$Arcuatedefect_OD_S_2."', '".$Arcuatedefect_OD_S_3."', '".$Arcuatedefect_OD_S_4."', ".
				 "'".$Arcuatedefect_OD_I_T."', '".$Arcuatedefect_OD_I_1."', '".$Arcuatedefect_OD_I_2."', '".$Arcuatedefect_OD_I_3."', '".$Arcuatedefect_OD_I_4."', ".
				 "'".$Defect_OD_Central."', '".$Defect_OD_Superior."', '".$Defect_OD_Inferior."', '".$Defect_OD_Scattered."', '".$Others_OD."', ".
				 "'".$blindSpot_OD_T."', '".$blindSpot_OD_1."', '".$blindSpot_OD_2."', '".$blindSpot_OD_3."', '".$blindSpot_OD_4."', ".
				 "'".$Normal_OS_T."', '".$Normal_OS_1."', '".$Normal_OS_2."', '".$Normal_OS_3."', '".$Normal_OS_4."', ".
				 "'".$BorderLineDefect_OS_T."', '".$BorderLineDefect_OS_1."', '".$BorderLineDefect_OS_2."', '".$BorderLineDefect_OS_3."', '".$BorderLineDefect_OS_4."', ".
				 "'".$Abnormal_OS_T."', '".$Abnormal_OS_1."', '".$Abnormal_OS_2."', '".$Abnormal_OS_3."', '".$Abnormal_OS_4."', ".
				 "'".$NasalSteep_OS_Superior."', '".$NasalSteep_OS_Inferior."', ".
				 "'".$NasalSteep_OS_S_T."', '".$NasalSteep_OS_S_1."', '".$NasalSteep_OS_S_2."', '".$NasalSteep_OS_S_3."', '".$NasalSteep_OS_S_4."', ".
				 "'".$NasalSteep_OS_I_T."', '".$NasalSteep_OS_I_1."', '".$NasalSteep_OS_I_2."', '".$NasalSteep_OS_I_3."', '".$NasalSteep_OS_I_4."', ".
				 "'".$Arcuatedefect_OS_Superior."', '".$Arcuatedefect_OS_Inferior."', ".
				 "'".$Arcuatedefect_OS_S_T."', '".$Arcuatedefect_OS_S_1."', '".$Arcuatedefect_OS_S_2."', '".$Arcuatedefect_OS_S_3."', '".$Arcuatedefect_OS_S_4."', ".
				 "'".$Arcuatedefect_OS_I_T."', '".$Arcuatedefect_OS_I_1."', '".$Arcuatedefect_OS_I_2."', '".$Arcuatedefect_OS_I_3."', '".$Arcuatedefect_OS_I_4."', ".
				 "'".$Defect_OS_Central."', '".$Defect_OS_Superior."', '".$Defect_OS_Inferior."', '".$Defect_OS_Scattered."', '".$Others_OS."', ".
				 "'".$blindSpot_OS_T."', '".$blindSpot_OS_1."', '".$blindSpot_OS_2."', '".$blindSpot_OS_3."', '".$blindSpot_OS_4."', ".
				 "'".$Defect_OD_T."', '".$Defect_OD_1."', '".$Defect_OD_2."', '".$Defect_OD_3."', '".$Defect_OD_4."', ".
				 "'".$Defect_OS_T."', '".$Defect_OS_1."', '".$Defect_OS_2."', '".$Defect_OS_3."', '".$Defect_OS_4."', ".
				 "'".$patientId."', '".$formId."', '".$diagnosisOther."', '".$monitorIOP."', '".$tech2InformPt."', ".
				 "'".$Normal_OD_PoorStudy."', '".$Normal_OS_PoorStudy."', ".
				 "'".$NoSigChange_OD."', '".$Improved_OD."', '".$IncAbn_OD."', ".
				 "'".$NoSigChange_OS."', '".$Improved_OS."', '".$IncAbn_OS."', ".
				 "'".$elem_trgtIopOd."', '".$elem_trgtIopOd."','".$elem_techComments."', '".$ptInformedNv."', ".
				 "'".$examTime."','".$contiMeds."', '".$rptTst1yr."', ".
				 "'".$encounter_id."','".$ordrby."','".$ordrdt."','".$elem_gla_mac."', ".
				 "'".$tapeuntaped_od."', '".$tapeuntaped_os."', ".
				 "'".$loss_sup_degree_od."', '".$loss_sup_degree_os."', ".
				 "'".$improve_degree_od."', '".$improve_degree_os."', '".$forum_procedure."' ".
				 $signPathFieldValue.$signDTmFieldValue.				 
				 ")";
			$insertres = imw_query($sql);
			$insertId = imw_insert_id();
			$vf_id = $insertId;
		}else if(!empty($vf_id)){
			$sql = "UPDATE vf ".
				 "SET ".
				 "vf = '".$vf."', ".
				 "vf_sel = '".$vf_sel."', ".
				 "vf_sel_other = '".$vf_sel_other."', ".
				 "vf_eye = '".$vf_eye."', ".
				 "performedBy = '".$performedBy."', ".
				 "diagnosis = '".$diagnosis."', ".
				 "ptUnderstanding = '".$ptUnderstanding."', ".
				 "reliabilityOd = '".$reliabilityOd."', ".
				 "reliabilityOs = '".$reliabilityOs."', ".
				 "vfOd = '".$vfOd."', ".
				 "vfOs = '".$vfOs."', ".
				 "descOd = '".$descOd."', ".
				 "descOs = '".$descOs."', ".
				 "stable = '".$stable."', ".
				 "fuApa = '".$fuApa."', ".
				 "ptInformed = '".$ptInformed."', ".
				 "comments = '".$comments."', ".
				 "signature = '".$signature."', ";
				
			if(!$zeissAction){  /*Prevent changing interpretation status if "Forum Add/Delete"*/
				$sql .= "phyName = '".$phyName."', ";
			}
			
			$sql .= "examDate = '".$examDate."', ".
				 //"patientId = '".$patientId."', ".
				 "Normal_OD_T = '".$Normal_OD_T."', ".
				 "Normal_OD_1 = '".$Normal_OD_1."', ".
				 "Normal_OD_2 = '".$Normal_OD_2."', ".
				 "Normal_OD_3 = '".$Normal_OD_3."', ".
				 "Normal_OD_4 = '".$Normal_OD_4."', ".
				 "BorderLineDefect_OD_T = '".$BorderLineDefect_OD_T."', ".
				 "BorderLineDefect_OD_1 = '".$BorderLineDefect_OD_1."', ".
				 "BorderLineDefect_OD_2 = '".$BorderLineDefect_OD_2."', ".
				 "BorderLineDefect_OD_3 = '".$BorderLineDefect_OD_3."', ".
				 "BorderLineDefect_OD_4 = '".$BorderLineDefect_OD_4."', ".
				 "Abnormal_OD_T = '".$Abnormal_OD_T."', ".
				 "Abnormal_OD_1 = '".$Abnormal_OD_1."', ".
				 "Abnormal_OD_2 = '".$Abnormal_OD_2."', ".
				 "Abnormal_OD_3 = '".$Abnormal_OD_3."', ".
				 "Abnormal_OD_4 = '".$Abnormal_OD_4."', ".
				 "NasalSteep_OD_Superior = '".$NasalSteep_OD_Superior."', ".
				 "NasalSteep_OD_Inferior = '".$NasalSteep_OD_Inferior."', ".
				 "NasalSteep_OD_S_T = '".$NasalSteep_OD_S_T."', ".
				 "NasalSteep_OD_S_1 = '".$NasalSteep_OD_S_1."', ".
				 "NasalSteep_OD_S_2 = '".$NasalSteep_OD_S_2."', ".
				 "NasalSteep_OD_S_3 = '".$NasalSteep_OD_S_3."', ".
				 "NasalSteep_OD_S_4 = '".$NasalSteep_OD_S_4."', ".
				 "NasalSteep_OD_I_T = '".$NasalSteep_OD_I_T."', ".
				 "NasalSteep_OD_I_1 = '".$NasalSteep_OD_I_1."', ".
				 "NasalSteep_OD_I_2 = '".$NasalSteep_OD_I_2."', ".
				 "NasalSteep_OD_I_3 = '".$NasalSteep_OD_I_3."', ".
				 "NasalSteep_OD_I_4 = '".$NasalSteep_OD_I_4."', ".
				 "Arcuatedefect_OD_Superior = '".$Arcuatedefect_OD_Superior."', ".
				 "Arcuatedefect_OD_Inferior = '".$Arcuatedefect_OD_Inferior."', ".
				 "Arcuatedefect_OD_S_T = '".$Arcuatedefect_OD_S_T."', ".
				 "Arcuatedefect_OD_S_1 = '".$Arcuatedefect_OD_S_1."', ".
				 "Arcuatedefect_OD_S_2 = '".$Arcuatedefect_OD_S_2."', ".
				 "Arcuatedefect_OD_S_3 = '".$Arcuatedefect_OD_S_3."', ".
				 "Arcuatedefect_OD_S_4 = '".$Arcuatedefect_OD_S_4."', ".
				 "Arcuatedefect_OD_I_T = '".$Arcuatedefect_OD_I_T."', ".
				 "Arcuatedefect_OD_I_1 = '".$Arcuatedefect_OD_I_1."', ".
				 "Arcuatedefect_OD_I_2 = '".$Arcuatedefect_OD_I_2."', ".
				 "Arcuatedefect_OD_I_3 = '".$Arcuatedefect_OD_I_3."', ".
				 "Arcuatedefect_OD_I_4 = '".$Arcuatedefect_OD_I_4."', ".
				 "Defect_OD_Central = '".$Defect_OD_Central."', ".
				 "Defect_OD_Superior = '".$Defect_OD_Superior."', ".
				 "Defect_OD_Inferior = '".$Defect_OD_Inferior."', ".
				 "Defect_OD_Scattered = '".$Defect_OD_Scattered."', ".
				 "blindSpot_OD_T = '".$blindSpot_OD_T."', ".
				 "blindSpot_OD_1 = '".$blindSpot_OD_1."', ".
				 "blindSpot_OD_2 = '".$blindSpot_OD_2."', ".
				 "blindSpot_OD_3 = '".$blindSpot_OD_3."', ".
				 "blindSpot_OD_4 = '".$blindSpot_OD_4."', ".
				 "Others_OD = '".$Others_OD."', ".
				 "Normal_OS_T = '".$Normal_OS_T."', ".
				 "Normal_OS_1 = '".$Normal_OS_1."', ".
				 "Normal_OS_2 = '".$Normal_OS_2."', ".
				 "Normal_OS_3 = '".$Normal_OS_3."', ".
				 "Normal_OS_4 = '".$Normal_OS_4."', ".
				 "BorderLineDefect_OS_T = '".$BorderLineDefect_OS_T."', ".
				 "BorderLineDefect_OS_1 = '".$BorderLineDefect_OS_1."', ".
				 "BorderLineDefect_OS_2 = '".$BorderLineDefect_OS_2."', ".
				 "BorderLineDefect_OS_3 = '".$BorderLineDefect_OS_3."', ".
				 "BorderLineDefect_OS_4 = '".$BorderLineDefect_OS_4."', ".
				 "Abnormal_OS_T = '".$Abnormal_OS_T."', ".
				 "Abnormal_OS_1 = '".$Abnormal_OS_1."', ".
				 "Abnormal_OS_2 = '".$Abnormal_OS_2."', ".
				 "Abnormal_OS_3 = '".$Abnormal_OS_3."', ".
				 "Abnormal_OS_4 = '".$Abnormal_OS_4."', ".
				 "NasalSteep_OS_Superior = '".$NasalSteep_OS_Superior."', ".
				 "NasalSteep_OS_Inferior = '".$NasalSteep_OS_Inferior."', ".
				 "NasalSteep_OS_S_T = '".$NasalSteep_OS_S_T."', ".
				 "NasalSteep_OS_S_1 = '".$NasalSteep_OS_S_1."', ".
				 "NasalSteep_OS_S_2 = '".$NasalSteep_OS_S_2."', ".
				 "NasalSteep_OS_S_3 = '".$NasalSteep_OS_S_3."', ".
				 "NasalSteep_OS_S_4 = '".$NasalSteep_OS_S_4."', ".
				 "NasalSteep_OS_I_T = '".$NasalSteep_OS_I_T."', ".
				 "NasalSteep_OS_I_1 = '".$NasalSteep_OS_I_1."', ".
				 "NasalSteep_OS_I_2 = '".$NasalSteep_OS_I_2."', ".
				 "NasalSteep_OS_I_3 = '".$NasalSteep_OS_I_3."', ".
				 "NasalSteep_OS_I_4 = '".$NasalSteep_OS_I_4."', ".
				 "Arcuatedefect_OS_Superior = '".$Arcuatedefect_OS_Superior."', ".
				 "Arcuatedefect_OS_Inferior = '".$Arcuatedefect_OS_Inferior."', ".
				 "Arcuatedefect_OS_S_T = '".$Arcuatedefect_OS_S_T."', ".
				 "Arcuatedefect_OS_S_1 = '".$Arcuatedefect_OS_S_1."', ".
				 "Arcuatedefect_OS_S_2 = '".$Arcuatedefect_OS_S_2."', ".
				 "Arcuatedefect_OS_S_3 = '".$Arcuatedefect_OS_S_3."', ".
				 "Arcuatedefect_OS_S_4 = '".$Arcuatedefect_OS_S_4."', ".
				 "Arcuatedefect_OS_I_T = '".$Arcuatedefect_OS_I_T."', ".
				 "Arcuatedefect_OS_I_1 = '".$Arcuatedefect_OS_I_1."', ".
				 "Arcuatedefect_OS_I_2 = '".$Arcuatedefect_OS_I_2."', ".
				 "Arcuatedefect_OS_I_3 = '".$Arcuatedefect_OS_I_3."', ".
				 "Arcuatedefect_OS_I_4 = '".$Arcuatedefect_OS_I_4."', ".
				 "Defect_OS_Central = '".$Defect_OS_Central."', ".
				 "Defect_OS_Superior = '".$Defect_OS_Superior."', ".
				 "Defect_OS_Inferior = '".$Defect_OS_Inferior."', ".
				 "Defect_OS_Scattered = '".$Defect_OS_Scattered."', ".
				 "blindSpot_OS_T = '".$blindSpot_OS_T."', ".
				 "blindSpot_OS_1 = '".$blindSpot_OS_1."', ".
				 "blindSpot_OS_2 = '".$blindSpot_OS_2."', ".
				 "blindSpot_OS_3 = '".$blindSpot_OS_3."', ".
				 "blindSpot_OS_4 = '".$blindSpot_OS_4."', ".
				 "Others_OS = '".$Others_OS."', ".
				 "Defect_OD_T = '".$Defect_OD_T."', ".
				 "Defect_OD_1 = '".$Defect_OD_1."', ".
				 "Defect_OD_2 = '".$Defect_OD_2."', ".
				 "Defect_OD_3 = '".$Defect_OD_3."', ".
				 "Defect_OD_4 = '".$Defect_OD_4."', ".
				 "Defect_OS_T = '".$Defect_OS_T."', ".
				 "Defect_OS_1 = '".$Defect_OS_1."', ".
				 "Defect_OS_2 = '".$Defect_OS_2."', ".
				 "Defect_OS_3 = '".$Defect_OS_3."', ".
				 "Defect_OS_4 = '".$Defect_OS_4."', ".
				 "monitorIOP = '".$monitorIOP."', ".
				 "diagnosisOther = '".$diagnosisOther."', ".
				 "tech2InformPt = '".$tech2InformPt."', ".
				 "Normal_OD_PoorStudy='".$Normal_OD_PoorStudy."', ".
				 "Normal_OS_PoorStudy='".$Normal_OS_PoorStudy."', ".
				 "NoSigChange_OD ='".$NoSigChange_OD."', ".
				 "Improved_OD='".$Improved_OD."', ".
				 "IncAbn_OD='".$IncAbn_OD."', ".
				 "NoSigChange_OS ='".$NoSigChange_OS."', ".
				 "Improved_OS='".$Improved_OS."', ".
				 "IncAbn_OS='".$IncAbn_OS."', ".
				 "iopTrgtOd='".$elem_trgtIopOd."', ".
				 "iopTrgtOs='".$elem_trgtIopOs."', ".
				 "techComments='".$elem_techComments."', ".
				 "ptInformedNv='".$ptInformedNv."', ".
				 "examTime = '".$examTime."', ".
				 "contiMeds = '".$contiMeds."', ".
				 "rptTst1yr = '".$rptTst1yr."', ".
				 "encounter_id='".$encounter_id."', ".
				 "ordrby='".$ordrby."',ordrdt='".$ordrdt."', ".
				 "forum_procedure='".$forum_procedure."', ".
				 "elem_gla_mac='".$elem_gla_mac."', ".
				 $signPathQry.
				 $signDTmQry.
				 "tapeuntaped_od = '".$tapeuntaped_od."', tapeuntaped_os = '".$tapeuntaped_os."', ".
				 "loss_sup_degree_od='".$loss_sup_degree_od."', loss_sup_degree_os='".$loss_sup_degree_os."', ".
				 "improve_degree_od='".$improve_degree_od."', improve_degree_os='".$improve_degree_os."'  ".
				 //"WHERE formId = '".$formId."' ";
				 "WHERE vf_id = '".$vf_id."' ";
			$res = sqlQuery($sql);
		}
		
		/*code, Purpose: Generate HL7 message for Test*/
		$zeissMsgType="VF";
		$zeissPatientId = $patientId;
		$zeissTestId = $vf_id;
		include("./zeissTestHl7.php");
		/*End code*/
		
		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "VF", $vf_id);
		//Super-Bill -----------
		if(!empty($ordrby) && !empty($vf_id)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$vf_id;
			$arIn["sb_testName"]="VF";
                        $arIn["form_id"]=$form_id;
                        $arIn["test_interpreted"]= ((int)$phyName > 0)? true : false;

			$oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);
		}
		//Super-Bill -----------

		//Test Pdf ---
		$objTests->saveTestPdfExe_2($patientId,$vf_id,"VF");
		//Test Pdf ---

		//FormId updates other tables -----
		if(!empty($formId)){
			// Make chart notes valid
			$oChartNote->makeChartNotesValid($formId);
			$funIopSum = "";
			//Syncronise with iop trgts
			$curformId = $objTests->isChartOpened($patientId);
			if($curformId == false){$curformId=0;}
			if(!empty($elem_trgtIopOd) || !empty($elem_trgtIopOs)){
				if($curformId != false){
					$cQry = "select sumOdIop, sumOsIop FROM chart_iop 
								WHERE form_id='".$curformId."' AND patient_id='".$patientId."' AND purged='0' ";
					$row = sqlQuery($cQry);
					if($row == false){	// Insert
						//get Last Id
						/*$lastId = 0;
						$res = valNewRecordIop($patientId,"chart_iop.iop_id");
						for($i=0;$row=sqlFetchArray($res);$i++){
							$lastId = $row["iop_id"];
						}
						//
						//
						echo $sql = "INSERT INTO chart_iop (trgtOd,trgtOs, form_id, patient_id) VALUES ('".$elem_trgtIopOd."', '".$elem_trgtIopOs."', '".$curformId."','".$elem_patientId."') ";
						//$insertId = sqlInsert($sql);
						//
						//$test = copyLastIop($insertId,$lastId);*/
					}else{
						$sumOdIop = $row["sumOdIop"];
						$sumOsIop = $row["sumOsIop"];
						$sumOdIop = preg_replace("/Trgt:\s*\d*/", "Trgt:".$elem_trgtIopOd,$sumOdIop);
						$sumOsIop = preg_replace("/Trgt:\s*\d*/", "Trgt:".$elem_trgtIopOs,$sumOsIop);
						//Update
						$sql = "UPDATE chart_iop SET trgtOd='".$elem_trgtIopOd."', trgtOs='".$elem_trgtIopOs."', 
								sumOdIop='".imw_real_escape_string($sumOdIop)."', sumOsIop='".imw_real_escape_string($sumOsIop)."' 
								WHERE form_id='".$curformId."' AND patient_id='".$patientId."' AND purged='0'  ";
						$res = sqlQuery($sql);
						$funIopSum = "";
					}
					//Update Nfa
					$sql = "UPDATE nfa SET iopTrgtOd='".$elem_trgtIopOd."', iopTrgtOs='".$elem_trgtIopOs."' 
							WHERE form_id = '".$curformId."' AND patient_id='".$patientId."'  ";
					$res = sqlQuery($sql);
				}else{
					$curformId = 0;
				}
				//Save IOP Def Vals
				$objTests->saveIopTrgt($elem_trgtIopOd,$elem_trgtIopOs,$patientId,$curformId);
			}else{//if empty targt values
				$objTests->remIopTrgtDefVal($elem_trgtIopOd,$elem_trgtIopOs,$patientId,$curformId);
			}
			//End Syncronise with iop trgts
		}

		echo "<script>".
				"try{";
					if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
						$afterSaveinPopup = 'yes';
					}else{
						$afterSaveinPopup = 'no';
						echo "top.alert_notification_show(\"Test has been saved.\");";
					}
					echo "
					window.location.replace(\"test_vf.php?noP=".$elem_noP."&tId=".$vf_id."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
				}catch(err){}".
			 "</script>";
		exit();

		break;
	}
	case "OCT":{
		$patient_id = $_POST["elem_patientId"];
		$form_id = $_POST["elem_formId"];
		$hd_oct_mode = $_POST["hd_oct_mode"];
		$oct_id =  $_POST["elem_octId"];
		$examDate =  getDateFormatDB($_POST["elem_examDate"]);
		if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
		$scanLaserOct =  $_POST["elem_scanLaserOct"];
		$scanLaserEye =  $_POST["elem_scanLaserEye"];
		$performBy =  $_POST["elem_performedBy"];
		$ptUndersatnding =  $_POST["elem_ptUndersatnding"];
		$diagnosis =  imw_real_escape_string($_POST["elem_diagnosis"]);
		$reliabilityOd =  $_POST["elem_reliabilityOd"];
		$reliabilityOs =  $_POST["elem_reliabilityOs"];
		$scanLaserOd =  $_POST["elem_scanLaserOd"];
		$descOd =  $_POST["elem_descOd"];
		$scanLaserOs =  $_POST["elem_scanLaserOs"];
		$descOs =  $_POST["elem_descOs"];
		$stable =  $_POST["elem_stable"];
		$monitorIOP =  $_POST["elem_monitorIOP"];
		$fuApa =  $_POST["elem_fuApa"];
		$ptInformed =  $_POST["elem_ptInformed"];
		$comments =  imw_real_escape_string($_POST["elem_comments"]);
		$signature =  $_POST["elem_vfSign"];
		$phyName =  $_POST["elem_physician"];
		$diagnosisOther =  imw_real_escape_string($_POST["elem_diagnosisOther"]);
		$tech2InformPt = $_POST["elem_tech2InformPt"];
		$Normal_OD_PoorStudy = $_POST["elem_normal_poorStudy_od"];
		$Normal_OS_PoorStudy = $_POST["elem_normal_poorStudy_os"];
		$elem_trgtIopOd = imw_real_escape_string($_POST["elem_targetIop_OD"]);
		$elem_foveaThick_OD = imw_real_escape_string($_POST["elem_foveaThick_OD"]);
		$elem_trgtIopOs = imw_real_escape_string($_POST["elem_targetIop_OS"]);
		$elem_foveaThick_OS = imw_real_escape_string($_POST["elem_foveaThick_OS"]);
		$avg_nfl_Thick_OD = imw_real_escape_string($_POST["elem_avg_nfl_Thick_OD"]);
		$avg_nfl_Thick_OS = imw_real_escape_string($_POST["elem_avg_nfl_Thick_OS"]);
		
		$forum_procedure = (isset($_REQUEST['forum_procedure']))?imw_real_escape_string($_REQUEST['forum_procedure']):"";
	    $zeissAction = (isset($_REQUEST['forum_procedure']))?$_POST['zeissAction']:false;

		//$elem_interpretedBy = $_POST["elem_interpretedBy"];
		if($_POST["techComments"] == 'Technician Comments'){
			$_POST["techComments"] = '';
		}
		$elem_techComments = imw_real_escape_string($_POST["techComments"]);
		$ptInformedNv = $_POST["elem_informedPtNv"];
		$elem_noP = $_POST["elem_noP"];
		$examTime = $_POST["elem_examTime"];
		$contiMeds = $_POST["elem_contiMeds"];
		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
		$mon_finding = $_POST["elem_monitor_finding"];

		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
			$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
			$encounter_id = $objTests->getEncounterId();
		}

		//TestRes
		$test_res_od = $test_res_os = "";
		$arrTestRes = array("CME","IRF","ERM",
							"Drusen","VMT","SRetF",
							"SRF","CNV","RPEDetach",
							"RPERip");
		foreach($arrTestRes as $key => $val){
			//Od
			if(isset($_POST["testRes_OD_".$val]) && !empty($_POST["testRes_OD_".$val])){
				$test_res_od .= (empty($test_res_od)) ? "" : ",";
				$test_res_od .= $_POST["testRes_OD_".$val];
			}
			//Os
			if(isset($_POST["testRes_OS_".$val]) && !empty($_POST["testRes_OS_".$val])){
				$test_res_os .= (empty($test_res_os)) ? "" : ",";
				$test_res_os .= $_POST["testRes_OS_".$val];
			}
		}

		//summary
		$summaryOd = $summaryOs = "";
		//Od
		$arr = array(""=>$Normal_OD_T, "Poor Study"=>$Normal_OD_PoorStudy);
		$summaryOd .= $objTests->getTestSumm($arr,"Normal");

		$arr = array("T"=>$BorderLineDefect_OD_T,"+1"=>$BorderLineDefect_OD_1,"+2"=>$BorderLineDefect_OD_2,
					"+3"=>$BorderLineDefect_OD_3,"+4"=>$BorderLineDefect_OD_4);
		$summaryOd .= $objTests->getTestSumm($arr,"Border Line Defect");

		$arr = array("T"=>$Abnorma_OD_T,"+1"=>$Abnorma_OD_1,"+2"=>$Abnorma_OD_2,
					"+3"=>$Abnorma_OD_3,"+4"=>$Abnorma_OD_4);
		$summaryOd .= $objTests->getTestSumm($arr,"Abnormal");

		//Test Res
		$arr = array("CME"=>$testRes_OD_CME,"Infra Retinal Fluid"=>$testRes_OD_IRF,"ERM"=>$testRes_OD_ERM,
					"Drusen"=>$testRes_OD_Drusen,"Vitreous Macula Traction"=>$testRes_OD_VMT,"Sub Retinal Fluid"=>$testRes_OD_SRetF,
					"Sub RPE Foveal"=>$testRes_OD_SRF,"CNV"=>$testRes_OD_CNV,"RPE Detach"=>$testRes_OD_RPEDetach,
					"RPE Rip"=>$testRes_OD_RPERip);
		$summaryOd .= $objTests->getTestSumm($arr,"");

		$NoSigChange_OD = $_POST["elem_noSigChange_OD"];
		$arr = array("" => $NoSigChange_OD);
		$summaryOd .= $objTests->getTestSumm($arr, "No Significant Change");

		$Improved_OD = $_POST["elem_improved_OD"];
		$arr = array("" => $Improved_OD);
		$summaryOd .= $objTests->getTestSumm($arr, "Improved");

		$IncAbn_OD = $_POST["elem_incAbn_OD"];
		$arr = array("" => $IncAbn_OD);
		$summaryOd .= $objTests->getTestSumm($arr, "Increase Abnormal");


		$arr = array($Others_OD=>$Others_OD);
		$summaryOd .= $objTests->getTestSumm($arr,"Other");
		//Os
		$arr = array(""=>$Normal_OS_T, "Poor Study"=>$Normal_OS_PoorStudy);
		$summaryOs .= $objTests->getTestSumm($arr,"Normal");

		$arr = array("T"=>$BorderLineDefect_OS_T,"+1"=>$BorderLineDefect_OS_1,"+2"=>$BorderLineDefect_OS_2,
					"+3"=>$BorderLineDefect_OS_3,"+4"=>$BorderLineDefect_OS_4);
		$summaryOs .= $objTests->getTestSumm($arr,"Border Line Defect");

		$arr = array("T"=>$Abnorma_OS_T,"+1"=>$Abnorma_OS_1,"+2"=>$Abnorma_OS_2,
					"+3"=>$Abnorma_OS_3,"+4"=>$Abnorma_OS_4);
		$summaryOs .= $objTests->getTestSumm($arr,"Abnormal");

		//Test Res
		$arr = array("CME"=>$testRes_OS_CME,"Infra Retinal Fluid"=>$testRes_OS_IRF,"ERM"=>$testRes_OS_ERM,
					"Drusen"=>$testRes_OS_Drusen,"Vitreous Macula Traction"=>$testRes_OS_VMT,"Sub Retinal Fluid"=>$testRes_OS_SRetF,
					"Sub RPE Foveal"=>$testRes_OS_SRF,"CNV"=>$testRes_OS_CNV,"RPE Detach"=>$testRes_OS_RPEDetach,
					"RPE Rip"=>$testRes_OS_RPERip);
		$summaryOs .= $objTests->getTestSumm($arr,"");

		$NoSigChange_OS = $_POST["elem_noSigChange_OS"];
		$arr = array("" => $NoSigChange_OS);
		$summaryOs .= $objTests->getTestSumm($arr, "No Significant Change");

		$Improved_OS = $_POST["elem_improved_OS"];
		$arr = array("" => $Improved_OS);
		$summaryOs .= $objTests->getTestSumm($arr, "Improved");

		$IncAbn_OS = $_POST["elem_incAbn_OS"];
		$arr = array("" => $IncAbn_OS);
		$summaryOs .= $objTests->getTestSumm($arr, "Increase Abnormal");

		$arr = array($Others_OS=>$Others_OS);
		$summaryOs .= $objTests->getTestSumm($arr,"Other");

		$descOd = (!empty($summaryOd)) ? imw_real_escape_string("".$summaryOd) : "";
		$descOs = (!empty($summaryOs)) ? imw_real_escape_string("".$summaryOs) : "";

		//echo "Desc:<br>".$descOd;
		//echo "Desc:<br>".$descOs;

		//summary

		//check
		if(empty($oct_id)){

			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;

			if(isset($arrTest2edit["OCT"]) && !empty($arrTest2edit["OCT"])){

				//Check in db
				$cQry = "select oct_id FROM oct
						WHERE patient_id ='".$patient_id."'
						AND oct_id = '".$arrTest2edit["OCT"]."' ";
				$row = sqlQuery($cQry);
				$oct_id = (($row == false) || empty($row["oct_id"])) ? "0" : $row["oct_id"];

				//Unset Session OCT
				$arrTest2edit["OCT"] = "";
				$arrTest2edit["OCT"] = NULL;
				unset($arrTest2edit["OCT"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);

			}else{

				$cQry = "SELECT oct_id FROM oct
						WHERE patient_id='".$patient_id."' AND examDate = '".$examDate."' AND examTime = '".$examTime."' ";
				$row = sqlQuery($cQry);
				$oct_id = (($row == false) || empty($row["oct_id"])) ? "0" : $row["oct_id"];

			}
		}

		if(empty($oct_id)){
			$phyName=($zeissAction)?0:$phyName; /*Mark test uninterpreted if "Forum Add"*/
			$sql = "INSERT INTO oct ".
					"( ".
					"oct_id, scanLaserOct, scanLaserEye, performBy, diagnosis, ptUndersatnding, ".
					"reliabilityOd, reliabilityOs, scanLaserOd, scanLaserOs, descOd, descOs, ".
					"stable, fuApa, ptInformed, comments, signature, phyName, examDate, ".
					"Normal_OD_T, Normal_OD_1, Normal_OD_2, Normal_OD_3, Normal_OD_4, ".
					"BorderLineDefect_OD_T, BorderLineDefect_OD_1, BorderLineDefect_OD_2, BorderLineDefect_OD_3, BorderLineDefect_OD_4, ".
					"Abnorma_OD_T, Abnorma_OD_1, Abnorma_OD_2, Abnorma_OD_3, Abnorma_OD_4, Others_OD, ".
					"Normal_OS_T, Normal_OS_1, Normal_OS_2, Normal_OS_3, Normal_OS_4, ".
					"BorderLineDefect_OS_T, BorderLineDefect_OS_1, BorderLineDefect_OS_2, BorderLineDefect_OS_3, BorderLineDefect_OS_4, ".
					"Abnorma_OS_T, Abnorma_OS_1, Abnorma_OS_2, Abnorma_OS_3, Abnorma_OS_4, Others_OS, ".
					"patient_id, form_id, diagnosisOther, monitorIOP,tech2InformPt, ".
					"Normal_OD_PoorStudy, Normal_OS_PoorStudy, ".
					"NoSigChange_OD, Improved_OD, IncAbn_OD, ".
					"NoSigChange_OS, Improved_OS, IncAbn_OS, ".
					"iopTrgtOd, iopTrgtOs,techComments, ".
					"ptInformedNv,examTime,contiMeds, ".
					"test_res_od, test_res_os, ".
					"encounter_id,ordrby,ordrdt,fovea_thick_OD, fovea_thick_OS, ".
				        "avg_nfl_Thick_OD,avg_nfl_Thick_OS,forum_procedure,mon_finding ".
					$signPathFieldName.$signDTmFieldName.
					") ".
					"VALUES ".
					"( ".
					"'', '".$scanLaserOct."', '".$scanLaserEye."', '".$performBy."', '".$diagnosis."', '".$ptUndersatnding."', ".
					"'".$reliabilityOd."', '".$reliabilityOs."', '".$scanLaserOd."', '".$scanLaserOs."', '".$descOd."', '".$descOs."', ".
					"'".$stable."', '".$fuApa."', '".$ptInformed."', '".$comments."', '".$signature."', '".$phyName."', '".$examDate."', ".
					"'".$Normal_OD_T."', '".$Normal_OD_1."', '".$Normal_OD_2."', '".$Normal_OD_3."', '".$Normal_OD_4."', ".
					"'".$BorderLineDefect_OD_T."', '".$BorderLineDefect_OD_1."', '".$BorderLineDefect_OD_2."', '".$BorderLineDefect_OD_3."', '".$BorderLineDefect_OD_4."', ".
					"'".$Abnorma_OD_T."', '".$Abnorma_OD_1."', '".$Abnorma_OD_2."', '".$Abnorma_OD_3."', '".$Abnorma_OD_4."', '".imw_real_escape_string($Others_OD)."',".
					"'".$Normal_OS_T."', '".$Normal_OS_1."', '".$Normal_OS_2."', '".$Normal_OS_3."', '".$Normal_OS_4."', ".
					"'".$BorderLineDefect_OS_T."', '".$BorderLineDefect_OS_1."', '".$BorderLineDefect_OS_2."', '".$BorderLineDefect_OS_3."', '".$BorderLineDefect_OS_4."', ".
					"'".$Abnorma_OS_T."', '".$Abnorma_OS_1."', '".$Abnorma_OS_2."', '".$Abnorma_OS_3."', '".$Abnorma_OS_4."', '".imw_real_escape_string($Others_OS)."',".
					"'".$patient_id."', '".$form_id."', '".$diagnosisOther."', '".$monitorIOP."', '".$tech2InformPt."', ".
					"'".$Normal_OD_PoorStudy."', '".$Normal_OD_PoorStudy."', ".
					"'".$NoSigChange_OD."', '".$Improved_OD."', '".$IncAbn_OD."', ".
					"'".$NoSigChange_OS."', '".$Improved_OS."', '".$IncAbn_OS."', ".
					"'".$elem_trgtIopOd."', '".$elem_trgtIopOs."','".$elem_techComments."', ".
					"'".$ptInformedNv."','".$examTime."','".$contiMeds."', ".
					"'".$test_res_od."', '".$test_res_os."', ".
					"'".$encounter_id."','".$ordrby."','".$ordrdt."','".$elem_foveaThick_OD."','".$elem_foveaThick_OS."', ".
					"'".$avg_nfl_Thick_OD."','".$avg_nfl_Thick_OS."','".$forum_procedure."','".$mon_finding."' ".
					$signPathFieldValue.$signDTmFieldValue.
					") ";			
			$Insert_res = imw_query($sql);
			$InsertId = imw_insert_id();
			$oct_id = $InsertId;
		}else if(!empty($oct_id)){
			$sql = "UPDATE oct ".
					"SET ".
					"scanLaserOct = '".$scanLaserOct."', ".
					"scanLaserEye = '".$scanLaserEye."', ".
					"performBy = '".$performBy."', ".
					"diagnosis = '".$diagnosis."', ".
					"ptUndersatnding = '".$ptUndersatnding."', ".
					"reliabilityOd = '".$reliabilityOd."', ".
					"reliabilityOs = '".$reliabilityOs."', ".
					"scanLaserOd = '".$scanLaserOd."', ".
					"scanLaserOs = '".$scanLaserOs."', ".
					"descOd = '".$descOd."', ".
					"Normal_OD_T = '".$Normal_OD_T."', ".
					"Normal_OD_1 = '".$Normal_OD_1."', ".
					"Normal_OD_2 = '".$Normal_OD_2."', ".
					"Normal_OD_3 = '".$Normal_OD_3."', ".
					"Normal_OD_4 = '".$Normal_OD_4."', ".
					"BorderLineDefect_OD_T = '".$BorderLineDefect_OD_T."', ".
					"BorderLineDefect_OD_1 = '".$BorderLineDefect_OD_1."', ".
					"BorderLineDefect_OD_2 = '".$BorderLineDefect_OD_2."', ".
					"BorderLineDefect_OD_3 = '".$BorderLineDefect_OD_3."', ".
					"BorderLineDefect_OD_4 = '".$BorderLineDefect_OD_4."', ".
					"Abnorma_OD_T = '".$Abnorma_OD_T."', ".
					"Abnorma_OD_1 = '".$Abnorma_OD_1."', ".
					"Abnorma_OD_2 = '".$Abnorma_OD_2."', ".
					"Abnorma_OD_3 = '".$Abnorma_OD_3."', ".
					"Abnorma_OD_4 = '".$Abnorma_OD_4."', ".
					"Others_OD = '".imw_real_escape_string($Others_OD)."', ".
					"Normal_OS_T = '".$Normal_OS_T."', ".
					"Normal_OS_1 = '".$Normal_OS_1."', ".
					"Normal_OS_2 = '".$Normal_OS_2."', ".
					"Normal_OS_3 = '".$Normal_OS_3."', ".
					"Normal_OS_4 = '".$Normal_OS_4."', ".
					"BorderLineDefect_OS_T = '".$BorderLineDefect_OS_T."', ".
					"BorderLineDefect_OS_1 = '".$BorderLineDefect_OS_1."', ".
					"BorderLineDefect_OS_2 = '".$BorderLineDefect_OS_2."', ".
					"BorderLineDefect_OS_3 = '".$BorderLineDefect_OS_3."', ".
					"BorderLineDefect_OS_4 = '".$BorderLineDefect_OS_4."', ".
					"Abnorma_OS_T = '".$Abnorma_OS_T."', ".
					"Abnorma_OS_1 = '".$Abnorma_OS_1."', ".
					"Abnorma_OS_2 = '".$Abnorma_OS_2."', ".
					"Abnorma_OS_3 = '".$Abnorma_OS_3."', ".
					"Abnorma_OS_4 = '".$Abnorma_OS_4."', ".
					"Others_OS = '".imw_real_escape_string($Others_OS)."', ".
					"descOs = '".$descOs."', ".
					"stable = '".$stable."', ".
					"fuApa = '".$fuApa."', ".
					"ptInformed = '".$ptInformed."', ".
					"comments = '".$comments."', ".
					"signature = '".$signature."', ";
					
				if(!$zeissAction){  /*Prevent changing interpretation status if "Forum Add/Delete"*/
					$sql .= "phyName = '".$phyName."', ";
				}
				
			$sql .= "examDate = '".$examDate."', ".
					"monitorIOP = '".$monitorIOP."', ".
					//"patient_id = '".$patient_id."', ".
					"tech2InformPt = '".$tech2InformPt."', ".
					"diagnosisOther = '".$diagnosisOther."', ".
					"Normal_OD_PoorStudy = '".$Normal_OD_PoorStudy."', ".
					"Normal_OS_PoorStudy = '".$Normal_OS_PoorStudy."', ".
					"NoSigChange_OD='".$NoSigChange_OD."', ".
					"Improved_OD='".$Improved_OD."', ".
					"IncAbn_OD='".$IncAbn_OD."', ".
					"NoSigChange_OS='".$NoSigChange_OS."', ".
					"Improved_OS='".$Improved_OS."', ".
					"IncAbn_OS='".$IncAbn_OS."', ".
					"iopTrgtOd='".$elem_trgtIopOd."', ".
					"iopTrgtOs='".$elem_trgtIopOs."', ".
					"techComments='".$elem_techComments."', ".
					"ptInformedNv='".$ptInformedNv."', ".
					"examTime ='".$examTime."', ".
					"contiMeds = '".$contiMeds."', ".
					"test_res_od = '".$test_res_od."', ".
					"test_res_os = '".$test_res_os."', ".
					"encounter_id='".$encounter_id."', ".
					"ordrby='".$ordrby."',ordrdt='".$ordrdt."', ".
					"fovea_thick_OD='".$elem_foveaThick_OD."', ".
					"fovea_thick_OS='".$elem_foveaThick_OS."', ".
					"avg_nfl_Thick_OD='".$avg_nfl_Thick_OD."', ".
					"forum_procedure='".$forum_procedure."', ".
					"mon_finding='".$mon_finding."', ".
					 $signPathQry.
					 $signDTmQry.
					"avg_nfl_Thick_OS='".$avg_nfl_Thick_OS."' ".					
					//"WHERE form_id = '".$form_id."' ";
					"WHERE oct_id = '".$oct_id."' ";			
			$res = sqlQuery($sql);
		}
		
		/*code, Purpose: Generate HL7 message for Test*/
		$zeissMsgType="OCT";
		$zeissPatientId = $patient_id;
		$zeissTestId = $oct_id;
		include("./zeissTestHl7.php");
		/*End code*/
		
		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "OCT", $oct_id);
		/*--interpretation end--*/
		if(!empty($ordrby) && !empty($oct_id)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$oct_id;
			$arIn["sb_testName"]="OCT";
                        $arIn["form_id"]=$form_id;
                        $arIn["test_interpreted"]= ((int)$phyName > 0)? true : false;

			$oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);
		}
		//Super-Bill -----------
		
		//save tests Pdf
		$objTests->saveTestPdfExe_2($patient_id,$oct_id,"OCT");

		//FormId updates other tables -----
		if(!empty($form_id)){
			// Make chart notes valid
			
			$oChartNote->makeChartNotesValid($form_id);

			//Syncronise with iop trgts
			$curformId = $objTests->isChartOpened($patient_id);
			if($curformId == false){$curformId =0;}
			if(!empty($elem_trgtIopOd) || !empty($elem_trgtIopOs)){
				
				if($curformId != false){
					//check
					$cQry = "select sumOdIop, sumOsIop FROM chart_iop 
							WHERE form_id='".$curformId."' AND patient_id='".$patient_id."' AND purged='0' ";
					$row = sqlQuery($cQry);

					if($row == false){	// Insert
						
					}else{
						$sumOdIop = $row["sumOdIop"];
						$sumOsIop = $row["sumOsIop"];

						$sumOdIop = preg_replace("/Trgt:\s*\d*/", "Trgt:".$elem_trgtIopOd,$sumOdIop);
						$sumOsIop = preg_replace("/Trgt:\s*\d*/", "Trgt:".$elem_trgtIopOs,$sumOsIop);

						//Update
						$sql = "UPDATE chart_iop SET trgtOd='".$elem_trgtIopOd."', trgtOs='".$elem_trgtIopOs."', 
								sumOdIop='".imw_real_escape_string($sumOdIop)."', 
								sumOsIop='".imw_real_escape_string($sumOsIop)."' 
								WHERE form_id='".$curformId."' AND patient_id='".$patient_id."' AND purged='0' ";
						$res = sqlQuery($sql);
					}

					//Update VF
					$sql = "UPDATE vf SET iopTrgtOd='".$elem_trgtIopOd."', iopTrgtOs='".$elem_trgtIopOs."' 
							WHERE formId = '".$curformId."' AND patientId='".$patient_id."'   ";
					$res = sqlQuery($sql);

				}else{
					$curformId = 0;
				}
				//Save IOP Def Vals
				$objTests->saveIopTrgt($elem_trgtIopOd,$elem_trgtIopOs,$patient_id,$curformId);
			}else{//if empty targt values
				$objTests->remIopTrgtDefVal($elem_trgtIopOd,$elem_trgtIopOs,$patient_id,$curformId);
			}
			//End Syncronise with iop trgts
		}
		//FormId updates other tables -----
		echo "<script>".
				"try{";
					if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
						$afterSaveinPopup = 'yes';
					}else{
						$afterSaveinPopup = 'no';
						echo "top.alert_notification_show(\"Test has been saved.\");";
					}
					echo "
					window.location.replace(\"test_oct.php?noP=".$elem_noP."&tId=".$oct_id."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
				}catch(err){}".
			 "</script>";
		exit();
		break;
	}
	case "OCT-RNFL":{
		$patient_id = $_POST["elem_patientId"];
		$form_id = $_POST["elem_formId"];
		$hd_oct_mode = $_POST["hd_oct_mode"];
		$oct_id =  $_POST["elem_octId"];
		$examDate =  getDateFormatDB($_POST["elem_examDate"]);
		if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
		$scanLaserOct =  $_POST["elem_scanLaserOct"];
		$scanLaserEye =  $_POST["elem_scanLaserEye"];
		$performBy =  $_POST["elem_performedBy"];
		$ptUndersatnding =  $_POST["elem_ptUndersatnding"];
		$diagnosis =  imw_real_escape_string($_POST["elem_diagnosis_od"]);
		$diagnosis_os = imw_real_escape_string($_POST["elem_diagnosis_os"]);		
		$reliabilityOd =  $_POST["elem_reliabilityOd"];
		$reliabilityOs =  $_POST["elem_reliabilityOs"];
		$scanLaserOd =  $_POST["elem_scanLaserOd"];
		$descOd =  $_POST["elem_descOd"];
		$scanLaserOs =  $_POST["elem_scanLaserOs"];
		$descOs =  $_POST["elem_descOs"];
		$stable =  $_POST["elem_stable"];
		$monitorIOP =  $_POST["elem_monitorIOP"];
		$fuApa =  $_POST["elem_fuApa"];
		$ptInformed =  $_POST["elem_ptInformed"];
		$comments_od = $_POST["elem_comments_od"];
		$comments_os = $_POST["elem_comments_os"];
		$comments = imw_real_escape_string($comments_od."!~!".$comments_os);
		$signature =  $_POST["elem_vfSign"];
		$phyName =  $_POST["elem_physician"];
		$diagnosisOther =  imw_real_escape_string($_POST["elem_diagnosisOtherOd"]);
		$diagnosisOther_os = imw_real_escape_string($_POST["elem_diagnosisOtherOs"]);		
		$tech2InformPt = $_POST["elem_tech2InformPt"];
		$findingDiscusPt = $_POST["elem_findingDiscusPt"];
		
		$forum_procedure = (isset($_REQUEST['forum_procedure']))?imw_real_escape_string($_REQUEST['forum_procedure']):"";
		$zeissAction = (isset($_REQUEST['forum_procedure']))?$_POST['zeissAction']:false;
		
		$interpretation_OD="";
		if(!empty($_POST["elem_stable_OD"])){$interpretation_OD.=$_POST["elem_stable_OD"].", ";}
		
		if(!empty($_POST["elem_improve_OD"])){$interpretation_OD.=$_POST["elem_improve_OD"].", ";}
		
		if(!empty($_POST["elem_worse_OD"])){$interpretation_OD.=$_POST["elem_worse_OD"].", ";}
		
		if(!empty($_POST["elem_likeProgrsn_OD"])){$interpretation_OD.=$_POST["elem_likeProgrsn_OD"].", ";}
		
		if(!empty($_POST["elem_possibleProgrsn_OD"])){$interpretation_OD.=$_POST["elem_possibleProgrsn_OD"].", ";}
		
		if(!empty($_POST["elem_likely_worse_OD"])){$interpretation_OD.=$_POST["elem_likely_worse_OD"].", ";}
		
		if(!empty($_POST["elem_probably_worse_OD"])){$interpretation_OD.=$_POST["elem_probably_worse_OD"].", ";}
		
		if(!empty($_POST["elem_glaucoma_stage_OD"])){$interpretation_OD.=$_POST["elem_glaucoma_stage_OD"].", ";}
		
		$interpretation_OS="";
		if(!empty($_POST["elem_stable_OS"])){$interpretation_OS.=$_POST["elem_stable_OS"].", ";}
		
		if(!empty($_POST["elem_improve_OS"])){$interpretation_OS.=$_POST["elem_improve_OS"].", ";}
		
		if(!empty($_POST["elem_worse_OS"])){$interpretation_OS.=$_POST["elem_worse_OS"].", ";}
		
		if(!empty($_POST["elem_likeProgrsn_OS"])){$interpretation_OS.=$_POST["elem_likeProgrsn_OS"].", ";}
		
		if(!empty($_POST["elem_possibleProgrsn_OS"])){$interpretation_OS.=$_POST["elem_possibleProgrsn_OS"].", ";}
		
		if(!empty($_POST["elem_likely_worse_OS"])){$interpretation_OS.=$_POST["elem_likely_worse_OS"].", ";}
		
		if(!empty($_POST["elem_probably_worse_OS"])){$interpretation_OS.=$_POST["elem_probably_worse_OS"].", ";}
		
		if(!empty($_POST["elem_glaucoma_stage_OS"])){$interpretation_OS.=$_POST["elem_glaucoma_stage_OS"].", ";}
		
		$comments_interp = imw_real_escape_string($_POST["elem_comments_interp"]);
		
		$glaucoma_stage_opt_OD="";
		if(!empty($_POST["elem_glst_unspecified_OD"])){ $glaucoma_stage_opt_OD.=$_POST["elem_glst_unspecified_OD"].", "; }
		
		if(!empty($_POST["elem_glst_mild_OD"])){ $glaucoma_stage_opt_OD.=$_POST["elem_glst_mild_OD"].", ";}
		
		if(!empty($_POST["elem_glst_moderate_OD"])){ $glaucoma_stage_opt_OD.=$_POST["elem_glst_moderate_OD"].", ";}
		
		if(!empty($_POST["elem_glst_severe_OD"])){ $glaucoma_stage_opt_OD.=$_POST["elem_glst_severe_OD"].", ";}
		
		if(!empty($_POST["elem_glst_intermediate_OD"])){ $glaucoma_stage_opt_OD.=$_POST["elem_glst_intermediate_OD"].", ";}
		
		$glaucoma_stage_opt_OS="";
		if(!empty($_POST["elem_glst_unspecified_OS"])){ $glaucoma_stage_opt_OS.=$_POST["elem_glst_unspecified_OS"].", "; }
		
		if(!empty($_POST["elem_glst_mild_OS"])){ $glaucoma_stage_opt_OS.=$_POST["elem_glst_mild_OS"].", ";}
		
		if(!empty($_POST["elem_glst_moderate_OS"])){ $glaucoma_stage_opt_OS.=$_POST["elem_glst_moderate_OS"].", ";}
		
		if(!empty($_POST["elem_glst_severe_OS"])){ $glaucoma_stage_opt_OS.=$_POST["elem_glst_severe_OS"].", ";}
		
		if(!empty($_POST["elem_glst_intermediate_OS"])){ $glaucoma_stage_opt_OS.=$_POST["elem_glst_intermediate_OS"].", ";}
		//if(!empty($_POST["elem_glst_indeterminate"])){ $glaucoma_stage_opt.=$_POST["elem_glst_indeterminate"];}
		
		$plan="";
		if(!empty($_POST["elem_ptInformYPhy"])){ $plan.=$_POST["elem_ptInformYPhy"];}
		if(!empty($_POST["elem_2bCallYTech"])){$plan.=$_POST["elem_2bCallYTech"];}
		if(!empty($_POST["elem_byLetter"])){$plan.=$_POST["elem_byLetter"];}
		if(!empty($_POST["elem_willInfrmNextVisit"])){$plan.=$_POST["elem_willInfrmNextVisit"];}
		if(!empty($_POST["elem_contMeds"])){$plan.=$_POST["elem_contMeds"];}
		if(!empty($_POST["elem_monitorFind"])){$plan.=$_POST["elem_monitorFind"];}
		//if(!empty($_POST["elem_repeatTestNxtVst"])){$plan.=$_POST["elem_repeatTestNxtVst"];}
		if(!empty($_POST["elem_repeatTest"])){$plan.=$_POST["elem_repeatTest"];}
		
		//$repeatTestNxtVstEye = $_POST["elem_repeatTestNxtVstEye"];
		$repeatTestNxtVstEye = "";
		$repeatTestVal1 = $_POST["elem_repeatTestVal1"];
		$repeatTestVal2 = $_POST["elem_repeatTestVal2"];
		$repeatTestEye=$_POST["elem_repeatTestEye"];
		
		//$comments_plan = imw_real_escape_string($_POST["elem_comments_plan"]);
		$comments_plan = "";
		$improve = $_POST["elem_improve"];
		$worse = $_POST["elem_worse"];
		
		$dilated = $_POST["elem_dilated"];
		
		$testTime = $_POST["elem_testTime"];
		
		//$elem_interpretedBy = $_POST["elem_interpretedBy"];
		if($_POST["techComments"] == 'Technician Comments'){
			$_POST["techComments"] = '';
		}
		$elem_techComments = imw_real_escape_string($_POST["techComments"]);
		$ptInformedNv = $_POST["elem_informedPtNv"];
		$elem_noP = $_POST["elem_noP"];
		$examTime = $_POST["elem_examTime"];
		$contiMeds = $_POST["elem_contiMeds"];
		$ordrby = $_POST["elem_opidTestOrdered"];
		$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);

		//OD
		$signal_strength_od = $_POST["elem_signal_strength_od"]; 
		
		$quality_od  = "";
		if(!empty($_POST["elem_quality_od_gd"])){ $quality_od  .= $_POST["elem_quality_od_gd"].", "; }
		if(!empty($_POST["elem_quality_od_adequate"])){ $quality_od  .= $_POST["elem_quality_od_adequate"].", "; }
		if(!empty($_POST["elem_quality_od_poor"])){ $quality_od  .= $_POST["elem_quality_od_poor"];}
		
		$details_od="";
		if(!empty($_POST["elem_details_od_AlgoFail"])) {$details_od  .= $_POST["elem_details_od_AlgoFail"]."!,! "; }
		if(!empty($_POST["elem_details_od_MediaOpacity"])) {$details_od  .= $_POST["elem_details_od_MediaOpacity"]."!,! "; }
		if(!empty($_POST["elem_details_od_Artifact"])) {$details_od  .= $_POST["elem_details_od_Artifact"]."!,! ";} 
		if(!empty($_POST["elem_details_od_other"])) {$details_od  .= imw_real_escape_string($_POST["elem_details_od_other"]).""; }
		
		$disc_area_od ="";
		if(!empty($_POST["elem_discarea_od"])) {$disc_area_od .= imw_real_escape_string($_POST["elem_discarea_od"]).""; }
		
		$disc_size_od ="";
		if(!empty($_POST["elem_discsize_od_Large"])) {$disc_size_od .= $_POST["elem_discsize_od_Large"].", "; }
		if(!empty($_POST["elem_discsize_od_Avg"])) {$disc_size_od .= $_POST["elem_discsize_od_Avg"].", "; }
		if(!empty($_POST["elem_discsize_od_Small"])) {$disc_size_od .= $_POST["elem_discsize_od_Small"].", "; }
		
		$verti_cd_od ="";
		if(!empty($_POST["elem_verti_cd_od"])) {$verti_cd_od .= imw_real_escape_string($_POST["elem_verti_cd_od"]).""; }
		
		$disc_edema_od ="";   
		if(!empty($_POST["elem_discedema_od_No"])) {$disc_edema_od .= $_POST["elem_discedema_od_No"].", "; }
		if(!empty($_POST["elem_discedema_od_Mild"])) {$disc_edema_od .= $_POST["elem_discedema_od_Mild"].", "; }
		if(!empty($_POST["elem_discedema_od_Md"])) {$disc_edema_od .= $_POST["elem_discedema_od_Md"].", "; }
		if(!empty($_POST["elem_discedema_od_Severe"])) {$disc_edema_od .= $_POST["elem_discedema_od_Severe"].", "; }
		if(!empty($_POST["elem_discedema_od_Sup"])) {$disc_edema_od .= $_POST["elem_discedema_od_Sup"].", "; }
		if(!empty($_POST["elem_discedema_od_Inf"])) {$disc_edema_od .= $_POST["elem_discedema_od_Inf"].", "; }
		
		$rnfl_od = imw_real_escape_string($_POST["elem_rnfl_od_Avg"]); 
		
		$contour_overall_od ="";
		if(!empty($_POST["elem_contour_overall_od_NL"])) {$contour_overall_od .= $_POST["elem_contour_overall_od_NL"].", "; }
		if(!empty($_POST["elem_contour_overall_od_Thin"])) {$contour_overall_od .= $_POST["elem_contour_overall_od_Thin"].", "; }
		if(!empty($_POST["elem_contour_overall_od_VeryThin"])) {$contour_overall_od .= $_POST["elem_contour_overall_od_VeryThin"].", "; }
		if(!empty($_POST["elem_contour_overall_od_Thick"])) {$contour_overall_od .= $_POST["elem_contour_overall_od_Thick"].", "; }
		if(!empty($_POST["elem_contour_overall_od_BL"])) {$contour_overall_od .= $_POST["elem_contour_overall_od_BL"].", "; }
		
		$contour_superior_od ="";
		if(!empty($_POST["elem_contour_superior_od_NL"])) {$contour_superior_od .= $_POST["elem_contour_superior_od_NL"].", "; }
		if(!empty($_POST["elem_contour_superior_od_Thin"])) {$contour_superior_od .= $_POST["elem_contour_superior_od_Thin"].", "; }
		if(!empty($_POST["elem_contour_superior_od_VeryThin"])) {$contour_superior_od .= $_POST["elem_contour_superior_od_VeryThin"].", "; }
		if(!empty($_POST["elem_contour_superior_od_Thick"])) {$contour_superior_od .= $_POST["elem_contour_superior_od_Thick"].", "; }
		if(!empty($_POST["elem_contour_superior_od_BL"])) {$contour_superior_od .= $_POST["elem_contour_superior_od_BL"].", "; }
		
		$contour_inferior_od ="";
		if(!empty($_POST["elem_contour_inferior_od_NL"])) {$contour_inferior_od .= $_POST["elem_contour_inferior_od_NL"].", "; }
		if(!empty($_POST["elem_contour_inferior_od_Thin"])) {$contour_inferior_od .= $_POST["elem_contour_inferior_od_Thin"].", "; }
		if(!empty($_POST["elem_contour_inferior_od_VeryThin"])) {$contour_inferior_od .= $_POST["elem_contour_inferior_od_VeryThin"].", "; }
		if(!empty($_POST["elem_contour_inferior_od_Thick"])) {$contour_inferior_od .= $_POST["elem_contour_inferior_od_Thick"].", "; }
		if(!empty($_POST["elem_contour_inferior_od_BL"])) {$contour_inferior_od .= $_POST["elem_contour_inferior_od_BL"].", "; }
		
		$contour_temporal_od ="";
		if(!empty($_POST["elem_contour_temporal_od_NL"])) {$contour_temporal_od .= $_POST["elem_contour_temporal_od_NL"].", "; }
		if(!empty($_POST["elem_contour_temporal_od_Thin"])) {$contour_temporal_od .= $_POST["elem_contour_temporal_od_Thin"].", "; }
		if(!empty($_POST["elem_contour_temporal_od_VeryThin"])) {$contour_temporal_od .= $_POST["elem_contour_temporal_od_VeryThin"].", "; } 
		if(!empty($_POST["elem_contour_temporal_od_Thick"])) {$contour_temporal_od .= $_POST["elem_contour_temporal_od_Thick"].", "; }
		if(!empty($_POST["elem_contour_temporal_od_BL"])) {$contour_temporal_od .= $_POST["elem_contour_temporal_od_BL"].", "; }
		
		$contour_nasal_od ="";
		if(!empty($_POST["elem_contour_nasal_od_NL"])) {$contour_nasal_od .= $_POST["elem_contour_nasal_od_NL"].", "; }
		if(!empty($_POST["elem_contour_nasal_od_Thin"])) {$contour_nasal_od .= $_POST["elem_contour_nasal_od_Thin"].", "; }
		if(!empty($_POST["elem_contour_nasal_od_VeryThin"])) {$contour_nasal_od .= $_POST["elem_contour_nasal_od_VeryThin"].", "; } 
		if(!empty($_POST["elem_contour_nasal_od_Thick"])) {$contour_nasal_od .= $_POST["elem_contour_nasal_od_Thick"].", "; }
		if(!empty($_POST["elem_contour_nasal_od_BL"])) {$contour_nasal_od .= $_POST["elem_contour_nasal_od_BL"].", "; }
		
		$contour_gcc_od ="";
		if(!empty($_POST["elem_contour_gcc_od_NL"])) {$contour_gcc_od .= $_POST["elem_contour_gcc_od_NL"].", "; }
		if(!empty($_POST["elem_contour_gcc_od_Thin"])) {$contour_gcc_od .= $_POST["elem_contour_gcc_od_Thin"].", "; }
		if(!empty($_POST["elem_contour_gcc_od_VeryThin"])) {$contour_gcc_od .= $_POST["elem_contour_gcc_od_VeryThin"].", "; } 
		
		if(!empty($_POST["elem_contour_gcc_od_Thick"])) {$contour_gcc_od .= $_POST["elem_contour_gcc_od_Thick"].", "; }
		if(!empty($_POST["elem_contour_gcc_od_BL"])) {$contour_gcc_od .= $_POST["elem_contour_gcc_od_BL"].", "; }   
		
		if(!empty($_POST["elem_symmertric_od_Yes"])){$symmetric_od = $_POST["elem_symmertric_od_Yes"]; }
		else if(!empty($_POST["elem_symmertric_od_No"])){$symmetric_od = $_POST["elem_symmertric_od_No"]; }
		
		$synthesis_od = imw_real_escape_string($_POST["elem_interpret_systhesis_od"]); 
		
		if(!empty($_POST["elem_gpa_od_No"])){$gpa_od = $_POST["elem_gpa_od_No"]; }
		else if(!empty($_POST["elem_gpa_od_pos"])){$gpa_od = $_POST["elem_gpa_od_pos"]; }
		else if(!empty($_POST["elem_gpa_od_lp"])){$gpa_od = $_POST["elem_gpa_od_lp"]; }
		
		//OS
		$signal_strength_os = imw_real_escape_string($_POST["elem_signal_strength_os"]); 
		
		$quality_os  = "";
		if(!empty($_POST["elem_quality_os_gd"])){ $quality_os  .= $_POST["elem_quality_os_gd"].", "; }
		if(!empty($_POST["elem_quality_os_adequate"])){ $quality_os  .= $_POST["elem_quality_os_adequate"].", "; }
		if(!empty($_POST["elem_quality_os_poor"])){ $quality_os  .= $_POST["elem_quality_os_poor"];}
		
		$details_os="";
		if(!empty($_POST["elem_details_os_AlgoFail"])) {$details_os  .= $_POST["elem_details_os_AlgoFail"]."!,! "; }
		if(!empty($_POST["elem_details_os_MediaOpacity"])) {$details_os  .= $_POST["elem_details_os_MediaOpacity"]."!,! "; }
		if(!empty($_POST["elem_details_os_Artifact"])) {$details_os  .= $_POST["elem_details_os_Artifact"]."!,! ";} 
		if(!empty($_POST["elem_details_os_other"])) {$details_os  .= imw_real_escape_string($_POST["elem_details_os_other"]).""; }
		
		$disc_area_os ="";
		if(!empty($_POST["elem_discarea_os"])) {$disc_area_os .= imw_real_escape_string($_POST["elem_discarea_os"]).""; }   
		
		$disc_size_os ="";
		if(!empty($_POST["elem_discsize_os_Large"])) {$disc_size_os .= $_POST["elem_discsize_os_Large"].", "; }
		if(!empty($_POST["elem_discsize_os_Avg"])) {$disc_size_os .= $_POST["elem_discsize_os_Avg"].", "; }
		if(!empty($_POST["elem_discsize_os_Small"])) {$disc_size_os .= $_POST["elem_discsize_os_Small"].", "; }
		
		$verti_cd_os ="";
		if(!empty($_POST["elem_verti_cd_os"])) {$verti_cd_os .= imw_real_escape_string($_POST["elem_verti_cd_os"]).""; }
		
		$disc_edema_os ="";   
		if(!empty($_POST["elem_discedema_os_No"])) {$disc_edema_os .= $_POST["elem_discedema_os_No"].", "; }
		if(!empty($_POST["elem_discedema_os_Mild"])) {$disc_edema_os .= $_POST["elem_discedema_os_Mild"].", "; }
		if(!empty($_POST["elem_discedema_os_Md"])) {$disc_edema_os .= $_POST["elem_discedema_os_Md"].", "; }
		if(!empty($_POST["elem_discedema_os_Severe"])) {$disc_edema_os .= $_POST["elem_discedema_os_Severe"].", "; }
		if(!empty($_POST["elem_discedema_os_Sup"])) {$disc_edema_os .= $_POST["elem_discedema_os_Sup"].", "; }
		if(!empty($_POST["elem_discedema_os_Inf"])) {$disc_edema_os .= $_POST["elem_discedema_os_Inf"].", "; }
		
		$rnfl_os = imw_real_escape_string($_POST["elem_rnfl_os_Avg"]); 
		
		$contour_overall_os ="";
		if(!empty($_POST["elem_contour_overall_os_NL"])) {$contour_overall_os .= $_POST["elem_contour_overall_os_NL"].", "; }
		if(!empty($_POST["elem_contour_overall_os_Thin"])) {$contour_overall_os .= $_POST["elem_contour_overall_os_Thin"].", "; }
		if(!empty($_POST["elem_contour_overall_os_VeryThin"])) {$contour_overall_os .= $_POST["elem_contour_overall_os_VeryThin"].", "; }
		if(!empty($_POST["elem_contour_overall_os_Thick"])) {$contour_overall_os .= $_POST["elem_contour_overall_os_Thick"].", "; }
		if(!empty($_POST["elem_contour_overall_os_BL"])) {$contour_overall_os .= $_POST["elem_contour_overall_os_BL"].", "; }
		
		$contour_superior_os ="";
		if(!empty($_POST["elem_contour_superior_os_NL"])) {$contour_superior_os .= $_POST["elem_contour_superior_os_NL"].", "; }
		if(!empty($_POST["elem_contour_superior_os_Thin"])) {$contour_superior_os .= $_POST["elem_contour_superior_os_Thin"].", "; }
		if(!empty($_POST["elem_contour_superior_os_VeryThin"])) {$contour_superior_os .= $_POST["elem_contour_superior_os_VeryThin"].", "; }
		if(!empty($_POST["elem_contour_superior_os_Thick"])) {$contour_superior_os .= $_POST["elem_contour_superior_os_Thick"].", "; }
		if(!empty($_POST["elem_contour_superior_os_BL"])) {$contour_superior_os .= $_POST["elem_contour_superior_os_BL"].", "; }
		
		$contour_inferior_os ="";
		if(!empty($_POST["elem_contour_inferior_os_NL"])) {$contour_inferior_os .= $_POST["elem_contour_inferior_os_NL"].", "; }
		if(!empty($_POST["elem_contour_inferior_os_Thin"])) {$contour_inferior_os .= $_POST["elem_contour_inferior_os_Thin"].", "; }
		if(!empty($_POST["elem_contour_inferior_os_VeryThin"])) {$contour_inferior_os .= $_POST["elem_contour_inferior_os_VeryThin"].", "; }
		if(!empty($_POST["elem_contour_inferior_os_Thick"])) {$contour_inferior_os .= $_POST["elem_contour_inferior_os_Thick"].", "; }
		if(!empty($_POST["elem_contour_inferior_os_BL"])) {$contour_inferior_os .= $_POST["elem_contour_inferior_os_BL"].", "; }
		
		$contour_temporal_os ="";
		if(!empty($_POST["elem_contour_temporal_os_NL"])) {$contour_temporal_os .= $_POST["elem_contour_temporal_os_NL"].", "; }
		if(!empty($_POST["elem_contour_temporal_os_Thin"])) {$contour_temporal_os .= $_POST["elem_contour_temporal_os_Thin"].", "; }
		if(!empty($_POST["elem_contour_temporal_os_VeryThin"])) {$contour_temporal_os .= $_POST["elem_contour_temporal_os_VeryThin"].", "; } 
		if(!empty($_POST["elem_contour_temporal_os_Thick"])) {$contour_temporal_os .= $_POST["elem_contour_temporal_os_Thick"].", "; }
		if(!empty($_POST["elem_contour_temporal_os_BL"])) {$contour_temporal_os .= $_POST["elem_contour_temporal_os_BL"].", "; }
		
		$contour_nasal_os ="";
		if(!empty($_POST["elem_contour_nasal_os_NL"])) {$contour_nasal_os .= $_POST["elem_contour_nasal_os_NL"].", "; }
		if(!empty($_POST["elem_contour_nasal_os_Thin"])) {$contour_nasal_os .= $_POST["elem_contour_nasal_os_Thin"].", "; }
		if(!empty($_POST["elem_contour_nasal_os_VeryThin"])) {$contour_nasal_os .= $_POST["elem_contour_nasal_os_VeryThin"].", "; } 
		if(!empty($_POST["elem_contour_nasal_os_Thick"])) {$contour_nasal_os .= $_POST["elem_contour_nasal_os_Thick"].", "; }
		if(!empty($_POST["elem_contour_nasal_os_BL"])) {$contour_nasal_os .= $_POST["elem_contour_nasal_os_BL"].", "; }
		
		$contour_gcc_os ="";
		if(!empty($_POST["elem_contour_gcc_os_NL"])) {$contour_gcc_os .= $_POST["elem_contour_gcc_os_NL"].", "; }
		if(!empty($_POST["elem_contour_gcc_os_Thin"])) {$contour_gcc_os .= $_POST["elem_contour_gcc_os_Thin"].", "; }
		if(!empty($_POST["elem_contour_gcc_os_VeryThin"])) {$contour_gcc_os .= $_POST["elem_contour_gcc_os_VeryThin"].", "; } 
		if(!empty($_POST["elem_contour_gcc_os_Thick"])) {$contour_gcc_os .= $_POST["elem_contour_gcc_os_Thick"].", "; }
		if(!empty($_POST["elem_contour_gcc_os_BL"])) {$contour_gcc_os .= $_POST["elem_contour_gcc_os_BL"].", "; }   
		
		if(!empty($_POST["elem_symmertric_os_Yes"])){$symmetric_os = $_POST["elem_symmertric_os_Yes"]; }
		else if(!empty($_POST["elem_symmertric_os_No"])){$symmetric_os = $_POST["elem_symmertric_os_No"]; }   
		
		$synthesis_os = imw_real_escape_string($_POST["elem_interpret_systhesis_os"]); 
		
		if(!empty($_POST["elem_gpa_os_No"])){$gpa_os = $_POST["elem_gpa_os_No"]; }
		else if(!empty($_POST["elem_gpa_os_pos"])){$gpa_os = $_POST["elem_gpa_os_pos"]; }
		else if(!empty($_POST["elem_gpa_os_lp"])){$gpa_os = $_POST["elem_gpa_os_lp"]; }
   
   		//Encounter Id
		if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
			$encounter_id = $_POST["elem_masterEncounterId"];
		}else{
			$encounter_id = $objTests->getEncounterId();
		}
		
		//TestRes
		$test_res_od = $test_res_os = "";
	
		//summary
		$summaryOd = $summaryOs = "";
		if(!empty($reliabilityOd)){$summaryOd .= "Reliability : ".$reliabilityOd."; ";}
		//if(!empty($signal_strength_od)){$summaryOd .= "Signal Strength ".$signal_strength_od."; ";}
		if(!empty($signal_strength_od)){$summaryOd .= "Signal Strength ".$signal_strength_od."; ";}
		if(!empty($quality_od)){$summaryOd .="Quality : ".$quality_od."; ";}
		if(!empty($details_od)){$summaryOd .="Details :  ".str_replace("!,!",",",$details_od)."; ";}
		if(!empty($disc_area_od)){$summaryOd .="Disc area : ".$disc_area_od."; ";}
		if(!empty($disc_size_od)){$summaryOd .="Disc size : ".$disc_size_od."; ";}
		if(!empty($verti_cd_od)){$summaryOd .="Vertical C:D : ".$verti_cd_od."; ";}
		if(!empty($disc_edema_od)){$summaryOd .="Disc edema :  ".$disc_edema_od."; ";}
		if(!empty($rnfl_od)){$summaryOd .="RNFL : Avg".$rnfl_od."&micro;; ";}
		if(!empty($contour_overall_od)){$summaryOd .="Overall : ".$contour_overall_od."; ";}
		if(!empty($contour_superior_od)){$summaryOd .="Superior : ".$contour_superior_od."; ";}
		if(!empty($contour_inferior_od)){$summaryOd .="Inferior : ".$contour_inferior_od."; ";}
		if(!empty($contour_temporal_od)){$summaryOd .="Temporal : ".$contour_temporal_od."; ";}
		if(!empty($contour_nasal_od)){$summaryOd .="Nasal : ".$contour_nasal_od."; ";}
		if(!empty($contour_gcc_od)){$summaryOd .="GCC : ".$contour_gcc_od."; ";}
		if(!empty($symmetric_od)){$summaryOd .="Symmetric : ".$symmetric_od."; ";}
		if(!empty($gpa_od)){$summaryOd .="GPA : ".$gpa_od."; ";}
		if(!empty($synthesis_od)){$summaryOd .="Synthesis : ".$synthesis_od."; ";}
		if(!empty($comments_od)){$summaryOd .="Comments : ".$comments_od."; ";}

		if(!empty($reliabilityOs)){$summaryOs .= "Reliability : ".$reliabilityOs."; ";}
		if(!empty($signal_strength_os)){$summaryOs .= "Signal Strength ".$signal_strength_os."; ";}
		if(!empty($quality_os)){$summaryOs .="Quality : ".$quality_os."; ";}
		if(!empty($details_os)){$summaryOs .="Details :  ".str_replace("!,!",",",$details_os)."; ";}
		if(!empty($disc_area_os)){$summaryOs .="Disc area : ".$disc_area_os."; ";}
		if(!empty($verti_cd_os)){$summaryOs .="Vertical C:D : ".$verti_cd_os."; ";}
		if(!empty($disc_size_os)){$summaryOs .="Disc size : ".$disc_size_os."; ";}
		if(!empty($disc_edema_os)){$summaryOs .="Disc edema :  ".$disc_edema_os."; ";}
		if(!empty($rnfl_os)){$summaryOs .="RNFL : Avg".$rnfl_os."&micro;; ";}
		if(!empty($contour_overall_os)){$summaryOs .="Overall : ".$contour_overall_os."; ";}
		if(!empty($contour_superior_os)){$summaryOs .="Superior : ".$contour_superior_os."; ";}
		if(!empty($contour_inferior_os)){$summaryOs .="Inferior : ".$contour_inferior_os."; ";}
		if(!empty($contour_temporal_os)){$summaryOs .="Temporal : ".$contour_temporal_os."; ";}
		if(!empty($contour_nasal_os)){$summaryOs .="Nasal : ".$contour_nasal_os."; ";}
		if(!empty($contour_gcc_os)){$summaryOs .="GCC : ".$contour_gcc_os."; ";}
		if(!empty($symmetric_os)){$summaryOs .="Symmetric : ".$symmetric_os."; ";}
		if(!empty($gpa_os)){$summaryOs .="GPA : ".$gpa_os."; ";}
		if(!empty($synthesis_os)){$summaryOs .="Synthesis : ".$synthesis_os."; ";}
		if(!empty($comments_os)){$summaryOs .="Comments : ".$comments_os."; ";}

		$descOd = (!empty($summaryOd)) ? imw_real_escape_string("".$summaryOd) : "";
		$descOs = (!empty($summaryOs)) ? imw_real_escape_string("".$summaryOs) : "";

		//summary
		if(empty($oct_id)){
			//Check if scan doc id exists in session
			$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;
			if(isset($arrTest2edit["OCT-RNFL"]) && !empty($arrTest2edit["OCT-RNFL"])){
				//Check in db
				$cQry = "select oct_rnfl_id FROM oct_rnfl
						WHERE patient_id ='".$patient_id."'
						AND oct_rnfl_id = '".$arrTest2edit["OCT-RNFL"]."' ";
				$row = sqlQuery($cQry);
				$oct_id = (($row == false) || empty($row["oct_rnfl_id"])) ? "0" : $row["oct_rnfl_id"];
		
				//Unset Session OCT-RNFL
				$arrTest2edit["OCT-RNFL"] = "";
				$arrTest2edit["OCT-RNFL"] = NULL;
				unset($arrTest2edit["OCT-RNFL"]);
				//Reset session
				$_SESSION["test2edit"] = serialize($arrTest2edit);
			}else{
				$cQry = "SELECT oct_rnfl_id FROM oct_rnfl
						WHERE patient_id='".$patient_id."' AND examDate = '".$examDate."' AND examTime = '".$examTime."' ";
				$row = sqlQuery($cQry);
				$oct_id = (($row == false) || empty($row["oct_rnfl_id"])) ? "0" : $row["oct_rnfl_id"];
			}
		}

		if(empty($oct_id)){
			$phyName=($zeissAction)?0:$phyName; /*Mark test uninterpreted if "Forum Add"*/
			$sql = "INSERT INTO oct_rnfl ".
			"( ".
			"oct_rnfl_id, scanLaserOct_rnfl, scanLaserEye, performBy, diagnosis, ptUndersatnding, ".
			"reliabilityOd, reliabilityOs, scanLaserOd, scanLaserOs, descOd, descOs, ".
			"stable, fuApa, ptInformed, comments, signature, phyName, examDate, ".			
			
			"patient_id, form_id, diagnosisOther, monitorIOP,tech2InformPt, ".			
			
			"techComments, ".
			"ptInformedNv,examTime,contiMeds, ".
			"test_res_od, test_res_os, ".
			"encounter_id,ordrby,ordrdt, ".
			
			"signal_strength_od, quality_od, details_od, disc_area_od,
			disc_size_od, disc_edema_od, rnfl_od, contour_overall_od, 
			contour_superior_od, contour_inferior_od, contour_temporal_od, 
			symmetric_od, synthesis_od, ".
			/*"notes_od, stable_od, improved_od,
			worse_od, comments_od, ".*/
			
			"signal_strength_os, quality_os, details_os, disc_area_os,
			disc_size_os, disc_edema_os, rnfl_os, contour_overall_os, 
			contour_superior_os, contour_inferior_os, contour_temporal_os, 
			symmetric_os, synthesis_os, ".
			/*
			"notes_os, stable_os, improved_os,
			worse_os, comments_os,  ".*/
			"improve, worse, dilated, testTime, ".
			"findingDiscusPt, ".
			" diagnosis_os, diagnosisOther_os,	
				interpretation_OD, interpretation_OS, comments_interp, 
				glaucoma_stage_opt_OD, glaucoma_stage_opt_OS, plan, repeatTestNxtVstEye,
				repeatTestVal1, repeatTestVal2, repeatTestEye, comments_plan, forum_procedure,
				verti_cd_od,verti_cd_os,contour_nasal_od,contour_gcc_od,
				contour_nasal_os,contour_gcc_os,gpa_od,gpa_os
				".$signPathFieldName.$signDTmFieldName.
			") ".
			"VALUES ".
			"( ".
			"'', '".$scanLaserOct."', '".$scanLaserEye."', '".$performBy."', '".$diagnosis."', '".$ptUndersatnding."', ".
			"'".$reliabilityOd."', '".$reliabilityOs."', '".$scanLaserOd."', '".$scanLaserOs."', '".$descOd."', '".$descOs."', ".
			"'".$stable."', '".$fuApa."', '".$ptInformed."', '".$comments."', '".$signature."', '".$phyName."', '".$examDate."', ".			
			
			"'".$patient_id."', '".$form_id."', '".$diagnosisOther."', '".$monitorIOP."', '".$tech2InformPt."', ".			
			
			"'".$elem_techComments."', ".
			"'".$ptInformedNv."','".$examTime."','".$contiMeds."', ".
			"'".$test_res_od."', '".$test_res_os."', ".
			
			"'".$encounter_id."','".$ordrby."','".$ordrdt."', ".
			
			"'".$signal_strength_od."', '".$quality_od."', '".$details_od."', '".$disc_area_od."', ".
			"'".$disc_size_od."', '".$disc_edema_od."', '".$rnfl_od."', '".$contour_overall_od."', ".
			"'".$contour_superior_od."', '".$contour_inferior_od."', '".$contour_temporal_od."', ". 
			"'".$symmetric_od."', '".$synthesis_od."', ".
			/*"'".$notes_od."', '".$stable_od."', '".$improved_od."', ".
			"'".$worse_od."', '".$comments_od."', ".*/
			
			"'".$signal_strength_os."', '".$quality_os."', '".$details_os."', '".$disc_area_os."', ".
			"'".$disc_size_os."', '".$disc_edema_os."', '".$rnfl_os."', '".$contour_overall_os."', ".
			"'".$contour_superior_os."', '".$contour_inferior_os."', '".$contour_temporal_os."', ". 
			"'".$symmetric_os."', '".$synthesis_os."', ".
			/*
			"'".$notes_os."', '".$stable_os."', '".$improved_os."', ".
			"'".$worse_os."', '".$comments_os."', ".
			*/
			"'".$improve."', '".$worse."', '".$dilated."', '".$testTime."', ".
			"'".$findingDiscusPt."', ".
			"'".$diagnosis_os."', '".$diagnosisOther_os."', ".
			"'".$interpretation_OD."', '".$interpretation_OS."', '".$comments_interp."', ".
			"'".$glaucoma_stage_opt_OD."', '".$glaucoma_stage_opt_OS."', '".$plan."', '".$repeatTestNxtVstEye."', ".
			"'".$repeatTestVal1."', '".$repeatTestVal2."', '".$repeatTestEye."', '".$comments_plan."', '".$forum_procedure."', ".
			"'".$verti_cd_od."', '".$verti_cd_os."', '".$contour_nasal_od."', '".$contour_gcc_od."', ".
			"'".$contour_nasal_os."', '".$contour_gcc_os."', '".$gpa_od."', '".$gpa_os."' ".
			$signPathFieldValue.$signDTmFieldValue.
			") ";	
			$Insert_res	= imw_query($sql);
			$InsertId	= imw_insert_id();
			$oct_id = $InsertId;
		}else if(!empty($oct_id)){
			$sql = "UPDATE oct_rnfl ".
			"SET ".
			"scanLaserOct_rnfl = '".$scanLaserOct."', ".
			"scanLaserEye = '".$scanLaserEye."', ".
			"performBy = '".$performBy."', ".
			"diagnosis = '".$diagnosis."', ".
			"ptUndersatnding = '".$ptUndersatnding."', ".
			"reliabilityOd = '".$reliabilityOd."', ".
			"reliabilityOs = '".$reliabilityOs."', ".
			"scanLaserOd = '".$scanLaserOd."', ".
			"scanLaserOs = '".$scanLaserOs."', ".
			"descOd = '".$descOd."', ".
			
			"descOs = '".$descOs."', ".
			"stable = '".$stable."', ".
			"fuApa = '".$fuApa."', ".
			"ptInformed = '".$ptInformed."', ".
			"comments = '".$comments."', ".
			"signature = '".$signature."', ";
					
			if(!$zeissAction){  /*Prevent changing interpretation status if "Forum Add/Delete"*/
				$sql .= "phyName = '".$phyName."', ";
			}
		
			$sql .= "examDate = '".$examDate."', ".
			"monitorIOP = '".$monitorIOP."', ".
			//"patient_id = '".$patient_id."', ".
			"tech2InformPt = '".$tech2InformPt."', ".
			"diagnosisOther = '".$diagnosisOther."', ".
			
			"techComments='".$elem_techComments."', ".
			"ptInformedNv='".$ptInformedNv."', ".
			"examTime ='".$examTime."', ".
			"contiMeds = '".$contiMeds."', ".
			"test_res_od = '".$test_res_od."', ".
			"test_res_os = '".$test_res_os."', ".
			"encounter_id='".$encounter_id."', ".
			"ordrby='".$ordrby."',ordrdt='".$ordrdt."', ".
			
			"signal_strength_od = '".$signal_strength_od."',
			quality_od = '".$quality_od."',
			details_od = '".$details_od."',
			disc_area_od = '".$disc_area_od."',
			disc_size_od = '".$disc_size_od."',
			disc_edema_od = '".$disc_edema_od."',
			rnfl_od = '".$rnfl_od."',
			contour_overall_od = '".$contour_overall_od."',
			contour_superior_od = '".$contour_superior_od."',
			contour_inferior_od = '".$contour_inferior_od."',
			contour_temporal_od = '".$contour_temporal_od."',
			symmetric_od = '".$symmetric_od."',
			synthesis_od = '".$synthesis_od."', ".
			/*"notes_od = '".$notes_od."',
			stable_od = '".$stable_od."',
			improved_od = '".$improved_od."',
			worse_od = '".$worse_od."',
			comments_od = '".$comments_od."',  ".*/
			
			"signal_strength_os = '".$signal_strength_os."',
			quality_os = '".$quality_os."',
			details_os = '".$details_os."',
			disc_area_os = '".$disc_area_os."',
			disc_size_os = '".$disc_size_os."',
			disc_edema_os = '".$disc_edema_os."',
			rnfl_os = '".$rnfl_os."',
			contour_overall_os = '".$contour_overall_os."',
			contour_superior_os = '".$contour_superior_os."',
			contour_inferior_os = '".$contour_inferior_os."',
			contour_temporal_os = '".$contour_temporal_os."',
			symmetric_os = '".$symmetric_os."',
			synthesis_os = '".$synthesis_os."', ".
			"improve = '".$improve."',
			worse = '".$worse."',
			dilated = '".$dilated."',
			testTime = '".$testTime."', ".
			/*
			"notes_os = '".$notes_os."',
			stable_os = '".$stable_os."',
			improved_os = '".$improved_os."',
			worse_os = '".$worse_os."',
			comments_os = '".$comments_os."',  ".	
			*/
			"findingDiscusPt='".$findingDiscusPt."', ".
			"diagnosis_os='".$diagnosis_os."', 
			diagnosisOther_os='".$diagnosisOther_os."', ".
			"interpretation_OD='".$interpretation_OD."', 
			interpretation_OS='".$interpretation_OS."',
			comments_interp='".$comments_interp."', ".
			"glaucoma_stage_opt_OD='".$glaucoma_stage_opt_OD."', 
			glaucoma_stage_opt_OS='".$glaucoma_stage_opt_OS."', 
			plan='".$plan."', 
			repeatTestNxtVstEye='".$repeatTestNxtVstEye."', ".
			"repeatTestVal1='".$repeatTestVal1."', 
			repeatTestVal2='".$repeatTestVal2."', 
			repeatTestEye='".$repeatTestEye."',
            forum_procedure='".$forum_procedure."',
			 ".$signPathQry.
			 $signDTmQry."
			
			comments_plan='".$comments_plan."', 
			verti_cd_od='".$verti_cd_od."',
			verti_cd_os='".$verti_cd_os."', 
			contour_nasal_od='".$contour_nasal_od."', 
			contour_gcc_od='".$contour_gcc_od."', 
			contour_nasal_os='".$contour_nasal_os."', 
			contour_gcc_os='".$contour_gcc_os."', 
			gpa_od='".$gpa_od."', 
			gpa_os='".$gpa_os."' ".
			
			//"WHERE form_id = '".$form_id."' ";
			"WHERE oct_rnfl_id = '".$oct_id."' ";			
			$res = sqlQuery($sql);
		}

		/*code, Purpose: Generate HL7 message for Test*/
		$zeissMsgType="OCT-RNFL";
		$zeissPatientId = $patient_id;
		$zeissTestId = $oct_id;
		include("./zeissTestHl7.php");
		/*End code*/

		/*--interpretation starts--*/
		$objTests->interpret_if_scan_exists($_SESSION['patient'], "OCT-RNFL", $oct_id);
		/*--interpretation end--*/

		//Super-Bill -----------
		if(!empty($ordrby) && !empty($oct_id)){
			$arIn=array();			
			$arIn["elem_physicianId"]=$ordrby;
			$arIn["doctorId"]=$ordrby;
			$arIn["caseId"]=$_POST["elem_masterCaseId"];
			$arIn["encounterId"]=$encounter_id;
			$arIn["date_of_service"]=$examDate;
			$arIn["sb_testId"]=$oct_id;
			$arIn["sb_testName"]="OCT-RNFL";
                        $arIn["form_id"]=$form_id;
                        $arIn["test_interpreted"]= ((int)$phyName > 0)? true : false;

			$oSuperbillSaver = new SuperbillSaver($patient_id);
			$oSuperbillSaver->save($arIn);
		}
		//Super-Bill -----------
		
		//save tests Pdf
		$objTests->saveTestPdfExe_2($patient_id,$oct_id,"OCT-RNFL");

		//FormId updates other tables -----
		if(!empty($form_id)){
			// Make chart notes valid
		$oChartNote->makeChartNotesValid($form_id);	
		}
		//FormId updates other tables -----

		//Redirect
		echo "<script>".
				"
				try{";
					if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
						$afterSaveinPopup = 'yes';
					}else{
						$afterSaveinPopup = 'no';
						echo "top.alert_notification_show(\"Test has been saved.\");";
					}
					echo "
					window.location.replace(\"test_oct_rnfl.php?noP=".$elem_noP."&tId=".$oct_id."&pop=".$_REQUEST["pop"]."&doNotShowRightSide=".$_REQUEST["doNotShowRightSide"]."&afterSaveinPopup=".$afterSaveinPopup."\");
				}catch(err){}".
			 "</script>";
		exit();
		break;
	}
	case "A-SCAN":{
		$saveSurgical = $_REQUEST['saveSurgical'];
		if($saveSurgical == "true"){
			$surgical_id = $_REQUEST['surgical_id'];
			$formId = $_REQUEST['form_id'];
			
			//start signature work
			$tmpDirPth_up = $oSaveFile->upDir;
			$signatureDateTime = date('Y-m-d H:i:s');
			$hidd_prev_signature_path = $_POST["hidd_prev_signature_path"];
			$hidd_signature_path = $_POST["hidd_signature_path"];
			$signPathQry=$signDTmQry=$signDTmFieldName=$signDTmFieldValue=$signPathFieldName=$signPathFieldValue="";
			$boolSignDtTm = false;
			if($hidd_prev_signature_path && $hidd_signature_path && $hidd_prev_signature_path!=$hidd_signature_path) {
				$hidd_prev_signature_real_path=realpath($tmpDirPth_up.$hidd_prev_signature_path);
				if(file_exists($hidd_prev_signature_real_path)) {
					@unlink($hidd_prev_signature_real_path);
				}
				$boolSignDtTm = true;
			}
			if($hidd_signature_path && !$hidd_prev_signature_path) {
				$boolSignDtTm = true;
			}
			if($boolSignDtTm == true) {
				$signDTmQry = " sign_path_date_time='".$signatureDateTime."', ";
				$signDTmFieldName = " ,sign_path_date_time ";
				$signDTmFieldValue = " ,'".$signatureDateTime."' ";
			}
			if($hidd_signature_path) {
				$signPathQry = " sign_path='".$hidd_signature_path."', ";
				$signPathFieldName = " ,sign_path ";
				$signPathFieldValue = " ,'".$hidd_signature_path."' ";
				
			}
			//end signature work		
			
			$mrSOD = imw_real_escape_string($_REQUEST['mrSOD']);
			$mrCOD = imw_real_escape_string($_REQUEST['mrCOD']);
			$mrAOD = imw_real_escape_string($_REQUEST['mrAOD']);
			$visionOD = imw_real_escape_string($_REQUEST['visionOD']);
			$glareOD = imw_real_escape_string($_REQUEST['glareOD']);
			$performedByOD = imw_real_escape_string($_REQUEST['performedByOD']);
			$dateOD = getDateFormatDB(imw_real_escape_string($_REQUEST['dateOD']));
				//list($dateODMonth, $dateODDay, $dateODYear) = split("-", $dateOD);
				//$dateOD = $dateODYear."-".$dateODMonth."-".$dateODDay;
			$autoSelectOD = $_REQUEST['autoSelectOD'];
				if($autoSelectOD!=''){
					$autoSelectOD = imw_real_escape_string($objTests->getKheadingId($autoSelectOD));
				}
			$iolMasterSelectOD = $_REQUEST['iolMasterSelectOD'];
				if($iolMasterSelectOD!=''){
					$iolMasterSelectOD = imw_real_escape_string($objTests->getKheadingId($iolMasterSelectOD));
				}
			$topographerSelectOD = $_REQUEST['topographerSelectOD'];
				if($topographerSelectOD!=''){
					$topographerSelectOD = imw_real_escape_string($objTests->getKheadingId($topographerSelectOD));
				}
			$k1Auto1OD = imw_real_escape_string($_REQUEST['k1Auto1OD']);
			$k1Auto2OD = imw_real_escape_string($_REQUEST['k1Auto2OD']);
			$k1IolMaster1OD = imw_real_escape_string($_REQUEST['k1IolMaster1OD']);
			$k1IolMaster2OD = imw_real_escape_string($_REQUEST['k1IolMaster2OD']);
			$k1Topographer1OD = imw_real_escape_string($_REQUEST['k1Topographer1OD']);
			$k1Topographer2OD = imw_real_escape_string($_REQUEST['k1Topographer2OD']);
			$k2Auto1OD = imw_real_escape_string($_REQUEST['k2Auto1OD']);
			$k2Auto2OD = imw_real_escape_string($_REQUEST['k2Auto2OD']);
			$k2IolMaster1OD = imw_real_escape_string($_REQUEST['k2IolMaster1OD']);
			$k2IolMaster2OD = imw_real_escape_string($_REQUEST['k2IolMaster2OD']);
			$k2Topographer1OD = imw_real_escape_string($_REQUEST['k2Topographer1OD']);
			$k2Topographer2OD = imw_real_escape_string($_REQUEST['k2Topographer2OD']);
			$cylAuto1OD = imw_real_escape_string($_REQUEST['cylAuto1OD']);
			$cylAuto2OD = imw_real_escape_string($_REQUEST['cylAuto2OD']);
			$cylIolMaster1OD = imw_real_escape_string($_REQUEST['cylIolMaster1OD']);
			$cylIolMaster2OD = imw_real_escape_string($_REQUEST['cylIolMaster2OD']);
			$cylTopographer1OD = imw_real_escape_string($_REQUEST['cylTopographer1OD']);
			$cylTopographer2OD = imw_real_escape_string($_REQUEST['cylTopographer2OD']);
			$aveAutoOD = imw_real_escape_string($_REQUEST['aveAutoOD']);
			$aveIolMasterOD = imw_real_escape_string($_REQUEST['aveIolMasterOD']);
			$aveTopographerOD = imw_real_escape_string($_REQUEST['aveTopographerOD']);
			$contactLengthOD = imw_real_escape_string($_REQUEST['contactLengthOD']);
			$immersionLengthOD = imw_real_escape_string($_REQUEST['immersionLengthOD']);
			$iolMasterLengthOD = imw_real_escape_string($_REQUEST['iolMasterLengthOD']);
			$contactNotesOD = imw_real_escape_string($_REQUEST['contactNotesOD']);
			$immersionNotesOD = imw_real_escape_string($_REQUEST['immersionNotesOD']);
			$iolMasterNotesOD = imw_real_escape_string($_REQUEST['iolMasterNotesOD']);
			$performedByPhyOD = imw_real_escape_string($_REQUEST['performedByPhyOD']);
			$powerIolOD = $_REQUEST['powerIolOD'];
				if($powerIolOD!=''){
					$powerIolOD = imw_real_escape_string($objTests->getFormulaHeadingId($powerIolOD));
				}
			$holladayOD = $_REQUEST['holladayOD'];
				if($holladayOD!=''){
					$holladayOD = imw_real_escape_string($objTests->getFormulaHeadingId($holladayOD));
				}
			$srk_tOD = $_REQUEST['srk_tOD'];
				if($srk_tOD!=''){
					$srk_tOD = imw_real_escape_string($objTests->getFormulaHeadingId($srk_tOD));
				}
			$hofferOD = $_REQUEST['hofferOD'];
				if($hofferOD!=''){
					$hofferOD = imw_real_escape_string($objTests->getFormulaHeadingId($hofferOD));
				}
			$iol1OD = $_REQUEST['iol1OD'];
				if($iol1OD!=''){
					$iol1OD = imw_real_escape_string($objTests->getLenseId($iol1OD));
				}
			$iol1PowerOD = imw_real_escape_string($_REQUEST['iol1PowerOD']);
			$iol1HolladayOD = imw_real_escape_string($_REQUEST['iol1HolladayOD']);
			$iol1srk_tOD = imw_real_escape_string($_REQUEST['iol1srk_tOD']);
			$iol1HofferOD = imw_real_escape_string($_REQUEST['iol1HofferOD']);
			$iol2OD = $_REQUEST['iol2OD'];
				if($iol2OD!=''){
					$iol2OD = imw_real_escape_string($objTests->getLenseId($iol2OD));
				}
			$iol2PowerOD = imw_real_escape_string($_REQUEST['iol2PowerOD']);
			$iol2HolladayOD = imw_real_escape_string($_REQUEST['iol2HolladayOD']);
			$iol2srk_tOD = imw_real_escape_string($_REQUEST['iol2srk_tOD']);
			$iol2HofferOD = imw_real_escape_string($_REQUEST['iol2HofferOD']);
			$iol3OD = $_REQUEST['iol3OD'];
				if($iol3OD!=''){
					$iol3OD = imw_real_escape_string($objTests->getLenseId($iol3OD));
				}
			$iol3PowerOD = imw_real_escape_string($_REQUEST['iol3PowerOD']);
			$iol3HolladayOD = imw_real_escape_string($_REQUEST['iol3HolladayOD']);
			$iol3srk_tOD = imw_real_escape_string($_REQUEST['iol3srk_tOD']);
			$iol3HofferOD = imw_real_escape_string($_REQUEST['iol3HofferOD']);
			$iol4OD = $_REQUEST['iol4OD'];
				if($iol4OD!=''){
					$iol4OD = imw_real_escape_string($objTests->getLenseId($iol4OD));
				}
			$iol4PowerOD = imw_real_escape_string($_REQUEST['iol4PowerOD']);
			$iol4HolladayOD = imw_real_escape_string($_REQUEST['iol4HolladayOD']);
			$iol4srk_tOD = imw_real_escape_string($_REQUEST['iol4srk_tOD']);
			$iol4HofferOD = imw_real_escape_string($_REQUEST['iol4HofferOD']);
			$notesOD = imw_real_escape_string($_REQUEST['notesOD']);
			$immersionNotesOD = imw_real_escape_string($_REQUEST['immersionNotesOD']);
			$iolMasterNotesOD = imw_real_escape_string($_REQUEST['iolMasterNotesOD']);
			$pachymetryValOD = imw_real_escape_string($_REQUEST['pachymetryValOD']);
			$pachymetryCorrecOD = imw_real_escape_string($_REQUEST['pachymetryCorrecOD']);
			$cornealDiamOD = imw_real_escape_string($_REQUEST['cornealDiamOD']);
			$dominantEyeOD = imw_real_escape_string($_REQUEST['dominantEyeOD']);
			$pupilSize1OD = imw_real_escape_string($_REQUEST['pupilSize1OD']);
			$pupilSize2OD = imw_real_escape_string($_REQUEST['pupilSize2OD']);
			$cataractOD = $_REQUEST['cataractOD'];
				if($cataractOD=='on'){
					$cataractOD = 1;
				}
			$astigmatismOD = $_REQUEST['astigmatismOD'];
				if($astigmatismOD=='on'){
					$astigmatismOD = 1;
				}
			$myopiaOD = $_REQUEST['myopiaOD'];
				if($myopiaOD=='on'){
					$myopiaOD = 1;
				}
			$selecedIOLsOD = imw_real_escape_string($_REQUEST['selecedIOLsOD']);
				//$selecedIOLsOD = getLenseId($selecedIOLsOD);
			$notesAssesmentPlansOD = imw_real_escape_string($_REQUEST['notesAssesmentPlansOD']);
			$lriOD = $_REQUEST['lriOD'];
				if($lriOD=="on"){ $lriOD = 1; }
			$dlOD = $_REQUEST['dlOD'];
				if($dlOD=="on"){ $dlOD = 1; }
			$synechiolysisOD = $_REQUEST['synechiolysisOD'];
				if($synechiolysisOD=="on"){ $synechiolysisOD = 1; }
			$irishooksOD = $_REQUEST['irishooksOD'];
				if($irishooksOD=="on"){ $irishooksOD = 1; }
			$trypanblueOD = $_REQUEST['trypanblueOD'];
				if($trypanblueOD=="on"){ $trypanblueOD = 1; }
			$flomaxOD = $_REQUEST['flomaxOD'];
				if($flomaxOD=="on"){ $flomaxOD = 1; }
	
			$cutsOD = imw_real_escape_string($_REQUEST['cutsOD']);
			$lengthCutsOD = imw_real_escape_string($_REQUEST['lengthCutsOD']);
			$lengthSelectOD = imw_real_escape_string($_REQUEST['lengthSelectOD']);
			$axisOD = imw_real_escape_string($_REQUEST['axisOD']);
	
			$superiorOD = $_REQUEST['superiorOD'];
				if($superiorOD=="on"){ $superiorOD = 1; }
			$inferiorOD = $_REQUEST['inferiorOD'];
				if($inferiorOD=="on"){ $inferiorOD = 1; }
			$nasalOD = $_REQUEST['nasalOD'];
				if($nasalOD=="on"){ $nasalOD = 1; }
			$temporalOD = $_REQUEST['temporalOD'];
				if($temporalOD=="on"){ $temporalOD = 1; }
			$STOD = $_REQUEST['STOD'];
				if($STOD=="on"){ $STOD = 1; }
			$SNOD = $_REQUEST['SNOD'];
				if($SNOD=="on"){ $SNOD = 1; }
			$ITOD = $_REQUEST['ITOD'];
				if($ITOD=="on"){ $ITOD = 1; }
			$INOD = $_REQUEST['INOD'];
				if($INOD=="on"){ $INOD = 1; }
			$vis_mr_os_s = imw_real_escape_string($_REQUEST['mrSOS']);
			$vis_mr_os_c = imw_real_escape_string($_REQUEST['mrCOS']);
			$vis_mr_os_a = imw_real_escape_string($_REQUEST['mrAOS']);
			$visionOS = imw_real_escape_string($_REQUEST['visionOS']);
			$glareOS = imw_real_escape_string($_REQUEST['glareOS']);
			$phyTechListOS = imw_real_escape_string($_REQUEST['phyTechListOS']);
			$dateOS = getDateFormatDB($_REQUEST['dateOS']);
				//list($dateOSMonth, $dateOSDay, $dateOSYear) = split("-", $dateOS);
				//$dateOS = $dateOSYear."-".$dateOSMonth."-".$dateOSDay;
	
			$autoSelectOS = $_REQUEST['autoSelectOS'];
				if($autoSelectOS!=''){
					$autoSelectOS = imw_real_escape_string($objTests->getKheadingId($autoSelectOS));
				}
			$iolMasterSelectOS = $_REQUEST['iolMasterSelectOS'];
				if($iolMasterSelectOS!=''){
					$iolMasterSelectOS = imw_real_escape_string($objTests->getKheadingId($iolMasterSelectOS));
				}
			$topographerSelectOS = $_REQUEST['topographerSelectOS'];
				if($topographerSelectOS!=''){
					$topographerSelectOS = imw_real_escape_string($objTests->getKheadingId($topographerSelectOS));
				}
			$k1Auto1OS = imw_real_escape_string($_REQUEST['k1Auto1OS']);
			$k1Auto2OS = imw_real_escape_string($_REQUEST['k1Auto2OS']);
			$k1IolMaster1OS = imw_real_escape_string($_REQUEST['k1IolMaster1OS']);
			$k1IolMaster2OS = imw_real_escape_string($_REQUEST['k1IolMaster2OS']);
			$k1Topographer1OS = imw_real_escape_string($_REQUEST['k1Topographer1OS']);
			$k1Topographer2OS = imw_real_escape_string($_REQUEST['k1Topographer2OS']);
			$k2Auto1OS = imw_real_escape_string($_REQUEST['k2Auto1OS']);
			$k2Auto2OS = imw_real_escape_string($_REQUEST['k2Auto2OS']);
			$k2IolMaster1OS = imw_real_escape_string($_REQUEST['k2IolMaster1OS']);
			$k2IolMaster2OS = imw_real_escape_string($_REQUEST['k2IolMaster2OS']);
			$k2Topographer1OS = imw_real_escape_string($_REQUEST['k2Topographer1OS']);
			$k2Topographer2OS = imw_real_escape_string($_REQUEST['k2Topographer2OS']);
			$cylAuto1OS = imw_real_escape_string($_REQUEST['cylAuto1OS']);
			$cylAuto2OS = imw_real_escape_string($_REQUEST['cylAuto2OS']);
			$cylIolMaster1OS = imw_real_escape_string($_REQUEST['cylIolMaster1OS']);
			$cylIolMaster2OS = imw_real_escape_string($_REQUEST['cylIolMaster2OS']);
			$cylTopographer1OS = imw_real_escape_string($_REQUEST['cylTopographer1OS']);
			$cylTopographer2OS = imw_real_escape_string($_REQUEST['cylTopographer2OS']);
			$aveAutoOS = imw_real_escape_string($_REQUEST['aveAutoOS']);
			$aveIolMasterOS = imw_real_escape_string($_REQUEST['aveIolMasterOS']);
			$aveTopographerOS = imw_real_escape_string($_REQUEST['aveTopographerOS']);
			$contactLengthOS = imw_real_escape_string($_REQUEST['contactLengthOS']);
			$immersionLengthOS = imw_real_escape_string($_REQUEST['immersionLengthOS']);
			$iolMasterLengthOS = imw_real_escape_string($_REQUEST['iolMasterLengthOS']);
			$contactNotesOS = imw_real_escape_string($_REQUEST['contactNotesOS']);
			$immersionNotesOS = imw_real_escape_string($_REQUEST['immersionNotesOS']);
			$iolMasterNotesOS = imw_real_escape_string($_REQUEST['iolMasterNotesOS']);
			$performedByOS = imw_real_escape_string($_REQUEST['performedByOS']);
			$performedIolOS = imw_real_escape_string($_REQUEST['performedByOS']);
			$powerIolOS = $_REQUEST['powerIolOS'];
				if($powerIolOS!=''){
					$powerIolOS = imw_real_escape_string($objTests->getFormulaHeadingId($powerIolOS));
				}
	
			$holladayOS = $_REQUEST['holladayOS'];
				if($holladayOS!=''){
					$holladayOS = imw_real_escape_string($objTests->getFormulaHeadingId($holladayOS));
				}
	
			$srk_tOS = $_REQUEST['srk_tOS'];
				if($srk_tOS!=''){
					$srk_tOS = imw_real_escape_string($objTests->getFormulaHeadingId($srk_tOS));
				}
	
			$hofferOS = $_REQUEST['hofferOS'];
				if($hofferOS!=''){
					$hofferOS = imw_real_escape_string($objTests->getFormulaHeadingId($hofferOS));
				}
	
			$iol1OS = $_REQUEST['iol1OS'];
				if($iol1OS!=''){
					$iol1OS = imw_real_escape_string($objTests->getLenseId($iol1OS));
				}
			$iol1PowerOS = imw_real_escape_string($_REQUEST['iol1PowerOS']);
			$iol1HolladayOS = imw_real_escape_string($_REQUEST['iol1HolladayOS']);
			$iol1srk_tOS = imw_real_escape_string($_REQUEST['iol1srk_tOS']);
			$iol1HofferOS = imw_real_escape_string($_REQUEST['iol1HofferOS']);
			$iol2OS = $_REQUEST['iol2OS'];
				if($iol2OS!=''){
					$iol2OS = imw_real_escape_string($objTests->getLenseId($iol2OS));
				}
			$iol2PowerOS = imw_real_escape_string($_REQUEST['iol2PowerOS']);
			$iol2HolladayOS = imw_real_escape_string($_REQUEST['iol2HolladayOS']);
			$iol2srk_tOS = imw_real_escape_string($_REQUEST['iol2srk_tOS']);
			$iol2HofferOS = imw_real_escape_string($_REQUEST['iol2HofferOS']);
			$iol3OS = $_REQUEST['iol3OS'];
				if($iol3OS!=''){
					$iol3OS = imw_real_escape_string($objTests->getLenseId($iol3OS));
				}
			$iol3PowerOS = imw_real_escape_string($_REQUEST['iol3PowerOS']);
			$iol3HolladayOS = imw_real_escape_string($_REQUEST['iol3HolladayOS']);
			$iol3srk_tOS = imw_real_escape_string($_REQUEST['iol3srk_tOS']);
			$iol3HofferOS = imw_real_escape_string($_REQUEST['iol3HofferOS']);
			$iol4OS = $_REQUEST['iol4OS'];
				if($iol4OS!=''){
					$iol4OS = imw_real_escape_string($objTests->getLenseId($iol4OS));
				}
			$iol4PowerOS = imw_real_escape_string($_REQUEST['iol4PowerOS']);
			$iol4HolladayOS = imw_real_escape_string($_REQUEST['iol4HolladayOS']);
			$iol4srk_tOS = imw_real_escape_string($_REQUEST['iol4srk_tOS']);
			$cellCountOS = imw_real_escape_string($_REQUEST['cellCountOS']);
			$notesOS = imw_real_escape_string($_REQUEST['notesOS']);
			$pachymetryValOS = imw_real_escape_string($_REQUEST['pachymetryValOS']);
			$pachymetryCorrecOS = imw_real_escape_string($_REQUEST['pachymetryCorrecOS']);
			$cornealDiamOS = imw_real_escape_string($_REQUEST['cornealDiamOS']);
			$dominantEyeOS = imw_real_escape_string($_REQUEST['dominantEyeOS']);
			$pupilSize1OS = imw_real_escape_string($_REQUEST['pupilSize1OS']);
			$pupilSize2OS = imw_real_escape_string($_REQUEST['pupilSize2OS']);
			$acdOD = imw_real_escape_string($_REQUEST['acdOD']);
			$acdOS = imw_real_escape_string($_REQUEST['acdOS']);
			$w2wOD = imw_real_escape_string($_REQUEST['w2wOD']);
			$w2wOS = imw_real_escape_string($_REQUEST['w2wOS']);
			
			$forum_procedure = (isset($_REQUEST['forum_procedure']))?imw_real_escape_string($_REQUEST['forum_procedure']):"";
			$zeissAction = (isset($_REQUEST['zeissAction']))?$_POST['zeissAction']:false;
	
			$cataractOS = $_REQUEST['cataractOS'];
				if($cataractOS=='on'){
					$cataractOS = 1;
				}
			$astigmatismOS = $_REQUEST['astigmatismOS'];
				if($astigmatismOS=='on'){
					$astigmatismOS = 1;
				}
			$myopiaOS = $_REQUEST['myopiaOS'];
				if($myopiaOS){
					$myopiaOS = 1;
				}
			$selecedIOLsOS = imw_real_escape_string($_REQUEST['selecedIOLsOS']);
				//$selecedIOLsOS = getLenseId($selecedIOLsOS);
			$notesAssesmentPlansOS = imw_real_escape_string($_REQUEST['notesAssesmentPlansOS']);
			$lriOS = $_REQUEST['lriOS'];
				if($lriOS=="on"){ $lriOS = 1; }
			$dlOS = $_REQUEST['dlOS'];
				if($dlOS=="on"){ $dlOS = 1; }
			$synechiolysisOS = $_REQUEST['synechiolysisOS'];
				if($synechiolysisOS=="on"){ $synechiolysisOS = 1; }
			$irishooksOS = $_REQUEST['irishooksOS'];
				if($irishooksOS=="on"){ $irishooksOS = 1; }
			$trypanblueOS = $_REQUEST['trypanblueOS'];
				if($trypanblueOS=="on"){ $trypanblueOS = 1; }
			$flomaxOS = $_REQUEST['flomaxOS'];
				if($flomaxOS=="on"){ $flomaxOS = 1; }
			$cutsOS = $_REQUEST['cutsOS'];
			$lengthCutsOS = imw_real_escape_string($_REQUEST['lengthCutsOS']);
			$legnthOSSelect = imw_real_escape_string($_REQUEST['lengthOSSelect']);
	
			$axisOS = imw_real_escape_string($_REQUEST['axisOS']);
			$superiorOS = $_REQUEST['superiorOS'];
				if($superiorOS=="on"){ $superiorOS = 1; }
			$inferiorOS = $_REQUEST['inferiorOS'];
				if($inferiorOS=="on"){ $inferiorOS = 1; }
			$nasalOS = $_REQUEST['nasalOS'];
				if($nasalOS=="on"){ $nasalOS = 1; }
			$temporalOS = $_REQUEST['temporalOS'];
				if($temporalOS=="on"){ $temporalOS = 1; }
			$STOS = $_REQUEST['STOS'];
				if($STOS=="on"){ $STOS = 1; }
			$SNOS = $_REQUEST['SNOS'];
				if($SNOS=="on"){ $SNOS = 1; }
			$ITOS = $_REQUEST['ITOS'];
				if($ITOS=="on"){ $ITOS = 1; }
			$INOS = $_REQUEST['INOS'];
				if($INOS=="on"){ $INOS = 1; }
			$physicianSelected = imw_real_escape_string($_REQUEST['physicianSelected']);
			$physicianSignature = imw_real_escape_string($_REQUEST['physicianSignature']);
	
			$physicianSelectedOS = imw_real_escape_string($_REQUEST['physicianSelectedOS']);
			$physicianSignatureOS = imw_real_escape_string($_REQUEST['physicianSignatureOS']);
			$examTime = $_POST["elem_examTime"];
			$examDate = getDateFormatDB($_POST["elem_examDate"]);
			if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
			$elem_noP = imw_real_escape_string($_POST["elem_noP"]);
			$ordrby = imw_real_escape_string($_POST["elem_opidTestOrdered"]);
			$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
			
			
	
			//Encounter Id
			if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
				$encounter_id = $_POST["elem_masterEncounterId"];
			}else{
				$encounter_id = $objTests->getEncounterId();
			}
	
			//Ag 09 --
			$sur_dt_od = $_POST["sur_dateOD"];
			$tmp = $_POST["elem_proc_od"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$proc_od = "".$tmp2;
	
			$tmp = $_POST["elem_anes_od"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$anes_od = "".$tmp2;
	
			$tmp = $_POST["elem_visc_od"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$visc_od = "".$tmp2;
	
	
			$visc_od_other = imw_real_escape_string($_POST["elem_visc_od_other"]);
	
			$tmp = $_POST["elem_opts_od"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$opts_od = "".$tmp2;
			$opts_od_other = imw_real_escape_string($_POST["elem_opts_od_other"]);
	
			$iol2dn_od = imw_real_escape_string($_POST["elem_iol2dn_od"]);
	
			$sur_dt_os = imw_real_escape_string($_POST["sur_dateOS"]);
	
			$tmp = $_POST["elem_proc_os"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$proc_os = "".$tmp2;
	
			$tmp = $_POST["elem_anes_os"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$anes_os = "".$tmp2;
	
			$tmp = $_POST["elem_visc_os"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$visc_os = "".$tmp2;
			$visc_os_other = imw_real_escape_string($_POST["elem_visc_os_other"]);
	
	
			$tmp = $_POST["elem_opts_os"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$opts_os = "".$tmp2;
	
			$opts_os_other = imw_real_escape_string($_POST["elem_opts_os_other"]);
			$iol2dn_os = imw_real_escape_string($_POST["elem_iol2dn_os"]);
	
			//print_r($_POST);
			//exit();
	
			//Ag 09 --
			
			//chart notes values
			$cn_mrk_val = imw_real_escape_string($_POST["el_cn_mrk_val"]);		
			
			//========== GETTING FORM EXISTS OR NOT
	
			//check
			if(empty($surgical_id)){
	
				//Check if scan doc id exists in session
				$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;
	
				if(isset($arrTest2edit["Ascan"]) && !empty($arrTest2edit["Ascan"])){
	
					//Check in db
					$cQry = "select surgical_id FROM surgical_tbl
							WHERE patient_id ='".$patient_id."'
							AND surgical_id = '".$arrTest2edit["Ascan"]."' ";
					$row = sqlQuery($cQry);
					$surgical_id = (($row == false) || empty($row["surgical_id"])) ? "0" : $row["surgical_id"];
	
					//Unset Session Ascan
					$arrTest2edit["Ascan"] = "";
					$arrTest2edit["Ascan"] = NULL;
					unset($arrTest2edit["Ascan"]);
					//Reset session
					$_SESSION["test2edit"] = serialize($arrTest2edit);
	
				}else{
	
					$cQry = "select surgical_id FROM surgical_tbl
							WHERE patient_id ='".$patient_id."'
							AND examDate = '".$examDate."' AND examTime = '".$examTime."' ";
					$row = sqlQuery($cQry);
					$surgical_id = (($row == false) || empty($row["surgical_id"])) ? "0" : $row["surgical_id"];
	
				}
			}
			/// Check
	
			/* Above Check is placed. RR
			$getDuplicateCheckStr = "SELECT * FROM surgical_tbl WHERE patient_id = '$patient_id' AND surgical_id = '$surgical_id'";
			$getDuplicateCheckQry = imw_query($getDuplicateCheckStr);
			$getDuplicateCheckRow = imw_num_rows($getDuplicateCheckQry);
			*/
	
			$newFlag = false;
			if(empty($surgical_id)){ //if($getDuplicateCheckRow<=0){
				$insertStr = "INSERT INTO surgical_tbl SET form_id = '".$formId."', patient_id = '$patient_id', ";
				$newFlag = true;
			}else{
				$insertStr = "UPDATE surgical_tbl SET";
			}
	
			$insertStr .= " 
							mrSOD = '$mrSOD',
							mrCOD = '$mrCOD',
							mrAOD = '$mrAOD',
							visionOD = '$visionOD',
							glareOD = '$glareOD',
							performedByOD = '$performedByOD',
							dateOD = '$dateOD',
							autoSelectOD = '$autoSelectOD',
							iolMasterSelectOD = '$iolMasterSelectOD',
							topographerSelectOD = '$topographerSelectOD',
							k1Auto1OD = '$k1Auto1OD',
							k1Auto2OD = '$k1Auto2OD',
							k1IolMaster1OD = '$k1IolMaster1OD',
							k1IolMaster2OD = '$k1IolMaster2OD',
							k1Topographer1OD = '$k1Topographer1OD',
							k1Topographer2OD = '$k1Topographer2OD',
							k2Auto1OD = '$k2Auto1OD',
							k2Auto2OD = '$k2Auto2OD',
							k2IolMaster1OD = '$k2IolMaster1OD',
							k2IolMaster2OD = '$k2IolMaster2OD',
							k2Topographer1OD = '$k2Topographer1OD',
							k2Topographer2OD = '$k2Topographer2OD',
							cylAuto1OD = '$cylAuto1OD',
							cylAuto2OD = '$cylAuto2OD',
							cylIolMaster1OD = '$cylIolMaster1OD',
							cylIolMaster2OD = '$cylIolMaster2OD',
							cylTopographer1OD = '$cylTopographer1OD',
							cylTopographer2OD = '$cylTopographer2OD',
							aveAutoOD = '$aveAutoOD',
							aveIolMasterOD = '$aveIolMasterOD',
							aveTopographerOD = '$aveTopographerOD',
							contactLengthOD = '$contactLengthOD',
							immersionLengthOD = '$immersionLengthOD',
							iolMasterLengthOD = '$iolMasterLengthOD',
							contactNotesOD = '$contactNotesOD',
							immersionNotesOD = '$immersionNotesOD',
							iolMasterNotesOD = '$iolMasterNotesOD',
							performedByPhyOD = '$performedByPhyOD',
							powerIolOD = '$powerIolOD',
							holladayOD = '$holladayOD',
							srk_tOD = '$srk_tOD',
							hofferOD = '$hofferOD',
							iol1OD = '$iol1OD',
							iol1PowerOD = '$iol1PowerOD',
							iol1HolladayOD = '$iol1HolladayOD',
							iol1srk_tOD = '$iol1srk_tOD',
							iol1HofferOD = '$iol1HofferOD',
							iol2OD = '$iol2OD',
							iol2PowerOD = '$iol2PowerOD',
							iol2HolladayOD = '$iol2HolladayOD',
							iol2srk_tOD = '$iol2srk_tOD',
							iol2HofferOD = '$iol2HofferOD',
							iol3OD = '$iol3OD',
							iol3PowerOD = '$iol3PowerOD',
							iol3HolladayOD = '$iol3HolladayOD',
							iol3srk_tOD = '$iol3srk_tOD',
							iol3HofferOD = '$iol3HofferOD',
							iol4OD = '$iol4OD',
							iol4PowerOD = '$iol4PowerOD',
							iol4HolladayOD = '$iol4HolladayOD',
							iol4srk_tOD = '$iol4srk_tOD',
							iol4HofferOD = '$iol4HofferOD',
							cellCountOD = '".imw_real_escape_string($cellCountOD)."',
							notesOD = '$notesOD',
							pachymetryValOD = '$pachymetryValOD',
							pachymetryCorrecOD = '$pachymetryCorrecOD',
							cornealDiamOD = '$cornealDiamOD',
							dominantEyeOD = '$dominantEyeOD',
							pupilSize1OD = '$pupilSize1OD',
							pupilSize2OD = '$pupilSize2OD',
							cataractOD = '$cataractOD',
							astigmatismOD = '$astigmatismOD',
							myopiaOD = '$myopiaOD',
							selecedIOLsOD = '$selecedIOLsOD',
							notesAssesmentPlansOD = '$notesAssesmentPlansOD',
							lriOD = '$lriOD',
							dlOD = '$dlOD',
							synechiolysisOD = '$synechiolysisOD',
							irishooksOD = '$irishooksOD',
							trypanblueOD = '$trypanblueOD',
							flomaxOD = '$flomaxOD',
							cutsOD = '$cutsOD',
							lengthOD = '$lengthCutsOD',
							lengthTypeOD = '$lengthSelectOD',
							axisOD = '$axisOD',
							superiorOD = '$superiorOD',
							inferiorOD = '$inferiorOD',
							nasalOD = '$nasalOD',
							temporalOD = '$temporalOD',
							STOD = '$STOD',
							SNOD = '$SNOD',
							ITOD = '$ITOD',
							INOD = '$INOD',
							mrSOS = '".imw_real_escape_string($mrSOS)."',
							mrCOS = '".imw_real_escape_string($mrCOS)."',
							mrAOS = '".imw_real_escape_string($mrAOS)."',
							visionOS = '$visionOS',
							glareOS = '$glareOS',
							performedByOS = '$phyTechListOS',
							dateOS = '$dateOS',
							autoSelectOS = '$autoSelectOS',
							iolMasterSelectOS = '$iolMasterSelectOS',
							topographerSelectOS = '$topographerSelectOS',
							k1Auto1OS = '$k1Auto1OS',
							k1Auto2OS = '$k1Auto2OS',
							k1IolMaster1OS = '$k1IolMaster1OS',
							k1IolMaster2OS = '$k1IolMaster2OS',
							k1Topographer1OS = '$k1Topographer1OS',
							k1Topographer2OS = '$k1Topographer2OS',
							k2Auto1OS = '$k2Auto1OS',
							k2Auto2OS = '$k2Auto2OS',
							k2IolMaster1OS = '$k2IolMaster1OS',
							k2IolMaster2OS = '$k2IolMaster2OS',
							k2Topographer1OS = '$k2Topographer1OS',
							k2Topographer2OS = '$k2Topographer2OS',
							cylAuto1OS = '$cylAuto1OS',
							cylAuto2OS = '$cylAuto2OS',
							cylIolMaster1OS = '$cylIolMaster1OS',
							cylIolMaster2OS = '$cylIolMaster2OS',
							cylTopographer1OS = '$cylTopographer1OS',
							cylTopographer2OS = '$cylTopographer2OS',
							aveAutoOS = '$aveAutoOS',
							aveIolMasterOS = '$aveIolMasterOS',
							aveTopographerOS = '$aveTopographerOS',
							contactLengthOS = '$contactLengthOS',
							immersionLengthOS = '$immersionLengthOS',
							iolMasterLengthOS = '$iolMasterLengthOS',
							contactNotesOS = '$contactNotesOS',
							immersionNotesOS = '$immersionNotesOS',
							performedIolOS = '$performedIolOS',
							iolMasterNotesOS = '$iolMasterNotesOS',
							powerIolOS = '$powerIolOS',
							holladayOS = '$holladayOS',
							srk_tOS = '$srk_tOS',
							hofferOS = '$hofferOS',
							iol1OS = '$iol1OS',
							iol1PowerOS = '$iol1PowerOS',
							iol1HolladayOS = '$iol1HolladayOS',
							iol1srk_tOS = '$iol1srk_tOS',
							iol1HofferOS = '$iol1HofferOS',
							iol2OS = '$iol2OS',
							iol2PowerOS = '$iol2PowerOS',
							iol2HolladayOS = '$iol2HolladayOS',
							iol2srk_tOS = '$iol2srk_tOS',
							iol2HofferOS = '".$iol2HofferOS."',
							iol3OS = '$iol3OS',
							iol3PowerOS = '$iol3PowerOS',
							iol3HolladayOS = '$iol3HolladayOS',
							iol3srk_tOS = '$iol3srk_tOS',
							iol3HofferOS = '$iol3HofferOS',
							iol4OS = '$iol4OS',
							iol4PowerOS = '$iol4PowerOS',
							iol4HolladayOS = '$iol4HolladayOS',
							iol4srk_tOS = '$iol4srk_tOS',
							iol4HofferOS = '".imw_real_escape_string($iol4HofferOS)."',
							cellCountOS = '$cellCountOS',
							notesOS = '$notesOS',
							pachymetryValOS = '$pachymetryValOS',
							pachymetryCorrecOS = '$pachymetryCorrecOS',
							cornealDiamOS = '$cornealDiamOS',
							dominantEyeOS = '$dominantEyeOS',
							pupilSize1OS = '$pupilSize1OS',
							pupilSize2OS = '$pupilSize2OS',
							cataractOS = '$cataractOS',
							astigmatismOS = '$astigmatismOS',
							myopiaOS = '$myopiaOS',
							selecedIOLsOS = '$selecedIOLsOS',
							notesAssesmentPlansOS = '$notesAssesmentPlansOS',
							lriOS = '$lriOS',
							dlOS = '$dlOS',
							synechiolysisOS = '$synechiolysisOS',
							irishooksOS = '$irishooksOS',
							trypanblueOS = '$trypanblueOS',
							flomaxOS = '$flomaxOS',
							cutsOS = '$cutsOS',
							lengthOS = '$lengthCutsOS',
							lengthTypeOS = '$lengthOSSelect',
							axisOS = '$axisOS',
							superiorOS = '$superiorOS',
							inferiorOS = '$inferiorOS',
							nasalOS = '$nasalOS',
							temporalOS = '$temporalOS',
							STOS = '$STOS',
							SNOS = '$SNOS',
							ITOS = '$ITOS',
							INOS = '$INOS',
							";
							
							if(!$zeissAction){ /*Prevent changing interpretation status if "Forum Add/Delete"*/
								$insertStr .= "signedById = '$physicianSelected',
								";
							}
			$insertStr .= "
							signature = '$physicianSignature',
							signedByOSId = '$physicianSelectedOS',
							signatureOS = '$physicianSignatureOS',
							examDate = '$examDate',
							examTime = '$examTime',
							encounter_id='".$encounter_id."',
							ordrby='".$ordrby."',ordrdt='".$ordrdt."',
							sur_dt_od='".$sur_dt_od."',
							proc_od='".$proc_od."',
							anes_od='".$anes_od."',
							visc_od='".$visc_od."',
							visc_od_other='".$visc_od_other."',
							opts_od='".$opts_od."',
							opts_od_other='".$opts_od_other."',
							iol2dn_od='".$iol2dn_od."',
	
							sur_dt_os='".$sur_dt_os."',
							proc_os='".$proc_os."',
							anes_os='".$anes_os."',
							visc_os='".$visc_os."',
							visc_os_other='".$visc_os_other."',
							opts_os='".$opts_os."',
							opts_os_other='".$opts_os_other."',
							iol2dn_os='".$iol2dn_os."',
							acdOD='".$acdOD."',
							acdOS='".$acdOS."',
							".$signPathQry."
							".$signDTmQry."
							w2wOD='".$w2wOD."',
							w2wOS='".$w2wOS."',
							forum_procedure='".$forum_procedure."',
							cn_mrk_val='".$cn_mrk_val."'
							";
	
			if(!empty($surgical_id)){ //($getDuplicateCheckRow>0){
				$insertStr .= " WHERE surgical_id = '$surgical_id' AND patient_id = '$patient_id'  ";
			}
			$insertQry = imw_query($insertStr);
			if(empty($surgical_id)){ //($getDuplicateCheckRow<=0){
				$surgical_id = $printId = imw_insert_id();
			}
			
			/*code, Purpose: Generate HL7 message for Test*/
			$zeissMsgType="A/SCAN";
			$zeissPatientId = $patient_id;
			$zeissTestId = $surgical_id;
			include("./zeissTestHl7.php");
			/*End code*/
	
			/*--interpretation starts--*/
			$objTests->interpret_if_scan_exists($_SESSION['patient'], "Ascan", $surgical_id);
			/*--interpretation end--*/
	
			//============================== INSERT FORMULA VALUE FOR PROVIDER TO THE PATIENT OD
			$getExistsODStr = "SELECT * FROM iolphyformulavalues
								WHERE patient_id = '$patient_id'
								AND provider_id = '$performedByPhyOD'
								AND form_id = '$form_id'
								AND type_OD_OS = 'OD'";
			$getExistsODQry = imw_query($getExistsODStr);
			if(imw_num_rows($getExistsODQry)>0){
				$insertUpdateODStr = "UPDATE iolphyformulavalues SET ";
			}else{
				$insertUpdateODStr = "INSERT INTO iolphyformulavalues SET
										patient_id = '$patient_id',
										provider_id = '$performedByPhyOD',
										form_id = '$form_id',
										type_OD_OS = 'OD', ";
			}
			$insertUpdateODStr.= "iol1 = '$iol1OD',
									iol1Power = '$iol1PowerOD',
									iol1Holladay = '$iol1HolladayOD',
									iol1srk_t = '$iol1srk_tOD',
									iol1Hoffer = '$iol1HofferOD',
									iol2 = '$iol2OD',
									iol2Power = '$iol2PowerOD',
									iol2Holladay = '$iol2HolladayOD',
									iol2srk_t = '$iol2srk_tOD',
									iol2Hoffer = '$iol2HofferOD',
									iol3 = '$iol3OD',
									iol3Power = '$iol3PowerOD',
									iol3Holladay = '$iol3HolladayOD',
									iol3srk_t = '$iol3srk_tOD',
									iol3Hoffer = '$iol3HofferOD',
									iol4 = '$iol4OD',
									iol4Power = '$iol4PowerOD',
									iol4Holladay = '$iol4HolladayOD',
									iol4srk_t = '$iol4srk_tOD',
									iol4Hoffer = '$iol4HofferOD',
									cellCount = '$cellCountOD',
									pachymetryVal = '$pachymetryValOD',
									pachymetryCorrec = '$pachymetryCorrecOD',
									cornealDiam = '$cornealDiamOD',
									dominantEye = '$dominantEyeOD',
									pupilSize1 = '$pupilSize1OD',
									pupilSize2  = '$pupilSize2OD',
									notes = '$notesOD',
									plan = '$selecedIOLsOD'";
	
				if(imw_num_rows($getExistsODQry)>0){
				$insertUpdateODStr.= " WHERE patient_id = '$patient_id'
										AND provider_id = '$performedByPhyOD'
										AND form_id = '$form_id'
										AND type_OD_OS = 'OD'";
				}
				$insertUpdateODQry = imw_query($insertUpdateODStr);
			//============================== INSERT FORMULA VALUE FOR PROVIDER TO THE PATIENT OD
	
			//============================== INSERT FORMULA VALUE FOR PROVIDER TO THE PATIENT OS
			if($performedByOS!=''){
				$getExistsOSStr = "SELECT * FROM iolphyformulavalues
									WHERE patient_id = '$patient_id'
									AND provider_id = '$performedByOS'
									AND form_id = '$form_id'
									AND type_OD_OS = 'OS'";
				$getExistsOSQry = imw_query($getExistsOSStr);
				if(imw_num_rows($getExistsOSQry)>0){
					$insertUpdateOSStr = "UPDATE iolphyformulavalues SET ";
				}else{
					$insertUpdateOSStr = "INSERT INTO iolphyformulavalues SET
											patient_id = '$patient_id',
											provider_id = '$performedByOS',
											form_id = '$form_id',
											type_OD_OS = 'OS', ";
				}
				$insertUpdateOSStr.= "iol1 = '$iol1OS',
										iol1Power = '$iol1PowerOS',
										iol1Holladay = '$iol1HolladayOS',
										iol1srk_t = '$iol1srk_tOS',
										iol1Hoffer = '$iol1HofferOS',
										iol2 = '$iol2OS',
										iol2Power = '$iol2PowerOS',
										iol2Holladay = '$iol2HolladayOS',
										iol2srk_t = '$iol2srk_tOS',
										iol2Hoffer = '$iol2HofferOS',
										iol3 = '$iol3OS',
										iol3Power = '$iol3PowerOS',
										iol3Holladay = '$iol3HolladayOS',
										iol3srk_t = '$iol3srk_tOS',
										iol3Hoffer = '$iol3HofferOS',
										iol4 = '$iol4OS',
										iol4Power = '$iol4PowerOS',
										iol4Holladay = '$iol4HolladayOS',
										iol4srk_t = '$iol4srk_tOS',
										iol4Hoffer = '$iol4HofferOS',
										cellCount = '$cellCountOS',
										pachymetryVal = '$pachymetryValOS',
										pachymetryCorrec = '$pachymetryCorrecOS',
										cornealDiam = '$cornealDiamOS',
										dominantEye = '$dominantEyeOS',
										pupilSize1 = '$pupilSize1OS',
										pupilSize2  = '$pupilSize2OS',
										notes = '$notesOS',
										plan = '$selecedIOLsOS',
										acdOD='".$acdOD."',
										acdOS='".$acdOS."',
										w2wOD='".$w2wOD."',
										w2wOS='".$w2wOS."'
										";
					if(imw_num_rows($getExistsOSQry)>0){
					$insertUpdateOSStr.= " WHERE patient_id = '$patient_id'
											AND provider_id = '$performedByOS'
											AND form_id = '$form_id'
											AND type_OD_OS = 'OS'";
					}
					$insertUpdateOSQry = imw_query($insertUpdateOSStr);
			}
			//============================== INSERT FORMULA VALUE FOR PROVIDER TO THE PATIENT OS
			
			//Super-Bill -----------
			if(!empty($ordrby) && !empty($surgical_id)){
				$arIn=array();			
				$arIn["elem_physicianId"]=$ordrby;
				$arIn["doctorId"]=$ordrby;
				$arIn["caseId"]=$_POST["elem_masterCaseId"];
				$arIn["encounterId"]=$encounter_id;
				$arIn["date_of_service"]=$examDate;
				$arIn["sb_testId"]=$surgical_id;
				$arIn["sb_testName"]="A-Scan";
				$arIn["form_id"]=$formId;
                                $arIn["test_interpreted"]= ((int)$physicianSelected > 0)? true : false;
				
				$oSuperbillSaver = new SuperbillSaver($patient_id);
				$oSuperbillSaver->save($arIn);			
			}
			//Super-Bill -----------
	
			//Test Pdf ---
				//saveTestPdfExe_2($patient_id,$surgical_id,"A-Scan");
			//Test Pdf ---
	
			//FormId updates other tables -----
			if(!empty($form_id)){
				// Make chart notes valid
				$oChartNote->makeChartNotesValid($form_id);
								
				//Synchronize chart_correction_values			
				if(!empty($pachymetryCorrecOD) || !empty($pachymetryCorrecOS)){			
					$arr = array("elem_formId" => $form_id, "elem_od_readings" => $pachymetryValOD,
							  "elem_od_average" => "", "elem_od_correction_value" => $pachymetryCorrecOD,
							  "elem_os_readings" => $pachymetryValOS, "elem_os_average" => "",
							  "elem_os_correction_value" => $pachymetryCorrecOS, "patientid" => $patient_id,
							  "elem_cor_date" => $examDate
							 );
					$objTests->saveCorrectionValues($arr);
					//pachy
					//check if Pachy is made and syncronize correction values
					//check
					$sql = "SELECT * FROM pachy WHERE formId='".$form_id."' AND patientId='".$patient_id."' ";
					$row = sqlQuery($sql);
					if($row != false){
						//Update
						$sql = "UPDATE pachy SET ".
							 "pachy_od_readings = '".$pachymetryValOD."', ".
							 "pachy_od_average='', ".
							 "pachy_od_correction_value = '".$pachymetryCorrecOD."', ".
							 "pachy_os_readings = '".$pachymetryValOS."', ".
							 "pachy_os_average = '', ".
							 "pachy_os_correction_value = '".$pachymetryCorrecOS."' ".
							 "WHERE formId = '".$form_id."' AND patientId='".$patient_id."' ";
						$row = sqlQuery($sql);
					}
					
					//chartPtDiag
					$sql = "SELECT * FROM chart_ptPastDiagnosis WHERE patient_id='".$patient_id."' ";
					$row = sqlQuery($sql);
					if( $row != false ){
						$ptPastPachy = "";
						$arr=array();
						$arr["od_readings"]=$pachymetryValOD;
						$arr["od_average"]="";
						$arr["od_correction_value"]=$pachymetryCorrecOD;
						$arr["os_readings"]=$pachymetryValOS;
						$arr["os_average"]="";
						$arr["os_correction_value"]=$pachymetryValOS;
						$arr["cor_date"]=$examDate;
						$ptPastPachy = serialize($arr);
						$sql = "UPDATE chart_ptPastDiagnosis SET pachy = '".imw_real_escape_string($ptPastPachy)."' WHERE patient_id='".$patient_id."' ";
						$res = sqlQuery($sql);
					}
				}
			}
			
			echo "<script>".
					"try{";
					if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
						$afterSaveinPopup = 'yes';
					}else{
						$afterSaveinPopup = 'no';
						echo "top.alert_notification_show(\"Test has been saved.\");";
					}
					echo "
						window.location.replace(\"ascan.php?noP=".$elem_noP."&tId=".$surgical_id."&pop=".$_REQUEST["pop"]."&afterSaveinPopup=".$afterSaveinPopup."\");
					}catch(err){}".
				 "</script>";
			exit();	
		}
		//======================== SAVE SURGICAL
		break;	
	}
	case "IOL Master":{
		$saveSurgical = $_REQUEST['saveSurgical'];
		if($saveSurgical == "true"){
			$iol_master_id = $_REQUEST['iol_master_id'];
			$formId = $_REQUEST['form_id'];
			
			//start signature work
			$tmpDirPth_up = $oSaveFile->upDir;
			$signatureDateTime = date('Y-m-d H:i:s');
			$hidd_prev_signature_path = $_POST["hidd_prev_signature_path"];
			$hidd_signature_path = $_POST["hidd_signature_path"];
			$signPathQry=$signDTmQry=$signDTmFieldName=$signDTmFieldValue=$signPathFieldName=$signPathFieldValue="";
			$boolSignDtTm = false;
			if($hidd_prev_signature_path && $hidd_signature_path && $hidd_prev_signature_path!=$hidd_signature_path) {
				$hidd_prev_signature_real_path=realpath($tmpDirPth_up.$hidd_prev_signature_path);
				if(file_exists($hidd_prev_signature_real_path)) {
					@unlink($hidd_prev_signature_real_path);
				}
				$boolSignDtTm = true;
			}
			if($hidd_signature_path && !$hidd_prev_signature_path) {
				$boolSignDtTm = true;
			}
			if($boolSignDtTm == true) {
				$signDTmQry = " sign_path_date_time='".$signatureDateTime."', ";
				$signDTmFieldName = " ,sign_path_date_time ";
				$signDTmFieldValue = " ,'".$signatureDateTime."' ";
			}
			if($hidd_signature_path) {
				$signPathQry = " sign_path='".$hidd_signature_path."', ";
				$signPathFieldName = " ,sign_path ";
				$signPathFieldValue = " ,'".$hidd_signature_path."' ";
				
			}
			//end signature work
					
			$mrSOD = imw_real_escape_string($_REQUEST['mrSOD']);
			$mrCOD = imw_real_escape_string($_REQUEST['mrCOD']);
			$mrAOD = imw_real_escape_string($_REQUEST['mrAOD']);
			$visionOD = imw_real_escape_string($_REQUEST['visionOD']);
			$glareOD = imw_real_escape_string($_REQUEST['glareOD']);
			$performedByOD = imw_real_escape_string($_REQUEST['performedByOD']);
			$dateOD = getDateFormatDB(imw_real_escape_string($_REQUEST['dateOD']));
				//list($dateODMonth, $dateODDay, $dateODYear) = split("-", $dateOD);
				//$dateOD = $dateODYear."-".$dateODMonth."-".$dateODDay;
			$autoSelectOD = $_REQUEST['autoSelectOD'];
				if($autoSelectOD!=''){
					$autoSelectOD = imw_real_escape_string($objTests->getKheadingId($autoSelectOD));
				}
			$iolMasterSelectOD = $_REQUEST['iolMasterSelectOD'];
				if($iolMasterSelectOD!=''){
					$iolMasterSelectOD = imw_real_escape_string($objTests->getKheadingId($iolMasterSelectOD));
				}
			$topographerSelectOD = $_REQUEST['topographerSelectOD'];
				if($topographerSelectOD!=''){
					$topographerSelectOD = imw_real_escape_string($objTests->getKheadingId($topographerSelectOD));
				}
			$k1Auto1OD = imw_real_escape_string($_REQUEST['k1Auto1OD']);
			$k1Auto2OD = imw_real_escape_string($_REQUEST['k1Auto2OD']);
			$k1IolMaster1OD = imw_real_escape_string($_REQUEST['k1IolMaster1OD']);
			$k1IolMaster2OD = imw_real_escape_string($_REQUEST['k1IolMaster2OD']);
			$k1Topographer1OD = imw_real_escape_string($_REQUEST['k1Topographer1OD']);
			$k1Topographer2OD = imw_real_escape_string($_REQUEST['k1Topographer2OD']);
			$k2Auto1OD = imw_real_escape_string($_REQUEST['k2Auto1OD']);
			$k2Auto2OD = imw_real_escape_string($_REQUEST['k2Auto2OD']);
			$k2IolMaster1OD = imw_real_escape_string($_REQUEST['k2IolMaster1OD']);
			$k2IolMaster2OD = imw_real_escape_string($_REQUEST['k2IolMaster2OD']);
			$k2Topographer1OD = imw_real_escape_string($_REQUEST['k2Topographer1OD']);
			$k2Topographer2OD = imw_real_escape_string($_REQUEST['k2Topographer2OD']);
			$cylAuto1OD = imw_real_escape_string($_REQUEST['cylAuto1OD']);
			$cylAuto2OD = imw_real_escape_string($_REQUEST['cylAuto2OD']);
			$cylIolMaster1OD = imw_real_escape_string($_REQUEST['cylIolMaster1OD']);
			$cylIolMaster2OD = imw_real_escape_string($_REQUEST['cylIolMaster2OD']);
			$cylTopographer1OD = imw_real_escape_string($_REQUEST['cylTopographer1OD']);
			$cylTopographer2OD = imw_real_escape_string($_REQUEST['cylTopographer2OD']);
			$aveAutoOD = imw_real_escape_string($_REQUEST['aveAutoOD']);
			$aveIolMasterOD = imw_real_escape_string($_REQUEST['aveIolMasterOD']);
			$aveTopographerOD = imw_real_escape_string($_REQUEST['aveTopographerOD']);
			$contactLengthOD = imw_real_escape_string($_REQUEST['contactLengthOD']);
			$immersionLengthOD = imw_real_escape_string($_REQUEST['immersionLengthOD']);
			$iolMasterLengthOD = imw_real_escape_string($_REQUEST['iolMasterLengthOD']);
			$contactNotesOD = imw_real_escape_string($_REQUEST['contactNotesOD']);
			$immersionNotesOD = imw_real_escape_string($_REQUEST['immersionNotesOD']);
			$iolMasterNotesOD = imw_real_escape_string($_REQUEST['iolMasterNotesOD']);
			$performedByPhyOD = imw_real_escape_string($_REQUEST['performedByPhyOD']);
			$powerIolOD = $_REQUEST['powerIolOD'];
			$tmp_other = trim($_REQUEST['powerIolOD_other']);			
			if(empty($powerIolOD) && !empty($tmp_other)){ $powerIolOD = $tmp_other; }
				if($powerIolOD!=''){
					$powerIolOD = imw_real_escape_string($objTests->getFormulaHeadingId($powerIolOD));
				}
			$holladayOD = $_REQUEST['holladayOD'];
			$tmp_other = trim($_REQUEST['holladayOD_other']);
			if(empty($holladayOD) && !empty($tmp_other)){ $holladayOD = $tmp_other; }
				if($holladayOD!=''){
					$holladayOD = imw_real_escape_string($objTests->getFormulaHeadingId($holladayOD));
				}
			$srk_tOD = $_REQUEST['srk_tOD'];
			$tmp_other = trim($_REQUEST['srk_tOD_other']);
			if(empty($srk_tOD) && !empty($tmp_other)){ $srk_tOD = $tmp_other; }
				if($srk_tOD!=''){
					$srk_tOD = imw_real_escape_string($objTests->getFormulaHeadingId($srk_tOD));
				}
			$hofferOD = $_REQUEST['hofferOD'];
			$tmp_other = trim($_REQUEST['hofferOD_other']);
			if(empty($hofferOD) && !empty($tmp_other)){ $hofferOD = $tmp_other; }
				if($hofferOD!=''){
					$hofferOD = imw_real_escape_string($objTests->getFormulaHeadingId($hofferOD));
				}
			$iol1OD = $_REQUEST['iol1OD'];
				if($iol1OD!=''){
					$iol1OD = imw_real_escape_string($objTests->getLenseId($iol1OD));
				}
			$iol1PowerOD = imw_real_escape_string($_REQUEST['iol1PowerOD']);
			$iol1HolladayOD = imw_real_escape_string($_REQUEST['iol1HolladayOD']);
			$iol1srk_tOD = imw_real_escape_string($_REQUEST['iol1srk_tOD']);
			$iol1HofferOD = imw_real_escape_string($_REQUEST['iol1HofferOD']);
			$iol2OD = $_REQUEST['iol2OD'];
				if($iol2OD!=''){
					$iol2OD = imw_real_escape_string($objTests->getLenseId($iol2OD));
				}
			$iol2PowerOD = imw_real_escape_string($_REQUEST['iol2PowerOD']);
			$iol2HolladayOD = imw_real_escape_string($_REQUEST['iol2HolladayOD']);
			$iol2srk_tOD = imw_real_escape_string($_REQUEST['iol2srk_tOD']);
			$iol2HofferOD = imw_real_escape_string($_REQUEST['iol2HofferOD']);
			$iol3OD = $_REQUEST['iol3OD'];
				if($iol3OD!=''){
					$iol3OD = imw_real_escape_string($objTests->getLenseId($iol3OD));
				}
			$iol3PowerOD = imw_real_escape_string($_REQUEST['iol3PowerOD']);
			$iol3HolladayOD = imw_real_escape_string($_REQUEST['iol3HolladayOD']);
			$iol3srk_tOD = imw_real_escape_string($_REQUEST['iol3srk_tOD']);
			$iol3HofferOD = imw_real_escape_string($_REQUEST['iol3HofferOD']);
			$iol4OD = $_REQUEST['iol4OD'];
				if($iol4OD!=''){
					$iol4OD = imw_real_escape_string($objTests->getLenseId($iol4OD));
				}
			$iol4PowerOD = imw_real_escape_string($_REQUEST['iol4PowerOD']);
			$iol4HolladayOD = imw_real_escape_string($_REQUEST['iol4HolladayOD']);
			$iol4srk_tOD = imw_real_escape_string($_REQUEST['iol4srk_tOD']);
			$iol4HofferOD = imw_real_escape_string($_REQUEST['iol4HofferOD']);
			$notesOD = imw_real_escape_string($_REQUEST['notesOD']);
			$immersionNotesOD = imw_real_escape_string($_REQUEST['immersionNotesOD']);
			$iolMasterNotesOD = imw_real_escape_string($_REQUEST['iolMasterNotesOD']);
			$pachymetryValOD = imw_real_escape_string($_REQUEST['pachymetryValOD']);
			$pachymetryCorrecOD = imw_real_escape_string($_REQUEST['pachymetryCorrecOD']);
			$cornealDiamOD = imw_real_escape_string($_REQUEST['cornealDiamOD']);
			$dominantEyeOD = imw_real_escape_string($_REQUEST['dominantEyeOD']);
			$pupilSize1OD = imw_real_escape_string($_REQUEST['pupilSize1OD']);
			$pupilSize2OD = imw_real_escape_string($_REQUEST['pupilSize2OD']);
			$cataractOD = $_REQUEST['cataractOD'];
				if($cataractOD=='on'){
					$cataractOD = 1;
				}
			$astigmatismOD = $_REQUEST['astigmatismOD'];
				if($astigmatismOD=='on'){
					$astigmatismOD = 1;
				}
			$myopiaOD = $_REQUEST['myopiaOD'];
				if($myopiaOD=='on'){
					$myopiaOD = 1;
				}
			$selecedIOLsOD = imw_real_escape_string($_REQUEST['selecedIOLsOD']);
				//$selecedIOLsOD = getLenseId($selecedIOLsOD);
			$notesAssesmentPlansOD = imw_real_escape_string($_REQUEST['notesAssesmentPlansOD']);
			$lriOD = $_REQUEST['lriOD'];
				if($lriOD=="on"){ $lriOD = 1; }
			$dlOD = $_REQUEST['dlOD'];
				if($dlOD=="on"){ $dlOD = 1; }
			$synechiolysisOD = $_REQUEST['synechiolysisOD'];
				if($synechiolysisOD=="on"){ $synechiolysisOD = 1; }
			$irishooksOD = $_REQUEST['irishooksOD'];
				if($irishooksOD=="on"){ $irishooksOD = 1; }
			$trypanblueOD = $_REQUEST['trypanblueOD'];
				if($trypanblueOD=="on"){ $trypanblueOD = 1; }
			$flomaxOD = $_REQUEST['flomaxOD'];
				if($flomaxOD=="on"){ $flomaxOD = 1; }
	
			$cutsOD = imw_real_escape_string($_REQUEST['cutsOD']);
			$lengthCutsOD = imw_real_escape_string($_REQUEST['lengthCutsOD']);
			$lengthSelectOD = imw_real_escape_string($_REQUEST['lengthSelectOD']);
			$axisOD = imw_real_escape_string($_REQUEST['axisOD']);
	
			$superiorOD = $_REQUEST['superiorOD'];
				if($superiorOD=="on"){ $superiorOD = 1; }
			$inferiorOD = $_REQUEST['inferiorOD'];
				if($inferiorOD=="on"){ $inferiorOD = 1; }
			$nasalOD = $_REQUEST['nasalOD'];
				if($nasalOD=="on"){ $nasalOD = 1; }
			$temporalOD = $_REQUEST['temporalOD'];
				if($temporalOD=="on"){ $temporalOD = 1; }
			$STOD = $_REQUEST['STOD'];
				if($STOD=="on"){ $STOD = 1; }
			$SNOD = $_REQUEST['SNOD'];
				if($SNOD=="on"){ $SNOD = 1; }
			$ITOD = $_REQUEST['ITOD'];
				if($ITOD=="on"){ $ITOD = 1; }
			$INOD = $_REQUEST['INOD'];
				if($INOD=="on"){ $INOD = 1; }
			$vis_mr_os_s = imw_real_escape_string($_REQUEST['mrSOS']);
			$vis_mr_os_c = imw_real_escape_string($_REQUEST['mrCOS']);
			$vis_mr_os_a = imw_real_escape_string($_REQUEST['mrAOS']);
			$visionOS = imw_real_escape_string($_REQUEST['visionOS']);
			$glareOS = imw_real_escape_string($_REQUEST['glareOS']);
			$phyTechListOS = imw_real_escape_string($_REQUEST['phyTechListOS']);
			$dateOS = getDateFormatDB($_REQUEST['dateOS']);
				//list($dateOSMonth, $dateOSDay, $dateOSYear) = split("-", $dateOS);
				//$dateOS = $dateOSYear."-".$dateOSMonth."-".$dateOSDay;
	
			$autoSelectOS = $_REQUEST['autoSelectOS'];
				if($autoSelectOS!=''){
					$autoSelectOS = imw_real_escape_string($objTests->getKheadingId($autoSelectOS));
				}
			$iolMasterSelectOS = $_REQUEST['iolMasterSelectOS'];
				if($iolMasterSelectOS!=''){
					$iolMasterSelectOS = imw_real_escape_string($objTests->getKheadingId($iolMasterSelectOS));
				}
			$topographerSelectOS = $_REQUEST['topographerSelectOS'];
				if($topographerSelectOS!=''){
					$topographerSelectOS = imw_real_escape_string($objTests->getKheadingId($topographerSelectOS));
				}
			$k1Auto1OS = imw_real_escape_string($_REQUEST['k1Auto1OS']);
			$k1Auto2OS = imw_real_escape_string($_REQUEST['k1Auto2OS']);
			$k1IolMaster1OS = imw_real_escape_string($_REQUEST['k1IolMaster1OS']);
			$k1IolMaster2OS = imw_real_escape_string($_REQUEST['k1IolMaster2OS']);
			$k1Topographer1OS = imw_real_escape_string($_REQUEST['k1Topographer1OS']);
			$k1Topographer2OS = imw_real_escape_string($_REQUEST['k1Topographer2OS']);
			$k2Auto1OS = imw_real_escape_string($_REQUEST['k2Auto1OS']);
			$k2Auto2OS = imw_real_escape_string($_REQUEST['k2Auto2OS']);
			$k2IolMaster1OS = imw_real_escape_string($_REQUEST['k2IolMaster1OS']);
			$k2IolMaster2OS = imw_real_escape_string($_REQUEST['k2IolMaster2OS']);
			$k2Topographer1OS = imw_real_escape_string($_REQUEST['k2Topographer1OS']);
			$k2Topographer2OS = imw_real_escape_string($_REQUEST['k2Topographer2OS']);
			$cylAuto1OS = imw_real_escape_string($_REQUEST['cylAuto1OS']);
			$cylAuto2OS = imw_real_escape_string($_REQUEST['cylAuto2OS']);
			$cylIolMaster1OS = imw_real_escape_string($_REQUEST['cylIolMaster1OS']);
			$cylIolMaster2OS = imw_real_escape_string($_REQUEST['cylIolMaster2OS']);
			$cylTopographer1OS = imw_real_escape_string($_REQUEST['cylTopographer1OS']);
			$cylTopographer2OS = imw_real_escape_string($_REQUEST['cylTopographer2OS']);
			$aveAutoOS = imw_real_escape_string($_REQUEST['aveAutoOS']);
			$aveIolMasterOS = imw_real_escape_string($_REQUEST['aveIolMasterOS']);
			$aveTopographerOS = imw_real_escape_string($_REQUEST['aveTopographerOS']);
			$contactLengthOS = imw_real_escape_string($_REQUEST['contactLengthOS']);
			$immersionLengthOS = imw_real_escape_string($_REQUEST['immersionLengthOS']);
			$iolMasterLengthOS = imw_real_escape_string($_REQUEST['iolMasterLengthOS']);
			$contactNotesOS = imw_real_escape_string($_REQUEST['contactNotesOS']);
			$immersionNotesOS = imw_real_escape_string($_REQUEST['immersionNotesOS']);
			$iolMasterNotesOS = imw_real_escape_string($_REQUEST['iolMasterNotesOS']);
			$performedByOS = imw_real_escape_string($_REQUEST['performedByOS']);
			$performedIolOS = imw_real_escape_string($_REQUEST['performedByOS']);
			$powerIolOS = $_REQUEST['powerIolOS'];
			$tmp_other = trim($_REQUEST['powerIolOS_other']);
			if(empty($powerIolOS) && !empty($tmp_other)){ $powerIolOS = $tmp_other; }
				if($powerIolOS!=''){
					$powerIolOS = imw_real_escape_string($objTests->getFormulaHeadingId($powerIolOS));
				}
	
			$holladayOS = $_REQUEST['holladayOS'];
			$tmp_other = trim($_REQUEST['holladayOS_other']);
			if(empty($holladayOS) && !empty($tmp_other)){ $holladayOS = $tmp_other; }
				if($holladayOS!=''){
					$holladayOS = imw_real_escape_string($objTests->getFormulaHeadingId($holladayOS));
				}
	
			$srk_tOS = $_REQUEST['srk_tOS'];
			$tmp_other = trim($_REQUEST['srk_tOS_other']);
			if(empty($srk_tOS) && !empty($tmp_other)){ $srk_tOS = $tmp_other; }
				if($srk_tOS!=''){
					$srk_tOS = imw_real_escape_string($objTests->getFormulaHeadingId($srk_tOS));
				}
	
			$hofferOS = $_REQUEST['hofferOS'];
			$tmp_other = trim($_REQUEST['hofferOS_other']);
			if(empty($hofferOS) && !empty($tmp_other)){ $hofferOS = $tmp_other; }
				if($hofferOS!=''){
					$hofferOS = imw_real_escape_string($objTests->getFormulaHeadingId($hofferOS));
				}
	
			$iol1OS = $_REQUEST['iol1OS'];
				if($iol1OS!=''){
					$iol1OS = imw_real_escape_string($objTests->getLenseId($iol1OS));
				}
			$iol1PowerOS = imw_real_escape_string($_REQUEST['iol1PowerOS']);
			$iol1HolladayOS = imw_real_escape_string($_REQUEST['iol1HolladayOS']);
			$iol1srk_tOS = imw_real_escape_string($_REQUEST['iol1srk_tOS']);
			$iol1HofferOS = imw_real_escape_string($_REQUEST['iol1HofferOS']);
			$iol2OS = $_REQUEST['iol2OS'];
				if($iol2OS!=''){
					$iol2OS = imw_real_escape_string($objTests->getLenseId($iol2OS));
				}
			$iol2PowerOS = imw_real_escape_string($_REQUEST['iol2PowerOS']);
			$iol2HolladayOS = imw_real_escape_string($_REQUEST['iol2HolladayOS']);
			$iol2srk_tOS = imw_real_escape_string($_REQUEST['iol2srk_tOS']);
			$iol2HofferOS = imw_real_escape_string($_REQUEST['iol2HofferOS']);
			$iol3OS = $_REQUEST['iol3OS'];
				if($iol3OS!=''){
					$iol3OS = imw_real_escape_string($objTests->getLenseId($iol3OS));
				}
			$iol3PowerOS = imw_real_escape_string($_REQUEST['iol3PowerOS']);
			$iol3HolladayOS = imw_real_escape_string($_REQUEST['iol3HolladayOS']);
			$iol3srk_tOS = imw_real_escape_string($_REQUEST['iol3srk_tOS']);
			$iol3HofferOS = imw_real_escape_string($_REQUEST['iol3HofferOS']);
			$iol4OS = $_REQUEST['iol4OS'];
				if($iol4OS!=''){
					$iol4OS = imw_real_escape_string($objTests->getLenseId($iol4OS));
				}
			$iol4PowerOS = imw_real_escape_string($_REQUEST['iol4PowerOS']);
			$iol4HolladayOS = imw_real_escape_string($_REQUEST['iol4HolladayOS']);
			$iol4srk_tOS = imw_real_escape_string($_REQUEST['iol4srk_tOS']);
			$cellCountOS = imw_real_escape_string($_REQUEST['cellCountOS']);
			$notesOS = imw_real_escape_string($_REQUEST['notesOS']);
			$pachymetryValOS = imw_real_escape_string($_REQUEST['pachymetryValOS']);
			$pachymetryCorrecOS = imw_real_escape_string($_REQUEST['pachymetryCorrecOS']);
			$cornealDiamOS = imw_real_escape_string($_REQUEST['cornealDiamOS']);
			$dominantEyeOS = imw_real_escape_string($_REQUEST['dominantEyeOS']);
			$pupilSize1OS = imw_real_escape_string($_REQUEST['pupilSize1OS']);
			$pupilSize2OS = imw_real_escape_string($_REQUEST['pupilSize2OS']);
			$acdOD = imw_real_escape_string($_REQUEST['acdOD']);
			$acdOS = imw_real_escape_string($_REQUEST['acdOS']);
			$w2wOD = imw_real_escape_string($_REQUEST['w2wOD']);
			$w2wOS = imw_real_escape_string($_REQUEST['w2wOS']);
			
			$forum_procedure = (isset($_REQUEST['forum_procedure']))?imw_real_escape_string($_REQUEST['forum_procedure']):"";
			$zeissAction = (isset($_REQUEST['forum_procedure']))?$_POST['zeissAction']:false;
	
			$cataractOS = $_REQUEST['cataractOS'];
				if($cataractOS=='on'){
					$cataractOS = 1;
				}
			$astigmatismOS = $_REQUEST['astigmatismOS'];
				if($astigmatismOS=='on'){
					$astigmatismOS = 1;
				}
			$myopiaOS = $_REQUEST['myopiaOS'];
				if($myopiaOS){
					$myopiaOS = 1;
				}
			$selecedIOLsOS = imw_real_escape_string($_REQUEST['selecedIOLsOS']);
				//$selecedIOLsOS = getLenseId($selecedIOLsOS);
			$notesAssesmentPlansOS = imw_real_escape_string($_REQUEST['notesAssesmentPlansOS']);
			$lriOS = $_REQUEST['lriOS'];
				if($lriOS=="on"){ $lriOS = 1; }
			$dlOS = $_REQUEST['dlOS'];
				if($dlOS=="on"){ $dlOS = 1; }
			$synechiolysisOS = $_REQUEST['synechiolysisOS'];
				if($synechiolysisOS=="on"){ $synechiolysisOS = 1; }
			$irishooksOS = $_REQUEST['irishooksOS'];
				if($irishooksOS=="on"){ $irishooksOS = 1; }
			$trypanblueOS = $_REQUEST['trypanblueOS'];
				if($trypanblueOS=="on"){ $trypanblueOS = 1; }
			$flomaxOS = $_REQUEST['flomaxOS'];
				if($flomaxOS=="on"){ $flomaxOS = 1; }
			$cutsOS = $_REQUEST['cutsOS'];
			$lengthCutsOS = imw_real_escape_string($_REQUEST['lengthCutsOS']);
			$legnthOSSelect = imw_real_escape_string($_REQUEST['lengthOSSelect']);
	
			$axisOS = imw_real_escape_string($_REQUEST['axisOS']);
			$superiorOS = $_REQUEST['superiorOS'];
				if($superiorOS=="on"){ $superiorOS = 1; }
			$inferiorOS = $_REQUEST['inferiorOS'];
				if($inferiorOS=="on"){ $inferiorOS = 1; }
			$nasalOS = $_REQUEST['nasalOS'];
				if($nasalOS=="on"){ $nasalOS = 1; }
			$temporalOS = $_REQUEST['temporalOS'];
				if($temporalOS=="on"){ $temporalOS = 1; }
			$STOS = $_REQUEST['STOS'];
				if($STOS=="on"){ $STOS = 1; }
			$SNOS = $_REQUEST['SNOS'];
				if($SNOS=="on"){ $SNOS = 1; }
			$ITOS = $_REQUEST['ITOS'];
				if($ITOS=="on"){ $ITOS = 1; }
			$INOS = $_REQUEST['INOS'];
				if($INOS=="on"){ $INOS = 1; }
			$physicianSelected = imw_real_escape_string($_REQUEST['physicianSelected']);
			$physicianSignature = imw_real_escape_string($_REQUEST['physicianSignature']);
	
			$physicianSelectedOS = imw_real_escape_string($_REQUEST['physicianSelectedOS']);
			$physicianSignatureOS = imw_real_escape_string($_REQUEST['physicianSignatureOS']);
			$examTime = $_POST["elem_examTime"];
			$examDate = getDateFormatDB($_POST["elem_examDate"]);
			if($examDate=='' || $examDate=='0000-00-00') $examDate = date('Y-m-d');
			$elem_noP = imw_real_escape_string($_POST["elem_noP"]);
			$ordrby = imw_real_escape_string($_POST["elem_opidTestOrdered"]);
			$ordrdt = getDateFormatDB($_POST["elem_opidTestOrderedDate"]);
	
			//Encounter Id
			if(isset($_POST["elem_masterEncounterId"]) && !empty($_POST["elem_masterEncounterId"])){
				$encounter_id = $_POST["elem_masterEncounterId"];
			}else{
				$encounter_id = $objTests->getEncounterId();
			}
	
			//Ag 09 --
			$sur_dt_od = $_POST["sur_dateOD"];
			$tmp = $_POST["elem_proc_od"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$proc_od = "".$tmp2;
	
			$tmp = $_POST["elem_anes_od"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$anes_od = "".$tmp2;
	
			$tmp = $_POST["elem_visc_od"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$visc_od = "".$tmp2;
	
	
			$visc_od_other = imw_real_escape_string($_POST["elem_visc_od_other"]);
	
			$tmp = $_POST["elem_opts_od"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$opts_od = "".$tmp2;
			$opts_od_other = imw_real_escape_string($_POST["elem_opts_od_other"]);
	
			$iol2dn_od = imw_real_escape_string($_POST["elem_iol2dn_od"]);
	
			$sur_dt_os = imw_real_escape_string($_POST["sur_dateOS"]);
	
			$tmp = $_POST["elem_proc_os"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$proc_os = "".$tmp2;
	
			$tmp = $_POST["elem_anes_os"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$anes_os = "".$tmp2;
	
			$tmp = $_POST["elem_visc_os"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$visc_os = "".$tmp2;
			$visc_os_other = imw_real_escape_string($_POST["elem_visc_os_other"]);
	
	
			$tmp = $_POST["elem_opts_os"];
			$tmp2 = "";
			if(count($tmp)>0){
				foreach($tmp as $t1 => $t2){
					if(!empty($tmp2)){
						$tmp2 .= ",";
					}
					$tmp2 .= "".$t2;
				}
			}
			$opts_os = "".$tmp2;
	
			$opts_os_other = imw_real_escape_string($_POST["elem_opts_os_other"]);
			$iol2dn_os = imw_real_escape_string($_POST["elem_iol2dn_os"]);
	
			//print_r($_POST);
			//exit();
	
			//Ag 09 --
			
			//chart notes values
			$cn_mrk_val = imw_real_escape_string($_POST["el_cn_mrk_val"]);		
			
			//========== GETTING FORM EXISTS OR NOT
	
			//check
			if(empty($iol_master_id)){
	
				//Check if scan doc id exists in session
				$arrTest2edit = (isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])) ? unserialize($_SESSION["test2edit"]) : "" ;
	
				if(isset($arrTest2edit["IOL_Master"]) && !empty($arrTest2edit["IOL_Master"])){
	
					//Check in db
					$cQry = "select iol_master_id FROM iol_master_tbl
							WHERE patient_id ='".$patient_id."'
							AND iol_master_id = '".$arrTest2edit["IOL_Master"]."' ";
					$row = sqlQuery($cQry);
					$iol_master_id = (($row == false) || empty($row["iol_master_id"])) ? "0" : $row["iol_master_id"];
	
					//Unset Session IOL_Master
					$arrTest2edit["IOL_Master"] = "";
					$arrTest2edit["IOL_Master"] = NULL;
					unset($arrTest2edit["IOL_Master"]);
					//Reset session
					$_SESSION["test2edit"] = serialize($arrTest2edit);
	
				}else{
	
					$cQry = "select iol_master_id FROM iol_master_tbl
							WHERE patient_id ='".$patient_id."'
							AND examDate = '".$examDate."' AND examTime = '".$examTime."' ";
					$row = sqlQuery($cQry);
					$iol_master_id = (($row == false) || empty($row["iol_master_id"])) ? "0" : $row["iol_master_id"];
	
				}
			}
			/// Check
			if(isset($_FILES['filePDF']) && $_FILES['filePDF']['name']!=""){
				if(!empty($_SESSION['patient'])){
					// $oSaveFile = new SaveFile($_SESSION['patient']);
					 $original_file=array();
					 $original_file["name"]=$_FILES['filePDF']['name'];
					 $original_file["type"]=$_FILES['filePDF']['type'];
					 $original_file["size"]=$_FILES['filePDF']['size'];
					 $original_file["tmp_name"]=$_FILES['filePDF']['tmp_name'];
					 $formName = $_REQUEST['formName'];
					 $file_pointer = $oSaveFile->copyfile($original_file,"Scan/".$formName);
					 if($file_pointer && file_exists("../main/uploaddir/".$file_pointer)){	
					 
					   $pdfFile = realpath($oSaveFile->upDir.$file_pointer);
					   $path_parts = pathinfo($pdfFile);
						   if($path_parts['extension'] == "PDF" || $path_parts['extension'] == "pdf"){
							   $tetxmlFile = realpath($oSaveFile->upDir.$oSaveFile->pDir)."\Scan\\".$formName."\\".$path_parts['filename'].".tetml";//die();
							   include_once("tetml.php");
							  //create_tetml($pdfFile,$tetxmlFile);
							   if(file_exists($tetxmlFile)){
								  include_once("xmlParser.php"); 
								  $arrPDF = XMLParser($tetxmlFile);
								  $k1IolMaster1OD = $arrPDF['OD']['K1'][0].$arrPDF['OD']['K1'][1];
								  $k1IolMaster2OD = $arrPDF['OD']['K1'][6];
								  $k2IolMaster1OD = $arrPDF['OD']['K2'][0].$arrPDF['OD']['K2'][1];
								  $k2IolMaster2OD = $arrPDF['OD']['K2'][6];
								  $cylIolMaster1OD = $arrPDF['OD']['Cyl'][0].$arrPDF['OD']['Cyl'][1];
								  $cylIolMaster2OD = $arrPDF['OD']['Cyl'][3];
								  $aveIolMasterOD = $arrPDF['OD']['R/SE'][2].$arrPDF['OD']['R/SE'][3];
								  $iolMasterLengthOD = $arrPDF['OD']['AL'][0].$arrPDF['OD']['AL'][1];
								  $acdOD = $arrPDF['OD']['opt ACD'][0].$arrPDF['OD']['opt ACD'][1];
								  
								  $k1IolMaster1OS = $arrPDF['OS']['K1'][0].$arrPDF['OS']['K1'][1];
								  $k1IolMaster2OS = $arrPDF['OS']['K1'][6];
								  $k2IolMaster1OS = $arrPDF['OS']['K2'][0].$arrPDF['OS']['K2'][1];
								  $k2IolMaster2OS = $arrPDF['OS']['K2'][6];
								  $cylIolMaster1OS = $arrPDF['OS']['Cyl'][0].$arrPDF['OS']['Cyl'][1];
								  $cylIolMaster2OS = $arrPDF['OS']['Cyl'][3];
								  $aveIolMasterOS =  $arrPDF['OS']['R/SE'][2].$arrPDF['OS']['R/SE'][3];
								  $iolMasterLengthOS = $arrPDF['OS']['AL'][0].$arrPDF['OS']['AL'][1];
								  $acdOS = $arrPDF['OS']['opt ACD'][0].$arrPDF['OS']['opt ACD'][1];
								  $refreshPage = 1;
								  @unlink($tetxmlFile);@unlink($pdfFile);
							}
						 }else  echo "Plz Upload PDF file only";
					}
				}
			}
			$newFlag = false;
			if(empty($iol_master_id)){ //if($getDuplicateCheckRow<=0){
				$insertStr = "INSERT INTO iol_master_tbl SET form_id = '".$formId."', patient_id = '$patient_id', ";
				$newFlag = true;
			}else{
				$insertStr = "UPDATE iol_master_tbl SET";
			}
	
			$insertStr .= " 
							mrSOD = '$mrSOD',
							mrCOD = '$mrCOD',
							mrAOD = '$mrAOD',
							visionOD = '$visionOD',
							glareOD = '$glareOD',
							performedByOD = '$performedByOD',
							dateOD = '$dateOD',
							autoSelectOD = '$autoSelectOD',
							iolMasterSelectOD = '$iolMasterSelectOD',
							topographerSelectOD = '$topographerSelectOD',
							k1Auto1OD = '$k1Auto1OD',
							k1Auto2OD = '$k1Auto2OD',
							k1IolMaster1OD = '$k1IolMaster1OD',
							k1IolMaster2OD = '$k1IolMaster2OD',
							k1Topographer1OD = '$k1Topographer1OD',
							k1Topographer2OD = '$k1Topographer2OD',
							k2Auto1OD = '$k2Auto1OD',
							k2Auto2OD = '$k2Auto2OD',
							k2IolMaster1OD = '$k2IolMaster1OD',
							k2IolMaster2OD = '$k2IolMaster2OD',
							k2Topographer1OD = '$k2Topographer1OD',
							k2Topographer2OD = '$k2Topographer2OD',
							cylAuto1OD = '$cylAuto1OD',
							cylAuto2OD = '$cylAuto2OD',
							cylIolMaster1OD = '$cylIolMaster1OD',
							cylIolMaster2OD = '$cylIolMaster2OD',
							cylTopographer1OD = '$cylTopographer1OD',
							cylTopographer2OD = '$cylTopographer2OD',
							aveAutoOD = '$aveAutoOD',
							aveIolMasterOD = '$aveIolMasterOD',
							aveTopographerOD = '$aveTopographerOD',
							contactLengthOD = '$contactLengthOD',
							immersionLengthOD = '$immersionLengthOD',
							iolMasterLengthOD = '$iolMasterLengthOD',
							contactNotesOD = '$contactNotesOD',
							immersionNotesOD = '$immersionNotesOD',
							iolMasterNotesOD = '$iolMasterNotesOD',
							performedByPhyOD = '$performedByPhyOD',
							powerIolOD = '$powerIolOD',
							holladayOD = '$holladayOD',
							srk_tOD = '$srk_tOD',
							hofferOD = '$hofferOD',
							iol1OD = '$iol1OD',
							iol1PowerOD = '$iol1PowerOD',
							iol1HolladayOD = '$iol1HolladayOD',
							iol1srk_tOD = '$iol1srk_tOD',
							iol1HofferOD = '$iol1HofferOD',
							iol2OD = '$iol2OD',
							iol2PowerOD = '$iol2PowerOD',
							iol2HolladayOD = '$iol2HolladayOD',
							iol2srk_tOD = '$iol2srk_tOD',
							iol2HofferOD = '$iol2HofferOD',
							iol3OD = '$iol3OD',
							iol3PowerOD = '$iol3PowerOD',
							iol3HolladayOD = '$iol3HolladayOD',
							iol3srk_tOD = '$iol3srk_tOD',
							iol3HofferOD = '$iol3HofferOD',
							iol4OD = '$iol4OD',
							iol4PowerOD = '$iol4PowerOD',
							iol4HolladayOD = '$iol4HolladayOD',
							iol4srk_tOD = '$iol4srk_tOD',
							iol4HofferOD = '$iol4HofferOD',
							cellCountOD = '".imw_real_escape_string($cellCountOD)."',
							notesOD = '$notesOD',
							pachymetryValOD = '$pachymetryValOD',
							pachymetryCorrecOD = '$pachymetryCorrecOD',
							cornealDiamOD = '$cornealDiamOD',
							dominantEyeOD = '$dominantEyeOD',
							pupilSize1OD = '$pupilSize1OD',
							pupilSize2OD = '$pupilSize2OD',
							cataractOD = '$cataractOD',
							astigmatismOD = '$astigmatismOD',
							myopiaOD = '$myopiaOD',
							selecedIOLsOD = '$selecedIOLsOD',
							notesAssesmentPlansOD = '$notesAssesmentPlansOD',
							lriOD = '$lriOD',
							dlOD = '$dlOD',
							synechiolysisOD = '$synechiolysisOD',
							irishooksOD = '$irishooksOD',
							trypanblueOD = '$trypanblueOD',
							flomaxOD = '$flomaxOD',
							cutsOD = '$cutsOD',
							lengthOD = '$lengthCutsOD',
							lengthTypeOD = '$lengthSelectOD',
							axisOD = '$axisOD',
							superiorOD = '$superiorOD',
							inferiorOD = '$inferiorOD',
							nasalOD = '$nasalOD',
							temporalOD = '$temporalOD',
							STOD = '$STOD',
							SNOD = '$SNOD',
							ITOD = '$ITOD',
							INOD = '$INOD',
							mrSOS = '".imw_real_escape_string($mrSOS)."',
							mrCOS = '".imw_real_escape_string($mrCOS)."',
							mrAOS = '".imw_real_escape_string($mrAOS)."',
							visionOS = '$visionOS',
							glareOS = '$glareOS',
							performedByOS = '$phyTechListOS',
							dateOS = '$dateOS',
							autoSelectOS = '$autoSelectOS',
							iolMasterSelectOS = '$iolMasterSelectOS',
							topographerSelectOS = '$topographerSelectOS',
							k1Auto1OS = '$k1Auto1OS',
							k1Auto2OS = '$k1Auto2OS',
							k1IolMaster1OS = '$k1IolMaster1OS',
							k1IolMaster2OS = '$k1IolMaster2OS',
							k1Topographer1OS = '$k1Topographer1OS',
							k1Topographer2OS = '$k1Topographer2OS',
							k2Auto1OS = '$k2Auto1OS',
							k2Auto2OS = '$k2Auto2OS',
							k2IolMaster1OS = '$k2IolMaster1OS',
							k2IolMaster2OS = '$k2IolMaster2OS',
							k2Topographer1OS = '$k2Topographer1OS',
							k2Topographer2OS = '$k2Topographer2OS',
							cylAuto1OS = '$cylAuto1OS',
							cylAuto2OS = '$cylAuto2OS',
							cylIolMaster1OS = '$cylIolMaster1OS',
							cylIolMaster2OS = '$cylIolMaster2OS',
							cylTopographer1OS = '$cylTopographer1OS',
							cylTopographer2OS = '$cylTopographer2OS',
							aveAutoOS = '$aveAutoOS',
							aveIolMasterOS = '$aveIolMasterOS',
							aveTopographerOS = '$aveTopographerOS',
							contactLengthOS = '$contactLengthOS',
							immersionLengthOS = '$immersionLengthOS',
							iolMasterLengthOS = '$iolMasterLengthOS',
							contactNotesOS = '$contactNotesOS',
							immersionNotesOS = '$immersionNotesOS',
							performedIolOS = '$performedIolOS',
							iolMasterNotesOS = '$iolMasterNotesOS',
							powerIolOS = '$powerIolOS',
							holladayOS = '$holladayOS',
							srk_tOS = '$srk_tOS',
							hofferOS = '$hofferOS',
							iol1OS = '$iol1OS',
							iol1PowerOS = '$iol1PowerOS',
							iol1HolladayOS = '$iol1HolladayOS',
							iol1srk_tOS = '$iol1srk_tOS',
							iol1HofferOS = '$iol1HofferOS',
							iol2OS = '$iol2OS',
							iol2PowerOS = '$iol2PowerOS',
							iol2HolladayOS = '$iol2HolladayOS',
							iol2srk_tOS = '$iol2srk_tOS',
							iol2HofferOS = '".$iol2HofferOS."',
							iol3OS = '$iol3OS',
							iol3PowerOS = '$iol3PowerOS',
							iol3HolladayOS = '$iol3HolladayOS',
							iol3srk_tOS = '$iol3srk_tOS',
							iol3HofferOS = '$iol3HofferOS',
							iol4OS = '$iol4OS',
							iol4PowerOS = '$iol4PowerOS',
							iol4HolladayOS = '$iol4HolladayOS',
							iol4srk_tOS = '$iol4srk_tOS',
							iol4HofferOS = '".imw_real_escape_string($iol4HofferOS)."',
							cellCountOS = '$cellCountOS',
							notesOS = '$notesOS',
							pachymetryValOS = '$pachymetryValOS',
							pachymetryCorrecOS = '$pachymetryCorrecOS',
							cornealDiamOS = '$cornealDiamOS',
							dominantEyeOS = '$dominantEyeOS',
							pupilSize1OS = '$pupilSize1OS',
							pupilSize2OS = '$pupilSize2OS',
							cataractOS = '$cataractOS',
							astigmatismOS = '$astigmatismOS',
							myopiaOS = '$myopiaOS',
							selecedIOLsOS = '$selecedIOLsOS',
							notesAssesmentPlansOS = '$notesAssesmentPlansOS',
							lriOS = '$lriOS',
							dlOS = '$dlOS',
							synechiolysisOS = '$synechiolysisOS',
							irishooksOS = '$irishooksOS',
							trypanblueOS = '$trypanblueOS',
							flomaxOS = '$flomaxOS',
							cutsOS = '$cutsOS',
							lengthOS = '$lengthCutsOS',
							lengthTypeOS = '$lengthOSSelect',
							axisOS = '$axisOS',
							superiorOS = '$superiorOS',
							inferiorOS = '$inferiorOS',
							nasalOS = '$nasalOS',
							temporalOS = '$temporalOS',
							STOS = '$STOS',
							SNOS = '$SNOS',
							ITOS = '$ITOS',
							INOS = '$INOS',";
							
						if(!$zeissAction){  /*Prevent changing interpretation status if "Forum Add/Delete"*/
							$insertStr .=  "signedById = '$physicianSelected', ";
						}
						
			$insertStr .= " signature = '$physicianSignature',
							signedByOSId = '$physicianSelectedOS',
							signatureOS = '$physicianSignatureOS',
							examDate = '$examDate',
							examTime = '$examTime',
							encounter_id='".$encounter_id."',
							ordrby='".$ordrby."',ordrdt='".$ordrdt."',
							sur_dt_od='".$sur_dt_od."',
							proc_od='".$proc_od."',
							anes_od='".$anes_od."',
							visc_od='".$visc_od."',
							visc_od_other='".$visc_od_other."',
							opts_od='".$opts_od."',
							opts_od_other='".$opts_od_other."',
							iol2dn_od='".$iol2dn_od."',
	
							sur_dt_os='".$sur_dt_os."',
							proc_os='".$proc_os."',
							anes_os='".$anes_os."',
							visc_os='".$visc_os."',
							visc_os_other='".$visc_os_other."',
							opts_os='".$opts_os."',
							opts_os_other='".$opts_os_other."',
							iol2dn_os='".$iol2dn_os."',
							acdOD='".$acdOD."',
							acdOS='".$acdOS."',
							".$signPathQry."
							".$signDTmQry."
							w2wOD='".$w2wOD."',
							w2wOS='".$w2wOS."',
							forum_procedure='".$forum_procedure."',
							cn_mrk_val='".$cn_mrk_val."'
							";
	
			if(!empty($iol_master_id)){ //($getDuplicateCheckRow>0){
				$insertStr .= " WHERE iol_master_id = '$iol_master_id'";
			}
			$insertQry = imw_query($insertStr);
			if(empty($iol_master_id)){ //($getDuplicateCheckRow<=0){
				$iol_master_id = $printId = imw_insert_id();
			}
			
			/*code, Purpose: Generate HL7 message for Test*/
			$zeissMsgType="IOL-MASTER";
			$zeissPatientId = $patient_id;
			$zeissTestId = $iol_master_id;
			include("./zeissTestHl7.php");
			/*End code*/
	
			/*--interpretation starts--*/
			$objTests->interpret_if_scan_exists($_SESSION['patient'], "IOL_Master", $iol_master_id);
			/*--interpretation end--*/
	
			//============================== INSERT FORMULA VALUE FOR PROVIDER TO THE PATIENT OD
			$getExistsODStr = "SELECT * FROM iolphyformulavalues
								WHERE patient_id = '$patient_id'
								AND provider_id = '$performedByPhyOD'
								AND form_id = '$form_id'
								AND type_OD_OS = 'OD'";
			$getExistsODQry = imw_query($getExistsODStr);
			if(imw_num_rows($getExistsODQry)>0){
				$insertUpdateODStr = "UPDATE iolphyformulavalues SET ";
			}else{
				$insertUpdateODStr = "INSERT INTO iolphyformulavalues SET
										patient_id = '$patient_id',
										provider_id = '$performedByPhyOD',
										form_id = '$form_id',
										type_OD_OS = 'OD', ";
			}
			$insertUpdateODStr.= "iol1 = '$iol1OD',
									iol1Power = '$iol1PowerOD',
									iol1Holladay = '$iol1HolladayOD',
									iol1srk_t = '$iol1srk_tOD',
									iol1Hoffer = '$iol1HofferOD',
									iol2 = '$iol2OD',
									iol2Power = '$iol2PowerOD',
									iol2Holladay = '$iol2HolladayOD',
									iol2srk_t = '$iol2srk_tOD',
									iol2Hoffer = '$iol2HofferOD',
									iol3 = '$iol3OD',
									iol3Power = '$iol3PowerOD',
									iol3Holladay = '$iol3HolladayOD',
									iol3srk_t = '$iol3srk_tOD',
									iol3Hoffer = '$iol3HofferOD',
									iol4 = '$iol4OD',
									iol4Power = '$iol4PowerOD',
									iol4Holladay = '$iol4HolladayOD',
									iol4srk_t = '$iol4srk_tOD',
									iol4Hoffer = '$iol4HofferOD',
									cellCount = '$cellCountOD',
									pachymetryVal = '$pachymetryValOD',
									pachymetryCorrec = '$pachymetryCorrecOD',
									cornealDiam = '$cornealDiamOD',
									dominantEye = '$dominantEyeOD',
									pupilSize1 = '$pupilSize1OD',
									pupilSize2  = '$pupilSize2OD',
									notes = '$notesOD',
									plan = '$selecedIOLsOD'";
	
				if(imw_num_rows($getExistsODQry)>0){
				$insertUpdateODStr.= " WHERE patient_id = '$patient_id'
										AND provider_id = '$performedByPhyOD'
										AND form_id = '$form_id'
										AND type_OD_OS = 'OD'";
				}
				$insertUpdateODQry = imw_query($insertUpdateODStr);
			//============================== INSERT FORMULA VALUE FOR PROVIDER TO THE PATIENT OD
	
			//============================== INSERT FORMULA VALUE FOR PROVIDER TO THE PATIENT OS
			if($performedByOS!=''){
				$getExistsOSStr = "SELECT * FROM iolphyformulavalues
									WHERE patient_id = '$patient_id'
									AND provider_id = '$performedByOS'
									AND form_id = '$form_id'
									AND type_OD_OS = 'OS'";
				$getExistsOSQry = imw_query($getExistsOSStr);
				if(imw_num_rows($getExistsOSQry)>0){
					$insertUpdateOSStr = "UPDATE iolphyformulavalues SET ";
				}else{
					$insertUpdateOSStr = "INSERT INTO iolphyformulavalues SET
											patient_id = '$patient_id',
											provider_id = '$performedByOS',
											form_id = '$form_id',
											type_OD_OS = 'OS', ";
				}
				$insertUpdateOSStr.= "iol1 = '$iol1OS',
										iol1Power = '$iol1PowerOS',
										iol1Holladay = '$iol1HolladayOS',
										iol1srk_t = '$iol1srk_tOS',
										iol1Hoffer = '$iol1HofferOS',
										iol2 = '$iol2OS',
										iol2Power = '$iol2PowerOS',
										iol2Holladay = '$iol2HolladayOS',
										iol2srk_t = '$iol2srk_tOS',
										iol2Hoffer = '$iol2HofferOS',
										iol3 = '$iol3OS',
										iol3Power = '$iol3PowerOS',
										iol3Holladay = '$iol3HolladayOS',
										iol3srk_t = '$iol3srk_tOS',
										iol3Hoffer = '$iol3HofferOS',
										iol4 = '$iol4OS',
										iol4Power = '$iol4PowerOS',
										iol4Holladay = '$iol4HolladayOS',
										iol4srk_t = '$iol4srk_tOS',
										iol4Hoffer = '$iol4HofferOS',
										cellCount = '$cellCountOS',
										pachymetryVal = '$pachymetryValOS',
										pachymetryCorrec = '$pachymetryCorrecOS',
										cornealDiam = '$cornealDiamOS',
										dominantEye = '$dominantEyeOS',
										pupilSize1 = '$pupilSize1OS',
										pupilSize2  = '$pupilSize2OS',
										notes = '$notesOS',
										plan = '$selecedIOLsOS',
										acdOD='".$acdOD."',
										acdOS='".$acdOS."',
										w2wOD='".$w2wOD."',
										w2wOS='".$w2wOS."'
										";
					if(imw_num_rows($getExistsOSQry)>0){
					$insertUpdateOSStr.= " WHERE patient_id = '$patient_id'
											AND provider_id = '$performedByOS'
											AND form_id = '$form_id'
											AND type_OD_OS = 'OS'";
					}
					$insertUpdateOSQry = imw_query($insertUpdateOSStr);
			}
			//============================== INSERT FORMULA VALUE FOR PROVIDER TO THE PATIENT OS
			
			//Super-Bill -----------
			if(!empty($ordrby) && !empty($iol_master_id)){
				$arIn=array();
				$arIn["elem_physicianId"]=$ordrby;
				$arIn["doctorId"]=$ordrby;
				$arIn["caseId"]=$_POST["elem_masterCaseId"];
				$arIn["encounterId"]=$encounter_id;
				$arIn["date_of_service"]=$examDate;
				$arIn["sb_testId"]=$iol_master_id;
				$arIn["sb_testName"]="iOLMaster";
                                $arIn["form_id"]=$formId;
                                $arIn["test_interpreted"]= ((int)$physicianSelected > 0)? true : false;

				$oSuperbillSaver = new SuperbillSaver($patient_id);
				$oSuperbillSaver->save($arIn);	
			}
	
			//Super-Bill -----------
	
			//Test Pdf ---
				//saveTestPdfExe_2($patient_id,$iol_master_id,"IOL Master");
			//Test Pdf ---
	
			//FormId updates other tables -----
			if(!empty($form_id)){
				// Make chart notes valid
				$oChartNote->makeChartNotesValid($form_id);
								
				//Synchronize chart_correction_values			
				if(!empty($pachymetryCorrecOD) || !empty($pachymetryCorrecOS)){			
					$arr = array("elem_formId" => $form_id, "elem_od_readings" => $pachymetryValOD,
							  "elem_od_average" => "", "elem_od_correction_value" => $pachymetryCorrecOD,
							  "elem_os_readings" => $pachymetryValOS, "elem_os_average" => "",
							  "elem_os_correction_value" => $pachymetryCorrecOS, "patientid" => $patient_id,
							  "elem_cor_date" => $examDate
							 );
					$objTests->saveCorrectionValues($arr);
					//pachy
					//check if Pachy is made and syncronize correction values
					//check
					$sql = "SELECT * FROM pachy WHERE formId='".$form_id."' AND patientId='".$patient_id."' ";
					$row = sqlQuery($sql);
					if($row != false){
						//Update
						$sql = "UPDATE pachy SET ".
							 "pachy_od_readings = '".$pachymetryValOD."', ".
							 "pachy_od_average='', ".
							 "pachy_od_correction_value = '".$pachymetryCorrecOD."', ".
							 "pachy_os_readings = '".$pachymetryValOS."', ".
							 "pachy_os_average = '', ".
							 "pachy_os_correction_value = '".$pachymetryCorrecOS."' ".
							 "WHERE formId = '".$form_id."' AND patientId='".$patient_id."' ";
						$row = sqlQuery($sql);
					}
					
					//chartPtDiag
					$sql = "SELECT * FROM chart_ptPastDiagnosis WHERE patient_id='".$patient_id."' ";
					$row = sqlQuery($sql);
					if( $row != false ){
						$ptPastPachy = "";
						$arr=array();
						$arr["od_readings"]=$pachymetryValOD;
						$arr["od_average"]="";
						$arr["od_correction_value"]=$pachymetryCorrecOD;
						$arr["os_readings"]=$pachymetryValOS;
						$arr["os_average"]="";
						$arr["os_correction_value"]=$pachymetryValOS;
						$arr["cor_date"]=$examDate;
						$ptPastPachy = serialize($arr);
						$sql = "UPDATE chart_ptPastDiagnosis SET pachy = '".imw_real_escape_string($ptPastPachy)."' WHERE patient_id='".$patient_id."' ";
						$res = sqlQuery($sql);
					}
				}
			}
			
			//Javascript ----------
			echo "<script>".
					"try{";
					if(!empty($_REQUEST["pop"]) && $_REQUEST["pop"]=='1'){
						$afterSaveinPopup = 'yes';
					}else{
						$afterSaveinPopup = 'no';
						echo "top.alert_notification_show(\"Test has been saved.\");";
					}
					echo "
						window.location.replace(\"iol_master.php?noP=".$elem_noP."&tId=".$iol_master_id."&pop=".$_REQUEST["pop"]."&afterSaveinPopup=".$afterSaveinPopup."\");
					}catch(err){}".
				 "</script>";
				 exit;
		}
		break;	
	}
}

?>