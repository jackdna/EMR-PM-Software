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

class medication extends medical_hx{
	public function __construct(){
		parent::__construct();
	}
	
	function print_getData($val){
		$name_str="";
		$getDetailsStr = "SELECT fname,mname,lname FROM users WHERE id = '$val'";
		$getDetailsQry = imw_query($getDetailsStr);
		$getDetailsRow = imw_fetch_array($getDetailsQry);
		$name_str=$getDetailsRow['fname'][0].$getDetailsRow['mname'][0].$getDetailsRow['lname'][0];
		return $name_str;
	}

	function get_physician_name(){
		
		$getDetailsStr = "SELECT id,fname,mname,lname FROM users where delete_status != 1";
		$getDetailsQry = imw_query($getDetailsStr);
		$i=0;
		while($getDetailsRow = imw_fetch_assoc($getDetailsQry)){
			$first = trim($getDetailsRow['fname']);
			$middle = trim($getDetailsRow['mname']);
			$last = trim($getDetailsRow['lname']);
			if($first != "" || $middle != "" || $last != ""){
				$name_str[$i]["name"] = $first[0].$middle[0].$last[0];
				$name_str[$i]["id"] = $getDetailsRow['id'];
				$i++;
			}
		}
		return $name_str;
	}
	
	function get_medication(){
		$this->db_obj->qry = "SELECT id, 
									title AS name,
									destination AS dosage,
									IF(sites=1,'OS',
										IF(sites=2,'OD',
											IF(sites=3,'OU',
											  IF(sites=4,'PO','')
											)
										)
									) AS site,
									sig,
									compliant,type,
									DATE_FORMAT(begdate,'%m-%d-%Y') as 'begdate', 
									DATE_FORMAT(enddate,'%m-%d-%Y') as 'enddate', 
									med_comments,
									allergy_status AS status,
									ccda_code AS code
								FROM lists 
								WHERE pid='".$this->patient."' 
									AND type IN (1,4)
									AND allergy_status != 'Deleted' 
								ORDER BY begdate DESC";
		$result = $this->db_obj->get_resultset_array();	
		return $result;
	}
	// new function for GFS // 
	function get_medication_app(){
		$this->db_obj->qry = "SELECT id, 
									title AS name,
									destination AS dosage,
									IF(sites=1,'OS',
										IF(sites=2,'OD',
											IF(sites=3,'OU',
											  IF(sites=4,'PO','')
											)
										)
									) AS site,
									sig,
									type,
									DATE_FORMAT(date,'%m-%d-%Y') as 'date',
									DATE_FORMAT(begdate,'%m-%d-%Y') as 'begdate', 
									DATE_FORMAT(enddate,'%m-%d-%Y') as 'enddate', 
									med_comments,comments,
									allergy_status AS status
								FROM lists 
								WHERE pid='".$this->patient."' 
									AND type IN (6,4)
									AND allergy_status = 'Active' 
								ORDER BY id DESC";
								
		$result = $this->db_obj->get_resultset_array();
		//return $result;
		
		
		foreach($result as $key => $value){
		
			
			if($value['begdate']=='00-00-0000'){
				$result[$key]['begdate']='';
			}
			if($value['enddate']=='00-00-0000'){
				$result[$key]['enddate']='';
			}
			
		}
		
		return $result;
	}
	// end of function for GFS //
	
	// new function get_medication_type_4()  // 
	
	function get_medication_type_4(){
		//$obj= new medication();
		$stat = $_REQUEST['status'];
		$status = trim($stat);
		$this->db_obj->qry = "SELECT id, 
									title AS name,
									destination AS dosage,
									IF(sites=1,'OS',
										IF(sites=2,'OD',
											IF(sites=3,'OU',
											  IF(sites=4,'PO','')
											)
										)
									) AS site,
									sig, DATE_FORMAT(last_take_time,'%m-%d-%Y %h:%i:%s') as 'last_take_time',
									compliant,	type, referredby,
									DATE_FORMAT(begdate,'%m-%d-%Y') as 'begdate', 
									DATE_FORMAT(enddate,'%m-%d-%Y') as 'enddate', 
									med_comments,
									allergy_status AS status,
									ccda_code AS code
								FROM lists 
								WHERE pid='".$this->patient."' 
									AND type =4
									";
		if($status != ""){
			$this->db_obj->qry.= "AND allergy_status = '".$status."'";
		}
		$this->db_obj->qry.="AND allergy_status != 'Deleted'  
								ORDER BY id DESC";
		$results = $this->db_obj->get_resultset_array();
		
		foreach($results as $key => $result){
			//echo "dance";print_r($result);
			if($result["referredby"]!="" || !empty($result["referredby"])){
				$results[$key]["referredby"] = $this->print_getData($result["referredby"]);
			}
			if($result['last_take_time']=='00-00-0000 12:00:00'){
				$results[$key]['last_take_time']='';
			}
			if($result['begdate']=='00-00-0000'){
				$results[$key]['begdate']='';
			}
			
			if($result['enddate']=='00-00-0000'){
				$results[$key]['enddate']='';
			}
			
		}
		
		return $results;
	}
	// end of function for GFS //
	
	// new function get_medication_type_1()  // 
	function get_medication_type_1(){
		
		$stat = $_REQUEST['status'];
		$status = trim($stat);
		
		$this->db_obj->qry = "SELECT id, 
									title AS name,
									destination AS dosage,
									sig, compliant,	type, referredby,
									DATE_FORMAT(begdate,'%m-%d-%Y') as 'begdate', 
									DATE_FORMAT(enddate,'%m-%d-%Y') as 'enddate', 
									med_comments,
									allergy_status AS status,
									ccda_code AS code
								FROM lists 
								WHERE pid='".$this->patient."' 
									AND type =1
									";
		if($status != ""){
			$this->db_obj->qry.= "AND allergy_status = '".$status."'";
		}
		$this->db_obj->qry.="AND allergy_status != 'Deleted'  
								ORDER BY id DESC";
		$results = $this->db_obj->get_resultset_array();
		$i=0;	
		foreach($results as $result){
			if($result["referredby"]!="" || !empty($result["referredby"])){
				$results[$i]["referredby"] = $this->print_getData($result["referredby"]);
			}
			if($result['begdate']=='00-00-0000'){
				$results[$i]['begdate']='';
			}
			if($result['enddate']=='00-00-0000'){
				$results[$i]['enddate']='';
			}
			$i++;
		}
		
		return $results;
	}
	// end of function for GFS //
	
	// new function get_medication_type 1 and 4() // 
	function save_medication_type_1_4(){
	
		$type = (isset($_REQUEST['type']) && $_REQUEST['type']!="")?imw_real_escape_string($_REQUEST['type']):"";
		$title_1 = (isset($_REQUEST['title']) && $_REQUEST['title']!="")?imw_real_escape_string($_REQUEST['title']):"";
		$dosage = (isset($_REQUEST['dosage']) && $_REQUEST['dosage']!="")?imw_real_escape_string($_REQUEST['dosage']):"";
		$sign = (isset($_REQUEST['sig']) && $_REQUEST['sig']!="")?imw_real_escape_string($_REQUEST['sig']):"";
		$compliant = (isset($_REQUEST['compliant']) && $_REQUEST['compliant']!="")?imw_real_escape_string($_REQUEST['compliant']):"";
		$site = (isset($_REQUEST['site']) && $_REQUEST['site']!="")?imw_real_escape_string($_REQUEST['site']):"";
		$begdate = (isset($_REQUEST['begdate']) && $_REQUEST['begdate']!="")?date('Y-m-d', strtotime($_REQUEST['begdate'])):"";
		$enddate = (isset($_REQUEST['enddate']) && $_REQUEST['enddate']!="")?date('Y-m-d', strtotime($_REQUEST['enddate'])):"";
		$comment = (isset($_REQUEST['comments']) && $_REQUEST['comments']!="")?imw_real_escape_string($_REQUEST['comments']):"";
		$code = (isset($_REQUEST['code']) && $_REQUEST['code']!="")?imw_real_escape_string($_REQUEST['code']):"";
		$status = (isset($_REQUEST['status']) && $_REQUEST['status']!="")?imw_real_escape_string($_REQUEST['status']):"Active";
		$last_take_time = (isset($_REQUEST['last_take_time']) && $_REQUEST['last_take_time']!="")?date('Y-m-d h:i:s', strtotime($_REQUEST['last_take_time'])):"0000-00-00 00:00:00";
		$referredby=(isset($_REQUEST['referredby']) && $_REQUEST['referredby']!="")?imw_real_escape_string($_REQUEST['referredby']):"";
		$title = trim($title_1);
		$sig = trim($sign);
		$comments = trim($comment);
		switch($site){
			case "OS":
			$site = "1";
			break;
			case "OD":
			$site = "2";
			break;
			case "OU":
			$site = "3";
			break;
			case "PO":
			$site = "4";
			break;
			default :
			$site = "0";
			break;
		}
		
		if($type != "" && $title != "" && $this->patient!="" && $this->authId !="" && $this->authId!=0){
			$this->db_obj->qry = "INSERT INTO lists 
									SET type = '".$type."',
									date = '".date('Y-m-d H:i:s')."',
									title = '".$title."',
									destination = '".$dosage."',
									referredby='".$referredby."',
									sig 	= '".$sig."',
									compliant 	= '".$compliant."',
									begdate = '".$begdate."',
									enddate = '".$enddate."',
									pid = '".$this->patient."' ,
									user = '".$this->authId."',
									med_comments = '".$comments."',
									allergy_status = '".$status."',
									ccda_code = '".$code."',
									ccda_code_system = '2.16.840.1.113883.6.88',
									ccda_code_system_name = 'RxNorm'
				
							";
			if($type == 4){
				$this->db_obj->qry.=",last_take_time='".$last_take_time."',
										sites 	= '".$site."'";
				}
			else{
					$this->db_obj->qry.=",last_take_time='0000-00-00 00:00:00',
											sites = '0'";
			}
			
			$result = $this->db_obj->run_query($this->db_obj->qry);
			$query = "UPDATE commonnomedicalhistory 
							  SET no_value = '',
							  	  date_time = '".date('Y-m-d H:i:s')."',
								  comments = '',
								  operator_id = '".$this->authId."',
								  timestamp = '".date('Y-m-d H:i:s')."',
								  parent_id = NULL,
								  src_server = NULL
								  WHERE patient_id = '".$this->patient."'
								  		AND module_name = 'Medication'";
								 $result = imw_query($query);
								 return $result;
		}
		else{
				return false;
		}
	}
	
	//end of function //
	
	// update 1 and 4 type medications//
	function update_medication_app(){
			
		$status = (isset($_REQUEST['status']) && $_REQUEST['status']!="")?imw_real_escape_string($_REQUEST['status']):"";	
		$id = (isset($_REQUEST['id']) && $_REQUEST['id']!="")?$_REQUEST['id']:"";
		$type = (isset($_REQUEST['type']) && $_REQUEST['type']!="")?imw_real_escape_string($_REQUEST['type']):"";
		$title_1 = (isset($_REQUEST['title']) && $_REQUEST['title']!="")?imw_real_escape_string($_REQUEST['title']):"";
		$dosage = (isset($_REQUEST['dosage']) && $_REQUEST['dosage']!="")?imw_real_escape_string($_REQUEST['dosage']):"";
		$sign = (isset($_REQUEST['sig']) && $_REQUEST['sig']!="")?imw_real_escape_string($_REQUEST['sig']):"";
		$compliant = (isset($_REQUEST['compliant']) && $_REQUEST['compliant']!="")?imw_real_escape_string($_REQUEST['compliant']):"";
		$site = (isset($_REQUEST['site']) && $_REQUEST['site']!="")?imw_real_escape_string($_REQUEST['site']):"0";
		$begdate = (isset($_REQUEST['begdate']) && $_REQUEST['begdate']!="")?date('Y-m-d', strtotime($_REQUEST['begdate'])):"";
		$enddate = (isset($_REQUEST['enddate']) && $_REQUEST['enddate']!="")?date('Y-m-d', strtotime($_REQUEST['enddate'])):"";
		$comment = (isset($_REQUEST['comments']) && $_REQUEST['comments']!="")?imw_real_escape_string($_REQUEST['comments']):"";
		$code = (isset($_REQUEST['code']) && $_REQUEST['code']!="")?imw_real_escape_string($_REQUEST['code']):"";
		$referredby=(isset($_REQUEST['referredby']) && $_REQUEST['referredby']!="")?imw_real_escape_string($_REQUEST['referredby']):"";
		$last_take_time = (isset($_REQUEST['last_take_time']) && $_REQUEST['last_take_time']!="")?date('Y-m-d h:i:s', strtotime($_REQUEST['last_take_time'])):"";
		$title = trim($title_1);
		$sig = trim($sign);
		$comments = trim($comment);
		switch($site){
			case "OS":
			$site = "1";
			break;
			case "OD":
			$site = "2";
			break;
			case "OU":
			$site = "3";
			break;
			case "PO":
			$site = "4";
			break;
			default :
			$site = "0";
			break;
		}
			//$referredby = $this->print_getData($referredby);
			if($id != ""  && $this->patient!="" && $this->authId !="" && $this->authId!=0){
				
				  $this->db_obj->qry = "Update lists 
										SET 
										date = '".date('Y-m-d H:i:s')."',
										pid = '".$this->patient."' ,
										user = '".$this->authId."'
									";
				if($type!="") $this->db_obj->qry.=",type = '".$type."'";
				if($title!="") $this->db_obj->qry.=",title = '".$title."'";
				if($dosage!="") $this->db_obj->qry.=",destination = '".$dosage."'";
				if($compliant!="")$this->db_obj->qry.=",compliant= '".$compliant."'";
				if($sig!="") $this->db_obj->qry.=",sig= '".$sig."'";
				if($status!="") $this->db_obj->qry.=",allergy_status= '".$status."'";
				if($begdate!="") $this->db_obj->qry.=",begdate= '".$begdate."'";
				if($enddate!="") $this->db_obj->qry.=",enddate= '".$enddate."'";
				if($comments!="") $this->db_obj->qry.=",med_comments= '".$comments."'";
				if($code!="") $this->db_obj->qry.=",ccda_code= '".$code."'";
				if($referredby!="") $this->db_obj->qry.=",referredby= '".$referredby."'";
				if($type == 4){
						$this->db_obj->qry.=",last_take_time= '".$last_take_time."',
				 					  		  sites= '".$site."'";			
				}
				
				else{
					    $this->db_obj->qry.=",last_take_time= '0000-00-00 00:00:00',
							   				  sites = '0'";
				}
				 		$this->db_obj->qry.=" where id ='".$id."' 
										  AND pid = '".$this->patient."'";
					$result = imw_query($this->db_obj->qry);
					$res = imw_query("select ROW_COUNT() as count");
					$fetch = imw_fetch_assoc($res);
					if($fetch['count'] == 1){
						return true; 
					}
					else{
							return false;
					}
			}
			else{
			
				return false;
			}
		}
		
		//get the comment of medication//
		function get_comment(){
			$pid = $this->patient;
			$query = imw_query("select * from commonnomedicalhistory where patient_id = '".$pid."' AND module_name='Medication'"); 
			$result = imw_fetch_assoc($query);
			$res[] =  $result;
			return $res;
		}
		// save the comment of medication//
		function save_comment(){
			$comment = (isset($_REQUEST['comment']) && $_REQUEST['comment']!="")?$_REQUEST['comment']:"";
			$qry = imw_query("select * from lists where pid = '".$this->patient."' AND allergy_status != 'Deleted' AND type IN (1,4)");
			$result_1 = imw_num_rows($qry);
			$query = imw_query("select * from commonnomedicalhistory where patient_id = '".$this->patient."' AND module_name = 'Medication'");
			$result = imw_num_rows($query);
			if($result_1 == 0){
				if($result == 0 ){
					$query = "INSERT INTO commonnomedicalhistory 
								SET patient_id = '".$this->patient."',
								module_name = 'Medication',
								no_value = 'NoMedications',
								date_time = '".date('Y-m-d H:i:s')."',
								operator_id = '".$this->authId."',
								comments = '".$comment."',
								timestamp = '".date('Y-m-d H:i:s')."',
								parent_id = NULL,
								src_server = NULL";
								$result = imw_query($query);
								return true;
						}
				else{
				 $query = "UPDATE commonnomedicalhistory 
							  SET no_value = 'NoMedications',
							  	  date_time = '".date('Y-m-d H:i:s')."',
								  comments = '".$comment."',
								  operator_id = '".$this->authId."',
								  timestamp = '".date('Y-m-d H:i:s')."',
								  parent_id = NULL,
								  src_server = NULL
								  WHERE patient_id = '".$this->patient."'
								  		AND module_name = 'Medication'";
								 $result = imw_query($query);
								return true;
					}
					}
		 else{
			return false;
		}
		
		}
	
	// new function get_medication_type_6() for GFS // 
	
	function get_medication_type_6(){
		
		$this->db_obj->qry = "SELECT id, 
									title AS name,
									IF(sites=1,'OS',
										IF(sites=2,'OD',
											IF(sites=3,'OU',
											  IF(sites=4,'PO','')
											)
										)
									) AS site,
									 proc_type, type, referredby,
									DATE_FORMAT(date,'%m-%d-%Y') as 'date',
									comments, ccda_code AS code
								FROM lists 
								WHERE pid='".$this->patient."' 
									AND type = 6 AND allergy_status != 'Deleted'  
										ORDER BY id DESC";
					$result = $this->db_obj->get_resultset_array();	
					return $result;
							}
	// end of function for GFS //
	
	// new function get_medication_type_5() for GFS // 
	
	function get_medication_type_5(){
		$this->db_obj->qry = "SELECT id, 
									title AS name,
									proc_type, type, referredby,
									DATE_FORMAT(date,'%m-%d-%Y') as 'date',
									comments, ccda_code AS code
									FROM lists 
									WHERE pid='".$this->patient."' 
									AND type = 5
									AND allergy_status != 'Deleted' 
									ORDER BY id DESC";
		$result = $this->db_obj->get_resultset_array();	
		return $result;
	}
	// end of function for GFS //
	
 	// new function for android and ios to insert data in "type 5" medication //
	function save_medication_type_5(){
		
		$title = (isset($_REQUEST['title']) && $_REQUEST['title']!="")?imw_real_escape_string($_REQUEST['title']):"";
		$comments = (isset($_REQUEST['comments']) && $_REQUEST['comments']!="")?imw_real_escape_string($_REQUEST['comments']):"";
		$code = (isset($_REQUEST['code']) && $_REQUEST['code']!="")?imw_real_escape_string($_REQUEST['code']):"";
		$proc_type = (isset($_REQUEST['proc_type']) && $_REQUEST['proc_type']!="")?imw_real_escape_string($_REQUEST['proc_type']):"";
		$referredby=(isset($_REQUEST['referredby']) && $_REQUEST['referredby']!="")?imw_real_escape_string($_REQUEST['referredby']):"";
		
		if($title != "" && $this->patient!="" && $this->authId !="" && $this->authId!=0){
			$this->db_obj->qry = "INSERT INTO lists 
									SET type = 5,
									date = '".date('Y-m-d H:i:s')."',
									begdate = '".date('Y-m-d H:i:s')."',
									title = '".$title."',
									pid = '".$this->patient."' ,
									user = '".$this->authId."',
									comments = '".$comments."',
									referredby='".$referredby."',
									proc_type='".$proc_type."',
									allergy_status = 'Active',
									ccda_code = '".$code."'
							";
			$result = $this->db_obj->run_query($this->db_obj->qry);
			return $result;
		}
	}
	//end of function //
	
	function save_medication(){
		$ocular = (isset($_REQUEST['ocular']) && $_REQUEST['ocular']!="")?imw_real_escape_string($_REQUEST['ocular']):"";
		$title = (isset($_REQUEST['title']) && $_REQUEST['title']!="")?imw_real_escape_string($_REQUEST['title']):"";
		$dosage = (isset($_REQUEST['dosage']) && $_REQUEST['dosage']!="")?imw_real_escape_string($_REQUEST['dosage']):"";
		$sig = (isset($_REQUEST['sig']) && $_REQUEST['sig']!="")?imw_real_escape_string($_REQUEST['sig']):"";
		$compliant = (isset($_REQUEST['compliant']) && $_REQUEST['compliant']!="")?imw_real_escape_string($_REQUEST['compliant']):"";
		$site = (isset($_REQUEST['site']) && $_REQUEST['site']!="")?imw_real_escape_string($_REQUEST['site']):"";
		$begdate = (isset($_REQUEST['begdate']) && $_REQUEST['begdate']!="")?date('Y-m-d', strtotime($_REQUEST['begdate'])):"";
		$enddate = (isset($_REQUEST['enddate']) && $_REQUEST['enddate']!="")?date('Y-m-d', strtotime($_REQUEST['enddate'])):"";
		$comments = (isset($_REQUEST['comments']) && $_REQUEST['comments']!="")?imw_real_escape_string($_REQUEST['comments']):"";
		$code = (isset($_REQUEST['code']) && $_REQUEST['code']!="")?imw_real_escape_string($_REQUEST['code']):"";
		switch($site){
			case "OS":
			$site = "1";
			break;
			case "OD":
			$site = "2";
			break;
			case "OU":
			$site = "3";
			break;
			case "PO":
			$site = "4";
			break;
			default :
			$site = "0";
			break;
		}
		if($ocular == 1)
		$type = 4;
		else if($ocular == 0)
		$type = 1;
		// below "else if" is added by Aqib to insert data into surgical med. // 
		else if($ocular == 2)
		$type = 6;
		if($title != "" && $this->patient!="" && $this->authId !="" && $this->authId!=0){
			$this->db_obj->qry = "INSERT INTO lists 
									SET type = '".$type."',
									date = '".date('Y-m-d H:i:s')."',
									title = '".$title."',
									destination = '".$dosage."',
									sites 	= '".$site."',
									sig 	= '".$sig."',
									compliant 	= '".$compliant."',
									begdate = '".$begdate."',
									enddate = '".$enddate."',
									pid = '".$this->patient."' ,
									user = '".$this->authId."',
									med_comments = '".$comments."',
									allergy_status = 'Active',
									ccda_code = '".$code."'
							";
			$result = $this->db_obj->run_query($this->db_obj->qry);
			return $result;
		}
	}
			
	
	function delete_medication(){
		$id = (isset($_REQUEST['id']) && $_REQUEST['id']!="")?$_REQUEST['id']:"";
		if($id != "" && $this->patient !="" && $this->authId !="" && $this->authId!=0){
			$this->db_obj->qry = "UPDATE lists
								 SET allergy_status = 'Deleted'
								 WHERE id = '".$id."'
								 AND pid = '".$this->patient."'
								 AND user = '".$this->authId."'
							";
			$result = imw_query($this->db_obj->qry);
			$res = imw_query("select ROW_COUNT() as count");
			$fetch = imw_fetch_assoc($res);
			
			if($fetch['count'] == 1){
						return true;
			}
			else{
						return false;
			}
		}
			else{
					return false;
			}
	}
	// new function for android and ios to insert data in "type 6" medication //
	function save_medication_type_4_6(){
		
		$id = $_REQUEST['id'];
		$title = (isset($_REQUEST['title']) && $_REQUEST['title']!="")?imw_real_escape_string($_REQUEST['title']):"";
		$site = (isset($_REQUEST['site']) && $_REQUEST['site']!="")?imw_real_escape_string($_REQUEST['site']):"";
		$sig = (isset($_REQUEST['sig']) && $_REQUEST['sig']!="")?imw_real_escape_string($_REQUEST['sig']):"";
		$comments = (isset($_REQUEST['comments']) && $_REQUEST['comments']!="")?imw_real_escape_string($_REQUEST['comments']):"";
		$beg_date = (isset($_REQUEST['beg_date']) && $_REQUEST['beg_date']!="")?date('Y-m-d', strtotime($_REQUEST['beg_date'])):"";
		$stop_date= (isset($_REQUEST['stop_date']) && $_REQUEST['stop_date']!="")?date('Y-m-d', strtotime($_REQUEST['stop_date'])):"";
		$type = $_REQUEST['type'];
		switch($site){
			case "OS":
			$site = "1";
			break;
			case "OD":
			$site = "2";
			break;
			case "OU":
			$site = "3";
			break;
			case "PO":
			$site = "4";
			break;
			default :
			$site = "0";
			break;
		}
		if($id == "" && $title != "" && $this->patient!="" && $this->authId !="" && $this->authId!=0){
			$this->db_obj->qry = "INSERT INTO lists 
								SET type = '".$type."',
									date = '".date('Y-m-d H:i:s')."',
									begdate = '".$beg_date."',
									enddate = '".end_date."',
									title = '".$title."',
									sites 	= '".$site."',
									sig = '".$sig."',
									pid = '".$this->patient."' ,
									user = '".$this->authId."',
									allergy_status = 'Active'";
			if($type == 4){
				$this->db_obj->qry .= ", med_comments = '".$comments."'";
			}
			else{
				$this->db_obj->qry .= ", comments  = '".$comments."'";
			}
			$result = imw_query($this->db_obj->qry);
			$prob_id = imw_insert_id();
			if($prob_id != ""){
				return true;
			}
			else{
				return false;
			}
		}		
		else{
				$this->db_obj->qry = "Update lists 
										SET type = '".$type."',
										date = '".date('Y-m-d H:i:s')."'";
				if($beg_date != ""){		
					$this->db_obj->qry .=",begdate = '".$beg_date."'";
				}	
				if($end_date != ""){		
					$this->db_obj->qry .=",enddate = '".$end_date."'";
				}						
				if($title != ""){		
					$this->db_obj->qry .=",title = '".$title."'";
				}		
				if($site != ""){		
					$this->db_obj->qry .=",sites = '".$site."'";
				}	
				if($sig != ""){		
					$this->db_obj->qry .=",sig = '".$sig."'";
				}				
				if($commets != ""){	
					if($type == 6){	
						$this->db_obj->qry .=",comments = '".$comments."'";
					}
					else{
						$this->db_obj->qry .=", med_comments = '".$comments."'";
					}
				}						
				$this->db_obj->qry .= "where 				
									pid = '".$this->patient."' AND
									user = '".$this->authId."'AND
									id = '".$id."'";
									//echo $this->db_obj->qry;
				$result = $this->db_obj->run_query($this->db_obj->qry);
				
				return true;					
		
		}
	}
	
	//end of function //
	// this function used for DUR button in medication//
	function erx_url(){
	require_once('/../../interface/common/functions.inc.php');
	$objManageData = new ManageData;
	
	//--- GET ERX URL ------
	$objManageData->QUERY_STRING = "select EmdeonUrl from copay_policies";
	$copay_policies_res = $objManageData->mysqlifetchdata();
	
	$EmdeonUrl = $copay_policies_res[0]['EmdeonUrl'];
	
	//--- GET PROVIDER LOGIN USERNAME AND PASSWORD ----
	 $userId = $this->authId;
	$objManageData->QUERY_STRING = "select eRx_user_name, erx_password , eRx_facility_id from users where id = '$userId'";
	$userRes = $objManageData->mysqlifetchdata();
	$eRx_user_name = $userRes[0]['eRx_user_name'];
	$erx_password = $userRes[0]['erx_password'];
	$eRx_facility_id = $userRes[0]['eRx_facility_id'];
	
	if(trim($EmdeonUrl) != '' and trim($eRx_user_name) != '' and trim($erx_password) != ''){
		//--- GET PATIENT DETAILS ---
		$patientId = $this->patient;
		$objManageData->QUERY_STRING = "select * from patient_data where id = '$patientId'";
		$qryRes = $objManageData->mysqlifetchdata();
		$id = $qryRes[0]['id'];
		$fname = $qryRes[0]['fname'];
		$lname = $qryRes[0]['lname'];
		list($year,$mon,$day) = preg_split('/-/',$qryRes[0]['DOB']);
		$patient_dob = $mon.'/'.$day.'/'.$year;
		$erx_url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&hdnBusiness=$eRx_facility_id&apiLogin=true&target=jsp/lab/person/PersonRxHistory.jsp&actionCommand=apiRxHistory&P_ACT=$id&P_LNM=$lname&P_FNM=$fname&P_DOB=$patient_dob";
		header("location: $erx_url");
	}
		
		}
		
}
?>
