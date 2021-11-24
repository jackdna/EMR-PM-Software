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

$ignoreAuth = true;
include("../../config/globals.php");
include($GLOBALS['srcdir'].'/classes/SaveFile.php');
$save = new saveFile();
//creating pdfsplit folder
$save->ptDir('pdfSplit/tmp');

//Show the number of files to upload
$files_to_upload = 1;

//Directory where the uploaded files have to come
$path 			= realpath(dirname(__FILE__));
$upload_dir = $GLOBALS['fileroot'].'/data/'.PRACTICE_PATH.'/pdfSplit/tmp/';
$upload_dir_web = $GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/pdfSplit/tmp/';
// **************** ADDED BY LAB Asprise! ********************

$allowed_ext = array("jpeg","jpg","gif","png");
$max_size = 1024 * 500; // Max: 500K.

if ( $_GET['method'] == "scan" )
{
	$_SESSION['message'] = "";
	$uploads = false;
	
	for($i = 0 ; $i < $files_to_upload; $i++) {
		if($_FILES['file']['name'][$i]) {

			if( file_exists($_FILES['file']['tmp_name'][$i]) ) {

				$uploads = true;

				// Check file extention
				$pathArr = pathinfo($_FILES['file']['name'][$i]);
				$extension = $pathArr['extension'];
				$orig_name = $pathArr['filename'];
				
				if( !in_array($extension,$allowed_ext) ) {
				  $_SESSION['message'] = $orig_name . " has invalid extension.<br>";
				  continue;
				}

				// validate file content type
				if( !check_img_mime($_FILES['file']['tmp_name'][$i]) ) {
					$_SESSION['message'] = $orig_name . " is an invalid image.<br>";
					continue;
				}
	  
				// Change file name to save into new location
				$fileName = $orig_name."-".time();
				$fileName = $fileName.".".$extension;
				$file_to_upload = $upload_dir."/".$fileName;

				$fContent = file_get_contents($_FILES['file']['tmp_name'][$i]);
				@file_put_contents($file_to_upload,$fContent);	
				if(file_exists($file_to_upload))
				{ 
					$pdf_info = pathinfo($file_to_upload);
					$pdf_basename 	= $pdf_info['basename'];
					$pdf_dir	  	= $pdf_info['dirname'];
					$pdf_name	  	= $pdf_info['filename'];
					$pdf_thumb_dest	= str_ireplace("/tmp","",$pdf_dir);
					$pdf_jthumb_dest= $pdf_thumb_dest."/".$pdf_name.".pdf";
					$source = realpath($pdf_dir."/".$pdf_basename).'[0]';
					$exe_path = $GLOBALS['IMAGE_MAGIC_PATH'];
					if(!empty($exe_path)){$exe_path .= "/";}else{$exe_path='';}
					
					if (!file_exists($pdf_jthumb_dest)){
						exec($exe_path.'convert -trim "'.$source.'" "'.$pdf_jthumb_dest.'"', $output, $return_var);
					}
				}
				
				unlink($file_to_upload);
				chmod($file_to_upload,0777);
        		$_SESSION['message'] .= $orig_name." uploaded.<br>";
    		}
		}
	}
	
	if(!$uploads)  $_SESSION['message'] = "No files selected!";

}
?>