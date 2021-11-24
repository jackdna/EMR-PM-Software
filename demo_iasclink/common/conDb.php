<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
date_default_timezone_set("America/New_York");
//START PHP 7 ENVIRONMENT VARIABLES
$asc_db_name = 'demo_iascemr';
$asc_host = 'localhost';
$asc_port = '3306';
$asc_login = 'imw';
$asc_pass = 'P@ssw0rd1';
$sqlconf = array("host"=>$asc_host, "login"=>$asc_login, "pass"=>$asc_pass, "ascdb"=>$asc_db_name, "port"=>$asc_port);
include_once('db.php');
$link = imw_connect($asc_host, $asc_login, $asc_pass,$asc_db_name,$asc_port) or die('Could not connect: ' . imw_error());
$GLOBALS['dbh'] = $link;
imw_select_db($asc_db_name,$link) or die(imw_error().'Could not select database');
//END PHP 7 ENVIRONMENT VARIABLES

$imwSwitchFile = "sync_imwemr.php";
$surgeryCenterDirectoryName='demo_iascemr';
$iolinkDirectoryName='demo_iasclink';
$imwDirectoryName='imwemr';
$imwPracticeName='demo';
$constantImwSlotMinute='10';
define('PRODUCT_VERSION_DATE', 'Ver R5.2  Apr 8, 2019');
define("CHECKLIST_DATE",'2012-01-01');
define('CHECK_USER_NPI', 'YES'); //Possible value "YES" OR "" if YES then match surgeon based on NPI while displaying appointment

//START VARIABLES FOR SUB-DOMAIN ENVIRONMENT
$surgeryCenterWebrootDirectoryName='iascemr';
$iolinkWebrootDirectoryName='iasclink';
$_SERVER['HTTPS'] = 'off';
$_SERVER['DOCUMENT_ROOT'] = str_replace('/'.$imwDirectoryName,'',$_SERVER['DOCUMENT_ROOT']);
//END VARIABLES FOR SUB-DOMAIN ENVIRONMENT

?>