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
// IF PRINT THEN FORM ID
$printId = $_REQUEST['printId'];

//---get Detail For Patient -------
$qry =imw_query("select * from patient_data where id = $pid");
$patientDetails = array();
while($row_pat = imw_fetch_assoc($qry)){
	$patientDetails[] = $row_pat;
}
$patientName = $patientDetails[0]['lname'].', '.$patientDetails[0]['fname'].' ';
$patientName .= $patientDetails[0]['mname'];
$date = substr($patientDetails[0]['date'],0,strpos($patientDetails[0]['date'],' '));
$created_date = changeDates($date);
$date_of_birth = changeDates($patientDetails[0]['DOB']);
$cityAddress = $patientDetails[0]['city'];
if($patientDetails[0]['state'])
	$cityAddress .= ', '.$patientDetails[0]['state'].' ';
else
 	$cityAddress .= ' ';
$cityAddress .= $patientDetails[0]['postal_code'];
//Code to Show Paitine Image
$p_imagename = $patientDetails[0]['p_imagename'];
if($p_imagename){
	$dirPath = '../main/uploaddir'.$p_imagename;
	$dir_real_path = realpath($dirPath);
	$img_name = substr($p_imagename,strrpos($p_imagename,'/')+1);	
	copy($dir_real_path,'../common/new_html2pdf/'.$img_name);
		$dirPath = $img_name;
		if(file_exists($dir_real_path)){
		$patient_img['patient'] = $img_name;
		$fileSize = getimagesize($dir_real_path);
		if($fileSize[0]>80 || $fileSize[0]>90){
			$imageWidth2 = imageResize($fileSize[0],$fileSize[1],90);
			$patientImage = '<img style="cursor:pointer" src="'.$img_name.'" alt="patient Image" '.$imageWidth2.'>';
		}
		else{
			$patientImage = '<img style="cursor:pointer" src="'.$img_name.'" alt="patient Image">';
		}		
	}
}
//End Code to Show Patient Image//
list($y,$m,$d) = explode('-',$patientDetails[0]['DOB']);
$age =show_age($patientDetails[0]['DOB']) ;//date('Y') - $y ;
//--- Get Physician Details --------
$phyId = $patientDetails[0]['providerID'];
if($phyId > 0){
	$qry = imw_query("select concat(fname,', ',lname) as name, mname from users where id = '$phyId'");
	$phyDetails = array();	
	while($rowphy = imw_fetch_assoc($qry)){	
		$phyDetails[] = $rowphy;
	}
	$phyName = trim($phyDetails[0]['name'].' '.$phyDetails[0]['mname']);
}

//--- Get Default Facility Details -------
$qry = imw_query("select default_group from facility where facility_type = '1'");
$facilityDetail = array();	
while($rowfd = imw_fetch_assoc($qry)){	
	$facilityDetail[] = $rowfd;
}
if(count($facilityDetail)>0){
	$gro_id = $facilityDetail[0]['default_group'];
	$qry = imw_query("select * from groups_new where gro_id = '$gro_id'");
	$groupDetails = array();	
	while($rowgd = imw_fetch_assoc($qry)){	
		$groupDetails[] = $rowgd;
	}
}
//$qry1=imw_query("select * from  chart_left_cc_history where patient_id='$pid' and form_id='$form_id'");
$qry1=imw_query("select * from  chart_master_table where patient_id='$pid' and id='$form_id'");
$co=imw_num_rows($qry1);
if(($co > 0)){
	$crow=imw_fetch_array($qry1);
	$date_of_service = date("m-d-Y", strtotime($crow["date_of_service"]));	
}
/////End date of sevice Code////////////////
//--- Get Reffering Physician Details --------

//================= GETTING PHYSICIANS AND TECH
$getPhysicianTechStr = "SELECT * FROM users WHERE user_type = 1 OR user_type = 3";
$getPhysicianTechQry = imw_query($getPhysicianTechStr);
while($getPhysicianTechRows = imw_fetch_array($getPhysicianTechQry)){
	$phyTechId = $getPhysicianTechRows['id'];
	$phyTechFname = $getPhysicianTechRows['fname'];
	$phyTechMname = $getPhysicianTechRows['mname'];
	$phyTechLname = $getPhysicianTechRows['lname'];
	$physiciansTechs = $getPhysicianTechRows['username'];
	$physiciansTechs = $phyTechFname." ".$phyTechMname." ".$phyTechLname;
	$phyTechArray[$phyTechId] = ucwords($physiciansTechs);
	
}
//================= GETTING PHYSICIANS AND TECH



//================================================================= FUNCTION OF GETTING RECORDS
/*
function getData($fieldReq, $tableName, $idField, $val){
	$getDetailsStr = "SELECT $fieldReq FROM $tableName WHERE $idField = '$val'";
	$getDetailsQry = imw_query($getDetailsStr);
	$getDetailsRow = imw_fetch_array($getDetailsQry);
	return $getDetailsRow[$fieldReq];
}
*/
//================================================================= FUNCTION OF GETTING RECORDS


//================= GETTIN RECORD FOR PRINT
if($printId==''){
	$getSurgicalRecordStr = "SELECT * FROM surgical_tbl WHERE patient_id = '$patient_id' AND form_id = '$form_id'";
}else{
	$getSurgicalRecordStr = "SELECT * FROM surgical_tbl WHERE surgical_id = '$printId'";
}
//echo($getSurgicalRecordStr);
$getSurgicalRecordQry = imw_query($getSurgicalRecordStr);
$rowsCount = imw_num_rows($getSurgicalRecordQry);
$boolAScnExist = '';
$scnMultiImage = '';
if($rowsCount>0){
	$getSurgicalRecordRows = imw_fetch_array($getSurgicalRecordQry);
		$surgical_id = $getSurgicalRecordRows['surgical_id'];
		$sqlScan = "SELECT scan_id, image_name, file_path, file_type FROM ".constant("IMEDIC_SCAN_DB").".scans ".
					 "WHERE patient_id = '$patient_id' AND image_form = 'Ascan'  AND test_id  = '".$surgical_id."' "; 		 
		$sqlScanRes = 	imw_query($sqlScan);		 
		if(imw_num_rows($sqlScanRes)>0) {
			$boolAScnExist='yes';
			//start code for a-scanned image
			
			while($sqlScanRow = imw_fetch_array($sqlScanRes)) {
				$scn_img_name = $sqlScanRow['image_name'];
				$s_imagename = $sqlScanRow['file_path'];
				$s_file_type = $sqlScanRow['file_type'];
				
				if($s_imagename && $s_file_type!='application/pdf') {
					$scndirPath = '../main/uploaddir'.$s_imagename;
					$scn_dir_real_path = realpath($scndirPath);
					$scn_img_name = substr($s_imagename,strrpos($s_imagename,'/')+1);	
					copy($scn_dir_real_path,'../common/new_html2pdf/'.$scn_img_name);
						$scndirPath = $scn_img_name;
						if(file_exists($scn_dir_real_path)){
						$scnfileSize = getimagesize($scn_dir_real_path);
						if($scnfileSize[0]>80 || $scnfileSize[0]>90){
							$scnimageWidth2 = imageResize($scnfileSize[0],$scnfileSize[1],90);
							$scnMultiImage .= '<tr><td align="left"><img style="cursor:pointer" src="'.$scn_img_name.'" alt="patient Image" '.$scnimageWidth2.'></td></tr>';
						}
						else{
							$scnMultiImage .= '<tr><td align="left"><img style="cursor:pointer" src="'.$scn_img_name.'" alt="patient Image"></td></tr>';
						}		
					}
				
				}
			}
			//end code for a-scanned image
		}
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
			$autoSelectOD = getKHeadingName($autoSelectOD);
		$iolMasterSelectOD = $getSurgicalRecordRows['iolMasterSelectOD'];
			$iolMasterSelectOD = getKHeadingName($iolMasterSelectOD);
		$topographerSelectOD = $getSurgicalRecordRows['topographerSelectOD'];
			$topographerSelectOD = getKHeadingName($topographerSelectOD);
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
			$powerIolOD = getFormulaHeadName($powerIolOD);
		$holladayOD = $getSurgicalRecordRows['holladayOD'];
			$holladayOD = getFormulaHeadName($holladayOD);
		$srk_tOD = $getSurgicalRecordRows['srk_tOD'];
			$srk_tOD = getFormulaHeadName($srk_tOD);
		$hofferOD = $getSurgicalRecordRows['hofferOD'];
			$hofferOD = getFormulaHeadName($hofferOD);
		$iol1OD = $getSurgicalRecordRows['iol1OD'];
			$iol1OD = getLenseName($iol1OD);
		$iol1PowerOD = $getSurgicalRecordRows['iol1PowerOD'];
		$iol1HolladayOD = $getSurgicalRecordRows['iol1HolladayOD'];
		$iol1srk_tOD = $getSurgicalRecordRows['iol1srk_tOD'];
		$iol1HofferOD = $getSurgicalRecordRows['iol1HofferOD'];
		$iol2OD = $getSurgicalRecordRows['iol2OD'];
			$iol2OD = getLenseName($iol2OD);
		$iol2PowerOD = $getSurgicalRecordRows['iol2PowerOD'];
		$iol2HolladayOD = $getSurgicalRecordRows['iol2HolladayOD'];
		$iol2srk_tOD = $getSurgicalRecordRows['iol2srk_tOD'];
		$iol2HofferOD = $getSurgicalRecordRows['iol2HofferOD'];
		$iol3OD = $getSurgicalRecordRows['iol3OD'];
			$iol3OD = getLenseName($iol3OD);
		$iol3PowerOD = $getSurgicalRecordRows['iol3PowerOD'];
		$iol3HolladayOD = $getSurgicalRecordRows['iol3HolladayOD'];
		$iol3srk_tOD = $getSurgicalRecordRows['iol3srk_tOD'];
		$iol3HofferOD = $getSurgicalRecordRows['iol3HofferOD'];
		$iol4OD = $getSurgicalRecordRows['iol4OD'];
			$iol4OD = getLenseName($iol4OD);
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
			$selecedIOLsOD = getLenseName($selecedIOLsOD);
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
			$autoSelectOS = getKHeadingName($autoSelectOS);
		$iolMasterSelectOS = $getSurgicalRecordRows['iolMasterSelectOS'];
			$iolMasterSelectOS = getKHeadingName($iolMasterSelectOS);
		$topographerSelectOS = $getSurgicalRecordRows['topographerSelectOS'];
			$topographerSelectOS = getKHeadingName($topographerSelectOS);
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
			$powerIolOS = getFormulaHeadName($powerIolOS);
		$holladayOS = $getSurgicalRecordRows['holladayOS'];
			$holladayOS = getFormulaHeadName($holladayOS);
		$srk_tOS = $getSurgicalRecordRows['srk_tOS'];
			$srk_tOS = getFormulaHeadName($srk_tOS);
		$hofferOS = $getSurgicalRecordRows['hofferOS'];
			$hofferOS = getFormulaHeadName($hofferOS);
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
			$selecedIOLsOS = getLenseName($selecedIOLsOS);
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
if($rowsCount>0 ){ //&& $boolAScnExist=='yes'
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
<page pageset="old">
	
    <table width="100%" cellpadding="0" cellspacing="0" align="left" border="0" style="display:none;">
        <tr height="25">
            <td align="left" width="100%" class="tb_heading" style="width:100%;"><b>A/Scan</b></td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="0" align="left" border="0" style="display:block;width:100%;">
		
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
						<td style="width: 5%;">&nbsp;</td>
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

								if(isAppletModified($signature)){
									$table = 'surgical_tbl';
									$idName = 'surgical_id';
									$docSign = 'signature';
									$signImage = '../../images/white.jpg';
									$alt = 'Physician Sign'; 
												 
									 if(getAppletImage($surgical_id,$table,$idName,$docSign,$signImage,$alt,"1")){
										@copy("../main/html2pdfprint/".$gdFilename,"../common/new_html2pdf/".$gdFilename);
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
								if(isAppletModified($signature)){

									$table = 'surgical_tbl';
									$idName = 'surgical_id';
									$docSign = 'signatureOS';
									$signImage = '../../images/white.jpg';
									$alt = 'Physician Sign'; 
									if(getAppletImage($surgical_id,$table,$idName,$docSign,$signImage,$alt,"1")){
										@copy("../main/html2pdfprint/".$gdFilename,"../common/new_html2pdf/".$gdFilename);										
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
        <?php echo $scnMultiImage;?>
	</table>
</page>
	<?php
	
}

?>


