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
if($callFromInterface != 'admin'){
	require_once("../../library/patient_must_loaded.php");
}
require_once("../../library/classes/class.tests.php");
require_once("../../library/classes/SaveFile.php");
require_once("../../library/classes/ChartTestPrev.php");
require_once(dirname(__FILE__)."/ascan_function.php");
require_once("../../library/classes/work_view/Epost.php");

$library_path 			= $GLOBALS['webroot'].'/library';
$patient_id				= $_SESSION['patient'];
$objTests				= new Tests;
$testname               = "Ascan";
$objEpost				= new Epost($patient_id,$testname);
//$objTests->patient_id 	= $patient_id;

//MAKING OBJECT TO SAVE IMAGE FILES
$oSaveFile = new SaveFile($patient_id);
$authUserId_pro = $authUserId = $_SESSION['authId'];
$test_table_name		= 'surgical_tbl';
$this_test_properties	= $objTests->get_table_cols_by_test_table_name($test_table_name);

$this_test_screen_name	= $this_test_properties['temp_name'];
$test_master_id			= $this_test_properties['id'];

if($callFromInterface != 'admin'){
	$elem_per_vo			= $objTests->get_tests_VO_access_status();
	
	//iMedicMonitor status.
	$objTests->patient_whc_room();
	
	//----GET ALL ACTIVE TESTS FROM ADMIN------
	$ActiveTests			= $objTests->get_active_tests();

	// FILE NAME for PRINT
	$date_f=date("Y_m_d");
	$rand=rand(0,500);
	$test_name="chart_tests";
	$html_file_name = $test_name.'/'.$date_f.'/ascan_print_'.$_SESSION['authId']."_".$rand;
	$objTests->mk_print_folder($test_name,$date_f,$oSaveFile->upDir."/UserId_".$_SESSION['authId']."/tmp/");
	$final_html_file_name_path = $oSaveFile->upDir."/UserId_".$_SESSION['authId']."/tmp/".$html_file_name;

	//Retain QueryString
	$qstr4js="";
	if(isset($_SERVER["QUERY_STRING"]) && !empty($_SERVER["QUERY_STRING"])){
		$qstr4js .= "&".$_SERVER["QUERY_STRING"];
	}

	
	$elem_examDate = get_date_format(date('Y-m-d'));
	$elem_examTime = date('Y-m-d H:i:s'); //time();
	$elem_opidTestOrderedDate = ""; // $elem_examDate;
	
	//User and  User_type
	$logged_user 	= $objTests->logged_user;
	$userType 		= $objTests->logged_user_type;

	//Assign Chart Notes specific user Type by checking the list
	if(in_array($userType,$GLOBALS['arrValidCNPhy'])){
		$userType = 1;
	}else if(in_array($userType,$GLOBALS['arrValidCNTech'])){
		$userType = 3;
	}
	
	//-----ORDER BY USERS------------
	$order_by_users									= $objTests->get_order_by_users('cn');
	
	//--------OPERATOR NAME----
	$elem_operatorId = (($userType == 1 || $userType == 12) || ($userType == 3)) ? $_SESSION["authId"] : "";
	$elem_operatorName = (($userType == 1 || $userType == 12) || ($userType == 3)) ? $objTests->getPersonnal3($elem_operatorId) : "";
	
	//GETTING FORM ID AND FINALIZED STATUS
	list($form_id,$finalize_flag)		= $objTests->get_chart_form_id($patient_id);


	//================= GETTING VALUES FROM CHART NOTE ( chart_vision )
	get_vision_values($patient_id, $finalize_flag);

}


//================= LENSES DEFINED TO PROVIDER OS
$getLensesForProviderStr = "SELECT * FROM lensesdefined a,
							lenses_iol_type b
							WHERE a.physician_id = '$provider_idOS'
							AND a.iol_type_id = b.iol_type_id";
$getLensesForProviderQry = imw_query($getLensesForProviderStr);
if((imw_num_rows($getLensesForProviderQry)>0)){
	while($getLensesForProviderRows = imw_fetch_array($getLensesForProviderQry)){
		$iol_type_id = $getLensesForProviderRows['iol_type_id'];
		$providersLenses = $getLensesForProviderRows['lenses_iol_type'];
		$lensesProviderArray[$iol_type_id] = $providersLenses;
		$providerLensesArrOS[] = $providersLenses;
	}
}
//================= LENSES DEFINED TO PROVIDER OS

//================= LENSES DEFINED TO PROVIDER OD
$getLensesForProviderStr = "SELECT * FROM lensesdefined a,
							lenses_iol_type b
							WHERE a.physician_id = '$provider_idOD'
							AND a.iol_type_id = b.iol_type_id";
$getLensesForProviderQry = imw_query($getLensesForProviderStr);
if(imw_num_rows($getLensesForProviderQry)>0){
	while($getLensesForProviderRows = imw_fetch_array($getLensesForProviderQry)){
		$iol_type_idOD = $getLensesForProviderRows['iol_type_id'];
		$providersLensesOD = $getLensesForProviderRows['lenses_iol_type'];
		$providerLensesArrOD[] = $providersLensesOD;
	}
}
//================= LENSES DEFINED TO PROVIDER OD

//================= GETTING PHYSICIANS
$getPhysicianStr = "SELECT * FROM users WHERE user_type IN (".implode(",", $GLOBALS['arrValidCNPhy']).") and delete_status='0'";
$getPhysicianQry = imw_query($getPhysicianStr);
while($getPhysicianRows = imw_fetch_array($getPhysicianQry)){
	$phyId = $getPhysicianRows['id'];
	$phyFname = $getPhysicianRows['fname'];
	$phyMname = $getPhysicianRows['mname'];
	$phyLname = $getPhysicianRows['lname'];
	$physicians = $getPhysicianRows['username'];
	$physicians = $phyFname." ".$phyMname." ".$phyLname;
	$phyArray[$phyId] = ucwords($physicians);
}
//================= GETTING PHYSICIANS

//================= GETTING LENSES
$getLensesStr = "SELECT * FROM lenses_iol_type ORDER BY lenses_iol_type ASC";
$getLensesQry = imw_query($getLensesStr);
while($getLensesRows = imw_fetch_array($getLensesQry)){
	$iol_type_id = $getLensesRows['iol_type_id'];
	$lenses_iol_type = $getLensesRows['lenses_iol_type'];
	$lensesArray[] = array($lenses_iol_type, $xyz, $lenses_iol_type,$iol_type_id);
}
//================= GETTING LENSES

//================= GETTING PHYSICIANS AND TECH
$getPhysicianTechStr = "SELECT * FROM users WHERE (user_type IN (".implode(",", $GLOBALS['arrValidCNPhy']).") OR user_type IN (".implode(",", $GLOBALS['arrValidCNTech']).")) and delete_status = '0' ";
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

//=================== FORMULA HEADING NAMES ARRAY
$getFormulaheadingsStr = "SELECT * FROM formulaheadings";
$getFormulaheadingsQry = imw_query($getFormulaheadingsStr);
while($getFormulaheadingsRows = imw_fetch_array($getFormulaheadingsQry)){
	$formulaHeadings = $getFormulaheadingsRows['formula_heading_name'];
	$formulaHeadingsArr[] =  array($formulaHeadings, $xyz, $formulaHeadings);
}
$formulaHeadingsArr[] = array("Other", $xyz, "");


//=================== K READING HEADING ARRAY
$getKreadingNameStr = "SELECT * FROM kheadingnames GROUP BY kheadingName ORDER BY kheadingName";
$getKreadingNameQry = imw_query($getKreadingNameStr);
while($getKreadingNameRows = imw_fetch_array($getKreadingNameQry)){
	$kReadingHeading = $getKreadingNameRows['kheadingName'];
	
	if(strpos($kReadingHeading, "K[")===false){$kReadingHeading = 'K['.$kReadingHeading;}
	if(strpos($kReadingHeading, "]")===false){$kReadingHeading = $kReadingHeading."]";}		
	//$kReadingHeadingName = $kReadingHeading;	
	$kReadingArr[] = array($kReadingHeading, $xyz, $kReadingHeading);
}
$kReadingArr[] = array("Other", $xyz, "");

if($callFromInterface != 'admin'){
	//================= GETTING PATIENT DATA
	$getPatientDataStr = "SELECT * FROM patient_data WHERE id = '$patient_id'";
	$getPatientDataQry = imw_query($getPatientDataStr);
	$getPatientDataRow = imw_fetch_array($getPatientDataQry);
	$patFname = $getPatientDataRow['fname'];
	$patMname = $getPatientDataRow['mname'];
	$patLname = $getPatientDataRow['lname'];
	$patientName = $patFname." ".$patMname." ".$patLname;

	$DOB = $getPatientDataRow['DOB'];
	list($dobYear, $dobMonth, $dobDay) = explode("-", $DOB);
	$DOB = $dobMonth."-".$dobDay."-".$dobYear;
	//============ GETTING AGE
	list($todayMonth, $todayDay, $todayYear) = explode("-", $today);
	$age = $todayYear - $dobYear;
	//================= GETTING PACHY READINGS


	//================= GETTING PACHY READINGS
	$tmpPachy = setDefPachyVals($form_id);
	if(count($tmpPachy)>0){
		$pachymetryValOD=(!empty($tmpPachy[1])) ? $tmpPachy[1] : $tmpPachy[0];
		$pachymetryCorrecOD=$tmpPachy[2];
		$pachymetryValOS=(!empty($tmpPachy[4])) ? $tmpPachy[4] : $tmpPachy[3];
		$pachymetryCorrecOS=$tmpPachy[5];
	}
	//================= GETTING PACHY READINGS
}

// Getting Values for Surgical Table ---------------------------
$classText = "";
//Test --

if($callFromInterface != 'admin'){
	//Test id
	$tId = 0;
	if(isset($_GET["tId"])){
		$tId = $_GET["tId"];
		//if you come directly without chart
		//Set Form Id zero
		$form_id = 0;
	}
	if(!empty($form_id)){
		//Get based on Form id patient id
		$getSurgicalRecordSavedStr = "SELECT * FROM surgical_tbl
									 WHERE patient_id = '$patient_id'
									 AND form_id = '$form_id' 
									 AND purged='0' ";
		$getSurgicalRecordSavedQry = imw_query($getSurgicalRecordSavedStr);
		$rowsCount = imw_num_rows($getSurgicalRecordSavedQry);
	
	}else{
		//if test id is given, then edit
		if(isset($tId) && !empty($tId)){
		//Get record based on patient id and test id
		$getSurgicalRecordSavedStr = "SELECT * FROM surgical_tbl
									  WHERE patient_id = '$patient_id'
									  AND surgical_id = '$tId'";
		$getSurgicalRecordSavedQry = imw_query($getSurgicalRecordSavedStr);
		$rowsCount = imw_num_rows($getSurgicalRecordSavedQry);
		
		}else{
			$getSurgicalRecordSavedQry = false; // open new test for patient
			$rowsCount = 0;
		}
	}
	//Test --

	if($rowsCount>0){
		$elem_edMode="update";
		$getSurgicalRecordSavedRow = imw_fetch_assoc($getSurgicalRecordSavedQry);
		extract($getSurgicalRecordSavedRow);			
			////////////////////////////////////////////// NOT EXTRACTED VARIABLES
			$test_form_id = $getSurgicalRecordSavedRow["form_id"];
			$sign_path = $getSurgicalRecordSavedRow["sign_path"];
			$sign_path_date_time = $getSurgicalRecordSavedRow["sign_path_date_time"];
			$sign_path_date = $sign_path_time = "";
			if($sign_path && $sign_path_date_time!="0000-00-00 00:00:00" || $sign_path_date_time!=0) {
				$sign_path_date = date("".phpDateFormat()."",strtotime($sign_path_date_time));
				$sign_path_time = date("h:i A",strtotime($sign_path_date_time));
			}		
	
			//OD
				//MR VALUES
				$vis_mr_od_s = $getSurgicalRecordSavedRow['mrSOD'];
					$vis_mr_od_sC = $vis_mr_od_s;
				$vis_mr_od_c = $getSurgicalRecordSavedRow['mrCOD'];
					$vis_mr_od_cC = $vis_mr_od_c;
				$vis_mr_od_a = $getSurgicalRecordSavedRow['mrAOD'];
					$vis_mr_od_aC = $vis_mr_od_a;
				$vis_ak_od_k = $getSurgicalRecordSavedRow['k1Auto1OD'];
					$vis_ak_od_kC = $vis_ak_od_k;
				$vis_ak_od_x = $getSurgicalRecordSavedRow['k1Auto2OD'];
					$vis_ak_od_xC = $vis_ak_od_x;
				$vis_ak_od_slash = $getSurgicalRecordSavedRow['k2Auto1OD'];
					$vis_ak_od_slashC = $vis_ak_od_slash;
				$k2Auto2OD = $getSurgicalRecordSavedRow['k2Auto2OD'];
					$k2Auto2ODC = $k2Auto2OD;
	
				//PERFORMED BY PROVIDER AND DATE
				$authUserId = $getSurgicalRecordSavedRow['performedByOD'];
				$dateOD = $getSurgicalRecordSavedRow['dateOD'];
					list($dateODYear, $dateODMonth, $dateODDay) = explode("-", $dateOD);
					$today = $dateODMonth."-".$dateODDay."-".$dateODYear;
				//K READING VALUES K[Auto]
				$autoSelectOD = $getSurgicalRecordSavedRow['autoSelectOD'];
					$autoSelectOD = getKheadingName($autoSelectOD);
				$iolMasterSelectOD = $getSurgicalRecordSavedRow['iolMasterSelectOD'];
					$iolMasterSelectOD = getKheadingName($iolMasterSelectOD);
				$topographerSelectOD = $getSurgicalRecordSavedRow['topographerSelectOD'];
					$topographerSelectOD = getKheadingName($topographerSelectOD);
				//IOL VALUES HOLLADAY
				$powerIolOD = $getSurgicalRecordSavedRow['powerIolOD'];
					$powerIolOD = getFormulaHeadName($powerIolOD);
				$holladayOD = $getSurgicalRecordSavedRow['holladayOD'];
					$holladayOD = getFormulaHeadName($holladayOD);
				$srk_tOD = $getSurgicalRecordSavedRow['srk_tOD'];
					$srk_tOD = getFormulaHeadName($srk_tOD);
				$hofferOD = $getSurgicalRecordSavedRow['hofferOD'];
					$hofferOD = getFormulaHeadName($hofferOD);
				//PERFORMED BY IOL
				$provider_idOD = $getSurgicalRecordSavedRow['performedByPhyOD'];
				//IOL VALUES
				$iol1OD = $getSurgicalRecordSavedRow['iol1OD'];
				if($iol1OD!=''){
					$providerLensesArrOD[0] = getLenseName($iol1OD);
					$providerLensesODArr[$iol1OD] = getLenseName($iol1OD);
				}
				$iol2OD = $getSurgicalRecordSavedRow['iol2OD'];
				if($iol2OD!=''){
					$providerLensesArrOD[1] = getLenseName($iol2OD);
					$providerLensesODArr[$iol2OD] = getLenseName($iol2OD);
				}
				$iol3OD = $getSurgicalRecordSavedRow['iol3OD'];
				if($iol3OD!=''){
					$providerLensesArrOD[2] = getLenseName($iol3OD);
					$providerLensesODArr[$iol3OD] = getLenseName($iol3OD);
				}
				$iol4OD = $getSurgicalRecordSavedRow['iol4OD'];
				if($iol4OD!=''){
					$providerLensesArrOD[3] = getLenseName($iol4OD);
					$providerLensesODArr[$iol4OD] = getLenseName($iol4OD);
				}
	
			//OS
				//MR VALUES
				$vis_mr_os_s = $getSurgicalRecordSavedRow['mrSOS'];
					$vis_mr_os_sC = $vis_mr_os_s;
				$vis_mr_os_c = $getSurgicalRecordSavedRow['mrCOS'];
					$vis_mr_os_cC = $vis_mr_os_c;
				$vis_mr_os_a = $getSurgicalRecordSavedRow['mrAOS'];
					$vis_mr_os_aC = $vis_mr_os_a;
				$vis_ak_os_k = $getSurgicalRecordSavedRow['k1Auto1OS'];
					$vis_ak_os_kC = $vis_ak_os_k;
				$vis_ak_os_x = $getSurgicalRecordSavedRow['k1Auto2OS'];
					$vis_ak_os_xC = $vis_ak_os_x;
				$vis_ak_os_slash = $getSurgicalRecordSavedRow['k2Auto1OS'];
					$vis_ak_os_slashC = $vis_ak_os_slash;
				$k2Auto2OS = $getSurgicalRecordSavedRow['k2Auto2OS'];
					$k2Auto2OSC = $k2Auto2OS;
	
				//PERFORMED BY PROVIDER AND DATE
				$provider_idOS = $getSurgicalRecordSavedRow['performedByOS'];
				$dateOS = $getSurgicalRecordSavedRow['dateOS'];
					list($dateOSYear, $dateOSMonth, $dateOSDay) = explode("-", $dateOS);
					$dateOS = $dateOSMonth."-".$dateOSDay."-".$dateOSYear;
				//K READING VALUES K[Auto]
				$autoSelectOS = $getSurgicalRecordSavedRow['autoSelectOS'];
					$autoSelectOS = getKheadingName($autoSelectOS);
				$iolMasterSelectOS = $getSurgicalRecordSavedRow['iolMasterSelectOS'];
					$iolMasterSelectOS = getKheadingName($iolMasterSelectOS);
				$topographerSelectOS = $getSurgicalRecordSavedRow['topographerSelectOS'];
					$topographerSelectOS = getKheadingName($topographerSelectOS);
				//IOL VALUES HOLLADAY
				$powerIolOS = $getSurgicalRecordSavedRow['powerIolOS'];
					$powerIolOS = getFormulaHeadName($powerIolOS);
				$holladayOS = $getSurgicalRecordSavedRow['holladayOS'];
					$holladayOS = getFormulaHeadName($holladayOS);
				$srk_tOS = $getSurgicalRecordSavedRow['srk_tOS'];
					$srk_tOS = getFormulaHeadName($srk_tOS);
				$hofferOS = $getSurgicalRecordSavedRow['hofferOS'];
					$hofferOS = getFormulaHeadName($hofferOS);
				//IOL VALUES
				$iol1OS = $getSurgicalRecordSavedRow['iol1OS'];
				if($iol1OS!=''){
					$providerLensesArrOS[0] = getLenseName($iol1OS);
					$providerLensesOSArr[$iol1OS] = getLenseName($iol1OS);
				}
				$iol2OS = $getSurgicalRecordSavedRow['iol2OS'];
				if($iol2OS!=''){
					$providerLensesArrOS[1] = getLenseName($iol2OS);
					$providerLensesOSArr[$iol2OS] = getLenseName($iol2OS);
				}
				$iol3OS = $getSurgicalRecordSavedRow['iol3OS'];
				if($iol3OS!=''){
					$providerLensesArrOS[2] = getLenseName($iol3OS);
					$providerLensesOSArr[$iol3OS] = getLenseName($iol3OS);
				}
				$iol4OS = $getSurgicalRecordSavedRow['iol4OS'];
				if($iol4OS!=''){
					$providerLensesArrOS[3] = getLenseName($iol4OS);
					$providerLensesOSArr[$iol4OS] = getLenseName($iol4OS);
				}
	
				/* Date & Time */
				$tmp = $getSurgicalRecordSavedRow["examDate"];
				$elem_examTime = $getSurgicalRecordSavedRow["examTime"];
	
				if($tmp != "0000-00-00"){
					$elem_examDate = get_date_format($tmp);
				}
	
				/* Date & Time */
	
				$encounterId = $getSurgicalRecordSavedRow["encounter_id"];
				$elem_opidTestOrdered = $getSurgicalRecordSavedRow["ordrby"];
				if(($getSurgicalRecordSavedRow["ordrdt"] != "" && $getSurgicalRecordSavedRow["ordrdt"] != "0000-00-00")){
					$elem_opidTestOrderedDate = get_date_format($getSurgicalRecordSavedRow["ordrdt"]);
				}
	
				//Ag 09 changes --
	
				$sur_dateOD = $getSurgicalRecordSavedRow["sur_dt_od"];
				$elem_proc_od = $getSurgicalRecordSavedRow["proc_od"];
				$elem_anes_od = $getSurgicalRecordSavedRow["anes_od"];
				$elem_visc_od = $getSurgicalRecordSavedRow["visc_od"];
				$elem_visc_od_other = $getSurgicalRecordSavedRow["visc_od_other"];
				$elem_opts_od = $getSurgicalRecordSavedRow["opts_od"];
				$elem_opts_od_other = $getSurgicalRecordSavedRow["opts_od_other"];
				$elem_iol2dn_od = $getSurgicalRecordSavedRow["iol2dn_od"];
	
				$sur_dateOS = $getSurgicalRecordSavedRow["sur_dt_os"];
				$elem_proc_os = $getSurgicalRecordSavedRow["proc_os"];
				$elem_anes_os = $getSurgicalRecordSavedRow["anes_os"];
				$elem_visc_os = $getSurgicalRecordSavedRow["visc_os"];
				$elem_visc_os_other = $getSurgicalRecordSavedRow["visc_os_other"];
				$elem_opts_os = $getSurgicalRecordSavedRow["opts_os"];
				$elem_opts_os_other = $getSurgicalRecordSavedRow["opts_os_other"];
				$elem_iol2dn_os = $getSurgicalRecordSavedRow["iol2dn_os"];
	
				//Ag 09 changes --
				
				//chart notes vales
				$str_cn_vals=$getSurgicalRecordSavedRow["cn_mrk_val"];
	
			/////////////////////////////////////////////// NOT EXTRACTED VARIABLES
	
	//FOR COMPARE
		//IF PREVIOUS NOT EXISTS
	
				$authUserIdC = $authUserId;
				$dateODC = $dateOD;
				$autoSelectODC = $autoSelectOD;
				$iolMasterSelectODC = $iolMasterSelectOD;
				$topographerSelectODC = $topographerSelectOD;
				$k1IolMaster1ODC = $k1IolMaster1OD;
				$k1IolMaster2ODC = $k1IolMaster2OD;
				$k1Topographer1ODC = $k1Topographer1OD;
				$k1Topographer2ODC = $k1Topographer2OD;
				$k2IolMaster1ODC = $k2IolMaster1OD;
				$k2IolMaster2ODC = $k2IolMaster2OD;
				$k2Topographer1ODC = $k2Topographer1OD;
				$k2Topographer2ODC = $k2Topographer2OD;
				$cylIolMaster1ODC = $cylIolMaster1OD;
				$cylIolMaster2ODC = $cylIolMaster2OD;
				$cylTopographer1ODC = $cylTopographer1OD;
				$cylTopographer2ODC = $cylTopographer2OD;
				$aveIolMasterODC = $aveIolMasterOD;
				$aveTopographerODC = $aveTopographerOD;
				$contactLengthODC = $contactLengthOD;
				$immersionLengthODC = $immersionLengthOD;
				$iolMasterLengthODC = $iolMasterLengthOD;
				$contactNotesODC = $contactNotesOD;
				$immersionNotesODC = $immersionNotesOD;
				$iolMasterNotesODC = $iolMasterNotesOD;
				$performedByPhyODC = $performedByPhyOD;
				$powerIolODC = $powerIolOD;
				$holladayODC = $holladayOD;
				$srk_tODC = $srk_tOD;
				$hofferODC = $hofferOD;
				$iol1ODC = $iol1OD;
				$iol1PowerODC = $iol1PowerOD;
				$iol1HolladayODC = $iol1HolladayOD;
				$iol1srk_tODC = $iol1srk_tOD;
				$iol1HofferODC = $iol1HofferOD;
				$iol2ODC = $iol2OD;
				$iol2PowerODC = $iol2PowerOD;
				$iol2HolladayODC = $iol2HolladayOD;
				$iol2srk_tODC = $iol2srk_tOD;
				$iol2HofferODC = $iol2HofferOD;
				$iol3ODC = $iol3OD;
				$iol3PowerODC = $iol3PowerOD;
				$iol3HolladayODC = $iol3HolladayOD;
				$iol3srk_tODC = $iol3srk_tOD;
				$iol3HofferODC = $iol3HofferOD;
				$iol4ODC = $iol4OD;
				$iol4PowerODC = $iol4PowerOD;
				$iol4HolladayODC = $iol4HolladayOD;
				$iol4srk_tODC = $iol4srk_tOD;
				$iol4HofferODC = $iol4HofferOD;
				$cellCountODC = $cellCountOD;
				$notesODC = $notesOD;
				$pachymetryValODC = $pachymetryValOD;
				$pachymetryCorrecODC = $pachymetryCorrecOD;
				$cornealDiamODC = $cornealDiamOD;
				$dominantEyeODC = $dominantEyeOD;
				$pupilSize1ODC = $pupilSize1OD;
				$pupilSize2ODC = $pupilSize2OD;
				$cataractODC = $cataractOD;
				$astigmatismODC = $astigmatismOD;
				$myopiaODC = $myopiaOD;
				$selecedIOLsODC = $selecedIOLsOD;
				$notesAssesmentPlansODC = $notesAssesmentPlansOD;
				$lengthODC = $lengthOD;
				$lengthTypeODC = $lengthTypeOD;
				$axisODC = $axisOD;
				$performedByOSC = $performedByOS;
				$dateOSC = $dateOS;
				$autoSelectOSC = $autoSelectOS;
				$iolMasterSelectOSC = $iolMasterSelectOS;
				$topographerSelectOSC = $topographerSelectOS;
				$k1IolMaster1OSC = $k1IolMaster1OS;
				$k1IolMaster2OSC = $k1IolMaster2OS;
				$k1Topographer1OSC = $k1Topographer1OS;
				$k1Topographer2OSC = $k1Topographer2OS;
				$k2IolMaster1OSC = $k2IolMaster1OS;
				$k2IolMaster2OSC = $k2IolMaster2OS;
				$k2Topographer1OSC = $k2Topographer1OS;
				$k2Topographer2OSC = $k2Topographer2OS;
				$cylIolMaster1OSC = $cylIolMaster1OS;
				$cylIolMaster2OSC = $cylIolMaster2OS;
				$cylTopographer1OSC = $cylTopographer1OS;
				$cylTopographer2OSC = $cylTopographer2OS;
				$aveIolMasterOSC = $aveIolMasterOS;
				$aveTopographerOSC = $aveTopographerOS;
				$contactLengthOSC = $contactLengthOS;
				$immersionLengthOSC = $immersionLengthOS;
				$iolMasterLengthOSC = $iolMasterLengthOS;
				$contactNotesOSC = $contactNotesOS;
				$immersionNotesOSC = $immersionNotesOS;
				$iolMasterNotesOSC = $iolMasterNotesOS;
				$powerIolOSC = $powerIolOS;
				$holladayOSC = $holladayOS;
				$srk_tOSC = $srk_tOS;
				$hofferOSC = $hofferOS;
				$iol1OSC = $iol1OS;
				$iol1PowerOSC = $iol1PowerOS;
				$iol1HolladayOSC = $iol1HolladayOS;
				$iol1srk_tOSC = $iol1srk_tOS;
				$iol1HofferOSC = $iol1HofferOS;
				$iol2OSC = $iol2OS;
				$iol2PowerOSC = $iol2PowerOS;
				$iol2HolladayOSC = $iol2HolladayOS;
				$iol2srk_tOSC = $iol2srk_tOS;
				$iol2HofferOSC = $iol2HofferOS;
				$iol3OSC = $iol3OS;
				$iol3PowerOSC = $iol3PowerOS;
				$iol3HolladayOSC = $iol3HolladayOS;
				$iol3srk_tOSC = $iol3srk_tOS;
				$iol3HofferOSC = $iol3HofferOS;
				$iol4OSC = $iol4OS;
				$iol4PowerOSC = $iol4PowerOS;
				$iol4HolladayOSC = $iol4HolladayOS;
				$iol4srk_tOSC = $iol4srk_tOS;
				$iol4HofferOSC = $iol4HofferOS;
				$cellCountOSC = $cellCountOS;
				$notesOSC = $notesOS;
				$pachymetryValOSC = $pachymetryValOS;
				$pachymetryCorrecOSC = $pachymetryCorrecOS;
				$cornealDiamOSC = $cornealDiamOS;
				$dominantEyeOSC = $dominantEyeOS;
				$pupilSize1OSC = $pupilSize1OS;
				$pupilSize2OSC = $pupilSize2OS;
				$acdODC = $acdOD;
				$acdOSC = $acdOS;
				$w2wODC=$w2wOD;
				$w2wOSC=$w2wOS;
	
				$cataractOSC = $cataractOS;
				$astigmatismOSC = $astigmatismOS;
				$myopiaOSC = $myopiaOS;
				$selecedIOLsOSC = $selecedIOLsOS;
				$notesAssesmentPlansOSC = $notesAssesmentPlansOS;
				$cutsOSC = $cutsOS;
				$lengthOSC = $lengthOS;
				$lengthTypeOSC = $lengthTypeOS;
				$temporalOSC = $temporalOS;
				$signedByIdC = $signedById;
				$signatureC = $signature;
				$signedByOSIdC = $signedByOSId;
				$signatureOSC = $signatureOS;
				$performedIolOSC = $performedIolOS;
	
	
		//IF PREVIOUS EXISTS
			//FOR GETTING PREVIOUS RESULT
			/*
			$getCompareStr = "SELECT * FROM surgical_tbl
								WHERE patient_id = '$patient_id'
								ORDER BY form_id DESC limit 0, 2";
			*/
			$getCompareStr = "SELECT * FROM surgical_tbl
								WHERE patient_id = '$patient_id'
								ORDER BY surgical_id DESC limit 0, 2";
			$getCompareQry = imw_query($getCompareStr);
			while($getCompareRows = imw_fetch_array($getCompareQry)){
				++$recordSet;
				if($recordSet==2){
					$vis_mr_od_sC = $getCompareRows['mrSOD'];
					$vis_mr_od_cC = $getCompareRows['mrCOD'];
					$vis_mr_od_aC = $getCompareRows['mrAOD'];
					$visionODC = $getCompareRows['visionOD'];
					$glareODC = $getCompareRows['glareOD'];
					$authUserIdC = $getCompareRows['performedByOD'];
					$dateODC = $getCompareRows['dateOD'];
					$autoSelectODC = $getCompareRows['autoSelectOD'];
					$iolMasterSelectODC = $getCompareRows['iolMasterSelectOD'];
					$topographerSelectODC = $getCompareRows['topographerSelectOD'];
					$k1IolMaster1ODC = $getCompareRows['k1IolMaster1OD'];
					$k1IolMaster2ODC = $getCompareRows['k1IolMaster2OD'];
					$k1Topographer1ODC = $getCompareRows['k1Topographer1OD'];
					$k1Topographer2ODC = $getCompareRows['k1Topographer2OD'];
					$k2IolMaster1ODC = $getCompareRows['k2IolMaster1OD'];
					$k2IolMaster2ODC = $getCompareRows['k2IolMaster2OD'];
					$k2Topographer1ODC = $getCompareRows['k2Topographer1OD'];
					$k2Topographer2ODC = $getCompareRows['k2Topographer2OD'];
					$cylIolMaster1ODC = $getCompareRows['cylIolMaster1OD'];
					$cylIolMaster2ODC = $getCompareRows['cylIolMaster2OD'];
					$cylTopographer1ODC = $getCompareRows['cylTopographer1OD'];
					$cylTopographer2ODC = $getCompareRows['cylTopographer2OD'];
					$aveIolMasterODC = $getCompareRows['aveIolMasterOD'];
					$aveTopographerODC = $getCompareRows['aveTopographerOD'];
					$contactLengthODC = $getCompareRows['contactLengthOD'];
					$immersionLengthODC = $getCompareRows['immersionLengthOD'];
					$iolMasterLengthODC = $getCompareRows['iolMasterLengthOD'];
					$contactNotesODC = $getCompareRows['contactNotesOD'];
					$immersionNotesODC = $getCompareRows['immersionNotesOD'];
					$iolMasterNotesODC = $getCompareRows['iolMasterNotesOD'];
					$performedByPhyODC = $getCompareRows['performedByPhyOD'];
					$powerIolODC = $getCompareRows['powerIolOD'];
						$powerIolODC = getFormulaHeadName($powerIolODC);
					$holladayODC = $getCompareRows['holladayOD'];
						$holladayODC = getFormulaHeadName($holladayODC);
					$srk_tODC = $getCompareRows['srk_tOD'];
						$srk_tODC = getFormulaHeadName($srk_tODC);
					$hofferODC = $getCompareRows['hofferOD'];
					$iol1ODC = $getCompareRows['iol1OD'];
					$iol1PowerODC = $getCompareRows['iol1PowerOD'];
					$iol1HolladayODC = $getCompareRows['iol1HolladayOD'];
					$iol1srk_tODC = $getCompareRows['iol1srk_tOD'];
					$iol1HofferODC = $getCompareRows['iol1HofferOD'];
					$iol2ODC = $getCompareRows['iol2OD'];
					$iol2PowerODC = $getCompareRows['iol2PowerOD'];
					$iol2HolladayODC = $getCompareRows['iol2HolladayOD'];
					$iol2srk_tODC = $getCompareRows['iol2srk_tOD'];
					$iol2HofferODC = $getCompareRows['iol2HofferOD'];
					$iol3ODC = $getCompareRows['iol3OD'];
					$iol3PowerODC = $getCompareRows['iol3PowerOD'];
					$iol3HolladayODC = $getCompareRows['iol3HolladayOD'];
					$iol3srk_tODC = $getCompareRows['iol3srk_tOD'];
					$iol3HofferODC = $getCompareRows['iol3HofferOD'];
					$iol4ODC = $getCompareRows['iol4OD'];
					$iol4PowerODC = $getCompareRows['iol4PowerOD'];
					$iol4HolladayODC = $getCompareRows['iol4HolladayOD'];
					$iol4srk_tODC = $getCompareRows['iol4srk_tOD'];
					$iol4HofferODC = $getCompareRows['iol4HofferOD'];
					$cellCountODC = $getCompareRows['cellCountOD'];
					$notesODC = $getCompareRows['notesOD'];
					$pachymetryValODC = $getCompareRows['pachymetryValOD'];
					$pachymetryCorrecODC = $getCompareRows['pachymetryCorrecOD'];
					$cornealDiamODC = $getCompareRows['cornealDiamOD'];
					$dominantEyeODC = $getCompareRows['dominantEyeOD'];
					$pupilSize1ODC = $getCompareRows['pupilSize1OD'];
					$pupilSize2ODC = $getCompareRows['pupilSize2OD'];
					$cataractODC = $getCompareRows['cataractOD'];
					$astigmatismODC = $getCompareRows['astigmatismOD'];
					$myopiaODC = $getCompareRows['myopiaOD'];
					$selecedIOLsODC = $getCompareRows['selecedIOLsOD'];
					$notesAssesmentPlansODC = $getCompareRows['notesAssesmentPlansOD'];
					$lriODC = $getCompareRows['lriOD'];
					$dlODC = $getCompareRows['dlOD'];
					$synechiolysisODC = $getCompareRows['synechiolysisOD'];
					$irishooksODC = $getCompareRows['irishooksOD'];
					$trypanblueODC = $getCompareRows['trypanblueOD'];
					$flomaxODC = $getCompareRows['flomaxOD'];
					$cutsODC = $getCompareRows['cutsOD'];
					$lengthODC = $getCompareRows['lengthOD'];
					$lengthTypeODC = $getCompareRows['lengthTypeOD'];
					$axisODC = $getCompareRows['axisOD'];
					$superiorODC = $getCompareRows['superiorOD'];
					$inferiorODC = $getCompareRows['inferiorOD'];
					$nasalODC = $getCompareRows['nasalOD'];
					$temporalODC = $getCompareRows['temporalOD'];
					$STODC = $getCompareRows['STOD'];
					$SNODC = $getCompareRows['SNOD'];
					$ITODC = $getCompareRows['ITOD'];
					$INODC = $getCompareRows['INOD'];
					$vis_mr_os_sC = $getCompareRows['mrSOS'];
					$vis_mr_os_cC = $getCompareRows['mrCOS'];
					$vis_mr_os_aC = $getCompareRows['mrAOS'];
					$visionOSC = $getCompareRows['visionOS'];
					$glareOSC = $getCompareRows['glareOS'];
					$performedByOSC = $getCompareRows['performedByOS'];
					$dateOSC = $getCompareRows['dateOS'];
					$autoSelectOSC = $getCompareRows['autoSelectOS'];
					$iolMasterSelectOSC = $getCompareRows['iolMasterSelectOS'];
					$topographerSelectOSC = $getCompareRows['topographerSelectOS'];
					$k1IolMaster1OSC = $getCompareRows['k1IolMaster1OS'];
					$k1IolMaster2OSC = $getCompareRows['k1IolMaster2OS'];
					$k1Topographer1OSC = $getCompareRows['k1Topographer1OS'];
					$k1Topographer2OSC = $getCompareRows['k1Topographer2OS'];
					$k2IolMaster1OSC = $getCompareRows['k2IolMaster1OS'];
					$k2IolMaster2OSC = $getCompareRows['k2IolMaster2OS'];
					$k2Topographer1OSC = $getCompareRows['k2Topographer1OS'];
					$k2Topographer2OSC = $getCompareRows['k2Topographer2OS'];
					$cylIolMaster1OSC = $getCompareRows['cylIolMaster1OS'];
					$cylIolMaster2OSC = $getCompareRows['cylIolMaster2OS'];
					$cylTopographer1OSC = $getCompareRows['cylTopographer1OS'];
					$cylTopographer2OSC = $getCompareRows['cylTopographer2OS'];
					$aveIolMasterOSC = $getCompareRows['aveIolMasterOS'];
					$aveTopographerOSC = $getCompareRows['aveTopographerOS'];
					$contactLengthOSC = $getCompareRows['contactLengthOS'];
					$immersionLengthOSC = $getCompareRows['immersionLengthOS'];
					$iolMasterLengthOSC = $getCompareRows['iolMasterLengthOS'];
					$contactNotesOSC = $getCompareRows['contactNotesOS'];
					$immersionNotesOSC = $getCompareRows['immersionNotesOS'];
					$iolMasterNotesOSC = $getCompareRows['iolMasterNotesOS'];
					$powerIolOSC = $getCompareRows['powerIolOS'];
					$holladayOSC = $getCompareRows['holladayOS'];
					$srk_tOSC = $getCompareRows['srk_tOS'];
					$hofferOSC = $getCompareRows['hofferOS'];
					$iol1OSC = $getCompareRows['iol1OS'];
					$iol1PowerOSC = $getCompareRows['iol1PowerOS'];
					$iol1HolladayOSC = $getCompareRows['iol1HolladayOS'];
					$iol1srk_tOSC = $getCompareRows['iol1srk_tOS'];
					$iol1HofferOSC = $getCompareRows['iol1HofferOS'];
					$iol2OSC = $getCompareRows['iol2OS'];
					$iol2PowerOSC = $getCompareRows['iol2PowerOS'];
					$iol2HolladayOSC = $getCompareRows['iol2HolladayOS'];
					$iol2srk_tOSC = $getCompareRows['iol2srk_tOS'];
					$iol2HofferOSC = $getCompareRows['iol2HofferOS'];
					$iol3OSC = $getCompareRows['iol3OS'];
					$iol3PowerOSC = $getCompareRows['iol3PowerOS'];
					$iol3HolladayOSC = $getCompareRows['iol3HolladayOS'];
					$iol3srk_tOSC = $getCompareRows['iol3srk_tOS'];
					$iol3HofferOSC = $getCompareRows['iol3HofferOS'];
					$iol4OSC = $getCompareRows['iol4OS'];
					$iol4PowerOSC = $getCompareRows['iol4PowerOS'];
					$iol4HolladayOSC = $getCompareRows['iol4HolladayOS'];
					$iol4srk_tOSC = $getCompareRows['iol4srk_tOS'];
					$iol4HofferOSC = $getCompareRows['iol4HofferOS'];
					$cellCountOSC = $getCompareRows['cellCountOS'];
					$notesOSC = $getCompareRows['notesOS'];
					$pachymetryValOSC = $getCompareRows['pachymetryValOS'];
					$pachymetryCorrecOSC = $getCompareRows['pachymetryCorrecOS'];
					$cornealDiamOSC = $getCompareRows['cornealDiamOS'];
					$dominantEyeOSC = $getCompareRows['dominantEyeOS'];
					
					$acdODC=$getCompareRows['acdOD'];
					$acdOSC=$getCompareRows['acdOS'];
					$w2wODC=$getCompareRows['w2wOD'];
					$w2wOSC=$getCompareRows['w2wOS'];					
	
					$pupilSize1OSC = $getCompareRows['pupilSize1OS'];
					$pupilSize2OSC = $getCompareRows['pupilSize2OS'];
					$cataractOSC = $getCompareRows['cataractOS'];
					$astigmatismOSC = $getCompareRows['astigmatismOS'];
					$myopiaOSC = $getCompareRows['myopiaOS'];
					$selecedIOLsOSC = $getCompareRows['selecedIOLsOS'];
					$notesAssesmentPlansOSC = $getCompareRows['notesAssesmentPlansOS'];
					$lriOSC = $getCompareRows['lriOS'];
					$dlOSC = $getCompareRows['dlOS'];
					$synechiolysisOSC = $getCompareRows['synechiolysisOS'];
					$irishooksOSC = $getCompareRows['irishooksOS'];
					$trypanblueOSC = $getCompareRows['trypanblueOS'];
					$flomaxOSC = $getCompareRows['flomaxOS'];
					$cutsOSC = $getCompareRows['cutsOS'];
					$lengthOSC = $getCompareRows['lengthOS'];
					$lengthTypeOSC = $getCompareRows['lengthTypeOS'];
					$axisOSC = $getCompareRows['axisOS'];
					$superiorOSC = $getCompareRows['superiorOS'];
					$inferiorOSC = $getCompareRows['inferiorOS'];
					$nasalOSC = $getCompareRows['nasalOS'];
					$temporalOSC = $getCompareRows['temporalOS'];
					$STOSC = $getCompareRows['STOS'];
					$SNOSC = $getCompareRows['SNOS'];
					$ITOSC = $getCompareRows['ITOS'];
					$INOSC = $getCompareRows['INOS'];
					$signedByIdC = $getCompareRows['signedById'];
					$signatureC = $getCompareRows['signature'];
					$signedByOSIdC = $getCompareRows['signedByOSId'];
					$signatureOSC = $getCompareRows['signatureOS'];
					$performedIolOSC = $getCompareRows['performedIolOS'];
					$forum_procedureC = $getSurgicalRecordRow['forum_procedure'];
					
					/**/
					$k1Auto1ODC = $getCompareRows['k1Auto1OD'];
					$k1Auto2ODC = $getCompareRows['k1Auto2OD'];
	
					$k2Auto1ODC = $getCompareRows['k2Auto1OD'];
					$k2Auto2ODC = $getCompareRows['k2Auto2OD'];
	
					$k1Auto1OSC = $getCompareRows['k1Auto1OS'];
					$k1Auto2OSC = $getCompareRows['k1Auto2OS'];
	
					$k2Auto1OSC = $getCompareRows['k2Auto1OS'];
					$k2Auto2OSC = $getCompareRows['k2Auto2OS'];
					
					$cylAuto1ODC = $getCompareRows['cylAuto1OD'];
					$cylAuto2ODC = $getCompareRows['cylAuto2OD'];
					$aveAutoODC = $getCompareRows['aveAutoOD'];
					$cylAuto1OSC = $getCompareRows['cylAuto1OS'];
					$cylAuto2OSC = $getCompareRows['cylAuto2OS'];
					$aveAutoOSC = $getCompareRows['aveAutoOS'];
					
					/**/
					
				}else{
					//To COMPARE MR AND AK FROM CHART VISION
						get_ak_values($patient_id, $form_id);						
					//To COMPARE MR AND AK FROM CHART VISION
				}
			}
	}else{
		$flgGetMRVals=0;$flgGetKVals=0;$ar_cn_vals=array();
		$elem_edMode="new";
		$ascanSurgical = 'False';
		$classText = 'backgroundLightGray';

		//get values from  chart note form Id in session
		if(!empty($form_id)){
			$getAKValuesStr = "SELECT
							c2.k_od AS vis_ak_od_k, c2.slash_od AS vis_ak_od_slash, c2.x_od AS vis_ak_od_x, c2.k_os AS vis_ak_os_k, 
							c2.slash_os AS vis_ak_os_slash, c2.x_os AS vis_ak_os_x, c2.k_type AS vis_ktype 
							FROM chart_vis_master c1
							INNER JOIN chart_ak c2 ON c2.id_chart_vis_master = c1.id	
							WHERE c1.patient_id = '$patient_id'
							AND c1.form_id = '$form_id'
							ORDER BY c1.form_id DESC LIMIT 0, 1
						";					
			$getAKValuesQry = imw_query($getAKValuesStr);
			$getNewChartNoteRow = imw_fetch_array($getAKValuesQry);
			$countAKRows = imw_num_rows($getAKValuesQry);
		}else{
			$countAKRows = 0 ;
		}
		if($countAKRows > 0){
			//get MR based on formId
			$getNewChartNoteRow = getCN_MRVals($patient_id,$form_id);	
		
			if(!empty($getNewChartNoteRow['vis_mr_od_s'])||!empty($getNewChartNoteRow['vis_mr_od_c'])||!empty($getNewChartNoteRow['vis_mr_od_a'])||
				!empty($getNewChartNoteRow['vis_mr_os_s'])||!empty($getNewChartNoteRow['vis_mr_os_c'])||!empty($getNewChartNoteRow['vis_mr_os_a'])				
			 ){
			//	OD
			$vis_mr_od_s = $getNewChartNoteRow['vis_mr_od_s'];
			$vis_mr_od_c = $getNewChartNoteRow['vis_mr_od_c'];
			$vis_mr_od_a = $getNewChartNoteRow['vis_mr_od_a'];
			$mrCOD = $getNewChartNoteRow['mrCOD'];
			$visionOD = $getNewChartNoteRow['vis_mr_od_txt_1'];
			$glareOD = $getNewChartNoteRow['vis_mr_od_txt_2'];
			
			//	OS
			$vis_mr_os_s = $getNewChartNoteRow['vis_mr_os_s'];
			$vis_mr_os_c = $getNewChartNoteRow['vis_mr_os_c'];
			$vis_mr_os_a = $getNewChartNoteRow['vis_mr_os_a'];
			$visionOS = $getNewChartNoteRow['vis_mr_os_txt_1'];
			$glareOS = $getNewChartNoteRow['vis_mr_os_txt_2'];			
			
			$flgGetMRVals=1;
			
			$ar_cn_vals["mr"]["od"]=$vis_mr_od_s."~".$vis_mr_od_c."~".$vis_mr_od_a."~".$visionOD."~".$glareOD;
			$ar_cn_vals["mr"]["os"]=$vis_mr_os_s."~".$vis_mr_os_c."~".$vis_mr_os_a."~".$visionOS."~".$glareOS;
			
			/*
			//Save for CN values
			$str_cn_vals_mr=$vis_mr_od_s."~".$vis_mr_od_c."~".$vis_mr_od_a."~".$visionOD."~".$glareOD."~".$vis_ak_od_k."~".$vis_ak_od_x."~".$vis_ak_od_slash."~".
						$vis_mr_os_s."~".$vis_mr_os_c."~".$vis_mr_os_a."~".$visionOS."~".$glareOS."~".$vis_ak_os_k."~".$vis_ak_os_x."~".$vis_ak_os_slash; 
			*/			
			}
			
			if(	!empty($getNewChartNoteRow['vis_ak_od_k'])||!empty($getNewChartNoteRow['vis_ak_od_x']) ||
				!empty($getNewChartNoteRow['vis_ak_os_k'])||!empty($getNewChartNoteRow['vis_ak_os_x'])
			 ){
				//OD
				$vis_ak_od_k = $getNewChartNoteRow['vis_ak_od_k'];
				$vis_ak_od_x = $getNewChartNoteRow['vis_ak_od_x'];
				$vis_ak_od_slash = $getNewChartNoteRow['vis_ak_od_slash'];
				
				//OS
				$vis_ak_os_k = $getNewChartNoteRow['vis_ak_os_k'];
				$vis_ak_os_x = $getNewChartNoteRow['vis_ak_os_x'];
				$vis_ak_os_slash = $getNewChartNoteRow['vis_ak_os_slash'];
				$vis_ktype = $getNewChartNoteRow["vis_ktype"];
				
				$flgGetKVals=1;
				$ar_cn_vals["k"]["od"]=$vis_ak_od_k."~".$vis_ak_od_x."~".$vis_ak_od_slash;
				$ar_cn_vals["k"]["os"]=$vis_ak_os_k."~".$vis_ak_os_x."~".$vis_ak_os_slash;
				
			 }
		}
	
		//get values from active chart note
		if($flgGetKVals==0){
			$getNewChartNoteRow = getCN_AKVals($patient_id,0,1); 				
			if(	empty($getNewChartNoteRow['vis_ak_od_k'])&&empty($getNewChartNoteRow['vis_ak_od_x']) &&
				empty($getNewChartNoteRow['vis_ak_os_k'])&&empty($getNewChartNoteRow['vis_ak_os_x'])
			 ){
				$getNewChartNoteRow = getCN_AKVals($patient_id); //get values from previous chart note
			 }
			
			if(	!empty($getNewChartNoteRow['vis_ak_od_k'])||!empty($getNewChartNoteRow['vis_ak_od_x']) ||
				!empty($getNewChartNoteRow['vis_ak_os_k'])||!empty($getNewChartNoteRow['vis_ak_os_x'])
			 ){
				//	OD					
				$vis_ak_od_k = $getNewChartNoteRow['vis_ak_od_k'];
				$vis_ak_od_x = $getNewChartNoteRow['vis_ak_od_x'];
				$vis_ak_od_slash = $getNewChartNoteRow['vis_ak_od_slash'];
				//	OS					
				$vis_ak_os_k = $getNewChartNoteRow['vis_ak_os_k'];
				$vis_ak_os_x = $getNewChartNoteRow['vis_ak_os_x'];
				$vis_ak_os_slash = $getNewChartNoteRow['vis_ak_os_slash'];
				$vis_ktype = $getNewChartNoteRow['vis_ktype'];
				$flgGetKVals=1;
				
				$ar_cn_vals["k"]["od"]=$vis_ak_od_k."~".$vis_ak_od_x."~".$vis_ak_od_slash;
				$ar_cn_vals["k"]["os"]=$vis_ak_os_k."~".$vis_ak_os_x."~".$vis_ak_os_slash;
			}
		}
		
		if($flgGetMRVals==0){	
			//MR
			$getNewChartNoteRow = getCN_MRVals($patient_id,0,1);
			if(empty($getNewChartNoteRow['vis_mr_od_s'])&&empty($getNewChartNoteRow['vis_mr_od_c'])&&empty($getNewChartNoteRow['vis_mr_od_a'])&&
				empty($getNewChartNoteRow['vis_mr_os_s'])&&empty($getNewChartNoteRow['vis_mr_os_c'])&&empty($getNewChartNoteRow['vis_mr_os_a'])				
			 ){
				$getNewChartNoteRow = getCN_MRVals($patient_id); //get values from previous chart note
			}
	
			if(!empty($getNewChartNoteRow['vis_mr_od_s'])||!empty($getNewChartNoteRow['vis_mr_od_c'])||!empty($getNewChartNoteRow['vis_mr_od_a'])||
				!empty($getNewChartNoteRow['vis_mr_os_s'])||!empty($getNewChartNoteRow['vis_mr_os_c'])||!empty($getNewChartNoteRow['vis_mr_os_a'])				
			 ){
			//	OD
			$vis_mr_od_s = $getNewChartNoteRow['vis_mr_od_s'];
			$vis_mr_od_c = $getNewChartNoteRow['vis_mr_od_c'];
			$vis_mr_od_a = $getNewChartNoteRow['vis_mr_od_a'];
			$mrCOD = $getNewChartNoteRow['mrCOD'];
			$visionOD = $getNewChartNoteRow['vis_mr_od_txt_1'];
			$glareOD = $getNewChartNoteRow['vis_mr_od_txt_2'];
			//	OS
			$vis_mr_os_s = $getNewChartNoteRow['vis_mr_os_s'];
			$vis_mr_os_c = $getNewChartNoteRow['vis_mr_os_c'];
			$vis_mr_os_a = $getNewChartNoteRow['vis_mr_os_a'];
			$visionOS = $getNewChartNoteRow['vis_mr_os_txt_1'];
			$glareOS = $getNewChartNoteRow['vis_mr_os_txt_2'];					
			$flgGetMRVals=1;
			
			$ar_cn_vals["mr"]["od"]=$vis_mr_od_s."~".$vis_mr_od_c."~".$vis_mr_od_a."~".$visionOD."~".$glareOD;
			$ar_cn_vals["mr"]["os"]=$vis_mr_os_s."~".$vis_mr_os_c."~".$vis_mr_os_a."~".$visionOS."~".$glareOS;
			}
		}		
		
		//get values from Previous Test
		if($flgGetMRVals==0&&$flgGetKVals==0){
				$getSurgicalRecordStr = "SELECT * FROM surgical_tbl
										WHERE patient_id = '$patient_id'
										ORDER BY examDate DESC, form_id DESC, surgical_id DESC limit 0, 1";
				$getSurgicalRecordQry = imw_query($getSurgicalRecordStr);
				$countRows = imw_num_rows($getSurgicalRecordQry);
				if($countRows>0){
					$boxColor = 'Blue';
					$getSurgicalRecordRow = imw_fetch_array($getSurgicalRecordQry);
	
						//extract($getSurgicalRecordRow); //RR Currupting formId and surgical id
	
						//MR AND AK VALUES OD
						$vis_mr_od_s = $getSurgicalRecordRow['mrSOD'];
							$vis_mr_od_sC = $vis_mr_od_s;
						$vis_mr_od_c = $getSurgicalRecordRow['mrCOD'];
							$vis_mr_od_cC = $vis_mr_od_c;
						$vis_mr_od_a = $getSurgicalRecordRow['mrAOD'];
							$vis_mr_od_aC = $vis_mr_od_a;
						$vis_ak_od_k = $getSurgicalRecordRow['k1Auto1OD'];
							$vis_ak_od_kC = $vis_ak_od_k;
						$vis_ak_od_x = $getSurgicalRecordRow['k1Auto2OD'];
							$vis_ak_od_xC = $vis_ak_od_x;
						$vis_ak_od_slash = $getSurgicalRecordRow['k2Auto1OD'];
							$vis_ak_od_slashC = $vis_ak_od_slash;
						$k2Auto2OD = $getSurgicalRecordRow['k2Auto2OD'];
							$k2Auto2ODC = $k2Auto2OD;
	
						$authUserId = $getSurgicalRecordRow['performedByOD'];
						$autoSelectOD = $getSurgicalRecordRow['autoSelectOD'];
							$autoSelectOD = getKheadingName($autoSelectOD);
						$iolMasterSelectOD = $getSurgicalRecordRow['iolMasterSelectOD'];
							$iolMasterSelectOD = getKheadingName($iolMasterSelectOD);
						$topographerSelectOD = $getSurgicalRecordRow['topographerSelectOD'];
							$topographerSelectOD = getKheadingName($topographerSelectOD);
						$powerIolOD = $getSurgicalRecordRow['powerIolOD'];
							$powerIolOD = getFormulaHeadName($powerIolOD);
						$holladayOD = $getSurgicalRecordRow['holladayOD'];
							$holladayOD = getFormulaHeadName($holladayOD);
						$srk_tOD = $getSurgicalRecordRow['srk_tOD'];
							$srk_tOD = getFormulaHeadName($srk_tOD);
						$hofferOD = $getSurgicalRecordRow['hofferOD'];
							$hofferOD = getFormulaHeadName($hofferOD);
						$iol1OD = $getSurgicalRecordRow['iol1OD'];
						if($iol1OD!=''){
							$providerLensesArrOD[0] = getLenseName($iol1OD);
							$providerLensesODArr[$iol1OD] = getLenseName($iol1OD);
						}
						$iol2OD = $getSurgicalRecordRow['iol2OD'];
						if($iol2OD!=''){
							$providerLensesArrOD[1] = getLenseName($iol2OD);
							$providerLensesODArr[$iol2OD] = getLenseName($iol2OD);
						}
	
						$iol3OD = $getSurgicalRecordRow['iol3OD'];
						if($iol3OD!=''){
							$providerLensesArrOD[2] = getLenseName($iol3OD);
							$providerLensesODArr[$iol3OD] = getLenseName($iol3OD);
						}
	
						$iol4OD = $getSurgicalRecordRow['iol4OD'];
						if($iol4OD!=''){
							$providerLensesArrOD[3] = getLenseName($iol4OD);
							$providerLensesODArr[$iol4OD] = getLenseName($iol4OD);
						}
	
						//MR AND AK VALUES OS
						$vis_mr_os_s = $getSurgicalRecordRow['mrSOS'];
							$vis_mr_os_sC = $vis_mr_os_s;
						$vis_mr_os_c = $getSurgicalRecordRow['mrCOS'];
							$vis_mr_os_cC = $vis_mr_os_c;
						$vis_mr_os_a = $getSurgicalRecordRow['mrAOS'];
							$vis_mr_os_aC = $vis_mr_os_a;
						$vis_ak_os_k = $getSurgicalRecordRow['k1Auto1OS'];
							$vis_ak_os_kC = $vis_ak_os_k;
						$vis_ak_os_x = $getSurgicalRecordRow['k1Auto2OS'];
							$vis_ak_os_xC = $vis_ak_os_x;
						$vis_ak_os_slash = $getSurgicalRecordRow['k2Auto1OS'];
							$vis_ak_os_slashC = $vis_ak_os_slash;
						$k2Auto2OS = $getSurgicalRecordRow['k2Auto2OS'];
							$k2Auto2OSC = $k2Auto2OS;
	
						$autoSelectOS = $getSurgicalRecordRow['autoSelectOS'];
							$autoSelectOS = getKheadingName($autoSelectOS);
						$iolMasterSelectOS = $getSurgicalRecordRow['iolMasterSelectOS'];
							$iolMasterSelectOS = getKheadingName($iolMasterSelectOS);
						$topographerSelectOS = $getSurgicalRecordRow['topographerSelectOS'];
							$topographerSelectOS = getKheadingName($topographerSelectOS);
						$k1IolMaster1OS = $getSurgicalRecordRow['k1IolMaster1OS'];
						$k1IolMaster2OS = $getSurgicalRecordRow['k1IolMaster2OS'];
						$k1Topographer1OS = $getSurgicalRecordRow['k1Topographer1OS'];
						$k1Topographer2OS = $getSurgicalRecordRow['k1Topographer2OS'];
						$k2IolMaster1OS = $getSurgicalRecordRow['k2IolMaster1OS'];
						$k2IolMaster2OS = $getSurgicalRecordRow['k2IolMaster2OS'];
						$k2Topographer1OS = $getSurgicalRecordRow['k2Topographer1OS'];
						$k2Topographer2OS = $getSurgicalRecordRow['k2Topographer2OS'];
						$cylIolMaster1OS = $getSurgicalRecordRow['cylIolMaster1OS'];
						$cylIolMaster2OS = $getSurgicalRecordRow['cylIolMaster2OS'];
						$cylTopographer1OS = $getSurgicalRecordRow['cylTopographer1OS'];
						$cylTopographer2OS = $getSurgicalRecordRow['cylTopographer2OS'];
						$aveIolMasterOS = $getSurgicalRecordRow['aveIolMasterOS'];
						$aveTopographerOS = $getSurgicalRecordRow['aveTopographerOS'];
						$contactLengthOS = $getSurgicalRecordRow['contactLengthOS'];
						$immersionLengthOS = $getSurgicalRecordRow['immersionLengthOS'];
						$iolMasterLengthOS = $getSurgicalRecordRow['iolMasterLengthOS'];
						$contactNotesOS = $getSurgicalRecordRow['contactNotesOS'];
						$immersionNotesOS = $getSurgicalRecordRow['immersionNotesOS'];
						$iolMasterNotesOS = $getSurgicalRecordRow['iolMasterNotesOS'];
						$powerIolOS = $getSurgicalRecordRow['powerIolOS'];
							$powerIolOS = getFormulaHeadName($powerIolOS);
						$holladayOS = $getSurgicalRecordRow['holladayOS'];
							$holladayOS = getFormulaHeadName($holladayOS);
						$srk_tOS = $getSurgicalRecordRow['srk_tOS'];
							$srk_tOS = getFormulaHeadName($srk_tOS);
						$hofferOS = $getSurgicalRecordRow['hofferOS'];
							$hofferOS = getFormulaHeadName($hofferOS);
						$iol1OS = $getSurgicalRecordRow['iol1OS'];
						if($iol1OS!=''){
							$providerLensesArrOS[0] = getLenseName($iol1OS);
							$providerLensesOSArr[$iol1OS] = getLenseName($iol1OS);
						}
	
						$iol1PowerOS = $getSurgicalRecordRow['iol1PowerOS'];
						$iol1HolladayOS = $getSurgicalRecordRow['iol1HolladayOS'];
						$iol1srk_tOS = $getSurgicalRecordRow['iol1srk_tOS'];
						$iol1HofferOS = $getSurgicalRecordRow['iol1HofferOS'];
						$iol2OS = $getSurgicalRecordRow['iol2OS'];
						if($iol2OS!=''){
							$providerLensesArrOS[1] = getLenseName($iol2OS);
							$providerLensesOSArr[$iol2OS] = getLenseName($iol2OS);
						}
	
						$iol2PowerOS = $getSurgicalRecordRow['iol2PowerOS'];
						$iol2HolladayOS = $getSurgicalRecordRow['iol2HolladayOS'];
						$iol2srk_tOS = $getSurgicalRecordRow['iol2srk_tOS'];
						$iol2HofferOS = $getSurgicalRecordRow['iol2HofferOS'];
						$iol3OS = $getSurgicalRecordRow['iol3OS'];
						if($iol3OS!=''){
							$providerLensesArrOS[2] = getLenseName($iol3OS);
							$providerLensesOSArr[$iol3OS] = getLenseName($iol3OS);
						}
	
						$iol3PowerOS = $getSurgicalRecordRow['iol3PowerOS'];
						$iol3HolladayOS = $getSurgicalRecordRow['iol3HolladayOS'];
						$iol3srk_tOS = $getSurgicalRecordRow['iol3srk_tOS'];
						$iol3HofferOS = $getSurgicalRecordRow['iol3HofferOS'];
						$iol4OS = $getSurgicalRecordRow['iol4OS'];
						if($iol4OS!=''){
							$providerLensesArrOS[3] = getLenseName($iol4OS);
							$providerLensesOSArr[$iol4OS] = getLenseName($iol4OS);
						}
	
						$iol4PowerOS = $getSurgicalRecordRow['iol4PowerOS'];
						$iol4HolladayOS = $getSurgicalRecordRow['iol4HolladayOS'];
						$iol4srk_tOS = $getSurgicalRecordRow['iol4srk_tOS'];
						$iol4HofferOS = $getSurgicalRecordRow['iol4HofferOS'];
						$cellCountOS = $getSurgicalRecordRow['cellCountOS'];
						$notesOS = $getSurgicalRecordRow['notesOS'];
						/*
						$pachymetryValOD = $getSurgicalRecordRow['pachymetryValOD'];
						$pachymetryCorrecOD = $getSurgicalRecordRow['pachymetryCorrecOD'];
						$pachymetryValOS = $getSurgicalRecordRow['pachymetryValOS'];
						$pachymetryCorrecOS = $getSurgicalRecordRow['pachymetryCorrecOS'];
						*/
						$cornealDiamOS = $getSurgicalRecordRow['cornealDiamOS'];
						$dominantEyeOS = $getSurgicalRecordRow['dominantEyeOS'];
						
	
						$acdOD=$getSurgicalRecordRow['acdOD'];
						$acdOS=$getSurgicalRecordRow['acdOS'];
						$w2wOD=$getSurgicalRecordRow['w2wOD'];
						$w2wOS=$getSurgicalRecordRow['w2wOS'];
	
						$pupilSize1OS = $getSurgicalRecordRow['pupilSize1OS'];
						$pupilSize2OS = $getSurgicalRecordRow['pupilSize2OS'];
						$cataractOS = $getSurgicalRecordRow['cataractOS'];
						$astigmatismOS = $getSurgicalRecordRow['astigmatismOS'];
						$myopiaOS = $getSurgicalRecordRow['myopiaOS'];
						$selecedIOLsOS = $getSurgicalRecordRow['selecedIOLsOS'];
						$notesAssesmentPlansOS = $getSurgicalRecordRow['notesAssesmentPlansOS'];
						$lriOS = $getSurgicalRecordRow['lriOS'];
						$dlOS = $getSurgicalRecordRow['dlOS'];
						$synechiolysisOS = $getSurgicalRecordRow['synechiolysisOS'];
						$irishooksOS = $getSurgicalRecordRow['irishooksOS'];
						$trypanblueOS = $getSurgicalRecordRow['trypanblueOS'];
						$flomaxOS = $getSurgicalRecordRow['flomaxOS'];
						$cutsOS = $getSurgicalRecordRow['cutsOS'];
						$lengthOS = $getSurgicalRecordRow['lengthOS'];
						$lengthTypeOS = $getSurgicalRecordRow['lengthTypeOS'];
						$axisOS = $getSurgicalRecordRow['axisOS'];
						$superiorOS = $getSurgicalRecordRow['superiorOS'];
						$inferiorOS = $getSurgicalRecordRow['inferiorOS'];
						$nasalOS = $getSurgicalRecordRow['nasalOS'];
						$temporalOS = $getSurgicalRecordRow['temporalOS'];
						$STOS = $getSurgicalRecordRow['STOS'];
						$SNOS = $getSurgicalRecordRow['SNOS'];
						$ITOS = $getSurgicalRecordRow['ITOS'];
						$INOS = $getSurgicalRecordRow['INOS'];
						$signedById = $getSurgicalRecordRow['signedById'];
						$signature = $getSurgicalRecordRow['signature'];
						$signedByOSId = $getSurgicalRecordRow['signedByOSId'];
						$signatureOS = $getSurgicalRecordRow['signatureOS'];
						//$performedIolOS = $getSurgicalRecordRow['performedIolOS'];
						//FOR COMPARE MR AND AUTO K READINGS ONLY
						$forum_procedure = $getSurgicalRecordRow['forum_procedure'];
						
						/* - OD - */
						$k1IolMaster1OD = $getSurgicalRecordRow['k1IolMaster1OD'];
						$k1IolMaster2OD = $getSurgicalRecordRow['k1IolMaster2OD'];
						$k1Topographer1OD = $getSurgicalRecordRow['k1Topographer1OD'];
						$k1Topographer2OD = $getSurgicalRecordRow['k1Topographer2OD'];
						$k2IolMaster1OD = $getSurgicalRecordRow['k2IolMaster1OD'];
						$k2IolMaster2OD = $getSurgicalRecordRow['k2IolMaster2OD'];
						$k2Topographer1OD = $getSurgicalRecordRow['k2Topographer1OD'];
						$k2Topographer2OD = $getSurgicalRecordRow['k2Topographer2OD'];
						$cylIolMaster1OD = $getSurgicalRecordRow['cylIolMaster1OD'];
						$cylIolMaster2OD = $getSurgicalRecordRow['cylIolMaster2OD'];
						$cylTopographer1OD = $getSurgicalRecordRow['cylTopographer1OD'];
						$cylTopographer2OD = $getSurgicalRecordRow['cylTopographer2OD'];
						$aveIolMasterOD = $getSurgicalRecordRow['aveIolMasterOD'];
						$aveTopographerOD = $getSurgicalRecordRow['aveTopographerOD'];
						
						$contactLengthOD = $getSurgicalRecordRow['contactLengthOD'];
						$immersionLengthOD = $getSurgicalRecordRow['immersionLengthOD'];
						$iolMasterLengthOD = $getSurgicalRecordRow['iolMasterLengthOD'];
						$contactNotesOD = $getSurgicalRecordRow['contactNotesOD'];
						$immersionNotesOD = $getSurgicalRecordRow['immersionNotesOD'];
						$iolMasterNotesOD = $getSurgicalRecordRow['iolMasterNotesOD'];
						
						$iol1PowerOD = $getSurgicalRecordRow['iol1PowerOD'];
						$iol1HolladayOD = $getSurgicalRecordRow['iol1HolladayOD'];
						$iol1srk_tOD = $getSurgicalRecordRow['iol1srk_tOD'];
						$iol1HofferOD = $getSurgicalRecordRow['iol1HofferOD'];
						
						$iol2PowerOD = $getSurgicalRecordRow['iol2PowerOD'];
						$iol2HolladayOD = $getSurgicalRecordRow['iol2HolladayOD'];
						$iol2srk_tOD = $getSurgicalRecordRow['iol2srk_tOD'];
						$iol2HofferOD = $getSurgicalRecordRow['iol2HofferOD'];
						
						$iol3PowerOD = $getSurgicalRecordRow['iol3PowerOD'];
						$iol3HolladayOD = $getSurgicalRecordRow['iol3HolladayOD'];
						$iol3srk_tOD = $getSurgicalRecordRow['iol3srk_tOD'];
						$iol3HofferOD = $getSurgicalRecordRow['iol3HofferOD'];
						
						$iol4PowerOD = $getSurgicalRecordRow['iol4PowerOD'];
						$iol4HolladayOD = $getSurgicalRecordRow['iol4HolladayOD'];
						$iol4srk_tOD = $getSurgicalRecordRow['iol4srk_tOD'];
						$iol4HofferOD = $getSurgicalRecordRow['iol4HofferOD'];
						
						$cellCountOD = $getSurgicalRecordRow['cellCountOD'];
						$notesOD = $getSurgicalRecordRow['notesOD'];
						$cornealDiamOD = $getSurgicalRecordRow['cornealDiamOD'];
						$dominantEyeOD = $getSurgicalRecordRow['dominantEyeOD'];
						$pupilSize1OD = $getSurgicalRecordRow['pupilSize1OD'];
						$pupilSize2OD = $getSurgicalRecordRow['pupilSize2OD'];
						
						//
						$cataractOD = $getSurgicalRecordRow['cataractOD'];
						$astigmatismOD = $getSurgicalRecordRow['astigmatismOD'];
						$myopiaOD = $getSurgicalRecordRow['myopiaOD'];
						$selecedIOLsOD = $getSurgicalRecordRow['selecedIOLsOD'];
						$notesAssesmentPlansOD = $getSurgicalRecordRow['notesAssesmentPlansOD'];
						$lriOD = $getSurgicalRecordRow['lriOD'];
						$dlOD = $getSurgicalRecordRow['dlOD'];
						$synechiolysisOD = $getSurgicalRecordRow['synechiolysisOD'];
						$irishooksOD = $getSurgicalRecordRow['irishooksOD'];
						$trypanblueOD = $getSurgicalRecordRow['trypanblueOD'];
						$flomaxOD = $getSurgicalRecordRow['flomaxOD'];
						$cutsOD = $getSurgicalRecordRow['cutsOD'];
						$lengthOD = $getSurgicalRecordRow['lengthOD'];
						$lengthTypeOD = $getSurgicalRecordRow['lengthTypeOD'];
						$axisOD = $getSurgicalRecordRow['axisOD'];
						$superiorOD = $getSurgicalRecordRow['superiorOD'];
						$inferiorOD = $getSurgicalRecordRow['inferiorOD'];
						$nasalOD = $getSurgicalRecordRow['nasalOD'];
						$temporalOD = $getSurgicalRecordRow['temporalOD'];
						$STOD = $getSurgicalRecordRow['STOD'];
						$SNOD = $getSurgicalRecordRow['SNOD'];
						$ITOD = $getSurgicalRecordRow['ITOD'];
						$INOD = $getSurgicalRecordRow['INOD'];
						//
						
						//Ag 09 changes --
	
						$sur_dateOD = $getSurgicalRecordRow["sur_dt_od"];
						$elem_proc_od = $getSurgicalRecordRow["proc_od"];
						$elem_anes_od = $getSurgicalRecordRow["anes_od"];
						$elem_visc_od = $getSurgicalRecordRow["visc_od"];
						$elem_visc_od_other = $getSurgicalRecordRow["visc_od_other"];
						$elem_opts_od = $getSurgicalRecordRow["opts_od"];
						$elem_opts_od_other = $getSurgicalRecordRow["opts_od_other"];
						$elem_iol2dn_od = $getSurgicalRecordRow["iol2dn_od"];
	
						$sur_dateOS = $getSurgicalRecordRow["sur_dt_os"];
						$elem_proc_os = $getSurgicalRecordRow["proc_os"];
						$elem_anes_os = $getSurgicalRecordRow["anes_os"];
						$elem_visc_os = $getSurgicalRecordRow["visc_os"];
						$elem_visc_os_other = $getSurgicalRecordRow["visc_os_other"];
						$elem_opts_os = $getSurgicalRecordRow["opts_os"];
						$elem_opts_os_other = $getSurgicalRecordRow["opts_os_other"];
						$elem_iol2dn_os = $getSurgicalRecordRow["iol2dn_os"];
	
						//Ag 09 changes --
						
						$visionOD = $getSurgicalRecordRow['visionOD']; $visionODC = $visionOD;
						$glareOD = $getSurgicalRecordRow['glareOD'];	$glareODC = $glareOD;
						$visionOS= $getSurgicalRecordRow['visionOS'];  $visionOSC = $visionOS;
						$glareOS = $getSurgicalRecordRow['glareOS'];	$glareOSC = $glareOS;
						
						/* - OD - */
						
						//Save for CN values
						$str_cn_vals=test_getCN_StrVals($patient_id);
				}
		}else{
			//make string for cn vals
			$str_cn_vals="";
			if(!isset($ar_cn_vals["mr"]["od"])){ $ar_cn_vals["mr"]["od"]="~"."~"."~"."~";  }
			if(!isset($ar_cn_vals["k"]["od"])){ $ar_cn_vals["k"]["od"]="~"."~"; }
			if(!isset($ar_cn_vals["mr"]["os"])){ $ar_cn_vals["mr"]["os"]="~"."~"."~"."~";  }
			if(!isset($ar_cn_vals["k"]["os"])){ $ar_cn_vals["k"]["os"]="~"."~";  }
			$str_cn_vals=$ar_cn_vals["mr"]["od"]."~".$ar_cn_vals["k"]["od"]."~".$ar_cn_vals["mr"]["os"]."~".$ar_cn_vals["k"]["os"];
		}
	}

}

if(!$autoSelectOD){	$autoSelectOD = ($vis_ktype=="Manual") ? 'K[Manual]' : 'K[Auto]' ; } 
if(!$iolMasterSelectOD){ $iolMasterSelectOD = 'K[IOL Master]'; }
if(!$topographerSelectOD){	$topographerSelectOD = 'K[Topographer]'; }
if(!$autoSelectOS){	$autoSelectOS = ($vis_ktype=="Manual") ? 'K[Manual]' : 'K[Auto]' ; }
if(!$iolMasterSelectOS){ $iolMasterSelectOS = 'K[IOL Master]'; }
if(!$topographerSelectOS){ $topographerSelectOS = 'K[Topographer]'; }

// Getting Values for Surgical Table ---------------------------
if($callFromInterface != 'admin'){
	//Compare values of work view with test, if changes are found than alert
	if($elem_edMode=="update"){
		
		$arr_tmp = array(
					"vis_mr_od_s"=>array($vis_mr_od_s, "mrSOD"),
					"vis_mr_od_c"=>array($vis_mr_od_c, "mrCOD"),
					"vis_mr_od_a"=>array($vis_mr_od_a, "mrAOD"),
					"vis_mr_od_txt_1"=>array($visionOD, "visionOD"),
					"vis_mr_od_txt_2"=>array($glareOD, "glareOD"),				
					"vis_ak_od_k"=>array($vis_ak_od_k, "k1Auto1OD"),
					"vis_ak_od_x"=>array($vis_ak_od_x, "k1Auto2OD"),
					"vis_ak_od_slash"=>array($vis_ak_od_slash, "k2Auto1OD"),				
					"vis_mr_os_s"=>array($vis_mr_os_s, "mrSOS"),
					"vis_mr_os_c"=>array($vis_mr_os_c, "mrCOS"),
					"vis_mr_os_a"=>array($vis_mr_os_a, "mrAOS"),
					"vis_mr_os_txt_1"=>array($visionOS, "visionOS"),
					"vis_mr_os_txt_2"=>array($glareOS, "glareOS"),
					"vis_ak_os_k"=>array($vis_ak_os_k, "k1Auto1OS"),
					"vis_ak_os_x"=>array($vis_ak_os_x, "k1Auto2OS"),
					"vis_ak_os_slash"=>array($vis_ak_os_slash, "k2Auto1OS")	);
		list($alert_user_prompt, $arr_new_cn_val,$str_cn_vals) = test_isCNValChanged($arr_tmp, $patient_id, $form_id, $str_cn_vals);
		//echo $alert_user_prompt;
		//exit();	
	}
	//----

	//check 20/ values
	$visionOD=test_check20slash($visionOD);
	$glareOD=test_check20slash($glareOD);
	$visionOS=test_check20slash($visionOS);
	$glareOS=test_check20slash($glareOS);
	
	$visionODC=test_check20slash($visionODC);
	$glareODC=test_check20slash($glareODC);
	$visionOSC=test_check20slash($visionOSC);
	$glareOSC=test_check20slash($glareOSC);
	//--

	//Super bill init() --
	$superLen = "1";
	$sb_testName = 'A/Scan';

	//Cpt Code Desc
	$thisCptDescSym = "A/Scan";
}

if($callFromInterface != 'admin'){
	if($surgical_id && $surgical_id > 0){
		$test_edid = $surgical_id;
	}else{
		$test_edid = 0;	
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title><?php echo $this_test_screen_name;?></title>
<link href="<?php echo $library_path; ?>/css/tests.css?<?php echo filemtime('../../library/css/tests.css');?>" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/common.css?<?php echo filemtime('../../library/css/common.css');?>" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/jquery-ui.min.css" rel="stylesheet">
<!-- Bootstrap -->
<link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<!-- Bootstrap Selctpicker CSS -->
<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
<!-- Messi Plugin for fancy alerts CSS -->
<!-- DateTime Picker CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
<link href="<?php echo $library_path; ?>/css/remove_checkbox.css" rel="stylesheet" type="text/css">
<?php if($callFromInterface != 'admin'){?>
	<link href="<?php echo $GLOBALS['webroot'];?>/library/css/workview.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/superbill.css" rel="stylesheet">
    <link href="<?php echo $GLOBALS['webroot'];?>/library/lightbox/lightbox.css" rel="stylesheet">
    <link href="<?php echo $library_path; ?>/css/epost.css" rel="stylesheet">
<?php }?>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]--> 

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery-ui.min.1.11.2.js"></script>
<!-- jQuery's Date Time Picker -->
<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
<!-- Bootstrap -->
<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>

<!-- Bootstrap Selectpicker -->
<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
<script src="<?php echo $library_path; ?>/js/jquery.mCustomScrollbar.concat.min.js"></script> 
<script src="<?php echo $library_path; ?>/js/common.js?<?php echo filemtime('../../library/js/common.js');?>" type="text/javascript"></script>
<script src="<?php echo $library_path; ?>/js/tests.js?<?php echo filemtime('../../library/js/tests.js');?>" type="text/javascript"></script>
<?php if($callFromInterface != 'admin'){?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/icd10_autocomplete.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/js_gen.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/typeahead.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/superbill.js?<?php echo filemtime('../../library/js/work_view/superbill.js');?>"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/lightbox/lightbox.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/epost.js"></script>
<?php }?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/correction.js"></script>
<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
<script type="text/javascript">
var imgPath 		= "<?php echo $GLOBALS['webroot'];?>";
var elem_per_vo 	= "<?php echo $elem_per_vo;?>";
var zPath			= "<?php echo $GLOBALS['rootdir'];?>";
var arrNewCnVal = <?php echo json_encode($arr_new_cn_val); ?>;
var JS_WEB_ROOT_PATH 	= "<?php echo $GLOBALS['webroot']; ?>";
<?php if($callFromInterface != 'admin'){?>
	//Test id
	var t_nxtid			= "<?php echo $tstNxtId;?>";
	var t_previd		= "<?php echo $tstPrevId;?>";
<?php }?>
<?php if($_GET['afterSaveinPopup']=='yes'){?>
	$(document).ready(function(e) {
		top.fAlert('Test has been saved.');
		<?php if($_GET['doNotShowRightSide']!='yes'){?>
		if(typeof(window.opener.top.fmain)!='undefined'){
			if(typeof(window.opener.top.fmain.$) != 'undefined' && window.opener.top.fmain.$("#sliderRight").length>0){
			window.opener.top.fmain.$("#sliderRight").attr("attrFilled",0);
			if(typeof(window.opener.top.fmain.showChartNotesTree)!='undefined'){
				window.opener.top.fmain.showChartNotesTree("1");
			}
			}
		}
		setTimeout(function(){window.close();},1500);
		<?php }?>
    });
<?php }?>

function alert_user_cn_changes(){
	var ans = confirm("MR/AK values in chart note are changed ! Do you want to fill chart note values? ");
	if(ans!=false){ for(var x in arrNewCnVal){	if(x && arrNewCnVal[x] && typeof(arrNewCnVal[x])!="undefined"&&arrNewCnVal[x]!="20/"){ $("#"+x).val(arrNewCnVal[x]).triggerHandler("blur");}}}
}

$(document).ready(function(e) {
	top.frm_submited=0;
	$("textarea").bind("focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){cn_ta1_no_assess(this, "");};});
	<?php if($callFromInterface != 'admin' && (!isset($_GET["tId"]) || trim($_GET["tId"]))==''){?>
		if(typeof(fillInterpretationProfileData)=='function' && typeof(document.forms[0].sel_interpretation_profile)!='undefined'){
			if(document.forms[0].sel_interpretation_profile.value!='' && document.forms[0].sel_interpretation_profile.value != '0')
			fillInterpretationProfileData(document.forms[0].sel_interpretation_profile.value);
		}
	<?php
	}?>
	
	<?php
		if($alert_user_prompt==1){
			echo " alert_user_cn_changes(); ";
		}
	?>
});

<?php if($callFromInterface != 'admin'){?>
// Prev Data
var oPrvDt;
/*function getPrevTestDt(ctid,cdt,ctm,dir){
	getPrevTestDtExe(ctid,cdt,ctm,dir,"LABS");
}*/

function printTest(){
	var tId = "<?php echo $_GET["tId"];?>";
	window.open('../../library/html_to_pdf/createPdf.php?op=l&onePage=false&file_location=<?php echo $html_file_name;?>&mergePDF=1&name=test_'+tId+'_pdf&testIds='+tId+'&saveOption=F&image_from=<?php echo $testname; ?>','ascan_Rpt','menubar=0,resizable=yes');	
}
<?php }?>
function selectOs(){
	var arrTestOd = new Array("elem_descOd","elem_inter_pret_od");
	var arrTestOs = new Array("elem_descOs","elem_inter_pret_os");
	var len = arrTestOd.length;
	for(var i=0;i<len;i++){
		var od = document.getElementById(arrTestOd[i]);
		var os = document.getElementById(arrTestOs[i]);
		if(od && os){
			if(od.type == "checkbox"){
				os.checked = od.checked;
				if(typeof os.onclick == "function"){
						os.onclick();
				}

			}else if((od.type == "text") || (od.type == "textarea")){
				os.value = od.value;

				if(typeof os.onchange == "function"){
						os.onchange();
				}
			}
		}
	}
}

<?php if($callFromInterface != 'admin'){?>
function b4saveAscan(){
	var err = true;
	var m = "<b>Please fill the following:-</b><br>";

	var ofrm= document.test_form_frm;

	if(ofrm.elem_examDate.value == ""){
		m += '&bull; DOS<br>';
		err = false;
	}

	if(ofrm.elem_opidTestOrdered.value==""){
		m += '&bull; Order By<br>';
		err = false;
	}

	//Super Bill
	var oSB = isSuperBillMade();
	var sb_dxids="";
	if(oSB.SBill == true){		
		
		if(oSB.DXCodeOK == false  ||  oSB.DXCodeAssocOK == false ){
			m += '&bull; Dx code in Super bill<br>';
			err = false;
		}
		
		if(oSB.DXCodeComplete==false){
			m += '&bull; Incomplete ICD-10 DX code(s) in Super bill<br>';
			err = true;
		}
		sb_dxids=oSB.dxids;
	}
	$("#sb_dxids").val(sb_dxids);
	if(err == false){
		fAlert(m,'','top.frm_submited=0;');
	}

	return err;
}

function saveAscan() {
	if(top.frm_submited==1){ return; }
	top.frm_submited=1;
	if(b4saveAscan()) {		
		document.test_form_frm.submit();	
	}
}
//Save Ascan ----------
<?php }?>

function changeMRBgColor(obj, val){
	var objVal = obj.value;
	if(objVal!=val){
		obj.style.background="#FFFFFF";
	}
}

function changeMRBgColor(obj, val){
	var objVal = obj.value;
	if(objVal!=val){
		obj.style.background="#FFFFFF";
	}
}

function chkFormat(obj, val){
	var objVal = document.getElementById(obj);
	changeMRBgColor(objVal, val);
	var objValue = document.getElementById(obj).value;
	if(objValue!=''){
		var slashPos = objValue.indexOf("/");
		if(slashPos!=2){
			fAlert("The format would be 20/xxxxx.");
			document.getElementById(obj).value = '';
			return false;
		}else{
			var valInitials = objValue.substr(objValue, 2, 2);
			if(valInitials!=20){
				fAlert("The format would be 20/xxxxx.");
				document.getElementById(obj).value = '';
				return false;
			}
		}
		var strlen = objValue.length;
		if((strlen>8) || (strlen<4)){
			fAlert("The format would be 20/xxxxx.");
			document.getElementById(obj).value = '';
			return false;
		}
	}
}

function calCorrectionVal(val,wh){
	//Remove Spaces
	var strVal = trim(val);
	//Check Value for numeric
	if(!isValid(strVal)){
		fAlert("Please enter comma separated Numeric values only.<br><br>(0123456789,)");
	}else{
		var arrReadings = new Array();
		arrReadings = val.split(",");
		//Length of Array
		var arrLength = arrReadings.length;
		var counter = 0;
		var pachyReading = 0;
		//Add all values
		for(i=0;i<arrLength;i++){
			if(trim(arrReadings[i]) != ""){
				pachyReading = parseInt(pachyReading) + parseInt(arrReadings[i]);
				counter += 1;
			}
		}
		//Get Avg
		var avgReading = parseInt(pachyReading)/parseInt(counter);
		avgReading = Math.round(avgReading);
		//Correction Value
		var correctionVal = getCorrectionValue(avgReading);

		// Set Values
		if(wh == "OD"){
			var objTextAvg = document.getElementById("pachymetryValOD");
			var objTextCor = document.getElementById("pachymetryCorrecOD");
			var objTextRead = document.getElementById("pachymetryValOD");
			//var objTextAvgLastFinalized = document.getElementById("elem_pachyOdAverage_lastfinalized");
			//var objTextCorLastFinalized = document.getElementById("elem_pachyOdCorrectionValue_lastfinalized");

		}else if(wh == "OS"){
			var objTextAvg = document.getElementById("pachymetryValOS");
			var objTextCor = document.getElementById("pachymetryCorrecOS");
			var objTextRead = document.getElementById("pachymetryValOS");
			//var objTextAvgLastFinalized = document.getElementById("elem_pachyOsAverage_lastfinalized");
			//var objTextCorLastFinalized = document.getElementById("elem_pachyOsCorrectionValue_lastfinalized");
		}
		if((correctionVal != "") && (typeof(correctionVal) != "undefined"))	{
			if(counter > 1)	{
				//
				objTextAvg.value = avgReading;
				objTextCor.value = correctionVal;
				//changeBgColor(objTextAvg);
				//objTextCor.onchange();
			}else{
				objTextAvg.value = avgReading; //avgReading;
				objTextCor.value = correctionVal;
				//changeBgColor(objTextAvg);
				//objTextCor.onchange();
			}
		}else{
			objTextRead.value = objTextRead.defaultValue;
			objTextCor.value = objTextCor.defaultValue;
			//objTextRead.onchange();
		}
	}
}

function listLenses(ele, obj){
	if((document.getElementById(obj).value!='') || (document.getElementById(obj).value!=0)){
		document.getElementById(obj).style.background="#FFFFFF";
	}
	var newLens = document.getElementById(obj).value;
	var newLensID = $('option:selected', $('#'+obj)).attr('value2');
	$('#selecedIOLsOD').append('<option value="'+newLensID+'">'+newLens+'</option>');
	return;
	var xmlHttp;
	try{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}catch (e){
		// Internet Explorer
		try{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}catch (e){
			try{
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}catch (e){
				fAlert("Your browser does not support AJAX!");
				return false;
			}
		}
	}

	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){
			var result = xmlHttp.responseText;
			var resultID=trim(result);
			document.getElementById("selecedIOLsOD").options[ele] = new Option(newLens, resultID);
		}
	}
	xmlHttp.open("GET","getIolForPhysician.php?newLens="+newLens,true);
	xmlHttp.send(null);
}
function listLenses1(ele, obj){
	if((document.getElementById(obj).value!='') || (document.getElementById(obj).value!=0)){
		document.getElementById(obj).style.background="#FFFFFF";
	}
	var newLens = document.getElementById(obj).value;
	var newLensID = $('option:selected', $('#'+obj)).attr('value2');
	$('#selecedIOLsOS').append('<option value="'+newLensID+'">'+newLens+'</option>');
	return;
	var xmlHttp;
	try{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}catch (e){
		// Internet Explorer
		try{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}catch (e){
			try{
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}catch (e){
				fAlert("Your browser does not support AJAX!");
				return false;
			}
		}
	}

	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4){
			var result = xmlHttp.responseText;
			var resultID=trim(result);
			document.getElementById("selecedIOLsOS").options[ele] = new Option(newLens, resultID);
		}
	}
	xmlHttp.open("GET","getIolForPhysician.php?newLens="+newLens,true);
	xmlHttp.send(null);
}
</script>
</head>
<body>
<form id="test_form_frm" name="test_form_frm" action="save_tests.php" method="post" onSubmit="return b4saveAscan()" style="margin:0px;">
<?php if($callFromInterface != 'admin'){?>
<input type="hidden" value="<?php echo $printId; ?>" name="printId">
<input type="hidden" value="" name="patient">
<input type="hidden" value="" name="findBy">
<input type="hidden" value="true" name="saveSurgical">
<input type="hidden" value="<?php if($finalizedIs == 'True'){ echo $surgical_id = ''; }else{ echo $surgical_id ; }?>" name="surgical_id">
<input type="hidden" value="<?php echo $form_id; ?>" name="form_id" >
<input type="hidden" name="wind_opn" id="wind_opn" value="0">
<input type="hidden" name="elem_examTime" value="<?php echo $elem_examTime;?>">
<!--<input type="hidden" name="elem_examDate" value="<?php echo $elem_examDate;?>">-->
<input type="hidden" name="elem_noP" value="<?php echo $noP;?>">
<input type="hidden" name="pop" value="<?php echo $_REQUEST['pop'] ?>">
<!--the hidden field doNotShowRightSide	is used for mest maneger-->
<input type="hidden" name="doNotShowRightSide" value="<?php echo $_REQUEST['doNotShowRightSide']; ?>">
<input type="hidden" name="hidFormLoaded" id="hidFormLoaded" value="0">
<!-- purge -->
<input type="hidden" name="elem_edMode" id="elem_edMode" value="<?php echo $elem_edMode;?>">
<input type="hidden" name="elem_saveForm" id="elem_saveForm" value="A-SCAN">
<input type="hidden" value="<?php echo $form_id; ?>" name="elem_formId" id="elem_formId">
<input type="hidden" name="elem_testId" id="elem_testId" value="<?php if($finalizedIs == 'True'){ echo $surgical_id = ''; }else{ echo $surgical_id ; }?>">
<input type="hidden" name="elem_patientId" id="elem_patientId" value="<?php echo $patient_id;?>">
<input type="hidden" name="elem_tests_name_id" id="elem_tests_name_id" value="<?php echo $test_master_id;?>">
<!-- purge -->
<input type="hidden" name="zeissAction" id="zeissAction" value="">
<input type="hidden" name="el_cn_mrk_val" id="el_cn_mrk_val" value="<?php echo $str_cn_vals; ?>">
<input type="hidden" name="elem_phyName_order" id="elem_phyName_order" value="<?php echo $authUserId_pro; ?>" data-phynm="<?php echo (!empty($authUserId_pro)) ? $objTests->getPersonnal3($authUserId_pro) : "" ;?>">
<?php $flg_interpreted_btn = ($userType==1 && empty($signedById)) ? 1 : 0; ?>
<?php }?>
<div class=" container-fluid">
    <div class="mainarea">
        <div class="row">
            <div class="col-sm-<?php if($callFromInterface != 'admin' && $doNotShowRightSide!='yes'){echo '10';}else{echo '12';}?>">
                <?php if($callFromInterface != 'admin'){ require_once("test_orderby_inc.php");}?>
                <div class="row">
                    <!--OD top -->
                    <div class="col-sm-6">
                        <div class="mrlft">
                            <div class="row">
                                <div class="col-sm-2">
	                                <span>MR</span>
                                </div>
                                <div class="col-sm-5">
                                    <div class="row">
                                        <div class="col-sm-4">
                                        	<div class="form-group">
                                            	<label for="">S</label>
	                                            <input type="text" tabindex="1" class="form-control <?php if($rowsCount>0 && $vis_mr_od_sC==$vis_mr_od_s){echo "backgroundLightGray";}else{echo $classText;} ?>" name="mrSOD" id="mrSOD" value="<?php echo $vis_mr_od_s; ?>" onBlur="changeMRBgColor(this, '<?php echo imw_real_escape_string($vis_mr_od_s); ?>');">
                                          	</div>
                                        </div>
                                        <div class="col-sm-4">
                                        	<div class="form-group">
                                                <label for="">C</label>
                                                <input type="text" tabindex="1" class="form-control <?php  if($rowsCount>0 && $vis_mr_od_cC==$vis_mr_od_c){echo "backgroundLightGray";}else{echo $classText;}?>" name="mrCOD" id="mrCOD" value="<?php echo $vis_mr_od_c; ?>" onBlur="changeMRBgColor(this, '<?php echo imw_real_escape_string($vis_mr_od_c); ?>');">
                                          	</div>
                                        </div>
                                        <div class="col-sm-4">
                                        	<div class="form-group">
                                            	<label for="">A</label>
                                            	<input type="text" tabindex="1" class="form-control <?php if($rowsCount>0 && $vis_mr_od_aC==$vis_mr_od_a){echo "backgroundLightGray";}else{echo $classText;}?>" name="mrAOD" id="mrAOD" value="<?php echo $vis_mr_od_a; ?>" onBlur="changeMRBgColor(this, '<?php echo imw_real_escape_string($vis_mr_od_a); ?>');">
                                          	</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <label for="">VISION</label>
                                    <div class="row">
                                        <div class="col-sm-6"><input type="text" tabindex="1" class="form-control <?php if($rowsCount>0 && $visionODC==$visionOD){echo "backgroundLightGray";}else{echo $classText;}?>" name="visionOD" id="visionOD" value="<?php echo $visionOD; ?>" onBlur="return chkFormat('visionOD', '<?php echo imw_real_escape_string($visionOD); ?>');"> Vision</div>
                                        <div class="col-sm-6"><input type="text" tabindex="1" class="form-control <?php if($rowsCount>0 && $glareODC==$glareOD){echo "backgroundLightGray";}else{echo $classText;}?>" name="glareOD" id="glareOD" value="<?php echo $glareOD; ?>" onBlur="return chkFormat('glareOD', '<?php echo imw_real_escape_string($glareOD); ?>');"> Glare</div>
                                    </div>
                                </div>
                            </div>
                			<div class="clearfix"></div>
                		</div>
                    	<div class="clearfix"></div>
                    	<div class="testsctbx">
                            <div class="tstthead">
                                <div class="row">
                                    <div class="col-sm-12  tstflt text-right form-inline">
                                        <figure class="odbox">OD</figure>
                                        <div class="form-group">
                                            <label for="">Performed By:</label>
                                            <select class="form-control minimal <?php if($rowsCount>0 && $authUserId==$authUserIdC){echo "text backgroundLightGray";}else{echo $classText;}?>"name="performedByOD" id="performedByOD" tabindex="1">
                                                <option value=""></option>
                                                <?php
                                                foreach($phyTechArray as $phyTechId => $phyTechName){
                                                    ?>
                                                    <option value="<?php echo $phyTechId; ?>" <?php $authUserId_tmp = (!empty($authUserId)) ? $authUserId : $authUserId_pro ; if($authUserId_tmp==$phyTechId) {echo "SELECTED";} ?>><?php echo $phyTechName; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="">Date</label>
                                            <div class="input-group">
                                                <input id="dateOD" name="dateOD" type="text" tabindex="1" value="<?php echo get_date_format($today,'mm-dd-yyyy'); ?>" onBlur="checkdate(this);" maxlength=10 class="form-control datePicker <?php if($rowsCount>0 && $dateODC!='' && $dateOD==$dateODC){echo "backgroundLightGray";}else{echo $classText;}?>">
                                                <div class="input-group-addon"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
            				<div class="clearfix"></div>
                            <div class="testsctbxpd">
                            	<div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-sm-3">&nbsp;</div>
                                    <div class="col-sm-3"><select tabindex="1" class="form-control minimal <?php if($rowsCount>0 && $autoSelectODC==$autoSelectOD){echo "backgroundLightGray";}else{echo $classText;}?>" id="menuMain4Text" name="autoSelectOD">
									  <?php foreach($kReadingArr as $k=>$v){
                                          $sel_autoSelectOD = '';
                                          if($v[2]==$autoSelectOD) $sel_autoSelectOD = ' selected';
                                          echo '<option value="'.$v[2].'"'.$sel_autoSelectOD.'>'.$v[0].'</option>';
                                      }
                                      ?>
                                    </select>
                                    </div>
                                    
                                    <div class="col-sm-3"><select tabindex="1" class="form-control minimal <?php if($rowsCount>0 && trim($iolMasterSelectOD)==trim($iolMasterSelectODC)){echo "backgroundLightGray";}else{echo $classText;}?>" name="iolMasterSelectOD" id="iolMasterSelectOD">
									  <?php foreach($kReadingArr as $k=>$v){
                                          $sel_iolMasterSelectOD = '';
                                          if($v[2]==$iolMasterSelectOD) $sel_iolMasterSelectOD = ' selected';
                                          echo '<option value="'.$v[2].'"'.$sel_iolMasterSelectOD.'>'.$v[0].'</option>';
                                      }
                                      ?>
                                    </select>
                                    </div>
                                    
                                    <div class="col-sm-3"><select tabindex="1" class="form-control minimal <?php if($rowsCount>0 && trim($iolMasterSelectOD)==trim($iolMasterSelectODC)){echo "backgroundLightGray";}else{echo $classText;}?>" name="topographerSelectOD">
									  <?php foreach($kReadingArr as $k=>$v){
                                          $sel_topographerSelectOD = '';
                                          if($v[2]==$topographerSelectOD) $sel_topographerSelectOD = ' selected';
                                          echo '<option value="'.$v[2].'"'.$sel_topographerSelectOD.'>'.$v[0].'</option>';
                                      }
                                      ?>
                                    </select>
                                    </div>
                                    <div class="clearfix"></div>
                                    
                                    <!-- K1 -->
                                    <?php
                                    if((!$surgical_id) && ($vis_ak_od_k!='')){
                                        if($vis_ak_od_k<=$vis_ak_od_slash){
                                            if($vis_ak_od_x<=90){
                                                $k2Auto2OD = $vis_ak_od_x;
                                                $vis_ak_od_x = $vis_ak_od_x + 90;
                                            }else{
                                                $k2Auto2OD = $vis_ak_od_x;
                                                $vis_ak_od_x = $vis_ak_od_x - 90;
                                            }
                                        }else{
                                            $tmp=$vis_ak_od_x+$k2Auto2OD;
                                            if($tmp<90){
                                                $k2Auto2OD = $tmp + 90;
                                            }else{
                                                $k2Auto2OD = $tmp - 90;
                                            }
                                        }
                                    }?>
                                    <div class="col-sm-3 text-right"><label>K1</label></div>
                                    <div class="col-sm-3 form-inline kloption">
                                        <div class="form-group"><input type="text" tabindex="1" class="form-control <?php if($rowsCount>0 && $vis_ak_od_k==$k1Auto1ODC) {echo "backgroundLightGray";}?>" name="k1Auto1OD" id="k1Auto1OD" value="<?php if($vis_ak_od_k!=0){ echo $vis_ak_od_k; } ?>"  onBlur="return calCulate('k1Auto1OD', 'k1Auto2OD', 'k2Auto1OD', 'k2Auto2OD', 'cylAuto1OD', 'cylAuto2OD', 'aveAutoOD');">
                                          <img src="<?php echo $library_path; ?>/images/close_small.png" alt=""/>
                                          <input type="text" tabindex="1" class="form-control  <?php if($rowsCount>0 && $k1Auto2OD==$k1Auto2ODC) {echo "backgroundLightGray";}?>" name="k1Auto2OD" id="k1Auto2OD" value="<?php if($vis_ak_od_x!=0){ echo $vis_ak_od_x; } ?>" onBlur="return calCulate('k1Auto1OD', 'k1Auto2OD', 'k2Auto1OD', 'k2Auto2OD', 'cylAuto1OD', 'cylAuto2OD', 'aveAutoOD');">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 form-inline kloption">
                                        <div class="form-group"><input type="text" tabindex="2"  class="form-control <?php if($rowsCount>0 && $k1IolMaster1OD==$k1IolMaster1ODC){echo "backgroundLightGray";}?>" name="k1IolMaster1OD" id="k1IolMaster1OD" value="<?php if(($k1IolMaster1OD!=0) && ($k1IolMaster1OD!='')){ echo $k1IolMaster1OD; } ?>" onBlur="return calCulate('k1IolMaster1OD', 'k1IolMaster2OD', 'k2IolMaster1OD', 'k2IolMaster2OD', 'cylIolMaster1OD', 'cylIolMaster2OD', 'aveIolMasterOD');">
                                            <img src="<?php echo $library_path; ?>/images/close_small.png" alt=""/>
                                            <input type="text" tabindex="2" size="8" class="form-control <?php if($rowsCount<=0){ echo 'text_9'; }else{ if($k1IolMaster2OD==$k1IolMaster2ODC) echo "backgroundLightGray"; else echo $classText; } ?>" name="k1IolMaster2OD" id="k1IolMaster2OD" value="<?php if(($k1IolMaster2OD!=0) && ($k1IolMaster2OD!='')){ echo $k1IolMaster2OD; } ?>" onBlur="return calCulate('k1IolMaster1OD', 'k1IolMaster2OD', 'k2IolMaster1OD', 'k2IolMaster2OD', 'cylIolMaster1OD', 'cylIolMaster2OD', 'aveIolMasterOD');">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 form-inline kloption">
                                        <div class="form-group"><input type="text" tabindex="3" class="form-control <?php if($rowsCount>0 && $k1Topographer1OD==$k1Topographer1ODC){ echo "backgroundLightGray";}?>" name="k1Topographer1OD" id="k1Topographer1OD" value="<?php if($k1Topographer1OD!=0){ echo $k1Topographer1OD; } ?>" onBlur="return calCulate('k1Topographer1OD', 'k1Topographer2OD', 'k2Topographer1OD', 'k2Topographer2OD', 'cylTopographer1OD', 'cylTopographer2OD', 'aveTopographerOD');">
                                            <img src="<?php echo $library_path; ?>/images/close_small.png" alt=""/>
                                            <input type="text" tabindex="3" class="form-control <?php if($rowsCount>0 && $k1Topographer2OD==$k1Topographer2ODC){echo "backgroundLightGray";}?>" name="k1Topographer2OD" id="k1Topographer2OD" value="<?php if($k1Topographer2OD!=0){ echo $k1Topographer2OD; } ?>" onBlur="return calCulate('k1Topographer1OD', 'k1Topographer2OD', 'k2Topographer1OD', 'k2Topographer2OD', 'cylTopographer1OD', 'cylTopographer2OD', 'aveTopographerOD');">
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <!-- K1 -->
                                    
                                    <!-- K2 -->
                                    <div class="col-sm-3 text-right"><label>K2</label></div>
                        
                                    <div class="col-sm-3 form-inline kloption">
                                        <div class="form-group">
                                            <input type="text" tabindex="1" class="form-control <?php if($rowsCount>0 && $k2Auto1OD==$k2Auto1ODC){echo "backgroundLightGray";}?>" name="k2Auto1OD" id="k2Auto1OD" value="<?php if($vis_ak_od_slash!=0){ echo $vis_ak_od_slash; } ?>" onBlur="return calCulate('k1Auto1OD', 'k1Auto2OD', 'k2Auto1OD', 'k2Auto2OD', 'cylAuto1OD', 'cylAuto2OD', 'aveAutoOD');">
                                            <img src="<?php echo $library_path; ?>/images/close_small.png" alt=""/>
                                            <input type="text" tabindex="1" class="form-control <?php if($rowsCount>0 && $k2Auto2OD==$k2Auto2ODC){echo "backgroundLightGray";}?>" name="k2Auto2OD" id="k2Auto2OD" value="<?php if($k2Auto2OD!='') echo $k2Auto2OD; else echo $vis_ak_od_x; ?>" onBlur="return calCulate('k1Auto1OD', 'k1Auto2OD', 'k2Auto1OD', 'k2Auto2OD', 'cylAuto1OD', 'cylAuto2OD', 'aveAutoOD');">
                                        </div>
                                    </div>
                        
                                    <div class="col-sm-3 form-inline kloption">
                                        <div class="form-group">
                                            <input tabindex="2" class="form-control <?php if($rowsCount>0 && $k2IolMaster1OD==$k2IolMaster1ODC){echo "backgroundLightGray";}?>" name="k2IolMaster1OD" id="k2IolMaster1OD" value="<?php if($k2IolMaster1OD!=0){ echo $k2IolMaster1OD; } ?>" onBlur="return calCulate('k1IolMaster1OD', 'k1IolMaster2OD', 'k2IolMaster1OD', 'k2IolMaster2OD', 'cylIolMaster1OD', 'cylIolMaster2OD', 'aveIolMasterOD');">
                                            <img src="<?php echo $library_path; ?>/images/close_small.png" alt=""/>
                                            <input type="text" tabindex="2" class="form-control <?php if($rowsCount>0 && $k2IolMaster2OD==$k2IolMaster2ODC){echo "backgroundLightGray";}?>" name="k2IolMaster2OD" id="k2IolMaster2OD" value="<?php if($k2IolMaster2OD!=0){ echo $k2IolMaster2OD; } ?>" onBlur="return calCulate('k1IolMaster1OD', 'k1IolMaster2OD', 'k2IolMaster1OD', 'k2IolMaster2OD', 'cylIolMaster1OD', 'cylIolMaster2OD', 'aveIolMasterOD');">
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="col-sm-3 form-inline kloption">
                                        <div class="form-group">
                                            <input type="text" tabindex="3" class="form-control <?php if($rowsCount>0 && $k2Topographer1OD==$k2Topographer1ODC){echo "backgroundLightGray";}?>" name="k2Topographer1OD" id="k2Topographer1OD" value="<?php if($k2Topographer1OD!=0){echo $k2Topographer1OD;}?>" onBlur="return calCulate('k1Topographer1OD', 'k1Topographer2OD', 'k2Topographer1OD', 'k2Topographer2OD', 'cylTopographer1OD', 'cylTopographer2OD', 'aveTopographerOD');">
                                            <img src="<?php echo $library_path; ?>/images/close_small.png" alt=""/>
                                            <input type="text" tabindex="3" class="form-control <?php if($rowsCount>0 && $k2Topographer2OD==$k2Topographer2ODC){echo "backgroundLightGray";}?>" name="k2Topographer2OD" id="k2Topographer2OD"   value="<?php if($k2Topographer2OD!=0){echo $k2Topographer2OD;}?>" onBlur="return calCulate('k1Topographer1OD', 'k1Topographer2OD', 'k2Topographer1OD', 'k2Topographer2OD', 'cylTopographer1OD', 'cylTopographer2OD', 'aveTopographerOD');">
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <!-- K2 -->
                                  
                                    
                                    <!-- CYL -->
                                    <?php
                                    if($vis_ak_od_k>=$vis_ak_od_slash){
                                        $cyl1OD = $vis_ak_od_k - $vis_ak_od_slash;
                                    }else{
                                        $cyl1OD = $vis_ak_od_slash - $vis_ak_od_k;
                                    }
                                    if($vis_ak_od_k>=$vis_ak_od_slash){
                                        if($cylAuto2OD==''){
                                            $cylAuto2OD = $vis_ak_od_x;
                                        }
                                    }else{
                                        if($cylAuto2OD==''){
                                            $cylAuto2OD = $k2Auto2OD;
                                        }
                                    }?>
                                    <div class="col-sm-3 text-right"><label>CYL</label></div>
                                    
                                    <div class="col-sm-3 form-inline kloption">
                                        <div class="form-group">
                                            <input readonly type="text" tabindex="1" class="form-control <?php if($rowsCount>0 && $cylAuto1OD==$cylAuto1ODC){echo "backgroundLightGray";}?>" name="cylAuto1OD" id="cylAuto1OD" value="<?php if($cyl1OD!=0){echo $cyl1OD;}?>">
                                            <input type="hidden" name="cylAuto1ODH" id="cylAuto1ODH" value="<?php if($cyl1OD!=0){echo $cyl1OD;}?>">
                                            <img src="<?php echo $library_path; ?>/images/atsign.png" alt=""/>
                                            <input readonly type="text" tabindex="1" class="form-control <?php if($rowsCount>0 && $cylAuto2OD==$cylAuto2ODC){echo "backgroundLightGray";}?>" name="cylAuto2OD" id="cylAuto2OD" value="<?php if($cylAuto2OD!=0){echo $cylAuto2OD;}?>">
                                            <input type="hidden"  name="cylAuto2ODH" id="cylAuto2ODH" value="<?php if($cylAuto2OD!=0){echo $cylAuto2OD;}?>">
                                        </div>
                                    </div>
                        
                                    <div class="col-sm-3 form-inline kloption">
                                        <div class="form-group">
                                            <input readonly type="text" tabindex="2"  class="form-control <?php if($rowsCount>0 && $cylIolMaster1OD==$cylIolMaster1ODC){echo "backgroundLightGray";}?>" name="cylIolMaster1OD" id="cylIolMaster1OD" value="<?php if($cylIolMaster1OD!=0){echo $cylIolMaster1OD;}?>">
                                            <input type="hidden" name="cylIolMaster1ODH" id="cylIolMaster1ODH" value="<?php if($cylIolMaster1OD!=0){echo $cylIolMaster1OD;}?>">
                                            <img src="<?php echo $library_path; ?>/images/atsign.png" alt=""/>
                                            <input readonly type="text" tabindex="2" class="form-control <?php if($rowsCount>0 && $cylIolMaster2OD==$cylIolMaster2ODC){echo "backgroundLightGray";}?>" name="cylIolMaster2OD" id="cylIolMaster2OD" value="<?php if($cylIolMaster2OD!=0){echo $cylIolMaster2OD;}?>">
                                            <input type="hidden" name="cylIolMaster2ODH" id="cylIolMaster2ODH" value="<?php if($cylIolMaster2OD!=0){echo $cylIolMaster2OD;}?>">
                                        </div>
                                    </div>
                        
                                    <div class="col-sm-3 form-inline kloption">
                                        <div class="form-group">
                                            <input readonly type="text" tabindex="3" class="form-control <?php if($rowsCount>0 && $cylTopographer1OD==$cylTopographer1ODC){echo "backgroundLightGray";}?>" name="cylTopographer1OD" id="cylTopographer1OD" value="<?php if($cylTopographer1OD!=0){echo $cylTopographer1OD;}?>">
                                            <input type="hidden" name="cylTopographer1ODH" id="cylTopographer1ODH" value="<?php if($cylTopographer1OD!=0){echo $cylTopographer1OD;}?>">
                                            <img src="<?php echo $library_path; ?>/images/atsign.png" alt=""/>
                                            <input readonly type="text" tabindex="3" class="form-control <?php if($rowsCount>0 && $cylTopographer2OD==$cylTopographer2ODC){echo "backgroundLightGray";}?>" name="cylTopographer2OD" id="cylTopographer2OD" value="<?php if($cylTopographer2OD!=0){echo $cylTopographer2OD;}?>">
                                            <input type="hidden" name="cylTopographer2ODH" id="cylTopographer2ODH" value="<?php if($cylTopographer2OD!=0){ echo $cylTopographer2OD;}?>">
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <!-- CYL -->
                                   
                                    <!-- AVE -->
                                    <?php
                                    $aveOD1 = ($vis_ak_od_k + $vis_ak_od_slash)/2;
                                    $aveIolMasterOD = ($k1IolMaster1OD + $k2IolMaster1OD)/2;
                                    $aveTopographerOD = ($k1Topographer1OD + $k2Topographer1OD)/2;
                                    ?>
                                    <div class="col-sm-3 text-right">
                                        <label>AVE</label></div>
                                        <div class="col-sm-3 form-inline kloption">
                                            <div class="form-group">
                                                <input type="text" tabindex="1" class="form-control <?php if($rowsCount>0 && $aveAutoOD==$aveAutoODC){echo "backgroundLightGray";}?>" name="aveAutoOD" id="aveAutoOD" value="<?php if($aveOD1!=0){echo $aveOD1;}?>">
                                                <input type="hidden" name="aveAutoODH" id="aveAutoODH" value="<?php if($aveOD1!=0){ echo $aveOD1; } ?>">
                                            </div>
                                    
                                        </div>
                                    
                                        <div class="col-sm-3 form-inline kloption">
                                            <div class="form-group">
                                                <input type="text" tabindex="2" class="form-control <?php if($rowsCount>0 && $aveIolMasterOD==$aveIolMasterODC){echo "backgroundLightGray";}?>" name="aveIolMasterOD" id="aveIolMasterOD" value="<?php if($aveIolMasterOD!=0){echo $aveIolMasterOD;}?>" onBlur="return calCulate('k1IolMaster1OD', 'k1IolMaster2OD', 'k2IolMaster1OD', 'k2IolMaster2OD', 'cylIolMaster1OD', 'cylIolMaster2OD', 'aveIolMasterOD');">
                                                <input type="hidden" name="aveIolMasterODH" id="aveIolMasterODH" value="<?php if($aveIolMasterOD!=0){echo $aveIolMasterOD;}?>">
                                            </div>
                                    
                                        </div>
                                        
                                        <div class="col-sm-3 form-inline kloption">
                                            <div class="form-group">
                                                <input type="text" tabindex="3" class="form-control <?php if($rowsCount>0 && $aveTopographerOD==$aveTopographerODC){echo "backgroundLightGray";}?>" name="aveTopographerOD" id="aveTopographerOD" value="<?php if($aveTopographerOD!=0){echo $aveTopographerOD;}?>" onBlur="return calCulate('k1Topographer1OD', 'k1Topographer2OD', 'k2Topographer1OD', 'k2Topographer2OD', 'cylTopographer1OD', 'cylTopographer2OD', 'aveTopographerOD');">
                                                <input type="hidden" name="aveTopographerODH" id="aveTopographerODH" value="<?php if($aveTopographerOD!=0){echo $aveTopographerOD;}?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <!-- AVE -->
                                
                                    <div class="table-responsive tsttbl ">
                                    <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr class="toptr">
                                        <td>Axial</td>
                                        <td>Contact </td>
                                        <td>Immersion</td>
                                        <td>IOL Master</td>
                                    </tr>
                                    <tr>
                                        <td>Length</td>
                                        <td><input type="text" tabindex="4" class="form-control <?php if($rowsCount>0 && $contactLengthOD==$contactLengthODC){echo "backgroundLightGray";}?>" name="contactLengthOD" id="contactLengthOD" value="<?php echo $contactLengthOD; ?>"></td>
                                        <td><input type="text" tabindex="4" class="form-control <?php if($rowsCount>0 && $immersionLengthOD==$immersionLengthODC){echo "backgroundLightGray";}?>" name="immersionLengthOD" id="immersionLengthOD" value="<?php echo $immersionLengthOD; ?>"></td>
                                        <td><input type="text" tabindex="4" class="form-control <?php if($rowsCount>0 && $iolMasterLengthOD==$iolMasterLengthODC){echo "backgroundLightGray";}?>" name="iolMasterLengthOD" id="iolMasterLengthOD" value="<?php echo $iolMasterLengthOD; ?>"></td>
                                    </tr>
										
                                    <tr>
                                        <td>Notes</td>
                                        <td><textarea tabindex="4" name="contactNotesOD" id="contactNotesOD" rows="1" cols="20" class="form-control"><?php echo $contactNotesOD; ?></textarea></td>
                                        <td><textarea tabindex="4" name="immersionNotesOD" id="immersionNotesOD" rows="1" cols="20" class="form-control"><?php echo $immersionNotesOD; ?></textarea></td>
                                        <td><textarea tabindex="4" name="iolMasterNotesOD" id="iolMasterNotesOD" rows="1" cols="20" class="form-control"><?php echo $iolMasterNotesOD; ?></textarea></td>
                                    </tr>
                                    </tbody>
                                    </table>
                                </div>
                            	<div class="clearfix"></div>
                            </div>
            			</div>
                    	<div class="clearfix"></div>
                    </div>
                    
                    <!--OS top -->
                    <div class="col-sm-6">
                        <div class="mrrht">
                            <div class="row">
                                <div class="col-sm-2"><span>MR</span></div>
                                <div class="col-sm-5">
                                    <div class="row">
                                        <div class="col-sm-4">
                                        	<div class="form-group">
                                            	<label for="">S</label>
	                                            <input type="text" tabindex="4" class="form-control <?php if($rowsCount>0 && $vis_mr_os_sC==$vis_mr_os_s){echo "backgroundLightGray";}else{echo $classText;}?>" name="mrSOS" id="mrSOS" value="<?php echo $vis_mr_os_s; ?>">
                                        	</div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="">C</label>
                                                <input type="text" tabindex="4" class="form-control <?php if($rowsCount>0 && $vis_mr_os_cC==$vis_mr_os_c){echo "backgroundLightGray";}else{echo $classText;}?>" name="mrCOS" id="mrCOS" value="<?php echo $vis_mr_os_c; ?>">
                                         	</div>
                                         </div>
                                         <div class="col-sm-4">
                                         	<div class="form-group">
                                                <label for="">A</label>
                                                <input type="text" tabindex="4" class="form-control <?php if($rowsCount>0 && $vis_mr_os_aC==$vis_mr_os_a){echo "backgroundLightGray";}else{echo $classText;}?>" name="mrAOS" id="mrAOS" value="<?php echo $vis_mr_os_a; ?>">
                                         	</div>
                                         </div>
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                	<label for="">VISION</label>
                                    <div class="row">
                                        <div class="col-sm-6"><input type="text" tabindex="4" class="form-control <?php if($rowsCount>0 && $visionOSC==$visionOS){echo "backgroundLightGray";}else{echo $classText;}?>" name="visionOS" id="visionOS" value="<?php echo $visionOS;?>" onBlur="return chkFormat('visionOS', '<?php echo imw_real_escape_string($visionOS); ?>');"> Vision</div>
                                        <div class="col-sm-6"><input type="text" tabindex="4" class="form-control <?php if($rowsCount>0 && $glareOSC==$glareOS){echo "text backgroundLightGray";}else{echo $classText;}?>" name="glareOS" id="glareOS" value="<?php echo $glareOS; ?>" onBlur="return chkFormat('glareOS', '<?php echo imw_real_escape_string($glareOS); ?>');"> Glare</div>
                                    </div>
                                </div>
                            </div>
                			<div class="clearfix"></div>
                		</div>
						
                    	<div class="clearfix"></div>
                    	<div class="testsctbx">
                            <div class="tstthead">
                                <div class="row">
                                    <div class="col-sm-12 tstflt text-right form-inline">
                                    	<figure class="osbox">OS</figure>
                                        <div class="form-group">
                                            <label for="">Performed By:</label>
                                            <select name="phyTechListOS" tabindex="4" id="phyTechListOS" class="form-control minimal <?php if($rowsCount>0 && $performedByOSC==$performedByOS){echo "backgroundLightGray";}?>">
                                            <option value=""></option>
                                            <?php
                                            foreach($phyTechArray as $phyTechId => $phyTechName){
                                                ?>
                                                <option value="<?php echo $phyTechId; ?>" <?php if($provider_idOS==$phyTechId) echo "SELECTED"; ?>><?php echo $phyTechName; ?></option>
                                                <?php
                                            }
                                            ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                        	<label for="">Date</label>
                                            <div class="input-group">
                                            	<input id="dateOS" tabindex="4" type="text" name="dateOS" onBlur="checkdate(this);" maxlength="10" class="form-control datePicker <?php if($rowsCount>0 && $dateOSC==$dateOS){echo "backgroundLightGray";}?>" value="<?php if(!$dateOS){echo get_date_format($today,'mm-dd-yyyy');}else{echo get_date_format($dateOS,'mm-dd-yyyy');}?>">
                                                <div class="input-group-addon">
                                                	<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
            				<div class="clearfix"></div>
                            <div class="testsctbxpd">
                            	<div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-sm-3">&nbsp;</div>
                                    <div class="col-sm-3">
                                    	<select tabindex="4" class="form-control minimal <?php if($rowsCount>0 && $autoSelectOS==$autoSelectOSC){echo "backgroundLightGray";}else{echo $classText;}?>" id="menuMain4Text" name="autoSelectOS">
										  <?php foreach($kReadingArr as $k=>$v){
                                              $sel_autoSelectOS = '';
                                              if($v[2]==$autoSelectOS) $sel_autoSelectOS = ' selected';
                                              echo '<option value="'.$v[2].'"'.$sel_autoSelectOS.'>'.$v[0].'</option>';
                                          }
                                          ?>
											
                                        </select>
                                    </div>
                                    <div class="col-sm-3"><select tabindex="4" class="form-control minimal <?php if($rowsCount>0 && $iolMasterSelectOS==$iolMasterSelectOSC){echo "backgroundLightGray";}else{echo $classText;}?>" name="iolMasterSelectOS" id="iolMasterSelectOS">
									  <?php foreach($kReadingArr as $k=>$v){
                                          $sel_iolMasterSelectOS = '';
                                          if($v[2]==$iolMasterSelectOS) $sel_iolMasterSelectOS = ' selected';
                                          echo '<option value="'.$v[2].'"'.$sel_iolMasterSelectOS.'>'.$v[0].'</option>';
                                      }
                                      ?>
                                    </select>
                                    </div>
                                    
                                    <div class="col-sm-3"><select tabindex="4" class="form-control minimal <?php if($rowsCount>0 && $topographerSelectOS==$topographerSelectOSC){echo "backgroundLightGray";}else{echo $classText;}?>" name="topographerSelectOS" id="topographerSelectOS">
									  <?php foreach($kReadingArr as $k=>$v){
                                          $sel_topographerSelectOS = '';
                                          if($v[2]==$topographerSelectOS) $sel_topographerSelectOS = ' selected';
                                          echo '<option value="'.$v[2].'"'.$sel_topographerSelectOS.'>'.$v[0].'</option>';
                                      }
                                      ?>
                                    </select>
                                    </div>
                                	<div class="clearfix"></div>
                                    
                                    <!-- K1 -->
                                    <?php
									if((!$surgical_id) && ($vis_ak_os_k!='')){
										if($vis_ak_os_k<=$vis_ak_os_slash){
											if($vis_ak_os_x<=90){
												$k2Auto2OS = $vis_ak_os_x;
												$vis_ak_os_x = $vis_ak_os_x + 90;
											}else{
												$k2Auto2OS = $vis_ak_os_x;
												$vis_ak_os_x = $vis_ak_os_x - 90;
											}
										}else{
											$tmp = $vis_ak_os_x+$k2Auto2OS;
											
											if($tmp<90){
												$k2Auto2OS = $tmp + 90;
											}else{
												$k2Auto2OS = $tmp - 90;
											}
										}
									}
									?>
                                    <div class="col-sm-3 text-right"><label>K1</label></div>
                                    <div class="col-sm-3 form-inline kloption">
                                    	<div class="form-group">
                                        	<input type="text" tabindex="5" class="form-control <?php if($rowsCount>0 && $vis_ak_os_k==$k1Auto1OSC){echo "backgroundLightGray";}?>" name="k1Auto1OS" id="k1Auto1OS" value="<?php if($vis_ak_os_k!=0){echo $vis_ak_os_k;}?>" onBlur="return calCulate('k1Auto1OS', 'k1Auto2OS', 'k2Auto1OS', 'k2Auto2OS', 'cylAuto1OS', 'cylAuto2OS', 'aveAutoOS');">
                                      		<img src="<?php echo $library_path; ?>/images/close_small.png" alt=""/>
                                    		<input type="text" tabindex="5" class="form-control <?php if($rowsCount>0 && $k1Auto2OS==$k1Auto2OSC){echo "backgroundLightGray";}?>" name="k1Auto2OS" id="k1Auto2OS" value="<?php if($k1Auto2OS!=''){echo $k1Auto2OS;}else{echo $vis_ak_os_x;}?>" onBlur="return calCulate('k1Auto1OS', 'k1Auto2OS', 'k2Auto1OS', 'k2Auto2OS', 'cylAuto1OS', 'cylAuto2OS', 'aveAutoOS');">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 form-inline kloption">
                                        <div class="form-group">
                                        	<input type="text" tabindex="6" class="form-control <?php if($rowsCount>0 && $k1IolMaster1OS==$k1IolMaster1OSC){echo "backgroundLightGray";}?>" name="k1IolMaster1OS" id="k1IolMaster1OS" value="<?php if($k1IolMaster1OS!=0){echo $k1IolMaster1OS;}?>" onBlur="return calCulate('k1IolMaster1OS', 'k1IolMaster2OS', 'k2IolMaster1OS', 'k2IolMaster2OS', 'cylIolMaster1OS', 'cylIolMaster2OS', 'aveIolMasterOS');">
                                          	<img src="<?php echo $library_path; ?>/images/close_small.png" alt=""/>
                                        	<input type="text" tabindex="6" class="form-control <?php if($rowsCount>0 && $k1IolMaster2OS==$k1IolMaster2OSC){echo "backgroundLightGray";}?>" name="k1IolMaster2OS" id="k1IolMaster2OS" value="<?php if($k1IolMaster2OS!=0){echo $k1IolMaster2OS;}?>" onBlur="return calCulate('k1IolMaster1OS', 'k1IolMaster2OS', 'k2IolMaster1OS', 'k2IolMaster2OS', 'cylIolMaster1OS', 'cylIolMaster2OS', 'aveIolMasterOS');">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 form-inline kloption">
                                    	<div class="form-group">
                                        	<input type="text" tabindex="7" class="form-control <?php if($rowsCount>0 && $k1Topographer1OS==$k1Topographer1OSC){echo "backgroundLightGray";}?>" name="k1Topographer1OS" id="k1Topographer1OS" value="<?php if($k1Topographer1OS!=0){echo $k1Topographer1OS;}?>" onBlur="return calCulate('k1Topographer1OS', 'k1Topographer2OS', 'k2Topographer1OS', 'k2Topographer2OS', 'cylTopographer1OS', 'cylTopographer2OS', 'aveTopographerOS');">
                                      		<img src="<?php echo $library_path; ?>/images/close_small.png" alt=""/>
                                    		<input type="text" tabindex="7" class="form-control <?php if($rowsCount>0 && $k1Topographer2OS==$k1Topographer2OSC){echo "backgroundLightGray";}?>" name="k1Topographer2OS" id="k1Topographer2OS" value="<?php if($k1Topographer2OS!=0){echo $k1Topographer2OS;}?>" onBlur="return calCulate('k1Topographer1OS', 'k1Topographer2OS', 'k2Topographer1OS', 'k2Topographer2OS', 'cylTopographer1OS', 'cylTopographer2OS', 'aveTopographerOS');">
                                        </div>
                                    </div>
	                                <div class="clearfix"></div>
                                    <!-- K1 -->

									<!-- K2 -->
                                    <div class="col-sm-3 text-right"><label>K2</label></div>
                                    <div class="col-sm-3 form-inline kloption">
                                        <div class="form-group">
                                        	<input type="text" tabindex="5" class="form-control <?php if($rowsCount>0 && $k2Auto1OS==$k2Auto1OSC){echo "backgroundLightGray";}?>" name="k2Auto1OS" id="k2Auto1OS" value="<?php if($vis_ak_os_slash!=0){echo $vis_ak_os_slash; }?>" onBlur="return calCulate('k1Auto1OS', 'k1Auto2OS', 'k2Auto1OS', 'k2Auto2OS', 'cylAuto1OS', 'cylAuto2OS', 'aveAutoOS');">
                                          	<img src="<?php echo $library_path; ?>/images/close_small.png" alt=""/>
                                        	<input type="text" tabindex="5" class="form-control <?php if($rowsCount>0 && $k2Auto2OS==$k2Auto2OSC){echo "backgroundLightGray";}?>" name="k2Auto2OS" id="k2Auto2OS" value="<?php if($k2Auto2OS!='') echo $k2Auto2OS; else  echo $vis_ak_os_x;?>" onBlur="return calCulate('k1Auto1OS', 'k1Auto2OS', 'k2Auto1OS', 'k2Auto2OS', 'cylAuto1OS', 'cylAuto2OS', 'aveAutoOS');">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 form-inline kloption">
                                    	<div class="form-group">
                                        	<input type="text" tabindex="6" class="form-control <?php if($rowsCount>0 && $k2IolMaster1OS==$k2IolMaster1OSC){echo "backgroundLightGray";}?>" name="k2IolMaster1OS" id="k2IolMaster1OS" value="<?php if($k2IolMaster1OS!=0){echo $k2IolMaster1OS; }?>" onBlur="return calCulate('k1IolMaster1OS', 'k1IolMaster2OS', 'k2IolMaster1OS', 'k2IolMaster2OS', 'cylIolMaster1OS', 'cylIolMaster2OS', 'aveIolMasterOS');">
                                      		<img src="<?php echo $library_path; ?>/images/close_small.png" alt=""/>
                                    		<input type="text" tabindex="6" class="form-control <?php if($rowsCount>0 && $k2IolMaster2OS==$k2IolMaster2OSC){echo "backgroundLightGray";}?>" name="k2IolMaster2OS" id="k2IolMaster2OS" value="<?php if($k2IolMaster2OS!=0){echo $k2IolMaster2OS; }?>" onBlur="return calCulate('k1IolMaster1OS', 'k1IolMaster2OS', 'k2IolMaster1OS', 'k2IolMaster2OS', 'cylIolMaster1OS', 'cylIolMaster2OS', 'aveIolMasterOS');">
                                        </div>
                                    </div>
                                    <div class="col-sm-3 form-inline kloption">
                                    	<div class="form-group">
                                        	<input type="text" tabindex="7" class="form-control <?php if($rowsCount>0 && $k2Topographer1OS==$k2Topographer1OSC){echo "backgroundLightGray";}?>" name="k2Topographer1OS" id="k2Topographer1OS" value="<?php if($k2Topographer1OS!=0){echo $k2Topographer1OS;}?>" onBlur="return calCulate('k1Topographer1OS', 'k1Topographer2OS', 'k2Topographer1OS', 'k2Topographer2OS', 'cylTopographer1OS', 'cylTopographer2OS', 'aveTopographerOS');">
                                      		<img src="<?php echo $library_path; ?>/images/close_small.png" alt=""/>
                                    		<input type="text" tabindex="7" class="form-control <?php if($rowsCount>0 && $k2Topographer2OS==$k2Topographer2OSC){echo "backgroundLightGray";}?>" name="k2Topographer2OS" id="k2Topographer2OS" value="<?php if($k2Topographer2OS!=0){echo $k2Topographer2OS;}?>" onBlur="return calCulate('k1Topographer1OS', 'k1Topographer2OS', 'k2Topographer1OS', 'k2Topographer2OS', 'cylTopographer1OS', 'cylTopographer2OS', 'aveTopographerOS');">
                                        </div>
                                    </div>
                                	<div class="clearfix"></div>
                                    <!-- K2 -->
                                    
                                    <!-- CYL -->
									
                                    <?php
									if($vis_ak_os_k>=$vis_ak_os_slash){
										$cyl1OS = $vis_ak_os_k - $vis_ak_os_slash;
									}else{
										$cyl1OS = $vis_ak_os_slash - $vis_ak_os_k;
									}
									if($vis_ak_os_k>=$vis_ak_os_slash){
										if($k1Auto2OS!=''){
											$cylAuto2OS = $k1Auto2OS;
										}else{
											$cylAuto2OS = $vis_ak_os_x;
										}
									}else{
										if($k2Auto2OS!=''){
											$cylAuto2OS = $k2Auto2OS;
										}else{
											$cylAuto2OS = $vis_ak_os_x;
										}
									}
									?>
                                    <div class="col-sm-3 text-right"><label>CYL</label></div>
                                    <div class="col-sm-3 form-inline kloption">
                                        <div class="form-group">
                                        	<input readonly type="text" tabindex="5" class="form-control <?php if($rowsCount>0 && $cylAuto1OS==$cylAuto1OSC){echo "backgroundLightGray";}?>" name="cylAuto1OS" id="cylAuto1OS" value="<?php if($cyl1OS!=0){ echo $cyl1OS; } ?>">
                                            <input type="hidden" name="cylAuto1OSH" id="cylAuto1OSH" value="<?php if($cyl1OS!=0){echo $cyl1OS;}?>">
											<img src="<?php echo $library_path; ?>/images/atsign.png" alt=""/>
                                        	<input readonly type="text" tabindex="5" class="form-control <?php if($rowsCount>0 && $cylAuto2OS==$cylAuto2OSC){echo "backgroundLightGray";}?>" name="cylAuto2OS" id="cylAuto2OS" value="<?php if($cylAuto2OS!=0){echo $cylAuto2OS;}?>">
                                            <input type="hidden" name="cylAuto2OSH" id="cylAuto2OSH" value="<?php if($cylAuto2OS!=0){echo $cylAuto2OS;}?>">
                                        </div>
                                    </div>
                                	<div class="col-sm-3 form-inline kloption">
                                        <div class="form-group">
                                            <input readonly type="text" tabindex="6" class="form-control <?php if($rowsCount>0 && $cylIolMaster1OS==$cylIolMaster1OSC){echo "backgroundLightGray";}?>" name="cylIolMaster1OS" id="cylIolMaster1OS" value="<?php if($cylIolMaster1OS!=0){echo $cylIolMaster1OS; } ?>">
                                            <input type="hidden" name="cylIolMaster1OSH" id="cylIolMaster1OSH" value="<?php if($cylIolMaster1OS!=0){echo $cylIolMaster1OS;}?>">
                                            <img src="<?php echo $library_path; ?>/images/atsign.png" alt=""/>
                                            <input readonly type="text" tabindex="6" class="form-control <?php if($rowsCount>0 && $cylIolMaster2OS==$cylIolMaster2OSC){echo "backgroundLightGray";}?>" name="cylIolMaster2OS" id="cylIolMaster2OS" value="<?php if($cylIolMaster2OS!=0){echo $cylIolMaster2OS;}?>">
                                            <input  type="hidden" name="cylIolMaster2OSH" id="cylIolMaster2OSH" value="<?php if($cylIolMaster2OS!=0){echo $cylIolMaster2OS;}?>">
                                        </div>
                                    </div>
                                	<div class="col-sm-3 form-inline kloption">
                                        <div class="form-group">
                                        	<input readonly type="text" tabindex="7" class="form-control <?php if($rowsCount>0 && $cylTopographer1OS==$cylTopographer1OSC){echo "backgroundLightGray";}?>" name="cylTopographer1OS" id="cylTopographer1OS" value="<?php if($cylTopographer1OS!=0){ echo $cylTopographer1OS;}?>">
                                            <input type="hidden"  name="cylTopographer1OSH" id="cylTopographer1OSH" value="<?php if($cylTopographer1OS!=0){echo $cylTopographer1OS;}?>">
                                          	<img src="<?php echo $library_path; ?>/images/atsign.png" alt=""/>
                                        	<input readonly type="text" tabindex="7" class="form-control <?php if($rowsCount>0 && $cylTopographer2OS==$cylTopographer2OSC){echo "backgroundLightGray";}?>" name="cylTopographer2OS" id="cylTopographer2OS" value="<?php if($cylTopographer2OS!=0){ echo $cylTopographer2OS;}?>">
                                            <input type="hidden" name="cylTopographer2OSH" id="cylTopographer2OSH" value="<?php if($cylTopographer2OS!=0){ echo $cylTopographer2OS; } ?>">
                                        </div>
                                	</div>
                                	<div class="clearfix"></div>
                                    <!-- CYL -->
                                    
                                    <!-- AVE -->
                                    <?php
									$aveOS1 = ($vis_ak_os_k + $vis_ak_os_slash)/2;
									$aveIolMasterOS = ($k1IolMaster1OS + $k2IolMaster1OS)/2;
									$aveTopographerOS = ($k1Topographer1OS + $k2Topographer1OS)/2;
									if($ascanSurgical == 'False') $classText = 'text backgroundLightGray';
									?>
                                    <div class="col-sm-3 text-right"><label>AVE</label></div>
                                    <div class="col-sm-3 form-inline kloption">
                                    	<div class="form-group">
                                        	<input type="text" tabindex="5" class="form-control <?php if($rowsCount>0 && $aveAutoOS==$aveAutoOSC){echo "backgroundLightGray";}?>" name="aveAutoOS" id="aveAutoOS" value="<?php if($aveOS1!=0) echo $aveOS1; ?>" onBlur="return calCulate('k1Auto1OS', 'k1Auto2OS', 'k2Auto1OS', 'k2Auto2OS', 'cylAuto1OS', 'cylAuto2OS', 'aveAutoOS');">
                                            <input type="hidden" name="aveAutoOSH" id="aveAutoOSH" value="<?php if($aveOS1!=0) echo $aveOS1; ?>">
                                        </div>
                                	</div>
                                	<div class="col-sm-3 form-inline kloption">
	                                    <div class="form-group">
                                        	<input type="text" tabindex="6" class="form-control <?php if($rowsCount>0 && $aveIolMasterOS==$aveIolMasterOSC){echo "backgroundLightGray";}?>" name="aveIolMasterOS" id="aveIolMasterOS" value="<?php if($aveIolMasterOS!=0) echo $aveIolMasterOS; ?>" onBlur="return calCulate('k1IolMaster1OS', 'k1IolMaster2OS', 'k2IolMaster1OS', 'k2IolMaster2OS', 'cylIolMaster1OS', 'cylIolMaster2OS', 'aveIolMasterOS');">
                                            <input type="hidden" name="aveIolMasterOSH" id="aveIolMasterOSH" value="<?php if($aveIolMasterOS!=0) echo $aveIolMasterOS; ?>">
                                        </div>
                                    </div>
                                	<div class="col-sm-3 form-inline kloption">
                                		<div class="form-group">
                                        	<input type="text" tabindex="7" class="form-control <?php if($rowsCount>0 && $aveTopographerOS==$aveTopographerOSC){echo "backgroundLightGray";}?>" name="aveTopographerOS" id="aveTopographerOS" value="<?php if($aveTopographerOS!=0) echo $aveTopographerOS; ?>" onBlur="return calCulate('k1Topographer1OS', 'k1Topographer2OS', 'k2Topographer1OS', 'k2Topographer2OS', 'cylTopographer1OS', 'cylTopographer2OS', 'aveTopographerOS');">
                                            <input type="hidden" name="aveTopographerOSH" id="aveTopographerOSH" value="<?php if($aveTopographerOS!=0) echo $aveTopographerOS; ?>">
                                        </div>
	                                </div>
                                </div>
     	                        <div class="clearfix"></div>
                                <!-- AVE -->
                                
                                <div class="table-responsive tsttbl ">
                                    <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr class="toptr">
                                        <td>Axial</td>
                                        <td>Contact </td>
                                        <td>Immersion</td>
                                        <td>IOL Master</td>
                                    </tr>
                                    <tr>
                                        <td>Length</td>
                                        <td><input type="text" tabindex="7" class="form-control <?php if($rowsCount<=0){ echo 'text_9'; }else{ if($contactLengthOS==$contactLengthOSC) echo "text backgroundLightGray"; else echo $classText; } ?>" size="10" name="contactLengthOS" id="contactLengthOS" value="<?php echo $contactLengthOS; ?>"></td>
										<td><input type="text" tabindex="7"  class="form-control <?php if($rowsCount<=0){ echo 'text_9'; }else{ if($immersionLengthOS==$immersionLengthOSC) echo "text backgroundLightGray"; else echo $classText; } ?>" size="10" name="immersionLengthOS" id="immersionLengthOS" value="<?php echo $immersionLengthOS; ?>"></td>
										<td><input type="text" tabindex="7" class="form-control <?php if($rowsCount<=0){ echo 'text_9'; }else{ if($iolMasterLengthOS==$iolMasterLengthOSC) echo "text backgroundLightGray"; else echo $classText; } ?>" size="10" name="iolMasterLengthOS" id="iolMasterLengthOS" value="<?php echo $iolMasterLengthOS; ?>"></td>
                                    </tr>
                                    <tr>
                                      	<td>Notes</td>
                                      	<td><textarea tabindex="7" name="contactNotesOS" id="contactNotesOS" rows="1" cols="20" class="form-control"><?php echo $contactNotesOS; ?></textarea></td>
									  	<td><textarea tabindex="7" name="immersionNotesOS" id="immersionNotesOS" rows="1" cols="20" class="form-cont rol"><?php echo $immersionNotesOS; ?></textarea></td>
										<td><textarea tabindex="7"  tabindex="-7" name="iolMasterNotesOS" id="iolMasterNotesOS" rows="1" cols="20" class="form-control"><?php echo $iolMasterNotesOS; ?></textarea></td>
                                    </tr>
                                    </tbody>
                                    </table>
                                </div>
                            	<div class="clearfix"></div>
                            </div>
						</div>
                    	<div class="clearfix"></div>
                	</div>
				</div>
                
                <div class="row">
                    <!-- OD IOL part -->
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <div class="testsctbx">
                            <div class="tstthead">
                                <div class="row">
                                    <div class="col-sm-12 tstflt text-right form-inline">
                                        <figure class="odbox">OD</figure>
                                        <div class="form-group">
                                            <label for="">Performed By:</label>
                                            <select class="form-control minimal <?php if($rowsCount>0 && ($provider_idOD==$provider_idODC || empty($provider_idOD)==empty($provider_idODC))){echo "backgroundLightGray";}?>" id="performedByPhyOD" name="performedByPhyOD" onChange="return getFormulaValues('performedByPhyOD', 'OD', '<? echo $patient_id; ?>', '<?php echo $form_id; ?>');">
                                                <option value=""></option>
                                                <?php foreach($phyArray as $phyId => $phyName){?>
                                                <option value="<?php echo $phyId; ?>" <?php if($provider_idOD==$phyId) echo "selected"; ?>><?php echo $phyName; ?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="testsctbxpd performedBy">
                                <div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-sm-2"><span class="iolbox">IOL</span></div>
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && $powerIolOD==$powerIolODC){echo "backgroundLightGray";}else{echo $classText;}?>" name="powerIolOD" id="powerIolOD">
                                          <?php
                                          if($powerIolOD=='') $powerIolOD = "POWER";
                                          foreach($formulaHeadingsArr as $k=>$v){
                                              $sel_powerIolOD = '';
                                              if($v[2]==$powerIolOD) $sel_powerIolOD = ' selected';
                                              echo '<option value="'.$v[2].'"'.$sel_powerIolOD.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && $holladayOD==$holladayODC){echo "backgroundLightGray";}else{echo $classText;}?>" name="holladayOD" id="holladayOD">
                                          <?php
                                          if($holladayOD=='') $holladayOD = "Holladay";
                                          foreach($formulaHeadingsArr as $k=>$v){
                                              $sel_holladayOD = '';
                                              if($v[2]==$holladayOD) $sel_holladayOD = ' selected';
                                              echo '<option value="'.$v[2].'"'.$sel_holladayOD.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && $srk_tOD==$srk_tODC){echo "backgroundLightGray";}else{echo $classText;}?>" name="srk_tOD" id="srk_tOD">
                                          <?php
                                          if($srk_tOD=='') $srk_tOD = "SRK-T";
                                          foreach($formulaHeadingsArr as $k=>$v){
                                              $sel_srk_tOD = '';
                                              if($v[2]==$srk_tOD) $sel_srk_tOD = ' selected';
                                              echo '<option value="'.$v[2].'"'.$sel_srk_tOD.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && ($hofferOD==$hofferODC||$hofferOD = getFormulaHeadName($hofferODC))){echo "backgroundLightGray";}else{echo $classText;}?>" name="hofferOD" id="hofferOD">
                                          <?php
                                          if($hofferOD=='') $hofferOD = "HOFFER";
                                          foreach($formulaHeadingsArr as $k=>$v){
                                              $sel_hofferOD = '';
                                              if($v[2]==$hofferOD) $sel_hofferOD = ' selected';
                                              echo '<option value="'.$v[2].'"'.$sel_hofferOD.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row" id="iolOD1">
                                	<!--OD1-->
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && (($iol1OD==$iol1ODC) || ($providerLensesArrOD[0]==$iol1ODC))){echo "backgroundLightGray";}else{echo $classText;}?>" name="iol1OD" id="iol1OD" onChange="return listLenses('1', 'iol1OD');"><option value=""></option>
                                          <?php
                                          foreach($lensesArray as $k=>$v){
                                              $sel_lensesArray = '';
                                              if($v[2]==$providerLensesArrOD[0]) $sel_lensesArray = ' selected';
                                              echo '<option value2="'.$v[3].'" value="'.$v[2].'"'.$sel_lensesArray.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol1PowerOD==$iol1PowerODC){echo "backgroundLightGray";}?>" name="iol1PowerOD" id="iol1PowerOD" value="<?php echo $iol1PowerOD; ?>" onKeyDown="return checkIolVal('iol1OD');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol1HolladayOD==$iol1HolladayODC){echo "backgroundLightGray";}?>" name="iol1HolladayOD" id="iol1HolladayOD" value="<?php echo $iol1HolladayOD; ?>" onKeyDown="return checkIolVal('iol1OD');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol1srk_tOD==$iol1srk_tODC){echo "backgroundLightGray";}?>" name="iol1srk_tOD" id="iol1srk_tOD" value="<?php echo $iol1srk_tOD; ?>" onKeyDown="return checkIolVal('iol1OD');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol1HofferOD==$iol1HofferODC){echo "backgroundLightGray";}?>" name="iol1HofferOD" id="iol1HofferOD" value="<?php echo $iol1HofferOD; ?>" onKeyDown="return checkIolVal('iol1OD');"></div>
                                	<!--OD1-->
                                </div>
                                <div class="row" id="iolOD2">
                                    <!--OD2-->
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && (($iol2OD==$iol2ODC) || ($providerLensesArrOD[1]==$iol2ODC))){echo "backgroundLightGray";}else{echo $classText;}?>" name="iol2OD" id="iol2OD" onChange="return listLenses('2', 'iol2OD');"><option value=""></option>
                                          <?php
                                          foreach($lensesArray as $k=>$v){
                                              $sel_lensesArray = '';
                                              if($v[2]==$providerLensesArrOD[1]) $sel_lensesArray = ' selected';
                                              echo '<option value2="'.$v[3].'" value="'.$v[2].'"'.$sel_lensesArray.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol2PowerOD==$iol2PowerODC){echo "backgroundLightGray";}?>" name="iol2PowerOD" id="iol2PowerOD" value="<?php echo $iol2PowerOD; ?>" onKeyDown="return checkIolVal('iol2OD');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol2HolladayOD==$iol2HolladayODC){echo "backgroundLightGray";}?>" name="iol2HolladayOD" id="iol2HolladayOD" value="<?php echo $iol2HolladayOD; ?>" onKeyDown="return checkIolVal('iol2OD');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol2srk_tOD==$iol2srk_tODC){echo "backgroundLightGray";}?>" name="iol2srk_tOD" id="iol2srk_tOD" value="<?php echo $iol2srk_tOD; ?>" onKeyDown="return checkIolVal('iol2OD');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol2HofferOD==$iol2HofferODC){echo "backgroundLightGray";}?>" name="iol2HofferOD" id="iol2HofferOD" value="<?php echo $iol2HofferOD; ?>" onKeyDown="return checkIolVal('iol2OD');"></div>
                                    <!--OD2-->
    							</div>
                                <div class="row" id="iolOD3">
                                    <!--OD3-->
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && (($iol3OD==$iol3ODC) || ($providerLensesArrOD[2]==$iol3ODC))){echo "backgroundLightGray";}else{echo $classText;}?>" name="iol3OD"  id="iol3OD" onChange="return listLenses('3', 'iol3OD');"><option value=""></option>
                                          <?php
                                          foreach($lensesArray as $k=>$v){
                                              $sel_lensesArray = '';
                                              if($v[2]==$providerLensesArrOD[2]) $sel_lensesArray = ' selected';
                                              echo '<option value2="'.$v[3].'" value="'.$v[2].'"'.$sel_lensesArray.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol3PowerOD==$iol3PowerODC){echo "backgroundLightGray";}?>" name="iol3PowerOD" id="iol3PowerOD" value="<?php echo $iol3PowerOD; ?>" onKeyDown="return checkIolVal('iol3OD');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol3HolladayOD==$iol3HolladayODC){echo "backgroundLightGray";}?>" name="iol3HolladayOD" id="iol3HolladayOD" value="<?php echo $iol3HolladayOD; ?>" onKeyDown="return checkIolVal('iol3OD');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol3srk_tOD==$iol3srk_tODC){echo "backgroundLightGray";}?>" name="iol3srk_tOD" id="iol3srk_tOD" value="<?php echo $iol3srk_tOD; ?>" onKeyDown="return checkIolVal('iol3OD');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol3HofferOD==$iol3HofferODC){echo "backgroundLightGray";}?>" name="iol3HofferOD" id="iol3HofferOD" value="<?php echo $iol3HofferOD; ?>" onKeyDown="return checkIolVal('iol3OD');"></div>
                                    <!--OD3-->
        						</div>
                                <div class="row" id="iolOD4">
                                    <!--OD4-->
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && (($iol4OD==$iol4ODC) || ($providerLensesArrOD[3]==$iol4ODC))){echo "backgroundLightGray";}else{echo $classText;}?>" name="iol4OD" id="iol4OD" onChange="return listLenses('4', 'iol4OD');"><option value=""></option>
                                          <?php
                                          foreach($lensesArray as $k=>$v){
                                              $sel_lensesArray = '';
                                              if($v[2]==$providerLensesArrOD[3]) $sel_lensesArray = ' selected';
                                              echo '<option value2="'.$v[3].'" value="'.$v[2].'"'.$sel_lensesArray.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol4PowerOD==$iol4PowerODC){echo "backgroundLightGray";}?>" name="iol4PowerOD" id="iol4PowerOD" value="<?php echo $iol4PowerOD; ?>" onKeyDown="return checkIolVal('iol4OD');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol4HolladayOD==$iol4HolladayODC){echo "backgroundLightGray";}?>" name="iol4HolladayOD" id="iol4HolladayOD" value="<?php echo $iol4HolladayOD; ?>" onKeyDown="return checkIolVal('iol4OD');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol4srk_tOD==$iol4srk_tODC){echo "backgroundLightGray";}?>" name="iol4srk_tOD" id="iol4srk_tOD" value="<?php echo $iol4srk_tOD; ?>" onKeyDown="return checkIolVal('iol4OD');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol4HofferOD==$iol4HofferODC){echo "backgroundLightGray";}?>" name="iol4HofferOD" id="iol4HofferOD" value="<?php echo $iol4HofferOD; ?>" onKeyDown="return checkIolVal('iol4OD');"></div>
                                    <!--OD4-->
                            	</div>
	                            <div class="clearfix"></div>
    
                                <div class="performedBy">
                                    <div class="row">
                                        <div class="col-sm-2"><label>Cell Count</label> <input type="text" class="form-control <?php if($rowsCount>0 && $cellCountOD==$cellCountODC){echo "backgroundLightGray";}?>" name="cellCountOD" id="cellCountOD" value="<?php echo $cellCountOD;?>"></div>
                                        <div class="col-sm-2">
                                            <label>Pachymetry</label> 
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control <?php if($rowsCount>0 && $pachymetryValOD==$pachymetryValODC){echo "backgroundLightGray";}?>" name="pachymetryValOD" id="pachymetryValOD" value="<?php echo $pachymetryValOD; ?>" onChange="calCorrectionVal(this.value,'OD');"></div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control <?php if($rowsCount>0 && $pachymetryCorrecOD==$pachymetryCorrecODC){echo "backgroundLightGray";}?>" name="pachymetryCorrecOD" id="pachymetryCorrecOD" value="<?php echo $pachymetryCorrecOD; ?>"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2"><label>Corneal Diam</label> <input type="text" class="form-control <?php if($rowsCount>0 && $cornealDiamOD==$cornealDiamODC){echo "backgroundLightGray";}?>" name="cornealDiamOD" id="cornealDiamOD" value="<?php echo $cornealDiamOD; ?>"></div>
                                        <div class="col-sm-2"><label>Dominant Eye</label> <input type="text" class="form-control <?php if($rowsCount>0 && $dominantEyeOD==$dominantEyeODC){echo "backgroundLightGray";}?>" name="dominantEyeOD" id="dominantEyeOD" value="<?php echo $dominantEyeOD; ?>"></div>
                                        <div class="col-sm-2"><label>ACD Eye</label> <input type="text" class="form-control <?php if($rowsCount>0 && $acdOD==$acdODC){echo "backgroundLightGray";}?>" name="acdOD" id="acdOD" value="<?php echo $acdOD; ?>"></div>
                                        <div class="col-sm-2"><label>White to White</label> <input type="text" class="form-control <?php if($rowsCount && $w2wOD==$w2wODC){echo "backgroundLightGray";}?>" name="w2wOD" id="w2wOD" value="<?php echo $w2wOD; ?>"></div>
                                        <div class="col-sm-4">
                                            <label>Pupil Size</label>
                                            <div class="row">
                                                <div class="col-sm-6"><input type="text" class="form-control <?php if($rowsCount>0 && $pupilSize1OD==$pupilSize1ODC){echo "backgroundLightGray";}?>" name="pupilSize1OD" id="pupilSize1OD" value="<?php echo $pupilSize1OD;?>"><small>Un-Dilated</small></div>
                                                <div class="col-sm-1">/</div>
                                                <div class="col-sm-5"><input type="text" class="form-control <?php if($rowsCount>0 && $pupilSize2OD==$pupilSize2ODC){echo "backgroundLightGray";} ?>" name="pupilSize2OD" id="pupilSize2OD" value="<?php echo $pupilSize2OD; ?>"><small>Dilated</small></div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-12"><label>Notes</label> <textarea rows="3" name="notesOD" id="notesOD" class="form-control" onClick="return makeEmpty(this)" placeholder="Notes..."><?php echo $notesOD; ?></textarea></div>
                                    </div>
                                </div>
                            	<div class="clearfix"></div>
                            
                            	<div class="table-responsive plntabl">
                                <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td class="tstschlft" width="22%">CC</td>
                                    <td colspan="2">Scheduled for Intraocular lens implant A/Scan reviewed and IOL Selected.</td>
                                </tr>
                                <tr>
                                    <td class="tstschlft" width="22%">Assessment</td>
                                    <td colspan="2" class="tstrstopt">
                                        <div class="row">
                                        <div class="col-sm-3"><label><input type="checkbox" name="cataractOD" id="cataractOD" <?php if($cataractOD==1) echo "CHECKED"; ?>><span class="label_txt">Cataract</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="astigmatismOD" id="astigmatismOD" <?php if($astigmatismOD==1) echo "CHECKED"; ?> class="text_9"><span class="label_txt">Astigmatism</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="myopiaOD" id="myopiaOD" <?php if($myopiaOD==1) echo "CHECKED"; ?> class="text_9"><span class="label_txt">Myopia</span></label></div>
                                    </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tstschlft" width="22%">Plan</td>
                                    <td colspan="2" class="form-inline">
                                    <div class="form-group">
                                        <select style="width:100px !important" name="selecedIOLsOD" id="selecedIOLsOD" class="form-control minimal" onChange="return doSelectOD(this)">
                                            <option value=""></option>
                                            <?php
                                                $lensesProviderArray = getLenses($provider_idOD);
                                                if((($lensesProviderArray)) != (($providerLensesArrOD))){
                                                    $lensesProviderArray = $providerLensesODArr;
                                                }
                                                if(count($lensesProviderArray)>0){
                                                foreach($lensesProviderArray as $lensesId => $lenseNames){
                                                        if($selecedIOLsOD==$lensesId) { $PlanOD = $lenseNames; }
                                                    ?>
                                                    <option value="<?php echo $lensesId; ?>" style="color:<?php if($selecedIOLsOD==$lensesId) echo "0000FF"; else echo "000000"; ?>;" <?php  if($selecedIOLsOD==$lensesId) echo "SELECTED"; ?>><?php echo $lenseNames; ?></option>
                                                    <?php
                                                }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                      <div class="form-group">
                                        <label for="">Date of Surgery</label>
                                      </div>
                                      <div class="form-group">
                                        <input id="sur_dateOD" type="text"  name="sur_dateOD" value="<?php echo $sur_dateOD;?>"  onBlur="checkdate(this);" maxlength=10 class="form-control datePicker">
                                      </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tstschlft" width="22%">Procedure</td>
                                    <td colspan="2" class="tstrstopt">
                                    <?php if(!empty($elem_proc_od)){
                                        $arrTmp = explode(",",$elem_proc_od);
        
                                        if(in_array("Phaco",$arrTmp)){
                                            $elem_proc_od_1 = "checked";
                                        }
                                        if(in_array("Complex Phaco",$arrTmp)){
                                            $elem_proc_od_2 = "checked";
                                        }
                                        if(in_array("Combined Procedure",$arrTmp)){
                                            $elem_proc_od_3 = "checked";
                                        }
                                    }
                                    ?>
                                        <div class="row">
                                        <div class="col-sm-3"><label><input type="checkbox" id="elem_proc_od_1" name="elem_proc_od[]" value="Phaco" <?php echo $elem_proc_od_1 ; ?>><span class="label_txt">Phaco</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_proc_od_2" name="elem_proc_od[]" value="Complex Phaco" <?php echo $elem_proc_od_2 ; ?>><span class="label_txt">Complex Phaco</span></label></div>
                                        <div class="col-sm-5"><label><input type="checkbox" id="elem_proc_od_3" name="elem_proc_od[]" value="Combined Procedure" <?php echo $elem_proc_od_3 ; ?>><span class="label_txt">Combined Procedure</span></label></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tstschlft" width="22%">Anesthesia</td>
                                    <td colspan="2" class="tstrstopt">
                                        <?php if(!empty($elem_anes_od)){
                                            $arrTmp = explode(",",$elem_anes_od);
            
                                            if(in_array("Local",$arrTmp)){
                                                $elem_anes_od_1 = "checked";
                                            }
                                            if(in_array("Topical",$arrTmp)){
                                                $elem_anes_od_2 = "checked";
                                            }
                                        }?>
                                        <div class="row">
                                        <div class="col-sm-3"><label><input type="checkbox" id="elem_anes_od_1" name="elem_anes_od[]" value="Local" <?php echo $elem_anes_od_1;?>><span class="label_txt">Local</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_anes_od_2" name="elem_anes_od[]" value="Topical" <?php echo $elem_anes_od_2;?>><span class="label_txt">Topical</span></label></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tstschlft">Viscoelastic</td>
                                    <td colspan="2" class="tstrstopt">
                                        <?php if(empty($elem_visc_od_other)){$elem_visc_od_other = "Other";}
                                        if(!empty($elem_visc_od)){
                                            $arrTmp = explode(",",$elem_visc_od);
                                            if(in_array("Discovisc",$arrTmp)||in_array("Duovisc",$arrTmp)){$elem_visc_od_1="checked";}
                                            if(in_array("Viscoat",$arrTmp)){$elem_visc_od_2="checked";}
                                            if(in_array("Amvisc plus",$arrTmp)){$elem_visc_od_3="checked";}
                                            if(in_array("Healon",$arrTmp)){$elem_visc_od_4="checked";}
                                        }?>
                                        <div class="row">
                                        <div class="col-sm-3"><label><input type="checkbox" id="elem_visc_od_1" name="elem_visc_od[]" value="Duovisc" <?php echo $elem_visc_od_1;?>><span class="label_txt">Duovisc</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_visc_od_2" name="elem_visc_od[]" value="Viscoat" <?php echo $elem_visc_od_2 ; ?>><span class="label_txt">Viscoat</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_visc_od_3" name="elem_visc_od[]" value="Amvisc plus" <?php echo $elem_visc_od_3 ; ?>><span class="label_txt">Amvisc plus</span></label></div>
                                        <div class="col-sm-3"><label><input type="checkbox" id="elem_visc_od_4" name="elem_visc_od[]" value="Healon" <?php echo $elem_visc_od_4 ; ?>><span class="label_txt">Healon</span></label></div>
                                        <div class="col-sm-4"><input type="text" id="elem_visc_od_other" name="elem_visc_od_other" value="<?php echo $elem_visc_od_other ; ?>" onFocus="if(this.value=='Other')this.value='';" class="form-control"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="tstrstopt">
                                        <div class="row">
                                        <div class="col-sm-4" title="Limbal Relaxing Incision"><label><input type="checkbox" name="lriOD" id="lriOD" <?php if($lriOD==1){ echo "CHECKED"; } ?> title="Limbal Relaxing Incision" onClick=" return showHide('lriODTr', 'lriOD', 'cutsValueODTr');"><span class="label_txt">LRI</span></label></div>
                                        <div class="col-sm-4" title="Deluxe IOL"><label><input type="checkbox" name="dlOD" id="dlOD" <?php if($dlOD==1){ echo "CHECKED"; } ?>><span class="label_txt">DL</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="synechiolysisOD" id="synechiolysisOD" <?php if($synechiolysisOD==1){ echo "CHECKED"; } ?>><span class="label_txt">Synechiolysis</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="irishooksOD" id="irishooksOD" <?php if($irishooksOD==1){ echo "CHECKED"; } ?>><span class="label_txt">IRIS Hooks</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="trypanblueOD" id="trypanblueOD" <?php if($trypanblueOD==1){ echo "CHECKED"; } ?>><span class="label_txt">Trypan Blue</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="flomaxOD" id="flomaxOD" <?php if($flomaxOD==1){ echo "CHECKED"; } ?>><span class="label_txt">Pt. On Flomax</span></label></div>
                                        </div>
                                        <?php if(empty($elem_opts_od_other)){$elem_opts_od_other = "Other";}
                                        if(!empty($elem_opts_od)){
                                            $arrTmp = explode(",",$elem_opts_od);
                                            if(in_array("Malyugin ring",$arrTmp)){$elem_opts_od_1="checked";}
                                            if(in_array("Shugarcaine",$arrTmp)){$elem_opts_od_2="checked";}
                                            if(in_array("Capsule tension rings",$arrTmp)){$elem_opts_od_3="checked";}
                                            if(in_array("IOL Cutter",$arrTmp)){$elem_opts_od_4="checked";}
                                        }?>
                                        <div class="row">
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_opts_od_1" <?php echo $elem_opts_od_1;?> name="elem_opts_od[]" value="Malyugin ring"><span class="label_txt">Malyugin ring</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_opts_od_2" <?php echo $elem_opts_od_2;?> name="elem_opts_od[]" value="Shugarcaine"><span class="label_txt">Shugarcaine</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_opts_od_3" <?php echo $elem_opts_od_3;?> name="elem_opts_od[]" value="Capsule tension rings"><span class="label_txt">Capsule tension rings</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_opts_od_4" <?php echo $elem_opts_od_4;?> name="elem_opts_od[]" value="IOL Cutter"><span class="label_txt">IOL Cutter</span></label></div>
                                        <div class="col-sm-4"><input type="text" id="elem_opts_od_other" class="form-control" name="elem_opts_od_other" value="<?php echo $elem_opts_od_other;?>" onfocus="if(this.value=='Other')this.value='';"></div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                                </table>
                            	</div>
                                <div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-sm-7"><strong>IOL master to be done on day of surgery</strong></div>
                                    <div class="col-sm-5">
                                    <?php if($elem_iol2dn_od == "Yes"){$elem_iol2dn_od_1 = "checked";}
                                        if($elem_iol2dn_od == "No"){$elem_iol2dn_od_2 = "checked";}?>
                                        <div class="row tstrstopt">
                                        <div class="col-sm-6"><label><input type="checkbox" id="elem_iol2dn_od_1" <?php echo $elem_iol2dn_od_1;?>	name="elem_iol2dn_od" value="Yes" onclick="if(this.checked)document.getElementById('elem_iol2dn_od_2').checked=!this.checked;"><span class="label_txt">Yes</span></label></div>
                                        <div class="col-sm-6"><label><input type="checkbox" id="elem_iol2dn_od_2" <?php echo $elem_iol2dn_od_2;?> name="elem_iol2dn_od" value="No" onClick="if(this.checked)document.getElementById('elem_iol2dn_od_1').checked=!this.checked;"><span class="label_txt">No</span></label></div>
                                        </div>
                                    </div>
                                
                                </div>
                                <div class="clearfix"></div>
                                
                                <textarea class="form-control" name="notesAssesmentPlansOD" id="notesAssesmentPlansOD" rows="3" onClick="return makeEmpty(this)"><?php if($notesAssesmentPlansOD!='') echo $notesAssesmentPlansOD; else echo "Notes..."; ?></textarea>
                                
                                <div class="row" id="lriODTr" <?php if($lriOD!=1){ echo "style=\"display:none;\"";  }?>>
                                    <div class="col-sm-1 text-right">Cuts:</div>
                                    <div class="col-sm-2">
                                        <select name="cutsOD" id="cutsOD" class="form-control minimal" onChange="return showCutsChk('cutsValueODTr', 'cutsOD');">
                                            <option value=""></option>
                                            <option value="1" <?php if($cutsOD==1) echo "SELECTED"; ?>>1</option>
                                            <option value="2" <?php if($cutsOD==2) echo "SELECTED"; ?>>2</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-1 text-right">Length:</div>
                                    <div class="col-sm-2"><input type="text" class="form-control" name="lengthCutsOD" value="<?php echo $lengthOD;?>"></div>
                                    <div class="col-sm-2">
                                        <select name="lengthSelectOD" id="lengthSelectOD" class="form-control minimal">
                                            <option value="mm" <?php if($lengthTypeOD=="mm") echo "SELECTED"; ?>>mm</option>
                                            <option value="degree" <?php if($lengthTypeOD=="degree") echo "SELECTED"; ?>>&#176;</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-1 text-right">Axis:</div>
                                    <div class="col-sm-1"><input type="text" name="axisOD" class="form-control" value="<?php echo $axisOD; ?>"></div>
                                </div>
                            
                            	<div class="row tstrstopt" id="cutsValueODTr" <?php if($cutsOD!=1){ echo "style=\"display:none;\"";}?>>
                                    <div class="col-sm-3"><label><input type="checkbox" name="superiorOD" id="superiorOD" <?php if($superiorOD==1) echo "Checked"; ?>><span class="label_txt">Superior</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="inferiorOD" id="inferiorOD" <?php if($inferiorOD==1) echo "Checked"; ?>><span class="label_txt">Inferior</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="nasalOD" id="nasalOD" <?php if($nasalOD==1) echo "Checked"; ?>><span class="label_txt">Nasal</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="temporalOD" id="temporalOD" <?php if($temporalOD==1) echo "Checked"; ?>><span class="label_txt">Temporal</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="STOD" id="STOD" <?php if($STOD==1) echo "Checked"; ?>><span class="label_txt">ST</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="SNOD" id="SNOD" <?php if($SNOD==1) echo "Checked"; ?>><span class="label_txt">SN</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="ITOD" id="ITOD" <?php if($ITOD==1) echo "Checked"; ?>><span class="label_txt">IT</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="INOD" id="INOD" <?php if($INOD==1) echo "Checked"; ?>><span class="label_txt">IN</span></label></div>
                                </div>
                            	
                    			<div class="clearfix"></div>
                    			<?php if($callFromInterface != 'admin'){?>
                                <div class="row">
                                    <div class="col-sm-4 odphyname">Physician Name:</div>
                                    <div class="col-sm-8 mt5">
                                    	<select class="form-control minimal" name="physicianSelected" id="physicianSelected">
                                            <option value=""></option>
                                            <?php $tmp = $signedById;
                                            foreach($phyArray as $pId => $physicianName){?>
                                            <option value="<?php echo $pId; ?>" <?php if($tmp==$pId) echo "SELECTED"; ?>><?php echo $physicianName; ?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                                <?php }?>
                            </div>
                        </div>
                    </div>

					<!-- OS IOL PART -->
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <div class="testsctbx">
                            <div class="tstthead">
                                <div class="row">
                                    <div class="col-sm-12 tstflt text-right form-inline">
                                        <figure class="osbox">OS</figure>
                                        <div class="form-group">
                                            <label for="">Performed By:</label>
                                            <select class="form-control minimal <?php if($rowsCount>0 && $performedIolOS==$performedIolOSC){echo "backgroundLightGray";}?>" id="performedByOS" name="performedByOS" onChange="return getFormulaValues('performedByPhyOS', 'OS', '<? echo $patient_id; ?>', '<?php echo $form_id; ?>');">
                                                <option value=""></option>
                                                <?php foreach($phyArray as $phyId => $phyName){?>
                                                <option value="<?php echo $phyId; ?>" <?php if($phyId==$performedIolOS) echo "SELECTED"; ?>><?php echo $phyName; ?></option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="testsctbxpd performedBy">
                                <div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-sm-2"><span class="osiolbox">IOL</span></div>
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && ($powerIolOS==$powerIolOSC||$powerIolOS==getFormulaHeadName($powerIolOSC))){echo "backgroundLightGray";}else{echo $classText;}?>" name="powerIolOS" id="powerIolOS">
                                          <?php
                                          if($powerIolOS=='') $powerIolOS = "POWER";
                                          foreach($formulaHeadingsArr as $k=>$v){
                                              $sel_powerIolOS = '';
                                              if($v[2]==$powerIolOS) $sel_powerIolOS = ' selected';
                                              echo '<option value="'.$v[2].'"'.$sel_powerIolOS.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && ($holladayOS==$holladayOSC||$holladayOS==getFormulaHeadName($holladayOSC))){echo "backgroundLightGray";}else{echo $classText;}?>" name="holladayOS" id="holladayOS">
                                          <?php
                                          if($holladayOS=='') $holladayOS = "Holladay";
                                          foreach($formulaHeadingsArr as $k=>$v){
                                              $sel_holladayOS = '';
                                              if($v[2]==$holladayOS) $sel_holladayOS = ' selected';
                                              echo '<option value="'.$v[2].'"'.$sel_holladayOS.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && ($srk_tOS==$srk_tOSC||$srk_tOS==getFormulaHeadName($srk_tOSC))){echo "backgroundLightGray";}else{echo $classText;}?>" name="srk_tOS" id="srk_tOS">
                                          <?php
                                          if($srk_tOS=='') $srk_tOS = "SRK-T";
                                          foreach($formulaHeadingsArr as $k=>$v){
                                              $sel_srk_tOS = '';
                                              if($v[2]==$srk_tOS) $sel_srk_tOS = ' selected';
                                              echo '<option value="'.$v[2].'"'.$sel_srk_tOS.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && ($hofferOS==$hofferOSC||$hofferOS = getFormulaHeadName($hofferOSC))){echo "backgroundLightGray";}else{echo $classText;}?>" name="hofferOS" id="hofferOS">
                                          <?php
                                          if($hofferOS=='') $hofferOS = "HOFFER";
                                          foreach($formulaHeadingsArr as $k=>$v){
                                              $sel_hofferOS = '';
                                              if($v[2]==$hofferOS) $sel_hofferOS = ' selected';
                                              echo '<option value="'.$v[2].'"'.$sel_hofferOS.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row" id="iolOS1">
                                	<!--OS1-->
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && (($iol1OS==$iol1OSC) || ($providerLensesArrOS[0]==$iol1OSC))){echo "backgroundLightGray";}else{echo $classText;}?>" name="iol1OS" id="iol1OS" onChange="return listLenses1('1', 'iol1OS');"><option value=""></option>
                                          <?php
                                          foreach($lensesArray as $k=>$v){
                                              $sel_lensesArray = '';
                                              if($v[2]==$providerLensesArrOS[0]) $sel_lensesArray = ' selected';
                                              echo '<option value2="'.$v[3].'" value="'.$v[2].'"'.$sel_lensesArray.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol1PowerOS==$iol1PowerOSC){echo "backgroundLightGray";}?>" name="iol1PowerOS" id="iol1PowerOS" value="<?php echo $iol1PowerOS; ?>" onKeyDown="return checkIolVal('iol1OS');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol1HolladayOS==$iol1HolladayOSC){echo "backgroundLightGray";}?>" name="iol1HolladayOS" id="iol1HolladayOS" value="<?php echo $iol1HolladayOS; ?>" onKeyDown="return checkIolVal('iol1OS');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol1srk_tOS==$iol1srk_tOSC){echo "backgroundLightGray";}?>" name="iol1srk_tOS" id="iol1srk_tOS" value="<?php echo $iol1srk_tOS; ?>" onKeyDown="return checkIolVal('iol1OS');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol1HofferOS==$iol1HofferOSC){echo "backgroundLightGray";}?>" name="iol1HofferOS" id="iol1HofferOS" value="<?php echo $iol1HofferOS; ?>" onKeyDown="return checkIolVal('iol1OS');"></div>
                                	<!--OS1-->
                                </div>
                                <div class="row" id="iolOS2">
                                    <!--OS2-->
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && (($iol2OS==$iol2OSC) || ($providerLensesArrOS[1]==$iol2OSC))){echo "backgroundLightGray";}else{echo $classText;}?>" name="iol2OS" id="iol2OS" onChange="return listLenses1('2', 'iol2OS');"><option value=""></option>
                                          <?php
                                          foreach($lensesArray as $k=>$v){
                                              $sel_lensesArray = '';
                                              if($v[2]==$providerLensesArrOS[1]) $sel_lensesArray = ' selected';
                                              echo '<option value2="'.$v[3].'" value="'.$v[2].'"'.$sel_lensesArray.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol2PowerOS==$iol2PowerOSC){echo "backgroundLightGray";}?>" name="iol2PowerOS" id="iol2PowerOS" value="<?php echo $iol2PowerOS; ?>" onKeyDown="return checkIolVal('iol2OS');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol2HolladayOS==$iol2HolladayOSC){echo "backgroundLightGray";}?>" name="iol2HolladayOS" id="iol2HolladayOS" value="<?php echo $iol2HolladayOS; ?>" onKeyDown="return checkIolVal('iol2OS');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol2srk_tOS==$iol2srk_tOSC){echo "backgroundLightGray";}?>" name="iol2srk_tOS" id="iol2srk_tOS" value="<?php echo $iol2srk_tOS; ?>" onKeyDown="return checkIolVal('iol2OS');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol2HofferOS==$iol2HofferOSC){echo "backgroundLightGray";}?>" name="iol2HofferOS" id="iol2HofferOS" value="<?php echo $iol2HofferOS; ?>" onKeyDown="return checkIolVal('iol2OS');"></div>
                                    <!--OS2-->
    							</div>
                                <div class="row" id="iolOS3">
                                    <!--OS3-->
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && (($iol3OS==$iol3OSC) || ($providerLensesArrOS[2]==$iol3OSC))){echo "backgroundLightGray";}?>" name="iol3OS" id="iol3OS" onChange="return listLenses1('3', 'iol3OS');"><option value=""></option>
                                          <?php
                                          foreach($lensesArray as $k=>$v){
                                              $sel_lensesArray = '';
                                              if($v[2]==$providerLensesArrOS[2]) $sel_lensesArray = ' selected';
                                              echo '<option value2="'.$v[3].'" value="'.$v[2].'"'.$sel_lensesArray.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol3PowerOS==$iol3PowerOSC){echo "backgroundLightGray";}?>" name="iol3PowerOS" id="iol3PowerOS" value="<?php echo $iol3PowerOS; ?>" onKeyDown="return checkIolVal('iol3OS');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol3HolladayOS==$iol3HolladayOSC){echo "backgroundLightGray";}?>" name="iol3HolladayOS" id="iol3HolladayOS" value="<?php echo $iol3HolladayOS; ?>" onKeyDown="return checkIolVal('iol3OS');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol3srk_tOS==$iol3srk_tOSC){echo "backgroundLightGray";}?>" name="iol3srk_tOS" id="iol3srk_tOS" value="<?php echo $iol3srk_tOS; ?>" onKeyDown="return checkIolVal('iol3OS');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol3HofferOS==$iol3HofferOSC){echo "backgroundLightGray";}?>" name="iol3HofferOS" id="iol3HofferOS" value="<?php echo $iol3HofferOS; ?>" onKeyDown="return checkIolVal('iol3OS');"></div>
                                    <!--OS3-->
        						</div>
                                <div class="row" id="iolOS4">
                                    <!--OS4-->
                                    <div class="col-sm-2">
                                        <select class="form-control minimal <?php if($rowsCount>0 && (($iol4OS==$iol4OSC) || ($providerLensesArrOS[3]==$iol4OSC))){echo "backgroundLightGray";}else{echo $classText;}?>" name="iol4OS" id="iol4OS" onChange="return listLenses1('4', 'iol4OS');"><option value=""></option>
                                          <?php
                                          foreach($lensesArray as $k=>$v){
                                              $sel_lensesArray = '';
                                              if($v[2]==$providerLensesArrOS[3]) $sel_lensesArray = ' selected';
                                              echo '<option value2="'.$v[3].'" value="'.$v[2].'"'.$sel_lensesArray.'>'.$v[0].'</option>';
                                          }
                                          ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol4PowerOS==$iol4PowerOSC){echo "backgroundLightGray";}?>" name="iol4PowerOS" id="iol4PowerOS" value="<?php echo $iol4PowerOS; ?>" onKeyDown="return checkIolVal('iol4OS');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol4HolladayOS==$iol4HolladayOSC){echo "backgroundLightGray";}?>" name="iol4HolladayOS" id="iol4HolladayOS" value="<?php echo $iol4HolladayOS; ?>" onKeyDown="return checkIolVal('iol4OS');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol4srk_tOS==$iol4srk_tOSC){echo "backgroundLightGray";}?>" name="iol4srk_tOS"  id="iol4srk_tOS" value="<?php echo $iol4srk_tOS; ?>" onKeyDown="return checkIolVal('iol4OS');"></div>
                                    <div class="col-sm-2"><input type="text" class="form-control <?php if($rowsCount>0 && $iol4HofferOS==$iol4HofferOSC){echo "backgroundLightGray";}?>" name="iol4HofferOS" id="iol4HofferOS" value="<?php echo $iol4HofferOS; ?>" onKeyDown="return checkIolVal('iol4OS');"></div>
                                    <!--OS4-->
                            	</div>
	                            <div class="clearfix"></div>
    
                                <div class="performedBy">
                                    <div class="row">
                                        <div class="col-sm-2"><label>Cell Count</label> <input type="text" class="form-control <?php if($rowsCount>0 && $cellCountOS==$cellCountOSC){echo "backgroundLightGray";}?>" name="cellCountOS" id="cellCountOS" value="<?php echo $cellCountOS; ?>"></div>
                                        <div class="col-sm-2">
                                            <label>Pachymetry</label> 
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control <?php if($rowsCount>0 && $pachymetryValOS==$pachymetryValOSC){echo "backgroundLightGray";}?>" name="pachymetryValOS" id="pachymetryValOS" value="<?php echo $pachymetryValOS; ?>" onChange="calCorrectionVal(this.value,'OS');"></div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control <?php if($rowsCount>0 && $pachymetryCorrecOS==$pachymetryCorrecOSC){echo "backgroundLightGray";}?>" name="pachymetryCorrecOS" id="pachymetryCorrecOS" value="<?php echo $pachymetryCorrecOS; ?>"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2"><label>Corneal Diam</label> <input type="text" class="form-control <?php if($rowsCount>0 && $cornealDiamOS==$cornealDiamOSC){echo "backgroundLightGray";}?>" name="cornealDiamOS" id="cornealDiamOS" value="<?php echo $cornealDiamOS; ?>"></div>
                                        <div class="col-sm-2"><label>Dominant Eye</label> <input type="text" class="form-control <?php if($rowsCount>0 && $dominantEyeOS==$dominantEyeOSC){echo "backgroundLightGray";}?>" name="dominantEyeOS" id="dominantEyeOS" value="<?php echo $dominantEyeOS; ?>"></div>
                                        <div class="col-sm-2"><label>ACD Eye</label> <input type="text" class="form-control <?php if($rowsCount>0 && $acdOS==$acdOSC){echo "backgroundLightGray";}?>" name="acdOS" id="acdOS" value="<?php echo $acdOS; ?>"></div>
                                        <div class="col-sm-2"><label>White to White</label> <input type="text" class="form-control <?php if($rowsCount && $w2wOS==$w2wOSC){echo "backgroundLightGray";}?>" name="w2wOS" id="w2wOS" value="<?php echo $w2wOS; ?>"></div>
                                        <div class="col-sm-4">
                                            <label>Pupil Size</label>
                                            <div class="row">
                                                <div class="col-sm-6"><input type="text" class="form-control <?php if($rowsCount>0 && $pupilSize1OS==$pupilSize1OSC){echo "backgroundLightGray";} ?>" name="pupilSize1OS" id="pupilSize1OS" value="<?php echo $pupilSize1OS; ?>"><small>Un-Dilated</small></div>
                                                <div class="col-sm-1">/</div>
                                                <div class="col-sm-5"><input type="text" class="form-control <?php if($rowsCount>0 && $pupilSize2OS==$pupilSize2OSC){echo "backgroundLightGray";}?>" name="pupilSize2OS" id="pupilSize2OS" value="<?php echo $pupilSize2OS; ?>"><small>Dilated</small></div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-sm-12"><label>Notes</label> <textarea rows="3" name="notesOS" id="notesOS" class="form-control" onClick="return makeEmpty(this)" placeholder="Notes..."><?php echo $notesOS;?></textarea></div>
                                    </div>
                                </div>
                            	<div class="clearfix"></div>
                            
                            	<div class="table-responsive plntabl">
                                <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td class="tstschrht" width="22%">CC</td>
                                    <td colspan="2">Scheduled for Intraocular lens implant A/Scan reviewed and IOL Selected.</td>
                                </tr>
                                <tr>
                                    <td class="tstschrht" width="22%">Assessment</td>
                                    <td colspan="2" class="tstrstopt">
                                        <div class="row">
                                        <div class="col-sm-3"><label><input type="checkbox" name="cataractOS" id="cataractOS" <?php if($cataractOS==1) echo "CHECKED"; ?>><span class="label_txt">Cataract</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="astigmatismOS" id="astigmatismOS" <?php if($astigmatismOS==1) echo "CHECKED"; ?>><span class="label_txt">Astigmatism</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="myopiaOS" id="myopiaOS" <?php if($myopiaOS==1) echo "CHECKED"; ?>><span class="label_txt">Myopia</span></label></div>
                                    </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tstschrht" width="22%">Plan</td>
                                    <td colspan="2" class="form-inline">
                                    <div class="form-group">
                                        <select style="width:100px !important" class="form-control minimal" name="selecedIOLsOS" id="selecedIOLsOS" onChange="return doSelectOS(this)">
                                            <option value=""></option>
                                            <?php $lensesProviderArray = getLenses($provider_idOS);
                                                if((($lensesProviderArray)) != (($providerLensesArrOS))){
                                                    $lensesProviderOSArray = $providerLensesOSArr;
                                                }
                                                if(count($lensesProviderOSArray)>0){
													foreach($lensesProviderOSArray as $lensesId => $lenseNames){?>
														<option value="<?php echo $lensesId; ?>" style="color:<?php if($selecedIOLsOS==$lensesId) echo "009900"; else echo "000000"; ?>;" <?php  if($selecedIOLsOS==$lensesId) echo "SELECTED"; ?>><?php echo $lenseNames; ?></option>
													<?php }
                                                }?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Date of Surgery</label>
                                    </div>
                                    <div class="form-group">
                                        <input id="sur_dateOS" type="text"  name="sur_dateOS" value="<?php echo $sur_dateOS;?>"  onBlur="checkdate(this);" maxlength=10 class="form-control datePicker">
                                    </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tstschrht" width="22%">Procedure</td>
                                    <td colspan="2" class="tstrstopt">
                                    <?php if(!empty($elem_proc_os)){
										$arrTmp = explode(",",$elem_proc_os);

										if(in_array("Phaco",$arrTmp)){
											$elem_proc_os_1 = "checked";
										}
										if(in_array("Complex Phaco",$arrTmp)){
											$elem_proc_os_2 = "checked";
										}
										if(in_array("Combined Procedure",$arrTmp)){
											$elem_proc_os_3 = "checked";
										}
									}?>
                                        <div class="row">
                                        <div class="col-sm-3"><label><input type="checkbox" id="elem_proc_os_1" name="elem_proc_os[]" value="Phaco" <?php echo $elem_proc_os_1 ; ?>><span class="label_txt">Phaco</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_proc_os_2" name="elem_proc_os[]" value="Complex Phaco" <?php echo $elem_proc_os_2 ; ?>><span class="label_txt">Complex Phaco</span></label></div>
                                        <div class="col-sm-5"><label><input type="checkbox" id="elem_proc_os_3" name="elem_proc_os[]" value="Combined Procedure" <?php echo $elem_proc_os_3 ; ?>><span class="label_txt">Combined Procedure</span></label></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tstschrht" width="22%">Anesthesia</td>
                                    <td colspan="2" class="tstrstopt">
                                        <?php if(!empty($elem_anes_os)){
											$arrTmp = explode(",",$elem_anes_os);
											if(in_array("Local",$arrTmp)){
												$elem_anes_os_1 = "checked";
											}
											if(in_array("Topical",$arrTmp)){
												$elem_anes_os_2 = "checked";
											}
										}?>
                                        <div class="row">
                                        <div class="col-sm-3"><label><input type="checkbox" id="elem_anes_os_1" name="elem_anes_os[]" value="Local" <?php echo $elem_anes_os_1;?>><span class="label_txt">Local</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_anes_os_2" name="elem_anes_os[]" value="Topical" <?php echo $elem_anes_os_2;?>><span class="label_txt">Topical</span></label></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="tstschrht">Viscoelastic</td>
                                    <td colspan="2" class="tstrstopt">
                                        <?php if(empty($elem_visc_os_other)){$elem_visc_os_other = "Other";}
											if(!empty($elem_visc_os)){
												$arrTmp = explode(",",$elem_visc_os);
												if(in_array("Discovisc",$arrTmp)||in_array("Duovisc",$arrTmp)){$elem_visc_os_1="checked";}
												if(in_array("Viscoat",$arrTmp)){$elem_visc_os_2="checked";}
												if(in_array("Amvisc plus",$arrTmp)){$elem_visc_os_3="checked";}
												if(in_array("Healon",$arrTmp)){$elem_visc_os_4="checked";}
											}?>
                                        <div class="row">
                                        <div class="col-sm-3"><label><input type="checkbox" id="elem_visc_os_1" name="elem_visc_os[]" value="Duovisc" <?php echo $elem_visc_os_1;?>><span class="label_txt">Duovisc</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_visc_os_2" name="elem_visc_os[]" value="Viscoat" <?php echo $elem_visc_os_2 ; ?>><span class="label_txt">Viscoat</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_visc_os_3" name="elem_visc_os[]" value="Amvisc plus" <?php echo $elem_visc_os_3 ; ?>><span class="label_txt">Amvisc plus</span></label></div>
                                        <div class="col-sm-3"><label><input type="checkbox" id="elem_visc_os_4" name="elem_visc_os[]" value="Healon" <?php echo $elem_visc_os_4 ; ?>><span class="label_txt">Healon</span></label></div>
                                        <div class="col-sm-4"><input type="text" id="elem_visc_os_other" name="elem_visc_os_other" value="<?php echo $elem_visc_os_other ; ?>" onFocus="if(this.value=='Other')this.value='';" class="form-control"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="tstrstopt">
                                        <div class="row">
                                        <div class="col-sm-4" title="Limbal Relaxing Incision"><label><input type="checkbox" name="lriOS" id="lriOS" <?php if($lriOS==1){ echo "CHECKED"; } ?> title="Limbal Relaxing Incision" onClick="return showHide('lriOSTr', 'lriOS', 'cutsValueOSTr');"><span class="label_txt">LRI</span></label></div>
                                        <div class="col-sm-4" title="Deluxe IOL"><label><input type="checkbox" name="dlOS" id="dlOS" <?php if($dlOS==1){ echo "CHECKED"; } ?>><span class="label_txt">DL</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="synechiolysisOS" id="synechiolysisOS" <?php if($synechiolysisOS==1){ echo "CHECKED"; } ?>><span class="label_txt">Synechiolysis</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="irishooksOS" id="irishooksOS" <?php if($irishooksOS==1){ echo "CHECKED"; } ?>><span class="label_txt">IRIS Hooks</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="trypanblueOS" id="trypanblueOS" <?php if($trypanblueOS==1){ echo "CHECKED"; } ?>><span class="label_txt">Trypan Blue</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="flomaxOS" id="flomaxOS" <?php if($flomaxOS==1){ echo "CHECKED"; } ?>><span class="label_txt">Pt. On Flomax</span></label></div>
                                        </div>
                                       <?php if(empty($elem_opts_os_other)){$elem_opts_os_other = "Other";}
										if(!empty($elem_opts_os)){
											$arrTmp = explode(",",$elem_opts_os);
											if(in_array("Malyugin ring",$arrTmp)){$elem_opts_os_1="checked";}
											if(in_array("Shugarcaine",$arrTmp)){$elem_opts_os_2="checked";}
											if(in_array("Capsule tension rings",$arrTmp)){$elem_opts_os_3="checked";}
											if(in_array("IOL Cutter",$arrTmp)){$elem_opts_os_4="checked";}
										}?>
                                        <div class="row">
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_opts_os_1" <?php echo $elem_opts_os_1;?> name="elem_opts_os[]" value="Malyugin ring"><span class="label_txt">Malyugin ring</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_opts_os_2" <?php echo $elem_opts_os_2;?> name="elem_opts_os[]" value="Shugarcaine"><span class="label_txt">Shugarcaine</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_opts_os_3" <?php echo $elem_opts_os_3;?> name="elem_opts_os[]" value="Capsule tension rings"><span class="label_txt">Capsule tension rings</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" id="elem_opts_os_4" <?php echo $elem_opts_os_4;?> name="elem_opts_os[]" value="IOL Cutter"><span class="label_txt">IOL Cutter</span></label></div>
                                        <div class="col-sm-4"><input type="text" id="elem_opts_os_other" class="form-control" name="elem_opts_os_other" value="<?php echo $elem_opts_os_other;?>" onfocus="if(this.value=='Other')this.value='';"></div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                                </table>
                            	</div>
                                <div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-sm-7"><strong>IOL master to be done on day of surgery</strong></div>
                                    <div class="col-sm-5">
                                    <?php if($elem_iol2dn_os == "Yes"){$elem_iol2dn_os_1 = "checked";}
									if($elem_iol2dn_os == "No"){$elem_iol2dn_os_2 = "checked";}?>
                                        <div class="row tstrstopt">
                                        <div class="col-sm-6"><label><input type="checkbox" id="elem_iol2dn_os_1" <?php echo $elem_iol2dn_os_1;?>	name="elem_iol2dn_os" value="Yes" onclick="if(this.checked)document.getElementById('elem_iol2dn_os_2').checked=!this.checked;"><span class="label_txt">Yes</span></label></div>
                                        <div class="col-sm-6"><label><input type="checkbox" id="elem_iol2dn_os_2" <?php echo $elem_iol2dn_os_2;?> name="elem_iol2dn_os" value="No" onClick="if(this.checked)document.getElementById('elem_iol2dn_os_1').checked=!this.checked;"><span class="label_txt">No</span></label></div>
                                        </div>
                                    </div>
                                
                                </div>
                                <div class="clearfix"></div>
                                
                                <textarea class="form-control" name="notesAssesmentPlansOS" id="notesAssesmentPlansOS" rows="3" onClick="return makeEmpty(this)"><?php if($notesAssesmentPlansOS!='') echo $notesAssesmentPlansOS; else echo "Notes..."; ?></textarea>
                                
                                <div class="row" id="lriOSTr" <?php if($lriOS!=1){ echo "style=\"display:none;\"";  }?>>
                                    <div class="col-sm-1 text-right">Cuts:</div>
                                    <div class="col-sm-2">
                                        <select name="cutsOS" id="cutsOS" class="form-control minimal" onChange="return showCutsChk('cutsValueOSTr', 'cutsOS');">
                                            <option value=""></option>
                                            <option value="1" <?php if($cutsOS==1) echo "SELECTED"; ?>>1</option>
                                            <option value="2" <?php if($cutsOS==2) echo "SELECTED"; ?>>2</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-1 text-right">Length:</div>
                                    <div class="col-sm-2"><input type="text" class="form-control" name="lengthCutsOS" id="lengthCutsOS" value="<?php echo $lengthOS; ?>"></div>
                                    <div class="col-sm-2">
                                        <select name="lengthOSSelect" id="lengthOSSelect" class="form-control minimal">
                                            <option value="mm" <?php if($lengthTypeOS=="mm") echo "SELECTED"; ?>>mm</option>
                                            <option value="degree" <?php if($lengthTypeOS=="degree") echo "SELECTED"; ?>>&#176;</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-1 text-right">Axis:</div>
                                    <div class="col-sm-1"><input type="text" name="axisOS" id="axisOS" class="form-control" value="<?php echo $axisOS; ?>"></div>
                                </div>
                            
                            	<div class="row tstrstopt" id="cutsValueOSTr" <?php if($cutsOS!=1){ echo "style=\"display:none;\"";}?>>
                                    <div class="col-sm-3"><label><input type="checkbox" name="superiorOS" id="superiorOS" <?php if($superiorOS==1) echo "Checked"; ?>><span class="label_txt">Superior</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="inferiorOS" id="inferiorOS" <?php if($inferiorOS==1) echo "Checked"; ?>><span class="label_txt">Inferior</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="nasalOS" id="nasalOS" <?php if($nasalOS==1) echo "Checked"; ?>><span class="label_txt">Nasal</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="temporalOS" id="temporalOS" <?php if($temporalOS==1) echo "Checked"; ?>><span class="label_txt">Temporal</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="STOS" id="STOS" <?php if($STOS==1) echo "Checked"; ?>><span class="label_txt">ST</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="SNOS" id="SNOS" <?php if($SNOS==1) echo "Checked"; ?>><span class="label_txt">SN</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="ITOS" id="ITOS" <?php if($ITOS==1) echo "Checked"; ?>><span class="label_txt">IT</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" name="INOS" id="INOS" <?php if($INOS==1) echo "Checked"; ?>><span class="label_txt">IN</span></label></div>
                                </div>
                            	
                    			<div class="clearfix"></div>
                    			<?php if($callFromInterface != 'admin'){?>
                                <div class="row">
                                    <div class="col-sm-4 osphyname">Physician Name:</div>
                                    <div class="col-sm-8 mt5">
                                    	<select class="form-control minimal" name="physicianSelectedOS" id="physicianSelectedOS">
                                            <option value=""></option>
                                            <?php $tmp = $signedByOSId;
                                            foreach($phyArray as $pId => $physicianNameOS){?>
                                            <option value="<?php echo $pId; ?>" <?php if($tmp==$pId) echo "SELECTED"; ?>><?php echo $physicianNameOS; ?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
				</div>
                
                <div class="row">
                    <div class="form-group">
                        <?php  if($callFromInterface != 'admin'){
							$sigUserType = $userType;
                            require_once(dirname(__FILE__)."/test_signature.php");
						}
                        ?>
                    </div>
                	
                    <div class="clearfix"></div>
					<?php if($callFromInterface != 'admin'){?>
                    <div class="col-sm-12 supperbill">
                        <!-- Superbill -->
                        <div id="superbill">                            
                            <script>
                                $('#superbill').load(zPath+'/chart_notes/onload_wv.php',{
                                                'elem_action':"GetSuperBill",
                                                'sb_testName':"<?php echo $sb_testName;?>",
                                                'thisCptDescSym':"<?php echo $thisCptDescSym;?>",
                                                'encounterId':"<?php echo $encounterId;?>",
                                                'test_form_id':"<?php echo $test_form_id;?>"
                                                });
                            </script>
                        </div>
                        <!-- Superbill -->
                        <div class="clearfix"></div>
                        <?php echo $objTests->get_zeiss_forum_button('A/SCAN',$test_edid,$patient_id);?>
                    </div>
                    <?php }?>
                    
                </div>
                
            </div>
            <?php 
                            $testprint_name = "Ascan";
                            $testprint_id = $test_edid;
							$test_scan_edit_id_scan = $surgical_id;
							if($callFromInterface != 'admin' && $doNotShowRightSide!='yes') {
								require_once("test_saved_list.php");
							}
			?>
            							
							<?php if($callFromInterface != 'admin'){?>
				
							<?php if(($elem_per_vo != "1" && $doNotShowRightSide == 'yes') || $_REQUEST['pop']==1 ){?>
                                    <?php
                                        $btnHide="";
                                        $btnPurgeVal="Purge";
                                        //Purged
                                        if(!empty($purged)){
                                                $btnHide=" hide";	
                                                $btnPurgeVal="UndoPurge";
                                        }
                                    ?>
                                    
                                    <div class="mainwhtbox" style="padding-left:10px; padding-right:10px;">																	
                                    <div class="row">
                                    <div class="col-sm-12 ad_modal_footer text-center" id="module_buttons">
                                    <?php if($elem_per_vo != "1"){ ?>
                                    <input type="button" class="btn btn-success" value="<< Previous" id="btnPrev"  onClick="setPrevValues('-1')" />
                                    <?php if($flg_interpreted_btn){?>
                                        <input type="button" class="btn btn-success<?php echo $btnHide;?>" id="btn_interpret"  value="Interpreted" onClick="test_interpreted()" />
                                    <?php }else{?>
                                        <input type="button" class="btn btn-success<?php echo $btnHide;?>" id="btn_done" value="Done"  onClick="saveAscan()" />
                                    <?php }?>
                                    <?php }?>
                                    <input type="button"  class="btn btn-danger pull-right" value="Cancel" id="Close" onClick="funWinClose();">			
            
                                    <?php if($elem_per_vo != "1"){ ?>
                                        <input type="button"  class="btn btn-success<?php echo $btnHide;?>" value="Reset" id="btnReset" onClick="resetTestExam();" />
                                    <?php } ?>			
            
                                    <input type="button"  class="btn btn-success"  value="ePost" id="btnEPost" onClick="epostpopTest();" />
                                    <input type="button" class="btn btn-success" align="bottom" name="btnPrint" id="btnPrint" onclick="printTest();" value="Print"/>
                                    <?php if($elem_per_vo != "1"){ ?>
                                        <input type="button"  class="btn btn-success"  value="<?php echo $btnPurgeVal;?>" id="btnPurge" onClick="resetTestExam(1);" />
                                        <input type="button"  class="btn btn-success<?php echo $btnHide;?>" value="Order" id="save" onClick="saveAscan()" />
                                        <input type="button"  class="btn btn-success" value="Next >>" id="btnNxtTst" onClick="setPrevValues('+1')"/>
                                    <?php } ?>
            
                                    </div>
                                </div>
                                </div>			
                            <?php } else { ?>
                                <?php if($noP=='1'){?>
                                <script>
                                    var btnArr = new Array();
									btnArr["purged"] = "<?php echo $purged; ?>";
									btnArr["elem_per_vo"] = elem_per_vo;
									btnArr["rtpath"] = zPath;
									btnArr["interpreted"] = "<?php echo $flg_interpreted_btn; ?>";
									top.btn_show("ASCAN",btnArr);
                                </script>	
                                <?php } ?>
                            <?php } ?>
                            
                            
                            
                            <?php }?>            
            
        </div>
	</div>	
</div>
</form>
<?php if($callFromInterface != 'admin'){echo $objEpost->getEposts();}?>
<script>
$(document).ready(function(e) {
    $("textarea").each(function(){
        $(this).attr('data-provide','multiple');
        $(this).attr('data-seperator',',');
    });
	$('[data-toggle="tooltip"]').tooltip()
	$("#content-1").mCustomScrollbar({theme:"minimal"});
    //init_page_display();
	var date_global_format = 'm-d-Y';
	if(typeof(top.jquery_date_format)=='string'){
		var date_global_format = top.jquery_date_format;
	}else if(typeof(window.top.opener.top.jquery_date_format)=='string'){
		var date_global_format = window.top.opener.top.jquery_date_format;
	}else if(typeof(window.top.opener.opener.top.jquery_date_format)=='string'){
		var date_global_format = window.top.opener.opener.top.jquery_date_format;
	}
	$('.datePicker').datetimepicker({
		timepicker:false,
		format:date_global_format,
		formatDate:'Y-m-d',
		scrollInput:false
	})
	<?php if($countRows > 0){?>set_initial_some_text_bg();<?php }?>
});

<?php if($noP=='1'){?>
var btnArr = new Array();
btnArr["purged"] = "<?php echo $purged; ?>";
btnArr["elem_per_vo"] = elem_per_vo;
btnArr["rtpath"] = zPath;
btnArr["interpreted"] = "<?php echo $flg_interpreted_btn; ?>";
top.btn_show("ASCAN",btnArr);
<?php } ?>

<?php if($callFromInterface != 'admin'){?>
	top.set_header_title('<?php echo $this_test_screen_name;?>');
	<?php if(($elem_topoMeterEye == "OD") || ($elem_topoMeterEye == "OS")){echo "setReli_Test(\"".$elem_topoMeterEye."\");";}?>
<?php } ?>
var control_array = new Array('mrSOD','mrCOD','mrAOD','visionOD','glareOD','mrSOS','mrCOS','mrAOS','visionOS','glareOS');
function set_initial_some_text_bg(){
	$(control_array).each(function(i, ele) {
        ci = control_array[i];
		$('#'+ci).css('background-color','#B9CAF7');
    });
}
</script>
<?php
if($callFromInterface != 'admin'){
	if($_GET["tId"]!=''){	// position of include file cannot go above than these lines
		include 'ascan_print.php';
	}
}
?>
</body>
</html>