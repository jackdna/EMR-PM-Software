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
include_once(dirname(__FILE__).'/patient_app.php');
class patient_messages extends patient_app{	
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
	
	public function inbox(){
		$arrArgInbox = explode(",",$_REQUEST['param']);
		$sort_by = (isset($arrArgInbox[0]) && $arrArgInbox[0]!='')?$arrArgInbox[0]:"pt_msg_id";
		$sort_order = (isset($arrArgInbox[1]) && $arrArgInbox[1]!='')?$arrArgInbox[1]:"DESC";
		$limit_from = (isset($arrArgInbox[2]) && $arrArgInbox[2]!='')?$arrArgInbox[2]:"0";
		$limit_to = (isset($arrArgInbox[3]) && $arrArgInbox[3]!='')?$arrArgInbox[3]:"30";
		$this->db_obj->qry = "SELECT pm.pt_msg_id, pm.flagged, pm.message_urgent, pm.msg_icon, pm.is_read, 
							 CONCAT(CONCAT(pd.lname,', ',pd.fname,' ',pd.mname),' - ',pm.sender_id) as from_name,
							 pm.msg_subject,
							 DATE_FORMAT(pm.msg_date_time,'%m-%d-%Y %h:%i %p') AS msg_date_time1,
							 pm.msg_data,
							 pm.sender_id
							 FROM patient_messages pm
							 JOIN patient_data pd ON pd.id = pm.sender_id 	
							 WHERE pm.communication_type = 2 and pm.del_status = 0 and pm.is_done = 0 
							 ORDER BY $sort_by $sort_order
							 LIMIT $limit_from, $limit_to
							";
		$result = $this->db_obj->get_resultset_array();	
		return $result;
	}
	public function inbox_total_count(){
		$this->db_obj->qry = "SELECT COUNT(*) AS total_count
							 FROM patient_messages pm
							 JOIN patient_data pd ON pd.id = pm.sender_id 	
							 WHERE pm.communication_type = 2 and pm.del_status = 0 and pm.is_done = 0  
							";
		$result_count = $this->db_obj->get_resultset_array();
		return $result_count[0]['total_count'];
	}
	public function sent(){
		$arrArgInbox = explode(",",$_REQUEST['param']);
		$sort_by = (isset($arrArgInbox[0]) && $arrArgInbox[0]!='')?$arrArgInbox[0]:"pt_msg_id";
		$sort_order = (isset($arrArgInbox[1]) && $arrArgInbox[1]!='')?$arrArgInbox[1]:"DESC";
		$limit_from = (isset($arrArgInbox[2]) && $arrArgInbox[2]!='')?$arrArgInbox[2]:"0";
		$limit_to = (isset($arrArgInbox[3]) && $arrArgInbox[3]!='')?$arrArgInbox[3]:"30";
		$this->db_obj->qry = "SELECT pm.pt_msg_id,
							CONCAT(CONCAT(pd.lname,', ',pd.fname,' ',pd.mname),' - ',pm.receiver_id) as from_name,
							pm.msg_subject, 
							concat(SUBSTRING(users.fname,1,1), SUBSTRING(users.lname,1,1),': ',
							DATE_FORMAT(pm.msg_date_time,'%m-%d-%Y %h:%i %p')) AS msg_date_time1,
							DATE_FORMAT(pm.msg_date_time,'%m-%d-%Y %h:%i %p') AS msg_date_time1_app,
							pm.msg_data
							FROM patient_messages pm
							JOIN patient_data pd ON pd.id = pm.receiver_id
							INNER JOIN users ON pm.sender_id = users.id  	
							WHERE pm.communication_type = 1 and pm.del_status = 0 and pm.is_done = 0 
							ORDER BY $sort_by $sort_order
							LIMIT $limit_from, $limit_to
							";
		$result = $this->db_obj->get_resultset_array();	
		
		return $result;
	}
	public function sent_total_count(){
		$this->db_obj->qry = "SELECT COUNT(*) AS total_count
							FROM patient_messages pm
							JOIN patient_data pd ON pd.id = pm.receiver_id
							INNER JOIN users ON pm.sender_id = users.id  	
							WHERE pm.communication_type = 1 and pm.del_status = 0 and pm.is_done = 0  
							";
		$result_count = $this->db_obj->get_resultset_array();
		return $result_count[0]['total_count'];
	}
	public function message_read(){
		$this->db_obj->qry = "UPDATE patient_messages SET is_read = 1 WHERE pt_msg_id = ".$_REQUEST['message_id'];
		$result = $this->db_obj->run_query($this->db_obj->qry);
		return $result;	
	}
	public function delete_messgae(){
		$this->db_obj->qry = "UPDATE patient_messages SET del_status = 1 WHERE pt_msg_id IN (".$_REQUEST['message_id'].")";
		$result = $this->db_obj->run_query($this->db_obj->qry);
		return $result;	
		
	}
	public function new_message(){
		
		//$arrArg = explode(",",$_REQUEST['new_message']);
		$patientId = isset($_REQUEST['patId'])?$_REQUEST['patId']:false;
		$authId = isset($_REQUEST['phyId'])?$_REQUEST['phyId']:false;
		$subject = isset($_REQUEST['subject'])?$_REQUEST['subject']:"";
		$message = isset($_REQUEST['message'])?$_REQUEST['message']:"";
		$message_urgent = isset($_REQUEST['message_urgent'])?$_REQUEST['message_urgent']:"";
		$OtherUser = isset($_REQUEST["OtherUser"]) ? $_REQUEST['OtherUser']: ""; 
		
		if($patientId === false || $authId === false) die("patId and phyId is compulsory");
		/* check otheruser exists then perform the required actions */
		
		if($OtherUser != "")
		{
			$other_user_arr = explode(",", $OtherUser);
			if(count($other_user_arr) > 0)
			{
				$final_user_arr = array();				
				$this->db_obj->qry = "SELECT CONCAT('r', user_type_id) as r_id, users.id as u_id FROM user_type INNER JOIN users ON users.user_type = user_type.user_type_id WHERE users.delete_status = 0";
				$users_data = $this->db_obj->get_resultset_array();	
				$users_type_arr = array();
				
				foreach($users_data as $u_row)
				{
					$users_type_arr[$u_row['r_id']][] = $u_row['u_id'];	
				}				
				
				foreach($other_user_arr as $o_u_row)
				{
					if(in_array($o_u_row, $users_type_arr))
					{
						foreach($users_type_arr[$o_u_row] as $ac_u_id)
						{
							$final_user_arr[] = $ac_u_id;	
						}
					}
					else
					{
						$final_user_arr[] = $o_u_row;
					}					
				}
				
				$final_user_arr = array_unique($final_user_arr);
				
				foreach($final_user_arr as $final_u_id)
				{
					$arr = array(imw_real_escape_string($subject), $final_u_id, imw_real_escape_string($message), $authId, $message_urgent);
					$this->db_obj->qry= "INSERT INTO user_messages 
											SET message_subject = ?,
											message_to = ?, 
											message_text = ?,
											message_sender_id = ?,
											message_urgent = ?";
							
					$result = $this->db_obj->run_query($this->db_obj->qry, $arr);	
				}
			}
		}	
		
				
		
		if($patientId != "" && $authId!="" && $subject!=""){
			$this->db_obj->qry  = "INSERT INTO patient_messages 
									SET receiver_id = '".$patientId."', 
									sender_id = '".$authId."', 
									communication_type = 1, 
									msg_subject = '".imw_real_escape_string($subject)."', 
									msg_data = '".imw_real_escape_string($message)."', 
									message_urgent='".$message_urgent."'";	
			$result = $this->db_obj->run_query($this->db_obj->qry);
			return $result;
		}else{
			return false;
		}
			
	}
	public function reply_message(){
		//$arrArg = explode(",",$_REQUEST['new_message']);
		$patientId = $_REQUEST['patId'];
		$patientId = isset($_REQUEST['patId'])?$_REQUEST['patId']:"";
		$authId = isset($_REQUEST['phyId'])?$_REQUEST['phyId']:"";
		$subject = isset($_REQUEST['subject'])?$_REQUEST['subject']:"";
		$message = isset($_REQUEST['message'])?$_REQUEST['message']:"";
		$message_urgent = isset($_REQUEST['message_urgent'])?$_REQUEST['message_urgent']:"";
		$replied_id = isset($_REQUEST['message_id'])?$_REQUEST['message_id']:"";
		$OtherUser = isset($_REQUEST["OtherUser"]) ? $_REQUEST['OtherUser']: ""; 
		
		if($patientId != "" && $authId!="" && $subject!="" && $message!="" && $replied_id!=""){
		$this->db_obj->qry  = "SELECT *, DATE_FORMAT(msg_date_time,'%m-%d-%Y %h:%i %p') AS msg_date_time FROM patient_messages WHERE pt_msg_id = '".$replied_id."'";
		$result_qry = $this->db_obj->get_resultset_array();
		$curr_pt_arr = core_get_patient_name($result_qry[0]["sender_id"]);
		$name_sendTo = $curr_pt_arr['2'].', '.$curr_pt_arr['1'];
		$ORsenderName = "Patient Co-ordinator";
		$sentDate = $result_qry[0]["msg_date_time"];
		$originalSubject = $result_qry[0]["msg_subject"];
		$originalTextPrefix = '	
					
			----ORIGINAL MESSAGE----
			From: '.$name_sendTo.'
			To: '.$ORsenderName.'
			Sent: '.$sentDate.'
			Subject: '.$originalSubject.'
					
		';
		$originalTextPrefix .= $result_qry[0]["msg_data"];
		$msg_data = $message.$originalTextPrefix;
		
		if($OtherUser != "")
		{
			$other_user_arr = explode(",", $OtherUser);
			if(count($other_user_arr) > 0)
			{
				$final_user_arr = array();				
				$this->db_obj->qry = "SELECT CONCAT('r', user_type_id) as r_id, users.id as u_id FROM user_type INNER JOIN users ON users.user_type = user_type.user_type_id WHERE users.delete_status = 0";
				$users_data = $this->db_obj->get_resultset_array();	
				$users_type_arr = array();
				
				foreach($users_data as $u_row)
				{
					$users_type_arr[$u_row['r_id']][] = $u_row['u_id'];	
				}				
				
				foreach($other_user_arr as $o_u_row)
				{
					if(in_array($o_u_row, $users_type_arr))
					{
						foreach($users_type_arr[$o_u_row] as $ac_u_id)
						{
							$final_user_arr[] = $ac_u_id;	
						}
					}
					else
					{
						$final_user_arr[] = $o_u_row;
					}					
				}
				
				$final_user_arr = array_unique($final_user_arr);
				
				foreach($final_user_arr as $final_u_id)
				{
					$arr = array(imw_real_escape_string($subject), $final_u_id, imw_real_escape_string($message), $authId, $message_urgent);
					$this->db_obj->qry= "INSERT INTO user_messages 
											SET message_subject = ?,
											message_to = ?, 
											message_text = ?,
											message_sender_id = ?,
											message_urgent = ?";
							
					$result = $this->db_obj->run_query($this->db_obj->qry, $arr);	
				}
			}
		}	
				
		$this->db_obj->qry  = "INSERT INTO patient_messages 
								SET receiver_id = '".$patientId."', 
								sender_id = '".$authId."', 
								communication_type = 1, 
								msg_subject = '".imw_real_escape_string($subject)."', 
								msg_data = '".imw_real_escape_string($msg_data)."', 
								message_urgent='".$message_urgent."',
								replied_id='".$replied_id."'";	
		$result = $this->db_obj->run_query($this->db_obj->qry);
		return $result;	
		}else{
			return false;
		}
	}
	function notifications(){
		$arrArg = explode(",",$_REQUEST['param']);
		$sort_by = (isset($arrArg[0]) && $arrArg[0]!='')?$arrArg[0]:"reqDateTime";
		$sort_order = (isset($arrArg[1]) && $arrArg[1]!='')?$arrArg[1]:"DESC";
		$limit_from = (isset($arrArg[2]) && $arrArg[2]!='')?$arrArg[2]:"0";
		$limit_to = (isset($arrArg[3]) && $arrArg[3]!='')?$arrArg[3]:"30";
		$this->db_obj->qry  = "SELECT id,pt_id,title_msg,reqDateTime,is_approved,
								DATE_FORMAT(reqDateTime,'%m-%d-%Y %h:%i %p') as reqDateTime1 
								FROM iportal_req_changes 
								WHERE del_status=0 
								ORDER BY $sort_by $sort_order 
								LIMIT $limit_from, $limit_to";
		//echo $this->db_obj->qry;die();
		$result = $this->db_obj->get_resultset_array();
		foreach($result as $key=>$arr){
			$arrPatient = core_get_patient_name($arr['pt_id']);
			$patientName = $arrPatient['2'].', '.$arrPatient['1'];	
			$patientName .= ' - '.$arr['pt_id'];
			$result[$key]['patient'] = $patientName;
			$result[$key]['data'] = $this->pt_changes_msg($arr['id']);
		}
		return $result;
								
	}
	public function notifications_total_count(){
		$this->db_obj->qry = "SELECT COUNT(*) AS total_count
							 FROM iportal_req_changes	
							 WHERE del_status=0 
							";
		$result_count = $this->db_obj->get_resultset_array();
		return $result_count[0]['total_count'];
	}
	function pt_changes_msg($id)
	{
		$this->db_obj->qry  = "SELECT *,
								DATE_FORMAT(reqDateTime,'%m-%d-%Y %h:%i %p') as reqDateTime 
								FROM iportal_req_changes 
								WHERE id=".$id." order by id DESC";
		$result = $this->db_obj->get_resultset_array();
		$notification_data = $result[0];
		$changes_modification = $this->pt_changes_modification($notification_data);
		$change_data_msg = "";
		if($notification_data["action"] == "edit" && $notification_data["tb_name"] == "lists" && ($notification_data["col_name"] == "begdate" || $notification_data["col_name"] == "enddate"))
		{
			$new_val_arr_date = explode("-", $notification_data["new_val"]);
			if(count($new_val_arr_date) == 3)
			{
				if(strlen($new_val_arr_date[2]) != 4)
				{
					$new_val_date_final = $new_val_arr_date[1].'-'.$new_val_arr_date[2].'-'.$new_val_arr_date[0];		
					$notification_data["new_val"] = $new_val_date_final;							
				}
			}
		}
		if($notification_data["action"] == "edit")
		{
			$old_val_label = $notification_data["old_val"];
			$new_val_label = $notification_data["new_val"];
			if($notification_data["old_val_lbl"] != "" && $notification_data["new_val_lbl"] != "")
			{
				$old_val_label = $notification_data["old_val_lbl"];
				$new_val_label = $notification_data["new_val_lbl"];			
			}
			
			$change_data_msg = "<table cellpadding='3' cellspacing='0' style='border:1px solid #333;'>
									<tr>
										<td colspan='2' style='font-weight:bold;'>".$notification_data["col_lbl"]." changed </td>
									</tr>
									<tr>
										<td>Old Value</td>
										<td>New Value</td>
									</tr>
									<tr>
										<td>".$old_val_label."</td>
										<td>".$new_val_label."</td>
									</tr>
									".$changes_modification."																
								</table>";	
		}
		else
		{
			$change_data_msg = 	$notification_data["new_val_lbl"];
		}
		return $change_data_msg;
	}

	function pt_changes_modification($notification_data)
	{
		$return_html ="";
		$tb_name  = $notification_data["tb_name"];
		$col_name = $notification_data["col_name"];
		
		if($tb_name == "insurance_data" && $col_name == "provider")
		{
			$changed_val = $notification_data["new_val"];
			$changed_val = imw_real_escape_string($changed_val);
			$req_qry = "SELECT id FROM insurance_companies WHERE name='".$changed_val."' and ins_del_status='0' LIMIT 1";
			$req_qry_obj = imw_query($req_qry);
			if(imw_num_rows($req_qry_obj) == 0)
			{
				$return_html = '<tr style="font-size:15px;padding:5px;color:#000;background-color:#ffbbbb;">
					<td colspan="2">The insurance company not exists in the database. Please add it from the Admin->Billing->Insurance before approving it.</td>
				</tr>';	
			}
		}
		return $return_html;
	}
	
	public function approve_notification(){
		$row_id = $_REQUEST['message_id'];
		$this->db_obj->qry  = "SELECT * FROM iportal_req_changes
							   WHERE is_approved = 0 
							  		AND id = ".$row_id;	
		$result = $this->db_obj->get_resultset_array();
		foreach($result as $key=>$arr){
			$iportal_extreme_action = $this->iportal_special_changes($arr,$row_id);
			if($iportal_extreme_action == 0)
			{
				if($arr["action"] == "edit" && trim($arr["patientIdColName"]) != "")
				{
					$this->db_obj->qry = "UPDATE ".$arr["tb_name"]." SET ".$arr["col_name"]." = '".$arr["new_val"]."' WHERE ".$arr["patientIdColName"]." = '".$arr["pt_id"]."' && ".$arr["pri_col_name"]." = '".$arr["col_pri_id"]."'";	
					$query_run = $this->db_obj->run_query($this->db_obj->qry);
					if($query_run)
					{
						$this->db_obj->qry = "UPDATE iportal_req_changes SET is_approved=1 WHERE id = ".$row_id;
						$this->db_obj->run_query($this->db_obj->qry);			
					}
				}
				else if($arr["action"] == "insert" && trim($arr["tb_name"]) != "")
				{
					$insert_qry = $arr["new_val"];
					$log_qry = $arr["log_query"];
					if(strpos($insert_qry,$arr["tb_name"]) !== false)				
					{
						$query_run = $this->db_obj->run_query($insert_qry);
						if($query_run)
						{
							$this->db_obj->qry = "UPDATE iportal_req_changes SET is_approved=1 WHERE id = ".$row_id;
							$this->db_obj->run_query($this->db_obj->qry);					
						}
						if(trim($log_qry) != "")
						{
							$query_run = imw_query($log_qry);
						}										
					}
					
					if(trim($arr["tb_name"]) == "insurance_data")
					{
						$req_qry = "UPDATE iportal_req_changes SET is_approved=1 WHERE id = ".$row_id;
						imw_query($req_qry);							
					}					
				}
				else if($arr["action"] == "updateQry" && trim($arr["tb_name"]) != "")
				{
					$update_qry = $arr["new_val"];
					$log_qry = $arr["log_query"];
					if(strpos($update_qry,$arr["tb_name"]) !== false)				
					{
						$query_run = $this->db_obj->run_query($update_qry);
						if($query_run)
						{
							$req_qry = "UPDATE iportal_req_changes SET is_approved=1 WHERE id = ".$row_id;
							$this->db_obj->run_query($req_qry);					
						}
						if(trim($log_qry) != "")
						{
							$query_run = $this->db_obj->run_query($log_qry);
						}
					}
				}				
			}
			
		}
		$this->db_obj->qry  = "SELECT is_approved FROM iportal_req_changes
							   WHERE id = ".$row_id;	
		$result = $this->db_obj->get_resultset_array();
		return $result[0]['is_approved'];
	}
	function iportal_special_changes($iportal_data,$row_id)
	{
		$tb_name  = $iportal_data["tb_name"];
		$col_name = $iportal_data["col_name"];	
		
		if($tb_name == "insurance_data" && $col_name == "provider")
		{
			$changed_val = $iportal_data["new_val"];
			$changed_val = imw_real_escape_string($changed_val);
			$this->db_obj->qry = "SELECT id FROM insurance_companies WHERE name='".$changed_val."' and ins_del_status='0' LIMIT 1";
			$result = $this->db_obj->get_resultset_array();
			$req_qry_obj = imw_query($req_qry);
			if(count($result) > 0)
			{
				foreach($result as $row){
					$insurance_data = $row;
					$insurance_data_id = $insurance_data["id"];
		
					$update_qry = "UPDATE ".$iportal_data["tb_name"]." SET ".$iportal_data["col_name"]." = '".$insurance_data_id."' WHERE ".$iportal_data["patientIdColName"]." = '".$iportal_data["pt_id"]."' && ".$iportal_data["pri_col_name"]." = '".$iportal_data["col_pri_id"]."'";	
					$query_run = imw_query($update_qry);
					if($query_run)
					{
						$req_qry = "UPDATE iportal_req_changes SET is_approved=1 WHERE id = ".$row_id;
						imw_query($req_qry);					
					}	
				}
			}
			
			return 1;
		}
		else
		{
			return 0;	
		}
	}
	
	function decline_notification(){
		$this->db_obj->qry  = "UPDATE iportal_req_changes SET is_approved=2 WHERE id = ".$_REQUEST['message_id'];
		//echo $this->db_obj->qry;die();
		$result = $this->db_obj->run_query($this->db_obj->qry);
		return $result;
	}
	public function delete_notification(){
		$this->db_obj->qry = "UPDATE iportal_req_changes  SET del_status = 1 WHERE id IN (".$_REQUEST['message_id'].")";
		$result = $this->db_obj->run_query($this->db_obj->qry);
		return $result;	
		
	}
	
}

?>
