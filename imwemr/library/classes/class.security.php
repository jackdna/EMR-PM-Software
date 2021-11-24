<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

/*
Coded in PHP 7
Purpose: Providing security related functions like login/switch user/logout/change pwe, user lock/unlock etc.
Access Type: include file.
*/
//required constants
define("DEFAULT_CAPTCHA_COUNT", 3);
define("DEFAULT_MAX_LOGIN_ATTEMPT", 3);
define("DEFAULT_SESSION_TIMEOUT", 3600);
define("DEFAULT_PASS_EXPIRY_DAYS", 90);
if(!isset($embedded_call) || (isset($embedded_call) &&  $embedded_call != true)){
	//config
	$ignoreAuth = true;
	require_once(dirname(__FILE__)."/../../config/globals.php");
}else{
	require_once(dirname(__FILE__)."/db.php");
}
require_once(dirname(__FILE__).'/class.language.php');
require_once(dirname(__FILE__).'/common_function.php');
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
require_once(dirname(__FILE__).'/work_view/ChartPtLock.php');
require_once(dirname(__FILE__).'/work_view/WorkView.php');
class security
{
	public $default_product, $product_version, $sess_height, $hash_method;
	public $logged_user, $logged_user_type, $logged_user_name, $res_fellow_sess, $login_facility;
	public $patient_id, $language, $class_var, $stop_login_redirect;
	private $isFirstLogger;
	
	
	###################################################################
	#	constructor function to set commonally used variable on page
	###################################################################
	function __construct($action = '')
	{
		if( !$action ) { 
			// code to redirect to login page if session expires OR cookie removed in any case 
			if( !$_SESSION['authId'] ){
				header('Access-Control-Expose-Headers: REQUIRES_AUTH');
				header('REQUIRES_AUTH:1');
				$_SESSION['REQUIRES_AUTH'] = true;
				header('Location:'.$GLOBALS['webroot'].'/interface/login/index.php');
			}
		}
		$this->session			= $_SESSION;
		$this->logged_user		= (isset($this->session["authId"]) 				&& $this->session["authId"] != "") 				? $this->session["authId"] 				: 0;
		$this->logged_user_type	= (isset($this->session["logged_user_type"]) 	&& $this->session["logged_user_type"] != "") 	? $this->session["logged_user_type"]	: 0;
		$this->logged_user_name	= (isset($this->session["authProviderName"]) 	&& $this->session["authProviderName"] != "") 	? $this->session["authProviderName"]	: '';
		$this->login_facility 	= (isset($this->session["login_facility"]) 		&& $this->session["login_facility"] != "") 		? $this->session["login_facility"] 		: 0;
		$this->default_product	= constant('DEFAULT_PRODUCT');
		if(defined('PRODUCT_VERSION'))
		$this->product_version	= constant('PRODUCT_VERSION');
		
		$this->hash_method		= constant('HASH_METHOD');
		$this->sess_height		= $this->session['wn_height'];
		$this->language 		= new core_lang();
		$this->class_var		= array();
        if(isset($_POST["us_name"])) {$_POST["us_name"] = xss_rem($_POST["us_name"]);}
        if(isset($_POST["u_n"])) {$_POST["u_n"] = xss_rem($_POST["u_n"]);}
        if(isset($_POST["p_w"])) {$_POST["p_w"] = xss_rem($_POST["p_w"]);}
        if(isset($_POST["l_facility"])) {$_POST["l_facility"] = xss_rem($_POST["l_facility"]);}
        if(isset($_POST["cc_t"])) {$_POST["cc_t"] = xss_rem($_POST["cc_t"]);}
		if(isset($_POST["u_n"]) && !empty($_POST["u_n"]) && isset($_POST["p_w"]) && !empty($_POST["p_w"])){
            if($this->check_office_hours()==false ) {
                header('Access-Control-Expose-Headers: OFFICE_HOURS');
                header('OFFICE_HOURS:1');
                $_SESSION['OFFICE_HOURS'] = true;
                header("location:".$GLOBALS['webroot']."/interface/login/index.php");
            }
			$this->login_page_driver();
		}
		$this->stop_login_redirect = '';
		$this->isFirstLogger=0;
	}
	
	function process_change_pw($post){
		$old_p = $this->match_old_pw($post['user_id'],$post['xp']);
		$return = array();
		if($old_p){//Old password matched.
			$HQfacRes = $this->getHQfacility();
			$maxRecenlyUsed = $HQfacRes[0]['maxRecentlyUsedPass'];
			
			$recent_matched = $this->match_in_recent_pw($post['user_id'],$post['pnew'],$maxRecenlyUsed);
			if($recent_matched){//IF MATCHED IN RECENT PASSWORDS, THEN SHOW ERROR, ELSE PROCEED
				$return['error'] 	= true;
				$return['errormsg'] = 'Password matched with recently used passwords!';
				$return['response'] = '';
			}else{//Check if any other user using the same password.
				$matched_with_other_user = $this->match_with_other_user($post['pnew']);
				if($matched_with_other_user){
					$return['error'] 	= true;
					$return['errormsg'] = 'Password not available. Please choose another password.';
					$return['response'] = '';
				}else{
					$saved = $this->save_new_password($post,$maxRecenlyUsed);
					if($saved){
						$return['error'] 	= false;
						$return['errormsg'] = '';
						$return['response'] = 'Password changed successfully!';
					}else{
						$return['error'] 	= false;
						$return['errormsg'] = '';
						$return['response'] = 'Failed! Please try again';
					}
				}
			}
		}else{//Old password not matched.
			$return['error'] 	= true;
			$return['errormsg'] = 'Current Password Mismatch!';
			$return['response'] = '';
		}
		return $return;
	}
	
	function match_old_pw($uid,$op){
		$res = imw_query("SELECT id FROM users WHERE id='$uid' AND password='$op' LIMIT 1");
		if($res && imw_num_rows($res)==1) return true;
		else return false;
	}
	
	function match_in_recent_pw($uid,$NEWp,$maxRecenlyUsed){
		$qry = "SELECT id FROM lastusedpassword WHERE password1 = '".$NEWp."' AND user_id = '".$uid."' ORDER BY id DESC LIMIT 0, ".$maxRecenlyUsed;
		$res = imw_query($qry);
		if($res && imw_num_rows($res)>0) return true;
		else return false;
	}
	
	function match_with_other_user($NEWp){
		$res = imw_query("SELECT id FROM users WHERE password='".$NEWp."' LIMIT 1");
		if($res && imw_num_rows($res)==1) return true;
		else return false;
	}
	
	function save_new_password($post,$maxRecenlyUsed){//saving new password; all validtions checked before.
		//INSERT RECORD IN RECENTLY USED PASSWORDS
		$res1 = imw_query("INSERT INTO lastusedpassword SET password1 = '".$post['pnew']."', user_id  = '".$post['user_id']."'");
		//UPDATE USER'S PASSWORD IN MAIN TABLE.
		$res2 = imw_query("UPDATE users SET password='".$post['pnew']."', locked = 0, passwordChanged=1, passCreatedOn='".date('Y-m-d')."', passwordReset=0 WHERE id='".$post['user_id']."'");
		if($res2){
			//removing excessive records for pass log according to as set in HQ facility
			$res3 = imw_query("SELECT id,password1 FROM lastusedpassword WHERE user_id  = '".$pro_id."' ORDER BY id DESC LIMIT 0, ".$maxRecenlyUsed);
			$preserve_id = array();
			while($rs = imw_fetch_assoc($res3)){$preserve_id[] = $rs['id'];}
			if(count($preserve_id)>0){
				$preserve_id = implode(',',$preserve_id);
				$res = imw_query("DELETE FROM lastusedpassword WHERE id NOT IN ($preserve_id) AND user_id = '".$pro_id."'");
			}
			return true;
		}
		return false;
	}
	
	function getHQfacility(){
		$res = imw_query("SELECT * FROM facility WHERE facility_type='1'");
		$return = false;
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$return[] = $rs;
			}
		}
		return $return;
	}
	
	function getFacilities($default_facility_id='')
	{
		$return = false;
		$q="SELECT id,name,facility_type FROM facility ORDER BY name ASC";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_array($res)){
                if($default_facility_id!=''){
                    if($default_facility_id==$rs['id']) $rs['selected'] = ' selected';
                } else {
                    if(isset($_SESSION["login_facility"]) && $_SESSION["login_facility"]!='') {
                        if($_SESSION["login_facility"]==$rs['id']) $rs['selected'] = ' selected';
                    } else if(!defined(constant("SHOW_NULL_FACILITY_LOGIN")) || constant("SHOW_NULL_FACILITY_LOGIN")==1){
                        if($rs['facility_type']=='1') $rs['selected'] = ' selected';
                    }
                }
				$return[] = $rs;
			}
		}
		return $return;
	}
	
	function getFacilityOptions($default_facility_id=''){
		$facRs = $this->getFacilities($default_facility_id);
		$options = '';
		if($facRs){
			foreach($facRs as $i=>$rs){
				$options .= '<option value="'.$rs['id'].'"'.$rs['selected'].'>'.$rs['name'].'</option>';
			}
		}
		return $options;
	}
	
	function getHQFacLogo(){
		$HQ	= $this->getHQfacility();
		$logo = $HQ[0]['logo'];
		
		$logo_img_file	= $GLOBALS['webroot'].'/library/images/s_logo.gif';
		$disc_file		= $GLOBALS['fileroot']."/data/".PRACTICE_PATH."/facilitylogo/".$logo;
		$web_file		= $GLOBALS['webroot']."/data/".PRACTICE_PATH."/facilitylogo/".$logo;
		if(file_exists($disc_file) && is_file($disc_file)) $logo_img_file = $web_file;
		return $logo_img_file;		
	}
	
	function login_page_driver(){
		$_SESSION['patient'] = '';
		$_SESSION['patient'] = NULL;
		unset($_SESSION['patient']);
		$err = "";

		//checking captcha if entered
		if(isset($_POST["vericode"])){
			if($_POST["vericode"] != '1' || $_POST['vericode'] == ''){
				$err = "Verification code error.";
			}
		}
		
		if($err == ""){
			//incrementing captch display counter
			if(isset($_SESSION["CAPTCHACOUNT"])){
				$_SESSION["CAPTCHACOUNT"] =  intval($_SESSION["CAPTCHACOUNT"]) + 1;
			}else{
				$_SESSION["CAPTCHACOUNT"] =  1;
			}
			
			//check if user exists i.e. username is valid
			$blAns = false;
			$ArrResLoginProcess = $this->app_login_process($_POST["u_n"], $_POST["p_w"], false, "arr");
			$blAns	= $ArrResLoginProcess[0];
			$u_id	= $ArrResLoginProcess[1];
			if($blAns !== false){
				if($this->isFirstLogger()){
					$this->setFirstLogger();
					$this->isFirstLogger=1;
				}
				//redirecting to main screen
				$this->app_login_success();
			}else{
				$err = "Authentication Failed.";
				header("HTTP/1.0 401 Unauthorized");
				if(!empty($u_id)){
                    //getting audit policy status in session
                    if(!isset($_SESSION['AUDIT_POLICIES'])){
                        $_SESSION['AUDIT_POLICIES'] = $this->get_all_audit_policies();
                    }
        
					//gettin max allowed login attempts by a vadlid user
					if(!(isset($_SESSION["MAXATTEMPT"]) && !empty($_SESSION["MAXATTEMPT"]))){
						$default_max_login_attemps	= constant("DEFAULT_MAX_LOGIN_ATTEMPT");
						$max_login_attemps_rs = $this->getHQfacility();
						$_SESSION["MAXATTEMPT"] = (intval($max_login_attemps_rs[0]['maxLoginAttempts']) > 0) ? intval($max_login_attemps_rs[0]['maxLoginAttempts']) : $default_max_login_attemps;
					}
					
					//incrementing wrong attempt counter
					if(isset($_SESSION["WRONGATTEMPT"])){
						$_SESSION["WRONGATTEMPT"] = intval($_SESSION["WRONGATTEMPT"]) + 1;
					}else{
						$_SESSION["WRONGATTEMPT"] = 1;
					}
					//audit failed login attempt
                    if($_SESSION["AUDIT_POLICIES"]["Login_Authentication_Failure"] == 1){
                        $this->app_audit_login_failure($u_id);
                    }					
					//lock if a valid username has wrong attempts exceeding the permitted attempts in the same session
					if($_SESSION["WRONGATTEMPT"] >= $_SESSION["MAXATTEMPT"]){
						$sql = "UPDATE users SET locked = 1 WHERE id = '".$u_id[0][0]."'";
						$res = imw_query($sql);
						
						//audit a/c locked action							
							if($_SESSION["AUDIT_POLICIES"]["A_c_Locked"] == 1){
								$this->app_audit_acc_locked($u_id);
							}
						
					}
				}
			}	
			
			/*Allscripts Sign in Error Message*/
			if( is_allscripts('enabled') && isset($_SESSION['as_error_msg']) && $_SESSION['as_error_msg'] != '' )
			{
				$err = ($err!='') ? $err.'<br />'.$_SESSION['as_error_msg'] : $_SESSION['as_error_msg'];
				unset($_SESSION['as_error_msg']);
			}
			/*End Allscripts Sign in Error Message*/
		}
		
		if($err != ""){
			$this->class_var['show_alert'] = $err;
		}
		

		//check audit start/stop app policy
		$this->class_var['DO_AUDIT'] = $_SESSION["AUDIT_POLICIES"]["Start___Stop_Application"];
		$_SESSION['DO_AUDIT_START_STOP'] = $_SESSION["AUDIT_POLICIES"]["Start___Stop_Application"];
		
		if($_SESSION["AUDIT_POLICIES"]["Start___Stop_Application"] == "1"){
			$this->audit_app_start();	//auditing app startup
			if(!isset($_SESSION['login_page_audited'])){
				 $_SESSION['login_page_audited'] = 'yes';
			}
		}
	}
	
	
	function isFirstLogger(){
		$curDt = date("Y-m-d");
		$sql = "SELECT COUNT(*) AS num FROM user_firstlogger WHERE dt_cur='".$curDt."' ";
		$res = sqlQuery($sql);
		if($res != false && $res["num"]>=1){
			return false;
		}
		return true;
	}
	
	function setFirstLogger(){
		$uid = !empty($this->logged_user) ? $this->logged_user : $_SESSION["authId"];
		$curDt = date("Y-m-d");
		$sql = "INSERT INTO user_firstlogger(uid, dt_cur) 
				VALUES ('".$uid."','".$curDt."' ) ";
		$res = imw_query($sql);
		$this->other_firstlogger_actions();
	}
	
	
	
	/*
	Function : insurance_daily_update
	Purpose : To expire and activate insurance for the day
	Returns : INTEGER - 1 on success
	*/
	function other_firstlogger_actions(){		
		$today_ts = date("Y-m-d");
		/*****INSURANCE UDPATE/ EXPIRE AND ACTIVATE*****/
		$yesterday_ts = $this->core_date_format(date('Y-m-d', strtotime($today_ts)-(24*60*60)),"Y-m-d");
		$this->expire_insurance($yesterday_ts);
		$this->activate_insurance($today_ts);
		
		/******CLEAN PATIENT LOCATION TABLE******/
		$old_date = $this->core_date_format(date('Y-m-d', strtotime($today_ts)-(15*24*60*60)),"Y-m-d"); //15 days old date
		$this->clean_patient_location_table($old_date);
		
		/******REMOVE 15 DAYS OLD IMEDICMONITOR CACHE FILES******/
		$this->clean_imonitor_cache_files();
		
		/******MARK APPOINTMETN AS NO SHOW******/
		if(!defined('MARK_AS_NO_SHOW') || constant('MARK_AS_NO_SHOW')==true){
		$this->mark_as_no_show();
		}
	}
	
	/*
	Function : expire_insurance
	Purpose : to expire_insurance with expiry date yesterday
	Returns : NULL
	*/
	function expire_insurance($exp_dt){
		$q = "update insurance_data set actInsComp = '0' where expiration_date <= '".$exp_dt."' and expiration_date !='0000-00-00 00:00:00' and actInsComp = 1";
		imw_query($q);
	}

	/*
	Function : activate_insurance
	Purpose : to activate_insurance with effective date today
	Returns : NULL
	*/
	function activate_insurance($eff_dt){
		$ArrEffDt = explode("-",$eff_dt);
		$YY	= $ArrEffDt[0];
		$MM	= $ArrEffDt[1];
		$DD	= $ArrEffDt[2];
		if(checkdate($MM,$DD,$YY)==false){return false;}
		$q = "update insurance_data set actInsComp = '1' where date_format(effective_date,'%Y-%m-%d') = '".$eff_dt."'";
		imw_query($q);
	}
	
	function core_date_format($dt, $format = "m/d/Y"){
		$ArrDt = explode("-",$dt);
		$y	= $ArrDt[0];
		$m	= $ArrDt[1];
		$d	= $ArrDt[2];
		
		if(is_numeric($y) === true){
			return date($format, mktime(0, 0, 0, $m, $d, $y));
		}
	}
	
	/*
	Function : clean_patient_location_table
	Purpose : to delete records from table, older that particular number of days.
	Returns : NULL
	*/
	function clean_patient_location_table($from_date){
		$q = "DELETE FROM patient_location WHERE cur_date < '".$from_date."'";
		imw_query($q);
	}
	
	/*
	Function : clean_imonitor_cache_files
	Purpose : to delete 10 days old imedicmonitor cache files.
	Returns : NULL
	*/
	function clean_imonitor_cache_files(){
		$path = $GLOBALS['fileroot']."/iMedicMonitor/cache";
		if(is_dir("$path")){
           $handle=opendir($path);
           while (false!==($file = readdir($handle))) {
               if ($file != "." && $file != "..") {
				   $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
				   if($extension=='xml'){
	                   $Diff = (time() - filemtime("$path/$file"))/60/60/24;
    	               if ($Diff > 15) unlink("$path/$file");
				   }

               }
           }
           closedir($handle);
        }
	}
	
	/*
	Function : mark appt as no show
	Purpose : mark appointments as no show if patient wasn't appear
	Returns : NULL
	*/
	function mark_as_no_show()
	{
		$start_date = $end_date = date('Y-m-d', strtotime('-1 days'));
		//get last logged date
		$qDate=imw_query("select dt_cur from user_firstlogger order by dt_cur DESC LIMIT 1,1");
		if(imw_num_rows($qDate)>0)
		{
			$rDate=imw_fetch_assoc($qDate);
			$date1=date_create($rDate['dt_cur']);
			$date2=date_create(date('Y-m-d'));
			$diff=date_diff($date1,$date2);
			$day_range=$diff->format("%a");
			$start_date = date('Y-m-d', mktime(0,0,0,date('m'),date('d')-$day_range,date('Y')));
		}
		
		$fac_where=$doc_where=$pro_where='';
		if(defined('MARK_AS_NO_SHOW_FAC') && constant('MARK_AS_NO_SHOW_FAC')!=0){
			$fac_where=" AND sa.sa_facility_id NOT IN(".constant('MARK_AS_NO_SHOW_FAC').")";
		}
		if(defined('MARK_AS_NO_SHOW_DOC') && constant('MARK_AS_NO_SHOW_DOC')!=0){
			$doc_where=" AND sa.sa_doctor_id NOT IN(".constant('MARK_AS_NO_SHOW_DOC').")";
		}
		if(defined('MARK_AS_NO_SHOW_PRO') && constant('MARK_AS_NO_SHOW_PRO')!=0){
			$pro_where=" AND sa.procedureid NOT IN(".constant('MARK_AS_NO_SHOW_PRO').")";
		}
		$q_appt=imw_query("SELECT sa.sa_patient_id, sa.id, sa.sa_app_start_date, sa.sa_app_starttime, sa.sa_doctor_id, sa.sa_facility_id, sa.sa_madeby,
				sa.sa_patient_app_status_id, sa.sa_app_endtime, sa.procedureid
				FROM schedule_appointments sa
				LEFT JOIN chart_master_table cmt ON ( cmt.patient_id = sa.sa_patient_id AND cmt.date_of_service = sa.sa_app_start_date ) 
				LEFT JOIN superbill sb ON ( sb.patientId = sa.sa_patient_id AND sb.dateOfService = sa.sa_app_start_date ) 
				LEFT JOIN patient_charge_list pcl ON ( pcl.patient_id = sa.sa_patient_id AND pcl.date_of_service = sa.sa_app_start_date ) 
				WHERE (sa.sa_app_start_date BETWEEN '$start_date' AND '$end_date')
				AND cmt.date_of_service IS NULL 
				AND sb.dateOfService IS NULL 
				AND pcl.date_of_service IS NULL 
				AND sa.sa_patient_app_status_id NOT 
				IN ( 203, 201, 18, 19, 20, 13, 11, 3) 
				AND IF( sa.sa_patient_app_status_id =271, sa.sa_patient_app_show =0, sa.sa_patient_app_show <>2 )
				$fac_where
				$doc_where
				$pro_where");
		
		while($d_appt=imw_fetch_object($q_appt))
		{
			$intAppId=$d_appt->id;
			$qryInsertAppPre = "INSERT INTO previous_status SET sch_id='$intAppId', 
			patient_id='$d_appt->sa_patient_id', 
			status_time='".date("H:i:s")."', 
			status_date='".date("Y-m-d")."', 
			status=3, 
			old_status='$d_appt->sa_patient_app_status_id', 
			old_date='$d_appt->sa_app_start_date', 
			old_time='$d_appt->sa_app_starttime', 
			old_appt_end_time='$d_appt->sa_app_endtime', 
			old_provider='$d_appt->sa_doctor_id', 
			old_facility='$d_appt->sa_facility_id', 
			oldMadeBy='$d_appt->sa_madeby',  
			old_procedure_id='$d_appt->procedureid', 
			statusChangedBy=1,
			dateTime='".date("Y-m-d H:i:s")."', 
			new_facility='$d_appt->sa_facility_id',  
			new_provider='$d_appt->sa_doctor_id',  
			new_appt_date='$d_appt->sa_app_start_date', 
			new_appt_start_time='$d_appt->sa_app_starttime', 
			new_appt_end_time='$d_appt->sa_app_endtime', 
			new_procedure_id='$d_appt->procedureid'";
			imw_query($qryInsertAppPre);	
			
			imw_query("update schedule_appointments set sa_patient_app_status_id = '3', sa_patient_app_show=0 where id = '".$intAppId."'");
			
			imw_query("delete from schedule_first_avail where sch_id = $intAppId");
		}
	}

	/*
	Purpose : To execute the login process using entered credentials
	Returns : BOOLEAN
	*/
	function app_login_process($u_n, $p_w, $sw_u = false, $returnType = "bl"){
		
		/**
		 * Validate Username and password for containing valid values. 
		 * This check is imposed to check that username and password are valid string values only
		 */
		if($sw_u === true) {
			if(
				gettype($p_w) !== 'string' ||
				trim(core_refine_user_input($p_w)) == ''
			) {
				return ($returnType == "arr")? array(false, false) : false;
			} 
		} else {
			if(
				gettype($u_n) !== 'string' ||
				gettype($p_w) !== 'string' ||
				trim(core_refine_user_input($u_n)) == '' ||
				trim(core_refine_user_input($p_w)) == ''
			)
			{
				return ($returnType == "arr") ? array(false, false) : false;
			}
		}

		if($_POST['redMedMod']!='redirectMed'){
			$p_w = hashPassword($p_w);
		}

		if($sw_u == true){
            $cc_t=(isset($_POST['cc_t']) && $_POST['cc_t']!='')?$_POST['cc_t']:'';

			/*Unset TW/AS session Keys*/
			$twSessionkeys = array('as_user_id', 'as_user_entry_code', 'as_error_msg', 'asMedicalHistorySync', 'as_token', 'as_token_time', 'as_user_pass');
			foreach($twSessionkeys as $twSessKey)
			{
				if( array_key_exists($twSessKey, $_SESSION) )
					unset($_SESSION[$twSessKey]);
			}

			$sql = "SELECT * FROM users WHERE password = '".$p_w."' AND locked = '0' AND delete_status = '0' ORDER BY id DESC LIMIT 1";
			$res = imw_query($sql);
			if(imw_num_rows($res) > 0){
				$arr_usr = imw_fetch_assoc($res);
                if($this->check_office_hours($arr_usr,$cc_t)==false ) {
                    $_SESSION["switch_user_tab"] = $_POST["switch_user_tab"];
                    die("You are not allowed to login in Off Hours.");
                }
                $askForReason==false;
                if(isset($_SESSION["patient"]) && !empty($_SESSION["patient"])){
                    require_once(dirname(__FILE__).'/../../library/classes/class.app_base.php');
                    $app_base	= new app_base();
                    unset($_SESSION["glassBreaked_ptId"]);
                    $switch_usr_id=$arr_usr["id"];
                    $askForReason=$app_base->core_get_restricted_status($_SESSION["patient"],$switch_usr_id);
                    if($askForReason==true) {$_SESSION['ask_for_reason_patient']=$_SESSION["patient"];unset($_SESSION["patient"]);}
                }
                
				//transfer chart note locks
				if(isset($_SESSION["patient"]) && !empty($_SESSION["patient"]) && $askForReason==false){
					$oPtLock = new ChartPtLock($_SESSION["authId"], $_SESSION["patient"]);			
					$oPtLock->transferLock($arr_usr["id"]);
					
					//updating recent patients after switch user success.
					$this->app_switch_user_update_recent_pt($arr_usr["id"], $_SESSION["patient"]);
					
					//Patient monitor --
					if($sw_u == true && $_REQUEST["switch_user_tab"]=="Work_View"){
						patient_monitor_daily("SWITCH_USER");
					}					
				}				
				$this->app_logout("app_switch_user");				
			}
		}else{
			$sql = "SELECT * FROM users WHERE username = '".core_refine_user_input($u_n)."' AND locked = '0' AND delete_status = '0' ORDER BY id DESC LIMIT 1";
			$res = imw_query($sql);
			if(imw_num_rows($res) > 0){
				$arr_usr =  imw_fetch_array($res);
				if($arr_usr['password'] != core_refine_user_input($p_w)) $arr_usr =  false;
			}
			else{$arr_usr =  false;}
		}
		
		if($arr_usr != false){
			//creating new session if login is successfull. (More Security: Not using same session file before and after login)
			$old_session_var = $_SESSION;
			session_destroy();		session_destroy();		session_destroy();
			$sess_id = trim( substr( sha1( uniqid( rand() ) ), 0, 22 ) );
			session_name(constant('GLOBAL_IMW_IDOC_SESSION_NAME'));
			session_id($sess_id);
			session_start();
			$_SESSION = $old_session_var;
			
			if(!empty($arr_usr["groups_prevlgs_id"]) && $arr_usr["groups_prevlgs_id"] > 0){
				$ar_prv_tmp = $this->get_grp_prvlgs($arr_usr["groups_prevlgs_id"]);	
			}
			if(isset($ar_prv_tmp) && count($ar_prv_tmp)>0){
				$arr_privileges = $ar_prv_tmp;
			}else{
				//gettin name, privileges and group info to set in session
				$arr_privileges = unserialize(html_entity_decode($arr_usr["access_pri"]));
			}
			
			
			$arr_group = $this->get_group_details($arr_usr["user_group_id"], "id, name");
			if($_REQUEST['remote_fac']>0 && $_REQUEST['redOptMod']!=""){
				$_POST['l_facility']=$_REQUEST['remote_fac'];
				$_SESSION["remote_opt_loc_id"] = $_REQUEST['remote_opt_loc_id'];
				$redOptMod_exp=explode('_',$_REQUEST['redOptMod']);
				$_SESSION['patient'] = $redOptMod_exp[1];
				$_SESSION['opt_enc_id']=$redOptMod_exp[2];
			}
			//setting session vars
			$_SESSION["pass_created_on"]		= $arr_usr["passCreatedOn"];
			$_SESSION["admin_pass_reset"]		= $arr_usr["passwordReset"]; // 1 / 0
			$_SESSION["hippa_status"]			= $arr_usr["HIPPA_STATUS"]; //yes / no
			$_SESSION["sla_status"]				= $arr_usr["SLA"]; // 1 / 0
			$_SESSION["sess_privileges"]		= $arr_privileges;
			$_SESSION["logged_user_type"]		= $arr_usr["user_type"];
			$_SESSION['follow_phy_id']			= $arr_usr['follow_phy_id'];
			$_SESSION["authUser"]				= $arr_usr["username"];
			$_SESSION["authGroup"]				= "";
			$_SESSION["authUserID"]				= $arr_usr["id"];
			$_SESSION["authPass"]				= $p_w;
			$_SESSION["authProvider"]			= "";
			$_SESSION["authProviderName"]		= core_name_format($arr_usr["lname"], $arr_usr["fname"], $arr_usr["mname"]);
			$_SESSION["authGroupId"]			= $arr_group["id"];
			$_SESSION["authProviderGroupName"]	= $arr_group["name"];
			$_SESSION["authId"]					= $arr_usr["id"];
			$_SESSION["userauthorized"]			= $arr_usr["authorized"];
			$_SESSION["WRONGATTEMPT"]			= NULL;
			$_SESSION["MAXATTEMPT"]				= NULL;
			$_SESSION["CAPTCHACOUNT"]			= NULL;
			$_SESSION["last_update"]			= time();
			$_SESSION["Patient_Viewed"]			= "";
			$_SESSION["login_facility"]			= xss_rem($_POST['l_facility']);
			$_SESSION["login_facility_erx_id"]	= $this->get_emdeon_facility_obj_id(xss_rem($_POST['l_facility']));
			$_SESSION["user_role"]				= ""; unset($_SESSION["user_role"]);
			$_SESSION["sess_user_role"] 			= ""; unset($_SESSION["sess_user_role"]);
			$_SESSION["PRACTICE_PATH"]			= constant('PRACTICE_PATH');
			$_SESSION["conf_sec_show_form"]		=""; unset($_SESSION["conf_sec_show_form"]);
			$_SESSION["conf_sec_auth"]			=""; unset($_SESSION["conf_sec_auth"]);	
			$_SESSION["disable_typeahead"]			=""; unset($_SESSION["disable_typeahead"]);
			$_SESSION['updox_user_id']			= trim($arr_usr['updox_user_id']);
			/*Add Touch Works credentials for the user in Session.*/
			if( is_allscripts('enabled') )
			{
				$_SESSION["as_user_id"] = trim($arr_usr["as_username"]);
				/*$_SESSION["as_user_pass"] = trim($arr_usr["as_password"]);*/
				$_SESSION["as_user_entry_code"] = trim($arr_usr["as_entry_code"]);
			}
			
			
			//getting audit policy status in session
			 if(!isset($_SESSION['AUDIT_POLICIES'])){
				 $_SESSION['AUDIT_POLICIES'] = $this->get_all_audit_policies();
			 }
			//auditing login action
			if($_SESSION["AUDIT_POLICIES"]["Provider_Login___Logout"] == 1){
				if($sw_u == true){
					$this->app_audit_switch_user_login();
				}else{
					$this->app_audit_login_success();
				}
			}
			if($returnType == "bl"){
				return true;
			}
			elseif($returnType == "arr"){
				$arrReturn = array(true, $arr_usr["id"]);
				return $arrReturn;
			}
		}else{
			if($returnType == "bl"){
				return false;
			}
			elseif($returnType == "arr"){				
				$arrReturn = array(false, $this->app_user_exists($u_n));
				return $arrReturn;
			}			
		}
	}
	
	/* 	Purpose : to check as if the username is valid or not
		Returns : Array if user exists else "0"	*/
	function app_user_exists($u_n){
		$sql = "SELECT id FROM users WHERE username = '".$u_n."' AND delete_status = '0' ORDER BY id DESC LIMIT 1";
		$res = imw_query($sql);
		if(imw_num_rows($res) > 0){
			return imw_fetch_array($res);			
		}else{
			return "0";
		}
	}

	/*	Function : get_group_details
		Purpose : to get specified details for a group
		Returns : Array with group details if found else false */
	function get_group_details($p_id, $cols){
		$sql = "SELECT ".$cols." FROM user_groups WHERE id = '".$p_id."'";
		$res = imw_query($sql);
		if(imw_num_rows($res) > 0){
			return imw_fetch_assoc($res);			
		}else{
			return false;
		}
	}

	/*	Purpose : to update recent users drop-down after switch user takes place with patient selected.
		Returns : nothing. */	
	function app_switch_user_update_recent_pt($user, $patient_id){		
		$sql = "SELECT patient_id, recent_user_id FROM recent_users WHERE provider_id = '$user' ORDER BY enter_date desc";
		$res = imw_query($sql);
		$count = imw_num_rows($res);
		$patientRecent = false;
		while($rs = imw_fetch_array($res)){
			$recent_user_id = $rs['recent_user_id'];
			if($patient_id == $rs['patient_id']){
				$patientRecent = true;
				$curDate = date('Y-m-d H:i:s');
				$sql2 = "UPDATE recent_users SET patient_id = '$patient_id', enter_date = '$curDate' WHERE recent_user_id = '$recent_user_id'";
				$res2 = imw_query($sql2);
			}
		}
		if($patientRecent == false){
			$curDate = date('Y-m-d H:i:s');	
			if($count == 5){
				$sql3 = "UPDATE recent_users SET patient_id = '$patient_id', provider_id = '$user', enter_date = '$curDate' 
						WHERE recent_user_id = '$recent_user_id'";
			}
			else{			
				$sql3 = "INSERT INTO recent_users SET patient_id = '$patient_id', provider_id = '$user', enter_date = '$curDate'";
			}
			$res3 = imw_query($sql3);
		}
	}
	
	/*	Purpose : To log the user out of the application
		Returns : NULL	*/
	function app_logout($actionLogout = ""){
		ob_start();
		if(isset($_SESSION['res_fellow_sess']))unset($_SESSION['res_fellow_sess']);		
		if($actionLogout == "logout"){
			if(isset($_SESSION['authId']) && !empty($_SESSION['authId'])){
				if($_SESSION["AUDIT_POLICIES"]["Provider_Login___Logout"] == 1){
					$this->app_audit_logout();
				}
			}
		}else if($actionLogout == "session_timeout"){	
			if(isset($_SESSION['authId']) && !empty($_SESSION['authId'])){
				if($_SESSION["AUDIT_POLICIES"]["Session_Timeout"] == 1){
					$this->app_audit_session_timeout();
				}
			}
		}
		if($actionLogout != "app_switch_user"){
			//Unlock patient
			if(isset($_SESSION["patient"]) && !empty($_SESSION["patient"])){
				include_once(dirname(__FILE__)."/work_view/ChartPtLock.php");
				$oPtLock = new ChartPtLock($_SESSION["authId"], $_SESSION["patient"]);			
				$oPtLock->releaseUsersPastPt();
			}
		
			// Unset all of the session variables.
			foreach ($_SESSION as $var => $val){$_SESSION[$var] = null;}

			//Unset session array, by initializing with empty array.
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
			header("location:".$GLOBALS['webroot']."/interface/login/");
			die("Session Expired.");
		}
	}
	
	/*	Purpose : to get Emdeon Facility Object ID again imw facility id
		Returns : STRING value	*/
	function get_emdeon_facility_obj_id($imwFacID){
		$sql = "SELECT fe.fac_obj_id FROM facilities_emdeon fe JOIN facility f ON (fe.id=f.erx_facility_id AND f.id='".$imwFacID."') LIMIT 0,1";
		$res = imw_query($sql);
		if($res && imw_num_rows($res) > 0){
			$arr = imw_fetch_assoc($res);
			return $arr["fac_obj_id"];
		}
		return '';
	}
	
	/*	Purpose : If the login credentials are authenticated successfully, a new popup window is opened for further processes
		Returns : A NEW POP WINDOW IF NOT IPAD, IF IPAD SAME WINDOW IS RELOADED WITH NEW URI */
	function app_login_success(){
		$strFL="";
	
		/*Login Redirect URL - Used only when Signing in from TW Launch Button*/
		$loginURL = '';
		if( is_allscripts() && isset($_POST['as_login']) && (bool)$_POST['as_login'] === true )
		{
			$loginURL = $GLOBALS['php_server'].'/interface/login/index.php';
			
			if( isset($_POST['idoc_pt_id']) && (int)$_POST['idoc_pt_id'] > 0 )
			{
				$_SESSION['patient'] = (int)$_POST['idoc_pt_id'];
				$_SESSION['selectedPatientId'] = (int)$_POST['idoc_pt_id'];
				
				$_SESSION['as_mrn'] = (isset($_POST['External_MRN_4'])) ? $_POST['External_MRN_4'] : '';
				$_SESSION['as_id'] = (isset($_POST['as_id'])) ? $_POST['as_id'] : '';
			}
		}

		/*Login Redirect URL - Used only when Signing in from DSS Launch Button*/
		if( isDssEnable() && isset($_POST['dss_login']) && (bool)$_POST['dss_login'] === true )
		{
			$loginURL = $GLOBALS['php_server'].'/interface/login/index.php';
		
			if( isset($_POST['idoc_pt_id']) && (int)$_POST['idoc_pt_id'] > 0 )
			{
				$_SESSION['patient'] = (int)$_POST['idoc_pt_id'];
				$_SESSION['selectedPatientId'] = (int)$_POST['idoc_pt_id'];
				$_SESSION['dss_mrn'] = (isset($_POST['External_MRN_5'])) ? $_POST['External_MRN_5'] : '';
			}
		}
		
		if($this->isFirstLogger==1){
			$strFL= "&firstLogger=1";
		}
		
		$qryString='';		
		if($_POST['redMedMod']!=''){		// IF REDIRECTED FROM OTHER SERVER
			$qryString = "&redMedMod=".$_POST['redMedMod']."&remote_fac=".$_POST['remote_fac']."&redOptMod=".$_POST['redOptMod'];
		}
		die("
		<html>
			<body>
				<script>
					var options = \"scrollbars=0,resizable=1,status=1,toolbar=0,menubar=0,location=0,width=\"+parseInt(screen.availWidth)+\",height=\" + screen.availHeight;
					var wn_height = parseInt(window.screen.availHeight) + 70;
					nav_name=navigator.appVersion;
					if(nav_name.indexOf(\"iPad\") != \"-1\"){
						self.moveTo(0,0);
						self.resizeTo(1280, screen.availHeight);
						self.name = \"imedic_frameR8\";
						self.location = \"../../index.php?pg=app-welcome-checks".$strFL."&wn_height=\"+wn_height;
					}else{
						var qq = window.open(\"../../index.php?pg=app-welcome-checks".$strFL.$qryString."&wn_height=\"+wn_height,\"imedic_frameR8\",options);
						qq.moveTo(0,0);
						var height_win = (window.screen.availHeight - 0); // It is done to register a change in height so tht a new val can be assign. as height
						qq.resizeTo(screen.width, height_win);
						if(self.name != \"imedic_frameR8\"){
							closeIT();
						}
					}
					function closeIT(){
						!(window.ActiveXObject) && \"ActiveXObject\"
						function isIE11(){
							return !!navigator.userAgent.match(/Trident.*rv[ :]*11\./);
						}
						var ie7 = (document.all && !window.opera && window.XMLHttpRequest) ? true : false; 
						var ie11 = isIE11();
						if (ie7 || ie11){
							window.open(\"\",\"_parent\",\"\"); 
							window.close(); 
						}else{
							this.focus();
							self.opener = this;
							self.close();
						}
					}
				</script>
			</body>
		</html>");
	}
	
	/*
	Function : app_welcome_checks
	Purpose : To perform welcome checks of user a/c on successful login. Also sets the screen height in session.
	Returns : NULL
	*/
	function app_welcome_checks($redMedMod='', $remote_fac='', $redOptMod=''){
		//setting window height in session
		$_SESSION["wn_height"] = intval($_GET["wn_height"]);
		
		//Check First Logger and AutoFinalize-- 
		if(isset($_GET["firstLogger"]) && $_GET["firstLogger"]==1){
			$owv = new WorkView();
			$owv->autoFinalizeCharts();
		}

		//Check First Logger and AutoFinalize-- 
		if($_SESSION["sla_status"] == 0){
			$this->app_sla_process();
		}else if($_SESSION["hippa_status"] == "no"){
			$this->app_hippa_agreement();
		}else if($_SESSION["admin_pass_reset"] == 1){
			$this->app_admin_pass_reset();
		}else{
			$this->app_check_pass_expiry($redMedMod, $remote_fac, $redOptMod);
		}
	}
	
	
	/* 	Purpose : To get the software licence agreement signed.
		Returns : NULL	*/
	function app_sla_process(){
		$show_content = 1;

		//process sla response
		if(isset($_GET["sla_mode"])){
			$show_content = 0;
			if($_REQUEST["sla_mode"] == 1){
				$sql = "UPDATE users SET SLA = 1, sla_date='".date("Y-m-d H:i:s")."' WHERE id = '".$_SESSION["authId"]."'";
				$res = imw_query($sql);
				$_SESSION["sla_status"] = 1;
				if($_SESSION["hippa_status"] == "no"){
					$this->app_hippa_agreement();
				}else if($_SESSION["admin_pass_reset"] == 1){
					$this->app_admin_pass_reset();
				}else{
					$this->app_check_pass_expiry();
				}
			}else{
				$this->app_logout();
			}
		}
		
		if($show_content == 1){
			//show agreement
			$this->class_var["page_title"]= "imwemr Software License Agreement";
			$a = require_once(dirname(__FILE__).'/../../interface/login/login_sla.php');
		}
	}
	
	/*	Purpose : To get the HIPPA agreement signed.
		Returns : NULL	*/
	function app_hippa_agreement(){
		$show_content = 1;
		if(isset($_REQUEST["hippa_mode"])){
			$show_content = 0;
			if($_REQUEST["hippa_mode"] == 1){
				$sql = "UPDATE users SET HIPPA_STATUS = 'yes', hippa_date='".date("Y-m-d H:i:s")."' WHERE id = '".$_SESSION["authId"]."'";
				$res = imw_query($sql);
				$_SESSION["hippa_status"] = "yes";
				if($_SESSION["admin_pass_reset"] == 1){
					$this->app_admin_pass_reset();
				}else{
					$this->app_check_pass_expiry();
				}
			}else{
				$this->app_logout();				
			}
		}
		
		if($show_content == 1){
			//show agreement
			$this->class_var['page_title']= "HIPAA Notice";
			$this->class_var['hippa_agreement_content']=$this->get_hippa_content();
			require_once(dirname(__FILE__).'/../../interface/login/login_hippa.php');
		}
	}
	
	/*	Purpose : to get the HIPPA agreement content
		Returns : STRING - HIPPA content	*/
	function get_hippa_content(){
		$sql = "SELECT loginLegalNotice FROM hippa_setting WHERE id = '1'";
		$res = imw_query($sql);
		if(imw_num_rows($res) > 0){
			$arr = imw_fetch_array($res);
			return $arr["loginLegalNotice"];
		}
	}
	
	/*	Purpose : To authenticate session and update session alive time
		Returns : NULL	*/
	function app_auth_user(){
		//if(isset($_SESSION['authId']) && intval($_SESSION['authId']) > 0)
		if($this->app_set_new_sess_timeout() === false){
			$this->app_logout();
		}
		//session management
		if(!isset($_SESSION["last_update"])){
			$this->app_logout();
		}else{
			//if page has not been updated in a given period of time, we call login screen
			if((time() - $_SESSION["last_update"]) > $GLOBALS["session_timeout"]){
				$this->app_logout();
			}else{
				$_SESSION["last_update"] = time();
			}
		}//$this->app_session_timeout();
	}
	
	/*	Purpose : To set new session timeout time diff
		Returns : NULL	*/
	function app_set_new_sess_timeout(){
		if(isset($_SESSION['authId']) && !empty($_SESSION['authId'])){
			$sess_to_val = $this->get_user_sess_timeout($_SESSION['authId']);
			if($sess_to_val != ""){
				$strMI_or_HR = strrev(substr(strrev($sess_to_val), 0, 2));
				$intTimeout = strrev(substr(strrev($sess_to_val), 2));
				
				if($strMI_or_HR == "MI"){
					$GLOBALS["session_timeout"] = ($intTimeout * 60);
				}else if($strMI_or_HR == "HR"){
					$GLOBALS["session_timeout"] = ($intTimeout * 60 * 60);
				}else{
					$GLOBALS["session_timeout"] = constant("DEFAULT_SESSION_TIMEOUT");
				}
			}else{
				$GLOBALS["session_timeout"] = constant("DEFAULT_SESSION_TIMEOUT");
			}
			@ini_set("session.gc_maxlifetime", $GLOBALS["session_timeout"]); 
			return true;
		}else{
			return false;
		}
	}
	
	/*	Purpose : to get session timeout setting fro this user
		Returns : STRING - session timeout time	*/
	function get_user_sess_timeout($u_id){
		$sql = "SELECT session_timeout FROM users WHERE id = '".$u_id."' LIMIT 1";
		$res = imw_query($sql);
		if($res && imw_num_rows($res) > 0){
			$arr = imw_fetch_assoc($res);
			return $arr["session_timeout"];
		}
		return false;
	}
	
	/*	Purpose : To check password expiry
		Returns : NULL	*/
	function app_check_pass_expiry($redMedMod='', $remote_fac='', $redOptMod=''){
		$this->class_var['show_alert'] = '';
		if(isset($_POST["pnew"]) && $_POST["pnew"] != ""){
			$this->app_save_new_password();
		}
		
		$pass_expiry_days_rs = $this->getHQfacility();
		$MaxExpDays = (intval($pass_expiry_days_rs[0]['maxPassExpiresDays']) > 0) ? intval($pass_expiry_days_rs[0]['maxPassExpiresDays']) : constant("DEFAULT_PASS_EXPIRY_DAYS");
		$created 	= $_SESSION["pass_created_on"];
		$reset 		= $_SESSION["admin_pass_reset"];
	//	echo 'maxDays='.$MaxExpDays.', Created='.$created.', Reset='.$reset.'<br>'; die();
		if($created != '0000-00-00'){
			$ArrCreated = explode("-",$created);
			$Cyear	= $ArrCreated[0];
			$Cmonth	= $ArrCreated[1];
			$Cday	= $ArrCreated[2];
			
			$mkexpire_time = @mktime(0, 0, 0, $Cmonth, $Cday + $MaxExpDays, $Cyear);//New Expiry time created.
			$expirydate = @date("Y-m-d",$mkexpire_time);
			//echo $expirydate.'<br>';
			$ArrExpDate = explode("-",$expirydate);
			$ey	= $ArrExpDate[0];
			$em	= $ArrExpDate[1];
			$ed	= $ArrExpDate[2];
			
			$near_date = @mktime(0, 0, 0, $emonth, $ed - constant("PASS_EXPIRY_NOTICE_DAYS"), $ey);//near date,  show alerts to user.

			$curr = date("Y-m-d");
			$diff_expiry = strtotime($curr) - $mkexpire_time;
			$expiry_day = $diff_expiry/86400;

			if($expiry_day > 0){//password has expired
				$this->class_var["show_reason"]="Your password has expired. Please set your new password.";
				$this->class_var["mandatory"]="yes";
				
				$this->class_var['show_alert']="no";
				$this->class_var["page_title"]= "Change Password";
				include(dirname(__FILE__)."/../../interface/login/login_cp.php");
				
			}else if(abs($expiry_day) <= constant('PASS_EXPIRY_NOTICE_DAYS')){//show warning for these days.
				$this->class_var["show_reason"]= "Your password will expire in ".abs($expiry_day)." days. You can set your new password.";
				$this->class_var["mandatory"]="no";
				
				if(isset($_POST["pnew"]) && $_POST["pnew"] != ""){
					$this->class_var['show_alert']="no";
				}else{
					$this->class_var['show_alert']=abs($expiry_day);
				}
				$this->class_var["page_title"]= "Change Password";
				include(dirname(__FILE__)."/../../interface/login/login_cp.php");
				
			}else{
				header("location:".$GLOBALS['webroot']."/index.php?pg=load-landing-page");//interface/core/index.php
			}
		}else{
			header("location:".$GLOBALS['webroot']."/index.php?pg=load-landing-page");//interface/core/index.php
		}
	}
	
	/*	Purpose : To show change password window if admin has reset or password expired or being expired
		Returns : NULL	*/
	function app_save_new_password(){
		$bl_save_process = true;
		$max_recent_pass_rs = $this->getHQfacility();
		$max_recent_pass = (intval($max_recent_pass_rs[0]['maxRecentlyUsedPass']) > 0) ? intval($max_recent_pass_rs[0]['maxRecentlyUsedPass']) : 10;
		$p_new = hashPassword($_POST["pnew"]);
		if($this->check_password_uniqueness($_SESSION["authId"], $p_new) === false){
			$arr_last_used = $this->last_used_passwords($_SESSION["authId"], $p_new, $max_recent_pass);
			if($arr_last_used==false){
				$bl_save_process = false;
				//password not available (recently used)
				$this->class_var["show_msg"]="New Password is matching with your recently used password. Please choose another password.";
			}
		}else{
			$bl_save_process = false;
			//password not available (duplicate exists)
			$this->class_var["show_msg"]="Password not available. Please choose another password.";
		}
		if($bl_save_process == true){
			//$this->save_user_password($_SESSION["authId"], $p_new);
			$sql = "update users set password = '".$p_new."', locked = 0, passwordChanged = 1, passCreatedOn = '".date('Y-m-d')."', passwordReset = 0 where id  = '".$_SESSION["authId"]."'";
			imw_query($sql);
			
			//$this->save_last_used_password($_SESSION["authId"], $p_new);
			$sql = "insert into lastusedpassword set password1 = '".$p_new."', user_id  = '".$_SESSION["authId"]."'";
			imw_query($sql);
			
			$this->class_var["show_msg"]="Password has been changed successfully.";
			if($this->stop_login_redirect=="yes"){
				header("location:".$GLOBALS['webroot']."/index.php?pg=load-landing-page");
			}else{
				$this->app_logout();
				//header("location:".$this->global_vars['webroot']."/");//interface/core/login.index.php
			}
		}
	}
	
	/* 	Purpose : to check if the new password is in the recently used password list
		Returns : Array with count id if found else false	*/
	function last_used_passwords($u_id, $p_new, $max_recent_pass){
		$return = true;
		$sql = "SELECT password1 FROM lastusedpassword WHERE user_id = '".$u_id."' ORDER BY id DESC LIMIT ".$max_recent_pass;
		$res = imw_query($sql);
		if(imw_num_rows($res) > 0){
			while($r = imw_fetch_array($res)){
				$usedPWD = trim($r['password1']);
				if(trim($p_new) == $usedPWD){
					$return = false;
					continue;
				}
			}
		}
		return $return;
	}
	
	/*	Purpose : to ensure no other user is having same password currently
		Returns : Array with user id if found else false	*/
	function check_password_uniqueness($u_id, $p_new){
		$return = false;
		$sql = "SELECT id FROM users WHERE id != '".$u_id."' AND password = '".$p_new."'"; //HQ facility
		$res = imw_query($sql);
		if(imw_num_rows($res) > 0){
			$return = imw_fetch_array($res);
		}
		return $return;
	}
	
	/*	Purpose : To propmt the user to change password if reset by administrator
		Returns : NULL*/
	function app_admin_pass_reset(){
		if(isset($_POST["pnew"]) && $_POST["pnew"] != ""){
			$this->stop_login_redirect="yes";
			$this->app_save_new_password();
		}
		$this->class_var["show_reason"]= "Your password was reset by the administrator. For security reasons, please change your password now.";
		$this->class_var["mandatory"]= "yes";
		
		$this->class_var['show_alert']="no";
		$this->class_var['page_title']= "Change Password";
		include(dirname(__FILE__)."/../../interface/login/login_cp.php");
		exit;
	}
	
	/*
	Function : app_audit_login_failure
	Purpose : To audit the login failure action for this user for each such instance
	Returns : NULL
	*/
	function app_audit_login_failure($u_id){
		$opreaterId = $u_id;
		if(is_array($u_id)) {
			$opreaterId = $u_id['id'];
		}
		$ip = getRealIpAddr();
		$URL = $_SERVER['PHP_SELF'];													 
		$os = getOS();
		$browserInfoArr = array();
		$browserInfoArr = _browser();
		$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
		$browserName = str_replace(";","",$browserInfo);													 
		$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		$arrAuditTrailLoginS [] = 
					array(
							"Pk_Id"=> $id,
							"Table_Name"=>"users",									
							"Action"=> "user_login_f",
							"Operater_Id"=> $opreaterId,
							"Operater_Type"=> getOperaterType($opreaterId) ,
							"IP"=> $ip,
							"MAC_Address"=> get_mac_add(),
							"URL"=> $URL,
							"Browser_Type"=> $browserName,
							"OS"=> $os,
							"Machine_Name"=> $machineName,
							"Category"=> "login_module",
							"Category_Desc"=> "login"									
						);																		
		$table = array("users");
		$error = array($userError);
		$mergedArray = mergingArray($table,$error);
		auditTrail($arrAuditTrailLoginS,$mergedArray);	
	}
	
	/*
	Function : app_audit_acc_locked
	Purpose : To audit the user a/c locked action
	Returns : NULL
	*/
	function app_audit_acc_locked($u_id){
		$opreaterId = $u_id;
		if(is_array($u_id)) {
			$opreaterId = $u_id['id'];
		}
		$ip = getRealIpAddr();
		$URL = $_SERVER['PHP_SELF'];													 
		$os = getOS();
		$browserInfoArr = array();
		$browserInfoArr = _browser();
		$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
		$browserName = str_replace(";","",$browserInfo);													 
		$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		$arrAuditTrailLoginS [] = 
					array(
							"Pk_Id"=> $id,
							"Table_Name"=>"users",									
							"Action"=> "user_locked",
							"Operater_Id"=> $opreaterId,
							"Operater_Type"=> getOperaterType($opreaterId) ,
							"IP"=> $ip,
							"MAC_Address"=> get_mac_add(),
							"URL"=> $URL,
							"Browser_Type"=> $browserName,
							"OS"=> $os,
							"Machine_Name"=> $machineName,
							"Category"=> "login_module",
							"Category_Desc"=> "login"									
						);																		
		$table = array("users");
		$error = array($userError);
		$mergedArray = mergingArray($table,$error);
		auditTrail($arrAuditTrailLoginS,$mergedArray);	
	}
	
	/*
	Function : app_audit_switch_user_login
	Purpose : To audit the current user switch login
	Returns : NULL
	*/
	function app_audit_switch_user_login(){		
		$opreaterId = $_SESSION['authId'];				
		$ip = getRealIpAddr();
		$URL = $_SERVER['PHP_SELF'];
		$os = getOS();
		$browserInfoArr = array();
		$browserInfoArr = _browser();
		$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
		$browserName = str_replace(";","",$browserInfo);													 
		$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		$arrAuditTrailLoginS [] = 
					array(
							"Pk_Id"=> $_SESSION['authId'],
							"Table_Name"=>"users",									
							"Action"=> "user_login_s",
							"Operater_Id"=> $opreaterId,
							"Operater_Type"=> getOperaterType($opreaterId) ,
							"IP"=> $ip,
							"MAC_Address"=> $_REQUEST['macaddrs'],
							"URL"=> $URL,
							"Browser_Type"=> $browserName,
							"OS"=> $os,
							"Machine_Name"=> $machineName,
							"Category"=> "login_module",
							"Category_Desc"=> "login"									
						);																		
		$table = array("users");
		$error = array($userError);
		$mergedArray = mergingArray($table,$error);
		auditTrail($arrAuditTrailLoginS,$mergedArray);				
	}
	
	/*
	Function : app_audit_login_success
	Purpose : To audit the login success action for this user
	Returns : NULL
	*/
	function app_audit_login_success(){
		$opreaterId = $_SESSION["authId"];				
		$ip = getRealIpAddr();
		$URL = $_SERVER["PHP_SELF"];													 
		$os = getOS();
		$browserInfoArr = array();
		$browserInfoArr = _browser();
		$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
		$browserName = str_replace(";","",$browserInfo);													 
		$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		$arrAuditTrailLoginS [] = 
					array(
							"Pk_Id"=> $id,
							"Table_Name"=>"users",									
							"Action"=> "user_login_s",
							"Operater_Id"=> $opreaterId,
							"Operater_Type"=> getOperaterType($opreaterId) ,
							"IP"=> $ip,
							"MAC_Address"=> get_mac_add(),
							"URL"=> $URL,
							"Browser_Type"=> $browserName,
							"OS"=> $os,
							"Machine_Name"=> $machineName,
							"Category"=> "login_module",
							"Category_Desc"=> "login"									
						);																		
		$table = array("users");
		$error = array($userError);
		$mergedArray = mergingArray($table,$error);
		auditTrail($arrAuditTrailLoginS,$mergedArray);	
	}
	
	/*
	Function : app_audit_logout
	Purpose : To audit the user logout action
	Returns : NULL
	*/
	function app_audit_logout(){
		$arrAuditTrailLogoutS = array();
		$opreaterId = $_SESSION['authId'];				
		$ip = getRealIpAddr();
		$URL = $_SERVER['PHP_SELF'];													 
		$os = getOS();
		$browserInfoArr = array();
		$browserInfoArr = _browser();
		$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
		$browserName = str_replace(";","",$browserInfo);													 
		$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		$arrAuditTrailLogoutS [] = 
					array(
							"Pk_Id"=> $_SESSION['authId'],
							"Table_Name"=>"users",									
							"Action"=> "user_logout_s",
							"Operater_Id"=> $opreaterId,
							"Operater_Type"=> getOperaterType($opreaterId) ,
							"IP"=> $ip,
							"MAC_Address"=> get_mac_add(),
							"URL"=> $URL,
							"Browser_Type"=> $browserName,
							"OS"=> $os,
							"Machine_Name"=> $machineName,
							"Category"=> "logout_module",
							"Category_Desc"=> "logout"									
						);																		
		$table = array("users");
		$error = array($userError);
		$mergedArray = mergingArray($table,$error);
		auditTrail($arrAuditTrailLogoutS,$mergedArray);	
	}
	
	/*
	Function : app_audit_session_timeout
	Purpose : To audit the user session timeout action
	Returns : NULL
	*/
	function app_audit_session_timeout(){
		$opreaterId = $_SESSION['authId'];				
		$ip = getRealIpAddr();
		$URL = $_SERVER['PHP_SELF'];													 
		$os = getOS();
		$browserInfoArr = array();
		$browserInfoArr = _browser();
		$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
		$browserName = str_replace(";","",$browserInfo);													 
		$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		$arrAuditTrailLogoutSessTiOutS [] = 
					array(
							"Pk_Id"=> $_SESSION['authId'],
							"Table_Name"=>"users",									
							"Action"=> "user_session_timeout_s",
							"Operater_Id"=> $opreaterId,
							"Operater_Type"=> getOperaterType($opreaterId) ,
							"IP"=> $ip,
							"MAC_Address"=> get_mac_add(),
							"URL"=> $URL,
							"Browser_Type"=> $browserName,
							"OS"=> $os,
							"Machine_Name"=> $machineName,
							"Category"=> "logout_module",
							"Category_Desc"=> "logout"									
						);																		
		$table = array("users");
		$error = array($userError);
		$mergedArray = mergingArray($table,$error);
		auditTrail($arrAuditTrailLogoutSessTiOutS,$mergedArray);	
	}
	
	/*
	Function : audit_app_start
	Purpose : To audit the application start action
	Returns : NULL
	*/
	function audit_app_start(){
		if(!(isset($_SESSION["login_page_audited"]) && $_SESSION["login_page_audited"] == "yes")){
			$ip = getRealIpAddr();
			$URL = $_SERVER['PHP_SELF'];													 
			$os = getOS();
			$browserInfoArr = array();
			$browserInfoArr = _browser();
			$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
			$browserName = str_replace(";","",$browserInfo);													 
			$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);			
			$arrAuditTrail [] = 
				array(						
						"Table_Name"=>"users",												
						"Action"=> "app_start",												
						"IP"=> $ip,
						"MAC_Address"=> get_mac_add(),
						"URL"=> $URL,
						"Browser_Type"=> $browserName,
						"OS"=> $os,
						"Machine_Name"=> $machineName,
						"Category"=> "login_module",
						"Category_Desc"=> "login"						
					);
			auditTrail($arrAuditTrail,$mergedArray);	
		}
	}
	
	/*
	Function : get_all_audit_policies
	Purpose : to get all policy status
	Returns : NULL
	*/
	function get_all_audit_policies(){
		$return = false;
		$arr_policies = $this->get_all_audit_policies_arr();
		if($arr_policies !== false){
			$arrRep = array("/"," ");
			for($j = 0; $j < count($arr_policies); $j++){
				$strPolicyName = str_replace($arrRep, "_", $arr_policies[$j]["policy_name"]);
				$arrAuditPolicies[$strPolicyName] = $arr_policies[$j]["policy_status"];
			}
			$return = $arrAuditPolicies;
		}
		return $return;
	}
	
	
	function get_all_audit_policies_arr(){
		$return = false;
		$sql = imw_query("select policy_name, policy_status from audit_policies order by policy_id");
		
		if(imw_num_rows($sql) > 0){
			while($row = imw_fetch_assoc($sql)){
				$return[] = $row;
			}
		}
		return $return;
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
    
    function check_office_hours($arr_usr=array(),$cc_t=false) {
        $ret = true;
		$weekdays=array();
        $excluded_users=array();
        $sqlRes=imw_query("select id,enable_office_hours,weekdays,start_hour,start_min,start_time,end_hour,end_min,end_time,excluded_users from office_hours_settings ");
        $ofchrsrow=imw_fetch_assoc($sqlRes);
        $excluded_users=explode(',',$ofchrsrow['excluded_users']);

        if(empty($ofchrsrow)==false && isset($ofchrsrow['enable_office_hours']) && $ofchrsrow['enable_office_hours']==1){
            //check excluded users
            $userName=(isset($_POST['u_n']) && $_POST['u_n']!='')?trim($_POST['u_n']):'';
            if(empty($arr_usr)==false) {
                $userName=$arr_usr['username'];
            }
            $userArrr=$this->app_user_exists($userName);
            if(in_array($userArrr['id'],$excluded_users)) {
                $ret = true;
            } else {
                $weekdays=explode(',',$ofchrsrow['weekdays']);
                $current_weekday=date("l", time());

                $start_hour=$ofchrsrow['start_hour'];
                $start_min=$ofchrsrow['start_min'];
                $start_time=$ofchrsrow['start_time'];

                $end_hour=$ofchrsrow['end_hour'];
                $end_min=$ofchrsrow['end_min'];
                $end_time=$ofchrsrow['end_time'];

                if(in_array($current_weekday,$weekdays)) {
                    $date2=$office_start_time=$start_hour.':'.$start_min.' '.$start_time;
                    $date3=$office_end_time=$end_hour.':'.$end_min.' '.$end_time;
                    if($cc_t!=false && $cc_t!='') {
                        $current_time=$cc_t;
                    } else {
                        $current_time=(isset($_POST['cc_t']) && $_POST['cc_t']!='')?$_POST['cc_t']:'';
                    }

                    $date1 = DateTime::createFromFormat('H:i a', $current_time);
                    $date2 = DateTime::createFromFormat('H:i a', $office_start_time);
                    $date3 = DateTime::createFromFormat('H:i a', $office_end_time);
                    if ($date1 >= $date2 && $date1 <= $date3) {
                        $ret = true;
                    } else {
                        $ret = false;
                    }
                } else {
                    $ret = true;
                }
            }

        }
		return $ret;
    }
	
    //IM-6581:- Default location
    function get_user_default_fac_options($post_data) {
        $default_facility_id=false;
        $default_facility_options='';
        $userName=(isset($post_data['us_name']) && $post_data['us_name']!='')?xss_rem($post_data['us_name']):'';
        if($userName!=''){
            $sql = "SELECT default_facility FROM users WHERE username = '".core_refine_user_input($userName)."' AND locked = '0' AND delete_status = '0' AND default_facility != '0' ORDER BY id DESC LIMIT 1";
            $res1 = imw_query($sql);
            if($res1 && imw_num_rows($res1) > 0){
                $arr_usr =  imw_fetch_assoc($res1);
                $default_facility_id= $arr_usr['default_facility'];
            }
        }
        
        if($default_facility_id) {
            $default_facility_options=$this->getFacilityOptions($default_facility_id);
        }
        
        return $default_facility_options;
    }
	
} //END CLASS
?>