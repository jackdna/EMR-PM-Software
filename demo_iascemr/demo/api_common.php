<?php
header('Access-Control-Allow-Origin: *');
include_once("../common/conDb.php");

$httpHost="https://demo.imwemr.com";
if($_SERVER["HTTP_HOST"]=="192.168.18.106") {
	$httpHost="http://192.168.18.106";	
}
//$url_new = $protocol.$_SERVER["HTTP_HOST"]."/".$surgeryCenterDirectoryName."/demo/api_demo.php";
$url_new = $httpHost."/".$surgeryCenterDirectoryName."/demo/api_demo.php";
?>
<script type="text/javascript" src="../js/jquery_1.7.1.js"></script>
