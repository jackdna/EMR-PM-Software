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
require_once(dirname(__FILE__) . '/../../config/globals.php');
require_once($GLOBALS['fileroot'] . '/library/classes/msgConsole.php');
/*--REVIEWING SELECTED AND SAVING COMMENTS BELOW--*/
if(isset($_POST['task_form_submit'])) {//UPDATE CASE
    $message_id = isset($_POST['message_id']) ? $_POST['message_id'] : array();
    $hidd_comment = isset($_POST['hidd_comment']) ? $_POST['hidd_comment'] : array();
	if(count($message_id)>0){//DELETE CASE
		$messageId = array();
		$taskId = array();
		for($i=0;$i<count($message_id);$i++){
			$message_id_arr = preg_split("/_/",$message_id[$i]);
			$taskId[] = "'".$message_id_arr[1]."'";
			
		}
		$taskIdStr = join(',',$taskId);
		$qry = "UPDATE ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl SET task_status = '2' where scan_doc_id in($taskIdStr)";
		$result = imw_query($qry);
	}

	if($hidd_comment) {
		foreach($hidd_comment as $key => $docType) {
			$cmntQry=" scandoc_comment = '".$_REQUEST['comment_'.$key]."' ";
			if($docType=="scan") {
				$cmntQry=" scandoc_comment = '".$_REQUEST['comment_'.$key]."' ";	
			}else if($docType=="upload") {
				$cmntQry=" upload_comment = '".$_REQUEST['comment_'.$key]."' ";	
			}
			$updtQry = " UPDATE ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl SET ".$cmntQry." WHERE scan_doc_id='".$key."'; ";		
			$updtRes = imw_query($updtQry) or die(imw_error());
		}
		echo 'tasksaved';
		exit;
	}
}

?>