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
	Purpose: Save patient's image
	Access Type: Direct 
*/

include_once("../../../../config/globals.php");
include_once("../../../../library/classes/SaveFile.php");
extract($_REQUEST);

$opreator_id = $_SESSION['authId'];
$patient_id = isset($_SESSION["patient"]) ? $_SESSION["patient"] : 0;
$save = new SaveFile($patient_id);
$upload_dir = $save->upDir;
$ptDirPath = ($patient_id > 0 ) ? $save->pDir.'/' : '/tmp/';
$DirPath = $upload_dir.$ptDirPath;

$file_data = $_FILES['webcam'];

// validate file content type
if( !check_img_mime($file_data["tmp_name"]) ) {
	die('Error: '.$file_data["name"] . " is an invalid image.<br>");
}

$scandoc = $save->copyfile($file_data);
if( $scandoc )
{
	if($patient_id != "")
	{
		$rsSel = imw_query("select p_imagename as ptPic from patient_data where id='".$patient_id."' ");
		if(imw_num_rows($rsSel))
		{
			extract(imw_fetch_array($rsSel));
			$tmpArr = explode("/",$ptPic);
			$fileName = end($tmpArr);
			$file_to_delete = $upload_dir.$ptPic;
			$file_thumb = $upload_dir . '/PatientId_'.$patient_id.'/thumb/'.$fileName;
			$file_thumbnail = $upload_dir . '/PatientId_'.$patient_id.'/thumbnail/'.$fileName;

			if($ptPic != "" && file_exists($file_to_delete))
				unlink($file_to_delete);

			if($ptPic != "" && file_exists($file_thumb))
			unlink($file_thumb);

			if($ptPic != "" && file_exists($file_thumbnail))
			unlink($file_thumbnail);	
				
		}
		$qry = "update patient_data set p_imagename ='".$scandoc."' where id  = '".$patient_id."' ";
		$res = imw_query($qry);
	}
	
	if($patient_id == "")
	{
		// IF NEW PATEINT					
		$_SESSION['scan_patient_image']=NULL;
		$_SESSION['scan_patient_image']="";					
		$_SESSION['scan_patient_image']=$scandoc;
	}
	else
	{
		$_SESSION['scan_patient_image']=NULL;
		$_SESSION['scan_patient_image']="";					
	}	
	
	$_SESSION['scan_patient_image_new']=NULL;
	$_SESSION['scan_patient_image_new']="";					
	$_SESSION['scan_patient_image_new']=$scandoc;
	
	echo $_SESSION['scan_patient_image_new'];
}
?>