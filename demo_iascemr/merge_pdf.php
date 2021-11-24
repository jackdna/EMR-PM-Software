<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
	
	include_once("common/conDb.php");
	require_once('PDFMerger/PDFMerger.php');
	$updir = __DIR__;
	$folderName= $updir.'/new_html2pdf';
	$delFileName = $_REQUEST['del_file_name'];
	if($delFileName){
		if(file_exists($delFileName)) {
			unlink($delFileName);
			echo "Done";
		}
	}
	
	if($_REQUEST['pConfId'] && $_REQUEST['del_file_name']==''){
		$pdf = new PDFMerger;
		$pdf_name=$folderName."/"."newPdf_".$_REQUEST['pConfId'].".pdf";
		$pdf->addPDF($pdf_name);
		$mergeFile = $folderName."/"."merge_newPdf_".$_REQUEST['pConfId'].date('YmdHis').".pdf";
		$pdf->merge('file', $mergeFile);
		$qryPDFPath = "SELECT sut.pdfFilePath as document_path,sd.document_name as scanCategoryName FROM 
					scan_upload_tbl sut INNER JOIN scan_documents sd ON (sd.document_id = sut.document_id)
					WHERE sut.confirmation_id = '".$_REQUEST['pConfId']."' AND 
					image_type='application/pdf' ORDER BY sd.document_name, sut.image_type";
		
		$resPDFPath=imw_query($qryPDFPath);
		if(imw_num_rows($resPDFPath)>0){
			while($rowPDFPath=imw_fetch_assoc($resPDFPath)){
				if($rowPDFPath['document_path']){
					if(file_exists($updir."/admin/".$rowPDFPath['document_path'])){
						$pdf->addPDF($updir."/admin/".$rowPDFPath['document_path']);		
						try
						{
							$pdf->merge('file', $mergeFile);
						}
						catch(Exception $exception) {
							//DO NOT INCLUDE PDF TO MERGE
						}
					}
				}
			}	
		}			
		if(file_exists($pdf_name)) {
			unlink($pdf_name);
		}
		$mergeFileLoc = str_ireplace($updir."/","",$mergeFile);
	}
	
?>
<script src="js/moocheck.js"></script>
<script type="text/javascript">
function del_pdf_file(delFileName){
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null){
		alert ("Browser does not support HTTP Request")
		return
	}
	var url="merge_pdf.php"
	url=url+"?del_file_name="+delFileName;
	xmlHttp.onreadystatechange=function() {
		if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		   var tmValTemp= xmlHttp.responseText;
		   if(tmValTemp) {
				window.close();
		   }
		} 
	};
	xmlHttp.open("GET",url,true)
	xmlHttp.send(null)
}
function openPDF(){
 win = window.open('<?php echo $mergeFileLoc;?>');
 if(typeof(win)=="object")
 return true;
 else
 return false;
}
if(openPDF()){
	setTimeout(function () {del_pdf_file('<?php echo $mergeFileLoc; ?>')},10);	
}
</script>
