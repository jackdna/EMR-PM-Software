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
include '../../config/globals.php';
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
$scan_doc_id = $_SESSION['document_scan_id'];
$userauthorized = $_SESSION['authId'];
$patient_id = $_SESSION['patient'];
$folder_id = $_REQUEST['folder_id'];
$editid = $_REQUEST['edit_id'];
$upload_dir = "uploaddir";
$quickScan = (isset($_REQUEST['qs']) && $_REQUEST['qs']) ? true : false;
if(!empty($patient_id)){
	$oSaveFile = new SaveFile($patient_id);
	$oSaveFile->ptDir("tmp_scan");
	$oSaveFile->ptDir("Folder/id_".$folder_id);
}
//$path = "uploaddir/PatientId_".$patient_id."/uploaddir";//'//192.168.0.3/documents/test';
$path = data_path()."PatientId_".$patient_id."/tmp_scan";
//$path_jpg = "uploaddir/PatientId_".$patient_id."/uploadJPG";
$ab = (is_dir($path)) ? opendir($path) : false ;
if($ab){
while(($filename = readdir($ab)) !== false)
	{
		$path1 = pathinfo($filename);
		if($path1['extension'] == 'jpg')
		{
			$filesize = filesize($path.'/'.$filename);
			$doctitle = $filename;			
			$orgFile = array();
			$orgFile["name"] = $path1['filename'];
			$orgFile["type"] =  filetype ($path.'/'.$filename);
			$orgFile["size"] = $filesize;
			//$orgFile["tmp_name"]= data_path();
			$orgFile["tmp_name"]= $path.'/'.$filename;
			upload_image_by_guru($patient_id,$doctitle,$orgFile,$filename,'jpg',$filesize,$path.'/'.$filename,$vf,$folder_id,$editid,$filename,$_REQUEST['comments'], $_REQUEST['task_physician_id']);
		}else if($editid && ($_REQUEST['comments'] || $_REQUEST['task_physician_id'])) {
			$updtCmntQry = "update ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl  set 
							scandoc_comment='".$_REQUEST['comments']."',
							upload_comment='".$_REQUEST['comments']."',
							task_physician_id='".$_REQUEST['task_physician_id']."'
							where scan_doc_id='".$editid."'";	
			$updtCmntRes=imw_query($updtCmntQry) or die(imw_error());										
		}
	}
}	
//$flPthJpg = $GLOBALS['rootdir']."/chart_notes/folder_category.php?cat_id=".$folder_id;
$flPthJpg = $GLOBALS['rootdir']."/chart_notes/scan_docs/scan_docs.php?doc_name=scan_docs&cat_id=".$folder_id;
if( $quickScan ) {
	$flPthJpg = $GLOBALS['rootdir']."/chart_notes/scan_documents.php?t=sch&a=iqs&sb=no&al=sh&folder_id=".$folder_id;
	header('Location:'.$flPthJpg);	
}
else {
?>
<script language="javascript">
	if(top.frames['fmain']) {
		top.frames['fmain'].location.href = '<?php echo $flPthJpg; ?>';
	}
</script>
<?php } ?>