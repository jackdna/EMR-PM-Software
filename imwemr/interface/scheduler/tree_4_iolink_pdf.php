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
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/Functions.php');
require_once($GLOBALS['fileroot'].'/library/classes/dhtmlgoodies_tree.class.php');
$library_path = $GLOBALS['webroot'].'/library';
$patentId = $_REQUEST['patentId'];
$tree = new dhtmlgoodies_tree();
$tree->addToArray(1,"IOlink PDF(s)",0);
$a = 2;
$getIolinkPatientDirPath = data_path()."iOLink/PatientId_".$patentId;
$getIolinkPatientDirPathWeb = data_path(1)."iOLink/PatientId_".$patentId;
if(is_dir($getIolinkPatientDirPath)){
	if ($handle = opendir($getIolinkPatientDirPath)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				//echo "$file\n";				
				if($file!='OcularHx.pdf') {
					$tree->addToArray($a,$file,1,"iolink_patient_pdf.php?path=$getIolinkPatientDirPathWeb/$file","patientIolinkPdfConsent","pdf-icon");
					$a++;
				}	
			}
	    }
	closedir($handle);
	}
}
$tree->addToArray($a,"Patient Info",0);
$patientifoNodeId = $a;
//////////
//$iolinkScanDoc = "../main/uploaddir";
$iolinkScanDoc = data_path();
$iolinkScanDocWeb = data_path(1);
$iolinkScanDocWeb = substr($iolinkScanDocWeb,0,strlen($iolinkScanDocWeb)-1);

$qryGetPatientInfoScan = imw_query("SELECT id,patient_id,scan_doc_add,mask from surgery_center_patient_scan_docs where patient_id=$patentId and scan_type_folder = 0 ORDER BY created_date desc");
while($rsGetPatientInfoScan = imw_fetch_assoc($qryGetPatientInfoScan))
{
	$patientifoNodeId++;
	$scanDocAdd = $rsGetPatientInfoScan['scan_doc_add'];
	$scanDocId = $rsGetPatientInfoScan['id'];
	$mask = $rsGetPatientInfoScan['mask'];
	$arrScanDocAdd = explode("/",$scanDocAdd);
	if($mask){
		$arrMask = explode('.',$mask);
		$filename = $arrMask[0];
	}
	else{
		$arrFilename = explode('.',$arrScanDocAdd[2]);
		$filename = $arrFilename[0];
	}
	$tree->addToArray($patientifoNodeId,$filename,$a,"iolink_patient_pdf.php?path=$iolinkScanDocWeb$scanDocAdd","patientIolinkPdfConsent","glyphicon-open-file");
}
/////////
$patientifoNodeId++;

$tree->addToArray($patientifoNodeId,"Clinical",0);
$clinicalNodeId = $patientifoNodeId;
//////////
$qryGetPatientInfoScan = imw_query("SELECT id,patient_id,scan_doc_add,mask from surgery_center_patient_scan_docs where patient_id=$patentId and scan_type_folder = 1 ORDER BY created_date desc");
while($rsGetPatientInfoScan = imw_fetch_assoc($qryGetPatientInfoScan))
{
	$clinicalNodeId++;
	$scanDocAdd = $rsGetPatientInfoScan['scan_doc_add'];
	$scanDocId = $rsGetPatientInfoScan['id'];
	$mask = $rsGetPatientInfoScan['mask'];
	$arrScanDocAdd = explode("/",$scanDocAdd);
	if($mask){
		$arrMask = explode('.',$mask);
		$filename = $arrMask[0];
	}
	else{
		$arrFilename = explode('.',$arrScanDocAdd[2]);
		$filename = $arrFilename[0];
	}
	$tree->addToArray($clinicalNodeId,$filename,$patientifoNodeId,"iolink_patient_pdf.php?path=$iolinkScanDocWeb$scanDocAdd","patientIolinkPdfConsent","glyphicon-open-file");
}
/////////
$clinicalNodeId++;
$tree->addToArray($clinicalNodeId,"Health Questionnaire",0);
$healthQuesNodeId = $clinicalNodeId;
//////////
$qryGetPatientInfoScan = imw_query("SELECT id,patient_id,scan_doc_add,mask from surgery_center_patient_scan_docs where patient_id=$patentId and scan_type_folder = 2 ORDER BY created_date desc");
while($rsGetPatientInfoScan = imw_fetch_assoc($qryGetPatientInfoScan))
{
	$healthQuesNodeId++;
	$scanDocAdd = $rsGetPatientInfoScan['scan_doc_add'];
	$scanDocId = $rsGetPatientInfoScan['id'];
	$mask = $rsGetPatientInfoScan['mask'];
	$arrScanDocAdd = explode("/",$scanDocAdd);
	if($mask){
		$arrMask = explode('.',$mask);
		$filename = $arrMask[0];
	}
	else{
		$arrFilename = explode('.',$arrScanDocAdd[2]);
		$filename = $arrFilename[0];
	}
	$tree->addToArray($healthQuesNodeId,$filename,$clinicalNodeId,"iolink_patient_pdf.php?path=$iolinkScanDocWeb$scanDocAdd","patientIolinkPdfConsent","glyphicon-open-file");
}
/////////
$healthQuesNodeId++;

//START CODE FOR OCULAR HX
$tree->addToArray($healthQuesNodeId,"Ocular Hx",0);
$ocularNodeId = $healthQuesNodeId;

//PDF FROM PATIENT-DIRECTORY
if(is_dir($getIolinkPatientDirPath)){
	if ($handle = opendir($getIolinkPatientDirPath)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if($file=='OcularHx.pdf') {
					$ocularNodeId++;
					$tree->addToArray($ocularNodeId,$file,$healthQuesNodeId,"iolink_patient_pdf.php?path=$getIolinkPatientDirPathWeb/$file",'',"pdf-icon");
				}	
			}
		}
		closedir($handle);
	}
}
//PDF FROM PATIENT-DIRECTORY
$qryGetPatientInfoScan = imw_query("SELECT id,patient_id,scan_doc_add,mask from surgery_center_patient_scan_docs where patient_id=$patentId and scan_type_folder = 5 ORDER BY created_date desc");
while($rsGetPatientInfoScan = imw_fetch_assoc($qryGetPatientInfoScan)){
	$ocularNodeId++;
	$scanDocAdd = $rsGetPatientInfoScan['scan_doc_add'];
	$scanDocId = $rsGetPatientInfoScan['id'];
	$mask = $rsGetPatientInfoScan['mask'];
	$arrScanDocAdd = explode("/",$scanDocAdd);
	if($mask){
		$arrMask = explode('.',$mask);
		$filename = $arrMask[0];
	}
	else{
		$arrFilename = explode('.',$arrScanDocAdd[2]);
		$filename = $arrFilename[0];
	}
	$tree->addToArray($ocularNodeId,$filename,$healthQuesNodeId,"iolink_patient_pdf.php?path=$iolinkScanDocWeb$scanDocAdd","patientIolinkPdfConsent","glyphicon-open-file");
}
$ocularNodeId++;
//END CODE FOR OCULAR HX

$tree->addToArray($ocularNodeId,"H&P",0);
$hpNodeId = $ocularNodeId;
//////////
$qryGetPatientInfoScan = imw_query("SELECT id,patient_id,scan_doc_add,mask from surgery_center_patient_scan_docs where patient_id=$patentId and scan_type_folder = 3 ORDER BY created_date desc");
while($rsGetPatientInfoScan = imw_fetch_assoc($qryGetPatientInfoScan)){
	$hpNodeId++;
	$scanDocAdd = $rsGetPatientInfoScan['scan_doc_add'];
	$scanDocId = $rsGetPatientInfoScan['id'];
	$mask = $rsGetPatientInfoScan['mask'];
	$arrScanDocAdd = explode("/",$scanDocAdd);
	if($mask){
		$arrMask = explode('.',$mask);
		$filename = $arrMask[0];
	}
	else{
		$arrFilename = explode('.',$arrScanDocAdd[2]);
		$filename = $arrFilename[0];
	}
	$tree->addToArray($hpNodeId,$filename,$ocularNodeId,"iolink_patient_pdf.php?path=$iolinkScanDocWeb$scanDocAdd","patientIolinkPdfConsent","glyphicon-open-file");
}
/////////
$hpNodeId++;
$tree->addToArray($hpNodeId,"EKG",0);
$ekgNodeId = $hpNodeId;
//////////
$qryGetPatientInfoScan = imw_query("SELECT id,patient_id,scan_doc_add,mask from surgery_center_patient_scan_docs where patient_id=$patentId and scan_type_folder = 4 ORDER BY created_date desc") ;
while($rsGetPatientInfoScan = imw_fetch_assoc($qryGetPatientInfoScan)){
	$ekgNodeId++;
	$scanDocAdd = $rsGetPatientInfoScan['scan_doc_add'];
	$scanDocId = $rsGetPatientInfoScan['id'];
	$mask = $rsGetPatientInfoScan['mask'];
	$arrScanDocAdd = explode("/",$scanDocAdd);
	if($mask){
		$arrMask = explode('.',$mask);
		$filename = $arrMask[0];
	}
	else{
		$arrFilename = explode('.',$arrScanDocAdd[2]);
		$filename = $arrFilename[0];
	}
	$tree->addToArray($ekgNodeId,$filename,$hpNodeId,"iolink_patient_pdf.php?path=$iolinkScanDocWeb$scanDocAdd","patientIolinkPdfConsent","glyphicon-open-file");
}
/////////

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
        <link href="<?php echo $library_path; ?>/css/document.css" rel="stylesheet">
        <link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
        <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
        <script src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script> 
	</head>
    <body>
    <?php
		$tree->writeCSS();
		$tree->writeJavascript();
		$tree->drawTree();
	?>
    </body>
</html>