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
File: minfy_inc.php
Purpose: This file provides compression header.
Access Type : Include file
*/
?>
<?php
/*
if(isset($z_ob_get_clean) && $z_ob_get_clean==1){
$out = ob_get_contents();
ob_end_clean();
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){ob_start("ob_gzhandler");}else{ob_start();}
$out = gen_compress_page($out);
echo $out;
ob_end_flush();
}else{
ob_end_clean();
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){ob_start("ob_gzhandler");}else{ob_start();}
}
*/

ob_end_clean();
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){ob_start("ob_gzhandler");}else{ob_start();}
if(isset($z_ob_flush_content) && !empty($z_ob_flush_content)){
echo $z_ob_flush_content;
}else{
include($z_ob_get_clean);
$out = ob_get_contents();
ob_end_clean();
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){ob_start("ob_gzhandler");}else{ob_start();}
$out = Minify_HTML::minify($out);
echo $out;
}
ob_end_flush();

?>