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

$ignoreAuth=true;
include_once("../../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/SaveFile.php");
include_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/print_functions_class.php');
include_once($GLOBALS['fileroot'].'/interface/chart_notes/chart_globals.php');
include_once($GLOBALS['srcdir'].'/classes/work_view/wv_functions.php');
include_once($GLOBALS['srcdir'].'/html_to_pdf/PDFMerger.php');
use PHPMailer\PHPMailer;
$cpr = new CmnFunc($_SESSION['patient']);
function pdfVersion($filename)
{ 
	$fp = @fopen($filename, 'rb');
 
	if (!$fp) {
		return 0;
	}
 
	/* Reset file pointer to the start */
	fseek($fp, 0);
 
	/* Read 20 bytes from the start of the PDF */
	preg_match('/\d\.\d/',fread($fp,20),$match);
 
	fclose($fp);
 
	if (isset($match[0])) {
		return $match[0];
	} else {
		return 0;
	}
} 

$file_path = (stristr($_REQUEST['pdf_name'],'tmp/')) ? $GLOBALS['srcdir'].'/html_to_pdf/'.$_REQUEST['patient_pdf'] : $_REQUEST['patient_pdf'];	
if($_REQUEST['action'] == "delete_pdf"){
	
	unlink($file_path);
	$deleted = "0";
	while(!$deleted){
		//if(unlink($_REQUEST['mergeFile']))
		$deleted = 1;
	}
	if($deleted){
		unlink($_REQUEST['mergeFile']);
		echo "success";
	}
}
if(!isset($_REQUEST['action']) && $_REQUEST['action'] != "delete_pdf"){
		if($_REQUEST['testIds']!=""){
            $image_from = $_REQUEST['image_from'] ? $_REQUEST['image_from'] : false;
            $arrPDFs = $cpr->getAllTestPdf($_REQUEST['testIds'],$image_from);
		}
		$pdf = new PDFMerger;
		if(file_exists($file_path)){
			$pdf->addPDF($file_path);
		}
		foreach($arrPDFs as $pdfFile){
			$fileChk = $endfileChk = "";
			if(file_exists($pdfFile)){
				$version = pdfVersion($pdfFile);
				if($version<=1.4){
					$fileChk = file($pdfFile);
					$endfileChk= trim($fileChk[count($fileChk) - 1]);
					if (trim($endfileChk) === "%%EOF" || strpos(trim($endfileChk),"%%EOF")!== false) {
						$pdf->addPDF($pdfFile);
					}
				} else {
					// Else case works when version check is failed, As per testing without version check it's working fine in chrome and ie browser. This fix is done for Jira ticket IM-2657.
					$fileChk = file($pdfFile);
					$endfileChk= trim($fileChk[count($fileChk) - 1]);
					if (trim($endfileChk) === "%%EOF" || strpos(trim($endfileChk),"%%EOF")!== false) {
						$pdf->addPDF($pdfFile);
					}
				}
			}
		}
		$mergeFile = "merge_".$_SESSION['patient']."_".date("H_i_s").".pdf";
		$mergeFileName = $mergeFile;
		$getPCIP = $_SESSION["authId"];			
		$getIP=str_ireplace(".","_",$getPCIP);

		if($_REQUEST['iportal'] == 1) {
			$mergeFileWeb=data_path(1)."tmp/".$mergeFile; 
			$mergeFile=data_path()."tmp/".$mergeFile; 
			$fld_path = $cpr->data_file_path.'/';
		} else {
			$mergeFileWeb=data_path(1)."UserId_".$_SESSION['authUserID']."/tmp/".$mergeFile; 
			$mergeFile=data_path()."UserId_".$_SESSION['authUserID']."/tmp/".$mergeFile; 
			$fld_path = $cpr->data_file_path.'/User_'.$_SESSION['authUserID'].'/';
		}
		$pdf->merge('file', $mergeFile);

		if(file_exists($mergeFile)){
			if($_REQUEST['iportal'] == 1) {
				$operator_folder="merge_iportal_".$_SESSION['patient'];
			} else {
				$operator_folder="merge_".$_SESSION['authUserID'];
			}

			if(!is_dir($fld_path.$operator_folder)){
				mkdir($fld_path.$operator_folder,0777);
			}
			foreach(glob($fld_path.$operator_folder."/*.pdf") as $pdf_file_names){
				if($pdf_file_names){unlink($pdf_file_names);}
			}
			$file_move=copy($mergeFile,$fld_path.$operator_folder."/".$mergeFile);
			if($file_move){
				unlink($mergeFile);
				if($_REQUEST['iportal'] == 1) {
					$mergeFile = $cpr->data_file_path.'/'.$operator_folder."/".$mergeFile;
				} else {
					$mergeFile = $cpr->data_file_path.'/User_'.$_SESSION['authUserID'].'/'.$operator_folder."/".$mergeFile;
				}				
			}
		}
		
		if($_REQUEST['ptmailid'] && $_REQUEST['ptmailname'])
		{
			
			$queryEmailCheck=imw_query("select * from groups_new where config_email!='' and config_pwd!='' and del_status='0' ORDER BY name ASC LIMIT 0,1")or die(imw_error());
			if(imw_num_rows($queryEmailCheck)>=1)
			{
				$dEmailCheck=imw_fetch_object($queryEmailCheck);
				$groupEmailConfig['email']=$dEmailCheck->config_email;
				$groupEmailConfig['pwd']=$dEmailCheck->config_pwd;
				$groupEmailConfig['host']=$dEmailCheck->config_host;
				$groupEmailConfig['header']=$dEmailCheck->config_header;
				$groupEmailConfig['footer']=$dEmailCheck->config_footer;
				$groupEmailConfig['port']=$dEmailCheck->config_port;
			}
			
			$emailID         	= $_REQUEST['ptmailid'];
			$emailIDname		= $_REQUEST['ptmailname'];
			//send smtp mail here
			// require_once('../../../library/phpmailer/PHPMailerAutoload.php');
			if($groupEmailConfig['email'] && $groupEmailConfig['pwd'] && $groupEmailConfig['host'] && $mergeFile)
			{
				//Create a new PHPMailer instance
				$mail = new PHPMailer\PHPMailer;
				//Tell PHPMailer to use SMTP
				$mail->isSMTP();
				//Enable SMTP debugging
				// 0 = off (for production use)
				// 1 = client messages
				// 2 = client and server messages
				$mail->SMTPDebug = 0;
				//Ask for HTML-friendly debug output
				$mail->Debugoutput = 'html';
				//Set the hostname of the mail server
				$mail->Host = $groupEmailConfig['host'];
				//Set the SMTP port number - likely to be 25, 465 or 587
				$mail->Port = $groupEmailConfig['port'];
				//Whether to use SMTP authentication
				$mail->SMTPAuth = true;
				//Username to use for SMTP authentication
				$mail->Username = $groupEmailConfig['email'];
				//Password to use for SMTP authentication
				$mail->Password = $groupEmailConfig['pwd'];
				//Set who the message is to be sent from
				$mail->setFrom($groupEmailConfig['email'], '');
				//Set an alternative reply-to address
				//$mail->addReplyTo('replyto@example.com', 'First Last');
				//Set who the message is to be sent to
				
				if($emailID)$mail->addAddress($emailID,$emailIDname);
				//Set the subject line
				$mail->Subject = 'Complete Patient Record';
				//Read an HTML message body from an external file, convert referenced images to embedded,
				//convert HTML into a basic plain-text alternative body
				$mail->msgHTML($groupEmailConfig['header']."<br/>Please find the Enclosed Complete Patient Record<br/>".$groupEmailConfig['footer']);
				//Replace the plain text body with one created manually
				$mail->AltBody = '';
				//Attach an image file
				if($mergeFile)$mail->addAttachment($mergeFile);
				
				//send the message, check for errors
				if (!$mail->send()) {
					$customMsg = "Mailer Error: " . $mail->ErrorInfo;
				} else {
					$customMsg = "Mail sent successfully";
				}
				
			}
		}


		
		?>
		<script src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.min.1.12.4.js"></script>
		<script>
		function openPDF(){
            if(window.frames.frameElement){
                var iframe = window.frames.frameElement.id;
                if(iframe == 'dynamiciFrame'){
                     var iframe = top.$('#dynamiciFrame');
                    if ( iframe.length ) {
                        iframe.attr('src','<?php echo $mergeFileWeb ?>');   
                        setTimeout(function(){ unlinkPdf(); }, 3000);
                    }

                    //alert(top.$('#dynamiciFrame').attr("src"));
				//COMPLETE PATIENT RECORD MERGE PDF SEND FAX CONDITION TO STOP OPEN PDFS IN IFRAME 
				<?php if(!$_REQUEST['sendFaxFromCPR'] && $_REQUEST['sendFaxFromCPR']=='')
				{ 
				?>
					$('#dynamiciFrame').attr("src",'<?php echo $mergeFile ?>');
					setTimeout(function(){ unlinkPdf(); }, 3000);
				<?php
				} 
				?>
                }else{
                  
				  <?php
					 //COMPLETE PATIENT RECORD MERGE PDF SEND FAX CONDITION TO STOP OPEN PDFS IN POPUP IN SEND FAX CASE
					 if(!$_REQUEST['sendFaxFromCPR'] && $_REQUEST['sendFaxFromCPR']=='')
					{ 
					//Changing Filepath to Webroot / Server path
                    $mergeFilePath = str_replace($GLOBALS['fileroot'], $GLOBALS['php_server'], $mergeFile);
                   ?>
                    win = window.open('<?php echo $mergeFilePath ?>');
                     if(typeof(win)=="object")
                     setTimeout(function(){ unlinkPdf(); }, 3000);
                     else
                     return false;
					<?php
					} 
					?>
                }
            }else{
                
                <?php
				//COMPLETE PATIENT RECORD MERGE PDF SEND FAX CONDITION TO STOP OPEN PDFS IN POPUP IN SEND FAX CASE
				if(!$_REQUEST['sendFaxFromCPR'] && $_REQUEST['sendFaxFromCPR']=='')
				{ 
                //Changing Filepath to Webroot / Server path
                $mergeFilePath = str_replace($GLOBALS['fileroot'], $GLOBALS['php_server'], $mergeFile);
                ?>
                win = window.open('<?php echo $mergeFilePath ?>');
                 if(typeof(win)=="object")
                 setTimeout(function(){ unlinkPdf(); }, 3000);
                 else
                 return false;
				<?php
				} 
				?>	
            }
            
            /*
    		if($('#dynamiciFrame').length)
    		{
    			 var iframe = top.$('#dynamiciFrame');
    			if ( iframe.length ) {
    				iframe.attr('src','<?php echo $mergeFileWeb ?>');   
    				return true;
    			}
    			
    			alert(top.$('#dynamiciFrame').attr("src"));
    			$('#dynamiciFrame').attr("src",'<?php echo $mergeFile ?>');
    			return true;
    		}
    		else
    		{
                <?php
                //Changing Filepath to Webroot / Server path
                //$mergeFilePath = str_replace($GLOBALS['fileroot'], $GLOBALS['php_server'], $mergeFile);
                ?>
    			win = window.open('<?php echo $mergeFilePath ?>');
    			 if(typeof(win)=="object")
    			 return true;
    			 else
    			 return false;
    		}
            */
        }
        openPDF();
		function unlinkPdf() {
			$.ajax({url:"<?php echo $GLOBALS['webroot'] ?>/interface/patient_info/complete_pt_rec/merge_pdf.php?action=delete_pdf&patient_pdf=<?php echo $_REQUEST['patient_pdf'];?>&mergeFile=<?php echo $mergeFile;?>",
				success: function(resp){
					if(resp == "success")
					window.close();
				}
			});
		}
		
		</script>
		
<?php 
   //COMPLETE PATIENT RECORD MERGE PDF SEND FAX CONDITION
	if($_REQUEST['sendFaxFromCPR'] && $_REQUEST['sendFaxFromCPR']!='')
	{ 
		include_once("../common/sendfax_chart_summary.php"); return false;
	   
	}

}?>