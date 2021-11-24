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
include_once("../../config/globals.php");

//PARAMTER REQUESTED FROM /libary/classes/work_view/VisionPrint.php (VP FILE) - FOR GL RX
//PARAMTER REQUESTED FROM /chart_notes/print_patient_contact_lenses.php(PPCL FILE) - FOR CL RX
$faxnumber 		= $_REQUEST['txtFaxNo'];  //FAX NO. 
$faxRecipent	= $_REQUEST['txtFaxRecipent'];  
$fileLocation 	= $_REQUEST['file_location'];
$formId			= $_REQUEST['faxedformId'];   // from (VP File)
$worksheetId	= $_REQUEST['faxedWorksheetId']; // from (PPCL File)

//=====FAX RECIPENT REQUESTED FOR USING UPDOX FAX FUNCTIONALITY========
$faxnumber = preg_replace('/[^0-9+]/', "", $faxnumber);
$pdfOp = (isset($_GET['pdfOp']) && trim($_GET['pdfOp'])!='')? trim($_GET['pdfOp']) : 'p';

$pdfVer = 'html_to_pdf';
$pdfCreate = 'createPdf.php';


$getPCIP=$_SESSION["authId"];			
$getIP=str_ireplace(".","_",$getPCIP);
$setNameFaxPDF="fax_ptrecord".$getIP.".pdf"; //FAX PDF NAME
$pdfPath=data_path()."UserId_".$_SESSION['authUserID']."/tmp/"; 

$myHTTPAddress = $GLOBALS['php_server'].'/library/html_to_pdf/createPdf.php';

$curNew = curl_init();
$urlPdfFile=$myHTTPAddress."?setIgnoreAuth=true&saveOption=F&font_size=8.0&page=1.3&pdf_name=".$pdfPath.$setNameFaxPDF."&file_location=".$fileLocation;
curl_setopt($curNew, CURLOPT_URL,$urlPdfFile);
curl_setopt($curNew, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curNew, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
$data = curl_exec($curNew);
curl_close($curNew); 
//===============CREATED PDF ADDRESS==================
$filename= $pdfPath.$setNameFaxPDF;
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
copy_file_new($pdfPath, "../../data/".PRACTICE_PATH."/".$patientFolderId."/Fax_log", $setNameFaxPDF, $log_file_name);

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
		$resp['message'] = "Error opening PDF fileaaaaa.";
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
	$params->Subject   			= "";
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

$customMsg = '';  //ALERT OF FAX MESSAGE
if($resp['status']=='success'){
	//IF FAX STATUS IS SUCCESS, TRANSACTION ALERT WILL BE POPULATE
	$customMsg = "Transaction_No. ".($resp['data']->faxId)." - Fax sent successfully ";	
	
	  if(trim($formId) || trim($worksheetId)) {
			
			$querySubPart=""; 
			if($formId){ 
				$querySubPart=",section_name='prs_gl_rx', `form_id`='".$formId."'"; 
			}else if($worksheetId){ 
				$querySubPart=",section_name='prs_cl_rx', `worksheetid`='".$worksheetId."'"; 
			}
			//INSERT INTO SEND FAX LOG WHEN NO CONSULT ID IS COMING
			$qry_insrt_sendfaxlog ="INSERT INTO `send_fax_log_tbl` SET patient_id='".$patient_id."', folder_date='".date('Y-m-d')."', operator_id='".$OperatorId."', updox_id='".$resp['data']->faxId."', updox_status='queued', fax_type='Primary', `file_name`='".$log_file_name."', `fax_number`='".$faxnumber."' $querySubPart ";
			
			$sendfaxlog_row = imw_query($qry_insrt_sendfaxlog);
			$sendfaxlogInsertId = imw_insert_id();
	  }
			 
	 //====UNLINKING PDF FILE AFTER SENDING FAX=========
	 if(file_exists($pdfPath.$setNameFaxPDF)) {
		unlink($pdfPath.$setNameFaxPDF);
	 }	 
}
else
{  
	//IF FAX FAILED, ALERT WILL DISPLAY WITH CODE AND MESSAGE.
	$customMsg = "Error No. ".$resp['statusCode']." - ".$resp['message'];
}

?>
<script type="text/javascript" src="../../library/js/jquery.min.1.12.1.js"></script>
<script type="text/javascript" src="../../library/js/common.js"></script>
<script type="text/javascript" src="../../library/js/core_main.js"></script>
<link rel="stylesheet" type="text/css" href="../../library/css/common.css" />
<script type="text/javascript">
<?php if($customMsg != ""){ ?>
	$('#send_fax_div').hide();
	alert('<?php echo $customMsg;?>'); //ALERT TO DISPLAY THE FAX STATUS	
	$('#div_load_image').hide();
	window.close();
<?php }?>
</script>