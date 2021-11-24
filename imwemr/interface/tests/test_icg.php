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
require_once("../../library/classes/work_view/Epost.php");
$callFromInterface		= strtolower(trim(strip_tags($_GET['callFromInterface'])));
$doNotShowRightSide 	= strtolower(trim($_REQUEST['doNotShowRightSide']));

$library_path 			= $GLOBALS['webroot'].'/library';
$patient_id				= $_SESSION['patient'];
$objTests				= new Tests;
$testname               = "ICG";
$objEpost				= new Epost($patient_id,$testname);
//$objTests->patient_id 	= $patient_id;

//MAKING OBJECT TO SAVE IMAGE FILES
$oSaveFile = new SaveFile($patient_id);
    
$test_table_name		= 'icg';
$this_test_properties	= $objTests->get_table_cols_by_test_table_name($test_table_name);
$this_test_screen_name	= $this_test_properties['temp_name'];
$test_master_id			= $this_test_properties['id'];
//User and  User_type
$logged_user 	= $objTests->logged_user;
$userType 		= $objTests->logged_user_type;

if($callFromInterface != 'admin'){
        //================= GETTING PATIENT DATA
	$getPatientDataStr = "SELECT * FROM patient_data WHERE id = '$patient_id'";
	$getPatientDataQry = imw_query($getPatientDataStr);
	$getPatientDataRow = imw_fetch_array($getPatientDataQry);
	$patFname = $getPatientDataRow['fname'];
	$patMname = $getPatientDataRow['mname'];
	$patLname = $getPatientDataRow['lname'];
	$patientName = $patFname." ".$patMname." ".$patLname;
    
	$elem_per_vo			= $objTests->get_tests_VO_access_status();
	
	//iMedicMonitor status.
	$objTests->patient_whc_room();
	
	//----GET ALL ACTIVE TESTS FROM ADMIN------
	$ActiveTests			= $objTests->get_active_tests();

    // FILE NAME for PRINT
    $date_f=date("Y_m_d");
	$rand=rand(0,500);
	$test_name="chart_tests";
	$html_file_name = $test_name.'/'.$date_f.'/icg_test_print_'. $_SESSION['patient']."_".$_SESSION['authId']."_".$rand;
	$objTests->mk_print_folder($test_name,$date_f,$oSaveFile->upDir."/UserId_".$_SESSION['authId']."/tmp/");
	$final_html_file_name_path = $oSaveFile->upDir."/UserId_".$_SESSION['authId']."/tmp/".$html_file_name;
	// FILE NAME for PRINT
	/*$date_f=date("Y_m_d");
	$rand=rand(0,500);
	$test_name="chart_tests";
	$html_file_name = $test_name.'/'.$date_f.'/icg_test_print_'.$_SESSION['authId']."_".$rand;
	mk_print_folder($test_name,$date_f,'common');*/
	
	//Retain QueryString
	$qstr4js="";
	if(isset($_SERVER["QUERY_STRING"]) && !empty($_SERVER["QUERY_STRING"])){
		$qstr4js .= "&".$_SERVER["QUERY_STRING"];
	}
	//Retain QueryString
	
	$elem_examDate = get_date_format(date('Y-m-d'));
	$elem_examTime = date('Y-m-d H:i:s'); //time();
	$elem_opidTestOrderedDate = ""; // $elem_examDate;

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


	
	//Previous Test --
	$oChartTestPrev = new ChartTestPrev($patient_id,"ICG");
	
	//No Pop
	$noP = 1;
	if(isset($_GET["noP"]) && !empty($_GET["noP"])){
		$noP = $_GET["noP"];
	}

	//Test id
	$tId = 0;
	if(isset($_GET["tId"])){
		$tId = $_GET["tId"];
		//if you come directly without chart
		//Set Form Id zero
		$form_id = 0;
	}

	if(!empty($form_id)){
		//Get Form id based on patient id
		$q = "SELECT * FROM ".$test_table_name." WHERE ".$this_test_properties['patient_key']." = '".$patient_id."' AND formId = '$form_id' AND purged='0' AND del_status='0'";
		$res = imw_query($q);
		$row = imw_fetch_assoc($res);
	}else if(isset($tId) && !empty($tId)){//Get record based on patient id and test id
			$q = "SELECT * FROM ".$test_table_name." WHERE ".$this_test_properties['patient_key']." = '$patient_id' AND ".$this_test_properties['test_table_pk_id']." = '".$tId."' ";
			$res = imw_query($q);
			$row = imw_fetch_assoc($res);
	}else{
		$row = false; // open new test for patient
	}


	if($row == false){	//&& ($finalize_flag == 0)
		//SET MODE
		$icg_mode = "new";
		$test_edid = "0";
	}else{
		$icg_mode = "update";
		$test_edid = $row["icg_id"];
	}

	//Default
	if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){//New Recors
		$tmp = ($row != false) ? $row["examDate"] : "";
		$row = $objTests->valuesNewRecordsTests($test_table_name, $patient_id, " * ",$tmp);
	}


	if($row != false){
		$icg_pkid = $row["icg_id"];
		$test_form_id = $row["form_id"];
		$elem_examDate= ($icg_mode != "new") ? get_date_format($row["exam_date"]) : $elem_examDate;
		$elem_examTime = ($icg_mode != "new") ? $row["examTime"] : $elem_examTime ;
		$elem_icg=$row["icg"];
		$elem_icg_od=$row["icg_od"];
		$elem_icg_early=$row["icg_early"];
		$elem_icg_extra=$row["icg_extra"];
		$elem_comments_icg=stripslashes($row["comments_icg"]);
		$elem_performedBy= ($icg_mode != "new") ? $row["performed_by"] : "" ;
		$elem_pa_under=$row["pa_under"];
		$elem_pa_inter=$row["pa_inter"];
		$elem_pa_inter1=$row["pa_inter1"];
		$elem_disc_od=$row["disc_od"];
		$elem_disc_cd_od=$row["disc_cd_od"];
		$elem_retina_od=$row["retina_od"];
		$elem_macula_od=$row["macula_od"];
		$elem_testresults_desc_od=stripslashes($row["testresults_desc_od"]);
		$elem_disc_os=$row["disc_os"];
		$elem_disc_cd_os=$row["disc_cd_os"];
		$elem_retina_os=$row["retina_os"];
		$elem_macula_os=$row["macula_os"];
		$elem_testresults_desc_os=stripslashes($row["testresults_desc_os"]);
		$elem_Stable=$row["Stable"];
		$elem_MonitorAg=$row["MonitorAg"];
		$elem_FuApa=$row["FuApa"];
		$elem_PatientInformed=$row["PatientInformed"];
		$elem_ArgonLaser=$row["ArgonLaser"];
		$elem_ArgonLaserEye=$row["ArgonLaserEye"];
		$elem_ArgonLaserEyeOptions=$row["ArgonLaserEyeOptions"];
		$elem_FuRetina=$row["FuRetina"];
		$elem_FuRetinaComments=$row["FuRetinaComments"];
		$elem_icg_signature=$row["icg_signature"];
		$elem_physician= ( $icg_mode == "update" ) ? $row["phy"] : "" ;
	
		$Sharp_Pink_OD=$row["Sharp_Pink_OD"];
		$Pale_OD=$row["Pale_OD"];
		$Large_Cap_OD=$row["Large_Cap_OD"];
		$Sloping_OD=$row["Sloping_OD"];
		$Notch_OD=$row["Notch_OD"];
		$NVD_OD=$row["NVD_OD"];
		$Leakage_OD=$row["Leakage_OD"];
		$Retina_Hemorrhage_OD=$row["Retina_Hemorrhage_OD"];
		$Retina_Microaneurysms_OD=$row["Retina_Microaneurysms_OD"];
		$Retina_Exudates_OD=$row["Retina_Exudates_OD"];
		$Retina_Laser_Scars_OD=$row["Retina_Laser_Scars_OD"];
		$Retina_NEVI_OD=$row["Retina_NEVI_OD"];
		$Retina_SRVNM_OD=$row["Retina_SRVNM_OD"];
		$Retina_Edema_OD=$row["Retina_Edema_OD"];
		$Retina_Nevus_OD=$row["Retina_Nevus_OD"];
		$Retina_BDR_OD_T=$row["Retina_BDR_OD_T"];
		$Retina_BDR_OD_1=$row["Retina_BDR_OD_1"];
		$Retina_BDR_OD_2=$row["Retina_BDR_OD_2"];
		$Retina_BDR_OD_3=$row["Retina_BDR_OD_3"];
		$Retina_BDR_OD_4=$row["Retina_BDR_OD_4"];
		$Retina_Druse_OD_T=$row["Retina_Druse_OD_T"];
		$Retina_Druse_OD_1=$row["Retina_Druse_OD_1"];
		$Retina_Druse_OD_2=$row["Retina_Druse_OD_2"];
		$Retina_Druse_OD_3=$row["Retina_Druse_OD_3"];
		$Retina_Druse_OD_4=$row["Retina_Druse_OD_4"];
		$Retina_RPE_Change_OD_T=$row["Retina_RPE_Change_OD_T"];
		$Retina_RPE_Change_OD_1=$row["Retina_RPE_Change_OD_1"];
		$Retina_RPE_Change_OD_2=$row["Retina_RPE_Change_OD_2"];
		$Retina_RPE_Change_OD_3=$row["Retina_RPE_Change_OD_3"];
		$Retina_RPE_Change_OD_4=$row["Retina_RPE_Change_OD_4"];
		$Druse_OD=$row["Druse_OD"];
		$RPE_Changes_OD=$row["RPE_Changes_OD"];
		$SRNVM_OD=$row["SRNVM_OD"];
		$Edema_OD=$row["Edema_OD"];
		$Scars_OD=$row["Scars_OD"];
		$Hemorrhage_OD=$row["Hemorrhage_OD"];
		$Microaneurysms_OD=$row["Microaneurysms_OD"];
		$Exudates_OD=$row["Exudates_OD"];
		$Macula_BDR_OD_T=$row["Macula_BDR_OD_T"];
		$Macula_BDR_OD_1=$row["Macula_BDR_OD_1"];
		$Macula_BDR_OD_2=$row["Macula_BDR_OD_2"];
		$Macula_BDR_OD_3=$row["Macula_BDR_OD_3"];
		$Macula_BDR_OD_4=$row["Macula_BDR_OD_4"];
		$Macula_SMD_OD_T=$row["Macula_SMD_OD_T"];
		$Macula_SMD_OD_1=$row["Macula_SMD_OD_1"];
		$Macula_SMD_OD_2=$row["Macula_SMD_OD_2"];
		$Macula_SMD_OD_3=$row["Macula_SMD_OD_3"];
		$Macula_SMD_OD_4=$row["Macula_SMD_OD_4"];
		
		$Feeder_Vessel_OD=$row["Feeder_Vessel_OD"];
		$Central_OD=$row["Central_OD"];
		$Nasal_OD=$row["Nasal_OD"];
		$Temporal_OD=$row["Temporal_OD"];
		$Inferior_OD=$row["Inferior_OD"];
		$Superior_OD=$row["Superior_OD"];
		$Hot_Spot_OD=$row["Hot_Spot_OD"];
		$Hot_Spot_Val_OD=$row["Hot_Spot_Val_OD"];
	
		$Retina_Ischemia_OD=$row["Retina_Ischemia_OD"]; 
		$Retina_BRVO_OD=$row["Retina_BRVO_OD"]; 
		$Retina_CRVO_OD=$row["Retina_CRVO_OD"];
		$SR_Heme_OD=$row["SR_Heme_OD"];
		$Classic_CNV_OD=$row["Classic_CNV_OD"];
		$Occult_CNV_OD=$row["Occult_CNV_OD"]; 
		
		$Sharp_Pink_OS=$row["Sharp_Pink_OS"];
		$Pale_OS=$row["Pale_OS"];
		$Large_Cap_OS=$row["Large_Cap_OS"];
		$Sloping_OS=$row["Sloping_OS"];
		$Notch_OS=$row["Notch_OS"];
		$NVD_OS=$row["NVD_OS"];
		$Leakage_OS=$row["Leakage_OS"];
		$Retina_Hemorrhage_OS=$row["Retina_Hemorrhage_OS"];
		$Retina_Microaneurysms_OS=$row["Retina_Microaneurysms_OS"];
		$Retina_Exudates_OS=$row["Retina_Exudates_OS"];
		$Retina_Laser_Scars_OS=$row["Retina_Laser_Scars_OS"];
		$Retina_NEVI_OS=$row["Retina_NEVI_OS"];
		$Retina_SRVNM_OS=$row["Retina_SRVNM_OS"];
		$Retina_Edema_OS=$row["Retina_Edema_OS"];
		$Retina_Nevus_OS=$row["Retina_Nevus_OS"];
		$Retina_BDR_OS_T=$row["Retina_BDR_OS_T"];
		$Retina_BDR_OS_1=$row["Retina_BDR_OS_1"];
		$Retina_BDR_OS_2=$row["Retina_BDR_OS_2"];
		$Retina_BDR_OS_3=$row["Retina_BDR_OS_3"];
		$Retina_BDR_OS_4=$row["Retina_BDR_OS_4"];
		$Retina_Druse_OS_T=$row["Retina_Druse_OS_T"];
		$Retina_Druse_OS_1=$row["Retina_Druse_OS_1"];
		$Retina_Druse_OS_2=$row["Retina_Druse_OS_2"];
		$Retina_Druse_OS_3=$row["Retina_Druse_OS_3"];
		$Retina_Druse_OS_4=$row["Retina_Druse_OS_4"];
		$Retina_RPE_Change_OS_T=$row["Retina_RPE_Change_OS_T"];
		$Retina_RPE_Change_OS_1=$row["Retina_RPE_Change_OS_1"];
		$Retina_RPE_Change_OS_2=$row["Retina_RPE_Change_OS_2"];
		$Retina_RPE_Change_OS_3=$row["Retina_RPE_Change_OS_3"];
		$Retina_RPE_Change_OS_4=$row["Retina_RPE_Change_OS_4"];
		$Druse_OS=$row["Druse_OS"];
		$RPE_Changes_OS=$row["RPE_Changes_OS"];
		$SRNVM_OS=$row["SRNVM_OS"];
		$Edema_OS=$row["Edema_OS"];
		$Scars_OS=$row["Scars_OS"];
		$Hemorrhage_OS=$row["Hemorrhage_OS"];
		$Microaneurysms_OS=$row["Microaneurysms_OS"];
		$Exudates_OS=$row["Exudates_OS"];
		$Macula_BDR_OS_T=$row["Macula_BDR_OS_T"];
		$Macula_BDR_OS_1=$row["Macula_BDR_OS_1"];
		$Macula_BDR_OS_2=$row["Macula_BDR_OS_2"];
		$Macula_BDR_OS_3=$row["Macula_BDR_OS_3"];
		$Macula_BDR_OS_4=$row["Macula_BDR_OS_4"];
		$Macula_SMD_OS_T=$row["Macula_SMD_OS_T"];
		$Macula_SMD_OS_1=$row["Macula_SMD_OS_1"];
		$Macula_SMD_OS_2=$row["Macula_SMD_OS_2"];
		$Macula_SMD_OS_3=$row["Macula_SMD_OS_3"];
		$Macula_SMD_OS_4=$row["Macula_SMD_OS_4"];
		
		$Feeder_Vessel_OS=$row["Feeder_Vessel_OS"];
		$Central_OS=$row["Central_OS"];
		$Nasal_OS=$row["Nasal_OS"];
		$Temporal_OS=$row["Temporal_OS"];
		$Inferior_OS=$row["Inferior_OS"];
		$Superior_OS=$row["Superior_OS"];
		$Hot_Spot_OS=$row["Hot_Spot_OS"];
		$Hot_Spot_Val_OS=$row["Hot_Spot_Val_OS"];
		
		$PED_OD=$row["PED_OD"];
		$PED_OS=$row["PED_OS"];
		
		$Retina_Ischemia_OS=$row["Retina_Ischemia_OS"]; 
		$Retina_BRVO_OS=$row["Retina_BRVO_OS"]; 
		$Retina_CRVO_OS=$row["Retina_CRVO_OS"];
		$SR_Heme_OS=$row["SR_Heme_OS"];
		$Classic_CNV_OS=$row["Classic_CNV_OS"];
		$Occult_CNV_OS=$row["Occult_CNV_OS"]; 	
		
		$icgComments=stripslashes($row["icgComments"]);
		$elem_tech2InformPt = $row["tech2InformPt"];
		$elem_informedPtNv = $row["ptInformedNv"];
		$elem_contiMeds = $row["ContinueMeds"];
		$elem_diagnosis = $row["diagnosis"];
		$elem_diagnosisOther = $row["diagnosisOther"];
		$encounterId = $row["encounter_id"];
		$elem_opidTestOrdered = $row["ordrby"];
		if(($row["ordrdt"] != "" && $row["ordrdt"] != "0000-00-00")){
			$elem_opidTestOrderedDate = get_date_format($row["ordrdt"]);
		}
		$elem_rptTst1yr = $row["rptTst1yr"];
		$purged=$row["purged"];
		$sign_path = $row["sign_path"];
		$sign_path_date_time = $row["sign_path_date_time"];
		$sign_path_date = $sign_path_time = "";
		if($sign_path && $sign_path_date_time!="0000-00-00 00:00:00" || $sign_path_date_time!=0) {
			$sign_path_date = date("".phpDateFormat()."",strtotime($sign_path_date_time));
			$sign_path_time = date("h:i A",strtotime($sign_path_date_time));
		}		
		$forum_procedure = $row['forum_procedure'];
	}

	//Performed Id
	if(empty($elem_performedBy) && (($userType == 1 || $userType == 12) || ($userType == 3))){
		$elem_performedBy = $logged_user;
	}

	//Interpreted By
	if(empty($elem_physician)){
		if($userType == '1'){
			$elem_phyName_order = $logged_user;
		}
	}

	//Current Performed by logged in
	$elem_performedByCurr = "";
	if(($userType == 1 || $userType == 12) || ($userType == 3)){
		$elem_performedByCurr = (empty($elem_performedBy) || (($userType == 3))) ? $logged_user : $elem_performedBy;
	}

	//Super bill init() --
	$superLen = "1";
	$sb_testName = "ICG";
	
	//Cpt Code Desc
	$thisCptDescSym = "ICG";

	//include($incdir."/chart_notes/superbill_init.php");
	//Super bill init() --

	//Prev + Next Records --
	$tmp = getDateFormatDB($elem_examDate);//Exam Date
	$tstPrevId = $oChartTestPrev->getPrevId($tmp,$icg_pkid);//getPervId
	$tstNxtId = $oChartTestPrev->getNxtId($tmp,$icg_pkid);//getNextId
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
<?php if($callFromInterface != 'admin'){?>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/icd10_autocomplete.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/js_gen.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/typeahead.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/superbill.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/lightbox/lightbox.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/epost.js"></script>
<?php }?>
<script src="<?php echo $library_path; ?>/js/common.js?<?php echo filemtime('../../library/js/common.js');?>" type="text/javascript"></script>
<script src="<?php echo $library_path; ?>/js/tests.js?<?php echo filemtime('../../library/js/tests.js');?>" type="text/javascript"></script>
<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
<script type="text/javascript">
var imgPath 		= "<?php echo $GLOBALS['webroot'];?>";
var elem_per_vo 	= "<?php echo $elem_per_vo;?>";
var zPath			= "<?php echo $GLOBALS['rootdir'];?>";
var JS_WEB_ROOT_PATH 	= "<?php echo $GLOBALS['webroot']; ?>";
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
});

<?php if($callFromInterface != 'admin'){?>

	//Test id
	var t_nxtid			= "<?php echo $tstNxtId;?>";
	var t_previd		= "<?php echo $tstPrevId;?>";


	function printTest()	{
		var tId = "<?php echo $_GET["tId"];?>";
		window.open('../../library/html_to_pdf/createPdf.php?op=l&onePage=false&file_location=<?php echo $html_file_name;?>&mergePDF=1&name=test_'+tId+'_pdf&testIds='+tId+'&saveOption=F&image_from=<?php echo $testname; ?>','vf_Rpt','menubar=0,resizable=yes');	
	}

	// Prev Data
	var oPrvDt;
	function getPrevTestDt(ctid,cdt,ctm,dir){
		getPrevTestDtExe(ctid,cdt,ctm,dir,"ICG");
	}

	function saveIcg(){
		if(top.frm_submited==1){ return; }
		top.frm_submited=1;
		<?php if($elem_per_vo == "1"){echo "return;";}?>
		var f = document.test_form_frm;
		var m = "<b>Please fill the following:-</b><br>";
		var err = false;
		/*
		if((f.elem_icg_signature.value == "0-0-0:;") || (f.elem_icg_signature.value == "")){
			m += "  -Signature\n";
			err = true;
		}
		*/ //elem_physician
		if((f.elem_performedBy.value == "") || (f.elem_performedBy.value == "0") ) {
			m += "&bull; Performed By (Physician or Technician only)<br>";
			err = true;
		}
		
		if(f.elem_opidTestOrdered.value==""){
			m += '&bull; Order By<br>';
			err = true;
		}
	
		//Super Bill
		var oSB = isSuperBillMade();
		var sb_dxids="";
		if(oSB.SBill == true){		
			
			if(oSB.DXCodeOK == false  ||  oSB.DXCodeAssocOK == false ){
				m += '&bull; Dx code in Super bill<br>';
				err = true;
			}
			
			if(oSB.DXCodeComplete==false){
				m += '&bull; Incomplete ICD-10 DX code(s) in Super bill<br>';
				err = true;
			}
			sb_dxids=oSB.dxids;
		}
		$("#sb_dxids").val(sb_dxids);
		//alert(f.elem_vfSign.value+"\n"+f.elem_phyName.value+"\n"+err)
		if(err == false){
			if(typeof(opener) == "undefined" && typeof(top.show_loading_image)!="undefined"){  top.show_loading_image("show");}
			f.submit();
		}else{
			top.fAlert(m,'','top.frm_submited=0;');
		}
	}
<?php }?>

function dispALEye(){
	var o = document.test_form_frm.elem_ArgonLaserEye;
	var l = o.length;
	var f = false;
	for(var i=0;i<l;i++){
		if(o[i].checked == true){
			f = true;
		}
	}
	document.getElementById("als").style.visibility = (f==true) ? "visible" : "hidden";
}

function selectOs(){
	var elements = document.test_form_frm.elements;
	var eleLen = elements.length;
	for(var i=0;i<eleLen;i++){
		if(elements[i].type == "checkbox"){
			var eleName = elements[i].name;
			if(eleName.indexOf('OD')!= -1){
				var chkStatus = elements[i].checked;
				eleName = eleName.replace(/OD/, "OS")
				document.getElementById(eleName).checked = chkStatus;
			}
		}
	}
	document.getElementById('elem_testresults_desc_os').value = document.getElementById('elem_testresults_desc_od').value;
}

function displayFUComment(o){
	document.getElementById("coment").style.visibility = (o.checked == true) ? "visible" : "hidden" ;
}


//Set Tests and Reliability fields as per interpretation
function setReli_Test(wh){
	var arrReli = new Array("elem_pa_inter","elem_pa_inter1");
	var arrTestOd = new Array("Sharp_Pink_OD","Pale_OD","Large_Cap_OD",
					"Sloping_OD","Notch_OD","NVD_OD","Leakage_OD",
					"Retina_Hemorrhage_OD","Retina_Microaneurysms_OD","Retina_Exudates_OD",
					"Retina_Laser_Scars_OD","Retina_NEVI_OD","Retina_SRVNM_OD","Retina_Edema_OD",
					"Retina_Nevus_OD","Retina_BDR_OD_T","Retina_BDR_OD_1",
					"Retina_BDR_OD_2","Retina_BDR_OD_3","Retina_BDR_OD_4",
					"Retina_Druse_OD_T","Retina_Druse_OD_1","Retina_Druse_OD_2",
					"Retina_Druse_OD_3","Retina_Druse_OD_4",
					"Retina_RPE_Change_OD_T","Retina_RPE_Change_OD_1","Retina_RPE_Change_OD_2",
					"Retina_RPE_Change_OD_3","Retina_RPE_Change_OD_4",
					"Druse_OD","RPE_Changes_OD","SRNVM_OD","Edema_OD","Scars_OD",
					"Hemorrhage_OD","Microaneurysms_OD","Exudates_OD","PED_OD",
					"Macula_BDR_OD_T","Macula_BDR_OD_1","Macula_BDR_OD_2",
					"Macula_BDR_OD_3","Macula_BDR_OD_4",
					"Macula_SMD_OD_T","Macula_SMD_OD_1","Macula_SMD_OD_2",
					"Macula_SMD_OD_3","Macula_SMD_OD_4",
					"elem_testresults_desc_od" );
	setReli_Exe(wh,arrTestOd,arrReli);
}

function dispFUComments(o){
	document.getElementById("coment").style.visibility = (o.checked == true) ?  "visible" : "hidden" ;
}

function enableDisable(chkBox,txtBox){
	if(document.getElementById(chkBox).checked==true){
		document.getElementById(txtBox).disabled=false;
		document.getElementById(txtBox).focus();
	}else{
		document.getElementById(txtBox).disabled=true;
	}
}
</script>
</head>
<body>
<form name="test_form_frm" action="save_tests.php" method="post">
<?php if($callFromInterface != 'admin'){?>
    <input type="hidden" name="elem_saveForm" id="elem_saveForm" value="ICG">
    <input type="hidden" name="elem_patientId" id="elem_patientId" value="<?php echo $patient_id;?>">
    <input type="hidden" name="elem_formId" id="elem_formId" value="<?php echo $form_id;?>">
    <input type="hidden" name="elem_tests_name_id" id="elem_tests_name_id" value="<?php echo $test_master_id;?>">
    <input type="hidden" id="elem_edMode" name="hd_icg_mode" value="<?php echo $icg_mode;?>">
    <input type="hidden" id="elem_testId" name="elem_icgId" value="<?php echo $test_edid;?>">
    <input type="hidden" name="wind_opn" id="wind_opn" value="0">
    <input type="hidden" name="elem_operatorId" value="<?php echo $elem_operatorId;?>">
    <input type="hidden" name="elem_operatorName" value="<?php echo $elem_operatorName;?>">
    <input type="hidden" name="elem_noP" value="<?php echo $noP;?>">
    <input type="hidden" name="elem_examTime" value="<?php echo $elem_examTime;?>">
    <input type="hidden" name="pop" id="pop" value="<?php echo $_REQUEST['pop'] ?>">
    <!--the hidden field doNotShowRightSide	is used for mest maneger-->
    <input type="hidden" name="doNotShowRightSide" value="<?php echo $_REQUEST['doNotShowRightSide']; ?>">
    <input type="hidden" name="zeissAction" id="zeissAction" value="">
    <input type="hidden" name="elem_phyName_order" id="elem_phyName_order" value="<?php echo $elem_phyName_order; ?>" data-phynm="<?php echo (!empty($elem_phyName_order)) ? $objTests->getPersonnal3($elem_phyName_order) : "" ; ?>">
	<?php $flg_interpreted_btn = (!empty($elem_phyName_order) ) ? 1 : 0;?>
<?php }?>
<div class=" container-fluid">
    <div class="mainarea">
        <div class="row">
            <div class="col-sm-<?php if($callFromInterface != 'admin' && $doNotShowRightSide != 'yes'){echo '10';}else{echo '12';}?>">
               <?php if($callFromInterface != 'admin'){ require_once("test_orderby_inc.php");}?>
                <div class="clearfix"></div>
                <div class="tstopt">
                    <div class="row">
                        <div class="col-sm-6 sitetab tstrstopt ">
                            <ul>
                                <li><label><input type="checkbox" name="elem_icg_early" value="1" <?php echo ($elem_icg_early == "1") ?  "checked" : "" ;?>><span class="label_txt">Early and late shots</span></label></li>
                                <li><label><input type="checkbox" name="elem_icg_extra" value="1" <?php echo ($elem_icg_extra == "1") ?  "checked" : "" ;?>><span class="label_txt">Extra Copy</span></label></li>
                            </ul>
                        </div>
                        <div class="col-sm-6 siteopt">
                            <div class="tstopt">
                                <div class="row">
                                    <div class="col-sm-3 sitehd">Sites</div>
                                    <div class="col-sm-5 testopt icg">
                                        <ul>
                                            <li class="ouc"><label><input type="radio" name="elem_icg_od" value="1" onClick="setReli_Test('OU')" <?php echo (!$elem_icg_od || $elem_icg_od == "1") ?  "checked" : "" ;?>><span class="label_txt">OU</span></label></li>
                                            <li class="odc"><label><input type="radio" name="elem_icg_od" value="2" onClick="setReli_Test('OD')" <?php echo ($elem_icg_od == "2") ?  "checked" : "" ;?>><span class="label_txt"><span>OD</span><strong>&gt;</strong><span>OS</span></span></label></li>
                                        	<li class="osc"><label><input type="radio" name="elem_icg_od" value="3" onClick="setReli_Test('OS')" <?php echo ($elem_icg_od == "3") ?  "checked" : "" ;?>><span class="label_txt"><span>OS</span><strong>&gt;</strong><span>OD</span></span></label></li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-4">
                                        <?php
                                            /*Purpose: Add dropdown for procedure codes to be used in Zeiss HL7 message*/
                                            if($callFromInterface != 'admin' && constant("ZEISS_FORUM") == "YES"){
                                                $procedure_opts = $objTests->zeissProcOpts(6);
                                            ?>
                                                <select id="forum_procedure" name="forum_procedure" class="form-control minimal mt5">
                                                    <option value="">-Forum Procedure-</option>
                                                    <?php
                                                        foreach($procedure_opts as $key=>$proc){
                                                            $selected = "";
                                                            if($key==$forum_procedure){$selected='selected="selected"';}
                                                            print '<option '.$selected.' value="'.$key.'">'.$proc.'</optionn>';
                                                        }
                                                    ?>
                                                </select>
                                        <?php }/*End Modification by Pankaj*/?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="technibox">
                    <div class="row">
                        <div class="col-sm-7">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Performed By</label>
                                        <input type="text" id="elem_performedByName" name="elem_performedByName" value="<?php echo $objTests->getPersonnal3($elem_performedBy);?>" class="form-control" readonly onDblClick="setOpNameId(this.name)">
                                        <input type="hidden" id="elem_performedBy" name="elem_performedBy" value="<?php echo $elem_performedByCurr;?>">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Diagnosis </label>
                                        <select class="form-control minimal" id="elem_diagnosis" name="elem_diagnosis" onChange="checkDiagnosis(this.value)" style="display:<?php echo ($elem_diagnosis == "Other") ? "none" : "block" ;?>;">
                                            <option>--Select--</option>
                                            <?php
                                            $arrDigOpts = $objTests->getDiagOpts("1","icg",$test_master_id);
                                            foreach($arrDigOpts  as $key=>$val){
                                                $sel = ($elem_diagnosis == $val) ? "SELECTED" : "";
                                                echo "<option value=\"".$val."\" ".$sel.">".$val."</option>";
                                            }
            
                                            ?>
                                        </select>
                                        <div id="td_diagnosisOther" style="display:<?php echo ($elem_diagnosis == "Other") ? "inline-block" : "none" ;?>;">
                                            <div class="col-sm-10"><input type="text" name="elem_diagnosisOther" value="<?php echo ($elem_diagnosisOther);?>" class="form-control"></div>
                                            <div class="col-sm-2"><img src="<?php echo $library_path;?>/images/close14.png" title="Change" onClick="checkDiagnosis('');" style="cursor:hand; padding:0px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group pdlft10">
                                <textarea class="form-control" rows="2" style="width:100%; height:55px !important;" name="elem_comments_icg" id="techComment" placeholder="Technician Comments"><?php echo $elem_comments_icg;?></textarea>
                            </div>
                        </div>	
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="corporat">
                    <div class="pdlft10">
                        <div class="row">
                            <div class="col-sm-6">
                                <ul>
                                    <li class="head">Patient Understanding &amp; Cooperation</li>
                                    <li>
                                    <div class="tstrstopt">
                                        <label><input type="radio" name="elem_pa_under" value="Good" <?php echo (!$elem_pa_under || $elem_pa_under == "Good") ? "checked" : "";?>><span class="label_txt">Good</span></label>
                                        <label><input type="radio" name="elem_pa_under" value="Fair" <?php echo ($elem_pa_under == "Fair") ? "checked" : "";?>><span class="label_txt">Fair</span></label>
                                        <label><input type="radio" name="elem_pa_under" value="Poor" <?php echo ($elem_pa_under == "Poor") ? "checked" : "";?>><span class="label_txt">Poor</span></label>
                                    </div>
                                    </li>  
                                </ul>
                            </div>
                            <div class="col-sm-4 text-center">
                                <div class="form-inline mt5">
                                    <?php if($callFromInterface != 'admin'){?>
                                        <label for="">Preference Card</label>
                                        <?php echo $objTests->DropDown_Interpretation_Profile($this_test_properties['id']);?>
                                	<?php }?>
                                </div>
                            </div>
                            <div class="col-sm-2 text-right">
                            	<?php if($callFromInterface != 'admin'){?>
	                                <button class="btn-value" type="button" onmouseover="inPrvVal()" onmouseout="inPrvVal(3)" onclick="inPrvVal(1)">Previous Values</button>
                            	<?php }?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div>
                    <table class="table table-bordered">
                        <tr>
                          <td colspan="4" class="phyintrhead">Physician Interpretation</td>
                        </tr>
                        <tr>
                            <td  class="tdlftpan"><strong>TEST RESULT</strong></td>
                            <td  class="odstrip">
                                <div class="row">
                                    <div class="col-sm-1">OD</div>
                                    <div class="col-sm-11 text-right">
                                        <div class="plr10 tstrstopt">
                                            <label><input type="radio" name="elem_pa_inter" value="Good" <?php echo (!$elem_pa_inter || $elem_pa_inter == "Good") ? "checked" : "" ;?>><span class="label_txt">Good</span></label>
                                            <label><input type="radio" name="elem_pa_inter" value="Fair" <?php echo ($elem_pa_inter == "Fair") ? "checked" : "" ;?>><span class="label_txt">Fair</span></label>
                                            <label><input type="radio" name="elem_pa_inter" value="Poor" <?php echo ($elem_pa_inter == "Poor") ? "checked" : "" ;?>><span class="label_txt">Poor</span></label>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td rowspan="6" align="center" valign="middle" class="bltra"><a href="javascript:selectOs();">BL</a></td>
                            <td class="osstrip">
                                <div class="row">
                                    <div class="col-sm-1">OS</div>
                                    <div class="col-sm-11 text-right">
                                        <div class="plr10 tstrstopt">
                                            <label><input type="radio" name="elem_pa_inter1" value="Good" <?php echo (!$elem_pa_inter1 || $elem_pa_inter1 == "Good") ? "checked" : "" ;?>><span class="label_txt">Good</span></label>
                                            <label><input type="radio" name="elem_pa_inter1" value="Fair" <?php echo ($elem_pa_inter1 == "Fair") ? "checked" : "" ;?>><span class="label_txt">Fair</span></label>
                                            <label><input type="radio" name="elem_pa_inter1" value="Poor" <?php echo ($elem_pa_inter1 == "Poor") ? "checked" : "" ;?>><span class="label_txt">Poor</span></label>
                                        </div>
                                    </div>
                                </div>
                            </td>		
                        </tr>
                        
                        <tr>
                            <td class="tdlftpan"><strong>Disc</strong></td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-4"><?php if($elem_PinkOd_Sharp == 'Pink Sharp'){ $Sharp_Pink_OD=1;}?><label><input type="checkbox" id="Sharp_Pink_OD" name="Sharp_Pink_OD" <?php if($Sharp_Pink_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Pink&nbsp;&&nbsp;Sharp</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Pale_OD" name="Pale_OD" <?php if($Pale_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Pale</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Large_Cap_OD" name="Large_Cap_OD" <?php if($Large_Cap_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Large Cup</span></label></div>
                                    <div class="cleafix"></div>
                                    <div class="col-sm-4"><?php if($elem_sloppingOd_Mild == 'Mild' || $elem_sloppingOd_Moderate == 'Moderate' || $elem_sloppingOd_Severe == 'Severe'){$Sloping_OD = 1;}?><label><input type="checkbox" id="Sloping_OD" name="Sloping_OD" <?php if($Sloping_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Sloping</span></label></div>
                                    <div class="col-sm-4"><?php if($elem_notchOd_Mild == "Mild" || $elem_notchOd_Moderate == "Moderate" || $elem_notchOd_Severe == "Severe"){$Notch_OD = 1;}?><label><input type="checkbox" id="Notch_OD" name="Notch_OD" <?php if($Notch_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Notch</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="NVD_OD" name="NVD_OD" <?php if($NVD_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">NVD</span></label></div>
                                    <div class="cleafix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Leakage_OD" name="Leakage_OD" <?php if($Leakage_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Leakage</span></label></div>
                                </div>                        
                            </td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-4"><?php if($elem_PinkOs_Sharp == 'Pink Sharp'){ $Sharp_Pink_OS=1; }?><label><input type="checkbox" id="Sharp_Pink_OS" name="Sharp_Pink_OS" <?php if($Sharp_Pink_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Pink&nbsp;&&nbsp;Sharp</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Pale_OS" name="Pale_OS" <?php if($Pale_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Pale</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Large_Cap_OS" name="Large_Cap_OS" <?php if($Large_Cap_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Large&nbsp;Cup</span></label></div>
                                    <div class="cleafix"></div>
                                    <div class="col-sm-4"><?php if($elem_sloppingOs_Mild == 'Mild' || $elem_sloppingOs_Moderate == 'Moderate' || $elem_sloppingOs_Severe == 'Severe'){$Sloping_OS = 1;}?><label><input type="checkbox" id="Sloping_OS" name="Sloping_OS" <?php if($Sloping_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Sloping</span></label></div>
                                    <div class="col-sm-4"><?php if($elem_notchOs_Mild == "Mild" || $elem_notchOs_Moderate == "Moderate" || $elem_notchOs_Severe == "Severe"){$Notch_OS = 1;}?><label><input type="checkbox" id="Notch_OS" name="Notch_OS" <?php if($Notch_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Notch</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="NVD_OS" name="NVD_OS" <?php if($NVD_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">NVD</span></label></div>
                                    <div class="cleafix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Leakage_OS" name="Leakage_OS" <?php if($Leakage_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Leakage</span></label></div>
                                </div>
                            
                            </td>	
                        </tr>
                        <tr>
                            <td class="tdlftpan"><strong>Retina</strong></td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_Hemorrhage_OD" name="Retina_Hemorrhage_OD" <?php if($Retina_Hemorrhage_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Hemorrhage</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_Microaneurysms_OD" name="Retina_Microaneurysms_OD" <?php if($Retina_Microaneurysms_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Microaneurysms</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_Exudates_OD" name="Retina_Exudates_OD" <?php if($Retina_Exudates_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Exudates</span></label></div>
                                    <div class="cleafix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_Laser_Scars_OD" name="Retina_Laser_Scars_OD" <?php if($Retina_Laser_Scars_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Laser Scars</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_NEVI_OD" name="Retina_NEVI_OD" <?php if($Retina_NEVI_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">NVE</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_SRVNM_OD" name="Retina_SRVNM_OD" <?php if($Retina_SRVNM_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">SRVNM</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_Edema_OD" name="Retina_Edema_OD" <?php if($Retina_Edema_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Edema</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_Nevus_OD" name="Retina_Nevus_OD" <?php if($Retina_Nevus_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Nevus</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_Ischemia_OD" name="Retina_Ischemia_OD" <?php if($Retina_Ischemia_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Ischemia</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_BRVO_OD" name="Retina_BRVO_OD" <?php if($Retina_BRVO_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">BRVO</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_CRVO_OD" name="Retina_CRVO_OD" <?php if($Retina_CRVO_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">CRVO</span></label></div>
                                </div> 
                                <div class="row">
                                    <div class="col-sm-3 tdlft">BDR</div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_BDR_OD_T" name="Retina_BDR_OD_T" <?php if($Retina_BDR_OD_T==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">T</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_BDR_OD_1" name="Retina_BDR_OD_1" <?php if($Retina_BDR_OD_1==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+1</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_BDR_OD_2" name="Retina_BDR_OD_2" <?php if($Retina_BDR_OD_2==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+2</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_BDR_OD_3" name="Retina_BDR_OD_3" <?php if($Retina_BDR_OD_3==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+3</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="Retina_BDR_OD_4" name="Retina_BDR_OD_4" <?php if($Retina_BDR_OD_4==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+4</span></label></div>
                                </div>
                                <div class="row">                                            
                                    <div class="col-sm-3 tdlft">Drusen</div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_Druse_OD_T" name="Retina_Druse_OD_T" <?php if($Retina_Druse_OD_T==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">T</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_Druse_OD_1" name="Retina_Druse_OD_1" <?php if($Retina_Druse_OD_1==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+1</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_Druse_OD_2" name="Retina_Druse_OD_2" <?php if($Retina_Druse_OD_2==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+2</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_Druse_OD_3" name="Retina_Druse_OD_3" <?php if($Retina_Druse_OD_3==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+3</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="Retina_Druse_OD_4" name="Retina_Druse_OD_4" <?php if($Retina_Druse_OD_4==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+4</span></label></div>
                                </div>
                                <div class="row">                                            
                                    <div class="col-sm-3 tdlft">Rpe&nbsp;change</div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_RPE_Change_OD_T" name="Retina_RPE_Change_OD_T" <?php if($Retina_RPE_Change_OD_T==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">T</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_RPE_Change_OD_1" name="Retina_RPE_Change_OD_1" <?php if($Retina_RPE_Change_OD_1==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+1</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_RPE_Change_OD_2" name="Retina_RPE_Change_OD_2" <?php if($Retina_RPE_Change_OD_2==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+2</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_RPE_Change_OD_3" name="Retina_RPE_Change_OD_3" <?php if($Retina_RPE_Change_OD_3==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+3</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="Retina_RPE_Change_OD_4" name="Retina_RPE_Change_OD_4" <?php if($Retina_RPE_Change_OD_4==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+4</span></label></div>
                                </div>                       
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_Hemorrhage_OS" name="Retina_Hemorrhage_OS" <?php if($Retina_Hemorrhage_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Hemorrhage</span></label></div>
                                    <div class="col-sm-4"><?php if($elem_macOs_hemoHage_microaneurism == "Micoraneurysm") $Microaneurysms_OS=1; ?><label><input type="checkbox" id="Retina_Microaneurysms_OS" name="Retina_Microaneurysms_OS" <?php if($Retina_Microaneurysms_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Microaneurysms</span></label></div>
                                    <div class="col-sm-4"><?php if($ExudateMacOs) $Exudates_OS=1; ?><label><input type="checkbox" id="Retina_Exudates_OS" name="Retina_Exudates_OS" <?php if($Retina_Exudates_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Exudates</span></label></div>
                                    <div class="cleafix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_Laser_Scars_OS" name="Retina_Laser_Scars_OS" <?php if($Retina_Laser_Scars_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Laser Scars</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_NEVI_OS" name="Retina_NEVI_OS" <?php if($Retina_NEVI_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">NVE</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_SRVNM_OS" name="Retina_SRVNM_OS" <?php if($Retina_SRVNM_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">SRVNM</span></label></div>
                                    <div class="cleafix"></div>
                                    <div class="col-sm-4"><?php if($edemaOs) $Edema_OS=1; ?><label><input type="checkbox" id="Retina_Edema_OS" name="Retina_Edema_OS" <?php if($Retina_Edema_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Edema</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_Nevus_OS" name="Retina_Nevus_OS" <?php if($Retina_Nevus_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Nevus</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_Ischemia_OS" name="Retina_Ischemia_OS" <?php if($Retina_Ischemia_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Ischemia</span></label></div>
                                    <div class="cleafix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_BRVO_OS" name="Retina_BRVO_OS" <?php if($Retina_BRVO_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">BRVO</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Retina_CRVO_OS" name="Retina_CRVO_OS" <?php if($Retina_CRVO_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">CRVO</span></label></div>
                                </div>
                            	<div class="row">
                                    <div class="col-sm-3 tdlft">BDR</div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_BDR_OS_T" name="Retina_BDR_OS_T" <?php if($Retina_BDR_OS_T==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">T</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_BDR_OS_1" name="Retina_BDR_OS_1" <?php if($Retina_BDR_OS_1==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+1</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_BDR_OS_2" name="Retina_BDR_OS_2" <?php if($Retina_BDR_OS_2==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+2</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_BDR_OS_3" name="Retina_BDR_OS_3" <?php if($Retina_BDR_OS_3==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+3</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="Retina_BDR_OS_4" name="Retina_BDR_OS_4" <?php if($Retina_BDR_OS_4==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+4</span></label></div>
                                </div>
                                <div class="row">                                            
                                    <div class="col-sm-3 tdlft">Drusen</div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_Druse_OS_T" name="Retina_Druse_OS_T" <?php if($Retina_Druse_OS_T==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">T</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_Druse_OS_1" name="Retina_Druse_OS_1" <?php if($Retina_Druse_OS_1==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+1</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_Druse_OS_2" name="Retina_Druse_OS_2" <?php if($Retina_Druse_OS_2==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+2</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_Druse_OS_3" name="Retina_Druse_OS_3" <?php if($Retina_Druse_OS_3==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+3</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="Retina_Druse_OS_4" name="Retina_Druse_OS_4" <?php if($Retina_Druse_OS_4==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+4</span></label></div>
                                </div>
                                <div class="row">                                            
                                    <div class="col-sm-3 tdlft">Rpe&nbsp;change</div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_RPE_Change_OS_T" name="Retina_RPE_Change_OS_T" <?php if($Retina_RPE_Change_OS_T==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">T</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_RPE_Change_OS_1" name="Retina_RPE_Change_OS_1" <?php if($Retina_RPE_Change_OS_1==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+1</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_RPE_Change_OS_2" name="Retina_RPE_Change_OS_2" <?php if($Retina_RPE_Change_OS_2==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+2</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Retina_RPE_Change_OS_3" name="Retina_RPE_Change_OS_3" <?php if($Retina_RPE_Change_OS_3==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+3</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="Retina_RPE_Change_OS_4" name="Retina_RPE_Change_OS_4" <?php if($Retina_RPE_Change_OS_4==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+4</span></label></div>
                                </div>
                            </td>	
                        </tr>
                        <tr>
                          <td class="tdlftpan"><strong>Macula</strong></td>
                          <td class="tstbx tstrstopt">
                            <div class="row">
                                <div class="col-sm-4"><?php if($maculaDescOd) $Druse_OD=1; ?><label><input type="checkbox" id="Druse_OD" name="Druse_OD" <?php if($Druse_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Drusen</span></label></div>
                                <div class="col-sm-4"><?php if($rpeChMaculaOs) $RPE_Changes_OD = 1; ?><label><input type="checkbox" id="RPE_Changes_OD" name="RPE_Changes_OD" <?php if($RPE_Changes_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">RPE&nbsp;Changes</span></label></div>
                                <div class="col-sm-4"><?php if($srnvmOd) $SRNVM_OD=1; ?><label><input type="checkbox" id="SRNVM_OD" name="SRNVM_OD" <?php if($SRNVM_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">SRNVM</span></label></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><?php if($edemaOd) $Edema_OD=1; ?><label><input type="checkbox" id="Edema_OD" name="Edema_OD" <?php if($Edema_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Edema</span></label></div>
                                <div class="col-sm-4"><?php if($fScarOd || $prpScarOd) $Scars_OD=1; ?><label><input type="checkbox" id="Scars_OD" name="Scars_OD" <?php if($Scars_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Scars</span></label></div>
                                <div class="col-sm-4"><?php if($hemorrhageOd) $Hemorrhage_OD=1; ?><label><input type="checkbox" id="Hemorrhage_OD" name="Hemorrhage_OD" <?php if($Hemorrhage_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Hemorrhage</span></label></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><?php if($elem_macOd_hemoHage_microaneurism == "Micoraneurysm") $Microaneurysms_OD=1; ?><label><input type="checkbox" id="Microaneurysms_OD" name="Microaneurysms_OD" <?php if($Microaneurysms_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Microaneurysms</span></label></div>
                                <div class="col-sm-4"><?php if($ExudateMacOd) $Exudates_OD=1; ?><label><input type="checkbox" id="Exudates_OD" name="Exudates_OD" <?php if($Exudates_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Exudates</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="PED_OD" name="PED_OD" <?php if($PED_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">PED</span></label></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><label><input type="checkbox" id="SR_Heme_OD" name="SR_Heme_OD" <?php if($SR_Heme_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">SR Heme</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="Classic_CNV_OD" name="Classic_CNV_OD" <?php if($Classic_CNV_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Classic CNV</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="Occult_CNV_OD" name="Occult_CNV_OD" <?php if($Occult_CNV_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Occult CNV</span></label></div>
                            </div>
							<div class="row">
                                <div class="col-sm-3 tdlft">BDR</div>
                                <div class="col-sm-2"><?php if($elem_macOd_dr_bdr_T == "T") $Macula_BDR_OD_T=1; ?><label><input type="checkbox" id="Macula_BDR_OD_T" name="Macula_BDR_OD_T" <?php if($Macula_BDR_OD_T==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">T</span></label></div>
                                <div class="col-sm-2"><?php if($elem_macOd_dr_bdr_pos1 == "+1") $Macula_BDR_OD_1=1; ?><label><input type="checkbox" id="Macula_BDR_OD_1" name="Macula_BDR_OD_1" <?php if($Macula_BDR_OD_1==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+1</span></label></div>
                                <div class="col-sm-2"><?php if($elem_macOd_dr_bdr_pos2 == "+2") $Macula_BDR_OD_2=1; ?><label><input type="checkbox" id="Macula_BDR_OD_2" name="Macula_BDR_OD_2" <?php if($Macula_BDR_OD_2==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+2</span></label></div>
                                <div class="col-sm-2"><?php if($elem_macOd_dr_bdr_pos3 == "+3") $Macula_BDR_OD_3=1; ?><label><input type="checkbox" id="Macula_BDR_OD_3" name="Macula_BDR_OD_3" <?php if($Macula_BDR_OD_3==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+3</span></label></div>
                                <div class="col-sm-1"><?php if($elem_macOd_dr_bdr_pos4 == "+4") $Macula_BDR_OD_4=1; ?><label><input type="checkbox" id="Macula_BDR_OD_4" name="Macula_BDR_OD_4" <?php if($Macula_BDR_OD_4==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+4</span></label></div>
                            </div>
                            <div class="row">                                            
                                <div class="col-sm-3 tdlft">SMD</div>
                                <div class="col-sm-2"><label><input type="checkbox" id="Macula_SMD_OD_T" name="Macula_SMD_OD_T" <?php if($Macula_SMD_OD_T==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">T</span></label></div>
                                <div class="col-sm-2"><label><input type="checkbox" id="Macula_SMD_OD_1" name="Macula_SMD_OD_1" <?php if($Macula_SMD_OD_1==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+1</span></label></div>
                                <div class="col-sm-2"><label><input type="checkbox" id="Macula_SMD_OD_2" name="Macula_SMD_OD_2" <?php if($Macula_SMD_OD_2==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+2</span></label></div>
                                <div class="col-sm-2"><label><input type="checkbox" id="Macula_SMD_OD_3" name="Macula_SMD_OD_3" <?php if($Macula_SMD_OD_3==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+3</span></label></div>
                                <div class="col-sm-1"><label><input type="checkbox" id="Macula_SMD_OD_4" name="Macula_SMD_OD_4" <?php if($Macula_SMD_OD_4==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+4</span></label></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><label><input type="checkbox" id="Feeder_Vessel_OD" name="Feeder_Vessel_OD" <?php if($Feeder_Vessel_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Feeder Vessel</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="Central_OD" name="Central_OD" <?php if($Central_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Central</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="Nasal_OD" name="Nasal_OD" <?php if($Nasal_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Nasal</span></label></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><label><input type="checkbox" id="Temporal_OD" name="Temporal_OD" <?php if($Temporal_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Temporal</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="Inferior_OD" name="Inferior_OD" <?php if($Inferior_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Inferior</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="Superior_OD" name="Superior_OD" <?php if($Superior_OD==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Superior</span></label></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><label><input type="checkbox" id="Hot_Spot_OD" name="Hot_Spot_OD" <?php if($Hot_Spot_OD==1) echo 'CHECKED'; ?> value="1" onClick="enableDisable(this.id,'Hot_Spot_Val_OD');"><span class="label_txt">Hot Spot</span></label></div>
                                <div class="col-sm-2"><input type="text" id="Hot_Spot_Val_OD" name="Hot_Spot_Val_OD" value="<?php echo ($Hot_Spot_Val_OD);?>" size="10" class="form-control" <?php if($Hot_Spot_OD!=1) echo 'disabled';?>></div>
                            </div>
                          </td>
                          <td class="tstbx tstrstopt">
                            <div class="row">
                                <div class="col-sm-4"><?php if($maculaDescOs) $Druse_OS=1; ?><label><input type="checkbox" id="Druse_OS" name="Druse_OS" <?php if($Druse_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Drusen</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="RPE_Changes_OS" name="RPE_Changes_OS" <?php if($RPE_Changes_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">RPE&nbsp;Changes</span></label></div>
                                <div class="col-sm-4"><?php if($srnvmOs) $SRNVM_OS=1; ?><label><input type="checkbox" id="SRNVM_OS" name="SRNVM_OS" <?php if($SRNVM_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">SRNVM</span></label></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><label><input type="checkbox" id="Edema_OS" name="Edema_OS" <?php if($Edema_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Edema</span></label></div>
                                <div class="col-sm-4"><?php if($fScarOs || $prpScarOs) $Scars_OS=1; ?><label><input type="checkbox" id="Scars_OS" name="Scars_OS" <?php if($Scars_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Scars</span></label></div>
                                <div class="col-sm-4"><?php if($hemorrhageOs) $Hemorrhage_OS=1; ?><label><input type="checkbox" id="Hemorrhage_OS" name="Hemorrhage_OS" <?php if($Hemorrhage_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Hemorrhage</span></label></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><label><input type="checkbox" id="Microaneurysms_OS" name="Microaneurysms_OS" <?php if($Microaneurysms_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Microaneurysms</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="Exudates_OS" name="Exudates_OS" <?php if($Exudates_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Exudates</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="PED_OS" name="PED_OS" <?php if($PED_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">PED</span></label></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><label><input type="checkbox" id="SR_Heme_OS" name="SR_Heme_OS" <?php if($SR_Heme_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">SR Heme</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="Classic_CNV_OS" name="Classic_CNV_OS" <?php if($Classic_CNV_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Classic CNV</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="Occult_CNV_OS" name="Occult_CNV_OS" <?php if($Occult_CNV_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Occult CNV</span></label></div>
                            </div>
							<div class="row">
                                <div class="col-sm-3 tdlft">BDR</div>
                                <div class="col-sm-2"><?php if($elem_macOs_dr_bdr_T == "T") $Macula_BDR_OS_T=1; ?><label><input type="checkbox" id="Macula_BDR_OS_T" name="Macula_BDR_OS_T" <?php if($Macula_BDR_OS_T==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">T</span></label></div>
                                <div class="col-sm-2"><?php if($elem_macOs_dr_bdr_pos1 == "+1") $Macula_BDR_OS_1=1; ?><label><input type="checkbox" id="Macula_BDR_OS_1" name="Macula_BDR_OS_1" <?php if($Macula_BDR_OS_1==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+1</span></label></div>
                                <div class="col-sm-2"><?php if($elem_macOs_dr_bdr_pos2 == "+2") $Macula_BDR_OS_2=1; ?><label><input type="checkbox" id="Macula_BDR_OS_2" name="Macula_BDR_OS_2" <?php if($Macula_BDR_OS_2==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+2</span></label></div>
                                <div class="col-sm-2"><?php if($elem_macOs_dr_bdr_pos3 == "+3") $Macula_BDR_OS_3=1; ?><label><input type="checkbox" id="Macula_BDR_OS_3" name="Macula_BDR_OS_3" <?php if($Macula_BDR_OS_3==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+3</span></label></div>
                                <div class="col-sm-1"><?php if($elem_macOs_dr_bdr_pos4 == "+4") $Macula_BDR_OS_4=1; ?><label><input type="checkbox" id="Macula_BDR_OS_4" name="Macula_BDR_OS_4" <?php if($Macula_BDR_OS_4==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+4</span></label></div>
                            </div>
                            <div class="row">                                            
                                <div class="col-sm-3 tdlft">SMD</div>
                                <div class="col-sm-2"><label><input type="checkbox" id="Macula_SMD_OS_T" name="Macula_SMD_OS_T" <?php if($Macula_SMD_OS_T==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">T</span></label></div>
                                <div class="col-sm-2"><label><input type="checkbox" id="Macula_SMD_OS_1" name="Macula_SMD_OS_1" <?php if($Macula_SMD_OS_1==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+1</span></label></div>
                                <div class="col-sm-2"><label><input type="checkbox" id="Macula_SMD_OS_2" name="Macula_SMD_OS_2" <?php if($Macula_SMD_OS_2==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+2</span></label></div>
                                <div class="col-sm-2"><label><input type="checkbox" id="Macula_SMD_OS_3" name="Macula_SMD_OS_3" <?php if($Macula_SMD_OS_3==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+3</span></label></div>
                                <div class="col-sm-1"><label><input type="checkbox" id="Macula_SMD_OS_4" name="Macula_SMD_OS_4" <?php if($Macula_SMD_OS_4==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+4</span></label></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><label><input type="checkbox" id="Feeder_Vessel_OS" name="Feeder_Vessel_OS" <?php if($Feeder_Vessel_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Feeder  Vessel</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="Central_OS" name="Central_OS" <?php if($Central_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Central</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="Nasal_OS" name="Nasal_OS" <?php if($Nasal_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Nasal</span></label></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><label><input type="checkbox" id="Temporal_OS" name="Temporal_OS" <?php if($Temporal_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Temporal</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="Inferior_OS" name="Inferior_OS" <?php if($Inferior_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Inferior</span></label></div>
                                <div class="col-sm-4"><label><input type="checkbox" id="Superior_OS" name="Superior_OS" <?php if($Superior_OS==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Superior</span></label></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4"><label><input type="checkbox" id="Hot_Spot_OS" name="Hot_Spot_OS" <?php if($Hot_Spot_OS==1) echo 'CHECKED'; ?> value="1" onClick="enableDisable(this.id,'Hot_Spot_Val_OS');"><span class="label_txt">Hot Spot</span></label></div>
                                <div class="col-sm-2"><input type="text" id="Hot_Spot_Val_OS" name="Hot_Spot_Val_OS" value="<?php echo ($Hot_Spot_Val_OS);?>" class="form-control" <?php if($Hot_Spot_OS!=1) echo 'disabled';?>></div>
                            </div>
                          </td>
                      </tr>
                        <tr>
                          <td class="tdlftpan"><strong>Other</strong></td>
                          <td class="tstrstopt">
                            <?php
							if(!$elem_testresults_desc_od)$elem_testresults_desc_od = $ermOd.''.$atrophicChangeOd.''.$peripheralMaculaOd.''.$edemaOd.''.$cottonWoolSpotsOd.''.$PDROd.''.$cmeOd.''.$ermOd.''.$nevusOd;?>
							<textarea cols="35" rows="3" id="elem_testresults_desc_od" name="elem_testresults_desc_od" class="form-control" style="width:100%; height:50px;"><?php echo $elem_testresults_desc_od;?></textarea>
                          </td>
                          <td class="tstrstopt">
                            <?php
							if(!$elem_testresults_desc_os)$elem_testresults_desc_os = $ermOs.''.$atrophicChangeOs.''.$peripheralMaculaOs.''.$edemaOs.''.$cottonWoolSpotsOs.''.$PDROs.''.$cmeOs.''.$ermOs.''.$nevusOs;?>
							<textarea cols="35" rows="3" id="elem_testresults_desc_os" name="elem_testresults_desc_os" class="form-control" style="width:100%; height:50px;"><?php echo $elem_testresults_desc_os;?></textarea>
                          </td>
                      </tr>
                    </table>
                </div>
                <div class="clearfix"></div>
                <div class="tstfot">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="whitebox">
                                <h2>Treatment/Prognosis</h2>
                                <div class="clearfix"></div>
                                <div class="tstrstopt">
                                    <div class="row">
                                        <div class="col-sm-3"><label><input type="checkbox" name="elem_Stable" value="1" <?php echo ($elem_Stable == "1") ? "checked" : "";?>><span class="label_txt">Stable</span></label></div>
                                        <div class="col-sm-3"><label><input type="checkbox" name="elem_contiMeds" value="1" <?php echo ($elem_contiMeds == "1") ? "checked" : "";?>><span class="label_txt">Continue Meds</span></label></div>
                                        <div class="col-sm-3"><label><input type="checkbox" name="elem_MonitorAg" value="1" <?php echo ($elem_MonitorAg == "1") ? "checked" : "";?>><span class="label_txt">Monitor AG</span></label></div>
                                        <div class="col-sm-3"><label><input type="checkbox" name="elem_tech2InformPt" value="1" <?php echo ($elem_tech2InformPt == "1") ? "checked" : "";?>><span class="label_txt">Tech to Inform Pt.</span></label></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3"><label><input type="checkbox" name="elem_informedPtNv" value="1" <?php echo ($elem_informedPtNv == "1") ? "checked" : "";?>><span class="label_txt">Inform Pt result next visit</span></label></div>
                                        <div class="col-sm-3"><label><input type="checkbox" name="elem_FuApa" value="1" <?php echo ($elem_FuApa == "1") ? "checked" : "";?>><span class="label_txt">F/U APA</span></label></div>
                                        <div class="col-sm-3"><label><input type="checkbox" name="elem_PatientInformed" value="1" <?php echo ($elem_PatientInformed == "1") ? "checked" : "";?>><span class="label_txt">Pt informed of results</span></label></div>
                                        <div class="col-sm-3"><label><input type="checkbox" name="elem_rptTst1yr" value="1" <?php echo ($elem_rptTst1yr == "1") ? "checked" : "";?>><span class="label_txt">Repeat test 1 year</span></label></div>
                                    </div>
                                    <div class="row">
                                    	<div class="col-sm-3"><label><input type="checkbox" name="elem_ArgonLaser" value="1" <?php echo ($elem_ArgonLaser == "1") ? "checked" : "";?>><span class="label_txt">Argon Laser Surgery</span></label></div>
                                        <div class="col-sm-3"><label><input type="radio" name="elem_ArgonLaserEye" value="OU" <?php echo (!$elem_ArgonLaserEye || $elem_ArgonLaserEye == "OU")? "checked" : ""; ?> onClick="dispALEye()" ><span class="drak_purple_color label_txt">OU</span></label>
                                            <label><input type="radio" name="elem_ArgonLaserEye" value="OD" <?php echo ($elem_ArgonLaserEye == "OD")? "checked" : ""; ?> onClick="dispALEye()"><span class="blue_color label_txt">OD</span></label>
                                            <label><input type="radio" name="elem_ArgonLaserEye" value="OS" <?php echo ($elem_ArgonLaserEye == "OS")? "checked" : ""; ?> onClick="dispALEye()"><span class="green_color label_txt">OS</span></label>
                                        </div>
                                        <div class="col-smi-3" id="als" style="visibility:<?php echo ($elem_ArgonLaserEye != "") ? "visible" : "hidden"; ?>;">
                                            <select class="txt_11" name="elem_ArgonLaserEyeOptions">
                                                <option></option>
                                                <option value="Focal" <?php echo ($elem_ArgonLaserEyeOptions == "Focal") ? "selected" : "" ;?>>Focal </option>
                                                <option value="PRP" <?php echo ($elem_ArgonLaserEyeOptions == "PRP") ? "selected" : "" ;?>>PRP</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-sm-2"><label><input type="checkbox" name="elem_FuRetina" value="1" <?php echo ($elem_FuRetina == "1") ? "checked" : "";?> onClick="dispFUComments(this)"><span class="label_txt">F/U Retina</span></label></div>
                                        <div class="col-sm-2"><span id="coment" style="visibility:<?php echo ($elem_FuRetina == "1") ? "visible" : "hidden" ;?>;"><textarea cols="10" rows="1" name="elem_FuRetinaComments" style="height:25px; width:100%;"><?php echo ($elem_FuRetinaComments);?></textarea></span></div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                
                                <div>
                                    <h2>Comments</h2>
                                    <div class="clearfix"></div>
                                    <textarea cols="23" rows="2"  id="icgComments" name="icgComments" class="form-control" style="width:100%; height:50px;"><?php echo $icgComments; ?></textarea>
                                </div>
                                <div class="clearfix"></div>
                                
                                <?php if($callFromInterface != 'admin'){
									$sigFolderName = 'test_icg';
									require_once("future_appt_interpret_by.php");
								} ?>
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
                				<?php echo $objTests->get_zeiss_forum_button('ICG',$test_edid,$patient_id);?>
                            </div>
                        	<?php }?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <?php if($callFromInterface != 'admin' && $doNotShowRightSide != 'yes'){
				$test_scan_edit_id_scan = $test_edid;
				require_once("test_saved_list.php");
			} ?>
       <?php if($callFromInterface != 'admin'){?>
				
				<?php if(($elem_per_vo != "1" && $doNotShowRightSide == 'yes') || $_REQUEST['pop']==1){?>
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
							<input type="button" class="btn btn-success<?php echo $btnHide;?>" id="btn_done" value="Done"  onClick="saveIcg()" />
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
							<input type="button"  class="btn btn-success<?php echo $btnHide;?>" value="Order" id="save" onClick="saveIcg()" />
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
						top.btn_show("ICG",btnArr);
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
   // init_page_display();
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
	
});

<?php if($callFromInterface != 'admin'){?>
	set_header_title('<? echo $this_test_screen_name;?>');
	<?php if(($elem_icg_od == "2") || ($elem_icg_od == "3")){$tmp = (($elem_icg_od == "3")) ? "OS" : "OD";echo "setReli_Test(\"".$tmp."\");";}?>
<?php }?>
</script>
<?php
if($callFromInterface != 'admin'){
	//Previous Test div--
	$oChartTestPrev->showdiv();
    if($_GET["tId"]!='')	// position of include file cannot go above than these lines
	{
		include 'test_icg_print.php';
	}
}
?></body>
</html>