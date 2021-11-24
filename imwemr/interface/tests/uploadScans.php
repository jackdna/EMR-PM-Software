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
require_once(dirname(__FILE__)."/../../config/globals.php");
require_once(dirname(__FILE__)."/../../library/patient_must_loaded.php");
require_once(dirname(__FILE__)."/../../library/classes/class.tests.php");
require_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
$objTests				= new Tests;
$patient_id 	= $_SESSION['patient'];
//MAKING OBJECT TO SAVE IMAGE FILES
$library_path 		= $GLOBALS['webroot'].'/library';

$patient_id 	= $_SESSION['patient'];
$formName 		= $_REQUEST['formName'];
$form_id 		= $_REQUEST["form_id"];
$frm_id 		= $_REQUEST["form_id"]; //Used this to insert
$edit_id 		= $_REQUEST["edit_id"];
$userauthorized = $_SESSION['authId'];
$testId 		= $_REQUEST["testId"];
$test_master_id = $_REQUEST["test_master_id"];

if(!empty($patient_id)) $oSaveFile = new SaveFile($patient_id);

if(empty($form_id)){
	if(!empty($_SESSION["finalize_id"])) $form_id = $_SESSION["finalize_id"];
	else if(!empty($_SESSION["form_id"]))$form_id = $_SESSION["form_id"];
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
							`".$this_test_cols['performed_key']."`	= '$userauthorized'	";

			if($this_test_cols['test_table']=='test_other' && $this_test_cols['test_type']=='1'){			
					$query_insert .= ", `test_template_id` = '".$this_test_cols['id']."'";
			}else if($this_test_cols['test_table']=='test_custom_patient' && $this_test_cols['test_type']=='1'){			
					$query_insert .= ", `test_template_id`  = '".$this_test_cols['id']."', 
										`version` 			= '".$this_test_cols['version']."'";
			}
			
			if(!empty($query_insert)){
				$result_insert = imw_query($query_insert);
				//file_put_contents('test_query.txt',$query_insert."\n\n".imw_error()."\n\n".serialize($this_test_cols));			
				$testId	= imw_insert_id();
				$tmparr[$formName] = $testId;
				$_SESSION["test2edit"] = serialize($tmparr);
			}
		}
	}
}

$today = date('Y-m-d');
error_reporting(0);
$files_to_upload = 1;
$_POST    = $HTTP_POST_VARS;
$_GET     = $HTTP_GET_VARS;
//$_SESSION = $HTTP_SESSION_VARS;
if($_REQUEST['method']){
	if($_REQUEST['method'] == "upload"){
		$uploads = false;
		for($i = 0 ; $i < $files_to_upload; $i++){

			if($_FILES["file"]['name'][$i]){
				$uploads = true;
				if($_FILES["file"]['name'][$i]){
					//$original_file=$fileinfo;
					$fileName = $_FILES["file"]['name'][$i];
					$fileType = $_FILES["file"]['type'][$i];
					$PSize = $_FILES["file"]['size'][$i];
					$tmp_file = $_FILES["file"]["tmp_name"][$i];
					
					//$TempFile = fopen($_FILES["file"]["tmp_name"][$i], "r");
					//$fileCon = addslashes(fread($TempFile, $PSize));				

					if($_POST) $comment = $_POST['comments'];
					 
					$original_file=array();
					$original_file["name"]=$fileName;
					$original_file["type"]=$fileType;
					$original_file["size"]=$PSize;
					$original_file["tmp_name"]=$tmp_file;
					
					//Copy File --
					$file_pointer = $oSaveFile->copyfile($original_file,"Scan/".$formName);
					
					/****FIX FOR OCTET STREAM JPG IMAGES***/
					if($fileType=='application/octet-stream'){
						$extension = end(explode(".", $fileName));
					 	$fileType  = $oSaveFile->getMiME($extension);
					}

					/****FIX END FOR OCTET STREAM JPG IMAGES***/
					
					if(isset($edit_id) && $edit_id>0){
						$updateStrQry = imw_query("update  ".constant("IMEDIC_SCAN_DB").".scans SET
												image_name = '$fileName',
												file_type = '$fileType',
												modi_date = now(),
												file_path = '".$file_pointer."',
												testing_docscan_operator='$userauthorized' where scan_id='$edit_id'");					
					 }else{
						$insertId = "";
						$qry = "INSERT INTO ".constant("IMEDIC_SCAN_DB").".scans SET
														patient_id = '$patient_id',".
														"form_id = '$form_id',".
														$frm_doc_rel_time.
														"test_id = '".$testId."', ".
														"image_form = '$formName', ".
														//"image_contents = '$fileCon',".
														"image_name = '$fileName',
														file_type = '$fileType',
														scan_or_upload='scan',
														created_date = now(),
														file_path = '".$file_pointer."',
														testing_docscan_operator='$userauthorized'";						
						
						$insertStrQry = imw_query($qry);					
						$insertId = imw_insert_id();
						$_SESSION['document_scan_id']=$insertId;

						//COMMENTED TEMPORARILIY.
						//providerViewLogFun($insertId,$_SESSION['authId'],$_SESSION['patient'],'tests');
					}
					
					$message .= $fileName." uploaded.<br>";
				}
			}
		}
		if(!$uploads)  $message = "No files selected!";
	}
}

$arrSessionData = array("ses_test_id" => $testId, "ses_form_name" => $formName, "ses_frm_id" => $frm_id);

unset($_SESSION['ARR_SESSION_TEST_DATA']);
$_SESSION['ARR_SESSION_TEST_DATA'] = NULL;
$_SESSION['ARR_SESSION_TEST_DATA'] = "";					
$_SESSION['ARR_SESSION_TEST_DATA'] = $arrSessionData;
?>