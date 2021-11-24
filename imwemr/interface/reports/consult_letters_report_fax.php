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
FILE : CONSULT_LETTER_REPORT_FAX.PHP
PURPOSE :  FAX CONSULT LETTER REPORT PDF
ACCESS TYPE : DIRECT
*/
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Always modified
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");  
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP 1.1
header("Cache-Control: post-check=0, pre-check=0", false); // HTTP 1.0header("Pragma: no-cache");
header("Cache-control: private, no-cache"); 
header("Pragma: no-cache");
include_once(dirname(__FILE__)."/../../config/globals.php");

$phy = $_REQUEST['phyname'];
$phycc1 = $_REQUEST['phycc1name'];
$phycc2 = $_REQUEST['phycc2name'];
$phycc3 = $_REQUEST['phycc3name'];

$faxnumber         	= $_REQUEST['send_fax_number'];
$faxnumberCc1       = $_REQUEST['txtFaxNoCc1'];
$faxnumberCc2       = $_REQUEST['txtFaxNoCc2'];
$faxnumberCc3       = $_REQUEST['txtFaxNoCc3'];
$txtFaxPdfName     	= str_replace('.html','',pathinfo($_REQUEST['txtFaxPdfName'], PATHINFO_BASENAME));
$txtFaxHtmlName     	= $_REQUEST['txtFaxHtmlName'];
$consult_letter_id	= $_REQUEST['consult_letter_ids'];
$consult_mode		= $_REQUEST['hidd_consult_mod'];
$pcp_id				= $_REQUEST['pcp_id'];
$returnStatus = ($consult_mode == 'consult_fax') ? '0@@' : '';

$faxnumber		= preg_replace('/[^0-9+]/', "", $faxnumber);
$faxnumberCc1	= preg_replace('/[^0-9+]/', "", $faxnumberCc1);
$faxnumberCc2	= preg_replace('/[^0-9+]/', "", $faxnumberCc2);
$faxnumberCc3	= preg_replace('/[^0-9+]/', "", $faxnumberCc3);

//$txtFaxPdfName="faxConsultLetterReportPdf";
$fileBasePath = data_path().'UserId_'.$_SESSION['authId'].'/tmp';
$filename= $fileBasePath.'/'.$txtFaxPdfName.'.pdf';

$log_file_name = $_SESSION['authId'].'_'.date('Ymdhis').'.pdf';
copy_file_new($fileBasePath, '../main/uploaddir/PatientId_'.$_SESSION['patient'].'/fax_log', $txtFaxPdfName.'.pdf', $log_file_name);

$responseData = array('primary'=>array(), 'cc1'=>array(), 'cc2'=>array(), 'cc3'=>array());


if( is_updox('fax') ){
	
	$pdfContent = base64_encode(file_get_contents($fileBasePath.'/'.$txtFaxPdfName.'.pdf'));
	//=================UPDOX FAX WORKS STARTS HERE======================
	include($GLOBALS['srcdir'].'/updox/updoxFax.php');  //UPDOX LIBRAY FILE
	$updox = new updoxFax();  //UPDOX OBJECT
	
	//========PRIMARY RECIPENT WORK=========
	$resp  = $updox->sendFax($phy, $faxnumber, $pdfContent);
	if($resp['status']=='success')
		$responseData['primary']['fax_id']=$resp['data']->faxId;
	else
		$responseData['primary']['error']=$resp['message'];

	//code start to send fax to Cc1, Cc2, Cc3	
	if($faxnumberCc1) {
		$resp  = $updox->sendFax($phycc1, $faxnumberCc1, $pdfContent);
		if($resp['status']=='success')
			$responseData['cc1']['fax_id']=$resp['data']->faxId;
		else
			$responseData['cc1']['error']=$resp['message'];
	}
	if($faxnumberCc2) {
		$resp  = $updox->sendFax($phycc2, $faxnumberCc2, $pdfContent);
		if($resp['status']=='success')
			$responseData['cc2']['fax_id']=$resp['data']->faxId;
		else
			$responseData['cc2']['error']=$resp['message'];
	}
	if($faxnumberCc3) {
		$resp  = $updox->sendFax($phycc3, $faxnumberCc3, $pdfContent);
		if($resp['status']=='success')
			$responseData['cc3']['fax_id']=$resp['data']->faxId;
		else
			$responseData['cc3']['error']=$resp['message'];
	}
	//code end to send fax to Cc1, Cc2, Cc3		
}
elseif( is_interfax() ){
	
	if(!($fp = fopen($filename, "r"))){
		$responseData['primary']['error'] = "Error opening PDF file";
		goto logfield;
	}
	
	$filetype = pathinfo($filename, PATHINFO_EXTENSION);
	
	$pdfContent = "";
	while(!feof($fp)) $pdfContent .= fread($fp,1024);
	fclose($fp);
	
	$client = new SoapClient("http://ws.interfax.net/dfs.asmx?WSDL");
	
	//START CODE GET PATIENT ID(s)
	$patient_id = '';
	$patient_id_str = "";
	if(trim($consult_letter_id)) {
		$ptQry = "SELECT distinct(patient_id) as patient_id FROM patient_consult_letter_tbl WHERE patient_consult_id IN (".$consult_letter_id.") ";	
		$ptRes = imw_query($ptQry);
		if(imw_num_rows($ptRes) == 1){//SEND PATIENT-ID IN FAX IF ONLY ONE PATIENT IS SELECTED
			$ptRow = imw_fetch_assoc($ptRes);
			$patient_id = $ptRow["patient_id"];
			//$patient_id = str_ireplace(",",", ",$patient_id);
		}
	}

	if($patient_id) {
		$patient_id_str	= "Patient ID ".$patient_id;
	}
	//END CODE GET PATIENT ID(s)
	
	
	if($_REQUEST['send_fax_subject']){$patient_id_str=$_REQUEST['send_fax_subject'];}
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
		$responseData['primary']['fax_id'] = $returnMsg;
		$responseData['primary']['no'] = $faxnumber;
	}
	elseif($returnMsg=='-1003')
		$responseData['primary']['error'] = "Error No. ".$returnMsg." - Authentication error";
	elseif($returnMsg=='-112')
		$responseData['primary']['error'] = "Error No. ".$returnMsg." - No valid recipients added or missing fax number or attempting to fax to a number that is not the designated fax number in a developer account.";
	else
		$responseData['primary']['error'] = "Error No. ".$returnMsg;
	
	
	//code start to send fax to Cc1, Cc2, Cc3	
	if($faxnumberCc1) {
		$params->FaxNumbers = $faxnumberCc1;
		$result = $client->SendFaxEx($params);
		$returnMsg=$result->SendfaxExResult;
		
		if($returnMsg>0){
			$responseData['cc1']['fax_id'] = $returnMsg;
			$responseData['cc1']['no'] = $faxnumber;
		}
		elseif($returnMsg=='-1003')
			$responseData['cc1']['error'] = "Error No. ".$returnMsg." - Authentication error";
		elseif($returnMsg=='-112')
			$responseData['cc1']['error'] = "Error No. ".$returnMsg." - No valid recipients added or missing fax number or attempting to fax to a number that is not the designated fax number in a developer account.";
		else
			$responseData['cc1']['error'] = "Error No. ".$returnMsg;
		
	}
	if($faxnumberCc2) {
		$params->FaxNumbers = $faxnumberCc2;
		$result = $client->SendFaxEx($params);
		$returnMsg=$result->SendfaxExResult; 
		
		if($returnMsg>0){
			$responseData['cc2']['fax_id'] = $returnMsg;
			$responseData['cc2']['no'] = $faxnumber;
		}
		elseif($returnMsg=='-1003')
			$responseData['cc2']['error'] = "Error No. ".$returnMsg." - Authentication error";
		elseif($returnMsg=='-112')
			$responseData['cc2']['error'] = "Error No. ".$returnMsg." - No valid recipients added or missing fax number or attempting to fax to a number that is not the designated fax number in a developer account.";
		else
			$responseData['cc2']['error'] = "Error No. ".$returnMsg;
	}
	if($faxnumberCc3) {
		$params->FaxNumbers = $faxnumberCc3;
		$result = $client->SendFaxEx($params);
		$returnMsg=$result->SendfaxExResult;
		
		if($returnMsg>0){
			$responseData['cc3']['fax_id'] = $returnMsg;
			$responseData['cc3']['no'] = $faxnumber;
		}
		elseif($returnMsg=='-1003')
			$responseData['cc3']['error'] = "Error No. ".$returnMsg." - Authentication error";
		elseif($returnMsg=='-112')
			$responseData['cc3']['error'] = "Error No. ".$returnMsg." - No valid recipients added or missing fax number or attempting to fax to a number that is not the designated fax number in a developer account.";
		else
			$responseData['cc3']['error'] = "Error No. ".$returnMsg;
	}
}
logfield:

if(file_exists("../common/new_html2pdf/".$txtFaxPdfName.".pdf")) {
	
	unlink("../common/new_html2pdf/".$txtFaxPdfName.".pdf");
	
	if(file_exists("../common/new_html2pdf/".$txtFaxHtmlName.".html")) {
		unlink("../common/new_html2pdf/".$txtFaxHtmlName.".html");
	}
}

if(
	$responseData['primary']['fax_id'] && !empty($responseData['primary']['fax_id'])
){
	//$getMsg="Transaction_No. ".$returnMsg." - Fax successfully sent";
	if($consult_letter_id){
		if($consult_mode == 'consult_fax')
		{
			$pcp_id_fax=$_REQUEST['pcp_id'];
			$qry_update_template="UPDATE patient_consult_letter_tbl set fax_status='1', clinical_fax_status='1', clinical_fax_sent_count = clinical_fax_sent_count+1, clinical_fax_sent_date='".date('Y-m-d')."',clinical_fax_pcp_id='".$pcp_id_fax."', clinical_fax_number='".$faxnumber."', clinical_fax_trans_no = '".$returnMsg."' where patient_consult_id IN(".$consult_letter_id.")";
			$returnStatus = '1@@'; 
			
		
			$qry_get_data="SELECT patient_consult_id, patient_id, templateId, templateName, operator_id, status FROM `patient_consult_letter_tbl` WHERE patient_consult_id IN(".$consult_letter_id.")";
			$qry_get_data_res = imw_query($qry_get_data);
			
			if(imw_num_rows($qry_get_data_res)>0){
				
				while($qry_get_data_row = imw_fetch_assoc($qry_get_data_res)){
				
					$qry_insrt_sendfaxlog ="INSERT INTO `send_fax_log_tbl` SET patient_consult_id='".$qry_get_data_row['patient_consult_id']."', patient_id='".$qry_get_data_row['patient_id']."', template_id='".$qry_get_data_row['templateId']."', template_name='".$qry_get_data_row['templateName']."', folder_date ='".date('Y-m-d')."', operator_id='".$qry_get_data_row['operator_id']."',  status='".$qry_get_data_row['status']."', `section_name`='report', `updox_id`='".$responseData['primary']['fax_id']."', `updox_status`='queued', `fax_type`='Primary', `file_name`='".$log_file_name."', `fax_number`='".$faxnumber."'";
				
					$sendfaxlog_row =imw_query($qry_insrt_sendfaxlog);
				}
			}
		}
		else
		{
			$ref_phy_id_fax=$_REQUEST['ref_phy_id'];
            if($ref_phy_id_fax!='' && ($faxnumber!='' && $phy=='') ) {
                $sql	=   "SELECT CONCAT(`LastName`, ', ', `FirstName`) AS 'phyLabel' FROM `refferphysician` WHERE `physician_Reffer_id`=".$ref_phy_id_fax;
                $resp	=   imw_query($sql);
                $resp	=   imw_fetch_assoc($resp);
                $phy  =   $resp['phyLabel'];
            }
			$qry_update_template="UPDATE patient_consult_letter_tbl set fax_status='1',report_sent_date='".date('Y-m-d')."',patient_consult_letter_to='".$phy."',fax_ref_phy_id='".$ref_phy_id_fax."',fax_number='".$faxnumber."' where patient_consult_id IN(".$consult_letter_id.")";
			
			$qry_get_data="SELECT patient_consult_id, patient_id, templateId, templateName, operator_id, status FROM `patient_consult_letter_tbl` WHERE patient_consult_id IN(".$consult_letter_id.")";
			$qry_get_data_res = imw_query($qry_get_data);
			
			if(imw_num_rows($qry_get_data_res)>0){
				
				while($qry_get_data_row = imw_fetch_assoc($qry_get_data_res)){
				
					$qry_insrt_sendfaxlog ="INSERT INTO `send_fax_log_tbl` SET patient_consult_id='".$qry_get_data_row['patient_consult_id']."', patient_id='".$qry_get_data_row['patient_id']."', template_id='".$qry_get_data_row['templateId']."', template_name='".$qry_get_data_row['templateName']."', folder_date ='".date('Y-m-d')."', operator_id='".$qry_get_data_row['operator_id']."',  status='".$qry_get_data_row['status']."', `section_name`='report', `updox_id`='".$responseData['primary']['fax_id']."', `updox_status`='queued', `fax_type`='Primary', `file_name`='".$log_file_name."', `fax_number`='".$faxnumber."'";
				
					$sendfaxlog_row =imw_query($qry_insrt_sendfaxlog);
				}
			}
		}
		$res_update_template=imw_query($qry_update_template);
	}
}
print json_encode($responseData);
/* if($getMsg!=""){
	echo $returnStatus.$getMsg;
} */
?>