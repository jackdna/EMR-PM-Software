<?php


$ignoreAuth = true;
include dirname(__FILE__)."/../config/globals.php";
$master_file_name = "../cache/".date("Ymd").session_id()."_master.xml";
$checked_in_file_name = "../cache/".date("Ymd").session_id()."_checked_in.xml";
$current_file_name = "../cache/".date("Ymd").session_id()."_current.xml";

$external_uri_path = $phpHTTPProtocol.$phpServerIP.$phpServerPort.$web_root."/iMedicMonitor/remote/";

?>
