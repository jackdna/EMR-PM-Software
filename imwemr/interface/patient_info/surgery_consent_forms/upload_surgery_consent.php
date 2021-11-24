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

set_time_limit(900);
include_once("../../../config/globals.php");		
include '../../../library/classes/SaveFile.php';

$pid = $_REQUEST['load_patient'];
$pid = ($pid) ? $pid : $_SESSION['patient'];
$pid = (int) $pid;
$save = new SaveFile($pid);

if($_REQUEST['scanTypeFolder']!="" && $_REQUEST['scanTypeFolder']!=0){
	$scanTypeFolder = $_REQUEST['scanTypeFolder'];
}else{
	$scanTypeFolder=0; 
}
					
$allowed_ext = array("jpeg","jpg","gif","png");

if($method == "upload")
{
		// Do Code here to upload multiple images
		require_once($GLOBALS['srcdir'].'/upload/server/php/UploadHandler.php');
	
		// Change Default Option for upload handler before calling class constructor
		$options = array(
			'script_url' => $GLOBALS['php_server'].'/interface/patient_info/surgery_consent_forms/upload_surgery_consent.php',
			'upload_dir' => $save->upDir.$save->pDir.'/',
			'upload_url' => $save->upDirWeb.$save->pDir.'/',
			'access_control_allow_origin' => '*','access_control_allow_credentials' => false,
			'access_control_allow_methods' => array('OPTIONS','HEAD','GET','POST','PUT','PATCH','DELETE'),
			'access_control_allow_headers' => array('Content-Type','Content-Range','Content-Disposition'),
			'inline_file_types' => '/\.(gif|jpe?g|png)$/i', 'accept_file_types' => '/\.(gif|jpe?g|png|pdf|tif|tiff)$/i',
			'max_file_size' => null,'min_file_size' => 1,'max_number_of_files' => null,'max_width'=>null,'max_height'=>null,'min_width'=>1,'min_height'=>1,
			'discard_aborted_uploads'=>true,'orient_image'=>false,'image_versions'=>array( 'thumbnail' => array('max_width' => 130,'max_height' => 150)),
		);
	
		$upload_handler = new UploadHandler($options,true);
		$response = (object) $upload_handler->response;
		
		if( $response->files )
		{
			foreach($response->files as $file)
			{
				if($file->type && !$file->error && $file->url )
				{
					if($pid<>"")
					{
						$qry= "insert into surgery_center_patient_scan_docs set
											patient_id = '".$pid."',
											scan_doc_add = '".$save->pDir."/".$file->name."',
											scan_type_folder = '".$scanTypeFolder."',
											created_date = '".date('Y-m-d')."',
											surgery_patient_scan_operator ='".$_SESSION['authId']."',
											surgery_patient_scan_date =now()";
						$res = imw_query($qry);
						$_SESSION['scanDocId'] = imw_insert_id();
					}
				}
			}
		}
}

else if($method == "scan")
{
	$i = -1;
	foreach($_FILES as $tagname => $fileinfo)
	{
		$i++;
		$upload_dir = $save->upDir;
		$save->pDir = $pid > 0 ? $save->pDir : '/tmp';
		$patientDir = $save->pDir;
		
		$file_data = array();
		$file_data["name"]	=	$fileinfo['name'][$i];
		$file_data["type"]	=	$fileinfo['type'][$i];
		$file_data["size"]	=	$fileinfo['size'][$i];
		$file_data["tmp_name"] = $fileinfo['tmp_name'][$i];
		$file_data["error"] = $fileinfo['error'][$i];
		
		// Check file extention
		$pathArr = pathinfo($file_data["name"]);
		$extension = $pathArr['extension'];
		$orig_name = $pathArr['filename'];
		
		if( !in_array($extension,$allowed_ext) ) {
			$_SESSION['message'] = $orig_name . " has invalid extension.<br>";
			continue;
		}
		
		// validate file content type
		if( !check_img_mime($file_data["tmp_name"]) ) {
			$_SESSION['message'] = $orig_name . " is an invalid image.<br>";
			continue;
		}

		$scandoc = $save->copyfile($file_data);
		
		$dataArr = array();		
		if($scandoc)
		{
			if($pid<>"")
			{
				echo $qry= "insert into surgery_center_patient_scan_docs set
									patient_id = '".$pid."',
									scan_doc_add = '".$scandoc."',
									scan_type_folder = '".$scanTypeFolder."',
									created_date = '".date('Y-m-d')."',
									surgery_patient_scan_operator ='".$_SESSION['authId']."',
									surgery_patient_scan_date =now()";
				$res = imw_query($qry);
				$_SESSION['scanDocId'] = imw_insert_id();
			}
		}
		
	}

}
?>