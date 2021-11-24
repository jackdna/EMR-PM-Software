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
 * File: index.php
 * Purpose: Action File for Allscripts Integration Admin Interface
 * Access Type: Direct
*/

include_once '../../../config/globals.php';
if( !isset( $_POST['action'] ) || $_POST['action'] == '' )
{
	die( json_encode(array('message' => 'Invalid Request')) );
}

$action = $_POST['action'];
switch( $action ){
	
	case "saveCredentials":
		$appName	= trim( xss_rem( $_POST['app'] ) );
		$userName= trim( xss_rem( $_POST['appuser'] ) );
		$password	= imw_real_escape_string( trim( $_POST['apppassword'] ) );
		$appURL	= 	urlencode( trim( $_POST['appUrl'] ) );
		$uid = (int) xss_rem( $_POST['record_id'] );
		
		$ubqAppName	= trim( xss_rem( $_POST['upbApp'] ) );
		$ubqUserName= trim( xss_rem( $_POST['ubqAppuser'] ) );
		$ubqPassword= imw_real_escape_string( trim( $_POST['ubqApppassword'] ) );
		$ubqAppURL	= 	urlencode( trim( $_POST['ubqAppUrl'] ) );
		
		$ubqEnabled = ($_POST['ubqEnabled']==='true')?1:0;

		$useOrdId		= ($_POST['useOrdId']==='true')?1:0;
		$ehrUsername	= trim( xss_rem( $_POST['ehrUsername'] ) ); 
		$ehrPassword	= xss_rem( $_POST['ehrPassword'] ); 

		$sql = "UPDATE `as_credentials`
				SET `appname` =  '".$appName."',
					`username` = '".$userName."',
					`password` = '".$password."',
					`url` = '".$appURL."',
					`ubq_appname` = '".$ubqAppName."',
					`ubq_username` ='".$ubqUserName."',
					`ubq_password` = '".$ubqPassword."',
					`ubq_url` = '".$ubqAppURL."',
					`ubq_status` = '".$ubqEnabled."',
					`use_org_id` = '".$useOrdId."',
					`ehr_username` = '".$ehrUsername."',
					`ehr_password` = '".$ehrPassword."'
				WHERE `id` = ".$uid;

		$resp = imw_query($sql);
		echo $resp;
		
		/*Unser AllScripts SessionData and ServerLog Entry*/
		if( isset($_SESSION['as_token']) )
			unset($_SESSION['as_token']);
		if( isset($_SESSION['as_token_time']) )
			unset($_SESSION['as_token_time']);
		if( file_exists(data_path().'as_data/serverInfoLog.txt') )
			file_put_contents(data_path().'as_data/serverInfoLog.txt', '');
		/*Unser AllScripts SessionData*/
		break;
	case "syncData":
		
		if( isset($_POST['option']) && $_POST['option'] === '' )
		{
			exit( json_encode(array('message' => 'Invalid Option')) );
		}
		
		$response = array();
		
		require_once( $GLOBALS['srcdir'].'/allscripts/as_base.php' );
		require_once( $GLOBALS['srcdir'].'/allscripts/as_dictionary.php' );
		$dictionary = new as_dictionary();
		
		header('Content-Type: text/octet-stream');
		header('Cache-Control: no-cache');
		
		$syncOption = $_POST['option'];
		
		$response['type'] = 'progress';
		$response['message'] = 'Sending Request to AllScripts';
		echo '~~'.json_encode( $response ). PHP_EOL;
		ob_flush();
		flush();
		
		$data = array();
		
		try{
			if( $syncOption == 'clinical_progress' )
				$data = $dictionary->clinicalProgress();
			elseif( $syncOption == 'problem_category' )
				$data = $dictionary->problemCategories();
			elseif( $syncOption == 'clinical_severity' )
				$data = $dictionary->clinicalSeverity();
			elseif( $syncOption == 'problem_status' )
				$data = $dictionary->problemStatus();
			elseif( $syncOption == 'allergy_status' )
				$data = $dictionary->allergyStatus();
			elseif( $syncOption == 'allergy_category' )
				$data = $dictionary->allergyCategory();
			elseif( $syncOption == 'allergen_reaction' )
				$data = $dictionary->allergenReaction();
			elseif( $syncOption == 'alert_type' )
				$data = $dictionary->alertTypes();
			elseif( $syncOption == 'relationship' )
				$data = $dictionary->relations();
			elseif( $syncOption == 'laterality_qualifier' )
				$data = $dictionary->laterality();
			else
				throw new asException( 'Call Error', 'Invalid Request' );
			
			if( count( $data ) == 0 )
				throw new asException( 'API Error', 'Blank data returned by allscripts.' );
			
			$response['type'] = 'progress';
			$response['message'] = 'Data Received from AllScripts.<br />Processing Started. Total Items to be Processed: '.count( $data );
			echo '~~'.json_encode( $response ) . PHP_EOL ;
			ob_flush();
			flush();
			
			foreach( $data as $key=>$item )
			{
				$sql = 'INSERT INTO `as_dictionary` SET ';
				$sqlFlag = false;
				foreach( $item as $itemkey => $itemData )
				{
					$sql .= '`'.$itemkey.'` = \''.$itemData.'\',';
					$sqlFlag = true;
				}
				$sql = rtrim($sql, ',');
				
				if( $sqlFlag ){
					imw_query( $sql );
					$response['type'] = 'progress';
					$response['message'] = ($key+1).' item(s) Processed';
					echo '~~'.json_encode( $response ) . PHP_EOL ;
					ob_flush();
					flush();
				}
			}
			
			echo '~~~';
			$response['type'] = 'success';
			$response['message'] = 'Directory Synced';
			echo json_encode($response);
		}
		catch( asException $e)
		{
			
			$response = $e->showMessage();
			echo '~~~'.json_encode( $response ) . PHP_EOL ;
			ob_flush();
			flush();
			
			/*Log Error text to file*/
			$errData = $e->date."\n".($e->getErrorText())."\n".$e->getTraceAsString()."\n";
			file_put_contents(data_path().'as_data/error.txt', $errData, FILE_APPEND );
			//file_put_contents( $GLOBALS['srcdir'].'/allscripts/data/error.txt', $errData, FILE_APPEND );
		}
		break;
	case "syncProblem":
		
		ini_set('max_execution_time', 0);
		
		$response = array();
		
		require_once( $GLOBALS['srcdir'].'/allscripts/as_base.php' );
		require_once( $GLOBALS['srcdir'].'/allscripts/as_dictionary.php' );
		$dictionary = new as_dictionary();
		
		header('Content-Type: text/octet-stream');
		header('Cache-Control: no-cache');
		
		$response['type'] = 'progress';
		$response['message'] = 'Syncing Problems List...';
		echo '~~'.json_encode( $response ). PHP_EOL;
		ob_flush();
		flush();
		
		$start = (isset($_POST['start']))?(int)$_POST['start']:0;
		$newCounter = (isset($_POST['newCounter']))?(int)$_POST['newCounter']:0;
		
		$sql = 'SELECT `diag_description` FROM `diagnosis_code_tbl` ORDER BY `diagnosis_id` LIMIT '.$start.', 50';
		$resp = imw_query($sql);
		
		$totalRows = imw_num_rows($resp);
		
		if( $resp && $totalRows > 0 )
		{
			unset($response['message']);
			while( $row = imw_fetch_assoc($resp) )
			{
				$data = array();
				$data = $dictionary->problem($row['diag_description']);
				
				foreach( $data as $key=>$item )
				{
					$sql1 = 'INSERT INTO `as_problems` SET ';
					$sqlFlag = false;
					
					foreach( $item as $itemkey => $itemData )
					{
						$sql1 .= '`'.$itemkey.'` = \''.$itemData.'\',';
						$sqlFlag = true;
					}
					$sql1 = rtrim($sql1, ',');
					
					if( $sqlFlag ){
						if( imw_query( $sql1 ) )
						{
							$newCounter += 1;
							$response['type'] = 'problemProgress';
							$response['data'] = array( 'added' => $newCounter, 'processed' => $start);
							echo '~~'.json_encode( $response ). PHP_EOL;
							ob_flush();
							flush();
						}
					}
				}
				
				$start += 1;
				$response['type'] = 'problemProgress';
				$response['data'] = array( 'added' => $newCounter, 'processed' => $start);
				echo '~~'.json_encode( $response ). PHP_EOL;
				ob_flush();
				flush();
			}
		}
		imw_free_result($resp);
		
		if( $totalRows == 50)
		{
			echo '~~~';
			$response = array();
			$response['type'] = 'continue';
			$response['data'] = array( 'added' => $newCounter, 'processed' => $start);
			echo json_encode($response);
		}
		else
		{
			echo '~~~';
			$response = array();
			$response['type'] = 'success';
			$response['message'] = 'Problems Directory Sync Completed.';
			echo json_encode($response);
		}
		break;
}

?>