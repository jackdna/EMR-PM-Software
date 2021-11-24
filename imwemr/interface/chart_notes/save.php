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

	File: save.php
	Purpose: Save drawings's camera
	Access Type: Direct
*/

include_once('../../config/globals.php');
include_once($GLOBALS['incdir']."/../library/classes/SaveFile.php");
extract($_REQUEST);

$opreator_id = $_SESSION['authId'];
$patient_id = isset($_SESSION["patient"]) ? $_SESSION["patient"] : 0;
$save = new SaveFile($patient_id);
$upload_dir = $save->upDir;
$ptDirPath = ($patient_id > 0 ) ? $save->pDir.'/' : '/tmp/';

$file_data = $_FILES['webcam'];
// validate file content type
if( !check_img_mime($file_data["tmp_name"]) ) {
	die('Error: '.$file_data["name"] . " is an invalid image.<br>");
}

$d = file_get_contents($file_data["tmp_name"]);
if($d)
{
	$jpg = $d;
	//$img = $_GET["img"];

	//$filename1 = $ptDirPath."Imedic_". mktime(). ".jpg";
	//$filename = $DirPath."Imedic_". mktime(). ".jpg";

	//$fileContent = file_put_contents($filename, $jpg);
	if($patient_id != "")
	{
		$fileName = "drw_camera_".time(). ".jpg";
		$dbDocPathFull = $save->cr_file("/idoc_drawing/scan_upload/".$fileName, $jpg);
		//resize big
		$file_pointer_inc_resize=$save->ptDir("idoc_drawing/scan_upload/resize","i");
		$file_pointer_inc_resize.="/".$fileName;
		$t = $save->createThumbs($dbDocPathFull,$file_pointer_inc_resize,730,465);
		if(file_exists($file_pointer_inc_resize)){
			$save->resize_image($file_pointer_inc_resize,320, 240,730,465);
			$file_pointer = $save->getFilePath($file_pointer_inc_resize, "db2");
			$file_pointer1= $save->getFilePath($file_pointer_inc_resize, "w2");
			$_SESSION['scan_patient_image_new']=NULL;
			$_SESSION['scan_patient_image_new']="";
			$_SESSION['scan_patient_image_new']=$file_pointer."!~!".$file_pointer1;
		}
	}

	echo $_SESSION['scan_patient_image_new'];
}
else
{
	echo "Encoded JPEG information not received.";
}
?>
