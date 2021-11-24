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

include_once("../../../config/globals.php");
include '../../../library/classes/SaveFile.php';
unset($_SESSION['site_care_upload_doc']);
unset($_SESSION['site_care_scan_image']);
$save = new SaveFile();
$siteCareId = "";
$siteCareId = $_REQUEST['siteCareId'];
// Turn off all error reporting
error_reporting(0);
$i = 0;

//$allowed_ext = array("jpeg","jpg","gif","png","pdf");
$allowed_ext 		= "jpeg, jpg, gif, png, pdf";
$max_size 			= 1024 * 500; // Max: 500K.
$subDirMain			= "site_care_plan";
$subDir 			= $subDirMain."/siteCareId_".$siteCareId;
if(!$siteCareId) {
	$subDir 		= $subDirMain."/tmp";	
}
$uploadPathTmp 		= $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/";
$uploadPath 		= $uploadPathTmp.$subDir."/";
$uploadPath_web 	= $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/";
$subDirMainPath		= $uploadPathTmp.$subDirMain."/";
if($_REQUEST['method'] == 'upload'){
	// Do Code here to upload multiple images
	require_once($GLOBALS['srcdir'].'/upload/server/php/UploadHandler.php');

	// Change Default Option for upload handler before calling class constructor
	$options = array(
		'script_url' => $GLOBALS['php_server'].'/interface/admin/alert/scan_site_care.php',
		'upload_dir' => $save->upDir.'/'.$subDir.'/',
		'upload_url' => $save->upDirWeb.'/'.$subDir.'/',
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
				$new_upload_path_tmp = $subDir."/".$file->name;
				$new_upload_path = str_ireplace("site_care_plan/","", $new_upload_path_tmp);
				if($siteCareId<>""){
					$qryGetStUploadPath = "select upload_path as stUploadPath from alert_tbl where alertId ='$siteCareId'";	
					$rsGetStUploadPath = imw_query($qryGetStUploadPath);
					if($rsGetStUploadPath){							
						if(imw_num_rows($rsGetStUploadPath) > 0){								
							$rowGetStUploadPath = imw_fetch_array($rsGetStUploadPath);
							$fileToDelete = "";
							$fileToDelete = $subDirMainPath.$rowGetStUploadPath['stUploadPath'];file_put_contents("a.txt",$fileToDelete);
							if(file_exists($fileToDelete)) {
								unlink($fileToDelete);
							}
						}	
						imw_free_result($rsGetStUploadPath);
					}
					
					$qryUpdateStUploadPath = "update alert_tbl set upload_path='$new_upload_path' where alertId ='$siteCareId'";							
					$rsUpdateStUploadPath = imw_query($qryUpdateStUploadPath);
					unset($_SESSION['site_care_upload_doc']);	
					$_SESSION['site_care_upload_doc']=NULL;
					$_SESSION['site_care_upload_doc']="";					
					$_SESSION['site_care_upload_doc']=$new_upload_path;	
				}else{
					// IF NEW Site care			
					unset($_SESSION['site_care_upload_doc']);	
					$_SESSION['site_care_upload_doc']=NULL;
					$_SESSION['site_care_upload_doc']="";					
					$_SESSION['site_care_upload_doc']=$new_upload_path;	
					
					unset($_SESSION['site_care_upload_doc_new']);
					$_SESSION['site_care_upload_doc_new'] = NULL;
					$_SESSION['site_care_upload_doc_new'] = "";					
					$_SESSION['site_care_upload_doc_new'] = $file->name;
				}
			}
		}
	}
}
else if($_REQUEST['method'] == 'scan'){
	//$upload_dir = $uploadPath;
	$uploads = false;
	$upload_dir 			= $save->upDir;
	$save->ptDir("/".$subDir);		

	$i = -1;
	foreach($_FILES as $tagname => $fileinfo)
	{	$uploads = true;
		$i++;
		$file_data 				= array();
		$file_data["name"]		= $fileinfo['name'][$i];
		$file_data["type"]		= $fileinfo['type'][$i];
		$file_data["size"]		= $fileinfo['size'][$i];
		$file_data["tmp_name"] 	= $fileinfo['tmp_name'][$i];
		$file_data["error"] 	= $fileinfo['error'][$i];
		
		$extension = pathinfo($file_data["name"]);
		$extension = $extension["extension"];
		$extensionAccepted = 0;
		$allowed_paths = explode(", ", $allowed_ext);
		for($j = 0; $j < count($allowed_paths); $j++)
		{
			if ($allowed_paths[$j] == "$extension") {
				$extensionAccepted = 1;
			}
		}
		
		if( !$extensionAccepted ) {
			$message .= $fileinfo['name'][$i] . " has invalid extension.<br>";
			continue;
		}
			
		$scandoc = $save->copyfile($file_data,"","","/".$subDir);
		$scandocSaveDB = str_ireplace("/site_care_plan/","", $scandoc);
		$fileName = end(explode("/",$scandoc));
		if($siteCareId<>""){
			$qryGetStScanPath = "select scan_path as stScanPath from alert_tbl where alertId ='$siteCareId'";	
			$rsGetStScanPath = imw_query($qryGetStScanPath);
			if($rsGetStScanPath){							
				if(imw_num_rows($rsGetStScanPath) > 0){								
					$rowGetStScanPath = imw_fetch_array($rsGetStScanPath);
					$fileToDelete = $subDirMainPath.$rowGetStUploadPath['stScanPath'];
					if(file_exists($fileToDelete)) {
						unlink($fileToDelete);
					}
				}	
				imw_free_result($rsGetStScanPath);
			}
			
			$qryUpdateStScanPath = "update alert_tbl set scan_path='$scandocSaveDB' where alertId ='$siteCareId'";							
			$rsUpdateStScanPath = imw_query($qryUpdateStScanPath);
			unset($_SESSION['site_care_scan_image']);	
			$_SESSION['site_care_scan_image']=NULL;
			$_SESSION['site_care_scan_image']="";					
			$_SESSION['site_care_scan_image']=$scandocSaveDB;	
		}else{
			// IF NEW Site care				
			unset($_SESSION['site_care_scan_image']);	
			$_SESSION['site_care_scan_image']=NULL;
			$_SESSION['site_care_scan_image']="";					
			$_SESSION['site_care_scan_image']=$scandocSaveDB;
			
			unset($_SESSION['site_care_scan_image_new']);	
			$_SESSION['site_care_scan_image_new']=NULL;
			$_SESSION['site_care_scan_image_new']="";					
			$_SESSION['site_care_scan_image_new']=$fileName;
		}
		
	}

	if(!$uploads)  $message = "No files selected!";
}
?>