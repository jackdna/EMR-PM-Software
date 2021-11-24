<?php
include_once("../globals.php");
include_once("common_functions.php");

//GETTING MASTER DATA
$cur = curl_init();
$url = $external_uri_path."remote_current_status.php";
curl_setopt($cur, CURLOPT_URL, $url);
curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
$data = curl_exec($cur);
curl_close($cur);

$fp = fopen($current_file_name, "w+");
fwrite($fp, $data);
fclose($fp);

echo $current_file_name = file_get_contents($current_file_name);
?>