<?php

ob_start();
include_once(dirname(__FILE__) . "/../../../config/globals.php");
include_once(dirname(__FILE__) . "/class.mur_reports.php");
include(dirname(__FILE__) . "/../../../library/classes/AES.class.php");

$library_path = $GLOBALS['webroot'] . '/library';
$objMUR = new MUR_Reports;

$selectedNQF = explode(',', $_REQUEST['selectedNQF']);

$inc_all_nqf = "yes";
$currentDate = date("Ymd");
$currentDateTime = date("YmdHis");
//$_REQUEST['dtfrom'] = '01-01-2018';
//$_REQUEST['dtupto'] = '12-31-2018';
$dtfrom = $_REQUEST['dtfrom'];
$dtupto = $_REQUEST['dtupto'];
$dtfrom1 = getDateFormatDB($dtfrom);
$dtfrom1Tm = $dtfrom1 ? $dtfrom1.date("His") : "";
$dtupto1 = getDateFormatDB($dtupto);
$dtupto1Tm = $dtupto1 ? $dtupto1.date("His") : "";
if ($_REQUEST['provider'] != "") {
    $pro_id = $_REQUEST['provider'];
} else {
    $pro_id = $_SESSION['authId'];
}
destroy("qrda_xml/qrda_cat3");
$ext_counter = "100";

/* BEGIN CUSTODIAN (FACILITY) DATA */
$facility = "";
$qry = "select * from users where id = '".$pro_id."'";
$res = imw_query($qry);
$row = imw_fetch_assoc($res);
$facility = $row['facility'];
if($facility > 0){
	$qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '".$facility."'";
}else{
	$qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
}
$res_facility = imw_query($qry_facility);
$row_facility = imw_fetch_assoc($res_facility);

//$all_nqf_arr = array("NQF0018", "NQF0022", "NQF0421", "CMS50v2", "NQF0028", "NQF0052", "NQF0055", "NQF0086", "NQF0088", "NQF0089");
$all_nqf_arr=array("NQF0018","NQF0022","NQF0419","NQF0565","NQF0028","NQF0564","NQF0055","NQF0086","NQF0088","NQF0089");
$all_nqf_arr = $selectedNQF;

$filesCreatedArr = array();
$file_path = 'qrda_xml/qrda_cat3/';
foreach($all_nqf_arr as $nqf)
{
	$xmlData = "";
	$measuresArr = array();
	$fileList = 'qrda_cat3_'.strtolower($nqf).'.php';
	if(file_exists($fileList)) {
		
		include($fileList);
		$xmlFileName = 'qrda_cat3_'.strtolower($nqf).'.xml';
		array_push($filesCreatedArr,$xmlFileName);
		file_put_contents($file_path.$xmlFileName,$xmlData);
	}
}

$archive_file_name = 'qrda_cat3.zip';
zipFilesAndDownload($filesCreatedArr, $archive_file_name, $file_path, 'yes');

?>

<?php

//  FUNCTIONS
function destroy($dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $mydir = @opendir($dir);
    while (false !== ($file = readdir($mydir))) {
        if ($file != "." && $file != "..") {
            @unlink($dir . '/' . $file);
        }
    }

    $file_hcfa = 'CDA.xsl';
    $newfile_hcfa = 'qrda_xml/qrda_cat3/CDA.xsl';
    @copy($file_hcfa, $newfile_hcfa);
}

function zipFilesAndDownload($file_names, $archive_file_name, $file_path, $download_status) {

    $zip = new ZipArchive();
    //create the file and throw the error if unsuccessful
    if ($zip->open($file_path . $archive_file_name, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
        exit("cannot open <$archive_file_name>\n");
    }
    //add each files of $file_name array to archive

    foreach ($file_names as $files) {
        $zip->addFile($file_path . $files, $files);
        //echo $file_path.$files."<br>";
    }//die();
    $zip->close();
    //then send the headers to foce download the zip file
    if ($download_status == "yes") {
	ob_end_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=$archive_file_name");
        @readfile("$file_path" . "$archive_file_name") or die("File not found.");
	//echo "<script>location.href='".$file_path.$archive_file_name."';</script>";
	exit;
    }
	
}


?>


