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
File: dicom_receiver.php
Purpose: This file provides DICOM receiver listener.
Access Type : Direct
*/
?>
<?php
//chdir(dirname(__FILE__));
require(dirname(__FILE__).'/dicom_link.php');
require(IMEDIC_DICOM.'/class_dicom.php');

//mk temp dir
//creating dir if not exists
$str_dir=DICOM_PRACTICE_DIR."/temp";
if (!file_exists($str_dir)) {  mkdir($str_dir, 0777, true); }

//Processing file	//IMEDIC_DICOM."/imp_port_listener.php
$processing_file=IMEDIC_DICOM."/imp_port_listener.php";

$o = new dicom_net();
$o->storage_server($processing_file, $str_dir);

?>