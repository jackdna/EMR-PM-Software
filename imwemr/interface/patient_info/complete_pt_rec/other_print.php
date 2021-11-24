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

//if zFormId is set ---
if(isset($zFormId)&&!empty($zFormId)){
	$form_id=$zFormId;
}
else{
	$zFormId = '';
}
//if zFormId is set ---
$patientDetails = $cpr->get_pt_data($patient_id);
if(empty($date_of_service) && empty($patient_id) == false && empty($form_id) == false){
	$chartDetails = $cpr->print_getChartDetails($patient_id,$form_id);
	if($chartDetails['date_of_service']) $date_of_service = $chartDetails['date_of_service'];
}
if((@in_array("Problem List",$_REQUEST["chart_nopro"])) || (@in_array("Allergies List",$_REQUEST["chart_nopro"])) || (@in_array("Ocular Meds",$_REQUEST["chart_nopro"])) || (@in_array("Sys Meds",$_REQUEST["chart_nopro"])) || @in_array("general_health",$_REQUEST["chart_nopro"]) || (@in_array("ocular",$_REQUEST["chart_nopro"]))){
	if(in_array("Chart Notes",$_REQUEST["chart_nopro"])){
		echo "<page backtop=\"5mm\" backbottom=\"5mm\">";
		$cpr->print_hdrTopbar($patientDetails,$date_of_service,$_REQUEST["chart_nopro"]);
		$reportName="Clinical Summary";
		$cpr->print_reportName($reportName);
	}
	
	//echo "<p style='text-align:center;text-decoration:underline; font-size:16px; font-weight:bold;'>Clinical Summary</p>";	
	
	if( is_allscripts() && array_key_exists('patient', $_SESSION) )
	{
		$_REQUEST["problem_testActive"]="Active";
		$_REQUEST["allergies_testActive"]="Active";
		$_REQUEST["ocularAction"]="Active";	
		$_REQUEST["sysAction"]="Active";		
	}
		
	if(@in_array("Problem List",$_REQUEST["chart_nopro"])) {
		$cpr->print_getProbList($pid,$_REQUEST["problem_testActive"]);
	
	}
	if(@in_array("Allergies List",$_REQUEST["chart_nopro"])){
		$cpr->print_other_allergies($pid,$_REQUEST["allergies_testActive"]);
	}
	$arrTmp = array();
	if(@in_array("Medication List",$_REQUEST["chart_nopro"])){
		$arrTmp[1] = $_REQUEST["medicationActive"];
		$arrTmp[4] = $_REQUEST["medicationActive"];
	//	print_getMeds($patient_id,$arrTmp,$_REQUEST["chart_nopro"],$strDosToPrint1);
	}
	else {
		if(@in_array("Ocular Meds",$_REQUEST["chart_nopro"]) )
			$arrTmp[4] = $_REQUEST["ocularAction"];
		
		if(@in_array("Sys Meds",$_REQUEST["chart_nopro"]))
			$arrTmp[1] = $_REQUEST["sysAction"];	
	}
	if(count($arrTmp)>0){
		$cpr->print_getMeds_clinic($patient_id,$arrTmp,$_REQUEST["chart_nopro"],"","");
	}
	if(@in_array("general_health",$_REQUEST["chart_nopro"]) || (@in_array("ocular",$_REQUEST["chart_nopro"]))){
		$cpr->print_allGen($pid,$_REQUEST["chart_nopro"],"");
	}
	if(in_array("Chart Notes",$_REQUEST["chart_nopro"])){
		echo "</page>";
	}
}
?>
