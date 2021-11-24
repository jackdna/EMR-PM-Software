<?php
/*
File: ajax_form_actions.php
Purpose:  Quick actions controller
Access Type: Direct Access (via Ajax)
*/
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/msgConsole.php');
$msgConsoleObj = new msgConsole();
$callFrom = (isset($_REQUEST['from']) && trim($_REQUEST['from'])!='') ? trim($_REQUEST['from']) : 'core';
$task = (isset($_REQUEST['task']) && trim($_REQUEST['task'])!='') ? trim($_REQUEST['task']) : '';
$arr_users = $msgConsoleObj->get_username_by_id();

switch($task){
	case 'quickreply':
		////task=quickreply&from=core&sendTo="+sendTo+"&msgText="+escape(msgText)+"&replyOf="+msgId+"&subject="+subject+"&msg_pid="+pId
		$msgText = core_refine_user_input($_REQUEST['msgText']);
		if(stristr($_REQUEST['subject'],'Re:')){$_REQUEST['subject'] = trim(substr($_REQUEST['subject'],3));}
		$subject = core_refine_user_input('Re: '.$_REQUEST['subject']);
		$patientId = intval($_REQUEST['msg_pid']);
		$sendTo = intval($_REQUEST['sendTo']);
		$name_sendTo = $arr_users[$sendTo]['full'];
		$originalId = intval($_REQUEST['replyOf']);
		/*--ATTACHING ORIGINAL INITIAL MSG WITH DONE--*/
		$getQ2 = "SELECT message_sender_id,message_text, message_subject, DATE_FORMAT(message_send_date,'%W, %M %d, %Y %h:%i %p') as msgDate 
		FROM user_messages WHERE message_to='".$_SESSION['authId']."' AND user_message_id = '$originalId' LIMIT 0,1";
		$resQ2 = imw_query($getQ2);
		$originalText = '';
		if($resQ2 && imw_num_rows($resQ2)>0){
			$rs2 = imw_fetch_assoc($resQ2);
			$originalText = $rs2['message_text'];
			$sentDate = $rs2['msgDate'];
			$originalSubject = core_extract_user_input($rs2['message_subject']);
			$ORsenderName = $arr_users[$_SESSION['authId']]['full'];
			$originalTextPrefix = '
----ORIGINAL MESSAGE----
From: '.$name_sendTo.'
To: '.$ORsenderName.'
Sent: '.$sentDate.'
Subject: '.$originalSubject.'
		   
';
			$originalText = $originalTextPrefix.$originalText;
		}/*--ORIGINAL MSG ATTACHED--*/
		$msgText .= $originalText;
		$subject = 'RE: '.$originalSubject;
		
		$query = "INSERT INTO user_messages SET
				  message_subject='".core_refine_user_input($subject)."', 
				  message_to = '$sendTo',
				  message_text = '".core_refine_user_input($msgText)."',
				  message_sender_id = '".$msgConsoleObj->operator_id."',
				  message_status=0,
				  message_read_status=0,
				  user_messages=0,
				  message_urgent=0,
				  msg_completed_by=0,
				  patientId = '$patientId',
				  Pt_Communication='0',
				  sent_to_groups='$name_sendTo',
				  review_by=0,
				  replied_id='$originalId',
				  del_status=0,
				  message_send_date='".date('Y-m-d H:i:s')."'";
		$result = imw_query($query);
		if($result){
			markAsRead($originalId);
		}else{
			echo 'failed';
		}
		/*-sending reply to PVC also, if patientID is set-*/
		if($patientId>0){
			$flag_ptcomm=1;
			$query2 = "INSERT INTO user_messages SET
					  message_subject='$subject', 
					  message_to = '$sendTo',
					  message_text = '$msgText',
					  message_sender_id = '".$msgConsoleObj->operator_id."',
					  message_status=0,
					  message_read_status=0,
					  user_messages=0,
					  message_urgent=0,
					  msg_completed_by=0,
					  patientId = '$patientId',
					  Pt_Communication='$flag_ptcomm',
					  sent_to_groups='$name_sendTo',
					  review_by=0,
					  replied_id='$originalId',
					  del_status=0,
					  message_send_date='".date('Y-m-d H:i:s')."'";
			$result2 = imw_query($query2);
		}
		break;
	case 'markRead':
		//?task=markRead&from=core&msg_id="+msgId
		$msgId = intval($_REQUEST['msg_id']);
		markAsRead($msgId,'read');
		break;
	case 'markDone':
		//?task=markRead&from=core&msg_id="+msgId
		$msgId = intval($_REQUEST['msg_id']);
		markAsRead($msgId,'done',$msgConsoleObj);
		break;
	case 'alter_msg_flag_status':
		$msgId = intval($_REQUEST['msg_id']);
		$getQ = "SELECT flagged FROM user_messages 
				 WHERE message_to='".$_SESSION['authId']."' AND user_message_id = '$msgId' LIMIT 0,1";
		$resQ = imw_query($getQ);
		if($resQ && imw_num_rows($resQ)==1){
			$rs = imw_fetch_assoc($resQ);
			$flagged = intval($rs['flagged']);
			if($flagged==1){$flagged = 0;}else{$flagged=1;}
			$query = "UPDATE user_messages SET flagged=$flagged WHERE user_message_id = '$msgId' LIMIT 1";
			$result = imw_query($query);
		}
		break;
		case 'alter_pt_msg_flag_status':
			$pt_msg_id = $_GET["pt_msg_id"];
			echo $update_pt_msg_qry = "UPDATE patient_messages SET flagged = if(flagged = 1,0,1) WHERE pt_msg_id = '".$pt_msg_id."'";
			imw_query($update_pt_msg_qry);
		break;		
	default:
		break;
}

function markAsRead($msgId,$task='read',$msgConsoleObj=''){
	global $arr_users;
	if($msgId>0){
	$query = '';
		if($task=='read'){
			$query = "UPDATE user_messages SET message_read_status=1 WHERE message_to='".$_SESSION['authId']."' AND user_message_id = '$msgId'";
		}else if($task=='done'){
			$q 		= "SELECT msg_id,msg_type FROM user_messages WHERE message_to='".$_SESSION['authId']."' AND user_message_id = '$msgId'";
			$res 	= imw_query($q);
			$row 	= imw_fetch_assoc($res);
			if($row['msg_id'] == 0 || $row['msg_type'] == 0){
				$query = "UPDATE user_messages SET message_read_status=1, message_status = 1, message_completed_date = '".date('Y-m-d')."', msg_completed_by='".$_SESSION['authId']."' WHERE message_to='".$_SESSION['authId']."' AND user_message_id = '$msgId'";
			}else{
				$query = "UPDATE user_messages SET message_read_status=IF(message_to = '".$_SESSION['authId']."',1,IF(message_read_status=0,0,1)), message_status = 1, message_completed_date = '".date('Y-m-d')."', msg_completed_by='".$_SESSION['authId']."' WHERE msg_id='".$row['msg_id']."'";
			}
		}
		if($query != ''){
			$result = imw_query($query);
			if($result){echo 'success';}
			if($result && $task=='done'){
				$getQ = "SELECT message_sender_id, message_subject,patientId FROM user_messages WHERE message_to='".$_SESSION['authId']."' AND user_message_id = '$msgId' LIMIT 0,1";
				$resQ = imw_query($getQ);
				if($resQ){
					$rs = imw_fetch_assoc($resQ);
					$sendTo = $rs['message_sender_id'];
					$sender = $_SESSION['authId'];
					$patientId = $rs['patientId'];
					$senderName = $arr_users[$sender]['full'];
					$sendToName = $arr_users[$sendTo]['full'];
					$msgText = 'This task is marked as DONE by '.$senderName;
					
					/*--ATTACHING ORIGINAL INITIAL MSG WITH DONE--*/
					//$arr_threads = $msgConsoleObj->get_threads_of_msg($msgId);					
					$initialMsgId = $msgId;//$arr_threads['0'];
					$getQ2 = "SELECT message_sender_id,message_text, message_subject, DATE_FORMAT(message_send_date,'%W, %M %d, %Y %h:%i %p') as msgDate FROM user_messages 
							  WHERE message_to='".$_SESSION['authId']."' AND user_message_id = '$initialMsgId' LIMIT 0,1";
					$resQ2 = imw_query($getQ2);
					$originalText = '';
					if($resQ2 && imw_num_rows($resQ2)>0){
						$rs2 = imw_fetch_assoc($resQ2);
						$originalText = $rs2['message_text'];
						$sentDate = $rs2['msgDate'];
						$originalSubject = $rs2['message_subject'];
						$originalTextPrefix = '
----ORIGINAL MESSAGE----
From: '.$sendToName.'
To: '.$senderName.'
Sent: '.$sentDate.'
Subject: '.$originalSubject.'

';
						$originalText = $originalTextPrefix.$originalText;
					}/*--ORIGINAL MSG ATTACHED--*/
					
					$msgText .= $originalText;
					$subject = 'DONE: '.$originalSubject;
					
					$query = "INSERT INTO user_messages SET
					  message_subject='$subject', 
					  message_to = '$sendTo',
					  message_text = '$msgText',
					  message_sender_id = '".$sender."',
					  message_status=0,
					  message_read_status=0,
					  user_messages=0,
					  message_urgent=0,
					  msg_completed_by=0,
					  patientId = '$patientId',
					  Pt_Communication='0',
					  sent_to_groups='$sendToName',
					  review_by=0,
					  replied_id='$msgId',
					  done_alert='1',
					  del_status=0,
					  message_send_date='".date('Y-m-d H:i:s')."'";
					if(substr($rs['message_subject'],0,5)!='DONE:'){
						$result = imw_query($query);
					}
				}
			}
		}
	}
}
?>