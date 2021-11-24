<?php
if(isset($_POST['msg_type']) && isset($_POST['data'])){
	$ignoreAuth = true;

	/*Set Practice Name - for dynamically including config file*/
	$MessageType = false;
	if($argv[1]){
		$practicePath = trim($argv[1]);
		if( isset($argv[2]) && trim($argv[2]) != ''){
			$MessageType = strtoupper(trim($argv[2]));
		}
		$_SERVER['REQUEST_URI'] = $practicePath;
		$_SERVER['HTTP_HOST']= $practicePath;
	}
	
	require_once(dirname(__FILE__)."/../../config/globals.php");
	
	
	error_reporting(-1);
	ini_set('display_errors', 1);
	
	/*Set Practice Name - for dynamically including config file*/
	include_once(dirname(__FILE__)."/class.ClientSocket.php");
	
	if(isset($GLOBALS["HL7_SENDER"])){
		$socket_config_array = $GLOBALS["HL7_SENDER"];
	}
	$MessageType = strtoupper(trim($_POST['msg_type']));
	if($MessageType && isset($GLOBALS['HL7_SENDER_ARRAY'][$MessageType])){
		//if(isset($GLOBALS['HL7_SENDER_ARRAY'][$MessageType]))
		$socket_config_array = $GLOBALS['HL7_SENDER_ARRAY'][$MessageType];
	}
	
	$HL7msg 	= trim($_POST['data']);
	$address	= $socket_config_array['IP'];
	$port		= $socket_config_array['PORT'];
	$AckWaitTime = intval($socket_config_array['ACK_WAIT']);
	echo ('Connection attempt will be made for IP: '.$address.' PORT: '.$port.'.<br>');
  //  $address = '10.11.25.27';
  // $port   = 43022;


	if(isset($sc)){unset($sc);}
	$sc = new ClientSocket();
	$sc->iReadTimeOut = $AckWaitTime;
	$sc->open($address,$port);
	
	$HL7msg     = chr(11).$HL7msg.chr(28).chr(13);

	$length 	= strlen($HL7msg);
	echo ('Trying to write on socket ('.$address.':'.$port.').<br>');
	
	try{
		$sc->send($HL7msg);
		$ack 	= $sc->recv();
		if($ack!=''){
			echo($ack.'<br>');
		}									
		if(trim($ack)!=''){
			echo 'ACK Received.<br>';
		}
		elseif(trim($ack)==''){
			echo ('ACK is <empty> or not received. This message is re-queued.<br>');
		}else{
			echo 'Exception..<br>';
		}
	}catch (Exception $e){
		echo ($e->getMessage());
	}
	die;
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Message Outbond Tested</title>
</head>

<body style="margin:10px;">
<form method="POST" target="frame_result">
<label>Messge Type: </label><select name="msg_type"><option value="ADT">ADT</option><option value="SIU">SIU</option><option value="DFT">DFT</option></select><br /><br />
HL7 MESSAGE<BR />
<textarea style="width:90%; height:150px; border:2px solid #ccc;" name="data"></textarea>
<br /><br />
<input type="submit" value="Send Message" />
</form>
<hr /><BR />
HL7 RESPONSE<BR />
<iframe style="width:90%; height:250px; border:2px solid #ccc;" name="frame_result"></iframe>
</body>
</html>
