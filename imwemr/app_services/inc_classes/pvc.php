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
set_time_limit(900);
include_once(dirname(__FILE__).'/patient_app.php');
class pvc extends patient_app{	
	var $reqModule;
	var $arrProvider = array();
	public function __construct($patient){
		parent::__construct($patient);
	}
	
	function get_pvc(){
		$filter = (isset($_REQUEST['filter']) && $_REQUEST['filter']!="")?$_REQUEST['filter']:"Active";
		if(strtolower($filter) == "active"){
			$where = "AND user_messages.del_status = 0 AND user_messages.edit_status = 0";
		}else if(strtolower($filter) == "all"){
			$where = "";
		}
		$this->db_obj->qry = "SELECT user_messages.user_message_id AS id,
							DATE_FORMAT(user_messages.message_send_date,'%m-%d-%Y') AS created_date, 
							user_messages.approved ,
							user_messages.message_subject AS task_subject,
							CONCAT(users.lname,', ',users.fname,' ',users.mname) AS user_name,
							user_messages.message_text AS message_text
							
							FROM user_messages
							LEFT JOIN patient_data ON user_messages.patientId = patient_data.id
							LEFT JOIN users ON users.id = user_messages.message_sender_id
							LEFT JOIN users usr2 ON usr2.id = user_messages.del_operator_id
							WHERE  patient_data.id = ".$this->patient."  
								AND user_messages.Pt_Communication = '1' 
								 $where
							GROUP BY message_send_date,message_text
							ORDER BY user_messages.message_send_date DESC
								";
		$result = $this->db_obj->get_resultset_array();
			
		return $result;						
	}
	function new_pvc(){
		//file_put_contents('tset.txt',print_r($_REQUEST,true));//m-d-y
		$return = "";
		if(isset($_REQUEST['date']) && $_REQUEST['date']!="")
		{
			list($mm,$dd,$yy)=explode('-',$_REQUEST['date']);
			$_REQUEST['date']=$yy.'-'.$mm.'-'.$dd;
		}
		$date = (isset($_REQUEST['date']) && $_REQUEST['date']!="")?date('Y-m-d', strtotime($_REQUEST['date'])):date('Y-m-d');
		$message_send_date = $date.' '.date('H:i:s');
		
		//decode
		$t_msg = $_REQUEST['message']; $t_subj = $_REQUEST['subject'];
		if(strpos($t_msg,"%20")!==false || strpos($t_subj,"%20")!==false){
			$t_subj = urldecode($t_subj);	
			$t_msg = urldecode($t_msg);
		}
		
		$message_text = core_refine_user_input($t_msg);
		$message_subject = core_refine_user_input($t_subj);
		$patient = $this->patient;
		$message_sender_id = $this->authId;
		$approved = (isset($_REQUEST['app_status']) && $_REQUEST['app_status']!="")?$_REQUEST['app_status']:"accept";
		if(strtolower($approved) != "accept" &&  strtolower($approved) != "decline")
		$approved = "";
		$Pt_Communication = 1;
		$message_status = 0;
		$message_to = $this->authId;
		if($message_subject !=""  && $message_text !="" && $patient !="" && $message_sender_id !=""){
			$this->db_obj->qry = " INSERT INTO user_messages
									SET message_send_date = '".$message_send_date."',
									message_text = '".$message_text."',
									message_subject = '".$message_subject."',
									patientId = '".$patient."',
									message_sender_id = '".$message_sender_id."',
									approved = '".$approved."',
									Pt_Communication = '".$Pt_Communication."',
									message_status = '".$message_status."',
									message_to = '".$message_to."'
								";
			$return = $this->db_obj->run_query($this->db_obj->qry);		
		}
		return $return;				
	}
	function edit_pvc(){
		$return = "";
		$date = (isset($_REQUEST['date']) && $_REQUEST['date']!="")?date('Y-m-d', strtotime($_REQUEST['date'])):date('Y-m-d');
		
		$message_send_date = $date.' '.date('H:i:s');
		//decode
		$t_msg = $_REQUEST['message']; $t_subj = $_REQUEST['subject'];
		if(strpos($t_msg,"%20")!==false || strpos($t_subj,"%20")!==false){
			$t_subj = urldecode($t_subj);	
			$t_msg = urldecode($t_msg);
		}
		
		$message_text = core_refine_user_input($t_msg);
		$message_subject = core_refine_user_input($t_subj);
		
		$patient = $this->patient;
		$message_sender_id = $this->authId;
		
		$approved = (isset($_REQUEST['app_status']) && $_REQUEST['app_status']!="")?$_REQUEST['app_status']:"accept";
		if($approved != "accept" && $approved != "decline")
		$approved = "";
		
		$Pt_Communication = 1;
		$message_status = 0;
		$message_to = $this->authId;
		$edit_id = $_REQUEST['id'];
		if($message_subject !=""  && $message_text !="" && $patient !="" && $message_sender_id !="" && $edit_id != ""){
			$this->db_obj->qry = " INSERT INTO user_messages (patientId, Pt_Communication )
									SELECT patientId,Pt_Communication FROM user_messages WHERE user_message_id ='".$edit_id."'
									";
			$return = imw_query($this->db_obj->qry);	
			$insert_id = imw_insert_id();	
			
			$this->db_obj->qry = "UPDATE user_messages 
									SET message_subject = '".$message_subject."', 
									message_text = '".$message_text."',
									approved = '".$approved."',
									message_sender_id = '".$message_sender_id."',
									message_send_date = '".date("Y-m-d H:i:s")."'
									WHERE user_message_id = '".$insert_id."'
								";
			$this->db_obj->run_query($this->db_obj->qry);	
			
			$this->db_obj->qry = "UPDATE user_messages 
									SET edit_status = 1, 
									edit_user_message_id = '".$insert_id."'
									WHERE user_message_id = '".$edit_id."'
								";
			$this->db_obj->run_query($this->db_obj->qry);							
		}
		return $return;				
	}
	function del_pvc(){
		if($this->authId !=""  && $_REQUEST['id'] !="" && $this->patient !=""){
			$this->db_obj->qry = "UPDATE user_messages 
									SET del_status = 1, 
									del_operator_id = '".$this->authId."',
									del_datetime = '".date("Y-m-d H:i:s")."'
									WHERE user_message_id = '".$_REQUEST['id']."'
									AND patientId = '".$this->patient."'
									";
			$return = $this->db_obj->run_query($this->db_obj->qry);	
		}
		return $return;	
	}
	
}

?>