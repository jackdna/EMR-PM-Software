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
include_once("../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/SaveFile.php");
include_once($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");

$patient_id = $_SESSION['patient'];
$formName = $_REQUEST['formName'];
$form_id = $_REQUEST["form_id"];
$frm_id = $_REQUEST["form_id"]; //Used this to insert
$edit_id = $_REQUEST["edit_id"];
$userauthorized = $_SESSION['authId'];
if($formName=='ptInfoMedHxGeneralHealth')
{
	$qryScans="select scan_id,file_path from ".constant("IMEDIC_SCAN_DB").".scans where image_form='$formName' and patient_id='$patient_id'";
	$resultScans=imw_query($qryScans);
	$rowScans=imw_fetch_array($resultScans);
	{
		$oldFile = $rowScans['file_path'];
		$edit_id = $rowScans['scan_id'];
	}
}
if(!empty($patient_id)){
	$oSaveFile = new SaveFile($patient_id);
}

if(empty($form_id)){
	if(!empty($_SESSION["finalize_id"])){
		$form_id = $_SESSION["finalize_id"];
	}elseif(!empty($_SESSION["form_id"])){
		$form_id = $_SESSION["form_id"];
	}
}

$testId = $_REQUEST["testId"];
if(!empty($patient_id) && isset($formName) && !empty($formName)){
	//$form_id = 0;	
	if(empty($testId) && empty($editid)){
		$tmparr = array();				
		if(isset($_SESSION["test2edit"]) && !empty($_SESSION["test2edit"])){
			$tmparr = unserialize($_SESSION["test2edit"]);
		}

		if(isset($tmparr[$formName]) && !empty($tmparr[$formName])){
			$testId = $tmparr[$formName];

		}else{
			if($formName != "ptInfoMedHxGeneralHealth" && $formName != "ptInfoAdvancedDirective")
			$tmpId = checkTestDone($formName,$patient_id,$frm_id,$testId);
			if($tmpId != false){
				$testId = $tmpId;
			}else{
				
				$examDate=date('Y-m-d');
				
				//Insert into test table
				//use test_id for scan table
				//set test_id in session for test form
				switch($formName){
					case "VF":
						$sql = "INSERT INTO vf (vf_id, patientId, examDate, examTime,formId, performedBy) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";

					break;
					case "VF-GL":
						$sql = "INSERT INTO vf_gl (vf_gl_id, patientId, examDate, examTime,formId, performedBy) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";

					break;
					case "NFA":
						$sql = "INSERT INTO nfa (nfa_id, patient_id, examDate, examTime,form_id, performBy) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";
					break;
					case "OCT":
						$sql = "INSERT INTO oct (oct_id, patient_id, examDate, examTime,form_id, performBy) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";					
					break;
					case "OCT-RNFL":
						$sql = "INSERT INTO oct_rnfl (oct_rnfl_id, patient_id, examDate, examTime,form_id, performBy) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";					
					break;
					case "Pacchy":
						$sql = "INSERT INTO pachy (pachy_id, patientId, examDate, examTime,formId, performedBy) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";
					break;
					case "IVFA":
						$sql = "INSERT INTO ivfa (vf_id, patient_id, exam_date, examTime,form_id, performed_by) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";
					break;
					case "Disc":
						$sql = "INSERT INTO disc (disc_id, patientId, examDate, examTime,formId, performedBy) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";
					break;
					case "discExternal":
						$sql = "INSERT INTO disc_external (disc_id, patientId, examDate, examTime,formId, performedBy) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";
					break;
					case "Topogrphy":
						$sql = "INSERT INTO topography (topo_id, patientId, examDate, examTime,formId, performedBy) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";
					break;
					case "TestOther":
						$sql = "INSERT INTO test_other (test_other_id, patientId, examDate, examTime,formId, performedBy) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";
					break;
					case "TestLabs":
						$sql = "INSERT INTO test_labs (test_labs_id, patientId, examDate, examTime,formId, performedBy) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";
					break;
					case "Ascan":
						$sql = "INSERT INTO surgical_tbl (surgical_id, patient_id, examDate, examTime, form_id, performedByOD) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";
					break;
					case "IOL_Master":
						$sql = "INSERT INTO iol_master_tbl (iol_master_id, patient_id, examDate, examTime, form_id, performedByOD) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";
					break;					
					case "BScan":
						$sql = "INSERT INTO test_bscan (test_bscan_id, patientId, examDate, examTime,formId, performedBy) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";
					break;
					case "CellCount":
						$sql = "INSERT INTO test_cellcnt (test_cellcnt_id, patientId, examDate, examTime,formId, performedBy) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";
					break;
					case "GDX":
						$sql = "INSERT INTO test_gdx (gdx_id, patient_id, examDate, examTime,form_id, performBy) ".
								"VALUES(NULL, '".$patient_id."', '".$examDate."', NOW(),'".$frm_id."','".$userauthorized."') ";
					break;
					case "ICG":
						$sql = "INSERT INTO icg (icg_id, patient_id, exam_date , examTime ,form_id , performed_by) ".
								"VALUES(NULL, '".$_SESSION["patient"]."', '".$examDate."', NOW(),'".$frm_id."','".$operator_id."') ";
					break;
					//===============TemplateTests CASE ADDED HERE========================
					//BECAUSE WHEN ADD ANY NEW TEST FROM ADMIN, SO IN THAT CASE IT WAS NOT DISPLAY THEIR FIRST UPLOADED SCAN THUMBS  					//ON RIGHT SIDE OF TESTS
					case "TemplateTests":
						$sql = "INSERT INTO test_other (test_other_id, patientId, test_other, examDate, examTime,formId, performedBy,test_template_id) ".
								"VALUES(NULL, '".$_SESSION["patient"]."', 'TemplateTests', '".$examDate."', NOW(),'".$frm_id."','".$operator_id."','".$test_temp_id."') ";
					break;
				
				}		
				
				// Test Id
				if(!empty($sql)){
					$testId = sqlInsert($sql);				
					$tmparr[$formName] = $testId;
					$_SESSION["test2edit"] = serialize($tmparr);
					
					//---
					if(empty($frm_id)){									
						$tmp_dos = $examDate;
						$tmp_form_id=$testId;				
						insert_remote_sync($_SESSION["patient"], $tmp_dos, $tmp_form_id, $formName);
					}
					//---
					
				}
			}
		}
	}
}
;
$today = date('Y-m-d');
error_reporting(0);
$files_to_upload = 1;
$_POST    = $HTTP_POST_VARS;
$_GET     = $HTTP_GET_VARS;
//$_SESSION = $HTTP_SESSION_VARS;
$allowed_ext = array("jpeg","jpg", "gif", "png");
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
					
					if($_POST){$comment = $_POST['comments'];}
					
					if($formName=='chartnoteDocumentsRel'){
						$frm_doc_rel_time="form_id = '$frm_id',";
					}
					
					$original_file=array();
					$original_file["name"]=$fileName;
					$original_file["type"]=$fileType;
					$original_file["size"]=$PSize;
					$original_file["tmp_name"]=$tmp_file;
					

					// Check file extention
					$pathArr = pathinfo($original_file["name"]);
					$extension = $pathArr['extension'];
					
					if( !in_array($extension,$allowed_ext) ) {
					  $_SESSION['message'] = $fileName . " has invalid extension.<br>";
					  continue;
					}
					 
					// validate file content type
					if( !check_img_mime($original_file["tmp_name"]) ) {
					  $_SESSION['message'] = $fileName . " is an invalid image.<br>";
					  continue;
					}

					//Copy File --
					$file_pointer = $oSaveFile->copyfile($original_file,"Scan/".$formName);
					 //Copy File --
					
					 if(isset($edit_id) && $edit_id>0 && $formName!='ptInfoAdvancedDirective' && $formName!='ptInfoMedHxGeneralHealth'){
					 /*
					 if($formName=='ptInfoMedHxGeneralHealth')
					 {
					 	$compPath = substr(data_path(),0,-1).$oldFile;
						if(!empty($compPath))
						{
							unlink($compPath);
						}
					 }*/
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
														"image_name = '$fileName',
														file_type = '$fileType',
														scan_or_upload='scan',
														created_date = now(),
														file_path = '".$file_pointer."',
														testing_docscan_operator='$userauthorized'";						
						
						$insertStrQry = imw_query($qry);					
						$insertId = imw_insert_id();
						$_SESSION['document_scan_id']=$insertId;
						
						providerViewLogFun($insertId,$_SESSION['authId'],$_SESSION['patient'],'tests');
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