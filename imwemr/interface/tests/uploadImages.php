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
/*
File: uploadScan.php
Purpose: This file provides save function in Test Upload, scan .
Access Type : Direct
*/
?>
<?php
ini_set('gd.jpeg_ignore_warning', 1);
require_once(dirname(__FILE__)."/../../config/globals.php");
require_once(dirname(__FILE__)."/../../library/patient_must_loaded.php");
require_once(dirname(__FILE__)."/../../library/classes/class.tests.php");
require_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
$objTests				= new Tests;
$patient_id 	= $_SESSION['patient'];
//MAKING OBJECT TO SAVE IMAGE FILES
$library_path 		= $GLOBALS['webroot'].'/library';
$formName 	= 	$_REQUEST["formName"];
$form_id	= 	$_REQUEST["form_id"];
$frm_id 	= 	$_REQUEST["form_id"]; 
$edit_id 	= 	$_REQUEST["edit_id"];
$testId 	= 	$_REQUEST["testId"];
$refer_id	=	$_SESSION['refer_id']; 
$folder_id	=	$_REQUEST['folder_id'];
$editid		=	$_REQUEST['editid'];
$pid 		= 	$_SESSION['patient'];
$operator_id= 	$_SESSION['authId'];
$test_temp_id=$_REQUEST['test_temp_id'];
$test_master_id = $_REQUEST["test_master_id"];
$save = new SaveFile($patient_id);

require_once($GLOBALS['srcdir'].'/upload/server/php/UploadHandler.php');

// Change Default Option for upload handler before calling class constructor
$options = array(
	'script_url' => $GLOBALS['php_server'].'/interface/tests/uploadImages.php',
	'upload_dir' => $save->upDir.$save->pDir.'/Scan/'.$formName.'/',
	'upload_url' => $save->upDirWeb.$save->pDir.'/Scan/'.$formName.'/',
	'access_control_allow_origin' => '*','access_control_allow_credentials' => false,
	'access_control_allow_methods' => array('OPTIONS','HEAD','GET','POST','PUT','PATCH','DELETE'),
	'access_control_allow_headers' => array('Content-Type','Content-Range','Content-Disposition'),
	'inline_file_types' => '/\.(gif|jpe?g|png)$/i', 'accept_file_types' => '/\.(gif|jpe?g|png|pdf|tif|tiff)$/i',
	'max_file_size' => null,'min_file_size' => 1,'max_number_of_files' => null,'max_width'=>null,'max_height'=>null,'min_width'=>1,'min_height'=>1,
	'discard_aborted_uploads'=>true,
	'orient_image'=>false,
	'image_versions'=>array(
		'' => array('auto_orient' => true),
		'thumb' => array(
			'max_width' => 500,
			'max_height' => 500,
			'auto_orient' => true
		), 	
	  'thumbnail' => array(
			'max_width' => 80,
			'max_height' => 80,
			'auto_orient' => true
		)
	)
);

$upload_handler = new UploadHandler($options,true);
$response = (object) $upload_handler->response;
	
if($response->files){
	//$fp = fopen('upload.txt','a');
	if(!empty($testId)){}else if(empty($form_id)&&!isset($_REQUEST["form_id"])){
		if(!empty($_SESSION["finalize_id"])){
			$form_id = $_SESSION["finalize_id"];
		}elseif(!empty($_SESSION["form_id"])){
			$form_id = $_SESSION["form_id"];
		}
	}
	
	if(!empty($patient_id) && isset($formName) && !empty($formName)){
		if(empty($testId) && empty($editid)){
			$tmparr = array();
			if(isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])){
				$tmparr = unserialize($_SESSION["test2edit"]);
			}
			if(isset($tmparr[$formName]) && !empty($tmparr[$formName])){
				$testId = $tmparr[$formName];
			}else{
				//Insert into test table //use test_id for scan table //set test_id in session for test form
				$examDate=date('Y-m-d');
				$this_test_cols = $objTests->get_table_cols_by_test_table_name($test_master_id,'id');
				$query_insert = "INSERT INTO `".$this_test_cols['test_table']."` SET 
								`".$this_test_cols['patient_key']."` 	= '$patient_id', 
								`".$this_test_cols['exam_date_key']."`	= '$examDate', 
								`examTime`								= '".date('Y-m-d H:i:s')."', 
								`".$this_test_cols['formid_key']."`		= '$frm_id',
								`".$this_test_cols['performed_key']."`	= '$operator_id'	";
	
				if($this_test_cols['test_table']=='test_other' && $this_test_cols['test_type']=='1'){			
						$query_insert .= ", `test_template_id` = '".$this_test_cols['id']."'";
				}else if($this_test_cols['test_table']=='test_custom_patient' && $this_test_cols['test_type']=='1'){			
						$query_insert .= ", `test_template_id`  = '".$this_test_cols['id']."', 
											`version` 			= '".$this_test_cols['version']."'";
				}
				
				if(!empty($query_insert)){
					$result_insert = imw_query($query_insert);
					//fwrite($fp,$query_insert."\n\n".imw_error());
					$testId	= imw_insert_id();
					$tmparr[$formName] = $testId;
					$_SESSION["test2edit"] = serialize($tmparr);
				}
			}
		}
	}
	
	
	
	$upload_dir = $save->pDir.'/Scan/'.$formName;
	$index = 0;
	foreach($response->files as $file)
	{
		$imgSrc_file = $save->upDir.$upload_dir."/".$file->name;
		//fwrite($fp,$imgSrc_file."\n\n");
		if(file_exists($imgSrc_file)){
			//fwrite($fp,"File exists.\n\n");
			$tifPDFpath = false;
			if(substr($imgSrc_file,-5)=='.tiff'){
				$tifPDFpath = substr($imgSrc_file,0,-4).'pdf';
				$file->name = substr($file->name,0,-4).'pdf';
				//$new_scanPth	= substr($scanPth,0,-4).'pdf';
				$file->type	= 'application/pdf';
				
			}else if(substr($imgSrc_file,-4)=='.tif'){
				$tifPDFpath = substr($imgSrc_file,0,-3).'pdf';
				$file->name = substr($file->name,0,-3).'pdf';
				//$new_scanPth	= substr($scanPth,0,-3).'pdf';
				$file->type	= 'application/pdf';
			}
			//fwrite($fp,$tifPDFpath."\n".$file->name."\n\n");
			if($tifPDFpath && (!is_file($tifPDFpath) || !file_exists($tifPDFpath))){
				exec('convert "'.$imgSrc_file.'" "'.$tifPDFpath.'"', $output, $return_var);
				if(is_file($tifPDFpath) && file_exists($tifPDFpath)){
					unlink($imgSrc_file);
				}
			}
		}

		if($file->type && !$file->error && $file->url){
			if($edit_id>0){
				$sql = "update  ".constant("IMEDIC_SCAN_DB").".scans SET
											image_name = '".$file->name."',
											file_type = '".$file->type."',
											file_path = '".$upload_dir."/".$file->name."',";
									$sql .= "doc_upload_date = '".date('Y-m-d H:i:s')."',
											operator_id='$operator_id' where scan_id='$edit_id'";
				$updateStrQry = imw_query($sql);					
			}else{
				$sql = "INSERT INTO ".constant("IMEDIC_SCAN_DB").".scans SET
										patient_id = '".$patient_id."',".
										"form_id = '".$form_id."',".
										"test_id = '".$testId."', ".
										"image_form = '".$formName."', ".
										"image_name = '".$file->name."',
										file_type = '".$file->type."',
										scan_or_upload='upload',
										doc_upload_date='".date('Y-m-d H:i:s')."',
										file_path = '".$upload_dir."/".$file->name."',";
										if(isset($_REQUEST['site'][$index])){$sql .= "site = '".$_REQUEST['site'][$index]."',";}
								$sql .= "operator_id='$operator_id'";
				//fwrite($fp,$sql."\n\n");
				
				$insertStrQry = imw_query($sql);
				$insertId = imw_insert_id();
				$_SESSION['document_scan_id']=$insertId;
				
				//COMMENTED TEMPORARILIY.
				//$insrtLog = providerViewLogFun($insertId,$_SESSION['authId'],$_SESSION['patient'],'tests');
			}
			
			
		}
		$index++;
	}
	
	//fclose($fp);
}

?>