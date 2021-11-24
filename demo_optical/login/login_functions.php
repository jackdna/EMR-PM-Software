<?php
function app_login_success(){
	die('<script type="text/javascript">
	var WindowDialog = new function() {
		this.openedWindows = {};
	
		this.open = function(instanceName,url,win,extra) {
			var handle=window.open(url,win,extra);
	
			this.openedWindows[instanceName] = handle;
	
			return handle;
		};
	
		this.close = function(instanceName) {
			if(this.openedWindows[instanceName])
				this.openedWindows[instanceName].close();
		};
	
		this.closeAll = function() {
			for(var dialog in this.openedWindows)
				this.openedWindows[dialog].close();
		};
	};
			//window.top.location.href="'.$GLOBALS['WEB_PATH'].'/";
			var options = "scrollbars=0,resizable=1,status=1,toolbar=0,menubar=0,location=0,width=1355,height="+window.screen.availHeight;
			var wn_height = parseInt(window.screen.availHeight) + 62;
			nav_name=navigator.appVersion;
				var height="";
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						if(xmlhttp.responseText!="")
						{
								if(nav_name.indexOf("iPad") != "-1"){
								self.moveTo(0,0);
								self.resizeTo(1355, screen.availHeight);
								self.name = "inventory_frame";
								self.location = "'.$GLOBALS['WEB_PATH'].'/index2.php";
							}else{
								top.WindowDialog.closeAll();
								var qq=top.WindowDialog.open(\'Add_new_popup\',"'.$GLOBALS['WEB_PATH'].'/index2.php","inventory_frame",options);
								qq.moveTo(0,0);
								if(self.name != "inventory_frame"){
									closeIT();
								}
							}
						}
					}
				};
				xmlhttp.open("GET", "ajax.php?wn_height=" + wn_height, true);
				xmlhttp.send();
				
			function closeIT(){
				!(window.ActiveXObject) && \'ActiveXObject\'
				function isIE11(){
					return !!navigator.userAgent.match(/Trident.*rv[ :]*11\./);
				}
				var ie7 = (document.all && !window.opera && window.XMLHttpRequest) ? true : false; 
				var ie11 = isIE11();
				if (ie7 || ie11){
					window.open(\'\',\'_parent\',\'\'); 
					window.close(); 
				}else{
					this.focus();
					self.opener = this;
					self.close();
				}
			}
		</script>');
}

function get_max_login_attempts($default_max){
	$return = 3;
	$res = imw_query("SELECT maxLoginAttempts FROM facility WHERE facility_type = '1'"); //HQ facility
	if($res && imw_num_rows($res) == 1){
		$arr = imw_fetch_assoc(res);
		$return = (!empty($arr["maxLoginAttempts"]) ? intval($arr["maxLoginAttempts"]) : $default_max);
	}
	return $return;
}

function check_wrong_attempts($u,$p,$hidd_multi_login = ""){
	$err = '';
	//checking captcha if entered
	if(isset($_POST["vericode"])){
		if($_POST["vericode"] != $_SESSION["vericode"]){
			$err = "Verification code error.";
		}
	}
	if($err == ""){
		//incrementing captch display counter
		if(isset($_SESSION["CAPTCHACOUNT"])){
			$_SESSION["CAPTCHACOUNT"] = $_SESSION["CAPTCHACOUNT"] + 1;
		}else{
			$_SESSION["CAPTCHACOUNT"] = 1;
		}
		$blAns = false;
		list($blAns, $u_id) = app_login_process($u, $p, false, "arr",$hidd_multi_login);
		if($blAns===true){//redirecting to main screen
			return true;//app_login_success();
		}elseif($blAns==='restricted'){
			return 'restricted';
		}else{
			//Authentication Failed
			if(!empty($u_id)){
				//gettin max allowed login attempts by a vadlid user
				if(!(isset($_SESSION["MAXATTEMPT"]) && !empty($_SESSION["MAXATTEMPT"]))){
					$_SESSION["MAXATTEMPT"]	= get_max_login_attempts(constant("DEFAULT_MAX_LOGIN_ATTEMPT"));
				}
				
				//incrementing wrong attempt counter
				if(isset($_SESSION["WRONGATTEMPT"])){
					$_SESSION["WRONGATTEMPT"] = ($_SESSION["WRONGATTEMPT"] + 1);
				}else{
					$_SESSION["WRONGATTEMPT"]	= 1;
				}
				//lock if a valid username has wrong attempts exceeding the permitted attempts in the same session
				if($_SESSION["WRONGATTEMPT"] >= $_SESSION["MAXATTEMPT"]){
					lock_user_acc($u_id);
				}
			}
			return false;
		}
	}

}

function lock_user_acc($u_id){
	$res = imw_query("UPDATE users SET locked = 1 WHERE id = '".$u_id."'");
}

function hashPassword($pass){
	if(!empty($pass)){
		if(HASH_METHOD && HASH_METHOD=='MD5'){
			return md5($pass);
		}else{
			return hash('sha256',$pass);
		}
	}
}

function db_check_login_process($u,$p){
	$sql = "SELECT * FROM users WHERE username = '".$u."' AND locked = '0' AND delete_status = '0' ORDER BY id DESC LIMIT 1";
	$res = imw_query($sql);
	if($res && imw_num_rows($res) == 1){
		$rs = imw_fetch_assoc($res);
		if( strtolower($rs['password']) == strtolower($p) ){
			return array(true,$rs);
		}else{
			return array(false,$rs);
		}
	}else{
		return false;
	}
}

function app_switch_user_process($p_w){		
	$res = imw_query("SELECT * FROM users WHERE password = '".$p_w."' AND locked = '0' AND delete_status = '0' ORDER BY id DESC LIMIT 1");
	if($res && imw_num_rows($res) ==1){
		return imw_fetch_assoc($res);			
	}else{
		return false;
	}
}

function app_switch_user_update_recent_pt($user, $patient_id){		
	$res = imw_query("SELECT patient_id, recent_user_id FROM recent_users WHERE provider_id = '$user' ORDER BY enter_date desc");
	$count = imw_num_rows($res);
	$patientRecent = false;
	$curDate = date('Y-m-d H:i:s');
	while($rs = imw_fetch_assoc($res)){
		$recent_user_id = $rs['recent_user_id'];
		if($patient_id == $rs['patient_id']){
			$patientRecent = true;
			$sql2 = "UPDATE recent_users SET patient_id = '$patient_id', enter_date='".$curDate."' WHERE recent_user_id = '$recent_user_id'";
			$res2 = imw_query($sql2);
		}
	}
	if($patientRecent == false){	
		if($count == 5){
			$sql3 = "UPDATE recent_users SET patient_id='$patient_id', provider_id='$user', enter_date='".$curDate."' WHERE recent_user_id='$recent_user_id'";
		}else{			
			$sql3 = "INSERT INTO recent_users SET patient_id='$patient_id', provider_id='$user', enter_date='".$curDate."'";
		}
		$res3 = imw_query($sql3);
	}
}

function app_logout($actionLogout = ""){
	ob_start();
	if($actionLogout != "app_switch_user"){
		foreach ($_SESSION as $var => $val) {
			$_SESSION[$var] = null;
		}
		//Unset session array
		$_SESSION = array();

		// Delete the session cookie.
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time() - 42000);
		}

		// Finally, destroy the session.
		$_SESSION['patient'] = '';$_SESSION['patient'] = NULL;
		unset($_SESSION['patient']);
		unset($_SESSION['patient']);
		session_destroy();
		session_destroy();
		//Logout--
		header("location:".$GLOBALS['WEB_PATH']."/login/");
		die("Session Expired.");
	}
}

function app_login_process($u_n, $p_w, $sw_u = false, $returnType = "bl",$hidd_multi_login = ""){
	if(!trim($hidd_multi_login)) {
		$p_w = hashPassword($p_w);
	}
	if($sw_u == true){
		$arr_usr = app_switch_user_process($p_w);
		if($arr_usr !== false){
			//transfer chart note locks
			if(isset($_SESSION["patient"]) && !empty($_SESSION["patient"])){
				//updating recent patients after switch user success.
				app_switch_user_update_recent_pt($arr_usr[0]["id"], $_SESSION["patient"]);
			}
			app_logout("app_switch_user");
		}
	}else{
		$status 	= db_check_login_process(takeUserInput($u_n), takeUserInput($p_w));
		$authStatus = $status[0];
		$arr_usr 	= $status[1];
		
	}
	if($authStatus && $arr_usr !== false){
		//delete any existing session
		//session_unset();
		$_SESSION['patient_session_id'] = '';$_SESSION['patient_session_id'] = NULL; unset($_SESSION['patient_session_id'],$_SESSION['patient_session_ins_alert']);
		//creating session if not exists
		if(!session_id()){
			session_name("idoc_inventory");
			session_start();
		}
		
		//setting session vars
		$_SESSION["pass_created_on"] 		= $arr_usr["passCreatedOn"];
		$_SESSION["admin_pass_reset"]		= $arr_usr["passwordReset"];
		$_SESSION["logged_user_type"] 		= $arr_usr["user_type"];
		$_SESSION["authUser"]				= $arr_usr["username"];
		$_SESSION["authPass"]				= $p_w;
		$_SESSION["authId"] 				= $arr_usr["id"];
		$_SESSION["pro_fac_id"]				= $_POST['faclity'];
		$_SESSION["authProviderName"] 		= $arr_usr["lname"].', '.$arr_usr["fname"].' '.substr($arr_usr["mname"],0,1);
		$_SESSION["WRONGATTEMPT"] 			= NULL;
		$_SESSION["MAXATTEMPT"] 			= NULL;
		$_SESSION["CAPTCHACOUNT"] 			= NULL;
		
        if(!empty($arr_usr["groups_prevlgs_id"]) && $arr_usr["groups_prevlgs_id"] > 0){
            $ar_prv_tmp = get_grp_prvlgs($arr_usr["groups_prevlgs_id"]);	
        }
        if(isset($ar_prv_tmp) && count($ar_prv_tmp)>0){
            $arr_privileges = $ar_prv_tmp;
        }else{
            //gettin name, privileges and group info to set in session
            $arr_privileges = unserialize( html_entity_decode($arr_usr["access_pri"]) );
        }
		$_SESSION["PERMISSION"]		=	$arr_privileges;
		
		if( !isset($_SESSION["PERMISSION"]['priv_Optical']) || $_SESSION["PERMISSION"]['priv_Optical']!=1 )
			return( array('restricted') );
		
		if($returnType == "bl"){
			return true;
		}
		elseif($returnType == "arr"){
			$arrReturn = array(true, $arr_usr["id"]);
			return $arrReturn;
		}
	}if(!$authStatus && $arr_usr !== false){
		$arrReturn = array(false, $arr_usr["id"]);
		return $arrReturn;
	}else{
		return false;
	}
}

function get_grp_prvlgs($id){
    $ret = array();
    $sql = imw_query("SELECT prevlgs from groups_prevlgs WHERE id='".$id."' AND deleted_by='0' ");
    $row = imw_fetch_assoc($sql);
    if(!empty($row["prevlgs"])){
        $ret = unserialize(html_entity_decode($row["prevlgs"]));
    }
    return $ret;
}

function get_expiry_days($default_expiry){
	$return = 90;
	$sql = "SELECT maxPassExpiresDays FROM facility WHERE facility_type = '1' order by maxPassExpiresDays desc LIMIT 0,1"; //HQ facility
	$res = imw_query($sql);
	if($res && imw_num_rows($res) == 1){
		$arr = imw_fetch_assoc($res);
		$return = (!empty($arr["maxPassExpiresDays"]) ? intval($arr["maxPassExpiresDays"]) : $default_expiry);
	}
	return $return;
}

function app_check_pass_expiry(){
	$MaxExpDays	= get_expiry_days(constant("DEFAULT_PASS_EXPIRY_DAYS"));
	$created 	= $_SESSION["pass_created_on"];
	$reset 		= $_SESSION["admin_pass_reset"];
//	echo 'maxDays='.$MaxExpDays.', Created='.$created.', Reset='.$reset.'<br>'; die();
	if($created != '0000-00-00'){
		$CreatedOn = explode("-", $created);
		$Cmonth = $CreatedOn[1];
		$Cday = $CreatedOn[2];
		$Cyear = $CreatedOn[0];
		
		$mkexpire_time = @mktime(0, 0, 0, $Cmonth, $Cday + $MaxExpDays, $Cyear);//New Expiry time created.
		$expirydate = @date("Y-m-d",$mkexpire_time);
		$expiry = explode("-", $expirydate);
		$em = $expiry[1];
		$ed = $expiry[2];
		$ey = $expiry[0];
		$near_date = @mktime(0, 0, 0, $emonth, $ed - constant("PASS_EXPIRY_NOTICE_DAYS"), $ey);//near date,  show alerts to user.

		$curr = date("Y-m-d");
		$diff_expiry = strtotime($curr) - $mkexpire_time;
		$expiry_day = $diff_expiry/86400;
		if($expiry_day > 0){		//password has expired
			$return = array(false);
		}else if(abs($expiry_day) <= constant("PASS_EXPIRY_NOTICE_DAYS")){ //show warning for these days.
			$return = array(true,"Your password will expire in ".abs($expiry_day)." days. You can set your new password.");
			return $return;
		}else{	
			app_login_success();
		}
	}else{
		app_login_success();
	}
}
?>