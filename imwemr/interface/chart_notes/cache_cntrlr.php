<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
/*
File: cache_cntrlr.php
Purpose: This file provides js/css data in work view .
Access Type : Direct
*/
?>
<?php
require_once(dirname(__FILE__).'/../../config/globals.php');
require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
$ocache = new Assets();
$ar = $ocache->main();

//
$cache_file = $ar[0];$flg_create=$ar[1];$hdr_con_type=$ar[2];
$content = file_get_contents($cache_file);
$time_cache_file = filemtime($cache_file);

$ExpireTime = 3600*10;
if($hdr_con_type=="css"){
header('Content-type: text/css;');
}else if($hdr_con_type=="js"){
header('Content-type: text/javascript;');
}
header("Last-Modified: ".gmdate("D, d M Y H:i:s", $time_cache_file)." GMT");
header('Cache-Control: max-age=' . $ExpireTime); // must-revalidate
header('Expires: '.gmdate('D, d M Y H:i:s', time()+$ExpireTime).' GMT');
$etag = md5_file($cache_file); 
header("Etag: ".$etag);

if((isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])==$time_cache_file) && 
    (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) && empty($flg_create)){  	
	header('HTTP/1.1 304 Not Modified');
	//header('Connection: close');
	exit();
}
else {
	header('HTTP/1.1 200 OK');
}

ob_end_clean();
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){ob_start("ob_gzhandler");}else{ob_start();}
echo $content;
ob_end_flush();
?>