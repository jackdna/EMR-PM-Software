<?php
include_once(dirname(__FILE__)."/class.ClientSocket.php");
$EmptyACKCount = 0;
do{
	if($recheck_pending_messages) $unsent_messages = $objHL7Engine->get_pending_hl7_outbound($interface_id,$MessageType);
		if(count($unsent_messages)>0){
		//CREATING SOCKET
		try {
			if(isset($sc)){unset($sc);}
			$sc = new ClientSocket($ack_wait_seconds);
			//$sc->iReadTimeOut = $ack_wait_seconds;
			$sc->open($ip_domain,$port);
			do{
				if($recheck_pending_messages) $unsent_messages = $objHL7Engine->get_pending_hl7_outbound($interface_id,$MessageType);
				if(count($unsent_messages)>0){
					imw_close($GLOBALS['dbh']);
					if(isset($GLOBALS['dbh'])) unset($GLOBALS['dbh']);
					if(isset($db))	unset($db);
					$sqlport = '';
					if(isset($sqlconf["port"]) && !empty($sqlconf["port"])) $sqlport = $sqlconf["port"];
					$GLOBALS['dbh'] = imw_connect($sqlconf["host"], $sqlconf["login"], $sqlconf["pass"],$GLOBALS['dbh'],$sqlport);
					$db  = imw_select_db(IMEDIC_IDOC, $GLOBALS['dbh']);
					if(!$GLOBALS['dbh']){
						$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,'Unable to create DB connection.'.imw_error());
					}
					elseif(!$db){
						$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,'Unable to select DB. MySql Error: '.imw_error());
					}
					
					for($z=0;$z < count($unsent_messages); $z++){
						if($EmptyACKCount==1){
							sleep(10); //sleep for half a minute, if continue getting empty ACK.
						}
						
						if($EmptyACKCount==2){
							sleep(30); //sleep for half a minute, if continue getting empty ACK.
						}
						
						if($EmptyACKCount==3 ){
							sleep(300); //sleep for 5 minutes, if continue getting empty ACK.
						}
						
						if($EmptyACKCount>5){
							sleep(3600); //sleep for an hour, if continue getting empty ACK.
							break 2;
						}
						$rs = $unsent_messages[$z];
						$msgID 		= $rs['id'];
						$HL7msg		= $rs['msg'];
						$patientId	= $rs['patient_id'];
						
						$HL7msg     = chr(11).$HL7msg.chr(28).chr(13);

						$length 	= strlen($HL7msg);
						$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,'Trying to write on socket ('.$ip_domain.':'.$port.') for iMW MRN# '.$patientId.'.');
						$thisMsgSent = false;
						do	{
							try{
								$sc->send($HL7msg);
								$ack 		= $sc->recv($ack_wait_seconds);
							
								if(trim($ack)!=''){							
									$EmptyACKCount = 0;
								//	$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,'ACKTEXT: '.$ack);
									$q3	= "UPDATE hl7_interface_messages_out SET sent=1, sent_on='".date('Y-m-d H:i:s')."', response='".addslashes($ack)."' WHERE id='".$msgID."'";
									$res3 = imw_query($q3);
									if($res3 && trim($ack)!=''){
										$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,'Message status and ACK updated in records.');
										$thisMsgSent = true;
									}
									elseif(!$res3){
										//IF MYSQL FOUND NOT CONNECTED.
										imw_close($GLOBALS['dbh']);
										if(isset($GLOBALS['dbh'])) unset($GLOBALS['dbh']);
										if(isset($db))	unset($db);
										$sqlport = '';
										if(isset($sqlconf["port"]) && !empty($sqlconf["port"])) $sqlport = $sqlconf["port"];
										$GLOBALS['dbh'] = imw_connect($sqlconf["host"], $sqlconf["login"], $sqlconf["pass"],$GLOBALS['dbh'],$sqlport);
										$db  = imw_select_db(IMEDIC_IDOC, $GLOBALS['dbh']);
										if(!$GLOBALS['dbh']){
											$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,'Unable to create DB connection.'.imw_error());
										}
										elseif(!$db){
											$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,'Unable to select DB. MySql Error: '.imw_error());
										}
										$res3 	= imw_query($q3);
									}
								}else if(trim($ack)==''){
									$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,'ACK is <empty> or not received. Message ID '.$msgID.' re-queued.');
									$EmptyACKCount++;
									break 2;	// Moving out of for, at start of 2nd do..while.
								}/*else{
									$my_error = imw_error();
									LogResponse('MySql Error: '.$my_error.'. Program will resume itself after 10 seconds. (1st)');
									imw_close($GLOBALS['dbh']);
									if(isset($GLOBALS['dbh'])) unset($GLOBALS['dbh']);
									if(isset($db))	unset($db);
									$sqlport = '';
									if(isset($sqlconf["port"]) && !empty($sqlconf["port"])) $sqlport = $sqlconf["port"];
									$GLOBALS['dbh'] = imw_connect($sqlconf["host"], $sqlconf["login"], $sqlconf["pass"],$GLOBALS['dbh'],$sqlport);
									$db  = imw_select_db(IMEDIC_IDOC, $GLOBALS['dbh']);
									if(!$GLOBALS['dbh']){
										$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,'Unable to create DB connection.'.imw_error());
									}
									elseif(!$db){
										$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,'Unable to select DB. MySql Error: '.imw_error());
									}
									sleep(5);
									break 3;	// Moving out of while, at start of 2nd do..while.
								}*/
							}catch (Exception $e){
								$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,$e->getMessage());
								sleep(10);//sleep for 10 seconds if interace connection reported error.
								break 2;
							}
						}while(!$thisMsgSent);
					}					
				}
				//sleep(10);
				$recheck_pending_messages = true;
			}while(true);
		}catch (Exception $e){
			$objHL7Engine->make_log_of_action('hl7_interface_connection',$connectionID,'Unable to connect: '.$e->getMessage());
			sleep(5);//retry after 5 seconds.
		}
	}else{
		sleep(5);
	}
	$recheck_pending_messages = true;
}while(true);
?>