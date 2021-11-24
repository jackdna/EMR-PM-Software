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
File: surgery_consent_patient_scan.php
Purpose: Show patient surgery consent scan
Access Type: Include 
*/	
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache"); 
include_once("../../../config/globals.php");
$library_path = $GLOBALS['webroot'].'/library';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;


$userauthorized 	 	= $_SESSION['authId'];
$patient_id			 	= $_SESSION['patient'];
$package_category_id 	= $_REQUEST["package_category_id"];
$form_information_id 	= $_REQUEST["form_information_id"];
$db_form_created_date	= $_REQUEST["db_form_created_date"];
$consent_form_id 	 	= $_REQUEST["consent_form_id"];
$form_information_id	= $_REQUEST["form_information_id"];

$txtConsentFaxPdfName	= $_REQUEST["txtConsentFaxPdfName"];
$faxname				= $_REQUEST["txtConsentFaxName"];
$faxnumber				= $_REQUEST["txtConsentFaxNo"];
$faxnumber = preg_replace('/[^0-9+]/', "", $faxnumber);
// $faxnumber				= str_ireplace("-","",$faxnumber);

$show_fax_popup 	 	= $_REQUEST["show_fax_popup"];
$send_fax 	 		 	= $_REQUEST["send_fax"];

$consent_fax_fld_path	= "";
if($send_fax =='yes') {
	//include_once('../../main/Functions.php');
	require_once('../../../library/bar_code/code128/code128.class.php');
	//include_once(dirname(__FILE__)."/../../chart_notes/progress_notes/pnTempParser.php");
	require_once(dirname(__FILE__)."/../../../library/classes/work_view/ChartAP.php");
	require_once(dirname(__FILE__)."/../../../library/classes/work_view/PnTempParser.php");
	//include_once("../../common/new_html2pdf/html2pdf.class.php");
	// require_once("../../../library/html_to_pdf/html2pdf.class.php");

	$faxContent = "";
	$andFaxConsentQry = " AND pcfi.package_category_id = '".$package_category_id."' AND (DATE_FORMAT(pcfi.form_created_date, '%Y-%m-%d')= '".$db_form_created_date."') ";
	if($form_information_id) {
		$andFaxConsentQry = " AND pcfi.form_information_id = '".$form_information_id."' ";
	}
	$pckQry = "SELECT pcfi.*, pd.fname AS pt_fname, pd.mname AS pt_mname, pd.lname AS pt_lname 
			 	FROM `patient_consent_form_information` pcfi 
			   	INNER JOIN patient_data pd ON (pd.pid = pcfi.patient_id AND pd.pid = '".$_SESSION['patient']."')
			  	WHERE pcfi.patient_id ='".$_SESSION['patient']."' ".$andFaxConsentQry;
	$pckRes = imw_query($pckQry);
	$sendFaxLogQryArr = array();
	if(imw_num_rows($pckRes)>0) {
		while($pckRow = imw_fetch_assoc($pckRes)) {
			$consent_form_id 	 = $pckRow["consent_form_id"];
			$form_information_id = $pckRow["form_information_id"];
			$sectionName = "Consent Form";
			if(trim($pckRow["package_category_id"])) {
				$sectionName = "Consent Package";	
			}
			
			$patName = $pckRow['pt_lname'].', ';
			$patName .= $pckRow['pt_fname'].' ';
			$patName .= $pckRow['pt_mname'];
			$patName = ucwords(trim($patName));
			if($patName[0] == ','){
				$patName = substr($patName,1);
			}
			
			//START CODE TO ADD LOG OF FAX
			$sendFaxLogQryArr[$form_information_id] = "INSERT INTO `send_fax_log_tbl` SET 
									patient_id 		= 	'".$_SESSION['patient']."',
								  	section_name 	= 	'".str_replace(' ', '_', strtolower($sectionName))."',
									section_pk_id 	= 	'".$form_information_id."',
									template_id 	= 	'".$consent_form_id."',
									template_name	=	'".$pckRow["consent_form_name"]."',
									folder_date 	=	'".$pckRow["form_created_date"]."',
									operator_id		=	'".$_SESSION['authId']."'";
			//END CODE TO ADD LOG OF FAX
			
			include("print_consent_form.php");
			$faxContent .= $consent_form_content;
		}
		$consent_fax_fld_path = $fld_path; //FROM print_consent_form.php
	}
	
	//START CODE TO CREATE PDF
	$log_file_name = '';
	if(trim($faxContent)) {
		$htmlFolderFax = $htmlFolder; //FROM print_consent_form.php
		$faxContent = str_ireplace('src="../../data/'.constant("PRACTICE_PATH").'/','src="'.data_path(),$faxContent);
		$path_set_fax = '';
		
		if($txtConsentFaxPdfName) {
			if(!file_exists($consent_fax_fld_path.'consent_form_fax')){
				mkdir($consent_fax_fld_path.'consent_form_fax');
			}
			$html_file_name_fax='consent_form_fax/'.$txtConsentFaxPdfName;
			$path_set_fax = $consent_fax_fld_path.$html_file_name_fax.'.html';
		}
		//$fp_fax = fopen($path_set_fax,'w');
		//$write_data_fax = fwrite($fp_fax,utf8_decode(html_entity_decode($faxContent)));
		
		//$savePdfFilePath = $webserver_root.'/interface/common/'.$htmlFolderFax;
		$savePdfFilePath = substr(data_path(), 0, -1);
		$savePdfFileName = $html_file_name_fax.'.pdf';
		if(file_exists($savePdfFilePath.'/'.$savePdfFileName)) {
			unlink($savePdfFilePath.'/'.$savePdfFileName);	
		}

		try {
		    $op = 'P';
		    $html2pdf = new Html2Pdf($op,'A4','en');
		    $html2pdf->setTestTdInOnePage(false);
		    // if($onePage=="false") {$html2pdf->setTestTdInOnePage(false); }
		    //$html2pdf->pdf->SetDisplayMode('fullpage');
			$html2pdf->writeHTML(utf8_decode(html_entity_decode($faxContent)), isset($_REQUEST['vuehtml']));
		    //$html2pdf->createIndex('Sommaire', 30, 12, false, true, 2, null, '10mm');
			$newFileName=$html2pdf->output($savePdfFilePath.'/'.$savePdfFileName, 'F');
		} catch (Html2PdfException $e) {
			$html2pdf->clean();
			//echo "Error while creating a PDF";
			$formatter = new ExceptionFormatter($e);
			echo $formatter->getHtmlMessage();
		}

		// $op = 'p';
		// $html2pdf = new Html2Pdf($op,'A4','en');
		// // $html2pdf = new HTML2PDF($op,'A4','en');
		// $html2pdf->setTestTdInOnePage(false);
		// $html2pdf->WriteHTML(utf8_decode(html_entity_decode($faxContent)), isset($_GET['vuehtml']));
		// $html2pdf->Output($savePdfFilePath.'/'.$savePdfFileName,false);
		
		$log_file_name = $_SESSION['authId'].'_'.date('Ymdhis').'.pdf';
		copy_file_new($savePdfFilePath, $savePdfFilePath."/PatientId_".$_SESSION['patient']."/fax_log", $savePdfFileName, $log_file_name);
	}
	//END CODE TO CREATE PDF
	
	//START CODE TO SEND FAX
	if( is_updox('fax') ){
		
		include($GLOBALS['srcdir'].'/updox/updoxFax.php');
		
		$pdfData = base64_encode(file_get_contents($savePdfFilePath.'/'.$savePdfFileName));
		$updox = new updoxFax();
		$resp  = $updox->sendFax($faxname, $faxnumber, $pdfData);
	}
	elseif( is_interfax() ){
		
		if(!($fp = fopen($savePdfFilePath.'/'.$savePdfFileName, "r"))){
			$resp['statusCode'] = '0';
			$resp['message'] = "Error opening PDF file.";
			goto logfield;
		}
		
		$filetype = pathinfo($savePdfFilePath.'/'.$savePdfFileName, PATHINFO_EXTENSION);
		
		$pdfContent = "";
		while(!feof($fp)) $pdfContent .= fread($fp,1024);
		fclose($fp);
		
		$patient_id_str	= "Patient ID ".$_SESSION["patient"];
		if($patName) {
			$patient_id_str	= $patName.' - '.$_SESSION['patient'];	
		}
		
		
		$client = new SoapClient("http://ws.interfax.net/dfs.asmx?WSDL");
		
		$params->Username  			= fax_username;
		$params->Password  			= fax_password;
		$params->FaxNumbers 		= $faxnumber;
		$params->FilesData  		= $pdfContent;
		$params->FileTypes  		= $filetype;
		$params->FileSizes   		= strlen($pdfContent);
		$params->Postpone   		= "2005-04-25T20:31:00-04:00";
		$params->IsHighResolution   = "0";
		$params->CSID   			= "";
		$params->Subject   			= $patient_id_str;
		$params->ReplyAddress 		= "";

		$result = $client->SendFaxEx($params);
		$returnMsg=$result->SendfaxExResult; // returns the transactionID if successful
		
		if($returnMsg>0){
			$resp['status'] = 'success';
			$resp['data']->faxId = $returnMsg;
		}
		elseif($returnMsg=='-1003'){
			$resp['statusCode'] = $returnMsg;
			$resp['message'] = "Authentication error";
		}
		elseif($returnMsg=='-112'){
			$resp['statusCode'] = $returnMsg;
			$resp['message'] = "No valid recipients added or missing fax number or attempting to fax to a number that is not the designated fax number in a developer account.";
		}
		else{
			$resp['statusCode'] = $returnMsg;
			$resp['message'] = "Fax sending failed.";
		}
	}
	
	logfield:
	
	if($resp['status']=='success'){
		echo("Transaction_No. ".($resp['data']->faxId)." - Fax successfully sent");
		if($_SESSION['patient']){
			foreach($sendFaxLogQryArr as $key=>$sendFaxLogQry) {
				if(trim($sendFaxLogQry)) {
					$sendFaxLogQry = $sendFaxLogQry.", updox_id='".$resp['data']->faxId."',`updox_status`='queued', `fax_type`='Primary', `file_name`='".$log_file_name."', `fax_number`='".$faxnumber."'";
					$sendFaxLogRes = imw_query($sendFaxLogQry);
					
					/*Update Fax sent status in consent_form table*/
					$sql_fax_status = 'UPDATE `patient_consent_form_information` SET `fax_status`=1 WHERE `form_information_id`='.$key;
					imw_query($sql_fax_status);
				}
			}
		}
	}
	else{
		echo("Error No. ".$resp['statusCode']." - ".$resp['message']);
	}
	//END CODE TO SEND FAX	
	exit;
}
?>

<html>
<head>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>

<script type="text/javascript">
	if(typeof(top.btn_show)!='undefined') {
		top.btn_show('');
	}
	var show_fax_popup = '<?php echo $show_fax_popup;?>';
	var db_form_created_date = '<?php echo $db_form_created_date;?>';
	var package_category_id = '<?php echo $package_category_id;?>';
	var form_information_id = '<?php echo $form_information_id;?>';
	if(package_category_id) {
		parent.$('#modal_title').html("Send Fax - Consent Package");
		form_information_id = '';
	}
	if(show_fax_popup == 'yes') {
		
		if(parent.document.getElementById("send_fax_div")) {
			parent.$('#send_fax_div').modal("show");
		}
		if(parent.document.getElementById("send_fax_number")) {
			parent.document.getElementById("send_fax_number").value = '';
		}
		if(parent.document.getElementById("db_form_created_date")) {
			parent.document.getElementById("db_form_created_date").value = db_form_created_date;
		}
		if(parent.document.getElementById("package_category_id")) {
			parent.document.getElementById("package_category_id").value = package_category_id;
		}
		if(parent.document.getElementById("form_information_id")) {
			parent.document.getElementById("form_information_id").value = form_information_id;
		}
	}
	function unloadFax() {
		if(parent.document.getElementById("send_fax_div")) {
			parent.document.getElementById("send_fax_div").style.display = 'none';	
		}
	}
</script>
<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap.css" type="text/css">
<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-select.css" type="text/css">
<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
<link href="<?php echo $library_path; ?>/css/style.css" rel="stylesheet">
	
</head>
<body onUnload="javascript:unloadFax();" bgcolor="#ffffff" >


<!--<table width="100%" border="0" cellspacing="0" cellpadding="0">  
<tr>            
	<td>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td colspan="3" align="left" valign="top">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr height="25px">
							<td width="6px" valign="bottom"><img src="../../../images/lt_conrer1.gif"></td>
							<td width="4%" valign="bottom" background="../../../images/rt_conrer1.gif">
							&nbsp;<img src="../../../images/win_demo.gif" align="bottom">									
							</td>
							<td width="100%" class="text_10" valign="middle" background="../../../images/menu_bg.gif">
								
								<?php
								$faxTitle = "Fax Consent Form";	
								if($package_category_id) { $faxTitle = "Fax Consent Package";	}
								?>
                                <b><?php echo $faxTitle; ?></b>
							</td>
							<td width="6px" valign="bottom"><img src="../../../images/rt_conrer1.gif"></td>
						</tr>
					</table>	
				</td>
			</tr>
		</table>
	   </td>
	</tr>
	<tr>
		<td align="center">&nbsp;</td>
	</tr>
</table>-->



<script>
//Btn ---
//top.btn_show();

//Btn ---
</script>

</body>
</html>