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
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
$library_path = $GLOBALS['webroot'].'/library';
$patientId = $_REQUEST['patientId'];	
if(!$patientId) { $patientId = $_SESSION['patient'];}
$oSaveFile = new SaveFile($patient_id);
$browserIpad = 'no';
if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
	$browserIpad = 'yes';
}
//START GET NKDA STATUS
$nkdaAllergyStatus = "";
$noMedicationStatus = "";
$noMedicationComments = "";
$nkdaAllergyQry = "SELECT no_value, comments FROM commonNoMedicalHistory WHERE patient_id = '".$patientId."'  AND module_name = 'Allergy' || module_name = 'Medication' ";
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

$selectQuerey = "select * from surgery_center_pre_op_health_ques where patient_id = $patientId";
$rsSelectQuerey = imw_query($selectQuerey);
$numRowsSelectQuerey = imw_num_rows($rsSelectQuerey);
if($numRowsSelectQuerey > 0){
	$rowGetSelectQuerey = imw_fetch_array($rsSelectQuerey);			
	extract($rowGetSelectQuerey);
}
function changeDateMDY($dateStr){
	list($yyDate, $mmDate, $ddDate) = split('-', $dateStr);
	$showDate = $mmDate.'-'.$ddDate.'-'.$yyDate;
	return $showDate;
}
function underLine($to){
	$NBSP = "<u>";
	for($counter = 1; $counter<=$to; $counter++){
		$NBSP .= "&nbsp;";	
	}
	$NBSP .= "</u>";
	return $NBSP;
}
?>
<html>
<head>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
<script>
window.focus();
top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
</script>
</head>
<body class="body_c"> 
<?php
$lable = 'Pre-Op Health Questionnaire';

$table_main.='
	<style>
		td { border-bottom:solid 1px #ddd; padding:2px 3px;}
	</style>
	<table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
		<tr><td width="750" align="center" style="border:solid 1px #fff;"><strong>'.$lable.'</strong></td></tr>
	</table>		
	<table width="750" border="1" bordercolor="#ddd" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
		<tr>
			<td style="background-color:#999; color:white;" width="650"><font face="Verdana, Arial, Helvetica, sans-serif"; size="+1"><strong>Have you ever had :-</strong></font></td>
			<td style="background-color:#999; color:white;" width="100" height="20" >&nbsp;</td>
		</tr>';
		
		if($heartTrouble!=""){
			$table_main.='
				<tr>
					<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">Heart Trouble/Heart Attack</font></td>
					<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.$heartTrouble.'</font></td>
				</tr>';
		}
		
		if($stroke!=""){	
			$table_main.='
				<tr>
					<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">Stroke</font></td>
					<td ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.$stroke.'</font></td>
				</tr>';
		}
		if($HighBP!=""){	
			$table_main.='<tr>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">HighBP</font></td>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.$HighBP.'</font></td>
				</tr>';
		}
		if($anticoagulationTherapy!=""){	
			$table_main.='<tr>
				<td valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">Anticoagulation therapy (i.e. Blood Thinners)</font></td>
				<td ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.$anticoagulationTherapy.'</font></td>
				</tr>';
		}
		
		if($asthma!=""){
			$table_main.='<tr>
				<td  nowrap valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">Asthma, Sleep Apnea, Breathing Problems</font></td>
				<td ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.$asthma.'</font></td>
				</tr>';
		}
		if($tuberculosis!=""){	
			$table_main.='<tr>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">Tuberculosis</font></td>
				<td ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.$tuberculosis.'</font></td>
				</tr>';
		}
		if($diabetes!=""){
			$table_main.='<tr>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">Diabetes</font></td>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.$diabetes.'</font></td>
				</tr>';
		}
		if($insulinDependence=="Yes"){	
			$table_main.='<tr>
				<td  valign="top" >&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif";>
					<em>&nbsp;&nbsp;&nbsp;Insulin Dependent&nbsp;&nbsp;&nbsp;'.$insulinDependence.'</em></font></td>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif";></font></td>
				</tr>';
		}
		if($insulinDependence=="No"){		
			$table_main.='<tr>
				<td  valign="top">&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif"; size="-2"><em>&nbsp;&nbsp;&nbsp;Non-Insulin Dependent&nbsp;&nbsp;&nbsp;'.$insulinDependence.'</em></font></td>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-2"></font></td>
				</tr>';
		}	
		if($epilepsy!=""){	
			$table_main.='<tr>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">Epilepsy, Convulsions, Parkinson\'s, Vertigo</font></td>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.$epilepsy.'</font></td>
				</tr>';
		}
		if($restlessLegSyndrome!=""){	
			$table_main.='<tr>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">Restless Leg Syndrome</font></td>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.$restlessLegSyndrome.'</font></td>
				</tr>';
		}
		if($hepatitis!=""){	
			$table_main.='<tr>
				<td  valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">Hepatitis</font></td>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.$hepatitis.'</font></td>
				</tr>';
		}
		if(($hepatitis!="")&&($hepatitisA == 'true' || $hepatitisB == 'true' ||$hepatitisC == 'true')){	
			$table_main.='<tr>
				<td  valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif">';
				if($hepatitisA == 'true') $table_main.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;A&nbsp;&nbsp;&nbsp; Yes';
				if($hepatitisB == 'true') $table_main.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;B&nbsp;&nbsp;&nbsp; Yes';
				if($hepatitisC == 'true') $table_main.='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;C&nbsp;&nbsp;&nbsp; Yes';
				$table_main.='</font></td>
				<td  valign="top">&nbsp;</td>
				</tr>';
		}
		if($kidneyDisease!=""){	
			$table_main.='<tr>
				<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">Kidney Disease, Dialysis</font></td>
				<td valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.$kidneyDisease.'</font></td>
				</tr>';
		}
		if($shunt!=""){	
			$table_main.='<tr>
				<td  valign="top">&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif"; size="-2">&nbsp;&nbsp;&nbsp;<em>Do you have a Shunt&nbsp;&nbsp;&nbsp;'.$shunt.'</em></font></td>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"></font></td>
				</tr>';
		}
		if($fistula!=""){		
			$table_main.='<tr>
				<td valign="top">&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif"; size="-2">&nbsp;&nbsp;&nbsp;<em>Fistula&nbsp;&nbsp;&nbsp;'.$fistula.'</em></font></td>
				<td valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-2"></font></td>
				</tr>';
		}
		if($hivAutoimmuneDiseases!=""){
			$table_main.='<tr>
				<td  valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">HIV, Autoimmune Diseases</font></td>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.$hivAutoimmuneDiseases.'</font></td>
				</tr>';
		}
		if($hivTextArea!=""){
			$table_main.='<tr>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-2">'.stripslashes($hivTextArea).'</font></td>
				<td  valign="top">&nbsp;</td>
				</tr>';
		}
		if($cancerHistory!=""){		
			$table_main.='<tr>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">History of cancer</font></td>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.$cancerHistory.'</font></td>
				</tr>';
		}
		if($brest_cancer == "Lef" || $brest_cancer=="Rig"){
			$table_main.='<tr>
				<td  valign="top">&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;<em>Brest cancer';
				if($brest_cancer=="Lef") $table_main.=' Left &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Yes';
				if($brest_cancer=="Rig") $table_main.=' Right &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Yes';
				$table_main.='</em></font></td>
				<td  valign="top">&nbsp;</td>
				</tr>';
		}
		if($cancerHistoryDesc!=""){
			$table_main.='<tr>
				<td  valign="top">&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif"; size="-2">&nbsp;&nbsp;&nbsp;<em>Describe</em>&nbsp;&nbsp;&nbsp; '.stripslashes($cancerHistoryDesc).'</font></td>
				<td  valign="top">&nbsp;</td>
				</tr>';
		}
		if($organTransplant!=""){
			$table_main.='<tr>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif">Organ Transplant</font></td>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif">'.$organTransplant.'</font></td>
				</tr>';
		}
		if($organTransplantDesc!=""){
			$table_main.='<tr>
				<td  valign="top">&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;<em>Describe</em>&nbsp;&nbsp;&nbsp; '.stripslashes($organTransplantDesc).'</font></td>
				<td  valign="top">&nbsp;</td>
				</tr>';
		}
		if($anesthesiaBadReaction!=""){
			$table_main.='<tr>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif">A Bad Reaction to Local or General Anesthesia</font></td>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif">'.$anesthesiaBadReaction.'</font></td>
				</tr>';
		}
		if($otherTroubles!=""){
			$table_main.='<tr>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">Other&nbsp;&nbsp;&nbsp;'.stripslashes($otherTroubles).'</font></td>
				<td  valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-2"></font></td>
				</tr>';
		}
		
		
	//adminHealthquestionare
	$selectAdminQuestionsQry="select * from surgery_center_health_questioner order by healthQuestioner";
	$selectAdminQuestions=imw_query($selectAdminQuestionsQry);
	$selectAdminQuestionsRows=imw_num_rows($selectAdminQuestions);
	$inc=0;
	while($ResultselectAdminQuestions=imw_fetch_array($selectAdminQuestions))
	{
	foreach($ResultselectAdminQuestions as $key=>$value){
		$question[$i][$key]=$value;
	}
	$inc++;	
	}
	
	$getQuesQry=imw_query("select * from surgery_center_health_question_admin where patient_id = '".$patientId."' order by id");
	$k=0;
	$QuesnumRows=imw_num_rows($getQuesQry);
	while($getQuesRes=imw_fetch_array($getQuesQry)){
	foreach($getQuesRes as $key=>$val){
	$quest[$k][$key]=$val;
	}
	$k++;
	}
	if($QuesnumRows>0)
	{
	$t = 0;
	for($k=0;$k<ceil($QuesnumRows/2);$k++)
	{
	$i = $k;
	$quest[$k]['adminQuestion']; 
	$questionid[]=$quest[$k]['adminQuestionStatus'];
									
	$endTd = $endTd<= $QuesnumRows ? $t + 2 : $QuesnumRows;
		for($t=$t;$t<$endTd;$t++){
			if($quest[$t]['adminQuestionStatus']!=""){
				$table_inner3.='
					<tr>
						<td width="650" ><font face="Verdana, Arial, Helvetica, sans-serif">'.trim($quest[$t]['adminQuestion']).'</font>';
							if($quest[$t]['adminQuestionStatus']=="Yes" && trim($quest[$t]['adminQuestionDesc'])){
								$table_inner3.='<br><span style="font-weight:bold;font-style:italic;">Describe:&nbsp;</span>'.stripslashes($quest[$t]['adminQuestionDesc']);
							}
				$table_inner3.='
						</td>';
				if($t<$QuesnumRows) {
					$table_inner3.='
						<td width="100" style="vertical-align:text-top" ><font face="Verdana, Arial, Helvetica, sans-serif">'.$quest[$t]['adminQuestionStatus'].'</font></td>';
				}
				$table_inner3.='
					</tr>';
			}
		}
	}
}		

	$table_main.=$table_inner3;			
	$table_main.='</table>';
//START GETTING ALLERGIES REACTIONS TO DISPLAY
$queryGetAllergyPrec = "select * from lists where pid=$patientId and type in(3,7) and allergy_status='Active'";
$queryGetAllergyPrecRes = imw_query($queryGetAllergyPrec) or die(imw_error());
$show_nka_status = $show_review_status = "";
if($nkdaAllergyStatus == "Yes") {
	$show_nka_status = "&nbsp;(NKA:&nbsp;&nbsp;&nbsp;&nbsp;Yes)";	
}
if($allergies_status_reviewed == "Yes") {
	$show_review_status = "&nbsp;&nbsp;(Allergies&nbsp;Reviewed&nbsp;&nbsp;&nbsp;&nbsp;Yes)";	
}
if($noMedicationStatus == "Yes") {
	if(!$noMedicationComments) { $noMedicationComments = "_________"; }
	$show_no_medication_status = "&nbsp;(No Medication:&nbsp;&nbsp;&nbsp;&nbsp;Yes)<br>Comments: ".$noMedicationComments;
}

$table_main.='<table width="750" border="1" bordercolor="#ddd" cellpadding="0" cellspacing="0" style="margin-top:5px;">
	<tr height="35"> 
		<td style="background-color:#999;color:white;" width="400" valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"><strong>Allergies/Drug Reaction</strong></font></td>
		<td style="background-color:#999;color:white;" width="350" valign="top"><font face="Verdana, Arial, Helvetica, sans-serif">'.$show_nka_status.$show_review_status.'</font></td>
	</tr>
	<tr style="background-color:#ddd;" height="30">
		<td style="background-color:#ddd;" width="400" ><strong>Name</strong></td>
		<td style="background-color:#ddd;" width="350"><strong>Reaction</strong></td>
	</tr>';
				

if(imw_num_rows($queryGetAllergyPrecRes)>0) {
	while($allergyRow = imw_fetch_array($queryGetAllergyPrecRes)){
		$strPatientAllergyName = $allergyRow['title'];
		$strPatientAllergyreaction = $allergyRow['comments'];
		$table_main.='
				<tr> 
					<td >'.stripslashes($strPatientAllergyName).'</td>
					<td >'.stripslashes($strPatientAllergyreaction).'</td>
				</tr>';
	}
}else {
		for($q=1;$q<=3;$q++) {
			$table_main.='
				<tr height="30"> 
					<td >_____________</td>
					<td >_____________</td>
				</tr>';
		}
}
$table_main.='</table>';
//END GETTING ALLERGIES REACTIONS TO DISPLAY


$queryGetMedication = "select * from lists where pid=$patientId and (type='1' or type='4') and allergy_status = 'Active'";
$queryGetMedicationRes = imw_query($queryGetMedication) or die(imw_error());
$i_healthquest_medication = 1;
$table_main.='<table width="750" border="1" bordercolor="#ddd" cellpadding="0" cellspacing="0" style="margin-top:5px;">
	<tr height="35" style="" >
		<td style="background-color:#999;color:white;" valign="top" width="350" nowrap><font face="Verdana, Arial, Helvetica, sans-serif"><strong>Take Prescription Medications</strong></font></td>
		<td style="background-color:#999;color:white;" colspan="2" valign="top" width="400" nowrap><font face="Verdana, Arial, Helvetica, sans-serif">'.$show_no_medication_status.'</font></td>
	</tr>
	<tr>
		<td style="background-color:#ddd;"><strong>Name</strong></td>
		<td style="background-color:#ddd;" width="200"><strong>Dosage</strong></td>
		<td style="background-color:#ddd;"><strong>Sig</strong></td>
	</tr>';

if(imw_num_rows($queryGetMedicationRes)>0) {
	
	while($medicationRow = imw_fetch_array($queryGetMedicationRes)){													
		$strPatientMedicationName = $medicationRow['title'];		
		$strPatientMedicationDosage = $medicationRow['destination'];
		$strPatientMedicationSig = $medicationRow['sig'];										
		$table_main.='
				<tr> 
						<td>'.stripslashes($strPatientMedicationName).'</td>
						<td>'.stripslashes($strPatientMedicationDosage).'</td>
						<td>'.stripslashes($strPatientMedicationSig).'</td>
				</tr>';
	}
}else {
	for($q=1;$q<=3;$q++) {
		$table_main.='
				<tr> 
						<td>_____________</td>
						<td>_____________</td>
						<td>_____________</td>
				</tr>';
	}
}
$table_main.='</table>';
	
$table_main.='<table cellpadding="0" cellspacing="0" width="750" border="1" bordercolor="#ddd" style="margin-top:5px;">
	<tr>
		<td width="650" style="background-color:#999; color:white;" valign="top" nowrap><font face="Verdana, Arial, Helvetica, sans-serif"><strong>Do You:-</strong></font></td>
		<td width="100" style="background-color:#999; color:white;">&nbsp;</td>
	</tr>';
	
	if($walker!=""){	
		$table_main.='<tr>
			<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">Use a Wheel Chair, Walker or Cane</font></td>
			<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.$walker.'</font></td>
			</tr>';
	}
	if($contactLenses!=""){	
		$table_main.='<tr>
			<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif">Wear Contact lenses</font></td>
			<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif">'.$contactLenses.'</font></td>
			</tr>';
	}
	if($smoke!=""){	
		$table_main.='<tr>
			<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif">Smoke</font></td>
			<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif">'.$smoke.'</font></td>
			</tr>';
			
			if($smokeHowMuch!=""){
				$table_main.='<tr>
				<td valign="top" >&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif"; size="-2">&nbsp;&nbsp;&nbsp;<em>How much&nbsp;&nbsp;&nbsp;'.$smokeHowMuch.'</em></font></td>
				<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"></font></td>
				</tr>';
			}
			if($smoke=="Yes"){
				$table_main.='<tr><td colspan="2">&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif"; size="-2">&nbsp;&nbsp;&nbsp;<em>Patient advised not to smoke 24 H prior to surgery:</em></font>&nbsp;';
				if($smokeAdvise=="Yes"){	$table_main.=$smokeAdvise;}else{$table_main.="____";}
				$table_main.='</td></tr>';															
			}
	}
	if($alchohol!=""){	
		$table_main.='<tr>
			<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif">Drink Alcohol</font></td>
			<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif">'.$alchohol.'</font></td>
			</tr>';
			
			if($alchoholHowMuch!=""){
				$table_main.='<tr >
				<td valign="top" >&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif"; size="-2">&nbsp;&nbsp;&nbsp;<em>How much&nbsp;&nbsp;&nbsp;'.$alchoholHowMuch.'</em></font></td>
				<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif"; size="-2"></font></td>
				</tr>';
			}
			if($alchohol=="Yes"){
				$table_main.='<tr><td colspan="2">&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif"; size="-2">&nbsp;&nbsp;&nbsp;<em>Patient advised not to drink 24 H prior to surgery:</em></font>&nbsp;';
				if($alchoholAdvise=="Yes"){	$table_main.=$alchoholAdvise;}else{$table_main.="____";}
				$table_main.='</td></tr>';															
			}
			
	}
	if($autoInternalDefibrillator!=""){	
		$table_main.='<tr>
			<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif">Have an automatic internal defibrillator</font></td>
			<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif">'.$autoInternalDefibrillator.'</font></td>
			</tr>';
	}
	if($metalProsthetics!=""){
		$table_main.='<tr>
			<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif">Have any Metal Prosthetics</font></td>
			<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif">'.$metalProsthetics.'</font></td>
			</tr>';
			if($notes!=""){
				$table_main.='<tr>
				<td valign="top" >&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif">&nbsp;&nbsp;&nbsp;<em>Notes</em>&nbsp;&nbsp;'.stripslashes($notes).'</font></td>
				<td valign="top" ><font face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></td>
				</tr>';
			}
	}
	$table_main.='</table>';
	
if($signNurseId<>0 && $signNurseId<>"") {
	$NurseNameShow = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
	$signOnFileStatus = $signNurseStatus;	

}

//START CODE RELATED TO WITNESS SIGNATURE ON FILE
if($signWitness1Id<>0 && $signWitness1Id<>"") {
	$Witness1NameShow = $signWitness1LastName.", ".$signWitness1FirstName." ".$signWitness1MiddleName;
	$signOnFileWitness1Status = $signWitness1Status;	

}
//END CODE RELATED TO WITNESS SIGNATURE ON FILE


$table_main.='<table cellpadding="0" width="750" cellspacing="0" width="100%" border="1" bordercolor="#ddd" style="margin-top:5px;" >
	<tr><td colspan="3" style="border:solid 0px;">&nbsp;</td></tr>';
	
	if($emergencyContactPerson!=""){
		$table_main.='<tr>
			<td width="450" colspan="2" valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1"><strong>Emergency Contact Person:&nbsp;&nbsp;&nbsp;</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$emergencyContactPerson.'</font></td>
			<td valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">';
			if($emergencyContactPhone){$table_main.='<strong>Tel.</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$emergencyContactPhone;}
		$table_main.='&nbsp;</font></td></tr>';
	}
	
	if($patientSign!="" || get_number($dateQuestionnaire)!='00000000' || $witnessname!="" || $witnessSign!=""){	
		$patient_sign_image_path = str_ireplace('SigPlus_images/',"",$patient_sign_image_path);
		$witness_sign_image_path = str_ireplace('SigPlus_images/',"",$witness_sign_image_path);
		$ptSignLabel='';
		$wtSignLabel='';
		if($patient_sign_image_path) { $ptSignLabel = 'Patient Signature:';}
		if($witness_sign_image_path) { $wtSignLabel = 'Witness Signature:';}
		$table_main.='<tr>
			<td valign="top" width="300" rowspan="2" style="border-left:solid 1px #ddd;"><font face="Verdana, Arial, Helvetica, sans-serif"><strong>'.$ptSignLabel.'</strong><br><img src="'.'../../data/'.PRACTICE_PATH.'/'.$patient_sign_image_path.'" width="225" height="45" ></font></td>
			<td valign="top" width="150" rowspan="2" style="border-left:solid 1px #ddd;"><font face="Verdana, Arial, Helvetica, sans-serif">';
			if(get_number($dateQuestionnaire)!='00000000'){$table_main.='<strong>Date:</strong>&nbsp;&nbsp;&nbsp;'.get_date_format($dateQuestionnaire);}
			$table_main.='&nbsp;</font></td>
			<td width="290" valign="top" style="border-left:solid 1px #ddd;"><strong>Witness Name&nbsp;&nbsp;&nbsp;</strong>&nbsp;&nbsp;&nbsp;'.$witnessname.'</td>
			</tr>
			<tr>
				<td style="border-left:solid 1px #ddd;"><font face="Verdana, Arial, Helvetica, sans-serif"><strong>'.$wtSignLabel.'</strong><br><img src="'.'../../data/'.PRACTICE_PATH.'/'.$witness_sign_image_path.'" width="225" height="45" ></font></td>
			</tr>';
	}
	
				
				
	if( $NurseNameShow!="" || $signOnFileStatus!="" )
	{
		$table_main.='<tr>';
		$table_main.='<td colspan="2" valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.($NurseNameShow ? '<strong>Nurse:</strong>&nbsp;&nbsp;&nbsp;'.$NurseNameShow : '').'</font></td>';
		
		$table_main.='<td valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.($signOnFileStatus ? '<strong>Signature On File:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$signOnFileStatus : '').'</font></td>';
		$table_main.='</tr>';
	}
	
	if($Witness1NameShow!="" || $signOnFileWitness1Status!="")
	{	
		$table_main.='<tr>';
		$table_main.='<td valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.($Witness1NameShow ? '<strong>Witness:</strong>&nbsp;&nbsp;&nbsp;'.$Witness1NameShow : '').'</font></td>';
		$table_main.='<td valign="top"><font face="Verdana, Arial, Helvetica, sans-serif"; size="-1">'.($signOnFileWitness1Status ? '<strong>Signature On File:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$signOnFileWitness1Status : '').'</font></td>';
		$table_main.='</tr>';
	}
				
				$table_main.='</table>';

$table_main = str_ireplace('../../common/html2pdf/','',$table_main);
$file_path = write_html(utf8_decode(html_entity_decode($table_main)));
//$fp = fopen('../../../library/html_to_pdf/pdffile.html','w');
//$write_data = fwrite($fp,$table_main);
//echo $table_main;exit;
if($file_path){
?>
	<script>
		var browserIpad = "<?php echo $browserIpad;?>";
		if(browserIpad=="yes") {
			//window.open('../../../library/html_to_pdf/createPdf.php?op=p&font_size=10&page=4','_blank','');	
			html_to_pdf('<?php echo $file_path; ?>','p');
		}else {
			//window.frames['ifrm_PdfContent'].location.href = '../../../library/html_to_pdf/createPdf.php?op=p','pdfPrint','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'';
			html_to_pdf('<?php echo $file_path; ?>','p','',true);
		
		}
    </script>
<?php
}
?>
</body>
</html>				