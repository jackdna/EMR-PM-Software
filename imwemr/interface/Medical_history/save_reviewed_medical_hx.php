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
File: save_reviewed_medical_hx.php
Purpose: Saves medical history review information
Access Type: Direct 
*/
require_once("../../config/globals.php");
require_once("../../library/patient_must_loaded.php");
require_once($GLOBALS['srcdir']."/classes/medical_hx/medical_history.class.php");
$showpage=(isset($_REQUEST['showpage']) && $_REQUEST['showpage']!='')?trim($_REQUEST['showpage']):'ocular';
if($showpage == 'medication') $showpage = 'medications';
$medical = new MedicalHistory($showpage);
$dt_now = date("Y-m-d H:i:s");    
$operator_id = $_REQUEST["operator_id"];
$patient_id = $_REQUEST["patient_id"];
$section_name = $_REQUEST["section_name"];

$section = $section_name;
switch($section)
{
	case "defaultTD":
		$section = "Ocular Hx";		
	break;
	case "general_health":
		$section = "General Health";
	break;	
	case "medication":
		$section = "Medications";
	break;	
	case "sx_procedures":
		$section = "Sx/Procedure";
	break;
	case "allergies":
		$section = "Allergies";
	break;	
	case "immunizations":
		$section = "Immunizations";
	break;
	case "family_hx":
		$section = "Family Hx";
	break;				
	case "cc_history":
		$section = "CC&History";
	break;
	case "cc_history":
		$section = "CC&History";
	break;
	case "vs":
		$section = "VS";
	break;
	case "lab":
		$section = "Lab";
	break;	
	case "radiology":
		$section = "Radiology";
	break;
	case "complete":
		$section = "complete";
	break;
}
if($section != "complete"){
	$qrySelPatLastExamined = "select patient_last_examined_id from patient_last_examined 
							where patient_id = '".$patient_id."' and operator_id = '".$operator_id."' and section_name = '".$section."' and save_or_review = '1'";
	$rsSelPatLastExamined = imw_query($qrySelPatLastExamined);	
	if($rsSelPatLastExamined){
		if(imw_num_rows($rsSelPatLastExamined) > 0){
			$rowSelPatLastExamined = imw_fetch_array($rsSelPatLastExamined);
			$masterPatLastExamId = $rowSelPatLastExamined['patient_last_examined_id']; 						
			$qryUpdatePatLastExamined = "update patient_last_examined set created_date = '".$dt_now."',save_or_review = '2' where patient_last_examined_id = '".$masterPatLastExamId."'";
			$rsUpdatePatLastExamined = imw_query($qryUpdatePatLastExamined);
		}
		else{
			$qryInsertPatLastExamined = "insert into patient_last_examined (patient_id,operator_id,section_name,created_date,status,save_or_review) 
								VALUES 
								('".$patient_id."','".$operator_id."','".$section."','".$dt_now."','0','2')";
			$rsInsertPatLastExamined = imw_query($qryInsertPatLastExamined);																		
		}		
		imw_free_result($rsSelPatLastExamined);			
	}				

	if($section == "General Health"){
		$sectionTemp = "General Health - Advanced Directive";
		$qrySelPatLastExamined = "select patient_last_examined_id from patient_last_examined 
								where patient_id = '".$patient_id."' and operator_id = '".$operator_id."' and section_name = '".$sectionTemp."' and save_or_review = '1'";
		$rsSelPatLastExamined = imw_query($qrySelPatLastExamined);	
		if($rsSelPatLastExamined){
			if(imw_num_rows($rsSelPatLastExamined) > 0){
				$rowSelPatLastExamined = imw_fetch_array($rsSelPatLastExamined);
				$masterPatLastExamId = $rowSelPatLastExamined['patient_last_examined_id']; 						
				$qryUpdatePatLastExamined = "update patient_last_examined set created_date = '".$dt_now."',save_or_review = '2' where patient_last_examined_id = '".$masterPatLastExamId."'";
				$rsUpdatePatLastExamined = imw_query($qryUpdatePatLastExamined);
			}
			imw_free_result($rsSelPatLastExamined);			
		}
	}		
}
elseif($section == "complete"){
	$qrySelPatLastExamined = "select patient_last_examined_id,section_name from patient_last_examined 
							where patient_id = '".$patient_id."' and operator_id = '".$operator_id."' and save_or_review = '1'";
	$rsSelPatLastExamined = imw_query($qrySelPatLastExamined);	
	if($rsSelPatLastExamined){
		if(imw_num_rows($rsSelPatLastExamined) > 0){
			$qryInsertPatLastExamined = "insert into patient_last_examined set patient_id = '".$patient_id."',operator_id = '".$operator_id."',section_name = '".$section."',created_date = '".$dt_now."', status = '0',save_or_review = '2'";
			$rsInsertPatLastExamined = imw_query($qryInsertPatLastExamined);
			$patLastExaminedInsertId = imw_insert_id();
			while($rowSelPatLastExamined = imw_fetch_array($rsSelPatLastExamined)){
				$masterPatLastExamId = $rowSelPatLastExamined['patient_last_examined_id']; 
				$sectionName = $rowSelPatLastExamined['section_name']; 						
				$qryUpdatePatLastExamined = "update patient_last_examined set created_date = '".$dt_now."', save_or_review = '2',section_name = '".$sectionName."', section_complete = '1',section_complete_id = '".$patLastExaminedInsertId."' where patient_last_examined_id = '".$masterPatLastExamId."'";
				$rsUpdatePatLastExamined = imw_query($qryUpdatePatLastExamined);
			}
		}																
		else{
			$sql = "insert into patient_last_examined set patient_id = '".$patient_id."',operator_id = '".$operator_id."',section_name = '".$section."',created_date = '".$dt_now."',status = '0',save_or_review = '2'";
			imw_query($sql);																	
		}		
		imw_free_result($rsSelPatLastExamined);			
	}				
	
}


//$curr_tab_title = $medical->get_tab_title($showpage);
echo $section;
die;
?>