<?php
include_once("../globals.php");
include_once("common_functions.php");

$sch_id = isset($_REQUEST['sch_id']) ? intval($_REQUEST['sch_id']) : 0;

if($sch_id >0){
	//GETTING MASTER DATA
	$cur = curl_init();
	echo $url = $external_uri_path."remote_ready_for_dr.php?sch_id=".$sch_id;
	curl_setopt($cur, CURLOPT_URL, $url);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
 	$data = curl_exec($cur);
	curl_close($cur);
}
?>