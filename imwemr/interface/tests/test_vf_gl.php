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
$testname               = "VF-GL";
$objEpost				= new Epost($patient_id,$testname);
//$objTests->patient_id 	= $patient_id;

//MAKING OBJECT TO SAVE IMAGE FILES
$oSaveFile = new SaveFile($patient_id);

$test_table_name		= 'vf_gl';
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
	//pre($this_test_properties,1);
	//iMedicMonitor status.
	$objTests->patient_whc_room();
	
	//----GET ALL ACTIVE TESTS FROM ADMIN------
	$ActiveTests			= $objTests->get_active_tests();
	
	// FILE NAME for PRINT
    $date_f=date("Y_m_d");
	$rand=rand(0,500);
	$test_name="chart_tests";
	$html_file_name = $test_name.'/'.$date_f.'/vf_gl_test_print_'. $_SESSION['patient']."_".$_SESSION['authId']."_".$rand;
	$objTests->mk_print_folder($test_name,$date_f,$oSaveFile->upDir."/UserId_".$_SESSION['authId']."/tmp/");
	$final_html_file_name_path = $oSaveFile->upDir."/UserId_".$_SESSION['authId']."/tmp/".$html_file_name;	
	// FILE NAME for PRINT
	/*$date_f=date("Y_m_d");
	$rand=rand(0,500);
	$test_name="chart_tests";
	$html_file_name = $test_name.'/'.$date_f.'/vf_gl_test_print_'. $_SESSION['patient']."_".$_SESSION['authId']."_".$rand;
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
	$oChartTestPrev = new ChartTestPrev($patient_id,"VF-GL");
	
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
		$q = "SELECT * FROM ".$test_table_name." WHERE ".$this_test_properties['patient_key']." = '".$patient_id."' AND formId = '".$form_id."' AND purged='0' AND del_status='0'";
		$res = imw_query($q);
		$row = imw_fetch_assoc($res);
	}else if(isset($tId) && !empty($tId)){//Get record based on patient id and test id
			$q = "SELECT * FROM ".$test_table_name." WHERE ".$this_test_properties['patient_key']." = '".$patient_id."' AND ".$this_test_properties['test_table_pk_id']." = '".$tId."'";
			$res = imw_query($q);
			$row = imw_fetch_assoc($res);
	}else{
		$row = false; // open new test for patient
	}

	if($row == false){	//&& ($finalize_flag == 0)
		//SET MODE
		$vf_mode = "new";
		$test_edid = "0";
		$elem_vfEye = "OU";
		$elem_gla_mac="Glaucoma: 24-2";
	}else{
		$vf_mode = "update";
		$test_edid = $row["vf_gl_id"];
	}
	
	//Default
	if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1))
	{
		//New Records
		$dt= (empty($dt) || ($dt == "0000-00-00")) ? date("Y-m-d") : $dt;
		$qry = "SELECT ".$sel." FROM vf_gl ".
				"WHERE patientId = '".$patient_id."' ".
				"AND examDate < '".$dt."' ".
				"AND purged='0' AND del_status='0' ".
				"ORDER BY examDate DESC, vf_gl_id DESC ";
		$res = imw_query($qry);
		$rew = imw_fetch_assoc($res);
		return $row;
	}
	
	
	if($row != false){
		$vf_pkid = $row["vf_gl_id"];
		$test_form_id = $row["formId"];
		$elem_examDate = ($vf_mode != "new") ? get_date_format($row["examDate"]) : $elem_examDate ;
		$elem_examTime = ($vf_mode != "new") ? $row["examTime"] : $elem_examTime ;
		$elem_testTime=   $row["testTime"];
		$elem_vf = $row["vf_gl"];
		$elem_vfSel = $row["vf_gl_sel"];
		$elem_vfSelOther = $row["vf_gl_sel_other"];
		$elem_vfEye = ($vf_mode != "new") ? $row["vf_gl_eye"] : "OU";
		$elem_performedBy = ($vf_mode != "new") ? $row["performedBy"] : "";
		$elem_ptUnderstanding = $row["ptUnderstanding"];	
		$elem_diagnosis_od = trim($row["diagnosis"]);	
		$elem_diagnosisOtherOd = $row["diagnosisOther"];
		
		$elem_diagnosis_os = $row["diagnosis_os"];
		$elem_diagnosisOtherOs = $row["diagnosisOther_os"];
		
		$elem_reliabilityOd = $row["reliabilityOd"];
		$elem_reliabilityOs = $row["reliabilityOs"];
		$elem_vfOd = $row["vf_gl_Od"];
		$elem_descOd = $row["descOd"];
		$elem_vfOs = $row["vf_gl_Os"];
		$elem_descOs = $row["descOs"];
		$elem_stable = $row["stable"];
		$elem_monitorIOP = $row["monitorIOP"];
		$elem_fuApa = $row["fuApa"];
		$elem_ptInformed = $row["ptInformed"];
		$elem_comments = stripslashes($row["comments"]);
		$ar_elem_comments = explode("!~!",$elem_comments);
		$elem_comments_od = $ar_elem_comments[0];
		$elem_comments_os = $ar_elem_comments[1];	
		$elem_vfSign = $row["signature"];
		$elem_physician = ( $vf_mode == "update" ) ? $row["phyName"] : "" ;	
		$elem_tech2InformPt = $row["tech2InformPt"];	
	
		$elem_techComments = stripslashes($row["techComments"]);
		$elem_informedPtNv = $row["ptInformedNv"];
	
		$elem_contiMeds = $row["contiMeds"];
		$elem_rptTst1yr = $row["rptTst1yr"];
	
		$encounterId = $row["encounter_id"];
		
		$elem_gla_mac_od = $row["elem_gla_mac"];
		$elem_gla_macOtherOd = $row["gla_mac_other_od"];
		
		$elem_gla_mac_os = $row["gla_mac_os"];
		$elem_gla_macOtherOs = $row["gla_mac_other_os"];	
	
		$elem_opidTestOrdered = $row["ordrby"];
		if(($row["ordrdt"] != "" && $row["ordrdt"] != "0000-00-00")){
			$elem_opidTestOrderedDate = get_date_format($row["ordrdt"]);
		}
		$purged=$row["purged"];	
		$sign_path = $row["sign_path"];
		$sign_path_date_time = $row["sign_path_date_time"];
		$sign_path_date = $sign_path_time = "";
		if($sign_path && $sign_path_date_time!="0000-00-00 00:00:00" || $sign_path_date_time!=0) {
			$sign_path_date = date("".phpDateFormat()."",strtotime($sign_path_date_time));
			$sign_path_time = date("h:i A",strtotime($sign_path_date_time));
		}		
		
		//od
		$details_high_od=$row["details_high_od"];
		$poor_study_od=$row["poor_study_od"];
		$intratest_fluctuation_od=$row["intratest_fluctuation_od"];
		$artifact_od=$row["artifact_od"];
		$details_lids_od=$row["details_lids_od"];
		$normal_od=$row["normal_od"];
		$nonspecific_od=$row["nonspecific_od"];
		$nasal_step_od=$row["nasal_step_od"];
		$arcuate_od=$row["arcuate_od"];
		$hemifield_od=$row["hemifield_od"];
		$paracentral_od=$row["paracentral_od"];
		$into_fixation_od=$row["into_fixation_od"];
		$central_island_od=$row["central_island_od"];
		$enlarged_blind_spot_od=$row["enlarged_blind_spot_od"];
		$cecocentral_scotone_od=$row["cecocentral_scotone_od"];
		$central_scotoma_od=$row["central_scotoma_od"];
		$hemianopsia_od=$row["hemianopsia_od"];
		$quadrantanopsia_od=$row["quadrantanopsia_od"];
		$congruent_od=$row["congruent_od"];
		$incongruent_od=$row["incongruent_od"];
		$synthesis_od=$row["synthesis_od"];
		$elem_poorStudyOd_desc=$row["poor_study_od_desc"];
		$elem_poorStudyOd_descOther=$row["poor_study_od_desc_other"];
		$elem_mdOd=$row["mdOd"];
		$elem_psdOd=$row["psdOd"];
		$elem_vfiOd=$row["vfiOd"];
		$homonomous_od=$row["homonomous_od"];
		
		//os
		$details_high_os=$row["details_high_os"];
		$poor_study_os=$row["poor_study_os"];
		$intratest_fluctuation_os=$row["intratest_fluctuation_os"];
		$artifact_os=$row["artifact_os"];
		$details_lids_os=$row["details_lids_os"];
		$normal_os=$row["normal_os"];
		$nonspecific_os=$row["nonspecific_os"];
		$nasal_step_os=$row["nasal_step_os"];
		$arcuate_os=$row["arcuate_os"];
		$hemifield_os=$row["hemifield_os"];
		$paracentral_os=$row["paracentral_os"];
		$into_fixation_os=$row["into_fixation_os"];
		$central_island_os=$row["central_island_os"];
		$enlarged_blind_spot_os=$row["enlarged_blind_spot_os"];
		$cecocentral_scotone_os=$row["cecocentral_scotone_os"];
		$central_scotoma_os=$row["central_scotoma_os"];
		$hemianopsia_os=$row["hemianopsia_os"];
		$quadrantanopsia_os=$row["quadrantanopsia_os"];
		$congruent_os=$row["congruent_os"];
		$incongruent_os=$row["incongruent_os"];
		$synthesis_os=$row["synthesis_os"];
		$elem_poorStudyOs_desc=$row["poor_study_os_desc"];
		$elem_poorStudyOs_descOther=$row["poor_study_os_desc_other"];
		$elem_mdOs=$row["mdOs"];
		$elem_psdOs=$row["psdOs"];
		$elem_vfiOs=$row["vfiOs"];
		$homonomous_os=$row["homonomous_os"];
		
		$improve=$row["improve"];
		$worse=$row["worse"];
		
		$elem_interpretation_OD = $row["interpretation_OD"];
		$elem_interpretation_OS = $row["interpretation_OS"];
		$elem_comments_interp = $row["comments_interp"];
		$elem_glaucoma_stage_opt_OD = $row["glaucoma_stage_opt_OD"];
		$elem_glaucoma_stage_opt_OS = $row["glaucoma_stage_opt_OS"];
		$elem_plan = $row["plan"];
		//$elem_repeatTestNxtVstEye = $row["repeatTestNxtVstEye"];
		$elem_repeatTestVal1 = $row["repeatTestVal1"];
		$elem_repeatTestVal2 = $row["repeatTestVal2"];
		$elem_repeatTestEye = $row["repeatTestEye"];
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
	$sb_testName = 'VF_GL';
	
	//Cpt Code Desc
	$thisCptDescSym = "VF - GL";
	
	//Prev + Next Records --
	$tmp = getDateFormatDB($elem_examDate);//Exam Date
	$tstPrevId = $oChartTestPrev->getPrevId($tmp,$vf_pkid);//getPervId
	$tstNxtId = $oChartTestPrev->getNxtId($tmp,$vf_pkid);//getNextId
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
var arrTypeAhead 	= new Array(<?php echo $strTypeAhead;?>); //get TH
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
			
			//
			if(typeof(window.opener.top.fmain.$) != 'undefined' && window.opener.top.fmain.$("#dv_vf_gl").length>0 && typeof(window.opener.top.fmain.loadExamsSummary)!="undefined"){window.opener.top.fmain.loadExamsSummary("vf_oct_gl");	}			
		}
		setTimeout(function(){window.close();},1500);
		<?php }?>
    });
<?php }?>

$(document).ready(function(e) {
  	$("textarea").bind("focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){cn_ta1_no_assess(this, "");};});
	$(".testtbl :input[type=checkbox],.testtbl :input[type=radio], :input[type=checkbox][id*='elem_glst_']").bind("click", function(){	mkSynthesis(); });
	$(".testtbl select, .testtbl input[type=text], #elem_comments_od, #elem_comments_os, :input[name*=elem_gla_mac]").bind("change", function(){	mkSynthesis();	});	
	$("#td_progrsn :input").bind("click", function(){ mkSynthesis(); });
	$(".pat_cop :input[type=radio]").bind("click", function(){ mkSynthesis(); });
	top.frm_submited=0;
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
	
	function printTest()
	{
		var tId = "<?php echo $_GET["tId"];?>";
		window.open('../../library/html_to_pdf/createPdf.php?op=l&onePage=false&file_location=<?php echo $html_file_name;?>&mergePDF=1&name=test_'+tId+'_pdf&testIds='+tId+'&saveOption=F&image_from=<?php echo $testname; ?>','vf_Rpt','menubar=0,resizable=yes');	
	}
	
	
	// Prev Data
	var oPrvDt;
	function getPrevTestDt(ctid,cdt,ctm,dir){
		getPrevTestDtExe(ctid,cdt,ctm,dir,"VF-GL");
	}

	function saveVF(){
		if(top.frm_submited==1){ return; }
		top.frm_submited=1;
		<?php if($elem_per_vo == "1"){echo "return;";}?>
		var f = document.test_form_frm;
		var m = "<b>Please fill the following:- </b><br>";
		var err = false;
		if((f.elem_performedBy.value == "") || ( f.elem_performedBy.value == "0" )){
			m += "&bull; Performed By (Physician or Technician only)<br>"; //"  -Physician Name\n";
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
		if(err == false){
			if(typeof(opener) == "undefined" && typeof(top.show_loading_image)!="undefined"){  top.show_loading_image("show");}
			f.submit();
		}else{
			top.fAlert(m,'','top.frm_submited=0;');
		}
	}
<?php }?>

// calendar
function insertTime(obj){
	var id = obj.name.replace(/finding/g, "timeStamp");
	var tobj = gebi(id);
	if(tobj) {
		tobj.value = currenttime();
		if(typeof(tobj.onblur) == "function"){	tobj.onblur();	}			
	}
}

function showGlStOpt(op,eye){	
	if(op==1){		
		$("#elem_glst_unspecified_"+eye)[0].checked=false;
		return;
	}
	if($("#elem_glaucoma_stage").attr("checked")){
		$("#dv_glcma_stage_OD").css("display","inline-block");
		$("#elem_glst_unspecified_"+eye)[0].checked=true;
		
	}else{$("#dv_glcma_stage_OD").css("display","none");}
}

function selectOs(){
	var elements = document.test_form_frm.elements;
	var eleLen = elements.length;
	for(var i=0;i<eleLen;i++){
		if(elements[i].type == "checkbox"){
			var eleName = elements[i].name;
			if((eleName.indexOf('od')!= -1) || (eleName.indexOf('Od')!= -1) ){				
				var chkStatus = elements[i].checked;
				eleName = (eleName.indexOf('od')!= -1) ? eleName.replace(/od/, "os")  : eleName.replace(/Od/, "Os") ;
				var eleOs = $("input[type=checkbox][name="+eleName+"]")[0];
				if(eleOs){					
					eleOs.checked = chkStatus;
				}
			}
		}else if(elements[i].type == "radio"){
			var eleName = elements[i].id;
			if((eleName.indexOf('od')!= -1) || (eleName.indexOf('Od')!= -1) ){				
				var chkStatus = elements[i].checked;
				eleName = (eleName.indexOf('od')!= -1) ? eleName.replace(/od/, "os")  : eleName.replace(/Od/, "Os") ;
				var eleOs = $("input[type=radio][id="+eleName+"]")[0];
				if(eleOs){					
					eleOs.checked = chkStatus;
				}
			}
		}
		
	}
	$(":input[name=elem_synthesisOs]").val($(":input[name=elem_synthesisOd]").val());
	$(":input[name=elem_centralIslandOs]").val($(":input[name=elem_centralIslandOd]").val());	
	
	$(":input[name=elem_mdOs]").val($(":input[name=elem_mdOd]").val());
	$(":input[name=elem_psdOs]").val($(":input[name=elem_psdOd]").val());
	$(":input[name=elem_vfiOs]").val($(":input[name=elem_vfiOd]").val());
	$(":input[name=elem_poorStudyOs_desc]").val($(":input[name=elem_poorStudyOd_desc]").val());
	$(":input[name=elem_poorStudyOs_descOther]").val($(":input[name=elem_poorStudyOd_descOther]").val());
}


//Set Tests and Reliability fields as per interpretation
function setReli_Test(wh){
	var arrReli = new Array("elem_reliabilityOd","elem_reliabilityOs");
	var arrTestOd = new Array("elem_detailOd_hgtFixLoss", "elem_detailOd_hgtFalsePos", "elem_detailOd_hgtFalseNeg", 
						"elem_poorStudyOd", "elem_intraFluctOd", "elem_artifactOd", "elem_detailOd_lid",
						"elem_detailOd_lens_rim", "elem_detailOd_lens_power", "elem_detailOd_cloverleaf",
						"elem_normalFullOd", "elem_nonspecificOd", "elem_nasalStepOd_Sup", "elem_nasalStepOd_Inf",
						"elem_arcuateOd_Sup", "elem_arcuateOd_Inf", "elem_hemifieldOd_Sup", "elem_hemifieldOd_Inf",
						"elem_paracentralOd_Sup", "elem_paracentralOd_Inf", "elem_intoFixOd_Sup", "elem_intoFixOd_Inf",						
						"elem_centralIslandOd" ,	"elem_enlargeBlindSpotOd" ,"elem_cecoScotoneOd" ,	"elem_cenScotomaOd" ,						
						"elem_synthesisOd",
						"elem_diagnosis_od", "elem_diagnosisOtherOd",
						"elem_mdOd", "elem_psdOd", "elem_vfiOd",
						"elem_poorStudyOd_desc", "elem_poorStudyOd_descOther",
						"elem_hemianopsiaOd_Right", "elem_hemianopsiaOd_Left", 
						"elem_hemianopsiaOd_Binasal", "elem_hemianopsiaOd_Bitemporal",
						"elem_QuadrantanopsiaOd_RightSup", "elem_QuadrantanopsiaOd_LeftSup",
						"elem_QuadrantanopsiaOd_RightInf", "elem_QuadrantanopsiaOd_LeftInf",
						"elem_HomonomousOd_Congruent", "elem_HomonomousOd_Incongruent"
						);
	setReli_Exe(wh,arrTestOd,arrReli);
}

function checkDiagnosis(val,wh){
	var str = (typeof(wh) != "undefined") ? "gla_mac" : "diagnosis" ;
	var ar = val.id.indexOf("Od")!=-1 || val.id.indexOf("od")!=-1 ? ["_od","Od"] : ["_os","Os"];	
	var osel = document.getElementById("elem_"+str+ar[0]);
	var otd = document.getElementById("td_"+str+"Other"+ar[1]);
	
	//alert(osel.name +" - "+ otd.id);
	
	if(val.id.indexOf("img") == -1 && osel.value == "Other"){		
		osel.style.display = "none";
		otd.style.display = "inline-block";
	}else{
		osel.style.display = "inline-block";
		otd.style.display = "none";
	}
	if(val.value!=""){
		if(val.id == "elem_gla_mac_od"){
			$("#elem_gla_mac_os").val(val.value);
			checkDiagnosis(document.getElementById("elem_gla_mac_os"),2);
		}
		
		if(val.id == "elem_diagnosis_od" && val.value!="Other")
		$("#elem_diagnosis_os").val(val.value)
		
	}
}

function checkPoorStudy(oval){
	//document.getElementById("td_diagnosisOther").style.visibility = (obj.value == "Other") ? "visible" : "hidden";
	var wh="",flgclose="";
	if(oval=="closeod"){
		wh = "Od_";	flgclose="1";		
	}else if(oval=="closeos"){
		wh = "Os_";flgclose="1";		
	}else{
		wh = oval.name.indexOf("Od_")!=-1 ? "Od_" : "Os_";	
	}
	var osel = document.getElementById("elem_poorStudy"+wh+"desc");
	var otd = document.getElementById("td_poorStudy"+wh+"descOther");
	if(flgclose=="" && oval.value == "Other"){
		osel.style.display = "none";
		otd.style.display = "inline-block";
	}else{
		osel.style.display = "inline-block";
		otd.style.display = "none";		
		$(otd).children("input").val("");
	}
}

//Synthesis--
function sortSynthesis(tmp){
	RStr = DStr = PStr = PCStr = "";
	RDotInd = DDotInd = PDotInd = PCDotInd =0;
	
	RInd = tmp.indexOf("R -"); 
	if(RInd!=-1){
		RDotInd = tmp.indexOf(".",RInd);
		RStr = tmp.substring(RInd,RDotInd)+".\n";
		RDotInd += 1;
	}
	
	PInd = tmp.indexOf("P -"); 
	if(PInd!=-1){
		PDotInd = tmp.indexOf(".",PInd);
		PStr = tmp.substring(PInd,PDotInd)+".\n";
	}
	
	DInd = tmp.indexOf("D -"); 
	if(DInd!=-1){
		DDotInd = tmp.indexOf(".",DInd);
		DStr = tmp.substring(DInd,DDotInd)+".\n";
	}
	
	PCInd = tmp.indexOf("PC -"); 
	if(PCInd!=-1){
		PCDotInd = tmp.indexOf(".",PCInd);
		PCStr = tmp.substring(PCInd,PCDotInd)+".\n";
	}
	tmp = RStr+DStr+PStr+PCStr;
	return tmp;
}

String.prototype.trimSpace = function() {
	return this.replace(/^\s+|\s+$/g,"");
}
String.prototype.trimSlash = function() {
	return this.replace(/^\/+|\/+$/g,"");
}
/*String.prototype.trimRComma = function() {
	return this.replace(/^\,+|\,+$/g,"");
}*/
String.prototype.trimRComma = function() {
	return this.replace(/^\,+|\,+$/g,"");
}
String.prototype.trimRDot = function() {
	return this.replace(/^\.+|\.+$/g,"");
}

String.prototype.trimMultiDot = function() {
	return this.replace(/\.{2,}/g,".");
}
String.prototype.trimHypen = function() {
	return this.replace(/^\-/g,"");
}

function mkSynthesis(){	
	
	var ar_eye=["od", "os"];	
	for(var x in ar_eye){
	var eye = ar_eye[x];
	eye1=(eye=="od")?"Od":"Os";
	eye2=(eye=="od")?"OD":"OS";
	
	
	var line=[];
	var t="";
	//Line 1: 1,2,10
	line[1] ="";
	
	t=$("#elem_gla_mac_"+eye).val();	
	if(t=="Other"){ t=$("#elem_gla_macOther"+eye1).val();}	
	if(typeof(t)!="undefined" && t!=""){line[1] =t;}
	
	//t=$("#elem_gla_mac_os").val();
	//if(t=="Other"){ t=$("#elem_gla_macOtherOs").val();  }
	//if(typeof(t)!="undefined" && t!=""){line[1] +=" "+t;}
	
	t=$("#elem_md"+eye1).val();
	if(typeof(t)!="undefined" && t!=""){line[1] +=" MD: "+t+" dB";}
	
	t=$("#elem_vfi"+eye1).val();
	if(typeof(t)!="undefined" && t!=""){line[1] +=" VFI: "+t+" dB";}
	
	t="";
	$(":checked[type=checkbox][id*=elem_glst_][id*='_"+eye2+"']").each(function(){ if(this.checked){t+=this.value+", ";}  });
	if(typeof(t)!="undefined" && t!=""){line[1] +=" Stage: "+t+" ";}
	
	
	//Line 2: 3,4,5
	line[2]="";
	t=$(":checked[id*=elem_reliability"+eye1+"]").val();
	if(typeof(t)!="undefined" && t!=""){line[2] +=" Reliab: "+t+" ";}
	t="";
	$("#elem_detail"+eye1+"_hgtFixLoss, #elem_detail"+eye1+"_hgtFalsePos, #elem_detail"+eye1+"_hgtFalseNeg").each(function(){ if(this.checked){  if(this.id.indexOf("Loss")!=-1){ t+="Hi FL"; } if(this.id.indexOf("Pos")!=-1){ if(t!=""){ t+=", "; }  t+="Hi FP"; } if(this.id.indexOf("Neg")!=-1){ if(t!=""){ t+=", "; } t+="Hi FN"; }   } });
	if(typeof(t)!="undefined" && t!=""){line[2] +=" "+t+"";}
	t="";
	$("#elem_intraFluct"+eye1+", #elem_artifact"+eye1+", #elem_detail"+eye1+"_lid, #elem_detail"+eye1+"_lens_rim, #elem_detail"+eye1+"_lens_power, #elem_detail"+eye1+"_cloverleaf").each(function(){  
			if(this.checked){	if(this.id.indexOf("elem_intraFluct")!=-1){ t+="Intratest Fluct "; }else if(this.id.indexOf("elem_artifact")!=-1){ t+="Artifact: "; }else{  t+=this.value+" "; } 	}	});
	if(typeof(t)!="undefined" && t!=""){line[2] +=" "+t+""; }	
	
	//Line 3: 6,7
	var t="", t1="", t2="", t3="", t4="";
	line[3]="";
	$("#elem_normalFull"+eye1+", #elem_nonspecific"+eye1+", "+
		"#elem_nasalStep"+eye1+"_Sup, #elem_nasalStep"+eye1+"_Inf,"+
		"#elem_arcuate"+eye1+"_Sup, #elem_arcuate"+eye1+"_Inf,"+
		"#elem_hemifield"+eye1+"_Sup, #elem_hemifield"+eye1+"_Inf,"+
		"#elem_paracentral"+eye1+"_Sup, #elem_paracentral"+eye1+"_Inf,"+
		"#elem_intoFix"+eye1+"_Sup, #elem_intoFix"+eye1+"_Inf,"+
		"#elem_centralIsland"+eye1+","+
		"#elem_enlargeBlindSpot"+eye1+","+
		"#elem_cecoScotone"+eye1+","+
		"#elem_cenScotoma"+eye1+"").each(function(){ 
			
			var tmp="";
			if(this.id.indexOf("centralIsland")!=-1 && this.value!=""){
					tmp=this.value;					
					//t3+="central isl remaining "+tmp+" degrees ";
					t4+="central isl remaining "+tmp+" degrees";	
			}else if(this.checked){
				if(this.id.indexOf("elem_normalFull")!=-1){
					t+="Interpret: Normal/Full; ";
				}else if(this.id.indexOf("elem_nonspecific")!=-1){
					t+="Nonspecific: "; 
				}else if(this.id.indexOf("nasalStep")!=-1 || this.id.indexOf("arcuate")!=-1 || this.id.indexOf("hemifield")!=-1 || this.id.indexOf("paracentral")!=-1 || this.id.indexOf("intoFix")!=-1){ 
					
					if(this.id.indexOf("nasalStep")!=-1){
						tmp="NS";
					}else	if(this.id.indexOf("arcuate")!=-1){
						tmp="Arc";
					}else	if(this.id.indexOf("hemifield")!=-1){
						tmp="Hemi";
					}else	if(this.id.indexOf("paracentral")!=-1){
						tmp="Paracentral";
					}else	if(this.id.indexOf("intoFix")!=-1){					
						tmp="into Fix";
					}			
				
					if(this.id.indexOf("_Sup")!=-1){
					t1+=tmp+" ";
					}
					if(this.id.indexOf("_Inf")!=-1){
					t2+=tmp+" ";
					}
				}else if(this.id.indexOf("enlargeBlindSpot")!=-1){
					t3+="Enl BS ";
				}else if(this.id.indexOf("cecoScotone")!=-1){
					t3+="cecocentral ";
				}else if(this.id.indexOf("cenScotoma")!=-1){
					t3+="central scotoma ";
				}
			}			
			
		});
	
		if(t1!=""){ t1="Sup "+t1+"; "; t+=t1; }
		if(t2!=""){ t2="Inf "+t2+"; "; t+=t2; }
		if(t3!=""){ t3="Inf "+t3+"; "; t+=t3; }
		if(t4!=""){ t4=""+t4+"; "; t+=t4; }
		//
		var eye1c="Od";
		tmp="";
		$("#elem_hemianopsia"+eye1c+"_Right, #elem_hemianopsia"+eye1c+"_Left, #elem_hemianopsia"+eye1c+"_Bitemporal").each(function(){ if(this.checked){ tmp+=this.value+" ";  }  });
		if(tmp!=""){ tmp=" Hemianopsis: "+tmp+";"; t+=tmp+" "; }
		tmp="";
		$("#elem_Quadrantanopsia"+eye1c+"_RightSup, #elem_Quadrantanopsia"+eye1c+"_LeftSup, #elem_Quadrantanopsia"+eye1c+"_RightInf, #elem_Quadrantanopsia"+eye1c+"_LeftInf").each(function(){ if(this.checked){ tmp+=this.value+" ";  } });
		if(tmp!=""){ tmp=" Quadranopsia: "+tmp+";"; t+=tmp+" ";  }
		tmp="";
		$("#elem_Homonomous"+eye1c+"_Congruent, #elem_Homonomous"+eye1c+"_Incongruent").each(function(){ if(this.checked){ tmp+=this.value+" ";  } });
		if(tmp!=""){ tmp=" Congruity: "+tmp+";"; t+=tmp+" "; }
	if(typeof(t)!="undefined" && t!=""){line[3] +=" "+t+"";}
	
	//Line 4: 8,9
	t="";
	line[4]="";
	$(":input[name=elem_stable_"+eye2+"], :input[name=elem_improve_"+eye2+"], :input[name=elem_worse_"+eye2+"], :input[name=elem_likeProgrsn_"+eye2+"], :input[name=elem_possibleProgrsn_"+eye2+"]").each(function(){ if(this.checked){ t+=this.value+" ";  } });
	if(typeof(t)!="undefined" && t!=""){line[4] +=" "+t+"";}	
	t="";t=$("#elem_comments_"+eye).val();
	if(typeof(t)!="undefined" && t!="" && t!="Comments"){line[4] +="Comments: "+t+"";}
	
	//
	var snts="";
	for(var z in line){
		if(line[z]!=""){
			if(snts!=""){  snts+="\n"; }
			snts+=line[z];
		}
	}
	//alert(snts);
	
	$("#elem_synthesis"+eye1).val(snts);
	}
}

function vfgl_addDxcodes(){
	return;
	var arnm = {"elem_glst_unspecified_":"365.70","elem_glst_mild_":"365.71","elem_glst_moderate_":"365.72","elem_glst_severe_":"365.73","elem_glst_intermediate_":"365.74"};
	var strchk="";
	$("input[id*=elem_glst]").each(function(indx){  
		var k = this.name.replace(/OD|OS/, "");
		if(typeof(arnm[k])!="undefined" && arnm[k]!=""){
			var sd="";
			for(var d=1;d<=12;d++){
				
				var ss = $("input[id=elem_dxCode_"+d+"]").val();
				if(typeof(ss)=="undefined"){ ss=""; }
				
				if(ss==""){
					if(sd==""){	sd=d; }
				}else{
					if(ss==arnm[k]){
						sd="-1";
						if(this.checked!=true){//del
							if($(":checked[id*="+k+"]").length<=0){
								$("input[id=elem_dxCode_"+d+"]").val("").each(function(){  /*this.onblur();*/ sb_crt_dx_dropdown(this);  });
							}
						}
					}
				}
			}
			if(this.checked==true){
				if(sd!="" && sd!="-1"){							
					if($("input[id=elem_dxCode_"+sd+"]").length > 0){
						$("input[id=elem_dxCode_"+sd+"]").val(arnm[k]).each(function(){  /*this.onblur();*/ sb_crt_dx_dropdown(this);  });
					}
				}
			}
		}
	});	
}
</script>
</head>
<body>
<form name="test_form_frm" action="save_tests.php" method="post" style="margin:0px;">
<?php if($callFromInterface != 'admin'){?>
    <input type="hidden" name="elem_saveForm" id="elem_saveForm" value="VF-GL">
    <input type="hidden" name="elem_patientId" id="elem_patientId" value="<?php echo $patient_id;?>">
    <input type="hidden" name="elem_formId" id="elem_formId" value="<?php echo $form_id;?>">
    <input type="hidden" name="elem_tests_name_id" id="elem_tests_name_id" value="<?php echo $test_master_id;?>">
    <input type="hidden" id="elem_edMode" name="hd_vf_mode" value="<?php echo $vf_mode;?>">
    <input type="hidden" id="elem_testId" name="elem_vfId" value="<?php echo $test_edid;?>">
    <input type="hidden" name="wind_opn" id="wind_opn" value="0">
    <input type="hidden" name="elem_operatorId" value="<?php echo $elem_operatorId;?>">
    <input type="hidden" name="elem_operatorName" value="<?php echo $elem_operatorName;?>">
    <input type="hidden" name="elem_noP" value="<?php echo $noP;?>">
    <input type="hidden" name="elem_examTime" value="<?php echo $elem_examTime;?>">
    <input type="hidden" name="pop" value="<?php echo $_REQUEST['pop']; ?>">
    <!--the hidden field doNotShowRightSide	is used for test maneger-->
    <input type="hidden" name="doNotShowRightSide" value="<?php echo $_REQUEST['doNotShowRightSide']; ?>">
    <input type="hidden" name="hidFormLoaded" id="hidFormLoaded" value="0">
    <!--<input type="hidden" name="todayRec" id="todayRec" value="<?php //echo $strTestDates; ?>">-->
    <input type="hidden" name="zeissAction" id="zeissAction" value="">
    <input type="hidden" name="elem_phyName_order" id="elem_phyName_order" value="<?php echo $elem_phyName_order; ?>" data-phynm="<?php echo (!empty($elem_phyName_order)) ? $objTests->getPersonnal3($elem_phyName_order) : "" ; ?>" >
    <?php $flg_interpreted_btn = (!empty($elem_phyName_order) ) ? 1 : 0; ?>
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
                            <div class="form-group col-sm-6 mt5">
                                <span>OD</span>
                                <select name="elem_gla_mac_od" id="elem_gla_mac_od" class="form-control minimal"  onchange="checkDiagnosis(this,2);" style="width:120px;display:<?php echo ($elem_gla_mac_od != "Other") ? "inline-block" : "none"; ?>;">
                                    <option value="">- Select -</option>
                                    <?php
                                        $arrVfTestOpts=array("SITA Std  24-2","SITA Std  10-2",
                                                        "SITA Std  30-2", "SITA-Fast 24.2", "SWAP-SITA",
                                                        "10-2 Red", "G Top WW", "M Top WW", "HVF 60-4", "FDT N-30",
                                                        "FDT C-20", "Ptosis @ 10 Vectors", "Tangent Screen", "Other");
                                    
                                    /*
                                    if(isset($GLOBALS["vfTestOpts"])&&count($GLOBALS["vfTestOpts"])>0){
                                        $arrVfTestOpts=array_merge($GLOBALS["vfTestOpts"],$arrVfTestOpts);
                                    }*/
    
                                    foreach($arrVfTestOpts as $key=>$val){
                                        if($elem_gla_mac_od==$val) { 
                                            $sel = "selected"; 
                                            $elem_gla_mac_od_print = $val; // FOR PRINT
                                            } else { $sel = ""; };
                                        echo "<option value=\"".$val."\" ".$sel.">".$val."</option>";	
                                    }
                                    
                                    ?>
    
                                  </select>
                                  <div id="td_gla_macOtherOd" style="display:<?php echo ($elem_gla_mac_od == "Other") ? "inline-block" : "none" ;?>;">
                                      <div class="col-sm-10"><input type="text" id="elem_gla_macOtherOd" name="elem_gla_macOtherOd" value="<?php echo ($elem_gla_macOtherOd);?>" class="form-control"></div>
                                      <div class="col-sm-2"><img id="img_gla_macOtherOd" src="<?php echo $library_path;?>/images/close14.png" title="Change" onClick="checkDiagnosis(this,2);" style="cursor:hand; padding:0px;"></div>
                                  </div>
                                  
                            </div>
                            <div class="form-group col-sm-6 mt5">
                                <span>OS</span>
                                <select name="elem_gla_mac_os" id="elem_gla_mac_os" class="form-control minimal" onchange="checkDiagnosis(this,2);" style="width:120px;display:<?php echo ($elem_gla_mac_os != "Other") ? "inline-block" : "none"; ?>;">
                                    <option value="">- Select -</option>
                                    <?php
                                    /*$arrVfTestOpts=array("Glaucoma: 24-2","Glaucoma: 30-2",
                                                        "Macula: 10-2", "Macula: 10-2 Red",
                                                        "60-4", "N-30", "C-20",
                                                        "Tangent Screen");
                                                        //"Neuro","Full Field","Ptosis");
                                    
                                    if(isset($GLOBALS["vfTestOpts"])&&count($GLOBALS["vfTestOpts"])>0){
                                        $arrVfTestOpts=array_merge($GLOBALS["vfTestOpts"],$arrVfTestOpts);
                                    }*/

                                    foreach($arrVfTestOpts as $key=>$val){
                                        if($elem_gla_mac_os==$val) { 
                                            $sel = "selected"; 
                                            $elem_gla_mac_os_print = $val; // FOR PRINT
                                            } else { $sel = ""; };
                                        echo "<option value=\"".$val."\" ".$sel.">".$val."</option>";	
                                    }
                                    
                                    ?>

                                  </select>
                                  <div id="td_gla_macOtherOs" style="display:<?php echo ($elem_gla_mac_od == "Other") ? "inline-block" : "none" ;?>;">
                                      <div class="col-sm-10"><input type="text" id="elem_gla_macOtherOs" name="elem_gla_macOtherOs" value="<?php echo ($elem_gla_macOtherOs);?>" class="form-control"></div>
                                      <div class="col-sm-2"><img id="img_gla_macOtherOs" src="<?php echo $library_path;?>/images/close14.png" title="Change" onClick="checkDiagnosis(this,2);" style="cursor:hand; padding:0px;"></div>
                                  </div>
                            </div>
                        </div>
                        <div class="col-sm-5 siteopt">
                            <div class="tstopt">
                                <div class="row">
                                    <div class="col-sm-3 sitehd">Sites</div>
                                    <div class="col-sm-5 testopt">
                                        <ul>
                                            <li class="ouc"><label><input type="radio" name="elem_vfEye" value="OU"		onclick="setReli_Test(this.value)" <?php echo ($elem_vfEye == "OU") ? "checked" : "" ;?>><span class="drak_purple_color label_txt">OU</span></label></li>
                                            <li class="odc"><label><input type="radio" name="elem_vfEye" value="OD" onClick="setReli_Test(this.value)" <?php echo ($elem_vfEye == "OD") ? "checked" : "" ;?>><span class="blue_color label_txt">OD</span></label></li>
                                            <li class="osc"><label><input type="radio" name="elem_vfEye" value="OS" onClick="setReli_Test(this.value)" <?php echo ($elem_vfEye == "OS") ? "checked" : "" ;?>><span class="green_color label_txt">OS</span></label></li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-4">
                                        <?php
                                            /*Purpose: Add dropdown for procedure codes to be used in Zeiss HL7 message*/
                                            if($callFromInterface != 'admin' && constant("ZEISS_FORUM") == "YES"){
                                                $procedure_opts = $objTests->zeissProcOpts(13);
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
                                        <label for="exampleInputPassword1">Diagnosis OD</label>
                                        <select class="form-control minimal" id="elem_diagnosis_od" name="elem_diagnosis_od" onChange="checkDiagnosis(this)" style="display:<?php echo ($elem_diagnosis_od == "Other") ? "none" : "block" ;?>;">
                                        <option>--Select--</option>
                                        <?php
                                        $arrDigOpts = $objTests->getDiagOpts('1','vfgl',$test_master_id);
										foreach($arrDigOpts  as $key=>$val){
                                            $sel = ($elem_diagnosis_od == $val) ? "SELECTED" : "";
                                            echo "<option value=\"".$val."\" ".$sel.">".$val."</option>";
                                        }
        
                                        ?>
                                        </select>
                                        <div id="td_diagnosisOtherOd" style="display:<?php echo ($elem_diagnosis_od == "Other") ? "inline-block" : "none" ;?>;">
                                            <div class="col-sm-10"><input type="text" name="elem_diagnosisOtherOd" value="<?php echo ($elem_diagnosisOtherOd);?>" class="form-control"></div>
                                            <div class="col-sm-2"><img id="img_diagnosisOtherOd" src="<?php echo $library_path;?>/images/close14.png" title="Change" onClick="checkDiagnosis(this);" style="cursor:hand; padding:0px;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Diagnosis OS</label>
                                        <select class="form-control minimal" id="elem_diagnosis_os" name="elem_diagnosis_os" onChange="checkDiagnosis(this)" style="display:<?php echo ($elem_diagnosis_os == "Other") ? "none" : "block" ;?>;">
                                        <option>--Select--</option>
                                        <?php
                                        $arrDigOpts = $objTests->getDiagOpts('1','vfgl',$test_master_id);
										foreach($arrDigOpts  as $key=>$val){
                                            $sel = ($elem_diagnosis_os == $val) ? "SELECTED" : "";
                                            echo "<option value=\"".$val."\" ".$sel.">".$val."</option>";
                                        }
        
                                        ?>
                                        </select>
                                        <div id="td_diagnosisOtherOs" style="display:<?php echo ($elem_diagnosis_os == "Other") ? "inline-block" : "none" ;?>;">
                                            <div class="col-sm-10"><input type="text" name="elem_diagnosisOtherOs" value="<?php echo ($elem_diagnosisOtherOs);?>" class="form-control"></div>
                                            <div class="col-sm-2"><img id="img_diagnosisOtherOs" src="<?php echo $library_path;?>/images/close14.png" title="Change" onClick="checkDiagnosis(this);" style="cursor:hand; padding:0px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="form-group pdlft10">
                                <textarea class="form-control" rows="2" style="width:100%; height:55px !important;" id="techComment" name="techComments" placeholder="Technician Comments"><?php echo $elem_techComments;?></textarea>
                            </div>
                        </div>	
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="corporat">
                    <div class="pdlft10">
                        <div class="row">
                            <div class="col-sm-<?php if($callFromInterface != 'admin')echo '6';else echo '7';?>">
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
                            <div class="col-sm-2 text-center">
                                <div class="form-inline mt5">
                                <?php if($callFromInterface != 'admin'){?>
                                    <label for="">Pref. Card</label>
                                    <?php echo $objTests->DropDown_Interpretation_Profile($this_test_properties['id']);?>
                                <?php }?>
                                </div>
                            </div>
                            <?php if($callFromInterface != 'admin'){?>
                            <div class="col-sm-2 text-right">
								<?php
									$sql = "SELECT count(*) as num FROM vf_gl WHERE patientId = '$patient_id' AND vf_gl_id!='$test_edid'   ";
									$row = sqlQuery($sql);
									if($row!=false && $row["num"]>0){
										echo '<button class="btn-value" type="button" onmouseover="inPrvVal()" onmouseout="inPrvVal(3)" onclick="inPrvSynthesis(\'VF-GL\',\''.$test_edid.'\')">Prev. Synthesis</button>';
									}
								?>
                            </div>
                            <?php }?>
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
                            <td class="tdlftpan">&nbsp;</td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-1 tdlft">MD</div>
                                    <div class="col-sm-2"><input type="text" id="elem_mdOd" name="elem_mdOd" value="<?php echo $elem_mdOd;?>" class="form-control"></div>
                                    <div class="col-sm-1">dB</div>
                                    <div class="col-sm-1 tdlft">PSD</div>
                                    <div class="col-sm-2"><input type="text" id="elem_psdOd" name="elem_psdOd" value="<?php echo $elem_psdOd;?>" class="form-control"></div>
                                    <div class="col-sm-1">dB</div>
                                    <div class="col-sm-1 tdlft">VFI</div>
                                    <div class="col-sm-2"><input type="text" id="elem_vfiOd" name="elem_vfiOd" value="<?php echo $elem_vfiOd;?>" class="form-control"></div>
                                    <div class="col-sm-1">%</div>
                                </div>
                                <div class="row mt5">
                                	<div class="col-sm-2 tdlft">Details</div>
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-9"><label><input type="checkbox" id="elem_detailOd_hgtFixLoss" name="elem_detailOd_hgtFixLoss" value="High Fixation Loss" <?php if(strpos($details_high_od,"High Fixation Loss")!==false) {  echo "checked"; } ?>><span class="label_txt"> High Fixation Loss</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9"><label><input type="checkbox" id="elem_detailOd_hgtFalsePos" name="elem_detailOd_hgtFalsePos" value="High False Positive" <?php if(strpos($details_high_od,"High False Positive")!==false) {  echo "checked"; } ?>><span class="label_txt">High False Positives</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9"><label><input type="checkbox" id="elem_detailOd_hgtFalseNeg" name="elem_detailOd_hgtFalseNeg" value="High False Negatives" <?php if(strpos($details_high_od,"High False Negatives")!==false) {  echo "checked"; } ?>><span class="label_txt">High False Negatives</span></label></div>
                                    <div class="clearfix"></div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                                <div class="row">
                                	<div class="col-sm-1 tdlft">MD</div>
                                    <div class="col-sm-2"><input type="text" id="elem_mdOs" name="elem_mdOs" value="<?php echo $elem_mdOs;?>" class="form-control"></div>
                                    <div class="col-sm-1">dB</div>
                                    <div class="col-sm-1 tdlft">PSD</div>
                                    <div class="col-sm-2"><input type="text" id="elem_psdOs" name="elem_psdOs" value="<?php echo $elem_psdOs;?>" class="form-control"></div>
                                    <div class="col-sm-1">dB</div>
                                    <div class="col-sm-1 tdlft">VFI</div>
                                    <div class="col-sm-2"><input type="text" id="elem_vfiOs" name="elem_vfiOs" value="<?php echo $elem_vfiOs;?>" class="form-control"></div>
                                    <div class="col-sm-1">%</div>
                                </div>
                                <div class="row mt5">
                                	<div class="col-sm-2 tdlft">Details</div>
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-9"><label><input type="checkbox" id="elem_detailOs_hgtFixLoss" name="elem_detailOs_hgtFixLoss" value="High Fixation Loss" <?php if(strpos($details_high_os,"High Fixation Loss")!==false){echo "checked";}?>><span class="label_txt"> High Fixation Loss</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9"><label><input type="checkbox" id="elem_detailOs_hgtFalsePos" name="elem_detailOs_hgtFalsePos" value="High False Positive" <?php if(strpos($details_high_os,"High False Positive")!==false){echo "checked";}?>><span class="label_txt">High False Positives</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9"><label><input type="checkbox" id="elem_detailOs_hgtFalseNeg" name="elem_detailOs_hgtFalseNeg" value="High False Negatives" <?php if(strpos($details_high_os,"High False Negatives")!==false){echo "checked";}?>><span class="label_txt">High False Negatives</span></label></div>
                                    <div class="clearfix"></div>
                                </div>
                            </td>	
                        </tr>
                        <tr>
                        	<td class="tdlftpan">&nbsp;</td>
                            <td class="pd5 tstbx tstrstopt">
                            	<div class="form-inline">
                                	<div class="form-group"><label><input type="checkbox" id="elem_poorStudyOd" name="elem_poorStudyOd" value="Poor Study" <?php if(strpos($poor_study_od,"Poor Study")!==false) {  echo "checked"; } ?>><span class="label_txt">Poor Study</span></label></div>
                                    <div class="form-group"><?php if(strpos($poor_study_od,"Poor Study")!==false){echo $yes;}else{echo $no;} ?></div>
                                    <div class="form-group"></div>
                                    <div class="form-group">
                                    	<select name="elem_poorStudyOd_desc" id="elem_poorStudyOd_desc" class="form-control minimal" onChange="checkPoorStudy(this)" style="display:<?php echo ($elem_poorStudyOd_desc == "Other") ? "none" : "inline-block"; ?>;">
                                            <option value="" ></option>
                                            <option value="Lens artifact" <?php if ($elem_poorStudyOd_desc=="Lens artifact"){echo "SELECTED";} ?> >Lens artifact</option>
                                            <option value="Ptosis" <?php if ($elem_poorStudyOd_desc=="Ptosis"){echo "SELECTED";} ?> >Ptosis</option>
                                            <option value="Lens Rim" <?php if ($elem_poorStudyOd_desc=="Lens Rim"){echo "SELECTED";} ?> >Lens Rim</option>
                                            <option value="Lens Power" <?php if ($elem_poorStudyOd_desc=="Lens Power"){echo "SELECTED";} ?> >Lens Power</option>
                                            <option value="Cloverleaf" <?php if ($elem_poorStudyOd_desc=="Cloverleaf"){echo "SELECTED";} ?> >Cloverleaf</option>
                                            <option value="Other" <?php if ($elem_poorStudyOd_desc=="Other"){echo "SELECTED";} ?> >Other</option>
                                        </select>
                                        <span  id="td_poorStudyOd_descOther" style="display:<?php echo ($elem_poorStudyOd_desc == "Other") ? "inline-block" : "none"; ?>;">
                                            <input type="text" id="elem_poorStudyOd_descOther" name="elem_poorStudyOd_descOther" value="<?php echo ($elem_poorStudyOd_descOther);?>" class="txt_11" style="width:110px">
                                            &nbsp;<img src="<?php echo $library_path;?>/images/close14.png" title="Change" onClick="checkPoorStudy('closeod');" style="cursor:hand;">
                                        </span>	
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="row mt5">
                                	<div class="col-sm-3"></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_intraFluctOd" name="elem_intraFluctOd" value="Intratest Fluctuation" <?php if(strpos($intratest_fluctuation_od,"Intratest Fluctuation")!==false) {  echo "checked"; } ?> ><span class="label_txt">Intratest Fluctuation</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" id="elem_artifactOd" name="elem_artifactOd" value="Artifact" <?php if(strpos($artifact_od,"Artifact")!==false) {  echo "checked"; } ?> ><span class="label_txt">Artifact</span></label></div>
                                </div>
                                <div class="row mt5">
                                	<div class="col-sm-2 tdlft">Details</div>
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_detailOd_lid" name="elem_detailOd_lid" value="Lid" style="margin-right:0px;" <?php if(strpos($details_lids_od,"Lid")!==false) {  echo "checked"; } ?> ><span class="label_txt">Lid</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_detailOd_lens_rim" name="elem_detailOd_lens_rim" value="Lens Rim" <?php if(strpos($details_lids_od,"Lens Rim")!==false) {  echo "checked"; } ?> ><span class="label_txt">Lens Rim</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_detailOd_lens_power" name="elem_detailOd_lens_power" value="Lens Power" style="margin-right:0px;" <?php if(strpos($details_lids_od,"Lens Power")!==false) {  echo "checked"; } ?> ><span class="label_txt">Lens Power</span></label></div>
									<div class="col-sm-4"><label><input type="checkbox" id="elem_detailOd_cloverleaf" name="elem_detailOd_cloverleaf" value="Cloverleaf" <?php if(strpos($details_lids_od,"Cloverleaf")!==false) {  echo "checked"; } ?> ><span class="label_txt">Cloverleaf</span></label></div>
                                    <div class="clearfix"></div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                            	<div class="form-inline">
                                	<div class="form-group"><label><input type="checkbox" id="elem_poorStudyOs" name="elem_poorStudyOs" value="Poor Study" <?php if(strpos($poor_study_os,"Poor Study")!==false){echo "checked";}?>><span class="label_txt">Poor Study</span></label></div>
                                    <div class="form-group"><?php if(strpos($poor_study_od,"Poor Study")!==false){echo $yes;}else{echo $no;} ?></div>
                                    <div class="form-group"></div>
                                    <div class="form-group">
                                    	<select name="elem_poorStudyOs_desc" id="elem_poorStudyOs_desc" class="form-control minimal" onChange="checkPoorStudy(this)" style="display:<?php echo ($elem_poorStudyOs_desc == "Other") ? "none" : "inline-block"; ?>;">
                                            <option value="" ></option>
                                            <option value="Lens artifact" <?php if ($elem_poorStudyOs_desc=="Lens artifact"){echo "SELECTED";} ?> >Lens artifact</option>
                                            <option value="Ptosis" <?php if ($elem_poorStudyOs_desc=="Ptosis"){echo "SELECTED";} ?> >Ptosis</option>
                                            <option value="Lens Rim" <?php if ($elem_poorStudyOs_desc=="Lens Rim"){echo "SELECTED";} ?> >Lens Rim</option>
                                            <option value="Lens Power" <?php if ($elem_poorStudyOs_desc=="Lens Power"){echo "SELECTED";} ?> >Lens Power</option>
                                            <option value="Cloverleaf" <?php if ($elem_poorStudyOs_desc=="Cloverleaf"){echo "SELECTED";} ?> >Cloverleaf</option>
                                            <option value="Other" <?php if ($elem_poorStudyOs_desc=="Other"){echo "SELECTED";} ?> >Other</option>
                                        </select>
                                        <span id="td_poorStudyOs_descOther" style="display:<?php echo ($elem_poorStudyOs_desc == "Other") ? "inline-block" : "none"; ?>;">
                                            <input type="text" id="elem_poorStudyOs_descOther" name="elem_poorStudyOs_descOther" value="<?php echo ($elem_poorStudyOs_descOther);?>" class="form-control" style="width:110px">
                                            &nbsp;<img src="<?php echo $library_path;?>/images/close14.png" title="Change" onClick="checkPoorStudy('closeos');" style="cursor:hand;">
                                        </span>	
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="row mt5">
                                	<div class="col-sm-3"></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_intraFluctOs" name="elem_intraFluctOs" value="Intratest Fluctuation" <?php if(strpos($intratest_fluctuation_os,"Intratest Fluctuation")!==false) {  echo "checked"; } ?>><span class="label_txt">Intratest Fluctuation</span></label></div>
                                	<div class="col-sm-4"><label><input type="checkbox" id="elem_artifactOs" name="elem_artifactOs" value="Artifact" <?php if(strpos($artifact_os,"Artifact")!==false){echo "checked";}?>><span class="label_txt">Artifact</span></label></div>
                                </div>
                                <div class="row mt5">
                                	<div class="col-sm-2 tdlft">Details</div>
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_detailOs_lid" name="elem_detailOs_lid" value="Lid" style="margin-right:0px;" <?php if(strpos($details_lids_os,"Lid")!==false){echo "checked";}?>><span class="label_txt">Lid</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_detailOs_lens_rim" name="elem_detailOs_lens_rim" value="Lens Rim" <?php if(strpos($details_lids_os,"Lens Rim")!==false){echo "checked";}?>><span class="label_txt">Lens Rim</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_detailOs_lens_power" name="elem_detailOs_lens_power" value="Lens Power" style="margin-right:0px;" <?php if(strpos($details_lids_os,"Lens Power")!==false){echo "checked";}?>><span class="label_txt">Lens Power</span></label></div>
									<div class="col-sm-4"><label><input type="checkbox" id="elem_detailOs_cloverleaf" name="elem_detailOs_cloverleaf" value="Cloverleaf" <?php if(strpos($details_lids_os,"Cloverleaf")!==false) {echo "checked";}?>><span class="label_txt">Cloverleaf</span></label></div>
                                    <div class="clearfix"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="tdlftpan">&nbsp;</td>
                            <td class="pd5 tstbx tstrstopt">
                            	<div class="row">
                                	<div class="col-sm-3"></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_normalFullOd" name="elem_normalFullOd" value="Normal / Full" <?php if(strpos($normal_od,"Normal / Full")!==false){echo "checked";}?> ><span class="label_txt">Normal / Full</span></label></div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                            	<div class="row">
                                	<div class="col-sm-3"></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_normalFullOs" name="elem_normalFullOs" value="Normal / Full" <?php if(strpos($normal_os,"Normal / Full")!==false){echo "checked";}?>><span class="label_txt">Normal / Full</span></label></div>
                                </div>
                            </td>
                    	</tr>
                        <tr>
                            <td class="tdlftpan">&nbsp;</td>
                            <td class="pd5 tstbx tstrstopt">
                            	<div class="row">
                                	<div class="col-sm-3"></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_nonspecificOd" name="elem_nonspecificOd" value="Nonspecific Details"  <?php if(strpos($nonspecific_od,"Nonspecific Details")!==false){echo "checked";}?>><span class="label_txt">Nonspecific Defects</span></label></div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
								<div class="row">
                                	<div class="col-sm-3"></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_nonspecificOs" name="elem_nonspecificOs" value="Nonspecific Details"  <?php if(strpos($nonspecific_os,"Nonspecific Details")!==false){echo "checked";}?>><span class="label_txt">Nonspecific Defects</span></label></div>
                                </div>
                            </td>
                    	</tr>
                        <tr>
                            <td class="tdlftpan">&nbsp;</td>
                            <td class="pd5 tstbx tstrstopt">
                            	<div class="row">
                                	<div class="col-sm-3 tdlft">Nasal Step</div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_nasalStepOd_Sup" name="elem_nasalStepOd_Sup" value="Superior" <?php if(strpos($nasal_step_od,"Superior")!==false) {  echo "checked"; } ?>><span class="label_txt">Superior</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_nasalStepOd_Inf" name="elem_nasalStepOd_Inf" value="Inferior" <?php if(strpos($nasal_step_od,"Inferior")!==false){echo "checked";}?>><span class="label_txt">Inferior</span></label></div>
                                </div>
								<div class="row">
                                	<div class="col-sm-3 tdlft">Arcuate</div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_arcuateOd_Sup" name="elem_arcuateOd_Sup" value="Superior" <?php if(strpos($arcuate_od,"Superior")!==false) {  echo "checked"; } ?>><span class="label_txt">Superior</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_arcuateOd_Inf" name="elem_arcuateOd_Inf" value="Inferior" <?php if(strpos($arcuate_od,"Inferior")!==false) {  echo "checked"; } ?>><span class="label_txt">Inferior</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-3 tdlft">Hemifield</div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_hemifieldOd_Sup" name="elem_hemifieldOd_Sup" value="Superior" <?php if(strpos($hemifield_od,"Superior")!==false){echo "checked";}?>><span class="label_txt">Superior</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_hemifieldOd_Inf" name="elem_hemifieldOd_Inf" value="Inferior" <?php if(strpos($hemifield_od,"Inferior")!==false){echo "checked";}?>><span class="label_txt">Inferior</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-3 tdlft">Paracentral</div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_paracentralOd_Sup" name="elem_paracentralOd_Sup" value="Superior" <?php if(strpos($paracentral_od,"Superior")!==false){echo "checked";}?>><span class="label_txt">Superior</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_paracentralOd_Inf" name="elem_paracentralOd_Inf" value="Inferior" <?php if(strpos($paracentral_od,"Inferior")!==false){echo "checked";}?>><span class="label_txt">Inferior</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-3 tdlft">Into Fixation</div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_intoFixOd_Sup" name="elem_intoFixOd_Sup" value="Superior" <?php if(strpos($into_fixation_od,"Superior")!==false){echo "checked";}?>><span class="label_txt">Superior</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_intoFixOd_Inf" name="elem_intoFixOd_Inf" value="Inferior" <?php if(strpos($into_fixation_od,"Inferior")!==false){echo "checked";}?>><span class="label_txt">Inferior</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-3 tdlft">Central Island</div>
                                    <div class="col-sm-2">Remaining</div>
                                    <div class="col-sm-2"><input type="text" id="elem_centralIslandOd" name="elem_centralIslandOd" value="<?php echo $central_island_od;?>" size="4" class="form-control"></div>
                                    <div class="col-sm-2">degrees</div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
                            	<div class="row">
                                	<div class="col-sm-3 tdlft">Nasal Step</div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_nasalStepOs_Sup" name="elem_nasalStepOs_Sup" value="Superior" <?php if(strpos($nasal_step_os,"Superior")!==false){echo "checked";}?>><span class="label_txt">Superior</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_nasalStepOs_Inf" name="elem_nasalStepOs_Inf" value="Inferior" <?php if(strpos($nasal_step_os,"Inferior")!==false){echo "checked";}?>><span class="label_txt">Inferior</span></label></div>
                                </div>
								<div class="row">
                                	<div class="col-sm-3 tdlft">Arcuate</div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_arcuateOs_Sup" name="elem_arcuateOs_Sup" value="Superior" <?php if(strpos($arcuate_os,"Superior")!==false){echo "checked";}?>><span class="label_txt">Superior</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_arcuateOs_Inf" name="elem_arcuateOs_Inf" value="Inferior" <?php if(strpos($arcuate_os,"Inferior")!==false){echo "checked";}?>><span class="label_txt">Inferior</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-3 tdlft">Hemifield</div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_hemifieldOs_Sup" name="elem_hemifieldOs_Sup" value="Superior" <?php if(strpos($hemifield_os,"Superior")!==false){echo "checked";}?>><span class="label_txt">Superior</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_hemifieldOs_Inf" name="elem_hemifieldOs_Inf" value="Inferior" <?php if(strpos($hemifield_os,"Inferior")!==false){echo "checked";}?>><span class="label_txt">Inferior</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-3 tdlft">Paracentral</div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_paracentralOs_Sup" name="elem_paracentralOs_Sup" value="Superior" <?php if(strpos($paracentral_os,"Superior")!==false){echo "checked";}?>><span class="label_txt">Superior</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_paracentralOs_Inf" name="elem_paracentralOs_Inf" value="Inferior" <?php if(strpos($paracentral_os,"Inferior")!==false){echo "checked";}?>><span class="label_txt">Inferior</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-3 tdlft">Into Fixation</div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_intoFixOs_Sup" name="elem_intoFixOs_Sup" value="Superior" <?php if(strpos($into_fixation_os,"Superior")!==false){echo "checked";}?>><span class="label_txt">Superior</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_intoFixOs_Inf" name="elem_intoFixOs_Inf" value="Inferior" <?php if(strpos($into_fixation_os,"Inferior")!==false){echo "checked";}?>><span class="label_txt">Inferior</span></label></div>
                                </div>
                                <div class="row">
                                	<div class="col-sm-3 tdlft">Central Island</div>
                                    <div class="col-sm-2">Remaining</div>
                                    <div class="col-sm-2"><input type="text" id="elem_centralIslandOs" name="elem_centralIslandOs" value="<?php echo $central_island_os;?>" size="4" class="form-control"></div>
                                    <div class="col-sm-2">degrees</div>
                                </div>
                            </td>
                    	</tr>
                        <tr>
                            <td class="tdlftpan">&nbsp;</td>
                            <td class="pd5 tstbx tstrstopt">
                            	<div class="row">
                                	<div class="col-sm-5"><label><input type="checkbox" id="elem_enlargeBlindSpotOd" name="elem_enlargeBlindSpotOd" value="Enlarged Blind Spot" <?php if(strpos($enlarged_blind_spot_od,"Enlarged Blind Spot")!==false){echo "checked"; }?>><span class="label_txt">Enlarged Blind Spot</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_cecoScotoneOd" name="elem_cecoScotoneOd" value="Cecocentral Scotone" <?php if(strpos($cecocentral_scotone_od,"Cecocentral Scotone")!==false){echo "checked"; }?>><span class="label_txt">Cecocentral Scotoma</span></label></div>
                                	<div class="clearfix"></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_cenScotomaOd" name="elem_cenScotomaOd" value="Central Scotoma" <?php if(strpos($central_scotoma_od,"Central Scotoma")!==false){echo "checked";}?>><span class="label_txt">Central Scotoma</span></label></div>
                                </div>
                            </td>
                            <td class="pd5 tstbx tstrstopt">
	                            <div class="row">
                                	<div class="col-sm-5"><label><input type="checkbox" id="elem_enlargeBlindSpotOs" name="elem_enlargeBlindSpotOs" value="Enlarged Blind Spot" <?php if(strpos($enlarged_blind_spot_os,"Enlarged Blind Spot")!==false){echo "checked";}?>><span class="label_txt">Enlarged Blind Spot</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_cecoScotoneOs" name="elem_cecoScotoneOs" value="Cecocentral Scotone" <?php if(strpos($cecocentral_scotone_os,"Cecocentral Scotone")!==false){echo "checked";}?>><span class="label_txt">Cecocentral Scotoma</span></label></div>
                                	<div class="clearfix"></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_cenScotomaOs" name="elem_cenScotomaOs" value="Central Scotoma" <?php if(strpos($central_scotoma_os,"Central Scotoma")!==false){echo "checked";}?>><span class="label_txt">Central Scotoma</span></label></div>
                                </div>
                            </td>
                    	</tr>
                        <tr>
                        	<td class="tdlftpan">&nbsp;</td>
                        	<td class="tstbx" colspan="3">
                            <div class="row mt5 tstrstopt">
                            	<div class="col-sm-2 tdlft">Hemianopsis</div>
                            	<div class="col-sm-2"><label><input type="checkbox" id="elem_hemianopsiaOd_Right" name="elem_hemianopsiaOd_Right" value="Right" <?php if(strpos($hemianopsia_od,"Right")!==false){echo "checked";}?>><span class="label_txt">Right</span></label></div>
                            	<div class="col-sm-2"><label><input type="checkbox" id="elem_hemianopsiaOd_Left" name="elem_hemianopsiaOd_Left" value="Left" <?php if(strpos($hemianopsia_od,"Left")!==false){echo "checked";}?>><span class="label_txt">Left</span></label></div>
                            	<div class="col-sm-2"><label><input type="checkbox" id="elem_hemianopsiaOd_Bitemporal" name="elem_hemianopsiaOd_Bitemporal" value="Bitemporal" <?php if(strpos($hemianopsia_od,"Bitemporal")!==false){echo "checked";}?>><span class="label_txt">Bitemporal</span></label></div>
                            </div>
                            <div class="row tstrstopt">
                            	<div class="col-sm-2 tdlft">Quadranopsia</div>
                            	<div class="col-sm-2"><label><input type="checkbox" id="elem_QuadrantanopsiaOd_RightSup" name="elem_QuadrantanopsiaOd_RightSup" value="Right Superior" <?php if(strpos($quadrantanopsia_od,"Right Superior")!==false){echo "checked";}?>><span class="label_txt">Right Superior</span></label></div>
                            	<div class="col-sm-2"><label><input type="checkbox" id="elem_QuadrantanopsiaOd_LeftSup" name="elem_QuadrantanopsiaOd_LeftSup" value="Left Superior" <?php if(strpos($quadrantanopsia_od,"Left Superior")!==false){echo "checked";} ?>><span class="label_txt">Left Superior</span></label></div>
                            	<div class="col-sm-2"><label><input type="checkbox" id="elem_QuadrantanopsiaOd_RightInf" name="elem_QuadrantanopsiaOd_RightInf" value="Right Inferior" <?php if(strpos($quadrantanopsia_od,"Right Inferior")!==false){echo "checked";}?>><span class="label_txt">Right Inferior</span></label></div>
                            	<div class="col-sm-2"><label><input type="checkbox" id="elem_QuadrantanopsiaOd_LeftInf" name="elem_QuadrantanopsiaOd_LeftInf" value="Left Inferior" <?php if(strpos($quadrantanopsia_od,"Left Inferior")!==false){echo "checked";}?>><span class="label_txt">Left Inferior</span></label></div>
                            </div>
                            <div class="row tstrstopt">
                            	<div class="col-sm-2 tdlft">Congruity</div>
                            	<div class="col-sm-2"><label><input type="checkbox" id="elem_HomonomousOd_Congruent" name="elem_HomonomousOd_Congruent" value="Congruent" <?php if(strpos($homonomous_od,"Congruent")!==false){echo "checked";}?>><span class="label_txt">Congruent</span></label></div>
                            	<div class="col-sm-2"><label><input type="checkbox" id="elem_HomonomousOd_Incongruent" name="elem_HomonomousOd_Incongruent" value="Incongruent" <?php if(strpos($homonomous_od,"Incongruent")!==false){echo "checked";}?>><span class="label_txt">Incongruent</span></label></div>
                            </div>
                            <div class="row">
                            	<div class="col-sm-6">
                                	<div class="row">
	                                	<div class="col-sm-4 tdlft">Synthesis</div>
    	                        		<div class="col-sm-8"><textarea class="form-control" rows="2" style="width:100%; height:55px !important;" id="elem_synthesisOd" name="elem_synthesisOd"><?php echo $synthesis_od;?></textarea></div>
                                    </div>
                                </div>
                            	<div class="col-sm-6">
                                	<div class="row">
	                                	<div class="col-sm-4 tdlft">Synthesis</div>
    	                        		<div class="col-sm-8"><textarea class="form-control" rows="2" style="width:100%; height:55px !important;" id="elem_synthesisOs" name="elem_synthesisOs"><?php echo $synthesis_os;?></textarea></div>
                                    </div>
                                </div>
                            </div>
                            </td>
                        </tr>
						<tr>
                            <td class="tdlftpan">&nbsp;</td>
                            <td class="tstbx tstrstopt">
                            	<div class="row">
                                	<div class="col-sm-2"><label> <input type="checkbox" name="elem_stable_OD" value="Stable" <?php if(strpos($elem_interpretation_OD,"Stable")!==false) {  echo "checked"; }?>><span class="label_txt">Stable</span></label></div>
                                    <div class="col-sm-6"><label><input type="checkbox" name="elem_improve_OD" value="Difficult Interpretation" <?php if(strpos($elem_interpretation_OD,"Not Improve")!==false || strpos($elem_interpretation_OD,"Difficult Interpretation")!==false){echo "checked";}?>><span class="label_txt">Difficult Interpretation</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_worse_OD" value="Worse" <?php if(strpos($elem_interpretation_OD,"Worse")!==false){echo "checked";}?>><span class="label_txt">Worse</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" name="elem_likeProgrsn_OD" value="Likely progression" <?php if(strpos($elem_interpretation_OD,"Likely progression")!==false){echo "checked";}?>><span class="label_txt">Likely progression</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" name="elem_possibleProgrsn_OD" value="Possible progression" <?php if(strpos($elem_interpretation_OD,"Possible progression")!==false){echo "checked";}?>><span class="label_txt">Possible progression </span></label></div>
                                </div>
                            </td>
                            <td class="tstbx">&nbsp;</td>
                            <td class="pd5 tstbx tstrstopt">
	                            <div class="row">
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_stable_OS" value="Stable" <?php if(strpos($elem_interpretation_OS,"Stable")!==false){echo "checked";}?>><span class="label_txt">Stable</span></label></div>
                                    <div class="col-sm-6"><label><input type="checkbox" name="elem_improve_OS" value="Difficult Interpretation" <?php if(strpos($elem_interpretation_OS,"Not Improve")!==false||strpos($elem_interpretation_OS,"Difficult Interpretation")!==false){echo "checked";}?>><span class="label_txt">Difficult Interpretation</span></label></div>
                                	<div class="clearfix"></div>
                                    <div class="col-sm-2"><label><input type="checkbox" name="elem_worse_OS" value="Worse" <?php if(strpos($elem_interpretation_OS,"Worse")!==false){echo "checked";}?>><span class="label_txt">Worse</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" name="elem_likeProgrsn_OS" value="Likely progression" <?php if(strpos($elem_interpretation_OS,"Likely progression")!==false){echo "checked";}?>><span class="label_txt">Likely progression</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" name="elem_possibleProgrsn_OS" value="Possible progression" <?php if(strpos($elem_interpretation_OS,"Possible progression")!==false) {  echo "checked"; }?>><span class="label_txt">Possible progression </span></label></div>
                                </div>
                            </td>
                    	</tr>
                        <tr>
                            <td class="tstbx">&nbsp;</td>
                        	<td class="pd5 tstbx" colspan="3">
                            	<textarea class="form-control" rows="2" style="width:100%; height:55px !important;" id="elem_comments_interp" name="elem_comments_interp"><?php echo $elem_comments_interp;?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="tdlftpan"><label class="text_12b_purple link_cursor" onClick="staging_info_fun();">Glaucoma Stage</label></td>
                            <td class="tstbx tstrstopt">
                            <?php include_once(dirname(__FILE__).'/glaucoma_staging.php');?>
                            	<div class="row">
                                	<div class="col-sm-7"><label><input type="checkbox" id="elem_glst_mild_OD" name="elem_glst_mild_OD" value="Mild" <?php if(strpos($elem_glaucoma_stage_opt_OD,"Mild")!==false){echo "checked";}?>  onclick="showGlStOpt(1,'OD'); vfgl_addDxcodes();"><span class="label_txt">Mild</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_glst_moderate_OD" name="elem_glst_moderate_OD" value="Moderate" <?php if(strpos($elem_glaucoma_stage_opt_OD,"Moderate")!==false) {  echo "checked"; }?> onclick="showGlStOpt(1,'OD'); vfgl_addDxcodes();" ><span class="label_txt">Moderate</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-7"><label><input type="checkbox" id="elem_glst_severe_OD" name="elem_glst_severe_OD" value="Severe" <?php if(strpos($elem_glaucoma_stage_opt_OD,"Severe")!==false) {  echo "checked"; }?> onclick="showGlStOpt(1,'OD'); vfgl_addDxcodes();"><span class="label_txt">Severe(not mild and moderate)</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_glst_intermediate_OD" name="elem_glst_intermediate_OD" value="Indeterminate" <?php if(strpos($elem_glaucoma_stage_opt_OD,"Indeterminate")!==false) {  echo "checked"; }?>  onclick="showGlStOpt(1,'OD'); vfgl_addDxcodes();"><span class="label_txt">Indeterminate</span></label></div>
                                </div>
                            </td>
                            <td class="tstbx">&nbsp;</td>
                            <td class="pd5 tstbx tstrstopt">
	                            <div class="row">
                                	<div class="col-sm-7"><label><input type="checkbox" id="elem_glst_mild_OS" name="elem_glst_mild_OS" value="Mild" <?php if(strpos($elem_glaucoma_stage_opt_OS,"Mild")!==false){echo "checked";}?> onclick="showGlStOpt(1,'OS');vfgl_addDxcodes();"><span class="label_txt">Mild</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_glst_moderate_OS" name="elem_glst_moderate_OS" value="Moderate" <?php if(strpos($elem_glaucoma_stage_opt_OS,"Moderate")!==false){echo "checked";}?>  onclick="showGlStOpt(1,'OS');vfgl_addDxcodes();"><span class="label_txt">Moderate</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-7"><label><input type="checkbox" id="elem_glst_severe_OS" name="elem_glst_severe_OS" value="Severe" <?php if(strpos($elem_glaucoma_stage_opt_OS,"Severe")!==false){echo "checked";}?> onclick="showGlStOpt(1,'OS');vfgl_addDxcodes();"><span class="label_txt">Severe(not mild and moderate)</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="elem_glst_intermediate_OS" name="elem_glst_intermediate_OS" value="Indeterminate" <?php if(strpos($elem_glaucoma_stage_opt_OS,"Indeterminate")!==false) {  echo "checked"; }?> onclick="showGlStOpt(1,'OS');vfgl_addDxcodes();"><span class="label_txt">Indeterminate</span></label></div>
                                </div>
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
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_ptInformYPhy" value="Pt informed of results by physician today" <?php if(strpos($elem_plan,"Pt informed of results by physician today")!==false){echo "checked";}?>><span class="label_txt">Pt informed of results by physician today</span></label></div>
                                        <div class="col-sm-3"><label><input type="checkbox" name="elem_2bCallYTech" value="to be called by technician" <?php if(strpos($elem_plan,"to be called by technician")!==false){echo "checked"; }?>><span class="label_txt">to be called by technician</span></label></div>
                                        <div class="col-sm-2"><label><input type="checkbox" name="elem_byLetter" value="by letter" <?php if(strpos($elem_plan,"by letter")!==false){echo "checked";}?>><span class="label_txt">by letter</span></label></div>
                                    	<div class="col-sm-3"><label><input type="checkbox" name="elem_willInfrmNextVisit" value="will inform next visit" <?php if(strpos($elem_plan,"will inform next visit")!==false){echo "checked";}?>><span class="label_txt">will inform next visit</span></label></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-2"><label><input type="checkbox" name="elem_tech2InformPt" value="1" <?php echo ($elem_tech2InformPt == "1") ? "checked" : "" ;?>><span class="label_txt">Tech to Inform Pt.</span></label></div>
				<div class="col-sm-2"><label><input type="checkbox" name="elem_contMeds" value="Continue meds" <?php if(strpos($elem_plan,"Continue meds")!==false){echo "checked"; }?>><span class="label_txt">Continue meds</span></label></div>
                                        <div class="col-sm-3"><label><input type="checkbox" name="elem_monitorFind" value="Monitor findings" <?php if(strpos($elem_plan,"Monitor findings")!==false){echo "checked";}?>><span class="label_txt">Monitor findings</span></label></div>
                                        <div class="col-sm-5 form-inline">
                                        	<div class="form-group">
	                                            <label><input type="checkbox" name="elem_repeatTest" value="Repeat test time" <?php if(strpos($elem_plan,"Repeat test time")!==false){echo "checked";}?> ><span class="label_txt">Repeat test </span></label>
                                            </div>
                                        	<div class="form-group">
                                                <select name="elem_repeatTestVal1" class="form-control minimal" style="width:80px;">
                                                    <option value=""></option>
                                                    <option value="Next visit" <?php echo (empty($elem_repeatTestVal1) || $elem_repeatTestVal1 == "Next visit") ? "Selected" : "";?>>Next visit</option>
                                                    <option value="3 mos" <?php echo ($elem_repeatTestVal1 == "3 mos") ? "Selected" : "";?>>3 mos</option>
                                                    <option value="6 mos" <?php echo ($elem_repeatTestVal1 == "6 mos") ? "Selected" : "";?>>6 mos</option>
                                                    <option value="1 year" <?php echo ($elem_repeatTestVal1 == "1 year") ? "Selected" : "";?>>1 year</option>
                                                    <option value="Other" <?php echo ($elem_repeatTestVal1 == "Other") ? "Selected" : "";?>>Other</option>									
                                                </select>
                                            </div>
                                            <div class="form-group">
	                                            <input class="form-control" type="text" name="elem_repeatTestVal2" value="<?php echo ($elem_repeatTestVal2);?>" size="6" >
                                            </div>
                                          	<div class="form-group">
                                                <label><input type="radio" name="elem_repeatTestEye" value="OU" <?php echo ($elem_repeatTestEye == "OU") ? "checked" : "";?>><span class="label_txt">OU</span></label>
                                                <label><input type="radio" name="elem_repeatTestEye" value="OD" <?php echo ($elem_repeatTestEye == "OD") ? "checked" : "";?>><span class="label_txt">OD</span></label>
                                                <label><input type="radio" name="elem_repeatTestEye" value="OS" <?php echo ($elem_repeatTestEye == "OS") ? "checked" : "";?>><span class="label_txt">OS</span></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                    	<div class="col-sm-6">
                                        	<textarea class="form-control" id="elem_comments_od" name="elem_comments_od" rows="2" style="width:100%; height:55px !important;" placeholder="Comments OD"><?php echo (!empty($elem_comments_od))?$elem_comments_od:"";?></textarea>
                                        </div>
                                    	<div class="col-sm-6">
                                        	<textarea class="form-control" id="elem_comments_os" name="elem_comments_os" rows="2" style="width:100%; height:55px !important;" placeholder="Comments OS"><?php echo (!empty($elem_comments_os))?$elem_comments_os:"";?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                
                                <?php if($callFromInterface != 'admin'){
									$sigFolderName = 'test_vf_gl';
									require_once("future_appt_interpret_by.php");
								}?>
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
                				<?php echo $objTests->get_zeiss_forum_button('VF-GL',$test_edid,$patient_id);?>
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
			}?>
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
							<input type="button" class="btn btn-success<?php echo $btnHide;?>" id="btn_done" value="Done"  onClick="saveVF()" />
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
							<input type="button"  class="btn btn-success<?php echo $btnHide;?>" value="Order" id="save" onClick="saveVF()" />
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
						top.btn_show("VF-GL",btnArr);
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

function staging_info_fun(){
	$('#staging_code_info').toggle();
	tp=(topPosition()+10)+'px';
	$('#staging_code_info').css({top:tp});
}

<?php if($callFromInterface != 'admin'){?>
	top.set_header_title('<?php echo $this_test_screen_name;?>');
	<?php if(($elem_vfEye == "OD") || ($elem_vfEye == "OS")){echo "setReli_Test(\"".$elem_vfEye."\");";}?>
<?php }?>
</script>
<?php
if($callFromInterface != 'admin'){
	//Previous Test div--
	$oChartTestPrev->showdiv();
    if($_GET["tId"]!='')	// position of include file cannot go above than these lines
    {
        include 'test_vf_gl_print.php'; 
    }
}
?>
</body>
</html>