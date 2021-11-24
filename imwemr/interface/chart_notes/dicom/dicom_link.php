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
File: dicom_link.php
Purpose: This file provides DICOM receiver listener.
Access Type : Direct
*/
?>
<?php
$ignoreAuth=true;
if(isset($argv[1]) && !empty($argv[1])){
	$_SERVER['REQUEST_URI'] = $argv[1];
	$_SERVER['HTTP_HOST'] = $argv[1];
}

if(empty($_SERVER['REQUEST_URI'])){
	exit("practice argument is missing.");
}

require_once(dirname(__FILE__).'/../../../config/globals.php');
require($GLOBALS['srcdir']."/classes/SaveFile.php");
require($GLOBALS['srcdir']."/classes/work_view/Patient.php");
$os = new SaveFile();
$dfp = $os->get_dicom_data_path();
if(!empty($dfp) && file_exists($dfp."/dicom_globals.php")){
	include_once($dfp."/dicom_globals.php");
}else{
	exit("Global file is missing.");
}

?>
