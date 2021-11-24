<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php 
require('html2pdf.php');
include_once("../../globals.php");
include_once("../../chart_notes/common/Savefile.php");



$fp = fopen("pdfFile.html","r");
$strContent = fread($fp, filesize("pdfFile.html"));
fclose($fp);
$height = $_REQUEST['page'];
$font_size = $_REQUEST['font_size'];
$tld = $_REQUEST['tld'];
$folder_id = $_REQUEST['folder_id'];
$edit_id = $_REQUEST['edit_id'];
$comments=addslashes($_REQUEST['comments']);
$patient_id = $_SESSION['patient'];
$userauthorized = $_SESSION['authId'];
if(!$tld){
	$tld = 'p';
}

if(!empty($patient_id)){
	$oSaveFile = new SaveFile($patient_id);	
}

$pdf=new HTML2FPDF($tld);
$pdf->AddPage();
$pdf->SetFont('Arial','',$font_size);
$pdf->FontSizePt=$font_size;
$pdf->UseCSS();
$pdf->lineheight = $height;
$pdf->WriteHTML($strContent);
$pdf->Output("pdfFile.pdf");
/*
if($_REQUEST['images']){
	$imagesArr = explode(',',$_REQUEST['images']);
	for($i=0;$i<count($imagesArr);$i++){
		@unlink($imagesArr[$i]); 
	}
}
*/
$path = "uploaddir/PatientId_".$patient_id."/uploaddir/";  //'//192.168.0.3/documents/test';
$file_name = date('d_m_Y_H_i_s');
//copy('pdfFile.pdf',$path.'\\'.$file_name.'.pdf');

$original_file = array();
$original_file["name"] = $file_name.'.pdf';
$original_file["type"] = filetype('pdfFile.pdf');
$original_file["size"] = filesize('pdfFile.pdf');
$original_file["tmp_name"] = dirname(__FILE__)."/pdfFile.pdf";
//Copy
$folderpath = "Folder/id_".$folder_id;
$file_pointer = $oSaveFile->copyfile($original_file,$folderpath);
//Copy
if(empty($edit_id))
{
    $qry = "INSERT into ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl set pdf_url = '$file_name',folder_categories_id = '$folder_id',
			patient_id = '$patient_id',
			scandoc_comment = '$comments',
			file_path = '".$file_pointer."',
			doc_upload_type='scan',scandoc_operator_id='$userauthorized',upload_date=now()";
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
	if(file_exists($path.'\\'.$fileName)){
		unlink($path.'\\'.$fileName);
	}

	//Unlink
	$oSaveFile->unlinkfile($row_get['file_path']);
		
	$qry = "UPDATE ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl set pdf_url = '$file_name',folder_categories_id = '$folder_id',
			patient_id = '$patient_id',
			file_path = '".$file_pointer."',
			doc_type = 'pdf',doc_title='$file_name',doc_size='', 
			scandoc_operator_id='$userauthorized',upload_date=now() where scan_doc_id = '$edit_id'";	
	$res = imw_query($qry);
    $insertId =imw_insert_id();
	$_SESSION['scan_doc_id']=$insertId;
}
$ab =  opendir($path);
while(($filename = readdir($ab)) !== false)
	{
		$path1 = pathinfo($filename);
		if($path1['extension'] == 'jpg')
		{
			@unlink($path.'\\'.$filename);
		}				
	}
header('location: '.$GLOBALS['rootdir']."/main/uploaddir".$file_pointer);
?>