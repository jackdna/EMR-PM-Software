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
$testname               = "CustomTests";
$objEpost				= new Epost($patient_id,$testname);
//$objTests->patient_id 	= $patient_id;

//MAKING OBJECT TO SAVE IMAGE FILES
$oSaveFile = new SaveFile($patient_id);

$test_master_id			= intval(trim($_GET['test_master_id']));
if($test_master_id==0) 	  $test_master_id = $objTests->get_template_test_id($_GET['tId'],'custom');
$this_test_properties	= $objTests->get_table_cols_by_test_table_name($test_master_id,'id');
$test_table_name		= $this_test_properties['test_table'];
$this_test_screen_name	= $this_test_properties['temp_name'];

//User and  User_type
$logged_user 	= $objTests->logged_user;
$userType 		= $objTests->logged_user_type;

//GET VERSION HTML DETAILS
$this_version_data					= $objTests->get_template_test_version_data($this_test_properties['id'],$this_test_properties['version']);
$elem_version_id					= $this_test_properties['version'];
$elem_test_main_options 			= $this_version_data['test_main_options'];
$elem_test_results 					= $this_version_data['test_results'];
$elem_test_treatment 				= $this_version_data['test_treatment'];

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
	$html_file_name = $test_name.'/'.$date_f.'/custom_test_print_'. $_SESSION['patient']."_".$_SESSION['authId']."_".$rand;
	$objTests->mk_print_folder($test_name,$date_f,$oSaveFile->upDir."/UserId_".$_SESSION['authId']."/tmp/");
	$final_html_file_name_path = $oSaveFile->upDir."/UserId_".$_SESSION['authId']."/tmp/".$html_file_name;
	

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
	$oChartTestPrev = new ChartTestPrev($patient_id,"CUSTOM",$test_master_id);
	
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
		$q = "SELECT * FROM ".$test_table_name." WHERE ".$this_test_properties['patient_key']." = '".$patient_id."' AND test_template_id = '$test_master_id' AND formId = '$form_id' AND purged='0' AND del_status='0'";
		$res = imw_query($q);
		$row = imw_fetch_assoc($res);
	}else if(isset($tId) && !empty($tId)){//Get record based on patient id and test id
			$q = "SELECT * FROM ".$test_table_name." WHERE ".$this_test_properties['patient_key']." = '".$patient_id."' AND test_id = '".$tId."' AND test_template_id = '$test_master_id'";
			$res = imw_query($q);
			$row = imw_fetch_assoc($res);
	}else{
		$row = false; // open new test for patient
	}

	if($row == false){	//&& ($finalize_flag == 0)
		//SET MODE
		$test_mode = "new";
		$test_edid = "0";
	}else{
		$test_mode = "update";
		$test_edid = $row["test_id"];
	}

	if($row != false){
		$test_pkid = $row["test_id"];
		$test_form_id = $row["formId"];
		$elem_examDate = ($test_mode != "new") ? get_date_format($row["examDate"]) : $elem_examDate;
		$elem_examTime = ($test_mode != "new") ? $row["examTime"] : $elem_examTime ;
		$elem_testOtherName = $row["test_other"];
		$elem_topoMeterEye = $row["test_other_eye"];
		$elem_performedBy = ($test_mode != "new") ? $row["performedBy"] : "";
		$elem_ptUnderstanding = $row["ptUnderstanding"];
		$elem_diagnosis = $row["diagnosis"];
		$elem_diagnosisOther = $row["diagnosisOther"];
		$elem_reliabilityOd = $row["reliabilityOd"];
		$elem_reliabilityOs = $row["reliabilityOs"];
	
	//	$elem_descOd = stripslashes($row["descOd"]);
	//	$elem_descOs = stripslashes($row["descOs"]);
	//	$elem_inter_pret_od = stripslashes($row["inter_pret_od"]);
	//	$elem_inter_pret_os = stripslashes($row["inter_pret_os"]);
		/*$elem_stable = $row["stable"];
		$elem_fuApa = $row["fuApa"];
		$elem_ptInformed = $row["ptInformed"];
		$elem_tech2InformPt=$row["tech2InformPt"];
		$elem_informedPtNv = $row["ptInformedNv"];
		$elem_contiMeds = $row["contiMeds"];*/
		$elem_physician = ( $test_mode == "update" ) ? $row["phyName"] : "" ;
		$elem_techComments = stripslashes($row["techComments"]);
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
			$sign_path_date = date("m-d-Y",strtotime($sign_path_date_time));
			$sign_path_time = date("h:i A",strtotime($sign_path_date_time));
		}
	
		//GET VERSION HTML DETAILS
		$this_version_data			= $objTests->get_template_test_version_data($this_test_properties['id'],$row['version']);
		$elem_version_id			= $this_version_data['id'];
		$elem_test_main_options 	= $this_version_data['test_main_options'];
		$elem_test_results 			= $this_version_data['test_results'];
		$elem_test_treatment 		= $this_version_data['test_treatment'];
		
		//this version data values
		$test_main_options 			= addslashes($row['test_main_options']);
		$test_result				= addslashes($row['test_result']);
		$test_treatment				= addslashes($row['test_treatment']);
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
	$sb_testName = $this_test_properties['temp_name'];

	//Cpt Code Desc
	$thisCptDescSym = "CustomTests";
	
	//include($incdir."/chart_notes/superbill_init.php");
	//Super bill init() --

	//Prev + Next Records --
	$tmp = getDateFormatDB($elem_examDate);//Exam Date
	$tstPrevId = $oChartTestPrev->getPrevId($tmp,$test_pkid);//getPervId
	$tstNxtId = $oChartTestPrev->getNxtId($tmp,$test_pkid);//getNextId
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
<?php if($callFromInterface != 'admin'){?>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/icd10_autocomplete.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/js_gen.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/typeahead.js"></script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/superbill.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/lightbox/lightbox.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/epost.js"></script>
<?php }?>
<script src="<?php echo $library_path; ?>/js/common.js?<?php echo filemtime('../../library/js/common.js');?>" type="text/javascript"></script>
<script>var isDssEnable = '<?php echo isDssEnable(); ?>';</script>
<script src="<?php echo $library_path; ?>/js/tests.js?<?php echo filemtime('../../library/js/tests.js');?>" type="text/javascript"></script>
<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script> 
<script type="text/javascript">
var arrTypeAhead 	= new Array(<?php echo $strTypeAhead;?>); //get TH
var imgPath 		= "<?php echo $GLOBALS['webroot'];?>";
var elem_per_vo 	= "<?php echo $elem_per_vo;?>";
var zPath			= "<?php echo $GLOBALS['rootdir'];?>";
var JS_WEB_ROOT_PATH 	= "<?php echo $GLOBALS['webroot']; ?>";
//Test id
var t_nxtid					= "<?php echo $tstNxtId;?>";
var t_previd				= "<?php echo $tstPrevId;?>";
var test_options_data 		= '<?php echo $test_main_options;?>';
var test_results_data 		= '<?php echo $test_result;?>';
var test_treatment_data		= '<?php echo $test_treatment;?>';
var arr_all_OD_elements		= new Array();

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
	record_all_OD_elements_IDs();
	<?php if($callFromInterface != 'admin' && (!isset($_GET["tId"]) || trim($_GET["tId"]))==''){?>
		if(typeof(fillInterpretationProfileData)=='function' && typeof(document.forms[0].sel_interpretation_profile)!='undefined'){
			if(document.forms[0].sel_interpretation_profile.value!='' && document.forms[0].sel_interpretation_profile.value != '0')
			fillInterpretationProfileData(document.forms[0].sel_interpretation_profile.value);
		}
	<?php
	}else if(isset($_GET["tId"]) && trim($_GET["tId"])!=''){?>
		fill_saved_custom_test_data(test_options_data,test_results_data,test_treatment_data);
		
	<?php
	}?>
});

<?php if($callFromInterface != 'admin'){?>
	function printTest(){
		var tId = "<?php echo $_GET["tId"];?>";
		window.open('../../library/html_to_pdf/createPdf.php?op=l&onePage=false&file_location=<?php echo $html_file_name;?>&mergePDF=1&name=test_'+tId+'_pdf&testIds='+tId+'&saveOption=F&image_from=<?php echo $testname; ?>','vf_Rpt','menubar=0,resizable=yes');	
	}
	
	// Prev Data
	var oPrvDt;
	function getPrevTestDt(ctid,cdt,ctm,dir){
		getPrevTestDtExe(ctid,cdt,ctm,dir,"CUSTOM");
	}
	
	function savePachy(){
		if(top.frm_submited==1){ return; }
		top.frm_submited=1;
		test_options_data	= $('#test_main_options').find('input').serialize();
		test_results_data	= $('#tbl_test_results tr').not(':first').find('input,select,textarea').serialize();
		treatment_data		= $('#custom_treatment_prognosis').find('input').serialize();
		
		$('#elem_test_main_options').val(test_options_data);
		$('#elem_test_result').val(test_results_data);
		$('#elem_test_treatment').val(treatment_data);
		//alert(test_options_data+"\n"+test_results_data+"\n"+treatment_data);
		
		<?php if($elem_per_vo == "1")echo "return;";?>
		var f = document.test_form_frm;
		var m = "<b>Please fill the following:-</b><br>";
		var err = false;
		if((f.elem_performedBy.value == "") || (f.elem_performedBy.value == "0")){
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
			/*if(oSB.DXCodeOK == false  ||  oSB.DXCodeAssocOK == false ){
				m += '&bull; Dx code in Super bill<br>';
				err = true;
			}
			if(oSB.DXCodeComplete==false){
				m += '&bull; Incomplete ICD-10 DX code(s) in Super bill<br>';
				err = true;
			}*/
			sb_dxids=oSB.dxids;
		}
		$("#sb_dxids").val(sb_dxids);
		if(err == false){
			if(typeof(opener) == "undefined" && typeof(top.show_loading_image)!="undefined"){  top.show_loading_image("show");}
			f.submit();
		}
		else{
			top.fAlert(m,'','top.frm_submited=0;');
		}
	}
<?php }?>

function record_all_OD_elements_IDs(){
	$('#tbl_test_results tr').not(':first').each(function(index, element) {
		od_td = $(this).find('td').each(function(index, element) {
            if(index==1){
				//alert($(this).html());
				$(this).find('input,textarea,select').each(function(index, element) {
					od_id 		= $(this).prop('id');
					arr_all_OD_elements[arr_all_OD_elements.length] = od_id;
                });	
			}
        });
    });
}

function selectOs(){
	$(arr_all_OD_elements).each(function(index, element) {
		od_id		= arr_all_OD_elements[index];
		od	 		= $('#'+od_id);
		od_type 	= od.prop('type');
		od_val 		= od.val();
		
		os_id 		= od_id.replace('_od_','_os_');
		os_obj		= document.getElementById(os_id);
		if(od_type=='textarea' || od_type=='text' || od_type=='select' || od_type=='number'){
			$('#'+os_id).val(od_val);
			if(typeof os_obj.onchange == "function"){os_obj.onchange();}
		}else if(od_type=='checkbox'){
			if(od.prop('checked')) $('#'+os_id).prop('checked',true); else $('#'+os_id).prop('checked',false);
			if(typeof os_obj.onclick == "function"){os_obj.onclick();}
		}
	});	

}



function setReli_Test(wh){
	var arrTestOd = arr_all_OD_elements;
	var arrReli = new Array("elem_reliabilityOd","elem_reliabilityOs");

	setReli_Exe(wh,arrTestOd,arrReli);
}
</script>
<style type="text/css">
.table-noborder tr td{border:0px !important;}
#test_main_options tr td{padding-top:8px !important;}
div[id^="row_od_"], div[id*=" row_od_"],div[id^="row_os_"], div[id*=" row_os_"]{margin-top:5px;}
input[type="number"]{width:80px !important;}
</style>
</head>
<body>
<input type="hidden" id="hidd_final_html_file_name_path" value="<?php echo $final_html_file_name_path;?>">
<input type="hidden" id="hidd_test_cpt_preference_variation_id" name="hidd_test_cpt_preference_variation_id"  value="0">
<form name="test_form_frm" id="test_form_frm" action="save_tests.php" method="post" style="margin:0px;">
<?php if($callFromInterface != 'admin'){?>
    <input type="hidden" name="elem_test_main_options" id="elem_test_main_options" value="<?php echo $test_main_options;?>">
    <input type="hidden" name="elem_test_result" id="elem_test_result" value="<?php echo $test_result;?>">
    <input type="hidden" name="elem_test_treatment" id="elem_test_treatment" value="<?php echo $test_treatment;?>">
    
    <input type="hidden" name="elem_saveForm" id="elem_saveForm" value="CustomTests">
    <input type="hidden" name="elem_patientId" id="elem_patientId" value="<?php echo $patient_id;?>">
    <input type="hidden" name="elem_formId" id="elem_formId" value="<?php echo $form_id;?>">
    <input type="hidden" name="elem_tests_name_id" id="elem_tests_name_id" value="<?php echo $test_master_id;?>">
    <input type="hidden" name="elem_test_template_id" value="<?php echo $this_test_properties['id'];?>">
    <input type="hidden" name="elem_version_id" id="elem_version_id" value="<?php echo $elem_version_id;?>">
    <input type="hidden" id="elem_edMode" name="elem_edMode" value="<?php echo $test_mode;?>">
    <input type="hidden" id="elem_testId" name="elem_testOtherId" value="<?php echo $test_edid;?>">
    <input type="hidden" name="wind_opn" id="wind_opn" value="0">
    <input type="hidden" name="elem_operatorId" value="<?php echo $elem_operatorId;?>">
    <input type="hidden" name="elem_operatorName" value="<?php echo $elem_operatorName;?>">
    <input type="hidden" name="elem_noP" value="<?php echo $noP;?>">
    <input type="hidden" name="elem_examTime" value="<?php echo $elem_examTime;?>">
    <input type="hidden" name="pop" value="<?php echo $_REQUEST['pop'] ?>">
     <!--the hidden field doNotShowRightSide	is used for mest maneger-->
    <input type="hidden" name="doNotShowRightSide" value="<?php echo $_REQUEST['doNotShowRightSide']; ?>">
    <input type="hidden" name="hidFormLoaded" id="hidFormLoaded" value="0">
    <input type="hidden" name="elem_phyName_order" id="elem_phyName_order" value="<?php echo $elem_phyName_order; ?>" data-phynm="<?php echo (!empty($elem_phyName_order)) ? $objTests->getPersonnal3($elem_phyName_order) : "" ; ?>" >
    <input type="hidden" name="elem_testOtherName" value="<?php echo $elem_testOtherName;?>">
    <?php $flg_interpreted_btn = (!empty($elem_phyName_order) ) ? 1 : 0; ?>
<?php }?>
<div class="container-fluid">
    <div class="mainarea">
        <div class="row">
            <div class="col-sm-<?php if($callFromInterface != 'admin' && $doNotShowRightSide != 'yes'){echo '10';}else{echo '12';}?>">
               <?php if($callFromInterface != 'admin'){ require_once("test_orderby_inc.php");}?>
                <div class="clearfix"></div>
                <div class="tstopt">
                    <div class="row">
                        <div class="col-sm-7 sitetab tstrstopt">
                            <table class="table table-noborder" id="test_main_options">
                            	<tr><?php echo html_entity_decode($elem_test_main_options);?></tr>
                            </table>
                        </div>
                        <div class="col-sm-5 siteopt">
                            <div class="tstopt">
                                <div class="row">
                                    <div class="col-sm-3 sitehd">Sites</div>
                                    <div class="col-sm-5 testopt">
                                        <ul>
                                            <li class="ouc"><label><input type="radio" name="elem_topoMeterEye" value="OU" onClick="setReli_Test(this.value)" <?php echo (!$elem_topoMeterEye || $elem_topoMeterEye == "OU") ? "checked" : "" ;?>><span class="label_txt">OU</span></label></li>
			                                <li class="odc"><label><input type="radio" name="elem_topoMeterEye" value="OD" onClick="setReli_Test(this.value)" <?php echo ($elem_topoMeterEye == "OD") ? "checked" : "" ;?>><span class="label_txt">OD</span></label></li>
            			                    <li class="osc"><label><input type="radio" name="elem_topoMeterEye" value="OS" onClick="setReli_Test(this.value)" <?php echo ($elem_topoMeterEye == "OS") ? "checked" : "" ;?>><span class="label_txt">OS</span></label></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <?php if(isDssEnable()){ require_once("test_template_dss.php"); } ?>
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
                                	<?php if(!isDssEnable()){ ?>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Diagnosis </label>
                                        <select class="form-control minimal" id="elem_diagnosis" name="elem_diagnosis" onChange="checkDiagnosis(this.value)" style="display:<?php echo ($elem_diagnosis == "Other") ? "none" : "block" ;?>;">
                                        <option>--Select--</option>
                                        <?php
                                        $arrDigOpts = $objTests->getDiagOpts('1','custom_template_pt',$test_master_id);
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
                                	<?php } ?>
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
                	
                    <div class="phyintrhead">Physician Interpretation</div>
                    <table class="table table-bordered" id="tbl_test_results">
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
                            <td rowspan="100" align="center" valign="middle" class="bltra"><a href="javascript:selectOs();">BL</a></td>
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
                        <?php echo html_entity_decode($elem_test_results);?>
                    </table>
                </div>
                <div class="clearfix"></div>
                <div class="tstfot">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="whitebox">
                                <h2>Treatment/Prognosis</h2>
                                <div class="clearfix"></div>
                                <div class="tstrstopt" id="custom_treatment_prognosis">
                                    <div class="row">
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_stable" id="elem_stable" value="1" <?php echo ($elem_stable == "1") ? "checked" : "" ;?>><span class="label_txt">Stable</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_contiMeds" id="elem_contiMeds" value="1" <?php echo ($elem_contiMeds == "1") ? "checked" : "" ;?>><span class="label_txt">Continue Meds</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_tech2InformPt" id="elem_tech2InformPt" value="1" <?php echo ($elem_tech2InformPt == "1") ? "checked" : "" ;?>><span class="label_txt">Tech to Inform Pt.</span></label></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_informedPtNv" id="elem_informedPtNv" value="1" <?php echo ($elem_informedPtNv == "1") ? "checked" : "" ;?>><span class="label_txt">Inform Pt result next visit</span></label></div>
                                        <div class="col-sm-4"><label><input type="checkbox" name="elem_fuApa" id="elem_fuApa" value="1" <?php echo ($elem_fuApa == "1") ? "checked" : "" ;?>><span class="label_txt">F/U APA</span></label></div>
                                    	<div class="col-sm-4"><label><input type="checkbox" name="elem_ptInformed" id="elem_ptInformed" value="1" <?php echo ($elem_ptInformed == "1") ? "checked" : "" ;?>><span class="label_txt">Pt informed of results</span></label></div>
                                    </div>
                                    <div class="row">
										<?php echo html_entity_decode($elem_test_treatment);?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                
                                <?php  if($callFromInterface != 'admin'){
									$sigFolderName = 'test_custom';
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
							<input type="button" class="btn btn-success<?php echo $btnHide;?>" id="btn_done" value="Done"  onClick="savePachy()" />
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
							<input type="button"  class="btn btn-success<?php echo $btnHide;?>" value="Order" id="save" onClick="savePachy()" />
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
						top.btn_show("OTHER",btnArr);
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
    init_page_display();
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
	
	$('#test_main_options input[type=checkbox]').click(function() {
		$('#hidd_test_cpt_preference_variation_id').val('0');
		if($(this).prop('checked')){
			if($(this).prop('id')!=''){
				temp_variation_id_arr = $(this).prop('id').split("_");
				if(temp_variation_id_arr.length>0){
					$('#hidd_test_cpt_preference_variation_id').val(temp_variation_id_arr[temp_variation_id_arr.length-1]);
					
					
				}
			}
			$('#test_main_options input[type=checkbox]').not($(this)).each(function(index, element) {
				$(this).prop('checked',false);
			});
		}
		
		
	});
});

<?php if($callFromInterface != 'admin'){?>
	<?php if($noP=='1'){?>
	var btnArr = new Array();
	btnArr["purged"] = "<?php echo $purged; ?>";
	btnArr["elem_per_vo"] = elem_per_vo;
	btnArr["rtpath"] = zPath;
	btnArr["interpreted"] = "<?php echo $flg_interpreted_btn; ?>";
	top.btn_show("OTHER",btnArr);
	<?php } ?>

	top.set_header_title('<?php echo strlen($this_test_screen_name) > 15 ? substr($this_test_screen_name,0,12).'..' : $this_test_screen_name;?>');

	<?php if(($elem_topoMeterEye == "OD") || ($elem_topoMeterEye == "OS")){echo "setReli_Test(\"".$elem_topoMeterEye."\");";}?>
<?php }?>
</script>
<?php
if($callFromInterface != 'admin'){
	//Previous Test div--
	$oChartTestPrev->showdiv();
}
?>
<input type="hidden" id="hidd_images_data" value="<?php echo htmlentities($STRPRINT);?>">
<div id="pdf_signature_data_div" style="display:none"><?php echo $sign_path_print;?></div>
<iframe id="frm_custom_test_print" name="frm_custom_test_print" src="test_template_custom_patient_rendered.php?tId=<?php echo $_GET['tId'];?>&final_html_file_name_path=<?php echo urlencode($final_html_file_name_path);?>" style="width:100%; height:500px; display:none;"></iframe>
</body>
</html>