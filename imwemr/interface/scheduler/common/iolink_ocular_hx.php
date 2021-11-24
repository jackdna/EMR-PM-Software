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
Purpose: To get DOS of Ocular History
Access Type: Indirect*/
$css="<style>

table{ width:750px;border-spacing:0;font-size:14px;}

td{
	font-size:14px;	
	font-weight:100;
	background-color:#FFFFFF;
	color:#000000;
	text-align:left;
	vertical-align:top;
	padding:0;
	border-spacing:0;	
	margin:0;
	word-wrap:break-word;
}

.pagebreak {page-break-before:always} 

.text_b_w{
	font-size:14px;	
	font-weight:bold;
	background-color:white;
	color:#000000;
}
.paddingLeft{
	padding-left:5;
}
.paddingTop{
	padding-top:5;
}
.tb_subheading{
	font-size:14px;	
	font-weight:bold;
	color:#000000;
	background-color:#f3f3f3;
}
.tb_heading{
	font-size:14px;	
	font-weight:bold;
	color:#000;
	background-color:#C0C0C0;
	margin-top:10;
	padding:3px 0px 3px 0px;
	vertical-align:middle;
	width:100%;
}
.tb_headingHeader{
	font-size:14px;	
	font-weight:bold;
	color:#FFFFFF;
	background-color:#4684ab;
}
.text_lable{
	font-size:14px;	
	background-color:#FFFFFF;
	font-weight:bold;
}
.text_value{
	font-size:14px;	
	font-weight:100;
	background-color:#FFFFFF;
}
.text_blue{
	font-size:14px;	
	color:#0000CC;
	font-weight:bold;
}
.text_green{
	font-size:14px;	
	color:#006600;
	font-weight:bold;
}
.text_9{
	font-size:14px;
}
.text_9b{
	font-size:14px;
	font-weight:bold;
}
.text_10{
	font-size:14px;
}
.text_10b{
	font-size:14px;
	font-weight:bold;	
}

.imgCon{width:325;height:auto;}
.imgdraw{width:325;height:auto;}

.lbl{width:10%;font-weight:bold; } 
.sum{width:45%;} 

.test td{border:1px solid red;}

.conlen td { font-size:14px; }

#crgp td{ font-size:14px;}

.headtilt, .grid{ width:43%;min-height:150px;text-align:left; }
.grid table{width:100%;height:95%;border-spacing:0;border-collapse: collapse;margin-top:3px; }
.grid table td{border-right:4px solid black;border-bottom:4px solid black; width:33%; text-align:center; height:20px;}
.border{
	border:1px solid #C0C0C0;
}
.bdrbtm{
	border-bottom:1px solid #C0C0C0;
	height:13px;	
	vertical-align:top;
	padding-top:2px;
	padding-left:3px;
}

.pdl5{
	padding-left:10px;
		
}

.width_700{ width:700px;}
table {
	width: 100%;
	border-spacing: 0;
}
td {
 font-size:14;
	/*font-family:Arial, Helvetica, sans-serif;*/
	font-weight: 100;
	background-color: #FFFFFF;
	color: #000000;
	text-align: left;
	vertical-align: top;
	padding: 0;
	border-spacing: 0;
	margin: 0;
	word-wrap: break-word;
}
.pagebreak {
	page-break-before: always
}
.text_b_w {
 font-size:14;
	/*font-family:Arial, Helvetica, sans-serif;*/
	font-weight: bold;
	background-color: white;
	color: #000000;
}
.paddingLeft {
	padding-left: 5;
}
.paddingTop {
	padding-top: 5;
}

.tb_heading {
 font-size:14;
	/*font-family:Arial, Helvetica, sans-serif;*/
	font-weight: bold;
	color: #000;
	background-color: #C0C0C0;
	margin-top: 10;
	padding: 3px 0px 3px 0px;
	vertical-align: middle;
	width: 100%;
}
.tb_headingHeader {
 font-size:14;
	/*font-family:Arial, Helvetica, sans-serif;*/
	font-weight: bold;
	color: #FFFFFF;
	background-color: #4684ab;
}
.text_lable {
 font-size:14;
	/*font-family:Arial, Helvetica, sans-serif;*/
	background-color: #FFFFFF;
	font-weight: bold;
}
.text_value {
 font-size:14;
	/*font-family:Arial, Helvetica, sans-serif;*/
	font-weight: 100;
	background-color: #FFFFFF;
}
.text_blue {
 font-size:14;
	/*font-family:Arial, Helvetica, sans-serif;*/
	color: #0000CC;
	font-weight: bold;
}
.text_green {
 font-size:14;
	/*font-family:Arial, Helvetica, sans-serif;*/
	color: #006600;
	font-weight: bold;
}
.text_purple {
 font-size:14;
	/*font-family:Arial, Helvetica, sans-serif;*/
	color: purple;
	font-weight: bold;
}
.imgCon {
	width: 325;
	height: auto;
}
.imgdraw {
	width: 325;
	height: auto;
}
.lbl {
	width: 10%;
	font-weight: bold;
}
.sum {
	width: 45%;
}
.test td {
	border: 1px solid red;
}
.conlen td {
font-size:12;
}
#crgp td {
font-size:9;
}
.headtilt, .grid {
	width: 43%;
	min-height: 150px;
	text-align: left;
}
.grid table {
	width: 100%;
	height: 95%;
	border-spacing: 0;
	border-collapse: collapse;
	margin-top: 3px;
}
.grid table td {
	border-right: 4px solid black;
	border-bottom: 4px solid black;
	width: 33%;
	text-align: center;
	height: 20px;
}
.border {
	border: 1px solid #C0C0C0;
}
.bdrbtm {
	border-bottom: 1px solid #C0C0C0;
	height: 13px;
	vertical-align: top;
	padding-top: 2px;
	padding-left: 3px;
}
.bdrtop {
	border-top: 1px solid #C0C0C0;
	height: 15px;
	vertical-align: top;
}
.pdl5 {
	padding-left: 10px;
}
.bdrright {
	border-right: 1px solid #C0C0C0;
}
</style>";

	
//include_once("../../main/main_functions.php");
$strPtDtQry="SELECT pd.*,date_format(pd.date,'%m-%d-%Y') as createdDate,
									date_format(pd.DOB,'%m-%d-%Y') as ptDOB,
									DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(dob, '00-%m-%d')) AS age,
									sa.sa_app_start_date,sa.sa_patient_id
									FROM schedule_appointments sa
									LEFT JOIN patient_data pd ON pd.pid = sa.sa_patient_id											
									WHERE sa.id = ".$schedule_id;									
$strPtDtRes = imw_query($strPtDtQry) or die(imw_error());
$strPtDtNumRow = imw_num_rows($strPtDtRes);
$form_id_from_iolink='';
if($strPtDtNumRow>0) {
	$strPtDtRow = imw_fetch_array($strPtDtRes);	
	$pid = $strPtDtRow['pid'];
	$patient_id_from_iolink = $strPtDtRow['pid'];
	$sa_app_start_date = $strPtDtRow['sa_app_start_date'];	
	
}

if($_REQUEST['iolink_ocular_hx_form_id']) {
	$form_id = $_REQUEST['iolink_ocular_hx_form_id'];
	$form_id_from_iolink = $_REQUEST['iolink_ocular_hx_form_id'];
}

$patientDirOcular = "/PatientId_".$pid;
$qry =imw_query("select *	from patient_data where id = '".$pid."'");
$patientDetails = imw_fetch_assoc($qry);
$patientName = $patientDetails['lname'].', '.$patientDetails['fname'].' ';
$patientName .= $patientDetails['mname'];

$qry1 = imw_query("select default_group from facility where facility_type = 1");
$facilityDetail = imw_fetch_assoc($qry1);
if(imw_num_rows($qry1)>0){
	$gro_id = $facilityDetail['default_group'];
	$qry = "select * from groups_new where gro_id = '$gro_id'";
	$groupDetails = get_array_records_query($qry);
}

$age =show_age($patientDetails['DOB']) ;//date('Y') - $y ;

function imageResize_ocular($width, $height, $target) {
	if ($width > $height) {
		$percentage = ($target / $width);
	} else {
		$percentage = ($target / $height);
	}
	$width = round($width * $percentage);
	$height = round($height * $percentage);
	return "width=\"$width\" height=\"$height\"";
}

$toMakePdfFor = "Iolink";
//$img_path_ocular = '../../main/uploaddir';
$img_path_ocular = substr(data_path().'iOLink/', 0, -1);


include_once($GLOBALS['fileroot'].'/interface/chart_notes/vision-test-print-h.php');

$patientPrintData = $patient_workprint_data;

//$fp = $img_path_ocular.'/pdfFile.html';
//$putData = file_put_contents($fp,$css.$patientPrintData);
//fclose($fp);
$rand=rand(0,500);
$htmlFlName = 'OcularHx_'.$_SESSION['authId'].'_'.$rand;
file_put_contents(data_path().'iOLink/'.$htmlFlName.'.html',$css.$patientPrintData);

$patientPrintData="";
$iolinkDirPath = $img_path_ocular;//.'/addons/iOLink';	
//$patientDir = "/PatientId_".$pid;
$patientDir = $patientDirOcular;
//Create patient directory
if(!is_dir($iolinkDirPath.$patientDir)){		
	mkdir($iolinkDirPath.$patientDir, 0755, true);
	chown($iolinkDirPath.$patientDir, 'apache');
}
$pdfFileName = 'OcularHx.pdf';
$pdfFilePath = urldecode($iolinkDirPath.$patientDir.'/'.$pdfFileName);
$arrProtocol = (explode("/",$_SERVER['SERVER_PROTOCOL']));
$arrPathPart = pathinfo($_SERVER['PHP_SELF']);
$arrPathPart = explode("/",($arrPathPart['dirname']));

$dir = explode('/',$_SERVER['HTTP_REFERER']);
$httpPro = $dir[0];
$httpHost = $dir[2];
$httpfolder = $dir[3];
$ip = $_SERVER['REMOTE_ADDR'];
		
$myHTTPAddress = $httpPro.'//'.$myInternalIP.'/'.$web_RootDirectoryName.'/library/html_to_pdf/iolinkMakePdf.php';
$data1 = "";
if($form_id_from_iolink) {
	$curNew = curl_init();
	//$urlPdfFile = $myHTTPAddress."??op=l&onePage=false&name=pdfFile&pdf_name=".$pdfFileName."&images=".$ChartNoteImagesStringFinal."";
	$urlPdfFile = $myHTTPAddress."?copyPathIolink=$pdfFilePath&pdf_name=$pdfFilePath&name=$htmlFlName";
	//echo $urlPdfFile;
	curl_setopt($curNew,CURLOPT_URL,$urlPdfFile);
	curl_setopt ($curNew, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($curNew, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
	$data1 = curl_exec($curNew);
	//print_r(curl_getinfo($curNew));
	curl_close($curNew);
}
if($form_id_from_iolink) {
	$chrtAssPlnQry = "UPDATE chart_assessment_plans SET surgical_ocular_hx_sent_dos = '".$sa_app_start_date."', surgical_ocular_hx = '1' WHERE form_id = '".$form_id_from_iolink."' AND form_id!='0' AND patient_id = '".$pid."' AND patient_id != '0' ";
	$chrtAssPlnRes = imw_query($chrtAssPlnQry);

	$schOcularHxQry = "UPDATE schedule_appointments SET iolink_ocular_chart_form_id = '".$form_id_from_iolink."',iolink_ocular_chart_sent_date = '".date("Y-m-d H:i:s")."' WHERE id = '".$schedule_id."'";
	$schOcularHxRes = imw_query($schOcularHxQry);
}
?>