<?php
header('Access-Control-Allow-Origin: *');
include_once("../common/conDb.php");

$httpHost="https://eclimedicware.com";
if($_SERVER["HTTP_HOST"]=="192.168.18.106") {
	$httpHost="http://192.168.18.106";	
}
//$url_new = $protocol.$_SERVER["HTTP_HOST"]."/".$surgeryCenterDirectoryName."/demo/api_demo.php";
$url_new = $httpHost."/".$iolinkDirectoryName."/sync_api/pdf_endpoint.php";
?>
<script type="text/javascript" src="../js/jquery-1.9.0.min.js"></script>
