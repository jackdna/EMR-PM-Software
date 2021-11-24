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
include_once("../../../config/globals.php");
include_once("../../../library/classes/SaveFile.php");		

$rqRTMEId = (int)$_REQUEST['id'];
$patId = $_SESSION["patient"];
// Turn off all error reporting

$save = new SaveFile($patId);

//Show the number of files to upload
$files_to_upload = 1;

// **************** ADDED BY LAB Asprise! ********************

$allowed_ext = array('jpeg','jpg','gif','png','pdf');

if(($_GET['method']) && (empty($rqRTMEId) == false) && (empty($patId) == false)){
	//Scan the file
	if($_GET['method'] == "scan"){
		$uploads = false;
		for($i = 0 ; $i < $files_to_upload; $i++){
			
			$file = array();
			$file['name'] = $_FILES['file']['name'][$i];
			$file['type'] = $_FILES['file']['type'][$i];
			$file['size'] = $_FILES['file']['size'][$i];
			$file['tmp_name'] = $_FILES['file']['tmp_name'][$i];
			
			if( file_exists($file['tmp_name'])) $uploads = true;
			else continue;	
			
			// ******** CHECK FILE EXTENSION *******************
			$extension = pathinfo($_FILES['file']['name'][$i]);					
			$extension = $extension[extension];
			$extensionAccepted = in_array($extension,$allowed_ext) ? 1 : 0;
			
			if(! $extensionAccepted) {
				$message .= $file['name'] . " has invalid extension.<br>";
				continue;
			}
		 
			// Create Directory of site care and upload image
			$image_path = $save->copyfile($file,'real_time_scan_upload');
			
			$file_name = end(explode('/',$image_path));
			
			$qryInsertRTSU = "INSERT INTO ".constant("IMEDIC_SCAN_DB").".scans SET 
														patient_id = '".$patId."', test_id = '".$rqRTMEId."',
														image_form = 'RTER',  image_name = '".$file_name."',
														file_type = '".$extension."', scan_or_upload='scan',
														created_date = '".date('Y-m-d')."', file_path = '".$image_path."',
														testing_docscan_operator='".$_SESSION["authId"]."'";
			$rsInsertRTSU = imw_query($qryInsertRTSU);
			
			$message .=	$file['name']." uploaded.<br>";
      
  	}
    if(!$uploads)  $message = "No files selected!";
	}
	elseif($_GET['method'] == "upload"){		
		
		require_once($GLOBALS['srcdir'].'/upload/server/php/UploadHandler.php');
		// Change Default Option for upload handler before calling class constructor
		
		$options = array(
			'script_url' => $GLOBALS['php_server'].'/interface/patient_info/eligibility/uploadeEligibilityScan.php',
			'upload_dir' => $save->upDir.$save->pDir.'/real_time_scan_upload/',
			'upload_url' => $save->upDirWeb.$save->pDir.'/real_time_scan_upload/',
			'access_control_allow_origin' => '*','access_control_allow_credentials' => false,
			'access_control_allow_methods' => array('OPTIONS','HEAD','GET','POST','PUT','PATCH','DELETE'),
			'access_control_allow_headers' => array('Content-Type','Content-Range','Content-Disposition'),
			'inline_file_types' => '/\.(gif|jpe?g|png)$/i', 'accept_file_types' => '/\.(gif|jpe?g|png|pdf|tif|tiff)$/i',
			'max_file_size' => null,'min_file_size' => 1,'max_number_of_files' => null,'max_width'=>null,'max_height'=>null,'min_width'=>1,'min_height'=>1,
			'discard_aborted_uploads'=>true,'orient_image'=>false,'image_versions'=>array( 'thumb' => array('max_width' => 800,'max_height' => 600),'thumbnail' => array('max_width' => 130,'max_height' => 150)),
		);
	
		$upload_handler = new UploadHandler($options,true);
		$response = (object) $upload_handler->response;
		
		if( $response->files ) {
			$upload_dir = $save->pDir.'/real_time_scan_upload';
			foreach($response->files as $file)
			{
				if($file->type && !$file->error && $file->url )
				{
					$file_type = $file->type;
					$file_type = str_replace('image/','',$file_type);
					$file_type = str_replace('application/','',$file_type);
					
					$dataArr = array();
					$dataArr['patient_id'] = $patId;
					$dataArr['test_id'] = $rqRTMEId;
					$dataArr['image_form'] = 'RTER';
					$dataArr['image_name'] = $file->name;
					$dataArr['file_type'] = $file_type;
					$dataArr['scan_or_upload'] = 'upload';
					$dataArr['created_date'] = date('Y-m-d');
					$dataArr['doc_upload_date'] = date('Y-m-d');
					$dataArr['operator_id'] = $_SESSION['authId'];
					$dataArr['file_path'] = $upload_dir."/".$file->name;
					
					AddRecords($dataArr,constant("IMEDIC_SCAN_DB").".scans");
					
				}
			}
		}
		
	}
}
else{
	die("Please select patient.");
}

?>