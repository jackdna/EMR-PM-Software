<?php
/*****BASH COMMAND FORMAT*********
FORMAT: <command> <action> <practicename> <interfaceID>
EXMPLE: hl7oubound start berkeleyeye 1234
*********************************/

$ignoreAuth = true;

//$argv[1] = 'idoc'; //FOR TESTING PURPOSE. REMOVE IN PRODUCTION FILE.
//$argv[2] = 1; //FOR TESTING PURPOSE. REMOVE IN PRODUCTION FILE.

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
	$out_config_array 	= $objHL7Engine->get_connection_details($connectionID);
	if(is_array($out_config_array)){
		$interface_id		= $out_config_array['interface_id'];
		$connectivity		= $out_config_array['connectivity'];
		$ip_domain			= $out_config_array['ip_domain'];
		$port				= $out_config_array['port'];
		$un					= $out_config_array['un'];
		$pw					= $out_config_array['pw'];
		$path				= $out_config_array['path'];
		$ack_wait_seconds 	= intval($out_config_array['ack_wait_seconds']);
		$module_name 		= $out_config_array['application_module'];
		$MessageType		= $objHL7Engine->check_other_msgtypes_on_same_destination($connectionID,$out_config_array['msg_type']);
		
		if( !$MessageType ){
			$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,'MessageTypes not found.');
		}else{
			$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,'Starting '.$connectivity.' sender.');
		
			$outbound_file_name	= dirname(__FILE__)."/sender_".$connectivity.'.php';
			if(file_exists($outbound_file_name) && is_file($outbound_file_name)){
				$unsent_messages = $objHL7Engine->get_pending_hl7_outbound($interface_id,$MessageType);// Getting pending outbound messages.
				$recheck_pending_messages = false;
				require_once($outbound_file_name);
			}else{
				write_my_failures("",'Outbound file not found with name: '.$outbound_file_name);
			}
		}
	}else{
		write_my_failures("",'Outbound connection details not found for connection ID: '.$connectionID);
	}	
}else{
	die('Provide Connection ID');
}
?>