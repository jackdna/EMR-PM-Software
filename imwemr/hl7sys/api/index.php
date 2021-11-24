<?php
set_time_limit(0);
$ignoreAuth = true;

/*Set Practice Name - for dynamically including config file*/
//$argv[1] = 'imwemr-Dev';
if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}
require_once(dirname(__FILE__)."/../../config/globals.php");

$GetMsgType = isset($_POST['MsgType']) ? xss_rem($_POST['MsgType']) : '';
if(empty($GetMsgType)) die('No message type provided');//CANNOT PROCEED FURTHER IF NO MSGTYPE SENT TO GENERATE.

$GetMsgFor 	= isset($_POST['MsgFor']) 	? xss_rem($_POST['MsgFor']) 	: '';//OTHER EMR NAME.
$GetPatId 	= isset($_POST['PatId']) 	? xss_rem($_POST['PatId']) 		: '';//PATIENT ID. (IDOC)
$GetSchId 	= isset($_POST['SchId']) 	? xss_rem($_POST['SchId']) 		: '';//SCHEDULE APPOINTMENT ID
$GetSubMsgType = isset($_POST['SubMsgType']) 	? xss_rem($_POST['SubMsgType'])	: '';//SCHEDULE APPOINTMENT Trigger ID or ADT msg event type.
$GetSchEVId = isset($_POST['SchEVId']) 	? xss_rem($_POST['SchEVId'])	: '';//SCHEDULE APPOINTMENT Event ID
$GetDchId 	= isset($_POST['DchId']) 	? xss_rem($_POST['DchId']) 		: '';//DISCHARGE SUMMARY ID
$GetPCLId 	= isset($_POST['PCLId']) 	? xss_rem($_POST['PCLId']) 		: '';//Patient Charge List ID
$GetCallFrom= isset($_POST['CallFrom']) ? xss_rem($_POST['CallFrom']) 	: '';//Script call from (idoc/scemr/iolink).
//pre($_POST,1);
switch($GetMsgType){
	case 'SIU':{
		/*Include iDoc hl7 library to create SIU message*/
		require_once(dirname(__FILE__).'/../hl7GP/hl7FeedData.php');
		$hl7 = new hl7FeedData();
		$hl7->PD['id'] = $GetPatId;
		$hl7->PD['schid'] = $GetSchId;
		$hl7->setTrigger('SIU', $GetSubMsgType);
		
		if( isset($GLOBALS['HL7_SIU_SEGMENTS']) && is_array($GLOBALS['HL7_SIU_SEGMENTS']) )	{
			foreach( $GLOBALS['HL7_SIU_SEGMENTS'] as $segment )	{
				$hl7->insertSegment($segment, 'SIU');
			}
		}
		$hl7->log_message();
		break;
	}
	case 'ADT':{
		require_once(dirname(__FILE__)."/../old/CLS_makeHL7.php");
		$makeHL7 = new makeHL7;
		$makeHL7->log_HL7_message($GetPatId, $GetSubMsgType);
		break;
	}
	
}


?>