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
$callFromInterface		= strtolower(trim(strip_tags($_GET['callFromInterface'])));
$doNotShowRightSide 	= strtolower(trim($_REQUEST['doNotShowRightSide']));
require_once("../../config/globals.php");
if($callFromInterface != 'admin'){
	require_once("../../library/patient_must_loaded.php");
}
require_once("../../library/classes/class.tests.php");
require_once("../../library/classes/SaveFile.php");
require_once("../../library/classes/ChartTestPrev.php");
require_once("../../library/classes/work_view/Epost.php");
$library_path 			= $GLOBALS['webroot'].'/library';
$patient_id				= $_SESSION['patient'];
$objTests				= new Tests;
$testname               = "NFA";
$objEpost				= new Epost($patient_id,$testname);
//$objTests->patient_id 	= $patient_id;

//MAKING OBJECT TO SAVE IMAGE FILES
$oSaveFile = new SaveFile($patient_id);

$test_table_name		= 'nfa';
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
	$html_file_name = $test_name.'/'.$date_f.'/hrt_test_print_'. $_SESSION['patient']."_".$_SESSION['authId']."_".$rand;
	$objTests->mk_print_folder($test_name,$date_f,$oSaveFile->upDir."/UserId_".$_SESSION['authId']."/tmp/");
	$final_html_file_name_path = $oSaveFile->upDir."/UserId_".$_SESSION['authId']."/tmp/".$html_file_name;
	// FILE NAME for PRINT
	/*$date_f=date("Y_m_d");
	$rand=rand(0,500);
	$test_name="chart_tests";
	$html_file_name = $test_name.'/'.$date_f.'/hrt_test_print_'. $_SESSION['patient']."_".$_SESSION['authId']."_".$rand;
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
	$oChartTestPrev = new ChartTestPrev($patient_id,"HRT");
	
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
		$q = "SELECT * FROM ".$test_table_name." WHERE ".$this_test_properties['patient_key']." = '".$patient_id."' AND form_id = '".$form_id."' AND purged='0' AND del_status='0'";
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
		$nfa_mode = "new";
		$test_edid = "0";
	}else{
		$nfa_mode = "update";
		$test_edid = $row["nfa_id"];
	}

	//Default
	if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){//New Recors
		$tmp = ($row != false) ? $row["examDate"] : "";
		$row = $objTests->valuesNewRecordsTests($test_table_name, $patient_id, " * ",$tmp);
	}

	if($row != false){
		$nfa_pkid = $row["nfa_id"];
		$test_form_id = $row["form_id"];
		$elem_examDate = ($nfa_mode != "new") ? get_date_format($row["examDate"]) : $elem_examDate;
		$elem_examTime = ($nfa_mode != "new") ? $row["examTime"] : $elem_examTime ;
		$elem_scanLaserNfa = $row["scanLaserNfa"];
		$elem_scanLaserEye = $row["scanLaserEye"];
		$elem_performedBy = ($nfa_mode != "new") ? $row["performBy"] : "" ;
		$elem_ptUndersatnding = $row["ptUndersatnding"];
		$elem_diagnosis = $row["diagnosis"];
		$elem_reliabilityOd = $row["reliabilityOd"];
		$elem_reliabilityOs = $row["reliabilityOs"];
		$elem_scanLaserOd = $row["scanLaserOd"];
		$elem_descOd = $row["descOd"];
		$elem_scanLaserOs = $row["scanLaserOs"];
		$elem_descOs = $row["descOs"];
		$elem_stable = $row["stable"];
		$elem_monitorIOP = $row["monitorIOP"];
		$elem_fuApa = $row["fuApa"];
		$elem_ptInformed = $row["ptInformed"];
		$elem_comments = stripslashes($row["comments"]);
		$elem_vfSign = $row["signature"];
		$elem_physician = ( $nfa_mode == "update" ) ? $row["phyName"] : "" ;
		$elem_diagnosisOther = $row["diagnosisOther"];
		$elem_techComments = stripslashes($row["techComments"]);
	
		$Normal_OD_T = $row["Normal_OD_T"];
		$Normal_OD_1 = $row["Normal_OD_1"];
		$Normal_OD_2 = $row["Normal_OD_2"];
		$Normal_OD_3 = $row["Normal_OD_3"];
		$Normal_OD_4 = $row["Normal_OD_4"];
		$BorderLineDefect_OD_T = $row["BorderLineDefect_OD_T"];
		$BorderLineDefect_OD_1 = $row["BorderLineDefect_OD_1"];
		$BorderLineDefect_OD_2 = $row["BorderLineDefect_OD_2"];
		$BorderLineDefect_OD_3 = $row["BorderLineDefect_OD_3"];
		$BorderLineDefect_OD_4 = $row["BorderLineDefect_OD_4"];
		$Abnorma_OD_T = $row["Abnorma_OD_T"];
		$Abnorma_OD_1 = $row["Abnorma_OD_1"];
		$Abnorma_OD_2 = $row["Abnorma_OD_2"];
		$Abnorma_OD_3 = $row["Abnorma_OD_3"];
		$Abnorma_OD_4 = $row["Abnorma_OD_4"];
		$Others_OD = stripslashes($row["Others_OD"]);
		$Normal_OS_T = $row["Normal_OS_T"];
		$Normal_OS_1 = $row["Normal_OS_1"];
		$Normal_OS_2 = $row["Normal_OS_2"];
		$Normal_OS_3 = $row["Normal_OS_3"];
		$Normal_OS_4 = $row["Normal_OS_4"];
		$BorderLineDefect_OS_T = $row["BorderLineDefect_OS_T"];
		$BorderLineDefect_OS_1 = $row["BorderLineDefect_OS_1"];
		$BorderLineDefect_OS_2 = $row["BorderLineDefect_OS_2"];
		$BorderLineDefect_OS_3 = $row["BorderLineDefect_OS_3"];
		$BorderLineDefect_OS_4 = $row["BorderLineDefect_OS_4"];
		$Abnorma_OS_T = $row["Abnorma_OS_T"];
		$Abnorma_OS_1 = $row["Abnorma_OS_1"];
		$Abnorma_OS_2 = $row["Abnorma_OS_2"];
		$Abnorma_OS_3 = $row["Abnorma_OS_3"];
		$Abnorma_OS_4 = $row["Abnorma_OS_4"];
		$Others_OS = stripslashes($row["Others_OS"]);
		$elem_tech2InformPt = $row["tech2InformPt"];
		$elem_normal_poorStudy_od = $row["Normal_OD_PoorStudy"];
		$elem_normal_poorStudy_os = $row["Normal_OS_PoorStudy"];
	
		$elem_noSigChange_OD = $row["NoSigChange_OD"];
		$elem_improved_OD = $row["Improved_OD"];
		$elem_incAbn_OD = $row["IncAbn_OD"];
	
		$elem_noSigChange_OS = $row["NoSigChange_OS"];
		$elem_improved_OS = $row["Improved_OS"];
		$elem_incAbn_OS = $row["IncAbn_OS"];
	
		$elem_targetIop_OD = $row["iopTrgtOd"];
		$elem_targetIop_OS = $row["iopTrgtOs"];
		$elem_informedPtNv = $row["ptInformedNv"];
		$elem_contiMeds = $row["contiMeds"];
	
		$decreased_OD = !empty($row["decreaseOd"]) ? explode(",",$row["decreaseOd"]) : array();
		$decreased_OS = !empty($row["decreaseOs"]) ? explode(",",$row["decreaseOs"]) : array();
	
		$thin_OD = !empty($row["thinOd"]) ? explode(",",$row["thinOd"]) : array();
		$thin_OS = !empty($row["thinOs"]) ? explode(",",$row["thinOs"]) : array();
	
		$total_thin_OD = $row["totalThinOd"];
		$total_thin_OS = $row["totalThinOs"];
		$elem_rptTst1yr = $row["rptTst1yr"];
	
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
			$sign_path_date = date("".phpDateFormat()."",strtotime($sign_path_date_time));
			$sign_path_time = date("h:i A",strtotime($sign_path_date_time));
		}
	}


	if(!empty($form_id)){
		$strTrgtVal = $elem_targetIop_OD = $elem_targetIop_OS = "";
		//def_table
		$row = $objTests->getIopTrgtDef($patient_id,$form_id);
		if($row != false){
			$elem_targetIop_OD = $row["iopTrgtOd"];
			$elem_targetIop_OS = $row["iopTrgtOs"];
		}
	}
	if( empty($elem_targetIop_OD) && empty($elem_targetIop_OS) ){
		list($elem_targetIop_OD, $elem_targetIop_OS) = $objTests->getIopTrgtVals($patient_id);	// iop
	}
	if( empty($elem_targetIop_OD) && empty($elem_targetIop_OS) ){
		list($elem_targetIop_OD, $elem_targetIop_OS) = $objTests->getGlucomaTargetIop($patient_id); // glucoma
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
	$sb_testName = 'HRT';
	
	//Cpt Code Desc
	$thisCptDescSym = "HRT";

	//Prev + Next Records --
	$tmp = getDateFormatDB($elem_examDate);//Exam Date
	
	$tstPrevId = $oChartTestPrev->getPrevId($tmp,$nfa_pkid);//getPervId
	$tstNxtId = $oChartTestPrev->getNxtId($tmp,$nfa_pkid);//getNextId

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

	function printTest()
	{
		var tId = "<?php echo $_GET["tId"];?>";
		window.open('../../library/html_to_pdf/createPdf.php?op=l&onePage=false&file_location=<?php echo $html_file_name;?>&mergePDF=1&name=test_'+tId+'_pdf&testIds='+tId+'&saveOption=F&image_from=<?php echo $testname; ?>','vf_Rpt','menubar=0,resizable=yes');	
	}
	
	// Prev Data
	var oPrvDt;
	function getPrevTestDt(ctid,cdt,ctm,dir){
		getPrevTestDtExe(ctid,cdt,ctm,dir,"HRT");
	}

	function saveNfa()
	{
		if(top.frm_submited==1){ return; } 
		top.frm_submited=1;
		<?php if($elem_per_vo == "1"){echo "return;";}?>
		var f = document.test_form_frm;
		var m = "<b>Please fill the following:-</b><br>";
		var err = false;
		//elem_phyName
		if( (f.elem_performedBy.value == "") || (f.elem_performedBy.value == "0") )
		{
			m += "&bull; Performed By (Physician or Technician only)<br>";
			err = true;
		}
		if(f.elem_opidTestOrdered.value==""){
			m += '&bull; Order By<br>';
			err = true;
		}
		//Super Bill
		var oSB = isSuperBillMade();
		if(oSB.SBill == true){					
			if(oSB.DXCodeOK == false  ||  oSB.DXCodeAssocOK == false ){
				m += '&bull; Dx code in Super bill<br>';
				err = true;
			}
			if(oSB.DXCodeComplete==false){
				m += '&bull; Incomplete ICD-10 DX code(s) in Super bill<br>';
				err = true;
			}
		}
		//alert(f.elem_vfSign.value+"\n"+f.elem_phyName.value+"\n"+err)
		if(err == false){
			if(typeof(opener) == "undefined" && typeof(top.show_loading_image)!="undefined"){  top.show_loading_image("show");}
			f.submit();
		}
		else{
			top.fAlert(m,'','top.frm_submited=0;');
		}
	}
<?php }?>

function selectOs(){
	var elements = document.test_form_frm.elements;
	var eleLen = elements.length;
	for(var i=0;i<eleLen;i++){
		if(elements[i].type == "checkbox"){
			var eleName = elements[i].name;
			if((eleName.indexOf('OD')!= -1) || (eleName.indexOf('od')!= -1) ){
				var chkStatus = elements[i].checked;
				eleName = (eleName.indexOf('od')!= -1) ? eleName.replace(/od/, "os") : eleName.replace(/OD/, "OS");
				var eleOs = document.getElementById(eleName);
				if(eleOs){
					eleOs.checked = chkStatus;
				}
			}
		}
	}
	document.getElementById('Others_OS').value = document.getElementById('Others_OD').value;
	document.getElementById('elem_targetIop_OS').value = document.getElementById('elem_targetIop_OD').value;
	document.getElementById('total_thin_OS').value = document.getElementById('total_thin_OD').value;
}
//Set Tests and Reliability fields as per interpretation
function setReli_Test(wh){
	var arrReli = new Array("elem_reliabilityOd","elem_reliabilityOs");
	var arrTestOd = new Array( "Normal_OD_T","elem_normal_poorStudy_od",
					 "BorderLineDefect_OD_T","BorderLineDefect_OD_1",
					 "BorderLineDefect_OD_2","BorderLineDefect_OD_3",
					 "BorderLineDefect_OD_4",
					 "Abnorma_OD_T","Abnorma_OD_1","Abnorma_OD_2",
					 "Abnorma_OD_3","Abnorma_OD_4",
					 "elem_noSigChange_OD","elem_improved_OD","elem_incAbn_OD",
					 "elem_targetIop_OD","Others_OD",
					 "decreased_OD_RA","decreased_OD_RV","decreased_OD_HVC",
					 "decreased_OD_CSM","decreased_OD_NFL","decreased_OD_MC",
					 "thin_OD_Temp","thin_OD_ST","thin_OD_SN","thin_OD_N",
					 "thin_OD_IN","thin_OD_IT",
					 "total_thin_OD" );
	setReli_Exe(wh,arrTestOd,arrReli);
}
</script>
</head>
<body>
<form name="test_form_frm" action="save_tests.php" method="post" style="margin:0px;">
<?php if($callFromInterface != 'admin'){?>
    <input type="hidden" name="elem_saveForm" id="elem_saveForm" value="NFA">
    <input type="hidden" name="elem_patientId" id="elem_patientId" value="<?php echo $patient_id;?>">
    <input type="hidden" name="elem_formId" id="elem_formId" value="<?php echo $form_id;?>">
    <input type="hidden" name="elem_tests_name_id" id="elem_tests_name_id" value="<?php echo $test_master_id;?>">
    <input type="hidden" id="elem_edMode" name="hd_nfa_mode" value="<?php echo $nfa_mode;?>">
    <input type="hidden" id="elem_testId" name="elem_nfaId" value="<?php echo $test_edid;?>">
    <input type="hidden" name="wind_opn" id="wind_opn" value="0">
    <input type="hidden" name="elem_operatorId" value="<?php echo $elem_operatorId;?>">
    <input type="hidden" name="elem_operatorName" value="<?php echo $elem_operatorName;?>">
    <input type="hidden" name="elem_noP" value="<?php echo $noP;?>">
    <input type="hidden" name="elem_examTime" value="<?php echo $elem_examTime;?>">
    <input type="hidden" name="pop" value="<?php echo $_REQUEST['pop'] ?>">
    <!--the hidden field doNotShowRightSide	is used for mest maneger-->
    <input type="hidden" name="doNotShowRightSide" value="<?php echo $_REQUEST['doNotShowRightSide']; ?>">
    <input type="hidden" name="hidFormLoaded" id="hidFormLoaded" value="0">
    <input type="hidden" name="elem_phyName_order" id="elem_phyName_order" value="<?php echo $elem_phyName_order; ?>" data-phynm="<?php echo (!empty($elem_phyName_order)) ? $objTests->getPersonnal3($elem_phyName_order) : "" ; ?>">
    <?php $flg_interpreted_btn = !empty($elem_phyName_order) ? 1 : 0;?>
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
                            &nbsp;
                        </div>
                        <div class="col-sm-5 siteopt">
                            <div class="tstopt">
                                <div class="row">
                                    <div class="col-sm-3 sitehd">Sites</div>
                                    <div class="col-sm-5 testopt">
                                        <ul>
                                            <li class="ouc"><label><input type="radio" name="elem_scanLaserEye" value="OU" onClick="setReli_Test(this.value)" <?php echo (!$elem_scanLaserEye || $elem_scanLaserEye == "OU") ? "checked" : "" ; ?>><span class="drak_purple_color label_txt">OU</span></label></li>
                                            <li class="odc"><label><input type="radio" name="elem_scanLaserEye" value="OD" onClick="setReli_Test(this.value)" <?php echo ($elem_scanLaserEye == "OD") ? "checked" : "" ; ?>><span class="blue_color label_txt">OD</span></label></li>
                                            <li class="osc"><label><input type="radio" name="elem_scanLaserEye" value="OS" onClick="setReli_Test(this.value)" <?php echo ($elem_scanLaserEye == "OS") ? "checked" : "" ; ?>><span class="green_color label_txt">OS</span></label></li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-4">
                                        &nbsp;
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
                                            $arrDigOpts = $objTests->getDiagOpts('1','nfa',$test_master_id);
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
                                <textarea class="form-control" rows="2" style="width:100%; height:55px !important;" name="techComments" id="techComment" placeholder="Technician Comments"><?php echo $elem_techComments;?></textarea>
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
                            <td rowspan="11" align="center" valign="middle" class="bltra"><a href="javascript:selectOs();">BL</a></td>
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
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Normal_OD_T" name="Normal_OD_T" <?php if(strpos($normal_OD, "Normal")!==false) echo 'CHECKED'; ?> value="Normal"><span class="label_txt">Normal</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_normal_poorStudy_od" name="elem_normal_poorStudy_od" value="Poor Study" <?php if(strpos($normal_OD, "Poor Study")!==false) echo "checked";?>><span class="label_txt">Poor Study</span></label></div>
                                </div>
                        	</td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="Normal_OS_T" name="Normal_OS_T" <?php if($Normal_OS_T==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">Normal</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_normal_poorStudy_os" name="elem_normal_poorStudy_os" value="1" <?php echo ($elem_normal_poorStudy_os == "1") ? "checked" : ""; ?>><span class="label_txt">Poor Study</span></label></div>
                                </div>
                        	</td>
                        </tr>
						<tr>
                            <td class="tdlftpan"><strong>Border Line Defect</strong></td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="BorderLineDefect_OD_T" name="BorderLineDefect_OD_T" <?php if($BorderLineDefect_OD_T==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">T</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="BorderLineDefect_OD_1" name="BorderLineDefect_OD_1" <?php if($BorderLineDefect_OD_1==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+1</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="BorderLineDefect_OD_2" name="BorderLineDefect_OD_2" <?php if($BorderLineDefect_OD_2==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+2</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="BorderLineDefect_OD_3" name="BorderLineDefect_OD_3" <?php if($BorderLineDefect_OD_3==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+3</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="BorderLineDefect_OD_4" name="BorderLineDefect_OD_4" <?php if($BorderLineDefect_OD_4==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+4</span></label></div>
                                </div>
							</td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="BorderLineDefect_OS_T" name="BorderLineDefect_OS_T" <?php if($BorderLineDefect_OS_T==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">T</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="BorderLineDefect_OS_1" name="BorderLineDefect_OS_1" <?php if($BorderLineDefect_OS_1==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+1</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="BorderLineDefect_OS_2" name="BorderLineDefect_OS_2" <?php if($BorderLineDefect_OS_2==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+2</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="BorderLineDefect_OS_3" name="BorderLineDefect_OS_3" <?php if($BorderLineDefect_OS_3==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+3</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="BorderLineDefect_OS_4" name="BorderLineDefect_OS_4" <?php if($BorderLineDefect_OS_4==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+4</span></label></div>
                                </div>
							</td>
                        </tr>
                        <tr>
                            <td class="tdlftpan"><strong>Abnormal</strong></td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
	                                <div class="col-sm-1"></div>
                                	<div class="col-sm-2"><label><input type="checkbox" id="Abnorma_OD_T" name="Abnorma_OD_T" <?php if($Abnorma_OD_T==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">T</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Abnorma_OD_1" name="Abnorma_OD_1" <?php if($Abnorma_OD_1==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+1</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Abnorma_OD_2" name="Abnorma_OD_2" <?php if($Abnorma_OD_2==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+2</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Abnorma_OD_3" name="Abnorma_OD_3" <?php if($Abnorma_OD_3==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+3</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Abnorma_OD_4" name="Abnorma_OD_4" <?php if($Abnorma_OD_4==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+4</span></label></div>
                                </div>
							</td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                	<div class="col-sm-1"></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Abnorma_OS_T" name="Abnorma_OS_T" <?php if($Abnorma_OS_T==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">T</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Abnorma_OS_1" name="Abnorma_OS_1" <?php if($Abnorma_OS_1==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+1</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Abnorma_OS_2" name="Abnorma_OS_2" <?php if($Abnorma_OS_2==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+2</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Abnorma_OS_3" name="Abnorma_OS_3" <?php if($Abnorma_OS_3==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+3</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="Abnorma_OS_4" name="Abnorma_OS_4" <?php if($Abnorma_OS_4==1) echo 'CHECKED'; ?> value="1"><span class="label_txt">+4</span></label></div>
                                </div>
							</td>
                        </tr>
                        <tr>
                            <td class="tdlftpan"><strong>Decreased</strong></td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-6"><label><input type="checkbox" id="decreased_OD_RA" name="decreased_OD_RA" <?php if(in_array("Rim Area",$decreased_OD)) echo 'CHECKED';?> value="Rim Area"><span class="label_txt">Rim Area</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="decreased_OD_RV" name="decreased_OD_RV" <?php if(in_array("Rim Volumn",$decreased_OD)) echo 'CHECKED'; ?> value="Rim Volumn"><span class="label_txt">Rim Volume</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-6"><label><input type="checkbox" id="decreased_OD_HVC" name="decreased_OD_HVC" <?php if(in_array("Height Variation Contour",$decreased_OD)) echo 'CHECKED';?> value="Height Variation Contour"><span class="label_txt">Height Variation Contour</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="decreased_OD_CSM" name="decreased_OD_CSM" <?php if(in_array("Cup Shape Measure",$decreased_OD)) echo 'CHECKED'; ?> value="Cup Shape Measure"><span class="label_txt">Cup Shape Measure</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-6"><label><input type="checkbox" id="decreased_OD_NFL" name="decreased_OD_NFL" <?php if(in_array("NFL",$decreased_OD)) echo 'CHECKED'; ?> value="NFL"><span class="label_txt">NFL</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="decreased_OD_MC" name="decreased_OD_MC" <?php if(in_array("MC Intact RIM X 360",$decreased_OD)) echo 'CHECKED'; ?> value="MC Intact RIM X 360"><span class="label_txt">MC Intact RIM X 360</span></label></div>
                                </div>
							</td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-6"><label><input type="checkbox" id="decreased_OS_RA" name="decreased_OS_RA" <?php if(in_array("Rim Area",$decreased_OS)) echo 'CHECKED'; ?> value="Rim Area"><span class="label_txt">Rim Area</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="decreased_OS_RV" name="decreased_OS_RV" <?php if(in_array("Rim Volumn",$decreased_OS)) echo 'CHECKED'; ?> value="Rim Volumn"><span class="label_txt">Rim Volume</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-6"><label><input type="checkbox" id="decreased_OS_HVC" name="decreased_OS_HVC" <?php if(in_array("Height Variation Contour",$decreased_OS)) echo 'CHECKED'; ?> value="Height Variation Contour"><span class="label_txt">Height Variation Contour</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="decreased_OS_CSM" name="decreased_OS_CSM" <?php if(in_array("Cup Shape Measure",$decreased_OS)) echo 'CHECKED'; ?> value="Cup Shape Measure"><span class="label_txt">Cup Shape Measure</span></label></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-6"><label><input type="checkbox" id="decreased_OS_NFL" name="decreased_OS_NFL" <?php if(in_array("NFL",$decreased_OS)) echo 'CHECKED'; ?> value="NFL"><span class="label_txt">NFL</span></label></div>
                                    <div class="col-sm-5"><label><input type="checkbox" id="decreased_OS_MC" name="decreased_OS_MC" <?php if(in_array("MC Intact RIM X 360",$decreased_OS)) echo 'CHECKED'; ?> value="MC Intact RIM X 360"><span class="label_txt">MC Intact RIM X 360</span></label></div>
                                </div>
							</td>
                        </tr>
                                                <tr>
                            <td class="tdlftpan"><strong>Thin</strong></td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="thin_OD_Temp" name="thin_OD_Temp" <?php if(in_array("Temp",$thin_OD)) echo 'CHECKED'; ?> value="Temp"><span class="label_txt">Temp</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="thin_OD_ST" name="thin_OD_ST" <?php if(in_array("ST",$thin_OD)) echo 'CHECKED'; ?> value="ST"><span class="label_txt">ST</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="thin_OD_SN" name="thin_OD_SN" <?php if(in_array("SN",$thin_OD)) echo 'CHECKED'; ?> value="SN"><span class="label_txt">SN</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="thin_OD_N" name="thin_OD_N" <?php if(in_array("N",$thin_OD)) echo 'CHECKED'; ?> value="N"><span class="label_txt">N</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="thin_OD_IN" name="thin_OD_IN" <?php if(in_array("IN",$thin_OD)) echo 'CHECKED'; ?> value="IN"><span class="label_txt">IN</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="thin_OD_IT" name="thin_OD_IT" <?php if(in_array("IT",$thin_OD)) echo 'CHECKED'; ?> value="IT"><span class="label_txt">IT</span></label></div>
                                    
                                </div>
							</td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="thin_OS_Temp" name="thin_OS_Temp" <?php if(in_array("Temp",$thin_OS)) echo 'CHECKED'; ?> value="Temp"><span class="label_txt">Temp</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="thin_OS_ST" name="thin_OS_ST" <?php if(in_array("ST",$thin_OS)) echo 'CHECKED'; ?> value="ST"><span class="label_txt">ST</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="thin_OS_SN" name="thin_OS_SN" <?php if(in_array("SN",$thin_OS)) echo 'CHECKED'; ?> value="SN"><span class="label_txt">SN</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="thin_OS_N" name="thin_OS_N" <?php if(in_array("N",$thin_OS)) echo 'CHECKED'; ?> value="N"><span class="label_txt">N</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="thin_OS_IN" name="thin_OS_IN" <?php if(in_array("IN",$thin_OS)) echo 'CHECKED'; ?> value="IN"><span class="label_txt">IN</span></label></div>
                                    <div class="col-sm-1"><label><input type="checkbox" id="thin_OS_IT" name="thin_OS_IT" <?php if(in_array("IT",$thin_OS)) echo 'CHECKED'; ?> value="IT"><span class="label_txt">IT</span></label></div>
                                    
                                </div>
							</td>
                        </tr>
                                                <tr>
                            <td class="tdlftpan"><strong>Total Thin</strong></td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-4"><input type="text" id="total_thin_OD" name="total_thin_OD" value="<?php echo $total_thin_OD;?>" class="form-control"></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_noSigChange_OD" name="elem_noSigChange_OD" value="1" <?php echo ($elem_noSigChange_OD == "1") ? "checked" : ""; ?>><span class="label_txt">No Sig. Change</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_improved_OD" name="elem_improved_OD" value="1" <?php echo ($elem_improved_OD == "1") ? "checked" : ""; ?>><span class="label_txt">Improved</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_incAbn_OD" name="elem_incAbn_OD" value="1" <?php echo ($elem_incAbn_OD == "1") ? "checked" : ""; ?>><span class="label_txt">Inc. Abn</span></label></div>
                                </div>
							</td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-4"><input type="text" id="total_thin_OS" name="total_thin_OS" value="<?php echo $total_thin_OS;?>" class="form-control"></div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_noSigChange_OS" name="elem_noSigChange_OS" value="1" <?php echo ($elem_noSigChange_OS == "1") ? "checked" : ""; ?>><span class="label_txt">No Sig. Change</span></label></div>
                                    <div class="col-sm-4"><label><input type="checkbox" id="elem_improved_OS" name="elem_improved_OS" value="1" <?php echo ($elem_improved_OS == "1") ? "checked" : ""; ?>><span class="label_txt">Improved</span></label></div>
                                    <div class="col-sm-2"><label><input type="checkbox" id="elem_incAbn_OS" name="elem_incAbn_OS" value="1" <?php echo ($elem_incAbn_OS == "1") ? "checked" : ""; ?>><span class="label_txt">Inc. Abn</span></label></div>
                                </div>
							</td>
                        </tr>
                        <tr>
                            <td class="tdlftpan"><strong>IOP Target</strong></td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-4"><input type="text" id="elem_targetIop_OD"  name="elem_targetIop_OD" value="<?php echo ($elem_targetIop_OD);?>" size="10" class="form-control"></div>
                                </div>
							</td>
                            <td class="pd5 tstbx">
                                <div class="row tstrstopt">
                                    <div class="col-sm-1"></div>
                                    <div class="col-sm-4"><input type="text" id="elem_targetIop_OS"  name="elem_targetIop_OS" value="<?php echo ($elem_targetIop_OS);?>" size="10" class="form-control"></div>
                                </div>
							</td>
                        </tr>
                        <tr>
                          <td class="tdlftpan"><strong>Other</strong></td>
                          <td><textarea cols="38" rows="3" id="Others_OD" name="Others_OD" class="form-control" style="width:100%; height:50px;"><?php echo ($Others_OD);?></textarea></td>
                          <td><textarea cols="38" rows="3" id="Others_OS" name="Others_OS" class="form-control" style="width:100%; height:50px;"><?php echo ($Others_OS);?></textarea></td>
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
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_monitorIOP" value="1" <?php echo ($elem_monitorIOP == "1") ? "checked" : "" ;?>><span class="label_txt">Monitor&nbsp;IOP</span></label></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_tech2InformPt" value="1" <?php echo ($elem_tech2InformPt == "1") ? "checked" : "" ;?>><span class="label_txt">Tech to Inform Pt.</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_informedPtNv" value="1" <?php echo ($elem_informedPtNv == "1") ? "checked" : "" ;?>><span class="label_txt">Inform Pt result next visit</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_fuApa" value="1" <?php echo ($elem_fuApa == "1") ? "checked" : "" ;?>><span class="label_txt">F/U APA</span></label></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_ptInformed" value="1" <?php echo ($elem_ptInformed == "1") ? "checked" : "" ;?>><span class="label_txt">Pt informed of results</span></label></div>                                        
                                    	<div class="col-sm-4"><label><input type="checkbox" name="elem_rptTst1yr" value="1" <?php echo ($elem_rptTst1yr == "1") ? "checked" : "";?>><span class="label_txt">Repeat test 1 year</span></label></div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                
                                <div>
                                    <h2>Comments</h2>
                                    <div class="clearfix"></div>	
                                    <textarea cols="23" rows="2"  id="elem_comments" name="elem_comments" class="form-control" style="width:100%; height:50px;"><?php echo $elem_comments; ?></textarea>
                                </div>
                                <div class="clearfix"></div>
                                
                                <?php if($callFromInterface != 'admin'){
									$sigFolderName = 'test_nfa';
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
							<input type="button" class="btn btn-success<?php echo $btnHide;?>" id="btn_done" value="Done"  onClick="saveNfa()" />
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
							<input type="button"  class="btn btn-success<?php echo $btnHide;?>" value="Order" id="save" onClick="saveNfa()" />
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
						top.btn_show("NFA",btnArr);
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
	top.set_header_title('<?php echo $this_test_screen_name;?>');
	<?php if(($elem_scanLaserEye == "OD") || ($elem_scanLaserEye == "OS")){echo "setReli_Test(\"".$elem_scanLaserEye."\");";}?>
<?php }?>
</script>
<?php
if($callFromInterface != 'admin'){
	//Previous Test div--
	$oChartTestPrev->showdiv();
    if($_GET["tId"]!='')	// position of include file cannot go above than these lines
	{
		include 'test_nfa_print.php';
	}
}
?>
</script>
</body>
</html>