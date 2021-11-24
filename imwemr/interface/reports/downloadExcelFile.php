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
FILE : DOWNLOADEXCELFILE.PHP
PURPOSE : FORCE DOWNLOAD EXCEL FILE
ACCESS TYPE : INCLUDED
*/
$ignoreAuth = true;
require_once(dirname(__FILE__)."/../../config/globals.php");
$csv_path = data_path().'users/UserId_'.$_SESSION['authId'].'/wsscript';
$filename = $csv_path."/patient_appointments.csv";
$content_type = "text/csv";
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

header("Cache-Control: private",false);
header("Content-Description: File Transfer");

header("Content-Type: ".$content_type."; charset=utf-8");
//die();
header("Content-disposition:attachment; filename=\"".$filename."\"");

header("Content-Length: ".@filesize($filename));
//echo filesize($filename);
@readfile($filename) or die("File not found.");
exit;
?>