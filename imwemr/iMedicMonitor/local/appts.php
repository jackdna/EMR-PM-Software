<?php
include_once("../globals.php");
include_once("common_functions.php");
$tz_value = trim($_GET['local_tz']);
//GETTING MASTER DATA
$url = $external_uri_path."remote_appt_data.php?local_tz=".urlencode($tz_value);
if(function_exists('curl_version')){
	$cur = curl_init();
	curl_setopt($cur, CURLOPT_URL, $url);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
	$data = curl_exec($cur);
	curl_close($cur);
}else{
	$data = file_get_contents($url);
}
$fp = fopen($checked_in_file_name, "w+");
fwrite($fp, $data);
fclose($fp);

echo $checked_in_file_content = file_get_contents($checked_in_file_name);
?>