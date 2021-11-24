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
 * index.php
 * Access Type: InClude
 * Purpose: Routes for Appointment API calls.
*/
set_time_limit(90);

$this->respond(array('POST','GET'), '/directMessage', function($request, $response, $service, $app) {
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include_once($GLOBALS['srcdir'].'/phpmailer/class.phpmailer.php');
include_once($GLOBALS['srcdir'].'/phpmailer/class.pop3.php');
include_once($GLOBALS['srcdir'].'/phpmailer/class.smtp.php');
//require_once('PHPMailerAutoload.php');
	
	$patientId = $request->__get('patientId');
	$to_email = $_REQUEST['to_email'];
	$subject = urldecode($_REQUEST['subject']);
	$body = urldecode($_REQUEST['body']);
	$type = trim($_REQUEST['type']);
	$attach_ccda = $_REQUEST['attach'];
	
	
	if($type=='Simple'){
	
			$result = false;
			$queryEmailCheck=$app->dbh->imw_query("select config_email, config_pwd, config_host, config_port from groups_new where config_email!='' and config_pwd!='' and del_status='0' ORDER BY name ASC LIMIT 0,1");
				
			if($queryEmailCheck && $app->dbh->imw_num_rows($queryEmailCheck)>0)
			{	
				$select = $app->dbh->imw_fetch_assoc($queryEmailCheck);
				$groupEmailConfig['email']=$select['config_email'];
				$groupEmailConfig['pwd']=$select['config_pwd'];
				$groupEmailConfig['host']=$select['config_host'];
				$groupEmailConfig['port']=$select['config_port'];
			}
			
			// Simple Mail
			if($groupEmailConfig['email'] && $groupEmailConfig['pwd'] && $groupEmailConfig['host'])
			{	
				
				//Create a new PHPMailer instance
				$mail = new PHPMailer;
				
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
					
				if($to_email)$mail->addAddress($to_email,'');
				//Set the subject line
				$mail->Subject = $subject;
				//Read an HTML message body from an external file, convert referenced images to embedded,
				//convert HTML into a basic plain-text alternative body
				$mail->msgHTML($body);
				//Replace the plain text body with one created manually
				$mail->AltBody = '';
				
				$arrAttachment[] = array(
									"complete_path"=>data_path().'xml\\'.$attach_ccda,
									"mime"=>'zip',
									"file_name"=>$attach_ccda,
									"file_path"=>data_path().'xml\\'.$attach_ccda
									);
												
	
			   $arrAttachment[] = array(
							"complete_path"=>$GLOBALS['fileroot'].'/interface/reports/ccd/CDA.xsl',
							"mime"=>'xsl',
							"file_name"=>'CDA.xsl',
							"file_path"=>$GLOBALS['fileroot'].'/interface/reports/ccd/CDA.xsl'
							);
	
				$zip = new ZipArchive();
				$filename = data_path().'xml\\CCDA-'.time().'.zip';
				
				if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
   					 exit("cannot open <$filename>\n");
				}
				
				foreach ($arrAttachment as $file) {
				
					$zip->addFile($file['file_path'],$file['file_name']);
				}
				$zip->close();
				
				$mail->addAttachment($filename);	
				//Attach an image file
				$attachment_files_arr_set = array($filename);
				
				$ccda_attached_status = 0;
				$pdf_attached_status = 0;	
				foreach($attachment_files_arr_set as $cur_file_name)
				{
					$imedic_ccda_zip_file = $cur_file_name;
					if($imedic_ccda_zip_file != "")
					{
						$file_ext_get = substr($imedic_ccda_zip_file, -3);
						$target_mime_type = "";
						if(strtolower($file_ext_get) == "zip")
						{
							$target_mime_type = "application/zip";
							$ccda_attached_status = 1;
						}
						else if(strtolower($file_ext_get) == "pdf")
						{
							$target_mime_type = "application/pdf";
							$pdf_attached_status = 1;
						}
						else
						{
							$target_mime_type = "text/plain";	
						}
						
						
						//echo $GLOBALS['fileroot'].'/cda/temp/'.$imedic_ccda_zip_file;
					}		
				}	
				//send the message, check for errors
				if (!$mail->send()) {
					$custom_message = "Mailer Error: " . $mail->ErrorInfo;
					$sent=true;
				} else {
					$custom_message = "Mail sent successfully";
					$sent=false;
				}
				
				$log_msg_append = "";
				$log_msg_append_arr = array();
	
				if($ccda_attached_status == 1)
				{
					$log_msg_append_arr[] = "CCDA Attached";		
				}
	
				if($pdf_attached_status == 1)
				{
					$log_msg_append_arr[] = "PDF Attached";
				}
	
				$log_msg_append = implode(", ", $log_msg_append_arr);
				if($log_msg_append){
					$log_msg_append = "(".$log_msg_append.")";
				}
					
				$desc = 'Simple Email to '.$_REQUEST['to_email'].' '.$log_msg_append;
				
				$req_sql = $app->dbh->imw_query("INSERT INTO pt_and_rp_logs(patient_id, pt_rp_id, u_action, `desc`) VALUES('".$patientId."','".$resp_party_id."', 'Transmit', '".$desc."')");
				$result = $app->dbh->imw_query($req_sql);
				
				$qry_smail = "INSERT INTO direct_messages_patient SET 
									patient_id = '".$patientId."', 
									to_email = '".$to_email."',
									from_email = '".$from_email."',
									subject = '".$subject."',
									message = '".$body."',
									folder_type = 3,
									MID = '".$MID."',
									del_status = 0,
									imedic_user_id = '".$patientId."',
									local_datetime = '".date('Y-m-d H:i:s')."',
									msg_type='Simple Email'";
				$result = $app->dbh->imw_query($qry_smail);
				$message_id = $app->dbh->imw_insert_id($qry_smail);
				
					$response = array('simpleMessage'=>array('status'=>$result));
					$data_path_zip = $filename;
					unlink($data_path_zip);
					$data_path = data_path().'xml\\'.$attach_ccda;
					unlink($data_path);
			}
			return json_encode($response);
		}	
			// Direct Message 
		else{
			
			if($type = 'Direct'){
				$record = false;
				
				
				$req_qry = "SELECT default_email as email, default_pass as email_password FROM default_patient_direct_credentials";
				$req_qry_obj = $app->dbh->imw_query($req_qry);
				$result_data = $app->dbh->imw_fetch_assoc($req_qry_obj);
				$from_email = $result_data['email'];
				 
				 if(UPDOX_DIRECT === true)
				{	
					include_once('updox.php');
					$objDirect = new updoxDirect();
				}
				else{
				
					include_once("direct_class.php");
					$objDirect = new Direct($result_data['email'],$result_data['email_password']);
				}
				
				
				$arrAttachment[] = array(
									"complete_path"=>data_path().'xml\\'.$attach_ccda,
									"mime"=>'zip',
									"file_name"=>$attach_ccda,
									"file_path"=>data_path().'xml\\'.$attach_ccda
									);
												
	
			   $arrAttachment[] = array(
							"complete_path"=>$GLOBALS['fileroot'].'/interface/reports/ccd/CDA.xsl',
							"mime"=>'xsl',
							"file_name"=>'CDA.xsl',
							"file_path"=>$GLOBALS['fileroot'].'/interface/reports/ccd/CDA.xsl'
							);
			$zip = new ZipArchive();
			$zipname = 'CCDA-'.time().'.zip';
	
			$zip->open($zipnamePath, ZipArchive::CREATE);
			
			foreach ($arrAttachment as $file) {
				$zip->addFile($file['file_path'], $file['file_name']);
			}			
			$zip->close();
	
			$attachment_files_arr_set = array($zipname);
	
			$ccda_attached_status = 0;
			$pdf_attached_status = 0;
			foreach($attachment_files_arr_set as $cur_file_name)
	{
		$imedic_ccda_zip_file = $cur_file_name;	
		if($imedic_ccda_zip_file != "")
		{
			$file_ext_get = substr($imedic_ccda_zip_file, -3);
			$target_mime_type = "";
			if(strtolower($file_ext_get) == "zip")
			{
				$target_mime_type = "application/zip";
				$ccda_attached_status = 1;
			}
			else if(strtolower($file_ext_get) == "pdf")
			{
				$target_mime_type = "application/pdf";
				$pdf_attached_status = 1;
			}
			else
			{
				$target_mime_type = "text/plain";	
			}		
	
			$objDirect->arrMail['attachment'][] = array(
												"complete_path"=> data_path().'xml\\'.$attach_ccda,
												"mime" => $target_mime_type,
												"file_name"=> $imedic_ccda_zip_file,
												"size"=> filesize(data_path().'xml\\'.$attach_ccda),
												"file_path"=> data_path().'xml\\'.$attach_ccda
												);		
		}		
	}
		if(UPDOX_DIRECT === true)
		{	
			$MID_arr = $objDirect->sendMail($to_email,$subject,$body);
		}
		else{
			$MID_arr = $objDirect->sendMail($to_email,$subject,$from_email);
		}
		
		if($MID_arr['status']=='failed'){
			//throw new Exception($MID_arr['statusCode'].'--->'.$MID_arr['message']);
			$record = false;
			
		}elseif($MID_arr['data']->messageId>0){
			$MID=$MID_arr['data']->messageId;
		}else{
			$MID=$MID_arr;
		}	
		$log_msg_append = "";
		$log_msg_append_arr = array();
	
		if($ccda_attached_status == 1)
		{
			$log_msg_append_arr[] = "CCDA Attached";		
		}
	
		if($pdf_attached_status == 1)
		{
			$log_msg_append_arr[] = "PDF Attached";
		}
	
		$log_msg_append = implode(", ", $log_msg_append_arr);
		if($log_msg_append){
		$log_msg_append = "(".$log_msg_append.")";
		}
		$desc = 'Direct Email to '.$_REQUEST['to_email'].' '.$log_msg_append;
				
		$req_sql = $app->dbh->imw_query("INSERT INTO pt_and_rp_logs(patient_id, pt_rp_id, u_action, `desc`) VALUES('".$patientId."','".$resp_party_id."', 'Transmit', '".$desc."')");
		$result = $app->dbh->imw_query($req_sql);
		if($MID != "" && $MID>0){
			$folder_type = "3";	
		}else{
			$folder_type = "2";	
		}
		if($folder_type = "3"){
			$sql_ins = "INSERT INTO direct_messages_patient SET 
						patient_id = '".$patientId."', 
						to_email = '".$to_email."',
						from_email = '".$from_email."',
						subject = '".$subject."',
						message = '".$body."',
						folder_type = '3',
						MID = '".$MID."',
						del_status = 0,
						imedic_user_id = '".$patientId."',
						local_datetime = '".date('Y-m-d H:i:s')."'
						";
						
			$result = $app->dbh->imw_query($sql_ins);
			$message_id = $app->dbh->imw_insert_id($sql_ins);	
			
			//$direct_message_id = mysql_insert_id();
			if(isset($objDirect->arrMail['attachment']) && $message_id>0){
				foreach($objDirect->arrMail['attachment'] as $cur_file_arr)
				{
					$complete_path = $cur_file_arr['file_path'];
					$file_name = $cur_file_arr['file_name'];
					$mime = $cur_file_arr['mime'];
					$size = $cur_file_arr['size'];
					
					if($file_name != ""){
						
					$sql_ins = "INSERT INTO direct_messages_patient_attachment SET 
									direct_message_id = '".$message_id."',
									file_name = '".$file_name."',
									size = '".$size."',
									mime = '".$mime."',
									complete_path = '".addslashes($complete_path)."'								
									";
						$record = $app->dbh->imw_query($sql_ins);
						if($record && $app->dbh->imw_affected_rows()>0){
							$response = array('directMessage'=>array('status'=>$record));
							$data_path = data_path().'xml/'.$attach_ccda;
							unlink($data_path);
							return json_encode($response);
						}
						else{
							$response = array('directMessage'=>array('status'=>$record));
							$data_path = data_path().'xml/'.$attach_ccda;
							unlink($data_path);
							return json_encode($response);
						}
					}					
				}
			}
			
		}
		
	} 
}
		
});



$this->respond(function($request, $response, $service) use(&$patientId) {

});