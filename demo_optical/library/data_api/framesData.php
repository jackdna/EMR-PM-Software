<?php
/*
 * Under MIT License
 * Use, Modify, Distribute under MIT License.
 * MIT License 2019
 *
 * File: framesData.php
 * Coded in PHP7
 * Purpose: PHP class to use frames data API
 * Access Type: Include
*/

class framesData{
	
	private $kaId;
	private $pId;
	private $szipcode;
	private $bzipcode;
	private $locations;
	protected $path;
	protected $tocken;
	
	public function __construct(){
		$this->locations = 1;
		$this->path = $GLOBALS['DIR_PATH']."/interface/patient_interface/uploaddir";
	}
	
	public function setConfig($configs=array()){
		
		$confName = array(1=>"pId",2=>"kaId",3=>"szipcode",4=>"bzipcode"); /*Error Codes for Members*/
		$errorCodes = array();
		
		foreach($confName as $key=>$conf){
			if(!(isset($configs[$conf]))){
				$errorCodes[] = $key;
			}
			else{
				/*Set Value of members Dynamically*/
				$this->{$conf} = $configs[$conf];
			}
			unset($configs[$conf]);
		}
		
		/*set other optional properties if supplied*/
		foreach($configs as $key=>$val){
			/*Set Value of members Dynamically*/
			$this->{$key} = $val;
		}
		
		/*Unset temperory variable that are not going to be used in rest of the program*/
		unset($key,$conf,$val,$configs,$confName);
		
		if(count($errorCodes)>0){
			$errorCodes = implode(",", $errorCodes);
			throw new Exception("Error Codes: ".$errorCodes);
		}
		
		/*To refresh Authorization token if expired*/
		$this->tocken = $this->getAuthorizationTicket();
	}
	
	/*
	 * Function to be used to get any data from framesData API
	 * 
	 * $api = Type of data to be queried
	 * $id = ID from query
	*/
	public function get($api, $id=false, $otherOptions=array()){
		if($api==""){
			throw new Exception("Missing Required Parameter API type");
		}
		
		$url = false;
		$noJson = false;	/*Curl Return data not json flag*/
		
		switch($api){
			case "ageGroups":
				$url = "agegroups";
				if($id){
					$url .="/".$id;
				}
				$url .= "?auth=".$this->tocken->auth;
			break;
			case "colors":
				$url = "colors";
				if($id){
					$url .="/".$id;
				}
				$url .= "?auth=".$this->tocken->auth;
			break;
			case "genders":
				$url = "gendergroups";
				if($id){
					$url .="/".$id;
				}
				$url .= "?auth=".$this->tocken->auth;
			break;
			case "materials":
				$url = "materials";
				if($id){
					$url .="/".$id;
				}
				$url .= "?auth=".$this->tocken->auth;
			break;
			case "productGroups":
				$url = "productgroups";
				if($id){
					$url .="/".$id;
				}
				$url .= "?auth=".$this->tocken->auth;
			break;
			case "manufacturers":
				$url = "manufacturers";
				if($id){
					$url .="/".$id;
				}
				$url .= "?auth=".$this->tocken->auth."&mkt=".$this->tocken->market;
			break;
			case "manufacturersStats":
				$url = "mfrstats";
				$flag = false;
				$days = (count($otherOptions)>0)?(isset($otherOptions['days'])?$otherOptions['days']:false):false;
				if($id && $days){
					$url .="/".$id;
					$url .= "?auth=".$this->tocken->auth."&days=".$days."&mkt=".$this->tocken->market;
					$flag = true;
				}
				elseif(!$id && !$days){
					$url .= "?auth=".$this->tocken->auth."&mkt=".$this->tocken->market;
					$flag = true;
				}
				elseif(!$id && $days){
					$url .= "?auth=".$this->tocken->auth."&days=".$days."&mkt=".$this->tocken->market;
					$flag = true;
				}
				
				if(!$flag){
					$url = "";
				}
			break;
			case "brands":
				$url = "brands";
				if($id){
					$url .="/".$id;
				}
				$url .= "?auth=".$this->tocken->auth."&mkt=".$this->tocken->market;
			break;
			case "manufacturerBrands":
				if($id){
					$url = "manufacturers/".$id."/brands";
					$url .= "?auth=".$this->tocken->auth."&mkt=".$this->tocken->market;
				}
			break;
			case "manufacturersColection":
				if($id){
					$url = "manufacturers/".$id."/collections";
					$url .= "?auth=".$this->tocken->auth."&mkt=".$this->tocken->market;
				}
			break;
			case "brandsColection":
				if($id){
					$url = "brands/".$id."/collections";
					$url .= "?auth=".$this->tocken->auth."&mkt=".$this->tocken->market;
				}
			break;
			case "styles":
				if($id){
					$url = "collections/".$id."/styles";
					$url .= "?auth=".$this->tocken->auth."&mkt=".$this->tocken->market;
				}
			break;
			case "stylesDiscontinued":
				$url = "styles/discontinued"."?auth=".$this->tocken->auth."&mkt=".$this->tocken->market;
				$validOptions = array('datestart','dateend','mid','bid','cid');
				$flag = false;
				foreach(array_keys($otherOptions)as $key){
					if(!in_array($key,$validOptions)){
						$flag = false;
						throw new Exception("Invalid option suppplied for Stylediscontinued Query");
					}
					else{
						$url .= "&".$key."=".$otherOptions[$key];
						$flag = true;
					}
				}
				
				if(!$flag){
					$url = "";
				}	
			break;
			case "styleConfigration":
				if((isset($otherOptions['startDate']) && $otherOptions['startDate'] != "") || (isset($otherOptions['endDate']) && $otherOptions['endDate'] != "")){
					$url = "styleconfigurations/updated";
					$url .= "?auth=".$this->tocken->auth."&mkt=".$this->tocken->market;
					if(isset($otherOptions['startDate']) && $otherOptions['startDate'] != ""){
						$url .= "&datestart=".$otherOptions['startDate'];
					}
					if(isset($otherOptions['endDate']) && $otherOptions['endDate'] != ""){
						$url .= "&dateend=".$otherOptions['endDate'];
					}
				}
				elseif($id){
					$url = "collections/".$id."/StyleConfigurations";
					$url .= "?auth=".$this->tocken->auth."&mkt=".$this->tocken->market;
				}
				$noJson = true;
			break;
			case "imgeData":
				if( $id && isset($otherOptions['image_size']) && $otherOptions['image_size']!='' ){
					$url = "images";
					$url .= "?auth=".$this->tocken->auth."&size=".$otherOptions['image_size']."&fpc=".$id;
				}
				$noJson = true;
			break;
		}
		
		if(!$url){
			throw new Exception("Invalid Query");
		}
		
		$data = $this->curl_request($url, $noJson);
		if($data['resp']!=200){
			throw new Exception("API call Failed with Message: \"".(isset($data['data']->Message)?$data['data']->Message:$data['data'])."\"<br />Error: \"".$data['error']."\"<br />Please try after some time.");	
		}
		$data = $data['data'];
		return $data;
	}
	
	/*Get Authorization Ticket
	 *
	 *Returns array
	 *@keys token = Authorization token
	 *@keys market = List of markets for the subscriber
	*/
	private function getAuthorizationTicket(){
		
		if(file_exists($this->path."/FramesDataAuth.txt")){
			$data = trim(file_get_contents($this->path."/FramesDataAuth.txt"));
			if($data!=""){
				$data = json_decode($data);
				$ptime = $data->lastCheck;
				$time = strtotime("-20hours", time());
				if($time<$ptime){
					$resp = array('auth'=>$data->auth->AuthorizationTicket, 'market'=>$data->auth->Markets,
								  'renew'=>$data->auth->RenewalMessage, 'terms'=>(bool)$data->auth->TermsAndAgreementValue);
					return (object)$resp;
				}
			}
		}
		$params = array('partnerid'=>$this->pId, 'username'=>$this->kaId, 'szipcode'=>$this->szipcode, 'bzipcode'=>$this->bzipcode, 'locations'=>$this->locations);
		$params = http_build_query($params);
		$url = "authenticatepms?".$params;
		$data = $this->curl_request($url);
		
		if($data['resp']==200){
			$data['data']->lastCheck = time();
			file_put_contents($this->path."/FramesDataAuth.txt", json_encode($data['data']));
			
			$resp = array('auth'=>$data['data']->auth->AuthorizationTicket, 'market'=>$data['data']->auth->Markets,
						  'renew'=>$data['data']->auth->RenewalMessage, 'terms'=>(bool)$data['data']->auth->TermsAndAgreementValue);
			return (object)$resp;
		}
		else{
			if(!isset($data['data']->Message))
				throw new Exception('Unknown Exception');
			else
				throw new Exception($data['data']->Message);
		}
	}
	
	/*Agree to Terms & conditions*/
	public function agreeTerms(){
		
		$params = array('partnerid'=>$this->pId, 'username'=>$this->kaId);
		$params = http_build_query($params);
		$url = "signreadmeagreement?".$params;
		$data = $this->curl_request($url);
	}
	
	/*Clear Content tocker File*/
	public function clearTocken(){
		file_put_contents($this->path."/FramesDataAuth.txt", '');
	}
	
	/*Get Tocken Renewal Message*/
	public function renewalMessage(){
		if($this->tocken == ''){
			throw new Exception('Error: Tocken not created.');
			return false;
		}
		return($this->tocken->renew);
	}
	
	/*Get Tocken Renewal Message*/
	public function termsAgreement(){
		if($this->tocken == ''){
			throw new Exception('Error: Tocken not created.');
			return false;
		}
		return($this->tocken->terms);
	}
	
	/*Prevent Setting Undefined Member Variables*/
	public function __set($name, $value) {
        throw new Exception('Cannot set Undeclared Member: '.$name);
    }
	
	/*Prevent Calling Undefined Member Variables*/
	public function __get($key){
		throw new Exception("You can't get value for Undeclared member \"".$key."\" from this class");
	}
	
	/*These are aliases*/
	public function escape($data){
		return imw_real_escape_string($data);
	}
	public function htEscape($data){
		return htmlentities(imw_real_escape_string($data));
	}
	
	/* Find Match In Array
	 * @find = Sting to be found
	 * @list = array of esisting elements
	*/
	public function noMatch($find, $list=array()){
		
		if(!is_array($list))
			return false;
		
		if( ! in_array(stripslashes($find),  $list)
			&&
			! in_array( html_entity_decode( stripslashes($find) ),  $list)
		)
			return true;
		else
			return false;
		
	}
	public function match($find, $list=array()){
		
		if(!is_array($list))
			return false;
		
		if( in_array(stripslashes($find),  $list)
			||
			in_array( html_entity_decode( stripslashes($find) ),  $list)
		)
			return true;
		else
			return false;
	}
	
	/*Function to make HTTP request to FramesData API
	 *Scope limited wtihin the class
	*/
	private function curl_request($url, $noJson=false){
		
		$header = array();
		$header[] = "Accept:text/json"; /*Data type of the response*/
		
		$url = "http://api.framesdata.com/api/".$url;
		
		$data = array();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPGET, true);	/*Reset HTTP method to GET*/
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); /*Return the response*/
		curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP); /*Set protocol to HTTP if default changed*/
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); /*Prevent checking SSL certificate of peer. Though we are sending request to http. So this option is of no use.*/
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);	/*fail verbosely if the HTTP error*/
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_HEADER, false); /*Include header in Output/Response*/
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); /*Default timeout for the request to prevent endles wait for response*/
		$data['data'] = curl_exec($ch); /*$data will hold data returned from FramesData API*/
		$data['resp'] = curl_getinfo($ch, CURLINFO_HTTP_CODE); /*Get response code from FramesData*/
		if (curl_error($ch)) {
			$data['error'] = curl_error($ch);
		}
		curl_close($ch);
		
		
		$data['data'] = ( $noJson ) ? $data['data'] : json_decode($data['data']);
		
		return($data);
	}
	
	/*Destroy Members when object's scope ends in*/
	public function __destruct(){
		
	}
}
?>