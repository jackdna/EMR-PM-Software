<?php ob_start();
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
$recreate_cache = 0;
$cache_js_file = "../../cache/cache_accounting_js.js";
$arr_includeFiles = array();
$arr_includeFiles[] = '../../library/js/bootstrap.min.js';
$arr_includeFiles[] = '../../library/js/jquery.min.1.12.4.js';
$arr_includeFiles[] = '../../library/js/jquery-ui.min.1.11.2.js';
$arr_includeFiles[] = '../../library/js/acc_common.js';

/*$arr_includeFiles[] = '../../js/common.js';
$arr_includeFiles[] = '../admin/menuIncludes_menu/js/jBoss.js';
$arr_includeFiles[] = '../main/javascript/superBill.js';
$arr_includeFiles[] = '../chart_notes/js/simpleMenu.js';
$arr_includeFiles[] = '../chart_notes/js/autoSave.js';
$arr_includeFiles[] = '../admin/menuIncludes_menu/js/disableKeyBackspace.js';
$arr_includeFiles[] = '../common/script_function.js';
$arr_includeFiles[] = '../main/javascript/actb.js';
$arr_includeFiles[] = '../main/javascript/common.js';
$arr_includeFiles[] = '../main/javascript/prompt.js';*/
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
	require '../../library/jsmin.php';
	$js = JSMin::minify($js);
	fwrite($fp,$js);
	fclose($fp);
	$time_cache_file = filemtime($cache_js_file);
}else{
	$js = file_get_contents($cache_js_file);	
}
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