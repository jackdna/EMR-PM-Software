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
include_once(dirname(__FILE__)."/../../config/globals.php");
include $GLOBALS['srcdir'].'/classes/SaveFile.php';
$library_path = $GLOBALS['webroot'].'/library';	
include($GLOBALS['srcdir'].'/classes/work_view/wv_functions.php');

$formName 	=	$_REQUEST["formName"];
$form_id 	= 	$_REQUEST["form_id"];
$frm_id 	=	$_REQUEST["form_id"];
$edit_id 	= 	$_REQUEST["edit_id"];
$testId 	= 	$_REQUEST["testId"];
$refer_id	=	$_SESSION['refer_id']; 
$folder_id	=	$_REQUEST['folder_id'];
$editid		=	$_REQUEST['editid'];
$pid 		= 	$_SESSION['patient'];
$operator_id = 	$_SESSION['authId'];
$test_temp_id=$_REQUEST['test_temp_id'];
$userauthorized  = $_SESSION['authId'];
if(!empty($testId)){	
}else if(empty($form_id)&&!isset($_REQUEST["form_id"])){
	if(!empty($_SESSION["finalize_id"])){
		$form_id = $_SESSION["finalize_id"];
	}elseif(!empty($_SESSION["form_id"])){
		$form_id = $_SESSION["form_id"];
	}
}

if(!empty($_SESSION["patient"])){
	$oSaveFile = new SaveFile($_SESSION["patient"]);
}
$folder_id = $_REQUEST["folder_id"];
$oSaveFile->ptDir("Folder/id_".$folder_id);


/*
include_once($GLOBALS['incdir']."/common/functions.inc.php");
include_once($GLOBALS['incdir']."/admin/manage_folder/folder_function.php");
include_once($GLOBALS['incdir']."/admin/manage_folder/folder_function.php");
include_once($GLOBALS['incdir']."/chart_notes/common/SaveFile.php");
include_once($GLOBALS['incdir']."/main/main_functions.php");
include_once($GLOBALS["incdir"]."/chart_notes/common/scan_function.php");
include_once($GLOBALS["incdir"]."/chart_notes/common/functions.php");
*/

//$arrBrow = browser();
if((!isset($activex) && $activex!=1)){
	require_once('../../library/upload/server/php/UploadHandler.php');
	//start
		$options = array(
			'script_url' => $GLOBALS['php_server'].'/interface/chart_notes/upload_test.php',
			'upload_dir' => $oSaveFile->upDir.$oSaveFile->pDir.'/Folder/id_'.$folder_id.'/',
			'upload_url' => $oSaveFile->upDirWeb.$oSaveFile->pDir.'/Folder/id_'.$folder_id.'/',
			'access_control_allow_origin' => '*','access_control_allow_credentials' => false,
			'access_control_allow_methods' => array('OPTIONS','HEAD','GET','POST','PUT','PATCH','DELETE'),
			'access_control_allow_headers' => array('Content-Type','Content-Range','Content-Disposition'),
			'inline_file_types' => '/\.(gif|jpe?g|png)$/i', 'accept_file_types' => '/\.(gif|jpe?g|png|pdf|tif|tiff)$/i',
			'max_file_size' => null,'min_file_size' => 1,'max_number_of_files' => null,'max_width'=>null,'max_height'=>null,'min_width'=>1,'min_height'=>1,
			'discard_aborted_uploads'=>true,'orient_image'=>false,'image_versions'=>array( 'thumb' => array('max_width' => 500,'max_height' => 300),'thumbnail' => array('max_width' => 130,'max_height' => 150)),
		);
	
		$upload_handler = new UploadHandler($options,true);
		$response = (object) $upload_handler->response;//pre($response);die;
	
		if( $response->files )
		{
			$upload_dir = $oSaveFile->pDir.'/Folder/id_'.$folder_id;
			foreach($response->files as $file)
			{
				if($file->type && !$file->error && $file->url )
				{
					$tmp_document_scan_id="";
					$file_type = $file->type;
					$file_type = str_replace('image/','',$file_type);
					$file_type = str_replace('application/','',$file_type);
					$file_type = str_replace('jpeg','jpg',$file_type);
					$file_path_db = $oSaveFile->pDir.'/Folder/id_'.$folder_id.'/'.$file->name;
					if(file_exists($oSaveFile->upDir.$oSaveFile->pDir.'/Folder/id_'.$folder_id.'/'.$file->name)){
						$upload_docs_date_val = date('Y-m-d H:i:s');
						if($editid==""){
							$saveQry = " INSERT INTO ";	
							$whrQry = "";
						}else {
							$saveQry = " INSERT INTO ";	
							$whrQry = " WHERE scan_doc_id = '".$editid."' ";
						}
						$query = $saveQry.constant("IMEDIC_SCAN_DB").".scan_doc_tbl  set 
									patient_id				=	'".$pid."',
									folder_categories_id	=	'".$folder_id."',
									upload_operator_id		=	'".$userauthorized."',
									upload_docs_date		=	'".$upload_docs_date_val."',
									doc_upload_type			=	'upload',
									doc_title				=	'".$file->name."',
									doc_type				=	'".$file_type."',
									doc_size				=	'".$file->size."',
									vf						=	'".$vf."',
									file_path 				= 	'".$file_path_db."',
									pdf_url 				= 	'$url'
									".$whrQry;
						$res = imw_query($query) or die(imw_error());
						if($editid==""){
							$insertId 	= imw_insert_id();
						}else {
							$insertId 	= 	$editid;
						}
						$tmp_document_scan_id = $tmp_document_scan_id.$insertId.",";
						$insrtLog = providerViewLogFunNew($insertId,$_SESSION['authId'],$_SESSION['patient'],'scan');
					}else{
						$files[$index]->error = "File not uploaded plz try again.";
					}
				}
			}
		}

	
	//end
	

//$upload_handler->fDir 	= '/Scan/'.$formName;
//$file_pointer 			= $upload_handler->pDir.$upload_handler->fDir;
/*
$upload_handler->generate_response(
            array($upload_handler->options['param_name'] => $files),
            true
);*/
}
/*
$arrSessionData = array("ses_test_id" => $testId, "ses_form_name" => $formName, "ses_frm_id" => $frm_id);

unset($_SESSION['ARR_SESSION_TEST_DATA']);
$_SESSION['ARR_SESSION_TEST_DATA'] = NULL;
$_SESSION['ARR_SESSION_TEST_DATA'] = "";					
$_SESSION['ARR_SESSION_TEST_DATA'] = $arrSessionData;
*/