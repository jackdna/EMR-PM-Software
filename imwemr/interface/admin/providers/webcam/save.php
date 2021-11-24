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
include_once("../../../../config/globals.php");
include_once("../../../../library/classes/SaveFile.php");
extract($_REQUEST);
$user_id = $_SESSION["Provider_id_img"];
$user_id = (int) $user_id;

$save = new SaveFile($user_id,1);
$upload_dir = $save->upDir;
$save->ptDir('profile_img');
$ptDirPath = ($user_id > 0 ) ? $save->pDir.'/profile_img/' : '/tmp/';
$DirPath = $upload_dir.$ptDirPath;
$webPath = $save->upDirWeb.$ptDirPath;

$file_data = $_FILES['webcam'];

// validate file content type
if( !check_img_mime($file_data["tmp_name"]) ) {
	die('Error: '.$file_data["name"] . " is an invalid image.");
}

$scandoc = $save->copyfile($file_data);

if( $scandoc ){
	$_SESSION['scan_provider_image_new']=NULL;
	$_SESSION['scan_provider_image_new']="";					
	$_SESSION['scan_provider_image_new']=$scandoc;
}
die;
?>