<?php
error_reporting(1);
ini_set("display_errors",1);

date_default_timezone_set('America/New_York');

//This is the idle logout function:
$GLOBALS['session_timeout'] = 1800;		//default value
ini_set("session.gc_maxlifetime", "21600");
$GLOBALS['WEB_PROTOCOL'] 	= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) == 'on' ? 'https://' : 'http://';

$host					= 'demo.myimw.com';

//Inventory application DIR path and WEB path
$GLOBALS['DIR_PATH']		= "/var/www/html/demo_optical";
$GLOBALS['WEB_PATH']		= $GLOBALS['WEB_PROTOCOL'].$host."/optical";

//imwemr application DIR path, WEB path and DB name
$GLOBALS['IMW_DIR_PATH']	= "/var/www/html/imwemr";
$GLOBALS['IMW_WEB_PATH']	= $GLOBALS['WEB_PROTOCOL'].$host;
$GLOBALS['IMW_WEB_LOGIN_PATH']  = $GLOBALS['IMW_WEB_PATH']."/interface/login/index.php";
$GLOBALS['IMW_DB_NAME']		= "demo_imw";
$sqlconf = array();
$sqlconf["host"]= 'localhost';
$sqlconf["port"] = '3306';
$sqlconf["login"] = 'imw';
$sqlconf["pass"] = 'P@ssw0rd1';

//holds sub domain name or server name
$GLOBALS['SUB_DOMAIN']          ="demo";

//Naming sessiong and starting session.
session_name('idoc_inventory');
session_start();

//INVENTORY MYSQL CONNECTIONS.
include($GLOBALS['DIR_PATH']."/config/sql_conf.php");

if((!isset($_SESSION['authId']) || intval($_SESSION['authId'])==0 || $_SESSION['authId']=='') && (!isset($ignoreAuth) || !$ignoreAuth)){
	die('<script type="text/javascript">window.top.location.href="'.$GLOBALS['WEB_PATH'].'/login/";</script>');
}

define("HASH_METHOD","SHA1"); //it can be SHA2 or MD5
define("PASS_EXPIRY_NOTICE_DAYS", 7); // 7 days of password expiry warning.
$GLOBALS["max_recent_search_cache"] = 5;

/*Phone No. Format*/
$GLOBALS['phone_format']='###-###-####';
/*Controle client side caching of js and css files*/
define("cache_version", md5(1));
/*Currency Symbol*/
$GLOBALS['CURRENCY_SYMBOL'] = "$";

/*Item Details for custom Items*/
$GLOBALS['CUSTOM_FRAME'] = array('id'=>'1', 'upc'=>'000001', 'name'=>'Custom Frame');
$GLOBALS['CUSTOM_LENS'] = array('id'=>'2', 'upc'=>'000002', 'name'=>'Custom Lens');
$GLOBALS['CUSTOM_CONTACT_LENS'] = array('id'=>'3', 'upc'=>'000003', 'name'=>'Custom Contact Lens');

/*VisionWeb Control Setting*/
$GLOBALS['connect_visionweb'] = "YES";

/*Server/Practice Name*/
$GLOBALS["LOCAL_SERVER"] = '';

?>