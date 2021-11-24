<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include("admin/adminLinkfile.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$imageId = $_REQUEST['imageId'];
$type = $_REQUEST['type'];
?>
<html>
<head>
<title>Scaned Image</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script>
window.focus();
</script>
</head>
<table border="0" width="150" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
	<tr>
		<td align="center" height="150">
		<td align="center">
			<?php
			if($type!='pdf'){
			?>
			<img border="0" src="admin/logoImg.php?from=ScanPopUP&imageId=<?php echo $imageId; ?>">
			<?php
			}else{
				$dataContents = $objManageData->getRowRecord('scan_upload_tbl', 'scan_upload_id', $imageId);
				$imageCont = $dataContents->img_content;
				$imageType = $dataContents->document_type;
				$pdfFilePathTemp = $dataContents->pdfFilePath;
				if($pdfFilePathTemp) {
					$pdfFilePath = 'admin/'.$pdfFilePathTemp;
				}
				//IF PDF CONTENT IN DATABASE IS NOT EMPTY THEN CREATE PDF FILE FROM DATABASE
				//NOTE--> (THIS CODE WILL RUN FOR OLD RECORDS...NEW FUCNTIONALITY HAS CHANGED) 
				if($imageCont) {
					
					$pdfTempraryFolderName = 'admin/pdfFiles/TempDatabase';
					if(is_dir($pdfTempraryFolderName)) {
						//DO NOT CREATE FOLDER AGAIN
					}else {
						mkdir($pdfTempraryFolderName, 0777);
					}
					$pdfFilePath = $pdfTempraryFolderName.'/'.$dataContents->scan_upload_id.'.pdf';
					//$pdfFilePath = 'admin/pdfFiles/'.$dataContents->document_name;
					$file = fopen($pdfFilePath,"w");
					$getdata = fwrite($file,$imageCont);
					fclose($file);
				}
			}
			?>
		</td>
	</tr>
</table>
<body>
</body>
</html>
<?php
	if($type=='pdf'){
		?>
		<script>
			location.href='<?php echo $pdfFilePath; ?>';
			/*
			if(window.open('<?php echo $pdfFilePath; ?>','pdfImage')){	
				window.close(); 
			}*/
			
		</script>
	<?php
	}
?>