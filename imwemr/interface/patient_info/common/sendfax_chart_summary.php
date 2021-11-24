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
 
if($_REQUEST['sendFaxFromCPR'] && $_REQUEST['sendFaxFromCPR']!='')
{
    //-----THIS CASE IS FROM COMPLETE PATIENT RECORD TO SEND FAX WITH TEST ATTACHMENTS	
	$faxRecipent = $_REQUEST['txtFaxRecipent'];
	$faxnumber = $_REQUEST['txtFaxNo']; 
	$faxnumber = preg_replace('/[^0-9+]/', "", $faxnumber);

	//-----CREATED MERGED PDF------------------
	$setNameFaxPDF = $mergeFile; //MERGED AND FAXED PDF FILE NAME
	$filename = $mergeFile; //PDF FULL LOCAL PATH
	
	$filetype= 'PDF';
	if(!($fp = fopen($filename, "r")))
	{
		echo "Error opening PDF file";
		exit;
	}
}
else
{	 
	//-----THIS CASE CALL FOR ALL REGULAR SEND FAXES WHICH WAS WORK BEFORE AS IT IS
	header("Cache-control: private, no-cache"); 
	header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
	header("Pragma: no-cache");
	
	//-----GLOBAL FILE INCLUSION------------------
	require_once(dirname(__FILE__).'/../../../config/globals.php');
	$faxConsultMultiId	= $_REQUEST['faxConsultMultiId']; //PATIENT TEMPLATE CONSULT ID IF SENT FROM CONSULT LETTER SECTION
	
	$faxnumber = $_REQUEST['txtFaxNo']; 
	$faxnumber = preg_replace('/[^0-9+]/', "", $faxnumber);
	// $faxnumber=str_ireplace("-","",$faxnumber);
	
	//-----FAX RECIPENT REQUESTED FOR USING UPDOX FAX FUNCTIONALITY
	$faxRecipent = $_REQUEST['txtFaxRecipent'];
	
	//-----PDF PAGE ORIENTATION SETTINGS, p -> PORTRAIT/ l -> LANDSCAPE 
	$pdfOp = (isset($_GET['pdfOp']) && trim($_GET['pdfOp'])!='')? trim($_GET['pdfOp']) : 'p';
	
	$getPCIP=$_SESSION["authId"];	//LOGIN USER		
	$getIP=str_ireplace(".","_",$getPCIP);
	$pdfPath=data_path()."UserId_".$_SESSION['authUserID']."/tmp/"; //PDF LOCATION
	$setNameFaxPDF="fax_ptrecord".$getIP.".pdf"; //FAXED PDF NAME
	
	//-----INCLUSION OF PDF CREATION MAIN FILE-------
	$myHTTPAddress = $GLOBALS['php_server'].'/library/html_to_pdf/createPdf.php';
	
	//-----CURL WORK STARTS FROM HERE-----------------
	$data1 = "";
	$curNew = curl_init();
	
	$urlPdfFile = $myHTTPAddress."?pdf_name=".$pdfPath.$setNameFaxPDF."&setIgnoreAuth=true&op=$pdfOp&saveOption=fax&file_location=$file_location";
	
	curl_setopt($curNew,CURLOPT_URL,$urlPdfFile);
	curl_setopt ($curNew, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($curNew, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
	
	$data1 = curl_exec($curNew);
	
	curl_close($curNew); 
	//-----CURL CLOSE HERE-----------------------------
	//-----CREATED PDF ADDRESS-------------------------
	$filename= $pdfPath.$setNameFaxPDF;
	
	$filetype= 'PDF';
	if(!($fp = fopen($filename, "r")))
	{
		echo "Error opening PDF file";
		exit;
	}
	$data = "";
	while(!feof($fp)) $data .= fread($fp,1024);
	fclose($fp);
}
//-----PATIENT ID STRING MAKING------------------------
$patient_id = $_REQUEST["patient_id"];
if(!trim($patient_id))
{
	$patient_id = $_SESSION["patient"];	
}
$patientFolderId = 'PatientId_'.$patient_id;

$OperatorId = $_SESSION["authId"];

//-----LOG FILE NAME CREATED FOR KEEPING IT FOR FAX LOG PURPOSE
$log_file_name = $_SESSION['authId'].'_'.date('Ymdhis').'.pdf';
//-----COPY FILE TO UNDER PATIENT FAX LOG FOLDER TO DISPLAY IT FOR FAX LOG SECTION

copy_file_new($pdfPath, data_path().$patientFolderId."/fax_log", $setNameFaxPDF, $log_file_name);

//-----UPDOX FAX FUNCTIONALITY WORKS STARTS HERE------
if( is_updox('fax') )
{
	//-----PDF LOCATION------------------------------- 
	$PDFContent = base64_encode(file_get_contents($filename));
	
	//-----UPDOX FAX WORKS STARTS FROM HERE---------
	include($GLOBALS['srcdir'].'/updox/updoxFax.php');  //CALL TO UPDOX LIBRAY FILE
	$updox = new updoxFax();  //UPDOX OBJECT
	
	//-----SEND FAX TO PRIMARY RECIPENT WORK----------
	$resp  = $updox->sendFax($faxRecipent, $faxnumber, $PDFContent);
}
elseif( is_interfax() )
{
	//-----OLD INTERFAX FUNCTIONALITY WORK STARTS FROM HERE	
	if(!($fp = fopen($filename, "r")))
	{
		$resp['statusCode'] = '0';
		$resp['message'] = "Error opening PDF file.";
		goto logfield;
	}
	
	$filetype = pathinfo($filename, PATHINFO_EXTENSION);
	
	$pdfContent = "";
	while(!feof($fp)) $pdfContent .= fread($fp,1024);
	fclose($fp);
	
	//-----START CODE GET PATIENT ID(s)
	$patient_id = $_REQUEST["patient_id"];
	if(!trim($patient_id))
	{
		$patient_id = $_SESSION["patient"];	
	}
	
	$patient_id_str = "";
	if(trim($faxConsultMultiId))
	{
		$ptQry = "	SELECT 
						distinct(patient_id) as patient_id 
					FROM 
						`patient_consult_letter_tbl`
					WHERE 
						patient_consult_id IN (".$faxConsultMultiId.") 
				 ";	
		$ptRes = imw_query($ptQry);
		
		if(imw_num_rows($ptRes) == 1)
		{	
			//-----SEND PATIENT-ID IN FAX IF ONLY ONE PATIENT IS SELECTED
			$ptRow = imw_fetch_assoc($ptRes);
			$patient_id = $ptRow["patient_id"];
			//$patient_id = str_ireplace(",",", ",$patient_id);
		}
	}
	
	if($patient_id)
	{
		$patient_id_str	= "Patient ID ".$patient_id;
	}
	
	if($_REQUEST['send_fax_subject']){ $patient_id_str = $_REQUEST['send_fax_subject']; }
	//END CODE GET PATIENT ID(s)
	
	//-----INTERFAX FUNCTIONALITY WORK STARTS HERE----
	$client = new SoapClient("http://ws.interfax.net/dfs.asmx?WSDL"); //MAIN LIBRARY FILE
	
	$params->Username  			= fax_username;  //GLOBAL SET FAX USERNAME AS PER INTERFAX WEBSITE LOGIN USERNAME
	$params->Password  			= fax_password; //GLOBAL SET INTERFAX PASSWORD
	$params->FaxNumbers 		= $faxnumber; 
	$params->FilesData  		= $pdfContent;
	$params->FileTypes  		= $filetype;
	$params->FileSizes   		= strlen($pdfContent);
	$params->Postpone   		= "2005-04-25T20:31:00-04:00";
	$params->IsHighResolution   = "0";
	$params->CSID   			= "";
	$params->Subject   			= $patient_id_str;
	$params->ReplyAddress 		= "";
	
	$result = $client->SendFaxEx($params); //MAIN FUNCTION TO SEND FAX
	$returnMsg=$result->SendfaxExResult; // RETURNS THE TRANSACTIONID IF FAX SENT SUCCESSFULLY
	
	//-----CONFIRMATION ALERTS------------
	if($returnMsg>0)
	{
		$resp['status'] = 'success';
		$resp['data']->faxId = $returnMsg;
	}
	elseif($returnMsg=='-1003')
	{
		$resp['statusCode'] = $returnMsg;
		$resp['message'] = "Authentication error";
	}
	elseif($returnMsg=='-112')
	{
		$resp['statusCode'] = $returnMsg;
		$resp['message'] = "No valid recipients added or missing fax number or attempting to fax to a number that is not the designated fax number in a developer account.";
	}
	else
	{
		$resp['statusCode'] = $returnMsg;
		$resp['message'] = "Fax sending failed.";
	}
}

logfield:

/*$resp['status']='success';
$resp['data']=(object) array('faxId'=>'123');*/

$customMsg = '';  //ALERT OF FAX MESSAGE
if($resp['status']=='success')
{
	
	//------IF FAX STATUS IS SUCCESS, TRANSACTION ALERT WILL BE POPULATE
	$customMsg = "Transaction_No. ".($resp['data']->faxId)." - Fax sent successfully ";	
	
	  //----CONSULT ID REQUESTED TO FETCH PATIENT CONSULT DATA 
	  if(trim($faxConsultMultiId))
	  {
		  $qry_get_data="SELECT 
							patient_consult_id, 
							patient_id, 
							templateId, 
							templateName, 
							operator_id, 
							status 
						FROM 
							`patient_consult_letter_tbl` 
						WHERE 
							patient_consult_id IN(".$faxConsultMultiId.")";
		  $qry_get_data_res = imw_query($qry_get_data);
			  
			  //CONSULT TEMPLATE DATA ENTERED INTO LOG TABLE FOR CREATING FAX LOG INFORMATION
			  if(imw_num_rows($qry_get_data_res)>0)
			  {
				  
				 while($qry_get_data_row = imw_fetch_assoc($qry_get_data_res)){
				  
					  $qry_insrt_sendfaxlog ="	INSERT INTO 
													`send_fax_log_tbl` 
												SET 
													patient_consult_id	= '".$qry_get_data_row['patient_consult_id']."', 
													folder_date			= '".date('Y-m-d')."', 
													patient_id			= '".$qry_get_data_row['patient_id']."',
													template_id			= '".$qry_get_data_row['templateId']."',
													template_name		= '".$qry_get_data_row['templateName']."',
													operator_id			= '".$qry_get_data_row['operator_id']."', 
													status				= '".$qry_get_data_row['status']."', 
													section_name		= 'complete_patient_record', 
													updox_id			= '".$resp['data']->faxId."', 
													updox_status		= 'queued', 
													fax_type			= 'Primary', 
													file_name			= '".$log_file_name."', 
													fax_number			= '".$faxnumber."'";
					  $sendfaxlog_row =imw_query($qry_insrt_sendfaxlog);
					  $sendfaxlogInsertId = imw_insert_id();  //SEND FAX LOG TABLE PRIMARY ID
				  }
			  }
	  }
	  else
	  {
			//------INSERT INTO SEND FAX LOG WHEN NO CONSULT ID IS COMING
			$qry_insrt_sendfaxlog ="INSERT INTO 
										`send_fax_log_tbl` 
									SET 
										patient_id	= '".$patient_id."',
										folder_date	= '".date('Y-m-d')."',
										operator_id	= '".$OperatorId."',
										section_name= 'complete_patient_record',
										updox_id	= '".$resp['data']->faxId."',
										updox_status= 'queued', 
										fax_type	= 'Primary', 
										file_name	= '".$log_file_name."', 
										`fax_number`= '".$faxnumber."'";
					
				$sendfaxlog_row =imw_query($qry_insrt_sendfaxlog);
				$sendfaxlogInsertId = imw_insert_id(); //SEND FAX LOG TABLE PRIMARY ID
	  }
			 
	 //------UNLINKING PDF FILE AFTER SENDING FAX------
	 if(file_exists($filename))
	 {
		unlink($filename);
	 }	 
}
else
{  
	//------IF FAX FAILED, ALERT WILL DISPLAY WITH CODE AND MESSAGE.
	$customMsg = "Error No. ".$resp['statusCode']." - ".$resp['message'];
}
?>
	
<script type="text/javascript">
<?php if($customMsg != ""){ /*substr($customMsg,0,14)=="Transaction_No"*/ ?>
	top.$("#div_loading_image").hide();
	top.$('#send_fax_number').val('');
	top.$('#send_fax_div').modal('hide');
	top.fAlert('<?php echo $customMsg;?>'); //ALERT TO DISPLAY THE FAX STATUS	
<?php }?>
</script>