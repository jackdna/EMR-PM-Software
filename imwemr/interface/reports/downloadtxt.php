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
FILE : DOWNLOADTXT.PHP
PURPOSE : FORCE DOWNOAD TEXT FILE
ACCESS TYPE : INCULDED
*/
include_once(dirname(__FILE__)."/../../config/globals.php");
$curDate = date('m-d-Y');

$fileName = "statement_report.txt";
if($fileName){
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=".$fileName."");	
	header("Content-Transfer-Encoding: binary");
	header('Content-Length: '.filesize($txt_filePath));
	readfile("$txt_filePath");
	die();
}
?>