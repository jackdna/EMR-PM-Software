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
error_reporting(-1);
require(dirname(__FILE__).'/dicom_link.php');
require(IMEDIC_DICOM.'/class_dicom.php');

//Test Connection --

$d = new dicom_net;

/*
$arr=array("1.dcm",
		"c.dcm",
		"71.dcm",
		"77.dcm");
*/		
		
$arr=array("PDF.test");

if(count($arr) > 0){

foreach($arr as $k => $v){

echo "<br/>".$v;

if(!empty($v) && file_exists($v)){
$d->file = trim($v);
$out = $d->send_dcm(''.DICOM_IP, ''.DICOM_PORT, ''.DICOM_AE, 'TEST');

if ($out) {
  print "$out\n";  
}

print "Sent!\n";
}

}

}

exit();

//Test Connection --

?>