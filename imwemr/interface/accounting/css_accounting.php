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
$cache_css_file = "../../cache/cache_accounting_css.css";
$arr_includeFiles = array();
$arr_includeFiles[] = '../../library/css/common.css';
$arr_includeFiles[] = '../../library/css/accounting.css';
$arr_includeFiles[] = '../../library/css/bootstrap.min.css';
$arr_includeFiles[] = '../../library/css/jquery.datetimepicker.min.css';

if(file_exists($cache_css_file) && is_file($cache_css_file)){
	$time_cache_file = filemtime($cache_css_file);
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
	$css = '';
	foreach($arr_includeFiles as $file){
		$css .= file_get_contents($file);
	}
	$fp = fopen($cache_css_file,'w');
	fwrite($fp,$css);
	fclose($fp);
	$time_cache_file = filemtime($cache_css_file);
}else{
	$css = file_get_contents($cache_css_file);	
}
header('Content-type: text/css;');
header("Last-Modified: ".gmdate("D, d M Y H:i:s", $time_cache_file)." GMT");
if(@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])==$time_cache_file){
	header('HTTP/1.1 304 Not Modified');
	header('Connection: close');
}
else {
	header('HTTP/1.1 200 OK');
}
echo $css;
ob_end_flush();
?>