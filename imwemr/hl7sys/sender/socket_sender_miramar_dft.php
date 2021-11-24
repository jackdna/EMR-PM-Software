<?php

$ignoreAuth = true;

/*Set Practice Name - for dynamically including config file*/
if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
}

require_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/class.ClientSocket.php");
include_once(dirname(__FILE__)."/commonFunctions.php");
include_once(dirname(__FILE__)."/../hl7GP/hl7Create.php");

$address = constant('IP_ADDR');
$port_arr = $GLOBALS['port'];//port key array.
$AckWaitTime = intval(constant('ACK_WAIT_SECONDS'));
$lastError = '';

$hl7Class = new hl7Create();
$flagFile = $hl7Class->hl7FlagPath();
$flagFile .= DIRECTORY_SEPARATOR.'senderCheckDB.log';

do{
	$db_check_flag = intval(file_get_contents($flagFile));
	if($db_check_flag=='1'){
		//CREATING SOCKET
		try {
			do{
				$db_check_flag = intval(file_get_contents($flagFile));
				if($db_check_flag=='1'){
					imw_close($GLOBALS['dbh']);
					if(isset($GLOBALS['dbh'])) unset($GLOBALS['dbh']);
					if(isset($db))	unset($db);
					$GLOBALS['dbh'] = imw_connect($sqlconf["host"], $sqlconf["login"], $sqlconf["pass"],constant('IMEDIC_IDOC'),$sqlconf["port"]);
					$db  = imw_select_db(constant('IMEDIC_IDOC'), $GLOBALS['dbh']);
					if(!$GLOBALS['dbh']){
						LogResponse('Unable to create DB connection.'.imw_error());
					}
					elseif(!$db){
						LogResponse('Unable to select DB. MySql Error: '.imw_error());
					}					
					$res = imw_query("SELECT * FROM hl7_sent WHERE sent=0 AND (send_to='SUP_DFT')"); //IF UNSENT MESSAGES FOUND
					if($res && imw_num_rows($res)>0){
						while($rs = imw_fetch_assoc($res)){
							$msgID 		= $rs['id'];
							$HL7msg		= stripslashes($rs['msg']);
							$patientId	= $rs['patient_id'];
							//$HL7msg 	= chr(10).$HL7msg.chr(28); //ADDING START AND ENDING ASCII CHARACTERS <LF>msg<FS>.. 
							$HL7msg 	= chr(11).$HL7msg.chr(28).chr(13); //<VT>msg<FS><CR>
							$length 	= strlen($HL7msg);
							
							//-----CHECK MESSAGE HEADER TO DECIDE DESTINATION IP-------
							$start_length = strlen('MSH|^~\&|IMW|IMW_MIRAMAR^');
							$port_key = substr($HL7msg,$start_length,4);
							$port = $port_arr[$port_key];							
							LogResponse('Trying to write on socket ('.$address.':'.$port.') for iMW MRN# '.$patientId.'.');
							$thisMsgSent = false;
							do{
								try{
									if(isset($sc)){unset($sc);}
									$sc = new ClientSocket();
									$sc->open($address,$port);
									//LogResponse('Socket handler created.');
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
										}else if(!$res3){
											imw_close($GLOBALS['dbh']);
											if(isset($GLOBALS['dbh'])) unset($GLOBALS['dbh']);
											if(isset($db))	unset($db);
											
											$GLOBALS['dbh'] = imw_connect($sqlconf["host"], $sqlconf["login"], $sqlconf["pass"],constant('IMEDIC_IDOC'),$sqlconf["port"]);
											$db  = imw_select_db(constant('IMEDIC_IDOC'), $GLOBALS['dbh']);
											if(!$GLOBALS['dbh']){
												LogResponse('Unable to create DB connection.'.imw_error());
											}
											elseif(!$db){
												LogResponse('Unable to select DB. MySql Error: '.imw_error());
											}
											$res3 	= imw_query($q3);
										}
									}else if(trim($ack)==''){
										LogResponse('ACK is <empty> or not received. This message is re-queued.');
										//break;
										continue;// Moving out of while, at start of 2nd do..while.
									}else{
										$my_error = imw_error();
										LogResponse('MySql Error: '.$my_error.'. Program will resume itself after 10 seconds. (1st)');
										imw_close($GLOBALS['dbh']);
										if(isset($GLOBALS['dbh'])) unset($GLOBALS['dbh']);
										if(isset($db))	unset($db);
										$GLOBALS['dbh'] = imw_connect($sqlconf["host"], $sqlconf["login"], $sqlconf["pass"],constant('IMEDIC_IDOC'),$sqlconf["port"]);
										$db  = imw_select_db(constant('IMEDIC_IDOC'), $GLOBALS['dbh']);
										if(!$GLOBALS['dbh']){
											LogResponse('Unable to create DB connection.'.imw_error());
										}
										elseif(!$db){
											LogResponse('Unable to select DB. MySql Error: '.imw_error());
										}
										sleep(10);
										break 3;	// Moving out of while, at start of 2nd do..while.
									}
								}catch (Exception $e){
									LogResponse($e->getMessage());
									sleep(10);
									break 3;
								}
							}while(!$thisMsgSent);
							//LogResponse('Once the process is finished for all pending messages.');
						}
					}else if($res && imw_num_rows($res)==0){
						file_put_contents($flagFile, '0');
					}else{
						sleep(5);
						imw_close($GLOBALS['dbh']);
						if(isset($GLOBALS['dbh'])) unset($GLOBALS['dbh']);
						if(isset($db))	unset($db);
						$GLOBALS['dbh'] = imw_connect($sqlconf["host"], $sqlconf["login"], $sqlconf["pass"],constant('IMEDIC_IDOC'),$sqlconf["port"]);
						$db  = imw_select_db(constant('IMEDIC_IDOC'), $GLOBALS['dbh']);
						if(!$GLOBALS['dbh']){
							LogResponse('Unable to create DB connection.'.imw_error());
						}
						elseif(!$db){
							LogResponse('Unable to select DB. MySql Error: '.imw_error());
						}
					}
				}
				sleep(10);
			}while(true);				
		}catch (Exception $e){
			LogResponse($e->getMessage());
			sleep(10);//retry after 5 seconds.
		}
	}
}while(true);
?> 
