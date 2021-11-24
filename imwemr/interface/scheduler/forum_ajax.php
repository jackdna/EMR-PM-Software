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
*/

?><?php
/*
File: forum_ajax.php
Purpose: For HL7 Operation
Access Type: Include
*/
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once('../../hl7sys/old/CLS_makeHL7.php');
require_once("../../hl7sys/sender/class.ClientSocket.php");
set_time_limit(0);
$sch_id = isset($_GET['sch_id']) ? intval($_GET['sch_id']) : 0;
$forum_btn_val = 'FORUM'; if(constant("HIE_FORUM") == "YES")$forum_btn_val = 'HIE';

$q = "SELECT sa_patient_id, sa_app_start_date as date_of_service FROM schedule_appointments WHERE id='$sch_id' LIMIT 0,1";
$res = imw_query($q);
if($res && imw_num_rows($res)==1){
	$rs = imw_fetch_assoc($res);
	
	$patient_id 				= $rs['sa_patient_id'];
	$date_of_service 			= $rs['date_of_service'];
	$makeHL7					= new makeHL7;
	$makeHL7->sch_id 			= $sch_id;
	$makeHL7->date_of_service 	= $date_of_service;
	$msg_type 					= 'Create_New_Order';
	if(constant("HIE_FORUM") == "YES"){
		$msg_type 				= 'Update_Patient';
	}
	$resultARR 	= $makeHL7->log_HL7_message($patient_id,$msg_type,$forum_btn_val);
	if(is_array($resultARR) && count($resultARR)==2){
		$new_msg_id	=  $resultARR[0];
		$new_msg	=  stripslashes($resultARR[1]);
		try {
			if(constant("ZEISS_FORUM") == "YES"){
				$address 	= '108.58.135.211';
				$port		= '5000';
			}else if(constant("HIE_FORUM") == "YES"){
				$address 	= '149.111.218.10';//'161.249.10.228'; // set HIE IP here.
				//$port		= '8225'; // ADT TEST
				$port		= '1609';//'8235'; // ADT PROD
			}
			$sc 		= new ClientSocket();
			$sc->open($address,$port);
			$HL7msg 	= chr(11).$new_msg.chr(28).chr(13); //ADDING START AND ENDING ASCII CHARACTERS
			$sc->send($HL7msg);
			$ack 		= $sc->recv(120); //2 minute waiting
			$echo = '&lt;not sent&gt; ';
			if($ack!=''){
				//echo 'Sent. <b>ACK:</b>'.$ack;
				$response = $makeHL7->read_hl7_ACK(trim($ack));
				$status = $status_text = '';
				if($response['error']==NULL){
					$resArr = $response['response'];
					if(is_array($resArr)){
						$status 	= $resArr['status'];
						$status_text= $resArr['status_text'];
					}
				}
				$q1		= "UPDATE hl7_sent SET 
						sent=1, 
						sent_on='".date('Y-m-d H:i:s')."', 
						response='".addslashes($ack)."', 
						status='".$status."', 
						status_text='".$status_text."' 
						WHERE id='".$new_msg_id."' AND sch_id='".$sch_id."'";
				$res1 	= imw_query($q1);

				$echo = 'Sent. ';
				if($status=='Y'){$echo .= 'Accepted. ';}
				else if($status=='N'){$echo .= 'Rejected. '.$status_text.' ';}
				else if(trim($ack)!=''){$echo .= '['.$ack.']';}
			}
			if(constant("HIE_FORUM") == "YES"){
				echo 'ADT: '.$echo;
			}else{
				echo $echo;	
			}
		}catch (Exception $e){
			echo ($e->getMessage());
		}
	}
	
	//LAB MSG
	if(constant("HIE_FORUM") == "YES"){
		$address 	= '161.249.10.228'; // set HIE IP here.
		//$port		= '8226'; // LAB TEST
		$port		= '8236'; // LAB PROD
		$msg_type = 'LAB';
		$resultARR 	= $makeHL7->log_HL7_message($patient_id,$msg_type,$forum_btn_val);
		if(is_array($resultARR) && count($resultARR)==2){
			$new_msg_id	=  $resultARR[0];
			$new_msg	=  stripslashes($resultARR[1]);
			try {
				$sc 	= new ClientSocket();
				$sc->open($address,$port);
				$HL7msg = chr(11).$new_msg.chr(28).chr(13); //ADDING START AND ENDING ASCII CHARACTERS
				$sc->send($HL7msg);
				$ack 	= $sc->recv(120); //2 minute waiting
				$echo 	= '&lt;not sent&gt; ';
				if($ack!=''){
					//echo 'Sent. <b>ACK:</b>'.$ack;
					$response = $makeHL7->read_hl7_ACK(trim($ack));
					$status = $status_text = '';
					if($response['error']==NULL){
						$resArr = $response['response'];
						if(is_array($resArr)){
							$status 	= $resArr['status'];
							$status_text= $resArr['status_text'];
						}
					}
					$q1		= "UPDATE hl7_sent SET 
							sent=1, 
							sent_on='".date('Y-m-d H:i:s')."', 
							response='".addslashes($ack)."', 
							status='".$status."', 
							status_text='".$status_text."' 
							WHERE id='".$new_msg_id."' AND sch_id='".$sch_id."'";
					$res1 	= imw_query($q1);
	
					$echo = 'Sent. ';
					if($status=='Y'){$echo .= 'Accepted. ';}
					else if($status=='N'){$echo .= 'Rejected. '.$status_text.' ';}
					else if(trim($ack)!=''){$echo .= '['.$ack.']';}
				}
				echo '<br>LAB: '.$echo;
			}catch (Exception $e){
				echo ($e->getMessage());
			}
		}

		//MDM message (Patient's General health)
		$address 	= '149.111.218.10';//'161.249.10.228'; // set HIE IP here.
		//$port		= '8228'; // TRN TEST
		$port		= '1657';//'8238'; // TRN PROD
		$msg_type = 'MDM';
		$resultARR 	= $makeHL7->log_HL7_message($patient_id,$msg_type,$forum_btn_val);
		if(is_array($resultARR) && count($resultARR)==2){
			$new_msg_id	=  $resultARR[0];
			$new_msg	=  stripslashes($resultARR[1]);
			try {
				$sc 	= new ClientSocket();
				$sc->open($address,$port);
				$HL7msg = chr(11).$new_msg.chr(28).chr(13); //ADDING START AND ENDING ASCII CHARACTERS
				$sc->send($HL7msg);
				$ack 	= $sc->recv(120); //2 minute waiting
				$echo 	= '&lt;not sent&gt; ';
				if($ack!=''){
					//echo 'Sent. <b>ACK:</b>'.$ack;
					$response = $makeHL7->read_hl7_ACK(trim($ack));
					$status = $status_text = '';
					if($response['error']==NULL){
						$resArr = $response['response'];
						if(is_array($resArr)){
							$status 	= $resArr['status'];
							$status_text= $resArr['status_text'];
						}
					}
					$q1		= "UPDATE hl7_sent SET 
							sent=1, 
							sent_on='".date('Y-m-d H:i:s')."', 
							response='".addslashes($ack)."', 
							status='".$status."', 
							status_text='".$status_text."' 
							WHERE id='".$new_msg_id."' AND sch_id='".$sch_id."'";
					$res1 	= imw_query($q1);
	
					$echo = 'Sent. ';
					if($status=='Y'){$echo .= 'Accepted. ';}
					else if($status=='N'){$echo .= 'Rejected. '.$status_text.' ';}
					else if(trim($ack)!=''){$echo .= '['.$ack.']';}
				}
				echo '<br>MDM: '.$echo;
			}catch (Exception $e){
				echo ($e->getMessage());
			}
		}

		//ORM message (RADIOLOGY)
		$address 	= '161.249.10.228'; // set HIE IP here.
		//$port		= '8227'; // RAD TEST
		$port		= '8237'; // RAD PROD
		$msg_type = 'ORM';
		$resultARR 	= $makeHL7->log_HL7_message($patient_id,$msg_type,$forum_btn_val);
		if(is_array($resultARR) && count($resultARR)==2){
			$new_msg_id	=  $resultARR[0];
			$new_msg	=  stripslashes($resultARR[1]);
			try {
				$sc 	= new ClientSocket();
				$sc->open($address,$port);
				$HL7msg = chr(11).$new_msg.chr(28).chr(13); //ADDING START AND ENDING ASCII CHARACTERS
				$sc->send($HL7msg);
				$ack 	= $sc->recv(120); //2 minute waiting
				$echo 	= '&lt;not sent&gt; ';
				if($ack!=''){
					//echo 'Sent. <b>ACK:</b>'.$ack;
					$response = $makeHL7->read_hl7_ACK(trim($ack));
					$status = $status_text = '';
					if($response['error']==NULL){
						$resArr = $response['response'];
						if(is_array($resArr)){
							$status 	= $resArr['status'];
							$status_text= $resArr['status_text'];
						}
					}
					$q1		= "UPDATE hl7_sent SET 
							sent=1, 
							sent_on='".date('Y-m-d H:i:s')."', 
							response='".addslashes($ack)."', 
							status='".$status."', 
							status_text='".$status_text."' 
							WHERE id='".$new_msg_id."' AND sch_id='".$sch_id."'";
					$res1 	= imw_query($q1);
	
					$echo = 'Sent. ';
					if($status=='Y'){$echo .= 'Accepted. ';}
					else if($status=='N'){$echo .= 'Rejected. '.$status_text.' ';}
					else if(trim($ack)!=''){$echo .= '['.$ack.']';}
				}
				echo '<br>RAD: '.$echo;
			}catch (Exception $e){
				echo ($e->getMessage());
			}
		}


	}
}




sleep(1);
?>