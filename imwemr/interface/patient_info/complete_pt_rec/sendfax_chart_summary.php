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

	header("Cache-control: private, no-cache"); 
	header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
	header("Pragma: no-cache");
	include_once("../../../config/globals.php");
	
	$faxConsultMultiId	= $_REQUEST['faxConsultMultiId']; //PATIENT TEMPLATE CONSULT ID
	$faxnumber = $_REQUEST['txtFaxNo'];  //FAX NO. 
	$faxRecipent = $_REQUEST['txtFaxRecipent'];  
	//=====FAX RECIPENT REQUESTED FOR USING UPDOX FAX FUNCTIONALITY========
	
	$faxnumber = preg_replace('/[^0-9+]/', "", $faxnumber);
	$pdfOp = (isset($_GET['pdfOp']) && trim($_GET['pdfOp'])!='')? trim($_GET['pdfOp']) : 'p';
	//$pdfVer = (isset($_GET['pdfversion']) && trim($_GET['pdfversion'])!='')? trim($_GET['pdfversion']) : 'new_html2pdf';
	$pdfVer = 'html_to_pdf';
	//$pdfCreate = $pdfVer=='html2pdf' ? 'index.php' : 'createPdf.php';
	$pdfCreate = 'createPdf.php';

	$dir = explode('/',$_SERVER['HTTP_REFERER']);
	$httpPro = $dir[0];
	$myHTTPAddress = $httpPro.'//'.$myInternalIP.'/'.$web_RootDirectoryName.'/library/'.$pdfVer.'/'.$pdfCreate;
	$getPCIP=$_SESSION["authId"];			
	$getIP=str_ireplace(".","_",$getPCIP);
	$setNameFaxPDF="fax_ptrecord".$getIP; //FAX PDF NAME
	$curNew = curl_init();
	//$urlPdfFile=$myHTTPAddress."?op=".$pdfOp."&saveOption=fax&name=".$setNameFaxPDF;
	//if($pdfVer=='html2pdf'){
		$urlPdfFile=$myHTTPAddress."?setIgnoreAuth=true&saveOption=F&font_size=8.0&page=1.3&file_location=pdffile.html&pdf_name=".$setNameFaxPDF;
	//}
	//echo "<iframe src=\"$urlPdfFile\" style=\"display:none;\" sandbox=\"allow-same-origin allow-scripts allow-popups allow-forms\"></iframe>";
	
	curl_setopt($curNew, CURLOPT_URL,$urlPdfFile);
	curl_setopt($curNew, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curNew, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
	$data = curl_exec($curNew);
	curl_close($curNew); 
	//===============CREATED PDF ADDRESS==================
	$filename= '../../../library/'.$pdfVer.'/'.$setNameFaxPDF.'.pdf';
	$filetype= 'PDF';
	if(!($fp = fopen($filename, "r"))){
		echo "Error opening PDF file";
		exit;
	}
	$data = "";
	while(!feof($fp)) $data .= fread($fp,1024);
	fclose($fp);
	
	//=============PATIENT ID STRING MAKING================
	$patient_id = $_REQUEST["patient_id"];
	if(!trim($patient_id)){
		$patient_id = $_SESSION["patient"];	
	}
	$patientFolderId = 'PatientId_'.$patient_id;
	
	$OperatorId = $_SESSION["authId"];
	
	$log_file_name = $_SESSION['authId'].'_'.date('Ymdhis').'.pdf';
	copy_file_new("../../../library/".$pdfVer, "../../../data/".PRACTICE_PATH."/".$patientFolderId."/fax_log", $setNameFaxPDF.".pdf", $log_file_name);
	
	
	if( is_updox('fax') ){
		
		$PDFContent = base64_encode(file_get_contents($filename));
		
		//===========UPDOX FAX WORKS STARTS FROM HERE=========
		include($GLOBALS['srcdir'].'/updox/updoxFax.php');  //UPDOX LIBRAY FILE
		$updox = new updoxFax();  //UPDOX OBJECT
		
		//========SEND FAX TO PRIMARY RECIPENT WORK==========
		$resp  = $updox->sendFax($faxRecipent, $faxnumber, $PDFContent);
		
	}
	elseif( is_interfax() ){
		
		if(!($fp = fopen($filename, "r"))){
			$resp['statusCode'] = '0';
			$resp['message'] = "Error opening PDF file.";
			goto logfield;
		}
		
		$filetype = pathinfo($filename, PATHINFO_EXTENSION);
		
		$pdfContent = "";
		while(!feof($fp)) $pdfContent .= fread($fp,1024);
		fclose($fp);
		
		//START CODE GET PATIENT ID(s)
		$patient_id = $_REQUEST["patient_id"];
		if(!trim($patient_id)) {
			$patient_id = $_SESSION["patient"];	
		}
		
		$patient_id_str = "";
		if(trim($faxConsultMultiId)) {
			$ptQry = "SELECT distinct(patient_id) as patient_id FROM patient_consult_letter_tbl WHERE patient_consult_id IN (".$faxConsultMultiId.") ";	
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
		if($_REQUEST['send_fax_subject']){$patient_id_str=$_REQUEST['send_fax_subject'];}
		//END CODE GET PATIENT ID(s)
		
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
	
	/*$resp['status']='success';
	$resp['data']=(object) array('faxId'=>'123');*/
	
	$customMsg = '';  //ALERT OF FAX MESSAGE
	if($resp['status']=='success'){
		//IF FAX STATUS IS SUCCESS, TRANSACTION ALERT WILL BE POPULATE
		$customMsg = "Transaction_No. ".($resp['data']->faxId)." - Fax sent successfully ";	
		
		  //CONSULT ID REQUESTED TO FETCH PATIENT CONSULT DATA 
		  if(trim($faxConsultMultiId)) {
			  $qry_get_data="SELECT patient_consult_id, patient_id, templateId, templateName, operator_id, status FROM `patient_consult_letter_tbl` WHERE patient_consult_id IN(".$faxConsultMultiId.")";
			  $qry_get_data_res = imw_query($qry_get_data);
				  
				  //CONSULT TEMPLATE DATA ENTERED INTO LOG TABLE FOR CREATING FAX LOG INFORMATION
				  if(imw_num_rows($qry_get_data_res)>0){
					  
					 while($qry_get_data_row = imw_fetch_assoc($qry_get_data_res)){
					  
						  $qry_insrt_sendfaxlog ="INSERT INTO `send_fax_log_tbl` SET patient_consult_id='".$qry_get_data_row['patient_consult_id']."', folder_date='".date('Y-m-d')."', patient_id='".$qry_get_data_row['patient_id']."', template_id='".$qry_get_data_row['templateId']."', template_name='".$qry_get_data_row['templateName']."', operator_id='".$qry_get_data_row['operator_id']."',  status='".$qry_get_data_row['status']."', section_name='complete_patient_record', updox_id='".$resp['data']->faxId."', updox_status='queued', fax_type='Primary', `file_name`='".$log_file_name."', `fax_number`='".$faxnumber."'";
						  $sendfaxlog_row = imw_query($qry_insrt_sendfaxlog);
						  $sendfaxlogInsertId = imw_insert_id();
					  }
				  }
		  }else{
		  	    //INSERT INTO SEND FAX LOG WHEN NO CONSULT ID IS COMING
				$qry_insrt_sendfaxlog ="INSERT INTO `send_fax_log_tbl` SET patient_id='".$patient_id."', folder_date='".date('Y-m-d')."', operator_id='".$OperatorId."', section_name='complete_patient_record', updox_id='".$resp['data']->faxId."', updox_status='queued', fax_type='Primary', `file_name`='".$log_file_name."', `fax_number`='".$faxnumber."'";
						
					$sendfaxlog_row = imw_query($qry_insrt_sendfaxlog);
					$sendfaxlogInsertId = imw_insert_id();
		  }
		 		 
		 //====UNLINKING PDF FILE AFTER SENDING FAX=========
		 if(file_exists("../../../library/".$pdfVer."/".$setNameFaxPDF.".pdf")) {
			unlink("../../../library/".$pdfVer."/".$setNameFaxPDF.".pdf");
		 }	 
	}
	else
	{  
		//IF FAX FAILED, ALERT WILL DISPLAY WITH CODE AND MESSAGE.
		$customMsg = "Error No. ".$resp['statusCode']." - ".$resp['message'];
	}
	?>
<script type="text/javascript" src="../../../library/js/jquery.min.1.12.1.js"></script>
<script type="text/javascript" src="../../../library/js/common.js"></script>
<script type="text/javascript" src="../../../library/js/core_main.js"></script>
<link rel="stylesheet" type="text/css" href="../../../library/css/common.css" />
<script type="text/javascript">
<?php if($customMsg != ""){ /*substr($customMsg,0,14)=="Transaction_No"*/ ?>
	top.$('#div_load_image').hide();
	top.$('#send_fax_div').hide();
	top.fAlert('<?php echo $customMsg;?>'); ////ALERT TO DISPLAY THE FAX STATUS	
<?php }?>
</script>