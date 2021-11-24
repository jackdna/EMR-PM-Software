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
* Purpose: Documents Common Ajax Handler
* Access Type: Direct 
*
****************************************************************************/
//For iDoc App
$ignoreAuth = false;
if(isset($_REQUEST['source']) && empty($_REQUEST['source']) == false) $ignoreAuth = true;

require_once("../../../config/globals.php");
require_once($GLOBALS['srcdir']."/classes/admin/documents/document.class.php");
require_once($GLOBALS['srcdir']."/classes/admin/documents/encoding.php");

$_REQUEST = array_map('trim',$_REQUEST);
$doc_obj = new Documents($_REQUEST['current_tab']);
$cur_tab = (isset($_REQUEST['current_tab'])) ? $_REQUEST['current_tab'] : $doc_obj->current_tab;
$update_field = '';
$enc_obj = new Encoding();
$enc_obj::fixUTF8($_REQUEST);

switch($cur_tab){
	case 'collection':
	case 'op_notes':
		if(isset($_REQUEST['saveBtn']) && empty($_REQUEST['saveBtn']) == false){  
			$action = $_REQUEST['perform_action'];
			$return = $doc_obj->manipulate_data($action,$_REQUEST);	//Performs Add and Update
		}
		$update_field = ($cur_tab == 'collection') ? 'id' : 'temp_id'; //True => collection && False => op_notes
	break;
	
	case 'surgery_consent':
			if(isset($_REQUEST['saveBtn']) && empty($_REQUEST['saveBtn']) == false){  
			$action = $_REQUEST['perform_action'];
			$return = $doc_obj->manipulate_data($action,$_REQUEST);	//Performs Add and Update
		}
		$update_field = 'consent_id';
	break;
	
	case 'consent':
		$old_ver_id = (isset($_REQUEST['old_version_id']) && empty($_REQUEST['old_version_id']) == false) ? $_REQUEST['old_version_id'] : '';
		if(empty($_REQUEST['consent_form_name']) == false && empty($old_ver_id) && empty($_REQUEST['consent_cat']) == false){
			$action = $_REQUEST['perform_action'];
			$return = $doc_obj->manipulate_data($action,$_REQUEST);	// Performs Add and Update for consents
		}
		$update_field = 'consent_form_id';
	break;
	
	case 'package':
		$task = (isset($_REQUEST['pac_mode']) && empty($_REQUEST['pac_mode']) == false) ? $_REQUEST['pac_mode'] : '';
		$return = $doc_obj->manipulate_data($task,$_REQUEST);	//Performs Update,Add and Delete for packages
	break;
	
	case 'consult':
		if(isset($_REQUEST['saveBtn']) && empty($_REQUEST['saveBtn']) == false){
			$action = $_REQUEST['perform_action'];
			//Updating Template Type first
			if(isset($_REQUEST['templateType']) && empty($_REQUEST['templateType']) == false){
				$templateType = $_REQUEST['templateType'];
				if($templateType == 'fax_cover_letter') {
					$arrTempType['consultTemplateType'] = '';
					$doc_obj->updateRecords($arrTempType, $doc_obj->cur_tab_table, 'consultTemplateType', 'fax_cover_letter');	
				}elseif($templateType == 'leftpanel'){
					$arrTempType['consultTemplateType'] = '';
					$doc_obj->updateRecords($arrTempType, $doc_obj->cur_tab_table, 'consultTemplateType', 'leftpanel');
				}
			}
			
			//Updating the main template
			$return = $doc_obj->manipulate_data($action,$_REQUEST);	//Performs Add and Update
		}
		$update_field = 'consultLeter_id';
	break;
	
	case 'education':
	case 'instructions':
		$update_field = 'id';
	break;
	
	case 'recall':
		if(isset($_REQUEST['saveBtn']) && empty($_REQUEST['saveBtn']) == false){  
			$action = $_REQUEST['perform_action'];
			$return = $doc_obj->manipulate_data($action,$_REQUEST);	//Performs Add and Update
		}
		$update_field = 'recallLeter_id';
	break;
	
	case 'pt_docs':
		if(isset($_REQUEST['saveBtn']) && empty($_REQUEST['saveBtn']) == false){  
			$action = $_REQUEST['perform_action'];
			$return = $doc_obj->manipulate_data($action,$_REQUEST);	//Performs Add and Update
		}
		$update_field = 'pt_docs_template_id';
	break;
	
	case 'order_temp':
		if(isset($_REQUEST['saveBtn']) && empty($_REQUEST['saveBtn']) == false){  
			$action = $_REQUEST['perform_action'];
			$return = $doc_obj->manipulate_data($action,$_REQUEST);	//Performs Add and Update
		}
		$update_field = 'template_id';
	break;
	
	case 'logos':
		$update_field = 'id';
	break;
	
	case 'panels':
		if(isset($_REQUEST['saveBtn']) && empty($_REQUEST['saveBtn']) == false){  
			$action = $_REQUEST['perform_action'];
			$return = $doc_obj->manipulate_data($action,$_REQUEST);	//Performs Add and Update
		}
		$update_field = 'id';
	break;
	
	case 'prescriptions':
		if(isset($_REQUEST['saveBtn']) && empty($_REQUEST['saveBtn']) == false){  
			$action = $_REQUEST['perform_action'];
			$return = $doc_obj->manipulate_data($action,$_REQUEST);	//Performs Add and Update
		}
		$update_field = 'prescription_template_type';
	break;
	
	case 'smart_tags':
		$save_status = array();
		switch($_REQUEST['call_from']){
			//Retrieve Child Tags for the provided parent tag id
			case 'sub_tag':
				$save_status = $doc_obj->manage_smart_tags($_REQUEST['call_from'],$_REQUEST);
			break;
			
			//Retrieve all parent tags
			case 'main_tag':
				$save_status = $doc_obj->manage_smart_tags($_REQUEST['call_from'],$_REQUEST);
			break;
			
			//Returns smart tags in App for the provided variables
			case 'get_tags_app':
				$tagStatus = $doc_obj->manage_smart_tags($_REQUEST['call_from'],$_REQUEST);
				if(isset($tagStatus['counter']) && $tagStatus['counter'] > 0){
					echo $tagStatus['txt'];
					exit();
				}
			break;
			
			//Returns tags in editor for the provided tag id
			case 'get_editor_tags':
				$validate = false;
				$smart_id = (isset($_REQUEST['smart_id']) && empty($_REQUEST['smart_id']) == false) ? $_REQUEST['smart_id'] : '';
				if(empty($smart_id) == false) $validate = true;
				
				if($validate == true){
					$save_status = $doc_obj->manage_smart_tags($_REQUEST['call_from'],$_REQUEST);
				}else $save_status = $validate;
			break;
		}
		$return = $save_status;
		$update_field = 'id';
	break;
}

//Managing categories
if(isset($_REQUEST['modal_category']) && empty($_REQUEST['modal_category']) == false){	
	$return = $doc_obj->manipulate_categories($_REQUEST);	//Performs Add, Update and delete for categories
}

//Performs template delete
if(isset($_REQUEST['delId']) && empty($_REQUEST['delId']) == false){	
	$delId = $_REQUEST['delId'];
	$return = $doc_obj->delRecord($doc_obj->cur_tab_table, $update_field, $delId);	//Performs delete for templates
	if($return == 'delete_success'){
		switch($cur_tab){
			case 'education':
				imw_query("update  document_patient_rel set status='1' where doc_id='$delId'");
			break;
		}	
	}
}

if($return == 'delete_success'){
	$return = 'Record Deleted Successfully';
}
echo json_encode($return);
exit();
?>
