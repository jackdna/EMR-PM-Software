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
require_once(dirname(__FILE__)."/../../config/globals.php");
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

$data = false;

require_once( dirname(__FILE__).'/updoxFax.php' );
$updoxFax=new updoxFax();

/*Allow Request only from updox*/
if($updoxFax->validateInboundIP()){
	$data = trim(file_get_contents("php://input"));	/*Get Request payloads*/
}

/*Process data if posted by Updox webhook*/
		/*containers to hold updox accound id and faxId posted in call from webhook*/
	$account_id = '';
	$fax_id = '';
if( $data && $data !== '' ) {
	$data = json_decode($data);
	$account_id = imw_real_escape_string( trim($data->accountId) );
	$fax_id = get_number($data->faxId);
}

try{
	/*Save fax id account_id Id posted in call from webhook - create record for the fax in DB*/
	$entry_resp = false;
	$fax_row = false;
	if( $account_id !== '' && $fax_id !== '' ){
		/*Check if fax with same id is already received*/
		$sql_check = "SELECT `id`, `from_number` FROM `inbound_fax` WHERE `fax_id`='".$fax_id."'";
		$rep_check = imw_query($sql_check);
		
		$exists = false;
		if($rep_check && imw_num_rows($rep_check)>0)
			$exists  = true;
		
		if( !$exists ){
			/*Add new entry for fax*/
			$received_at = date('Y:m:d H:i:s');
			$entry_sql = "INSERT INTO `inbound_fax` SET `fax_id`='".$fax_id."', `updox_account_id`='".$account_id."', `received_at`='".$received_at."'";
			$entry_resp = imw_query($entry_sql);
			$fax_row = imw_insert_id();
            if($fax_row){
                require_once(dirname(__FILE__)."/../../interface/common/assign_new_task.php");
                inbound_fax_task($fax_row);
            }
		}
		else{
			$resp_data = imw_fetch_assoc($rep_check);
			/*If from number is blank the retry to get fax data*/
			if( trim($resp_data['from_number']) === '' ){
				$entry_resp = true;
				$fax_row = $resp_data['id'];
			}
			else
				throw new Exception('data exists');
		}
	}
	
	/*Query Updox to get PDF Data if DB record created for the fax*/
	if( $entry_resp !== false && $fax_row !== false ){
		
		$resp = $updoxFax->getFaxPDF($fax_id);
		
		if($resp['status'] === 'failed'){
			/*Record Error Message in DB*/
			$sql_error  = "UPDATE `inbound_fax` SET `message`='".imw_real_escape_string($resp['message'])."' WHERE `id`=".$fax_row;
			imw_query($sql_error);
			
			$status_code = ( isset($resp['statusCode']) )? $resp['statusCode']:false;
			if( substr($status_code, 0,1) == 5)
				throw new Exception('Temperory Error');
		}
		elseif($resp['status'] === 'success'){
			
			/*Chek if fax_directory exists, create if not*/
			$dir_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/fax_files";
			if( !is_dir($dir_path) ){
				mkdir( $dir_path, 0755, true );
				chown( $dir_path, 'apache' );
			}
			/*Traverse attachments - PDF files*/
			$data = $resp['data'];
			$files = array();
			$messages = array();
			
			/*Save PDF files*/
			foreach( $data->attachments as $attachment ){
				
				$fileName = trim($attachment->fileName);
				/*Chek if file with same name already exists. If yes then modify the name of new file to be saved*/
				if( file_exists($dir_path.'/'.$fileName) )
					$fileName = date('His').'-'.$fileName;
				
				$file_content = trim($attachment->content);
				$file_content = html_entity_decode($file_content);
				$file_content = base64_decode($file_content);
				
				$write_status = file_put_contents($dir_path.'/'.$fileName, $file_content);
				if( $write_status !== false )
					array_push($files, $fileName);	/*List files saved successfully*/
				else
					array_push($messages,$fileName);	/*File saving failed*/
			}
			
			/*Update fax record in DB with fax Data*/
			$from_number	= get_number($data->from);
			$to_number		= get_number($data->to);
			$files			= imw_real_escape_string( implode(',', $files) );
			$messages		= imw_real_escape_string( implode(',', $messages) );
			
			if( count($data->attachments) > 0){
				
				$sql_fax = "UPDATE `inbound_fax` SET
								`from_number`='".$from_number."',
								`to_number`='".$to_number."',
								`files`='".$files."',
								`message`='".$messages."'
								WHERE `id`=".$fax_row;
				imw_query($sql_fax);
			}
		}
		else
			throw new Exception('No data returned');
	}
}
catch(Exception $e){
	if( $e->getMessage() == 'data exists' )
		/*Set response code to conflict. If PDF data already exists.*/
		//http_response_code(409);
		header("HTTP/1.1 409");
	else
		/*Set response code to service unavailable. So, that Updox web hook can retry to push the fax data - if any error occoured.*/
		//http_response_code(503);
		header("HTTP/1.1 503");
}


?>