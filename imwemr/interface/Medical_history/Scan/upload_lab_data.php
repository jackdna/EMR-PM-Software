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

include '../../../config/globals.php';
include $GLOBALS['srcdir'].'/classes/SaveFile.php';


$pid = $_REQUEST['load_patient'];
$pid = ($pid) ? $pid : $_SESSION['patient'];
$pid = (int) $pid;
$save = new SaveFile($pid);

if(isset($_GET['upload_from']) && empty($_GET['upload_from']) === false){
	$_REQUEST['upload_from'] = $_GET['upload_from'];
}

$allowed_ext = array("jpeg","jpg","gif","png");
$pat_id = $_REQUEST['load_patient'];
if($_REQUEST['upload_from_module']!=""){$_REQUEST['upload_from']=$_REQUEST['upload_from_module'];}
if($_REQUEST['upload_from']=='admin_documents') {
	$sub_dir = $_REQUEST['upload_from'];
	$pat_id='';
}else {
	$sub_dir = "upload_".$_REQUEST['upload_from'];	
}
$upload_dir = $save->upDir.$save->pDir."/".$sub_dir;
$save->ptDir($sub_dir);

$upload_url = $save->upDirWeb.$save->pDir.'/upload_'.$_REQUEST['upload_from'];

//$max_size = 1024 * 500; // Max: 500K.
$upload = $_FILES['files'];
$uplode_string = "";
$upload_arr_new[0] = array();
if($_REQUEST['control']=='l9'){
	$upload_arr_new = $upload;
	$uplode_string = "";
	foreach($upload as $nkey => $up_data){
		$upload_arr_new[0][$nkey] = $up_data;
	}
}else{
	foreach($upload as $nkey => $up_data){
		foreach($up_data as $my_key => $main_val_arr){
			$upload_arr_new[0][$nkey] = $main_val_arr;
		}
	}
}

//--- UPLOAD IMAGES ----
if(trim($method) != ''){
	if($method == 'scan'){
		$files_to_upload = 1;
		$db_upload_dir = $save->pDir."/upload_".$_REQUEST['upload_from'];
		for($i = 0 ; $i < $files_to_upload; $i++){
			if($_FILES["file"]['name'][$i]){
				$uploads = true;
				if($_FILES["file"]['name'][$i]){
					//$original_file=$fileinfo;
					$fileName = $_FILES["file"]['name'][$i];
					$fileType = $_FILES["file"]['type'][$i];
					$PSize = $_FILES["file"]['size'][$i];
					$tmp_file = $_FILES["file"]["tmp_name"][$i];

					// Check file extention
					$pathArr = pathinfo($fileName);
					$extension = $pathArr['extension'];
					$orig_name = $pathArr['filename'];
					
					if( !in_array($extension,$allowed_ext) ) {
						$_SESSION['message'] = $orig_name . " has invalid extension.<br>";
						continue;
					}
					
					// validate file content type
					if( !check_img_mime($tmp_file) ) {
						$_SESSION['message'] = $orig_name . " is an invalid image.<br>";
						continue;
					}

					// Change file name to save into new location
					$fileName = $orig_name."-".time();
					$fileName = $fileName.".".$extension;
			  		
					$dataArr = array();
					if(copy($tmp_file,$upload_dir."/".$fileName)){
						$dataArr['patient_id']= $pat_id;
						$dataArr['scan_from'] = $_REQUEST['upload_from'];
						$dataArr['uplaod_primary_id'] = $_REQUEST['primary_id'];
						$dataArr['upload_file_name'] = $db_upload_dir."/".$fileName;
						$dataArr['upload_file_type'] = $fileType;
						$dataArr['upload_date'] = date('Y-m-d');
						$dataArr['upload_by'] = $_SESSION['authId'];
						$dataArr['upload_status'] = '0';
						AddRecords($dataArr,'upload_lab_rad_data');
					}
					
				}
			}
		}
	}else if($method == 'upload'){
		// Do Code here to upload multiple images
	
		require_once($GLOBALS['srcdir'].'/upload/server/php/UploadHandler.php');
	
		// Change Default Option for upload handler before calling class constructor
		$options = array(
			'script_url' => $GLOBALS['php_server'].'/interface/Medical_history/Scan/upload_lab_data.php',
			'upload_dir' => $upload_dir.'/',
			'upload_url' => $upload_url.'/',
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
			$upload_dir = $save->pDir."/upload_".$_REQUEST['upload_from'];
			foreach($response->files as $file)
			{
				if($file->type && !$file->error && $file->url )
				{
					$file_type = $file->type;
					$image_name = $file->name;
					$image_tmp_name = $file->tmp_name;
					$image_size = $file->size;
					
					if($filetype=="application/pdf"){
						$image_name = urldecode($image_name);
						$image_name = str_ireplace(" ","-",$image_name);
						$image_name = str_ireplace(",","-",$image_name);
						$image_name = str_ireplace(".pdf","",$image_name);
						$image_name = str_ireplace(".","",$image_name);
						$image_name = $image_name.'.pdf';
					}
					
					if($method == 'scan'){
						$image_name = join('',$image_name);
						$image_tmp_name = join('',$image_tmp_name);
						$image_size = join('',$image_size);
					}
					
					if($file->type && !$file->error && $file->url ){
						$dataArr = array();
						
						$file_type = $file->type;
						$file_type = str_replace('image/','',$file_type);
						$file_type = str_replace('application/','',$file_type);	
						
						$dataArr['patient_id'] = $pat_id;
						$dataArr['scan_from'] = $_REQUEST['upload_from'];
						$dataArr['uplaod_primary_id'] = $_REQUEST['primary_id'];
						$dataArr['upload_file_name'] = $upload_dir."/".$image_name;
						$dataArr['upload_file_type'] = $file_type;
						$dataArr['upload_date'] = date('Y-m-d');
						$dataArr['upload_by'] = $_SESSION['authId'];
						$dataArr['upload_status'] = '0';
						AddRecords($dataArr,'upload_lab_rad_data');
					}
				}
			}
		}
	}
}
?>