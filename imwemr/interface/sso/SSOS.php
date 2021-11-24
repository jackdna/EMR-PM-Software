<?php

include("config.nic.php");

class SSOS {
	private $token_life_seconds;
	public function __construct(){
		$this->token_life_seconds = $GLOBALS["token_life_seconds"];
	}
	
	function gen_token(){
		return sha1(uniqid(rand(), TRUE));
	}
	
	function save_token($tk, $url, $uid, $stts='1'){
		$sql = "SELECT * FROM `sso_log` WHERE uid='".$uid."' and url='".sqlEscStr($url)."' AND token='".sqlEscStr($tk)."' and status='0'  ";
		$row=sqlQuery($sql);
		if($row==false){
			$sql = "INSERT INTO sso_log(token, uid, dtime, url, status) VALUES('".$tk."', '".$uid."', NOW(), '".sqlEscStr($url)."', '0') ";	
			$row=sqlQuery($sql);
		}else{
			//Update wth new inform :: not sure
			$ssoid = $row["id"];
			$sql = "Update sso_log SET token='".$tk."', dtime = NOW(), status='".$stts."' WHERE id = '".$ssoid."' ";	
			$row=sqlQuery($sql);
		}
	}
	
	function login($url, $sso_id, $uid, $appnm){
		$sso_id=trim($sso_id);
		if(!empty($sso_id) && !empty($url) && !empty($uid) && !empty($GLOBALS["enable_sso"]) && strpos($GLOBALS["enable_sso"], $appnm)!==false){			
			$token = $this->gen_token();
			
			if(!empty($token)){
				$this->save_token($token, $url, $uid);
				if(strpos($url, "?")===false){ $url .= "?"; }				
				$url .= "&imw_tkn=".urlencode($token)."&sso_id=".urlencode(hash("sha256", $sso_id))."&anm=".urlencode($appnm);
				header("location:".$url);
				exit();
			}
		}
	}	
	
	function check_token_db(){
		$headers = getallheaders();
		//$res = array();
		$res = "Fail";
		if(!empty($headers["Token"])){
			$tkn = $headers["Token"];
			$sql = "SELECT id, dtime, NOW() as curtime FROM sso_log WHERE token = '".$tkn."' and status='0' ";
			$row=sqlQuery($sql);
			if($row!=false){
				$id = $row["id"];
				$dtime = $row["dtime"];				
				$cur_dtime = $row["curtime"];
				$diff = date_diff($cur_dtime, $dtime);				
				$total = ((($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i)*60 + $diff->s;				
				
				// Number seconds allowed
				if($total<=$this->token_life_seconds){
					$res = "Pass";
				}
				
				//
				$sql = "UPDATE sso_log SET status='1' WHERE id = '".$id."'  ";
				$row=sqlQuery($sql);
				
			}
		}
		echo $res;
		exit();
	}	
}

?>