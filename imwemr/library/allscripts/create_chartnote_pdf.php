<?php

$ignoreAuth = true;
//include_once( '../../interface/globals.php' );
include_once("../../config/globals.php");

$_SESSION = unserialize($_REQUEST['session']);

$_SESSION['asPrint'] = true; //added to print the all active medication, allergies data - use in getMedicalHistoryPrint.php file
$_SESSION['printTestData'] = 'printTestsData';  //added to print the all tests data - use in visionPrintWithNotes.php file

include_once($GLOBALS['srcdir']."/classes/SaveFile.php");
include_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/print_functions_class.php');
include_once($GLOBALS['fileroot'].'/interface/chart_notes/chart_globals.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/wv_functions.php');
$library_path = $GLOBALS['webroot'].'/library';

$pid = $_SESSION['patient'];

$cpr = New CmnFunc($pid);
$pid = $cpr->patient_id;

//$zRemotePageName = "main/print_fun";

//-----  Get data from remote server -------------------

/* require_once($GLOBALS['fileroot']."/interface/common/functions.inc.php");
include_once($GLOBALS['fileroot']."/interface/printing/Functions.php");
include_once($GLOBALS['fileroot']."/interface/printint/common_functions.php");	
include_once($GLOBALS['fileroot']."/interface/patient_access/common/config.php");	
include_once($GLOBALS['fileroot']."/interface/patient_access/common/functions.php");	
include_once($GLOBALS['fileroot']."/interface/main_functions.php");
require_once($GLOBALS['fileroot']."/interface/admin/chart_more_functions.php");	
require_once($GLOBALS['fileroot']."/interface/chart_notes/fu_functions.php");
require_once($GLOBALS['fileroot']."/interface/chart_notes/common/cl_functions.php"); */

$fdr_pat_img=$GLOBALS['fileroot']."/interface/patient_access/patient_photos/";

$chartNoteId = $_REQUEST['cnId'];

$_GET = array();
$_REQUEST = array();


$_GET['chart_nopro'] = array('Chart Notes');
$_GET['formIdToPrint'] = array($chartNoteId);

$_REQUEST['chart_nopro'] = array('Chart Notes');
$_REQUEST['formIdToPrint'] = array($chartNoteId);

$form_id = $chartNoteId;


$reportName="Visit Notes";
$lenFIds=count($_REQUEST["formIdToPrint"]);

ob_start();
echo "<page backtop=\"5mm\" backbottom=\"5mm\">
<style>
				table{
					margin-bottom:5px;
				}
				
				.width_700{
					width:700px!important;
				}
				
				.width_100{
					width:700px!important;
				}
				
				.tb_headingHeader11{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#C0C0C0;
				}
				
				.text_b_w{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
				}	
				
				.text_lable{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
					font-weight:bold;
					vertical-align:middle;
				}
				.text_value{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:100;
					background-color:#FFFFFF;
				}	
				
				.bdrbtm{
					border-bottom:1px solid #C0C0C0;
					height:20px;	
					vertical-align:baseline;
				}
				.bdrtop{
					border-top:1px solid #C0C0C0;
				}
				.bdrrght{
					border-right:1px solid #C0C0C0;
					vertical-align:baseline;
				}
				
				.bdrlft{
					border-left:1px solid #C0C0C0;
					vertical-align:baseline;
				}
				
				.pd5{
					padding-top:5px;
					padding-bottom:5px;
				}	
				
				.marginBot{
					padding-bottom:5px;
				}
			</style>
";


include($GLOBALS['fileroot']."/interface/patient_info/complete_pt_rec/visionPrintWithNotes_1.php");

//Set timeout
set_time_limit(10);

$zFormId=$chartNoteId;	
$arrDosToPrint = $cpr->print_getDosfromId(array($zFormId));
$strDosToPrint1 = "'".implode("', '", $arrDosToPrint)."'";			
include($GLOBALS['fileroot']."/interface/patient_info/complete_pt_rec/visionPrintWithNotes.php");

//Add Empty Page ---
if($key<$lenFIds-1){ //do not add at end
	echo "<div style=\"height:100%;border:0px solid red;\"></div>";				
}
//Add Empty Page ---

include($GLOBALS['fileroot']."/interface/patient_info/complete_pt_rec/other_print.php");

//Medical History & ROS Data
include($GLOBALS['fileroot']."/interface/patient_info/complete_pt_rec/getMedicalHistoryPrint.php");

echo "</page>";
$patient_workprint_data = ob_get_contents();

ob_end_clean();

$headDataRR = $patient_workprint_data;

$headDataRR =  str_ireplace("../../interface/main/uploaddir/","../../main/uploaddir/",$headDataRR);
$fileName = write_html($headDataRR);
/* $path_pdf_html = $GLOBALS['srcdir'].'/html_to_pdf/pdffile.html';
$fp = fopen($path_pdf_html,'w');
$putData = fputs($fp,$headDataRR);
fclose($fp); */
$ChartNoteImagesStringFinal=implode(",",$ChartNoteImagesString);

//$fileName = 'patient_'.$_SESSION['patient'].'_'.date('H_i_s');
$pathInfo = pathinfo($fileName);
$fileDir = (is_dir($pathInfo['dirname']) === true) ? $pathInfo['dirname'].'/patient_'.$_SESSION['patient'].'_'.date('Y_m_d_H_i_s').'.pdf' : '';

$params = array(
	'page'=>'1.3', 
	'op'=>'P', 
	'font_size'=>'7.5', 
	'saveOption'=>'F', 
	'name'=>$fileName, 
	'file_location'=>$fileName, 
	'pdf_name'=>$fileDir, 
	//'htmlFileName'=>'pdffile', 
	'images'=>$ChartNoteImagesStringFinal 
);


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $GLOBALS['php_server'].'/library/html_to_pdf/createPdf.php?setIgnoreAuth=true');
curl_setopt($ch, CURLOPT_POST, true);	/*Reset HTTP method to GET*/
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); /*Return the response*/
curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP); /*Set protocol to HTTP if default changed*/
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false); /*Include header in Output/Response*/
$data = curl_exec($ch); /*$data will hold data returned from FramesData API*/
/*Close curl session/connection*/
curl_close($ch);

print $fileDir;

unset($_SESSION['asPrint']);
unset($_SESSION['printTestData']);
?>