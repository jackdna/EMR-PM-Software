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
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
include_once("../../config/globals.php");
$library_path = $GLOBALS['webroot'].'/library';
//========RECIPENTS FAX NAMES & NUMBERS===========
$faxname		= trim($_REQUEST['txtFaxName']);
$faxnumber      = trim($_REQUEST['txtFaxNo']);

$faxnameCc1		= trim($_REQUEST['txtFaxNameCc1']);
$faxnumberCc1   = trim($_REQUEST['txtFaxNoCc1']);

$faxnameCc2		= trim($_REQUEST['txtFaxNameCc2']);
$faxnumberCc2   = trim($_REQUEST['txtFaxNoCc2']);

$faxnameCc3		= trim($_REQUEST['txtFaxNameCc3']);
$faxnumberCc3   = trim($_REQUEST['txtFaxNoCc3']);

$patientConsultId  = trim($_REQUEST['pat_temp_id']); //PATIENT CONSULT ID
$ref_phy_id_fax= $_REQUEST['ref_phy_id']; //REF. PHY. ID

//=====HIFEN SYMBOL REMOVAL FROM FAX NUMBERS======
$faxnumber		= preg_replace('/[^0-9+]/', "", $faxnumber);
$faxnumberCc1	= preg_replace('/[^0-9+]/', "", $faxnumberCc1);
$faxnumberCc2	= preg_replace('/[^0-9+]/', "", $faxnumberCc2);
$faxnumberCc3	= preg_replace('/[^0-9+]/', "", $faxnumberCc3);

$txtFaxPdfName  = $_REQUEST['txtFaxPdfName'];
$send_fax 	 		 	= $_REQUEST["send_fax"];
if($send_fax =='yes') {

	$savePdfFilePath = substr(data_path(), 0, -1);
	$html_file_name_fax='consult_form_fax/'.$txtFaxPdfName;
	$savePdfFileName = $html_file_name_fax.'.pdf';
	$log_file_name = $_SESSION['authId'].'_'.date('Ymdhis').'.pdf';
	$filename = $savePdfFilePath.'/'.$savePdfFileName;
	copy_file_new($savePdfFilePath, $savePdfFilePath."/PatientId_".$_SESSION['patient']."/fax_log", $savePdfFileName, $log_file_name);

	
	$responseData = array('primary'=>array(), 'cc1'=>array(), 'cc2'=>array(), 'cc3'=>array());
	
	
	if( is_updox('fax') ){
		
		$PDFContent = base64_encode(file_get_contents($filename));
		
		//=================UPDOX FAX WORKS STARTS HERE=======
		include($GLOBALS['srcdir'].'/updox/updoxFax.php');//UPDOX LIBRAY FILE
		$updox = new updoxFax();  //UPDOX OBJECT
		
		//==============PRIMARY RECIPENT WORK==================
		$resp  = $updox->sendFax($faxname, $faxnumber, $PDFContent);
		if($resp['status']=='success')
			$responseData['primary']['fax_id']=$resp['data']->faxId;
		else
			$responseData['primary']['error']=$resp['message'];
	
		//===============CC1 RECIPENT WORK======================
		if($faxnameCc1 && $faxnumberCc1) {
			$resp  = $updox->sendFax($faxnameCc1, $faxnumberCc1, $PDFContent);
			if($resp['status']=='success')
				$responseData['cc1']['fax_id']=$resp['data']->faxId;
			else
				$responseData['cc1']['error']=$resp['message'];
		}
	
		//=================CC2 RECIPENT WORK===================
		if($faxnameCc2 && $faxnumberCc2) {
			$resp  = $updox->sendFax($faxnameCc2, $faxnumberCc2, $PDFContent);
			if($resp['status']=='success')
				$responseData['cc2']['fax_id']=$resp['data']->faxId;
			else
				$responseData['cc2']['error']=$resp['message'];
		}
	
		//=================CC3 RECIPENT WORK===================
		if($faxnameCc3 && $faxnumberCc3) {
			$resp  = $updox->sendFax($faxnameCc3, $faxnumberCc3, $PDFContent);
			if($resp['status']=='success')
				$responseData['cc3']['fax_id']=$resp['data']->faxId;
			else
				$responseData['cc3']['error']=$resp['message'];
		}
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
		
		$patient_id_str = "";
		$patient_id = $_REQUEST["patient_id"];
		if(!trim($patient_id)) {
			$patient_id = $_SESSION["patient"];	
		}
		if($patient_id) {
			$patient_id_str	= "Patient ID ".$patient_id;
		}
		
		
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
	//========WORK END TO SEND FAX=======================
	
	logfield:
	
	//=========CHECK IF FAX SEND SUCCESSFULLY============
	if(
		($responseData['primary']['fax_id'] && !empty($responseData['primary']['fax_id'])) ||
		($responseData['cc1']['fax_id']  && !empty($responseData['cc1']['fax_id'])) ||
		($responseData['cc2']['fax_id'] && (!empty($responseData['cc2']['fax_id']))) ||
		($responseData['cc3']['fax_id'] && !empty($responseData['cc3']['fax_id']))
	 ){
	//==========FAX STATUS UPDATED INTO CONSULT TABLE=====
	if($patientConsultId && !empty($patientConsultId)){
		$updateConsultTable=imw_query("UPDATE `patient_consult_letter_tbl` set fax_status='1', report_sent_date='".date('Y-m-d')."',fax_ref_phy_id='".$ref_phy_id_fax."',fax_number='".$faxnumber."' where patient_consult_id='".$patientConsultId."'");
		
	//=======GET CONSULT DATA FOR INSERTION INTO SEND_FAX_LOG_TBL ON BASED OF REQUESTED CONSULT ID=========
		$getConsultData="SELECT patient_id, templateId, templateName, operator_id FROM `patient_consult_letter_tbl` WHERE patient_consult_id='".$patientConsultId."'";
			$getConsultRow = imw_query($getConsultData);
			if(imw_num_rows($getConsultRow)>0){
				$getConsultRes = imw_fetch_assoc($getConsultRow);
				
				$patient_consult_id = $getConsultRes['patient_consult_id'];
				$patient_id = $getConsultRes['patient_id'];
				$template_id = $getConsultRes['templateId'];
				$template_name = $getConsultRes['templateName'];
				$operator_id = $getConsultRes['operator_id'];
			  
			  //==PRIMARY RECIPENT DATA INSERT INTO SEND FAX LOG TABLE======
				if($responseData['primary']['fax_id']){
					$qry = imw_query("INSERT INTO `send_fax_log_tbl` SET patient_consult_id='".$patientConsultId."', patient_id='".$patient_id."', template_id='".$template_id."', template_name='".$template_name."', folder_date='".date('Y-m-d')."', operator_id='".$operator_id."', section_name='savedconsult', updox_id='".$responseData['primary']['fax_id']."', updox_status='queued', `fax_type`='Primary', `file_name`='".$log_file_name."', fax_number='".$faxnumber."'");
				}
			  //==CC1 RECIPENT DATA INSERT INTO SEND FAX LOG TABLE======
			  if($responseData['cc1']['fax_id']){
				  $qry = imw_query("INSERT INTO `send_fax_log_tbl` SET patient_consult_id='".$patientConsultId."', patient_id='".$patient_id."', template_id='".$template_id."', template_name='".$template_name."', folder_date='".date('Y-m-d')."', operator_id='".$operator_id."', section_name='savedconsult', updox_id='".$responseData['cc1']['fax_id']."', updox_status='queued', `fax_type`='CC1', `file_name`='".$log_file_name."', fax_number='".$faxnumberCc1."'");
			  }
			  //==CC2 RECIPENT DATA INSERT INTO SEND FAX LOG TABLE======
			  if($responseData['cc2']['fax_id']){
				  $qry = imw_query("INSERT INTO `send_fax_log_tbl` SET patient_consult_id='".$patientConsultId."', patient_id='".$patient_id."', template_id='".$template_id."', template_name='".$template_name."', folder_date='".date('Y-m-d')."', operator_id='".$operator_id."', section_name='savedconsult', updox_id='".$responseData['cc2']['fax_id']."', updox_status='queued', `fax_type`='CC2', `file_name`='".$log_file_name."', fax_number='".$faxnumberCc2."'");
			  }
			  //==CC3 RECIPENT DATA INSERT INTO SEND FAX LOG TABLE======
			  if($responseData['cc3']['fax_id']){
				  $qry = imw_query("INSERT INTO `send_fax_log_tbl` SET patient_consult_id='".$patientConsultId."', patient_id='".$patient_id."', template_id='".$template_id."', template_name='".$template_name."', folder_date='".date('Y-m-d')."', operator_id='".$operator_id."', section_name='savedconsult', updox_id='".$responseData['cc3']['fax_id']."', updox_status='queued', `fax_type`='CC3', `file_name`='".$log_file_name."', fax_number='".$faxnumberCc3."'");
			}
		 }
	  }
	}
	//==RESPONSE JSON ENCODE AND SEND BACK TO CONSULT_LETTER_PAGE.PHP FILE FUNCTION sendSavedFax()====
	 print json_encode($responseData);
	 exit;
}
?>	
<html>
	<head>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
        <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
        <script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
		<script>
		
			top.btn_show('');
			window.top.show_loading_image("hide");
			var show_fax_popup = '<?php echo $show_fax_popup;?>';
			var pat_temp_id = '<?php echo $_REQUEST["pat_temp_id"];?>';
			if(show_fax_popup == 'yes') {
				if(parent.document.getElementById("send_fax_div")) {
					top.fmain.$('#send_fax_div').modal("show");
				}
				// Condition is commemnted, because it's conflicting with existing data on popup load.
				// if(parent.document.getElementById("send_fax_number")) {
				// 	parent.document.getElementById("send_fax_number").value = '';
				// }
				if(parent.document.getElementById("pat_temp_id")) {
					parent.document.getElementById("pat_temp_id").value = pat_temp_id;
				}
			}
			function unloadFax() {
				if(parent.document.getElementById("send_fax_div")) {
					parent.document.getElementById("send_fax_div").style.display = 'none';	
				}
			}

		</script>    
    </head>
    <body onUnload="javascript:unloadFax();" bgcolor="#ffffff">
    
    </body>
</html>