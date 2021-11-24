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
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i")." GMT");  // HTTP 1.1
header("Cache-Control: no-store, no-cache, must-revalidate"); // ////////////////////////
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");

include_once($GLOBALS['srcdir'].'/classes/work_view/wv_functions.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/ChartTemp.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/ChartNote.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/ChartAP.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/User.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/CLSImageManipulation.php');
include_once($GLOBALS['srcdir']."/classes/SaveFile.php");
include_once($GLOBALS['fileroot'].'/interface/chart_notes/chart_globals.php');
global $cpr;
//if zFormId is set ---
if(isset($zFormId)&&!empty($zFormId)){
	$form_id=$zFormId;
}
else{
	$zFormId = '';
}
//if zFormId is set ---

$pid = $patient_id = $_SESSION['patient'];

$patientDetails[0] = $cpr->get_pt_data($pid);

//DOS
$chartDetails = $cpr->print_getChartDetails($pid,$form_id);
$date_of_service = $chartDetails["date_of_service"];
$date_of_service_ymd = $cpr->FormatDate_insert($date_of_service);
$elem_chartTemplateId = $chartDetails["templateId"];
/////End date of sevice Code////////////////


//Get Template Procedures ---
if(!empty($elem_chartTemplateId)){
	$oChartTemp = new ChartTemp();
	$tmp = $oChartTemp->getTempInfo($elem_chartTemplateId);
	if(!empty($tmp[1])){
		$elem_chartTempName = $tmp[1];
		$arrTempProc = (!empty($tmp[2])) ? explode(",", stripslashes($tmp[2])) : array();
	}
}
//Get Template Procedures ---

//print Header Top Bar
$arrdos=array("Chart Notes");
$cpr->print_hdrTopbar($patientDetails,$date_of_service,$arrdos);

//print Report name bar
$reportName="Visit Notes";
if(!in_array("Chart Notes",$_REQUEST["chart_nopro"])){
	$reportName="Clinical Summary";
}
//print_reportName($reportName);
?>
<table>
	<tr>
		<td class="text_b_w" style="width:30%;"><?php echo($reportName);?></td>
		<td style="width:1%;"></td>
		<td style="width:69%;" align="right">Printed by:&nbsp;<?php echo($patientDetails[0]['patientName']." - ".$patient_id);?>&nbsp;on&nbsp;<?php echo(date("m-d-Y H:i:s"));?></td>
	</tr>
	<tr>
		<td colspan="3"><hr/></td>
	</tr>
</table>
<?php
//print main header
$cpr->print_mainHeader($patientDetails,$chartDetails,$_REQUEST["chart_nopro"]);

if(@in_array("Record Release",$_REQUEST["chart_nopro"])) {
	$cpr->print_getDisclosedDetails($patient_id,$_REQUEST);
}

//Problem List
if(@in_array("Problem List",$_REQUEST["chart_nopro"]) && @in_array("Chart Notes",$_REQUEST["chart_nopro"])){ //|| @in_array("Medical History",$_REQUEST["chart_nopro"])
	$cpr->print_getProbList($patient_id,"",$strDosToPrint1,$zFormId);
}

//Allergies 
if(@in_array("Allergies List",$_REQUEST["chart_nopro"]) && @in_array("Chart Notes",$_REQUEST["chart_nopro"])){
	$cpr->print_getAllergies($patient_id,$_REQUEST["allergies_testActive"],$_REQUEST["chart_nopro"],$strDosToPrint1,$zFormId);
}

//Medication
$arrTmp=array();
if(@in_array("medication",$_REQUEST["chart_nopro"])){
	if(@in_array("Ocular Meds",$_REQUEST["chart_nopro"]) || @in_array("Sys Meds",$_REQUEST["chart_nopro"])){
		$arrTmp[1] = "Active";
		$arrTmp[4] = "Active";
	}
	else {
		if(@in_array("Medical History",$_REQUEST["chart_nopro"]) )
			
		
		if(@in_array("Medical History",$_REQUEST["chart_nopro"]))
			$arrTmp[1] = "Active";	
	}
	$arrTmp[1] = "Active";	
	$arrTmp[4] = "Active";
	if(count($arrTmp)>0){
		$cpr->print_getMeds($patient_id,$arrTmp,$_REQUEST["chart_nopro"],$strDosToPrint1,$zFormId);
	}
}

//SX
if((!in_array("Chart Notes",$_REQUEST["chart_nopro"]))&&((@in_array("Problem List",$_REQUEST["chart_nopro"])) || (@in_array("Allergies List",$_REQUEST["chart_nopro"])) || (@in_array("Ocular Meds",$_REQUEST["chart_nopro"])) || (@in_array("Sys Meds",$_REQUEST["chart_nopro"])) || @in_array("general_health",$_REQUEST["chart_nopro"]) || (@in_array("ocular",$_REQUEST["chart_nopro"])))){
	
	//echo "<p style='text-align:center;text-decoration:underline; font-size:16px; font-weight:bold;'>Clinical Summary</p>";	
	if(@in_array("Problem List",$_REQUEST["chart_nopro"]) ) {
		$cpr->print_getProbList($pid,$_REQUEST["problem_testActive"]);
	
	}
	if(@in_array("Allergies List",$_REQUEST["chart_nopro"])){
		$cpr->print_other_allergies($pid,$_REQUEST["allergies_testActive"]);
	}
	$arrTmp = array();
	if(@in_array("medication",$_REQUEST["chart_nopro"])){
		$arrTmp[1] = $_REQUEST["medicationActive"];
		$arrTmp[4] = $_REQUEST["medicationActive"];
		//$cpr->print_getMeds($patient_id,$arrTmp,$_REQUEST["chart_nopro"],$strDosToPrint1);
	}
	else {
		if(@in_array("Ocular Meds",$_REQUEST["chart_nopro"]) )
			$arrTmp[4] = $_REQUEST["ocularAction"];
		
		if(@in_array("Sys Meds",$_REQUEST["chart_nopro"]))
			$arrTmp[1] = $_REQUEST["sysAction"];	
	}
	if(count($arrTmp)>0){
		$cpr->print_getMeds($patient_id,$arrTmp,$_REQUEST["chart_nopro"],$strDosToPrint1,$zFormId);
	}
	if(@in_array("medical",$_REQUEST["chart_nopro"])){
		$arr_ocular=array('ocular');
		$cpr->print_allGen($pid,$arr_ocular,$strDosToPrint1);	
	}
	if(@in_array("general_health",$_REQUEST["chart_nopro"]) || (@in_array("ocular",$_REQUEST["chart_nopro"]))){
		$cpr->print_allGen($pid,$_REQUEST["chart_nopro"],"");
	}
	
}

if(@in_array("medical",$_REQUEST["chart_nopro"])){
	$cpr->print_getSx($patient_id,$_REQUEST["allergies_testActive"],$_REQUEST["chart_nopro"],$strDosToPrint1,$zFormId);
}
if(@in_array("general_health",$_REQUEST["chart_nopro"]) && in_array("Chart Notes",$_REQUEST["chart_nopro"])){
	$cpr->print_allGen($pid,$_REQUEST["chart_nopro"],$strDosToPrint1);
}
if(@in_array("medical",$_REQUEST["chart_nopro"])) {
	$cpr->print_getGenMed($patient_id,$_REQUEST["chart_nopro"],$strDosToPrint1);
}
if(@in_array("immunizations",$_REQUEST["chart_nopro"])){
	$cpr->print_getImmunization($patient_id);
}
if(@in_array("radiology",$_REQUEST["chart_nopro"])){
	$cpr->print_getRadiology($patient_id);
}
if(@in_array("vital_signs",$_REQUEST["chart_nopro"])){
	$cpr->print_vital_signs($patient_id);	
}
if(@in_array("lab",$_REQUEST["chart_nopro"])){
	$cpr->print_getLabResults($patient_id);
}

//CCHx
if(@in_array("chief_complaint",$_REQUEST["chart_nopro"])) {
	$cpr->print_getCCHx($patient_id,$form_id);
}

if(@in_array("Patient Amendment",$_REQUEST["chart_nopro"])) {
	$cpr->print_getAmendments($patient_id);
}

if(@in_array("vision",$_REQUEST["chart_nopro"])) {
	echo $cpr->print_getVision($patient_id,$form_id);
}

//Contect Lens
if(@in_array("pupil",$_REQUEST["chart_nopro"])) {
	$cpr->print_Pupil($patient_id,$form_id);
}

if(@in_array("eom",$_REQUEST["chart_nopro"])) {
	$cpr->print_EOM($patient_id,$form_id);
}
if(@in_array("external",$_REQUEST["chart_nopro"])) {
	$cpr->print_EE($patient_id,$form_id);
}
	
if(@in_array("la",$_REQUEST["chart_nopro"])) {
	$cpr->print_LA($patient_id,$form_id);
}

if(@in_array("iop",$_REQUEST["chart_nopro"]) && in_array("gonio",$_REQUEST["chart_nopro"])){
	$cpr->print_IOPGonio($patient_id,$form_id);
}
else if(@in_array("iop",$_REQUEST["chart_nopro"])) {
	$cpr->print_IOPGonio($patient_id,$form_id);
}else if(@in_array("gonio",$_REQUEST["chart_nopro"])) {
	$cpr->print_IOPGonio($patient_id,$form_id);
}

if(@in_array("sle",$_REQUEST["chart_nopro"])) {
	$cpr->print_SLE($patient_id,$form_id);
}

if(@in_array("fundus",$_REQUEST["chart_nopro"])) {
	$cpr->print_RV($patient_id,$form_id);
}

if(!isset($arrTempProc)||in_array("Refractive Surgery",$arrTempProc)){
	$cpr->print_RefractSurgery($patient_id,$form_id);
}

if(@in_array("AmslerGrid",$_REQUEST["chart_nopro"]) || @in_array("Chart Notes",$_REQUEST["chart_nopro"])){
	$cpr->print_Ophtha($patient_id,$form_id);
}

//Tests print
if(@in_array("Diagnostic Tests",$_REQUEST["chart_nopro"])){
	//include(dirname(__FILE__)."/leftForms_pdf_print.php");
	$cpr->print_vf($patient_id,$form_id,$_REQUEST["printTestRadioVF"]);
	$cpr->print_hrt($patient_id,$form_id,$_REQUEST["printTestRadioHRT"]);
	$cpr->print_oct($patient_id,$form_id,$_REQUEST["printTestRadioOCT"]);
	$cpr->print_gdx($patient_id,$form_id,$_REQUEST["printTestRadioGDX"]);
	$cpr->print_pachy($patient_id,$form_id,$_REQUEST["printTestRadioPachy"]);
	$cpr->print_ivfa($patient_id,$form_id,$_REQUEST["printTestRadioIVFA"]);
	$cpr->print_icg($patient_id,$form_id,$_REQUEST["printTestRadioICG"]);
	$cpr->print_disc($patient_id,$form_id,$_REQUEST["printTestRadioFundus"]);
	$cpr->print_external($patient_id,$form_id,$_REQUEST["printTestRadioExternal_Anterior"]);
	$cpr->print_topo($patient_id,$form_id,$_REQUEST["printTestRadioTopography"]);
	$cpr->print_cellcount($patient_id,$form_id,$_REQUEST["printTestRadioCellCount"]);
	$cpr->print_lab($patient_id,$form_id,$_REQUEST["printTestRadioLaboratories"]);
	$cpr->print_bscan($patient_id,$form_id,$_REQUEST["printTestRadioBscan"]);	
	$cpr->print_testother($patient_id,$form_id,$_REQUEST["printTestRadioOther"]);	
}

if(@in_array("Chart Notes",$_REQUEST["chart_nopro"])) {
	//$cpr->print_Memo($patient_id,$form_id);
}

if(@in_array("Include Provider Notes",$_REQUEST["chart_nopro"])){
	$cpr->print_PNotes($patient_id,$form_id);
}

if(@in_array("assessment",$_REQUEST["chart_nopro"]) && in_array("plan",$_REQUEST["chart_nopro"])){
	$cpr->print_AssessPlan($patient_id,$form_id);
	//Add Signatures
	//$cpr->print_chartsignatures($patient_id,$form_id);
}else if(@in_array("assessment",$_REQUEST["chart_nopro"])){
	$cpr->print_Assess($patient_id,$form_id);	
}else if(@in_array("plan",$_REQUEST["chart_nopro"])){
	$cpr->print_plan($patient_id,$form_id);	
}

if(@in_array("Patient Demographics",$_REQUEST["chart_nopro"])==true){
	include("print_demographics2.php");
}

if(@in_array("Patient LegalForms",$_REQUEST["chart_nopro"])==true){
	$cpr->printLegal_main();
}

//12-02-2013: #171; Also she showed that the patient communication notes are in the middle of the chart note vs being at the end which would be more appropriate.
//Start Include Include Patient Communication//
if(@in_array("Patient Communication",$_REQUEST["chart_nopro"])) {
	$cpr->print_getPtComm($patient_id);
}
if(@in_array("Chart Notes",$_REQUEST["chart_nopro"])) {
	$cpr->print_education_forms($patient_id);
}
if(@in_array("Chart Notes",$_REQUEST["chart_nopro"])) {
	$cpr->print_provider($patient_id);
	//userInfo($patient_id);
	$cpr->print_provider_info($patient_id,$form_id);
	$cpr->superbill_print($patient_id,$form_id);
	$cpr->print_future_appointment($patient_id);
	$cpr->print_test_future_appointments($patient_id);
	$cpr->referrals_to_other_providers($patient_id,$form_id);
	$cpr->print_clicnic($patient_id,$form_id);
	$cpr->pt_dos_facility($patient_id,$form_id,$date_of_service);
}
//End Include Include Patient Communication//
?>