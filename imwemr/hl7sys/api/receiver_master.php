<?php
/*****BASH COMMAND FORMAT*********
FORMAT: <command> <action> <practicename> <interfaceID>
EXMPLE: hl7oubound start berkeleyeye 1234
*********************************/
set_time_limit(0);
$ignoreAuth = true;

//$argv[1] = 'idoc'; //FOR TESTING PURPOSE. REMOVE IN PRODUCTION FILE.
//$argv[2] = 3; //FOR TESTING PURPOSE. REMOVE IN PRODUCTION FILE.

$connectionID = false;
if($argv[1]){
	$practicePath = trim($argv[1]);
	if( isset($argv[2]) && trim($argv[2]) != ''){
		$connectionID = strtoupper(trim($argv[2]));
	}
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}

require_once(dirname(__FILE__)."/../../config/globals.php");

if($connectionID){
	require_once(dirname(__FILE__)."/class.HL7Engine.php");
	$objHL7Engine = new HL7Engine();
	$con_config_array 	= $objHL7Engine->get_connection_details($connectionID);
	if(is_array($con_config_array)){
		$interface_id		= $con_config_array['interface_id'];
		$connectivity		= $con_config_array['connectivity'];
		$ip_domain			= $con_config_array['ip_domain'];
		$port				= $con_config_array['port'];
		$un					= $con_config_array['un'];
		$pw					= $con_config_array['pw'];
		$path				= $con_config_array['path'];
		$msg_encryption		= $con_config_array['msg_encryption'];
		$static_ack_text	= $con_config_array['static_ack_text'];
		$ack_wait_seconds 	= intval($con_config_array['ack_wait_seconds']);
		$parsing_script		= $con_config_array['parsing_script'];
		$module_name 		= $con_config_array['application_module'];
		$MessageType 		= $con_config_array['msg_type'];
		if($MessageType){
			$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,'Starting '.$connectivity.' receiver.');
		}
		
		$inbound_file_name	= dirname(__FILE__)."/receiver_".$connectivity.'.php';
		if(file_exists($inbound_file_name) && is_file($inbound_file_name)){
			require_once($inbound_file_name);
		}else{
			write_my_failures("",'Inbound file not found with name: '.$inbound_file_name);
		}
	}else{
		write_my_failures("",'Inbound connection details not found for connection ID: '.$connectionID);
	}	
}else{
	die('Provide Connection ID');
}
?>