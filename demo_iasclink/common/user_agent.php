<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
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

function isHtml5OK(){
	$ret=0;
	$strUserAgent = $_SERVER['HTTP_USER_AGENT'];
	if(stristr($strUserAgent, 'Safari') == true) {
		$ret=1;	
	}
	elseif(stristr($strUserAgent, 'MSIE') == true){
		$pos = strpos($strUserAgent, 'MSIE');
		(int)substr($strUserAgent,$pos + 5, 3);
		if((int)substr($strUserAgent,$pos + 5, 3) > 8){
			$ret=1;
		}
	}else if(stristr($_SERVER['HTTP_USER_AGENT'],"rv:11") !== false){ //IE 11
		$ret=1;
	}
	return $ret;
}


?>
