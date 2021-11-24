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
Purpose:  Check posted HL7 message and save in DB.
Access Type: Direct Access
*/
$ignoreAuth = true;

/*Set Practice Name - for dynamically including config file*/
$listenerCount = 0;

require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/../receiver/commonFunctions.php');

$data 			= trim($_POST['data']);
$connectionID 	= (int)$_POST['connectionID'];
$version		= trim($_GET['version']);

//FILTERING IF INVLAID DATA FOUND (case1)
if(stristr($data,"MSH|^~\\\&|MIK")){
	$data = str_replace("MSH|^~\\\&|MIK","MSH|^~\\&|MIK",$data);
}
if(stristr($data,"\034")){
	$data = str_replace("\034","",$data);
}

$hl7text = trim($data);
if(strlen($hl7text) > 2 ){
	//if(strpos($hl7text,'MSH|^~')===false) $hl7text = base64_decode($hl7text);
	
	require_once(dirname(__FILE__)."/../old/data_receiver/parseHL7.php");
	$ACK_msg		= 'MSH|^~\&|IMW|IMW|TEST|TEST|'.date('YmdHis').'||ACK^O01|00000|P|2.5.1
MSA|AE|'.$recvdMsgId;
	if(strpos($hl7text,'MSH|^~') !== false){
			$objParseHL7	= new parseHL7();
			$objParseHL7->parser($hl7text,'2.5.1');
			$arrHL7msg		= $objParseHL7->HL7segments;
			$senderApp		= $objParseHL7->getSegmentVal('MSH',2);
			$senderFac		= $objParseHL7->getSegmentVal('MSH',3);
			$recvdMsgType	= $objParseHL7->getSegmentVal('MSH',8);
			$recvdMsgId		= $objParseHL7->getSegmentVal('MSH',9);
			$msgMode		= $objParseHL7->getSegmentVal('MSH',10);
			$msgVersion		= $objParseHL7->getSegmentVal('MSH',11);
			$ARR_PID3		= explode('^',$objParseHL7->getSegmentVal('PID',3));
			$pat_id			= $ARR_PID3['0'];
			
			$ARR_PID5		= explode('^',$objParseHL7->getSegmentVal('PID',5));
			
			$STR_PID7		= $objParseHL7->getSegmentVal('PID',7);
			$pat_dobdb		= substr($STR_PID7,0,4).'-'.substr($STR_PID7,4,2).'-'.substr($STR_PID7,6,2);
			
			$pat_sex		= $objParseHL7->getGenderByCode($objParseHL7->getSegmentVal('PID',8));
			
			$q_chkPat		= "SELECT id FROM patient_data WHERE ";
			
			// IF LAST NAME ANF FIRST NAME ARE NOT EMPTY
			$patient_id = 0;
			if(trim($ARR_PID5['0'])!= '' && trim($ARR_PID5['1'])!= ''){
				$q_chkPat  .= "lname='".trim($ARR_PID5['0'])."' AND fname='".trim($ARR_PID5['1'])."' AND mname='".trim($ARR_PID5['2'])."' ";
				if($pat_dobdb!='' || $pat_dobdb!='0000-00-00'){
					$q_chkPat	.= "AND DOB='".$pat_dobdb."' ";
				}
				$q_chkPat  .= "LIMIT 0,1";
				$res_chkpat = imw_query($q_chkPat);
				if($res_chkpat && imw_num_rows($res_chkpat)==1){
					$rs_chkpat = imw_fetch_assoc($res_chkpat);
					$patient_id = $rs_chkpat['id'];
				}
			}
			
			$recvdMsgTypeARR = explode('^',$recvdMsgType);
			$q  = "INSERT INTO hl7_interface_messages_in ";
			$q .= "(connection_id, patient_id, msg, msg_type, msg_sub_type, saved_on, ack_text) ";
			$q .= "VALUES ($connectionID, $patient_id, '".addslashes($hl7text)."', '".$recvdMsgTypeARR[0]."', '".$recvdMsgTypeARR[1]."', '".date('Y-m-d H:i:s')."', '')";
			imw_query($q);
			$new_id	= imw_insert_id();
			
			$ACK_msg = 'MSH|^~\&|imwemr|IMEDICWARE_'.constant('LOCAL_PRACTICE_NAME').'|'.$senderApp.'|'.$senderFac.'|'.date('YmdHis').'||ACK^O01|'.$new_id.'|'.$msgMode.'|2.5.1';
			$ACK_msg .= chr(13);
			$ACK_msg .= 'MSA|AA|'.$recvdMsgId.chr(13);
			imw_query("UPDATE hl7_interface_messages_in SET ack_text='".addslashes($ACK_msg)."' WHERE id=".$new_id." LIMIT 1");
			
	}else{
		$q  = "INSERT INTO hl7_interface_messages_in ";
		$q .= "(connection_id, patient_id, msg, msg_type, msg_sub_type, saved_on, ack_text) ";
		$q .= "VALUES ($connectionID, 0, '".addslashes($hl7text)."', '', '', '".date('Y-m-d H:i:s')."', '')";
		imw_query($q);
		$new_id	= imw_insert_id();
		$ACK_msg = 'MSH|^~\&|imwemr|IMEDICWARE_'.constant('LOCAL_PRACTICE_NAME').'|TEST|TEST|'.date('YmdHis').'||ACK^O01|'.$new_id.'|T|2.5.1'.chr(13);
		$ACK_msg.= 'MSA|AE|'.$recvdMsgId.chr(13);
	}
	
	post_to_hl7reader($hl7text,$new_id,$version,$connectionID);
	echo $ACK_msg;
}

function post_to_hl7reader($data,$new_id=0,$version,$connectionID){
	$url  = $GLOBALS['php_server'].'/hl7sys/old/HL7Reader/index.php';
	if(!$url || $url=='') return;
	$data = str_replace(array(chr(11),chr(28),'"'),'',$data);
	$data = urlencode($data);
	$myvars = 'version='.$version.'&connectionID='.$connectionID.'&newHL7msgID='.$new_id.'&data=' . $data;
	$ch = curl_init( $url ); 
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt( $ch, CURLOPT_HEADER, 0);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
	if(substr($url,0,5)=='https'){
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	}
	$response = curl_exec( $ch );
	#var_dump($response);
	if(curl_errno($ch)){
		$response.= 'ERROR: '.curl_error($ch);
	}
	curl_close($ch);
	//return $response;
}

?>
