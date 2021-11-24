<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php 
require('html2fpdf.php');
//$pdf=new HTML2FPDF();
$pageType = stripslashes($_GET['AddPage']);
$pdf = new HTML2FPDF($pageType);
$fp = fopen("../testPdf.html","r");
$strContent = fread($fp, filesize("../testPdf.html"));
fclose($fp);
//$height = $_REQUEST['page'];
//$font_size = $_REQUEST['font_size'];
$height = 4;
$font_size = 10;

$pdf->AddPage();
$pdf->SetFont('Arial','',$font_size);
$pdf->FontSizePt=$font_size;
$pdf->UseCSS();
$pdf->lineheight = $height;
$pdf->WriteHTML($strContent);
$pdf->Output("doc.pdf");
if($_REQUEST['images']){
	$imagesArr = explode(',',$_REQUEST['images']);
	for($i=0;$i<count($imagesArr);$i++){
		@unlink($imagesArr[$i]); 
	}
}
header('location: doc.pdf');
?>