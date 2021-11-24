<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

/*
Purpose: Providing required data to main interface of login (header/footer).
Access Type: include file.
*/
require_once(dirname(__FILE__).'/class.language.php');
require_once(dirname(__FILE__).'/common_function.php');
require_once(dirname(__FILE__).'/work_view/ChartPtLock.php');
class app_base
{
	public $default_product, $product_version, $sess_height, $hash_method;
	public $logged_user, $logged_user_type, $logged_user_name, $res_fellow_sess, $login_facility;
	public $patient_id, $language;
	
	
	###################################################################
	#	constructor function to set commonally used variable on page
	###################################################################
	function __construct()
	{
		$this->session			= $_SESSION;
		$this->logged_user		= (isset($this->session["authId"]) 				&& $this->session["authId"] != "") 				? $this->session["authId"] 				: 0;
		$this->logged_user_type	= (isset($this->session["logged_user_type"]) 	&& $this->session["logged_user_type"] != "") 	? $this->session["logged_user_type"]	: 0;
		$this->logged_user_name	= (isset($this->session["authProviderName"]) 	&& $this->session["authProviderName"] != "") 	? $this->session["authProviderName"]	: '';
		$this->res_fellow_sess 	= (isset($this->session["res_fellow_sess"]) 	&& $this->session["res_fellow_sess"] != "") 	? $this->session["res_fellow_sess"] 	: '';
		$this->login_facility 	= (isset($this->session["login_facility"]) 		&& $this->session["login_facility"] != "") 		? $this->session["login_facility"] 		: 0;
		$this->default_product	= constant('DEFAULT_PRODUCT');
		$this->product_version	= constant('PRODUCT_VERSION');
		$this->hash_method		= constant('HASH_METHOD');
		$this->sess_height		= $this->session['wn_height'];
		$this->session['patient'] = isset($this->session['patient']) ? $this->session['patient'] : '';
		$this->language 		= new core_lang();
	}
	
	
	###################################################################
	#    to get logged in user image and provider console link/text
	###################################################################
	function get_user_info_console()
	{
		$items = array();
		
		$userProfilePic = data_path().'UserId_'.$_SESSION['authId'].'/profile_img/Provider_'.$_SESSION['authId'].'.jpg';      // Profile pic path
		
		// Check if user profile picture exists
		if(file_exists($userProfilePic)){     // If provider profile pic exists
		    // Use profile pic
		    $items['user_image'] 			= $GLOBALS['webroot'].'/data/'.constant('PRACTICE_PATH').'/UserId_'.$_SESSION['authId'].'/profile_img/Provider_'.$_SESSION['authId'].'.jpg';
		}
		else {        // If profile pic does not exist
		    // Use default
            $items['user_image'] 			= $GLOBALS['webroot'].'/library/images/demo_user_pic.jpg';
		}
		
		//CONSOLE TITLE
		$items['console_title']		= 'Console';
		if($this->logged_user_type=='1') $items['console_title']		= 'Physician Console';
			
		//CONSOLE TEXT (LOGGED IN USER'S NAME)
		$items['console_text']		= (strlen($this->logged_user_name) > 20) ? substr($this->logged_user_name,0,18).'..' : $this->logged_user_name;
		
		//RESIDENT/FELLOW CAPTION
		$items['res_fellow_info']	= $this->resident_fellow_info();
		
		return $items;
	}


	###################################################################
	#         "Follow" text to concat with User's Name on Console Link
	###################################################################
	function resident_fellow_info(){
		$res_fellow_name	= '';
		if(in_array($this->logged_user_type,$GLOBALS['arrFollowPhyUserTypes'])){	//Follow Physician – Please enable for Techs and Scribes
			$q 		= "SELECT follow_physician, follow_phy_id, user_type FROM users WHERE id='".$this->logged_user."' LIMIT 0,1";
			$res	= imw_query($q);
			if($res && imw_num_rows($res)==1){
				$rs	= imw_fetch_assoc($res);
				if(($rs["follow_physician"]=="1" && $rs["user_type"]==3) || $rs["user_type"]!=3){
					$res_fellow_name	= '- Attending';
					if(!empty($rs['follow_phy_id'])){
						$this->res_fellow_sess	= $rs['follow_phy_id'];
						$_SESSION['res_fellow_sess'] = $this->res_fellow_sess;
						$res2				= imw_query("SELECT lname,fname FROM users WHERE id = '".$this->res_fellow_sess."' LIMIT 0,1");
						$rs2				= imw_fetch_assoc($res2);
						$res_fellow_name 	='- '.$rs2['lname'].', '.substr($rs2['fname'],0,1).'.';
					}
				}
			}
		}
		return $res_fellow_name;
	}
	

	###################################################################
	#               submenu listing under TESTS main tab
	###################################################################
	function tests_tab_submenu(){
		/********TESTS tab data **********/
		$main_tests_arr = $this->get_core_test_names();
		$test_tab_options_str = '';
		$total_tests_found = count($main_tests_arr);
		if($total_tests_found<20) $total_tests_inCol = 10;
		else if($total_tests_found<30) $total_tests_inCol = 15;
		else if($total_tests_found<40) $total_tests_inCol = 20;
		else if($total_tests_found<50) $total_tests_inCol = 25;
		$num_of_test_cols = (int)$total_tests_found/$total_tests_inCol;
		$column_size_tests = 12;
		if(fmod($total_tests_found,$total_tests_inCol) > 0){$num_of_test_cols++;}
		$column_size_tests_value = (int)($column_size_tests/$num_of_test_cols);
		//if($num_of_test_cols == 3) {$column_size_tests_value=4;}
		//if($num_of_test_cols > 3) {$column_size_tests_value=3;}
        $column_size_tests_value=6;
		$tests_counter = 0;
		foreach($main_tests_arr as $t_type_id=>$test_name){
			$test_opt2 = $t_type_id;
			$test_tab_options_str .= '<li><a href="#" id="Tests" onClick="change_main_Selection(this,\''.$test_opt2.'\');">'.$test_name.'</a></li>';
			$tests_counter++;
			if($tests_counter==$total_tests_inCol){
				$tests_counter = 0;
				echo '<li class="col-sm-'.$column_size_tests_value.'"><ul>'.$test_tab_options_str.'</ul></li>';
				$test_tab_options_str = '';
			}
		}
		if(fmod($total_tests_found,$total_tests_inCol) > 0){
			$tests_counter = 0;
			echo '<li class="col-sm-'.$column_size_tests_value.'"><ul>'.$test_tab_options_str.'</ul></li>';
			$test_tab_options_str = '';
		}
	}
	

	###################################################################
	#              GET Default and Custom Test Names
	###################################################################
	function get_core_test_names(){
		$ret = false;
		$q_tests = "SELECT test_name, temp_name, test_table, test_type,id FROM tests_name WHERE del_status=0 AND status=1 ORDER BY temp_name";
		$res_tests = imw_query($q_tests);
		if($res_tests && imw_num_rows($res_tests)>0){
			$ret = array();
			while($rs_test=imw_fetch_assoc($res_tests)){
				$test_name = $rs_test['temp_name']=='' ? $rs_test['test_name'] : $rs_test['temp_name'];
				$ret[$rs_test['id']]=$test_name;
			}	
		}
		return $ret;
	}
	
	
	###################################################################
	#       Recent patientss to show in search patient dropdown
	###################################################################
	function this_user_recent_patients(){
		$recent_pts_cnt = (int)$GLOBALS["max_recent_search_cache"];
		if($recent_pts_cnt<= 0) $recent_pts_cnt = 5;
		$html = '';
		$q = "SELECT CONCAT(pd.lname,', ',pd.fname,' - ',pd.id) as patient, pd.id FROM patient_data pd 
							JOIN recent_users ru ON (pd.id=ru.patient_id) 
							WHERE ru.provider_id = '".$this->logged_user."' 
							ORDER BY ru.enter_date DESC limit ".$recent_pts_cnt;
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$recent_arr_tmp = array();
			$cnt1 = 1;
			while($rs = imw_fetch_assoc($res)){
				$recent_arr_tmp[$cnt1] = $rs;
				$cnt1++;
			}
			for($i = $recent_pts_cnt; $i>0; $i--){
				$rs = $recent_arr_tmp[$i];
				$html .= '<li><a href="#" pt_id="'.$rs['id'].'">'.$rs['patient'].'</a></li>';
			}
		}
		return $html;
	}
	
	
	###################################################################
	#                   Used in patient search process
	###################################################################	
	function getFindBy($search)
	{
	   $genderSearch = "";
	   $arrSearch = explode(";",$search);
	   $search = trim($arrSearch[0]);
	   $genderSearch = trim($arrSearch[1]);
	   switch($genderSearch){
			case "M":
			case "MALE":
				$genderSearch = "Male"; break;
			case "F":
			case "FEMALE":
				$genderSearch = "Female";
	   }
	   	   
	   $search = trim($search);    
	   $retVal = "Last";
	   $ptrnSSN = '/^[0-9]{3}-[0-9]{2}-[0-9]{4}$/'; 
	   $ptrnPhone = '/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/'; 
	   $ptrnDate = '/^((0[1-9])|(1[012]))[-\/](0[1-9]|[12][0-9]|3[01])[-\/]((18|19|20|21)?[0-9]{2})$/'; 
	   
	   if(is_numeric($search)){
		 $retVal = "ID";
	   }else if(preg_match($ptrnSSN,$search)){
		 $retVal = "SSN";   
	   }else if(preg_match($ptrnPhone,$search)){
		 $retVal = "phone";
	   }else if(preg_match($ptrnDate,$search)){
		 $retVal = "DOB";  
	   }else if(preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is',$search)){
		 $retVal = "email";    
	   }else if(preg_match('/\w+/',$search) && (preg_match('/\d+/',$search)) && (preg_match('/\s*/',$search))){
		 $retVal = "street";
	   }elseif(strpos($search,",") !== false){
		 $retVal = "LastFirstName";
	   }elseif(is_string($search)){
		 $retVal = "Last";  
	   }
	   
	   return $retVal;
	}
	
	###################################################################
	#                      Get Account Status ID
	###################################################################
	function get_account_status_id($statusName){
		$id=0;
		$qry="Select id from account_status WHERE LOWER(status_name)=LOWER('".$statusName."')";
		$rs=imw_query($qry);
		$res=imw_fetch_assoc($rs);
		$stsId= $res['id'];
		return $stsId;
	}
	
	
	###################################################################
	#          CHECK PRIVILEGE FROM SESSION OF LOGGED USER
	###################################################################
	function core_check_privilege($privName){
		if($privName!='' && is_array($privName)==false){
			$privs_array	= $this->session['sess_privileges'];
			if(array_key_exists($privName,$privs_array)){
				return $privs_array[$privName];
			}else return false;
		}else return false;
	}
	

	###################################################################
	#          Main function to search matching patients
	###################################################################
	function core_search($coreFindBy, $coreElemStatus, $corePatient, $previousSearch = false){
		//$arrCorePatient = explode('-',$corePatient);
		//echo $coreFindBy."--".$coreElemStatus."--".$corePatient."--".count($arrCorePatient);
		
		//die;
		// Set to Active if status is Pt. Hx Name
		if($coreElemStatus == 'Pt. Hx Name'){
			$coreElemStatus = 'Active';
		}
		$maxRecordToShow = 200;
		$arrCorePatient = array();	
		$arrCorePatient = explode('__',$corePatient);
		if (count($arrCorePatient) > 1){		
			array_splice($arrCorePatient, $maxRecordToShow);
			$corePatient = implode(',',$arrCorePatient);
			$coreFindBy = "strId";
		}
		elseif (count($arrCorePatient) == 1 && is_numeric($arrCorePatient[0]) && $coreFindBy != "Ins.Policy" && $coreFindBy != "ID" && $coreFindBy != "External MRN"){		
			$corePatient = $arrCorePatient[0];		
			$coreFindBy = "strId";
		}		
		$prevDataPtIdArr = array();
		//$coreStatus = ($coreElemStatus!="Active") ? $coreElemStatus : "Active";	patientStatus='".$coreStatus."'
		$coreStatus = ($coreElemStatus!="Active") ? "patientStatus='".$coreElemStatus."'" : "(patientStatus='Active' OR patientStatus='Transferred' OR patientStatus='Moved' OR patientStatus='Seen as Consult Only' OR patientStatus='Moved out of Area' OR patientStatus='No response Recall' OR patientStatus='Seeing another Dr.' OR patientStatus='Other')";
	
			/*
			Transferred
			Moved
			Seen as Consult Only
			Moved out of Area
			No response Recall
			Seeing another Dr.
			Other
			*/
	
		$corePatient = trim($corePatient);
		
		//$corePatient = str_replace(" ","",$corePatient);		
		$genderSearch = "";
		$arrSearch = explode(";",$corePatient);
		$corePatient = trim($arrSearch[0]);
		$genderSearch = trim($arrSearch[1]);
		
		if(strtoupper($genderSearch) == "M"){
			$genderSearch = "Male";
		}
		elseif(strtoupper($genderSearch) == "MALE"){
			$genderSearch = "Male";
		}
		elseif(strtoupper($genderSearch) == "F"){
			$genderSearch = "Female";
		}
		elseif(strtoupper($genderSearch) == "FEMALE"){
			$genderSearch = "Female";
		}
		
		if($genderSearch){
			$genderSearch = "AND sex ='".$genderSearch."' ";
		}
		//die($coreFindBy);
		$patientSql = "";
		$patientPreviousSql = "";
		$result = array();
		
		//$search_cols = " * ";
		$search_cols = " patient_data.id, patient_data.ss, patient_data.fname, patient_data.mname, patient_data.lname, patient_data.sex, patient_data.phone_home, patient_data.phone_biz, patient_data.phone_cell, patient_data.street, patient_data.street2, patient_data.city, patient_data.state, patient_data.postal_code, patient_data.DOB, patient_data.External_MRN_1, patient_data.External_MRN_2, patient_data.pat_account_status,patient_data.patientStatus ";
		if( is_allscripts() ){$search_cols .= ', patient_data.External_MRN_4'; }
		if(constant('REMOTE_SYNC')==1){$search_cols .= ", patient_data.src_server ";}
		
		$arrCorePatientScan = explode('-',$corePatient);
		if (count($arrCorePatientScan) == 2){		
			$corePatient = $arrCorePatientScan[0];
			$coreConsentFolderId = $arrCorePatientScan[1];
			$coreFindBy = "ID_SCAN";
		}
		
		//echo $coreFindBy."--".$coreElemStatus."--".$corePatient."--".count($arrCorePatientScan);die();
		switch ($coreFindBy):
			case "ID":
			case "ID_SCAN":
				//echo $coreFindBy."--".$coreElemStatus."--".$corePatient."--".count($arrCorePatientScan);die();
				$result = $this->core_getPatientId("$corePatient",$search_cols.", DATE_FORMAT(DOB,'%m/%d/%Y') as DOB_TS","lname ASC, fname ASC","all","0",$coreElemStatus);
				$num_all=count($result);	
			break;
			case "Last":					$lname = trim($corePatient);				
				$fname = '';
				if (preg_match('/^(.*\S)\s*,\s*(.*)/', $lname, $matches)) {
					$lname = $matches[1];
					$fname = $matches[2];
				}						
				$patientSql = "select ".$search_cols.", DATE_FORMAT(DOB,'%m/%d/%Y') as DOB_TS from patient_data where lname like '".addslashes($lname)."%' " .
								"AND fname like '$fname%' ".
								"AND ".$coreStatus." ".$genderSearch.
								"order by lname ASC, fname ASC LIMIT 0, ".$maxRecordToShow;									
				############################
				/*$patientPreviousSql = "SELECT ".$search_cols.", patient_previous_data.patient_id 
										FROM patient_data, patient_previous_data 
										WHERE (prev_lname LIKE '".addslashes($lname)."%' OR new_lname LIKE '".addslashes($lname)."%') 
										AND ".$coreStatus." AND patient_data.pid = patient_previous_data.patient_id 
										GROUP BY patient_id 
										ORDER By patient_data.lname ASC, patient_data.fname ASC 
										LIMIT 0, ".$maxRecordToShow;*/

				$patientPreviousSql = "SELECT ".$search_cols.", ppd.patient_id 
										FROM patient_data INNER JOIN (SELECT patient_previous_data.patient_id AS patient_id 
										FROM patient_previous_data 
										WHERE (prev_lname LIKE '".addslashes($lname)."%')
										UNION 
										SELECT patient_previous_data.patient_id  AS patient_id 
										FROM patient_previous_data 
										WHERE (new_lname LIKE '".addslashes($lname)."%')
										)ppd  ON ppd.patient_id = patient_data.pid
										WHERE ".$coreStatus." 
										GROUP BY patient_data.pid 
										ORDER By patient_data.lname ASC, patient_data.fname ASC 
										LIMIT 0, ".$maxRecordToShow;
				############################						
			break;
			case "DOB":
				$DOB = $this->core_fixDate($corePatient, $DOB);			
				$dob_where=" DOB like '".$DOB."'% ";
				if(strlen($DOB)==10)$dob_where=" DOB='".$DOB."' ";
				$patientSql = "select ".$search_cols.", DATE_FORMAT(DOB,'%m/%d/%Y') as DOB_TS from patient_data where $dob_where " .
								"AND ".$coreStatus." ".$genderSearch.
								"order by lname ASC, fname ASC LIMIT 0, ".$maxRecordToShow;									
				###################################			
				/*$patientPreviousSql = "SELECT ".$search_cols.", patient_previous_data.patient_id 
										FROM patient_data, patient_previous_data 
										WHERE ".$coreStatus." AND patient_data.DOB like '".$DOB."%' 
										AND patient_data.pid = patient_previous_data.patient_id 
										GROUP BY patient_id 
										ORDER by patient_data.lname ASC, patient_data.fname ASC 
										LIMIT 0, ".$maxRecordToShow;*/
				$patientPreviousSql = "SELECT ".$search_cols.", ppd.patient_id 
										FROM patient_data INNER JOIN (SELECT patient_previous_data.patient_id AS patient_id 
										FROM patient_previous_data 
										WHERE (prev_dob LIKE '".addslashes($DOB)."%')
										UNION 
										SELECT patient_previous_data.patient_id  AS patient_id 
										FROM patient_previous_data 
										WHERE (new_dob LIKE '".addslashes($DOB)."%')
										)ppd  ON ppd.patient_id = patient_data.pid
										WHERE ".$coreStatus." 
										GROUP BY patient_data.pid 
										ORDER By patient_data.lname ASC, patient_data.fname ASC 
										LIMIT 0, ".$maxRecordToShow;

				############################					
			break;
			case "LastFirstName":
				$patientSearch = trim($corePatient);
				$searchArr = preg_split("/[\s|,|;]+/", $patientSearch);
				$lastName = trim($searchArr[0]);
				$firstName = trim($searchArr[1]);
				$middleName = trim($searchArr[2]);
				$MNameSearch = "";
				if($middleName){
					$middleName = str_replace(".","",$middleName);
					$MNameSearch = "AND mname like '".addslashes($middleName)."%' ";
				}
				$patientSql = "select ".$search_cols." from patient_data where lname like '".addslashes($lastName)."%' AND fname like '".addslashes($firstName)."%' ".$MNameSearch." ".$genderSearch." AND ".$coreStatus." order by lname, mname, fname LIMIT 0, ".$maxRecordToShow;
				###################################			
				/*$patientPreviousSql = "SELECT ".$search_cols.", patient_previous_data.patient_id 
										FROM patient_data, patient_previous_data 
										WHERE ".$coreStatus." AND (prev_lname LIKE '".addslashes($lastName)."%' OR new_lname LIKE '".addslashes($lastName)."%') 
										AND (prev_fname LIKE '".addslashes($firstName)."%' OR new_fname LIKE '".addslashes($firstName)."%')
										AND patient_data.pid = patient_previous_data.patient_id 
										GROUP BY patient_id 
										ORDER by patient_data.lname ASC, patient_data.fname ASC 
										LIMIT 0, ".$maxRecordToShow;*/
				$patientPreviousSql = "SELECT ".$search_cols.", ppd.patient_id 
											FROM patient_data INNER JOIN (SELECT patient_previous_data.patient_id AS patient_id 
											FROM patient_previous_data 
											WHERE (prev_lname LIKE '".addslashes($lastName)."%')
											AND (prev_fname LIKE '".addslashes($firstName)."%')
											UNION 
											SELECT patient_previous_data.patient_id  AS patient_id 
											FROM patient_previous_data 
											WHERE (new_lname LIKE '".addslashes($lastName)."%')
											AND (new_fname LIKE '".addslashes($firstName)."%')
											)ppd  ON ppd.patient_id = patient_data.pid
											WHERE ".$coreStatus." 
											GROUP BY patient_data.pid 
											ORDER By patient_data.lname ASC, patient_data.fname ASC 
											LIMIT 0, ".$maxRecordToShow;						
				############################	
			break;
			case "phone":
				$phoneNo = trim($corePatient);
				$phoneNo = core_phone_format($phoneNo, $countryName = "USA");
				$phoneNo = preg_replace('/[^0-9]/','',$phoneNo);
				$patientSql = "SELECT ".$search_cols.", DATE_FORMAT(DOB,'%m/%d/%Y') as DOB_TS FROM patient_data WHERE " .
							 "(replace(phone_home,'-','') like '$phoneNo%' ". 
							 "OR replace(phone_biz,'-','') like '$phoneNo%' ".
							 "OR replace(phone_cell,'-','') like '$phoneNo%') " .
							 "AND ".$coreStatus." ".$genderSearch. 
							 "ORDER BY lname ASC, fname ASC LIMIT 0, ".$maxRecordToShow;
				#############################3
				/*$patientPreviousSql = "SELECT ".$search_cols.", patient_previous_data.patient_id 
										FROM patient_data, patient_previous_data 
										WHERE ".$coreStatus." AND ".
										"( replace(prev_phone_home,'-','') LIKE '$phoneNo%' OR replace(new_phone_home,'-','') LIKE '$phoneNo%' ".
										"OR replace(prev_phone_biz,'-','') LIKE '$phoneNo%' OR replace(new_phone_biz,'-','') LIKE '$phoneNo%' ".
										"OR replace(prev_phone_cell,'-','') LIKE '$phoneNo%' OR replace(new_phone_cell,'-','') LIKE '$phoneNo%' )".
										"AND patient_data.pid = patient_previous_data.patient_id 
										GROUP BY patient_id 
										ORDER by patient_data.lname ASC, patient_data.fname ASC 
										LIMIT 0, ".$maxRecordToShow;*/
				$patientPreviousSql = "SELECT ".$search_cols.", ppd.patient_id 
										  FROM patient_data INNER JOIN (SELECT patient_previous_data.patient_id as patient_id 
										  FROM patient_previous_data 
										  WHERE prev_phone_home LIKE '".$phoneNo."%'
										  UNION 
										  SELECT patient_previous_data.patient_id  AS patient_id 
										  FROM patient_previous_data 
										  WHERE new_phone_home LIKE '".$phoneNo."%'
										  UNION 
										  SELECT patient_previous_data.patient_id  AS patient_id 
										  FROM patient_previous_data 
										  WHERE prev_phone_biz LIKE '".$phoneNo."%'
										  UNION 
										  SELECT patient_previous_data.patient_id  AS patient_id 
										  FROM patient_previous_data 
										  WHERE new_phone_biz LIKE '".$phoneNo."%'
										  UNION
										  SELECT patient_previous_data.patient_id  AS patient_id 
										  FROM patient_previous_data 
										  WHERE prev_phone_cell LIKE '".$phoneNo."%'
										  UNION 
										  SELECT patient_previous_data.patient_id  AS patient_id 
										  FROM patient_previous_data 
										  WHERE new_phone_cell LIKE '".$phoneNo."%') ppd 
										  ON ppd.patient_id = patient_data.pid
										  WHERE ".$coreStatus."
										  GROUP BY patient_data.pid
										  ORDER by patient_data.lname ASC, patient_data.fname ASC 
										  LIMIT 0, ".$maxRecordToShow;
				############################	
			break;
			case "street":
			case "Address":
				$address = trim($corePatient);
				$patientSql="SELECT ".$search_cols.", DATE_FORMAT(DOB,'%m/%d/%Y') as DOB_TS FROM patient_data WHERE " .
							 "(street LIKE '%".addslashes($address)."%' OR street2 LIKE '%".addslashes($address)."%' OR 
								postal_code LIKE '%".addslashes($address)."%' OR city LIKE '%".addslashes($address)."%')".
							 "AND ".$coreStatus." ".$genderSearch. 
							 "ORDER BY lname ASC, fname ASC LIMIT 0, ".$maxRecordToShow;
				#############################
				/*$patientPreviousSql = "SELECT ".$search_cols.", patient_previous_data.patient_id 
										FROM patient_data, patient_previous_data 
										WHERE ".$coreStatus." AND ((prev_street LIKE '".addslashes($address)."%' OR new_street LIKE '".addslashes($address)."%')) 
										AND patient_data.pid = patient_previous_data.patient_id 
										GROUP BY patient_id 
										ORDER by patient_data.lname ASC, patient_data.fname ASC 
										LIMIT 0, ".$maxRecordToShow;*/
				$patientPreviousSql = "SELECT ".$search_cols.", ppd.patient_id 
										FROM patient_data INNER JOIN (SELECT patient_previous_data.patient_id AS patient_id 
										FROM patient_previous_data 
										WHERE (prev_street LIKE '".addslashes($address)."%')
										UNION 
										SELECT patient_previous_data.patient_id  AS patient_id 
										FROM patient_previous_data 
										WHERE (new_street LIKE '".addslashes($address)."%')
										)ppd  ON ppd.patient_id = patient_data.pid
										WHERE ".$coreStatus." 
										GROUP BY patient_data.pid 
										ORDER By patient_data.lname ASC, patient_data.fname ASC 
										LIMIT 0, ".$maxRecordToShow;
				############################				
			break;
			case "email":
				$emailId = trim($corePatient);
				$patientSql="SELECT ".$search_cols.", DATE_FORMAT(DOB,'%m/%d/%Y') as DOB_TS FROM patient_data WHERE " .
							 "email like '".addslashes($emailId)."%' ".
							 "AND ".$coreStatus." ".$genderSearch. 
							 "ORDER BY lname ASC, fname ASC LIMIT 0, ".$maxRecordToShow;
				#############################
				/*$patientPreviousSql = "SELECT ".$search_cols.", patient_previous_data.patient_id 
										FROM patient_data, patient_previous_data 
										WHERE ".$coreStatus." AND ((prev_email LIKE '".addslashes($emailId)."%'OR new_email LIKE '".addslashes($emailId)."%')) 
										AND patient_data.pid = patient_previous_data.patient_id 
										GROUP BY patient_id 
										ORDER by patient_data.lname ASC, patient_data.fname ASC 
										LIMIT 0, ".$maxRecordToShow;*/
				$patientPreviousSql = "SELECT ".$search_cols.", ppd.patient_id 
										FROM patient_data INNER JOIN (SELECT patient_previous_data.patient_id AS patient_id 
										FROM patient_previous_data 
										WHERE (prev_email LIKE '".addslashes($emailId)."%')
										UNION 
										SELECT patient_previous_data.patient_id  AS patient_id 
										FROM patient_previous_data 
										WHERE (new_email LIKE '".addslashes($emailId)."%')
										)ppd  ON ppd.patient_id = patient_data.pid
										WHERE ".$coreStatus." 
										GROUP BY patient_data.pid 
										ORDER By patient_data.lname ASC, patient_data.fname ASC 
										LIMIT 0, ".$maxRecordToShow;
				############################
			break;
			case "SSN":
				$ssn = trim($corePatient);
				$patientSql="SELECT ".$search_cols.", DATE_FORMAT(DOB,'%m/%d/%Y') as DOB_TS FROM patient_data WHERE " .
							 "ss like '".addslashes($ssn)."%' ".
							 "AND ".$coreStatus." ".$genderSearch. 
							 "ORDER BY lname ASC, fname ASC LIMIT 0, ".$maxRecordToShow;
							
			break;
			case "Resp.LN":
				$patientSearch = trim($corePatient);			
				$patientSql = "SELECT ".$search_cols." 
								FROM resp_party INNER JOIN patient_data ON resp_party.patient_id = patient_data.id 
								WHERE resp_party.lname LIKE '%$patientSearch%'                                        
								ORDER BY patient_data.fname LIMIT 0, ".$maxRecordToShow;				
				#############################						
				$patientPreviousSql = "SELECT ".$search_cols.", patient_previous_data.patient_id, resp_party.patient_id 
										FROM resp_party 
										INNER JOIN patient_data ON resp_party.patient_id = patient_data.id 
										INNER JOIN patient_previous_data ON patient_previous_data.patient_id = resp_party.patient_id  
										WHERE ".$coreStatus." AND resp_party.lname LIKE '%$patientSearch%' 
										GROUP BY patient_id 
										ORDER by patient_data.lname ASC, patient_data.fname ASC 
										LIMIT 0, ".$maxRecordToShow;
				############################				
				
			break;
			case "Ins.Policy":
				$patientSearch = trim($corePatient);						
				$patientSql = "SELECT insurance_data.policy_number,	patient_data.fname,patient_data.pid,patient_data.lname, patient_data.street,patient_data.phone_home,patient_data.ss,patient_data.DOB,patient_data.id
								FROM insurance_data 
								INNER JOIN patient_data ON insurance_data.pid = patient_data.id
								WHERE insurance_data.policy_number LIKE '$patientSearch%'
								GROUP BY patient_data.id	
								ORDER BY patient_data.fname LIMIT 0, ".$maxRecordToShow;				
				#############################	
				$patientPreviousSql = "SELECT ".$search_cols.", patient_previous_data.patient_id, resp_party.patient_id 
										FROM insurance_data 
										INNER JOIN patient_data ON insurance_data.pid = patient_data.id
										INNER JOIN patient_previous_data ON patient_previous_data.patient_id = insurance_data.pid   
										WHERE ".$coreStatus." AND insurance_data.policy_number LIKE '$patientSearch%' 
										GROUP BY patient_data.id 
										ORDER by patient_data.lname ASC, patient_data.fname ASC 
										LIMIT 0, ".$maxRecordToShow;											
				############################				
			break;
			case "strId":
				$patientSearch = trim($corePatient);						
				$patientSql = "select ".$search_cols." from patient_data where id IN (".$patientSearch.") order by lname ASC, fname ASC LIMIT 0, ".$maxRecordToShow;	
				$patientPreviousSql = "";
				#############################													
			break;
			case "External MRN":
				$external_MRN = trim($corePatient);
                $MRNSearch="";
                if(is_numeric($external_MRN)) {
                    $MRNSearch="(TRIM(LEADING '0' FROM External_MRN_1) = '".(int)$external_MRN."' or TRIM(LEADING '0' FROM External_MRN_2) = '".(int)$external_MRN."') ";
                    if(constant("DISP_EXTERNAL_MRN")==1){$MRNSearch="TRIM(LEADING '0' FROM External_MRN_1) = '".(int)$external_MRN."' ";}
                    if(constant("DISP_EXTERNAL_MRN")==2){$MRNSearch="TRIM(LEADING '0' FROM External_MRN_2) = '".(int)$external_MRN."' ";}
                } else if(ctype_alnum($external_MRN)){
                    $MRNSearch="External_MRN_1 = '".$external_MRN."' or External_MRN_2 = '".$external_MRN."' ";
                    if(constant("DISP_EXTERNAL_MRN")==1){$MRNSearch="External_MRN_1 = '".$external_MRN."' ";}
                    if(constant("DISP_EXTERNAL_MRN")==2){$MRNSearch="External_MRN_2 = '".$external_MRN."' ";}
                }
                    
				$patientSql = "SELECT ".$search_cols.", DATE_FORMAT(DOB,'%m/%d/%Y') as DOB_TS FROM patient_data WHERE " .
							 $MRNSearch.
							 "AND ".$coreStatus." ".$genderSearch. 
							 "ORDER BY lname ASC, fname ASC LIMIT 0, ".$maxRecordToShow;
							
			break;
		endswitch;	
		$arrPatient = array();
		$prevDataPatientIdArr = array();
		
		$rsPatientSql = imw_query($patientSql);
		$rsPatientCnt = imw_num_rows($rsPatientSql);
		if( $coreElemStatus == 'Active' && $rsPatientCnt <= 0 ) {
			$patientSql = str_replace($coreStatus,"(patientStatus='Inactive' OR patientStatus='Deceased')",$patientSql);
		}
		
		//die($patientSql.'sdfsdfdsfds');
		#getting Array for patient main Search
		if(empty($patientSql) == false){
			$rsPatientSql = imw_query($patientSql);
			if($rsPatientSql){
				if(imw_num_rows($rsPatientSql)>0) {		
					$a=0;
					while($rowPatientSql = imw_fetch_assoc($rsPatientSql)){
						if(constant('REMOTE_SYNC')==1){
							$rowPatientSql['ptAddr'] 		= core_address_format(trim($rowPatientSql['street']), trim($rowPatientSql['street2']), trim($rowPatientSql['city']), trim($rowPatientSql['state']), trim($rowPatientSql['postal_code']));
							$rowPatientSql["phone_home1"]	= core_phone_format($rowPatientSql["phone_home"]);
							$rowPatientSql["DOB1"] 			= core_date_format($rowPatientSql["DOB"]);
						}
						$arrPatient[$rowPatientSql['id']] = $rowPatientSql;		
						$a++;		
					}		
				}
				imw_free_result($rsPatientSql);
			}
		}
		
		#getting Array for patient Prior Names
		if($previousSearch == true && empty($patientPreviousSql) == false){
			$prevDataSearchRes = imw_query($patientPreviousSql);
			if($prevDataSearchRes){
				if(imw_num_rows($prevDataSearchRes)>0) {
					$a=0;
					while($prevDataSearchRow = imw_fetch_assoc($prevDataSearchRes)){
						$prevDataPatientIdArr[$prevDataSearchRow['id']] = $prevDataSearchRow;
						$a++;		
					}
					imw_free_result($prevDataSearchRes);
				}
			}
		}
		
		$num_all = count($arrPatient);
		$Total_Records = count($prevDataPatientIdArr);
		if((int)constant("APP_DEBUG_MODE") == 1){
			$csvFilePath = dirname(__file__)."/../tmp/search_report.csv";		
			if(file_exists($csvFilePath) == false){
				$fpH = fopen($csvFilePath,"w");
				if($fpH!==false){
				fputcsv ( $fpH, array ("Search Keyword", "Search by Operator", "Search Date(m-d-y)", "Search Time", "Status Search By", "Master Search Made For", "Total Result"), ",", '"' );
				fclose($fpH);
				}	
			}
			$fpH = fopen($csvFilePath,"a+");
			if($fpH!==false){
			fputcsv ( $fpH, array ($corePatient, $_SESSION['authId'], date("m-d-y"), date("h:i:s A"), $coreStatus, $coreFindBy, $num_all), ",", '"' );
			fclose($fpH);
			}
		}
		if(count($result) > 0){
			$arrPatient = $result;
			$num_all = count($arrPatient);
		}
		return array($arrPatient, $num_all, $prevDataPatientIdArr, $Total_Records);
	}
	

	function core_fixDate($date, $default="0000-00-00") {
		$fixed_date = $default;
		$date = trim($date);
        
        $curr_year=date('y');
        $curr_date=date('Y-m-d');
		if (preg_match("'^[0-9]{1,4}[/\.\-][0-9]{1,2}[/\.\-][0-9]{1,4}$'", $date) ) {
			$dmy = preg_split("'[/\.\-]'", $date);
			if ($dmy[0] > 99) {
				$fixed_date = sprintf("%04u-%02u-%02u", $dmy[0], $dmy[1], $dmy[2]);
			} else {
                if(strlen($dmy[2])!=4) {
                    $org_dmy=$dmy[2];
                    if ($dmy[2] < 1000) $dmy[2] += 1900;
                    if ($dmy[2] < 1910) $dmy[2] += 100;
                    if($curr_year>$org_dmy){
                        $dt = DateTime::createFromFormat('y', $org_dmy);
                        $dmy[2]=$dt->format('Y');
                    }
                }
				$fixed_date = sprintf("%04u-%02u-%02u", $dmy[2], $dmy[0], $dmy[1]);
			}
		}
		if(preg_match("'^[0-9]{1,4}[\][0-9]{1,2}[\][0-9]{1,4}$'", $date))	{
			$date=raw_date_reverse($date,$reverse=false);
			$dmy = preg_split("'-'", $date);
			if ($dmy[0] > 99) {
				$fixed_date = sprintf("%04u-%02u-%02u", $dmy[0], $dmy[1], $dmy[2]);
			} else {
                if(strlen($dmy[2])!=4) {
                    $org_dmy=$dmy[2];
                    if ($dmy[2] < 1000) $dmy[2] += 1900;
                    if ($dmy[2] < 1910) $dmy[2] += 100;
                    if($curr_year>$org_dmy){
                        $dt = DateTime::createFromFormat('y', $org_dmy);
                        $dmy[2]=$dt->format('Y');
                    }
                }
				$fixed_date = sprintf("%04u-%02u-%02u", $dmy[2], $dmy[0], $dmy[1]);
			}
		}
        if(preg_match("'^[0-9]{1,4}[\][0-9]{1,2}[\,\/][0-9]{1,4}$'", $date))	{
			
			$date=raw_date_reverse($date,$reverse=true);
			$dmy = preg_split("'-'", $date);
			if ($dmy[0] > 99) {
				$fixed_date = sprintf("%04u-%02u-%02u", $dmy[0], $dmy[1], $dmy[2]);
			} else {
                if(strlen($dmy[2])!=4) {
                    $org_dmy=$dmy[2];
                    if ($dmy[2] < 1000) $dmy[2] += 1900;
                    if ($dmy[2] < 1910) $dmy[2] += 100;
                    if($curr_year>$org_dmy){
                        $dt = DateTime::createFromFormat('y', $org_dmy);
                        $dmy[2]=$dt->format('Y');
                    }
                }
				$fixed_date = sprintf("%04u-%02u-%02u", $dmy[2], $dmy[0], $dmy[1]);
			}
		}

        $pattern2 = '/^([0-9]{1,2})\\/([0-9]{1,2})\\/([0-9]{2})$/';
        $pattern4 = '/^([0-9]{1,2})\\/([0-9]{1,2})\\/([0-9]{4})$/';
        if (preg_match($pattern2, $date, $matches)){
            $dt1 = DateTime::createFromFormat('m/d/y',$date);
        }else if (preg_match($pattern4, $date, $matches)){
            $dt1 = DateTime::createFromFormat('m/d/Y',$date);
        }
        if($dt1) {
            $posted_date=$dt1->format('Y-m-d');
            if($posted_date && $curr_date>$posted_date){
                $fixed_date=$posted_date;
            }
        }
		return $fixed_date;
	}

	############################################################################
	#Main function to return matching patient's data during pt.search operation
	############################################################################
	function core_getPatientId($corePid = "%", $given = "pid, id, lname, fname, mname, providerID, DATE_FORMAT(DOB,'%m/%d/%Y') as DOB_TS", $orderby = "lname ASC, fname ASC", $limit="all", $start="0",$status="Active"){
		global $athenaIDArr; 
		$externalIDArr = $athenaIDArr;
		$returnval=array();
		
		//Superbill --
		if($status=="EID"){
			$sql2 = "SELECT *  FROM patient_data 
			   INNER JOIN patient_charge_list ON patient_data.id = patient_charge_list.patient_id
			   WHERE patient_charge_list.encounter_id LIKE '$corePid'";
			   $rez2 = imw_query($sql2);
			while($row2=imw_fetch_assoc($rez2)) {
				$returnval[]=$row2;
			}
			if(imw_num_rows($rez2)==0){
				$sql1 = "SELECT *  FROM patient_data 
				   INNER JOIN superbill ON patient_data.id = superbill.patientId
				   WHERE superbill.encounterId LIKE '$corePid'";
				   $rez1 = imw_query($sql1);
				while($row1=imw_fetch_assoc($rez1)) {
					$returnval[]=$row1;
				}
			}
		}else{
			$sql = "select $given from patient_data where id='".$corePid."' " .
				   "/*AND patientStatus = '".$status."'*/".
				   "order by $orderby";
			if ($limit != "all") {
				$sql .= " limit $start, $limit"; }
			$rez = imw_query($sql);
			while($row=imw_fetch_assoc($rez)){
				$returnval[]=$row;
			}		
		}
		
		/*All Scripts Patient Search*/
		if( is_allscripts() && count($returnval)<=0 ) {
			$sql = "select $given from patient_data where External_MRN_4='".$corePid."' " .
				   "order by $orderby";
			if ($limit != "all") {
				$sql .= " limit $start, $limit";
			}
			$rez = imw_query($sql);
			if($rez && imw_num_rows($rez) > 0){
				while($row = imw_fetch_assoc($rez)){
					$returnval[$iter]=$row;
				}
			}
		}
		/*End All Scripts Patient Search*/
		
		//	
		
		if(count($returnval)<=0) {
			$blExternalRecFound = false;
			if(count($externalIDArr)>0){
				foreach($externalIDArr as $athenaFieldName) {
					$sql = "select $given from patient_data where $athenaFieldName='".$corePid."' " .
						   "/*AND patientStatus = '".$status."'*/".
						   "order by $orderby";
					$rez = imw_query($sql);
					while($row=imw_fetch_assoc($rez)) {
						$returnval[]=$row;
						$blExternalRecFound = true;
					}
					if($blExternalRecFound == true){
						break;
					}
				}
			}
		}
		  
		return $returnval;      
	}
	

	###################################################################
	#   Getting provider's restricted status for particular patient
	###################################################################
	function core_get_restricted_status($patient_id,$switch_usr_id=false){	
		$askForReason = false;
		
		if(empty($patient_id)){
			return $askForReason;
		}
		
		if(isset($_SESSION["glassBreaked_ptId"])){
			if($_SESSION["glassBreaked_ptId"] == $patient_id){
				return $askForReason;
			}
		}
		
		$sql_getRestricted = "SELECT restrict_providers FROM restricted_providers where patient_id ='".imw_real_escape_string($patient_id)."' and restrict_providers != ''";
		$res_Restricted = imw_query($sql_getRestricted);				
		$num_rows = 0;	
		if($res_Restricted){
			$num_rows = imw_num_rows($res_Restricted);
			if($num_rows > 0){
				$resultRow = imw_fetch_array($res_Restricted);
				$explodeArray = explode(",", $resultRow["restrict_providers"]);
				if(is_array($explodeArray)){
					if(in_array($_SESSION["authId"], $explodeArray)){
						$askForReason = true;
					}
                    if($switch_usr_id && in_array($switch_usr_id, $explodeArray)){
						$askForReason = true;
					}
				}
			}
		}
		return $askForReason;
	}
	

	###################################################################
	#   Change patient or while selecting new patient after search 
	###################################################################
	function clean_patient_session(){
		patient_monitor_daily("PATIENT_CLOSE");
		$arr = array("patient","new_casetype","pid","form_id","finalize_id","defSxView",
					 "PT_DOC_ALERT_STATUS","test2edit","document_scan_id","encounter_id","currentCaseid",
					 "patient_parent_server","flg_phy_view","PT_EDU_ALERT_STATUS","PT_EDU_ARRAY",
					 "PT_EDU_ALERT_ARRAY","lockedChart","permitChart","pt_poe_msg","acc_commt_pat","glassBreaked_ptId");
		
		if( is_allscripts() ) array_push($arr, 'as_mrn', 'as_id', 'tw_encounter_id');
		
		if(isDssEnable()) array_push($arr,'dss_mrn');
		foreach($arr as $key=>$val){
			$_SESSION[$val]="";
			$_SESSION[$val]=NULL;
			unset($_SESSION[$val]);
		}
		//Release Chart Pt Lock -------------------------
		$oPtLock = new ChartPtLock($this->sess_vars["authId"]);
		$oPtLock->releaseUsersPastPt();
			
	}


	###################################################################
	#         To get tabs according to the provider privileges
	###################################################################
	function get_provider_tabs(){
		$arr_tabs = array("Work_View", "Tests", "Medical_Hx", "Patient_Info", "Docs", "Accounting", "Billing", "Optical", "Reports", "Admin");
		$arr_prov_tab = array();
		if(is_array($arr_tabs) && count($arr_tabs) > 0){
			foreach($arr_tabs as $this_tab){
				$arr_prov_tab[$this_tab]["status"] = "on";
				$arr_prov_tab[$this_tab]["default"] = "no";
			}
			//EMR TABS
			if(core_check_privilege(array("priv_cl_work_view")) == false && isset($arr_prov_tab["Work_View"]["status"])){
				$arr_prov_tab["Work_View"]["status"] = "off";
			}
			if(core_check_privilege(array("priv_cl_tests")) == false && isset($arr_prov_tab["Tests"]["status"])){
				$arr_prov_tab["Tests"]["status"] = "off";
			}
			if(core_check_privilege(array("priv_cl_medical_hx")) == false && isset($arr_prov_tab["Medical_Hx"]["status"])){
				$arr_prov_tab["Medical_Hx"]["status"] = "off";
			}

			//PATIENT INFO TAB
			/*
			if(core_check_privilege(array("priv_Front_Desk")) == false && isset($arr_prov_tab["Patient_Info"]["status"])){
				$arr_prov_tab["Patient_Info"]["status"] = "off";
			}*/
			if(core_check_privilege(array("priv_scheduler_demo")) == false && isset($arr_prov_tab["Patient_Info"]["status"])){
				$arr_prov_tab["Patient_Info"]["status"] = "off";
			}
			
			//DOCUMENTS TAB
			if(core_check_privilege(array("priv_document")) == false && isset($arr_prov_tab["Docs"]["status"])){
				$arr_prov_tab["Docs"]["status"] = "off";
			}
			
			//ACCOUNTING TAB
			if(core_check_privilege(array("priv_Accounting")) == false && isset($arr_prov_tab["Accounting"]["status"])){
				$arr_prov_tab["Accounting"]["status"] = "off";
			}

			//BILLING TAB
			if(core_check_privilege(array("priv_Billing")) == false && isset($arr_prov_tab["Billing"]["status"])){
				$arr_prov_tab["Billing"]["status"] = "off";
			}

			//OPTICAL TAB
			if(core_check_privilege(array("priv_Optical")) == false && isset($arr_prov_tab["Optical"]["status"])){
				$arr_prov_tab["Optical"]["status"] = "off";
			}
			$arr_report_priv=array('priv_sc_scheduler','priv_report_schduled','priv_billing_fun','priv_sc_daily','priv_bi_analytics','priv_acct_receivable','priv_report_audit','priv_pt_instruction','priv_report_mur','priv_cl_clinical','priv_cl_visits','priv_cl_ccd','priv_sc_house_calls','priv_sc_recall_fulfillment','priv_cl_order_set','priv_cdc','priv_report_tests','priv_bi_end_of_day','priv_bi_front_desk','priv_bi_ledger','priv_bi_prod_payroll','priv_bi_ar','priv_bi_statements','priv_report_payments','priv_report_copay_rocan','priv_un_superbills','priv_un_encounters','priv_un_payments','priv_report_adjustment','priv_report_refund','priv_daily_balance','priv_fd_collection','priv_report_practice_analytics','priv_cpt_analysis','priv_report_yearly','priv_bi_ledger','priv_report_revenue','priv_provider_mon','priv_ref_phy_monthly','priv_facility_monthly','priv_report_ref_phy','priv_credit_analysis','priv_report_patient','priv_report_ins_cases','priv_report_eid_status','priv_allowable_verify','priv_vip_deferred','priv_provider_rvu','priv_sx_payment','priv_net_gross','priv_ar_reports','priv_days_ar','priv_receivables','priv_unworked_ar','priv_unbilled_claims','priv_top_rej_reason','priv_new_statements','priv_prev_statements','priv_prev_hcfa','priv_statements_pay','priv_pt_statements','priv_pt_collections','priv_assessment','priv_collection_report','priv_tfl_proof','priv_report_rta','priv_billing_verification','priv_patient_status','priv_saved_scheduled','priv_executed_report','priv_cn_pending','priv_contact_lens','priv_cn_pending','priv_cn_ordered','priv_cn_received','priv_cn_dispensed','priv_cn_reports','priv_glasses','priv_gl_pending','priv_gl_ordered','priv_gl_received','priv_gl_dispensed','priv_gl_report','priv_documents','priv_alerts','priv_stage_i','priv_stage_ii','priv_stage_iii','priv_ccd_export','priv_ccd_import','priv_lab_import','priv_ccr_import','priv_dat_appts','priv_recalls','priv_reminder_lists','priv_no_shows');
			if(core_check_privilege($arr_report_priv,'any') == false && isset($arr_prov_tab["Reports"]["status"])){
				$arr_prov_tab["Reports"]["status"] = "off";
			}

			//ADMIN TAB
			$priv_arr = array("priv_admin","priv_admin_billing","priv_group","priv_facility","priv_provider_management","priv_ref_physician","priv_admin_billing","priv_admin_scheduler","priv_document","priv_console","priv_iols","priv_set_margin","priv_erx_preferences","priv_room_assign","priv_Optical","priv_iOLink","priv_chart_notes","priv_Security","priv_admin_scp","priv_vs","priv_immunization","priv_manage_fields","priv_orders","priv_iportal");
			if(core_check_privilege($priv_arr,'any') == false && isset($arr_prov_tab["Admin"]["status"])){
				$arr_prov_tab["Admin"]["status"] = "off";
			}

			if($this->default_product == "imwemr"){
				$arr_prov_tab["Work_View"]["status"] = "off";
				$arr_prov_tab["Tests"]["status"] = "off";
				$arr_prov_tab["Medical_Hx"]["status"] = "off";
				$arr_prov_tab["Optical"]["status"] = "off";
			}

			if(isset($this->session["logged_user_type"]) && !empty($this->session["logged_user_type"])){
				//if user switched
				$switch_tab_name = "";
				if(isset($this->session["switch_user_tab"]) && !empty($this->session["switch_user_tab"])){
					foreach($arr_prov_tab as $tab_name => $this_prov_tab){
						if($this_prov_tab["status"] == "on" && $tab_name == $this->session["switch_user_tab"]){
							$arr_prov_tab[$tab_name]["default"] = "yes";
							$switch_tab_name = $tab_name;
							break;
						}
					}
				}
				
				//unset switch user tab
				$this->session["switch_user_tab"] =  "";

				if($switch_tab_name == ""){
					switch($this->session["logged_user_type"]){
						case "1":
						case "3":
							if($this->default_product == "imwemr"){
								$arr_prov_tab["Patient_Info"]["default"] = "yes";
								$switch_tab_name = "Patient_Info";
							}else{
								$arr_prov_tab["Work_View"]["default"] = "yes";
								$switch_tab_name = "Work_View";
							}							
						break;
						default:
							if(isset($arr_prov_tab["Patient_Info"]["status"]) && $arr_prov_tab["Patient_Info"]["status"] == "on"){
								$arr_prov_tab["Patient_Info"]["default"] = "yes";
								$switch_tab_name = "Patient_Info";
							}else{
								foreach($arr_prov_tab as $tab_name => $this_prov_tab){
									if($this_prov_tab["status"] == "on"){
										$arr_prov_tab[$tab_name]["default"] = "yes";
										$switch_tab_name = $tab_name;
										break;
									}
								}
							}
						break;
					}
				}
			}
		}
		$arr_prov_tab["LOAD_THIS_TAB"] = $switch_tab_name;
		return $arr_prov_tab;
	}
	

	###################################################################
	#         Get iconbar related info
	###################################################################
	function get_iconbar_status($icon_name=''){
		$patient_id = $this->session['patient'];
		$returnArray = array();
		//if($icon_name=='') $icon_name='PtNameIdeRx';
		switch($icon_name){
			case '':
			case 'PtNameIdeRx':
				$arr_PatDetails = array();
				if($patient_id != 0){
					$res1 = imw_query("SELECT * FROM patient_data WHERE id='$patient_id' LIMIT 0,1");
					if($res1 && imw_num_rows($res1)==1){
						$rs1 = imw_fetch_assoc($res1);
						$arr_PatDetails['name'] = (($rs1["title"])?$rs1["title"].' ':'').$rs1["lname"].', '.$rs1["fname"].' '.substr($rs1["mname"],0,1).' - '.$patient_id;
						
						$arr_PatDetails['nick_name'] = $rs1['nick_name'];
						$arr_PatDetails['phonetic_name'] = $rs1['phonetic_name'];
						$arr_PatDetails['language'] = $rs1['language'];
						$arr_PatDetails['lang_code'] = $rs1['lang_code'];
						
						$arr_PatDetails['eRx'] = '';
						$ArBilPolicies = $this->get_copay_policies();
						if($ArBilPolicies["Allow_erx_medicare"] == "Yes" && $rs1["erx_patient_id"] != "null" && $rs1["erx_patient_id"] != ""){
							$arr_PatDetails["eRx"] = "e/Rx";
						}
						
						$arr_PatDetails["DOD"] = "";
						if($rs1["patientStatus"] == "Deceased" && $rs1["dod_patient"] != "0000-00-00" && $rs1["dod_patient"] != "" && $rs1["dod_patient"] != NULL){ 
							$arr_PatDetails["DOD"] = $rs1["dod_patient"];
						}
						
						$arr_PatDetails["pt_image"] = "";
						if($rs1["p_imagename"] != "" && $rs1["p_imagename"] != NULL && file_exists(data_path().$rs1["p_imagename"])){ 
								$image_size = newImageResize(data_path().$rs1["p_imagename"],45,'','array');
								$arr_PatDetails["pt_image"] = $rs1["p_imagename"];
								$arr_PatDetails["pt_image_width"] = $image_size['width'];
								$arr_PatDetails["pt_image_height"] = $image_size['height'];
						}
						
						$arr_PatDetails["Default_Facility"] = "";
						if($rs1["default_facility"]!='' && $rs1["default_facility"]!='0'){
							$vquery_t = "select facilityPracCode from pos_facilityies_tbl WHERE pos_facility_id='".$rs1["default_facility"]."' LIMIT 0,1";
							$vsql_t = imw_query($vquery_t);
							if($vsql_t && imw_num_rows($vsql_t)==1){
								$vrs = imw_fetch_assoc($vsql_t);
								$arr_PatDetails["Default_Facility"] = $vrs['facilityPracCode'];
							}
							
						}
						$arr_PatDetails["pt_acc_status"] = "";
						$qry = 'Select * from account_status WHERE del_status=0 ORDER BY status_name';
						$sql = imw_query($qry);
						while($row = imw_fetch_assoc($sql)){
							if( $rs1['pat_account_status'] == $row['id'] ) {
								$arr_PatDetails["pt_acc_status"] = $row['status_name'];
								break;	
							}
						}
						
						// get referral icon status
						$arr_PatDetails["pri_ref_flag"] = "";
						$arr_PatDetails["sec_ref_flag"] = "";
						$arr_PatDetails["ter_ref_flag"] = "";
						
						// Code to set if insurance  page is not loaded yet
						if(trim($_SESSION["new_casetype"])=="")
						{
							$ins_qry = "select ins_caseid,ins_case_type from insurance_case 
									where patient_id = '".$_SESSION["patient"]."' and case_status = 'Open'
									order by ins_case_type LIMIT 0,1";
							$ins_sql = imw_query($ins_qry);
							$ins_res = imw_fetch_assoc($ins_sql);		
							$_SESSION["new_casetype"] = $ins_res['ins_case_type'];
							$_SESSION['new_casetype'] = $_SESSION["new_casetype"];
							$_SESSION['currentCaseid'] = $ins_res['ins_caseid'];
						}
						//End Code to set if insurance  page is not loaded yet
						
						$refArr = $this->get_pat_ins_flag_status($patient_id,$_SESSION["new_casetype"]);
						$arr_PatDetails["pri_ref_flag"] = $refArr['pri_ref_flag'];
						$arr_PatDetails["sec_ref_flag"] = $refArr['sec_ref_flag'];
						$arr_PatDetails["ter_ref_flag"] = $refArr['ter_ref_flag'];
						
					}
					$return = '';
					foreach($arr_PatDetails as $k=>$v){
						$return .= $k.':~:'.$v.'::~::';
					}
				}
				$returnArray['PtNameIdeRx'] = $return; unset($return);
				if($icon_name!='') break;
			case 'ptSpecificAlert':
				$query_ptalert = "SELECT alertId FROM alert_tbl WHERE patient_id='".$patient_id."' AND alert_showed NOT LIKE '%1%' AND is_deleted = '0' and status='1'";
				$result_ptalert = imw_query($query_ptalert);
				if($result_ptalert){
					$returnArray['ptSpecificAlert'] = imw_num_rows($result_ptalert);
				}
				if($icon_name!='') break;
			case 'ptPortal':
					$query_ptPortal = " SELECT username, password FROM patient_data WHERE id = '".$patient_id."' ";
					$result_ptPortal = imw_query($query_ptPortal);
					if($result_ptPortal && imw_num_rows($result_ptPortal) > 0){
						$rowFetch = imw_fetch_assoc($result_ptPortal);

						$ptUsername = (isset($rowFetch['username']) && empty($rowFetch['username']) == false) ? trim($rowFetch['username']) : '';
						$ptPassword = (isset($rowFetch['password']) && empty($rowFetch['password']) == false) ? trim($rowFetch['password']) : '';

						if(empty($ptPassword) == false && empty($ptUsername) == false) $returnArray['ptPortal'] = true;
					}
					if($icon_name!='') break;	
			case 'MODtext':
				$mod_text = $this->get_user_mod();
				$returnArray['MODtext'] = $mod_text;
				if($icon_name!='') break;
			case 'update_recent_search':
				$search = '
					<li><a href="#">Active</a></li>
					<li><a href="#">Inactive</a></li>';
					if(constant('EXTERNAL_MRN_SEARCH')=="YES") $search .= '<li><a href="#">External MRN</a></li>';
				$search .= '<li class="divider"></li>
					<li class="dropdown-submenu">
						<a tabindex="-1" href="#" class="noclose" role="button" data-toggle="dropdown" data-target="#"><b>Advance</b></a>
						<ul class="dropdown-menu pl15">
							<li><a href="#">Deceased</a></li>
							<li><a href="#">Moved out of Area</a></li>
							<li><a href="#">No response Recall</a></li>
							<li><a href="#">Seen as Consult Only</a></li>
							<li><a href="#">EID</a></li>
							<li><a href="#">Resp.LN</a></li>
							<li><a href="#">Ins.Policy</a></li>
							<li><a href="#">Address</a></li>
							<li><a href="#">Pt. Hx Name</a></li>
						</ul>
					</li>
					<li class="divider"></li>
					<li><a href="#"><strong>Recent Patients</strong></a></li>
					<li><iframe style="z-index:-1;top:0px;background:transparent;position:absolute;width:100%;height:100%;border:transparent;"  allowtransparency="true" src="about:blank"></iframe></li>';
					$search .= $this->this_user_recent_patients();
				$returnArray['recent_search'] = $search;
				if($icon_name!='') break;
		}
		return $returnArray;
	}


	###################################################################
	#         Checking eRX status
	###################################################################
	function get_copay_policies(){
		$res = imw_query("SELECT * FROM copay_policies ORDER BY policies_id ASC LIMIT 1");
		if($res && imw_num_rows($res) > 0){
			$rs = imw_fetch_assoc($res);
			return $rs;
		}else{
			return false;
		}
	}

	

	###################################################################
	#   			Display patient information Menu 
	###################################################################
	function show_patient_info(){
		
		$mrn = "";
		if(isset($GLOBALS["SHOW_PT_MRN"]) && !empty($GLOBALS["SHOW_PT_MRN"])){
			if($GLOBALS["SHOW_PT_MRN"] == "External_MRN_1"){ $mrn = "External_MRN_1, ";	}
			else if($GLOBALS["SHOW_PT_MRN"] == "External_MRN_2"){	$mrn = "External_MRN_2, ";	}
			else if($GLOBALS["SHOW_PT_MRN"] == "External_MRN_3"){	$mrn = "External_MRN_3, ";	}
			else if($GLOBALS["SHOW_PT_MRN"] == "External_MRN_4"){	$mrn = "External_MRN_4, ";	}				
		}
		
		$patient_id = $this->session['patient'];
		$querypatient = imw_query("SELECT ".$mrn." DOB, ss, phone_home, sex FROM patient_data WHERE id ='$patient_id'");
		$pt_detail_array = array();
		if(count($querypatient) > 0){
			$info_row = imw_fetch_assoc($querypatient);
			$dob = core_date_format($info_row["DOB"]);
			$info_row['DOB'] =	$dob;
			$pt_detail_array = $info_row;
		}
		return $pt_detail_array;
	}
	
	function set_res_fellow_sess()
	{
		$user_id = trim($_REQUEST['user_id']);
		$_SESSION['res_fellow_sess'] = $user_id != "" ? $user_id : "";		
		$this->set_follow_phy_id($_SESSION['res_fellow_sess']);//set  in db	
		
		$returnStr = "||";
		if(!empty($_SESSION['res_fellow_sess'])){
			$res_fel_name_arr = getUserDetails($_SESSION['res_fellow_sess']," lname, fname ");
			$res_fel_name ='- '.$res_fel_name_arr['lname'].', '.$res_fel_name_arr['fname'];				
			$res_fel_name = substr($res_fel_name, 0, 11).'..';
			$returnStr = $res_fel_name.'||';
		}
		
		if(isset($_SESSION['patient']) && trim($_SESSION['patient'])!="")
		{
			$returnStr .= $_SESSION['patient'];
		}		
		echo $returnStr;
	}
	
	/*
	Function: set_follow_phy_id
	Purpose: To set Follow physician 
	Returns: nothing
	*/
	function set_follow_phy_id($id){
		if(!empty($id) || $id=="0"){			
			$this->reset_scribe_phyid($id);
			$q = "UPDATE users SET follow_phy_id='".$id."', follow_physician='1' WHERE id='".$_SESSION['authId']."' ";			
			$row=imw_query($q);
		}
	}
	
	//Function:change Scribe in work view
	function reset_scribe_phyid($id){
		$phyid = trim($id);
		if(!empty($phyid) || $id=="0"){
			if(isset($_SESSION["form_id"]) && !empty($_SESSION["form_id"])){ //active form exists
				//get Prev follow id
				$sql="SELECT follow_phy_id FROM users where id='".$_SESSION['authId']."' ";
				$res=imw_query($sql);
				if(imw_num_rows($res)>=1){
					$row = imw_fetch_assoc($res);
					$prev_follow_phy_id=$row["follow_phy_id"];
				}				
				
				if(!empty($prev_follow_phy_id)){
					//change in follow phy id saved chart_master_table
					$sql="SELECT count(*) as num FROM chart_master_table 
							where id='".$_SESSION["form_id"]."' AND finalize='0' AND providerId='".$prev_follow_phy_id."' AND delete_status='0' ";
					$row=imw_query($sql);
					if(imw_num_rows($row)>=1){
						//check if prev_follow_phy_id exists in signature
						$sql="SELECT count(*) as num FROM chart_signatures where pro_id='".$prev_follow_phy_id."' AND form_id='".$_SESSION["form_id"]."' ";
						$row=imw_query($sql);
						if(imw_num_rows($row)>=1){
						}else{
							//change in master table
							$sql = "UPDATE chart_master_table SET providerId='".$phyid."' WHERE id='".$_SESSION["form_id"]."'  ";
							$row=imw_query($sql);
							
							//change in message
							if(!empty($phyid)){
								//User Name
								$user_nm="";
								$qry = imw_query("select lname,fname,mname,id from users where id='".$phyid."' order by user_type, fname, lname ");
								if(imw_num_rows($qry)>=1){
									$row = imw_fetch_assoc($qry);
									$user_nm= $row["fname"]." ".$row["mname"]." ".$row["lname"];
								}
								if(!empty($user_nm)){
									$dt = date("m-d-Y");
									$arret=array();
									$sql = "SELECT c1.id, c1.atsd_msg, c2.user_type FROM chart_signatures c1 
											LEFT JOIN users c2 ON c2.id = c1.pro_id
											WHERE c1.attsd='1' AND c1.form_id='".$_SESSION["form_id"]."' AND c1.pro_id='".$_SESSION['authId']."'  ";
									$rez = imw_query($sql);
									while($row=imw_fetch_assoc($rez)){
										$i++;
										$id = $row["id"];
										$msg = $row["atsd_msg"];
										if(!empty($msg)){
											$msg=preg_replace('/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/', $dt, $msg); //date change
											if($row["user_type"]=="13"){
												//scribe
												$ptrn = '/to me by \<font color\=\"red\"\>(.*?)\<\/font\>/';
												$rep = 'to me by <font color="red">'.$user_nm.'</font>';
												$btn_id = "btn_attest_scribe";
											}
											//
											$msg=preg_replace($ptrn, $rep, $msg); //msg change										
											//
											$sql = "UPDATE chart_signatures SET atsd_msg = '".imw_real_escape_string($msg)."' WHERE id = '".$id."' ";
											$r = imw_query($sql);
										}
									}
								}
							}//	
						}
					}
				}	
			}
		}	
	}
	
	//Returns user MOD details 
	function get_user_mod(){//MESSAGE OF DAY
		$authId = $this->session["authId"];
		$return = false;
		$result1 = imw_query("SELECT sch_facilities FROM users WHERE id='$authId'");
		if($result1 && imw_num_rows($result1)>0){
			while($rs1 = imw_fetch_array($result1)){
				$str_facs = '';
				if($rs1[0] != ''){
					$arr_facilities = explode(';',$rs1[0]);
					$arr_HqFac = $this->get_hq_fac_arr(true);
					$arr_facilities[] = $arr_HqFac['id'];
					$str_facs = implode(',',array_unique($arr_facilities));
					$query2 = "SELECT name, mess_of_day FROM facility WHERE id IN(".$this->login_facility.") AND mess_of_day!=''";
					$result2 = imw_query($query2);
					if($result2 && imw_num_rows($result2) > 0){
						while($val = imw_fetch_array($result2)){
							$return .= '<b>'.$val[0].'</b><div class="unBold">'.$val[1].'</div><br>';	
							$return = str_replace(array("\r\n", "\n"), "", $return);
						}
					}
				}
			}
		}
		return $return;
	}
	
	//Returns HQ Facility array
	function get_hq_fac_arr($force=false){	
		$arr_HQ = array();
		$query = array();
		$query[] = "SELECT id, name FROM facility WHERE facility_type = '1' LIMIT 0,1 ";
		if($force){
			$query[] = "SELECT id, name FROM facility ORDER BY id LIMIT 0,1";/*--if No Hq. facility is defined, then selecting 1st facility---*/
		}
		
		foreach($query as $sql_qry){
			$result = imw_query($sql_qry);
			if($result && imw_num_rows($result) == 1){
				$rs = imw_fetch_array($result);
				$arr_HQ[0] =  $rs['id'];
				$arr_HQ['id'] = $rs['id'];
				$arr_HQ[1] =  $rs['name'];
				$arr_HQ['name'] =  $rs['name'];
				return $arr_HQ;
				break;		
			}else{
				continue;
			}
		}
		
		if(count($arr_HQ) >= 1)
			return $arr_HQ;
		else
			return false;
	}
	
	/*
	Function: get_rp_reason_codes
	Purpose: To get restricted provider reason codes
	Returns: options for select else empty
	*/
	function get_rp_reason_codes(){		
		$reason_code_options = "";
		$sql = "SELECT * FROM scp_reasons ORDER BY reason_code";
		$res = imw_query($sql);
		while($val = imw_fetch_array($res)){
			$reason_code_options .= "<option value=\\\"".$val["scp_id"]."\\\">".$val["reason_code"]." - ".$val["reason_desc"]."</option>";
		}
		return $reason_code_options;
	}


	/*
	Function: get_loggedin_facility
	Purpose: To get Name of the logged in facility
	Returns: Logged in Facility Name
	*/
	function get_loggedin_facility_name(){		
		$sql = 'SELECT `name` FROM `facility` WHERE `id`='.((int)$_SESSION['login_facility']);
		$resp = imw_query($sql);

		$facilityName = '';
		if( $resp && imw_num_rows($resp) > 0)
		{
			$facilityName = imw_fetch_assoc($resp);
			$facilityName = $facilityName['name'];
		}
		return $facilityName;
	}
	
	//Returns Touchworks patient data
	function get_touchworks_patients($findBy = 'Active', $string = ''){
		$returnArr = array();
		$returnArr['title'] = 'Remote Patient Search Result';
		$returnArr['msg'] = '';
		if(empty($string)) return $returnArr;
		
		if(is_allscripts()){
			$GLOBALS['rethrow'] = true;
			include_once( $GLOBALS['srcdir'].'/allscripts/as_patient.php' );
			try{
				$search_name_value = trim($string);
				
				if( $search_name_value == '')
					throw new asException( 'Call Error', 'Blank value supplied to search.' );
				
				
				/*Find details of new patient and present for confirmation*/
				$patientObj = new as_patient();
				$patientDataSet = $patientObj->PtName( $search_name_value );
				
				if( isset($patientObj) && is_object($patientObj) ) unset($patientObj);
				
				if( is_null( $patientDataSet ) && count($patientDataSet) > 0 )
					throw new asException( 'Alert', 'No data returned from All Scripts.' );
				
				if(count($patientDataSet) > 0){
					$counterr = 1;
					foreach( $patientDataSet as $patientData ){
						$ptDob = strtotime($patientData->dateofbirth);
						$patientData->dateofbirth = core_date_format( date('Y-m-d', $ptDob) );
						$ptAddress = core_address_format( trim($patientData->AddressLine1), trim($patientData->AddressLine2), trim($patientData->City), trim($patientData->State), trim($patientData->ZipCode) );
						
						$tmpArr = array();
						$tmpArr['#'] = $counterr;
						$tmpArr['First Name'] = $patientData->firstname;
						$tmpArr['Last Name'] = $patientData->lastname;
						$tmpArr['Middle Name'] = $patientData->middlename;
						$tmpArr['Patient ID'] = $patientData->ID;
						$tmpArr['MRN'] = $patientData->MRN;
						$tmpArr['SSN'] = $patientData->ssn;
						$tmpArr['Gender'] = $patientData->gender;
						$tmpArr['Street Address'] = $ptAddress;
						$tmpArr['Phone'] = core_phone_format($patientData->HomePhone);
						$tmpArr['Date of birth'] = $patientData->dateofbirth;
						
						array_push($returnArr, $tmpArr);
						$counterr++;
					}	
				}
				
			}catch( asException $e){
				$returnArr['Error'] = $e->getErrorText();
			}	
		}else{
			$returnArr['Error'] = 'Allscript Integration not active. Please contact administrator.';
		}
		
		return $returnArr;
	}
	
	/*
	Function: get_case_type_class e.g. normal or vision class
	Purpose: To get patient case type's class
	Returns: ARRAY - if found, else false
	*/
	function get_case_type_class($p_casetype_id = 0){	
		$return = false;
		if($p_casetype_id != 0){
			$sql = "select vision, normal from insurance_case_types where case_id = '".$p_casetype_id."'";
			$res = imw_query($sql);
			if(imw_num_rows($res) > 0){
				$return = imw_fetch_assoc($res);
			}
		}
		return $return;
	}
	
	/*
	Function: get_active_ins_for_casetype
	Purpose: To get active insurance for this case type for this patient
	Returns: ARRAY - if found, else false
	*/
	function get_active_ins_for_casetype($p_id, $p_casetype_id = 0){
		$return = false;
		$sql = "select patient_reff.no_of_reffs as no_of_reffs,patient_reff.reff_used as reff_used,patient_reff.end_date as end_date,patient_reff.effective_date as effective_date,insurance_data.type, insurance_data.id 
		from insurance_data 
		join patient_reff on insurance_data.id = patient_reff.ins_data_id 
		join insurance_case on insurance_case.ins_caseid = insurance_data.ins_caseid 
		where insurance_case.case_status = 'Open' and insurance_data.pid = '".$p_id."' and patient_reff.patient_id = insurance_data.pid and insurance_data.referal_required = 'Yes' and insurance_data.actInsComp = '1' and insurance_data.provider > '0' and insurance_case.ins_case_type = '".$p_casetype_id."' 	
		and patient_reff.del_status = 0 
		order by insurance_data.type asc
		";
		$res = imw_query($sql);
		if($res && imw_num_rows($res) > 0){
			$return = $res;
		}
		return $return;
	}
	
	/*
	Function: get_pat_ins_flag_status
	Purpose: to get reff flags for this patient for the ins case in session if normal
	Returns: ARRAY with flag images
	*/

	function get_pat_ins_flag_status($p_id, $p_casetype_id = 0){

		$name_convert = array("primary" => "Pri", "secondary" => "Sec", "tertiary" => "Ter");
		$flag_convert = array("primary" => "pr", "secondary" => "sr", "tertiary" => "tr");
		$Pri_flag = $Sec_flag = $Ter_flag = "";
		$arr_ct_class = $this->get_case_type_class($p_casetype_id);		
		if($arr_ct_class !== false){
			if($arr_ct_class['normal'] == 1){
				$res_ins = $this->get_active_ins_for_casetype($p_id, $p_casetype_id);
				if($res_ins !== false){
					$reff_flag_status_arr = array();
					while($arr_ins = imw_fetch_assoc($res_ins)){
						
						//setting names
						$temp_name_index = trim($arr_ins["type"]);
						if($temp_name_index != ""){
							$flag_name = $name_convert[$temp_name_index]."_flag";						
							$sess_name = "insId".$name_convert[$temp_name_index];
							$_SESSION[$sess_name] = $arr_ins["id"];
							
							$reff_flag_status_arr[$flag_name]['temp_name_index'] = $temp_name_index;
							if(!isset($reff_flag_status_arr[$flag_name]['no_of_reffs']) || $reff_flag_status_arr[$flag_name]['no_of_reffs'] == "")
							{
								$reff_flag_status_arr[$flag_name]['no_of_reffs'] = 0;	
							}
							
							if($reff_flag_status_arr[$flag_name]['no_of_reffs'] > 1)
							{
								continue;	
							}
							
							//reff calculations
							$remaining_reffs = $arr_ins["no_of_reffs"];
							$reff_used = $arr_ins["reff_used"];
							$end_date = $arr_ins["end_date"];
							if($end_date != "0000-00-00" && $end_date != "" && $end_date != NULL){
	
								$curdate = strtotime(date("Y-m-d"));
								$end_date = strtotime($end_date);
								
								if($end_date < $curdate || ($remaining_reffs <= 0 && $reff_used > 0 && $reff_used != '')){
									
								}else if($end_date == $curdate){
									$reff_flag_status_arr[$flag_name]['no_of_reffs'] = 1;
								}else if($end_date == $curdate && $remaining_reffs == 1){
									$reff_flag_status_arr[$flag_name]['no_of_reffs'] = 1;
								}else if($arr_ins["effective_date"]<=date("Y-m-d")){
									$reff_flag_status_arr[$flag_name]['no_of_reffs'] = 2;
								}
							}else if($arr_ins["effective_date"] != "0000-00-00" && $arr_ins["end_date"] == "0000-00-00"){
								if(empty($remaining_reffs)){
								}
								else if($remaining_reffs == 1){
									$reff_flag_status_arr[$flag_name]['no_of_reffs']=1;
								}
								else {
									$reff_flag_status_arr[$flag_name]['no_of_reffs']=2;
								}
							}
						}					
					}
					
					foreach($reff_flag_status_arr as $flag_name => $reff_no_arr)
					{
						$no_of_reffs = $reff_no_arr['no_of_reffs'];
						$temp_name_index = $reff_no_arr['temp_name_index'];
						//getting flags
						if($no_of_reffs == 1){
							$$flag_name = "text-orange";
							//$$flag_name = "<img src=\"".$GLOBALS["web_root"]."/images/flag_orange_".$flag_convert[$temp_name_index].".gif\" align=\"bottom\" alt=\"".ucfirst($temp_name_index)." Referrals\" border=\"0\" style=\"cursor:hand\" tbl=\"".substr($temp_name_index, 0, 3)."\">";
						}else if($no_of_reffs > 1){
							$$flag_name = "text-green";
							//$$flag_name = "<img src=\"".$GLOBALS["web_root"]."/images/flag_green_".$flag_convert[$temp_name_index].".gif\" align=\"bottom\" alt=\"".ucfirst($temp_name_index)." Referrals\" border=\"0\" style=\"cursor:hand\" tbl=\"".substr($temp_name_index, 0, 3)."\">";
						}else{
							$$flag_name = "text-red";
							//$$flag_name = "<img src=\"".$GLOBALS["web_root"]."/images/flag_red_".$flag_convert[$temp_name_index].".gif\" align=\"bottom\" alt=\"".ucfirst($temp_name_index)." Referrals\" border=\"0\" style=\"cursor:hand\" tbl=\"".substr($temp_name_index, 0, 3)."\">";
						}						
					}				
				}
			}
		}
		return array('pri_ref_flag'=>$Pri_flag, 'sec_ref_flag'=>$Sec_flag, 'ter_ref_flag'=>$Ter_flag);
	}
    
    function check_docs_exists() {
        $docExistClass=' doc_exists ';
        if(intval($_SESSION['logged_user_type'])==1 && isset($_SESSION['patient']) && $_SESSION['patient']!='')
        {
            $qry = "SELECT sdt.scan_doc_id, plt.scan_doc_id as plt_id
                    FROM ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl sdt
                    INNER JOIN ".constant("IMEDIC_SCAN_DB").".folder_categories fc on sdt.folder_categories_id = fc.folder_categories_id
                    LEFT JOIN ".constant("IMEDIC_IDOC").".provider_view_log_tbl plt ON (plt.scan_doc_id = sdt.scan_doc_id AND plt.section_name = 'scan' AND plt.provider_id =  '".$_SESSION['authId']."')

                    WHERE sdt.patient_id='".$_SESSION['patient']."' 
                    AND (task_physician_id = ".$_SESSION['authId']." || task_physician_id = 0  ) 
                    AND fc.alertPhysician = 1
                    AND fc.folder_status = 'active'
                    ORDER BY (sdt.upload_docs_date + sdt.upload_date) DESC,sdt.scan_doc_id DESC";		
            $sql = imw_query($qry);
            $doc_exists=false;
            while($row=imw_fetch_assoc($sql)){
                if($row['scan_doc_id']!=$row['plt_id']) {
                    $doc_exists=true;
                }
            }
            if($doc_exists){
                return $docExistClass;
            }
        }
        return '';
    }	
    
} //END CLASS
?>