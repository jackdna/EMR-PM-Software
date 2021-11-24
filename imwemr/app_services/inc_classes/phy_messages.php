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
?>
<?php  
include_once(dirname(__FILE__).'/physician_console.php');
include_once(dirname(__FILE__).'/patient_app.php');
class phy_messages extends physician_console{	
	var $reqModule;
	public function __construct($patient){
		parent::__construct($patient);
	}
	
	public function getUsersList(){
		$this->db_obj->qry = "SELECT user_type_name, CONCAT('r', user_type_id) as user_type_id FROM user_type";
		$result = $this->db_obj->get_resultset_array();	
		
		$this->db_obj->qry = "SELECT id as user_type_id, CONCAT(users.lname, ', ', users.fname) as user_type_name FROM users WHERE delete_status = 0 and users.fname != '' and users.lname IS NOT NULL ";
		$result1 = $this->db_obj->get_resultset_array();	
		
		foreach($result1 as $row)
		{
			$result[] = $row; 	
		}
		
		return $result;
	}
	public function message_read(){
		$this->db_obj->qry = "UPDATE user_messages SET message_read_status = 1 WHERE user_message_id  = ".$_REQUEST['message_id'];
		$result = $this->db_obj->run_query($this->db_obj->qry);
		return $result;	
	}
	public function delete_inbox_messgae(){
		$this->db_obj->qry = "UPDATE user_messages SET receiver_delete = 1 WHERE user_message_id  IN (".$_REQUEST['message_id'].")";
		$result = $this->db_obj->run_query($this->db_obj->qry);
		return 1;	
		
	}
	public function delete_sent_messgae(){
		 $this->db_obj->qry = "UPDATE user_messages SET del_status = 1 WHERE user_message_id  IN (".$_REQUEST['message_id'].")";
		 $result = $this->db_obj->run_query($this->db_obj->qry);
		return 1;	
		
	}
	//add new fuction for android //
	public function delete_sent_messgae_new(){
		 $this->db_obj->qry = "UPDATE user_messages SET del_status = 1 WHERE msg_id  IN (".$_REQUEST['message_id'].")";
		 $result = $this->db_obj->run_query($this->db_obj->qry);
		return 1;	
		
	}
	//end
	public function delete_future_alerts(){
		 $this->db_obj->qry = "UPDATE user_messages SET del_status = '1',receiver_delete = '1',del_future_alert=1 WHERE user_message_id IN (".$_REQUEST['message_id'].")";
		 $result = $this->db_obj->run_query($this->db_obj->qry);
		return 1;	
		
	}
	public function restore_deleted_msg(){
		 $this->db_obj->qry = "UPDATE user_messages 
								SET del_status = IF(message_sender_id = '".$this->authId."' && del_status = '1',0,IF(del_status=1,1,0)),
								receiver_delete = IF(message_to = '".$this->authId."' && receiver_delete = '1',0,IF(receiver_delete=1,1,0))
								WHERE user_message_id IN (".$_REQUEST['message_id'].")";
		 $result = $this->db_obj->run_query($this->db_obj->qry);
		return 1;	
		
	}
	// function for app //
	public function restore_deleted_msg_app(){
		 $this->db_obj->qry = "UPDATE user_messages 
								SET del_status = IF(message_sender_id = '".$this->authId."' && del_status = '1',0,IF(del_status=1,1,0)),
								receiver_delete = IF(message_to = '".$this->authId."' && receiver_delete = '1',0,IF(receiver_delete=1,1,0))
								WHERE msg_id IN (".$_REQUEST['message_id'].")";
		 $result = $this->db_obj->run_query($this->db_obj->qry);
		return 1;	
		
	}
	// end of function
	public function get_username_by_id($id_arr = array())
	{
		$filterById = '';
		$result_arr = array();		
		if(count($id_arr)>0)
		{
			$id_arr_str = implode(',',$id_arr);
			$filterById = ' and id IN ('.$id_arr_str.')';
		}
		
		$reqQry = 'select id, concat(SUBSTRING(fname,1,1),SUBSTRING(lname,1,1)) as short_name, concat(lname,", ",fname) as medium_name, concat(lname,", ",fname," ",mname) as full_name from users where id > 0 '.$filterById.' and delete_status = 0 order by lname,fname';		
		$resultOb = imw_query($reqQry);
		while($result_row = imw_fetch_assoc($resultOb))
		{
			$result_arr[$result_row['id']]['short'] = $result_row['short_name'];
			$result_arr[$result_row['id']]['medium'] = $result_row['medium_name'];
			$result_arr[$result_row['id']]['full'] = $result_row['full_name'];
		}
		return $result_arr;
	}
	public function task_completed(){
		/*$this->db_obj->qry = "UPDATE user_messages 
							 SET message_read_status = 1, 
							 	message_status = 1, 
								message_completed_date = CURDATE(), 
								msg_completed_by = '".$this->authId."' 
								WHERE user_message_id = ".$_REQUEST['message_id'];
		$result = $this->db_obj->run_query($this->db_obj->qry);
		return $result;	*/
			$arr_users = $this->get_username_by_id();
			$q 		= "SELECT msg_id,msg_type FROM user_messages WHERE message_to='".$this->authId."' AND user_message_id = '".$_REQUEST['message_id']."'";
			$res 	= imw_query($q);
			$row 	= imw_fetch_assoc($res);
			if($row['msg_id'] == 0 || $row['msg_type'] == 0){
				$query = "UPDATE user_messages SET message_read_status=1, message_status = 1, message_completed_date = '".date('Y-m-d')."', msg_completed_by='".$this->authId."' WHERE message_to='".$this->authId."' AND user_message_id = '".$_REQUEST['message_id']."'";
			}else{
				$query = "UPDATE user_messages SET message_read_status=IF(message_to = '".$this->authId."',1,IF(message_read_status=0,0,1)), message_status = 1, message_completed_date = '".date('Y-m-d')."', msg_completed_by='".$this->authId."' WHERE msg_id='".$row['msg_id']."'";
			}
			if($query != ''){
			$result = imw_query($query);
			if($result){echo 'success';}
			if($result){
				$getQ = "SELECT message_sender_id, message_subject,patientId FROM user_messages WHERE message_to='".$this->authId."' AND user_message_id = '".$_REQUEST['message_id']."' LIMIT 0,1";
				$resQ = imw_query($getQ);
				if($resQ){
					$rs = imw_fetch_assoc($resQ);
					$sendTo = $rs['message_sender_id'];
					$sender = $this->authId;
					$patientId = $rs['patientId'];
					$senderName = $arr_users[$sender]['full'];
					$sendToName = $arr_users[$sendTo]['full'];
					$msgText = 'This task is marked as DONE by '.$senderName;
					
					/*--ATTACHING ORIGINAL INITIAL MSG WITH DONE--*/
					//$arr_threads = $msgConsoleObj->get_threads_of_msg($msgId);					
					$initialMsgId = $_REQUEST['message_id'];//$arr_threads['0'];
					$getQ2 = "SELECT message_sender_id,message_text, message_subject, DATE_FORMAT(message_send_date,'%W, %M %d, %Y %h:%i %p') as msgDate FROM user_messages 
							  WHERE message_to='".$this->authId."' AND user_message_id = '$initialMsgId' LIMIT 0,1";
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
					  replied_id='".$_REQUEST['message_id']."',
					  done_alert='1',
					  del_status=0";
					if(substr($rs['message_subject'],0,5)!='DONE:'){
						$result = imw_query($query);
					}
				}
			}
		}
	}
	public function inbox(){
		//require_once(dirname(__FILE__)."/../../interface/common/functions.inc.php");
		//$objManageData = new DataManage;
		$arrArgInbox = explode(",",$_REQUEST['param']);
		$sort_by = (isset($arrArgInbox[0]) && $arrArgInbox[0]!='')?$arrArgInbox[0]:"user_messages.user_message_id";
		$sort_order = (isset($arrArgInbox[1]) && $arrArgInbox[1]!='')?$arrArgInbox[1]:"DESC";
		$limit_from = (isset($arrArgInbox[2]) && $arrArgInbox[2]!='')?$arrArgInbox[2]:"0";
		$limit_to = (isset($arrArgInbox[3]) && $arrArgInbox[3]!='')?$arrArgInbox[3]:"30";
		//CONCAT('<div style=\'height:200px; border:#666 1px solid; overflow:auto; width:100%;text-wrap:!important\'>',user_messages.message_text,'</div>') AS message_text,
		$this->db_obj->qry ="SELECT user_messages.message_read_status,
							user_messages.message_subject, 
							user_messages.message_text,
							user_messages.message_urgent, 
							CONCAT(users.fname,' ',users.mname,', ',users.lname) AS message_sender_name, 
							user_messages.message_sender_id as message_sender_id,  
							user_groups.name as user_group, 
							if(TRIM(patient_data.fname)!='',CONCAT(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id),'') AS patient_name, 
							patient_data.id AS message_patient_id, 
							DATE_FORMAT(message_send_date,'".get_sql_date_format()." %h:%i %p') AS msg_send_date, 
							DATE_FORMAT(message_send_date,'%m-%d-%Y %h:%i %p') AS msg_send_date_app, 
							user_messages.flagged, 
							user_messages.msg_icon ,
							user_messages.user_message_id 
						FROM user_messages LEFT JOIN patient_data ON patient_data.id = user_messages.patientId 
						LEFT JOIN users ON user_messages.message_sender_id = users.id
						LEFT JOIN user_groups ON user_groups.id = users.user_group_id 
						where 
							user_messages.message_status = 0 
							and user_messages.receiver_delete=0 
							and user_messages.message_to = ".$this->authId."
							and user_messages.message_sender_id > 0 
							and user_messages.delivery_date <= CURDATE()
						 	and user_messages.Pt_Communication = 0 
							and user_messages.message_sender_id != 0 
						 ORDER BY $sort_by $sort_order
						 LIMIT $limit_from, $limit_to
						";
		$result = $this->db_obj->get_resultset_array();	
		foreach($result as $index=>$arr){
			$result[$index]['arrReplyUserId'] = array();
			$arrReplyId = array();
							$sqlQry = imw_query("select message_sender_id, msg_id,message_subject,
																	DATE_FORMAT(message_send_date,'%Y-%m-%d') AS message_send_date
															from user_messages where user_message_id = '".$arr['user_message_id']."'");
							$msg = '';
							if($sqlQry && imw_num_rows($sqlQry) > 0){
								$msg = imw_fetch_assoc($sqlQry);
							}
							
							//$msg = $objManageData->mysqlifetchdata();
							
							if($msg[0]['msg_id'] != "0"){
								$sqlQry1 = imw_query("SELECT group_concat(message_to) AS reply_usr_id,message_sender_id 
																FROM user_messages where msg_id = '".$msg[0]['msg_id']."'
																");
								$res = '';
								if($sqlQry1 && imw_num_rows($sqlQry1) > 0){
									$res = imw_fetch_assoc($sqlQry1);
								}
								//$res = $objManageData->mysqlifetchdata();
								$reply_usr_id = $res[0]['reply_usr_id'];
								$arrTmp = explode(",",$reply_usr_id);
								
								$key = array_search($this->authId,$arrTmp);
								unset($arrTmp[$key]);
								
								$message_sender_id = $res[0]['message_sender_id'];
								$arrSenderId = explode(",",$message_sender_id);
								
								$arrReplyUserId = array_merge($arrTmp,$arrSenderId);
								foreach($arrReplyUserId as $userId){
									$arrReplyId[] = $userId;
								}
							}else{
								$sqlQry2 = imw_query("SELECT group_concat(message_to) AS reply_usr_id,message_sender_id 
																FROM user_messages 
																WHERE message_subject = '".$msg[0]['message_subject']."'
																		AND DATE_FORMAT(message_send_date,'%Y-%m-%d') = '".$msg[0]['message_send_date']."'
																		AND message_sender_id = '".$arr['msg_sender_id']."'
																GROUP BY user_messages.sent_to_groups, 
																		 user_messages.message_subject, 
																		 DATE_FORMAT(user_messages.message_send_date,'%Y-%m-%d')
																");
								$res = '';
								if($sqlQry2 && imw_num_rows($sqlQry2) > 0){
									$res = imw_fetch_assoc($sqlQry2);
								}
								//$res = $objManageData->mysqlifetchdata();
								$reply_usr_id = $res[0]['reply_usr_id'];
								$arrTmp = explode(",",$reply_usr_id);
								
								$key = array_search($_SESSION['authId'],$arrTmp);
								unset($arrTmp[$key]);
								
								$message_sender_id = $res[0]['message_sender_id'];
								$arrSenderId = explode(",",$message_sender_id);
								
								$arrReplyUserId = array_merge($arrTmp,$arrSenderId);
								foreach($arrReplyUserId as $userId){
									$arrReplyId[] = $userId;
								}
							}
			$result[$index]['arrReplyUserId'] = $arrReplyId;				
		}
		
		return $result;
	}
	public function inbox_total_count(){
		$this->db_obj->qry = "SELECT count(*) AS total_count
						FROM user_messages LEFT JOIN patient_data ON patient_data.id = user_messages.patientId 
						LEFT JOIN users ON user_messages.message_sender_id = users.id
						LEFT JOIN user_groups ON user_groups.id = users.user_group_id 
						where 
							user_messages.message_status = 0 
							and user_messages.receiver_delete=0 
							and user_messages.message_to = ".$this->authId."
							and user_messages.message_sender_id > 0 
							and user_messages.delivery_date <= CURDATE()
						 	and user_messages.Pt_Communication = 0 
							and user_messages.message_sender_id != 0 
							";
		$result_count = $this->db_obj->get_resultset_array();
		return $result_count[0]['total_count'];
	}
	public function future_alerts(){
		//require_once(dirname(__FILE__)."/../../interface/common/functions.inc.php");
		//$objManageData = new DataManage;
		$arrArgInbox = explode(",",$_REQUEST['param']);
		$sort_by = (isset($arrArgInbox[0]) && $arrArgInbox[0]!='')?$arrArgInbox[0]:"user_messages.user_message_id";
		$sort_order = (isset($arrArgInbox[1]) && $arrArgInbox[1]!='')?$arrArgInbox[1]:"DESC";
		$limit_from = (isset($arrArgInbox[2]) && $arrArgInbox[2]!='')?$arrArgInbox[2]:"0";
		$limit_to = (isset($arrArgInbox[3]) && $arrArgInbox[3]!='')?$arrArgInbox[3]:"30";
		$this->db_obj->qry ="SELECT GROUP_CONCAT(user_messages.user_message_id) AS user_message_id , 
						user_messages.message_read_status, user_messages.message_subject, 
						user_messages.message_text, 
						user_messages.message_urgent, 
						GROUP_CONCAT(CONCAT(users.fname,' ',users.mname,', ',users.lname) SEPARATOR ' :: ') AS message_sender_name, user_messages.message_sender_id as message_sender_id,  
						user_groups.name as user_group, if(TRIM(patient_data.fname)!='',CONCAT(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id),'') AS patient_name, 
						patient_data.id AS message_patient_id, 
						DATE_FORMAT(message_send_date,'".get_sql_date_format()." %h:%i %p') AS msg_send_date, 
						DATE_FORMAT(message_send_date,'%m-%d-%Y %h:%i %p') AS msg_send_date_app, 
						user_messages.flagged, user_messages.msg_icon 
						FROM user_messages LEFT JOIN patient_data ON patient_data.id = user_messages.patientId 
						LEFT JOIN users ON user_messages.message_to = users.id 
						LEFT JOIN user_groups ON user_groups.id = users.user_group_id 
						where 
						 user_messages.message_status = 0 
						 and user_messages.del_future_alert=0 
						 and user_messages.del_status ='0' 
						 and user_messages.message_sender_id = ".$this->authId." 
						 and date_format(user_messages.delivery_date,'%Y-%m-%d') > CURDATE() 
						 AND user_messages.delivery_date != '0000-00-00' 
						 and user_messages.Pt_Communication = 0 
						 and user_messages.message_sender_id != 0 
						group by (CASE WHEN (msg_id != 0) THEN msg_id ELSE (CONCAT_WS(',',user_messages.sent_to_groups, user_messages.message_subject, DATE_FORMAT(user_messages.message_send_date,'%Y-%m-%d'))) END)
						ORDER BY $sort_by $sort_order
						LIMIT $limit_from, $limit_to";
						
		$result = $this->db_obj->get_resultset_array();	
		return $result;
	}
	public function future_alerts_total_count(){
		$this->db_obj->qry = "SELECT count(*) AS total_count
							FROM user_messages LEFT JOIN patient_data ON patient_data.id = user_messages.patientId 
						LEFT JOIN users ON user_messages.message_to = users.id 
						LEFT JOIN user_groups ON user_groups.id = users.user_group_id 
						where 
						 user_messages.message_status = 0 
						 and user_messages.del_future_alert=0 
						 and user_messages.del_status ='0' 
						 and user_messages.message_sender_id = ".$this->authId." 
						 and date_format(user_messages.delivery_date,'%Y-%m-%d') > CURDATE() 
						 AND user_messages.delivery_date != '0000-00-00' 
						 and user_messages.Pt_Communication = 0 
						 and user_messages.message_sender_id != 0 
						group by (CASE WHEN (msg_id != 0) THEN msg_id ELSE (CONCAT_WS(',',user_messages.sent_to_groups, user_messages.message_subject, DATE_FORMAT(user_messages.message_send_date,'%Y-%m-%d'))) END)
						";
		$result_count = $this->db_obj->get_resultset_array();
		return count($result_count);
	}
	public function deleted_msg(){ //add a msg_id field for app
		//require_once(dirname(__FILE__)."/../../interface/common/functions.inc.php");
		//$objManageData = new DataManage;
		$arrArgInbox = explode(",",$_REQUEST['param']);
		$sort_by = (isset($arrArgInbox[0]) && $arrArgInbox[0]!='')?$arrArgInbox[0]:"user_messages.user_message_id";
		$sort_order = (isset($arrArgInbox[1]) && $arrArgInbox[1]!='')?$arrArgInbox[1]:"DESC";
		$limit_from = (isset($arrArgInbox[2]) && $arrArgInbox[2]!='')?$arrArgInbox[2]:"0";
		$limit_to = (isset($arrArgInbox[3]) && $arrArgInbox[3]!='')?$arrArgInbox[3]:"30";
		$this->db_obj->qry ="SELECT GROUP_CONCAT(user_messages.user_message_id) AS user_message_id , 
						user_messages.message_read_status, user_messages.message_subject, 
						user_messages.message_text, 
						user_messages.message_urgent,
						user_messages.msg_id, 
						GROUP_CONCAT(CONCAT(users.fname,' ',users.mname,', ',users.lname) SEPARATOR '; ') AS message_sender_name, 
						user_messages.message_sender_id as message_sender_id,  
						user_groups.name as user_group, if(TRIM(patient_data.fname)!='',
						CONCAT(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id),'') AS patient_name, 
						patient_data.id AS message_patient_id,
						 DATE_FORMAT(message_send_date,'".get_sql_date_format()." %h:%i %p') AS msg_send_date,
						 DATE_FORMAT(message_send_date,'%m-%d-%Y %h:%i %p') AS msg_send_date_app, 
						  user_messages.flagged, user_messages.msg_icon 
						FROM user_messages LEFT JOIN patient_data ON patient_data.id = user_messages.patientId 
						LEFT JOIN users ON user_messages.message_to = users.id
						LEFT JOIN user_groups ON user_groups.id = users.user_group_id 
						where 
						user_messages.message_status = 0 and ((user_messages.message_sender_id = '".$this->authId."' AND user_messages.del_status = '1') OR (user_messages.message_to = '".$this->authId."' AND user_messages.receiver_delete ='1' AND user_messages.del_future_alert=0) )
						 and user_messages.Pt_Communication = 0 
						 and user_messages.message_sender_id != 0 
						group by user_messages.sent_to_groups, user_messages.message_subject, DATE_FORMAT(user_messages.message_send_date,'%Y-%m-%d')
						ORDER BY $sort_by $sort_order
						LIMIT $limit_from, $limit_to";
						
		$result = $this->db_obj->get_resultset_array();	
		return $result;
	}
	public function deleted_msg_total_count(){
		$this->db_obj->qry = "SELECT count(*) AS total_count
							FROM user_messages LEFT JOIN patient_data ON patient_data.id = user_messages.patientId 
						LEFT JOIN users ON user_messages.message_to = users.id
						LEFT JOIN user_groups ON user_groups.id = users.user_group_id 
						where 
						user_messages.message_status = 0 and ((user_messages.message_sender_id = '".$this->authId."' AND user_messages.del_status = '1') OR (user_messages.message_to = '".$this->authId."' AND user_messages.receiver_delete ='1' AND user_messages.del_future_alert=0) )
						 and user_messages.Pt_Communication = 0 
						 and user_messages.message_sender_id != 0 
						group by user_messages.sent_to_groups, user_messages.message_subject, 
						DATE_FORMAT(user_messages.message_send_date,'%Y-%m-%d')
						";
		$result_count = $this->db_obj->get_resultset_array();
		return count($result_count);
	}
	public function sent(){
		$arrArgInbox = explode(",",$_REQUEST['param']);
		$sort_by = (isset($arrArgInbox[0]) && $arrArgInbox[0]!='')?$arrArgInbox[0]:"pt_msg_id";
		$sort_order = (isset($arrArgInbox[1]) && $arrArgInbox[1]!='')?$arrArgInbox[1]:"DESC";
		$limit_from = (isset($arrArgInbox[2]) && $arrArgInbox[2]!='')?$arrArgInbox[2]:"0";
		$limit_to = (isset($arrArgInbox[3]) && $arrArgInbox[3]!='')?$arrArgInbox[3]:"30";
		$this->db_obj->qry = "SELECT GROUP_CONCAT(user_messages.user_message_id) AS user_message_id, 
						user_messages.message_read_status, 
						user_messages.message_subject, 
						user_messages.message_text,
						user_messages.message_urgent, 
						GROUP_CONCAT(CONCAT(users.fname,' ',users.mname,', ',users.lname) SEPARATOR '; ') AS message_sender_name, 
						user_messages.message_sender_id as message_sender_id,  
						user_groups.name as user_group, 
						if(TRIM(patient_data.fname)!='',CONCAT(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id),'') AS patient_name, 
						patient_data.id AS message_patient_id, 							
						DATE_FORMAT(message_send_date,'".get_sql_date_format()." %h:%i %p') AS msg_send_date,
						DATE_FORMAT(message_send_date,'%m-%d-%Y %h:%i %p') AS msg_send_date_app,  
						user_messages.flagged, 
						user_messages.msg_icon
						
						FROM user_messages LEFT JOIN patient_data ON patient_data.id = user_messages.patientId
						LEFT JOIN users ON user_messages.message_to = users.id 
						LEFT JOIN user_groups ON user_groups.id = users.user_group_id 
						where 
						user_messages.del_status ='0'
						and user_messages.message_sender_id = ".$this->authId." 
						and (date_format(user_messages.delivery_date,'%Y-%m-%d') <= '".date('Y-m-d')."' OR user_messages.delivery_date = '0000-00-00') 
						and user_messages.Pt_Communication = 0 and user_messages.message_sender_id != 0 
						group by (CASE WHEN (msg_id != 0) THEN msg_id ELSE (CONCAT_WS(',',user_messages.sent_to_groups, user_messages.message_subject, DATE_FORMAT(user_messages.message_send_date,'%Y-%m-%d'))) END)
						ORDER BY $sort_by $sort_order
						LIMIT $limit_from, $limit_to
						";
		$result = $this->db_obj->get_resultset_array();	
		return $result;
	}
	public function sent_total_count(){
		$this->db_obj->qry = "SELECT count(*) AS total_count
							FROM user_messages LEFT JOIN patient_data ON patient_data.id = user_messages.patientId
						LEFT JOIN users ON user_messages.message_to = users.id 
						LEFT JOIN user_groups ON user_groups.id = users.user_group_id 
						where 
						user_messages.del_status ='0'
						and user_messages.message_sender_id = ".$this->authId." 
						and (date_format(user_messages.delivery_date,'%Y-%m-%d') <= '".date('Y-m-d')."' OR user_messages.delivery_date = '0000-00-00') 
						and user_messages.Pt_Communication = 0 and user_messages.message_sender_id != 0 
						group by (CASE WHEN (msg_id != 0) THEN msg_id ELSE (CONCAT_WS(',',user_messages.sent_to_groups, user_messages.message_subject, DATE_FORMAT(user_messages.message_send_date,'%Y-%m-%d'))) END)
							";
		$result_count = $this->db_obj->get_resultset_array();
		return count($result_count);
	}
	
	public function sent_current_count(){
		$arrArgInbox = explode(",",$_REQUEST['param']);
		$sort_by = (isset($arrArgInbox[0]) && $arrArgInbox[0]!='')?$arrArgInbox[0]:"pt_msg_id";
		$sort_order = (isset($arrArgInbox[1]) && $arrArgInbox[1]!='')?$arrArgInbox[1]:"DESC";
		$limit_from = (isset($arrArgInbox[2]) && $arrArgInbox[2]!='')?$arrArgInbox[2]:"0";
		$limit_to = (isset($arrArgInbox[3]) && $arrArgInbox[3]!='')?$arrArgInbox[3]:"30";
		$this->db_obj->qry = "SELECT GROUP_CONCAT(user_messages.user_message_id) AS user_message_id, 
						user_messages.message_read_status, 
						user_messages.message_subject, 
						user_messages.message_text,
						user_messages.message_urgent, 
						GROUP_CONCAT(CONCAT(users.fname,' ',users.mname,', ',users.lname) SEPARATOR '; ') AS message_sender_name, 
						user_messages.message_sender_id as message_sender_id,  
						user_groups.name as user_group, 
						if(TRIM(patient_data.fname)!='',CONCAT(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id),'') AS patient_name, 
						patient_data.id AS message_patient_id, 							
						DATE_FORMAT(message_send_date,'".get_sql_date_format()." %h:%i %p') AS msg_send_date, 
						user_messages.flagged, 
						user_messages.msg_icon
						
						FROM user_messages LEFT JOIN patient_data ON patient_data.id = user_messages.patientId
						LEFT JOIN users ON user_messages.message_to = users.id 
						LEFT JOIN user_groups ON user_groups.id = users.user_group_id 
						where 
						user_messages.del_status ='0'
						and user_messages.message_sender_id = ".$this->authId." 
						and (date_format(user_messages.delivery_date,'%Y-%m-%d') <= '".date('Y-m-d')."' OR user_messages.delivery_date = '0000-00-00') 
						and user_messages.Pt_Communication = 0 and user_messages.message_sender_id != 0 
						group by (CASE WHEN (msg_id != 0) THEN msg_id ELSE (CONCAT_WS(',',user_messages.sent_to_groups, user_messages.message_subject, DATE_FORMAT(user_messages.message_send_date,'%Y-%m-%d'))) END)
						ORDER BY $sort_by $sort_order
						LIMIT $limit_from, $limit_to
						";
		$current_counter=imw_num_rows(imw_query($this->db_obj->qry));
		return $current_counter;
	}
	public function new_message(){
		//require_once(dirname(__FILE__)."/../../interface/common/functions.inc.php");
		//$objManageData = new DataManage;
		//$arrArg = explode(",",$_REQUEST['new_message']);
        
        $_REQUEST = array_map('rawurldecode',$_REQUEST);
		$patientId = isset($_REQUEST['patId'])?$_REQUEST['patId']:"";
		$authId = isset($_REQUEST['phyId'])?$_REQUEST['phyId']:"";
		$subject = isset($_REQUEST['subject'])?$_REQUEST['subject']:"";
		$message = isset($_REQUEST['message'])?$_REQUEST['message']:"";
		$message_urgent = isset($_REQUEST['message_urgent'])?$_REQUEST['message_urgent']:"";
		$Pt_Communication = isset($_REQUEST['Pt_Communication'])?$_REQUEST['Pt_Communication']:"";
		$OtherUser = isset($_REQUEST["OtherUser"]) ? $_REQUEST['OtherUser']: ""; 
		$mark_forwarded = isset($_REQUEST['forward']) ? $_REQUEST['forward']: "";
		$OtherUser = explode(",",$OtherUser);
		//-----BEGIN GET USER IDS------------------------------
		$arr_sent_to_groups = $OtherUser;
		foreach($arr_sent_to_groups as $val){
			if(is_numeric($val) == true){
					$groupQryRes[] = $val;
					$arrayChkUser[] = $val;
			}
			else{
				$query1 = "select users.id as id from users join user_groups on user_groups.id = users.user_group_id 
						where user_groups.name = '$val' and user_groups.status = '1'
						and users.delete_status = '0'
						";
				$result1 = imw_query($query1);
				if($result1 && imw_num_rows($result1)>0){
					while($rs1 = imw_fetch_array($result1)){
							$groupQryRes[] = $rs1['id'];
							$arrayChkUser[] = $rs1['id'];
					}
				}
			}
		}
		$arr_diffProviders = array();
		$temp_senttogroups = $OtherUser;
		for($i=0;$i<count($temp_senttogroups);$i++){
			if(intval($OtherUser[$i])>0){
				$arr_diffProviders[] = intval($OtherUser[$i]);
				unset($OtherUser[$i]);
			}
		}
		$_POST['sent_to_groups'] = implode(', ',$OtherUser);
		if(count($groupQryRes)>0) {
			$groupQryRes = array_unique($groupQryRes);	
			$arrMsgMaster = array();
			$arrMsgMaster['subject'] 	= $subject;
			$arrMsgMaster['msg'] 		= $message;
			$arrMsgMaster['sender_id'] 	= $authId;
			$arrMsgMaster['sent_date'] 	= date('Y-m-d H:i:s');
			$master_insert_id 			= AddRecords($arrMsgMaster,'user_messages_master');
			$arrMsgMaster['msg_id'] 	= $master_insert_id;
			
			
			$groupQryRes = array_unique($groupQryRes);
			foreach($groupQryRes as $i=>$send_user_id){	
					$arrMsgMaster = array();
					$arrMsgMaster['message_subject'] 	= $subject;
					$arrMsgMaster['message_text'] 		= $message;
					$arrMsgMaster['message_sender_id'] 	= $authId;
					$arrMsgMaster['message_send_date'] 	= date('Y-m-d H:i:s');
					$arrMsgMaster['msg_id'] 			= $master_insert_id;
				    $arrMsgMaster['message_to'] 		= $send_user_id;
					$arrMsgMaster['patientId'] 			= $patientId;
					$arrMsgMaster['sent_to_groups'] 	= $_POST['sent_to_groups'];
					$arrMsgMaster['message_urgent'] 	= $message_urgent;
				
					if($Pt_Communication == "1" && $patientId != '' && $patientId != '0') {
						$arrMsgMaster['Pt_Communication'] = 1;
						
						$insert_id = AddRecords($arrMsgMaster,'user_messages');
					}
					$Pt_Communication = "0";
					$arrMsgMaster['Pt_Communication'] = 0;
					$insert_id = AddRecords($arrMsgMaster,'user_messages');
			}
		}else {
			$arrMsgMaster = array();
			$arrMsgMaster['message_subject'] 	= $subject;
			$arrMsgMaster['message_text'] 		= $message;
			$arrMsgMaster['message_sender_id'] 	= $authId;
			$arrMsgMaster['message_send_date'] 	= date('Y-m-d H:i:s');
			$master_insert_id 			= AddRecords($arrMsgMaster,'user_messages_master');
			$arrMsgMaster['msg_id'] 	= $master_insert_id;
			$arrMsgMaster['Pt_Communication'] = 0;
			$arrMsgMaster['sent_to_groups'] 	= $_POST['sent_to_groups'];
			$arrMsgMaster['message_urgent'] 	= $message_urgent;
			if($Pt_Communication=="1" && $patientId != '' && $patientId != '0') {
				$insert_id = AddRecords($arrMsgMaster,'user_messages');
			}
			$Pt_Communication = "0";	
		}
		return true;	
	}
	public function reply_message(){
		
		//require_once(dirname(__FILE__)."/../../interface/common/functions.inc.php");
		//$objManageData = new DataManage;
		//$arrArg = explode(",",$_REQUEST['new_message']);
        $_REQUEST = array_map('rawurldecode',$_REQUEST);
		$patientId = isset($_REQUEST['patId'])?$_REQUEST['patId']:"";
		$authId = isset($_REQUEST['phyId'])?$_REQUEST['phyId']:"";
		$subject = isset($_REQUEST['subject'])?$_REQUEST['subject']:"";
		$message = isset($_REQUEST['message'])?$_REQUEST['message']:"";
		$replied_id = isset($_REQUEST['message_id'])?$_REQUEST['message_id']:"";
		
		if($authId!="" && $subject!="" && $message!="" && $replied_id!=""){
		$this->db_obj->qry  = "SELECT user_messages.*, DATE_FORMAT(message_send_date,'%m-%d-%Y %h:%i %p') AS msg_date_time,
									CONCAT(users.lname,', ',users.fname,' ',users.mname) AS sender_name
 									FROM user_messages 
									JOIN users ON users.id = user_messages.message_sender_id
									WHERE user_message_id = '".$replied_id."'";
		$result_qry = $this->db_obj->get_resultset_array();
		$senderName = $result_qry[0]['sender_name'];
		$ORsenderName = "Patient Co-ordinator";
		$sentDate = $result_qry[0]["msg_date_time"];
		$originalSubject = $result_qry[0]["message_subject"];
		$originalTextPrefix = '	
					
			----ORIGINAL MESSAGE----
			From: '.$senderName.'
			To: '.$ORsenderName.'
			Sent: '.$sentDate.'
			Subject: '.$originalSubject.'
					
		';
		$originalTextPrefix .= $result_qry[0]["message_text"];
		$message = $message.$originalTextPrefix;
		}
		
		$message_urgent = isset($_REQUEST['message_urgent'])?$_REQUEST['message_urgent']:"";
		$Pt_Communication = isset($_REQUEST['Pt_Communication'])?$_REQUEST['Pt_Communication']:"";
		$OtherUser = isset($_REQUEST["OtherUser"]) ? $_REQUEST['OtherUser']: ""; 
		$mark_forwarded = isset($_REQUEST['forward']) ? $_REQUEST['forward']: "";
		$OtherUser = explode(",",$OtherUser);
		//-----BEGIN GET USER IDS------------------------------
		$arr_sent_to_groups = $OtherUser;
		foreach($arr_sent_to_groups as $val){
			if(is_numeric($val) == true){
					$groupQryRes[] = $val;
					$arrayChkUser[] = $val;
			}
			else{
				$query1 = "select users.id as id from users join user_groups on user_groups.id = users.user_group_id 
						where user_groups.name = '$val' and user_groups.status = '1'
						and users.delete_status = '0'
						";
				$result1 = imw_query($query1);
				if($result1 && imw_num_rows($result1)>0){
					while($rs1 = imw_fetch_array($result1)){
							$groupQryRes[] = $rs1['id'];
							$arrayChkUser[] = $rs1['id'];
					}
				}
			}
		}
		$arr_diffProviders = array();
		$temp_senttogroups = $OtherUser;
		for($i=0;$i<count($temp_senttogroups);$i++){
			if(intval($OtherUser[$i])>0){
				$arr_diffProviders[] = intval($OtherUser[$i]);
				unset($OtherUser[$i]);
			}
		}
		$_POST['sent_to_groups'] = implode(', ',$OtherUser);
		if(count($groupQryRes)>0) {
			$groupQryRes = array_unique($groupQryRes);	
			$arrMsgMaster = array();
			$arrMsgMaster['subject'] 	= $subject;
			$arrMsgMaster['msg'] 		= $message;
			$arrMsgMaster['sender_id'] 	= $authId;
			$arrMsgMaster['sent_date'] 	= date('Y-m-d H:i:s');
			$master_insert_id 			= AddRecords($arrMsgMaster,'user_messages_master');
			$arrMsgMaster['msg_id'] 	= $master_insert_id;
			
			
			foreach($groupQryRes as $i=>$send_user_id){	
					$arrMsgMaster = array();
					$arrMsgMaster['message_subject'] 	= $subject;
					$arrMsgMaster['message_text'] 		= $message;
					$arrMsgMaster['message_sender_id'] 	= $authId;
					$arrMsgMaster['message_send_date'] 	= date('Y-m-d H:i:s');
					$arrMsgMaster['msg_id'] 			= $master_insert_id;
				    $arrMsgMaster['message_to'] 		= $send_user_id;
					$arrMsgMaster['patientId'] 			= $patientId;
					$arrMsgMaster['sent_to_groups'] 	= $_POST['sent_to_groups'];
					$arrMsgMaster['replied_id'] 		= $replied_id;
				
					if($Pt_Communication == "1" && $patientId != '' && $patientId != '0') {
						$arrMsgMaster['Pt_Communication'] = 1;
						
						$insert_id = AddRecords($arrMsgMaster,'user_messages');
					}
					$Pt_Communication = "0";
					$arrMsgMaster['Pt_Communication'] = 0;
					$insert_id = AddRecords($arrMsgMaster,'user_messages');
			}
		}else {
			$arrMsgMaster = array();
			$arrMsgMaster['message_subject'] 	= $subject;
			$arrMsgMaster['message_text'] 		= $message;
			$arrMsgMaster['message_sender_id'] 	= $authId;
			$arrMsgMaster['message_send_date'] 	= date('Y-m-d H:i:s');
			$master_insert_id 			= AddRecords($arrMsgMaster,'user_messages_master');
			$arrMsgMaster['msg_id'] 	= $master_insert_id;
			$arrMsgMaster['Pt_Communication'] = 0;
			$arrMsgMaster['sent_to_groups'] 	= $_POST['sent_to_groups'];
			$arrMsgMaster['replied_id'] 		= $replied_id;
			if($Pt_Communication=="1" && $patientId != '' && $patientId != '0') {
				$insert_id = AddRecords($arrMsgMaster,'user_messages');
			}
			$Pt_Communication = "0";	
		}
		return true;	
	}
	
	
}

?>