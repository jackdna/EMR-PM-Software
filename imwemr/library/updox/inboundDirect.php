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

$ignoreAuth = true;

/*Set Practice Name - for dynamically including config file*/
$listenerCount = 0;
if( $argv[1] )
		{
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}

require_once(dirname(__FILE__)."/../../config/globals.php");
include($GLOBALS['srcdir'].'/updox/updoxDirect.php');
$updox = new updoxDirect();

$data = false;
$received_at = date('Y:m:d H:i:s');

/*Allow Request only from updox*/
if($updox->validateInboundIP()){

	if( $argv[1] && isset($argv[2]) && !empty($argv[2]) )
	{
		$data = data_path().'updoxPendingInboundDirect/'.$argv[2];
		$data = file_get_contents($data);
	}
	else
	{
		$data = trim(file_get_contents("php://input"));	/*Get Request payloads*/
	}
	
}

/*Process data if posted by Updox webhook*/
/*containers to hold updox accound id and userId posted in call from webhook*/
	$account_id = '';
	$user_id = '';
if( $data && $data !== '' ) {
	$data = json_decode($data);
	$account_id = imw_real_escape_string( trim($data->accountId));
	$user_id = trim($data->userId);
	$msg_id = get_number($data->messageId);
}

try{
	/*Save user id account_id Id posted in call from webhook - create record for the user in DB*/
	$entry_resp = false;
	$direct_row = false;
	if( $account_id !== '' && $user_id !== '' ){
		
		$sel_usr=imw_query("select id from users where updox_user_id='$user_id'");
		$row_usr=imw_fetch_array($sel_usr);
		$imedic_user_id=$row_usr['id'];
		
		if($imedic_user_id<=0){
			throw new Exception('Updox user not exists');
		}
		
		/*Check if message with same id is already received*/
		$sql_check = "SELECT id,from_email FROM direct_messages WHERE `MID`='".$msg_id."'";
		$rep_check = imw_query($sql_check);
		$exists = false;
		if($rep_check && imw_num_rows($rep_check)>0)
			$exists  = true;
		
		if( !$exists ){
			/*Add new entry for message*/
			$entry_sql = "INSERT INTO `direct_messages` SET `MID`='".$msg_id."',imedic_user_id = '".$imedic_user_id."',`updox_user_id`='".$user_id."', `local_datetime`='".$received_at."'";
			$entry_resp = imw_query($entry_sql);
			$direct_row = imw_insert_id();
		}
		else{
			$resp_data = imw_fetch_assoc($rep_check);
			/*If from number is blank the retry to get message data*/
			if( trim($resp_data['from_email']) === '' ){
				$entry_resp = true;
				$direct_row = $resp_data['id'];
			}
			else
				throw new Exception('data exists');
		}
	}
	
	/*Query Updox to get PDF Data if DB record created for the message*/
	if( $entry_resp !== false && $direct_row !== false ){
		
		$resp = $updox->getMessage($msg_id,$user_id);
		
		if($resp['status'] === 'failed'){
			/*Record Error Message in DB*/
			$sql_error  = "UPDATE `direct_messages` SET `message`='".imw_real_escape_string($resp['message'])."' WHERE `id`=".$direct_row;
			imw_query($sql_error);
			
			$status_code = ( isset($resp['statusCode']) )? $resp['statusCode']:false;
			if( substr($status_code, 0,1) == 5)
				throw new Exception('Temperory Error');	
		}
		elseif($resp['status'] === 'success'){

			/*Chek if fax_directory exists, create if not*/
			$dir_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/users/UserId_".$imedic_user_id."/mails";
			if( !is_dir($dir_path) ){
				mkdir( $dir_path, 0755, true );
				chown( $dir_path, 'apache' );
			}
			/*Traverse attachments - PDF files*/
			$msg_data = $resp['data'];
			$files = array();
			$messages = array();
			
			/*Update direct message record in DB with direct message Data*/
			$sql_ins = "update direct_messages SET 
						to_email = '".$msg_data->to->directAddress."',
						from_email = '".$msg_data->from->directAddress."',
						subject = '".$msg_data->subject."',
						message = '".$msg_data->textMessage."',
						folder_type = '1',
						direct_datetime = '".$received_at."'
						where MID='".$msg_data->messageId."'";
			imw_query($sql_ins);	
			
			/*Save PDF files*/
			foreach( $msg_data->attachments as $attachment ){
				
				$attach_id=$attachment->id;
				$fileName=$attachment->name;
				$fileMimeType=$attachment->mimeType;
				
				$attach_resp = $updox->getMessageAttachment($attach_id,$user_id);
				$attach_data = $attach_resp['data'];

				/*Chek if file with same name already exists. If yes then modify the name of new file to be saved*/
				if( file_exists($dir_path.'/'.$fileName) )
					$fileName = date('His').'-'.$fileName;
				
				$file_content = trim($attach_data->content);
				$file_content = html_entity_decode($file_content);
				$file_content_bk = $file_content;
				
				$file_content = base64_decode($file_content);
				
				//Performing String Repairs for Recevied content -- get Mime Type for XML
				if(function_exists('finfo_open') == true){
					$f = finfo_open();
					$mime_type = finfo_buffer($f, $file_content, FILEINFO_MIME_TYPE);
					
					//If mime type is XML
					$repairedString = '';
					if(strpos(strtolower($mime_type), 'xml') == true){
						
						//Checking and repairing xml string using PHP Tidy Class only if extention is loaded
						if(extension_loaded('tidy') == true){
							ob_start();
								echo $file_content;
							
							//Getting Decoded data from updox	
							$buffer = ob_get_clean();
							
							//Cleaning and correcting the xml string
							$repairedString = tidy_repair_string($buffer, array( 
							    'output-xml' => true, 
							    'input-xml' => true 
							)); 
						}
					}
					if(empty($repairedString) == false) $file_content = $repairedString;
				}
				
				$write_status = file_put_contents($dir_path.'/'.$fileName, $file_content);
				
				/*Fix for corrupt Zip file*/
				if( strtolower(substr($fileName, -4)) === '.zip')
				{
					$zip = new ZipArchive;
					$zipOpen = $zip->open($dir_path.'/'.$fileName);
					$zip->close();
					
					if( $zipOpen !== true )
					{
						$i = 1;
						
						$flag = true;
						while($flag)
						{
							
							$char = substr($file_content_bk, -$i, 1);
							
							$i++;
							
							if($i>6 || $char != 'A')
							{
								$flag = false;
								$i = $i-2;
							}
						}
						
						if( $i<6 )
						{
							$loop = 6-$i;
							
							while($loop>0)
							{
								$file_content_bk .= 'A';
								$loop--;
							}
						}
						
						$file_content_bk = base64_decode($file_content_bk);
						file_put_contents($dir_path.'/'.$fileName, $file_content_bk);
					}
				}
				/*End Fix for corrupt Zip File*/
				
				$file_size=filesize($dir_path.'/'.$fileName);
				$file_size=round($file_size / 1024, 2).' KB';
				
				$complete_path="/UserId_".$imedic_user_id."/mails/".$fileName;
				
				$sql_ins = "INSERT INTO direct_messages_attachment SET 
							direct_message_id = '".$direct_row."',
							file_name = '".$fileName."',
							size = '".$file_size."',
							mime = '".$fileMimeType."',
							complete_path = '".imw_real_escape_string($complete_path)."'";
			   imw_query($sql_ins);	
			}
			
			$updox->setMessageMark($msg_id,$user_id);
		}
		else
			throw new Exception('No data returned');
	}
}
catch(Exception $e){
	if( $e->getMessage() == 'data exists' )
		/*Set response code to conflict. If PDF data already exists.*/
		// http_response_code(409);
		header("HTTP/1.1 409");
	else
		/*Set response code to service unavailable. So, that Updox web hook can retry to push the fax data - if any error occoured.*/
		// http_response_code(503);
		header("HTTP/1.1 503");
}

?>