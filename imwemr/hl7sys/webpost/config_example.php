<?php
set_time_limit(3000);
error_reporting(0);

//version control
if(!defined("FILE_VERSION_NAME"))
	define("FILE_VERSION_NAME", "athenaV2"); //athenaV2, athena

//error log
if(!defined("ERROR_LOG_PATH"))
	define("ERROR_LOG_PATH","");
if(!defined("ERROR_LOG"))
	define("ERROR_LOG", 1); //1 - on , 0 - off

//response log
if(!defined("RESPONSE_LOG_PATH"))
	define("RESPONSE_LOG_PATH","response/");
if(!defined("RESPONSE_LOG"))
	define("RESPONSE_LOG", 1); //1 - on , 0 - off

//posts log
if(!defined("ATHENA_POSTS_PATH"))
	define("ATHENA_POSTS_PATH","");
if(!defined("POSTS_LOG"))
	define("POSTS_LOG", 1); //1 - on , 0 - off

//Debug mode
if(!defined("DEBUG_MODE"))
	define("DEBUG_MODE", 0); //1 - on , 0 - off

//username and password
if(!defined("ATHENA_USERNAME"))
	define("ATHENA_USERNAME","");
if(!defined("ATHENA_PASSWORD"))
	define("ATHENA_PASSWORD","");

//default insurance case type
if(!defined("HL7_DEFAULT_INSURANCE_CASE_TYPE"))
	define("HL7_DEFAULT_INSURANCE_CASE_TYPE","1");
if(!defined("HL7_DEFAULT_RESCHEDULE_STATUS_ID"))
	define("HL7_DEFAULT_RESCHEDULE_STATUS_ID","202");
if(!defined("HL7_DEFAULT_CHECKIN_STATUS_ID"))
	define("HL7_DEFAULT_CHECKIN_STATUS_ID","13");
if(!defined("HL7_DEFAULT_CHECKOUT_STATUS_ID"))
	define("HL7_DEFAULT_CHECKOUT_STATUS_ID","11");
if(!defined("HL7_DEFAULT_CANCEL_STATUS_ID"))
	define("HL7_DEFAULT_CANCEL_STATUS_ID","18");

//xml envelope
if(!defined("XML_HEADER"))
	define("XML_HEADER","<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><response>");
if(!defined("XML_FOOTER"))
	define("XML_FOOTER","</response>");
?>