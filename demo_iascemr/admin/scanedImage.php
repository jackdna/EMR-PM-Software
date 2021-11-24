<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../common/conDb.php");
include("adminLinkfile.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$id = $_REQUEST['id'];
$type = $_REQUEST['type'];
$from = "ScanPopUP";
if($_REQUEST['from']) {
	$from = $_REQUEST['from'];
}

$tblName = "scan_upload_tbl";
if($_REQUEST["tblName"]) {
	$tblName = $_REQUEST["tblName"];;	
}

?>
<html>
<head>
<title>Scaned Image</title>
<script language="javascript" type="text/javascript">
	window.focus();
</script>
</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<TR>
		<td align="center">
			<?php
			if($type!='pdf'){
			?>
			<img id="imgThumbNail" src="logoImg.php?from=<?php echo $from; ?>&imageId=<?php echo $id; ?>">
			<?php
			}else{
				$dataContents = $objManageData->getRowRecord($tblName, 'scan_upload_id', $id);
				
				$imageCont = $dataContents->img_content;
				$imageType = $dataContents->document_type;
				$pdfFilePath = $dataContents->pdfFilePath;
				
				//IF PDF CONTENT IN DATABASE IS NOT EMPTY THEN CREATE PDF FILE IN TEMPRARY FOLDER FROM DATABASE
				//NOTE--> (THIS CODE WILL RUN FOR OLD RECORDS...NEW FUCNTIONALITY HAS CHANGED)
				
				if($imageCont) {
					
					$pdfTempraryFolderName = 'pdfFiles/TempDatabase';
					if(is_dir($pdfTempraryFolderName)) {
						//DO NOT CREATE FOLDER AGAIN
					}else {
						mkdir($pdfTempraryFolderName, 0777);
					}
					
					$pdfFilePath = $pdfTempraryFolderName.'/'.$dataContents->scan_upload_id.'.pdf';
					$file = fopen($pdfFilePath,"w");
					$getdata = fwrite($file,$imageCont);
					fclose($file);
				}
			}
			?>
		</td>
	</TR>
</table>
</body>
</html>
<?php
	if($type=='pdf'){
		?>
		<script>
			location.href='<?php echo $pdfFilePath; ?>';
			/*
			if(window.open('<?php echo $pdfFilePath; ?>','pdfImage')) {
				window.close();
			}*/
				
		</script>
	<?php
	}
?>