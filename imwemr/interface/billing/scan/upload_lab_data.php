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
	Purpose: Saves lab scans
	Access Type: Direct 
*/
include '../../../config/globals.php';
include $GLOBALS['srcdir'].'/classes/SaveFile.php';

$pid = $_REQUEST['load_patient'];
$pid = ($pid) ? $pid : $_SESSION['patient'];
$pid = (int) $pid;
$save = new SaveFile($pid);

$allowed_ext = array("jpeg", "jpg", "gif", "png");

$pid = $_REQUEST['load_patient'];
if($_REQUEST['upload_from_module']!=""){$_REQUEST['upload_from']=$_REQUEST['upload_from_module'];}
if($_REQUEST['upload_from']=='admin_documents') {
	$upload_dir = '/'.$_REQUEST['upload_from'].'/';
	$upload_dir_web = '/'.$_REQUEST['upload_from'].'/';
	$pid='';
}else {
	$folder_name = 'upload_'.$_REQUEST['upload_from'].'/';
	$upload_dir = '/'.$folder_name;
	$upload_dir_web = '/'.$folder_name;
}

if($method == "upload")
{
		// Do Code here to upload multiple images
	
		require_once($GLOBALS['srcdir'].'/upload/server/php/UploadHandler.php');
	
		// Change Default Option for upload handler before calling class constructor
		$options = array(
			'script_url' => $GLOBALS['php_server'].'/interface/billing/scan/upload_scan_data.php',
			'upload_dir' => $save->upDir.$upload_dir,
			'upload_url' => $save->upDirWeb.$upload_dir_web,
			'access_control_allow_origin' => '*','access_control_allow_credentials' => false,
			'access_control_allow_methods' => array('OPTIONS','HEAD','GET','POST','PUT','PATCH','DELETE'),
			'access_control_allow_headers' => array('Content-Type','Content-Range','Content-Disposition'),
			'inline_file_types' => '/\.(gif|jpe?g|png)$/i', 'accept_file_types' => '/\.(gif|jpe?g|png|pdf|tif|tiff)$/i',
			'max_file_size' => null,'min_file_size' => 1,'max_number_of_files' => null,'max_width'=>null,'max_height'=>null,'min_width'=>1,'min_height'=>1,
			'discard_aborted_uploads'=>true,'orient_image'=>false,'image_versions'=>array( 'thumb' => array('max_width' => 600,'max_height' => 400),'thumbnail' => array('max_width' => 130,'max_height' => 150)),
		);
	
		$upload_handler = new UploadHandler($options,true);
		$response = (object) $upload_handler->response;
		
		if( $response->files )
		{
			foreach($response->files as $file)
			{
				if($file->type && !$file->error && $file->url )
				{
					$file_type = $file->type;
					$file_type = str_replace('image/','',$file_type);
					$file_type = str_replace('application/','',$file_type);
				
					$dataArr = array();
					$dataArr['patient_id'] = $pid;
					$dataArr['scan_from'] = $_REQUEST['upload_from'];
					$dataArr['uplaod_primary_id'] = $_REQUEST['primary_id'];
					$dataArr['upload_file_name'] = $upload_dir.$file->name;
					$dataArr['upload_file_type'] = $file_type;
					$dataArr['upload_date'] = date('Y-m-d');
					$dataArr['upload_by'] = $_SESSION['authId'];
					$dataArr['upload_status'] = '0';
					
					AddRecords($dataArr,'upload_lab_rad_data');
				}
			}
		}
	
	
}

else if($method == "scan")
{
	$files_to_upload = 1;
	for($i = 0 ; $i < $files_to_upload; $i++)
	{
		if($_FILES["file"]['name'][$i])
		{
			$uploads = true;
			if($_FILES["file"]['name'][$i])
			{
				$fileName =$_FILES["file"]['name'][$i];
				
				$files = array();
				$files['name'] = $_FILES["file"]['name'][$i];
				$files['type'] = $_FILES["file"]['type'][$i];
				$files['size'] = $_FILES["file"]['size'][$i];
				$files['tmp_name'] = $_FILES["file"]["tmp_name"][$i];
				
				// Check file extention
				$pathArr = pathinfo($files['name']);
				$extension = $pathArr['extension'];
				
				if( !in_array($extension,$allowed_ext) ) {
				  $_SESSION['message'] = $fileName . " has invalid extension.<br>";
				  continue;
				}

				// validate file content type
				if( !check_img_mime($files['tmp_name']) ) {
					$_SESSION['message'] = $fileName . " is an invalid image.<br>";
					continue;
				}	

				if( $file_path = $save->copyfile($files,$upload_dir))	
				{
					$dataArr['patient_id']= $pid;
					$dataArr['scan_from'] = $_REQUEST['upload_from'];
					$dataArr['uplaod_primary_id'] = $_REQUEST['primary_id'];
					$dataArr['upload_file_name'] = $file_path;
					$dataArr['upload_file_type'] = $file_extn;
					$dataArr['upload_date'] = date('Y-m-d');
					$dataArr['upload_by'] = $_SESSION['authId'];
					$dataArr['upload_status'] = '0';
					AddRecords($dataArr,'upload_lab_rad_data');
				}
					
			}
		}
	}
}


?>