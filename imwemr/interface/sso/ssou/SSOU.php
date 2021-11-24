<?php
include(dirname(__FILE__)."/config.nic.php");
class SSOU {
	private $psinfo;
	public function __construct($ap){
		$this->psinfo["appnm"] = $ap;		
		$this->psinfo["sso_url_verify"] = $GLOBALS[$ap]["sso_url_verify"]; 
		$this->psinfo["dbinfo"] = $GLOBALS[$ap]["dbinfo"];
		$this->psinfo["sso_path_login"] = $GLOBALS[$ap]["sso_path_login"];
		$this->psinfo["login_post_vars"] = $GLOBALS[$ap]["login_post_vars"];		
		$this->psinfo["token_life_seconds"] = $GLOBALS[$ap]["token_life_seconds"];		
		$this->psinfo["db_name"] = $GLOBALS[$ap]["db_name"];
		
		//
		if(empty($this->psinfo["sso_url_verify"]) || empty($this->psinfo["sso_path_login"]) || count($this->psinfo["dbinfo"]) <=0 || count($this->psinfo["login_post_vars"])<=0){
			exit("SSOU: Settings are missing.");
		}
	}
	
	function mk_call($ar){
		
		$tkn = $ar["Token"];
		
		$requestType = 'POST';
		$header = array();
		
		if(!empty($tkn)){
			//Header
			$header = array(
				'Content-type: application/json',
				'Token: ' . $tkn
			);
			
			$content = "";
			$surl = $this->psinfo["sso_url_verify"];
		}
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_HEADER, 1);			// is 1 to provide response WITH header information > will be split below (for content only use 0)
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_URL, $surl);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $requestType);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

		$response = curl_exec($curl);					// this is the JSON data received from the API - the response from the CURL action
		$info = curl_getinfo($curl);					// get info from the last transfer
		
		//echo curl_error($curl) . '<br>';
		$actualResponse = (isset($info["header_size"]))?substr($response,$info["header_size"]):"";						// response content > use for response parameters
		$actualResponseHeaders = (isset($info["header_size"]))?substr($response,0,$info["header_size"]):"";					// response header > use for status code and reason phrase

		//var_dump($actualResponse);	
		
		curl_close($curl);
		
		return $actualResponse;
		
	}
	
	function get_usr_info($sso_id){
		define("IMW_SSO","".$this->psinfo["db_name"]);
		include("ssou_db.php");
		session_start();
		session_regenerate_id();
		$ssn_id = session_id();	
		$_SESSION["tm"] = date("Y-m-d H:i:s");	
		if(!empty($sso_id)){
			$sql = "SELECT * FROM ".$this->psinfo["dbinfo"]["tbl"]." WHERE ".$this->psinfo["dbinfo"]["sso_key"]."!='' AND ".$this->psinfo["dbinfo"]["sso_key"]."!='0' AND ".$this->psinfo["dbinfo"]["sso_key"]." IS NOT NULL  ";
			if(!empty($this->psinfo["dbinfo"]["active"])){ $sql .= "AND ".$this->psinfo["dbinfo"]["active"];  }
			$rez=sso_sqlStatement($sql);
			for($i=1; $row=sso_sqlFetchArray($rez); $i++){
				$usr_sso_id = trim($row[$this->psinfo["dbinfo"]["sso_key"]]);				
				if(!empty($usr_sso_id)){
					$usr_sso_id = hash("sha256", $usr_sso_id);
					
					if($usr_sso_id == $sso_id){
						if(!empty($this->psinfo["sso_path_login"])){
							
							if(count($this->psinfo["login_post_vars"]) > 0){
								foreach($this->psinfo["login_post_vars"] as $k => $ar_prms){
									$db_field = $ar_prms[1];
									$post_var = $ar_prms[0];
									$db_table_nm = (isset($ar_prms[2]) && !empty($ar_prms[2])) ? $ar_prms[2] : "";
									$wh_cl = (isset($ar_prms[3]) && !empty($ar_prms[3])) ? $ar_prms[3] : "";
									
									if(!empty($db_table_nm)){
										$sql = "SELECT ".$db_field." FROM ".$db_table_nm;
										if(!empty($wh_cl)){ $sql .= " WHERE ".$wh_cl;  }
										$row1=sso_sqlQuery($sql);
										if($row1!=false){
											$db_value = $row1[$db_field];
											if(!empty($post_var)){$_SESSION[$post_var] = $db_value;}
										}
									}else{
									
									$db_value = $row[$db_field];
									if(!empty($post_var)){$_SESSION[$post_var] = $db_value;}
									
									}
								}
							}
							
							break;
						}
					}
				}
			}
		}
		sso_close();
		//include($this->psinfo["sso_path_login"]);
		header("Location: ".$this->psinfo["sso_path_login"]."?anm=".urlencode($this->psinfo["appnm"])."&nss=".base64_encode($ssn_id));	
		exit();
	}
	
	function validate_token(){		
		$imw_tkn = urldecode($_GET["imw_tkn"]);
		$sso_id = urldecode($_GET["sso_id"]);
		$res = $this->mk_call(array("Token"=>$imw_tkn));
		if($res == "Pass" && !empty($sso_id)){			
			$ar_usr_info = $this->get_usr_info($sso_id);
		}else{
			echo "Token Verification failed.";
		}
		exit();
	}
	
	function validate_ssn($id){
		$id = base64_decode($id);
		session_id($id);
		session_start();
		$dtime = date_create($_SESSION['tm']);
		$cur_dtime = date("Y-m-d H:i:s");		
		$dtime2 = new DateTime($_SESSION['tm']);
		$dtime1 = new DateTime($cur_dtime);
		$diff = $dtime1->diff($dtime2); 				
		$total = ((($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i)*60 + $diff->s;
		
		// Number seconds allowed
		if($total<=$this->psinfo["token_life_seconds"]){
			foreach($_SESSION as $k => $v){
				$_POST[$k] = $v;
			}
		}
		session_destroy();
	}
	
	function main(){		
		if(isset($_GET["imw_tkn"]) && !empty($_GET["imw_tkn"])){			
			$this->validate_token();
			exit();
		}else if(isset($_GET["nss"]) && !empty($_GET["nss"])){
			$this->validate_ssn($_GET["nss"]);			
		}
	}
}

if(isset($_GET["anm"]) && !empty($_GET["anm"])){
$appnm = urldecode($_GET["anm"]);
$ossos = new SSOU($appnm);
$ossos->main();
}

?>