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

?><?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
include_once("../../../config/globals.php");
if($_REQUEST["scanOrUpload"]=="upload") {
	$picName = $_SESSION['site_care_upload_doc'];
	$_SESSION['site_care_upload_doc']=NULL;
	$_SESSION['site_care_upload_doc']="";
	unset($_SESSION['site_care_upload_doc']);
}else {
	$picName = $_SESSION['site_care_scan_image'];
	$_SESSION['site_care_scan_image']=NULL;
	$_SESSION['site_care_scan_image']="";
	unset($_SESSION['site_care_scan_image']);
}
echo $picName;
?>