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
$testname               = "discExternal";
$objEpost				= new Epost($patient_id,$testname);
//$objTests->patient_id 	= $patient_id;

//MAKING OBJECT TO SAVE IMAGE FILES
$oSaveFile = new SaveFile($patient_id);

$test_table_name		= 'disc_external';
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
	$html_file_name = $test_name.'/'.$date_f.'/external_test_print_'. $_SESSION['patient']."_".$_SESSION['authId']."_".$rand;
	$objTests->mk_print_folder($test_name,$date_f,$oSaveFile->upDir."/UserId_".$_SESSION['authId']."/tmp/");
	$final_html_file_name_path = $oSaveFile->upDir."/UserId_".$_SESSION['authId']."/tmp/".$html_file_name;
    
	/*$date_f=date("Y_m_d");
	$rand=rand(0,500);
	$test_name="chart_tests";
	$html_file_name = $test_name.'/'.$date_f.'/external_test_print_'.$_SESSION['authId']."_".$rand;
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
	$oChartTestPrev = new ChartTestPrev($patient_id,"EXTERNAL/ANTERIOR");

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
		$q = "SELECT * FROM disc_external WHERE patientId = '$patient_id' AND formId = '$form_id' AND purged='0' AND del_status='0'";
		$res = imw_query($q);
		$row = imw_fetch_assoc($res);
	}else if(isset($tId) && !empty($tId)){//Get record based on patient id and test id
			$q = "SELECT * FROM disc_external WHERE patientId = '$patient_id' AND disc_id = '".$tId."'";
			$res = imw_query($q);
			$row = imw_fetch_assoc($res);
	}else{
		$row = false; // open new test for patient
	}


	if($row == false){	//&& ($finalize_flag == 0)
		//SET MODE
		$disc_mode = "new";
		$test_edid = "0";
	}else{
		$disc_mode = "update";
		$test_edid = $row["disc_id"];
	}

	//Default
	if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){//New Recors
		$tmp = ($row != false) ? $row["examDate"] : "";
		$row = $objTests->valuesNewRecordsTests($test_table_name, $patient_id, " * ",$tmp);
	}


	if($row != false){
		$disc_pkid = $row["disc_id"];
		$test_form_id = $row["formId"];
		$elem_examDate = ($disc_mode != "new") ? get_date_format($row["examDate"]) : $elem_examDate ;
		$elem_examTime = ($disc_mode != "new") ? $row["examTime"] : $elem_examTime ;
		$elem_fundusDiscPhoto = $row["fundusDiscPhoto"];
		$elem_shots = $row["shots"];
		$elem_extraCopy = $row["extraCopy"];
		$elem_desc = stripslashes($row["discDesc"]);
		$elem_photoEye = $row["photoEye"];
		$elem_performedBy = ($disc_mode != "new") ? $row["performedBy"] : "";
		$elem_ptUnderstanding = $row["ptUnderstanding"];
		$elem_reliabilityOd = $row["reliabilityOd"];
		$elem_reliabilityOs = $row["reliabilityOs"];
		$elem_discOd = $row["discOd"];
		$elem_cdOd = $row["cdOd"];
		$elem_retinaOd = $row["retinaOd"];
		$elem_maculaOd = $row["maculaOd"];
		$elem_resDescOd = stripslashes($row["resDescOd"]);
		$elem_discOs = $row["discOs"];
		$elem_cdOs = $row["cdOs"];
		$elem_retinaOs = $row["retinaOs"];
		$elem_maculaOs = $row["maculaOs"];
		$elem_resDescOs = stripslashes($row["resDescOs"]);
		$elem_stable = $row["stable"];
		$elem_monitorAg = $row["monitorAg"];
		$elem_fuApa = $row["fuApa"];
		$elem_ptInformed = $row["ptInformed"];
		$elem_fuRetina = $row["fuRetina"];
		$elem_fuRetinaDesc = stripslashes($row["fuRetinaDesc"]);
		$elem_signature = $row["signature"];
		$elem_physician = ($disc_mode == "update") ? $row["phyName"] : "";
		$elem_ptosisOd_neg=$row["ptosisOd_neg"];
		$elem_ptosisOd_T=$row["ptosisOd_T"];
		$elem_ptosisOd_pos1=$row["ptosisOd_pos1"];
		$elem_ptosisOd_pos2=$row["ptosisOd_pos2"];
		$elem_ptosisOd_pos3=$row["ptosisOd_pos3"];
		$elem_ptosisOd_pos4=$row["ptosisOd_pos4"];
		$elem_ptosisOd_rul=$row["ptosisOd_rul"];
		$elem_ptosisOd_rll=$row["ptosisOd_rll"];
		$elem_dermaOd_neg=$row["dermaOd_neg"];
		$elem_dermaOd_T=$row["dermaOd_T"];
		$elem_dermaOd_pos1=$row["dermaOd_pos1"];
		$elem_dermaOd_pos2=$row["dermaOd_pos2"];
		$elem_dermaOd_pos3=$row["dermaOd_pos3"];
		$elem_dermaOd_pos4=$row["dermaOd_pos4"];
		$elem_dermaOd_rul=$row["dermaOd_rul"];
		$elem_dermaOd_rll=$row["dermaOd_rll"];
		$elem_pterygium1mmOd=$row["pterygium1mmOd"];
		$elem_pterygium2mmOd=$row["pterygium2mmOd"];
		$elem_pterygium3mmOd=$row["pterygium3mmOd"];
		$elem_pterygium4mmOd=$row["pterygium4mmOd"];
		$elem_pterygium5mmOd=$row["pterygium5mmOd"];
		$elem_pterygiumNasalOd=$row["pterygiumNasalOd"];
		$elem_vascOd_Superficial=$row["vascOd_Superficial"];
		$elem_vascOd_Deep=$row["vascOd_Deep"];
		$elem_vascOd_Endothelial=$row["vascOd_Endothelial"];
		$elem_vascOd_Peripheral=$row["vascOd_Peripheral"];
		$elem_vascOd_Central=$row["vascOd_Central"];
		$elem_vascOd_Pannus=$row["vascOd_Pannus"];
		$elem_vascOd_GhostBV=$row["vascOd_GhostBV"];
		$elem_vascOd_Inferior=$row["vascOd_Inferior"];
		$elem_vascOd_Nasal=$row["vascOd_Nasal"];
		$elem_vascOd_Temporal=$row["vascOd_Temporal"];
		$elem_NevusOd_neg=$row["NevusOd_neg"];
		$elem_NevusOd_Pos=$row["NevusOd_Pos"];
		$elem_NevusOd_Inferior=$row["NevusOd_Inferior"];
		$elem_NevusOd_Superior=$row["NevusOd_Superior"];
		$normal_OS=$row["normal_OS"];
		$elem_NevusOd_Temporal=$row["NevusOd_Temporal"];
		$elem_NevusOd_Nasal=$row["NevusOd_Nasal"];
		$elem_ptosisOs_neg=$row["ptosisOs_neg"];
		$elem_ptosisOs_T=$row["ptosisOs_T"];
		$elem_ptosisOs_pos1=$row["ptosisOs_pos1"];
		$elem_ptosisOs_pos2=$row["ptosisOs_pos2"];
		$elem_ptosisOs_pos3=$row["ptosisOs_pos3"];
		$elem_ptosisOs_pos4=$row["ptosisOs_pos4"];
		$elem_ptosisOs_rul=$row["ptosisOs_rul"];
		$elem_ptosisOs_rll=$row["ptosisOs_rll"];
		$elem_dermaOs_neg=$row["dermaOs_neg"];
		$elem_dermaOs_T=$row["dermaOs_T"];
		$elem_dermaOs_pos1=$row["dermaOs_pos1"];
		$elem_dermaOs_pos2=$row["dermaOs_pos2"];
		$elem_dermaOs_pos3=$row["dermaOs_pos3"];
		$elem_dermaOs_pos4=$row["dermaOs_pos4"];
		$elem_dermaOs_rul=$row["dermaOs_rul"];
		$elem_dermaOs_rll=$row["dermaOs_rll"];
		$elem_pterygium1mmOs=$row["pterygium1mmOs"];
		$elem_pterygium2mmOs=$row["pterygium2mmOs"];
		$elem_pterygium3mmOs=$row["pterygium3mmOs"];
		$elem_pterygium4mmOs=$row["pterygium4mmOs"];
		$elem_pterygium5mmOs=$row["pterygium5mmOs"];
		$elem_pterygiumNasalOs=$row["pterygiumNasalOs"];
		$elem_pterygiumTemporalOs=$row["pterygiumTemporalOs"];
		$elem_vascOs_SubEpithelial=$row["vascOs_SubEpithelial"];
		$elem_vascOs_Stromal=$row["vascOs_Stromal"];
	
		$elem_vascOs_Superficial=$row["vascOs_Superficial"];
		$elem_vascOs_Deep=$row["vascOs_Deep"];
		$elem_vascOs_Endothelial=$row["vascOs_Endothelial"];
		$elem_vascOs_Peripheral=$row["vascOs_Peripheral"];
		$elem_vascOs_Central=$row["vascOs_Central"];
		$elem_vascOs_Pannus=$row["vascOs_Pannus"];
		$elem_vascOs_GhostBV=$row["vascOs_GhostBV"];
		$elem_vascOs_Superior=$row["vascOs_Superior"];
		$elem_vascOs_Inferior=$row["vascOs_Inferior"];
		$elem_vascOs_Nasal = $row["vascOs_Nasal"];
		$elem_vascOs_Temporal = $row["vascOs_Temporal"];
		$elem_irisNevusOs_neg = $row["irisNevusOs_neg"];
		$elem_irisNevusOs_Pos = $row["irisNevusOs_Pos"];
		$elem_irisNevusOs_Inferior = $row["irisNevusOs_Inferior"];
		$elem_irisNevusOs_Temporal = $row["irisNevusOs_Temporal"];
		$elem_irisNevusOs_Nasal = $row["irisNevusOs_Nasal"];
		$elem_irisNevusOs_Superior = $row["irisNevusOs_Superior"];
		$elem_vascOd_Superior = $row["vascOd_Superior"];
		$elem_pterygiumTemporalOd = $row["pterygiumTemporalOd"];
		$elem_vascOd_SubEpithelial = $row["vascOd_SubEpithelial"];
		$elem_vascOd_Stromal = $row["vascOd_Stromal"];
	
		$elem_tech2InformPt = $row["tech2InformPt"];
		$discComments = stripslashes($row["discComments"]);
		$elem_informedPtNv = $row["ptInformedNv"];
		$elem_contiMeds = $row["contiMeds"];
		$elem_diagnosis = $row["diagnosis"];
		$elem_diagnosisOther = $row["diagnosisOther"];
		$encounterId = $row["encounter_id"];
		$elem_opidTestOrdered = $row["ordrby"];
		if(($row["ordrdt"] != "" && $row["ordrdt"] != "0000-00-00")){
			$elem_opidTestOrderedDate = get_date_format($row["ordrdt"]);
		}
		$purged=$row["purged"];
		$sign_path = $row["sign_path"];
		$sign_path_date_time = $row["sign_path_date_time"];
		$sign_path_date = $sign_path_time = "";
		if($sign_path && $sign_path_date_time!="0000-00-00 00:00:00" || $sign_path_date_time!=0) {
			//list($s_y,$s_m,$s_d,$s_h,$s_i,$s_s) = explode("-",)	
			$sign_path_date = date("".phpDateFormat()."",strtotime($sign_path_date_time));
			$sign_path_time = date("h:i A",strtotime($sign_path_date_time));
		}		
		$forum_procedure = $row["forum_procedure"];
	}

	if(!empty($form_id)){
		//get CD Values
		if(($elem_cdOd == "") && ($elem_cdOs == "")){
			list($elem_cdOd, $elem_cdOs) = $objTests->getCdValues($patient_id, $form_id);
		}
		//TEST commenting it.
		//include_once("getExtractedResults.php");
	
		//Get Macula Values Values
		list($cn_opt,$cn_dscod,$cn_dscos) = $objTests->getCnCom($form_id);
		if(empty($elem_fundusDiscPhoto)){
			$elem_fundusDiscPhoto = $cn_opt;
		}
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
	$sb_testName = "External";
	
	//Cpt Code Desc
	$thisCptDescSym = "External";

	//include($incdir."/chart_notes/superbill_init.php");
	//Super bill init() --

	//Prev + Next Records --
	$tmp = getDateFormatDB($elem_examDate);//Exam Date
	$tstPrevId = $oChartTestPrev->getPrevId($tmp,$disc_pkid);//getPervId
	$tstNxtId = $oChartTestPrev->getNxtId($tmp,$disc_pkid);//getNextId
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

<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" rel="stylesheet">
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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/typeahead.js"></script>
<script src="<?php echo $library_path; ?>/js/common.js?<?php echo filemtime('../../library/js/common.js');?>" type="text/javascript"></script>
<script src="<?php echo $library_path; ?>/js/tests.js?<?php echo filemtime('../../library/js/tests.js');?>" type="text/javascript"></script>

<?php if($callFromInterface != 'admin'){?>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/icd10_autocomplete.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/js_gen.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/superbill.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/lightbox/lightbox.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/epost.js"></script>
<?php }?>
<script type="text/javascript">
var imgPath 		= "<?php echo $GLOBALS['webroot'];?>";
var elem_per_vo 	= "<?php echo $elem_per_vo;?>";
var zPath			= "<?php echo $GLOBALS['rootdir'];?>";
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
	function printTest()
	{
		var tId = "<?php echo $_GET["tId"];?>";
		window.open('../../library/html_to_pdf/createPdf.php?op=l&onePage=false&file_location=<?php echo $html_file_name;?>&mergePDF=1&name=test_'+tId+'_pdf&testIds='+tId+'&saveOption=F&image_from=<?php echo $testname; ?>','vf_Rpt','menubar=0,resizable=yes');	
	}
	
	// Prev Data
	var oPrvDt;
	function getPrevTestDt(ctid,cdt,ctm,dir){
		getPrevTestDtExe(ctid,cdt,ctm,dir,"EXTERNAL/ANTERIOR");
	}
	
	function saveDisc(){
		if(top.frm_submited==1){ return; }
		top.frm_submited=1;
		<?php if($elem_per_vo == "1"){echo "return;";}?>
	
		var f = document.test_form_frm;
		var m = "<b>Please fill the following:-</b><br>";
		var err = false;
		var elemRd = f.elem_fundusDiscPhoto;
		var flagRd = false;
		 //elem_physician
		if ( (f.elem_performedBy.value == "") || (f.elem_performedBy.value == "0") ) {
			m += "&bull; Performed by (Physician or Technician only)<br>";
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

function setDiagOpts(){
	var chk=1;
	var ord = $("#elem_fundusDiscPhoto",1);
	var ln=ord.length;
	for(var i=0;i<ln;i++){
		if(ord[i].checked == true){
			chk = ord[i].value;
		}
	}

	var osel = document.getElementById("elem_diagnosis");
	osel.options.length=0;

	var arr = <?php echo $objTests->getDiagOpts("JS",'discexternal',$test_master_id);?>;
	//Adde
	var arr2 = arr[chk-1];
	ln = arr2.length;
	osel.options[osel.options.length]=new Option("","");
	for(var i=0; i<ln; i++){
		osel.options[osel.options.length]=new Option(""+arr2[i],""+arr2[i]);
	}
	//width
	osel.style.width=(chk==3)?"220px":"200px";
}


function selectOs(){
	var elements = document.test_form_frm.elements;
	var eleLen = elements.length;
	for(var i=0;i<eleLen;i++){
		if(elements[i].type == "checkbox"){
			var eleName = elements[i].name;
			if(eleName.indexOf('Od')!= -1){
				var chkStatus = elements[i].checked;
				eleName = eleName.replace(/Od/, "Os")
				if(document.getElementById(eleName)){
					document.getElementById(eleName).checked = chkStatus;
				}
			}
		}
	}
	document.getElementById('elem_resDescOs').value = document.getElementById('elem_resDescOd').value;
	//document.getElementById('elem_cdOs').value = document.getElementById('elem_cdOd').value;
}

function displayFUComment(o){
	document.getElementById("coment").style.visibility = (o.checked == true) ? "visible" : "hidden" ;
}

//Set Tests and Reliability fields as per interpretation
function setReli_Test(wh){
	var arrReli = new Array("elem_reliabilityOd","elem_reliabilityOs");
	var arrTestOd = new Array("elem_ptosisOd_neg","elem_ptosisOd_T","elem_ptosisOd_pos1","elem_ptosisOd_pos2",
					 "elem_ptosisOd_pos3","elem_ptosisOd_pos4","elem_ptosisOd_rul","elem_ptosisOd_rll",
					 "elem_dermaOd_neg","elem_dermaOd_T","elem_dermaOd_pos1","elem_dermaOd_pos2",
					 "elem_dermaOd_pos3","elem_dermaOd_pos4","elem_dermaOd_rul","elem_dermaOd_rll",
					 "elem_pterygium1mmOd","elem_pterygium2mmOd","elem_pterygium3mmOd","elem_pterygium4mmOd",
					 "elem_pterygium5mmOd","elem_pterygiumNasalOd","elem_pterygiumTemporalOd","elem_vascOd_SubEpithelial",
					 "elem_vascOd_Stromal","elem_vascOd_Superficial","elem_vascOd_Deep","elem_vascOd_Endothelial","elem_vascOd_Peripheral",
					 "elem_vascOd_Central","elem_vascOd_Pannus","elem_vascOd_GhostBV","elem_vascOd_Superior","elem_vascOd_Inferior",
					 "elem_vascOd_Nasal","elem_vascOd_Temporal","elem_NevusOd_neg","elem_NevusOd_Pos","elem_NevusOd_Inferior",
					 "elem_NevusOd_Superior","elem_NevusOd_Temporal","elem_NevusOd_Nasal","elem_resDescOd");
	setReli_Exe(wh,arrTestOd,arrReli);
}
</script>
</head>
<body>
<form name="test_form_frm" action="save_tests.php" method="post" style="margin:0px;">
<?php if($callFromInterface != 'admin'){?>
    <input type="hidden" name="elem_saveForm" id="elem_saveForm" value="discExternal">
    <input type="hidden" name="elem_patientId" id="elem_patientId" value="<?php echo $patient_id;?>">
    <input type="hidden" name="elem_formId" id="elem_formId" value="<?php echo $form_id;?>">
    <input type="hidden" name="elem_tests_name_id" id="elem_tests_name_id" value="<?php echo $test_master_id;?>">
    <input type="hidden" id="elem_edMode" name="hd_disc_mode" value="<?php echo $disc_mode;?>">
    <input type="hidden" id="elem_testId" name="elem_discId" value="<?php echo $test_edid;?>">
    <input type="hidden" name="wind_opn" id="wind_opn" value="0">
    <input type="hidden" name="elem_operatorId" value="<?php echo $elem_operatorId;?>">
    <input type="hidden" name="elem_operatorName" value="<?php echo $elem_operatorName;?>">
    <input type="hidden" name="elem_noP" value="<?php echo $noP;?>">
    <input type="hidden" name="elem_examTime" value="<?php echo $elem_examTime;?>">
    <input type="hidden" name="pop" value="<?php echo $_REQUEST['pop'] ?>">
    <!--the hidden field doNotShowRightSide	is used for mest maneger-->
    <input type="hidden" name="doNotShowRightSide" value="<?php echo $_REQUEST['doNotShowRightSide']; ?>">
    <input type="hidden" name="hidFormLoaded" id="hidFormLoaded" value="0">
    <input type="hidden" name="zeissAction" id="zeissAction" value="">
    <input type="hidden" name="elem_phyName_order" id="elem_phyName_order" value="<?php echo $elem_phyName_order; ?>" data-phynm="<?php echo (!empty($elem_phyName_order)) ? $objTests->getPersonnal3($elem_phyName_order) : "" ; ?>" >
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
                        <div class="col-sm-7 sitetab tstrstopt">
                            <ul>
                                <li><label><input type="radio" name="elem_fundusDiscPhoto" value="1" <?php echo ($elem_fundusDiscPhoto == "1") ? "checked" : "" ;?>><span class="label_txt">ES&nbsp;(External)</span></label></li>
                                <li style="margin-left:15px !important"><label><input type="radio" name="elem_fundusDiscPhoto" value="2" <?php echo ($elem_fundusDiscPhoto == "2") ? "checked" : "" ;?>><span class="label_txt">ASP (Anterior Segment Photos)</span></label></li>
                            </ul>
                        </div>
                        <div class="col-sm-5 siteopt">
                            <div class="tstopt">
                                <div class="row">
                                    <div class="col-sm-3 sitehd">Sites</div>
                                    <div class="col-sm-5 testopt">
                                        <ul>
                                            <li class="ouc"><label><input type="radio" name="elem_photoEye" value="OU" onClick="setReli_Test(this.value)"  <?php echo (!$elem_photoEye || $elem_photoEye == "OU") ? "checked" : "" ;?>><span class="label_txt">OU</span></label></li>
                                            <li class="odc"><label><input type="radio" name="elem_photoEye" value="OD" onClick="setReli_Test(this.value)"  <?php echo ($elem_photoEye == "OD") ? "checked" : "" ;?>><span class="label_txt">OD</span></label></li>
                                            <li class="osc"><label><input type="radio" name="elem_photoEye" value="OS" onClick="setReli_Test(this.value)"  <?php echo ($elem_photoEye == "OS") ? "checked" : "" ;?>><span class="label_txt">OS</span></label></li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-4">
                                        <?php
                                            /* Purpose: Add dropdown for procedure codes to be used in Zeiss HL7 message*/
                                            if($callFromInterface != 'admin' && constant("ZEISS_FORUM") == "YES"){
                                                $procedure_opts = $objTests->zeissProcOpts(4);
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
                                            $arrDigOpts = $objTests->getDiagOpts($elem_fundusDiscPhoto,'discexternal',$test_master_id);
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
                                <textarea class="form-control" rows="2" style="width:100%; height:55px !important;" name="elem_desc" id="techComment" placeholder="Technician Comments"><?php echo $elem_desc;?></textarea>
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
                                    <label><input type="radio" name="elem_ptUnderstanding" value="Good" <?php echo (!$elem_ptUnderstanding || $elem_ptUnderstanding == "Good") ? "checked" : "" ;?>><span class="label_txt">Good</span></label>
                                    <label><input type="radio" name="elem_ptUnderstanding" value="Fair" <?php echo ($elem_ptUnderstanding == "Fair") ? "checked" : "" ;?>><span class="label_txt">Fair</span></label>
                                    <label><input type="radio" name="elem_ptUnderstanding" value="Poor" <?php echo ($elem_ptUnderstanding == "Poor") ? "checked" : "" ;?>><span class="label_txt">Poor</span></label>
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
                                            <label><input type="radio" name="elem_reliabilityOd" value="Good" <?php echo (!$elem_reliabilityOd || $elem_reliabilityOd == "Good") ? "checked" : "" ;?>><span class="label_txt">Good</span></label>
                                            <label><input type="radio" name="elem_reliabilityOd" value="Fair" <?php echo ($elem_reliabilityOd == "Fair") ? "checked" : "" ;?>><span class="label_txt">Fair</span></label>
                                            <label><input type="radio" name="elem_reliabilityOd" value="Poor" <?php echo ($elem_reliabilityOd == "Poor") ? "checked" : "" ;?>><span class="label_txt">Poor</span></label>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td rowspan="7" align="center" valign="middle" class="bltra"><a href="javascript:selectOs();">BL</a></td>
                            <td class="osstrip">
                                <div class="row">
                                    <div class="col-sm-1">OS</div>
                                    <div class="col-sm-11 text-right">
                                        <div class="plr10 tstrstopt">
                                            <label><input type="radio" name="elem_reliabilityOs" value="Good" <?php echo (!$elem_reliabilityOs || $elem_reliabilityOs == "Good") ? "checked" : "" ;?>><span class="label_txt">Good</span></label>
                                            <label><input type="radio" name="elem_reliabilityOs" value="Fair" <?php echo ($elem_reliabilityOs == "Fair") ? "checked" : "" ;?>><span class="label_txt">Fair</span></label>
                                            <label><input type="radio" name="elem_reliabilityOs" value="Poor" <?php echo ($elem_reliabilityOs == "Poor") ? "checked" : "" ;?>><span class="label_txt">Poor</span></label>
                                        </div>
                                    </div>
                                </div>
                            </td>		
                        </tr>
                        <tr>
                            <td class="tdlftpan"><strong>Ptosis</strong></td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_ptosisOd_neg" name="elem_ptosisOd_neg" value="1" <?php echo ($elem_ptosisOd_neg == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">-ve</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_ptosisOd_T" name="elem_ptosisOd_T" value="1" <?php echo ($elem_ptosisOd_T == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">T</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_ptosisOd_pos1" name="elem_ptosisOd_pos1" value="1" <?php echo ($elem_ptosisOd_pos1 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+1</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_ptosisOd_pos2" name="elem_ptosisOd_pos2" value="1" <?php echo ($elem_ptosisOd_pos2 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+2</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_ptosisOd_pos3" name="elem_ptosisOd_pos3" value="1" <?php echo ($elem_ptosisOd_pos3 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+3</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_ptosisOd_pos4" name="elem_ptosisOd_pos4" value="1" <?php echo ($elem_ptosisOd_pos4 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+4</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_ptosisOd_rul" name="elem_ptosisOd_rul" value="1" <?php echo ($elem_ptosisOd_rul == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">RUL</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_ptosisOd_rll" name="elem_ptosisOd_rll" value="1" <?php echo ($elem_ptosisOd_rll == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">RLL</span></label></div>
                                </div>
                        	</td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_ptosisOs_neg" name="elem_ptosisOs_neg" value="1" <?php echo ($elem_ptosisOs_neg == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">-ve</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_ptosisOs_T" name="elem_ptosisOs_T" value="1" <?php echo ($elem_ptosisOs_T == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">T</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_ptosisOs_pos1" name="elem_ptosisOs_pos1" value="1" <?php echo ($elem_ptosisOs_pos1 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+1</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_ptosisOs_pos2" name="elem_ptosisOs_pos2" value="1" <?php echo ($elem_ptosisOs_pos2 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+2</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_ptosisOs_pos3" name="elem_ptosisOs_pos3" value="1" <?php echo ($elem_ptosisOs_pos3 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+3</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_ptosisOs_pos4" name="elem_ptosisOs_pos4" value="1" <?php echo ($elem_ptosisOs_pos4 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+4</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_ptosisOs_rul" name="elem_ptosisOs_rul" value="1" <?php echo ($elem_ptosisOs_rul == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">LUL</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_ptosisOs_rll" name="elem_ptosisOs_rll" value="1" <?php echo ($elem_ptosisOs_rll == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">LLL</span></label></div>
                                </div>
                        	</td>
						<tr>
                            <td class="tdlftpan"><strong>Dermatochalasis</strong></td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_dermaOd_neg" name="elem_dermaOd_neg" value="1" <?php echo ($elem_dermaOd_neg == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">-ve</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_dermaOd_T" name="elem_dermaOd_T" value="1" <?php echo ($elem_dermaOd_T == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">T</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_dermaOd_pos1" name="elem_dermaOd_pos1" value="1" <?php echo ($elem_dermaOd_pos1 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+1</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_dermaOd_pos2" name="elem_dermaOd_pos2" value="1" <?php echo ($elem_dermaOd_pos2 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+2</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_dermaOd_pos3" name="elem_dermaOd_pos3" value="1" <?php echo ($elem_dermaOd_pos3 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+3</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_dermaOd_pos4" name="elem_dermaOd_pos4" value="1" <?php echo ($elem_dermaOd_pos4 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+4</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_dermaOd_rul" name="elem_dermaOd_rul" value="1" <?php echo ($elem_dermaOd_rul == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">RUL</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_dermaOd_rll" name="elem_dermaOd_rll" value="1" <?php echo ($elem_dermaOd_rll == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">RLL</span></label></div>
                                </div>
							</td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_dermaOs_neg" name="elem_dermaOs_neg" value="1" <?php echo ($elem_dermaOs_neg == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">-ve</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_dermaOs_T" name="elem_dermaOs_T" value="1" <?php echo ($elem_dermaOs_T == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">T</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_dermaOs_pos1" name="elem_dermaOs_pos1" value="1" <?php echo ($elem_dermaOs_pos1 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+1</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_dermaOs_pos2" name="elem_dermaOs_pos2" value="1" <?php echo ($elem_dermaOs_pos2 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+2</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_dermaOs_pos3" name="elem_dermaOs_pos3" value="1" <?php echo ($elem_dermaOs_pos3 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+3</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_dermaOs_pos4" name="elem_dermaOs_pos4" value="1" <?php echo ($elem_dermaOs_pos4 == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">+4</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_dermaOs_rul" name="elem_dermaOs_rul" value="1" <?php echo ($elem_dermaOs_rul == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">LUL</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="elem_dermaOs_rll" name="elem_dermaOs_rll" value="1" <?php echo ($elem_dermaOs_rll == "1") ? "checked=\"checked\"" : "";?>><span class="label_txt">LLL</span></label></div>
                                </div>
							</td>
                        </tr>
                        <tr>
                            <td class="tdlftpan"><strong>Pterygium</strong></td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_pterygium1mmOd" name="elem_pterygium1mmOd" value="1" <?php echo ($elem_pterygium1mmOd == "1") ? "checked=checked" : "" ;?>><span class="label_txt">1mm</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_pterygium2mmOd" name="elem_pterygium2mmOd" value="1" <?php echo ($elem_pterygium2mmOd == "1") ? "checked=checked" : "" ;?>><span class="label_txt">2mm</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_pterygium3mmOd" name="elem_pterygium3mmOd" value="1" <?php echo ($elem_pterygium3mmOd == "1") ? "checked=checked" : "" ;?>><span class="label_txt">3mm</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_pterygium4mmOd" name="elem_pterygium4mmOd" value="1" <?php echo ($elem_pterygium4mmOd == "1") ? "checked=checked" : "" ;?>><span class="label_txt">4mm</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox"  onClick="" id="elem_pterygium5mmOd" name="elem_pterygium5mmOd" value="1" <?php echo ($elem_pterygium5mmOd == "1") ? "checked=checked" : "" ;?>><span class="label_txt">5mm</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_pterygiumNasalOd" name="elem_pterygiumNasalOd" value="1" <?php echo ($elem_pterygiumNasalOd == "1") ? "checked=checked" : "" ;?>><span class="label_txt">Nasal</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox"  onclick="" id="elem_pterygiumTemporalOd" name="elem_pterygiumTemporalOd" value="1" <?php echo ($elem_pterygiumTemporalOd == "1") ? "checked=checked" : "" ;?>><span class="label_txt">Temporal</span></label></div>
                                </div>
							</td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_pterygium1mmOs" name="elem_pterygium1mmOs" value="1" <?php echo ($elem_pterygium1mmOs == "1") ? "checked=checked" : "" ;?>><span class="label_txt">1mm</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_pterygium2mmOs" name="elem_pterygium2mmOs" value="1" <?php echo ($elem_pterygium2mmOs == "1") ? "checked=checked" : "" ;?>><span class="label_txt">2mm</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_pterygium3mmOs" name="elem_pterygium3mmOs" value="1" <?php echo ($elem_pterygium3mmOs == "1") ? "checked=checked" : "" ;?>><span class="label_txt">3mm</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_pterygium4mmOs" name="elem_pterygium4mmOs" value="1" <?php echo ($elem_pterygium4mmOs == "1") ? "checked=checked" : "" ;?>><span class="label_txt">4mm</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_pterygium5mmOs" name="elem_pterygium5mmOs" value="1" <?php echo ($elem_pterygium5mmOs == "1") ? "checked=checked" : "" ;?>><span class="label_txt">5mm</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_pterygiumNasalOs" name="elem_pterygiumNasalOs" value="1" <?php echo ($elem_pterygiumNasalOs == "1") ? "checked=checked" : "" ;?>><span class="label_txt">Nasal</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox"  onclick="" id="elem_pterygiumTemporalOs" name="elem_pterygiumTemporalOs" value="1" <?php echo ($elem_pterygiumTemporalOs == "1") ? "checked=checked" : "" ;?>><span class="label_txt">Temporal</span></label></div>
                                </div>
							</td>
                        </tr>
                        <tr>
                            <td class="tdlftpan"><strong>Vascularization</strong></td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_vascOd_SubEpithelial" name="elem_vascOd_SubEpithelial" onClick=";" <?php if($elem_vascOd_SubEpithelial == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Sub-epithelial</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" id="elem_vascOd_Stromal" name="elem_vascOd_Stromal" onClick=";" <?php if($elem_vascOd_Stromal== '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Stromal</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" id="elem_vascOd_Superficial" name="elem_vascOd_Superficial" onClick=";" <?php if($elem_vascOd_Superficial == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Superficial</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_vascOd_Deep" name="elem_vascOd_Deep" onClick=";" <?php if($elem_vascOd_Deep == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Deep</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_vascOd_Endothelial" onClick=";" <?php if($elem_vascOd_Endothelial == '1') echo 'CHECKED'; ?> name="elem_vascOd_Endothelial" value="1"><span class="label_txt">Endothelial</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" id="elem_vascOd_Peripheral" name="elem_vascOd_Peripheral" onClick=";" <?php if($elem_vascOd_Peripheral == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Peripheral</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" id="elem_vascOd_Central" onClick=";" <?php if($elem_vascOd_Central == '1') echo 'CHECKED'; ?> name="elem_vascOd_Central" value="1"><span class="label_txt">Central</span></label></div>
                                    <div class="col-sm-2"><label><input  type="checkbox" id="elem_vascOd_Pannus" onClick=";" <?php if($elem_vascOd_Pannus == '1') echo 'CHECKED'; ?> name="elem_vascOd_Pannus" value="1"><span class="label_txt">Pannus</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_vascOd_GhostBV" name="elem_vascOd_GhostBV" onClick=";" <?php if($elem_vascOd_GhostBV == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Ghost BV</span></label></div>                                                    
                                    <div class="col-sm-3"><label><input  type="checkbox" id="elem_vascOd_Superior" onClick=";" <?php if($elem_vascOd_Superior == '1') echo 'CHECKED'; ?> name="elem_vascOd_Superior" value="1"><span class="label_txt">Superior</span></label></div>                                    
                                    <div class="col-sm-3"><label><input type="checkbox" id="elem_vascOd_Inferior" name="elem_vascOd_Inferior" onClick=";" <?php if($elem_vascOd_Inferior == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Inferior</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_vascOd_Nasal" name="elem_vascOd_Nasal" onClick=";" <?php if($elem_vascOd_Nasal == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Nasal</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-3"><label><input type="checkbox" id="elem_vascOd_Temporal" name="elem_vascOd_Temporal" onClick=";" <?php if($elem_vascOd_Temporal == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Temporal</span></label></div>
                                </div>
							</td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_vascOs_SubEpithelial" name="elem_vascOs_SubEpithelial" onClick=";" <?php if($elem_vascOs_SubEpithelial == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Sub-epithelial</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" id="elem_vascOs_Stromal" name="elem_vascOs_Stromal" onClick=";" <?php if($elem_vascOs_Stromal== '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Stromal</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" id="elem_vascOs_Superficial" name="elem_vascOs_Superficial" onClick=";" <?php if($elem_vascOs_Superficial == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Superficial</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_vascOs_Deep" name="elem_vascOs_Deep" onClick=";" <?php if($elem_vascOs_Deep == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Deep</span></label></div>
                                	<div class="clearfix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_vascOs_Endothelial" onClick=";" <?php if($elem_vascOs_Endothelial == '1') echo 'CHECKED'; ?> name="elem_vascOs_Endothelial" value="1"><span class="label_txt">Endothelial</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" id="elem_vascOs_Peripheral" name="elem_vascOs_Peripheral" onClick=";" <?php if($elem_vascOs_Peripheral == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Peripheral</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox" id="elem_vascOs_Central" onClick=";" <?php if($elem_vascOs_Central == '1') echo 'CHECKED'; ?> name="elem_vascOs_Central" value="1"><span class="label_txt">Central</span></label></div>
                                    <div class="col-sm-2"><label><input  type="checkbox" id="elem_vascOs_Pannus" onClick=";" <?php if($elem_vascOs_Pannus == '1') echo 'CHECKED'; ?> name="elem_vascOs_Pannus" value="1"><span class="label_txt">Pannus</span></label></div>
                                	<div class="clearfix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_vascOs_GhostBV" name="elem_vascOs_GhostBV" onClick=";" <?php if($elem_vascOs_GhostBV == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Ghost BV</span></label></div>                                                    
                                    <div class="col-sm-3"><label><input  type="checkbox" id="elem_vascOs_Superior" onClick=";" <?php if($elem_vascOs_Superior == '1') echo 'CHECKED'; ?> name="elem_vascOs_Superior" value="1"><span class="label_txt">Superior</span></label></div>                                    
                                    <div class="col-sm-3"><label><input type="checkbox" id="elem_vascOs_Inferior" name="elem_vascOs_Inferior" onClick=";" <?php if($elem_vascOs_Inferior == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Inferior</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_vascOs_Nasal" name="elem_vascOs_Nasal" onClick=";" <?php if($elem_vascOs_Nasal == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Nasal</span></label></div>
                                	<div class="clearfix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_vascOs_Temporal" name="elem_vascOs_Temporal" onClick=";" <?php if($elem_vascOs_Temporal == '1') echo 'CHECKED'; ?> value="1"><span class="label_txt">Temporal</span></label></div>
                                </div>
							</td>
                        </tr>
                        <tr>
                            <td class="tdlftpan"><strong>Nevus</strong></td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_NevusOd_neg" name="elem_NevusOd_neg" value="1" <?php echo ($elem_NevusOd_neg=="1") ? "checked" : ""; ?>><span class="label_txt">-ve</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_NevusOd_Pos" name="elem_NevusOd_Pos" value="1" <?php echo ($elem_NevusOd_Pos=="1") ? "checked" : ""; ?>><span class="label_txt">+ve</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox"  onclick="" id="elem_NevusOd_Inferior" name="elem_NevusOd_Inferior" value="1" <?php echo ($elem_NevusOd_Inferior=="1") ? "checked" : ""; ?>><span class="label_txt">Inferior</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox"  onclick="" id="elem_NevusOd_Superior" name="elem_NevusOd_Superior" value="1" <?php echo ($elem_NevusOd_Superior=="1") ? "checked" : ""; ?>><span class="label_txt">Superior</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox"  onclick="" id="elem_NevusOd_Temporal" name="elem_NevusOd_Temporal" value="1" <?php echo ($elem_NevusOd_Temporal=="1") ? "checked" : ""; ?>><span class="label_txt">Temporal</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_NevusOd_Nasal" name="elem_NevusOd_Nasal" value="1" <?php echo ($elem_NevusOd_Nasal=="1") ? "checked" : ""; ?>><span class="label_txt">Nasal</span></label></div>
                                </div>
							</td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_NevusOs_neg" name="elem_irisNevusOs_neg" value="1" <?php echo ($elem_irisNevusOs_neg=="1") ? "checked" : ""; ?>><span class="label_txt">-ve</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_NevusOs_Pos" name="elem_irisNevusOs_Pos" value="1" <?php echo ($elem_irisNevusOs_Pos=="1") ? "checked" : ""; ?>><span class="label_txt">+ve</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox"  onclick="" id="elem_NevusOs_Inferior" name="elem_irisNevusOs_Inferior" value="1" <?php echo ($elem_irisNevusOs_Inferior=="1") ? "checked" : ""; ?>><span class="label_txt">Inferior</span></label></div>
                                    <div class="col-sm-3"><label><input type="checkbox"  onclick="" id="elem_NevusOs_Superior" name="elem_irisNevusOs_Superior" value="1" <?php echo ($elem_irisNevusOs_Superior=="1") ? "checked" : ""; ?>><span class="label_txt">Superior</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-4"><label><input type="checkbox"  onclick="" id="elem_NevusOs_Temporal" name="elem_irisNevusOs_Temporal" value="1" <?php echo ($elem_irisNevusOs_Temporal=="1") ? "checked" : ""; ?>><span class="label_txt">Temporal</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox"  onclick="" id="elem_NevusOs_Nasal" name="elem_irisNevusOs_Nasal" value="1" <?php echo ($elem_irisNevusOs_Nasal=="1") ? "checked" : ""; ?>><span class="label_txt">Nasal</span></label></div>
                                </div>
							</td>
                        </tr>
                        <tr>
                          <td class="tdlftpan"><strong>Description</strong></td>
                          <td><?php if(!$elem_resDescOd) $elem_resDescOd = $cn_dscod.''.$maculaDescOd.''.$atrophicChangeOd.''.$peripheralMaculaOd.''.$cottonWoolSpotsOd.''.$cmeOd.''.$nevusOd.''.$ermOd.''.$hemorrhageOd.''.$bdrPeripheryOd.''.$ExudateOD.''.$cWoolSpotsOd.''.$fScarOd.''.$PDROd.''.$scarPeripheryOd.''.$retinalTearOd.''.$retinalDetachOd.''.$vitCellsOd.''.$atrophicChangesOd.''.$periDegenDrusenOd.''.$latticeOd;?><textarea cols="38" rows="3" id="elem_resDescOd" name="elem_resDescOd" class="form-control" style="width:100%; height:50px;"><?php echo ($elem_resDescOd);?></textarea></td>
                          <td><?php if(!$elem_resDescOs) $elem_resDescOs = $cn_dscos.''.$maculaDescOs.''.$atrophicChangeOs.''.$peripheralMaculaOs.''.$cottonWoolSpotsOs.''.$cmeOs.''.$nevusOs.''.$ermOs.''.$hemorrhageOs.''.$bdrPeripheryOs.''.$ExudateOS.''.$cWoolSpotsOs.''.$fScarOs.''.$PDROs.''.$scarPeripheryOs.''.$retinalTearOs.''.$retinalDetachOs.''.$vitCellsOs.''.$atrophicChangesOs.''.$periDegenDrusenOs.''.$latticeOs;?><textarea cols="38" rows="3" id="elem_resDescOs" name="elem_resDescOs" class="form-control" style="width:100%; height:50px;"><?php echo ($elem_resDescOs);?></textarea></td>
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
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_stable" value="1" <?php echo ($elem_stable == "1") ? "checked" : "" ;?>><span class="label_txt">Stable</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_contiMeds" value="1" <?php echo ($elem_contiMeds == "1") ? "checked" : "" ;?>><span class="label_txt">Continue Meds</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_monitorAg" value="1" <?php echo ($elem_monitorAg == "1") ? "checked" : "" ;?>><span class="label_txt">Monitor AG</span></label></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_tech2InformPt" value="1" <?php echo ($elem_tech2InformPt == "1") ? "checked" : "" ;?>><span class="label_txt">Tech to Inform Pt.</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_informedPtNv" value="1" <?php echo ($elem_informedPtNv == "1") ? "checked" : "" ;?>><span class="label_txt">Inform Pt result next visit</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_fuApa" value="1" <?php echo ($elem_fuApa == "1") ? "checked" : "" ;?>><span class="label_txt">F/U APA</span></label></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_ptInformed" value="1" <?php echo ($elem_ptInformed == "1") ? "checked" : "" ;?>><span class="label_txt">Pt informed of results</span></label></div>
                                        <div class="col-sm-2"><label><input type="checkbox" name="elem_fuRetina" id="elem_fuRetina" value="1" <?php echo ($elem_fuRetina == "1") ? "checked" : "" ;?> onClick="displayFUComment(this)"><span class="label_txt">F/U Retina</span></label></div>
                                        <div class="col-sm-6"><span id="coment" style="visibility:<?php echo ($elem_fuRetina == "1") ? "visible" : "hidden" ;?>;"><textarea cols="10" rows="1" name="elem_fuRetinaDesc" style="height:25px; width:100%;"><?php echo ($elem_fuRetinaDesc);?></textarea></span></div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                
                                <div>
                                    <h2>Comments</h2>
                                    <div class="clearfix"></div>	
                                    <textarea cols="23" rows="2"  id="discComments" name="discComments" class="form-control" style="width:100%; height:50px;"><?php echo $discComments; ?></textarea>
                                </div>
                                <div class="clearfix"></div>
                                
                                <?php if($callFromInterface != 'admin'){
									$sigFolderName = 'test_external';
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
                				<?php echo $objTests->get_zeiss_forum_button('DISCEXTERNAL',$test_edid,$patient_id);?>
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
							<input type="button" class="btn btn-success<?php echo $btnHide;?>" id="btn_done" value="Done"  onClick="saveDisc()" />
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
							<input type="button"  class="btn btn-success<?php echo $btnHide;?>" value="Order" id="save" onClick="saveDisc()" />
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
						top.btn_show("DISC",btnArr);
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
	
});

<?php if($callFromInterface != 'admin'){?>
	set_header_title('Fundus');
	<?php if(($elem_photoEye == "OD") || ($elem_photoEye == "OS")){echo "setReli_Test(\"".$elem_photoEye."\");";}?>
<?php }?>
</script>
<?php
if($callFromInterface != 'admin'){
	//Previous Test div--
	$oChartTestPrev->showdiv();
    if($_GET["tId"]!='')	// position of include file cannot go above than these lines
	{
		include 'test_external_print.php';
	}
}
?> 
</script> 
</body>
</html>