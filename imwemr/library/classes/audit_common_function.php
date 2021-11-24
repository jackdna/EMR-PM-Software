<?php 
function get_os_($user_agent)
{
	$oses = array (
		'Windows 3.11' => 'Win16',
		'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
		'Windows 98' => '(Windows 98)|(Win98)',
		'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
		'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
		'Windows 2003' => '(Windows NT 5.2)',
		'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
		'Windows ME' => 'Windows ME',
		'Open BSD'=>'OpenBSD',
		'Sun OS'=>'SunOS',
		'Linux'=>'(Linux)|(X11)',
		'Macintosh'=>'(Mac_PowerPC)|(Macintosh)',
		'QNX'=>'QNX',
		'BeOS'=>'BeOS',
		'OS/2'=>'OS/2',
		'Search Bot'=>'(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)'
	);
 
	foreach($oses as $os=>$pattern)
	{
		if (preg_match($pattern, $user_agent))
			return $os;
	}
	return 'Unknown';
}
if(!function_exists("w")){
	function w($a = '')
	{
		if (empty($a)) return array();
		
		return explode(' ', $a);
	}
}
if(!function_exists("_browser")){
	function _browser($a_browser = false, $a_version = false, $name = false)
	{
		$browser_list = 'msie firefox konqueror safari netscape navigator opera mosaic lynx amaya omniweb chrome avant camino flock seamonkey aol mozilla gecko';
		$user_browser = strtolower($_SERVER['HTTP_USER_AGENT']);
		$this_version = $this_browser = '';
		
		$browser_limit = strlen($user_browser);
		foreach (w($browser_list) as $row)
		{
			$row = ($a_browser !== false) ? $a_browser : $row;
			$n = stristr($user_browser, $row);
			if (!$n || !empty($this_browser)) continue;
			
			$this_browser = $row;
			$j = strpos($user_browser, $row) + strlen($row) + 1;
			for (; $j <= $browser_limit; $j++)
			{
				$s = trim(substr($user_browser, $j, 1));
				$this_version .= $s;
				
				if ($s === '') break;
			}
		}
		
		if ($a_browser !== false)
		{
			$ret = false;
			if (strtolower($a_browser) == $this_browser)
			{
				$ret = true;
				
				if ($a_version !== false && !empty($this_version))
				{
					$a_sign = explode(' ', $a_version);
					if (version_compare($this_version, $a_sign[1], $a_sign[0]) === false)
					{
						$ret = false;
					}
				}
			}
			
			return $ret;
		}
		
		//
		$this_platform = '';
		if (strpos($user_browser, 'linux'))
		{
			$this_platform = 'linux';
		}
		elseif (strpos($user_browser, 'macintosh') || strpos($user_browser, 'mac platform x'))
		{
			$this_platform = 'mac';
		}
		else if (strpos($user_browser, 'windows') || strpos($user_browser, 'win32'))
		{
			$this_platform = 'windows';
		}
		
		if ($name !== false)
		{
			return $this_browser . ' ' . $this_version;
		}
		
		return array(
			"browser"      => $this_browser,
			"version"      => $this_version,
			"platform"     => $this_platform,
			"useragent"    => $user_browser
		);
	}
}

if(!function_exists("get_mac_add")){
	function get_mac_add(){
		/*
		exec('ipconfig/all',$out);	
		$outMain = explode(":",$out[13]);
		//echo $outMain[1];
		return $outMain[1];
		*/
		##############################
	/*	$ip= $_SERVER['REMOTE_ADDR'];
		exec( 'arp -a', $output );
		
		for($i=0;$i<count($output);$i++){
			if(eregi($ip,$output[$i])){
				$add= strstr($output[$i],$ip);
			}
			
		}
		if(isset($add) && !strstr($add,'0x2')){
				//echo ($add);
				$a=explode(" ",get_nac($add));
				for($i=0;$i<count($a);$i++){
					if($a[$i]!=""){
						if(get_nac($a[$i])!=""){
							//echo "<b class=\"text_10b\">MAC address of your system: </b><br><br><span class=\"text_10\" style=\"font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#0000FF; font-weight:bold;\">".get_nac($a[$i])."</span><br><br>";
							$macs=get_nac($a[$i]);
						}
					}
				}
		}else{
			exec('getmac',$out);
			$a=explode(" ",$out[3]);
			for($i=0;$i<count($a);$i++){
				if($a[$i]!=""){
					if(get_nac($a[$i])!=""){
						//echo "<b class=\"text_10b\">MAC address of your system: </b><br><br><span class=\"text_10\" style=\"font-family:Arial, Helvetica, sans-serif; font-size:11px; color:#0000FF; font-weight:bold;\">".get_nac($a[$i])."</span><br><br>";
						$macs=get_nac($a[$i]);
					}
				}
			}
		}
		if($_COOKIE['macaddrs']==""  || empty($_COOKIE['macaddrs'])){		
			$_SESSION["macaddrs"] = $macs;
			setcookie("macaddrs", $macs,time()+(60*60*24*100),"/");
		}
		return $macs;
		*/
	} 
}

if(!function_exists("get_nac")){
	function get_nac($spLine){
		if (preg_match("/[0-9a-f][0-9a-f][:-]". 
			"[0-9a-f][0-9a-f][:-]". 
			"[0-9a-f][0-9a-f][:-]". 
			"[0-9a-f][0-9a-f][:-]". 
			"[0-9a-f][0-9a-f][:-]". 
			"[0-9a-f][0-9a-f]/i",$spLine)) { 
			return strtoupper($spLine); 
		}		
	}  	
}
//function by Er. Ravi Mantra to get real ip
if(!function_exists("getRealIpAddr")){
	function getRealIpAddr()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
		  $ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
		  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
		  $ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}
//function by Er. Ravi Mantra to get opreater type
if(!function_exists("getOperaterType")){
	function getOperaterType($id){
		$ret = "";
		$sql = "SELECT user_type FROM users WHERE id = '".$id."'  ";
		$rsSql = imw_query($sql);	
		$row = imw_fetch_array($rsSql);
		if($row != false){
			$ret = $row["user_type"];
			switch ($ret):
					case 1:
						$userType = "Physician";
					break;
					case 2:
						$userType = "Nurse";
					break;
					case 3:
						$userType = "Technician";
					break;
					case 4:
						$userType = "Staff";
					break;
					case 5:
						$userType = "Test";
					break;
					case 6:
						$userType = "Surgical Coordinator";
					break;
					case 7:
						$userType = "PA";
					break;
					case 8:
						$userType = "CNP";
					break;
					case 9:
						$userType = "CRN";
					break;
					case 10:
						$userType = "Consultant";
					break;
					case 11:
						$userType = "Resident Physician";
					break;
					case 12:
						$userType = "Attending Physician";
					break;
			endswitch;
		}
		return $userType;
	}
}
//function by Er. Ravi Mantra to database field type
if(!function_exists("funGetFieldType")){
	function funGetFieldType($dataFieldArray,$field){
		if($dataFieldArray[0] != ''){
			foreach($dataFieldArray as $key => $value){
				if($dataFieldArray [$key]["DB_Field_Name"] == trim($field)){			
					 return $dataFieldArray [$key]["DB_Field_Type"];			
				}
			}
		}
	}
}

if(!function_exists("makeFieldTypeArray")){
	function makeFieldTypeArray($strQry){
		$rsStrQry = imw_query($strQry);
		if(!$rsStrQry){
			echo ("Error : ".imw_error());
		}
		$totDataFields = imw_num_fields($rsStrQry);
		$dataFields = array(); 
		for ($i=0; $i < $totDataFields; $i++) {
			$type  = imw_field_type($rsStrQry, $i);
			$name  = imw_field_name($rsStrQry, $i);	
			$dataFields[] = array(
										"DB_Field_Name"=> $name,
										"DB_Field_Type"=> $type			
										);	
		}
		if (count($dataFields)>0){
			return $dataFields;
		}
		else{
			return imw_errno();
		}
	}
}


//function by Er. Ravi Mantra for making unique 2 Dimensional array
if(!function_exists("makeUnique")){
	function makeUnique($array){
        $dupes=array();
		foreach($array as $values){
            if(!in_array($values,$dupes))
				$dupes[]=$values;
		}
		return $dupes;
	}
}
//function by Er. Ravi Mantra to merge array
if(!function_exists("mergingArray")){
	function mergingArray($table,$error){
		$mergedArray = array();
		if(count($table) == count($error)){
			for($a=0; $a < count($table); $a++){
				$mergedArray[] = array(
										"Table_Name"=> trim($table[$a]),
										"Error"=> trim($error[$a])
									  );
			}
			return $mergedArray;		
		}
	}
}

if(!function_exists("auditViewArray")){
	function auditViewArray($arrAuditTrail){
		$arrTable = array();
		foreach ($arrAuditTrail as $key => $value) {
			if(
				(!empty($arrAuditTrail [$key]["Table_Name"]) && $arrAuditTrail [$key]["Table_Name"]!="")
				&&
				(!empty($arrAuditTrail [$key]["Pk_Id"]) && $arrAuditTrail [$key]["Pk_Id"]!="")
			  ){
					$arrTable[] = array(
										"key" => $arrAuditTrail [$key]["Pk_Id"],
										"value" =>$arrAuditTrail [$key]["Table_Name"]
										);		
			   }
		}
		return $arrTable;
	}
}

if(!function_exists("createAuditTable")){
	function createAuditTable($nextTableDay,$crtTimeStamp=0){
		if($crtTimeStamp==0){
			$curtime = mktime(0, 0 , 0, date("m"), date("d"), date("Y"));
		}
		else{
			$curtime = $crtTimeStamp;
		}
		$qryCreateTable	= "CREATE TABLE `audit_trail_".$curtime."` (
							  `id` int(11) NOT NULL AUTO_INCREMENT,
							  `Pk_Id` int(11) NOT NULL,
							  `Table_Name` varchar(255) NOT NULL,
							  `Data_Base_Field_Name` varchar(255) NOT NULL,
							  `Data_Base_Field_Type` varchar(255) NOT NULL,
							  `Field_Label` varchar(255) NOT NULL,
							  `Old_Value` longtext NOT NULL,
							  `New_Value` longtext NOT NULL,
							  `Operater_Id` int(11) NOT NULL,
							  `Operater_Type` varchar(255) NOT NULL,
							  `IP` varchar(15) NOT NULL,
							  `MAC_Address` varchar(255) NOT NULL,
							  `URL` varchar(255) NOT NULL,
							  `Browser_Type` varchar(255) NOT NULL,
							  `OS` varchar(255) NOT NULL,
							  `Machine_Name` varchar(255) NOT NULL,
							  `Category` varchar(255) NOT NULL,
							  `Category_Desc` varchar(255) NOT NULL,
							  `Action` varchar(50) NOT NULL,
							  `Date_Time` datetime NOT NULL,
							  `Query_Success` int(1) NOT NULL COMMENT '0 = success , 1 = failed',
							  `Query_Failed_Msg` tinytext COMMENT 'if Query_Success is 1',
							  PRIMARY KEY (`id`)
							)";
		$rsCreateTable = imw_query($qryCreateTable);		
		if($crtTimeStamp==0){
			$qry = "insert into audit_trail_master (create_timestamp,create_date) VALUES('".$curtime."',CURDATE())";	
			$rsQry = imw_query($qry);
			$qry = "insert into audit_day (next_table_day,last_created_date) VALUES('".$nextTableDay."',CURDATE())";	
			$rsQry = imw_query($qry);
		}	
		return "audit_trail_".$curtime;		
	}
}

if(!function_exists("auditTrail")){
	function auditTrail($arrAuditTrail,$tableError,$optionalPK_ID = 0,$debug = 0,$debugArray = 0){
		/*$totNumRowATM = 0;
		$qryAuditDay = "select count(*) as totNumRowAD from audit_day";
		$rsAuditDay = imw_query($qrySelectDays);
		if($rsAuditDay){
			if(imw_num_rows($rsAuditDay)>0){
				extract(imw_fetch_array($rsAuditDay));						
			}
		}
		$qryAuditMaster = "select create_timestamp as crtTimeStamp from audit_trail_master ORDER BY create_date DESC LIMIT 0,1";	
		$rsAuditMaster = imw_query($qryAuditMaster);
		if($rsAuditMaster){
			if(imw_num_rows($rsAuditMaster)>0){
				extract(imw_fetch_array($rsAuditMaster));						
				$totNumRowATM = imw_num_rows($rsAuditMaster);
			}
		}
		if($totNumRowAD == 0 && $totNumRowATM == 0){
			$auditTable = "audit_trail";
		}
		else{
			$qrySelectDays = "select max(next_table_day) as nextTableDay,DATEDIFF(CURDATE(),max(last_created_date)) as laftDays from audit_day";
			$rsSelectDays = imw_query($qrySelectDays);
			if($rsSelectDays){
				if(imw_num_rows($rsSelectDays)>0){
					extract(imw_fetch_array($rsSelectDays));			
					if($laftDays >= $nextTableDay){
						$auditTable = createAuditTable($nextTableDay);
					}
					else{
						$auditTable = "audit_trail_".$crtTimeStamp;	
															
						$rsQry = imw_query("select * from ".$auditTable." LIMIT 0 , 1");
						if(imw_errno() == 1146){
							//$auditTable = "audit_trail";
							$auditTable = createAuditTable($nextTableDay,$crtTimeStamp);
						}
					}
				}
			}
		}
		*/
		$auditTable = "audit_trail";
		$readyToInsert = false;
		$insertQryAuditTrail = "insert into ".$auditTable." ( 
										Pk_Id,Table_Name,Data_Base_Field_Name,Field_Label,Old_Value,
										New_Value,Operater_Id,IP,URL,Category,Category_Desc,Action,
										Date_Time,Query_Success,Query_Failed_Msg,Data_Base_Field_Type,
										Operater_Type,MAC_Address,Browser_Type,OS,Machine_Name,
										Depend_Select,Depend_Table,Depend_Search,Filed_Text,pid
										) VALUES ";
		foreach ((array)$arrAuditTrail as $key => $value) {		
			if($arrAuditTrail [$key]["Pk_Id"]==""){
				$arrAuditTrail [$key]["Pk_Id"] = $optionalPK_ID;
			}
			if($arrAuditTrail [$key]["Pk_Id"]==""){
				$arrAuditTrail [$key]["Pk_Id"] = $arrAuditTrail [$key-1]["Pk_Id"];
			}
			if($arrAuditTrail [$key]["Table_Name"]==""){
				$arrAuditTrail [$key]["Table_Name"] = $arrAuditTrail [$key-1]["Table_Name"];
			}
			if($arrAuditTrail [$key]["Operater_Id"]==""){
				$arrAuditTrail [$key]["Operater_Id"] = $arrAuditTrail [$key-1]["Operater_Id"];
			}
			if($arrAuditTrail [$key]["IP"]==""){
				$arrAuditTrail [$key]["IP"] = $arrAuditTrail [$key-1]["IP"];
			}
			if($arrAuditTrail [$key]["URL"]==""){
				$arrAuditTrail [$key]["URL"] = $arrAuditTrail [$key-1]["URL"];
			}
			if($arrAuditTrail [$key]["Category"]==""){
				$arrAuditTrail [$key]["Category"] = $arrAuditTrail [$key-1]["Category"];
			}
			if($arrAuditTrail [$key]["Category_Desc"]==""){
				$arrAuditTrail [$key]["Category_Desc"] = $arrAuditTrail [$key-1]["Category_Desc"];
			}
			if($arrAuditTrail [$key]["Action"]==""){
				$arrAuditTrail [$key]["Action"] = $arrAuditTrail [$key-1]["Action"];
			}
			if($arrAuditTrail [$key]["Operater_Type"]==""){
				$arrAuditTrail [$key]["Operater_Type"] = $arrAuditTrail [$key-1]["Operater_Type"];
			}
			if($arrAuditTrail [$key]["MAC_Address"]==""){
				$arrAuditTrail [$key]["MAC_Address"] = $arrAuditTrail [$key-1]["MAC_Address"];
			}
			if($arrAuditTrail [$key]["Browser_Type"]==""){
				$arrAuditTrail [$key]["Browser_Type"] = $arrAuditTrail [$key-1]["Browser_Type"];
			}
			if($arrAuditTrail [$key]["OS"]==""){
				$arrAuditTrail [$key]["OS"] = $arrAuditTrail [$key-1]["OS"];
			}
			if($arrAuditTrail [$key]["Machine_Name"]==""){
				$arrAuditTrail [$key]["Machine_Name"] = $arrAuditTrail [$key-1]["Machine_Name"];
			}
			if($arrAuditTrail [$key]["pid"]==""){
				$arrAuditTrail [$key]["pid"] = $arrAuditTrail [$key-1]["pid"];
			}
			if(trim($arrAuditTrail [$key]["New_Value"])==""){			
				if($arrAuditTrail [$key]["Filed_Label"]=="pro_type"){
					$type_new_value=explode('--',$_REQUEST[$arrAuditTrail [$key]["Filed_Label"]]);
					$arrAuditTrail [$key]["New_Value"] = trim(addcslashes(addslashes($type_new_value[0]),"\0..\37!@\177..\377"));			
				}else if($arrAuditTrail [$key]["Filed_Label"]=="race" || $arrAuditTrail [$key]["Filed_Label"]=="ethnicity"){
					$type_new_value_imp=implode(',',$_REQUEST[$arrAuditTrail [$key]["Filed_Label"]]);
					$arrAuditTrail [$key]["New_Value"] = trim(addcslashes(addslashes($type_new_value_imp),"\0..\37!@\177..\377"));			
				}
				else if($arrAuditTrail [$key]["Filed_Label"]=="account_status"){
					$new_val = trim(addcslashes(addslashes($_REQUEST[$arrAuditTrail [$key]["Filed_Label"]]),"\0..\37!@\177..\377"));
					$arrAuditTrail [$key]["New_Value"] = ($new_val == "!") ? 0 : $new_val;
				}
				else{
					$arrAuditTrail [$key]["New_Value"] = trim(addcslashes(addslashes($_REQUEST[$arrAuditTrail [$key]["Filed_Label"]]),"\0..\37!@\177..\377"));			
				}
			}
			foreach ((array)$tableError as $tableErrorKey => $tableErrorValue) {
				if($arrAuditTrail [$key]["Table_Name"] == $tableError [$tableErrorKey]["Table_Name"]){
					if(!empty($tableError [$tableErrorKey]["Error"])){
						$arrAuditTrail [$key]["Query_Success"]  = "1";
						$arrAuditTrail [$key]["Query_Failed_Msg"]  = addcslashes(addslashes($tableError [$tableErrorKey]["Error"]),"\0..\37!@\177..\377");
					}
				}
			}	
			$action = false;
			
			switch (trim($arrAuditTrail [$key]["Action"])):
				case "update":
					if(trim(strtolower($arrAuditTrail [$key]["Old_Value"])) != trim(strtolower($arrAuditTrail [$key]["New_Value"]))){
						$action = true;
						$readyToInsert = true;
					}
					elseif(is_int(trim($arrAuditTrail [$key]["Old_Value"]) == true) && is_int(trim($arrAuditTrail [$key]["New_Value"]) == true)){
						$oldValue = (int)trim($arrAuditTrail [$key]["Old_Value"]);
						$newValue = (int)trim($arrAuditTrail [$key]["New_Value"]);
						if($oldValue != $newValue){
							$action = true;
							$readyToInsert = true;
						}
					}
					break;
				case "add":
					if(trim($arrAuditTrail [$key]["New_Value"])!=""){								
						$action = true;
						$readyToInsert = true;
					}
					break;
				case "delete":
					$action = true;
					$readyToInsert = true;
					break;
				case "view":
					$action = true;
					$readyToInsert = true;
					break;
				case "app_start":
					$action = true;
					$readyToInsert = true;
					break;
				case "user_login_s":
					$action = true;
					$readyToInsert = true;
					break;	
				case "user_logout_s":
					$action = true;
					$readyToInsert = true;
					break;		
	
				case "app_stop":
					$action = true;
					$readyToInsert = true;
					break;	
				case "user_login_f":
					$action = true;
					$readyToInsert = true;
					break;		
				case "user_locked":
					$action = true;
					$readyToInsert = true;
					break;	
				case "user_session_timeout_s":
					$action = true;
					$readyToInsert = true;
					break;		
				case "query_search":
					$action = true;
					$readyToInsert = true;
					break;					
				case "phi_export":
					$action = true;
					$readyToInsert = true;
					break;		
				case "sig_create":
					$action = true;
					$readyToInsert = true;
					break;										
			endswitch;
	
	
			/*if(trim($arrAuditTrail [$key]["Action"]) == "update"){
				if(trim(strtolower($arrAuditTrail [$key]["Old_Value"])) != trim(strtolower($arrAuditTrail [$key]["New_Value"]))){
					$action = true;
					$readyToInsert = true;
				}
				elseif(is_int(trim($arrAuditTrail [$key]["Old_Value"]) == true) && is_int(trim($arrAuditTrail [$key]["New_Value"]) == true)){
					$oldValue = (int)trim($arrAuditTrail [$key]["Old_Value"]);
					$newValue = (int)trim($arrAuditTrail [$key]["New_Value"]);
					if($oldValue != $newValue){
						$action = true;
						$readyToInsert = true;
					}
				}
			}
			elseif(trim($arrAuditTrail [$key]["Action"]) == "add"){
				if(trim($arrAuditTrail [$key]["New_Value"])!=""){			
					//$arrAuditTrail [$key]["New_Value"] = trim(addcslashes(addslashes($_REQUEST[$arrAuditTrail [$key]["Filed_Label"]]),"\0..\37!@\177..\377"));			
					//$arrAuditTrail [$key]["New_Value"] = $_REQUEST[$arrAuditTrail [$key]["Filed_Label"]];			
					$action = true;
					$readyToInsert = true;
				}
			}
			elseif(trim($arrAuditTrail [$key]["Action"]) == "delete"){
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrAuditTrail [$key]["Action"]) == "view"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrAuditTrail [$key]["Action"]) == "app_start"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrAuditTrail [$key]["Action"]) == "user_login_s"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrAuditTrail [$key]["Action"]) == "user_logout_s"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrAuditTrail [$key]["Action"]) == "app_stop"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrAuditTrail [$key]["Action"]) == "user_login_f"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrAuditTrail [$key]["Action"]) == "user_locked"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrAuditTrail [$key]["Action"]) == "user_session_timeout_s"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrAuditTrail [$key]["Action"]) == "query_search"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrAuditTrail [$key]["Action"]) == "phi_export"){			
				$action = true;
				$readyToInsert = true;
			}
			elseif(trim($arrAuditTrail [$key]["Action"]) == "sig_create"){			
				$action = true;
				$readyToInsert = true;
			}*/
			$actionAddUpdt=$arrAuditTrail [$key]["Action"];
			if(trim($arrAuditTrail [$key]["Old_Value"]=="") && trim($arrAuditTrail [$key]["New_Value"])!="" && $arrAuditTrail [$key]["Action"]=="update") {
				$actionAddUpdt = "add";	
			}
			if(trim(strtolower($arrAuditTrail [$key]["New_Value"]))=="other" || (trim($arrAuditTrail [$key]["Old_Value"])=="00-00-0000" && (trim($arrAuditTrail [$key]["New_Value"])=="")|| trim($arrAuditTrail [$key]["New_Value"])=="--")){								
				$action = false;
				$readyToInsert = false;
			}
			if($actionAddUpdt == "add") {
				$arrAuditTrail [$key]["Old_Value"]="";
			}
			
			$php_date = date("Y-m-d H:i:s");
			
			if($action == true){
				/* Fixed provider login/Logout audit issue */
				if( ($arrAuditTrail [$key]["New_Value"] !== "" && $arrAuditTrail [$key]["New_Value"] !== 0 && $arrAuditTrail [$key]["New_Value"] !== "0000-00-00") 
                                        || ($arrAuditTrail[$key]["Category"] == "login_module" || $arrAuditTrail[$key]["Category"] == "logout_module") ){
					$insertQryAuditTrail .= "(
											 '".$arrAuditTrail [$key]["Pk_Id"]."',
											 '".$arrAuditTrail [$key]["Table_Name"]."',
											 '".$arrAuditTrail [$key]["Data_Base_Field_Name"]."',
											 '".$arrAuditTrail [$key]["Filed_Label"]."',
											 '".$arrAuditTrail [$key]["Old_Value"]."',
											 '".$arrAuditTrail [$key]["New_Value"]."',
											 '".$arrAuditTrail [$key]["Operater_Id"]."',
											 '".$arrAuditTrail [$key]["IP"]."',
											 '".$arrAuditTrail [$key]["URL"]."',
											 '".$arrAuditTrail [$key]["Category"]."',
											 '".$arrAuditTrail [$key]["Category_Desc"]."',
											 '".$actionAddUpdt."',
											 '".$php_date."',
											 '".$arrAuditTrail [$key]["Query_Success"]."',
											 '".$arrAuditTrail [$key]["Query_Failed_Msg"]."',
											 '".$arrAuditTrail [$key]["Data_Base_Field_Type"]."',
											 '".$arrAuditTrail [$key]["Operater_Type"]."',
											 '".$arrAuditTrail [$key]["MAC_Address"]."',
											 '".$arrAuditTrail [$key]["Browser_Type"]."',
											 '".$arrAuditTrail [$key]["OS"]."',
											 '".$arrAuditTrail [$key]["Machine_Name"]."',
											 '".trim(addslashes($arrAuditTrail [$key]["Depend_Select"]))."',
											 '".trim(addslashes($arrAuditTrail [$key]["Depend_Table"]))."',
											 '".trim(addslashes($arrAuditTrail [$key]["Depend_Search"]))."',
											 '".trim(addslashes($arrAuditTrail [$key]["Filed_Text"]))."',
											 '".trim(addslashes($arrAuditTrail [$key]["pid"]))."'
										 ),								
										";
				}									
			}
			
		}
		if($debug == 0){
			if($readyToInsert == true){	
				$insertQryAuditTrail = substr(trim($insertQryAuditTrail), 0, -1);  									
				$rsInsertQryAuditTrail = imw_query($insertQryAuditTrail);			
			}
		}
		elseif($debug == 1){
			if($debugArray == 1){
				echo '<pre>';
				print_r($arrAuditTrail);	
			}	
			if($readyToInsert == true){	
				$insertQryAuditTrail = substr(trim($insertQryAuditTrail), 0, -1);  
				echo $insertQryAuditTrail;				
			}
			die("<br>Debug Status");
		}
		//echo '<br><br>'.$insertQryAuditTrail;die();
	}
}
//function by Er. Ravi Mantra for checking date and return date in mm-dd-yyyy
if(!function_exists("isDate")){
	function isDate($i_sDate){	
		if(preg_match ("/^([0-9]{4}-[0-9]{2}-[0-9]{2})$/", $i_sDate)){		
			//ereg ("^[0-9]{2}/[0-9]{2}/[0-9]{4}$", $i_sDate)	    
			$arrDate = explode("-", $i_sDate); 		
			$intYear = $arrDate[0]; 
			$intMonth = $arrDate[1];
			$intDay = $arrDate[2];		
			$intIsDate = checkdate($intMonth, $intDay, $intYear);
			if($intIsDate){
				$date= date(phpDateFormat(), mktime(0,0,0, $intMonth, $intDay, $intYear));
			}
			return ($date);
		}
		else{
			return $i_sDate;
		}  	
	   
	} //end function isDate
}
if(!function_exists("getOrignalValComa")){
	function getOrignalValComa($Old_Value,$New_Value,$arrMEDHXGenHealth,$Field_Label){
		$OldValueOrignal = "";
		$arrOldVal = explode(',',$Old_Value);										
		foreach ($arrOldVal as $key => $value) {
			if ($arrOldVal[$key] == "") {unset($arrOldVal[$key]);}
		}
		foreach ($arrMEDHXGenHealth as $key => $value) {
			if($arrMEDHXGenHealth[$key]['Filed_Label'] == $Field_Label){
				//echo $arrMEDHXOculer[$key]['Filed_Label_Og_Val'].'--'.$Field_Label.'<br>';												
				foreach ($arrOldVal as $keyOld => $valueOld) {
					if ($arrOldVal[$keyOld] == $arrMEDHXGenHealth[$key]['Filed_Label_Val']){														
						$OldValueOrignal .= $arrMEDHXGenHealth[$key]['Filed_Label_Og_Val']."<br>";														
						unset($arrOldVal[$keyOld]);																																							
						break;
					}
				}																								
			}
		}
		$Old_Value = $OldValueOrignal;
		
		$newValueOrignal = "";
		$arrNewVal = explode(',',$New_Value);										
		foreach ($arrNewVal as $key => $value) {
			if ($arrNewVal[$key] == "") {unset($arrNewVal[$key]);}
		}										
		foreach ($arrMEDHXGenHealth as $key => $value) {
			if($arrMEDHXGenHealth[$key]['Filed_Label'] == $Field_Label){
				//echo $arrMEDHXOculer[$key]['Filed_Label_Og_Val'].'--'.$Field_Label.'<br>';												
				foreach ($arrNewVal as $keyNew => $valueNew) {
					if ($arrNewVal[$keyNew] == $arrMEDHXGenHealth[$key]['Filed_Label_Val']){														
						$newValueOrignal .= $arrMEDHXGenHealth[$key]['Filed_Label_Og_Val']."<br>";														
						unset($arrNewVal[$keyNew]);																																							
						break;
					}
				}																								
			}
		}										
		$New_Value = $newValueOrignal;
		return $Old_Value."~~~~".$New_Value;
	}
}

if(!function_exists("getOrignalValWt2Sep")){
	function getOrignalValWt2Sep($Old_Value,$New_Value,$arrMEDHXOculer,$Field_Label){
		$arrTmp = array();								
		$OldValueOrignal = "";
		$strSep="~!!~~";
		$strSep2=":*:";
		$arrOldVal = explode($strSep, $Old_Value);
		foreach($arrOldVal as $keyOld => $valueOld){
			$arrTmp[] = explode($strSep2,$valueOld);											
		}										
		foreach ($arrTmp as $key => $value) {											
			foreach ($value as $keyInner => $valueInner) {																					
				if ($value[$keyInner] == "") {unset($arrTmp[$key]);}																							
			}																					
		}	
		//echo '<pre>';										
		//print_r($arrMEDHXOculer);
		//print_r($arrTmp);
		//die;	
		$OldValueOrignal.='<table style="width:365px;" cellpadding="0" cellspacing="0">';								
		foreach ($arrMEDHXOculer as $key => $value) {
			if($arrMEDHXOculer[$key]['Filed_Label'] == $Field_Label){												
				foreach ($arrTmp as $keyOld => $valueOld) {
					foreach ($valueOld as $keyInner => $valueInner) {																					
						if ($valueOld[$keyInner] == $arrMEDHXOculer[$key]['Filed_Label_Val']){																												
							$OldValueOrignal .= "<tr><td style='width:140px;' class='bdrbtm'>".$arrMEDHXOculer[$key]['Filed_Label_Og_Val']."</td><td style='width:224px;' class='bdrbtm bdrright'>".$valueOld[$keyInner+1]."</td></tr>";											
							unset($arrTmp[$keyOld]);																																							
							break;
						} 																							
					}													
				}																								
			}
		}
		$OldValueOrignal.='</table>';
		//echo $OldValueOrignal;
		//die;	
		$Old_Value = $OldValueOrignal;
		
		$newValueOrignal = "";
		$arrTmp = array();																		
		$strSep="~!!~~";
		$strSep2=":*:";
		$arrOldVal = explode($strSep, $New_Value);
		foreach($arrOldVal as $keyOld => $valueOld){
			$arrTmp[] = explode($strSep2,$valueOld);											
		}										
		foreach ($arrTmp as $key => $value) {											
			foreach ($value as $keyInner => $valueInner) {																					
				if ($value[$keyInner] == "") {unset($arrTmp[$key]);}																							
			}																					
		}	
		//echo '<pre>';										
		//print_r($arrMEDHXOculer);
		//print_r($arrTmp);
		//die;									
		foreach ($arrMEDHXOculer as $key => $value) {
			if($arrMEDHXOculer[$key]['Filed_Label'] == $Field_Label){												
				foreach ($arrTmp as $keyOld => $valueOld) {
					foreach ($valueOld as $keyInner => $valueInner) {																					
						if ($valueOld[$keyInner] == $arrMEDHXOculer[$key]['Filed_Label_Val']){																												
							$newValueOrignal .= $arrMEDHXOculer[$key]['Filed_Label_Og_Val']."&nbsp;".$valueOld[$keyInner+1]."<br>";											
							unset($arrTmp[$keyOld]);																																							
							break;
						} 																							
					}													
				}																								
			}
		}
		//echo $OldValueOrignal;
		//die;	
		$New_Value = $newValueOrignal;
		return $Old_Value."~~~~".$New_Value;
	}
}
?>