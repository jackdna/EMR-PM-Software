<?php
//authentication
//require_once($GLOBALS['srcdir']."/../interface/common/audit_common_function.php");

include_once(dirname(__FILE__)."/db.php");
include_once(dirname(__FILE__)."/../../config/globals.php");
$embedded_call = true;
require_once(dirname(__FILE__)."/class.security.php");
$ObjSecurity	= new security();
$ObjSecurity->app_auth_user();
?>