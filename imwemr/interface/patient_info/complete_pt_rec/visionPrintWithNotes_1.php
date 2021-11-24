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
File: visionPrintWithNotes1.php
Purpose: This file provides initial header for printing pdf.
Access Type : Include file
*/
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i")." GMT");  // HTTP 1.1
header("Cache-Control: no-store, no-cache, must-revalidate"); // ////////////////////////
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");

include_once("../../../config/globals.php");
include_once($GLOBALS['srcdir'].'/classes/work_view/wv_functions.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/ChartTemp.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/ChartNote.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/ChartAP.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/User.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/CLSImageManipulation.php');
include_once($GLOBALS['srcdir']."/classes/SaveFile.php");
include_once($GLOBALS['fileroot'].'/interface/chart_notes/chart_globals.php');


/*
$objImageManipulation = new CLSImageManipulation();
$objectSaveFile=new SaveFile;
*/
$pid = $patient_id = $_SESSION['patient'];

global $cpr;

$patientDetailsNew = $cpr->get_pt_data($patient_id);
$yesNo = "no";

//Get Pt. Image
if($patientDetailsNew['p_imagename']){
	$pt_images = $cpr->get_pt_images($patientDetailsNew['p_imagename']);
	$patient_img = $pt_images['patient_img'];		//Array 
	$patientImage = $pt_images['patientImage'];		//Single img
	$ChartNoteImagesString = $pt_images['ChartNoteImagesString'];	
}

/**

If they select a Visit note date then it should simply print the problem, Medication and Allergies list of that visit.   basically what is select is what it should print for those visit.  
For example if the date selected is 10-06-2012  and problem, medication and allergies list select then it should print these items for that DOS and 
if multiple DOS is selected then their correspond problem, medication and allergies should be printed.  
If All I selected and Chart notes is not selected and problem, medication and allergies is selected then print the problem, medication and allergies for all visits.  
Basically what is select is what correspondingly should be printed.

**/

$arrDosToPrint = array();
$strDosToPrint="";

//GET DOS from FormIds ------------
if(isset($_REQUEST["formIdToPrint"]) && count($_REQUEST["formIdToPrint"])>0){		
	$arrDosToPrint = $cpr->print_getDosfromId($_REQUEST["formIdToPrint"]);
}
if(isset($_REQUEST["chart_nopro"]) && in_array("All",$_REQUEST["chart_nopro"]) && !in_array("Chart Notes",$_REQUEST["chart_nopro"])){	
	$arrDosToPrint = array();
}

if(count($arrDosToPrint)>0){
	$strDosToPrint = "'".implode("', '", $arrDosToPrint)."'";
}

//Style
$cpr->print_getStyle(2);

//footer
$cpr->print_getPageFooter();
?>