<?php
//require_once(dirname(__FILE__).'/../../../config/globals.php');
//require_once(dirname(__FILE__).'/../../receiver/commonFunctions.php');

/* Turn on implicit output flushing so we see what we're getting as it comes in. */
ob_implicit_flush();
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($sock === false) {
    $msg = "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
	$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,$msg);
}else{
	if(socket_bind($sock, $ip_domain, $port) === false){
		$msg = "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
		$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,$msg);
	}
	if (socket_listen($sock, 5) === false) {
		$msg = "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
		$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,$msg);
	}
	
	//do listening always.
	do{
		/****CHECK AGAIN FOR LISTENING; IF FAILED, UNSET AND SET AGAIN***/
		if (socket_listen($sock, 5) === false) {
			$msg = "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . " :: \n";
			$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,$msg);
			
			//****UNSETTING ALL BEFORE RESETTING**/
			@socket_close($sock);
			@socket_close($msgsock);
			unset($sock);
			unset($msgsock);
			
			if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
				$msg = "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . " :: \n";
				$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,$msg);
			}
			/*
			if (!socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1)) {
				echo 'Unable to set option on socket: '. socket_strerror(socket_last_error()) . "\n";
			}
			*/
			if (socket_bind($sock, $address, $port) === false) {
				$msg = "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . " :: \n";
				$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,$msg);
			}
			
			if (socket_listen($sock, 5) === false) {
				$msg = "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . " :: \n";
				$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,$msg);
			}
		}
		
		if (($msgsock = @socket_accept($sock)) === false) {
			$msg = "socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . " :: \n";
			$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,$msg);
			break;
		}		
	   
		$bufAll="";   //to contains all the received data.
		do {
			$buf = @socket_read($msgsock, 2048, PHP_NORMAL_READ);
			if (false === $buf) {
				$msg = "socket_read() failed: reason: " . socket_strerror(socket_last_error($msgsock)) . "\n";
				$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,$msg);
				break;
			}
			if (trim($buf) == 'quit') {
				$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,"QUIT signal received."."\n");
				break;
			}
			if (trim($buf) == 'shutdown') {
				$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,"SHUTDOWN signal received."."\n");
				socket_close($msgsock);	
				break;
			}        
		
			//WRITE ENTIRE ---    
			try{
				//uncomment line below to write line by line received data.
				//$objHL7Engine->make_log_of_action($buf);
				
				$bufAll.="".$buf;
				if(strpos($buf,"\034")!==false || $buf == chr(28)){
					//$objHL7Engine->make_log_of_action("File Separator received.");
					$bufAll_tr=trim($bufAll);
					if(!empty($bufAll_tr)){
						$res = $objHL7Engine->post_message_for_parsing($bufAll, $parsing_script,$msg_encryption,$connectionID);
						$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,"Message Posted to DB_ACK_SYSTEM. ".$res);
						if( !empty( $static_ack_text ) )
						{
							$res = $static_ack_text;
						}
						$res = chr(11).$res.chr(28).chr(13);						
						$bufAll="";
						$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,"Writing back ACK: ".$res);
						$ack_write = socket_write($msgsock, $res, strlen($res));
						$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,"ACK ".$ack_write." bytes written successfully.");
						sleep(2);
					}
				}
			}catch(Exception $e){
				$msg = 'Caught exception: '.  $e->getMessage(). "\n";
				$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,$msg);
			}
		} while (true); 
	
		//When connect stop --
		if(!empty($bufAll)){
			$res = $objHL7Engine->post_message_for_parsing($bufAll, $parsing_script,$msg_encryption);
			$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,"Message Posted to DB_ACK_SYSTEM. ".$res);
			$bufAll="";
		}
		
		//When connect stop --
		socket_close($msgsock);    
		
	}while (true);
}
if($sock) socket_close($sock);
?>