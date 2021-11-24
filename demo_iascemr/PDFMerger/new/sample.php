<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
include '../PDFMerger.php';

$pdf = new PDFMerger;

$pdf->addPDF('../samplepdfs/one.pdf', '1, 3, 4')
	->addPDF('../samplepdfs/two.pdf', '1-2')
	->merge('file', '../samplepdfs/TEST2.pdf');
	
	//REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options
	//You do not need to give a file path for browser, string, or download - just the name.
