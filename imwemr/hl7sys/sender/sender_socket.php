<?php

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
include_once(dirname(__FILE__)."/class.ClientSocket.php");
include_once(dirname(__FILE__)."/commonFunctions.php");
include_once(dirname(__FILE__)."/../hl7GP/hl7Create.php");

$socket_config_array = $GLOBALS["HL7_SENDER"];
if($MessageType){
	if(isset($GLOBALS['HL7_SENDER_ARRAY'][$MessageType]))
		$socket_config_array = $GLOBALS['HL7_SENDER_ARRAY'][$MessageType];
	else
		die('Invalid message type provided.');
}

$address	= $socket_config_array['IP'];
$port		= $socket_config_array['PORT'];
$AckWaitTime = intval($socket_config_array['ACK_WAIT']);

if($MessageType){
	LogResponse('Sender service started for IP: '.$address.', PORT: '.$port.' MsgType: '.$MessageType);
}

$hl7Class = new hl7Create();
$flagFile = $hl7Class->hl7FlagPath();
$flagFile .= DIRECTORY_SEPARATOR.'senderCheckDB.log';

do{
	$flag_file_content = (int)file_get_contents($flagFile);
	$db_check_flag = intval($flag_file_content);
	if($db_check_flag=='1'){
		//CREATING SOCKET
		try {
			if(isset($sc)){unset($sc);}
			$sc = new ClientSocket();
			$sc->iReadTimeOut = $AckWaitTime;
			$sc->open($address,$port);
			
			do{
				$flag_file_content = (int)file_get_contents($flagFile);
				$db_check_flag = intval($flag_file_content);
				
				if($db_check_flag=='1'){
					imw_close($GLOBALS['dbh']);
					if(isset($GLOBALS['dbh'])) unset($GLOBALS['dbh']);
					if(isset($db))	unset($db);
					$sqlport = '';
					if(isset($sqlconf["port"]) && !empty($sqlconf["port"])) $sqlport = $sqlconf["port"];
					$GLOBALS['dbh'] = imw_connect($sqlconf["host"], $sqlconf["login"], $sqlconf["pass"],constant('IMEDIC_IDOC'),$sqlport);
					$db  = imw_select_db(constant('IMEDIC_IDOC'), $GLOBALS['dbh']);
					if(!$GLOBALS['dbh']){
						LogResponse('Unable to create DB connection.'.imw_error());
					}
					elseif(!$db){
						LogResponse('Unable to select DB. MySql Error: '.imw_error());
					}
					
					$main_wherepart = "";
					if($MessageType){
						switch($MessageType){
							case 'ADT':
								$main_wherepart = " AND msg_type IN ('ADT','Add_New_Patient','Update_Patient','')";
								break;
							case 'SIU':
								if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye'))){ sleep(5);}
								$main_wherepart = " AND msg_type IN ('SIU','update_appointment','add_appointment','cancel_appointment','cancel_appointemnt','reschedule_appointment','book_appointemnt','book_appointment','checkIn_appointment','reschedule_appointemnt')";
								break;
							case 'DFT':
								$main_wherepart = " AND msg_type IN ('DFT','Detailed Financial Transaction')";	
								break;
							case 'PTVISITORU':
								$main_wherepart = " AND msg_type IN ('PTVISIT_ORU')";	
								break;
						}
					}
					
					$res = imw_query("SELECT * FROM hl7_sent WHERE sent=0".$main_wherepart); //IF UNSENT MESSAGES FOUND
					
					if($res && imw_num_rows($res)>0){
						while($rs = imw_fetch_assoc($res)){
							$sentFlag	= (int)$rs['sent'];
							if(!$sentFlag){
								$msgID 		= $rs['id'];
								$HL7msg		= $rs['msg'];
								$patientId	= $rs['patient_id'];
								
								$HL7msg     = chr(11).$HL7msg.chr(28).chr(13);
	
								$length 	= strlen($HL7msg);
								LogResponse('Trying to write on socket ('.$address.':'.$port.') for iMW MRN# '.$patientId.'.');
								$thisMsgSent = false;
								do{
								try{
									$sc->send($HL7msg);
									$ack 		= $sc->recv();
									if($ack!=''){
										LogResponse($ack);
									}									
									if(trim($ack)!=''){
										$response = read_hl7_ACK(trim($ack));
										$status = $status_text = '';
										if($response['error']=='NoACK'){
											sleep(1);
											break 3;
										}
										elseif($response['error']==NULL){
											$resArr = $response['response'];
											if(is_array($resArr)){
												$status 	= $resArr['status'];
												$status_text= $resArr['status_text'];
											}
										}
										
										$q3		= "UPDATE hl7_sent SET 
												sent=1, 
												sent_on='".date('Y-m-d H:i:s')."', 
												response='".addslashes($ack)."', 
												status='".$status."', 
												status_text='".addslashes($status_text)."'  
												WHERE id=".$msgID." AND sent=0";
										$res3 	= imw_query($q3);
										if($res3 && trim($ack)!=''){
											LogResponse('Message+ACK saved in DB successfully.');
											$thisMsgSent = true;
										}
										elseif(!$res3){
											imw_close($GLOBALS['dbh']);
											if(isset($GLOBALS['dbh'])) unset($GLOBALS['dbh']);
											if(isset($db))	unset($db);
											
											$GLOBALS['dbh'] = imw_connect($sqlconf["host"], $sqlconf["login"], $sqlconf["pass"], constant('IMEDIC_IDOC'), $sqlconf["port"]);
											$db  = imw_select_db(constant('IMEDIC_IDOC'), $GLOBALS['dbh']);
											if(!$GLOBALS['dbh']){
												LogResponse('Unable to create DB connection.'.imw_error());
											}
											elseif(!$db){
												LogResponse('Unable to select DB. MySql Error: '.imw_error());
											}
											$res3 	= imw_query($q3);
										}
									}
									elseif(trim($ack)==''){
										LogResponse('ACK is <empty> or not received. This message is re-queued.');
									//	sleep(10);
										break 3;	// Moving out of while, at start of 2nd do..while.
									}else{
										$my_error = imw_error();
										LogResponse('MySql Error: '.$my_error.'. Program will resume itself after 10 seconds. (1st)');
										imw_close($GLOBALS['dbh']);
										if(isset($GLOBALS['dbh'])) unset($GLOBALS['dbh']);
										if(isset($db))	unset($db);
										$GLOBALS['dbh'] = imw_connect($sqlconf["host"], $sqlconf["login"], $sqlconf["pass"],constant('IMEDIC_IDOC'), $sqlconf["port"]);
										$db  = imw_select_db(constant('IMEDIC_IDOC'), $GLOBALS['dbh']);
										if(!$GLOBALS['dbh']){
											LogResponse('Unable to create DB connection.'.imw_error());
										}
										elseif(!$db){
											LogResponse('Unable to select DB. MySql Error: '.imw_error());
										}
										sleep(5);
										break 3;	// Moving out of while, at start of 2nd do..while.
									}
								}catch (Exception $e){
									LogResponse($e->getMessage());
									sleep(1);
									break 3;
								}
								}while(!$thisMsgSent);
							}							
						}
					}
					elseif($res && imw_num_rows($res)==0){
						if(!$MessageType){//setting file read flag 0 only if it is ordinary sender.
							file_put_contents($flagFile, '0');
						}
						sleep(3);
					}else{
						
						sleep(5);
						imw_close($GLOBALS['dbh']);
						if(isset($GLOBALS['dbh'])) unset($GLOBALS['dbh']);
						if(isset($db))	unset($db);
						$GLOBALS['dbh'] = imw_connect($sqlconf["host"], $sqlconf["login"], $sqlconf["pass"],constant('IMEDIC_IDOC'), $sqlconf["port"]);
						$db  = imw_select_db(constant('IMEDIC_IDOC'), $GLOBALS['dbh']);
						if(!$GLOBALS['dbh']){
							LogResponse('Unable to create DB connection.'.imw_error());
						}
						elseif(!$db){
							LogResponse('Unable to select DB. MySql Error: '.imw_error());
						}
					}
				}
				sleep(1);
			}while(true);				
		}catch (Exception $e){
			LogResponse('Unable to connect: '.$e->getMessage());
			sleep(5);//retry after 5 seconds.
		}
	}else{
		sleep(5);
	}
}while(true);
?>