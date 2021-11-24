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
include_once(dirname(__FILE__).'/medical_hx.php');
class problem_list extends medical_hx{
	
	public function __construct(){
		parent::__construct();
	}
	function get_problem_list(){
		$arrReturn = array();
		
		$this->db_obj->qry = "SELECT id,
								CONCAT(DATE_FORMAT(onset_date,'%m-%d-%Y'),' ',DATE_FORMAT(OnsetTime,'%h:%i %p')) AS onset_date,
								problem_name,
								prob_type,
								ccda_code,
								status,
								DATE_FORMAT(timestamp,'%m-%d-%Y') AS date,
								DATE_FORMAT(timestamp,'%h:%i %p') AS time,
								user_id AS op_id
								FROM pt_problem_list 
								WHERE pt_id='".$this->patient."' 
								AND status = 'Active' 
								ORDER BY onset_date DESC";
		$result = $this->db_obj->get_resultset_array();	
		//print_r($result);
		//die();
		foreach($result as $record){
			$record['op_id'] = $this->get_user_initials($record['op_id']);
			$arrReturn[] = $record;
		}
		return $arrReturn;
	}
	function get_problem_list_app(){
		$status = $_REQUEST['status'];
		$arrReturn = array();
		if($status == "") {
			
			$this->db_obj->qry = "SELECT id,
									CONCAT(DATE_FORMAT(onset_date,'%m-%d-%Y'),' ',DATE_FORMAT(OnsetTime,'%h:%i %p')) AS onset_date,
									problem_name,
									prob_type,
									ccda_code,
									status,
									DATE_FORMAT(timestamp,'%m-%d-%Y') AS date,
									DATE_FORMAT(timestamp,'%h:%i %p') AS time,
									user_id AS op_id
									FROM pt_problem_list 
									WHERE pt_id='".$this->patient."' 
									AND status != 'Deleted'	 
									ORDER BY onset_date DESC";
			$result = $this->db_obj->get_resultset_array();
				
			//print_r($result);
			//die();
			
			foreach($result as $record){
				$button[0]['count'] = "";
				$but = $record['id'];
				
					$record['op_id'] = $this->get_user_initials($record['op_id']);
					$this->db_obj->qry = "SELECT * from pt_problem_list_log where problem_id = '$but'";
					
					$button = $this->db_obj->get_resultset_array();
					 //print_r($button);
					$counter=count($button);
					//echo "<br>";
					
					if($counter > 1){
						$record['array_data_status'] = 1; 
						}
					else {
						$record['array_data_status'] = 0;
					}
					$arrReturn[] = $record;
			} 
		}
		
		else {
			$this->db_obj->qry = "SELECT id,
									CONCAT(DATE_FORMAT(onset_date,'%m-%d-%Y'),' ',DATE_FORMAT(OnsetTime,'%h:%i %p')) AS onset_date,
									problem_name,
									prob_type,
									ccda_code,
									status,
									DATE_FORMAT(timestamp,'%m-%d-%Y') AS date,
									DATE_FORMAT(timestamp,'%h:%i %p') AS time,
									user_id AS op_id
									FROM pt_problem_list 
									WHERE pt_id='".$this->patient."' 
										AND status = '$status' 
										AND status != 'Deleted'
									ORDER BY onset_date DESC";
			$result = $this->db_obj->get_resultset_array();	
			//print_r($result);
			//die();
			foreach($result as $record){
			$button[0]['count'] = "";
			$but = $record['id'];
			
				$record['op_id'] = $this->get_user_initials($record['op_id']);
				$this->db_obj->qry = "SELECT * from pt_problem_list_log where problem_id = '$but'";
				
				$button = $this->db_obj->get_resultset_array();
				 //print_r($button);
				$counter=count($button);
				//echo "<br>";
				
				if($counter > 1){
					$record['array_data_status'] = 1; }
				else {
					$record['array_data_status'] = 0;
				}
				$arrReturn[] = $record;
			} 
		}
		
		return $arrReturn;
	}
	
	function save_problem_list(){
		$onset_date = (isset($_REQUEST['onset_date']) && $_REQUEST['onset_date']!="")?imw_real_escape_string($_REQUEST['onset_date']):"";
		$onset_time = (isset($_REQUEST['onset_time']) && $_REQUEST['onset_time']!="")?imw_real_escape_string($_REQUEST['onset_time']):"";
		$problem_name = (isset($_REQUEST['problem_name']) && $_REQUEST['problem_name']!="")?imw_real_escape_string($_REQUEST['problem_name']):"";
		$prob_type = (isset($_REQUEST['prob_type']) && $_REQUEST['prob_type']!="")?imw_real_escape_string($_REQUEST['prob_type']):"";
		$status = (isset($_REQUEST['status']) && $_REQUEST['status']!="")?imw_real_escape_string($_REQUEST['status']):"Active";
		$code = (isset($_REQUEST['code']) && $_REQUEST['code']!="")?imw_real_escape_string($_REQUEST['code']):"";
		
		if($problem_name != "" && $this->patient!="" && $this->authId !="" && $this->authId!=0){
			$query = "INSERT INTO pt_problem_list 
								SET pt_id = '".$this->patient."',
									user_id = '".$this->authId."',
									problem_name = '".$problem_name."',
									onset_date = '".$onset_date."',
									OnsetTime = '".$this->convert_time($onset_time)."',
									status = '".$status."',
									prob_type = '".$prob_type."',
									ccda_code = '".$code."'
										
								";
			$result = imw_query($query);
				
			$prob_id =  imw_insert_id();
				 
			if($prob_id != "" || $prob_id = 0){
					
				$this->db_obj->qry = "INSERT INTO pt_problem_list_log 
									SET problem_id = '".$prob_id."',
										pt_id = '".$this->patient."' ,
										user_id = '".$this->authId."',
										problem_name = '".$problem_name."',
										onset_date = '".$onset_date."',
										OnsetTime = '".$this->convert_time($onset_time)."',
										status = '".$status."',
										prob_type = '".$prob_type."',
										ccda_code = '".$code."'
											
									";
				$result = $this->db_obj->run_query($this->db_obj->qry);
				return $result;
			}
		}
	}
	
	function update_problem_list(){
		$onset_date = (isset($_REQUEST['onset_date']) && $_REQUEST['onset_date']!="")?imw_real_escape_string($_REQUEST['onset_date']):"";
		$onset_time = (isset($_REQUEST['onset_time']) && $_REQUEST['onset_time']!="")?imw_real_escape_string($_REQUEST['onset_time']):"";
		$problem_name = (isset($_REQUEST['problem_name']) && $_REQUEST['problem_name']!="")?imw_real_escape_string($_REQUEST['problem_name']):"";
		$prob_type = (isset($_REQUEST['prob_type']) && $_REQUEST['prob_type']!="")?imw_real_escape_string($_REQUEST['prob_type']):"";
		$status = (isset($_REQUEST['status']) && $_REQUEST['status']!="")?imw_real_escape_string($_REQUEST['status']):"";
		$code = (isset($_REQUEST['code']) && $_REQUEST['code']!="")?imw_real_escape_string($_REQUEST['code']):"";
		$id = $_REQUEST['id'];
		
		if($id != "" &&  $this->patient!="" && $this->authId !="" && $this->authId!=0){
		
			 $query = "UPDATE pt_problem_list 
								SET 
								user_id = '".$this->authId."'
								";
				if($problem_name!="")$query.=",problem_name = '".$problem_name."'";
				
				if($onset_date!="")$query.=",onset_date = '".$onset_date."'";
				
				if($onset_time!="")$query.=",OnsetTime  = '".$this->convert_time($onset_time)."'";
				
				if($status!="")$query.=",status  = '".$status."'";
				
				if($prob_type!="")$query.=",prob_type  = '".$prob_type."'";
				
				if($code!="")$query.=",ccda_code  = '".$code."'";
				
				$query.=" WHERE id ='".$id."'
								    AND pt_id = '".$this->patient."'";		
											
			$result1 = imw_query($query);
			
			  $query = "select * from pt_problem_list where id = '".$id."'";
			  $res = imw_query($query);
			  $fetch = imw_fetch_assoc($res);
			  $time = $this->convert_time($fetch['OnsetTime']);
			 
			$this->db_obj->qry = "INSERT INTO pt_problem_list_log 
								SET problem_id = '".$fetch['id']."',
									pt_id = '".$this->patient."' ,
									user_id = '".$this->authId."',
									problem_name = '".$fetch['problem_name']."',
									onset_date = '".$fetch['onset_date']."',
								    OnsetTime = '".$time."',
									status = '".$fetch['status']."',
								prob_type = '".$fetch['prob_type']."',
									ccda_code = '".$fetch['ccda_code']."'
									
							";
			$result = $this->db_obj->run_query($this->db_obj->qry);
			return true;
		}
		else{
		
			return false;
		}
	}
	
	
	function delete_problem_list(){
		$id = (isset($_REQUEST['id']) && $_REQUEST['id']!="")?$_REQUEST['id']:"";
		if($id != "" && $this->patient!=""){
			$this->db_obj->qry = "UPDATE pt_problem_list
								 SET status = 'Deleted'
								 WHERE id IN (".$id.")
								 AND pt_id = '".$this->patient."'
							";
			$result = $this->db_obj->run_query($this->db_obj->qry);
			return $result;
		}
	}
	
	function convert_time($time){
		$timeArr = preg_split('/(:)|( )/',$time);
		if($timeArr[0] != 12){
			if(strtolower($timeArr[2]) == 'pm'){
				$timeArr[0] = $timeArr[0] + 12;
			}
		}
		else{
			if(strtolower($timeArr[2]) == 'am'){
				$timeArr[0] = $timeArr[0] + 12;
			}
		}
		return $timeArr[0] .':'.$timeArr[1].':00'; 
	}


	function problem_history(){
		$arrReturn = array();
		$id = $_REQUEST['id'];
		$this->db_obj->qry = "SELECT id,
								CONCAT(DATE_FORMAT(onset_date,'%m-%d-%Y'),' ',DATE_FORMAT(OnsetTime,'%h:%i %p')) AS onset_date,
								problem_name,
								prob_type,
								ccda_code,
								status,
								DATE_FORMAT(timestamp,'%m-%d-%Y') AS date,
								DATE_FORMAT(timestamp,'%h:%i %p') AS time,
								user_id AS op_id
								FROM pt_problem_list_log 
								WHERE pt_id='".$this->patient."' 
									AND  problem_id IN (".$id.")
								ORDER BY onset_date DESC";
		$result = $this->db_obj->get_resultset_array();	
		foreach($result as $record){
			$code = $record['problem_name'];
			if(preg_match('/-/',$code)){
				$code = explode('-',$code);
				$record['code'] = $code[1];
			}
			else{
				$code = explode('(',$code);
				$record['code'] = str_replace(")", "" ,$code[1]);
			}
			$arrReturn[] = $record;
		}
		return $arrReturn;
	}
	
	function get_problem_name(){
		
		$this->db_obj->qry = "SELECT diag_description , d_prac_code from diagnosis_code_tbl ";
		$med_name = $this->db_obj->get_resultset_array();
		foreach($med_name as $med){
			$name = $med['diag_description'];
			$code = $med['d_prac_code'];
			$med_name = $name ."-".$code;
			$arrReturn1[] = $arrReturn1.$med_name;
		}
		$arrReturn['med_name'] = $arrReturn1;
		
		return $arrReturn;
	}
		
}
?>

