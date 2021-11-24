<?php
//Config --
$zsso_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$zsso_imwDirectoryName = "" ;

/* On SP advice */
if(!isset($imwPracticeName) || empty($imwPracticeName)){ 
	$imwPracticeName = $imwPracName;
}

if(isset($imwPracticeName) && !empty($imwPracticeName)){ 
	$zsso_imwDirectoryName = $imwPracticeName;
	if(strpos($zsso_link, $zsso_imwDirectoryName)!==false){ $zsso_imwDirectoryName = "" ; }	
}
/*
	URL : where sso token is to verified.
*/
$GLOBALS["iasclink"]["sso_url_verify"] = $zsso_link."/".$zsso_imwDirectoryName."/interface/sso/sso_login.php";

/*
	URL: script to which username and password will be supplied when a user will login
*/
$iolinkDirectoryName = (isset($iolinkWebrootDirectoryName) && !empty($iolinkWebrootDirectoryName)) ? $iolinkWebrootDirectoryName : $iolinkDirectoryName;
$GLOBALS["iasclink"]["sso_path_login"] = $zsso_link."/".$iolinkDirectoryName."/index.php";

$GLOBALS["iasclink"]["token_life_seconds"] = '60';

/*
	Array : provide essential information regarding users table in 
		'tbl' : name of table where users information is saved.
		'sso_key' : Field name for sso_key in above table		
		'active' : pharse for active records in format " delete_fieldname='0' " e.g " delete_status='0' ".
*/
$GLOBALS["iasclink"]["dbinfo"] = array('tbl'=>"users", 'sso_key'=>"sso_identifier", "active" => "  deleteStatus = ''  ");

/*
	Array : login parameter names which will be provided in $_POST with  db  field name in db table in following format. first is variable name in post, db field name in db
		"0" => array('myusername', 'username'),
		"1" => array('mypass', 'pwd'),
		"2" => array('mypass', 'pwd', 'db_table', 'wherecluse'),
		
		* if dbname is not provided, default db table is table defined in dbinfo->tbl
		
*/
$GLOBALS["iasclink"]["login_post_vars"] = array(
				 "0" => array('userName', 'loginName'),
				 "1" => array('password', 'user_password'),
				 "2" => array('iolink_facility_id','fac_id','facility_tbl'," fac_del_by='0' AND fac_head_quater = '1' ")

			);
			
$GLOBALS["iasclink"]["db_name"] = $asc_db_name;

//Config --

//Mysql Connection --
global $sqlconf;
$sqlconf = array();
$sqlconf["host"]= $asc_host;  //'localhost';
$sqlconf["port"] = $asc_port; // '3306';
$sqlconf["login"] = $asc_login; // 'root';
$sqlconf["pass"] = $asc_pass; // '';

//--

?>