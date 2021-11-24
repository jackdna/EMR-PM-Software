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

?><?php 
include '../../config/globals.php';
include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");
//include_once($GLOBALS['fileroot']."/library/html_to_pdf/html2pdf.class.php");
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

$height = $_REQUEST['page'];
$font_size = $_REQUEST['font_size'];
$tld = $_REQUEST['tld'];
$page_size = $_REQUEST['page_size'];
$folder_id = $_REQUEST['folder_id'];
$edit_id = $_REQUEST['edit_id'];
$comments=addslashes($_REQUEST['comments']);
$task_physician_id = $_REQUEST['task_physician_id'];
$quickScan = (isset($_REQUEST['qs']) && $_REQUEST['qs']) ? true : false;

$patient_id = $_SESSION['patient'];
$data_dir = $GLOBALS['fileroot'].'/data/'.constant('PRACTICE_PATH'); 
$patient_dir = $data_dir."/PatientId_".$patient_id; 

$userauthorized = $_SESSION['authId'];

$tmp_scan_path = $GLOBALS['fileroot'].'/data/'.constant('PRACTICE_PATH')."/PatientId_".$patient_id.'/tmp_scan/'; 
$htmlFileName = isset($_REQUEST['htmlFileName']) ? $_REQUEST['htmlFileName'] : 'pdfFile';
$fp = fopen($tmp_scan_path.$htmlFileName.".html","r");
$strContent = fread($fp, filesize($tmp_scan_path.$htmlFileName.".html"));
fclose($fp);

if(!$tld){
	$tld = 'p';
}
if(!$page_size){
	$page_size = 'A4';
}
if(!empty($patient_id)){
	$oSaveFile = new SaveFile($patient_id);	
}

//START
$file_pointer = "";
$folderpath = "/PatientId_".$patient_id."/Folder/id_".$folder_id;
$savePdfFilePath = $data_dir.$folderpath;
$oSaveFile->ptDir("Folder/id_".$folder_id);
$file_name = date(get_date_format(date('Y-m-d'),'',inter_date_format(),4,'_').'_H_i_s');
$file_name_pdf = $file_name.'.pdf';
if(file_exists($savePdfFilePath.'/'.$file_name_pdf)) {
	unlink($savePdfFilePath.'/'.$file_name_pdf);	
}
$file_pointer = $folderpath.'/'.$file_name_pdf;
/*
$op = 'p';
$html2pdf = new HTML2PDF($tld,$page_size,'en');
$html2pdf->setTestTdInOnePage(false);
$html2pdf->WriteHTML(utf8_decode(html_entity_decode($strContent)), isset($_GET['vuehtml']));
$html2pdf->Output($savePdfFilePath.'/'.$file_name_pdf,'F');
*/
try {
	$op = strtoupper($tld);
	$html2pdf = new Html2Pdf($op,$page_size,'en');
	$html2pdf->setTestTdInOnePage($onePage);
	$html2pdf->writeHTML(utf8_decode(html_entity_decode($strContent)), isset($_GET['vuehtml']));
	$newFileName=$html2pdf->output($savePdfFilePath.'/'.$file_name_pdf,'F');
} catch (Html2PdfException $e) {
	$html2pdf->clean();
	$formatter = new ExceptionFormatter($e);
	echo $formatter->getHtmlMessage();
}
$contentExist=false;
if(stristr($strContent,"<img ")) {
	$contentExist=true;	
}
//END

$path = $patient_dir.'/tmp_scan/';
$editQry = "";
$upload_date=date("Y-m-d H:i:s");
if($contentExist==true) {
	$editQry="pdf_url = '$file_name',file_path = '".$file_pointer."',doc_type = 'pdf',doc_title='".$file_name."',doc_size='',
					doc_upload_type='scan',scandoc_operator_id='".$userauthorized."',upload_date='".$upload_date."',";
}
//Copy

if(empty($edit_id))
{
    $qry = "INSERT into ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl set pdf_url = '$file_name',folder_categories_id = '$folder_id',
			patient_id = '".$patient_id."',
			scandoc_comment = '".$comments."',
			task_physician_id = '".$task_physician_id."',
			task_status ='0',
			file_path = '".$file_pointer."',
			doc_upload_type='scan',scandoc_operator_id='".$userauthorized."',upload_date='".$upload_date."'";
	
	$res = imw_query($qry);
	$insertId =imw_insert_id();
	$_SESSION['scan_doc_id']=$insertId;
}
else if(!empty($edit_id))
{
	$qry_get = "SELECT * from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where scan_doc_id = '$edit_id'";
	$res_get = imw_query($qry_get);
	$row_get = imw_fetch_array($res_get);
	
	$fileName = $row_get['pdf_url'].".pdf";
	if(file_exists($path.'/'.$fileName)){
		unlink($path.'/'.$fileName);
	}
	if(file_exists($path.'\\'.$fileName)){
		unlink($path.'\\'.$fileName);
	}
	
	//Unlink
	//$oSaveFile->unlinkfile($row_get['file_path']);
	$qry = "UPDATE ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl set folder_categories_id = '$folder_id',
				patient_id = '".$patient_id."',
				$editQry
			 	task_physician_id='".$task_physician_id."',
			 	scandoc_comment = '".$comments."',
				upload_comment = '".$comments."'
			 	where scan_doc_id = '".$edit_id."'";	
	$res = imw_query($qry);
    $insertId =$edit_id;
	$_SESSION['scan_doc_id']=$insertId;
}
//START CODE TO CREATE LOG OF SCAN
	$scanProviderLog = providerViewLogFunNew($insertId,$_SESSION['authId'],$_SESSION['patient'],'scan');
//END CODE TO CREATE LOG OF SCAN

$ab =  opendir($path);
while(($filename = readdir($ab)) !== false)
	{
		$path1 = pathinfo($filename);
		if($path1['extension'] == 'jpg')
		{
			@unlink($path.'/'.$filename);
		}
		if($path1['extension'] == 'jpg')
		{
			@unlink($path.'\\'.$filename);
		}				
	}
//$flPth = $GLOBALS['rootdir']."/chart_notes/folder_category.php?cat_id=".$folder_id;
$flPth = $GLOBALS['rootdir']."/chart_notes/scan_docs/scan_docs.php?doc_name=scan_docs&cat_id=".$folder_id;
if( $quickScan ) {
	$flPthJpg = $GLOBALS['rootdir']."/chart_notes/scan_documents.php?t=sch&a=iqs&sb=no&al=sh&folder_id=".$folder_id;
	header('Location:'.$flPthJpg);	
}
else {
?>
<script>
	if(top.frames['fmain']) {
		top.frames['fmain'].location.href = '<?php echo $flPth; ?>';
	}
	//top.frames['fmain'].frames['ifrm_FolderContent'].location.href = '<?php //echo $flPth; ?>';
	if(typeof(top.fmain.refrashNavi) == "function"){
		//top.fmain.refrashNavi();
	}
</script>
<?php } ?>