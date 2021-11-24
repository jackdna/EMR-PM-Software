<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$browserPlatform = "iPad";
$browserName = "IE";
if(strpos($user_agent,"Windows")>-1){
	$browserPlatform =  "Windows";
}else if(strpos($user_agent,"iPad")>-1){
	$browserPlatform = "iPad";
}

if(strpos($user_agent,"MSIE")>-1 && strpos($user_agent,"Opera")<0){
	$browserName = "IE";
}else if(strpos($user_agent,"Safari")>-1){
	$browserName = "Safari";
}else if(strpos($user_agent,"Firefox")>-1){
	$browserName = "Firefox";
}else if(strpos($user_agent,"Chrome")>-1){
	$browserName = "Chrome";
}else if(strpos($user_agent,"Opera")>-1){
	$browserName = "Opera";
}
//echo $browserName;
$tmAgent= date('h:i A');
$dtFullAgent = date('m/d/Y h:i:s A');
$dtFullAgentChart = date('m/d/Y h:i:s A');
$dtFullTmAgentChart = date('m-d-Y h:i A');
$dtFullTmDb = date('Y-m-d h:i:s');

if(constant("SHOW_MILITARY_TIME")=="YES") {
	$tmAgent= date('H:i');
	$dtFullAgent = date('m/d/Y H:i:s');
	$dtFullAgentChart = date('m/d/Y H:i:s');
	$dtFullTmAgentChart = date('m-d-Y H:i');
}
$jsServerTimeRequest = $_REQUEST['jsServerTimeRequest'];
if($jsServerTimeRequest == 'yes') { 
	echo $tmAgent;
}else if($jsServerTimeRequest == 'fullDtTime') { 
	echo $dtFullAgent;
}else if($jsServerTimeRequest == 'fullDtTimeChart') { 
	echo $dtFullAgentChart;
}else if($jsServerTimeRequest == 'fullDtTimeReview') { 
	echo $dtFullTmAgentChart."@@".$dtFullTmDb;
}
function browser() {
	$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
	// you can add different browsers with the same way ..
	if(preg_match('/(chromium)[ \/]([\w.]+)/', $ua))
			$browser = 'chromium';
	elseif(preg_match('/(chrome)[ \/]([\w.]+)/', $ua))
			$browser = 'chrome';
	elseif(preg_match('/(safari)[ \/]([\w.]+)/', $ua))
			$browser = 'safari';
	elseif(preg_match('/(opera)[ \/]([\w.]+)/', $ua))
			$browser = 'opera';
	elseif(preg_match('/(msie)[ \/]([\w.]+)/', $ua))
			$browser = 'msie';
	elseif(preg_match('/(mozilla)[ \/]([\w.]+)/', $ua))
			$browser = 'mozilla';

	preg_match('/('.$browser.')[ \/]([\w]+)/', $ua, $version);

	if(preg_match('/(windows nt)[ \/]([\w.]+)/', $ua) && stripos($ua, 'Trident/7.0; rv:11.0') !== false){
		$browser = 'msie';
		$version[2] = "11"; 
	}
	return array($browser,$version[2], 'name'=>$browser,'version'=>$version[2]);
}


?>
