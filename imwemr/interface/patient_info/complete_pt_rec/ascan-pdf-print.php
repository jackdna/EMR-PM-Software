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
include_once("../../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/SaveFile.php");
include_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/print_functions_class.php');
include_once($GLOBALS['fileroot'].'/interface/chart_notes/chart_globals.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/wv_functions.php');

$pid = $_SESSION['patient'];
$cpr = New CmnFunc($pid);

ob_start();

$today = date('m-d-Y');
$patient_id = $_SESSION['patient'];
$pid = $_SESSION['patient'];
$authUserId = $_SESSION['authId'];
$form_id = $_SESSION['form_id'];
if($form_id==''){
	$form_id = $_SESSION["finalize_id"];
}
// IF PRINT THEN FORM ID
$print_form_id = $_REQUEST['print_form_id'];
if($print_form_id){
	$form_id = $print_form_id;
}
// IF PRINT THEN FORM ID
// IF PRINT DOS SELECT FORM ID THEN FORM ID
$formIdToPrint = $_REQUEST['formIdToPrint'];
if($formIdToPrint){
	$form_id = $formIdToPrint;
}
// IF PRINT THEN FORM ID
$printId = $_REQUEST['printId'];

//Get Pt. Data
$pt_data = $cpr->get_pt_data($cpr->patient_id);

$p_imagename = $pt_data['p_imagename'];
if($p_imagename){
	$pt_images = $cpr->get_pt_images($p_imagename);
	$patient_img = $pt_images['patient_img'];		//Array 
	$patientImage = $pt_images['patientImage'];		//Single img
	$ChartNoteImagesString = $pt_images['ChartNoteImagesString'];
}

$qry1=imw_query("select * from  chart_left_cc_history where patient_id='$pid' and form_id='$form_id'");
$co=imw_num_rows($qry1);
if(($co > 0)){
	$crow=imw_fetch_array($qry1);
	$date_of_service = date("m-d-Y", strtotime($crow["date_of_service"]));	
 }

//Getting physicians and tech
$phyTechArray = $cpr->get_phy_and_tech();



//================= GETTIN RECORD FOR PRINT
if($printId==''){
	$getSurgicalRecordStr = "SELECT * FROM surgical_tbl WHERE patient_id = '$patient_id' AND form_id = '$form_id'";
}else{
	$getSurgicalRecordStr = "SELECT * FROM surgical_tbl WHERE surgical_id = '$printId'";
}
//echo($getSurgicalRecordStr);
$getSurgicalRecordQry = imw_query($getSurgicalRecordStr);
$rowsCount = imw_num_rows($getSurgicalRecordQry);
if($rowsCount>0){
	$getSurgicalRecordRows = imw_fetch_array($getSurgicalRecordQry);
		$surgical_id = $getSurgicalRecordRows['surgical_id'];
		$patient_id = $getSurgicalRecordRows['patient_id'];
		$form_id = $getSurgicalRecordRows['form_id'];
		$vis_mr_od_s = $getSurgicalRecordRows['mrSOD'];
		$vis_mr_od_c = $getSurgicalRecordRows['mrCOD'];
		$vis_mr_od_a = $getSurgicalRecordRows['mrAOD'];
		$visionOD = $getSurgicalRecordRows['visionOD'];
		$glareOD = $getSurgicalRecordRows['glareOD'];
		$performedByOD = $getSurgicalRecordRows['performedByOD'];
			$performedByODFname = getData('fname', 'users', 'id', $performedByOD);
			$performedByODLname = getData('lname', 'users', 'id', $performedByOD);
			$performedByOD = $performedByODFname." ".$performedByODLname;

		$dateOD = $getSurgicalRecordRows['dateOD'];
			list($dateODYear, $dateODMonth, $dateODDay) = split("-", $dateOD);
			$dateOD = $dateODMonth."-".$dateODDay."-".$dateODYear;
			
		$autoSelectOD = $getSurgicalRecordRows['autoSelectOD'];
			$autoSelectOD = $cpr->getKHeadingName($autoSelectOD);
		$iolMasterSelectOD = $getSurgicalRecordRows['iolMasterSelectOD'];
			$iolMasterSelectOD = $cpr->getKHeadingName($iolMasterSelectOD);
		$topographerSelectOD = $getSurgicalRecordRows['topographerSelectOD'];
			$topographerSelectOD = $cpr->getKHeadingName($topographerSelectOD);
		$vis_ak_od_k = $getSurgicalRecordRows['k1Auto1OD'];
		$vis_ak_od_x = $getSurgicalRecordRows['k1Auto2OD'];
		$k1IolMaster1OD = $getSurgicalRecordRows['k1IolMaster1OD'];
		$k1IolMaster2OD = $getSurgicalRecordRows['k1IolMaster2OD'];
		$k1Topographer1OD = $getSurgicalRecordRows['k1Topographer1OD'];
		$k1Topographer2OD = $getSurgicalRecordRows['k1Topographer2OD'];
		$vis_ak_od_slash = $getSurgicalRecordRows['k2Auto1OD'];
		$k2Auto2OD = $getSurgicalRecordRows['k2Auto2OD'];
		$k2IolMaster1OD = $getSurgicalRecordRows['k2IolMaster1OD'];
		$k2IolMaster2OD = $getSurgicalRecordRows['k2IolMaster2OD'];
		$k2Topographer1OD = $getSurgicalRecordRows['k2Topographer1OD'];
		$k2Topographer2OD = $getSurgicalRecordRows['k2Topographer2OD'];
		$cylAuto1OD = $getSurgicalRecordRows['cylAuto1OD'];
		$cylAuto2OD = $getSurgicalRecordRows['cylAuto2OD'];
		$cylIolMaster1OD = $getSurgicalRecordRows['cylIolMaster1OD'];
		$cylIolMaster2OD = $getSurgicalRecordRows['cylIolMaster2OD'];
		$cylTopographer1OD = $getSurgicalRecordRows['cylTopographer1OD'];
		$cylTopographer2OD = $getSurgicalRecordRows['cylTopographer2OD'];
		$aveOD1 = $getSurgicalRecordRows['aveAutoOD'];
		$aveIolMasterOD = $getSurgicalRecordRows['aveIolMasterOD'];
		$aveTopographerOD = $getSurgicalRecordRows['aveTopographerOD'];
		$contactLengthOD = $getSurgicalRecordRows['contactLengthOD'];
		$immersionLengthOD = $getSurgicalRecordRows['immersionLengthOD'];
		$iolMasterLengthOD = $getSurgicalRecordRows['iolMasterLengthOD'];
		$contactNotesOD = $getSurgicalRecordRows['contactNotesOD'];
		$immersionNotesOD = $getSurgicalRecordRows['immersionNotesOD'];
		$iolMasterNotesOD = $getSurgicalRecordRows['iolMasterNotesOD'];
		$provider_idOD = $getSurgicalRecordRows['performedByPhyOD'];
				$performedByODFname = getData('fname', 'users', 'id', $provider_idOD);
				$performedByODLname = getData('lname', 'users', 'id', $provider_idOD);
				$provider_idOD = $performedByODFname." ".$performedByODLname;
			
		$powerIolOD = $getSurgicalRecordRows['powerIolOD'];
			$powerIolOD = $cpr->getFormulaHeadName($powerIolOD);
		$holladayOD = $getSurgicalRecordRows['holladayOD'];
			$holladayOD = $cpr->getFormulaHeadName($holladayOD);
		$srk_tOD = $getSurgicalRecordRows['srk_tOD'];
			$srk_tOD = $cpr->getFormulaHeadName($srk_tOD);
		$hofferOD = $getSurgicalRecordRows['hofferOD'];
			$hofferOD = $cpr->getFormulaHeadName($hofferOD);
		$iol1OD = $getSurgicalRecordRows['iol1OD'];
			$iol1OD = $cpr->getLenseName($iol1OD);
		$iol1PowerOD = $getSurgicalRecordRows['iol1PowerOD'];
		$iol1HolladayOD = $getSurgicalRecordRows['iol1HolladayOD'];
		$iol1srk_tOD = $getSurgicalRecordRows['iol1srk_tOD'];
		$iol1HofferOD = $getSurgicalRecordRows['iol1HofferOD'];
		$iol2OD = $getSurgicalRecordRows['iol2OD'];
			$iol2OD = $cpr->getLenseName($iol2OD);
		$iol2PowerOD = $getSurgicalRecordRows['iol2PowerOD'];
		$iol2HolladayOD = $getSurgicalRecordRows['iol2HolladayOD'];
		$iol2srk_tOD = $getSurgicalRecordRows['iol2srk_tOD'];
		$iol2HofferOD = $getSurgicalRecordRows['iol2HofferOD'];
		$iol3OD = $getSurgicalRecordRows['iol3OD'];
			$iol3OD = $cpr->getLenseName($iol3OD);
		$iol3PowerOD = $getSurgicalRecordRows['iol3PowerOD'];
		$iol3HolladayOD = $getSurgicalRecordRows['iol3HolladayOD'];
		$iol3srk_tOD = $getSurgicalRecordRows['iol3srk_tOD'];
		$iol3HofferOD = $getSurgicalRecordRows['iol3HofferOD'];
		$iol4OD = $getSurgicalRecordRows['iol4OD'];
			$iol4OD = $cpr->getLenseName($iol4OD);
		$iol4PowerOD = $getSurgicalRecordRows['iol4PowerOD'];
		$iol4HolladayOD = $getSurgicalRecordRows['iol4HolladayOD'];
		$iol4srk_tOD = $getSurgicalRecordRows['iol4srk_tOD'];
		$iol4HofferOD = $getSurgicalRecordRows['iol4HofferOD'];
		$cellCountOD = $getSurgicalRecordRows['cellCountOD'];
		$notesOD = $getSurgicalRecordRows['notesOD'];
		$pachymetryValOD = $getSurgicalRecordRows['pachymetryValOD'];
		$pachymetryCorrecOD = $getSurgicalRecordRows['pachymetryCorrecOD'];
		$cornealDiamOD = $getSurgicalRecordRows['cornealDiamOD'];
		$dominantEyeOD = $getSurgicalRecordRows['dominantEyeOD'];
		$pupilSize1OD = $getSurgicalRecordRows['pupilSize1OD'];
		$pupilSize2OD = $getSurgicalRecordRows['pupilSize2OD'];
		$cataractOD = $getSurgicalRecordRows['cataractOD'];
		$astigmatismOD = $getSurgicalRecordRows['astigmatismOD'];
		$myopiaOD = $getSurgicalRecordRows['myopiaOD'];
		$selecedIOLsOD = $getSurgicalRecordRows['selecedIOLsOD'];
			$selecedIOLsOD = $cpr->getLenseName($selecedIOLsOD);
		$notesAssesmentPlansOD = $getSurgicalRecordRows['notesAssesmentPlansOD'];
		$lriOD = $getSurgicalRecordRows['lriOD'];
		$dlOD = $getSurgicalRecordRows['dlOD'];
		$synechiolysisOD = $getSurgicalRecordRows['synechiolysisOD'];
		$irishooksOD = $getSurgicalRecordRows['irishooksOD'];
		$trypanblueOD = $getSurgicalRecordRows['trypanblueOD'];
		$flomaxOD = $getSurgicalRecordRows['flomaxOD'];
		$cutsOD = $getSurgicalRecordRows['cutsOD'];
		$lengthOD = $getSurgicalRecordRows['lengthOD'];
		$lengthTypeOD = $getSurgicalRecordRows['lengthTypeOD'];
			if($lengthTypeOD == 'percent') $lengthTypeOD = '%';
		$axisOD = $getSurgicalRecordRows['axisOD'];
		$superiorOD = $getSurgicalRecordRows['superiorOD'];
		$inferiorOD = $getSurgicalRecordRows['inferiorOD'];
		$nasalOD = $getSurgicalRecordRows['nasalOD'];
		$temporalOD = $getSurgicalRecordRows['temporalOD'];
		$STOD = $getSurgicalRecordRows['STOD'];
		$SNOD = $getSurgicalRecordRows['SNOD'];
		$ITOD = $getSurgicalRecordRows['ITOD'];
		$INOD = $getSurgicalRecordRows['INOD'];
		$mrSOS = $getSurgicalRecordRows['mrSOS'];
		$mrCOS = $getSurgicalRecordRows['mrCOS'];
		$mrAOS = $getSurgicalRecordRows['mrAOS'];
		$visionOS = $getSurgicalRecordRows['visionOS'];
		$glareOS = $getSurgicalRecordRows['glareOS'];
		$provider_idOS = $getSurgicalRecordRows['performedByOS'];
				$performedByOSFname = getData('fname', 'users', 'id', $provider_idOS);
				$performedByOSLname = getData('lname', 'users', 'id', $provider_idOS);
				$performedByOS = $performedByOSFname." ".$performedByOSLname;
		
		$dateOS = $getSurgicalRecordRows['dateOS'];
			list($dateOSYear, $dateOSMonth, $dateOSDay) = split("-", $dateOS);
			$dateOS = $dateOSMonth."-".$dateOSDay."-".$dateOSYear;
		
		$autoSelectOS = $getSurgicalRecordRows['autoSelectOS'];
			$autoSelectOS = $cpr->getKHeadingName($autoSelectOS);
		$iolMasterSelectOS = $getSurgicalRecordRows['iolMasterSelectOS'];
			$iolMasterSelectOS = $cpr->getKHeadingName($iolMasterSelectOS);
		$topographerSelectOS = $getSurgicalRecordRows['topographerSelectOS'];
			$topographerSelectOS = $cpr->getKHeadingName($topographerSelectOS);
		$k1Auto1OS = $getSurgicalRecordRows['k1Auto1OS'];
		$vis_ak_os_k = $getSurgicalRecordRows['k1Auto1OS'];
		$vis_ak_os_slash = $getSurgicalRecordRows['k2Auto1OS'];
		$k1Auto2OS = $getSurgicalRecordRows['k1Auto2OS'];		
		$k1IolMaster1OS = $getSurgicalRecordRows['k1IolMaster1OS'];
		$k1IolMaster2OS = $getSurgicalRecordRows['k1IolMaster2OS'];
		$k1Topographer1OS = $getSurgicalRecordRows['k1Topographer1OS'];
		$k1Topographer2OS = $getSurgicalRecordRows['k1Topographer2OS'];
		$k2Auto1OS = $getSurgicalRecordRows['k2Auto1OS'];
		$k2Auto2OS = $getSurgicalRecordRows['k2Auto2OS'];
		$k2IolMaster1OS = $getSurgicalRecordRows['k2IolMaster1OS'];
		$k2IolMaster2OS = $getSurgicalRecordRows['k2IolMaster2OS'];
		$k2Topographer1OS = $getSurgicalRecordRows['k2Topographer1OS'];
		$k2Topographer2OS = $getSurgicalRecordRows['k2Topographer2OS'];
		
		$cylAuto1OS = $getSurgicalRecordRows['cylAuto1OS'];
		$cylAuto2OS = $getSurgicalRecordRows['cylAuto2OS'];		
		$cylIolMaster1OS = $getSurgicalRecordRows['cylIolMaster1OS'];
		$cylIolMaster2OS = $getSurgicalRecordRows['cylIolMaster2OS'];
		$cylTopographer1OS = $getSurgicalRecordRows['cylTopographer1OS'];
		$cylTopographer2OS = $getSurgicalRecordRows['cylTopographer2OS'];
		
		$aveAutoOS = $getSurgicalRecordRows['aveAutoOS'];
		$aveIolMasterOS = $getSurgicalRecordRows['aveIolMasterOS'];
		$aveTopographerOS = $getSurgicalRecordRows['aveTopographerOS'];
		$contactLengthOS = $getSurgicalRecordRows['contactLengthOS'];
		$immersionLengthOS = $getSurgicalRecordRows['immersionLengthOS'];
		$iolMasterLengthOS = $getSurgicalRecordRows['iolMasterLengthOS'];
		$contactNotesOS = $getSurgicalRecordRows['contactNotesOS'];
		$immersionNotesOS = $getSurgicalRecordRows['immersionNotesOS'];
		$iolMasterNotesOS = $getSurgicalRecordRows['iolMasterNotesOS'];
		$performedByPhyOS = $getSurgicalRecordRows['performedByPhyOS'];
		
		$performedIolOS = $getSurgicalRecordRows['performedIolOS'];		
				$performedByPhyOSFname = getData('fname', 'users', 'id', $performedIolOS);
				$performedByPhyOSLname = getData('lname', 'users', 'id', $performedIolOS);
				$performedIolOS = $performedByPhyOSFname." ".$performedByPhyOSLname;
		
		$powerIolOS = $getSurgicalRecordRows['powerIolOS'];
			$powerIolOS = $cpr->getFormulaHeadName($powerIolOS);
		$holladayOS = $getSurgicalRecordRows['holladayOS'];
			$holladayOS = $cpr->getFormulaHeadName($holladayOS);
		$srk_tOS = $getSurgicalRecordRows['srk_tOS'];
			$srk_tOS = $cpr->getFormulaHeadName($srk_tOS);
		$hofferOS = $getSurgicalRecordRows['hofferOS'];
			$hofferOS = $cpr->getFormulaHeadName($hofferOS);
		$iol1OS = $getSurgicalRecordRows['iol1OS'];
			$iol1OS = getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol1OS);			
		$iol1PowerOS = $getSurgicalRecordRows['iol1PowerOS'];
		$iol1HolladayOS = $getSurgicalRecordRows['iol1HolladayOS'];
		$iol1srk_tOS = $getSurgicalRecordRows['iol1srk_tOS'];
		$iol1HofferOS = $getSurgicalRecordRows['iol1HofferOS'];
		$iol2OS = $getSurgicalRecordRows['iol2OS'];
			$iol2OS = getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol2OS);
		$iol2PowerOS = $getSurgicalRecordRows['iol2PowerOS'];
		$iol2HolladayOS = $getSurgicalRecordRows['iol2HolladayOS'];
		$iol2srk_tOS = $getSurgicalRecordRows['iol2srk_tOS'];
		$iol2HofferOS = $getSurgicalRecordRows['iol2HofferOS'];
		$iol3OS = $getSurgicalRecordRows['iol3OS'];
			$iol3OS = getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol3OS);
		$iol3PowerOS = $getSurgicalRecordRows['iol3PowerOS'];
		$iol3HolladayOS = $getSurgicalRecordRows['iol3HolladayOS'];
		$iol3srk_tOS = $getSurgicalRecordRows['iol3srk_tOS'];
		$iol3HofferOS = $getSurgicalRecordRows['iol3HofferOS'];
		$iol4OS = $getSurgicalRecordRows['iol4OS'];
			$iol4OS = getData('lenses_iol_type', 'lenses_iol_type', 'iol_type_id', $iol4OS);
		$iol4PowerOS = $getSurgicalRecordRows['iol4PowerOS'];
		$iol4HolladayOS = $getSurgicalRecordRows['iol4HolladayOS'];
		$iol4srk_tOS = $getSurgicalRecordRows['iol4srk_tOS'];
		$iol4HofferOS = $getSurgicalRecordRows['iol4HofferOS'];
		$cellCountOS = $getSurgicalRecordRows['cellCountOS'];
		$notesOS = $getSurgicalRecordRows['notesOS'];
		$pachymetryValOS = $getSurgicalRecordRows['pachymetryValOS'];
		$pachymetryCorrecOS = $getSurgicalRecordRows['pachymetryCorrecOS'];
		$cornealDiamOS = $getSurgicalRecordRows['cornealDiamOS'];
		$dominantEyeOS = $getSurgicalRecordRows['dominantEyeOS'];
		$pupilSize1OS = $getSurgicalRecordRows['pupilSize1OS'];
		$pupilSize2OS = $getSurgicalRecordRows['pupilSize2OS'];
		$cataractOS = $getSurgicalRecordRows['cataractOS'];
		$astigmatismOS = $getSurgicalRecordRows['astigmatismOS'];
		$myopiaOS = $getSurgicalRecordRows['myopiaOS'];
		$selecedIOLsOS = $getSurgicalRecordRows['selecedIOLsOS'];
			$selecedIOLsOS = $cpr->getLenseName($selecedIOLsOS);
		$notesAssesmentPlansOS = $getSurgicalRecordRows['notesAssesmentPlansOS'];
		$lriOS = $getSurgicalRecordRows['lriOS'];
		$dlOS = $getSurgicalRecordRows['dlOS'];
		$synechiolysisOS = $getSurgicalRecordRows['synechiolysisOS'];
		$irishooksOS = $getSurgicalRecordRows['irishooksOS'];
		$trypanblueOS = $getSurgicalRecordRows['trypanblueOS'];
		$flomaxOS = $getSurgicalRecordRows['flomaxOS'];
		$cutsOS = $getSurgicalRecordRows['cutsOS'];
		$lengthOS = $getSurgicalRecordRows['lengthOS'];
		$lengthTypeOS = $getSurgicalRecordRows['lengthTypeOS'];		
			if($lengthTypeOS == 'percent') $lengthTypeOS = '%';
		$axisOS = $getSurgicalRecordRows['axisOS'];
		$superiorOS = $getSurgicalRecordRows['superiorOS'];
		$inferiorOS = $getSurgicalRecordRows['inferiorOS'];
		$nasalOS = $getSurgicalRecordRows['nasalOS'];
		$temporalOS = $getSurgicalRecordRows['temporalOS'];
		$STOS = $getSurgicalRecordRows['STOS'];
		$SNOS = $getSurgicalRecordRows['SNOS'];
		$ITOS = $getSurgicalRecordRows['ITOS'];
		$INOS = $getSurgicalRecordRows['INOS'];
		$signedById = $getSurgicalRecordRows['signedById'];		
		$signature = $getSurgicalRecordRows['signature'];
		$signedByOSId = $getSurgicalRecordRows['signedByOSId'];
		$signatureOS = $getSurgicalRecordRows['signatureOS'];
}
//================= GETTIN RECORD FOR PRINT
?>

<?php
if($rowsCount>0){
	?>
<style>
.text_b{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	background-color:#FFFFFF;
}
.text_10b{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	background-color:#FFFFFF;
}
.tb_heading{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#FFFFFF;
	background-color:#4684AB;
}
.text{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	background-color:#FFFFFF;
}
.text_9{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	background-color:#FFFFFF;
}
</style>
			<page backtop="30mm" backbottom="10mm">			
			<page_footer> 
				<table style="width: 100%;">
					<tr>
						<td style="text-align: center;	width: 100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
<?php 
			if($printAscan != 'printFromOutOfAScan'){
			?>
<page_header>
<table border="0" cellspacing="0" style="width: 100%; display:none;" cellpadding="0" >
<tr>
		<td style="text-align: center;	width: 100%" valign="top"> 
			<table style="width: 100%;" border="0" cellspacing="0" cellpadding="0">
			 <tr style="width: 100%;">
				<td width="19%" class="text_9" align="left">DOS:</td>
				<td width="23%" class="text_10b" align="left"><?php if($date_of_service){ print $date_of_service;} else '&nbsp;'; ?></td>
				<td style="width: 15%;" align="left">&nbsp;</td>				
				<td width="13%" align="left">ID :</td>
				<td width="27%" align="left"><?php print $pt_data['id']; ?></td>
				<td style="width: 15%;" align="left">&nbsp;</td>						
				<td width="18%" align="center" rowspan="4"><?php print $patientImage; ?></td>
			  </tr>
			<tr style="width: 100%;">
				<td width="19%" align="left" class="text_9">Practice&nbsp;Name:</td>
				<td width="23%" align="left" class="text_10b"><?php print $pt_data['groupDetails'][0]['name']; ?></td>
				<td style="width: 15%;" align="left">&nbsp;</td>				
				<td width="13%" align="left">Patient Name:</td>
				<td width="27%" align="left"><b><?php print $pt_data['title'].' '.$pt_data['patientName']; ?> (<?php print $pt_data['sex']; ?>)</b></td>
				<td style="width: 15%;" align="left">&nbsp;</td>				
				<td width="18%" class="text_9">&nbsp;</td>
		
			</tr>				
				<tr style="width: 100%;">
				<td valign="top" align="left" class="text_9">Address:</td>			
				<td align="left" valign="top" class="text_10b"><?php print ucwords($pt_data['groupDetails'][0]['group_Address1']); ?><?php print ucwords($pt_data['groupDetails'][0]['group_Address2']); ?><?php print $pt_data['groupDetails'][0]['group_City'].', '.$pt_data['groupDetails'][0]['group_State'].' '.$pt_data['groupDetails'][0]['group_Zip']; ?></td>	
				<td style="width: 15%;" align="left">&nbsp;</td>				
				<td align="left" >DOB:</td>
				<td align="left"><b><?php print $pt_data['date_of_birth']." (".$pt_data['age'].")"; ?></b></td>
				<td style="width: 15%;" align="left">&nbsp;</td>
				<td class="text_9">&nbsp;</td>
										
			  </tr>
				<tr style="width: 100%;">
						<td class="text_9" align="left">Ph.</td>
						<td align="left" class="text_9"><b># <?php print $pt_data['groupDetails'][0]['group_Telephone']; ?></b>&nbsp;Fax<b> # <?php print $pt_data['groupDetails'][0]['group_Fax']; ?></b></td>
						<td style="width: 15%;" align="left">&nbsp;</td>				
						<td valign="top" align="left" class="text_9">Address:</td>			
						<td align="left" valign="top" class="text_10b"><?php print $pt_data['street']."&nbsp;". $pt_data['street2']."&nbsp;".$pt_data['city']."&nbsp;".$pt_data['state']."&nbsp;".$pt_data['postal_code']; ?></td>						
			  </tr>
		</table>
	</td>
	
</tr>
</table>
</page_header>
			<?php
		}else{
		?>
<page_header>
<table width="100%" cellpadding="0" cellspacing="0" align="left" border="0" style="display:none;">
			<tr height="25">
				<td align="left" width="100%" class="text_10b" bgcolor="#CCCCCC" style="text-decoration:underline;"><b>A/Scan</b></td>
			</tr>
			</table>
</page_header>
		<?php
		}
		?>	
	<table cellpadding="0" cellspacing="0" align="left" border="0" style="display:none;width:100%;">
		
		<tr>
			<td align="center" style="width:100%;">
					
				<table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
					<tr height="50">
						<td align="center" class="text_10b" style="width:50%;">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="text_10b"><span style="color:#0000FF;">OD</span></td>
								
							</tr>
						</table>
							<table border="0" cellpadding="0" cellspacing="0">
								<tr height="">
									<td class="text_10b" align="">MR</td>
									<td class="text_10b" width="5">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b" align="left">S:&nbsp;</td>
											<td class="text_9" align="right"><?php if($vis_mr_od_s!='') echo $vis_mr_od_s; ?></td>
										</table>
									</td>
									<td width="10" valign="middle">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b">C:&nbsp;</td>
											<td class="text_9"><?php if($vis_mr_od_c!='') echo $vis_mr_od_c; ?></td>
										</table>
									</td>
									<td width="10" valign="middle">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b">A:&nbsp;</td>
											<td class="text_9"><?php if($vis_mr_od_a!='') echo $vis_mr_od_a; ?>&#176;</td>
										</table>
									</td>
									<td width="45" class="text_10b" align="right">Vision:</td>
									<td width="45" class="text_9"><?php if($visionOD!='') echo $visionOD; ?></td>
									<td width="10" class="text_10b" align="center">/</td>
									<td width="45" class="text_9" align="right"><?php if($glareOD!='') echo $glareOD; ?></td>
									<td width="15"></td>
								</tr>
								<tr class="text_9">
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td class="text" align="center">&nbsp;</td>
									<td width="45" class="text"  align="left" >Vision</td>
									<td width="10" class="text" align="center" >/</td>
									<td width="45" class="text" align="left" >Glare</td>
									<td class="text" align="center">&nbsp;</td>
							  </tr>
						  </table>
						</td>
						
						<td align="center" class="text_10b" style="width:50%;">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="text_10b"><span style="color:#009900;">OS</span></td>
							</tr>
						</table>
							<table border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td class="text_10b" align="">MR</td>
									<td class="text_10b" width="5">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b">S:&nbsp;</td>
											<td class="text_9"><?php if($mrSOS!='') echo $mrSOS; else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
										</table>
									</td>								
									<td width="10" valign="middle">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b">C:&nbsp;</td>
											<td class="text_9"><?php if($mrCOS!='') echo $mrCOS; else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
										</table>
									</td>
									<td width="10" valign="middle">&nbsp;</td>
									<td width="45" valign="middle">
										<table border="0" cellpadding="0" cellspacing="0">
											<td class="text_10b">A:&nbsp;</td>
											<td class="text_9"><?php if($mrAOS!='') echo $mrAOS; else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?>&#176;</td>
										</table>
									</td>
									<td width="10" valign="middle">&nbsp;</td>
									<td width="45" class="text_10b" align="">Vision:</td>
									<td width="45" class="text_9"><?php if($visionOS!='') echo $visionOS; else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
									<td width="10" class="text_10b" align="">/</td>
									<td width="45" class="text_9"><?php if($glareOS!='') echo $glareOS; else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
									<td class="text_10b" align=""></td>
								</tr>
								<tr>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="center">&nbsp;</td>
									<td class="text_9" align="left" >Vision</td>
									<td class="text_9" align="center" >/</td>
									<td class="text_9" align="left" >Glare</td>
									<td class="text" align="center">&nbsp;</td>
								</tr>
						  </table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top" style="width:45%;">
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td style="width:45%;" align="left" valign="top">
							<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="width:100%;">
									<tr height="20">
										<td align="left" class="text_10b" ><span style="color:#0000FF;font-size:<?php if($printAscan == 'printFromOutOfAScan') echo "10px"; else echo "14px"; ?>;font-weight:bold;">OD</span></td>
										<td align="left" class="text_10b">&nbsp;</td>
										<td align="left" class="text_10b">Performed By:</td>
										<td align="left" class="text_10b"><?php echo $performedByOD; ?></td>
										<td align="left" class="text_10b">Date:</td>
										<td align="left" class="text_9"><?php echo $dateOD; ?></td>
									</tr>
								</table>
								
									<table border="0" cellpadding="0" cellspacing="0">
										<tr height="10">
											<td colspan="6"></td>
										</tr>
										<tr>
											<td width="5"></td>
											<td align="left" class="text_10b"><?php echo $autoSelectOD; ?></td>
											<td width="5" align="left"></td>
											<td align="left" class="text_10b"><?php echo $iolMasterSelectOD; ?></td>
											<td width="5" align="left"></td>
											<td align="left" class="text_10b"><?php echo $topographerSelectOD; ?></td>
										</tr>
										<tr height="5">
											<td colspan="6"></td>
										</tr>
										<!-- K1 -->
										<tr height="20">
											<td width="5" class="text_10b">K1</td>
											<?php
											if($vis_ak_od_k){
												?>
												<td align="left">
													<table border="0" cellpadding="0" cellspacing="0">
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($vis_ak_od_k!='') && ($vis_ak_od_k!=0)) echo number_format($vis_ak_od_k, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($vis_ak_od_x!='') && ($vis_ak_od_x!=0)) echo $vis_ak_od_x; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
													</table>
												</td>
												<?php
											}
											?>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k1IolMaster1OD!='') && ($k1IolMaster1OD!=0)) echo number_format($k1IolMaster1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k1IolMaster2OD!='') && ($k1IolMaster2OD!=0)) echo $k1IolMaster2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k1Topographer1OD!='') && ($k1Topographer1OD!=0)) echo number_format($k1Topographer1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k1Topographer2OD!='') && ($k1Topographer2OD!=0)) echo $k1Topographer2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- K1 -->
										
										<!-- K2 -->
										<tr height="20">
											<td width="5" class="text_10b">K2</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($vis_ak_od_k){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($vis_ak_od_slash!='') && ($vis_ak_od_slash!=0)) echo number_format($vis_ak_od_slash, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2Auto2OD!='') && ($k2Auto2OD!=0)) echo $k2Auto2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k2IolMaster1OD!='') && ($k2IolMaster1OD!=0)) echo number_format($k2IolMaster1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2IolMaster2OD!='') && ($k2IolMaster2OD!=0)) echo $k2IolMaster2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k2Topographer1OD!='') && ($k2Topographer1OD!=0)) echo number_format($k2Topographer1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2Topographer2OD!='') && ($k2Topographer2OD!=0)) echo $k2Topographer2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- K2 -->
										
										<!-- CYL -->
										<tr height="20">
											<td width="5" class="text_10b">CYL</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($vis_ak_od_k){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylAuto1OD!='') && ($cylAuto1OD!=0)) echo number_format($cylAuto1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylAuto2OD!='') && ($cylAuto2OD!=0)) echo $cylAuto2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylIolMaster1OD!='') && ($cylIolMaster1OD!=0)) echo number_format($cylIolMaster1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylIolMaster2OD!='') && ($cylIolMaster2OD!=0)) echo $cylIolMaster2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OD){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylTopographer1OD!='') && ($cylTopographer1OD!=0)) echo number_format($cylTopographer1OD, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylTopographer2OD!='') && ($cylTopographer2OD!=0)) echo $cylTopographer2OD; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- CYL -->
	
										<!-- AVE -->
										<tr height="20">
											<td width="5" class="text_10b">AVE</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($vis_ak_od_k){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveOD1!='') && ($aveOD1!=0))  echo number_format($aveOD1, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OD){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveIolMasterOD!='') && ($aveIolMasterOD!=0)) echo number_format($aveIolMasterOD, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OD){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveTopographerOD!='') && ($aveTopographerOD!=0)) echo number_format($aveTopographerOD, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- AVE -->
										
										<tr height="20">
											<td colspan="6" align="left">
												<table border="0" cellpadding="0" cellspacing="1">
													<tr height="10">
														<td colspan="4"></td>
													</tr>
													<tr height="">
														<td align="left" class="text_10b">Axial</td>
														<td align="left" class="text_10b" width="86" style="padding-left:10px;">Contact</td>
														<td align="left" class="text_10b" width="86" style="padding-left:2px;">Immersion</td>
														<td align="left" class="text_10b" width="86" style="padding-left:2px;">IOL Master</td>
													</tr>
													<tr height="">
														<td align="left" class="text_10b">Length</td>
														<td align="left" class="text_9" style="padding-left:10px;"><?php echo $contactLengthOD; ?></td>
														<td align="left" class="text_9" style="padding-left:2px;"><?php echo $immersionLengthOD; ?></td>
														<td align="left" class="text_9" style="padding-left:2px;"><?php echo $iolMasterLengthOD; ?></td>
													</tr>
													<tr height="20">
														<?php
														if(($contactNotesOD) || ($immersionNotesOD) || ($iolMasterNotesOD)){
															?>
															<td align="left" class="text_10b">Notes</td>
															<td align="left" class="text_9" style="padding-left:2px;"><?php echo nl2br($contactNotesOD); ?></td>
															<td align="left" class="text_9" style="padding-left:2px;"><?php echo nl2br($immersionNotesOD); ?></td>
															<td align="left" class="text_9" style="padding-left:2px;"><?php echo nl2br($iolMasterNotesOD); ?></td>
															<?php
														}
														?>
													</tr>
													<tr height="2">
														<td colspan="4"></td>
													</tr>
											  </table>
											</td>
										</tr>									
								  </table>
								
						</td>
						<td style="width: 12%;">&nbsp;</td>
						<td style="width: 50%; padding-left:5px;" align="left" valign="top">
							<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="width:100%;">
									<tr height="20">
										<td align="left" class="text_10b" ><span style="color:#009900;font-size:<?php if($printAscan == 'printFromOutOfAScan') echo "10px"; else echo "14px"; ?>;font-weight:bold;">OS</span></td>
										<td align="left" class="text_10b">&nbsp;</td>
										<td align="left" class="text_10b">Performed By:</td>
										<td align="left" class="text_10b"><?php echo $performedByOS; ?></td>
										<td align="left" class="text_10b">Date:</td>
										<td align="left" class="text_9"><?php echo $dateOS; ?></td>
									</tr>
								</table>
								
									<table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
										<tr height="10">
											<td colspan="6"></td>
										</tr>
										<tr>
											<td width="5"></td>
											<td align="left" class="text_10b"><?php echo $autoSelectOS; ?></td>
											<td width="5" align="left"></td>
											<td align="left" class="text_10b"><?php echo $iolMasterSelectOS; ?></td>
											<td width="5" align="left"></td>
											<td align="left" class="text_10b"><?php echo $topographerSelectOS; ?></td>
										</tr>
										<tr height="5">
											<td colspan="6"></td>
										</tr>
										<!-- K1 -->
										<tr height="20">
											<td width="5" class="text_10b">K1</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Auto1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k1Auto1OS!='') && ($k1Auto1OS!=0)) echo number_format($k1Auto1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k1Auto2OS!='') && ($k1Auto2OS!=0)) echo $k1Auto2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k1IolMaster1OS!='') && ($k1IolMaster1OS!=0)) echo number_format($k1IolMaster1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k1IolMaster2OS!='') && ($k1IolMaster2OS!=0)) echo $k1IolMaster2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k1Topographer1OS!='') && ($k1Topographer1OS!=0)) echo number_format($k1Topographer1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k1Topographer2OS!='') && ($k1Topographer2OS!=0)) echo $k1Topographer2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- K1 -->
	
										<!-- K2 -->
										<tr height="20">
											<td width="5" class="text_10b">K2</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Auto1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($vis_ak_os_slash!='') && ($vis_ak_os_slash!=0)) echo number_format($vis_ak_os_slash, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2Auto2OS!='') && ($k2Auto2OS!=0)) echo $k2Auto2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k2IolMaster1OS!='') && ($k2IolMaster1OS!=0)) echo number_format($k2IolMaster1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2IolMaster2OS!='') && ($k2IolMaster2OS!=0)) echo $k2IolMaster2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php 
													if($k1Topographer1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($k2Topographer1OS!='') && ($k2Topographer1OS!=0)) echo number_format($k2Topographer1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9" width="10">X</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($k2Topographer2OS!='') && ($k2Topographer2OS!=0)) echo $k2Topographer2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- K2 -->
										
										<!-- CYL -->
										<tr height="20">
											<td width="5" class="text_10b">CYL</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Auto1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylAuto1OS!='') && ($cylAuto1OS!=0)) echo number_format($cylAuto1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylAuto2OS!='') && ($cylAuto2OS!=0)) echo $cylAuto2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylIolMaster1OS!='') && ($cylIolMaster1OS!=0)) echo number_format($cylIolMaster1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylIolMaster2OS!='') && ($cylIolMaster2OS!=0)) echo $cylIolMaster2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OS){
														?>
														<tr>
															<td align="left" class="text_9" width="35"><?php if(($cylTopographer1OS!='') && ($cylTopographer1OS!=0)) echo number_format($cylTopographer1OS, 2); else echo "&nbsp;"; ?></td>
															<td align="center" class="text_9">@</td>
															<td align="left" class="text_9" width="35" style="padding-left:1px;"><?php if(($cylTopographer2OS!='') && ($cylTopographer2OS!=0)) echo $cylTopographer2OS; else echo "&nbsp;"; ?>&#176;</td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- CYL -->
										
										<!-- AVE -->
										<tr height="20">
											<td width="5" class="text_10b">AVE</td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Auto1OS){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveAutoOS!='') && ($aveAutoOS!=0)) echo number_format($aveAutoOS, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1IolMaster1OS){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveIolMasterOS!='') && ($aveIolMasterOS!=0)) echo number_format($aveIolMasterOS, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
											<td width="5"></td>
											<td align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<?php
													if($k1Topographer1OS){
														?>
														<tr>
															<td align="center" class="text_9"><?php if(($aveTopographerOS!='') && ($aveTopographerOS!=0)) echo number_format($aveTopographerOS, 2); else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?></td>
														</tr>
														<?php
													}
													?>
												</table>
											</td>
										</tr>
										<!-- AVE -->
										<tr height="20">
											<td colspan="6" align="left">
												<table border="0" cellpadding="0" cellspacing="1" width="100%">
													<tr height="10">
														<td colspan="4"></td>
													</tr>
													<tr height="">
														<td align="left" class="text_10b">Axial</td>
														<td align="left" class="text_10b" width="86" style="padding-left:10px;">Contact</td>
														<td align="left" class="text_10b" width="86" style="padding-left:2px;">Immersion</td>
														<td align="left" class="text_10b" width="86" style="padding-left:2px;">IOL Master</td>
													</tr>
													<tr height="">
														<td align="left" class="text_10b">Length</td>
														<td align="left" class="text_9" style="padding-left:10px;"><?php echo $contactLengthOS; ?></td>
														<td align="left" class="text_9" style="padding-left:2px;"><?php echo $immersionLengthOS; ?></td>
														<td align="left" class="text_9" style="padding-left:2px;"><?php echo $iolMasterLengthOS; ?></td>
													</tr>
													<tr height="20">
														<?php
														if(($contactNotesOS) || ($immersionNotesOS) || ($iolMasterNotesOS)){
															?>
															<td align="left" class="text_10b">Notes</td>
															<td align="left" class="text_9" style="padding-left:10px;"><?php echo nl2br($contactNotesOS); ?></td>
															<td align="left" class="text_9" style="padding-left:2px;"><?php echo nl2br($immersionNotesOS); ?></td>
															<td align="left" class="text_9" style="padding-left:2px;"><?php echo nl2br($iolMasterNotesOS); ?></td>
															<?php
														}
														?>
													</tr>
													<tr height="2">
														<td colspan="4"></td>
													</tr>
											  </table>
											</td>
										</tr>									
								  </table>
								
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr height="10">
			<td align="left"></td>
		</tr>
		<tr>
			<td align="left" valign="top" style="width: 50%;">
				<table border="0" cellpadding="0" cellspacing="5" bordercolor="#FFFFFF" style="width: 100%;">
					<tr>
						<td align="center" valign="top">
							<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="width:100%;">
									<tr height="20">
										<td align="left" class="text_10b" ><span class="text_10b" style="color:#0000FF;font-size:<?php if($printAscan == 'printFromOutOfAScan') echo "10px"; else echo "14px"; ?>;">OD</span></td>
										<td align="left" class="text_10b">&nbsp;</td>
										<td align="left" class="text_10b">Performed By:</td>
										<td align="left" class="text_10b"><?php echo $provider_idOD; ?></td>
										
									</tr>
						  </table>
								
								<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="padding-left:5px;">
									<tr height="20">
										<td align="left" class="text_10b" width="90">IOL</td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($powerIolOD!='') echo $powerIolOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($holladayOD!='') echo $holladayOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($srk_tOD!='') echo $srk_tOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($hofferOD!='') echo $hofferOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="90"><?php if($iol1OD!='') echo $iol1OD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1PowerOD!='') echo $iol1PowerOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1HolladayOD!='') echo $iol1HolladayOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1srk_tOD!='') echo $iol1srk_tOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1HofferOD!='') echo $iol1HofferOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="90"><?php if($iol2OD!='') echo $iol2OD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2PowerOD!='') echo $iol2PowerOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2HolladayOD!='') echo $iol2HolladayOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2srk_tOD!='') echo $iol2srk_tOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2HofferOD!='') echo $iol2HofferOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="90"><?php if($iol3OD!='') echo $iol3OD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3PowerOD!='') echo $iol3PowerOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3HolladayOD!='') echo $iol3HolladayOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3srk_tOD!='') echo $iol3srk_tOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3HofferOD!='') echo $iol3HofferOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="90"><?php if($iol4OD!='') echo $iol4OD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4PowerOD!='') echo $iol4PowerOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4HolladayOD!='') echo $iol4HolladayOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4srk_tOD!='') echo $iol4srk_tOD; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4HofferOD!='') echo $iol4HofferOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="90">Cell Count</td>
										<td align="left" class="text_9"><?php if($cellCountOD!='') echo $cellCountOD; else echo "&nbsp;"; ?></td>
										<?php
										if($notesOD){
											?>
											<td colspan="3" rowspan="6" align="left" valign="top" class="text_9"><b>NOTES:&nbsp;</b><?php echo nl2br($notesOD); ?></td>
											<?php
										}
										?>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="90">Pachymetry</td>
										<td align="left" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="30" align="left" class="text_9"><?php if($pachymetryValOD!='') echo $pachymetryValOD; else echo "&nbsp;"; ?></td>
													<td width="10" align="center" class="text_9">/</td>
													<td align="center" class="text_9"><?php if($pachymetryCorrecOD!='') echo $pachymetryCorrecOD; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="90">Corneal Diam</td>
										<td align="left" class="text_9"><?php if($cornealDiamOD!='') echo $cornealDiamOD; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="90">Dominant Eye</td>
										<td align="left" class="text_9"><?php if($dominantEyeOD!='') echo $dominantEyeOD; else echo "&nbsp;"; ?></td>
									</tr>

									<tr height="20">
										<td align="left" class="text_10b" width="90">Pupil Size</td>
										<td align="left" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td align="left" width="20" class="text_9"><?php if($pupilSize1OD!='') echo $pupilSize1OD; else echo "&nbsp;"; ?></td>
													<td width="20" class="text_9" align="center">/</td>
													<td align="center" class="text_9"><?php if($pupilSize2OD!='') echo $pupilSize2OD; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="2" align="right" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td align="left" class="text_10b" >Un-Dilated</td>
													<td width="3" >/</td>
													<td align="left" class="text_10b" >Dilated</td>
												</tr>
										  </table>
										</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" class="text_9">&nbsp;</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" ><b>CC:</b>&nbsp;&nbsp;Scheduled for Intraocular lens implant<br/> A/Scan reviewed and IOL Selected.</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<tr>
										<td colspan="5" align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td align="left" class="text_10b" >Assessment:</td>
													<td ><?php if($cataractOD==1) echo "Cataract"; ?></td>
													<td > <?php if($astigmatismOD==1) echo "Astigmatism"; ?> </td>
													<td > <?php if($myopiaOD==1) echo "Myopia"; ?> </td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<tr>
										<td colspan="5" valign="top" align="left" >
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td  align="left" class="text_10b">Plan:</td>
													<td  style="padding-left:5px;"><?php if($selecedIOLsOD) echo $selecedIOLsOD; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<tr height="15">
										<td colspan="5" align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<td align="left"  valign="top" class="text_10b">Notes:</td>
												<td align="left"  style="padding-left:2px;"><?php if($notesOD!='') echo nl2br($notesOD); else echo "Notes..."; ?></td>
										  </table>
										</td>
									</tr>
									<tr height="20">
										<td colspan="5">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td > <?php if($lriOD==1){ echo "LRI"; } ?> </td>
													<td > <?php if($dlOD==1){ echo "DL"; } ?> </td>
													<td > <?php if($synechiolysisOD==1){ echo "Synechiolysis"; } ?> </td>
												</tr>
												<tr>
													<td > <?php if($irishooksOD==1){ echo "IRIS Hooks"; } ?> </td>
													<td ><?php if($trypanblueOD==1){ echo "Trypan Blue"; } ?></td>
													<td > <?php if($flomaxOD==1){ echo "Pt. On Flomax"; } ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="15" <?php if($lriOD!=1){ ?> style="display:none;" <?php } ?>>
										<td colspan="5" align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="35"  align="left" class="text_10b">Cuts:</td>
													<td width="25" align="left" ><?php echo $cutsOD; ?></td>
													<td width="10"></td>
													<td width="35" align="right" class="text_10b" >Length:</td>
													<td width="35" align="center" ><?php echo $lengthOD."  ".$lengthTypeOD; ?></td>
													<td width="15"></td>
													<td width="35" align="center" class="text_10b" >Axis:</td>
													<td width="35" align="center" ><?php echo $axisOD; ?></td>
												</tr>
										  </table>
										</td>
									</tr>
									<tr height="20" <?php if($cutsOD!=1){ ?> style="display:none;" <?php } ?>>
									  <td colspan="5" align="left">
											<table width="" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td ><?php if($superiorOD==1) echo "superior"; ?> </td>
													<td > <?php if($inferiorOD==1) echo "inferior"; ?> </td>
													<td > <?php if($nasalOD==1) echo "nasal"; ?></td>
													<td > <?php if($temporalOD==1) echo "temporal"; ?> </td>
													<td > <?php if($STOD==1) echo "ST"; ?> </td>
													<td > <?php if($SNOD==1) echo "SN"; ?> </td>
												</tr>
												<tr>												
													<td > <?php if($ITOD==1) echo "IT"; ?> </td>
													<td > <?php if($INOD==1) echo "IN"; ?> </td>
												</tr>
										  </table>
									  </td>
									</tr>
						  </table>
								
						</td>
						<td style="width: 12%;">&nbsp;</td>
						<td align="center" valign="top" class="text_9">
							<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="width:100%;">
									<tr height="20">
										<td align="left" class="text_10b" ><span style="color:#009900;font-size:<?php if($printAscan == 'printFromOutOfAScan') echo "10px"; else echo "14px"; ?>;"><b>OS</b></span></td>
										<td align="left" class="text_10b">&nbsp;</td>
										<td align="left" class="text_10b">Performed By:</td>
										<td align="left" class="text_10b"><?php echo $performedIolOS; ?></td>
										
									</tr>
						  </table>
								
								<table border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" style="padding-left:5px;">
									<tr height="20">
										<td align="left" class="text_10b" width="104">IOL</td>
										<td width="75" align="left" class="text_9" style="padding-left:1px;"><?php if($powerIolOS!='') echo $powerIolOS; else echo "&nbsp;"; ?></td>
										<td width="36" align="left" class="text_9" style="padding-left:1px;"><?php if($holladayOS!='') echo $holladayOS; else echo "&nbsp;"; ?></td>
										<td width="35" align="left" class="text_9" style="padding-left:1px;"><?php if($srk_tOS!='') echo $srk_tOS; else echo "&nbsp;"; ?></td>
										<td width="52" align="left" class="text_9" style="padding-left:1px;"><?php if($hofferOS!='') echo $hofferOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="104"><?php if($iol1OS!='') echo $iol1OS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1PowerOS!='') echo $iol1PowerOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1HolladayOS!='') echo $iol1HolladayOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1srk_tOS!='') echo $iol1srk_tOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol1HofferOS!='') echo $iol1HofferOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="104"><?php if($iol2OS!='') echo $iol2OS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2PowerOS!='') echo $iol2PowerOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2HolladayOS!='') echo $iol2HolladayOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2srk_tOS!='') echo $iol2srk_tOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol2HofferOS!='') echo $iol2HofferOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="104"><?php if($iol3OS!='') echo $iol3OS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3PowerOS!='') echo $iol3PowerOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3HolladayOS!='') echo $iol3HolladayOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3srk_tOS!='') echo $iol3srk_tOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol3HofferOS!='') echo $iol3HofferOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_9" width="104"><?php if($iol4OS!='') echo $iol4OS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4PowerOS!='') echo $iol4PowerOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4HolladayOS!='') echo $iol4HolladayOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4srk_tOS!='') echo $iol4srk_tOS; else echo "&nbsp;"; ?></td>
										<td align="left" style="padding-left:1px;" class="text_9"><?php if($iol4HofferOS!='') echo $iol4HofferOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="104">Cell Count</td>
										<td align="left" class="text_9"><?php if($cellCountOS!='') echo $cellCountOS; else echo "&nbsp;"; ?></td>
										<?php
										if($notesOS){
											?>
											<td colspan="3" width="75" rowspan="6" align="left" valign="top" class="text_9"><b>NOTES:&nbsp;</b><?php echo nl2br($notesOS); ?></td>
											<?php
										}
										?>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="104">Pachymetry</td>
										<td align="left" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="30" align="left" class="text_9"><?php if($pachymetryValOS!='') echo $pachymetryValOS; else echo "&nbsp;"; ?></td>
													<td width="10" class="text_9" align="center">/</td>
													<td align="left" class="text_9"><?php if($pachymetryCorrecOS!='') echo $pachymetryCorrecOS; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="104">Corneal Diam</td>
										<td align="left" class="text_9"><?php if($cornealDiamOS!='') echo $cornealDiamOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="104">Dominant Eye</td>
										<td align="left" class="text_9"><?php if($dominantEyeOS!='') echo $dominantEyeOS; else echo "&nbsp;"; ?></td>
									</tr>
									<tr height="20">
										<td align="left" class="text_10b" width="104">Pupil Size</td>
										<td align="left" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="30" align="left" class="text_9"><?php if($pupilSize1OS!='') echo $pupilSize1OS; else echo "&nbsp;"; ?></td>
													<td width="10" class="text_9" align="center">/</td>
													<td width="" align="left" class="text_9"><?php if($pupilSize2OS!='') echo $pupilSize2OS; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="2" align="right" class="text_9">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td align="left" class="text_10b" >Un-Dilated</td>
													<td width="3" >/</td>
													<td align="left" class="text_10b" >Dilated</td>
												</tr>
										  </table>
										</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" class="text_9">&nbsp;</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" ><b>CC:</b>&nbsp;&nbsp;Scheduled for Intraocular lens implant<br/> A/Scan reviewed and IOL Selected.</td>
									</tr>
									<tr height="20">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<tr>
										<td colspan="5" align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td align="left" class="text_10b" >Assessment:</td>
													<td > <?php if($cataractOS==1) echo "Cataract"; ?> </td>
													<td ><?php if($astigmatismOS==1) echo "Astigmatism"; ?> </td>
													<td > <?php if($myopiaOS==1) echo "Myopia"; ?> </td>
												</tr>
										  </table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<tr>
										<td colspan="5" valign="top" align="left" >
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td  align="left" class="text_10b">Plan:</td>
													<td  style="padding-left:5px;"><?php if($selecedIOLsOS) echo $selecedIOLsOS; else echo "&nbsp;"; ?></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="5">
										<td colspan="5" valign="top" align="left" >&nbsp;</td>
									</tr>
									<?php 
										//if(($notesAssesmentPlansOS!='Notes...') && ($notesAssesmentPlansOS!='')){
										?>
										<tr height="15">
											<td colspan="5" align="left">
												<table border="0" cellpadding="0" cellspacing="0">
													<td align="left"  valign="top"><b>Notes:</b></td>
													<td align="left"  style="padding-left:2px;"><?php if($notesAssesmentPlansOS!='') echo nl2br($notesAssesmentPlansOS); else echo "Notes..."; ?></td>
												</table>
											</td>
										</tr>
										<?php
									//}
									?>
									<tr height="20">
										<td colspan="5">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td ><?php if($lriOS==1){ echo "LRI"; } ?> </td>
													<td > <?php if($dlOS==1){ echo "DL"; } ?> </td>
													<td > <?php if($synechiolysisOS==1){ echo "Synechiolysis"; } ?> </td>
												</tr>
												<tr>
													<td > <?php if($irishooksOS==1){ echo "IRIS Hooks"; } ?> </td>
													<td > <?php if($trypanblueOS==1){ echo "Trypan Blue"; } ?> </td>
													<td ><?php if($flomaxOS==1){ echo "Pt. On Flomax"; } ?> </td>
												</tr>
											</table>
										</td>
									</tr>
									<tr height="15" <?php if($lriOS!=1){ ?> style="display:none;" <?php } ?>>
										<td colspan="5" align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="35"  align="left" class="text_10b">Cuts:</td>
													<td width="25" align="left" class="text_9" ><?php echo $cutsOS; ?></td>
													<td width="10"></td>
													<td width="35" align="right" class="text_10b" >Length:</td>
													<td width="35" align="center" class="text_9" ><?php echo $lengthOS."  ".$lengthTypeOS; ?></td>
													<td width="15"></td>
													<td width="35" align="center" class="text_10b" >Axis:</td>
													<td width="35" align="center" class="text_9" ><?php echo $axisOS; ?></td>
												</tr>
										  </table>
										</td>
									</tr>
									<tr height="20" <?php if($cutsOS!=1){ ?> style="display:none;" <?php } ?>>
									  <td colspan="5" align="left">
											<table width="" border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td ><?php if($superiorOS==1) echo "superior"; ?> </td>
													<td ><?php if($inferiorOS==1) echo "inferior"; ?> </td>
													<td ><?php if($nasalOS==1) echo "nasal"; ?></td>
													<td ><?php if($temporalOS==1) echo "temporal"; ?></td>
													<td ><?php if($STOS==1) echo "ST"; ?></td>
													<td ><?php if($SNOS==1) echo "SN"; ?> </td>
												</tr>
												<tr>												
													<td ><?php if($ITOS==1) echo "IT"; ?></td>
													<td ><?php if($INOS==1) echo "IN"; ?> </td>
												</tr>
										  </table>
									  </td>
									</tr>
						  </table>
								
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="left" >
				<table border="0" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" width="100%">
					<tr>
						<td width="60" align="left" class="text_10b" >Signature:</td>
						<td width="275" align="left" >
							
							<?php

								if($cpr->isAppletModified($signature)){
									$table = 'surgical_tbl';
									$idName = 'surgical_id';
									$docSign = 'signature';
									$signImage = '../../../library/images/white.jpg';
									$alt = 'Physician Sign'; 
												 
									 if($cpr->getAppletImage($surgical_id,$table,$idName,$docSign,$signImage,$alt,"1")){
										@copy("html2pdfprint/".$gdFilename,"../common/new_html2pdf/".$gdFilename);
										echo "<img src='".$gdFilename."' height='45' width='225'/>";
										$ChartNoteImagesString[]=$gdFilename;					 
									} 
									
								}
							?>	
						</td>
						<td width="75" align="left" >&nbsp;</td>
						<td width="60" align="left" class="text_10b" >Signature:</td>
						<td width="250" align="left" >
							
							<?php
								if($cpr->isAppletModified($signature)){

									$table = 'surgical_tbl';
									$idName = 'surgical_id';
									$docSign = 'signatureOS';
									$signImage = '../../../library/images/white.jpg';
									$alt = 'Physician Sign'; 
									if($cpr->getAppletImage($surgical_id,$table,$idName,$docSign,$signImage,$alt,"1")){
										@copy("html2pdfprint/".$gdFilename,"../common/new_html2pdf/".$gdFilename);										
										echo "<img src='".$gdFilename."' height='45' width='225'/>";
										$ChartNoteImagesString[]=$gdFilename;					 
									} 
									
								}
							?>	
						</td>
					</tr>
			  </table>
			</td>
		</tr>
	</table>
	<?php
	///Add Ascan Images//
	$imagesHtml=$cpr->getTestImages($surgical_id,$sectionImageFrom="Ascan",$patient_id);
	if($imagesHtml!=""){
		echo("<table >							 
				".$imagesHtml." 
			</table>");
	} 
	$imagesHtml="";
	//End Ascan Images
	?>				
</page>
	<?php
	
}
$headDataALL = ob_get_contents();

if(trim($headDataALL) != ""){
	$file_location = '';
	$file_location = write_html($headDataALL);
	if(trim($file_location) != ''){
	 	if(isset($_REQUEST['txtFaxNo']) && trim($_REQUEST['txtFaxNo'])!=''){
			echo '<script type="text/javascript">window.location="sendfax_chart_summary.php?faxRecipent='.trim($_REQUEST['txtFaxRecipent']).'&txtFaxNo='.trim($_REQUEST['txtFaxNo']).'&pdfOp=l";</script>';
			exit;
		}
		
		if(isset($_REQUEST['txtEmailId']) && trim($_REQUEST['txtEmailId'])!=''){
			echo '<script type="text/javascript">window.location="send_email_chart_summary.php?txtEmailId='.trim($_REQUEST['txtEmailId']).'&pdfOp=l&txtEmailName='.trim($_REQUEST['txtEmailName']).'";</script>';
			exit;
		}
	
	?>
	<html>
		<body>
				<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
				<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
				<script type="text/javascript">
					var file_name = '<?php print $print_file_name; ?>';
					top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
					html_to_pdf('<?php echo $file_location; ?>','l',file_name);
				</script>
		</body>
	</html>
	<?php
	}
}else{
?>
<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.css" rel="stylesheet" type="text/css">
	<div class="col-sm-12" style="padding-top:10px">
		<div class="alert alert-info text-center ">
		  <strong>No Result </strong>
		</div>
	</div>
<?php
}
?>
