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

File: HL7_to_DB.php
Purpose:  Check posted HL7 message and save in DB, parse and process the message.
Access Type: Direct Access
*/
ini_set('max_execution_time', 90);
$ignoreAuth = true;
require_once(dirname(__FILE__)."/../../config/globals.php");

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$nowDate = date('Y-m-d H:i:s');

$hl7Message = trim( $_REQUEST['data'] );
$hl7Message = base64_decode($hl7Message);

if( $hl7Message != '' )
{
	require_once( dirname(__FILE__).'/hl7Parser.php' );

	$hl7 = new hl7Parser;
	$hl7->parse($hl7Message);

	$senderApp		= $hl7->MSH('sending_application');
	$senderFac		= $hl7->MSH('sending_facility');
	$recvdMsgType	= trim($hl7->MSH('message_type'));
	$recvdMsgId		= trim($hl7->MSH('message_control_id'));
	$msgMode		= $hl7->MSH('processing_id');
	$msgVersion		= $hl7->MSH('version_id');

	$sql = 'INSERT INTO `hl7_received`
			SET
				`msg` = \''.addslashes($hl7Message).'\', 
				`msg_type` = \''.addslashes($recvdMsgType).'\',
				`msg_mode` = \''.$msgMode.'\',
				`saved_on` = \''.$nowDate.'\',
				`received_from` = \''.addslashes($senderApp).'\',
				`sender_facility` = \''.addslashes($senderFac).'\',
				`msg_control_num` = \''.addslashes($recvdMsgId).'\'';
	imw_query($sql);
	$logId	= imw_insert_id();

	$ACK = 'MSH|^~\&|imwemr|imwemr|'.$senderApp.'|'.$senderFac.'|'.date('YmdHis').'||ACK^O01|'.$logId.'|P|2.4'.chr(13);

	$recvdMsgType = strtolower($recvdMsgType);

	$status = 'A';
	$statusText = '';

	try
	{
		if( $recvdMsgType === 'dft' )
		{
			include_once( dirname(__FILE__).'/dft.php' );
		}
		else
		{
			throw new Exception('Message type not supported', 2);
		}

		$ACK .= 'MSA|AA|'.$recvdMsgId;
	}
	catch(Exception $e)
	{
		$acknowledgementCode = $e->getCode();
		$acknowledgementCode = ( $acknowledgementCode === 2) ? 'AR' : 'AE';

		$statusText = $e->getMessage();
		$ACK .= 'MSA|'.$acknowledgementCode.'|'.$recvdMsgId.'|'.$statusText;

		$status = 'R';
	}
	unset($hl7);

	/*update patient details in hl7 received log*/
	$patientId = (isset($patientId)) ? $patientId : 0;
	$sql = 'UPDATE `hl7_received`
			SET
				`patient_id` = '.$patientId.', `response` = \''.addslashes($ACK).'\',
				`status` = \''.$status.'\', `status_text` = \''.$statusText.'\'
			WHERE
				`id` = '.$logId;
	imw_query($sql);

	echo $ACK;
}


?>