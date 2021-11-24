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
/*
File: js_scheduler.php
Purpose: Scheduler supporting functions
Access Type: Included
*/
set_time_limit(100); 
$webroot='../..';
$recreate_cache = 0;
$cache_js_file = $webroot."/cache/cache_week_scheduler_js.js";
$arr_includeFiles = array();

$arr_includeFiles[] = $webroot.'/library/js/jquery.min.1.12.4.js';
$arr_includeFiles[] = $webroot.'/library/js/jquery-ui.min.js';
$arr_includeFiles[] = $webroot.'/library/js/bootstrap.min.js';
$arr_includeFiles[] = $webroot.'/library/js/bootstrap-formhelpers-colorpicker.js';
$arr_includeFiles[] = $webroot.'/library/js/bootstrap-dropdownhover.min.js';
$arr_includeFiles[] = $webroot.'/library/js/jquery.datetimepicker.full.min.js';
$arr_includeFiles[] = $webroot.'/library/js/jquery.mCustomScrollbar.concat.min.js';
$arr_includeFiles[] = $webroot.'/library/js/bootstrap-select.js';
//jquery to suport discontinued functions
$arr_includeFiles[] = $webroot.'/library/js/jquery-migrate-1.2.1.js';
$arr_includeFiles[] = $webroot.'/library/js/sc_script.js';
$arr_includeFiles[] = $webroot.'/library/js/common.js';
	
//unlink($cache_js_file);
if(file_exists($cache_js_file) && is_file($cache_js_file)){
	$time_cache_file = filemtime($cache_js_file);
	foreach($arr_includeFiles as $file){
		$time_include_file = filemtime($file);
		if($time_include_file > $time_cache_file){
			$recreate_cache = 1;
			break;
		}
	}
}else{
	$recreate_cache = 1;
}
if($recreate_cache){
	$js = '';
	foreach($arr_includeFiles as $file){
		$js .= file_get_contents($file);
	}
	$fp = fopen($cache_js_file,'w');
	fwrite($fp,$js);
	fclose($fp);
	$time_cache_file = filemtime($cache_js_file);
}else{
	$js = file_get_contents($cache_js_file);	
}

ob_start();
header('Content-type: text/javascript;');
header("Last-Modified: ".gmdate("D, d M Y H:i:s", $time_cache_file)." GMT");
if(@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])==$time_cache_file){
	header('HTTP/1.1 304 Not Modified');
	header('Connection: close');
}
else {
	header('HTTP/1.1 200 OK');
}
echo $js;
ob_end_flush();
?>