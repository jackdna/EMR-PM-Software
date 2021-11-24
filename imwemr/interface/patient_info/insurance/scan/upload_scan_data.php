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
include '../../../../config/globals.php';
include $GLOBALS['srcdir'].'/classes/SaveFile.php';


$pid = $_REQUEST['load_patient'];
$pid = ($pid) ? $pid : $_SESSION['patient'];
$pid = (int) $pid;
$save = new SaveFile($pid);

$allowed_ext = array("jpeg","jpg","gif","png");

if($method == "upload")
{
	// Do Code here to upload multiple images
	
		require_once($GLOBALS['srcdir'].'/upload/server/php/UploadHandler.php');
	
		// Change Default Option for upload handler before calling class constructor
		$options = array(
			'script_url' => $GLOBALS['php_server'].'/interface/patient_info/insurance/scan/upload_scan_data.php',
			'upload_dir' => $save->upDir.$save->pDir.'/upload_patient_scan/',
			'upload_url' => $save->upDirWeb.$save->pDir.'/upload_patient_scan/',
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
			$upload_dir = $save->pDir.'/upload_patient_scan';
			foreach($response->files as $file)
			{
				if($file->type && !$file->error && $file->url )
				{
					$file_type = $file->type;
					$file_type = str_replace('image/','',$file_type);
					$file_type = str_replace('application/','',$file_type);
				
					$dataArr = array();
					$dataArr['patient_id'] = $pid;
					$dataArr['scan_from'] = $_REQUEST['scan_for'];
					$dataArr['uplaod_primary_id'] = $_REQUEST['scan_id'];
					$dataArr['upload_file_name'] = $upload_dir."/".$file->name;
					$dataArr['upload_file_type'] = $file_type;
					$dataArr['upload_date'] = date('Y-m-d');
					$dataArr['upload_by'] = $_SESSION['authId'];
					$dataArr['upload_status'] = '0';
					$dataArr['session_id'] = session_id();
					$dataArr['ins_type'] = ($_REQUEST['scan_for'] == "primary_reff")?1:(($_REQUEST['scan_for'] == "secondary_reff")?2:(($_REQUEST['scan_for'] == "tertiary_reff")?3:('')));
					$dataArr['ins_data_id'] = $_REQUEST['ins_data_id'];
			
					AddRecords($dataArr,'upload_lab_rad_data');
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
		$file_data["name"]	=	$fileinfo['name'][$i]	;
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

		$scandoc = $save->copyfile($file_data,'upload_patient_scan');
		
		$dataArr = array();		
		if($scandoc)
		{
			$dataArr['patient_id'] = $pid;
			$dataArr['scan_from'] = $_REQUEST['scan_for'];
			$dataArr['uplaod_primary_id'] = $_REQUEST['scan_id'];
			$dataArr['upload_file_name'] = $scandoc;
			$dataArr['upload_file_type'] = $extension;
			$dataArr['upload_date'] = date('Y-m-d');
			$dataArr['upload_by'] = $_SESSION['authId'];
			$dataArr['upload_status'] = '0';
			$dataArr['session_id'] = session_id();
			$dataArr['ins_type'] = ($_REQUEST['scan_for'] == "primary_reff")?1:(($_REQUEST['scan_for'] == "secondary_reff")?2:(($_REQUEST['scan_for'] == "tertiary_reff")?3:('')));
			$dataArr['ins_data_id'] = $_REQUEST['ins_data_id'];
			AddRecords($dataArr,'upload_lab_rad_data');
		}
		
	}

}
?>