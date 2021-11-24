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
$library_path = $GLOBALS['webroot'].'/library';

//require_once(dirname(__FILE__).'/../../main/Functions.php');
//$objManageData = new ManageData;
if(empty($template_id) === false){
	$templateDataArr = array();
	$templateDataArr['patient_id'] = $_SESSION['patient'];
	$templateDataArr['pt_doc_primary_template_id'] = $template_id;
	$templateDataArr['pt_doc_primary_template_id'] = $template_id;
	$templateDataArr['pt_enable_footer'] = $enable_footer;
	$templateDataArr['template_content'] = $pt_docs_template_content;
	/*	REPLACING SMART TAG OPTONS WITH NON-ANCHOR STRING. */
	$regpattern='|<a class=\"cls_smart_tags_link\" id=(.*) href=(.*)>(.*)<\/a>|U'; 
	$templateDataArr['template_content'] = preg_replace($regpattern, "\\3", $templateDataArr['template_content']);
	$regpattern='|<a id=(.*) class=\"cls_smart_tags_link\" href=(.*)>(.*)<\/a>|U'; 
	$templateDataArr['template_content'] = preg_replace($regpattern, "\\3", $templateDataArr['template_content']);
	/*--SMART TAG REPLACEMENT END--*/
	$templateDataArr['created_date'] = date('Y-m-d h:i:s');
	$templateDataArr['operator_id'] = $_SESSION['authId'];
	$templateDataArr['template_delete_status'] = 0;
	AddRecords($templateDataArr, 'pt_docs_patient_templates');
}
?>
<script type="text/javascript">
	 
	top.alert_notification_show("Record Saved Successfully");
	top.core_set_pt_session(top.fmain,'<?php echo $_SESSION['patient'];?>','../chart_notes/scan_docs/pt_docs.php?doc_name=pt_docs_template');
</script>