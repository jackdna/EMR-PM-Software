<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php 

include_once("../../common/conDb.php");
include_once("../classObjectFunction.php");
$objManageData = new manageData;

$pConfirmId = $_REQUEST['pconfirmId'];
$patient_id = $_REQUEST['patient_id'];
$ptStubId 	= $_REQUEST['ptStubId'];
$formName 	= $_REQUEST['formName'];
$folderId 	= $_REQUEST['folderId'];
$scanIOL	= $_REQUEST['scanIOL'];
$IOLScan 	= $_REQUEST['IOLScan'];

require('html2pdf.php');
$fp = fopen("pdfFile.html","r");
$strContent = fread($fp, filesize("pdfFile.html"));
fclose($fp);
$height = $_REQUEST['page'];
$font_size = $_REQUEST['font_size'];
$tld = $_REQUEST['tld'];
if(!$tld){
	$tld = 'p';
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
$path = "uploaddir"; //'//192.168.0.3/documents/test';
$ab =  opendir($path);
$chkFileExist = 'false';
while(($filename = readdir($ab)) !== false)
	{
		$path1 = pathinfo($filename);
		if($path1['extension'] == 'jpg')
		{
			$chkFileExist = 'true';
			@unlink($path.'\\'.$filename);
		}
				
	}
	$savePDF_FileName = $folderId.'scan'.date('d_m_y_h_i_s').'.pdf';	
	
	//copy('pdfFile.pdf',$savePDF_FileName);
	/*echo "<script>location.href='".$savePDF_FileName."'</script>";*/
	//header('location: abcd'.date('d_m_y_h_i_s').'.pdf');

//$pConfirmId = $_REQUEST['pConfirmId'];	
//START

if($chkFileExist=='true') {

	
	//copy('pdfFile.pdf',$savePDF_FileName);
	
	if($pConfirmId) {
		// GET SURGEON NAME FOR GIVEN CONFIRMATION ID 
			$surgeonData = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId);
			$surgeonName = $surgeonData->surgeon_name;
		// END GET SURGEON NAME FOR GIVEN CONFIRMATION ID 
	}else {
		//GET SURGEON NAME FROM STUB TABLE
			$stubTblSurgeonData = $objManageData->getRowRecord('stub_tbl', 'stub_id', $_REQUEST['ptStubId']);
			$stubTblSurgeonFname = $stubTblSurgeonData->surgeon_fname;
			$stubTblSurgeonMname = $stubTblSurgeonData->surgeon_mname;
			$stubTblSurgeonLname = $stubTblSurgeonData->surgeon_lname;
			if($stubTblSurgeonMname){
				$stubTblSurgeonMname = ' '.$stubTblSurgeonMname;
			}
			$surgeonName = $stubTblSurgeonFname.$stubTblSurgeonMname.' '.$stubTblSurgeonLname;
		//END SURGEON NAME FROM STUB TABLE
	}
	
		
	$surgeonName = str_replace(" ","_",$surgeonName);
	$surgeonName = str_replace(",","",$surgeonName);
	$surgeonName = str_replace("!","",$surgeonName);
	$surgeonName = str_replace("@","",$surgeonName);
	$surgeonName = str_replace("%","",$surgeonName);
	$surgeonName = str_replace("^","",$surgeonName);
	$surgeonName = str_replace("$","",$surgeonName);
	$surgeonName = str_replace("'","",$surgeonName);
	$surgeonName = str_replace("*","",$surgeonName);

	$pdfFolderName = '../pdfFiles/'.$surgeonName;
	
	if(is_dir($pdfFolderName)) {
		//DO NOT CREATE FOLDER AGAIN
	}else {
		mkdir($pdfFolderName, 0777);
	}
	
	$pdfFilePath = $pdfFolderName."/".$savePDF_FileName;
	$pdfFilePathSave = 'pdfFiles/'.$surgeonName."/".$savePDF_FileName;
	
	copy('pdfFile.pdf',$pdfFilePath);
	
	unset($arrayRecord);
	$arrayRecord['image_type'] = 'application/pdf';
	//$arrayRecord['img_content'] = $image;
	$arrayRecord['document_name'] = $savePDF_FileName;
	//$arrayRecord['document_size'] = $PSize;
	$arrayRecord['confirmation_id'] = $pConfirmId;
	$arrayRecord['patient_id'] = $patient_id;
	$arrayRecord['document_id'] = $folderId;
	
	
	$arrayRecord['pdfFilePath'] = $pdfFilePathSave;
	$objManageData->addRecords($arrayRecord, 'scan_upload_tbl');
	//$updtScanUpldTbl = $objManageData->updateRecords($arrayRecord, 'scan_upload_tbl', 'scan_upload_id', $inserIdScanUpload);

	echo "<script>location.href='".$pdfFilePath."'</script>";
}else {
	echo "<center><p class='text_10b'>No file found to retrieve</p></center>";
}	
//END	

?>