<?php
class IBRA{
	private $token,$apiKey,$apiSecretKey,$esign,$apiBase,$patient_id,$case_id;
	public function __construct($pid, $fid=0){
		$this->token=0;
		$this->apiKey='ioe5_780p3';		// this is the public key (of EMR company)
		$this->apiSecretKey = 'kuz32n87';		// this is the private key (of EMR company)
		$this->esign='kj9752rtzdsx';			// = esign (LINK ID of user)
		$this->apiBase='https://www.zubisoft.eu/api/ibra';
		$this->patient_id = $pid;
		$this->form_id = $fid;
		$this->case_id = 0;
		$this->initToken();
	}

	function getToken(){
		##### URL Link and HTTP Method (tp = third party)
		$apiBase = $this->apiBase."/token";
		$requestType = 'POST';
		
		##### Authentication 
		$apiKey = $this->apiKey;							
		$apiSecretKey = $this->apiSecretKey;								
		
		##### Request Parameters
		$data = array();
		$data['esign']=$this->esign;				
		$data['action']="create";							// possible actions: create, revoke
		
		##### Content
		$content = json_encode($data);
		
		##### Signature (hash)
		$apiHash = base64_encode(hash_hmac('SHA256',$requestType . "\n" . $content, $apiSecretKey));
		
		//var_dump($apiHash);
		//Header
		$header = array(
			'Content-type: application/json',
			'X-Public-Key: ' . $apiKey,
			'X-Signed-Request-Hash: ' . $apiHash
		);
		
		
		$ob = $this->sendRequest($apiBase, $requestType, $content, $header );
		if(is_array($ob)){ 
			$this->token = $ob["token"];
			
			//
			if(!empty($this->token)){
				$sql = "INSERT INTO ibra_token SET status='0', create_time='".date('Y-m-d H:i:s')."', token='".$this->token."'   ";
				$row = sqlQuery($sql);
				
			}
		}else{
			exit($ob);
		}
	}
	
	function sendRequest($apiBase, $requestType, $content, $header ){
	
	
		//$fp = fopen('data.txt', 'w');
		//fwrite($fp, ''.$content);
		//fclose($fp);
		
		$json_input = $content;
		$req_header = json_encode($header);
		
		
		##### This part is used to request the SQL data
		// cURL = client URL = a library that lets you make HTTP requests in PHP
		// cURL is used to handover information (content, key, hash,...) to the API site (adiBase) and to retrieve the data from the API: this is a JSON file ($response) that is decided into an array ($obj)
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_HEADER, 1);			// is 1 to provide response WITH header information > will be split below (for content only use 0)
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_URL, $apiBase );
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $requestType);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

		$response = curl_exec($curl);					// this is the JSON data received from the API - the response from the CURL action
		$info = curl_getinfo($curl);					// get info from the last transfer
		
		//echo curl_error($curl) . '<br>';
		$actualResponse = (isset($info["header_size"]))?substr($response,$info["header_size"]):"";									// response content > use for response parameters
		$actualResponseHeaders = (isset($info["header_size"]))?substr($response,0,$info["header_size"]):"";					// response header > use for status code and reason phrase

		//var_dump($actualResponse);
		curl_close($curl);
		
		//log in db
		$sql = "INSERT INTO ibra_call_log SET json_input = '".sqlEscStr($apiBase.", ".$requestType.", ".$json_input)."', req_header='".sqlEscStr($req_header)."', response='".sqlEscStr($response)."', dt_time='".date('Y-m-d H:i:s')."' ";
		$row = sqlInsert($sql);
		
		##### Output the data
		// the array ($obj) is read out line by line (row by row) if it exists (if SQL data was retrieved)
		$obj = json_decode($actualResponse,true);				// JSON data will be converted into an 2 dimensional array (1st dim: each is a case, like a number - 2nd dim: the parameters with names, e.g. 'id', 'cid',...)

		if (isset($obj))
		{
			//echo "Token = " . $obj['token'];
			return $obj;
		}
		else 
		{ 
			$echo="";
			$echo.= "<br>Status = " . $info['http_code'];		// comes from CURL via curl_getinfo			
			preg_match('#^HTTP/1.(?:0|1) [\d]{3} (.*)$#m', $actualResponseHeaders, $match);
			$reason = trim($match[1]);
			$echo.= "<br>Reason Phrase = " . $reason;
			
			return $echo;
		}	
	}
	
	function initToken(){
		$new_token = 0; $token=0; $id = 0;
		$cur_time = date('Y-m-d H:i:s');
		$sql = "SELECT id, token, create_time FROM ibra_token WHERE status = '0' ";
		$row = sqlQuery($sql);
		if($row!=false){
			$token_time = $row["create_time"];
			$token = $row["token"];
			$id = $row["id"];
		}
		
		if(empty($token)){
			$new_token = 1; 
		}else	if(!empty($token_time) && !empty($cur_time)){
			$time1 = new DateTime($cur_time);
			$time2 = new DateTime($token_time);
			$interval = $time1->diff($time2);
			$hrs = $interval->format('%H');
			if($hrs >= 12){
				$new_token = 1; 
			}
		}
		
		if(!empty($new_token)){
			$this->token = 0;
			if(!empty($id)){
				$sql = "UPDATE ibra_token SET status='1' where id = '".$id."'  ";
				$row = sqlQuery($sql);
			}
			
			$this->getToken();
		}else{
			$this->token = $token;
		}	
	}
	
	function is_ibra_patient(){
		$flg=0;$ar_pt_inf=array();
		/*
		$sql = "SELECT id FROM ibra_case WHERE patient_id='".$this->patient_id."' and del_by='0' "; 
		$row = sqlQuery($sql);
		if($row!=false){
			$flg=1;
			$ar_pt_inf["patient_id"]=$this->patient_id;	
		}else{
		*/
			$sql = "SELECT id, lname, fname, sex, DOB, street, street2,postal_code,zip_ext,city,phone_home, phone_cell,email 
					FROM patient_data WHERE id='".$this->patient_id."' and patientStatus='Active' ";
			$row = sqlQuery($sql);
			if($row!=false){
			
				$ar_pt_inf["patient_id"]=$row["id"];				
				$ar_pt_inf["surname"]=$row["lname"];
				$ar_pt_inf["firstname"]=$row["fname"];
				
				if(strtolower($row["sex"])=="male"){
				$ar_pt_inf["sex"]= "m";
				}else if(strtolower($row["sex"])=="female"){
				$ar_pt_inf["sex"]= "f";	
				}
				
				$tmp = explode("-", $row["DOB"]);
				$ar_pt_inf["date_birth_day"]=$tmp[2];
				$ar_pt_inf["date_birth_month"]=$tmp[1];
				$ar_pt_inf["date_birth_year"]=$tmp[0];
				
				$ar_pt_inf["street"]=$row["street"];
				if(!empty($row["street2"])){ if(!empty($ar_pt_inf["street"])){ $ar_pt_inf["street"] .= " "; } $ar_pt_inf["street"].=$row["street2"]; }
				
				$ar_pt_inf["postcode"]=$row["postal_code"];
				if(!empty($row["zip_ext"])){ if(!empty($ar_pt_inf["postcode"])){ $ar_pt_inf["postcode"] .= " "; } $ar_pt_inf["postcode"].=$row["zip_ext"];}
				
				$ar_pt_inf["city"]=$row["city"];
				
				$ar_pt_inf["phone"]=$row["phone_cell"];
				if(empty($row["phone"])){$ar_pt_inf["phone"]=$row["phone_home"];}
				$ar_pt_inf["email"]=$row["email"];
				
				
			}
		//}
		return array($flg, $ar_pt_inf);	
	}
	
	function create_case_id($data){

		$eye = $data["eye"];
		$data["patient_id"] = "".$this->patient_id;
		$sql = "SELECT * FROM ibra_case WHERE patient_id='".$this->patient_id."' AND form_id='".$this->form_id."' AND site='".sqlEscStr($eye)."' ";
		$row = sqlQuery($sql);
		if($row!=false){
			$case_id = $row["id"];
			$data["case_id"] = "".$case_id;
			$this->case_id = $case_id;
			$data["case_update"] = "1";
		}else{
			$sql = "INSERT INTO ibra_case SET patient_id = '".$this->patient_id."',
					cr_time='".wv_dt("now")."',
					provider='".$_SESSION["authId"]."',
					form_id='".$this->form_id."',
					site='".sqlEscStr($eye)."'
					";
			$case_id = sqlInsert($sql);
			if(!empty($case_id)){	
				$case_id = $case_id;
				$data["case_id"] = "".$case_id;
				$this->case_id = $case_id;
			}
		}
		
		return $data;		
		
	}
	
	function createCase($ar_data){
		
		$data = array(); //$_REQUEST;
		$data = array_merge($data, $ar_data);
		
		//
		list($ibra_patient, $data_pt_info) = $this->is_ibra_patient();
		$data = array_merge($data, $data_pt_info);
		
		##### URL Link and HTTP Method (tp = third party)
		$apiBase = $this->apiBase."/cases";
		$requestType = 'POST';
		
		##### Content
		//$data = array();
		
		//Header
		$header = array(
			'Content-type: application/json',
			'X-Public-Key: ' . $this->apiKey,
			'Token: ' . $this->token
		);
		
		//		
		$data = $this->create_case_id($data);
		if(empty($this->case_id)){echo "Case is not created."; return;}
		if(isset($data["case_update"])){ 
			$requestType = 'PUT'; unset($data["case_update"]);
			if(isset($data["method"]) && empty($data["method"])){ 
				$requestType = 'DELETE'; 
				
				$case_id = $data["case_id"]; 
				$case_id = $data["case_id"];
				$patient_id = $data["patient_id"];
				$eye = $data["eye"];
				$data = array();
				$data["case_id"] = $case_id;
				$data["eye"] = $eye;
				$data["patient_id"] = $patient_id;
				
			}
		}
		$content = json_encode($data);
		
		//
		$flg=0;
		$ob = $this->sendRequest($apiBase, $requestType, $content, $header );
		if(is_array($ob)){ 
			$ob_js = json_encode($ob);	
		}else{
			$ob_js = $ob;
			$flg=1;	
		}
		
		
		return $flg;
	}
	
	function send_lasik(){
		if(empty($this->token) || empty($this->patient_id) || empty($this->form_id)){return;}
		
		$sql = "SELECT * FROM chart_vis_lasik WHERE form_id='".$this->form_id."' AND patient_id='".$this->patient_id."' ";		
		$row = sqlQuery($sql);
		if($row!=false){
			extract($row);
			
			$ar_target = json_decode($target, true);
			$ar_laser = json_decode($laser, true);
			
			$ar_date_lasik = explode("-", $date_lasik);
			
			$date_operation_day = $ar_date_lasik[2];
			$date_operation_month = $ar_date_lasik[1];
			$date_operation_year = $ar_date_lasik[0];
			
			$time_lasik_mil = (strpos($time_lasik, "PM")!==false) ? 12 : 0;
			$time_lasik = str_replace(array("PM", "AM"), "", $time_lasik);
			$ar_time_lasik = explode(":", $time_lasik);
			$date_operation_hour = $ar_time_lasik[0] + $time_lasik_mil; $date_operation_hour = "".$date_operation_hour;
			$date_operation_minute = $ar_time_lasik[1];
			
			$target_sphere_od=$ar_target["Od"]["S"];
			$target_cylinder_od=$ar_target["Od"]["C"];
			$target_axis_od=$ar_target["Od"]["A"];			
			$target_sphere_os=$ar_target["Os"]["S"];
			$target_cylinder_os=$ar_target["Os"]["C"];
			$target_axis_os=$ar_target["Os"]["A"];			
			$operation_details=$ar_target["Desc"];
			
			$laser_treatment_sphere_od=$ar_laser["Od"]["S"];
			$laser_treatment_cylinder_od=$ar_laser["Od"]["C"];
			$laser_treatment_axis_od=$ar_laser["Od"]["A"];
			$laser_treatment_sphere_os=$ar_laser["Os"]["S"];
			$laser_treatment_cylinder_os=$ar_laser["Os"]["C"];
			$laser_treatment_axis_os=$ar_laser["Os"]["A"];			
			if(!empty($ar_laser["Desc"])){
				if(!empty($operation_details)){ $operation_details.=", "; }
				$operation_details.=$ar_laser["Desc"];
			}
			
			if(!empty($intervention)){ $intervention = strtolower($intervention); }
			
			//
			$treatment_location = "";
			
			$arr_send["OD"] = $arr_send["OS"] = array();
			//Od
			$tmp = array();
			$tmp["eye"] = "od (R)";
			$tmp["treatment_location"] = $treatment_location;
			$tmp["date_operation_day"] = $date_operation_day;
			$tmp["date_operation_month"] = $date_operation_month;
			$tmp["date_operation_year"] = $date_operation_year;
			$tmp["date_operation_hour"] = $date_operation_hour;
			$tmp["date_operation_minute"] = $date_operation_minute;
			
			if(!empty($method)){$tmp["method"] = $method;}
			$tmp["intervention"] = $intervention;
			if(!empty($target_sphere_od) && $target_sphere_od >= -0 && $target_sphere_od <= 26){$tmp["target_sphere"] = $target_sphere_od;}
			if(!empty($target_cylinder_od) && $target_cylinder_od >= -0 && $target_cylinder_od <= 13){$tmp["target_cylinder"] = $target_cylinder_od;}
			if(!empty($target_axis_od) && $target_axis_od >= 0 && $target_axis_od <= 180){$tmp["target_axis"] = $target_axis_od;}
			$tmp["microkeratome"] = $microkeratome;
			$tmp["laser_excimer"] = $laser_excimer;
			$tmp["laser_mode"] = $laser_mode;
			
			if(!empty($laser_optical_zone) && $laser_optical_zone >= 5 && $laser_optical_zone <= 12){$tmp["laser_optical_zone"] = $laser_optical_zone;}
			if(!empty($laser_treatment_sphere_od) && $laser_treatment_sphere_od >= -3 && $laser_treatment_sphere_od <= 5){$tmp["laser_treatment_sphere"] = $laser_treatment_sphere_od;}
			if(!empty($laser_treatment_cylinder_od) && $laser_treatment_cylinder_od >= -1 && $laser_treatment_cylinder_od <= 25){$tmp["laser_treatment_cylinder"] = $laser_treatment_cylinder_od;}
			if(!empty($laser_treatment_axis_od) && $laser_treatment_axis_od >= 0 && $laser_treatment_axis_od <= 180){$tmp["laser_treatment_axis"] = $laser_treatment_axis_od;}
			
			$tmp["operation_details"] = $operation_details;			
			
			$arr_send["OD"] = $tmp;
			
			
			//Os
			$tmp = array();
			$tmp["eye"] = "os (L)";
			$tmp["treatment_location"] = $treatment_location;
			$tmp["date_operation_day"] = $date_operation_day;
			$tmp["date_operation_month"] = $date_operation_month;
			$tmp["date_operation_year"] = $date_operation_year;
			$tmp["date_operation_hour"] = $date_operation_hour;
			$tmp["date_operation_minute"] = $date_operation_minute;
			
			if(!empty($method)){$tmp["method"] = $method;}
			$tmp["intervention"] = $intervention;
			if(!empty($target_sphere_os) && $target_sphere_os >= -0 && $target_sphere_os <= 26){$tmp["target_sphere"] = $target_sphere_os;}
			if(!empty($target_cylinder_os) && $target_cylinder_os >= -0 && $target_cylinder_os <= 13){$tmp["target_cylinder"] = $target_cylinder_os;}
			if(!empty($target_axis_os) && $target_axis_os >= 0 && $target_axis_os <= 180){$tmp["target_axis"] = $target_axis_os;}
			$tmp["microkeratome"] = $microkeratome;
			$tmp["laser_excimer"] = $laser_excimer;
			$tmp["laser_mode"] = $laser_mode;
			
			if(!empty($laser_optical_zone) && $laser_optical_zone >= 5 && $laser_optical_zone <= 12){$tmp["laser_optical_zone"] = $laser_optical_zone;}
			if(!empty($laser_treatment_sphere_os) && $laser_treatment_sphere_os >= -3 && $laser_treatment_sphere_os <= 5){$tmp["laser_treatment_sphere"] = $laser_treatment_sphere_os;}
			if(!empty($laser_treatment_cylinder_os) && $laser_treatment_cylinder_os >= -1 && $laser_treatment_cylinder_os <= 25){$tmp["laser_treatment_cylinder"] = $laser_treatment_cylinder_os;}
			if(!empty($laser_treatment_axis_os) && $laser_treatment_axis_os >= 0 && $laser_treatment_axis_os <= 180){$tmp["laser_treatment_axis"] = $laser_treatment_axis_os;}
			
			$tmp["operation_details"] = $operation_details;	
			
			$arr_send["OS"] = $tmp;
			
			$this->createCase($arr_send["OD"]);
			$this->createCase($arr_send["OS"]);
		}
	}
	
	function load_ibra(){
		##
		header('Content-Type: text/html; charset=utf-8');
		##
		$z_ob_get_clean=$GLOBALS['fileroot']."/interface/chart_notes/view/ibra_cal.php";
		include($GLOBALS['fileroot']."/interface/chart_notes/minfy_inc.php");
		exit();
	}

	function main(){
		if(empty($this->token) || empty($this->patient_id)){return;}
		
		switch($_REQUEST["elem_ibra_action"]){
			case "new_case":
				$res = $this->createCase();
			break;
			case "launch_ibra":
				$this->load_ibra();
			break;
			case "send_lasik":
				$this->send_lasik();
			break;
		}
		
		if(!empty($res)){
			$res = "Something went wrong. Data in IBRA is not saved. Please try later!";
		}else{
			$res = -1;
		}
		
		echo $res;
	}
}