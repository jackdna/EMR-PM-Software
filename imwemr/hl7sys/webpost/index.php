<?php
set_time_limit(0);
set_include_path(dirname(__FILE__).'/../../library');
$ignoreAuth = true;
/*Set Practice Name - for dynamically including config file*/
require_once(dirname(__FILE__)."/../../config/globals.php");
//error_reporting(-1);
//ini_set("display_errors",-1);
require_once "Net/HL7/Message.php";

if(strtolower($GLOBALS["LOCAL_SERVER"])=='urbaneye'){
	require_once(dirname(__FILE__)."/config_urbaneye.php");	
}else if(strtolower($GLOBALS["LOCAL_SERVER"])=='myemrdemo'){
	require_once(dirname(__FILE__)."/config_myemr.php");	
}else if(strtolower($GLOBALS["LOCAL_SERVER"])=='cec_bsc'){
	require_once(dirname(__FILE__)."/config_bsc_asc.php");	
}

require_once(dirname(__FILE__)."/".FILE_VERSION_NAME.".php");

//saving sent data
$objAthena = new athena();
$objAthena->router($query_string['data'], 1);
?>