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
File: folder_scan_date.php
Purpose: This file saves scan date in Folder functionality.
Access Type : Direct
*/
?>
<?php
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
$library_path = $GLOBALS['webroot'].'/library';
$pid = $_SESSION['patient'];
//include_once("../admin/manage_folder/folder_function.php");


$oSaveFile = new SaveFile($pid);
$GLOBALDATEFORMAT = $GLOBALS["date_format"];
$type=$_REQUEST['type'];
$file_name = $_REQUEST['file_name'].".pdf";
if($_REQUEST['cdate']<>"")	{
	$dat=explode("-",trim($_REQUEST['cdate'])); //m/d/y
	if($GLOBALDATEFORMAT == "dd-mm-yyyy" && $GLOBALDATEFORMAT != "")
	{
		$chartdate2=$dat[2]."-".$dat[1]."-".$dat[0];
	}
	else
	{
		$chartdate2=$dat[2]."-".$dat[0]."-".$dat[1];
	}
	//$chartdate2=$dat[2]."-".$dat[0]."-".$dat[1];
}
if($_REQUEST['id']<>"" && $_REQUEST['cdate']<>"" && $_REQUEST['type']=="update")	{
	imw_query("update ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl set chart_note='yes', chart_note_date='$chartdate2' where scan_doc_id='".$_REQUEST['id']."'");
}else if($_REQUEST['type']=="del")	{
	$sql 	= "SELECT file_path from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where scan_doc_id='".$_REQUEST['id']."'";
	$sqlRes = imw_query($sql);
	$row 	= imw_fetch_assoc($sqlRes);
	if($row != false){
		if(!empty($row["file_path"])){
			$oSaveFile->unlinkfile($row["file_path"]);
		}
	}

	imw_query("delete from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where scan_doc_id='".$_REQUEST['id']."'");	
	if(file_exists('../main/demoApplet/uploaddir/'.$file_name)) {
		@unlink('../main/demoApplet/uploaddir/'.$file_name);
	}
	imw_query("DELETE FROM provider_view_log_tbl WHERE scan_doc_id='".$_REQUEST['id']."' AND section_name='scan'");//DELETE LOG	OF SCAN

}else if($_REQUEST['type']=="move")	{
	imw_query("update ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl set folder_categories_id='".$_REQUEST['folder_id']."'  where scan_doc_id='".$_REQUEST['file_id']."'");
	//imw_query("update ".constant("IMEDIC_SCAN_DB").".folder_categories  set patient_id=$pid  where folder_categories_id='".$_REQUEST['folder_id']."'");
}
if($_REQUEST['type']=="remove")	{
	$qry="Select * from ".constant("IMEDIC_SCAN_DB").".folder_categories where folder_categories_id ='".$_REQUEST['folder_id']."'";
	$res=imw_query($qry);
	while($row=imw_fetch_array($res))	{
		if(delete_folder($row['folder_categories_id']))	{
			imw_query("Delete from ".constant("IMEDIC_SCAN_DB").".folder_categories where folder_categories_id ='".$_REQUEST['folder_id']."'");
			imw_query("Delete from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where folder_categories_id='".$_REQUEST['folder_id']."'");
		}
	}
}
//for alert messages
//echo $type=$_REQUEST['type'];
echo $type;

?>
