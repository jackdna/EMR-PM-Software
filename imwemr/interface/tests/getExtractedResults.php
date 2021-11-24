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
/*
File: getExtractedResults.php
Purpose: This file provides exam summary. Now not in use.
Access Type : Include file
*/
?>
<?php
include_once($GLOBALS['srcdir']."/classes/work_view/ExamXml.php");
$oExamXml = new ExamXml();
// GET C:D Values
	$sql = "SELECT * FROM chart_optic WHERE form_id = '$form_id'";
	$res = imw_query($sql);
	if(imw_num_rows($res)>0){
		$extractData = extract(imw_fetch_array($res));
	}
// GET C:D Values
// CHART NOTE VALUES
	$sql = "SELECT * FROM chart_optic WHERE form_id = '$form_id' ";
	$res = imw_query($sql);
	while($arrayResult = imw_fetch_array($res)){
		$optic_nerve_od = $arrayResult['optic_nerve_od'];
		$optic_nerve_od = stripslashes($optic_nerve_od);
		
		$optic_nerve_os = $arrayResult['optic_nerve_os'];
		$optic_nerve_os = stripslashes($optic_nerve_os);
	}		
	
	$arrOpticNerveOd = $oExamXml->extractXmlValue($optic_nerve_od);
	extract($arrOpticNerveOd);
	
	$arrOpticNerveOs = $oExamXml->extractXmlValue($optic_nerve_os);
	extract($arrOpticNerveOs);
	
// CHART NOTE VALUES

// CHART NOTE VALUES
	$sql = "SELECT macula_od, macula_os,
				retinal_od as retina_od, retinal_os as retina_os,
				vitreous_od, vitreous_os,
				blood_vessels_od, blood_vessels_os,
				periphery_od, periphery_os
			FROM chart_macula c1
			left join chart_periphery c2 ON c2.form_id = c1.form_id AND c2.purged='0'
			left join chart_vitreous c3 ON c3.form_id = c1.form_id AND c3.purged='0'
			left join chart_retinal_exam c4 ON c4.form_id = c1.form_id AND c4.purged='0'
			left join chart_blood_vessels c5 ON c5.form_id = c1.form_id	AND c5.purged='0'		
			WHERE c1.form_id = '$form_id' AND c1.purged='0' ";
	$res = imw_query($sql);
	while($arrayResult2 = imw_fetch_array($res)){
		//$optic_nerveOd = $arrayResult2['optic_nerve_od'];
		$maculaOd = $arrayResult2['macula_od'];		
		$retinaOd = $arrayResult2['retina_od'];		
		$vitreousOd = $arrayResult2['vitreous_od'];
		$bloodVesselsOd = $arrayResult2['blood_vessels_od'];
		$peripheryOd = $arrayResult2['periphery_od'];

		//$optic_nerveOd = stripslashes($optic_nerveOd);
		$maculaOd = stripslashes($maculaOd);
		$retinaOd = stripslashes($retinaOd);
		$vitreousOd = stripslashes($vitreousOd);
		$bloodVesselsOd = stripslashes($bloodVesselsOd);
		$peripheryOd = stripslashes($peripheryOd);

		//$optic_nerveOs = $arrayResult2['optic_nerve_os'];		
		$maculaOs = $arrayResult2['macula_os'];
		$retinaOs = $arrayResult2['retina_os'];
		$vitreousOs = $arrayResult2['vitreous_os'];
		$bloodVesselsOs = $arrayResult2['blood_vessels_os'];
		$peripheryOs = $arrayResult2['periphery_os'];
		
		//$optic_nerveOs = stripslashes($optic_nerveOs);
		$maculaOs = stripslashes($maculaOs);
		$retinaOs = stripslashes($retinaOs);
		$vitreousOs = stripslashes($vitreousOs);
		$bloodVesselsOs = stripslashes($bloodVesselsOs);
		$peripheryOs = stripslashes($peripheryOs);
	}
	
	
	//$x = $oExamXml->extractXmlValue($optic_nerveOd);
	//extract($x);	
	
	$x = $oExamXml->extractXmlValue($maculaOd);
	extract($x);
	
	$x = $oExamXml->extractXmlValue($maculaOs);
	extract($x);	
	
	$x = $oExamXml->extractXmlValue($retinaOd);
	extract($x);
	
	$x = $oExamXml->extractXmlValue($bloodVesselsOd);
	extract($x);
	
	$x = $oExamXml->extractXmlValue($peripheryOd);
	extract($x);	
	
	//$x = $oExamXml->extractXmlValue($optic_nerveOs);
	//extract($x);
	
	$x = $oExamXml->extractXmlValue($retinaOs);
	extract($x);
	
	$x = $oExamXml->extractXmlValue($vitreousOd);
	extract($x);
	
	$x = $oExamXml->extractXmlValue($vitreousOs);
	extract($x);
	
	$x = $oExamXml->extractXmlValue($bloodVesselsOs);
	extract($x);
	
	$x = $oExamXml->extractXmlValue($peripheryOs);
	extract($x);

// CHART NOTE VALUES


//Atrophic Change OD
if($elem_macOd_armd_atrCh_neg == "-ve"){
	$atrophicChangeOd = " -ve ";
}
if($elem_macOd_armd_atrCh_T == "T"){
	if($atrophicChangeOd) $atrophicChangeOd = $atrophicChangeOd.' T';
	else $atrophicChangeOd = ' T';
}
if($elem_macOd_armd_atrCh_pos1 == "+1"){
	if($atrophicChangeOd) $atrophicChangeOd = $atrophicChangeOd.' +1';
	else $atrophicChangeOd = ' +1';
}
if($elem_macOd_armd_atrCh_pos2 == "+2"){
	if($atrophicChangeOd) $atrophicChangeOd = $atrophicChangeOd.' +2';
	else $atrophicChangeOd = ' +2';
}
if($elem_macOd_armd_atrCh_pos3 == "+3"){
	if($atrophicChangeOd) $atrophicChangeOd = $atrophicChangeOd.' +3';
	else $atrophicChangeOd = ' +3';
}
if($elem_macOd_armd_atrCh_pos4 == "+4"){
	if($atrophicChangeOd) $atrophicChangeOd = $atrophicChangeOd.' +4';
	else $atrophicChangeOd = ' +4';
}
if($atrophicChangeOd){
	$atrophicChangeOd = $atrophicChangeOd.' Atrophic Change.';
}
//Atrophic Change OD

//Atrophic Change OS
if($elem_macOs_armd_atrCh_neg == "-ve"){
	$atrophicChangeOs = " -ve ";
}
if($elem_macOs_armd_atrCh_T == "T"){
	if($atrophicChangeOs) $atrophicChangeOd = $atrophicChangeOs.' T';
	else $atrophicChangeOs = ' T';
}
if($elem_macOs_armd_atrCh_pos1 == "+1"){
	if($atrophicChangeOs) $atrophicChangeOs = $atrophicChangeOs.' +1';
	else $atrophicChangeOs = ' +1';
}
if($elem_macOs_armd_atrCh_pos2 == "+2"){
	if($atrophicChangeOs) $atrophicChangeOs = $atrophicChangeOs.' +2';
	else $atrophicChangeOs = ' +2';
}
if($elem_macOs_armd_atrCh_pos3 == "+3"){
	if($atrophicChangeOs) $atrophicChangeOs = $atrophicChangeOs.' +3';
	else $atrophicChangeOs = ' +3';
}
if($elem_macOs_armd_atrCh_pos4 == "+4"){
	if($atrophicChangeOs) $atrophicChangeOs = $atrophicChangeOs.' +4';
	else $atrophicChangeOs = ' +4';
}
if($atrophicChangeOs){
	$atrophicChangeOs = $atrophicChangeOs.' Atrophic Change.';
}
//Atrophic Change OS


// Peripheral OD
if($elem_macOd_armd_Peripheral_Hard == "Hard"){
	$peripheralMaculaOd = ' Hard';
}
if($elem_macOd_armd_Peripheral_Soft == "Soft"){
	if($peripheralMaculaOd) $peripheralMaculaOd = $peripheralMaculaOd.' Soft';
	else $peripheralMaculaOd = ' Soft';
}
if($peripheralMaculaOd){
	$peripheralMaculaOd = $peripheralMaculaOd.' Peripheral.';
}
// Peripheral OD


// Peripheral OS
if($elem_macOs_armd_Peripheral_Hard == "Hard"){
	$peripheralMaculaOs = ' Hard';
}
if($elem_macOs_armd_Peripheral_Soft == "Soft"){
	if($peripheralMaculaOs) $peripheralMaculaOs = $peripheralMaculaOs.' Soft';
	else $peripheralMaculaOs = ' Soft';
}
if($peripheralMaculaOs){
	$peripheralMaculaOs = $peripheralMaculaOs.' Peripheral.';
}
// Peripheral OS


// Cotton Wool Spots OD
if($elem_macOd_dr_cws_neg == "-ve"){
	$cottonWoolSpotsOd = ' -ve';
}
if($elem_macOd_dr_cws_T == "T"){
	if($cottonWoolSpotsOd) $cottonWoolSpotsOd = $cottonWoolSpotsOd.' T';
	else $cottonWoolSpotsOd = ' T';
}
if($elem_macOd_dr_cws_pos1 == "+1"){
	if($cottonWoolSpotsOd) $cottonWoolSpotsOd = $cottonWoolSpotsOd.' +1';
	else $cottonWoolSpotsOd = ' +1';
}
if($elem_macOd_dr_cws_pos2 == "+2"){
	if($cottonWoolSpotsOd) $cottonWoolSpotsOd = $cottonWoolSpotsOd.' +2';
	else $cottonWoolSpotsOd = ' +2';
}
if($elem_macOd_dr_cws_pos3 == "+3"){
	if($cottonWoolSpotsOd) $cottonWoolSpotsOd = $cottonWoolSpotsOd.' +3';
	else $cottonWoolSpotsOd = ' +3';
}
if($elem_macOd_dr_cws_pos4 == "+4"){
	if($cottonWoolSpotsOd) $cottonWoolSpotsOd = $cottonWoolSpotsOd.' +4';
	else $cottonWoolSpotsOd = ' +4';
}
if($cottonWoolSpotsOd){
	$cottonWoolSpotsOd = $cottonWoolSpotsOd.' Cotton Wool Spots.';
}
// Cotton Wool Spots OD


// Cotton Wool Spots OS
if($elem_macOs_dr_cws_neg == "-ve"){
	$cottonWoolSpotsOs = ' -ve';
}
if($elem_macOs_dr_cws_T == "T"){
	if($cottonWoolSpotsOs) $cottonWoolSpotsOs = $cottonWoolSpotsOs.' T';
	else $cottonWoolSpotsOs = ' T';
}
if($elem_macOs_dr_cws_pos1 == "+1"){
	if($cottonWoolSpotsOs) $cottonWoolSpotsOs = $cottonWoolSpotsOs.' +1';
	else $cottonWoolSpotsOs = ' +1';
}
if($elem_macOs_dr_cws_pos2 == "+2"){
	if($cottonWoolSpotsOs) $cottonWoolSpotsOs = $cottonWoolSpotsOs.' +2';
	else $cottonWoolSpotsOs = ' +2';
}
if($elem_macOs_dr_cws_pos3 == "+3"){
	if($cottonWoolSpotsOs) $cottonWoolSpotsOs = $cottonWoolSpotsOs.' +3';
	else $cottonWoolSpotsOs = ' +3';
}
if($elem_macOs_dr_cws_pos4 == "+4"){
	if($cottonWoolSpotsOs) $cottonWoolSpotsOs = $cottonWoolSpotsOs.' +4';
	else $cottonWoolSpotsOs = ' +4';
}
if($cottonWoolSpotsOs){
	$cottonWoolSpotsOs = $cottonWoolSpotsOs.' Cotton Wool Spots.';
}
// Cotton Wool Spots OS

// CME OD
if($elem_macOd_cme_neg == "-ve"){
	$cmeOd = ' -ve';
}
if($elem_macOd_cme_T == "T"){
	if($cmeOd) $cmeOd = $cmeOd.' T';
	else $cmeOd = ' T';
}
if($elem_macOd_cme_pos1 == "+1"){
	if($cmeOd) $cmeOd = $cmeOd.' +1';
	else $cmeOd = ' +1';
}
if($elem_macOd_cme_pos2 == "+2"){
	if($cmeOd) $cmeOd = $cmeOd.' +2';
	else $cmeOd = ' +2';
}
if($elem_macOd_cme_pos3 == "+3"){
	if($cmeOd) $cmeOd = $cmeOd.' +3';
	else $cmeOd = ' +3';
}
if($elem_macOd_cme_pos4 == "+4"){
	if($cmeOd) $cmeOd = $cmeOd.' +4';
	else $cmeOd = ' +4';
}
if($cmeOd){
	$cmeOd = $cmeOd.' CME.';
}
// CME OD


// CME OS
if($elem_macOs_cme_neg == "-ve"){
	$cmeOs = ' -ve';
}
if($elem_macOs_cme_T == "T"){
	if($cmeOs) $cmeOs = $cmeOs.' T';
	else $cmeOs = ' T';
}
if($elem_macOs_cme_pos1 == "+1"){
	if($cmeOs) $cmeOs = $cmeOs.' +1';
	else $cmeOs = ' +1';
}
if($elem_macOs_cme_pos2 == "+2"){
	if($cmeOs) $cmeOs = $cmeOs.' +2';
	else $cmeOs = ' +2';
}
if($elem_macOs_cme_pos3 == "+3"){
	if($cmeOs) $cmeOs = $cmeOs.' +3';
	else $cmeOs = ' +3';
}
if($elem_macOs_cme_pos4 == "+4"){
	if($cmeOs) $cmeOs = $cmeOs.' +4';
	else $cmeOs = ' +4';
}
if($cmeOs){
	$cmeOs = $cmeOs.' CME.';
}
// CME OS

//ERM OD
if($elem_macOd_erm_neg == "-ve"){
	$ermOd = ' -ve';
}
if($elem_macOd_erm_T == "T"){
	if($ermOd) $ermOd = $ermOd.' T';
	else $ermOd = ' T';
}
if($elem_macOd_erm_pos1 == "+1"){
	if($ermOd) $ermOd = $ermOd.' +1';
	else $ermOd = ' +1';
}
if($elem_macOd_erm_pos2 == "+2"){
	if($ermOd) $ermOd = $ermOd.' +2';
	else $ermOd = ' +2';
}
if($elem_macOd_erm_pos3 == "+3"){
	if($ermOd) $ermOd = $ermOd.' +3';
	else $ermOd = ' +3';
}
if($elem_macOd_erm_pos4 == "+4"){
	if($ermOd) $ermOd = $ermOd.' +4';
	else $ermOd = ' +4';
}
if($ermOd){
	$ermOd = $ermOd.' ERM.';
}
//ERM OD


//ERM OS
if($elem_macOs_erm_neg == "-ve"){
	$ermOs = ' -ve';
}
if($elem_macOs_erm_T == "T"){
	if($ermOs) $ermOs = $ermOs.' T';
	else $ermOs = ' T';
}
if($elem_macOs_erm_pos1 == "+1"){
	if($ermOs) $ermOs = $ermOs.' +1';
	else $ermOs = ' +1';
}
if($elem_macOs_erm_pos2 == "+2"){
	if($ermOs) $ermOs = $ermOs.' +2';
	else $ermOs = ' +2';
}
if($elem_macOs_erm_pos3 == "+3"){
	if($ermOs) $ermOs = $ermOs.' +3';
	else $ermOs = ' +3';
}
if($elem_macOs_erm_pos4 == "+4"){
	if($ermOs) $ermOs = $ermOs.' +4';
	else $ermOs = ' +4';
}
if($ermOs){
	$ermOs = $ermOs.' ERM.';
}
//ERM OS

// Nevus OD
if($elem_macOd_nevus_sup == "Superior"){
	$nevusOd = ' Superior';
}
if($elem_macOd_nevus_inf == "Inferior"){
	if($nevusOd) $nevusOd = $nevusOd.' Inferior';
	else $nevusOd = ' Inferior';
}
if($elem_macOd_nevus_nasal == "Nasal"){
	if($nevusOd) $nevusOd = $nevusOd.' Nasal';
	else $nevusOd = ' Nasal';
}
if($elem_macOd_nevus_temp == "Temporal"){
	if($nevusOd) $nevusOd = $nevusOd.' Temporal';
	else $nevusOd = ' Temporal';
}
if($nevusOd){
	$nevusOd = $nevusOd.' Nevus.';
}
// Nevus OD


// Nevus OS
if($elem_macOs_nevus_sup == "Superior"){
	$nevusOs = ' Superior';
}
if($elem_macOs_nevus_inf == "Inferior"){
	if($nevusOs) $nevusOs = $nevusOs.' Inferior';
	else $nevusOs = ' Inferior';
}
if($elem_macOs_nevus_nasal == "Nasal"){
	if($nevusOs) $nevusOs = $nevusOs.' Nasal';
	else $nevusOs = ' Nasal';
}
if($elem_macOs_nevus_temp == "Temporal"){
	if($nevusOs) $nevusOs = $nevusOs.' Temporal';
	else $nevusOs = ' Temporal';
}
if($nevusOs){
	$nevusOs = $nevusOs.' Nevus.';
}
// Nevus OS

//Periphery
	// Hemorrhage OD
		if($elem_periOd_hemoHage_sup == "Superior"){
			$hemoHageOd = ' Superior';
		}
		if($elem_periOd_hemoHage_inf == "Inferior"){
			if($hemoHageOd) $hemoHageOd = $hemoHageOd.' Inferior';
			else $hemoHageOd = ' Inferior';
		}
		if($elem_periOd_hemoHage_nasal == "Nasal"){
			if($hemoHageOd) $hemoHageOd = $hemoHageOd.' Nasal';
			else $hemoHageOd = ' Nasal';
		}
		if($elem_periOd_hemoHage_temp == "Temporal"){
			if($hemoHageOd) $hemoHageOd = $hemoHageOd.' Temporal';
			else $hemoHageOd = ' Temporal';
		}
		if($hemoHageOd){
			$hemoHageOd = $hemoHageOd.' Hemorrhage.';
		}
	// Hemorrhage OD

	// Hemorrhage OS
		if($elem_periOs_hemoHage_sup == "Superior"){
			$hemoHageOs = ' Superior';
		}
		if($elem_periOs_hemoHage_inf == "Inferior"){
			if($hemoHageOs) $hemoHageOs = $hemoHageOs.' Inferior';
			else $hemoHageOs = ' Inferior';
		}
		if($elem_periOs_hemoHage_nasal == "Nasal"){
			if($hemoHageOs) $hemoHageOs = $hemoHageOs.' Nasal';
			else $hemoHageOs = ' Nasal';
		}
		if($elem_periOs_hemoHage_temp == "Temporal"){
			if($hemoHageOs) $hemoHageOs = $hemoHageOs.' Temporal';
			else $hemoHageOs = ' Temporal';
		}
		if($hemoHageOs){
			$hemoHageOs = $hemoHageOs.' Hemorrhage.';
		}
	// Hemorrhage OD
	
	//BDR OD
	if($elem_periOd_dr_bdr_neg == "-ve"){
		$bdrPeripheryOd = ' -ve';
	}
	if($elem_periOd_dr_bdr_T == "T"){
		if($bdrPeripheryOd) $bdrPeripheryOd = $bdrPeripheryOd.' T';
		else $bdrPeripheryOd = ' T';
	}
	if($elem_periOd_dr_bdr_pos1 == "+1"){
		if($bdrPeripheryOd) $bdrPeripheryOd = $bdrPeripheryOd.' +1';
		else $bdrPeripheryOd = ' +1';
	}
	if($elem_periOd_dr_bdr_pos2 == "+2"){
		if($bdrPeripheryOd) $bdrPeripheryOd = $bdrPeripheryOd.' +2';
		else $bdrPeripheryOd = ' +2';
	}
	if($elem_periOd_dr_bdr_pos3 == "+3"){
		if($bdrPeripheryOd) $bdrPeripheryOd = $bdrPeripheryOd.' +3';
		else $bdrPeripheryOd = ' +3';
	}
	if($elem_periOd_dr_bdr_pos4 == "+4"){
		if($bdrPeripheryOd) $bdrPeripheryOd = $bdrPeripheryOd.' +4';
		else $bdrPeripheryOd = ' +4';
	}
	if($bdrPeripheryOd){
		$bdrPeripheryOd = $bdrPeripheryOd.' BDR.';
	}
	//BDR OD

	//BDR OS
	if($elem_periOs_dr_bdr_neg == "-ve"){
		$bdrPeripheryOs = ' -ve';
	}
	if($elem_periOs_dr_bdr_T == "T"){
		if($bdrPeripheryOs) $bdrPeripheryOs = $bdrPeripheryOs.' T';
		else $bdrPeripheryOs = ' T';
	}
	if($elem_periOs_dr_bdr_pos1 == "+1"){
		if($bdrPeripheryOs) $bdrPeripheryOs = $bdrPeripheryOs.' +1';
		else $bdrPeripheryOs = ' +1';
	}
	if($elem_periOs_dr_bdr_pos2 == "+2"){
		if($bdrPeripheryOs) $bdrPeripheryOs = $bdrPeripheryOs.' +2';
		else $bdrPeripheryOs = ' +2';
	}
	if($elem_periOs_dr_bdr_pos3 == "+3"){
		if($bdrPeripheryOs) $bdrPeripheryOs = $bdrPeripheryOs.' +3';
		else $bdrPeripheryOs = ' +3';
	}
	if($elem_periOs_dr_bdr_pos4 == "+4"){
		if($bdrPeripheryOs) $bdrPeripheryOs = $bdrPeripheryOs.' +4';
		else $bdrPeripheryOs = ' +4';
	}
	if($bdrPeripheryOs){
		$bdrPeripheryOs = $bdrPeripheryOs.' BDR.';
	}
	//BDR OS
	
	// Exudate OD
	if($elem_periOd_dr_exu_neg == "-ve"){
		$ExudateOD = ' -ve';
	}
	if($elem_periOd_dr_exu_T == "T"){
		if($ExudateOD) $ExudateOD = $ExudateOD.' T';
		else $ExudateOD = ' T';
	}
	if($elem_periOd_dr_exu_pos1 == "+1"){
		if($ExudateOD) $ExudateOD = $ExudateOD.' +1';
		else $ExudateOD = ' +1';
	}
	if($elem_periOd_dr_exu_pos2 == "+2"){
		if($ExudateOD) $ExudateOD = $ExudateOD.' +2';
		else $ExudateOD = ' +2';
	}
	if($elem_periOd_dr_exu_pos3 == "+3"){
		if($ExudateOD) $ExudateOD = $ExudateOD.' +3';
		else $ExudateOD = ' +3';
	}
	if($elem_periOd_dr_exu_pos4 == "+4"){
		if($ExudateOD) $ExudateOD = $ExudateOD.' +4';
		else $ExudateOD = ' +4';
	}
	if($ExudateOD){
		$ExudateOD = $ExudateOD." Exudate.";
	}
	// Exudate OD

	// Exudate OS
	if($elem_periOs_dr_exu_neg == "-ve"){
		$ExudateOS = ' -ve';
	}
	if($elem_periOs_dr_exu_T == "T"){
		if($ExudateOS) $ExudateOS = $ExudateOS.' T';
		else $ExudateOs = ' T';
	}
	if($elem_periOs_dr_exu_pos1 == "+1"){
		if($ExudateOS) $ExudateOS = $ExudateOS.' +1';
		else $ExudateOS = ' +1';
	}
	if($elem_periOs_dr_exu_pos2 == "+2"){
		if($ExudateOS) $ExudateOS = $ExudateOS.' +2';
		else $ExudateOS	= ' +2';
	}
	if($elem_periOs_dr_exu_pos3 == "+3"){
		if($ExudateOS) $ExudateOS = $ExudateOS.' +3';
		else $ExudateOS = ' +3';
	}
	if($elem_periOs_dr_exu_pos4 == "+4"){
		if($ExudateOS) $ExudateOS = $ExudateOS.' +4';
		else $ExudateOS = ' +4';
	}
	if($ExudateOS){
		$ExudateOS = $ExudateOS." Exudate.";
	}
	// Exudate OS
	
	// Cotton Wool Spots OD
	if($elem_periOd_dr_cws_neg == "-ve"){
		$cWoolSpotsOd = ' -ve';
	}
	if($elem_periOd_dr_cws_T == "T"){
		if($cWoolSpotsOd) $cWoolSpotsOd = $cWoolSpotsOd.' T';
		else $cWoolSpotsOd = ' T';
	}
	if($elem_periOd_dr_cws_pos1 == "+1"){
		if($cWoolSpotsOd) $cWoolSpotsOd = $cWoolSpotsOd.' +1';
		else $cWoolSpotsOd = ' +1';
	}
	if($elem_periOd_dr_cws_pos2 == "+2"){
		if($cWoolSpotsOd) $cWoolSpotsOd = $cWoolSpotsOd.' +2';
		else $cWoolSpotsOd = ' +2';
	}
	if($elem_periOd_dr_cws_pos3 == "+3"){
		if($cWoolSpotsOd) $cWoolSpotsOd = $cWoolSpotsOd.' +3';
		else $cWoolSpotsOd = ' +3';
	}
	if($elem_periOd_dr_cws_pos4 == "+4"){
		if($cWoolSpotsOd) $cWoolSpotsOd = $cWoolSpotsOd.' +4';
		else $cWoolSpotsOd = ' +4';
	}	
	if($cWoolSpotsOd){
		$cWoolSpotsOd = $cWoolSpotsOd.' Cotton Wool Spots.';
	}
	// Cotton Wool Spots OD

	// Cotton Wool Spots OS
	if($elem_periOs_dr_cws_neg == "-ve"){
		$cWoolSpotsOs = ' -ve';
	}
	if($elem_periOs_dr_cws_T == "T"){
		if($cWoolSpotsOs) $cWoolSpotsOs = $cWoolSpotsOs.' T';
		else $cWoolSpotsOs = ' T';
	}
	if($elem_periOs_dr_cws_pos1 == "+1"){
		if($cWoolSpotsOs) $cWoolSpotsOs = $cWoolSpotsOs.' +1';
		else $cWoolSpotsOs = ' +1';
	}
	if($elem_periOs_dr_cws_pos2 == "+2"){
		if($cWoolSpotsOs) $cWoolSpotsOs = $cWoolSpotsOs.' +2';
		else $cWoolSpotsOs = ' +2';
	}
	if($elem_periOs_dr_cws_pos3 == "+3"){
		if($cWoolSpotsOs) $cWoolSpotsOs = $cWoolSpotsOs.' +3';
		else $cWoolSpotsOs = ' +3';
	}
	if($elem_periOs_dr_cws_pos4 == "+4"){
		if($cWoolSpotsOs) $cWoolSpotsOs = $cWoolSpotsOs.' +4';
		else $cWoolSpotsOs = ' +4';
	}	
	if($cWoolSpotsOs){
		$cWoolSpotsOs = $cWoolSpotsOs.' Cotton Wool Spots.';
	}
	// Cotton Wool Spots OD
	
	
	// PDR(Neovascularization) OD
	if($elem_periOd_dr_pdrNeo_neg == "-ve"){
		$PDROd = ' -ve';
	}
	if($elem_periOd_dr_pdrNeo_pos == "+ve"){
		if($PDROd) $PDROd = $PDROd.' +ve';
		else $PDROd = ' +ve';
	}
	if($PDROd){
		$PDROd = $PDROd.' PDR.';
	}
	// PDR(Neovascularization) OD

	// PDR(Neovascularization) OS
	if($elem_periOs_dr_pdrNeo_neg == "-ve"){
		$PDROs = ' -ve';
	}
	if($elem_periOs_dr_pdrNeo_pos == "+ve"){
		if($PDROs) $PDROs = $PDROs.' +ve';
		else $PDROs = ' +ve';
	}
	if($PDROs){
		$PDROs = $PDROs.' PDR.';
	}
	// PDR(Neovascularization) OS
	
	// Nevus Periphery OD
	if($elem_periOd_nevus_sup == "Superior"){
		$nevusPerOd = " Superior";
	}
	if($elem_periOd_nevus_inf == "Inferior"){
		if($nevusPerOd) $nevusPerOd = $nevusPerOd.' Inferior';
		else $nevusPerOd = ' Inferior';
	}
	if($elem_periOd_nevus_nasal == "Nasal"){
		if($nevusPerOd) $nevusPerOd = $nevusPerOd.' Nasal';
		else $nevusPerOd = ' Nasal';
	}
	if($elem_periOd_nevus_temp == "Temporal"){
		if($nevusPerOd) $nevusPerOd = $nevusPerOd.' Temporal';
		else $nevusPerOd = ' Temporal';
	}
	if($elem_periOd_nevus_flat == "Flat"){
		if($nevusPerOd) $nevusPerOd = $nevusPerOd.' Flat';
		else $nevusPerOd = ' Flat';
	}
	if($nevusPerOd){
		$nevusPerOd = ' Periphery'.$nevusPerOd.' Nevus';
	}
	// Nevus Periphery OD


	// Nevus Periphery Os
	if($elem_periOs_nevus_sup == "Superior"){
		$nevusPerOs = " Superior";
	}
	if($elem_periOs_nevus_inf == "Inferior"){
		if($nevusPerOs) $nevusPerOs = $nevusPerOs.' Inferior';
		else $nevusPerOs = ' Inferior';
	}
	if($elem_periOs_nevus_nasal == "Nasal"){
		if($nevusPerOs) $nevusPerOs = $nevusPerOs.' Nasal';
		else $nevusPerOs = ' Nasal';
	}
	if($elem_periOs_nevus_temp == "Temporal"){
		if($nevusPerOs) $nevusPerOs = $nevusPerOs.' Temporal';
		else $nevusPerOs = ' Temporal';
	}
	if($elem_periOs_nevus_flat == "Flat"){
		if($nevusPerOs) $nevusPerOs = $nevusPerOs.' Flat';
		else $nevusPerOs = ' Flat';
	}
	if($nevusPerOs){
		$nevusPerOs = ' Periphery'.$nevusPerOs.' Nevus';
	}
	// Nevus Periphery OS
	
	// SCAR OD
	if($elem_periOd_scar_sup == "Superior"){
		$scarPeripheryOd = ' Superior';
	}
	if($elem_periOd_scar_inf == "Inferior"){
		if($scarPeripheryOd) $scarPeripheryOd = $scarPeripheryOd." Inferior";
		else $scarPeripheryOd = ' Inferior';
	}
	if($elem_periOd_scar_nasal == "Nasal"){
		if($scarPeripheryOd) $scarPeripheryOd = $scarPeripheryOd." Nasal";
		else $scarPeripheryOd = ' Nasal';
	}
	if($elem_periOd_scar_temp == "Temporal"){
		if($scarPeripheryOd) $scarPeripheryOd = $scarPeripheryOd." Temporal";
		else $scarPeripheryOd = ' Temporal';
	}
	if($scarPeripheryOd){
		$scarPeripheryOd = ' Periphery'.$scarPeripheryOd.' Scar.';
	}
	// SCAR OD

	// SCAR OS
	if($elem_periOs_scar_sup == "Superior"){
		$scarPeripheryOs = ' Superior';
	}
	if($elem_periOs_scar_inf == "Inferior"){
		if($scarPeripheryOs) $scarPeripheryOs = $scarPeripheryOs." Inferior";
		else $scarPeripheryOs = ' Inferior';
	}
	if($elem_periOs_scar_nasal == "Nasal"){
		if($scarPeripheryOs) $scarPeripheryOs = $scarPeripheryOs." Nasal";
		else $scarPeripheryOs = ' Nasal';
	}
	if($elem_periOs_scar_temp == "Temporal"){
		if($scarPeripheryOs) $scarPeripheryOs = $scarPeripheryOs." Temporal";
		else $scarPeripheryOs = ' Temporal';
	}
	if($scarPeripheryOs){
		$scarPeripheryOs = ' Periphery'.$scarPeripheryOs.' Scar.';
	}
	// SCAR OS
	
	//Retinal Tear OD
		if($elem_periOd_retinalTear_neg == "-ve"){
			$retinalTearOd = ' -ve';
		}
		if($elem_periOd_retinalTear_pos == "+ve"){
			if($retinalTearOd) $retinalTearOd = $retinalTearOd.' +ve';
			else $retinalTearOd = ' +ve';
		}
		if($retinalTearOd){
			$retinalTearOd = $retinalTearOd.' Retinal Tear.';
		}
	// Retinal Tear OD


	// Retinal Tear OS
		if($elem_periOs_retinalTear_neg == "-ve"){
			$retinalTearOs = ' -ve';
		}
		if($elem_periOs_retinalTear_pos == "+ve"){
			if($retinalTearOs) $retinalTearOs = $retinalTearOs.' +ve';
			else $retinalTearOs = ' +ve';
		}
		if($retinalTearOs){
			$retinalTearOs = $retinalTearOs.' Retinal Tear.';
		}
	// Retinal Tear OS

	// Retinal Detachment OD
	if($elem_periOd_retinalDetach_neg == "-ve"){
		$retinalDetachOd = ' -ve';
	}
	if($elem_periOd_retinalDetach_pos == "+ve"){
		if($retinalDetachOd) $retinalDetachOd = $retinalDetachOd.' +ve';
		else $retinalDetachOd = ' +ve';
	}
	if($retinalDetachOd) $retinalDetachOd = $retinalDetachOd.' Retinal Detach.';
	// Retinal Detachment OD

	// Retinal Detachment OS
	if($elem_periOs_retinalDetach_neg == "-ve"){
		$retinalDetachOs = ' -ve';
	}
	if($elem_periOs_retinalDetach_pos == "+ve"){
		if($retinalDetachOs) $retinalDetachOs = $retinalDetachOs.' +ve';
		else $retinalDetachOs = ' +ve';
	}
	if($retinalDetachOs) $retinalDetachOs = $retinalDetachOs.' Retinal Detach.';
	// Retinal Detachment OS
	
	// Vitreous Cells OD
	if($elem_periOd_vitCells_neg == "-ve"){
		$vitCellsOd = ' -ve';
	}
	if($elem_periOd_vitCells_pos == "+ve"){
		if($vitCellsOd) $vitCellsOd = $vitCellsOd.' +ve';
		else $vitCellsOd = ' +ve';
	}
	if($elem_periOd_vitCells_T == "T"){
		if($vitCellsOd) $vitCellsOd = $vitCellsOd.' T';
		else $vitCellsOd = ' T';
	}
	if($elem_periOd_vitCells_pos1 == "+1"){
		if($vitCellsOd) $vitCellsOd = $vitCellsOd.' +1';
		else $vitCellsOd = ' +1';
	}
	if($elem_periOd_vitCells_pos2 == "+2"){
		if($vitCellsOd) $vitCellsOd = $vitCellsOd.' +2';
		else $vitCellsOd = ' +2';
	}
	if($elem_periOd_vitCells_pos3 == "+3"){
		if($vitCellsOd) $vitCellsOd = $vitCellsOd.' +3';
		else $vitCellsOd = ' +3';
	}
	if($elem_periOd_vitCells_pos4 == "+4"){
		if($vitCellsOd) $vitCellsOd = $vitCellsOd.' +4';
		else $vitCellsOd = ' +4';
	}
	if($vitCellsOd) $vitCellsOd = $vitCellsOd.' Vitreous Cells.';
	// Vitreous Cells OD


	// Vitreous Cells OS
	if($elem_periOs_vitCells_neg == "-ve"){
		$vitCellsOs = ' -ve';
	}
	if($elem_periOs_vitCells_pos == "+ve"){
		if($vitCellsOs) $vitCellsOs = $vitCellsOs.' +ve';
		else $vitCellsOs = ' +ve';
	}
	if($elem_periOs_vitCells_T == "T"){
		if($vitCellsOs) $vitCellsOs = $vitCellsOs.' T';
		else $vitCellsOs = ' T';
	}
	if($elem_periOs_vitCells_pos1 == "+1"){
		if($vitCellsOs) $vitCellsOs = $vitCellsOs.' +1';
		else $vitCellsOs = ' +1';
	}
	if($elem_periOs_vitCells_pos2 == "+2"){
		if($vitCellsOs) $vitCellsOd = $vitCellsOs.' +2';
		else $vitCellsOs = ' +2';
	}
	if($elem_periOs_vitCells_pos3 == "+3"){
		if($vitCellsOs) $vitCellsOd = $vitCellsOs.' +3';
		else $vitCellsOs = ' +3';
	}
	if($elem_periOs_vitCells_pos4 == "+4"){
		if($vitCellsOs) $vitCellsOs = $vitCellsOs.' +4';
		else $vitCellsOs = ' +4';
	}
	if($vitCellsOs) $vitCellsOs = $vitCellsOs.' Vitreous Cells.';
	// Vitreous Cells OS
	
	//Atrophic-Changes OD
		if($elem_periOd_periDegen_atroCh_sup == "Superior"){
			$atrophicChangesOd = " Superior";
		}
		if($elem_periOd_periDegen_atroCh_inf == "Inferior"){
			if($atrophicChangesOd) $atrophicChangesOd = $atrophicChangesOd." Inferior";
			else $atrophicChangesOd = ' Inferior';
		}
		if($elem_periOd_periDegen_atroCh_nasal == "Nasal"){
			if($atrophicChangesOd) $atrophicChangesOd = $atrophicChangesOd." Nasal";
			else $atrophicChangesOd = ' Nasal';
		}
		if($elem_periOd_periDegen_atroCh_temp == "Temporal"){
			if($atrophicChangesOd) $atrophicChangesOd = $atrophicChangesOd." Temporal";
			else $atrophicChangesOd = ' Temporal';
		}
		if($atrophicChangesOd){
			$atrophicChangesOd = $atrophicChangesOd.' Atrophic-Changes.';
		}
	//Atrophic-Changes OD


	//Atrophic-Changes OS
		if($elem_periOs_periDegen_atroCh_sup == "Superior"){
			$atrophicChangesOs = " Superior";
		}
		if($elem_periOs_periDegen_atroCh_inf == "Inferior"){
			if($atrophicChangesOs) $atrophicChangesOs = $atrophicChangesOs." Inferior";
			else $atrophicChangesOs = ' Inferior';
		}
		if($elem_periOs_periDegen_atroCh_nasal == "Nasal"){
			if($atrophicChangesOs) $atrophicChangesOs = $atrophicChangesOs." Nasal";
			else $atrophicChangesOs = ' Nasal';
		}
		if($elem_periOs_periDegen_atroCh_temp == "Temporal"){
			if($atrophicChangesOs) $atrophicChangesOs = $atrophicChangesOs." Temporal";
			else $atrophicChangesOs = ' Temporal';
		}
		if($atrophicChangesOs){
			$atrophicChangesOs = $atrophicChangesOs.' Atrophic-Changes.';
		}
	//Atrophic-Changes OS
	
	//DRUSEN OD
	if($elem_periOd_periDegen_drusen_sup == "Superior"){
		$periDegenDrusenOd = ' Superior';
	}
	if($elem_periOd_periDegen_drusen_inf == "Inferior"){
		if($periDegenDrusenOd) $periDegenDrusenOd = $periDegenDrusenOd.' Inferior';
		else $periDegenDrusenOd = ' Inferior';
	}
	if($elem_periOd_periDegen_drusen_nasal == "Nasal"){
		if($periDegenDrusenOd) $periDegenDrusenOd = $periDegenDrusenOd.' Nasal';
		else $periDegenDrusenOd = ' Nasal';
	}
	if($elem_periOd_periDegen_drusen_temp == "Temporal"){
		if($periDegenDrusenOd) $periDegenDrusenOd = $periDegenDrusenOd.' Temporal';
		else $periDegenDrusenOd = ' Temporal';
	}
	if($periDegenDrusenOd) $periDegenDrusenOd = $periDegenDrusenOd.' Drusen.';
	//DRUSEN OD

	//DRUSEN OS
	if($elem_periOs_periDegen_drusen_sup == "Superior"){
		$periDegenDrusenOs = ' Superior';
	}
	if($elem_periOs_periDegen_drusen_inf == "Inferior"){
		if($periDegenDrusenOs) $periDegenDrusenOs = $periDegenDrusenOs.' Inferior';
		else $periDegenDrusenOs = ' Inferior';
	}
	if($elem_periOs_periDegen_drusen_nasal == "Nasal"){
		if($periDegenDrusenOs) $periDegenDrusenOs = $periDegenDrusenOs.' Nasal';
		else $periDegenDrusenOs = ' Nasal';
	}
	if($elem_periOs_periDegen_drusen_temp == "Temporal"){
		if($periDegenDrusenOs) $periDegenDrusenOs = $periDegenDrusenOs.' Temporal';
		else $periDegenDrusenOs = ' Temporal';
	}
	if($periDegenDrusenOs) $periDegenDrusenOs = $periDegenDrusenOs.' Drusen.';
	//DRUSEN OS
	
	//lattice OD
		if($elem_periOd_periDegen_lattice_sup == "Superior"){
			$latticeOd = ' Superior';
		}
		if($elem_periOd_periDegen_lattice_inf == "Inferior"){
			if($latticeOd) $latticeOd = $latticeOd.' Inferior';
			else $latticeOd = ' Inferior';
		}
		if($elem_periOd_periDegen_lattice_nasal == "Nasal"){
			if($latticeOd) $latticeOd = $latticeOd.' Nasal';
			else $latticeOd = ' Nasal';
		}
		if($elem_periOd_periDegen_lattice_temp == "Temporal"){
			if($latticeOd) $latticeOd = $latticeOd.' Temporal';
			else $latticeOd = ' Temporal';
		}
		if($latticeOd) $latticeOd = $latticeOd.' Lattice.';
	//lattice OD


	//lattice OS
		if($elem_periOs_periDegen_lattice_sup == "Superior"){
			$latticeOs = ' Superior';
		}
		if($elem_periOs_periDegen_lattice_inf == "Inferior"){
			if($latticeOs) $latticeOs = $latticeOs.' Inferior';
			else $latticeOs = ' Inferior';
		}
		if($elem_periOs_periDegen_lattice_nasal == "Nasal"){
			if($latticeOs) $latticeOs = $latticeOs.' Nasal';
			else $latticeOs = ' Nasal';
		}
		if($elem_periOs_periDegen_lattice_temp == "Temporal"){
			if($latticeOs) $latticeOs = $latticeOs.' Temporal';
			else $latticeOs = ' Temporal';
		}
		if($latticeOs) $latticeOs = $latticeOs.' Lattice.';
	//lattice OS
//Periphery


//Drusen OD
if($elem_macOd_armd_drusen_neg == "-ve"){
	$maculaDescOd = " -ve";
}
if($elem_macOd_armd_drusen_T == "T"){
	if($maculaDescOd) $maculaDescOd = $maculaDescOd.' T';
	else $maculaDescOd = " T";
}
if($elem_macOd_armd_drusen_pos1 == "+1"){
	if($maculaDescOd) $maculaDescOd = $maculaDescOd.' +1';
	else $maculaDescOd = " +1";
}
if($elem_macOd_armd_drusen_pos2 == "+2"){
	if($maculaDescOd) $maculaDescOd = $maculaDescOd.' +2';
	else $maculaDescOd = " +2";
}
if($elem_macOd_armd_drusen_pos3 == "+3"){
	if($maculaDescOd) $maculaDescOd = $maculaDescOd.' +3';
	else $maculaDescOd = " +3";
}
if($elem_macOd_armd_drusen_pos4 == "+4"){
	if($maculaDescOd) $maculaDescOd = $maculaDescOd.' +4';
	else $maculaDescOd = " +4";
}
if($elem_macOd_armd_drusen_foveal == "Perifovial"){
	if($maculaDescOd) $maculaDescOd = $maculaDescOd.' Perifovial';
	else $maculaDescOd = " Perifovial";
}
if($elem_macOd_armd_drusen_hard == "Hard"){
	if($maculaDescOd) $maculaDescOd = $maculaDescOd.' Hard';
	else $maculaDescOd = " Hard";
}
if($elem_macOd_armd_drusen_soft == "Soft"){
	if($maculaDescOd) $maculaDescOd = $maculaDescOd.' Soft';
	else $maculaDescOd = " Soft";
}
if($maculaDescOd){
	$maculaDescOd = $maculaDescOd.' Drusen.';
}
//Drusen OD


//Drusen OS
if($elem_macOs_armd_drusen_neg == "-ve"){
	$maculaDescOs = " -ve";
}
if($elem_macOs_armd_drusen_T == "T"){
	if($maculaDescOs) $maculaDescOs = $maculaDescOs.' T';
	else $maculaDescOs = " T";
}
if($elem_macOs_armd_drusen_pos1 == "+1"){
	if($maculaDescOs) $maculaDescOs = $maculaDescOs.' +1';
	else $maculaDescOs = " +1";
}
if($elem_macOs_armd_drusen_pos2 == "+2"){
	if($maculaDescOs) $maculaDescOs = $maculaDescOs.' +2';
	else $maculaDescOs = " +2";
}
if($elem_macOs_armd_drusen_pos3 == "+3"){
	if($maculaDescOs) $maculaDescOs = $maculaDescOs.' +3';
	else $maculaDescOs = " +3";
}
if($elem_macOs_armd_drusen_pos4 == "+4"){
	if($maculaDescOs) $maculaDescOs = $maculaDescOs.' +4';
	else $maculaDescOs = " +4";
}
if($elem_macOs_armd_drusen_foveal == "Perifovial"){
	if($maculaDescOs) $maculaDescOs = $maculaDescOs.' Perifovial';
	else $maculaDescOs = " Perifovial";
}
if($elem_macOs_armd_drusen_hard == "Hard"){
	if($maculaDescOs) $maculaDescOs = $maculaDescOs.' Hard';
	else $maculaDescOs = " Hard";
}
if($elem_macOs_armd_drusen_soft == "Soft"){
	if($maculaDescOs) $maculaDescOs = $maculaDescOs.' Soft';
	else $maculaDescOs = " Soft";
}
if($maculaDescOs){
	$maculaDescOs = $maculaDescOs.' Drusen.';
}
//Drusen OS

//RPE CHARGES OD
if($elem_macOd_armd_rpeCh_neg == "-ve"){
	$rpeChMaculaOd = ' -ve';
}
if($elem_macOd_armd_rpeCh_T == "T"){
	if($rpeChMaculaOd) $rpeChMaculaOd = $rpeChMaculaOd.' T';
	else $rpeChMaculaOd = ' T';
}
if($elem_macOd_armd_rpeCh_pos1 == "+1"){
	if($rpeChMaculaOd) $rpeChMaculaOd = $rpeChMaculaOd.' +1';
	else $rpeChMaculaOd = ' +1';
}
if($elem_macOd_armd_rpeCh_pos2 == "+2"){
	if($rpeChMaculaOd) $rpeChMaculaOd = $rpeChMaculaOd.' +2';
	else $rpeChMaculaOd = ' +2';
}
if($elem_macOd_armd_rpeCh_pos3 == "+3"){
	if($rpeChMaculaOd) $rpeChMaculaOd = $rpeChMaculaOd.' +3';
	else $rpeChMaculaOd = ' +3';
}
if($elem_macOd_armd_rpeCh_pos4 == "+4"){
	if($rpeChMaculaOd) $rpeChMaculaOd = $rpeChMaculaOd.' +4';
	else $rpeChMaculaOd = ' +4';
}
if($elem_macOd_armd_rpeCh_foveal == "Perifovial"){
	if($rpeChMaculaOd) $rpeChMaculaOd = $rpeChMaculaOd.' Perifovial';
	else $rpeChMaculaOd = ' Perifovial';
}
if($rpeChMaculaOd) $rpeChMaculaOd = $rpeChMaculaOd.' Rpe Charges.';
//RPE CHARGES OD


//RPE CHARGES Os
if($elem_macOs_armd_rpeCh_neg == "-ve"){
	$rpeChMaculaOs = ' -ve';
}
if($elem_macOs_armd_rpeCh_T == "T"){
	if($rpeChMaculaOs) $rpeChMaculaOs = $rpeChMaculaOs.' T';
	else $rpeChMaculaOs = ' T';
}
if($elem_macOs_armd_rpeCh_pos1 == "+1"){
	if($rpeChMaculaOs) $rpeChMaculaOs = $rpeChMaculaOs.' +1';
	else $rpeChMaculaOs = ' +1';
}
if($elem_macOs_armd_rpeCh_pos2 == "+2"){
	if($rpeChMaculaOs) $rpeChMaculaOs = $rpeChMaculaOs.' +2';
	else $rpeChMaculaOs = ' +2';
}
if($elem_macOs_armd_rpeCh_pos3 == "+3"){
	if($rpeChMaculaOs) $rpeChMaculaOs = $rpeChMaculaOs.' +3';
	else $rpeChMaculaOs = ' +3';
}
if($elem_macOs_armd_rpeCh_pos4 == "+4"){
	if($rpeChMaculaOs) $rpeChMaculaOs = $rpeChMaculaOs.' +4';
	else $rpeChMaculaOs = ' +4';
}
if($elem_macOs_armd_rpeCh_foveal == "Perifovial"){
	if($rpeChMaculaOs) $rpeChMaculaOs = $rpeChMaculaOs.' Perifovial';
	else $rpeChMaculaOs = ' Perifovial';
}
if($rpeChMaculaOs) $rpeChMaculaOs = $rpeChMaculaOs.' Rpe Charges.';
//RPE CHARGES OS

// SRNVM OD
if($elem_macOd_armd_srnvm_neg == "-ve"){
	$srnvmOd = ' -ve';
}
if($elem_macOd_armd_srnvm_subfoveal == "Subfoveal"){
	if($srnvmOd) $srnvmOd = $srnvmOd.' Subfoveal';
	else $srnvmOd = ' Subfoveal';
}
if($elem_macOd_armd_srnvm_exfoveal == "Perifovial"){
	if($srnvmOd) $srnvmOd = $srnvmOd.' Perifovial';
	else $srnvmOd = ' Perifovial';
}
if($srnvmOd) $srnvmOd = $srnvmOd.' SRNVM.';
// SRNVM OD

// SRNVM OS
if($elem_macOs_armd_srnvm_neg == "-ve"){
	$srnvmOs = ' -ve';
}
if($elem_macOs_armd_srnvm_subfoveal == "Subfoveal"){
	if($srnvmOs) $srnvmOs = $srnvmOs.' Subfoveal';
	else $srnvmOs = ' Subfoveal';
}
if($elem_macOs_armd_srnvm_exfoveal == "Perifovial"){
	if($srnvmOs) $srnvmOs = $srnvmOs.' Perifovial';
	else $srnvmOs = ' Perifovial';
}
if($srnvmOs) $srnvmOs = $srnvmOs.' SRNVM.';
// SRNVM OS

// EDEMA OD
if($elem_macOd_dr_macEdema_neg == "-ve"){
	$edemaOd = ' -ve';
}
if($elem_macOd_dr_macEdema_T == "T"){
	if($edemaOd) $edemaOd = $edemaOd.' T';
	else $edemaOd = ' T';
}
if($elem_macOd_dr_macEdema_pos1 == "+1"){
	if($edemaOd) $edemaOd = $edemaOd.' +1';
	else $edemaOd = ' +1';
}
if($elem_macOd_dr_macEdema_pos2 == "+2"){
	if($edemaOd) $edemaOd = $edemaOd.' +2';
	else $edemaOd = ' +2';
}
if($elem_macOd_dr_macEdema_pos3 == "+3"){
	if($edemaOd) $edemaOd = $edemaOd.' +3';
	else $edemaOd = ' +3';
}
if($elem_macOd_dr_macEdema_pos4 == "+4"){
	if($edemaOd) $edemaOd = $edemaOd.' +4';
	else $edemaOd = ' +4';
}
if($edemaOd) $edemaOd = $edemaOd.' EDEMA.';
// EDEMA OD


// EDEMA Os
if($elem_macOs_dr_macEdema_neg == "-ve"){
	$edemaOs = ' -ve';
}
if($elem_macOs_dr_macEdema_T == "T"){
	if($edemaOs) $edemaOs = $edemaOs.' T';
	else $edemaOs = ' T';
}
if($elem_macOs_dr_macEdema_pos1 == "+1"){
	if($edemaOs) $edemaOs = $edemaOs.' +1';
	else $edemaOs = ' +1';
}
if($elem_macOs_dr_macEdema_pos2 == "+2"){
	if($edemaOs) $edemaOs = $edemaOs.' +2';
	else $edemaOs = ' +2';
}
if($elem_macOs_dr_macEdema_pos3 == "+3"){
	if($edemaOs) $edemaOs = $edemaOs.' +3';
	else $edemaOs = ' +3';
}
if($elem_macOs_dr_macEdema_pos4 == "+4"){
	if($edemaOs) $edemaOs = $edemaOs.' +4';
	else $edemaOs = ' +4';
}
if($edemaOs) $edemaOs = $edemaOs.' EDEMA.';
// EDEMA OS

	//Focal Scar OD
	if($elem_periOd_dr_fScar_neg == "-ve"){
		$fScarOd = ' -ve';
	}
	if($elem_periOd_dr_fScar_T == "T"){
		if($fScarOd) $fScarOd = $fScarOd.' T';
		else $fScarOd = ' T';
	}
	if($elem_periOd_dr_fScar_pos1 == "+1"){
		if($fScarOd) $fScarOd = $fScarOd.' +1';
		else $fScarOd = ' +1';
	}
	if($elem_periOd_dr_fScar_pos2 == "+2"){
		if($fScarOd) $fScarOd = $fScarOd.' +2';
		else $fScarOd = ' +2';
	}
	if($elem_periOd_dr_fScar_pos3 == "+3"){
		if($fScarOd) $fScarOd = $fScarOd.' +3';
		else $fScarOd = ' +3';
	}
	if($elem_periOd_dr_fScar_pos4 == "+4"){
		if($fScarOd) $fScarOd = $fScarOd.' +4';
		else $fScarOd = ' +4';
	}
	if($fScarOd){
		$fScarOd = $fScarOd.' Focal Scar.';
	}
	//Focal Scar OD


	//Focal Scar OS
	if($elem_periOs_dr_fScar_neg == "-ve"){
		$fScarOs = ' -ve';
	}
	if($elem_periOs_dr_fScar_T == "T"){
		if($fScarOs) $fScarOs = $fScarOs.' T';
		else $fScarOs = ' T';
	}
	if($elem_periOs_dr_fScar_pos1 == "+1"){
		if($fScarOs) $fScarOs = $fScarOs.' +1';
		else $fScarOs = ' +1';
	}
	if($elem_periOs_dr_fScar_pos2 == "+2"){
		if($fScarOs) $fScarOs = $fScarOs.' +2';
		else $fScarOs = ' +2';
	}
	if($elem_periOs_dr_fScar_pos3 == "+3"){
		if($fScarOs) $fScarOs = $fScarOs.' +3';
		else $fScarOs = ' +3';
	}
	if($elem_periOs_dr_fScar_pos4 == "+4"){
		if($fScarOs) $fScarOs = $fScarOs.' +4';
		else $fScarOs = ' +4';
	}
	if($fScarOs){
		$fScarOs = $fScarOs.' Focal Scar.';
	}
	//Focal Scar OS

	//Prp Scar OD
	if($elem_macOd_dr_prpScar_neg == "-ve"){
		$prpScarOd = "-ve";
	}
	if($elem_macOd_dr_prpScar_T == "T"){
		if($prpScarOd) $prpScarOd = $prpScarOd." T";
		else $prpScarOd = ' T';
	}
	if($elem_macOd_dr_prpScar_pos1 == "+1"){
		if($prpScarOd) $prpScarOd = $prpScarOd." +1";
		else $prpScarOd = ' +1';
	}
	if($elem_macOd_dr_prpScar_pos2 == "+2"){
		if($prpScarOd) $prpScarOd = $prpScarOd." +2";
		else $prpScarOd = ' +2';
	}
	if($elem_macOd_dr_prpScar_pos3 == "+3"){
		if($prpScarOd) $prpScarOd = $prpScarOd." +3";
		else $prpScarOd = ' +3';
	}
	if($elem_macOd_dr_prpScar_pos4 == "+4"){
		if($prpScarOd) $prpScarOd = $prpScarOd." +4";
		else $prpScarOd = ' +4';
	}
	if($prpScarOd) $prpScarOd = $prpScarOd." Prp Scar.";
	//Prp Scar OD

	//Prp Scar OS
	if($elem_macOs_dr_prpScar_neg == "-ve"){
		$prpScarOs = "-ve";
	}
	if($elem_macOs_dr_prpScar_T == "T"){
		if($prpScarOs) $prpScarOs = $prpScarOs." T";
		else $prpScarOs = ' T';
	}
	if($elem_macOs_dr_prpScar_pos1 == "+1"){
		if($prpScarOs) $prpScarOs = $prpScarOs." +1";
		else $prpScarOs = ' +1';
	}
	if($elem_macOs_dr_prpScar_pos2 == "+2"){
		if($prpScarOs) $prpScarOs = $prpScarOs." +2";
		else $prpScarOs = ' +2';
	}
	if($elem_macOs_dr_prpScar_pos3 == "+3"){
		if($prpScarOs) $prpScarOs = $prpScarOs." +3";
		else $prpScarOs = ' +3';
	}
	if($elem_macOs_dr_prpScar_pos4 == "+4"){
		if($prpScarOs) $prpScarOs = $prpScarOs." +4";
		else $prpScarOs = ' +4';
	}
	if($prpScarOs) $prpScarOs = $prpScarOs." Prp Scar.";
	//Prp Scar OS
	
	// Hemorrhage OD
if($elem_macOd_hemoHage_preRetinal == "Pre-retinal"){
	$hemorrhageOd = ' Pre-retinal';
}
if($elem_macOd_hemoHage_retinalNfl == "Retinal/NFL"){
	if($hemorrhageOd) $hemorrhageOd = $hemorrhageOd.' Retinal/NFL';
	else $hemorrhageOd = ' Retinal/NFL';
}
if($elem_macOd_hemoHage_subRetinal == "Sub-retinal"){
	if($hemorrhageOd) $hemorrhageOd = $hemorrhageOd.' Sub-retinal';
	else $hemorrhageOd = ' Sub-retinal';
}
if($elem_macOd_hemoHage_flameHmg == "Flame Hmg"){
	if($hemorrhageOd) $hemorrhageOd = $hemorrhageOd.' Flame Hmg';
	else $hemorrhageOd = ' Flame Hmg';
}
if($elem_macOd_hemoHage_dotHmg == "Dot Hmg"){
	if($hemorrhageOd) $hemorrhageOd = $hemorrhageOd.' Dot Hmg';
	else $hemorrhageOd = ' Dot Hmg';
}
if($elem_macOd_hemoHage_microaneurism == "Micoraneurysm"){
	if($hemorrhageOd) $hemorrhageOd = $hemorrhageOd.' Micoraneurysm';
	else $hemorrhageOd = ' Micoraneurysm';
}
if($hemorrhageOd){
	$hemorrhageOd = $hemorrhageOd.' Hemorrhage.';
}	
// Hemorrhage OD

// Hemorrhage OS
if($elem_macOs_hemoHage_preRetinal == "Pre-retinal"){
	$hemorrhageOs = ' Pre-retinal';
}
if($elem_macOs_hemoHage_retinalNfl == "Retinal/NFL"){
	if($hemorrhageOs) $hemorrhageOs = $hemorrhageOs.' Retinal/NFL';
	else $hemorrhageOs = ' Retinal/NFL';
}
if($elem_macOs_hemoHage_subRetinal == "Sub-retinal"){
	if($hemorrhageOs) $hemorrhageOs = $hemorrhageOs.' Sub-retinal';
	else $hemorrhageOs = ' Sub-retinal';
}
if($elem_macOs_hemoHage_flameHmg == "Flame Hmg"){
	if($hemorrhageOs) $hemorrhageOs = $hemorrhageOs.' Flame Hmg';
	else $hemorrhageOs = ' Flame Hmg';
}
if($elem_macOs_hemoHage_dotHmg == "Dot Hmg"){
	if($hemorrhageOs) $hemorrhageOs = $hemorrhageOs.' Dot Hmg';
	else $hemorrhageOs = ' Dot Hmg';
}
if($elem_macOs_hemoHage_microaneurism == "Micoraneurysm"){
	if($hemorrhageOs) $hemorrhageOs = $hemorrhageOs.' Micoraneurysm';
	else $hemorrhageOs = ' Micoraneurysm';
}
if($hemorrhageOs){
	$hemorrhageOs = $hemorrhageOs.' Hemorrhage.';
}	
// Hemorrhage OS

// Exudate OD
if($elem_macOd_dr_exudate_neg == "-ve"){
	$ExudateMacOd = ' -ve';
}
if($elem_macOd_dr_exudate_T == "T"){
	if($ExudateMacOd) $ExudateMacOd = $ExudateMacOd.' T';
	else $ExudateMacOd = ' T';
}
if($elem_macOd_dr_exudate_pos1 == "+1"){
	if($ExudateMacOd) $ExudateMacOd = $ExudateMacOd.' +1';
	else $ExudateMacOd = ' +1';
}
if($elem_macOd_dr_exudate_pos2 == "+2"){
	if($ExudateMacOd) $ExudateMacOd = $ExudateMacOd.' +2';
	else $ExudateMacOd = ' +2';
}
if($elem_macOd_dr_exudate_pos3 == "+3"){
	if($ExudateMacOd) $ExudateMacOd = $ExudateMacOd.' +3';
	else $ExudateMacOd = ' +3';
}
if($elem_macOd_dr_exudate_pos4 == "+4"){
	if($ExudateMacOd) $ExudateMacOd = $ExudateMacOd.' +4';
	else $ExudateMacOd = ' +4';
}
if($ExudateMacOd) $ExudateMacOd = $ExudateMacOd.' Exudate.';
// Exudate OD


// Exudate OS
if($elem_macOs_dr_exudate_neg == "-ve"){
	$ExudateMacOs = ' -ve';
}
if($elem_macOs_dr_exudate_T == "T"){
	if($ExudateMacOs) $ExudateMacOs = $ExudateMacOs.' T';
	else $ExudateMacOs = ' T';
}
if($elem_macOs_dr_exudate_pos1 == "+1"){
	if($ExudateMacOs) $ExudateMacOs = $ExudateMacOs.' +1';
	else $ExudateMacOs = ' +1';
}
if($elem_macOs_dr_exudate_pos2 == "+2"){
	if($ExudateMacOs) $ExudateMacOs = $ExudateMacOs.' +2';
	else $ExudateMacOs = ' +2';
}
if($elem_macOs_dr_exudate_pos3 == "+3"){
	if($ExudateMacOs) $ExudateMacOs = $ExudateMacOs.' +3';
	else $ExudateMacOs = ' +3';
}
if($elem_macOs_dr_exudate_pos4 == "+4"){
	if($ExudateMacOs) $ExudateMacOs = $ExudateMacOs.' +4';
	else $ExudateMacOs = ' +4';
}
if($ExudateMacOs) $ExudateMacOs = $ExudateMacOs.' Exudate.';
// Exudate OS

//BDR OD
if($elem_macOd_dr_bdr_T == "T"){
	$macOdBdr = ' T';
}
if($elem_macOd_dr_bdr_pos1 == "+1"){
	if($macOdBdr) $macOdBdr = $macOdBdr.' +1';
	else $macOdBdr = ' +1';
}
if($elem_macOd_dr_bdr_pos2 == "+2"){
	if($macOdBdr) $macOdBdr = $macOdBdr.' +2';
	else $macOdBdr = ' +2';
}
if($elem_macOd_dr_bdr_pos2 == "+3"){
	if($macOdBdr) $macOdBdr = $macOdBdr.' +3';
	else $macOdBdr = ' +3';
}
if($elem_macOd_dr_bdr_pos4 == "+4"){
	if($macOdBdr) $macOdBdr = $macOdBdr.' +4';
	else $macOdBdr = ' +4';
}
if($macOdBdr) $macOdBdr = $macOdBdr.' BDR.';
//BDR OD


//BDR OS
if($elem_macOs_dr_bdr_T == "T"){
	$macOsBdr = ' T';
}
if($elem_macOs_dr_bdr_pos1 == "+1"){
	if($macOsBdr) $macOsBdr = $macOsBdr.' +1';
	else $macOsBdr = ' +1';
}
if($elem_macOs_dr_bdr_pos2 == "+2"){
	if($macOsBdr) $macOsBdr = $macOsBdr.' +2';
	else $macOsBdr = ' +2';
}
if($elem_macOs_dr_bdr_pos2 == "+3"){
	if($macOsBdr) $macOsBdr = $macOsBdr.' +3';
	else $macOsBdr = ' +3';
}
if($elem_macOs_dr_bdr_pos4 == "+4"){
	if($macOsBdr) $macOsBdr = $macOsBdr.' +4';
	else $macOsBdr = ' +4';
}
if($macOsBdr) $macOsBdr = $macOsBdr.' BDR.';
//BDR OS
?>