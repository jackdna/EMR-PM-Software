<?php
//include_once("mailReader.php");
require_once('Mail_mimeDecode/mimeDecode.php');
class Direct{
	public $username = "";
	public $password = "";
	public $client;
	public $path_to_wsdl = "";
	public $sessionKey = '';
	public $arrInbox =  array();
	public $arrMail=  array();
	public $save_directory = "";
	public $saved_files = array();
	public $debug = FALSE;
	function __construct($username, $password){
		
		$this->username = $username;
		$this->password = $password;
		
		if(!isset($GLOBALS["direct_soap_file"]) || $GLOBALS["direct_soap_file"] == "PRODUCTION")
		$this->path_to_wsdl = $GLOBALS['srcdir']."/classes/cmv4_production.wsdl";
		else
		$this->path_to_wsdl = $GLOBALS['srcdir']."/classes/cmv4.wsdl";
		
		$this->soapLogin();
		$this->set_attach_dir_path();
	}
	
	function soapLogin(){
		
		$this->client = new SoapClient($this->path_to_wsdl, array('trace' => 1));
		$arrLogin = array(
			"UserIDorEmail" => $this->username,
			"Password" => $this->password
			);
			
		$resLogin = $this->client->Logon($arrLogin);
		$this->sessionKey = $resLogin->LogonResult;
		
	}
	
	function set_attach_dir_path(){
		$upload_dir = dirname(__FILE__)."/../main/uploaddir/users";
		if(!is_dir($upload_dir)){
			mkdir($upload_dir,0700);
		}
		$uDir = $upload_dir."/UserId_".$_SESSION["iPortalPatientId"];
		if(!is_dir($uDir)){
			mkdir($uDir,0700);
		}
		$uDirMailAttach = $upload_dir."/UserId_".$_SESSION["iPortalPatientId"]."/mails";
		if(!is_dir($uDirMailAttach)){
			mkdir($uDirMailAttach,0700);
		}
		
	}
	
	function readInbox(){
		$arrInbox = array(
			"SessionKey" => $this->sessionKey,
			"MailboxType" => "Inbox",
			"PageNum" => "1",
			"FolderID" => "1",
			"PageSize" => "500",
			"OrderDesc" => true,
			"GetRetractedMsgs" => true,
			"GetInboxUnReadOnly"=>false
			);
		$resInbox = $this->client->GetMailboxXML($arrInbox);
		$objInbox = new SimpleXMLElement($resInbox->GetMailboxXMLResult);	
		foreach($objInbox->MessageListItem as $objMessage){//pre($objInbox);
			
			if(!$this->chk_mail_exist($objMessage->MID,$objMessage->MsID)){
				$this->arrMail = array();
				$this->arrMail['attachment'] = array();
				
				$this->arrMail['from'] = $objMessage->FromEMail;
				$this->arrMail['subject'] = $objMessage->Subject;
				$this->arrMail['datTime'] = $objMessage->CreateTime;
				$this->arrMail['mID'] = $objMessage->MID;
				$this->arrMail['msID'] = $objMessage->MsID;
				$this->arrMail['fromUID'] = $objMessage->FromUID;
				$this->arrMail['msgSize'] = $objMessage->MsgSize;
			
				$arrMsg = array(
						"SessionKey" => $this->sessionKey,
						"MID" => $objMessage->MID,
						"WithCMHeaderXML" => true,
						"WithTrackingXML" => true,
						"WithSecurityEnvelope" => true
						);
				$resMsg = $this->client->GetMIMEMessage($arrMsg);
				$raw_mime = $resMsg->GetMIMEMessageResult;
				$decoder = new Mail_mimeDecode($raw_mime);
				$decoded = $decoder->decode(
					Array(
					'decode_headers' => TRUE,
					'include_bodies' => TRUE, 
					'decode_bodies' => TRUE,
					)
				);
				if(is_array($decoded->parts)){
					foreach($decoded->parts as $idx => $body_part){
						$this->decodePart($body_part);
					}
				}
				$this->arrInbox[] = $this->arrMail;
			}
		}// END foreach
	}
	
	function sendMail($to_email,$subject,$from_email){
		
		$mimeMsg = $this->create_envelope($to_email,$subject,$from_email);
		
		$arrMsg = array(
					"SessionKey" => $this->sessionKey,
					"Message" => $mimeMsg
					);
				 
	   $resMsg = $this->client->SendMIMEMessage($arrMsg);
	  
	   if($resMsg){
		   $xml = $resMsg->SendMIMEMessageResult;	
		   $objXml = simplexml_load_string($xml);	
		   $MID = $objXml->NewMID;
		   return $MID;
	   }else{
		   return false;
	   }
	}
	function chk_mail_exist($mID,$msID){
		$qry = "SELECT * FROM direct_messages 
				WHERE imedic_user_id = '".$_SESSION["iPortalPatientId"]."' 
				AND MID = '".$mID."'
				AND MSID = '".$msID."'
				AND folder_type = 1
				";
		$res = unserialize(mysql_query_i($qry));		
		if(count($res)<=0 || $res=='')
		return 0;
		else return 1;
	}
	function formatBytes($bytes, $precision = 2) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
	
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
	
		$bytes /= pow(1024, $pow);
	
		return round($bytes, $precision) . ' ' . $units[$pow];
	} 
	
	function saveFile($filename,$contents,$mimeType){
		$unlocked_and_unique = FALSE;
		while(!$unlocked_and_unique){
		$name = time() . "_" . $filename;
		$outfile = fopen($this->save_directory.$name,'w');
		if(flock($outfile,LOCK_EX)){
			$unlocked_and_unique = TRUE;
		}else{
			flock($outfile,LOCK_UN);
			fclose($outfile);
		}
		}
		fwrite($outfile,$contents);
		fclose($outfile);
		$this->arrMail['attachment'][] = array("name"=> $name,
											   "size"=> $this->formatBytes(filesize($this->save_directory.$name)),
											   "mime"=> $mimeType,
											   "complete_path"=> "/UserId_".$_SESSION["iPortalPatientId"]."/mails/".$name
												);
	}
	
	function decodePart($body_part){
		//global $body,$debug;
		if(array_key_exists('name',$body_part->ctype_parameters)){
		$filename = $body_part->ctype_parameters['name'];
		}else if($body_part->ctype_parameters && array_key_exists('filename',$body_part->ctype_parameters)){ // hotmail
		$filename = $body_part->ctype_parameters['filename'];
		}/*else{
		$filename = "file";
		}*/
		if($this->debug){ 
		print "Found body part type {$body_part->ctype_primary}/{$body_part->ctype_secondary}\n"; 
		}
		$mimeType = "{$body_part->ctype_primary}/{$body_part->ctype_secondary}"; 
		
		switch($body_part->ctype_primary){
		case 'text':
		switch($body_part->ctype_secondary){
		case 'plain':
			if($filename == "" || $filename == "file"){
				$this->arrMail['body'] = $body_part->body; // If there are multiple text/plain parts, we will only get the last one.
			}
			if($filename != ""){
				$this->saveFile($filename,$body_part->body,$mimeType);
			}
			break;
		case 'xml':
			$this->saveFile($filename,$body_part->body,$mimeType);
			break;	 
		}
		break;
		case 'application':
		switch ($body_part->ctype_secondary){
		case 'pdf': // save these file types
		case 'zip':
		case 'octet-stream':
			$this->saveFile($filename,$body_part->body,$mimeType);
			break;
		default:
			// anything else (exe, rar, etc.) will faill into this hole and die
			break;
		}
		break;
		case 'image':
		switch($body_part->ctype_secondary){
		case 'jpeg': // Save these image types
		case 'png':
		case 'gif':
			$this->saveFile($filename,$body_part->body,$mimeType);
			break;
		default:
			break;
		}
		break;
		case 'multipart':
		if(is_array($body_part->parts)){
			foreach($body_part->parts as $ix => $sub_part){
			$this->decodePart($sub_part);
			}
		}
		break;
		default:
		break;
		}
	}
	
	function create_envelope($to_email,$subject,$from_email){
		include 'Mail/Mail.php';
		include 'Mail_Mime/mime.php' ;
		
		$text = $this->arrMail['body'];
		$html = "<html><body>$text</body></html>";
		
		$crlf = "\n";
		$hdrs = array(
					  'From'    => $from_email,
					  'Subject' => $subject,
					  'to' => $to_email
					  );
		
		$mime = new Mail_mime(array('eol' => $crlf));
		
		$mime->setTXTBody($text);
		$mime->setHTMLBody($html);
		/*foreach($this->arrMail['attachment'] as $arr){
			$file = $arr['complete_path'];
			$mime = $arr['mime'];
		}*/
		if(isset( $this->arrMail['attachment'])){
			foreach($this->arrMail['attachment'] as $file_to_attach_arr)
			{
				$file = $file_to_attach_arr['complete_path'];
			//$file ="D:/imw/apache/htdocs/R6-Dev/interface/main/uploaddir/users/UserId_1/mails/imedic-PLAIN-1382672918.xml";
				if($file != "")
				$file_ext_get = substr($file, -3);
				$target_mime_type = "";
				if(strtolower($file_ext_get) == "zip")
				{
					$target_mime_type = "application/zip";
				}
				else if(strtolower($file_ext_get) == "pdf")
				{
					$target_mime_type = "application/pdf";
				}
				else
				{
					$target_mime_type = "text/plain";	
				}
				
				$mime->addAttachment($file, $target_mime_type);				
			}
		}
		$body = $mime->get();
		$hdrs = $mime->headers($hdrs);
		return $mime->getMessage();
	}
}



?>
